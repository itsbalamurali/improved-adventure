<?php
$intervalmins = INTERVAL_SECONDS; // Added By HJ On 13-03-2020 Which is Defined In configuration_variables.php
$PHOTO_UPLOAD_SERVICE_ENABLE = "Yes";
$orderEventChannelName = 'ORDER_EVENT_NOTIFICATIONS'; //added by SP on 23-01-2021 to send notification used in store panel using socket cluster for order inventory.
$host_arr = array();
$host_arr = explode(".", $_SERVER["HTTP_HOST"]);
$host_system = $host_arr[0];
require_once($tconfig["tpanel_path"] . "send_invoice_receipt.php");
$uuid = "fg5k3i7i7l5ghgk1jcv43w0j41";
if (isset($_REQUEST['APP_TYPE']) && $_REQUEST['APP_TYPE'] != "") {
    $APP_TYPE = $_REQUEST['APP_TYPE'];
}
/* creating objects */
$thumb = new thumbnail;
/* Get variables */


if (!empty($REQUEST_DATA_DEBUG) && $REQUEST_DATA_DEBUG == 'Yes') {
    $Data_request['tType'] = $type;
    $Data_request['tRequestParam'] = http_build_query($_REQUEST);
    $obj->MySQLQueryPerform('request_data_debug', $Data_request, 'insert');
}
/* Paypal supported Currency Codes */
$currency_supported_paypal = array('AUD', 'BRL', 'CAD', 'CZK', 'DKK', 'EUR', 'HKD', 'HUF', 'ILS', 'JPY', 'MYR', 'MXN', 'TWD', 'NZD', 'NOK', 'PHP', 'PLN', 'GBP', 'RUB', 'SGD', 'SEK', 'CHF', 'THB', 'TRY', 'USD');
$demo_site_msg = "Edit / Delete Record Feature has been disabled on the Demo Application. This feature will be enabled on the main script we will provide you.";
if ($type == '') {
    $type = isset($_REQUEST['function']) ? trim($_REQUEST['function']) : '';
}
if (strtoupper(PACKAGE_TYPE) != "STANDARD") {
    include_once('include/include_webservice_dl_enterprisefeatures.php');
}
$lang_label = array();
$lang_code = '';
$GeneralDeviceType = isset($_REQUEST['GeneralDeviceType']) ? trim($_REQUEST['GeneralDeviceType']) : '';
if ($_SERVER["HTTP_HOST"] == "192.168.1.131" || $_SERVER["HTTP_HOST"] == "www.mobileappsdemo.com" || $_SERVER["HTTP_HOST"] == "192.168.1.141") {
    if ($APPSTORE_MODE_IOS == "Review" /* && $GeneralDeviceType == "Ios" */) {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "Configuration name 'APPSTORE_MODE_IOS' must be set to Development mode for 131 and MobileProjectsDemo";
        setDataResponse($returnArr);
    }
}

/* To Check App Version */
$appVersion = isset($_REQUEST['AppVersion']) ? trim($_REQUEST['AppVersion']) : '';
if(empty($appVersion)) {
    $appVersion = isset($_REQUEST['GeneralAppVersion']) ? trim($_REQUEST['GeneralAppVersion']) : '';
}
$Platform = isset($_REQUEST['Platform']) ? trim($_REQUEST['Platform']) : '';
if(empty($Platform)) {
    $Platform = isset($_REQUEST['GeneralDeviceType']) ? trim($_REQUEST['GeneralDeviceType']) : '';
}
$vTimeZone = isset($_REQUEST["vTimeZone"]) ? $_REQUEST["vTimeZone"] : '';
$iServiceId = isset($_REQUEST["iServiceId"]) ? $_REQUEST["iServiceId"] : '';
$vUserDeviceCountry = isset($_REQUEST["vUserDeviceCountry"]) ? $_REQUEST["vUserDeviceCountry"] : '';
$vCurrentTime = isset($_REQUEST["vCurrentTime"]) ? $_REQUEST["vCurrentTime"] : '';
$eSystemAppType = isset($_REQUEST["eSystemAppType"]) ? $_REQUEST["eSystemAppType"] : '';
if ($appVersion != "") {
    if(strtoupper($MAINTENANCE_APPS) == "YES") {
        $returnArr['Action'] = "0";
        $returnArr['RESTRICT_APP'] = "Yes";
        $returnArr['isAppMaintenance'] = "Yes";
        $returnArr['message'] = "LBL_MAINTENANCE_CONTENT_MSG";
        $returnArr['message_title'] = "LBL_MAINTENANCE_HEADER_MSG";
        setDataResponse($returnArr);
    }
    
    $UserType = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : 'Passenger';
    if ($UserType == "Passenger") {
        $newAppVersion = strtoupper($Platform) == "IOS" ? $PASSENGER_IOS_APP_VERSION : $PASSENGER_ANDROID_APP_VERSION;
        $HMS_DEVICE = isset($_REQUEST["HMS_DEVICE"]) ? $_REQUEST["HMS_DEVICE"] : 'No';
        $HUAWEI_DEVICE = isset($_REQUEST["HUAWEI_DEVICE"]) ? $_REQUEST["HUAWEI_DEVICE"] : 'No';
        if(strtoupper($HMS_DEVICE) == "YES" || strtoupper($HUAWEI_DEVICE) == "YES") {
            $newAppVersion = $PASSENGER_HMS_APP_VERSION;
        }
    }
    else if ($UserType == "Company") {
        $newAppVersion = strtoupper($Platform) == "IOS" ? $COMPANY_IOS_APP_VERSION : $COMPANY_ANDROID_APP_VERSION;
        if (!empty($eSystemAppType) && strtoupper($eSystemAppType) == "KIOSK") {
            $newAppVersion = strtoupper($Platform) == "IOS" ? $STORE_KIOSK_IOS_APP_VERSION : $STORE_KIOSK_ANDROID_APP_VERSION;
        }
    }
    else if ($UserType == "Hotel") {
        $newAppVersion = strtoupper($Platform) == "IOS" ? $KIOSK_IOS_APP_VERSION : $KIOSK_ANDROID_APP_VERSION;
    }
    else {
        $newAppVersion = strtoupper($Platform) == "IOS" ? $DRIVER_IOS_APP_VERSION : $DRIVER_ANDROID_APP_VERSION;
    }
    $appVersion = round($appVersion, 2);
    if ($newAppVersion != $appVersion && $newAppVersion > $appVersion) {
        $returnArr['Action'] = "0";
        $returnArr['RESTRICT_APP'] = "Yes";
        $returnArr['isAppUpdate'] = "Yes";
        $returnArr['message'] = "LBL_NEW_UPDATE_MSG";
        $returnArr['message_title'] = "LBL_NEW_UPDATE_TITLE";
        setDataResponse($returnArr);
    }
}
/* To Check App Version End */

/* general fucntions */
if ($type != "generalConfigData" && $type != "signIn" && $type != "isUserExist" && $type != "signup" && $type != "LoginWithFB" && $type != "sendVerificationSMS" && $type != "countryList" && $type != "changelanguagelabel" && $type != "requestResetPassword" && $type != "UpdateLanguageLabelsValue" && $type != "staticPage" && $type != "sendContactQuery" && $type != "loadAvailableRestaurants" && $type != "getCuisineList" && $type != "loadSearchRestaurants" && $type != "GetRestaurantDetails" && $type != "signup_company" && $type != "GetItemOptionAddonDetails" && $type != "getBanners" && $type != "getServiceCategories" && $type != "CheckOutOrderEstimateDetails" && $type != "getFAQ" && $type != "getUserLanguagesAsPerServiceType" && $type != "uploadcompanydocument" && $type != "getAdvertisementBanners" && $type != "insertBannereImpressionCount" && $type != "getNewsNotification" && $type != "CheckPrescriptionRequired" && isAllowFetchAPIDetails() == false && $type != "sendAuthOtp" && $type != "AuthenticateMember" && $type != "CheckMemberAccount" && $type != "LoginWithAppleID" && $type != "loadAvailableRestaurantsAll" && $type != "loadStaticInfo" && $type != "loadStaticPages" && $type != "SetFaqs" && $type != "SetCancelReasons" && $type != "loadAppImages") {
    $tSessionId = isset($_REQUEST['tSessionId']) ? trim($_REQUEST['tSessionId']) : '';
    $GeneralMemberId = isset($_REQUEST['GeneralMemberId']) ? trim($_REQUEST['GeneralMemberId']) : '';
    $GeneralUserType = isset($_REQUEST['GeneralUserType']) ? trim($_REQUEST['GeneralUserType']) : '';
    $eSystemAppType = isset($_REQUEST['eSystemAppType']) ? trim($_REQUEST['eSystemAppType']) : '';
    if ($tSessionId == "" || $GeneralMemberId == "" || $GeneralUserType == "") {
        $returnArr['Action'] = "0";
        $returnArr['RESTRICT_APP'] = "Yes";
        $returnArr['isSessionExpired'] = "Yes";
        $returnArr['message'] = "LBL_SESSION_TIME_OUT";
        $returnArr['message_title'] = "LBL_SESSION_TIME_OUT_TITLE";
        setDataResponse($returnArr);
    }
    else {
        if ($GeneralUserType == "Company") {
            $tableName = "company";
            $userData = get_value("company", "*,iCompanyId as iMemberId", "iCompanyId", $GeneralMemberId);
            if (!empty($eSystemAppType) && strtoupper($eSystemAppType) == "KIOSK") {
                $userData[0]['tSessionId'] = $userData[0]['tSessionIdKiosk'];
            }
        }
        else {
            //Added By HJ On 09-07-2020 For Optimize register_user/driver Table Query Start
            $tableName = "register_user";
            if (strtoupper($GeneralUserType) == "DRIVER") {
                $tableName = "register_driver";
            }
            //Added By HJ On 09-07-2020 For Optimize register_user/driver Table Query End
            $userData = get_value($GeneralUserType == "Driver" ? "register_driver" : "register_user", $GeneralUserType == "Driver" ? "*,iDriverId as iMemberId" : "*,iUserId as iMemberId", $GeneralUserType == "Driver" ? "iDriverId" : "iUserId", $GeneralMemberId);
        }
        //Added By HJ On 09-07-2020 For Optimize company Table Query Start
        $userDetailsArr[$tableName . "_" . $GeneralMemberId] = $userData;
        //Added By HJ On 09-07-2020 For Optimize company Table Query End
        if ($userData[0]['iMemberId'] != $GeneralMemberId || $userData[0]['tSessionId'] != $tSessionId) {
            $returnArr['Action'] = "0";
            $returnArr['RESTRICT_APP'] = "Yes";
            $returnArr['isSessionExpired'] = "Yes";
            $returnArr['message'] = "LBL_SESSION_TIME_OUT";
            $returnArr['message_title'] = "LBL_SESSION_TIME_OUT_TITLE";
            setDataResponse($returnArr);
        } elseif (strtolower($userData[0]['eStatus']) != "active" && strtoupper($GeneralUserType) == "PASSENGER") {
            $returnArr['Action'] = "0";
            $returnArr['RESTRICT_APP'] = "Yes";
            $returnArr['message'] = "LBL_ACC_DELETE_TXT";
            $returnArr['message_title'] = "LBL_ACC_DELETE_TITLE";
            if ($userData[0]['eStatus'] != "Deleted") {
                $returnArr['isAccountInactive'] = "Yes";
                $returnArr['message'] = "LBL_CONTACT_US_STATUS_NOTACTIVE_PASSENGER";
                $returnArr['message_title'] = "LBL_ACC_INACTIVE_TITLE";
            } else {
                $returnArr['isAccountDeleted'] = "Yes";
            }
            setDataResponse($returnArr);
        }
    }
}

 
function getCompanyDetailInfo($iCompanyId, $fromSignIN = 0)
{
    global $obj, $demo_site_msg, $PHOTO_UPLOAD_SERVICE_ENABLE, $parent_ufx_catid, $generalSystemConfigDataArr, $vTimeZone, $tconfig, $vUserDeviceCountry, $ENABLE_RESTAURANTS_ADVERTISEMENT_BANNER, $ADVERTISEMENT_TYPE, $SITE_NAME, $THERMAL_PRINT_ENABLE, $SHOW_CITY_FIELD, $languageAssociateArr, $userDetailsArr, $Data_ALL_currency_Arr, $currencyAssociateArr, $country_data_retrieve, $generalConfigPaymentArr, $country_data_arr, $languageLabelDataArr, $vSystemDefaultCurrencySymbol, $vSystemDefaultCurrencyName, $tripDetailsArr, $MODULES_OBJ, $WALLET_OBJ, $STATIC_PAGE_OBJ, $LANG_OBJ;
    $where = " iCompanyId = '" . $iCompanyId . "'";
    $data_version['iAppVersion'] = "2";
    $data_version['eLogout'] = 'No';
    $data_version['eDebugMode'] = isset($_REQUEST["IS_DEBUG_MODE"]) ? $_REQUEST["IS_DEBUG_MODE"] : "";
    $data_version['tApiFileName'] = pathinfo(__FILE__, PATHINFO_FILENAME);
    #################################### Generate Session For GeoAPI ########################################
    generateSessionForGeo($iCompanyId, "Company");
    #################################### Generate Session For GeoAPI ########################################
    #################################### Configure App Version Info ########################################
    $arr_app_version = array();
    $arr_app_version['AppVersionName'] = isset($_REQUEST['GeneralAppVersion']) ? $_REQUEST['GeneralAppVersion'] : "";
    $arr_app_version['AppVersionCode'] = isset($_REQUEST['GeneralAppVersionCode']) ? $_REQUEST['GeneralAppVersionCode'] : "";
    #################################### Configure App Version Info ########################################
    $data_version['tVersion'] = strval(json_encode($arr_app_version));
    $data_version['tDeviceData'] = isset($_REQUEST['DEVICE_DATA']) ? $_REQUEST['DEVICE_DATA'] : "";
    $obj->MySQLQueryPerform("company", $data_version, 'update', $where);
    $returnArr = array();
    $vLangCode = isset($_REQUEST['vLang']) ? $_REQUEST['vLang'] : "";
    $languageCode = "";
    if ($vLangCode != NULL && $vLangCode != "") {
        //Added By HJ On 09-07-2020 For Optimize language_master Table Query Start
        if (isset($languageAssociateArr[$vLangCode])) {
            $check_lng = $languageAssociateArr[$vLangCode]['vTitle'];
        }
        else {
            $check_lng = get_value('language_master', 'vTitle', 'vCode', $vLangCode, '', 'true');
        }
        //Added By HJ On 09-07-2020 For Optimize language_master Table Query End
        if ($check_lng != NULL) {
            $languageCode = $vLangCode;
        }
    }
    if ($languageCode == "" || $languageCode == NULL) {
        //Added By HJ On 09-07-2020 For Optimize language_master Table Query Start
        $languageCode = $LANG_OBJ->FetchDefaultLangData("vCode");
        //Added By HJ On 09-07-2020 For Optimize language_master Table Query End
    }
    //Added By HJ On 09-07-2020 For Optimize company Table Query Start
    if (isset($userDetailsArr['company_' . $iCompanyId])) {
        // $Data = $userDetailsArr['company_'.$iCompanyId]; // commented by NM type=updateUserProfileDetail no email issue
        $Data = $obj->MySQLSelect("SELECT *,iCompanyId as iMemberId FROM `company` WHERE iCompanyId='" . $iCompanyId . "'");
        $userDetailsArr['company_' . $iCompanyId] = $Data;
    }
    else {
        $Data = $obj->MySQLSelect("SELECT *,iCompanyId as iMemberId FROM `company` WHERE iCompanyId='" . $iCompanyId . "'");
        $userDetailsArr['company_' . $iCompanyId] = $Data;
    }
    //Added By HJ On 09-07-2020 For Optimize company Table Query End
    if (count($Data) > 0) {
        foreach ($generalSystemConfigDataArr as $key => $value) {
            if (is_null($generalSystemConfigDataArr[$key]) || empty($generalSystemConfigDataArr[$key])) {
                $generalSystemConfigDataArr[$key] = "";
            }
        }
        $Data[0] = array_merge($Data[0], $generalSystemConfigDataArr); // Added By HJ On 18-03-2020 For Optimized Function
        if (isset($_REQUEST['eSystemAppType']) && strtoupper($_REQUEST['eSystemAppType']) == "KIOSK") {
            $Data[0]['tSessionId'] = $Data[0]['tSessionIdKiosk'];
        }
        $Data[0]['restaurantAddressAdded'] = "No";
        if ($Data[0]['vRestuarantLocation'] != "" && $Data[0]['vRestuarantLocationLat'] != "" && $Data[0]['vRestuarantLocationLong'] != "") {
            $Data[0]['restaurantAddressAdded'] = "Yes";
        }
        if ($_REQUEST['APP_TYPE'] != "") {
            $Data[0]['APP_TYPE'] = $_REQUEST['APP_TYPE'];
        }
        $Data[0]['PACKAGE_TYPE'] = strtoupper(PACKAGE_TYPE);
        $Data[0]['GOOGLE_ANALYTICS'] = "";
        //Added By HJ On 16-07-2019 For Check Empty and # Value Of Configuration Start
        if (isset($Data[0]['LIVE_CHAT_LICENCE_NUMBER']) && ($Data[0]['LIVE_CHAT_LICENCE_NUMBER'] == "" || strpos($Data[0]['LIVE_CHAT_LICENCE_NUMBER'], '#') !== false)) {
            $Data[0]['ENABLE_LIVE_CHAT'] = "No";
        }
        if (isset($Data[0]['AUDIO_CALLING_METHOD']) && strtoupper($Data[0]['AUDIO_CALLING_METHOD']) == "SINCH") {
            if (isset($Data[0]['SINCH_APP_ENVIRONMENT_HOST']) && ($Data[0]['SINCH_APP_ENVIRONMENT_HOST'] == "" || strpos($Data[0]['SINCH_APP_ENVIRONMENT_HOST'], '#') !== false)) {
                $Data[0]['RIDE_DRIVER_CALLING_METHOD'] = "Normal";
            }
            if (isset($Data[0]['SINCH_APP_KEY']) && ($Data[0]['SINCH_APP_KEY'] == "" || strpos($Data[0]['SINCH_APP_KEY'], '#') !== false)) {
                $Data[0]['RIDE_DRIVER_CALLING_METHOD'] = "Normal";
            }
            if (isset($Data[0]['SINCH_APP_SECRET_KEY']) && ($Data[0]['SINCH_APP_SECRET_KEY'] == "" || strpos($Data[0]['SINCH_APP_SECRET_KEY'], '#') !== false)) {
                $Data[0]['RIDE_DRIVER_CALLING_METHOD'] = "Normal";
            }
            //Added By HJ On 16-07-2019 For Check Empty and # Value Of Configuration End
            //Added By HJ On 21-10-2020 For Enable/Disable Sinch Base On Country Configuration Start
            $usercountrycode = $Data[0]['vCountry'];
            if ($usercountrycode != "") {
                $eEnableSinch = checkCountryVoipMethod($usercountrycode);
                if (strtoupper($eEnableSinch) == "NO") {
                    $Data[0]['RIDE_DRIVER_CALLING_METHOD'] = "Normal";
                }
            }
            //Added By HJ On 21-10-2020 For Enable/Disable Sinch Base On Country Configuration End
        }
        $COMPANY_EMAIL_VERIFICATION = $Data[0]["COMPANY_EMAIL_VERIFICATION"];
        $COMPANY_PHONE_VERIFICATION = $Data[0]["COMPANY_PHONE_VERIFICATION"];
        if ($COMPANY_EMAIL_VERIFICATION == 'No') {
            $Data[0]['eEmailVerified'] = "Yes";
        }
        /*for email optional*/
        if ($Data[0]['vEmail'] == "") {
            $Data[0]['eEmailVerified'] = "Yes";
        }
        if ($COMPANY_PHONE_VERIFICATION == 'No') {
            $Data[0]['ePhoneVerified'] = "Yes";
        }
        // # Check and update Device Session ID ##
        if ($Data[0]['tDeviceSessionId'] == "") {
            $random = substr(md5(rand()), 0, 7);
            $Update_Device_Session['tDeviceSessionId'] = session_id() . time() . $random;
            $Update_Device_Session_id = $obj->MySQLQueryPerform("company", $Update_Device_Session, 'update', $where);
            $Data[0]['tDeviceSessionId'] = $Update_Device_Session['tDeviceSessionId'];
        }
        // # Check and update Device Session ID ##
        // # Check and update Session ID ##
        if ($Data[0]['tSessionId'] == "") {
            $Update_Session['tSessionId'] = session_id() . time();
            $Update_Session_id = $obj->MySQLQueryPerform("company", $Update_Session, 'update', $where);
            $Data[0]['tSessionId'] = $Update_Session['tSessionId'];
        }
        // # Check and update Session ID ##
        if ($Data[0]['eStatus'] == "Deleted") {
            $returnArr['Action'] = "0";
            $returnArr['eStatus'] = $Data[0]['eStatus'];
            $returnArr['message'] = "LBL_ACC_DELETE_TXT";
            setDataResponse($returnArr);
        }
        // $Data[0]['RegistrationDate'] = date("Y-m-d", strtotime($Data[0]['tRegistrationDate'] . ' -1 day '));
        $Data[0]['RegistrationDate'] = date("Y-m-d", strtotime($Data[0]['tRegistrationDate']));
        $Data[0]['ABOUT_US_PAGE_DESCRIPTION'] = "";
        $Data[0]['DefaultCurrencySign'] = $Data[0]["DEFAULT_CURRENCY_SIGN"];
        $Data[0]['DefaultCurrencyCode'] = $Data[0]["DEFAULT_CURRENCY_CODE"];
        $Data[0]['SITE_TYPE'] = SITE_TYPE;
        $Data[0]['SITE_NAME'] = $SITE_NAME;
        $Data[0]['DELIVERALL'] = DELIVERALL;
        $Data[0]['ONLYDELIVERALL'] = ONLYDELIVERALL;
        $Data[0]['SITE_TYPE_DEMO_MSG'] = $demo_site_msg;
        $Data[0]['FETCH_TRIP_STATUS_TIME_INTERVAL'] = fetchtripstatustimeinterval();
        //Added By HJ On 08-06-2020 For Optimization currency Table Query Start
        $vCurrencyCompany = $Data[0]['vCurrencyCompany'];
        if (isset($currencyAssociateArr[$vCurrencyCompany])) {
            $currencySymbol = $currencyAssociateArr[$vCurrencyCompany]['vSymbol'];
        }
        else {
            $currencySymbol = get_value('currency', 'vSymbol', 'vName', $Data[0]['vCurrencyCompany'], '', 'true');
        }

        $Data[0]['CurrencySymbol'] = $currencySymbol;

        if(isset($_REQUEST['type']) && $_REQUEST['type'] != "getDetail") {
            //Added By HJ On 17-06-2020 For Optimize currency Table Query Start
            if (count($Data_ALL_currency_Arr) > 0) {
                $defCurrencyValues = array();
                for ($c = 0; $c < count($Data_ALL_currency_Arr); $c++) {
                    if (strtoupper($Data_ALL_currency_Arr[$c]['eStatus']) == "ACTIVE") {
                        unset($Data_ALL_currency_Arr[$c]['eReverseSymbolEnable']);
                        unset($Data_ALL_currency_Arr[$c]['eReverseformattingEnable']);
                        unset($Data_ALL_currency_Arr[$c]['eRoundingOffEnable']);
                        unset($Data_ALL_currency_Arr[$c]['fFirstRangeValue']);
                        unset($Data_ALL_currency_Arr[$c]['fMiddleRangeValue']);
                        unset($Data_ALL_currency_Arr[$c]['fSecRangeValue']);
                        $defCurrencyValues[] = $Data_ALL_currency_Arr[$c];
                    }
                }
            } else {
                $defCurrencyValues = $obj->MySQLSelect("SELECT iCurrencyId,vName, vSymbol,iDispOrder, eDefault,Ratio,fThresholdAmount,eStatus  FROM  `currency` WHERE  `eStatus` = 'Active' ORDER BY iDispOrder ASC ");
            }
            //Added By HJ On 17-06-2020 For Optimize currency Table Query End
            $Data[0]['LIST_CURRENCY'] = $defCurrencyValues;

            for ($i = 0; $i < count($defCurrencyValues); $i++) {
                $Data[0]['LIST_CURRENCY'][$i]['vService_BG_color'] = RANDOM_COLORS_ARR[$i];
                $Data[0]['LIST_CURRENCY'][$i]['vService_TEXT_color'] = "#FFFFFF";
            }
        }

        //Added By HJ On 08-06-2020 For Optimization currency Table Query End
        $usercountrydetailbytimezone = FetchMemberCountryData($iCompanyId, "Company", $vTimeZone, $vUserDeviceCountry);
        $Data[0]['vDefaultCountry'] = $usercountrydetailbytimezone['vDefaultCountry'];
        $Data[0]['vDefaultCountryCode'] = $usercountrydetailbytimezone['vDefaultCountryCode'];
        $Data[0]['vDefaultPhoneCode'] = $usercountrydetailbytimezone['vDefaultPhoneCode'];
        //$Data[0]['vRCountryImage'] = $usercountrydetailbytimezone['vRImage']; //added by SP for country image related changes on 05-08-2019
        //$Data[0]['vSCountryImage'] = $usercountrydetailbytimezone['vSImage']; //added by SP for country image related changes on 05-08-2019
        $Data[0]['vRCountryImage'] = $usercountrydetailbytimezone['vRImageMember']; //added by SP for country image related changes on 05-08-2019
        $Data[0]['vSCountryImage'] = $usercountrydetailbytimezone['vSImageMember']; //added by SP for country image related changes on 05-08-2019
        $Data[0]['vDefaultCountryImage'] = empty($Data[0]['vSCountryImage']) ? $usercountrydetailbytimezone['vDefaultCountryImage'] : $Data[0]['vSCountryImage']; //added by SP for country image related changes on 06-08-2019
        $Data[0]['vCode'] = empty($Data[0]['vCode']) ? $Data[0]['vDefaultPhoneCode'] : $Data[0]['vCode'];
        $Data[0]['vCountry'] = empty($Data[0]['vCountry']) ? $Data[0]['vDefaultCountryCode'] : $Data[0]['vCountry'];
        $SITE_POLICE_CONTROL_NUMBER = getMemberCountryPoliceNumber($iCompanyId, "Company", $Data[0]['vCountry']);
        $Data[0]['SITE_POLICE_CONTROL_NUMBER'] = $SITE_POLICE_CONTROL_NUMBER;
        $Data[0]['SC_CONNECT_URL'] = getSocketURL();
        $Data[0]['THERMAL_PRINT_ENABLE'] = $THERMAL_PRINT_ENABLE;
        if (empty($_REQUEST['vDeviceType'])) {
            $_REQUEST['vDeviceType'] = $_REQUEST['GeneralDeviceType'];
        }
        if (isset($_REQUEST['vDeviceType']) && strtoupper($_REQUEST['vDeviceType']) == "IOS") {
            $Data[0]['THERMAL_PRINT_ENABLE'] = "No";
            $Data[0]['eThermalPrintEnable'] = "No";            
        }
        $Data[0]['THERMAL_PRINT_ALLOWED'] = $Data[0]['eThermalPrintEnable'];
        $Data[0]['AUTO_PRINT'] = $Data[0]['eThermalAutoPrint'];
        unset($Data[0]['eThermalPrintEnable']);
        unset($Data[0]['eThermalAutoPrint']);
        $Data[0]['KOT_BILL_FORMAT'] = '';
        $Data[0]['SC_CONNECT_URL'] = getSocketURL();
        $Data[0]['tsite_upload_docs_file_extensions'] = $tconfig['tsite_upload_docs_file_extensions'];
        $Data[0]['tsite_upload_image_file_extensions'] = $tconfig['tsite_upload_image_file_extensions'];
        $Data[0]['tsite_upload_video_file_extensions'] = $tconfig['tsite_upload_video_file_extensions'];
        //Added By HJ On 16-07-2019 For Check Multiple Country Exists Or Not Start
        // Added By HJ On 09-07-2020 For Optimized country Table Query Start
        if (count($country_data_retrieve) > 0) {
            $getCountryData = array();
            for ($h = 0; $h < count($country_data_retrieve); $h++) {
                if (strtoupper($country_data_retrieve[$h]['eStatus']) == "ACTIVE") {
                    $getCountryData[] = $country_data_retrieve[$h]['iCountryId'];
                }
            }
        }
        else {
            $getCountryData = $obj->MySQLSelect("SELECT iCountryId FROM country WHERE eStatus='Active'");
        }
        // Added By HJ On 09-07-2020 For Optimized country Table Query End
        $multiCountry = "No";
        if (count($getCountryData) > 1) {
            $multiCountry = "Yes";
        }
        $Data[0]['showCountryList'] = $multiCountry;
        //Added By HJ On 16-07-2019 For Check Multiple Country Exists Or Not End
        $Data[0]['SHOW_CITY_FIELD'] = $SHOW_CITY_FIELD; //city field shown or not
        //Added By Hasmukh On 25-12-2018 For Get Advertise Banner Data Start
        if ($MODULES_OBJ->checkSharkPackage() && $Data[0]['eStatus'] == "Active") {
            $Data[0]['advertise_banner_data'] = getAdvertisementBannersAsPerDevice($iCompanyId, "Company");
        }
        //Added By Hasmukh On 25-12-2018 For Get Advertise Banner Data End
        //$EnableGopay = get_value('configurations_payment', 'vValue', 'vName', 'ENABLE_GOPAY'); //added by SP for Gojek-gopay
        //Added By HJ On 08-07-2020 For Optimization configurations_payment Table Query Start
        if (isset($generalConfigPaymentArr['ENABLE_GOPAY']) && trim($generalConfigPaymentArr['ENABLE_GOPAY']) != "") {
            $EnableGopay = trim($generalConfigPaymentArr['ENABLE_GOPAY']);
        }
        else {
            $EnableGopay = get_value('configurations_payment', 'vValue', 'vName', 'ENABLE_GOPAY'); //added by SP for Gojek-gopay
        }
        //Added By HJ On 08-07-2020 For Optimization configurations_payment Table Query End
        if (!empty($EnableGopay[0]['vValue'])) {
            $Data[0]['ENABLE_GOPAY'] = $EnableGopay[0]['vValue'];
        }
        else if (!empty($EnableGopay)) {
            $Data[0]['ENABLE_GOPAY'] = $EnableGopay;
        }
        else {
            $Data[0]['ENABLE_GOPAY'] = '';
        }
        $Data[0]['RANDOM_COLORS_KEY_VAL_ARR'] = RANDOM_COLORS_KEY_VAL_ARR;
        $Data[0]['AUTH_EMAIL_SYSTEM'] = AUTH_EMAIL_SYSTEM;
        $Data[0]['ENABLE_TAKE_AWAY'] = ($MODULES_OBJ->isTakeAwayEnable()) ? "Yes" : "No";
        $Data[0]['ENABLE_ADD_PROVIDER_FROM_STORE'] = ENABLE_ADD_PROVIDER_FROM_STORE;
        /* added by SP on 10-08-2020 for page active or not */
        $getPageData = $obj->MySQLSelect("SELECT iPageId,eStatus FROM pages WHERE iPageId IN(4,33,52)");
        foreach ($getPageData as $kPage => $vPage) {
            if ($vPage['iPageId'] == 4) $pagename = "showTermsCondition";
            if ($vPage['iPageId'] == 33) $pagename = "showPrivacyPolicy";
            if ($vPage['iPageId'] == 52) $pagename = "showAboutUs";
            $Data[0][$pagename] = $vPage['eStatus'] == 'Active' ? 'Yes' : 'No';
        }
        /*added by SP on 18-09-2020 */
        $Data[0]['IS_RIDE_MODULE_AVAIL'] = ($MODULES_OBJ->isRideFeatureAvailable() == 1) ? "Yes" : "No";
        $Data[0]['IS_DELIVERY_MODULE_AVAIL'] = ($MODULES_OBJ->isDeliveryFeatureAvailable() == 1) ? "Yes" : "No";
        $Data[0]['IS_UFX_MODULE_AVAIL'] = ($MODULES_OBJ->isUberXFeatureAvailable() == 1) ? "Yes" : "No";
        $Data[0]['IS_DELIVERALL_MODULE_AVAIL'] = ($MODULES_OBJ->isDeliverAllFeatureAvailable() == 1) ? "Yes" : "No";
        $Data[0]['ENABLE_ITEM_SEARCH_STORE_ORDER'] = ($MODULES_OBJ->isEnableItemSearchStoreOrder()) ? "Yes" : "No";
        //added by SP on 14-10-2020 for timeslot changes
        $Data[0]['ENABLE_TIMESLOT_ADDON'] = !empty($MODULES_OBJ->isEnableTimeslotFeature()) ? "Yes" : "No";
        $Data[0]['ENABLE_DELIVERY_INSTRUCTIONS_ORDERS'] = !empty($MODULES_OBJ->isEnableVoiceDeliveryInstructionsOrder()) ? "Yes" : "No";
        $Data[0]['ENABLE_TIP_MODULE_DELIVERALL'] = "No";
        $Data[0]['WALLET_ENABLE'] = "No";
        $Data[0]['ENABLE_GOOGLE_ADS'] = "No";
        //UpdateAppTerminateStatus($iCompanyId, "Company");
        $Data[0]['APP_LAUNCH_IMAGES'] = "";
        if (!empty(getAppLaunchImages($languageCode, 'Company'))) {
            $Data[0]['APP_LAUNCH_IMAGES'] = getAppLaunchImages($languageCode, 'Company');
        }
        if (!empty($Data[0]['vKioskImage']) && file_exists($tconfig['tsite_upload_images_compnay_path'] . '/' . $iCompanyId . '/' . $Data[0]['vKioskImage'])) {
            $Data[0]['vKioskImage'] = $tconfig['tsite_upload_images_compnay'] . '/' . $iCompanyId . '/' . $Data[0]['vKioskImage'];
        }
        $Data[0]['USER_IDLE_TIMER'] = USER_IDLE_TIMER;
        $Data[0]['ENABLE_LOCATION_WISE_BANNER'] = ($MODULES_OBJ->isEnableLocationwiseBanner()) ? "Yes" : "No";
        $Data[0]['isSmartLoginEnable'] = $MODULES_OBJ->isEnableSmartLogin() ? "Yes" : "No";
        $Data[0]['WEBRTC_SOCKET_URL'] = WEBRTC_SOCKET_URL;
        $Data[0]['WEBRTC_STUN_URL'] = WEBRTC_STUN_URL;
        $Data[0]['WEBRTC_TURN_URL'] = WEBRTC_TURN_URL;
        $Data[0]['WEBRTC_ICE_SERVER_LIST'] = WEBRTC_ICE_SERVER_LIST;
        $Data[0]['WEBRTC_USERNAME'] = $tconfig["tsite_webrtc_username"];
        $Data[0]['WEBRTC_PASS'] = $tconfig["tsite_webrtc_pass"];

        $Data[0]['RIDE_ENABLED'] = RIDE_ENABLED;
        $Data[0]['DELIVERY_ENABLED'] = DELIVERY_ENABLED;
        $Data[0]['UFX_ENABLED'] = UFX_ENABLED;
        $Data[0]['DELIVERALL_ENABLED'] = DELIVERALL_ENABLED;
        $Data[0]['GENIE_ENABLED'] = GENIE_ENABLED;
        $Data[0]['RUNNER_ENABLED'] = RUNNER_ENABLED;
        $Data[0]['BIDDING_ENABLED'] = BIDDING_ENABLED;
        $Data[0]['VC_ENABLED'] = VC_ENABLED;
        $Data[0]['MED_UFX_ENABLED'] = MED_UFX_ENABLED;
        $Data[0]['RENT_ITEM_ENABLED'] = RENT_ITEM_ENABLED;
        $Data[0]['RENT_ESTATE_ENABLED'] = RENT_ESTATE_ENABLED;
        $Data[0]['RENT_CARS_ENABLED'] = RENT_CARS_ENABLED;
        $Data[0]['NEARBY_ENABLED'] = NEARBY_ENABLED;
        $Data[0]['TRACK_SERVICE_ENABLED'] = TRACK_SERVICE_ENABLED;
        $Data[0]['RIDE_SHARE_ENABLED'] = RIDE_SHARE_ENABLED;
        $Data[0]['SERVICE_CATEGORIES_ARR'] = SERVICE_CATEGORIES_ARR;

        $Data[0]['MANAGE_GALLERY_INFO'] = $tconfig['tsite_url'] . 'store_gallery_info.php?vLang=' . $languageCode;

        unset($Data[0]['tLocationUpdateDate']);
        unset($Data[0]['tSeenAdvertiseTime']);
        unset($Data[0]['CRON_TIME']);
        unset($Data[0]['tLastOnline']);
        unset($Data[0]['MAIL_FOOTER']);

        return $Data[0];
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['eStatus'] = "";
        $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
        setDataResponse($returnArr);
    }
}

if ($type == '') {
    $result['result'] = 0;
    $result['message'] = 'Required parameter missing.';
    setDataResponse($result);
}


include_once('include/include_common_function.php');
	
/* If no type found */
if (strtoupper(PACKAGE_TYPE) == "SHARK") {
    include_once('include/include_webservice_sharkfeatures.php'); // for 22 feature
}
//add fav store files feature
if ($MODULES_OBJ->isFavouriteStoreModuleAvailable()) {
    include_once('include/features/include_fav_store.php');
}
/* For Gojek-gopay added by SP start */
if ($MODULES_OBJ->isGojekGopayModuleAvailable()) {
    include_once('include/features/include_gojek_gopay.php');
}
/* For Gojek-gopay added by SP end */
/* For DriverSubscription added by SP start */
if ($MODULES_OBJ->isDriverSubscriptionModuleAvailable()) {
    include_once('include/features/include_driver_subscription.php');
}
/* For DriverSubscription added by SP end */
/* added by PM for Auto credit wallet driver on 25-01-2020 start */
if ($MODULES_OBJ->isAutoCreditToDriverModuleAvailable()) {
    include_once('include/features/include_auto_credit_driver.php');
}
/* added by PM for Auto credit wallet driver on 25-01-2020 end */

include_once('include/include_webservice_general.php');

/* -------------- For Luggage Lable default and as per user's Prefered language ----------------------- */
if ($type == 'language_label') {
    $lCode = isset($_REQUEST['vCode']) ? clean(strtoupper($_REQUEST['vCode'])) : ''; // User's prefered language
    /* find default language of website set by admin */
    if ($lCode == '') {
        //Added By HJ On 15-07-2020 For Optimize language_master Table Query Start
        $lCode = $LANG_OBJ->FetchDefaultLangData("vCode");
        //Added By HJ On 15-07-2020 For Optimize language_master Table Query End
        //$default_label = $obj->MySQLSelect("SELECT  `vCode` FROM  `language_master` WHERE eStatus = 'Active' AND `eDefault` = 'Yes' ");
        //$lCode = (isset($default_label[0]['vCode']) && $default_label[0]['vCode']) ? $default_label[0]['vCode'] : 'EN';
    }
    $sql = "SELECT  `vLabel` , `vValue`  FROM  `language_label`  WHERE  `vCode` = '" . $lCode . "' ";
    $all_label = $obj->MySQLSelect($sql);
    $x = array();
    for ($i = 0; $i < count($all_label); $i++) {
        $vLabel = $all_label[$i]['vLabel'];
        $vValue = $all_label[$i]['vValue'];
        $x[$vLabel] = $vValue;
    }
    $x['vCode'] = $lCode; // to check in which languge code it is loading
    setDataResponse($x);
}
// #########################################################################
// # NEW WEBSERVICE START ##
// #########################################################################
// #########################################################################
if ($type == 'generalConfigData') {
    $UserType = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : 'Passenger';
    $GeneralMemberId = isset($_REQUEST['GeneralMemberId']) ? trim($_REQUEST['GeneralMemberId']) : '';
    $vLang = isset($_REQUEST["vLang"]) ? $_REQUEST["vLang"] : '';
    $vCurrency = isset($_REQUEST["vCurrency"]) ? $_REQUEST["vCurrency"] : '';
    $storeCatArr = json_decode(serviceCategories, true);
    $GeneralAppVersion = isset($_REQUEST['GeneralAppVersion']) ? $_REQUEST['GeneralAppVersion'] : "";
    validateApiRequest();
    //it is done bc when in table in desc field insert like [] then null value is shown so app crash so put the following code
    foreach ($storeCatArr as $key => $value) {
        if (is_null($value['tDescription']) || $value['tDescription'] == '' || $value['tDescription'] == 'null' || empty($value['tDescription'])) {
            $storeCatArr[$key]['tDescription'] = '';
        }
        $eShowTerms = "No";
        $eProofUpload = "No";
        $tProofNote = "";
        if (empty($vLang)) {
            $vLang = $LANG_OBJ->FetchDefaultLangData('vCode');
        }
        $scsql = "select iServiceId,eShowTerms,eProofUpload,JSON_UNQUOTE(JSON_VALUE(tProofNote, '$.tProofNote_" . $vLang . "')) as tProofNote from service_categories WHERE iServiceId = " . $storeCatArr[$key]['iServiceId'];
        $scsqlData = $obj->MySQLSelect($scsql);
        if ($MODULES_OBJ->isEnableTermsServiceCategories()) {
            $eShowTerms = $scsqlData[0]['eShowTerms'];
        }
        if ($MODULES_OBJ->isEnableProofUploadServiceCategories()) {
            $eProofUpload = $scsqlData[0]['eProofUpload'];
            if (is_null($scsqlData[0]['tProofNote']) || $scsqlData[0]['tProofNote'] == "null") {
                $scsqlData[0]['tProofNote'] = "";
            }
            $tProofNote = $scsqlData[0]['tProofNote'];
        }
        if ($UserType == "Passenger") {
            $eProofUpload = "No";
        }
        $storeCatArr[$key]['eShowTerms'] = ($eShowTerms != '') ? $eShowTerms : "";
        $storeCatArr[$key]['eProofUpload'] = ($eProofUpload != '') ? $eProofUpload : "";//$eProofUpload;
        $storeCatArr[$key]['tProofNote'] = (!empty($tProofNote) && $tProofNote != NULL) ? $tProofNote : "";
    }
    $iserviceidstore = 0;
    if (count($storeCatArr) == 1) $iserviceidstore = $storeCatArr[0]['iServiceId'];
    $systemStoreEnable = $MODULES_OBJ->isSingleStoreSelection();
    if ($systemStoreEnable > 0) {
        for ($g = 0; $g < count($storeCatArr); $g++) {
            $storeData = getStoreDataForSystemStoreSelection($storeCatArr[$g]['iServiceId']);
            $iCompanyId = $storeData['iCompanyId'];
            $storeData['ispriceshow'] = $storeCatArr[$g]['iServiceId'];
            $storeCatArr[$g]['iCompanyId'] = $iCompanyId;
            $storeCatArr[$g]['STORE_DATA'] = $storeData;
            $storeCatArr[$g]['STORE_ID'] = $iCompanyId;
        }
        $companyData = getStoreDataForSystemStoreSelection(0);
        $DataArr['STORE_ID'] = $companyData[0]['iCompanyId'];
    }
    $DataArr['ServiceCategories'] = $storeCatArr;
    if ($vLang != '') {
        //Added By HJ On 09-07-2020 For Optimize language_master Table Query Start
        if (isset($languageAssociateArr[$vLang])) {
            $check_label = array();
            $check_label[] = $languageAssociateArr[$vLang];
        }
        else {
            $check_label = $obj->MySQLSelect("SELECT  `vCode` FROM  `language_master` WHERE eStatus = 'Active' AND `vCode` = '" . $vLang . "'");
        }
        $lCodeLang = $LANG_OBJ->FetchDefaultLangData("vCode");
        if (!empty($lCodeLang)) {
            $default_label = array();
            $default_label[]['vCode'] = $lCodeLang;
        }
        else {
            $default_label = $obj->MySQLSelect("SELECT `vCode` FROM  `language_master` WHERE eStatus = 'Active' AND `eDefault` = 'Yes' ");
        }
        //Added By HJ On 09-07-2020 For Optimize language_master Table Query End
        $vLang = (isset($check_label[0]['vCode']) && $check_label[0]['vCode']) ? $check_label[0]['vCode'] : $default_label[0]['vCode'];
    }
    else {
        //Added By HJ On 09-07-2020 For Optimize language_master Table Query Start
        $lCodeLang = $LANG_OBJ->FetchDefaultLangData("vCode");
        if (!empty($lCodeLang)) {
            $default_label = array();
            $default_label[]['vCode'] = $lCodeLang;
        }
        else {
            $default_label = $obj->MySQLSelect("SELECT `vCode` FROM  `language_master` WHERE eStatus = 'Active' AND `eDefault` = 'Yes' ");
        }
        //Added By HJ On 09-07-2020 For Optimize language_master Table Query End
        $vLang = $default_label[0]['vCode'];
    }
    /*if(count($storeCatArr) != 1){
        $iServiceId = "";
    }*/
    //Added By HJ On 24-07-2020 For langauge labele and Other Union Table Query Start
    if (isset($languageLabelDataArr['language_label_union_other_food_' . $vLang . "_" . $iServiceId])) {
        $languageLabelsArr = $languageLabelDataArr['language_label_union_other_food_' . $vLang . "_" . $iServiceId];
    }
    else {
        $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLang, "1", $iServiceId);
        $languageLabelDataArr['language_label_union_other_food_' . $vLang . "_" . $iServiceId] = $languageLabelsArr;
    }
    //Added By HJ On 24-07-2020 For langauge labele and Other Union Table Query End
    $DataArr['LanguageLabels'] = $languageLabelsArr;
    $DataArr['Action'] = "1";
    //Added By HJ On 09-07-2020 For Optimize language_master Table Query Start
    $defLangValues = array();
    if (count($Data_ALL_langArr) > 0) {
        for ($g = 0; $g < count($Data_ALL_langArr); $g++) {
            if (strtoupper($Data_ALL_langArr[$g]['eStatus']) == "ACTIVE") {
                $defLangValues[] = $Data_ALL_langArr[$g];
            }
        }
    }
    else {
        $defLangValues = $obj->MySQLSelect("SELECT vCode, vGMapLangCode, eDirectionCode as eType, vTitle,vCurrencyCode,vCurrencySymbol,eDefault  FROM  `language_master` WHERE  `eStatus` = 'Active' ORDER BY iDispOrder ASC");
    }
    //Added By HJ On 09-07-2020 For Optimize language_master Table Query End
    $DataArr['LIST_LANGUAGES'] = $defLangValues;
    for ($i = 0; $i < count($defLangValues); $i++) {
        if ($defLangValues[$i]['eDefault'] == "Yes") {
            $DataArr['DefaultLanguageValues'] = $defLangValues[$i];
        }
        $defLangValues[$i]['vService_BG_color'] = RANDOM_COLORS_ARR[array_rand(RANDOM_COLORS_ARR, 1)];
        $defLangValues[$i]['vService_TEXT_color'] = "#FFFFFF";
        $DataArr['LIST_LANGUAGES'][$i]['vService_BG_color'] = RANDOM_COLORS_ARR[array_rand(RANDOM_COLORS_ARR, 1)];
        $DataArr['LIST_LANGUAGES'][$i]['vService_TEXT_color'] = "#FFFFFF";
    }
    if ($vLang != "") {
        //Added By HJ On 09-07-2020 For Optimize language_master Table Query Start
        if (isset($languageAssociateArr[$vLang])) {
            $requireLangValues = array();
            $requireLangValues[] = $languageAssociateArr[$vLang];
        }
        else {
            $requireLangValues = $obj->MySQLSelect("SELECT vCode, vGMapLangCode, eDirectionCode as eType, vTitle,vCurrencyCode,vCurrencySymbol,eDefault  FROM  `language_master` WHERE  `vCode` = '" . $vLang . "' ");
        }
        //Added By HJ On 09-07-2020 For Optimize language_master Table Query End
        $DataArr['DefaultLanguageValues'] = $requireLangValues[0];
    }
    //Added By HJ On 09-07-2020 For Optimize currency Table Query Start
    if (count($Data_ALL_currency_Arr) > 0) {
        $defCurrencyValues = array();
        for ($c = 0; $c < count($Data_ALL_currency_Arr); $c++) {
            if (strtoupper($Data_ALL_currency_Arr[$c]['eStatus']) == "ACTIVE") {
                $defCurrencyValues[] = $Data_ALL_currency_Arr[$c];
            }
        }
    }
    else {
        $defCurrencyValues = $obj->MySQLSelect("SELECT iCurrencyId,vName, vSymbol,iDispOrder, eDefault,Ratio,fThresholdAmount,eStatus  FROM `currency` WHERE  `eStatus` = 'Active' ORDER BY `iDispOrder` ASC");
    }
    //Added By HJ On 09-07-2020 For Optimize currency Table Query End
    $DataArr['LIST_CURRENCY'] = $defCurrencyValues;
    for ($i = 0; $i < count($defCurrencyValues); $i++) {
        if ($defCurrencyValues[$i]['eDefault'] == "Yes") {
            $DataArr['DefaultCurrencyValues'] = $defCurrencyValues[$i];
        }
        $defCurrencyValues[$i]['vService_BG_color'] = RANDOM_COLORS_ARR[array_rand(RANDOM_COLORS_ARR, 1)];
        $defCurrencyValues[$i]['vService_TEXT_color'] = "#FFFFFF";
        $DataArr['LIST_CURRENCY'][$i]['vService_BG_color'] = RANDOM_COLORS_ARR[array_rand(RANDOM_COLORS_ARR, 1)];
        $DataArr['LIST_CURRENCY'][$i]['vService_TEXT_color'] = "#FFFFFF";
    }
    //Added By KP On 15-10-2019 For Get Active Currency Functionality Start
    $DataArr['UPDATE_TO_DEFAULT'] = 'No';
    if (!empty($vCurrency)) {
        //Added By HJ On 09-07-2020 For Optimize currency Table Query Start
        if (isset($currencyAssociateArr[$vCurrency])) {
            $check_currency = array();
            $check_currency[]['iCurrencyId'] = $currencyAssociateArr[$vCurrency]['iCurrencyId'];
        }
        else {
            $check_currency = $obj->MySQLSelect("SELECT iCurrencyId FROM  `currency` WHERE eStatus = 'Active' AND `vName` = '" . $vCurrency . "'");
        }
        //Added By HJ On 09-07-2020 For Optimize currency Table Query End
        if (count($check_currency) == 0) {
            $DataArr['UPDATE_TO_DEFAULT'] = 'Yes';
        }
    }
    //Added By KP On 15-10-2019 For Get Active Currency Functionality End
    if (empty($vCurrency)) {
        $DataArr['UPDATE_TO_DEFAULT'] = 'Yes';
    }
    $DataArr = array_merge($DataArr, $generalSystemConfigDataArr); // Added By HJ On 18-03-2020 For Optimized Function
    //Added By HJ On 16-07-2019 For Check Multiple Country Exists Or Not Start
    //Added By HJ On 09-07-2020 For Optimize currency Table Query Start
    if (count($country_data_retrieve) > 0) {
        $getCountryData = array();
        for ($h = 0; $h < count($country_data_retrieve); $h++) {
            if (strtoupper($country_data_retrieve[$h]['eStatus']) == "ACTIVE") {
                $getCountryData[] = $country_data_retrieve[$h]['iCountryId'];
            }
        }
    }
    else {
        $getCountryData = $obj->MySQLSelect("SELECT iCountryId FROM country WHERE eStatus='Active'");
    }
    //Added By HJ On 09-07-2020 For Optimize currency Table Query End
    $multiCountry = "No";
    if (count($getCountryData) > 1) {
        $multiCountry = "Yes";
    }
    $DataArr['showCountryList'] = $multiCountry;
    //Added By HJ On 16-07-2019 For Check Multiple Country Exists Or Not End
    $DataArr['GOOGLE_ANALYTICS'] = $DataArr['FACEBOOK_IFRAME'] = "";
    if ($UserType == "Passenger") {
        $DataArr['LINK_FORGET_PASS_PAGE_PASSENGER'] = $tconfig["tsite_url"] . $LINK_FORGET_PASS_PAGE_PASSENGER;
        $DataArr['CONFIG_CLIENT_ID'] = $CONFIG_CLIENT_ID;
        $DataArr['FACEBOOK_LOGIN'] = $PASSENGER_FACEBOOK_LOGIN;
        $DataArr['GOOGLE_LOGIN'] = $PASSENGER_GOOGLE_LOGIN;
        $DataArr['TWITTER_LOGIN'] = $PASSENGER_TWITTER_LOGIN;
        $DataArr['LINKEDIN_LOGIN'] = $PASSENGER_LINKEDIN_LOGIN;
        $DataArr['APPLE_LOGIN'] = $ENABLE_APPLE_LOGIN_FOR_USER;
    }
    else {
        $DataArr['LINK_FORGET_PASS_PAGE_DRIVER'] = $tconfig["tsite_url"] . $LINK_FORGET_PASS_PAGE_DRIVER;
        $DataArr['LINK_SIGN_UP_PAGE_DRIVER'] = $tconfig["tsite_url"] . $LINK_SIGN_UP_PAGE_DRIVER;
        $DataArr['FACEBOOK_LOGIN'] = $DRIVER_FACEBOOK_LOGIN;
        $DataArr['GOOGLE_LOGIN'] = $DRIVER_GOOGLE_LOGIN;
        $DataArr['TWITTER_LOGIN'] = $DRIVER_TWITTER_LOGIN;
        $DataArr['LINKEDIN_LOGIN'] = $DRIVER_LINKEDIN_LOGIN;
        $DataArr['APPLE_LOGIN'] = $ENABLE_APPLE_LOGIN_FOR_PROVIDER;
    }
    $DataArr['SERVER_MAINTENANCE_ENABLE'] = $MAINTENANCE_APPS;
    //Added By HJ On 16-07-2019 For Check Empty and # Value Of Configuration Start
    if (isset($DataArr['LIVE_CHAT_LICENCE_NUMBER']) && ($DataArr['LIVE_CHAT_LICENCE_NUMBER'] == "" || strpos($DataArr['LIVE_CHAT_LICENCE_NUMBER'], '#') !== false)) {
        $DataArr['ENABLE_LIVE_CHAT'] = "No";
    }
    if (isset($DataArr['AUDIO_CALLING_METHOD']) && strtoupper($DataArr['AUDIO_CALLING_METHOD']) == "SINCH") {
        if (isset($DataArr['SINCH_APP_ENVIRONMENT_HOST']) && ($DataArr['SINCH_APP_ENVIRONMENT_HOST'] == "" || strpos($DataArr['SINCH_APP_ENVIRONMENT_HOST'], '#') !== false)) {
            $DataArr['RIDE_DRIVER_CALLING_METHOD'] = "Normal";
        }
        if (isset($DataArr['SINCH_APP_KEY']) && ($DataArr['SINCH_APP_KEY'] == "" || strpos($DataArr['SINCH_APP_KEY'], '#') !== false)) {
            $DataArr['RIDE_DRIVER_CALLING_METHOD'] = "Normal";
        }
        if (isset($DataArr['SINCH_APP_SECRET_KEY']) && ($DataArr['SINCH_APP_SECRET_KEY'] == "" || strpos($DataArr['SINCH_APP_SECRET_KEY'], '#') !== false)) {
            $DataArr['RIDE_DRIVER_CALLING_METHOD'] = "Normal";
        }
    }
    //Added By HJ On 16-07-2019 For Check Empty and # Value Of Configuration End
    $DataArr['SITE_TYPE'] = SITE_TYPE;
    $usercountrydetailbytimezone = FetchMemberCountryData($GeneralMemberId, $UserType, $vTimeZone, $vUserDeviceCountry);
    $DataArr['vDefaultCountry'] = $usercountrydetailbytimezone['vDefaultCountry'];
    $DataArr['vDefaultCountryCode'] = $usercountrydetailbytimezone['vDefaultCountryCode'];
    $DataArr['vDefaultPhoneCode'] = $usercountrydetailbytimezone['vDefaultPhoneCode'];
    //$DataArr['vRCountryImage'] = $usercountrydetailbytimezone['vRImage']; //added by SP for country image related changes on 05-08-2019
    //$DataArr['vSCountryImage'] = $usercountrydetailbytimezone['vSImage']; //added by SP for country image related changes on 05-08-2019
    $DataArr['vRCountryImage'] = $usercountrydetailbytimezone['vRImageMember']; //added by SP for country image related changes on 06-09-2019
    $DataArr['vSCountryImage'] = $usercountrydetailbytimezone['vSImageMember']; //added by SP for country image related changes on 06-09-2019
    $DataArr['vDefaultCountryImage'] = empty($DataArr['vSCountryImage']) ? $usercountrydetailbytimezone['vDefaultCountryImage'] : $DataArr['vSCountryImage']; //added by SP for country image related changes on 06-08-2019
    $DataArr['OPEN_SETTINGS_URL_SCHEMA'] = "A###p####!!!!!###p####!!!!###@@@@#######-Pr###@@@!!!!###ef####s:r##@@@@#oo###t=Se####tt###i@@@##n##@@g#s";
    $DataArr['OPEN_LOCATION_SETTINGS_URL_SCHEMA'] = "A##@@@##p#!!!!##p###-#P###!!!##r##!!!!#ef#!!!##@@##s:###@@@####ro##@@###!!!!###o###@@@#t=P####riv####!!!###ac####y&###!!!##p###a##!!!#t##h=L###O##CA#@@#TI##O#@#N";
    $DataArr['SC_CONNECT_URL'] = getSocketURL();
    $DataArr['DELIVERALL'] = DELIVERALL;
    $DataArr['ONLYDELIVERALL'] = ONLYDELIVERALL;
    //if($iserviceidstore > 0){
    //    $DataArr['ONLYDELIVERALL'] = "Yes";
    //}
    $DataArr['ENABLE_CATEGORY_WISE_STORES'] = ($MODULES_OBJ->isStoreClassificationEnable() == 1) ? "Yes" : "No";
    $DataArr = getCustomeNotificationSound($DataArr); // Added By HJ On 06-08-2019 For Get Custome Sound Notification File Name
    $DataArr['CHECK_SYSTEM_STORE_SELECTION'] = ($systemStoreEnable > 0) ? "Yes" : "No";
    $DataArr['SERVICE_CATEGORIES_ARR'] = SERVICE_CATEGORIES_ARR;
    /* added by SP on 10-08-2020 for page active or not */
    $getPageData = $obj->MySQLSelect("SELECT iPageId,eStatus FROM pages WHERE iPageId IN(4,33,52)");
    foreach ($getPageData as $kPage => $vPage) {
        if ($vPage['iPageId'] == 4) $pagename = "showTermsCondition";
        if ($vPage['iPageId'] == 33) $pagename = "showPrivacyPolicy";
        if ($vPage['iPageId'] == 52) $pagename = "showAboutUs";
        $DataArr[$pagename] = $vPage['eStatus'] == 'Active' ? 'Yes' : 'No';
    }
    /*added by SP on 18-09-2020 */
    $DataArr['IS_RIDE_MODULE_AVAIL'] = ($MODULES_OBJ->isRideFeatureAvailable() == 1) ? "Yes" : "No";
    $DataArr['IS_DELIVERY_MODULE_AVAIL'] = ($MODULES_OBJ->isDeliveryFeatureAvailable() == 1) ? "Yes" : "No";
    $DataArr['IS_UFX_MODULE_AVAIL'] = ($MODULES_OBJ->isUberXFeatureAvailable() == 1) ? "Yes" : "No";
    $DataArr['IS_DELIVERALL_MODULE_AVAIL'] = ($MODULES_OBJ->isDeliverAllFeatureAvailable() == 1) ? "Yes" : "No";
    //added by SP on 14-10-2020 for timeslot changes
    $DataArr['ENABLE_TIMESLOT_ADDON'] = !empty($MODULES_OBJ->isEnableTimeslotFeature()) ? "Yes" : "No";
    // Added by HV on 04-03-2021 for App launch images
    $DataArr['APP_LAUNCH_IMAGES'] = "";

    if (!empty(getAppLaunchImages($vLang, $UserType))) {
        $DataArr['APP_LAUNCH_IMAGES'] = getAppLaunchImages($vLang, $UserType);
    }
    $DataArr['isSmartLoginEnable'] = ($MODULES_OBJ->isEnableSmartLogin() == 1) ? "Yes" : "No";
    if ($MODULES_OBJ->isEnableSmartLogin() || $DataArr['FACEBOOK_LOGIN'] == "Yes" || $DataArr['GOOGLE_LOGIN'] == "Yes" || $DataArr['LINKEDIN_LOGIN'] == "Yes") {
        $DataArr['isOtherSignInOptionsAvailable'] = "Yes";
    }

    $DataArr['UBERX_PARENT_CAT_ID'] = "0";

    $DataArr['WEBRTC_ICE_SERVER_LIST'] = WEBRTC_ICE_SERVER_LIST;
    $DataArr['WEBRTC_SOCKET_URL'] = WEBRTC_SOCKET_URL;
    $DataArr['WEBRTC_STUN_URL'] = WEBRTC_STUN_URL;
    $DataArr['WEBRTC_TURN_URL'] = WEBRTC_TURN_URL;
    $DataArr['WEBRTC_USERNAME'] = $tconfig["tsite_webrtc_username"];
    $DataArr['WEBRTC_PASS'] = $tconfig["tsite_webrtc_pass"];

    $DataArr['RIDE_ENABLED'] = RIDE_ENABLED;
    $DataArr['DELIVERY_ENABLED'] = DELIVERY_ENABLED;
    $DataArr['UFX_ENABLED'] = UFX_ENABLED;
    $DataArr['DELIVERALL_ENABLED'] = DELIVERALL_ENABLED;
    $DataArr['GENIE_ENABLED'] = GENIE_ENABLED;
    $DataArr['RUNNER_ENABLED'] = RUNNER_ENABLED;
    $DataArr['BIDDING_ENABLED'] = BIDDING_ENABLED;
    $DataArr['VC_ENABLED'] = VC_ENABLED;
    $DataArr['MED_UFX_ENABLED'] = MED_UFX_ENABLED;
    $DataArr['RENT_ITEM_ENABLED'] = RENT_ITEM_ENABLED;
    $DataArr['RENT_ESTATE_ENABLED'] = RENT_ESTATE_ENABLED;
    $DataArr['RENT_CARS_ENABLED'] = RENT_CARS_ENABLED;
    $DataArr['NEARBY_ENABLED'] = NEARBY_ENABLED;
    $DataArr['TRACK_SERVICE_ENABLED'] = TRACK_SERVICE_ENABLED;
    $DataArr['RIDE_SHARE_ENABLED'] = RIDE_SHARE_ENABLED;
    
    $ADD_CARD_STATUS = strtoupper($APP_PAYMENT_METHOD) . '_ADD_CARD_ENABLE';
    $DataArr['CARD_SAVE_ENABLE'] = $$ADD_CARD_STATUS;
    setDataResponse($DataArr);
}

// ##########################################################################
if ($type == "signup") {
    $fbid = isset($_REQUEST["vFbId"]) ? $_REQUEST["vFbId"] : '';
    $vAppleId = isset($_REQUEST["vAppleId"]) ? $_REQUEST["vAppleId"] : '';
    $Fname = isset($_REQUEST["vFirstName"]) ? $_REQUEST["vFirstName"] : '';
    $Lname = isset($_REQUEST["vLastName"]) ? $_REQUEST["vLastName"] : '';
    $email = isset($_REQUEST["vEmail"]) ? $_REQUEST["vEmail"] : '';
    $email = strtolower($email);
    $phone_mobile = isset($_REQUEST["vPhone"]) ? $_REQUEST["vPhone"] : '';
    $password = isset($_REQUEST["vPassword"]) ? $_REQUEST["vPassword"] : '';
    $iGcmRegId = isset($_REQUEST["vDeviceToken"]) ? $_REQUEST["vDeviceToken"] : '';
    $phoneCode = isset($_REQUEST["PhoneCode"]) ? $_REQUEST["PhoneCode"] : '';
    $CountryCode = isset($_REQUEST["CountryCode"]) ? $_REQUEST["CountryCode"] : '';
    $vInviteCode = isset($_REQUEST["vInviteCode"]) ? $_REQUEST["vInviteCode"] : '';
    $deviceType = isset($_REQUEST["vDeviceType"]) ? $_REQUEST["vDeviceType"] : 'Android';
    $vCurrency = isset($_REQUEST["vCurrency"]) ? $_REQUEST["vCurrency"] : '';
    $vLang = isset($_REQUEST["vLang"]) ? $_REQUEST["vLang"] : '';
    $user_type = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : 'Passenger';
    $eSignUpType = isset($_REQUEST["eSignUpType"]) ? $_REQUEST["eSignUpType"] : 'Normal';
    $vFirebaseDeviceToken = isset($_REQUEST["vFirebaseDeviceToken"]) ? $_REQUEST["vFirebaseDeviceToken"] : '';
    $vImageURL = isset($_REQUEST["vImageURL"]) ? $_REQUEST["vImageURL"] : '';
    // if (SITE_TYPE == 'Demo') {
    //     $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLang, "1", $iServiceId);
    //     $returnArr['Action'] = "0";
    //     $returnArr['message'] = strip_tags($languageLabelsArr["LBL_SIGNUP_DEMO_CONTENT"]);
    //     setDataResponse($returnArr);
    // }
    if ($email == "" && $phone_mobile == "" && $fbid == "" && $vAppleId == "") {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
        setDataResponse($returnArr);
    }
    if (!empty($vAppleId) && empty($email) && empty($phone_mobile)) {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_APPLE_LOGIN_ERROR_MSG";
        setDataResponse($returnArr);
    }
    if ($vCurrency == '') {
        //Added By HJ On 24-07-2020 For Optimize currency Table Query Start
        if (!empty($vSystemDefaultCurrencyName)) {
            $vCurrency = $vSystemDefaultCurrencyName;
        }
        else {
            $vCurrency = get_value('currency', 'vName', 'eDefault', 'Yes', '', 'true');
        }
        //Added By HJ On 24-07-2020 For Optimize currency Table Query End
    }
    if ($vLang == '') {
        //Added By HJ On 15-07-2020 For Optimize language_master Table Query Start
        $vLang = $LANG_OBJ->FetchDefaultLangData("vCode");
        //Added By HJ On 15-07-2020 For Optimize language_master Table Query End
    }
    //Added By HJ On 24-07-2020 For Optimize country Table Query Start
    if (count($country_data_retrieve) > 0) {
        for ($g = 0; $g < count($country_data_retrieve); $g++) {
            if ($country_data_retrieve[$g]['vPhoneCode'] == $phoneCode) {
                $CountryData = array();
                $CountryData[] = $country_data_retrieve[$g];
            }
        }
    }
    else {
        $CountryData = $obj->MySQLSelect("SELECT eZeroAllowed,vCountryCode FROM `country` WHERE vPhoneCode = '" . $phoneCode . "'");
    }
    //Added By HJ On 24-07-2020 For Optimize country Table Query End
    $eZeroAllowed = $CountryData[0]['eZeroAllowed'];
    if ($eZeroAllowed == 'Yes') {
        $phone_mobile = $phone_mobile;
    }
    else {
        $first = substr($phone_mobile, 0, 1);
        if ($first == "0") {
            $phone_mobile = substr($phone_mobile, 1);
        }
    }
    if ($fbid != "" || $vAppleId != "") {
        if ($Lname == "" || $Lname == NULL) {
            $username = explode(" ", $Fname);
            if ($username[1] != "") {
                $Fname = $username[0];
                $Lname = $username[1];
            }
        }
    }
    if ($user_type == "Passenger") {
        $tblname = "register_user";
        $eRefType = "Rider";
        $Data_passenger['vPhoneCode'] = $phoneCode;
        $Data_passenger['vCurrencyPassenger'] = $vCurrency;
        $vImage = 'vImgName';
        $iMemberId = 'iUserId';
    }
    else {
        $tblname = "register_driver";
        $eRefType = "Driver";
        $Data_passenger['vCode'] = $phoneCode;
        $Data_passenger['vCurrencyDriver'] = $vCurrency;
        $Data_passenger['iCompanyId'] = 1;
        $vImage = 'vImage';
        $iMemberId = 'iDriverId';
    }
    //$sql = "SELECT * FROM $tblname WHERE 1=1 AND IF('$email'!='',vEmail = '$email',0) OR IF('$phone_mobile'!='',vPhone = '$phone_mobile',0) OR IF('$fbid'!='',vFbId = '$fbid',0)";
    $check_passenger = $obj->MySQLSelect("SELECT * FROM $tblname WHERE 1=1 AND (IF('$email'!='',vEmail = '$email',0) OR IF('$fbid'!='',vFbId = '$fbid',0) OR IF('$vAppleId'!='',vAppleId = '$vAppleId',0)) AND eStatus != 'Deleted'");
    if (count($check_passenger) > 0) {
        $returnArr['Action'] = "0";
        if ($check_passenger[0]['eStatus'] == "Deleted") {
            $returnArr['message'] = "LBL_ACCOUNT_STATUS_DELETED_TXT";
            setDataResponse($returnArr);
        }
        if ($email == strtolower($check_passenger[0]['vEmail'])) {
            $returnArr['message'] = "LBL_ALREADY_REGISTERED_TXT";
            setDataResponse($returnArr);
        }
    }
    $Password_passenger = "";
    if ($password != "") {
        $Password_passenger = encrypt_bycrypt($password);
    }
    //Added By HJ On 31-12-2018 For Get LinkedIn Picture Data Start
    $socialData = array();
    if (isset($_REQUEST["socialData"])) {
        $socialData = (array)json_decode($_REQUEST["socialData"]);
    }
    if (isset($socialData['pictureUrls']) && $eSignUpType == 'LinkedIn') {
        $pictureUrls = $socialData['pictureUrls']->_total;
        if ($pictureUrls > 0) {
            $vImageURL = $socialData['pictureUrls']->values[0];
        }
        else {
            $vImageURL = $socialData['pictureUrl'];
        }
    }
    $eSystem = "";
    //Added By HJ On 31-12-2018 For Get LinkedIn Picture Data End
    if ($phone_mobile != "") {
        $checPhoneExist = checkMemberDataInfo($phone_mobile, "", $user_type, $CountryCode, "", $eSystem); //Added By HJ On 09-09-2019 For Chekc User Country and Mobile Number When Register
    }
    if (isset($checPhoneExist['status']) && $checPhoneExist['status'] == 0) {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_MOBILE_EXIST";
        setDataResponse($returnArr);
    }
    else if (isset($checPhoneExist['status']) && $checPhoneExist['status'] == 2) {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_INVALID_MEMBER_USER_COUNTRY_EMAIL_TXT";
        setDataResponse($returnArr);
    }
    $check_inviteCode = "";
    $inviteSuccess = false;
    if ($vInviteCode != "") {
        $check_inviteCode = $REFERRAL_OBJ->ValidateReferralCode($vInviteCode);
        if ($check_inviteCode == "" || $check_inviteCode == "0" || $check_inviteCode == 0) {
            $returnArr['Action'] = "0";
            $returnArr['message'] = "LBL_INVITE_CODE_INVALID";
            setDataResponse($returnArr);
        }
        else {
            $inviteRes = explode("|", $check_inviteCode);
            $Data_passenger['iRefUserId'] = $inviteRes[0];
            $Data_passenger['eRefType'] = $inviteRes[1];
            $inviteSuccess = true;
        }
    }
    $Data_passenger['vFbId'] = $fbid;
    $Data_passenger['vAppleId'] = $vAppleId;
    $Data_passenger['vName'] = $Fname;
    $Data_passenger['vLastName'] = $Lname;
    $Data_passenger['vEmail'] = $email;
    $Data_passenger['vPhone'] = $phone_mobile;
    $Data_passenger['vPassword'] = $Password_passenger;
    $Data_passenger['iGcmRegId'] = $iGcmRegId;
    $Data_passenger['vFirebaseDeviceToken'] = $vFirebaseDeviceToken;
    $Data_passenger['vLang'] = $vLang;
    // $Data_passenger['vPhoneCode']=$phoneCode;
    $Data_passenger['vCountry'] = $CountryCode;
    $Data_passenger['eDeviceType'] = $deviceType;
    $Data_passenger['vRefCode'] = $REFERRAL_OBJ->GenerateReferralCode($eRefType);
    // $Data_passenger['vCurrencyPassenger']=$vCurrency;
    $Data_passenger['dRefDate'] = @date('Y-m-d H:i:s');
    $Data_passenger['tRegistrationDate'] = @date('Y-m-d H:i:s');
    $Data_passenger['eSignUpType'] = $eSignUpType;
    if ($eSignUpType == "Facebook" || $eSignUpType == "Google") {
        $Data_passenger['eEmailVerified'] = "Yes";
    }
    $random = substr(md5(rand()), 0, 7);
    $Data_passenger['tDeviceSessionId'] = session_id() . time() . $random;
    $Data_passenger['tSessionId'] = session_id() . time();
    if (SITE_TYPE == 'Demo') {
        $Data_passenger['eStatus'] = 'Active';
        $Data_passenger['eEmailVerified'] = 'Yes';
        $Data_passenger['ePhoneVerified'] = 'Yes';
    }
    $id = $obj->MySQLQueryPerform($tblname, $Data_passenger, 'insert');

    if ($user_type == "Passenger") {
        createUserLog('Passenger', 'No', $id, $deviceType,'AppLogin','SignUp');
    } else {
        createUserLog('Driver', 'No', $id, $deviceType,'AppLogin','SignUp');
    }

    /* Multi Level Referral */
    if ($vInviteCode != "") {
        $refData = $obj->MySQLSelect("SELECT * FROM user_referrer_transaction WHERE iMemberId = " . $inviteRes[0] . " AND eUserType = '" . $inviteRes[1] . "'");
        if ($refData[0]['tReferrerInfo'] == "") {
            $referrerInfo[] = array('Position of Referrer' => 1, 'iMemberId' => $inviteRes[0], 'eUserType' => $inviteRes[1]);
        }
        else {
            $referrerInfo = json_decode($refData[0]['tReferrerInfo'], true);
            $referrerInfo[] = array('Position of Referrer' => (count($referrerInfo) + 1), 'iMemberId' => $inviteRes[0], 'eUserType' => $inviteRes[1]);
        }
        $Data_Referrer['tReferrerInfo'] = json_encode($referrerInfo);
        $Data_Referrer['iMemberId'] = $id;
        $Data_Referrer['eUserType'] = ($user_type == "Passenger") ? 'Rider' : 'Driver';
        $obj->MySQLQueryPerform("user_referrer_transaction", $Data_Referrer, 'insert');
    }
    /* Multi Level Referral End */
    // # Upload Image of Member if SignUp from Google, Facebook Or Twitter ##
    if ($fbid != 0 || $fbid != "") {
        $UserImage = UploadUserImage($id, $user_type, $eSignUpType, $fbid, $vImageURL);
        if ($UserImage != "") {
            $where = " $iMemberId = '$id' ";
            $Data_update_image_member[$vImage] = $UserImage;
            $imageuploadid = $obj->MySQLQueryPerform($tblname, $Data_update_image_member, 'update', $where);
        }
    }
    // # Upload Image of Member if SignUp from Google, Facebook Or Twitter ##
    // $sql_checkLangCode = "SELECT  vCode FROM  language_master WHERE `eStatus` = 'Active' AND `eDefault` = 'Yes' ";
    // $Data_checkLangCode = $obj->MySQLSelect($sql_checkLangCode);
    $returnArr['changeLangCode'] = "Yes";
    $returnArr['UpdatedLanguageLabels'] = $LANG_OBJ->FetchLanguageLabels($vLang, "1", $iServiceId);
    $returnArr['vLanguageCode'] = $vLang;
    //Added By HJ On 24-07-2020 For Optimization language_master Table Query Start
    if (isset($languageAssociateArr[$vLang])) {
        $Data_checkLangCode = array();
        $Data_checkLangCode[] = $languageAssociateArr[$vLang];
    }
    else {
        $Data_checkLangCode = $obj->MySQLSelect("SELECT * FROM language_master WHERE `vCode` = '" . $vLang . "' ");
    }
    //Added By HJ On 24-07-2020 For Optimization language_master Table Query End
    $returnArr['langType'] = $Data_checkLangCode[0]['eDirectionCode'];
    $returnArr['vGMapLangCode'] = $Data_checkLangCode[0]['vGMapLangCode'];
    //$defLangValues = $obj->MySQLSelect("SELECT vCode, vGMapLangCode, eDirectionCode as eType, vTitle,vCurrencyCode,vCurrencySymbol,eDefault  FROM  `language_master` WHERE  `eStatus` = 'Active' ORDER BY iDispOrder ASC ");
    //Added By HJ On 24-07-2020 For Optimization language_master Table Query Start
    if (count($Data_ALL_langArr) > 0) {
        $defLangValues = array();
        for ($g = 0; $g < count($Data_ALL_langArr); $g++) {
            if (strtoupper($Data_ALL_langArr[$g]['eStatus']) == "ACTIVE") {
                $defLangValues[] = $Data_ALL_langArr[$g];
            }
        }
    }
    else {
        $defLangValues = $obj->MySQLSelect("SELECT vCode, vGMapLangCode, eDirectionCode as eType, vTitle,vCurrencyCode,vCurrencySymbol,eDefault  FROM  `language_master` WHERE  `eStatus` = 'Active' ORDER BY iDispOrder ASC ");
    }
    //Added By HJ On 24-07-2020 For Optimization language_master Table Query End
    $returnArr['LIST_LANGUAGES'] = $defLangValues;
    for ($i = 0; $i < count($defLangValues); $i++) {
        if ($defLangValues[$i]['eDefault'] == "Yes") {
            $returnArr['DefaultLanguageValues'] = $defLangValues[$i];
        }
        $defLangValues[$i]['vService_BG_color'] = RANDOM_COLORS_ARR[array_rand(RANDOM_COLORS_ARR, 1)];
        $defLangValues[$i]['vService_TEXT_color'] = "#FFFFFF";
        $returnArr['LIST_LANGUAGES'][$i]['vService_BG_color'] = RANDOM_COLORS_ARR[array_rand(RANDOM_COLORS_ARR, 1)];
        $returnArr['LIST_LANGUAGES'][$i]['vService_TEXT_color'] = "#FFFFFF";
    }
    $defCurrencyValues = $obj->MySQLSelect("SELECT iCurrencyId,vName, vSymbol,iDispOrder, eDefault,Ratio,fThresholdAmount,eStatus  FROM  `currency` WHERE  `eStatus` = 'Active' ORDER BY iDispOrder ASC ");
    $returnArr['LIST_CURRENCY'] = $defCurrencyValues;
    for ($i = 0; $i < count($defCurrencyValues); $i++) {
        if ($defCurrencyValues[$i]['eDefault'] == "Yes") {
            $returnArr['DefaultCurrencyValues'] = $defCurrencyValues[$i];
        }
        $defCurrencyValues[$i]['vService_BG_color'] = RANDOM_COLORS_ARR[array_rand(RANDOM_COLORS_ARR, 1)];
        $defCurrencyValues[$i]['vService_TEXT_color'] = "#FFFFFF";
        $returnArr['LIST_CURRENCY'][$i]['vService_BG_color'] = RANDOM_COLORS_ARR[array_rand(RANDOM_COLORS_ARR, 1)];
        $returnArr['LIST_CURRENCY'][$i]['vService_TEXT_color'] = "#FFFFFF";
    }
    if (SITE_TYPE == 'Demo') {
        $result = $obj->MySQLSelect("SELECT GROUP_CONCAT(iVehicleTypeId)as countId FROM `vehicle_type`");
        $Drive_vehicle['iDriverId'] = $id;
        $Drive_vehicle['iCompanyId'] = "1";
        $Drive_vehicle['iMakeId'] = "5";
        $Drive_vehicle['iModelId'] = "9";
        $Drive_vehicle['iYear'] = "2014";
        $Drive_vehicle['vLicencePlate'] = "CK201";
        $Drive_vehicle['eStatus'] = "Active";
        $Drive_vehicle['eCarX'] = "Yes";
        $Drive_vehicle['eCarGo'] = "Yes";
        $Drive_vehicle['vCarType'] = $result[0]['countId'];
        $iDriver_VehicleId = $obj->MySQLQueryPerform('driver_vehicle', $Drive_vehicle, 'insert');
        $obj->sql_query("UPDATE register_driver set iDriverVehicleId='" . $iDriver_VehicleId . "' WHERE iDriverId='" . $id . "'");
    }
    if ($id > 0) {
        if ($inviteSuccess == true) {
            $eFor = "Referrer";
            $tDescription = "Referral amount credited";
            $dDate = Date('Y-m-d H:i:s');
            $ePaymentStatus = "Unsettelled";
        }
        /* new added */
        $returnArr['Action'] = "1";
        if ($user_type == "Passenger") {
            $returnArr['message'] = getPassengerDetailInfo($id, "", "");
        }
        else {
            $returnArr['message'] = getDriverDetailInfo($id);
        }
        $maildata['EMAIL'] = $email;
        $maildata['NAME'] = $Fname;
        //$maildata['PASSWORD'] = "Password: " . $password; //Commented By HJ On 11-01-2019 For Hide Password As Per Discuss With QA BM
        $maildata['SOCIALNOTES'] = '';
        if ($user_type == "Passenger") {
            $COMM_MEDIA_OBJ->SendMailToMember("MEMBER_REGISTRATION_USER", $maildata);
        }
        else {
            $COMM_MEDIA_OBJ->SendMailToMember("DRIVER_REGISTRATION_USER", $maildata);
            $COMM_MEDIA_OBJ->SendMailToMember("DRIVER_REGISTRATION_ADMIN", $maildata);
        }
        setDataResponse($returnArr);
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
        setDataResponse($returnArr);
    }
}

if ($type == "signIn") {
    $Emid = isset($_REQUEST["vEmail"]) ? $_REQUEST["vEmail"] : '';
    $Emid = strtolower($Emid);
    $PhoneCode = isset($_REQUEST["PhoneCode"]) ? $_REQUEST["PhoneCode"] : '';
    $CountryCode = isset($_REQUEST["CountryCode"]) ? $_REQUEST["CountryCode"] : '';
    $Password_user = $userPassword = isset($_REQUEST["vPassword"]) ? $_REQUEST["vPassword"] : '';
    $GCMID = isset($_REQUEST["vDeviceToken"]) ? $_REQUEST["vDeviceToken"] : '';
    $DeviceType = isset($_REQUEST["vDeviceType"]) ? $_REQUEST["vDeviceType"] : 'Android';
    $UserType = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : 'Passenger'; // Passenger, Driver , Company
    $vCurrency = isset($_REQUEST["vCurrency"]) ? $_REQUEST["vCurrency"] : '';
    $vLang = isset($_REQUEST["vLang"]) ? $_REQUEST["vLang"] : '';
    $vFirebaseDeviceToken = isset($_REQUEST["vFirebaseDeviceToken"]) ? $_REQUEST["vFirebaseDeviceToken"] : '';
    $eSystemAppType = isset($_REQUEST["eSystemAppType"]) ? $_REQUEST["eSystemAppType"] : '';
    $GeneralMemberId = isset($_REQUEST["GeneralMemberId"]) ? $_REQUEST["GeneralMemberId"] : '';
    $tSessionId = isset($_REQUEST["tSessionId"]) ? $_REQUEST["tSessionId"] : '';
    // $Password_user = encrypt($Password_user);
    if (SITE_TYPE == "Demo") {
        $tablename = ($UserType == 'Passenger') ? "register_user" : "register_driver";
        $iMemberId = ($UserType == 'Passenger') ? "iUserId" : "iDriverId";
        $iUserId = ($UserType == 'Passenger') ? "36" : "31";
        $Member_Currency = ($UserType == 'Passenger') ? "vCurrencyPassenger" : "vCurrencyDriver";
        $Member_Image = ($UserType == 'Passenger') ? "vImgName" : "vImage";
        $Data_Update_Member['vName'] = ($UserType == 'Passenger') ? "MAC" : "Mark";
        $Data_Update_Member['vLastName'] = ($UserType == 'Passenger') ? "ANDREW" : "Bruno";
        $Data_Update_Member['vEmail'] = ($UserType == 'Passenger') ? "rider@gmail.com" : "driver@gmail.com";
        $Password_User = encrypt_bycrypt("123456");
        $Data_Update_Member['vPassword'] = $Password_User;
        $Data_Update_Member['vCountry'] = ($UserType == 'Passenger') ? "US" : "US";
        $Data_Update_Member['vLang'] = ($UserType == 'Passenger') ? "EN" : "EN";
        $Data_Update_Member['eStatus'] = ($UserType == 'Passenger') ? "Active" : "active";
        $Data_Update_Member[$Member_Currency] = ($UserType == 'Passenger') ? "USD" : "USD";
        $Data_Update_Member[$Member_Image] = ($UserType == 'Passenger') ? "1504878922_81109.jpg" : "1505208397_54463.jpg";
        $where = " $iMemberId = '" . $iUserId . "'";
        $Update_Member_id = $obj->MySQLQueryPerform($tablename, $Data_Update_Member, 'update', $where);
    }
    $passUserType = $UserType;
    if (strtoupper($UserType) == "KIOSK" || strtoupper($UserType) == "HOTEL") {
        $passUserType = "ADMIN";
    }
    $eSystem = "";
    if ($UserType == "Company") {
        $eSystem = "DeliverAll";
    }
    //$checkValid = checkMemberDataInfo($Emid, $userPassword, $passUserType, '', "", $eSystem); //Added By HJ On 09-09-2019 For Chekc User Country and Mobile Number When Login
    //  if(isset($_REQUEST["PhoneCode"])){
    //     $checkValid = checkMemberDataInfoLogin($Emid, $userPassword, $passUserType, $PhoneCode, "", $eSystem);
    // }else{
    //     // For Old Apps.
    //     $checkValid = checkMemberDataInfo($Emid, $userPassword, $passUserType, "", "", $eSystem); //Added By HJ On 09-09-2019 For Chekc User Country and Mobile Number When Login
    // }
    $checkValid = checkMemberDataInfo($Emid, $userPassword, $passUserType, $CountryCode, "", $eSystem);
    $isBiometricLogin = "No";
    if (empty($Emid) && empty($userPassword)) {
        $isBiometricLogin = "Yes";
        $login_session = $obj->MySQLSelect("SELECT * FROM member_login_session_log WHERE iMemberId = '$GeneralMemberId' AND eUserType = '$UserType' AND tSessionId = '$tSessionId'");
        if (!empty($login_session) && count($login_session) > 0) {
            $tSessionId = $login_session[0]['tSessionId'];
        }
        else {
            $login_session_data = array();
            $login_session_data['iMemberId'] = $GeneralMemberId;
            $login_session_data['eUserType'] = $UserType;
            $login_session_data['tSessionId'] = $tSessionId;
            $login_session_data['eDeviceType'] = $DeviceType;
            $login_session_data['iGcmRegId'] = $GCMID;
            $obj->MySQLQueryPerform("member_login_session_log", $login_session_data, 'insert');
        }
    }
    else if (isset($checkValid['status']) && $checkValid['status'] == 0) {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_WRONG_DETAIL";
        setDataResponse($returnArr);
    }
    else if (isset($checkValid['status']) && $checkValid['status'] == 2) {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_INVALID_MEMBER_USER_COUNTRY_EMAIL_TXT";
        setDataResponse($returnArr);
    }
    $primaryField = "iCompanyId";
    $primaryField1 = "iCompanyId";
    if ($UserType == "Passenger") {
        $primaryField = "iUserId";
        $primaryField1 = "iUserId";
    }
    else if ($UserType == "Driver") {
        $primaryField = "iDriverId";
        $primaryField1 = "rd.iDriverId";
    }
    $whereCondition = "";
    if (isset($checkValid['USER_DATA'][$primaryField]) && $checkValid['USER_DATA'][$primaryField] > 0) {
        $whereCondition = " AND $primaryField1='" . $checkValid['USER_DATA'][$primaryField] . "'";
    }
    if ($UserType == "Passenger") {
        if ($isBiometricLogin == "No") {
            $sql = "SELECT iUserId,eStatus,vLang,vTripStatus,vLang,vPassword FROM `register_user` WHERE (vEmail='$Emid' OR vPhone = '$Emid') $whereCondition AND eStatus != 'Deleted'";
        }
        else {
            $sql = "SELECT iUserId,eStatus,vLang,vTripStatus,vLang,vPassword FROM `register_user` WHERE iUserId='$GeneralMemberId'";
        }
        $Data = $obj->MySQLSelect($sql);
        /* $iCabRequestId= get_value('cab_request_now', 'max(iCabRequestId)', 'iUserId',$Data[0]['iUserId'],'','true');
        $eStatus_cab= get_value('cab_request_now', 'eStatus', 'iCabRequestId',$iCabRequestId,'','true'); */
        $sql_cabrequest = "SELECT iCabRequestId,eStatus FROM `cab_request_now` WHERE iUserId='" . $Data[0]['iUserId'] . "' ORDER BY iCabRequestId DESC LIMIT 0,1";
        $Data_cabrequest = $obj->MySQLSelect($sql_cabrequest);
        $iCabRequestId = $Data_cabrequest[0]['iCabRequestId'];
        $eStatus_cab = $Data_cabrequest[0]['eStatus'];
        if (count($Data) > 0) {
            // Check For Valid password #
            $hash = $Data[0]['vPassword'];
            $checkValidPass = $AUTH_OBJ->VerifyPassword($Password_user, $hash);
            if ($checkValidPass == 0 && $isBiometricLogin == "No") {
                $returnArr['Action'] = "0";
                $returnArr['message'] = "LBL_WRONG_DETAIL";
                setDataResponse($returnArr);
            }
            // Check For Valid password #
            if ($Data[0]['eStatus'] == "Active") {
                $iUserId_passenger = $Data[0]['iUserId'];
                $where = " iUserId = '$iUserId_passenger' ";
                if ($Data[0]['vLang'] == "" && $vLang == "") {
                    //Added By HJ On 15-07-2020 For Optimize language_master Table Query Start
                    $vLang = $LANG_OBJ->FetchDefaultLangData("vCode");
                    //Added By HJ On 15-07-2020 For Optimize language_master Table Query End
                    $Data_update_passenger['vLang'] = $vLang;
                }
                if ($vLang != "") {
                    $Data_update_passenger['vLang'] = $vLang;
                    $Data[0]['vLang'] = $vLang;
                }
                if ($vCurrency != "") {
                    $Data_update_passenger['vCurrencyPassenger'] = $vCurrency;
                }
                if ($GCMID != '') {
                    $Data_update_passenger['iGcmRegId'] = $GCMID;
                    $Data_update_passenger['eDeviceType'] = $DeviceType;
                    if ($isBiometricLogin == "No") {
                        $Data_update_passenger['tSessionId'] = session_id() . time();
                    }
                    else {
                        $Data_update_passenger['tSessionId'] = $tSessionId;
                    }
                    $Data_update_passenger['vFirebaseDeviceToken'] = $vFirebaseDeviceToken;
                    if (SITE_TYPE == "Demo") {
                        $Data_update_passenger['tRegistrationDate'] = date('Y-m-d H:i:s');
                    }
                    $id = $obj->MySQLQueryPerform("register_user", $Data_update_passenger, 'update', $where);
                }
                if ($eStatus_cab == "Requesting") {
                    $where1 = " iCabRequestId = '$iCabRequestId' ";
                    $Data_update_cab_now['eStatus'] = "Cancelled";
                    $id = $obj->MySQLQueryPerform("cab_request_now", $Data_update_cab_now, 'update', $where1);
                }
                $returnArr['changeLangCode'] = "Yes";
                //Added By HJ On 23-07-2020 For langauge labele and Other Union Table Query Start
                if (isset($languageLabelDataArr['language_label_union_other_food_' . $Data[0]['vLang'] . "_" . $iServiceId])) {
                    $languageLabelsArr = $languageLabelDataArr['language_label_union_other_food_' . $Data[0]['vLang'] . "_" . $iServiceId];
                }
                else {
                    $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($Data[0]['vLang'], "1", $iServiceId);
                    $languageLabelDataArr['language_label_union_other_food_' . $Data[0]['vLang'] . "_" . $iServiceId] = $languageLabelsArr;
                }
                //Added By HJ On 23-07-2020 For langauge labele and Other Union Table Query End
                $returnArr['UpdatedLanguageLabels'] = $languageLabelsArr;
                $returnArr['vLanguageCode'] = $Data[0]['vLang'];
                //Added By HJ On 23-07-2020 For Optimization language_master Table Query Start
                if (isset($languageAssociateArr[$Data[0]['vLang']])) {
                    $Data_checkLangCode = array();
                    $Data_checkLangCode[] = $languageAssociateArr[$Data[0]['vLang']];
                }
                else {
                    $Data_checkLangCode = $obj->MySQLSelect("SELECT * FROM language_master WHERE `vCode` = '" . $Data[0]['vLang'] . "' ");
                }
                //Added By HJ On 23-07-2020 For Optimization language_master Table Query End
                $returnArr['langType'] = $Data_checkLangCode[0]['eDirectionCode'];
                $returnArr['vGMapLangCode'] = $Data_checkLangCode[0]['vGMapLangCode'];
                //Added By HJ On 23-07-2020 For Optimization language_master Table Query Start
                if (count($Data_ALL_langArr) > 0) {
                    $defLangValues = array();
                    for ($g = 0; $g < count($Data_ALL_langArr); $g++) {
                        if (strtoupper($Data_ALL_langArr[$g]['eStatus']) == "ACTIVE") {
                            $defLangValues[] = $Data_ALL_langArr[$g];
                        }
                    }
                }
                else {
                    $defLangValues = $obj->MySQLSelect("SELECT vCode, vGMapLangCode, eDirectionCode as eType, vTitle,vCurrencyCode,vCurrencySymbol,eDefault  FROM  `language_master` WHERE  `eStatus` = 'Active' ORDER BY iDispOrder ASC ");
                }
                //Added By HJ On 23-07-2020 For Optimization language_master Table Query End
                $returnArr['LIST_LANGUAGES'] = $defLangValues;
                for ($i = 0; $i < count($defLangValues); $i++) {
                    if ($defLangValues[$i]['eDefault'] == "Yes") {
                        $returnArr['DefaultLanguageValues'] = $defLangValues[$i];
                    }
                    $defLangValues[$i]['vService_BG_color'] = RANDOM_COLORS_ARR[array_rand(RANDOM_COLORS_ARR, 1)];
                    $defLangValues[$i]['vService_TEXT_color'] = "#FFFFFF";
                    $returnArr['LIST_LANGUAGES'][$i]['vService_BG_color'] = RANDOM_COLORS_ARR[array_rand(RANDOM_COLORS_ARR, 1)];
                    $returnArr['LIST_LANGUAGES'][$i]['vService_TEXT_color'] = "#FFFFFF";
                }
                //Added By HJ On 23-07-2020 For Optimize currency Table Query Start
                if (count($Data_ALL_currency_Arr) > 0) {
                    $defCurrencyValues = array();
                    for ($c = 0; $c < count($Data_ALL_currency_Arr); $c++) {
                        if (strtoupper($Data_ALL_currency_Arr[$c]['eStatus']) == "ACTIVE") {
                            $defCurrencyValues[] = $Data_ALL_currency_Arr[$c];
                        }
                    }
                }
                else {
                    $defCurrencyValues = $obj->MySQLSelect("SELECT iCurrencyId,vName, vSymbol,iDispOrder, eDefault,Ratio,fThresholdAmount,eStatus  FROM  `currency` WHERE  `eStatus` = 'Active' ORDER BY iDispOrder ASC ");
                }
                //Added By HJ On 23-07-2020 For Optimize currency Table Query End
                $returnArr['LIST_CURRENCY'] = $defCurrencyValues;
                for ($i = 0; $i < count($defCurrencyValues); $i++) {
                    if ($defCurrencyValues[$i]['eDefault'] == "Yes") {
                        $returnArr['DefaultCurrencyValues'] = $defCurrencyValues[$i];
                    }
                    $defCurrencyValues[$i]['vService_BG_color'] = RANDOM_COLORS_ARR[array_rand(RANDOM_COLORS_ARR, 1)];
                    $defCurrencyValues[$i]['vService_TEXT_color'] = "#FFFFFF";
                    $returnArr['LIST_CURRENCY'][$i]['vService_BG_color'] = RANDOM_COLORS_ARR[array_rand(RANDOM_COLORS_ARR, 1)];
                    $returnArr['LIST_CURRENCY'][$i]['vService_TEXT_color'] = "#FFFFFF";
                }
                $returnArr['Action'] = "1";
                $returnArr['message'] = getPassengerDetailInfo($Data[0]['iUserId'], '', "");
                $returnArr['message'] = getCustomeNotificationSound($returnArr['message']);
                $returnArr['message']['LIST_CURRENCY'] = $defCurrencyValues; //put bc naresh wants it in message..
                createUserLog($UserType, "No", $Data[0]['iUserId'], $DeviceType);
                setDataResponse($returnArr);
            }
            else {
                $returnArr['Action'] = "0";
                $returnArr['RESTRICT_APP'] = "Yes";
                if ($Data[0]['eStatus'] != "Deleted") {
                    $returnArr['message'] = "LBL_CONTACT_US_STATUS_NOTACTIVE_PASSENGER";
                    $returnArr['message_title'] = "LBL_ACC_INACTIVE_TITLE";
                    $returnArr['eStatus'] = $Data[0]['eStatus'];
                    $returnArr['isAccountInactive'] = "Yes";
                } else {
                    $returnArr['message'] = "LBL_ACC_DELETE_TXT";
                    $returnArr['message_title'] = "LBL_ACC_DELETE_TITLE";
                    $returnArr['eStatus'] = $Data[0]['eStatus'];
                    $returnArr['isAccountDeleted'] = "Yes";
                }
                setDataResponse($returnArr);
            }
        }
        else {
            $returnArr['Action'] = "0";
            $returnArr['message'] = "LBL_WRONG_DETAIL";
            setDataResponse($returnArr);
        }
    }
    else if ($UserType == "Driver") {
        if ($isBiometricLogin == "No") {
            $Data = $obj->MySQLSelect("SELECT rd.iDriverId,rd.eStatus,rd.vLang,rd.vPassword FROM `register_driver` as rd WHERE ( rd.vEmail='$Emid' OR rd.vPhone = '$Emid' ) $whereCondition AND rd.eStatus != 'Deleted'");
        }
        else {
            $Data = $obj->MySQLSelect("SELECT rd.iDriverId,rd.eStatus,rd.vLang,rd.vPassword FROM `register_driver` as rd WHERE rd.iDriverId='$GeneralMemberId'");
        }
        if (count($Data) > 0) {
            // Check For Valid password #
            $hash = $Data[0]['vPassword'];
            $checkValidPass = $AUTH_OBJ->VerifyPassword($Password_user, $hash);
            if ($checkValidPass == 0 && $isBiometricLogin == "No") {
                $returnArr['Action'] = "0";
                $returnArr['message'] = "LBL_WRONG_DETAIL";
                setDataResponse($returnArr);
            }
            // Check For Valid password #
            if ($Data[0]['eStatus'] != "Deleted") {
                if ($GCMID != '') {
                    $iDriverId_driver = $Data[0]['iDriverId'];
                    $where = " iDriverId = '$iDriverId_driver' ";
                    if ($Data[0]['vLang'] == "" && $vLang == "") {
                        //Added By HJ On 15-07-2020 For Optimize language_master Table Query Start
                        $vLang = $LANG_OBJ->FetchDefaultLangData("vCode");
                        //Added By HJ On 15-07-2020 For Optimize language_master Table Query End
                        $Data_update_driver['vLang'] = $vLang;
                    }
                    if ($vLang != "") {
                        $Data_update_driver['vLang'] = $vLang;
                        $Data[0]['vLang'] = $vLang;
                    }
                    if ($vCurrency != "") {
                        $Data_update_driver['vCurrencyDriver'] = $vCurrency;
                    }
                    $Data_update_driver['vFirebaseDeviceToken'] = $vFirebaseDeviceToken;
                    if ($isBiometricLogin == "No") {
                        $Data_update_driver['tSessionId'] = session_id() . time();
                    }
                    else {
                        $Data_update_driver['tSessionId'] = $tSessionId;
                    }
                    $Data_update_driver['iGcmRegId'] = $GCMID;
                    $Data_update_driver['eDeviceType'] = $DeviceType;
                    $id = $obj->MySQLQueryPerform("register_driver", $Data_update_driver, 'update', $where);
                }
                $returnArr['changeLangCode'] = "Yes";
                //Added By HJ On 23-07-2020 For langauge labele and Other Union Table Query Start
                if (isset($languageLabelDataArr['language_label_union_other_food_' . $Data[0]['vLang'] . "_" . $iServiceId])) {
                    $languageLabelsArr = $languageLabelDataArr['language_label_union_other_food_' . $Data[0]['vLang'] . "_" . $iServiceId];
                }
                else {
                    $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($Data[0]['vLang'], "1", $iServiceId);
                    $languageLabelDataArr['language_label_union_other_food_' . $Data[0]['vLang'] . "_" . $iServiceId] = $languageLabelsArr;
                }
                //Added By HJ On 23-07-2020 For langauge labele and Other Union Table Query End
                $returnArr['UpdatedLanguageLabels'] = $languageLabelsArr;
                $returnArr['vLanguageCode'] = $Data[0]['vLang'];
                //Added By HJ On 23-07-2020 For Optimization language_master Table Query Start
                if (isset($languageAssociateArr[$Data[0]['vLang']])) {
                    $Data_checkLangCode = array();
                    $Data_checkLangCode[] = $languageAssociateArr[$Data[0]['vLang']];
                }
                else {
                    $Data_checkLangCode = $obj->MySQLSelect("SELECT * FROM language_master WHERE `vCode` = '" . $Data[0]['vLang'] . "' ");
                }
                //Added By HJ On 23-07-2020 For Optimization language_master Table Query End
                $returnArr['langType'] = $Data_checkLangCode[0]['eDirectionCode'];
                $returnArr['vGMapLangCode'] = $Data_checkLangCode[0]['vGMapLangCode'];
                //Added By HJ On 23-07-2020 For Optimization language_master Table Query Start
                if (count($Data_ALL_langArr) > 0) {
                    $defLangValues = array();
                    for ($g = 0; $g < count($Data_ALL_langArr); $g++) {
                        if (strtoupper($Data_ALL_langArr[$g]['eStatus']) == "ACTIVE") {
                            $defLangValues[] = $Data_ALL_langArr[$g];
                        }
                    }
                }
                else {
                    $defLangValues = $obj->MySQLSelect("SELECT vCode, vGMapLangCode, eDirectionCode as eType, vTitle,vCurrencyCode,vCurrencySymbol,eDefault  FROM  `language_master` WHERE  `eStatus` = 'Active' ORDER BY iDispOrder ASC ");
                }
                //Added By HJ On 23-07-2020 For Optimization language_master Table Query End
                $returnArr['LIST_LANGUAGES'] = $defLangValues;
                for ($i = 0; $i < count($defLangValues); $i++) {
                    if ($defLangValues[$i]['eDefault'] == "Yes") {
                        $returnArr['DefaultLanguageValues'] = $defLangValues[$i];
                    }
                    $defLangValues[$i]['vService_BG_color'] = RANDOM_COLORS_ARR[array_rand(RANDOM_COLORS_ARR, 1)];
                    $defLangValues[$i]['vService_TEXT_color'] = "#FFFFFF";
                    $returnArr['LIST_LANGUAGES'][$i]['vService_BG_color'] = RANDOM_COLORS_ARR[array_rand(RANDOM_COLORS_ARR, 1)];
                    $returnArr['LIST_LANGUAGES'][$i]['vService_TEXT_color'] = "#FFFFFF";
                }
                //Added By HJ On 23-07-2020 For Optimize currency Table Query Start
                if (count($Data_ALL_currency_Arr) > 0) {
                    $defCurrencyValues = array();
                    for ($c = 0; $c < count($Data_ALL_currency_Arr); $c++) {
                        if (strtoupper($Data_ALL_currency_Arr[$c]['eStatus']) == "ACTIVE") {
                            $defCurrencyValues[] = $Data_ALL_currency_Arr[$c];
                        }
                    }
                }
                else {
                    $defCurrencyValues = $obj->MySQLSelect("SELECT iCurrencyId,vName, vSymbol,iDispOrder, eDefault,Ratio,fThresholdAmount,eStatus  FROM  `currency` WHERE  `eStatus` = 'Active' ORDER BY iDispOrder ASC ");
                }
                //Added By HJ On 23-07-2020 For Optimize currency Table Query End
                $returnArr['LIST_CURRENCY'] = $defCurrencyValues;
                for ($i = 0; $i < count($defCurrencyValues); $i++) {
                    if ($defCurrencyValues[$i]['eDefault'] == "Yes") {
                        $returnArr['DefaultCurrencyValues'] = $defCurrencyValues[$i];
                    }
                    $defCurrencyValues[$i]['vService_BG_color'] = RANDOM_COLORS_ARR[array_rand(RANDOM_COLORS_ARR, 1)];
                    $defCurrencyValues[$i]['vService_TEXT_color'] = "#FFFFFF";
                    $returnArr['LIST_CURRENCY'][$i]['vService_BG_color'] = RANDOM_COLORS_ARR[array_rand(RANDOM_COLORS_ARR, 1)];
                    $returnArr['LIST_CURRENCY'][$i]['vService_TEXT_color'] = "#FFFFFF";
                }
                $returnArr['Action'] = "1";
                $returnArr['message'] = getDriverDetailInfo($Data[0]['iDriverId'], 1);
                $returnArr['message'] = getCustomeNotificationSound($returnArr['message']);
                $returnArr['message']['LIST_CURRENCY'] = $defCurrencyValues; //put bc naresh wants it in message..
                createUserLog($UserType, "No", $Data[0]['iDriverId'], $DeviceType);
                setDataResponse($returnArr);
            }
            else {
                $returnArr['Action'] = "0";
                $returnArr['RESTRICT_APP'] = "Yes";
                if ($Data[0]['eStatus'] != "Deleted") {
                    $returnArr['message'] = "LBL_CONTACT_US_STATUS_NOTACTIVE_PASSENGER";
                    $returnArr['message_title'] = "LBL_ACC_INACTIVE_TITLE";
                    $returnArr['eStatus'] = $Data[0]['eStatus'];
                    $returnArr['isAccountInactive'] = "Yes";
                } else {
                    $returnArr['message'] = "LBL_ACC_DELETE_TXT";
                    $returnArr['message_title'] = "LBL_ACC_DELETE_TITLE";
                    $returnArr['eStatus'] = $Data[0]['eStatus'];
                    $returnArr['isAccountDeleted'] = "Yes";
                }
                setDataResponse($returnArr);
            }
        }
        else {
            $returnArr['Action'] = "0";
            $returnArr['message'] = "LBL_WRONG_DETAIL";
            setDataResponse($returnArr);
        }
    }
    else {
        if ($isBiometricLogin == "No") {
            $Data = $obj->MySQLSelect("SELECT iServiceId,iCompanyId,eStatus,vLang,vPassword FROM `company` WHERE ( vEmail='$Emid' OR vPhone = '$Emid' ) AND eSystem = 'DeliverAll' $whereCondition AND eStatus != 'Deleted'");
        }
        else {
            $Data = $obj->MySQLSelect("SELECT iServiceId,iCompanyId,eStatus,vLang,vPassword FROM `company` WHERE iCompanyId='$GeneralMemberId' AND eSystem = 'DeliverAll'");
        }
        if (count($Data) > 0) {
            // Check For Valid password #
            $hash = $Data[0]['vPassword'];
            $checkValidPass = $AUTH_OBJ->VerifyPassword($Password_user, $hash);
            if ($checkValidPass == 0 && $isBiometricLogin == "No") {
                $returnArr['Action'] = "0";
                $returnArr['message'] = "LBL_WRONG_DETAIL";
                setDataResponse($returnArr);
            }
            elseif (!empty($eSystemAppType) && strtoupper($eSystemAppType) == "KIOSK" && $Data[0]['iServiceId'] != "1") {
                $returnArr['Action'] = "0";
                $returnArr['message'] = "LBL_KIOSK_LOGIN_ERROR_MSG";
                setDataResponse($returnArr);
            }
            // Check For Valid password #
            if ($Data[0]['eStatus'] != "Deleted") {
                if ($GCMID != '') {
                    $iCompanyId = $Data[0]['iCompanyId'];
                    $where = " iCompanyId = '$iCompanyId' ";
                    if ($Data[0]['vLang'] == "" && $vLang == "") {
                        //Added By HJ On 15-07-2020 For Optimize language_master Table Query Start
                        $vLang = $LANG_OBJ->FetchDefaultLangData("vCode");
                        //Added By HJ On 15-07-2020 For Optimize language_master Table Query End
                        $Data_update_company['vLang'] = $vLang;
                    }
                    if ($vLang != "") {
                        $Data_update_company['vLang'] = $vLang;
                        $Data[0]['vLang'] = $vLang;
                    }
                    if ($vCurrency != "") {
                        $Data_update_company['vCurrencyCompany'] = $vCurrency;
                    }
                    $Data_update_company['vFirebaseDeviceToken'] = $vFirebaseDeviceToken;
                    $Data_update_company['eDeviceType'] = $DeviceType;
                    if (!empty($eSystemAppType) && strtoupper($eSystemAppType) == "KIOSK") {
                        $Data_update_company['iGcmRegIdKiosk'] = $GCMID;
                        $Data_update_company['tSessionIdKiosk'] = session_id() . time();
                    }
                    else {
                        $Data_update_company['iGcmRegId'] = $GCMID;
                        if ($isBiometricLogin == "No") {
                            $Data_update_company['tSessionId'] = session_id() . time();
                        }
                        else {
                            $Data_update_company['tSessionId'] = $tSessionId;
                        }
                    }
                    $id = $obj->MySQLQueryPerform("company", $Data_update_company, 'update', $where);
                }
                $returnArr['changeLangCode'] = "Yes";
                //Added By HJ On 23-07-2020 For langauge labele and Other Union Table Query Start
                if (isset($languageLabelDataArr['language_label_union_other_food_' . $Data[0]['vLang'] . "_" . $iServiceId])) {
                    $languageLabelsArr = $languageLabelDataArr['language_label_union_other_food_' . $Data[0]['vLang'] . "_" . $iServiceId];
                }
                else {
                    $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($Data[0]['vLang'], "1", $Data[0]['iServiceId']);
                    $languageLabelDataArr['language_label_union_other_food_' . $Data[0]['vLang'] . "_" . $iServiceId] = $languageLabelsArr;
                }
                //Added By HJ On 23-07-2020 For langauge labele and Other Union Table Query End
                $returnArr['UpdatedLanguageLabels'] = $languageLabelsArr; //added by SP on 2-7-2019 when signin get serviceid from table not from the request becoz a signin not updated
                $returnArr['vLanguageCode'] = $Data[0]['vLang'];
                //Added By HJ On 23-07-2020 For Optimization language_master Table Query Start
                if (isset($languageAssociateArr[$Data[0]['vLang']])) {
                    $Data_checkLangCode = array();
                    $Data_checkLangCode[] = $languageAssociateArr[$Data[0]['vLang']];
                }
                else {
                    $Data_checkLangCode = $obj->MySQLSelect("SELECT * FROM language_master WHERE `vCode` = '" . $Data[0]['vLang'] . "' ");
                }
                //Added By HJ On 23-07-2020 For Optimization language_master Table Query End
                $returnArr['langType'] = $Data_checkLangCode[0]['eDirectionCode'];
                $returnArr['vGMapLangCode'] = $Data_checkLangCode[0]['vGMapLangCode'];
                //Added By HJ On 23-07-2020 For Optimization language_master Table Query Start
                if (count($Data_ALL_langArr) > 0) {
                    $defLangValues = array();
                    for ($g = 0; $g < count($Data_ALL_langArr); $g++) {
                        if (strtoupper($Data_ALL_langArr[$g]['eStatus']) == "ACTIVE") {
                            $defLangValues[] = $Data_ALL_langArr[$g];
                        }
                    }
                }
                else {
                    $defLangValues = $obj->MySQLSelect("SELECT vCode, vGMapLangCode, eDirectionCode as eType, vTitle,vCurrencyCode,vCurrencySymbol,eDefault  FROM  `language_master` WHERE  `eStatus` = 'Active' ORDER BY iDispOrder ASC ");
                }
                //Added By HJ On 23-07-2020 For Optimization language_master Table Query End
                $returnArr['LIST_LANGUAGES'] = $defLangValues;
                for ($i = 0; $i < count($defLangValues); $i++) {
                    if ($defLangValues[$i]['eDefault'] == "Yes") {
                        $returnArr['DefaultLanguageValues'] = $defLangValues[$i];
                    }
                    $defLangValues[$i]['vService_BG_color'] = RANDOM_COLORS_ARR[array_rand(RANDOM_COLORS_ARR, 1)];
                    $defLangValues[$i]['vService_TEXT_color'] = "#FFFFFF";
                    $returnArr['LIST_LANGUAGES'][$i]['vService_BG_color'] = RANDOM_COLORS_ARR[array_rand(RANDOM_COLORS_ARR, 1)];
                    $returnArr['LIST_LANGUAGES'][$i]['vService_TEXT_color'] = "#FFFFFF";
                }
                //Added By HJ On 23-07-2020 For Optimize currency Table Query Start
                if (count($Data_ALL_currency_Arr) > 0) {
                    $defCurrencyValues = array();
                    for ($c = 0; $c < count($Data_ALL_currency_Arr); $c++) {
                        if (strtoupper($Data_ALL_currency_Arr[$c]['eStatus']) == "ACTIVE") {
                            $defCurrencyValues[] = $Data_ALL_currency_Arr[$c];
                        }
                    }
                }
                else {
                    $defCurrencyValues = $obj->MySQLSelect("SELECT iCurrencyId,vName, vSymbol,iDispOrder, eDefault,Ratio,fThresholdAmount,eStatus  FROM  `currency` WHERE  `eStatus` = 'Active' ORDER BY iDispOrder ASC ");
                }
                //Added By HJ On 23-07-2020 For Optimize currency Table Query End
                $returnArr['LIST_CURRENCY'] = $defCurrencyValues;
                for ($i = 0; $i < count($defCurrencyValues); $i++) {
                    if ($defCurrencyValues[$i]['eDefault'] == "Yes") {
                        $returnArr['DefaultCurrencyValues'] = $defCurrencyValues[$i];
                    }
                    $defCurrencyValues[$i]['vService_BG_color'] = RANDOM_COLORS_ARR[array_rand(RANDOM_COLORS_ARR, 1)];
                    $defCurrencyValues[$i]['vService_TEXT_color'] = "#FFFFFF";
                    $returnArr['LIST_CURRENCY'][$i]['vService_BG_color'] = RANDOM_COLORS_ARR[array_rand(RANDOM_COLORS_ARR, 1)];
                    $returnArr['LIST_CURRENCY'][$i]['vService_TEXT_color'] = "#FFFFFF";
                }
                $returnArr['Action'] = "1";
                $returnArr['message'] = getCompanyDetailInfo($Data[0]['iCompanyId'], 1);
                $returnArr['message'] = getCustomeNotificationSound($returnArr['message']);
                $returnArr['message']['LIST_CURRENCY'] = $defCurrencyValues; //put bc naresh wants it in message..
                $vCompanyLang = $vLang;
                if (isset($Data[0]['vLang']) && $Data[0]['vLang'] != "") {
                    $vCompanyLang = $Data[0]['vLang'];
                }
                $returnArr['message']['driverOptionArr'] = FetchStoreDriverOptions($vCompanyLang, $iServiceId);
                createUserLog($UserType, "No", $Data[0]['iCompanyId'], $DeviceType);
                setDataResponse($returnArr);
            }
            else {
                $returnArr['Action'] = "0";
                $returnArr['message'] = "LBL_ACC_DELETE_TXT";
                $returnArr['eStatus'] = $Data[0]['eStatus'];
                setDataResponse($returnArr);
            }
        }
        else {
            $returnArr['Action'] = "0";
            $returnArr['message'] = "LBL_WRONG_DETAIL";
            setDataResponse($returnArr);
        }
    }
}
// ##########################################################################
if ($type == "getDetail") {
    $iUserId = isset($_REQUEST["iUserId"]) ? $_REQUEST["iUserId"] : '';
    $GCMID = isset($_REQUEST["vDeviceToken"]) ? $_REQUEST["vDeviceToken"] : '';
    $deviceType = isset($_REQUEST["vDeviceType"]) ? $_REQUEST["vDeviceType"] : 'Android';
    $UserType = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : 'Passenger';
    $vLangCode = isset($_REQUEST["vLang"]) ? $_REQUEST["vLang"] : '';
    $eSystemAppType = isset($_REQUEST["eSystemAppType"]) ? $_REQUEST["eSystemAppType"] : '';
    $OLD_PROFILE_RESPONSE = isset($_REQUEST["OLD_PROFILE_RESPONSE"]) ? $_REQUEST["OLD_PROFILE_RESPONSE"] : '';

    if ($UserType == "Passenger") {
        //Added By HJ On 11-07-2020 For Optimization register_user Table Query Start
        $tblName = "register_user";
        if (isset($userDetailsArr[$tblName . "_" . $iUserId]) && count($userDetailsArr[$tblName . "_" . $iUserId]) > 0) {
            $Data = $userDetailsArr[$tblName . "_" . $iUserId];
        }
        else {
            $Data = $obj->MySQLSelect("SELECT *,iUserId as iMemberId FROM " . $tblName . " WHERE iUserId='" . $iUserId . "'");
            $userDetailsArr[$tblName . "_" . $iUserId] = $Data;
        }
        //Added By HJ On 11-07-2020 For Optimization register_user Table Query End
        //$sql = "SELECT iGcmRegId,vTripStatus,vLang,eChangeLang FROM `register_user` WHERE iUserId='$iUserId'";
        //$Data = $obj->MySQLSelect($sql);
        /* $iCabRequestId= get_value('cab_request_now' , 'max(iCabRequestId)', 'iUserId',$iUserId,'','true');
        $eStatus_cab= get_value('cab_request_now', 'eStatus', 'iCabRequestId',$iCabRequestId,'','true'); */
        $sql_cab = "SELECT iCabRequestId,eStatus FROM cab_request_now WHERE iUserId = '" . $iUserId . "' ORDER BY iCabRequestId DESC LIMIT 0,1";
        $Data_cab = $obj->MySQLSelect($sql_cab);
        $iCabRequestId = $Data_cab[0]['iCabRequestId'];
        $eStatus_cab = $Data_cab[0]['eStatus'];
        if (count($Data) > 0) {
            $iGCMregID = $Data[0]['iGcmRegId'];
            $vTripStatus = $Data[0]['vTripStatus'];
            if ($GCMID != "" && $GCMID != $iGCMregID) {
                $returnArr['Action'] = "0";
                $returnArr['RESTRICT_APP'] = "Yes";
                $returnArr['isSessionExpired'] = "Yes";
                $returnArr['message'] = "LBL_SESSION_TIME_OUT";
                $returnArr['message_title'] = "LBL_SESSION_TIME_OUT_TITLE";
                $returnArr['eStatus'] = "";
                setDataResponse($returnArr);
            }
            if ($Data[0]['vLang'] == "") {
                $where = " iUserId = '$iUserId' ";
                //Added By HJ On 11-07-2020 For Optimize language_master Table Query Start
                $vLang = $LANG_OBJ->FetchDefaultLangData("vCode");
                //Added By HJ On 11-07-2020 For Optimize language_master Table Query End
                $Data_update_passenger['vLang'] = $vLang;
                $updateid = $obj->MySQLQueryPerform("register_user", $Data_update_passenger, 'update', $where);
                $Data[0]['vLang'] = $vLang;
            }
            if ($eStatus_cab == "Requesting") {
                $where = " iCabRequestId = '$iCabRequestId' ";
                $Data_update_cab_now['eStatus'] = "Cancelled";
                $id = $obj->MySQLQueryPerform("cab_request_now", $Data_update_cab_now, 'update', $where);
            }
            if (($vLangCode != $Data[0]['vLang']) || $Data[0]['eChangeLang'] == "Yes") {
                $returnArr['changeLangCode'] = "Yes";
                $returnArr['UpdatedLanguageLabels'] = $LANG_OBJ->FetchLanguageLabels($Data[0]['vLang'], "1", $iServiceId);
                $returnArr['vLanguageCode'] = $Data[0]['vLang'];
                //Added By HJ On 11-07-2020 For Optimize language_master Table Query Start
                if (isset($languageAssociateArr[$Data[0]['vLang']])) {
                    $Data_checkLangCode = array();
                    $Data_checkLangCode[] = $languageAssociateArr[$Data[0]['vLang']];
                }
                else {
                    $Data_checkLangCode = $obj->MySQLSelect("SELECT eDirectionCode,vGMapLangCode FROM language_master WHERE `vCode` = '" . $Data[0]['vLang'] . "' ");
                }
                //Added By HJ On 11-07-2020 For Optimize language_master Table Query End
                $returnArr['langType'] = $Data_checkLangCode[0]['eDirectionCode'];
                $returnArr['vGMapLangCode'] = $Data_checkLangCode[0]['vGMapLangCode'];
                $where = " iUserId = '$iUserId' ";
                $Data_update_passenger_lang['eChangeLang'] = "No";
                $updateLangid = $obj->MySQLQueryPerform("register_user", $Data_update_passenger_lang, 'update', $where);
                $Data[0]['eChangeLang'] = "No";
                //Added By HJ On 11-07-2020 For Optimize language_master Table Query Start
                if (count($Data_ALL_langArr) > 0) {
                    $defLangValues = array();
                    for ($g = 0; $g < count($Data_ALL_langArr); $g++) {
                        if (strtoupper($Data_ALL_langArr[$g]['eStatus']) == "ACTIVE") {
                            $defLangValues[] = $Data_ALL_langArr[$g];
                        }
                    }
                }
                else {
                    $defLangValues = $obj->MySQLSelect("SELECT vCode, vGMapLangCode, eDirectionCode as eType, vTitle,vCurrencyCode,vCurrencySymbol,eDefault  FROM  `language_master` WHERE  `eStatus` = 'Active' ORDER BY iDispOrder ASC");
                }
                //Added By HJ On 11-07-2020 For Optimize language_master Table Query End
                //$sql = "SELECT vCode, vGMapLangCode, eDirectionCode as eType, vTitle,vCurrencyCode,vCurrencySymbol,eDefault  FROM  `language_master` WHERE  `eStatus` = 'Active' ORDER BY iDispOrder ASC ";
                //$defLangValues = $obj->MySQLSelect($sql);
                $returnArr['LIST_LANGUAGES'] = $defLangValues;
                for ($i = 0; $i < count($defLangValues); $i++) {
                    if ($defLangValues[$i]['eDefault'] == "Yes") {
                        $returnArr['DefaultLanguageValues'] = $defLangValues[$i];
                    }
                    $defLangValues[$i]['vService_BG_color'] = RANDOM_COLORS_ARR[$i];
                    $defLangValues[$i]['vService_TEXT_color'] = "#FFFFFF";
                    $returnArr['LIST_LANGUAGES'][$i]['vService_BG_color'] = RANDOM_COLORS_ARR[$i];
                    $returnArr['LIST_LANGUAGES'][$i]['vService_TEXT_color'] = "#FFFFFF";
                }
            }
            else {
                $returnArr['changeLangCode'] = "No";
            }
            //Added By HJ On 11-07-2020 For Optimize currency Table Query Start
            if (count($Data_ALL_currency_Arr) > 0) {
                $defCurrencyValues = array();
                for ($c = 0; $c < count($Data_ALL_currency_Arr); $c++) {
                    if (strtoupper($Data_ALL_currency_Arr[$c]['eStatus']) == "ACTIVE") {
                        unset($Data_ALL_currency_Arr[$c]['eReverseSymbolEnable']);
                        unset($Data_ALL_currency_Arr[$c]['eReverseformattingEnable']);
                        unset($Data_ALL_currency_Arr[$c]['eRoundingOffEnable']);
                        unset($Data_ALL_currency_Arr[$c]['fFirstRangeValue']);
                        unset($Data_ALL_currency_Arr[$c]['fMiddleRangeValue']);
                        unset($Data_ALL_currency_Arr[$c]['fSecRangeValue']);
                        $defCurrencyValues[] = $Data_ALL_currency_Arr[$c];
                    }
                }
            }
            else {
                $defCurrencyValues = $obj->MySQLSelect("SELECT iCurrencyId,vName, vSymbol,iDispOrder, eDefault,Ratio,fThresholdAmount,eStatus  FROM  `currency` WHERE  `eStatus` = 'Active' ORDER BY iDispOrder ASC ");
            }
            //Added By HJ On 11-07-2020 For Optimize currency Table Query End
            //$sql = "SELECT iCurrencyId,vName, vSymbol,iDispOrder, eDefault,Ratio,fThresholdAmount,eStatus  FROM  `currency` WHERE  `eStatus` = 'Active' ORDER BY iDispOrder ASC ";
            //$defCurrencyValues = $obj->MySQLSelect($sql);
            $returnArr['LIST_CURRENCY'] = $defCurrencyValues;
            for ($i = 0; $i < count($defCurrencyValues); $i++) {
                if ($defCurrencyValues[$i]['eDefault'] == "Yes") {
                    $returnArr['DefaultCurrencyValues'] = $defCurrencyValues[$i];
                }
                $defCurrencyValues[$i]['vService_BG_color'] = RANDOM_COLORS_ARR[$i];
                $defCurrencyValues[$i]['vService_TEXT_color'] = "#FFFFFF";
                $returnArr['LIST_CURRENCY'][$i]['vService_BG_color'] = RANDOM_COLORS_ARR[$i];
                $returnArr['LIST_CURRENCY'][$i]['vService_TEXT_color'] = "#FFFFFF";
            }
            $returnArr['Action'] = "1";
            $returnArr['message'] = getPassengerDetailInfo($iUserId, '', "");
            //Added By HJ On 09-11-2020 For Set App Type Wise Safety Feature As Per Discuss With KS Sir Start
            $returnArr['message']['ENABLE_SAFETY_FEATURE_RIDE'] = $ENABLE_SAFETY_FEATURE_RIDE;
            $returnArr['message']['ENABLE_SAFETY_FEATURE_DELIVERY'] = $ENABLE_SAFETY_FEATURE_DELIVERY;
            $returnArr['message']['ENABLE_SAFETY_FEATURE_UFX'] = $ENABLE_SAFETY_FEATURE_UFX;
            //Added By HJ On 09-11-2020 For Set App Type Wise Safety Feature As Per Discuss With KS Sir End
            $returnArr['message'] = getCustomeNotificationSound($returnArr['message']); // Added By HJ On 06-08-2019 For Get Custome Sound Notification File Name
            $returnArr['message']['LIST_CURRENCY'] = $defCurrencyValues;
            createUserLog($UserType, "Yes", $iUserId, $deviceType);
        }
        else {
            $returnArr['Action'] = "0";
            $returnArr['RESTRICT_APP'] = "Yes";
            $returnArr['isSessionExpired'] = "Yes";
            $returnArr['message'] = "LBL_SESSION_TIME_OUT";
            $returnArr['message_title'] = "LBL_SESSION_TIME_OUT_TITLE";
            $returnArr['eStatus'] = "";
            setDataResponse($returnArr);
        }

        if(!empty($OLD_PROFILE_RESPONSE)) {
            $OLD_PROFILE_RESPONSE = json_decode($OLD_PROFILE_RESPONSE, true);
            unset($OLD_PROFILE_RESPONSE['FETCH_TRIP_STATUS_TIME_INTERVAL']);    
            unset($OLD_PROFILE_RESPONSE['FETCH_TRIP_STATUS_TIME_INTERVAL_POOL']);    
            unset($OLD_PROFILE_RESPONSE['liveTrackingUrl']);    
            unset($OLD_PROFILE_RESPONSE['INVITE_SHARE_CONTENT']);    
            unset($OLD_PROFILE_RESPONSE['tDeviceData']);    
            unset($OLD_PROFILE_RESPONSE['eAppTerminate']);    

            unset($OLD_PROFILE_RESPONSE['PassengerDetails']['vLatitude']);    
            unset($OLD_PROFILE_RESPONSE['PassengerDetails']['vLongitude']);    
            unset($OLD_PROFILE_RESPONSE['PassengerDetails']['tLastOnline']);    
            unset($OLD_PROFILE_RESPONSE['PassengerDetails']['tDeviceData']);    
            unset($OLD_PROFILE_RESPONSE['PassengerDetails']['tLocationUpdateDate']);   
            unset($OLD_PROFILE_RESPONSE['PassengerDetails']['eAppTerminate']);   

            unset($OLD_PROFILE_RESPONSE['DriverDetails']['vLatitude']);    
            unset($OLD_PROFILE_RESPONSE['DriverDetails']['vLongitude']);    
            unset($OLD_PROFILE_RESPONSE['DriverDetails']['tLastOnline']);    
            unset($OLD_PROFILE_RESPONSE['DriverDetails']['tSwitchOnline']);    
            unset($OLD_PROFILE_RESPONSE['DriverDetails']['tOnline']);    
            unset($OLD_PROFILE_RESPONSE['DriverDetails']['vAvailability']);    
            unset($OLD_PROFILE_RESPONSE['DriverDetails']['tDeviceData']);    
            unset($OLD_PROFILE_RESPONSE['DriverDetails']['tLocationUpdateDate']);  
            unset($OLD_PROFILE_RESPONSE['DriverDetails']['eAppTerminate']);  

            unset($OLD_PROFILE_RESPONSE['TripDetails']['DriverDetails']['vLatitude']);    
            unset($OLD_PROFILE_RESPONSE['TripDetails']['DriverDetails']['vLongitude']);    
            unset($OLD_PROFILE_RESPONSE['TripDetails']['DriverDetails']['tLastOnline']);    
            unset($OLD_PROFILE_RESPONSE['TripDetails']['DriverDetails']['tOnline']);    
            unset($OLD_PROFILE_RESPONSE['TripDetails']['DriverDetails']['tSwitchOnline']);    
            unset($OLD_PROFILE_RESPONSE['TripDetails']['DriverDetails']['vAvailability']);
            unset($OLD_PROFILE_RESPONSE['TripDetails']['DriverDetails']['tDeviceData']);
            unset($OLD_PROFILE_RESPONSE['TripDetails']['DriverDetails']['tLocationUpdateDate']);
            unset($OLD_PROFILE_RESPONSE['TripDetails']['DriverDetails']['eAppTerminate']);

            unset($OLD_PROFILE_RESPONSE['TripDetails']['PassengerDetails']['vLatitude']);    
            unset($OLD_PROFILE_RESPONSE['TripDetails']['PassengerDetails']['vLongitude']); 
            unset($OLD_PROFILE_RESPONSE['TripDetails']['PassengerDetails']['tLastOnline']); 
            unset($OLD_PROFILE_RESPONSE['TripDetails']['PassengerDetails']['tDeviceData']); 
            unset($OLD_PROFILE_RESPONSE['TripDetails']['PassengerDetails']['tLocationUpdateDate']); 
            unset($OLD_PROFILE_RESPONSE['TripDetails']['PassengerDetails']['eAppTerminate']); 


            unset($OLD_PROFILE_RESPONSE['TripDetails']['eDriverPaymentStatus']);

            
            sortNestedArrayAssoc($OLD_PROFILE_RESPONSE);
            $OLD_PROFILE_RESPONSE = getDataResponse($OLD_PROFILE_RESPONSE);              

            $returnArrTmp = $returnArr;
            unset($returnArrTmp['message']['FETCH_TRIP_STATUS_TIME_INTERVAL']); 
            unset($returnArrTmp['message']['FETCH_TRIP_STATUS_TIME_INTERVAL_POOL']);

            unset($returnArrTmp['message']['liveTrackingUrl']);
            unset($returnArrTmp['message']['INVITE_SHARE_CONTENT']);
            unset($returnArrTmp['message']['tDeviceData']);
            unset($returnArrTmp['message']['eAppTerminate']);

            unset($returnArrTmp['message']['PassengerDetails']['vLatitude']);
            unset($returnArrTmp['message']['PassengerDetails']['vLongitude']);
            unset($returnArrTmp['message']['PassengerDetails']['tLastOnline']);
            unset($returnArrTmp['message']['PassengerDetails']['tDeviceData']);
            unset($returnArrTmp['message']['PassengerDetails']['tLocationUpdateDate']);
            unset($returnArrTmp['message']['PassengerDetails']['eAppTerminate']);

            unset($returnArrTmp['message']['DriverDetails']['vLatitude']);
            unset($returnArrTmp['message']['DriverDetails']['vLongitude']);
            unset($returnArrTmp['message']['DriverDetails']['tLastOnline']);
            unset($returnArrTmp['message']['DriverDetails']['tOnline']);
            unset($returnArrTmp['message']['DriverDetails']['tSwitchOnline']);
            unset($returnArrTmp['message']['DriverDetails']['vAvailability']);
            unset($returnArrTmp['message']['DriverDetails']['tDeviceData']);
            unset($returnArrTmp['message']['DriverDetails']['tLocationUpdateDate']);
            unset($returnArrTmp['message']['DriverDetails']['eAppTerminate']);

            unset($returnArrTmp['message']['TripDetails']['DriverDetails']['vLatitude']);
            unset($returnArrTmp['message']['TripDetails']['DriverDetails']['vLongitude']);
            unset($returnArrTmp['message']['TripDetails']['DriverDetails']['tLastOnline']);
            unset($returnArrTmp['message']['TripDetails']['DriverDetails']['tOnline']);
            unset($returnArrTmp['message']['TripDetails']['DriverDetails']['tSwitchOnline']);
            unset($returnArrTmp['message']['TripDetails']['DriverDetails']['vAvailability']);
            unset($returnArrTmp['message']['TripDetails']['DriverDetails']['tDeviceData']);
            unset($returnArrTmp['message']['TripDetails']['DriverDetails']['tLocationUpdateDate']);
            unset($returnArrTmp['message']['TripDetails']['DriverDetails']['eAppTerminate']);

            unset($returnArrTmp['message']['TripDetails']['PassengerDetails']['vLatitude']);
            unset($returnArrTmp['message']['TripDetails']['PassengerDetails']['vLongitude']);
            unset($returnArrTmp['message']['TripDetails']['PassengerDetails']['tLastOnline']);
            unset($returnArrTmp['message']['TripDetails']['PassengerDetails']['tDeviceData']);
            unset($returnArrTmp['message']['TripDetails']['PassengerDetails']['tLocationUpdateDate']);
            unset($returnArrTmp['message']['TripDetails']['PassengerDetails']['eAppTerminate']);

            unset($returnArrTmp['message']['TripDetails']['eDriverPaymentStatus']);

            sortNestedArrayAssoc($returnArrTmp['message']);
            $typeResponse = getDataResponse($returnArrTmp['message']);
            
            // echo $OLD_PROFILE_RESPONSE . '<br><br> ============= <br><br>' . $typeResponse; exit;
            if($OLD_PROFILE_RESPONSE == $typeResponse) {
                $returnArr['UPDATE_USER_DATA'] = "No";
            } else {
                $returnArr['UPDATE_USER_DATA'] = "Yes";
            }
        }
        setDataResponse($returnArr);
    }
    else if ($UserType == "Driver") {
        //$sql = "SELECT iGcmRegId,vLang,eChangeLang FROM `register_driver` WHERE iDriverId='$iUserId'";
        //$Data = $obj->MySQLSelect($sql);
        //Added By HJ On 11-07-2020 For Optimization register_driver Table Query Start
        $tblName = "register_driver";
        if (isset($userDetailsArr[$tblName . "_" . $iUserId]) && count($userDetailsArr[$tblName . "_" . $iUserId]) > 0) {
            $Data = $userDetailsArr[$tblName . "_" . $iUserId];
        }
        else {
            $Data = $obj->MySQLSelect("SELECT *,iDriverId as iMemberId FROM register_driver WHERE iDriverId='" . $iUserId . "'");
            $userDetailsArr[$tblName . "_" . $iUserId] = $Data;
        }
        //Added By HJ On 11-07-2020 For Optimization register_driver Table Query End
        if (count($Data) > 0) {
            $iGCMregID = $Data[0]['iGcmRegId'];
            if ($Data[0]['vLang'] == "") {
                $where = " iDriverId = '$iUserId' ";
                //Added By HJ On 17-06-2020 For Optimize language_master Table Query Start
                $vLang = $LANG_OBJ->FetchDefaultLangData("vCode");
                //Added By HJ On 17-06-2020 For Optimize language_master Table Query End
                //$vLang = get_value('language_master', 'vCode', 'eDefault', 'Yes', '', 'true');
                $Data_update_driver['vLang'] = $vLang;
                $updateid = $obj->MySQLQueryPerform("register_driver", $Data_update_driver, 'update', $where);
            }
            if ($GCMID != "" && $GCMID != $iGCMregID) {
                $returnArr['Action'] = "0";
                $returnArr['RESTRICT_APP'] = "Yes";
                $returnArr['isSessionExpired'] = "Yes";
                $returnArr['message'] = "LBL_SESSION_TIME_OUT";
                $returnArr['message_title'] = "LBL_SESSION_TIME_OUT_TITLE";
                $returnArr['eStatus'] = "";
                setDataResponse($returnArr);
            }
            if (($vLangCode != $Data[0]['vLang']) || $Data[0]['eChangeLang'] == "Yes") {
                $returnArr['changeLangCode'] = "Yes";
                $returnArr['UpdatedLanguageLabels'] = $LANG_OBJ->FetchLanguageLabels($Data[0]['vLang'], "1", $iServiceId);
                $returnArr['vLanguageCode'] = $Data[0]['vLang'];
                //$sql_LangCode = "SELECT eDirectionCode,vGMapLangCode FROM language_master WHERE `vCode` = '" . $Data[0]['vLang'] . "' ";
                //$Data_checkLangCode = $obj->MySQLSelect($sql_LangCode);
                //Added By HJ On 11-07-2020 For Optimize language_master Table Query Start
                if (isset($languageAssociateArr[$Data[0]['vLang']])) {
                    $Data_checkLangCode = array();
                    $Data_checkLangCode[] = $languageAssociateArr[$Data[0]['vLang']];
                }
                else {
                    $Data_checkLangCode = $obj->MySQLSelect("SELECT eDirectionCode,vGMapLangCode FROM language_master WHERE `vCode` = '" . $Data[0]['vLang'] . "' ");
                }
                //Added By HJ On 11-07-2020 For Optimize language_master Table Query End
                $returnArr['langType'] = $Data_checkLangCode[0]['eDirectionCode'];
                $returnArr['vGMapLangCode'] = $Data_checkLangCode[0]['vGMapLangCode'];
                $where = " iDriverId = '$iUserId' ";
                $Data_update_passenger_lang['eChangeLang'] = "No";
                $updateLangid = $obj->MySQLQueryPerform("register_driver", $Data_update_passenger_lang, 'update', $where);
                $Data[0]['eChangeLang'] = "No";
                //$sql = "SELECT vCode, vGMapLangCode, eDirectionCode as eType, vTitle,vCurrencyCode,vCurrencySymbol,eDefault  FROM  `language_master` WHERE  `eStatus` = 'Active' ORDER BY iDispOrder ASC ";
                //$defLangValues = $obj->MySQLSelect($sql);
                //Added By HJ On 11-07-2020 For Optimize language_master Table Query Start
                if (count($Data_ALL_langArr) > 0) {
                    $defLangValues = array();
                    for ($g = 0; $g < count($Data_ALL_langArr); $g++) {
                        if (strtoupper($Data_ALL_langArr[$g]['eStatus']) == "ACTIVE") {
                            $defLangValues[] = $Data_ALL_langArr[$g];
                        }
                    }
                }
                else {
                    $defLangValues = $obj->MySQLSelect("SELECT vCode, vGMapLangCode, eDirectionCode as eType, vTitle,vCurrencyCode,vCurrencySymbol,eDefault  FROM  `language_master` WHERE  `eStatus` = 'Active' ORDER BY iDispOrder ASC");
                }
                //Added By HJ On 11-07-2020 For Optimize language_master Table Query End
                $returnArr['LIST_LANGUAGES'] = $defLangValues;
                for ($i = 0; $i < count($defLangValues); $i++) {
                    if ($defLangValues[$i]['eDefault'] == "Yes") {
                        $returnArr['DefaultLanguageValues'] = $defLangValues[$i];
                    }
                    $defLangValues[$i]['vService_BG_color'] = RANDOM_COLORS_ARR[$i];
                    $defLangValues[$i]['vService_TEXT_color'] = "#FFFFFF";
                    $returnArr['LIST_LANGUAGES'][$i]['vService_BG_color'] = RANDOM_COLORS_ARR[$i];
                    $returnArr['LIST_LANGUAGES'][$i]['vService_TEXT_color'] = "#FFFFFF";
                }
            }
            else {
                $returnArr['changeLangCode'] = "No";
            }
            //Added By HJ On 11-07-2020 For Optimize currency Table Query Start
            if (count($Data_ALL_currency_Arr) > 0) {
                $defCurrencyValues = array();
                for ($c = 0; $c < count($Data_ALL_currency_Arr); $c++) {
                    if (strtoupper($Data_ALL_currency_Arr[$c]['eStatus']) == "ACTIVE") {
                        unset($Data_ALL_currency_Arr[$c]['eReverseSymbolEnable']);
                        unset($Data_ALL_currency_Arr[$c]['eReverseformattingEnable']);
                        unset($Data_ALL_currency_Arr[$c]['eRoundingOffEnable']);
                        unset($Data_ALL_currency_Arr[$c]['fFirstRangeValue']);
                        unset($Data_ALL_currency_Arr[$c]['fMiddleRangeValue']);
                        unset($Data_ALL_currency_Arr[$c]['fSecRangeValue']);
                        $defCurrencyValues[] = $Data_ALL_currency_Arr[$c];
                    }
                }
            }
            else {
                $defCurrencyValues = $obj->MySQLSelect("SELECT iCurrencyId,vName, vSymbol,iDispOrder, eDefault,Ratio,fThresholdAmount,eStatus  FROM  `currency` WHERE  `eStatus` = 'Active' ORDER BY iDispOrder ASC ");
            }
            //Added By HJ On 11-07-2020 For Optimize currency Table Query End
            //$sql = "SELECT iCurrencyId,vName, vSymbol,iDispOrder, eDefault,Ratio,fThresholdAmount,eStatus  FROM  `currency` WHERE  `eStatus` = 'Active' ORDER BY iDispOrder ASC ";
            //$defCurrencyValues = $obj->MySQLSelect($sql);
            $returnArr['LIST_CURRENCY'] = $defCurrencyValues;
            for ($i = 0; $i < count($defCurrencyValues); $i++) {
                if ($defCurrencyValues[$i]['eDefault'] == "Yes") {
                    $returnArr['DefaultCurrencyValues'] = $defCurrencyValues[$i];
                }
                $defCurrencyValues[$i]['vService_BG_color'] = RANDOM_COLORS_ARR[$i];
                $defCurrencyValues[$i]['vService_TEXT_color'] = "#FFFFFF";
                $returnArr['LIST_CURRENCY'][$i]['vService_BG_color'] = RANDOM_COLORS_ARR[$i];
                $returnArr['LIST_CURRENCY'][$i]['vService_TEXT_color'] = "#FFFFFF";
            }
            $returnArr['Action'] = "1";
            $returnArr['message'] = getDriverDetailInfo($iUserId);
            //Added By HJ On 09-11-2020 For Set App Type Wise Safety Feature As Per Discuss With KS Sir Start
            $returnArr['message']['ENABLE_SAFETY_FEATURE_RIDE'] = $ENABLE_SAFETY_FEATURE_RIDE;
            $returnArr['message']['ENABLE_SAFETY_FEATURE_DELIVERY'] = $ENABLE_SAFETY_FEATURE_DELIVERY;
            $returnArr['message']['ENABLE_SAFETY_FEATURE_UFX'] = $ENABLE_SAFETY_FEATURE_UFX;
            //Added By HJ On 09-11-2020 For Set App Type Wise Safety Feature As Per Discuss With KS Sir End
            $returnArr['message'] = getCustomeNotificationSound($returnArr['message']); // Added By HJ On 06-08-2019 For Get Custome Sound Notification File Name
            $returnArr['message']['LIST_CURRENCY'] = $defCurrencyValues;
            createUserLog($UserType, "Yes", $iUserId, $deviceType);
        }
        else {
            $returnArr['Action'] = "0";
            $returnArr['RESTRICT_APP'] = "Yes";
            $returnArr['isSessionExpired'] = "Yes";
            $returnArr['message'] = "LBL_SESSION_TIME_OUT";
            $returnArr['message_title'] = "LBL_SESSION_TIME_OUT_TITLE";
            $returnArr['eStatus'] = "";
            setDataResponse($returnArr);
        }

        if(!empty($OLD_PROFILE_RESPONSE)) {
            $OLD_PROFILE_RESPONSE = json_decode($OLD_PROFILE_RESPONSE, true);
            unset($OLD_PROFILE_RESPONSE['FETCH_TRIP_STATUS_TIME_INTERVAL']);    
            unset($OLD_PROFILE_RESPONSE['FETCH_TRIP_STATUS_TIME_INTERVAL_POOL']);    
            unset($OLD_PROFILE_RESPONSE['REWARD_SUBTITLE_DESC']);    
            unset($OLD_PROFILE_RESPONSE['liveTrackingUrl']);    
            unset($OLD_PROFILE_RESPONSE['LIST_CURRENCY']);
            unset($OLD_PROFILE_RESPONSE['USER_NOTIFICATION']);
            unset($OLD_PROFILE_RESPONSE['PROVIDER_NOTIFICATION']);
            unset($OLD_PROFILE_RESPONSE['DIAL_NOTIFICATION']);
            unset($OLD_PROFILE_RESPONSE['STORE_NOTIFICATION']);
            unset($OLD_PROFILE_RESPONSE['VOIP_NOTIFICATION']);  
            unset($OLD_PROFILE_RESPONSE['tOnline']);  
            unset($OLD_PROFILE_RESPONSE['tLastOnline']);  
            unset($OLD_PROFILE_RESPONSE['tSwitchOnline']);  
            unset($OLD_PROFILE_RESPONSE['vAvailability']);  
            unset($OLD_PROFILE_RESPONSE['eAppTerminate']);  

            unset($OLD_PROFILE_RESPONSE['PassengerDetails']['vLatitude']);    
            unset($OLD_PROFILE_RESPONSE['PassengerDetails']['vLongitude']);    
            unset($OLD_PROFILE_RESPONSE['PassengerDetails']['tLastOnline']);    
            unset($OLD_PROFILE_RESPONSE['PassengerDetails']['tLocationUpdateDate']); 
            unset($OLD_PROFILE_RESPONSE['PassengerDetails']['eAppTerminate']); 

            unset($OLD_PROFILE_RESPONSE['DriverDetails']['vLatitude']);    
            unset($OLD_PROFILE_RESPONSE['DriverDetails']['vLongitude']);    
            unset($OLD_PROFILE_RESPONSE['DriverDetails']['tOnline']);  
            unset($OLD_PROFILE_RESPONSE['DriverDetails']['tLastOnline']);  
            unset($OLD_PROFILE_RESPONSE['DriverDetails']['tSwitchOnline']);
            unset($OLD_PROFILE_RESPONSE['DriverDetails']['tLocationUpdateDate']);
            unset($OLD_PROFILE_RESPONSE['DriverDetails']['eAppTerminate']);

            unset($OLD_PROFILE_RESPONSE['TripDetails']['DriverDetails']['vLatitude']);    
            unset($OLD_PROFILE_RESPONSE['TripDetails']['DriverDetails']['vLongitude']);    
            unset($OLD_PROFILE_RESPONSE['TripDetails']['DriverDetails']['tOnline']);  
            unset($OLD_PROFILE_RESPONSE['TripDetails']['DriverDetails']['tLastOnline']);  
            unset($OLD_PROFILE_RESPONSE['TripDetails']['DriverDetails']['tSwitchOnline']);
            unset($OLD_PROFILE_RESPONSE['TripDetails']['DriverDetails']['tLocationUpdateDate']);
            unset($OLD_PROFILE_RESPONSE['TripDetails']['DriverDetails']['eAppTerminate']);

            unset($OLD_PROFILE_RESPONSE['TripDetails']['PassengerDetails']['vLatitude']);    
            unset($OLD_PROFILE_RESPONSE['TripDetails']['PassengerDetails']['vLongitude']);    
            unset($OLD_PROFILE_RESPONSE['TripDetails']['PassengerDetails']['tLastOnline']);    
            unset($OLD_PROFILE_RESPONSE['TripDetails']['PassengerDetails']['tLocationUpdateDate']);  
            unset($OLD_PROFILE_RESPONSE['TripDetails']['PassengerDetails']['eAppTerminate']); 
            
            sortNestedArrayAssoc($OLD_PROFILE_RESPONSE);
            $OLD_PROFILE_RESPONSE = getDataResponse($OLD_PROFILE_RESPONSE);

             

            $returnArrTmp = $returnArr;
            unset($returnArrTmp['message']['FETCH_TRIP_STATUS_TIME_INTERVAL']); 
            unset($returnArrTmp['message']['FETCH_TRIP_STATUS_TIME_INTERVAL_POOL']);  
            unset($returnArrTmp['message']['REWARD_SUBTITLE_DESC']); 
            unset($returnArrTmp['message']['liveTrackingUrl']);
            unset($returnArrTmp['message']['LIST_CURRENCY']);
            unset($returnArrTmp['message']['USER_NOTIFICATION']);
            unset($returnArrTmp['message']['PROVIDER_NOTIFICATION']);
            unset($returnArrTmp['message']['DIAL_NOTIFICATION']);
            unset($returnArrTmp['message']['STORE_NOTIFICATION']);
            unset($returnArrTmp['message']['VOIP_NOTIFICATION']);
            unset($returnArrTmp['message']['tOnline']);  
            unset($returnArrTmp['message']['tLastOnline']);  
            unset($returnArrTmp['message']['tSwitchOnline']);  
            unset($returnArrTmp['message']['vAvailability']);  
            unset($returnArrTmp['message']['eAppTerminate']);  

            unset($returnArrTmp['message']['PassengerDetails']['vLatitude']);
            unset($returnArrTmp['message']['PassengerDetails']['vLongitude']);
            unset($returnArrTmp['message']['PassengerDetails']['tLastOnline']);
            unset($returnArrTmp['message']['PassengerDetails']['tLocationUpdateDate']);
            unset($returnArrTmp['message']['PassengerDetails']['eAppTerminate']);

            unset($returnArrTmp['message']['DriverDetails']['vLatitude']);
            unset($returnArrTmp['message']['DriverDetails']['vLongitude']);
            unset($returnArrTmp['message']['DriverDetails']['tOnline']);  
            unset($returnArrTmp['message']['DriverDetails']['tLastOnline']);  
            unset($returnArrTmp['message']['DriverDetails']['tSwitchOnline']);  
            unset($returnArrTmp['message']['DriverDetails']['tLocationUpdateDate']);  
            unset($returnArrTmp['message']['DriverDetails']['eAppTerminate']);  

            unset($returnArrTmp['message']['TripDetails']['DriverDetails']['vLatitude']);
            unset($returnArrTmp['message']['TripDetails']['DriverDetails']['vLongitude']);
            unset($returnArrTmp['message']['TripDetails']['DriverDetails']['tOnline']);  
            unset($returnArrTmp['message']['TripDetails']['DriverDetails']['tLastOnline']);  
            unset($returnArrTmp['message']['TripDetails']['DriverDetails']['tSwitchOnline']);  
            unset($returnArrTmp['message']['TripDetails']['DriverDetails']['tLocationUpdateDate']);  
            unset($returnArrTmp['message']['TripDetails']['DriverDetails']['eAppTerminate']); 

            unset($returnArrTmp['message']['TripDetails']['PassengerDetails']['vLatitude']);
            unset($returnArrTmp['message']['TripDetails']['PassengerDetails']['vLongitude']);
            unset($returnArrTmp['message']['TripDetails']['PassengerDetails']['tLastOnline']);
            unset($returnArrTmp['message']['TripDetails']['PassengerDetails']['tLocationUpdateDate']);
            unset($returnArrTmp['message']['TripDetails']['PassengerDetails']['eAppTerminate']);

            sortNestedArrayAssoc($returnArrTmp['message']);
            $typeResponse = getDataResponse($returnArrTmp['message']);
            
            // echo $OLD_PROFILE_RESPONSE . '<br><br> ============= <br><br>' . $typeResponse; exit;
            if($OLD_PROFILE_RESPONSE == $typeResponse) {
                $returnArr['UPDATE_USER_DATA'] = "No";
            } else {
                $returnArr['UPDATE_USER_DATA'] = "Yes";
            }
        }
        setDataResponse($returnArr);
    }
    else {
        //Added By HJ On 09-07-2020 For Optimization register_user Table Query Start
        $tblName = "company";
        if (isset($userDetailsArr[$tblName . "_" . $iUserId]) && count($userDetailsArr[$tblName . "_" . $iUserId]) > 0) {
            $Data = $userDetailsArr[$tblName . "_" . $iUserId];
        }
        else {
            $Data = $obj->MySQLSelect("SELECT *,iCompanyId as iMemberId FROM " . $tblName . " WHERE iUserId='$iUserId'");
            $userDetailsArr[$tblName . "_" . $iUserId] = $Data;
        }
        //$sql = "SELECT iGcmRegId,vLang,eChangeLang FROM `company` WHERE iCompanyId='$iUserId'";
        //$Data = $obj->MySQLSelect($sql);
        //Added By HJ On 09-07-2020 For Optimization register_user Table Query End
        if (count($Data) > 0) {
            $iGCMregID = $Data[0]['iGcmRegId'];
            if (!empty($eSystemAppType) && strtoupper($eSystemAppType) == "KIOSK") {
                $iGCMregID = $Data[0]['iGcmRegIdKiosk'];
            }
            if ($Data[0]['vLang'] == "") {
                $where = " iCompanyId = '$iUserId' ";
                //Added By HJ On 09-07-2020 For Optimize language_master Table Query Start
                $vLang = $LANG_OBJ->FetchDefaultLangData("vCode");
                //Added By HJ On 09-07-2020 For Optimize language_master Table Query End
                //$vLang = get_value('language_master', 'vCode', 'eDefault', 'Yes', '', 'true');
                $Data_update_driver['vLang'] = $vLang;
                $updateid = $obj->MySQLQueryPerform("company", $Data_update_driver, 'update', $where);
            }
            if ($GCMID != "" && $GCMID != $iGCMregID) {
                $returnArr['Action'] = "0";
                $returnArr['RESTRICT_APP'] = "Yes";
                $returnArr['isSessionExpired'] = "Yes";
                $returnArr['message'] = "LBL_SESSION_TIME_OUT";
                $returnArr['message_title'] = "LBL_SESSION_TIME_OUT_TITLE";
                $returnArr['eStatus'] = "";
                setDataResponse($returnArr);
            }
            if (($vLangCode != $Data[0]['vLang']) || $Data[0]['eChangeLang'] == "Yes") {
                $returnArr['changeLangCode'] = "Yes";
                $returnArr['UpdatedLanguageLabels'] = $LANG_OBJ->FetchLanguageLabels($Data[0]['vLang'], "1", $iServiceId);
                $returnArr['vLanguageCode'] = $Data[0]['vLang'];
                //Added By HJ On 09-07-2020 For Optimize language_master Table Query Start
                if (isset($languageAssociateArr[$Data[0]['vLang']])) {
                    $Data_checkLangCode = array();
                    $Data_checkLangCode[] = $languageAssociateArr[$Data[0]['vLang']];
                }
                else {
                    $Data_checkLangCode = $obj->MySQLSelect("SELECT eDirectionCode,vGMapLangCode FROM language_master WHERE `vCode` = '" . $Data[0]['vLang'] . "'");
                }
                //Added By HJ On 09-07-2020 For Optimize language_master Table Query End
                $returnArr['langType'] = $Data_checkLangCode[0]['eDirectionCode'];
                $returnArr['vGMapLangCode'] = $Data_checkLangCode[0]['vGMapLangCode'];
                $where = " iCompanyId = '$iUserId' ";
                $Data_update_passenger_lang['eChangeLang'] = "No";
                $updateLangid = $obj->MySQLQueryPerform("company", $Data_update_passenger_lang, 'update', $where);
                $Data[0]['eChangeLang'] = "No";
                //Added By HJ On 09-07-2020 For Optimize language_master Table Query Start
                if (count($Data_ALL_langArr) > 0) {
                    $defLangValues = array();
                    for ($g = 0; $g < count($Data_ALL_langArr); $g++) {
                        if (strtoupper($Data_ALL_langArr[$g]['eStatus']) == "ACTIVE") {
                            $defLangValues[] = $Data_ALL_langArr[$g];
                        }
                    }
                }
                else {
                    $defLangValues = $obj->MySQLSelect("SELECT vCode, vGMapLangCode, eDirectionCode as eType, vTitle,vCurrencyCode,vCurrencySymbol,eDefault  FROM  `language_master` WHERE  `eStatus` = 'Active' ORDER BY iDispOrder ASC ");
                }
                //Added By HJ On 09-07-2020 For Optimize language_master Table Query End
                $returnArr['LIST_LANGUAGES'] = $defLangValues;
                for ($i = 0; $i < count($defLangValues); $i++) {
                    if ($defLangValues[$i]['eDefault'] == "Yes") {
                        $returnArr['DefaultLanguageValues'] = $defLangValues[$i];
                    }
                    $defLangValues[$i]['vService_BG_color'] = RANDOM_COLORS_ARR[$i];
                    $defLangValues[$i]['vService_TEXT_color'] = "#FFFFFF";
                    $returnArr['LIST_LANGUAGES'][$i]['vService_BG_color'] = RANDOM_COLORS_ARR[$i];
                    $returnArr['LIST_LANGUAGES'][$i]['vService_TEXT_color'] = "#FFFFFF";
                }
            }
            else {
                $returnArr['changeLangCode'] = "No";
            }
            //Added By HJ On 09-07-2020 For Optimize currency Table Query Start
            if (count($Data_ALL_currency_Arr) > 0) {
                $defCurrencyValues = array();
                for ($c = 0; $c < count($Data_ALL_currency_Arr); $c++) {
                    if (strtoupper($Data_ALL_currency_Arr[$c]['eStatus']) == "ACTIVE") {
                        unset($Data_ALL_currency_Arr[$c]['eReverseSymbolEnable']);
                        unset($Data_ALL_currency_Arr[$c]['eReverseformattingEnable']);
                        unset($Data_ALL_currency_Arr[$c]['eRoundingOffEnable']);
                        unset($Data_ALL_currency_Arr[$c]['fFirstRangeValue']);
                        unset($Data_ALL_currency_Arr[$c]['fMiddleRangeValue']);
                        unset($Data_ALL_currency_Arr[$c]['fSecRangeValue']);
                        $defCurrencyValues[] = $Data_ALL_currency_Arr[$c];
                    }
                }
            }
            else {
                $defCurrencyValues = $obj->MySQLSelect("SELECT iCurrencyId,vName, vSymbol,iDispOrder, eDefault,Ratio,fThresholdAmount,eStatus  FROM  `currency` WHERE  `eStatus` = 'Active' ORDER BY iDispOrder ASC");
            }
            //Added By HJ On 09-07-2020 For Optimize currency Table Query End
            $returnArr['LIST_CURRENCY'] = $defCurrencyValues;
            for ($i = 0; $i < count($defCurrencyValues); $i++) {
                if ($defCurrencyValues[$i]['eDefault'] == "Yes") {
                    $returnArr['DefaultCurrencyValues'] = $defCurrencyValues[$i];
                }
                $defCurrencyValues[$i]['vService_BG_color'] = RANDOM_COLORS_ARR[$i];
                $defCurrencyValues[$i]['vService_TEXT_color'] = "#FFFFFF";
                $returnArr['LIST_CURRENCY'][$i]['vService_BG_color'] = RANDOM_COLORS_ARR[$i];
                $returnArr['LIST_CURRENCY'][$i]['vService_TEXT_color'] = "#FFFFFF";
            }
            $returnArr['Action'] = "1";
            $returnArr['message'] = getCompanyDetailInfo($iUserId);
            //Added By HJ On 09-11-2020 For Set App Type Wise Safety Feature As Per Discuss With KS Sir Start
            $returnArr['message']['ENABLE_SAFETY_FEATURE_RIDE'] = $ENABLE_SAFETY_FEATURE_RIDE;
            $returnArr['message']['ENABLE_SAFETY_FEATURE_DELIVERY'] = $ENABLE_SAFETY_FEATURE_DELIVERY;
            $returnArr['message']['ENABLE_SAFETY_FEATURE_UFX'] = $ENABLE_SAFETY_FEATURE_UFX;
            //Added By HJ On 09-11-2020 For Set App Type Wise Safety Feature As Per Discuss With KS Sir End
            $returnArr['message'] = getCustomeNotificationSound($returnArr['message']); // Added By HJ On 06-08-2019 For Get Custome Sound Notification File Name
            $returnArr['message']['LIST_CURRENCY'] = $defCurrencyValues;
            $vCompanyLang = $vLangCode;
            if (isset($Data[0]['vLang']) && $Data[0]['vLang'] != "") {
                $vCompanyLang = $Data[0]['vLang'];
            }
            $returnArr['message']['driverOptionArr'] = FetchStoreDriverOptions($vCompanyLang, $iServiceId);
            createUserLog($UserType, "Yes", $iUserId, $deviceType);
        }
        else {
            $returnArr['Action'] = "0";
            $returnArr['RESTRICT_APP'] = "Yes";
            $returnArr['isSessionExpired'] = "Yes";
            $returnArr['message'] = "LBL_SESSION_TIME_OUT";
            $returnArr['message_title'] = "LBL_SESSION_TIME_OUT_TITLE";
            $returnArr['eStatus'] = "";
            setDataResponse($returnArr);
        }

        if(!empty($OLD_PROFILE_RESPONSE)) {
            $OLD_PROFILE_RESPONSE = json_decode($OLD_PROFILE_RESPONSE, true);
            unset($OLD_PROFILE_RESPONSE['FETCH_TRIP_STATUS_TIME_INTERVAL']);    
            unset($OLD_PROFILE_RESPONSE['FETCH_TRIP_STATUS_TIME_INTERVAL_POOL']);
            unset($OLD_PROFILE_RESPONSE['LIST_CURRENCY']);
            unset($OLD_PROFILE_RESPONSE['USER_NOTIFICATION']);
            unset($OLD_PROFILE_RESPONSE['PROVIDER_NOTIFICATION']);
            unset($OLD_PROFILE_RESPONSE['DIAL_NOTIFICATION']);
            unset($OLD_PROFILE_RESPONSE['STORE_NOTIFICATION']);
            unset($OLD_PROFILE_RESPONSE['VOIP_NOTIFICATION']);  
            unset($OLD_PROFILE_RESPONSE['APP_LAUNCH_IMAGES']);  
            unset($OLD_PROFILE_RESPONSE['driverOptionArr']);
            unset($OLD_PROFILE_RESPONSE['eAppTerminate']);
            
            sortNestedArrayAssoc($OLD_PROFILE_RESPONSE);
            $OLD_PROFILE_RESPONSE = getDataResponse($OLD_PROFILE_RESPONSE);

            unset($returnArr['message']['FETCH_TRIP_STATUS_TIME_INTERVAL']); 
            unset($returnArr['message']['FETCH_TRIP_STATUS_TIME_INTERVAL_POOL']);
            unset($returnArr['message']['APP_LAUNCH_IMAGES']);  

            $returnArrTmp = $returnArr;

            unset($returnArrTmp['message']['LIST_CURRENCY']);
            unset($returnArrTmp['message']['USER_NOTIFICATION']);
            unset($returnArrTmp['message']['PROVIDER_NOTIFICATION']);
            unset($returnArrTmp['message']['DIAL_NOTIFICATION']);
            unset($returnArrTmp['message']['STORE_NOTIFICATION']);
            unset($returnArrTmp['message']['VOIP_NOTIFICATION']);
            unset($returnArrTmp['message']['driverOptionArr']);
            unset($returnArrTmp['message']['eAppTerminate']);

            sortNestedArrayAssoc($returnArrTmp['message']);
            $typeResponse = getDataResponse($returnArrTmp['message']);
            
            // echo $OLD_PROFILE_RESPONSE . '<br><br> ============= <br><br>' . $typeResponse; exit;
            if($OLD_PROFILE_RESPONSE == $typeResponse) {
                $returnArr['UPDATE_USER_DATA'] = "No";
            } else {
                $returnArr['UPDATE_USER_DATA'] = "Yes";
            }
        }
        setDataResponse($returnArr);
    }
}

// ##########################################################################
if ($type == "getDriverStates") {
    $driverId = isset($_REQUEST['iDriverId']) ? clean($_REQUEST['iDriverId']) : '';
    $userType = isset($_REQUEST['UserType']) ? clean($_REQUEST['UserType']) : 'Driver';
    $docUpload = 'Yes';
    $driverVehicleUpload = 'Yes';
    $driverStateActive = 'Yes';
    $driverVehicleDocumentUpload = 'Yes';
    // $APP_TYPE = $CONFIG_OBJ->getConfigurations("configurations", "APP_TYPE");
    $vCountry = get_value('register_driver', 'vCountry', 'iDriverId', $driverId, '', true);
    $sql1 = "SELECT dm.doc_masterid masterid, dm.doc_usertype , dm.doc_name ,dm.ex_status,dm.status, COALESCE(dl.doc_id,  '' ) as doc_id,COALESCE(dl.doc_masterid, '') as masterid_list ,COALESCE(dl.ex_date, '') as ex_date,COALESCE(dl.doc_file, '') as doc_file, COALESCE(dl.status, '') as status FROM document_master dm left join (SELECT * FROM `document_list` where doc_userid='" . $driverId . "' ) dl on dl.doc_masterid=dm.doc_masterid where dm.doc_usertype='driver' and (dm.country='" . $vCountry . "' OR dm.country='All') and dm.status='Active' AND dm.eDocServiceType = 'General' ";
    $db_document = $obj->MySQLSelect($sql1);
    if (count($db_document) > 0) {
        for ($i = 0; $i < count($db_document); $i++) {
            if ($db_document[$i]['doc_file'] == "") {
                $docUpload = 'No';
            }
        }
    }
    else {
        $docUpload = 'No';
    }
    if ($APP_TYPE != 'UberX') {
        // # Count Driver Vehicle ##
        $sql = "SELECT count(iDriverVehicleId) as TotalVehicles from driver_vehicle WHERE iDriverId = '" . $driverId . "' AND eStatus != 'Deleted'";
        $db_Total_vehicle = $obj->MySQLSelect($sql);
        $TotalVehicles = $db_Total_vehicle[0]['TotalVehicles'];
        $returnArr['TotalVehicles'] = strval($TotalVehicles);
        // # Count Driver Vehicle ##
        $sql = "SELECT iDriverVehicleId from driver_vehicle WHERE iDriverId = '" . $driverId . "' AND eStatus != 'Deleted'";
        $db_drv_vehicle = $obj->MySQLSelect($sql);
        if (count($db_drv_vehicle) == 0) {
            $driverVehicleUpload = 'No';
        }
        else if ($driverVehicleUpload != 'No') {
            $test = array();
            // Check For Driver's selected vehicle's document are upload or not #
            $sql = "SELECT dl.*,dv.iDriverVehicleId FROM `driver_vehicle` AS dv LEFT JOIN document_list as dl ON dl.doc_userid=dv.iDriverVehicleId WHERE dv.iDriverId='$driverId' AND dl.doc_usertype = 'car' AND dv.eStatus != 'Deleted' ";
            $db_selected_vehicle = $obj->MySQLSelect($sql);
            if (count($db_selected_vehicle) > 0) {
                for ($i = 0; $i < count($db_selected_vehicle); $i++) {
                    if ($db_selected_vehicle[$i]['doc_file'] == "") {
                        $test[] = '1';
                    }
                }
            }
            if (count($test) == count($db_selected_vehicle)) {
                $driverVehicleUpload = 'No';
            }
        }
    }
    else {
        $sql = "SELECT vCarType from driver_vehicle WHERE iDriverId = '" . $driverId . "'";
        $db_drv_vehicle = $obj->MySQLSelect($sql);
        if ($db_drv_vehicle[0]['vCarType'] == "") {
            $driverVehicleUpload = 'No';
        }
        else {
            $driverVehicleUpload = 'Yes';
        }
    }
    $sql = "SELECT rd.eStatus as driverstatus,cmp.eStatus as cmpEStatus FROM `register_driver` as rd,`company` as cmp WHERE rd.iDriverId='" . $driverId . "' AND cmp.iCompanyId=rd.iCompanyId";
    $Data = $obj->MySQLSelect($sql);
    if (strtolower($Data[0]['driverstatus']) != "active" || strtolower($Data[0]['cmpEStatus']) != "active") {
        $driverStateActive = 'No';
    }
    if ($APP_TYPE == "UberX") {
        $sql = "select * from `driver_manage_timing` where iDriverId = '" . $driverId . "'";
        $db_driver_timing = $obj->MySQLSelect($sql);
        if (count($db_driver_timing) > 0) {
            $returnArr['IS_DRIVER_MANAGE_TIME_AVAILABLE'] = "Yes";
        }
        else {
            $returnArr['IS_DRIVER_MANAGE_TIME_AVAILABLE'] = "No";
        }
    }
    if ($driverStateActive == "Yes") {
        $docUpload = "Yes";
        $driverVehicleUpload = "Yes";
        $driverVehicleDocumentUpload = "Yes";
    }
    $returnArr['Action'] = "1";
    $returnArr['IS_DOCUMENT_PROCESS_COMPLETED'] = $docUpload;
    $returnArr['IS_VEHICLE_PROCESS_COMPLETED'] = $driverVehicleUpload;
    $returnArr['IS_VEHICLE_DOCUMENT_PROCESS_COMPLETED'] = $driverVehicleDocumentUpload;
    $returnArr['IS_DRIVER_STATE_ACTIVATED'] = $driverStateActive;
    setDataResponse($returnArr);
}
// ##########################################################################
if ($type == "CheckPromoCode") {
    $iCompanyId = isset($_REQUEST['iCompanyId']) ? $_REQUEST['iCompanyId'] : '';
    $iUserAddressId = isset($_REQUEST['iUserAddressId']) ? $_REQUEST['iUserAddressId'] : '';
    $eTakeAway = isset($_REQUEST['eTakeAway']) ? $_REQUEST['eTakeAway'] : 'No';
    $validPromoCodesArr = getValidPromoCodes();
    if (!empty($validPromoCodesArr) && !empty($validPromoCodesArr['CouponList']) && count($validPromoCodesArr['CouponList']) > 0) {
        $returnArr['Action'] = "1"; // code is valid
        $returnArr["message"] = "LBL_SUCCESS_COUPON_CODE";
        $userAddressData = $obj->MySQLSelect("SELECT * FROM user_address WHERE iUserAddressId = '$iUserAddressId'");
        $userAddressLatitude = $userAddressData[0]['vLatitude'];
        $userAddressLongitude = $userAddressData[0]['vLongitude'];
        $db_companydata = $obj->MySQLSelect("SELECT vRestuarantLocationLat, vRestuarantLocationLong FROM company WHERE iCompanyId = '$iCompanyId'");
        $User_Address_Array = array($userAddressLatitude, $userAddressLongitude);
        $Rest_Address_Array = array($db_companydata[0]['vRestuarantLocationLat'], $db_companydata[0]['vRestuarantLocationLong']);
        $iLocationIdUser = GetUserGeoLocationIdPromoCode($User_Address_Array);
        $iLocationIdRest = GetUserGeoLocationIdPromoCode($Rest_Address_Array);
        if ($MODULES_OBJ->isEnableFreeDeliveryOrStoreSpecificPromoCode() && !empty($validPromoCodesArr['CouponList'][0]['eStoreType'])) {
            if ($eTakeAway == "Yes") {
                if ($validPromoCodesArr['CouponList'][0]['eStoreType'] == "All") {
                    if ($validPromoCodesArr['CouponList'][0]['eFreeDelivery'] == "Yes") {
                        $returnArr['Action'] = "0"; // code is invalid
                        $returnArr["message"] = "LBL_INVALID_COUPON_CODE";
                    }
                }
                else {
                    if ($validPromoCodesArr['CouponList'][0]['iCompanyId'] == $iCompanyId) {
                        if ($promoCodeData['eFreeDelivery'] == "Yes") {
                            $returnArr['Action'] = "0"; // code is invalid
                            $returnArr["message"] = "LBL_INVALID_COUPON_CODE";
                        }
                    }
                    else {
                        $returnArr['Action'] = "0"; // code is invalid
                        $returnArr["message"] = "LBL_INVALID_COUPON_CODE";
                    }
                }
            }
            else {
                if ($validPromoCodesArr['CouponList'][0]['eStoreType'] == "StoreSpecific" && $validPromoCodesArr['CouponList'][0]['iCompanyId'] != $iCompanyId) {
                    $returnArr['Action'] = "0"; // code is invalid
                    $returnArr["message"] = "LBL_INVALID_COUPON_CODE";
                }
            }
            if ($MODULES_OBJ->isEnableLocationWisePromoCode() && $validPromoCodesArr['CouponList'][0]['iLocationId'] > 0) {
                if ($eTakeAway == "No") {
                    if (!in_array($validPromoCodesArr['CouponList'][0]['iLocationId'], $iLocationIdUser) || !in_array($validPromoCodesArr['CouponList'][0]['iLocationId'], $iLocationIdRest)) {
                        $returnArr['Action'] = "0"; // code is invalid
                        $returnArr["message"] = "LBL_INVALID_PROMOCODE";
                    }
                }
                else {
                    if (!in_array($validPromoCodesArr['CouponList'][0]['iLocationId'], $iLocationIdRest)) {
                        $returnArr['Action'] = "0"; // code is invalid
                        $returnArr["message"] = "LBL_INVALID_PROMOCODE";
                    }
                }
            }
        }
        if ($MODULES_OBJ->isEnableLocationWisePromoCode() && $validPromoCodesArr['CouponList'][0]['iLocationId'] > 0) {
            if ($eTakeAway == "No") {
                if (!in_array($validPromoCodesArr['CouponList'][0]['iLocationId'], $iLocationIdUser) || !in_array($validPromoCodesArr['CouponList'][0]['iLocationId'], $iLocationIdRest)) {
                    $returnArr['Action'] = "0"; // code is invalid
                    $returnArr["message"] = "LBL_INVALID_PROMOCODE";
                }
            }
            else {
                if (!in_array($validPromoCodesArr['CouponList'][0]['iLocationId'], $iLocationIdRest)) {
                    $returnArr['Action'] = "0"; // code is invalid
                    $returnArr["message"] = "LBL_INVALID_PROMOCODE";
                }
            }
        }
        setDataResponse($returnArr);
    }
    else {
        $returnArr['Action'] = "0"; // code is invalid
        $returnArr["message"] = "LBL_INVALID_COUPON_CODE";
        setDataResponse($returnArr);
    }
}
// ##########################################################################
if ($type == "updateUserProfileDetail") {
    $vName = isset($_REQUEST["vName"]) ? $_REQUEST["vName"] : '';
    $vLastName = isset($_REQUEST["vLastName"]) ? stripslashes($_REQUEST["vLastName"]) : '';
    $vPhone = isset($_REQUEST["vPhone"]) ? $_REQUEST["vPhone"] : '';
    $iMemberId = isset($_REQUEST["iMemberId"]) ? $_REQUEST['iMemberId'] : '';
    $phoneCode = isset($_REQUEST["vPhoneCode"]) ? $_REQUEST['vPhoneCode'] : '';
    $vCountry = isset($_REQUEST["vCountry"]) ? $_REQUEST['vCountry'] : '';
    $currencyCode = isset($_REQUEST["CurrencyCode"]) ? $_REQUEST['CurrencyCode'] : '';
    $languageCode = isset($_REQUEST["LanguageCode"]) ? $_REQUEST['LanguageCode'] : '';
    $userType = isset($_REQUEST["UserType"]) ? $_REQUEST['UserType'] : 'Passenger'; // Passenger, Driver, Company
    $vEmail = isset($_REQUEST["vEmail"]) ? $_REQUEST['vEmail'] : '';
    $tProfileDescription = isset($_REQUEST["tProfileDescription"]) ? $_REQUEST['tProfileDescription'] : '';
    $vInviteCode = isset($_REQUEST["vInviteCode"]) ? $_REQUEST['vInviteCode'] : '';
    if ($userType == "" || $userType == NULL) {
        $userType = "Passenger";
    }
    //Added By HJ On 13-11-2019 For Check Provider Profile Edit Permission Start
    $driverData = $Data_update_User = array();
    if ($userType == "Driver") {
        $driverData = $obj->MySQLSelect("SELECT vLang,vCode,vPhone,vEmail,vInviteCode,eStatus,vName,vLastName FROM register_driver WHERE iDriverId = '" . $iMemberId . "'");
        $message = "LBL_EDIT_PROFILE_DISABLED";
        if (count($driverData) > 0) {
            $driverFname = $driverData[0]['vName'];
            $driverLname = $driverData[0]['vLastName'];
            $checkEditProfileStatus = getEditDriverProfileStatus($driverData[0]['eStatus']); // Added By HJ On 13-11-2019 For Check Driver Profile Edit Status As Per Discuss With KS Sir
            if (($driverFname != $vName || $driverLname != $vLastName) && $checkEditProfileStatus == "No") {
                $message = "LBL_PROFILE_EDIT_BLOCK_TXT";
                $checkEditProfileStatus = "No";
            }
            if ($checkEditProfileStatus == "No") {
                $returnArr['Action'] = "0";
                $returnArr['message'] = $message;
                setDataResponse($returnArr);
            }
        }
        else if ($ENABLE_EDIT_DRIVER_PROFILE == "No") {
            $returnArr['Action'] = "0";
            $returnArr['message'] = $message;
            setDataResponse($returnArr);
        }
    }
    if ($vInviteCode != "") {
        $check_inviteCode = $REFERRAL_OBJ->ValidateReferralCode($vInviteCode);
        if ($check_inviteCode == "" || $check_inviteCode == "0" || $check_inviteCode == 0) {
            $returnArr['Action'] = "0";
            $returnArr['message'] = "LBL_INVITE_CODE_INVALID";
            setDataResponse($returnArr);
        }
        else {
            $inviteRes = explode("|", $check_inviteCode);
            $iRefUserId = $inviteRes[0];
            $eRefType = $inviteRes[1];
        }
    }
    /* Multi Level Referral */
    if ($vInviteCode != "") {
        $refData = $obj->MySQLSelect("SELECT * FROM user_referrer_transaction WHERE iMemberId = " . $inviteRes[0] . " AND eUserType = '" . $inviteRes[1] . "'");
        if ($refData[0]['tReferrerInfo'] == "") {
            $referrerInfo[] = array('Position of Referrer' => 1, 'iMemberId' => $inviteRes[0], 'eUserType' => $inviteRes[1]);
        }
        else {
            $referrerInfo = json_decode($refData[0]['tReferrerInfo'], true);
            $referrerInfo[] = array('Position of Referrer' => (count($referrerInfo) + 1), 'iMemberId' => $inviteRes[0], 'eUserType' => $inviteRes[1]);
        }
        $Data_Referrer['tReferrerInfo'] = json_encode($referrerInfo);
        $Data_Referrer['iMemberId'] = $iMemberId;
        $Data_Referrer['eUserType'] = ($userType == "Passenger") ? 'Rider' : 'Driver';
        $obj->MySQLQueryPerform("user_referrer_transaction", $Data_Referrer, 'insert');
    }
    /* Multi Level Referral End */
    //Added By HJ On 10-08-2019 For Check Provider Profile Edit Permission Start
    /* if ($ENABLE_EDIT_DRIVER_PROFILE == "No" && $userType == "Driver") {
      $returnArr['Action'] = "0";
      $returnArr['message'] = "LBL_EDIT_PROFILE_DISABLED";
      setDataResponse($returnArr);
    } */
    //Added By HJ On 10-08-2019 For Check Provider Profile Edit Permission End
    $csql = "SELECT eZeroAllowed,vCountryCode FROM `country` WHERE vPhoneCode = '" . $phoneCode . "'";
    $CountryData = $obj->MySQLSelect($csql);
    $eZeroAllowed = $CountryData[0]['eZeroAllowed'];
    if ($eZeroAllowed == 'Yes') {
        $vPhone = $vPhone;
    }
    else {
        $first = substr($vPhone, 0, 1);
        if ($first == "0") {
            $vPhone = substr($vPhone, 1);
        }
    }
    $eSystem = "";
    if ($vPhone != "") {
        if (strtolower($userType) == "company") {
            $companyData = $obj->MySQLSelect("SELECT vLang,vCode,vPhone,vEmail,eSystem FROM company WHERE iCompanyId = '" . $iMemberId . "'");
            if (count($companyData) > 0) {
                $eSystem = $companyData[0]['eSystem'];
            }
        }
        $checPhoneExist = checkMemberDataInfo($vPhone, "", $userType, $vCountry, $iMemberId, $eSystem); //Added By HJ On 09-09-2019 For Chekc User Country and Mobile Number When Register
    }
    if (isset($checPhoneExist['status']) && $checPhoneExist['status'] == 0) {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_MOBILE_EXIST";
        setDataResponse($returnArr);
    }
    else if (isset($checPhoneExist['status']) && $checPhoneExist['status'] == 2) {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_INVALID_MEMBER_USER_COUNTRY_EMAIL_TXT";
        setDataResponse($returnArr);
    }
    if ($userType == "Passenger") {
        $vEmail_userId_check = get_value('register_user', 'iUserId', 'vEmail', $vEmail, '', 'true');
        $vPhone_userId_check = get_value('register_user', 'iUserId', 'vPhone', $vPhone, '', 'true');
        $where = " iUserId = '$iMemberId'";
        $tableName = "register_user";
        $Data_update_User['vPhoneCode'] = $phoneCode;
        $Data_update_User['vCurrencyPassenger'] = $currencyCode;
        $currentLanguageCode = get_value('register_user', 'vLang', 'iUserId', $iMemberId, '', 'true');
        $sqlp = "SELECT vPhoneCode,vPhone,vEmail FROM register_user WHERE iUserId = '" . $iMemberId . "'";
        $passengerData = $obj->MySQLSelect($sqlp);
        $vPhoneCode_orig = $passengerData[0]['vPhoneCode'];
        $vPhone_orig = $passengerData[0]['vPhone'];
        $vEmail_orig = $passengerData[0]['vEmail'];
    }
    else if ($userType == "Driver") {
        $vEmail_userId_check = get_value('register_driver', 'iDriverId', 'vEmail', $vEmail, '', 'true');
        //$vPhone_userId_check = get_value('register_driver', 'iDriverId', 'vPhone', $vPhone, '', 'true');
        $where = " iDriverId = '$iMemberId'";
        $tableName = "register_driver";
        $Data_update_User['vCode'] = $phoneCode;
        $Data_update_User['vCurrencyDriver'] = $currencyCode;
        $Data_update_User['tProfileDescription'] = $tProfileDescription;
        if (empty($driverData) || count($driverData) == 0) {
            $sqlp = "SELECT vLang,vCode,vPhone,vEmail,vInviteCode FROM register_driver WHERE iDriverId = '" . $iMemberId . "'";
            $driverData = $obj->MySQLSelect($sqlp);
        }
        // $sqlp = "SELECT vLang,vCode,vPhone,vEmail FROM register_driver WHERE iDriverId = '" . $iMemberId . "'";
        // $companyData = $obj->MySQLSelect($sqlp);
        $currentLanguageCode = $driverData[0]['vLang'];
        $vPhoneCode_orig = $driverData[0]['vCode'];
        $vPhone_orig = $driverData[0]['vPhone'];
        $vEmail_orig = $driverData[0]['vEmail'];
    }
    else {
        if (count($companyData) == 0) {
            $companyData = $obj->MySQLSelect("SELECT vLang,vCode,vPhone,vEmail FROM company WHERE iCompanyId = '" . $iMemberId . "'");
        }
        $checkEmial = $obj->MySQLSelect("SELECT iCompanyId FROM company WHERE vEmail = '" . $vEmail . "' AND eSystem='" . $eSystem . "'");
        if (count($checkEmial) > 0) {
            $vEmail_userId_check = $checkEmial[0]['iCompanyId'];
        }
        //$vEmail_userId_check = get_value('company', 'iCompanyId', 'vEmail', $vEmail, '', 'true');
        //$vPhone_userId_check = get_value('company', 'iCompanyId', 'vPhone', $vPhone, '', 'true');
        $where = " iCompanyId = '$iMemberId'";
        $tableName = "company";
        $Data_update_User['vCode'] = $phoneCode;
        $Data_update_User['vCurrencyCompany'] = $currencyCode;
        $currentLanguageCode = $companyData[0]['vLang'];
        $vPhoneCode_orig = $companyData[0]['vCode'];
        $vPhone_orig = $companyData[0]['vPhone'];
        $vEmail_orig = $companyData[0]['vEmail'];
    }
    // $currentLanguageCode = ($obj->MySQLSelect("SELECT vLang FROM ".$tableName." WHERE".$where)[0]['vLang']);
    /*email optional*/
    if ($ENABLE_EMAIL_OPTIONAL == "Yes") {
        if (!empty($vEmail) && $vEmail_userId_check != "" && $vEmail_userId_check != $iMemberId) {
            $returnArr['Action'] = "0";
            $returnArr['message'] = "LBL_ALREADY_REGISTERED_TXT";
            setDataResponse($returnArr);
        }
    }
    else {
        if ($vEmail_userId_check != "" && $vEmail_userId_check != $iMemberId) {
            $returnArr['Action'] = "0";
            $returnArr['message'] = "LBL_ALREADY_REGISTERED_TXT";
            setDataResponse($returnArr);
        }
    }
    /* if ($vPhone_userId_check != "" && $vPhone_userId_check != $iMemberId) {
      $returnArr['Action'] = "0";
      $returnArr['message'] = "LBL_MOBILE_EXIST";
      setDataResponse($returnArr);
    } */
    if ($vPhone_orig != $vPhone || $vPhoneCode_orig != $phoneCode) {
        $Data_update_User['ePhoneVerified'] = "No";
    }
    if ($vEmail_orig != $vEmail) {
        $Data_update_User['eEmailVerified'] = "No";
    }
    if ($vEmail != "") {
        $Data_update_User['vEmail'] = $vEmail;
    }
    else {
        if ($ENABLE_PHONE_LOGIN_VIA_COUNTRY_SELECTION_METHOD == 'Yes') { // else condition added by NM ENABLE_PHONE_LOGIN_VIA_COUNTRY_SELECTION_METHOD passing blank email that is not updated with existing email.
            $Data_update_User['vEmail'] = $vEmail;
        }
    }
    if ($userType == "Company") {
        $Data_update_User['vCompany'] = $vName;
        $Data_update_User['vPhone'] = $vPhone;
        $Data_update_User['vCountry'] = $vCountry;
        $Data_update_User['vLang'] = $languageCode;
        if ($vPhone_orig != $vPhone || $vPhoneCode_orig != $phoneCode || $vEmail_orig != $vEmail) {
            $Data_update_User['eAvailable'] = "No";
        }
    }
    else {
        $Data_update_User['vName'] = $vName;
        $Data_update_User['vLastName'] = $vLastName;
        $Data_update_User['vPhone'] = $vPhone;
        $Data_update_User['vCountry'] = $vCountry;
        $Data_update_User['vLang'] = $languageCode;
    }
    $id = $obj->MySQLQueryPerform($tableName, $Data_update_User, 'update', $where);
    if ($currentLanguageCode != $languageCode) {
        $returnArr['changeLangCode'] = "Yes";
        $returnArr['UpdatedLanguageLabels'] = $LANG_OBJ->FetchLanguageLabels($languageCode, "1", $iServiceId);
        $returnArr['vLanguageCode'] = $languageCode;
        /* $returnArr['langType'] = get_value('language_master', 'eDirectionCode', 'vCode',$languageCode,'','true');
        $returnArr['vGMapLangCode'] = get_value('language_master', 'vGMapLangCode', 'vCode',$languageCode,'','true'); */
        $sql_LangCode = "SELECT eDirectionCode,vGMapLangCode FROM language_master WHERE `vCode` = '" . $languageCode . "' ";
        $Data_checkLangCode = $obj->MySQLSelect($sql_LangCode);
        $returnArr['langType'] = $Data_checkLangCode[0]['eDirectionCode'];
        $returnArr['vGMapLangCode'] = $Data_checkLangCode[0]['vGMapLangCode'];
        $sql = "SELECT vCode, vGMapLangCode, eDirectionCode as eType, vTitle,vCurrencyCode,vCurrencySymbol,eDefault  FROM  `language_master` WHERE  `eStatus` = 'Active' ORDER BY iDispOrder ASC ";
        $defLangValues = $obj->MySQLSelect($sql);
        $returnArr['LIST_LANGUAGES'] = $defLangValues;
        for ($i = 0; $i < count($defLangValues); $i++) {
            if ($defLangValues[$i]['eDefault'] == "Yes") {
                $returnArr['DefaultLanguageValues'] = $defLangValues[$i];
            }
            $defLangValues[$i]['vService_BG_color'] = RANDOM_COLORS_ARR[$i];
            $defLangValues[$i]['vService_TEXT_color'] = "#FFFFFF";
            $returnArr['LIST_LANGUAGES'][$i]['vService_BG_color'] = RANDOM_COLORS_ARR[$i];
            $returnArr['LIST_LANGUAGES'][$i]['vService_TEXT_color'] = "#FFFFFF";
        }
    }
    else {
        $returnArr['changeLangCode'] = "No";
    }
    $sql = "SELECT iCurrencyId,vName, vSymbol,iDispOrder, eDefault,Ratio,fThresholdAmount,eStatus  FROM  `currency` WHERE  `eStatus` = 'Active' ORDER BY iDispOrder ASC ";
    $defCurrencyValues = $obj->MySQLSelect($sql);
    $returnArr['LIST_CURRENCY'] = $defCurrencyValues;
    for ($i = 0; $i < count($defCurrencyValues); $i++) {
        if ($defCurrencyValues[$i]['eDefault'] == "Yes") {
            $returnArr['DefaultCurrencyValues'] = $defCurrencyValues[$i];
        }
        $defCurrencyValues[$i]['vService_BG_color'] = RANDOM_COLORS_ARR[$i];
        $defCurrencyValues[$i]['vService_TEXT_color'] = "#FFFFFF";
        $returnArr['LIST_CURRENCY'][$i]['vService_BG_color'] = RANDOM_COLORS_ARR[$i];
        $returnArr['LIST_CURRENCY'][$i]['vService_TEXT_color'] = "#FFFFFF";
    }
    if ($userType == "Passenger") {
        $returnArr['message'] = getPassengerDetailInfo($iMemberId, "", "");
    }
    else if ($userType == "Driver") {
        $returnArr['message'] = getDriverDetailInfo($iMemberId);
    }
    else {
        $returnArr['message'] = getCompanyDetailInfo($iMemberId);
    }
    if ($id > 0) {
        $returnArr['Action'] = "1";
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
    }
    setDataResponse($returnArr);
}

// ##########################################################################

if ($type == 'updateStoreAddress') {
    $iCompanyId = isset($_REQUEST['iCompanyId']) ? clean($_REQUEST['iCompanyId']) : '';
    $Address = isset($_REQUEST['Address']) ? clean($_REQUEST['Address']) : '';
    $Latitude = isset($_REQUEST['Latitude']) ? clean($_REQUEST['Latitude']) : '';
    $Longitude = isset($_REQUEST['Longitude']) ? clean($_REQUEST['Longitude']) : '';
    $sql = "SELECT vCompany  FROM company WHERE iCompanyId=$iCompanyId";
    $result_data = $obj->MySQLSelect($sql);
    $where = " iCompanyId = '" . $iCompanyId . "'";
    $Data_company['vRestuarantLocation'] = $Address;
    $Data_company['vRestuarantLocationLat'] = $Latitude;
    $Data_company['vRestuarantLocationLong'] = $Longitude;
    if (count($result_data) > 0 && $Address != "" && $Latitude != "" && $Longitude != "") {
        $id = $obj->MySQLQueryPerform("company", $Data_company, 'update', $where);
        $returnArr['Action'] = "1";
        $returnArr['restaurantAddressAdded'] = "Yes";
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['restaurantAddressAdded'] = "No";
    }
    setDataResponse($returnArr);
}
// ##########################################################################
if ($type == 'getReceipt') {
    $iTripId = isset($_REQUEST['iTripId']) ? clean($_REQUEST['iTripId']) : '';
    $UserType = isset($_REQUEST['UserType']) ? clean($_REQUEST['UserType']) : ''; //Passenger OR Driver
    $value = sendTripReceipt($iTripId);
    if ($value == true || $value == "true" || $value == "1") {
        $returnArr['Action'] = "1";
        $returnArr['message'] = "LBL_CHECK_INBOX_TXT";
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_FAILED_SEND_RECEIPT_EMAIL_TXT";
    }
    setDataResponse($returnArr);
}
// ##########################################################################
if ($type == "sendRequestToDrivers") {
    sleep(mt_rand(2,7));
    $iOrderId = isset($_REQUEST["iOrderId"]) ? $_REQUEST["iOrderId"] : '';
    $vDeviceToken = isset($_REQUEST["vDeviceToken"]) ? $_REQUEST["vDeviceToken"] : '';
    $eDriverType = isset($_REQUEST["eDriverType"]) ? trim($_REQUEST["eDriverType"]) : '';
    $iDriverIdWeb = isset($_REQUEST["iDriverIdWeb"]) ? trim($_REQUEST["iDriverIdWeb"]) : '';
    $isFromAdmin = isset($_REQUEST["isFromAdmin"]) ? trim($_REQUEST["isFromAdmin"]) : 'No'; //added by SP on 29-01-2021 send request from admin, skip checking wallet balance

    $_REQUEST['eSystem'] = 'DeliverAll';
    $trip_status = "Requesting";
    $checkOrderRequestStatusArr = checkOrderRequestStatus($iOrderId);
    $action = $checkOrderRequestStatusArr['Action'];
    if ($action == 0) {
        setDataResponse($checkOrderRequestStatusArr);
    }
    $sql = "select * from orders WHERE iOrderId='" . $iOrderId . "'";
    $db_order = $obj->MySQLSelect($sql);
    // isMemberEmailPhoneVerified($passengerId,"Passenger");
    $iUserId = $db_order[0]['iUserId'];
    $iCompanyId = $db_order[0]['iCompanyId'];
    $iUserAddressId = $db_order[0]['iUserAddressId'];
    $ePaymentOption = $db_order[0]['ePaymentOption'];
    $companyfields = "vCompany,vRestuarantLocation,vRestuarantLocationLat,vRestuarantLocationLong,vCaddress,eDriverOption";
    $Data_cab_requestcompany = get_value('company', $companyfields, 'iCompanyId', $iCompanyId);
    if ($eDriverType == "") {
        $eDriverType = $Data_cab_requestcompany[0]['eDriverOption'];
    }
    $UserSelectedAddressArr = FetchMemberAddressData($iUserId, "Passenger", $iUserAddressId);
    //Added By HJ On 15-07-2020 For Optimize language_master Table Query Start
    //$vLangCode = $LANG_OBJ->FetchDefaultLangData("vCode");
    //Added By HJ On 15-07-2020 For Optimize language_master Table Query End
    //$languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLangCode, "1", $iServiceId);
    //$userwaitinglabel = $languageLabelsArr['LBL_TRIP_USER_WAITING_DL'];
    //$alertMsg = $userwaitinglabel;
    $PickUpAddress = $Data_cab_requestcompany[0]['vRestuarantLocation'];
    $DestAddress = $UserSelectedAddressArr['UserAddress'];
    $PickUpLatitude = $Data_cab_requestcompany[0]['vRestuarantLocationLat'];
    $PickUpLongitude = $Data_cab_requestcompany[0]['vRestuarantLocationLong'];
    $DestLatitude = $UserSelectedAddressArr['vLatitude'];
    $DestLongitude = $UserSelectedAddressArr['vLongitude'];
    $address_data['PickUpAddress'] = $PickUpAddress;
    $address_data['DropOffAddress'] = $DestAddress;
    $address_data['eDriverType'] = $eDriverType;
    $address_data['iCompanyId'] = $iCompanyId;
    $address_data['iOrderId'] = $iOrderId;
    $DataArr = FetchAvailableDrivers($PickUpLatitude, $PickUpLongitude, $address_data, "Yes", "No", "No", "", $DestLatitude, $DestLongitude, $iUserId, $iDriverIdWeb);
    $Data = $DataArr['DriverList'];
    $driver_id_auto = $DataArr['driver_id_auto'];
    $fWalletDebit = $db_order[0]['fWalletDebit'];
    $fNetTotal = $db_order[0]['fNetTotal'];
    $isFullWalletCharge = "No";
    if ($fWalletDebit > 0 && $fNetTotal == 0) {
        $isFullWalletCharge = "Yes";
    }
    // # Exclude Drivers From list if wallet balance is lower than minimum wallet balance only for cash orders ##
    if ($ePaymentOption == "Cash" && $isFullWalletCharge == "No" && $isFromAdmin == "No") {
        $Data_new = array();
        $Data_new = $Data;
        for ($i = 0; $i < count($Data); $i++) {
            $isRemoveFromList = "No";
            $ACCEPT_CASH_TRIPS = $Data[$i]['ACCEPT_CASH_TRIPS'];
            if ($ACCEPT_CASH_TRIPS == "No") {
                $isRemoveFromList = "Yes";
            }
            if ($isRemoveFromList == "Yes") {
                unset($Data_new[$i]);
            }
        }
        $Data = array_values($Data_new);
        $driver_id_auto = "";
        for ($j = 0; $j < count($Data); $j++) {
            $driver_id_auto .= $Data[$j]['iDriverId'] . ",";
        }
        //$driver_id_auto = substr($driver_id_auto, 0, -1);
        $driver_id_auto = trim($driver_id_auto, ",");
    }
    // # Exclude Drivers From list if wallet balance is lower than minimum wallet balance only for cash orders ##
    $sqlp = "SELECT iGcmRegId,vCompany,vImage as vImgName,vAvgRating,vPhone,vCode as vPhoneCode FROM company WHERE iCompanyId = '" . $iCompanyId . "'";
    $passengerData = $obj->MySQLSelect($sqlp);
    $iGcmRegId = $passengerData[0]['iGcmRegId'];
    if ($vDeviceToken != "" && $vDeviceToken != $iGcmRegId) {
        $returnArr['Action'] = "0";
        $returnArr['RESTRICT_APP'] = "Yes";
        $returnArr['isSessionExpired'] = "Yes";
        $returnArr['message'] = "LBL_SESSION_TIME_OUT";
        $returnArr['message_title'] = "LBL_SESSION_TIME_OUT_TITLE";
        setDataResponse($returnArr);
    }
    $final_message['Message'] = "CabRequested";
    $final_message['sourceLatitude'] = strval($PickUpLatitude);
    $final_message['sourceLongitude'] = strval($PickUpLongitude);
    $final_message['PassengerId'] = strval($iUserId);
    $final_message['iCompanyId'] = strval($iCompanyId);
    $final_message['iOrderId'] = strval($iOrderId);
    $passengerFName = $passengerData[0]['vCompany'];
    $final_message['PName'] = $passengerFName;
    $final_message['PPicName'] = $passengerData[0]['vImgName'];
    $final_message['PRating'] = $passengerData[0]['vAvgRating'];
    $final_message['PPhone'] = $passengerData[0]['vPhone'];
    $final_message['PPhoneC'] = $passengerData[0]['vPhoneCode'];
    $final_message['PPhone'] = '+' . $final_message['PPhoneC'] . $final_message['PPhone'];
    $final_message['destLatitude'] = strval($DestLatitude);
    $final_message['destLongitude'] = strval($DestLongitude);
    $final_message['MsgCode'] = strval(time() . mt_rand(1000, 9999));
    //$final_message['vTitle'] = $alertMsg;
    $final_message['eSystem'] = "DeliverAll";
    $final_message['GenieOrder'] = "No";
    if ($db_order[0]['eBuyAnyService'] == "Yes") {
        $final_message['GenieOrder'] = "Yes";
    }
    $cmpMinutes = ceil((fetchtripstatustimeMAXinterval() + $intervalmins) / 60);
    $str_date = @date('Y-m-d H:i:s', strtotime('-' . $cmpMinutes . ' minutes'));
    $sql = "SELECT iGcmRegId,eDeviceType,iDriverId,vLang,tSessionId,iAppVersion,eAppTerminate,eDebugMode,eHmsDevice FROM register_driver WHERE iDriverId IN (" . $driver_id_auto . ") AND tLocationUpdateDate > '$str_date' AND vAvailability='Available'";
    $result = $obj->MySQLSelect($sql);
    $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($result[0]['vLang'], "1", $iServiceId);
    $result_new = array();
    if ($MODULES_OBJ->isEnableAcceptMultipleOrders()) {
        $sql = "SELECT iGcmRegId,eDeviceType,iDriverId,vLang,tSessionId,iAppVersion,eAppTerminate,eDebugMode,eHmsDevice FROM register_driver WHERE iDriverId IN (" . $driver_id_auto . ") AND tLocationUpdateDate > '$str_date'";
        $result_new = $obj->MySQLSelect($sql);
    }
    $result = array_merge($result, $result_new);
    if (count($result) == 0 || $driver_id_auto == "" || count($Data) == 0) {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "NO_CARS";
        setDataResponse($returnArr);
    }

    $sourceLoc = $PickUpLatitude . ',' . $PickUpLongitude;
    $destLoc = $DestLatitude . ',' . $DestLongitude;

    $generalDataArr = $DriverDataArr = array();
    foreach ($result as $item) {
        //Added By HJ On 11-01-2019 For Get Language Label Value Start
        $alertMsg_db = $languageLabelsArr['LBL_TRIP_USER_WAITING_DL'];
        if ($alertMsg_db == "") {
            $alertMsg_db = "Restaurant is waiting for you";
        }
        //Added By HJ On 11-01-2019 For Get Language Label Value End
        $tSessionId = $item['tSessionId'];
        $final_message['tSessionId'] = $tSessionId;
        $final_message['vTitle'] = $alertMsg_db;
        $generalDataArr[] = array(
            'eDeviceType'    => $item['eDeviceType'], 'deviceToken' => $item['iGcmRegId'], 'alertMsg' => $alertMsg_db, 'eAppTerminate' => $item['eAppTerminate'], 'eDebugMode' => $item['eDebugMode'], 'eHmsDevice' => $item['eHmsDevice'], 'message' => $final_message, 'addRequestSentArr' => array(
                'iUserId' => $iUserId, 'iDriverId' => $item['iDriverId'], 'tMessage' => $final_message, 'iMsgCode' => $final_message['MsgCode'], 'vStartLatlong' => $sourceLoc, 'vEndLatlong' => $destLoc, 'tStartAddress' => $PickUpAddress, 'tEndAddress' => $DestAddress, 'iOrderId' => $iOrderId
            ), 'channelName' => "CAB_REQUEST_DRIVER_" . $item['iDriverId'], 'orderEventChannelName' => $orderEventChannelName
        );

        $item['iOrderId'] = $iOrderId;
        $DriverDataArr[] = $item;
    }
    if ($db_order[0]['eBuyAnyService'] == "No" || $isFromAdmin == "Yes") {
        if(!empty($OPTIMIZE_DATA_OBJ)) {
            $OPTIMIZE_DATA_OBJ->SetCabRequestAddress($DriverDataArr, 'DeliverAll');    
        }

        $EVENT_MSG_OBJ->send(array('GENERAL_DATA' => $generalDataArr), RN_PROVIDER);
    }

    $returnArr['Action'] = "1";
    
    setDataResponse($returnArr);
}
// ##########################################################################
if ($type == "submitRating") {
    $iMemberId = isset($_REQUEST["iMemberId"]) ? $_REQUEST["iMemberId"] : ''; // for both driver or passenger
    $tripID = isset($_REQUEST["tripID"]) ? $_REQUEST["tripID"] : '';
    $iOrderId = isset($_REQUEST["iOrderId"]) ? $_REQUEST["iOrderId"] : '';
    $rating = isset($_REQUEST["rating"]) ? $_REQUEST["rating"] : '';
    $message = isset($_REQUEST["message"]) ? $_REQUEST["message"] : '';
    $rating1 = isset($_REQUEST["rating1"]) ? $_REQUEST["rating1"] : '';
    $message1 = isset($_REQUEST["message1"]) ? $_REQUEST["message1"] : '';
    $eFromUserType = isset($_REQUEST["eFromUserType"]) ? $_REQUEST["eFromUserType"] : 'Passenger'; // Passenger or Driver
    $eToUserType = isset($_REQUEST["eToUserType"]) ? $_REQUEST["eToUserType"] : 'Company'; // Passenger or Driver
    $message = stripslashes($message);
    $iMemberProfileId = $iMemberId;
    $isDetailRatingForDriver = isset($_REQUEST["isDetailRatingForDriver"]) ? $_REQUEST["isDetailRatingForDriver"] : 'No';
    $driverFeedbackDetails = isset($_REQUEST["driverFeedbackDetails"]) ? $_REQUEST["driverFeedbackDetails"] : '';

    $getTripData = $obj->MySQLSelect("SELECT iUserId,iDriverId FROM trips WHERE iTripId='" . $tripID . "'");

    $sql = "SELECT * FROM `ratings_user_driver` WHERE iOrderId = '$iOrderId' and eFromUserType = '$eFromUserType' AND eToUserType = '$eToUserType' ";
    $row_check = $obj->MySQLSelect($sql);
    if (count($row_check) > 0) {
        if ($eFromUserType == "Passenger") {
            $tableName = "register_user";
            $where = "iUserId='" . $getTripData[0]['iUserId'] . "'";
        }
        else {
            $tableName = "register_driver";
            $where = "iDriverId='" . $getTripData[0]['iDriverId'] . "'";
        }

        $Data_update['vTripStatus'] = "Not Active";
        $Data_update['iTripId'] = $tripID;
        $obj->MySQLQueryPerform($tableName, $Data_update, 'update', $where);

        if ($eFromUserType == "Passenger") {
            $returnArr['USER_DATA'] = getPassengerDetailInfo($getTripData[0]['iUserId']);
        }
        else {
            $returnArr['USER_DATA'] = getDriverDetailInfo($getTripData[0]['iDriverId']);
        }
        
        $returnArr['Action'] = "1";
        $returnArr['message'] = "LBL_TRIP_FINISHED_TXT_DL";
        setDataResponse($returnArr);
    }
    else {
        if ($eFromUserType == "Passenger") {
            $OrderData = get_value('orders', 'iDriverId,iCompanyId,eBuyAnyService', 'iOrderId', $iOrderId);
            $TripsData = get_value('trips', 'iTripid', 'iOrderId', $iOrderId);
            $iDriverId = $OrderData[0]['iDriverId'];
            $iCompanyId = $OrderData[0]['iCompanyId'];
            $eBuyAnyService = $OrderData[0]['eBuyAnyService'];
            $tripID = $TripsData[0]['iTripid'];
            $tableName = "register_driver";
            $where = "iDriverId='" . $iDriverId . "'";
            $iMemberId = $iDriverId;
            $tableName1 = "company";
            $where1 = "iCompanyId='" . $iCompanyId . "'";
            $iMemberId1 = $iCompanyId;
            /* Insert records into ratings table */
            $Data_update_ratings['iTripId'] = $tripID;
            $Data_update_ratings['iOrderId'] = $iOrderId;
            $Data_update_ratings['vRating1'] = $rating;
            $Data_update_ratings['vMessage'] = $message;
            $Data_update_ratings['eFromUserType'] = $eFromUserType;
            $Data_update_ratings['eToUserType'] = $eToUserType;
            $id = $obj->MySQLQueryPerform("ratings_user_driver", $Data_update_ratings, 'insert');
            if ($eBuyAnyService == "No") {
                $Data_update['vAvgRating'] = FetchUserAvgRating($iMemberId1, "Company");
                $Company_Rating_id = $obj->MySQLQueryPerform($tableName1, $Data_update, 'update', $where1);
            }
            $Data_update_ratings1['iOrderId'] = $iOrderId;
            $Data_update_ratings1['iTripId'] = $tripID;
            $Data_update_ratings1['vRating1'] = $rating1;
            $Data_update_ratings1['vMessage'] = $message1;
            $Data_update_ratings1['eFromUserType'] = $eFromUserType;
            $Data_update_ratings1['eToUserType'] = "Driver";
            if (!empty($driverFeedbackDetails)) {
                $Data_update_ratings1['tDriverFeedbackDetails'] = $driverFeedbackDetails;
            }
            $Driver_Rating_insert_id = $obj->MySQLQueryPerform("ratings_user_driver", $Data_update_ratings1, 'insert');
            $Data_update1['vAvgRating'] = FetchUserAvgRating($iMemberId, $eFromUserType);
            $Driver_Rating_update_id = $obj->MySQLQueryPerform($tableName, $Data_update1, 'update', $where);
        }
        else {
            $iUserId = get_value('orders', 'iUserId', 'iOrderId', $iOrderId, '', 'true');
            $tableName = "register_user";
            $where = "iUserId='" . $iUserId . "'";
            $iMemberId = $iUserId;
            /* Insert records into ratings table */
            $Data_update_ratings['iTripId'] = $tripID;
            $Data_update_ratings['iOrderId'] = $iOrderId;
            $Data_update_ratings['vRating1'] = $rating;
            $Data_update_ratings['vMessage'] = $message;
            $Data_update_ratings['eFromUserType'] = $eFromUserType;
            $Data_update_ratings['eToUserType'] = $eToUserType;
            $id = $obj->MySQLQueryPerform("ratings_user_driver", $Data_update_ratings, 'insert');
            $Data_update['vAvgRating'] = FetchUserAvgRating($iMemberId, $eFromUserType);
            $Passenger_Rating_update_id = $obj->MySQLQueryPerform($tableName, $Data_update, 'update', $where);

            $Data_update_trip = array();
            $Data_update_trip['vTripStatus'] = "Not Active";
            $Data_update_trip['iTripId'] = $tripID;
            $where_trip = " iDriverId = '" . $getTripData[0]['iDriverId'] . "' ";
            $obj->MySQLQueryPerform("register_driver", $Data_update_trip, 'update', $where_trip);
        }
        if ($id) {
            $returnArr['Action'] = "1";
            $returnArr['message'] = "LBL_TRIP_FINISHED_TXT_DL";
            if ($eFromUserType == "Passenger") {
                $returnArr['USER_DATA'] = getPassengerDetailInfo($iMemberProfileId, "", "");
            }
            else {
                $returnArr['USER_DATA'] = getDriverDetailInfo($iMemberProfileId, "");
            }
            setDataResponse($returnArr);
        }
        else {
            $returnArr['Action'] = "0";
            $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
            setDataResponse($returnArr);
        }
        if ($eFromUserType == "Passenger") {
            // sendTripReceipt($tripID);
        }
        else {
            // sendTripReceiptAdmin($tripID);
        }
    }
}

// ##########################################################################
if ($type == "updateDriverStatus") {
    $iDriverId = isset($_REQUEST["iDriverId"]) ? $_REQUEST["iDriverId"] : '';
    $Status_driver = isset($_REQUEST["Status"]) ? $_REQUEST["Status"] : '';
    $isUpdateOnlineDate = isset($_REQUEST["isUpdateOnlineDate"]) ? $_REQUEST["isUpdateOnlineDate"] : '';
    $latitude_driver = isset($_REQUEST["latitude"]) ? $_REQUEST["latitude"] : '';
    $longitude_driver = isset($_REQUEST["longitude"]) ? $_REQUEST["longitude"] : '';
    $iGCMregID = isset($_REQUEST["vDeviceToken"]) ? $_REQUEST["vDeviceToken"] : '';
    $vTimeZone = isset($_REQUEST["vTimeZone"]) ? $_REQUEST["vTimeZone"] : '';
    $ssql = "";
    $sql_car = "SELECT make.vMake, model.vTitle, dv.*, rd.iDriverVehicleId as iSelectedVehicleId FROM `driver_vehicle` dv, make, model, register_driver as rd WHERE dv.iDriverId='$iDriverId' AND rd.iDriverId='$iDriverId' AND dv.`iMakeId` = make.`iMakeId` AND dv.`iModelId` = model.`iModelId` AND dv.`eStatus`='Active'" . $ssql;
    //added by SP on 08-09-2020 chk whether ride or any other service is not selected..so in app select vehicle line in home screen will be disabled.
    $Data_Car = $obj->MySQLSelect($sql_car);
    $returnArr['isOnlyUfxServicesSelected'] = 'No';
    if (count($Data_Car) > 0) {
        if (count($Data_Car) == 1 && $Data_Car[0]['eType'] == "UberX" && $Data_Car[0]['vCarType'] != "" && $isUfxAvailable == 'Yes') {
            $returnArr['isOnlyUfxServicesSelected'] = 'Yes';
        }
    }
    if ($APP_TYPE == "UberX") {
        $returnArr['isOnlyUfxServicesSelected'] = 'Yes';
    }
    if ($PACKAGE_TYPE == "SHARK" && $Status_driver == "Available") {
        $BlockData = getBlockData("Driver", $iDriverId);
        if (!empty($BlockData) || $BlockData != "") {
            setDataResponse($BlockData);
        }
    }
    /* $sql = "SELECT eIsBlocked,iDriverId FROM register_driver WHERE iDriverId='$iDriverId' ";
      $Data_Driver = $obj->MySQLSelect($sql);
      $eIsBlocked = $Data_Driver[0]['eIsBlocked'];

      if ($eIsBlocked == 'Yes' && $Status_driver == "Available") {
      $returnArr['Action'] = "0";
      $returnArr['isShowContactUs'] = "Yes";
      $returnArr['message'] = "LBL_DRIVER_BLOCK";
      setDataResponse($returnArr);
    } */
    if ($Status_driver == "Available") {
        isMemberEmailPhoneVerified($iDriverId, "Driver");
    }
    if ($iDriverId == '') {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
        setDataResponse($returnArr);
    }
    if ($MODULES_OBJ->isEnableCancelDriverOrder()) {
        $TODATE = date('Y-m-d');
        $data = $obj->MySQLSelect("SELECT dDate FROM order_driver_log WHERE iDriverId = '" . $iDriverId . "' AND DATE(dDate) = '" . $TODATE . "' ORDER BY iLogId ASC LIMIT 1");
        $TODATE_STIME = strtotime(date('Y-m-d h:i:s'));
        $DRIVER_STIME = strtotime(date('Y-m-d h:i:s', strtotime("+" . $FLAG_DRIVER_FOR_CANCLE_ORDER . " minutes", strtotime($data[0]['dDate']))));
        if ($DRIVER_STIME > $TODATE_STIME && $Status_driver == "Available") {
            $returnArr['Action'] = "0";
            $returnArr['message'] = "LBL_DRIVER_BLOCK_FOR_CANCLE";
            $returnArr['message2'] = "Driver Will Online when " . $DRIVER_STIME . " > " . $TODATE_STIME . "";
            setDataResponse($returnArr);
        }
    }
    $GCMID = get_value('register_driver', 'iGcmRegId', 'iDriverId', $iDriverId, '', 'true');
    if ($GCMID != "" && $iGCMregID != "" && $GCMID != $iGCMregID) {
        $returnArr['Action'] = "0";
        $returnArr['RESTRICT_APP'] = "Yes";
        $returnArr['isSessionExpired'] = "Yes";
        $returnArr['message'] = "LBL_SESSION_TIME_OUT";
        $returnArr['message_title'] = "LBL_SESSION_TIME_OUT_TITLE";
        setDataResponse($returnArr);
    }
    $returnArr['Enable_Hailtrip'] = "No";
    $enableCommisionDeduct = $MODULES_OBJ->autoDeductDriverCommision("DeliverAll"); // Added By HJ On 16-10-2020 For get Auto Deduct Driver Commision Configuration As Per eSystem
    if (isset($_REQUEST['test'])) {
    }
    if ($enableCommisionDeduct == 'Yes' && $Status_driver == "Available") {
        $vLang = get_value('register_driver', 'vLang', 'iDriverId', $iDriverId, '', 'true');
        if ($vLang == "" || $vLang == NULL) {
            //Added By HJ On 15-07-2020 For Optimize language_master Table Query Start
            $vLang = $LANG_OBJ->FetchDefaultLangData("vCode");
            //Added By HJ On 15-07-2020 For Optimize language_master Table Query End
        }
        $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLang, "1", $iServiceId);
        $user_available_balance = $WALLET_OBJ->FetchMemberWalletBalance($iDriverId, "Driver");
        $driverDetail = get_value('register_driver AS rd LEFT JOIN currency AS c ON c.vName=rd.vCurrencyDriver', 'rd.vCurrencyDriver,c.Ratio,c.vSymbol', 'rd.iDriverId', $iDriverId);
        $vCurrencyDriver = $driverDetail[0]['vCurrencyDriver'];
        $ratio = $driverDetail[0]['Ratio'];
        $currencySymbol = $driverDetail[0]['vSymbol'];
        // $WALLET_MIN_BALANCE=$CONFIG_OBJ->getConfigurations("configurations","WALLET_MIN_BALANCE");
        if ($WALLET_MIN_BALANCE > $user_available_balance) {
            // $returnArr['Action'] = "0";
            $returnArr['message'] = "REQUIRED_MINIMUM_BALNCE";
            if ($APP_TYPE == "UberX") {
                $returnArr['Msg'] = str_replace('####', $currencySymbol . ($WALLET_MIN_BALANCE * $ratio), $languageLabelsArr['LBL_REQUIRED_MINIMUM_BALNCE_UBERX']);
            }
            else {
                $returnArr['Msg'] = str_replace('####', $currencySymbol . ($WALLET_MIN_BALANCE * $ratio), $languageLabelsArr['LBL_REQUIRED_MINIMUM_BALNCE']);
            }
            // if ($APP_PAYMENT_MODE == "Cash") {
            if ($CONFIG_OBJ->isOnlyCashPaymentModeAvailable()) {
                if ($Status_driver == "Available") {
                    $returnArr['Action'] = "0";
                    setDataResponse($returnArr);
                }
            }
        }
        //$returnArr['Enable_Hailtrip'] = "Yes";
    }
    //added by SP on 15-02-2021 as discussed with KS for issue sheet 2002
    // if ($APP_PAYMENT_MODE != "Card" && $Status_driver == "Available" && $MODULES_OBJ->isRideFeatureAvailable()==1) {
    if (strtoupper($CASH_AVAILABLE) == "YES" && $Status_driver == "Available" && $MODULES_OBJ->isRideFeatureAvailable() == 1) {
        $returnArr['Enable_Hailtrip'] = "Yes";
    }
    //if ($enableCommisionDeduct == 'No' && ($APP_PAYMENT_MODE == "Cash" || $APP_PAYMENT_MODE == "Cash-Card")) {
    //    $returnArr['Enable_Hailtrip'] = "Yes";
    //}
    // getDriverStatus($iDriverId);
    // $APP_TYPE = $CONFIG_OBJ->getConfigurations("configurations", "APP_TYPE");
    //$ssql = "";
    //$sql = "SELECT make.vMake, model.vTitle, dv.*, rd.iDriverVehicleId as iSelectedVehicleId FROM `driver_vehicle` dv, make, model, register_driver as rd WHERE dv.iDriverId='$iDriverId' AND rd.iDriverId='$iDriverId' AND dv.`iMakeId` = make.`iMakeId` AND dv.`iModelId` = model.`iModelId` AND dv.`eStatus`='Active'" . $ssql;
    //$Data_Car = $obj->MySQLSelect($sql);
    if (count($Data_Car) > 0) {
        $status = "CARS_NOT_ACTIVE";
        $i = 0;
        while (count($Data_Car) > $i) {
            $eStatus = $Data_Car[$i]['eStatus'];
            if ($eStatus == "Active") {
                $status = "CARS_AVAIL";
            }
            $i++;
        }
        if ($status == "CARS_AVAIL" && ($Data_Car[0]['iSelectedVehicleId'] == "0" || $Data_Car[0]['iSelectedVehicleId'] == "")) {
            $returnArr['Action'] = "0";
            $returnArr['message'] = "LBL_SELECT_CAR_MESSAGE_TXT";
            setDataResponse($returnArr);
        }
        else if ($status == "CARS_NOT_ACTIVE") {
            $returnArr['Action'] = "0";
            $returnArr['message'] = "LBL_INACTIVE_CARS_MESSAGE_TXT";
            setDataResponse($returnArr);
        }
    }
    else if ($Status_driver == "Available") { // Added By HJ On 02-12-2019 For Solved Sheet Bug = 567 As Per Discuss With KS Sir
        $sql = "SELECT count(iDriverVehicleId) as TotalVehicles from driver_vehicle WHERE iDriverId = '" . $iDriverId . "' AND ( eStatus = 'Inactive' OR eStatus = 'Deleted')";
        $db_Total_vehicle = $obj->MySQLSelect($sql);
        $TotalVehicles = $db_Total_vehicle[0]['TotalVehicles'];
        $returnArr['Action'] = "0";
        if ($TotalVehicles == 0) {
            $returnArr['message'] = "LBL_NO_CAR_AVAIL_TXT";
        }
        else {
            $returnArr['message'] = "LBL_INACTIVE_CARS_MESSAGE_TXT";
        }
        setDataResponse($returnArr);
    }
    $where = " iDriverId='" . $iDriverId . "'";
    if ($Status_driver != '') {
        $Data_update_driver['vAvailability'] = $Status_driver;
    }
    if ($latitude_driver != '' && $longitude_driver != '') {
        $Data_update_driver['vLatitude'] = $latitude_driver;
        $Data_update_driver['vLongitude'] = $longitude_driver;
    }
    if ($Status_driver == "Available") {
        $Data_update_driver['tOnline'] = @date("Y-m-d H:i:s");
        // insert as online
        // Code for Check last logout date is update in driver_log_report
        $query = "SELECT * FROM driver_log_report WHERE dLogoutDateTime = '0000-00-00 00:00:00' AND iDriverId = '" . $iDriverId . "' ORDER BY iDriverLogId DESC LIMIT 0,1";
        $db_driver = $obj->MySQLSelect($query);
        if (count($db_driver) > 0) {
            $sql = "SELECT tLastOnline FROM register_driver WHERE iDriverId = '" . $iDriverId . "'";
            $db_drive_lastonline = $obj->MySQLSelect($sql);
            $driver_lastonline = $db_drive_lastonline[0]['tLastOnline'];
            $updateQuery = "UPDATE driver_log_report set dLogoutDateTime='" . $driver_lastonline . "' WHERE iDriverLogId = " . $db_driver[0]['iDriverLogId'];
            $obj->sql_query($updateQuery);
        }
        // Code for Check last logout date is update in driver_log_report Ends
        $vIP = get_client_ip();
        $curr_date = date('Y-m-d H:i:s');
        $sql = "INSERT INTO `driver_log_report` (`iDriverId`,`dLoginDateTime`,`vIP`) VALUES ('" . $iDriverId . "','" . $curr_date . "','" . $vIP . "')";
        $insert_log = $obj->sql_query($sql);
        //update insurance log
        if (strtoupper($PACKAGE_TYPE) == "SHARK") {
            $details_arr['iTripId'] = "0";
            $details_arr['LatLngArr']['vLatitude'] = $latitude_driver;
            $details_arr['LatLngArr']['vLongitude'] = $longitude_driver;
            // $details_arr['LatLngArr']['vLocation'] = "";
            update_driver_insurance_status($iDriverId, "Available", $details_arr, "updateDriverStatus", "Online");
        }
        //update insurance log
    }
    if ($Status_driver == "Not Available") {
        // update as offline
        $Data_update_driver['tLastOnline'] = @date("Y-m-d H:i:s");
        $curr_date = date('Y-m-d H:i:s');
        $selct_query = "select * from driver_log_report WHERE iDriverId = '" . $iDriverId . "' order by `iDriverLogId` desc limit 0,1";
        $get_data_log = $obj->sql_query($selct_query);
        $update_sql = "UPDATE driver_log_report set dLogoutDateTime = '" . $curr_date . "' WHERE iDriverLogId ='" . $get_data_log[0]['iDriverLogId'] . "'";
        $result = $obj->sql_query($update_sql);
        //update insurance log
        if (strtoupper($PACKAGE_TYPE) == "SHARK") {
            $details_arr['iTripId'] = "0";
            $details_arr['LatLngArr']['vLatitude'] = $latitude_driver;
            $details_arr['LatLngArr']['vLongitude'] = $longitude_driver;
            // $details_arr['LatLngArr']['vLocation'] = "";
            update_driver_insurance_status($iDriverId, "Available", $details_arr, "updateDriverStatus", "Offline");
        }
        //update insurance log
    }
    if (($isUpdateOnlineDate == "true" && $Status_driver == "Available") || ($isUpdateOnlineDate == "" && $Status_driver == "") || $isUpdateOnlineDate == "true") {
        $Data_update_driver['tOnline'] = @date("Y-m-d H:i:s");
        $Data_update_driver['tLastOnline'] = @date("Y-m-d H:i:s");
    }
    $id = $obj->MySQLQueryPerform("register_driver", $Data_update_driver, 'update', $where);
    // Update User Location Date #
    Updateuserlocationdatetime($iDriverId, "Driver", $vTimeZone);
    // Update User Location Date #
    if ($id) {
        $returnArr['Action'] = "1";
        setDataResponse($returnArr);
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
        setDataResponse($returnArr);
    }
}
// ##########################################################################
if ($type == "LoadAvailableCars") {
    $iDriverId = isset($_REQUEST["iDriverId"]) ? $_REQUEST["iDriverId"] : '';
    $ssql = " AND dv.eType != 'UberX'";
    $sql = "SELECT register_driver.iDriverVehicleId as DriverSelectedVehicleId,make.vMake, model.vTitle, dv.* FROM `driver_vehicle` dv, make, model,register_driver WHERE dv.iDriverId='$iDriverId' AND register_driver.iDriverId = '$iDriverId' AND dv.`iMakeId` = make.`iMakeId` AND dv.`iModelId` = model.`iModelId` AND dv.`eStatus`='Active' $ssql";
    $Data_Car = $obj->MySQLSelect($sql);
    if (count($Data_Car) > 0) {
        $status = "CARS_NOT_ACTIVE";
        $i = 0;
        while (count($Data_Car) > $i) {
            $eStatus = $Data_Car[$i]['eStatus'];
            if ($eStatus == "Active") {
                $status = "CARS_AVAIL";
            }
            $i++;
        }
        if ($status == "CARS_NOT_ACTIVE") {
            $returnArr['Action'] = "0";
            $returnArr['message'] = "LBL_INACTIVE_CARS_MESSAGE_TXT";
            setDataResponse($returnArr);
        }
        // $returnArr['carList'] = $Data_Car;
        $db_vehicle_new = $Data_Car;
        for ($i = 0; $i < count($Data_Car); $i++) {
            $vCarType = $Data_Car[$i]['vCarType'];
            $sql = "SELECT iVehicleTypeId,eType  FROM `vehicle_type` WHERE `iVehicleTypeId` IN ($vCarType)";
            $db_cartype = $obj->MySQLSelect($sql);
            $k = 0;
            if (count($db_cartype) > 0) {
                for ($j = 0; $j < count($db_cartype); $j++) {
                    $eType = $db_cartype[$j]['eType'];
                    if ($eType == "UberX") {
                        // unset($db_vehicle_new[$i]);
                    }
                }
            }
        }
        $db_vehicle_new = array_values($db_vehicle_new);
        // setDataResponse($returnArr);
        $returnArr['Action'] = "1";
        $returnArr['message'] = $db_vehicle_new;
        setDataResponse($returnArr);
    }
    else {
        $sql = "SELECT count(iDriverVehicleId) as TotalVehicles from driver_vehicle WHERE iDriverId = '" . $driverId . "' AND ( eStatus = 'Inactive' OR eStatus = 'Deleted')";
        $db_Total_vehicle = $obj->MySQLSelect($sql);
        $TotalVehicles = $db_Total_vehicle[0]['TotalVehicles'];
        $returnArr['Action'] = "0";
        if ($TotalVehicles == 0) {
            $returnArr['message'] = "LBL_NO_CAR_AVAIL_TXT";
        }
        else {
            $returnArr['message'] = "LBL_INACTIVE_CARS_MESSAGE_TXT";
        }
        setDataResponse($returnArr);
    }
}
// ########################## Set Driver CarID ############################
if ($type == "SetDriverCarID") {
    $iDriverId = isset($_REQUEST["iDriverId"]) ? $_REQUEST["iDriverId"] : '';
    $Data['iDriverVehicleId'] = isset($_REQUEST["iDriverVehicleId"]) ? $_REQUEST["iDriverVehicleId"] : '';
    $where = " iDriverId = '" . $iDriverId . "'";
    $sql = $obj->MySQLQueryPerform("register_driver", $Data, 'update', $where);
    if ($sql > 0) {
        $returnArr['Action'] = "1";
        setDataResponse($returnArr);
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
        setDataResponse($returnArr);
    }
}
// ##########################################################################
if ($type == "GenerateTrip") {
    $iOrderId = isset($_REQUEST["iOrderId"]) ? $_REQUEST["iOrderId"] : '';
    $iDriverId = isset($_REQUEST["iDriverId"]) ? $_REQUEST["iDriverId"] : '';
    $passenger_id = isset($_REQUEST["PassengerID"]) ? $_REQUEST["PassengerID"] : '';
    $Source_point_latitude = isset($_REQUEST["tSourceLat"]) ? $_REQUEST["tSourceLat"] : '';
    $Source_point_longitude = isset($_REQUEST["tSourceLong"]) ? $_REQUEST["tSourceLong"] : '';
    $Source_point_Address = isset($_REQUEST["tSourceAddress"]) ? $_REQUEST["tSourceAddress"] : '';
    $Dest_point_latitude = isset($_REQUEST["tDestLatitude"]) ? $_REQUEST["tDestLatitude"] : '';
    $Dest_point_longitude = isset($_REQUEST["tDestLongitude"]) ? $_REQUEST["tDestLongitude"] : '';
    $Dest_point_Address = isset($_REQUEST["tDestAddress"]) ? $_REQUEST["tDestAddress"] : '';
    $GoogleServerKey = isset($_REQUEST["GoogleServerKey"]) ? $_REQUEST["GoogleServerKey"] : '';
    $vMsgCode = isset($_REQUEST["vMsgCode"]) ? $_REQUEST["vMsgCode"] : '';
    $setCron = isset($_REQUEST["setCron"]) ? $_REQUEST["setCron"] : 'No';
    $vLatitude = isset($_REQUEST["vLatitude"]) ? $_REQUEST["vLatitude"] : '';
    $vLongitude = isset($_REQUEST["vLongitude"]) ? $_REQUEST["vLongitude"] : '';
    $_REQUEST['eSystem'] = "DeliverAll";

    //Added By HJ On 13-02-2020 For Get Start and End Lat and Lang Data Start
    if ($Source_point_latitude == "") {
        $Source_point_latitude = isset($_REQUEST["start_lat"]) ? $_REQUEST["start_lat"] : '';
    }
    if ($Source_point_longitude == "") {
        $Source_point_longitude = isset($_REQUEST["start_lon"]) ? $_REQUEST["start_lon"] : '';
    }
    if ($Source_point_Address == "") {
        $Source_point_Address = isset($_REQUEST["sAddress"]) ? $_REQUEST["sAddress"] : '';
    }
    if ($Dest_point_latitude == "") {
        $Dest_point_latitude = isset($_REQUEST["vLatitude"]) ? $_REQUEST["vLatitude"] : '';
    }
    if ($Dest_point_longitude == "") {
        $Dest_point_longitude = isset($_REQUEST["vLongitude"]) ? $_REQUEST["vLongitude"] : '';
    }
    //Added By HJ On 13-02-2020 For Get Start and End Lat and Lang Data End
    if ($PACKAGE_TYPE == "SHARK") {
        $BlockData = getBlockData("Driver", $iDriverId);
        if (!empty($BlockData) || $BlockData != "") {
            setDataResponse($BlockData);
        }
    }

    // ### Update Driver Request Status of Trip ####
    UpdateDriverRequest2($iDriverId, $passenger_id, $iTripId, "", $vMsgCode, "Yes", $iOrderId);
    // $APP_TYPE = $CONFIG_OBJ->getConfigurations("configurations","APP_TYPE");
    /* ------------------------------ order_request ----------------------------- */
    if (isset($iOrderId) && !empty($iOrderId)) {
        $orderRequest = $obj->MySQLSelect("SELECT COUNT(orderRequestId) as count FROM `order_request` WHERE 1=1 AND iOrderId = '" . $iOrderId . "' AND vMsgCode = '" . $vMsgCode . "' AND eStatus = 'Accept'");
        if ($orderRequest[0]['count'] == 0) {
            $order_request['iOrderId'] = $iOrderId;
            $order_request['vMsgCode'] = $vMsgCode;
            $order_request['eStatus'] = 'Accept';
            $order_request['iDriverId'] = $iDriverId;
            $obj->MySQLQueryPerform('order_request', $order_request, 'insert');
        }
        else {
            $returnArr['Action'] = "0";
            $returnArr['message'] = "LBL_SAME_ORDER_TRIP_EXIST_TXT";
            setDataResponse($returnArr);
        }
    }
    /* ------------------------------ order_request ----------------------------- */
    if ($MODULES_OBJ->isEnableAcceptMultipleOrders()) {
        $checkOrderTrip = $obj->MySQLSelect("SELECT iTripId FROM trips WHERE iOrderId = '$iOrderId' AND iActive != 'Canceled' ORDER BY iTripId DESC");
        if (!empty($checkOrderTrip) && count($checkOrderTrip) > 0) {
            $returnArr['Action'] = "0";
            $returnArr['message'] = "LBL_SAME_ORDER_TRIP_EXIST_TXT";
           setDataResponse($returnArr);
        }

        $oData = $obj->MySQLSelect("SELECT o.iOrderId, o.iStatusCode, c.vRestuarantLocationLat, c.vRestuarantLocationLong, uad.vLatitude, uad.vLongitude FROM orders as o LEFT JOIN company as c ON c.iCompanyId = o.iCompanyId LEFT JOIN user_address as uad ON uad.iUserAddressId = o.iUserAddressId WHERE iDriverId = '" . $iDriverId . "' AND o.eCancelledbyDriver = 'No' AND iStatusCode IN (4,5,13,14) ORDER BY iOrderId");

        if (!empty($oData)) {
            if(count($oData) >= $MAXIMUM_MULTIPLE_ORDERS_COUNT) {

                $returnArr['Action'] = "0";
                $returnArr['message'] = "LBL_DRIVER_NOT_ACCEPT_TRIP";
                setDataResponse($returnArr);
            }

            if ($oData[0]['iStatusCode'] == "4") {
                $currentOrder = $obj->MySQLSelect("SELECT c.vRestuarantLocationLat, c.vRestuarantLocationLong, uad.vLatitude, uad.vLongitude FROM orders as o LEFT JOIN company as c ON c.iCompanyId = o.iCompanyId LEFT JOIN user_address as uad ON uad.iUserAddressId = o.iUserAddressId WHERE iOrderId = '" . $iOrderId . "' ");
                $pickupRangeDistance = distanceByLocation($oData[0]['vRestuarantLocationLat'], $oData[0]['vRestuarantLocationLong'], $currentOrder[0]['vRestuarantLocationLat'], $currentOrder[0]['vRestuarantLocationLong'], "K");
                $dropoffRangeDistance = distanceByLocation($oData[0]['vLatitude'], $oData[0]['vLongitude'], $currentOrder[0]['vLatitude'], $currentOrder[0]['vLongitude'], "K");
                
                if ($pickupRangeDistance > $MULTIPLE_ORDER_PICKUP_RANGE || $dropoffRangeDistance > $MULTIPLE_ORDER_DELIVERY_RANGE) {
                    $returnArr['Action'] = "0";
                    $returnArr['message'] = "LBL_DRIVER_NOT_ACCEPT_TRIP";
                    setDataResponse($returnArr);
                }
            }
        }
    }
    else {
        if ($iDriverId > 0) {
            $sqldata = "SELECT iTripId FROM `trips` WHERE ( iActive='On Going Trip' OR iActive='Active' ) AND iDriverId='" . $iDriverId . "'";
            $TripData = $obj->MySQLSelect($sqldata);
            if (count($TripData) > 0) {
                $returnArr['Action'] = "0";
                $returnArr['message'] = "LBL_DRIVER_NOT_ACCEPT_TRIP";
                setDataResponse($returnArr);
            }
        }
        if ($MODULES_OBJ->isEnableCancelDriverOrder()) {
            $sqld = "SELECT orders.eCancelledbyDriver,orders.iStatusCode,trips.iTripId FROM `trips` LEFT JOIN orders ON orders.iOrderId ='" . $iOrderId . "' WHERE trips.iOrderId ='" . $iOrderId . "' ";
            $TripOrderData = $obj->MySQLSelect($sqld);
            if (count($TripOrderData) > 0 && $TripOrderData[0]['eCancelledbyDriver'] == "No" && $TripOrderData[0]['iStatusCode'] != 4) {
                $returnArr['Action'] = "0";
                $returnArr['message'] = "LBL_SAME_ORDER_TRIP_EXIST_TXT";
                setDataResponse($returnArr);
            }
        }
        else {
            $sqld = "SELECT iTripId FROM `trips` WHERE iOrderId ='" . $iOrderId . "'";
            $TripOrderData = $obj->MySQLSelect($sqld);
            if (count($TripOrderData) > 0) {
                $returnArr['Action'] = "0";
                $returnArr['message'] = "LBL_SAME_ORDER_TRIP_EXIST_TXT";
                setDataResponse($returnArr);
            }
        }
        /*     * ******* Create Service Lock ********* */
        $isServiceLock = checkServiceLock($iOrderId, "", true, false);
        if ($isServiceLock) {
            $returnArr['Action'] = "0";
            $returnArr['message'] = "LBL_SAME_ORDER_TRIP_EXIST_TXT";
            //unLinkFile($driverLockFilePath);
            setDataResponse($returnArr);
        }
        /*     * ******* Create Service Lock ********* */
        /*     * ******* Create Service Lock Added By HJ On 10-07-2019 End********* */
    }
    // ### Update Driver Request Status of Trip ####
    $sql = "select * from orders WHERE iOrderId='" . $iOrderId . "'";
    $db_order = $obj->MySQLSelect($sql);
    $iUserId = $db_order[0]['iUserId'];
    $iCompanyId = $db_order[0]['iCompanyId'];
    $iServiceId = $db_order[0]['iServiceId'];
    $iUserAddressId = $db_order[0]['iUserAddressId'];
    $vOrderNo = $db_order[0]['vOrderNo'];
    // payment method 2
    $ePayWallet = $db_order[0]['ePayWallet'];
    $fWalletDebit = $db_order[0]['fWalletDebit'];
    // payment method 2
    $DriverMessage = "CabRequestAccepted";
    $TripRideNO = GenerateUniqueTripNo();
    $TripVerificationCode = generateCommonRandom();
    $Active = "Active";
    $vLangCode = get_value('register_user', 'vLang', 'iUserId', $iUserId, '', 'true');
    if ($vLangCode == "" || $vLangCode == NULL) {
        //Added By HJ On 15-07-2020 For Optimize language_master Table Query Start
        $vLangCode = $LANG_OBJ->FetchDefaultLangData("vCode");
        //Added By HJ On 15-07-2020 For Optimize language_master Table Query End
    }
    //Added By HJ On 15-07-2020 For Optimize language_master Table Query Start
    if (isset($languageAssociateArr[$vLangCode])) {
        $vGMapLangCode = $languageAssociateArr[$vLangCode]['vGMapLangCode'];
    }
    else {
        $vGMapLangCode = get_value('language_master', 'vGMapLangCode', 'vCode', $vLangCode, '', 'true');
    }
    //Added By HJ On 15-07-2020 For Optimize language_master Table Query End
    $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLangCode, "1", $iServiceId);
    $tripdriverarrivlbl = $languageLabelsArr['LBL_DRIVER_ARRIVING'];
    $reqestId = "";
    $trip_status_chkField = "iCabRequestId";
    if ($iOrderId > 0) {
        if ($iDriverId != "") {


            /*if (isset($iOrderId) && !empty($iOrderId)) {
                $orderRequest = $obj->MySQLSelect("SELECT COUNT(orderRequestId) as count , iDriverId  FROM `order_request` WHERE 1=1 AND iOrderId = '" . $iOrderId . "' AND vMsgCode = '" . $vMsgCode . "'  AND eStatus = 'Accept'");
                if ($orderRequest[0]['count'] > 0 && $orderRequest[0]['iDriverId'] != $iDriverId )  {
                    $returnArr['Action'] = "0";
                    $returnArr['message'] = "LBL_SAME_ORDER_TRIP_EXIST_TXT";
                    setDataResponse($returnArr);
                }
            }*/
            $where = " iOrderId = '$iOrderId'";
            $Data_update_order_driver['iDriverId'] = $iDriverId;
            $Data_update_order_driver['iStatusCode'] = "4";
            $obj->MySQLQueryPerform("orders", $Data_update_order_driver, 'update', $where);
            $Order_Status_id = createOrderLog($iOrderId, "4");
        }
        $sql = "SELECT vCurrencyPassenger,iAppVersion,iUserPetId FROM `register_user` WHERE iUserId = '$iUserId'";
        $Data_passenger_detail = $obj->MySQLSelect($sql);
        $sql = "select GROUP_CONCAT(iVehicleTypeId)as VehicleTypeId from `vehicle_type` where eType = 'DeliverAll'";
        $db_deliverall_vehicle = $obj->MySQLSelect($sql);
        $VehicleTypeId = $db_deliverall_vehicle[0]['VehicleTypeId'];
        $VehicleTypeIdArr = explode(",", $VehicleTypeId);
        $sql = "SELECT iDriverVehicleId,vCurrencyDriver,iAppVersion,vName,vLastName FROM `register_driver` WHERE iDriverId = '$iDriverId'";
        $Data_vehicle = $obj->MySQLSelect($sql);
        $CAR_id_driver = $Data_vehicle[0]['iDriverVehicleId'];
        $DriverCarType = get_value('driver_vehicle', 'vCarType', 'iDriverVehicleId', $CAR_id_driver, '', 'true');
        $drivercartypeArr = explode(",", $DriverCarType);
        $vCarTypeArr = array_intersect($VehicleTypeIdArr, $drivercartypeArr);
        $vCarTypeArr = array_values($vCarTypeArr);
        $vCarType = $vCarTypeArr[0];
        $vehicleTypeData = $obj->MySQLSelect("SELECT fDeliveryChargeCancelOrder,fCommision,eIconType,eFareType FROM vehicle_type WHERE iVehicleTypeId = $vCarType");
        $fDeliveryCharge = $vehicleTypeData[0]['fDeliveryChargeCancelOrder'];
        if ($MODULES_OBJ->isEnableDistanceWiseDeliveryChargeOrder()) {
            $fDeliveryCharge = 0;
            $fCustomDeliveryChargeDetails = get_value('order_delivery_charge_details', 'tDeliveryChargeDetails', 'iOrderId', $iOrderId, '', 'true');
            $Data_trip_vehicle = get_value('order_delivery_charge_details', 'tDeliveryChargeDetails', 'iOrderId', $iOrderId, '', 'true');
            $fCustomDeliveryCharge = 0;
            $Data_update_odcd['fDeliveryCharge'] = 0;
            $Data_update_odcd['iDriverVehicleTypeId'] = 0;
            if ($fCustomDeliveryChargeDetails != "") {
                $fCustomDeliveryChargeDetails = json_decode($fCustomDeliveryChargeDetails, true);
                foreach ($fCustomDeliveryChargeDetails as $dcDetails) {
                    if ($dcDetails['iVehicleTypeId'] == $vCarType) {
                        $fDeliveryCharge = $dcDetails['fDeliveryChargeCancelled'];
                        $Data_update_odcd['fDeliveryCharge'] = $dcDetails['fDeliveryChargeCancelled'];
                        $Data_update_odcd['iDriverVehicleTypeId'] = $dcDetails['iVehicleTypeId'];
                    }
                }
            }
            $odcdwhere = " iOrderId = '" . $iOrderId . "'";
            $obj->MySQLQueryPerform("order_delivery_charge_details", $Data_update_odcd, 'update', $odcdwhere);
        }
        if ($MODULES_OBJ->isEnableAnywhereDeliveryFeature() && $db_order[0]['eBuyAnyService'] == "Yes") {
            $deliveryCharge = get_value('orders', 'fDeliveryChargeCancelled', 'iOrderId', $iOrderId, '', 'true');
            $fVehicleTypeCommision = $vehicleTypeData[0]['fCommision'];
            $orderCommission = round((($deliveryCharge * $fVehicleTypeCommision) / 100), 2);
            $Data_update_order['fCommision'] = $orderCommission;
            /*Add Company Id*/
            $DriverCompanyData = getDriverCompany($iDriverId);
            $Data_update_order['vDriverCompanyId'] = $DriverCompanyData['vDriverCompanyId'];
            /*Add Company Id*/
            $where_ord = " iOrderId = '" . $iOrderId . "'";
            $obj->MySQLQueryPerform("orders", $Data_update_order, 'update', $where_ord);
            $fDeliveryCharge = $deliveryCharge - $orderCommission;

        }
        $Data_trips['iOrderId'] = $iOrderId;
        $Data_trips['fDeliveryCharge'] = $fDeliveryCharge;
        $Data_trips['vRideNo'] = $TripRideNO;
        $Data_trips['iUserId'] = $iUserId;
        $Data_trips['iDriverId'] = $iDriverId;
        $Data_trips['iCompanyId'] = $iCompanyId;
        $Data_trips['iServiceId'] = $iServiceId;
        $Data_trips['tTripRequestDate'] = @date("Y-m-d H:i:s");
        $Data_trips['iDriverVehicleId'] = $CAR_id_driver;
        $Data_trips['tStartLat'] = $Source_point_latitude;
        $Data_trips['tStartLong'] = $Source_point_longitude;
        $Data_trips['tSaddress'] = $Source_point_Address;
        $Data_trips['tEndLat'] = $Dest_point_latitude;
        $Data_trips['tEndLong'] = $Dest_point_longitude;
        $Data_trips['tDaddress'] = $Dest_point_Address;
        $Data_trips['iActive'] = $Active;
        $Data_trips['iVerificationCode'] = $TripVerificationCode;
        $Data_trips['iVehicleTypeId'] = $vCarType;
        $Data_trips['vTripPaymentMode'] = $db_order[0]['ePaymentOption'];
        $Data_trips['fTripGenerateFare'] = $db_order[0]['fNetTotal'];
        $Data_trips['vCountryUnitRider'] = getMemberCountryUnit($iUserId, "Passenger");
        $Data_trips['vCountryUnitDriver'] = getMemberCountryUnit($iDriverId, "Driver");
        $Data_trips['vTimeZone'] = $vTimeZone;
        $Data_trips['iUserAddressId'] = $iUserAddressId;
        $Data_trips['eSystem'] = "DeliverAll";
        $Data_trips['eFareType'] = $vehicleTypeData[0]['eFareType'];
        /*new added*/
        $Data_trips['fRoundingAmount'] = $db_order[0]['fRoundingAmount'];;
        $Data_trips['eRoundingType'] = $db_order[0]['eRoundingType'];;
        //added by sk on 15-11-2019 for rounding off start
        $sqlp = "SELECT cu.vName, cu.iCurrencyId, cu.eRoundingOffEnable, cu.Ratio FROM register_user AS ru LEFT JOIN currency AS cu ON ru.vCurrencyPassenger = cu.vName WHERE ru.iUserId = '" . $passenger_id . "'";
        $currData = $obj->MySQLSelect($sqlp);
        $vCurrency = $currData[0]['vName'];
        $sqld = "SELECT cu.vName, cu.iCurrencyId, cu.eRoundingOffEnable FROM register_driver AS rd LEFT JOIN currency AS cu ON rd.vCurrencyDriver = cu.vName WHERE rd.iDriverId = '" . $iDriverId . "'";
        $currDatad = $obj->MySQLSelect($sqld);
        $vCurrencyd = $currDatad[0]['vName'];
        $DriverRation = get_value('currency', 'Ratio', 'vName', $vCurrencyd, '', 'true');
        if ($db_order[0]['ePaymentOption'] == "Cash" && $MODULES_OBJ->isEnableRoundingMethod()) {
            if ($currData[0]['eRoundingOffEnable'] == "No" || $currDatad[0]['eRoundingOffEnable'] == "Yes") {
                $roundingOffTotal_fare_amountArr_driver = getRoundingOffAmount($db_order[0]['fNetTotal'] * $DriverRation, $vCurrencyd);
                if (isset($roundingOffTotal_fare_amountArr_driver['method']) && $roundingOffTotal_fare_amountArr_driver['method'] == "Addition") {
                    $eRoundingTypeDriver = "Addition";
                }
                else {
                    $eRoundingTypeDriver = "Substraction";
                }
                $fRoundingAmountDriver = isset($roundingOffTotal_fare_amountArr_driver['differenceValue']) ? setTwoDecimalPoint($roundingOffTotal_fare_amountArr_driver['differenceValue']) : 0;
                $Data_trips['fRoundingAmountDriver'] = $fRoundingAmountDriver;
                $Data_trips['eRoundingTypeDriver'] = $eRoundingTypeDriver;
            }
        }
        // payment method 2
        $Data_trips['ePayWallet'] = $ePayWallet;
        $Data_trips['fWalletDebit'] = $fWalletDebit;
        //payment method 2
        $currencyList = get_value('currency', '*', 'eStatus', 'Active');
        for ($i = 0; $i < count($currencyList); $i++) {
            $currencyCode = $currencyList[$i]['vName'];
            $Data_trips['fRatio_' . $currencyCode] = $currencyList[$i]['Ratio'];
        }
        $Data_trips['vCurrencyPassenger'] = $Data_passenger_detail[0]['vCurrencyPassenger'];
        $Data_trips['vCurrencyDriver'] = $Data_vehicle[0]['vCurrencyDriver'];
        /*if (isset($iOrderId) && !empty($iOrderId)) {
            $orderRequest = $obj->MySQLSelect("SELECT COUNT(orderRequestId) as count , iDriverId  FROM `order_request` WHERE 1=1 AND iOrderId = '" . $iOrderId . "' AND vMsgCode = '" . $vMsgCode . "'  AND eStatus = 'Accept'");
            if ($orderRequest[0]['count'] > 0 && $orderRequest[0]['iDriverId'] != $iDriverId )  {
                $returnArr['Action'] = "0";
                 $returnArr['Actio2n'] = "0";
                 $returnArr['message'] = "LBL_SAME_ORDER_TRIP_EXIST_TXT";
                setDataResponse($returnArr);
            }
        }*/
        $id = $obj->MySQLQueryPerform("trips", $Data_trips, 'insert');
        $iTripId = $id;
        $trip_status = "Active";
        // ### Update Driver Request Status of Trip ####
        UpdateDriverRequest2($iDriverId, $iUserId, $iTripId, "Accept", $vMsgCode, "No", $iOrderId);
        // ### Update Driver Request Status of Trip ####
        $where = " iUserId = '$iUserId'";
        /* $Data_update_passenger['iTripId'] = $iTripId;
          $Data_update_passenger['vTripStatus'] = $trip_status;
          $id = $obj->MySQLQueryPerform("register_user", $Data_update_passenger, 'update', $where); */
        $vRideCountry = "";
        $where = " iDriverId = '$iDriverId'";
        $Data_update_driver['iTripId'] = $iTripId;
        $Data_update_driver['vTripStatus'] = $trip_status;
        $Data_update_driver['vRideCountry'] = $vRideCountry;
        $Data_update_driver['vAvailability'] = "Not Available";
        $id = $obj->MySQLQueryPerform("register_driver", $Data_update_driver, 'update', $where);
        //update insurance log
        if (strtoupper($PACKAGE_TYPE) == "SHARK") {
            $details_arr['iTripId'] = $iTripId;
            $details_arr['LatLngArr']['vLatitude'] = $vLatitude;
            $details_arr['LatLngArr']['vLongitude'] = $vLongitude;
            // $details_arr['LatLngArr']['vLocation'] = $Source_point_Address;
            update_driver_insurance_status($iDriverId, "Accept", $details_arr, "GenerateTrip");
        }
        //update insurance log
        /* if($eType == "Deliver"){
          $drivername = $Data_vehicle[0]['vName']." ".$Data_vehicle[0]['vLastName'];
          $tripdriverarrivlbl = $languageLabelsArr['LBL_DELIVERY_DRIVER_TXT']." ".$drivername." ".$languageLabelsArr['LBL_DRIVER_IS_ARRIVING'];
        } */
        $drivername = $Data_vehicle[0]['vName'] . " " . $Data_vehicle[0]['vLastName'];
        $tripdriverarrivlbl = $languageLabelsArr['LBL_DELIVERY_EXECUTIVE_TXT'] . " " . $drivername . " " . $languageLabelsArr['LBL_DELIVERY_ON_WAY_TXT'] . " #" . $vOrderNo;
        $alertMsg = $tripdriverarrivlbl;
        $message_arr = array();
        $message_arr['iDriverId'] = $iDriverId;
        $message_arr['Message'] = $DriverMessage;
        $message_arr['iTripId'] = strval($iTripId);
        $message_arr['DriverAppVersion'] = strval($Data_vehicle[0]['iAppVersion']);
        $message_arr['iTripVerificationCode'] = $TripVerificationCode;
        $message_arr['driverName'] = $Data_vehicle[0]['vName'] . " " . $Data_vehicle[0]['vLastName'];
        $message_arr['vRideNo'] = $TripRideNO;
        $message_arr['iOrderId'] = $iOrderId;
        $message_arr['vTitle'] = $alertMsg;
        $message_arr['eSystem'] = "DeliverAll";
        //added by SP on 02-02-2021 for custom notification
        $message_arr['CustomNotification'] = $MODULES_OBJ->isEnableCustomNotification() ? "Yes" : "No";
        //these two btn CustomViewBtn,CustomTrackDetails whether shown in app or not
        $message_arr['CustomViewBtn'] = "Yes";
        $message_arr['CustomTrackDetails'] = "Yes";
        $message_arr['LBL_VIEW_DETAILS'] = $languageLabelsArr['LBL_VIEW_DETAILS'];
        $message_arr['LBL_TRACK_ORDER'] = $languageLabelsArr['LBL_TRACK_ORDER'];
        $customNotiArray = GetCustomNotificationDetails($iOrderId, $message_arr, $vLangCode);
        //title and sub description shown in custom notification
        $message_arr['CustomTitle'] = $customNotiArray[0]['vCurrentStatus'];
        $message_arr['CustomSubTitle'] = $customNotiArray[0]['vCurrentStatus_Track'];
        $message_arr['CustomMessage'] = $customNotiArray;
        $message = json_encode($message_arr);
        $order_driver_log = array();
        if ($MODULES_OBJ->isEnableCancelDriverOrder()) {
            $Data_order1['eCancelledbyDriver'] = "No";
            $where_ord = " iOrderId = '" . $iOrderId . "'";
            $obj->MySQLQueryPerform("orders", $Data_order1, 'update', $where_ord);
            $order_driver_log_q = "SELECT * FROM `order_driver_log` WHERE iOrderId = '" . $iOrderId . "'";
            $order_driver_log = $obj->MySQLSelect($order_driver_log_q);
        }
        if ($iTripId > 0) {
            /* For PubNub Setting */
            $tableName = "register_user";
            $iMemberId_VALUE = $iUserId;
            $iMemberId_KEY = "iUserId";
            /* $iAppVersion=get_value($tableName, 'iAppVersion', $iMemberId_KEY,$iMemberId_VALUE,'','true');
            $eDeviceType=get_value($tableName, 'eDeviceType', $iMemberId_KEY,$iMemberId_VALUE,'','true'); */
            $AppData = get_value($tableName, 'iAppVersion,eDeviceType', $iMemberId_KEY, $iMemberId_VALUE);
            $iAppVersion = $AppData[0]['iAppVersion'];
            $eDeviceType = $AppData[0]['eDeviceType'];
            /* For PubNub Setting Finished */
            $sql = "SELECT iGcmRegId,eDeviceType,tSessionId,eAppTerminate,eDebugMode,eHmsDevice FROM register_user WHERE iUserId='$iUserId'";
            $result = $obj->MySQLSelect($sql);
            $message_arr['tSessionId'] = $result[0]['tSessionId'];
            $channelName = "PASSENGER_" . $iUserId;
            $generalDataArr[] = array(
                'eDeviceType' => $result[0]['eDeviceType'], 'deviceToken' => $result[0]['iGcmRegId'], 'alertMsg' => $alertMsg, 'eAppTerminate' => $result[0]['eAppTerminate'], 'eDebugMode' => $result[0]['eDebugMode'], 'eHmsDevice' => $result[0]['eHmsDevice'], 'message' => $message_arr, 'channelName' => $channelName, 'tripStatusMsgArr' => array(
                    'tMessage' => $message_arr, 'iDriverId' => $iDriverId, 'iTripId' => $iTripId, 'iUserId' => $iUserId, 'eFromUserType' => "Driver", 'eToUserType' => "Passenger", 'eReceived' => "No", 'iOrderId' => $iOrderId
                )
            );
            if (count($order_driver_log) == 0) {
                $EVENT_MSG_OBJ->send(array('GENERAL_DATA' => $generalDataArr), RN_USER);
            }
            $sql = "SELECT iGcmRegId,eDeviceType,iAppVersion,tSessionId,eAppTerminate,eDebugMode,eHmsDevice FROM company WHERE iCompanyId='$iCompanyId'";
            $result_company = $obj->MySQLSelect($sql);
            $message_arr['tSessionId'] = $result_company[0]['tSessionId'];
            $channelName_company = "COMPANY_" . $iCompanyId;
            $generalDataArr = array();
            $generalDataArr[] = array(
                'eDeviceType' => $result_company[0]['eDeviceType'], 'deviceToken' => $result_company[0]['iGcmRegId'], 'alertMsg' => $alertMsg, 'eAppTerminate' => $result_company[0]['eAppTerminate'], 'eDebugMode' => $result_company[0]['eDebugMode'], 'eHmsDevice' => $result_company[0]['eHmsDevice'], 'message' => $message_arr, 'channelName' => $channelName_company, 'orderEventChannelName' => $orderEventChannelName
            );
            if (count($order_driver_log) == 0) {
                $EVENT_MSG_OBJ->send(array('GENERAL_DATA' => $generalDataArr), RN_COMPANY);
            }

            $Data_activity = array();
            $Data_activity['iOrderId'] = $iOrderId;
            $Data_activity['vLatitude'] = $vLatitude;
            $Data_activity['vLongitude'] = $vLongitude;
            $obj->MySQLQueryPerform("trip_activity_details", $Data_activity, "insert");

            $returnArr['Action'] = "1";
            $data['iTripId'] = $iTripId;
            $data['tEndLat'] = $Dest_point_latitude;
            $data['tEndLong'] = $Dest_point_longitude;
            $data['tDaddress'] = $Dest_point_Address;
            $data['PAppVersion'] = $Data_passenger_detail[0]['iAppVersion'];
            $data['eFareType'] = $Data_trips['eFareType'];
            $data['vVehicleType'] = $vehicleTypeData[0]['eIconType'];
            $returnArr['APP_TYPE'] = $APP_TYPE;
            $returnArr['message'] = $data;
            if ($iOrderId != "") {
                $passengerData = get_value('register_user', 'vName,vLastName,vImgName,vFbId,vAvgRating,vPhone,vPhoneCode,iAppVersion', 'iUserId', $iUserId);
                $returnArr['sourceLatitude'] = $Source_point_latitude;
                $returnArr['sourceLongitude'] = $Source_point_longitude;
                $returnArr['PassengerId'] = $iUserId;
                $returnArr['PName'] = $passengerData[0]['vName'] . ' ' . $passengerData[0]['vLastName'];
                $returnArr['PPicName'] = $passengerData[0]['vImgName'];
                $returnArr['PFId'] = $passengerData[0]['vFbId'];
                $returnArr['PRating'] = $passengerData[0]['vAvgRating'];
                $returnArr['PPhone'] = $passengerData[0]['vPhone'];
                $returnArr['PPhoneC'] = $passengerData[0]['vPhoneCode'];
                $returnArr['PAppVersion'] = $passengerData[0]['iAppVersion'];
                $returnArr['TripId'] = strval($iTripId);
                $returnArr['DestLocLatitude'] = $Dest_point_latitude;
                $returnArr['DestLocLongitude'] = $Dest_point_longitude;
                $returnArr['DestLocAddress'] = $Dest_point_Address;
                $returnArr['vVehicleType'] = $vehicleTypeData[0]['eIconType'];
            }
            //unLinkFile($driverLockFilePath);
            $returnArr['USER_DATA'] = getDriverDetailInfo($iDriverId);
            setDataResponse($returnArr);
        }
        else {
            $data['Action'] = "0";
            $data['message'] = "LBL_TRY_AGAIN_LATER_TXT";
            //unLinkFile($driverLockFilePath);
            setDataResponse($data);
        }
    }
    else {
        if ($eStatus == "Complete") {
            $returnArr['Action'] = "0";
            $returnArr['message'] = "LBL_FAIL_ASSIGN_TO_PASSENGER_TXT";
        }
        else if ($eStatus == "Cancelled") {
            $returnArr['Action'] = "0";
            $returnArr['message'] = "LBL_CAR_REQUEST_CANCELLED_TXT_DL";
        }
        //unLinkFile($driverLockFilePath);
        setDataResponse($returnArr);
    }
}
// ##########################################################################
// ##########################################################################
if ($type == "loadDriverFeedBack") {
    //global $tconfig; // Commented By HJ On 15-07-2020 Bcoz Not Required
    $page = isset($_REQUEST['page']) ? trim($_REQUEST['page']) : 1;
    $iDriverId = isset($_REQUEST["iDriverId"]) ? $_REQUEST["iDriverId"] : '';
    $UserType = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : 'Passenger';
    $vAvgRating = get_value('register_driver', 'vAvgRating', 'iDriverId', $iDriverId, '', 'true');
    $per_page = 10;
    $sql_all = "SELECT COUNT(o.iOrderId) As TotalIds FROM orders as o LEFT JOIN ratings_user_driver as rate on rate.iOrderId = o.iOrderId WHERE o.iDriverId='$iDriverId' AND o.iStatusCode='6' AND rate.eToUserType = '" . $UserType . "' AND rate.vRating1 != '' AND rate.eStatus != 'Deleted'";
    $data_count_all = $obj->MySQLSelect($sql_all);
    $TotalPages = ceil($data_count_all[0]['TotalIds'] / $per_page);
    $start_limit = ($page - 1) * $per_page;
    $limit = " LIMIT " . $start_limit . ", " . $per_page;
    // $sql = "SELECT rate.*,CONCAT(ru.vName,' ',ru.vLastName) as vName,ru.iUserId as passengerid,ru.vImgName FROM ratings_user_driver as rate LEFT JOIN orders as o ON o.iOrderId = rate.iOrderId  LEFT JOIN register_user as ru ON ru.iUserId = o.iUserId WHERE o.iDriverId='$iDriverId' AND o.iStatusCode='6' AND rate.eToUserType = '" . $UserType . "' ORDER BY o.iOrderId DESC" . $limit;
    $sql = "SELECT rate.*,CONCAT(ru.vName,' ',ru.vLastName) as vName,ru.iUserId as passengerid,ru.vImgName FROM ratings_user_driver as rate LEFT JOIN orders as o ON o.iOrderId = rate.iOrderId  LEFT JOIN register_user as ru ON ru.iUserId = o.iUserId WHERE o.iDriverId='$iDriverId' AND o.iStatusCode='6' AND rate.eToUserType = '" . $UserType . "' AND rate.vRating1 != '' AND rate.eStatus != 'Deleted' ORDER BY rate.iRatingId DESC" . $limit;
    $Data = $obj->MySQLSelect($sql);
    for ($i = 0; $i < count($Data); $i++) {
        $Data[$i]['vImage'] = $tconfig["tsite_upload_images_passenger"] . '/' . $Data[$i]['passengerid'] . '/3_' . $Data[$i]['vImgName'];
        $Data[$i]['tDateOrig'] = $Data[$i]['tDate'];
        $Data[$i]['tDate'] = DateTime($Data[$i]['tDate'], 14);
    }
    $totalNum = count($Data);
    if (count($Data) > 0) {
        $returnData['message'] = $Data;
        if ($TotalPages > $page) {
            $returnData['NextPage'] = $page + 1;
        }
        else {
            $returnData['NextPage'] = "0";
        }
        $returnData['vAvgRating'] = strval($vAvgRating);
        $returnData['Action'] = "1";
        setDataResponse($returnData);
    }
    else {
        $returnData['Action'] = "0";
        $returnData['message'] = "LBL_NO_FEEDBACK";
        setDataResponse($returnData);
    }
}

// ########################### UBER-For-X ################################
/*if ($type == "getServiceCategories") {
    $parentId = isset($_REQUEST['parentId']) ? clean($_REQUEST['parentId']) : 0;
    $userId = isset($_REQUEST['userId']) ? clean($_REQUEST['userId']) : '';
    if ($userId > 0) {
        //Added By HJ On 21-07-2020 For Optimize register_user Table Query Start
        if (isset($userDetailsArr['register_user_' . $userId])) {
            $row = $userDetailsArr['register_user_' . $userId];
        }
        else {
            $row = $obj->MySQLSelect("SELECT *,iUserId as iMemberId FROM `register_user` WHERE iUserId='" . $userId . "'");
            $userDetailsArr['register_user_' . $userId] = $row;
        }
        //Added By HJ On 21-07-2020 For Optimize register_user Table Query End
        //$row = $obj->MySQLSelect("SELECT vLang FROM `register_user` WHERE iUserId='".$userId."'");
        $lang = $row[0]['vLang'];
    }
    if ($lang == "") {
        //Added By HJ On 15-07-2020 For Optimize language_master Table Query Start
        $lang = $LANG_OBJ->FetchDefaultLangData("vCode");
        //Added By HJ On 15-07-2020 For Optimize language_master Table Query End
    }
    if ($parentId == "" || $parentId == NULL) {
        $parentId = 0;
    }
    $ssql = "";
    if (!$MODULES_OBJ->isEnableAnywhereDeliveryFeature()) {
        $ssql .= " AND eCatType!='Genie' AND eCatType!='Runner' AND eCatType!='Anywhere'";
    }
    $sql_vehicle_category_table_name = getVehicleCategoryTblName();
    $Data = $obj->MySQLSelect("SELECT iVehicleCategoryId, vLogo,vCategory_" . $lang . " as vCategory,eStatus FROM " . $sql_vehicle_category_table_name . " WHERE eStatus='Active' AND iParentId='" . $parentId . "' $ssql");
    //Added By HJ On 11-07-2019 For Get All Vehicle Type For Optimized Code Start
    $Data3 = $obj->MySQLSelect("SELECT iVehicleTypeId,iVehicleCategoryId FROM vehicle_type WHERE eStatus='Active' ORDER BY iDisplayOrder ASC");
    $categoryArr = $Datacategory = array();
    for ($vc = 0; $vc < count($Data3); $vc++) {
        $categoryArr[$Data3[$vc]['iVehicleCategoryId']][] = $Data3[$vc];
    }
    //Added By HJ On 11-07-2019 For Get All Vehicle Type For Optimized Code End
    $Datacategory = array();
    if ($parentId == 0) {
        if (count($Data) > 0) {
            $k = 0;
            //Added By HJ On 21-07-2020 For Optimize vehicle_category Table Query Start
            $Data2 = $obj->MySQLSelect("SELECT iParentId,iVehicleCategoryId, vLogo,vCategory_" . $lang . " as vCategory FROM " . $sql_vehicle_category_table_name . " WHERE eStatus='Active' ORDER BY iDisplayOrder ASC");
            for ($h = 0; $h < count($Data2); $h++) {
                $vehicleCatArr[$Data2[$h]['iParentId']][] = $Data2[$h];
            }
            //Added By HJ On 21-07-2020 For Optimize vehicle_category Table Query End
            for ($i = 0; $i < count($Data); $i++) {
                //Added By HJ On 21-07-2020 For Optimize vehicle_category Table Query Start
                $Data2 = array();
                if (isset($vehicleCatArr[$Data[$i]['iVehicleCategoryId']])) {
                    $Data2 = $vehicleCatArr[$Data[$i]['iVehicleCategoryId']];
                }
                //Added By HJ On 21-07-2020 For Optimize vehicle_category Table Query End
                if (count($Data2) > 0) {
                    for ($j = 0; $j < count($Data2); $j++) {
                        //Removed By HJ On 11-07-2019 For Get All Vehicle Type For Optimized Code Start
                        //$sql4 = "SELECT iVehicleTypeId FROM vehicle_type WHERE eStatus='Active' AND iVehicleCategoryId='" . $Data2[$j]['iVehicleCategoryId'] . "'";
                        //$Data3 = $obj->MySQLSelect($sql4);
                        //Removed By HJ On 11-07-2019 For Get All Vehicle Type For Optimized Code End
                        //Added By HJ On 11-07-2019 For Get All Vehicle Type For Optimized Code Start
                        $Data3 = array();
                        if (isset($categoryArr[$Data2[$j]['iVehicleCategoryId']])) {
                            $Data3 = $categoryArr[$Data2[$j]['iVehicleCategoryId']];
                        }
                        //Added By HJ On 11-07-2019 For Get All Vehicle Type For Optimized Code End
                        if (count($Data3) > 0) {
                            $Datacategory[$k]['iVehicleCategoryId'] = $Data[$i]['iVehicleCategoryId'];
                            $Datacategory[$k]['vLogo'] = $Data[$i]['vLogo'];
                            $Datacategory[$k]['vLogo_image'] = $tconfig['tsite_upload_images_vehicle_category'] . '/' . $Data[$i]['iVehicleCategoryId'] . '/android/' . $Data[$i]['vLogo'];
                            $Datacategory[$k]['vCategory'] = $Data[$i]['vCategory'];
                            $Datacategory[$k]['eStatus'] = $Data[$i]['eStatus'];
                            $k++;
                        }
                    }
                }
            }
        }
    }
    else {
        if (count($Data) > 0) {
            $k = 0;
            for ($j = 0; $j < count($Data); $j++) {
                //Removed By HJ On 11-07-2019 For Get All Vehicle Type For Optimized Code Start
                //$sql4 = "SELECT iVehicleTypeId FROM vehicle_type WHERE eStatus='Active' AND iVehicleCategoryId='" . $Data[$j]['iVehicleCategoryId'] . "'";
                //$Data3 = $obj->MySQLSelect($sql4);
                //Removed By HJ On 11-07-2019 For Get All Vehicle Type For Optimized Code End
                //Added By HJ On 11-07-2019 For Get All Vehicle Type For Optimized Code Start
                $Data3 = array();
                if (isset($categoryArr[$Data[$j]['iVehicleCategoryId']])) {
                    $Data3 = $categoryArr[$Data[$j]['iVehicleCategoryId']];
                }
                //Added By HJ On 11-07-2019 For Get All Vehicle Type For Optimized Code End
                if (count($Data3) > 0) {
                    $Datacategory[$k]['iVehicleCategoryId'] = $Data[$j]['iVehicleCategoryId'];
                    $Datacategory[$k]['vLogo'] = $Data[$j]['vLogo'];
                    $Datacategory[$k]['vLogo_image'] = $tconfig['tsite_upload_images_vehicle_category'] . '/' . $Data[$j]['iVehicleCategoryId'] . '/android/' . $Data[$j]['vLogo'];
                    $Datacategory[$k]['vCategory'] = $Data[$j]['vCategory'];
                    $Datacategory[$k]['eStatus'] = $Data[$j]['eStatus'];
                    $k++;
                }
            }
        }
    }
    $Datacategory1 = array_unique($Datacategory, SORT_REGULAR);
    $DatanewArr = array();
    foreach ($Datacategory1 as $inner) {
        if ($inner['eCatType'] == "Genie") {
            $inner['eCatType'] = "Anywhere";
        }
        array_push($DatanewArr, $inner);
    }
    $returnArr['Action'] = "1";
    $returnArr['message'] = array_reverse($DatanewArr);
    setDataResponse($returnArr);
} */

if ($type == "getServiceCategories") {
    $parentId = isset($_REQUEST['parentId']) ? clean($_REQUEST['parentId']) : 0;
    $userId = isset($_REQUEST['userId']) ? clean($_REQUEST['userId']) : '';
    $vGeneralLang = isset($_REQUEST["vGeneralLang"]) ? $_REQUEST["vGeneralLang"] : '';
    $bookingFrom = isset($_REQUEST["bookingFrom"]) ? $_REQUEST["bookingFrom"] : 'App';//added by SP for manual booking for admin side not check user id.

    $eCatType = isset($_REQUEST["eCatType"]) ? $_REQUEST["eCatType"] : '';
    $eForVideoConsultation = isset($_REQUEST["eForVideoConsultation"]) ? $_REQUEST["eForVideoConsultation"] : 'No';

    $sql_vehicle_category_table_name = getVehicleCategoryTblName();

    if ($userId != "" || $bookingFrom == 'Web') {
        $lang = $vGeneralLang;
        //Added By HJ On 07-07-2020 For Optimize register_user Table Query Start
        if (isset($userDetailsArr['register_user_' . $userId])) {
            $row = $userDetailsArr['register_user_' . $userId];
        } else {
            $row = $obj->MySQLSelect("SELECT *,iUserId as iMemberId FROM `register_user` WHERE iUserId='$userId'");
            $userDetailsArr['register_user_' . $userId] = $row;
        }
        //Added By HJ On 07-07-2020 For Optimize register_user Table Query End
        $lang = $row[0]['vLang'];
    }
    
    if ($lang == "" || $lang == NULL) {
        //Added By HJ On 24-06-2020 For Optimize language_master Table Query Start
        $lang = $LANG_OBJ->FetchDefaultLangData("vCode");
        //Added By HJ On 24-06-2020 For Optimize language_master Table Query End
    }

    $vehicle_category_main = get_value($sql_vehicle_category_table_name, 'vCategory_' . $lang, 'iVehicleCategoryId', $parentId, '', 'true');
    $ssql = $ssql1 = $ssql2 = $ssql3 = '';
    //$ufxEnable = $MODULES_OBJ->isUfxFeatureAvailable(); // Commented By HJ On 04-06-2020 For Optimized Query Below Line
    $ufxEnable = $isUfxAvailable; // Added By HJ On 04-06-2020 For Optimized Query
    if ($APP_TYPE == "UberX") {
        $ssql = " AND eCatType='ServiceProvider'";

        if ($parent_ufx_catid > 0 && $parentId == 0) {
            $ssql .= " AND iVehicleCategoryId='" . $parent_ufx_catid . "'";
        }
    }
    if ($THEME_OBJ->isCubexThemeActive() == 'Yes' || $THEME_OBJ->isCubeXv2ThemeActive() == 'Yes') {
        $ssql1 = $ssql2 = $ssql3 = $eCatTypeQuery = '';
        if ($ufxEnable != 'Yes') {
            $ssql1 .= " AND eCatType!='ServiceProvider'";
        } else {
            $ssql2 .= " OR eCatType='ServiceProvider'";
        }

        if ($THEME_OBJ->isCubexThemeActive() == 'Yes') {
            $eCatTypeQuery = " ('Ride', 'MotoRide', 'Fly', 'Donation') ";
        } elseif ($THEME_OBJ->isCubeXv2ThemeActive() == 'Yes') {
            $eCatTypeQuery = " ('Ride', 'MotoRide', 'Rental', 'MotoRental', 'Fly', 'Donation', 'RidePool') ";
        }
        $ssql .= " AND (iServiceId IN ($enablesevicescategory) OR eCatType IN $eCatTypeQuery OR (eFor = 'DeliveryCategory' AND eCatType = 'MoreDelivery')  $ssql2 )  $ssql1";
    }
    if ($THEME_OBJ->isCubeJekXThemeActive() == 'Yes' || !$MODULES_OBJ->isUberXFeatureAvailable()) {
        if ($ufxEnable != 'Yes') {
            $ssql .= " AND eCatType!='ServiceProvider'";
        }
    }
    if (!$MODULES_OBJ->isAirFlightModuleAvailable()) {
        $ssql .= " AND eCatType!='Fly'";
    }
    if (!$MODULES_OBJ->isEnableAnywhereDeliveryFeature()) {
        $ssql .= " AND eCatType!='Genie' AND eCatType!='Runner' AND eCatType!='Anywhere' ";
    } elseif ($MODULES_OBJ->isEnableAnywhereDeliveryFeature() && $THEME_OBJ->isCubeXv2ThemeActive() == 'Yes') {
        $ssql .= " OR eCatType IN ('Genie', 'Runner', 'Anywhere') ";
    }
    if (!$MODULES_OBJ->isDeliverAllFeatureAvailable()) {
        $ssql .= " AND eCatType != 'DeliverAll' AND (eCatType != 'MoreDelivery' AND eFor != 'DeliverAllCategory')";
    }
    if ($MODULES_OBJ->isDonationFeatureAvailable()) {
        $sql2 = "SELECT iVehicleCategoryId, vLogo, eShowType,vBannerImage, iDisplayOrder,vCategory_" . $lang . " as vCategory, eCatType, eSubCatType, tBannerButtonText,iServiceId, eDeliveryType,vListLogo,eCatViewType,tListDescription,vListLogo1,vListLogo2,vListLogo3,eFor,vIconDetails FROM " . $sql_vehicle_category_table_name . " WHERE eStatus='Active' AND iParentId='$parentId' " . $ssql . " AND iServiceId > '0' ORDER BY iDisplayOrder,iVehicleCategoryId ASC";
    } else {
        $sql2 = "SELECT iVehicleCategoryId, vLogo, eShowType,vBannerImage, iDisplayOrder,vCategory_" . $lang . " as vCategory, eCatType, eSubCatType, tBannerButtonText,iServiceId, eDeliveryType,vListLogo,eCatViewType,tListDescription,vListLogo1,vListLogo2,vListLogo3,eFor,vIconDetails FROM " . $sql_vehicle_category_table_name . " WHERE eStatus='Active' AND eCatType!='Donation' AND iParentId='$parentId' " . $ssql . "  AND iServiceId > '0' ORDER BY iDisplayOrder,iVehicleCategoryId ASC";
    }

    $Data = $obj->MySQLSelect($sql2);
    
    $categoryArr = $Datacategory = array();

    $eShowTerms = "No";
    $eProofUpload = "No";
    $tProofNote = "";
    $deliverAll_serviceArr = array();
    if (strtoupper(DELIVERALL) == "YES") {
        $scsql = "select iServiceId,eShowTerms,eProofUpload,JSON_UNQUOTE(JSON_VALUE(tProofNote, '$.tProofNote_" . $lang . "')) as tProofNote from service_categories";
        $scsqlData = $obj->MySQLSelect($scsql);
        foreach ($scsqlData as $scValue) {
            if ($MODULES_OBJ->isEnableTermsServiceCategories()) {
                $eShowTerms = $scValue['eShowTerms'];
            }
            if ($MODULES_OBJ->isEnableProofUploadServiceCategories()) {
                $eProofUpload = $scValue['eProofUpload'];
                if (is_null($scValue['tProofNote']) || $scValue['tProofNote'] == "null") {
                    $scValue['tProofNote'] = "";
                }
                $tProofNote = $scValue['tProofNote'];
            }

            $deliverAll_serviceArr[$scValue['iServiceId']]['eShowTerms'] = $eShowTerms;
            $deliverAll_serviceArr[$scValue['iServiceId']]['eProofUpload'] = $eProofUpload;
            $deliverAll_serviceArr[$scValue['iServiceId']]['tProofNote'] = (!empty($tProofNote) && $tProofNote != NULL) ? $tProofNote : "";
        }
    }
    //Added By HJ On 07-07-2020 For Optimize vehicle_type Table Query Start
    $vehicleCatArr = $vehicleTypeArr = array();
    $vehicleTypeData = $obj->MySQLSelect("SELECT iVehicleTypeId,iVehicleCategoryId FROM vehicle_type WHERE eStatus='Active'");
    for ($g = 0; $g < count($vehicleTypeData); $g++) {
        $vehicleTypeArr[$vehicleTypeData[$g]['iVehicleCategoryId']][] = $vehicleTypeData[$g];
    }
    //Added By HJ On 07-07-2020 For Optimize vehicle_type Table Query End


    if (count($Data) > 0) {
        $k = 0;
        //Added By HJ On 07-07-2020 For Optimize vehicle_category Table Query Start
        $Data2 = $obj->MySQLSelect("SELECT iParentId,iVehicleCategoryId, vLogo, eShowType,vBannerImage, vCategory_" . $lang . " as vCategory, eCatType, eSubCatType, tBannerButtonText, iServiceId, eDeliveryType,vListLogo,eCatViewType,tListDescription,vListLogo1,vListLogo2,eFor,vIconDetails FROM " . $sql_vehicle_category_table_name . " WHERE eStatus='Active' ORDER BY iDisplayOrder ASC");

        for ($h = 0; $h < count($Data2); $h++) {
            $vehicleCatArr[$Data2[$h]['iParentId']][] = $Data2[$h];
        }
        //Added By HJ On 07-07-2020 For Optimize vehicle_category Table Query End

        for ($i = 0; $i < count($Data); $i++) {
            $BannerButtonText = "tBannerButtonText_" . $lang;
            $tBannerButtonTextArr = json_decode($Data[$i]['tBannerButtonText'], true);
            $tBannerButtonText = $tBannerButtonTextArr[$BannerButtonText];

            $listDescText = "tListDescription_" . $lang;
            $tListDescriptionArr = json_decode($Data[$i]['tListDescription'], true);
            $tListDescriptionText = $tListDescriptionArr[$listDescText];

            $PromoteBannerTitle = "tPromoteBannerTitle_" . $lang;
            $tPromoteBannerTitleArr = json_decode($Data[$i]['tPromoteBannerTitle'], true);
            $tPromoteBannerTitle = $tPromoteBannerTitleArr[$PromoteBannerTitle];

            $Data[$i]['vBgColor'] = $Data[$i]['vBorderColor'] = "";
            if(!empty($Data[$i]['vIconDetails'])) {
                $vIconDetails = json_decode($Data[$i]['vIconDetails'], true);
                $Data[$i]['vBgColor'] = $vIconDetails['vBgColor'];
                $Data[$i]['vBorderColor'] = $vIconDetails['vBorderColor'];
            }

            if ($tListDescriptionText == null) {
                $tListDescriptionText = "";
            }
            if ($tPromoteBannerTitle == null) {
                $tPromoteBannerTitle = "";
            }

            if ($Data[$i]['eCatType'] == "ServiceProvider" || $Data[$i]['eCatType'] == "MoreDelivery") {
                //Added By HJ On 07-07-2020 For Optimize vehicle_category Table Query Start
                $Data2 = array();
                if (isset($vehicleCatArr[$Data[$i]['iVehicleCategoryId']])) {
                    $Data2 = $vehicleCatArr[$Data[$i]['iVehicleCategoryId']];
                }
                //Added By HJ On 07-07-2020 For Optimize vehicle_category Table Query End
                if (count($Data2) > 0) {
                    for ($j = 0; $j < count($Data2); $j++) {
                        if ($Data2[$j]['eCatType'] == "ServiceProvider") {
                            //$sql4 = "SELECT iVehicleTypeId FROM vehicle_type WHERE eStatus='Active' AND iVehicleCategoryId='" . $Data2[$j]['iVehicleCategoryId'] . "'";
                            //$Data3 = $obj->MySQLSelect($sql4);
                            //Added By HJ On 07-07-2020 For Optimize vehicle_type Table Query Start
                            $Data3 = array();
                            if (isset($vehicleTypeArr[$Data2[$j]['iVehicleCategoryId']])) {
                                $Data3 = $vehicleTypeArr[$Data2[$j]['iVehicleCategoryId']];
                            }
                            //Added By HJ On 07-07-2020 For Optimize vehicle_type Table Query End
                            if (count($Data3) > 0) {
                                $Datacategory[$k]['eCatType'] = $Data[$i]['eCatType'];
                                $Datacategory[$k]['eSubCatType'] = $Data[$i]['eSubCatType'];
                                $Datacategory[$k]['eDeliveryType'] = $Data[$i]['eDeliveryType'];
                                $Datacategory[$k]['iVehicleCategoryId'] = $Data[$i]['iVehicleCategoryId'];
                                $Datacategory[$k]['vCategory'] = $Data[$i]['vCategory'];
                                $Datacategory[$k]['vCategoryBanner'] = $Data[$i]['vCategory'];
                                $Datacategory[$k]['vLogo'] = $Data[$i]['vLogo'];
                                $Datacategory[$k]['vLogo_image'] = $tconfig['tsite_upload_images_vehicle_category'] . '/' . $Data[$i]['iVehicleCategoryId'] . '/android/' . $Data[$i]['vLogo'];
                                $Datacategory[$k]['eShowType'] = $Data[$i]['eShowType'];
                                $Datacategory[$k]['iServiceId'] = $Data[$i]['iServiceId'];
                                $Datacategory[$k]['tBannerButtonText'] = $tBannerButtonText;
                                $Datacategory[$k]['vBannerImage'] = ($Data[$i]['vBannerImage'] != "") ? $tconfig['tsite_upload_images_vehicle_category'] . '/' . $Data[$i]['iVehicleCategoryId'] . '/' . $Data[$i]['vBannerImage'] : "";
                                $Datacategory[$k]['eShowTerms'] = "No";
                                $Datacategory[$k]['eProofUpload'] = "No";
                                $Datacategory[$k]['tProofNote'] = "";
                                if (strtoupper(DELIVERALL) == "YES" && $Data[$i]['iServiceId'] > 0) {
                                    if ($MODULES_OBJ->isEnableTermsServiceCategories()) {
                                        $Datacategory[$k]['eShowTerms'] = $deliverAll_serviceArr[$Data[$i]['iServiceId']]['eShowTerms'];
                                    }
                                    if ($MODULES_OBJ->isEnableProofUploadServiceCategories()) {
                                        $Datacategory[$k]['eProofUpload'] = $deliverAll_serviceArr[$Data[$i]['iServiceId']]['eProofUpload'];
                                        $Datacategory[$k]['tProofNote'] = $deliverAll_serviceArr[$Data[$i]['iServiceId']]['tProofNote'];
                                    }
                                }
                                $Datacategory[$k]['vListLogo'] = ($Data[$i]['vListLogo'] != "") ? $tconfig['tsite_upload_images_vehicle_category'] . '/' . $Data[$i]['iVehicleCategoryId'] . '/' . $Data[$i]['vListLogo'] : "";
                                $Datacategory[$k]['eCatViewType'] = $Data[$i]['eCatViewType'];
                                $Datacategory[$k]['tListDescription'] = $tListDescriptionText;
                                if ($MODULES_OBJ->isEnableAppHomeScreenLayoutV1()) {
                                    $Datacategory[$k]['vListLogo'] = ($Data[$i]['vListLogo1'] != "") ? $tconfig['tsite_upload_images_vehicle_category'] . '/' . $Data[$i]['iVehicleCategoryId'] . '/' . $Data[$i]['vListLogo1'] : "";
                                }
                                if ($MODULES_OBJ->isEnableAppHomeScreenLayoutV2()) {
                                    $Datacategory[$k]['vListLogo'] = ($Data[$i]['vListLogo2'] != "") ? $tconfig['tsite_upload_images_vehicle_category'] . '/' . $Data[$i]['iVehicleCategoryId'] . '/' . $Data[$i]['vListLogo2'] : "";
                                    
                                    $Datacategory[$k]['vBgColor'] = $Data[$i]['vBgColor'];
                                    $Datacategory[$k]['vBorderColor'] = $Data[$i]['vBorderColor'];
                                    if($THEME_OBJ->isDeliverallXv2ThemeActive() == "Yes") {
                                        $Datacategory[$k]['vListLogo'] = ($Data[$i]['vListLogo2'] != "") ? $tconfig['tsite_upload_service_categories_images'] . $Data[$i]['vListLogo2'] : "";
                                    }
                                }
                                $Datacategory[$k]['eFor'] = $Data[$i]['eFor'];

                                $k++;
                            }
                        } else {
                            $Datacategory[$k]['eCatType'] = $Data[$i]['eCatType'];
                            $Datacategory[$k]['eSubCatType'] = $Data[$i]['eSubCatType'];
                            $Datacategory[$k]['eDeliveryType'] = $Data[$i]['eDeliveryType'];
                            $Datacategory[$k]['iVehicleCategoryId'] = $Data[$i]['iVehicleCategoryId'];
                            $Datacategory[$k]['vCategory'] = $Data[$i]['vCategory'];
                            $Datacategory[$k]['vCategoryBanner'] = $Data[$i]['vCategory'];
                            $Datacategory[$k]['vLogo'] = $Data[$i]['vLogo'];
                            $Datacategory[$k]['vLogo_image'] = $tconfig['tsite_upload_images_vehicle_category'] . '/' . $Data[$i]['iVehicleCategoryId'] . '/android/' . $Data[$i]['vLogo'];
                            $Datacategory[$k]['eShowType'] = $Data[$i]['eShowType'];
                            $Datacategory[$k]['iServiceId'] = $Data[$i]['iServiceId'];
                            $Datacategory[$k]['tBannerButtonText'] = $tBannerButtonText;
                            $Datacategory[$k]['vBannerImage'] = ($Data[$i]['vBannerImage'] != "") ? $tconfig['tsite_upload_images_vehicle_category'] . '/' . $Data[$i]['iVehicleCategoryId'] . '/' . $Data[$i]['vBannerImage'] : "";
                            $Datacategory[$k]['eShowTerms'] = "No";
                            $Datacategory[$k]['eProofUpload'] = "No";
                            $Datacategory[$k]['tProofNote'] = "";
                            if (strtoupper(DELIVERALL) == "YES" && $Data[$i]['iServiceId'] > 0) {
                                if ($MODULES_OBJ->isEnableTermsServiceCategories()) {
                                    $Datacategory[$k]['eShowTerms'] = $deliverAll_serviceArr[$Data[$i]['iServiceId']]['eShowTerms'];
                                }
                                if ($MODULES_OBJ->isEnableProofUploadServiceCategories()) {
                                    $Datacategory[$k]['eProofUpload'] = $deliverAll_serviceArr[$Data[$i]['iServiceId']]['eProofUpload'];
                                    $Datacategory[$k]['tProofNote'] = $deliverAll_serviceArr[$Data[$i]['iServiceId']]['tProofNote'];
                                }
                            }
                            $Datacategory[$k]['vListLogo'] = ($Data[$i]['vListLogo'] != "") ? $tconfig['tsite_upload_images_vehicle_category'] . '/' . $Data[$i]['iVehicleCategoryId'] . '/' . $Data[$i]['vListLogo'] : "";
                            $Datacategory[$k]['eCatViewType'] = $Data[$i]['eCatViewType'];
                            $Datacategory[$k]['tListDescription'] = $tListDescriptionText;
                            if ($MODULES_OBJ->isEnableAppHomeScreenLayoutV1()) {
                                $Datacategory[$k]['vListLogo'] = ($Data[$i]['vListLogo1'] != "") ? $tconfig['tsite_upload_images_vehicle_category'] . '/' . $Data[$i]['iVehicleCategoryId'] . '/' . $Data[$i]['vListLogo1'] : "";
                            }

                            if ($MODULES_OBJ->isEnableAppHomeScreenLayoutV2()) {
                                $Datacategory[$k]['vListLogo'] = ($Data[$i]['vListLogo2'] != "") ? $tconfig['tsite_upload_images_vehicle_category'] . '/' . $Data[$i]['iVehicleCategoryId'] . '/' . $Data[$i]['vListLogo2'] : "";
                                
                                $Datacategory[$k]['vBgColor'] = $Data[$i]['vBgColor'];
                                $Datacategory[$k]['vBorderColor'] = $Data[$i]['vBorderColor'];
                                if($THEME_OBJ->isDeliverallXv2ThemeActive() == "Yes") {
                                    $Datacategory[$k]['vListLogo'] = ($Data[$i]['vListLogo2'] != "") ? $tconfig['tsite_upload_service_categories_images'] . $Data[$i]['vListLogo2'] : "";
                                }
                            }
                            if ($MODULES_OBJ->isEnableAppHomeScreenLayoutV3()) {
                                $Datacategory[$k]['vListLogo'] = ($Data[$i]['vListLogo3'] != "") ? $tconfig['tsite_upload_images_vehicle_category'] . '/' . $Data[$i]['iVehicleCategoryId'] . '/' . $Data[$i]['vListLogo3'] : "";
                            }
                            $Datacategory[$k]['eFor'] = $Data[$i]['eFor'];

                            $k++;
                        }
                    }
                }
            } else {
                $Datacategory[$k]['eCatType'] = $Data[$i]['eCatType'];
                $Datacategory[$k]['eSubCatType'] = $Data[$i]['eSubCatType'];
                $Datacategory[$k]['eDeliveryType'] = $Data[$i]['eDeliveryType'];
                $Datacategory[$k]['iVehicleCategoryId'] = $Data[$i]['iVehicleCategoryId'];
                $Datacategory[$k]['vCategory'] = $Data[$i]['vCategory'];
                $Datacategory[$k]['vCategoryBanner'] = $Data[$i]['vCategory'];
                $Datacategory[$k]['vLogo'] = $Data[$i]['vLogo'];
                $Datacategory[$k]['vLogo_image'] = $tconfig['tsite_upload_images_vehicle_category'] . '/' . $Data[$i]['iVehicleCategoryId'] . '/android/' . $Data[$i]['vLogo'];
                $Datacategory[$k]['eShowType'] = $Data[$i]['eShowType'];
                $Datacategory[$k]['iServiceId'] = $Data[$i]['iServiceId'];
                $Datacategory[$k]['tBannerButtonText'] = $tBannerButtonText;
                $Datacategory[$k]['vBannerImage'] = ($Data[$i]['vBannerImage'] != "") ? $tconfig['tsite_upload_images_vehicle_category'] . '/' . $Data[$i]['iVehicleCategoryId'] . '/' . $Data[$i]['vBannerImage'] : "";
                $Datacategory[$k]['eShowTerms'] = "No";
                $Datacategory[$k]['eProofUpload'] = "No";
                $Datacategory[$k]['tProofNote'] = "";
                if (strtoupper(DELIVERALL) == "YES" && $Data[$i]['iServiceId'] > 0 && $MODULES_OBJ->isEnableTermsServiceCategories()) {
                    if ($MODULES_OBJ->isEnableTermsServiceCategories()) {
                        $Datacategory[$k]['eShowTerms'] = $deliverAll_serviceArr[$Data[$i]['iServiceId']]['eShowTerms'];
                    }
                    if ($MODULES_OBJ->isEnableProofUploadServiceCategories()) {
                        $Datacategory[$k]['eProofUpload'] = $deliverAll_serviceArr[$Data[$i]['iServiceId']]['eProofUpload'];
                        $Datacategory[$k]['tProofNote'] = $deliverAll_serviceArr[$Data[$i]['iServiceId']]['tProofNote'];
                    }
                }
                $Datacategory[$k]['vListLogo'] = ($Data[$i]['vListLogo'] != "") ? $tconfig['tsite_upload_images_vehicle_category'] . '/' . $Data[$i]['iVehicleCategoryId'] . '/' . $Data[$i]['vListLogo'] : "";
                $Datacategory[$k]['eCatViewType'] = $Data[$i]['eCatViewType'];
                $Datacategory[$k]['tListDescription'] = $tListDescriptionText;

                if ($MODULES_OBJ->isEnableAppHomeScreenLayoutV2()) {
                    $Datacategory[$k]['vListLogo'] = ($Data[$i]['vListLogo2'] != "") ? $tconfig['tsite_upload_images_vehicle_category'] . '/' . $Data[$i]['iVehicleCategoryId'] . '/' . $Data[$i]['vListLogo2'] : "";
                    $Datacategory[$k]['vBgColor'] = $Data[$i]['vBgColor'];
                    $Datacategory[$k]['vBorderColor'] = $Data[$i]['vBorderColor'];
                    if($THEME_OBJ->isDeliverallXv2ThemeActive() == "Yes") {
                        $Datacategory[$k]['vListLogo'] = ($Data[$i]['vListLogo2'] != "") ? $tconfig['tsite_upload_service_categories_images'] . $Data[$i]['vListLogo2'] : "";
                    }
                }
                if ($MODULES_OBJ->isEnableAppHomeScreenLayoutV3()) {
                    $Datacategory[$k]['vListLogo'] = ($Data[$i]['vListLogo3'] != "") ? $tconfig['tsite_upload_images_vehicle_category'] . '/' . $Data[$i]['iVehicleCategoryId'] . '/' . $Data[$i]['vListLogo3'] : "";
                }
                $Datacategory[$k]['eFor'] = $Data[$i]['eFor'];

                $k++;
            }
        }
    }
    $Datacategory1 = array_unique($Datacategory, SORT_REGULAR);

    $DatanewArr = array();
    foreach ($Datacategory1 as $inner) {
        if ($inner['eCatType'] == "Genie") {
            $inner['eCatType'] = "Anywhere";
        }
        array_push($DatanewArr, $inner);
    }

    $returnArr['Action'] = "1";
    if ($vehicle_category_main != '') {
        $returnArr['vParentCategoryName'] = $vehicle_category_main;
    } else {
        $returnArr['vParentCategoryName'] = '';
    }


    //$returnArr['message'] = array_reverse($DatanewArr);
    for ($i = 0; $i < count($DatanewArr); $i++) {
        $vLogo_image_tmp = $DatanewArr[$i]['vLogo_image'];
        $vBannerImage_tmp = $DatanewArr[$i]['vBannerImage'];
        $vLogo_image_tmp_orig_name_arr = explode("/", $DatanewArr[$i]['vLogo_image']);
        $vBannerImage_tmp_orig_name_arr = explode("/", $DatanewArr[$i]['vBannerImage']);
        $isFileExist = false;
        if (!empty($vBannerImage_tmp_orig_name_arr) && count($vBannerImage_tmp_orig_name_arr) > 0) {
            $vBannerImage_tmp_orig_name = $vBannerImage_tmp_orig_name_arr[count($vBannerImage_tmp_orig_name_arr) - 1];
            $isFileExist = file_exists($tconfig['tsite_upload_images_vehicle_category_path'] . '/' . $DatanewArr[$i]['iVehicleCategoryId'] . '/' . $vBannerImage_tmp_orig_name);
        }
        $isFileExist_1 = false;
        if (!empty($vLogo_image_tmp_orig_name_arr) && count($vLogo_image_tmp_orig_name_arr) > 0) {
            $vLogo_image_tmp_orig_name = $vLogo_image_tmp_orig_name_arr[count($vLogo_image_tmp_orig_name_arr) - 1];
            $isFileExist_1 = file_exists($tconfig['tsite_upload_images_vehicle_category_path'] . '/' . $DatanewArr[$i]['iVehicleCategoryId'] . '/android/' . $vLogo_image_tmp_orig_name);
        }
        if (empty($vBannerImage_tmp) || !$isFileExist) {
            $DatanewArr[$i]['vBannerImage'] = $tconfig["tsite_url"] . "webimages/icons/DefaultImg/15529086332815.png";
        }
        if (empty($vLogo_image_tmp) || !$isFileExist_1) {
            $DatanewArr[$i]['vLogo_image'] = $tconfig["tsite_url"] . "webimages/icons/DefaultImg/service_categories.png";
        }
    }

   
    $returnArr['message'] = $DatanewArr;

    $whereloc = " AND iLocationid IN ('-1')";
    if ($MODULES_OBJ->isEnableLocationwiseBanner()) {
        $vLatitude = isset($_REQUEST["vLatitude"]) ? $_REQUEST["vLatitude"] : '';
        $vLongitude = isset($_REQUEST["vLongitude"]) ? $_REQUEST["vLongitude"] : '';
        $User_Address_Banner = array($vLatitude, $vLongitude);
        if (!empty($User_Address_Banner)) {
            $iLocationIdBanner = GetUserGeoLocationIdBanner($User_Address_Banner);
            $country_str_banner = "'-1'";
            if (count($iLocationIdBanner) > 0) {
                foreach ($iLocationIdBanner as $key => $value) {
                    $country_str_banner .= ", '" . $value . "'";
                }
                $whereloc = " AND iLocationid IN (" . $country_str_banner . ")";
            }
        }
    }

    $Data_banners = $obj->MySQLSelect("SELECT vImage,vStatusBarColor,iUniqueId FROM banners WHERE vCode= '" . $lang . "' AND vImage != '' AND eStatus = 'Active' AND (iServiceId = 0) AND eType = 'General' $whereloc ORDER BY iDisplayOrder ASC");
    $dataOfBanners = array();
    $count = 0;
    for ($i = 0; $i < count($Data_banners); $i++) {
        if (isset($Data_banners[$i]['vImage']) && $Data_banners[$i]['vImage'] != "") {
            $dataOfBanners[$count]['vImage'] = $tconfig["tsite_url"] . 'assets/img/images/' . $Data_banners[$i]['vImage'];
            $dataOfBanners[$count]['vStatusBarColor'] = $Data_banners[$i]['vStatusBarColor'];
            $banner_img_path = $tconfig['tpanel_path'] . 'assets/img/images/' . $Data_banners[$i]['vImage'];
            if (file_exists($banner_img_path) && empty($Data_banners[$i]['vStatusBarColor'])) {
                $dataOfBanners[$count]['vStatusBarColor'] = getColorFromImage($banner_img_path);
                $obj->sql_query("UPDATE banners SET vStatusBarColor = '" . $dataOfBanners[$count]['vStatusBarColor'] . "' WHERE iUniqueId = '" . $Data_banners[$i]['iUniqueId'] . "'");
            }
            $count++;
        }
    }
    $returnArr['BANNER_DATA'] = $dataOfBanners;

    if ($MODULES_OBJ->isEnableAppHomeScreenLayoutV2() && $parentId == 0) {
        $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($lang, "1", $iServiceId);

        $returnArr['message'] = "";
        $master_service_categories = $obj->MySQLSelect("SELECT *, JSON_UNQUOTE(JSON_VALUE(vCategoryName, '$.vCategoryName_" . $lang . "')) as vCategoryName FROM $master_service_category_tbl WHERE eStatus = 'Active'");

        $MASTER_SERVICE_CATEGORIES = array();
        $MASTER_SERVICE_CATEGORIES[] = array('vTitle' => $languageLabelsArr['LBL_HEADER_ORDER_SERVICE_TITLE'], 'eShowType' => 'Header');

        $all_deliverall_cat_ids = array();
        foreach ($DatanewArr as $skey => $SubCategories) {
            if ($MODULES_OBJ->isDeliverAllFeatureAvailable() && in_array($SubCategories['eCatType'], ['DeliverAll'])) {
                $all_deliverall_cat_ids[] = $SubCategories['iVehicleCategoryId'];
            }
        }

        $cat_ids_arr = array_chunk($all_deliverall_cat_ids, 3);

        foreach ($cat_ids_arr as $ckey => $category) {
            $mServiceCategoryArr = array();
            $mServiceCategoryArr['vCategoryName'] = "";
            $mServiceCategoryArr['vCategoryTitle'] = "";
            $mServiceCategoryArr['vCategory'] = "";
            $mServiceCategoryArr['eShowType'] = "Service_Icon";

            $mServiceCategoryArr['SubCategories'] = array();
            foreach ($DatanewArr as $skey => $SubCategories) {
                $DatanewArr[$skey]['vLogo_image'] = $DatanewArr[$skey]['vListLogo'];
                if ($MODULES_OBJ->isDeliverAllFeatureAvailable() && in_array($SubCategories['eCatType'], ['DeliverAll'])) {
                    if(in_array($SubCategories['iVehicleCategoryId'], $category)) {
                        $DatanewArr[$skey]['eShowType'] = "Service_Icon";
                        $mServiceCategoryArr['SubCategories'][] = $DatanewArr[$skey]; 
                    }
                }
            }

            $MASTER_SERVICE_CATEGORIES[] = $mServiceCategoryArr;
        }
        
        
        $returnArr['HOME_SCREEN_DATA'] = $MASTER_SERVICE_CATEGORIES;
    }

    $returnArr['MORE_ICON'] = $tconfig["tsite_url"] . "webimages/icons/DefaultImg/ic_more.png";
    setDataResponse($returnArr);
}

if ($type == "getServiceCategoryTypes") {
    // // Commented By HJ On 15-07-2020 Bcoz Not Required
    $iVehicleCategoryId = isset($_REQUEST['iVehicleCategoryId']) ? clean($_REQUEST['iVehicleCategoryId']) : 0;
    $vLatitude = isset($_REQUEST["vLatitude"]) ? $_REQUEST["vLatitude"] : '';
    $vLongitude = isset($_REQUEST["vLongitude"]) ? $_REQUEST["vLongitude"] : '';
    $userId = isset($_REQUEST['userId']) ? clean($_REQUEST['userId']) : '';
    $eCheck = isset($_REQUEST['eCheck']) ? clean($_REQUEST['eCheck']) : 'No';
    $pickuplocationarr = array($vLatitude, $vLongitude);
    $sql_vehicle_category_table_name = getVehicleCategoryTblName();
    $GetVehicleIdfromGeoLocation = FetchVehicleTypeFromGeoLocation($pickuplocationarr);
    if ($eCheck == "" || $eCheck == NULL) {
        $eCheck = "No";
    }
    if ($eCheck == "Yes") {
        // $allowed_ans = checkRestrictedAreaNew($pickuplocationarr,"No");
        $allowed_ans = checkAreaRestriction($pickuplocationarr, "No");
        if ($allowed_ans == "Yes") {
            $sql23 = "SELECT iVehicleTypeId FROM `vehicle_type` WHERE iLocationid IN ($GetVehicleIdfromGeoLocation) AND iVehicleCategoryId = '" . $iVehicleCategoryId . "' ORDER BY iVehicleTypeId ASC";
            $vehicleTypes = $obj->MySQLSelect($sql23);
            if (count($vehicleTypes) > 0) {
                $returnArr['Action'] = "1";
            }
            else {
                $returnArr['Action'] = "0";
                $returnArr['message'] = "LBL_NO_SERVICES_AVAIL_FOR_JOB_LOC";
            }
        }
        else {
            $returnArr['Action'] = "0";
            $returnArr['message'] = "LBL_JOB_LOCATION_NOT_ALLOWED";
        }
        setDataResponse($returnArr);
    }
    else {
        if ($userId != "") {
            $row = $obj->MySQLSelect("SELECT vLang,vCurrencyPassenger FROM `register_user` WHERE iUserId='" . $userId . "'");
            $lang = $row[0]['vLang'];
            if ($lang == "" || $lang == NULL) {
                $lang = "EN";
            }
            $vCurrencyPassenger = $row[0]['vCurrencyPassenger'];
            if ($vCurrencyPassenger == "" || $vCurrencyPassenger == NULL) {
                $vCurrencyPassenger = get_value('currency', 'vName', 'eDefault', 'Yes', '', 'true');
            }
            $UserCurrencyData = get_value('currency', 'vSymbol, Ratio', 'vName', $vCurrencyPassenger);
            $priceRatio = $UserCurrencyData[0]['Ratio'];
            $vSymbol = $UserCurrencyData[0]['vSymbol'];
            $vehicleCategoryData = get_value($sql_vehicle_category_table_name, "vCategoryTitle_" . $lang . " as vCategoryTitle, tCategoryDesc_" . $lang . " as tCategoryDesc", 'iVehicleCategoryId', $iVehicleCategoryId);
            $vCategoryTitle = $vehicleCategoryData[0]['vCategoryTitle'];
            $vCategoryDesc = $vehicleCategoryData[0]['tCategoryDesc'];
            $Data = $obj->MySQLSelect("SELECT vc.iVehicleCategoryId, vc.iParentId,vc.vCategory_" . $lang . " as vCategory, vc.ePriceType, vt.iVehicleTypeId, vt.vVehicleType_" . $lang . " as vVehicleType, vt.eFareType, vt.fFixedFare, vt.fPricePerHour, vt.fPricePerKM, vt.fPricePerMin, vt.iBaseFare,vt.fCommision, vt.iMinFare,vt.iPersonSize, vt.vLogo as vVehicleTypeImage, vt.eType, vt.eIconType, vt.eAllowQty, vt.iMaxQty, vt.iVehicleTypeId, fFixedFare FROM " . $sql_vehicle_category_table_name . " as vc LEFT JOIN vehicle_type AS vt ON vt.iVehicleCategoryId = vc.iVehicleCategoryId WHERE vc.eStatus='Active' AND vt.eStatus='Active' AND vt.iVehicleCategoryId='" . $iVehicleCategoryId . "' AND vt.iLocationid IN ($GetVehicleIdfromGeoLocation)");
            if (!empty($Data)) {
                for ($i = 0; $i < count($Data); $i++) {
                    $Data[$i]['fFixedFare_value'] = round($Data[$i]['fFixedFare'] * $priceRatio, 2);
                    $fFixedFare = round($Data[$i]['fFixedFare'] * $priceRatio, 2);
                    $Data[$i]['fFixedFare'] = $vSymbol . formatNum($fFixedFare);
                    $Data[$i]['fPricePerHour_value'] = round($Data[$i]['fPricePerHour'] * $priceRatio, 2);
                    $fPricePerHour = round($Data[$i]['fPricePerHour'] * $priceRatio, 2);
                    $Data[$i]['fPricePerHour'] = $vSymbol . formatNum($fPricePerHour);
                    $fPricePerKM = round($Data[$i]['fPricePerKM'] * $priceRatio, 2);
                    $Data[$i]['fPricePerKM'] = $vSymbol . formatNum($fPricePerKM);
                    $fPricePerMin = round($Data[$i]['fPricePerMin'] * $priceRatio, 2);
                    $Data[$i]['fPricePerMin'] = $vSymbol . formatNum($fPricePerMin);
                    $iBaseFare = round($Data[$i]['iBaseFare'] * $priceRatio, 2);
                    $Data[$i]['iBaseFare'] = $vSymbol . formatNum($iBaseFare);
                    $fCommision = round($Data[$i]['fCommision'] * $priceRatio, 2);
                    $Data[$i]['fCommision'] = $vSymbol . formatNum($fCommision);
                    $iMinFare = round($Data[$i]['iMinFare'] * $priceRatio, 2);
                    $Data[$i]['iMinFare'] = $vSymbol . formatNum($iMinFare);
                    $Data[$i]['vSymbol'] = $vSymbol;
                    $Data[$i]['vCategoryTitle'] = $vCategoryTitle;
                    $Data[$i]['vCategoryDesc'] = $vCategoryDesc;
                    $iParentId = $Data[$i]['iParentId'];
                    if ($iParentId == 0) {
                        $ePriceType = $Data[$i]['ePriceType'];
                    }
                    else {
                        $ePriceType = get_value($sql_vehicle_category_table_name, 'ePriceType', 'iVehicleCategoryId', $iParentId, '', 'true');
                    }
                    $Data[$i]['ePriceType'] = $ePriceType;
                    $Data[$i]['ALLOW_SERVICE_PROVIDER_AMOUNT'] = $ePriceType == "Provider" ? "Yes" : "No";
                    // $Data[$i]['ALLOW_SERVICE_PROVIDER_AMOUNT']= $Data[$i]['ePriceType'] == "Provider"? "Yes" :"No";
                }
                $returnArr['Action'] = "1";
                $returnArr['message'] = $Data;
                // $returnArr['ALLOW_SERVICE_PROVIDER_AMOUNT'] = $ALLOW_SERVICE_PROVIDER_AMOUNT;
                $returnArr['vCategoryTitle'] = $vCategoryTitle;
                $returnArr['vCategoryDesc'] = $vCategoryDesc;
            }
            else {
                $returnArr['Action'] = "0";
                $returnArr['message'] = "LBL_NO_DATA_AVAIL";
            }
        }
        else {
            $returnArr['Action'] = "0";
            $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
        }
    }
    setDataResponse($returnArr);
}
if ($type == "getBanners") {
    $iMemberId = isset($_REQUEST['iMemberId']) ? clean($_REQUEST['iMemberId']) : '';
    $eCatType = isset($_REQUEST['eCatType']) ? clean($_REQUEST['eCatType']) : '';
    if ($iMemberId != "") {
        $vLanguage = get_value('register_user', 'vLang', 'iUserId', $iMemberId, '', 'true');
    }
    if ($vLanguage == "" || $vLanguage == NULL) {
        //Added By HJ On 15-07-2020 For Optimize language_master Table Query Start
        $vLanguage = $LANG_OBJ->FetchDefaultLangData("vCode");
        //Added By HJ On 15-07-2020 For Optimize language_master Table Query End
    }
    $banners = $obj->MySQLSelect("SELECT vImage FROM `banners` WHERE vCode = '" . $vLanguage . "' AND eStatus = 'Active' AND iServiceId = '" . $iServiceId . "' AND eType = 'General' ORDER BY iDisplayOrder ASC");
    if (in_array($eCatType, ['Genie', 'Runner', 'Anywhere']) && $MODULES_OBJ->isEnableAnywhereDeliveryFeature()) {
        if ($eCatType == "Genie" || $eCatType == "Anywhere") {
            $eCatType = "Genie";
        }
        $banners = $obj->MySQLSelect("SELECT vImage FROM `banners` WHERE vCode = '" . $vLanguage . "' AND eStatus = 'Active' AND eType = '" . $eCatType . "' ORDER BY iDisplayOrder ASC");
    }
    $data = array();
    $count = 0;
    for ($i = 0; $i < count($banners); $i++) {
        if ($banners[$i]['vImage'] != "") {
            $data[$count]['vImage'] = $tconfig["tsite_url"] . 'assets/img/images/' . urlencode($banners[$i]['vImage']);
            $count++;
        }
    }
    if (empty($data)) {
        $data = '';
    }
    $returnArr['Action'] = "1";
    $returnArr['message'] = $data;
    setDataResponse($returnArr);
}
if ($type == "getUserVehicleDetails") {
    $iMemberId = isset($_REQUEST['iMemberId']) ? clean($_REQUEST['iMemberId']) : '';
    $user_type = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : 'Driver';
    $vCountry = '';
    if ($user_type == "Passenger") {
        $tblname = "register_user";
        $fieldName = "iUserId";
        //$vLangCode = get_value('register_user', 'vLang', 'iUserId', $iMemberId, '', 'true');
    }
    else {
        $tblname = "register_driver";
        $fieldName = "iDriverId";
        //$driveData = get_value('register_driver', 'vLang,vCountry', 'iDriverId', $iMemberId);
    }
    //Added By HJ On 22-07-2020 For Optimize register_driver/register_user Table Query Start
    if (isset($userDetailsArr[$tblname . '_' . $iMemberId])) {
        $memberData = $userDetailsArr[$tblname . '_' . $iMemberId];
    }
    else {
        $memberData = $obj->MySQLSelect("SELECT *,$fieldName as iMemberId FROM " . $tblname . " WHERE $fieldName = '" . $iMemberId . "'");
        $userDetailsArr[$tblname . '_' . $iMemberId] = $memberData;
    }
    $vLangCode = $memberData[0]['vLang'];
    $vCountry = $memberData[0]['vCountry'];
    if ($user_type == "Passenger") {
        $vCountry = '';
    }
    //Added By HJ On 22-07-2020 For Optimize register_driver/register_user Table Query End
    if ($vLangCode == "" || $vLangCode == NULL) {
        //Added By HJ On 15-07-2020 For Optimize language_master Table Query Start
        $vLanguage = $LANG_OBJ->FetchDefaultLangData("vCode");
        //Added By HJ On 15-07-2020 For Optimize language_master Table Query End
    }
    $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLangCode, "1", $iServiceId);
    $lbl_all = $languageLabelsArr['LBL_ALL'];
    $sql = "SELECT iVehicleTypeId,vVehicleType_" . $vLangCode . " as vVehicleType,iLocationid,iCountryId,iStateId,iCityId,eType,eDeliveryHelper FROM `vehicle_type` WHERE eType = 'DeliverAll' AND `eStatus` = 'Active'";
    $db_vehicletype = $obj->MySQLSelect($sql);
    $sql1 = "select * from make where eStatus = 'Active' ORDER BY vMake ASC ";
    $make = $obj->MySQLSelect($sql1);
    $start = @date('Y');
    $end = '1970';
    $year = array();
    for ($j = $start; $j >= $end; $j--) {
        $year[] = strval($j);
    }
    $carlist = array();
    if (count($make) > 0) {
        for ($i = 0; $i < count($make); $i++) {
            $sql = "SELECT  * FROM  `model` WHERE iMakeId = '" . $make[$i]['iMakeId'] . "' AND `eStatus` = 'Active' ORDER BY vTitle ASC ";
            $db_model = $obj->MySQLSelect($sql);
            $ModelArr['List'] = $db_model;
            $carlist[$i]['iMakeId'] = $make[$i]['iMakeId'];
            $carlist[$i]['vMake'] = $make[$i]['vMake'];
            $carlist[$i]['vModellist'] = $ModelArr['List'];
        }
        $data['year'] = $year;
        $data['carlist'] = $carlist;
        $data['vehicletypelist'] = $db_vehicletype;
        if (count($db_vehicletype) == 0) {
            $returnArr['message1'] = "LBL_EDIT_VEHI_RESTRICTION_TXT";
        }
        $returnArr['Action'] = "1";
        $returnArr['message'] = $data;
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
    }
    setDataResponse($returnArr);
}
// ##########################Add/Edit Driver Vehicle#######################################################
if ($type == "UpdateDriverVehicle") {
    $iDriverVehicleId = isset($_REQUEST['iDriverVehicleId']) ? $_REQUEST['iDriverVehicleId'] : '';
    $iDriverId = isset($_REQUEST["iDriverId"]) ? $_REQUEST["iDriverId"] : '';
    $iMakeId = isset($_REQUEST["iMakeId"]) ? $_REQUEST["iMakeId"] : '';
    $iModelId = isset($_REQUEST["iModelId"]) ? $_REQUEST["iModelId"] : '';
    $iYear = isset($_REQUEST["iYear"]) ? $_REQUEST["iYear"] : '';
    $vLicencePlate = isset($_REQUEST["vLicencePlate"]) ? $_REQUEST["vLicencePlate"] : '';
    $eCarX = isset($_REQUEST["eCarX"]) ? $_REQUEST["eCarX"] : '';
    $eCarGo = isset($_REQUEST["eCarGo"]) ? $_REQUEST["eCarGo"] : '';
    $vColour = isset($_REQUEST["vColor"]) ? $_REQUEST["vColor"] : '';
    // $eStatus = ($CONFIG_OBJ->getConfigurations("configurations", "VEHICLE_AUTO_ACTIVATION") == 'Yes') ? 'Active' : 'Inactive';
    $vCarType = isset($_REQUEST["vCarType"]) ? $_REQUEST["vCarType"] : '';
    $handiCap = isset($_REQUEST["HandiCap"]) ? $_REQUEST["HandiCap"] : 'No';
    $iVehicleCategoryId = isset($_REQUEST["iVehicleCategoryId"]) ? $_REQUEST["iVehicleCategoryId"] : '';
    $action = ($iDriverVehicleId != 0) ? 'Edit' : 'Add';
    if ($action == "Add") {
        $eStatus = "Inactive";
    }
    //$db_usr = $obj->MySQLSelect("select iCompanyId,iDriverVehicleId,vAvailability from `register_driver` where iDriverId = '" . $iDriverId . "'");
    //Added By HJ On 22-07-2020 For Optimize register_driver Table Query Start
    $tblname = "register_driver";
    if (isset($userDetailsArr[$tblname . '_' . $iDriverId])) {
        $db_usr = $userDetailsArr[$tblname . '_' . $iDriverId];
    }
    else {
        $db_usr = $obj->MySQLSelect("SELECT *,iDriverId as iMemberId FROM " . $tblname . " WHERE iDriverId = '" . $iDriverId . "'");
        $userDetailsArr[$tblname . '_' . $iDriverId] = $driverData;
    }
    //Added By HJ On 22-07-2020 For Optimize register_driver Table Query End
    $SelctediDriverVehicleId = $iCompanyId = 0;
    $vAvailability = "Not Available";
    if (count($db_usr) > 0) {
        $SelctediDriverVehicleId = $db_usr[0]['iDriverVehicleId'];
        $vAvailability = $db_usr[0]['vAvailability'];
        $iCompanyId = $db_usr[0]['iCompanyId'];
    }
    if ($action == "Edit" && $ENABLE_EDIT_DRIVER_VEHICLE == "No" && $APP_TYPE != "UberX") {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_EDIT_VEHICLE_DISABLED";
        setDataResponse($returnArr);
    }
    else if ($APP_TYPE == "UberX" && $action == "Edit" && $ENABLE_EDIT_DRIVER_SERVICE == "No") { // Added By HJ On 10-08-2019 For Check Permission As Per Discuss With KS
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_EDIT_SERVICE_DISABLED";
        setDataResponse($returnArr);
    }
    if ($action == "Edit" && $iDriverVehicleId == $SelctediDriverVehicleId && $vAvailability == "Available") {
        //$SelctediDriverVehicleId = get_value('register_driver', 'iDriverVehicleId', 'iDriverId', $iDriverId, '', 'true');
        $returnArr['Action'] = 0;
        $returnArr['message'] = "LBL_DELETE_VEHICLE_RESTRICT_NOTE";
        setDataResponse($returnArr);
    }
    //$sql = "select iCompanyId from `register_driver` where iDriverId = '" . $iDriverId . "'";
    //$db_usr = $obj->MySQLSelect($sql);
    //$iCompanyId = $db_usr[0]['iCompanyId'];
    $Data_Driver_Vehicle['iDriverId'] = $iDriverId;
    $Data_Driver_Vehicle['iCompanyId'] = $iCompanyId;
    if (SITE_TYPE == "Demo") {
        $Data_Driver_Vehicle['eStatus'] = "Active";
    }
    else {
        if ($action == "Add") {
            $Data_Driver_Vehicle['eStatus'] = $eStatus;
        }
    }
    $Data_Driver_Vehicle['eCarX'] = $eCarX;
    $Data_Driver_Vehicle['eCarGo'] = $eCarGo;
    $Data_Driver_Vehicle['vCarType'] = $vCarType;
    $Data_Driver_Vehicle['eHandiCapAccessibility'] = $handiCap;
    if ($iMakeId != "") {
        $Data_Driver_Vehicle['iMakeId'] = $iMakeId;
    }
    if ($iModelId != "") {
        $Data_Driver_Vehicle['iModelId'] = $iModelId;
    }
    if ($iYear != "") {
        $Data_Driver_Vehicle['iYear'] = $iYear;
    }
    if ($vColour != "") {
        $Data_Driver_Vehicle['vColour'] = $vColour;
    }
    if ($vLicencePlate != "") {
        $Data_Driver_Vehicle['vLicencePlate'] = $vLicencePlate;
    }
    // $Data_Driver_Vehicle['vColour'] = $vColour;
    // $Data_Driver_Vehicle['vLicencePlate'] = $vLicencePlate;
    if ($action == "Add") {
        $id = $obj->MySQLQueryPerform("driver_vehicle", $Data_Driver_Vehicle, 'insert');
    }
    else {
        $where = " iDriverVehicleId = '" . $iDriverVehicleId . "'";
        $id = $obj->MySQLQueryPerform("driver_vehicle", $Data_Driver_Vehicle, 'update', $where);
    }
    if ($id > 0) {
        $returnArr['Action'] = "1";
        $returnArr['message'] = ($action == 'Add') ? 'LBL_VEHICLE_ADD_SUCCESS_NOTE' : 'LBL_VEHICLE_UPDATE_SUCCESS';
        $returnArr['VehicleInsertId'] = $id;
        $returnArr['VehicleStatus'] = $Data_Driver_Vehicle['eStatus'];
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
    }
    setDataResponse($returnArr);
}
// ##########################Add/Edit Driver Vehicle End#######################################################

// ##########################displayDocList##########################################################
if ($type == "displayDocList") {
    $iMemberId = isset($_REQUEST['iMemberId']) ? clean($_REQUEST['iMemberId']) : '';
    $memberType = isset($_REQUEST['MemberType']) ? clean($_REQUEST['MemberType']) : 'Driver';
    $iDriverVehicleId = isset($_REQUEST['iDriverVehicleId']) ? clean($_REQUEST['iDriverVehicleId']) : '';
    $doc_usertype = isset($_REQUEST['doc_usertype']) ? clean(strtolower($_REQUEST['doc_usertype'])) : 'driver';
    if ($doc_usertype == "vehicle") {
        $doc_usertype = "car";
    }
    $doc_userid = ($doc_usertype == 'car') ? $iDriverVehicleId : $iMemberId;
    // $APP_TYPE = $CONFIG_OBJ->getConfigurations("configurations", "APP_TYPE");
    /* $vCountry = get_value('register_driver', 'vCountry', 'iDriverId', $iMemberId,'',true);
    $vLang = get_value('register_driver', 'vLang', 'iDriverId', $iMemberId,'',true); */
    $UserData = get_value('register_driver', 'vCountry,vLang', 'iDriverId', $iMemberId);
    $vCountry = $UserData[0]['vCountry'];
    $vLang = $UserData[0]['vLang'];
    if ($vLang == '' || $vLang == NULL) {
        //Added By HJ On 15-07-2020 For Optimize language_master Table Query Start
        $vLang = $LANG_OBJ->FetchDefaultLangData("vCode");
        //Added By HJ On 15-07-2020 For Optimize language_master Table Query End
    }

    $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLang, "1", $iServiceId);

    $sql1 = "SELECT dm.doc_masterid masterid, dm.doc_usertype , dm.doc_name_" . $vLang . " as doc_name ,dm.ex_status,dm.status, COALESCE(dl.doc_id,  '' ) as doc_id,COALESCE(dl.doc_masterid, '') as masterid_list ,COALESCE(dl.ex_date, '') as ex_date,COALESCE(dl.doc_file, '') as doc_file, COALESCE(dl.status, '') as status FROM document_master dm left join (SELECT * FROM `document_list` where doc_userid='" . $doc_userid . "' ) dl on dl.doc_masterid=dm.doc_masterid where dm.doc_usertype='" . $doc_usertype . "' AND (dm.country='" . $vCountry . "' OR dm.country='All') and dm.status='Active' AND dm.eDocServiceType = 'General' ";
    $db_vehicle = $obj->MySQLSelect($sql1);
    if (count($db_vehicle) > 0) {
        // $Photo_Gallery_folder = $tconfig['tsite_upload_driver_doc']."/".$iMemberId."/";
        if ($doc_usertype == "driver") {
            $Photo_Gallery_folder = $tconfig['tsite_upload_driver_doc'] . "/" . $iMemberId . "/";
        }
        else {
            $Photo_Gallery_folder = $tconfig['tsite_upload_vehicle_doc_panel'] . "/" . $iDriverVehicleId . "/";
        }
        for ($i = 0; $i < count($db_vehicle); $i++) {
            if ($db_vehicle[$i]['doc_file'] != "") {
                $db_vehicle[$i]['vimage'] = $Photo_Gallery_folder . $db_vehicle[$i]['doc_file'];
            }
            else {
                $db_vehicle[$i]['vimage'] = "";
            }
            // # Checking for expire date of document ##
            $ex_date = $db_vehicle[$i]['ex_date'];
            $todaydate = date('Y-m-d');
            if ($ex_date == "" || $ex_date == "0000-00-00" || $db_vehicle[$i]['ex_date'] == "0000-00-00" || $db_vehicle[$i]['ex_date'] == "1970-01-01") {
                $expire_document = "No";
            }
            else {
                if (strtotime($ex_date) < strtotime($todaydate)) {
                    $expire_document = "Yes";
                }
                else {
                    $expire_document = "No";
                }
            }
            $db_vehicle[$i]['exp_date'] = "";
            if ($ex_date != "0000-00-00") {
                $expireLabel = $languageLabelsArr['LBL_EXPIRE_TXT'];
                //$newFormat = date("jS F Y", strtotime($db_vehicle[$i]['ex_date']));
                $newFormat = date("d M, Y (D)", strtotime($db_vehicle[$i]['ex_date']));
                $db_vehicle[$i]['exp_date'] = $expireLabel . ": " . $newFormat;
            }
            $db_vehicle[$i]['EXPIRE_DOCUMENT'] = $expire_document;
            // # Checking for expire date of document ##
        }
        $returnArr['Action'] = "1";
        $returnArr['message'] = $db_vehicle;
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_NO_DOC_AVAIL";
    }
    setDataResponse($returnArr);
}
// ###################################################################################################

// ##########################Add/Update Driver's Document and Vehilcle Document ##########################################################
if ($type == "uploaddrivedocument") {
    //global $tconfig; // Commented By HJ On 15-07-2020 Bcoz Not Required
    $iMemberId = isset($_REQUEST['iMemberId']) ? clean($_REQUEST['iMemberId']) : '';
    $iDriverVehicleId = isset($_REQUEST['iDriverVehicleId']) ? clean($_REQUEST['iDriverVehicleId']) : '';
    // $doc_userid = isset($_REQUEST['doc_userid']) ? clean($_REQUEST['doc_userid']) : '';
    $memberType = isset($_REQUEST['MemberType']) ? clean($_REQUEST['MemberType']) : 'Driver';
    $doc_usertype = isset($_REQUEST['doc_usertype']) ? clean(strtolower($_REQUEST['doc_usertype'])) : 'driver'; // vehicle OR driver
    $doc_masterid = isset($_REQUEST['doc_masterid']) ? clean($_REQUEST['doc_masterid']) : '';
    $doc_name = isset($_REQUEST['doc_name']) ? clean($_REQUEST['doc_name']) : '';
    $doc_id = isset($_REQUEST['doc_id']) ? clean($_REQUEST['doc_id']) : '';
    $doc_file = isset($_REQUEST['doc_file']) ? clean($_REQUEST['doc_file']) : '';
    $ex_date = isset($_REQUEST['ex_date']) ? clean($_REQUEST['ex_date']) : '';
    $ex_status = isset($_REQUEST['ex_status']) ? clean($_REQUEST['ex_status']) : '';
    if ($doc_usertype == "vehicle") {
        $doc_usertype = "car";
    }
    $doc_userid = ($doc_usertype == 'car') ? $iDriverVehicleId : $iMemberId;
    $status = ($doc_usertype == 'car') ? "Active" : "Inctive";
    $image_name = $vImage = isset($_FILES['vImage']['name']) ? $_FILES['vImage']['name'] : '';
    $image_object = isset($_FILES['vImage']['tmp_name']) ? $_FILES['vImage']['tmp_name'] : '';
    $action = ($doc_id != '') ? 'Edit' : 'Add';
    $addupdatemode = ($action == 'Add') ? 'insert' : 'update';
    if ($doc_file != "") {
        $vImageName = $doc_file;
    }
    else {
        if ($doc_usertype == "driver") {
            $Photo_Gallery_folder = $tconfig['tsite_upload_driver_doc_path'] . "/" . $iMemberId . "/";
        }
        else {
            $Photo_Gallery_folder = $tconfig['tsite_upload_vehicle_doc'] . "/" . $iDriverVehicleId . "/";
        }
        if (!is_dir($Photo_Gallery_folder)) {
            mkdir($Photo_Gallery_folder, 0777);
            chmod($Photo_Gallery_folder, 0777);
        }
        $vFile = $UPLOAD_OBJ->GeneralFileUpload($Photo_Gallery_folder, $image_object, $image_name, $prefix = '', $vaildExt = "bmp,pdf,doc,docx,jpg,jpeg,gif,png,xls,xlsx,csv");
        $vImageName = $vFile[0];
    }
    if ($vImageName != '') {
        $Data_Update["doc_masterid"] = $doc_masterid;
        $Data_Update["doc_usertype"] = $doc_usertype;
        $Data_Update["doc_userid"] = $doc_userid;
        $Data_Update["ex_date"] = $ex_date;
        $Data_Update["doc_file"] = $vImageName;
        $Data_Update["edate"] = @date("Y-m-d H:i:s");
        if ($action == "Add") {
            $Data_Update["status"] = $status;
            $id = $obj->MySQLQueryPerform("document_list", $Data_Update, 'insert');
        }
        else {
            $where = " doc_id = '" . $doc_id . "'";
            $id = $obj->MySQLQueryPerform("document_list", $Data_Update, 'update', $where);
        }
        save_log_data('0', $iMemberId, 'driver', $doc_name, $vImageName);
        if ($id > 0) {
            $sql_user = "SELECT rd.iDriverId,rd.vName,rd.vLastName,rd.vEmail as rdemail,c.vCompany,c.vEmail as cemail  FROM `register_driver` as rd join company as c on c.iCompanyId =rd.iCompanyId WHERE rd.iDriverId='" . $iMemberId . "'";
            $userdetails = $obj->MySQLSelect($sql_user);
            $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLang, "1", $iServiceId);
            if ($doc_usertype == "driver") {
                $maildata['NAME'] = $userdetails[0]['vName'] . " " . $userdetails[0]['vLastName'] . " (" . $languageLabelsArr['LBL_DOCUMNET_UPLOAD_BY_DRIVER'] . ")";
                $maildata['DOCUMENTFOR'] = $languageLabelsArr['LBL_DOCUMNET_UPLOAD_BY_DRIVER'];
                //Added By HJ On 15-07-2020 For Optimize language_master Table Query Start
                $vLang = $LANG_OBJ->FetchDefaultLangData("vCode");
                //Added By HJ On 15-07-2020 For Optimize language_master Table Query End
                $maildata['EMAIL'] = $userdetails[0]['rdemail'];
                $docname_SQL = "SELECT doc_name_" . $vLang . " as docname FROM document_master WHERE doc_masterid = '" . $doc_masterid . "'";
                $docname_data = $obj->MySQLSelect($docname_SQL);
                $maildata['DOCUMENTTYPE'] = $docname_data[0]['docname'];
                $COMM_MEDIA_OBJ->SendMailToMember("DOCCUMENT_UPLOAD_WEB", $maildata);
                $maildata['COMPANYEMAIL'] = $userdetails[0]['cemail'];
                $maildata['COMPANYNAME'] = $userdetails[0]['vCompany'];
                $COMM_MEDIA_OBJ->SendMailToMember("DOCCUMENT_UPLOAD_WEB_COMPANY", $maildata);
            }
            $returnArr['Action'] = "1";
            // $returnArr['message'] = getDriverDetailInfo($iMemberId);
        }
        else {
            $returnArr['Action'] = "0";
            $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
        }
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
    }
    setDataResponse($returnArr);
}
// ##########################Add/Update Driver's Document and Vehilcle Document Ends##########################################################


// ########################################################################
// # NEW WEBSERVICE END ##

// ##########################################################################
if ($type == "setVehicleTypes") {
    $langCodesArr = get_value('language_master', 'vCode', '', '');
    for ($i = 0; $i < count($langCodesArr); $i++) {
        $currLngCode = $langCodesArr[$i]['vCode'];
        $vVehicleType = $langCodesArr[$i]['vVehicleType'];
        $fieldName = "vVehicleType_" . $currLngCode;
        $suffixName = $i == 0 ? "vVehicleType" : "vVehicleType_" . $langCodesArr[$i - 1]['vCode'];
        $sql = "ALTER TABLE vehicle_type ADD " . $fieldName . " VARCHAR(50) AFTER" . " " . $suffixName;
        $id = $obj->sql_query($sql);
    }
    $vehicleTypesArr = get_value('vehicle_type', 'vVehicleType,iVehicleTypeId', '', '');
    for ($j = 0; $j < count($vehicleTypesArr); $j++) {
        $vVehicleType = $vehicleTypesArr[$j]['vVehicleType'];
        $iVehicleTypeId = $vehicleTypesArr[$j]['iVehicleTypeId'];
        echo "vVehicleType:" . $vVehicleType . "<BR/>";
        for ($k = 0; $k < count($langCodesArr); $k++) {
            $currLngCode = $langCodesArr[$k]['vCode'];
            $fieldName = "vVehicleType_" . $currLngCode;
            $suffixName = $k == 0 ? "vVehicleType" : "vVehicleType_" . $langCodesArr[$k - 1]['vCode'];
            // $sql = "ALTER TABLE vehicle_type ADD ".$fieldName." VARCHAR(50) AFTER"." ".$suffixName;
            // $id= $obj->sql_query($sql);
            echo $sql = "UPDATE `vehicle_type` SET " . $fieldName . " = '" . $vVehicleType . "' WHERE iVehicleTypeId = '$iVehicleTypeId'";
            echo "<br/>";
            $id1 = $obj->sql_query($sql);
            echo "<br/>" . $id1;
        }
    }
    // $id1= $obj->sql_query($sql);
}
if ($type == "DeclineTripRequest") {
    // $passenger_id = isset($_REQUEST["PassengerID"]) ? $_REQUEST["PassengerID"] : '';
    $driver_id = isset($_REQUEST["DriverID"]) ? $_REQUEST["DriverID"] : '';
    $iOrderId = isset($_REQUEST["iOrderId"]) ? $_REQUEST["iOrderId"] : '';
    $vMsgCode = isset($_REQUEST["vMsgCode"]) ? $_REQUEST["vMsgCode"] : '';
    $sql = "SELECT iDriverRequestId,eAcceptAttempted FROM `driver_request` WHERE iDriverId = '" . $driver_id . "' AND iOrderId = '" . $iOrderId . "' AND iTripId = '0' AND vMsgCode='" . $vMsgCode . "' AND eAcceptAttempted = 'No'";
    $db_sql = $obj->MySQLSelect($sql);
    if (count($db_sql) > 0) {
        $request_count = UpdateDriverRequest2($driver_id, $passenger_id, "0", "Decline", $vMsgCode, "No", $iOrderId);
    }
    else {
        $request_count = 0;
    }
    echo $request_count;
}
if ($type == "getYearTotalEarnings") {
    $iMemberId = isset($_REQUEST['iMemberId']) ? clean($_REQUEST['iMemberId']) : '';
    $year = isset($_REQUEST["year"]) ? $_REQUEST["year"] : @date('Y');
    $UserType = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : 'Driver';
    if ($year == "") {
        $year = @date('Y');
    }
    //Added By HJ On 17-07-2020 For Optimization register_driver/company Table Query Start
    $tableName = "company";
    $currencyFieldName = "vCurrencyCompany";
    $whereField = "iCompanyId";
    if ($UserType == 'Driver') {
        $tableName = "register_driver";
        $currencyFieldName = "vCurrencyDriver";
        $whereField = "iDriverId";
        //$vCurrency = get_value('register_driver', 'vCurrencyDriver', 'iDriverId', $iMemberId, '', 'true');
        //$vCurrencySymbol = get_value('currency', 'vSymbol', 'vName', $vCurrency, '', 'true');
    }
    if (isset($userDetailsArr[$tableName . "_" . $iMemberId])) {
        $getMemberData = $userDetailsArr[$tableName . "_" . $iMemberId];
    }
    else {
        $getMemberData = $obj->MySQLSelect("SELECT *,$whereField as iMemberId FROM " . $tableName . " WHERE $whereField='" . $iMemberId . "'");
        $userDetailsArr[$tableName . "_" . $iMemberId] = $getMemberData;
    }
    $vCurrency = $getMemberData[0][$currencyFieldName];
    if ($vCurrency == "" || $vCurrency == NULL) {
        $vCurrency = get_value('currency', 'vName', 'eDefault', 'Yes', '', 'true');
    }
    
    $vLangCode = $getMemberData[0]['vLang'];
    //Added By HJ On 17-07-2020 For Optimization register_driver/company Table Query End
    //Added By HJ On 17-07-2020 For Optimization currency Table Query Start
    if (isset($currencyAssociateArr[$vCurrency])) {
        $vCurrencySymbol = $currencyAssociateArr[$vCurrency]['vSymbol'];
    }
    else {
        $vCurrencySymbol = get_value('currency', 'vSymbol', 'vName', $vCurrency, '', 'true');
    }
    //Added By HJ On 17-07-2020 For Optimization currency Table Query End
    if ($vLangCode == "" || $vLangCode == NULL) {
        //Added By HJ On 15-07-2020 For Optimize language_master Table Query Start
        $vLangCode = $LANG_OBJ->FetchDefaultLangData("vCode");
        //Added By HJ On 15-07-2020 For Optimize language_master Table Query End
    }
    $lngLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLangCode, "1", $iServiceId);
    $start = @date('Y');
    $end = '1970';
    $year_arr = array();
    for ($j = $start; $j >= $end; $j--) {
        $year_arr[] = strval($j);
    }
    $Month_Array = array('01' => $lngLabelsArr['LBL_JANUARY'], '02' => $lngLabelsArr['LBL_FEBRUARY'], '03' => $lngLabelsArr['LBL_MARCH'], '04' => $lngLabelsArr['LBL_APRIL'], '05' => $lngLabelsArr['LBL_MAY'], '06' => $lngLabelsArr['LBL_JUNE'], '07' => $lngLabelsArr['LBL_JULY'], '08' => $lngLabelsArr['LBL_AUGUST'], '09' => $lngLabelsArr['LBL_SEPTEMBER'], '10' => $lngLabelsArr['LBL_OCTOBER'], '11' => $lngLabelsArr['LBL_NOVEMBER'], '12' => $lngLabelsArr['LBL_DECEMBER']);
    /*if ($UserType == 'Driver') {
        $sql = "SELECT * FROM trips WHERE iDriverId='" . $iMemberId . "' AND tTripRequestDate LIKE '" . $year . "%' AND eSystem = 'DeliverAll'";
    } else {
        $sql = "SELECT * FROM orders WHERE iCompanyId='" . $iMemberId . "' AND iStatusCode = '6' AND tOrderRequestDate LIKE '" . $year . "%'";
    }
    $tripData = $obj->MySQLSelect($sql);*/
    //Added By HJ On 17-07-2020 For Optimize Order/Trip Data Table Query Start
    $dateField = "tOrderRequestDate";
    $sql_Month = "SELECT * FROM orders WHERE iCompanyId='" . $iMemberId . "' AND iStatusCode IN (6,7,8)";
    if ($UserType == 'Driver') {
        $dateField = "tTripRequestDate";
        $sql_Month = "SELECT * FROM trips WHERE iDriverId='" . $iMemberId . "' AND eSystem = 'DeliverAll'";
    }
    $tripyearmonthData = $obj->MySQLSelect($sql_Month);
    $yearmontharr = $yearmontearningharr_Max = $orderDataArr = $tripData = array();
    for ($g = 0; $g < count($tripyearmonthData); $g++) {
        if ($tripyearmonthData[$g]['iOrderId'] > 0) {
            $checkDriverCancelOrder = $obj->MySQLSelect("SELECT iLogId FROM order_driver_log WHERE iOrderId = '" . $tripyearmonthData[$g]['iOrderId'] . "' AND iDriverId = '" . $tripyearmonthData[$g]['iDriverId'] . "'");
            if (!empty($checkDriverCancelOrder) && count($checkDriverCancelOrder) > 0) {
                continue;
            }
        }
        $reqDate = $tripyearmonthData[$g][$dateField];
        $yearMonth = date("Y-m", strtotime($reqDate));
        $dateYear = date("Y", strtotime($reqDate));
        //$month = date("m",strtotime($reqDate));
        $orderDataArr[$yearMonth][] = $tripyearmonthData[$g];
        if ($year == $dateYear) {
            $tripData[] = $tripyearmonthData[$g];
        }
    }
    //Added By HJ On 17-07-2020 For Optimize Order/Trip Data Table Query End
    $totalEarnings = 0;
    for ($i = 0; $i < count($tripData); $i++) {
        if ($UserType == 'Driver') {
            $OrderId = $tripData[$i]['iOrderId'];
            $iFare = 0;
            if ($OrderId > 0) {
                $orderData = $obj->MySQLSelect("SELECT fTipAmount,ePaymentOption,eBuyAnyService,iStatusCode,fDriverPaidAmount FROM orders WHERE iOrderId = '$OrderId' AND iStatusCode IN (6,7,8)");
                if (!empty($orderData) && count($orderData) > 0) {
                    $iFare = $tripData[$i]['fDeliveryCharge'];
                    $iStatusCode = $orderData[0]['iStatusCode'];
                    $fDriverPaidAmount = $orderData[0]['fDriverPaidAmount'];
                    if ($iStatusCode == '7' || $iStatusCode == '8') {
                        $iFare = $fDriverPaidAmount;
                    }
                    else {
                        $subtotal = 0;
                        if ($orderData[0]['eBuyAnyService'] == "Yes" && $orderData[0]['ePaymentOption'] == "Card") {
                            $order_buy_anything = $obj->MySQLSelect("SELECT * FROM order_items_buy_anything WHERE iOrderId = '" . $OrderId . "'");
                            if (count($order_buy_anything) > 0) {
                                foreach ($order_buy_anything as $oItem) {
                                    if ($oItem['eConfirm'] == "Yes") {
                                        $subtotal += $oItem['fItemPrice'];
                                    }
                                }
                            }
                        }
                        $iFare = $iFare + $orderData[0]['fTipAmount'] + $subtotal;
                    }
                }
            }
        }
        else {
            $iStatusCode = $tripData[$i]['iStatusCode'];
            $fRestaurantPaidAmount = $tripData[$i]['fRestaurantPaidAmount'];
            if ($iStatusCode == '7' || $iStatusCode == '8') {
                $iFare = $fRestaurantPaidAmount;
            }
            else {
                $iFare = $tripData[$i]['fTotalGenerateFare'] - $tripData[$i]['fOffersDiscount'] - $tripData[$i]['fDeliveryCharge'] - $tripData[$i]['fCommision'] - $tripData[$i]['fTipAmount'] - $tripData[$i]['fOutStandingAmount'];
            }
        }
        $priceRatio = $tripData[$i]['fRatio_' . $vCurrency];
        $totalEarnings += $iFare * $priceRatio;
    }
    foreach ($Month_Array as $key => $value) {
        $tripyearmonthdate = $year . "-" . $key;
        //Commented By HJ On 17-07-2020 For Optimize Order/Trip Data Table Query Start
        /*if ($UserType == 'Driver') {
            $sql_Month = "SELECT * FROM trips WHERE iDriverId='" . $iMemberId . "' AND tTripRequestDate LIKE '" . $tripyearmonthdate . "%' AND eSystem = 'DeliverAll'";
        } else {
            $sql_Month = "SELECT * FROM orders WHERE iCompanyId='" . $iMemberId . "' AND iStatusCode = '6' AND tOrderRequestDate LIKE '" . $tripyearmonthdate . "%'";
        }
        $tripyearmonthData = $obj->MySQLSelect($sql_Month);*/
        //Commented By HJ On 17-07-2020 For Optimize Order/Trip Data Table Query End
        $tripyearmonthData = array();
        if (isset($orderDataArr[$tripyearmonthdate])) {
            $tripyearmonthData = $orderDataArr[$tripyearmonthdate];
        }
        $tripData_M = strval(count($tripyearmonthData));
        $yearmontearningharr = array();
        $totalEarnings_M = 0;
        for ($j = 0; $j < count($tripyearmonthData); $j++) {
            if ($UserType == 'Driver') {
                $OrderId_M = $tripyearmonthData[$j]['iOrderId'];
                $iFare_M = 0;
                if ($OrderId_M > 0) {
                    $orderData_M = $obj->MySQLSelect("SELECT fTipAmount,ePaymentOption,eBuyAnyService,iStatusCode,fDriverPaidAmount FROM orders WHERE iOrderId = '$OrderId_M' AND iStatusCode IN (6,7,8)");
                    if (!empty($orderData_M) && count($orderData_M) > 0) {
                        $iFare_M = $tripyearmonthData[$j]['fDeliveryCharge'];
                        $iStatusCode = $orderData_M[0]['iStatusCode'];
                        $fDriverPaidAmount = $orderData_M[0]['fDriverPaidAmount'];
                        if ($iStatusCode == '7' || $iStatusCode == '8') {
                            $iFare_M = $fDriverPaidAmount;
                        }
                        else {
                            $subtotal = 0;
                            if ($orderData_M[0]['eBuyAnyService'] == "Yes" && $orderData_M[0]['ePaymentOption'] == "Card") {
                                $order_buy_anything = $obj->MySQLSelect("SELECT * FROM order_items_buy_anything WHERE iOrderId = '" . $OrderId_M . "'");
                                if (count($order_buy_anything) > 0) {
                                    foreach ($order_buy_anything as $oItem) {
                                        if ($oItem['eConfirm'] == "Yes") {
                                            $subtotal += $oItem['fItemPrice'];
                                        }
                                    }
                                }
                            }
                            $iFare_M = $iFare_M + $orderData_M[0]['fTipAmount'] + $subtotal;
                        }
                    }
                }
            }
            else {
                $iStatusCode = $tripyearmonthData[$j]['iStatusCode'];
                $fRestaurantPaidAmount = $tripyearmonthData[$j]['fRestaurantPaidAmount'];
                if ($iStatusCode == '7' || $iStatusCode == '8') {
                    $iFare_M = $fRestaurantPaidAmount;
                }
                else {
                    $iFare_M = $tripyearmonthData[$j]['fTotalGenerateFare'] - $tripyearmonthData[$j]['fOffersDiscount'] - $tripyearmonthData[$j]['fDeliveryCharge'] - $tripyearmonthData[$j]['fCommision'] - $tripyearmonthData[$j]['fTipAmount'] - $tripyearmonthData[$j]['fOutStandingAmount'];
                }
            }
            $priceRatio_M = $tripyearmonthData[$j]['fRatio_' . $vCurrency];
            $totalEarnings_M += $iFare_M * $priceRatio_M;
        }
        $yearmontearningharr_Max[] = $totalEarnings_M;
        $yearmontearningharr["CurrentMonth"] = $value;
        $yearmontearningharr["TotalEarnings"] = strval(round($totalEarnings_M < 0 ? 0 : $totalEarnings_M, 1));
        $yearmontearningharr["OrderCount"] = strval(round($tripData_M, 1));
        array_push($yearmontharr, $yearmontearningharr);
    }
    $max = 0;
    foreach ($yearmontearningharr_Max as $key => $value) {
        if ($value >= $max) {
            $max = $value;
        }
    }
    $returnArr['Action'] = "1";
    $returnArr['TotalEarning'] = formateNumAsPerCurrency(round($totalEarnings, 1), $vCurrency);
    $returnArr['OrderCount'] = strval(count($tripData));
    $returnArr["CurrentYear"] = $year;
    $returnArr['MaxEarning'] = strval($max);
    $returnArr['YearMonthArr'] = $yearmontharr;
    $returnArr['YearArr'] = $year_arr;
    setDataResponse($returnArr);
}
/* For Forgot Password */
if ($type == 'requestResetPassword') {
    global $obj, $tconfig, $ENABLE_PHONE_LOGIN_VIA_COUNTRY_SELECTION_METHOD;
    $Emid = isset($_REQUEST["vEmail"]) ? $_REQUEST["vEmail"] : '';
    $isEmail = isset($_REQUEST["isEmail"]) ? $_REQUEST["isEmail"] : 'Yes';
    $phoneCode = isset($_REQUEST["PhoneCode"]) ? $_REQUEST["PhoneCode"] : '';
    $userType = isset($_REQUEST["UserType"]) ? clean($_REQUEST["UserType"]) : ''; // UserType = Driver/Passenger
    if ($userType == "" || $userType == NULL) {
        $userType = "Passenger";
    }
    if ($userType == "Passenger") {
        $tblname = "register_user";
        $fields = 'iUserId as iMemberId, vPhone,vPhoneCode as vPhoneCode, vEmail, vName, vLastName, vPassword, vLang';
        $condfield = 'iUserId';
        $EncMembertype = base64_encode(base64_encode('rider'));
        /*for email optional*/
        if ($isEmail == "Yes") {
            $sql = "select $fields from $tblname where vEmail = '" . $Emid . "'";
        }
        else {
            $sql = "select $fields from $tblname where vPhone = '" . $Emid . "' AND  vPhoneCode = '" . $phoneCode . "'";
        }
    }
    else if ($userType == "Company") {
        $tblname = "company";
        $fields = 'iCompanyId  as iMemberId, vPhone,vCode as vPhoneCode, vEmail, vCompany, vPassword, vLang';
        $condfield = 'iCompanyId';
        $EncMembertype = base64_encode(base64_encode('company'));
        /*for email optional*/
        if ($isEmail == "Yes") {
            $sql = "select $fields from $tblname where vEmail = '" . $Emid . "'";
        }
        else {
            $sql = "select $fields from $tblname where vPhone = '" . $Emid . "' AND  vCode = '" . $phoneCode . "'";
        }
    }
    else {
        $tblname = "register_driver";
        $fields = 'iDriverId  as iMemberId, vPhone,vCode as vPhoneCode, vEmail, vName, vLastName,   vPassword, vLang';
        $condfield = 'iDriverId';
        $EncMembertype = base64_encode(base64_encode('driver'));
        if ($isEmail == "Yes") {
            $sql = "select $fields from $tblname where vEmail = '" . $Emid . "'";
        }
        else {
            $sql = "select $fields from $tblname where vPhone = '" . $Emid . "' AND  vCode = '" . $phoneCode . "'";
        }
    }
    $db_member = $obj->MySQLSelect($sql);
    if (count($db_member) > 0) {
        $vLangCode = $db_member[0]['vLang'];
        if ($vLangCode == "" || $vLangCode == NULL) {
            //Added By HJ On 15-07-2020 For Optimize language_master Table Query Start
            $vLangCode = $LANG_OBJ->FetchDefaultLangData("vCode");
            //Added By HJ On 15-07-2020 For Optimize language_master Table Query End
        }
        $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLangCode, "1", $iServiceId);
        $clickherelabel = $languageLabelsArr['LBL_CLICKHERE_SIGNUP'];
        $milliseconds = time();
        $tempGenrateCode = substr($milliseconds, 1);
        $Today = Date('Y-m-d H:i:s');
        $today = base64_encode(base64_encode($Today));
        $type = $EncMembertype;
        $id = encrypt($db_member[0]["iMemberId"]);
        $newToken = RandomString(32);
        $url = $tconfig["tsite_url"] . 'reset_password.php?type=' . $type . '&id=' . $id . '&_token=' . $newToken;
        $link = get_tiny_url($url);
        $activation_text = '<a href="' . $url . '" target="_blank"> ' . $clickherelabel . ' </a>';
        $maildata['EMAIL'] = $db_member[0]["vEmail"];
        $maildata['NAME'] = $db_member[0]["vName"] . " " . $db_member[0]["vLastName"];
        if ($isEmail == "Yes") {
            $maildata['LINK'] = $activation_text;
            $status = $COMM_MEDIA_OBJ->SendMailToMember("CUSTOMER_RESET_PASSWORD", $maildata);
        }
        else {
            $maildata['LINK'] = $link;
            $message_layout = $COMM_MEDIA_OBJ->GetSMSTemplate("CUSTOMER_RESET_PASSWORD", $maildata, "", $vLangCode);
            $status = $COMM_MEDIA_OBJ->SendSystemSMS($Emid, $phoneCode, $message_layout);
        }
        $sql = "UPDATE $tblname set vPassword_token='" . $newToken . "' WHERE vEmail='" . $Emid . "' and eStatus != 'Deleted'";
        $obj->sql_query($sql);
        if ($status == 1) {
            $returnArr['Action'] = "1";
            // $returnArr['message'] = "LBL_PASSWORD_SENT_TXT";
            if (isOnlyDigitsStrSGF($Emid) && !empty($phoneCode) && $ENABLE_PHONE_LOGIN_VIA_COUNTRY_SELECTION_METHOD == 'Yes') {
                $returnArr['message'] = "LBL_PASSWORD_SENT_TXT_SMS";
            }
            else {
                $returnArr['message'] = "LBL_PASSWORD_SENT_TXT";
            }
        }
        else {
            $returnArr['Action'] = "0";
            $returnArr['message'] = "LBL_ERROR_PASSWORD_MAIL";
        }
    }
    else {
        $returnArr['Action'] = "0";
        if ($isEmail == "Yes") {
            $returnArr['message'] = "LBL_WRONG_EMAIL_PASSWORD_TXT";
        }
        else {
            $returnArr['message'] = "LBL_INVALID_PHONE_NUMBER";
        }
    }
    setDataResponse($returnArr);
}
/* For Forgot Password */
// ##########################################################################
/* For Driver Vehicle Details */
if ($type == "getDriverVehicleDetails") {
    $driverId = isset($_REQUEST['iDriverId']) ? clean($_REQUEST['iDriverId']) : '';
    $userType = isset($_REQUEST['UserType']) ? clean($_REQUEST['UserType']) : 'Driver';
    $distance = isset($_REQUEST["distance"]) ? $_REQUEST["distance"] : '';
    $time = isset($_REQUEST["time"]) ? $_REQUEST["time"] : '';
    $StartLatitude = isset($_REQUEST["StartLatitude"]) ? $_REQUEST["StartLatitude"] : '0.0';
    $EndLongitude = isset($_REQUEST["EndLongitude"]) ? $_REQUEST["EndLongitude"] : '0.0';
    $DestLatitude = isset($_REQUEST["DestLatitude"]) ? $_REQUEST["DestLatitude"] : '';
    $DestLongitude = isset($_REQUEST["DestLongitude"]) ? $_REQUEST["DestLongitude"] : '';
    $PickUpAddress = isset($_REQUEST["PickUpAddress"]) ? $_REQUEST["PickUpAddress"] : '';
    $time = round(($time / 60), 2);
    $distance = round(($distance / 1000), 2);
    $VehicleTypeIds = isset($_REQUEST["VehicleTypeIds"]) ? $_REQUEST["VehicleTypeIds"] : '';
    $isDestinationAdded = "No";
    if ($DestLatitude != "" && $DestLongitude != "") {
        $isDestinationAdded = "Yes";
    }
    $vLang = get_value('register_driver', 'vLang', 'iDriverId', $driverId, '', 'true');
    if ($vLang == "" || $vLang == NULL) {
        //Added By HJ On 15-07-2020 For Optimize language_master Table Query Start
        $vLang = $LANG_OBJ->FetchDefaultLangData("vCode");
        //Added By HJ On 15-07-2020 For Optimize language_master Table Query End
    }
    $iDriverVehicleId = get_value('register_driver', 'iDriverVehicleId', 'iDriverId', $driverId, '', 'true');
    if ($iDriverVehicleId > 0) {
        $Fare_Data = array();
        $vCarType = get_value('driver_vehicle', 'vCarType', 'iDriverVehicleId', $iDriverVehicleId, '', 'true');
        $DriverVehicle_Arr = explode(",", $vCarType);
        // $sql11 = "SELECT vVehicleType_".$vLang." as vVehicleTypeName, iVehicleTypeId, vLogo, iPersonSize FROM `vehicle_type`  WHERE  iVehicleTypeId IN (".$vCarType.") AND eType='Ride'";
        if ($VehicleTypeIds != "") {
            $sql11 = "SELECT  vVehicleType_" . $vLang . " as vVehicleTypeName,iVehicleTypeId, vLogo,vLogo1, iPersonSize FROM vehicle_type WHERE iVehicleTypeId IN (" . $VehicleTypeIds . ") AND eType='Ride' AND eStatus='Active'";
        }
        else {
            $pickuplocationarr = array($StartLatitude, $EndLongitude);
            $GetVehicleIdfromGeoLocation = FetchVehicleTypeFromGeoLocation($pickuplocationarr);
            $sql_vehicle = "SELECT iVehicleTypeId FROM vehicle_type WHERE iLocationid IN (" . $GetVehicleIdfromGeoLocation . ") AND eType='Ride' AND eStatus='Active'";
            $db_vehicle_location = $obj->MySQLSelect($sql_vehicle);
            $array_vehiclie_id = array();
            for ($i = 0; $i < count($db_vehicle_location); $i++) {
                array_push($array_vehiclie_id, $db_vehicle_location[$i]['iVehicleTypeId']);
            }
            $Vehicle_array_diff = array_values(array_intersect($DriverVehicle_Arr, $array_vehiclie_id));
            $VehicleTypeIds_Str = implode(",", $Vehicle_array_diff);
            if ($VehicleTypeIds_Str == "") {
                $VehicleTypeIds_Str = "0";
            }
            $sql11 = "SELECT  vVehicleType_" . $vLang . " as vVehicleTypeName,iVehicleTypeId, vLogo,vLogo1, iPersonSize FROM vehicle_type WHERE iVehicleTypeId IN (" . $VehicleTypeIds_Str . ") AND eType='Ride' AND eStatus='Active'";
        }
        $vCarType_Arr = $obj->MySQLSelect($sql11);
        $Fare_Data = array();
        if (count($vCarType_Arr) > 0) {
            for ($i = 0; $i < count($vCarType_Arr); $i++) {
                // ######## Checking For Flattrip #########
                if ($isDestinationAdded == "Yes") {
                    $sourceLocationArr = array($StartLatitude, $EndLongitude);
                    $destinationLocationArr = array($DestLatitude, $DestLongitude);
                    $data_flattrip = checkFlatTripnew($sourceLocationArr, $destinationLocationArr, $vCarType_Arr[$i]['iVehicleTypeId']);
                    $eFlatTrip = $data_flattrip['eFlatTrip'];
                    $fFlatTripPrice = $data_flattrip['Flatfare'];
                }
                else {
                    $eFlatTrip = "No";
                    $fFlatTripPrice = 0;
                }
                $Fare_Data[$i]['eFlatTrip'] = $eFlatTrip;
                $Fare_Data[$i]['fFlatTripPrice'] = $fFlatTripPrice;
                // ######## Checking For Flattrip #########
                $Fare_Single_Vehicle_Data = calculateApproximateFareGeneral($time, $distance, $vCarType_Arr[$i]['iVehicleTypeId'], $driverId, 1, "", "", "", 1, 0, 0, 0, "DisplySingleVehicleFare", "Driver", 1, "", $isDestinationAdded, $eFlatTrip, $fFlatTripPrice);
                $Fare_Data[$i]['iVehicleTypeId'] = $vCarType_Arr[$i]['iVehicleTypeId'];
                $Fare_Data[$i]['vVehicleTypeName'] = $vCarType_Arr[$i]['vVehicleTypeName'];
                // $Fare_Data[$i]['vLogo'] = $vCarType_Arr[$i]['vLogo'];
                $Photo_Gallery_folder = $tconfig["tsite_upload_images_vehicle_type_path"] . '/' . $vCarType_Arr[$i]['iVehicleTypeId'] . '/android/' . $vCarType_Arr[$i]['vLogo'];
                if ($vCarType_Arr[$i]['vLogo'] != "" && file_exists($Photo_Gallery_folder)) {
                    $Fare_Data[$i]['vLogo'] = $vCarType_Arr[$i]['vLogo'];
                }
                else {
                    $Fare_Data[$i]['vLogo'] = "";
                }
                $Photo_Gallery_folder_vLogo1 = $tconfig["tsite_upload_images_vehicle_type_path"] . '/' . $vCarType_Arr[$i]['iVehicleTypeId'] . '/android/' . $vCarType_Arr[$i]['vLogo1'];
                if ($vCarType_Arr[$i]['vLogo1'] != "" && file_exists($Photo_Gallery_folder_vLogo1)) {
                    $Fare_Data[$i]['vLogo1'] = $vCarType_Arr[$i]['vLogo1'];
                }
                else {
                    $Fare_Data[$i]['vLogo1'] = "";
                }
                $Fare_Data[$i]['iPersonSize'] = $vCarType_Arr[$i]['iPersonSize'];
                $lastvalue = end($Fare_Single_Vehicle_Data);
                $lastvalue1 = array_shift($lastvalue);
                $Fare_Data[$i]['SubTotal'] = $lastvalue1;
                $Fare_Data[$i]['VehicleFareDetail'] = $Fare_Single_Vehicle_Data;
                // array_push($Fare_Data, $Fare_Single_Vehicle_Data);
            }
        }
        $returnArr['Action'] = "1";
        $returnArr['message'] = $Fare_Data;
        // $returnArr['eFlatTrip'] = $eFlatTrip;
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_NO_VEHICLE_SELECTED";
    }
    setDataResponse($returnArr);
}
// ##########################################################################

if ($type == "callOnLogout") {
    $iMemberId = isset($_REQUEST["iMemberId"]) ? $_REQUEST["iMemberId"] : '';
    $userType = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : 'Passenger';
    $vPassword = isset($_REQUEST["vPassword"]) ? $_REQUEST["vPassword"] : '';
    $eSystemAppType = isset($_REQUEST["eSystemAppType"]) ? $_REQUEST["eSystemAppType"] : '';
    $Data_logout = array();
    if ($userType == "Passenger") {
        $Data_logout['eLogout'] = 'Yes';
        $tableName = "register_user";
        $where = " iUserId='" . $iMemberId . "'";
        $id = $obj->MySQLQueryPerform($tableName, $Data_logout, 'update', $where);
    }
    else if ($userType == "Company") {
        if (!empty($eSystemAppType) && strtoupper($eSystemAppType) == "KIOSK") {
            $GeneralMemberId = isset($_REQUEST["GeneralMemberId"]) ? $_REQUEST["GeneralMemberId"] : '';
            $company_data = $obj->MySQLSelect("SELECT vPassword FROM company WHERE iCompanyId = '$GeneralMemberId'");
            $checkValid = $AUTH_OBJ->VerifyPassword($vPassword, $company_data[0]['vPassword']);
            if ($checkValid == 0) {
                $returnArr['Action'] = "0";
                $returnArr['message'] = "LBL_WRONG_DETAIL";
                setDataResponse($returnArr);
            }
            $id = 1;
        }
        else {
            $Data_logout['eAvailable'] = 'No';
            $Data_logout['eLogout'] = 'Yes';
            $tableName = "company";
            $where = " iCompanyId='" . $iMemberId . "'";
            $id = $obj->MySQLQueryPerform($tableName, $Data_logout, 'update', $where);
        }
    }
    else {
        $Data_logout['vAvailability'] = 'Not Available';
        $Data_logout['eLogout'] = 'Yes';
        $tableName = "register_driver";
        $where = " iDriverId='" . $iMemberId . "'";
        $id = $obj->MySQLQueryPerform($tableName, $Data_logout, 'update', $where);
        $curr_date = date('Y-m-d H:i:s');
        $selct_query = "select * from driver_log_report WHERE iDriverId = '" . $iMemberId . "' AND dLogoutDateTime = '0000-00-00 00:00:00' order by `iDriverLogId` desc limit 0,1";
        $get_data_log = $obj->sql_query($selct_query);
        if (count($get_data_log) > 0) {
            $update_sql = "UPDATE driver_log_report set dLogoutDateTime = '" . $curr_date . "' WHERE iDriverLogId ='" . $get_data_log[0]['iDriverLogId'] . "'";
            $result = $obj->sql_query($update_sql);
        }
    }
    if ($id) {
        $returnArr['Action'] = "1";
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
    }
    setDataResponse($returnArr);
}
if ($type == "getCabRequestAddress") {
    $iOrderId = isset($_REQUEST["iOrderId"]) ? $_REQUEST["iOrderId"] : '';
    $iDriverId = isset($_REQUEST["GeneralMemberId"]) ? $_REQUEST["GeneralMemberId"] : '';
    $fields = "iUserId,iCompanyId,iStatusCode,iUserAddressId";
    $Data_cab_request = get_value('orders', $fields, 'iOrderId', $iOrderId, '', '');
    $iCompanyId = $Data_cab_request[0]['iCompanyId'];
    $companyfields = "vCompany,vRestuarantLocation,vRestuarantLocationLat,vRestuarantLocationLong,vCaddress";
    $Data_cab_requestcompany = get_value('company', $companyfields, 'iCompanyId', $iCompanyId, '', '');
    $iUserAddressId = $Data_cab_request[0]['iUserAddressId'];
    $userfields = "vServiceAddress,vBuildingNo,vLatitude,vLongitude";
    $Data_cab_requestuser = get_value('user_address', $userfields, 'iUserAddressId', $iUserAddressId, '', '');
    if (!empty($Data_cab_requestcompany)) {
        //$vRestuarantLocation = ($Data_cab_requestcompany[0]['vRestuarantLocation'] != '') ? $Data_cab_requestcompany[0]['vRestuarantLocation'] : ''; // Commened By HJ On 21-10-2020 As Per Discuss with KS sir
        $vRestuarantLocation = ($Data_cab_requestcompany[0]['vCaddress'] != '') ? $Data_cab_requestcompany[0]['vCaddress'] : ''; // Added By HJ On 21-10-2020 As Per Discuss with KS sir
        $vRestuarantLocationLat = ($Data_cab_requestcompany[0]['vRestuarantLocationLat'] != '') ? $Data_cab_requestcompany[0]['vRestuarantLocationLat'] : '';
        $vRestuarantLocationLong = ($Data_cab_requestcompany[0]['vRestuarantLocationLong'] != '') ? $Data_cab_requestcompany[0]['vRestuarantLocationLong'] : '';
        if (!empty($Data_cab_requestuser[0]['vBuildingNo'])) {
            $tDestAddress = $Data_cab_requestuser[0]['vBuildingNo'] . ", " . $Data_cab_requestuser[0]['vServiceAddress'];
        }
        else {
            $tDestAddress = $Data_cab_requestuser[0]['vServiceAddress'];
        }
        $UserAddressArr = FetchMemberAddressData($Data_cab_request[0]['iUserId'], "Passenger", $iUserAddressId);
        $vLatitude = ($Data_cab_requestuser[0]['vLatitude'] != '') ? $Data_cab_requestuser[0]['vLatitude'] : '';
        $vLongitude = ($Data_cab_requestuser[0]['vLongitude'] != '') ? $Data_cab_requestuser[0]['vLongitude'] : '';
    }
    $Data_cab_request[0]['tSourceAddress'] = $vRestuarantLocation;
    $Data_cab_request[0]['tSourceLat'] = $Data_cab_request[0]['sourceLatitude'] = $vRestuarantLocationLat;
    $Data_cab_request[0]['tSourceLong'] = $Data_cab_request[0]['sourceLongitude'] = $vRestuarantLocationLong;
    $Data_cab_request[0]['tDestAddress'] = $UserAddressArr['UserAddress'];
    $Data_cab_request[0]['tDestLatitude'] = $Data_cab_request[0]['destLatitude'] = $vLatitude;
    $Data_cab_request[0]['tDestLongitude'] = $Data_cab_request[0]['destLongitude'] = $vLongitude;
    $Data_cab_request[0]['eType'] = "DeliverAll"; // Added By HJ On 23-09-2019 As Per Discuss With CS
    if (!empty($Data_cab_request)) {
        $returnArr['Action'] = "1";
        $returnArr['message'] = $Data_cab_request[0];
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
    }
    setDataResponse($returnArr);
}
// ##########################################################################

// #######################Get Driver Bank Details############################
if ($type == "CompanyBankDetails") {
    $iCompanyId = isset($_REQUEST["iCompanyId"]) ? $_REQUEST["iCompanyId"] : "";
    $userType = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : 'Company';
    $vPaymentEmail = isset($_REQUEST["vPaymentEmail"]) ? $_REQUEST["vPaymentEmail"] : '';
    $vAcctHolderName = isset($_REQUEST["vAcctHolderName"]) ? $_REQUEST["vAcctHolderName"] : '';
    $vAcctNo = isset($_REQUEST["vAcctNo"]) ? $_REQUEST["vAcctNo"] : '';
    $vBankLocation = isset($_REQUEST["vBankLocation"]) ? $_REQUEST["vBankLocation"] : '';
    $vBankName = isset($_REQUEST["vBankName"]) ? $_REQUEST["vBankName"] : '';
    $vSwiftCode = isset($_REQUEST["vSwiftCode"]) ? $_REQUEST["vSwiftCode"] : '';
    $CALL_TYPE = isset($_REQUEST["CALL_TYPE"]) ? $_REQUEST["CALL_TYPE"] : "Display"; // Display , Update
    $returnArr = array();
    if ($CALL_TYPE == "Display") {
        $sqlc = "SELECT vPaymentEmail,vAcctHolderName,vAcctNo,vBankLocation,vBankName,vSwiftCode FROM company WHERE iCompanyId = '" . $iCompanyId . "'";
        $result_company = $obj->MySQLSelect($sqlc);
        $returnArr['Action'] = "1";
        $returnArr['message'] = $result_company[0];
        setDataResponse($returnArr);
    }
    else {
        $Data_Update['vPaymentEmail'] = $vPaymentEmail;
        $Data_Update['vAcctHolderName'] = $vAcctHolderName;
        $Data_Update['vAcctNo'] = $vAcctNo;
        $Data_Update['vBankLocation'] = $vBankLocation;
        $Data_Update['vBankName'] = $vBankName;
        $Data_Update['vSwiftCode'] = $vSwiftCode;
        $where = " iCompanyId = '" . $iCompanyId . "'";
        $Company_Update_id = $obj->MySQLQueryPerform("company", $Data_Update, 'update', $where);
        if ($Company_Update_id) {
            $returnArr['Action'] = "1";
            //$returnArr['message'] = "LBL_INFO_UPDATED_TXT";
            $returnArr['message'] = getCompanyDetailInfo($iCompanyId, 1);
        }
        else {
            $returnArr['Action'] = "0";
            $returnArr['message'] = "LBL_TRY_AGAIN_LATER";
        }
        setDataResponse($returnArr);
    }
}
// ##########################################################################
if ($type == "getServiceTypes") {
    $iVehicleCategoryId = isset($_REQUEST['iVehicleCategoryId']) ? $_REQUEST['iVehicleCategoryId'] : '';
    $iDriverId = isset($_REQUEST['iDriverId']) ? $_REQUEST['iDriverId'] : '';
    $languageCode = "";
    $sql_vehicle_category_table_name = getVehicleCategoryTblName();
    if ($iDriverId != "") {
        //Added By HJ On 22-06-2020 For Optimization register_driver Table Query Start
        if (isset($userDetailsArr["register_driver_" . $iDriverId])) {
            $driverData = $userDetailsArr["register_driver_" . $iDriverId];
        }
        else {
            $driverData = $obj->MySQLSelect("SELECT *,iDriverId as iMemberId FROM register_driver WHERE iDriverId='" . $iDriverId . "' ");
            $userDetailsArr["register_driver_" . $iDriverId] = $driverData;
        }
        $languageCode = $driverData[0]['vLang'];
        //Added By HJ On 22-06-2020 For Optimization register_driver Table Query End
        //$languageCode = get_value('register_driver', 'vLang', 'iDriverId', $iDriverId, '', 'true');
    }
    if ($languageCode == "" || $languageCode == NULL) {
        //Added By HJ On 15-07-2020 For Optimize language_master Table Query Start
        $languageCode = $LANG_OBJ->FetchDefaultLangData("vCode");
        //Added By HJ On 15-07-2020 For Optimize language_master Table Query End
    }
    //$db_driverdetail = $obj->MySQLSelect("SELECT * FROM `register_driver` where iDriverId ='" . $iDriverId . "'");
    $vCountry = $driverData[0]['vCountry'];
    $vCurrencyDriver = $driverData[0]['vCurrencyDriver'];
    $iDriverVehicleId = $driverData[0]['iDriverVehicleId'];
    $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($languageCode, "1", $iServiceId);
    $lbl_all = $languageLabelsArr['LBL_ALL'];
    $ssql = "";
    $getLocationData = $obj->MySQLSelect("SELECT * FROM location_master");
    if ($vCountry != "") {
        //Added By HJ On 22-07-2020 For Optimize country Table Query Start
        if (isset($country_data_arr[$vCountry])) {
            $iCountryId = $country_data_arr[$vCountry]['iCountryId'];
        }
        else {
            $iCountryId = get_value('country', 'iCountryId', 'vCountryCode', $vCountry, '', 'true');
        }
        //Added By HJ On 22-07-2020 For Optimize country Table Query End
        //Added By HJ On 22-07-2020 For Optimize language_master Table Query Start
        $db_country = array();
        for ($d = 0; $d < count($getLocationData); $d++) {
            if ($getLocationData[$d]['eStatus'] == "Active" && $getLocationData[$d]['iCountryId'] == $iCountryId && $getLocationData[$d]['eFor'] == "VehicleType") {
                $db_country[] = $getLocationData[$d];
            }
        }
        //$db_country = $obj->MySQLSelect("SELECT * FROM location_master WHERE eStatus='Active' AND iCountryId = '" . $iCountryId . "' AND eFor = 'VehicleType'");
        //Added By HJ On 22-07-2020 For Optimize language_master Table Query End
        $country_str = "-1";
        if (count($db_country) > 0) {
            for ($i = 0; $i < count($db_country); $i++) {
                $country_str .= "," . $db_country[$i]['iLocationId'];
            }
        }
        $ssql .= " AND iLocationid IN ($country_str) ";
    }
    $vehicleDetail = $obj->MySQLSelect("SELECT iVehicleTypeId, vVehicleType_" . $languageCode . " as vTitle,eFareType,eAllowQty,iMaxQty,fFixedFare,fPricePerHour,iLocationid from vehicle_type where iVehicleCategoryId in($iVehicleCategoryId)" . $ssql);
    //$vCurrencyDriver = get_value('register_driver', 'vCurrencyDriver', 'iDriverId', $driverData[0]['iDriverId'], '', 'true');
    if ($vCurrencyDriver == "" || $vCurrencyDriver == NULL) {
        //Added By HJ On 22-07-2020 For Optimization currency Table Query Start
        if (!empty($vSystemDefaultCurrencyName)) {
            $vCurrencyDriver = $vSystemDefaultCurrencyName;
        }
        else {
            $vCurrencyDriver = get_value('currency', 'vName', 'eDefault', 'Yes', '', 'true');
        }
        //Added By HJ On 22-07-2020 For Optimization currency Table Query End
    }
    //Added By HJ On 22-07-2020 For Optimization currency Table Query Start
    if (isset($currencyAssociateArr[$vCurrencyDriver])) {
        $currencyData = array();
        $currencyData[] = $currencyAssociateArr[$vCurrencyDriver];
    }
    else {
        $vCurrencyData = get_value('currency', 'vSymbol, Ratio', 'vName', $vCurrencyDriver);
    }
    $vCurrencySymbol = $vCurrencyData[0]['vSymbol'];
    $vCurrencyRatio = $vCurrencyData[0]['Ratio'];
    //Added By HJ On 22-07-2020 For Optimization currency Table Query End
    //Added By HJ On 22-07-2020 For Optimization vehicle_category Table Query Start
    $getVehicleCatData = $obj->MySQLSelect("SELECT iParentId,ePriceType,iVehicleCategoryId FROM vehicle_category");
    $iParentId = 0;
    $vehicleCatDataArr = array();
    for ($c = 0; $c < count($getVehicleCatData); $c++) {
        $vehicleCatDataArr[$getVehicleCatData[$c]['iVehicleCategoryId']] = $getVehicleCatData[$c];
    }
    if (isset($vehicleCatDataArr[$iVehicleCategoryId])) {
        $iParentId = $vehicleCatDataArr[$iVehicleCategoryId]['iParentId'];
    }
    //$iParentId = get_value('vehicle_category', 'iParentId,ePriceType', 'iVehicleCategoryId', $iVehicleCategoryId, '', 'true');
    if ($iParentId == 0) {
        if (isset($vehicleCatDataArr[$iVehicleCategoryId])) {
            $ePriceType = $vehicleCatDataArr[$iVehicleCategoryId]['iVehicleCategoryId'];
        }
        //$ePriceType = get_value('vehicle_category', 'ePriceType', 'iVehicleCategoryId', $iVehicleCategoryId, '', 'true');
    }
    else {
        if (isset($vehicleCatDataArr[$iParentId])) {
            $ePriceType = $vehicleCatDataArr[$iParentId]['iVehicleCategoryId'];
        }
        //$ePriceType = get_value('vehicle_category', 'ePriceType', 'iVehicleCategoryId', $iParentId, '', 'true');
    }
    //Added By HJ On 22-07-2020 For Optimization vehicle_category Table Query End
    //$iDriverVehicleId = get_value('register_driver', 'iDriverVehicleId', 'iDriverId', $iDriverId, '', 'true');
    /* Added By PJ for get pending services status */
    $ReqServices = $obj->MySQLSelect('SELECT iVehicleCategoryId FROM driver_service_request WHERE iDriverId = "' . $iDriverId . '" ');
    $requestedServices = array();
    foreach ($ReqServices as $key => $ReqService) {
        $requestedServices[] = $ReqService['iVehicleCategoryId'];
    }
    /* END pending services status */
    $db_vCarType = $obj->MySQLSelect("SELECT vCarType FROM `driver_vehicle` where iDriverId ='" . $iDriverId . "' AND iDriverVehicleId = '" . $iDriverVehicleId . "'");
    if (count($db_vCarType) > 0) {
        $vehicle_service_id = explode(",", $db_vCarType[0]['vCarType']);
        //Added By HJ On 22-07-2020 For Optimize language_master Table Query Start
        for ($l = 0; $l < count($getLocationData); $l++) {
            $locationDataArr[$getLocationData[$l]['iLocationId']] = $getLocationData[$l]['vLocationName'];
        }
        //Added By HJ On 22-07-2020 For Optimize location_master Table Query End
        //Added By HJ On 22-07-2020 For Optimize service_pro_amount Table Query Start
        $db_serviceproviderid = $obj->MySQLSelect("SELECT * FROM `service_pro_amount` where iDriverVehicleId ='" . $iDriverVehicleId . "'");
        $serviceProDataArr = array();
        for ($s = 0; $s < count($db_serviceproviderid); $s++) {
            $serviceProDataArr[$db_serviceproviderid[$s]['iVehicleTypeId']][] = $db_serviceproviderid[$s];
        }
        //Added By HJ On 22-07-2020 For Optimize service_pro_amount Table Query End
        for ($i = 0; $i < count($vehicleDetail); $i++) {
            //Added By HJ On 22-07-2020 For Optimize service_pro_amount Table Query Start
            //$db_serviceproviderid = $obj->MySQLSelect("SELECT * FROM `service_pro_amount` where iDriverVehicleId ='" . $iDriverVehicleId . "' AND iVehicleTypeId='" . $vehicleDetail[$i]['iVehicleTypeId'] . "'");
            $db_serviceproviderid = array();
            if (isset($serviceProDataArr[$vehicleDetail[$i]['iVehicleTypeId']])) {
                $db_serviceproviderid = $serviceProDataArr[$vehicleDetail[$i]['iVehicleTypeId']];
            }
            //Added By HJ On 22-07-2020 For Optimize service_pro_amount Table Query End
            if (count($db_serviceproviderid) > 0) {
                $vehicleDetail[$i]['fAmount'] = $db_serviceproviderid[0]['fAmount'];
            }
            else {
                if ($vehicleDetail[$i]['eFareType'] == "Hourly") {
                    $vehicleDetail[$i]['fAmount'] = $vehicleDetail[$i]['fPricePerHour'];
                }
                else {
                    $vehicleDetail[$i]['fAmount'] = $vehicleDetail[$i]['fFixedFare'];
                }
            }
            // $vehicleDetail[$i]['iDriverVehicleId']=$driverData[0]['iDriverVehicleId'];
            $fAmount = round($vehicleDetail[$i]['fAmount'] * $vCurrencyRatio, 2);
            $vehicleDetail[$i]['fAmount'] = $fAmount;
            $vehicleDetail[$i]['ePriceType'] = $ePriceType;
            $vehicleDetail[$i]['vCurrencySymbol'] = $vCurrencySymbol;
            $data_service[$i] = $vehicleDetail[$i];
            if (in_array($data_service[$i]['iVehicleTypeId'], $vehicle_service_id)) {
                $vehicleDetail[$i]['VehicleServiceStatus'] = 'true';
            }
            else {
                $vehicleDetail[$i]['VehicleServiceStatus'] = 'false';
            }
            /* Added By PJ for get pending services status */
            if ($APP_TYPE == 'UberX' || $APP_TYPE == 'Ride-Delivery-UberX') {
                if (in_array($data_service[$i]['iVehicleTypeId'], $vehicle_service_id)) {
                    $vehicleDetail[$i]['eServiceRequest'] = 'Active';
                }
                else if (in_array($data_service[$i]['iVehicleTypeId'], $requestedServices)) {
                    $vehicleDetail[$i]['eServiceRequest'] = 'Pending';
                }
                else {
                    $vehicleDetail[$i]['eServiceRequest'] = 'Inactive';
                }
                $vehicleDetail[$i]['VehicleServiceId'] = $data_service[$i]['iVehicleTypeId'];
            }
            if ($vehicleDetail[$i]['iLocationid'] == "-1") {
                $vehicleDetail[$i]['SubTitle'] = $lbl_all;
            }
            else {
                //$locationname = $obj->MySQLSelect("SELECT vLocationName FROM location_master WHERE iLocationId = '" . $vehicleDetail[$i]['iLocationid'] . "'");
                $vLocationName = "";
                if (isset($locationDataArr[$vehicleDetail[$i]['iLocationid']])) {
                    $vLocationName = $locationDataArr[$vehicleDetail[$i]['iLocationid']];
                }
                $vehicleDetail[$i]['SubTitle'] = $locationname[0]['vLocationName'];
            }
        }
    }
    if (count($vehicleDetail) > 0) {
        $returnArr['Action'] = "1";
        $returnArr['message'] = $vehicleDetail;
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_NO_DATA_AVAIL";
    }
    setDataResponse($returnArr);
}
// ##########################Display User Address##########################################################
if ($type == "DisplayUserAddress") {
    $iUserId = isset($_REQUEST['iUserId']) ? clean($_REQUEST['iUserId']) : '';
    $eUserType = isset($_REQUEST['eUserType']) ? clean($_REQUEST['eUserType']) : 'Passenger';
    $passengerLat = isset($_REQUEST["PassengerLat"]) ? $_REQUEST["PassengerLat"] : '';
    $passengerLon = isset($_REQUEST["PassengerLon"]) ? $_REQUEST["PassengerLon"] : '';
    $iCompanyId = isset($_REQUEST["iCompanyId"]) ? $_REQUEST["iCompanyId"] : '';
    $eCatType = isset($_REQUEST["eCatType"]) ? $_REQUEST["eCatType"] : '';
    if ($eUserType == "Passenger") {
        $eUserType = "Rider";
    }
    $sql = "select * from `user_address` where iUserId = '" . $iUserId . "' AND eUserType = '" . $eUserType . "' AND eStatus = 'Active' ORDER BY iUserAddressId DESC";
    $db_userdata = $obj->MySQLSelect($sql);
    if (count($db_userdata) > 0) {
        $sql = "select vRestuarantLocationLat,vRestuarantLocationLong from `company` where iCompanyId = '" . $iCompanyId . "'";
        $db_companydata = $obj->MySQLSelect($sql);
        $vRestuarantLocationLat = $db_companydata[0]['vRestuarantLocationLat'];
        $vRestuarantLocationLong = $db_companydata[0]['vRestuarantLocationLong'];
        $distancewithcompany = distanceByLocation($passengerLat, $passengerLon, $vRestuarantLocationLat, $vRestuarantLocationLong, "K");
        for ($i = 0; $i < count($db_userdata); $i++) {
            $isRemoveAddressFromList = "No";
            $eLocationAvailable = "Yes";
            $addressLatitude = $db_userdata[$i]['vLatitude'];
            $addressLongitude = $db_userdata[$i]['vLongitude'];
            $distance = distanceByLocation($vRestuarantLocationLat, $vRestuarantLocationLong, $addressLatitude, $addressLongitude, "K");
            if ($distance > $LIST_RESTAURANT_LIMIT_BY_DISTANCE && !in_array($eCatType, ['Genie', 'Runner', 'Anywhere'])) {
                $isRemoveAddressFromList = "Yes";
            }
            if ($iCompanyId > 0) {
                if ($distancewithcompany > $LIST_RESTAURANT_LIMIT_BY_DISTANCE && !in_array($eCatType, ['Genie', 'Runner', 'Anywhere'])) {
                    $isRemoveAddressFromList = "Yes";
                }
            }
            if ($isRemoveAddressFromList == "Yes") {
                $eLocationAvailable = "No";
            }
            $db_userdata[$i]['eLocationAvailable'] = $eLocationAvailable;
        }
        //$db_userdata = array_values($db_userdata_new);
        if (count($db_userdata) > 0) {
            $returnArr['Action'] = "1";
            $returnArr['message'] = $db_userdata;
        }
        else {
            $returnArr['Action'] = "0";
            $returnArr['message'] = "LBL_NO_USER_ADDRESS_FOUND";
        }
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_NO_USER_ADDRESS_FOUND";
    }
    setDataResponse($returnArr);
}
// ##########################Display User Address End######################################################

// ##########################Display Availability##########################################################
if ($type == "DisplayAvailability") {
    $iDriverId = isset($_REQUEST['iDriverId']) ? clean($_REQUEST['iDriverId']) : '';
    $vDay = isset($_REQUEST['vDay']) ? clean($_REQUEST['vDay']) : '';
    $db_data = $obj->MySQLSelect("select * from `driver_manage_timing` where iDriverId = '" . $iDriverId . "' AND vDay LIKE '" . $vDay . "' ORDER BY iDriverTimingId DESC");
    if (count($db_data) > 0) {
        $returnArr['Action'] = "1";
        $returnArr['message'] = $db_data[0];
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_NO_AVAILABILITY_FOUND";
    }
    setDataResponse($returnArr);
}
// ##########################Display Availability End######################################################
// ##########################Add/Update Availability ##########################################################
if ($type == "UpdateAvailability") {
    $iDriverTimingId = isset($_REQUEST['iDriverTimingId']) ? $_REQUEST['iDriverTimingId'] : '';
    $iDriverId = isset($_REQUEST["iDriverId"]) ? $_REQUEST["iDriverId"] : '';
    $vDay = isset($_REQUEST["vDay"]) ? $_REQUEST["vDay"] : '';
    $vAvailableTimes = isset($_REQUEST["vAvailableTimes"]) ? $_REQUEST["vAvailableTimes"] : '';
    $eStatus = isset($_REQUEST["eStatus"]) ? $_REQUEST["eStatus"] : 'Active';
    $dAddedDate = @date("Y-m-d H:i:s");
    $vAvailableTimes = CheckAvailableTimes($vAvailableTimes); // Convert to 04-05,05-06,07-08,11-12,14-15
    $db_data = $obj->MySQLSelect("select iDriverTimingId from `driver_manage_timing` where iDriverId = '" . $iDriverId . "' AND vDay LIKE '" . $vDay . "'");
    if (count($db_data) > 0) {
        $action = "Edit";
        $iDriverTimingId = $db_data[0]['iDriverTimingId'];
    }
    else {
        $action = "Add";
    }
    $Data_driver_timing['iDriverId'] = $iDriverId;
    $Data_driver_timing['vDay'] = $vDay;
    $Data_driver_timing['vAvailableTimes'] = $vAvailableTimes;
    $Data_driver_timing['dAddedDate'] = $dAddedDate;
    $Data_driver_timing['eStatus'] = $eStatus;
    if ($action == "Add") {
        $insertid = $obj->MySQLQueryPerform("driver_manage_timing", $Data_driver_timing, 'insert');
        $TimingId = $insertid;
    }
    else {
        $where = " iDriverTimingId = '" . $iDriverTimingId . "'";
        $insertid = $obj->MySQLQueryPerform("driver_manage_timing", $Data_driver_timing, 'update', $where);
        $TimingId = $iDriverTimingId;
    }
    if ($insertid > 0) {
        $returnArr['Action'] = "1";
        $returnArr['TimingId'] = $insertid;
        $returnArr['message'] = "LBL_TIMESLOT_ADD_SUCESS_MSG";
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
    }
    setDataResponse($returnArr);
}
// #############################Add/Update User Address End##########################################################
// #############################Update Radius ##########################################################
if ($type == "UpdateRadius") {
    $iDriverId = isset($_REQUEST["iDriverId"]) ? $_REQUEST["iDriverId"] : '';
    $vWorkLocationRadius = isset($_REQUEST["vWorkLocationRadius"]) ? $_REQUEST["vWorkLocationRadius"] : '';
    $eStatus = isset($_REQUEST["eStatus"]) ? $_REQUEST["eStatus"] : 'Active';
    $Data_register_driver['vWorkLocationRadius'] = $vWorkLocationRadius;
    $eUnit = getMemberCountryUnit($iDriverId, "Driver");
    if ($eUnit == "Miles") {
        $Data_register_driver['vWorkLocationRadius'] = round($vWorkLocationRadius * 1.60934, 2); // convert miles to km
    }
    $where = " iDriverId = '" . $iDriverId . "'";
    $updateid = $obj->MySQLQueryPerform("register_driver", $Data_register_driver, 'update', $where);
    if ($updateid > 0) {
        $returnArr['Action'] = "1";
        $returnArr['UpdateId'] = $iDriverId;
        $returnArr['message'] = getDriverDetailInfo($iDriverId);
        $returnArr['message1'] = "LBL_INFO_UPDATED_TXT";
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
    }
    setDataResponse($returnArr);
}
// #############################Update Radius  End##########################################################
// ##########################Display Driver Day Availability##########################################################
if ($type == "DisplayDriverDaysAvailability") {
    $iDriverId = isset($_REQUEST['iDriverId']) ? clean($_REQUEST['iDriverId']) : '';
    $db_data = $obj->MySQLSelect("select vDay from `driver_manage_timing` where iDriverId = '" . $iDriverId . "' AND  vAvailableTimes <> '' ORDER BY iDriverTimingId DESC");
    if (count($db_data) > 0) {
        $returnArr['Action'] = "1";
        $returnArr['message'] = $db_data;
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_NO_AVAILABILITY_FOUND";
    }
    setDataResponse($returnArr);
}
// ##########################Display Driver Day Availability Ends##########################################################

// ############################ Start submitTripHelpDetail ############################################################
if ($type == "submitTripHelpDetail") {
    $iOrderId = isset($_REQUEST['iOrderId']) ? clean($_REQUEST['iOrderId']) : '';
    $iMemberId = isset($_REQUEST['iMemberId']) ? clean($_REQUEST['iMemberId']) : '';
    $iHelpDetailId = isset($_REQUEST['iHelpDetailId']) ? clean($_REQUEST['iHelpDetailId']) : '';
    $vComment = isset($_REQUEST['vComment']) ? clean($_REQUEST['vComment']) : '';
    $appType = isset($_REQUEST['appType']) ? clean($_REQUEST['appType']) : '';
    $current_date = date('Y-m-d H:i:s');
    if ($appType == "Driver") {
        $sql = "SELECT CONCAT(vName,' ',vLastName) as Name FROM `register_driver` WHERE iDriverId='" . $iMemberId . "'";
    }
    else {
        $sql = "SELECT CONCAT(vName,' ',vLastName) as Name FROM `register_user` WHERE iUserId='" . $iMemberId . "'";
    }
    $Data = $obj->MySQLSelect($sql);
    $Data_trip_help_detail['iOrderId'] = $iOrderId;
    $Data_trip_help_detail['iUserId'] = $iMemberId;
    $Data_trip_help_detail['iHelpDetailId'] = $iHelpDetailId;
    $Data_trip_help_detail['vComment'] = $vComment;
    $Data_trip_help_detail['tDate'] = $current_date;
    $id = $obj->MySQLQueryPerform('trip_help_detail', $Data_trip_help_detail, 'insert');
    if ($id > 0) {
        $vOrderNo = get_value('orders', 'vOrderNo', 'iOrderId', $iOrderId, '', 'true');
        $maildata['iTripId'] = $vOrderNo;
        $maildata['NAME'] = $Data[0]['Name'];
        $maildata['vComment'] = $vComment;
        $maildata['Ddate'] = $current_date;
        $COMM_MEDIA_OBJ->SendMailToMember("USER_ORDER_HELP_DETAIL", $maildata);
        $returnArr['Action'] = "1";
        $returnArr['message'] = "LBL_COMMENT_ADDED_TXT";
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
    }
    setDataResponse($returnArr);
}
// ############################ End submitTripHelpDetail ############################################################
// ############################ Check Available Restaurants ############################################################
if ($type == "loadAvailableRestaurants") {
    $page = isset($_REQUEST['page']) ? trim($_REQUEST['page']) : 1;
    $iUserId = isset($_REQUEST["iUserId"]) ? $_REQUEST["iUserId"] : '';
    $passengerLat = isset($_REQUEST["PassengerLat"]) ? $_REQUEST["PassengerLat"] : '';
    $passengerLon = isset($_REQUEST["PassengerLon"]) ? $_REQUEST["PassengerLon"] : '';
    $fOfferType = isset($_REQUEST["fOfferType"]) ? $_REQUEST["fOfferType"] : ''; // Yes Or No
    $cuisineId = isset($_REQUEST["cuisineId"]) ? $_REQUEST["cuisineId"] : ''; // 1,2,3
    $orderby = isset($_REQUEST["orderby"]) ? $_REQUEST["orderby"] : ''; // 1,2,3
    $iCategoryId = isset($_REQUEST["iCategoryId"]) ? $_REQUEST["iCategoryId"] : ''; // 1,2,3
    $vUserDeviceCountry = isset($_REQUEST["vUserDeviceCountry"]) ? $_REQUEST["vUserDeviceCountry"] : '';
    $vUserDeviceCountry = strtoupper($vUserDeviceCountry);
    $vAddress = isset($_REQUEST["vAddress"]) ? $_REQUEST["vAddress"] : '';
    $sortby = isset($_REQUEST["sortby"]) ? $_REQUEST["sortby"] : 'relevance'; // relevance , rating, time, costlth, costhtl
    $searchword = isset($_REQUEST["searchword"]) ? $_REQUEST["searchword"] : '';
    $searchword = strtolower($searchword);
    if ($searchword == "" || $searchword == NULL) {
        $searchword = "";
    }
    $cuisineId_arr = array();
    if ($cuisineId != "") {
        $cuisineId_arr = explode(",", $cuisineId);
    }
    if ($vAddress != "") {
        $vAddress_arr = explode(",", $vAddress);
        $vAddress = end($vAddress_arr);
        $vAddress = trim($vAddress);
    }
    ## Update Demo User's Lat Long As per User's Location ##
    if (SITE_TYPE == "Demo" && $iUserId != "") {
        //Added By HJ On 14-07-2020 For Optimization register_user Table Query Start
        $tblName = "register_user";
        if (isset($userDetailsArr[$tblName . "_" . $iUserId]) && count($userDetailsArr[$tblName . "_" . $iUserId]) > 0) {
            $userData = $userDetailsArr[$tblName . "_" . $iUserId];
        }
        else {
            $userData = $obj->MySQLSelect("SELECT *,iUserId as iMemberId FROM " . $tblName . " WHERE iUserId='" . $iUserId . "'");
            $userDetailsArr[$tblName . "_" . $iUserId] = $userData;
        }
        $uemail = $userData[0]['vEmail'];
        //Added By HJ On 14-07-2020 For Optimization register_user Table Query End
        //$uemail = get_value('register_user', 'vEmail', 'iUserId', $iUserId, '', 'true');
        $uemail = explode("-", $uemail);
        $uemail = $uemail[1];
        if ($uemail != "") {
            $sql = "SELECT GROUP_CONCAT(iCompanyId)as companyId FROM company WHERE vEmail LIKE '%$uemail%' AND iServiceId = $iServiceId";
            $db_rec = $obj->MySQLSelect($sql);
            $usercompanyId = $db_rec[0]['companyId'];
            if ($usercompanyId != "") {
                $vLatitude = 'vRestuarantLocationLat';
                $vLongitude = 'vRestuarantLocationLong';
                $sql = "SELECT ROUND(( 6371 * acos( cos( radians(" . $passengerLat . ") )
              * cos( radians( ROUND(" . $vLatitude . ",8) ) )
                * cos( radians( ROUND(" . $vLongitude . ",8) ) - radians(" . $passengerLon . ") )
                    + sin( radians(" . $passengerLat . ") )
                * sin( radians( ROUND(" . $vLatitude . ",8) ) ) ) ),2) AS distance, company.*  FROM `company`
                    WHERE (" . $vLatitude . " != '' AND " . $vLongitude . " != '' ) AND iCompanyId IN ($usercompanyId) AND eStatus = 'Active' AND iServiceId = $iServiceId
                    HAVING distance < " . $USER_STORE_RANGE . " ORDER BY distance ASC LIMIT 0,1";
                $Data = $obj->MySQLSelect($sql);
                if (count($Data) == 0) {
                    $sql = "SELECT iCompanyId FROM company WHERE iCompanyId IN ($usercompanyId) AND eStatus = 'Active' AND iServiceId = '" . $iServiceId . "' AND eStoreLocationUpdate = 'NO' LIMIT 0,1";
                    $CompanyData = $obj->MySQLSelect($sql);
                    $CurrentDate = date("Y-m-d H:i:s");
                    if (count($CompanyData) > 0) {
                        $updateCompanyId = $CompanyData[0]['iCompanyId'];
                        $updateQuery = "UPDATE company SET vRestuarantLocationLat='" . $passengerLat . "', vRestuarantLocationLong = '" . $passengerLon . "', eStoreLocationUpdate = 'Yes', eStoreLocationUpdateDateTime = '" . $CurrentDate . "' WHERE iCompanyId = '" . $updateCompanyId . "'";
                        $obj->sql_query($updateQuery);
                    }
                    else {
                        $sql = "SELECT iCompanyId FROM company WHERE iCompanyId IN ($usercompanyId) AND eStatus = 'Active' AND iServiceId = '" . $iServiceId . "' AND eStoreLocationUpdate = 'Yes' ORDER BY eStoreLocationUpdateDateTime ASC LIMIT 0,1";
                        $NewCompanyData = $obj->MySQLSelect($sql);
                        $newupdateCompanyId = $NewCompanyData[0]['iCompanyId'];
                        $updateQuery = "UPDATE company SET vRestuarantLocationLat='" . $passengerLat . "', vRestuarantLocationLong = '" . $passengerLon . "',  eStoreLocationUpdateDateTime = '" . $CurrentDate . "' WHERE iCompanyId = '" . $newupdateCompanyId . "'";
                        $obj->sql_query($updateQuery);
                    }
                }
            }
        }
    }
    ## Update Demo User's Lat Long As per User's Location ##
    $Data = FetchNearByStores($passengerLat, $passengerLon, $iUserId, $fOfferType, $searchword, $vAddress, $iServiceId);

    $totalsearchcuisinerestaurants = 0;
    $Data = array_values($Data);
    $dataNewArr = array();
    $countdata = count($Data);
    for ($c = 0; $c < $countdata; $c++) {
        unset($Data[$c]['vPassword']);
        unset($Data[$c]['vPasswordToken']);
        unset($Data[$c]['vPassword_token']);
        $iCompanyId = $Data[$c]['iCompanyId'];
        if ($Data[$c]['vImage'] != "") {
            $Data[$c]['vImage'] = $tconfig['tsite_upload_images_compnay'] . '/' . $iCompanyId . '/3_' . $Data[$c]['vImage'];
        }
        //Added By HJ On 26-06-2019 For Get And Display Store Demo Image Start
        if (isset($Data[$c]['vDemoStoreImage']) && $Data[$c]['vDemoStoreImage'] != "" && SITE_TYPE == "Demo") {
            $demoImgPath = $tconfig['tsite_upload_demo_compnay_doc_path'] . $Data[$c]['vDemoStoreImage'];
            if (file_exists($demoImgPath)) {
                $demoImgUrl = $tconfig['tsite_upload_demo_compnay_doc'] . $Data[$c]['vDemoStoreImage'];
                $Data[$c]['vImage'] = $demoImgUrl;
            }
        }
        //Added By HJ On 26-06-2019 For Get And Display Store Demo Image End
        if ($Data[$c]['vCoverImage'] != "") {
            $Data[$c]['vCoverImage'] = $tconfig['tsite_upload_images_compnay'] . '/' . $iCompanyId . '/' . $Data[$c]['vCoverImage'];
        }
        $isRemoveRestaurantIntoList = "No";
        // # Checking For Selected Cuisine ##
        $Restaurant_Cuisine_Id_str = $Data[$c]['Restaurant_Cuisine_Id'];
        $Restaurant_Cuisine_Id_arr = explode(",", $Restaurant_Cuisine_Id_str);
        $match_cusisine_result_arr = array_intersect($cuisineId_arr, $Restaurant_Cuisine_Id_arr);
        if (count($match_cusisine_result_arr) == 0 && count($cuisineId_arr) > 0) {
            $isRemoveRestaurantIntoList = "Yes";
        }
        // # Checking For Selected Cuisine ##
        // # Checking For Search Keyword ##
        $vCompany = strtolower($Data[$c]['vCompany']);
        $Restaurant_Cuisine = strtolower($Data[$c]['Restaurant_Cuisine']);
        if (((!preg_match("/$searchword/i", $vCompany)) && (!preg_match("/$searchword/i", $Restaurant_Cuisine))) && $searchword != "") {
            $isRemoveRestaurantIntoList = "Yes";
        }
        // # Checking For Search Keyword ##
        // # Getting Nos of restaurants matching with cuisine searchtext ##
        if (preg_match("/$searchword/i", $Restaurant_Cuisine) && $searchword != "") {
            $totalsearchcuisinerestaurants = $totalsearchcuisinerestaurants + 1;
        }
        // # Getting Nos of restaurants matching with cuisine searchtext ##
        // # Checking For Food Menu Available for Company Or Not ##
        $CompanyFoodDataCount = $Data[$c]['CompanyFoodDataCount'];
        if ($CompanyFoodDataCount == 0) {
            $isRemoveRestaurantIntoList = "Yes";
        }
        // # Checking For Food Menu Available for Company Or Not ##
        if ($isRemoveRestaurantIntoList != "Yes") {
            $dataNewArr[] = $Data[$c];
        }
        $fsql1 = "";
        if ($MODULES_OBJ->isEnableStoreMultiServiceCategories()) {
            $fsql1 = " AND iServiceId = '$iServiceId'";
        }
        $getFooMenu = $obj->MySQLSelect("SELECT GROUP_CONCAT(iFoodMenuId) as foodMenuIds FROM food_menu WHERE iCompanyId = '" . $Data[$c]['iCompanyId'] . "' AND eStatus = 'Active' $fsql1");
        if (!empty($getFooMenu[0]['foodMenuIds'])) {
            $getStoreItems = $obj->MySQLSelect("SELECT COUNT(iMenuItemId) as menuItemCount FROM menu_items WHERE iFoodMenuId IN (" . $getFooMenu[0]['foodMenuIds'] . ") AND eStatus = 'Active'");
            if ($getStoreItems[0]['menuItemCount'] == 0) {
                unset($Data[$c]);
            }
        }
        else {
            unset($Data[$c]);
        }
    }
    if ($cuisineId != "") {
        $Data = $dataNewArr;
    }
    $Data_Filter = $Data;
    $Data = array_values($Data_Filter);
    // ## Sorting Of Restaurants by relevance , rating, time, costlth, costhtl ###
    if ($sortby == "" || $sortby == NULL) {
        $sortby = "relevance";
    }
    if ($sortby == "rating") {
        $sortfield = "vAvgRatingOrig";
        $sortorder = SORT_DESC;
    }
    elseif ($sortby == "time") {
        $sortfield = "fPrepareTime";
        $sortorder = SORT_ASC;
    }
    elseif ($sortby == "costlth") {
        $sortfield = "fPricePerPerson";
        $sortorder = SORT_ASC;
    }
    elseif ($sortby == "costhtl") {
        $sortfield = "fPricePerPerson";
        $sortorder = SORT_DESC;
    }
    else {
        $sortfield = "restaurantstatus";
        $sortorder = SORT_DESC;
    }
    foreach ($Data as $k => $v) {
        $Data_name[$sortfield][$k] = $v[$sortfield];
        $Data_name['restaurantstatus'][$k] = $v['restaurantstatus'];
    }
    array_multisort($Data_name['restaurantstatus'], SORT_DESC, $Data_name[$sortfield], $sortorder, $Data);
    // ## Sorting Of Restaurants by relevance , rating, time, costlth, costhtl ###
    // ## Sorting Of Demo User Restaurant To Display First ###
    $searchbydemousercompany = "No";
    if (SITE_TYPE == "Demo" && $iUserId != "") {
        //Added By HJ On 14-07-2020 For Optimization register_user Table Query Start
        $tblName = "register_user";
        if (isset($userDetailsArr[$tblName . "_" . $iUserId]) && count($userDetailsArr[$tblName . "_" . $iUserId]) > 0) {
            $userData = $userDetailsArr[$tblName . "_" . $iUserId];
        }
        else {
            $userData = $obj->MySQLSelect("SELECT *,iUserId as iMemberId FROM " . $tblName . " WHERE iUserId='" . $iUserId . "'");
            $userDetailsArr[$tblName . "_" . $iUserId] = $userData;
        }
        $useremail = $userData[0]['vEmail'];
        //Added By HJ On 14-07-2020 For Optimization register_user Table Query End
        //$useremail = get_value('register_user', 'vEmail', 'iUserId', $iUserId, '', 'true');
        $useremail = explode("-", $useremail);
        if (count($useremail) > 0) {
            $searchbydemousercompany = "Yes";
            $useremail = $useremail[1];
            for ($k = 0; $k < count($Data); $k++) {
                $companyemail = $Data[$k]['vEmail'];
                if (preg_match("/$useremail/", $companyemail)) {
                    $Data[$k]['eDemoUserCompany'] = "Yes";
                }
                else {
                    $Data[$k]['eDemoUserCompany'] = "No";
                }
            }
        }
    }
    if ($searchbydemousercompany == "Yes") {
        
        usort($Data, function ($a, $b) {
            if ($a["eDemoUserCompany"] == $b["eDemoUserCompany"]) {
                return 0;
            }
            return ($a["eDemoUserCompany"] < $b["eDemoUserCompany"]) ? 1 : -1;
        });

        $newData = array();
        $newData = $Data;
        for ($j = 0; $j < count($Data); $j++) {
            if ($Data[$j]['eDemoUserCompany'] == "Yes") {
                if ($j != 0) {
                    //unset($newData[$j]);
                }
            }
        }
        $Data = array_values($newData);
    }
    // ## Sorting Of Demo User Restaurant To Display First ###
    // ## Checking For Pagination ###
    $Data_new = array_values($Data);
    if ($iCategoryId != "" && $iCategoryId > 0) {
        $Data = $storeCatAccArr = $s_sctSqlData = $storeTagsAccArr = array();
        //Added By HJ On 14-07-2020 For Optimize store_categories Table Query Start
        $storeCatData = $obj->MySQLSelect("SELECT * FROM store_categories");
        for ($h = 0; $h < count($storeCatData); $h++) {
            if ($storeCatData[$h]['iCategoryId'] == $iCategoryId) {
                $s_sctSqlData = array();
                $s_sctSqlData[] = $storeCatData[$h];
            }
            $storeCatAccArr[$storeCatData[$h]['iServiceId']][$storeCatData[$h]['eType']][] = $storeCatData[$h];
        }
        if (count($s_sctSqlData) == 0) {
            $s_sctSqlData = $obj->MySQLSelect("select iCategoryId,eType,iServiceId from store_categories where iCategoryId = " . $iCategoryId);
        }
        //Added By HJ On 14-07-2020 For Optimize store_categories Table Query End
        //Added By HJ On 14-07-2020 For Optimize store_categories Table Query Start
        $storCattagsData = $obj->MySQLSelect("SELECT iCategoryId,iCompanyId from store_category_tags");
        for ($g = 0; $g < count($storCattagsData); $g++) {
            $storeTagsAccArr[$storCattagsData[$g]['iCompanyId']][$storCattagsData[$g]['iCategoryId']][] = $storCattagsData[$g];
        }
        //Added By HJ On 14-07-2020 For Optimize store_categories Table Query End
        foreach ($Data_new as $dkey => $dvalue) {
            if ($s_sctSqlData[0]['eType'] == 'newly_open' && $s_sctSqlData[0]['iServiceId'] == $iServiceId) {
                $date1 = date('Y-m-d H:i:s');
                $date2 = $dvalue['tRegistrationDate'];
                $diff = strtotime($date2) - strtotime($date1);
                $diff_days = abs(round($diff / 86400));
                //$sctSql = "select iDaysRange from store_categories where eType='newly_open' AND iServiceId=" . $iServiceId;
                $sctDaysRange = ($sctSql_data[0]['iDaysRange'] != "") ? $sctSql_data[0]['iDaysRange'] : 30;
                if ($diff_days <= $sctDaysRange) {
                    $Data[] = $dvalue;
                }
            }
            else if ($s_sctSqlData[0]['eType'] == 'offers' && $s_sctSqlData[0]['iServiceId'] == $iServiceId) {
                if ($dvalue['fOfferAppyType'] != "None") {
                    $Data[] = $dvalue;
                }
            }
            else if ($s_sctSqlData[0]['eType'] == 'list_all' && $s_sctSqlData[0]['iServiceId'] == $iServiceId) {
                $Data[] = $dvalue;
            }
            else {
                //$storCattagsSql = "select iCategoryId from store_category_tags where iCompanyId = " . $dvalue['iCompanyId'] . " AND iCategoryId = " . $iCategoryId;
                //$storCattagsData = $obj->MySQLSelect($storCattagsSql);
                $storCattagsData = array();
                if (isset($storeTagsAccArr[$dvalue['iCompanyId']][$iCategoryId])) {
                    $storCattagsData = $storeTagsAccArr[$dvalue['iCompanyId']][$iCategoryId];
                }
                if (count($storCattagsData)) {
                    $Data[] = $dvalue;
                }
            }
        }
    }
    $Data_new = array_values($Data);
    $per_page = 12;
    $totalStore = count($Data); //Added By HJ On 18-01-2020 As Per Discuss Between CS and KS Sir
    $TotalPages = ceil(count($Data) / $per_page);
    $pagecount = $page - 1;
    $start_limit = $pagecount * $per_page;
    $Data = array_slice($Data_new, $start_limit, $per_page);
    //$Data = $Data_new;
    $ispriceshow = '';
    $servFields = 'eType';
    $ServiceCategoryData = get_value('service_categories', $servFields, 'iServiceId', $iServiceId);
    if (!empty($ServiceCategoryData)) {
        if (!empty($ServiceCategoryData[0]['eType'])) {
            $ispriceshow = $ServiceCategoryData[0]['eType'];
        }
    }
    // ## Checking For Pagination ###
    $returnArr['totalStore'] = $totalStore; //Added By HJ On 18-01-2020 As Per Discuss Between CS and KS Sir
    if (!empty($Data)) {
        $returnArr['Action'] = "1";
        $returnArr['message'] = $Data;
        if ($TotalPages > $page) {
            $returnArr['NextPage'] = $page + 1;
        }
        else {
            $returnArr['NextPage'] = "0";
        }
        $storeCatIserviceId = $iServiceId;
        if ($MODULES_OBJ->isStoreClassificationEnable() && $iCategoryId == "") {
            //lang code same as staticpage type as told by KS..
            $vLangCode = "";
            $vGeneralLang = isset($_REQUEST["vGeneralLang"]) ? $_REQUEST["vGeneralLang"] : '';
            $vLang = isset($_REQUEST["vLang"]) ? $_REQUEST["vLang"] : '';
            if (!empty($vGeneralLang)) {
                $vLangCode = $vGeneralLang;
            }
            else if (!empty($vLang)) {
                $vLangCode = $vLang;
            }
            else if (!empty($iUserId)) {
                //Added By HJ On 14-07-2020 For Optimization register_user Table Query Start
                $tblName = "register_user";
                if (isset($userDetailsArr[$tblName . "_" . $iUserId]) && count($userDetailsArr[$tblName . "_" . $iUserId]) > 0) {
                    $userData = $userDetailsArr[$tblName . "_" . $iUserId];
                }
                else {
                    $userData = $obj->MySQLSelect("SELECT *,iUserId as iMemberId FROM " . $tblName . " WHERE iUserId='" . $iUserId . "'");
                    $userDetailsArr[$tblName . "_" . $iUserId] = $userData;
                }
                $vLangCode = $userData[0]['vLang'];
                //Added By HJ On 14-07-2020 For Optimization register_user Table Query End
                //$vLangCode = get_value('register_user', 'vLang', 'iUserId', $iUserId, '', 'true');
            }
            if ($vLangCode == "" || $vLangCode == NULL) {
                //Added By HJ On 14-07-2020 For Optimize language_master Table Query Start
                $vLangCode = $LANG_OBJ->FetchDefaultLangData("vCode");
                //Added By HJ On 14-07-2020 For Optimize language_master Table Query End
            }
            // Store Categories
            $returnArr['CategoryWiseStores'] = $storeTagsAccArr = $storeCatAccArr = $storeCatServiceArr = array();
            //Added By HJ On 14-07-2020 For Optimize store_categories Table Query Start
            $storCattagsData = $obj->MySQLSelect("SELECT iCategoryId,iCompanyId from store_category_tags");
            for ($g = 0; $g < count($storCattagsData); $g++) {
                $storeTagsAccArr[$storCattagsData[$g]['iCompanyId']][] = $storCattagsData[$g];
            }
            //Added By HJ On 14-07-2020 For Optimize store_categories Table Query End
            //Added By HJ On 14-07-2020 For Optimize store_categories Table Query Start
            $storeCatData = $obj->MySQLSelect("SELECT iServiceId,iCategoryId,JSON_UNQUOTE(JSON_VALUE(tCategoryName, '$.tCategoryName_" . $vLangCode . "')) as tCategoryName,JSON_UNQUOTE(JSON_VALUE(tCategoryDescription, '$.tCategoryDescription_" . $vLangCode . "')) as tCategoryDescription,tCategoryImage,iDisplayOrder,iDaysRange,eType FROM store_categories WHERE eStatus = 'Active' AND iServiceId = '$iServiceId'");
            for ($h = 0; $h < count($storeCatData); $h++) {
                $storeCatServiceArr[$storeCatData[$h]['iServiceId']][$storeCatData[$h]['eType']][] = $storeCatData[$h];
                $storeCatAccArr[$storeCatData[$h]['iCategoryId']][] = $storeCatData[$h];
            }
            //Added By HJ On 14-07-2020 For Optimize store_categories Table Query End
            foreach ($Data_new as $dkey => $dvalue) {
                //$storCattagsData = $obj->MySQLSelect("SELECT iCategoryId from store_category_tags where iCompanyId = " . $dvalue['iCompanyId']);
                $storCattagsData = array();
                if (isset($storeTagsAccArr[$dvalue['iCompanyId']])) {
                    $storCattagsData = $storeTagsAccArr[$dvalue['iCompanyId']];
                }
                if (count($storCattagsData)) {
                    foreach ($storCattagsData as $sctvalue) {
                        //$store_cat_sql = "select iCategoryId,JSON_UNQUOTE(JSON_VALUE(tCategoryName, '$.tCategoryName_" . $vLangCode . "')) as tCategoryName,JSON_UNQUOTE(JSON_VALUE(tCategoryDescription, '$.tCategoryDescription_" . $vLangCode . "')) as tCategoryDescription,tCategoryImage,iDisplayOrder,eType from store_categories where iCategoryId = " . $sctvalue['iCategoryId'] . " AND eStatus = 'Active'";
                        //$store_cat_sql_data = $obj->MySQLSelect($store_cat_sql);
                        $store_cat_sql_data = array();
                        if (isset($storeCatAccArr[$sctvalue['iCategoryId']])) {
                            $store_cat_sql_data = $storeCatAccArr[$sctvalue['iCategoryId']];
                        }
                        foreach ($store_cat_sql_data as $sctdata) {
                            $sctName = $sctdata['tCategoryName'];
                            $sctDesc = $sctdata['tCategoryDescription'];
                            $sctDataId = $sctdata['iCategoryId'];
                            $tCategoryImage = $sctdata['tCategoryImage'];
                            $eType = $sctdata['eType'];
                            if (count($returnArr['CategoryWiseStores']) > 0) {
                                $getTitlekey = searchStoreCategoryTitle($sctName, $returnArr['CategoryWiseStores']);
                                if ($getTitlekey > -1) {
                                    if (count($returnArr['CategoryWiseStores'][$getTitlekey]['subData']) < 12) {
                                        $returnArr['CategoryWiseStores'][$getTitlekey]['subData'][] = $dvalue;
                                        $returnArr['CategoryWiseStores'][$getTitlekey]['countdata'] = count($returnArr['CategoryWiseStores'][$getTitlekey]['subData']);
                                    }
                                    else {
                                        $newdata['totaldata'][] = $dvalue;
                                        $returnArr['CategoryWiseStores'][$getTitlekey]['countdata'] = count($returnArr['CategoryWiseStores'][$getTitlekey]['subData']) + count($newdata['totaldata']);
                                    }
                                }
                                else {
                                    $returnArr['CategoryWiseStores'][] = array(
                                        'iCategoryId' => $sctDataId, 'vTitle' => $sctName, 'vDescription' => ($sctDesc != "") ? $sctDesc : "", 'vCategoryImage' => ($tCategoryImage != '') ? ($tconfig['tsite_upload_images_store_categories'] . "/" . $tCategoryImage) : "", 'iDisplayOrder' => $sctdata['iDisplayOrder'], 'eType' => $eType, 'subData' => array($dvalue)
                                    );
                                }
                            }
                            else {
                                $returnArr['CategoryWiseStores'][] = array(
                                    'iCategoryId' => $sctDataId, 'vTitle' => $sctName, 'vDescription' => ($sctDesc != "") ? $sctDesc : "", 'vCategoryImage' => ($tCategoryImage != '') ? ($tconfig['tsite_upload_images_store_categories'] . "/" . $tCategoryImage) : "", 'iDisplayOrder' => $sctdata['iDisplayOrder'], 'eType' => $eType, 'subData' => array($dvalue)
                                );
                            }
                        }
                    }
                }
                // Offers - Stores/Restaurants
                if ($dvalue['fOfferAppyType'] != "None") {
                    //$sctSql = "select iCategoryId,JSON_UNQUOTE(JSON_VALUE(tCategoryName, '$.tCategoryName_" . $vLangCode . "')) as tCategoryName,JSON_UNQUOTE(JSON_VALUE(tCategoryDescription, '$.tCategoryDescription_" . $vLangCode . "')) as tCategoryDescription,tCategoryImage,iDisplayOrder,eType from store_categories where eType = 'offers' AND iServiceId = " . $storeCatIserviceId . " AND eStatus = 'Active'";
                    //$sctSql_data = $obj->MySQLSelect($sctSql);
                    $sctSql_data = array();
                    if (isset($storeCatServiceArr[$storeCatIserviceId]['offers'])) {
                        $sctSql_data = $storeCatServiceArr[$storeCatIserviceId]['offers'];
                    }
                    $sctNameOffer = $sctSql_data[0]['tCategoryName'];
                    $sctDescOffer = $sctSql_data[0]['tCategoryDescription'];
                    $sctDataId = $sctSql_data[0]['iCategoryId'];
                    $getTitlekey = searchStoreCategoryTitle($sctNameOffer, $returnArr['CategoryWiseStores']);
                    if ($getTitlekey > -1) {
                        if (count($returnArr['CategoryWiseStores'][$getTitlekey]['subData']) < 12) {
                            $returnArr['CategoryWiseStores'][$getTitlekey]['subData'][] = $dvalue;
                            $returnArr['CategoryWiseStores'][$getTitlekey]['countdata'] = count($returnArr['CategoryWiseStores'][$getTitlekey]['subData']);
                        }
                        else {
                            $newdata['totaldata'][] = $dvalue;
                            $returnArr['CategoryWiseStores'][$getTitlekey]['countdata'] = count($returnArr['CategoryWiseStores'][$getTitlekey]['subData']) + count($newdata['totaldata']);
                        }
                    }
                    else {
                        $returnArr['CategoryWiseStores'][] = array(
                            'iCategoryId' => $sctDataId, 'vTitle' => $sctNameOffer, 'vDescription' => ($sctDescOffer != "") ? $sctDescOffer : "", 'vCategoryImage' => ($sctSql_data[0]['tCategoryImage'] != "") ? ($tconfig['tsite_upload_images_store_categories'] . "/" . $sctSql_data[0]['tCategoryImage']) : "", 'iDisplayOrder' => $sctSql_data[0]['iDisplayOrder'], 'eType' => $sctSql_data[0]['eType'], 'subData' => array($dvalue)
                        );
                    }
                }
                // Newly Open Stores/Restaurants
                $date1 = date('Y-m-d H:i:s');
                $date2 = $dvalue['tRegistrationDate'];
                $diff = strtotime($date2) - strtotime($date1);
                $diff_days = abs(round($diff / 86400));
                //$sctSql = "select iCategoryId,JSON_UNQUOTE(JSON_VALUE(tCategoryName, '$.tCategoryName_" . $vLangCode . "')) as tCategoryName,JSON_UNQUOTE(JSON_VALUE(tCategoryDescription, '$.tCategoryDescription_" . $vLangCode . "')) as tCategoryDescription,tCategoryImage,iDisplayOrder,iDaysRange,eType from store_categories where eType = 'newly_open' AND iServiceId = " . $storeCatIserviceId . " AND eStatus = 'Active'";
                //$sctSql_data = $obj->MySQLSelect($sctSql);
                $sctSql_data = array();
                if (isset($storeCatServiceArr[$storeCatIserviceId]['newly_open'])) {
                    $sctSql_data = $storeCatServiceArr[$storeCatIserviceId]['newly_open'];
                }
                $sctNameNew = $sctSql_data[0]['tCategoryName'];
                $sctDescNew = $sctSql_data[0]['tCategoryDescription'];
                $sctDataId = $sctSql_data[0]['iCategoryId'];
                $sctDaysRange = ($sctSql_data[0]['iDaysRange'] != "") ? $sctSql_data[0]['iDaysRange'] : 30;
                if ($diff_days <= $sctDaysRange) {
                    $getTitlekey = searchStoreCategoryTitle($sctNameNew, $returnArr['CategoryWiseStores']);
                    if ($getTitlekey > -1) {
                        if (count($returnArr['CategoryWiseStores'][$getTitlekey]['subData']) < 12) {
                            $returnArr['CategoryWiseStores'][$getTitlekey]['subData'][] = $dvalue;
                            $returnArr['CategoryWiseStores'][$getTitlekey]['countdata'] = count($returnArr['CategoryWiseStores'][$getTitlekey]['subData']);
                        }
                        else {
                            $newdata['totaldata'][] = $dvalue;
                            $returnArr['CategoryWiseStores'][$getTitlekey]['countdata'] = count($returnArr['CategoryWiseStores'][$getTitlekey]['subData']) + count($newdata['totaldata']);
                        }
                    }
                    else {
                        $returnArr['CategoryWiseStores'][] = array(
                            'iCategoryId' => $sctDataId, 'vTitle' => $sctNameNew, 'vDescription' => ($sctDescNew != "") ? $sctDescNew : "", 'vCategoryImage' => ($sctSql_data[0]['tCategoryImage'] != "") ? ($tconfig['tsite_upload_images_store_categories'] . "/" . $sctSql_data[0]['tCategoryImage']) : "", 'iDisplayOrder' => $sctSql_data[0]['iDisplayOrder'], 'eType' => $sctSql_data[0]['eType'], 'subData' => array($dvalue)
                        );
                    }
                }
            }
            // All Stores/Restaurants
            //$storCatAllsql = "select iCategoryId,JSON_UNQUOTE(JSON_VALUE(tCategoryName, '$.tCategoryName_" . $vLangCode . "')) as tCategoryName,JSON_UNQUOTE(JSON_VALUE(tCategoryDescription, '$.tCategoryDescription_" . $vLangCode . "')) as tCategoryDescription,tCategoryImage,iDisplayOrder,eType from store_categories where eType = 'list_all' AND iServiceId = " . $storeCatIserviceId;
            //$storCatAlldata = $obj->MySQLSelect($storCatAllsql);
            $storCatAlldata = array();
            if (isset($storeCatServiceArr[$storeCatIserviceId]['list_all'])) {
                $storCatAlldata = $storeCatServiceArr[$storeCatIserviceId]['list_all'];
            }
            $sctNameAll = $storCatAlldata[0]['tCategoryName'];
            $sctDescAll = $storCatAlldata[0]['tCategoryDescription'];
            $sctDataId = $storCatAlldata[0]['iCategoryId'];
            $returnArr['CategoryWiseStores'][] = array(
                'iCategoryId' => $sctDataId, 'vTitle' => $sctNameAll, 'vDescription' => ($sctDescAll != "") ? $sctDescAll : "", 'vCategoryImage' => ($storCatAlldata[0]['tCategoryImage'] != "") ? ($tconfig['tsite_upload_images_store_categories'] . "/" . $storCatAlldata[0]['tCategoryImage']) : "", 'iDisplayOrder' => $storCatAlldata[0]['iDisplayOrder'], 'eType' => $storCatAlldata[0]['eType'], 'subData' => $Data
            );
            usort($returnArr['CategoryWiseStores'], function ($a, $b) {
                return $a["iDisplayOrder"] - $b["iDisplayOrder"];
            });
            foreach ($returnArr['CategoryWiseStores'] as $catkey => $catvalue) {
                if ($returnArr['CategoryWiseStores'][$catkey]['eType'] == "list_all") {
                    if ($totalStore >= 13) {
                        $returnArr['CategoryWiseStores'][$catkey]['IS_SHOW_ALL'] = "Yes";
                    }
                    else {
                        $returnArr['CategoryWiseStores'][$catkey]['IS_SHOW_ALL'] = "No";
                    }
                }
                else {
                    //$countSubData = count($returnArr['CategoryWiseStores'][$catkey]['subData']);
                    $countSubData = $returnArr['CategoryWiseStores'][$catkey]['countdata'];
                    if ($countSubData >= 13) {
                        $returnArr['CategoryWiseStores'][$catkey]['IS_SHOW_ALL'] = "Yes";
                    }
                    else {
                        $returnArr['CategoryWiseStores'][$catkey]['IS_SHOW_ALL'] = "No";
                    }
                }
                if ($returnArr['CategoryWiseStores'][$catkey]['vTitle'] != $sctNameAll) {
                    shuffle($returnArr['CategoryWiseStores'][$catkey]['subData']);
                    $shuffled_arr = $returnArr['CategoryWiseStores'][$catkey]['subData'];
                    $movetolast = array();
                    foreach ($shuffled_arr as $mkey => $mvalue) {
                        if (strtolower($mvalue['restaurantstatus']) == 'closed') {
                            $movetolast[] = $shuffled_arr[$mkey];
                            unset($shuffled_arr[$mkey]);
                        }
                    }
                    $returnArr['CategoryWiseStores'][$catkey]['subData'] = array_merge($shuffled_arr, $movetolast);
                }
                if ($returnArr['CategoryWiseStores'][$catkey]['iCategoryId'] == "") {
                    unset($returnArr['CategoryWiseStores'][$catkey]);
                }
            }
        }
        $returnArr['CategoryWiseStores'] = array_values($returnArr['CategoryWiseStores']);
        $returnArr['totalsearchcuisinerestaurants'] = $totalsearchcuisinerestaurants;
        $returnArr['ispriceshow'] = $ispriceshow;
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_NO_RESTAURANT_FOUND_TXT";
        if ($MODULES_OBJ->isFavouriteStoreModuleAvailable() && !empty($iUserId)) {
            $eFavStore = isset($_REQUEST['eFavStore']) ? clean($_REQUEST['eFavStore']) : ''; // No=> 'Not
        }
        if ((!empty($fOfferType) && strtoupper($fOfferType) == "YES") || !empty($cuisineId) || (!empty($eFavStore) && strtoupper($eFavStore) == "YES")) {
            $returnArr['message1'] = "LBL_NO_RECORDS_FOUND1";
        }
    }
    //getBanners type start
    if ($iUserId != "") {
        //Added By HJ On 14-07-2020 For Optimization register_user Table Query Start
        $tblName = "register_user";
        if (isset($userDetailsArr[$tblName . "_" . $iUserId]) && count($userDetailsArr[$tblName . "_" . $iUserId]) > 0) {
            $userData = $userDetailsArr[$tblName . "_" . $iUserId];
        }
        else {
            $userData = $obj->MySQLSelect("SELECT *,iUserId as iMemberId FROM " . $tblName . " WHERE iUserId='" . $iUserId . "'");
            $userDetailsArr[$tblName . "_" . $iUserId] = $userData;
        }
        $vLanguage = $userData[0]['vLang'];
        //Added By HJ On 14-07-2020 For Optimization register_user Table Query End
        //$vLanguage = get_value('register_user', 'vLang', 'iUserId', $iUserId, '', 'true');
    }
    if ($vLanguage == "" || $vLanguage == NULL) {
        //Added By HJ On 14-07-2020 For Optimize language_master Table Query Start
        $vLanguage = $LANG_OBJ->FetchDefaultLangData("vCode");
        //Added By HJ On 14-07-2020 For Optimize language_master Table Query End
        //$vLanguage = get_value('language_master', 'vCode', 'eDefault', 'Yes', '', 'true');
    }
    // $banners= get_value('banners', 'vImage', 'vCode',$vLanguage,' ORDER BY iDisplayOrder ASC');
    $whereloc = " AND iLocationid IN ('-1')";
    if ($MODULES_OBJ->isEnableLocationwiseBanner()) {
        $User_Address_Banner = array($passengerLat, $passengerLon);
        if (!empty($User_Address_Banner)) {
            $iLocationIdBanner = GetUserGeoLocationIdBanner($User_Address_Banner);
            $country_str_banner = "'-1'";
            if (count($iLocationIdBanner) > 0) {
                foreach ($iLocationIdBanner as $key => $value) {
                    $country_str_banner .= ", '" . $value . "'";
                }
                $whereloc = " AND iLocationid IN (" . $country_str_banner . ")";
            }
        }
    }
    $sql = "SELECT vImage,vStatusBarColor,iUniqueId FROM `banners` WHERE vCode = '" . $vLanguage . "' AND eStatus = 'Active' AND iServiceId = '" . $iServiceId . "' $whereloc ORDER BY iDisplayOrder ASC";
    $banners = $obj->MySQLSelect($sql);
    $bdata = array();
    $count = 0;
    for ($i = 0; $i < count($banners); $i++) {
        if ($banners[$i]['vImage'] != "") {
            $bdata[$count]['vImage'] = $tconfig["tsite_url"] . 'assets/img/images/' . urlencode($banners[$i]['vImage']);
            $bdata[$count]['vStatusBarColor'] = (isset($banners[$i]['vStatusBarColor']) ? $banners[$i]['vStatusBarColor'] : '');
            $banner_img_path = $tconfig['tpanel_path'] . 'assets/img/images/' . $banners[$i]['vImage'];
            if (file_exists($banner_img_path) && empty($banners[$i]['vStatusBarColor'])) {
                $bdata[$count]['vStatusBarColor'] = getColorFromImage($banner_img_path);
                $obj->sql_query("UPDATE banners SET vStatusBarColor = '" . $bdata[$count]['vStatusBarColor'] . "' WHERE iUniqueId = '" . $banners[$i]['iUniqueId'] . "'");
            }
            $count++;
        }
    }
    $returnArr['banner_data'] = !empty($bdata) ? $bdata : '';
    //getBanners type end
    //getCuisineList type start
    $vGeneralLang = isset($_REQUEST["vGeneralLang"]) ? $_REQUEST["vGeneralLang"] : '';
    //$vLanguage = "";
    if (!empty($vGeneralLang)) {
        $vLanguage = $vGeneralLang;
    }
    if ($vLanguage == "" || $vLanguage == NULL) {
        //Added By HJ On 14-07-2020 For Optimize language_master Table Query Start
        $vLanguage = $LANG_OBJ->FetchDefaultLangData("vCode");
        //Added By HJ On 14-07-2020 For Optimize language_master Table Query End
    }
    $ssqllast = " `company`.`iCompanyId` ASC ";
    $Restaurant_Cuisine_Id_Arr = $db_cuisine_list = $db_cuisine_list_new = $db_cuisine_list_new1 = $languageLabelsArr = array();
    $Restaurant_Cuisine_Id_str = "";
    $sql = "SELECT DISTINCT (company.iCompanyId),ROUND(( 6371 * acos( cos( radians(" . $passengerLat . ") ) * cos( radians( vRestuarantLocationLat ) ) * cos( radians( vRestuarantLocationLong ) - radians(" . $passengerLon . ") ) + sin( radians(" . $passengerLat . ") ) * sin( radians( vRestuarantLocationLat ) ) ) ),2) AS distance, company.iCompanyId FROM `company` WHERE vRestuarantLocationLat != '' AND vRestuarantLocationLong != '' AND eStatus='Active' AND eSystem = 'DeliverAll' AND iServiceId = '" . $iServiceId . "' HAVING distance < " . $LIST_RESTAURANT_LIMIT_BY_DISTANCE . " ORDER BY " . $ssqllast . "";
    $cData = $obj->MySQLSelect($sql);
    $storeIdArr = array();
    for ($r = 0; $r < count($cData); $r++) {
        $storeIdArr[] = $cData[$r]['iCompanyId'];
    }
    //Added By HJ On 13-07-2020 For langauge labele and Other Union Table Query Start
    if (isset($languageLabelDataArr['language_label_union_other_food_' . $vLanguage . "_" . $iServiceId])) {
        $languageLabelsArr = $languageLabelDataArr['language_label_union_other_food_' . $vLanguage . "_" . $iServiceId];
    }
    else {
        $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLanguage, "1", $iServiceId);
        $languageLabelDataArr['language_label_union_other_food_' . $vLanguage . "_" . $iServiceId] = $languageLabelsArr;
    }
    //Added By HJ On 13-07-2020 For langauge labele and Other Union Table Query End
    $allStoreData = getcuisinelist($storeIdArr, $iUserId, $languageLabelsArr, $iServiceId);
    $Data_Company = $allStoreData['companyCuisineArr'];
    $offerMsgArr = $allStoreData['offerMsgArr'];
    $companyCuisineIdArr = $allStoreData['companyCuisineIdArr'];
    //$Data_Company = FetchNearByStores($passengerLat, $passengerLon, $iUserId, "No", "", "", $iServiceId);
    $isOfferApply = "No";
    if (count($Data_Company) > 0) {
        foreach ($Data_Company as $companyId => $cuisinArr) {
            $Restaurant_OfferMessage = "";
            if (isset($offerMsgArr[$companyId]['Restaurant_OfferMessage'])) {
                $Restaurant_OfferMessage = trim($offerMsgArr[$companyId]['Restaurant_OfferMessage']);
            }
            $restCuisineArr = array();
            if (isset($companyCuisineIdArr[$companyId])) {
                $restCuisineArr = $companyCuisineIdArr[$companyId];
            }
            if ($Restaurant_OfferMessage != "") {
                $isOfferApply = "Yes";
            }
            for ($d = 0; $d < count($restCuisineArr); $d++) {
                $Restaurant_Cuisine_Id_str .= $restCuisineArr[$d] . ",";
            }
        }
        //$Restaurant_Cuisine_Id_str = substr($Restaurant_Cuisine_Id_str, 0, -1);
        $Restaurant_Cuisine_Id_str = trim($Restaurant_Cuisine_Id_str, ",");
        $Restaurant_Cuisine_Id_Arr = explode(",", $Restaurant_Cuisine_Id_str);
    }
    $Restaurant_Cuisine_Id_Arr = array_unique($Restaurant_Cuisine_Id_Arr);
    //added by SP vImage for cubex on 12-10-2019
    $defaultImage = $tconfig["tsite_url"] . "webimages/upload/DefaultImg/food_service.png";
    //Added By HJ On 31-10-2019 For Set Cuisine Default Icon as Per Service Category Start Bug - 1382 141 Mantis
    if ($iServiceId != 1) {
        $defaultImage = $tconfig["tsite_url"] . "webimages/upload/DefaultImg/other_services.png";
    }
    //Added By HJ On 31-10-2019 For Set Cuisine Default Icon as Per Service Category End Bug - 1382 141 Mantis
    //$sql = "SELECT cuisineId,cuisineName_" . $vLanguage . " as cuisineName,eStatus,CONCAT('aa',vImage) as vImage  FROM cuisine WHERE iServiceId = '" . $iServiceId . "' AND eStatus = 'Active' ORDER BY cuisineName ASC";
    $sql = "SELECT cuisineId,cuisineName_$vLanguage as cuisineName,eStatus,CASE WHEN vImage != '' THEN CONCAT('" . $tconfig['tsite_upload_images_menu_item_type'] . "/',vImage) ELSE '" . $defaultImage . "' END AS vImage,vBgColor,vTextColor,vBorderColor FROM cuisine WHERE iServiceId = '" . $iServiceId . "' AND eStatus = 'Active' AND eDefault = 'No' ORDER BY iDisplayOrder ASC";
    $db_cuisine_list = $obj->MySQLSelect($sql);
    $isRemoveCuisineListArry = array();
    $db_cuisine_listNew = $db_cuisine_list;
    if (count($db_cuisine_list) > 0) {
        for ($i = 0; $i < count($db_cuisine_list); $i++) {
            $isRemoveCuisineList = "No";
            $cuisineId = $db_cuisine_list[$i]['cuisineId'];

            $vBgColor = RANDOM_COLORS_ARR[array_rand(RANDOM_COLORS_ARR, 1)];
            $vTextColor = $vBorderColor = RANDOM_COLORS_ARR[array_rand(RANDOM_COLORS_ARR, 1)];
            $db_cuisine_list[$i]['vBgColor'] = !empty($db_cuisine_list[$i]['vBgColor']) ? $db_cuisine_list[$i]['vBgColor'] : $vBgColor;
            $db_cuisine_list[$i]['vTextColor'] = !empty($db_cuisine_list[$i]['vTextColor']) ? $db_cuisine_list[$i]['vTextColor'] : $vTextColor;
            $db_cuisine_list[$i]['vBorderColor'] = !empty($db_cuisine_list[$i]['vBorderColor']) ? $db_cuisine_list[$i]['vBorderColor'] : $vBorderColor;
            if (!in_array($cuisineId, $Restaurant_Cuisine_Id_Arr)) {
                $isRemoveCuisineList = "Yes";
                $isRemoveCuisineListArry[] = $i;
            }
            // Code Commented by NM START

            // if ($isRemoveCuisineList == "Yes") {
            //         unset($db_cuisine_list[$i]);
            //     }
            // Code Commented by NM END
            // Code Added by NM START
            if ($isRemoveCuisineList == "Yes") {
                unset($db_cuisine_listNew[$i]);
            }
            // Code Added by NM END
        }
    }
    $db_cuisine_list = $db_cuisine_listNew;

    //added by SP for cubex to add all in cuisine list so when click on it show all restaurant on 15-10-2019
    $default_cuisine = $obj->MySQLSelect("SELECT cuisineId, cuisineName_$vLanguage as cuisineName, eStatus, vImage,vBgColor,vTextColor,vBorderColor FROM cuisine WHERE iServiceId = '$iServiceId' AND eDefault = 'Yes' ");
    if (isset($default_cuisine) && !empty($default_cuisine)) {
        $vBgColor = RANDOM_COLORS_ARR[array_rand(RANDOM_COLORS_ARR, 1)];
        $vTextColor = $vBorderColor = RANDOM_COLORS_ARR[array_rand(RANDOM_COLORS_ARR, 1)];
        $db_cuisine_list_new1[0]['cuisineId'] = '';
        $db_cuisine_list_new1[0]['cuisineName'] = $default_cuisine[0]['cuisineName'];
        $db_cuisine_list_new1[0]['eStatus'] = $default_cuisine[0]['eStatus'];
        $db_cuisine_list_new1[0]['vImage'] = $tconfig['tsite_upload_images_menu_item_type'] . '/' . $default_cuisine[0]['vImage'];
        $db_cuisine_list_new1[0]['vBgColor'] = !empty($default_cuisine[0]['vBgColor']) ? $default_cuisine[0]['vBgColor'] : $vBgColor;
        $db_cuisine_list_new1[0]['vTextColor'] = !empty($default_cuisine[0]['vTextColor']) ? $default_cuisine[0]['vTextColor'] : $vTextColor;
        $db_cuisine_list_new1[0]['vBorderColor'] = !empty($default_cuisine[0]['vBorderColor']) ? $default_cuisine[0]['vBorderColor'] : $vBorderColor;
    }
    //$allCuisines = count($db_cuisine_list_new);
    $db_cuisine_list_new = array_merge($db_cuisine_list_new1, $db_cuisine_list);
    $db_cuisine_list_new = array_values($db_cuisine_list_new);
    $db_cuisine_list = $db_cuisine_list_new;
    if (count($db_cuisine_list) == 0) {
        $db_cuisine_list = "";
    }
    $getItemData = "";
    //Added By HJ On 13-10-2020 For Get Item List For Item wise Search Functionality Start
    if ($MODULES_OBJ->isEnableItemSearchStoreOrder() > 0) {
        $searchWord = "";
    }
    //Added By HJ On 13-10-2020 For Get Item List For Item wise Search Functionality End
    $countryArr['Action'] = "1";
    $countryArr['totalValues'] = count($db_cuisine_list);
    $countryArr['isOfferApply'] = $isOfferApply;
    $countryArr['CuisineList'] = $db_cuisine_list;
    $returnArr['getCuisineList'] = $countryArr;
    $returnArr['itemData'] = $getItemData;
    //getCuisineList type end
    setDataResponse($returnArr);
}
// ############################ Check Available Restaurants ##############################################################
// ############################################### Cuisine list ##########################################################
if ($type == 'getCuisineList') {
    $iUserId = isset($_REQUEST["iUserId"]) ? $_REQUEST["iUserId"] : '0';
    $passengerLat = isset($_REQUEST["PassengerLat"]) ? $_REQUEST["PassengerLat"] : '';
    $passengerLon = isset($_REQUEST["PassengerLon"]) ? $_REQUEST["PassengerLon"] : '';
    $iServiceId = isset($_REQUEST["iServiceId"]) ? $_REQUEST["iServiceId"] : '';
    $vGeneralLang = isset($_REQUEST["vGeneralLang"]) ? $_REQUEST["vGeneralLang"] : '';
    $vLanguage = "";
    if (!empty($vGeneralLang)) {
        $vLanguage = $vGeneralLang;
    }
    else if ($iUserId != "") {
        $vLanguage = get_value('register_user', 'vLang', 'iUserId', $iUserId, '', 'true');
    }
    if ($vLanguage == "" || $vLanguage == NULL) {
        //Added By HJ On 15-07-2020 For Optimize language_master Table Query Start
        $vLanguage = $LANG_OBJ->FetchDefaultLangData("vCode");
        //Added By HJ On 15-07-2020 For Optimize language_master Table Query End
    }
    $ssqllast = " `company`.`iCompanyId` ASC ";
    $Restaurant_Cuisine_Id_Arr = $db_cuisine_list = $db_cuisine_list_new = $languageLabelsArr = array();
    $Restaurant_Cuisine_Id_str = "";
    $sql = "SELECT DISTINCT (company.iCompanyId),ROUND(( 6371 * acos( cos( radians(" . $passengerLat . ") ) * cos( radians( vRestuarantLocationLat ) ) * cos( radians( vRestuarantLocationLong ) - radians(" . $passengerLon . ") ) + sin( radians(" . $passengerLat . ") ) * sin( radians( vRestuarantLocationLat ) ) ) ),2) AS distance, company.iCompanyId FROM `company` WHERE vRestuarantLocationLat != '' AND vRestuarantLocationLong != '' AND eStatus='Active' AND eSystem = 'DeliverAll' AND iServiceId = '" . $iServiceId . "' HAVING distance < " . $LIST_RESTAURANT_LIMIT_BY_DISTANCE . " ORDER BY " . $ssqllast . "";
    $Data = $obj->MySQLSelect($sql);
    $storeIdArr = array();
    for ($r = 0; $r < count($Data); $r++) {
        $storeIdArr[] = $Data[$r]['iCompanyId'];
    }
    $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLanguage, "1", $iServiceId);
    $allStoreData = getcuisinelist($storeIdArr, $iUserId, $languageLabelsArr, $iServiceId);
    $Data_Company = $allStoreData['companyCuisineArr'];
    $offerMsgArr = $allStoreData['offerMsgArr'];
    $companyCuisineIdArr = $allStoreData['companyCuisineIdArr'];
    $isOfferApply = "No";
    if (count($Data_Company) > 0) {
        foreach ($Data_Company as $companyId => $cuisinArr) {
            $Restaurant_OfferMessage = "";
            if (isset($offerMsgArr[$companyId]['Restaurant_OfferMessage'])) {
                $Restaurant_OfferMessage = trim($offerMsgArr[$companyId]['Restaurant_OfferMessage']);
            }
            $restCuisineArr = array();
            if (isset($companyCuisineIdArr[$companyId])) {
                $restCuisineArr = $companyCuisineIdArr[$companyId];
            }
            if ($Restaurant_OfferMessage != "") {
                $isOfferApply = "Yes";
            }
            for ($d = 0; $d < count($restCuisineArr); $d++) {
                $Restaurant_Cuisine_Id_str .= $restCuisineArr[$d] . ",";
            }
        }
        $Restaurant_Cuisine_Id_str = trim($Restaurant_Cuisine_Id_str, ",");
        $Restaurant_Cuisine_Id_Arr = explode(",", $Restaurant_Cuisine_Id_str);
    }
    $Restaurant_Cuisine_Id_Arr = array_unique($Restaurant_Cuisine_Id_Arr);
    //added by SP vImage for cubex on 12-10-2019
    $defaultImage = $tconfig["tsite_url"] . "webimages/upload/DefaultImg/food_service.png";
    //Added By HJ On 31-10-2019 For Set Cuisine Default Icon as Per Service Category Start Bug - 1382 141 Mantis
    if ($iServiceId != 1) {
        $defaultImage = $tconfig["tsite_url"] . "webimages/upload/DefaultImg/other_services.png";
    }
    //Added By HJ On 31-10-2019 For Set Cuisine Default Icon as Per Service Category End Bug - 1382 141 Mantis
    //$sql = "SELECT cuisineId,cuisineName_" . $vLanguage . " as cuisineName,eStatus,CONCAT('aa',vImage) as vImage  FROM cuisine WHERE iServiceId = '" . $iServiceId . "' AND eStatus = 'Active' ORDER BY cuisineName ASC";
    $sql = "SELECT cuisineId,cuisineName_" . $vLanguage . " as cuisineName,eStatus,CASE WHEN vImage != '' THEN CONCAT('" . $tconfig['tsite_upload_images_menu_item_type'] . "/',vImage) ELSE '" . $defaultImage . "' END AS vImage FROM cuisine WHERE iServiceId = '" . $iServiceId . "' AND eStatus = 'Active' AND eDefault = 'No' ORDER BY cuisineName ASC";
    $db_cuisine_list = $obj->MySQLSelect($sql);
    if (count($db_cuisine_list) > 0) {
        for ($i = 0; $i < count($db_cuisine_list); $i++) {
            $isRemoveCuisineList = "No";
            $cuisineId = $db_cuisine_list[$i]['cuisineId'];
            if (!in_array($cuisineId, $Restaurant_Cuisine_Id_Arr)) {
                $isRemoveCuisineList = "Yes";
            }
            if ($isRemoveCuisineList == "Yes") {
                unset($db_cuisine_list_new[$i]);
            }
        }
    }
    //added by SP for cubex to add all in cuisine list so when click on it show all restaurant on 15-10-2019
    $default_cuisine = $obj->MySQLSelect("SELECT cuisineId, cuisineName_$vLanguage as cuisineName, eStatus, vImage FROM cuisine WHERE iServiceId = '$iServiceId' AND eDefault = 'Yes' ");
    /*$db_cuisine_list_new1[0]['cuisineName'] = $languageLabelsArr['LBL_ALL'];
    $db_cuisine_list_new1[0]['eStatus'] = $languageLabelsArr['LBL_ACTIVE'];
    $db_cuisine_list_new1[0]['vImage'] = $defaultImage;*/
    $db_cuisine_list_new1[0]['cuisineId'] = '';
    $db_cuisine_list_new1[0]['cuisineName'] = $default_cuisine[0]['cuisineName'];
    $db_cuisine_list_new1[0]['eStatus'] = $default_cuisine[0]['eStatus'];
    $db_cuisine_list_new1[0]['vImage'] = $tconfig['tsite_upload_images_menu_item_type'] . '/' . $default_cuisine[0]['vImage'];
    //$allCuisines = count($db_cuisine_list_new);
    $db_cuisine_list_new = array_merge($db_cuisine_list_new1, $db_cuisine_list);
    $db_cuisine_list_new = array_values($db_cuisine_list_new);
    $db_cuisine_list = $db_cuisine_list_new;
    if (count($db_cuisine_list) == 0) {
        $db_cuisine_list = "";
    }
    $countryArr['Action'] = "1";
    $countryArr['totalValues'] = count($db_cuisine_list);
    $countryArr['isOfferApply'] = $isOfferApply;
    $countryArr['CuisineList'] = $db_cuisine_list;
    setDataResponse($countryArr);
}
// ############################################### Cuisine list ##########################################################
// ############################ Check Search Restaurants ##############################################################
if ($type == "loadSearchRestaurants") {
    $iUserId = isset($_REQUEST["iUserId"]) ? $_REQUEST["iUserId"] : '';
    $passengerLat = isset($_REQUEST["PassengerLat"]) ? $_REQUEST["PassengerLat"] : '';
    $passengerLon = isset($_REQUEST["PassengerLon"]) ? $_REQUEST["PassengerLon"] : '';
    $vLang = isset($_REQUEST["vGeneralLang"]) ? $_REQUEST["vGeneralLang"] : '';
    $searchword = isset($_REQUEST["searchword"]) ? $_REQUEST["searchword"] : '';
    $searchword = strtolower(trim($searchword));
    $vAddress = isset($_REQUEST["vAddress"]) ? $_REQUEST["vAddress"] : '';
    if ($searchword == "" || $searchword == NULL) {
        $searchword = "";
    }
    if ($vAddress != "") {
        $vAddress_arr = explode(",", $vAddress);
        $vAddress = end($vAddress_arr);
        $vAddress = trim($vAddress);
    }
    if (empty($vLang)) {
        $vLang = $LANG_OBJ->FetchDefaultLangData('vCode');
    }
    $Data = FetchNearByStores($passengerLat, $passengerLon, $iUserId, "No", $searchword, $vAddress, $iServiceId);
    //echo count($Data);
    $companyIdArr = $itemListArr = array();
    for ($i = 0; $i < count($Data); $i++) {
        if ($Data[$i]['vImage'] != "") {
            $Data[$i]['vImage'] = $tconfig['tsite_upload_images_compnay'] . '/' . $Data[$i]['iCompanyId'] . '/3_' . $Data[$i]['vImage'];
        }
        if ($Data[$i]['vCoverImage'] != "") {
            $Data[$i]['vCoverImage'] = $tconfig['tsite_upload_images_compnay'] . '/' . $Data[$i]['iCompanyId'] . '/' . $Data[$i]['vCoverImage'];
        }
        //Added By HJ On 26-06-2019 For Get And Display Store Demo Image Start
        if (isset($Data[$i]['vDemoStoreImage']) && $Data[$i]['vDemoStoreImage'] != "" && SITE_TYPE == "Demo") {
            $demoImgPath = $tconfig['tsite_upload_demo_compnay_doc_path'] . $Data[$i]['vDemoStoreImage'];
            if (file_exists($demoImgPath)) {
                $demoImgUrl = $tconfig['tsite_upload_demo_compnay_doc'] . $Data[$i]['vDemoStoreImage'];
                $Data[$i]['vImage'] = $demoImgUrl;
            }
        }
        //Added By HJ On 26-06-2019 For Get And Display Store Demo Image End
        $TotalCompanyFoodDataCount = $Data[$i]['CompanyFoodDataCount'];
        if ($TotalCompanyFoodDataCount > 0) {
            $companyIdArr[] = $Data[$i]['iCompanyId'];
            $Restaurant_id_str .= $Data[$i]['iCompanyId'] . ",";
        }
    }
    $Restaurant_id_str = trim($Restaurant_id_str, ",");
    $cuisineId_arr = GetStoresByCuisine($iUserId, $searchword, $Restaurant_id_str);
    //Added By HJ On 15-10-2020 For Search Item With Store Functionality Start
    $enableItemSearch = ($MODULES_OBJ->isEnableItemSearchStoreOrder()) ? "Yes" : "No";
    if (strtoupper($enableItemSearch) == "YES") {
        $itemListArr = getStoreItemData($companyIdArr, $vLang, $searchword, $iUserId, $iServiceId);
    }
    //Added By HJ On 15-10-2020 For Search Item With Store Functionality End
    $Data_Filter = $Data;
    for ($i = 0; $i < count($Data); $i++) {
        $isRemoveRestaurantIntoList = "No";
        // # Checking For Search Keyword ##
        $vCompany = strtolower($Data[$i]['vCompany']);
        $Restaurant_Cuisine = strtolower($Data[$i]['Restaurant_Cuisine']);
        if (((!preg_match("/$searchword/i", $vCompany)) && (!preg_match("/$searchword/i", $Restaurant_Cuisine))) && $searchword != "") {
            $isRemoveRestaurantIntoList = "Yes";
        }
        // # Checking For Search Keyword ##
        // # Checking For Food Menu Available for Company Or Not ##
        $CompanyFoodDataCount = $Data[$i]['CompanyFoodDataCount'];
        if ($CompanyFoodDataCount == 0) {
            $isRemoveRestaurantIntoList = "Yes";
        }
        // # Checking For Food Menu Available for Company Or Not ##
        if ($isRemoveRestaurantIntoList == "Yes") {
            unset($Data_Filter[$i]);
        }
        $fsql1 = "";
        if ($MODULES_OBJ->isEnableStoreMultiServiceCategories()) {
            $fsql1 = " AND iServiceId = '$iServiceId'";
        }
        $getFooMenu = $obj->MySQLSelect("SELECT GROUP_CONCAT(iFoodMenuId) as foodMenuIds FROM food_menu WHERE iCompanyId = '" . $Data[$i]['iCompanyId'] . "' AND eStatus = 'Active' $fsql1");
        if (!empty($getFooMenu[0]['foodMenuIds'])) {
            $getStoreItems = $obj->MySQLSelect("SELECT COUNT(iMenuItemId) as menuItemCount FROM menu_items WHERE iFoodMenuId IN (" . $getFooMenu[0]['foodMenuIds'] . ")  AND eStatus = 'Active'");
            if ($getStoreItems[0]['menuItemCount'] == 0) {
                unset($Data_Filter[$i]);
            }
        }
        else {
            unset($Data_Filter[$i]);
        }
    }
    $ispriceshow = '';
    //Added By HJ On 15-07-2020 For Optimize service_categories Table Query Start
    $serviceCatArr = array();
    if (!isset($serviceCatDataArr)) {
        $serviceCatDataArr = $obj->MySQLSelect("SELECT * FROM service_categories");
    }

    for ($h = 0; $h < count($serviceCatDataArr); $h++) {
        $serviceCatArr[$serviceCatDataArr[$h]['iServiceId']] = $serviceCatDataArr[$h];
    }
    if (isset($serviceCatArr[$iServiceId])) {
        $ServiceCategoryData = array();
        $ServiceCategoryData[] = $serviceCatArr[$iServiceId];
    }
    else {
        $servFields = 'eType';
        $ServiceCategoryData = get_value('service_categories', $servFields, 'iServiceId', $iServiceId);
    }
    //Added By HJ On 15-07-2020 For Optimize service_categories Table Query End
    if (!empty($ServiceCategoryData)) {
        if (!empty($ServiceCategoryData[0]['eType'])) {
            $ispriceshow = $ServiceCategoryData[0]['eType'];
        }
    }
    $Data = array_values($Data_Filter);
    // ## Sorting Of Restaurants by relevance  ###
    foreach ($Data as $k => $v) {
        $Data_name['restaurantstatus'][$k] = $v['restaurantstatus'];
    }
    array_multisort($Data_name['restaurantstatus'], SORT_DESC, $Data);
    // ## Sorting Of Restaurants by relevance  ###
    if ((!empty($Data) || !empty($cuisineId_arr) || !empty($itemListArr)) && $searchword != "") {
        $returnArr['Action'] = "1";
        $returnArr['message'] = $Data;
        $returnArr['message_cusine'] = $cuisineId_arr;
        $returnArr['message_item'] = $itemListArr;
        $returnArr['isShowSearchedItemEnabled'] = $enableItemSearch;
        $returnArr['ispriceshow'] = $ispriceshow;
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_NO_RESTAURANT_FOUND_TXT";
    }
    setDataResponse($returnArr);
}
// ############################ Check Search Restaurants ##############################################################
// ############################ Get Restaurant Details   ##############################################################
if ($type == "GetRestaurantDetails") {
    $page = isset($_REQUEST['page']) ? trim($_REQUEST['page']) : 1;
    $iUserId = isset($_REQUEST["iUserId"]) ? $_REQUEST["iUserId"] : '';
    $iCompanyId = isset($_REQUEST["iCompanyId"]) ? $_REQUEST["iCompanyId"] : '';
    $CheckNonVegFoodType = isset($_REQUEST["CheckNonVegFoodType"]) ? $_REQUEST["CheckNonVegFoodType"] : 'No';
    $searchword = isset($_REQUEST["searchword"]) ? $_REQUEST["searchword"] : '';
    $vLanguage = isset($_REQUEST["vGeneralLang"]) ? $_REQUEST["vGeneralLang"] : '';
    $searchword = strtolower(trim($searchword));
    if ($searchword == "" || $searchword == NULL) {
        $searchword = "";
    }
    if ($CheckNonVegFoodType == "" || $CheckNonVegFoodType == NULL) {
        $CheckNonVegFoodType = "No";
    }
    $ssql_fav_q = "";
    if ($MODULES_OBJ->isFavouriteStoreModuleAvailable() && !empty($iUserId)) {
        $data = addUpdateFavStore();
        $ssql_fav_q = getFavSelectQuery($iCompanyId, $iUserId);
    }
    $db_company = $obj->MySQLSelect("SELECT * " . $ssql_fav_q . " FROM company WHERE iCompanyId = '" . $iCompanyId . "'");
    if (count($db_company) > 0) {
        $iCompanyId = $db_company[0]['iCompanyId'];
        $CompanyDetails_Arr = getCompanyDetails($iCompanyId, $iUserId, $CheckNonVegFoodType, $searchword, $iServiceId);
        $db_company[0]['fPricePerPerson'] = "";
        if ($iServiceId == '1') {
            $db_company[0]['fPricePerPerson'] = isset($CompanyDetails_Arr['fPricePerPersonWithCurrency']) ? $CompanyDetails_Arr['fPricePerPersonWithCurrency'] : '$ 1.00';
        }
        $db_favorite = $obj->MySQLSelect("select eFavStore AS eIsFavourite from store_favorites where iCompanyId = '" . $iCompanyId . "' AND  iUserId = '" . $iUserId . "' AND  eFavStore = 'Yes'");
        if (count($db_favorite) > 0) {
            $db_company[0]['eIsFavourite'] = 'Yes';
        }
        else {
            $db_company[0]['eIsFavourite'] = 'No';
        }
        $db_company[0]['fPackingCharge'] = isset($CompanyDetails_Arr['fPackingCharge']) ? $CompanyDetails_Arr['fPackingCharge'] : 0;
        $db_company[0]['fMinOrderValue'] = isset($CompanyDetails_Arr['fMinOrderValue']) ? $CompanyDetails_Arr['fMinOrderValue'] : 1;
        $db_company[0]['fMinOrderValueDisplay'] = isset($CompanyDetails_Arr['fMinOrderValueDisplay']) ? $CompanyDetails_Arr['fMinOrderValueDisplay'] : '';
        $db_company[0]['Restaurant_OfferMessage'] = isset($CompanyDetails_Arr['Restaurant_OfferMessage']) ? $CompanyDetails_Arr['Restaurant_OfferMessage'] : '';
        $db_company[0]['Restaurant_OfferMessage_short'] = isset($CompanyDetails_Arr['Restaurant_OfferMessage_short']) ? $CompanyDetails_Arr['Restaurant_OfferMessage_short'] : '';
        $db_company[0]['Restaurant_OrderPrepareTime'] = isset($CompanyDetails_Arr['Restaurant_OrderPrepareTime']) ? $CompanyDetails_Arr['Restaurant_OrderPrepareTime'] : '0 mins';
        $db_company[0]['monfritimeslot_TXT'] = isset($CompanyDetails_Arr['monfritimeslot_TXT']) ? $CompanyDetails_Arr['monfritimeslot_TXT'] : '';
        $db_company[0]['monfritimeslot_Time'] = isset($CompanyDetails_Arr['monfritimeslot_Time']) ? $CompanyDetails_Arr['monfritimeslot_Time'] : '';
        $db_company[0]['satsuntimeslot_TXT'] = isset($CompanyDetails_Arr['satsuntimeslot_TXT']) ? $CompanyDetails_Arr['satsuntimeslot_TXT'] : '';
        $db_company[0]['satsuntimeslot_Time'] = isset($CompanyDetails_Arr['satsuntimeslot_Time']) ? $CompanyDetails_Arr['satsuntimeslot_Time'] : '';
        $db_company[0]['eNonVegToggleDisplay'] = isset($CompanyDetails_Arr['eNonVegToggleDisplay']) ? $CompanyDetails_Arr['eNonVegToggleDisplay'] : 'No';
        $db_company[0]['RatingCounts'] = isset($CompanyDetails_Arr['RatingCounts']) ? $CompanyDetails_Arr['RatingCounts'] : '';
        $db_company[0]['CompanyDetails'] = $CompanyDetails_Arr;
        $db_company[0]['MenuItemsDetails'] = isset($CompanyDetails_Arr['MenuItemsDataArr']) ? $CompanyDetails_Arr['MenuItemsDataArr'] : array();
        $db_company[0]['RegistrationDate'] = date("Y-m-d", strtotime($db_company[0]['tRegistrationDate'] . ' -1 day '));
        /*--------------------- for the store timeslotavailable --------------------*/
        $restaurant_status_arr = GetStoreWorkingHoursDetails($iCompanyId, $iUserId);
        $db_company[0]['timeslotavailable'] = "No";
        if (isset($restaurant_status_arr['timeslotavailable'])) {
            $db_company[0]['timeslotavailable'] = $restaurant_status_arr['timeslotavailable'];
        }
        /*--------------------- for the store timeslotavailable --------------------*/
        if ($db_company[0]['vImage'] != "") {
            $db_company[0]['vImage'] = $tconfig['tsite_upload_images_compnay'] . '/' . $iCompanyId . '/3_' . $db_company[0]['vImage'];
        }
        if ($db_company[0]['vCoverImage'] != "") {
            $db_company[0]['vCoverImage'] = $tconfig['tsite_upload_images_compnay'] . '/' . $iCompanyId . '/' . $db_company[0]['vCoverImage'];
        }
        //Added By HJ On 26-06-2019 For Get And Display Store Demo Image Start
        if (isset($db_company[0]['vDemoStoreImage']) && $db_company[0]['vDemoStoreImage'] != "" && SITE_TYPE == "Demo") {
            $demoImgPath = $tconfig['tsite_upload_demo_compnay_doc_path'] . $db_company[0]['vDemoStoreImage'];
            if (file_exists($demoImgPath)) {
                $demoImgUrl = $tconfig['tsite_upload_demo_compnay_doc'] . $db_company[0]['vDemoStoreImage'];
                $db_company[0]['vImage'] = $demoImgUrl;
            }
        }
        //Added By HJ On 26-06-2019 For Get And Display Store Demo Image End
        $vAvgRating = $db_company[0]['vAvgRating'];
        $db_company[0]['vAvgRating'] = ($vAvgRating > 0) ? number_format($db_company[0]['vAvgRating'], 1) : 0;
        $returnArr['Action'] = "1";
        $returnArr['message'] = $db_company[0];
        $sql = "SELECT vImage FROM `banners` WHERE vCode = '" . $db_company[0]['vLang'] . "' AND eStatus = 'Active' AND iServiceId = '" . $iServiceId . "' ORDER BY iDisplayOrder ASC";
        $banners = $obj->MySQLSelect($sql);
        $dataOfBanners = array();
        $count = 0;
        for ($i = 0; $i < count($banners); $i++) {
            if ($banners[$i]['vImage'] != "") {
                $dataOfBanners[$count]['vImage'] = $tconfig["tsite_url"] . 'assets/img/images/' . urlencode($banners[$i]['vImage']);
                $count++;
            }
        }
        if (empty($dataOfBanners)) {
            $dataOfBanners = '';
        }
        $returnArr['BANNER_DATA'] = $dataOfBanners;
        $vGeneralLang = !empty($_REQUEST['vGeneralLang']) ? $_REQUEST['vGeneralLang'] : "EN";
        // $returnArr['Restaurant_Safety_Status'] = (!empty($db_company[0]['eSafetyPractices']) && ($db_company[0]['iServiceId'] == 1 || $db_company[0]['iServiceId'] == 2)) ? $db_company[0]['eSafetyPractices'] : "No";
        $returnArr['Restaurant_Safety_Status'] = (!empty($db_company[0]['eSafetyPractices']) && $MODULES_OBJ->isEnableStoreSafetyProcedure()) ? $db_company[0]['eSafetyPractices'] : "No";
        $safetyimg = "/webimages/icons/DefaultImg/ic_safety.png";
        $returnArr['Restaurant_Safety_Icon'] = (file_exists($tconfig["tpanel_path"] . $safetyimg)) ? $tconfig["tsite_url"] . $safetyimg : "";
        $time = time();
        $returnArr['Restaurant_Safety_URL'] = $tconfig["tsite_url"] . "safety-measures?time_data=" . $time . "&fromlang=" . $vGeneralLang . "&id=" . base64_encode($iCompanyId) . "&iServiceId=" . base64_encode($iServiceId);
        $banner_images = 0;
        if ($MODULES_OBJ->isEnableStorePhotoUploadFacility()) {
            $banner_data = $obj->MySQLSelect("SELECT * FROM store_wise_banners WHERE eStatus = 'Active' AND iCompanyId = " . $iCompanyId . " ORDER BY iDisplayOrder");
            if (count($banner_data) > 0) {
                $banner_images = 1;
            }
        }
        if (($db_company[0]['eSafetyPractices'] == "Yes" && $MODULES_OBJ->isEnableStoreSafetyProcedure()) || ($MODULES_OBJ->isEnableStorePhotoUploadFacility() && $banner_images == 1)) {
            $returnArr['Restaurant_Safety_URL'] = $tconfig["tsite_url"] . "safety-measures?time_data=" . $time . "&fromlang=" . $vGeneralLang . "&id=" . base64_encode($iCompanyId) . "&iServiceId=" . base64_encode($iServiceId);
        }
        else {
            $returnArr['Restaurant_Safety_URL'] = "";
        }
        $ServiceCategoryData = $obj->MySQLSelect("SELECT eType FROM service_categories WHERE iServiceId = $iServiceId");
        $returnArr['ispriceshow'] = $ServiceCategoryData[0]['eType'];
        $returnArr['restaurantstatus'] = $CompanyDetails_Arr['restaurantstatus'];
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_NO_RESTAURANT_FOUND_TXT";
    }
    setDataResponse($returnArr);
}
// ############################ Get Restaurant Details   ##############################################################
// ################################## Restaurant Signup ###############################################################
if ($type == "signup_company") {
    $vCompany = isset($_REQUEST["vCompany"]) ? $_REQUEST["vCompany"] : '';
    $email = isset($_REQUEST["vEmail"]) ? $_REQUEST["vEmail"] : '';
    $email = strtolower($email);
    $phone_mobile = isset($_REQUEST["vPhone"]) ? $_REQUEST["vPhone"] : '';
    $password = isset($_REQUEST["vPassword"]) ? $_REQUEST["vPassword"] : '';
    $iGcmRegId = isset($_REQUEST["vDeviceToken"]) ? $_REQUEST["vDeviceToken"] : '';
    $phoneCode = isset($_REQUEST["PhoneCode"]) ? $_REQUEST["PhoneCode"] : '';
    $CountryCode = isset($_REQUEST["CountryCode"]) ? $_REQUEST["CountryCode"] : '';
    $deviceType = isset($_REQUEST["vDeviceType"]) ? $_REQUEST["vDeviceType"] : 'Android';
    $vCurrency = isset($_REQUEST["vCurrency"]) ? $_REQUEST["vCurrency"] : '';
    $vLang = isset($_REQUEST["vLang"]) ? $_REQUEST["vLang"] : '';
    $user_type = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : 'Company';
    $eSignUpType = isset($_REQUEST["eSignUpType"]) ? $_REQUEST["eSignUpType"] : 'Normal';
    $vFirebaseDeviceToken = isset($_REQUEST["vFirebaseDeviceToken"]) ? $_REQUEST["vFirebaseDeviceToken"] : '';
    $vImageURL = isset($_REQUEST["vImageURL"]) ? $_REQUEST["vImageURL"] : '';
    $Data = array();
    // if (SITE_TYPE == 'Demo') {
    //     $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLang, "1", $iServiceId);
    //     $returnArr['Action'] = "0";
    //     $returnArr['message'] = strip_tags($languageLabelsArr["LBL_SIGNUP_DEMO_CONTENT"]);
    //     setDataResponse($returnArr);
    // }
    if ($email == "" && $phone_mobile == "" && $fbid == "") {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
        setDataResponse($returnArr);
    }
    if ($vCurrency == '') {
        $vCurrency = get_value('currency', 'vName', 'eDefault', 'Yes', '', 'true');
    }
    if ($vLang == '') {
        //Added By HJ On 15-07-2020 For Optimize language_master Table Query Start
        $vLang = $LANG_OBJ->FetchDefaultLangData("vCode");
        //Added By HJ On 15-07-2020 For Optimize language_master Table Query End
    }
    $csql = "SELECT eZeroAllowed,vCountryCode FROM `country` WHERE vPhoneCode = '" . $phoneCode . "'";
    $CountryData = $obj->MySQLSelect($csql);
    $eZeroAllowed = $CountryData[0]['eZeroAllowed'];
    if ($eZeroAllowed == 'Yes') {
        $phone_mobile = $phone_mobile;
    }
    else {
        $first = substr($phone_mobile, 0, 1);
        if ($first == "0") {
            $phone_mobile = substr($phone_mobile, 1);
        }
    }
    $eSystem = "DeliverAll";
    if ($phone_mobile != "") {
        $checPhoneExist = checkMemberDataInfo($phone_mobile, "", $user_type, $CountryCode, "", $eSystem); //Added By HJ On 09-09-2019 For Chekc User Country and Mobile Number When Register
    }
    if (isset($checPhoneExist['status']) && $checPhoneExist['status'] == 0) {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_MOBILE_EXIST";
        setDataResponse($returnArr);
    }
    else if (isset($checPhoneExist['status']) && $checPhoneExist['status'] == 2) {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_INVALID_MEMBER_USER_COUNTRY_EMAIL_TXT";
        setDataResponse($returnArr);
    }
    // $sql    = "SELECT * FROM $tblname WHERE 1=1 AND IF('$email'!='',vEmail = '$email',0) OR IF('$phone_mobile'!='',vPhone = '$phone_mobile',0) OR IF('$fbid'!='',vFbId = '$fbid',0)";
    $sql = "SELECT * FROM company WHERE 1=1 AND IF('$email'!='',vEmail = '$email',0) AND eStatus != 'Deleted'";

    $check_passenger = $obj->MySQLSelect($sql);
    // $Password_passenger = encrypt($password);
    if ($password != "") {
        $Password_passenger = encrypt_bycrypt($password);
    }
    else {
        $Password_passenger = "";
    }
    //if (count($check_passenger) > 0) {
    if (isset($check_passenger[0]['vEmail']) && strtolower($check_passenger[0]['vEmail']) == $email) {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_ALREADY_REGISTERED_TXT";
        echo json_encode($returnArr);
        exit;
    }
    else {
        $Data['vCompany'] = $vCompany;
        $Data['vEmail'] = $email;
        $Data['vPhone'] = $phone_mobile;
        $Data['vPassword'] = $Password_passenger;
        $Data['iGcmRegId'] = $iGcmRegId;
        $Data['vFirebaseDeviceToken'] = $vFirebaseDeviceToken;
        $Data['vLang'] = $vLang;
        $Data['vCode'] = $phoneCode;
        $Data['vCountry'] = $CountryCode;
        $Data['eDeviceType'] = $deviceType;
        $Data['vCurrencyCompany'] = $vCurrency;
        $Data['tRegistrationDate'] = @date('Y-m-d H:i:s');
        $Data['eSignUpType'] = $eSignUpType;
        $Data['iServiceId'] = $iServiceId;
        $Data['eSystem'] = $eSystem;
        $Data['eStatus'] = "Inactive";
        if ($eSignUpType == "Facebook" || $eSignUpType == "Google") {
            $Data['eStatus'] = "Active";
        }
        $random = substr(md5(rand()), 0, 7);
        $Data['tDeviceSessionId'] = session_id() . time() . $random;
        $Data['tSessionId'] = session_id() . time();
        $Data['vTimeZone'] = get_value('country', 'vTimeZone', 'vCountryCode', $CountryCode, '', 'true');
        if (SITE_TYPE == 'Demo') {
            $Data['eStatus'] = 'Active';
        }
        $id = $obj->MySQLQueryPerform("company", $Data, 'insert');
        createUserLog('Company', 'No', $id, $deviceType,'AppLogin','SignUp');
        // $sql_checkLangCode = "SELECT  vCode FROM  language_master WHERE `eStatus` = 'Active' AND `eDefault` = 'Yes' ";
        // $Data_checkLangCode = $obj->MySQLSelect($sql_checkLangCode);
        $returnArr['changeLangCode'] = "Yes";
        $returnArr['UpdatedLanguageLabels'] = $LANG_OBJ->FetchLanguageLabels($vLang, "1", $iServiceId);
        $returnArr['vLanguageCode'] = $vLang;
        $sql_LangCode = "SELECT eDirectionCode,vGMapLangCode FROM language_master WHERE `vCode` = '" . $vLang . "' ";
        $Data_checkLangCode = $obj->MySQLSelect($sql_LangCode);
        $returnArr['langType'] = $Data_checkLangCode[0]['eDirectionCode'];
        $returnArr['vGMapLangCode'] = $Data_checkLangCode[0]['vGMapLangCode'];
        $sql = "SELECT vCode, vGMapLangCode, eDirectionCode as eType, vTitle,vCurrencyCode,vCurrencySymbol,eDefault  FROM  `language_master` WHERE  `eStatus` = 'Active' ORDER BY iDispOrder ASC ";
        $defLangValues = $obj->MySQLSelect($sql);
        $returnArr['LIST_LANGUAGES'] = $defLangValues;
        for ($i = 0; $i < count($defLangValues); $i++) {
            if ($defLangValues[$i]['eDefault'] == "Yes") {
                $returnArr['DefaultLanguageValues'] = $defLangValues[$i];
            }
            $defLangValues[$i]['vService_BG_color'] = RANDOM_COLORS_ARR[array_rand(RANDOM_COLORS_ARR, 1)];
            $defLangValues[$i]['vService_TEXT_color'] = "#FFFFFF";
            $returnArr['LIST_LANGUAGES'][$i]['vService_BG_color'] = RANDOM_COLORS_ARR[array_rand(RANDOM_COLORS_ARR, 1)];
            $returnArr['LIST_LANGUAGES'][$i]['vService_TEXT_color'] = "#FFFFFF";
        }
        $sql = "SELECT iCurrencyId,vName, vSymbol,iDispOrder, eDefault,Ratio,fThresholdAmount,eStatus  FROM  `currency` WHERE  `eStatus` = 'Active' ORDER BY iDispOrder ASC ";
        $defCurrencyValues = $obj->MySQLSelect($sql);
        $returnArr['LIST_CURRENCY'] = $defCurrencyValues;
        for ($i = 0; $i < count($defCurrencyValues); $i++) {
            if ($defCurrencyValues[$i]['eDefault'] == "Yes") {
                $returnArr['DefaultCurrencyValues'] = $defCurrencyValues[$i];
            }
            $defCurrencyValues[$i]['vService_BG_color'] = RANDOM_COLORS_ARR[array_rand(RANDOM_COLORS_ARR, 1)];
            $defCurrencyValues[$i]['vService_TEXT_color'] = "#FFFFFF";
            $returnArr['LIST_CURRENCY'][$i]['vService_BG_color'] = RANDOM_COLORS_ARR[array_rand(RANDOM_COLORS_ARR, 1)];
            $returnArr['LIST_CURRENCY'][$i]['vService_TEXT_color'] = "#FFFFFF";
        }
        if ($id > 0) {
            /* new added */
            $returnArr['Action'] = "1";
            $returnArr['message'] = getCompanyDetailInfo($id);
            $returnArr['message'] = getCustomeNotificationSound($returnArr['message']);
            $returnArr['message']['driverOptionArr'] = FetchStoreDriverOptions($vLang, $iServiceId); //Added By HJ On 19-06-2020 As Per Discuss With NM
            $maildata['EMAIL'] = $email;
            $maildata['NAME'] = $vCompany;
            $pass_txt = ($returnArr['UpdatedLanguageLabels']['LBL_PASSWORD'] != "") ? $returnArr['UpdatedLanguageLabels']['LBL_PASSWORD'] : "Password";
            //$maildata['PASSWORD'] = $pass_txt . ": " . $password; //Commented By HJ On 11-01-2019 For Hide Password As Per Discuss With QA BM
            $COMM_MEDIA_OBJ->SendMailToMember("STORE_REGISTRATION_USER", $maildata);
            $COMM_MEDIA_OBJ->SendMailToMember("STORE_REGISTRATION_ADMIN", $maildata);
            setDataResponse($returnArr);
        }
        else {
            $returnArr['Action'] = "0";
            $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
            setDataResponse($returnArr);
        }
    }
}
// ################################## Restaurant Signup ###############################################################
// ############################ Get Option and AddOn Details ##############################################################
if ($type == "GetItemOptionAddonDetails") {
    $iUserId = isset($_REQUEST["iUserId"]) ? $_REQUEST["iUserId"] : '';
    $iCompanyId = isset($_REQUEST["iCompanyId"]) ? $_REQUEST["iCompanyId"] : '';
    $iMenuItemId = isset($_REQUEST["iMenuItemId"]) ? $_REQUEST["iMenuItemId"] : '';
    //Added By HJ On 23-01-2020 For Solved Currency Related Issues Start
    $currencySymbol = $currencycode = "";
    if ($iUserId > 0) {
        $UserDetailsArr = getUserCurrencyLanguageDetails($iUserId);
        $currencySymbol = $UserDetailsArr['currencySymbol'];
        $currencycode = $UserDetailsArr['currencycode'];
        $Ratio = $UserDetailsArr['Ratio'];
        $vLang = $UserDetailsArr['vLang'];
    }
    else {
        $currencycode = isset($_REQUEST["vGeneralCurrency"]) ? $_REQUEST["vGeneralCurrency"] : ''; // Added By HJ On 23-01-2020 When User Not Logged In Get Currency Code
        $vLang = isset($_REQUEST["vGeneralLang"]) ? $_REQUEST["vGeneralLang"] : ''; // Added By HJ On 23-01-2020 When User Not Logged In Get Language Code
        if (($currencySymbol == "" || $currencySymbol == NULL) && $currencycode != "") {
            //Added By HJ On 15-07-2020 For Optimization currency Table Query Start
            if (isset($currencyAssociateArr[$currencycode])) {
                $currencyData = array();
                $currencyData[] = $currencyAssociateArr[$currencycode];
            }
            else {
                $currencyData = $obj->MySQLSelect("SELECT vName,vSymbol,Ratio FROM currency WHERE vName = '" . $currencycode . "'");
            }
            //Added By HJ On 15-07-2020 For Optimization currency Table Query End
            //$currencyData = $obj->MySQLSelect("SELECT vName,vSymbol,Ratio FROM currency WHERE vName = '" . $currencycode . "'");
        }
        else {
            //Added By HJ On 15-07-2020 For Optimization currency Table Query Start
            if (!empty($vSystemDefaultCurrencyName) && !empty($vSystemDefaultCurrencySymbol) && !empty($vSystemDefaultCurrencyRatio)) {
                $currencyData = array();
                $currencyData[0]['vName'] = $vSystemDefaultCurrencyName;
                $currencyData[0]['vSymbol'] = $vSystemDefaultCurrencySymbol;
                $currencyData[0]['Ratio'] = $vSystemDefaultCurrencyRatio;
            }
            else {
                $currencyData = $obj->MySQLSelect("SELECT vName,vSymbol,Ratio FROM currency WHERE eDefault = 'Yes'");
                $currencycode = $currencyData[0]['vName'];
                $currencySymbol = $currencyData[0]['vSymbol'];
                $Ratio = $currencyData[0]['Ratio'];
            }
            //Added By HJ On 15-07-2020 For Optimization currency Table Query End
            //$currencyData = $obj->MySQLSelect("SELECT vName,vSymbol,Ratio FROM currency WHERE eDefault = 'Yes'");
        }
        if (count($currencyData) > 0) {
            $currencycode = $currencyData[0]['vName'];
            $currencySymbol = $currencyData[0]['vSymbol'];
            $Ratio = $currencyData[0]['Ratio'];
        }
        else {
            $currencycode = "USD";
            $currencySymbol = "$";
            $priceRatio = 1.0000;
        }
        if ($vLang == "") {
            $vLang = "EN";
        }
    }
    //Added By HJ On 23-01-2020 For Solved Currency Related Issues End
    $GetAllMenuItemOptionsTopping_Arr = GetAllMenuItemOptionsTopping($iCompanyId, $currencySymbol, $Ratio, $vLang, "Display", $iServiceId, $currencycode, $iMenuItemId);
    if ((!empty($GetAllMenuItemOptionsTopping_Arr))) {
        $returnArr['Action'] = "1";
        $returnArr['message'] = $GetAllMenuItemOptionsTopping_Arr;
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
    }
    setDataResponse($returnArr);
}
// ############################ Get Option and AddOn Details ##############################################################
// ############################ Start Get All Order Details Restaurant #######################################################
if ($type == "GetAllOrderDetailsRestaurant") {
    $iCompanyId = isset($_REQUEST["iCompanyId"]) ? $_REQUEST["iCompanyId"] : '';
    $OrderType = isset($_REQUEST["OrderType"]) ? $_REQUEST["OrderType"] : '';
    $vTimeZone = isset($_REQUEST["vTimeZone"]) ? $_REQUEST["vTimeZone"] : "";
    $vLang = isset($_REQUEST["vGeneralLang"]) ? $_REQUEST["vGeneralLang"] : "";
    $page = isset($_REQUEST['page']) ? trim($_REQUEST['page']) : 1;
    $per_page = 10;
    $statusCode = "2,4";
    if ($OrderType == 'NEW') {
        $statusCode = "1";
    }
    else if ($OrderType == 'DISPATCHED') {
        $statusCode = "5";
    }
    $data_count_all = $obj->MySQLSelect("SELECT COUNT(iOrderId) As TotalIds FROM orders WHERE  iCompanyId='" . $iCompanyId . "' AND iStatusCode IN ($statusCode)");
    $totOrdCount = 0;
    if (isset($data_count_all[0]['TotalIds']) && $data_count_all[0]['TotalIds'] > 0) {
        $totOrdCount = $data_count_all[0]['TotalIds'];
    }
    $TotalPages = ceil($totOrdCount / $per_page);
    $start_limit = ($page - 1) * $per_page;
    $limit = " LIMIT " . $start_limit . ", " . $per_page;
    $sql = "SELECT o.vOrderNo,o.iOrderId,o.tOrderRequestDate,o.eTakeaway,o.eOrderplaced_by FROM orders as o LEFT JOIN order_details as od ON od.iOrderId = o.iOrderId LEFT JOIN order_status as os on os.iStatusCode = o.iStatusCode WHERE o.iCompanyId = '" . $iCompanyId . "' AND o.iStatusCode IN ($statusCode) GROUP BY o.iOrderId ORDER BY o.iOrderId DESC" . $limit;
    $db_orders = $obj->MySQLSelect($sql);
    //Added By HJ On 09-05-2019 For Optimize Code Start
    $orderIds = "";
    for ($r = 0; $r < count($db_orders); $r++) {
        $orderIds .= ",'" . $db_orders[$r]['iOrderId'] . "'";
    }
    $orderDataCountArr = array();
    if ($orderIds != "") {
        $orderIds = trim($orderIds, ",");
        $orderData = $obj->MySQLSelect("SELECT COUNT(od.iOrderDetailId) as total,od.iOrderId FROM order_details as od WHERE od.iOrderId IN ($orderIds) GROUP BY od.iOrderId");
        for ($g = 0; $g < count($orderData); $g++) {
            $orderDataCountArr[$orderData[$g]['iOrderId']] = $orderData[$g]['total'];
        }
    }
    //Added By HJ On 09-05-2019 For Optimize Code End
    if (empty($vLang)) {
        $vLang = $LANG_OBJ->FetchDefaultLangData("vCode");
    }
    $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLang, "1", "");
    if (!empty($db_orders)) {
        $orderIdArr = array();
        for ($h = 0; $h < count($db_orders); $h++) {
            $orderIdArr[] = $db_orders[$h]['iOrderId'];
        }
        $eConfirmArr = checkOrderStatus($orderIdArr, "2");
        foreach ($db_orders as $key => $value) {
            $serverTimeZone = date_default_timezone_get();
            $date = converToTz($value['tOrderRequestDate'], $vTimeZone, $serverTimeZone, "Y-m-d H:i:s");
            $OrderTime = date('h:iA', strtotime($date));
            $db_orders[$key]['tOrderRequestDate_Org'] = $date;
            $db_orders[$key]['tOrderRequestDateFormatted'] = date('d M, h:iA', strtotime($date));
            $db_orders[$key]['tOrderRequestDate'] = $OrderTime;
            //Commented By HJ On 24-05-2019 For Optimize Code Start
            /* $order_query = "SELECT COUNT(od.iOrderDetailId) as total FROM order_details as od LEFT JOIN  orders as o on o.iOrderId = od.iOrderId WHERE o.iCompanyId = '" . $iCompanyId . "' AND od.iOrderId = '" . $value['iOrderId'] . "' ";
              $orderData = $obj->MySQLSelect($order_query);
              $db_orders[$key]['TotalItems'] = $orderData[0]['total']; */
            //Commented By HJ On 24-05-2019 For Optimize Code End
            $totOrdItems = 0;
            if (isset($orderDataCountArr[$value['iOrderId']])) {
                $totOrdItems = $orderDataCountArr[$value['iOrderId']];
            }
            $db_orders[$key]['TotalItems'] = $totOrdItems;
            $db_orders[$key]['eTakeaway'] = !empty($value['eTakeaway']) ? $value['eTakeaway'] : "No";
            $eConfirm = "No";
            if (isset($eConfirmArr[$value['iOrderId']]) && $eConfirmArr[$value['iOrderId']] > 0) {
                $eConfirm = "Yes";
            }
            //$eConfirm = checkOrderStatus($value['iOrderId'], "2");
            $db_orders[$key]['eConfirm'] = $eConfirm;
            //commented by SP on 04-02-2021 - no idea why it is put, so commented bcoz it will shown some records only and in total full record shown like 4 shown and in order list 2 shown...
            //if ($eConfirm == 'Yes' && $statusCode == 1) {
            //    unset($db_orders[$key]);
            //}
            $db_orders[$key]['eOrderType'] = $languageLabelsArr['LBL_DELIVERY_TXT'];
            if ($value['eOrderplaced_by'] == "Kiosk" && $value['eTakeaway'] == "No") {
                $db_orders[$key]['eOrderType'] = $languageLabelsArr['LBL_DINE_IN_TXT'];
            }
            $db_orders[$key]['vTextColor'] = '#FFFFFF';
            $db_orders[$key]['vBgColor'] = RANDOM_COLORS_ARR[array_rand(RANDOM_COLORS_ARR, 1)];
        }
        $db_orders = array_values($db_orders);
    }
    if ($TotalPages > $page) {
        $returnArr['NextPage'] = "" . ($page + 1);
    }
    else {
        $returnArr['NextPage'] = "0";
    }
    $getOrderCount = $obj->MySQLSelect("SELECT COUNT(iOrderId) As TotalIds,iStatusCode FROM orders WHERE  iCompanyId='" . $iCompanyId . "' AND iStatusCode IN ('1','2','4','5') GROUP BY iStatusCode");
    $newOrderCount = $dispatchOrderCount = $processOrderCount = 0;
    for ($r = 0; $r < count($getOrderCount); $r++) {
        $iStatusCode = $getOrderCount[$r]['iStatusCode'];
        $TotalIds = $getOrderCount[$r]['TotalIds'];
        if ($iStatusCode == 1) {
            $newOrderCount += $TotalIds;
        }
        else if ($iStatusCode == 5) {
            $dispatchOrderCount += $TotalIds;
        }
        else if ($iStatusCode == 2 || $iStatusCode == 4) {
            $processOrderCount += $TotalIds;
        }
    }
    $returnArr['TotalOrders'] = strval($totOrdCount);
    $returnArr['TotalOrdersNewCount'] = strval($newOrderCount);
    $returnArr['TotalOrdersDispatchCount'] = strval($dispatchOrderCount);
    if ((!empty($db_orders))) {
        $returnArr['Action'] = "1";
        $returnArr['message'] = $db_orders;
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_NO_ORDERS_FOUND_TXT";
        if ($OrderType == 'NEW') {
            $returnArr['message1'] = "LBL_NO_NEW_ORDERS_MSG";
        }
        else if ($OrderType == 'DISPATCHED') {
            $returnArr['message1'] = "LBL_NO_DISPATCH_ORDERS_MSG";
        } 
        else if ($OrderType == 'INPROCESS') {
            $returnArr['message1'] = "LBL_NO_PROCESSING_ORDERS_MSG";
        }
    }
    setDataResponse($returnArr);
}
// ############################ End Get All Order Details For Restaurant #####################################################
// ######################## Get Single Order Details #####################################################################
if ($type == "GetOrderDetailsRestaurant") {
    $iOrderId = isset($_REQUEST["iOrderId"]) ? $_REQUEST["iOrderId"] : "";
    $vTimeZone = isset($_REQUEST["vTimeZone"]) ? $_REQUEST["vTimeZone"] : "Asia/Kolkata";
    $UserType = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : 'Company';
    $GeneralUserType = isset($_REQUEST["GeneralUserType"]) ? $_REQUEST["GeneralUserType"] : 'Company';
    $IS_FROM_HISTORY = isset($_REQUEST["IS_FROM_HISTORY"]) ? $_REQUEST["IS_FROM_HISTORY"] : 'No';
    $vGeneralLang = isset($_REQUEST["vGeneralLang"]) ? $_REQUEST["vGeneralLang"] : '';
    if ($IS_FROM_HISTORY == "" || $IS_FROM_HISTORY == NULL) {
        $IS_FROM_HISTORY = "No";
    }
    if ($UserType == "" || $UserType == NULL) {
        $UserType = "Company";
    }
    $db_orders = GetOrderDetails($iOrderId, $vTimeZone, $UserType, $IS_FROM_HISTORY);
    $storeImgUrl = $tconfig["tsite_upload_images_compnay"];
    if (!empty($db_orders)) {
        $returnArr['Action'] = "1";
        $returnArr['message'] = $db_orders[0];
        $iCompanyId = $db_orders[0]['iCompanyId'];
        $iServiceId = $db_orders[0]['iServiceId'];
        $UserDetails_Arr = getUserCurrencyLanguageDetails($db_orders[0]['iUserId'], $iOrderId);
        $GetAllMenuItemOptionsTopping_Arr = GetAllMenuItemOptionsTopping($iCompanyId, $UserDetails_Arr['currencySymbol'], $UserDetails_Arr['Ratio'], $UserDetails_Arr['vLang'], "", $iServiceId, $UserDetails_Arr['currencycode']);
        $checkOrderRequestStatusArr = checkOrderRequestStatus($iOrderId);
        $action = $checkOrderRequestStatusArr['Action'];
        $AssignStatus = $checkOrderRequestStatusArr['message1'];
        $orderexist = "No";
        if ($AssignStatus == "DRIVER_ASSIGN") {
            $orderexist = checkOrderStatus($iOrderId, "5");
        }
        $ispriceshow = '';
        //$servFields = 'eType';
        //$ServiceCategoryData = get_value('service_categories', $servFields, 'iServiceId', $iServiceId);
        //Added By HJ On 13-07-2020 For Optimize service_categories Table Query Start
        if (!isset($serviceCatDataArr)) {
            $serviceCatDataArr = $obj->MySQLSelect("SELECT * FROM service_categories");
        }

        $serviceCatArr = array();
        for ($h = 0; $h < count($serviceCatDataArr); $h++) {
            $serviceCatArr[$serviceCatDataArr[$h]['iServiceId']] = $serviceCatDataArr[$h];
        }
        //$servFields = 'eType';
        //$ServiceCategoryData = get_value('service_categories', $servFields, 'iServiceId', $iServiceId);
        if (isset($serviceCatArr[$iServiceId])) {
            $ServiceCategoryData = array();
            $ServiceCategoryData[] = $serviceCatArr[$iServiceId];
        }
        else {
            $ServiceCategoryData = get_value('service_categories', $servFields, 'iServiceId', $iServiceId);
        }
        //Added By HJ On 13-07-2020 For Optimize service_categories Table Query End
        if (!empty($ServiceCategoryData)) {
            if (!empty($ServiceCategoryData[0]['eType'])) {
                $ispriceshow = $ServiceCategoryData[0]['eType'];
            }
        }
        $returnArr['ispriceshow'] = $ispriceshow;
        $DisplayReorder = checkOrderStatus($iOrderId, "6");
        $REQUEST_REMAINS_SEC = getremainingtimeorderrequest($iOrderId);
        $returnArr['message']['AssignStatus'] = $AssignStatus;
        $returnArr['message']['eOrderPickedByDriver'] = $orderexist;
        $returnArr['message']['REQUEST_REMAINS_SEC'] = $REQUEST_REMAINS_SEC;
        $returnArr['message']['options'] = !empty($GetAllMenuItemOptionsTopping_Arr['options']) ? $GetAllMenuItemOptionsTopping_Arr['options'] : "";
        $returnArr['message']['addon'] = !empty($GetAllMenuItemOptionsTopping_Arr['addon']) ? $GetAllMenuItemOptionsTopping_Arr['addon'] : "";
        $returnArr['message']['DisplayReorder'] = $DisplayReorder;
        $returnArr['message']['currencySymbol'] = $UserDetails_Arr['currencySymbol'];
        $returnArr['message']['OrderStatustext'] = GetOrderStatusLogText($iOrderId, $UserType);
        $returnArr['message']['OrderStatusValue'] = str_replace("on", "", GetOrderStatusLogText($iOrderId, $UserType, "Yes"));
        $returnArr['message']['OrderMessage'] = GetOrderStatusLogTextForCancelled($iOrderId, $UserType);
        //added by SP for cubex on 11-10-2019 start
        $logText = GetOrderStatusLogTextForCancelledSplit($iOrderId, $UserType);
        $vStatusNew = $returnArr['message']['vStatus'];
        //$sql = "select ru.vLang,ord.eTakeaway, ru.eDriverOption from orders as ord LEFT JOIN company as ru ON ord.iCompanyId=ru.iCompanyId where ord.iOrderId = '" . $iOrderId . "'";
        //$data_order_company = $obj->MySQLSelect($sql);
        //Added By HJ On 13-07-2020 For Optimize Order Table Query Start
        if (isset($orderDetailsArr['orders_' . $iOrderId])) {
            $data_order_company = $orderDetailsArr['orders_' . $iOrderId];
        }
        else {
            $data_order_company = $obj->MySQLSelect("SELECT * FROM orders WHERE iOrderId = '" . $iOrderId . "'");
            $orderDetailsArr['orders_' . $iOrderId] = $data_order_company;
        }
        //Added By HJ On 13-07-2020 For Optimize Order Table Query End
        //Added By HJ On 13-07-2020 For Optimize company Table Query Start
        if (isset($userDetailsArr['company_' . $iCompanyId])) {
            $data_company_detail = $userDetailsArr['company_' . $iCompanyId];
        }
        else {
            $data_company_detail = $obj->MySQLSelect("SELECT *,iCompanyId as iMemberId FROM company WHERE iCompanyId='" . $iCompanyId . "'");
            $userDetailsArr['company_' . $iCompanyId] = $data_company_detail;
        }
        $data_order_company[0]['vLang'] = $data_company_detail[0]['vLang'];
        $data_order_company[0]['eDriverOption'] = $data_company_detail[0]['eDriverOption'];
        //Added By HJ On 13-07-2020 For Optimize company Table Query End
        $vLangCode = $data_order_company[0]['vLang'];
        if ($vLangCode == "" || $vLangCode == NULL) {
            //Added By HJ On 13-07-2020 For Optimize language_master Table Query Start
            $vLangCode = $LANG_OBJ->FetchDefaultLangData("vCode");
            //Added By HJ On 13-07-2020 For Optimize language_master Table Query End
        }
        if (isset($vGeneralLang) && !empty($vGeneralLang)) {
            $vLangCode = $vGeneralLang;
        }
        //Added By HJ On 13-07-2020 For langauge labele and Other Union Table Query Start
        if (isset($languageLabelDataArr['language_label_union_other_food_' . $vLangCode . "_" . $iServiceId])) {
            $languageLabelsArr = $languageLabelDataArr['language_label_union_other_food_' . $vLangCode . "_" . $iServiceId];
        }
        else {
            $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLangCode, "1", $iServiceId);
            $languageLabelDataArr['language_label_union_other_food_' . $vLangCode . "_" . $iServiceId] = $languageLabelsArr;
        }
        //Added By HJ On 13-07-2020 For langauge labele and Other Union Table Query End
        $key_arr = array("#STORE#", "#DriverName#");
        if ($UserType == 'Company') {
            $val_arr1 = $languageLabelsArr['LBL_YOU_TEXT'];
        }
        else {
            $val_arr1 = $languageLabelsArr['LBL_RESTAURANT_TXT_ADMIN'];
            if ($data_order_company[0]['eBuyAnyService'] == "Yes") {
                $val_arr1 = $languageLabelsArr['LBL_STORE'];
            }
        }
        $val_arr = array($val_arr1, $db_orders[0]['DriverName']);
        $vStatusNew = str_replace($key_arr, $val_arr, $vStatusNew);
        $isOpenDriverSelection = "No";
        $isStoreDriverOption = $MODULES_OBJ->isStorePersonalDriverAvailable();
        if (isset($data_order_company[0]['eDriverOption']) && strtoupper($data_order_company[0]['eDriverOption']) == "ALL" && $isStoreDriverOption > 0) {
            $isOpenDriverSelection = "Yes";
        }
        $returnArr['message']['isOpenDriverSelection'] = $isOpenDriverSelection;
        $returnArr['message']['vStatus'] = $vStatusNew;
        $returnArr['message']['vStatusNew'] = (!empty($logText['Displaytext'])) ? $logText['Displaytext'] : $vStatusNew;
        $returnArr['message']['CancelOrderMessage'] = $logText['Displaytext1'];
        $returnArr['message']['CancelOrderReason'] = (!empty($logText['vCancelReason'])) ? $languageLabelsArr['LBL_CANCELLATION_REASON'] . ": " . $logText['vCancelReason'] : "";
        //added by SP for cubex on 11-10-2019 end
        if ($data_order_company[0]['eBuyAnyService'] == "Yes") {
            $val_arr1 = $languageLabelsArr['LBL_STORE'];
            if ($data_order_company[0]['iStatusCode'] == "1" || $data_order_company[0]['iStatusCode'] == "2") {
                $returnArr['message']['vStatusNew'] = $languageLabelsArr['LBL_WAITING_DRIVER_ACCEPT_TXT'];
            }
        }
        //$sqlc = "select fMinOrderValue,vImage,vAvgRating from `company` where iCompanyId='" . $iCompanyId . "'";
        //$data_company_detail = $obj->MySQLSelect($sqlc);
        $fMinOrderValue = $data_company_detail[0]['fMinOrderValue'];
        $fMinOrderValueArr = getPriceUserCurrency($db_orders[0]['iUserId'], "Passenger", $fMinOrderValue);
        $fMinOrderValue = $fMinOrderValueArr['fPrice'];
        $returnArr['message']['fMinOrderValue'] = $fMinOrderValue;
        $returnArr['message']['companyImage'] = $storeImgUrl . "/" . $iCompanyId . "/" . $data_company_detail[0]['vImage'];
        $returnArr['message']['vAvgRating'] = $data_company_detail[0]['vAvgRating'];
        //Added BY HJ On 26-06-2019 For Display In App Start
        $is_rating = "No";
        if ($data_order_company[0]['eBuyAnyService'] == "Yes") {
            $returnArr['message']['vAvgRating'] = "";
            $ratingsData = $obj->MySQLSelect("SELECT iRatingId FROM ratings_user_driver WHERE iOrderId = $iOrderId AND eFromUserType = 'Passenger' AND eToUserType = 'Driver' ");
            if (count($ratingsData) > 0) {
                $is_rating = "Yes";
            }
        }
        $returnArr['message']['is_rating'] = $is_rating;
        $iActive = "No";
        if ($data_order_company[0]['iStatusCode'] == 6) {
            $iActive = "Yes";
        }
        $returnArr['message']['iActive'] = $iActive;
        $vInstruction = "";
        if (isset($db_orders[0]['vInstruction']) && $db_orders[0]['vInstruction'] != "") {
            $vInstruction = $db_orders[0]['vInstruction'];
        }
        $returnArr['message']['vInstruction'] = $vInstruction;
        //Added BY HJ On 26-06-2019 For Display In App End
        if ($data_order_company[0]['eBuyAnyService'] == "Yes") {
            $DisplayReorder = "No";
            $returnArr['message']['DisplayReorder'] = "No";
        }
        if ($DisplayReorder == "Yes") {
            $query = "SELECT * FROM order_details WHERE iOrderId = '" . $iOrderId . "'";
            $orderDetails = $obj->MySQLSelect($query);
            $DataReorder = array();
            for ($i = 0; $i < count($orderDetails); $i++) {
                $DataReorder[$i] = GetOrderDetailsForReorder($orderDetails[$i]['iOrderDetailId'], $db_orders[0]['iUserId'], "Passenger", $db_orders[0]['iCompanyId'], $iServiceId);
            }
            $returnArr['message']['DataReorder'] = $DataReorder;
        }
        //Get Prescription Images from orderid done by sneha start
        $getImages = $obj->MySQLSelect("Select * from prescription_images WHERE order_id = '" . $iOrderId . "'");
        foreach ($getImages as $key => $value) {
            $prescriptionimage[] = $tconfig['tsite_upload_prescription_image'] . '/' . $value['vImage'];
        }
        if (!empty($prescriptionimage)) {
            $returnArr['message']['PrescriptionImages'] = $prescriptionimage;
        }
        else {
            $returnArr['message']['PrescriptionImages'] = "";
        }
        //Get Prescription Images from orderid done by sneha end
        $returnArr['message']['eTakeAway'] = !empty($data_order_company[0]['eTakeaway']) ? $data_order_company[0]['eTakeaway'] : "No";
        $returnArr['message']['eTakeAwayPickedUpNote'] = "";
        if ($DisplayReorder == 'Yes') {
            $returnArr['message']['eTakeAwayPickedUpNote'] = str_replace('#RESTAURANT_NAME#', $db_orders[0]['vCompany'], $languageLabelsArr['LBL_TAKE_AWAY_ORDER_NOTE']);
        }
        if ($returnArr['message']['eTakeAway'] == 'Yes') {
            $returnArr['message']['vStatusNew'] = $languageLabelsArr['LBL_OREDR_PICKED_UP_TXT'];
        }
        if ($UserType == "Company") {
            $iUserId = $db_orders[0]['iCompanyId'];
        }
        else {
            $iUserId = $data_order_company[0]['iDriverId'];
        }
        $returnArr['message']['voiceDirectionFileUrl'] = "";
        if ($UserType == "Driver") {
            $returnArr['message']['InvoiceTitle'] = $languageLabelsArr['LBL_TOTAL_BILL_TXT'];
            $returnArr['message']['eShowPaymentInfo'] = "No";
            $returnArr['message']['PaymentInfo'] = "";
            if ($data_order_company[0]['iStatusCode'] == '5') {
                $returnArr['message']['eShowPaymentInfo'] = "No";
                $returnArr['message']['PaymentInfo'] = "";
            }
            if (!empty($data_order_company[0]['tVoiceDirectionFile'])) {
                $returnArr['message']['voiceDirectionFileUrl'] = $tconfig['tsite_upload_voice_direction_file'] . 'Orders/' . $data_order_company[0]['tVoiceDirectionFile'];
            }
        }
        $returnArr['message']['eBuyAnyService'] = $data_order_company[0]['eBuyAnyService'];
        $returnArr['message']['genieWaitingForUserApproval'] = $data_order_company[0]['genieWaitingForUserApproval'];
        $returnArr['message']['genieUserApproved'] = $data_order_company[0]['genieUserApproved'];
        $returnArr['message']['ePaid'] = $data_order_company[0]['ePaid'];
        $returnArr['message']['eForPickDropGenie'] = $data_order_company[0]['eForPickDropGenie'];
        if ($data_order_company[0]['eBuyAnyService'] == "Yes") {
            $returnArr['message']['GenieOrderType'] = "Anywhere";
            if ($data_order_company[0]['eForPickDropGenie'] == "Yes") {
                $returnArr['message']['GenieOrderType'] = "Runner";
                $returnArr['message']['companyImage'] = $tconfig['tsite_url'] . 'webimages/icons/DefaultImg/ic_map_placeholder_1.png';
            }
        }
        if ($data_order_company[0]['ePaymentOption'] == "Cash" && $data_order_company[0]['iStatusCode'] == '5') {
            $returnArr['message']['ePaid'] = 'Yes';
        }
        $fTipAmountArr = getPriceUserCurrency($iUserId, $UserType, $data_order_company[0]['fTipAmount']);
        $returnArr['message']['fTipAmount'] = $fTipAmountArr['fPrice'];
        $returnArr['message']['fTipAmountWithSymbol'] = $fTipAmountArr['fPricewithsymbol'];
        $returnArr['DeliveryPreferences']['Enable'] = ($MODULES_OBJ->isDeliveryPreferenceEnable() == true) ? 'Yes' : 'No';
        if ($MODULES_OBJ->isDeliveryPreferenceEnable()) {
            //$selectedPrefSql = "SELECT selectedPreferences, vImageDeliveryPref FROM orders WHERE iOrderId = " . $iOrderId;
            //$selectedPrefData = $obj->MySQLSelect($selectedPrefSql);
            //Added By HJ On 13-07-2020 For Optimize Order Table Query Start
            if (isset($orderDetailsArr['orders_' . $iOrderId])) {
                $selectedPrefData = $orderDetailsArr['orders_' . $iOrderId];
            }
            else {
                $selectedPrefData = $obj->MySQLSelect("SELECT * FROM orders WHERE iOrderId = '" . $iOrderId . "'");
                $orderDetailsArr['orders_' . $iOrderId] = $selectedPrefData;
            }
            //Added By HJ On 13-07-2020 For Optimize Order Table Query End
            $selectedPrefIds = "";
            if ($selectedPrefData[0]['selectedPreferences'] != "") {
                $selectedPrefIds = $selectedPrefData[0]['selectedPreferences'];
            }
            $ssql = "";
            if (strtolower($GeneralUserType) == 'company') {
                $ssql .= " WHERE ePreferenceFor = 'Store' AND iPreferenceId IN (" . $selectedPrefIds . ")";
            }
            elseif (strtolower($GeneralUserType) == 'driver') {
                $ssql .= " WHERE ePreferenceFor = 'Provider' AND iPreferenceId IN (" . $selectedPrefIds . ")";
            }
            elseif (strtolower($GeneralUserType) == 'passenger') {
                $ssql .= " WHERE iPreferenceId IN (" . $selectedPrefIds . ")";
            }
            $deliveryPrefSql = "SELECT iPreferenceId,JSON_UNQUOTE(JSON_VALUE(tTitle, '$.tTitle_" . $vLangCode . "')) as tTitle, JSON_UNQUOTE(JSON_VALUE(tDescription, '$.tDescription_" . $vLangCode . "')) as tDescription, ePreferenceFor, eImageUpload, iDisplayOrder, eContactLess, eStatus FROM delivery_preferences " . $ssql;
            $deliveryPrefSqlData = $obj->MySQLSelect($deliveryPrefSql);
            $returnArr['DeliveryPreferences']['vTitle'] = "";
            $returnArr['DeliveryPreferences']['vImageDeliveryPref'] = "";
            if (strtolower($GeneralUserType) != 'passenger') {
                $returnArr['DeliveryPreferences']['isContactLessDeliverySelected'] = 'No';
                $returnArr['DeliveryPreferences']['isPreferenceImageUploadRequired'] = 'No';
                if(!empty($deliveryPrefSqlData)) {
                    $returnArr['DeliveryPreferences']['vTitle'] = $deliveryPrefSql[0]['ePreferenceFor'] == "Store" ? $languageLabelsArr['LBL_USER_PREF'] : $languageLabelsArr['LBL_DELIVERY_PREF'];

                    foreach ($deliveryPrefSqlData as $dvalue) {
                        if ($dvalue['eContactLess'] == "Yes") {
                            $returnArr['DeliveryPreferences']['isContactLessDeliverySelected'] = 'Yes';
                        }
                        if ($dvalue['eImageUpload'] == "Yes") {
                            $returnArr['DeliveryPreferences']['isPreferenceImageUploadRequired'] = 'Yes';
                        }
                    }
                }
                
            }
            if (strtolower($GeneralUserType) == 'passenger' || strtolower($GeneralUserType) == 'driver') {
                if ($selectedPrefData[0]['vImageDeliveryPref'] != "") {
                    $returnArr['DeliveryPreferences']['vImageDeliveryPref'] = $tconfig['tsite_upload_order_delivery_pref_images'] . $selectedPrefData[0]['vImageDeliveryPref'];
                }
            }
            $returnArr['DeliveryPreferences']['Data'] = $deliveryPrefSqlData;
            if ((strtolower($GeneralUserType) == 'company' || strtolower($GeneralUserType) == 'driver' || strtolower($GeneralUserType) == 'passenger') && $selectedPrefIds == "") {
                //commented by SP to solve the issue#17040 on 22-12-2020
                $returnArr['DeliveryPreferences']['Enable'] = 'No';
                unset($returnArr['DeliveryPreferences']['vTitle']);
                unset($returnArr['DeliveryPreferences']['Data']);
                unset($returnArr['DeliveryPreferences']['isContactLessDeliverySelected']);
                unset($returnArr['DeliveryPreferences']['isPreferenceImageUploadRequired']);
                unset($returnArr['DeliveryPreferences']['vImageDeliveryPref']);
            }
            else {
                if (count($returnArr['DeliveryPreferences']['Data']) == 0) {
                    $returnArr['DeliveryPreferences']['Enable'] = 'No';
                    unset($returnArr['DeliveryPreferences']['vTitle']);
                    unset($returnArr['DeliveryPreferences']['Data']);
                    unset($returnArr['DeliveryPreferences']['isContactLessDeliverySelected']);
                    unset($returnArr['DeliveryPreferences']['isPreferenceImageUploadRequired']);
                    unset($returnArr['DeliveryPreferences']['vImageDeliveryPref']);
                }
            }
        }
        if (strtolower($GeneralUserType) == 'company') {
            if (isset($languageAssociateArr[$vLangCode])) {
                $eDirectionCode = $languageAssociateArr[$vLangCode]['eDirectionCode'];
            }
            else {
                $eDirectionCode = get_value('language_master', 'eDirectionCode', 'vCode', $vLangCode, '', 'true');
            }
            $meta = $STATIC_PAGE_OBJ->FetchStaticPage(47, $vLangCode);
            $kotBillFormat = $meta[0]['tPageDesc_' . $vLangCode];
            $kotBillFormat = strip_tags($kotBillFormat);
            $kotBillFormat = explode('#', $kotBillFormat);
            $kotBillFormat = array_map('trim', $kotBillFormat);
            $kotBillFormat = array_values(array_filter($kotBillFormat));
            $html_content = array(
                'COMPANY_NAME' => '<span style="font-size: 20px"><b>' . $db_orders[0]['vCompany'] . '</b></span>', 'ORDER_DATETIME' => $db_orders[0]['tOrderRequestDate'], 'ORDER_NO' => $db_orders[0]['vOrderNo'], 'ORDER_VIA' => $SITE_NAME, 'CUSTOMER_NAME' => $db_orders[0]['UserName'], 'INSTRUCTIONS' => !empty($vInstruction) ? $vInstruction : "-", 'ITEM_LIST' => $db_orders[0]['itemlist'], 'DELIVERY_TYPE' => ($data_order_company[0]['eTakeaway'] == "Yes") ? $languageLabelsArr['LBL_TAKE_WAY'] : $languageLabelsArr['LBL_HOME'] . ' ' . $languageLabelsArr['LBL_DELIVERY'], 'TOTAL_FARE' => $db_orders[0]['FareTotal']
            );
            $receiptPdfData = getReceiptPdf($kotBillFormat, $html_content, $eDirectionCode, $vLangCode, $db_orders[0]['iServiceId']);
            // $returnArr['message']['tReceiptData'] = $receiptPdfData;
            if (!is_dir($tconfig['tpanel_path'] . 'webimages/order_invoices/')) {
                mkdir($tconfig['tpanel_path'] . 'webimages/order_invoices/', 0777);
                chmod($tconfig['tpanel_path'] . 'webimages/order_invoices/', 0777);
            }
            $order_invoice_name = 'ORDER_' . $db_orders[0]['vOrderNo'];
            $order_invoice_path = $tconfig['tpanel_path'] . 'webimages/order_invoices/ORDER_' . $db_orders[0]['vOrderNo'] . '_' . date('YmdHis') . '.pdf';
            $order_invoice_path_exist = $tconfig['tpanel_path'] . 'webimages/order_invoices/ORDER_' . $db_orders[0]['vOrderNo'] . '_*.pdf';
            foreach (glob($order_invoice_path_exist) as $file_to_delete) {
                unlink($file_to_delete);
            }
            file_put_contents($order_invoice_path, base64_decode($receiptPdfData));
            // $returnArr['message']['tReceiptDataImage'] = "";
            if (file_exists($order_invoice_path)) {
                $receiptImageData = getReceiptImage($order_invoice_path, $order_invoice_name);
                // $returnArr['message']['tReceiptDataImage'] = $receiptImageData;
                $order_invoice_image_name = 'ORDER_' . $db_orders[0]['vOrderNo'] . '_' . date('YmdHis') . '.jpg';
                $order_invoice_image_path = $tconfig['tpanel_path'] . 'webimages/order_invoices/' . $order_invoice_image_name;
                $order_invoice_image_path_exist = $tconfig['tpanel_path'] . 'webimages/order_invoices/ORDER_' . $db_orders[0]['vOrderNo'] . '_*.jpg';
                foreach (glob($order_invoice_image_path_exist) as $file_to_delete) {
                    unlink($file_to_delete);
                }
                file_put_contents($order_invoice_image_path, base64_decode($receiptImageData));
                $returnArr['message']['tReceiptDataImageUrl'] = "";
                if (file_exists($order_invoice_image_path)) {
                    $order_invoice_image_path_url = $tconfig['tsite_url'] . 'webimages/order_invoices/' . $order_invoice_image_name;
                    $returnArr['message']['tReceiptDataImageUrl'] = $order_invoice_image_path_url;
                }
            }
        }
        $eDriverPaymentStatus = get_value('trips', 'eDriverPaymentStatus', 'iOrderId', $iOrderId, '', 'true');
        $returnArr['message']['giveTip'] = 'No';
        if ($data_order_company[0]['fTipAmount'] == 0 && $data_order_company[0]['iStatusCode'] == 6 && $db_orders[0]['ePaymentOption'] == "Card" && $eDriverPaymentStatus == "Unsettelled" && $data_order_company[0]['eTakeaway'] == "No" && $MODULES_OBJ->isEnableDeliveryTipFeatureDeliverAll() && strtolower($GeneralUserType) == 'passenger' && strtolower(ENABLE_DELIVERY_TIP_IN_HISTORY) == 'yes' && $data_order_company[0]['eBuyAnyService'] == "No") {
            $returnArr['message']['giveTip'] = 'Yes';
        }
        $returnArr['message']['vIdProofImage'] = "";
        $returnArr['message']['vIdProofImageNote'] = "";
        $returnArr['message']['vIdProofImageUploaded'] = "No";
        if (!empty($data_order_company[0]['vIdProofImg'])) {
            $returnArr['message']['vIdProofImage'] = $tconfig['tsite_upload_id_proof_service_categories_images'] . "Orders/" . $data_order_company[0]['vIdProofImg'];
            if (strtolower($GeneralUserType) == 'company') {
                $scData = $obj->MySQLSelect("SELECT JSON_UNQUOTE(JSON_VALUE(tProofNoteStore, '$.tProofNoteStore_" . $vLangCode . "')) as tProofNote FROM service_categories WHERE iServiceId = $iServiceId");
            }
            elseif (strtolower($GeneralUserType) == 'driver') {
                $scData = $obj->MySQLSelect("SELECT JSON_UNQUOTE(JSON_VALUE(tProofNoteDriver, '$.tProofNoteDriver_" . $vLangCode . "')) as tProofNote FROM service_categories WHERE iServiceId = $iServiceId");
            }
            else {
                $scData = $obj->MySQLSelect("SELECT JSON_UNQUOTE(JSON_VALUE(tProofNote, '$.tProofNoteStore_" . $vLangCode . "')) as tProofNote FROM service_categories WHERE iServiceId = $iServiceId");
            }
            if (is_null($scData[0]['tProofNote']) || $scData[0]['tProofNote'] == "null") {
                $scData[0]['tProofNote'] = "";
            }
            if (is_null($scData[0]['tProofNote']) || $scData[0]['tProofNote'] == "null") {
                $scData[0]['tProofNote'] = "";
            }
            $vIdProofImageNote = $scData[0]['tProofNote'];
            $returnArr['message']['vIdProofImageNote'] = $vIdProofImageNote;
            $returnArr['message']['vIdProofImageUploaded'] = "Yes";
        }
        $returnArr['message']['eAutoaccept'] = ($MODULES_OBJ->isEnableAutoAcceptStoreOrder()) ? $data_company_detail[0]['eAutoaccept'] : 'No';
        if (strtolower($GeneralUserType) == 'driver') {
            $returnArr['message']['eAskCodeToUser'] = $data_order_company[0]['eAskCodeToUser'];
            $returnArr['message']['vRandomCode'] = strlen($data_order_company[0]['vRandomCode']);
            $returnArr['message']['vText'] = (!empty($data_order_company[0]['vRandomCode'])) ? encodeVerificationCode($data_order_company[0]['vRandomCode']) : "";
        }
        //added by SP for store panel in web not needed it for app.
        $returnArr['message']['LBL_PROOF_DECLINE_NOTE'] = $languageLabelsArr['LBL_PROOF_DECLINE_NOTE'];
        $returnArr['message']['proffDataforWeb'] = "<img class='smallimg' src='" . $returnArr['message']['vIdProofImage'] . "'><br>" . $returnArr['message']['vIdProofImageNote'];
        $returnArr['message']['eOrderType'] = $data_order_company[0]['eOrderplaced_by'] == "Kiosk" && $data_order_company[0]['eTakeaway'] == "No" ? "Dine In" : "";
        $returnArr['message']['eOrderplacedBy'] = $data_order_company[0]['eOrderplaced_by'];
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_NO_ORDERS_FOUND_TXT";
    }
    setDataResponse($returnArr);
}
// ######################## End Get Single Order Details ###################################################################
// ######################## Update Single Order Details ###################################################################
if ($type == "UpdateOrderDetailsRestaurant") {
    $iOrderId = isset($_REQUEST["iOrderId"]) ? $_REQUEST["iOrderId"] : "";
    $iOrderDetailId = isset($_REQUEST["iOrderDetailId"]) ? $_REQUEST["iOrderDetailId"] : "";
    $eAvailable = isset($_REQUEST["eAvailable"]) ? $_REQUEST["eAvailable"] : "Yes";
    $vTimeZone = isset($_REQUEST["vTimeZone"]) ? $_REQUEST["vTimeZone"] : "";
    $where = " iOrderDetailId = '" . $iOrderDetailId . "'";
    $Data_update_order_details['eAvailable'] = $eAvailable;
    $OrderDetail_Update_Id = $obj->MySQLQueryPerform("order_details", $Data_update_order_details, 'update', $where);
    $Order_data = calculateOrderFare($iOrderId);
    $where = " iOrderId = '" . $iOrderId . "'";
    $Data_update_order['fSubTotal'] = $Order_data['fSubTotal'];
    $Data_update_order['fPackingCharge'] = $Order_data['fPackingCharge'];
    $Data_update_order['fDeliveryCharge'] = $Order_data['fDeliveryCharge'];
    $Data_update_order['fTax'] = $Order_data['fTax'];
    $Data_update_order['fDiscount'] = $Order_data['fDiscount'];
    $Data_update_order['vDiscount'] = $Order_data['vDiscount'];
    $Data_update_order['fCommision'] = $Order_data['fCommision'];
    $Data_update_order['fNetTotal'] = $Order_data['fNetTotal'];
    $Data_update_order['fTotalGenerateFare'] = $Order_data['fTotalGenerateFare'];
    $Data_update_order['fOutStandingAmount'] = $Order_data['fOutStandingAmount'];
    $Data_update_order['fWalletDebit'] = $Order_data['fWalletDebit'];
    $Order_Update_Id = $obj->MySQLQueryPerform("orders", $Data_update_order, 'update', $where);
    if ($Order_Update_Id > 0) {
        $returnArr['Action'] = "1";
        $returnArr['message'] = "LBL_ORDER_DETAILS_UPDATE";
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_NO_ORDERS_FOUND_TXT";
    }
    setDataResponse($returnArr);
}
// ######################## End Update Single Order Details ###############################################################
// ######################## Get Cancel Reason #############################################################################
if ($type == "GetCancelReasons") {
    $iMemberId = isset($_REQUEST["iMemberId"]) ? $_REQUEST["iMemberId"] : "";
    $eUserType = isset($_REQUEST["eUserType"]) ? $_REQUEST["eUserType"] : "";
    $iTripId = isset($_REQUEST["iTripId"]) ? $_REQUEST["iTripId"] : "";
    if ($eUserType == "Passenger") {
        $UserDetailsArr = getUserCurrencyLanguageDetails($iMemberId);
    }
    else if ($eUserType == "Driver") {
        $UserDetailsArr = getDriverCurrencyLanguageDetails($iMemberId);
    }
    else {
        $UserDetailsArr = getCompanyCurrencyLanguageDetails($iMemberId);
    }
    $vLang = $UserDetailsArr['vLang'];
    $sql = "SELECT vTitle_" . $vLang . " as vTitle,iCancelReasonId FROM cancel_reason WHERE eStatus = 'Active' AND eType = 'DeliverAll' AND (eFor = '" . $GeneralUserType . "' OR eFor='General')";
    $CancelReasonData = $obj->MySQLSelect($sql);
    if (!empty($CancelReasonData)) {
        $returnArr['Action'] = "1";
        $returnArr['message'] = $CancelReasonData;
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_NO_DATA_AVAIL";
    }
    setDataResponse($returnArr);
}
// ######################## End Get Cancel Reason #########################################################################
// ######################## Start Order Decline ######################################################
if ($type == "DeclineOrder") {
    $iMemberId = isset($_REQUEST["iCompanyId"]) ? $_REQUEST["iCompanyId"] : "";
    $UserType = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : "";
    $vCancelReason = isset($_REQUEST["vCancelReason"]) ? $_REQUEST["vCancelReason"] : "";
    $iReasonId = isset($_REQUEST["iCancelReasonId"]) ? $_REQUEST["iCancelReasonId"] : "";
    $iOrderId = isset($_REQUEST["iOrderId"]) ? $_REQUEST["iOrderId"] : "";
    $_REQUEST['eSystem'] = "DeliverAll";
    if ($UserType == 'Driver') {
        $eCancelledBy = 'Driver';
    }
    else if ($UserType == 'Passenger') {
        $eCancelledBy = 'Passenger';
    }
    else {
        $eCancelledBy = 'Company';
        $UserType = 'Company'; //added by SP for emailissue on 3-7-2019
    }
    if (!empty($iOrderId)) {
        $sql = "select ru.iUserId, ord.iStatusCode, ord.vOrderNo from orders as ord LEFT JOIN register_user as ru ON ord.iUserId=ru.iUserId where ord.iOrderId = '" . $iOrderId . "' AND ord.iStatusCode='8'";
        $data_order = $obj->MySQLSelect($sql);
        if (count($data_order) > 0) {
            $returnArr['Action'] = "1";
            $returnArr['message'] = "LBL_CANCEL_ORDER_ADMIN_TXT";
            $returnArr['DO_RESTART'] = "Yes";
            setDataResponse($returnArr);
        }
        $data_order = $obj->MySQLSelect("select iStatusCode from orders where iOrderId = '" . $iOrderId . "' and iStatusCode!='1'");
        if (count($data_order) > 0) {
            if ($data_order[0]['iStatusCode'] == 2) {
                $checkOrderRequestStatusArr = checkOrderRequestStatus($iOrderId);
                $action = $checkOrderRequestStatusArr['Action'];
                $AssignStatus = $checkOrderRequestStatusArr['message1'];
                if ($AssignStatus != "REQ_NOT_FOUND" && $AssignStatus != "DRIVER_ASSIGN" && $AssignStatus != "REQ_PROCESS" && $AssignStatus != "REQ_FAILED") {
                    $returnArr['Action'] = "0";
                    $returnArr['message'] = "LBL_SOMETHING_WENT_WRONG_TRY_AGAIN";
                    $returnArr['DO_RESTART'] = "Yes";
                    setDataResponse($returnArr);
                }
            }
            else {
                $returnArr['Action'] = "0";
                $returnArr['message'] = "LBL_SOMETHING_WENT_WRONG_TRY_AGAIN";
                $returnArr['DO_RESTART'] = "Yes";
                setDataResponse($returnArr);
            }
        }
    }
    $where = " iOrderId = '" . $iOrderId . "'";
    $Data_update_order['iCancelledById'] = $iMemberId;
    $Data_update_order['eCancelledBy'] = $eCancelledBy;
    $Data_update_order['iReasonId'] = $iReasonId;
    $Data_update_order['vCancelReason'] = $vCancelReason;
    $Data_update_order['iStatusCode'] = '9';
    $Order_Update_Id = $obj->MySQLQueryPerform("orders", $Data_update_order, 'update', $where);
    $id = createOrderLog($iOrderId, "9");
    // # Send Notification To User ##
    $Message = "OrderDeclineByRestaurant";
    $sql = "select ru.iUserId, ru.iGcmRegId, ru.eDeviceType, ru.tSessionId, ru.vEmail, ru.iAppVersion, ru.vLang, ru.eDebugMode, ord.vOrderNo, ord.fWalletDebit,ord.vCouponCode, CONCAT(ru.vName,' ',ru.vLastName) as vUserName,ru.eAppTerminate,ru.eHmsDevice from orders as ord LEFT JOIN register_user as ru ON ord.iUserId=ru.iUserId where ord.iOrderId = '" . $iOrderId . "'";
    //$sql = "select ru.iUserId, ru.iGcmRegId, ru.eDeviceType, ru.tSessionId, ru.iAppVersion, ru.vLang, ord.vOrderNo from orders as ord LEFT JOIN register_user as ru ON ord.iUserId=ru.iUserId where ord.iOrderId = '" . $iOrderId . "'";
    $data_order = $obj->MySQLSelect($sql);
    $vLangCode = $data_order[0]['vLang'];
    $vOrderNo = $data_order[0]['vOrderNo'];
    $iUserId = $data_order[0]['iUserId'];
    $vUserName = $data_order[0]['vUserName'];
    $vEmail = $data_order[0]['vEmail'];
    $fWalletDebit = $data_order[0]['fWalletDebit'];
    ### Insert Wallet Amount into user's account ####
    // Commented by HV on 16-02-2021 as discussed with KS, wallet amount should not be credited to user wallet. Admin will refund manually.
    /*if ($fWalletDebit > 0) {
        $eUserType = 'Rider';
        $iBalance = $fWalletDebit;
        $eType = 'Credit';
        $eFor = 'Deposit';
        $tDescription = "#LBL_CREDITED_BOOKING_DL#" . $vOrderNo;
        $ePaymentStatus = 'Unsettelled';
        $dDate = Date('Y-m-d H:i:s');
        $WALLET_OBJ->PerformWalletTransaction($iUserId, $eUserType, $iBalance, $eType, 0, $eFor, $tDescription, $ePaymentStatus, $dDate,$iOrderId);
    }*/
    ### Insert Wallet Amount into user's account ####
    //added by SP on 27-06-2020, promocode usage limit increase..bcz it is done only when order finished..so when cancel that order then other user use it..that is wrong so put it...
    $vCouponCode = $data_order[0]['vCouponCode'];
    if ($vCouponCode != '') {
        $sql = "SELECT iUsed, iUsageLimit from coupon WHERE vCouponCode = '" . $vCouponCode . "'";
        $coupon_result = $obj->MySQLSelect($sql);
        $noOfCouponUsed = $coupon_result[0]['iUsed'];
        $iUsageLimit = $coupon_result[0]['iUsageLimit'];
        $where = " vCouponCode = '" . $vCouponCode . "'";
        $data_coupon['iUsed'] = $noOfCouponUsed + 1;
        $obj->MySQLQueryPerform("coupon", $data_coupon, 'update', $where);
        ## Check Coupon Code Usage Limit , Send Email to Admin if Usage  Limit is over ##
        $UpdatedCouponUsedNo = $noOfCouponUsed + 1;
        if ($iUsageLimit == $UpdatedCouponUsedNo) {
            $maildata['vCouponCode'] = $vCouponCode;
            $maildata['iUsageLimit'] = $iUsageLimit;
            $maildata['COMPANY_NAME'] = $COMPANY_NAME;
            $mail = $COMM_MEDIA_OBJ->SendMailToMember('COUPON_LIMIT_COMPLETED_TO_ADMIN', $maildata);
        }
        ## Check Coupon Code Usage Limit , Send Email to Admin if Usage  Limit is over ##
    }
    //added by SP end
    if ($vLangCode == "" || $vLangCode == NULL) {
        //Added By HJ On 15-07-2020 For Optimize language_master Table Query Start
        $vLangCode = $LANG_OBJ->FetchDefaultLangData("vCode");
        //Added By HJ On 15-07-2020 For Optimize language_master Table Query End
    }
    $sql = "select vTitle_" . $vLangCode . " as vTitle FROM cancel_reason where iCancelReasonId = '" . $iReasonId . "'";
    $db_sql = $obj->MySQLSelect($sql);
    $vTitle = $db_sql[0]['vTitle'];
    // $vTitle = get_value('cancel_reason', 'vTitle_'.$vLangCode.' as vTitle', 'iCancelReasonId', $iReasonId,'','true');
    $vTitleReasonMessage = ($vCancelReason != "") ? $vCancelReason : $vTitle;
    $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLangCode, "1", $iServiceId);
    $alertMsg = $languageLabelsArr['LBL_DECLINE_ORDER_APP_TXT'] . " #" . $vOrderNo . " " . $languageLabelsArr['LBL_REASON_TXT'] . " " . $vTitleReasonMessage;
    $message_arr = array();
    $message_arr['Message'] = $Message;
    $message_arr['iOrderId'] = $iOrderId;
    $message_arr['vOrderNo'] = $vOrderNo;
    $message_arr['vTitle'] = $alertMsg;
    $message_arr['tSessionId'] = $data_order[0]['tSessionId'];
    $message_arr['eSystem'] = "DeliverAll";
    //added by SP on 02-02-2021 for custom notification
    $message_arr['CustomNotification'] = $MODULES_OBJ->isEnableCustomNotification() ? "Yes" : "No";
    //these two btn CustomViewBtn,CustomTrackDetails whether shown in app or not
    $message_arr['CustomViewBtn'] = "Yes";
    $message_arr['CustomTrackDetails'] = "No";
    $message_arr['LBL_VIEW_DETAILS'] = $languageLabelsArr['LBL_VIEW_DETAILS'];
    $message_arr['LBL_TRACK_ORDER'] = $languageLabelsArr['LBL_TRACK_ORDER'];
    $customNotiArray = GetCustomNotificationDetails($iOrderId, $message_arr, $vLangCode);
    //title and sub description shown in custom notification
    $message_arr['CustomTitle'] = $customNotiArray[0]['vCurrentStatus'];
    $message_arr['CustomSubTitle'] = $customNotiArray[0]['vCurrentStatus_Track'];
    $message_arr['CustomMessage'] = $customNotiArray;
    $iAppVersion = $data_order[0]['iAppVersion'];
    $eDeviceType = $data_order[0]['eDeviceType'];
    $iGcmRegId = $data_order[0]['iGcmRegId'];
    $tSessionId = $data_order[0]['tSessionId'];
    $eAppTerminate = $data_order[0]['eAppTerminate'];
    $eDebugMode = $data_order[0]['eDebugMode'];
    $eHmsDevice = $data_order[0]['eHmsDevice'];
    $channelName = "PASSENGER_" . $iUserId;
    $generalDataArr[] = array(
        'eDeviceType' => $eDeviceType, 'deviceToken' => $iGcmRegId, 'alertMsg' => $alertMsg, 'eAppTerminate' => $eAppTerminate, 'eDebugMode' => $eDebugMode, 'eHmsDevice' => $eHmsDevice, 'message' => $message_arr, 'channelName' => $channelName, 'orderEventChannelName' => $orderEventChannelName
    );

    if(strtoupper($eDeviceType) == "IOS") {
        $tDeviceLiveActivityToken = getLiveActivityDeviceToken($iOrderId, 'Order');
    } else {
        $tDeviceLiveActivityToken = $iGcmRegId;
    } 

    if(strtoupper($ENABLE_NOTIFICATION_LIVE_ACTIVITY) == "YES") {
        $message_arr['LiveActivityData'] = getOrderLiveActivity($iOrderId);
        $message_arr['LiveActivity'] = "Yes";
        $message_arr['LiveActivityEnd'] = "Yes";
    
        $generalDataArr[] = array(
            'eDeviceType' => $eDeviceType, 'deviceToken' => $tDeviceLiveActivityToken, 'alertMsg' => $alertMsg, 'eAppTerminate' => $eAppTerminate, 'eDebugMode' => $eDebugMode, 'eHmsDevice' => $eHmsDevice, 'message' => $message_arr
        );
    }


    $EVENT_MSG_OBJ->send(array('GENERAL_DATA' => $generalDataArr), RN_USER);
    if ($Order_Update_Id > 0) {
        if ($UserType == "Company") {
            $sql_cmp = "select vCompany from company where iCompanyId = '" . $iMemberId . "'"; //added by SP for mailissues wrong query
            $data_cmp = $obj->MySQLSelect($sql_cmp);
            $cmpname = $data_cmp[0]['vCompany'];
            $decline_arr['UserName'] = $vUserName;
            $decline_arr['CompanyName'] = $cmpname;
            $decline_arr['vOrderNo'] = $vOrderNo;
            $decline_arr['MSG'] = $languageLabelsArr['LBL_REASON_TXT'] . " " . $vTitleReasonMessage; //added by SP for mailissues wrong reason
            $decline_arr_user['vEmail'] = $vEmail;
            $decline_arr_user['UserName'] = $vUserName;
            $decline_arr_user['CompanyName'] = $cmpname;
            $decline_arr_user['vOrderNo'] = $vOrderNo;
            $decline_arr_user['MSG'] = $languageLabelsArr['LBL_REASON_TXT'] . " " . $vTitleReasonMessage; //added by SP for mailissues wrong reason
            $COMM_MEDIA_OBJ->SendMailToMember("COMPANY_DECLINE_ORDER_TO_USER", $decline_arr_user);
            $COMM_MEDIA_OBJ->SendMailToMember("COMPANY_DECLINE_ORDER", $decline_arr);
        }
        $returnArr['Action'] = "1";
        $returnArr['message'] = "LBL_ORDER_DECLINE_BY_RESTAURANT";
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_NO_ORDERS_FOUND_TXT";
    }
    setDataResponse($returnArr);
}
// ######################## End Order Decline ######################################################################
// ######################## Confirm Order By Restaurant ############################################################
if ($type == "ConfirmOrderByRestaurant") {
    $iCompanyId = isset($_REQUEST["iCompanyId"]) ? $_REQUEST["iCompanyId"] : "";
    $iOrderId = isset($_REQUEST["iOrderId"]) ? $_REQUEST["iOrderId"] : "";
    $ePickedUp = isset($_REQUEST["ePickedUp"]) ? $_REQUEST["ePickedUp"] : "No";
    $_REQUEST['eSystem'] = "DeliverAll";
    if (!empty($iOrderId)) {
        $sql = "select ru.iUserId, ord.iStatusCode, ord.vOrderNo from orders as ord LEFT JOIN register_user as ru ON ord.iUserId=ru.iUserId where ord.iOrderId = '" . $iOrderId . "' AND ord.iStatusCode='8'";
        $data_order = $obj->MySQLSelect($sql);
        if (count($data_order) > 0) {
            $returnArr['Action'] = "1";
            $returnArr['message'] = "LBL_CANCEL_ORDER_ADMIN_TXT";
            $returnArr['DO_RESTART'] = "Yes";
            setDataResponse($returnArr);
        }
        if ($ePickedUp == "Yes") {
            $data_order = $obj->MySQLSelect("select iStatusCode from orders where iOrderId = '" . $iOrderId . "' and iStatusCode!='2' and eTakeaway='Yes'");
            if (count($data_order) > 0) {
                $returnArr['Action'] = "0";
                $returnArr['message'] = "LBL_SOMETHING_WENT_WRONG_TRY_AGAIN";
                $returnArr['DO_RESTART'] = "Yes";
                setDataResponse($returnArr);
            }
        }
        else {
            $data_order = $obj->MySQLSelect("select iStatusCode from orders where iOrderId = '" . $iOrderId . "' and iStatusCode!='1'");
            if (count($data_order) > 0) {
                $returnArr['Action'] = "0";
                $returnArr['message'] = "LBL_SOMETHING_WENT_WRONG_TRY_AGAIN";
                $returnArr['DO_RESTART'] = "Yes";
                setDataResponse($returnArr);
            }
        }
    }
    $Data_update_order = array();
    $where = " iOrderId = '" . $iOrderId . "'";
    if ($ePickedUp == "Yes") {
        $Data_update_order['iStatusCode'] = '6';
    }
    else {
        $Data_update_order['iStatusCode'] = '2';
    }
    if ($ePickedUp == "Yes") {
        $Data_update_order['ePaid'] = "Yes";
        $Data_update_order['dDeliveryDate'] = @date("Y-m-d H:i:s");
        $Data_update_order['iStatusCode'] = '6';
        $Order_Status_id = createOrderLog($iOrderId, "6");
    }
    $Order_Update_Id = $obj->MySQLQueryPerform("orders", $Data_update_order, 'update', $where);
    if ($ePickedUp == "No") {
        $id = createOrderLog($iOrderId, "2");
    }
    // # Send Notification To User ##
    if ($ePickedUp == "Yes") {
        $COMM_MEDIA_OBJ->orderemaildataDelivered($iOrderId, "Passenger"); //added by HV on 28-03-2020 to send email when user picks up (For takeaway)
        $REFERRAL_OBJ->CreditReferralAmountTakeAway($iOrderId); //added by HV on 07-05-2020 to send email when takeaway order is completed
    }
    $sql = "select ru.iUserId,ru.iGcmRegId,ru.eDeviceType,ru.tSessionId,ru.iAppVersion,ru.vLang,ru.eDebugMode,ord.vOrderNo,ord.eTakeaway,ord.iCompanyId,ord.eBuyAnyService,ord.iStatusCode,ru.eAppTerminate,ru.eHmsDevice,ord.vCouponCode from orders as ord LEFT JOIN register_user as ru ON ord.iUserId=ru.iUserId where ord.iOrderId = '" . $iOrderId . "'";
    $data_order = $obj->MySQLSelect($sql);
    $vLangCode = $data_order[0]['vLang'];
    $vOrderNo = $data_order[0]['vOrderNo'];
    $iUserId = $data_order[0]['iUserId'];
    $vCouponCode = $data_order[0]['vCouponCode'];
    if ($vLangCode == "" || $vLangCode == NULL) {
        //Added By HJ On 15-07-2020 For Optimize language_master Table Query Start
        $vLangCode = $LANG_OBJ->FetchDefaultLangData("vCode");
        //Added By HJ On 15-07-2020 For Optimize language_master Table Query End
    }
    $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLangCode, "1", $iServiceId);
    $db_companyname = $obj->MySQLSelect("select vCompany from company where iCompanyId = " . $data_order[0]['iCompanyId']);
    if ($ePickedUp == "Yes") {
        $Message = "OrderDelivered";
        $alertMsg = str_replace('#RESTAURANT_NAME#', $db_companyname[0]['vCompany'], $languageLabelsArr['LBL_TAKE_AWAY_ORDER_NOTE']);
        // # Update Coupon Used Limit ##
        if ($vCouponCode != '') {
            $noOfCouponUsed = get_value('coupon', 'iUsed', 'vCouponCode', $vCouponCode, '', 'true');
            $where_coupon = " vCouponCode = '" . $vCouponCode . "'";
            $data_coupon['iUsed'] = $noOfCouponUsed + 1;
            $obj->MySQLQueryPerform("coupon", $data_coupon, 'update', $where_coupon);
            ## Check Coupon Code Usage Limit , Send Email to Admin if Usage  Limit is over ##
            $UpdatedCouponUsedNo = $noOfCouponUsed + 1;
            $sql = "SELECT iUsed, iUsageLimit from coupon WHERE vCouponCode = '" . $vCouponCode . "'";
            $coupon_result = $obj->MySQLSelect($sql);
            if ($iUsageLimit == $UpdatedCouponUsedNo) {
                $maildata['vCouponCode'] = $vCouponCode;
                $maildata['iUsageLimit'] = $iUsageLimit;
                $maildata['COMPANY_NAME'] = $COMPANY_NAME;
                $mail = $COMM_MEDIA_OBJ->SendMailToMember('COUPON_LIMIT_COMPLETED_TO_ADMIN', $maildata);
            }
            ## Check Coupon Code Usage Limit , Send Email to Admin if Usage  Limit is over ##
        }
        // # Update Coupon Used Limit ##
    }
    else {
        $Message = "OrderConfirmByRestaurant";
        $alertMsg = str_replace('#STORE_TITLE#', $languageLabelsArr['LBL_RESTAURANT_TXT'] . " (" . $db_companyname[0]['vCompany'] . ")", $languageLabelsArr['LBL_STORE_CONFIRM_ORDER']);
        $alertMsg = rtrim($alertMsg, ".");
        $alertMsg .= " #" . $vOrderNo . ".";
    }
    $message_arr = array();
    $message_arr['Message'] = $Message;
    $message_arr['iOrderId'] = $iOrderId;
    $message_arr['vOrderNo'] = $vOrderNo;
    $message_arr['vTitle'] = $alertMsg;
    $message_arr['tSessionId'] = $data_order[0]['tSessionId'];
    $message_arr['eTakeaway'] = $data_order[0]['eTakeaway'];
    $message_arr['iStatusCode'] = $data_order[0]['iStatusCode'];
    $message_arr['eSystem'] = "DeliverAll";
    $message_arr['iUserId'] = $iUserId;
    //added by SP on 02-02-2021 for custom notification
    $message_arr['CustomNotification'] = $MODULES_OBJ->isEnableCustomNotification() ? "Yes" : "No";
    //these two btn CustomViewBtn,CustomTrackDetails whether shown in app or not
    if ($ePickedUp == "Yes") {
        $message_arr['CustomTrackDetails'] = "No";
    }
    else {
        $message_arr['CustomTrackDetails'] = "Yes";
    }
    $message_arr['CustomViewBtn'] = "Yes";
    $message_arr['LBL_VIEW_DETAILS'] = $languageLabelsArr['LBL_VIEW_DETAILS'];
    $message_arr['LBL_TRACK_ORDER'] = $languageLabelsArr['LBL_TRACK_ORDER'];
    $customNotiArray = GetCustomNotificationDetails($iOrderId, $message_arr, $vLangCode);
    //title and sub description shown in custom notification
    $message_arr['CustomTitle'] = $customNotiArray[0]['vCurrentStatus'];
    $message_arr['CustomSubTitle'] = $customNotiArray[0]['vCurrentStatus_Track'];
    $message_arr['CustomMessage'] = $customNotiArray;
    $iAppVersion = $data_order[0]['iAppVersion'];
    $eDeviceType = $data_order[0]['eDeviceType'];
    $iGcmRegId = $data_order[0]['iGcmRegId'];
    $tSessionId = $data_order[0]['tSessionId'];
    $eAppTerminate = $data_order[0]['eAppTerminate'];
    $eDebugMode = $data_order[0]['eDebugMode'];
    $eHmsDevice = $data_order[0]['eHmsDevice'];
    $channelName = "PASSENGER_" . $iUserId;
    $generalDataArr[] = array(
        'eDeviceType' => $eDeviceType, 'deviceToken' => $iGcmRegId, 'alertMsg' => $alertMsg, 'eAppTerminate' => $eAppTerminate, 'eDebugMode' => $eDebugMode, 'eHmsDevice' => $eHmsDevice, 'message' => $message_arr, 'channelName' => $channelName, 'orderEventChannelName' => $orderEventChannelName
    );

    if(strtoupper($eDeviceType) == "IOS") {
        $tDeviceLiveActivityToken = getLiveActivityDeviceToken($iOrderId, 'Order');
    } else {
        $tDeviceLiveActivityToken = $iGcmRegId;
    }

    if(strtoupper($ENABLE_NOTIFICATION_LIVE_ACTIVITY) == "YES") {
        $message_arr['LiveActivityData'] = getOrderLiveActivity($iOrderId);
        $message_arr['LiveActivity'] = "Yes";

        $generalDataArr[] = array(
            'eDeviceType' => $eDeviceType, 'deviceToken' => $tDeviceLiveActivityToken, 'alertMsg' => $alertMsg, 'eAppTerminate' => $eAppTerminate, 'eDebugMode' => $eDebugMode, 'eHmsDevice' => $eHmsDevice, 'message' => $message_arr
        );
    }

    $EVENT_MSG_OBJ->send(array('GENERAL_DATA' => $generalDataArr), RN_USER);
    if ($Order_Update_Id > 0) {
        $returnArr['Action'] = "1";
        if ($ePickedUp == "Yes") {
            $returnArr['message'] = "LBL_PICKUP"; //label remain to put
        }
        else {
            $returnArr['message'] = "LBL_CONFIRM_ORDER_BY_RESTAURANT";
        }
        $COMM_MEDIA_OBJ->orderemaildata($iOrderId, 'Passenger');
        $returnArr['USER_DATA'] = getCompanyDetailInfo($iCompanyId);
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_NO_ORDERS_FOUND_TXT";
    }
    setDataResponse($returnArr);
}
// ######################## End Accept Order By Restaurant #############################################
// Driver app Types
// ######################## Get Live Task Details #####################################################
if ($type == "GetLiveTaskDetailDriver") {
    $iOrderId = isset($_REQUEST["iOrderId"]) ? $_REQUEST["iOrderId"] : "";
    $iDriverId = isset($_REQUEST["GeneralMemberId"]) ? $_REQUEST["GeneralMemberId"] : "";
    $vLangCode = isset($_REQUEST["vGeneralLang"]) ? $_REQUEST["vGeneralLang"] : "";
    $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLangCode, "1", "");
    $returnArrDataNew = array();
    $orderIds = $obj->MySQLSelect("SELECT GROUP_CONCAT(iOrderId) as order_ids FROM trips WHERE (iActive = 'Active' OR iActive = 'On Going Trip') AND iDriverId = '$iDriverId' AND eSystem = 'DeliverAll'");
    $allOrders = $obj->MySQLSelect("SELECT iOrderId, iUserId, iDriverId, iCompanyId, vOrderNo, iUserAddressId, iStatusCode, ePaid, ePaymentOption, eBuyAnyService, genieUserApproved, eForPickDropGenie FROM orders where iOrderId IN (" . $orderIds[0]['order_ids'] . ") ORDER BY iOrderId");
    $count = 0;
    $pickupArr = $dropoffArr = array();
    $statusSet = "No";
    foreach ($allOrders as $OrderData) {
        $query = "SELECT iTripId,vImage,eImgSkip,iVehicleTypeId FROM `trips` WHERE iOrderId = '" . $OrderData['iOrderId'] . "' ORDER BY iTripId DESC";
        $TripsData = $obj->MySQLSelect($query);
        $Vehiclefields = "iVehicleTypeId,vVehicleType";
        $VehicleTypeDataDriver = get_value('vehicle_type', $Vehiclefields, 'iVehicleTypeId', $TripsData[0]['iVehicleTypeId']);
        $SelectdVehicleTypeId = ($VehicleTypeDataDriver[0]['iVehicleTypeId'] != '') ? $VehicleTypeDataDriver[0]['iVehicleTypeId'] : "";
        $SelectdVehicleType = ($VehicleTypeDataDriver[0]['vVehicleType'] != '') ? $VehicleTypeDataDriver[0]['vVehicleType'] : "";
        $liveTaskDataPickup = array();
        $liveTaskDataPickup['iOrderId'] = $OrderData['iOrderId'];
        $liveTaskDataPickup['iVehicleTypeId'] = $SelectdVehicleTypeId;
        $liveTaskDataPickup['vVehicleType'] = $SelectdVehicleType;
        $liveTaskDataPickup['iTripId'] = $TripsData[0]['iTripId'];
        $iUserId = $OrderData['iUserId'];
        $iUserAddressId = $OrderData['iUserAddressId'];
        $iCompanyId = $OrderData['iCompanyId'];
        $isPhotoUploaded = 'No';
        if (!empty($TripsData)) {
            if ($OrderData['iStatusCode'] == '5' && $TripsData[0]['eImgSkip'] == 'None') {
                $isPhotoUploaded = 'No';
            }
            else if ($OrderData['iStatusCode'] == '5' && $TripsData[0]['eImgSkip'] == 'No') {
                $isPhotoUploaded = 'Yes';
            }
            else if ($OrderData['iStatusCode'] == '5' && $TripsData[0]['eImgSkip'] == 'Yes') {
                $isPhotoUploaded = 'Yes';
            }
        }
        if ($OrderData['iStatusCode'] == '5' && $OrderData['eBuyAnyService'] == 'Yes') {
            $isPhotoUploaded = 'Yes';
        }
        $liveTaskDataPickup['isPhotoUploaded'] = $isPhotoUploaded;
        $cquery = "SELECT iCompanyId,vImage,vCompany,vCaddress AS vRestuarantLocation,vRestuarantLocationLat,vRestuarantLocationLong,vPhone,vCode,eBuyAnyService FROM company WHERE iCompanyId = '" . $iCompanyId . "'"; // Get vCaddress As Store Location as per Discuss With NM On 26-10-2019
        $CompanyData = $obj->MySQLSelect($cquery);
        if (!empty($CompanyData)) {
            if ($OrderData['iStatusCode'] == '5') {
                $liveTaskDataPickup['PickedFromRes'] = 'Yes';
            }
            else {
                $liveTaskDataPickup['PickedFromRes'] = 'No';
            }
            $liveTaskDataPickup['iCompanyId'] = $iCompanyId;
            $liveTaskDataPickup['vRestuarantImage'] = ($CompanyData[0]['vImage'] != '') ? $CompanyData[0]['vImage'] : "";
            $liveTaskDataPickup['vOrderNo'] = $OrderData['vOrderNo'];
            $liveTaskDataPickup['eBuyAnyService'] = $OrderData['eBuyAnyService'];
            $liveTaskDataPickup['vCompany'] = ($CompanyData[0]['vCompany'] != '') ? $CompanyData[0]['vCompany'] : "";
            $liveTaskDataPickup['vRestuarantLocation'] = ($CompanyData[0]['vRestuarantLocation'] != '') ? $CompanyData[0]['vRestuarantLocation'] : "";
            $liveTaskDataPickup['vRestuarantLocationLat'] = ($CompanyData[0]['vRestuarantLocationLat'] != '') ? $CompanyData[0]['vRestuarantLocationLat'] : "";
            $liveTaskDataPickup['vRestuarantLocationLong'] = ($CompanyData[0]['vRestuarantLocationLong'] != '') ? $CompanyData[0]['vRestuarantLocationLong'] : "";
            if ($CompanyData[0]['vCode'] != '') {
                $liveTaskDataPickup['vPhoneRestaurant'] = ($CompanyData[0]['vPhone'] != "") ? '+' . $CompanyData[0]['vCode'] . $CompanyData[0]['vPhone'] : "";
            }
            else {
                $liveTaskDataPickup['vPhoneRestaurant'] = ($CompanyData[0]['vPhone'] != "") ? $CompanyData[0]['vPhone'] : "";
            }
        }
        $uQuery = "SELECT concat(ru.vName,' ',ru.vLastName) as UserName,ru.vPhone,ru.vPhoneCode,ua.vLatitude,ua.vLongitude,ru.vImgName,ru.vName,ru.vLastName FROM register_user as ru LEFT JOIN user_address as ua on ua.iUserId = ru.iUserId WHERE ru.iUserId = '" . $iUserId . "' AND ua.iUserAddressId = '" . $iUserAddressId . "'  AND ua.eUserType = 'Rider'";
        $UserData = $obj->MySQLSelect($uQuery);
        //Added By HJ On 09-01-2020 For Solved 141 Mantis Bug #2799 Start
        if (count($UserData) == 0 || empty($UserData)) {
            $uQuery = "SELECT concat(ru.vName,' ',ru.vLastName) as UserName,ru.vPhone,ru.vPhoneCode,ua.vLatitude,ua.vLongitude,ru.vImgName,ru.vName,ru.vLastName FROM register_user as ru LEFT JOIN user_fave_address as ua on ua.iUserId = ru.iUserId WHERE ru.iUserId = '" . $iUserId . "' AND ua.iUserFavAddressId = '" . $iUserAddressId . "'  AND ua.eUserType = 'Passenger'";
            $UserData = $obj->MySQLSelect($uQuery);
        }
        //Added By HJ On 09-01-2020 For Solved 141 Mantis Bug #2799 End
        if (!empty($UserData)) {
            $liveTaskDataPickup['UserName'] = $UserData[0]['UserName'];
            $liveTaskDataPickup['vName'] = $UserData[0]['vName'];
            $liveTaskDataPickup['vLastName'] = $UserData[0]['vLastName'];
            $liveTaskDataPickup['passengerId'] = $iUserId;
            $liveTaskDataPickup['PPicName'] = "";
            if (!empty($UserData[0]['vImgName']) && file_exists($tconfig['tsite_upload_images_passenger'] . "/" . $iUserId . "/" . $UserData[0]['vImgName'])) {
                $liveTaskDataPickup['PPicName'] = $tconfig['tsite_upload_images_passenger'] . "/" . $iUserId . "/" . $UserData[0]['vImgName'];
            }
            $UserAddressArr = FetchMemberAddressData($iUserId, "Passenger", $iUserAddressId);
            $liveTaskDataPickup['UserAdress'] = $UserAddressArr['UserAddress'];
            $liveTaskDataPickup['vLatitude'] = $UserData[0]['vLatitude'];
            $liveTaskDataPickup['vLongitude'] = $UserData[0]['vLongitude'];
            if ($UserData[0]['vPhone'] != '') {
                $liveTaskDataPickup['vPhoneUser'] = '+' . $UserData[0]['vPhoneCode'] . $UserData[0]['vPhone'];
            }
            else {
                $liveTaskDataPickup['vPhoneUser'] = $UserData[0]['vPhone'];
            }
        }
        else {
            $liveTaskDataPickup['UserName'] = $liveTaskDataPickup['UserAdress'] = '';
        }
        if ($OrderData['eBuyAnyService'] == "Yes") {
            $liveTaskDataPickup['GenieOrderType'] = "Anywhere";
            if ($OrderData['eForPickDropGenie'] == "Yes") {
                $liveTaskDataPickup['GenieOrderType'] = "Runner";
            }
        }
        $liveTaskDataPickup['isForPickup'] = "Yes";
        $liveTaskDataPickup['isForDropoff'] = "No";
        $liveTaskDataPickup['isCurrentTask'] = "Yes";
        $liveTaskDataPickup['liveTaskStatus'] = "";
        if ($count > 1) {
            $liveTaskDataPickup['isCurrentTask'] = "No";
        }
        $liveTaskDataDropoff = array();
        $liveTaskDataDropoff = $liveTaskDataPickup;
        $liveTaskDataDropoff['isForPickup'] = "No";
        $liveTaskDataDropoff['isForDropoff'] = "Yes";
        $liveTaskDataDropoff['isCurrentTask'] = "No";
        if ($statusSet == "Yes") {
            $liveTaskDataPickup['liveTaskStatus'] = $liveTaskDataDropoff['liveTaskStatus'] = "";
        }
        $statusSet = "Yes";
        if (!($liveTaskDataPickup['isPhotoUploaded'] == "Yes" && $liveTaskDataPickup['PickedFromRes'] == "Yes")) {
            $pickupArr[] = $liveTaskDataPickup;
        }
        $dropoffArr[] = $liveTaskDataDropoff;
        $count += 2;
    }
    $allLiveTaskData = array_merge($pickupArr, $dropoffArr);
    if (!empty($allLiveTaskData)) {
        $allLiveTaskData[0]['isCurrentTask'] = "Yes";
        $allLiveTaskData[0]['liveTaskStatus'] = $languageLabelsArr['LBL_CURRENT_TASK_TXT'];
        $tmpArr = array_slice($allLiveTaskData, -(count($allLiveTaskData) - 1));
        if (count($tmpArr) > 1) {
            $allLiveTaskData[1]['liveTaskStatus'] = $languageLabelsArr['LBL_NEXT_TASKS_TXT'];
        }
        elseif (isset($allLiveTaskData[1])) {
            $allLiveTaskData[1]['liveTaskStatus'] = $languageLabelsArr['LBL_NEXT_TASK_TXT'];
        }
    }
    $returnArrDataNew = $allLiveTaskData;
    if (!empty($returnArrDataNew)) {
        $returnArr['Action'] = "1";
        $returnArr['message'] = $returnArrDataNew;
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_NO_ORDERS_FOUND_TXT";
    }
    setDataResponse($returnArr);
}
// ######################## End Get Live Task Details ###################################################################
// ############################ Check Out Order Details ###################################################################
if ($type == "CheckOutOrderDetails") {
    $iUserId = isset($_REQUEST["iUserId"]) ? $_REQUEST["iUserId"] : '';
    $iCompanyId = isset($_REQUEST["iCompanyId"]) ? $_REQUEST["iCompanyId"] : '';
    $iUserAddressId = isset($_REQUEST["iUserAddressId"]) ? $_REQUEST["iUserAddressId"] : '';
    $vCouponCode = isset($_REQUEST["vCouponCode"]) ? $_REQUEST["vCouponCode"] : '';
    $ePaymentOption = isset($_REQUEST["ePaymentOption"]) ? $_REQUEST["ePaymentOption"] : '';
    $vDeviceToken = isset($_REQUEST["vDeviceToken"]) ? $_REQUEST["vDeviceToken"] : '';
    $OrderDetails = isset($_REQUEST["OrderDetails"]) ? $_REQUEST["OrderDetails"] : '';
    $vInstruction = isset($_REQUEST["vInstruction"]) ? $_REQUEST["vInstruction"] : '';
    $CheckUserWallet = isset($_REQUEST["CheckUserWallet"]) ? $_REQUEST["CheckUserWallet"] : 'No';
    $selectedpreferences = isset($_REQUEST["selectedprefrences"]) ? $_REQUEST["selectedprefrences"] : '';
    $eTakeAway = isset($_REQUEST["eTakeAway"]) ? $_REQUEST["eTakeAway"] : 'No';
    $tipAmount = isset($_REQUEST["fTipAmount"]) ? $_REQUEST["fTipAmount"] : 0;
    $selectedTipPos = isset($_REQUEST["selectedTipPos"]) ? $_REQUEST["selectedTipPos"] : 0;
    $eCatType = isset($_REQUEST["eCatType"]) ? $_REQUEST["eCatType"] : "";
    $iIdProofImageId = isset($_REQUEST["iIdProofImageId"]) ? $_REQUEST["iIdProofImageId"] : "";
    $eForPickDropGenie = isset($_REQUEST["IS_FOR_PICK_DROP_GENIE"]) ? $_REQUEST["IS_FOR_PICK_DROP_GENIE"] : "No";
    $isCommunicationError = isset($_REQUEST["isCommunicationError"]) ? $_REQUEST["isCommunicationError"] : "No";
    $iVoiceDirectionFileId = isset($_REQUEST["iVoiceDirectionFileId"]) ? $_REQUEST["iVoiceDirectionFileId"] : "";
    $isStoreKiosk = isset($_REQUEST["isStoreKiosk"]) ? $_REQUEST["isStoreKiosk"] : 'No';
    $UserDetails = isset($_REQUEST["UserDetails"]) ? $_REQUEST["UserDetails"] : '';
    $iServiceId = isset($_REQUEST["iServiceId"]) ? $_REQUEST["iServiceId"] : '';
    $iPaymentInfoId = isset($_REQUEST["iPaymentInfoId"]) ? $_REQUEST["iPaymentInfoId"] : '';
    $iOrderId = isset($_REQUEST["iOrderId"]) ? $_REQUEST["iOrderId"] : '';
    $ORDER_FROM_WEB = isset($_REQUEST["ORDER_FROM_WEB"]) ? $_REQUEST["ORDER_FROM_WEB"] : 'No';
    $userLanguageCode = isset($_REQUEST["vGeneralLang"]) ? $_REQUEST["vGeneralLang"] : '';
    $isAddOutstandingAmt = isset($_REQUEST["isAddOutstandingAmt"]) ? $_REQUEST["isAddOutstandingAmt"] : 'No';
    $eSystemAppType = isset($_REQUEST["eSystemAppType"]) ? $_REQUEST["eSystemAppType"] : '';
    $eWalletIgnore = isset($_REQUEST["eWalletIgnore"]) ? $_REQUEST["eWalletIgnore"] : 'No';
    $ePayWallet = isset($_REQUEST["ePayWallet"]) ? $_REQUEST["ePayWallet"] : 'No';
    $adminSkip = isset($_REQUEST["adminSkip"]) ? $_REQUEST["adminSkip"] : 'No';
    
    if (empty($userLanguageCode)) {
        $userLanguageCode = $LANG_OBJ->FetchDefaultLangData("vCode");
    }
    if ($isCommunicationError == "Yes") {
        $getLastOrder = $obj->MySQLSelect("SELECT iOrderId,tOrderRequestDate,CURRENT_TIMESTAMP() FROM orders WHERE iUserId = $iUserId AND tOrderRequestDate >= DATE_SUB(NOW(),INTERVAL 3 MINUTE) AND iStatusCode NOT IN (7,8,11) ORDER BY iOrderId DESC LIMIT 1");
        $iOrderId = $getLastOrder[0]['iOrderId'];
        $returnArr['Action'] = "1";
        $returnArr['iOrderId'] = $iOrderId;
        $returnArr['isCommunicationError'] = $isCommunicationError;
        $returnArr['message'] = getPassengerDetailInfo($iUserId, "", "");
        setDataResponse($returnArr);
    }
    // payment method-2
    $OrderDetails = json_decode(stripcslashes($OrderDetails), true);

    $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($userLanguageCode, "1", $iServiceId);

    if((isset($OrderDetails) && count($OrderDetails) == 0) || !isset($OrderDetails) || empty($OrderDetails)){
        $returnArr['Action'] = "0";
        $returnArr['message'] = $languageLabelsArr["LBL_NO_ITEM_ADDED_ERROR_MSG"];
        setDataResponse($returnArr);
    }

    //Added By HJ On 19-03-2020 For Get User Order Details Start - (For Solved SGO Bug)
    if (isset($_REQUEST['fromOrder']) && trim($_REQUEST['fromOrder']) != "") {
        $fromOrder = trim($_REQUEST['fromOrder']);
        $orderDetailsSession = "ORDER_DETAILS_" . strtoupper($fromOrder);
        $OrderDetails = $_SESSION[$orderDetailsSession];
    }
    //Added By HJ On 19-03-2020 For Get User Order Details End - (For Solved SGO Bug)
    
    if (empty($fromOrder)) {
        /* For New Payment Flow */
        $isContactlessDelivery = "No";
        if ($MODULES_OBJ->isDeliveryPreferenceEnable() && !empty($selectedpreferences)) {
            $deliveryPrefSqlData = $obj->MySQLSelect("SELECT eContactLess FROM delivery_preferences WHERE iPreferenceId IN (" . $selectedpreferences . ")");
            $returnArr['isContactLessDeliverySelected'] = 'No';
            foreach ($deliveryPrefSqlData as $value) {
                if ($value['eContactLess'] == 'Yes') {
                    $isContactlessDelivery = 'Yes';
                }
            }
        }

        $params = array(
            "iMemberId" => $iUserId, "eUserType" => "Passenger", "eType" => "DeliverAll", "GET_DATA" => "Yes"
        );


        $payment_mode_data = GetPaymentModeDetails($params);

        $ePaymentMode = !empty($payment_mode_data['PaymentMode']) ? $payment_mode_data['PaymentMode'] : "cash";

        $cashPayment = $ePaymentMode == "cash" ? "Yes" : "No";
        $ePayWallet = $ePaymentMode == "wallet" ? "Yes" : "No";
        $CheckUserWallet = $ePaymentMode == "wallet" ? "Yes" : ($payment_mode_data['eWalletDebit'] == "Yes" ? "Yes" : "No");
        $isRestrictToWallet = $payment_mode_data['PAYMENT_MODE_RESTRICT_TO_WALLET'];
        if($ePaymentMode == "wallet" && (strtoupper($isContactlessDelivery) == "YES" || strtoupper($eTakeAway) == "YES")) {
            $isRestrictToWallet = "Yes";
        }

        $ePaymentOption = mb_convert_case($ePaymentMode, MB_CASE_TITLE, 'UTF-8');
       // $ePaymentOption = ucfirst($ePaymentMode);
        $iPaymentInfoId = $payment_mode_data['iPaymentInfoId'];

        if($eSystemAppType == 'kiosk'){
            $ePaymentOption = $_REQUEST["ePaymentOption"];
        }
    }
    if (!empty($fromOrder)) {
        $ePaymentOption = $_REQUEST['ePaymentOption'];
    }



    /* For New Payment Flow End */
    // payment method-2
    if ($CheckUserWallet == "" || $CheckUserWallet == NULL) {
        $CheckUserWallet = "No";
    }
    if ($adminSkip != "Yes" && strtoupper($isStoreKiosk) == "NO") {
        isMemberEmailPhoneVerified($iUserId, "Passenger");
    }
    //Added By HJ On 18-07-2020 For Optimize register_user Table Query Start
    if (isset($userDetailsArr['register_user_' . $iUserId])) {
        $userData = $userDetailsArr['register_user_' . $iUserId];
    }
    else {
        $userData = $obj->MySQLSelect("SELECT *,iUserId as iMemberId FROM `register_user` WHERE iUserId='" . $iUserId . "'");
        $userDetailsArr['register_user_' . $iUserId] = $userData;
    }
    $iGcmRegId = $userData[0]['iGcmRegId'];
    $userLanguageCode = $userData[0]['vLang'];
    $currency_data = $obj->MySQLSelect("SELECT Ratio FROM currency WHERE vName = '" . $userData[0]['vCurrencyPassenger'] . "' ");
    $priceRatio = $currency_data[0]['Ratio'];
    $currencycode = $userData[0]['vCurrencyPassenger'];

    //Added By HJ On 18-07-2020 For Optimize company Table Query Start
    if (isset($userDetailsArr["company_" . $iCompanyId])) {
        $db_companydata = $userDetailsArr["company_" . $iCompanyId];
    }
    else {
        $db_companydata = $obj->MySQLSelect("SELECT *,iCompanyId AS iMemberId FROM company WHERE iCompanyId='" . $iCompanyId . "' ");
        $userDetailsArr["company_" . $iCompanyId] = $db_companydata;
    }
    //Added By HJ On 18-07-2020 For Optimize company Table Query End
    if (strtoupper($isStoreKiosk) == "YES") {
        $iGcmRegId = $db_companydata[0]['iGcmRegIdKiosk'];
        $userLanguageCode = $db_companydata[0]['vLang'];
        $UserDetails = json_decode(str_replace("\\", "", $UserDetails), true);
    }
    if (empty($userLanguageCode)) {
        $userLanguageCode = $LANG_OBJ->FetchDefaultLangData("vCode");
    }
    //Added By HJ On 18-07-2020 For Optimize register_user Table Query End
    if ($vDeviceToken != "" && $vDeviceToken != $iGcmRegId) {
        $returnArr['Action'] = "0";
        $returnArr['RESTRICT_APP'] = "Yes";
        $returnArr['isSessionExpired'] = "Yes";
        $returnArr['message'] = "LBL_SESSION_TIME_OUT";
        $returnArr['message_title'] = "LBL_SESSION_TIME_OUT_TITLE";
        setDataResponse($returnArr);
    }
    //Added By HJ On 21-07-2020 For Check User Restricted Area Start

    if ($iUserAddressId > 0 && !empty($iUserAddressId)) {
        $db_sql = $obj->MySQLSelect("SELECT vLatitude,vLongitude,vServiceAddress FROM user_address WHERE iUserAddressId = '" . $iUserAddressId . "'");

        if (count($db_sql) > 0) {
            $sourceLat = $db_sql[0]['vLatitude'];
            $sourceLon = $db_sql[0]['vLongitude'];
            $sourceLocationArr = array($sourceLat, $sourceLon);
            $allowed_ans = checkAreaRestriction($sourceLocationArr, "Yes");

            if (strtoupper($allowed_ans) == "NO") {
                $Data = array();
                $returnArr['Action'] = "0";
                $returnArr['message'] = "LBL_DELIVERY_LOCATION_NOT_ALLOW";
                setDataResponse($returnArr);
            }
        }
    } else {
        if($eTakeAway == 'No' && strtoupper($isStoreKiosk) == "NO") {
            $returnArr['Action'] = "0";
            $returnArr['message'] = $languageLabelsArr["LBL_SELECT_ADDRESS_TITLE_TXT"];
            setDataResponse($returnArr);
        }
    }

    //Added By HJ On 21-07-2020 For Check User Restricted Area End
    ## Update Demo User's Restaurants Lat Long As per User's Location ##
    if (SITE_TYPE == "Demo" && $iUserId != "" && $iUserAddressId != "") {
        $uemail = $userData[0]['vEmail'];
        $uemail = explode("-", $uemail);
        $uemail = $uemail[1];
        if ($uemail != "") {
            $db_rec = $obj->MySQLSelect("SELECT GROUP_CONCAT(iCompanyId)as companyId FROM company WHERE vEmail LIKE '%$uemail%'");
            $usercompanyId = $db_rec[0]['companyId'];
            $db_sql = $obj->MySQLSelect("SELECT vLatitude,vLongitude,vServiceAddress FROM user_address WHERE iUserAddressId = '" . $iUserAddressId . "'");
            $passengerLat = $db_sql[0]['vLatitude'];
            $passengerLon = $db_sql[0]['vLongitude'];
            $vServiceAddress = $db_sql[0]['vServiceAddress'];
            $db_demo_company = $obj->MySQLSelect("SELECT vRestuarantLocationLat, vRestuarantLocationLong FROM company WHERE iCompanyId = '$iCompanyId'");
            if (empty($db_demo_company[0]['vRestuarantLocationLat']) && empty($db_demo_company[0]['vRestuarantLocationLong'])) {
                $Data_update_company['vRestuarantLocationLat'] = $passengerLat;
                $Data_update_company['vRestuarantLocationLong'] = $passengerLon;
                $Data_update_company['vRestuarantLocation'] = $vServiceAddress;
                $where_company = " iCompanyId = '$iCompanyId'";
                $obj->MySQLQueryPerform("company", $Data_update_company, 'update', $where_company);
            }
            //$obj->sql_query("UPDATE company SET vRestuarantLocationLat='" . $passengerLat . "', vRestuarantLocationLong = '" . $passengerLon . "', vRestuarantLocation = '" . addslashes($vServiceAddress) . "' WHERE iCompanyId = '" . $iCompanyId . "'");
        }
    }
    ## Update Demo User's Restaurants Lat Long As per User's Location ##
    $eBuyAnyService = "No";
    /* Buy Any Service Feature - Added by HV on 31-08-2020 */
    if (in_array($eCatType, ['Genie', 'Runner', 'Anywhere'])) {
        $iStoreName = isset($_REQUEST["iStoreName"]) ? $_REQUEST["iStoreName"] : '';
        $iStorelatitude = isset($_REQUEST["iStorelatitude"]) ? $_REQUEST["iStorelatitude"] : '';
        $iStorelongitude = isset($_REQUEST["iStorelongitude"]) ? $_REQUEST["iStorelongitude"] : '';
        $iStoreAddress = isset($_REQUEST["iStoreAddress"]) ? $_REQUEST["iStoreAddress"] : '';
        $getCompany = $obj->MySQLSelect("SELECT c.iCompanyId,c.eBuyAnyService,fm.iFoodMenuId FROM company as c LEFT JOIN food_menu as fm ON fm.iCompanyId = c.iCompanyId WHERE vRestuarantLocationLat = '$iStorelatitude' AND vRestuarantLocationLong = '$iStorelongitude' AND c.eBuyAnyService = 'Yes'");
        $Data_Company_Insert['vCompany'] = $iStoreName;
        $Data_Company_Insert['vRestuarantLocationLat'] = $iStorelatitude;
        $Data_Company_Insert['vRestuarantLocationLong'] = $iStorelongitude;
        $Data_Company_Insert['vRestuarantLocation'] = $iStoreAddress;
        $Data_Company_Insert['eBuyAnyService'] = "Yes";
        $Data_Company_Insert['eAutoaccept'] = "Yes";
        $Data_Company_Insert['vCaddress'] = $iStoreAddress;
        $Data_Company_Insert['iServiceId'] = 1;
        $iCompanyId = $obj->MySQLQueryPerform("company", $Data_Company_Insert, 'insert');
        $eBuyAnyService = 'Yes';
        $Data_Item_Category['iCompanyId'] = $iCompanyId;
        $Data_Item_Category['vMenu_' . $userLanguageCode] = "Others";
        $Data_Item_Category['eBuyAnyService'] = "Yes";
        $iItemCategoryId = $obj->MySQLQueryPerform("food_menu", $Data_Item_Category, 'insert');
        $image_object = preg_replace('~/+~', '/', $tconfig['tpanel_path'] . 'webimages/icons/DefaultImg/ic_store.png');
        $image_name = 'ic_store.png';
        if ($eForPickDropGenie == "Yes") {
            $image_object = preg_replace('~/+~', '/', $tconfig['tpanel_path'] . 'webimages/icons/DefaultImg/ic_map_placeholder.png');
            $image_name = 'ic_map_placeholder.png';
        }
        $img_path = $tconfig["tsite_upload_images_compnay_path"];
        $temp_gallery = $img_path . '/';
        $Photo_Gallery_folder = $img_path . '/' . $iCompanyId . '/';
        if (!is_dir($Photo_Gallery_folder)) {
            mkdir($Photo_Gallery_folder, 0777);
            chmod($Photo_Gallery_folder, 0777);
        }
        $img1 = $UPLOAD_OBJ->GeneralUploadImage($image_object, $image_name, $Photo_Gallery_folder, '', '', '', '', '', '', 'Y', '', $Photo_Gallery_folder);
        if ($img1 != '') {
            if (is_file($Photo_Gallery_folder . $img1)) {
                include_once(TPATH_CLASS . "/SimpleImage.class.php");
                $img1 = $UPLOAD_OBJ->img_data_upload($Photo_Gallery_folder, $img1, $Photo_Gallery_folder, $tconfig["tsite_upload_images_member_size1"], $tconfig["tsite_upload_images_member_size2"], $tconfig["tsite_upload_images_member_size3"], "");
            }
        }
        $vImgName = $img1;
        $sql = "UPDATE company SET `vImage` = '" . $vImgName . "' WHERE `iCompanyId` = '" . $iCompanyId . "'";
        $obj->sql_query($sql);
        /* Insert Items */
        foreach ($OrderDetails as $okey => $Order_Detail) {
            $Data_Item_Insert['iFoodMenuId'] = $iItemCategoryId;
            $Data_Item_Insert['vItemType_' . $userLanguageCode] = $Order_Detail['itemName'];
            $Data_Item_Insert['vItemTypeBuyAnyService'] = $Order_Detail['itemName'];
            $Data_Item_Insert['eBuyAnyService'] = "Yes";
            $OrderMenuItemId = $obj->MySQLQueryPerform("menu_items", $Data_Item_Insert, 'insert');
            $OrderDetails[$okey]['iMenuItemId'] = $OrderMenuItemId;
            $OrderDetails[$okey]['iFoodMenuId'] = $iItemCategoryId;
        }
        /* Insert Items End */

        $db_companydata = $obj->MySQLSelect("SELECT *,iCompanyId AS iMemberId FROM company WHERE iCompanyId='" . $iCompanyId . "' ");
    }
    /* Buy Any Service Feature End - Added by HV on 31-08-2020 */
    $checkrestaurantstatusarr = GetStoreWorkingHoursDetails($iCompanyId, $iUserId);
    if (strtoupper($isStoreKiosk) == "YES") {
        $checkrestaurantstatusarr = GetStoreWorkingHoursDetails($iCompanyId, "", $userLanguageCode);
    }
    $restaurantstatus = $checkrestaurantstatusarr['restaurantstatus'];
    

    if($eBuyAnyService == "No") {
        if ($restaurantstatus == "closed") {
            $returnArr['Action'] = "0";
            $returnArr['message'] = "LBL_RESTAURANTS_CLOSE_NOTE";
            setDataResponse($returnArr);
        }

        $isAllItemAvailableCheckArr = isStoreItemAvailable($OrderDetails);
        $isAllItemAvailable = $isAllItemAvailableCheckArr['isAllItemAvailable'];
        $isAllItemOptionsAvailable = $isAllItemAvailableCheckArr['isAllItemOptionsAvailable'];
        $isAllItemToppingssAvailable = $isAllItemAvailableCheckArr['isAllItemToppingssAvailable'];
        //Added By HJ On 30-09-2020 For Check Item Availability When Checkout Order Start
        if (isset($_REQUEST['fromOrder'])) {
            $orderItemAvailabilityArr = isStoreItemAvailable($OrderDetails, 1); // Added By HJ On 29-09-2020 For Check Item,Option and Addon Available Or Not
            if (isset($orderItemAvailabilityArr['item'])) {
                $unAvailableItemIdArr = $orderItemAvailabilityArr['item'];
            }
            if (isset($orderItemAvailabilityArr['addon'])) {
                $unAvailableAddonIdArr = $orderItemAvailabilityArr['addon'];
            }
            if (isset($orderItemAvailabilityArr['option'])) {
                $unAvailableOptionIdArr = $orderItemAvailabilityArr['option'];
            }
            $unAvailableItemIds = $unAvailableAddonIds = $unAvailableOptionIds = array();
            foreach ($unAvailableItemIdArr as $itemid => $itemstatus) {
                if (strtoupper($itemstatus) == "NO") {
                    $unAvailableItemIds[] = $itemid;
                }
            }
            foreach ($unAvailableAddonIdArr as $addonid => $addontatus) {
                if (strtoupper($addontatus) == "NO") {
                    $unAvailableAddonIds[] = $addonid;
                }
            }
            foreach ($unAvailableOptionIdArr as $optionid => $optionstatus) {
                if (strtoupper($optionstatus) == "NO") {
                    $unAvailableOptionIds[] = $optionid;
                }
            }
        }
        if ($isAllItemAvailable == "No") {
            $returnArr['Action'] = "0";
            $returnArr['message'] = "LBL_MENU_ITEM_NOT_AVAILABLE_TXT";
            if (isset($_REQUEST['fromOrder'])) {
                $returnArr['itemIdArr'] = $unAvailableItemIds;
            }
            setDataResponse($returnArr);
        }
        if ($isAllItemOptionsAvailable == "No" && $eBuyAnyService == "No") {
            $returnArr['Action'] = "0";
            $returnArr['message'] = "LBL_MENU_ITEM_OPTIONS_NOT_AVAILABLE_TXT";
            if (isset($_REQUEST['fromOrder'])) {
                $returnArr['itemIdArr'] = $unAvailableOptionIds;
            }
            setDataResponse($returnArr);
        }
        if ($isAllItemToppingssAvailable == "No" && $eBuyAnyService == "No") {
            $returnArr['Action'] = "0";
            $returnArr['message'] = "LBL_MENU_ITEM_ADDONS_NOT_AVAILABLE_TXT";
            if (isset($_REQUEST['fromOrder'])) {
                $returnArr['itemIdArr'] = $unAvailableAddonIds;
            }
            setDataResponse($returnArr);
        }
    }
    
    $fOutStandingAmount = GetPassengerOutstandingAmount($iUserId);
    if (strtoupper($isStoreKiosk) == "YES") {
        $fOutStandingAmount = 0;
    }
    // $outStandingSql = "";
    // // if ($SYSTEM_PAYMENT_FLOW == 'Method-2' || $SYSTEM_PAYMENT_FLOW == 'Method-3') {
    // if (strtoupper($ePayWallet) == "YES") {
    //     $outStandingSql = " AND eAuthoriseIdName='No' AND iAuthoriseId ='0'";
    // }
    // //$orderOutstanding = $obj->MySQLSelect("SELECT count(iOrderId) as OrderCount, count(iTripId) as TripCount FROM trip_outstanding_amount WHERE iUserId='" . $iUserId . "' AND iUserId > 0 AND ePaidByPassenger = 'No' $outStandingSql");
    // $counttripData = $obj->MySQLSelect("SELECT count(iTripOutstandId) as counttrip FROM trip_outstanding_amount WHERE iUserId='" . $iUserId . "' AND iUserId > 0 AND ePaidByPassenger = 'No' $outStandingSql");
    // $returnArr['ShowContactUsBtn'] = "No";
    // if ($MODULES_OBJ->isEnableOutstandingRestriction()) {
    //     //if($fOutStandingAmount > 0 && $ePaymentOption == "Cash" && ($orderOutstanding[0]['OrderCount'] >= $OUTSTANDING_ALLOW_TRIP_COUNT || $orderOutstanding[0]['TripCount'] >= $OUTSTANDING_ALLOW_TRIP_COUNT))
    //     if ($fOutStandingAmount > 0 && $ePaymentOption == "Cash" && $counttripData[0]['counttrip'] >= $OUTSTANDING_ALLOW_TRIP_COUNT) {
    //         $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($userLanguageCode, "1", "");
    //         $returnArr['Action'] = "0";
    //         $returnArr['message'] = str_replace("##", formateNumAsPerCurrency($fOutStandingAmount, $currencycode), $languageLabelsArr["LBL_OUTSTANDING_RESTRICTION_ORDER"]);
    //         $returnArr['message1'] = str_replace("##", formateNumAsPerCurrency($fOutStandingAmount, $currencycode), $languageLabelsArr["LBL_OUTSTANDING_RESTRICTION_ORDER"]);//it is for web to shown message to user in restaurant_place-order.php
    //         $returnArr['ShowContactUsBtn'] = "Yes";
    //         setDataResponse($returnArr);
    //     }
    // }
    // else {
    //     if ($fOutStandingAmount > 0 && $ePaymentOption == "Cash") {
    //         $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($userLanguageCode, "1", "");
    //         $returnArr['Action'] = "0";
    //         $returnArr['message'] = $languageLabelsArr["LBL_PLEASE_CONTACT_ADMIN"];
    //         $returnArr['message1'] = str_replace("##", formateNumAsPerCurrency($fOutStandingAmount, $currencycode), $languageLabelsArr["LBL_OUTSTANDING_RESTRICTION_ORDER"]);//it is for web to shown message to user in restaurant_place-order.php
    //         $returnArr['ShowContactUsBtn'] = "Yes";
    //         setDataResponse($returnArr);
    //     }
    // }

    //added by HV on 09-01-2023 for outstanding restriction ...
    $tOutStandingIds = "";
    $orderData = $obj->MySQLSelect("SELECT tOutStandingIds FROM orders WHERE fOutStandingAmount > 0 AND iStatusCode IN (1,2,4,5,13,14) AND iUserId = '$iUserId' ");
    if(!empty($orderData[0]['tOutStandingIds'])) {
        $tOutStandingIds = $orderData[0]['tOutStandingIds'];
    }

    $returnArr['fOutStandingAmount'] = 0.00;
    $returnArr['fOutStandingAmountWithSymbol'] = formateNumAsPerCurrency(0, $currencycode);
    $fOutStandingAmount = GetPassengerOutstandingAmount($iUserId, $tOutStandingIds);
    $fOutStandingAmount = setTwoDecimalPoint($fOutStandingAmount * $priceRatio);
    $returnArr['fOutStandingAmount'] = $fOutStandingAmount;
    $returnArr['fOutStandingAmountWithSymbol'] = formateNumAsPerCurrency($fOutStandingAmount, $currencycode);

    /* Outstanding Flow */    
    $returnArr['ShowAdjustTripBtn'] = $returnArr['ShowPayNow'] = "Yes";
    $returnArr['ShowContactUsBtn'] = $returnArr['isShowOutstanding'] = "No";
    if ($MODULES_OBJ->isEnableOutstandingRestriction() && $fOutStandingAmount > 0 && $iUserId > 0 && strtoupper($isAddOutstandingAmt) == "NO") {
        if ($CONFIG_OBJ->isOnlyCashPaymentModeAvailable()) {
            $returnArr['ShowPayNow'] = "No";
            $returnArr['ShowAdjustTripBtn'] = "No";
            $returnArr['ShowContactUsBtn'] = "Yes";
        }

        if ($CONFIG_OBJ->isOnlyCardPaymentModeAvailable() || $CONFIG_OBJ->isOnlyWalletPaymentModeAvailable()) {
            $returnArr['ShowPayNow'] = "Yes";
            $returnArr['ShowAdjustTripBtn'] = "No";
        }

        if (strtoupper($ePayWallet) == "YES") {
            $outStandingSql = " AND eAuthoriseIdName='No' AND iAuthoriseId ='0'";
        }

        $counttripData = $obj->MySQLSelect("SELECT count(iTripOutstandId) as counttrip FROM trip_outstanding_amount WHERE iUserId='" . $iUserId . "' AND iUserId > 0 AND ePaidByPassenger = 'No' $outStandingSql");
        if ($counttripData[0]['counttrip'] >= $OUTSTANDING_ALLOW_TRIP_COUNT) {

            if ($CONFIG_OBJ->isOnlyCashPaymentModeAvailable()) {
                $returnArr['outstanding_restriction_label'] = str_replace("##", formateNumAsPerCurrency($fOutStandingAmount, $currencycode), $languageLabelsArr["LBL_OUTSTANDING_RESTRICTION_MSG"]);
                $returnArr['ShowAdjustTripBtn'] = "No";
            }

            if ($CONFIG_OBJ->isOnlyCardPaymentModeAvailable() || $CONFIG_OBJ->isOnlyWalletPaymentModeAvailable()) {
                $returnArr['outstanding_restriction_label'] = str_replace("##", formateNumAsPerCurrency($fOutStandingAmount, $currencycode), $languageLabelsArr["LBL_OUTSTANDING_RESTRICTION_MSG"]);
                $returnArr['ShowAdjustTripBtn'] = "No";
                $returnArr['ShowPayNow'] = "Yes";
            }
        }

        $returnArr['OutstandingTrips'] = $counttripData[0]['counttrip'];
        if ($returnArr['OutstandingTrips'] >= $OUTSTANDING_ALLOW_TRIP_COUNT) {
            $returnArr['ShowAdjustTripBtn'] = "No";
        }
        if ($returnArr['ShowPayNow'] == "Yes") {
            $returnArr['ShowContactUsBtn'] = "No";
        }

        $returnArr['isShowOutstanding'] = "Yes";
        setDataResponse($returnArr);
    }
    /* Outstanding Flow End */

    if (strtoupper($isAddOutstandingAmt) == "NO") {
        $fOutStandingAmount = 0;
    }

    $tOutStandingIdsInsert = "";
    if($fOutStandingAmount > 0) {
        $tOutStandingIdsInsert = GetPassengerOutstandingAmount($iUserId, $tOutStandingIds, 'Yes');
    }

    $Data_insert['tOutStandingIds'] = $tOutStandingIdsInsert;

    //Added By HJ On 30-09-2020 For Check Item Availability When Checkout Order End
    if ($ePaymentOption == "Card") {
        // UpdateCardPaymentPendingOrder();
    }
    /** To Get User Language Code And Currency * */
    $vCurrencyPassenger = $userData[0]['vCurrencyPassenger'];
    $vCountry = $userData[0]['vCountry'];
    if (isset($currencyAssociateArr[$vCurrencyPassenger])) {
        $userData[0]['vSymbol'] = $currencyAssociateArr[$vCurrencyPassenger]['vSymbol'];
        $userData[0]['Ratio'] = $currencyAssociateArr[$vCurrencyPassenger]['Ratio'];
        $userData[0]['currencyName'] = $currencyAssociateArr[$vCurrencyPassenger]['vName'];
        $userData[0]['iCurrencyId'] = $currencyAssociateArr[$vCurrencyPassenger]['iCurrencyId'];
        $userData[0]['eRoundingOffEnable'] = $currencyAssociateArr[$vCurrencyPassenger]['eRoundingOffEnable'];
    }
    if (isset($country_data_arr[$vCountry])) {
        $userData[0]['eUnit'] = $country_data_arr[$vCountry]['eUnit'];
    }
    //$user_detail = $obj->MySQLSelect("SELECT ru.vCurrencyPassenger, ru.vName, ru.vLastName, ru.vEmail, ru.vLang, cu.vSymbol, cu.Ratio, co.eUnit FROM register_user as ru, currency as cu, country as co WHERE ru.iUserId='" . $iUserId . "' AND cu.vName = ru.vCurrencyPassenger AND co.vCountryCode = ru.vCountry");
    //Added By HJ On 18-07-2020 For langauge labele and Other Union Table Query Start
    if (isset($languageLabelDataArr['language_label_union_other_food_' . $userLanguageCode . "_" . $iServiceId])) {
        $userLanguageLabelsArr = $languageLabelDataArr['language_label_union_other_food_' . $userLanguageCode . "_" . $iServiceId];
    }
    else {
        $userLanguageLabelsArr = $LANG_OBJ->FetchLanguageLabels($userLanguageCode, "1", $iServiceId);
        $languageLabelDataArr['language_label_union_other_food_' . $userLanguageCode . "_" . $iServiceId] = $userLanguageLabelsArr;
    }
    //Added By HJ On 18-07-2020 For langauge labele and Other Union Table Query End
    $user_currency_symbol = $userData[0]['vSymbol'];
    $user_currency_ratio = $userData[0]['Ratio'];
    $vCountry = $userData[0]['vCountry'];
    $vName = $userData[0]['vName'];
    $vLastName = $userData[0]['vLastName'];
    $vUserEmail = $userData[0]['vEmail'];
    if (strtoupper($isStoreKiosk) == "YES") {
        $vUserEmail = $UserDetails['userEmail'];
        $vName = $UserDetails['userName'];
        $vCurrencyCompany = $db_companydata[0]['vCurrencyCompany'];
        if (isset($currencyAssociateArr[$vCurrencyCompany])) {
            $user_currency_symbol = $currencyAssociateArr[$vCurrencyCompany]['vSymbol'];
            $user_currency_ratio = $currencyAssociateArr[$vCurrencyCompany]['Ratio'];
        }
    }
    $vCompany = $db_companydata[0]['vCompany'];
    $fMaxOfferAmt = $db_companydata[0]['fMaxOfferAmt'];
    $fTargetAmt = $db_companydata[0]['fTargetAmt'];
    $fOfferType = $db_companydata[0]['fOfferType'];
    $fOfferAppyType = $db_companydata[0]['fOfferAppyType'];
    $fOfferAmt = $db_companydata[0]['fOfferAmt'];
    $eAutoaccept = $db_companydata[0]['eAutoaccept'];
    $vLangCompany = $vLangCode = $db_companydata[0]['vLang'];
    //Added By HJ On 03-09-2019 For Get Store Service Id When Place Order Start
    $storeServiceId = $iServiceId;
    if (isset($db_companydata[0]['iServiceId']) && $db_companydata[0]['iServiceId'] > 0 && empty($iServiceId)) {
        $storeServiceId = $db_companydata[0]['iServiceId'];
    }
    //Added By HJ On 03-09-2019 For Get Store Service Id When Place Order End
    $Data_insert['iUserId'] = $iUserId;
    $Data_insert['iCompanyId'] = $iCompanyId;
    $Data_insert['iUserAddressId'] = $iUserAddressId;
    $Data_insert['vOrderNo'] = GenerateUniqueOrderNo($isStoreKiosk);
    $vOrderNo = $Data_insert['vOrderNo'];
    $Data_insert['tOrderRequestDate'] = @date("Y-m-d H:i:s");
    $Data_insert['dDeliveryDate'] = @date("Y-m-d H:i:s");
    $Data_insert['vUserEmail'] = $vUserEmail;
    $Data_insert['vName'] = $vName;
    $Data_insert['vLastName'] = $vLastName;
    $Data_insert['vCompany'] = $vCompany;
    $Data_insert['vCouponCode'] = trim($vCouponCode);
    $Data_insert['dDate'] = @date("Y-m-d H:i:s");
    $Data_insert['ePaymentOption'] = $ePaymentOption;
    $Data_insert['eTakeAway'] = $eTakeAway;
    if (strtoupper($isStoreKiosk) == "YES") {
        $Data_insert['eOrderplaced_by'] = "Kiosk";
        $Data_insert['tKioskUserDetails'] = json_encode($UserDetails, JSON_UNESCAPED_UNICODE);
    }
    /* OTP Verification Feature */
    if ($MODULES_OBJ->isEnableOTPVerificationDeliverAll()) {
        if ($eBuyAnyService == "No") {
            $service_category_data = $obj->MySQLSelect("SELECT eOTPCodeEnable FROM service_categories WHERE iServiceId = $storeServiceId");
        }
        else {
            $service_category_data = $obj->MySQLSelect("SELECT eOTPCodeEnable FROM vehicle_category WHERE eCatType = '$eCatType'");
        }
        if (($service_category_data[0]['eOTPCodeEnable'] == "Yes") || (ONLYDELIVERALL == "Yes" && count($service_categories_ids_arr) == 1)) {
            $Data_insert['eAskCodeToUser'] = "Yes";
            $Data_insert['vRandomCode'] = generateCommonRandom();
        }
    }
    /* OTP Verification Feature */
    /*     * ***** Changes for System payment flow method-2/method-3 ****** */
    if ($ePaymentOption == "Cash") {
        $Data_insert['iStatusCode'] = 1;
        // } else if (($SYSTEM_PAYMENT_FLOW == 'Method-2' || $SYSTEM_PAYMENT_FLOW == 'Method-3') && $ePaymentOption != 'Cash') {
    }
    else if (strtoupper($ePayWallet) == "YES" && $ePaymentOption != 'Cash') {
        $Data_insert['iStatusCode'] = 1;
    }
    else {
        $Data_insert['iStatusCode'] = 12;
        if (strtoupper($isStoreKiosk) == "YES") {
            $Data_insert['iStatusCode'] = 1;
        }
    }
    if ($eBuyAnyService == "Yes") {
        $storeServiceId = 1;
        $Data_insert['eBuyAnyService'] = "Yes";
        $Data_insert['eForPickDropGenie'] = $eForPickDropGenie;
        $Data_insert['iStatusCode'] = 1;
        if ($ePaymentOption == "Card") {
            $Data_insert['iStatusCode'] = 12;
        }
    }
    /*     * ***** Changes for System payment flow method-2/method-3 ****** */
    //$Data_insert['iStatusCode'] = ($ePaymentOption == "Cash") ? 1 : 12;
    $Data_insert['dDeliveryDate'] = @date("Y-m-d H:i:s");
    $Data_insert['vInstruction'] = $vInstruction;
    $Data_insert['vTimeZone'] = $vTimeZone;
    $Data_insert['fMaxOfferAmt'] = $fMaxOfferAmt;
    $Data_insert['fTargetAmt'] = $fTargetAmt;
    $Data_insert['fOfferType'] = $fOfferType;
    $Data_insert['fOfferAppyType'] = $fOfferAppyType;
    $Data_insert['fOfferAmt'] = $fOfferAmt;
    $Data_insert['iServiceId'] = $storeServiceId;
    $Data_insert['eCheckUserWallet'] = $CheckUserWallet;
    $user_available_balance_wallet = $WALLET_OBJ->FetchMemberWalletBalance($iUserId, "Rider", true, 'order');
    $walletDataArr = array();
    if (is_array($user_available_balance_wallet)) {
        $walletDataArr = $user_available_balance_wallet;
        $user_available_balance_wallet = $walletDataArr['CurrentBalance'];
        //$Data_insert['tUserWalletBalance'] = $walletDataArr['AutorizedWalletBalance']; // Commented By HJ On 10-01-2020 For Solved Payment Flow 2 Related Issyes Start
    }
    // payment method 2
    $Data_insert['ePayWallet'] = $ePayWallet;
    // payment method 2
    //$currencyList = get_value('currency', '*', 'eStatus', 'Active');
    for ($i = 0; $i < count($Data_ALL_currency_Arr); $i++) {
        if (strtoupper($Data_ALL_currency_Arr[$i]['eStatus']) == "ACTIVE") {
            $currencyCode = $Data_ALL_currency_Arr[$i]['vName'];
            $Data_insert['fRatio_' . $currencyCode] = $Data_ALL_currency_Arr[$i]['Ratio'];
        }
    }
    // payment method 2
    if ($iOrderId == "" || $iOrderId == NULL) {
        $Data_insert['selectedPreferences'] = $selectedpreferences;
        $iOrderId = $obj->MySQLQueryPerform("orders", $Data_insert, 'insert');
        $OrderLogId = createOrderLog($iOrderId, $Data_insert['iStatusCode']);
        $OrderDetailsIdsArr = array();
        if (!empty($OrderDetails)) {
            $fTotalMenuItemBasePrice = 0;
            //Added By HJ On 1-05-2019 For Optimize Code Start
            $optionPriceArr = getAllOptionAddonPriceArr();
            //Added By HJ On 20-07-2020 For Optimize menu_items Table Query Start
            $orderItemIdArr = array();
            for ($m = 0; $m < count($OrderDetails); $m++) {
                $orderItemIdArr[] = $OrderDetails[$m]['iMenuItemId'];
            }
            $orderDetailsArr = array();
            if (count($orderItemIdArr) > 0) {
                $implodeIds = implode(",", $orderItemIdArr);
                $menuItemData = $obj->MySQLSelect("select * from menu_items where iMenuItemId IN ($implodeIds)");
                for ($b = 0; $b < count($menuItemData); $b++) {
                    $orderDetailsArr['menu_items_' . $menuItemData[$b]['iMenuItemId']][] = $menuItemData[$b];
                }
            }
            //Added By HJ On 20-07-2020 For Optimize menu_items Table Query End
            //Added By HJ On 1-05-2019 For Optimize Code End
            for ($j = 0; $j < count($OrderDetails); $j++) {
                $iQty = $OrderDetails[$j]['iQty'];
                //$fMenuItemPrice = FoodMenuItemBasicPrice($OrderDetails[$j]['iMenuItemId'], $iQty); //Commnent By HJ On 25-05-2019 For Optimize Below Code
                //Added By HJ On 09-05-2019 For Optimize Code Start
                /*$fMenuItemPrice = 0;
                if (isset($ordItemPriceArr[$OrderDetails[$j]['iMenuItemId']]) && $ordItemPriceArr[$OrderDetails[$j]['iMenuItemId']] > 0) {
                    $fMenuItemPrice = $ordItemPriceArr[$OrderDetails[$j]['iMenuItemId']] * $iQty;
                }*/
                //Added By HJ On 09-05-2019 For Optimize Code End
                //Added By HJ On 20-07-2020 For Optimize Code menu_items Table Query Start
                if (isset($orderDetailsArr["menu_items_" . $OrderDetails[$j]['iMenuItemId']])) {
                    $fMenuItemPrice = $orderDetailsArr["menu_items_" . $OrderDetails[$j]['iMenuItemId']][0]['fPrice'] * $iQty;
                }
                else {
                    $fMenuItemPrice = FoodMenuItemBasicPrice($OrderDetails[$j]['iMenuItemId'], $iQty);
                }
                //$vOptionPrice = GetFoodMenuItemOptionPrice($OrderDetails[$j]['vOptionId']); //Commnent By HJ On 25-05-2019 For Optimize Below Code
                //Added By HJ On 20-07-2020 For Optimize Code menu_items Table Query End
                $vOptionPrice1 = $vAddonPrice1 = $vOptionPrice = $vAddonPrice = 0;

                if($eBuyAnyService == "No") {
                    $explodeOption = explode(",", $OrderDetails[$j]['vOptionId']);
                    for ($fd = 0; $fd < count($explodeOption); $fd++) {
                        if (isset($optionPriceArr[$explodeOption[$fd]]) && $optionPriceArr[$explodeOption[$fd]] > 0) {
                            $vOptionPrice1 += $optionPriceArr[$explodeOption[$fd]];
                        }
                    }
                    //Added By HJ On 1-05-2019 For Optimize Code End
                    $vOptionPrice = $vOptionPrice1 * $iQty;
                    //$vAddonPrice = GetFoodMenuItemAddOnPrice($OrderDetails[$j]['vAddonId']); //Commnent By HJ On 25-05-2019 For Optimize Below Code
                    //Added By HJ On 1-05-2019 For Optimize Code Start
                    $explodeAddon = explode(",", $OrderDetails[$j]['vAddonId']);
                    for ($df = 0; $df < count($explodeAddon); $df++) {
                        if (isset($optionPriceArr[$explodeAddon[$df]]) && $optionPriceArr[$explodeAddon[$df]] > 0) {
                            $vAddonPrice1 += $optionPriceArr[$explodeAddon[$df]];
                        }
                    }
                    //Added By HJ On 1-05-2019 For Optimize Code End
                    $vAddonPrice = $vAddonPrice1 * $iQty;
                }
                
                $fTotalMenuItemBasePrice = $fTotalMenuItemBasePrice + $fMenuItemPrice + $vOptionPrice + $vAddonPrice;
            }
            $fTotalMenuItemBasePrice = setTwoDecimalPoint($fTotalMenuItemBasePrice);
            for ($i = 0; $i < count($OrderDetails); $i++) {
                $Data = array();
                $Data['iOrderId'] = $iOrderId;
                $Data['iMenuItemId'] = isset($OrderDetails[$i]['iMenuItemId']) ? $OrderDetails[$i]['iMenuItemId'] : '';
                $Data['iFoodMenuId'] = isset($OrderDetails[$i]['iFoodMenuId']) ? $OrderDetails[$i]['iFoodMenuId'] : '';
                // $Data['fPrice'] = GetFoodMenuItemBasicPrice($Data['iMenuItemId']);
                $Data['iQty'] = isset($OrderDetails[$i]['iQty']) ? $OrderDetails[$i]['iQty'] : 0;
                if($eBuyAnyService == "Yes") {
                    $OrderDetails[$i]['vOptionId'] = $OrderDetails[$i]['vAddonId'] = "";
                }
                $MenuItemPriceArr = FetchMenuItemCostByStoreOffer($Data['iMenuItemId'], $iCompanyId, 1, $iUserId, "Calculate", $OrderDetails[$i]['vOptionId'], $OrderDetails[$i]['vAddonId'], $storeServiceId);
                $fOriginalPrice = $MenuItemPriceArr['fOriginalPrice'];
                $fDiscountPrice = $MenuItemPriceArr['fDiscountPrice'];
                $fPrice = $MenuItemPriceArr['fPrice'];
                $TotOrders = $MenuItemPriceArr['TotOrders'];
                if ($fOfferType == "Flat" && $fOfferAppyType != "None" && $TotOrders == 0) {
                    $Data['fOriginalPrice'] = $fOriginalPrice;
                    $Data['fDiscountPrice'] = $MenuItemPriceArr['fOfferAmt'];
                    $Data['fPrice'] = $fOriginalPrice;
                    $fTotalDiscountPrice = $MenuItemPriceArr['fOfferAmt'];
                    $Data['fTotalDiscountPrice'] = $fTotalDiscountPrice;
                }
                else {
                    $Data['fOriginalPrice'] = $fOriginalPrice;
                    $Data['fDiscountPrice'] = $fDiscountPrice;
                    $Data['fPrice'] = $fPrice;
                    $fTotalDiscountPrice = $fDiscountPrice * $Data['iQty'];
                    $Data['fTotalDiscountPrice'] = $fTotalDiscountPrice;
                }
                if ($fTotalMenuItemBasePrice <= $fTargetAmt && $fOfferAppyType != "None" && $fOfferType == "Flat") {
                    $Data['fOriginalPrice'] = $fOriginalPrice;
                    $Data['fDiscountPrice'] = 0;
                    $Data['fPrice'] = $fOriginalPrice;
                    $Data['fTotalDiscountPrice'] = 0;
                }
                $Data['vOptionId'] = isset($OrderDetails[$i]['vOptionId']) ? $OrderDetails[$i]['vOptionId'] : '';
                //$Data['vOptionPrice'] = GetFoodMenuItemOptionPrice($Data['vOptionId']); //Commnent By HJ On 25-05-2019 For Optimize Below Code
                //Added By HJ On 1-05-2019 For Optimize Code Start
                $vOptionPrice2 = 0;
                $explodeOption = explode(",", $Data['vOptionId']);
                for ($fd = 0; $fd < count($explodeOption); $fd++) {
                    if (isset($optionPriceArr[$explodeOption[$fd]]) && $optionPriceArr[$explodeOption[$fd]] > 0) {
                        $vOptionPrice2 += $optionPriceArr[$explodeOption[$fd]];
                    }
                }
                $Data['vOptionPrice'] = $vOptionPrice2;
                //Added By HJ On 1-05-2019 For Optimize Code End
                $Data['vAddonId'] = isset($OrderDetails[$i]['vAddonId']) ? $OrderDetails[$i]['vAddonId'] : '';
                //$Data['vAddonPrice'] = GetFoodMenuItemAddOnPrice($Data['vAddonId']); //Commnent By HJ On 25-05-2019 For Optimize Below Code
                //Added By HJ On 1-05-2019 For Optimize Code Start
                $vAddonPrice2 = 0;
                $explodeAddon = explode(",", $Data['vAddonId']);
                for ($df = 0; $df < count($explodeAddon); $df++) {
                    if (isset($optionPriceArr[$explodeAddon[$df]]) && $optionPriceArr[$explodeAddon[$df]] > 0) {
                        $vAddonPrice2 += $optionPriceArr[$explodeAddon[$df]];
                    }
                }
                $Data['vAddonPrice'] = $vAddonPrice2;
                //Added By HJ On 1-05-2019 For Optimize Code End
                $Data['fPrice'] = $Data['fOriginalPrice'] - $Data['vOptionPrice'] - $Data['vAddonPrice'];
                // $fSubTotal = $Data['fOriginalPrice']+$Data['vOptionPrice']+$Data['vAddonPrice'];
                $fSubTotal = $Data['fOriginalPrice'];
                $Data['fSubTotal'] = $fSubTotal;
                $fTotalPrice = $fSubTotal * $Data['iQty'];
                $Data['fTotalPrice'] = $fTotalPrice;
                $Data['dDate'] = @date("Y-m-d H:i:s");
                $Data['eAvailable'] = "Yes";
                //$Data['tOptionIdOrigPrice'] = GetFoodMenuItemOptionIdPriceString($Data['vOptionId']); //Commnent By HJ On 25-05-2019 For Optimize Below Code
                //Added By HJ On 09-05-2019 For Optimize Code Start
                $OptionIdPriceString = "";
                if ($Data['vOptionId'] != "") {
                    $vOptionIdArr = explode(",", $Data['vOptionId']);
                    if (count($vOptionIdArr) > 0) {
                        for ($p = 0; $p < count($vOptionIdArr); $p++) {
                            $fPriceOption = 0;
                            if (isset($optionPriceArr[$vOptionIdArr[$p]])) {
                                $fPriceOption = $optionPriceArr[$vOptionIdArr[$p]];
                            }
                            $OptionIdPriceString .= $vOptionIdArr[$p] . "#" . $fPriceOption . ",";
                        }
                    }
                }
                $Data['tOptionIdOrigPrice'] = trim($OptionIdPriceString, ",");
                //Added By HJ On 09-05-2019 For Optimize Code End
                //$Data['tAddOnIdOrigPrice'] = GetFoodMenuItemAddOnIdPriceString($Data['vAddonId']); //Commnent By HJ On 25-05-2019 For Optimize Below Code
                //Added By HJ On 09-05-2019 For Optimize Code Start
                $AddOnIdPriceString = "";
                if ($Data['vAddonId'] != "") {
                    $vAddonIdArr = explode(",", $Data['vAddonId']);
                    if (count($vAddonIdArr) > 0) {
                        for ($a = 0; $a < count($vAddonIdArr); $a++) {
                            $fPriceOption = 0;
                            if (isset($optionPriceArr[$vAddonIdArr[$a]])) {
                                $fPriceOption = $optionPriceArr[$vAddonIdArr[$a]];
                            }
                            $AddOnIdPriceString .= $vAddonIdArr[$a] . "#" . $fPriceOption . ",";
                        }
                    }
                }
                $Data['tAddOnIdOrigPrice'] = trim($AddOnIdPriceString, ",");
                // Added by HV on 13-10-2020 for Genie Pickup - Dropoff Items
                if (isset($OrderDetails[$i]['eExtraPayment'])) {
                    $Data['eExtraPayment'] = $OrderDetails[$i]['eExtraPayment'];
                }
                //Added By HJ On 09-05-2019 For Optimize Code End
                // $Data['tOptionAddonAttribute'] = isset($OrderDetails[$i]['tOptionAddonAttribute']) ? $OrderDetails[$i]['tOptionAddonAttribute'] : '';
                // payment method 2
                if ($Data['iQty'] > 0) {
                    $iOrderDetailId = $obj->MySQLQueryPerform("order_details", $Data, 'insert');
                    array_push($OrderDetailsIdsArr, $iOrderDetailId);
                }
            }
        }
    }
    else {
        $where = " iOrderId = '" . $iOrderId . "'";

        $orderdata = $obj->MySQLSelect("SELECT vOrderNo FROM orders WHERE iOrderId = '" . $iOrderId . "'");
        $Data_update_order['ePaymentOption'] = $ePaymentOption;
        if ($ePaymentOption == "Cash") {
            $Data_update_order['iStatusCode'] = 1;
        }
        else if (strtoupper($ePayWallet) == "YES" && $ePaymentOption != 'Cash') {
            $Data_update_order['iStatusCode'] = 1;
        }
        else {
            $Data_update_order['iStatusCode'] = 12;
            if (strtoupper($isStoreKiosk) == "YES") {
                $Data_update_order['iStatusCode'] = 1;
            }
        }
        $vOrderNo = $orderdata[0]['vOrderNo'];
        $Data_update_order['eCheckUserWallet'] = $CheckUserWallet;
        $obj->MySQLQueryPerform("orders", $Data_update_order, 'update', $where);
    }
    // payment method 2
    $Order_data = calculateOrderFare($iOrderId);
    $where = " iOrderId = '" . $iOrderId . "'";
    $Data_update_order['vInstruction'] = $vInstruction;
    $Data_update_order['fSubTotal'] = $Order_data['fSubTotal'];
    $Data_update_order['fOffersDiscount'] = $Order_data['fOffersDiscount'];
    $Data_update_order['fPackingCharge'] = $Order_data['fPackingCharge'];
    $Data_update_order['fDeliveryCharge'] = ($eTakeAway == 'Yes' || $isStoreKiosk == "Yes") ? 0 : $Order_data['fDeliveryCharge'];
    $Data_update_order['fTax'] = $Order_data['fTax'];
    $Data_update_order['fDiscount'] = $Order_data['fDiscount'];
    $Data_update_order['vDiscount'] = $Order_data['vDiscount'];
    $Data_update_order['fCommision'] = $Order_data['fCommision'];
    $Data_update_order['fNetTotal'] = $Order_data['fNetTotal'];
    $Data_update_order['fTotalGenerateFare'] = $Order_data['fTotalGenerateFare'];
    $Data_update_order['fOutStandingAmount'] = $Order_data['fOutStandingAmount'];
    $Data_update_order['fWalletDebit'] = $Order_data['fWalletDebit'];
    $Data_update_order['fTipAmount'] = $Order_data['fTipAmount'];
    $Data_update_order['eTipIncludedAtOrderRequest'] = 'No';
    $Data_update_order['ePaymentOption'] = $Order_data['ePaymentOption'];
    $Data_update_order['eOrderPlatform'] = strtoupper($ORDER_FROM_WEB) == "YES" ? "Web" : "App";
    if ($Order_data['fTipAmount'] > 0) {
        $Data_update_order['eTipIncludedAtOrderRequest'] = 'Yes';
    }


    $ePaymentOption = $Order_data['ePaymentOption'];

    if (!empty($iIdProofImageId)) {
        $IdProofImgData = $obj->MySQLSelect("SELECT vImage FROM idproof_images WHERE iImageId = $iIdProofImageId");
        $IdProofImage = $IdProofImgData[0]['vImage'];
        $IdProofImagePathSrc = $tconfig['tsite_upload_id_proof_service_categories_images_path'] . "/temp/";
        $IdProofImagePathDest = $tconfig['tsite_upload_id_proof_service_categories_images_path'] . "/Orders/";
        if (file_exists($IdProofImagePathSrc . $IdProofImage)) {
            if (!is_dir($IdProofImagePathDest)) {
                mkdir($IdProofImagePathDest, 0777);
                chmod($IdProofImagePathDest, 0777);
            }
            rename($IdProofImagePathSrc . $IdProofImage, $IdProofImagePathDest . $IdProofImage);
            $Data_update_order['vIdProofImg'] = $IdProofImage;
        }
    }
    if (!empty($iVoiceDirectionFileId)) {
        $voiceDirectionFileData = $obj->MySQLSelect("SELECT vFile FROM voice_direction_files WHERE iVoiceDirectionFileId = $iVoiceDirectionFileId");
        $voiceDirectionFile = $voiceDirectionFileData[0]['vFile'];
        $voiceDirectionFilePathSrc = $tconfig['tsite_upload_voice_direction_file_path'] . "/temp/";
        $voiceDirectionFilePathDest = $tconfig['tsite_upload_voice_direction_file_path'] . "/Orders/";
        if (file_exists($voiceDirectionFilePathSrc . $voiceDirectionFile)) {
            if (!is_dir($voiceDirectionFilePathDest)) {
                mkdir($voiceDirectionFilePathDest, 0777);
                chmod($voiceDirectionFilePathDest, 0777);
            }
            rename($voiceDirectionFilePathSrc . $voiceDirectionFile, $voiceDirectionFilePathDest . $voiceDirectionFile);
            $Data_update_order['tVoiceDirectionFile'] = $voiceDirectionFile;
        }
    }
    //added by SP on 15-11-2019 for rounding off start
    //$sqlp = "SELECT cu.vName, cu.iCurrencyId, cu.eRoundingOffEnable, cu.Ratio FROM register_user AS ru LEFT JOIN currency AS cu ON ru.vCurrencyPassenger = cu.vName WHERE ru.iUserId = '" . $iUserId . "'";
    //$currData = $obj->MySQLSelect($sqlp);
    $vCurrency = $userData[0]['currencyName'];
    if ($userData[0]['eRoundingOffEnable'] == "Yes" && $MODULES_OBJ->isEnableRoundingMethod()) {
        $nettotal = setTwoDecimalPoint($Order_data['fNetTotal']);
        $roundingOffTotal_fare_amountArr = getRoundingOffAmount($nettotal * $user_currency_ratio, $vCurrency);
        $roundingOffTotal_fare_amount = $roundingOffTotal_fare_amountArr['finalFareValue'];
        if ($roundingOffTotal_fare_amountArr['method'] == "Addition") {
            $eRoundingType = "Addition";
        }
        else {
            $eRoundingType = "Substraction";
        }
        $fRoundingAmount = setTwoDecimalPoint($roundingOffTotal_fare_amountArr['differenceValue']);
        if ($ePaymentOption == "Cash" && $eBuyAnyService == "No") {
            $Data_update_order['fRoundingAmount'] = $fRoundingAmount;
            $Data_update_order['eRoundingType'] = $eRoundingType;
        }
    }
    ###########################
    if ($Order_data['fNetTotal'] == 0) {
        $Data_update_order['ePaid'] = "Yes";
    }
    if ($eBuyAnyService == "Yes") {
        $Data_update_order['ePaid'] = "No";
    }
    $fareAmount = $Order_data['fNetTotal'] * $user_currency_ratio;
    // payment method 2
    /*     * ******** Checking Wallet balance when system payment method-2/method-3 ******* */
    // if ($ePaymentOption != 'Cash' && ($SYSTEM_PAYMENT_FLOW == 'Method-2' || $SYSTEM_PAYMENT_FLOW == 'Method-3') && $eWalletIgnore == 'No' && $isStoreKiosk == "No") {
    if ($ePaymentOption != 'Cash' && $ePaymentOption != 'Card' && strtoupper($ePayWallet) == "YES" && $eWalletIgnore == 'No' && $isStoreKiosk == "No") {
        if (setTwoDecimalPoint($user_available_balance_wallet) < $Order_data['fNetTotal']) {
            $Data_update_order_new['iStatusCode'] = 12;
            $Order_Update_Id = $obj->MySQLQueryPerform("orders", $Data_update_order_new, 'update', $where);
            $returnArr['Action'] = "0";
            $returnArr['iOrderId'] = $iOrderId;
            $returnArr['message'] = "LOW_WALLET_AMOUNT";
            $user_available_balance_wallet = $user_available_balance_wallet * $user_currency_ratio;
            if (!empty($walletDataArr) && count($walletDataArr) > 0) {
                $auth_wallet_amount = strval((isset($walletDataArr['TotalAuthorizedAmount']) ? $walletDataArr['TotalAuthorizedAmount'] : 0) * $user_currency_ratio);
                $returnArr['AUTH_AMOUNT'] = $auth_wallet_amount > 0 ? ($user_currency_symbol . ' ' . $auth_wallet_amount) : "";
                $returnArr['AUTH_AMOUNT_VALUE'] = $auth_wallet_amount > 0 ? $auth_wallet_amount : "";
                // $returnArr['ORIGINAL_WALLET_BALANCE'] = $user_currency_symbol . ' ' . strval((isset($walletDataArr['WalletBalance']) ? $walletDataArr['WalletBalance'] : 0) * $user_currency_ratio);
                $returnArr['ORIGINAL_WALLET_BALANCE'] = strval((isset($walletDataArr['WalletBalance']) ? $walletDataArr['WalletBalance'] : 0) * $user_currency_ratio);
                $returnArr['ORIGINAL_WALLET_BALANCE'] = formateNumAsPerCurrency($returnArr['ORIGINAL_WALLET_BALANCE'], $vCurrencyPassenger);
                $returnArr['ORIGINAL_WALLET_BALANCE_VALUE'] = strval((isset($walletDataArr['WalletBalance']) ? $walletDataArr['WalletBalance'] : 0) * $user_currency_ratio);
            }
            $returnArr['CURRENT_JOB_EST_CHARGE'] = formateNumAsPerCurrency($fareAmount, $vCurrencyPassenger);
            $returnArr['CURRENT_JOB_EST_CHARGE_VALUE'] = strval($fareAmount);
            //$returnArr['WALLET_AMOUNT_NEEDED'] = $user_currency_symbol . ' ' . strval($fareAmount - $user_available_balance_wallet);
            $returnArr['WALLET_AMOUNT_NEEDED'] = formateNumAsPerCurrency(($fareAmount - $user_available_balance_wallet), $vCurrencyPassenger);
            $returnArr['WALLET_AMOUNT_NEEDED_VALUE'] = strval($fareAmount - $user_available_balance_wallet);
            if (!empty($walletDataArr) && count($walletDataArr) > 0 && $auth_wallet_amount > 0) {
                $content_msg_low_balance = $userLanguageLabelsArr['LBL_LOW_WALLET_BAL_NOTE_WITH_AUTH_AMT'];
                // if ($SYSTEM_PAYMENT_FLOW == 'Method-3') {
                if (strtoupper($ePayWallet) == "YES" && strtoupper($isRestrictToWallet) == "YES") {
                    $content_msg_low_balance = $userLanguageLabelsArr['LBL_LOW_WALLET_BAL_NOTE_WITH_AUTH_AMT_NO_CASH'];
                }
                $content_msg_low_balance = str_replace("#####", $returnArr['WALLET_AMOUNT_NEEDED'], $content_msg_low_balance);
                if (!empty($returnArr['ORIGINAL_WALLET_BALANCE'])) {
                    $content_msg_low_balance = str_replace("####", $returnArr['ORIGINAL_WALLET_BALANCE'], $content_msg_low_balance);
                }
                if (!empty($returnArr['AUTH_AMOUNT'])) {
                    $content_msg_low_balance = str_replace("###", $returnArr['AUTH_AMOUNT'], $content_msg_low_balance);
                }
                $content_msg_low_balance = str_replace("##", "\n\n", $content_msg_low_balance);
                $returnArr['low_balance_content_msg'] = $content_msg_low_balance;
            }
            else {
                $content_msg_low_balance = $userLanguageLabelsArr['LBL_LOW_WALLET_BAL_NOTE_WITH_AMT'];
                if(strtoupper($CASH_AVAILABLE) != "YES"){
                    $content_msg_low_balance = $userLanguageLabelsArr['LBL_LOW_WALLET_BAL_NOTE_WITH_AMT_NO_CASH'];
                }
                // if ($SYSTEM_PAYMENT_FLOW == 'Method-3') {
                if (strtoupper($ePayWallet) == "YES" && strtoupper($isRestrictToWallet) == "YES") {
                    $content_msg_low_balance = $userLanguageLabelsArr['LBL_LOW_WALLET_BAL_NOTE_WITH_AMT_NO_CASH'];
                }
                $content_msg_low_balance = str_replace("#####", $returnArr['WALLET_AMOUNT_NEEDED'], $content_msg_low_balance);
                if (!empty($returnArr['ORIGINAL_WALLET_BALANCE'])) {
                    $content_msg_low_balance = str_replace("####", $returnArr['ORIGINAL_WALLET_BALANCE'], $content_msg_low_balance);
                }
                if (!empty($returnArr['CURRENT_JOB_EST_CHARGE'])) {
                    $content_msg_low_balance = str_replace("###", $returnArr['CURRENT_JOB_EST_CHARGE'], $content_msg_low_balance);
                }
                $content_msg_low_balance = str_replace("##", "\n\n", $content_msg_low_balance);
                $returnArr['low_balance_content_msg'] = $content_msg_low_balance;
            }
            // if ($SYSTEM_PAYMENT_FLOW == 'Method-3') {
            if (strtoupper($ePayWallet) == "YES" && strtoupper($isRestrictToWallet) == "YES") {
                $returnArr['IS_RESTRICT_TO_WALLET_AMOUNT'] = "Yes";
            }
            else {
                $returnArr['IS_RESTRICT_TO_WALLET_AMOUNT'] = "No";
            }
            setDataResponse($returnArr);
        }
    }
    $tEstimatedCharge = $fareAmount / $user_currency_ratio;
    if (!empty($tEstimatedCharge) && $user_available_balance_wallet > $tEstimatedCharge && strtoupper($ePayWallet) == "YES") {
        $Data_update_order['tUserWalletBalance'] = $tEstimatedCharge;
    }
    /*     * ******** Checking Wallet balance when system payment method-2/method-3 ******* */
    // payment method 2
    $Order_Update_Id = $obj->MySQLQueryPerform("orders", $Data_update_order, 'update', $where);
    /* Custom Delivery Charges */
    if ($MODULES_OBJ->isEnableDistanceWiseDeliveryChargeOrder()) {
        $order_delivery_charge_details['iOrderId'] = $iOrderId;
        $order_delivery_charge_details['fDeliveryCharge'] = $Order_data['fCustomDeliveryCharge']['fDeliveryCharge'];
        $order_delivery_charge_details['fDeliveryChargeUser'] = $Order_data['fCustomDeliveryCharge']['fDeliveryChargeUser'];
        $order_delivery_charge_details['tDeliveryChargeDetails'] = $Order_data['fCustomDeliveryCharge']['customDeliveryChargeDetails'];
        $order_delivery_charge_details['dDate'] = @date("Y-m-d H:i:s");
        $obj->MySQLQueryPerform("order_delivery_charge_details", $order_delivery_charge_details, 'insert');
    }
    /* Custom Delivery Charges End*/
    if ($Order_Update_Id > 0) {
        if ($vLangCode == "" || $vLangCode == NULL) {
            //Added By HJ On 15-07-2020 For Optimize language_master Table Query Start
            $vLangCode = $LANG_OBJ->FetchDefaultLangData("vCode");
            //Added By HJ On 15-07-2020 For Optimize language_master Table Query End
        }
        //Added By HJ On 18-07-2020 For langauge labele and Other Union Table Query Start
        if (isset($languageLabelDataArr['language_label_union_other_food_' . $vLangCode . "_" . $iServiceId])) {
            $languageLabelsArr = $languageLabelDataArr['language_label_union_other_food_' . $vLangCode . "_" . $iServiceId];
        }
        else {
            $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLangCode, "1", $storeServiceId);
            $languageLabelDataArr['language_label_union_other_food_' . $vLangCode . "_" . $iServiceId] = $languageLabelsArr;
        }
        $tableName = "company";
        $iMemberId_VALUE = $iCompanyId;
        $iMemberId_KEY = "iCompanyId";
        //Added By HJ On 18-07-2020 For Optimize company Table Query Start
        /*if(isset($userDetailsArr[$tableName."_".$iMemberId_VALUE])){
            $AppData = $userDetailsArr[$tableName."_".$iMemberId_VALUE];
        } else {
            $AppData = $obj->MySQLSelect("SELECT *,$iMemberId_KEY AS iMemberId FROM company WHERE $iMemberId_KEY='".$iMemberId_VALUE."' ");
            $userDetailsArr[$tableName."_".$iMemberId_VALUE] = $AppData;
        }*/
        $AppData = $obj->MySQLSelect("SELECT *,$iMemberId_KEY AS iMemberId FROM company WHERE $iMemberId_KEY='" . $iMemberId_VALUE . "' ");
        //Added By HJ On 18-07-2020 For Optimize company Table Query End
        $iAppVersion = $AppData[0]['iAppVersion'];
        $eDeviceType = $AppData[0]['eDeviceType'];
        $iGcmRegId = $AppData[0]['iGcmRegId'];
        $tSessionId = $AppData[0]['tSessionId'];
        $eAppTerminate = $AppData[0]['eAppTerminate'];
        $eDebugMode = $AppData[0]['eDebugMode'];
        $eHmsDevice = $AppData[0]['eHmsDevice'];
        //Added By HJ On 18-07-2020 For langauge labele and Other Union Table Query End
        $orderreceivelbl = $languageLabelsArr['LBL_NEW_ORDER_PLACED_TXT'] . $vOrderNo;
        $alertMsg = $orderreceivelbl;
        $CompanyMessage = "OrderRequested";
        $message_arr['tSessionId'] = $tSessionId;
        $message_arr['vTitle'] = $alertMsg;
        $message_arr['Message'] = $CompanyMessage;
        $message_arr['MsgCode'] = strval(time() . mt_rand(1000, 9999));
        $message_arr['iOrderId'] = $iOrderId;
        $message_arr['eSystem'] = "DeliverAll";
        $message_arr['iCompanyId'] = $iCompanyId;//added by SP on 28-01-2021 to send notification used in store panel using socket cluster for order inventory.
        $message_pub = json_encode($message_arr, JSON_UNESCAPED_UNICODE);
        /* For PubNub Setting Finished */
        $data_CompanyRequest = array();
        $data_CompanyRequest['iCompanyId'] = $iCompanyId;
        $data_CompanyRequest['iOrderId'] = $iOrderId;
        $data_CompanyRequest['tMessage'] = $message_pub;
        $data_CompanyRequest['vMsgCode'] = $message_arr['MsgCode'];
        $data_CompanyRequest['dAddedDate'] = @date("Y-m-d H:i:s");
        $requestId = addToCompanyRequest2($data_CompanyRequest);
        if ($ePaymentOption == "Cash" || $Order_data['fNetTotal'] == 0) {
            $CompanyMessage = "OrderRequested";
            $channelName = "COMPANY_" . $iCompanyId;
            $generalDataArr[] = array(
                'eDeviceType' => $eDeviceType, 'deviceToken' => $iGcmRegId, 'alertMsg' => $alertMsg, 'eAppTerminate' => $eAppTerminate, 'eDebugMode' => $eDebugMode, 'eHmsDevice' => $eHmsDevice, 'message' => $message_arr, 'channelName' => $channelName, 'orderEventChannelName' => $orderEventChannelName
            );
            if ($eBuyAnyService == "No") {
                $EVENT_MSG_OBJ->send(array('GENERAL_DATA' => $generalDataArr), RN_COMPANY);
            }
        }
        if ($storeServiceId == 5) {
            $pres_update = setorderid_for_prescription($iUserId, $iOrderId);
        }
        $iStatusCode = get_value('orders', 'iStatusCode', 'iOrderId', $iOrderId, '', 'true');
        $returnArr['Action'] = "1";
        $returnArr['iOrderId'] = $iOrderId;
        $returnArr['iOrderIdWeb'] = base64_encode(base64_encode($iOrderId));
        $returnArr['vOrderNo'] = $vOrderNo;
        //Added By HJ On 13-11-2019 B'coz Code Moved In CaptureCardPaymentOrder Type As Per Discuss With KS Sir Start For Only Cash Payment Mode
        if (($eAutoaccept == "Yes" && $MODULES_OBJ->isEnableAutoAcceptStoreOrder() && ($ePaymentOption == "Cash" || $Order_data['fNetTotal'] == 0)) || ($ePaymentOption == "Cash" && $eBuyAnyService == "Yes") || strtoupper($isStoreKiosk) == "YES" || ($ePaymentOption == "Wallet" && $Order_data['fNetTotal'] == 0 && $eBuyAnyService == "Yes")) { // If Store have enable and Admin Side Enable Setting
            if ($iStatusCode != "2" && $eBuyAnyService == "No") {
                $returnArr1 = ConfirmOrderByRestaurantcall($iCompanyId, $iOrderId); // For Auto Accept order From Store
            }
            if ($vCountry == "") {
                $vCountry = $db_companydata[0]['vCountry'];
            }
            $channelName = "COMPANY_" . $iCompanyId;
            $message_arr['Message'] = "OrderAutoAccept";
            $message_arr['eOrderplacedBy'] = "User";
            if (strtoupper($isStoreKiosk) == "YES") {
                $message_arr['eOrderplacedBy'] = "Kiosk";
            }
            $generalDataArr = array();
            $generalDataArr[] = array(
                'eDeviceType' => $eDeviceType, 'deviceToken' => $iGcmRegId, 'alertMsg' => $alertMsg, 'eAppTerminate' => $eAppTerminate, 'eDebugMode' => $eDebugMode, 'eHmsDevice' => $eHmsDevice, 'message' => $message_arr, 'channelName' => $channelName, 'orderEventChannelName' => $orderEventChannelName
            );
            if ($eBuyAnyService == "No") {
                $EVENT_MSG_OBJ->send(array('GENERAL_DATA' => $generalDataArr), RN_COMPANY);
            }
            if (strtoupper($isStoreKiosk) == "NO") {
                sendAutoRequestToDriver($iOrderId, $vCountry, $eBuyAnyService); // For Send Request to Drivers
            }
        }
        //Added By HJ On 13-11-2019 B'coz Code Moved In CaptureCardPaymentOrder Type As Per Discuss With KS Sir End For Only Cash Payment Mode
        if (strtoupper($isStoreKiosk) == "NO") {
            $returnArr['WebviewPayment'] = "No";
            if ($ePaymentOption == "Card") {
                $UserAppPaymentMethodDetails = getCountryWiseAppPaymentMethod($userData[0]['vCountry']);

                if ($UserAppPaymentMethodDetails['IS_TOKENIZED'] == "No" || strtoupper($ORDER_FROM_WEB) == "YES" || $Order_data['fNetTotal'] == 0) {
                    $dataArr = array(
                        'GeneralMemberId' => $_REQUEST['GeneralMemberId'], 'GeneralUserType' => $_REQUEST['GeneralUserType'], 'tSessionId' => $_REQUEST['tSessionId'], 'iUserId' => $_REQUEST['GeneralMemberId'], 'iOrderId' => $iOrderId, 'type' => "CaptureCardPaymentOrder", 'iServiceId' => $storeServiceId, 'vPayMethod' => "Instant", 'eSystem' => "DeliverAll",
                    );
                    callCaptureCardPaymentOrder($dataArr);
                }
                else {
                    $tDescription = "Amount charge for order no: " . $vOrderNo;
                    $paymentData = array(
                        "amount" => $Order_data['fNetTotal'], "description" => $tDescription, "iMemberId" => $iUserId, "UserType" => "Passenger", "iPaymentInfoId" => $iPaymentInfoId, "iOrderId" => $iOrderId
                    );
                    $result = (PaymentGateways::getInstance())->execute($paymentData);
            
                    if ($result['Action'] == "1") {
                        $sql = "SELECT * FROM user_payment_info WHERE iPaymentInfoId = '" . $iPaymentInfoId . "'";
                        $sqlData = $obj->MySQLSelect($sql);
                        $payment_arr['CARD_TOKEN'] = $sqlData[0]['tCardToken'];
                        $payment_id = $result['payment_id'];
                        $AMOUNT = $Order_data['fNetTotal'];
                        $UserType = "Passenger";
                        $SYSTEM_TYPE = "APP";
                        // Update details in db
                        include $tconfig['tpanel_path'] . 'assets/libraries/webview/capture-payment-details.php';
                        exit;
                    }
                    else {
                        $returnArr['Action'] = "0";
                        $returnArr['message'] = $result['message'];
                        setDataResponse($returnArr);
                    }
                }
            }
            elseif (($ePaymentOption == "Cash" || $ePaymentOption == "Wallet") && !empty($fromOrder)) {
                $orderDetailsSession = "ORDER_DETAILS_" . strtoupper($fromOrder);
                unset($_SESSION[$orderDetailsSession]);
            }
        }
        else {
            $amountToPay = formateNumAsPerCurrency($fareAmount, $vCurrencyCompany);

            $paymentMode = $languageLabelsArr['LBL_CASH_TXT'];
            if ($ePaymentOption == "Card") {
                $paymentMode = $languageLabelsArr['LBL_CARD'];
            }
            $returnArr['AmountPaidText'] = str_replace(['#AMOUNT#', '#PAYMENT_MODE#'], [$amountToPay, $paymentMode], $languageLabelsArr['LBL_AMOUNT_PAY_AT_COUNTER']);
            $returnArr['OrderPlacedScreenTime'] = STORE_KIOSK_ORDER_PLACED_SCREEN_TIME;
            $Data_SMS['vOrderNo'] = $vOrderNo;
            $sms_message_layout = $COMM_MEDIA_OBJ->GetSMSTemplate("ORDER_ACCEPTED_KIOSK", $Data_SMS, "", $vLangCode);
            $COMM_MEDIA_OBJ->SendSystemSMS($UserDetails['userMobile'], $db_companydata[0]['vCode'], $sms_message_layout);
            $Data_Mail['vOrderNo'] = $vOrderNo;
            $Data_Mail['vName'] = $UserDetails['userName'];
            $Data_Mail['vEmail'] = $UserDetails['userEmail'];
            $COMM_MEDIA_OBJ->SendMailToMember("ORDER_ACCEPTED_KIOSK", $Data_Mail);
        }
        //$returnArr['message'] = getPassengerDetailInfo($iUserId, "", "");

        if (strtoupper($isStoreKiosk) == "YES"){
            $returnArr['message'] = getCompanyDetailInfo($iCompanyId);
        }else{
            if($eBuyAnyService == "No") {
                $LiveActivityArr = array();
                $LiveActivityArr["APP_NAME"] = $SITE_NAME;
                $LiveActivityArr["ETA"] = "";
                $LiveActivityArr["ETA_TITLE"] = "";
                $LiveActivityArr["ETA_SUBTITLE"] = $languageLabelsArr['LBL_WAITING_STORE_ACCEPT_NOTI_MSG'];
                $LiveActivityArr["STORE_NAME"] = $vCompany;
                $LiveActivityArr["DISTANCE_STEP"] = "1";
                $LiveActivityArr["ORDER_STAGE"] = "OrderPlaced";
                $LiveActivityArr["APP_TYPE"] = "DeliverAll";

                $returnArr['LiveActivityData'] = $LiveActivityArr;
            }
            
            $returnArr['message'] = getPassengerDetailInfo($iUserId, "", "");
        }

        setDataResponse($returnArr);
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
        setDataResponse($returnArr);
    }
}
// ############################ Check Out Order Details ###########################################################################
// ############################# Capture Card Paymant of Order ####################################################################
if ($type == "CaptureCardPaymentOrder") {
    $iOrderId = isset($_REQUEST["iOrderId"]) ? $_REQUEST["iOrderId"] : '';
    $ePaymentOption = isset($_REQUEST["ePaymentOption"]) ? $_REQUEST["ePaymentOption"] : '';
    $vStripeToken = isset($_REQUEST["vStripeToken"]) ? $_REQUEST["vStripeToken"] : '';
    $CheckUserWallet = isset($_REQUEST["CheckUserWallet"]) ? $_REQUEST["CheckUserWallet"] : 'No';
    $iUserId = isset($_REQUEST["iUserId"]) ? $_REQUEST["iUserId"] : '';
    $payStatus = isset($_REQUEST["payStatus"]) ? $_REQUEST["payStatus"] : '';
    $vPayMethod = isset($_REQUEST["vPayMethod"]) ? $_REQUEST["vPayMethod"] : ''; // Instant,Manual
    $fromOrder = isset($_REQUEST["order"]) ? $_REQUEST["order"] : 'guest';
    $SYSTEM_TYPE = isset($_REQUEST['SYSTEM_TYPE']) ? $_REQUEST['SYSTEM_TYPE'] : 'APP';
    $cardToken = isset($_REQUEST['cardToken']) ? $_REQUEST['cardToken'] : '';
    $tPaymentId = isset($_REQUEST['tPaymentId']) ? $_REQUEST['tPaymentId'] : '';
    if ($payStatus != "succeeded" && $payStatus != "") {
        $payStatus = "Failed";
    }
    $vCountry = "";
    if ($CheckUserWallet == "" || $CheckUserWallet == NULL) {
        $CheckUserWallet = "No";
    }
    $data_order = $obj->MySQLSelect("SELECT * FROM orders WHERE iOrderId='" . $iOrderId . "'");
    $iUserId = $data_order[0]['iUserId'];
    $fNetTotal = $data_order[0]['fNetTotal'];
    $tUserWalletBalance = $data_order[0]['tUserWalletBalance'];
    $ePaymentOption = $data_order[0]['ePaymentOption'];
    $CheckUserWallet = $data_order[0]['eCheckUserWallet'];
    $eProcessed = $data_order[0]['eProcessed'];
    $iServiceIdOrder = $data_order[0]['iServiceId'];

    if($eProcessed == "No") {    
        /* Check debit wallet For Count Total Fare  Start */
        $user_wallet_debit_amount = 0;
        $full_adjustment = 0;
        if ($CheckUserWallet == "Yes") {
            $user_available_balance = $WALLET_OBJ->FetchMemberWalletBalance($iUserId, "Rider");
            if ($user_available_balance > 0) {
                $totalCurrentActiveTripsArr = FetchTotalOngoingTrips($iUserId);
                $totalCurrentActiveTripsIdsArr = $totalCurrentActiveTripsArr['ActiveTripIds'];
                $totalCurrentActiveOrderIdsArr = $totalCurrentActiveTripsArr['ActiveOrderIds'];
                $totalCurrentActiveTripsCount = $totalCurrentActiveTripsArr['TotalCount'];
                /*             * ******** Replace current wallet balance of user when System payment flow is Method-2/Method-3 ***** */
                // Charge an amount that is autorized when trip was initially requested in case when multiple jobs are going on.
                // if (($totalCurrentActiveTripsCount > 1 || in_array($iOrderId, $totalCurrentActiveOrderIdsArr) == false) && ($SYSTEM_PAYMENT_FLOW == "Method-2" || $SYSTEM_PAYMENT_FLOW == 'Method-3')) {
                if (($totalCurrentActiveTripsCount > 1 || in_array($iOrderId, $totalCurrentActiveOrderIdsArr) == false) && $ePaymentOption == "Wallet") {
                    $user_available_balance = $tUserWalletBalance;
                }
                /*             * ******** Replace current wallet balance of user when System payment flow is Method-2/Method-3 ***** */
            }
            if ($fNetTotal > $user_available_balance) {
                $fNetTotal = $fNetTotal - $user_available_balance;
                $user_wallet_debit_amount = $user_available_balance;
            }
            else {
                $user_wallet_debit_amount = $fNetTotal;
                $fNetTotal = 0;
                $full_adjustment = 1;
            }
        }
        /* Check debit wallet For Count Total Fare  Start */
        $vOrderNo = $data_order[0]['vOrderNo'];
        $iCompanyId = $data_order[0]['iCompanyId'];
        if ($ePaymentOption == "Card") {
            $DefaultCurrencyData = get_value('currency', 'vName,Ratio', 'eDefault', 'Yes');
            $currencyCode = $DefaultCurrencyData[0]['vName'];
            $currencyratio = $DefaultCurrencyData[0]['Ratio'];
            $price_new = round($fNetTotal * $currencyratio, 2);
            $tDescription = "Amount charge for order no: " . $vOrderNo;
            if ($fNetTotal > 0 && $payStatus == "") {
                $REFERRAL_AMOUNT_ARR = getPriceUserCurrency($iUserId, "Passenger", $fNetTotal);
                $REFERRAL_AMOUNT_USER = $REFERRAL_AMOUNT_ARR['fPricewithsymbol'];
                $eSystem = isset($_REQUEST["eSystem"]) ? $_REQUEST["eSystem"] : '';
                $themeColor = isset($_REQUEST["AppThemeColor"]) ? $_REQUEST["AppThemeColor"] : '000000';
                $textColor = isset($_REQUEST["AppThemeTxtColor"]) ? $_REQUEST["AppThemeTxtColor"] : 'FFFFFF';
                $GeneralAppVersion = $appVersion;
                $returnUrl = isset($_REQUEST['returnUrl']) ? trim($_REQUEST['returnUrl']) : 'webservice_shark.php';
                $extraPara = "&ePaymentOption=" . $ePaymentOption . "&CheckUserWallet=" . $CheckUserWallet . "&eSystem=" . $eSystem . "&vStripeToken=" . $vStripeToken . "&type=" . $type . "&Platform=" . $Platform . "&tSessionId=" . $tSessionId . "&GeneralMemberId=" . $GeneralMemberId . "&GeneralUserType=" . $GeneralUserType . "&GeneralDeviceType=" . $GeneralDeviceType . "&GeneralAppVersion=" . $GeneralAppVersion . "&vTimeZone=" . $vTimeZone . "&vUserDeviceCountry=" . $vUserDeviceCountry . "&iServiceId=" . $iServiceId . "&vCurrentTime=" . $vCurrentTime . "&returnUrl=" . $returnUrl . "&vPayMethod=" . $vPayMethod . "&AppThemeColor=" . $themeColor . "&AppThemeTxtColor=" . $textColor . "&SYSTEM_TYPE=" . $SYSTEM_TYPE . "&description=" . urlencode($tDescription);
                $extraPara1 = "&order=" . $fromOrder . "&PAGE_TYPE=CHARGE_CARD";
                /* Changed by HV on 22-02-2020 Updated webview url */
                $getWayUrl = $tconfig['tsite_url'] . "assets/libraries/webview/system_payment.php?iUserId=" . $iUserId . "&iOrderId=" . $iOrderId . "&AMOUNT=" . $price_new . "&currencyCode=" . $currencyCode . "&userAmount=" . $REFERRAL_AMOUNT_USER . "&vOrderNo=" . $vOrderNo . $extraPara . $extraPara1;
                $returnArr = array();
                $returnArr['Action'] = "1";
                $returnArr['message'] = $getWayUrl;
                setDataResponse($returnArr);
            }
            if (isset($result['status']) && $result['status'] == "succeeded" && $result['paid'] == "1" || $status == "success" || $payStatus == "succeeded" || $fNetTotal == 0) {
                $where = " iOrderId = '$iOrderId'";
                $iTransactionId = 0;
                if (isset($result) && $result != "") {
                    $iTransactionId = $result['id'];
                    if ($fNetTotal == 0) {
                        $iTransactionId = 0;
                    }
                }
                $data['iTransactionId'] = $iTransactionId;
                if ($data_order[0]['eBuyAnyService'] == "No") {
                    $data['ePaid'] = "Yes";
                    $OrderLogId = createOrderLog($iOrderId, "1");
                }

                $data['fWalletDebit'] = $user_wallet_debit_amount;
                $data['fNetTotal'] = $fNetTotal;
                /*--------------------- wallet and card payment --------------------*/
                if (isset($data_order[0]['fWalletDebit']) && !empty($data_order[0]['fWalletDebit']) && $data_order[0]['fWalletDebit'] > 0) {
                    $data['fWalletDebit'] = $user_wallet_debit_amount = $data_order[0]['fWalletDebit'];
                }

                if(isset($data_order[0]['fNetTotal']) && !empty($data_order[0]['fNetTotal']) && $data_order[0]['fNetTotal'] >0 ){
                    $data['fNetTotal'] = $fNetTotal = $data_order[0]['fNetTotal'];
                }
                /*--------------------- wallet and card payment --------------------*/

                $data['eCheckUserWallet'] = $CheckUserWallet;
                $data['eProcessed'] = 'Yes';


                $id = $obj->MySQLQueryPerform("orders", $data, 'update', $where);
                $returnArr["Action"] = "1";
                //Added By HJ On 13-11-2019 As Per Discuss With KS Sir For Auto accept Store and Send Request To Driver Auto Process Start
                $db_companydata = $obj->MySQLSelect("select eAutoaccept,vCountry,eBuyAnyService from `company` where iCompanyId = '" . $iCompanyId . "'");
                if ((isset($db_companydata[0]['eAutoaccept']) && $db_companydata[0]['eAutoaccept'] == "Yes" && $MODULES_OBJ->isEnableAutoAcceptStoreOrder()) || $db_companydata[0]['eBuyAnyService'] == "Yes") { // If Store have enable and Admin Side Enable Setting
                    if ($data_order[0]['iStatusCode'] != "2" && $db_companydata[0]['eBuyAnyService'] == "No") {
                        $returnArr1 = ConfirmOrderByRestaurantcall($iCompanyId, $iOrderId); // For Auto Accept order From Store
                    }
                    if ($vCountry == "") {
                        $vCountry = $db_companydata[0]['vCountry'];
                    }
                    if ($data_order[0]['eTakeaway'] != "Yes") {
                        sendAutoRequestToDriver($iOrderId, $vCountry, $db_companydata[0]['eBuyAnyService']); // For Send Request to Drivers
                    }
                }
                //Added By HJ On 13-11-2019 As Per Discuss With KS Sir For Auto accept Store and Send Request To Driver Auto Process End
                ## Insert Into Payment Table ##
                /* Added by HV on 21-02-2020 Common for all Payment Methods */
                if ($tPaymentId == "") {
                    $allCurrentSystemPaymentDetails = $CONFIG_OBJ->getCurrentSystemPaymentDetails();
                    $payment_arr = array();
                    foreach ($allCurrentSystemPaymentDetails as $zkey => $zValue) {
                        if (startsWith(strtoupper($zkey), strtoupper($APP_PAYMENT_METHOD))) {
                            $payment_arr[$zkey] = $zValue;
                        }
                    }
                    $payment_arr['SYSTEM_PAYMENT_ENVIRONMENT'] = $SYSTEM_PAYMENT_ENVIRONMENT;
                    $payment_arr['APP_PAYMENT_MODE'] = $APP_PAYMENT_MODE;
                    $payment_arr['APP_PAYMENT_METHOD'] = $APP_PAYMENT_METHOD;
                    $payment_arr['COMMISION_DEDUCT_ENABLE'] = $COMMISION_DEDUCT_ENABLE;
                    $payment_arr['WALLET_MIN_BALANCE'] = $WALLET_MIN_BALANCE;
                    $payment_arr['PAYMENT_ENABLED'] = $PAYMENT_ENABLED;
                    $payment_arr['CARD_TOKEN'] = $cardToken;
                    $tPaymentDetails = json_encode($payment_arr, JSON_UNESCAPED_UNICODE);
                    /* Added by HV on 21-02-2020 Common for all Payment Methods End */
                    // $pay_data['tPaymentUserID'] = $iTransactionId;


                    $pay_data['tPaymentUserID'] = $_REQUEST['tPaymentUserID'];
                    $pay_data['vPaymentUserStatus'] = "approved";
                    $pay_data['iAmountUser'] = $fNetTotal;
                    $pay_data['tPaymentDetails'] = $tPaymentDetails;
                    $pay_data['iOrderId'] = $iOrderId;
                    $pay_data['vPaymentMethod'] = $APP_PAYMENT_METHOD;
                    $pay_data['iUserId'] = $iUserId;
                    $pay_data['eUserType'] = "Passenger";
                    $pay_data['eEvent'] = "OrderPayment";
                    $id = $obj->MySQLQueryPerform("payments", $pay_data, 'insert');
                    $payment_id = $id;
                }
                else {
                    $where = " iPaymentId = '$tPaymentId'";
                    $pay_data['eEvent'] = "OrderPayment";
                    $pay_data['iOrderId'] = $iOrderId;
                    $id = $obj->MySQLQueryPerform("payments", $pay_data, 'update', $where);
                    $payment_id = $tPaymentId;
                }
                ## Insert Into Payment Table ##
                // Update User Wallet
                if ($user_wallet_debit_amount > 0 && $CheckUserWallet == "Yes") {
                    $vRideNo = $data_order[0]['vOrderNo'];
                    $data_wallet['iUserId'] = $iUserId;
                    $data_wallet['eUserType'] = "Rider";
                    $data_wallet['iBalance'] = $user_wallet_debit_amount;
                    $data_wallet['eType'] = "Debit";
                    $data_wallet['dDate'] = date("Y-m-d H:i:s");
                    $data_wallet['iTripId'] = 0;
                    $data_wallet['iOrderId'] = $iOrderId;
                    $data_wallet['eFor'] = "Booking";
                    $data_wallet['ePaymentStatus'] = "Unsettelled";
                    $data_wallet['tDescription'] = "#LBL_DEBITED_BOOKING_DL#" . " " . $vRideNo;
                    $WALLET_OBJ->PerformWalletTransaction($data_wallet['iUserId'], $data_wallet['eUserType'], $data_wallet['iBalance'], $data_wallet['eType'], $data_wallet['iTripId'], $data_wallet['eFor'], $data_wallet['tDescription'], $data_wallet['ePaymentStatus'], $data_wallet['dDate'], $iOrderId);
                    // $obj->MySQLQueryPerform("user_wallet",$data_wallet,'insert');
                }
                // Update User Wallet
                $updateQury = "UPDATE trip_outstanding_amount set ePaidByPassenger = 'Yes',vOrderAdjusmentId = '" . $vOrderNo . "',ePaidByWallet='Yes' WHERE iUserId = '" . $iUserId . "' AND ePaidByPassenger = 'No'";
                $obj->sql_query($updateQury);
            }
            else {
                $where = " iOrderId = '$iOrderId'";
                $data['iStatusCode'] = 11;
                $id = $obj->MySQLQueryPerform("orders", $data, 'update', $where);
                $OrderLogId = createOrderLog($iOrderId, "11");
                $returnArr['Action'] = "0";
                $returnArr['message'] = "LBL_CHARGE_COLLECT_FAILED";
                setDataResponse($returnArr);
            }
            $data['ePaymentOption'] = "Card";
            //}
        }
        else if ($ePaymentOption == "Cash") {
            $data['ePaymentOption'] = "Cash";
            $data['ePaid'] = "No";
        }
        $eConfirmArr = checkOrderStatus($iOrderId, "2");
        if (($eConfirmArr != "Yes" && $data_order[0]['eBuyAnyService'] == "No") || ($data_order[0]['eBuyAnyService'] == "Yes") )  {
            $data['iStatusCode'] = "1";
        }
        $where = " iOrderId = '$iOrderId'";
        $id = $obj->MySQLQueryPerform("orders", $data, 'update', $where);
        $OrderLogId = createOrderLog($iOrderId, "1");
        // # Send Notification To Company ##
        $CompanyMessage = "OrderRequested";
        $vLangCode = get_value('company', 'vLang', 'iCompanyId', $iCompanyId, '', 'true');
        if ($vLangCode == "" || $vLangCode == NULL) {
            //Added By HJ On 15-07-2020 For Optimize language_master Table Query Start
            $vLangCode = $LANG_OBJ->FetchDefaultLangData("vCode");
            //Added By HJ On 15-07-2020 For Optimize language_master Table Query End
        }
        $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLangCode, "1", $iServiceIdOrder);
        $orderreceivelbl = $languageLabelsArr['LBL_NEW_ORDER_PLACED_TXT'] . " " . $vOrderNo;
        $alertMsg = $orderreceivelbl;
        if ($PUBNUB_DISABLED == "Yes") {
            $ENABLE_PUBNUB = "No";
        }
        $tableName = "company";
        $iMemberId_VALUE = $iCompanyId;
        $iMemberId_KEY = "iCompanyId";
        $AppData = get_value($tableName, 'iAppVersion,eDeviceType,iGcmRegId,tSessionId,eAppTerminate,eDebugMode,eHmsDevice,vCompany', $iMemberId_KEY, $iMemberId_VALUE);
        $iAppVersion = $AppData[0]['iAppVersion'];
        $eDeviceType = $AppData[0]['eDeviceType'];
        $iGcmRegId = $AppData[0]['iGcmRegId'];
        $tSessionId = $AppData[0]['tSessionId'];
        $eAppTerminate = $AppData[0]['eAppTerminate'];
        $eDebugMode = $AppData[0]['eDebugMode'];
        $eHmsDevice = $AppData[0]['eHmsDevice'];
        $registatoin_ids = $iGcmRegId;
        $deviceTokens_arr_ios = array();
        $registation_ids_new = array();
        $message_arr['tSessionId'] = $tSessionId;
        $message_arr['vTitle'] = $alertMsg;
        $message_arr['Message'] = $CompanyMessage;
        $message_arr['MsgCode'] = strval(time() . mt_rand(1000, 9999));
        $message_arr['iOrderId'] = $iOrderId;
        $message_arr['eSystem'] = "DeliverAll";
        $message_pub = json_encode($message_arr, JSON_UNESCAPED_UNICODE);
        $data_CompanyRequest = array();
        $data_CompanyRequest['iCompanyId'] = $iCompanyId;
        $data_CompanyRequest['iOrderId'] = $iOrderId;
        $data_CompanyRequest['tMessage'] = $message_pub;
        $data_CompanyRequest['vMsgCode'] = $message_arr['MsgCode'];
        $data_CompanyRequest['dAddedDate'] = @date("Y-m-d H:i:s");
        $requestId = addToCompanyRequest2($data_CompanyRequest);
        $channelName = "COMPANY_" . $iCompanyId;
        $generalDataArr[] = array(
            'eDeviceType' => $eDeviceType, 'deviceToken' => $iGcmRegId, 'alertMsg' => $alertMsg, 'eAppTerminate' => $eAppTerminate, 'eDebugMode' => $eDebugMode, 'eHmsDevice' => $eHmsDevice, 'message' => $message_arr, 'channelName' => $channelName,
        );
        if ($data_order[0]['eBuyAnyService'] == "No") {
            $EVENT_MSG_OBJ->send(array('GENERAL_DATA' => $generalDataArr), RN_COMPANY);
        }
    }
    
    if ($vPayMethod == "Instant") {
        if ($payStatus == "" && $fNetTotal == 0 && $CheckUserWallet == "Yes" && $full_adjustment == 1) { //its used when wallet amt > order amt
            $returnArr['Action'] = "1";
            $returnArr['full_wallet_adjustment'] = "Yes";
            $returnArr['iOrderIdWeb'] = base64_encode(base64_encode($iOrderId));
            $returnArr['message'] = getPassengerDetailInfo($iUserId, "", "");
            if($data_order[0]['eBuyAnyService'] == "No") {
                $LiveActivityArr = array();
                $LiveActivityArr["APP_NAME"] = $SITE_NAME;
                $LiveActivityArr["ETA"] = "";
                $LiveActivityArr["ETA_TITLE"] = "";
                $LiveActivityArr["ETA_SUBTITLE"] = $languageLabelsArr['LBL_WAITING_STORE_ACCEPT_NOTI_MSG'];
                $LiveActivityArr["STORE_NAME"] = $AppData[0]['vCompany'];
                $LiveActivityArr["DISTANCE_STEP"] = "1";
                $LiveActivityArr["ORDER_STAGE"] = "OrderPlaced";
                $LiveActivityArr["APP_TYPE"] = "DeliverAll";

                $returnArr['LiveActivityData'] = $LiveActivityArr;
            }
            setDataResponse($returnArr);
        }
        if ($payStatus == "succeeded") {
            $payment_data = $obj->MySQLSelect("SELECT tPaymentUserID FROM payments WHERE iPaymentId = $payment_id");
            $transaction_id = $payment_data[0]['tPaymentUserID'];
            $success_msg = "LBL_MANUAL_STORE_THANK_YOU_ORDER_PLACE_ORDER";
            $successUrl = $tconfig['tsite_url'] . "assets/libraries/webview/thanks.php?orderid=" . $iOrderId . "&message=" . $success_msg . "&SYSTEM_TYPE=" . $SYSTEM_TYPE . "&order=" . $fromOrder . "&TIME=" . time() . "&iTransactionId=" . $transaction_id;
            header('Location: ' . $successUrl);
        }
        else if ($payStatus == "Failed") {
            $failedLabelValue = "LBL_CHARGE_COLLECT_FAILED";
            $failedUrl = $tconfig['tsite_url'] . "assets/libraries/webview/failure.php?success=0&message=" . $failedLabelValue . "&SYSTEM_TYPE=" . $SYSTEM_TYPE;
            header('Location: ' . $failedUrl);
        }
    }
    // # Send Notification To Company ##
    $returnArr['Action'] = "1";
    $returnArr['iOrderId'] = $iOrderId;
    $returnArr['iOrderIdWeb'] = base64_encode(base64_encode($iOrderId));
    $returnArr['message'] = getPassengerDetailInfo($iUserId, "", "");

    if($data_order[0]['eBuyAnyService'] == "No") {
        $LiveActivityArr = array();
        $LiveActivityArr["APP_NAME"] = $SITE_NAME;
        $LiveActivityArr["ETA"] = "";
        $LiveActivityArr["ETA_TITLE"] = "";
        $LiveActivityArr["ETA_SUBTITLE"] = $languageLabelsArr['LBL_WAITING_STORE_ACCEPT_NOTI_MSG'];
        $LiveActivityArr["STORE_NAME"] = $AppData[0]['vCompany'];
        $LiveActivityArr["DISTANCE_STEP"] = "1";
        $LiveActivityArr["ORDER_STAGE"] = "OrderPlaced";
        $LiveActivityArr["APP_TYPE"] = "DeliverAll";

        $returnArr['LiveActivityData'] = $LiveActivityArr;
    }
    
    setDataResponse($returnArr);
}
// ############################# Capture Card Paymant of Order ####################################################################
// ############################# Check Out Order Details ###########################################################################
// ############################ Calculate Order Estimate Amount ###################################################################
if ($type == "CheckOutOrderEstimateDetails") {
    $iUserId = isset($_REQUEST["iUserId"]) ? $_REQUEST["iUserId"] : 0;
    $iCompanyId = isset($_REQUEST["iCompanyId"]) ? $_REQUEST["iCompanyId"] : '';
    $iUserAddressId = isset($_REQUEST["iUserAddressId"]) ? $_REQUEST["iUserAddressId"] : '';
    $couponCode = isset($_REQUEST["vCouponCode"]) ? $_REQUEST["vCouponCode"] : '';
    $ePaymentOption = isset($_REQUEST["ePaymentOption"]) ? $_REQUEST["ePaymentOption"] : '';
    $vDeviceToken = isset($_REQUEST["vDeviceToken"]) ? $_REQUEST["vDeviceToken"] : '';
    $OrderDetails = isset($_REQUEST["OrderDetails"]) ? $_REQUEST["OrderDetails"] : '';
    $vInstruction = isset($_REQUEST["vInstruction"]) ? $_REQUEST["vInstruction"] : '';
    $passengerLat = isset($_REQUEST["PassengerLat"]) ? $_REQUEST["PassengerLat"] : '';
    $passengerLon = isset($_REQUEST["PassengerLon"]) ? $_REQUEST["PassengerLon"] : '';
    $CheckUserWallet = isset($_REQUEST["CheckUserWallet"]) ? $_REQUEST["CheckUserWallet"] : 'No';
    $eTakeAway = isset($_REQUEST["eTakeAway"]) ? $_REQUEST["eTakeAway"] : 'No';
    $tipAmount = isset($_REQUEST["fTipAmount"]) ? $_REQUEST["fTipAmount"] : 0;
    $selectedTipPos = isset($_REQUEST["selectedTipPos"]) ? $_REQUEST["selectedTipPos"] : 0;
    $vGeneralCurrency = isset($_REQUEST["vGeneralCurrency"]) ? $_REQUEST["vGeneralCurrency"] : '';
    $vLang = isset($_REQUEST["vGeneralLang"]) ? $_REQUEST["vGeneralLang"] : 'EN';
    $iServiceId = isset($_REQUEST["iServiceId"]) ? $_REQUEST["iServiceId"] : '';
    $isStoreKiosk = isset($_REQUEST["isStoreKiosk"]) ? $_REQUEST["isStoreKiosk"] : 'No';
    $isAddOutstandingAmt = isset($_REQUEST["isAddOutstandingAmt"]) ? $_REQUEST["isAddOutstandingAmt"] : 'No';
    /* For New Payment Flow */
    $params = array(
        "iMemberId" => $iUserId, "eUserType" => "Passenger", "GET_DATA" => "Yes"
    );
    $payment_mode_data = GetPaymentModeDetails($params);
    $ePaymentMode = !empty($payment_mode_data['PaymentMode']) ? $payment_mode_data['PaymentMode'] : "cash";
    $cashPayment = $ePaymentMode == "cash" ? "Yes" : "No";
    $ePayWallet = $ePaymentMode == "wallet" ? "Yes" : "No";
    $CheckUserWallet = $ePaymentMode == "wallet" ? "Yes" : ($payment_mode_data['eWalletDebit'] == "Yes" ? "Yes" : "No");
    $isRestrictToWallet = $payment_mode_data['PAYMENT_MODE_RESTRICT_TO_WALLET'];
	$ePaymentOption =mb_convert_case($ePaymentMode, MB_CASE_TITLE, 'UTF-8');

 // $ePaymentOption = ucfirst($ePaymentMode);
    /* For New Payment Flow End */
    //Added By HJ On 21-07-2020 For Optimization register_user Table Query Start
    $tblName = "register_user";
    $currencycode = "vCurrencyPassenger";
    if (isset($userDetailsArr[$tblName . "_" . $iUserId]) && count($userDetailsArr[$tblName . "_" . $iUserId]) > 0) {
        $user_detail = $userDetailsArr[$tblName . "_" . $iUserId];
    }
    else {
        $user_detail = $obj->MySQLSelect("SELECT *,iUserId as iMemberId FROM " . $tblName . " WHERE iUserId='" . $iUserId . "'");
        $userDetailsArr[$tblName . "_" . $iUserId] = $user_detail;
    }
    //Added By HJ On 21-07-2020 For Optimization register_user Table Query End
    //$user_detail = $obj->MySQLSelect("SELECT vName,vLastName,vEmail,vPhone,$currencycode from $tbl_name WHERE iUserId = '" . $iUserId . "'");
    //Added By HJ On 21-07-2020 For Optimization currency Table Query Start
    $vCurrencyPassenger = $user_detail[0][$currencycode];
    if (isset($currencyAssociateArr[$vCurrencyPassenger])) {
        $userCurrencyData = array();
        $userCurrencyData[] = $currencyAssociateArr[$vCurrencyPassenger];
    }
    else {
        $userCurrencyData = $obj->MySQLSelect("SELECT * FROM currency WHERE vName = '" . $vCurrencyPassenger . "'");
    }
    //Added By HJ On 21-07-2020 For Optimization currency Table Query End
    //$DefaultCurrencyData = $obj->MySQLSelect("SELECT * FROM currency WHERE eDefault = 'Yes'");
    if ($CheckUserWallet == "" || $CheckUserWallet == NULL) {
        $CheckUserWallet = "No";
    }
    $Data = array();
    $restaurantnotavailable = 0;
    //Added By HJ On 21-07-2020 For Optimization company Table Query Start
    $companyTblName = "company";
    if (isset($userDetailsArr[$companyTblName . "_" . $iCompanyId]) && count($userDetailsArr[$companyTblName . "_" . $iCompanyId]) > 0) {
        $db_companydata = $userDetailsArr[$companyTblName . "_" . $iCompanyId];
    }
    else {
        $db_companydata = $obj->MySQLSelect("SELECT *,iCompanyId as iMemberId FROM " . $companyTblName . " WHERE iCompanyId='" . $iCompanyId . "'");
        $userDetailsArr[$companyTblName . "_" . $iCompanyId] = $db_companydata;
    }
    //Added By HJ On 21-07-2020 For Optimization company Table Query End
    if ($eTakeAway == 'No' && $isStoreKiosk == "No") {
        if (!empty($iUserAddressId)) {
            $data_user_address_data = $obj->MySQLSelect("SELECT iUserAddressId FROM `user_address` WHERE iUserAddressId = '" . $iUserAddressId . "' AND eStatus='Active'");
            if (empty($data_user_address_data) || count($data_user_address_data) == 0) {
                $iUserAddressId = "";
            }
        }
        if (count($iUserId) > 0) {
            $UserSelectedAddressArr = GetUserSelectedLastOrderAddressCompanyLocationWise($iUserId, "Passenger", $passengerLat, $passengerLon, $iCompanyId, $iUserAddressId);
            if (!empty($UserSelectedAddressArr)) {
                $Data['UserSelectedAddress'] = $UserSelectedAddressArr['UserSelectedAddress'];
                $Data['UserSelectedLatitude'] = $UserSelectedAddressArr['UserSelectedLatitude'];
                $Data['UserSelectedLongitude'] = $UserSelectedAddressArr['UserSelectedLongitude'];
                $Data['UserSelectedAddressId'] = $UserSelectedAddressArr['UserSelectedAddressId'];
                $iUserAddressId = $UserSelectedAddressArr['UserSelectedAddressId'];
            }
        }
        //added by SP for selected user address remove if restaurant is not in that location on 12-08-2019
        if ($iUserId > 0) {
            $db_userdata = $obj->MySQLSelect("select * from `user_address` where iUserId = '" . $iUserId . "' AND eUserType = 'Rider' AND eStatus = 'Active' ORDER BY iUserAddressId DESC");
            
            $vLangCodeData = get_value('language_master', 'vCode, vGMapLangCode', 'eDefault', 'Yes');
            $vGMapLangCode = $vLangCodeData[0]['vGMapLangCode'];
            $vRestuarantLocationLat = $db_companydata[0]['vRestuarantLocationLat'];
            $vRestuarantLocationLong = $db_companydata[0]['vRestuarantLocationLong'];
            for ($i = 0; $i < count($db_userdata); $i++) {
                $isRemoveAddressFromList = "No";
                $eLocationAvailable = "Yes";
                $addressLatitude = $db_userdata[$i]['vLatitude'];
                $addressLongitude = $db_userdata[$i]['vLongitude'];
                // $requestDataArr = array();
                // $requestDataArr['SOURCE_LATITUDE'] = $vRestuarantLocationLat;
                // $requestDataArr['SOURCE_LONGITUDE'] = $vRestuarantLocationLong;
                // $requestDataArr['DEST_LATITUDE'] = $addressLatitude;
                // $requestDataArr['DEST_LONGITUDE'] = $addressLongitude;
                // $requestDataArr['LANGUAGE_CODE'] = $vGMapLangCode;
                // $direction_data = getPathInfoBetweenLocations($requestDataArr);
                // $distance = $direction_data['distance'] / 1000;
                $distance = distanceByLocation($vRestuarantLocationLat, $vRestuarantLocationLong, $addressLatitude, $addressLongitude, "K");
                if ($distance > $LIST_RESTAURANT_LIMIT_BY_DISTANCE) {
                    $isRemoveAddressFromList = "Yes";
                }
                // if ($iCompanyId > 0) {
                //     if ($distancewithcompany > $LIST_RESTAURANT_LIMIT_BY_DISTANCE) {
                //         $isRemoveAddressFromList = "Yes";
                //     }
                // }
                if ($isRemoveAddressFromList == "Yes") {
                    $eLocationAvailable = "No";
                }
                $db_userdata[$i]['eLocationAvailable'] = $eLocationAvailable;
                if ($eLocationAvailable == 'Yes') {
                    $restaurantnotavailable = 1;
                }
            }
        }
        else {
            $restaurantnotavailable = -1;
        }
    }
    //added by SP for selected user address remove if restaurant is not in that location on 12-08-2019
    // # Checking Distance Between Company and User Address ##
    //Added By HJ On 23-01-2020 For Solved Currency Related Issues Start
    $currencySymbol = "";
    $currencycode = isset($_REQUEST["vGeneralCurrency"]) ? $_REQUEST["vGeneralCurrency"] : ''; // Added By HJ On 23-01-2020 When User Not Logged In Get Curren cy Code
    if ($iUserId > 0) {
        $UserDetailsArr = getUserCurrencyLanguageDetails($iUserId);
        if (count($UserDetailsArr) > 0) {
            $Ratio = $UserDetailsArr['Ratio'];
            $currencySymbol = $UserDetailsArr['currencySymbol'];
            $vLang = $UserDetailsArr['vLang'];
            $currencycode = $UserDetailsArr['currencycode'];
        }
    }
    else if ($isStoreKiosk == "Yes") {
        $UserDetailsArr = getCompanyCurrencyLanguageDetails($iCompanyId);
        if (count($UserDetailsArr) > 0) {
            $Ratio = $UserDetailsArr['Ratio'];
            $currencySymbol = $UserDetailsArr['currencySymbol'];
            $vLang = $UserDetailsArr['vLang'];
            $currencycode = $UserDetailsArr['currencycode'];
        }
    }
    else {
        //Added By HJ On 21-07-2020 For Optimization currency Table Query Start
        if (($currencySymbol == "" || $currencySymbol == NULL) && $currencycode != "") {
            if (isset($currencyAssociateArr[$currencycode])) {
                $currencyData = array();
                $currencyData[] = $currencyAssociateArr[$currencycode];
            }
            else {
                $currencyData = $obj->MySQLSelect("SELECT vName,vSymbol,Ratio FROM currency WHERE vName = '" . $currencycode . "'");
            }
        }
        else {
            if (!empty($vSystemDefaultCurrencyName) && !empty($vSystemDefaultCurrencySymbol) && !empty($vSystemDefaultCurrencyRatio)) {
                $currencyData = array();
                $currencyData[0]['vName'] = $vSystemDefaultCurrencyName;
                $currencyData[0]['vSymbol'] = $vSystemDefaultCurrencySymbol;
                $currencyData[0]['Ratio'] = $vSystemDefaultCurrencyRatio;
            }
            else {
                $currencyData = $obj->MySQLSelect("SELECT vName,vSymbol,Ratio FROM currency WHERE eDefault = 'Yes'");
            }
        }
        //Added By HJ On 21-07-2020 For Optimization currency Table Query End
        if (count($currencyData) > 0) {
            $currencycode = $currencyData[0]['vName'];
            $currencySymbol = $currencyData[0]['vSymbol'];
            $Ratio = $currencyData[0]['Ratio'];
        }
        else {
            $currencycode = "USD";
            $currencySymbol = "$";
            $priceRatio = 1.0000;
        }
    }
    if ($vLang == "" || $vLang == NULL) {
        $vLang = isset($_REQUEST["vGeneralLang"]) ? $_REQUEST["vGeneralLang"] : ''; // Added By HJ On 23-01-2020 When User Not Logged In Get Language Code
    }
    //Added By HJ On 23-01-2020 For Solved Currency Related Issues End
    if ($vLang == "" || $vLang == NULL) {
        //Added By HJ On 15-07-2020 For Optimize language_master Table Query Start
        $vLang = $LANG_OBJ->FetchDefaultLangData("vCode");
        //Added By HJ On 15-07-2020 For Optimize language_master Table Query End
    }
    //$db_companydata = $obj->MySQLSelect("select vImage,vCaddress,vCompany,fMaxOfferAmt,fTargetAmt,fOfferType,fOfferAppyType,iMaxItemQty,fOfferAmt,iServiceId,vRestuarantLocationLat as restaurantlat,vRestuarantLocationLong as restaurantlong,fPackingCharge,eTakeaway from `company` where iCompanyId = '" . $iCompanyId . "'");
    $db_companydata[0]['restaurantlat'] = $db_companydata[0]['vRestuarantLocationLat'];
    $db_companydata[0]['restaurantlong'] = $db_companydata[0]['vRestuarantLocationLong'];
    $iServiceId = $db_companydata[0]['iServiceId'];
    //Added By HJ On 21-07-2020 For langauge labele and Other Union Table Query Start
    if (isset($languageLabelDataArr['language_label_union_other_food_' . $vLang . "_" . $iServiceId])) {
        $languageLabelsArr = $languageLabelDataArr['language_label_union_other_food_' . $vLang . "_" . $iServiceId];
    }
    else {
        $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLang, "1", $iServiceId);
        $languageLabelDataArr['language_label_union_other_food_' . $vLang . "_" . $iServiceId] = $languageLabelsArr;
    }
    //Added By HJ On 21-07-2020 For langauge labele and Other Union Table Query End
    $vCompany = $db_companydata[0]['vCompany'];
    $vCaddress = $db_companydata[0]['vCaddress'];
    $vCompanyImage = $db_companydata[0]['vImage'];
    $fMaxOfferAmt = $db_companydata[0]['fMaxOfferAmt'];
    $fMaxOfferAmt = setTwoDecimalPoint($fMaxOfferAmt * $Ratio);
    $fTargetAmt = $db_companydata[0]['fTargetAmt'];
    $fTargetAmt = setTwoDecimalPoint($fTargetAmt * $Ratio);
    $fOfferAppyType = $db_companydata[0]['fOfferAppyType'];
    $fOfferType = $db_companydata[0]['fOfferType'];
    $iMaxItemQty = $db_companydata[0]['iMaxItemQty'];
    //Added By HJ On 15-05-2020 As Per Discuss With KS Start
    // if (strtoupper($APP_PAYMENT_MODE) == "CASH") {
    if ($CONFIG_OBJ->isOnlyCashPaymentModeAvailable()) {
        $db_companydata[0]['eTakeaway'] = "No";
    } /*else if (strtoupper($APP_PAYMENT_MODE) == "CARD" || strtoupper($APP_PAYMENT_MODE) == "CASH-CARD") {
        $db_companydata[0]['eTakeaway'] = "Yes";
    }*/ //Commented By HJ On 29-10-2020 As Per Discuss With KS Sir
    //Added By HJ On 15-05-2020 As Per Discuss With KS End
    $returnArr["eRemoveCouponCode"] = "No";
    $couponCode = trim($couponCode);
    if ($couponCode != "") {
        $validPromoCodesArr = getValidPromoCodes();
        if (empty($validPromoCodesArr) || empty($validPromoCodesArr['CouponList']) || count($validPromoCodesArr['CouponList']) == 0) {
            $returnArr['Action'] = "0"; // code is invalid
            $returnArr["message"] = "LBL_INVALID_COUPON_CODE";
            $returnArr["eRemoveCouponCode"] = "Yes";
            setDataResponse($returnArr);
        }
    }
    $ispriceshow = '';
    if (isset($iServiceId) && !empty($iServiceId)) {
        //Added By HJ On 21-07-2020 For Optimize service_categories Table Query Start
        $serviceCatArr = array();
        if (!isset($serviceCatDataArr)) {
            $serviceCatDataArr = $obj->MySQLSelect("SELECT * FROM service_categories");
        }

        for ($h = 0; $h < count($serviceCatDataArr); $h++) {
            $serviceCatArr[$serviceCatDataArr[$h]['iServiceId']] = $serviceCatDataArr[$h];
        }
        if (isset($serviceCatArr[$iServiceId])) {
            $ServiceCategoryData = array();
            $ServiceCategoryData[] = $serviceCatArr[$iServiceId];
        }
        else {
            $servFields = 'eType';
            $ServiceCategoryData = get_value('service_categories', $servFields, 'iServiceId', $iServiceId);
        }
        //Added By HJ On 21-07-2020 For Optimize service_categories Table Query End
        if (!empty($ServiceCategoryData)) {
            if (!empty($ServiceCategoryData[0]['eType']) && $ServiceCategoryData[0]['eType'] == 'separate') {
                $ispriceshow = $ServiceCategoryData[0]['eType'];
            }
        }
    }
    $OrderDetails = json_decode(stripcslashes($OrderDetails), true);
    $OrderDetailsItemsArr = array();
    if (!empty($OrderDetails)) {
        $fFinalTotal = $fTotalDiscount = $fTotalMenuItemBasePrice = $fFinalDiscountPercentage = 0;
        //Added By HJ On 09-05-2019 For Optimize Code Start
        $optionPriceArr = getAllOptionAddonPriceArr();
        $ordItemPriceArr = getAllMenuItemPriceArr();
        //Added By HJ On 09-05-2019 For Optimize Code End
        for ($j = 0; $j < count($OrderDetails); $j++) {
            $iQty = $OrderDetails[$j]['iQty'];
            //$fMenuItemPrice = FoodMenuItemBasicPrice($OrderDetails[$j]['iMenuItemId'], $iQty); //Commnent By HJ On 09-05-2019 For Optimize Below Code
            $fMenuItemPrice = 0;
            if (isset($ordItemPriceArr[$OrderDetails[$j]['iMenuItemId']]) && $ordItemPriceArr[$OrderDetails[$j]['iMenuItemId']] > 0) {
                $fMenuItemPrice = $ordItemPriceArr[$OrderDetails[$j]['iMenuItemId']] * $iQty;
            }
            //$vOptionPrice = GetFoodMenuItemOptionPrice($OrderDetails[$j]['vOptionId']); //Commnent By HJ On 09-05-2019 For Optimize Below Code
            //Added By HJ On 09-05-2019 For Optimize Code Start
            $vOptionPrice = 0;
            $explodeOption = explode(",", $OrderDetails[$j]['vOptionId']);
            for ($fd = 0; $fd < count($explodeOption); $fd++) {
                if (isset($optionPriceArr[$explodeOption[$fd]]) && $optionPriceArr[$explodeOption[$fd]] > 0) {
                    $vOptionPrice += $optionPriceArr[$explodeOption[$fd]];
                }
            }
            //Added By HJ On 09-05-2019 For Optimize Code End
            $vOptionPrice = $vOptionPrice * $iQty;
            //$vAddonPrice = GetFoodMenuItemAddOnPrice($OrderDetails[$j]['vAddonId']); //Commnent By HJ On 09-05-2019 For Optimize Below Code
            //Added By HJ On 09-05-2019 For Optimize Code Start
            $vAddonPrice = 0;
            $explodeAddon = explode(",", $OrderDetails[$j]['vAddonId']);
            for ($df = 0; $df < count($explodeAddon); $df++) {
                if (isset($optionPriceArr[$explodeAddon[$df]]) && $optionPriceArr[$explodeAddon[$df]] > 0) {
                    $vAddonPrice += $optionPriceArr[$explodeAddon[$df]];
                }
            }
            //Added By HJ On 09-05-2019 For Optimize Code End
            $vAddonPrice = $vAddonPrice * $iQty;
            if (isset($ispriceshow) && !empty($ispriceshow)) {
                if ($vOptionPrice == 0) {
                    $vOptionPrice = $vOptionPrice + $fMenuItemPrice;
                }
                $fTotalMenuItemBasePrice = $fTotalMenuItemBasePrice + $vOptionPrice + $vAddonPrice;
            }
            else {
                $fTotalMenuItemBasePrice = $fTotalMenuItemBasePrice + $vOptionPrice + $vAddonPrice + $fMenuItemPrice;
            }
        }
        if ($db_companydata[0]['fMaxOfferAmt'] > 0 && $fOfferType == "Percentage" && $fOfferAppyType != "None") {
            $fFinalDiscountPercentage = (($fTotalMenuItemBasePrice * $db_companydata[0]['fOfferAmt']) / 100);
        }
        $fTotalMenuItemBasePrice = setTwoDecimalPoint($fTotalMenuItemBasePrice * $Ratio);
        $fFinalDiscountPercentage = setTwoDecimalPoint($fFinalDiscountPercentage * $Ratio);
        //Added By HJ On 20-07-2020 For Optimize menu_items Table Query Start
        $orderItemIdArr = $itemDataArr = array();
        for ($m = 0; $m < count($OrderDetails); $m++) {
            $orderItemIdArr[] = $OrderDetails[$m]['iMenuItemId'];
        }
        if (count($orderItemIdArr) > 0) {
            $implodeIds = implode(",", $orderItemIdArr);
            $menuItemData = $obj->MySQLSelect("select * from menu_items where iMenuItemId IN ($implodeIds)");
            for ($b = 0; $b < count($menuItemData); $b++) {
                $orderDetailsArr['menu_items_' . $menuItemData[$b]['iMenuItemId']][] = $menuItemData[$b];
                $itemDataArr[$menuItemData[$b]['iMenuItemId']] = $menuItemData[$b];
            }
        }
        //Added By HJ On 20-07-2020 For Optimize menu_items Table Query End
        $itemImageUrl = $tconfig["tsite_upload_images_menu_item"];
        $orderItemAvailabilityArr = isStoreItemAvailable($OrderDetails, 1); // Added By HJ On 29-09-2020 For Check Item,Option and Addon Available Or Not
        $totalItemQty = 0;
        for ($i = 0; $i < count($OrderDetails); $i++) {
            $iMenuItemId = $OrderDetails[$i]['iMenuItemId'];
            $iFoodMenuId = $OrderDetails[$i]['iFoodMenuId'];
            $vOptionId = $OrderDetails[$i]['vOptionId'];
            $vAddonId = $OrderDetails[$i]['vAddonId'];
            $iQty = $OrderDetails[$i]['iQty'];
            //$vItemType = get_value('menu_items', 'vItemType_' . $vLang, 'iMenuItemId', $iMenuItemId, '', 'true');
            $vItemType = $vImage = "";
            if (isset($itemDataArr[$iMenuItemId])) {
                $vItemType = $itemDataArr[$iMenuItemId]['vItemType_' . $vLang];
                $vImage = $itemDataArr[$iMenuItemId]['vImage'];
            }
            $MenuItemPriceArr = FetchMenuItemCostByStoreOffer($iMenuItemId, $iCompanyId, "1", $iUserId, "Calculate", $vOptionId, $vAddonId, $iServiceId);
            $TotOrders = $MenuItemPriceArr['TotOrders'];
            if ($fOfferType == "Flat" && $fOfferAppyType != "None" && $TotOrders == 0) {
                $fOriginalPrice = $MenuItemPriceArr['fOriginalPrice'] * $iQty * $Ratio;
                $fOriginalPrice = setTwoDecimalPoint($fOriginalPrice);
                $fDiscountPrice = $MenuItemPriceArr['fDiscountPrice'] * $Ratio;
                $fDiscountPrice = setTwoDecimalPoint($fDiscountPrice);
                $fPrice = $fOriginalPrice;
                $fOfferAmt = 0;
            }
            else {
                $fOriginalPrice = $MenuItemPriceArr['fOriginalPrice'] * $iQty * $Ratio;
                $fOriginalPrice = setTwoDecimalPoint($fOriginalPrice);
                $fDiscountPrice = $MenuItemPriceArr['fDiscountPrice'] * $iQty * $Ratio;
                //Added By HJ On 31-07-2020 As Per Discuss with GP Start - Tested By GP in Other Project
                if ($fDiscountPrice > $db_companydata[0]['fMaxOfferAmt'] && $db_companydata[0]['fMaxOfferAmt'] > 0) {
                    $fDiscountPrice = $db_companydata[0]['fMaxOfferAmt'];
                }
                //Added By HJ On 31-07-2020 As Per Discuss with GP End - Tested By GP in Other Project
                $fDiscountPrice = setTwoDecimalPoint($fDiscountPrice);
                $fPrice = $MenuItemPriceArr['fPrice'] * $iQty * $Ratio;
                $fPrice = setTwoDecimalPoint($fPrice);
                $fOfferAmt = $MenuItemPriceArr['fOfferAmt'];
                $fOfferAmt = setTwoDecimalPoint($fOfferAmt);
                if ($fOfferType == "Flat" && $fOfferAppyType == "All") {
                    $fDiscountPrice = $MenuItemPriceArr['fDiscountPrice'] * $Ratio;
                    $fDiscountPrice = setTwoDecimalPoint($fDiscountPrice);
                    $fPrice = $fOriginalPrice;
                    $fOfferAmt = 0;
                }
            }
            if ($fTotalMenuItemBasePrice <= $fTargetAmt && $fOfferAppyType != "None" && $fOfferType == "Flat") {
                if (isset($_REQUEST['test'])) {
                }
                $fOriginalPrice = $MenuItemPriceArr['fOriginalPrice'] * $iQty * $Ratio;
                $fOriginalPrice = setTwoDecimalPoint($fOriginalPrice);
                $fDiscountPrice = $fOfferAmt = 0;
                $fPrice = $fOriginalPrice;
            }
            $fTotalPrice = $fOriginalPrice;
            $fTotalPrice = setTwoDecimalPoint($fTotalPrice);
            $fFinalTotal = $fFinalTotal + $fTotalPrice;
            if ($fOfferType == "Flat" && $fOfferAppyType != "None" && $TotOrders == 0) {
                $fTotalDiscount = $fDiscountPrice;
            }
            else if ($fOfferType == "Percentage" && $fOfferAppyType != "None") {
                $fTotalDiscount += $fDiscountPrice;
            }
            else {
                if ($fOfferType == "Flat" && ($fOfferAppyType == "All" || ($fOfferAppyType == "First" && $TotOrders == 0))) {
                    $fTotalDiscount = $fDiscountPrice;
                }
                else {
                    $fTotalDiscount += $fDiscountPrice;
                }
            }
            //Added By HJ On 31-07-2020 As Per Discuss with GP Start - Tested By GP in Other Project
            if ($fTotalDiscount > $db_companydata[0]['fMaxOfferAmt'] && $db_companydata[0]['fMaxOfferAmt'] > 0) {
                $fTotalDiscount = $db_companydata[0]['fMaxOfferAmt'];
            }
            //Added By HJ On 31-07-2020 As Per Discuss with GP End - Tested By GP in Other Project
            /* if ($fMaxOfferAmt > 0 && $fOfferType == "Percentage" && $fOfferAppyType != "None") {
              $fTotalDiscount = ($fTotalDiscount > $fMaxOfferAmt) ? $fMaxOfferAmt : $fTotalDiscount;
              $fPrice = ($fFinalDiscountPercentage > $fMaxOfferAmt) ? $fOriginalPrice : $fPrice;
              $fOfferAmt = ($fFinalDiscountPercentage > $fMaxOfferAmt) ? 0 : $fOfferAmt;
          } */
            $OrderDetailsItemsArr[$i]['iMenuItemId'] = $iMenuItemId;
            $OrderDetailsItemsArr[$i]['iFoodMenuId'] = $iFoodMenuId;
            $OrderDetailsItemsArr[$i]['vItemType'] = $vItemType;
            $OrderDetailsItemsArr[$i]['iQty'] = $iQty;
            /*            $OrderDetailsItemsArr[$i]['fOfferAmt'] = $fOfferAmt;
            $OrderDetailsItemsArr[$i]['fOriginalPrice'] = formatnum($fOriginalPrice);
            $OrderDetailsItemsArr[$i]['fPrice'] = formatnum($fPrice);*/
            $OrderDetailsItemsArr[$i]['fOfferAmt'] = formateNumAsPerCurrency($fOfferAmt, $currencycode);
            $OrderDetailsItemsArr[$i]['fOriginalPrice'] = formateNumAsPerCurrency($fOriginalPrice, $currencycode);
            $OrderDetailsItemsArr[$i]['fPrice'] = formateNumAsPerCurrency($fPrice, $currencycode);
            $imageUrl = "";
            if ($MODULES_OBJ->isEnableItemMultipleImageVideoUpload()) {
                $ItemMedia = $MENU_ITEM_MEDIA_OBJ->getItemMedia($iMenuItemId);
                $MenuItemMediaArr = $ItemMedia['MenuItemMedia'];
                if (isset($MenuItemMediaArr) && !empty($MenuItemMediaArr)) {
                    if ($MenuItemMediaArr[0]['eFileType'] == 'Video') {
                        $imageUrl = $MenuItemMediaArr[0]['ThumbImage'];
                    }
                    else {
                        $imageUrl = $MenuItemMediaArr[0]['vImage'];
                    }
                }
            }
            else {
                if ($vImage != "") {
                    $imageUrl = $itemImageUrl . "/" . $vImage;
                }
            }
            $OrderDetailsItemsArr[$i]['vImage'] = $imageUrl;
            // Added By HJ On 29-09-2020 For Check Item,Option and Addon Available Or Not Start
            $isItemAvailable = $isItemOptionsAvailable = $isItemAddonAvailable = "Yes";
            if (isset($orderItemAvailabilityArr['item'][$iMenuItemId])) {
                $isItemAvailable = $orderItemAvailabilityArr['item'][$iMenuItemId];
            }
            if (isset($orderItemAvailabilityArr['option'][$vOptionId])) {
                $isItemOptionsAvailable = $orderItemAvailabilityArr['option'][$vOptionId];
            }
            if (isset($orderItemAvailabilityArr['addon'][$vAddonId])) {
                $isItemAddonAvailable = $orderItemAvailabilityArr['addon'][$vAddonId];
            }
            $OrderDetailsItemsArr[$i]['isItemAvailable'] = $isItemAvailable;
            $OrderDetailsItemsArr[$i]['isItemOptionAVailable'] = $isItemOptionsAvailable;
            $OrderDetailsItemsArr[$i]['isItemAddonAVailable'] = $isItemAddonAvailable;
            // Added By HJ On 29-09-2020 For Check Item,Option and Addon Available Or Not End
            $optionaddonname = "";
            if ($vOptionId != "") {
                $optionname = GetMenuItemOptionsToppingName($vOptionId, $vLang);
                $optionaddonname = $optionname;
            }


            if ($vAddonId != "") {
                $addonname = GetMenuItemOptionsToppingName($vAddonId, $vLang);

                if ($optionaddonname != "") {
                    $optionaddonname .=  $addonname;
                }
                else {
                    $optionaddonname = $addonname;
                }
            }


            $OrderDetailsItemsArr[$i]['optionaddonname'] = trim($optionaddonname, ", ");
            $totalItemQty += $iQty;
        }
        $Data['OrderDetailsItemsArr'] = $OrderDetailsItemsArr;
        $Data['fTotalItemQty'] = $totalItemQty;
        //$fPackingCharge = get_value('company', 'fPackingCharge', 'iCompanyId', $iCompanyId, '', 'true');
        $fPackingCharge = 0;
        if (isset($db_companydata[0]['fPackingCharge']) && $db_companydata[0]['fPackingCharge'] > 0) {
            $fPackingCharge = setTwoDecimalPoint($db_companydata[0]['fPackingCharge'] * $Ratio);
        }
        // # Calculate Order Delivery Charge ##
        $fDeliveryCharge = 0;
        if ($eTakeAway == 'No' && $isStoreKiosk == "No") {
            if (isset($Data['UserSelectedLatitude']) && isset($Data['UserSelectedLongitude'])) {
                //$sql = "SELECT vRestuarantLocationLat as restaurantlat,vRestuarantLocationLong as restaurantlong FROM company WHERE iCompanyId    = '" . $iCompanyId . "'";
                //$datac = $obj->MySQLSelect($sql);
                if (count($db_companydata) > 0) {
                    $User_Address_Array = array($Data['UserSelectedLatitude'], $Data['UserSelectedLongitude']);
                    $iLocationId = GetUserGeoLocationId($User_Address_Array);
                    $requestDataArr = array();
                    $requestDataArr['SOURCE_LATITUDE'] = $db_companydata[0]['restaurantlat'];
                    $requestDataArr['SOURCE_LONGITUDE'] = $db_companydata[0]['restaurantlong'];
                    $requestDataArr['DEST_LATITUDE'] = $Data['UserSelectedLatitude'];
                    $requestDataArr['DEST_LONGITUDE'] = $Data['UserSelectedLongitude'];
                    $requestDataArr['LANGUAGE_CODE'] = $vGMapLangCode;
                    $direction_data = getPathInfoBetweenLocations($requestDataArr);
                    $distance = $direction_data['distance'] / 1000;
                    //Added By HJ On 02-01-2019 For Get All Location Delivery Charge Start As Per Discuss With CD Sir
                    $checkAllLocation = 1;
                    if (count($iLocationId) > 0) {
                        // $sql = "SELECT * FROM `delivery_charges` WHERE iLocationId = '" . $iLocationId . "' AND eStatus='Active'";
                        $iLocationIdArr = implode(',', $iLocationId);
                        $sql = "SELECT * FROM `delivery_charges` WHERE iLocationId IN (" . $iLocationIdArr . ") AND eStatus='Active' GROUP BY iLocationId ORDER BY iDeliveyChargeId DESC";
                        if ($MODULES_OBJ->isEnableDistanceWiseDeliveryChargeOrder()) {
                            $sql = "SELECT * FROM `delivery_charges` WHERE iLocationId IN (" . $iLocationIdArr . ") AND eStatus='Active' AND $distance >= iDistanceRangeFrom AND $distance <= iDistanceRangeTo ORDER BY iDeliveyChargeId DESC";
                        }
                        $data_location = $obj->MySQLSelect($sql);
                        if (count($data_location) > 0) {
                            $checkAllLocation = 0;
                        }
                    }
                    if ($checkAllLocation == 1) {
                        $sql = "SELECT * FROM `delivery_charges` WHERE iLocationId = '0' AND eStatus='Active' GROUP BY iLocationId";
                        if ($MODULES_OBJ->isEnableDistanceWiseDeliveryChargeOrder()) {
                            $sql = "SELECT * FROM `delivery_charges` WHERE iLocationId = '0' AND eStatus='Active' AND $distance >= iDistanceRangeFrom AND $distance <= iDistanceRangeTo ORDER BY iDeliveyChargeId DESC";
                        }
                        $data_location = $obj->MySQLSelect($sql);
                    }
                    $fDeliveryCharge = 0;
                    if (count($data_location) > 0) {
                        //Added By HJ On 02-01-2019 For Get All Location Delivery Charge End As Per Discuss With CD Sir
                        $iFreeDeliveryRadius = $data_location[0]['iFreeDeliveryRadius'];
                        $checkedDlCharge = 0;
                        //if ($distance < $iFreeDeliveryRadius && $iFreeDeliveryRadius >= 0) {
                        if ($distance < $iFreeDeliveryRadius && $iFreeDeliveryRadius >= 0 && !empty($iFreeDeliveryRadius)) { //when zero for free order and radius then do not allow free order
                            $fDeliveryCharge = 0;
                            $checkedDlCharge = 1;
                        }
                        $fFreeOrderPriceSubtotal = $data_location[0]['fFreeOrderPriceSubtotal'];
                        $fFreeOrderPriceSubtotal = setTwoDecimalPoint($fFreeOrderPriceSubtotal * $Ratio);
                        //added by SP 27-06-2019 for delivery charge blank then it does not count as free delivery
                        //if (!empty($fFreeOrderPriceSubtotal) && $fFreeOrderPriceSubtotal != 0) {
                        if (!empty($fFreeOrderPriceSubtotal) && $fFreeOrderPriceSubtotal != 0 && !empty($fFreeOrderPriceSubtotal)) { //when zero for free order and radius then do not allow free order
                            if ($fFinalTotal > $fFreeOrderPriceSubtotal && $checkedDlCharge == 0) {
                                $fDeliveryCharge = 0;
                                $checkedDlCharge = 1;
                            }
                        }
                        $fOrderPriceValue = $data_location[0]['fOrderPriceValue'];
                        $fOrderPriceValue = setTwoDecimalPoint($fOrderPriceValue * $Ratio);
                        $fDeliveryChargeAbove = $data_location[0]['fDeliveryChargeAbove'];
                        //$fDeliveryChargeAbove = setTwoDecimalPoint($fDeliveryChargeAbove * $Ratio);
                        $fDeliveryChargeBelow = $data_location[0]['fDeliveryChargeBelow'];
                        //$fDeliveryChargeBelow = setTwoDecimalPoint($fDeliveryChargeBelow * $Ratio);
                        if ($checkedDlCharge == 0) {
                            if ($fFinalTotal >= $fOrderPriceValue) {
                                $fDeliveryCharge = $fDeliveryChargeAbove;
                                //$fDeliveryCharge = $fDeliveryChargeBelow;
                            }
                            else {
                                $fDeliveryCharge = $fDeliveryChargeBelow;
                                //$fDeliveryCharge = $fDeliveryChargeAbove;
                            }
                        }
                        /* Custom Delivery Charges */
                        $customDeliveryChargesuser = 0;
                        if ($MODULES_OBJ->isEnableDistanceWiseDeliveryChargeOrder()) {
                            $cdcSql = "SELECT * FROM custom_delivery_charges_order WHERE $distance >= iDistanceRangeFrom AND $distance <= iDistanceRangeTo ORDER BY iDeliveyChargeId DESC";
                            $cdcData = $obj->MySQLSelect($cdcSql);
                            if (count($cdcData) > 0) {
                                $customDeliveryChargesuser = $cdcData[0]['fDeliveryChargeUser'];
                            }
                        }
                        $Data['distance1'] = $distance;
                        // $Data['customDeliveryChargesuser'] = $customDeliveryChargesuser;
                        $fDeliveryCharge = ($fDeliveryCharge + $customDeliveryChargesuser) * $Ratio;
                        /* Custom Delivery Charges End */
                    }
                }
            }
        }
        if ($iUserId != "" && $iUserId > 0) {
            /* Delivery Tip */
            if ($DELIVERY_TIP_AMOUNT_TYPE_DELIVERALL == "Percentage") {
                if ($selectedTipPos == 1) {
                    $tipAmount = ($TIP_AMOUNT_1 / 100) * ($fFinalTotal - $fTotalDiscount);
                }
                else if ($selectedTipPos == 2) {
                    $tipAmount = ($TIP_AMOUNT_2 / 100) * ($fFinalTotal - $fTotalDiscount);
                }
                else if ($selectedTipPos == 3) {
                    $tipAmount = ($TIP_AMOUNT_3 / 100) * ($fFinalTotal - $fTotalDiscount);
                }
            }
            /* Delivery Tip End */
        }
        // # Calculate Order Delivery Charge ##
        $TaxArr = getMemberCountryTax($iUserId, "Passenger");
        if ($isStoreKiosk == "Yes") {
            $TaxArr = getMemberCountryTax($iCompanyId, "Company");
        }
        $fTax = $TaxArr['fTax1'];
        if ($fTax > 0) {
            $ftaxamount = $fFinalTotal - $fTotalDiscount + $fPackingCharge;
            $fTax = setTwoDecimalPoint((($ftaxamount * $fTax) / 100));
        }
        $fCommision = $ADMIN_COMMISSION;
        $fNetTotal = $fFinalTotal + $fPackingCharge + $fDeliveryCharge + $fTax - $fTotalDiscount;
        $fTotalGenerateFare = $fNetTotal;
        $fOrderFare_For_Commission = $fFinalTotal;
        $fCommision = setTwoDecimalPoint((($fOrderFare_For_Commission * $fCommision) / 100));
        /* Check Coupon Code For Count Total Fare Start */
        $discountValue = 0;
        $discountValueType = "cash";
        $discountApplied = "No";
        $userAddressData = $obj->MySQLSelect("SELECT * FROM user_address WHERE iUserAddressId = '$iUserAddressId'");
        $userAddressLatitude = $userAddressData[0]['vLatitude'];
        $userAddressLongitude = $userAddressData[0]['vLongitude'];
        $db_companydata1 = $obj->MySQLSelect("SELECT vRestuarantLocationLat, vRestuarantLocationLong FROM company WHERE iCompanyId = '$iCompanyId'");
        $User_Address_Array = array($userAddressLatitude, $userAddressLongitude);
        $Rest_Address_Array = array($db_companydata1[0]['vRestuarantLocationLat'], $db_companydata1[0]['vRestuarantLocationLong']);
        $iLocationIdUser = GetUserGeoLocationIdPromoCode($User_Address_Array);
        $iLocationIdRest = GetUserGeoLocationIdPromoCode($Rest_Address_Array);
        if ($couponCode != '') {
            //Added By HJ On 18-01-2019 For Check and Get Active Coupon Data Start
            $getCouponCode = $obj->MySQLSelect("SELECT fDiscount,eType,eStoreType,iCompanyId,eFreeDelivery,iLocationId FROM coupon WHERE vCouponCode='" . $couponCode . "' AND eStatus='Active'");
            if (count($getCouponCode) > 0) {
                $discountValue = $getCouponCode[0]['fDiscount'];
                $discountValueType = $getCouponCode[0]['eType'];
                $Data['eFreeDelivery'] = "No";
                if ($MODULES_OBJ->isEnableFreeDeliveryOrStoreSpecificPromoCode() && !empty($getCouponCode[0]['eStoreType'])) {
                    if ($eTakeAway == "No") {
                        if ($getCouponCode[0]['eStoreType'] == "All") {
                            if ($getCouponCode[0]['eFreeDelivery'] == "Yes") {
                                $Data['eFreeDelivery'] = "Yes";
                                $fNetTotal = $fNetTotal - $fDeliveryCharge;
                                $fDeliveryCharge = 0;
                            }
                            $discountValue = $getCouponCode[0]['fDiscount'];
                            $discountValueType = $getCouponCode[0]['eType'];
                            $discountApplied = "Yes";
                        }
                        else {
                            if ($getCouponCode[0]['iCompanyId'] == $iCompanyId) {
                                if ($getCouponCode[0]['eFreeDelivery'] == "Yes") {
                                    $Data['eFreeDelivery'] = "Yes";
                                    $fNetTotal = $fNetTotal - $fDeliveryCharge;
                                    $fDeliveryCharge = 0;
                                }
                                $discountValue = $getCouponCode[0]['fDiscount'];
                                $discountValueType = $getCouponCode[0]['eType'];
                                $discountApplied = "Yes";
                            }
                        }
                    }
                    if ($MODULES_OBJ->isEnableLocationWisePromoCode() && $getCouponCode[0]['iLocationId'] > 0) {
                        $discountApplied = "Yes";
                        if ($eTakeAway == "No") {
                            if (in_array($getCouponCode[0]['iLocationId'], $iLocationIdUser) && in_array($getCouponCode[0]['iLocationId'], $iLocationIdRest)) {
                                $discountValue = $getCouponCode[0]['fDiscount'];
                                $discountValueType = $getCouponCode[0]['eType'];
                            }
                            else {
                                $discountValue = 0;
                                $discountValueType = "cash";
                            }
                        }
                        else {
                            if (in_array($getCouponCode[0]['iLocationId'], $iLocationIdRest)) {
                                $discountValue = $getCouponCode[0]['fDiscount'];
                                $discountValueType = $getCouponCode[0]['eType'];
                            }
                            else {
                                $discountValue = 0;
                                $discountValueType = "cash";
                            }
                        }
                    }
                }
                if ($MODULES_OBJ->isEnableLocationWisePromoCode() && $getCouponCode[0]['iLocationId'] > 0 && $discountApplied == "No") {
                    if ($eTakeAway == "No") {
                        if (in_array($getCouponCode[0]['iLocationId'], $iLocationIdUser) && in_array($getCouponCode[0]['iLocationId'], $iLocationIdRest)) {
                            $discountValue = $getCouponCode[0]['fDiscount'];
                            $discountValueType = $getCouponCode[0]['eType'];
                            $discountApplied = "Yes";
                        }
                    }
                    else {
                        if (in_array($getCouponCode[0]['iLocationId'], $iLocationIdRest)) {
                            $discountValue = $getCouponCode[0]['fDiscount'];
                            $discountValueType = $getCouponCode[0]['eType'];
                            $discountApplied = "Yes";
                        }
                    }
                }
                if (!($MODULES_OBJ->isEnableFreeDeliveryOrStoreSpecificPromoCode() && $MODULES_OBJ->isEnableLocationWisePromoCode())) {
                    $discountValue = $getCouponCode[0]['fDiscount'];
                    $discountValueType = $getCouponCode[0]['eType'];
                }
            }
            //Added By HJ On 18-01-2019 For Check and Get Active Coupon Data End
            //$discountValue = get_value('coupon', 'fDiscount', 'vCouponCode', $couponCode, '', 'true'); //Commented By HJ On 18-01-2019
            //$discountValue = setTwoDecimalPoint($discountValue * $Ratio);
            //$discountValueType = get_value('coupon', 'eType', 'vCouponCode', $couponCode, '', 'true'); //Commented By HJ On 18-01-2019
            if ($discountValueType == "percentage") {
                $discountValue = setTwoDecimalPoint($discountValue);
            }
            else {
                $discountValue = setTwoDecimalPoint($discountValue * $Ratio);
            }
        }
        if ($couponCode != '' && $discountValue != 0) {
            if ($discountValueType == "percentage") {
                $discountApplyOn = $fNetTotal - $fDeliveryCharge - $fTax; // Added By HJ On 27-06-2019 As Per Discuss With BM Mam
                $vDiscount = setTwoDecimalPoint($discountValue, 1) . ' ' . "%";
                $discountValue = setTwoDecimalPoint($discountApplyOn * $discountValue) / 100;
            }
            else {
                $curr_sym = get_value('currency', 'vSymbol', 'eDefault', 'Yes', '', 'true');
                if ($discountValue > $fNetTotal) {
                    $vDiscount = setTwoDecimalPoint($fNetTotal) . ' ' . $curr_sym;
                }
                else {
                    $vDiscount = setTwoDecimalPoint($discountValue) . ' ' . $curr_sym;
                }
            }
            $fNetTotal = $fNetTotal - $discountValue;
            if ($fNetTotal < 0) {
                $fNetTotal = $fTotalGenerateFare = 0;
                // $discountValue = $fNetTotal;
            }
            $fTotalGenerateFare = $fNetTotal;
            $Order_data[0]['fDiscount'] = $discountValue;
            $Order_data[0]['vDiscount'] = $vDiscount;
        }
        /* Check Coupon Code Total Fare  End */
        $fTotalGenerateFare = $fNetTotal = $fNetTotal + $tipAmount;
        /* Checking For Passenger Outstanding Amount */
        $fOutStandingAmount = 0;
        if ($isStoreKiosk == "No") {
            $fOutStandingAmount = GetPassengerOutstandingAmount($iUserId);
        }

        $fOutStandingAmount = setTwoDecimalPoint($fOutStandingAmount * $Ratio);
        $Data['fOutStandingAmountWithSymbol'] = formateNumAsPerCurrency($fOutStandingAmount, $currencycode);

        if(strtoupper($isAddOutstandingAmt) == "NO") {
            $fOutStandingAmount = 0;
        }
        
        if ($fOutStandingAmount > 0) {
            $fNetTotal = $fNetTotal + $fOutStandingAmount;
            $fTotalGenerateFare = $fTotalGenerateFare + $fOutStandingAmount;
        }
        /* Checking For Passenger Outstanding Amount */
        /* Check debit wallet For Count Total Order Fare Start */
        $user_wallet_debit_amount = 0;
        $DisplayCardPayment = "Yes";
        if ($iUserId > 0 && $CheckUserWallet == "Yes") {
            $user_available_balance = $WALLET_OBJ->FetchMemberWalletBalance($iUserId, "Rider");
            $user_available_balance = setTwoDecimalPoint($user_available_balance * $Ratio);
            if ($fNetTotal > $user_available_balance) {
                $fNetTotal = $fNetTotal - $user_available_balance;
                $user_wallet_debit_amount = $user_available_balance;
                $fTotalGenerateFare = $fNetTotal;
                $DisplayCardPayment = "Yes";
            }
            else {
                $user_wallet_debit_amount = ($fNetTotal > 0) ? $fNetTotal : 0;
                $fNetTotal = 0;
                $fTotalGenerateFare = $fNetTotal;
                $DisplayCardPayment = "No";
            }
        }
        /* Check debit wallet For Count Total Order Fare End */
        if ($fNetTotal < 0) {
            $fNetTotal = $fTotalGenerateFare = 0;
        }
        #############################
        //added by SP on 15-11-2019 for rounding off start
        //$currData = $obj->MySQLSelect("SELECT cu.vName, cu.iCurrencyId, cu.eRoundingOffEnable, cu.Ratio FROM register_user AS ru LEFT JOIN currency AS cu ON ru.vCurrencyPassenger = cu.vName WHERE ru.iUserId = '" . $iUserId . "'");
        $currData = $userCurrencyData;
        $vCurrency = $currData[0]['vName'];
        $userCurrencyRatio = $currData[0]['Ratio'];
        if ($currData[0]['eRoundingOffEnable'] == "Yes" && $MODULES_OBJ->isEnableRoundingMethod()) {
            //$userCurrencyRatio = get_value('currency', 'Ratio', 'vName', $vCurrency, '', 'true');
            //  * $userCurrencyRatio
            $roundingOffTotal_fare_amountArr = getRoundingOffAmount($fNetTotal, $vCurrency);
            $fNetTotal = $fTotalGenerateFare = $roundingOffTotal_fare_amountArr['finalFareValue'];
            $eRoundingType = "Substraction";
            if ($roundingOffTotal_fare_amountArr['method'] == "Addition") {
                $eRoundingType = "Addition";
            }
            $fRoundingAmount = setTwoDecimalPoint($roundingOffTotal_fare_amountArr['differenceValue']);
            $fRoundingAmount = $fRoundingAmount;
            $eRoundingType = $eRoundingType;
        }
        $Data['fSubTotal'] = formateNumAsPerCurrency($fFinalTotal, $currencycode);
        $Data['fSubTotalamount'] = $fFinalTotal;
        $Data['fTotalDiscount'] = formateNumAsPerCurrency($fTotalDiscount, $currencycode);
        $fPackingCharge = setTwoDecimalPoint($fPackingCharge);
        $Data['fPackingCharge'] = ($fPackingCharge > 0) ? formateNumAsPerCurrency($fPackingCharge, $currencycode) : formateNumAsPerCurrency(0, $currencycode);
        $fDeliveryCharge = setTwoDecimalPoint($fDeliveryCharge);
        $Data['fDeliveryCharge'] = ($fDeliveryCharge > 0) ? formateNumAsPerCurrency($fDeliveryCharge, $currencycode) : formateNumAsPerCurrency(0, $currencycode);
        $fTax = setTwoDecimalPoint($fTax);
        $Data['fTax'] = ($fTax > 0) ? formateNumAsPerCurrency($fTax, $currencycode) : formateNumAsPerCurrency(0, $currencycode);
        $fDiscount_Val = 0;
        if (isset($Order_data[0]['fDiscount']) && $Order_data[0]['fDiscount'] > 0) {
            $fDiscount_Val = setTwoDecimalPoint($Order_data[0]['fDiscount']);
        }
        //$Data['fDiscount'] = ($fDiscount_Val > 0) ? formateNumAsPerCurrency($fDiscount_Val,$currencycode) : formateNumAsPerCurrency(0,$currencycode);
        $Data['fDiscount'] = ($fDiscount_Val > 0) ? $fDiscount_Val : 0;//added by SP on 03-09-2020 becoz in app it needs without currency symbol..
        // $Data['vDiscount'] = $Order_data[0]['vDiscount'];
        $fCommision = setTwoDecimalPoint($fCommision);
        $Data['fCommision'] = ($fCommision > 0) ? formateNumAsPerCurrency($fCommision, $currencycode) : formateNumAsPerCurrency(0, $currencycode);
        $Data['fTipAmount'] = $tipAmount;
        $fNetTotal = setTwoDecimalPoint($fNetTotal);
        $Data['fNetTotal'] = ($fNetTotal > 0) ? formateNumAsPerCurrency($fNetTotal, $currencycode) : formateNumAsPerCurrency(0, $currencycode);
        $Data['fNetTotalAmount'] = $fNetTotal;
        $fTotalGenerateFare = setTwoDecimalPoint($fTotalGenerateFare);
        $Data['fTotalGenerateFare'] = ($fTotalGenerateFare > 0) ? formateNumAsPerCurrency($fTotalGenerateFare, $currencycode) : formateNumAsPerCurrency(0, $currencycode);
        $Data['fTotalGenerateFareAmount'] = $fTotalGenerateFare;
        $Data['fOutStandingAmount'] = ($fOutStandingAmount > 0) ? formateNumAsPerCurrency($fOutStandingAmount, $currencycode) : formateNumAsPerCurrency(0, $currencycode);
        $Data['fWalletDebit'] = ($user_wallet_debit_amount > 0) ? formateNumAsPerCurrency($user_wallet_debit_amount, $currencycode) : formateNumAsPerCurrency(0, $currencycode);
        $Data['user_wallet_debit_amount'] = $user_wallet_debit_amount;
        $Data['currencySymbol'] = $currencySymbol;
        $Data['DisplayCardPayment'] = $DisplayCardPayment;
        $Data['DisplayUserWalletDebitAmount'] = ($user_wallet_debit_amount > 0) ? formateNumAsPerCurrency($user_wallet_debit_amount, $currencycode) : "";
        //$Data['DISABLE_CASH_PAYMENT_OPTION'] = ($fOutStandingAmount > 0) ? "Yes" : "No";
        // if ($SYSTEM_PAYMENT_FLOW == 'Method-2' || $SYSTEM_PAYMENT_FLOW == 'Method-3') {
        $outStandingSql = "";
        if (strtoupper($ePayWallet) == "YES") {
            $outStandingSql = " AND eAuthoriseIdName='No' AND iAuthoriseId ='0'";
        }
        $counttripData = $obj->MySQLSelect("SELECT count(iTripOutstandId) as counttrip FROM trip_outstanding_amount WHERE iUserId='" . $iUserId . "' AND iUserId > 0 AND ePaidByPassenger = 'No' $outStandingSql");
        $Data['DISABLE_CASH_PAYMENT_OPTION'] = 'No';
        if ($MODULES_OBJ->isEnableOutstandingRestriction()) {
            if ($fOutStandingAmount > 0 && $counttripData[0]['counttrip'] >= $OUTSTANDING_ALLOW_TRIP_COUNT) {
                $Data['DISABLE_CASH_PAYMENT_OPTION'] = "Yes";
            }
        }
        else {
            if ($fOutStandingAmount > 0) {
                $Data['DISABLE_CASH_PAYMENT_OPTION'] = "Yes";
            }
        }
        $OrderFareDetailsArr = $OrderFareDetailsArrNew = array();
        if ($fFinalTotal > 0) {
            $OrderFareDetailsArr[][$languageLabelsArr['LBL_BILL_SUB_TOTAL']] = $Data['fSubTotal'];
            $OrderFareDetailsArrNew[][$languageLabelsArr['LBL_BILL_SUB_TOTAL']] = $Data['fSubTotal'];
            $OrderFareDetailsArr[]['eDisplaySeperator'] = 'Yes';
            $OrderFareDetailsArrNew[]['eDisplaySeperator'] = 'Yes';
        }
        if ($fTotalDiscount > 0) {
            $OrderFareDetailsArr[][$languageLabelsArr['LBL_OFFERS_DISCOUNT_TXT']] = "- " . formateNumAsPerCurrency($fTotalDiscount, $currencycode);
            $OrderFareDetailsArrNew[][$languageLabelsArr['LBL_OFFERS_DISCOUNT_TXT']] = "- " . formateNumAsPerCurrency($fTotalDiscount, $currencycode);
            $OrderFareDetailsArr[]['eDisplaySeperator'] = 'Yes';
            $OrderFareDetailsArrNew[]['eDisplaySeperator'] = 'Yes';
        }
        if ($fPackingCharge > 0) {
            $OrderFareDetailsArr[][$languageLabelsArr['LBL_PACKING_CHARGE']] = formateNumAsPerCurrency($fPackingCharge, $currencycode);
            $OrderFareDetailsArrNew[][$languageLabelsArr['LBL_PACKING_CHARGE']] = formateNumAsPerCurrency($fPackingCharge, $currencycode);
            $OrderFareDetailsArr[]['eDisplaySeperator'] = 'Yes';
            $OrderFareDetailsArrNew[]['eDisplaySeperator'] = 'Yes';
        }
        if ($fDeliveryCharge > 0) {
            $OrderFareDetailsArr[][$languageLabelsArr['LBL_DELIVERY_CHARGES_TXT']] = formateNumAsPerCurrency($fDeliveryCharge, $currencycode);
            $OrderFareDetailsArrNew[][$languageLabelsArr['LBL_DELIVERY_CHARGES_TXT']] = formateNumAsPerCurrency($fDeliveryCharge, $currencycode);
            $OrderFareDetailsArr[]['eDisplaySeperator'] = 'Yes';
            $OrderFareDetailsArrNew[]['eDisplaySeperator'] = 'Yes';
        }
        if ($fTax > 0) {
            $OrderFareDetailsArr[][$languageLabelsArr['LBL_TOTAL_TAX_TXT']] = formateNumAsPerCurrency($fTax, $currencycode);
            $OrderFareDetailsArrNew[][$languageLabelsArr['LBL_TOTAL_TAX_TXT']] = formateNumAsPerCurrency($fTax, $currencycode);
            $OrderFareDetailsArr[]['eDisplaySeperator'] = 'Yes';
            $OrderFareDetailsArrNew[]['eDisplaySeperator'] = 'Yes';
        }
        if ($fOutStandingAmount > 0) {
            $OrderFareDetailsArr[][$languageLabelsArr['LBL_OUTSTANDING_AMOUNT_TXT']] = formateNumAsPerCurrency($fOutStandingAmount, $currencycode);
            $OrderFareDetailsArrNew[][$languageLabelsArr['LBL_OUTSTANDING_AMOUNT_TXT']] = formateNumAsPerCurrency($fOutStandingAmount, $currencycode);
            $OrderFareDetailsArr[]['eDisplaySeperator'] = 'Yes';
            $OrderFareDetailsArrNew[]['eDisplaySeperator'] = 'Yes';
        }
        if ($fDiscount_Val > 0) {
            $OrderFareDetailsArr[][$languageLabelsArr['LBL_DISCOUNT_TXT']] = "- " . formateNumAsPerCurrency($fDiscount_Val, $currencycode);
            $OrderFareDetailsArrNew[][$languageLabelsArr['LBL_DISCOUNT_TXT']] = "- " . formateNumAsPerCurrency($fDiscount_Val, $currencycode);
            $OrderFareDetailsArr[]['eDisplaySeperator'] = 'Yes';
            $OrderFareDetailsArrNew[]['eDisplaySeperator'] = 'Yes';
        }
        if ($user_wallet_debit_amount > 0) {
            $OrderFareDetailsArr[][$languageLabelsArr['LBL_WALLET_ADJUSTMENT']] = "- " . formateNumAsPerCurrency($user_wallet_debit_amount, $currencycode);
            $OrderFareDetailsArrNew[][$languageLabelsArr['LBL_WALLET_ADJUSTMENT']] = "- " . formateNumAsPerCurrency($user_wallet_debit_amount, $currencycode);
            $OrderFareDetailsArr[]['eDisplaySeperator'] = 'Yes';
            $OrderFareDetailsArrNew[]['eDisplaySeperator'] = 'Yes';
        }
        if ($tipAmount > 0) {
            $OrderFareDetailsArr[][$languageLabelsArr['LBL_TIP_AMOUNT']] = formateNumAsPerCurrency($tipAmount, $currencycode);
            $OrderFareDetailsArrNew[][$languageLabelsArr['LBL_TIP_AMOUNT']] = formateNumAsPerCurrency($tipAmount, $currencycode);
            $OrderFareDetailsArr[]['eDisplaySeperator'] = 'Yes';
            $OrderFareDetailsArrNew[]['eDisplaySeperator'] = 'Yes';
        }
        //added by SP on 15-11-2019 for rounding off start
        //if($currData[0]['eRoundingOffEnable'] == "Yes" && ){
        if (isset($fRoundingAmount) && !empty($fRoundingAmount) && $fRoundingAmount != 0 && $currData[0]['eRoundingOffEnable'] == "Yes" && $MODULES_OBJ->isEnableRoundingMethod()) {
            $fRoundingAmount = $fRoundingAmount;
            $eRoundingType = $eRoundingType;
            if ($eRoundingType == "Addition") {
                $roundingMethod = "";
            }
            else {
                $roundingMethod = "-";
            }
            $rounding_diff = isset($roundingOffTotal_fare_amountArr['differenceValue']) && $roundingOffTotal_fare_amountArr['differenceValue'] != '' ? $roundingOffTotal_fare_amountArr['differenceValue'] : "0.00";
            $OrderFareDetailsArr[][$languageLabelsArr['LBL_ROUNDING_DIFF_TXT']] = $roundingMethod . " " . formateNumAsPerCurrency($fRoundingAmount, $currencycode);
            $OrderFareDetailsArrNew[][$languageLabelsArr['LBL_ROUNDING_DIFF_TXT']] = $roundingMethod . " " . formateNumAsPerCurrency($rounding_diff, $currencycode);
            $OrderFareDetailsArr[]['eDisplaySeperator'] = 'Yes';
            $OrderFareDetailsArrNew[]['eDisplaySeperator'] = 'Yes';
        }
        $OrderFareDetailsArr[][$languageLabelsArr['LBL_TOTAL_BILL_AMOUNT_TXT']] = formateNumAsPerCurrency($fTotalGenerateFare, $currencycode);
        $OrderFareDetailsArrNew[][$languageLabelsArr['LBL_TOTAL_BILL_AMOUNT_TXT']] = formateNumAsPerCurrency($fTotalGenerateFare, $currencycode);
    }
    $restaurant_status_arr = GetStoreWorkingHoursDetails($iCompanyId, $iUserId);
    if ($isStoreKiosk == "Yes") {
        $restaurant_status_arr = GetStoreWorkingHoursDetails($iCompanyId, "", $vLang);
    }
    $Data['restaurantstatus'] = $restaurant_status_arr['restaurantstatus'];
    $Data['FareDetailsArr'] = $OrderFareDetailsArr;
    $Data['FareDetailsArrNew'] = $OrderFareDetailsArrNew;
    //added by SP for selected user address remove if restaurant is not in that location on 12-08-2019
    if ($restaurantnotavailable == 0) {
        $Data['RestaurantAddressNotMatch'] = "0";
        $Data['RestaurantAddressNotMatchLBL'] = 'LBL_CHANGE_ADDRESS_AVAILABLE_NOTE';
    }
    else {
        $Data['RestaurantAddressNotMatch'] = "";
    }
    //added by SP for selected user address remove if restaurant is not in that location on 12-08-2019
    //$Data['ToTalAddress'] = FetchTotalMemberAddress($iUserId, "Passenger", $passengerLat, $passengerLon, $iCompanyId);
    $Data['ToTalAddress'] = !empty($UserSelectedAddressArr) ? "1" : "0";
    if ($eTakeAway == 'Yes' || $isStoreKiosk == "Yes") {
        $Data['ToTalAddress'] = 1;
    }
    $Data['vCompany'] = $vCompany;
    //added by SP on 9-9-2019 for new design
    $Data['vCaddress'] = $vCaddress;
    $Data['vImage'] = $tconfig['tsite_upload_images_compnay'] . "/" . $iCompanyId . "/" . $vCompanyImage;
    $Data['iMaxItemQty'] = $iMaxItemQty;
    $Data['eTakeaway'] = ($MODULES_OBJ->isTakeAwayEnable() && $db_companydata[0]['eTakeaway'] == 'Yes') ? 'Yes' : 'No';
    $returnArr = $Data;
    $returnArr['DeliveryPreferences']['Enable'] = ($MODULES_OBJ->isDeliveryPreferenceEnable() == true) ? 'Yes' : 'No';
    if ($MODULES_OBJ->isDeliveryPreferenceEnable()) {
        $deliveryPrefSql = "SELECT iPreferenceId,JSON_UNQUOTE(JSON_VALUE(tTitle, '$.tTitle_" . $vLang . "')) as tTitle, JSON_UNQUOTE(JSON_VALUE(tDescription, '$.tDescription_" . $vLang . "')) as tDescription, ePreferenceFor, eImageUpload, iDisplayOrder, eContactLess, eStatus FROM delivery_preferences WHERE eStatus = 'Active' AND is_deleted = 0";
        $deliveryPrefSqlData = $obj->MySQLSelect($deliveryPrefSql);
        foreach ($deliveryPrefSqlData as $pkey => $pref) {
            // if ($APP_PAYMENT_MODE == "Cash" && $pref['eContactLess'] == 'Yes') {
            if ($CONFIG_OBJ->isOnlyCashPaymentModeAvailable() && $pref['eContactLess'] == 'Yes') {
                unset($deliveryPrefSqlData[$pkey]);
            }
            if (($eTakeAway == "Yes" || $isStoreKiosk == "Yes") && $pref['ePreferenceFor'] == 'Provider') {
                unset($deliveryPrefSqlData[$pkey]);
            }
        }
        $deliveryPrefSqlData = array_values($deliveryPrefSqlData);
        if (count($deliveryPrefSqlData) > 0) {
            $returnArr['DeliveryPreferences']['vTitle'] = $languageLabelsArr['LBL_DELIVERY_PREF'];
            $returnArr['DeliveryPreferences']['Data'] = $deliveryPrefSqlData;
        }
        else {
            $returnArr['DeliveryPreferences']['Enable'] = 'No';
        }
    }

    if(empty($iUserId) && $iUserId == 0) {
        $returnArr['DeliveryPreferences']['Enable'] = 'No';
    }
    $returnArr['eShowTerms'] = "No";
    $returnArr['eProofUpload'] = "No";
    $returnArr['tProofNote'] = "";
    $sc_data = $obj->MySQLSelect("SELECT eShowTerms,eProofUpload,JSON_UNQUOTE(JSON_VALUE(tProofNote, '$.tProofNote_" . $vLang . "')) as tProofNote FROM service_categories WHERE iServiceId = $iServiceId");
    if ($MODULES_OBJ->isEnableTermsServiceCategories()) {
        $returnArr['eShowTerms'] = $sc_data[0]['eShowTerms'];
    }
    if ($MODULES_OBJ->isEnableProofUploadServiceCategories() && strtolower($_REQUEST['GeneralUserType']) != "company") {
        $returnArr['eProofUpload'] = $sc_data[0]['eProofUpload'];
        if (is_null($sc_data[0]['tProofNote']) || $sc_data[0]['tProofNote'] == "null") {
            $sc_data[0]['tProofNote'] = "";
        }
        $returnArr['tProofNote'] = $sc_data[0]['tProofNote'];
    }
    /* Get Last order payment method */
    $lastOrderDetails = $obj->MySQLSelect("SELECT iOrderId,ePaymentOption,eCheckUserWallet,fWalletDebit,fNetTotal FROM orders WHERE iUserId = $iUserId AND iStatusCode = 6 ORDER BY iOrderId DESC LIMIT 1");
    $returnArr['lastOrderPaymentMethod'] = $languageLabelsArr['LBL_CASH_TXT'];
    if (!empty($lastOrderDetails) && count($lastOrderDetails) > 0) {
        if ($lastOrderDetails[0]['ePaymentOption'] == "Card") {
            $payment_details = $obj->MySQLSelect("SELECT tPaymentDetails FROM payments WHERE iOrderId = '" . $lastOrderDetails[0]['iOrderId'] . "' AND eEvent = 'OrderPayment'");
            $tPaymentDetails = json_decode($payment_details[0]['tPaymentDetails'], true);
            if (isset($tPaymentDetails['CARD_TOKEN'])) {
                $card_details = $obj->MySQLSelect("SELECT tCardNum FROM user_payment_info WHERE tCardToken = '" . $tPaymentDetails['CARD_TOKEN'] . "' AND eStatus = 'Active' ");
            }
            else {
                $card_details = $obj->MySQLSelect("SELECT tCardNum FROM user_payment_info WHERE iMemberId = " . $iUserId . " AND eUserType = 'Passenger' AND vPaymentMethod = '" . $APP_PAYMENT_METHOD . "' AND eStatus = 'Active' AND eDefault = 'Yes'");
            }
            if (!empty($card_details) && count($card_details) > 0) {
                $returnArr['lastOrderPaymentMethod'] = str_replace(" ", "", $card_details[0]['tCardNum']);
                $returnArr['lastOrderPaymentMethod'] = substr($returnArr['lastOrderPaymentMethod'], -6);
            }
        }
    }
    /* Get Last order payment method End */
    $returnArr['Action'] = "1";
    if (!empty($iUserId)) {
        $returnArr['message'] = getPassengerDetailInfo($iUserId, "", ""); // Added By HJ On 08-11-2019 As Per Dicuss WIth KS and DT
    }
    setDataResponse($returnArr);
}
// ############################ Calculate Order Estimate Amount ###################################################################
// ############################# Display User's Active Orders ###################################################################
if ($type == "DisplayActiveOrder") {
    $page = isset($_REQUEST['page']) ? trim($_REQUEST['page']) : 1;
    $iUserId = isset($_REQUEST["GeneralMemberId"]) ? $_REQUEST["GeneralMemberId"] : '';
    $UserType = isset($_REQUEST["GeneralUserType"]) ? $_REQUEST["GeneralUserType"] : 'Passenger'; // Passenger, Driver , Company
    $vSubFilterParam = isset($_REQUEST["vSubFilterParam"]) ? $_REQUEST["vSubFilterParam"] : "";
    $per_page = 10;
    if ($UserType == "Passenger") {
        $tblname = "register_user";
        $iMemberId = 'ord.iUserId';
        $UserDetailsArr = getUserCurrencyLanguageDetails($iUserId);
    }
    else if ($UserType == "Driver") {
        $tblname = "register_driver";
        $iMemberId = 'ord.iDriverId';
        $UserDetailsArr = getDriverCurrencyLanguageDetails($iUserId);
    }
    else {
        $tblname = "company";
        $iMemberId = 'ord.iCompanyId';
        $UserDetailsArr = getCompanyCurrencyLanguageDetails($iUserId);
    }
    $enable_takeaway = 0;
    if ($MODULES_OBJ->isTakeAwayEnable()) {
        $enable_takeaway = 1;
    }
    $eBuyAnyService = 0;
    if ($MODULES_OBJ->isEnableAnywhereDeliveryFeature()) {
        $eBuyAnyService = 1;
    }
    $whereStatusCode = "  AND ord.iStatusCode NOT IN(12)";
    if ($vSubFilterParam != "") {
        if ($enable_takeaway == 1) {
            if ($vSubFilterParam == '6-1') {
                $whereStatusCode = " AND ord.iStatusCode IN (6) AND ord.eTakeaway = 'Yes'";
            }
            else if ($vSubFilterParam == '6') {
                $whereStatusCode = " AND ord.iStatusCode IN ($vSubFilterParam) AND ord.eTakeaway = 'No'";
            }
            else if ($vSubFilterParam == '8') {
                $whereStatusCode = " AND ord.iStatusCode IN (8,9)";
            }
            else if ($vSubFilterParam == '5') {
                $whereStatusCode = " AND ord.iStatusCode IN (5)";
            } else if ($vSubFilterParam == '9') {
                $whereStatusCode = " AND ord.iStatusCode IN (9)";
            }
            else {
                $whereStatusCode = " AND ord.iStatusCode IN ($vSubFilterParam)";
                if ($eBuyAnyService == 1) {
                    $vSubFilterParam1 = '1,2';
                    $whereStatusCode = " AND ord.iStatusCode IN ($vSubFilterParam1)";
                }
            }
        }
        else if ($vSubFilterParam == '8') {
            $whereStatusCode = " AND ord.iStatusCode IN (8,9)";
        }  else if ($vSubFilterParam == '9') {
            $whereStatusCode = " AND ord.iStatusCode IN (9)";
        }
        else {
            $whereStatusCode = " AND ord.iStatusCode IN ($vSubFilterParam)";
            if ($eBuyAnyService == 1) {
                $vSubFilterParam1 = '1,2';
                $whereStatusCode = " AND ord.iStatusCode IN ($vSubFilterParam1)";
            }
        }
    }
    $filterSelected = "All";
    if ($vSubFilterParam != "") {
        $filterSelected = $vSubFilterParam;
    }
    $filterSelected = $vSubFilterParam;
    $Ratio = $UserDetailsArr['Ratio'];
    $currencySymbol = $UserDetailsArr['currencySymbol'];
    $currencycode = $UserDetailsArr['currencycode'];
    $vLang = $UserDetailsArr['vLang'];
    $data_count_all = $obj->MySQLSelect("select COUNT(ord.iOrderId) As TotalIds from orders as ord where $iMemberId = '" . $iUserId . "' AND ord.iStatusCode NOT IN(12) ORDER BY ord.iOrderId DESC");
    $TotalPages = 0;
    if (isset($data_count_all[0]['TotalIds'])) {
        $TotalPages += ceil($data_count_all[0]['TotalIds'] / $per_page);
    }
    $start_limit = ($page - 1) * $per_page;
    $limit = " LIMIT " . $start_limit . ", " . $per_page;
    $sql = "select co.vDemoStoreImage,co.vCompany,co.iServiceId,sc.vServiceName_" . $vLang . " as vServiceCategoryName, co.vCaddress as vRestuarantLocation,co.vImage,ord.iOrderId,ord.tOrderRequestDate,ord.fNetTotal,ord.iCompanyId,ord.iStatusCode,ord.vOrderNo,ord.fRoundingAmount,ord.eRoundingType,ord.eTakeaway,ord.eBuyAnyService,eForPickDropGenie,ord.fWalletDebit,ord.eAskCodeToUser,ord.vRandomCode,ord.fDeliveryCharge,ord.fOutStandingAmount, ord.genieUserApproved, ord.iDriverId from orders as ord LEFT JOIN company as co ON ord.iCompanyId=co.iCompanyId LEFT JOIN service_categories as sc on sc.iServiceId=co.iServiceId where $iMemberId = '" . $iUserId . "' $whereStatusCode ORDER BY ord.iOrderId DESC" . $limit; //added by SP on 01-10-2019 for cubex design
    $data_order = $obj->MySQLSelect($sql);

    $sql_rating = "select co.vDemoStoreImage,co.vCompany,co.iServiceId,sc.vServiceName_" . $vLang . " as vServiceCategoryName, co.vCaddress as vRestuarantLocation,co.vImage,ord.iOrderId,ord.tOrderRequestDate,ord.fNetTotal,ord.iCompanyId,ord.iStatusCode,ord.vOrderNo from orders as ord LEFT JOIN company as co ON ord.iCompanyId=co.iCompanyId LEFT JOIN service_categories as sc on sc.iServiceId=co.iServiceId where $iMemberId = '" . $iUserId . "' $whereStatusCode ORDER BY ord.iOrderId DESC" . $limit; //added by SP on 01-10-2019 for cubex design
    $data_order_rating = $obj->MySQLSelect($sql_rating);
    $orderIds_rating = "";
    for ($s = 0; $s < count($data_order_rating); $s++) {
        $orderIds_rating .= "'" . $data_order_rating[$s]['iOrderId'] . "',";
    }
    $getDriverRateData = $obj->MySQLSelect("SELECT SUM(vRating1) vRating1 FROM ratings_user_driver WHERE eUserType='Passenger' AND iOrderId IN ($orderIds_rating) ");
    $totalDriverRate = $driverAvgRate_new = 0;
    if (count($getDriverRateData) > 0) {
        $totalDriverRate = $getDriverRateData[0]['vRating1'];
    }
    if (count($data_order) > 0) {
        $driverAvgRate_new = $totalDriverRate / count($data_order);
    }
    $driverAvgRate_new = round($driverAvgRate_new, 1);
    $serverTimeZone = date_default_timezone_get();
    $appTypeFilterArr = AppTypeFilterArr($iUserId, $UserType, $vLang);
    $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLang, "1", $iServiceId);
    $returnData['AppTypeFilterArr'] = $appTypeFilterArr;
    $takeaway_orderstaus = '';
    if ($enable_takeaway == 0) {
        $takeaway_orderstaus = " AND eTakeaway != 'Yes'";
    }
    $eBuyAnyService_orderstaus = " AND eBuyAnyService != 'Yes'";
    if ($eBuyAnyService == 1) {
        $eBuyAnyService_orderstaus = " AND IF(eBuyAnyService = 'Yes' && iStatusCode IN (1,6,8), eBuyAnyService = 'Yes', eBuyAnyService = 'No' AND iStatusCode != '1')";
    }
    $getOrderStatus = $obj->MySQLSelect("SELECT vStatus_" . $vLang . ",vStatus_Track,iStatusCode,eTakeaway FROM order_status WHERE iStatusCode IN (1,6,8,9)" . $takeaway_orderstaus . $eBuyAnyService_orderstaus . " ORDER BY iStatusCode");

    $returnArr['orderStatusFilter'] = $getOrderStatus;
    $appTypeFilterArr = $optionArr = array();
    $optionArr[] = array("vSubFilterParam" => "", "vTitle" => $languageLabelsArr['LBL_ALL']);
    for ($d = 0; $d < count($getOrderStatus); $d++) {
        $statusArr = array();
        //$statusArr['vSubFilterParam'] = $getOrderStatus[$d]['iStatusCode'];
        if ($getOrderStatus[$d]['iStatusCode'] == 6 && $getOrderStatus[$d]['eTakeaway'] == 'Yes' && $enable_takeaway == 1) {
            $statusArr['vSubFilterParam'] = "6-1";
        }
        else {
            $statusArr['vSubFilterParam'] = $getOrderStatus[$d]['iStatusCode'];
        }
        $statusArr['vTitle'] = $getOrderStatus[$d]['vStatus_' . $vLang];
        if($getOrderStatus[$d]['iStatusCode'] == 9){
            $statusArr['vTitle'] = $getOrderStatus[$d]['vStatus_Track'];
        }
        $optionArr[] = $statusArr;
    }
    $returnArr['subFilterOption'] = $optionArr;
    if (count($data_order) > 0) {
        //$seviceCategoriescount = getServiceCategoryCounts();//commented bc for grocery it takes from conf file not from db..bc in table all entries are there..
        $seviceCategoriescount = count($service_categories_ids_arr);
        $orderIds = "";
        for ($s = 0; $s < count($data_order); $s++) {
            $orderIds .= "'" . $data_order[$s]['iOrderId'] . "',";
        }
        $orderStatusArr = array();
        if ($orderIds != "") {
            $orderIds = trim($orderIds, ",");
            $OrderStatus = $obj->MySQLSelect("SELECT os.vStatus_Track,ord.iOrderId FROM order_status as os LEFT JOIN orders as ord ON os.iStatusCode = ord.iStatusCode WHERE ord.iOrderId IN ($orderIds)");
            for ($d = 0; $d < count($OrderStatus); $d++) {
                $ordStatus = $OrderStatus[$d]['vStatus_Track'];
                $orderIds = $OrderStatus[$d]['iOrderId'];
                $orderStatusArr[$orderIds] = $ordStatus;
            }
            $getDriverRateData = $obj->MySQLSelect("SELECT SUM(vRating1) vRating1 FROM ratings_user_driver WHERE eUserType='Passenger' AND iOrderId IN ($orderIds) ");
            $totalDriverRate = $driverAvgRate = 0;
            if (count($getDriverRateData) > 0) {
                $totalDriverRate = $getDriverRateData[0]['vRating1'];
            }
            if (count($data_order) > 0) {
                $driverAvgRate = $totalDriverRate / count($data_order);
            }
            $driverAvgRate = round($driverAvgRate, 1);
            for ($i = 0; $i < count($data_order); $i++) {
                $iCompanyId = $data_order[$i]['iCompanyId'];
                $Photo_Gallery_folder = $tconfig['tsite_upload_images_compnay'] . "/" . $iCompanyId . "/3_";
                if ($data_order[$i]['vImage'] != "") {
                    $data_order[$i]['vImage'] = $Photo_Gallery_folder . $data_order[$i]['vImage'];
                }
                //Added By HJ On 26-06-2019 For Get And Display Store Demo Image Start
                if (isset($data_order[$i]['vDemoStoreImage']) && $data_order[$i]['vDemoStoreImage'] != "" && SITE_TYPE == "Demo") {
                    $demoImgPath = $tconfig['tsite_upload_demo_compnay_doc_path'] . $data_order[$i]['vDemoStoreImage'];
                    if (file_exists($demoImgPath)) {
                        $demoImgUrl = $tconfig['tsite_upload_demo_compnay_doc'] . $data_order[$i]['vDemoStoreImage'];
                        $data_order[$i]['vImage'] = $demoImgUrl;
                    }
                }
                //Added By HJ On 26-06-2019 For Get And Display Store Demo Image End
                if ($seviceCategoriescount > 1) {
                    $data_order[$i]['vServiceCategoryName'] = $data_order[$i]['vServiceCategoryName'];
                    $data_order[$i]['vServiceCategoryName_e'] = $data_order[$i]['vServiceCategoryName'];
                }
                else {
                    $data_order[$i]['vServiceCategoryName'] = '';
                }
                if ($data_order[$i]['eBuyAnyService'] == "Yes") {
                    $data_order[$i]['vServiceCategoryName'] = $languageLabelsArr["LBL_OTHER_DELIVERY"];
                    if ($data_order[$i]['eForPickDropGenie'] == "Yes") {
                        $data_order[$i]['vServiceCategoryName'] = $languageLabelsArr["LBL_RUNNER"];
                    }
                }
                // $fNetTotal = round($fNetTotal*$Ratio,2);
                // $data_order[$i]['fNetTotal'] = $currencySymbol." ".$fNetTotal;
                //added by SP on 19-01-2021 by solving issue#1935, full wallet adjustment then in list it will be shown 0 so its wrong.
                $fNetTotal = $data_order[$i]['fNetTotal'] + $data_order[$i]['fWalletDebit'];
                //$fNetTotal_Arr = getPriceUserCurrency($iUserId, $UserType, $fNetTotal, $data_order[$i]['iOrderId']);
                $fPrice = setTwoDecimalPoint($fNetTotal * $Ratio);
                if ($UserType == "Passenger") {
                    if ($data_order[$i]['eBuyAnyService'] == "Yes" && $data_order[$i]['genieUserApproved'] == "No") {
                        $fPrice = ($data_order[$i]['fDeliveryCharge'] + $data_order[$i]['fOutStandingAmount']) * $Ratio;
                    }
                    $sqlp = "SELECT cu.vName, cu.iCurrencyId, cu.eRoundingOffEnable,cu.ratio,ru.vCurrencyPassenger FROM register_user AS ru LEFT JOIN currency AS cu ON ru.vCurrencyPassenger = cu.vName WHERE ru.iUserId = '" . $iUserId . "'";
                    $currData = $obj->MySQLSelect($sqlp);
                    $vCurrency = $currData[0]['vName'];
                    $query = "SELECT vCurrencyDriver,vCurrencyPassenger FROM `trips` WHERE iOrderId = '" . $data_order[$i]['iOrderId'] . "'";
                    $TripsData = $obj->MySQLSelect($query);
                    if (isset($data_order[$i]['fRoundingAmount']) && !empty($data_order[$i]['fRoundingAmount']) && $data_order[$i]['fRoundingAmount'] != 0 && $TripsData[0]['vCurrencyPassenger'] == $currData[0]['vCurrencyPassenger'] && $currData[0]['eRoundingOffEnable'] == "Yes" && $MODULES_OBJ->isEnableRoundingMethod()) {
                        $roundingOffTotal_fare_amountArr['method'] = $data_order[$i]['eRoundingType'];
                        $roundingOffTotal_fare_amountArr['differenceValue'] = $data_order[$i]['fRoundingAmount'];
                        $roundingOffTotal_fare_amountArr = getRoundingOffAmounttrip($fPrice, $data_order[$i]['fRoundingAmount'], $data_order[$i]['eRoundingType']); ////start
                        if ($roundingOffTotal_fare_amountArr['method'] == "Addition") {
                            $roundingMethod = "";
                        }
                        else {
                            $roundingMethod = "-";
                        }
                        $roundingOffTotal_fare_amount = isset($roundingOffTotal_fare_amountArr['finalFareValue']) && $roundingOffTotal_fare_amountArr['finalFareValue'] != '' ? $roundingOffTotal_fare_amountArr['finalFareValue'] : "0.00";
                        $fPrice = formatNum($roundingOffTotal_fare_amount);
                    }
                }
                $data_order[$i]['fNetTotal'] = formateNumAsPerCurrency($fPrice, $currencycode);
                //$data_order[$i]['vStatus'] = getOrderStatus($data_order[$i]['iOrderId']);
                $vStatus = "";
                if (isset($orderStatusArr[$data_order[$i]['iOrderId']])) {
                    $vStatus = $orderStatusArr[$data_order[$i]['iOrderId']];
                }
                $data_order[$i]['vStatus'] = $vStatus;
                $iStatusCode = $data_order[$i]['iStatusCode'];
                $data_order[$i]['DisplayLiveTrack'] = "Yes";
                if ($iStatusCode == 6 || $iStatusCode == 7 || $iStatusCode == 8 || $iStatusCode == 9 || $iStatusCode == 11) {
                    $data_order[$i]['DisplayLiveTrack'] = "No";
                }
                $tOrderRequestDate = $data_order[$i]['tOrderRequestDate'];
                //$tOrderRequestDate = converToTz($tOrderRequestDate, $serverTimeZone, $vTimeZone);
                $tOrderRequestDate = converToTz($tOrderRequestDate, $vTimeZone, $serverTimeZone);
                $data_order[$i]['tOrderRequestDate'] = $tOrderRequestDate;
                //added by SP for cubex on 01-10-2019 start
                if ($data_order[$i]['iStatusCode'] == '11' || $data_order[$i]['iStatusCode'] == '9') {
                    $status = $languageLabelsArr["LBL_HISTORY_REST_DECLINED"];
                }
                else if ($data_order[$i]['iStatusCode'] == '8') {
                    $status = $languageLabelsArr["LBL_HISTORY_REST_CANCELLED"];
                }
                else if ($data_order[$i]['iStatusCode'] == '7' && $UserType == "Passenger") {
                    $status = $languageLabelsArr["LBL_HISTORY_REST_REFUNDS"];
                }
                else if ($data_order[$i]['iStatusCode'] == '7' && $UserType != "Passenger") {
                    $status = $languageLabelsArr["LBL_HISTORY_REST_CANCELLED"];
                }
                else if ($data_order[$i]['iStatusCode'] == '6') {
                    if ($data_order[$i]['eTakeaway'] == 'Yes') {
                        $status = $languageLabelsArr["LBL_TAKE_AWAY_ORDER_PICKEDUP_TXT"];
                    }
                    else {
                        $status = $languageLabelsArr["LBL_HISTORY_REST_DELIVERED"];
                    }
                }
                else if ($data_order[$i]['iStatusCode'] == '2' || $data_order[$i]['iStatusCode'] == '1') {
                    $status = $languageLabelsArr["LBL_ORDER_PLACED"];
                }
                else {
                    $status = $languageLabelsArr["LBL_ORDER_PLACED"];
                }
                $data_order[$i]['vOrderStatus'] = $status;
                $data_order[$i]['vService_BG_color'] = RANDOM_COLORS_ARR[array_rand(RANDOM_COLORS_ARR, 1)];
                $data_order[$i]['vService_TEXT_color'] = "#FFFFFF";
                //added by SP for cubex on 01-10-2019 end
                $driverData = $obj->MySQLSelect("SELECT vName, vLastName FROM register_driver WHERE iDriverId = '" . $data_order[$i]['iDriverId'] . "'");
                if (!empty($driverData) && count($driverData) > 0) {
                    $data_order[$i]['driverId'] = $data_order[$i]['iDriverId'];
                    $data_order[$i]['driverName'] = $driverData[0]['vName'] . ' ' . $driverData[0]['vLastName'];
                }
                if ($data_order[$i]['iStatusCode'] == "6") {
                    $check_rating_company = $obj->MySQLSelect("SELECT * FROM `ratings_user_driver` WHERE iOrderId = '" . $data_order[$i]['iOrderId'] . "' and eFromUserType = 'Passenger' AND eToUserType = 'Company' ");
                    $check_rating_driver = $obj->MySQLSelect("SELECT * FROM `ratings_user_driver` WHERE iOrderId = '" . $data_order[$i]['iOrderId'] . "' and eFromUserType = 'Passenger' AND eToUserType = 'Driver'");
                    if (empty($check_rating_company) || empty($check_rating_driver)) {
                        $data_order[$i]['isRatingButtonShow'] = "Yes";
                    }
                    else {
                        $data_order[$i]['isRatingButtonShow'] = "No";
                    }
                    if (empty($check_rating_company)) {
                        $data_order[$i]['STORE_RATING_DONE'] = "No";
                    }
                    else {
                        $data_order[$i]['STORE_RATING_DONE'] = "Yes";
                    }
                    if (empty($check_rating_driver)) {
                        $data_order[$i]['DRIVER_RATING_DONE'] = "No";
                    }
                    else {
                        $data_order[$i]['DRIVER_RATING_DONE'] = "Yes";
                    }
                    if ($MODULES_OBJ->isEnableFoodRatingDetailFlow() && $data_order[$i]['iServiceId'] == "1" && $data_order[$i]['eBuyAnyService'] == "No") {
                        $data_order[$i]['ENABLE_FOOD_RATING_DETAIL_FLOW'] = "Yes";
                    }
                }
            }
        }
        if ($MODULES_OBJ->isEnableFoodRatingDetailFlow()) {
            $DRIVER_FEEDBACK_QUESTIONS = getFoodRatingDetailFeedbackQuestions($vLang);
            $returnArr['DRIVER_FEEDBACK_QUESTIONS'] = !empty($DRIVER_FEEDBACK_QUESTIONS) ? $DRIVER_FEEDBACK_QUESTIONS : "";
        }
        //8 (Cancelled) = 6,8
        //2 (Inprocess) =  2,4,5
        //6 (Order Placed) = 1
        //$getOrderStatus = $obj->MySQLSelect("SELECT vStatus_" . $vLang . ",iStatusCode FROM order_status WHERE iStatusCode IN (1,6,8)");
        $inProcessStatus = array("vStatus_" . $vLang => "Inprocess", "iStatusCode" => "2");
        $returnArr['Action'] = "1";
        $returnArr['message'] = $data_order;
        /* $returnArr['orderStatusFilter'] = $getOrderStatus;
          $appTypeFilterArr = $optionArr = array();
          $optionArr[] = array("vSubFilterParam" => "", "vTitle" => $languageLabelsArr['LBL_ALL']);
          for ($d = 0; $d < count($getOrderStatus); $d++) {
          $statusArr = array();
          $statusArr['vSubFilterParam'] = $getOrderStatus[$d]['iStatusCode'];
          $statusArr['vTitle'] = $getOrderStatus[$d]['vStatus_' . $vLang];
          $optionArr[] = $statusArr;
          }
          $returnArr['subFilterOption'] = $optionArr; */
        if ($filterSelected != "All" && $filterSelected != "") {
            if ($pending > 0) {
                $filterSelected = $selPending;
            }
            else if ($upcoming > 0) {
                $filterSelected = $selUpcoming;
            }
        }
        if ($TotalPages > $page) {
            $returnArr['NextPage'] = "" . ($page + 1);
        }
        else {
            $returnArr['NextPage'] = "0";
        }
        //$returnArr['AvgRating'] = $driverAvgRate;
        $returnArr['AvgRating'] = $driverAvgRate_new;
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_NO_ORDERS_FOUND_TXT";
    }
    $returnArr['eFilterSel'] = $filterSelected;
    setDataResponse($returnArr);
}
// ############################# Display User's Active Orders ###################################################################
// ############################# Config Company Order Status  ###################################################################
if ($type == "configCompanyOrderStatus") {
    $iCompanyId = isset($_REQUEST["iCompanyId"]) ? $_REQUEST["iCompanyId"] : '';
    $userType = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : 'Company';
    $iOrderId = isset($_REQUEST["iOrderId"]) ? $_REQUEST["iOrderId"] : '';
    $vTimeZone = isset($_REQUEST["vTimeZone"]) ? $_REQUEST["vTimeZone"] : '';
    if ($iCompanyId != "") {
        if (!empty($isSubsToCabReq) && $isSubsToCabReq == 'true') {
            $driver_update['tLastOnline'] = date('Y-m-d H:i:s');
            $driver_update['tOnline'] = date('Y-m-d H:i:s');
        }
        if (!empty($vLatitude) && !empty($vLongitude)) {
            $driver_update['vLatitude'] = $vLatitude;
            $driver_update['vLongitude'] = $vLongitude;
        }
        if (count($driver_update) > 0) {
            $where = " iDriverId = '" . $iMemberId . "'";
            $Update_driver = $obj->MySQLQueryPerform("register_driver", $driver_update, "update", $where);
            // Update User Location Date #
            Updateuserlocationdatetime($iMemberId, "Driver", $vTimeZone);
            // Update User Location Date #
        }
    }
    if ($iTripId != "") {
        $sql = "SELECT tMessage as msg, iStatusId FROM trip_status_messages WHERE iDriverId='" . $iMemberId . "' AND eToUserType='Driver' AND eReceived='No' ORDER BY iStatusId DESC LIMIT 1 ";
        $msg = $obj->MySQLSelect($sql);
    }
    else {
        $date = @date("Y-m-d");
        $sql = "SELECT passenger_requests.tMessage as msg  FROM passenger_requests LEFT JOIN driver_request ON  driver_request.iRequestId=passenger_requests.iRequestId  LEFT JOIN register_driver ON register_driver.iDriverId=passenger_requests.iDriverId where date_format(passenger_requests.dAddedDate,'%Y-%m-%d')= '" . $date . "' AND  passenger_requests.iDriverId=" . $iMemberId . " AND driver_request.eStatus='Timeout' AND driver_request.iDriverId='" . $iMemberId . "' AND register_driver.vTripStatus IN ('Not Active','NONE','Cancelled') ORDER BY passenger_requests.iRequestId DESC LIMIT 1 ";
        $msg = $obj->MySQLSelect($sql);
    }
    $returnArr['Action'] = "0";
    if (!empty($msg)) {
        $returnArr['Action'] = "1";
        if ($iTripId != "") {
            $updateQuery = "UPDATE trip_status_messages SET eReceived = 'Yes' WHERE iDriverId='" . $iMemberId . "'";
            $obj->sql_query($updateQuery);
            $returnArr['Action'] = "1";
            $returnArr['message'] = $msg[0]['msg'];
        }
        else {
            $driver_request['eStatus'] = "Received";
            $where = " iDriverId =" . $iMemberId . " and date_format(tDate,'%Y-%m-%d') = '" . $date . "' AND eStatus = 'Timeout' ";
            $obj->MySQLQueryPerform("driver_request", $driver_request, "update", $where);
            $returnArr['Action'] = "1";
            $dataArr = array();
            for ($i = 0; $i < count($msg); $i++) {
                $dataArr[$i] = $msg[$i]['msg'];
            }
            $returnArr['message'] = $dataArr;
        }
    }
    setDataResponse($returnArr);
}
// ############################# Config Company Order Status ######################################################
// ############################### Get Order States Tracking  ###################################################################
if ($type == "getOrderDeliveryLog") {
    $iOrderId = isset($_REQUEST["iOrderId"]) ? $_REQUEST["iOrderId"] : '';
    $vTimeZone = isset($_REQUEST["vTimeZone"]) ? $_REQUEST["vTimeZone"] : '';
    $iUserId = isset($_REQUEST["iUserId"]) ? $_REQUEST["iUserId"] : '';
    $UserType = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : 'Passenger'; // Passenger, Driver , Company
    if ($UserType == "Passenger") {
        $tblname = "register_user";
        $iMemberId = 'ord.iUserId';
        $UserDetailsArr = getUserCurrencyLanguageDetails($iUserId, $iOrderId);
        $Ratio = $UserDetailsArr['Ratio'];
        $currencySymbol = $UserDetailsArr['currencySymbol'];
        $vLang = $UserDetailsArr['vLang'];
        $currencycode = $UserDetailsArr['currencycode'];
        $NotInStatusCode = "12";
        $fields = "concat(vName,' ',vLastName) as drivername,vImgName AS vImage";
    }
    else if ($UserType == "Driver") {
        $tblname = "register_driver";
        $iMemberId = 'ord.iDriverId';
        $UserDetailsArr = getDriverCurrencyLanguageDetails($iUserId, $iOrderId);
        $Ratio = $UserDetailsArr['Ratio'];
        $currencySymbol = $UserDetailsArr['currencySymbol'];
        $currencycode = $UserDetailsArr['currencycode'];
        $vLang = $UserDetailsArr['vLang'];
        $NotInStatusCode = "12";
        $fields = "concat(vName,' ',vLastName) as drivername,vImage";
    }
    else {
        $tblname = "company";
        $iMemberId = 'ord.iCompanyId';
        $UserDetailsArr = getCompanyCurrencyLanguageDetails($iUserId, $iOrderId);
        $Ratio = $UserDetailsArr['Ratio'];
        $currencySymbol = $UserDetailsArr['currencySymbol'];
        $currencycode = $UserDetailsArr['currencycode'];
        $vLang = $UserDetailsArr['vLang'];
        $NotInStatusCode = "1,2,12";
        $fields = "concat(vName,' ',vLastName) as drivername,vImage";
    }
    /* $getUserImgData  = $obj->MySQLSelect("SELECT $fields FROM ".$tblname." AS ord WHERE $iMemberId='".$iUserId."'");
      $driverName = $imgaeName = "";
      if(count($getUserImgData) > 0){
      $driverName = $getUserImgData[0]['drivername'];
      $imgaeName = $getUserImgData[0]['vImage'];
  } */
    //takeaway feature start
    $orderdata = get_value('orders', 'iServiceId,eTakeaway,ePaymentOption,fSubTotal,fOffersDiscount,fDeliveryCharge,fNetTotal,eBuyAnyService,iStatusCode, genieWaitingForUserApproval,genieUserApproved,ePaid,fOutStandingAmount,fRoundingAmount,eRoundingType', 'iOrderId', $iOrderId, '');
    $iServiceId = $orderdata[0]['iServiceId'];
    $eTakeaway = !empty($orderdata[0]['eTakeaway']) ? $orderdata[0]['eTakeaway'] : "No";
    $eBuyAnyServiceOrd = $orderdata[0]['eBuyAnyService'];
    if ($eTakeaway == 'Yes') {
        $NotInStatusCode .= ", 4 ,5";
    }
    //takeaway feature end
    $OrderStatusMain = $OrderStatusNotExistMain = array();
    $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLang, "1", $iServiceId);
    $LBL_ITEMSLBL_ITEMS = $languageLabelsArr['LBL_ITEMSLBL_ITEMS'];
    $LBL_ITEMSLBL_ITEM = $languageLabelsArr['LBL_ITEMSLBL_ITEM'];
    $LBL_RESTAURANT_TXT = $languageLabelsArr['LBL_RESTAURANT_TXT'];
    $LBL_VEHICLE_DRIVER_TXT_FRONT = $languageLabelsArr['LBL_VEHICLE_DRIVER_TXT_FRONT'];
    $sql = "SELECT os.vStatus_" . $vLang . " as vStatus,os.vStatus_Track_" . $vLang . " as vStatus_Track,osl.dDate,osl.iStatusCode,ord.iUserId,ord.iCompanyId,ord.iDriverId,ord.iStatusCode as OrderCurrentStatusCode,ord.iUserAddressId,ord.vOrderNo,ord.tOrderRequestDate,ord.fNetTotal,ord.iOrderId,ord.vImageDeliveryPref,selectedPreferences,os.eTakeaway,ord.fTipAmount,os.eBuyAnyService,ord.eAskCodeToUser,ord.vRandomCode FROM order_status_logs as osl LEFT JOIN order_status as os ON osl.iStatusCode = os.iStatusCode LEFT JOIN orders as ord ON osl.iOrderId=ord.iOrderId WHERE osl.iOrderId = " . $iOrderId . " AND osl.iStatusCode NOT IN(" . $NotInStatusCode . ") ORDER BY osl.iStatusCode ASC";
    $OrderStatusMain = $obj->MySQLSelect($sql);
    $eDisplayDottedLine = "No";
    $eDisplayRouteLine = "No";
    if (count($OrderStatusMain) > 0) {
        $returnArr['Action'] = "1";
        $UserSelectedAddressArr = FetchMemberAddressData($OrderStatusMain[0]['iUserId'], "Passenger", $OrderStatusMain[0]['iUserAddressId']);
        $sql = "SELECT concat(vName,' ',vLastName) as drivername,vImage from  register_driver WHERE iDriverId ='" . $OrderStatusMain[0]['iDriverId'] . "'";
        $driverdetail = $obj->MySQLSelect($sql);
        $drivername = $imgaeName = "";
        if(!empty($driverdetail)) {
            $drivername = !empty($driverdetail[0]['drivername']) ? $driverdetail[0]['drivername'] : $LBL_VEHICLE_DRIVER_TXT_FRONT;
            $imgaeName = empty($driverdetail[0]['vImage']) ? "" : $driverdetail[0]['vImage'];
        }

        $OrderPickedUpDate = $OrderStatusCode = "";
        $CheckOtherStatusCode = "Yes";
        $companyfields = "vCompany,vRestuarantLocation,vRestuarantLocationLat,vRestuarantLocationLong,vCaddress,fPrepareTime";
        $Data_cab_requestcompany = get_value('company', $companyfields, 'iCompanyId', $OrderStatusMain[0]['iCompanyId']);

       
        $serverTimeZone = date_default_timezone_get();
        $eta1 = $eta2 = "";
        for ($i = 0; $i < count($OrderStatusMain); $i++) {
            //takeaway feature start
            if ($OrderStatusMain[$i]['iStatusCode'] == 6 || $OrderStatusMain[$i]['iStatusCode'] == 2) {
                $ordtakeaway = !empty($OrderStatusMain[$i]['eTakeaway']) ? $OrderStatusMain[$i]['eTakeaway'] : "No";
                if ($eTakeaway == 'Yes' && $ordtakeaway == "No") {
                    continue;
                }
                if ($eTakeaway == 'No' && $ordtakeaway == "Yes") {
                    continue;
                }
            }
            //takeaway feature end
            //Buy Any Service feature start /* Added by HV on 01-09-2020 for Buy Any Service Feature */
            if (in_array($OrderStatusMain[$i]['iStatusCode'], [1, 2, 4])) {
                if (($eBuyAnyServiceOrd == 'Yes' && $OrderStatusMain[$i]['eBuyAnyService'] == "No") || ($eBuyAnyServiceOrd == 'No' && $OrderStatusMain[$i]['eBuyAnyService'] == "Yes") || ($OrderStatusMain[$i]['iStatusCode'] == 2 && $eBuyAnyServiceOrd == "Yes")) {
                    continue;
                }
            }
            if (in_array($OrderStatusMain[$i]['iStatusCode'], [13, 14]) && $eBuyAnyServiceOrd == 'No') {
                continue;
            }
            //Buy Any Service feature /* Added by HV on 01-09-2020 for Buy Any Service Feature End */
            $OrderStatus[$i] = $OrderStatusMain[$i];
            $OrderStatusCode .= $OrderStatus[$i]['iStatusCode'] . ",";
            $dDate = $OrderStatus[$i]['dDate'];
            $dDate = converToTz($dDate, $vTimeZone, $serverTimeZone);
            $OrderStatus[$i]['dDate'] = $dDate;
            $OrderStatus[$i]['driverName'] = $drivername;
            $OrderStatus[$i]['driverImage'] = $imgaeName;
            $iStatusCode = $OrderStatus[$i]['OrderCurrentStatusCode'];
            if ($iStatusCode == 1 || $iStatusCode == 2 || $iStatusCode == 8 || $iStatusCode == 8) {
                $eDisplayDottedLine = "Yes";
                $eDisplayRouteLine = "No";
            }
            if ($iStatusCode == 5 || $OrderStatus[$i]['iStatusCode'] == 5) {
                $eDisplayDottedLine = "No";
                $eDisplayRouteLine = "Yes";
                $OrderPickedUpDate = $OrderStatus[$i]['dDate'];
            }
            $OrderStatus[$i]['eShowCallImg'] = "No";
            $StatusCodeLogwise = $OrderStatus[$i]['iStatusCode'];
            if ($StatusCodeLogwise == 5) {
                $OrderStatus[$i]['eShowCallImg'] = "Yes";
            }
            $OrderStatus[$i]['vStatus_Track'] = str_replace("#DriverName#", $drivername, $OrderStatus[$i]['vStatus_Track']);
            $OrderStatus[$i]['vStatus_Track'] = str_replace("#STORE#", $LBL_RESTAURANT_TXT, $OrderStatus[$i]['vStatus_Track']);
            $OrderStatus[$i]['vStatus'] = str_replace("#STORE#", $LBL_RESTAURANT_TXT, $OrderStatus[$i]['vStatus']);
            $OrderStatus[$i]['eCompleted'] = "Yes";
            if ($iStatusCode == 8 || $iStatusCode == 9) {
                $CheckOtherStatusCode = "No";
            }
            if ($MODULES_OBJ->isDeliveryPreferenceEnable()) {
                if ($OrderStatus[$i]['iStatusCode'] == 6) {
                    $OrderStatus[$i]['isPrefrenceImageUploaded'] = 'No';
                    if ($OrderStatus[$i]['vImageDeliveryPref'] != "") {
                        $OrderStatus[$i]['isPrefrenceImageUploaded'] = 'Yes';
                        $OrderStatus[$i]['vImageDeliveryPref'] = $tconfig['tsite_upload_order_delivery_pref_images'] . $OrderStatus[$i]['vImageDeliveryPref'];
                    }
                    else {
                        $OrderStatus[$i]['vImageDeliveryPref'] = "";
                    }
                }
            }
            if ($MODULES_OBJ->isEnableOTPVerificationDeliverAll()) {
                if ($OrderStatus[$i]['OrderCurrentStatusCode'] == "5" && $OrderStatus[$i]['iStatusCode'] == "5" && $OrderStatus[$i]['eAskCodeToUser'] == "Yes" && $UserType == "Passenger") {
                    $OrderStatus[$i]['vStatus_Track'] = $OrderStatus[$i]['vStatus_Track'] . " " . $languageLabelsArr['LBL_YOUR_TRIP_OTP_TXT'] . " " . $OrderStatus[$i]['vRandomCode'];
                }
            }
            if ($orderdata[0]['eBuyAnyService'] == "Yes") {
                if ($OrderStatus[$i]['iStatusCode'] == "4" && $OrderStatus[$i]['OrderCurrentStatusCode'] == "4") {
                    $OrderStatus[$i]['eShowCallImg'] = "Yes";
                }
                $OrderStatus[$i]['genieWaitingForUserApproval'] = $orderdata[0]['genieWaitingForUserApproval'];
                $OrderStatus[$i]['genieUserApproved'] = $orderdata[0]['genieUserApproved'];
                if ($OrderStatus[$i]['iStatusCode'] == "13") {
                    $itemDetails = $obj->MySQLSelect("SELECT oba.*,od.iQty,mi.vItemType_$vLang,mi.vItemTypeBuyAnyService FROM order_items_buy_anything as oba LEFT JOIN order_details as od ON od.iOrderDetailId = oba.iOrderDetailId LEFT JOIN menu_items as mi ON mi.iMenuItemId = od.iMenuItemId WHERE oba.iOrderId = $iOrderId AND oba.eConfirm = 'No' AND oba.eDecline = 'No' AND oba.eItemAvailable = 'Yes' LIMIT 1");
                    if (count($itemDetails) > 0) {
                        $itemArr = array(
                            'iItemDetailsId' => $itemDetails[0]['iItemDetailsId'], 'iOrderId' => $itemDetails[0]['iOrderId'], 'iOrderDetailId' => $itemDetails[0]['iOrderDetailId'], 'iQty' => $itemDetails[0]['iQty'], 'MenuItem' => $itemDetails[0]['vItemTypeBuyAnyService'], 'fTotPrice' => formateNumAsPerCurrency((setTwoDecimalPoint($itemDetails[0]['fItemPrice']) * $Ratio), $currencycode), 'vImage' => ($itemDetails[0]['vItemImage'] != "") ? $tconfig['tsite_upload_order_buy_anything'] . $itemDetails[0]['vItemImage'] : "", 'eItemAvailable' => $itemDetails[0]['eItemAvailable'], 'eExtraPayment' => $itemDetails[0]['eExtraPayment']
                        );
                        $OrderStatus[$i]['itemForReview'] = $itemArr;
                        if ($OrderStatus[$i]['OrderCurrentStatusCode'] == "13") {
                            $OrderStatus[$i]['eShowCallImg'] = "Yes";
                        }
                    }
                }
                if ($OrderStatus[$i]['iStatusCode'] == "14") {
                    $storeBillAmount = $orderdata[0]['fNetTotal'];
                    if ($orderdata[0]['ePaymentOption'] == "Card") {
                        $storeBillAmount = $orderdata[0]['fNetTotal'] - ($orderdata[0]['fDeliveryCharge'] + $orderdata[0]['fOutStandingAmount']);
                    }
                    $storeBillAmount = setTwoDecimalPoint($storeBillAmount) * $Ratio;
                    $storeBillAmount = ($storeBillAmount <= 0) ? 0 : $storeBillAmount;
                    $OrderStatus[$i]['showViewBillButton'] = ($orderdata[0]['ePaid'] == "No") ? "Yes" : "No";
                    $OrderStatus[$i]['showPaymentButton'] = (($orderdata[0]['ePaymentOption'] == "Card" || $orderdata[0]['ePaymentOption'] == "Wallet") && $orderdata[0]['iStatusCode'] == "14" && $orderdata[0]['ePaid'] == "No") ? "Yes" : "No";
                    $OrderStatus[$i]['fStoreBillAmount'] = formateNumAsPerCurrency($storeBillAmount, $currencycode);
                    $currData = $obj->MySQLSelect("SELECT * FROM currency WHERE vName='" . $currencycode . "'");
                    if (isset($orderdata[0]['fRoundingAmount']) && !empty($orderdata[0]['fRoundingAmount']) && $orderdata[0]['fRoundingAmount'] != 0 && $currData[0]['eRoundingOffEnable'] == "Yes" && $MODULES_OBJ->isEnableRoundingMethod()) {
                        $roundingOffTotal_fare_amountArr['method'] = $orderdata[0]['eRoundingType'];
                        $roundingOffTotal_fare_amountArr['differenceValue'] = $orderdata[0]['fRoundingAmount'];
                        $roundingOffTotal_fare_amountArr = getRoundingOffAmounttrip($storeBillAmount, $orderdata[0]['fRoundingAmount'], $orderdata[0]['eRoundingType']); ////start
                        if ($roundingOffTotal_fare_amountArr['method'] == "Addition") {
                            $roundingMethod = "";
                        }
                        else {
                            $roundingMethod = "-";
                        }
                        $storeBillAmount = isset($roundingOffTotal_fare_amountArr['finalFareValue']) && $roundingOffTotal_fare_amountArr['finalFareValue'] != '' ? $roundingOffTotal_fare_amountArr['finalFareValue'] : "0.00";
                        $OrderStatus[$i]['fStoreBillAmount'] = formateNumAsPerCurrency($storeBillAmount, $currencycode);
                    }
                }
            }
            /* Added by HV on 22-02-2021 for ETA of delivery */
            if ($OrderStatus[$i]['iStatusCode'] == '5' && $OrderStatus[$i]['eCompleted'] == "Yes") {
                $eta1 = $OrderStatus[$i]['dDate'];
            }
            if ($OrderStatus[$i]['iStatusCode'] == '6' && $OrderStatus[$i]['eCompleted'] == "Yes") {
                $eta2 = $OrderStatus[$i]['dDate'];
            }
        }
        if ($eBuyAnyServiceOrd == "Yes") {
            $OrderStatus = array_values($OrderStatus);
            if ($OrderStatus[0]['OrderCurrentStatusCode'] == 8 || $OrderStatus[0]['OrderCurrentStatusCode'] == 9) {
                $CheckOtherStatusCode = "No";
            }
        }
        if ($iStatusCode == 7) { //if order refunded..
            $CheckOtherStatusCode = "No";
        }
        if ($CheckOtherStatusCode == "Yes" && $UserType == "Passenger") {
            //$OrderStatusCode = substr($OrderStatusCode, 0, -1);
            $OrderStatusCode = trim($OrderStatusCode, ",");
            $OrderStatusCode = $OrderStatusCode . ",7,8,9,11,12";
            if ($eTakeaway == 'Yes') {
                $OrderStatusCode .= ", 4 ,5";
            }
            $OrderStatusCode = trim($OrderStatusCode, ",");
            $sql = "SELECT vStatus_" . $vLang . " as vStatus,vStatus_Track_" . $vLang . " as vStatus_Track,iStatusCode,eTakeaway,eBuyAnyService FROM order_status WHERE iStatusCode NOT IN(" . $OrderStatusCode . ") ORDER BY iDisplayOrder ASC";
            $OrderStatusNotExistMain = $obj->MySQLSelect($sql);
            for ($i = 0; $i < count($OrderStatusNotExistMain); $i++) {
                if ($OrderStatusNotExistMain[$i]['iStatusCode'] == 6 || $OrderStatusNotExistMain[$i]['iStatusCode'] == 2) {
                    $ordtakeaway = !empty($OrderStatusNotExistMain[$i]['eTakeaway']) ? $OrderStatusNotExistMain[$i]['eTakeaway'] : "No";
                    if ($eTakeaway == 'Yes' && $ordtakeaway == "No") {
                        continue;
                    }
                    if ($eTakeaway == 'No' && $ordtakeaway == "Yes") {
                        continue;
                    }
                }
                //Buy Any Service feature start /* Added by HV on 01-09-2020 for Buy Any Service Feature */
                if (in_array($OrderStatusNotExistMain[$i]['iStatusCode'], [1, 2, 4])) {
                    if (($eBuyAnyServiceOrd == 'Yes' && $OrderStatusNotExistMain[$i]['eBuyAnyService'] == "No") || ($eBuyAnyServiceOrd == 'No' && $OrderStatusNotExistMain[$i]['eBuyAnyService'] == "Yes") || ($OrderStatusNotExistMain[$i]['iStatusCode'] == 2 && $eBuyAnyServiceOrd == "Yes")) {
                        continue;
                    }
                }
                if (in_array($OrderStatusNotExistMain[$i]['iStatusCode'], [13, 14]) && $eBuyAnyServiceOrd == 'No') {
                    continue;
                }
                //Buy Any Service feature /* Added by HV on 01-09-2020 for Buy Any Service Feature End */
                $OrderStatusNotExist[$i] = $OrderStatusNotExistMain[$i];
                $OrderStatusNotExist[$i]['vStatus'] = $OrderStatusNotExist[$i]['vStatus'];
                $OrderStatusNotExist[$i]['vStatus_Track'] = str_replace("#DriverName#", $drivername, $OrderStatusNotExist[$i]['vStatus_Track']);
                $OrderStatusNotExist[$i]['vStatus_Track'] = str_replace("#STORE#", $LBL_RESTAURANT_TXT, $OrderStatusNotExist[$i]['vStatus_Track']);
                $OrderStatusNotExist[$i]['vStatus'] = str_replace("#STORE#", $LBL_RESTAURANT_TXT, $OrderStatusNotExist[$i]['vStatus']);
                $OrderStatusNotExist[$i]['dDate'] = "";
                $OrderStatusNotExist[$i]['iStatusCode'] = $OrderStatusNotExist[$i]['iStatusCode'];
                $OrderStatusNotExist[$i]['iUserId'] = $OrderStatus[0]['iUserId'];
                $OrderStatusNotExist[$i]['iCompanyId'] = $OrderStatus[0]['iCompanyId'];
                $OrderStatusNotExist[$i]['iDriverId'] = $OrderStatus[0]['iDriverId'];
                $OrderStatusNotExist[$i]['OrderCurrentStatusCode'] = $OrderStatus[0]['OrderCurrentStatusCode'];
                $OrderStatusNotExist[$i]['iUserAddressId'] = $OrderStatus[0]['iUserAddressId'];
                $OrderStatusNotExist[$i]['vOrderNo'] = $OrderStatus[0]['vOrderNo'];
                $OrderStatusNotExist[$i]['tOrderRequestDate'] = $OrderStatus[0]['tOrderRequestDate'];
                $OrderStatusNotExist[$i]['fNetTotal'] = $OrderStatus[0]['fNetTotal'];
                $OrderStatusNotExist[$i]['eShowCallImg'] = $OrderStatus[0]['eShowCallImg'];
                $OrderStatusNotExist[$i]['eCompleted'] = "No";
                array_push($OrderStatus, $OrderStatusNotExist[$i]);
            }
        }
        foreach ($OrderStatus as $k => $v) {
            $Data_name['iStatusCode'][$k] = $v['iStatusCode'];
        }
        array_multisort($Data_name['iStatusCode'], SORT_ASC, $OrderStatus); //Added By HJ ON 3-1-2019 For Sort BY iStatusCode
        /* Added by HV on 01-09-2020 for Buy Any Service Feature */
        if ($eBuyAnyServiceOrd == "Yes") {
            $insertAfterKey = "";
            $arrToInsert = array();
            foreach ($OrderStatus as $okey => $oValue) {
                if ($oValue['iStatusCode'] == 4) {
                    $insertAfterKey = $okey;
                }
                if ($oValue['iStatusCode'] == 13 || $oValue['iStatusCode'] == 14) {
                    $arrToInsert[] = $OrderStatus[$okey];
                    unset($OrderStatus[$okey]);
                }
            }
            if (count($arrToInsert) > 0) {
                array_splice($OrderStatus, $insertAfterKey + 1, 0, $arrToInsert);
            }
            if ($orderdata[0]['ePaymentOption'] == "Cash") {
                $insertAfterKeyNew = "";
                $arrToInsertNew = array();
                foreach ($OrderStatus as $okey => $oValue) {
                    if ($oValue['iStatusCode'] == 5) {
                        $insertAfterKeyNew = $okey;
                    }
                    if ($oValue['iStatusCode'] == 14) {
                        $arrToInsertNew[] = $OrderStatus[$okey];
                        unset($OrderStatus[$okey]);
                    }
                }
                if (count($arrToInsertNew) > 0) {
                    array_splice($OrderStatus, $insertAfterKeyNew, 0, $arrToInsertNew);
                }
            }
            if ($orderdata[0]['genieUserApproved'] == "No") {
                $OrderStatus[0]['fNetTotal'] = $orderdata[0]['fDeliveryCharge'] + $orderdata[0]['fOutStandingAmount'];
            }
        }
        /* Added by HV on 01-09-2020 for Buy Any Service Feature End */
        if ($iStatusCode == 7) {
            $tempOrderStatus[0] = $OrderStatus[count($OrderStatus) - 1];
            $OrderStatus[count($OrderStatus) - 1] = $OrderStatus[count($OrderStatus) - 2];
            $OrderStatus[count($OrderStatus) - 2] = $tempOrderStatus[0];
        }
        $returnArr['message'] = $OrderStatus;
        $fNetTotal = $OrderStatus[0]['fNetTotal'];
        $fNetTotal = round($fNetTotal * $Ratio, 2);
        //$returnArr['fNetTotal'] = $currencySymbol . " " . formatnum($fNetTotal);
        $returnArr['fNetTotal'] = formateNumAsPerCurrency($fNetTotal, $currencycode);
        $returnArr['vOrderNo'] = $OrderStatus[0]['vOrderNo'];
        $TotalOrderItems = getTotalOrderDetailItemsCount($iOrderId);
        $returnArr['TotalOrderItems'] = ($TotalOrderItems > 1) ? $TotalOrderItems . " " . $LBL_ITEMSLBL_ITEMS : $TotalOrderItems . " " . $LBL_ITEMSLBL_ITEM;
        $tOrderRequestDate = $OrderStatus[0]['tOrderRequestDate'];
        $tOrderRequestDate = converToTz($tOrderRequestDate, $vTimeZone, $serverTimeZone);
        $returnArr['tOrderRequestDate'] = $tOrderRequestDate;
        $returnArr['OrderCurrentStatusCode'] = $OrderStatus[0]['OrderCurrentStatusCode'];
        $returnArr['PassengerLat'] = empty($UserSelectedAddressArr['vLatitude']) ? "" : $UserSelectedAddressArr['vLatitude'];
        $returnArr['PassengerLong'] = empty($UserSelectedAddressArr['vLongitude']) ? "" : $UserSelectedAddressArr['vLongitude'];
        $returnArr['DeliveryAddress'] = empty($UserSelectedAddressArr['UserAddress']) ? "" : $UserSelectedAddressArr['UserAddress'];
        $returnArr['vCompany'] = $Data_cab_requestcompany[0]['vCompany'];
        $returnArr['CompanyLat'] = $Data_cab_requestcompany[0]['vRestuarantLocationLat'];
        $returnArr['CompanyLong'] = $Data_cab_requestcompany[0]['vRestuarantLocationLong'];
        $returnArr['CompanyAddress'] = $Data_cab_requestcompany[0]['vRestuarantLocation'];
        $returnArr['iDriverId'] = $OrderStatus[0]['iDriverId'];
        $returnArr['eDisplayDottedLine'] = $eDisplayDottedLine;
        $returnArr['eDisplayRouteLine'] = $eDisplayRouteLine;
        $returnArr['OrderPickedUpDate'] = $OrderPickedUpDate;
        $returnArr['iServiceId'] = $iServiceId;
        $returnArr['ETA'] = "";
        if (!empty($eta1) && !empty($eta2)) {
            // $returnArr['ETA'] = (strtotime($eta2) - strtotime($eta1)) * 1000;
            $returnArr['ETA'] = (strtotime($eta2) - strtotime($eta1));
        }
        if ($OrderStatus[0]['iDriverId'] > 0) {
            $Data_cab_driverlatlong = get_value('register_driver', 'vLatitude,vLongitude,vCode,vPhone,vImage,vAvgRating, CONCAT(vName, " ", vLastname) as driverName', 'iDriverId', $OrderStatus[0]['iDriverId']);
            $returnArr['driverName'] = $Data_cab_driverlatlong[0]['driverName'];
            $returnArr['driverImage'] = $tconfig["tsite_upload_images_driver"] . "/" . $OrderStatus[0]['iDriverId'] . "/2_" . $Data_cab_driverlatlong[0]['vImage'];
            $returnArr['driverAvgRating'] = $Data_cab_driverlatlong[0]['vAvgRating'];
            $returnArr['DriverLong'] = $Data_cab_driverlatlong[0]['vLongitude'];
            $returnArr['DriverLat'] = $Data_cab_driverlatlong[0]['vLatitude'];
            $returnArr['DriverPhone'] = '+' . $Data_cab_driverlatlong[0]['vCode'] . $Data_cab_driverlatlong[0]['vPhone'];
        }
        else {
            $returnArr['driverName'] = "";
            $returnArr['driverImage'] = "";
            $returnArr['driverAvgRating'] = "";
            $returnArr['DriverLat'] = "";
            $returnArr['DriverLong'] = "";
            $returnArr['DriverPhone'] = "";
        }
        if ($MODULES_OBJ->isDeliveryPreferenceEnable()) {
            $selectedPreferences = $OrderStatus[0]['selectedPreferences'];
            $deliveryPrefSql = "SELECT eContactLess FROM delivery_preferences WHERE iPreferenceId IN (" . $selectedPreferences . ")";
            $deliveryPrefSqlData = $obj->MySQLSelect($deliveryPrefSql);
            $returnArr['isContactLessDeliverySelected'] = 'No';
            if(!empty($deliveryPrefSqlData)) {
                foreach ($deliveryPrefSqlData as $value) {
                    if ($value['eContactLess'] == 'Yes') {
                        $returnArr['isContactLessDeliverySelected'] = 'Yes';
                    }
                }
            }            
        }
        $returnArr['eTakeAway'] = $eTakeaway;
        if ($eTakeaway == 'Yes' && $OrderStatus[0]['OrderCurrentStatusCode'] == 2) {
            $preparetimedata = $languageLabelsArr['LBL_REST_PREPARATION_TIME'] . " " . $Data_cab_requestcompany[0]['fPrepareTime'] . " " . $languageLabelsArr['LBL_MINUTES_TXT'];
            $returnArr['prepareTime'] = $preparetimedata;
        }
        $returnArr['eTakeAwayPickedUpNote'] = "";
        if ($eTakeaway == 'Yes' && $OrderStatus[0]['OrderCurrentStatusCode'] == 6) {
            $returnArr['eTakeAwayPickedUpNote'] = str_replace('#RESTAURANT_NAME#', $Data_cab_requestcompany[0]['vCompany'], $languageLabelsArr['LBL_TAKE_AWAY_ORDER_NOTE']);
        }
        $returnArr['showTipAddArea'] = "No";
        if ($OrderStatusMain[0]['fTipAmount'] == 0 && in_array($OrderStatusMain[0]['OrderCurrentStatusCode'], [1, 2, 4, 5]) && $UserType == "Passenger" && $eTakeaway == "No" && $orderdata[0]['ePaymentOption'] == "Card" && $MODULES_OBJ->isEnableDeliveryTipFeatureDeliverAll() && $orderdata[0]['eBuyAnyService'] == "No") {
            $returnArr['showTipAddArea'] = "Yes";
        }
        $returnArr['TIP_AMOUNT_1_VALUE'] = round($TIP_AMOUNT_1 * $Ratio);
        $returnArr['TIP_AMOUNT_2_VALUE'] = round($TIP_AMOUNT_2 * $Ratio);
        $returnArr['TIP_AMOUNT_3_VALUE'] = round($TIP_AMOUNT_3 * $Ratio);
        if ($DELIVERY_TIP_AMOUNT_TYPE_DELIVERALL == "Percentage") {
            $returnArr['TIP_AMOUNT_1_VALUE'] = round((($TIP_AMOUNT_1 / 100) * ($orderdata[0]['fSubTotal'] - $orderdata[0]['fOffersDiscount']) * $Ratio), 2);
            $returnArr['TIP_AMOUNT_2_VALUE'] = round((($TIP_AMOUNT_2 / 100) * ($orderdata[0]['fSubTotal'] - $orderdata[0]['fOffersDiscount']) * $Ratio), 2);
            $returnArr['TIP_AMOUNT_3_VALUE'] = round((($TIP_AMOUNT_3 / 100) * ($orderdata[0]['fSubTotal'] - $orderdata[0]['fOffersDiscount']) * $Ratio), 2);
        }
        $returnArr['fNetTotalValue'] = round(($orderdata[0]['fNetTotal'] * $Ratio), 2);
        $returnArr['fTotalItemPrice'] = round((($orderdata[0]['fSubTotal'] - $orderdata[0]['fOffersDiscount']) * $Ratio), 2);
        $returnArr['userCurrencySymbol'] = $currencySymbol;
        if ($orderdata[0]['eBuyAnyService'] == "Yes") {
            $returnArr['eDisplayDottedLine'] = "No";
            if (in_array($orderdata[0]['iStatusCode'], [1, 2, 4, 13])) {
                $returnArr['eDisplayDottedLine'] = "Yes";
            }
        }
        $returnArr['eBuyAnyService'] = $orderdata[0]['eBuyAnyService'];

        if ($MODULES_OBJ->isEnableFoodRatingDetailFlow() && $orderdata[0]['iServiceId'] == "1" && $orderdata[0]['eBuyAnyService'] == "No") {
            $returnArr['ENABLE_FOOD_RATING_DETAIL_FLOW'] = "Yes";
        }
        if ($MODULES_OBJ->isEnableFoodRatingDetailFlow()) {
            $DRIVER_FEEDBACK_QUESTIONS = getFoodRatingDetailFeedbackQuestions($vLang);
            $returnArr['DRIVER_FEEDBACK_QUESTIONS'] = !empty($DRIVER_FEEDBACK_QUESTIONS) ? $DRIVER_FEEDBACK_QUESTIONS : "";
        }
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_NO_DATA_AVAIL";
    }
    setDataResponse($returnArr);
}
// ############################### Get Order States Tracking  ###################################################################
// ###################### start getOrderHistory #############################
if ($type == "getOrderHistory") {
    $page = isset($_REQUEST['page']) ? trim($_REQUEST['page']) : 1;
    $iGeneralUserId = isset($_REQUEST["iGeneralUserId"]) ? $_REQUEST["iGeneralUserId"] : "";
    $UserType = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : "Company";
    $vFromDate = isset($_REQUEST["vFromDate"]) ? $_REQUEST["vFromDate"] : "";
    $vToDate = isset($_REQUEST["vToDate"]) ? $_REQUEST["vToDate"] : "";
    $vSubFilterParam = isset($_REQUEST["vSubFilterParam"]) ? $_REQUEST["vSubFilterParam"] : "6";
    $vTimeZone = isset($_REQUEST["vTimeZone"]) ? $_REQUEST["vTimeZone"] : "Asia/Kolkata";
    $systemTimeZone = date_default_timezone_get();
    $vConvertFromDate = converToTz($vFromDate, $vTimeZone, $systemTimeZone, "Y-m-d");
    $vConvertToDate = '';
    if (!empty($vToDate)) {
        $vConvertToDate = converToTz($vToDate, $vTimeZone, $systemTimeZone, "Y-m-d");
    }
    if ($UserType == 'Driver') {
        $conditonalFields = 'iDriverId';
        $UserDetailsArr = getDriverCurrencyLanguageDetails($iGeneralUserId);
    }
    else if ($UserType == 'Passenger') {
        $conditonalFields = 'iUserId';
        $UserDetailsArr = getUserCurrencyLanguageDetails($iGeneralUserId);
    }
    else {
        $conditonalFields = 'iCompanyId';
        $UserDetailsArr = getCompanyCurrencyLanguageDetails($iGeneralUserId);
    }
    $filterSelected = $vSubFilterParam;
    $currencycode = $UserDetailsArr['currencycode'];
    $vSymbol = $UserDetailsArr['currencySymbol'];
    $enable_takeaway = 0;
    if ($MODULES_OBJ->isTakeAwayEnable() && $UserType != 'Driver') {
        $enable_takeaway = 1;
    }
    // $priceRatio = $UserDetailsArr['Ratio'];
    $vLang = $UserDetailsArr['vLang'];
    $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLang, "1", $iServiceId);
    $per_page = 10;
    $whereFilter = "";
    if ($vFromDate != "") {
        $whereFilter = "DATE(tOrderRequestDate) = '" . $vFromDate . "' AND ";
    }
    if ($vFromDate != "" && $vConvertToDate != "") {
        $whereFilter = "(DATE(tOrderRequestDate) BETWEEN '$vConvertFromDate' AND '$vConvertToDate') AND ";
    }
    $whereStatusCode = "AND  `iStatusCode` IN (6, 7, 8, 11)";
    if ($vSubFilterParam != "") {
        if ($vSubFilterParam == '6-1' && $enable_takeaway == 1) {
            $whereStatusCode = " AND ord.iStatusCode IN (6) AND ord.eTakeaway = 'Yes'";
        }
        else if ($vSubFilterParam == '6' && $enable_takeaway == 1) {
            $whereStatusCode = " AND ord.iStatusCode IN ($vSubFilterParam) AND ord.eTakeaway = 'No'";
        }
        else if ($vSubFilterParam == 8) {
            $whereStatusCode = "AND  `iStatusCode` IN (7, 8)";
        }
        else {
            $whereStatusCode = "AND  `iStatusCode` IN ($vSubFilterParam)";
        }
    }
    $sql_all = "SELECT COUNT(iOrderId) As TotalIds FROM orders WHERE $whereFilter $conditonalFields='$iGeneralUserId' $whereStatusCode";
    $data_count_all = $obj->MySQLSelect($sql_all);
    $TotalPages = ceil($data_count_all[0]['TotalIds'] / $per_page);
    $start_limit = ($page - 1) * $per_page;
    $limit = " LIMIT " . $start_limit . ", " . $per_page;
    //$sql = "SELECT *,vOrderNo, iCompanyId,iDriverId,iUserAddressId,vCompany,iOrderId, tOrderRequestDate, iUserId, fNetTotal, fTotalGenerateFare, fCommision, fOffersDiscount, fDeliveryCharge, iStatusCode, fRatio_" . $currencycode . " as Ratio, fRestaurantPaidAmount, fDriverPaidAmount,eRestaurantPaymentStatus,eAdminPaymentStatus  FROM `orders` WHERE $whereFilter $conditonalFields='$iGeneralUserId' $whereStatusCode ORDER BY tOrderRequestDate DESC " . $limit;
    $sql = "SELECT ord.vOrderNo, ord.iCompanyId,ord.iDriverId, ord.iUserAddressId, ord.vCompany, ord.iOrderId, ord.tOrderRequestDate, ord.iUserId, ord.fNetTotal, ord.fTotalGenerateFare, ord.fCommision, ord.fOffersDiscount, ord.fDeliveryCharge, ord.iStatusCode, ord.fRatio_" . $currencycode . " as Ratio, ord.fRestaurantPaidAmount, ord.fDriverPaidAmount, ord.eRestaurantPaymentStatus, ord.eAdminPaymentStatus,sc.vServiceName_" . $vLang . " as vServiceCategoryName,ord.eTakeaway, ord.fTipAmount,ord.fOutStandingAmount,ord.eBuyAnyService,ord.ePaymentOption,ord.eOrderplaced_by, ord.vName,ord.eForPickDropGenie,ord.iServiceId FROM `orders` as ord LEFT JOIN service_categories as sc on sc.iServiceId=ord.iServiceId WHERE $whereFilter $conditonalFields='$iGeneralUserId' $whereStatusCode ORDER BY tOrderRequestDate DESC " . $limit;
    //added by SP on 30-09-2019 for cubex to get service category name as per HJ added on DisplayActiveOrder
    $Data = $obj->MySQLSelect($sql);
    $existingArr = $storeIdArr = $newdata = $storeImageArr = $addressIdArr = $orderAddressArr = $driverRateArr = $orderIdArr = array();
    $count = $totalOrder = 0;
    $sql_whole = "SELECT ord.vOrderNo, ord.iCompanyId,ord.iDriverId, ord.iUserAddressId, ord.vCompany, ord.iOrderId, ord.tOrderRequestDate, ord.iUserId, ord.fNetTotal, ord.fTotalGenerateFare, ord.fCommision, ord.fOffersDiscount, ord.fDeliveryCharge, ord.iStatusCode, ord.fRatio_" . $currencycode . " as Ratio, ord.fRestaurantPaidAmount, ord.fDriverPaidAmount, ord.eRestaurantPaymentStatus, ord.eAdminPaymentStatus,sc.vServiceName_" . $vLang . " as vServiceCategoryName FROM `orders` as ord LEFT JOIN service_categories as sc on sc.iServiceId=ord.iServiceId WHERE $whereFilter $conditonalFields='$iGeneralUserId' $whereStatusCode ORDER BY tOrderRequestDate DESC ";
    $Data_whole = $obj->MySQLSelect($sql_whole);
    for ($d = 0; $d < count($Data_whole); $d++) {
        $storeIdArr[] = $Data_whole[$d]['iCompanyId'];
        $addressIdArr[] = $Data_whole[$d]['iUserAddressId'];
        $orderIdArr[] = $Data_whole[$d]['iOrderId'];
        $totalOrder += 1;
    }
    $takeaway_orderstaus = '';
    if ($enable_takeaway == 0) {
        $takeaway_orderstaus = " AND eTakeaway != 'Yes'";
    }
    $subStatusArr = array();
    $getOrderStatus = $obj->MySQLSelect("SELECT vStatus_" . $vLang . " As vTitle,iStatusCode,eTakeaway FROM order_status WHERE iStatusCode IN (6,8)" . $takeaway_orderstaus);
    if ($UserType == "Driver" || $UserType == "Company") {
        $optionArr = array();
        $optionArr['vSubFilterParam'] = "";
        $optionArr['vTitle'] = $languageLabelsArr['LBL_ALL']; //added by SP on cubex design for 01-10-2019
        $subStatusArr[] = $optionArr;
        for ($s = 0; $s < count($getOrderStatus); $s++) {
            if ($getOrderStatus[$s]['iStatusCode'] == 6 && $getOrderStatus[$s]['eTakeaway'] == 'Yes' && $enable_takeaway == 1) {
                $optionArr['vSubFilterParam'] = "6-1";
            }
            else {
                $optionArr['vSubFilterParam'] = $getOrderStatus[$s]['iStatusCode'];
            }
            //$optionArr['vSubFilterParam'] = $getOrderStatus[$s]['iStatusCode'];
            $optionArr['vTitle'] = $getOrderStatus[$s]['vTitle'];
            $subStatusArr[] = $optionArr;
        }

        $dSetupDate = $setupInfoDataArr[0]['dSetupDate'];
        $yearFromDate = date('Y-m-d 00:00:00', strtotime($dSetupDate));
        $yearToDate = date('Y-m-d 23:59:59', strtotime($vConvertFromDate));

        $getMonthOrders = $obj->MySQLSelect("SELECT tOrderRequestDate FROM orders WHERE (tOrderRequestDate BETWEEN '$yearFromDate' AND '$yearToDate') AND $conditonalFields='$iGeneralUserId' $whereStatusCode ");
        $MonthOrdersArr = array();
        foreach ($getMonthOrders as $monthOrder) {
            $MonthOrdersArr[] = date('Y-m-d', strtotime($monthOrder['tOrderRequestDate']));
        }

        // $returnData['NO_DATA'] = getMissingdate($MonthOrdersArr, $vConvertFromDate);
        $returnData['EARNING_DATA'] = getEarningDates($MonthOrdersArr);
    }
    $returnData['eFilterSel'] = $filterSelected;
    $returnData['TotalOrder'] = strval($totalOrder);
    $returnData['subFilterOption'] = $subStatusArr;

    if (count($Data) > 0) {
        if (count($storeIdArr) > 0) {
            $storeIds = implode($storeIdArr, ",");
            $addressIds = implode($addressIdArr, ",");
            $orderIds = implode($orderIdArr, ",");
            /* $getCompanyData = $obj->MySQLSelect("SELECT vImage,iCompanyId FROM company WHERE iCompanyId IN ($storeIds)");
              for ($c = 0; $c < count($getCompanyData); $c++) {
              $storeImageArr[$getCompanyData[$c]['iCompanyId']] = $getCompanyData[$c]['vImage'];
          } */
            $getDeliveryAddress = $obj->MySQLSelect("SELECT iUserAddressId,vServiceAddress FROM user_address WHERE iUserAddressId IN ($addressIds) AND eUserType='Rider'");
            for ($a = 0; $a < count($getDeliveryAddress); $a++) {
                $orderAddressArr[$getDeliveryAddress[$a]['iUserAddressId']] = $getDeliveryAddress[$a]['vServiceAddress'];
            }
        }
        if ($UserType == "" || $UserType == NULL) {
            $UserType = "Company";
        }
        //$getDriverRateData = $obj->MySQLSelect("SELECT SUM(vRating1) vRating1 FROM ratings_user_driver WHERE eUserType='Passenger' AND iOrderId IN ($orderIds)";)
        if ($UserType == 'Driver') {
            $getDriverRateData = $obj->MySQLSelect("SELECT SUM(vRating1) vRating1 FROM ratings_user_driver WHERE eFromUserType='Passenger' AND eToUserType='Driver' AND iOrderId IN ($orderIds) ");
            $getAvgRating = FetchUserAvgRating($iGeneralUserId,'Driver');
        }
        else if ($UserType == 'Company') {
            $getDriverRateData = $obj->MySQLSelect("SELECT SUM(vRating1) vRating1 FROM ratings_user_driver WHERE eFromUserType='Passenger' AND eToUserType='Company' AND iOrderId IN ($orderIds) ");

            $getAvgRating = FetchUserAvgRating($iGeneralUserId,'Company');
        }
        $totalDriverRate = $driverAvgRate = 0;
        if (count($getDriverRateData) > 0) {
            $totalDriverRate = $getDriverRateData[0]['vRating1'];
        }
        if ($totalOrder > 0) {
            //$driverAvgRate = $totalDriverRate / $totalOrder;
            $driverAvgRate = $getAvgRating; 
        }
        $driverAvgRate = round($driverAvgRate, 2);
        $imgUrl = $tconfig['tsite_upload_images_compnay'];
        //$seviceCategoriescount = getServiceCategoryCounts();//added by SP on 30-09-2019 for cubex to get service category name as per HJ added on DisplayActiveOrder //commented bc for grocery it takes from conf file not from db..bc in table all entries are there..
        $seviceCategoriescount = count($service_categories_ids_arr);
        for ($i = 0; $i < count($Data); $i++) {
            $priceRatio = $Data[$i]['Ratio'];
            $date = converToTz($Data[$i]['tOrderRequestDate'], $vTimeZone, $systemTimeZone, "Y-m-d H:i:s");
            $OrderTime = date('h:i A', strtotime($date));
            $OrderTimeNew = date('d M Y', strtotime($date));
            $dateName = get_day_name(strtotime($date));
            if (array_key_exists($dateName, $existingArr)) {
                continue;
            }
            $odata[$count]['vDate'] = $dateName;
            $existingArr[$dateName] = "Yes";
            $subDataCount = 0;
            for ($j = 0; $j < count($Data); $j++) {
                $date_tmp = converToTz($Data[$j]['tOrderRequestDate'], $vTimeZone, $systemTimeZone, "Y-m-d H:i:s");
                $dateName_tmp = get_day_name(strtotime($date_tmp));
                if ($dateName == $dateName_tmp) {
                    $storeImg = $delAddress = "";
                    /* if (isset($storeImageArr[$Data[$j]['iCompanyId']])) {
                      $storeImg = $storeImageArr[$Data[$j]['iCompanyId']];
                  } */
                    if (isset($orderAddressArr[$Data[$j]['iUserAddressId']])) {
                        $delAddress = $orderAddressArr[$Data[$j]['iUserAddressId']];
                    }
                    $date_j = converToTz($Data[$j]['tOrderRequestDate'], $vTimeZone, $systemTimeZone, "Y-m-d H:i:s");
                    $OrderTime_j = date('d M, Y h:i A', strtotime($date_j)); //h:iA
                    $OrderTimeNew_j = date('d M Y', strtotime($date_j));
                    $uniquedate = date('jnY', strtotime($date_j));
                    $odata[$count]['Data'][$subDataCount]['iUniqueId'] = $uniquedate;
                    $odata[$count]['Data'][$subDataCount]['iOrderId'] = $Data[$j]['iOrderId'];
                    $odata[$count]['Data'][$subDataCount]['vOrderNo'] = $Data[$j]['vOrderNo'];
                    $odata[$count]['Data'][$subDataCount]['iServiceId'] = $Data[$j]['iServiceId'];
                    $odata[$count]['Data'][$subDataCount]['iStatusCode'] = $Data[$j]['iStatusCode'];
                    $odata[$count]['Data'][$subDataCount]['vCompany'] = $Data[$j]['vCompany'];
                    $odata[$count]['Data'][$subDataCount]['tOrderRequestDate_Org'] = $date_j;
                    //$odata[$count]['Data'][$subDataCount]['vImage'] = $imgUrl . "/" . $Data[$j]['iCompanyId'] . "/" . $storeImg;
                    $odata[$count]['Data'][$subDataCount]['vUserAddress'] = $delAddress;
                    //added by SP on 30-09-2019 for  cubex to get service category name as per HJ added on DisplayActiveOrder start
                    if ($seviceCategoriescount > 1) {
                        $odata[$count]['Data'][$subDataCount]['vServiceCategoryName'] = $Data[$j]['vServiceCategoryName'];
                    }
                    else {
                        $odata[$count]['Data'][$subDataCount]['vServiceCategoryName'] = '';
                    }
                    $odata[$count]['Data'][$subDataCount]['eBuyAnyService'] = $Data[$j]['eBuyAnyService'];
                    if ($Data[$j]['eBuyAnyService'] == "Yes") {
                        $odata[$count]['Data'][$subDataCount]['vServiceCategoryName'] = $languageLabelsArr["LBL_OTHER_DELIVERY"];
                        if ($Data[$j]['eForPickDropGenie'] == "Yes") {
                            $odata[$count]['Data'][$subDataCount]['vServiceCategoryName'] = $languageLabelsArr["LBL_RUNNER"];
                        }
                    }
                    //added by SP on 30-09-2019 for  cubex to get service category name as per HJ added on DisplayActiveOrder end
                    //added by SP on 30-09-2019 for  cubex to get payment option and itemlist start
                    $db_orders = GetOrderDetails($Data[$j]['iOrderId'], $vTimeZone, $UserType);
                    $odata[$count]['Data'][$subDataCount]['ePaymentOption'] = $db_orders[0]['ePaymentOption'];
                    $odata[$count]['Data'][$subDataCount]['itemlist'] = $db_orders[0]['itemlist'];
                    $ratingStore = 0;
                    if ($UserType == 'Driver') {
                        $getUserToCompanyRateData = $obj->MySQLSelect("SELECT SUM(vRating1) vRating1 FROM ratings_user_driver WHERE eFromUserType='Passenger' AND eToUserType='Driver'  AND iOrderId = " . $Data[$j]['iOrderId']);
                    }
                    else if ($UserType == 'Company') {
                        $getUserToCompanyRateData = $obj->MySQLSelect("SELECT SUM(vRating1) vRating1 FROM ratings_user_driver WHERE eFromUserType='Passenger' AND eToUserType='Company' AND iOrderId = " . $Data[$j]['iOrderId']);
                    }
                    if (!empty($getUserToCompanyRateData[0]['vRating1'])) $ratingStore = $getUserToCompanyRateData[0]['vRating1'];
                    $odata[$count]['Data'][$subDataCount]['vAvgRating'] = $ratingStore;
                    //added by SP on 30-09-2019 for  cubex to get payment option and itemlist end
                    $odata[$count]['Data'][$subDataCount]['vCompany'] = $Data[$j]['vCompany'];
                    //$odata[$count]['Data'][$subDataCount]['tOrderRequestDate'] = $storeImageArr;
                    $query1 = "SELECT vName,vLastName,vImgName FROM register_user WHERE iUserId = '" . $Data[$j]['iUserId'] . "'";
                    $orderDetail = $obj->MySQLSelect($query1);
                    $odata[$count]['Data'][$subDataCount]['UseName'] = $orderDetail[0]['vName'] . " " . $orderDetail[0]['vLastName'];
                    if ($Data[$j]['eOrderplaced_by'] == "Kiosk") {
                        $odata[$count]['Data'][$subDataCount]['UseName'] = $Data[$j]['vName'];
                    }
                    if (!empty($orderDetail[0]['vImgName'])) {
                        $odata[$count]['Data'][$subDataCount]['vImage'] = $tconfig["tsite_upload_images_passenger"] . "/" . $Data[$j]['iUserId'] . "/" . $orderDetail[0]['vImgName'];
                    }
                    else {
                        $odata[$count]['Data'][$subDataCount]['vImage'] = $tconfig["tsite_img"] . "/profile-user-img.png";
                    }
                    $query = "SELECT iOrderDetailId FROM order_details WHERE iOrderId = '" . $Data[$j]['iOrderId'] . "'";
                    $orderDetailId = $obj->MySQLSelect($query);
                    $odata[$count]['Data'][$subDataCount]['TotalItems'] = strval(count($orderDetailId));
                    if ($Data[$j]['iStatusCode'] == '11' || $Data[$j]['iStatusCode'] == '9') {
                        $status = $languageLabelsArr["LBL_HISTORY_REST_DECLINED"];
                    }
                    else if ($Data[$j]['iStatusCode'] == '8') {
                        $status = $languageLabelsArr["LBL_HISTORY_REST_CANCELLED"];
                    }
                    else if ($Data[$j]['iStatusCode'] == '7' && $UserType == "Passenger") {
                        $status = $languageLabelsArr["LBL_HISTORY_REST_REFUNDS"];
                    }
                    else if ($Data[$j]['iStatusCode'] == '7' && $UserType != "Passenger") {
                        $status = $languageLabelsArr["LBL_HISTORY_REST_CANCELLED"];
                    }
                    else if ($Data[$j]['iStatusCode'] == '6') {
                        if ($Data[$j]['eTakeaway'] == 'Yes') {
                            $status = $languageLabelsArr["LBL_TAKE_AWAY_ORDER_PICKEDUP_TXT"];
                        }
                        else {
                            $status = $languageLabelsArr["LBL_HISTORY_REST_DELIVERED"];
                        }
                    }
                    else {
                        $status = '';
                    }
                    $odata[$count]['Data'][$subDataCount]['iStatus'] = $status;
                    if ($UserType == 'Driver') {
                        $OrderId = $Data[$j]['iOrderId'];
                        $subquery = "SELECT t.fDeliveryCharge,t.eDriverPaymentStatus,odcd.fDeliveryCharge as fCustomDeliveryCharge,vt.fDeliveryCharge as fDeliveryChargeVehicle FROM trips as t LEFT JOIN order_delivery_charge_details as odcd ON odcd.iOrderId = t.iOrderId LEFT JOIN vehicle_type as vt ON vt.iVehicleTypeId = t.iVehicleTypeId WHERE t.iOrderId = '" . $OrderId . "' AND t.iActive != 'Canceled'";
                        $DriverCharge = $obj->MySQLSelect($subquery);
                        if ($Data[$j]['iStatusCode'] == '7' || $Data[$j]['iStatusCode'] == '8') {
                            $EarningFare = $Data[$j]['fDriverPaidAmount'];
                        }
                        else {
                            $EarningFare = $DriverCharge[0]['fDeliveryCharge'];
                            // $EarningFare = $EarningFare - ($EarningFare - ($DriverCharge[0]['fCustomDeliveryCharge'] + $DriverCharge[0]['fDeliveryChargeVehicle']));
                            $subtotal = 0;
                            if ($Data[$j]['eBuyAnyService'] == "Yes" && $Data[$j]['ePaymentOption'] == "Card") {
                                $order_buy_anything = $obj->MySQLSelect("SELECT * FROM order_items_buy_anything WHERE iOrderId = '" . $OrderId . "'");
                                if (count($order_buy_anything) > 0) {
                                    foreach ($order_buy_anything as $oItem) {
                                        if ($oItem['eConfirm'] == "Yes") {
                                            $subtotal += $oItem['fItemPrice'];
                                        }
                                    }
                                }
                            }
                            $EarningFare = $EarningFare + $Data[$j]['fTipAmount'] + $subtotal;
                        }
                    }
                    else if ($UserType == 'Passenger') {
                        $EarningFare = $Data[$j]['fNetTotal'];
                    }
                    else {
                        if ($Data[$j]['iStatusCode'] == '7' || $Data[$j]['iStatusCode'] == '8') {
                            $EarningFare = $Data[$j]['fRestaurantPaidAmount'];
                        }
                        else {
                            //added by SP on 08-09-2020 because solving mantis bugs 0013929 - wrong calculation
                            $EarningFare = $Data[$j]['fTotalGenerateFare'] - ($Data[$j]['fCommision'] + $Data[$j]['fOffersDiscount'] + $Data[$j]['fDeliveryCharge'] + $Data[$j]['fOutStandingAmount']);
                            // $EarningFare = $Data[$j]['fTotalGenerateFare'] - ($Data[$j]['fCommision'] + $Data[$j]['fDeliveryCharge'] + $Data[$j]['fOutStandingAmount']);
                            $EarningFare = $EarningFare - $Data[$j]['fTipAmount'];
                        }
                    }
                    $returnArr['fTotalGenerateFare'] = setTwoDecimalPoint($EarningFare) * $priceRatio;
                    $fTotalGenerateFare = formatNum($returnArr['fTotalGenerateFare']);
                    if ($fTotalGenerateFare == 0 && $Data[$j]['eRestaurantPaymentStatus'] == "Unsettelled") {
                        $odata[$count]['Data'][$subDataCount]['EarningFare'] = '';
                    }
                    else {
                        $odata[$count]['Data'][$subDataCount]['EarningFare'] = formateNumAsPerCurrency($fTotalGenerateFare, $currencycode);
                    }
                    $odata[$count]['Data'][$subDataCount]['vService_BG_color'] = RANDOM_COLORS_ARR[array_rand(RANDOM_COLORS_ARR, 1)];
                    $odata[$count]['Data'][$subDataCount]['vService_TEXT_color'] = "#FFFFFF";
                    $subDataCount++;
                }
            }
            $count++;
            // $i++;
        }
        foreach ($odata[0]['Data'] as $okey => $oData) {
            if ($oData['eBuyAnyService'] == "No") {
                $orderStatusAll = $obj->MySQLSelect("SELECT * FROM order_status_logs WHERE iOrderId = " . $oData['iOrderId'] . " AND iStatusCode = 2");
                if (count($orderStatusAll) == 0) {
                    unset($odata[0]['Data'][$okey]);
                    $returnData['TotalOrder']--;
                }
            }
            if ($UserType == 'Driver') {
                $driver_log = $obj->MySQLSelect("SELECT * FROM order_driver_log WHERE iOrderId = " . $oData['iOrderId'] . " AND iDriverId = $iGeneralUserId");
                if (!empty($driver_log) && count($driver_log) > 0) {
                    unset($odata[0]['Data'][$okey]);
                    $returnData['TotalOrder']--;
                }
            }
        }
        $odata[0]['Data'] = array_values($odata[0]['Data']);
        if ($returnData['TotalOrder'] == 0) {
            $returnData['Action'] = "0";
            $totalEarning = "0";
            $returnData['AvgRating'] = number_format(floatval($driverAvgRate), 1, ".", "");
            $returnData['TotalEarning'] = formateNumAsPerCurrency($totalEarning, $currencycode);
            $returnData['message'] = "LBL_NO_DATA_AVAIL";
            setDataResponse($returnData);
        }
        $returnData['message'] = $odata;
        if ($TotalPages > $page) {
            $returnData['NextPage'] = "" . ($page + 1);
        }
        else {
            $returnData['NextPage'] = "0";
        }
        if ($UserType == 'Driver') {
            $totalEarning = TotalOrderPaymentForDriver($iGeneralUserId, $vFromDate, $vToDate, $UserType, $vTimeZone, $vSubFilterParam);
        }
        else if ($UserType == 'Passenger') {
            $totalEarning = TotalOrderPaymentForUser($iGeneralUserId, $vFromDate, $vToDate, $UserType, $vTimeZone);
        }
        else {
            $totalEarning = TotalOrderPaymentForStore($iGeneralUserId, $vFromDate, $vToDate, $UserType, $vTimeZone, $vSubFilterParam);
        }

        $returnData['TotalEarning'] = formateNumAsPerCurrency($totalEarning, $currencycode);
        //$returnData['AvgRating'] = setTwoDecimalPoint($driverAvgRate);
        $returnData['AvgRating'] = number_format(floatval($driverAvgRate), 1, ".", "");
        $returnData['Action'] = "1";
        setDataResponse($returnData);
    }
    else {
        $returnData['Action'] = "0";
        $totalEarning = "0";
        $returnData['AvgRating'] = number_format(floatval($driverAvgRate), 1, ".", "");
        $returnData['TotalEarning'] = formateNumAsPerCurrency($totalEarning, $currencycode);
        $returnData['message'] = "LBL_NO_ORDERS_FOUND_TXT";
        setDataResponse($returnData);
    }
}
// ###############################End getOrderHistory###########################################
// ##################################START FOOD MENU ITEM FOR RESTAURANT########################################
if ($type == "ManageFoodItem") {
    $page = isset($_REQUEST['page']) ? trim($_REQUEST['page']) : 1;
    $iGeneralUserId = isset($_REQUEST["iGeneralUserId"]) ? $_REQUEST["iGeneralUserId"] : "";
    $vTimeZone = isset($_REQUEST["vTimeZone"]) ? $_REQUEST["vTimeZone"] : "Asia/Kolkata";
    $SearchWord = isset($_REQUEST["SearchWord"]) ? $_REQUEST["SearchWord"] : "";
    $UserDetailsArr = getCompanyCurrencyLanguageDetails($iGeneralUserId);
    $currencycode = $UserDetailsArr['currencycode'];
    $vSymbol = $UserDetailsArr['currencySymbol'];
    $priceRatio = $UserDetailsArr['Ratio'];
    $vLang = $UserDetailsArr['vLang'];
    $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLang, "1", $iServiceId);
    $per_page = 10;
    $sql_all = "SELECT COUNT(m.iMenuItemId) As TotalIds FROM food_menu as f LEFT JOIN menu_items as m on  m.iFoodMenuId=f.iFoodMenuId WHERE f.iCompanyId='" . $iGeneralUserId . "' AND m.eStatus!='Deleted'";
    $data_count_all = $obj->MySQLSelect($sql_all);
    $TotalPages = ceil($data_count_all[0]['TotalIds'] / $per_page);
    $start_limit = ($page - 1) * $per_page;
    $limit = " LIMIT " . $start_limit . ", " . $per_page;
    $query = "SELECT fm.iFoodMenuId, fm.iFoodMenuId, fm.vMenu_" . $vLang . " as catName, fm.vMenuDesc_" . $vLang . " as catDesc, mt.eAvailable FROM food_menu as fm, menu_items as mt WHERE fm.iCompanyId = '$iGeneralUserId' AND fm.eStatus = 'Active' AND mt.eStatus = 'Active' AND mt.iFoodMenuId = fm.iFoodMenuId GROUP BY mt.iFoodMenuId ORDER BY fm.iDisplayOrder ASC";
    $Data = $obj->MySQLSelect($query);
    $i = 0;
    $foodItemIds = "";
    $itemCatDataArr = $getItemData = array();
    foreach ($Data as $key => $value) {
        $iFoodMenuId = $value['iFoodMenuId'];
        $foodItemIds .= "," . $iFoodMenuId;
    }
    if ($foodItemIds != "") {
        $trimData = trim($foodItemIds, ",");
        $ssql = '';
        if (!empty($SearchWord)) $ssql = " AND vItemType_" . $vLang . " Like '%$SearchWord%'";
        $getItemData = $obj->MySQLSelect("SELECT iFoodMenuId,iMenuItemId,vImage,vItemType_" . $vLang . " as menuitemname, vItemDesc_" . $vLang . " as menuitemdesc, fPrice, eFoodType,eAvailable FROM menu_items WHERE iFoodMenuId IN ($trimData) and eStatus = 'Active' $ssql ORDER BY iDisplayOrder DESC");
    }
    for ($r = 0; $r < count($getItemData); $r++) {
        $foodItemId = $getItemData[$r]['iFoodMenuId'];
        $itemCatDataArr[$foodItemId][] = $getItemData[$r];
    }
    if (count($Data) > 0) {
        foreach ($Data as $key => $value) {
            $CategoryData[$i]['CategoryName'] = $value['catName'];
            $iFoodMenuId = $value['iFoodMenuId'];
            // $subQuery = "SELECT iMenuItemId,vItemType_".$vLang." as menuitemname, vItemDesc_".$vLang." as menuitemdesc, fPrice, eFoodType,eAvailable FROM menu_items WHERE iFoodMenuId = '".$iFoodMenuId."' ORDER BY iDisplayOrder DESC". $limit;
            //$subQuery = "SELECT iMenuItemId,vItemType_" . $vLang . " as menuitemname, vItemDesc_" . $vLang . " as menuitemdesc, fPrice, eFoodType,eAvailable FROM menu_items WHERE iFoodMenuId = '" . $iFoodMenuId . "' ORDER BY iDisplayOrder DESC";
            //$MenuItemData = $obj->MySQLSelect($subQuery);
            $returnDataArr = $MenuItemData = array();
            if (isset($itemCatDataArr[$iFoodMenuId])) {
                $MenuItemData = $itemCatDataArr[$iFoodMenuId];
            }
            foreach ($MenuItemData as $k => $val) {
                $returnDataArr[$k]['vService_BG_color'] = RANDOM_COLORS_ARR[array_rand(RANDOM_COLORS_ARR, 1)];
                $returnDataArr[$k]['vService_TEXT_color'] = "#FFFFFF";
                $returnDataArr[$k]['MenuItemName'] = $val['menuitemname'];
                $returnDataArr[$k]['iMenuItemId'] = $val['iMenuItemId'];
                $returnDataArr[$k]['iFoodMenuId'] = $iFoodMenuId;
                $returnDataArr[$k]['MenuItemDesc'] = $val['menuitemdesc'];
                $oldImage = $val['vImage'];
                $imgpth = $tconfig["tsite_upload_images_menu_item_path"] . '/' . $oldImage;
                if ($oldImage != "" && file_exists($imgpth)) {
                    $returnDataArr[$k]['vImage'] = $imgUrl = $tconfig["tsite_upload_images_menu_item"] . '/' . $oldImage;
                }
                else {
                    $returnDataArr[$k]['vImage'] = $imgUrl = $tconfig["tsite_upload_images_menu_item"] . '/sample_image.png';
                }
                $returnArr['fPrice'] = $val['fPrice'] * $priceRatio;
                $fPrice = formatNum($returnArr['fPrice']);
                //$returnDataArr[$k]['fPrice'] = $vSymbol . $fPrice;
                $returnDataArr[$k]['fPrice'] = formateNumAsPerCurrency($fPrice, $currencycode);
                $returnDataArr[$k]['eAvailable'] = $val['eAvailable'];
            }
            $CategoryData[$i]['Data'] = $returnDataArr;
            $i++;
        }
        // ## Checking For Pagination ###
        $per_page = 10;
        $TotalPages = ceil(count($CategoryData) / $per_page);
        $pagecount = $page - 1;
        $start_limit = $pagecount * $per_page;
        $CategoryData = array_slice($CategoryData, $start_limit, $per_page);
        // ## Checking For Pagination ###
        $returnData['message'] = $CategoryData;
        if ($TotalPages > $page) {
            $returnData['NextPage'] = $page + 1;
        }
        else {
            $returnData['NextPage'] = "0";
        }
        $returnData['Action'] = "1";
        setDataResponse($returnData);
    }
    else {
        $returnData['Action'] = "0";
        $returnData['message'] = "LBL_NOTE_NO_FOOD_ITEMS";
        setDataResponse($returnData);
    }
}
// #################################END FOOD MENU ITEM FOR RESTAURANT#########################################
// #################################Update Foodmenu Item For Restaurant#########################################
if ($type == "UpdateFoodMenuItemForRestaurant") {
    // // Commented By HJ On 15-07-2020 Bcoz Not Required
    $iMenuItemId = isset($_REQUEST["iMenuItemId"]) ? $_REQUEST["iMenuItemId"] : "";
    $eAvailable = isset($_REQUEST["eAvailable"]) ? $_REQUEST["eAvailable"] : "Yes";
    $where = " iMenuItemId = '$iMenuItemId'";
    $Data_update_menuItem['eAvailable'] = $eAvailable;
    $id = $obj->MySQLQueryPerform("menu_items", $Data_update_menuItem, 'update', $where);
    if ($id) {
        $returnData['Action'] = "1";
        $returnData['message'] = "LBL_INFO_UPDATED_TXT";
        setDataResponse($returnData);
    }
    else {
        $returnData['Action'] = "0";
        $returnData['message'] = "LBL_NO_DATA_AVAIL";
        setDataResponse($returnData);
    }
}
// #################################end Update Foodmenu Item For Restaurant######################################
// ############################################## Order Pickup Type ########################################
if ($type == "UpdateOrderStatusDriver") {
    $iTripId = isset($_REQUEST["iTripid"]) ? $_REQUEST["iTripid"] : "";
    $iOrderId = isset($_REQUEST["iOrderId"]) ? $_REQUEST["iOrderId"] : "";
    $iDriverId = isset($_REQUEST["iDriverId"]) ? $_REQUEST["iDriverId"] : "";
    $orderStatus = isset($_REQUEST["orderStatus"]) ? $_REQUEST["orderStatus"] : "";
    $billAmount = isset($_REQUEST["billAmount"]) ? $_REQUEST["billAmount"] : "";
    $UserType = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : "Driver";
    $genieWaitingForUserApproval = isset($_REQUEST["genieWaitingForUserApproval"]) ? $_REQUEST["genieWaitingForUserApproval"] : "No";
    $fields = "iUserId,iDriverId,iCompanyId,fNetTotal,ePaymentOption,ePaid,vOrderNo,vCouponCode,fRoundingAmount,eRoundingType,eCheckUserWallet,fWalletDebit,eBuyAnyService,fOutStandingAmount,eAskCodeToUser,vRandomCode,eSMSSendToUser,eOrderplaced_by,iServiceId,tOutStandingIds";
    $OrderData = get_value('orders', $fields, 'iOrderId', $iOrderId);
    $iUserId = $OrderData[0]['iUserId'];
    $iCompanyId = $OrderData[0]['iCompanyId'];
    $iDriverId = $OrderData[0]['iDriverId'];
    $ePaymentOption = $OrderData[0]['ePaymentOption'];
    $ePaid = $OrderData[0]['ePaid'];
    $vOrderNo = $OrderData[0]['vOrderNo'];
    $vCouponCode = $OrderData[0]['vCouponCode'];
    $tOutStandingIds = $OrderData[0]['tOutStandingIds'];
    $UserDetailsArr = getDriverCurrencyLanguageDetails($OrderData[0]['iDriverId'], $iOrderId);
    $vSymbol = $UserDetailsArr['currencySymbol'];
    $priceRatio = $UserDetailsArr['Ratio'];
    $vLang = $UserDetailsArr['vLang'];
    //$vLangUser = get_value('register_user', 'vLang', 'iUserId', $iUserId, '', true);
    if ($UserType == "Driver") {
        $UserDetailsArr = getUserCurrencyLanguageDetails($iUserId, $iOrderId);
        $vLang = $UserDetailsArr['vLang'];
    }
    $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLang, "1", $iServiceId);
    $confirmprice = getPriceUserCurrency($OrderData[0]['iDriverId'], "Driver", $OrderData[0]['fNetTotal'], $iOrderId);
    //added by SP for rounding off currency wise on 19-11-2019 start
    $query = "SELECT iTripId,eSystem,vCurrencyDriver,vCurrencyPassenger,fRoundingAmountDriver,eRoundingTypeDriver FROM `trips` WHERE iOrderId = '" . $iOrderId . "' AND iActive != 'Canceled' ORDER BY iTripId DESC";
    $TripsData = $obj->MySQLSelect($query);
    $tripeSystem = $TripsData[0]['eSystem'];
    $iTripId = $TripsData[0]['iTripId'];
    $sqlp = "SELECT cu.vName, cu.iCurrencyId, cu.eRoundingOffEnable, rd.vCurrencyDriver, cu.ratio FROM register_driver AS rd LEFT JOIN currency AS cu ON rd.vCurrencyDriver = cu.vName WHERE rd.iDriverId = '" . $OrderData[0]['iDriverId'] . "'";
    $currData = $obj->MySQLSelect($sqlp);
    $vCurrency = $currData[0]['vName'];
    $samecur = ($TripsData[0]['vCurrencyDriver'] == $currData[0]['vCurrencyDriver'] && $TripsData[0]['vCurrencyDriver'] == $TripsData[0]['vCurrencyPassenger']) ? 1 : 0;
    if (isset($OrderData[0]['fRoundingAmount']) && !empty($OrderData[0]['fRoundingAmount']) && $OrderData[0]['fRoundingAmount'] != 0 && $samecur == 1 && $currData[0]['eRoundingOffEnable'] == "Yes" && $ePaymentOption == "Cash" && $MODULES_OBJ->isEnableRoundingMethod()) {
        $roundingOffTotal_fare_amountArr['method'] = $OrderData[0]['eRoundingType'];
        $roundingOffTotal_fare_amountArr['differenceValue'] = $OrderData[0]['fRoundingAmount'];
        $roundingOffTotal_fare_amountArr = getRoundingOffAmounttrip($confirmprice['fPrice'], $OrderData[0]['fRoundingAmount'], $OrderData[0]['eRoundingType']);
        if ($roundingOffTotal_fare_amountArr['method'] == "Addition") {
            $roundingMethod = "";
        }
        else {
            $roundingMethod = "-";
        }
        $confirmprice['fPrice'] = isset($roundingOffTotal_fare_amountArr['finalFareValue']) && $roundingOffTotal_fare_amountArr['finalFareValue'] != '' ? $roundingOffTotal_fare_amountArr['finalFareValue'] : "0.00"; // Commented By HJ On 16-12-2019 As Per Discuss With SP
    }
    // new for rounding data
    if ($TripsData[0]['vCurrencyDriver'] == $currData[0]['vCurrencyDriver'] && $currData[0]['eRoundingOffEnable'] == "Yes" && $TripsData[0]['vCurrencyDriver'] != $TripsData[0]['vCurrencyPassenger'] && $MODULES_OBJ->isEnableRoundingMethod()) {
        if (isset($TripsData[0]['fRoundingAmountDriver']) && !empty($TripsData[0]['fRoundingAmountDriver']) && $TripsData[0]['fRoundingAmountDriver'] != 0) {
            $roundingOffTotal_fare_amountArr['method'] = $TripsData[0]['eRoundingTypeDriver'];
            $roundingOffTotal_fare_amountArr['differenceValue'] = $TripsData[0]['fRoundingAmountDriver'];
            $roundingOffTotal_fare_amountArr = getRoundingOffAmounttrip($confirmprice['fPrice'], $TripsData[0]['fRoundingAmountDriver'], $TripsData[0]['eRoundingTypeDriver']);
            if ($roundingOffTotal_fare_amountArr['method'] == "Addition") {
                $roundingMethod = "";
            }
            else {
                $roundingMethod = "-";
            }
            $confirmprice['fPrice'] = isset($roundingOffTotal_fare_amountArr['finalFareValue']) && $roundingOffTotal_fare_amountArr['finalFareValue'] != '' ? $roundingOffTotal_fare_amountArr['finalFareValue'] : "0.00";
        }
    }
    //added by SP for rounding off currency wise on 19-11-2019 end
    if (!empty($iOrderId)) {
        $sql = "select ru.iUserId, ord.iStatusCode, ord.vOrderNo from orders as ord LEFT JOIN register_user as ru ON ord.iUserId=ru.iUserId where ord.iOrderId = '" . $iOrderId . "' AND ord.iStatusCode='8'";
        $data_order = $obj->MySQLSelect($sql);
        if (count($data_order) > 0) {
            $returnArr['Action'] = "1";
            $returnArr['message'] = "LBL_CANCEL_ORDER_ADMIN_TXT";
            $returnArr['DO_RESTART'] = "Yes";
            setDataResponse($returnArr);
        }
    }
    if ($orderStatus == "OrderPickedup") {
        $billAmount = $confirmprice['fPrice'];
        ## Add On for Checking Driver Distance in nearest to Store Pick Up Address ##
        if ($MODULES_OBJ->isEnableDriverArrivalDistance()) {
            $driverLat = isset($_REQUEST["vLatitude"]) ? $_REQUEST["vLatitude"] : '';
            $driverLon = isset($_REQUEST["vLongitude"]) ? $_REQUEST["vLongitude"] : '';
            $StoreData = $obj->MySQLSelect("SELECT vRestuarantLocationLat,vRestuarantLocationLong FROM company WHERE iCompanyId='" . $iCompanyId . "'");
            $StoreLat = $StoreData[0]['vRestuarantLocationLat'];
            $StoreLong = $StoreData[0]['vRestuarantLocationLong'];
            $driverdistance = distanceByLocation($driverLat, $driverLon, $StoreLat, $StoreLong, "K");
            $driverdistance = $driverdistance * 1000;
            if ($driverdistance > $DRIVER_ARRIVAL_DISTANCE_TO_USER_PICKUP_ADDRESS) {
                $returnArr['Action'] = "0";
                $returnArr['message'] = "LBL_DRIVER_ARRIVED_STORE_DISTANCE_TXT";
                setDataResponse($returnArr);
            }
        }
        ## Add On for Checking Driver Distance in nearest to Store Pick Up Address ##
    }

    if ($MODULES_OBJ->isDeliveryPreferenceEnable()) {
        if ($orderStatus == "OrderDelivered") {
            // Upload Delivery Preference Image
            $image_name = $vImage = isset($_FILES['vImage']['name']) ? $_FILES['vImage']['name'] : '';
            $image_object = isset($_FILES['vImage']['tmp_name']) ? $_FILES['vImage']['tmp_name'] : '';
            if ($image_object != "") {
                if ($image_object) {
                    ExifCleaning::adjustImageOrientation($image_object);
                }
                $where = " iOrderId = '$iOrderId'";
                if ($image_name != "") {
                    $Photo_Gallery_folder = $tconfig['tsite_upload_order_delivery_pref_images_path'];
                    if (!is_dir($Photo_Gallery_folder)) {
                        mkdir($Photo_Gallery_folder, 0777);
                        chmod($Photo_Gallery_folder, 0777);
                    }
                    $vFile = $UPLOAD_OBJ->GeneralFileUpload($Photo_Gallery_folder, $image_object, $image_name, $prefix = '', $vaildExt = "bmp,pdf,doc,docx,jpg,jpeg,gif,png,xls,xlsx,csv");
                    $vImageName = $vFile[0];
                    $Data_update_order['vImageDeliveryPref'] = $vImageName;
                    $vImageDeliveryPref = $tconfig['tsite_upload_order_delivery_pref_images'] . $vImageName;
                    $obj->MySQLQueryPerform("orders", $Data_update_order, 'update', $where);
                }
            }
        }
    }
    if (empty($billAmount) && $orderStatus == "OrderDelivered") {
        $billAmount = $confirmprice['fPrice'];
    }
    if ($billAmount == "") {
        $billAmount = 0;
    }
    $billAmount = setTwoDecimalPoint(str_replace(",", "", $billAmount));
    if (isset($confirmprice['fPrice']) && $confirmprice['fPrice'] == "") {
        $confirmprice['fPrice'] = setTwoDecimalPoint(0);
    }
    if ($confirmprice['fPrice'] == $billAmount) {
        $sql = "SELECT vCurrencyPassenger,iAppVersion,iUserPetId FROM `register_user` WHERE iUserId = '$iUserId'";
        $Data_passenger_detail = $obj->MySQLSelect($sql);
        $sql = "SELECT iDriverVehicleId,vCurrencyDriver,iAppVersion,CONCAT(vName,' ',vLastName) AS driverName FROM `register_driver` WHERE iDriverId = '$iDriverId'";
        $Data_vehicle = $obj->MySQLSelect($sql);
        $drivername = $Data_vehicle[0]['driverName'];
        $sql = "SELECT vt.fDeliveryCharge,vt.iVehicleTypeId from vehicle_type as vt LEFT JOIN trips as tr ON tr.iVehicleTypeId=vt.iVehicleTypeId WHERE iTripId = '" . $iTripId . "'";
        $Data_trip_vehicle = $obj->MySQLSelect($sql);
        $fDeliveryCharge = $Data_trip_vehicle[0]['fDeliveryCharge'];
        $eBuyAnyServiceOrd = get_value('orders', 'eBuyAnyService', 'iOrderId', $iOrderId, '', 'true');
        // Notify only user
        $DriverMessage = $orderStatus;
        if ($orderStatus == 'OrderPickedup') {
            if ($OrderData[0]['eBuyAnyService'] == "Yes") {
                $itemDetails = $obj->MySQLSelect("SELECT eItemAvailable FROM order_items_buy_anything WHERE iOrderId = $iOrderId");
                $itemsAvailability = "No";
                foreach ($itemDetails as $sItem) {
                    if ($sItem['eItemAvailable'] == "Yes") {
                        $itemsAvailability = "Yes";
                    }
                }
                if ($itemsAvailability == "No") {
                    $returnArr['Action'] = "0";
                    $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
                    $returnArr['itemsAvailability'] = "No";
                    setDataResponse($returnArr);
                }
                $Data_update_orders['genieWaitingForUserApproval'] = $genieWaitingForUserApproval;
                $Data_update_Trips['iActive'] = 'On Going Trip';
                $Data_update_driver['vTripStatus'] = 'On Going Trip';
                $genieUserApproved = get_value('orders', 'genieUserApproved', 'iOrderId', $iOrderId, '', 'true');
                $returnArr['genieWaitingForUserApproval'] = $genieWaitingForUserApproval;
                $returnArr['genieUserApproved'] = $genieUserApproved;
                $returnArr['ePaid'] = $ePaid;
                $returnArr['itemsAvailability'] = $itemsAvailability;
                if ($ePaymentOption == "Cash") {
                    $returnArr['ePaid'] = "Yes";
                }
                if ($genieWaitingForUserApproval == "Yes" && $genieUserApproved == "No") {
                    $Data_update_Trips['iActive'] = 'On Going Trip';
                    $Data_update_orders['iStatusCode'] = '13';
                    $Data_update_driver['vTripStatus'] = 'On Going Trip';
                    $Order_Status_id = createOrderLog($iOrderId, "13");
                    $DriverMessage = "OrderReviewItems";
                    $tripdriverarrivlbl = $drivername . " " . $languageLabelsArr['LBL_ORDER_REVIEW_NOTIFICATION_TXT'];
                }
                if (($ePaymentOption == "Cash" && $genieUserApproved == "Yes") || ($ePaid == "Yes" && ($ePaymentOption == "Card" || $ePaymentOption == "Wallet") && $genieUserApproved == "Yes")) {
                    $Data_update_Trips['tDriverArrivedDate'] = @date("Y-m-d H:i:s");
                    $Data_update_Trips['tStartDate'] = @date("Y-m-d H:i:s");
                    $Data_update_Trips['iActive'] = 'On Going Trip';
                    $Data_update_orders['iStatusCode'] = '5';
                    $Data_update_driver['vTripStatus'] = 'On Going Trip';
                    $Order_Status_id = createOrderLog($iOrderId, "5");
                    $tripdriverarrivlbl = $drivername . " " . $languageLabelsArr['LBL_PICKUP_ORDER_NOTIFICATION_TXT'];
                }
            }
            else {
                $Data_update_Trips['tDriverArrivedDate'] = @date("Y-m-d H:i:s");
                $Data_update_Trips['tStartDate'] = @date("Y-m-d H:i:s");
                $Data_update_Trips['iActive'] = 'On Going Trip';
                $Data_update_orders['iStatusCode'] = '5';
                $Data_update_driver['vTripStatus'] = 'On Going Trip';
                $Order_Status_id = createOrderLog($iOrderId, "5");
                // $tripdriverarrivlbl = $languageLabelsArr['LBL_DELIVERY_EXECUTIVE_TXT']." ".$drivername." ".$languageLabelsArr['LBL_DELIVERY_ON_WAY_TXT']." #".$vOrderNo;
                $tripdriverarrivlbl = $drivername . " " . $languageLabelsArr['LBL_PICKUP_ORDER_NOTIFICATION_TXT'];
            }
            if ($OrderData[0]['eAskCodeToUser'] == "Yes" && $OrderData[0]['eSMSSendToUser'] == "No") {
                $sql = "SELECT vLang,vName,vLastName,vEmail,vPhone,vPhoneCode FROM register_user WHERE iUserId='$iUserId'";
                $UserDetail = $obj->MySQLSelect($sql);
                $Data_Mail['OTP'] = $OrderData[0]['vRandomCode'];
                $Data_Mail['FROMNAME'] = $UserDetail[0]['vName'] . " " . $UserDetail[0]['vLastName'];
                $Data_Mail['vEmail'] = $UserDetail[0]['vEmail'];
                $Data_Mail['DRIVER'] = $languageLabelsArr['LBL_DRIVER'];
                $sendemail = $COMM_MEDIA_OBJ->SendMailToMember("START_TRIP_OTP", $Data_Mail);
                $Data_SMS['OTP'] = $OrderData[0]['vRandomCode'];
                $Data_SMS['DRIVER'] = $languageLabelsArr['LBL_DRIVER'];
                $sms_message_layout = $COMM_MEDIA_OBJ->GetSMSTemplate("START_TRIP_OTP", $Data_SMS, "", $UserDetail[0]['vLang']);
                $result_sms = $COMM_MEDIA_OBJ->SendSystemSMS($UserDetail[0]['vPhone'], $UserDetail[0]['vPhoneCode'], $sms_message_layout);
                if ($result_sms == 1) {
                    $where_update_sms_order = " iOrderId = '$iOrderId'";
                    $Data_update_sms_order['eSMSSendToUser'] = "Yes";
                    $obj->MySQLQueryPerform("orders", $Data_update_sms_order, 'update', $where_update_sms_order);
                }
            }
        }
        else if ($orderStatus == 'OrderDelivered') {
            $enableCommisionDeduct = $MODULES_OBJ->autoDeductDriverCommision("DeliverAll"); // Added By HJ On 16-10-2020 For get Auto Deduct Driver Commision Configuration As Per eSystem
            $fCustomDeliveryCharge = 0;
            if ($MODULES_OBJ->isEnableDistanceWiseDeliveryChargeOrder()) {
                $fCustomDeliveryChargeDetails = get_value('order_delivery_charge_details', 'tDeliveryChargeDetails', 'iOrderId', $iOrderId, '', 'true');
                $Data_update_odcd['fDeliveryCharge'] = 0;
                $Data_update_odcd['iDriverVehicleTypeId'] = 0;
                $fDeliveryCharge = 0;
                if ($fCustomDeliveryChargeDetails != "") {
                    $fCustomDeliveryChargeDetails = json_decode($fCustomDeliveryChargeDetails, true);
                    foreach ($fCustomDeliveryChargeDetails as $dcDetails) {
                        if ($dcDetails['iVehicleTypeId'] == $Data_trip_vehicle[0]['iVehicleTypeId']) {
                            $fCustomDeliveryCharge = $dcDetails['fDeliveryCharge'];
                            $Data_update_odcd['fDeliveryCharge'] = $dcDetails['fDeliveryCharge'];
                            $Data_update_odcd['iDriverVehicleTypeId'] = $dcDetails['iVehicleTypeId'];
                        }
                    }
                }
                $odcdwhere = " iOrderId = '" . $iOrderId . "'";
                $obj->MySQLQueryPerform("order_delivery_charge_details", $Data_update_odcd, 'update', $odcdwhere);
            }
            $Data_update_Trips['iActive'] = 'Finished';
            $Data_update_Trips['tEndDate'] = @date("Y-m-d H:i:s");
            $Data_update_Trips['fDeliveryCharge'] = ($fDeliveryCharge + $fCustomDeliveryCharge);
            $fDeliveryChargeFinal = ($fDeliveryCharge + $fCustomDeliveryCharge);
             /*Add Company Id*/
            $DriverCompanyData = getDriverCompany($acceptediDriverId);
            $Data_update_orders['vDriverCompanyId'] = $DriverCompanyData[0]['iCompanyId'];
            /*Add Company Id*/
            
            if ($OrderData[0]['eBuyAnyService'] == "Yes") {
                $vehicleTypeData = $obj->MySQLSelect("SELECT fCommision FROM vehicle_type WHERE iVehicleTypeId = " . $Data_trip_vehicle[0]['iVehicleTypeId']);
                // $deliveryCharge = $Data_update_Trips['fDeliveryCharge'];
                $deliveryCharge = get_value('orders', 'fDeliveryCharge', 'iOrderId', $iOrderId, '', 'true');
                $fVehicleTypeCommision = $vehicleTypeData[0]['fCommision'];
                $orderCommission = round((($deliveryCharge * $fVehicleTypeCommision) / 100), 2);
                $Data_update_orders['fCommision'] = $orderCommission;
                if ($ePaymentOption == "Cash") {
                    $Data_update_Trips['fDeliveryCharge'] = 0;
                    if ($OrderData[0]['fWalletDebit'] > 0) {
                        $Data_update_Trips['fDeliveryCharge'] = $OrderData[0]['fWalletDebit'];
                    }
                    /* Deduct commission from driver wallet in Cash Payment*/
                    if ($enableCommisionDeduct == 'Yes' && $orderCommission > 0) {
                        $iBalance = $orderCommission;
                        $eType = "Debit";
                        $eFor = "Withdrawl";
                        $tDescription = '#LBL_DEBITED_BOOKING_DL# ' . ' ' . $vOrderNo;
                        $ePaymentStatus = 'Settelled';
                        $dDate = @date('Y-m-d H:i:s');
                        $WALLET_OBJ->PerformWalletTransaction($iDriverId, "Driver", $iBalance, $eType, $iTripId, $eFor, $tDescription, $ePaymentStatus, $dDate, $iOrderId, 0);
                        $Where_Order1 = " iTripId = '$iTripId'";
                        $Data_update_driver_paymentstatus1['eDriverPaymentStatus'] = "Settelled";
                        $Update_Payment_Id = $obj->MySQLQueryPerform("trips", $Data_update_driver_paymentstatus1, 'update', $Where_Order1);
                    }
                    /* Deduct commission from driver wallet in Cash Payment End*/
                }
                else {
                    $Data_update_Trips['fDeliveryCharge'] = $deliveryCharge - $orderCommission;
                }
                $fDeliveryChargeFinal = $Data_update_Trips['fDeliveryCharge'];
            }
            if ($ePaymentOption == "Cash") {
                $Data_update_orders['ePaid'] = "Yes";
            }
            $Data_update_orders['dDeliveryDate'] = @date("Y-m-d H:i:s");
            $Data_update_orders['iStatusCode'] = '6';
            $Data_update_driver['vTripStatus'] = 'Finished';
            $Order_Status_id = createOrderLog($iOrderId, "6");
            $tripdriverarrivlbl = $languageLabelsArr['LBL_DELIVERY_EXECUTIVE_TXT'] . " " . $drivername . " " . $languageLabelsArr['LBL_DELIVERY_DELIVER_TXT'] . " #" . $vOrderNo;
            $genie_items_subtotal = 0;
            if ($OrderData[0]['eBuyAnyService'] == "Yes" && $ePaymentOption == "Card") {
                $order_buy_anything = $obj->MySQLSelect("SELECT * FROM order_items_buy_anything WHERE iOrderId = '" . $iOrderId . "'");
                if (count($order_buy_anything) > 0) {
                    foreach ($order_buy_anything as $oItem) {
                        if ($oItem['eConfirm'] == "Yes") {
                            $genie_items_subtotal += $oItem['fItemPrice'];
                        }
                    }
                }
                $fDeliveryChargeFinal = $fDeliveryChargeFinal + $genie_items_subtotal;
            }
            /* added by PM for Auto credit wallet driver on 25-01-2020 start */
            if ($MODULES_OBJ->isAutoCreditToDriverModuleAvailable()) {
                $Dataorder = array();
                $Dataorder['ePaymentOption'] = $ePaymentOption;
                $Dataorder['iUserId'] = $iUserId;
                $Dataorder['vOrderNo'] = $vOrderNo;
                $Dataorder['iOrderId'] = $iOrderId;
                $Dataorder['iTripId'] = $iTripId;
                $Dataorder['iDriverId'] = $iDriverId;
                $Dataorder['fDeliveryCharge'] = $fDeliveryChargeFinal;
                if(!empty($tOutStandingIds)) {
                    $Dataorder['tOutStandingIds'] = $tOutStandingIds;
                }
                //AutoCreditWalletDriver($Dataorder, "UpdateOrderStatusDriver", $iServiceId);
                if ($Dataorder['fDeliveryCharge'] > 0) {
                    autoCreditDriverEarning($Dataorder, "UpdateOrderStatusDriver");
                }
                $fTipAmount = get_value('orders', 'fTipAmount', 'iOrderId', $iOrderId, '', 'true');
                if ($fTipAmount > 0) {
                    $Data['price'] = $fTipAmount;
                    $Data['iDriverId'] = $iDriverId;
                    $Data['iTripId'] = $iTripId;
                    $Data['iOrderId'] = $iOrderId;
                    $Data['vOrderNo'] = $vOrderNo;
                    // AutoCreditWalletDriverOrder($Data, "TripCollectTip", 0);
                    autoCreditDriverEarning($Data, "TripCollectTip");
                }
            }
            else {
                $oSql = "";
                if(!empty($tOutStandingIds)) {
                    $oSql = " AND iTripOutstandId IN ($tOutStandingIds) ";
                }
                $updateQury = "UPDATE trip_outstanding_amount set ePaidByPassenger = 'Yes',vOrderAdjusmentId = '" . $vOrderNo . "' WHERE iUserId = '" . $iUserId . "' AND ePaidByPassenger = 'No' $oSql ";
                $obj->sql_query($updateQury);
            }
            /* added by PM for Auto credit wallet driver on 25-01-2020 start */
            // # Deduct Order Amount From Driver's Wallet Only For Cash Delivered Orders ##
            if ($ePaymentOption == "Cash" && $enableCommisionDeduct == 'Yes' && $OrderData[0]['fNetTotal'] > 0 && $OrderData[0]['eBuyAnyService'] != "Yes") {
                $iBalance = $OrderData[0]['fNetTotal'];
                $eType = "Debit";
                $eFor = "Withdrawl";
                $tDescription = '#LBL_DEBITED_BOOKING_DL# ' . ' ' . $vOrderNo;
                $ePaymentStatus = 'Settelled';
                $dDate = @date('Y-m-d H:i:s');
                // Added By HJ On 18-12-2019 For Prevent Duplication Issue Dicuss with KS Sir Start
                $getPaymentStatus = $obj->MySQLSelect("SELECT iOrderId,iTripId,eUserType,ePaymentStatus,iUserWalletId,eType FROM user_wallet WHERE iTripId='" . $iTripId . "'");
                $walletArray = array();
                for ($h = 0; $h < count($getPaymentStatus); $h++) {
                    $walletArray[$getPaymentStatus[$h]['eType']][$getPaymentStatus[$h]['eUserType']][$getPaymentStatus[$h]['iTripId']] = $getPaymentStatus[$h]['eType'];
                }
                // Added By HJ On 18-12-2019 For Prevent Duplication Issue Dicuss with KS Sir End
                if (!isset($walletArray[$eType]['Driver'][$iTripId])) {
                    $WALLET_OBJ->PerformWalletTransaction($iDriverId, "Driver", $iBalance, $eType, $iTripId, $eFor, $tDescription, $ePaymentStatus, $dDate, $iOrderId, 0);
                }
                $Where_Order = " iTripId = '$iTripId'";
                //$Data_update_driver_paymentstatus['eDriverPaymentStatus'] = "Settelled";
                if ($MODULES_OBJ->isAutoCreditToDriverModuleAvailable()) {
                    $Data_update_driver_paymentstatus['eDriverPaymentStatus'] = "Settelled";
                }
                else {
                    $Data_update_driver_paymentstatus['eDriverPaymentStatus'] = "Unsettelled";
                }
                $Update_Payment_Id = $obj->MySQLQueryPerform("trips", $Data_update_driver_paymentstatus, 'update', $Where_Order);
                // } else if ($ePaymentOption == "Card" && $enableCommisionDeduct == 'Yes' && $OrderData[0]['fNetTotal'] > 0 && $OrderData[0]['eBuyAnyService'] != "Yes" && ($SYSTEM_PAYMENT_FLOW == 'Method-2' || $SYSTEM_PAYMENT_FLOW == 'Method-3')) { //added by SP deduct from driver wallet for method-2 and 3, cash + wallet order.
            }
            else if ($ePaymentOption == "Wallet" && $enableCommisionDeduct == 'Yes' && $OrderData[0]['fNetTotal'] > 0 && $OrderData[0]['eBuyAnyService'] != "Yes") { //added by SP deduct from driver wallet for method-2 and 3, cash + wallet order.
                $iBalance = $OrderData[0]['fNetTotal'];
                $eType = "Debit";
                $eFor = "Withdrawl";
                $tDescription = '#LBL_DEBITED_BOOKING_DL# ' . ' ' . $vOrderNo;
                $ePaymentStatus = 'Settelled';
                $dDate = @date('Y-m-d H:i:s');
                // Added By HJ On 18-12-2019 For Prevent Duplication Issue Dicuss with KS Sir Start
                $getPaymentStatus = $obj->MySQLSelect("SELECT iOrderId,iTripId,eUserType,ePaymentStatus,iUserWalletId,eType FROM user_wallet WHERE iTripId='" . $iTripId . "'");
                $walletArray = array();
                for ($h = 0; $h < count($getPaymentStatus); $h++) {
                    $walletArray[$getPaymentStatus[$h]['eType']][$getPaymentStatus[$h]['eUserType']][$getPaymentStatus[$h]['iTripId']] = $getPaymentStatus[$h]['eType'];
                }
                // Added By HJ On 18-12-2019 For Prevent Duplication Issue Dicuss with KS Sir End
                if (!isset($walletArray[$eType]['Driver'][$iTripId])) {
                    $WALLET_OBJ->PerformWalletTransaction($iDriverId, "Driver", $iBalance, $eType, $iTripId, $eFor, $tDescription, $ePaymentStatus, $dDate, $iOrderId, 0);
                }
                $Where_Order = " iTripId = '$iTripId'";
                if ($MODULES_OBJ->isAutoCreditToDriverModuleAvailable()) {
                    $Data_update_driver_paymentstatus['eDriverPaymentStatus'] = "Settelled";
                }
                else {
                    $Data_update_driver_paymentstatus['eDriverPaymentStatus'] = "Unsettelled";
                }
                //$Data_update_driver_paymentstatus['eDriverPaymentStatus'] = "Settelled";
                $Update_Payment_Id = $obj->MySQLQueryPerform("trips", $Data_update_driver_paymentstatus, 'update', $Where_Order);
            }
            // # Deduct Order Amount From Driver's Wallet Only For Cash Delivered Orders ##
            //orderemaildataDelivered($iOrderId, "Passenger"); //added by SP on 2-7-2019 to work emails properly put it below after update
            // # Update Coupon Used Limit ##
            if ($vCouponCode != '') {
                $Data_update_orders['vCouponCode'] = $vCouponCode;
                $noOfCouponUsed = get_value('coupon', 'iUsed', 'vCouponCode', $vCouponCode, '', 'true');
                $where_coupon = " vCouponCode = '" . $vCouponCode . "'";
                $data_coupon['iUsed'] = $noOfCouponUsed + 1;
                $obj->MySQLQueryPerform("coupon", $data_coupon, 'update', $where_coupon);
                ## Check Coupon Code Usage Limit , Send Email to Admin if Usage  Limit is over ##
                $UpdatedCouponUsedNo = $noOfCouponUsed + 1;
                $sql = "SELECT iUsed, iUsageLimit from coupon WHERE vCouponCode = '" . $vCouponCode . "'";
                $coupon_result = $obj->MySQLSelect($sql);
                if ($iUsageLimit == $UpdatedCouponUsedNo) {
                    $maildata['vCouponCode'] = $vCouponCode;
                    $maildata['iUsageLimit'] = $iUsageLimit;
                    $maildata['COMPANY_NAME'] = $COMPANY_NAME;
                    $mail = $COMM_MEDIA_OBJ->SendMailToMember('COUPON_LIMIT_COMPLETED_TO_ADMIN', $maildata);
                }
                ## Check Coupon Code Usage Limit , Send Email to Admin if Usage  Limit is over ##
            }
            // # Update Coupon Used Limit ##
            if ($OrderData[0]['eBuyAnyService'] == "Yes" && $ePaymentOption == "Cash") {
                createOrderLog($iOrderId, "14");
                $Data_update_Trips['fDeliveryCharge'] = $deliveryCharge - $orderCommission - $OrderData[0]['fOutStandingAmount'];
            }
            /* For Kiosk Order */
            if ($OrderData[0]['eOrderplaced_by'] == "Kiosk") {
                $COMM_MEDIA_OBJ->orderemaildataRecipt($iOrderId, 'Passenger', $OrderData[0]['iServiceId']);
            }
            /* For Kiosk Order End */
        }
        $twhere = " iTripId = '" . $iTripId . "'";
        $TripId = $obj->MySQLQueryPerform("trips", $Data_update_Trips, 'update', $twhere);
        $owhere = " iOrderId = '" . $iOrderId . "'";
        $OrderId = $obj->MySQLQueryPerform("orders", $Data_update_orders, 'update', $owhere);
        $rdwhere = " iDriverId = '" . $OrderData[0]['iDriverId'] . "'";
        $OrderStatus = $obj->MySQLQueryPerform("register_driver", $Data_update_driver, 'update', $rdwhere);
        if ($MODULES_OBJ->isEnableAcceptMultipleOrders()) {
            $orderIds = $obj->MySQLSelect("SELECT COUNT(iOrderId) as order_ids FROM trips WHERE (iActive = 'Active' OR iActive = 'On Going Trip') AND iDriverId = '" . $OrderData[0]['iDriverId'] . "' AND eSystem = 'DeliverAll'");
            if (!empty($orderIds[0]['order_ids']) && $orderIds[0]['order_ids'] > 0) {
                $obj->sql_query("UPDATE register_driver SET vTripStatus = 'On Going Trip' WHERE iDriverId = '" . $OrderData[0]['iDriverId'] . "'");
            }
        }
        // # Deduct Order Amount From Driver's Wallet Only For Cash Delivered Orders ##
        //orderemaildataDelivered($iOrderId, "Passenger"); //added by SP on 2-7-2019 to work emails properly
        $alertMsg = $tripdriverarrivlbl;
        $message_arr = array();
        $message_arr['iDriverId'] = $iDriverId;
        $message_arr['Message'] = $DriverMessage;
        $message_arr['iTripId'] = strval($iTripId);
        $message_arr['DriverAppVersion'] = strval($Data_vehicle[0]['iAppVersion']);
        // $message_arr['driverName'] = $Data_vehicle[0]['vName'] . " " . $Data_vehicle[0]['vLastName'];
        $message_arr['driverName'] = $drivername;
        // $message_arr['vRideNo'] = $TripRideNO;
        $message_arr['iOrderId'] = $iOrderId;
        $message_arr['iServiceId'] = $OrderData[0]['iServiceId'];
        $message_arr['vTitle'] = $alertMsg;
        $message_arr['eSystem'] = "DeliverAll";
        if ($MODULES_OBJ->isDeliveryPreferenceEnable()) {
            if ($orderStatus == "OrderDelivered" && !empty($vImageDeliveryPref)) {
                $message_arr['vImageDeliveryPref'] = $vImageDeliveryPref;
            }
        }
        //added by SP on 02-02-2021 for custom notification
        $message_arr['CustomNotification'] = $MODULES_OBJ->isEnableCustomNotification() ? "Yes" : "No";
        //these two btn CustomViewBtn,CustomTrackDetails whether shown in app or not
        $message_arr['CustomViewBtn'] = "Yes";
        if ($orderStatus == "OrderDelivered") $message_arr['CustomTrackDetails'] = "No";
        else $message_arr['CustomTrackDetails'] = "Yes";
        $message_arr['LBL_VIEW_DETAILS'] = $languageLabelsArr['LBL_VIEW_DETAILS'];
        $message_arr['LBL_TRACK_ORDER'] = $languageLabelsArr['LBL_TRACK_ORDER'];
        $customNotiArray = GetCustomNotificationDetails($iOrderId, $message_arr);
        //title and sub description shown in custom notification
        $message_arr['CustomTitle'] = $customNotiArray[0]['vCurrentStatus'];
        $message_arr['CustomSubTitle'] = $customNotiArray[0]['vCurrentStatus_Track'];
        $message_arr['CustomMessage'] = $customNotiArray;
        $message = json_encode($message_arr);
        // ####################Add Status Message#########################
        /* $DataTripMessages['tMessage']= $message;
          $DataTripMessages['iDriverId']= $iDriverId;
          $DataTripMessages['iTripId']= $iTripId;
          $DataTripMessages['iOrderId']= $iOrderId;
          $DataTripMessages['iUserId']= $iUserId;
          $DataTripMessages['eFromUserType']= "Driver";
          $DataTripMessages['eToUserType']= "Passenger";
          $DataTripMessages['eReceived']= "Yes";
          $DataTripMessages['dAddedDate']= @date("Y-m-d H:i:s");
          $obj->MySQLQueryPerform("trip_status_messages",$DataTripMessages,'insert'); */
        // ###############################################################
        // Notify user and restaurant for OrderDelivered and order Pickup
        if ($iTripId > 0) {
            /* For PubNub Setting */
            $tableName = "register_user";
            $iMemberId_VALUE = $iUserId;
            $iMemberId_KEY = "iUserId";
            $AppData = get_value($tableName, 'iAppVersion,eDeviceType', $iMemberId_KEY, $iMemberId_VALUE);
            $iAppVersion = $AppData[0]['iAppVersion'];
            $eDeviceType = $AppData[0]['eDeviceType'];
            /* For PubNub Setting Finished */
            $sql = "SELECT iGcmRegId,eDeviceType,eAppTerminate,tSessionId,eDebugMode,eHmsDevice FROM register_user WHERE iUserId='$iUserId'";
            $result = $obj->MySQLSelect($sql);
            
            $channelName = "PASSENGER_" . $iUserId;
            $message_arr['tSessionId'] = $result[0]['tSessionId'];
            $generalDataArr[] = array(
                'eDeviceType' => $result[0]['eDeviceType'], 'deviceToken' => $result[0]['iGcmRegId'], 'alertMsg' => $alertMsg, 'eAppTerminate' => $result[0]['eAppTerminate'], 'eDebugMode' => $result[0]['eDebugMode'], 'eHmsDevice' => $result[0]['eHmsDevice'], 'message' => $message_arr, 'channelName' => $channelName,
            );

            if ($OrderData[0]['eBuyAnyService'] == "No") {
                if(strtoupper($eDeviceType) == "IOS") {
                    $tDeviceLiveActivityToken = getLiveActivityDeviceToken($iOrderId, 'Order');
                } else {
                    $tDeviceLiveActivityToken = $result[0]['iGcmRegId'];
                }
                
                if(strtoupper($ENABLE_NOTIFICATION_LIVE_ACTIVITY) == "YES") {
                    $message_arr['LiveActivityData'] = getOrderLiveActivity($iOrderId);
                    $message_arr['LiveActivity'] = "Yes";
                
                    if($orderStatus == 'OrderDelivered') {
                        $message_arr['LiveActivityEnd'] = "Yes";
                    }

                    $generalDataArr[] = array(
                        'eDeviceType' => $result[0]['eDeviceType'], 'deviceToken' => $tDeviceLiveActivityToken, 'alertMsg' => $alertMsg, 'eAppTerminate' => $result[0]['eAppTerminate'], 'eDebugMode' => $result[0]['eDebugMode'], 'eHmsDevice' => $result[0]['eHmsDevice'], 'message' => $message_arr
                    );
                }
            }

            $EVENT_MSG_OBJ->send(array('GENERAL_DATA' => $generalDataArr), RN_USER);
            $sql = "SELECT iGcmRegId,eDeviceType,iAppVersion,tSessionId,eAppTerminate,eDebugMode,eHmsDevice FROM company WHERE iCompanyId='$iCompanyId'";
            $result_company = $obj->MySQLSelect($sql);
            $channelName = "COMPANY_" . $iCompanyId;
            $message_arr['tSessionId'] = $result_company[0]['tSessionId'];
            $generalDataArr = array();
            $generalDataArr[] = array(
                'eDeviceType' => $result_company[0]['eDeviceType'], 'deviceToken' => $result_company[0]['iGcmRegId'], 'alertMsg' => $alertMsg, 'eAppTerminate' => $result_company[0]['eAppTerminate'], 'eDebugMode' => $result_company[0]['eDebugMode'], 'eHmsDevice' => $result_company[0]['eHmsDevice'], 'message' => $message_arr, 'channelName' => $channelName, 'orderEventChannelName' => $orderEventChannelName
            );
            $EVENT_MSG_OBJ->send(array('GENERAL_DATA' => $generalDataArr), RN_COMPANY);
            $returnArr['Action'] = "1";
            if ($orderStatus == 'OrderDelivered') { // Added BY HJ On 09-07-2019 For Prevent Multiple Referrer Amount Issue With Discuss KS
                $REFERRAL_OBJ->CreditReferralAmount($iTripId);
            }

            $returnArr['USER_DATA'] = getDriverDetailInfo($iDriverId);
            setDataResponse($returnArr);
        }
        else {
            $returnArr['Action'] = "0";
            $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
            setDataResponse($returnArr);
        }
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_BILL_VALUE_ERROR_TXT";
        setDataResponse($returnArr);
    }
}
// ############################################# Order Pickup Type #########################################
// ###################################### Image Upload after order Picked up #####################################
if ($type == "OrderImageUpload") {
    $iTripId = isset($_REQUEST["iTripid"]) ? $_REQUEST["iTripid"] : "";
    $iOrderId = isset($_REQUEST["iOrderId"]) ? $_REQUEST["iOrderId"] : "";
    $eImgSkip = isset($_REQUEST["eImgSkip"]) ? $_REQUEST["eImgSkip"] : "";
    $image_name = $vImage = isset($_FILES['vImage']['name']) ? $_FILES['vImage']['name'] : '';
    $image_object = isset($_FILES['vImage']['tmp_name']) ? $_FILES['vImage']['tmp_name'] : '';
    if ($image_object) {
        ExifCleaning::adjustImageOrientation($image_object);
    }
    $where = " iTripId = '$iTripId'";
    if ($image_name != "") {
        $Photo_Gallery_folder = $tconfig['tsite_upload_order_images_path'];
        if (!is_dir($Photo_Gallery_folder)) {
            mkdir($Photo_Gallery_folder, 0777);
            chmod($Photo_Gallery_folder, 0777);
        }
        $vFile = $UPLOAD_OBJ->GeneralFileUpload($Photo_Gallery_folder, $image_object, $image_name, $prefix = '', $vaildExt = "bmp,pdf,doc,docx,jpg,jpeg,gif,png,xls,xlsx,csv");
        $vImageName = $vFile[0];
        $Data_update_trips['vImage'] = $vImageName;
    }
    $Data_update_trips['eImgSkip'] = $eImgSkip;
    $id = $obj->MySQLQueryPerform("trips", $Data_update_trips, 'update', $where);
    if ($id) {
        $returnData['Action'] = "1";
    }
    else {
        $returnData['Action'] = "0";
    }
    setDataResponse($returnData);
}
// ###################################### Image Uplaod after order Picked up #####################################
// ############################ Get State Using country code ######################
if ($type == "GetStatesFromCountry") {
    $iCompanyId = isset($_REQUEST["iCompanyId"]) ? $_REQUEST["iCompanyId"] : "";
    $vCountry = isset($_REQUEST["vCountry"]) ? $_REQUEST["vCountry"] : "";
    $UserDetailsArr = getCompanyCurrencyLanguageDetails($iCompanyId);
    $vLang = $UserDetailsArr['vLang'];
    $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLang, "1", $iServiceId);
    if ($vCountry == '') {
        $usercountrydetailbytimezone = FetchMemberCountryData($iCompanyId, "Company", $vTimeZone, $vUserDeviceCountry);
        $vCountryCode = $usercountrydetailbytimezone['vDefaultCountryCode'];
    }
    else {
        $vCountryCode = $vCountry;
    }
    $Sql = "SELECT iCountryId FROM country WHERE vCountryCode = '" . $vCountryCode . "'";
    $DataCountry = $obj->MySQLSelect($Sql);
    $iCountryId = $DataCountry[0]['iCountryId'];
    $query = "SELECT iStateId,vStateCode,vState FROM state WHERE iCountryId = '" . $iCountryId . "' AND eStatus = 'Active' ORDER BY vState";
    $db_rec = $obj->MySQLSelect($query);
    if (count($db_rec) > 0) {
        $StateArr['Action'] = "1";
        $StateArr['totalValues'] = count($db_rec);
        $StateArr['StateList'] = $db_rec;
    }
    else {
        $StateArr['Action'] = "0";
        $cityArr['message'] = $languageLabelsArr['LBL_NO_STATE_AVAILABLE'];
    }
    setDataResponse($StateArr);
}
// ############################ Get State Using country code ######################
// ############################ Get State Using country code ######################
if ($type == "GetCityFromState") {
    $iCompanyId = isset($_REQUEST["iCompanyId"]) ? $_REQUEST["iCompanyId"] : "";
    $iStateId = isset($_REQUEST["iStateId"]) ? $_REQUEST["iStateId"] : "";
    $UserDetailsArr = getCompanyCurrencyLanguageDetails($iCompanyId);
    $vLang = $UserDetailsArr['vLang'];
    $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLang, "1", $iServiceId);
    if ($iStateId == '') {
        $usercountrydetailbytimezone = FetchMemberCountryData($iCompanyId, "Company", $vTimeZone, $vUserDeviceCountry);
        $vCountryCode = $usercountrydetailbytimezone['vDefaultCountryCode'];
        $Sql = "SELECT iCountryId FROM country WHERE vCountryCode = '" . $vCountryCode . "'";
        $DataCountry = $obj->MySQLSelect($Sql);
        $iCountryId = $DataCountry[0]['iCountryId'];
        $query = "SELECT iStateId FROM state WHERE iCountryId = '" . $iCountryId . "' AND eStatus = 'Active'";
        $db_rec = $obj->MySQLSelect($query);
        $iStateId = $db_rec[0]['iStateId'];
    }
    $query1 = "SELECT iCityId,vCity,eStatus FROM city WHERE  iStateId = '" . $iStateId . "' AND eStatus ='Active' ORDER BY vCity";
    $City_rec = $obj->MySQLSelect($query1);
    if (count($City_rec) > 0) {
        $cityArr['Action'] = "1";
        $cityArr['totalValues'] = count($City_rec);
        $cityArr['CityList'] = $City_rec;
    }
    else {
        $cityArr['Action'] = "0";
        $cityArr['message'] = $languageLabelsArr['LBL_NO_CITY_AVAILABLE'];
    }
    setDataResponse($cityArr);
}
// ############################ Get State Using country code ######################
// ################################## For Strappers Scree Update Restaurant Details ##################################
if ($type == "UpdateRestaurantDetails") {
    $iCompanyId = isset($_REQUEST["iCompanyId"]) ? $_REQUEST["iCompanyId"] : "";
    $CALL_TYPE = isset($_REQUEST["CALL_TYPE"]) ? $_REQUEST["CALL_TYPE"] : "Display"; // Display , Update
    $sql = "SELECT vName,vSymbol,Ratio FROM  `currency` WHERE  `eDefault` = 'Yes' ";
    $defCurrencyValues = $obj->MySQLSelect($sql);
    $vCurrency = $defCurrencyValues[0]['vName'];
    $vCurrencySymbol = $defCurrencyValues[0]['vSymbol'];
    $returnArr['vCurrency'] = $vCurrency;
    $returnArr['vCurrencySymbol'] = $vCurrencySymbol;
    if ($CALL_TYPE == "Display") {
        $sqlc = "SELECT co.vContactName,co.vRestuarantLocation,co.vRestuarantLocationLat,co.vRestuarantLocationLong,co.vCaddress,co.vState as iStateId,co.vCity as iCityId,co.vZip,co.iMaxItemQty,co.fPrepareTime,co.fMinOrderValue,st.vState,ci.vCity FROM company as co LEFT JOIN state as st ON st.iStateId=co.vState LEFT JOIN city as ci ON ci.iCityId=co.vCity WHERE co.iCompanyId = '" . $iCompanyId . "'";
        $result_company = $obj->MySQLSelect($sqlc);
        $result_company[0]['iMaxItemQty'] = ($result_company[0]['iMaxItemQty'] > 0) ? $result_company[0]['iMaxItemQty'] : "";
        $result_company[0]['fPrepareTime'] = ($result_company[0]['fPrepareTime'] > 0) ? $result_company[0]['fPrepareTime'] : "";
        $result_company[0]['fMinOrderValue'] = ($result_company[0]['fMinOrderValue'] > 0) ? $result_company[0]['fMinOrderValue'] : "";
        $result_company[0]['vCity'] = (!empty($result_company[0]['vCity'])) ? $result_company[0]['vCity'] : "";
        $result_company[0]['vState'] = (!empty($result_company[0]['vState'])) ? $result_company[0]['vState'] : "";
        $returnArr['Action'] = "1";
        $returnArr['message'] = $result_company[0];
        setDataResponse($returnArr);
    }
    else {
        $vContactName = isset($_REQUEST["vContactName"]) ? $_REQUEST["vContactName"] : "";
        $vRestuarantLocation = isset($_REQUEST["vRestuarantLocation"]) ? $_REQUEST["vRestuarantLocation"] : "";
        $vRestuarantLocationLat = isset($_REQUEST["vRestuarantLocationLat"]) ? $_REQUEST["vRestuarantLocationLat"] : "";
        $vRestuarantLocationLong = isset($_REQUEST["vRestuarantLocationLong"]) ? $_REQUEST["vRestuarantLocationLong"] : "";
        $vCaddress = isset($_REQUEST["vCaddress"]) ? $_REQUEST["vCaddress"] : "";
        $vState = isset($_REQUEST["vState"]) ? $_REQUEST["vState"] : "";
        $vCity = isset($_REQUEST["vCity"]) ? $_REQUEST["vCity"] : "";
        $vZip = isset($_REQUEST["vZip"]) ? $_REQUEST["vZip"] : "";
        $iMaxItemQty = isset($_REQUEST["iMaxItemQty"]) ? $_REQUEST["iMaxItemQty"] : "";
        $fPrepareTime = isset($_REQUEST["fPrepareTime"]) ? $_REQUEST["fPrepareTime"] : "";
        $fMinOrderValue = isset($_REQUEST["fMinOrderValue"]) ? $_REQUEST["fMinOrderValue"] : "";
        $where = " iCompanyId = '$iCompanyId'";
        $Data_update_Companies['vContactName'] = $vContactName;
        $Data_update_Companies['vRestuarantLocation'] = $vRestuarantLocation;
        $Data_update_Companies['vRestuarantLocationLat'] = $vRestuarantLocationLat;
        $Data_update_Companies['vRestuarantLocationLong'] = $vRestuarantLocationLong;
        $Data_update_Companies['vCaddress'] = $vCaddress;
        $Data_update_Companies['vState'] = $vState;
        $Data_update_Companies['vCity'] = $vCity;
        $Data_update_Companies['vZip'] = $vZip;
        if (isset($_REQUEST["iMaxItemQty"])) {
            $Data_update_Companies['iMaxItemQty'] = $iMaxItemQty;
        }
        if (isset($_REQUEST["fPrepareTime"])) {
            $Data_update_Companies['fPrepareTime'] = $fPrepareTime;
        }
        if (isset($_REQUEST["fMinOrderValue"])) {
            $Data_update_Companies['fMinOrderValue'] = $fMinOrderValue;
        }
        $Companyid = $obj->MySQLQueryPerform("company", $Data_update_Companies, 'update', $where);
        if ($Companyid) {
            $returnData['Action'] = "1";
            $returnData['message'] = "LBL_INFO_UPDATED_TXT";
        }
        else {
            $returnData['Action'] = "0";
            $returnData['message'] = "LBL_TRY_AGAIN_LATER";
        }
        setDataResponse($returnData);
    }
}
// ################################## Update Restaurant Details ##################################
// ############################## Company States ###############################
if ($type == "getCompanyStates") {
    $iCompanyId = isset($_REQUEST['iCompanyId']) ? clean($_REQUEST['iCompanyId']) : '';
    $userType = isset($_REQUEST['UserType']) ? clean($_REQUEST['UserType']) : 'Company';
    if ($userType == 'company' || $userType == 'Company') {
        $doc_usertype = 'store';
        // $doc_usertype = 'company';
    }
    $docUpload = $CompanyDetailCompleted = $WorkingHoursCompleted = $CompanyStateActive = 'Yes';
    $fields = "vCountry,vContactName,vRestuarantLocation,vRestuarantLocationLat,vRestuarantLocationLong,vCaddress,vState,vFromMonFriTimeSlot1,vToMonFriTimeSlot1,vFromSatSunTimeSlot1,vToSatSunTimeSlot1";
    //added by SP on 14-10-2020 for timeslot changes
    $ENABLE_TIMESLOT_ADDON = !empty($MODULES_OBJ->isEnableTimeslotFeature()) ? "Yes" : "No";
    if (strtoupper($ENABLE_TIMESLOT_ADDON) == "YES") {
        //$orgtimingArray = array('vMonFromSlot1', 'vMonToSlot1', 'vTueFromSlot1', 'vTueToSlot1', 'vWedFromSlot1', 'vWedToSlot1', 'vThuFromSlot1', 'vThuToSlot1', 'vFriFromSlot1', 'vFriToSlot1', 'vSatFromSlot1', 'vSatToSlot1', 'vSunFromSlot1', 'vSunToSlot1', 'vMonFromSlot2', 'vMonToSlot2', 'vTueFromSlot2', 'vTueToSlot2', 'vWedFromSlot2', 'vWedToSlot2', 'vThuFromSlot2', 'vThuToSlot2', 'vFriFromSlot2', 'vFriToSlot2', 'vSatFromSlot2', 'vSatToSlot2', 'vSunFromSlot2', 'vSunToSlot2');
        $orgtimingArray = array('vMonFromSlot1', 'vMonToSlot1', 'vTueFromSlot1', 'vTueToSlot1', 'vWedFromSlot1', 'vWedToSlot1', 'vThuFromSlot1', 'vThuToSlot1', 'vFriFromSlot1', 'vFriToSlot1', 'vSatFromSlot1', 'vSatToSlot1', 'vSunFromSlot1', 'vSunToSlot1');
        $fieldNames = "";
        foreach ($orgtimingArray as $fieldValue) {
            $fieldNames .= $fieldValue . ",";
        }
        $fieldNames = rtrim($fieldNames, ",");
        $CompanyData = get_value('company', $fields . "," . $fieldNames, 'iCompanyId', $iCompanyId);
        if (isset($_REQUEST['test'])) {
        }
        foreach ($orgtimingArray as $key => $value) {
            if ($CompanyData[0][$value] == '00:00:00') {
                $WorkingHoursCompleted = 'No';
            }
        }
    }
    else {
        $CompanyData = get_value('company', $fields, 'iCompanyId', $iCompanyId);
        $vFromMonFriTimeSlot1 = $CompanyData[0]['vFromMonFriTimeSlot1'];
        $vToMonFriTimeSlot1 = $CompanyData[0]['vToMonFriTimeSlot1'];
        $vFromSatSunTimeSlot1 = $CompanyData[0]['vFromSatSunTimeSlot1'];
        $vToSatSunTimeSlot1 = $CompanyData[0]['vToSatSunTimeSlot1'];
        if (isset($_REQUEST['test'])) {
        }
        if (($vFromMonFriTimeSlot1 == '00:00:00' || $vFromMonFriTimeSlot1 == '') || ($vToMonFriTimeSlot1 == '00:00:00' || $vToMonFriTimeSlot1 == '') || ($vFromSatSunTimeSlot1 == '00:00:00' || $vFromSatSunTimeSlot1 == '') || ($vToSatSunTimeSlot1 == '00:00:00' || $vToSatSunTimeSlot1 == '')) {
            $WorkingHoursCompleted = 'No';
        }
    }
    $vContactName = $CompanyData[0]['vContactName'];
    $vRestuarantLocation = $CompanyData[0]['vRestuarantLocation'];
    $vRestuarantLocationLat = $CompanyData[0]['vRestuarantLocationLat'];
    $vRestuarantLocationLong = $CompanyData[0]['vRestuarantLocationLong'];
    $vCaddress = $CompanyData[0]['vCaddress'];
    $vState = $CompanyData[0]['vState'];
    if ($vContactName == '' || $vRestuarantLocation == '' || $vRestuarantLocationLat == '' || $vRestuarantLocationLong == '' || $vCaddress == '' || $vState == '') {
        $CompanyDetailCompleted = 'No';
    }
    $vCountry = $CompanyData[0]['vCountry'];
    $sql1 = "SELECT dm.doc_masterid masterid, dm.doc_usertype , dm.doc_name ,dm.ex_status,dm.status, COALESCE(dl.doc_id,  '' ) as doc_id,COALESCE(dl.doc_masterid, '') as masterid_list ,COALESCE(dl.ex_date, '') as ex_date,COALESCE(dl.doc_file, '') as doc_file, COALESCE(dl.status, '') as status FROM document_master dm left join (SELECT * FROM `document_list` where doc_userid='" . $iCompanyId . "' ) dl on dl.doc_masterid=dm.doc_masterid where dm.doc_usertype='" . $doc_usertype . "' and (dm.country='" . $vCountry . "' OR dm.country='All') and dm.status='Active'";
    $db_document = $obj->MySQLSelect($sql1);
    if (count($db_document) > 0) {
        for ($i = 0; $i < count($db_document); $i++) {
            if ($db_document[$i]['doc_file'] == "") {
                $docUpload = 'No';
            }
        }
    }
    else {
        $docUpload = 'No';
    }
    $sql = "SELECT eStatus FROM `company` WHERE iCompanyId ='" . $iCompanyId . "'";
    $Data = $obj->MySQLSelect($sql);
    if (strtolower($Data[0]['eStatus']) != "active" || strtolower($Data[0]['eStatus']) != "active") {
        $CompanyStateActive = 'No';
    }
    if ($CompanyStateActive == "Yes") {
        $docUpload = $CompanyDetailCompleted = $WorkingHoursCompleted = "Yes";
    }
    $returnArr['Action'] = "1";
    $returnArr['IS_COMPANY_DETAIL_COMPLETED'] = $CompanyDetailCompleted;
    $returnArr['IS_DOCUMENT_PROCESS_COMPLETED'] = $docUpload;
    $returnArr['IS_WORKING_HOURS_COMPLETED'] = $WorkingHoursCompleted;
    $returnArr['IS_COMPANY_STATE_ACTIVATED'] = $CompanyStateActive;
    setDataResponse($returnArr);
}
// ############################## Company States ###############################
// ##########################displayDocList for company##########################################################
if ($type == "displayCompanyDocList") {
    //global $tconfig; // Commented By HJ On 15-07-2020 Bcoz Not Required
    $iMemberId = isset($_REQUEST['iMemberId']) ? clean($_REQUEST['iMemberId']) : '';
    $memberType = isset($_REQUEST['MemberType']) ? clean($_REQUEST['MemberType']) : 'Driver';
    $doc_usertype = isset($_REQUEST['doc_usertype']) ? clean(strtolower($_REQUEST['doc_usertype'])) : 'company';
    $doc_userid = $iMemberId;
    $UserData = get_value('company', 'vCountry,vLang', 'iCompanyId', $iMemberId);
    $vCountry = $UserData[0]['vCountry'];
    $vLang = $UserData[0]['vLang'];
    if ($vLang == '' || $vLang == NULL) {
        //Added By HJ On 15-07-2020 For Optimize language_master Table Query Start
        $vLang = $LANG_OBJ->FetchDefaultLangData("vCode");
        //Added By HJ On 15-07-2020 For Optimize language_master Table Query End
    }
    if ($doc_usertype == 'company') {
        $doc_usertype = 'store';
        //$doc_usertype = 'Company';
    }
    $sql1 = "SELECT dm.doc_masterid masterid, dm.doc_usertype , dm.doc_name_" . $vLang . " as doc_name ,dm.ex_status,dm.status, COALESCE(dl.doc_id,  '' ) as doc_id,COALESCE(dl.doc_masterid, '') as masterid_list ,COALESCE(dl.ex_date, '') as ex_date,COALESCE(dl.doc_file, '') as doc_file, COALESCE(dl.status, '') as status FROM document_master dm left join (SELECT * FROM `document_list` where doc_userid='" . $doc_userid . "' ) dl on dl.doc_masterid=dm.doc_masterid where dm.doc_usertype='" . $doc_usertype . "' AND (dm.country='" . $vCountry . "' OR dm.country='All') and dm.status='Active' GROUP BY dm.doc_masterid ORDER BY dm.iDisplayOrder"; // (GROUP BY dm.doc_masterid ORDER BY dl.doc_id) DESC Added By HJ For Solved Duplicate Data issues As Per Discuss with KS Sir
    $db_vehicle = $obj->MySQLSelect($sql1);
    if (count($db_vehicle) > 0) {
        $Photo_Gallery_folder = $tconfig['tsite_upload_compnay_doc'] . "/" . $iMemberId . "/";
        for ($i = 0; $i < count($db_vehicle); $i++) {
            if ($db_vehicle[$i]['doc_file'] != "") {
                $db_vehicle[$i]['vimage'] = $Photo_Gallery_folder . $db_vehicle[$i]['doc_file'];
            }
            else {
                $db_vehicle[$i]['vimage'] = "";
            }
            // # Checking for expire date of document ##
            $ex_date = $db_vehicle[$i]['ex_date'];
            $todaydate = date('Y-m-d');
            if ($ex_date == "" || $ex_date == "0000-00-00") {
                $expire_document = "No";
            }
            else {
                if (strtotime($ex_date) < strtotime($todaydate)) {
                    $expire_document = "Yes";
                }
                else {
                    $expire_document = "No";
                }
            }
            $db_vehicle[$i]['exp_date'] = "";
            if ($ex_date != "0000-00-00") {
                $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLang, "1", $iServiceId);
                $expireLabel = $languageLabelsArr['LBL_EXPIRE_TXT'];
                //$newFormat = date("jS F Y", strtotime($db_vehicle[$i]['ex_date']));
                $newFormat = date("d M, Y (D)", strtotime($db_vehicle[$i]['ex_date']));
                $db_vehicle[$i]['exp_date'] = $expireLabel . ": " . $newFormat;
            }
            $allowDate = date('Y-m-d', strtotime($db_vehicle[$i]['ex_date'] . ' - ' . $BEFORE_DAYS_ALLLOW_UPDATE_DOCS . ' days'));
            if (($db_vehicle[$i]['ex_date'] == '' || $todaydate >= $allowDate) /*|| $SET_DRIVER_OFFLINE_AS_DOC_EXPIRED == 'No'*/) {
                $db_vehicle[$i]['allow_date_change'] = 'Yes';
                $db_vehicle[$i]['doc_update_disable'] = '';
            }
            else {
                $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLang, "1", $iServiceId);
                $db_vehicle[$i]['allow_date_change'] = 'No';
                $db_vehicle[$i]['doc_update_disable'] = $languageLabelsArr['LBL_DOC_UPDATE_DISABLE'];
            }
            $db_vehicle[$i]['EXPIRE_DOCUMENT'] = $expire_document;
            // # Checking for expire date of document ##
        }
        $returnArr['Action'] = "1";
        $returnArr['message'] = $db_vehicle;
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_NO_DOC_AVAIL";
    }
    setDataResponse($returnArr);
}
// ###################################################################################################
// ##########################Add/Update Company Documents ############################
if ($type == "uploadcompanydocument") {
    $iMemberId = isset($_REQUEST['iMemberId']) ? clean($_REQUEST['iMemberId']) : '';
    $memberType = isset($_REQUEST['MemberType']) ? clean($_REQUEST['MemberType']) : 'Driver';
    $doc_usertype = isset($_REQUEST['doc_usertype']) ? clean(strtolower($_REQUEST['doc_usertype'])) : 'company';
    $doc_masterid = isset($_REQUEST['doc_masterid']) ? clean($_REQUEST['doc_masterid']) : '';
    $doc_name = isset($_REQUEST['doc_name']) ? clean($_REQUEST['doc_name']) : '';
    $doc_id = isset($_REQUEST['doc_id']) ? clean($_REQUEST['doc_id']) : '';
    $doc_file = isset($_REQUEST['doc_file']) ? clean($_REQUEST['doc_file']) : '';
    $ex_date = isset($_REQUEST['ex_date']) ? clean($_REQUEST['ex_date']) : '';
    $ex_status = isset($_REQUEST['ex_status']) ? clean($_REQUEST['ex_status']) : '';
    $Today = Date('Y-m-d');
    $doc_userid = $iMemberId;
    $status = "Inactive";
    $image_name = $vImage = isset($_FILES['vImage']['name']) ? $_FILES['vImage']['name'] : '';
    $image_object = isset($_FILES['vImage']['tmp_name']) ? $_FILES['vImage']['tmp_name'] : '';
    $action = ($doc_id != '') ? 'Edit' : 'Add';
    $addupdatemode = ($action == 'Add') ? 'insert' : 'update';
    if ($doc_usertype == 'company') {
        $doc_usertype = 'store';
        // $doc_usertype = 'company';
    }
    if ($doc_file != "") {
        $vImageName = $doc_file;
    }
    else {
        $extensionArr = explode(".", $image_name);
        $extension = $extensionArr[count($extensionArr) - 1];
        $extension = strtolower($extension);
        if ($extension == "png" || $extension == "jpg" || $extension == "jpeg") {
            if ($image_object) {
                ExifCleaning::adjustImageOrientation($image_object);
            }
        }
        $Photo_Gallery_folder = $tconfig['tsite_upload_compnay_doc_path'] . "/" . $iMemberId . "/";
        if (!is_dir($Photo_Gallery_folder)) {
            mkdir($Photo_Gallery_folder, 0777);
            chmod($Photo_Gallery_folder, 0777);
        }
        $vFile = $UPLOAD_OBJ->GeneralFileUpload($Photo_Gallery_folder, $image_object, $image_name, $prefix = '', $tconfig["tsite_upload_docs_file_extensions"]);
        $vImageName = $vFile[0];
    }
    if ($vImageName != '') {
        $Data_Update["doc_masterid"] = $doc_masterid;
        $Data_Update["doc_usertype"] = $doc_usertype;
        $Data_Update["doc_userid"] = $doc_userid;
        $Data_Update["edate"] = @date("Y-m-d H:i:s");
        $returnArr['doc_under_review'] = '';
        $exitingExpDate = 'SELECT dm.ex_status,dl.ex_date,dl.req_date FROM document_list AS dl LEFT JOIN document_master as dm ON dm.doc_masterid = dl.doc_masterid  WHERE doc_id = ' . $doc_id;
        $db_data1 = $obj->MySQLSelect($exitingExpDate);
        $allowDate = date('Y-m-d', strtotime($db_data1[0]['ex_date'] . ' - ' . $BEFORE_DAYS_ALLLOW_UPDATE_DOCS . ' days'));
        /*if ($Today >= $allowDate && $SET_DRIVER_OFFLINE_AS_DOC_EXPIRED == 'Yes' && $action != "Add" && $db_data1[0]['ex_status'] == 'yes') {
            $ex_date = $ex_date == $db_data1[0]['ex_date'] ? $db_data1[0]['req_date'] : $ex_date;

            $Data_Update["req_date"] = $ex_date;
            $Data_Update["req_file"] = $vImageName;

            $returnArr['doc_under_review'] = 'LBL_FOR_DOCS_UNDER_REVIEW';
        } else {
            $Data_Update["ex_date"] = $ex_date;
            $Data_Update["doc_file"] = $vImageName;
        }*/
        $Data_Update["ex_date"] = $ex_date;
        $Data_Update["doc_file"] = $vImageName;
        if ($action == "Add") {
            $Data_Update["status"] = $status;
            $id = $obj->MySQLQueryPerform("document_list", $Data_Update, 'insert');
        }
        else {
            $where = " doc_id = '" . $doc_id . "'";
            $id = $obj->MySQLQueryPerform("document_list", $Data_Update, 'update', $where);
        }
        save_log_data($iMemberId, $iMemberId, 'company', $doc_name, $vImageName);
        if ($id > 0) {
            $returnArr['Action'] = "1";
        }
        else {
            $returnArr['Action'] = "0";
            $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
        }
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
    }
    setDataResponse($returnArr);
}
// ##########################Add/Update Driver's Document and Vehilcle Document Ends#######################
// ##########################Update Time Slot for Restaurant#######################
if ($type == "UpdateCompanyTiming") {
    //added by SP on 14-10-2020 for timeslot changes
    $slot = isset($_REQUEST['slot']) ? $_REQUEST['slot'] : '';
    $ENABLE_TIMESLOT_ADDON = !empty($MODULES_OBJ->isEnableTimeslotFeature()) ? "Yes" : "No";
    $returnArr['ENABLE_TIMESLOT_ADDON'] = $ENABLE_TIMESLOT_ADDON;
   
    if (strtoupper($ENABLE_TIMESLOT_ADDON) == "YES" && !empty($slot)) {
        UpdateStoreTimeSlots();
    }
    else {
        UpdateStoreTimeSlotsGeneral();
    }
}
if ($type == "UpdateCompanyTiming_old") {
    $iCompanyId = isset($_REQUEST['iCompanyId']) ? $_REQUEST['iCompanyId'] : '';
    $CALL_TYPE = isset($_REQUEST["CALL_TYPE"]) ? $_REQUEST["CALL_TYPE"] : "Display"; // Display , Update
    if ($CALL_TYPE == "Display") {
        $sqlc = "SELECT vFromMonFriTimeSlot1,vToMonFriTimeSlot1,vFromMonFriTimeSlot2,vToMonFriTimeSlot2,vFromSatSunTimeSlot1,vToSatSunTimeSlot1,vFromSatSunTimeSlot2,vToSatSunTimeSlot2 FROM company WHERE iCompanyId = '" . $iCompanyId . "'";
        $result_company = $obj->MySQLSelect($sqlc);
        $returnArr['Action'] = "1";
        $returnArr['message'] = $result_company[0];
        setDataResponse($returnArr);
    }
    else {
        $vFromMonFriTimeSlot1 = isset($_REQUEST['vFromMonFriTimeSlot1']) ? $_REQUEST['vFromMonFriTimeSlot1'] : '';
        $vToMonFriTimeSlot1 = isset($_REQUEST['vToMonFriTimeSlot1']) ? $_REQUEST['vToMonFriTimeSlot1'] : '';
        $vFromMonFriTimeSlot2 = isset($_REQUEST['vFromMonFriTimeSlot2']) ? $_REQUEST['vFromMonFriTimeSlot2'] : '';
        $vToMonFriTimeSlot2 = isset($_REQUEST['vToMonFriTimeSlot2']) ? $_REQUEST['vToMonFriTimeSlot2'] : '';
        $vFromSatSunTimeSlot1 = isset($_REQUEST['vFromSatSunTimeSlot1']) ? $_REQUEST['vFromSatSunTimeSlot1'] : '';
        $vToSatSunTimeSlot1 = isset($_REQUEST['vToSatSunTimeSlot1']) ? $_REQUEST['vToSatSunTimeSlot1'] : '';
        $vFromSatSunTimeSlot2 = isset($_REQUEST['vFromSatSunTimeSlot2']) ? $_REQUEST['vFromSatSunTimeSlot2'] : '';
        $vToSatSunTimeSlot2 = isset($_REQUEST['vToSatSunTimeSlot2']) ? $_REQUEST['vToSatSunTimeSlot2'] : '';
        $where = " iCompanyId = '" . $iCompanyId . "'";
        $Data_Update['vFromMonFriTimeSlot1'] = $vFromMonFriTimeSlot1;
        $Data_Update['vToMonFriTimeSlot1'] = $vToMonFriTimeSlot1;
        $Data_Update['vFromMonFriTimeSlot2'] = $vFromMonFriTimeSlot2;
        $Data_Update['vToMonFriTimeSlot2'] = $vToMonFriTimeSlot2;
        $Data_Update['vFromSatSunTimeSlot1'] = $vFromSatSunTimeSlot1;
        $Data_Update['vToSatSunTimeSlot1'] = $vToSatSunTimeSlot1;
        $Data_Update['vFromSatSunTimeSlot2'] = $vFromSatSunTimeSlot2;
        $Data_Update['vToSatSunTimeSlot2'] = $vToSatSunTimeSlot2;
        $id = $obj->MySQLQueryPerform("company", $Data_Update, 'update', $where);
        if ($id) {
            $returnArr['Action'] = "1";
            $returnArr['message'] = "LBL_INFO_UPDATED_TXT";
        }
        else {
            $returnArr['Action'] = "0";
            $returnArr['message'] = "LBL_TRY_AGAIN_LATER";
        }
        setDataResponse($returnArr);
    }
}
// ##########################Update Time Slot for Restaurant#######################
// ################################## For Update Restaurant Availability  ##################################
if ($type == "UpdateRestaurantAvailability") {
    $iCompanyId = isset($_REQUEST["iCompanyId"]) ? $_REQUEST["iCompanyId"] : "";
    $UserType = isset($_REQUEST['UserType']) ? clean($_REQUEST['UserType']) : 'Company';
    $eAvailable = isset($_REQUEST["eAvailable"]) ? $_REQUEST["eAvailable"] : "Yes";
    $CALL_TYPE = isset($_REQUEST["CALL_TYPE"]) ? $_REQUEST["CALL_TYPE"] : "Display"; // Display , Update
    $isFromWeb = isset($_REQUEST["isFromWeb"]) ? $_REQUEST["isFromWeb"] : "No";
    if ($CALL_TYPE == "Display") {
        $sqlc = "SELECT eAvailable FROM company WHERE iCompanyId = '" . $iCompanyId . "'";
        $result_company = $obj->MySQLSelect($sqlc);
        $returnArr['Action'] = "1";
        $returnArr['message'] = $result_company[0];
        setDataResponse($returnArr);
    }
    else {
        $isAllInformationUpdate = "Yes";
        if ($eAvailable == "Yes") {
            //isMemberEmailPhoneVerified($iCompanyId, $UserType);
            isMemberPhoneVerified($iCompanyId, $UserType);
            //added by SP on 14-10-2020 for timeslot changes
            $ENABLE_TIMESLOT_ADDON = !empty($MODULES_OBJ->isEnableTimeslotFeature()) ? "Yes" : "No";
            if (strtoupper($ENABLE_TIMESLOT_ADDON) == "YES") {
                $orgtimingArray = array('vMonFromSlot1', 'vMonToSlot1', 'vTueFromSlot1', 'vTueToSlot1', 'vWedFromSlot1', 'vWedToSlot1', 'vThuFromSlot1', 'vThuToSlot1', 'vFriFromSlot1', 'vFriToSlot1', 'vSatFromSlot1', 'vSatToSlot1', 'vSunFromSlot1', 'vSunToSlot1', 'vMonFromSlot2', 'vMonToSlot2', 'vTueFromSlot2', 'vTueToSlot2', 'vWedFromSlot2', 'vWedToSlot2', 'vThuFromSlot2', 'vThuToSlot2', 'vFriFromSlot2', 'vFriToSlot2', 'vSatFromSlot2', 'vSatToSlot2', 'vSunFromSlot2', 'vSunToSlot2');
                $fieldNames = "";
                foreach ($orgtimingArray as $fieldValue) {
                    $fieldNames .= $fieldValue . ",";
                }
                $fieldNames = rtrim($fieldNames, ",");
                $sqlc = "SELECT iMaxItemQty,fPrepareTime,fMinOrderValue,vContactName,vRestuarantLocation,vRestuarantLocationLat,vRestuarantLocationLong,vCaddress,vState,vZip,vFromMonFriTimeSlot1,vToMonFriTimeSlot1,vFromSatSunTimeSlot1,vToSatSunTimeSlot1," . $fieldNames . " FROM company WHERE iCompanyId = '" . $iCompanyId . "'";
            }
            else {
                $sqlc = "SELECT iMaxItemQty,fPrepareTime,fMinOrderValue,vContactName,vRestuarantLocation,vRestuarantLocationLat,vRestuarantLocationLong,vCaddress,vState,vZip,vFromMonFriTimeSlot1,vToMonFriTimeSlot1,vFromSatSunTimeSlot1,vToSatSunTimeSlot1 FROM company WHERE iCompanyId = '" . $iCompanyId . "'";
            }
            $result_company = $obj->MySQLSelect($sqlc);
            // remove from condition $result_company[0]['fMinOrderValue']
            //added by SP on 14-10-2020 for timeslot changes
            $ENABLE_TIMESLOT_ADDON = !empty($MODULES_OBJ->isEnableTimeslotFeature()) ? "Yes" : "No";
            if (strtoupper($ENABLE_TIMESLOT_ADDON) == "YES") {
                if ($isFromWeb == "Yes") {
                    if ($result_company[0]['fPrepareTime'] == 0 || $result_company[0]['fPrepareTime'] == "" || $result_company[0]['vContactName'] == "" || $result_company[0]['vRestuarantLocation'] == "" || $result_company[0]['vRestuarantLocationLat'] == "" || $result_company[0]['vRestuarantLocationLong'] == "" || $result_company[0]['vCaddress'] == "") {
                        $isAllInformationUpdate = "No";
                    }
                }
                else {
                    if ($result_company[0]['iMaxItemQty'] == 0 || $result_company[0]['iMaxItemQty'] == "" || $result_company[0]['fPrepareTime'] == 0 || $result_company[0]['fPrepareTime'] == "" || $result_company[0]['vContactName'] == "" || $result_company[0]['vRestuarantLocation'] == "" || $result_company[0]['vRestuarantLocationLat'] == "" || $result_company[0]['vRestuarantLocationLong'] == "" || $result_company[0]['vCaddress'] == "" || $result_company[0]['vState'] == "" || $result_company[0]['vZip'] == "") {
                        $isAllInformationUpdate = "No";
                    }
                }
            }
            else {
                if ($result_company[0]['iMaxItemQty'] == 0 || $result_company[0]['iMaxItemQty'] == "" || $result_company[0]['fPrepareTime'] == 0 || $result_company[0]['fPrepareTime'] == "" || $result_company[0]['vContactName'] == "" || $result_company[0]['vRestuarantLocation'] == "" || $result_company[0]['vRestuarantLocationLat'] == "" || $result_company[0]['vRestuarantLocationLong'] == "" || $result_company[0]['vCaddress'] == "" || $result_company[0]['vState'] == "" || $result_company[0]['vZip'] == "" || $result_company[0]['vFromMonFriTimeSlot1'] == "" || $result_company[0]['vFromMonFriTimeSlot1'] == "00:00:00" || $result_company[0]['vToMonFriTimeSlot1'] == "" || $result_company[0]['vToMonFriTimeSlot1'] == "00:00:00" || $result_company[0]['vFromSatSunTimeSlot1'] == "" || $result_company[0]['vFromSatSunTimeSlot1'] == "00:00:00" || $result_company[0]['vToSatSunTimeSlot1'] == "" || $result_company[0]['vToSatSunTimeSlot1'] == "00:00:00") {
                    $isAllInformationUpdate = "No";
                }
            }
        }
        if ($isAllInformationUpdate == "No" && strtoupper($eAvailable) == "YES") {
            $returnArr['Action'] = "0";
            $returnArr['message'] = "LBL_TRY_AGAIN_LATER";
            $returnArr['isAllInformationUpdate'] = $isAllInformationUpdate;
            setDataResponse($returnArr);
        }
        $sql = "SELECT count(cu.cuisineId) as cnt FROM cuisine as cu INNER JOIN company_cuisine as ccu ON ccu.cuisineId=cu.cuisineId INNER JOIN company cmp ON ccu.iCompanyId=cmp.iCompanyId WHERE ccu.iCompanyId = $iCompanyId AND cu.eStatus = 'Active'";
        $db_cuisine = $obj->MySQLSelect($sql);
        if ($db_cuisine[0]['cnt'] <= 0 && strtoupper($eAvailable) == "YES") {
            $returnArr['Action'] = "0";
            $returnArr['message'] = "LBL_NO_CUISINES_AVAILABLE_FOR_RESTAURANT";
            setDataResponse($returnArr);
        }
        $CheckRideDeliveryFeatureDisable_Arr = CheckRideDeliveryFeatureDisable(); //Checked By HJ On 10-01-2019 As Per Discuss WIth KS Sir For Solve Bug
        $eShowDeliverAllVehicles = $CheckRideDeliveryFeatureDisable_Arr['eShowDeliverAllVehicles'];
        if ($eShowDeliverAllVehicles == "Yes") {
            $CompanyDetailsArr = getCompanyDetails($iCompanyId, 0, "No", "");
            $CompanyFoodDataCount = $CompanyDetailsArr['CompanyFoodDataCount'];
            if ($CompanyFoodDataCount == 0 && strtoupper($eAvailable) == "YES") {
                $returnArr['Action'] = "0";
                $returnArr['message'] = "LBL_NO_FOOD_MENU_ITEM_AVAILABLE_TXT";
                setDataResponse($returnArr);
            }
        }
        else if (strtoupper($eAvailable) == "YES") {
            $returnArr['Action'] = "0";
            $returnArr['message'] = "LBL_DELIVER_ALL_SERVICE_DISABLE_TXT";
            setDataResponse($returnArr);
        }
        $where = " iCompanyId = '$iCompanyId'";
        $Data_update_Companies['eAvailable'] = $eAvailable;
        $Company_Update_id = $obj->MySQLQueryPerform("company", $Data_update_Companies, 'update', $where);
        if ($Company_Update_id) {
            $returnArr['Action'] = "1";
            $returnArr['message'] = "LBL_INFO_UPDATED_TXT";
            $returnArr['isAllInformationUpdate'] = $isAllInformationUpdate;
        }
        else {
            $returnArr['Action'] = "0";
            $returnArr['message'] = "LBL_TRY_AGAIN_LATER";
            $returnArr['isAllInformationUpdate'] = $isAllInformationUpdate;
        }
        setDataResponse($returnArr);
    }
}
// ################################## For Update Restaurant Availability  ##################################
// ################################## For Update Restaurant Store Settings  ##################################
if ($type == "UpdateDisplayRestaurantStoreSettings") {
    $iCompanyId = isset($_REQUEST["iCompanyId"]) ? $_REQUEST["iCompanyId"] : "";
    $UserType = isset($_REQUEST['UserType']) ? clean($_REQUEST['UserType']) : 'Company';
    $CALL_TYPE = isset($_REQUEST["CALL_TYPE"]) ? $_REQUEST["CALL_TYPE"] : "Display"; // Display , Update
    $vScreenName = isset($_REQUEST["vScreenName"]) ? $_REQUEST["vScreenName"] : "StoreSetting"; // Order , StoreSetting
    $eDriverOption = isset($_REQUEST["eDriverOption"]) ? $_REQUEST["eDriverOption"] : "All"; // Order , StoreSetting
    $vGeneralLang = isset($_REQUEST["vGeneralLang"]) ? $_REQUEST["vGeneralLang"] : "EN"; // Order , StoreSetting
    $eTakeAway = isset($_REQUEST["eTakeAway"]) ? $_REQUEST["eTakeAway"] : "No"; // Order , StoreSetting
    $sql = "SELECT vName,vSymbol,Ratio FROM  `currency` WHERE  `eDefault` = 'Yes' ";
    $defCurrencyValues = $obj->MySQLSelect($sql);
    $vCurrency = $defCurrencyValues[0]['vName'];
    $vCurrencySymbol = $defCurrencyValues[0]['vSymbol'];
    $returnArr['vCurrency'] = $vCurrency;
    $returnArr['vCurrencySymbol'] = $vCurrencySymbol;
    if ($vScreenName == "StoreSetting") {
        if ($CALL_TYPE == "Display") {
            $langage_lblData = $LANG_OBJ->FetchLanguageLabels($vGeneralLang, "1", $iServiceId);
            $sqlc = "SELECT eDriverOption,iMaxItemQty,eAvailable,fPrepareTime,fMinOrderValue,eTakeaway FROM company WHERE iCompanyId = '" . $iCompanyId . "'";
            $result_company = $obj->MySQLSelect($sqlc);
            $result_company[0]['iMaxItemQty'] = ($result_company[0]['iMaxItemQty'] > 0) ? $result_company[0]['iMaxItemQty'] : "";
            $result_company[0]['fPrepareTime'] = ($result_company[0]['fPrepareTime'] > 0) ? $result_company[0]['fPrepareTime'] : "";
            $result_company[0]['fMinOrderValue'] = ($result_company[0]['fMinOrderValue'] > 0) ? $result_company[0]['fMinOrderValue'] : "";
            $result_company[0]['eDriverOptionLabel'] = $langage_lblData['LBL_BOTH_DELIEVERY_DRIVERS'];
            if ($result_company[0]['eDriverOption'] == "Personal") {
                $result_company[0]['eDriverOptionLabel'] = $langage_lblData['LBL_PERSONAL_DELIVERY_DRIVER'];
            }
            else if ($result_company[0]['eDriverOption'] == "Site") {
                $result_company[0]['eDriverOptionLabel'] = $langage_lblData['LBL_SITE_DELIVERY_DRIVER'];
            }
            $result_company[0]['eTakeAway'] = ($result_company[0]['eTakeaway'] == "Yes") ? "Yes" : "No";
            $returnArr['Action'] = "1";
            $returnArr['message'] = $result_company[0];
            setDataResponse($returnArr);
        }
        else {
            $iMaxItemQty = isset($_REQUEST["iMaxItemQty"]) ? $_REQUEST["iMaxItemQty"] : "";
            // $eAvailable = isset($_REQUEST["eAvailable"]) ? $_REQUEST["eAvailable"] : "Yes";
            $fPrepareTime = isset($_REQUEST["fPrepareTime"]) ? $_REQUEST["fPrepareTime"] : "";
            $fMinOrderValue = isset($_REQUEST["fMinOrderValue"]) ? $_REQUEST["fMinOrderValue"] : "";
            if ($eAvailable == "Yes") {
                // isMemberEmailPhoneVerified($iCompanyId, $UserType);
            }
            $Data_update_Companies = array();
            $where = " iCompanyId = '$iCompanyId'";
            $Data_update_Companies['iMaxItemQty'] = $iMaxItemQty;
            // $Data_update_Companies['eAvailable'] = $eAvailable;
            $Data_update_Companies['fPrepareTime'] = $fPrepareTime;
            $Data_update_Companies['fMinOrderValue'] = $fMinOrderValue;
            $Data_update_Companies['eDriverOption'] = $eDriverOption;
            if($MODULES_OBJ->isTakeAwayEnable()) {
                $Data_update_Companies['eTakeaway'] = $eTakeAway;    
            }            
            $Company_Update_id = $obj->MySQLQueryPerform("company", $Data_update_Companies, 'update', $where);
            if ($Company_Update_id) {
                $returnArr['Action'] = "1";
                $returnArr['message'] = "LBL_INFO_UPDATED_TXT";
            }
            else {
                $returnArr['Action'] = "0";
                $returnArr['message'] = "LBL_TRY_AGAIN_LATER";
            }
            setDataResponse($returnArr);
        }
    }
    else {
        if ($CALL_TYPE == "Display") {
            $sqlc = "SELECT eAvailable FROM company WHERE iCompanyId = '" . $iCompanyId . "'";
            $result_company = $obj->MySQLSelect($sqlc);
            $returnArr['Action'] = "1";
            $returnArr['message'] = $result_company[0];
            setDataResponse($returnArr);
        }
        else {
            $eAvailable = isset($_REQUEST["eAvailable"]) ? $_REQUEST["eAvailable"] : "Yes";
            if ($eAvailable == "Yes") {
                isMemberEmailPhoneVerified($iCompanyId, $UserType);
            }
            $where = " iCompanyId = '$iCompanyId'";
            $Data_update_Companies['eAvailable'] = $eAvailable;
            $Company_Update_id = $obj->MySQLQueryPerform("company", $Data_update_Companies, 'update', $where);
            if ($Company_Update_id) {
                $returnArr['Action'] = "1";
                $returnArr['message'] = "LBL_INFO_UPDATED_TXT";
            }
            else {
                $returnArr['Action'] = "0";
                $returnArr['message'] = "LBL_TRY_AGAIN_LATER";
            }
            setDataResponse($returnArr);
        }
    }
}
// ################################## For Update Restaurant Store Settings  ##################################
if ($type == "GetExistingOrderDetails") {
    $iUserId = isset($_REQUEST["iUserId"]) ? $_REQUEST["iUserId"] : '';
    $iCompanyId = isset($_REQUEST["iCompanyId"]) ? $_REQUEST["iCompanyId"] : '';
    $iOrderId = isset($_REQUEST["iOrderId"]) ? $_REQUEST["iOrderId"] : '';
    $UserDetailsArr = getUserCurrencyLanguageDetails($iUserId, $iOrderId);
    $vSymbol = $UserDetailsArr['currencySymbol'];
    $priceRatio = $UserDetailsArr['Ratio'];
    $vLang = $UserDetailsArr['vLang'];
    $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLang, "1", $iServiceId);
    $sql = "select * from orders where iOrderId='" . $iOrderId . "'";
    $data_order = $obj->MySQLSelect($sql);
    $query = "SELECT * FROM order_details WHERE iOrderId = '" . $iOrderId . "'";
    $orderDetails = $obj->MySQLSelect($query);
    $Data = array();
    for ($i = 0; $i < count($orderDetails); $i++) {
        $Data[$i] = DisplayOrderDetailItemList($orderDetails[$i]['iOrderDetailId'], $iUserId, "Passenger", $iOrderId);
    }
    $returnArr['Action'] = "1";
    $returnArr['message'] = $Data;
    setDataResponse($returnArr);
}
// ################################## Get Details of Existing Orders  ########################################

// ################### UserLangugaes as per service type ###################
if ($type == "getUserLanguagesAsPerServiceType") {
    $iServiceId = isset($_REQUEST["iServiceId"]) ? $_REQUEST["iServiceId"] : $iServiceId;
    $languageCode = isset($_REQUEST["LanguageCode"]) ? $_REQUEST['LanguageCode'] : '';
    $returnArr = array();
    $returnArr['changeLangCode'] = "Yes";
    $returnArr['message'] = $LANG_OBJ->FetchLanguageLabels($languageCode, "1", $iServiceId);
    $iServicesIDS = isset($_REQUEST['iServicesIDS']) ? clean($_REQUEST['iServicesIDS']) : '';
    if (!empty($iServicesIDS)) {
        $serviceIds = explode(',', $iServicesIDS);
        if (count($serviceIds) > 1) {
            $UpdatedLanguageLabels = array();
            foreach ($serviceIds as $service_id) {
                $UpdatedLanguageLabels[] = array(
                    'iServiceId' => $service_id, 'dataDic' => $LANG_OBJ->FetchLanguageLabels($vLang, "1", $service_id)
                );
            }
            $returnArr['message'] = $UpdatedLanguageLabels;
        }
    }
    $returnArr['vLanguageCode'] = $languageCode;
    $Data_checkLangCode = $obj->MySQLSelect("SELECT eDirectionCode,vGMapLangCode FROM language_master WHERE `vCode` = '" . $languageCode . "' ");
    $langType = "ltr";
    $vGMapLangCode = "en";
    if (count($Data_checkLangCode) > 0) {
        $langType = $Data_checkLangCode[0]['eDirectionCode'];
        $vGMapLangCode = $Data_checkLangCode[0]['vGMapLangCode'];
    }
    $returnArr['langType'] = $langType;
    $returnArr['vGMapLangCode'] = $vGMapLangCode;
    $returnArr['Action'] = "1";
    $storeCatArr = json_decode(serviceCategories, true);
    $systemStoreEnable = $MODULES_OBJ->isSingleStoreSelection();
    if ($systemStoreEnable > 0) {
        for ($g = 0; $g < count($storeCatArr); $g++) {
            $storeData = getStoreDataForSystemStoreSelection($storeCatArr[$g]['iServiceId']);
            $iCompanyId = $storeData['iCompanyId'];
            $storeData['ispriceshow'] = $storeCatArr[$g]['iServiceId'];
            $storeCatArr[$g]['iCompanyId'] = $iCompanyId;
            $storeCatArr[$g]['STORE_DATA'] = $storeData;
            $storeCatArr[$g]['STORE_ID'] = $iCompanyId;
        }
        if (count($storeCatArr) == 1) {
            $companyData = getStoreDataForSystemStoreSelection($storeCatArr[0]['iServiceId']);
            $returnArr['STORE_ID'] = $companyData['iCompanyId'];
            $returnArr['ispriceshow'] = $storeCatArr[0]['ispriceshow'];
        }
        else {
            $companyData = getStoreDataForSystemStoreSelection(0);
            $returnArr['STORE_ID'] = $companyData[0]['iCompanyId'];
        }
        $returnArr['StoreSelectionData'] = $storeCatArr;
    }
    setDataResponse($returnArr);
}
// ################### UserLangugaes as per service type ###################
#####################################DisplayCouponList ###########################################################
if ($type == "DisplayCouponList") {
    $iCompanyId = isset($_REQUEST['iCompanyId']) ? $_REQUEST['iCompanyId'] : '';
    $iUserAddressId = isset($_REQUEST['iUserAddressId']) ? $_REQUEST['iUserAddressId'] : '';
    $eTakeAway = isset($_REQUEST['eTakeAway']) ? $_REQUEST['eTakeAway'] : 'No';
    $validPromoCodesArr = getValidPromoCodes();
    if (!empty($validPromoCodesArr) && !empty($validPromoCodesArr['CouponList']) && count($validPromoCodesArr['CouponList']) > 0) {
        if ($MODULES_OBJ->isEnableFreeDeliveryOrStoreSpecificPromoCode() || $MODULES_OBJ->isEnableLocationWisePromoCode()) {
            $validPromoCodesArrTmp = $validPromoCodesArr['CouponList'];
            $userAddressData = $obj->MySQLSelect("SELECT * FROM user_address WHERE iUserAddressId = '$iUserAddressId'");
            $userAddressLatitude = $userAddressData[0]['vLatitude'];
            $userAddressLongitude = $userAddressData[0]['vLongitude'];
            $db_companydata = $obj->MySQLSelect("SELECT vRestuarantLocationLat, vRestuarantLocationLong FROM company WHERE iCompanyId = '$iCompanyId'");
            $User_Address_Array = array($userAddressLatitude, $userAddressLongitude);
            $Rest_Address_Array = array($db_companydata[0]['vRestuarantLocationLat'], $db_companydata[0]['vRestuarantLocationLong']);
            $iLocationIdUser = GetUserGeoLocationIdPromoCode($User_Address_Array);
            $iLocationIdRest = GetUserGeoLocationIdPromoCode($Rest_Address_Array);
            foreach ($validPromoCodesArrTmp as $key => $promoCodeData) {
                if ($MODULES_OBJ->isEnableFreeDeliveryOrStoreSpecificPromoCode() && !empty($promoCodeData['eStoreType'])) {
                    if ($eTakeAway == "Yes") {
                        if ($promoCodeData['eStoreType'] == "All") {
                            if ($promoCodeData['eFreeDelivery'] == "Yes") {
                                unset($validPromoCodesArr['CouponList'][$key]);
                            }
                        }
                        else {
                            if ($promoCodeData['iCompanyId'] == $iCompanyId) {
                                if ($promoCodeData['eFreeDelivery'] == "Yes") {
                                    unset($validPromoCodesArr['CouponList'][$key]);
                                }
                            }
                            else {
                                unset($validPromoCodesArr['CouponList'][$key]);
                            }
                        }
                    }
                    else {
                        if ($promoCodeData['eStoreType'] == "StoreSpecific" && $promoCodeData['iCompanyId'] != $iCompanyId) {
                            unset($validPromoCodesArr['CouponList'][$key]);
                        }
                    }
                    if ($MODULES_OBJ->isEnableLocationWisePromoCode() && $promoCodeData['iLocationId'] > 0) {
                        if ($eTakeAway == "No") {
                            if (!in_array($promoCodeData['iLocationId'], $iLocationIdUser) || !in_array($promoCodeData['iLocationId'], $iLocationIdRest)) {
                                unset($validPromoCodesArr['CouponList'][$key]);
                            }
                        }
                        else {
                            if (!in_array($promoCodeData['iLocationId'], $iLocationIdRest)) {
                                unset($validPromoCodesArr['CouponList'][$key]);
                            }
                        }
                    }
                }
                if ($MODULES_OBJ->isEnableLocationWisePromoCode() && $promoCodeData['iLocationId'] > 0) {
                    if ($eTakeAway == "No") {
                        if (!in_array($promoCodeData['iLocationId'], $iLocationIdUser) || !in_array($promoCodeData['iLocationId'], $iLocationIdRest)) {
                            unset($validPromoCodesArr['CouponList'][$key]);
                        }
                    }
                    else {
                        if (!in_array($promoCodeData['iLocationId'], $iLocationIdRest)) {
                            unset($validPromoCodesArr['CouponList'][$key]);
                        }
                    }
                }
            }
            $validPromoCodesArr['CouponList'] = array_values($validPromoCodesArr['CouponList']);
        }
    }
    ## Filter Of Coupon Data ##
    if (count($validPromoCodesArr['CouponList']) > 0) {
        $returnArr['Action'] = "1";
        $returnArr['message'] = $validPromoCodesArr['CouponList'];
        $returnArr['vCurrency'] = $validPromoCodesArr['vCurrency'];
        $returnArr['vSymbol'] = $validPromoCodesArr['vSymbol'];
        setDataResponse($returnArr);
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_NO_RECORDS_FOUND1";
        $returnArr['vCurrency'] = $validPromoCodesArr['vCurrency'];
        $returnArr['vSymbol'] = $validPromoCodesArr['vSymbol'];
        setDataResponse($returnArr);
    }
}
#####################################DisplayCouponList ###########################################################
####################### For Prescription required start added by SP #################################
if ($type == "PrescriptionImages") { // used for uploading and delete images
    //global $tconfig; // Commented By HJ On 15-07-2020 Bcoz Not Required
    $action_type = isset($_REQUEST["action_type"]) ? $_REQUEST["action_type"] : 'ADD';
    $image_name = $vImage = isset($_FILES['vImage']['name']) ? $_FILES['vImage']['name'] : '';
    $image_object = isset($_FILES['vImage']['tmp_name']) ? $_FILES['vImage']['tmp_name'] : '';
    $iImageId = isset($_REQUEST["iImageId"]) ? $_REQUEST["iImageId"] : '';
    $iUserId = isset($_REQUEST["iUserId"]) ? $_REQUEST["iUserId"] : '';
    if ($action_type == "ADD") {
        if ($image_name != "") {
            $Photo_Gallery_folder = $tconfig['tsite_upload_prescription_image_path'];
            if (!is_dir($Photo_Gallery_folder)) {
                mkdir($Photo_Gallery_folder, 0777);
                chmod($Photo_Gallery_folder, 0777);
            }
            $imgext = explode('.', $image_name);
            $unique = uniqid('', true);
            $file_name = substr($unique, strlen($unique) - 4, strlen($unique));
            $new_imagename = $file_name . "." . $imgext[1];
            $vFile = $UPLOAD_OBJ->GeneralFileUpload($Photo_Gallery_folder, $image_object, $new_imagename, $prefix = '', $vaildExt = "jpg,jpeg,gif,png,pdf,doc,docx");
            $vImageName = $vFile[0];
            $Data_update_images['vImage'] = $vImageName;
        }
        $Data_update_images['iUserId'] = $iUserId;
        $Data_update_images['tAddedDate'] = @date("Y-m-d H:i:s");
        $id = $obj->MySQLQueryPerform("prescription_images", $Data_update_images, 'insert');
        if ($id > 0) {
            $returnArr['Action'] = "1";
            $returnArr['message'] = "LBL_IMAGE_UPLOAD_SUCCESS_NOTE";
        }
        else {
            $returnArr['Action'] = "0";
            $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
        }
    }
    else if ($action_type == "DELETE" && $iImageId != "") {
        $Photo_Gallery_folder = $tconfig['tsite_upload_prescription_image_path'];
        $OldImageName = get_value('prescription_images', 'vImage', 'iImageId', $iImageId, '', 'true');
        if ($OldImageName != '') {
            unlink($Photo_Gallery_folder . $OldImageName);
        }
        $sql = "DELETE FROM prescription_images WHERE `iImageId`='" . $iImageId . "'";
        $id = $obj->sql_query($sql);
        if ($id > 0) {
            $returnArr['Action'] = "1";
            $returnArr['message'] = "LBL_IMAGE_DELETE_SUCCESS_NOTE";
        }
        else {
            $returnArr['Action'] = "0";
            $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
        }
    }
    else if ($action_type == "ADD_FROM_WEB") {
        // Added by HV on 24-10-2020 for uploading prescription images from web
        if (count($vImage) > 0) {
            foreach ($vImage as $key => $value) {
                $Photo_Gallery_folder = $tconfig['tsite_upload_prescription_image_path'];
                if (!is_dir($Photo_Gallery_folder)) {
                    mkdir($Photo_Gallery_folder, 0777);
                    chmod($Photo_Gallery_folder, 0777);
                }
                $imgext = explode('.', $value);
                $unique = uniqid('', true);
                $file_name = substr($unique, strlen($unique) - 4, strlen($unique));
                $new_imagename = $file_name . "." . $imgext[1];
                $new_image_object = $image_object[$key];
                $vFile = $UPLOAD_OBJ->GeneralFileUpload($Photo_Gallery_folder, $new_image_object, $new_imagename, $prefix = '', $vaildExt = "jpg,jpeg,gif,png,pdf,doc,docx");
                $vImageName = $vFile[0];
                $Data_update_images['vImage'] = $vImageName;
                $Data_update_images['iUserId'] = $iUserId;
                $Data_update_images['tAddedDate'] = @date("Y-m-d H:i:s");
                $id = $obj->MySQLQueryPerform("prescription_images", $Data_update_images, 'insert');
                if ($id > 0) {
                    $returnArr['Action'] = "1";
                    $returnArr['message'] = "LBL_IMAGE_UPLOAD_SUCCESS_NOTE";
                }
                else {
                    $returnArr['Action'] = "0";
                    $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
                }
            }
        }
        $returnArr['Action'] = "1";
        $returnArr['message'] = "LBL_IMAGE_UPLOAD_SUCCESS_NOTE";
        setDataResponse($returnArr);
        // Added by HV on 24-10-2020 for uploading prescription images from web End
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
    }
    setDataResponse($returnArr);
}
if ($type == "getPrescriptionImages") { //get prescription image data, here when user uploaded images at that time order id 0, then when placce imag using that image then it will be updated orderid...
    //global $tconfig; // Commented By HJ On 15-07-2020 Bcoz Not Required
    $iUserId = isset($_REQUEST["iUserId"]) ? $_REQUEST['iUserId'] : '';
    $PreviouslyUploaded = isset($_REQUEST["PreviouslyUploaded"]) ? $_REQUEST['PreviouslyUploaded'] : ''; //check whether to get previously uploaded images
    $getImages = array();
    if ($PreviouslyUploaded == 1) { // here it is displayed in prescription uploaded by you/in prescription history
        $getImagearray = $obj->MySQLSelect("SELECT iImageId FROM prescription_images WHERE eStatus='Active' AND iUserId='" . $iUserId . "' AND order_id != 0");
        foreach ($getImagearray as $key => $value) {
            $getImages_duplicate = $obj->MySQLSelect("SELECT iImageId FROM prescription_images WHERE duplicate_id = '" . $value['iImageId'] . "'");
            foreach ($getImages_duplicate as $key => $value) {
                if (!empty($value['iImageId'])) $except_imagearray .= $value['iImageId'] . ",";
            }
        }
        $except_imagearray = rtrim($except_imagearray, ',');
        if (!empty($except_imagearray)) {
            $getImages = $obj->MySQLSelect("SELECT * FROM prescription_images WHERE eStatus='Active' AND iUserId='" . $iUserId . "' AND order_id != 0 AND iImageId NOT IN (" . $except_imagearray . ")");
        }
        else {
            $getImages = $obj->MySQLSelect("SELECT * FROM prescription_images WHERE eStatus='Active' AND iUserId='" . $iUserId . "' AND order_id != 0");
        }
        for ($p = 0; $p < count($getImages); $p++) {
            $tmp = explode(".", $getImages[$p]['vImage']);
            for ($i = 0; $i < count($tmp) - 1; $i++) {
                $tmp1[] = $tmp[$i];
            }
            $file = implode("_", $tmp1);
            $ext = $tmp[count($tmp) - 1];
            $videoExt_arr = array('MP4', 'MOV', 'WMV', 'AVI', 'FLV', 'MKV', 'WEBM');
            $getImages[$p]['eFileType'] = 'Image';
            $getImages[$p]['ThumbImage'] = '';
            if (in_array(strtoupper($ext), $videoExt_arr)) {
                $getImages[$p]['eFileType'] = 'Video';
                $getImages[$p]['ThumbImage'] = getVideoThumbImageProvider($getImages[$p]['vImage']);
            }
            $getImages[$p]['vImage'] = $tconfig['tsite_upload_prescription_image'] . '/' . $getImages[$p]['vImage'];
        }
    }
    else { //displaying recent list
        $getImagearray = $obj->MySQLSelect("SELECT iImageId FROM prescription_images WHERE eStatus='Active' AND iUserId='" . $iUserId . "' AND order_id = 0");
        foreach ($getImagearray as $key => $value) {
            $getImages_duplicate = $obj->MySQLSelect("SELECT iImageId FROM prescription_images WHERE duplicate_id = '" . $value['iImageId'] . "'");
            foreach ($getImages_duplicate as $key => $value) {
                if (!empty($value['iImageId'])) $except_imagearray .= $value['iImageId'] . ",";
            }
        }
        $except_imagearray = rtrim($except_imagearray, ',');
        if (!empty($except_imagearray)) {
            $getImages = $obj->MySQLSelect("SELECT * FROM prescription_images WHERE eStatus='Active' AND iUserId='" . $iUserId . "' AND order_id = 0 AND iImageId NOT IN (" . $except_imagearray . ")");
        }
        else {
            $getImages = $obj->MySQLSelect("SELECT * FROM prescription_images WHERE eStatus='Active' AND iUserId='" . $iUserId . "' AND order_id = 0");
        }
        for ($p = 0; $p < count($getImages); $p++) {
            $tmp = explode(".", $getImages[$p]['vImage']);
            for ($i = 0; $i < count($tmp) - 1; $i++) {
                $tmp1[] = $tmp[$i];
            }
            $file = implode("_", $tmp1);
            $ext = $tmp[count($tmp) - 1];
            $videoExt_arr = array('MP4', 'MOV', 'WMV', 'AVI', 'FLV', 'MKV', 'WEBM');
            $getImages[$p]['eFileType'] = 'Image';
            $getImages[$p]['ThumbImage'] = '';
            if (in_array(strtoupper($ext), $videoExt_arr)) {
                $getImages[$p]['eFileType'] = 'Video';
                $getImages[$p]['ThumbImage'] = getVideoThumbImageProvider($getImages[$p]['vImage']);
            }
            $getImages[$p]['vImage'] = $tconfig['tsite_upload_prescription_image'] . '/' . $getImages[$p]['vImage'];
        }
    }
    if (!empty($getImages)) {
        $returnArr['Action'] = "1";
        $returnArr['message'] = $getImages;
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "";
    }
    setDataResponse($returnArr);
}
if ($type == 'CheckPrescriptionRequired') { //check prescription required or not for the every menu items which is set from the admin side
    $iServiceId = isset($_REQUEST["iServiceId"]) ? $_REQUEST['iServiceId'] : '';
    $iMenuItemId = isset($_REQUEST["iMenuItemId"]) ? $_REQUEST['iMenuItemId'] : '';
    /* $servFields = 'prescription_required'; //check using menuitem only bc using serviceid getting issue in ios(Dhruvin) in cart...
      $ServiceCategoryData = get_value('service_categories', $servFields, 'iServiceId', $iServiceId)[0]['prescription_required'];

      if($ServiceCategoryData=='No') {
      $returnArr['Action'] = "0";
      $returnArr['message'] = "LBL_NO_RECORDS_FOUND1";
      setDataResponse($returnArr);
  } */
    $items = $obj->MySQLSelect("SELECT iMenuItemId FROM menu_items WHERE eStatus='Active' AND iMenuItemId IN(" . $iMenuItemId . ") AND prescription_required = 'Yes'");
    if ((count($items)) > 0) {
        $returnArr['Action'] = "1";
        $returnArr['message'] = "LBL_PRESCRIPTION_UPLOAD";
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_NO_RECORDS_FOUND1";
    }
    setDataResponse($returnArr);
}
if ($type == 'PreviouslyUploadedbyYou') { //previously uploaded by you, here when user select image from the history which is he was uploaded before... then we have generated duplicate entry for it in the table..and in the image folder image is copied..in the field duplicate_id..id is image_id
    $iUserId = isset($_REQUEST["iUserId"]) ? $_REQUEST['iUserId'] : '';
    $iImageId = isset($_REQUEST["iImageId"]) ? $_REQUEST['iImageId'] : ''; //uploaded from previously uploaded by you
    if (!empty($iImageId)) {
        $Data_update_images['iUserId'] = $iUserId;
        $Data_update_images['tAddedDate'] = @date("Y-m-d H:i:s");
        $getImages = $obj->MySQLSelect("SELECT * FROM prescription_images WHERE eStatus='Active' AND iImageId IN (" . $iImageId . ") AND iUserId = '" . $iUserId . "'"); // put in for the multiple image select
        foreach ($getImages as $key => $value) { //foreach because if multiple image select
            $getImages_already = $obj->MySQLSelect("SELECT * FROM prescription_images WHERE duplicate_id = '" . $value['iImageId'] . "' AND order_id = 0"); //check if in history select img123, then again in attach prescription select img123 then it is not added, in recent list displayed images one time only..order id = 0 because if it is from previous order then it is first time..then again add that item then for that item orderid will be 0
            if (empty($getImages_already)) {
                $imgext = explode('.', $value['vImage']);
                $Data_update_images['duplicate_id'] = $value['iImageId'];
                //$new_imagename = uniqid().".".$imgext[1];
                $unique = uniqid('', true);
                $file_nameTmp = substr($unique, strlen($unique) - 4, strlen($unique));
                $new_imagename = $file_nameTmp . "_" . date("YmdHis") . "." . $imgext[1];
                $copyfile = copy($tconfig['tsite_upload_prescription_image_path'] . '/' . $value['vImage'], $tconfig['tsite_upload_prescription_image_path'] . '/' . $new_imagename);
                if ($copyfile == 1) {
                    $Data_update_images['vImage'] = $new_imagename;
                }
                $id = $obj->MySQLQueryPerform("prescription_images", $Data_update_images, 'insert');
            }
            else {
                $id = 1;
            }
        }
        if ($id > 0) {
            $returnArr['Action'] = "1";
            $returnArr['message'] = "LBL_IMAGE_UPLOAD_SUCCESS_NOTE";
        }
        else {
            $returnArr['Action'] = "0";
            $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
        }
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
    }
    setDataResponse($returnArr);
}
if ($type == 'GetOrderPrescriptionImages') { //Get all images from the order(store)...
    //global $obj; // Commented By HJ On 15-07-2020 Bcoz Not Required
    $iOrderId = isset($_REQUEST["iOrderId"]) ? $_REQUEST['iOrderId'] : '';
    $getImages = $obj->MySQLSelect("Select * from prescription_images WHERE order_id = '" . $iOrderId . "'");
    if (!empty($getImages)) {
        $returnArr['Action'] = "1";
        $returnArr['message'] = "LBL_IMAGE_UPLOAD_SUCCESS_NOTE";
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
    }
    setDataResponse($returnArr);
}
if ($type == 'removePrescriptionImagesForCart') { //when remove all items from the cart, it will remove prescription images
    //global $obj; // Commented By HJ On 15-07-2020 Bcoz Not Required
    $iUserId = isset($_REQUEST["iUserId"]) ? $_REQUEST['iUserId'] : '';
    $sql = "DELETE FROM prescription_images WHERE `iUserId`='" . $iUserId . "' AND `order_id` = 0";
    $id = $obj->sql_query($sql);
    if ($id > 0) {
        $returnArr['Action'] = "1";
        $returnArr['message'] = "LBL_IMAGE_DELETE_SUCCESS_NOTE";
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
    }
    setDataResponse($returnArr);
}
function setorderid_for_prescription($iUserId, $iOrderId)
{ //when place order in prescription images table orderid is updated
    global $obj;
    if (!empty($iUserId) && !empty($iOrderId)) {
        $updateQuery = "UPDATE prescription_images SET order_id = '" . $iOrderId . "' WHERE iUserId='" . $iUserId . "' AND order_id = 0";
        //$obj->MySQLSelect("UPDATE prescription_images SET order_id = '".$iOrderId."' WHERE iUserId='" . $iUserId . "'");
        $obj->sql_query($updateQuery);
    }
    return true;
}

####################### For Prescription required end added by SP #################################
if ($type == "updateThermalPrintStatus") {
    $iMemberId = isset($_REQUEST["iMemberId"]) ? $_REQUEST['iMemberId'] : '';
    $eThermalAutoPrint = isset($_REQUEST["eThermalAutoPrint"]) ? $_REQUEST['eThermalAutoPrint'] : '';
    $eThermalPrintEnable = isset($_REQUEST["eThermalPrintEnable"]) ? $_REQUEST['eThermalPrintEnable'] : '';
    if (isset($iMemberId) && !empty($iMemberId) && !empty($eThermalAutoPrint) && !empty($eThermalPrintEnable)) {
        $where = " iCompanyId = '" . $iMemberId . "'";
        $data_company['eThermalPrintEnable'] = $eThermalPrintEnable;
        $data_company['eThermalAutoPrint'] = $eThermalAutoPrint;
        $obj->MySQLQueryPerform("company", $data_company, 'update', $where);
        $returnArr['Action'] = "1";
        $returnArr['message'] = getCompanyDetailInfo($iMemberId);
        $returnArr['message1'] = "LBL_INFO_UPDATED_TXT";
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
    }
    setDataResponse($returnArr);
}
// ##########################Call Masking##########################################################
if ($type == "getCallMaskNumber") {
    //global $tconfig; // Commented By HJ On 15-07-2020 Bcoz Not Required
    $returnArr = array();
    $iTripId = isset($_REQUEST['iTripid']) ? $_REQUEST['iTripid'] : '';
    $GeneralDeviceType = isset($_REQUEST['GeneralDeviceType']) ? $_REQUEST['GeneralDeviceType'] : '';
    $UserType = isset($_REQUEST['UserType']) ? $_REQUEST['UserType'] : '';
    $returnArr['Action'] = "0";
    $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
    setDataResponse($returnArr);
}
// ###########################call masking Ends##########################################################
if ($type == 'getReceiptOrder') {
    $iOrderId = isset($_REQUEST['iOrderId']) ? clean($_REQUEST['iOrderId']) : '';
    if (empty($iServiceId)) $iServiceId = 1;
    $value = $COMM_MEDIA_OBJ->orderemaildataRecipt($iOrderId, 'Passenger', $iServiceId);
    
    if ($value === true || $value == "true" || $value == "1") {
        $returnArr['Action'] = "1";
        $returnArr['message'] = "LBL_CHECK_INBOX_TXT";
    }
    else if ($value == "3" || $value == 3) {
        $returnArr['Action'] = "1";
        $returnArr['message'] = "LBL_NO_EMAIL_ADDED";
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_FAILED_SEND_RECEIPT_EMAIL_TXT";
    }
    setDataResponse($returnArr);
}

// ############################## Collect Tip Order #############################################
if ($type == "OrderCollectTip") {
    $iMemberId = isset($_REQUEST["GeneralMemberId"]) ? $_REQUEST["GeneralMemberId"] : ''; // for both driver or passenger
    $iOrderId = isset($_REQUEST["iOrderId"]) ? $_REQUEST["iOrderId"] : '';
    $userType = isset($_REQUEST["GeneralUserType"]) ? $_REQUEST["GeneralUserType"] : 'Passenger'; // Passenger or Driver
    $fTipAmount = isset($_REQUEST["fTipAmount"]) ? $_REQUEST["fTipAmount"] : 0;
    $selectedTipPos = isset($_REQUEST["selectedTipPos"]) ? $_REQUEST["selectedTipPos"] : 0;
    if ($fTipAmount > 0 || $selectedTipPos > 0) {
        $tbl_name = "register_user";
        $currencycode = "vCurrencyPassenger";
        $sql = "SELECT vName,vLastName,vEmail,vPhone,$currencycode from $tbl_name WHERE iUserId = '" . $iMemberId . "'";
        $user_detail = $obj->MySQLSelect($sql);
        $userCurrencyData = $obj->MySQLSelect("SELECT * FROM currency WHERE vName = '" . $user_detail[0][$currencycode] . "'");
        $DefaultCurrencyData = $obj->MySQLSelect("SELECT * FROM currency WHERE eDefault = 'Yes'");
        $orderfSubTotal = get_value('orders', 'fSubTotal', 'iOrderId', $iOrderId, '', 'true');
        $orderfOffersDiscount = get_value('orders', 'fOffersDiscount', 'iOrderId', $iOrderId, '', 'true');
        if ($DELIVERY_TIP_AMOUNT_TYPE_DELIVERALL == "Percentage") {
            if ($_REQUEST['selectedTipPos'] == 1) {
                $fTipAmount = ($TIP_AMOUNT_1 / 100) * ($orderfSubTotal - $orderfOffersDiscount);
            }
            else if ($_REQUEST['selectedTipPos'] == 2) {
                $fTipAmount = ($TIP_AMOUNT_2 / 100) * ($orderfSubTotal - $orderfOffersDiscount);
            }
            else if ($_REQUEST['selectedTipPos'] == 3) {
                $fTipAmount = ($TIP_AMOUNT_3 / 100) * ($orderfSubTotal - $orderfOffersDiscount);
            }
            else if ($_REQUEST['selectedTipPos'] == 4) {
                $fTipAmount = $fTipAmount / $userCurrencyData[0]['Ratio'];
                $fTipAmount = $fTipAmount * $DefaultCurrencyData[0]['Ratio'];
            }
        }
        $orderfTipAmount = get_value('orders', 'fTipAmount', 'iOrderId', $iOrderId, '', 'true');
        if ($orderfTipAmount > 0) {
            $returnArr['Action'] = "0";
            $returnArr['message'] = "LBL_DELIVERY_TIP_GIVEN_TXT";
            setDataResponse($returnArr);
        }
        OrderDeliveryTipPayment($iMemberId, $iOrderId, $fTipAmount);
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_SELECT_ENTER_TIP_AMOUNT_TXT";
        setDataResponse($returnArr);
    }
}
// ############################## Collect Tip Order End #############################################

// ############################## Add by HV on 02-09-2020 for Genie Update Review Item Details #############################################
if ($type == "UpdateOrderReviewItemDetails") {
    $iOrderId = isset($_REQUEST['iOrderId']) ? $_REQUEST['iOrderId'] : "";
    $iOrderDetailId = isset($_REQUEST['iOrderDetailId']) ? $_REQUEST['iOrderDetailId'] : "";
    $iItemPrice = isset($_REQUEST['iItemPrice']) ? $_REQUEST['iItemPrice'] : "";
    $eItemAvailable = isset($_REQUEST['eItemAvailable']) ? $_REQUEST['eItemAvailable'] : "Yes";
    $image_name = isset($_FILES['vImage']['name']) ? $_FILES['vImage']['name'] : '';
    $image_object = isset($_FILES['vImage']['tmp_name']) ? $_FILES['vImage']['tmp_name'] : '';
    $OrderData = $obj->MySQLSelect("SELECT iDriverId,fNetTotal,fTotalGenerateFare,fDeliveryCharge FROM orders WHERE iOrderId = $iOrderId");
    $sqlp = "SELECT cu.vName, cu.iCurrencyId, cu.eRoundingOffEnable, rd.vCurrencyDriver, cu.ratio, rd.vLang FROM register_driver AS rd LEFT JOIN currency AS cu ON rd.vCurrencyDriver = cu.vName WHERE rd.iDriverId = '" . $OrderData[0]['iDriverId'] . "'";
    $currData = $obj->MySQLSelect($sqlp);
    $currencycode = $currData[0]['vCurrencyDriver'];
    $vLang = $currData[0]['vLang'];
    if ($vLang == "" || $vLang == NULL) {
        $vLang = $LANG_OBJ->FetchDefaultLangData("vCode");
    }
    $eExtraPayment = get_value('order_details', 'eExtraPayment', 'iOrderDetailId', $iOrderDetailId, '', 'true');
    $rItemPrice = $iItemPrice / $currData[0]['ratio'];
    $Data_Item_Detail_Insert['iOrderId'] = $iOrderId;
    $Data_Item_Detail_Insert['iOrderDetailId'] = $iOrderDetailId;
    $Data_Item_Detail_Insert['fItemPrice'] = ($eItemAvailable == "Yes") ? $rItemPrice : 0;
    $Data_Item_Detail_Insert['eItemAvailable'] = $eItemAvailable;
    $Data_Item_Detail_Insert['eExtraPayment'] = $eExtraPayment;
    $checkItem = $obj->MySQLSelect("SELECT iItemDetailsId,vItemImage FROM order_items_buy_anything WHERE iOrderId = $iOrderId AND iOrderDetailId = $iOrderDetailId");
    if (count($checkItem) > 0) {
        $iItemDetailsId = $checkItem[0]['iItemDetailsId'];
        $whereItem = " iItemDetailsId  = '$iItemDetailsId'";
        $obj->MySQLQueryPerform("order_items_buy_anything", $Data_Item_Detail_Insert, 'update', $whereItem);
        $oldImage = $checkItem[0]['vItemImage'];
    }
    else {
        $iItemDetailsId = $obj->MySQLQueryPerform("order_items_buy_anything", $Data_Item_Detail_Insert, 'insert');
    }
    if ($image_object != "") {
        if ($image_object) {
            ExifCleaning::adjustImageOrientation($image_object);
        }
        $where = " iItemDetailsId  = '$iItemDetailsId'";
        if ($image_name != "") {
            $Photo_Gallery_folder = $tconfig['tsite_upload_order_buy_anything_path'];
            if (!is_dir($Photo_Gallery_folder)) {
                mkdir($Photo_Gallery_folder, 0777);
                chmod($Photo_Gallery_folder, 0777);
            }
            $vFile = $UPLOAD_OBJ->GeneralFileUpload($Photo_Gallery_folder, $image_object, $image_name, $prefix = '', $vaildExt = "bmp,pdf,doc,docx,jpg,jpeg,gif,png,xls,xlsx,csv");
            $vImageName = $vFile[0];
            $Data_Item_Detail_Update['vItemImage'] = $vImageName;
            $obj->MySQLQueryPerform("order_items_buy_anything", $Data_Item_Detail_Update, 'update', $where);
            if (file_exists($Photo_Gallery_folder . $oldImage)) {
                unlink($Photo_Gallery_folder . $oldImage);
            }
        }
    }
    $orderTotal = $OrderData[0]['fDeliveryCharge'];
    $itemDetails = $obj->MySQLSelect("SELECT oba.*,od.iQty,mi.vItemType_$vLang,mi.vItemTypeBuyAnyService FROM order_items_buy_anything as oba LEFT JOIN order_details as od ON od.iOrderDetailId = oba.iOrderDetailId LEFT JOIN menu_items as mi ON mi.iMenuItemId = od.iMenuItemId WHERE oba.iOrderId = $iOrderId");
    $itemDetailsArr = array();
    foreach ($itemDetails as $sItem) {
        $itemArr = array(
            'iOrderId' => $sItem['iOrderId'], 'iOrderDetailId' => $sItem['iOrderDetailId'], "iQty" => $sItem['iQty'], "MenuItem" => $sItem['vItemTypeBuyAnyService'], 'fTotPrice' => formateNumAsPerCurrency((setTwoDecimalPoint($sItem['fItemPrice']) * $currData[0]['ratio']), $currencycode), 'vImage' => ($sItem['vItemImage'] != "") ? $tconfig['tsite_upload_order_buy_anything'] . $sItem['vItemImage'] : ""
        );
        $itemArr['itemDetailsUpdated'] = "No";
        if ($sItem['vItemImage'] != "") {
            if (!empty($sItem['eItemAvailable'])) {
                $itemArr['itemDetailsUpdated'] = "Yes";
            }
        }
        $itemDetailsArr[] = $itemArr;
        $orderTotal = $orderTotal + $sItem['fItemPrice'];
    }
    $Data_update_order['fNetTotal'] = $orderTotal;
    $Data_update_order['fTotalGenerateFare'] = $orderTotal;
    $where_order = " iOrderId  = '$iOrderId'";
    $obj->MySQLQueryPerform("orders", $Data_update_order, 'update', $where_order);
    $returnArr['Action'] = "1";
    $returnArr['itemlist'] = $itemDetailsArr;
    setDataResponse($returnArr);
}
// ############################## Add by HV on 02-09-2020 for Genie Update Review Item Details End #############################################
// ############################## Add by HV on 01-09-2020 for Genie Confirm Items #############################################
if ($type == "ConfirmReviewItems") {
    $iOrderId = isset($_REQUEST['iOrderId']) ? $_REQUEST['iOrderId'] : '';
    $iOrderDetailId = isset($_REQUEST['iOrderDetailId']) ? $_REQUEST['iOrderDetailId'] : '';
    $iItemDetailsId = isset($_REQUEST['iItemDetailsId']) ? $_REQUEST['iItemDetailsId'] : '';
    $eConfirm = isset($_REQUEST['eConfirm']) ? $_REQUEST['eConfirm'] : 'No';
    $eDecline = isset($_REQUEST['eDecline']) ? $_REQUEST['eDecline'] : 'No';
    $UserType = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : "Driver";
    $Data_update['eConfirm'] = 'No';
    $Data_update['eDecline'] = 'No';
    if ($eConfirm == 'Yes') {
        $Data_update['eConfirm'] = 'Yes';
        $Data_update['eDecline'] = 'No';
    }
    if ($eDecline == 'Yes') {
        $Data_update['eConfirm'] = 'No';
        $Data_update['eDecline'] = 'Yes';
    }
    $where = " iItemDetailsId  = '$iItemDetailsId' ";
    $obj->MySQLQueryPerform("order_items_buy_anything", $Data_update, 'update', $where);
    $OrderData = $obj->MySQLSelect("SELECT * FROM orders WHERE iOrderId = $iOrderId");
    $iUserId = $OrderData[0]['iUserId'];
    $iDriverId = $OrderData[0]['iDriverId'];
    $sqlp = "SELECT cu.vName, cu.iCurrencyId, cu.eRoundingOffEnable, rd.vCurrencyPassenger, cu.ratio, rd.vLang FROM register_user AS rd LEFT JOIN currency AS cu ON rd.vCurrencyPassenger = cu.vName WHERE rd.iUserId = '" . $OrderData[0]['iUserId'] . "'";
    $currData = $obj->MySQLSelect($sqlp);
    $currencycode = $currData[0]['vCurrencyPassenger'];
    $vLang = $currData[0]['vLang'];
    if ($UserType != "Driver") {
        $UserDetailsArr = $obj->MySQLSelect("SELECT vLang FROM register_driver WHERE iDriverId = $iDriverId");
        $vLang = $UserDetailsArr[0]['vLang'];
    }
    if ($vLang == "" || $vLang == NULL) {
        $vLang = $LANG_OBJ->FetchDefaultLangData("vCode");
    }
    $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLang, "1", $iServiceId);
    //$DriverData = $obj->MySQLSelect("SELECT vLang FROM register_driver WHERE iDriverId = $iDriverId");
    //$languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($DriverData[0]['vLang'], "1", $iServiceId);
    $itemDetails = $obj->MySQLSelect("SELECT oba.*,od.iQty,mi.vItemType_$vLang,mi.vItemTypeBuyAnyService FROM order_items_buy_anything as oba LEFT JOIN order_details as od ON od.iOrderDetailId = oba.iOrderDetailId LEFT JOIN menu_items as mi ON mi.iMenuItemId = od.iMenuItemId WHERE oba.iOrderId = $iOrderId");
    $itemDetailsArr = array();
    $orderTotal = $OrderData[0]['fDeliveryCharge'] + $OrderData[0]['fTax'] + $OrderData[0]['fOutStandingAmount'];
    foreach ($itemDetails as $sItem) {
        $itemArr = array(
            'iItemDetailsId' => $sItem['iItemDetailsId'], 'iOrderId' => $sItem['iOrderId'], 'iOrderDetailId' => $sItem['iOrderDetailId'], "iQty" => $sItem['iQty'], "MenuItem" => $sItem['vItemTypeBuyAnyService'], 'fTotPrice' => formateNumAsPerCurrency((setTwoDecimalPoint($sItem['fItemPrice']) * $currData[0]['ratio']), $currencycode), 'vImage' => ($sItem['vItemImage'] != "") ? $tconfig['tsite_upload_order_buy_anything'] . $sItem['vItemImage'] : ""
        );
        $itemArr['itemDetailsUpdated'] = "No";
        if ($sItem['fItemPrice'] > 0 && $sItem['vItemImage'] != "") {
            $itemArr['itemDetailsUpdated'] = "Yes";
        }
        $itemDetailsArr[] = $itemArr;
        if ($sItem['eConfirm'] == "Yes") {
            $orderTotal = $orderTotal + $sItem['fItemPrice'];
        }
    }
    $Data_update_order['fNetTotal'] = ($orderTotal <= 0) ? 0 : $orderTotal;
    $Data_update_order['fTotalGenerateFare'] = ($orderTotal <= 0) ? 0 : $orderTotal;
    $where_order = " iOrderId  = '$iOrderId'";
    $obj->MySQLQueryPerform("orders", $Data_update_order, 'update', $where_order);
    $itemDetailsingle = $obj->MySQLSelect("SELECT oba.*,od.iQty,mi.vItemType_$vLang,mi.vItemTypeBuyAnyService FROM order_items_buy_anything as oba LEFT JOIN order_details as od ON od.iOrderDetailId = oba.iOrderDetailId LEFT JOIN menu_items as mi ON mi.iMenuItemId = od.iMenuItemId WHERE oba.iOrderId = $iOrderId AND oba.eConfirm = 'No' AND oba.eDecline='No' AND eItemAvailable = 'Yes'");
    $itemsConfirmed = "Yes";
    if (count($itemDetailsingle) > 0) {
        $itemArr = array(
            'iItemDetailsId' => $itemDetailsingle[0]['iItemDetailsId'], 'iOrderId' => $itemDetailsingle[0]['iOrderId'], 'iOrderDetailId' => $itemDetailsingle[0]['iOrderDetailId'], "iQty" => $itemDetailsingle[0]['iQty'], "MenuItem" => $itemDetailsingle[0]['vItemTypeBuyAnyService'], 'fTotPrice' => formateNumAsPerCurrency((setTwoDecimalPoint($itemDetailsingle[0]['fItemPrice']) * $currData[0]['ratio']), $currencycode), 'vImage' => ($itemDetailsingle[0]['vItemImage'] != "") ? $tconfig['tsite_upload_order_buy_anything'] . $itemDetailsingle[0]['vItemImage'] : ""
        );
        $returnArr['itemForReview'] = $itemArr;
        $itemsConfirmed = "No";
    }
    if ($itemsConfirmed == "Yes") {
        $itemCount = $obj->MySQLSelect("SELECT COUNT(iItemDetailsId) as itemCount FROM order_items_buy_anything WHERE iOrderId = $iOrderId AND eConfirm = 'Yes'");
        $tripData = $obj->MySQLSelect("SELECT iTripId,fDeliveryCharge FROM trips WHERE iOrderId = $iOrderId");
        $iTripId = $tripData[0]['iTripId'];
        if ($itemCount[0]['itemCount'] > 0) {
            $orderdata = $obj->MySQLSelect("SELECT iUserId,ePaymentOption,fNetTotal,fDeliveryCharge,fOutStandingAmount,eCheckUserWallet,vOrderNo FROM orders WHERE iOrderId = $iOrderId");
            if ($orderdata[0]['ePaymentOption'] == "Card" || $orderdata[0]['ePaymentOption'] == "Wallet") {
                $storeBillAmount = $orderdata[0]['fNetTotal'] - ($orderdata[0]['fDeliveryCharge'] + $orderdata[0]['fOutStandingAmount']);
                if ($storeBillAmount <= 0) {
                    $data['ePaid'] = "Yes";
                }
                $data['iStatusCode'] = '14';
                createOrderLog($iOrderId, "14");
            }
            if ($orderdata[0]['ePaymentOption'] == "Cash" && $orderdata[0]['eCheckUserWallet'] == "Yes" && $orderdata[0]['fNetTotal'] > 0) {
                $user_available_balance = $WALLET_OBJ->FetchMemberWalletBalance($orderdata[0]['iUserId'], "Rider");
                $fNetTotal = $orderdata[0]['fNetTotal'];
                if ($fNetTotal > $user_available_balance) {
                    $fNetTotal = $fNetTotal - $user_available_balance;
                    $user_wallet_debit_amount = $user_available_balance;
                }
                else {
                    $user_wallet_debit_amount = ($fNetTotal > 0) ? $fNetTotal : 0;
                    $fNetTotal = 0;
                }
                $Data_update_order1['fNetTotal'] = $fNetTotal;
                $Data_update_order1['fWalletDebit'] = $user_wallet_debit_amount;
                $where_ord = " iOrderId = '" . $iOrderId . "'";
                $obj->MySQLQueryPerform("orders", $Data_update_order1, 'update', $where_ord);
                // Update User Wallet
                if ($user_wallet_debit_amount > 0) {
                    $vRideNo = $orderdata[0]['vOrderNo'];
                    $data_wallet['iUserId'] = $iUserId;
                    $data_wallet['eUserType'] = "Rider";
                    $data_wallet['iBalance'] = $user_wallet_debit_amount;
                    $data_wallet['eType'] = "Debit";
                    $data_wallet['dDate'] = date("Y-m-d H:i:s");
                    $data_wallet['iTripId'] = $iTripId;
                    $data_wallet['iOrderId'] = $iOrderId;
                    $data_wallet['eFor'] = "Charges";
                    $data_wallet['ePaymentStatus'] = "Unsettelled";
                    $data_wallet['tDescription'] = "#LBL_DEBITED_BOOKING_DL#" . " " . $vRideNo;
                    $WALLET_OBJ->PerformWalletTransaction($data_wallet['iUserId'], $data_wallet['eUserType'], $data_wallet['iBalance'], $data_wallet['eType'], $iTripId, $data_wallet['eFor'], $data_wallet['tDescription'], $data_wallet['ePaymentStatus'], $data_wallet['dDate'], $iOrderId);
                }
            }
            $where = " iOrderId = '$iOrderId'";
            $data['genieUserApproved'] = "Yes";
            if ($currData[0]['eRoundingOffEnable'] == "Yes" && $OrderData[0]['ePaymentOption'] == "Cash" && $MODULES_OBJ->isEnableRoundingMethod()) {
                $fNetTotal = get_value('orders', 'fNetTotal', 'iOrderId', $iOrderId, '', 'true');
                $fNetTotal = setTwoDecimalPoint($fNetTotal * $currData[0]['ratio']);
                $roundingOffTotal_fare_amountArr = getRoundingOffAmount($fNetTotal, $currencycode);
                $roundingOffTotal_fare_amount = $roundingOffTotal_fare_amountArr['finalFareValue'];
                if ($roundingOffTotal_fare_amountArr['method'] == "Addition") {
                    $eRoundingType = "Addition";
                }
                else {
                    $eRoundingType = "Substraction";
                }
                $fRoundingAmount = setTwoDecimalPoint($roundingOffTotal_fare_amountArr['differenceValue']);
                $data['fRoundingAmount'] = $fRoundingAmount;
                $data['eRoundingType'] = $eRoundingType;
            }
            $id = $obj->MySQLQueryPerform("orders", $data, 'update', $where);
            $DriverMessage = "OrderItemsReviewed";
            if ($OrderData[0]['ePaymentOption'] == "Card") {
                $tripdriverarrivlbl = $languageLabelsArr['LBL_ITEMS_REVIEWED_NOTIFICATION_TXT'];
                if ($storeBillAmount <= 0) {
                    $tripdriverarrivlbl = $languageLabelsArr['LBL_ITEMS_REVIEWED_PICKUP_NOTIFICATION_TXT'];
                }
            }
            else {
                $tripdriverarrivlbl = $languageLabelsArr['LBL_ITEMS_REVIEWED_DELIVERY_NOTIFICATION_TXT'];
            }
        }
        else {
            $where = " iOrderId = '$iOrderId'";
            $data['iStatusCode'] = '8';
            $data['genieUserApproved'] = "Yes";
            $data['eCancelledBy'] = "Passenger";
            $data['vCancelReason'] = $languageLabelsArr['LBL_CANCELLED_BY_USER'];
            $id = $obj->MySQLQueryPerform("orders", $data, 'update', $where);
            createOrderLog($iOrderId, "8");
            $query1 = "UPDATE register_driver SET vTripStatus = 'Cancelled' WHERE iDriverId = '" . $OrderData[0]['iDriverId'] . "'";
            $obj->sql_query($query1);
            $query2 = "UPDATE trips SET iActive = 'Canceled' WHERE iOrderId = '" . $iOrderId . "'";
            $obj->sql_query($query2);
            $fCancellationCharge = $OrderData[0]['fDeliveryChargeCancelled'];
            if ($fCancellationCharge > 0) {
                $query_trip_outstanding_amount = "INSERT INTO `trip_outstanding_amount`(`iOrderId`, `iTripId`, `iUserId`, `iDriverId`,`iCompanyId`,`fCancellationFare`,`fPendingAmount`) VALUES ('" . $iOrderId . "','" . $iTripId . "','" . $iUserId . "','" . $iDriverId . "','" . $OrderData[0]['iCompanyId'] . "','" . $fCancellationCharge . "','" . $fCancellationCharge . "')";
                $last_insert_id = $obj->MySQLInsert($query_trip_outstanding_amount);
                $sql = "SELECT * FROM currency WHERE eStatus = 'Active'";
                $db_curr = $obj->MySQLSelect($sql);
                $where = "iTripOutstandId = '" . $last_insert_id . "'";
                for ($i = 0; $i < count($db_curr); $i++) {
                    $data_currency_ratio['fRatio_' . $db_curr[$i]['vName']] = $db_curr[$i]['Ratio'];
                    $obj->MySQLQueryPerform("trip_outstanding_amount", $data_currency_ratio, 'update', $where);
                }
            }
            $drvTitleReasonMessage = $languageLabelsArr['LBL_CANCELLED_BY_USER'];
            $DriverMessage = $MessageUser = "OrderCancelByAdmin";
            $tripdriverarrivlbl = $languageLabelsArr['LBL_ORDER_CANCEL_TEXT'] . " " . $languageLabelsArr['LBL_REASON_TXT'] . " " . $drvTitleReasonMessage;
        }
        $sql = "SELECT iDriverVehicleId,vCurrencyDriver,iAppVersion,CONCAT(vName,' ',vLastName) AS driverName FROM `register_driver` WHERE iDriverId = '$iDriverId'";
        $Data_vehicle = $obj->MySQLSelect($sql);
        $drivername = $Data_vehicle[0]['driverName'];
        $alertMsg = $tripdriverarrivlbl;
        $message_arr = array();
        $message_arr['iDriverId'] = $iDriverId;
        $message_arr['Message'] = $DriverMessage;
        $message_arr['iTripId'] = strval($iTripId);
        $message_arr['DriverAppVersion'] = strval($Data_vehicle[0]['iAppVersion']);
        $message_arr['driverName'] = $drivername;
        $message_arr['time'] = intval(microtime(true) * 1000);
        // $message_arr['vRideNo'] = $TripRideNO;
        $message_arr['iOrderId'] = $iOrderId;
        $message_arr['vTitle'] = $alertMsg;
        $message_arr['eSystem'] = "DeliverAll";
        $message = json_encode($message_arr);
        if ($iTripId > 0) {
            $sql = "SELECT iGcmRegId,eDeviceType,eAppTerminate,tSessionId,eDebugMode,eHmsDevice FROM register_driver WHERE iDriverId='$iDriverId'";
            $result = $obj->MySQLSelect($sql);
            $channelName = "DRIVER_" . $iDriverId;
            $message_arr['tSessionId'] = $result[0]['tSessionId'];
            $message_arr['time'] = intval(microtime(true) * 1000);
            $generalDataArr[] = array(
                'eDeviceType' => $result[0]['eDeviceType'], 'deviceToken' => $result[0]['iGcmRegId'], 'alertMsg' => $alertMsg, 'eAppTerminate' => $result[0]['eAppTerminate'], 'eDebugMode' => $result[0]['eDebugMode'], 'eHmsDevice' => $result[0]['eHmsDevice'], 'message' => $message_arr, 'channelName' => $channelName
            );
            $EVENT_MSG_OBJ->send(array('GENERAL_DATA' => $generalDataArr), RN_PROVIDER);
        }
    }
    $returnArr['Action'] = "1";
    $returnArr['genieUserApproved'] = $itemsConfirmed;
    // $returnArr['itemDetails'] = $itemDetailsArr;
    setDataResponse($returnArr);
}
// ############################## Add by HV on 01-09-2020 for Genie Confirm Items End #############################################
// ############################## Add by HV on 01-09-2020 for Genie Collect Payment #############################################
if ($type == "CollectPaymentBuyAnything") {
    $iUserId = isset($_REQUEST['iUserId']) ? $_REQUEST['iUserId'] : "";
    $UserType = isset($_REQUEST['UserType']) ? $_REQUEST['UserType'] : "";
    $iOrderId = isset($_REQUEST['iOrderId']) ? $_REQUEST['iOrderId'] : "";
    $ePaymentOption = isset($_REQUEST['ePaymentOption']) ? $_REQUEST['ePaymentOption'] : "";
    $CheckUserWallet = isset($_REQUEST["CheckUserWallet"]) ? $_REQUEST["CheckUserWallet"] : 'No';
    $payStatus = isset($_REQUEST["payStatus"]) ? $_REQUEST["payStatus"] : '';
    $SYSTEM_TYPE = isset($_REQUEST['SYSTEM_TYPE']) ? $_REQUEST['SYSTEM_TYPE'] : 'APP';
    $cardToken = isset($_REQUEST['cardToken']) ? $_REQUEST['cardToken'] : '';
    $tPaymentId = isset($_REQUEST['tPaymentId']) ? $_REQUEST['tPaymentId'] : '';
    $vPayMethod = isset($_REQUEST['vPayMethod']) ? $_REQUEST['vPayMethod'] : '';
    $eWalletIgnore = isset($_REQUEST['eWalletIgnore']) ? $_REQUEST['eWalletIgnore'] : 'No';
    if ($payStatus != "succeeded" && $payStatus != "") {
        $payStatus = "Failed";
    }
    if ($CheckUserWallet == "" || $CheckUserWallet == NULL) {
        $CheckUserWallet = "No";
    }
    $orderData = $obj->MySQLSelect("SELECT * FROM orders WHERE iOrderId = $iOrderId");
    $iUserId = $orderData[0]['iUserId'];
    $iDriverId = $orderData[0]['iDriverId'];
    $ePaymentOption = $orderData[0]['ePaymentOption'];
    $CheckUserWallet = $orderData[0]['eCheckUserWallet'];
    $ePayWallet = $CheckUserWallet;
    $vCountry = "";
    $userData = $obj->MySQLSelect("SELECT vCountry,vCurrencyPassenger FROM register_user WHERE iUserId = $iUserId");
    $vCurrencyPassenger = $userData[0]['vCurrencyPassenger'];
    $vCountry = $userData[0]['vCountry'];
    if (isset($currencyAssociateArr[$vCurrencyPassenger])) {
        $userData[0]['vSymbol'] = $currencyAssociateArr[$vCurrencyPassenger]['vSymbol'];
        $userData[0]['Ratio'] = $currencyAssociateArr[$vCurrencyPassenger]['Ratio'];
        $userData[0]['currencyName'] = $currencyAssociateArr[$vCurrencyPassenger]['vName'];
        $userData[0]['iCurrencyId'] = $currencyAssociateArr[$vCurrencyPassenger]['iCurrencyId'];
        $userData[0]['eRoundingOffEnable'] = $currencyAssociateArr[$vCurrencyPassenger]['eRoundingOffEnable'];
    }
    $user_currency_ratio = $userData[0]['Ratio'];
    $user_currency_symbol = $userData[0]['vSymbol'];
    $fNetTotal = $orderData[0]['fNetTotal'];
    $vOrderNo = $orderData[0]['vOrderNo'];
    if ($ePaymentOption == "Card" || $ePaymentOption == "Wallet") {
        $fNetTotal = $fNetTotal - ($orderData[0]['fDeliveryCharge'] + $orderData[0]['fOutStandingAmount']);
        }
    
    $tUserWalletBalance = $orderData[0]['tUserWalletBalance'];
    $user_wallet_debit_amount = 0;
    $full_adjustment = 0;
    if ($CheckUserWallet == "Yes") {
        $user_available_balance = $WALLET_OBJ->FetchMemberWalletBalance($iUserId, "Rider");
        if ($user_available_balance > 0) {
            $totalCurrentActiveTripsArr = FetchTotalOngoingTrips($iUserId);
            $totalCurrentActiveTripsIdsArr = $totalCurrentActiveTripsArr['ActiveTripIds'];
            $totalCurrentActiveOrderIdsArr = $totalCurrentActiveTripsArr['ActiveOrderIds'];
            $totalCurrentActiveTripsCount = $totalCurrentActiveTripsArr['TotalCount'];
            /*             * ******** Replace current wallet balance of user when System payment flow is Method-2/Method-3 ***** */
            // Charge an amount that is autorized when trip was initially requested in case when multiple jobs are going on.
            // if (($totalCurrentActiveTripsCount > 1 || in_array($iOrderId, $totalCurrentActiveOrderIdsArr) == false) && ($SYSTEM_PAYMENT_FLOW == "Method-2" || $SYSTEM_PAYMENT_FLOW == 'Method-3')) {
            if (($totalCurrentActiveTripsCount > 1 || in_array($iOrderId, $totalCurrentActiveOrderIdsArr) == false) && $ePaymentOption == "Wallet" && $tUserWalletBalance > 0) {
                $user_available_balance = $tUserWalletBalance;
            }
            /*             * ******** Replace current wallet balance of user when System payment flow is Method-2/Method-3 ***** */
        }

        if ($fNetTotal > $user_available_balance) {
            $fNetTotal = $fNetTotal - $user_available_balance;
            $user_wallet_debit_amount = $user_available_balance;
        }
        else {
            $user_wallet_debit_amount = $fNetTotal;
            $fNetTotal = 0;
            $full_adjustment = 1;
        }
    }


    $user_available_balance_wallet = $WALLET_OBJ->FetchMemberWalletBalance($iUserId, "Rider", true, 'order');
    $walletDataArr = array();
    if (is_array($user_available_balance_wallet)) {
        $walletDataArr = $user_available_balance_wallet;
        $user_available_balance_wallet = $walletDataArr['CurrentBalance'];
    }
    
    if (isset($userDetailsArr['register_user_' . $iUserId])) {
        $userData = $userDetailsArr['register_user_' . $iUserId];
    }
    else {
        $userData = $obj->MySQLSelect("SELECT *,iUserId as iMemberId FROM `register_user` WHERE iUserId='" . $iUserId . "'");
        $userDetailsArr['register_user_' . $iUserId] = $userData;
    }
    $vCurrencyPassenger = $userData[0]['vCurrencyPassenger'];
    if (isset($currencyAssociateArr[$vCurrencyPassenger])) {
        $userData[0]['vSymbol'] = $currencyAssociateArr[$vCurrencyPassenger]['vSymbol'];
        $userData[0]['Ratio'] = $currencyAssociateArr[$vCurrencyPassenger]['Ratio'];
        $userData[0]['currencyName'] = $currencyAssociateArr[$vCurrencyPassenger]['vName'];
        $userData[0]['iCurrencyId'] = $currencyAssociateArr[$vCurrencyPassenger]['iCurrencyId'];
        $userData[0]['eRoundingOffEnable'] = $currencyAssociateArr[$vCurrencyPassenger]['eRoundingOffEnable'];
    }
    if (isset($country_data_arr[$vCountry])) {
        $userData[0]['eUnit'] = $country_data_arr[$vCountry]['eUnit'];
    }
    //$user_detail = $obj->MySQLSelect("SELECT ru.vCurrencyPassenger, ru.vName, ru.vLastName, ru.vEmail, ru.vLang, cu.vSymbol, cu.Ratio, co.eUnit FROM register_user as ru, currency as cu, country as co WHERE ru.iUserId='" . $iUserId . "' AND cu.vName = ru.vCurrencyPassenger AND co.vCountryCode = ru.vCountry");
    //Added By HJ On 18-07-2020 For langauge labele and Other Union Table Query Start
    if (isset($languageLabelDataArr['language_label_union_other_food_' . $userLanguageCode . "_" . $iServiceId])) {
        $userLanguageLabelsArr = $languageLabelDataArr['language_label_union_other_food_' . $userLanguageCode . "_" . $iServiceId];
    }
    else {
        $userLanguageLabelsArr = $LANG_OBJ->FetchLanguageLabels($userLanguageCode, "1", $iServiceId);
        $languageLabelDataArr['language_label_union_other_food_' . $userLanguageCode . "_" . $iServiceId] = $userLanguageLabelsArr;
    }
    //Added By HJ On 18-07-2020 For langauge labele and Other Union Table Query End
    $user_currency_ratio = $userData[0]['Ratio'];
    $user_currency_symbol = $userData[0]['vSymbol'];
    $fareAmount = $fNetTotal * $user_currency_ratio;
    /*     * ******** Checking Wallet balance when system payment method-2/method-3 ******* */
    $fareAmount = ($fNetTotal + $user_wallet_debit_amount) * $user_currency_ratio;
    
    if ($ePaymentOption == 'Wallet' && strtoupper($ePayWallet) == "YES" && $eWalletIgnore == "No") {
        $isRestrictToWallet = $PAYMENT_MODE_RESTRICT_TO_WALLET;
        if ($full_adjustment == 0) {
            $returnArr['Action'] = "0";
            $returnArr['iOrderId'] = $iOrderId;
            $returnArr['message'] = "LOW_WALLET_AMOUNT";
            $user_available_balance_wallet = $user_available_balance_wallet * $user_currency_ratio;

            $returnArr['ORIGINAL_WALLET_BALANCE'] = strval($user_available_balance_wallet);
            $returnArr['ORIGINAL_WALLET_BALANCE'] = formateNumAsPerCurrency($returnArr['ORIGINAL_WALLET_BALANCE'], $vCurrencyPassenger);
            $returnArr['ORIGINAL_WALLET_BALANCE_VALUE'] = strval($user_available_balance_wallet);

            if (!empty($walletDataArr) && count($walletDataArr) > 0) {
                $auth_wallet_amount = strval((isset($walletDataArr['TotalAuthorizedAmount']) ? $walletDataArr['TotalAuthorizedAmount'] : 0) * $user_currency_ratio);
                $returnArr['AUTH_AMOUNT'] = $auth_wallet_amount > 0 ? ($user_currency_symbol . ' ' . $auth_wallet_amount) : "";
                $returnArr['AUTH_AMOUNT_VALUE'] = $auth_wallet_amount > 0 ? $auth_wallet_amount : "";
                // $returnArr['ORIGINAL_WALLET_BALANCE'] = $user_currency_symbol . ' ' . strval((isset($walletDataArr['WalletBalance']) ? $walletDataArr['WalletBalance'] : 0) * $user_currency_ratio);
                $returnArr['ORIGINAL_WALLET_BALANCE'] = strval((isset($walletDataArr['WalletBalance']) ? $walletDataArr['WalletBalance'] : 0) * $user_currency_ratio);
                $returnArr['ORIGINAL_WALLET_BALANCE'] = formateNumAsPerCurrency($returnArr['ORIGINAL_WALLET_BALANCE'], $vCurrencyPassenger);
                $returnArr['ORIGINAL_WALLET_BALANCE_VALUE'] = strval((isset($walletDataArr['WalletBalance']) ? $walletDataArr['WalletBalance'] : 0) * $user_currency_ratio);
            }
            $returnArr['CURRENT_JOB_EST_CHARGE'] = formateNumAsPerCurrency($fareAmount, $vCurrencyPassenger);
            $returnArr['CURRENT_JOB_EST_CHARGE_VALUE'] = strval($fareAmount);
            //$returnArr['WALLET_AMOUNT_NEEDED'] = $user_currency_symbol . ' ' . strval($fareAmount - $user_available_balance_wallet);
            $returnArr['WALLET_AMOUNT_NEEDED'] = formateNumAsPerCurrency(($fareAmount - $user_available_balance_wallet), $vCurrencyPassenger);
            $returnArr['WALLET_AMOUNT_NEEDED_VALUE'] = strval($fareAmount - $user_available_balance_wallet);
            if (!empty($walletDataArr) && count($walletDataArr) > 0 && $auth_wallet_amount > 0) {
                $content_msg_low_balance = $userLanguageLabelsArr['LBL_LOW_WALLET_BAL_NOTE_WITH_AUTH_AMT'];
                // if ($SYSTEM_PAYMENT_FLOW == 'Method-3') {
                if (strtoupper($ePayWallet) == "YES" && strtoupper($isRestrictToWallet) == "YES") {
                    $content_msg_low_balance = $userLanguageLabelsArr['LBL_LOW_WALLET_BAL_NOTE_WITH_AUTH_AMT_NO_CASH'];
                }
                $content_msg_low_balance = str_replace("#####", $returnArr['WALLET_AMOUNT_NEEDED'], $content_msg_low_balance);
                if (!empty($returnArr['ORIGINAL_WALLET_BALANCE'])) {
                    $content_msg_low_balance = str_replace("####", $returnArr['ORIGINAL_WALLET_BALANCE'], $content_msg_low_balance);
                }
                if (!empty($returnArr['AUTH_AMOUNT'])) {
                    $content_msg_low_balance = str_replace("###", $returnArr['AUTH_AMOUNT'], $content_msg_low_balance);
                }
                $content_msg_low_balance = str_replace("##", "\n\n", $content_msg_low_balance);
                $returnArr['low_balance_content_msg'] = $content_msg_low_balance;
            }
            else {
                $content_msg_low_balance = $userLanguageLabelsArr['LBL_LOW_WALLET_BAL_NOTE_WITH_AMT'];
                // if ($SYSTEM_PAYMENT_FLOW == 'Method-3') {
                if (strtoupper($ePayWallet) == "YES" && strtoupper($isRestrictToWallet) == "YES") {
                    $content_msg_low_balance = $userLanguageLabelsArr['LBL_LOW_WALLET_BAL_NOTE_WITH_AMT_NO_CASH'];
                }
                $content_msg_low_balance = str_replace("#####", $returnArr['WALLET_AMOUNT_NEEDED'], $content_msg_low_balance);
                if (!empty($returnArr['ORIGINAL_WALLET_BALANCE'])) {
                    $content_msg_low_balance = str_replace("####", $returnArr['ORIGINAL_WALLET_BALANCE'], $content_msg_low_balance);
                }
                if (!empty($returnArr['CURRENT_JOB_EST_CHARGE'])) {
                    $content_msg_low_balance = str_replace("###", $returnArr['CURRENT_JOB_EST_CHARGE'], $content_msg_low_balance);
                }
                $content_msg_low_balance = str_replace("##", "\n\n", $content_msg_low_balance);
                $returnArr['low_balance_content_msg'] = $content_msg_low_balance;
            }
            // if ($SYSTEM_PAYMENT_FLOW == 'Method-3') {
            if (strtoupper($ePayWallet) == "YES" && strtoupper($isRestrictToWallet) == "YES") {
                $returnArr['IS_RESTRICT_TO_WALLET_AMOUNT'] = "Yes";
            }
            else {
                $returnArr['IS_RESTRICT_TO_WALLET_AMOUNT'] = "No";
            }
            setDataResponse($returnArr);
        }
    }
    // $tEstimatedCharge = $fareAmount / $user_currency_ratio;
    // if (!empty($tEstimatedCharge) && $user_available_balance_wallet > $tEstimatedCharge && strtoupper($ePayWallet) == "YES") {
    //     $Data_update_order['tUserWalletBalance'] = $tEstimatedCharge;
    // }
    if ($ePaymentOption == "Card") {
        $DefaultCurrencyData = get_value('currency', 'vName,Ratio', 'eDefault', 'Yes');
        $currencyCode = $DefaultCurrencyData[0]['vName'];
        $currencyratio = $DefaultCurrencyData[0]['Ratio'];
        $price_new = round($fNetTotal * $currencyratio, 2);
        $tDescription = "Amount charge for order no: " . $vOrderNo;
        if ($fNetTotal > 0 && $payStatus == "") {
            $UserAppPaymentMethodDetails = getCountryWiseAppPaymentMethod($userData[0]['vCountry']);
            if ($UserAppPaymentMethodDetails['IS_TOKENIZED'] == "No") {
                $REFERRAL_AMOUNT_ARR = getPriceUserCurrency($iUserId, "Passenger", $fNetTotal);
                $REFERRAL_AMOUNT_USER = $REFERRAL_AMOUNT_ARR['fPricewithsymbol'];
                $eSystem = isset($_REQUEST["eSystem"]) ? $_REQUEST["eSystem"] : '';
                $themeColor = isset($_REQUEST["AppThemeColor"]) ? $_REQUEST["AppThemeColor"] : '000000';
                $textColor = isset($_REQUEST["AppThemeTxtColor"]) ? $_REQUEST["AppThemeTxtColor"] : 'FFFFFF';
                $GeneralAppVersion = $appVersion;
                $returnUrl = isset($_REQUEST['returnUrl']) ? trim($_REQUEST['returnUrl']) : 'webservice_shark.php';
                $extraPara = http_build_query($_REQUEST);
                $getWayUrl = $tconfig['tsite_url'] . "assets/libraries/webview/system_payment.php?iUserId=" . $iUserId . "&iOrderId=" . $iOrderId . "&amount=" . $price_new . "&ccode=" . $currencyCode . "&userAmount=" . $REFERRAL_AMOUNT_USER . "&vOrderNo=" . $vOrderNo . $extraPara . "&PAGE_TYPE=CHARGE_CARD_GENIE&vPayMethod=Instant";
                $returnArr = array();
                $returnArr['Action'] = "1";
                $returnArr['WebviewPayment'] = "Yes";
                $returnArr['message'] = $getWayUrl;
                setDataResponse($returnArr);
            }
            else {
                $tDescription = "Amount charge for order no: " . $vOrderNo;
                $paymentData = array(
                    "amount" => $fNetTotal, "description" => $tDescription, "iMemberId" => $iUserId, "UserType" => "Passenger", "iPaymentInfoId" => $iPaymentInfoId
                );
                $result = (PaymentGateways::getInstance())->execute($paymentData);
                if ($result['Action'] == "1") {
                    $sql = "SELECT * FROM user_payment_info WHERE iPaymentInfoId = '" . $iPaymentInfoId . "'";
                    $sqlData = $obj->MySQLSelect($sql);
                    $payment_arr['CARD_TOKEN'] = $sqlData[0]['tCardToken'];
                    $payment_id = $result['payment_id'];
                    $AMOUNT = $fNetTotal;
                    $UserType = "Passenger";
                    $SYSTEM_TYPE = "APP";
                    $PAGE_TYPE = $page_type = "CHARGE_CARD_GENIE";
                    // Update details in db
                    include $tconfig['tpanel_path'] . 'assets/libraries/webview/capture-payment-details.php';
                    exit;
                }
                else {
                    $returnArr['Action'] = "0";
                    $returnArr['message'] = $result['message'];
                    setDataResponse($returnArr);
                }
            }
        }
        if (isset($result['status']) && $result['status'] == "succeeded" && $result['paid'] == "1" || $status == "success" || $payStatus == "succeeded" || $fNetTotal == 0) {
            $where = " iOrderId = '$iOrderId'";
            $iTransactionId = 0;
            if (isset($result) && $result != "") {
                $iTransactionId = $result['id'];
                if ($fNetTotal == 0) {
                    $iTransactionId = 0;
                }
            }
            if ($ePaymentOption == "Card") {
                $fNetTotal = $fNetTotal + $orderData[0]['fDeliveryCharge'] + $orderData[0]['fOutStandingAmount'];
            }
            $data['iTransactionId'] = $iTransactionId;
            $data['ePaid'] = "Yes";
            $data['fNetTotal'] = $fNetTotal;
            if ($CheckUserWallet == "Yes") {
                $data['eCheckUserWallet'] = $CheckUserWallet;
            }
            $data['fWalletDebit'] = $orderData[0]['fWalletDebit'] + $user_wallet_debit_amount;
            $id = $obj->MySQLQueryPerform("orders", $data, 'update', $where);
            // createOrderLog($iOrderId, "5");
            $returnArr["Action"] = "1";
            ## Insert Into Payment Table ##
            if ($tPaymentId == "") {
                $allCurrentSystemPaymentDetails = $CONFIG_OBJ->getCurrentSystemPaymentDetails();
                $payment_arr = array();
                foreach ($allCurrentSystemPaymentDetails as $zkey => $zValue) {
                    if (startsWith(strtoupper($zkey), strtoupper($APP_PAYMENT_METHOD))) {
                        $payment_arr[$zkey] = $zValue;
                    }
                }
                $payment_arr['SYSTEM_PAYMENT_ENVIRONMENT'] = $SYSTEM_PAYMENT_ENVIRONMENT;
                $payment_arr['APP_PAYMENT_MODE'] = $APP_PAYMENT_MODE;
                $payment_arr['APP_PAYMENT_METHOD'] = $APP_PAYMENT_METHOD;
                $payment_arr['COMMISION_DEDUCT_ENABLE'] = $COMMISION_DEDUCT_ENABLE;
                $payment_arr['WALLET_MIN_BALANCE'] = $WALLET_MIN_BALANCE;
                $payment_arr['PAYMENT_ENABLED'] = $PAYMENT_ENABLED;
                $payment_arr['CARD_TOKEN'] = $cardToken;
                $tPaymentDetails = json_encode($payment_arr, JSON_UNESCAPED_UNICODE);
                /* Added by HV on 21-02-2020 Common for all Payment Methods End */
                // $pay_data['tPaymentUserID'] = $iTransactionId;
                $pay_data['tPaymentUserID'] = $_REQUEST['tPaymentUserID'];
                $pay_data['vPaymentUserStatus'] = "approved";
                $pay_data['iAmountUser'] = ($fNetTotal > 0) ? $fNetTotal : $user_wallet_debit_amount;
                $pay_data['tPaymentDetails'] = $tPaymentDetails;
                $pay_data['iOrderId'] = $iOrderId;
                $pay_data['vPaymentMethod'] = $APP_PAYMENT_METHOD;
                $pay_data['vPaymentMode'] = $SYSTEM_PAYMENT_ENVIRONMENT;
                $pay_data['iUserId'] = $iUserId;
                $pay_data['eUserType'] = "Passenger";
                $pay_data['eEvent'] = "OrderPayment";
                $id = $obj->MySQLQueryPerform("payments", $pay_data, 'insert');
                $payment_id = $id;
            }
            else {
                $where = " iPaymentId = '$tPaymentId'";
                $pay_data['eEvent'] = "OrderPayment";
                $pay_data['iOrderId'] = $iOrderId;
                $id = $obj->MySQLQueryPerform("payments", $pay_data, 'update', $where);
                $payment_id = $tPaymentId;
            }
            ## Insert Into Payment Table ##
            // Update User Wallet
            if ($user_wallet_debit_amount > 0 && $CheckUserWallet == "Yes") {
                $vRideNo = $orderData[0]['vOrderNo'];
                $data_wallet['iUserId'] = $iUserId;
                $data_wallet['eUserType'] = "Rider";
                $data_wallet['iBalance'] = $user_wallet_debit_amount;
                $data_wallet['eType'] = "Debit";
                $data_wallet['dDate'] = date("Y-m-d H:i:s");
                $data_wallet['iTripId'] = 0;
                $data_wallet['iOrderId'] = $iOrderId;
                $data_wallet['eFor'] = "Charges";
                $data_wallet['ePaymentStatus'] = "Unsettelled";
                $data_wallet['tDescription'] = "#LBL_DEBITED_BOOKING_DL#" . " " . $vRideNo;
                $WalletId = $WALLET_OBJ->PerformWalletTransaction($data_wallet['iUserId'], $data_wallet['eUserType'], $data_wallet['iBalance'], $data_wallet['eType'], $data_wallet['iTripId'], $data_wallet['eFor'], $data_wallet['tDescription'], $data_wallet['ePaymentStatus'], $data_wallet['dDate'], $iOrderId);
                // $obj->MySQLQueryPerform("user_wallet",$data_wallet,'insert');
                $Data_Update_Payment["iUserWalletId"] = $WalletId;
                $where_payment = " iPaymentId = '" . $payment_id . "'";
                $Data_wallet_payment_update_id = $obj->MySQLQueryPerform("payments", $Data_Update_Payment, 'update', $where_payment);
            }
            // Update User Wallet
            $updateQury = "UPDATE trip_outstanding_amount set ePaidByPassenger = 'Yes',vOrderAdjusmentId = '" . $vOrderNo . "',ePaidByWallet='Yes' WHERE iUserId = '" . $iUserId . "' AND ePaidByPassenger = 'No'";
            $obj->sql_query($updateQury);
        }
        else {
            /*$where = " iOrderId = '$iOrderId'";
            $data['iStatusCode'] = 11;
            $id = $obj->MySQLQueryPerform("orders", $data, 'update', $where);
            $OrderLogId = createOrderLog($iOrderId, "11");*/
            $returnArr['Action'] = "0";
            $returnArr['message'] = "LBL_CHARGE_COLLECT_FAILED";
            setDataResponse($returnArr);
        }
        $data['ePaymentOption'] = "Card";
        //}
    }
    else if ($ePaymentOption == "Cash" && $fNetTotal > 0) {
        $data['ePaymentOption'] = "Cash";
        $data['ePaid'] = "No";
    }
    elseif ($ePaymentOption == "Wallet") {
        $vRideNo = $orderData[0]['vOrderNo'];
        $data_wallet['iUserId'] = $iUserId;
        $data_wallet['eUserType'] = "Rider";
        $data_wallet['iBalance'] = $user_wallet_debit_amount;
        $data_wallet['eType'] = "Debit";
        $data_wallet['dDate'] = date("Y-m-d H:i:s");
        $data_wallet['iTripId'] = 0;
        $data_wallet['iOrderId'] = $iOrderId;
        $data_wallet['eFor'] = "Charges";
        $data_wallet['ePaymentStatus'] = "Unsettelled";
        $data_wallet['tDescription'] = "#LBL_DEBITED_BOOKING_DL#" . " " . $vRideNo;
        $WALLET_OBJ->PerformWalletTransaction($data_wallet['iUserId'], $data_wallet['eUserType'], $data_wallet['iBalance'], $data_wallet['eType'], $data_wallet['iTripId'], $data_wallet['eFor'], $data_wallet['tDescription'], $data_wallet['ePaymentStatus'], $data_wallet['dDate'], $iOrderId);
        $data['fWalletDebit'] = $orderData[0]['fWalletDebit'] + $user_wallet_debit_amount;
        if ($full_adjustment == 1) {
            $data['ePaymentOption'] = "Wallet";
            $data['ePaid'] = "Yes";
            $data['fNetTotal'] = $fNetTotal;
            $data['fTotalGenerateFare'] = $data['fWalletDebit'] + $fNetTotal;
        }
        else {
            $data['ePaymentOption'] = "Cash";
            $data['ePaid'] = "No";
            $data['iStatusCode'] = '13';
            $deliveryOutstandingAmt = $orderData[0]['fDeliveryCharge'] + $orderData[0]['fOutStandingAmount'];
            if ($data['fWalletDebit'] >= $deliveryOutstandingAmt) {
                $fNetTotal = $fNetTotal - $deliveryOutstandingAmt;
            }
            $data['fNetTotal'] = $fNetTotal;
            $data['fTotalGenerateFare'] = $data['fWalletDebit'] + $fNetTotal;
            $obj->sql_query("DELETE FROM order_status_logs WHERE iOrderId = '$iOrderId' AND iStatusCode = '14'");
        }
    }
    $where = " iOrderId = '$iOrderId'";
    $obj->MySQLQueryPerform("orders", $data, 'update', $where);
    $iTripId = get_value('trips', 'iTripId', 'iOrderId', $iOrderId, '', 'true');
    $sql = "SELECT iDriverVehicleId,vCurrencyDriver,iAppVersion,CONCAT(vName,' ',vLastName) AS driverName,vLang FROM `register_driver` WHERE iDriverId = '$iDriverId'";
    $Data_vehicle = $obj->MySQLSelect($sql);
    $drivername = $Data_vehicle[0]['driverName'];
    $vLang = $Data_vehicle[0]['vLang'];
    if ($vLang == "" || $vLang == NULL) {
        $vLang = $LANG_OBJ->FetchDefaultLangData("vCode");
    }
    $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLang, "1", $iServiceId);
    $DriverMessage = "OrderPaymentByUser";
    $tripdriverarrivlbl = $languageLabelsArr['LBL_ORDER_PAYMENT_USER_NOTIFICATION_TXT'];
    $alertMsg = $tripdriverarrivlbl;
    $message_arr = array();
    $message_arr['iDriverId'] = $iDriverId;
    $message_arr['Message'] = $DriverMessage;
    $message_arr['iTripId'] = strval($iTripId);
    $message_arr['DriverAppVersion'] = strval($Data_vehicle[0]['iAppVersion']);
    $message_arr['driverName'] = $drivername;
    // $message_arr['vRideNo'] = $TripRideNO;
    $message_arr['iOrderId'] = $iOrderId;
    $message_arr['vTitle'] = $alertMsg;
    $message_arr['eSystem'] = "DeliverAll";
    $message = json_encode($message_arr);
    if ($iTripId > 0) {
        $sql = "SELECT iGcmRegId,eDeviceType,eAppTerminate,eDebugMode,eHmsDevice FROM register_driver WHERE iDriverId='$iDriverId'";
        $result = $obj->MySQLSelect($sql);
        $channelName = "DRIVER_" . $iDriverId;
        $message_arr['tSessionId'] = $result[0]['tSessionId'];
        $generalDataArr[] = array(
            'eDeviceType' => $result[0]['eDeviceType'], 'deviceToken' => $result[0]['iGcmRegId'], 'alertMsg' => $alertMsg, 'eAppTerminate' => $result[0]['eAppTerminate'], 'eDebugMode' => $result[0]['eDebugMode'], 'eHmsDevice' => $result[0]['eHmsDevice'], 'message' => $message_arr, 'channelName' => $channelName
        );
        $EVENT_MSG_OBJ->send(array('GENERAL_DATA' => $generalDataArr), RN_PROVIDER);
    }
    if ($vPayMethod == "Instant") {
        if ($payStatus == "succeeded") {
            $payment_data = $obj->MySQLSelect("SELECT tPaymentUserID FROM payments WHERE iPaymentId = $payment_id");
            $transaction_id = $payment_data[0]['tPaymentUserID'];
            $success_msg = "LBL_MANUAL_STORE_THANK_YOU_ORDER_PLACE_ORDER";
            $successUrl = $tconfig['tsite_url'] . "assets/libraries/webview/thanks.php?orderid=" . $iOrderId . "&message=" . $success_msg . "&SYSTEM_TYPE=" . $SYSTEM_TYPE . "&TIME=" . time() . "&iTransactionId=" . $transaction_id;
            header('Location: ' . $successUrl);
        }
        else if ($payStatus == "Failed") {
            $failedLabelValue = "LBL_CHARGE_COLLECT_FAILED";
            $failedUrl = $tconfig['tsite_url'] . "assets/libraries/webview/failure.php?success=0&message=" . $failedLabelValue . "&SYSTEM_TYPE=" . $SYSTEM_TYPE;
            header('Location: ' . $failedUrl);
        }
    }
    $returnArr['Action'] = "1";
    $returnArr['iOrderId'] = $iOrderId;
    $returnArr['message1'] = 'LBL_PAYMENT_SUCCESS_TXT';
    $returnArr['message'] = getPassengerDetailInfo($iUserId, "", "");
    setDataResponse($returnArr);
}
// ############################## Add by HV on 01-09-2020 for Genie Collect Payment End #############################################
// ############################## Add by HV on 01-09-2020 for Genie Get Store Bill  Details End #############################################
if ($type == "GetBuyAnyServiceBillDetails") {
    $iOrderId = isset($_REQUEST['iOrderId']) ? $_REQUEST['iOrderId'] : "";
    $orderData = $obj->MySQLSelect("SELECT * FROM orders WHERE iOrderId = $iOrderId");
    $UserDetailsArr = getUserCurrencyLanguageDetails($orderData[0]['iUserId']);
    if (count($UserDetailsArr) > 0) {
        $Ratio = $UserDetailsArr['Ratio'];
        $currencySymbol = $UserDetailsArr['currencySymbol'];
        $vLang = $UserDetailsArr['vLang'];
        $currencycode = $UserDetailsArr['currencycode'];
    }
    if ($vLang == "" || $vLang == NULL) {
        $vLang = $LANG_OBJ->FetchDefaultLangData("vCode");
    }
    $fTax = $orderData[0]['fTax'];
    $storeTotalAmount = $orderData[0]['fNetTotal'];
    $fDeliveryCharge = $orderData[0]['fDeliveryCharge'];
    $storeItemsAmount = $orderData[0]['fNetTotal'] - ($orderData[0]['fDeliveryCharge'] + $orderData[0]['fOutStandingAmount']);
    $fOutStandingAmount = $orderData[0]['fOutStandingAmount'];
    $fWalletDebit = $orderData[0]['fWalletDebit'];
    $fTax = setTwoDecimalPoint($fTax * $Ratio);
    $storeItemsAmount = ($storeItemsAmount <= 0) ? 0 : $storeItemsAmount;
    $storeItemsAmount = setTwoDecimalPoint($storeItemsAmount * $Ratio);
    $fDeliveryCharge = setTwoDecimalPoint($fDeliveryCharge * $Ratio);
    $storeTotalAmount = setTwoDecimalPoint($storeTotalAmount * $Ratio);
    $fOutStandingAmount = setTwoDecimalPoint($fOutStandingAmount * $Ratio);
    $fWalletDebit = setTwoDecimalPoint($fWalletDebit * $Ratio);
    $alreadyPaidAmount = $fDeliveryCharge + $fOutStandingAmount;
    $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLang, "1", "1");
    $returnArr['Action'] = "1";
    $returnArr['SubTotal'] = $storeItemsAmount;
    $returnArr['fDeliveryCharge'] = $fDeliveryCharge;
    $returnArr['TotalAmount'] = $storeTotalAmount;
    $OrderFareDetailsArr = array();
    $itemDetails = $obj->MySQLSelect("SELECT oba.*,od.iQty,mi.vItemType_$vLang,mi.vItemTypeBuyAnyService FROM order_items_buy_anything as oba LEFT JOIN order_details as od ON od.iOrderDetailId = oba.iOrderDetailId LEFT JOIN menu_items as mi ON mi.iMenuItemId = od.iMenuItemId WHERE oba.iOrderId = $iOrderId");
    $itemDetailsArr = array();
    $orderTotal = $orderData[0]['fDeliveryCharge'];
    foreach ($itemDetails as $sItem) {
        $itemPrice = setTwoDecimalPoint($sItem['fItemPrice'] * $Ratio);
        $itemPrice = formateNumAsPerCurrency($itemPrice, $currencycode);
        if ($sItem['eExtraPayment'] == "No" && $sItem['eItemAvailable'] == "Yes") {
            $OrderFareDetailsArr[][$sItem['vItemTypeBuyAnyService'] . " (X " . $sItem['iQty'] . ")\n" . $languageLabelsArr['LBL_PAYMENT_NOT_REQUIRED']] = $itemPrice;
        }
        elseif ($sItem['eItemAvailable'] == "No") {
            if ($sItem['eExtraPayment'] == "No") {
                $OrderFareDetailsArr[][$sItem['vItemTypeBuyAnyService'] . " (X " . $sItem['iQty'] . ")\n" . $languageLabelsArr['LBL_ITEM_NO_PAYMENT_UNAVAILABLE']] = $itemPrice;
            }
            else {
                $OrderFareDetailsArr[][$sItem['vItemTypeBuyAnyService'] . " (X " . $sItem['iQty'] . ")\n" . $languageLabelsArr['LBL_ITEM_NOT_AVAILABLE']] = $itemPrice;
            }
        }
        else {
            $OrderFareDetailsArr[][$sItem['vItemTypeBuyAnyService'] . " (X " . $sItem['iQty'] . ")"] = $itemPrice;
        }
    }
    // $OrderFareDetailsArr[][$languageLabelsArr['LBL_STORE_BILL_TXT']] = formateNumAsPerCurrency($storeItemsAmount,$currencycode);
    $OrderFareDetailsArr[][$languageLabelsArr['LBL_DELIVERY_CHARGES_TXT']] = formateNumAsPerCurrency($fDeliveryCharge, $currencycode);
    if ($orderData[0]['fTax'] > 0) {
        $OrderFareDetailsArr[][$languageLabelsArr['LBL_TOTAL_TAX_TXT']] = formateNumAsPerCurrency($fTax, $currencycode);
    }
    if ($orderData[0]['fOutStandingAmount'] > 0) {
        $OrderFareDetailsArr[][$languageLabelsArr['LBL_OUTSTANDING_AMOUNT_TXT']] = formateNumAsPerCurrency($fOutStandingAmount, $currencycode);
    }
    $OrderFareDetailsArr[]['eDisplaySeperator'] = 'Yes';
    $OrderFareDetailsArr[][$languageLabelsArr['LBL_TOTAL_BILL_AMOUNT_TXT']] = formateNumAsPerCurrency($storeTotalAmount, $currencycode);
    if ($orderData[0]['ePaymentOption'] == "Card") {
        $OrderFareDetailsArr[][$languageLabelsArr['LBL_AMOUNT_ALREADY_PAID_TXT']] = "- " . formateNumAsPerCurrency($alreadyPaidAmount, $currencycode);
    }
    /*if($orderData[0]['fWalletDebit'] > 0 && $orderData[0]['eCheckUserWallet'] == "Yes")
    {
        $OrderFareDetailsArr[][$languageLabelsArr['LBL_WALLET_ADJUSTMENT']] = "- " . formateNumAsPerCurrency($fWalletDebit,$currencycode);
    }*/
    $currData = $obj->MySQLSelect("SELECT * FROM currency WHERE vName = '" . $currencycode . "'");
    if (isset($orderData[0]['fRoundingAmount']) && !empty($orderData[0]['fRoundingAmount']) && $orderData[0]['fRoundingAmount'] != 0 && $currData[0]['eRoundingOffEnable'] == "Yes" && $orderData[0]['ePaymentOption'] == "Cash" && $MODULES_OBJ->isEnableRoundingMethod()) {
        $roundingOffTotal_fare_amountArr['method'] = $orderData[0]['eRoundingType'];
        $roundingOffTotal_fare_amountArr['differenceValue'] = $orderData[0]['fRoundingAmount'];
        $roundingOffTotal_fare_amountArr = getRoundingOffAmounttrip($storeTotalAmount, $orderData[0]['fRoundingAmount'], $orderData[0]['eRoundingType']); ////start
        if ($roundingOffTotal_fare_amountArr['method'] == "Addition") {
            $roundingMethod = "";
        }
        else {
            $roundingMethod = "-";
        }
        $storeTotalAmount = isset($roundingOffTotal_fare_amountArr['finalFareValue']) && $roundingOffTotal_fare_amountArr['finalFareValue'] != '' ? $roundingOffTotal_fare_amountArr['finalFareValue'] : "0.00";
        $rounding_diff = isset($roundingOffTotal_fare_amountArr['differenceValue']) && $roundingOffTotal_fare_amountArr['differenceValue'] != '' ? $roundingOffTotal_fare_amountArr['differenceValue'] : "0.00";
        $OrderFareDetailsArr[][$languageLabelsArr['LBL_ROUNDING_DIFF_TXT']] = $roundingMethod . " " . formateNumAsPerCurrency($rounding_diff, $currencycode);
    }
    $OrderFareDetailsArr[]['eDisplaySeperator'] = 'Yes';
    if ($orderData[0]['ePaymentOption'] == "Card") {
        $OrderFareDetailsArr[][$languageLabelsArr['LBL_REMAINING_AMOUNT_TXT']] = formateNumAsPerCurrency($storeItemsAmount, $currencycode);
    }
    else {
        $OrderFareDetailsArr[][$languageLabelsArr['LBL_REMAINING_AMOUNT_TXT']] = formateNumAsPerCurrency($storeTotalAmount, $currencycode);
    }
    $returnArr['FareDetailsArr'] = $OrderFareDetailsArr;
    setDataResponse($returnArr);
}
// ############################## Add by HV on 01-09-2020 for Genie Get Store Bill  Details End #############################################
// ########################## Added by HV on 29-09-2020 for Upload ID Proof for Age feature ##########################################################
if ($type == "UploadIdProof") {
    $iUserId = isset($_REQUEST['iUserId']) ? $_REQUEST['iUserId'] : "";
    $image_name = $vImage = isset($_FILES['vImage']['name']) ? $_FILES['vImage']['name'] : '';
    $image_object = isset($_FILES['vImage']['tmp_name']) ? $_FILES['vImage']['tmp_name'] : '';
    $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLang, "1", "");
    if (!empty($image_name)) {
        $img_path = $tconfig["tsite_upload_id_proof_service_categories_images_path"];
        $temp_gallery = $img_path . '/';
        $filecheck = basename($image_name);
        $fileextarr = explode(".", $filecheck);
        $ext = strtolower($fileextarr[count($fileextarr) - 1]);
        if ($ext != "jpg" && $ext != "gif" && $ext != "png" && $ext != "jpeg" && $ext != "bmp" && $ext != "heic") {
            $var_msg = $languageLabelsArr['LBL_FILE_EXT_VALID_ERROR_MSG'] . " .jpg, .jpeg, .gif, .png, .bmp, .heic";
            $returnArr['Action'] = "0";
            $returnArr['message'] = $var_msg;
            setDataResponse($returnArr);
        }
        $Photo_Gallery_folder = $img_path;
        $Photo_Gallery_folder_temp = $img_path . '/temp/';
        if (!is_dir($Photo_Gallery_folder)) {
            mkdir($Photo_Gallery_folder, 0777);
            chmod($Photo_Gallery_folder, 0777);
        }
        if (!is_dir($Photo_Gallery_folder_temp)) {
            mkdir($Photo_Gallery_folder_temp, 0777);
            chmod($Photo_Gallery_folder_temp, 0777);
        }
        $img1 = $UPLOAD_OBJ->GeneralFileUpload($Photo_Gallery_folder_temp, $image_object, $image_name, '', 'jpg,png,gif,jpeg,bmp,heic');
        $vImgName = $img1[0];
        if (!empty($vImgName)) {
            $Data_insert['iUserId'] = $iUserId;
            $Data_insert['vImage'] = $vImgName;
            $Data_insert['tAddedDate'] = date('Y-m-d H:i:s');
            $image_id = $obj->MySQLQueryPerform("idproof_images", $Data_insert, 'insert');
            $returnArr['Action'] = "1";
            $returnArr['iIdProofImageId'] = $image_id;
        }
        else {
            $returnArr['Action'] = "0";
            $returnArr['message'] = "LBL_UPLOAD_ID_PROOF_IMG_ERROR";
        }
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_UPLOAD_ID_PROOF_IMG_ERROR";
    }
    setDataResponse($returnArr);
}
// ########################## Added by HV on 29-09-2020 for Upload ID Proof for Age feature End ################################################
// ########################## Added by HV on 26-11-2020 for Voice Direction File upload feature ##########################################################
if ($type == "UploadVoiceDirectionFile") {
    $iUserId = isset($_REQUEST['iUserId']) ? $_REQUEST['iUserId'] : "";
    $voiceDirectionFileName = isset($_FILES['voiceDirectionFile']['name']) ? $_FILES['voiceDirectionFile']['name'] : '';
    $voiceDirectionFileObject = isset($_FILES['voiceDirectionFile']['tmp_name']) ? $_FILES['voiceDirectionFile']['tmp_name'] : '';
    $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLang, "1", "");
    if (!empty($voiceDirectionFileName)) {
        $file_path = $tconfig["tsite_upload_voice_direction_file_path"];
        $temp_gallery = $file_path . '/';
        $filecheck = basename($voiceDirectionFileName);
        $fileextarr = explode(".", $filecheck);
        $ext = strtolower($fileextarr[count($fileextarr) - 1]);
        if ($ext != "wav") {
            $var_msg = $languageLabelsArr['LBL_FILE_EXT_VALID_ERROR_MSG'] . " .wav";
            $returnArr['Action'] = "0";
            $returnArr['message'] = $var_msg;
            setDataResponse($returnArr);
        }
        $Photo_Gallery_folder = $file_path;
        $Photo_Gallery_folder_temp = $file_path . '/temp/';
        if (!is_dir($Photo_Gallery_folder)) {
            mkdir($Photo_Gallery_folder, 0777);
            chmod($Photo_Gallery_folder, 0777);
        }
        if (!is_dir($Photo_Gallery_folder_temp)) {
            mkdir($Photo_Gallery_folder_temp, 0777);
            chmod($Photo_Gallery_folder_temp, 0777);
        }
        $img1 = $UPLOAD_OBJ->GeneralFileUpload($Photo_Gallery_folder_temp, $voiceDirectionFileObject, $voiceDirectionFileName, '', 'wav');
        $vFileName = $img1[0];
        if (!empty($vFileName)) {
            $Data_insert['iUserId'] = $iUserId;
            $Data_insert['vFile'] = $vFileName;
            $Data_insert['tAddedDate'] = date('Y-m-d H:i:s');
            $file_id = $obj->MySQLQueryPerform("voice_direction_files", $Data_insert, 'insert');
            $returnArr['Action'] = "1";
            $returnArr['iVoiceDirectionFileId'] = $file_id;
        }
        else {
            $returnArr['Action'] = "0";
            $returnArr['message'] = "LBL_ERROR_OCCURED";
        }
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_ERROR_OCCURED";
    }
    setDataResponse($returnArr);
}
// ########################## Added by HV on 26-11-2020 for Voice Direction File upload End ################################################
// ########################## Added by HV on 25-12-2020 for Store Wise Banner ################################################
if ($type == "configStoreBannerImages") {
    $action_type = isset($_REQUEST["action_type"]) ? $_REQUEST["action_type"] : 'ADD';
    $image_name = $vImage = isset($_FILES['vImage']['name']) ? $_FILES['vImage']['name'] : '';
    $image_object = isset($_FILES['vImage']['tmp_name']) ? $_FILES['vImage']['tmp_name'] : '';
    $iImageId = isset($_REQUEST["iImageId"]) ? $_REQUEST["iImageId"] : '';
    $iCompanyId = isset($_REQUEST["iCompanyId"]) ? $_REQUEST["iCompanyId"] : '';
    $company_data = $obj->MySQLSelect("SELECT iServiceId, vLang, eStatus, eSafetyPractices FROM company WHERE iCompanyId = $iCompanyId");
    $iServiceId = $company_data[0]['iServiceId'];
    $vLang = $company_data[0]['vLang'];
    $eStatus = $company_data[0]['eStatus'];
    $eSafetyPractices = $company_data[0]['eSafetyPractices'];
    if ($eStatus == "Inactive" && $action_type == "ADD") {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_IMAGE_UPLOAD_ERROR_STATUS_INACTIVE";
        setDataResponse($returnArr);
    }
    if ((!$MODULES_OBJ->isEnableStoreSafetyProcedure() || $eSafetyPractices != 'Yes') && $action_type == "ADD") {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_IMAGE_UPLOAD_ERROR_NO_SAFETY";
        setDataResponse($returnArr);
    }
    $tbl_name = 'store_wise_banners';
    $sql = "SELECT vCode FROM `language_master` ORDER BY `iDispOrder`";
    $db_master = $obj->MySQLSelect($sql);
    $count_all = count($db_master);
    $select_order = $obj->MySQLSelect("SELECT MAX(iDisplayOrder) AS iDisplayOrder, MAX(iUniqueId) AS iUniqueId FROM " . $tbl_name . " WHERE  iCompanyId = " . $iCompanyId);
    $iDisplayOrder = isset($select_order[0]['iDisplayOrder']) ? $select_order[0]['iDisplayOrder'] : 0;
    $iDisplayOrder = $iDisplayOrder + 1;
    $iUniqueId = isset($select_order[0]['iUniqueId']) ? $select_order[0]['iUniqueId'] : 0;
    $iUniqueId = $iUniqueId + 1;
    if ($action_type == "ADD") {
        $q = "INSERT INTO ";
        $where = '';
        /*if($id != '' ){
            $q = "UPDATE ";
            $where = " WHERE `iUniqueId` = '".$id."' AND vCode = '".$db_master[$i]['vCode']."'";
            $iUniqueId = $id;
        }*/
        if ($image_name != "") {
            $filecheck = basename($_FILES['vImage']['name']);
            $fileextarr = explode(".", $filecheck);
            $ext = strtolower($fileextarr[count($fileextarr) - 1]);
            $flag_error = 0;
            if ($ext != "jpg" && $ext != "gif" && $ext != "png" && $ext != "jpeg" && $ext != "bmp") {
                $var_msg = $languageLabelsArr['LBL_FILE_EXT_VALID_ERROR_MSG'] . " .jpg, .jpeg, .gif, .png, .bmp";
                $returnArr['Action'] = "0";
                $returnArr['message'] = $var_msg;
                setDataResponse($returnArr);
            }
            $Photo_Gallery_folder = $tconfig["tsite_upload_images_panel"] . '/';
            if (!is_dir($Photo_Gallery_folder)) {
                mkdir($Photo_Gallery_folder, 0777);
                chmod($Photo_Gallery_folder, 0777);
            }
            $img = $UPLOAD_OBJ->GeneralFileUpload($Photo_Gallery_folder, $image_object, $image_name, '', 'jpg,png,gif,jpeg,bmp');
            $vImage = $img[0];
        }
        $query = $q . " `" . $tbl_name . "` SET  
        `vImage` = '" . $vImage . "',
        `eStatus` = 'Active',
        `iUniqueId` = '" . $iUniqueId . "',
        `iDisplayOrder` = '" . $iDisplayOrder . "',
        `iServiceId`= '" . $iServiceId . "',
        `iCompanyId`= '" . $iCompanyId . "',
        `vCode` = '" . $db_master[$i]['vCode'] . "'" . $where;
        $id = $obj->sql_query($query);
        if ($id > 0) {
            $returnArr['Action'] = "1";
            $returnArr['message'] = "LBL_IMAGE_UPLOAD_SUCCESS_NOTE";
        }
        else {
            $returnArr['Action'] = "0";
            $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
        }
    }
    else if ($action_type == "DELETE" && $iImageId != "") {
        $Photo_Gallery_folder = $tconfig['tsite_upload_images_panel'];
        $OldImageName = $obj->MySQLSelect("SELECT vImage FROM " . $tbl_name . " WHERE iUniqueId = '" . $iImageId . "' AND iCompanyId = " . $iCompanyId);
        $OldImageName = $OldImageName[0]['vImage'];
        if ($OldImageName != '') {
            unlink($Photo_Gallery_folder . $OldImageName);
        }
        $data_logo = $obj->MySQLSelect("SELECT iDisplayOrder FROM " . $tbl_name . " WHERE iUniqueId = '" . $iImageId . "' AND iCompanyId = " . $iCompanyId);
        if (count($data_logo) > 0) {
            $iDisplayOrder_db = isset($data_logo[0]['iDisplayOrder']) ? $data_logo[0]['iDisplayOrder'] : '';
            $id = $obj->sql_query("DELETE FROM `" . $tbl_name . "` WHERE iUniqueId = '" . $iImageId . "' AND iCompanyId = " . $iCompanyId);
            if ($iDisplayOrder_db < $iDisplayOrder) for ($i = $iDisplayOrder_db + 1; $i <= $iDisplayOrder; $i++) $obj->sql_query("UPDATE " . $tbl_name . " SET iDisplayOrder = " . ($i - 1) . " WHERE iDisplayOrder = " . $i . " AND iCompanyId = " . $iCompanyId);
        }
        if ($id > 0) {
            $returnArr['Action'] = "1";
            $returnArr['message'] = "LBL_IMAGE_DELETE_SUCCESS_NOTE";
        }
        else {
            $returnArr['Action'] = "0";
            $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
        }
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
    }
    setDataResponse($returnArr);
}
if ($type == "getStoreBannerImages") {
    $iCompanyId = isset($_REQUEST["iCompanyId"]) ? $_REQUEST['iCompanyId'] : '';
    if (!empty($iCompanyId)) {
        $vLang = get_value('company', 'vLang', 'iCompanyId', $iCompanyId, '', 'true');
        $getImagesArr = array();
        $getImages = $obj->MySQLSelect("SELECT * FROM store_wise_banners WHERE iCompanyId='" . $iCompanyId . "' GROUP BY iUniqueId ORDER BY iUniqueId DESC ");
        for ($p = 0; $p < count($getImages); $p++) {
            $getImagesArr[] = array(
                'vImage' => $tconfig['tsite_upload_images'] . '/' . $getImages[$p]['vImage'], 'iImageId' => $getImages[$p]['iUniqueId']
            );
        }
        $returnArr['Action'] = "1";
        $returnArr['message'] = $getImagesArr;
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
    }
    setDataResponse($returnArr);
}
// ########################## Added by HV on 25-12-2020 for Store Wise Banner End ################################################
// ########################## Added by HV on 26-12-2020 for Cancel Driver Order ################################################
if ($type == "cancelDriverOrder") {
    $iMemberId = isset($_REQUEST["GeneralMemberId"]) ? $_REQUEST["GeneralMemberId"] : '';
    $iOrderId = isset($_REQUEST["iOrderId"]) ? $_REQUEST["iOrderId"] : '';
    $iTripId = isset($_REQUEST["iTripId"]) ? $_REQUEST["iTripId"] : '';
    $vCancelReason = isset($_REQUEST["vCancelReason"]) ? $_REQUEST["vCancelReason"] : '';
    $isFromAdmin = isset($_REQUEST["isFromAdmin"]) ? $_REQUEST["isFromAdmin"] : '';
    /* ------------------------------ order_request ----------------------------- */
    if (isset($iOrderId) && !empty($iOrderId)) {
        $orderRequest = $obj->MySQLSelect("SELECT COUNT(orderRequestId) as count FROM `order_request` WHERE 1=1 AND iOrderId = '" . $iOrderId . "' AND eStatus = 'Accept'");
        if ($orderRequest[0]['count'] > 0) {
            $where = " iOrderId = '$iOrderId'";
            $order_request['eStatus'] = 'Pending';
            $obj->MySQLQueryPerform("order_request", $order_request, 'update', $where);
        }
    }
    /* ------------------------------ order_request ----------------------------- */
    //for reset driver it is used from admin side and store panel
    if ($isFromAdmin == "Yes") {
        $orderData = $obj->MySQLSelect("SELECT iDriverId FROM orders WHERE iOrderId =" . $iOrderId);
        $iMemberId = $orderData[0]['iDriverId'];
        $vLang = $LANG_OBJ->FetchDefaultLangData("vCode");
        $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLang, "1", "");
        $vCancelReason = $languageLabelsArr['LBL_CANCELLED_BY_ADMIN'];
        $tripData = $obj->MySQLSelect("SELECT iTripId FROM trips WHERE iOrderId =" . $iOrderId);
        $iTripId = $tripData[0]['iTripId'];
    }
    $DRIVER_NAME = get_value('register_driver', 'CONCAT(vName," ",vLastName) AS FNAME', 'iDriverId', $iMemberId);
    $ORDER_NO = get_value('orders', 'vOrderNo,tOrderRequestDate,eBuyAnyService,iUserId, iCompanyId', 'iOrderId', $iOrderId);
    if (!empty($iOrderId) && $isFromAdmin != "Yes" && !empty($ORDER_NO[0]['iUserId'])) {
        if($ORDER_NO[0]['eBuyAnyService'] == 'Yes') {
            $vItemImageData = $obj->MySQLSelect("SELECT vItemImage FROM order_items_buy_anything WHERE iOrderId = '" . $iOrderId . "'");
            foreach ($vItemImageData as $key => $value) {
                unlink($tconfig['tsite_upload_order_buy_anything_path'] . $value['vItemImage']);
            }
            $sql = $obj->sql_query("DELETE FROM order_status_logs WHERE `iOrderId`='" . $iOrderId . "' AND `iStatusCode` != '1'");
            $updateOrder['eConfirm'] = "No";
            $updateOrder['eDecline'] = "No";
            $updateOrder['eItemAvailable'] = "Yes";
            $updateOrder['vItemImage'] = "";
            $where = " iOrderId = '" . $iOrderId . "'";
            $obj->MySQLQueryPerform("order_items_buy_anything", $updateOrder, 'update', $where);
            $dataUser = $obj->MySQLSelect("SELECT iGcmRegId,eDeviceType,eAppTerminate,tSessionId,vLang,iAppVersion,vName,vLastName,eDebugMode,eHmsDevice FROM register_user WHERE iUserId='" . $ORDER_NO[0]['iUserId'] . "'");
            $vLang = $dataUser[0]['vLang'];
            if ($vLang == "" || $vLang == NULL) {
                $vLang = $LANG_OBJ->FetchDefaultLangData("vCode");
            }
            $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLang, "1", $iServiceId);
            $DriverMessage = "GenieOrderCancelByDriver";
            $alertMsg = $languageLabelsArr['LBL_CANCEL_NOTIFICATION_USER_GENIE'];
            $message_arr = array();
            $message_arr['iDriverId'] = $iMemberId;
            $message_arr['Message'] = $DriverMessage;
            $message_arr['iTripId'] = strval($iTripId);
            $message_arr['DriverAppVersion'] = strval($dataUser[0]['iAppVersion']);
            $message_arr['userName'] = $dataUser[0]['vName'] . " " . $dataUser[0]['vLastName'];
            $message_arr['iOrderId'] = $iOrderId;
            $message_arr['vTitle'] = $alertMsg;
            $message_arr['eSystem'] = "DeliverAll";
            $message = json_encode($message_arr);
            $channelName = "PASSENGER_" . $ORDER_NO[0]['iUserId'];
            $message_arr['tSessionId'] = $dataUser[0]['tSessionId'];
            $message_arr['time'] = intval(microtime(true) * 1000);
            $generalDataArr[] = array(
                'eDeviceType' => $dataUser[0]['eDeviceType'], 'deviceToken' => $dataUser[0]['iGcmRegId'], 'alertMsg' => $alertMsg, 'eAppTerminate' => $dataUser[0]['eAppTerminate'], 'eDebugMode' => $dataUser[0]['eDebugMode'], 'eHmsDevice' => $dataUser[0]['eHmsDevice'], 'message' => $message_arr, 'channelName' => $channelName
            );
            $EVENT_MSG_OBJ->send(array('GENERAL_DATA' => $generalDataArr), RN_USER);
            $data_version['genieUserApproved'] = 'No';
            $data_version['genieWaitingForUserApproval'] = 'No';
            $data_version['iStatusCode'] = '1';
            
        } else {
            $dataCompany = $obj->MySQLSelect("SELECT iGcmRegId,eDeviceType,eAppTerminate,tSessionId,vLang,iAppVersion,eDebugMode,eHmsDevice FROM company WHERE iCompanyId='" . $ORDER_NO[0]['iCompanyId'] . "'");
            $vLang = $dataCompany[0]['vLang'];
            if ($vLang == "" || $vLang == NULL) {
                $vLang = $LANG_OBJ->FetchDefaultLangData("vCode");
            }
            $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLang, "1", $iServiceId);
            $DriverMessage = "OrderCancelByDriver";
            $alertMsg = $languageLabelsArr['LBL_CANCEL_NOTIFICATION_STORE'];
            $message_arr = array();
            $message_arr['iDriverId'] = $iMemberId;
            $message_arr['Message'] = $DriverMessage;
            $message_arr['MsgType'] = $DriverMessage;
            $message_arr['iTripId'] = strval($iTripId);
            $message_arr['iOrderId'] = $iOrderId;
            $message_arr['vTitle'] = $alertMsg;
            $message_arr['eSystem'] = "DeliverAll";
            $message = json_encode($message_arr);
            $channelName = "COMPANY_" . $ORDER_NO[0]['iCompanyId'];
            $message_arr['tSessionId'] = $dataCompany[0]['tSessionId'];
            $message_arr['time'] = intval(microtime(true) * 1000);
            $generalDataArr[] = array(
                'eDeviceType' => $dataCompany[0]['eDeviceType'], 'deviceToken' => $dataCompany[0]['iGcmRegId'], 'alertMsg' => $alertMsg, 'eAppTerminate' => $dataCompany[0]['eAppTerminate'], 'eDebugMode' => $dataCompany[0]['eDebugMode'], 'eHmsDevice' => $dataCompany[0]['eHmsDevice'], 'message' => $message_arr, 'channelName' => $channelName
            );
            $EVENT_MSG_OBJ->send(array('GENERAL_DATA' => $generalDataArr), RN_COMPANY);

            $data_version['iStatusCode'] = '2';
        }
        
    }
    //Updating Order table
    $data_version['eCancelledbyDriver'] = "Yes";
    $data_version['vCancelReasonDriver'] = $vCancelReason;
    $data_version['iDriverId'] = '0';
    $where = " iOrderId = '" . $iOrderId . "'";
    $obj->MySQLQueryPerform("orders", $data_version, 'update', $where);

    $obj->sql_query("DELETE FROM order_status_logs WHERE iOrderId = '$iOrderId' AND iStatusCode = '4' ");
    
    //Generating Log
    $data_log['iOrderId'] = $iOrderId;
    $data_log['iDriverId'] = $iMemberId;
    $data_log['vMessage'] = $vCancelReason;
    $data_log['dDate'] = date('Y-m-d h:i:s');
    $obj->MySQLQueryPerform("order_driver_log", $data_log, 'insert');
    //Updating Driver Table & free driver
    $data_driver['vTripStatus'] = "Cancelled";
    if (strtolower($_REQUEST['GeneralDeviceType']) == "android") {
        $data_driver['vAvailability'] = "Available";
    }
    else {
        $data_driver['vAvailability'] = "";
    }
    $data_driver['iTripId'] = $iTripId;
    $where = " iDriverId = '" . $iMemberId . "'";
    $obj->MySQLQueryPerform("register_driver", $data_driver, 'update', $where);
    $where = " iOrderId = '" . $iOrderId . "'";
    $data_driver_trip['iActive'] = "Canceled";
    $obj->MySQLQueryPerform("trips", $data_driver_trip, 'update', $where);
    if ($MODULES_OBJ->isEnableAcceptMultipleOrders()) {
        $orderIds = $obj->MySQLSelect("SELECT COUNT(iOrderId) as order_ids FROM trips WHERE (iActive = 'Active' OR iActive = 'On Going Trip') AND iDriverId = '$iMemberId' AND eSystem = 'DeliverAll'");
        if (!empty($orderIds[0]['order_ids']) && $orderIds[0]['order_ids'] > 0) {
            $obj->sql_query("UPDATE register_driver SET vTripStatus = 'On Going Trip', vAvailability = 'Not Available' WHERE iDriverId = '$iMemberId'");
        }
    }
    //Sending Email to ADmin
    $maildata['vAdminMail'] = $ADMIN_EMAIL;
    $maildata['vDriver'] = $DRIVER_NAME[0]['FNAME'];
    $maildata['vBookingNo'] = $ORDER_NO[0]['vOrderNo'];
    $maildata['Ddate'] = $ORDER_NO[0]['tOrderRequestDate'];
    $COMM_MEDIA_OBJ->SendMailToMember("DRIVER_CANCEL_ORDER", $maildata);
    //Unlinking Lock File
    $fileName = "Order_" . $iOrderId;
    $fileName .= "_" . $_SERVER['HTTP_HOST'];
    $file_name = md5($fileName) . ".txt";
    $file_path = str_replace("//", "/", $tconfig["tpanel_path"]) . "webimages/lockFile/" . $file_name;
    unlink($file_path);
    //for reset driver it is used from admin side and store panel
    if ($isFromAdmin == "Yes") {
        $sql = "SELECT iGcmRegId,eDeviceType,eAppTerminate,tSessionId,vLang,iAppVersion,vName,vLastName,eDebugMode,eHmsDevice FROM register_driver WHERE iDriverId='$iMemberId'";
        $result = $obj->MySQLSelect($sql);
        $vLang = $result[0]['vLang'];
        if ($vLang == "" || $vLang == NULL) {
            $vLang = $LANG_OBJ->FetchDefaultLangData("vCode");
        }
        $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLang, "1", $iServiceId);
        $DriverMessage = "OrderCancelByAdmin";
        $tripdriverarrivlbl = $languageLabelsArr['LBL_ORDER_CANCEL_TEXT'];
        $alertMsg = $tripdriverarrivlbl;
        $message_arr = array();
        $message_arr['iDriverId'] = $iMemberId;
        $message_arr['Message'] = $DriverMessage;
        $message_arr['iTripId'] = strval($iTripId);
        $message_arr['DriverAppVersion'] = strval($result[0]['iAppVersion']);
        $message_arr['driverName'] = $result[0]['vName'] . " " . $result[0]['vLastName'];
        // $message_arr['vRideNo'] = $TripRideNO;
        $message_arr['iOrderId'] = $iOrderId;
        $message_arr['vTitle'] = $alertMsg;
        $message_arr['eSystem'] = "DeliverAll";
        $message = json_encode($message_arr);
        $channelName = "DRIVER_" . $iMemberId;
        $message_arr['tSessionId'] = $result[0]['tSessionId'];
        $message_arr['time'] = intval(microtime(true) * 1000);
        $generalDataArr[] = array(
            'eDeviceType' => $result[0]['eDeviceType'], 'deviceToken' => $result[0]['iGcmRegId'], 'alertMsg' => $alertMsg, 'eAppTerminate' => $result[0]['eAppTerminate'], 'eDebugMode' => $result[0]['eDebugMode'], 'eHmsDevice' => $result[0]['eHmsDevice'], 'message' => $message_arr, 'channelName' => $channelName
        );
        $EVENT_MSG_OBJ->send(array('GENERAL_DATA' => $generalDataArr), RN_PROVIDER);
    }

    $returnArr['Action'] = 1;
    $returnArr['message'] = "LBL_DRIVER_CANCEL_ORDER";//LBL_REQUEST_CANCEL_FAILED_TXT
    setDataResponse($returnArr);
}
// ########################## Added by HV on 26-12-2020 for Cancel Driver Order End ################################################

if ($type == "getDriverStats") {
    $iDriverId = isset($_REQUEST['GeneralMemberId']) ? $_REQUEST['GeneralMemberId'] : "";
    $dateRange = isset($_REQUEST["dateRange"]) ? $_REQUEST["dateRange"] : 'today';
    $startDate = isset($_REQUEST["startDate"]) ? $_REQUEST["startDate"] : '';
    $endDate = isset($_REQUEST["endDate"]) ? $_REQUEST["endDate"] : '';
    $lang = isset($_REQUEST["vGeneralLang"]) ? $_REQUEST["vGeneralLang"] : '';
    $duration_sql = '';
    $duration_sql_1 = '';
    if (!empty($dateRange)) {
        if (strtolower($dateRange) == "today") {
            $start_date = date('Y-m-d 00:00:00');
            $end_date = date('Y-m-d 23:59:59');
        }
        elseif (strtolower($dateRange) == "week") {
            $start_date = date('Y-m-d', strtotime('sunday this week -1 week'));
            $end_date = date('Y-m-d', strtotime('saturday this week'));
        }
        elseif (strtolower($dateRange) == "month") {
            $start_date = date('Y-m-01 00:00:00');
            $end_date = date('Y-m-t 23:59:59');
        }
        elseif (strtolower($dateRange) == "range") {
            $start_date = date('Y-m-d 00:00:00', strtotime($startDate));
            $end_date = date('Y-m-d 23:59:59', strtotime($endDate));
        }
        $avg_rating = CalculateMemberAvgRating($iDriverId, 'Driver', $start_date, $end_date);
        //$duration_sql = " AND tEndDate BETWEEN '" . $start_date . "' AND '" . $end_date . "' ";
        $duration_sql = " AND o.tOrderRequestDate BETWEEN '" . $start_date . "' AND '" . $end_date . "' ";
        $duration_sql_2 = " AND tOrderRequestDate BETWEEN '" . $start_date . "' AND '" . $end_date . "' ";
        //$duration_sql_2 = " AND tTripRequestDate BETWEEN '" . $start_date . "' AND '" . $end_date . "' ";
        $duration_sql_3 = " AND o.tOrderRequestDate BETWEEN '" . $start_date . "' AND '" . $end_date . "' ";
        //$duration_sql_3 = " AND t.tTripRequestDate BETWEEN '" . $start_date . "' AND '" . $end_date . "' ";
        $start_date = date('Y-m-d H:i:s', strtotime("-30 minutes"));
        $duration_sql_1 = " AND dBooking_date BETWEEN '" . $start_date . "' AND '" . $end_date . "' ";
    }
    if (empty($lang)) {
        $lang = $LANG_OBJ->FetchDefaultLangData("vCode");
    }
    $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($lang, "1", "");
    /* ------------------------------- total trip ------------------------------- */
    //$trips = $obj->MySQLSelect("SELECT iDriverId ,COUNT(case when iActive != '' AND (iActive = 'Finished' || iActive = 'Canceled') then 1 else NULL  end) `minimum_trip`  FROM `trips` WHERE `iDriverId` = " . $iDriverId . " $duration_sql ");
    $trips = $obj->MySQLSelect("SELECT t.iDriverId ,COUNT(case when t.iActive != '' AND (iActive = 'Finished' || iActive = 'Canceled') then 1 else NULL  end) `minimum_trip`  FROM trips as t LEFT JOIN orders as o on o.iOrderId = t.iOrderId WHERE t.iDriverId = " . $iDriverId . " $duration_sql ");
    $returnArr['total_trip'] = array(
        'vtitle' => (strtolower($dateRange) == "today") ? $languageLabelsArr['LBL_TRIP_TODAY'] : $languageLabelsArr['LBL_TRIP'], 'value' => ($trips[0]['minimum_trip'] > 0 && empty($trips[0]['minimum_trip']) == false) ? $trips[0]['minimum_trip'] : '0', 'color' => RANDOM_COLORS_ARR[array_rand(RANDOM_COLORS_ARR, 1)], 'vBgColor' => '#C4EBEA', 'vTextColor' => '#000000'
    );
    /* ------------------------------- total trip ------------------------------- */
    /* ------------------------------- avg rating ------------------------------- */
    $returnArr['avg_rating'] = array(
        'vtitle' => $languageLabelsArr['LBL_AVG_RATING'], 'value' => ($avg_rating > 0) ? $avg_rating : '0', 'color' => RANDOM_COLORS_ARR[array_rand(RANDOM_COLORS_ARR, 1)], 'vBgColor' => '#FCD9D8', 'vTextColor' => '#000000'
    );
    /* ------------------------------- avg rating ------------------------------- */
    /* -------------------- pending_count and upcoming_count -------------------- */
    if ($MODULES_OBJ->isUberXFeatureAvailable()) {
        $getBookingData = $obj->MySQLSelect("SELECT iCabBookingId,eStatus FROM cab_booking WHERE iDriverId = '" . $iDriverId . "' $duration_sql_1 ");
        $db_data_pending = $db_data_assign = array();
        for ($g = 0; $g < count($getBookingData); $g++) {
            $eStatus = $getBookingData[$g]['eStatus'];
            if (strtoupper($eStatus) == "PENDING") {
                $db_data_pending[] = $getBookingData[$g]['iCabBookingId'];
            }
            if (strtoupper($eStatus) == "ACCEPTED" || strtoupper($eStatus) == "ASSIGN") {
                $db_data_assign[] = $getBookingData[$g]['iCabBookingId'];
            }
        }
        $returnArr['pending_count'] = '';
        $returnArr['upcoming_count'] = '';
    }
    else {
        $returnArr['pending_count'] = '';
        $returnArr['upcoming_count'] = '';
    }
    /* -------------------- pending_count and upcoming_count -------------------- */
    /* ------------------------------ your_earning ------------------------------ */
   // $sql = "SELECT fDeliveryCharge,eSystem,eType,fHotelCommision,fAddedOutstandingamt,fOutStandingAmount,fTax2,fTax1,fCommision,fTipPrice,fTripGenerateFare,iTripId,iActive,fCancellationFare,fWalletDebit FROM trips WHERE iDriverId='" . $iDriverId . "' $duration_sql_2";
    $sql = "SELECT t.fTipPrice as fTipAmountTrip,t.fDeliveryCharge,t.eSystem,t.eType,t.fHotelCommision,t.fAddedOutstandingamt,t.fOutStandingAmount,t.fTax2,t.fTax1,t.fCommision,t.fTipPrice,t.fTripGenerateFare,t.iTripId,t.iActive,t.fCancellationFare,t.fWalletDebit,t.iOrderId 
            FROM trips as t LEFT JOIN orders as o on o.iOrderId = t.iOrderId
            WHERE t.iDriverId='" . $iDriverId . "' $duration_sql_3";
    $tripData = $obj->MySQLSelect($sql);
    $totalEarnings = 0;
    for ($t = 0; $t < count($tripData); $t++) {
        $iTripId = $tripData[$t]['iTripId'];
        $iActive = $tripData[$t]['iActive'];
        $iOrderId = $tripData[$t]['iOrderId'];
        $fTipPrice = $tripData[$t]['fTipPrice'];
        $fCancellationFare = $fWalletDebit = 0;
        if (isset($tripData[$t]['fCancellationFare'])) {
            $fCancellationFare = $tripData[$t]['fCancellationFare'];
        }
        if (isset($tripData[$t]['fWalletDebit'])) {
            $fWalletDebit = $tripData[$t]['fWalletDebit'];
        }
        $earning = $tripData[$t]['fTripGenerateFare'];
        if ($tripData[$t]['eType'] == 'Ride' && $tripData[$t]['eSystem'] == 'DeliverAll') {
            $earning = $tripData[$t]['fDeliveryCharge'];
        }
        if ($tripData[$t]['eSystem'] == 'DeliverAll') {
            $sql1 = "SELECT fTipAmount FROM orders WHERE iOrderId = '" . $iOrderId . "'";
            $orderData = $obj->MySQLSelect($sql1);
            $fTipPrice = $orderData[0]['fTipAmount'];
        }
        if ($iActive == "Finished" || ($iActive == "Canceled" && ($fCancellationFare > 0 || $fWalletDebit > 0))) {
            $iFare = $earning + $fTipPrice - $tripData[$t]['fCommision'] - $tripData[$t]['fTax1'] - $tripData[$t]['fTax2'] - $tripData[$t]['fOutStandingAmount'] - $tripData[$t]['fAddedOutstandingamt'] - $tripData[$t]['fHotelCommision'];
            $iFareSum = str_replace(',', '', $iFare);
            $totalEarnings += $iFareSum;
        }
    }
    $register_driver = $obj->MySQLSelect("SELECT vCurrencyDriver,vAvgRating , tRegistrationDate FROM `register_driver` WHERE `iDriverId` = " . $iDriverId . " ");
    $currency = $obj->MySQLSelect("SELECT ratio,vName FROM `currency` WHERE vName='" . $register_driver[0]['vCurrencyDriver'] . "'");
    $totalEarning = formateNumAsPerCurrency($totalEarnings * $currency[0]['ratio'], $register_driver[0]['vCurrencyDriver']);
    $returnArr['your_earning'] = array(
        'vtitle' => (strtolower($dateRange) == "today") ? $languageLabelsArr['LBL_YOUR_EARNINGS_TODAY'] : $languageLabelsArr['LBL_YOUR_EARNINGS'], 'value' => $totalEarning,
    );
    /* ------------------------------ your_earning ------------------------------ */
    $returnArr['Action'] = "1";
    setDataResponse($returnArr);
}

if ($type == "loadAvailableRestaurantsAll") {
    $page = isset($_REQUEST['page']) ? trim($_REQUEST['page']) : 1;
    $iUserId = isset($_REQUEST["iUserId"]) ? $_REQUEST["iUserId"] : '';
    $passengerLat = isset($_REQUEST["PassengerLat"]) ? $_REQUEST["PassengerLat"] : '';
    $passengerLon = isset($_REQUEST["PassengerLon"]) ? $_REQUEST["PassengerLon"] : '';
    $fOfferType = isset($_REQUEST["fOfferType"]) ? $_REQUEST["fOfferType"] : ''; // Yes Or No
    $cuisineId = isset($_REQUEST["cuisineId"]) ? $_REQUEST["cuisineId"] : ''; // 1,2,3
    $orderby = isset($_REQUEST["orderby"]) ? $_REQUEST["orderby"] : ''; // 1,2,3
    $iCategoryId = isset($_REQUEST["iCategoryId"]) ? $_REQUEST["iCategoryId"] : ''; // 1,2,3
    $vUserDeviceCountry = isset($_REQUEST["vUserDeviceCountry"]) ? $_REQUEST["vUserDeviceCountry"] : '';
    $vUserDeviceCountry = strtoupper($vUserDeviceCountry);
    $vAddress = isset($_REQUEST["vAddress"]) ? $_REQUEST["vAddress"] : '';
    $sortby = isset($_REQUEST["sortby"]) ? $_REQUEST["sortby"] : 'relevance'; // relevance , rating, time, costlth, costhtl
    $searchword = isset($_REQUEST["searchword"]) ? $_REQUEST["searchword"] : '';
    $searchword = strtolower($searchword);
    if ($searchword == "" || $searchword == NULL) {
        $searchword = "";
    }
    $cuisineId_arr = array();
    if ($cuisineId != "") {
        $cuisineId_arr = explode(",", $cuisineId);
    }
    if ($vAddress != "") {
        $vAddress_arr = explode(",", $vAddress);
        $vAddress = end($vAddress_arr);
        $vAddress = trim($vAddress);
    }

    $allServiceCategories = json_decode(serviceCategories, true);
    // echo "<pre>"; print_r($allServiceCategories); exit;
    foreach ($allServiceCategories as $ServiceCategory) {
        $returnArr = array();
        $iServiceId = $ServiceCategory['iServiceId'];

        ## Update Demo User's Lat Long As per User's Location ##
        if (SITE_TYPE == "Demo" && $iUserId != "") {
            //Added By HJ On 14-07-2020 For Optimization register_user Table Query Start
            $tblName = "register_user";
            if (isset($userDetailsArr[$tblName . "_" . $iUserId]) && count($userDetailsArr[$tblName . "_" . $iUserId]) > 0) {
                $userData = $userDetailsArr[$tblName . "_" . $iUserId];
            }
            else {
                $userData = $obj->MySQLSelect("SELECT *,iUserId as iMemberId FROM " . $tblName . " WHERE iUserId='" . $iUserId . "'");
                $userDetailsArr[$tblName . "_" . $iUserId] = $userData;
            }
            $uemail = $userData[0]['vEmail'];
            //Added By HJ On 14-07-2020 For Optimization register_user Table Query End
            //$uemail = get_value('register_user', 'vEmail', 'iUserId', $iUserId, '', 'true');
            $uemail = explode("-", $uemail);
            $uemail = $uemail[1];
            if ($uemail != "") {
                $sql = "SELECT GROUP_CONCAT(iCompanyId)as companyId FROM company WHERE vEmail LIKE '%$uemail%' AND iServiceId = $iServiceId";
                $db_rec = $obj->MySQLSelect($sql);
                $usercompanyId = $db_rec[0]['companyId'];
                if ($usercompanyId != "") {
                    $vLatitude = 'vRestuarantLocationLat';
                    $vLongitude = 'vRestuarantLocationLong';
                    $sql = "SELECT ROUND(( 6371 * acos( cos( radians(" . $passengerLat . ") )
                  * cos( radians( ROUND(" . $vLatitude . ",8) ) )
                    * cos( radians( ROUND(" . $vLongitude . ",8) ) - radians(" . $passengerLon . ") )
                        + sin( radians(" . $passengerLat . ") )
                    * sin( radians( ROUND(" . $vLatitude . ",8) ) ) ) ),2) AS distance, company.*  FROM `company`
                        WHERE (" . $vLatitude . " != '' AND " . $vLongitude . " != '' ) AND iCompanyId IN ($usercompanyId) AND eStatus = 'Active' AND iServiceId = $iServiceId
                        HAVING distance < " . $USER_STORE_RANGE . " ORDER BY distance ASC LIMIT 0,1";
                    $Data = $obj->MySQLSelect($sql);
                    if (count($Data) == 0) {
                        $sql = "SELECT iCompanyId FROM company WHERE iCompanyId IN ($usercompanyId) AND eStatus = 'Active' AND iServiceId = '" . $iServiceId . "' AND eStoreLocationUpdate = 'NO' LIMIT 0,1";
                        $CompanyData = $obj->MySQLSelect($sql);
                        $CurrentDate = date("Y-m-d H:i:s");
                        if (count($CompanyData) > 0) {
                            $updateCompanyId = $CompanyData[0]['iCompanyId'];
                            $updateQuery = "UPDATE company SET vRestuarantLocationLat='" . $passengerLat . "', vRestuarantLocationLong = '" . $passengerLon . "', eStoreLocationUpdate = 'Yes', eStoreLocationUpdateDateTime = '" . $CurrentDate . "' WHERE iCompanyId = '" . $updateCompanyId . "'";
                            $obj->sql_query($updateQuery);
                        }
                        else {
                            $sql = "SELECT iCompanyId FROM company WHERE iCompanyId IN ($usercompanyId) AND eStatus = 'Active' AND iServiceId = '" . $iServiceId . "' AND eStoreLocationUpdate = 'Yes' ORDER BY eStoreLocationUpdateDateTime ASC LIMIT 0,1";
                            $NewCompanyData = $obj->MySQLSelect($sql);
                            $newupdateCompanyId = $NewCompanyData[0]['iCompanyId'];
                            $updateQuery = "UPDATE company SET vRestuarantLocationLat='" . $passengerLat . "', vRestuarantLocationLong = '" . $passengerLon . "',  eStoreLocationUpdateDateTime = '" . $CurrentDate . "' WHERE iCompanyId = '" . $newupdateCompanyId . "'";
                            $obj->sql_query($updateQuery);
                        }
                    }
                }
            }
        }
        ## Update Demo User's Lat Long As per User's Location ##
        $Data = FetchNearByStores($passengerLat, $passengerLon, $iUserId, $fOfferType, $searchword, $vAddress, $iServiceId);
        $totalsearchcuisinerestaurants = 0;
        $Data = array_values($Data);
        $dataNewArr = array();
        $countdata = count($Data);
        for ($c = 0; $c < $countdata; $c++) {
            unset($Data[$c]['vPassword']);
            unset($Data[$c]['vPasswordToken']);
            unset($Data[$c]['vPassword_token']);
            $iCompanyId = $Data[$c]['iCompanyId'];
            if ($Data[$c]['vImage'] != "") {
                $Data[$c]['vImage'] = $tconfig['tsite_upload_images_compnay'] . '/' . $iCompanyId . '/3_' . $Data[$c]['vImage'];
            }
            //Added By HJ On 26-06-2019 For Get And Display Store Demo Image Start
            if (isset($Data[$c]['vDemoStoreImage']) && $Data[$c]['vDemoStoreImage'] != "" && SITE_TYPE == "Demo") {
                $demoImgPath = $tconfig['tsite_upload_demo_compnay_doc_path'] . $Data[$c]['vDemoStoreImage'];
                if (file_exists($demoImgPath)) {
                    $demoImgUrl = $tconfig['tsite_upload_demo_compnay_doc'] . $Data[$c]['vDemoStoreImage'];
                    $Data[$c]['vImage'] = $demoImgUrl;
                }
            }
            //Added By HJ On 26-06-2019 For Get And Display Store Demo Image End
            if ($Data[$c]['vCoverImage'] != "") {
                $Data[$c]['vCoverImage'] = $tconfig['tsite_upload_images_compnay'] . '/' . $iCompanyId . '/' . $Data[$c]['vCoverImage'];
            }
            $isRemoveRestaurantIntoList = "No";
            // # Checking For Selected Cuisine ##
            $Restaurant_Cuisine_Id_str = $Data[$c]['Restaurant_Cuisine_Id'];
            $Restaurant_Cuisine_Id_arr = explode(",", $Restaurant_Cuisine_Id_str);
            $match_cusisine_result_arr = array_intersect($cuisineId_arr, $Restaurant_Cuisine_Id_arr);
            if (count($match_cusisine_result_arr) == 0 && count($cuisineId_arr) > 0) {
                $isRemoveRestaurantIntoList = "Yes";
            }
            // # Checking For Selected Cuisine ##
            // # Checking For Search Keyword ##
            $vCompany = strtolower($Data[$c]['vCompany']);
            $Restaurant_Cuisine = strtolower($Data[$c]['Restaurant_Cuisine']);
            if (((!preg_match("/$searchword/i", $vCompany)) && (!preg_match("/$searchword/i", $Restaurant_Cuisine))) && $searchword != "") {
                $isRemoveRestaurantIntoList = "Yes";
            }
            // # Checking For Search Keyword ##
            // # Getting Nos of restaurants matching with cuisine searchtext ##
            if (preg_match("/$searchword/i", $Restaurant_Cuisine) && $searchword != "") {
                $totalsearchcuisinerestaurants = $totalsearchcuisinerestaurants + 1;
            }
            // # Getting Nos of restaurants matching with cuisine searchtext ##
            // # Checking For Food Menu Available for Company Or Not ##
            $CompanyFoodDataCount = $Data[$c]['CompanyFoodDataCount'];
            if ($CompanyFoodDataCount == 0) {
                $isRemoveRestaurantIntoList = "Yes";
            }
            // # Checking For Food Menu Available for Company Or Not ##
            if ($isRemoveRestaurantIntoList != "Yes") {
                $dataNewArr[] = $Data[$c];
            }
            $fsql1 = "";
            if ($MODULES_OBJ->isEnableStoreMultiServiceCategories()) {
                $fsql1 = " AND iServiceId = '$iServiceId'";
            }
            $getFooMenu = $obj->MySQLSelect("SELECT GROUP_CONCAT(iFoodMenuId) as foodMenuIds FROM food_menu WHERE iCompanyId = '" . $Data[$c]['iCompanyId'] . "' AND eStatus = 'Active' $fsql1");
            if (!empty($getFooMenu[0]['foodMenuIds'])) {
                $getStoreItems = $obj->MySQLSelect("SELECT COUNT(iMenuItemId) as menuItemCount FROM menu_items WHERE iFoodMenuId IN (" . $getFooMenu[0]['foodMenuIds'] . ") AND eStatus = 'Active'");
                if ($getStoreItems[0]['menuItemCount'] == 0) {
                    unset($Data[$c]);
                }
            }
            else {
                unset($Data[$c]);
            }
        }
        if ($cuisineId != "") {
            $Data = $dataNewArr;
        }
        $Data_Filter = $Data;
        $Data = array_values($Data_Filter);
        // ## Sorting Of Restaurants by relevance , rating, time, costlth, costhtl ###
        if ($sortby == "" || $sortby == NULL) {
            $sortby = "relevance";
        }
        if ($sortby == "rating") {
            $sortfield = "vAvgRatingOrig";
            $sortorder = SORT_DESC;
        }
        elseif ($sortby == "time") {
            $sortfield = "fPrepareTime";
            $sortorder = SORT_ASC;
        }
        elseif ($sortby == "costlth") {
            $sortfield = "fPricePerPerson";
            $sortorder = SORT_ASC;
        }
        elseif ($sortby == "costhtl") {
            $sortfield = "fPricePerPerson";
            $sortorder = SORT_DESC;
        }
        else {
            $sortfield = "restaurantstatus";
            $sortorder = SORT_DESC;
        }
        foreach ($Data as $k => $v) {
            $Data_name[$sortfield][$k] = $v[$sortfield];
            $Data_name['restaurantstatus'][$k] = $v['restaurantstatus'];
        }
        array_multisort($Data_name['restaurantstatus'], SORT_DESC, $Data_name[$sortfield], $sortorder, $Data);
        // ## Sorting Of Restaurants by relevance , rating, time, costlth, costhtl ###
        // ## Sorting Of Demo User Restaurant To Display First ###
        $searchbydemousercompany = "No";
        if (SITE_TYPE == "Demo" && $iUserId != "") {
            //Added By HJ On 14-07-2020 For Optimization register_user Table Query Start
            $tblName = "register_user";
            if (isset($userDetailsArr[$tblName . "_" . $iUserId]) && count($userDetailsArr[$tblName . "_" . $iUserId]) > 0) {
                $userData = $userDetailsArr[$tblName . "_" . $iUserId];
            }
            else {
                $userData = $obj->MySQLSelect("SELECT *,iUserId as iMemberId FROM " . $tblName . " WHERE iUserId='" . $iUserId . "'");
                $userDetailsArr[$tblName . "_" . $iUserId] = $userData;
            }
            $useremail = $userData[0]['vEmail'];
            //Added By HJ On 14-07-2020 For Optimization register_user Table Query End
            //$useremail = get_value('register_user', 'vEmail', 'iUserId', $iUserId, '', 'true');
            $useremail = explode("-", $useremail);
            if (count($useremail) > 0) {
                $searchbydemousercompany = "Yes";
                $useremail = $useremail[1];
                for ($k = 0; $k < count($Data); $k++) {
                    $companyemail = $Data[$k]['vEmail'];
                    if (preg_match("/$useremail/", $companyemail)) {
                        $Data[$k]['eDemoUserCompany'] = "Yes";
                    }
                    else {
                        $Data[$k]['eDemoUserCompany'] = "No";
                    }
                }
            }
        }
        if ($searchbydemousercompany == "Yes") {
            usort($Data, function ($a, $b) {
                if ($a["eDemoUserCompany"] == $b["eDemoUserCompany"]) {
                    return 0;
                }
                return ($a["eDemoUserCompany"] < $b["eDemoUserCompany"]) ? 1 : -1;
            });
            $newData = array();
            $newData = $Data;
            for ($j = 0; $j < count($Data); $j++) {
                if ($Data[$j]['eDemoUserCompany'] == "Yes") {
                    if ($j != 0) {
                        //unset($newData[$j]);
                    }
                }
            }
            $Data = array_values($newData);
        }
        // ## Sorting Of Demo User Restaurant To Display First ###
        // ## Checking For Pagination ###
        $Data_new = array_values($Data);
        if ($iCategoryId != "" && $iCategoryId > 0) {
            $Data = $storeCatAccArr = $s_sctSqlData = $storeTagsAccArr = array();
            //Added By HJ On 14-07-2020 For Optimize store_categories Table Query Start
            $storeCatData = $obj->MySQLSelect("SELECT * FROM store_categories");
            for ($h = 0; $h < count($storeCatData); $h++) {
                if ($storeCatData[$h]['iCategoryId'] == $iCategoryId) {
                    $s_sctSqlData = array();
                    $s_sctSqlData[] = $storeCatData[$h];
                }
                $storeCatAccArr[$storeCatData[$h]['iServiceId']][$storeCatData[$h]['eType']][] = $storeCatData[$h];
            }
            if (count($s_sctSqlData) == 0) {
                $s_sctSqlData = $obj->MySQLSelect("select iCategoryId,eType,iServiceId from store_categories where iCategoryId = " . $iCategoryId);
            }
            //Added By HJ On 14-07-2020 For Optimize store_categories Table Query End
            //Added By HJ On 14-07-2020 For Optimize store_categories Table Query Start
            $storCattagsData = $obj->MySQLSelect("SELECT iCategoryId,iCompanyId from store_category_tags");
            for ($g = 0; $g < count($storCattagsData); $g++) {
                $storeTagsAccArr[$storCattagsData[$g]['iCompanyId']][$storCattagsData[$g]['iCategoryId']][] = $storCattagsData[$g];
            }
            //Added By HJ On 14-07-2020 For Optimize store_categories Table Query End
            foreach ($Data_new as $dkey => $dvalue) {
                if ($s_sctSqlData[0]['eType'] == 'newly_open' && $s_sctSqlData[0]['iServiceId'] == $iServiceId) {
                    $date1 = date('Y-m-d H:i:s');
                    $date2 = $dvalue['tRegistrationDate'];
                    $diff = strtotime($date2) - strtotime($date1);
                    $diff_days = abs(round($diff / 86400));
                    //$sctSql = "select iDaysRange from store_categories where eType='newly_open' AND iServiceId=" . $iServiceId;
                    $sctDaysRange = ($sctSql_data[0]['iDaysRange'] != "") ? $sctSql_data[0]['iDaysRange'] : 30;
                    if ($diff_days <= $sctDaysRange) {
                        $Data[] = $dvalue;
                    }
                }
                else if ($s_sctSqlData[0]['eType'] == 'offers' && $s_sctSqlData[0]['iServiceId'] == $iServiceId) {
                    if ($dvalue['fOfferAppyType'] != "None") {
                        $Data[] = $dvalue;
                    }
                }
                else if ($s_sctSqlData[0]['eType'] == 'list_all' && $s_sctSqlData[0]['iServiceId'] == $iServiceId) {
                    $Data[] = $dvalue;
                }
                else {
                    //$storCattagsSql = "select iCategoryId from store_category_tags where iCompanyId = " . $dvalue['iCompanyId'] . " AND iCategoryId = " . $iCategoryId;
                    //$storCattagsData = $obj->MySQLSelect($storCattagsSql);
                    $storCattagsData = array();
                    if (isset($storeTagsAccArr[$dvalue['iCompanyId']][$iCategoryId])) {
                        $storCattagsData = $storeTagsAccArr[$dvalue['iCompanyId']][$iCategoryId];
                    }
                    if (count($storCattagsData)) {
                        $Data[] = $dvalue;
                    }
                }
            }
        }
        $Data_new = array_values($Data);
        $per_page = 12;
        $totalStore = count($Data); //Added By HJ On 18-01-2020 As Per Discuss Between CS and KS Sir
        $TotalPages = ceil(count($Data) / $per_page);
        $pagecount = $page - 1;
        $start_limit = $pagecount * $per_page;
        $Data = array_slice($Data_new, $start_limit, $per_page);
        //$Data = $Data_new;
        $ispriceshow = '';
        $servFields = 'eType';
        $ServiceCategoryData = get_value('service_categories', $servFields, 'iServiceId', $iServiceId);
        if (!empty($ServiceCategoryData)) {
            if (!empty($ServiceCategoryData[0]['eType'])) {
                $ispriceshow = $ServiceCategoryData[0]['eType'];
            }
        }
        // ## Checking For Pagination ###
        $returnArr['totalStore'] = $totalStore; //Added By HJ On 18-01-2020 As Per Discuss Between CS and KS Sir
        if (!empty($Data)) {
            $returnArr['Action'] = "1";
            $returnArr['message'] = $Data;
            if ($TotalPages > $page) {
                $returnArr['NextPage'] = $page + 1;
            }
            else {
                $returnArr['NextPage'] = "0";
            }
            $storeCatIserviceId = $iServiceId;
            if ($MODULES_OBJ->isStoreClassificationEnable() && $iCategoryId == "") {
                //lang code same as staticpage type as told by KS..
                $vLangCode = "";
                $vGeneralLang = isset($_REQUEST["vGeneralLang"]) ? $_REQUEST["vGeneralLang"] : '';
                $vLang = isset($_REQUEST["vLang"]) ? $_REQUEST["vLang"] : '';
                if (!empty($vGeneralLang)) {
                    $vLangCode = $vGeneralLang;
                }
                else if (!empty($vLang)) {
                    $vLangCode = $vLang;
                }
                else if (!empty($iUserId)) {
                    //Added By HJ On 14-07-2020 For Optimization register_user Table Query Start
                    $tblName = "register_user";
                    if (isset($userDetailsArr[$tblName . "_" . $iUserId]) && count($userDetailsArr[$tblName . "_" . $iUserId]) > 0) {
                        $userData = $userDetailsArr[$tblName . "_" . $iUserId];
                    }
                    else {
                        $userData = $obj->MySQLSelect("SELECT *,iUserId as iMemberId FROM " . $tblName . " WHERE iUserId='" . $iUserId . "'");
                        $userDetailsArr[$tblName . "_" . $iUserId] = $userData;
                    }
                    $vLangCode = $userData[0]['vLang'];
                    //Added By HJ On 14-07-2020 For Optimization register_user Table Query End
                    //$vLangCode = get_value('register_user', 'vLang', 'iUserId', $iUserId, '', 'true');
                }
                if ($vLangCode == "" || $vLangCode == NULL) {
                    //Added By HJ On 14-07-2020 For Optimize language_master Table Query Start
                    $vLangCode = $LANG_OBJ->FetchDefaultLangData("vCode");
                    //Added By HJ On 14-07-2020 For Optimize language_master Table Query End
                }
                // Store Categories
                $returnArr['CategoryWiseStores'] = $storeTagsAccArr = $storeCatAccArr = $storeCatServiceArr = array();
                //Added By HJ On 14-07-2020 For Optimize store_categories Table Query Start
                $storCattagsData = $obj->MySQLSelect("SELECT iCategoryId,iCompanyId from store_category_tags");
                for ($g = 0; $g < count($storCattagsData); $g++) {
                    $storeTagsAccArr[$storCattagsData[$g]['iCompanyId']][] = $storCattagsData[$g];
                }
                //Added By HJ On 14-07-2020 For Optimize store_categories Table Query End
                //Added By HJ On 14-07-2020 For Optimize store_categories Table Query Start
                $storeCatData = $obj->MySQLSelect("SELECT iServiceId,iCategoryId,JSON_UNQUOTE(JSON_VALUE(tCategoryName, '$.tCategoryName_" . $vLangCode . "')) as tCategoryName,JSON_UNQUOTE(JSON_VALUE(tCategoryDescription, '$.tCategoryDescription_" . $vLangCode . "')) as tCategoryDescription,tCategoryImage,iDisplayOrder,iDaysRange,eType FROM store_categories WHERE eStatus = 'Active' AND iServiceId = '$iServiceId'");
                for ($h = 0; $h < count($storeCatData); $h++) {
                    $storeCatServiceArr[$storeCatData[$h]['iServiceId']][$storeCatData[$h]['eType']][] = $storeCatData[$h];
                    $storeCatAccArr[$storeCatData[$h]['iCategoryId']][] = $storeCatData[$h];
                }
                //Added By HJ On 14-07-2020 For Optimize store_categories Table Query End
                foreach ($Data_new as $dkey => $dvalue) {
                    //$storCattagsData = $obj->MySQLSelect("SELECT iCategoryId from store_category_tags where iCompanyId = " . $dvalue['iCompanyId']);
                    $storCattagsData = array();
                    if (isset($storeTagsAccArr[$dvalue['iCompanyId']])) {
                        $storCattagsData = $storeTagsAccArr[$dvalue['iCompanyId']];
                    }
                    if (count($storCattagsData)) {
                        foreach ($storCattagsData as $sctvalue) {
                            //$store_cat_sql = "select iCategoryId,JSON_UNQUOTE(JSON_VALUE(tCategoryName, '$.tCategoryName_" . $vLangCode . "')) as tCategoryName,JSON_UNQUOTE(JSON_VALUE(tCategoryDescription, '$.tCategoryDescription_" . $vLangCode . "')) as tCategoryDescription,tCategoryImage,iDisplayOrder,eType from store_categories where iCategoryId = " . $sctvalue['iCategoryId'] . " AND eStatus = 'Active'";
                            //$store_cat_sql_data = $obj->MySQLSelect($store_cat_sql);
                            $store_cat_sql_data = array();
                            if (isset($storeCatAccArr[$sctvalue['iCategoryId']])) {
                                $store_cat_sql_data = $storeCatAccArr[$sctvalue['iCategoryId']];
                            }
                            foreach ($store_cat_sql_data as $sctdata) {
                                $sctName = $sctdata['tCategoryName'];
                                $sctDesc = $sctdata['tCategoryDescription'];
                                $sctDataId = $sctdata['iCategoryId'];
                                $tCategoryImage = $sctdata['tCategoryImage'];
                                $eType = $sctdata['eType'];
                                if (count($returnArr['CategoryWiseStores']) > 0) {
                                    $getTitlekey = searchStoreCategoryTitle($sctName, $returnArr['CategoryWiseStores']);
                                    if ($getTitlekey > -1) {
                                        if (count($returnArr['CategoryWiseStores'][$getTitlekey]['subData']) < 12) {
                                            $returnArr['CategoryWiseStores'][$getTitlekey]['subData'][] = $dvalue;
                                            $returnArr['CategoryWiseStores'][$getTitlekey]['countdata'] = count($returnArr['CategoryWiseStores'][$getTitlekey]['subData']);
                                        }
                                        else {
                                            $newdata['totaldata'][] = $dvalue;
                                            $returnArr['CategoryWiseStores'][$getTitlekey]['countdata'] = count($returnArr['CategoryWiseStores'][$getTitlekey]['subData']) + count($newdata['totaldata']);
                                        }
                                    }
                                    else {
                                        $returnArr['CategoryWiseStores'][] = array(
                                            'iCategoryId' => $sctDataId, 'vTitle' => $sctName, 'vDescription' => ($sctDesc != "") ? $sctDesc : "", 'vCategoryImage' => ($tCategoryImage != '') ? ($tconfig['tsite_upload_images_store_categories'] . "/" . $tCategoryImage) : "", 'iDisplayOrder' => $sctdata['iDisplayOrder'], 'eType' => $eType, 'subData' => array($dvalue)
                                        );
                                    }
                                }
                                else {
                                    $returnArr['CategoryWiseStores'][] = array(
                                        'iCategoryId' => $sctDataId, 'vTitle' => $sctName, 'vDescription' => ($sctDesc != "") ? $sctDesc : "", 'vCategoryImage' => ($tCategoryImage != '') ? ($tconfig['tsite_upload_images_store_categories'] . "/" . $tCategoryImage) : "", 'iDisplayOrder' => $sctdata['iDisplayOrder'], 'eType' => $eType, 'subData' => array($dvalue)
                                    );
                                }
                            }
                        }
                    }
                    // Offers - Stores/Restaurants
                    if ($dvalue['fOfferAppyType'] != "None") {
                        //$sctSql = "select iCategoryId,JSON_UNQUOTE(JSON_VALUE(tCategoryName, '$.tCategoryName_" . $vLangCode . "')) as tCategoryName,JSON_UNQUOTE(JSON_VALUE(tCategoryDescription, '$.tCategoryDescription_" . $vLangCode . "')) as tCategoryDescription,tCategoryImage,iDisplayOrder,eType from store_categories where eType = 'offers' AND iServiceId = " . $storeCatIserviceId . " AND eStatus = 'Active'";
                        //$sctSql_data = $obj->MySQLSelect($sctSql);
                        $sctSql_data = array();
                        if (isset($storeCatServiceArr[$storeCatIserviceId]['offers'])) {
                            $sctSql_data = $storeCatServiceArr[$storeCatIserviceId]['offers'];
                        }
                        $sctNameOffer = $sctSql_data[0]['tCategoryName'];
                        $sctDescOffer = $sctSql_data[0]['tCategoryDescription'];
                        $sctDataId = $sctSql_data[0]['iCategoryId'];
                        $getTitlekey = searchStoreCategoryTitle($sctNameOffer, $returnArr['CategoryWiseStores']);
                        if ($getTitlekey > -1) {
                            if (count($returnArr['CategoryWiseStores'][$getTitlekey]['subData']) < 12) {
                                $returnArr['CategoryWiseStores'][$getTitlekey]['subData'][] = $dvalue;
                                $returnArr['CategoryWiseStores'][$getTitlekey]['countdata'] = count($returnArr['CategoryWiseStores'][$getTitlekey]['subData']);
                            }
                            else {
                                $newdata['totaldata'][] = $dvalue;
                                $returnArr['CategoryWiseStores'][$getTitlekey]['countdata'] = count($returnArr['CategoryWiseStores'][$getTitlekey]['subData']) + count($newdata['totaldata']);
                            }
                        }
                        else {
                            $returnArr['CategoryWiseStores'][] = array(
                                'iCategoryId' => $sctDataId, 'vTitle' => $sctNameOffer, 'vDescription' => ($sctDescOffer != "") ? $sctDescOffer : "", 'vCategoryImage' => ($sctSql_data[0]['tCategoryImage'] != "") ? ($tconfig['tsite_upload_images_store_categories'] . "/" . $sctSql_data[0]['tCategoryImage']) : "", 'iDisplayOrder' => $sctSql_data[0]['iDisplayOrder'], 'eType' => $sctSql_data[0]['eType'], 'subData' => array($dvalue)
                            );
                        }
                    }
                    // Newly Open Stores/Restaurants
                    $date1 = date('Y-m-d H:i:s');
                    $date2 = $dvalue['tRegistrationDate'];
                    $diff = strtotime($date2) - strtotime($date1);
                    $diff_days = abs(round($diff / 86400));
                    //$sctSql = "select iCategoryId,JSON_UNQUOTE(JSON_VALUE(tCategoryName, '$.tCategoryName_" . $vLangCode . "')) as tCategoryName,JSON_UNQUOTE(JSON_VALUE(tCategoryDescription, '$.tCategoryDescription_" . $vLangCode . "')) as tCategoryDescription,tCategoryImage,iDisplayOrder,iDaysRange,eType from store_categories where eType = 'newly_open' AND iServiceId = " . $storeCatIserviceId . " AND eStatus = 'Active'";
                    //$sctSql_data = $obj->MySQLSelect($sctSql);
                    $sctSql_data = array();
                    if (isset($storeCatServiceArr[$storeCatIserviceId]['newly_open'])) {
                        $sctSql_data = $storeCatServiceArr[$storeCatIserviceId]['newly_open'];
                    }
                    $sctNameNew = $sctSql_data[0]['tCategoryName'];
                    $sctDescNew = $sctSql_data[0]['tCategoryDescription'];
                    $sctDataId = $sctSql_data[0]['iCategoryId'];
                    $sctDaysRange = ($sctSql_data[0]['iDaysRange'] != "") ? $sctSql_data[0]['iDaysRange'] : 30;
                    if ($diff_days <= $sctDaysRange) {
                        $getTitlekey = searchStoreCategoryTitle($sctNameNew, $returnArr['CategoryWiseStores']);
                        if ($getTitlekey > -1) {
                            if (count($returnArr['CategoryWiseStores'][$getTitlekey]['subData']) < 12) {
                                $returnArr['CategoryWiseStores'][$getTitlekey]['subData'][] = $dvalue;
                                $returnArr['CategoryWiseStores'][$getTitlekey]['countdata'] = count($returnArr['CategoryWiseStores'][$getTitlekey]['subData']);
                            }
                            else {
                                $newdata['totaldata'][] = $dvalue;
                                $returnArr['CategoryWiseStores'][$getTitlekey]['countdata'] = count($returnArr['CategoryWiseStores'][$getTitlekey]['subData']) + count($newdata['totaldata']);
                            }
                        }
                        else {
                            $returnArr['CategoryWiseStores'][] = array(
                                'iCategoryId' => $sctDataId, 'vTitle' => $sctNameNew, 'vDescription' => ($sctDescNew != "") ? $sctDescNew : "", 'vCategoryImage' => ($sctSql_data[0]['tCategoryImage'] != "") ? ($tconfig['tsite_upload_images_store_categories'] . "/" . $sctSql_data[0]['tCategoryImage']) : "", 'iDisplayOrder' => $sctSql_data[0]['iDisplayOrder'], 'eType' => $sctSql_data[0]['eType'], 'subData' => array($dvalue)
                            );
                        }
                    }
                }
                // All Stores/Restaurants
                //$storCatAllsql = "select iCategoryId,JSON_UNQUOTE(JSON_VALUE(tCategoryName, '$.tCategoryName_" . $vLangCode . "')) as tCategoryName,JSON_UNQUOTE(JSON_VALUE(tCategoryDescription, '$.tCategoryDescription_" . $vLangCode . "')) as tCategoryDescription,tCategoryImage,iDisplayOrder,eType from store_categories where eType = 'list_all' AND iServiceId = " . $storeCatIserviceId;
                //$storCatAlldata = $obj->MySQLSelect($storCatAllsql);
                $storCatAlldata = array();
                if (isset($storeCatServiceArr[$storeCatIserviceId]['list_all'])) {
                    $storCatAlldata = $storeCatServiceArr[$storeCatIserviceId]['list_all'];
                }
                $sctNameAll = $storCatAlldata[0]['tCategoryName'];
                $sctDescAll = $storCatAlldata[0]['tCategoryDescription'];
                $sctDataId = $storCatAlldata[0]['iCategoryId'];
                $returnArr['CategoryWiseStores'][] = array(
                    'iCategoryId' => $sctDataId, 'vTitle' => $sctNameAll, 'vDescription' => ($sctDescAll != "") ? $sctDescAll : "", 'vCategoryImage' => ($storCatAlldata[0]['tCategoryImage'] != "") ? ($tconfig['tsite_upload_images_store_categories'] . "/" . $storCatAlldata[0]['tCategoryImage']) : "", 'iDisplayOrder' => $storCatAlldata[0]['iDisplayOrder'], 'eType' => $storCatAlldata[0]['eType'], 'subData' => $Data
                );
                usort($returnArr['CategoryWiseStores'], function ($a, $b) {
                    return $a["iDisplayOrder"] - $b["iDisplayOrder"];
                });
                foreach ($returnArr['CategoryWiseStores'] as $catkey => $catvalue) {
                    if ($returnArr['CategoryWiseStores'][$catkey]['eType'] == "list_all") {
                        if ($totalStore >= 13) {
                            $returnArr['CategoryWiseStores'][$catkey]['IS_SHOW_ALL'] = "Yes";
                        }
                        else {
                            $returnArr['CategoryWiseStores'][$catkey]['IS_SHOW_ALL'] = "No";
                        }
                    }
                    else {
                        //$countSubData = count($returnArr['CategoryWiseStores'][$catkey]['subData']);
                        $countSubData = $returnArr['CategoryWiseStores'][$catkey]['countdata'];
                        if ($countSubData >= 13) {
                            $returnArr['CategoryWiseStores'][$catkey]['IS_SHOW_ALL'] = "Yes";
                        }
                        else {
                            $returnArr['CategoryWiseStores'][$catkey]['IS_SHOW_ALL'] = "No";
                        }
                    }
                    if ($returnArr['CategoryWiseStores'][$catkey]['vTitle'] != $sctNameAll) {
                        shuffle($returnArr['CategoryWiseStores'][$catkey]['subData']);
                        $shuffled_arr = $returnArr['CategoryWiseStores'][$catkey]['subData'];
                        $movetolast = array();
                        foreach ($shuffled_arr as $mkey => $mvalue) {
                            if (strtolower($mvalue['restaurantstatus']) == 'closed') {
                                $movetolast[] = $shuffled_arr[$mkey];
                                unset($shuffled_arr[$mkey]);
                            }
                        }
                        $returnArr['CategoryWiseStores'][$catkey]['subData'] = array_merge($shuffled_arr, $movetolast);
                    }
                    if ($returnArr['CategoryWiseStores'][$catkey]['iCategoryId'] == "") {
                        unset($returnArr['CategoryWiseStores'][$catkey]);
                    }
                }
            }
            $returnArr['CategoryWiseStores'] = array_values($returnArr['CategoryWiseStores']);
            $returnArr['totalsearchcuisinerestaurants'] = $totalsearchcuisinerestaurants;
            $returnArr['ispriceshow'] = $ispriceshow;
        }
        else {
            $returnArr['Action'] = "0";
            $returnArr['message'] = "LBL_NO_RESTAURANT_FOUND_TXT";
            if ($MODULES_OBJ->isFavouriteStoreModuleAvailable() && !empty($iUserId)) {
                $eFavStore = isset($_REQUEST['eFavStore']) ? clean($_REQUEST['eFavStore']) : ''; // No=> 'Not
            }
            if ((!empty($fOfferType) && strtoupper($fOfferType) == "YES") || !empty($cuisineId) || (!empty($eFavStore) && strtoupper($eFavStore) == "YES")) {
                $returnArr['message1'] = "LBL_NO_RECORDS_FOUND1";
            }
        }
        //getBanners type start
        if ($iUserId != "") {
            //Added By HJ On 14-07-2020 For Optimization register_user Table Query Start
            $tblName = "register_user";
            if (isset($userDetailsArr[$tblName . "_" . $iUserId]) && count($userDetailsArr[$tblName . "_" . $iUserId]) > 0) {
                $userData = $userDetailsArr[$tblName . "_" . $iUserId];
            }
            else {
                $userData = $obj->MySQLSelect("SELECT *,iUserId as iMemberId FROM " . $tblName . " WHERE iUserId='" . $iUserId . "'");
                $userDetailsArr[$tblName . "_" . $iUserId] = $userData;
            }
            $vLanguage = $userData[0]['vLang'];
            //Added By HJ On 14-07-2020 For Optimization register_user Table Query End
            //$vLanguage = get_value('register_user', 'vLang', 'iUserId', $iUserId, '', 'true');
        }
        if ($vLanguage == "" || $vLanguage == NULL) {
            //Added By HJ On 14-07-2020 For Optimize language_master Table Query Start
            $vLanguage = $LANG_OBJ->FetchDefaultLangData("vCode");
            //Added By HJ On 14-07-2020 For Optimize language_master Table Query End
            //$vLanguage = get_value('language_master', 'vCode', 'eDefault', 'Yes', '', 'true');
        }
        // $banners= get_value('banners', 'vImage', 'vCode',$vLanguage,' ORDER BY iDisplayOrder ASC');
        $whereloc = " AND iLocationid IN ('-1')";
        if ($MODULES_OBJ->isEnableLocationwiseBanner()) {
            $User_Address_Banner = array($passengerLat, $passengerLon);
            if (!empty($User_Address_Banner)) {
                $iLocationIdBanner = GetUserGeoLocationIdBanner($User_Address_Banner);
                $country_str_banner = "'-1'";
                if (count($iLocationIdBanner) > 0) {
                    foreach ($iLocationIdBanner as $key => $value) {
                        $country_str_banner .= ", '" . $value . "'";
                    }
                    $whereloc = " AND iLocationid IN (" . $country_str_banner . ")";
                }
            }
        }
        $sql = "SELECT vImage,vStatusBarColor,iUniqueId FROM `banners` WHERE vCode = '" . $vLanguage . "' AND eStatus = 'Active' AND iServiceId = '" . $iServiceId . "' $whereloc ORDER BY iDisplayOrder ASC";
        $banners = $obj->MySQLSelect($sql);
        $bdata = array();
        $count = 0;
        for ($i = 0; $i < count($banners); $i++) {
            if ($banners[$i]['vImage'] != "") {
                $bdata[$count]['vImage'] = $tconfig["tsite_url"] . 'assets/img/images/' . urlencode($banners[$i]['vImage']);
                $bdata[$count]['vStatusBarColor'] = (isset($banners[$i]['vStatusBarColor']) ? $banners[$i]['vStatusBarColor'] : '');
                $banner_img_path = $tconfig['tpanel_path'] . 'assets/img/images/' . $banners[$i]['vImage'];
                if (file_exists($banner_img_path) && empty($banners[$i]['vStatusBarColor'])) {
                    $bdata[$count]['vStatusBarColor'] = getColorFromImage($banner_img_path);
                    $obj->sql_query("UPDATE banners SET vStatusBarColor = '" . $bdata[$count]['vStatusBarColor'] . "' WHERE iUniqueId = '" . $banners[$i]['iUniqueId'] . "'");
                }
                $count++;
            }
        }
        $returnArr['banner_data'] = !empty($bdata) ? $bdata : '';
        //getBanners type end
        //getCuisineList type start
        $vGeneralLang = isset($_REQUEST["vGeneralLang"]) ? $_REQUEST["vGeneralLang"] : '';
        //$vLanguage = "";
        if (!empty($vGeneralLang)) {
            $vLanguage = $vGeneralLang;
        }
        if ($vLanguage == "" || $vLanguage == NULL) {
            //Added By HJ On 14-07-2020 For Optimize language_master Table Query Start
            $vLanguage = $LANG_OBJ->FetchDefaultLangData("vCode");
            //Added By HJ On 14-07-2020 For Optimize language_master Table Query End
        }
        $ssqllast = " `company`.`iCompanyId` ASC ";
        $Restaurant_Cuisine_Id_Arr = $db_cuisine_list = $db_cuisine_list_new = $db_cuisine_list_new1 = $languageLabelsArr = array();
        $Restaurant_Cuisine_Id_str = "";
        $sql = "SELECT DISTINCT (company.iCompanyId),ROUND(( 6371 * acos( cos( radians(" . $passengerLat . ") ) * cos( radians( vRestuarantLocationLat ) ) * cos( radians( vRestuarantLocationLong ) - radians(" . $passengerLon . ") ) + sin( radians(" . $passengerLat . ") ) * sin( radians( vRestuarantLocationLat ) ) ) ),2) AS distance, company.iCompanyId FROM `company` WHERE vRestuarantLocationLat != '' AND vRestuarantLocationLong != '' AND eStatus='Active' AND eSystem = 'DeliverAll' AND iServiceId = '" . $iServiceId . "' HAVING distance < " . $LIST_RESTAURANT_LIMIT_BY_DISTANCE . " ORDER BY " . $ssqllast . "";
        $cData = $obj->MySQLSelect($sql);
        $storeIdArr = array();
        for ($r = 0; $r < count($cData); $r++) {
            $storeIdArr[] = $cData[$r]['iCompanyId'];
        }
        //Added By HJ On 13-07-2020 For langauge labele and Other Union Table Query Start
        if (isset($languageLabelDataArr['language_label_union_other_food_' . $vLanguage . "_" . $iServiceId])) {
            $languageLabelsArr = $languageLabelDataArr['language_label_union_other_food_' . $vLanguage . "_" . $iServiceId];
        }
        else {
            $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLanguage, "1", $iServiceId);
            $languageLabelDataArr['language_label_union_other_food_' . $vLanguage . "_" . $iServiceId] = $languageLabelsArr;
        }
        //Added By HJ On 13-07-2020 For langauge labele and Other Union Table Query End
        $allStoreData = getcuisinelist($storeIdArr, $iUserId, $languageLabelsArr, $iServiceId);
        $Data_Company = $allStoreData['companyCuisineArr'];
        $offerMsgArr = $allStoreData['offerMsgArr'];
        $companyCuisineIdArr = $allStoreData['companyCuisineIdArr'];
        //$Data_Company = FetchNearByStores($passengerLat, $passengerLon, $iUserId, "No", "", "", $iServiceId);
        $isOfferApply = "No";
        if (count($Data_Company) > 0) {
            foreach ($Data_Company as $companyId => $cuisinArr) {
                $Restaurant_OfferMessage = "";
                if (isset($offerMsgArr[$companyId]['Restaurant_OfferMessage'])) {
                    $Restaurant_OfferMessage = trim($offerMsgArr[$companyId]['Restaurant_OfferMessage']);
                }
                $restCuisineArr = array();
                if (isset($companyCuisineIdArr[$companyId])) {
                    $restCuisineArr = $companyCuisineIdArr[$companyId];
                }
                if ($Restaurant_OfferMessage != "") {
                    $isOfferApply = "Yes";
                }
                for ($d = 0; $d < count($restCuisineArr); $d++) {
                    $Restaurant_Cuisine_Id_str .= $restCuisineArr[$d] . ",";
                }
            }
            //$Restaurant_Cuisine_Id_str = substr($Restaurant_Cuisine_Id_str, 0, -1);
            $Restaurant_Cuisine_Id_str = trim($Restaurant_Cuisine_Id_str, ",");
            $Restaurant_Cuisine_Id_Arr = explode(",", $Restaurant_Cuisine_Id_str);
        }
        $Restaurant_Cuisine_Id_Arr = array_unique($Restaurant_Cuisine_Id_Arr);
        //added by SP vImage for cubex on 12-10-2019
        $defaultImage = $tconfig["tsite_url"] . "webimages/upload/DefaultImg/food_service.png";
        //Added By HJ On 31-10-2019 For Set Cuisine Default Icon as Per Service Category Start Bug - 1382 141 Mantis
        if ($iServiceId != 1) {
            $defaultImage = $tconfig["tsite_url"] . "webimages/upload/DefaultImg/other_services.png";
        }
        //Added By HJ On 31-10-2019 For Set Cuisine Default Icon as Per Service Category End Bug - 1382 141 Mantis
        //$sql = "SELECT cuisineId,cuisineName_" . $vLanguage . " as cuisineName,eStatus,CONCAT('aa',vImage) as vImage  FROM cuisine WHERE iServiceId = '" . $iServiceId . "' AND eStatus = 'Active' ORDER BY cuisineName ASC";
        $sql = "SELECT cuisineId,cuisineName_$vLanguage as cuisineName,eStatus,CASE WHEN vImage != '' THEN CONCAT('" . $tconfig['tsite_upload_images_menu_item_type'] . "/',vImage) ELSE '" . $defaultImage . "' END AS vImage,vBgColor,vTextColor,vBorderColor FROM cuisine WHERE iServiceId = '" . $iServiceId . "' AND eStatus = 'Active' AND eDefault = 'No' ORDER BY iDisplayOrder ASC";
        $db_cuisine_list = $obj->MySQLSelect($sql);
        if (count($db_cuisine_list) > 0) {
            for ($i = 0; $i < count($db_cuisine_list); $i++) {
                $isRemoveCuisineList = "No";
                $cuisineId = $db_cuisine_list[$i]['cuisineId'];

                $vBgColor = RANDOM_COLORS_ARR[array_rand(RANDOM_COLORS_ARR, 1)];
                $vTextColor = $vBorderColor = RANDOM_COLORS_ARR[array_rand(RANDOM_COLORS_ARR, 1)];
                $db_cuisine_list[$i]['vBgColor'] = !empty($db_cuisine_list[$i]['vBgColor']) ? $db_cuisine_list[$i]['vBgColor'] : $vBgColor;
                $db_cuisine_list[$i]['vTextColor'] = !empty($db_cuisine_list[$i]['vTextColor']) ? $db_cuisine_list[$i]['vTextColor'] : $vTextColor;
                $db_cuisine_list[$i]['vBorderColor'] = !empty($db_cuisine_list[$i]['vBorderColor']) ? $db_cuisine_list[$i]['vBorderColor'] : $vBorderColor;
                if (!in_array($cuisineId, $Restaurant_Cuisine_Id_Arr)) {
                    $isRemoveCuisineList = "Yes";
                }
                if ($isRemoveCuisineList == "Yes") {
                    unset($db_cuisine_list[$i]);
                }
            }
        }

        //added by SP for cubex to add all in cuisine list so when click on it show all restaurant on 15-10-2019
        $default_cuisine = $obj->MySQLSelect("SELECT cuisineId, cuisineName_$vLanguage as cuisineName, eStatus, vImage,vBgColor,vTextColor,vBorderColor FROM cuisine WHERE iServiceId = '$iServiceId' AND eDefault = 'Yes' ");
        if (isset($default_cuisine) && !empty($default_cuisine)) {
            $vBgColor = RANDOM_COLORS_ARR[array_rand(RANDOM_COLORS_ARR, 1)];
            $vTextColor = $vBorderColor = RANDOM_COLORS_ARR[array_rand(RANDOM_COLORS_ARR, 1)];
            $db_cuisine_list_new1[0]['cuisineId'] = '';
            $db_cuisine_list_new1[0]['cuisineName'] = $default_cuisine[0]['cuisineName'];
            $db_cuisine_list_new1[0]['eStatus'] = $default_cuisine[0]['eStatus'];
            $db_cuisine_list_new1[0]['vImage'] = $tconfig['tsite_upload_images_menu_item_type'] . '/' . $default_cuisine[0]['vImage'];
            $db_cuisine_list_new1[0]['vBgColor'] = !empty($default_cuisine[0]['vBgColor']) ? $default_cuisine[0]['vBgColor'] : $vBgColor;
            $db_cuisine_list_new1[0]['vTextColor'] = !empty($default_cuisine[0]['vTextColor']) ? $default_cuisine[0]['vTextColor'] : $vTextColor;
            $db_cuisine_list_new1[0]['vBorderColor'] = !empty($default_cuisine[0]['vBorderColor']) ? $default_cuisine[0]['vBorderColor'] : $vBorderColor;
        }
        //$allCuisines = count($db_cuisine_list_new);
        $db_cuisine_list_new = array_merge($db_cuisine_list_new1, $db_cuisine_list);
        $db_cuisine_list_new = array_values($db_cuisine_list_new);
        $db_cuisine_list = $db_cuisine_list_new;
        if (count($db_cuisine_list) == 0) {
            $db_cuisine_list = "";
        }
        $getItemData = "";
        //Added By HJ On 13-10-2020 For Get Item List For Item wise Search Functionality Start
        if ($MODULES_OBJ->isEnableItemSearchStoreOrder() > 0) {
            $searchWord = "";
        }
        //Added By HJ On 13-10-2020 For Get Item List For Item wise Search Functionality End
        $countryArr['Action'] = "1";
        $countryArr['totalValues'] = count($db_cuisine_list);
        $countryArr['isOfferApply'] = $isOfferApply;
        $countryArr['CuisineList'] = $db_cuisine_list;
        $returnArr['getCuisineList'] = $countryArr;
        $returnArr['itemData'] = $getItemData;
        //getCuisineList type end

        $returnArr['iServiceId'] = $iServiceId;
        $returnArrTmp = $returnArr;
        $returnArrFinal['message'][] = $returnArrTmp;
    }
    
    $returnArrFinal['Action'] = "1";
    // echo "<pre>"; print_r($returnArrFinal); exit;
    setDataResponse($returnArrFinal);
}

$obj->MySQLClose();
?>