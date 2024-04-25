<?php

function getDriverDetailInfo($driverId, $fromSignIN = 0)
{
    if (strtoupper(ONLYDELIVERALL) == "YES") {
        return getDriverDetailInfoDeliverAll($driverId, $fromSignIN);
    } else {
        return getDriverDetailInfoGeneral($driverId, $fromSignIN);
    }
}


function getDriverDetailInfoGeneral($driverId, $fromSignIN = 0)
{
    global $CONFIG_OBJ, $GIFT_CARD_OBJ, $obj, $demo_site_msg, $PHOTO_UPLOAD_SERVICE_ENABLE, $parent_ufx_catid, $generalSystemConfigDataArr, $vTimeZone, $tconfig, $vUserDeviceCountry, $intervalmins, $generalConfigPaymentArr, $POOL_ENABLE, $ENABLE_DRIVER_ADVERTISEMENT_BANNER, $ADVERTISEMENT_TYPE, $APP_TYPE, $_REQUEST, $MAX_DRIVER_DESTINATIONS, $isUfxAvailable, $vSystemDefaultCurrencyName, $vSystemDefaultCurrencySymbol, $generalTripRatingDataArr, $userAddressDataArr, $country_data_retrieve, $country_data_arr, $Data_ALL_currency_Arr, $driverVehicleDataArr, $currencyAssociateArr, $ENABLE_OTHER_CHARGES_FEATURE, $ENABLE_MANUAL_TOLL_FEATURE, $APP_PAYMENT_METHOD, $MODULES_OBJ, $WALLET_OBJ, $DRIVER_REWARD_OBJ, $LANG_OBJ, $iServiceId, $BIDDING_OBJ, $TRACK_SERVICE_OBJ, $RIDE_SHARE_OBJ;

    if (strtoupper(PACKAGE_TYPE) == "SHARK") {
        getDriverPoolTrips($driverId);
    }

    ChangeDriverVehicleRideDeliveryFeatureDisable($driverId);
    $where = " iDriverId = '" . $driverId . "'";
    $data_version['iAppVersion'] = "2";
    $data_version['eLogout'] = 'No';
    $data_version['eDebugMode'] = isset($_REQUEST["IS_DEBUG_MODE"]) ? $_REQUEST["IS_DEBUG_MODE"] : "";
    $data_version['tApiFileName'] = pathinfo(__FILE__, PATHINFO_FILENAME);
    #################################### Generate Session For GeoAPI ########################################
    generateSessionForGeo($driverId, "Driver");
    #################################### Generate Session For GeoAPI ########################################
    #################################### Configure App Version Info ########################################
    $arr_app_version = array();
    $arr_app_version['AppVersionName'] = isset($_REQUEST['GeneralAppVersion']) ? $_REQUEST['GeneralAppVersion'] : "";
    $arr_app_version['AppVersionCode'] = isset($_REQUEST['GeneralAppVersionCode']) ? $_REQUEST['GeneralAppVersionCode'] : "";
    #################################### Configure App Version Info ########################################
    $data_version['tVersion'] = strval(json_encode($arr_app_version));
    $data_version['tDeviceData'] = isset($_REQUEST['DEVICE_DATA']) ? $_REQUEST['DEVICE_DATA'] : "";
    $obj->MySQLQueryPerform("register_driver", $data_version, 'update', $where);
    $updateQuery = "UPDATE trip_status_messages SET eReceived='Yes' WHERE iDriverId='" . $driverId . "' AND eToUserType='Driver'";
    $obj->sql_query($updateQuery);
    $returnArr = array();
    // $sql = "SELECT rd.*,cmp.eSystem,cmp.eStatus as cmpEStatus,(SELECT dv.vLicencePlate From driver_vehicle as dv WHERE rd.iDriverVehicleId != '' AND rd.iDriverVehicleId !='0' AND dv.iDriverVehicleId = rd.iDriverVehicleId) as vLicencePlateNo,rd.iDestinationCount FROM `register_driver` as rd,`company` as cmp WHERE rd.iDriverId='$driverId' AND cmp.iCompanyId=rd.iCompanyId";
    $sql = "SELECT rd.*,cmp.eSystem,cmp.eStatus as cmpEStatus,(SELECT dv.vLicencePlate From driver_vehicle as dv WHERE rd.iDriverVehicleId != '' AND rd.iDriverVehicleId !='0' AND dv.iDriverVehicleId = rd.iDriverVehicleId) as vLicencePlateNo,rd.iDestinationCount FROM `register_driver` as rd LEFT JOIN `company` as cmp ON cmp.iCompanyId = rd.iCompanyId WHERE rd.iDriverId='$driverId' ";
    //$sql = "SELECT rd.* FROM `register_driver` as rd WHERE rd.iDriverId='$driverId'";
    $Data = $obj->MySQLSelect($sql);
    $Data[0]['eSystem_original'] = $Data[0]['eSystem'];
    if (count($Data) > 0) {
        if ($MODULES_OBJ->checkDriverDestinationModule()) {
            $iDestinationCount = $Data[0]['iDestinationCount'];
            if ($iDestinationCount >= $MAX_DRIVER_DESTINATIONS) {
                $Data[0]['DRIVER_DESTINATION_AVAILABLE'] = 'No';
            } else {
                $Data[0]['DRIVER_DESTINATION_AVAILABLE'] = 'Yes';
            }
        }
        //Added By HJ On 17-06-2020 For Optimization currency Table Query Start
        if (!empty($vSystemDefaultCurrencyName) && !empty($vSystemDefaultCurrencySymbol)) {
            $Data[0]['vFlutterwaveCurrency'] = $vSystemDefaultCurrencyName;
            $vFlutterwavevSymbol = $vSystemDefaultCurrencySymbol;
        } else {
            $defaultCurrencyDataArr = get_value('currency', 'vName,vSymbol', 'eDefault', 'Yes');
            /* Added By PM On 09-12-2019 For Flutterwave Code Start */
            $Data[0]['vFlutterwaveCurrency'] = $defaultCurrencyDataArr[0]['vName'];
            $vFlutterwavevSymbol = $defaultCurrencyDataArr[0]['vSymbol'];
            /* Added By PM On 09-12-2019 For Flutterwave Code End */
        }
        //Added By HJ On 17-06-2020 For Optimization currency Table Query End
        $page_link = $tconfig['tsite_url'] . "sign-up.php?UserType=Driver&vRefCode=" . $Data[0]['vRefCode'];
        $link = get_tiny_url($page_link);
        //$activation_text = '<a href="'.$link.'" target="_blank"> '.$link.' </a>';
        $activation_text = "<a href='" . $link . "' target='_blank'> '" . $link . "' </a>";
        $vLanguage = $Data[0]['vLang'];
        if ($vLanguage == "" || $vLanguage == NULL) {
            //Added By HJ On 17-06-2020 For Optimize language_master Table Query Start
            $vLanguage = $LANG_OBJ->FetchDefaultLangData("vCode");
            //Added By HJ On 17-06-2020 For Optimize language_master Table Query End
        }
        $langLabels = $LANG_OBJ->FetchLanguageLabels($vLanguage, "1", $iServiceId);
        if (isset($langLabels['LBL_SHARE_CONTENT_DRIVER']) && trim($langLabels['LBL_SHARE_CONTENT_DRIVER']) != "") {
            $LBL_SHARE_CONTENT_DRIVER = $langLabels['LBL_SHARE_CONTENT_DRIVER'];
        } else {
            $db_label = $obj->MySQLSelect("SELECT vValue FROM `language_label` WHERE vLabel = 'LBL_SHARE_CONTENT_DRIVER' AND vCode = '" . $vLanguage . "'");
            $LBL_SHARE_CONTENT_DRIVER = $db_label[0]['vValue'];
        }
        $Data[0]['INVITE_SHARE_CONTENT'] = $LBL_SHARE_CONTENT_DRIVER . " " . $link;

        //Added By HJ On 09-07-2020 For Optimize currency Table Query Start
        $vCurrencyDriver = $Data[0]['vCurrencyDriver'];
        if(isset($currencyAssociateArr[$vCurrencyDriver])){
            $driverCurrencyRatio = $currencyAssociateArr[$vCurrencyDriver]['Ratio'];
            $driverCurrencySymbol = $currencyAssociateArr[$vCurrencyDriver]['vSymbol'];
        }else{
            $driverCurrencyData = $obj->MySQLSelect("SELECT vSymbol,vName,Ratio FROM currency WHERE vName='".$vCurrencyDriver."'");
            $driverCurrencySymbol= $driverCurrencyData[0]['vSymbol'];
            $driverCurrencyRatio = $driverCurrencyData[0]['Ratio'];
        }
        //Added By HJ On 09-07-2020 For Optimize currency Table Query End

        foreach ($generalSystemConfigDataArr as $key => $value) {
            if (is_null($generalSystemConfigDataArr[$key]) || empty($generalSystemConfigDataArr[$key])) {
                $generalSystemConfigDataArr[$key] = "";
            }
            if ($APP_TYPE == 'Delivery' && $key == "ENABLE_DRIVER_DESTINATIONS") {
                $generalSystemConfigDataArr[$key] = "No";
            }

            if(in_array($key, ["WALLET_FIXED_AMOUNT_1", "WALLET_FIXED_AMOUNT_2", "WALLET_FIXED_AMOUNT_3"])) {
                $generalSystemConfigDataArr[$key] = round($value * $driverCurrencyRatio);
            }
        }
        $Data[0] = array_merge($Data[0], $generalSystemConfigDataArr); // Added By HJ On 18-03-2020 For Optimized Function
        if ($_REQUEST['APP_TYPE'] != "") {
            $Data[0]['APP_TYPE'] = $_REQUEST['APP_TYPE'];
        }
        $checkEditProfileStatus = getEditDriverProfileStatus($Data[0]['eStatus']); // Added By HJ On 13-11-2019 For Check Driver Profile Edit Status As Per Discuss With KS Sir
        $Data[0]['ENABLE_EDIT_DRIVER_PROFILE'] = $checkEditProfileStatus;
        $Data[0]['PACKAGE_TYPE'] = strtoupper(PACKAGE_TYPE);
        $Data[0]['GOOGLE_ANALYTICS'] = "";
        $Data[0]['SERVER_MAINTENANCE_ENABLE'] = $Data[0]['MAINTENANCE_APPS'];
        //Added By HJ On 16-07-2019 For Check Empty and # Value Of Configuration Start
        if (isset($Data[0]['LIVE_CHAT_LICENCE_NUMBER']) && ($Data[0]['LIVE_CHAT_LICENCE_NUMBER'] == "" || strpos($Data[0]['LIVE_CHAT_LICENCE_NUMBER'], '#') !== false)) {
            $Data[0]['ENABLE_LIVE_CHAT'] = "No";
        }

        $usercountrycode = "";
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
        $DRIVER_EMAIL_VERIFICATION = $Data[0]["DRIVER_EMAIL_VERIFICATION"];
        $DRIVER_PHONE_VERIFICATION = $Data[0]["DRIVER_PHONE_VERIFICATION"];
        if ($Data[0]['MULTI_LEVEL_REFERRAL_SCHEME_ENABLE'] == "Yes") {
            //added by SP on 03-03-2021 when multilevel then only shows text not amt..becoz it will not be calculated here..
            if (isset($langLabels['LBL_REFERRAL_AMOUNT']) && trim($langLabels['LBL_REFERRAL_AMOUNT']) != "") {
                $LBL_REFERRAL_AMOUNT = $langLabels['LBL_REFERRAL_AMOUNT'];
            } else {
                $LBL_REFERRAL_AMOUNT = get_value('language_label', 'vValue', 'vLabel', 'LBL_REFERRAL_AMOUNT', " and vCode='" . $vLanguage . "'", 'true');
            }
            $REFERRAL_AMOUNT_USER = $LBL_REFERRAL_AMOUNT;
        } else {
            $REFERRAL_AMOUNT = $Data[0]["REFERRAL_AMOUNT"];
            $REFERRAL_AMOUNT_ARR = getPriceUserCurrency($driverId, "Driver", $REFERRAL_AMOUNT);
            $REFERRAL_AMOUNT_USER = $REFERRAL_AMOUNT_ARR['fPricewithsymbol'];
        }
        if (isset($langLabels['LBL_INVITE_FRIEND_SHARE_PREFIX_TXT']) && trim($langLabels['LBL_INVITE_FRIEND_SHARE_PREFIX_TXT']) != "") {
            $LBL_INVITE_FRIEND_SHARE_PREFIX_TXT = $langLabels['LBL_INVITE_FRIEND_SHARE_PREFIX_TXT'];
        } else {
            $LBL_INVITE_FRIEND_SHARE_PREFIX_TXT = get_value('language_label', 'vValue', 'vLabel', 'LBL_INVITE_FRIEND_SHARE_PREFIX_TXT', " and vCode='" . $vLanguage . "'", 'true');
        }
        if (isset($langLabels['LBL_INVITE_FRIEND_SHARE_POSTFIX_TXT']) && trim($langLabels['LBL_INVITE_FRIEND_SHARE_POSTFIX_TXT']) != "") {
            $LBL_INVITE_FRIEND_SHARE_POSTFIX_TXT = $langLabels['LBL_INVITE_FRIEND_SHARE_POSTFIX_TXT'];
        } else {
            $LBL_INVITE_FRIEND_SHARE_POSTFIX_TXT = get_value('language_label', 'vValue', 'vLabel', 'LBL_INVITE_FRIEND_SHARE_POSTFIX_TXT', " and vCode='" . $vLanguage . "'", 'true');
        }
        if (isset($langLabels['LBL_INVITE_FRIEND_SHARE_POSTFIX_ORDER_TXT']) && trim($langLabels['LBL_INVITE_FRIEND_SHARE_POSTFIX_ORDER_TXT']) != "") {
            $LBL_INVITE_FRIEND_SHARE_POSTFIX_ORDER_TXT = $langLabels['LBL_INVITE_FRIEND_SHARE_POSTFIX_ORDER_TXT'];
        } else {
            $LBL_INVITE_FRIEND_SHARE_POSTFIX_ORDER_TXT = get_value('language_label', 'vValue', 'vLabel', 'LBL_INVITE_FRIEND_SHARE_POSTFIX_ORDER_TXT', " and vCode='" . $vLanguage . "'", 'true');
        }
        $Data[0]['INVITE_DESCRIPTION_CONTENT'] = $LBL_INVITE_FRIEND_SHARE_PREFIX_TXT . " " . $REFERRAL_AMOUNT_USER . " " . $LBL_INVITE_FRIEND_SHARE_POSTFIX_TXT;
        if ($DRIVER_EMAIL_VERIFICATION == 'No') {
            $Data[0]['eEmailVerified'] = "Yes";
        }
        /*for email optional*/
        if ($Data[0]['vEmail'] == "") {
            $Data[0]['eEmailVerified'] = "Yes";
        }
        if ($DRIVER_PHONE_VERIFICATION == 'No') {
            $Data[0]['ePhoneVerified'] = "Yes";
        }
        $lang_usr = $Data[0]['vLang'];
        $sql = "select vBody_$lang_usr as Message from send_message_templates where vEmail_Code = 'VERIFICATION_CODE_MESSAGE'";
        $data_SMS = $obj->MySQLSelect($sql);
        $Data[0]['SMS_BODY'] = $data_SMS[0]['Message'];
        ## Check and vWorkLocationRadius For UberX ##
        $eUnit = getMemberCountryUnit($driverId, "Driver");
        $Data[0]['eUnit'] = $eUnit;
        if ($Data[0]['vWorkLocationRadius'] == "" || $Data[0]['vWorkLocationRadius'] == "0" || $Data[0]['vWorkLocationRadius'] == 0) {
            $vWorkLocationRadius = $Data[0]['RESTRICTION_KM_NEAREST_TAXI'];
            $Update_Driver_radius['vWorkLocationRadius'] = $vWorkLocationRadius;
            $obj->MySQLQueryPerform("register_driver", $Update_Driver_radius, 'update', $where);
            $Data[0]['vWorkLocationRadius'] = $vWorkLocationRadius;
            if ($eUnit == "Miles") {
                $Data[0]['vWorkLocationRadius'] = round($vWorkLocationRadius * 0.621371, 2);
            } else {
                $Data[0]['vWorkLocationRadius'] = $vWorkLocationRadius;
            }
        } else {
            $vWorkLocationRadius = $Data[0]['vWorkLocationRadius'];
            $Data[0]['vWorkLocationRadius'] = $vWorkLocationRadius;
            if ($eUnit == "Miles") {
                $Data[0]['vWorkLocationRadius'] = round($vWorkLocationRadius * 0.621371, 2);
            }
        }
        ## Display Braintree Charge Message ##
        /*if (isset($langLabels['LBL_BRAINTREE_CHARGE_MSG_TXT']) && trim($langLabels['LBL_BRAINTREE_CHARGE_MSG_TXT']) != "") {
            $LBL_BRAINTREE_CHARGE_MSG_TXT = $langLabels['LBL_BRAINTREE_CHARGE_MSG_TXT'];
        } else {
            $db_label_braintree = $obj->MySQLSelect("SELECT * FROM `language_label` WHERE vLabel = 'LBL_BRAINTREE_CHARGE_MSG_TXT' AND vCode = '" . $vLanguage . "'");
            $LBL_BRAINTREE_CHARGE_MSG_TXT = $db_label_braintree[0]['vValue'];
        }
        $BRAINTREE_CHARGE_AMOUNT = $Data[0]['BRAINTREE_CHARGE_AMOUNT'];
        $BRAINTREE_CHARGE_AMOUNT_USER_ARR = getPriceUserCurrency($driverId, "Driver", $BRAINTREE_CHARGE_AMOUNT);
        $BRAINTREE_CHARGE_AMOUNT_USER = $BRAINTREE_CHARGE_AMOUNT_USER_ARR['fPricewithsymbol'];
        //$msg = str_replace('##AMOUNT##', $BRAINTREE_CHARGE_AMOUNT_USER, $LBL_BRAINTREE_CHARGE_MSG_TXT);
        $msg = str_replace('####', $BRAINTREE_CHARGE_AMOUNT_USER, $LBL_BRAINTREE_CHARGE_MSG_TXT);
        $Data[0]['BRAINTREE_CHARGE_MESSAGE'] = $msg;
        ## Display Braintree Charge Message ##
        ## Display Adyen Charge Message ##
        if (isset($langLabels['LBL_ADYEN_CHARGE_MSG_TXT']) && trim($langLabels['LBL_ADYEN_CHARGE_MSG_TXT']) != "") {
            $LBL_ADYEN_CHARGE_MSG_TXT = $langLabels['LBL_ADYEN_CHARGE_MSG_TXT'];
        } else {
            $db_label_adyen = $obj->MySQLSelect("SELECT * FROM `language_label` WHERE vLabel = 'LBL_ADYEN_CHARGE_MSG_TXT' AND vCode = '" . $vLanguage . "'");
            $LBL_ADYEN_CHARGE_MSG_TXT = $db_label_adyen[0]['vValue'];
        }
        $ADEYN_CHARGE_AMOUNT = $Data[0]['ADYEN_CHARGE_AMOUNT'];
        $ADEYN_CHARGE_AMOUNT_USER_ARR = getPriceUserCurrency($driverId, "Driver", $ADEYN_CHARGE_AMOUNT);
        $ADEYN_CHARGE_AMOUNT_USER = $ADEYN_CHARGE_AMOUNT_USER_ARR['fPricewithsymbol'];
        $msg = str_replace('####', $ADEYN_CHARGE_AMOUNT_USER, $LBL_ADYEN_CHARGE_MSG_TXT);
        $Data[0]['ADEYN_CHARGE_MESSAGE'] = $msg;*/
        ## Display Adyen Charge Message ##
        /* Added By PM On 09-12-2019 For Flutterwave Code Start */
        ## Display Flutterwave Charge Message ##
        $FLUTTERWAVE_CHARGE_AMOUNT = $generalConfigPaymentArr['FLUTTERWAVE_CHARGE_AMOUNT'];
        if (isset($langLabels['LBL_FLUTTERWAVE_CHARGE_MSG_TXT']) && trim($langLabels['LBL_FLUTTERWAVE_CHARGE_MSG_TXT']) != "") {
            $LBL_FLUTTERWAVE_CHARGE_MSG_TXT = $langLabels['LBL_FLUTTERWAVE_CHARGE_MSG_TXT'];
        } else {
            $db_label_flutter = $obj->MySQLSelect("SELECT * FROM `language_label` WHERE vLabel = 'LBL_FLUTTERWAVE_CHARGE_MSG_TXT' AND vCode = '" . $vLanguage . "'");
            $LBL_FLUTTERWAVE_CHARGE_MSG_TXT = $db_label_flutter[0]['vValue'];
        }
        $amountDataArr = getSupportedCurrencyAmt($Data[0]['FLUTTERWAVE_CHARGE_AMOUNT'], $Data[0]['vFlutterwaveCurrency']);
        $Data[0]['vFlutterwaveCurrency'] = $amountDataArr['CURRENCY_CODE'];
        $FLUTTERWAVE_CHARGE_AMOUNT = $amountDataArr['AMOUNT'];
        //$FLUTTERWAVE_CHARGE_AMOUNT_USER = $vFlutterwavevSymbol . $FLUTTERWAVE_CHARGE_AMOUNT;
        $FLUTTERWAVE_CHARGE_AMOUNT_USER = formateNumAsPerCurrency($FLUTTERWAVE_CHARGE_AMOUNT, $Data[0]['vFlutterwaveCurrency']);
        $Data[0]['FLUTTERWAVE_CHARGE_AMOUNT'] = $FLUTTERWAVE_CHARGE_AMOUNT;
        $msg = str_replace('####', $FLUTTERWAVE_CHARGE_AMOUNT_USER, $LBL_FLUTTERWAVE_CHARGE_MSG_TXT);
        $Data[0]['FLUTTERWAVE_CHARGE_MESSAGE'] = $msg;
        /* Added By PM On 09-12-2019 For Flutterwave Code End */
        ## Check and update Device Session ID ##
        if ($Data[0]['tDeviceSessionId'] == "") {
            $random = substr(md5(rand()), 0, 7);
            $Update_Device_Session['tDeviceSessionId'] = session_id() . time() . $random;
            $Update_Device_Session_id = $obj->MySQLQueryPerform("register_driver", $Update_Device_Session, 'update', $where);
            $Data[0]['tDeviceSessionId'] = $Update_Device_Session['tDeviceSessionId'];
        }
        ## Check and update Device Session ID ##
        ## Check and update Session ID ##
        if ($Data[0]['tSessionId'] == "") {
            $Update_Session['tSessionId'] = session_id() . time();
            $Update_Session_id = $obj->MySQLQueryPerform("register_driver", $Update_Session, 'update', $where);
            $Data[0]['tSessionId'] = $Update_Session['tSessionId'];
        }
        ## Check and update Session ID ##
        $Data[0]['Driver_Password_decrypt'] = "";
        if ($Data[0]['vImage'] != "" && $Data[0]['vImage'] != "NONE") {
            $Data[0]['vImage'] = "3_" . $Data[0]['vImage'];
        }
        if (($Data[0]['iDriverVehicleId'] == '' || $Data[0]['iDriverVehicleId'] == NULL) && $Data[0]['APP_TYPE'] != "Ride-Delivery-UberX") {
            $Data_vehicle = $obj->MySQLSelect("SELECT iDriverVehicleId,vLicencePlate FROM  driver_vehicle WHERE `eStatus` = 'Active' AND `iDriverId` = '" . $driverId . "'");
            $iDriver_VehicleId = $Data_vehicle[0]['iDriverVehicleId'];
            $vLicencePlate = $Data_vehicle[0]['vLicencePlate'];
            $obj->sql_query("UPDATE register_driver set iDriverVehicleId='" . $iDriver_VehicleId . "' WHERE iDriverId='" . $driverId . "'");
            $Data[0]['iDriverVehicleId'] = $iDriver_VehicleId;
            //$vLicencePlate = get_value('driver_vehicle', 'vLicencePlate', 'iDriverVehicleId', $iDriver_VehicleId, '', 'true');
            $Data[0]['vLicencePlateNo'] = $vLicencePlate;
        }
        if ($Data[0]['iDriverVehicleId'] != '' && $Data[0]['iDriverVehicleId'] != '0') {
            //Added By HJ On 17-06-2020 For Optimize driver_vehicle Table Query Start
            if (isset($driverVehicleDataArr['driver_vehicle_' . $Data[0]['iDriverVehicleId']])) {
                $DriverVehicle = $driverVehicleDataArr['driver_vehicle_' . $Data[0]['iDriverVehicleId']];
            } else {
                $DriverVehicle = $obj->MySQLSelect("SELECT ma.vMake,mo.vTitle,dv.* FROM driver_vehicle as dv LEFT JOIN make as ma ON dv.iMakeId = ma.iMakeId LEFT JOIN model as mo ON dv.iModelId = mo.iModelId WHERE dv.iDriverVehicleId = '" . $Data[0]['iDriverVehicleId'] . "'");
                $driverVehicleDataArr['driver_vehicle_' . $Data[0]['iDriverVehicleId']] = $DriverVehicle;
            }
            //Added By HJ On 17-06-2020 For Optimize driver_vehicle Table Query End
            $Data[0]['vMake'] = $DriverVehicle[0]['vMake'];
            $Data[0]['vModel'] = $DriverVehicle[0]['vTitle'];
            $vLicencePlate = $DriverVehicle[0]['vLicencePlate'];
            // added
            //$vLicencePlate = get_value('driver_vehicle', 'vLicencePlate', 'iDriverVehicleId', $Data[0]['iDriverVehicleId'], '', 'true');
            $Data[0]['vLicencePlateNo'] = $vLicencePlate;
        }
        $Data[0]['isOnlyUfxServicesSelected'] = 'No';//added by SP on 08-09-2020 chk whether ride or any other service is not selected..so in app select vehicle line in home screen will be disabled.
        $sqlUFXVehicle = "SELECT make.vMake, model.vTitle, dv.*, rd.iDriverVehicleId as iSelectedVehicleId,rd.iDestinationCount,rd.tDestinationModifiedDate,rd.tOnline FROM `driver_vehicle` dv, make, model, register_driver as rd WHERE dv.iDriverId='$driverId' AND rd.iDriverId='$driverId' AND dv.`iMakeId` = make.`iMakeId` AND dv.`iModelId` = model.`iModelId` AND dv.`eStatus`='Active'";
        $DriverUFXVehicle = $obj->MySQLSelect($sqlUFXVehicle);
        if (count($DriverUFXVehicle) == 1 && $DriverUFXVehicle[0]['eType'] == "UberX" && $DriverUFXVehicle[0]['vCarType'] != "" && $isUfxAvailable == 'Yes') {
            $Data[0]['isOnlyUfxServicesSelected'] = 'Yes';
        }
        if ($APP_TYPE == "UberX") {
            $Data[0]['isOnlyUfxServicesSelected'] = 'Yes';
        }
        if ($Data[0]['eStatus'] == "Deleted") {
            $returnArr['Action'] = "0";
            $returnArr['RESTRICT_APP'] = "Yes";
            $returnArr['message'] = "LBL_ACC_DELETE_TXT";
            $returnArr['message_title'] = "LBL_ACC_DELETE_TITLE";
            $returnArr['eStatus'] = $Data[0]['eStatus'];
            $returnArr['isAccountDeleted'] = "Yes";

            setDataResponse($returnArr);
        }
        $TripStatus = $Data[0]['vTripStatus'];
        //$Data[0]['RegistrationDate'] = date("Y-m-d", strtotime($Data[0]['tRegistrationDate'] . ' -1 day '));
        $Data[0]['RegistrationDate'] = date("Y-m-d", strtotime($Data[0]['tRegistrationDate']));
        if ($TripStatus != "NONE") {
            if ($MODULES_OBJ->isEnableBiddingServices() && !empty($Data[0]['vTaskStatus']) && $Data[0]['vTaskStatus'] != "NONE") {
                $Data[0]['TaskDetails'] = $BIDDING_OBJ->getTaskDetails($Data[0]['iBiddingPostId'], $vLanguage);
            } else {
                $TripID = $Data[0]['iTripId'];
                $row_result_trips = FetchTripFareDetails($TripID, $driverId, "Driver");
                //Added By HJ On 29-08-2020 For Manual Toll and Extra Charge Related Changes Start
                unset($row_result_trips['DriverDetails']['tLocationUpdateDate']);
                unset($row_result_trips['PassengerDetails']['tLocationUpdateDate']);
                if ($TripStatus == "On Going Trip") {
                    $row_result_trips['eAcceptTripRequest'] = "Yes";
                } else {
                    $row_result_trips['eAcceptTripRequest'] = "No";
                }
                $eFlyEnable = 1;
                if ($row_result_trips['iFromStationId'] > 0 && $row_result_trips['iToStationId'] > 0) {
                    $eFlyEnable = 0;
                }
                if (strtoupper($row_result_trips['eFareGenerated']) == "NO" && strtoupper($row_result_trips['eServiceEnd']) == "YES" && strtoupper($row_result_trips['eVerifyTollCharges']) == "NO" && strtoupper($row_result_trips['eType']) == "RIDE" && strtoupper($row_result_trips['eApproved']) != "YES" && strtoupper($row_result_trips['ePoolRide']) == "NO" && $eFlyEnable > 0 && (strtoupper($ENABLE_MANUAL_TOLL_FEATURE) == 'YES' || strtoupper($ENABLE_OTHER_CHARGES_FEATURE) == "YES")) {
                    $row_result_trips['eVerifyTollCharges'] = "Yes";
                }
                if (strtoupper($row_result_trips['eApproved']) != "YES" && strtoupper($ENABLE_MANUAL_TOLL_FEATURE) != "YES" && strtoupper($ENABLE_OTHER_CHARGES_FEATURE) != "YES") {
                    $row_result_trips['eVerifyTollCharges'] = "No";
                }
                if (trim($row_result_trips['vChargesDetailData']) == "" || $row_result_trips['vChargesDetailData'] == null) {
                    $row_result_trips['vChargesDetailData'] = "{}";
                } else {
                    $vCurrencyDriver = $Data[0]['vCurrencyDriver'];
                    if (isset($currencyAssociateArr[$vCurrencyDriver]['Ratio']) && trim($currencyAssociateArr[$vCurrencyDriver]['vSymbol']) != "") {
                        $driverPriceRatio = $currencyAssociateArr[$vCurrencyDriver]['Ratio'];
                        $driverCurrencySymbol = $currencyAssociateArr[$vCurrencyDriver]['vSymbol'];
                    } else {
                        $driverCurrencyData = $obj->MySQLSelect("SELECT vSymbol,vName,Ratio FROM currency WHERE vName='" . $vCurrencyDriver . "'");
                        $driverCurrencySymbol = $driverCurrencyData[0]['vSymbol'];
                        $driverPriceRatio = $driverCurrencyData[0]['Ratio'];
                    }
                    $DataCharge = array();
                    $vChargeData = json_decode($row_result_trips['vChargesDetailData'], true);
                    /*if(strtoupper($ENABLE_MANUAL_TOLL_FEATURE) == 'YES') {
                        if (isset($vChargeData['fTollPrice']) && $vChargeData['fTollPrice'] != '' && $vChargeData['fTollPrice'] != 'nan') {
                            $DataCharge['fTollPrice'] = setTwoDecimalPoint($vChargeData['fTollPrice'] * $driverPriceRatio);
                        }
                        else {
                            $DataCharge['fTollPrice'] = setTwoDecimalPoint(0);
                        }
                    }
                    if(strtoupper($ENABLE_OTHER_CHARGES_FEATURE) == 'YES') {
                        if (isset($vChargeData['fOtherCharges']) && $vChargeData['fOtherCharges'] != '' && $vChargeData['fOtherCharges'] != 'nan') {
                            $DataCharge['fOtherCharges'] = setTwoDecimalPoint($vChargeData['fOtherCharges'] * $driverPriceRatio);
                        } else {
                            $DataCharge['fOtherCharges'] = setTwoDecimalPoint(0);
                        }
                    }*/
                    if (strtoupper($ENABLE_MANUAL_TOLL_FEATURE) == 'YES') {
                        if (isset($vChargeData['fTollPrice']) && $vChargeData['fTollPrice'] != '' && $vChargeData['fTollPrice'] != 'nan') {
                            $DataCharge['fTollPrice'] = setTwoDecimalPoint($vChargeData['fTollPrice'] * $driverPriceRatio);
                        }
                    }
                    if (strtoupper($ENABLE_OTHER_CHARGES_FEATURE) == 'YES') {
                        if (isset($vChargeData['fOtherCharges']) && $vChargeData['fOtherCharges'] != '' && $vChargeData['fOtherCharges'] != 'nan') {
                            $DataCharge['fOtherCharges'] = setTwoDecimalPoint($vChargeData['fOtherCharges'] * $driverPriceRatio);
                        }
                    }
                    if (isset($vChargeData['totalAmount']) && $vChargeData['totalAmount'] != '' && $vChargeData['totalAmount'] != 'nan') {
                        $DataCharge['totalAmount'] = setTwoDecimalPoint($vChargeData['totalAmount'] * $driverPriceRatio);
                    }
                    if (isset($vChargeData['serviceCost']) && $vChargeData['serviceCost'] != '' && $vChargeData['serviceCost'] != 'nan') {
                        $DataCharge['serviceCost'] = setTwoDecimalPoint($vChargeData['serviceCost'] * $driverPriceRatio);
                    }
                    if (isset($vChargeData['fMaterialFee']) && $vChargeData['fMaterialFee'] != '' && $vChargeData['fMaterialFee'] != 'nan') {
                        $DataCharge['fMaterialFee'] = setTwoDecimalPoint($vChargeData['fMaterialFee'] * $driverPriceRatio);
                    }
                    if (isset($vChargeData['fMiscFee']) && $vChargeData['fMiscFee'] != '' && $vChargeData['fMiscFee'] != 'nan') {
                        $DataCharge['fMiscFee'] = setTwoDecimalPoint($vChargeData['fMiscFee'] * $driverPriceRatio);
                    }
                    if (isset($vChargeData['fDriverDiscount']) && $vChargeData['fDriverDiscount'] != '' && $vChargeData['fDriverDiscount'] != 'nan') {
                        $DataCharge['fDriverDiscount'] = setTwoDecimalPoint($vChargeData['fDriverDiscount'] * $driverPriceRatio);
                    }
                    if (isset($vChargeData['vConfirmationCode']) && $vChargeData['vConfirmationCode'] != '' && $vChargeData['vConfirmationCode'] != 'nan') {
                        $DataCharge['vConfirmationCode'] = $vChargeData['vConfirmationCode'];
                    }
                    if (count($DataCharge) > 0) {
                        $row_result_trips['vChargesDetailData'] = json_encode($DataCharge);
                    } else {
                        $row_result_trips['vChargesDetailData'] = "{}";
                    }
                }
                // Added by HV on 16-10-2020 for Restrict Passenger Limit
                $person_limit = get_value('vehicle_type', 'iPersonSize', 'iVehicleTypeId', $row_result_trips['iVehicleTypeId'], '', 'true');
                $db_label = $obj->MySQLSelect("SELECT vValue FROM `language_label` WHERE vLabel = 'LBL_CURRENT_PERSON_LIMIT' AND vCode = '" . $vLanguage . "'");
                $row_result_trips['RESTRICT_PASSENGER_LIMIT_NOTE'] = str_replace('#PERSON_LIMIT#', $person_limit, $db_label[0]['vValue']);
                // Added by HV on 31-10-2020 for Face Mask verification - only for Ride & UberX
                if (!in_array($row_result_trips['eType'], ['Ride', 'UberX'])) {
                    $Data[0]['ENABLE_FACE_MASK_VERIFICATION'] = "No";
                }
                if ($row_result_trips['eType'] != 'Ride') {
                    $Data[0]['ENABLE_RESTRICT_PASSENGER_LIMIT'] = "No";
                }
                if ($MODULES_OBJ->isEnableOTPVerificationDelivery()) {
                    if ($row_result_trips['DriverDetails']['vTripStatus'] == "Arrived" && $row_result_trips['eType'] == "Multi-Delivery" && $row_result_trips['IS_OPEN_SIGN_VERIFY'] == "No" && $row_result_trips['IS_OPEN_FOR_SENDER'] == "No") {
                        $row_result_trips['eAskCodeToUser'] = "No";
                    }
                }
                if (isset($row_result_trips['vRandomCode'])) {
                    $row_result_trips['vText'] = (!empty($row_result_trips['vRandomCode'])) ? encodeVerificationCode($row_result_trips['vRandomCode']) : "";
                    $row_result_trips['vRandomCode'] = strlen($row_result_trips['vRandomCode']);
                }
                //Added By HJ On 29-08-2020 For Manual Toll and Extra Charge Related Changes End
                $Data[0]['TripDetails'] = $row_result_trips;
                $Data[0]['PassengerDetails'] = $row_result_trips['PassengerDetails'];
                $Data[0]['eSystem'] = $row_result_trips['eSystem'];
                //Added By HJ On 17-06-2020 For Optimize trip_times Table Query Start
                if (isset($tripDetailsArr["trip_times_" . $TripID])) {
                    $db_tripTimes = $tripDetailsArr["trip_times_" . $TripID];
                } else {
                    $db_tripTimes = $obj->MySQLSelect("SELECT * FROM `trip_times` WHERE iTripId='" . $TripID . "'");
                    $tripDetailsArr["trip_times_" . $TripID] = $db_tripTimes;
                }
                //Added By HJ On 17-06-2020 For Optimize trip_times Table Query End
                $totalSec = 0;
                $timeState = 'Pause';
                $iTripTimeId = '';
                if(!empty($db_tripTimes) && count($db_tripTimes) > 0) {
                    foreach ($db_tripTimes as $dtT) {
                        if ($dtT['dPauseTime'] != '' && $dtT['dPauseTime'] != '0000-00-00 00:00:00') {
                            $totalSec += strtotime($dtT['dPauseTime']) - strtotime($dtT['dResumeTime']);
                        } else {
                            $totalSec += strtotime(date('Y-m-d H:i:s')) - strtotime($dtT['dResumeTime']);
                            $iTripTimeId = $dtT['iTripTimeId'];
                            $timeState = 'Resume';
                        }
                    }
                }

                // $diff = strtotime('2009-10-05 18:11:08') - strtotime('2009-10-05 18:07:13')
                $Data[0]['iTripTimeId'] = $iTripTimeId;
                $Data[0]['TotalSeconds'] = $totalSec;
                $Data[0]['TimeState'] = $timeState;
                if ($Data[0]['eSystem'] == "DeliverAll") {
                    ############################# Food System Ratings From Driver  #############################
                    $sql = "SELECT ord.iOrderId,ord.iCompanyId,ord.iUserId,ord.iUserAddressId,ord.fNetTotal,ord.vOrderNo,ord.iStatusCode FROM `orders` as ord WHERE ord.iDriverId='" . $driverId . "' AND ord.iStatusCode = '6' AND (select count(iRatingId) from ratings_user_driver as rud where rud.iOrderId=ord.iOrderId AND rud.eFromUserType = 'Driver' ) = 0  ORDER BY ord.iOrderId DESC LIMIT 0,1";
                    $row_order = $obj->MySQLSelect($sql);
                    if (empty($row_order) && $MODULES_OBJ->isEnableAcceptMultipleOrders()) {
                        $sql = "SELECT ord.iOrderId,ord.iCompanyId,ord.iUserId,ord.iUserAddressId,ord.fNetTotal,ord.vOrderNo,ord.iStatusCode FROM `orders` as ord WHERE ord.iDriverId='" . $driverId . "' AND ord.iStatusCode IN (4,5,13,14) AND (select count(iRatingId) from ratings_user_driver as rud where rud.iOrderId=ord.iOrderId AND rud.eFromUserType = 'Driver' ) = 0  ORDER BY ord.iOrderId DESC LIMIT 0,1";
                        $row_order = $obj->MySQLSelect($sql);
                    }
                    if (count($row_order) > 0) {
                        $order_id = $row_order[0]['iOrderId'];
                        if ($MODULES_OBJ->isEnableAcceptMultipleOrders()) {
                            $order_trip_data = $obj->MySQLSelect("SELECT iTripId FROM trips WHERE iOrderId = '" . $order_id . "' ORDER BY iTripId DESC LIMIT 1");
                            $row_result_trips = FetchTripFareDetails($order_trip_data[0]['iTripId'], $driverId, "Driver");
                            $Data[0]['TripDetails'] = $row_result_trips;
                            $Data[0]['PassengerDetails'] = $row_result_trips['PassengerDetails'];
                        }
                        $queryChk = array();
                        if ($MODULES_OBJ->isEnableCancelDriverOrder()) {
                            $queryChk = $obj->MySQLSelect("SELECT * FROM order_driver_log WHERE `iOrderId` = '" . $order_id . "' AND `iDriverId` = '" . $driverId . "'");
                        }
                        if (!empty($queryChk) && count($queryChk) > 0) {
                            $Data[0]['Ratings_From_Driver'] = "";
                        } else {
                            $LastOrderId = $row_order[0]['iOrderId'];
                            $LastOrderCompanyId = $row_order[0]['iCompanyId'];
                            $LastOrderUserId = $row_order[0]['iUserId'];
                            $fNetTotal = $row_order[0]['fNetTotal'];
                            $iUserAddressId = $row_order[0]['iUserAddressId'];
                            $LastOrderNo = $row_order[0]['vOrderNo'];
                            $UserAddressArr = FetchMemberAddressData($LastOrderUserId, "Passenger", $iUserAddressId);
                            $UserAdress = ucfirst($UserAddressArr['UserAddress']);
                            $DriverDetailsArr = getDriverCurrencyLanguageDetails($driverId, $LastOrderId);
                            $vSymbol = $DriverDetailsArr['currencySymbol'];
                            $priceRatio = $DriverDetailsArr['Ratio'];
                            $fNetTotal = round(($fNetTotal * $priceRatio), 2);
                            //Added By HJ On 11-07-2020 For Optimization register_user Table Query Start
                            if (isset($userDetailsArr["register_user_" . $LastOrderUserId]) && count($userDetailsArr["register_user_" . $LastOrderUserId]) > 0) {
                                $result_user = $userDetailsArr["register_user_" . $LastOrderUserId];
                            } else {
                                $result_user = $obj->MySQLSelect("SELECT *,iUserId as iMemberId FROM register_user WHERE iUserId='" . $LastOrderUserId . "'");
                                $userDetailsArr["register_user_" . $LastOrderUserId] = $result_user;
                            }
                            $result_user[0]['UserName'] = $result_user[0]['vName'] . " " . $result_user[0]['vLastName'];
                            //Added By HJ On 11-07-2020 For Optimization register_user Table Query End
                            //$sql = "SELECT CONCAT(vName,' ',vLastName) AS UserName FROM register_user WHERE iUserId = '" . $LastOrderUserId . "'";
                            //$result_user = $obj->MySQLSelect($sql);
                            $row_result_ratings = $obj->MySQLSelect("SELECT count(iRatingId) as TotalRating FROM `ratings_user_driver` WHERE iOrderId='" . $LastOrderId . "' AND eFromUserType = 'Driver'");
                            $TotalRating = $row_result_ratings[0]['TotalRating'];
                            if ($TotalRating > 0) {
                                $Data[0]['Ratings_From_Driver'] = "Done";
                            } else {
                                $Data[0]['Ratings_From_Driver'] = "Not Done";
                                if ($MODULES_OBJ->isEnableAcceptMultipleOrders()) {
                                    if ($row_order[0]['iStatusCode'] == "6") {
                                        $Data[0]['vTripStatus'] = "Finished";
                                    }
                                }
                            }
                            $Data[0]['LastOrderId'] = $LastOrderId;
                            $Data[0]['LastOrderCompanyId'] = $LastOrderCompanyId;
                            $Data[0]['LastOrderUserId'] = $LastOrderUserId;
                            $Data[0]['LastOrderUserAddress'] = $UserAdress;
                            $Data[0]['LastOrderUserName'] = $result_user[0]['UserName'];
                            $Data[0]['LastOrderAmount'] = $vSymbol . " " . $fNetTotal;
                            $Data[0]['LastOrderNo'] = $LastOrderNo;
                        }
                    } else {
                        $Data[0]['Ratings_From_Driver'] = "";
                    }
                    ############################# Food System Ratings From Driver  #############################
                } else {
                    ############################# Ride System Ratings From Driver  #############################
                    //Added By HJ On 13-06-2020 For Optimization ratings_user_driver Table Query Start
                    $row_result_ratings = array();
                    if ($TripID > 0) {
                        if (isset($generalTripRatingDataArr['ratings_user_driver_' . $TripID])) {
                            $getTripRateData = $generalTripRatingDataArr['ratings_user_driver_' . $TripID];
                            for ($r = 0; $r < count($getTripRateData); $r++) {
                                $rateUserType = $getTripRateData[$r]['eUserType'];
                                if (strtoupper($rateUserType) == "DRIVER") {
                                    $row_result_ratings[] = $getTripRateData[$r];
                                }
                            }
                        } else {
                            $row_result_ratings = $obj->MySQLSelect("SELECT iTripId,eUserType FROM `ratings_user_driver` WHERE iTripId='" . $TripID . "' AND eUserType='Driver' AND vRating1 != '' ");
                        }
                    }
                    //Added By HJ On 13-06-2020 For Optimization ratings_user_driver Table Query End
                    $Data[0]['Ratings_From_Driver'] = "No Entry";
                    if (count($row_result_ratings) > 0) {
                        $count_row_rating = 0;
                        $ContentWritten = "false";
                        while (count($row_result_ratings) > $count_row_rating) {
                            $UserType = $row_result_ratings[$count_row_rating]['eUserType'];
                            $Data[0]['Ratings_From_Driver'] = "Not Done";
                            if ($UserType == "Driver") {
                                $ContentWritten = "true";
                                $Data[0]['Ratings_From_Driver'] = "Done";
                            }
                            $count_row_rating++;
                        }
                    }
                    if (strtolower($row_result_trips['eBookingFrom']) == 'kiosk' && $row_result_trips['ePaymentCollect'] == 'Yes') {
                        $ContentWritten = "true";
                        $Data[0]['Ratings_From_Driver'] = "Done";
                    }
                    if ($row_result_trips['isVideoCall'] == "Yes" && $row_result_trips['eCancelled'] == "Yes") {
                        $Data[0]['Ratings_From_Driver'] = "Done";
                    }
                }
                ############################# Ride System Ratings From Driver  #############################
                $Data[0]['TotalFareUberX'] = $Data[0]['TotalFareUberXValue'] = $Data[0]['UberXFareCurrencySymbol'] = "0";
                //$isAvailable = $MODULES_OBJ->isUfxFeatureAvailable(); // Commented By HJ On 04-06-2020 For Optimized Query Below Line
                $isAvailable = $isUfxAvailable; // Added By HJ On 04-06-2020 For Optimized Query
                if ((strtoupper(APP_TYPE) == "UBERX" || strtoupper(APP_TYPE) == "RIDE-DELIVERY-UBERX") && $isAvailable == "Yes") {
                    include_once('include/uberx/include_webservice_uberx.php');
                    $UberX_Trip_Charge = DisplayTripChargeForUberX($TripID);
                    // $Data[0]['TotalFareUberX'] = !is_numeric($UberX_Trip_Charge['TotalFareUberX']) ? "" : $UberX_Trip_Charge['TotalFareUberX'];
                    $Data[0]['TotalFareUberX'] = $UberX_Trip_Charge['TotalFareUberX'];
                    $Data[0]['TotalFareUberXValue'] = !is_numeric($UberX_Trip_Charge['TotalFareUberXValue']) ? "" : $UberX_Trip_Charge['TotalFareUberXValue'];
                    $Data[0]['UberXFareCurrencySymbol'] = $UberX_Trip_Charge['UberXFareCurrencySymbol'];
                }
            }
        } elseif ($MODULES_OBJ->isEnableBiddingServices() && !empty($Data[0]['vTaskStatus']) && $Data[0]['vTaskStatus'] != "NONE") {
            $Data[0]['TaskDetails'] = $BIDDING_OBJ->getTaskDetails($Data[0]['iBiddingPostId'], $vLanguage);
        }
        $Data[0]['isVideoCall'] = "No";
        if ($MODULES_OBJ->isEnableVideoConsultingService()) {
            if (!empty($TripID)) {
                $Data[0]['isVideoCall'] = get_value('trips', 'isVideoCall', 'iTripId', $TripID, '', 'true');
            }
        }
        //Added By HJ On 17-06-2020 For Optimization user_address Table Query Start
        if (isset($userAddressDataArr['user_address_' . $driverId])) {
            $result_Address = $userAddressDataArr['user_address_' . $driverId];
        } else {
            $userAddressDataArr = array();
            $result_Address = $obj->MySQLSelect("SELECT * from user_address WHERE iUserId = '" . $driverId . "' AND eStatus = 'Active'");
            $userAddressDataArr['user_address_' . $driverId] = $result_Address;
        }
        $totalAddressCount = 0;
        for ($a = 0; $a < count($result_Address); $a++) {
            $addresUser = $result_Address[$a]['eUserType'];
            if (strtoupper($addresUser) == "DRIVER") {
                $totalAddressCount += 1;
            }
        }
        //Added By HJ On 17-06-2020 For Optimization user_address Table Query End
        $Data[0]['ToTalAddress'] = $totalAddressCount;
        $Data[0]['ABOUT_US_PAGE_DESCRIPTION'] = "";
        $Data[0]['DefaultCurrencySign'] = $Data[0]["DEFAULT_CURRENCY_SIGN"];
        $Data[0]['DefaultCurrencyCode'] = $Data[0]["DEFAULT_CURRENCY_CODE"];
        $Data[0]['SITE_TYPE'] = SITE_TYPE;
        $Data[0]['RIIDE_LATER'] = RIIDE_LATER;
        $Data[0]['DELIVERALL'] = DELIVERALL;
        $Data[0]['ONLYDELIVERALL'] = ONLYDELIVERALL;
        //Added By HJ On 01-05-2020 For Check Store Driver Start
        $isStoreDriver = "No";
        $Data[0]['UFX_SERVICE_AVAILABLE'] = $isUfxAvailable; // Added By HJ On 04-06-2020 For Optimized Query
        if (strtoupper($Data[0]['eSystem_original']) == "DELIVERALL" && $MODULES_OBJ->isStorePersonalDriverAvailable()) {
            $Data[0]['ONLYDELIVERALL'] = "Yes";
            $isStoreDriver = "Yes";
            $Data[0]['INVITE_DESCRIPTION_CONTENT'] = $LBL_INVITE_FRIEND_SHARE_PREFIX_TXT . " " . $REFERRAL_AMOUNT_USER . " " . $LBL_INVITE_FRIEND_SHARE_POSTFIX_ORDER_TXT;
            $Data[0]['UFX_SERVICE_AVAILABLE'] = "No"; //added by SP on 25-01-2021 for store personel driver ufx service is not available
            $Data[0]['ENABLE_BIDDING_SERVICES'] = "No";
            $Data[0]['ENABLE_DRIVER_REWARD_MODULE'] = "No";
        }
        $Data[0]['STORE_PERSONAL_DRIVER'] = $isStoreDriver;
        //Added By HJ On 01-05-2020 For Check Store Driver End
        $Data[0]['SITE_TYPE_DEMO_MSG'] = $demo_site_msg;
        $Data[0]['vLicencePlateNo'] = is_null($Data[0]['vLicencePlateNo']) == false ? $Data[0]['vLicencePlateNo'] : '';
        //Added By HJ On 16-07-2019 For Check Multiple Country Exists Or Not Start
        // Added By HJ On 17-06-2020 For Optimized country Table Query Start
        if (count($country_data_retrieve) > 0) {
            $getCountryData = array();
            for ($h = 0; $h < count($country_data_retrieve); $h++) {
                if (strtoupper($country_data_retrieve[$h]['eStatus']) == "ACTIVE") {
                    $getCountryData[] = $country_data_retrieve[$h]['iCountryId'];
                }
            }
        } else {
            $getCountryData = $obj->MySQLSelect("SELECT iCountryId FROM country WHERE eStatus='Active'");
        }
        // Added By HJ On 17-06-2020 For Optimized country Table Query End
        $multiCountry = "No";
        if (count($getCountryData) > 1) {
            $multiCountry = "Yes";
        }
        $Data[0]['showCountryList'] = $multiCountry;
        //Added By HJ On 16-07-2019 For Check Multiple Country Exists Or Not End
        //Added By Hasmukh On 25-12-2018 For Get Advertise Banner Data Start
        if ($MODULES_OBJ->checkSharkPackage() && $Data[0]['eStatus'] == "active") {
            $Data[0]['advertise_banner_data'] = getAdvertisementBannersAsPerDevice($driverId, "Driver");
        }
        //Added By Hasmukh On 25-12-2018 For Get Advertise Banner Data End
        //Added By Hasmukh On 16-11-2018 For Check Trip Pool Status Start
        $Data[0]['FETCH_TRIP_STATUS_TIME_INTERVAL'] = fetchtripstatustimeinterval();
        //Added By Hasmukh On 16-11-2018 For Check Trip Pool Status End
        $Data[0]['ENABLE_TOLL_COST'] = $Data[0]['APP_TYPE'] != "UberX" ? $Data[0]['ENABLE_TOLL_COST'] : "No";
        /* Check Toll Enable For Driver's Country */
        if ($usercountrycode != "") {
            //Added By HJ On 17-06-2020 For Optimization country Table Query Start
            if (isset($country_data_arr[$usercountrycode]['eEnableToll']) && trim($country_data_arr[$usercountrycode]['eEnableToll']) != "") {
                $eEnableToll = $country_data_arr[$usercountrycode]['eEnableToll'];
            } else {
                $user_country_toll = $obj->MySQLSelect("SELECT eEnableToll from country WHERE vCountryCode = '" . $usercountrycode . "'");
                $eEnableToll = $user_country_toll[0]['eEnableToll'];
            }
            //Added By HJ On 17-06-2020 For Optimization country Table Query End
            if ($eEnableToll != "") {
                $Data[0]['ENABLE_TOLL_COST'] = ($eEnableToll == "Yes" && $Data[0]['ENABLE_TOLL_COST'] == "Yes") ? "Yes" : "No";
            }
        }
        if ($Data[0]['eSystem_original'] == "DeliverAll") {
            $Data[0]['ENABLE_SAFETY_CHECKLIST'] = "No";
            $Data[0]['WAYBILL_ENABLE'] = "No";
            $Data[0]['DRIVER_SUBSCRIPTION_ENABLE'] = "No";
        }
        /* Check Toll Enable For Driver's Country */
        if ($Data[0]['APP_TYPE'] == "UberX") {
            $Data[0]['APP_DESTINATION_MODE'] = "None";
            $Data[0]['ENABLE_TOLL_COST'] = $Data[0]['HANDICAP_ACCESSIBILITY_OPTION'] = $Data[0]['FEMALE_RIDE_REQ_ENABLE'] = $Data[0]['ENABLE_HAIL_RIDES'] = $Data[0]['CHILD_SEAT_ACCESSIBILITY_OPTION'] = $Data[0]['WHEEL_CHAIR_ACCESSIBILITY_OPTION'] = $Data[0]['WAYBILL_ENABLE'] = "No";
        }
        if ($Data[0]['APP_TYPE'] == "Ride" || $Data[0]['APP_TYPE'] == "Ride-Delivery" || $Data[0]['APP_TYPE'] == "Ride-Delivery-UberX") {
            $Data[0]['FEMALE_RIDE_REQ_ENABLE'] = $Data[0]['FEMALE_RIDE_REQ_ENABLE'];
            $Data[0]['HANDICAP_ACCESSIBILITY_OPTION'] = $Data[0]['HANDICAP_ACCESSIBILITY_OPTION'];
            $Data[0]['CHILD_SEAT_ACCESSIBILITY_OPTION'] = $Data[0]['CHILD_SEAT_ACCESSIBILITY_OPTION'];
            $Data[0]['WHEEL_CHAIR_ACCESSIBILITY_OPTION'] = $Data[0]['WHEEL_CHAIR_ACCESSIBILITY_OPTION'];
            $CheckRideDeliveryFeatureDisable_Arr = CheckRideDeliveryFeatureDisable();
            $eShowRideVehicles = $CheckRideDeliveryFeatureDisable_Arr['eShowRideVehicles'];
            $eShowDeliveryVehicles = $CheckRideDeliveryFeatureDisable_Arr['eShowDeliveryVehicles'];
            if ($eShowRideVehicles == 'No' && $eShowDeliveryVehicles == "No") {
                $Data[0]['HANDICAP_ACCESSIBILITY_OPTION'] = "No";
            }
        } else {
            $Data[0]['FEMALE_RIDE_REQ_ENABLE'] = $Data[0]['HANDICAP_ACCESSIBILITY_OPTION'] = $Data[0]['CHILD_SEAT_ACCESSIBILITY_OPTION'] = $Data[0]['WHEEL_CHAIR_ACCESSIBILITY_OPTION'] = "No";
        }
        if ($Data[0]['APP_TYPE'] == "Ride" || $Data[0]['APP_TYPE'] == "Ride-Delivery" || $Data[0]['APP_TYPE'] == "Ride-Delivery-UberX") {
            $Data[0]['ENABLE_HAIL_RIDES'] = $Data[0]['ENABLE_HAIL_RIDES'];
        } else {
            $Data[0]['ENABLE_HAIL_RIDES'] = "No";
        }
        // if ($Data[0]['APP_PAYMENT_MODE'] == "Card" || ONLYDELIVERALL == "Yes") {
        if (strtoupper($Data[0]['CASH_AVAILABLE']) == "NO" || ONLYDELIVERALL == "Yes") {
            $Data[0]['ENABLE_HAIL_RIDES'] = "No";
        }
        //$Data[0]['ENABLE_HAIL_RIDES'] = "Yes"; //Comment This Line Added For Testing By HJ On 30-12-2018
        $Data[0]['PHOTO_UPLOAD_SERVICE_ENABLE'] = $Data[0]['APP_TYPE'] == "UberX" ? $PHOTO_UPLOAD_SERVICE_ENABLE : "No";
        $Data[0]['ENABLE_DELIVERY_MODULE'] = SITE_TYPE == "Demo" ? $Data[0]['eDeliverModule'] : $Data[0]['ENABLE_DELIVERY_MODULE'];
        $Data[0]['PayPalConfiguration'] = $Data[0]['ENABLE_DELIVERY_MODULE'] == "Yes" ? "Yes" : $Data[0]['PAYMENT_ENABLED'];
        //Added By HJ On 17-06-2020 For Optimization currency Table Query Start
        /*$currencyNameArr = $defCurrencyValues = array();
        if (count($Data_ALL_currency_Arr) > 0) {
            for ($c = 0; $c < count($Data_ALL_currency_Arr); $c++) {
                if (strtoupper($Data_ALL_currency_Arr[$c]['eStatus']) == "ACTIVE") {
                    $defCurrencyValues[] = $Data_ALL_currency_Arr[$c];
                    $currencyNameArr[$Data_ALL_currency_Arr[$c]['vName']] = $Data_ALL_currency_Arr[$c];
                }
            }
            $Data[0]['CurrencyList'] = $defCurrencyValues;
        } else {
            $Data[0]['CurrencyList'] = get_value('currency', '*', 'eStatus', 'Active');
        }*/
        //Added By HJ On 17-06-2020 For Optimization currency Table Query End
        //$Data[0]['CurrencyList'] = get_value('currency', '*', 'eStatus', 'Active');
        $Data[0]['UBERX_PARENT_CAT_ID'] = $parent_ufx_catid;
        $Data[0]['UBERX_SUB_CAT_ID'] = "0";
        /* DRIVER DESTINATIONS START */
        if ($MODULES_OBJ->checkDriverDestinationModule() && !empty($driverId)) {
            include_once('include/features/include_destinations_driver.php');
            $Data[0]['DestinationLocations'] = getDriverFiveDestination($driverId);
        }
        /* DRIVER DESTINATIONS END */
        /* As a part of Socket Cluster */
        $Data[0]['MAX_ALLOW_TIME_INTERVAL_MILLI'] = (fetchtripstatustimeMAXinterval() + $intervalmins) * 1000;
        /* As a part of Socket Cluster */
        $user_available_balance = $WALLET_OBJ->FetchMemberWalletBalanceApp($driverId, "Driver");
        $Data[0]['user_available_balance'] = strval($user_available_balance);
        $user_available_balance_value = $WALLET_OBJ->FetchMemberWalletBalanceApp($driverId, "Driver", 'Yes');
        $Data[0]['user_available_balance_value'] = strval($user_available_balance_value);
        $Data[0]['eWalletBalanceAvailable'] = 'Yes';
        if ($user_available_balance_value <= 0 || $user_available_balance_value <= 0.00) {
            $Data[0]['eWalletBalanceAvailable'] = 'No';
        }
        //Added By HJ On 17-06-2020 For Optimization currency Table Query Start
        $vCurrencyDriver = $Data[0]['vCurrencyDriver'];
        if (isset($currencyNameArr[$vCurrencyDriver]['vSymbol']) && trim($currencyNameArr[$vCurrencyDriver]['vSymbol']) != "") {
            $CurrencySymbol = $currencyNameArr[$vCurrencyDriver]['vSymbol'];
        } else {
            $CurrencySymbol = get_value('currency', 'vSymbol', 'vName', $vCurrencyDriver, '', 'true');
        }
        $Data[0]['CurrencySymbol'] = $CurrencySymbol;
        //Added By HJ On 17-06-2020 For Optimization currency Table Query End
        //CP have told SP to change it, in cubetaxix it will get 0 so told me to do it like this on 28-10-2020
        if (isset($Data[0]['UberXFareCurrencySymbol']) && $Data[0]['UberXFareCurrencySymbol'] == 0) {
            $Data[0]['UberXFareCurrencySymbol'] = $CurrencySymbol;
        }
        $currencydata = get_value('currency', 'eReverseformattingEnable,eReverseSymbolEnable', 'vName', $vCurrencyDriver, '', '');
        $Data[0]['eReverseformattingEnable'] = $currencydata[0]['eReverseformattingEnable'];
        $Data[0]['eReverseSymbolEnable'] = $currencydata[0]['eReverseSymbolEnable'];
        /*$str_date = @date('Y-m-d H:i:s', strtotime('-1 minutes'));
        $sql_request = "SELECT * FROM passenger_requests WHERE iDriverId='" . $driverId . "' AND dAddedDate > '" . $str_date . "' ";
        $data_requst = $obj->MySQLSelect($sql_request);
        $Data[0]['CurrentRequests'] = $data_requst;*/
        $db_driver_fav_address = $obj->MySQLSelect("SELECT * FROM user_fave_address where iUserId = '" . $driverId . "' AND eUserType = 'Driver' AND eStatus = 'Active' ORDER BY iUserFavAddressId ASC");
        $Data[0]['UserFavouriteAddress'] = $db_driver_fav_address;
        $usercountrydetailbytimezone = FetchMemberCountryData($driverId, "Driver", $vTimeZone, $vUserDeviceCountry);
        $Data[0]['vDefaultCountry'] = $usercountrydetailbytimezone['vDefaultCountry'];
        $Data[0]['vDefaultCountryCode'] = $usercountrydetailbytimezone['vDefaultCountryCode'];
        $Data[0]['vDefaultPhoneCode'] = $usercountrydetailbytimezone['vDefaultPhoneCode'];
        $Data[0]['vRCountryImage'] = $usercountrydetailbytimezone['vRImageMember']; //added by SP for country image related changes on 06-09-2019
        $Data[0]['vSCountryImage'] = $usercountrydetailbytimezone['vSImageMember']; //added by SP for country image related changes on 06-09-2019
        //$Data[0]['vRCountryImage'] = $usercountrydetailbytimezone['vRImage']; //added by SP for country image related changes on 05-08-2019
        //$Data[0]['vSCountryImage'] = $usercountrydetailbytimezone['vSImage']; //added by SP for country image related changes on 05-08-2019
        $Data[0]['vDefaultCountryImage'] = empty($Data[0]['vSCountryImage']) ? $usercountrydetailbytimezone['vDefaultCountryImage'] : $Data[0]['vSCountryImage']; //added by SP for country image related changes on 06-08-2019
        $Data[0]['vCode'] = empty($Data[0]['vCode']) ? $Data[0]['vDefaultPhoneCode'] : $Data[0]['vCode'];
        $Data[0]['vCountry'] = empty($Data[0]['vCountry']) ? $Data[0]['vDefaultCountryCode'] : $Data[0]['vCountry'];
        $SITE_POLICE_CONTROL_NUMBER = getMemberCountryPoliceNumber($driverId, "Driver", $Data[0]['vCountry']);
        $Data[0]['SITE_POLICE_CONTROL_NUMBER'] = $SITE_POLICE_CONTROL_NUMBER;
        $Data[0]['MONGO_DB'] = $tconfig['tmongodb_databse'];
        $Data[0]['MONGO_DB_CONNECTION_PORT'] = $tconfig['tmongodb_port'];
        $Data[0]['SERVER_DEFAULT_TIMEZONE'] = date_default_timezone_get();
        if ($Data[0]['APP_TYPE'] == "Ride-Delivery-UberX") {
            $Data[0]['eShowVehicles'] = $Data[0]['eShowRideVehicles'] = $Data[0]['eShowDeliveryVehicles'] = "Yes";
            $checkridedelivery = CheckRideDeliveryFeatureDisable();
            $Data[0]['eShowRideVehicles'] = $checkridedelivery['eShowRideVehicles'];
            $Data[0]['eShowDeliveryVehicles'] = $checkridedelivery['eShowDeliveryVehicles'];
            $Data[0]['eShowDeliverAllVehicles'] = $checkridedelivery['eShowDeliverAllVehicles'];
            if ($Data[0]['eShowRideVehicles'] == "No" && $Data[0]['eShowDeliveryVehicles'] == "No" && ($Data[0]['eShowDeliverAllVehicles'] == "No" || DELIVERALL == "No")) {
                $Data[0]['eShowVehicles'] = "No";
            }
        }
        $Data[0]['ENABLE_MULTI_DELIVERY'] = ENABLE_MULTI_DELIVERY;
        $Data[0]['tsite_upload_docs_file_extensions'] = $tconfig['tsite_upload_docs_file_extensions'];
        $Data[0]['tsite_upload_image_file_extensions'] = $tconfig['tsite_upload_image_file_extensions'];
        $Data[0]['tsite_upload_video_file_extensions'] = $tconfig['tsite_upload_video_file_extensions'];
        $Data[0]['tsite_upload_audio_file_extensions'] = $tconfig['tsite_upload_audio_file_extensions'];
        $Data[0]['SC_CONNECT_URL'] = getSocketURL();
        $Data[0]['APP_SERVICE_URL'] = APP_SERVICE_URL;
        $Data[0]['GOOGLE_API_REPLACEMENT_URL'] = GOOGLE_API_REPLACEMENT_URL;
        $Data[0]['TSITE_DB'] = TSITE_DB;
        if ($MODULES_OBJ->checkDriverDestinationModule()) {
            include_once('include/features/include_destinations_driver.php');
            $Data[0]['DriverDestinationData'] = getDriverDestination($driverId);
        }
        if ($MODULES_OBJ->isDriverSubscriptionModuleAvailable()) {
            $Data[0]['DRIVER_SUBSCRIPTION_ENABLE'] = 'Yes';
        } else {
            $Data[0]['DRIVER_SUBSCRIPTION_ENABLE'] = 'No';
        }

        //Added By HJ On 08-06-2020 For Optimization configurations_payment Table Query Start
        if (isset($generalConfigPaymentArr['ENABLE_GOPAY']) && trim($generalConfigPaymentArr['ENABLE_GOPAY']) != "") {
            $EnableGopay = trim($generalConfigPaymentArr['ENABLE_GOPAY']);
        } else {
            $EnableGopay = get_value('configurations_payment', 'vValue', 'vName', 'ENABLE_GOPAY'); //added by SP for Gojek-gopay
        }
        //Added By HJ On 08-06-2020 For Optimization configurations_payment Table Query End
        if (!empty($EnableGopay[0]['vValue'])) {
            $Data[0]['ENABLE_GOPAY'] = $EnableGopay[0]['vValue'];
        } else if (!empty($EnableGopay)) {
            $row[0]['ENABLE_GOPAY'] = $EnableGopay;
        } else {
            $Data[0]['ENABLE_GOPAY'] = '';
        }
        $Data[0]['PAYMENT_BASE_URL'] = $tconfig['tsite_url'] . 'assets/libraries/webview/system_payment.php?';
        $Data[0]['PAYMENT_MODE_URL'] = $tconfig['tsite_url'] . 'assets/libraries/webview/payment_mode_select.php?';
        //$Data[0]['UFX_SERVICE_AVAILABLE'] = $MODULES_OBJ->isUfxFeatureAvailable(); // Commented By HJ On 04-06-2020 For Optimized Query Below Line
        $Data[0]['RANDOM_COLORS_KEY_VAL_ARR'] = RANDOM_COLORS_KEY_VAL_ARR;
        $Data[0]['AUTH_EMAIL_SYSTEM'] = AUTH_EMAIL_SYSTEM;
        $Data[0]['ENABLE_TAKE_AWAY'] = ($MODULES_OBJ->isTakeAwayEnable()) ? "Yes" : "No";
        $Data[0]['ENABLE_ADD_PROVIDER_FROM_STORE'] = $MODULES_OBJ->isStorePersonalDriverAvailable() ? 'Yes' : 'No';
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
        $Data[0]['APP_HOME_PAGE_LIST_VIEW_ENABLED'] = ($MODULES_OBJ->isEnableAppHomePageListView()) ? "Yes" : "No"; //Added By HJ On 24-09-2020 For Enable/Disable Home Page List View Type
        $Data[0]['DELIVERY_LATER_BOOKING_ENABLED'] = ($MODULES_OBJ->isEnableDeliveryScheduleLaterBooking()) ? "Yes" : "No"; //Added By HJ On 26-09-2020 For Enable/Disable Delivery Later Booking Module
        $Data[0]['PICK_DROP_GENIE'] = PICK_DROP_GENIE; // Added by HV on 12-10-2020 for Genie Pickup/Dropoff Items
        // Added by HV for Restrict Passenger Limit Feature
        $Data[0]['RESTRICT_PASSENGER_LIMIT_INFO_URL'] = $tconfig['tsite_url'] . 'safety_checklist.php?iPageId=56&vLang=' . $vLanguage;
        $Data[0]['ENABLE_DELIVERY_INSTRUCTIONS_ORDERS'] = ($MODULES_OBJ->isEnableVoiceDeliveryInstructionsOrder()) ? "Yes" : "No";
        $Data[0]['ENABLE_RIDE_DELIVERY_NEW_FLOW'] = ($MODULES_OBJ->isEnableRideDeliveryV1()) ? "Yes" : "No";
        $Data[0]['ENABLE_APPLE_LOGIN_FOR_PROVIDER'] = ($MODULES_OBJ->isEnableAppleLoginForProvider()) ? "Yes" : "No";
        //UpdateAppTerminateStatus($driverId, "Driver");
        /* Default card according to Payment Gateway*/
        $countryPaymentMethod = $obj->MySQLSelect("SELECT vPaymentGateway FROM country WHERE vCountryCode = '" . $Data[0]['vCountry'] . "'");
        $USER_APP_PAYMENT_METHOD = $APP_PAYMENT_METHOD;
        if (!empty($countryPaymentMethod[0]['vPaymentGateway'])) {
            $USER_APP_PAYMENT_METHOD = $countryPaymentMethod[0]['vPaymentGateway'];
        }
        $userPaymentInfo = getPaymentDefaultCard($driverId, 'Driver');
        $Data[0]['vCreditCard'] = (count($userPaymentInfo) > 0) ? $userPaymentInfo[0]['tCardNum'] : "";
        $Data[0]['isSmartLoginEnable'] = $MODULES_OBJ->isEnableSmartLogin() ? "Yes" : "No";
        if ($MODULES_OBJ->isEnableDriverRewardModule()) {
            $Data[0]['reward'] = $DRIVER_REWARD_OBJ->getDriverRewardInfo($driverId, $vLanguage);  // Added By HP for the reward  On 13-11-2021
        }
        $Data[0]['WEBRTC_SOCKET_URL'] = WEBRTC_SOCKET_URL;
        $Data[0]['WEBRTC_STUN_URL'] = WEBRTC_STUN_URL;
        $Data[0]['WEBRTC_TURN_URL'] = WEBRTC_TURN_URL;
        $Data[0]['WEBRTC_ICE_SERVER_LIST'] = WEBRTC_ICE_SERVER_LIST;
        $Data[0]['WEBRTC_USERNAME'] = $tconfig["tsite_webrtc_username"];
        $Data[0]['WEBRTC_PASS'] = $tconfig["tsite_webrtc_pass"];
        if ($MODULES_OBJ->isEnableDriverRewardModule()) {
            $getActiveCampaign = $DRIVER_REWARD_OBJ->getActiveCampaign();
            if (count($getActiveCampaign) > 0) {
                $dEnd_date = date('M d Y', strtotime($getActiveCampaign[0]['dEnd_date']));
                $db_label = $obj->MySQLSelect("SELECT vValue FROM `language_label` WHERE vLabel = 'LBL_REWARD_SUBTITLE' AND vCode = '" . $vLanguage . "'");
                $Data[0]['REWARD_SUBTITLE_DESC'] = str_replace(array('#CAMPAIGN_END_DATE#'), array($dEnd_date), $db_label[0]['vValue']);
            } else {
                $Data[0]['ENABLE_DRIVER_REWARD_MODULE'] = 'No';
            }
        }
        $vehicle_data = $obj->MySQLSelect("SELECT COUNT(iDriverVehicleId) as VehicleCount FROM driver_vehicle WHERE iDriverId = '$driverId' AND eStatus != 'Deleted' AND eType != 'UberX' ");
        $Data[0]['VEHICLE_COUNT'] = $vehicle_data[0]['VehicleCount'];

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
        $Data[0]['TRACK_ANY_SERVICE_ENABLED'] = TRACK_ANY_SERVICE_ENABLED;
        $Data[0]['IS_TRACKING_PROVIDER'] = "No";
        if ($Data[0]['iTrackServiceCompanyId'] > 0) {
            $Data[0]['IS_TRACKING_PROVIDER'] = "Yes";
            $Data[0]['TrackingTripDetails'] = $TRACK_SERVICE_OBJ->fetchTrackingTripStatus($Data[0]['iTrackServiceTripId']);
        }
        if ($MODULES_OBJ->isEnableGiftCardFeature()) {
            $Data[0]['GIFT_CARD_IMAGES'] = $GIFT_CARD_OBJ->getGiftCardImages();
            $Data[0]['GIFT_CARD_MAX_AMOUNT_WITH_SYMBOL'] = formateNumAsPerCurrency($Data[0]['GIFT_CARD_MAX_AMOUNT'], $vCurrencyDriver);
            $Data[0]['PREVIEW_GIFT_CARD_URL'] = $tconfig['tsite_url'] . 'preview_gift_card.php?';
            $Data[0]['TERMS_&_CONDITIONS_GIFT_CARD_URL'] = $tconfig['tsite_url'] . 'terms_conditions_gift_card.php';
        }

        if ($CONFIG_OBJ->isOnlyCashPaymentModeAvailable() || $Data[0]['WALLET_ENABLE'] == 'No') {
            $Data[0]['ENABLE_GIFT_CARD_FEATURE'] = 'No';
        } 
        $Data[0]['SERVICE_CATEGORIES_ARR'] = SERVICE_CATEGORIES_ARR;

        $Data[0]['ENABLE_PIP_MODE'] = ENABLE_PIP_MODE;

    /*     $Data[0]['LOCATION_BATCH_TASK_DURATION'] = fetchTaskInterval('LOCATION_BATCH_TASK_DURATION');
        $Data[0]['PROVIDER_STATUS_TASK_DURATION'] = fetchTaskInterval('PROVIDER_STATUS_TASK_DURATION'); */
        $Data[0]['CARD_SAVE_ENABLE'] = isEnableAddCard()['CARD_SAVE_ENABLE'];
        unset($Data[0]['tLocationUpdateDate']);
        unset($Data[0]['tSeenAdvertiseTime']);
        unset($Data[0]['CRON_TIME']);
        unset($Data[0]['tLastOnline']);
        unset($Data[0]['MAIL_FOOTER']);

        return $Data[0];
    } else {
        $returnArr['Action'] = "0";
        $returnArr['eStatus'] = "";
        $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
        setDataResponse($returnArr);
    }
}

function getDriverDetailInfoDeliverAll($driverId, $fromSignIN = 0)
{
    global $CONFIG_OBJ,$GIFT_CARD_OBJ, $obj, $demo_site_msg, $PHOTO_UPLOAD_SERVICE_ENABLE, $parent_ufx_catid, $generalSystemConfigDataArr, $vTimeZone, $tconfig, $vUserDeviceCountry, $intervalmins, $generalConfigPaymentArr, $ENABLE_DRIVER_ADVERTISEMENT_BANNER, $ADVERTISEMENT_TYPE, $RIDER_REQUEST_ACCEPT_TIME, $vSystemDefaultCurrencyName, $vSystemDefaultCurrencySymbol, $tripDetailsArr, $generalTripRatingDataArr, $userAddressDataArr, $country_data_retrieve, $country_data_arr, $Data_ALL_currency_Arr, $driverVehicleDataArr, $currencyAssociateArr, $languageLabelDataArr, $userDetailsArr, $APP_PAYMENT_METHOD, $MODULES_OBJ, $WALLET_OBJ, $LANG_OBJ, $iServiceId, $APP_TYPE;
    ChangeDriverVehicleRideDeliveryFeatureDisable($driverId);
    $where = " iDriverId = '" . $driverId . "'";
    $data_version['iAppVersion'] = "2";
    $data_version['eLogout'] = 'No';
    $data_version['eDebugMode'] = isset($_REQUEST["IS_DEBUG_MODE"]) ? $_REQUEST["IS_DEBUG_MODE"] : "";
    $data_version['tApiFileName'] = pathinfo(__FILE__, PATHINFO_FILENAME);
    #################################### Generate Session For GeoAPI ########################################
    generateSessionForGeo($driverId, "Driver");
    #################################### Generate Session For GeoAPI ########################################
    #################################### Configure App Version Info ########################################
    $arr_app_version = array();
    $arr_app_version['AppVersionName'] = isset($_REQUEST['GeneralAppVersion']) ? $_REQUEST['GeneralAppVersion'] : "";
    $arr_app_version['AppVersionCode'] = isset($_REQUEST['GeneralAppVersionCode']) ? $_REQUEST['GeneralAppVersionCode'] : "";
    #################################### Configure App Version Info ########################################
    $data_version['tVersion'] = strval(json_encode($arr_app_version));
    $data_version['tDeviceData'] = isset($_REQUEST['DEVICE_DATA']) ? $_REQUEST['DEVICE_DATA'] : "";
    $obj->MySQLQueryPerform("register_driver", $data_version, 'update', $where);
    $obj->sql_query("UPDATE trip_status_messages SET eReceived='Yes' WHERE iDriverId='" . $driverId . "' AND eToUserType='Driver'");
    $returnArr = array();
    $Data = $obj->MySQLSelect("SELECT rd.*,cmp.eSystem,cmp.eStatus as cmpEStatus,(SELECT dv.vLicencePlate From driver_vehicle as dv WHERE rd.iDriverVehicleId != '' AND rd.iDriverVehicleId !='0' AND dv.iDriverVehicleId = rd.iDriverVehicleId) as vLicencePlateNo FROM `register_driver` as rd,`company` as cmp WHERE rd.iDriverId='" . $driverId . "' AND cmp.iCompanyId=rd.iCompanyId");
    $Data[0]['eSystem_original'] = $Data[0]['eSystem'];
    if (count($Data) > 0) {
        //Added By HJ On 11-07-2020 For Optimization currency Table Query Start
        if (!empty($vSystemDefaultCurrencyName) && !empty($vSystemDefaultCurrencySymbol)) {
            $Data[0]['vFlutterwaveCurrency'] = $vSystemDefaultCurrencyName;
            $vFlutterwavevSymbol = $vSystemDefaultCurrencySymbol;
        }
        else {
            $defaultCurrencyDataArr = get_value('currency', 'vName,vSymbol', 'eDefault', 'Yes');
            /* Added By PM On 09-12-2019 For Flutterwave Code Start */
            $Data[0]['vFlutterwaveCurrency'] = $defaultCurrencyDataArr[0]['vName'];
            $vFlutterwavevSymbol = $defaultCurrencyDataArr[0]['vSymbol'];
            /* Added By PM On 09-12-2019 For Flutterwave Code End */
        }
        //Added By HJ On 11-07-2020 For Optimization currency Table Query End
        $page_link = $tconfig['tsite_url'] . "sign-up.php?UserType=Driver&vRefCode=" . $Data[0]['vRefCode'];
        $link = get_tiny_url($page_link);
        // $activation_text = '<a href="'.$link.'" target="_blank"> '.$link.' </a>';
        $activation_text = "<a href='" . $link . "' target='_blank'> '" . $link . "' </a>";
        $vLanguage = $Data[0]['vLang'];
        if ($vLanguage == "" || $vLanguage == NULL) {
            //Added By HJ On 17-06-2020 For Optimize language_master Table Query Start
            $vLanguage = $LANG_OBJ->FetchDefaultLangData("vCode");
            //Added By HJ On 17-06-2020 For Optimize language_master Table Query End
        }
        $langLabels = $LANG_OBJ->FetchLanguageLabels($vLanguage, "1", $iServiceId);
        if (isset($langLabels['LBL_SHARE_CONTENT_DRIVER']) && trim($langLabels['LBL_SHARE_CONTENT_DRIVER']) != "") {
            $LBL_SHARE_CONTENT_DRIVER = $langLabels['LBL_SHARE_CONTENT_DRIVER'];
        }
        else {
            $db_label = $obj->MySQLSelect("SELECT * FROM `language_label` WHERE vLabel = 'LBL_SHARE_CONTENT_DRIVER' AND vCode = '" . $vLanguage . "'");
            $LBL_SHARE_CONTENT_DRIVER = $db_label[0]['vValue'];
        }
        $Data[0]['INVITE_SHARE_CONTENT'] = $LBL_SHARE_CONTENT_DRIVER . " " . $link;
        foreach ($generalSystemConfigDataArr as $key => $value) {
            if (is_null($generalSystemConfigDataArr[$key]) || empty($generalSystemConfigDataArr[$key])) {
                $generalSystemConfigDataArr[$key] = "";
            }
        }
        $Data[0] = array_merge($Data[0], $generalSystemConfigDataArr); // Added By HJ On 18-03-2020 For Optimized Function
        if ($_REQUEST['APP_TYPE'] != "") {
            $Data[0]['APP_TYPE'] = $_REQUEST['APP_TYPE'];
        }
        $Data[0]['PACKAGE_TYPE'] = strtoupper(PACKAGE_TYPE);
        $Data[0]['GOOGLE_ANALYTICS'] = "";
        $Data[0]['SERVER_MAINTENANCE_ENABLE'] = $Data[0]['MAINTENANCE_APPS'];
        //Added By HJ On 16-07-2019 For Check Empty and # Value Of Configuration Start
        if (isset($Data[0]['LIVE_CHAT_LICENCE_NUMBER']) && ($Data[0]['LIVE_CHAT_LICENCE_NUMBER'] == "" || strpos($Data[0]['LIVE_CHAT_LICENCE_NUMBER'], '#') !== false)) {
            $Data[0]['ENABLE_LIVE_CHAT'] = "No";
        }

        $usercountrycode = "";
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
        $DRIVER_EMAIL_VERIFICATION = $Data[0]["DRIVER_EMAIL_VERIFICATION"];
        $DRIVER_PHONE_VERIFICATION = $Data[0]["DRIVER_PHONE_VERIFICATION"];
        if ($Data[0]['MULTI_LEVEL_REFERRAL_SCHEME_ENABLE'] == "Yes") {
            //added by SP on 03-03-2021 when multilevel then only shows text not amt..becoz it will not be calculated here..
            if (isset($langLabels['LBL_REFERRAL_AMOUNT']) && trim($langLabels['LBL_REFERRAL_AMOUNT']) != "") {
                $LBL_REFERRAL_AMOUNT = $langLabels['LBL_REFERRAL_AMOUNT'];
            }
            else {
                $LBL_REFERRAL_AMOUNT = get_value('language_label', 'vValue', 'vLabel', 'LBL_REFERRAL_AMOUNT', " and vCode='" . $vLanguage . "'", 'true');
            }
            $REFERRAL_AMOUNT_USER = $LBL_REFERRAL_AMOUNT;
        }
        else {
            $REFERRAL_AMOUNT = $Data[0]["REFERRAL_AMOUNT"];
            $REFERRAL_AMOUNT_ARR = getPriceUserCurrency($driverId, "Driver", $REFERRAL_AMOUNT);
            $REFERRAL_AMOUNT_USER = $REFERRAL_AMOUNT_ARR['fPricewithsymbol'];
        }
        if (isset($langLabels['LBL_INVITE_FRIEND_SHARE_PREFIX_TXT']) && trim($langLabels['LBL_INVITE_FRIEND_SHARE_PREFIX_TXT']) != "") {
            $LBL_INVITE_FRIEND_SHARE_PREFIX_TXT = $langLabels['LBL_INVITE_FRIEND_SHARE_PREFIX_TXT'];
        }
        else {
            $LBL_INVITE_FRIEND_SHARE_PREFIX_TXT = get_value('language_label', 'vValue', 'vLabel', 'LBL_INVITE_FRIEND_SHARE_PREFIX_TXT', " and vCode='" . $vLanguage . "'", 'true');
        }
        if (isset($langLabels['LBL_INVITE_FRIEND_SHARE_POSTFIX_TXT']) && trim($langLabels['LBL_INVITE_FRIEND_SHARE_POSTFIX_TXT']) != "") {
            $LBL_INVITE_FRIEND_SHARE_POSTFIX_TXT = $langLabels['LBL_INVITE_FRIEND_SHARE_POSTFIX_TXT'];
        }
        else {
            $LBL_INVITE_FRIEND_SHARE_POSTFIX_TXT = get_value('language_label', 'vValue', 'vLabel', 'LBL_INVITE_FRIEND_SHARE_POSTFIX_TXT', " and vCode='" . $vLanguage . "'", 'true');
        }
        if (isset($langLabels['LBL_INVITE_FRIEND_SHARE_POSTFIX_ORDER_TXT']) && trim($langLabels['LBL_INVITE_FRIEND_SHARE_POSTFIX_ORDER_TXT']) != "") {
            $LBL_INVITE_FRIEND_SHARE_POSTFIX_ORDER_TXT = $langLabels['LBL_INVITE_FRIEND_SHARE_POSTFIX_ORDER_TXT'];
        }
        else {
            $LBL_INVITE_FRIEND_SHARE_POSTFIX_ORDER_TXT = get_value('language_label', 'vValue', 'vLabel', 'LBL_INVITE_FRIEND_SHARE_POSTFIX_ORDER_TXT', " and vCode='" . $vLanguage . "'", 'true');
        }
        $Data[0]['INVITE_DESCRIPTION_CONTENT'] = $LBL_INVITE_FRIEND_SHARE_PREFIX_TXT . " " . $REFERRAL_AMOUNT_USER . " " . $LBL_INVITE_FRIEND_SHARE_POSTFIX_TXT;
        if ($DRIVER_EMAIL_VERIFICATION == 'No') {
            $Data[0]['eEmailVerified'] = "Yes";
        }
        /*for email optional*/
        if ($Data[0]['vEmail'] == "") {
            $Data[0]['eEmailVerified'] = "Yes";
        }
        if ($DRIVER_PHONE_VERIFICATION == 'No') {
            $Data[0]['ePhoneVerified'] = "Yes";
        }
        $lang_usr = $Data[0]['vLang'];
        $sql = "select vBody_$lang_usr as Message from send_message_templates where vEmail_Code = 'VERIFICATION_CODE_MESSAGE'";
        $data_SMS = $obj->MySQLSelect($sql);
        $Data[0]['SMS_BODY'] = $data_SMS[0]['Message'];
        ## Check and vWorkLocationRadius For UberX ##
        $eUnit = getMemberCountryUnit($driverId, "Driver");
        $Data[0]['eUnit'] = $eUnit;
        if ($Data[0]['vWorkLocationRadius'] == "" || $Data[0]['vWorkLocationRadius'] == "0" || $Data[0]['vWorkLocationRadius'] == 0) {
            $vWorkLocationRadius = $Data[0]['RESTRICTION_KM_NEAREST_TAXI'];
            $Update_Driver_radius['vWorkLocationRadius'] = $vWorkLocationRadius;
            $obj->MySQLQueryPerform("register_driver", $Update_Driver_radius, 'update', $where);
            $Data[0]['vWorkLocationRadius'] = $vWorkLocationRadius;
            if ($eUnit == "Miles") {
                $Data[0]['vWorkLocationRadius'] = round($vWorkLocationRadius * 0.621371, 2);
            }
            else {
                $Data[0]['vWorkLocationRadius'] = $vWorkLocationRadius;
            }
        }
        else {
            $vWorkLocationRadius = $Data[0]['vWorkLocationRadius'];
            if ($eUnit == "Miles") {
                $Data[0]['vWorkLocationRadius'] = round($vWorkLocationRadius * 0.621371, 2);
            }
            else {
                $Data[0]['vWorkLocationRadius'] = $vWorkLocationRadius;
            }
        }
        ## Display Braintree  Charge Message ##
        /*if (isset($langLabels['LBL_BRAINTREE_CHARGE_MSG_TXT']) && trim($langLabels['LBL_BRAINTREE_CHARGE_MSG_TXT']) != "") {
            $LBL_BRAINTREE_CHARGE_MSG_TXT = $langLabels['LBL_BRAINTREE_CHARGE_MSG_TXT'];
        }
        else {
            $db_label_braintree = $obj->MySQLSelect("SELECT * FROM `language_label` WHERE vLabel = 'LBL_BRAINTREE_CHARGE_MSG_TXT' AND vCode = '" . $vLanguage . "'");
            $LBL_BRAINTREE_CHARGE_MSG_TXT = $db_label_braintree[0]['vValue'];
        }
        $BRAINTREE_CHARGE_AMOUNT = $Data[0]['BRAINTREE_CHARGE_AMOUNT'];
        $BRAINTREE_CHARGE_AMOUNT_USER_ARR = getPriceUserCurrency($driverId, "Driver", $BRAINTREE_CHARGE_AMOUNT);
        $BRAINTREE_CHARGE_AMOUNT_USER = $BRAINTREE_CHARGE_AMOUNT_USER_ARR['fPricewithsymbol'];
        $msg = str_replace('####', $BRAINTREE_CHARGE_AMOUNT_USER, $LBL_BRAINTREE_CHARGE_MSG_TXT);
        $Data[0]['BRAINTREE_CHARGE_MESSAGE'] = $msg;
        ## Display Braintree Charge Message ##
        ## Display Adyen Charge Message ##
        if (isset($langLabels['LBL_ADYEN_CHARGE_MSG_TXT']) && trim($langLabels['LBL_ADYEN_CHARGE_MSG_TXT']) != "") {
            $LBL_ADYEN_CHARGE_MSG_TXT = $langLabels['LBL_ADYEN_CHARGE_MSG_TXT'];
        }
        else {
            $db_label_adyen = $obj->MySQLSelect("SELECT * FROM `language_label` WHERE vLabel = 'LBL_ADYEN_CHARGE_MSG_TXT' AND vCode = '" . $vLanguage . "'");
            $LBL_ADYEN_CHARGE_MSG_TXT = $db_label_adyen[0]['vValue'];
        }
        $ADEYN_CHARGE_AMOUNT = $Data[0]['ADYEN_CHARGE_AMOUNT'];
        $ADEYN_CHARGE_AMOUNT_USER_ARR = getPriceUserCurrency($passengerID, "Passenger", $ADEYN_CHARGE_AMOUNT);
        $ADEYN_CHARGE_AMOUNT_USER = $ADEYN_CHARGE_AMOUNT_USER_ARR['fPricewithsymbol'];
        $msg = str_replace('####', $ADEYN_CHARGE_AMOUNT_USER, $LBL_ADYEN_CHARGE_MSG_TXT);
        $Data[0]['ADEYN_CHARGE_MESSAGE'] = $msg;*/
        ## Display Adyen Charge Message ##
        /* Added By PM On 09-12-2019 For Flutterwave Code Start */
        ## Display Flutterwave Charge Message ##
        /*$FLUTTERWAVE_CHARGE_AMOUNT = $generalConfigPaymentArr['FLUTTERWAVE_CHARGE_AMOUNT'];
        ## Display Flutterwave Charge Message ##
        if (isset($langLabels['LBL_FLUTTERWAVE_CHARGE_MSG_TXT']) && trim($langLabels['LBL_FLUTTERWAVE_CHARGE_MSG_TXT']) != "") {
            $LBL_FLUTTERWAVE_CHARGE_MSG_TXT = $langLabels['LBL_FLUTTERWAVE_CHARGE_MSG_TXT'];
        }
        else {
            $db_label_flutter = $obj->MySQLSelect("SELECT * FROM `language_label` WHERE vLabel = 'LBL_FLUTTERWAVE_CHARGE_MSG_TXT' AND vCode = '" . $vLanguage . "'");
            $LBL_FLUTTERWAVE_CHARGE_MSG_TXT = $db_label_flutter[0]['vValue'];
        }
        $amountDataArr = getSupportedCurrencyAmt($Data[0]['FLUTTERWAVE_CHARGE_AMOUNT'], $Data[0]['vFlutterwaveCurrency']);
        $Data[0]['vFlutterwaveCurrency'] = $amountDataArr['CURRENCY_CODE'];
        $FLUTTERWAVE_CHARGE_AMOUNT = $amountDataArr['AMOUNT'];
        $FLUTTERWAVE_CHARGE_AMOUNT_USER = $vFlutterwavevSymbol . $FLUTTERWAVE_CHARGE_AMOUNT;
        $Data[0]['FLUTTERWAVE_CHARGE_AMOUNT'] = $FLUTTERWAVE_CHARGE_AMOUNT;
        $msg = str_replace('####', $FLUTTER_WAVE_CHARGE_AMOUNT_USER, $LBL_FLUTTERWAVE_CHARGE_MSG_TXT);
        $Data[0]['FLUTTERWAVE_CHARGE_MESSAGE'] = $msg;*/
        /* Added By PM On 09-12-2019 For Flutterwave Code Start */
        ## Check and update Device Session ID ##
        if ($Data[0]['tDeviceSessionId'] == "") {
            $random = substr(md5(rand()), 0, 7);
            $Update_Device_Session['tDeviceSessionId'] = session_id() . time() . $random;
            $Update_Device_Session_id = $obj->MySQLQueryPerform("register_driver", $Update_Device_Session, 'update', $where);
            $Data[0]['tDeviceSessionId'] = $Update_Device_Session['tDeviceSessionId'];
        }
        ## Check and update Device Session ID ##
        ## Check and update Session ID ##
        if ($Data[0]['tSessionId'] == "") {
            $Update_Session['tSessionId'] = session_id() . time();
            $Update_Session_id = $obj->MySQLQueryPerform("register_driver", $Update_Session, 'update', $where);
            $Data[0]['tSessionId'] = $Update_Session['tSessionId'];
        }
        ## Check and update Session ID ##
        // $Data[0]['Driver_Password_decrypt']= decrypt($Data[0]['vPassword']);
        $Data[0]['Driver_Password_decrypt'] = "";
        if ($Data[0]['vImage'] != "" && $Data[0]['vImage'] != "NONE") {
            $Data[0]['vImage'] = "3_" . $Data[0]['vImage'];
        }
        if (($Data[0]['iDriverVehicleId'] == '' || $Data[0]['iDriverVehicleId'] == NULL) && $Data[0]['APP_TYPE'] != "Ride-Delivery-UberX") {
            $Data_vehicle = $obj->MySQLSelect("SELECT iDriverVehicleId FROM  driver_vehicle WHERE `eStatus` = 'Active' AND `iDriverId` = '" . $driverId . "'");
            $iDriver_VehicleId = $Data_vehicle[0]['iDriverVehicleId'];
            $vLicencePlate = $Data_vehicle[0]['vLicencePlate'];
            $obj->sql_query("UPDATE register_driver set iDriverVehicleId='" . $iDriver_VehicleId . "' WHERE iDriverId='" . $driverId . "'");
            $Data[0]['iDriverVehicleId'] = $iDriver_VehicleId;
            $Data[0]['vLicencePlateNo'] = $vLicencePlate;
        }
        if ($Data[0]['iDriverVehicleId'] != '' && $Data[0]['iDriverVehicleId'] != '0') {
            //Added By HJ On 11-07-2020 For Optimize driver_vehicle Table Query Start
            if (isset($driverVehicleDataArr['driver_vehicle_' . $Data[0]['iDriverVehicleId']])) {
                $DriverVehicle = $driverVehicleDataArr['driver_vehicle_' . $Data[0]['iDriverVehicleId']];
            }
            else {
                $DriverVehicle = $obj->MySQLSelect("SELECT ma.vMake,mo.vTitle,dv.* FROM driver_vehicle as dv LEFT JOIN make as ma ON dv.iMakeId = ma.iMakeId LEFT JOIN model as mo ON dv.iModelId = mo.iModelId WHERE dv.iDriverVehicleId = '" . $Data[0]['iDriverVehicleId'] . "'");
                $driverVehicleDataArr['driver_vehicle_' . $Data[0]['iDriverVehicleId']] = $DriverVehicle;
            }
            //Added By HJ On 11-07-2020 For Optimize driver_vehicle Table Query End
            //$sql = "SELECT ma.vMake,mo.vTitle FROM driver_vehicle as dv LEFT JOIN make as ma ON dv.iMakeId = ma.iMakeId LEFT JOIN model as mo ON dv.iModelId = mo.iModelId WHERE dv.iDriverVehicleId = '" . $Data[0]['iDriverVehicleId'] . "'";
            //$DriverVehicle = $obj->MySQLSelect($sql);
            $Data[0]['vMake'] = $DriverVehicle[0]['vMake'];
            $Data[0]['vModel'] = $DriverVehicle[0]['vTitle'];
            $vLicencePlate = $DriverVehicle[0]['vLicencePlate'];
            // added
            //$vLicencePlate = get_value('driver_vehicle', 'vLicencePlate', 'iDriverVehicleId', $Data[0]['iDriverVehicleId'], '', 'true');
            $Data[0]['vLicencePlateNo'] = $vLicencePlate;
        }
        //added by SP on 08-09-2020 chk whether ride or any other service is not selected..so in app select vehicle line in home screen will be disabled.
        $Data[0]['isOnlyUfxServicesSelected'] = 'No';
        $sqlUFXVehicle = "SELECT make.vMake, model.vTitle, dv.*, rd.iDriverVehicleId as iSelectedVehicleId FROM `driver_vehicle` dv, make, model, register_driver as rd WHERE dv.iDriverId='$driverId' AND rd.iDriverId='$driverId' AND dv.`iMakeId` = make.`iMakeId` AND dv.`iModelId` = model.`iModelId` AND dv.`eStatus`='Active'";
        $DriverUFXVehicle = $obj->MySQLSelect($sqlUFXVehicle);
        if (count($DriverUFXVehicle) == 1 && $DriverUFXVehicle[0]['eType'] == "UberX" && $DriverUFXVehicle[0]['vCarType'] != "" && $isUfxAvailable == 'Yes') {
            $Data[0]['isOnlyUfxServicesSelected'] = 'Yes';
        }
        if ($APP_TYPE == "UberX") {
            $Data[0]['isOnlyUfxServicesSelected'] = 'Yes';
        }
        if ($Data[0]['eStatus'] == "Deleted") {
            $returnArr['Action'] = "0";
            $returnArr['eStatus'] = $Data[0]['eStatus'];
            $returnArr['message'] = "LBL_ACC_DELETE_TXT";
            setDataResponse($returnArr);
        }
        $TripStatus = $Data[0]['vTripStatus'];
        // $Data[0]['RegistrationDate'] = date("Y-m-d", strtotime($Data[0]['tRegistrationDate'] . ' -1 day '));
        $Data[0]['RegistrationDate'] = date("Y-m-d", strtotime($Data[0]['tRegistrationDate']));
        if ($TripStatus != "NONE") {
            $TripID = $Data[0]['iTripId'];
            $row_result_trips = FetchTripFareDetails($TripID, $driverId, "Driver");

            unset($row_result_trips['DriverDetails']['tLocationUpdateDate']);
            unset($row_result_trips['PassengerDetails']['tLocationUpdateDate']);

            $Data[0]['TripDetails'] = $row_result_trips;
            $Data[0]['PassengerDetails'] = $row_result_trips['PassengerDetails'];
            $Data[0]['eSystem'] = $row_result_trips['eSystem'];
            //Added By HJ On 11-07-2020 For Optimize trip_times Table Query Start
            if (isset($tripDetailsArr["trip_times_" . $TripID])) {
                $db_tripTimes = $tripDetailsArr["trip_times_" . $TripID];
            }
            else {
                $db_tripTimes = $obj->MySQLSelect("SELECT * FROM `trip_times` WHERE iTripId='" . $TripID . "'");
                $tripDetailsArr["trip_times_" . $TripID] = $db_tripTimes;
            }
            //Added By HJ On 11-07-2020 For Optimize trip_times Table Query End
            $totalSec = 0;
            $timeState = 'Pause';
            $iTripTimeId = '';
            foreach ($db_tripTimes as $dtT) {
                if ($dtT['dPauseTime'] != '' && $dtT['dPauseTime'] != '0000-00-00 00:00:00') {
                    $totalSec += strtotime($dtT['dPauseTime']) - strtotime($dtT['dResumeTime']);
                }
                else {
                    $totalSec += strtotime(date('Y-m-d H:i:s')) - strtotime($dtT['dResumeTime']);
                    $iTripTimeId = $dtT['iTripTimeId'];
                    $timeState = 'Resume';
                }
            }
            $Data[0]['iTripTimeId'] = $iTripTimeId;
            $Data[0]['TotalSeconds'] = $totalSec;
            $Data[0]['TimeState'] = $timeState;
            if ($Data[0]['eSystem'] == "DeliverAll") {
                ############################# Food System Ratings From Driver  #############################
                $sql = "SELECT ord.iOrderId,ord.iCompanyId,ord.iUserId,ord.iUserAddressId,ord.fNetTotal,ord.vOrderNo,ord.iStatusCode FROM `orders` as ord WHERE ord.iDriverId='" . $driverId . "' AND ord.iStatusCode = '6' AND (select count(iRatingId) from ratings_user_driver as rud where rud.iOrderId=ord.iOrderId AND rud.eFromUserType = 'Driver' ) = 0  ORDER BY ord.iOrderId DESC LIMIT 0,1";
                $row_order = $obj->MySQLSelect($sql);
                if (empty($row_order) && $MODULES_OBJ->isEnableAcceptMultipleOrders()) {
                    $sql = "SELECT ord.iOrderId,ord.iCompanyId,ord.iUserId,ord.iUserAddressId,ord.fNetTotal,ord.vOrderNo,ord.iStatusCode FROM `orders` as ord WHERE ord.iDriverId='" . $driverId . "' AND ord.iStatusCode IN (4,5,13,14) AND (select count(iRatingId) from ratings_user_driver as rud where rud.iOrderId=ord.iOrderId AND rud.eFromUserType = 'Driver' ) = 0  ORDER BY ord.iOrderId DESC LIMIT 0,1";
                    $row_order = $obj->MySQLSelect($sql);
                }
                if (count($row_order) > 0) {
                    $order_id = $row_order[0]['iOrderId'];
                    if ($MODULES_OBJ->isEnableAcceptMultipleOrders()) {
                        $order_trip_data = $obj->MySQLSelect("SELECT iTripId FROM trips WHERE iOrderId = '" . $order_id . "' ORDER BY iTripId DESC LIMIT 1");
                        $row_result_trips = FetchTripFareDetails($order_trip_data[0]['iTripId'], $driverId, "Driver");
                        $Data[0]['TripDetails'] = $row_result_trips;
                        $Data[0]['PassengerDetails'] = $row_result_trips['PassengerDetails'];
                    }
                    $queryChk = array();
                    if ($MODULES_OBJ->isEnableCancelDriverOrder()) {
                        $queryChk = $obj->MySQLSelect("SELECT * FROM order_driver_log WHERE `iOrderId` = '" . $order_id . "' AND `iDriverId` = '" . $driverId . "'");
                    }
                    if (!empty($queryChk) && count($queryChk) > 0) {
                        $Data[0]['Ratings_From_Driver'] = "";
                    }
                    else {
                        $LastOrderId = $row_order[0]['iOrderId'];
                        $LastOrderCompanyId = $row_order[0]['iCompanyId'];
                        $LastOrderUserId = $row_order[0]['iUserId'];
                        $fNetTotal = $row_order[0]['fNetTotal'];
                        $iUserAddressId = $row_order[0]['iUserAddressId'];
                        $LastOrderNo = $row_order[0]['vOrderNo'];
                        $UserAddressArr = FetchMemberAddressData($LastOrderUserId, "Passenger", $iUserAddressId);
                        $UserAdress = ucfirst($UserAddressArr['UserAddress']);
                        $DriverDetailsArr = getDriverCurrencyLanguageDetails($driverId, $LastOrderId);
                        $vSymbol = $DriverDetailsArr['currencySymbol'];
                        $priceRatio = $DriverDetailsArr['Ratio'];
                        $fNetTotal = round(($fNetTotal * $priceRatio), 2);
                        //Added By HJ On 11-07-2020 For Optimization register_user Table Query Start
                        if (isset($userDetailsArr["register_user_" . $LastOrderUserId]) && count($userDetailsArr["register_user_" . $LastOrderUserId]) > 0) {
                            $result_user = $userDetailsArr["register_user_" . $LastOrderUserId];
                        }
                        else {
                            $result_user = $obj->MySQLSelect("SELECT *,iUserId as iMemberId FROM register_user WHERE iUserId='" . $LastOrderUserId . "'");
                            $userDetailsArr["register_user_" . $LastOrderUserId] = $result_user;
                        }
                        $result_user[0]['UserName'] = $result_user[0]['vName'] . " " . $result_user[0]['vLastName'];
                        //Added By HJ On 11-07-2020 For Optimization register_user Table Query End
                        //$sql = "SELECT CONCAT(vName,' ',vLastName) AS UserName FROM register_user WHERE iUserId = '" . $LastOrderUserId . "'";
                        //$result_user = $obj->MySQLSelect($sql);
                        $row_result_ratings = $obj->MySQLSelect("SELECT count(iRatingId) as TotalRating FROM `ratings_user_driver` WHERE iOrderId='" . $LastOrderId . "' AND eFromUserType = 'Driver' ");
                        $TotalRating = $row_result_ratings[0]['TotalRating'];
                        if ($TotalRating > 0) {
                            $Data[0]['Ratings_From_Driver'] = "Done";
                        }
                        else {
                            $Data[0]['Ratings_From_Driver'] = "Not Done";
                            if ($MODULES_OBJ->isEnableAcceptMultipleOrders()) {
                                if ($row_order[0]['iStatusCode'] == "6") {
                                    $Data[0]['vTripStatus'] = "Finished";
                                }
                            }
                        }
                        $Data[0]['LastOrderId'] = $LastOrderId;
                        $Data[0]['LastOrderCompanyId'] = $LastOrderCompanyId;
                        $Data[0]['LastOrderUserId'] = $LastOrderUserId;
                        $Data[0]['LastOrderUserAddress'] = $UserAdress;
                        $Data[0]['LastOrderUserName'] = $result_user[0]['UserName'];
                        $Data[0]['LastOrderAmount'] = $vSymbol . " " . $fNetTotal;
                        $Data[0]['LastOrderNo'] = $LastOrderNo;
                    }
                }
                else {
                    $Data[0]['Ratings_From_Driver'] = "";
                }
                ############################# Food System Ratings From Driver  #############################
            }
            else {
                ############################# Ride System Ratings From Driver  #############################
                //$sql = "SELECT iTripId,eUserType FROM `ratings_user_driver` WHERE iTripId='$TripID'";
                //$row_result_ratings = $obj->MySQLSelect($sql);
                //Added By HJ On 13-06-2020 For Optimization ratings_user_driver Table Query Start
                $row_result_ratings = array();
                if ($TripID > 0) {
                    if (isset($generalTripRatingDataArr['ratings_user_driver_' . $TripID])) {
                        $getTripRateData = $generalTripRatingDataArr['ratings_user_driver_' . $TripID];
                        for ($r = 0; $r < count($getTripRateData); $r++) {
                            $rateUserType = $getTripRateData[$r]['eUserType'];
                            if (strtoupper($rateUserType) == "DRIVER") {
                                $row_result_ratings[] = $getTripRateData[$r];
                            }
                        }
                    }
                    else {
                        $row_result_ratings = $obj->MySQLSelect("SELECT iTripId,eUserType FROM `ratings_user_driver` WHERE iTripId='" . $TripID . "' AND eUserType='Driver' AND vRating1 != ''");
                    }
                }
                //Added By HJ On 13-06-2020 For Optimization ratings_user_driver Table Query End
                if (count($row_result_ratings) > 0) {
                    $count_row_rating = 0;
                    $ContentWritten = "false";
                    while (count($row_result_ratings) > $count_row_rating) {
                        $UserType = $row_result_ratings[$count_row_rating]['eUserType'];
                        if ($UserType == "Driver") {
                            $ContentWritten = "true";
                            $Data[0]['Ratings_From_Driver'] = "Done";
                        }
                        else if ($ContentWritten == "false") {
                            $Data[0]['Ratings_From_Driver'] = "Not Done";
                        }
                        $count_row_rating++;
                    }
                }
                else {
                    $Data[0]['Ratings_From_Driver'] = "No Entry";
                }
            }
            ############################# Ride System Ratings From Driver  #############################
            $Data[0]['TotalFareUberX'] = "0";
            $Data[0]['TotalFareUberXValue'] = "0";
            $Data[0]['UberXFareCurrencySymbol'] = "";
        }
        ### Driver Order Detail Summury ##
        // $sql = "SELECT iOrderId,iCompanyId,iUserId,iUserAddressId,fNetTotal,vOrderNo FROM `orders` WHERE iDriverId='".$driverId."' AND iStatusCode = '6' ORDER BY iOrderId DESC LIMIT 0,1";
        $row_order = $obj->MySQLSelect("SELECT ord.iOrderId,ord.iCompanyId,ord.iUserId,ord.iUserAddressId,ord.fNetTotal,ord.vOrderNo FROM `orders` as ord WHERE ord.iDriverId='" . $driverId . "' AND ord.iStatusCode = '6' AND (select count(iRatingId) from ratings_user_driver as rud where rud.iOrderId=ord.iOrderId AND rud.eFromUserType = 'Driver' ) = 0  ORDER BY ord.iOrderId DESC LIMIT 0,1");
        if (count($row_order) > 0) {
            $LastOrderId = $row_order[0]['iOrderId'];
            $LastOrderCompanyId = $row_order[0]['iCompanyId'];
            $LastOrderUserId = $row_order[0]['iUserId'];
            $fNetTotal = $row_order[0]['fNetTotal'];
            $iUserAddressId = $row_order[0]['iUserAddressId'];
            $LastOrderNo = $row_order[0]['vOrderNo'];
            $UserAddressArr = FetchMemberAddressData($LastOrderUserId, "Passenger", $iUserAddressId);
            $UserAdress = ucfirst($UserAddressArr['UserAddress']);
            $DriverDetailsArr = getDriverCurrencyLanguageDetails($driverId, $LastOrderId);
            $vSymbol = $DriverDetailsArr['currencySymbol'];
            $priceRatio = $DriverDetailsArr['Ratio'];
            $fNetTotal = round(($fNetTotal * $priceRatio), 2);
            $row_result_ratings = $obj->MySQLSelect("SELECT count(iRatingId) as TotalRating FROM `ratings_user_driver` WHERE iOrderId='" . $LastOrderId . "' AND eFromUserType = 'Driver' ");
            $TotalRating = $row_result_ratings[0]['TotalRating'];
            if ($TotalRating > 0) {
                $Data[0]['Ratings_From_Driver'] = "Done";
            }
            else {
                $Data[0]['Ratings_From_Driver'] = "Not Done";
            }
            //Added By HJ On 11-07-2020 For Optimization register_user Table Query Start
            if (isset($userDetailsArr["register_user_" . $LastOrderUserId]) && count($userDetailsArr["register_user_" . $LastOrderUserId]) > 0) {
                $result_user = $userDetailsArr["register_user_" . $LastOrderUserId];
            }
            else {
                $result_user = $obj->MySQLSelect("SELECT *,iUserId as iMemberId FROM register_user WHERE iUserId='" . $LastOrderUserId . "'");
                $userDetailsArr["register_user_" . $LastOrderUserId] = $result_user;
            }
            $result_user[0]['UserName'] = $result_user[0]['vName'] . " " . $result_user[0]['vLastName'];
            //Added By HJ On 11-07-2020 For Optimization register_user Table Query End
            //$sql = "SELECT CONCAT(vName,' ',vLastName) AS UserName FROM register_user WHERE iUserId = '" . $LastOrderUserId . "'";
            //$result_user = $obj->MySQLSelect($sql);
            $Data[0]['LastOrderId'] = $LastOrderId;
            $Data[0]['LastOrderCompanyId'] = $LastOrderCompanyId;
            $Data[0]['LastOrderUserId'] = $LastOrderUserId;
            $Data[0]['LastOrderUserAddress'] = $UserAdress;
            $Data[0]['LastOrderUserName'] = $result_user[0]['UserName'];
            $Data[0]['LastOrderAmount'] = $vSymbol . " " . $fNetTotal;
            $Data[0]['LastOrderNo'] = $LastOrderNo;
        }
        else {
            $Data[0]['Ratings_From_Driver'] = "";
        }
        ### Driver Order Detail Summury ##
        //Added By HJ On 11-07-2020 For Optimization user_address Table Query Start
        if (isset($userAddressDataArr['user_address_' . $driverId])) {
            $result_Address = $userAddressDataArr['user_address_' . $driverId];
        }
        else {
            $userAddressDataArr = array();
            $result_Address = $obj->MySQLSelect("SELECT * from user_address WHERE iUserId = '" . $driverId . "' AND eStatus = 'Active'");
            $userAddressDataArr['user_address_' . $driverId] = $result_Address;
        }
        $totalAddressCount = 0;
        for ($a = 0; $a < count($result_Address); $a++) {
            $addresUser = $result_Address[$a]['eUserType'];
            if (strtoupper($addresUser) == "DRIVER") {
                $totalAddressCount += 1;
            }
        }
        //Added By HJ On 11-07-2020 For Optimization user_address Table Query End
        //$sql = "SELECT count(iUserAddressId) as ToTalAddress from user_address WHERE iUserId = '" . $driverId . "' AND eUserType = 'Driver' AND eStatus = 'Active'";
        //$result_Address = $obj->MySQLSelect($sql);
        $Data[0]['ToTalAddress'] = $totalAddressCount;
        $Data[0]['ABOUT_US_PAGE_DESCRIPTION'] = "";
        $Data[0]['DefaultCurrencySign'] = $Data[0]["DEFAULT_CURRENCY_SIGN"];
        $Data[0]['DefaultCurrencyCode'] = $Data[0]["DEFAULT_CURRENCY_CODE"];
        $Data[0]['SITE_TYPE'] = SITE_TYPE;
        $Data[0]['RIIDE_LATER'] = RIIDE_LATER;
        $Data[0]['DELIVERALL'] = DELIVERALL;
        $Data[0]['ONLYDELIVERALL'] = ONLYDELIVERALL;
        //Added By HJ On 01-05-2020 For Check Store Driver Start
        $isStoreDriver = "No";
        $Data[0]['UFX_SERVICE_AVAILABLE'] = $MODULES_OBJ->isUfxFeatureAvailable();
        if (strtoupper($Data[0]['eSystem_original']) == "DELIVERALL" && $MODULES_OBJ->isStorePersonalDriverAvailable()) {
            $Data[0]['ONLYDELIVERALL'] = "Yes";
            $isStoreDriver = "Yes";
            $Data[0]['INVITE_DESCRIPTION_CONTENT'] = $LBL_INVITE_FRIEND_SHARE_PREFIX_TXT . " " . $REFERRAL_AMOUNT_USER . " " . $LBL_INVITE_FRIEND_SHARE_POSTFIX_ORDER_TXT;
            $Data[0]['UFX_SERVICE_AVAILABLE'] = "No";//added by SP on 25-01-2021 for store personel driver ufx service is not available
            $Data[0]['ENABLE_BIDDING_SERVICES'] = "No";
            $Data[0]['ENABLE_DRIVER_REWARD_MODULE'] = "No";
        }
        $Data[0]['STORE_PERSONAL_DRIVER'] = $isStoreDriver;
        //Added By HJ On 01-05-2020 For Check Store Driver End
        $Data[0]['SITE_TYPE_DEMO_MSG'] = $demo_site_msg;
        $Data[0]['vLicencePlateNo'] = is_null($Data[0]['vLicencePlateNo']) == false ? $Data[0]['vLicencePlateNo'] : '';
        $Data[0]['FETCH_TRIP_STATUS_TIME_INTERVAL'] = fetchtripstatustimeinterval();
        $Data[0]['ENABLE_TOLL_COST'] = $Data[0]['APP_TYPE'] != "UberX" ? $Data[0]['ENABLE_TOLL_COST'] : "No";
        /* Check Toll Enable For Driver's Country */
        if ($usercountrycode != "") {
            //$sqlc = "SELECT eEnableToll from country WHERE vCountryCode = '" . $usercountrycode . "'";
            //$user_country_toll = $obj->MySQLSelect($sqlc);
            //Added By HJ On 11-07-2020 For Optimization country Table Query Start
            if (isset($country_data_arr[$usercountrycode]['eEnableToll']) && trim($country_data_arr[$usercountrycode]['eEnableToll']) != "") {
                $eEnableToll = $country_data_arr[$usercountrycode]['eEnableToll'];
            }
            else {
                $user_country_toll = $obj->MySQLSelect("SELECT eEnableToll from country WHERE vCountryCode = '" . $usercountrycode . "'");
                $eEnableToll = $user_country_toll[0]['eEnableToll'];
            }
            //Added By HJ On 11-07-2020 For Optimization country Table Query End
            $eEnableToll = $user_country_toll[0]['eEnableToll'];
            if ($eEnableToll != "") {
                $Data[0]['ENABLE_TOLL_COST'] = ($eEnableToll == "Yes" && $Data[0]['ENABLE_TOLL_COST'] == "Yes") ? "Yes" : "No";
            }
        }
        /* Check Toll Enable For Driver's Country */
        if ($Data[0]['APP_TYPE'] == "UberX") {
            $Data[0]['APP_DESTINATION_MODE'] = "None";
            $Data[0]['ENABLE_TOLL_COST'] = $Data[0]['HANDICAP_ACCESSIBILITY_OPTION'] = $Data[0]['FEMALE_RIDE_REQ_ENABLE'] = $Data[0]['ENABLE_HAIL_RIDES'] = "No";
        }
        else {
            // $Data[0]['APP_DESTINATION_MODE'] = "Strict";
        }
        if ($Data[0]['APP_TYPE'] == "Ride" || $Data[0]['APP_TYPE'] == "Ride-Delivery" || $Data[0]['APP_TYPE'] == "Ride-Delivery-UberX") {
            $Data[0]['FEMALE_RIDE_REQ_ENABLE'] = $Data[0]['FEMALE_RIDE_REQ_ENABLE'];
            $Data[0]['HANDICAP_ACCESSIBILITY_OPTION'] = $Data[0]['HANDICAP_ACCESSIBILITY_OPTION'];
        }
        else {
            $Data[0]['FEMALE_RIDE_REQ_ENABLE'] = "No";
            $Data[0]['HANDICAP_ACCESSIBILITY_OPTION'] = "No";
        }
        if ($Data[0]['APP_TYPE'] == "Ride" || $Data[0]['APP_TYPE'] == "Ride-Delivery" || $Data[0]['APP_TYPE'] == "Ride-Delivery-UberX") {
            $Data[0]['ENABLE_HAIL_RIDES'] = $Data[0]['ENABLE_HAIL_RIDES'];
        }
        else {
            $Data[0]['ENABLE_HAIL_RIDES'] = "No";
        }
        if ($Data[0]['APP_PAYMENT_MODE'] == "Card") {
            $Data[0]['ENABLE_HAIL_RIDES'] = "No";
        }
        $Data[0]['PHOTO_UPLOAD_SERVICE_ENABLE'] = $Data[0]['APP_TYPE'] == "UberX" ? $PHOTO_UPLOAD_SERVICE_ENABLE : "No";
        $Data[0]['ENABLE_DELIVERY_MODULE'] = SITE_TYPE == "Demo" ? $Data[0]['eDeliverModule'] : $Data[0]['ENABLE_DELIVERY_MODULE'];
        $Data[0]['PayPalConfiguration'] = $Data[0]['ENABLE_DELIVERY_MODULE'] == "Yes" ? "Yes" : $Data[0]['PAYMENT_ENABLED'];
        // $Data[0]['CurrencyList']=($obj->MySQLSelect("SELECT * FROM currency"));
        //Added By HJ On 11-07-2020 For Optimization currency Table Query Start
        /*$currencyNameArr = $defCurrencyValues = array();
        if (count($Data_ALL_currency_Arr) > 0) {
            for ($c = 0; $c < count($Data_ALL_currency_Arr); $c++) {
                if (strtoupper($Data_ALL_currency_Arr[$c]['eStatus']) == "ACTIVE") {
                    $defCurrencyValues[] = $Data_ALL_currency_Arr[$c];
                    $currencyNameArr[$Data_ALL_currency_Arr[$c]['vName']] = $Data_ALL_currency_Arr[$c];
                }
            }
            $Data[0]['CurrencyList'] = $defCurrencyValues;
        }
        else {
            $Data[0]['CurrencyList'] = get_value('currency', '*', 'eStatus', 'Active');
        }*/
        //Added By HJ On 11-07-2020 For Optimization currency Table Query End
        //$Data[0]['CurrencyList'] = get_value('currency', '*', 'eStatus', 'Active');
        $Data[0]['UBERX_PARENT_CAT_ID'] = $parent_ufx_catid;
        $Data[0]['UBERX_SUB_CAT_ID'] = "0";
        /* As a part of Socket Cluster */
        $Data[0]['MAX_ALLOW_TIME_INTERVAL_MILLI'] = (fetchtripstatustimeMAXinterval() + $intervalmins) * 1000;
        $Data[0]['RIDER_REQUEST_ACCEPT_TIME'] = $RIDER_REQUEST_ACCEPT_TIME;
        /* As a part of Socket Cluster */ // $user_available_balance = $WALLET_OBJ->FetchMemberWalletBalance($driverId,"Driver");
        // $Data[0]['user_available_balance'] = strval($WALLET_OBJ->MemberCurrencyWalletBalance(0,$user_available_balance,$Data[0]['vCurrencyDriver']));
        $user_available_balance = $WALLET_OBJ->FetchMemberWalletBalanceApp($driverId, "Driver");
        $Data[0]['user_available_balance'] = strval($user_available_balance);
        $user_available_balance_value = $WALLET_OBJ->FetchMemberWalletBalanceApp($driverId, "Driver", 'Yes');
        $Data[0]['user_available_balance_value'] = strval($user_available_balance_value);
        if ($user_available_balance_value <= 0 || $user_available_balance_value <= 0.00) {
            $Data[0]['eWalletBalanceAvailable'] = 'No';
        }
        else {
            $Data[0]['eWalletBalanceAvailable'] = 'Yes';
        }
        //Added By HJ On 11-07-2020 For Optimization currency Table Query Start
        $vCurrencyDriver = $Data[0]['vCurrencyDriver'];
        if (isset($currencyNameArr[$vCurrencyDriver]['vSymbol']) && trim($currencyNameArr[$vCurrencyDriver]['vSymbol']) != "") {
            $CurrencySymbol = $currencyNameArr[$vCurrencyDriver]['vSymbol'];
        }
        else {
            $CurrencySymbol = get_value('currency', 'vSymbol', 'vName', $vCurrencyDriver, '', 'true');
        }
        $Data[0]['CurrencySymbol'] = $CurrencySymbol;
        //Added By HJ On 11-07-2020 For Optimization currency Table Query End
        //$Data[0]['CurrencySymbol'] = get_value('currency', 'vSymbol', 'vName', $Data[0]['vCurrencyDriver'], '', 'true');
        $currencydata = get_value('currency', 'eReverseformattingEnable,eReverseSymbolEnable', 'vName', $vCurrencyDriver, '', '');
        $Data[0]['eReverseformattingEnable'] = $currencydata[0]['eReverseformattingEnable'];
        $Data[0]['eReverseSymbolEnable'] = $currencydata[0]['eReverseSymbolEnable'];
        /*$str_date = @date('Y-m-d H:i:s', strtotime('-1 minutes'));
        $data_requst = $obj->MySQLSelect("SELECT * FROM passenger_requests WHERE iDriverId='" . $driverId . "' AND dAddedDate > '" . $str_date . "'");
        $Data[0]['CurrentRequests'] = $data_requst;*/
        $db_driver_fav_address = $obj->MySQLSelect("SELECT * FROM user_fave_address where iUserId = '" . $driverId . "' AND eUserType = 'Driver' AND eStatus = 'Active' ORDER BY iUserFavAddressId ASC");
        $Data[0]['UserFavouriteAddress'] = $db_driver_fav_address;
        $usercountrydetailbytimezone = FetchMemberCountryData($driverId, "Driver", $vTimeZone, $vUserDeviceCountry);
        $Data[0]['vDefaultCountry'] = $usercountrydetailbytimezone['vDefaultCountry'];
        $Data[0]['vDefaultCountryCode'] = $usercountrydetailbytimezone['vDefaultCountryCode'];
        $Data[0]['vDefaultPhoneCode'] = $usercountrydetailbytimezone['vDefaultPhoneCode'];
        $Data[0]['vRCountryImage'] = $usercountrydetailbytimezone['vRImageMember']; //added by SP for country image related changes on 05-08-2019
        $Data[0]['vSCountryImage'] = $usercountrydetailbytimezone['vSImageMember']; //added by SP for country image related changes on 05-08-2019
        $Data[0]['vDefaultCountryImage'] = empty($Data[0]['vSCountryImage']) ? $usercountrydetailbytimezone['vDefaultCountryImage'] : $Data[0]['vSCountryImage']; //added by SP for country image related changes on 06-08-2019
        $Data[0]['vCode'] = empty($Data[0]['vCode']) ? $Data[0]['vDefaultPhoneCode'] : $Data[0]['vCode'];
        $Data[0]['vCountry'] = empty($Data[0]['vCountry']) ? $Data[0]['vDefaultCountryCode'] : $Data[0]['vCountry'];
        $SITE_POLICE_CONTROL_NUMBER = getMemberCountryPoliceNumber($driverId, "Driver", $Data[0]['vCountry']);
        $Data[0]['SITE_POLICE_CONTROL_NUMBER'] = $SITE_POLICE_CONTROL_NUMBER;
        $Data[0]['MONGO_DB'] = $tconfig['tmongodb_databse'];
        $Data[0]['MONGO_DB_CONNECTION_PORT'] = $tconfig['tmongodb_port'];
        $Data[0]['SERVER_DEFAULT_TIMEZONE'] = date_default_timezone_get();
        if ($Data[0]['APP_TYPE'] == "Ride-Delivery-UberX") {
            $checkridedelivery = CheckRideDeliveryFeatureDisable();
            $Data[0]['eShowRideVehicles'] = $checkridedelivery['eShowRideVehicles'];
            $Data[0]['eShowDeliveryVehicles'] = $checkridedelivery['eShowDeliveryVehicles'];
            $Data[0]['ENABLE_MULTI_DELIVERY'] = ENABLE_MULTI_DELIVERY;
        }
        $Data[0]['ENABLE_ADD_PROVIDER_FROM_STORE'] = $MODULES_OBJ->isStorePersonalDriverAvailable() ? 'Yes' : 'No';
        $Data[0]['tsite_upload_docs_file_extensions'] = $tconfig['tsite_upload_docs_file_extensions'];
        $Data[0]['tsite_upload_image_file_extensions'] = $tconfig['tsite_upload_image_file_extensions'];
        $Data[0]['tsite_upload_video_file_extensions'] = $tconfig['tsite_upload_video_file_extensions'];
        $Data[0]['SC_CONNECT_URL'] = getSocketURL();
        $Data[0]['APP_SERVICE_URL'] = APP_SERVICE_URL;
        $Data[0]['GOOGLE_API_REPLACEMENT_URL'] = GOOGLE_API_REPLACEMENT_URL;
        $Data[0]['TSITE_DB'] = TSITE_DB;
        //Added By HJ On 16-07-2019 For Check Multiple Country Exists Or Not Start
        //$getCountryData = $obj->MySQLSelect("SELECT iCountryId FROM country WHERE eStatus='Active'");
        // Added By HJ On 11-07-2020 For Optimized country Table Query Start
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
        // Added By HJ On 11-07-2020 For Optimized country Table Query End
        $multiCountry = "No";
        if (count($getCountryData) > 1) {
            $multiCountry = "Yes";
        }
        $Data[0]['showCountryList'] = $multiCountry;
        //Added By HJ On 16-07-2019 For Check Multiple Country Exists Or Not End
        //Added By Hasmukh On 25-12-2018 For Get Advertise Banner Data Start
        if ($MODULES_OBJ->checkSharkPackage() && $Data[0]['eStatus'] == "active") {
            $Data[0]['advertise_banner_data'] = getAdvertisementBannersAsPerDevice($driverId, "Driver");
        }
        //Added By Hasmukh On 25-12-2018 For Get Advertise Banner Data End
        //Added By HJ On 11-07-2020 For Optimization configurations_payment Table Query Start
        if (isset($generalConfigPaymentArr['ENABLE_GOPAY']) && trim($generalConfigPaymentArr['ENABLE_GOPAY']) != "") {
            $EnableGopay = trim($generalConfigPaymentArr['ENABLE_GOPAY']);
        }
        else {
            $EnableGopay = get_value('configurations_payment', 'vValue', 'vName', 'ENABLE_GOPAY'); //added by SP for Gojek-gopay
        }
        //Added By HJ On 11-01-2020 For Optimization configurations_payment Table Query End
        if (!empty($EnableGopay[0]['vValue'])) {
            $Data[0]['ENABLE_GOPAY'] = $EnableGopay[0]['vValue'];
        }
        else if (!empty($EnableGopay)) {
            $Data[0]['ENABLE_GOPAY'] = $EnableGopay;
        }
        else {
            $Data[0]['ENABLE_GOPAY'] = '';
        }

        $Data[0]['PAYMENT_BASE_URL'] = $tconfig['tsite_url'] . 'assets/libraries/webview/system_payment.php?';
        $Data[0]['PAYMENT_MODE_URL'] = $tconfig['tsite_url'] . 'assets/libraries/webview/payment_mode_select.php?';

        $Data[0]['RANDOM_COLORS_KEY_VAL_ARR'] = RANDOM_COLORS_KEY_VAL_ARR;
        $Data[0]['PAYMENT_BASE_URL'] = $tconfig['tsite_url'] . 'assets/libraries/webview/system_payment.php?';
        $Data[0]['UFX_SERVICE_AVAILABLE'] = $MODULES_OBJ->isUfxFeatureAvailable();
        $Data[0]['AUTH_EMAIL_SYSTEM'] = AUTH_EMAIL_SYSTEM;
        $Data[0]['ENABLE_TAKE_AWAY'] = ($MODULES_OBJ->isTakeAwayEnable()) ? "Yes" : "No";
        $Data[0]['ENABLE_ITEM_SEARCH_STORE_ORDER'] = ($MODULES_OBJ->isEnableItemSearchStoreOrder()) ? "Yes" : "No";
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
        $Data[0]['PICK_DROP_GENIE'] = PICK_DROP_GENIE; // Added by HV on 12-10-2020 for Genie Pickup/Dropoff Items
        $Data[0]['ENABLE_DELIVERY_INSTRUCTIONS_ORDERS'] = !empty($MODULES_OBJ->isEnableVoiceDeliveryInstructionsOrder()) ? "Yes" : "No";
        if ($Data[0]['iCompanyId'] > 1) {
            $Data[0]['ENABLE_SAFETY_CHECKLIST'] = "No";
            $Data[0]['WAYBILL_ENABLE'] = "No";
            $Data[0]['DRIVER_SUBSCRIPTION_ENABLE'] = "No";
        }
        if (!$MODULES_OBJ->isDriverSubscriptionModuleAvailable()) {
            $Data[0]['DRIVER_SUBSCRIPTION_ENABLE'] = "No";
        }
        // Added by HV for Restrict Passenger Limit Feature
        $Data[0]['RESTRICT_PASSENGER_LIMIT_INFO_URL'] = $tconfig['tsite_url'] . 'safety_checklist.php?iPageId=56&vLang=' . $vLanguage;
        //UpdateAppTerminateStatus($driverId, "Driver");
        $Data[0]['APP_LAUNCH_IMAGES'] = "";
        if (!empty(getAppLaunchImages($vLanguage, 'Driver'))) {
            $Data[0]['APP_LAUNCH_IMAGES'] = getAppLaunchImages($vLanguage, 'Driver');
        }
        $Data[0]['ENABLE_LOCATION_WISE_BANNER'] = ($MODULES_OBJ->isEnableLocationwiseBanner()) ? "Yes" : "No";
        /* Default card according to Payment Gateway*/
        $countryPaymentMethod = $obj->MySQLSelect("SELECT vPaymentGateway FROM country WHERE vCountryCode = '" . $Data[0]['vCountry'] . "'");
        $USER_APP_PAYMENT_METHOD = $APP_PAYMENT_METHOD;
        if (!empty($countryPaymentMethod[0]['vPaymentGateway'])) {
            $USER_APP_PAYMENT_METHOD = $countryPaymentMethod[0]['vPaymentGateway'];
        }
        $userPaymentInfo = getPaymentDefaultCard($driverId, 'Driver');
        $Data[0]['vCreditCard'] = (count($userPaymentInfo) > 0) ? $userPaymentInfo[0]['tCardNum'] : "";
        $Data[0]['ENABLE_APPLE_LOGIN_FOR_PROVIDER'] = ($MODULES_OBJ->isEnableAppleLoginForProvider()) ? "Yes" : "No";
        $Data[0]['isSmartLoginEnable'] = $MODULES_OBJ->isEnableSmartLogin() ? "Yes" : "No";
        $Data[0]['WEBRTC_SOCKET_URL'] = WEBRTC_SOCKET_URL;
        $Data[0]['WEBRTC_STUN_URL'] = WEBRTC_STUN_URL;
        $Data[0]['WEBRTC_TURN_URL'] = WEBRTC_TURN_URL;
        $Data[0]['WEBRTC_ICE_SERVER_LIST'] = WEBRTC_ICE_SERVER_LIST;
        $Data[0]['WEBRTC_USERNAME'] = $tconfig["tsite_webrtc_username"];
        $Data[0]['WEBRTC_PASS'] = $tconfig["tsite_webrtc_pass"];

        $Data[0]['RIDE_ENABLED'] = "No";
        $Data[0]['DELIVERY_ENABLED'] = "No";
        $Data[0]['UFX_ENABLED'] = "No";
        $Data[0]['DELIVERALL_ENABLED'] = "Yes";
        $Data[0]['GENIE_ENABLED'] = "No";
        $Data[0]['RUNNER_ENABLED'] = "No";
        $Data[0]['BIDDING_ENABLED'] = "No";
        $Data[0]['VC_ENABLED'] = "No";
        $Data[0]['MED_UFX_ENABLED'] = "No";
        $Data[0]['RENT_ITEM_ENABLED'] = "No";
        $Data[0]['RENT_ESTATE_ENABLED'] = "No";
        $Data[0]['RENT_CARS_ENABLED'] = "No";
        $Data[0]['NEARBY_ENABLED'] = "No";
        $Data[0]['TRACK_SERVICE_ENABLED'] = "No";
        $Data[0]['RIDE_SHARE_ENABLED'] = "No";

        $Data[0]['SERVICE_CATEGORIES_ARR'] = SERVICE_CATEGORIES_ARR;
        if ($MODULES_OBJ->isEnableGiftCardFeature()) {
            $Data[0]['GIFT_CARD_IMAGES'] = $GIFT_CARD_OBJ->getGiftCardImages();
            $Data[0]['GIFT_CARD_MAX_AMOUNT_WITH_SYMBOL'] = formateNumAsPerCurrency($Data[0]['GIFT_CARD_MAX_AMOUNT'], $vCurrencyDriver);
            $Data[0]['PREVIEW_GIFT_CARD_URL'] = $tconfig['tsite_url'] . 'preview_gift_card.php?';
            $Data[0]['TERMS_&_CONDITIONS_GIFT_CARD_URL'] = $tconfig['tsite_url'] . 'terms_conditions_gift_card.php';
        }
 
        if ($CONFIG_OBJ->isOnlyCashPaymentModeAvailable() || $Data[0]['WALLET_ENABLE'] == 'No') {
            $Data[0]['ENABLE_GIFT_CARD_FEATURE'] = 'No';
        }

        $Data[0]['ENABLE_GOOGLE_ADS'] = "No";

        $Data[0]['ENABLE_PIP_MODE'] = ENABLE_PIP_MODE;

     /*    $Data[0]['LOCATION_BATCH_TASK_DURATION'] = fetchTaskInterval('LOCATION_BATCH_TASK_DURATION');
        $Data[0]['PROVIDER_STATUS_TASK_DURATION'] = fetchTaskInterval('PROVIDER_STATUS_TASK_DURATION'); */
        $Data[0]['CARD_SAVE_ENABLE'] = isEnableAddCard()['CARD_SAVE_ENABLE'];

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

function getPassengerDetailInfo($passengerID, $cityName = "", $LiveTripId = "")
{
    if (strtoupper(ONLYDELIVERALL) == "YES") {
        return getPassengerDetailInfoDeliverAll($passengerID, $cityName,$LiveTripId );
    } else {
        return getPassengerDetailInfoGeneral($passengerID, $cityName,$LiveTripId);
    }
}


function getPassengerDetailInfoGeneral($passengerID, $cityName = "", $LiveTripId = "")
{
    global $CONFIG_OBJ, $GIFT_CARD_OBJ, $langLabels, $MAXIMUM_HOURS_LATER_BOOKING, $MINIMUM_HOURS_LATER_BOOKING, $MAXIMUM_HOURS_LATER_BIDDING, $MINIMUM_HOURS_LATER_BIDDING, $obj, $demo_site_msg, $PHOTO_UPLOAD_SERVICE_ENABLE, $parent_ufx_catid, $generalSystemConfigDataArr, $tconfig, $vTimeZone, $vUserDeviceCountry, $intervalmins, $generalConfigPaymentArr, $ENABLE_RIDER_ADVERTISEMENT_BANNER, $ADVERTISEMENT_TYPE, $isUfxAvailable, $iServiceId, $vSystemDefaultCurrencyName, $vSystemDefaultCurrencySymbol, $Data_ALL_currency_Arr, $country_data_retrieve, $country_data_arr, $generalTripRatingDataArr, $userAddressDataArr, $vehicleCategoryDataArr, $languageLabelDataArr, $currencyAssociateArr, $ENABLE_OTHER_CHARGES_FEATURE, $ENABLE_MANUAL_TOLL_FEATURE, $APP_PAYMENT_METHOD, $MODULES_OBJ, $WALLET_OBJ, $LANG_OBJ, $TRACK_ANY_SERVICE_OBJ, $SITE_TYPE;

    $where = " iUserId = '" . $passengerID . "'";
    $tblName = "register_user";
    $data_version['iAppVersion'] = "2";
    $data_version['eLogout'] = 'No';
    $data_version['eDebugMode'] = isset($_REQUEST["IS_DEBUG_MODE"]) ? $_REQUEST["IS_DEBUG_MODE"] : "";
    $data_version['tApiFileName'] = pathinfo(__FILE__, PATHINFO_FILENAME);
    #################################### Generate Session For GeoAPI ########################################
    generateSessionForGeo($passengerID, "Passenger");
    #################################### Generate Session For GeoAPI ########################################
    #################################### Configure App Version Info ########################################
    $arr_app_version = array();
    $arr_app_version['AppVersionName'] = isset($_REQUEST['GeneralAppVersion']) ? $_REQUEST['GeneralAppVersion'] : "";
    $arr_app_version['AppVersionCode'] = isset($_REQUEST['GeneralAppVersionCode']) ? $_REQUEST['GeneralAppVersionCode'] : "";
    #################################### Configure App Version Info ########################################
    $data_version['tVersion'] = strval(json_encode($arr_app_version));
    $data_version['tDeviceData'] = isset($_REQUEST['DEVICE_DATA']) ? $_REQUEST['DEVICE_DATA'] : "";
    $data_version['eHmsDevice'] = isset($_REQUEST['HMS_DEVICE']) ? $_REQUEST['HMS_DEVICE'] : "No";
    $obj->MySQLQueryPerform($tblName, $data_version, 'update', $where);
    $obj->sql_query("UPDATE trip_status_messages SET eReceived='Yes' WHERE iUserId='" . $passengerID . "' AND eToUserType='Passenger'");
    $row = $obj->MySQLSelect("SELECT *,iUserId as iMemberId FROM " . $tblName . " WHERE iUserId='" . $passengerID . "'");
    $userDetailsArr[$tblName . "_" . $passengerID] = $row;
    if ($LiveTripId != "") {
        //Added By HJ On 10-06-2020 For Optimization trips Table Query Start
        if (isset($tripDetailsArr['trips_' . $LiveTripId])) {
            $userlivetripdetails = $tripDetailsArr['trips_' . $LiveTripId];
        } else {
            $userlivetripdetails = $obj->MySQLSelect("SELECT * FROM `trips` WHERE iTripId = '" . $LiveTripId . "'");
            $tripDetailsArr['trips_' . $LiveTripId] = $userlivetripdetails;
        }
        //Added By HJ On 10-06-2020 For Optimization trips Table Query End
        //$sql_livetrip = "SELECT iTripId,iActive,vTripPaymentMode,iVehicleTypeId,fPickUpPrice,fNightPrice,vCouponCode,eType FROM `trips` WHERE iTripId='" . $LiveTripId . "'";
        //$userlivetripdetails = $obj->MySQLSelect($sql_livetrip);
        if (count($userlivetripdetails) > 0) {
            $row[0]['iTripId'] = $userlivetripdetails[0]['iTripId'];
            $row[0]['vTripStatus'] = $userlivetripdetails[0]['iActive'];
            $row[0]['vTripPaymentMode'] = $userlivetripdetails[0]['vTripPaymentMode'];
            $row[0]['iSelectedCarType'] = $userlivetripdetails[0]['iVehicleTypeId'];
            $row[0]['fPickUpPrice'] = $userlivetripdetails[0]['fPickUpPrice'];
            $row[0]['fNightPrice'] = $userlivetripdetails[0]['fNightPrice'];
            $row[0]['vCouponCode'] = $userlivetripdetails[0]['vCouponCode'];
            $row[0]['eType'] = $userlivetripdetails[0]['eType'];
        }
        if (strtoupper(PACKAGE_TYPE) == "SHARK") {
            $row[0]['liveTrackingUrl'] = getTrackingUrl($LiveTripId);
        }
    }
    if (count($row) > 0) {
        ### Update Tripid - 0 and TripStatus - None For UberX Trip ###
        $currenttrip = $row[0]['iTripId'];
        if ($currenttrip > 0) {
            //Added By HJ On 10-06-2020 For Optimization trips Table Query Start
            if (isset($tripDetailsArr['trips_' . $currenttrip])) {
                $db_currenttrip = $tripDetailsArr['trips_' . $currenttrip];
            } else {
                $db_currenttrip = $obj->MySQLSelect("SELECT * FROM `trips` WHERE iTripId = '" . $currenttrip . "'");
                $tripDetailsArr['trips_' . $currenttrip] = $db_currenttrip;
            }
            //Added By HJ On 10-06-2020 For Optimization trips Table Query End
            if (count($db_currenttrip) > 0) {
                $currenttriptype = $db_currenttrip[0]['eType'];
                $currenttripsystem = $db_currenttrip[0]['eSystem'];
                if (($currenttriptype == "UberX" || $currenttriptype == "Multi-Delivery") && $LiveTripId == "") {
                    $update_sql = "UPDATE " . $tblName . " set iTripId = '0',vTripStatus = 'NONE' WHERE iUserId ='" . $passengerID . "'";
                    $result = $obj->sql_query($update_sql);
                    $row[0]['vTripStatus'] = "NONE";
                    $row[0]['iTripId'] = 0;
                }
                if ($currenttripsystem == "DeliverAll") {
                    $row[0]['vTripStatus'] = "NONE";
                    $row[0]['iTripId'] = 0;
                }
            }
            if (strtoupper(PACKAGE_TYPE) == "SHARK" && empty($LiveTripId)) {
                $row[0]['liveTrackingUrl'] = getTrackingUrl($currenttrip);
            }
        }
        //Added By HJ On 08-06-2020 For Optimization currency Table Query Start
        if (!empty($vSystemDefaultCurrencyName) && !empty($vSystemDefaultCurrencySymbol)) {
            $row[0]['vFlutterwaveCurrency'] = $vSystemDefaultCurrencyName;
            $vFlutterwavevSymbol = $vSystemDefaultCurrencySymbol;
        } else {
            $defaultCurrencyDataArr = get_value('currency', 'vName,vSymbol', 'eDefault', 'Yes');
            /* Added By PM On 09-12-2019 For Flutterwave Code Start */
            $row[0]['vFlutterwaveCurrency'] = $defaultCurrencyDataArr[0]['vName'];
            $vFlutterwavevSymbol = $defaultCurrencyDataArr[0]['vSymbol'];
        }
        //Added By HJ On 08-06-2020 For Optimization currency Table Query End
        /* Added By PM On 09-12-2019 For Flutterwave Code End */

        ### Update Tripid - 0 and TripStatus - None For UberX Trip ###
        $page_link = $tconfig['tsite_url'] . "sign-up_rider.php?UserType=Rider&vRefCode=" . $row[0]['vRefCode'];
        $link = get_tiny_url($page_link);
        //$activation_text = '<a href="'.$link.'" target="_blank"> '.$link.' </a>';
        $activation_text = "<a href='" . $link . "' target='_blank'> '" . $link . "' </a>";
        $vLanguage = $row[0]['vLang'];
        if ($vLanguage == "" || $vLanguage == NULL) {
            //Added By HJ On 17-06-2020 For Optimize language_master Table Query Start
            $vLanguage = $LANG_OBJ->FetchDefaultLangData("vCode");
            //Added By HJ On 17-06-2020 For Optimize language_master Table Query End
        }
        //Added By HJ On 25-06-2020 For langauge labele and Other Union Table Query Start
        if (isset($languageLabelDataArr['language_label_union_other_' . $vLanguage])) {
            $langLabels = $languageLabelDataArr['language_label_union_other_' . $vLanguage];
        } else {
            $langLabels = $LANG_OBJ->FetchLanguageLabels($vLanguage, "1");
            $languageLabelDataArr['language_label_union_other_' . $vLanguage] = $langLabels;
        }
        //Added By HJ On 25-06-2020 For langauge labele and Other Union Table Query End
        if (isset($langLabels['LBL_SHARE_CONTENT_PASSENGER']) && trim($langLabels['LBL_SHARE_CONTENT_PASSENGER']) != "") {
            $LBL_SHARE_CONTENT_PASSENGER = $langLabels['LBL_SHARE_CONTENT_PASSENGER'];
        } else {
            $db_label = $obj->MySQLSelect("SELECT * FROM `language_label` WHERE vLabel = 'LBL_SHARE_CONTENT_PASSENGER' AND vCode = '" . $vLanguage . "'");
            $LBL_SHARE_CONTENT_PASSENGER = $db_label[0]['vValue'];
        }
        //Added By HJ On 09-07-2020 For Optimize currency Table Query Start
        $vCurrencyPassenger = $row[0]['vCurrencyPassenger'];
        if (isset($currencyAssociateArr[$vCurrencyPassenger])) {
            $userCurrencyRatio = $currencyAssociateArr[$vCurrencyPassenger]['Ratio'];
            $userCurrencySymbol = $currencyAssociateArr[$vCurrencyPassenger]['vSymbol'];
        } else {
            $userCurrencyData = $obj->MySQLSelect("SELECT vSymbol,vName,Ratio FROM currency WHERE vName='" . $vCurrencyPassenger . "'");
            $userCurrencySymbol = $userCurrencyData[0]['vSymbol'];
            $userCurrencyRatio = $userCurrencyData[0]['Ratio'];
        }
        //Added By HJ On 09-07-2020 For Optimize currency Table Query End
        $row[0]['INVITE_SHARE_CONTENT'] = $LBL_SHARE_CONTENT_PASSENGER . " " . $link;
        foreach ($generalSystemConfigDataArr as $key => $value) {
            if (is_null($generalSystemConfigDataArr[$key]) || empty($generalSystemConfigDataArr[$key])) {
                $generalSystemConfigDataArr[$key] = "";
            }
            if (in_array($key, ["TIP_AMOUNT_1", "TIP_AMOUNT_2", "TIP_AMOUNT_3"])) {
                if ($generalSystemConfigDataArr['DELIVERY_TIP_AMOUNT_TYPE_DELIVERALL'] == "Flat") {
                    $generalSystemConfigDataArr[$key] = round($value * $userCurrencyRatio);
                } else {
                    $generalSystemConfigDataArr[$key] = $value . '%';
                }
            }

            if(in_array($key, ["WALLET_FIXED_AMOUNT_1", "WALLET_FIXED_AMOUNT_2", "WALLET_FIXED_AMOUNT_3"])) {
                $generalSystemConfigDataArr[$key] = round($value * $userCurrencyRatio);
            }
        }
        $row[0] = array_merge($row[0], $generalSystemConfigDataArr); // Added By HJ On 18-03-2020 For Optimized Function
        if (isset($_REQUEST['APP_TYPE']) && $_REQUEST['APP_TYPE'] != "") {
            $row[0]['APP_TYPE'] = $_REQUEST['APP_TYPE'];
        }
        $row[0]['GOOGLE_ANALYTICS'] = "";
        $row[0]['SERVER_MAINTENANCE_ENABLE'] = $row[0]['MAINTENANCE_APPS'];
        //Added By HJ On 16-07-2019 For Check Empty and # Value Of Configuration Start
        if (isset($row[0]['LIVE_CHAT_LICENCE_NUMBER']) && ($row[0]['LIVE_CHAT_LICENCE_NUMBER'] == "" || strpos($row[0]['LIVE_CHAT_LICENCE_NUMBER'], '#') !== false)) {
            $row[0]['ENABLE_LIVE_CHAT'] = "No";
        }
        $usercountrycode = "";
        if (isset($row[0]['AUDIO_CALLING_METHOD']) && strtoupper($row[0]['AUDIO_CALLING_METHOD']) == "SINCH") {
            if (isset($row[0]['SINCH_APP_ENVIRONMENT_HOST']) && ($row[0]['SINCH_APP_ENVIRONMENT_HOST'] == "" || strpos($row[0]['SINCH_APP_ENVIRONMENT_HOST'], '#') !== false)) {
                $row[0]['RIDE_DRIVER_CALLING_METHOD'] = "Normal";
            }
            if (isset($row[0]['SINCH_APP_KEY']) && ($row[0]['SINCH_APP_KEY'] == "" || strpos($row[0]['SINCH_APP_KEY'], '#') !== false)) {
                $row[0]['RIDE_DRIVER_CALLING_METHOD'] = "Normal";
            }
            if (isset($row[0]['SINCH_APP_SECRET_KEY']) && ($row[0]['SINCH_APP_SECRET_KEY'] == "" || strpos($row[0]['SINCH_APP_SECRET_KEY'], '#') !== false)) {
                $row[0]['RIDE_DRIVER_CALLING_METHOD'] = "Normal";
            }
            //Added By HJ On 16-07-2019 For Check Empty and # Value Of Configuration End
            //Added By HJ On 21-10-2020 For Enable/Disable Sinch Base On Country Configuration Start
            $usercountrycode = $row[0]['vCountry'];
            if ($usercountrycode != "") {
                $eEnableSinch = checkCountryVoipMethod($usercountrycode);
                if (strtoupper($eEnableSinch) == "NO") {
                    $row[0]['RIDE_DRIVER_CALLING_METHOD'] = "Normal";
                }
            }
            //Added By HJ On 21-10-2020 For Enable/Disable Sinch Base On Country Configuration End
        }
        $RIDER_EMAIL_VERIFICATION = $row[0]["RIDER_EMAIL_VERIFICATION"];
        $RIDER_PHONE_VERIFICATION = $row[0]["RIDER_PHONE_VERIFICATION"];
        $maxHoursBid = convertMinToHoursToDays($MAXIMUM_HOURS_LATER_BIDDING, 'Hours');
        $minHoursBid = convertMinToHoursToDays($MINIMUM_HOURS_LATER_BIDDING, 'Hours');
        $row[0]['LBL_INVALID_BIDDING_MAX_NOTE_MSG'] = str_replace(['#####', '####'], [$maxHoursBid, $minHoursBid], $langLabels['LBL_INVALID_BIDDING_MAX_NOTE_MSG']);
        $minHoursBid = convertMinToHoursToDays($MINIMUM_HOURS_LATER_BOOKING, 'Minutes');
        $maxHoursBid = convertMinToHoursToDays($MAXIMUM_HOURS_LATER_BOOKING, 'Minutes');
        $row[0]['LBL_INVALID_PICKUP_MAX_NOTE_MSG'] = str_replace(['#####', '####'], [$maxHoursBid, $minHoursBid], $langLabels['LBL_INVALID_PICKUP_MAX_NOTE_MSG']);
        if ($row[0]['MULTI_LEVEL_REFERRAL_SCHEME_ENABLE'] == "Yes") {
            //added by SP on 03-03-2021 when multilevel then only shows text not amt..becoz it will not be calculated here..
            if (isset($langLabels['LBL_REFERRAL_AMOUNT']) && trim($langLabels['LBL_REFERRAL_AMOUNT']) != "") {
                $LBL_REFERRAL_AMOUNT = $langLabels['LBL_REFERRAL_AMOUNT'];
            } else {
                $LBL_REFERRAL_AMOUNT = get_value('language_label', 'vValue', 'vLabel', 'LBL_REFERRAL_AMOUNT', " and vCode='" . $vLanguage . "'", 'true');
            }
            $REFERRAL_AMOUNT_USER = $LBL_REFERRAL_AMOUNT;
        } else {
            $REFERRAL_AMOUNT = $row[0]["REFERRAL_AMOUNT"];
            $REFERRAL_AMOUNT_ARR = getPriceUserCurrency($passengerID, "Passenger", $REFERRAL_AMOUNT);
            $REFERRAL_AMOUNT_USER = $REFERRAL_AMOUNT_ARR['fPricewithsymbol'];
        }
        if (isset($langLabels['LBL_INVITE_FRIEND_SHARE_PREFIX_TXT']) && trim($langLabels['LBL_INVITE_FRIEND_SHARE_PREFIX_TXT']) != "") {
            $LBL_INVITE_FRIEND_SHARE_PREFIX_TXT = $langLabels['LBL_INVITE_FRIEND_SHARE_PREFIX_TXT'];
        } else {
            $LBL_INVITE_FRIEND_SHARE_PREFIX_TXT = get_value('language_label', 'vValue', 'vLabel', 'LBL_INVITE_FRIEND_SHARE_PREFIX_TXT', " and vCode='" . $vLanguage . "'", 'true');
        }
        if (isset($langLabels['LBL_INVITE_FRIEND_SHARE_POSTFIX_TXT']) && trim($langLabels['LBL_INVITE_FRIEND_SHARE_POSTFIX_TXT']) != "") {
            $LBL_INVITE_FRIEND_SHARE_POSTFIX_TXT = $langLabels['LBL_INVITE_FRIEND_SHARE_POSTFIX_TXT'];
        } else {
            $LBL_INVITE_FRIEND_SHARE_POSTFIX_TXT = get_value('language_label', 'vValue', 'vLabel', 'LBL_INVITE_FRIEND_SHARE_POSTFIX_TXT', " and vCode='" . $vLanguage . "'", 'true');
        }
        $row[0]['INVITE_DESCRIPTION_CONTENT'] = $LBL_INVITE_FRIEND_SHARE_PREFIX_TXT . " " . $REFERRAL_AMOUNT_USER . " " . $LBL_INVITE_FRIEND_SHARE_POSTFIX_TXT;
        if ($RIDER_EMAIL_VERIFICATION == 'No') {
            $row[0]['eEmailVerified'] = "Yes";
        }
        /*for email optional*/
        if ($row[0]['vEmail'] == "") {
            $row[0]['eEmailVerified'] = "Yes";
        }
        if ($RIDER_PHONE_VERIFICATION == 'No') {
            $row[0]['ePhoneVerified'] = "Yes";
        }
        $row[0]['PACKAGE_TYPE'] = strtoupper(PACKAGE_TYPE);
        $lang_usr = $row[0]['vLang'];
        $sql = "select vBody_$lang_usr as Message from send_message_templates where vEmail_Code = 'VERIFICATION_CODE_MESSAGE'";
        $data_SMS = $obj->MySQLSelect($sql);
        $row[0]['SMS_BODY'] = $data_SMS[0]['Message'];
        ## Display Braintree Charge Message ##
        /*if (isset($langLabels['LBL_BRAINTREE_CHARGE_MSG_TXT']) && trim($langLabels['LBL_BRAINTREE_CHARGE_MSG_TXT']) != "") {
            $LBL_BRAINTREE_CHARGE_MSG_TXT = $langLabels['LBL_BRAINTREE_CHARGE_MSG_TXT'];
        } else {
            $db_label_braintree = $obj->MySQLSelect("SELECT * FROM `language_label` WHERE vLabel = 'LBL_BRAINTREE_CHARGE_MSG_TXT' AND vCode = '" . $vLanguage . "'");
            $LBL_BRAINTREE_CHARGE_MSG_TXT = $db_label_braintree[0]['vValue'];
        }
        $BRAINTREE_CHARGE_AMOUNT = $row[0]['BRAINTREE_CHARGE_AMOUNT'];
        $BRAINTREE_CHARGE_AMOUNT_USER_ARR = getPriceUserCurrency($passengerID, "Passenger", $BRAINTREE_CHARGE_AMOUNT);
        $BRAINTREE_CHARGE_AMOUNT_USER = $BRAINTREE_CHARGE_AMOUNT_USER_ARR['fPricewithsymbol'];
        $msg = str_replace('####', $BRAINTREE_CHARGE_AMOUNT_USER, $LBL_BRAINTREE_CHARGE_MSG_TXT);
        $row[0]['BRAINTREE_CHARGE_MESSAGE'] = $msg;
        ## Display Braintree Charge Message ##
        ## Display Adyen Charge Message ##
        if (isset($langLabels['LBL_ADYEN_CHARGE_MSG_TXT']) && trim($langLabels['LBL_ADYEN_CHARGE_MSG_TXT']) != "") {
            $LBL_ADYEN_CHARGE_MSG_TXT = $langLabels['LBL_ADYEN_CHARGE_MSG_TXT'];
        } else {
            $db_label_adyen = $obj->MySQLSelect("SELECT * FROM `language_label` WHERE vLabel = 'LBL_ADYEN_CHARGE_MSG_TXT' AND vCode = '" . $vLanguage . "'");
            $LBL_ADYEN_CHARGE_MSG_TXT = $db_label_adyen[0]['vValue'];
        }
        $ADEYN_CHARGE_AMOUNT = $row[0]['ADYEN_CHARGE_AMOUNT'];
        $ADEYN_CHARGE_AMOUNT_USER_ARR = getPriceUserCurrency($passengerID, "Passenger", $ADEYN_CHARGE_AMOUNT);
        $ADEYN_CHARGE_AMOUNT_USER = $ADEYN_CHARGE_AMOUNT_USER_ARR['fPricewithsymbol'];
        $msg = str_replace('####', $ADEYN_CHARGE_AMOUNT_USER, $LBL_ADYEN_CHARGE_MSG_TXT);
        $row[0]['ADEYN_CHARGE_MESSAGE'] = $msg;*/
        ## Display Adyen Charge Message ##
        /* Added By PM On 09-12-2019 For Flutterwave Code Start */
        if (isset($langLabels['LBL_FLUTTERWAVE_CHARGE_MSG_TXT']) && trim($langLabels['LBL_FLUTTERWAVE_CHARGE_MSG_TXT']) != "") {
            $LBL_FLUTTERWAVE_CHARGE_MSG_TXT = $langLabels['LBL_FLUTTERWAVE_CHARGE_MSG_TXT'];
        } else {
            $db_label_flutter = $obj->MySQLSelect("SELECT * FROM `language_label` WHERE vLabel = 'LBL_FLUTTERWAVE_CHARGE_MSG_TXT' AND vCode = '" . $vLanguage . "'");
            $LBL_FLUTTERWAVE_CHARGE_MSG_TXT = $db_label_flutter[0]['vValue'];
        }
        $amountDataArr = getSupportedCurrencyAmt($row[0]['FLUTTERWAVE_CHARGE_AMOUNT'], $row[0]['vFlutterwaveCurrency']);
        $row[0]['vFlutterwaveCurrency'] = $amountDataArr['CURRENCY_CODE'];
        $FLUTTERWAVE_CHARGE_AMOUNT = $amountDataArr['AMOUNT'];
        $FLUTTERWAVE_CHARGE_AMOUNT_USER_ARR = $FLUTTERWAVE_CHARGE_AMOUNT;
        //$FLUTTERWAVE_CHARGE_AMOUNT_USER = $vFlutterwavevSymbol . $FLUTTERWAVE_CHARGE_AMOUNT;
        $FLUTTERWAVE_CHARGE_AMOUNT_USER = formateNumAsPerCurrency($FLUTTERWAVE_CHARGE_AMOUNT, $row[0]['vFlutterwaveCurrency']);
        $msg = str_replace('####', $FLUTTERWAVE_CHARGE_AMOUNT_USER, $LBL_FLUTTERWAVE_CHARGE_MSG_TXT);
        $row[0]['FLUTTERWAVE_CHARGE_AMOUNT'] = $FLUTTERWAVE_CHARGE_AMOUNT;
        $row[0]['FLUTTERWAVE_CHARGE_MESSAGE'] = $msg;
        /* Added By PM On 09-12-2019 For Flutterwave Code End */
        ## Check and update Device Session ID ##
        if ($row[0]['tDeviceSessionId'] == "") {
            $random = substr(md5(rand()), 0, 7);
            $Update_Device_Session['tDeviceSessionId'] = session_id() . time() . $random;
            $Update_Device_Session_id = $obj->MySQLQueryPerform($tblName, $Update_Device_Session, 'update', $where);
            $row[0]['tDeviceSessionId'] = $Update_Device_Session['tDeviceSessionId'];
        }
        ## Check and update Device Session ID ##
        ## Check and update Session ID ##
        if ($row[0]['tSessionId'] == "") {
            $Update_Session['tSessionId'] = session_id() . time();
            $Update_Session_id = $obj->MySQLQueryPerform($tblName, $Update_Session, 'update', $where);
            $row[0]['tSessionId'] = $Update_Session['tSessionId'];
        }
        ## Check and update Session ID ##
        if ($row[0]['vImgName'] != "" && $row[0]['vImgName'] != "NONE") {
            $row[0]['vImgName'] = "3_" . $row[0]['vImgName'];
        }
        $row[0]['Passenger_Password_decrypt'] = "";
        if ($row[0]['eStatus'] != "Active") {
            $returnArr['Action'] = "0";
            $returnArr['RESTRICT_APP'] = "Yes";
            $returnArr['message'] = "LBL_ACC_DELETE_TXT";
            $returnArr['message_title'] = "LBL_ACC_DELETE_TITLE";
            if ($row[0]['eStatus'] != "Deleted") {
                $returnArr['isAccountInactive'] = "Yes";
                $returnArr['message'] = "LBL_CONTACT_US_STATUS_NOTACTIVE_PASSENGER";
                if($SITE_TYPE == "Demo") {
                    $returnArr['message'] = "Your Account has been Inactivated. Please contact the Sales Team to re-activate it and to continue testing the System.";
                }
                $returnArr['message_title'] = "LBL_ACC_INACTIVE_TITLE";
            } else {
                $returnArr['isAccountDeleted'] = "Yes";
            }

            setDataResponse($returnArr);
        }
        $TripStatus = $row[0]['vTripStatus'];
        $TripID = $row[0]['iTripId'];
        $eType = "";
        if ($TripID != "" && $TripID != NULL && $TripID != 0) {
            //Added By HJ On 10-06-2020 For Optimization trips Table Query Start
            if (isset($tripDetailsArr["trips_" . $TripID]) && count($tripDetailsArr["trips_" . $TripID]) > 0) {
                $eType = $tripDetailsArr["trips_" . $TripID][0]['eType'];
            } else {
                $eType = get_value('trips', 'eType', 'iTripId', $TripID, '', 'true');
            }
            //Added By HJ On 10-06-2020 For Optimization trips Table Query End
            $row[0]['isVideoCall'] = "No";
            if ($MODULES_OBJ->isEnableVideoConsultingService()) {
                $row[0]['isVideoCall'] = get_value('trips', 'isVideoCall', 'iTripId', $TripID, '', 'true');
            }
        }
        if ($row[0]['APP_TYPE'] == "Ride-Delivery-UberX" || $row[0]['APP_TYPE'] == "Ride-Delivery") { // Changed By HJ On 02-04-2019 As Per Discuss With KS
            $ssql = " AND (eType = 'Ride' or eType = 'Deliver')";
        } else if ($row[0]['APP_TYPE'] == "Delivery") { // Added By HJ On 02-04-2019 As Per Discuss With KS
            $ssql = " AND eType = 'Deliver'";
        }
        $Tripcheckride = $obj->MySQLSelect("SELECT iTripId,eType,iActive FROM `trips` WHERE (iActive='Active' OR iActive='On Going Trip') $ssql and iUserId = '" . $passengerID . "' AND eSystem = 'General' ORDER BY iTripId DESC LIMIT 0,1");
        if (count($Tripcheckride) > 0) {
            $row[0]['vTripStatus'] = $Tripcheckride[0]['iActive'];
            $row[0]['iTripId'] = $Tripcheckride[0]['iTripId'];
            $row[0]['eType'] = $Tripcheckride[0]['eType'];
            $TripStatus = $row[0]['vTripStatus'];
            $TripID = $row[0]['iTripId'];
            $eType = $row[0]['eType'];
        }
        $check_trip = $obj->MySQLSelect("SELECT iTripId FROM `trips` WHERE (iActive='Active' OR iActive='On Going Trip') and iUserId = '" . $passengerID . "' AND eSystem = 'General'");
        $row[0]['Allow_Edit_Profile'] = "Yes";
        if (count($Tripcheckride) > 0) {
            $row[0]['Allow_Edit_Profile'] = "No";
        }
        if ($LiveTripId == "" && $eType == "Multi-Delivery") {
            $row[0]['vTripStatus'] = "NONE";
            $row[0]['iTripId'] = "0";
            $TripStatus = $row[0]['vTripStatus'];
            $TripID = $row[0]['iTripId'];
        }
        if ($TripStatus != "NONE") {
            $TripID = $row[0]['iTripId'];
            if ($LiveTripId != "") {
                $TripID = $LiveTripId;
            }
            //Added By HJ On 13-06-2020 For Optimization Start
            $row_result_ratings_trip = array();
            if ($TripID > 0) {
                if (isset($generalTripRatingDataArr['ratings_user_driver_' . $TripID])) {
                    $getTripRateData = $generalTripRatingDataArr['ratings_user_driver_' . $TripID];
                } else {
                    $generalTripRatingDataArr = array();
                    $getTripRateData = $obj->MySQLSelect("SELECT * FROM `ratings_user_driver` WHERE iTripId='" . $TripID . "' AND vRating1 != '' ");
                    $generalTripRatingDataArr['ratings_user_driver_' . $TripID] = $getTripRateData;
                }
                for ($r = 0; $r < count($getTripRateData); $r++) {
                    $rateUserType = $getTripRateData[$r]['eUserType'];
                    if (strtoupper($rateUserType) == "PASSENGER") {
                        $row_result_ratings_trip[] = $getTripRateData[$r];
                    }
                }
            }
            //Added By HJ On 13-06-2020 For Optimization End
            //Added By HJ On 29-08-2020 For Manual Toll and Extra Charge Related Changes Start
            $row_result_trips = FetchTripFareDetails($TripID, $passengerID, "Passenger");
            unset($row_result_trips['DriverDetails']['tLocationUpdateDate']);
            unset($row_result_trips['PassengerDetails']['tLocationUpdateDate']);
            $eFlyEnable = 1;
            if ($row_result_trips['iFromStationId'] > 0 && $row_result_trips['iToStationId'] > 0) {
                $eFlyEnable = 0;
            }
            if (strtoupper($row_result_trips['eApproveRequestSentByDriver']) == "YES" && strtoupper($row_result_trips['eApproveByUser']) != "YES" && strtoupper($row_result_trips['eType']) == "RIDE" && strtoupper($row_result_trips['ePoolRide']) == "NO" && $eFlyEnable > 0 && (strtoupper($ENABLE_MANUAL_TOLL_FEATURE) == 'YES' || strtoupper($ENABLE_OTHER_CHARGES_FEATURE) == 'YES')) {
                $row_result_trips['eVerifyTollCharges'] = "Yes";
            }
            if (strtoupper($row_result_trips['eApproved']) != "YES" && strtoupper($ENABLE_MANUAL_TOLL_FEATURE) != "YES" && strtoupper($ENABLE_OTHER_CHARGES_FEATURE) != "YES") {
                $row_result_trips['eVerifyTollCharges'] = "No";
            }
            if (trim($row_result_trips['vChargesDetailData']) == "" || $row_result_trips['vChargesDetailData'] == null) {
                $row_result_trips['vChargesDetailData'] = "{}";
            } else {
                $DataCharge = array();
                $vChargeData = json_decode($row_result_trips['vChargesDetailData'], true);
                if (strtoupper($ENABLE_MANUAL_TOLL_FEATURE) == 'YES') {
                    if (isset($vChargeData['fTollPrice']) && $vChargeData['fTollPrice'] != '' && $vChargeData['fTollPrice'] != 'nan') {
                        $DataCharge['fTollPrice'] = $userCurrencySymbol . ' ' . setTwoDecimalPoint($vChargeData['fTollPrice'] * $userCurrencyRatio);
                    } else {
                        $DataCharge['fTollPrice'] = $userCurrencySymbol . ' ' . setTwoDecimalPoint(0);
                    }
                }
                //$vChargeData['fOtherCharges'] > 0 put bcz after driver added charges, this other charge feature is off then at user side it will be shown..so not put condition of ENABLE_OTHER_CHARGES_FEATURE is off or not.issue#2021
                if (strtoupper($ENABLE_OTHER_CHARGES_FEATURE) == 'YES') {
                    if (isset($vChargeData['fOtherCharges']) && $vChargeData['fOtherCharges'] != '' && $vChargeData['fOtherCharges'] != 'nan' && $vChargeData['fOtherCharges'] > 0) {
                        $DataCharge['fOtherCharges'] = $userCurrencySymbol . ' ' . setTwoDecimalPoint($vChargeData['fOtherCharges'] * $userCurrencyRatio);
                    } else {
                        $DataCharge['fOtherCharges'] = $userCurrencySymbol . ' ' . setTwoDecimalPoint(0);
                    }
                }
                if (isset($vChargeData['totalAmount']) && $vChargeData['totalAmount'] != '' && $vChargeData['totalAmount'] != 'nan') {
                    $DataCharge['totalAmount'] = $userCurrencySymbol . ' ' . setTwoDecimalPoint($vChargeData['totalAmount'] * $userCurrencyRatio);
                }
                if (isset($vChargeData['serviceCost']) && $vChargeData['serviceCost'] != '' && $vChargeData['serviceCost'] != 'nan') {
                    $DataCharge['serviceCost'] = $userCurrencySymbol . ' ' . setTwoDecimalPoint($vChargeData['serviceCost'] * $userCurrencyRatio);
                }
                if (isset($vChargeData['fMaterialFee']) && $vChargeData['fMaterialFee'] != '' && $vChargeData['fMaterialFee'] != 'nan') {
                    $DataCharge['fMaterialFee'] = $userCurrencySymbol . ' ' . setTwoDecimalPoint($vChargeData['fMaterialFee'] * $userCurrencyRatio);
                }
                if (isset($vChargeData['fMiscFee']) && $vChargeData['fMiscFee'] != '' && $vChargeData['fMiscFee'] != 'nan') {
                    $DataCharge['fMiscFee'] = $userCurrencySymbol . ' ' . setTwoDecimalPoint($vChargeData['fMiscFee'] * $userCurrencyRatio);
                }
                if (isset($vChargeData['fDriverDiscount']) && $vChargeData['fDriverDiscount'] != '' && $vChargeData['fDriverDiscount'] != 'nan') {
                    $DataCharge['fDriverDiscount'] = $userCurrencySymbol . ' ' . setTwoDecimalPoint($vChargeData['fDriverDiscount'] * $userCurrencyRatio);
                }
                if (isset($vChargeData['vConfirmationCode']) && $vChargeData['vConfirmationCode'] != '' && $vChargeData['vConfirmationCode'] != 'nan') {
                    $DataCharge['vConfirmationCode'] = $vChargeData['vConfirmationCode'];
                }
                if (count($DataCharge) > 0) {
                    $row_result_trips['vChargesDetailData'] = json_encode($DataCharge);
                } else {
                    $row_result_trips['vChargesDetailData'] = "{}";
                }
            }
            //Added By HJ On 29-08-2020 For Manual Toll and Extra Charge Related Changes End
            //Added By HJ On 09-01-2020 For Get Driver Destination Mode Status Start
            $row[0]['DriverDetails'] = $row_result_trips['DriverDetails'];
            $row_result_trips['eDestinationMode'] = "No";
            if (isset($row_result_trips['DriverDetails']['eDestinationMode'])) {
                $row_result_trips['eDestinationMode'] = $row_result_trips['DriverDetails']['eDestinationMode'];
            }
            // Added by HV on 16-10-2020 for Restrict Passenger Limit
            $person_limit = get_value('vehicle_type', 'iPersonSize', 'iVehicleTypeId', $row_result_trips['iVehicleTypeId'], '', 'true');
            $db_label = $obj->MySQLSelect("SELECT vValue FROM `language_label` WHERE vLabel = 'LBL_CURRENT_PERSON_LIMIT' AND vCode = '" . $vLanguage . "'");
            $row_result_trips['RESTRICT_PASSENGER_LIMIT_NOTE'] = str_replace('#PERSON_LIMIT#', $person_limit, $db_label[0]['vValue']);
            //Added By HJ On 09-01-2020 For Get Dr    iver Destination Mode Status End
            $row[0]['TripDetails'] = $row_result_trips;
            $row_result_trips['DriverCarDetails']['make_title'] = $row_result_trips['DriverCarDetails']['model_title'] = "";
            if (isset($row_result_trips['DriverCarDetails']['vMake'])) {
                $row_result_trips['DriverCarDetails']['make_title'] = $row_result_trips['DriverCarDetails']['vMake'];
            }
            if (isset($row_result_trips['DriverCarDetails']['vTitle'])) {
                $row_result_trips['DriverCarDetails']['model_title'] = $row_result_trips['DriverCarDetails']['vTitle'];
            }
            $row[0]['DriverCarDetails'] = $row_result_trips['DriverCarDetails'];
            $row_result_payments = $obj->MySQLSelect("SELECT vPaymentUserStatus,isSkipRating FROM `payments` WHERE iTripId='$TripID'");
            $row[0]['PaymentStatus_From_Passenger'] = "No Entry";
            if (count($row_result_payments) > 0) {
                $row[0]['PaymentStatus_From_Passenger'] = "Approved";
                if ($row_result_payments[0]['vPaymentUserStatus'] != 'approved') {
                    $row[0]['PaymentStatus_From_Passenger'] = "Not Approved";
                }
            }
            $row[0]['Ratings_From_Passenger'] = "No Entry";
            if (count($row_result_ratings_trip) > 0) {
                $count_row_rating = 0;
                $ContentWritten = "false";
                while (count($row_result_ratings_trip) > $count_row_rating) {
                    $UserType = $row_result_ratings_trip[$count_row_rating]['eUserType'];
                    $row[0]['Ratings_From_Passenger'] = "Not Done";
                    if ($UserType == "Passenger") {
                        $ContentWritten = "true";
                        $row[0]['Ratings_From_Passenger'] = "Done";
                    }
                    $count_row_rating++;
                }
            }
            if (($db_currenttrip[0]['isSkipRating'] == 'Yes' && $MODULES_OBJ->isEnableSkipRatingRide()) || (isset($db_currenttrip['isVideoCall']) && $db_currenttrip['isVideoCall'] == "Yes" && $db_currenttrip['eCancelled'] == "Yes")) {
                $row[0]['Ratings_From_Passenger'] = "Done";
            }
        }
        $sql = "SELECT ord.iOrderId,ord.iCompanyId,ord.iDriverId,ord.vOrderNo,ord.eTakeaway,ord.iServiceId,ord.eBuyAnyService FROM `orders` as ord WHERE ord.iUserId='" . $passengerID . "' AND ord.iStatusCode = '6' AND (select count(iRatingId) from ratings_user_driver as rud where rud.iOrderId=ord.iOrderId AND rud.eFromUserType = 'Passenger' ) = 0 AND ord.eBuyAnyService = 'No' ORDER BY ord.iOrderId DESC LIMIT 0,1";
        $row_order = $obj->MySQLSelect($sql);
        $row[0]['Ratings_From_DeliverAll'] = "";
        if (count($row_order) > 0) {
            $LastOrderId = $row_order[0]['iOrderId'];
            $LastOrderNo = $row_order[0]['vOrderNo'];
            $LastOrderCompanyId = $row_order[0]['iCompanyId'];
            $LastOrderDriverId = $row_order[0]['iDriverId'];
            //Added By HJ On 04-07-2020 For Optimization register_driver Table Query Start
            if (isset($userDetailsArr["register_driver_" . $LastOrderDriverId])) {
                $result_driver = $userDetailsArr["register_driver_" . $LastOrderDriverId];
            } else {
                $result_driver = $obj->MySQLSelect("SELECT *,iDriverId as iMemberId FROM register_driver WHERE iDriverId='" . $LastOrderDriverId . "' ");
                $userDetailsArr["register_driver_" . $LastOrderDriverId] = $result_driver;
            }
            $result_driver[0]['driverName'] = $result_driver[0]['vName'] . " " . $result_driver[0]['vLastName'];
            //Added By HJ On 04-07-2020 For Optimization register_driver Table Query End
            //Added By HJ On 25-07-2020 For Optimization register_user Table Query Start
            $tblName = "company";
            if (isset($userDetailsArr[$tblName . "_" . $LastOrderCompanyId]) && count($userDetailsArr[$tblName . "_" . $LastOrderCompanyId]) > 0) {
                $result_company = $userDetailsArr[$tblName . "_" . $LastOrderCompanyId];
            } else {
                $result_company = $obj->MySQLSelect("SELECT *,iCompanyId as iMemberId FROM " . $tblName . " WHERE iCompanyId='" . $LastOrderCompanyId . "'");
                $userDetailsArr[$tblName . "_" . $LastOrderCompanyId] = $result_company;
            }
            //Added By HJ On 25-07-2020 For Optimization register_user Table Query End
            //$result_company = $obj->MySQLSelect("SELECT vCompany AS CompanyName FROM company WHERE iCompanyId = '" . $LastOrderCompanyId . "'");
            $row_result_ratings = $obj->MySQLSelect("SELECT count(iRatingId) as TotalRating FROM `ratings_user_driver` WHERE iOrderId='" . $LastOrderId . "' AND eFromUserType = 'Passenger'");
            $TotalRating = $row_result_ratings[0]['TotalRating'];
            $row[0]['Ratings_From_DeliverAll'] = "Not Done";
            if ($TotalRating > 0) {
                $row[0]['Ratings_From_DeliverAll'] = "Done";
            }
            $row[0]['LastOrderId'] = $LastOrderId;
            $row[0]['LastOrderNo'] = $LastOrderNo;
            $row[0]['LastOrderCompanyId'] = $LastOrderCompanyId;
            $row[0]['LastOrderCompanyName'] = $result_company[0]['vCompany'];
            $row[0]['LastOrderTakeaway'] = $row_order[0]['eTakeaway'];
            $row[0]['LastOrderDriverId'] = $LastOrderDriverId;
            $row[0]['LastOrderDriverName'] = "";
            if (isset($result_driver[0]['driverName']) && $result_driver[0]['driverName'] != "") {
                $row[0]['LastOrderDriverName'] = $result_driver[0]['driverName'];
            }
            $row[0]['LastOrderFoodDetailRatingShow'] = "No";
            if ($MODULES_OBJ->isEnableMultiOptionsToppings() && $row_order[0]['iServiceId'] == "1" && $row_order[0]['eBuyAnyService'] == "No" && $row_order[0]['eTakeaway'] == "No") {
                $check_rating_company = $obj->MySQLSelect("SELECT * FROM `ratings_user_driver` WHERE iOrderId = '" . $LastOrderId . "' and eFromUserType = 'Passenger' AND eToUserType = 'Company'");
                $check_rating_driver = $obj->MySQLSelect("SELECT * FROM `ratings_user_driver` WHERE iOrderId = '" . $LastOrderId . "' and eFromUserType = 'Passenger' AND eToUserType = 'Driver'");
                if (empty($check_rating_company) || empty($check_rating_driver)) {
                    $row[0]['LastOrderFoodDetailRatingShow'] = "Yes";
                } else {
                    $row[0]['LastOrderFoodDetailRatingShow'] = "No";
                }
                $DRIVER_FEEDBACK_QUESTIONS = getFoodRatingDetailFeedbackQuestions($vLanguage);
                $row[0]['DRIVER_FEEDBACK_QUESTIONS'] = !empty($DRIVER_FEEDBACK_QUESTIONS) ? $DRIVER_FEEDBACK_QUESTIONS : "";
            }
        }
        //Added By HJ On 13-06-2020 For Optimization user_address Table Query Start
        if (isset($userAddressDataArr['user_address_' . $passengerID])) {
            $result_Address = $userAddressDataArr['user_address_' . $passengerID];
        } else {
            $userAddressDataArr = array();
            $result_Address = $obj->MySQLSelect("SELECT * from user_address WHERE iUserId = '" . $passengerID . "' AND eStatus = 'Active'");
            $userAddressDataArr['user_address_' . $passengerID] = $result_Address;
        }
        $totalAddressCount = 0;
        for ($a = 0; $a < count($result_Address); $a++) {
            $addresUser = $result_Address[$a]['eUserType'];
            if (strtoupper($addresUser) == "RIDER") {
                $totalAddressCount += 1;
            }
        }
        //Added By HJ On 13-06-2020 For Optimization user_address Table Query End
        $row[0]['ToTalAddress'] = $totalAddressCount;
        $row[0]['DefaultCurrencySign'] = $row[0]["DEFAULT_CURRENCY_SIGN"];
        $row[0]['DefaultCurrencyCode'] = $row[0]["DEFAULT_CURRENCY_CODE"];
        $row[0]['FETCH_TRIP_STATUS_TIME_INTERVAL'] = fetchtripstatustimeinterval();
        $row[0]['ENABLE_TOLL_COST'] = $row[0]['APP_TYPE'] != "UberX" ? $row[0]['ENABLE_TOLL_COST'] : "No";
        /* Check Toll Enable For Passenger's Country */
        if ($usercountrycode != "") {
            //Added By HJ On 09-06-2020 For Optimization country Table Query Start
            if (isset($country_data_arr[$usercountrycode]['eEnableToll']) && trim($country_data_arr[$usercountrycode]['eEnableToll']) != "") {
                $eEnableToll = $country_data_arr[$usercountrycode]['eEnableToll'];
            } else {
                $user_country_toll = $obj->MySQLSelect("SELECT eEnableToll from country WHERE vCountryCode = '" . $usercountrycode . "'");
                $eEnableToll = $user_country_toll[0]['eEnableToll'];
            }
            //Added By HJ On 09-06-2020 For Optimization country Table Query End
            if ($eEnableToll != "") {
                $row[0]['ENABLE_TOLL_COST'] = ($eEnableToll == "Yes" && $row[0]['ENABLE_TOLL_COST'] == "Yes") ? "Yes" : "No";
            }
        }
        /* Check Toll Enable For Passenger's Country */
        if ($row[0]['APP_TYPE'] == "Ride" || $row[0]['APP_TYPE'] == "Ride-Delivery" || $row[0]['APP_TYPE'] == "Ride-Delivery-UberX") {
            $row[0]['FEMALE_RIDE_REQ_ENABLE'] = $row[0]['FEMALE_RIDE_REQ_ENABLE'];
            $row[0]['HANDICAP_ACCESSIBILITY_OPTION'] = $row[0]['HANDICAP_ACCESSIBILITY_OPTION'];
        } else {
            $row[0]['FEMALE_RIDE_REQ_ENABLE'] = "No";
            $row[0]['HANDICAP_ACCESSIBILITY_OPTION'] = "No";
            // $row[0]['ENABLE_TOLL_COST'] = "No";
        }
        if ($row[0]['APP_TYPE'] == "Ride" || $row[0]['APP_TYPE'] == "Ride-Delivery" || $row[0]['APP_TYPE'] == "Ride-Delivery-UberX") {
            $row[0]['ENABLE_HAIL_RIDES'] = $row[0]['ENABLE_HAIL_RIDES'];
        } else {
            $row[0]['ENABLE_HAIL_RIDES'] = "No";
        }
        // if ($row[0]['APP_PAYMENT_MODE'] == "Card" || ONLYDELIVERALL == "Yes") {
        if (strtoupper($row[0]['CASH_AVAILABLE']) == "NO" || ONLYDELIVERALL == "Yes") {
            $row[0]['ENABLE_HAIL_RIDES'] = "No";
        }
        $user_available_balance = $WALLET_OBJ->FetchMemberWalletBalanceApp($passengerID, "Rider");
        $row[0]['user_available_balance'] = strval($user_available_balance);
        $user_available_balance_arr = explode(" ", $user_available_balance);
        $row[0]['user_available_balance_amount'] = strval($user_available_balance_arr[1]);
        $user_available_balance_value = $WALLET_OBJ->FetchMemberWalletBalanceApp($passengerID, "Rider", 'Yes');
        $row[0]['user_available_balance_value'] = strval($user_available_balance_value);
        $row[0]['eWalletBalanceAvailable'] = 'Yes';
        if ($user_available_balance_value <= 0 || $user_available_balance_value <= 0.00) {
            $row[0]['eWalletBalanceAvailable'] = 'No';
        }
        if (!empty($_REQUEST['eSignUpType']) && $_REQUEST['eSignUpType'] == "kiosk") {
            $row[0]['eWalletBalanceAvailable'] = 'No';
        }
        $row[0]['PHOTO_UPLOAD_SERVICE_ENABLE'] = $row[0]['APP_TYPE'] == "UberX" ? $PHOTO_UPLOAD_SERVICE_ENABLE : "No";
        $row[0]['ENABLE_TIP_MODULE'] = $row[0]['ENABLE_TIP_MODULE'];
        $sql_vehicle_category_table_name = getVehicleCategoryTblName();
        //Added By Hasmukh On 21-12-2018 For Get Common Delivery Vehicle Category Data Start - Delivery Apps To Be Multi Delivery
        //Added By HJ On 13-06-2020 For Optimization Vehicle Category Table Query Start
        if (isset($vehicleCategoryDataArr[$sql_vehicle_category_table_name])) {
            $getVehicleCatData = $vehicleCategoryDataArr[$sql_vehicle_category_table_name];
        } else {
            $getVehicleCatData = $obj->MySQLSelect("SELECT iServiceId,eDeliveryType,eSubCatType,eStatus,iParentId,eCatType,eFor,iVehicleCategoryId,vCategory_" . $vLanguage . " FROM " . $sql_vehicle_category_table_name);
            $vehicleCategoryDataArr[$sql_vehicle_category_table_name] = $getVehicleCatData;
        }
        $getDataCategory = $vehicleCatNameArr = array();
        for ($v = 0; $v < count($getVehicleCatData); $v++) {
            $vehicleeCatType = $getVehicleCatData[$v]['eCatType'];
            $iVehicleCategoryId = $getVehicleCatData[$v]['iVehicleCategoryId'];
            $iVehicleCategoryName = $getVehicleCatData[$v]['vCategory_' . $vLanguage];
            $vehicleeFor = $getVehicleCatData[$v]['eFor'];
            $vehicleCatNameArr[$iVehicleCategoryId] = $iVehicleCategoryName;
            if (strtoupper($vehicleeCatType) == "MOREDELIVERY" && strtoupper($vehicleeFor) == "DELIVERYCATEGORY") {
                $getDataCategory[] = $getVehicleCatData[$v];
            }
        }
        $vehicleCatName = "";
        if (isset($vehicleCatNameArr[$getDataCategory[0]['iVehicleCategoryId']])) {
            $vehicleCatName = $vehicleCatNameArr[$getDataCategory[0]['iVehicleCategoryId']];
        }
        //Added By HJ On 13-06-2020 For Optimization Vehicle Category Table Query End
        $row[0]['DELIVERY_CATEGORY_ID'] = !empty($getDataCategory[0]['iVehicleCategoryId']) ? $getDataCategory[0]['iVehicleCategoryId'] : "";
        $row[0]['DELIVERY_CATEGORY_NAME'] = $vehicleCatName;
        $host_arr = array();
        $host_arr = explode(".", $_SERVER["HTTP_HOST"]);
        $host_system = $host_arr[0];
        $row[0]['UBERX_PARENT_CAT_ID'] = $parent_ufx_catid;
        if (isset($row[0]['APP_TYPE']) && $row[0]['APP_TYPE'] == "UberX") {
            $row[0]['APP_DESTINATION_MODE'] = "None";
            $row[0]['ENABLE_TOLL_COST'] = $row[0]['HANDICAP_ACCESSIBILITY_OPTION'] = $row[0]['FEMALE_RIDE_REQ_ENABLE'] = $row[0]['ENABLE_HAIL_RIDES'] = $row[0]['ENABLE_CORPORATE_PROFILE'] = "No";
            $row[0]['ONLINE_DRIVER_LIST_UPDATE_TIME_INTERVAL'] = "5";
            if ($MODULES_OBJ->isEnableAppHomeScreenLayoutV2()) {
                $row[0]['UBERX_PARENT_CAT_ID'] = 0;
                if ($parent_ufx_catid > 0) {
                    $row[0]['UBERX_PARENT_CAT_ID'] = $parent_ufx_catid;
                }
            }
        }
        $row[0]['ENABLE_DELIVERY_MODULE'] = SITE_TYPE == "Demo" ? $row[0]['eDeliverModule'] : $row[0]['ENABLE_DELIVERY_MODULE'];
        $row[0]['PayPalConfiguration'] = $row[0]['ENABLE_DELIVERY_MODULE'] == "Yes" ? "Yes" : $row[0]['PAYMENT_ENABLED'];
        if (isset($_REQUEST['type']) && $_REQUEST['type'] != "getDetail") {
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
            $row[0]['LIST_CURRENCY'] = $defCurrencyValues;
            for ($i = 0; $i < count($defCurrencyValues); $i++) {
                $row[0]['LIST_CURRENCY'][$i]['vService_BG_color'] = RANDOM_COLORS_ARR[$i];
                $row[0]['LIST_CURRENCY'][$i]['vService_TEXT_color'] = "#FFFFFF";
            }
        }
        $row[0]['SITE_TYPE'] = SITE_TYPE;
        $row[0]['RIIDE_LATER'] = RIIDE_LATER;
        $row[0]['PROMO_CODE'] = PROMO_CODE;
        $row[0]['DELIVERALL'] = $MODULES_OBJ->isDeliverAllFeatureAvailable() ? DELIVERALL : "No";
        $row[0]['ONLYDELIVERALL'] = ONLYDELIVERALL;
        $row[0]['SITE_TYPE_DEMO_MSG'] = $demo_site_msg;
        //Added By HJ On 08-06-2020 For Optimization Start
        if (isset($currencyNameArr[$row[0]['vCurrencyPassenger']]['vSymbol']) && trim($currencyNameArr[$row[0]['vCurrencyPassenger']]['vSymbol']) != "") {
            $row[0]['CurrencySymbol'] = $currencyNameArr[$row[0]['vCurrencyPassenger']]['vSymbol'];
        } else {
            $row[0]['CurrencySymbol'] = get_value('currency', 'vSymbol', 'vName', $row[0]['vCurrencyPassenger'], '', 'true');
        }
        //Added By HJ On 08-06-2020 For Optimization End
        $eUnit = getMemberCountryUnit($passengerID, "Passenger");
        $row[0]['eUnit'] = $eUnit;
        $row[0]['SourceLocations'] = getusertripsourcelocations($passengerID, "SourceLocation");
        $row[0]['DestinationLocations'] = getusertripsourcelocations($passengerID, "DestinationLocation");
        if ($MODULES_OBJ->isEnableAnywhereDeliveryFeature()) {
            $row[0]['GenieLocations'] = getgenieorderlocations($passengerID);
        }
        $sql = "SELECT * FROM user_fave_address where iUserId = '" . $passengerID . "' AND eUserType = 'Passenger' AND eStatus = 'Active' ORDER BY iUserFavAddressId ASC";
        $db_passenger_fav_address = $obj->MySQLSelect($sql);
        $row[0]['UserFavouriteAddress'] = $db_passenger_fav_address;
        $usercountrydetailbytimezone = FetchMemberCountryData($passengerID, "Passenger", $vTimeZone, $vUserDeviceCountry);
        $row[0]['vDefaultCountry'] = $usercountrydetailbytimezone['vDefaultCountry'];
        $row[0]['vDefaultCountryCode'] = $usercountrydetailbytimezone['vDefaultCountryCode'];
        $row[0]['vDefaultPhoneCode'] = $usercountrydetailbytimezone['vDefaultPhoneCode'];
        $row[0]['vRCountryImage'] = $usercountrydetailbytimezone['vRImageMember']; //added by SP for country image related changes on 06-09-2019
        $row[0]['vSCountryImage'] = $usercountrydetailbytimezone['vSImageMember']; //added by SP for country image related changes on 06-09-2019
        $row[0]['vDefaultCountryImage'] = empty($row[0]['vSCountryImage']) ? $usercountrydetailbytimezone['vDefaultCountryImage'] : $row[0]['vSCountryImage']; //added by SP for country image related changes on 06-08-2019
        $row[0]['vPhoneCode'] = empty($row[0]['vPhoneCode']) ? $row[0]['vDefaultPhoneCode'] : $row[0]['vPhoneCode'];
        $row[0]['vCountry'] = empty($row[0]['vCountry']) ? $row[0]['vDefaultCountryCode'] : $row[0]['vCountry'];
        $SITE_POLICE_CONTROL_NUMBER = getMemberCountryPoliceNumber($passengerID, "Passenger", $row[0]['vCountry']);
        $row[0]['SITE_POLICE_CONTROL_NUMBER'] = $SITE_POLICE_CONTROL_NUMBER;
        $UserSelectedAddressArr = FetchMemberSelectedAddress($passengerID, "Passenger");
        $row[0]['UserSelectedAddress'] = $UserSelectedAddressArr['UserSelectedAddress'];
        $row[0]['UserSelectedLatitude'] = $UserSelectedAddressArr['UserSelectedLatitude'];
        $row[0]['UserSelectedLongitude'] = $UserSelectedAddressArr['UserSelectedLongitude'];
        $row[0]['UserSelectedAddressId'] = $UserSelectedAddressArr['UserSelectedAddressId'];
        $rowtRegistrationDate = strtotime($usercountrydetailbytimezone['tRegistrationDate']);
        // $row[0]['RegistrationDate'] = date("Y-m-d", strtotime($usercountrydetailbytimezone['tRegistrationDate'] . ' -1 day '));
        $row[0]['RegistrationDate'] = date("Y-m-d", strtotime($usercountrydetailbytimezone['tRegistrationDate']));
        $row[0]['tMemberSince'] = $langLabels['LBL_MEMBER_SINCE_TXT'] . " " . humanReadableTiming($rowtRegistrationDate);
        $fOutStandingAmount = $fTripsOutStandingAmount = GetPassengerOutstandingAmount($passengerID);
        $row[0]['DISABLE_CASH_PAYMENT_OPTION'] = "No";
        $row[0]['fOutStandingAmount'] = 0;
        $row[0]['ServiceCategories'] = json_decode(serviceCategories, true);
        for ($i = 0; $i < count($row[0]['ServiceCategories']); $i++) {
            $item_tmp = $row[0]['ServiceCategories'][$i];
            if (is_null($item_tmp['tDescription']) || empty($item_tmp['tDescription'])) {
                $row[0]['ServiceCategories'][$i]['tDescription'] = "";
            }
        }
        if ($fOutStandingAmount > 0) {
            $row[0]['DISABLE_CASH_PAYMENT_OPTION'] = "Yes";
            $getPriceUserCurrencyArr = getPriceUserCurrency($passengerID, "Passenger", $fOutStandingAmount);
            $row[0]['fOutStandingAmount'] = $getPriceUserCurrencyArr['fPricewithsymbol'];
        }
        /* As a part of Socket Cluster */
        $row[0]['MAX_ALLOW_TIME_INTERVAL_MILLI'] = (fetchtripstatustimeMAXinterval() + $intervalmins) * 1000;
        /* As a part of Socket Cluster */
        $vCurrencyPassenger = $row[0]['vCurrencyPassenger'];
        if ($vCurrencyPassenger == "" || $vCurrencyPassenger == NULL) {
            //Added By HJ On 08-06-2020 For Optimization currency Table Query Start
            if (!empty($vSystemDefaultCurrencyName)) {
                $vCurrencyPassenger = $vSystemDefaultCurrencyName;
            } else {
                $vCurrencyPassenger = get_value('currency', 'vName', 'eDefault', 'Yes', '', 'true');
            }
            //Added By HJ On 08-06-2020 For Optimization currency Table Query End
        }
        if (isset($currencyNameArr[$vCurrencyPassenger]['vSymbol']) && trim($currencyNameArr[$vCurrencyPassenger]['vSymbol']) != "") {
            $CurrencySymbol = $currencyNameArr[$vCurrencyPassenger]['vSymbol'];
        } else {
            $CurrencySymbol = get_value('currency', 'vSymbol', 'vName', $vCurrencyPassenger, '', 'true');
        }
        if (isset($currencyNameArr[$vCurrencyPassenger]['Ratio']) && trim($currencyNameArr[$vCurrencyPassenger]['Ratio']) != "") {
            $Ratio = $currencyNameArr[$vCurrencyPassenger]['Ratio'];
        } else {
            $Ratio = get_value('currency', 'Ratio', 'vName', $vCurrencyPassenger, '', 'true');
        }
        $currencydata = get_value('currency', 'eReverseformattingEnable,eReverseSymbolEnable', 'vName', $vCurrencyPassenger, '', '');
        $row[0]['eReverseformattingEnable'] = $currencydata[0]['eReverseformattingEnable'];
        $row[0]['eReverseSymbolEnable'] = $currencydata[0]['eReverseSymbolEnable'];
        //$fTripsOutStandingAmount = GetPassengerOutstandingAmount($passengerID); // Commented By HJ On 09-06-2020 Because Already Above Got
        $fTripsOutStandingAmount = round(($fTripsOutStandingAmount * $Ratio), 2);
        $row[0]['fOutStandingAmount'] = $fTripsOutStandingAmount;
        $row[0]['fOutStandingAmountWithSymbol'] = formateNumAsPerCurrency($fTripsOutStandingAmount, $vCurrencyPassenger);
        if ($fTripsOutStandingAmount > 0) {
            $userOutStandingData = $obj->MySQLSelect("SELECT fPendingAmount, iTripId, iOrderId FROM trip_outstanding_amount WHERE iUserId = '$passengerID' AND ePaidByPassenger = 'No' AND ePaymentBy = 'Passenger' ORDER BY iTripId DESC LIMIT 1");
            if (!empty($userOutStandingData) && count($userOutStandingData) > 0) {
                $outstandingTripData = $obj->MySQLSelect("SELECT vRideNo, eType FROM trips WHERE iTripId = '" . $userOutStandingData[0]['iTripId'] . "'");
                if (empty($outstandingTripData)) {
                    $outstandingTripData = $obj->MySQLSelect("SELECT vOrderNo as vRideNo FROM orders WHERE iOrderId = '" . $userOutStandingData[0]['iOrderId'] . "'");
                    $outstandingTripData[0]['eType'] = "DeliverAll";
                }
                $row[0]['PaymentPendingMsg'] = str_replace("#RIDE_NO#", $outstandingTripData[0]['vRideNo'], $langLabels['LBL_OUTSTANDING_PAYMENT_PENDING_MSG']);
                $row[0]['ShowContactUsBtn'] = "Yes";
                $tDescription = "Amount charge for trip oustanding balance";
                $extraParams = "eType=" . $outstandingTripData[0]['eType'] . "&ePaymentType=ChargeOutstandingAmount&tSessionId=" . $row[0]['tSessionId'] . "&GeneralMemberId=" . $passengerID . "&GeneralUserType=Passenger&iServiceId=&AMOUNT=" . $fTripsOutStandingAmount . "&PAGE_TYPE=CHARGE_OUTSTANDING_AMT&SYSTEM_TYPE=APP&IS_RETURN_RESULT=Yes&description=" . urlencode($tDescription);
                $row[0]['OUTSTANDING_PAYMENT_URL'] = $tconfig['tsite_url'] . 'assets/libraries/webview/payment_mode_select.php?' . $extraParams;
            }
        }
        $row[0]['MONGO_DB'] = $tconfig['tmongodb_databse'];
        $row[0]['MONGO_DB_CONNECTION_PORT'] = $tconfig['tmongodb_port'];
        $row[0]['SERVER_DEFAULT_TIMEZONE'] = date_default_timezone_get();
        $row[0]['ENABLE_MULTI_DELIVERY'] = ENABLE_MULTI_DELIVERY;
        $row[0]['tsite_upload_docs_file_extensions'] = $tconfig['tsite_upload_docs_file_extensions'];
        $row[0]['tsite_upload_image_file_extensions'] = $tconfig['tsite_upload_image_file_extensions'];
        $row[0]['tsite_upload_video_file_extensions'] = $tconfig['tsite_upload_video_file_extensions'];
        $row[0]['tsite_upload_audio_file_extensions'] = $tconfig['tsite_upload_audio_file_extensions'];
        $row[0]['SC_CONNECT_URL'] = getSocketURL();
        $row[0]['APP_SERVICE_URL'] = APP_SERVICE_URL;
        $row[0]['GOOGLE_API_REPLACEMENT_URL'] = GOOGLE_API_REPLACEMENT_URL;
        $row[0]['TSITE_DB'] = TSITE_DB;
        $storeCatArr = json_decode(serviceCategories, true);
        $systemStoreEnable = $MODULES_OBJ->isSingleStoreSelection();
        $iserviceidstore = 0;
        if (count($storeCatArr) == 1) $iserviceidstore = $storeCatArr[0]['iServiceId'];
        if ($systemStoreEnable > 0) {
            for ($g = 0; $g < count($storeCatArr); $g++) {
                $storeData = getStoreDataForSystemStoreSelection($storeCatArr[$g]['iServiceId']);
                $iCompanyId = $storeData['iCompanyId'];
                $storeData['ispriceshow'] = $storeCatArr[$g]['iServiceId'];
                $storeCatArr[$g]['iCompanyId'] = $iCompanyId;
                $storeCatArr[$g]['STORE_DATA'] = $storeData;
                $storeCatArr[$g]['STORE_ID'] = $iCompanyId;
            }
            $companyData = getStoreDataForSystemStoreSelection($iserviceidstore);
            if (!empty($companyData[0]['iCompanyId'])) $row[0]['STORE_ID'] = $companyData[0]['iCompanyId'];
            else $row[0]['STORE_ID'] = $companyData['iCompanyId'];
        }
        $row[0]['ServiceCategories'] = $storeCatArr;
        $deliverAll_serviceArr = array();
        if (count($row[0]['ServiceCategories']) > 0) {
            $scsql = "select *,JSON_UNQUOTE(JSON_VALUE(tProofNote, '$.tProofNote_" . $vLanguage . "')) as tProofNote from service_categories";
            $scsqlData = $obj->MySQLSelect($scsql);
            foreach ($scsqlData as $scValue) {
                if (is_null($scValue['tProofNote']) || $scValue['tProofNote'] == "null") {
                    $scValue['tProofNote'] = "";
                }
                $deliverAll_serviceArr[$scValue['iServiceId']] = $scValue;
            }
        }
        for ($i = 0; $i < count($row[0]['ServiceCategories']); $i++) {
            $item_tmp = $row[0]['ServiceCategories'][$i];
            if (is_null($item_tmp['tDescription']) || empty($item_tmp['tDescription'])) {
                $row[0]['ServiceCategories'][$i]['tDescription'] = "";
            }
            if ($row[0]['ServiceCategories'][$i]['iServiceId'] > 0) {
                $sc_eShowTerms = "No";
                $sc_eProofUpload = "No";
                $sc_tProofNote = "";
                if (isset($deliverAll_serviceArr[$row[0]['ServiceCategories'][$i]['iServiceId']])) {
                    if ($MODULES_OBJ->isEnableTermsServiceCategories()) {
                        $sc_eShowTerms = $deliverAll_serviceArr[$row[0]['ServiceCategories'][$i]['iServiceId']]['eShowTerms'];
                    }
                    if ($MODULES_OBJ->isEnableProofUploadServiceCategories()) {
                        $sc_eProofUpload = $deliverAll_serviceArr[$row[0]['ServiceCategories'][$i]['iServiceId']]['eProofUpload'];
                        $sc_tProofNote = $deliverAll_serviceArr[$row[0]['ServiceCategories'][$i]['iServiceId']]['tProofNote'];
                    }
                } else {
                    $sc_data = $obj->MySQLSelect("SELECT eShowTerms,eProofUpload,JSON_UNQUOTE(JSON_VALUE(tProofNote, '$.tProofNote_" . $vLanguage . "')) as tProofNote FROM service_categories WHERE iServiceId = " . $row[0]['ServiceCategories'][$i]['iServiceId']);
                    if ($MODULES_OBJ->isEnableTermsServiceCategories()) {
                        $sc_eShowTerms = $sc_data[0]['eShowTerms'];
                    }
                    if ($MODULES_OBJ->isEnableProofUploadServiceCategories()) {
                        $sc_eProofUpload = $sc_data[0]['eProofUpload'];
                        if (is_null($sc_data[0]['tProofNote']) || $sc_data[0]['tProofNote'] == "null") {
                            $sc_data[0]['tProofNote'] = "";
                        }
                        $sc_tProofNote = $sc_data[0]['tProofNote'];
                    }
                }
                $row[0]['ServiceCategories'][$i]['eShowTerms'] = ($sc_eShowTerms != "") ? $sc_eShowTerms : "";
                $row[0]['ServiceCategories'][$i]['eProofUpload'] = ($sc_eProofUpload != "") ? $sc_eProofUpload : "";
                $row[0]['ServiceCategories'][$i]['tProofNote'] = (!empty($sc_tProofNote) && $sc_tProofNote != NULL) ? $sc_tProofNote : "";
            }
        }
        if ((defined('FOOD_ONLY') && strtoupper(FOOD_ONLY) == 'YES') || (count($storeCatArr) == 1 && $storeCatArr[0]['iServiceId'] == 1)) {
            $sc_eShowTerms = "No";
            $sc_eProofUpload = "No";
            $sc_tProofNote = "";
            $sc_data = $obj->MySQLSelect("SELECT eShowTerms,eProofUpload,JSON_UNQUOTE(JSON_VALUE(tProofNote, '$.tProofNote_" . $vLanguage . "')) as tProofNote FROM service_categories WHERE iServiceId = " . $storeCatArr[0]['iServiceId']);
            if (isset($deliverAll_serviceArr[1])) {
                if ($MODULES_OBJ->isEnableTermsServiceCategories()) {
                    $sc_eShowTerms = $deliverAll_serviceArr[1]['eShowTerms'];
                }
                if ($MODULES_OBJ->isEnableProofUploadServiceCategories()) {
                    $sc_eProofUpload = $deliverAll_serviceArr[1]['eProofUpload'];
                    $sc_tProofNote = $deliverAll_serviceArr[1]['tProofNote'];
                }
            } else {
                if ($MODULES_OBJ->isEnableTermsServiceCategories()) {
                    $sc_eShowTerms = $sc_data[0]['eShowTerms'];
                }
                if ($MODULES_OBJ->isEnableProofUploadServiceCategories()) {
                    $sc_eProofUpload = $sc_data[0]['eProofUpload'];
                    if (is_null($sc_data[0]['tProofNote']) || $sc_data[0]['tProofNote'] == "null") {
                        $sc_data[0]['tProofNote'] = "";
                    }
                    $sc_tProofNote = $sc_data[0]['tProofNote'];
                }
            }
            if (isset($deliverAll_serviceArr[1])) {
                $sc_vServiceName = $deliverAll_serviceArr[1]['vServiceName_' . $vLanguage];
            } else {
                $sc_vServiceName = get_value('service_categories', 'vServiceName_' . $vLanguage, 'iServiceId', '1', '', 'true');
            }
            $row[0]['vCategory'] = $sc_vServiceName;
            $row[0]['eShowTerms'] = ($sc_eShowTerms != "") ? $sc_eShowTerms : "";
            $row[0]['eProofUpload'] = ($sc_eProofUpload != "") ? $sc_eProofUpload : "";
            $row[0]['tProofNote'] = (!empty($sc_tProofNote) && $sc_tProofNote != NULL) ? $sc_tProofNote : "";
        }
        //Added By HJ On 16-07-2019 For Check Multiple Country Exists Or Not Start
        // Added By HJ On 04-06-2020 For Optimized country Table Query Start
        if (count($country_data_retrieve) > 0) {
            $getCountryData = array();
            for ($h = 0; $h < count($country_data_retrieve); $h++) {
                if (strtoupper($country_data_retrieve[$h]['eStatus']) == "ACTIVE") {
                    $getCountryData[] = $country_data_retrieve[$h]['iCountryId'];
                }
            }
        } else {
            $getCountryData = $obj->MySQLSelect("SELECT iCountryId FROM country WHERE eStatus='Active'");
        }
        // Added By HJ On 04-06-2020 For Optimized country Table Query End
        $multiCountry = "No";
        if (count($getCountryData) > 1) {
            $multiCountry = "Yes";
        }
        $row[0]['showCountryList'] = $multiCountry;
        //Added By HJ On 16-07-2019 For Check Multiple Country Exists Or Not End
        //Added By Hasmukh On 25-12-2018 For Get Advertise Banner Data Start
        if ($MODULES_OBJ->checkSharkPackage() && $row[0]['eStatus'] == "Active" && ($row[0]['APP_TYPE'] != "Ride" || ($row[0]['APP_TYPE'] == "Ride" && $row[0]['vTripStatus'] != "On Going Trip" && $row[0]['vTripStatus'] != "Active"))) {
            $row[0]['advertise_banner_data'] = getAdvertisementBannersAsPerDevice($passengerID, "Passenger");
        }
        //Added By Hasmukh On 25-12-2018 For Get Advertise Banner Data End
        //Added By HJ On 08-06-2020 For Optimization configurations_payment Table Query Start
        if (isset($generalConfigPaymentArr['ENABLE_GOPAY']) && trim($generalConfigPaymentArr['ENABLE_GOPAY']) != "") {
            $EnableGopay = trim($generalConfigPaymentArr['ENABLE_GOPAY']);
        } else {
            $EnableGopay = get_value('configurations_payment', 'vValue', 'vName', 'ENABLE_GOPAY'); //added by SP for Gojek-gopay
        }
        //Added By HJ On 08-06-2020 For Optimization configurations_payment Table Query End
        if (!empty($EnableGopay[0]['vValue'])) {
            $row[0]['ENABLE_GOPAY'] = $EnableGopay[0]['vValue'];
        } else if (!empty($EnableGopay)) {
            $row[0]['ENABLE_GOPAY'] = $EnableGopay;
        } else {
            $row[0]['ENABLE_GOPAY'] = '';
        }
        if ($MODULES_OBJ->isDriverSubscriptionModuleAvailable()) {
            $row[0]['DRIVER_SUBSCRIPTION_ENABLE'] = 'Yes';
        } else {
            $row[0]['DRIVER_SUBSCRIPTION_ENABLE'] = 'No';
        }
        $row[0]['PAYMENT_BASE_URL'] = $tconfig['tsite_url'] . 'assets/libraries/webview/system_payment.php?';
        $row[0]['PAYMENT_MODE_URL'] = $tconfig['tsite_url'] . 'assets/libraries/webview/payment_mode_select.php?';
        $row[0]['RANDOM_COLORS_KEY_VAL_ARR'] = RANDOM_COLORS_KEY_VAL_ARR;
        //$row[0]['UFX_SERVICE_AVAILABLE'] = $MODULES_OBJ->isUfxFeatureAvailable(); // Commented By HJ On 04-06-2020 For Optimized Query Below Line
        $row[0]['UFX_SERVICE_AVAILABLE'] = $isUfxAvailable; // Added By HJ On 04-06-2020 For Optimized Query
        $row[0]['AUTH_EMAIL_SYSTEM'] = AUTH_EMAIL_SYSTEM;
        $row[0]['ENABLE_CATEGORY_WISE_STORES'] = ($MODULES_OBJ->isStoreClassificationEnable() == 1) ? "Yes" : "No";
        $row[0]['ENABLE_TAKE_AWAY'] = ($MODULES_OBJ->isTakeAwayEnable()) ? "Yes" : "No";
        $row[0]['CHECK_SYSTEM_STORE_SELECTION'] = ($systemStoreEnable > 0) ? "Yes" : "No";
        $row[0]['ENABLE_ADD_PROVIDER_FROM_STORE'] = $MODULES_OBJ->isStorePersonalDriverAvailable() ? 'Yes' : 'No';
        $row[0]['APP_HOME_PAGE_LIST_VIEW_ENABLED'] = $MODULES_OBJ->isEnableAppHomePageListView() ? "Yes" : "No"; //Added By HJ On 24-09-2020 For Enable/Disable Home Page List View Type
        $row[0]['DELIVERY_LATER_BOOKING_ENABLED'] = $MODULES_OBJ->isEnableDeliveryScheduleLaterBooking() ? "Yes" : "No"; //Added By HJ On 26-09-2020 For Enable/Disable Delivery Later Booking Module
        /* added by SP on 10-08-2020 for page active or not */
        $getPageData = $obj->MySQLSelect("SELECT iPageId,eStatus FROM pages WHERE iPageId IN(4,33,52)");
        foreach ($getPageData as $kPage => $vPage) {
            if ($vPage['iPageId'] == 4) $pagename = "showTermsCondition";
            if ($vPage['iPageId'] == 33) $pagename = "showPrivacyPolicy";
            if ($vPage['iPageId'] == 52) $pagename = "showAboutUs";
            $row[0][$pagename] = $vPage['eStatus'] == 'Active' ? 'Yes' : 'No';
        }
        /*added by SP on 18-09-2020 */
        $row[0]['IS_RIDE_MODULE_AVAIL'] = $MODULES_OBJ->isRideFeatureAvailable() ? "Yes" : "No";
        $row[0]['IS_DELIVERY_MODULE_AVAIL'] = $MODULES_OBJ->isDeliveryFeatureAvailable() ? "Yes" : "No";
        $row[0]['IS_UFX_MODULE_AVAIL'] = $MODULES_OBJ->isUberXFeatureAvailable() ? "Yes" : "No";
        $row[0]['IS_DELIVERALL_MODULE_AVAIL'] = $MODULES_OBJ->isDeliverAllFeatureAvailable() ? "Yes" : "No";
        $row[0]['PICK_DROP_GENIE'] = PICK_DROP_GENIE; // Added by HV on 12-10-2020 for Genie Pickup/Dropoff Items
        // Added by HV for Restrict Passenger Limit Feature
        $row[0]['RESTRICT_PASSENGER_LIMIT_INFO_URL'] = $tconfig['tsite_url'] . 'safety_checklist.php?iPageId=55&vLang=' . $vLanguage;
        $row[0]['isShowSearchedItemEnabled'] = ($MODULES_OBJ->isEnableItemSearchStoreOrder()) ? "Yes" : "No";
        $row[0]['ENABLE_DELIVERY_INSTRUCTIONS_ORDERS'] = ($MODULES_OBJ->isEnableVoiceDeliveryInstructionsOrder()) ? "Yes" : "No";
        /* Default card according to Payment Gateway*/
        $countryPaymentMethod = $obj->MySQLSelect("SELECT vPaymentGateway FROM country WHERE vCountryCode = '" . $row[0]['vCountry'] . "'");
        $USER_APP_PAYMENT_METHOD = $APP_PAYMENT_METHOD;
        if (!empty($countryPaymentMethod[0]['vPaymentGateway'])) {
            $USER_APP_PAYMENT_METHOD = $countryPaymentMethod[0]['vPaymentGateway'];
        }
        $userPaymentInfo = getPaymentDefaultCard($passengerID, 'Passenger');
        $row[0]['vCreditCard'] = (count($userPaymentInfo) > 0) ? $userPaymentInfo[0]['tCardNum'] : "";

        //UpdateAppTerminateStatus($passengerID, "Passenger");
        $row[0]['APP_LAUNCH_IMAGES'] = "";
        if (!empty(getAppLaunchImages($vLanguage, 'Passenger'))) {
            $row[0]['APP_LAUNCH_IMAGES'] = getAppLaunchImages($vLanguage, 'Passenger');
        }
        $row[0]['ENABLE_RIDE_DELIVERY_NEW_FLOW'] = ($MODULES_OBJ->isEnableRideDeliveryV1()) ? "Yes" : "No";
        $row[0]['TSITE_DB'] = TSITE_DB;
        $row[0]['GOOGLE_API_REPLACEMENT_URL'] = GOOGLE_API_REPLACEMENT_URL;
        $row[0]['ENABLE_APPLE_LOGIN_FOR_USER'] = ($MODULES_OBJ->isEnableAppleLoginForUser()) ? "Yes" : "No";
        $row[0]['WEBRTC_SOCKET_URL'] = WEBRTC_SOCKET_URL;
        $row[0]['WEBRTC_STUN_URL'] = WEBRTC_STUN_URL;
        $row[0]['WEBRTC_TURN_URL'] = WEBRTC_TURN_URL;
        $row[0]['WEBRTC_ICE_SERVER_LIST'] = WEBRTC_ICE_SERVER_LIST;
        $row[0]['WEBRTC_USERNAME'] = $tconfig["tsite_webrtc_username"];
        $row[0]['WEBRTC_PASS'] = $tconfig["tsite_webrtc_pass"];
        $row[0]['isSmartLoginEnable'] = $MODULES_OBJ->isEnableSmartLogin() ? "Yes" : "No";
        $row[0]['RIDE_ENABLED'] = RIDE_ENABLED;
        $row[0]['DELIVERY_ENABLED'] = DELIVERY_ENABLED;
        $row[0]['UFX_ENABLED'] = UFX_ENABLED;
        $row[0]['DELIVERALL_ENABLED'] = DELIVERALL_ENABLED;
        $row[0]['GENIE_ENABLED'] = GENIE_ENABLED;
        $row[0]['RUNNER_ENABLED'] = RUNNER_ENABLED;
        $row[0]['BIDDING_ENABLED'] = BIDDING_ENABLED;
        $row[0]['VC_ENABLED'] = VC_ENABLED;
        $row[0]['MED_UFX_ENABLED'] = MED_UFX_ENABLED;
        $row[0]['RENT_ITEM_ENABLED'] = RENT_ITEM_ENABLED;
        $row[0]['RENT_ESTATE_ENABLED'] = RENT_ESTATE_ENABLED;
        $row[0]['RENT_CARS_ENABLED'] = RENT_CARS_ENABLED;
        $row[0]['NEARBY_ENABLED'] = NEARBY_ENABLED;
        $row[0]['TRACK_SERVICE_ENABLED'] = TRACK_SERVICE_ENABLED;
        $row[0]['RIDE_SHARE_ENABLED'] = RIDE_SHARE_ENABLED;
        $row[0]['TRACK_ANY_SERVICE_ENABLED'] = TRACK_ANY_SERVICE_ENABLED;
        $row[0]['ENABLE_LIVE_CHAT_TRACK_ORDER'] = (isset($row[0]['ENABLE_LIVE_CHAT'])) ? $row[0]['ENABLE_LIVE_CHAT'] : ENABLE_LIVE_CHAT_TRACK_ORDER;
        $row[0] = getCustomeNotificationSound($row[0]);

        if ($MODULES_OBJ->isEnableGiftCardFeature()) {
            $row[0]['GIFT_CARD_IMAGES'] = $GIFT_CARD_OBJ->getGiftCardImages();
            $row[0]['GIFT_CARD_MAX_AMOUNT_WITH_SYMBOL'] = formateNumAsPerCurrency($row[0]['GIFT_CARD_MAX_AMOUNT'], $vCurrencyPassenger);
            $row[0]['PREVIEW_GIFT_CARD_URL'] = $tconfig['tsite_url'] . 'preview_gift_card.php?';
            $row[0]['TERMS_&_CONDITIONS_GIFT_CARD_URL'] = $tconfig['tsite_url'] . 'terms_conditions_gift_card.php';
        }
        if ($CONFIG_OBJ->isOnlyCashPaymentModeAvailable() || $row[0]['WALLET_ENABLE'] == 'No') {
            $row[0]['ENABLE_GIFT_CARD_FEATURE'] = 'No';
        }


        if($MODULES_OBJ->isEnableTrackAnyServiceFeature()) {
            $row[0]['TRACK_ANY_SERVICE_CATEGORIES'] = $TRACK_ANY_SERVICE_OBJ->getServiceCategories($vLanguage);
        }
 
        $passenger_nos_arr = array();
        for ($i = 1; $i <= RIDE_SHARE_PASSENGER_NOS; $i++) {
            $passenger_nos_arr[] = $i;
        }
        $row[0]['RIDE_SHARE_PASSENGER_NOS'] = $passenger_nos_arr;

        $row[0]['ENABLE_RENT_ITEM_SERVICES'] = $MODULES_OBJ->isEnableRentItemService() ? "Yes" : "No";
        $row[0]['ENABLE_RENT_ESTATE_SERVICES'] = $MODULES_OBJ->isEnableRentEstateService() ? "Yes" : "No";
        $row[0]['ENABLE_RENT_CARS_SERVICES'] = $MODULES_OBJ->isEnableRentCarsService() ? "Yes" : "No";
        $row[0]['SERVICE_CATEGORIES_ARR'] = SERVICE_CATEGORIES_ARR;

        $row[0]['IS_CUBEX_APP'] = IS_CUBEX_APP;
        $row[0]['IS_DELIVERYKING_APP'] = IS_DELIVERYKING_APP;
 
        $row[0]['ONLY_ENABLE_RIDE_SHARING_PRO'] = ONLY_ENABLE_RIDE_SHARING_PRO;
        $row[0]['ONLY_ENABLE_BUY_SELL_RENT_PRO'] = ONLY_ENABLE_BUY_SELL_RENT_PRO;
        $row[0]['ONLY_ENABLE_FET_APP'] = ONLY_ENABLE_FET_APP;
    
        $row[0]['CARD_SAVE_ENABLE'] = isEnableAddCard()['CARD_SAVE_ENABLE'];

       
        unset($row[0]['tLocationUpdateDate']);
        unset($row[0]['tSeenAdvertiseTime']);
        unset($row[0]['CRON_TIME']);
        unset($row[0]['tLastOnline']);
        unset($row[0]['vLatitude']);
        unset($row[0]['vLongitude']);
        unset($row[0]['MAIL_FOOTER']);


        /* fetch value */
        return $row[0];
    } else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
        setDataResponse($returnArr);
    }
}

function getPassengerDetailInfoDeliverAll($passengerID, $cityName = "", $LiveTripId = "")
{
    global  $CONFIG_OBJ,$GIFT_CARD_OBJ,  $obj, $demo_site_msg, $PHOTO_UPLOAD_SERVICE_ENABLE, $parent_ufx_catid, $generalSystemConfigDataArr, $tconfig, $vTimeZone, $vUserDeviceCountry, $_REQUEST, $intervalmins, $generalConfigPaymentArr, $ENABLE_RIDER_ADVERTISEMENT_BANNER, $ADVERTISEMENT_TYPE, $RIDER_REQUEST_ACCEPT_TIME, $userDetailsArr, $tripDetailsArr, $vSystemDefaultCurrencyName, $vSystemDefaultCurrencySymbol, $languageLabelDataArr, $currencyAssociateArr, $country_data_arr, $country_data_retrieve, $Data_ALL_currency_Arr, $APP_PAYMENT_METHOD, $MODULES_OBJ, $WALLET_OBJ, $iServiceId, $LANG_OBJ;

    $where = " iUserId = '" . $passengerID . "'";
    $data_version['iAppVersion'] = "2";
    $data_version['eLogout'] = 'No';
    $data_version['eDebugMode'] = isset($_REQUEST["IS_DEBUG_MODE"]) ? $_REQUEST["IS_DEBUG_MODE"] : "";
    $data_version['tApiFileName'] = pathinfo(__FILE__, PATHINFO_FILENAME);
    #################################### Generate Session For GeoAPI ########################################
    generateSessionForGeo($passengerID, "Passenger");
    #################################### Generate Session For GeoAPI ########################################
    #################################### Configure App Version Info ########################################
    $arr_app_version = array();
    $arr_app_version['AppVersionName'] = isset($_REQUEST['GeneralAppVersion']) ? $_REQUEST['GeneralAppVersion'] : "";
    $arr_app_version['AppVersionCode'] = isset($_REQUEST['GeneralAppVersionCode']) ? $_REQUEST['GeneralAppVersionCode'] : "";
    #################################### Configure App Version Info ########################################
    $data_version['tVersion'] = strval(json_encode($arr_app_version));
    $data_version['tDeviceData'] = isset($_REQUEST['DEVICE_DATA']) ? $_REQUEST['DEVICE_DATA'] : "";
    $data_version['eHmsDevice'] = isset($_REQUEST['HMS_DEVICE']) ? $_REQUEST['HMS_DEVICE'] : "No";
    $obj->MySQLQueryPerform("register_user", $data_version, 'update', $where);
    $obj->sql_query("UPDATE trip_status_messages SET eReceived='Yes' WHERE iUserId='" . $passengerID . "' AND eToUserType='Passenger'");
    $row = $obj->MySQLSelect("SELECT *,iUserId as iMemberId FROM `register_user` WHERE iUserId='$passengerID'");
    $userDetailsArr["register_user_" . $passengerID] = $row;
    if ($LiveTripId != "") {
        //$sql_livetrip = "SELECT iTripId,iActive,vTripPaymentMode,iVehicleTypeId,fPickUpPrice,fNightPrice,vCouponCode,eType FROM `trips` WHERE iTripId='" . $LiveTripId . "'";
        //$userlivetripdetails = $obj->MySQLSelect($sql_livetrip);
        //Added By HJ On 10-07-2020 For Optimization trips Table Query Start
        if (isset($tripDetailsArr['trips_' . $LiveTripId])) {
            $userlivetripdetails = $tripDetailsArr['trips_' . $LiveTripId];
        }
        else {
            $userlivetripdetails = $obj->MySQLSelect("SELECT * FROM `trips` WHERE iTripId = '" . $LiveTripId . "'");
            $tripDetailsArr['trips_' . $LiveTripId] = $userlivetripdetails;
        }
        //Added By HJ On 10-07-2020 For Optimization trips Table Query End
        $row[0]['iTripId'] = $userlivetripdetails[0]['iTripId'];
        $row[0]['vTripStatus'] = $userlivetripdetails[0]['iActive'];
        $row[0]['vTripPaymentMode'] = $userlivetripdetails[0]['vTripPaymentMode'];
        $row[0]['iSelectedCarType'] = $userlivetripdetails[0]['iVehicleTypeId'];
        $row[0]['fPickUpPrice'] = $userlivetripdetails[0]['fPickUpPrice'];
        $row[0]['fNightPrice'] = $userlivetripdetails[0]['fNightPrice'];
        $row[0]['vCouponCode'] = $userlivetripdetails[0]['vCouponCode'];
        $row[0]['eType'] = $userlivetripdetails[0]['eType'];
    }
    if (count($row) > 0) {
        ### Update Tripid - 0 and TripStatus - None For UberX Trip ###
        $currenttrip = $row[0]['iTripId'];
        if ($currenttrip > 0) {
            //$sql = "SELECT eType,eSystem FROM `trips` WHERE iTripId = '" . $currenttrip . "'";
            //$db_currenttrip = $obj->MySQLSelect($sql);
            //Added By HJ On 10-07-2020 For Optimization trips Table Query Start
            if (isset($tripDetailsArr['trips_' . $currenttrip])) {
                $db_currenttrip = $tripDetailsArr['trips_' . $currenttrip];
            }
            else {
                $db_currenttrip = $obj->MySQLSelect("SELECT * FROM `trips` WHERE iTripId = '" . $currenttrip . "'");
                $tripDetailsArr['trips_' . $currenttrip] = $db_currenttrip;
            }
            //Added By HJ On 10-07-2020 For Optimization trips Table Query End
            if (count($db_currenttrip) > 0) {
                $currenttriptype = $db_currenttrip[0]['eType'];
                $currenttripsystem = $db_currenttrip[0]['eSystem'];
                if (($currenttriptype == "UberX" || $currenttriptype == "Multi-Delivery") && $LiveTripId == "") {
                    $result = $obj->sql_query("UPDATE register_user set iTripId = '0',vTripStatus = 'NONE' WHERE iUserId ='" . $passengerID . "'");
                    $row[0]['vTripStatus'] = "NONE";
                    $row[0]['iTripId'] = 0;
                }
                if ($currenttripsystem == "DeliverAll") {
                    $row[0]['vTripStatus'] = "NONE";
                    $row[0]['iTripId'] = 0;
                }
            }
        }
        //Added By HJ On 10-07-2020 For Optimization currency Table Query Start
        if (!empty($vSystemDefaultCurrencyName) && !empty($vSystemDefaultCurrencySymbol)) {
            $row[0]['vFlutterwaveCurrency'] = $vSystemDefaultCurrencyName;
            $vFlutterwavevSymbol = $vSystemDefaultCurrencySymbol;
        }
        else {
            $defaultCurrencyDataArr = get_value('currency', 'vName,vSymbol', 'eDefault', 'Yes');
            /* Added By PM On 09-12-2019 For Flutterwave Code Start */
            $row[0]['vFlutterwaveCurrency'] = $defaultCurrencyDataArr[0]['vName'];
            $vFlutterwavevSymbol = $defaultCurrencyDataArr[0]['vSymbol'];
        }
        //Added By HJ On 10-07-2020 For Optimization currency Table Query End
        ### Update Tripid - 0 and TripStatus - None For UberX Trip ###
        $page_link = $tconfig['tsite_url'] . "sign-up_rider.php?UserType=Rider&vRefCode=" . $row[0]['vRefCode'];
        $link = get_tiny_url($page_link);
        //$activation_text = '<a href="'.$link.'" target="_blank"> '.$link.' </a>';
        $activation_text = "<a href='" . $link . "' target='_blank'> '" . $link . "' </a>";
        $vLanguage = $row[0]['vLang'];
        if ($vLanguage == "" || $vLanguage == NULL) {
            $vLanguage = $LANG_OBJ->FetchDefaultLangData("vCode");
        }
        //Added By HJ On 10-07-2020 For langauge labele and Other Union Table Query Start
        if (isset($languageLabelDataArr['language_label_union_other_food_' . $vLanguage . "_" . $iServiceId])) {
            $langLabels = $languageLabelDataArr['language_label_union_other_food_' . $vLanguage . "_" . $iServiceId];
        }
        else {
            $langLabels = $LANG_OBJ->FetchLanguageLabels($vLanguage, "1");
            $languageLabelDataArr['language_label_union_other_food_' . $vLanguage . "_" . $iServiceId] = $langLabels;
        }
        //Added By HJ On 10-07-2020 For langauge labele and Other Union Table Query End
        if (isset($langLabels['LBL_SHARE_CONTENT_PASSENGER']) && trim($langLabels['LBL_SHARE_CONTENT_PASSENGER']) != "") {
            $LBL_SHARE_CONTENT_PASSENGER = $langLabels['LBL_SHARE_CONTENT_PASSENGER'];
        }
        else {
            $db_label = $obj->MySQLSelect("SELECT * FROM `language_label` WHERE vLabel = 'LBL_SHARE_CONTENT_PASSENGER' AND vCode = '" . $vLanguage . "'");
            $LBL_SHARE_CONTENT_PASSENGER = $db_label[0]['vValue'];
        }
        $row[0]['INVITE_SHARE_CONTENT'] = $LBL_SHARE_CONTENT_PASSENGER . " " . $link;
        //Added By HJ On 15-07-2020 For Optimize currency Table Query Start
        $vCurrencyPassenger = $row[0]['vCurrencyPassenger'];
        if (isset($currencyAssociateArr[$vCurrencyPassenger])) {
            $userCurrencyRatio = $currencyAssociateArr[$vCurrencyPassenger]['Ratio'];
        }
        else {
            $userCurrencyRatio = get_value('currency', 'Ratio', 'vName', $row[0]['vCurrencyPassenger'], '', 'true');
        }
        foreach ($generalSystemConfigDataArr as $key => $value) {
            if (is_null($generalSystemConfigDataArr[$key]) || empty($generalSystemConfigDataArr[$key])) {
                $generalSystemConfigDataArr[$key] = "";
            }
            if (in_array($key, ["TIP_AMOUNT_1", "TIP_AMOUNT_2", "TIP_AMOUNT_3"])) {
                if ($generalSystemConfigDataArr['DELIVERY_TIP_AMOUNT_TYPE_DELIVERALL'] == "Flat") {
                    $generalSystemConfigDataArr[$key] = round($value * $userCurrencyRatio);
                }
                else {
                    $generalSystemConfigDataArr[$key] = $value . '%';
                }
            }
        }
        $row[0] = array_merge($row[0], $generalSystemConfigDataArr); // Added By HJ On 18-03-2020 For Optimized Function
        $row[0]['PACKAGE_TYPE'] = strtoupper(PACKAGE_TYPE);
        if (!empty($_REQUEST['APP_TYPE'])) {
            $row[0]['APP_TYPE'] = $_REQUEST['APP_TYPE'];
        }
        $row[0]['GOOGLE_ANALYTICS'] = "";
        $row[0]['SERVER_MAINTENANCE_ENABLE'] = $row[0]['MAINTENANCE_APPS'];
        //Added By HJ On 16-07-2019 For Check Empty and # Value Of Configuration Start
        if (isset($row[0]['LIVE_CHAT_LICENCE_NUMBER']) && ($row[0]['LIVE_CHAT_LICENCE_NUMBER'] == "" || strpos($row[0]['LIVE_CHAT_LICENCE_NUMBER'], '#') !== false)) {
            $row[0]['ENABLE_LIVE_CHAT'] = "No";
        }

        $usercountrycode = "";
        if (isset($row[0]['AUDIO_CALLING_METHOD']) && strtoupper($row[0]['AUDIO_CALLING_METHOD']) == "SINCH") {
            if (isset($row[0]['SINCH_APP_ENVIRONMENT_HOST']) && (empty($row[0]['SINCH_APP_ENVIRONMENT_HOST']) || strpos($row[0]['SINCH_ APP_ENVIRONMENT_HOST'], '#') !== false)) {
                $row[0]['RIDE_DRIVER_CALLING_METHOD'] = "Normal";
            }
            if (isset($row[0]['SINCH_APP_KEY']) && ($row[0]['SINCH_APP_KEY'] == "" || strpos($row[0]['SINCH_APP_KEY'], '#') !== false)) {
                $row[0]['RIDE_DRIVER_CALLING_METHOD'] = "Normal";
            }
            if (isset($row[0]['SINCH_APP_SECRET_KEY']) && ($row[0]['SINCH_APP_SECRET_KEY'] == "" || strpos($row[0]['SINCH_APP_SECRET_KEY'], '#') !== false)) {
                $row[0]['RIDE_DRIVER_CALLING_METHOD'] = "Normal";
            }
            //Added By HJ On 16-07-2019 For Check Empty and # Value Of Configuration End
            //Added By HJ On 21-10-2020 For Enable/Disable Sinch Base On Country Configuration Start
            $usercountrycode = $row[0]['vCountry'];
            if ($usercountrycode != "") {
                $eEnableSinch = checkCountryVoipMethod($usercountrycode);
                if (strtoupper($eEnableSinch) == "NO") {
                    $row[0]['RIDE_DRIVER_CALLING_METHOD'] = "Normal";
                }
            }
            //Added By HJ On 21-10-2020 For Enable/Disable Sinch Base On Country Configuration End
        }
        $RIDER_EMAIL_VERIFICATION = $row[0]["RIDER_EMAIL_VERIFICATION"];
        $RIDER_PHONE_VERIFICATION = $row[0]["RIDER_PHONE_VERIFICATION"];
        if ($row[0]['MULTI_LEVEL_REFERRAL_SCHEME_ENABLE'] == "Yes") {
            //added by SP on 03-03-2021 when multilevel then only shows text not amt..becoz it will not be calculated here..
            if (isset($langLabels['LBL_REFERRAL_AMOUNT']) && trim($langLabels['LBL_REFERRAL_AMOUNT']) != "") {
                $LBL_REFERRAL_AMOUNT = $langLabels['LBL_REFERRAL_AMOUNT'];
            }
            else {
                $LBL_REFERRAL_AMOUNT = get_value('language_label', 'vValue', 'vLabel', 'LBL_REFERRAL_AMOUNT', " and vCode='" . $vLanguage . "'", 'true');
            }
            $REFERRAL_AMOUNT_USER = $LBL_REFERRAL_AMOUNT;
        }
        else {
            $REFERRAL_AMOUNT = $row[0]["REFERRAL_AMOUNT"];
            $REFERRAL_AMOUNT_ARR = getPriceUserCurrency($passengerID, "Passenger", $REFERRAL_AMOUNT);
            $REFERRAL_AMOUNT_USER = $REFERRAL_AMOUNT_ARR['fPricewithsymbol'];
        }
        if (isset($langLabels['LBL_INVITE_FRIEND_SHARE_PREFIX_TXT']) && trim($langLabels['LBL_INVITE_FRIEND_SHARE_PREFIX_TXT']) != "") {
            $LBL_INVITE_FRIEND_SHARE_PREFIX_TXT = $langLabels['LBL_INVITE_FRIEND_SHARE_PREFIX_TXT'];
        }
        else {
            $LBL_INVITE_FRIEND_SHARE_PREFIX_TXT = get_value('language_label', 'vValue', 'vLabel', 'LBL_INVITE_FRIEND_SHARE_PREFIX_TXT', " and vCode='" . $vLanguage . "'", 'true');
        }
        if (isset($langLabels['LBL_INVITE_FRIEND_SHARE_POSTFIX_TXT']) && trim($langLabels['LBL_INVITE_FRIEND_SHARE_POSTFIX_TXT']) != "") {
            $LBL_INVITE_FRIEND_SHARE_POSTFIX_TXT = $langLabels['LBL_INVITE_FRIEND_SHARE_POSTFIX_TXT'];
        }
        else {
            $LBL_INVITE_FRIEND_SHARE_POSTFIX_TXT = get_value('language_label', 'vValue', 'vLabel', 'LBL_INVITE_FRIEND_SHARE_POSTFIX_TXT', " and vCode='" . $vLanguage . "'", 'true');
        }

        if($MODULES_OBJ->isOnlyEnableRideSharingPro()){

             if (isset($langLabels['LBL_RIDE_SHARE_INVITE_FRIEND_SHARE_POSTFIX_TXT']) && trim($langLabels['LBL_RIDE_SHARE_INVITE_FRIEND_SHARE_POSTFIX_TXT']) != "") {
                 $LBL_INVITE_FRIEND_SHARE_POSTFIX_TXT = $langLabels['LBL_RIDE_SHARE_INVITE_FRIEND_SHARE_POSTFIX_TXT'];
             }
             else {
                 $LBL_INVITE_FRIEND_SHARE_POSTFIX_TXT = get_value('language_label', 'vValue', 'vLabel', 'LBL_RIDE_SHARE_INVITE_FRIEND_SHARE_POSTFIX_TXT', " and vCode='" . $vLanguage . "'", 'true');
             }
        }


        $row[0]['INVITE_DESCRIPTION_CONTENT'] = $LBL_INVITE_FRIEND_SHARE_PREFIX_TXT . " " . $REFERRAL_AMOUNT_USER . " " . $LBL_INVITE_FRIEND_SHARE_POSTFIX_TXT;
        if ($RIDER_EMAIL_VERIFICATION == 'No') {
            $row[0]['eEmailVerified'] = "Yes";
        }
        if ($RIDER_PHONE_VERIFICATION == 'No') {
            $row[0]['ePhoneVerified'] = "Yes";
        }
        $lang_usr = $row[0]['vLang'];
        $sql = "select vBody_$lang_usr as Message from send_message_templates where vEmail_Code = 'VERIFICATION_CODE_MESSAGE'";
        $data_SMS = $obj->MySQLSelect($sql);
        $row[0]['SMS_BODY'] = $data_SMS[0]['Message'];
        ## Display Braintree Charge Message ##
        /*if (isset($langLabels['LBL_BRAINTREE_CHARGE_MSG_TXT']) && trim($langLabels['LBL_BRAINTREE_CHARGE_MSG_TXT']) != "") {
            $LBL_BRAINTREE_CHARGE_MSG_TXT = $langLabels['LBL_BRAINTREE_CHARGE_MSG_TXT'];
        }
        else {
            $db_label_braintree = $obj->MySQLSelect("SELECT * FROM `language_label` WHERE vLabel = 'LBL_BRAINTREE_CHARGE_MSG_TXT' AND vCode = '" . $vLanguage . "'");
            $LBL_BRAINTREE_CHARGE_MSG_TXT = $db_label_braintree[0]['vValue'];
        }
        $BRAINTREE_CHARGE_AMOUNT = $row[0]['BRAINTREE_CHARGE_AMOUNT'];
        $BRAINTREE_CHARGE_AMOUNT_USER_ARR = getPriceUserCurrency($passengerID, "Passenger", $BRAINTREE_CHARGE_AMOUNT);
        $BRAINTREE_CHARGE_AMOUNT_USER = $BRAINTREE_CHARGE_AMOUNT_USER_ARR['fPricewithsymbol'];
        //$msg = str_replace('##AMOUNT##', $BRAINTREE_CHARGE_AMOUNT_USER, $LBL_BRAINTREE_CHARGE_MSG_TXT);
        $msg = str_replace('####', $BRAINTREE_CHARGE_AMOUNT_USER, $LBL_BRAINTREE_CHARGE_MSG_TXT);
        $row[0]['BRAINTREE_CHARGE_MESSAGE'] = $msg;
        ## Display Braintree Charge Message ##
        ## Display Adyen Charge Message ##
        if (isset($langLabels['LBL_ADYEN_CHARGE_MSG_TXT']) && trim($langLabels['LBL_ADYEN_CHARGE_MSG_TXT']) != "") {
            $LBL_ADYEN_CHARGE_MSG_TXT = $langLabels['LBL_ADYEN_CHARGE_MSG_TXT'];
        }
        else {
            $db_label_adyen = $obj->MySQLSelect("SELECT * FROM `language_label` WHERE vLabel = 'LBL_ADYEN_CHARGE_MSG_TXT' AND vCode = '" . $vLanguage . "'");
            $LBL_ADYEN_CHARGE_MSG_TXT = $db_label_adyen[0]['vValue'];
        }
        $ADEYN_CHARGE_AMOUNT = $row[0]['ADYEN_CHARGE_AMOUNT'];
        $ADEYN_CHARGE_AMOUNT_USER_ARR = getPriceUserCurrency($passengerID, "Passenger", $ADEYN_CHARGE_AMOUNT);
        $ADEYN_CHARGE_AMOUNT_USER = $ADEYN_CHARGE_AMOUNT_USER_ARR['fPricewithsymbol'];
        $msg = str_replace('####', $ADEYN_CHARGE_AMOUNT_USER, $LBL_ADYEN_CHARGE_MSG_TXT);
        $row[0]['ADEYN_CHARGE_MESSAGE'] = $msg;*/
        ## Display Adyen Charge Message ##
        /* Added By PM On 09-12-2019 For Flutterwave Code Start */
        ## Display Flutterwave Charge Message ##
        if (isset($langLabels['LBL_FLUTTERWAVE_CHARGE_MSG_TXT']) && trim($langLabels['LBL_FLUTTERWAVE_CHARGE_MSG_TXT']) != "") {
            $LBL_FLUTTERWAVE_CHARGE_MSG_TXT = $langLabels['LBL_FLUTTERWAVE_CHARGE_MSG_TXT'];
        }
        else {
            $db_label_flutter = $obj->MySQLSelect("SELECT * FROM `language_label` WHERE vLabel = 'LBL_FLUTTERWAVE_CHARGE_MSG_TXT' AND vCode = '" . $vLanguage . "'");
            $LBL_FLUTTERWAVE_CHARGE_MSG_TXT = $db_label_flutter[0]['vValue'];
        }
        $amountDataArr = getSupportedCurrencyAmt($row[0]['FLUTTERWAVE_CHARGE_AMOUNT'], $row[0]['vFlutterwaveCurrency']);
        $row[0]['vFlutterwaveCurrency'] = $amountDataArr['CURRENCY_CODE'];
        $FLUTTERWAVE_CHARGE_AMOUNT = $amountDataArr['AMOUNT'];
        $FLUTTERWAVE_CHARGE_AMOUNT_USER_ARR = $FLUTTERWAVE_CHARGE_AMOUNT;
        $FLUTTERWAVE_CHARGE_AMOUNT_USER = $vFlutterwavevSymbol . $FLUTTERWAVE_CHARGE_AMOUNT;
        $msg = str_replace('####', $FLUTTERWAVE_CHARGE_AMOUNT_USER, $LBL_FLUTTERWAVE_CHARGE_MSG_TXT);
        $row[0]['FLUTTERWAVE_CHARGE_AMOUNT'] = $FLUTTERWAVE_CHARGE_AMOUNT;
        $row[0]['FLUTTERWAVE_CHARGE_MESSAGE'] = $msg;
        /* Added By PM On 09-12-2019 For Flutterwave Code Start */
        if (isset($row[0]['vCreditCard']) && stripos($row[0]['vCreditCard'], 'XXXX') === false && !empty($row[0]['vCreditCard'])) {
            $row[0]['vCreditCard'] = 'XXXXXXXXXXXX' . $row[0]['vCreditCard'];
        }
        ## Check and update Device Session ID ##
        if ($row[0]['tDeviceSessionId'] == "") {
            $random = substr(md5(rand()), 0, 7);
            $Update_Device_Session['tDeviceSessionId'] = session_id() . time() . $random;
            $Update_Device_Session_id = $obj->MySQLQueryPerform("register_user", $Update_Device_Session, 'update', $where);
            $row[0]['tDeviceSessionId'] = $Update_Device_Session['tDeviceSessionId'];
        }
        ## Check and update Device Session ID ##
        ## Check and update Session ID ##
        if ($row[0]['tSessionId'] == "") {
            $Update_Session['tSessionId'] = session_id() . time();
            $Update_Session_id = $obj->MySQLQueryPerform("register_user", $Update_Session, 'update', $where);
            $row[0]['tSessionId'] = $Update_Session['tSessionId'];
        }
        ## Check and update Session ID ##
        if ($row[0]['vImgName'] != "" && $row[0]['vImgName'] != "NONE") {
            $row[0]['vImgName'] = "3_" . $row[0]['vImgName'];
        }
        // $row[0]['Passenger_Password_decrypt']= decrypt($row[0]['vPassword']);
        $row[0]['Passenger_Password_decrypt'] = "";
        if ($row[0]['eStatus'] != "Active") {
            $returnArr['Action'] = "0";
            $returnArr['RESTRICT_APP'] = "Yes";
            $returnArr['message'] = "LBL_ACC_DELETE_TXT";
            $returnArr['message_title'] = "LBL_ACC_DELETE_TITLE";
            if ($row[0]['eStatus'] != "Deleted") {
                $returnArr['isAccountInactive'] = "Yes";
                $returnArr['message'] = "LBL_CONTACT_US_STATUS_NOTACTIVE_PASSENGER";
                $returnArr['message_title'] = "LBL_ACC_INACTIVE_TITLE";
            } else {
                $returnArr['isAccountDeleted'] = "Yes";
            }
            setDataResponse($returnArr);
        }
        $TripStatus = $row[0]['vTripStatus'];
        $TripID = $row[0]['iTripId'];
        $eType = "";
        if ($TripID != "" && $TripID != NULL && $TripID != 0) {
            //Added By HJ On 10-07-2020 For Optimization trips Table Query Start
            if (isset($tripDetailsArr["trips_" . $TripID]) && count($tripDetailsArr["trips_" . $TripID]) > 0) {
                $eType = $tripDetailsArr["trips_" . $TripID][0]['eType'];
            }
            else {
                $eType = get_value('trips', 'eType', 'iTripId', $TripID, '', 'true');
            }
            //Added By HJ On 10-07-2020 For Optimization trips Table Query End
        }
        if ($row[0]['APP_TYPE'] == "Ride-Delivery-UberX" || $row[0]['APP_TYPE'] == "Ride-Delivery") { // Changed By HJ On 02-04-2019 As Per Discuss With KS
            $ssql = " AND (eType = 'Ride' or eType = 'Deliver')";
        }
        else if ($row[0]['APP_TYPE'] == "Delivery") { // Added By HJ On 02-04-2019 As Per Discuss With KS
            $ssql = " AND eType = 'Deliver'";
        }
        $sqlcheckride = "SELECT iTripId,eType,iActive FROM `trips` WHERE (iActive='Active' OR iActive='On Going Trip') $ssql and iUserId = '" . $passengerID . "' AND eSystem = 'General' ORDER BY iTripId DESC LIMIT 0,1";
        $Tripcheckride = $obj->MySQLSelect($sqlcheckride);
        if (count($Tripcheckride) > 0) {
            $row[0]['vTripStatus'] = $Tripcheckride[0]['iActive'];
            $row[0]['iTripId'] = $Tripcheckride[0]['iTripId'];
            $row[0]['eType'] = $Tripcheckride[0]['eType'];
            $TripStatus = $row[0]['vTripStatus'];
            $TripID = $row[0]['iTripId'];
            $eType = $row[0]['eType'];
        }
        $sql1 = "SELECT iTripId FROM `trips` WHERE (iActive='Active' OR iActive='On Going Trip') and iUserId = '" . $passengerID . "' AND eSystem = 'General'";
        $check_trip = $obj->MySQLSelect($sql1);
        $row[0]['Allow_Edit_Profile'] = "Yes";
        if (count($Tripcheckride) > 0) {
            $row[0]['Allow_Edit_Profile'] = "No";
        }
        if ($LiveTripId == "" && $eType == "Multi-Delivery") {
            $row[0]['vTripStatus'] = "NONE";
            $row[0]['iTripId'] = "0";
            $TripStatus = $row[0]['vTripStatus'];
            $TripID = $row[0]['iTripId'];
        }
        if ($TripStatus != "NONE") {
            // $TripID = $row[0]['iTripId'];
            if ($LiveTripId != "") {
                $TripID = $LiveTripId;
            }
            else {
                $TripID = $row[0]['iTripId'];
            }
            $row_result_trips = FetchTripFareDetails($TripID, $passengerID, "Passenger");

            unset($row_result_trips['DriverDetails']['tLocationUpdateDate']);
            unset($row_result_trips['PassengerDetails']['tLocationUpdateDate']);

            $row[0]['TripDetails'] = $row_result_trips;
            $row[0]['DriverDetails'] = $row_result_trips['DriverDetails'];
            $row_result_trips['DriverCarDetails']['make_title'] = $row_result_trips['DriverCarDetails']['vMake'];
            $row_result_trips['DriverCarDetails']['model_title'] = $row_result_trips['DriverCarDetails']['vTitle'];
            $row[0]['DriverCarDetails'] = $row_result_trips['DriverCarDetails'];
            $row_result_payments = $obj->MySQLSelect("SELECT vPaymentUserStatus FROM `payments` WHERE iTripId='" . $TripID . "'");
            if (count($row_result_payments) > 0) {
                if ($row_result_payments[0]['vPaymentUserStatus'] != 'approved') {
                    $row[0]['PaymentStatus_From_Passenger'] = "Not Approved";
                }
                else {
                    $row[0]['PaymentStatus_From_Passenger'] = "Approved";
                }
            }
            else {
                $row[0]['PaymentStatus_From_Passenger'] = "No Entry";
            }
            $row_result_ratings_trip = $obj->MySQLSelect("SELECT iTripId,eUserType FROM `ratings_user_driver` WHERE iTripId='" . $TripID . "' AND vRating1 != '' ");
            if (count($row_result_ratings_trip) > 0) {
                $count_row_rating = 0;
                $ContentWritten = "false";
                while (count($row_result_ratings_trip) > $count_row_rating) {
                    $UserType = $row_result_ratings_trip[$count_row_rating]['eUserType'];
                    if ($UserType == "Passenger") {
                        $ContentWritten = "true";
                        $row[0]['Ratings_From_Passenger'] = "Done";
                    }
                    else if ($ContentWritten == "false") {
                        $row[0]['Ratings_From_Passenger'] = "Not Done";
                    }
                    $count_row_rating++;
                }
            }
            else {
                $row[0]['Ratings_From_Passenger'] = "No Entry";
            }
        }
        $sql = "SELECT ord.iOrderId,ord.iCompanyId,ord.iDriverId,ord.vOrderNo,ord.eTakeaway,ord.iServiceId,ord.eBuyAnyService FROM `orders` as ord WHERE ord.iUserId='" . $passengerID . "' AND ord.iStatusCode = '6' AND (select count(iRatingId) from ratings_user_driver as rud where rud.iOrderId=ord.iOrderId AND rud.eFromUserType = 'Passenger' ) = 0 AND ord.eBuyAnyService = 'No' ORDER BY ord.iOrderId DESC LIMIT 0,1";
        $row_order = $obj->MySQLSelect($sql);
        if (count($row_order) > 0) {
            $LastOrderId = $row_order[0]['iOrderId'];
            $LastOrderNo = $row_order[0]['vOrderNo'];
            $LastOrderCompanyId = $row_order[0]['iCompanyId'];
            $LastOrderDriverId = $row_order[0]['iDriverId'];
            //Added By HJ On 21-07-2020 For Optimize register_driver Table Query Start
            if (isset($userDetailsArr['register_driver_' . $LastOrderDriverId])) {
                $result_driver = $userDetailsArr['register_driver_' . $LastOrderDriverId];
            }
            else {
                $result_driver = $obj->MySQLSelect("SELECT *,iDriverId as iMemberId FROM `register_driver` WHERE iDriverId = '" . $LastOrderDriverId . "'");
                $userDetailsArr['register_driver_' . $LastOrderDriverId] = $result_driver;
            }
            $result_driver[0]['driverName'] = $result_driver[0]['vName'] . " " . $result_driver[0]['vLastName'];
            //Added By HJ On 21-07-2020 For Optimize register_driver Table Query End
            //$result_driver = $obj->MySQLSelect("SELECT CONCAT(vName,' ',vLastName) AS driverName FROM register_driver WHERE iDriverId = '" . $LastOrderDriverId . "'");
            //Added By HJ On 21-07-2020 For Optimize company Table Query Start
            if (isset($userDetailsArr['company_' . $LastOrderCompanyId])) {
                $result_company = $userDetailsArr['company_' . $LastOrderCompanyId];
            }
            else {
                $result_company = $obj->MySQLSelect("SELECT *,iCompanyId as iMemberId FROM `company` WHERE iCompanyId = '" . $LastOrderCompanyId . "'");
                $userDetailsArr['company_' . $LastOrderCompanyId] = $result_company;
            }
            $result_company[0]['CompanyName'] = $result_company[0]['vCompany'];
            //Added By HJ On 21-07-2020 For Optimize company Table Query End
            //$result_company = $obj->MySQLSelect("SELECT vCompany AS CompanyName FROM company WHERE iCompanyId = '" . $LastOrderCompanyId . "'");
            $row_result_ratings = $obj->MySQLSelect("SELECT count(iRatingId) as TotalRating FROM `ratings_user_driver` WHERE iOrderId='" . $LastOrderId . "' AND eFromUserType = 'Passenger'  ");
            $TotalRating = $row_result_ratings[0]['TotalRating'];
            if ($TotalRating > 0) {
                $row[0]['Ratings_From_DeliverAll'] = "Done";
            }
            else {
                $row[0]['Ratings_From_DeliverAll'] = "Not Done";
            }
            $row[0]['LastOrderId'] = $LastOrderId;
            $row[0]['LastOrderNo'] = $LastOrderNo;
            $row[0]['LastOrderCompanyId'] = $LastOrderCompanyId;
            $row[0]['LastOrderCompanyName'] = $result_company[0]['CompanyName'];
            $row[0]['LastOrderTakeaway'] = $row_order[0]['eTakeaway'];
            $row[0]['LastOrderDriverId'] = $LastOrderDriverId;
            $row[0]['LastOrderDriverName'] = $result_driver[0]['driverName'];
            $row[0]['LastOrderFoodDetailRatingShow'] = "No";
            if ($MODULES_OBJ->isEnableFoodRatingDetailFlow() && $row_order[0]['iServiceId'] == "1" && $row_order[0]['eBuyAnyService'] == "No") {
                $check_rating_company = $obj->MySQLSelect("SELECT * FROM `ratings_user_driver` WHERE iOrderId = '" . $LastOrderId . "' and eFromUserType = 'Passenger' AND eToUserType = 'Company' ");
                $check_rating_driver = $obj->MySQLSelect("SELECT * FROM `ratings_user_driver` WHERE iOrderId = '" . $LastOrderId . "' and eFromUserType = 'Passenger' AND eToUserType = 'Driver'");
                if (empty($check_rating_company) || empty($check_rating_driver)) {
                    $row[0]['LastOrderFoodDetailRatingShow'] = "Yes";
                }
                else {
                    $row[0]['LastOrderFoodDetailRatingShow'] = "No";
                }
                $DRIVER_FEEDBACK_QUESTIONS = getFoodRatingDetailFeedbackQuestions($vLanguage);
                $row[0]['DRIVER_FEEDBACK_QUESTIONS'] = !empty($DRIVER_FEEDBACK_QUESTIONS) ? $DRIVER_FEEDBACK_QUESTIONS : "";
            }
        }
        else {
            $row[0]['Ratings_From_DeliverAll'] = "";
        }
        $result_Address = $obj->MySQLSelect("SELECT count(iUserAddressId) as ToTalAddress from user_address WHERE iUserId = '" . $passengerID . "' AND eUserType = 'Rider' AND eStatus = 'Active'");
        $row[0]['ToTalAddress'] = $result_Address[0]['ToTalAddress'];
        $row[0]['DefaultCurrencySign'] = $row[0]["DEFAULT_CURRENCY_SIGN"];
        $row[0]['DefaultCurrencyCode'] = $row[0]["DEFAULT_CURRENCY_CODE"];
        $row[0]['FETCH_TRIP_STATUS_TIME_INTERVAL'] = fetchtripstatustimeinterval();
        $row[0]['ENABLE_TOLL_COST'] = $row[0]['APP_TYPE'] != "UberX" ? $row[0]['ENABLE_TOLL_COST'] : "No";
        /* Check Toll Enable For Passenger's Country */
        if ($usercountrycode != "") {
            //Added By HJ On 09-06-2020 For Optimization country Table Query Start
            if (isset($country_data_arr[$usercountrycode]['eEnableToll']) && trim($country_data_arr[$usercountrycode]['eEnableToll']) != "") {
                $eEnableToll = $country_data_arr[$usercountrycode]['eEnableToll'];
            }
            else {
                $user_country_toll = $obj->MySQLSelect("SELECT eEnableToll from country WHERE vCountryCode = '" . $usercountrycode . "'");
                $eEnableToll = $user_country_toll[0]['eEnableToll'];
            }
            //Added By HJ On 09-06-2020 For Optimization country Table Query End
            //$user_country_toll = $obj->MySQLSelect("SELECT eEnableToll from country WHERE vCountryCode = '" . $usercountrycode . "'");
            //$eEnableToll = $user_country_toll[0]['eEnableToll'];
            if ($eEnableToll != "") {
                $row[0]['ENABLE_TOLL_COST'] = ($eEnableToll == "Yes" && $row[0]['ENABLE_TOLL_COST'] == "Yes") ? "Yes" : "No";
            }
        }
        /* Check Toll Enable For Passenger's Country */
        if ($row[0]['APP_TYPE'] == "Ride" || $row[0]['APP_TYPE'] == "Ride-Delivery" || $row[0]['APP_TYPE'] == "Ride-Delivery-UberX") {
            $row[0]['FEMALE_RIDE_REQ_ENABLE'] = $row[0]['FEMALE_RIDE_REQ_ENABLE'];
            $row[0]['HANDICAP_ACCESSIBILITY_OPTION'] = $row[0]['HANDICAP_ACCESSIBILITY_OPTION'];
        }
        else {
            $row[0]['FEMALE_RIDE_REQ_ENABLE'] = "No";
            $row[0]['HANDICAP_ACCESSIBILITY_OPTION'] = "No";
            // $row[0]['ENABLE_TOLL_COST'] = "No";
        }
        if ($row[0]['APP_TYPE'] == "Ride" || $row[0]['APP_TYPE'] == "Ride-Delivery" || $row[0]['APP_TYPE'] == "Ride-Delivery-UberX") {
            $row[0]['ENABLE_HAIL_RIDES'] = $row[0]['ENABLE_HAIL_RIDES'];
        }
        else {
            $row[0]['ENABLE_HAIL_RIDES'] = "No";
        }
        if ($row[0]['APP_PAYMENT_MODE'] == "Card") {
            $row[0]['ENABLE_HAIL_RIDES'] = "No";
        }
        // $user_available_balance = $WALLET_OBJ->FetchMemberWalletBalance($passengerID,"Rider");
        // $row[0]['user_available_balance'] = strval($WALLET_OBJ->MemberCurrencyWalletBalance(0,$user_available_balance,$row[0]['vCurrencyPassenger']));
        $user_available_balance = $WALLET_OBJ->FetchMemberWalletBalanceApp($passengerID, "Rider");
        $row[0]['user_available_balance'] = strval($user_available_balance);
        $user_available_balance_arr = explode(" ", $user_available_balance);
        $row[0]['user_available_balance_amount'] = strval($user_available_balance_arr[1]);
        $user_available_balance_value = $WALLET_OBJ->FetchMemberWalletBalanceApp($passengerID, "Rider", 'Yes');
        $row[0]['user_available_balance_value'] = strval($user_available_balance_value);
        if ($user_available_balance_value <= 0 || $user_available_balance_value <= 0.00) {
            $row[0]['eWalletBalanceAvailable'] = 'No';
        }
        else {
            $row[0]['eWalletBalanceAvailable'] = 'Yes';
        }
        // $row[0]['PHOTO_UPLOAD_SERVICE_ENABLE']=$PHOTO_UPLOAD_SERVICE_ENABLE;
        $row[0]['PHOTO_UPLOAD_SERVICE_ENABLE'] = $row[0]['APP_TYPE'] == "UberX" ? $PHOTO_UPLOAD_SERVICE_ENABLE : "No";
        $row[0]['ENABLE_TIP_MODULE'] = $row[0]['ENABLE_TIP_MODULE'];
        $host_arr = array();
        $host_arr = explode(".", $_SERVER["HTTP_HOST"]);
        $host_system = $host_arr[0];
        /*if ($_REQUEST['UBERX_PARENT_CAT_ID'] != "") {
            $parent_ufx_catid = $_REQUEST['UBERX_PARENT_CAT_ID'];
        } else {
            $parent_ufx_catid = "0";
        }

        if ($host_system == "carwash4") {
            $parent_ufx_catid = "1";
        }
        if ($host_system == "homecleaning4") {
            $parent_ufx_catid = "2";
        }
        if ($host_system == "doctor4") {
            $parent_ufx_catid = "3";
        }
        if ($host_system == "beautician4") {
            $parent_ufx_catid = "4";
        }
        if ($host_system == "massage4") {
            $parent_ufx_catid = "5";
        }
        if ($host_system == "tutors4") {
            $parent_ufx_catid = "7";
        }
        if ($host_system == "dogwalking4") {
            $parent_ufx_catid = "8";
        }
        if ($host_system == "towtruck4") {
            $parent_ufx_catid = "9";
        }
        if ($host_system == "plumbers4") {
            $parent_ufx_catid = "10";
        }
        if ($host_system == "electricians4") {
            $parent_ufx_catid = "11";
        }
        if ($host_system == "babysitting4") {
            $parent_ufx_catid = "12";
        }
        if ($host_system == "escorts4") {
            $parent_ufx_catid = "18";
        }
        if ($host_system == "fitnesscoach4") {
            $parent_ufx_catid = "13";
        }
        if ($host_system == "laundry4") {
            $parent_ufx_catid = "6";
        }
        if ($host_system == "snowplow4") {
            $parent_ufx_catid = "29";
        }
        if ($host_system == "securityguard4") {
            $parent_ufx_catid = "64";
        }*/
        $row[0]['UBERX_PARENT_CAT_ID'] = $parent_ufx_catid;
        //$row[0]['UBERX_PARENT_CAT_ID'] = 1;
        if ($row[0]['APP_TYPE'] == "UberX") {
            $row[0]['APP_DESTINATION_MODE'] = "None";
            $row[0]['ENABLE_TOLL_COST'] = "No";
            $row[0]['HANDICAP_ACCESSIBILITY_OPTION'] = "No";
            $row[0]['FEMALE_RIDE_REQ_ENABLE'] = "No";
            $row[0]['ENABLE_HAIL_RIDES'] = "No";
            $row[0]['ONLINE_DRIVER_LIST_UPDATE_TIME_INTERVAL'] = "5";
            $row[0]['ENABLE_CORPORATE_PROFILE'] = "No";
        }
        else {
            // $row[0]['APP_DESTINATION_MODE'] = "Strict";
        }
        $row[0]['ENABLE_DELIVERY_MODULE'] = SITE_TYPE == "Demo" ? $row[0]['eDeliverModule'] : $row[0]['ENABLE_DELIVERY_MODULE'];
        $row[0]['PayPalConfiguration'] = $row[0]['ENABLE_DELIVERY_MODULE'] == "Yes" ? "Yes" : $row[0]['PAYMENT_ENABLED'];

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
            $row[0]['LIST_CURRENCY'] = $defCurrencyValues;

            for ($i = 0; $i < count($defCurrencyValues); $i++) {
                $row[0]['LIST_CURRENCY'][$i]['vService_BG_color'] = RANDOM_COLORS_ARR[$i];
                $row[0]['LIST_CURRENCY'][$i]['vService_TEXT_color'] = "#FFFFFF";
            }
        }

        //Added By HJ On 08-06-2020 For Optimization currency Table Query End
        $row[0]['SITE_TYPE'] = SITE_TYPE;
        $row[0]['RIIDE_LATER'] = RIIDE_LATER;
        $row[0]['PROMO_CODE'] = PROMO_CODE;
        $row[0]['DELIVERALL'] = $MODULES_OBJ->isDeliverAllFeatureAvailable() ? DELIVERALL : "No";
        $row[0]['ONLYDELIVERALL'] = ONLYDELIVERALL;
        $row[0]['SITE_TYPE_DEMO_MSG'] = $demo_site_msg;
        //Added By HJ On 20-07-2020 For Optimization currency Table Query Start
        if (isset($currencyNameArr[$row[0]['vCurrencyPassenger']]['vSymbol']) && trim($currencyNameArr[$row[0]['vCurrencyPassenger']]['vSymbol']) != "") {
            $row[0]['CurrencySymbol'] = $currencyNameArr[$row[0]['vCurrencyPassenger']]['vSymbol'];
        }
        else {
            $row[0]['CurrencySymbol'] = get_value('currency', 'vSymbol', 'vName', $row[0]['vCurrencyPassenger'], '', 'true');
        }
        //Added By HJ On 20-07-2020 For Optimization currency Table Query End
        //$row[0]['CurrencySymbol'] = get_value('currency', 'vSymbol', 'vName', $row[0]['vCurrencyPassenger'], '', 'true');
        $eUnit = getMemberCountryUnit($passengerID, "Passenger");
        $row[0]['eUnit'] = $eUnit;
        $row[0]['SourceLocations'] = getusertripsourcelocations($passengerID, "SourceLocation");
        $row[0]['DestinationLocations'] = getusertripsourcelocations($passengerID, "DestinationLocation");
        if ($MODULES_OBJ->isEnableAnywhereDeliveryFeature()) {
            $row[0]['GenieLocations'] = getgenieorderlocations($passengerID);
        }
        $row[0]['DestinationLocations'] = getusertripsourcelocations($passengerID, "DestinationLocation");
        $sql = "SELECT * FROM user_fave_address where iUserId = '" . $passengerID . "' AND eUserType = 'Passenger' AND eStatus = 'Active' ORDER BY iUserFavAddressId ASC";
        $db_passenger_fav_address = $obj->MySQLSelect($sql);
        $row[0]['UserFavouriteAddress'] = $db_passenger_fav_address;
        $usercountrydetailbytimezone = FetchMemberCountryData($passengerID, "Passenger", $vTimeZone, $vUserDeviceCountry);
        $row[0]['vDefaultCountry'] = $usercountrydetailbytimezone['vDefaultCountry'];
        $row[0]['vDefaultCountryCode'] = $usercountrydetailbytimezone['vDefaultCountryCode'];
        $row[0]['vRCountryImage'] = $usercountrydetailbytimezone['vRImageMember']; //added by SP for country image related changes on 05-08-2019
        $row[0]['vSCountryImage'] = $usercountrydetailbytimezone['vSImageMember']; //added by SP for country image related changes on 05-08-2019
        $row[0]['vDefaultCountryImage'] = empty($row[0]['vSCountryImage']) ? $usercountrydetailbytimezone['vDefaultCountryImage'] : $row[0]['vSCountryImage']; //added by SP for country image related changes on 06-08-2019
        $row[0]['vDefaultPhoneCode'] = $usercountrydetailbytimezone['vDefaultPhoneCode'];
        $row[0]['vPhoneCode'] = empty($row[0]['vPhoneCode']) ? $row[0]['vDefaultPhoneCode'] : $row[0]['vPhoneCode'];
        $row[0]['vCountry'] = empty($row[0]['vCountry']) ? $row[0]['vDefaultCountryCode'] : $row[0]['vCountry'];
        $SITE_POLICE_CONTROL_NUMBER = getMemberCountryPoliceNumber($passengerID, "Passenger", $row[0]['vCountry']);
        $row[0]['SITE_POLICE_CONTROL_NUMBER'] = $SITE_POLICE_CONTROL_NUMBER;
        $UserSelectedAddressArr = FetchMemberSelectedAddress($passengerID, "Passenger");
        $row[0]['UserSelectedAddress'] = $UserSelectedAddressArr['UserSelectedAddress'];
        $row[0]['UserSelectedLatitude'] = $UserSelectedAddressArr['UserSelectedLatitude'];
        $row[0]['UserSelectedLongitude'] = $UserSelectedAddressArr['UserSelectedLongitude'];
        $row[0]['UserSelectedAddressId'] = $UserSelectedAddressArr['UserSelectedAddressId'];
        // $row[0]['RegistrationDate'] = date("Y-m-d", strtotime($usercountrydetailbytimezone['tRegistrationDate'] . ' -1 day '));
        $row[0]['RegistrationDate'] = date("Y-m-d", strtotime($usercountrydetailbytimezone['tRegistrationDate']));
        $fOutStandingAmount = GetPassengerOutstandingAmount($passengerID);
        $row[0]['DISABLE_CASH_PAYMENT_OPTION'] = "No";
        $row[0]['fOutStandingAmount'] = 0;
        if ($fOutStandingAmount > 0) {
            $row[0]['DISABLE_CASH_PAYMENT_OPTION'] = "Yes";
            $getPriceUserCurrencyArr = getPriceUserCurrency($passengerID, "Passenger", $fOutStandingAmount);
            $row[0]['fOutStandingAmount'] = $getPriceUserCurrencyArr['fPricewithsymbol'];
        }
        $row[0]['ServiceCategories'] = json_decode(serviceCategories, true);
        for ($i = 0; $i < count($row[0]['ServiceCategories']); $i++) {
            $item_tmp = $row[0]['ServiceCategories'][$i];
            if (is_null($item_tmp['tDescription']) || empty($item_tmp['tDescription'])) {
                $row[0]['ServiceCategories'][$i]['tDescription'] = "";
            }
        }
        /* As a part of Socket Cluster */
        $row[0]['MAX_ALLOW_TIME_INTERVAL_MILLI'] = (fetchtripstatustimeMAXinterval() + $intervalmins) * 1000;
        $row[0]['RIDER_REQUEST_ACCEPT_TIME'] = $RIDER_REQUEST_ACCEPT_TIME;
        /* As a part of Socket Cluster */
        $vCurrencyPassenger = $row[0]['vCurrencyPassenger'];
        if ($vCurrencyPassenger == "" || $vCurrencyPassenger == NULL) {
            //Added By HJ On 08-06-2020 For Optimization currency Table Query Start
            if (!empty($vSystemDefaultCurrencyName)) {
                $vCurrencyPassenger = $vSystemDefaultCurrencyName;
            }
            else {
                $vCurrencyPassenger = get_value('currency', 'vName', 'eDefault', 'Yes', '', 'true');
            }
            //Added By HJ On 08-06-2020 For Optimization currency Table Query End
        }
        if (isset($currencyNameArr[$vCurrencyPassenger]['vSymbol']) && trim($currencyNameArr[$vCurrencyPassenger]['vSymbol']) != "") {
            $CurrencySymbol = $currencyNameArr[$vCurrencyPassenger]['vSymbol'];
        }
        else {
            $CurrencySymbol = get_value('currency', 'vSymbol', 'vName', $vCurrencyPassenger, '', 'true');
        }
        if (isset($currencyNameArr[$vCurrencyPassenger]['Ratio']) && trim($currencyNameArr[$vCurrencyPassenger]['Ratio']) != "") {
            $Ratio = $currencyNameArr[$vCurrencyPassenger]['Ratio'];
        }
        else {
            $Ratio = get_value('currency', 'Ratio', 'vName', $vCurrencyPassenger, '', 'true');
        }
        $currencydata = get_value('currency', 'eReverseformattingEnable,eReverseSymbolEnable', 'vName', $vCurrencyPassenger, '', '');
        $row[0]['eReverseformattingEnable'] = $currencydata[0]['eReverseformattingEnable'];
        $row[0]['eReverseSymbolEnable'] = $currencydata[0]['eReverseSymbolEnable'];
        $fTripsOutStandingAmount = GetPassengerOutstandingAmount($passengerID);
        $fTripsOutStandingAmount = round(($fTripsOutStandingAmount * $Ratio), 2);
        $row[0]['fOutStandingAmount'] = $fTripsOutStandingAmount;
        $row[0]['fOutStandingAmountWithSymbol'] = formateNumAsPerCurrency($fTripsOutStandingAmount, $vCurrencyPassenger);

        if ($fTripsOutStandingAmount > 0) {
            $userOutStandingData = $obj->MySQLSelect("SELECT fPendingAmount, iTripId, iOrderId FROM trip_outstanding_amount WHERE iUserId = '$passengerID' AND ePaidByPassenger = 'No' AND ePaymentBy = 'Passenger' ORDER BY iTripId DESC LIMIT 1");
            if (!empty($userOutStandingData) && count($userOutStandingData) > 0) {
                $outstandingTripData = $obj->MySQLSelect("SELECT vRideNo, eType FROM trips WHERE iTripId = '" . $userOutStandingData[0]['iTripId'] . "'");
                if (empty($outstandingTripData)) {
                    $outstandingTripData = $obj->MySQLSelect("SELECT vOrderNo as vRideNo FROM orders WHERE iOrderId = '" . $userOutStandingData[0]['iOrderId'] . "'");
                    $outstandingTripData[0]['eType'] = "DeliverAll";
                }
                $row[0]['PaymentPendingMsg'] = str_replace("#RIDE_NO#", $outstandingTripData[0]['vRideNo'], $langLabels['LBL_OUTSTANDING_PAYMENT_PENDING_MSG']);
                $row[0]['ShowContactUsBtn'] = "Yes";
                $tDescription = "Amount charge for trip oustanding balance";
                $extraParams = "eType=" . $outstandingTripData[0]['eType'] . "&ePaymentType=ChargeOutstandingAmount&tSessionId=" . $row[0]['tSessionId'] . "&GeneralMemberId=" . $passengerID . "&GeneralUserType=Passenger&iServiceId=&AMOUNT=" . $fTripsOutStandingAmount . "&PAGE_TYPE=CHARGE_OUTSTANDING_AMT&SYSTEM_TYPE=APP&IS_RETURN_RESULT=Yes&description=" . urlencode($tDescription);
                $row[0]['OUTSTANDING_PAYMENT_URL'] = $tconfig['tsite_url'] . 'assets/libraries/webview/payment_mode_select.php?' . $extraParams;
            }
        }

        $row[0]['MONGO_DB'] = $tconfig['tmongodb_databse'];
        $row[0]['MONGO_DB_CONNECTION_PORT'] = $tconfig['tmongodb_port'];
        $row[0]['SERVER_DEFAULT_TIMEZONE'] = date_default_timezone_get();
        if ($row[0]['APP_TYPE'] == "Ride-Delivery-UberX") {
            $RideDeliveryIconArr = getGeneralVarAll_IconBanner();
            for ($i = 0; $i < count($RideDeliveryIconArr); $i++) {
                $eImageType = $RideDeliveryIconArr[$i]['eImageType'];
                $vName = $RideDeliveryIconArr[$i]['vName'];
                $vValue = $RideDeliveryIconArr[$i]['vValue'];
                $$vName = $vValue;
                if ($eImageType == "No") {
                    $row[0][$vName] = $$vName;
                }
                else {
                    $row[0][$vName] = ($$vName != "") ? $tconfig['tsite_upload_images_vehicle_category'] . "/" . $$vName : "";
                }
            }
            if (ENABLE_RENTAL_OPTION == 'No') {
                $row[0]['RENTAL_SHOW_SELECTION'] = "None";
                $row[0]['RENTAL_GRID_ICON_NAME'] = "";
                $row[0]['RENTAL_BANNER_IMG_NAME'] = "";
                $row[0]['MOTO_RENTAL_SHOW_SELECTION'] = "None";
                $row[0]['MOTO_RENTAL_GRID_ICON_NAME'] = "";
                $row[0]['MOTO_RENTAL_BANNER_IMG_NAME'] = "";
            }
            $row[0]['ENABLE_MULTI_DELIVERY'] = ENABLE_MULTI_DELIVERY;
            /* $row[0]['RIDE_GRID_ICON_NAME']= ($row[0]['RIDE_GRID_ICON_NAME'] != "")?$tconfig['tsite_upload_images_vehicle_category']."/".$row[0]['RIDE_GRID_ICON_NAME']:""; */
        }
        $row[0]['tsite_upload_docs_file_extensions'] = $tconfig['tsite_upload_docs_file_extensions'];
        $row[0]['tsite_upload_image_file_extensions'] = $tconfig['tsite_upload_image_file_extensions'];
        $row[0]['tsite_upload_video_file_extensions'] = $tconfig['tsite_upload_video_file_extensions'];
        $row[0]['SC_CONNECT_URL'] = getSocketURL();
        $row[0]['APP_SERVICE_URL'] = APP_SERVICE_URL;
        $row[0]['GOOGLE_API_REPLACEMENT_URL'] = GOOGLE_API_REPLACEMENT_URL;
        $row[0]['TSITE_DB'] = TSITE_DB;
        $row[0]['DELIVERY_SHOW_SELECTION'] = "None";
        $row[0]['MOTO_DELIVERY_SHOW_SELECTION'] = "None";
        $row[0]['ENABLE_ADD_PROVIDER_FROM_STORE'] = $MODULES_OBJ->isStorePersonalDriverAvailable() ? 'Yes' : 'No';
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
            $companyData = getStoreDataForSystemStoreSelection(0);
            $row[0]['STORE_ID'] = $companyData[0]['iCompanyId'];
        }
        $row[0]['ServiceCategories'] = $storeCatArr;
        for ($i = 0; $i < count($row[0]['ServiceCategories']); $i++) {
            $item_tmp = $row[0]['ServiceCategories'][$i];
            if (is_null($item_tmp['tDescription']) || empty($item_tmp['tDescription'])) {
                $row[0]['ServiceCategories'][$i]['tDescription'] = "";
            }
            if ($row[0]['ServiceCategories'][$i]['iServiceId'] > 0) {
                $sc_eShowTerms = "No";
                $sc_eProofUpload = "No";
                $sc_tProofNote = "";
                if (isset($deliverAll_serviceArr[$row[0]['ServiceCategories'][$i]['iServiceId']])) {
                    if ($MODULES_OBJ->isEnableTermsServiceCategories()) {
                        $sc_eShowTerms = $deliverAll_serviceArr[$row[0]['ServiceCategories'][$i]['iServiceId']]['eShowTerms'];
                    }
                    if ($MODULES_OBJ->isEnableProofUploadServiceCategories()) {
                        $sc_eProofUpload = $deliverAll_serviceArr[$row[0]['ServiceCategories'][$i]['iServiceId']]['eProofUpload'];
                        $sc_tProofNote = $deliverAll_serviceArr[$row[0]['ServiceCategories'][$i]['iServiceId']]['tProofNote'];
                    }
                }
                else {
                    $sc_data = $obj->MySQLSelect("SELECT eShowTerms,eProofUpload,JSON_UNQUOTE(JSON_VALUE(tProofNote, '$.tProofNote_" . $vLanguage . "')) as tProofNote FROM service_categories WHERE iServiceId = " . $row[0]['ServiceCategories'][$i]['iServiceId']);
                    if ($MODULES_OBJ->isEnableTermsServiceCategories()) {
                        $sc_eShowTerms = $sc_data[0]['eShowTerms'];
                    }
                    if ($MODULES_OBJ->isEnableProofUploadServiceCategories()) {
                        $sc_eProofUpload = $sc_data[0]['eProofUpload'];
                        if (is_null($sc_data[0]['tProofNote']) || $sc_data[0]['tProofNote'] == "null") {
                            $sc_data[0]['tProofNote'] = "";
                        }
                        $sc_tProofNote = $sc_data[0]['tProofNote'];
                    }
                }
                $row[0]['ServiceCategories'][$i]['eShowTerms'] = $sc_eShowTerms;
                $row[0]['ServiceCategories'][$i]['eProofUpload'] = $sc_eProofUpload;
                $row[0]['ServiceCategories'][$i]['tProofNote'] = (!empty($sc_tProofNote) && $sc_tProofNote != NULL) ? $sc_tProofNote : "";
            }
        }
        //Added By HJ On 16-07-2019 For Check Multiple Country Exists Or Not Start
        // Added By HJ On 04-06-2020 For Optimized country Table Query Start
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
        // Added By HJ On 04-06-2020 For Optimized country Table Query End
        $multiCountry = "No";
        if (count($getCountryData) > 1) {
            $multiCountry = "Yes";
        }
        $row[0]['showCountryList'] = $multiCountry;
        //Added By HJ On 16-07-2019 For Check Multiple Country Exists Or Not End
        //Added By Hasmukh On 25-12-2018 For Get Advertise Banner Data Start
        if ($MODULES_OBJ->checkSharkPackage() && $row[0]['eStatus'] == "Active") {
            $row[0]['advertise_banner_data'] = getAdvertisementBannersAsPerDevice($passengerID, "Passenger");
        }
        //Added By Hasmukh On 25-12-2018 For Get Advertise Banner Data End
        //Added By HJ On 20-07-2020 For Optimization configurations_payment Table Query Start
        if (isset($generalConfigPaymentArr['ENABLE_GOPAY']) && trim($generalConfigPaymentArr['ENABLE_GOPAY']) != "") {
            $EnableGopay = trim($generalConfigPaymentArr['ENABLE_GOPAY']);
        }
        else {
            $EnableGopay = get_value('configurations_payment', 'vValue', 'vName', 'ENABLE_GOPAY'); //added by SP for Gojek-gopay
        }
        //Added By HJ On 20-07-2020 For Optimization configurations_payment Table Query End
        if (!empty($EnableGopay[0]['vValue'])) {
            $row[0]['ENABLE_GOPAY'] = $EnableGopay[0]['vValue'];
        }
        else if (!empty($EnableGopay)) {
            $row[0]['ENABLE_GOPAY'] = $EnableGopay;
        }
        else {
            $row[0]['ENABLE_GOPAY'] = '';
        }
        $row[0]['RANDOM_COLORS_KEY_VAL_ARR'] = RANDOM_COLORS_KEY_VAL_ARR;
        $row[0]['PAYMENT_BASE_URL'] = $tconfig['tsite_url'] . 'assets/libraries/webview/system_payment.php?';
        $row[0]['UFX_SERVICE_AVAILABLE'] = $MODULES_OBJ->isUfxFeatureAvailable();
        $row[0]['AUTH_EMAIL_SYSTEM'] = AUTH_EMAIL_SYSTEM;
        $row[0]['ENABLE_CATEGORY_WISE_STORES'] = ($MODULES_OBJ->isStoreClassificationEnable()) ? "Yes" : "No";
        $row[0]['CHECK_SYSTEM_STORE_SELECTION'] = ($systemStoreEnable > 0) ? "Yes" : "No";
        $row[0]['ENABLE_TAKE_AWAY'] = ($MODULES_OBJ->isTakeAwayEnable()) ? "Yes" : "No";
        $row[0]['ENABLE_ITEM_SEARCH_STORE_ORDER'] = ($MODULES_OBJ->isEnableItemSearchStoreOrder()) ? "Yes" : "No";
        /* added by SP on 10-08-2020 for page active or not */
        $getPageData = $obj->MySQLSelect("SELECT iPageId,eStatus FROM pages WHERE iPageId IN(4,33,52)");
        foreach ($getPageData as $kPage => $vPage) {
            if ($vPage['iPageId'] == 4) $pagename = "showTermsCondition";
            if ($vPage['iPageId'] == 33) $pagename = "showPrivacyPolicy";
            if ($vPage['iPageId'] == 52) $pagename = "showAboutUs";
            $row[0][$pagename] = $vPage['eStatus'] == 'Active' ? 'Yes' : 'No';
        }
        /*added by SP on 18-09-2020 */
        $row[0]['IS_RIDE_MODULE_AVAIL'] = ($MODULES_OBJ->isRideFeatureAvailable() == 1) ? "Yes" : "No";
        $row[0]['IS_DELIVERY_MODULE_AVAIL'] = ($MODULES_OBJ->isDeliveryFeatureAvailable() == 1) ? "Yes" : "No";
        $row[0]['IS_UFX_MODULE_AVAIL'] = ($MODULES_OBJ->isUberXFeatureAvailable() == 1) ? "Yes" : "No";
        $row[0]['IS_DELIVERALL_MODULE_AVAIL'] = ($MODULES_OBJ->isDeliverAllFeatureAvailable() == 1) ? "Yes" : "No";
        $row[0]['PICK_DROP_GENIE'] = PICK_DROP_GENIE; // Added by HV on 12-10-2020 for Genie Pickup/Dropoff Items
        //added by SP on 14-10-2020 for timeslot changes
        $row[0]['ENABLE_TIMESLOT_ADDON'] = !empty($MODULES_OBJ->isEnableTimeslotFeature()) ? "Yes" : "No";
        $row[0]['ENABLE_DELIVERY_INSTRUCTIONS_ORDERS'] = !empty($MODULES_OBJ->isEnableVoiceDeliveryInstructionsOrder()) ? "Yes" : "No";
        /* Default card according to Payment Gateway*/
        $countryPaymentMethod = $obj->MySQLSelect("SELECT vPaymentGateway FROM country WHERE vCountryCode = '" . $row[0]['vCountry'] . "'");
        $USER_APP_PAYMENT_METHOD = $APP_PAYMENT_METHOD;
        if (!empty($countryPaymentMethod[0]['vPaymentGateway'])) {
            $USER_APP_PAYMENT_METHOD = $countryPaymentMethod[0]['vPaymentGateway'];
        }
        $userPaymentInfo = getPaymentDefaultCard($passengerID, 'Passenger');
        $row[0]['vCreditCard'] = (count($userPaymentInfo) > 0) ? $userPaymentInfo[0]['tCardNum'] : "";

        $row[0]['PAYMENT_BASE_URL'] = $tconfig['tsite_url'] . 'assets/libraries/webview/system_payment.php?';
        $row[0]['PAYMENT_MODE_URL'] = $tconfig['tsite_url'] . 'assets/libraries/webview/payment_mode_select.php?';

        // Added by HV for Restrict Passenger Limit Feature
        $row[0]['RESTRICT_PASSENGER_LIMIT_INFO_URL'] = $tconfig['tsite_url'] . 'safety_checklist.php?iPageId=55&vLang=' . $vLanguage;
        $row[0]['isShowSearchedItemEnabled'] = ($MODULES_OBJ->isEnableItemSearchStoreOrder()) ? "Yes" : "No";
        $row[0]['ENABLE_DELIVERY_INSTRUCTIONS_ORDERS'] = ($MODULES_OBJ->isEnableVoiceDeliveryInstructionsOrder()) ? "Yes" : "No";
        //UpdateAppTerminateStatus($passengerID, "Passenger");
        $row[0]['APP_LAUNCH_IMAGES'] = "";
        if (!empty(getAppLaunchImages($vLanguage, 'Passenger'))) {
            $row[0]['APP_LAUNCH_IMAGES'] = getAppLaunchImages($vLanguage, 'Passenger');
        }
        $row[0]['ENABLE_LOCATION_WISE_BANNER'] = ($MODULES_OBJ->isEnableLocationwiseBanner()) ? "Yes" : "No";
        $row[0]['ENABLE_APPLE_LOGIN_FOR_USER'] = ($MODULES_OBJ->isEnableAppleLoginForUser()) ? "Yes" : "No";
        $row[0]['isSmartLoginEnable'] = $MODULES_OBJ->isEnableSmartLogin() ? "Yes" : "No";
        $row[0]['WEBRTC_SOCKET_URL'] = WEBRTC_SOCKET_URL;
        $row[0]['WEBRTC_STUN_URL'] = WEBRTC_STUN_URL;
        $row[0]['WEBRTC_TURN_URL'] = WEBRTC_TURN_URL;
        $row[0]['WEBRTC_ICE_SERVER_LIST'] = WEBRTC_ICE_SERVER_LIST;
        $row[0]['WEBRTC_USERNAME'] = $tconfig["tsite_webrtc_username"];
        $row[0]['WEBRTC_PASS'] = $tconfig["tsite_webrtc_pass"];

        $row[0]['RIDE_ENABLED'] = RIDE_ENABLED;
        $row[0]['DELIVERY_ENABLED'] = DELIVERY_ENABLED;
        $row[0]['UFX_ENABLED'] = UFX_ENABLED;
        $row[0]['DELIVERALL_ENABLED'] = DELIVERALL_ENABLED;
        $row[0]['GENIE_ENABLED'] = GENIE_ENABLED;
        $row[0]['RUNNER_ENABLED'] = RUNNER_ENABLED;
        $row[0]['BIDDING_ENABLED'] = BIDDING_ENABLED;
        $row[0]['VC_ENABLED'] = VC_ENABLED;
        $row[0]['MED_UFX_ENABLED'] = MED_UFX_ENABLED;
        $row[0]['RENT_ITEM_ENABLED'] = RENT_ITEM_ENABLED;
        $row[0]['RENT_ESTATE_ENABLED'] = RENT_ESTATE_ENABLED;
        $row[0]['RENT_CARS_ENABLED'] = RENT_CARS_ENABLED;
        $row[0]['NEARBY_ENABLED'] = NEARBY_ENABLED;
        $row[0]['TRACK_SERVICE_ENABLED'] = TRACK_SERVICE_ENABLED;
        $row[0]['RIDE_SHARE_ENABLED'] = RIDE_SHARE_ENABLED;

        $row[0]['ENABLE_LIVE_CHAT_TRACK_ORDER'] = (isset($row[0]['ENABLE_LIVE_CHAT'])) ? $row[0]['ENABLE_LIVE_CHAT'] : ENABLE_LIVE_CHAT_TRACK_ORDER;
        $row[0]['SERVICE_CATEGORIES_ARR'] = SERVICE_CATEGORIES_ARR;
        if ($MODULES_OBJ->isEnableGiftCardFeature()) {
            $row[0]['GIFT_CARD_IMAGES'] = $GIFT_CARD_OBJ->getGiftCardImages();
            $row[0]['GIFT_CARD_MAX_AMOUNT_WITH_SYMBOL'] = formateNumAsPerCurrency($row[0]['GIFT_CARD_MAX_AMOUNT'], $vCurrencyPassenger);
            $row[0]['PREVIEW_GIFT_CARD_URL'] = $tconfig['tsite_url'] . 'preview_gift_card.php?';
            $row[0]['TERMS_&_CONDITIONS_GIFT_CARD_URL'] = $tconfig['tsite_url'] . 'terms_conditions_gift_card.php';
        }
        if ($CONFIG_OBJ->isOnlyCashPaymentModeAvailable() || $row[0]['WALLET_ENABLE'] == 'No' ) {
            $row[0]['ENABLE_GIFT_CARD_FEATURE'] = 'No';
        }
 
      /*   $row[0]['LOCATION_BATCH_TASK_DURATION'] = fetchTaskInterval('LOCATION_BATCH_TASK_DURATION');
        $row[0]['PROVIDER_STATUS_TASK_DURATION'] = fetchTaskInterval('PROVIDER_STATUS_TASK_DURATION'); */
        $row[0]['CARD_SAVE_ENABLE'] = isEnableAddCard()['CARD_SAVE_ENABLE'];


        unset($row[0]['tLocationUpdateDate']);
        unset($row[0]['tSeenAdvertiseTime']);
        unset($row[0]['CRON_TIME']);
        unset($row[0]['tLastOnline']);
        unset($row[0]['vLatitude']);
        unset($row[0]['vLongitude']);
        unset($row[0]['MAIL_FOOTER']);

        /* fetch value */
        return $row[0];
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
        setDataResponse($returnArr);
    }
}

 ?>