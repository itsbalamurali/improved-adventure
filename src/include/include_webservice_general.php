<?php
############################ country_list #############################
if ($type == "countryList") {
    $GeneralAppVersion = isset($_REQUEST['GeneralAppVersion']) ? $_REQUEST['GeneralAppVersion'] : "";
    $GeneralUserType = isset($_REQUEST['GeneralUserType']) ? $_REQUEST['GeneralUserType'] : "";
    $HUAWEI_DEVICE = isset($_REQUEST['HUAWEI_DEVICE']) ? $_REQUEST['HUAWEI_DEVICE'] : "";
    $ssql = "";
    $tableName = "country";
    $returnArr = array();
    $counter = 0;

    if(strtoupper($APPGALLERY_MODE_ANDROID) == "REVIEW" && strtoupper($HUAWEI_DEVICE) == "YES"){
        $ssql .= " AND vCountryCode IN ('IN','CA','US','AU','CN')";
    }
    for ($i = 0; $i < 26; $i++) {
        $cahracter = chr(65 + $i);
        $sql = "SELECT COU.* FROM $tableName as COU WHERE COU.eStatus = 'Active' AND COU.vPhoneCode!='' AND COU.vCountryCode!='' AND COU.vCountry LIKE '$cahracter%' $ssql ORDER BY COU.vCountry";
        $db_rec = $obj->MySQLSelect($sql);
        if (count($db_rec) > 0) {
            $countryListArr = array();
            $subCounter = 0;
            for ($j = 0; $j < count($db_rec); $j++) {
                $countryListArr[$subCounter] = $db_rec[$j];

                $temp_image = checkimgexist("webimages/icons/country_flags/" . $db_rec[$j]['vRImage'], '1');
                $countryListArr[$subCounter]['vRImage'] = $temp_image;
                
                $temp_image = checkimgexist("webimages/icons/country_flags/" . $db_rec[$j]['vSImage'], '2');
                $countryListArr[$subCounter]['vSImage'] = $temp_image;


                $countryListArr[$subCounter]['vPhoneCodeWithPlusSign'] = '+'.$db_rec[$j]['vPhoneCode'];


                
                $subCounter++;
            }
            if (count($countryListArr) > 0) {
                $returnArr[$counter]['key'] = $cahracter;
                $returnArr[$counter]['TotalCount'] = count($countryListArr);
                $returnArr[$counter]['List'] = $countryListArr;
                $counter++;
            }
        }
    }
    $countryArr['Action'] = "1";
    $countryArr['totalValues'] = count($returnArr);
    $countryArr['CountryList'] = $returnArr;
    setDataResponse($countryArr);
}

// ######################## isUserExist #############################
if ($type == "isUserExist") {
    $Emid = isset($_REQUEST["Email"]) ? $_REQUEST["Email"] : '';
    $Phone = isset($_REQUEST["Phone"]) ? $_REQUEST["Phone"] : '';
    $fbid = isset($_REQUEST["fbid"]) ? $_REQUEST["fbid"] : '';
    $Data = $obj->MySQLSelect("SELECT vEmail,vPhone,vFbId FROM register_user WHERE 1=1 AND IF('$Emid'!='',vEmail = '$Emid',0) OR IF('$Phone'!='',vPhone = '$Phone',0) OR IF('$fbid'!='',vFbId = '$fbid',0)");
    if (count($Data) > 0) {
        $returnArr['Action'] = "0";
        if ($Emid == $Data[0]['vEmail']) {
            $returnArr['message'] = "LBL_ALREADY_REGISTERED_TXT";
        }
        else if ($Phone == $Data[0]['vPhone']) {
            $returnArr['message'] = "LBL_MOBILE_EXIST";
        }
        else {
            $returnArr['message'] = "LBL_FACEBOOK_ACC_EXIST";
        }
    }
    else {
        $returnArr['Action'] = "1";
    }
    setDataResponse($returnArr);
}

if ($type == "LoginWithFB") {
    $fbid = isset($_REQUEST["iFBId"]) ? $_REQUEST["iFBId"] : '';
    $Fname = isset($_REQUEST["vFirstName"]) ? $_REQUEST["vFirstName"] : '';
    $Lname = isset($_REQUEST["vLastName"]) ? $_REQUEST["vLastName"] : '';
    $email = isset($_REQUEST["vEmail"]) ? $_REQUEST["vEmail"] : '';
    $GCMID = isset($_REQUEST["vDeviceToken"]) ? $_REQUEST["vDeviceToken"] : '';
    $vDeviceType = isset($_REQUEST["vDeviceType"]) ? $_REQUEST["vDeviceType"] : 'Android';
    $eLoginType = isset($_REQUEST["eLoginType"]) ? $_REQUEST["eLoginType"] : 'Facebook';
    $user_type = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : 'Passenger';
    $vCurrency = isset($_REQUEST["vCurrency"]) ? $_REQUEST["vCurrency"] : '';
    $vLang = isset($_REQUEST["vLang"]) ? $_REQUEST["vLang"] : '';
    $vFirebaseDeviceToken = isset($_REQUEST["vFirebaseDeviceToken"]) ? $_REQUEST["vFirebaseDeviceToken"] : '';
    $vImageURL = isset($_REQUEST["vImageURL"]) ? $_REQUEST["vImageURL"] : '';
    if ($fbid == "") {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
        setDataResponse($returnArr);
    }
    
    $DeviceType = $vDeviceType;
    if ($user_type == "Passenger") {
        $tblname = "register_user";
        $iMemberId = 'iUserId';
        $vCurrencyMember = "vCurrencyPassenger";
        $vImageFiled = 'vImgName';
    } else {
        $tblname = "register_driver";
        $iMemberId = 'iDriverId';
        $vCurrencyMember = "vCurrencyDriver";
        $vImageFiled = 'vImage';
    }
    if ($user_type == "Passenger") {
        $sql = "SELECT iUserId as iUserId,eStatus,vFbId,vLang,vTripStatus,eSignUpType,vImgName as vImage FROM $tblname WHERE 1=1 AND IF('$email'!='',vEmail = '$email',0) OR IF('$fbid'!='',vFbId = '$fbid',0)";
    } else {
        $sql = "SELECT iDriverId as iUserId,eStatus,vFbId,vLang,vTripStatus,eSignUpType,vImage as vImage FROM $tblname WHERE 1=1 AND IF('$email'!='',vEmail = '$email',0) OR IF('$fbid'!='',vFbId = '$fbid',0)";
    }
    $Data = $obj->MySQLSelect($sql);
    if (isset($Data[0]['iUserId']) && $Data[0]['iUserId'] > 0 && $user_type == "Passenger") {
        $sql_cabrequest = "SELECT iCabRequestId,eStatus FROM `cab_request_now` WHERE iUserId='" . $Data[0]['iUserId'] . "' ORDER BY iCabRequestId DESC LIMIT 0,1";
        $Data_cabrequest = $obj->MySQLSelect($sql_cabrequest);
        $eStatus_cab = "Active";
        $iCabRequestId = 0;
        if (count($Data_cabrequest) > 0) {
            $iCabRequestId = $Data_cabrequest[0]['iCabRequestId'];
            $eStatus_cab = $Data_cabrequest[0]['eStatus'];
        }
    }
    //Added By HJ On 31-12-2018 For Get LinkedIn Picture Data Start
    $socialData = array();
    if (isset($_REQUEST["socialData"])) {
        $socialData = json_decode($_REQUEST["socialData"], true);
    }
    if (isset($socialData['pictureUrls']) && $eLoginType == 'LinkedIn') {
        $pictureUrls = $socialData['pictureUrls']['_total'];
        if ($pictureUrls > 0) {
            $vImageURL = $socialData['pictureUrls']['values'][0];
        } else {
            $vImageURL = $socialData['pictureUrl'];
        }
    }
    //Added By HJ On 31-12-2018 For Get LinkedIn Picture Data End
    if (count($Data) > 0) {
        if ($Data[0]['eStatus'] == "Active" || ($user_type == "Driver" && $Data[0]['eStatus'] != "Deleted")) {
            $iUserId_passenger = $Data[0]['iUserId'];

            $where = " $iMemberId = '$iUserId_passenger' ";
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
                $Data_update_passenger[$vCurrencyMember] = $vCurrency;
            }
            ## Upload Image of Member if SignUp from Google, Facebook Or Twitter ##
            //if ($fbid != 0 || $fbid != "") { // Commented By HJ On 07-03-2019 For Prevent Image Name Update Action If Already Exists Image
            if (isset($Data[0]['vImage']) && $Data[0]['vImage'] == "" && ($fbid != 0 || $fbid != "")) { // Added By HJ On 07-03-2019 For Prevent Image Name Update Action If Already Exists Image
                $userid = $Data[0]['iUserId'];
                $eSignUpType = $eLoginType;
                $UserImage = UploadUserImage($userid, $user_type, $eSignUpType, $fbid, $vImageURL);
                if ($UserImage != "") {
                    $where = " $iMemberId = '$userid' ";
                    $Data_update_image_member[$vImageFiled] = $UserImage;
                    $imageuploadid = $obj->MySQLQueryPerform($tblname, $Data_update_image_member, 'update', $where);
                }
            }
            ## Upload Image of Member if SignUp from Google, Facebook Or Twitter ##
            if ($GCMID != '') {
                $Data_update_passenger['iGcmRegId'] = $GCMID;
                $Data_update_passenger['eDeviceType'] = $DeviceType;
                $Data_update_passenger['vFbId'] = $fbid;
                $Data_update_passenger['eSignUpType'] = $eLoginType;
                $Data_update_passenger['tSessionId'] = session_id() . time();
                $Data_update_passenger['vFirebaseDeviceToken'] = $vFirebaseDeviceToken;
                $id = $obj->MySQLQueryPerform($tblname, $Data_update_passenger, 'update', $where);
            }
            if ($user_type == "Passenger") {
                if ($eStatus_cab == "Requesting") {
                    $where1 = " iCabRequestId = '$iCabRequestId' ";
                    $Data_update_cab_now['eStatus'] = "Cancelled";
                    $id = $obj->MySQLQueryPerform("cab_request_now", $Data_update_cab_now, 'update', $where1);
                }
            }
            $returnArr['changeLangCode'] = "Yes";
            $returnArr['UpdatedLanguageLabels'] = $LANG_OBJ->FetchLanguageLabels($Data[0]['vLang'], "1", $iServiceId);
            $returnArr['vLanguageCode'] = $Data[0]['vLang'];
            $sql_LangCode = "SELECT eDirectionCode,vGMapLangCode FROM language_master WHERE `vCode` = '" . $Data[0]['vLang'] . "' ";
            $Data_checkLangCode = $obj->MySQLSelect($sql_LangCode);
            $returnArr['langType'] = $Data_checkLangCode[0]['eDirectionCode'];
            $returnArr['vGMapLangCode'] = $Data_checkLangCode[0]['vGMapLangCode'];
            $sql = "SELECT vCode, vGMapLangCode, eDirectionCode as eType, vTitle,vCurrencyCode,vCurrencySymbol,eDefault  FROM  `language_master` WHERE  `eStatus` = 'Active' ";
            $defLangValues = $obj->MySQLSelect($sql);
            $returnArr['LIST_LANGUAGES'] = $defLangValues;
            for ($i = 0; $i < count($defLangValues); $i++) {
                if ($defLangValues[$i]['eDefault'] == "Yes") {
                    $returnArr['DefaultLanguageValues'] = $defLangValues[$i];
                }

                if($defLangValues[$i]['vTitle'] != $defLangValues[$i]['vTitle_EN']){ 
                    $returnArr['LIST_LANGUAGES'][$i]['vTitle'] = $defLangValues[$i]['vTitle_EN']." (".mb_convert_case($defLangValues[$i]['vTitle'], MB_CASE_TITLE, 'UTF-8').")";
                } else {
                    $returnArr['LIST_LANGUAGES'][$i]['vTitle'] = $defLangValues[$i]['vTitle_EN'];
                }

                $defLangValues[$i]['vService_BG_color'] = RANDOM_COLORS_ARR[array_rand(RANDOM_COLORS_ARR, 1)];
                $defLangValues[$i]['vService_TEXT_color'] = "#FFFFFF";
                $returnArr['LIST_LANGUAGES'][$i]['vService_BG_color'] = RANDOM_COLORS_ARR[array_rand(RANDOM_COLORS_ARR, 1)];
                $returnArr['LIST_LANGUAGES'][$i]['vService_TEXT_color'] = "#FFFFFF";
            }
            $defCurrencyValues = $obj->MySQLSelect("SELECT iCurrencyId,vName, vSymbol,iDispOrder, eDefault,Ratio,fThresholdAmount,eStatus  FROM  `currency` WHERE  `eStatus` = 'Active' ");
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
            if ($user_type == "Passenger") {
                $returnArr['message'] = getPassengerDetailInfo($Data[0]['iUserId'], '', "");
                $returnArr['message'] = getCustomeNotificationSound($returnArr['message']);
                createUserLog("Passenger", "No", $Data[0]['iUserId'], $DeviceType);
            } else {
                $returnArr['message'] = getDriverDetailInfo($Data[0]['iUserId'], '');
                $returnArr['message'] = getCustomeNotificationSound($returnArr['message']);
                createUserLog("Driver", "No", $Data[0]['iUserId'], $DeviceType);
            }
            setDataResponse($returnArr);
        } else {
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
    } else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "DO_REGISTER";
        setDataResponse($returnArr);
    }
}

if ($type == "LoginWithAppleID") {
    $fbid = isset($_REQUEST["iFBId"]) ? $_REQUEST["iFBId"] : '';
    $vAppleId = isset($_REQUEST["vAppleId"]) ? $_REQUEST["vAppleId"] : '';
    $Fname = isset($_REQUEST["vFirstName"]) ? $_REQUEST["vFirstName"] : '';
    $Lname = isset($_REQUEST["vLastName"]) ? $_REQUEST["vLastName"] : '';
    $email = isset($_REQUEST["vEmail"]) ? $_REQUEST["vEmail"] : '';
    $GCMID = isset($_REQUEST["vDeviceToken"]) ? $_REQUEST["vDeviceToken"] : '';
    $vDeviceType = isset($_REQUEST["vDeviceType"]) ? $_REQUEST["vDeviceType"] : 'Android';
    $eLoginType = isset($_REQUEST["eLoginType"]) ? $_REQUEST["eLoginType"] : 'Facebook';
    $user_type = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : 'Passenger';
    $vCurrency = isset($_REQUEST["vCurrency"]) ? $_REQUEST["vCurrency"] : '';
    $vLang = isset($_REQUEST["vLang"]) ? $_REQUEST["vLang"] : '';
    $vFirebaseDeviceToken = isset($_REQUEST["vFirebaseDeviceToken"]) ? $_REQUEST["vFirebaseDeviceToken"] : '';
    $vImageURL = isset($_REQUEST["vImageURL"]) ? $_REQUEST["vImageURL"] : '';
    if ($vAppleId == "") {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
        setDataResponse($returnArr);
    }
    $DeviceType = $vDeviceType;
    if ($user_type == "Passenger") {
        $tblname = "register_user";
        $iMemberId = 'iUserId';
        $vCurrencyMember = "vCurrencyPassenger";
        $vImageFiled = 'vImgName';
    } else {
        $tblname = "register_driver";
        $iMemberId = 'iDriverId';
        $vCurrencyMember = "vCurrencyDriver";
        $vImageFiled = 'vImage';
    }
    if ($user_type == "Passenger") {
        $sql = "SELECT iUserId as iUserId,eStatus,vFbId,vLang,vTripStatus,eSignUpType,vImgName as vImage,vAppleId FROM $tblname WHERE 1=1 AND IF('$email'!='',vEmail = '$email',0) OR IF('$vAppleId'!='',vAppleId = '$vAppleId',0)";
    } else {
        $sql = "SELECT iDriverId as iUserId,eStatus,vFbId,vLang,vTripStatus,eSignUpType,vImage as vImage,vAppleId FROM $tblname WHERE 1=1 AND IF('$email'!='',vEmail = '$email',0) OR IF('$vAppleId'!='',vAppleId = '$vAppleId',0)";
    }
    $Data = $obj->MySQLSelect($sql);
    if (isset($Data[0]['iUserId']) && $Data[0]['iUserId'] > 0 && $user_type == "Passenger") {

        $sql_cabrequest = "SELECT iCabRequestId,eStatus FROM `cab_request_now` WHERE iUserId='" . $Data[0]['iUserId'] . "' ORDER BY iCabRequestId DESC LIMIT 0,1";
        $Data_cabrequest = $obj->MySQLSelect($sql_cabrequest);
        $eStatus_cab = "Active";
        $iCabRequestId = 0;
        if (count($Data_cabrequest) > 0) {
            $iCabRequestId = $Data_cabrequest[0]['iCabRequestId'];
            $eStatus_cab = $Data_cabrequest[0]['eStatus'];
        }
    }
    //Added By HJ On 31-12-2018 For Get LinkedIn Picture Data Start
    $socialData = array();
    if (isset($_REQUEST["socialData"])) {
        $socialData = json_decode($_REQUEST["socialData"], true);
    }
    if (isset($socialData['pictureUrls']) && $eLoginType == 'LinkedIn') {
        $pictureUrls = $socialData['pictureUrls']['_total'];
        if ($pictureUrls > 0) {
            $vImageURL = $socialData['pictureUrls']['values'][0];
        } else {
            $vImageURL = $socialData['pictureUrl'];
        }
    }
    //Added By HJ On 31-12-2018 For Get LinkedIn Picture Data End
    if (count($Data) > 0) {
        if ($Data[0]['eStatus'] == "Active" || ($user_type == "Driver" && $Data[0]['eStatus'] != "Deleted")) {
            $iUserId_passenger = $Data[0]['iUserId'];
            //$where = " iUserId = '$iUserId_passenger' ";
            $where = " $iMemberId = '$iUserId_passenger' ";
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
                $Data_update_passenger[$vCurrencyMember] = $vCurrency;
            }
            ## Upload Image of Member if SignUp from Google, Facebook Or Twitter ##
            //if ($fbid != 0 || $fbid != "") { // Commented By HJ On 07-03-2019 For Prevent Image Name Update Action If Already Exists Image
            if (isset($Data[0]['vImage']) && $Data[0]['vImage'] == "" && ($fbid != 0 || $fbid != "")) { // Added By HJ On 07-03-2019 For Prevent Image Name Update Action If Already Exists Image
                $userid = $Data[0]['iUserId'];
                $eSignUpType = $eLoginType;
                $UserImage = UploadUserImage($userid, $user_type, $eSignUpType, $fbid, $vImageURL);
                if ($UserImage != "") {
                    $where = " $iMemberId = '$userid' ";
                    $Data_update_image_member[$vImageFiled] = $UserImage;
                    $imageuploadid = $obj->MySQLQueryPerform($tblname, $Data_update_image_member, 'update', $where);
                }
            }
            ## Upload Image of Member if SignUp from Google, Facebook Or Twitter ##
            if ($GCMID != '') {
                $Data_update_passenger['iGcmRegId'] = $GCMID;
                $Data_update_passenger['eDeviceType'] = $DeviceType;
                //$Data_update_passenger['vFbId'] = $fbid;
                $Data_update_passenger['vAppleId'] = $vAppleId;
                $Data_update_passenger['eSignUpType'] = $eLoginType;
                $Data_update_passenger['tSessionId'] = session_id() . time();
                $Data_update_passenger['vFirebaseDeviceToken'] = $vFirebaseDeviceToken;
                $id = $obj->MySQLQueryPerform($tblname, $Data_update_passenger, 'update', $where);
            }
            if ($user_type == "Passenger") {
                if ($eStatus_cab == "Requesting") {
                    $where1 = " iCabRequestId = '$iCabRequestId' ";
                    $Data_update_cab_now['eStatus'] = "Cancelled";
                    $id = $obj->MySQLQueryPerform("cab_request_now", $Data_update_cab_now, 'update', $where1);
                }
            }
            $returnArr['changeLangCode'] = "Yes";
            $returnArr['UpdatedLanguageLabels'] = $LANG_OBJ->FetchLanguageLabels($Data[0]['vLang'], "1", $iServiceId);
            $returnArr['vLanguageCode'] = $Data[0]['vLang'];
            $sql_LangCode = "SELECT eDirectionCode,vGMapLangCode FROM language_master WHERE `vCode` = '" . $Data[0]['vLang'] . "' ";
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

                if($defLangValues[$i]['vTitle'] != $defLangValues[$i]['vTitle_EN']){ 
                    $returnArr['LIST_LANGUAGES'][$i]['vTitle'] = $defLangValues[$i]['vTitle_EN']." (".mb_convert_case($defLangValues[$i]['vTitle'], MB_CASE_TITLE, 'UTF-8').")";
                } else {
                    $returnArr['LIST_LANGUAGES'][$i]['vTitle'] = $defLangValues[$i]['vTitle_EN'];
                }
                $defLangValues[$i]['vService_BG_color'] = RANDOM_COLORS_ARR[array_rand(RANDOM_COLORS_ARR, 1)];
                $defLangValues[$i]['vService_TEXT_color'] = "#FFFFFF";
                $returnArr['LIST_LANGUAGES'][$i]['vService_BG_color'] = RANDOM_COLORS_ARR[array_rand(RANDOM_COLORS_ARR, 1)];
                $returnArr['LIST_LANGUAGES'][$i]['vService_TEXT_color'] = "#FFFFFF";
            }
            $defCurrencyValues = $obj->MySQLSelect("SELECT iCurrencyId,vName, vSymbol,iDispOrder, eDefault,Ratio,fThresholdAmount,eStatus  FROM  `currency` WHERE  `eStatus` = 'Active' ");
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
            if ($user_type == "Passenger") {
                $returnArr['message'] = getPassengerDetailInfo($Data[0]['iUserId'], '', "");
                $returnArr['message'] = getCustomeNotificationSound($returnArr['message']);
                createUserLog("Passenger", "No", $Data[0]['iUserId'], $DeviceType);
            } else {
                $returnArr['message'] = getDriverDetailInfo($Data[0]['iUserId'], '');
                $returnArr['message'] = getCustomeNotificationSound($returnArr['message']);
                createUserLog("Driver", "No", $Data[0]['iUserId'], $DeviceType);
            }
            setDataResponse($returnArr);
        } else {
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
    } else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "DO_REGISTER";
        setDataResponse($returnArr);
    }
}

if ($type == "staticPage") {
    $iPageId = isset($_REQUEST['iPageId']) ? clean($_REQUEST['iPageId']) : '';
    $languageCode = getUserLanguageCode();
    $pageDesc = get_value('pages', 'tPageDesc_' . $languageCode, 'iPageId', $iPageId, '', 'true');
    $meta['page_desc'] = $pageDesc;
    setDataResponse($meta);
}

if ($type == "sendContactQuery") {
    $UserType = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : '';
    $UserId = isset($_REQUEST["UserId"]) ? $_REQUEST["UserId"] : '';
    $message = isset($_REQUEST["message"]) ? $_REQUEST["message"] : '';
    $subject = isset($_REQUEST["subject"]) ? $_REQUEST["subject"] : '';
    if ($UserType == 'Passenger') {
        $result_data = $obj->MySQLSelect("SELECT vName,vLastName,vPhone,vEmail FROM register_user WHERE iUserId=$UserId");
    } else if ($UserType == 'Driver') {
        $result_data = $obj->MySQLSelect("SELECT vName,vLastName,vPhone,vEmail FROM register_driver WHERE iDriverId=$UserId");
    }
    else if ($UserType == 'Company') {
        $sql = "SELECT vCompany,vPhone,vEmail FROM company WHERE iCompanyId=$UserId";
        $result_data = $obj->MySQLSelect($sql);
    }
    if ($UserId != "") {
        if ($UserType == 'Company') {
            $Data['vFirstName'] = $result_data[0]['vCompany'];
            $Data['vLastName'] = "";
        }
        else {
            $Data['vFirstName'] = $result_data[0]['vName'];
            $Data['vLastName'] = $result_data[0]['vLastName'];
        }
        $Data['vEmail'] = $result_data[0]['vEmail'];
        $Data['cellno'] = $result_data[0]['vPhone'];
        $Data['eSubject'] = removehtml($subject);
        $Data['tSubject'] = removehtml($message);
        if (strtoupper(SITE_TYPE) == "LIVE") {
            $id = $COMM_MEDIA_OBJ->SendMailToMember("CONTACTUS", $Data);
        }
        else {
            $id = 1;
        }
    }
    else {
        $Data['vFirstName'] = "App User";
        $Data['vLastName'] = "";
        $Data['vEmail'] = "-";
        $Data['cellno'] = "-";
        $Data['eSubject'] = removehtml($subject);
        $Data['tSubject'] = removehtml($message);
        if (strtoupper(SITE_TYPE) == "LIVE") {
            $id = $COMM_MEDIA_OBJ->SendMailToMember("CONTACTUSWITHOUTLOGIN", $Data);
        }
        else {
            $id = 1;
        }
        $UserType = "Guest";
    }
    if ($id > 0) {
        //added by SP on 22-01-2021 to save data of contactus
        $datainsert = array();
        $datainsert["iMemberId"] = $UserId;
        $datainsert["vFirstname"] = validName($Data['vFirstName']);
        $datainsert["vLastname"] = validName($Data['vLastName']);
        $datainsert["vEmail"] = $Data['vEmail'];
        $datainsert["vPhone"] = $Data['cellno'];
        $datainsert["vSubject"] = removehtml($Data['eSubject']);
        $datainsert["tDescription"] = removehtml($Data['tSubject']);
        $datainsert["eUserType"] = $UserType;
        $datainsert["tRequestDate"] = @date("Y-m-d H:i:s");
        if ($UserType == 'Company') {
            $datainsert["eSystem"] = "DeliverAll";
        }
        $id = $obj->MySQLQueryPerform("contactus", $datainsert, 'insert');
        $returnArr['Action'] = "1";
        $returnArr['message'] = "LBL_SENT_CONTACT_QUERY_SUCCESS_TXT";
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_FAILED_SEND_CONTACT_QUERY_TXT";
    }
    setDataResponse($returnArr);
}

// ############################ GetFAQ ######################################
if ($type == "getFAQ") {
    $status = "Active";
    $iMemberId = isset($_REQUEST['iMemberId']) ? clean($_REQUEST['iMemberId']) : '';
    $appType = isset($_REQUEST['appType']) ? clean($_REQUEST['appType']) : '';
    $GeneralUserType = isset($_REQUEST['GeneralUserType']) ? trim($_REQUEST['GeneralUserType']) : '';
    $vLang = isset($_REQUEST["vGeneralLang"]) ? $_REQUEST["vGeneralLang"] : '';
    $languageCode = "";
    $tableName = "register_user";
    $fieldName = "iUserId";
    if ($appType == "Driver") {
        $tableName = "register_driver";
        $fieldName = "iDriverId";
        //$languageCode = get_value('register_driver', 'vLang', 'iDriverId', $iMemberId, '', 'true');
    }
    //Added By HJ On 24-07-2020 For Optimize register_user/register_driver Table Query Start
    if (isset($userDetailsArr[$tableName . '_' . $iMemberId])) {
        $memberData = $userDetailsArr[$tableName . '_' . $iMemberId];
    }
    else {
        $memberData = $obj->MySQLSelect("SELECT *,$fieldName as iMemberId FROM " . $tableName . " WHERE $fieldName='" . $iMemberId . "'");
        $userDetailsArr[$tableName . '_' . $iMemberId] = $memberData;
    }
    $languageCode = $memberData[0]['vLang'];
    //Added By HJ On 24-07-2020 For Optimize register_user/register_driver Table Query End
    if ($languageCode == "") {
        //Added By HJ On 15-07-2020 For Optimize language_master Table Query Start
        $languageCode = $LANG_OBJ->FetchDefaultLangData("vCode");
        //Added By HJ On 15-07-2020 For Optimize language_master Table Query End
    }
    if ($vLang != "") {
        $languageCode = $vLang;
    }
    $Data = $obj->MySQLSelect("SELECT * FROM `faq_categories` WHERE eStatus='$status' AND vCode='" . $languageCode . "' AND ( eCategoryType = 'General' OR eCategoryType = '" . $GeneralUserType . "' ) ORDER BY iDisplayOrder ASC ");
    $i = $k = 0;
    $row_main = array();
    if (count($Data) > 0) {
        $row = $Data;
        //Added By HJ On 24-07-2020 For Optimize faqs Table Query Start
        $row_questions = $obj->MySQLSelect("SELECT iFaqcategoryId,vTitle_" . $languageCode . " as vTitle,tAnswer_" . $languageCode . " as tAnswer FROM `faqs` WHERE eStatus='" . $status . "' ORDER BY iDisplayOrder ASC");
        $faqDataArr = array();
        for ($h = 0; $h < count($row_questions); $h++) {
            $faqDataArr[$row_questions[$h]['iFaqcategoryId']][] = $row_questions[$h];
        }
        //Added By HJ On 24-07-2020 For Optimize faqs Table Query End
        while (count($row) > $i) {
            $rows_questions = $row_questions = array();
            $iUniqueId = $row[$i]['iUniqueId'];
            //Added By HJ On 24-07-2020 For Optimize faqs Table Query Start
            //$row_questions = $obj->MySQLSelect("SELECT vTitle_" . $languageCode . " as vTitle,tAnswer_" . $languageCode . " as tAnswer FROM `faqs` WHERE eStatus='".$status."' AND iFaqcategoryId='" . $iUniqueId . "' ORDER BY iDisplayOrder ASC");
            if (isset($faqDataArr[$iUniqueId])) {
                $row_questions = $faqDataArr[$iUniqueId];
            }
            //Added By HJ On 24-07-2020 For Optimize faqs Table Query End
            $j = 0;
            while (count($row_questions) > $j) {
                $rows_questions[$j] = $row_questions[$j];
                $j++;
            }
            if (!empty($rows_questions)) {
                $row[$i]['Questions'] = $rows_questions;
                $row_main[$k] = $row[$i];
                $k++;
            }

            $i++;
        }
        $returnData['Action'] = "1";
        $returnData['message'] = $row_main;
    }
    else {
        $returnData['Action'] = "0";
        $returnData['message'] = "LBL_FAQ_NOT_AVAIL";
    }
    setDataResponse($returnData);
}

if ($type == "uploadImage") {
    $iMemberId = isset($_REQUEST['iMemberId']) ? clean($_REQUEST['iMemberId']) : '';
    $memberType = isset($_REQUEST['GeneralUserType']) ? clean($_REQUEST['GeneralUserType']) : '';
    $image_name = $vImage = isset($_FILES['vImage']['name']) ? $_FILES['vImage']['name'] : '';
    $image_object = isset($_FILES['vImage']['tmp_name']) ? $_FILES['vImage']['tmp_name'] : '';
    $image_name = "123.jpg";
    if ($memberType == "Driver") {
        $Photo_Gallery_folder = $tconfig['tsite_upload_images_driver_path'];
    } elseif ($memberType == "Tracking") {
        $Photo_Gallery_folder = $tconfig['tsite_upload_images_track_company_user_path'];
    } else {
        $Photo_Gallery_folder = $tconfig['tsite_upload_images_passenger_path'];
    }
    if (!is_dir($Photo_Gallery_folder)) mkdir($Photo_Gallery_folder, 0777);
    $Photo_Gallery_folder = $Photo_Gallery_folder . "/" . $iMemberId . "/";
    if (!is_dir($Photo_Gallery_folder)) mkdir($Photo_Gallery_folder, 0777);
    $vImageName = $UPLOAD_OBJ->GeneralUploadImage($image_object, $image_name, $Photo_Gallery_folder, $tconfig["tsite_upload_images_member_size1"], $tconfig["tsite_upload_images_member_size2"], $tconfig["tsite_upload_images_member_size3"], '', '', '', 'Y', '', $Photo_Gallery_folder);
    if ($vImageName != '') {
        if ($memberType == "Driver") {
            $getDriverData = $obj->MySQLSelect("SELECT vImage,eStatus,vName,vLastName FROM register_driver WHERE iDriverId = '" . $iMemberId . "'");
            $OldImageName = $getDriverData[0]['vImage'];
            $checkEditProfileStatus = getEditDriverProfileStatus($getDriverData[0]['eStatus']); // Added By HJ On 13-11-2019 For Check Driver Profile Edit Status As Per Discuss With KS Sir
            if ($OldImageName != "" && $checkEditProfileStatus == "No") {
                $returnArr['Action'] = "0";
                $returnArr['message'] = "LBL_EDIT_PROFILE_DISABLED";
                setDataResponse($returnArr);
            }
            $where = " iDriverId = '" . $iMemberId . "'";
            $Data_passenger['vImage'] = $vImageName;
            $id = $obj->MySQLQueryPerform("register_driver", $Data_passenger, 'update', $where);
        } elseif ($memberType == "Tracking") {
            $OldImageName = get_value('track_service_users', 'vImage', 'iTrackServiceUserId', $iMemberId, '', 'true');
            $where = " iTrackServiceUserId = '" . $iMemberId . "'";
            $Data_passenger['vImage'] = $vImageName;
            $id = $obj->MySQLQueryPerform("track_service_users", $Data_passenger, 'update', $where);
        } else {
            $OldImageName = get_value('register_user', 'vImgName', 'iUserId', $iMemberId, '', 'true');
            $where = " iUserId = '" . $iMemberId . "'";
            $Data_passenger['vImgName'] = $vImageName;
            $id = $obj->MySQLQueryPerform("register_user", $Data_passenger, 'update', $where);
        }
        unlink($Photo_Gallery_folder . $OldImageName);
        unlink($Photo_Gallery_folder . "1_" . $OldImageName);
        unlink($Photo_Gallery_folder . "2_" . $OldImageName);
        unlink($Photo_Gallery_folder . "3_" . $OldImageName);
        if ($id > 0) {
            $returnArr['Action'] = "1";
            if ($memberType == "Driver") {
                $returnArr['message'] = getDriverDetailInfo($iMemberId);
            } elseif ($memberType == "Tracking") {
                $returnArr['message'] = $TRACK_ANY_SERVICE_OBJ->getTrackingMemberDetailInfo($iMemberId);
            } else {
                $returnArr['message'] = getPassengerDetailInfo($iMemberId, "", "");
            }
        } else {
            $returnArr['Action'] = "0";
            $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
        }
    } else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
    }
    setDataResponse($returnArr);
}

if ($type == "updatePassword") {
    $user_id = isset($_REQUEST["UserID"]) ? $_REQUEST["UserID"] : '';
    $Upass = isset($_REQUEST["pass"]) ? $_REQUEST["pass"] : '';
    $UserType = isset($_REQUEST["UserType"]) ? clean($_REQUEST["UserType"]) : ''; // UserType = Driver/Passenger
    $CurrentPassword = isset($_REQUEST["CurrentPassword"]) ? $_REQUEST["CurrentPassword"] : '';
    if ($UserType == "Passenger") {
        $tblname = "register_user";
        $tblField = "iUserId";
    } elseif ($UserType == "Tracking") {
        $tblname = "track_service_users";
        $tblField = "iTrackServiceUserId";
    } elseif ($UserType == "Company") {
        $tblname = "company";
        $tblField = "iCompanyId";
    } else {
        $tblname = "register_driver";
        $tblField = "iDriverId";
    }

    $vPassword = get_value($tblname, 'vPassword', $tblField, $user_id, '', 'true');
    # Check For Valid password #
    if ($CurrentPassword != "") {
        $hash = $vPassword;
        $checkValidPass = $AUTH_OBJ->VerifyPassword($CurrentPassword, $hash);
        if ($checkValidPass == 0) {
            $returnArr['Action'] = "0";
            $returnArr['message'] = "LBL_WRONG_PASSWORD";
            setDataResponse($returnArr);
        }
    }
    # Check For Valid password #
    $updatedPassword = encrypt_bycrypt($Upass);
    $Data_update_user['vPassword'] = $updatedPassword;
    $where = " $tblField = '$user_id'";
    $id = $obj->MySQLQueryPerform($tblname, $Data_update_user, 'update', $where);
    
    if ($id > 0) {
        $returnArr['Action'] = "1";
        $returnArr['message1'] = "LBL_PASS_UPDATE_MSG";
        if ($UserType == "Passenger") {
            $returnArr['message'] = getPassengerDetailInfo($user_id, "", "");
        } elseif ($UserType == "Tracking") {
            $returnArr['message'] = $TRACK_ANY_SERVICE_OBJ->getTrackingMemberDetailInfo($user_id);
        } elseif ($UserType == "Company") {
            $returnArr['message'] = getCompanyDetailInfo($user_id, "");
        } else {
            $returnArr['message'] = getDriverDetailInfo($user_id);
        }
        setDataResponse($returnArr);
    } else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
        setDataResponse($returnArr);
    }
}

// ###########################Send Sms Twilio####################################
if ($type == 'sendVerificationSMS') {
    $mobileNo = isset($_REQUEST['MobileNo']) ? clean($_REQUEST['MobileNo']) : '';
    $mobileNo = str_replace('+', '', $mobileNo);
    $iMemberId = isset($_REQUEST['iMemberId']) ? clean($_REQUEST['iMemberId']) : '';
    $userType = isset($_REQUEST['UserType']) ? clean($_REQUEST['UserType']) : 'Passenger';
    $REQ_TYPE = isset($_REQUEST["REQ_TYPE"]) ? $_REQUEST['REQ_TYPE'] : '';
    if ($REQ_TYPE == "DO_EMAIL_PHONE_VERIFY" || $REQ_TYPE == "DO_PHONE_VERIFY") {
        CheckUserSmsLimit($iMemberId, $userType);
    }

    $isdCode = $SITE_ISD_CODE;

    if ($userType == "Passenger") {
        $tblname = "register_user";
        $fields = 'iUserId, vPhone,vPhoneCode as vPhoneCode, vEmail, vName, vLastName';
        $condfield = 'iUserId';
        $vLangCode = get_value('register_user', 'vLang', 'iUserId', $iMemberId, '', 'true');
    }
    else if ($userType == "Company") {
        $tblname = "company";
        $fields = 'iCompanyId, vPhone,vCode as vPhoneCode, vEmail, vCompany as vName';
        $condfield = 'iCompanyId';
        $vLangCode = get_value('company', 'vLang', 'iCompanyId', $iMemberId, '', 'true');
    }
    else {
        $tblname = "register_driver";
        $fields = 'iDriverId, vPhone,vCode as vPhoneCode, vEmail, vName, vLastName';
        $condfield = 'iDriverId';
        $vLangCode = get_value('register_driver', 'vLang', 'iDriverId', $iMemberId, '', 'true');
    }
    if ($vLangCode == "" || $vLangCode == NULL) {
        //Added By HJ On 15-07-2020 For Optimize language_master Table Query Start
        $vLangCode = $LANG_OBJ->FetchDefaultLangData("vCode");
        //Added By HJ On 15-07-2020 For Optimize language_master Table Query End
    }
    $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLangCode, "1", $iServiceId);
    $str = "select * from send_message_templates where vEmail_Code='VERIFICATION_CODE_MESSAGE'";
    $res = $obj->MySQLSelect($str);
    $prefix = $res[0]['vBody_' . $vLangCode];
    // $prefix = $languageLabelsArr['LBL_VERIFICATION_CODE_TXT'];
    $verificationCode_sms = generateCommonRandom();
    $verificationCode_email = generateCommonRandom();
    $message = $prefix . ' ' . $verificationCode_sms;
    if ($iMemberId == "" && $REQ_TYPE == "DO_PHONE_VERIFY") {
        $toMobileNum = "+" . $mobileNo;
    } else {
        $db_member = $obj->MySQLSelect("select $fields from $tblname where $condfield = '" . $iMemberId . "'");
        $Data_Mail['vEmail'] = isset($db_member[0]['vEmail']) ? $db_member[0]['vEmail'] : '';
        $vFirstName = isset($db_member[0]['vName']) ? $db_member[0]['vName'] : '';
        $vLastName = isset($db_member[0]['vLastName']) ? $db_member[0]['vLastName'] : '';
        $Data_Mail['vName'] = $vFirstName . " " . $vLastName;
        $Data_Mail['CODE'] = $verificationCode_email;
        $mobileNo = $db_member[0]['vPhoneCode'] . $db_member[0]['vPhone'];
        $toMobileNum = "+" . $mobileNo;
    }
    /********************** Firebase SMS Verfication **********************************/
    $returnArr['MOBILE_NO_VERIFICATION_METHOD'] = $MOBILE_NO_VERIFICATION_METHOD;
    /********************** Firebase SMS Verfication **********************************/
    $emailmessage = "";
    $phonemessage = "";
    if ($REQ_TYPE == "DO_EMAIL_PHONE_VERIFY") {
        $sendemail = $COMM_MEDIA_OBJ->SendMailToMember("APP_EMAIL_VERIFICATION_USER", $Data_Mail);
        if ($sendemail != true || $sendemail != "true" || $sendemail != "1") {
            $sendemail = 0;
        }
        //added by SP for sms functionality change, to get phonecode use this one on 12-7-2019 start
        if ($userType == "Passenger") {
            $passengerData = $obj->MySQLSelect("SELECT r.vPhone,c.vPhoneCode FROM  `register_user` AS r, `country` AS c WHERE r.iUserId = $iMemberId AND r.vCountry = c.vCountryCode");
        }
        else if ($userType == "Company") {
            $passengerData = $obj->MySQLSelect("SELECT r.vPhone,c.vPhoneCode FROM  `company` AS r, `country` AS c WHERE r.iCompanyId = $iMemberId AND r.vCountry = c.vCountryCode");
        }
        else {
            $passengerData = $obj->MySQLSelect("SELECT r.vPhone,c.vPhoneCode FROM  `register_driver` AS r, `country` AS c WHERE r.iDriverId = $iMemberId AND r.vCountry = c.vCountryCode");
        }
        $PhoneCode = $passengerData[0]['vPhoneCode'];
        /********************** Firebase SMS Verfication **********************************/
        if (strtoupper($MOBILE_NO_VERIFICATION_METHOD) != "FIREBASE") {
            $result = $COMM_MEDIA_OBJ->SendSystemSMS($toMobileNum, $PhoneCode, $message);
        }
        else {
            $result = 1;
        }
        /********************** Firebase SMS Verfication **********************************/
        if ($result == 1) {
            UpdateUserSmsLimit($iMemberId, $userType);
        }
        $returnArr['Action'] = "1";
        if ($sendemail == 0 && $result == 0) {
            $returnArr['Action'] = "0";
            $returnArr['message'] = "LBL_ACC_VERIFICATION_FAILED";
        }
        else {
            $returnArr['message_sms'] = $result == 0 ? "LBL_MOBILE_VERIFICATION_FAILED_TXT" : $verificationCode_sms;
            $returnArr['eSMSFailed'] = "No";
            if ($returnArr['message_sms'] == "LBL_MOBILE_VERIFICATION_FAILED_TXT") {
                $returnArr['eSMSFailed'] = "Yes";
            }
            $returnArr['message_email'] = $sendemail == 0 ? "LBL_EMAIL_VERIFICATION_FAILED_TXT" : $verificationCode_email;
            $returnArr['eEmailFailed'] = "No";
            if ($returnArr['message_email'] == "LBL_EMAIL_VERIFICATION_FAILED_TXT") {
                $returnArr['eEmailFailed'] = "Yes";
            }
        }
        setDataResponse($returnArr);
    } else if ($REQ_TYPE == "DO_PHONE_VERIFY") {
        //added by SP for sms functionality change, to get phonecode use this one on 12-7-2019 start
        if ($userType == "Passenger") {
            $passengerData = $obj->MySQLSelect("SELECT r.vPhone,c.vPhoneCode FROM  `register_user` AS r, `country` AS c WHERE r.iUserId = $iMemberId AND r.vCountry = c.vCountryCode");
        }
        else if ($userType == "Company") {
            $passengerData = $obj->MySQLSelect("SELECT r.vPhone,c.vPhoneCode FROM  `company` AS r, `country` AS c WHERE r.iCompanyId = $iMemberId AND r.vCountry = c.vCountryCode");
        }
        else {
            $passengerData = $obj->MySQLSelect("SELECT r.vPhone,c.vPhoneCode FROM  `register_driver` AS r, `country` AS c WHERE r.iDriverId = $iMemberId AND r.vCountry = c.vCountryCode");
        }
        $PhoneCode = $passengerData[0]['vPhoneCode'];
        /********************** Firebase SMS Verfication **********************************/
        if (strtoupper($MOBILE_NO_VERIFICATION_METHOD) != "FIREBASE") {
            $result = $COMM_MEDIA_OBJ->SendSystemSMS($toMobileNum, $PhoneCode, $message);
        }
        else {
            $result = 1;
        }
        /********************** Firebase SMS Verfication **********************************/
        if ($result == 0) {
            $returnArr['Action'] = "0";
            $returnArr['message'] = "LBL_MOBILE_VERIFICATION_FAILED_TXT";
            setDataResponse($returnArr);
        }
        else {
            $returnArr['Action'] = "1";
            $returnArr['message'] = strval($verificationCode_sms);
            $returnArr['Phone'] = $mobileNo;
            UpdateUserSmsLimit($iMemberId, $userType);
            setDataResponse($returnArr);
        }
    }
    else if ($REQ_TYPE == "DO_EMAIL_VERIFY") {
        $sendemail = $COMM_MEDIA_OBJ->SendMailToMember("APP_EMAIL_VERIFICATION_USER", $Data_Mail);
        if ($sendemail != true || $sendemail != "true" || $sendemail != "1") {
            $sendemail = 0;
        }
        if ($sendemail == 0) {
            $returnArr['Action'] = "0";
            $returnArr['message'] = "LBL_EMAIL_VERIFICATION_FAILED_TXT";
            setDataResponse($returnArr);
        }
        else {
            $returnArr['Action'] = "1";
            $returnArr['message'] = strval($Data_Mail['CODE']);
            setDataResponse($returnArr);
        }
    }
    else if ($REQ_TYPE == "EMAIL_VERIFIED") {
        $where = " " . $condfield . " = '" . $iMemberId . "'";
        $Data['eEmailVerified'] = "Yes";
        $id = $obj->MySQLQueryPerform($tblname, $Data, 'update', $where);
        if ($id) {
            $returnArr['Action'] = "1";
            $returnArr['message'] = "LBL_EMAIl_VERIFIED";
            if ($userType == 'Passenger') {
                $returnArr['userDetails']['Action'] = "1";
                $returnArr['userDetails']['message'] = getPassengerDetailInfo($iMemberId, "", "");
            }
            else if ($userType == 'Company') {
                $returnArr['userDetails']['Action'] = "1";
                $returnArr['userDetails']['message'] = getCompanyDetailInfo($iMemberId);
            }
            else {
                $returnArr['userDetails']['Action'] = "1";
                $returnArr['userDetails']['message'] = getDriverDetailInfo($iMemberId);
            }
            setDataResponse($returnArr);
        }
        else {
            $returnArr['Action'] = "0";
            $returnArr['message'] = "LBL_EMAIl_VERIFIED_ERROR";
            setDataResponse($returnArr);
        }
    }
    else if ($REQ_TYPE == "PHONE_VERIFIED") {
        $where = " " . $condfield . " = '" . $iMemberId . "'";
        $Data['ePhoneVerified'] = "Yes";
        $id = $obj->MySQLQueryPerform($tblname, $Data, 'update', $where);
        if ($id) {
            $returnArr['Action'] = "1";
            $returnArr['message'] = "LBL_PHONE_VERIFIED";
            if ($userType == 'Passenger') {
                $returnArr['userDetails']['Action'] = "1";
                $returnArr['userDetails']['message'] = getPassengerDetailInfo($iMemberId, "", "");
            }
            else if ($userType == 'Company') {
                $returnArr['userDetails']['Action'] = "1";
                $returnArr['userDetails']['message'] = getCompanyDetailInfo($iMemberId);
            }
            else {
                $returnArr['userDetails']['Action'] = "1";
                $returnArr['userDetails']['message'] = getDriverDetailInfo($iMemberId);
            }
            setDataResponse($returnArr);
        }
        else {
            $returnArr['Action'] = "0";
            $returnArr['message'] = "LBL_PHONE_VERIFIED_ERROR";
            setDataResponse($returnArr);
        }
    }
}
// ###########################Send Sms Twilio END################################

if ($type == "getTransactionHistory") {
    $page = isset($_REQUEST['page']) ? trim($_REQUEST['page']) : 1;
    $iUserId = isset($_REQUEST["iMemberId"]) ? $_REQUEST["iMemberId"] : '';
    $UserType = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : '';
    $tripTimeZone = isset($_REQUEST["vTimeZone"]) ? $_REQUEST["vTimeZone"] : '';
    $ListType = isset($_REQUEST["ListType"]) ? $_REQUEST["ListType"] : 'All';
    $dateRange = isset($_REQUEST["dateRange"]) ? $_REQUEST["dateRange"] : '';
    $startDate = isset($_REQUEST["startDate"]) ? $_REQUEST["startDate"] : '';
    $endDate = isset($_REQUEST["endDate"]) ? $_REQUEST["endDate"] : '';
    $duration_sql = "";
    if (!empty($dateRange)) {
        if (strtolower($dateRange) == "today") {
            $start_date = date('Y-m-d 00:00:00');
            $end_date = date('Y-m-d 23:59:59');
        } elseif (strtolower($dateRange) == "week") {
            $start_date = date('Y-m-d 00:00:00', strtotime('sunday this week -1 week'));
            $end_date = date('Y-m-d 23:59:59');
        } elseif (strtolower($dateRange) == "month") {
            $start_date = date('Y-m-01 00:00:00');
            $end_date = date('Y-m-d 23:59:59');
        } elseif (strtolower($dateRange) == "range") {
            $start_date = date('Y-m-d 00:00:00', strtotime($startDate));
            $end_date = date('Y-m-d 23:59:59', strtotime($endDate));
        }
        $duration_sql = " AND dDate BETWEEN '" . $start_date . "' AND '" . $end_date . "' ";
    }
    if ($page == "0" || $page == 0) {
        $page = 1;
    }
    if ($UserType == "Passenger") {
        $UserType = "Rider";
    }
    $ssql = '';
    if ($ListType != "All") {
        $ssql .= " AND uw.eType ='" . $ListType . "'";
    }
    $per_page = 10;
    $data_count_all = $obj->MySQLSelect("SELECT COUNT(iUserWalletId) As TotalIds FROM user_wallet as uw WHERE  iUserId='" . $iUserId . "' AND eUserType = '" . $UserType . "' " . $ssql . $duration_sql);
    $TotalPages = ceil($data_count_all[0]['TotalIds'] / $per_page);
    $start_limit = ($page - 1) * $per_page;
    $limit = " LIMIT " . $start_limit . ", " . $per_page;
    $sql = "SELECT uw.*, p.tPaymentUserID as iTransactionId from user_wallet as uw LEFT JOIN payments as p on p.iUserWalletId = uw.iUserWalletId where uw.iUserId='" . $iUserId . "' AND uw.eUserType = '" . $UserType . "' " . $ssql . $duration_sql . " ORDER BY iUserWalletId DESC" . $limit;
    $Data = $obj->MySQLSelect($sql);
    $totalNum = count($Data);
    //Added By HJ On 08-07-2020 For Optimization currency Table Query Start
    if (!empty($vSystemDefaultCurrencySymbol)) {
        $vSymbol = $vSystemDefaultCurrencySymbol;
    } else {
        $vSymbol = get_value('currency', 'vSymbol', 'eDefault', 'Yes', '', 'true');
    }
    //Added By HJ On 08-07-2020 For Optimization currency Table Query End
    //Added By HJ On 08-07-2020 For Optimization register_driver/register_user Table Query Start
    if (strtoupper($UserType) == 'DRIVER') {
        $tableName = "register_driver";
        $fieldName = "iDriverId";
        $currencyField = "vCurrencyDriver";
        //$UserData = get_value('register_driver', 'vCurrencyDriver,vLang', 'iDriverId', $iUserId);
        //$uservSymbol = $UserData[0]['vCurrencyDriver'];
        //$vLangCode = $UserData[0]['vLang'];
    } else {
        $tableName = "register_user";
        $fieldName = "iUserId";
        $currencyField = "vCurrencyPassenger";
        //$UserData = get_value('register_user', 'vCurrencyPassenger,vLang', 'iUserId', $iUserId);
        //$uservSymbol = $UserData[0]['vCurrencyPassenger'];
        //$vLangCode = $UserData[0]['vLang'];
    }
    //Added By HJ On 08-07-2020 For Optimize register_driver/register_user Table Query Start
    if (isset($userDetailsArr[$tableName . "_" . $iUserId])) {
        $UserData = $userDetailsArr[$tableName . "_" . $iUserId];
    } else {
        $UserData = $obj->MySQLSelect("SELECT *,$fieldName AS iMemberId FROM " . $tableName . " WHERE $fieldName='" . $iUserId . "'");
        $userDetailsArr[$tableName . "_" . $iUserId] = $UserData;
    }
    //Added By HJ On 08-07-2020 For Optimize register_driver/user Table Query End
    $uservSymbol = $UserData[0][$currencyField];
    $vLangCode = $UserData[0]['vLang'];
    $vAccountNumber = "";
    $walletSqlData = array();
    if (strtoupper($UserType) == 'DRIVER') {
        $vAccountNumber = $UserData[0]['vAccountNumber'];
        //$walletSqlData = $obj->MySQLSelect("SELECT (SELECT SUM(iBalance) FROM `user_wallet` WHERE iUserId = '" . $iUserId . "' AND eFor = 'Referrer') as REFERRAL_AMOUNT, (SELECT SUM(iBalance) FROM `user_wallet` WHERE iUserId = '" . $iUserId . "' AND eType = 'Credit') as CREDIT_AMOUNT, (SELECT SUM(iBalance) FROM `user_wallet` WHERE iUserId = '" . $iUserId . "' AND eType = 'DEBIT') as DEBIT_AMOUNT");
        $walletSqlData = $obj->MySQLSelect("SELECT iBalance,eType,dDate,eFor FROM user_wallet WHERE iUserId = '" . $iUserId . "' AND eUserType = 'Driver'");
    }
    //Added By HJ On 08-07-2020 For Optimize currency Table Query Start
    if (isset($currencyAssociateArr[$uservSymbol])) {
        $userCurrencyData = $currencyAssociateArr[$uservSymbol];
        $userCurrencySymbol = $vSymbol = $userCurrencyData['vSymbol'];
        $vCurrency = $userCurrencyData['vName'];
        $Ratio = $userCurrencyData['Ratio'];
    } else {
        $userCurrencyData = $obj->MySQLSelect("SELECT vSymbol,vName,Ratio FROM currency WHERE vName='" . $uservSymbol . "'");
        $vSymbol = $userCurrencySymbol = $userCurrencyData[0]['vSymbol'];
        $vCurrency = $userCurrencyData[0]['vName'];
        $Ratio = $userCurrencyData[0]['Ratio'];
    }
    if ($vCurrency == "" || $vCurrency == null) {
        if (!empty($vSystemDefaultCurrencyName) && !empty($vSystemDefaultCurrencySymbol) && !empty($vSystemDefaultCurrencyRatio)) {
            $vCurrency = $vSystemDefaultCurrencyName;
            $vSymbol = $vSystemDefaultCurrencySymbol;
            $Ratio = $vSystemDefaultCurrencyRatio;
        } else {
            $db_currency = $obj->MySQLSelect("SELECT vName,vSymbol,Ratio from currency WHERE eDefault = 'Yes'");
            $vSymbol = $db_currency[0]['vSymbol'];
            $vCurrency = $db_currency[0]['vName'];
            $Ratio = $db_currency[0]['Ratio'];
        }
    }
    //Added By HJ On 08-07-2020 For Optimize currency Table Query End
    if ($vLangCode == "" || $vLangCode == NULL) {
        //Added By HJ On 08-07-2020 For Optimize language_master Table Query Start
        $vLangCode = $LANG_OBJ->FetchDefaultLangData("vCode");
        //Added By HJ On 08-07-2020 For Optimize language_master Table Query End
    }
    $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLangCode, "1", $iServiceId);
    $i = 0;
    if (count($Data) > 0) {
        $row = $Data;
    
        $prevbalance = 0;
        while (count($row) > $i) {
            if (!empty($row[$i]['tDescription'])) {
                $pat = '/\#([^\"]*?)\#/';
                preg_match($pat, $row[$i]['tDescription'], $tDescription_value);
                $tDescription_translate = $languageLabelsArr[$tDescription_value[1]];
                $row[$i]['tDescription'] = str_replace($tDescription_value[0], $tDescription_translate, $row[$i]['tDescription']);
                //added by SP on 12-11-2020 for transfer description converted from label.

              

                if($row[$i]['eFor'] == "Deposit" && strpos($row[$i]['tDescription'], '#TRIP_NUMBER#') !== false){
                    $row_tDescription = $row[$i]['tDescription'];
                    $ride_number = get_value('trips', 'vRideNo', 'iTripId', $row[$i]['iTripId']);
                    $row_tDescription = str_replace('#TRIP_NUMBER#', $ride_number[0]['vRideNo'] ,$row_tDescription);
                    $row[$i]['tDescription'] = $row_tDescription;
                }

                if ($row[$i]['eFor'] == "Transfer") {
                    $row_tDescription = $row[$i]['tDescription'];
                    if (preg_match($pat, $row_tDescription, $tDescription_value_new)) {
                        $tDescription_translate_second = $languageLabelsArr[$tDescription_value_new[1]];
                        $row_tDescription_one = str_replace($tDescription_value_new[0], $tDescription_translate_second, $row_tDescription);
                        if (preg_match($pat, $row_tDescription_one, $tDescription_value_one)) {
                            $tDescription_translate_third = $languageLabelsArr[$tDescription_value_one[1]];
                            $row[$i]['tDescription'] = str_replace($tDescription_value_one[0], $tDescription_translate_third, $row_tDescription_one);
                        } else {
                            $row[$i]['tDescription'] = $row_tDescription_one;
                        }
                    } else {
                        $row[$i]['tDescription'] = $row_tDescription;
                    }
                }
            }
            // Convert Into Timezone
            if ($tripTimeZone != "") {
                $serverTimeZone = date_default_timezone_get();
                $row[$i]['dDate'] = converToTz($row[$i]['dDate'], $tripTimeZone, $serverTimeZone);
            }
            // Convert Into Timezone
            if ($row[$i]['eType'] == "Credit") {
                $row[$i]['currentbal'] = $prevbalance + $row[$i]['iBalance'];
            } else {
                $row[$i]['currentbal'] = $prevbalance - $row[$i]['iBalance'];
            }
            $prevbalance = $row[$i]['currentbal'];
            $row[$i]['dDateOrig'] = $row[$i]['dDate'];
            $row[$i]['dDate'] = date('d-M-Y', strtotime($row[$i]['dDate']));
            $row[$i]['currentbal'] = $WALLET_OBJ->MemberCurrencyWalletBalance($row[$i]['fRatio_' . $uservSymbol], $row[$i]['currentbal'], $uservSymbol);
            $row[$i]['iBalance'] = $WALLET_OBJ->MemberCurrencyWalletBalance($row[$i]['fRatio_' . $uservSymbol], $row[$i]['iBalance'], $uservSymbol);
            $i++;
        }
        $returnData['message'] = $row;
        if ($TotalPages > $page) {
            $returnData['NextPage'] = $page + 1;
        } else {
            $returnData['NextPage'] = 0;
        }
        $returnData['Action'] = "1";
        if (strtoupper($UserType) == 'DRIVER') {
            $totalCredit = $totalDebit = $totalReferrer = $lastRefBalance = 0;
            $lastRefDate = $lastDebitDate = "";
            foreach ($walletSqlData as $transaction) {
                if ($transaction['eFor'] == "Referrer") {
                    $totalReferrer += $transaction['iBalance'];
                    $lastRefDate = $transaction['dDate'];
                    $lastRefBalance = $transaction['iBalance'];
                }
                if ($transaction['eType'] == "Credit") {
                    $totalCredit += $transaction['iBalance'];
                } elseif ($transaction['eType'] == "Debit") {
                    $totalDebit += $transaction['iBalance'];
                    $lastDebitDate = $transaction['date'];
                    if ($lastRefDate != "") {
                        if (strtotime($lastRefDate) < strtotime($lastDebitDate)) {
                            $totalReferrer -= $lastRefBalance;
                            $totalCredit -= $lastRefBalance;
                        }
                    }
                }
            }
            $non_withdrawable_amount = ($totalReferrer < 0) ? 0 : $totalReferrer;
            $non_withdrawable_amount = number_format($non_withdrawable_amount, 2, '.', '');
            $withdrawable_amount = $totalCredit - ($totalDebit + $non_withdrawable_amount);
            $withdrawable_amount = number_format($withdrawable_amount, 2, '.', '');
            $returnData['WITHDRAWABLE_AMOUNT'] = formateNumAsPerCurrency(($withdrawable_amount * $Ratio), $vCurrency);
            $returnData['ORIG_WITHDRAWABLE_AMOUNT'] = ($withdrawable_amount * $Ratio);
            $returnData['NON_WITHDRAWABLE_AMOUNT'] = formateNumAsPerCurrency(($non_withdrawable_amount * $Ratio), $vCurrency);
            $returnData['ORIG_NON_WITHDRAWABLE_AMOUNT'] = ($non_withdrawable_amount * $Ratio);
            //$vAccountNumber = get_value('register_driver', 'vAccountNumber', 'iDriverId', $iUserId);
            $returnData['vAccountNumber'] = ($vAccountNumber != "") ? 'Yes' : 'No';
            $returnData['ACCOUNT_NO'] = ($vAccountNumber != "") ? $vAccountNumber : 'XXXXXXX';
        }
    } else {
        $returnData['Action'] = "1";
        $returnData['message'] = "LBL_NO_TRANSACTION_AVAIL";
        if (strtolower($ListType) == "credit") {
            $returnData['message'] = "LBL_NO_CREDIT_TRANSACTION_AVAIL";
        } elseif (strtolower($ListType) == "debit") {
            $returnData['message'] = "LBL_NO_DEBIT_TRANSACTION_AVAIL";
        }
        //$returnData['user_available_balance'] = $returnData['MemberBalance'] = $userCurrencySymbol . "0.00";
        $returnData['user_available_balance'] = $returnData['MemberBalance'] = formateNumAsPerCurrency(0, $uservSymbol);
    }
    $user_available_balance = $WALLET_OBJ->FetchMemberWalletBalanceApp($iUserId, $UserType, '', 'Yes');
    $returnData['user_available_balance_default'] = $user_available_balance['DISPLAY_AMOUNT'];
    $returnData['user_available_balance'] = strval($user_available_balance['DISPLAY_AMOUNT']);
    $returnData["MemberBalance"] = strval($user_available_balance['DISPLAY_AMOUNT']);
    $returnData['user_available_balance_amount'] = strval($user_available_balance['ORIG_AMOUNT']);
    setDataResponse($returnData);
}

// ############################## Create withdrawl request #############################################
if ($type == "createWithdrawlRequest") {
    $iUserId = isset($_REQUEST['iMemberId']) ? $_REQUEST['iMemberId'] : '';
    $eUserType = isset($_REQUEST['UserType']) ? $_REQUEST['UserType'] : '';
    $sql = "SELECT vBankAccountHolderName,vAccountNumber,vBankLocation,vBankName,vBIC_SWIFT_Code FROM register_driver WHERE iDriverId = " . $iUserId;
    $userData = $obj->MySQLSelect($sql);
    $vHolderName = $vBankName = $iBankAccountNo = $BICSWIFTCode = $vBankBranch = "";
    if (!empty($userData)) {
        $bank_details = $userData[0];
        $vHolderName = ($bank_details['vBankAccountHolderName'] != "") ? $bank_details['vBankAccountHolderName'] : '';
        $vBankName = ($bank_details['vBankName'] != "") ? $bank_details['vBankName'] : '';
        $iBankAccountNo = ($bank_details['vAccountNumber'] != "") ? $bank_details['vAccountNumber'] : '';
        $BICSWIFTCode = ($bank_details['vBIC_SWIFT_Code'] != "") ? $bank_details['vBIC_SWIFT_Code'] : '';
        $vBankBranch = ($bank_details['vBankLocation'] != "") ? $bank_details['vBankLocation'] : '';
    }
    if ($eUserType == 'Driver') {
        $tblname = 'register_driver';
        $usercurr = 'Driver';
        $where = "WHERE iDriverId = '" . $iUserId . "'";
    } else {
        $eUserType = "Rider";
        $tblname = 'register_user';
        $usercurr = 'Passenger';
        $where = "WHERE iUserId = '" . $iUserId . "'";
    }
    $sql = "select vName, vLastName, vEmail, vCurrency" . $usercurr . " as sess_vCurrency, vPhone,vCode from " . $tblname . " " . $where;
    $db_user = $obj->MySQLSelect($sql);
    //$db_user[0]['sess_vCurrency'] = 'INR';
    $sql = "select vName, Ratio from currency where vName = '" . $db_user[0]['sess_vCurrency'] . "'";
    $db_currency = $obj->MySQLSelect($sql);
    $sql = "select vName, Ratio from currency where eDefault = 'Yes'";
    $db_currency_admin = $obj->MySQLSelect($sql);
    $User_Available_Balance = $WALLET_OBJ->FetchMemberWalletBalance($iUserId, $UserType);
    $fAmount = $_REQUEST['amount'];
    //$fcheckamount = round($fAmount * $db_currency[0]['Ratio'],2);
    $fcheckamount = round($fAmount, 2); //changed by SP withdraw request  for different currency wrong msg shown bc user enters in his currency only so no need to multiplied it from issue#329 on 03-10-2019
    $fcheckamountNew = round($fcheckamount / $db_currency[0]['Ratio'], 2);
    $withdrawalamtuser = formateNumAsPerCurrency($fAmount, $db_user[0]['sess_vCurrency']);
    $withdrawalamtadmin = formateNumAsPerCurrency($fcheckamount, $db_currency_admin[0]['vName']);
    $availableAmountOfUser = round($User_Available_Balance * $db_currency[0]['Ratio'], 2); // Added By HJ On 30-09-2019 For Solved Sheet Issue #http://mobileappsdemo.com/support-system/view.php?id=8131
    // $walletSql = "SELECT (SELECT SUM(iBalance) FROM `user_wallet` WHERE iUserId = '" . $iUserId . "' AND eFor = 'Referrer') as REFERRAL_AMOUNT, (SELECT SUM(iBalance) FROM `user_wallet` WHERE iUserId = '" . $iUserId . "' AND eType = 'Credit') as CREDIT_AMOUNT, (SELECT SUM(iBalance) FROM `user_wallet` WHERE iUserId = '" . $iUserId . "' AND eType = 'DEBIT') as DEBIT_AMOUNT";
    // $walletSqlData = $obj->MySQLSelect($walletSql);
    $walletSqlData = $obj->MySQLSelect("SELECT iBalance,eType,dDate,eFor FROM user_wallet WHERE iUserId = '" . $iUserId . "' AND eUserType = '$eUserType'");
    $totalCredit = $totalDebit = $totalReferrer = $lastRefBalance = 0;
    $lastRefDate = $lastDebitDate = "";
    foreach ($walletSqlData as $transaction) {
        if ($transaction['eFor'] == "Referrer") {
            $totalReferrer += $transaction['iBalance'];
            $lastRefDate = $transaction['dDate'];
            $lastRefBalance = $transaction['iBalance'];
        }
        if ($transaction['eType'] == "Credit") {
            $totalCredit += $transaction['iBalance'];
        } elseif ($transaction['eType'] == "Debit") {
            $totalDebit += $transaction['iBalance'];
            $lastDebitDate = $transaction['date'];
            if ($lastRefDate != "") {
                if (strtotime($lastRefDate) < strtotime($lastDebitDate)) {
                    $totalReferrer -= $lastRefBalance;
                    $totalCredit -= $lastRefBalance;
                }
            }
        }
    }
    // $ref_deb_diff = $walletSqlData[0]['REFERRAL_AMOUNT'] - $walletSqlData[0]['DEBIT_AMOUNT'];
    // $withdrawable_amount = $walletSqlData[0]['CREDIT_AMOUNT'] + ($walletSqlData[0]['REFERRAL_AMOUNT'] - $walletSqlData[0]['DEBIT_AMOUNT']);
    // $withdrawable_amount = ($ref_deb_diff > 0) ? ($withdrawable_amount - $ref_deb_diff) : $withdrawable_amount;
    // $withdrawable_amount = number_format($withdrawable_amount, 2, '.', '');
    //$ref_deb_diff = $walletSqlData[0]['REFERRAL_AMOUNT'] - $walletSqlData[0]['DEBIT_AMOUNT'];
    $non_withdrawable_amount = ($totalReferrer < 0) ? 0 : $totalReferrer;
    $non_withdrawable_amount = number_format($non_withdrawable_amount, 2, '.', '');
    //$withdrawable_amount = $walletSqlData[0]['CREDIT_AMOUNT'] + ($walletSqlData[0]['REFERRAL_AMOUNT'] - $walletSqlData[0]['DEBIT_AMOUNT']);
    $withdrawable_amount = $totalCredit - ($totalDebit + $non_withdrawable_amount);
    $withdrawable_amount = number_format($withdrawable_amount, 2, '.', '');
    $withdrawable_amount = ($withdrawable_amount * $db_currency[0]['Ratio']);
    if ($fcheckamount > $availableAmountOfUser) {
        $returnArr['Action'] = "0";
        $returnArr['message'] = 'LBL_WITHDRAW_AMT_VALIDATION_MSG';
        setDataResponse($returnArr);
    } else {
        //added by SP on 17-03-2021 to save data of withdraw requests
        $datainsert = array();
        $datainsert['iDriverId'] = $iUserId;
        $datainsert['vName'] = $db_user[0]['vName'];
        $datainsert['vLastName'] = $db_user[0]['vLastName'];
        $datainsert['vEmail'] = $db_user[0]['vEmail'];
        $datainsert['vCode'] = $db_user[0]['vCode'];
        $datainsert['vPhone'] = $db_user[0]['vPhone'];
        $datainsert['fAccountBalance'] = $User_Available_Balance;
        $datainsert['fAmount'] = $fcheckamountNew;
        $datainsert['vCurrency'] = $db_currency_admin[0]['vName'];
        $datainsert['vBankAccountHolderName'] = $vHolderName;
        $datainsert['vBankName'] = $vBankName;
        $datainsert['vAccountNumber '] = $iBankAccountNo;
        $datainsert['vBIC_SWIFT_Code'] = $BICSWIFTCode;
        $datainsert['vBankLocation'] = $vBankBranch;
        $datainsert["tRequestDate"] = @date("Y-m-d H:i:s");
        $insid = $obj->MySQLQueryPerform("withdraw_requests", $datainsert, 'insert');
        /* Admin mail */
        $maildataadmin['Member_Name'] = $db_user[0]['vName'] . " " . $db_user[0]['vLastName'];
        $maildataadmin['Member_Phone'] = $db_user[0]['vPhone'];
        $maildataadmin['Member_Email'] = $db_user[0]['vEmail'];
        $maildataadmin['Account_Name'] = $vHolderName;
        $maildataadmin['Bank_Name'] = $vBankName;
        $maildataadmin['Account_Number'] = $iBankAccountNo;
        $maildataadmin['BIC/SWIFT_Code'] = $BICSWIFTCode;
        $maildataadmin['Bank_Branch'] = $vBankBranch;
        $maildataadmin['Withdrawal_amount'] = $withdrawalamtadmin;
        $res = $COMM_MEDIA_OBJ->SendMailToMember("WITHDRAWAL_MONEY_REQUEST_ADMIN", $maildataadmin);
        //User Mail
        $maildata['User_Name'] = $db_user[0]['vName'] . " " . $db_user[0]['vLastName'];
        $maildata['Withdrawal_amount'] = $withdrawalamtuser;
        $maildata['User_Email'] = $db_user[0]['vEmail'];
        $COMM_MEDIA_OBJ->SendMailToMember("WITHDRAWAL_MONEY_REQUEST_USER", $maildata);
        $returnArr['Action'] = "1";
        $returnArr['message'] = 'LBL_WITHDRAW_AMT_SUCCESS_MSG';
        setDataResponse($returnArr);
    }
}
// ############################## Create withdrawl request End #########################################

if ($type == 'changelanguagelabel') {
    $iHotelId = isset($_REQUEST['iUserId']) ? clean($_REQUEST['iUserId']) : '';
    $GeneralUserType = isset($_REQUEST['GeneralUserType']) ? trim($_REQUEST['GeneralUserType']) : '';
    $vLang = isset($_REQUEST['vLang']) ? clean($_REQUEST['vLang']) : '';
    $UpdatedLanguageLabels = $LANG_OBJ->FetchLanguageLabels($vLang, "1", $iServiceId);
    // Added by HV on 19-03-2021 for multiple service ids
    $iServicesIDS = isset($_REQUEST['iServicesIDS']) ? clean($_REQUEST['iServicesIDS']) : '';
    if(empty($vLang)){
         $vLang = isset($_REQUEST['vGeneralLang']) ? clean($_REQUEST['vGeneralLang']) : '';
    }
    if (!empty($iServicesIDS)) {
        $serviceIds = explode(',', $iServicesIDS);
        if (count($serviceIds) > 0) {
            $UpdatedLanguageLabels = array();
            foreach ($serviceIds as $service_id) {
                $UpdatedLanguageLabels[] = array('iServiceId' => $service_id, 'dataDic' => $LANG_OBJ->FetchLanguageLabels($vLang, "1", $service_id));
            }
        }
    }
    if (strtolower($GeneralUserType) == "hotel" || strtolower($GeneralUserType) == "kiosk") {
        $Datahotel["vLang"] = $vLang;
        $where = " iHotelId = '" . $iHotelId . "'";
        $id = $obj->MySQLQueryPerform("hotel", $Datahotel, 'update', $where);
    }
    $lngData = get_value('language_master', 'vCode, vGMapLangCode, eDirectionCode as eType, vTitle', 'vCode', $vLang);
    $returnArr['Action'] = "1";
    $returnArr['message'] = $UpdatedLanguageLabels;
    $returnArr['vCode'] = $lngData[0]['vCode'];
    $returnArr['vGMapLangCode'] = $lngData[0]['vGMapLangCode'];
    $returnArr['eType'] = $lngData[0]['eType'];
    $returnArr['vTitle'] = $lngData[0]['vTitle'];
    // Added by HV on 04-03-2021 for App launch images
    $returnArr['APP_LAUNCH_IMAGES'] = "";
    if (!empty(getAppLaunchImages($vLang, $GeneralUserType))) {
        $returnArr['APP_LAUNCH_IMAGES'] = getAppLaunchImages($vLang, $GeneralUserType);
    }
    //in ios when language chage then service cat list is not getting so do it.
    $storeCatArr = json_decode(serviceCategories, true);
    //it is done bc when in table in desc field insert like [] then null value is shown so app crash so put the following code
    foreach ($storeCatArr as $key => $value) {
        if (is_null($value['tDescription']) || $value['tDescription'] == '' || $value['tDescription'] == 'null' || empty($value['tDescription'])) {
            $storeCatArr[$key]['tDescription'] = '';
        }
        $eShowTerms = "No";
        $eProofUpload = "No";
        $tProofNote = "";
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
        $storeCatArr[$key]['eShowTerms'] = $eShowTerms;
        $storeCatArr[$key]['eProofUpload'] = $eProofUpload;
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
        $companyData = getStoreDataForSystemStoreSelection($iserviceidstore);
        if (!empty($companyData[0]['iCompanyId'])) {
            $returnArr['STORE_ID'] = $companyData[0]['iCompanyId'];
        } else {
            $returnArr['STORE_ID'] = $companyData['iCompanyId'];
        }
    }
    $returnArr['ServiceCategories'] = $storeCatArr;
    setDataResponse($returnArr);
}

if ($type == "checkUserStatus") {
    $iMemberId = isset($_REQUEST["iMemberId"]) ? $_REQUEST["iMemberId"] : '';
    $UserType = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : 'Passenger';
    if ($UserType == "Passenger") {
        $condfield = 'iUserId';
    } else {
        $condfield = 'iDriverId';
    }
    if ($APP_TYPE == "UberX") {
        $sql = "SELECT iTripId FROM trips WHERE 1=1 AND $condfield = '" . $iMemberId . "' AND vTripPaymentMode != 'Cash' AND eType='UberX' AND (iActive=    'Active' OR iActive='On Going Trip')";
        $checkStatus = $obj->MySQLSelect($sql);
    } else {
        $sql = "SELECT iTripId,iOrderId FROM trips WHERE 1=1 AND $condfield = '" . $iMemberId . "' AND vTripPaymentMode != 'Cash' AND (eType='Ride' || eType='Deliver' || eType='Multi-Delivery') AND (iActive= 'Active' OR iActive='On Going Trip') order by iTripId DESC limit 1";
        $checkStatus = $obj->MySQLSelect($sql);
    }
    if (count($checkStatus) > 0) {
        if ($checkStatus[0]['iOrderId'] > 0) {
            $eBuyAnyService = get_value('orders', 'eBuyAnyService', 'iOrderId', $checkStatus[0]['iOrderId'], '', 'true');
            if ($eBuyAnyService == "Yes") {
                $returnArr['Action'] = "1";
            }
            else {
                $returnArr['Action'] = "0";
                $returnArr['message'] = 'LBL_DIS_ALLOW_EDIT_CARD_DL';
            }
        }
        else {
            $returnArr['Action'] = "0";
            $returnArr['message'] = 'LBL_DIS_ALLOW_EDIT_CARD_DL';
        }
    } else {
        $returnArr['Action'] = "1";
    }
    setDataResponse($returnArr);
}

################################################Get Member Wallet Balance########################################################
if ($type == "GetMemberWalletBalance") {
    $iUserId = isset($_REQUEST["iUserId"]) ? $_REQUEST["iUserId"] : '';
    $UserType = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : 'Passenger';
    $bookingFrom = isset($_REQUEST["bookingFrom"]) ? $_REQUEST["bookingFrom"] : 'App';
    if ($UserType == "Passenger") {
        $tbl_name = "register_user";
        $currencycode = "vCurrencyPassenger";
        $iMemberId = "iUserId";
        $eUserType = "Rider";
    } else {
        $tbl_name = "register_driver";
        $currencycode = "vCurrencyDriver";
        $iMemberId = "iDriverId";
        $eUserType = "Driver";
    }
    //Added By HJ On 18-07-2020 For Optimize register_driver/user Table Query Start
    if (isset($userDetailsArr[$tbl_name . '_' . $iUserId])) {
        $memberData = $userDetailsArr[$tbl_name . '_' . $iUserId];
    } else {
        $memberData = $obj->MySQLSelect("SELECT *,$iMemberId as iMemberId FROM " . $tbl_name . " WHERE $iMemberId='" . $iUserId . "'");
        $userDetailsArr[$tbl_name . '_' . $iUserId] = $memberData;
    }
    $userCurrencyCode = $memberData[0][$currencycode];
    //Added By HJ On 18-07-2020 For Optimize register_driver/user Table Query End
    //$userCurrencyCode = get_value($tbl_name, $currencycode, $iMemberId, $iUserId, '', 'true');
    $user_available_balance = $WALLET_OBJ->FetchMemberWalletBalanceApp($iUserId, $eUserType, '', 'Yes');
    // exit();
    $returnArr['Action'] = "1";
    $returnArr["MemberBalance"] = strval($user_available_balance['DISPLAY_AMOUNT']);
    $returnArr['user_available_balance'] = strval($user_available_balance['DISPLAY_AMOUNT']);
    $returnArr['user_available_balance_amount'] = strval($user_available_balance['ORIG_AMOUNT']);
    if ($bookingFrom == 'Web') {
        $user_available_balance = $WALLET_OBJ->FetchMemberWalletBalance($iUserId, $eUserType, '', 'Yes');
        $returnArr['user_available_balance_web'] = $user_available_balance;
    }
    setDataResponse($returnArr);
}

if ($type == "getHelpDetailCategoty") {
    $status = "Active";
    $iMemberId = isset($_REQUEST['iMemberId']) ? clean($_REQUEST['iMemberId']) : '';
    $appType = isset($_REQUEST['appType']) ? clean($_REQUEST['appType']) : '';
    $eSystem = isset($_REQUEST['eSystem']) ? clean($_REQUEST['eSystem']) : 'General';
    $languageCode = "";
    if ($appType == "Driver") {
        $languageCode = get_value('register_driver', 'vLang', 'iDriverId', $iMemberId, '', 'true');
    } else {
        $languageCode = get_value('register_user', 'vLang', 'iUserId', $iMemberId, '', 'true');
    }
    if ($languageCode == "") {
        //Added By HJ On 15-07-2020 For Optimize language_master Table Query Start
        $languageCode = $LANG_OBJ->FetchDefaultLangData("vCode");
        //Added By HJ On 15-07-2020 For Optimize language_master Table Query End
    }

    if($eSystem != "DeliverAll") {
        $eSystem = "General";
    }
    $Data = $obj->MySQLSelect("SELECT * FROM `help_detail_categories` WHERE eStatus='$status' AND vCode='" . $languageCode . "' AND eSystem = '$eSystem' ORDER BY iDisplayOrder ASC ");
    if (count($Data) > 0) {
        $arr_cat = array();
        for ($i = 0; $i < count($Data); $i++) {
            $arr_cat[$i]['iHelpDetailCategoryId'] = $Data[$i]['iHelpDetailCategoryId'];
            $arr_cat[$i]['vTitle'] = $Data[$i]['vTitle'];
            $arr_cat[$i]['eSystem'] = $Data[$i]['eSystem'];
            $arr_cat[$i]['iUniqueId'] = $Data[$i]['iUniqueId'];
            $iUniqueId = $Data[$i]['iUniqueId'];
            $Data_sub = $obj->MySQLSelect("SELECT vTitle_" . $languageCode . " as vTitle,tAnswer_" . $languageCode . " as tAnswer,eShowDetail,iHelpDetailId FROM `help_detail` WHERE eStatus='$status'  AND iHelpDetailCategoryId='" . $iUniqueId . "' ORDER BY iDisplayOrder ASC ");
            if (count($Data_sub) > 0) {
                $arr_helpdetail = array();
                for ($j = 0; $j < count($Data_sub); $j++) {
                    $arr_helpdetail[$j]['iHelpDetailId'] = $Data_sub[$j]['iHelpDetailId'];
                    $arr_helpdetail[$j]['vTitle'] = $Data_sub[$j]['vTitle'];
                    $arr_helpdetail[$j]['tAnswer'] = $Data_sub[$j]['tAnswer'];
                    $arr_helpdetail[$j]['eShowFrom'] = $Data_sub[$j]['eShowDetail'];
                }
                $arr_cat[$i]['subData'] = $arr_helpdetail;
            } else {
                unset($arr_cat[$i]);
            }
        }

        $arr_cat = array_values($arr_cat);
        
        $returnData['Action'] = "1";
        $returnData['message'] = $arr_cat;
    } else {
        $returnData['Action'] = "0";
        $returnData['message'] = "LBL_HELP_DETAIL_NOT_AVAIL";
    }
    setDataResponse($returnData);
}

############################# getsubHelpdetail #####################################################################
if ($type == "getsubHelpdetail") {
    $status = "Active";
    $iMemberId = isset($_REQUEST['iMemberId']) ? clean($_REQUEST['iMemberId']) : '';
    $iUniqueId = isset($_REQUEST['iUniqueId']) ? clean($_REQUEST['iUniqueId']) : '';
    $appType = isset($_REQUEST['appType']) ? clean($_REQUEST['appType']) : '';
    $languageCode = "";
    if ($appType == "Driver") {
        $languageCode = get_value('register_driver', 'vLang', 'iDriverId', $iMemberId, '', 'true');
    } else {
        $languageCode = get_value('register_user', 'vLang', 'iUserId', $iMemberId, '', 'true');
    }
    if ($languageCode == "") {
        //Added By HJ On 15-07-2020 For Optimize language_master Table Query Start
        $languageCode = $LANG_OBJ->FetchDefaultLangData("vCode");
        //Added By HJ On 15-07-2020 For Optimize language_master Table Query End
    }
    $Data = $obj->MySQLSelect("SELECT vTitle_" . $languageCode . " as vTitle,tAnswer_" . $languageCode . " as tAnswer,eShowDetail,iHelpDetailId FROM `help_detail` WHERE eStatus='$status'  AND iHelpDetailCategoryId='" . $iUniqueId . "' ORDER BY iDisplayOrder ASC ");
    if (count($Data) > 0) {
        $arr_helpdetail = array();
        for ($j = 0; $j < count($Data); $j++) {
            $arr_helpdetail[$j]['iHelpDetailId'] = $Data[$j]['iHelpDetailId'];
            $arr_helpdetail[$j]['vTitle'] = $Data[$j]['vTitle'];
            $arr_helpdetail[$j]['tAnswer'] = $Data[$j]['tAnswer'];
            $arr_helpdetail[$j]['eShowFrom'] = $Data[$j]['eShowDetail'];
        }
        $returnData['Action'] = "1";
        $returnData['message'] = $arr_helpdetail;
    } else {
        $returnData['Action'] = "0";
        $returnData['message'] = "LBL_HELP_DETAIL_NOT_AVAIL";
    }
    setDataResponse($returnData);
}
#############################End getsubHelpdetail #####################################################################
#############################Start getHelpDetail #####################################################################
if ($type == "getHelpDetail") {
    $iMemberId = isset($_REQUEST['iMemberId']) ? clean($_REQUEST['iMemberId']) : '';
    $appType = isset($_REQUEST['appType']) ? clean($_REQUEST['appType']) : '';
    $iUniqueId = isset($_REQUEST['iUniqueId']) ? clean($_REQUEST['iUniqueId']) : '';
    $eSystem = isset($_REQUEST['eSystem']) ? clean($_REQUEST['eSystem']) : 'General';
    $languageCode = "";
    if ($appType == "Driver") {
        $languageCode = get_value('register_driver', 'vLang', 'iDriverId', $iMemberId, '', 'true');
    } else {
        $languageCode = get_value('register_user', 'vLang', 'iUserId', $iMemberId, '', 'true');
    }
    if ($languageCode == "") {
        //Added By HJ On 15-07-2020 For Optimize language_master Table Query Start
        $languageCode = $LANG_OBJ->FetchDefaultLangData("vCode");
        //Added By HJ On 15-07-2020 For Optimize language_master Table Query End
    }

    $ssql = " AND eSystem = 'General' ";
    if($eSystem == "DeliverAll") {
        $ssql = " AND (eSystem='DeliverAll' OR eSystem='General') ";
    }

    $Data = $obj->MySQLSelect("SELECT vTitle_" . $languageCode . " as vTitle,tAnswer_" . $languageCode . " as tAnswer,iHelpDetailId, eShowDetail FROM `help_detail` WHERE eStatus='Active'  $ssql AND iHelpDetailCategoryId='" . $iUniqueId . "'");


    if (count($Data) > 0) {
        $arr_helpdetail = array();
        for ($j = 0; $j < count($Data); $j++) {
            $arr_helpdetail[$j]['iHelpDetailId'] = $Data[$j]['iHelpDetailId'];
            $arr_helpdetail[$j]['vTitle'] = $Data[$j]['vTitle'];
            $arr_helpdetail[$j]['tAnswer'] = $Data[$j]['tAnswer'];
            $arr_helpdetail[$j]['eShowFrom'] = $Data[$j]['eShowDetail'];
        }
        $returnData['Action'] = "1";
        $returnData['message'] = $arr_helpdetail;
    } else {
        $returnData['Action'] = "0";
        $returnData['message'] = "LBL_HELP_DETAIL_NOT_AVAIL";
    }
    setDataResponse($returnData);
}
############################# End getHelpDetail #####################################################################

##########################################Send Verification Email #########################################
if ($type == "sendVerificationEmail") {
    $iMemberId = isset($_REQUEST['iMemberId']) ? clean($_REQUEST['iMemberId']) : '';
    $userType = isset($_REQUEST['UserType']) ? clean($_REQUEST['UserType']) : 'Passenger';
    if ($userType == "Passenger") {
        $tblname = "register_user";
        $fields = 'iUserId, vPhone,vPhoneCode as vPhoneCode, vEmail, vName, vLastName';
        $condfield = 'iUserId';
        $vLangCode = get_value('register_user', 'vLang', 'iUserId', $iMemberId, '', 'true');
    } else {
        $tblname = "register_driver";
        $fields = 'iDriverId, vPhone,vCode as vPhoneCode, vEmail, vName, vLastName';
        $condfield = 'iDriverId';
        $vLangCode = get_value('register_driver', 'vLang', 'iDriverId', $iMemberId, '', 'true');
    }
    $sql = "select $fields from $tblname where $condfield = '" . $iMemberId . "'";
    $db_member = $obj->MySQLSelect($sql);
    $vName = $db_member[0]['vName'] . " " . $db_member[0]['vLastName'];
    $vEmail = $db_member[0]['vEmail'];
    $dt = date("Y-m-d H:i:s");
    $random = substr(number_format(time() * rand(), 0, '', ''), 0, 20);
    $Data['vEmailVarificationCode'] = $random . strtotime($dt);
    $where = " " . $condfield . " = '" . $iMemberId . "'";
    $res = $obj->MySQLQueryPerform($tblname, $Data, 'update', $where);
    $Data_Mail['vEmail'] = $vEmail;
    $Data_Mail['vName'] = $vName;
    $Data_Mail['act_link'] = $tconfig['tsite_url'] . "verifymail.php?act=" . $Data['vEmailVarificationCode'] . "&iMemberId=" . $iMemberId . "&UserType=" . $userType;
    $sendemail = $COMM_MEDIA_OBJ->SendMailToMember("EMAIL_VERIFICATION_USER", $Data_Mail);
    if ($sendemail == 1) {
        $returnArr['Action'] = "1";
        $returnArr['message'] = "LBL_EMAIl_VERIFICATION_SEND_TXT";
        $returnArr['act_link'] = $Data_Mail['act_link'];
    } else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
        $returnArr['act_link'] = $Data_Mail['act_link'];
    }
    setDataResponse($returnArr);
}
#############################Send Verification Email #####################################

// #######################Get Driver Bank Details############################
if ($type == "DriverBankDetails") {
    $iDriverId = isset($_REQUEST["iDriverId"]) ? $_REQUEST["iDriverId"] : '';
    $userType = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : 'Driver';
    $eDisplay = isset($_REQUEST["eDisplay"]) ? $_REQUEST["eDisplay"] : 'Yes';
    $vPaymentEmail = isset($_REQUEST["vPaymentEmail"]) ? $_REQUEST["vPaymentEmail"] : '';
    $vBankAccountHolderName = isset($_REQUEST["vBankAccountHolderName"]) ? $_REQUEST["vBankAccountHolderName"] : '';
    $vAccountNumber = isset($_REQUEST["vAccountNumber"]) ? $_REQUEST["vAccountNumber"] : '';
    $vBankLocation = isset($_REQUEST["vBankLocation"]) ? $_REQUEST["vBankLocation"] : '';
    $vBankName = isset($_REQUEST["vBankName"]) ? $_REQUEST["vBankName"] : '';
    $vBIC_SWIFT_Code = isset($_REQUEST["vBIC_SWIFT_Code"]) ? $_REQUEST["vBIC_SWIFT_Code"] : '';
    if ($eDisplay == "" || $eDisplay == NULL) {
        $eDisplay = "Yes";
    }
    $returnArr = array();
    if ($eDisplay == "Yes") {
        $Driver_Bank_Arr = get_value('register_driver', 'vPaymentEmail, vBankAccountHolderName, vAccountNumber, vBankLocation, vBankName, vBIC_SWIFT_Code', 'iDriverId', $iDriverId);
        $returnArr['Action'] = "1";
        $returnArr['message'] = $Driver_Bank_Arr[0];
        setDataResponse($returnArr);
    }
    else {
        $Data_Update['vPaymentEmail'] = $vPaymentEmail;
        $Data_Update['vBankAccountHolderName'] = $vBankAccountHolderName;
        $Data_Update['vAccountNumber'] = $vAccountNumber;
        $Data_Update['vBankLocation'] = $vBankLocation;
        $Data_Update['vBankName'] = $vBankName;
        $Data_Update['vBIC_SWIFT_Code'] = $vBIC_SWIFT_Code;
        $where = " iDriverId = '" . $iDriverId . "'";
        $obj->MySQLQueryPerform("register_driver", $Data_Update, 'update', $where);
        $returnArr['Action'] = "1";
        $returnArr['message'] = getDriverDetailInfo($iDriverId);
        setDataResponse($returnArr);
    }
}
// #######################Get Driver Bank Details End ############################

if ($type == "SendTripMessageNotification") {
    $UserType = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : 'Passenger';
    $iFromMemberId = isset($_REQUEST["iFromMemberId"]) ? $_REQUEST["iFromMemberId"] : '';
    $iToMemberId = isset($_REQUEST['iToMemberId']) ? clean($_REQUEST['iToMemberId']) : '';
    $iTripId = isset($_REQUEST['iTripId']) ? clean($_REQUEST['iTripId']) : '';
    $iBiddingPostId = isset($_REQUEST['iBiddingPostId']) ? clean($_REQUEST['iBiddingPostId']) : '';
    $tMessage = isset($_REQUEST['tMessage']) ? stripslashes($_REQUEST['tMessage']) : '';
    $sendToStore = isset($_REQUEST['sendToStore']) ? stripslashes($_REQUEST['sendToStore']) : 'No';
    /* Added by HV on 08-03-2021 for VOIP Call Notification */
    $isForVoip = isset($_REQUEST["isForVoip"]) ? $_REQUEST["isForVoip"] : 'No';
    $isCallEnded = isset($_REQUEST["isCallEnded"]) ? $_REQUEST["isCallEnded"] : 'No';
    $isForVideoCall = isset($_REQUEST["isForVideoCall"]) ? $_REQUEST["isForVideoCall"] : 'No';
    if (($isForVoip == "Yes" || $isForVideoCall == "Yes") && $isCallEnded == "No") {
        $nToMemberIdTmp = explode("_", $iToMemberId);
        $nToUserType = $nToMemberIdTmp[0];
        $nToMemberId = $nToMemberIdTmp[1];
        $_REQUEST['nToUserType'] = $nToUserType;
        $vLangCode = $LANG_OBJ->FetchDefaultLangData("vCode");
        $iServiceId = "";
        if (strtolower($nToUserType) == "passenger") {
            $vLangCode = get_value('register_user', 'vLang', 'iUserId', $nToMemberId, '', 'true');
        } elseif (strtolower($nToUserType) == "driver") {
            $vLangCode = get_value('register_driver', 'vLang', 'iDriverId', $nToMemberId, '', 'true');
        } elseif (strtolower($nToUserType) == "company") {
            $vLangCode = get_value('company', 'vLang', 'iCompanyId', $nToMemberId, '', 'true');
        }
        if (strtolower($UserType) == "company") {
            $iServiceId = get_value('company', 'iServiceId', 'iCompanyId', $iFromMemberId, '', 'true');
        }
        $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLangCode, "1", $iServiceId);
        if (strtolower($UserType) == "passenger") {
            $tMessage = $languageLabelsArr['LBL_INCOMING_CALL_BY_USER'];
        } elseif (strtolower($UserType) == "driver") {
            $tMessage = $languageLabelsArr['LBL_INCOMING_CALL_BY_PROVIDER'];
        } elseif (strtolower($UserType) == "company") {
            $tMessage = $languageLabelsArr['LBL_INCOMING_CALL_BY_STORE'];
        } else {
            $tMessage = $languageLabelsArr['LBL_INCOMING_CALL'];
        }
        sendTripMessagePushNotification($iFromMemberId, $UserType, $nToMemberId, $iTripId, $tMessage, "Yes", $iBiddingPostId);
        $returnArr['Action'] = "1";
        setDataResponse($returnArr);
    }
    if (($isForVoip == "Yes" || $isForVideoCall == "Yes") && $isCallEnded == "Yes") {
        $nToMemberIdTmp = explode("_", $iToMemberId);
        $nToUserType = $nToMemberIdTmp[0];
        $nToMemberId = $nToMemberIdTmp[1];
        $_REQUEST['nToUserType'] = $nToUserType;
        $vLangCode = $LANG_OBJ->FetchDefaultLangData("vCode");
        if (strtolower($nToUserType) == "passenger") {
            $vLangCode = get_value('register_user', 'vLang', 'iUserId', $nToMemberId, '', 'true');
        } elseif (strtolower($nToUserType) == "driver") {
            $vLangCode = get_value('register_driver', 'vLang', 'iDriverId', $nToMemberId, '', 'true');
        } elseif (strtolower($nToUserType) == "company") {
            $vLangCode = get_value('company', 'vLang', 'iCompanyId', $nToMemberId, '', 'true');
        }
        $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLangCode, "1", "");
        $tMessage = $languageLabelsArr['LBL_CALL_ENDED'];
        sendTripMessagePushNotification($iFromMemberId, $UserType, $nToMemberId, $iTripId, $tMessage, "Yes", $iBiddingPostId);
        $returnArr['Action'] = "1";
        setDataResponse($returnArr);
    }
    /* Added by HV on 08-03-2021 for VOIP Call Notification End */
    $Data['iTripId'] = $iTripId;
    $Data['iFromMemberId'] = $iFromMemberId;
    $Data['iToMemberId'] = $iToMemberId;
    $Data['tMessage'] = $tMessage;
    $Data['dAddedDate'] = @date("Y-m-d H:i:s");
    $Data['eStatus'] = "Unread";
    $Data['eUserType'] = $UserType;
    $id = $obj->MySQLQueryPerform('trip_messages', $Data, 'insert');
    if ($id > 0) {
        $returnArr['Action'] = "1";
        if ($sendToStore == "Yes") {
            $_REQUEST['nToUserType'] = "Company";
        }
        sendTripMessagePushNotification($iFromMemberId, $UserType, $iToMemberId, $iTripId, $tMessage, '', $iBiddingPostId);
        setDataResponse($returnArr);
    } else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
        setDataResponse($returnArr);
    }
}

if ($type == "configDriverTripStatus") {
    $iMemberId = isset($_REQUEST["iMemberId"]) ? $_REQUEST["iMemberId"] : '';
    $userType = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : 'Passenger';
    $vLatitude = isset($_REQUEST["vLatitude"]) ? $_REQUEST["vLatitude"] : '';
    $vLongitude = isset($_REQUEST["vLongitude"]) ? $_REQUEST["vLongitude"] : '';
    $iTripId = isset($_REQUEST["iTripId"]) ? $_REQUEST["iTripId"] : '';
    $isSubsToCabReq = isset($_REQUEST["isSubsToCabReq"]) ? $_REQUEST["isSubsToCabReq"] : '';
    $vTimeZone = isset($_REQUEST["vTimeZone"]) ? $_REQUEST["vTimeZone"] : '';

    if(strtoupper($userType) == "TRACKING") {
        $TRACK_ANY_SERVICE_OBJ->configTrackingTripStatus();
    }
    //Added By HJ On 21-07-2020 For Optimize register_driver_ Table Query Start
    if (isset($userDetailsArr["register_driver_" . $iMemberId])) {
        $driverdetails = $userDetailsArr["register_driver_" . $iMemberId];
    } else {
        $driverdetails = get_value('register_driver', '*,iDriverId as iMemberId', 'iDriverId', $iMemberId);
        $userDetailsArr["register_driver_" . $iMemberId] = $driverdetails;
    }
    //Added By HJ On 21-07-2020 For Optimize register_driver_ Table Query End
    //$driverdetails = $obj->MySQLSelect("SELECT vAvailability,vTripStatus,eStatus FROM `register_driver` WHERE iDriverId='" . $iMemberId . "'");
    $vAvailability = $driverdetails[0]['vAvailability'];
    $vTripStatus = $driverdetails[0]['vTripStatus'];
    if ($iMemberId != "") {
        //if (!empty($isSubsToCabReq) && $isSubsToCabReq == 'true') {
        if ($vAvailability == "Available" && ($vTripStatus != "On Going Trip" && $vTripStatus != "Arrived" && $vTripStatus != "Active")) {
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
            # Update User Location Date #
            Updateuserlocationdatetime($iMemberId, "Driver", $vTimeZone);
            # Update User Location Date #
        }
    }
    if ($iTripId != "") {
        $msg = $obj->MySQLSelect("SELECT tMessage as msg, iStatusId FROM trip_status_messages WHERE iDriverId='" . $iMemberId . "' AND eToUserType='Driver' AND eReceived='No' ORDER BY iStatusId DESC LIMIT 1");
    }
    else {
        $date = @date("Y-m-d H:i:s", strtotime('-1 minutes'));
        // $msg = $obj->MySQLSelect("SELECT passenger_requests.tMessage as msg  FROM passenger_requests LEFT JOIN driver_request ON  driver_request.iRequestId=passenger_requests.iRequestId  LEFT JOIN register_driver ON register_driver.iDriverId=passenger_requests.iDriverId where date_format(passenger_requests.dAddedDate,'%Y-%m-%d')= '" . $date . "' AND  passenger_requests.iDriverId=" . $iMemberId . " AND driver_request.eStatus='Timeout' AND driver_request.iDriverId='" . $iMemberId . "' AND register_driver.vTripStatus IN ('Not Active','NONE','Cancelled') ORDER BY passenger_requests.iRequestId DESC LIMIT 1");
        $msg = $obj->MySQLSelect("SELECT passenger_requests.tMessage as msg  FROM passenger_requests LEFT JOIN driver_request ON  driver_request.iRequestId=passenger_requests.iRequestId  LEFT JOIN register_driver ON register_driver.iDriverId=passenger_requests.iDriverId where passenger_requests.dAddedDate > '" . $date . "' AND  passenger_requests.iDriverId=" . $iMemberId . " AND driver_request.eStatus='Timeout' AND driver_request.iDriverId='" . $iMemberId . "' AND register_driver.vTripStatus IN ('Not Active','NONE','Cancelled') ORDER BY passenger_requests.iRequestId DESC LIMIT 1");
    }
    /* For DriverSubscription added by SP start */
    if ($vAvailability == "Available" && $MODULES_OBJ->isDriverSubscriptionModuleAvailable()) {
        $returnSubStatus = 0;
        $sql = "SELECT count(iDriverSubscriptionPlanId) as cnt FROM driver_subscription_details WHERE iDriverId = $iMemberId";
        $getDriverSubscription = $obj->MySQLSelect($sql);
        if ($getDriverSubscription[0]['cnt'] > 0) {
            $returnSubStatus = checkDriverSubscribed($iMemberId);
            //$returnSubStatus = checkDriverPlanExpired($iMemberId);
            if ($returnSubStatus == 1) {
                $message_sub = "LBL_PENDING_MIXSUBSCRIPTION";
            }
            if ($returnSubStatus == 2) {
                $message_sub = "PENDING_SUBSCRIPTION";
            }
            if ($returnSubStatus == 1 || $returnSubStatus == 2) {
                //$returnArr['Action'] = "1";
                $returnArr['message_subscription'] = $message_sub;
                $Data_update_driver['tLastOnline'] = @date("Y-m-d H:i:s");
                $curr_date = date('Y-m-d H:i:s');
                $get_data_log = $obj->sql_query("select * from driver_log_report WHERE iDriverId = '" . $iMemberId . "' order by `iDriverLogId` desc limit 0,1");
                $result = $obj->sql_query("UPDATE driver_log_report set dLogoutDateTime = '" . $curr_date . "' WHERE iDriverLogId ='" . $get_data_log[0]['iDriverLogId'] . "'");
                //update insurance log
                if (strtoupper(PACKAGE_TYPE) == "SHARK") {
                    $details_arr['iTripId'] = "0";
                    $details_arr['LatLngArr']['vLatitude'] = $vLatitude;
                    $details_arr['LatLngArr']['vLongitude'] = $vLongitude;
                    // $details_arr['LatLngArr']['vLocation'] = "";
                    update_driver_insurance_status($iMemberId, "Available", $details_arr, "updateDriverStatus", "Offline");
                }
                $Data_update_driver['vAvailability'] = 'Not Available';
                $where = "iDriverId = '" . $iMemberId . "'";
                $id = $obj->MySQLQueryPerform("register_driver", $Data_update_driver, 'update', $where);
            }
        }
    }
    /* For DriverSubscription added by SP end */
    $returnArr['Action'] = "0";
    if (!empty($msg)) {
        $returnArr['Action'] = "1";
        if ($iTripId != "") {
            //$updateQuery = "UPDATE trip_status_messages SET eReceived = 'Yes' WHERE iStatusId='".$msg[0]['iStatusId']."'";
            $updateQuery = "UPDATE trip_status_messages SET eReceived = 'Yes' WHERE iDriverId='" . $iMemberId . "'";
            $obj->sql_query($updateQuery);
            $returnArr['Action'] = "1";
            $returnArr['message'] = $msg[0]['msg'];
        } else {
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
    $returnArr['USER_DATA'] = getDriverDetailInfo($iMemberId);
    setDataResponse($returnArr);
}

if ($type == "configPassengerTripStatus") {
    $iMemberId = isset($_REQUEST["iMemberId"]) ? $_REQUEST["iMemberId"] : '';
    $userType = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : 'Passenger';
    $vLatitude = isset($_REQUEST["vLatitude"]) ? $_REQUEST["vLatitude"] : '';
    $vLongitude = isset($_REQUEST["vLongitude"]) ? $_REQUEST["vLongitude"] : '';
    $iTripId = isset($_REQUEST["iTripId"]) ? $_REQUEST["iTripId"] : '';
    $CurrentDriverIds = isset($_REQUEST["CurrentDriverIds"]) ? explode(',', $_REQUEST["CurrentDriverIds"]) : '';
    $vTimeZone = isset($_REQUEST["vTimeZone"]) ? $_REQUEST["vTimeZone"] : '';
    if ($CurrentDriverIds == "" && $iTripId != "") {
        //Added By HJ On 18-07-2020 For Optimize trips Table Query Start
        if (isset($tripDetailsArr['trips_' . $iTripId])) {
            $data_requst = $tripDetailsArr['trips_' . $iTripId];
        } else {
            $data_requst = $obj->MySQLSelect("SELECT * FROM trips WHERE iTripId='" . $iTripId . "'");
            $tripDetailsArr['trips_' . $iTripId] = $data_requst;
        }
        //Added By HJ On 18-07-2020 For Optimize trips Table Query End
        //$data_requst = $obj->MySQLSelect("SELECT iDriverId FROM trips WHERE iTripId='" . $iTripId . "'");
        $iDriverId = $data_requst[0]['iDriverId'];
        $CurrentDriverIds = (array)$iDriverId;
    }
    if ($iMemberId != "") {
        if (!empty($vLatitude) && !empty($vLongitude)) {
            $user_update['vLatitude'] = $vLatitude;
            $user_update['vLongitude'] = $vLongitude;
            $where = " iUserId = '" . $iMemberId . "'";
            $Update_driver = $obj->MySQLQueryPerform("register_user", $user_update, "update", $where);
            # Update User Location Date #
            Updateuserlocationdatetime($iMemberId, "Passenger", $vTimeZone);
            # Update User Location Date #
        }
    }
    $currDriver = $driverIdArr = $driverDataArr = array();
    if (!empty($CurrentDriverIds)) {
        $k = 0;
        //Added By HJ On 18-07-2020 For Optimize register_driver Table Query Start
        $driverIdArr = array();
        foreach ($CurrentDriverIds as $driverId) {
            $driverIdArr[] = $driverId;
        }
        if (count($driverIdArr) > 0) {
            $implodeId = implode(",", $driverIdArr);
            $driverData = $obj->MySQLSelect("SELECT iDriverId,vLatitude,vLongitude FROM register_driver WHERE iDriverId IN ($implodeId)");
            for ($g = 0; $g < count($driverData); $g++) {
                $driverDataArr[$driverData[$g]['iDriverId']] = $driverData[$g];
            }
        }
        //Added By HJ On 18-07-2020 For Optimize register_driver Table Query End
        foreach ($CurrentDriverIds as $cDriv) {
            //$driverDetails = get_value('register_driver', 'iDriverId,vLatitude,vLongitude', 'iDriverId', $cDriv);
            $driverDetails = array();
            if (isset($driverDataArr[$cDriv])) {
                $driverDetails[] = $driverDataArr[$cDriv];
            }
            $currDriver[$k]['iDriverId'] = $driverDetails[0]['iDriverId'];
            $currDriver[$k]['vLatitude'] = $driverDetails[0]['vLatitude'];
            $currDriver[$k]['vLongitude'] = $driverDetails[0]['vLongitude'];
            $k++;
        }
    }
    $msg = $obj->MySQLSelect("SELECT tMessage as msg, iStatusId FROM trip_status_messages WHERE iUserId='" . $iMemberId . "' AND eToUserType='Passenger' AND eReceived='No' ORDER BY iStatusId DESC LIMIT 1 ");
    $returnArr['Action'] = "0";
    if (!empty($msg)) {
        //$updateQuery = "UPDATE trip_status_messages SET eReceived ='Yes' WHERE iStatusId='".$msg[0]['iStatusId']."'";
        $obj->sql_query("UPDATE trip_status_messages SET eReceived ='Yes' WHERE iUserId='" . $iMemberId . "'");
        $returnArr['Action'] = "1";
        $returnArr['message'] = json_decode($msg[0]['msg'], true);
    }
    $returnArr['currentDrivers'] = $currDriver;
    $returnArr['USER_DATA'] = getPassengerDetailInfo($iMemberId);
    setDataResponse($returnArr);
}

if ($type == "loadPassengersLocation") {
    $iMemberId = isset($_REQUEST["iMemberId"]) ? $_REQUEST["iMemberId"] : '';
    $radius = isset($_REQUEST["Radius"]) ? $_REQUEST["Radius"] : '';
    $sourceLat = isset($_REQUEST["Latitude"]) ? $_REQUEST["Latitude"] : '';
    $sourceLon = isset($_REQUEST["Longitude"]) ? $_REQUEST["Longitude"] : '';
    $str_date = @date('Y-m-d H:i:s', strtotime('-5 minutes'));
    // register_user table
    $sql = "SELECT ROUND(( 6371 * acos( cos( radians(" . $sourceLat . ") )
        * cos( radians( vLatitude ) )
        * cos( radians( vLongitude ) - radians(" . $sourceLon . ") )
        + sin( radians(" . $sourceLat . ") )
        * sin( radians( vLatitude ) ) ) ),2) AS distance, register_user.*  FROM `register_user`
        WHERE (vLatitude != '' AND vLongitude != '' AND eStatus='Active' AND tLastOnline > '$str_date')
        HAVING distance < " . $radius . " ORDER BY `register_user`.iUserId ASC";
    $Data = $obj->MySQLSelect($sql);
    $storeuser = $storetrip = array();
    foreach ($Data as $value) {
        $dataofuser = array("Type" => 'Online', "Latitude" => $value['vLatitude'], "Longitude" => $value['vLongitude'], "iUserId" => $value['iUserId']);
        array_push($storeuser, $dataofuser);
    }
    // trip table
    if (SITE_TYPE == 'Demo') {
        $sql_trip = "SELECT ROUND(( 6371 * acos( cos( radians(" . $sourceLat . ") )
        * cos( radians( tStartLat ) )
        * cos( radians( tStartLong ) - radians(" . $sourceLon . ") )
            + sin( radians(" . $sourceLat . ") )
        * sin( radians( tStartLat ) ) ) ),2) AS distance, trips.*  FROM `trips`
            WHERE (tStartLat != '' AND tStartLong != '' AND tTripRequestDate >= DATE_SUB(CURDATE(), INTERVAL 2500 HOUR))
            HAVING distance < " . $radius . " ORDER BY `trips`.iTripId DESC";
    } else {
        $sql_trip = "SELECT ROUND(( 6371 * acos( cos( radians(" . $sourceLat . ") )
        * cos( radians( tStartLat ) )
        * cos( radians( tStartLong ) - radians(" . $sourceLon . ") )
                + sin( radians(" . $sourceLat . ") )
        * sin( radians( tStartLat ) ) ) ),2) AS distance, trips.*  FROM `trips`
            WHERE (tStartLat != '' AND tStartLong != '' AND tTripRequestDate >= DATE_SUB(CURDATE(), INTERVAL 24 HOUR))
            HAVING distance < " . $radius . " ORDER BY `trips`.iTripId DESC";
    }
    $Dataoftrips = $obj->MySQLSelect($sql_trip);
    foreach ($Dataoftrips as $value1) {
        $valuetrip = array("Type" => 'History', "Latitude" => $value1['tStartLat'], "Longitude" => $value1['tStartLong'], "iTripId" => $value1['iTripId']);
        array_push($storetrip, $valuetrip);
    }
    $finaldata = array_merge($storeuser, $storetrip);
    if (count($finaldata) > 0) {
        $returnData['Action'] = "1";
        $returnData['message'] = $finaldata;
    } else {
        $returnData['Action'] = "0";
    }
    setDataResponse($returnData);
}

if ($type == 'deletedrivervehicle') {
    //global $tconfig, $obj; // Commented By HJ On 15-07-2020 Bcoz Not Required
    $returnArr = array();
    $iMemberCarId = isset($_REQUEST['iDriverVehicleId']) ? clean($_REQUEST['iDriverVehicleId']) : '';
    $iDriverId = isset($_REQUEST['iDriverId']) ? clean($_REQUEST['iDriverId']) : '';
    //Added By HJ On 19-07-2019 For Check Driver Vehicle Availability As Per Discuss With KS Start
    $getDriverData = $obj->MySQLSelect("SELECT iDriverVehicleId,vAvailability FROM register_driver WHERE iDriverId='" . $iDriverId . "'");
    $iDriverVehicleId = 0;
    $vAvailability = "Not Available";
    if (count($getDriverData) > 0) {
        $iDriverVehicleId = $getDriverData[0]['iDriverVehicleId'];
        $vAvailability = $getDriverData[0]['vAvailability'];
    }

    //added by SP when vehicle is active then it can not be deleted on 28-09-2019
    if ($iMemberCarId == $iDriverVehicleId) {
        $returnArr['Action'] = 0;
        $returnArr['message'] = "LBL_ACTIVE_VEHICLE_NOT_DELETE";
        setDataResponse($returnArr);
    }
    $getTripData = $obj->MySQLSelect("SELECT iTripId FROM trips WHERE iActive NOT IN ('Canceled','Finished') AND iDriverVehicleId='" . $iDriverVehicleId . "'");
    if (count($getTripData) > 0) {
        $returnArr['Action'] = 0;
        $returnArr['message'] = "LBL_DELETE_VEHICLE_RESTRICT_SERVICES_NOTE";
        setDataResponse($returnArr);
    }
    if ($iDriverVehicleId == $iMemberCarId && $vAvailability == "Available") {
        $returnArr['Action'] = 0;
        $returnArr['message'] = "LBL_DELETE_VEHICLE_RESTRICT_NOTE";
        setDataResponse($returnArr);
    }
    //Added By HJ On 19-07-2019 For Check Driver Vehicle Availability As Per Discuss With KS End
    $sql = "UPDATE driver_vehicle set eStatus='Deleted' WHERE iDriverVehicleId='" . $iMemberCarId . "' AND iDriverId = '" . $iDriverId . "'";
    $db_sql = $obj->sql_query($sql);
    if ($obj->GetAffectedRows() > 0) {
        $returnArr['Action'] = 1;
        $returnArr['message'] = "LBL_DELETE_VEHICLE";
    } else {
        $returnArr['Action'] = 0;
        $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
    }
    setDataResponse($returnArr);
}

if ($type == "displaydrivervehicles") {
    $iMemberId = isset($_REQUEST['iMemberId']) ? clean($_REQUEST['iMemberId']) : '';
    $memberType = isset($_REQUEST['MemberType']) ? clean($_REQUEST['MemberType']) : 'Driver';
    $eType = isset($_REQUEST["eType"]) ? $_REQUEST["eType"] : 'Ride'; //'Ride', 'Delivery', 'UberX'
    $ssql = "";
    if ($eType == "UberX") {
        $ssql .= " AND dv.eType = 'UberX'";
    } else {
        $ssql .= " AND dv.eType != 'UberX'";
    }
    //$db_usr = $obj->MySQLSelect("select iCompanyId from `register_driver` where iDriverId = '" . $iMemberId . "'");
    //Added By HJ On 22-07-2020 For Optimize register_driver Table Query Start
    if (isset($userDetailsArr['register_driver_' . $iMemberId])) {
        $db_usr = $userDetailsArr['register_driver_' . $iMemberId];
    } else {
        $db_usr = $obj->MySQLSelect("SELECT *,iDriverId as iMemberId FROM `register_driver` WHERE iDriverId = '" . $iMemberId . "'");
        $userDetailsArr['register_driver_' . $iMemberId] = $db_usr;
    }
    //Added By HJ On 22-07-2020 For Optimize register_driver Table Query End
    $iCompanyId = $db_usr[0]['iCompanyId'];
    if ($APP_TYPE == 'UberX') {
        $sql = "SELECT *,eChildSeatAvailable AS eChildAccessibility,eWheelChairAvailable AS eWheelChairAccessibility FROM driver_vehicle where iCompanyId = '" . $iCompanyId . "' and iDriverId = '" . $iMemberId . "' and eStatus != 'Deleted'";
        $db_vehicle = $obj->MySQLSelect($sql);
    } else {
        $sql = "SELECT m.vTitle, mk.vMake,dv.*,eChildSeatAvailable AS eChildAccessibility,eWheelChairAvailable AS eWheelChairAccessibility,case WHEN (dv.vInsurance='' OR dv.vPermit='' OR dv.vRegisteration='') THEN 'TRUE' ELSE 'FALSE' END as 'VEHICLE_DOCUMENT' FROM driver_vehicle as dv JOIN model m ON dv.iModelId=m.iModelId JOIN make mk ON dv.iMakeId=mk.iMakeId where iCompanyId = '" . $iCompanyId . "' and iDriverId = '" . $iMemberId . "' and dv.eStatus != 'Deleted' $ssql Order By dv.iDriverVehicleId desc";
        $db_vehicle = $obj->MySQLSelect($sql);
        $db_vehicle_new = $db_vehicle;
        if (count($db_vehicle) > 0) {
            $getVehicleType = $obj->MySQLSelect("SELECT iVehicleTypeId,eType FROM vehicle_type");
            for ($i = 0; $i < count($db_vehicle); $i++) {
                $vCarType = $db_vehicle[$i]['vCarType'];
                //Added By HJ On 22-07-2020 For Optimize vehicle_type Table Query Start
                $db_cartype = array();
                $explodeType = explode(",", $vCarType);
                for ($h = 0; $h < count($getVehicleType); $h++) {
                    if (in_array($getVehicleType[$h]['iVehicleTypeId'], $explodeType)) {
                        $db_cartype[] = $getVehicleType[$h];
                    }
                }
                //$db_cartype = $obj->MySQLSelect("SELECT iVehicleTypeId,eType  FROM `vehicle_type` WHERE `iVehicleTypeId` IN ($vCarType)");
                //Added By HJ On 22-07-2020 For Optimize vehicle_type Table Query End
                $k = 0;
                if (count($db_cartype) > 0) {
                    for ($j = 0; $j < count($db_cartype); $j++) {
                        $eType = $db_cartype[$j]['eType'];
                        if ($eType == "UberX") {
                        }
                    }
                }
            }
        }
    }
    $db_vehicle_new = array_values($db_vehicle_new);
    if (count($db_vehicle_new) > 0) {
        $returnArr['Action'] = "1";
        $returnArr['message'] = $db_vehicle_new;
    } else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_NO_VEHICLES_FOUND";
    }
    setDataResponse($returnArr);
}

if ($type == "UpdateUserVehicleDetails") {
    //global $tconfig; // Commented By HJ On 15-07-2020 Bcoz Not Required
    $iUserVehicleId = isset($_REQUEST['iUserVehicleId']) ? $_REQUEST['iUserVehicleId'] : '';
    $iUserId = isset($_REQUEST["iUserId"]) ? $_REQUEST["iUserId"] : '';
    $iMakeId = isset($_REQUEST["iMakeId"]) ? $_REQUEST["iMakeId"] : '';
    $iModelId = isset($_REQUEST["iModelId"]) ? $_REQUEST["iModelId"] : '';
    $iYear = isset($_REQUEST["iYear"]) ? $_REQUEST["iYear"] : '';
    $vLicencePlate = isset($_REQUEST["vLicencePlate"]) ? $_REQUEST["vLicencePlate"] : '';
    $vColour = isset($_REQUEST["vColour"]) ? $_REQUEST["vColour"] : '';
    $eStatus = isset($_REQUEST["eStatus"]) ? $_REQUEST["eStatus"] : 'Inactive';
    //$vImage = isset($_REQUEST["vImage"]) ? $_REQUEST["vImage"] : '';
    $image_name = $vImage = isset($_FILES['vImage']['name']) ? $_FILES['vImage']['name'] : '';
    $image_object = isset($_FILES['vImage']['tmp_name']) ? $_FILES['vImage']['tmp_name'] : '';
    $Photo_Gallery_folder = $tconfig['tsite_upload_images_passenger_vehicle'] . "/" . $iUserVehicleId . "/"; // /webimages/upload/uservehicle
    if (!is_dir($Photo_Gallery_folder)) {
        mkdir($Photo_Gallery_folder, 0777);
        chmod($Photo_Gallery_folder, 0777);
    }
    $action = ($iUserVehicleId != '') ? 'Edit' : 'Add';
    $Data_User_Vehicle['iUserId'] = $iUserId;
    $Data_User_Vehicle['iMakeId'] = $iMakeId;
    $Data_User_Vehicle['iModelId'] = $iModelId;
    $Data_User_Vehicle['iYear'] = $iYear;
    $Data_User_Vehicle['vLicencePlate'] = $vLicencePlate;
    $Data_User_Vehicle['eStatus'] = $eStatus;
    $Data_User_Vehicle['vColour'] = $vColour;
    //$Data_User_Vehicle['vImage']=$vImage;
    if ($action == "Add") {
        $id = $obj->MySQLQueryPerform("user_vehicle", $Data_User_Vehicle, 'insert');
        $updateimageid = $id;
    } else {
        $where = " iUserVehicleId = '" . $iUserVehicleId . "'";
        $updateimageid = $iUserVehicleId;
        $id = $obj->MySQLQueryPerform("user_vehicle", $Data_User_Vehicle, 'update', $where);
    }
    if ($image_name != "") {
        $vFile = $UPLOAD_OBJ->GeneralFileUpload($Photo_Gallery_folder, $image_object, $image_name, $prefix = '', $vaildExt = "pdf,doc,docx,jpg,jpeg,gif,png");
        $vImageName = $vFile[0];
        $Data_passenger["vImage"] = $vImageName;
        $where_image = " iUserVehicleId = '" . $updateimageid . "'";
        $id = $obj->MySQLQueryPerform("user_vehicle", $Data_passenger, 'update', $where_image);
    }
    if ($id > 0) {
        $returnArr['Action'] = "1";
        $returnArr['message'] = getPassengerDetailInfo($iUserId, "", "");
    } else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
    }
    setDataResponse($returnArr);
}

if ($type == "displayuservehicles") {
    //global $tconfig; // Commented By HJ On 15-07-2020 Bcoz Not Required
    $iUserId = isset($_REQUEST['iUserId']) ? clean($_REQUEST['iUserId']) : '';
    $sql = "SELECT m.vTitle, mk.vMake,uv.*  FROM user_vehicle as uv JOIN model m ON uv.iModelId=m.iModelId JOIN make mk ON uv.iMakeId=mk.iMakeId where iUserId = '" . $iUserId . "' and uv.eStatus != 'Deleted'";
    $db_vehicle = $obj->MySQLSelect($sql);
    if (count($db_vehicle) > 0) {
        $returnArr['Action'] = "1";
        $returnArr['message'] = $db_vehicle;
    } else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "No Vehicles Found";
    }
    setDataResponse($returnArr);
}

// ############################# Get DriverDetail ###################################
if ($type == "getDriverDetail") {
    $Did = isset($_REQUEST["DriverAutoId"]) ? $_REQUEST["DriverAutoId"] : '';
    $GCMID = isset($_REQUEST["GCMID"]) ? $_REQUEST["GCMID"] : '';
    $sql = "SELECT iGcmRegId FROM `register_driver` WHERE iDriverId='$Did'";
    $Data = $obj->MySQLSelect($sql);
    if (count($Data) > 0) {
        $iGCMregID = $Data[0]['iGcmRegId'];
        if ($GCMID != '') {
            if ($iGCMregID != $GCMID) {
                $where = " iDriverId = '$Did' ";
                $Data_update_driver['iGcmRegId'] = $GCMID;
                $id = $obj->MySQLQueryPerform("register_driver", $Data_update_driver, 'update', $where);
            }
        }
    }
    setDataResponse(getDriverDetailInfo($Did));
}

if ($type == "UpdateUserAddressDetails") {
    $iUserAddressId = isset($_REQUEST['iUserAddressId']) ? $_REQUEST['iUserAddressId'] : '';
    $iUserId = isset($_REQUEST["iUserId"]) ? $_REQUEST["iUserId"] : '';
    $iCompanyId = isset($_REQUEST["iCompanyId"]) ? $_REQUEST["iCompanyId"] : '';
    $eUserType = isset($_REQUEST["eUserType"]) ? $_REQUEST["eUserType"] : 'Passenger';
    $vServiceAddress = isset($_REQUEST["vServiceAddress"]) ? $_REQUEST["vServiceAddress"] : '';
    $vBuildingNo = isset($_REQUEST["vBuildingNo"]) ? $_REQUEST["vBuildingNo"] : '';
    $vLandmark = isset($_REQUEST["vLandmark"]) ? $_REQUEST["vLandmark"] : '';
    $vAddressType = isset($_REQUEST["vAddressType"]) ? $_REQUEST["vAddressType"] : '';
    $vLatitude = isset($_REQUEST["vLatitude"]) ? $_REQUEST["vLatitude"] : '';
    $vLongitude = isset($_REQUEST["vLongitude"]) ? $_REQUEST["vLongitude"] : '';
    $vTimeZone = isset($_REQUEST["vTimeZone"]) ? $_REQUEST["vTimeZone"] : '';
    $eStatus = isset($_REQUEST["eStatus"]) ? $_REQUEST["eStatus"] : 'Active';
    $iSelectVehicalId = isset($_REQUEST["iSelectVehicalId"]) ? $_REQUEST["iSelectVehicalId"] : '';
    $eCatType = isset($_REQUEST["eCatType"]) ? $_REQUEST["eCatType"] : '';
    $IsProceed = "Yes";
    if ($iSelectVehicalId == "" || $iSelectVehicalId == NULL) {
        $IsProceed = "Yes";
    }
    if ($iSelectVehicalId != "") {
        $pickuplocationarr = array(
            $vLatitude, $vLongitude
        );
        // $allowed_ans = checkRestrictedAreaNew($pickuplocationarr,"No");
        $allowed_ans = checkAreaRestriction($pickuplocationarr, "No");
        if ($allowed_ans == "Yes") {
            $GetVehicleIdfromGeoLocation = FetchVehicleTypeFromGeoLocation($pickuplocationarr);
            $sql23 = "SELECT iVehicleTypeId FROM `vehicle_type` WHERE iLocationid IN ($GetVehicleIdfromGeoLocation) ORDER BY iVehicleTypeId ASC";
            $vehicleTypes = $obj->MySQLSelect($sql23);
            $Vehicle_Str = "";
            if (count($vehicleTypes) > 0) {
                for ($i = 0; $i < count($vehicleTypes); $i++) {
                    $Vehicle_Str .= $vehicleTypes[$i]['iVehicleTypeId'] . ",";
                }
                //$Vehicle_Str = substr($Vehicle_Str, 0, -1);
                $Vehicle_Str = trim($Vehicle_Str, ",");
            }
            $Vehicle_Str_Arr = explode(",", $Vehicle_Str);
            if (in_array($iSelectVehicalId, $Vehicle_Str_Arr)) {
                $IsProceed = "Yes";
            }
            else {
                $IsProceed = "No";
            }
        }
        else {
            $IsProceed = "No";
        }
    }
    if ($eUserType == "Passenger") {
        $UserType = "Rider";
    }
    else {
        $UserType = "Driver";
    }
    $dAddedDate = @date("Y-m-d H:i:s");
    $action = ($iUserAddressId != '') ? 'Edit' : 'Add';
    // # Checking Distance Between Company and User Address ##
    $sql = "select vRestuarantLocationLat,vRestuarantLocationLong from `company` where iCompanyId = '" . $iCompanyId . "'";
    $db_companydata = $obj->MySQLSelect($sql);
    $vRestuarantLocationLat = $db_companydata[0]['vRestuarantLocationLat'];
    $vRestuarantLocationLong = $db_companydata[0]['vRestuarantLocationLong'];
    $distance = distanceByLocation($vLatitude, $vLongitude, $vRestuarantLocationLat, $vRestuarantLocationLong, "K");
    if ($distance > $LIST_RESTAURANT_LIMIT_BY_DISTANCE && !in_array($eCatType, ['Genie', 'Runner', 'Anywhere'])) {
        $returnArr['Action'] = "0";
        $returnArr["message"] = "LBL_LOCATION_FAR_AWAY_TXT";
        setDataResponse($returnArr);
    }
    // # Checking Distance Between Company and User Address ##
    $Data_User_Address['iUserId'] = $iUserId;
    $Data_User_Address['eUserType'] = $UserType;
    $Data_User_Address['vServiceAddress'] = $vServiceAddress;
    $Data_User_Address['vBuildingNo'] = $vBuildingNo;
    $Data_User_Address['vLandmark'] = $vLandmark;
    $Data_User_Address['vAddressType'] = $vAddressType;
    $Data_User_Address['vLatitude'] = $vLatitude;
    $Data_User_Address['vLongitude'] = $vLongitude;
    $Data_User_Address['dAddedDate'] = $dAddedDate;
    $Data_User_Address['vTimeZone'] = $vTimeZone;
    $Data_User_Address['eStatus'] = $eStatus;
    if ($action == "Add") {
        $insertid = $obj->MySQLQueryPerform("user_address", $Data_User_Address, 'insert');
        $AddressId = $insertid;
    }
    else {
        $where = " iUserAddressId = '" . $iUserAddressId . "'";
        $insertid = $obj->MySQLQueryPerform("user_address", $Data_User_Address, 'update', $where);
        $AddressId = $iUserAddressId;
    }
    if ($insertid > 0) {
        $returnArr['Action'] = "1";
        $returnArr['AddressId'] = $insertid;
        $returnArr['message1'] = "LBL_ADDRSS_ADD_SUCCESS";
        $returnArr['IsProceed'] = $IsProceed;
        if ($eUserType == "Passenger") {
            $returnArr['message'] = getPassengerDetailInfo($iUserId, "", "");
        }
        else {
            $returnArr['message'] = getDriverDetailInfo($iUserId);
        }
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
    }
    setDataResponse($returnArr);
}

if ($type == "DeleteUserAddressDetail") {
    $iUserAddressId = isset($_REQUEST['iUserAddressId']) ? $_REQUEST['iUserAddressId'] : '';
    $iUserId = isset($_REQUEST["iUserId"]) ? $_REQUEST["iUserId"] : '';
    $eUserType = isset($_REQUEST["eUserType"]) ? $_REQUEST["eUserType"] : 'Passenger';
    /* Food App Param */
    $passengerLat = isset($_REQUEST["PassengerLat"]) ? $_REQUEST["PassengerLat"] : '';
    $passengerLon = isset($_REQUEST["PassengerLon"]) ? $_REQUEST["PassengerLon"] : '';
    /* Food App Param */
    if ($eUserType == "Passenger") {
        $UserType = "Rider";
    } else {
        $UserType = "Driver";
    }
    $sql = "Update user_address set eStatus = 'Deleted' WHERE `iUserAddressId`='" . $iUserAddressId . "' AND `iUserId`='" . $iUserId . "' AND eUserType = '" . $UserType . "'";
    $id = $obj->MySQLSelect($sql);
    if ($id > 0) {
        $returnArr['Action'] = "1";
        $returnArr['message1'] = "LBL_USER_ADDRESS_DELETED_TXT";
        if ($eUserType == "Passenger") {
            $returnArr['message'] = getPassengerDetailInfo($iUserId, "", "");
            if ($passengerLat != "" && $passengerLon != "") {
                $returnArr['ToTalAddress'] = FetchTotalMemberAddress($iUserId, "Passenger", $passengerLat, $passengerLon, 0);
            }
        } else {
            $returnArr['message'] = getDriverDetailInfo($iUserId);
            if ($passengerLat != "" && $passengerLon != "") {
                $returnArr['ToTalAddress'] = FetchTotalMemberAddress($iUserId, "Driver", $passengerLat, $passengerLon, 0);
            }
        }
    } else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
    }
    setDataResponse($returnArr);
}

if ($type == "Checkuseraddressrestriction") {
    $iUserId = isset($_REQUEST["iUserId"]) ? $_REQUEST["iUserId"] : '';
    $UserType = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : 'Passenger';
    $iUserAddressId = isset($_REQUEST["iUserAddressId"]) ? $_REQUEST["iUserAddressId"] : '';
    $iSelectVehicalId = isset($_REQUEST["iSelectVehicalId"]) ? $_REQUEST["iSelectVehicalId"] : '';
    $sql = "SELECT vLatitude,vLongitude FROM user_address WHERE iUserAddressId='" . $iUserAddressId . "'";
    $address_data = $obj->MySQLSelect($sql);
    if (count($address_data) > 0) {
        $StartLatitude = $address_data[0]['vLatitude'];
        $EndLongitude = $address_data[0]['vLongitude'];
        $pickuplocationarr = array($StartLatitude, $EndLongitude);
        //$allowed_ans = checkRestrictedAreaNew($pickuplocationarr,"No");
        $allowed_ans = checkAreaRestriction($pickuplocationarr, "No");
        if ($allowed_ans == "Yes") {
            $GetVehicleIdfromGeoLocation = FetchVehicleTypeFromGeoLocation($pickuplocationarr);
            $sql23 = "SELECT iVehicleTypeId FROM `vehicle_type` WHERE iLocationid IN ($GetVehicleIdfromGeoLocation) ORDER BY iVehicleTypeId ASC";
            $vehicleTypes = $obj->MySQLSelect($sql23);
            $Vehicle_Str = "";
            if (count($vehicleTypes) > 0) {
                for ($i = 0; $i < count($vehicleTypes); $i++) {
                    $Vehicle_Str .= $vehicleTypes[$i]['iVehicleTypeId'] . ",";
                }
                //$Vehicle_Str = substr($Vehicle_Str, 0, -1);
                $Vehicle_Str = trim($Vehicle_Str, ",");
            }
            $Vehicle_Str_Arr = explode(",", $Vehicle_Str);
            if (in_array($iSelectVehicalId, $Vehicle_Str_Arr)) {
                $returnArr['Action'] = "1";
            } else {
                $returnArr['Action'] = "0";
                $returnArr['message'] = "LBL_NO_SERVICES_AVAIL_FOR_JOB_LOC";
            }
        } else {
            $returnArr['Action'] = "0";
            $returnArr['message'] = "LBL_JOB_LOCATION_NOT_ALLOWED";
        }
    } else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_JOB_LOCATION_NOT_ALLOWED";
    }
    setDataResponse($returnArr);
}

if ($type == "UpdateUserFavouriteAddress") {
    $iUserFavAddressId = isset($_REQUEST['iUserFavAddressId']) ? $_REQUEST['iUserFavAddressId'] : '';
    $iUserId = isset($_REQUEST["iUserId"]) ? $_REQUEST["iUserId"] : '';
    $eUserType = isset($_REQUEST["eUserType"]) ? $_REQUEST["eUserType"] : 'Passenger'; // Passenger , Driver
    $vAddress = isset($_REQUEST["vAddress"]) ? $_REQUEST["vAddress"] : '';
    $vLatitude = isset($_REQUEST["vLatitude"]) ? $_REQUEST["vLatitude"] : '';
    $vLongitude = isset($_REQUEST["vLongitude"]) ? $_REQUEST["vLongitude"] : '';
    $eType = isset($_REQUEST["eType"]) ? $_REQUEST["eType"] : 'Home'; // Home,Work
    $vTimeZone = isset($_REQUEST["vTimeZone"]) ? $_REQUEST["vTimeZone"] : '';
    $eStatus = isset($_REQUEST["eStatus"]) ? $_REQUEST["eStatus"] : 'Active';
    $dAddedDate = @date("Y-m-d H:i:s");
    $action = ($iUserFavAddressId != '') ? 'Edit' : 'Add';
    $Data_User_Address['iUserId'] = $iUserId;
    $Data_User_Address['eUserType'] = $eUserType;
    $Data_User_Address['vAddress'] = $vAddress;
    $Data_User_Address['vLatitude'] = $vLatitude;
    $Data_User_Address['vLongitude'] = $vLongitude;
    $Data_User_Address['eType'] = $eType;
    $Data_User_Address['dAddedDate'] = $dAddedDate;
    $Data_User_Address['vTimeZone'] = $vTimeZone;
    $Data_User_Address['eStatus'] = $eStatus;
    if ($action == "Add") {
        $insertid = $obj->MySQLQueryPerform("user_fave_address", $Data_User_Address, 'insert');
        $AddressId = $insertid;
    } else {
        $where = " iUserFavAddressId = '" . $iUserFavAddressId . "'";
        $insertid = $obj->MySQLQueryPerform("user_fave_address", $Data_User_Address, 'update', $where);
        $AddressId = $iUserAddressId;
    }
    if ($insertid > 0) {
        $returnArr['Action'] = "1";
        $returnArr['AddressId'] = $insertid;
        $returnArr['message1'] = "LBL_ADDRSS_ADD_SUCCESS";
        if ($eUserType == "Passenger") {
            $returnArr['message'] = getPassengerDetailInfo($iUserId, "", "");
        } else {
            $returnArr['message'] = getDriverDetailInfo($iUserId);
        }
    } else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
    }
    setDataResponse($returnArr);
}


if ($type == "DeleteUserFavouriteAddress") {
    $iUserFavAddressId = isset($_REQUEST['iUserFavAddressId']) ? $_REQUEST['iUserFavAddressId'] : '';
    $iUserId = isset($_REQUEST["iUserId"]) ? $_REQUEST["iUserId"] : '';
    $eUserType = isset($_REQUEST["eUserType"]) ? $_REQUEST["eUserType"] : 'Passenger';
    $sql = "DELETE FROM user_fave_address WHERE `iUserFavAddressId`='" . $iUserFavAddressId . "'";
    $id = $obj->MySQLSelect($sql);
    if ($id > 0) {
        $returnArr['Action'] = "1";
        $returnArr['message1'] = "LBL_USER_ADDRESS_DELETED_TXT";
        if ($eUserType == "Passenger") {
            $returnArr['message'] = getPassengerDetailInfo($iUserId, "", "");
        } else {
            $returnArr['message'] = getDriverDetailInfo($iUserId);
        }
    } else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
    }
    setDataResponse($returnArr);
}

if ($type == "fetchAPIDetails") {
    fetchAPIDetails();
}

#################### Add by HV on 31-08-2020 for Genie Checkout Estimate Details #######################
if ($type == "CheckOutOrderEstimateDetailsGenie") {
    $iUserId = isset($_REQUEST["iUserId"]) ? $_REQUEST["iUserId"] : $_REQUEST["GeneralMemberId"];
    $iStoreName = isset($_REQUEST["iStoreName"]) ? $_REQUEST["iStoreName"] : '';
    $iStorelatitude = isset($_REQUEST["iStorelatitude"]) ? $_REQUEST["iStorelatitude"] : '';
    $iStorelongitude = isset($_REQUEST["iStorelongitude"]) ? $_REQUEST["iStorelongitude"] : '';
    $iStoreAddress = isset($_REQUEST["iStoreAddress"]) ? $_REQUEST["iStoreAddress"] : '';
    $iUserAddressId = isset($_REQUEST["iUserAddressId"]) ? $_REQUEST["iUserAddressId"] : '';
    $couponCode = isset($_REQUEST["vCouponCode"]) ? $_REQUEST["vCouponCode"] : '';
    $OrderDetails = isset($_REQUEST["OrderDetails"]) ? $_REQUEST["OrderDetails"] : '';
    $passengerLat = isset($_REQUEST["PassengerLat"]) ? $_REQUEST["PassengerLat"] : '';
    $passengerLon = isset($_REQUEST["PassengerLon"]) ? $_REQUEST["PassengerLon"] : '';
    $CheckUserWallet = isset($_REQUEST["CheckUserWallet"]) ? $_REQUEST["CheckUserWallet"] : 'No';
    $vGeneralCurrency = isset($_REQUEST["vGeneralCurrency"]) ? $_REQUEST["vGeneralCurrency"] : '';
    $vLang = isset($_REQUEST["vGeneralLang"]) ? $_REQUEST["vGeneralLang"] : 'EN';
    $IS_FOR_PICK_DROP_GENIE = isset($_REQUEST["IS_FOR_PICK_DROP_GENIE"]) ? $_REQUEST["IS_FOR_PICK_DROP_GENIE"] : 'No';
    $isAddOutstandingAmt = isset($_REQUEST["isAddOutstandingAmt"]) ? $_REQUEST["isAddOutstandingAmt"] : 'No';

    $userAddressData = get_value('user_address', 'vLatitude, vLongitude', 'iUserAddressId', $iUserAddressId);
    $userAddressLatitude = $userAddressData[0]['vLatitude'];
    $userAddressLongitude = $userAddressData[0]['vLongitude'];
    $vLangCodeData = get_value('language_master', 'vCode, vGMapLangCode', 'eDefault', 'Yes');
    $vGMapLangCode = $vLangCodeData[0]['vGMapLangCode'];
    $requestDataArr = array();
    $requestDataArr['SOURCE_LATITUDE'] = $iStorelatitude;
    $requestDataArr['SOURCE_LONGITUDE'] = $iStorelongitude;
    $requestDataArr['DEST_LATITUDE'] = $userAddressLatitude;
    $requestDataArr['DEST_LONGITUDE'] = $userAddressLongitude;
    $requestDataArr['LANGUAGE_CODE'] = $vGMapLangCode;
    $direction_data = getPathInfoBetweenLocations($requestDataArr);
    $distance = $direction_data['distance'] / 1000;
    if ($distance > $LIST_RESTAURANT_LIMIT_BY_DISTANCE) {
        $returnArr['Action'] = "0";
        $returnArr["message"] = "LBL_LOCATION_FAR_AWAY_TXT";
        setDataResponse($returnArr);
    }
    $User_Address_Array = array($userAddressLatitude, $userAddressLongitude);

    $iLocationId = GetUserGeoLocationId($User_Address_Array);

    $checkAllLocation = 1;
    if (count($iLocationId) > 0) {

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
        $fDeliveryCharge = $data_location[0]['fDeliveryChargeBuyAnyService'];
    }
    $currencySymbol = "";
    $currencycode = $vGeneralCurrency;
    if ($iUserId > 0) {
        $UserDetailsArr = getUserCurrencyLanguageDetails($iUserId);
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
        $vLang = $LANG_OBJ->FetchDefaultLangData("vCode");
    }
    $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLang, "1", $iServiceId);
    $fDeliveryCharge = setTwoDecimalPoint($fDeliveryCharge * $Ratio);
    $fTotalGenerateFare = $fDeliveryCharge;
    $fOutStandingAmount = 0;
    $fOutStandingAmount = GetPassengerOutstandingAmount($iUserId);
    $fOutStandingAmount = setTwoDecimalPoint($fOutStandingAmount * $Ratio);
    
    if(strtoupper($isAddOutstandingAmt) == "NO") {
        $fOutStandingAmount = 0;
    }

    $returnArr['Action'] = "1";
    $returnArr['fDeliveryCharge'] = $fDeliveryCharge;
    $returnArr['distance'] = $distance;
    $returnArr['fStoreBill'] = "--";
    $OrderFareDetailsArr = array();
    $OrderFareDetailsArr[][$languageLabelsArr['LBL_DELIVERY_CHARGES_TXT']] = formateNumAsPerCurrency($fDeliveryCharge, $currencycode);
    if ($fOutStandingAmount > 0) {
        $OrderFareDetailsArr[][$languageLabelsArr['LBL_OUTSTANDING_AMOUNT_TXT']] = formateNumAsPerCurrency($fOutStandingAmount, $currencycode);
    }
    if ($IS_FOR_PICK_DROP_GENIE == "No") {
        $OrderFareDetailsArr[][$languageLabelsArr['LBL_STORE_BILL_TXT']] = "--";
    }
    $returnArr['FareDetailsArr'] = $OrderFareDetailsArr;
    if (!empty($iUserId)) {
        $returnArr['message'] = getPassengerDetailInfo($iUserId, "", ""); // Added By HJ On 08-11-2019 As Per Dicuss WIth KS and DT
    }
    setDataResponse($returnArr);
}
################ Add by HV on 31-08-2020 for Genie Checkout Estimate Details End ###################

//added by SP on 22-02-2021 as per DT told for checking app terminate or not
if ($type == "CheckAppTerminate") {
    $iMemberId = isset($_REQUEST['iMemberId']) ? $_REQUEST['iMemberId'] : '';
    $MemberType = isset($_REQUEST['GeneralUserType']) ? $_REQUEST['GeneralUserType'] : '';
    $eAppTerminate = isset($_REQUEST['eAppTerminate']) ? $_REQUEST['eAppTerminate'] : 'No';
    UpdateAppTerminateStatus($iMemberId, $MemberType, $eAppTerminate);
    $returnArr['Action'] = "1";
    $returnArr['message'] = "LBL_Record_Updated_successfully";
    setDataResponse($returnArr);
}

// Added by HV on 20-04-2021 for New Payment Flow #############################
if ($type == "GetProfilePaymentDetails") {
    $iMemberId = isset($_REQUEST['GeneralMemberId']) ? clean($_REQUEST['GeneralMemberId']) : '';
    $eUserType = isset($_REQUEST['GeneralUserType']) ? clean($_REQUEST['GeneralUserType']) : '';
    $vLang = isset($_REQUEST['vGeneralLang']) ? clean($_REQUEST['vGeneralLang']) : '';
    $eType = isset($_REQUEST['eType']) ? clean($_REQUEST['eType']) : '';
    $isContactlessDelivery = isset($_REQUEST['isContactlessDelivery']) ? clean($_REQUEST['isContactlessDelivery']) : 'No';
    $eTakeAway = isset($_REQUEST['eTakeAway']) ? clean($_REQUEST['eTakeAway']) : 'No';
    if ($iMemberId != "" && $eUserType != "") {
        $params = array(
            "iMemberId" => $iMemberId, "eUserType" => $eUserType, "eType" => $eType, "vLang" => $vLang, "isContactlessDelivery" => $isContactlessDelivery, "eTakeAway" => $eTakeAway
        );
        GetPaymentModeDetails($params);
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
        setDataResponse($returnArr);
    }
}

if ($type == "getTwilioAccessToken") {
    $roomName = isset($_REQUEST["roomName"]) ? $_REQUEST["roomName"] : '';
    $identity = time();
    $token = new Twilio\Jwt\AccessToken($TWILIO_ACCOUNT_SID, $TWILIO_API_KEY, $TWILIO_API_SECRET, 6000, $identity);
    if (empty($roomName)) {
        $roomName = "Room_" . time();
    }
    // Grant access to Video
    $grant = new Twilio\Jwt\Grants\VideoGrant();
    $grant->setRoom($roomName);
    $token->addGrant($grant);
    $tokenPassed = $token->toJWT();
    $tokenData = array();
    $tokenData['token'] = $tokenPassed;
    $tokenData['roomName'] = $roomName;
    if (!empty($tokenPassed)) {
        $returnArr['Action'] = "1";
        $returnArr['message'] = $tokenData;
        setDataResponse($returnArr);
    } else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
        setDataResponse($returnArr);
    }
    $returnArr['Action'] = "0";
    $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
    setDataResponse($returnArr);
}

if ($type == "sendNotificationForTwilio") {
    $fromMemberId = isset($_REQUEST["fromMemberId"]) ? $_REQUEST["fromMemberId"] : '';
    $toMemberId = isset($_REQUEST["toMemberId"]) ? $_REQUEST["toMemberId"] : '';
    $fromMemberType = isset($_REQUEST["fromMemberType"]) ? $_REQUEST["fromMemberType"] : '';
    $toMemberType = isset($_REQUEST["toMemberType"]) ? $_REQUEST["toMemberType"] : '';
    $isDecline = isset($_REQUEST["isDecline"]) ? $_REQUEST["isDecline"] : 'No';
    $roomName = isset($_REQUEST["roomName"]) ? $_REQUEST["roomName"] : '';
    $iTripId = isset($_REQUEST["iTripId"]) ? $_REQUEST["iTripId"] : '';
    $isVideoCall = isset($_REQUEST["isVideoCall"]) ? $_REQUEST["isVideoCall"] : '';
    $dbData = array();
    $commonField = "iGcmRegId,eDeviceType,eAppTerminate,eDebugMode,eHmsDevice";
    if ($fromMemberType == "Passenger") {
        $fromTblName = "register_user";
        $fromCondField = "iUserId";
        $fromGetField = "iUserId as iMemberId,CONCAT(vName,' ',vLastName) as vName,vImgName as vImage";
    }
    else if ($fromMemberType == "Driver") {
        $fromTblName = "register_driver";
        $fromCondField = "iDriverId";
        $fromGetField = "iDriverId as iMemberId,CONCAT(vName,' ',vLastName) as vName,vImage";
    }
    else if ($fromMemberType == "Company") {
        $fromTblName = "company";
        $fromCondField = "iCompanyId";
        $fromGetField = "iCompanyId as iMemberId,vCompany as vName,vImage";
    }
    if ($toMemberType == "Passenger") {
        $toTblName = "register_user";
        $toCondField = "iUserId";
        $toGetField = "iUserId as iMemberId,CONCAT(vName,' ',vLastName) as vName,vImgName as vImage";
        $channelName = "PASSENGER_" . $toMemberId;
        $sendTitle = RN_USER;
    }
    else if ($toMemberType == "Driver") {
        $toTblName = "register_driver";
        $toCondField = "iDriverId";
        $toGetField = "iDriverId as iMemberId,CONCAT(vName,' ',vLastName) as vName,vImage";
        $channelName = "DRIVER_" . $toMemberId;
        $sendTitle = RN_PROVIDER;
    }
    else if ($toMemberType == "Company") {
        $toTblName = "company";
        $toCondField = "iCompanyId";
        $toGetField = "iCompanyId as iMemberId,vCompany as vName,vImage";
        $channelName = "COMPANY_" . $toMemberId;
        $sendTitle = RN_COMPANY;
    }
    $fromMemberData = $obj->MySQLSelect("SELECT $commonField,$fromGetField FROM " . $fromTblName . " WHERE $fromCondField ='" . $fromMemberId . "'");
    $toMemberData = $obj->MySQLSelect("SELECT $commonField,$toGetField FROM " . $toTblName . " WHERE $toCondField ='" . $toMemberId . "'");
    $message_arr = array();
    if ($isDecline == 'Yes') {
        $alertMsg = "TwilioVideocall";
        $message_arr['Message'] = $alertMsg;
        $message_arr['MsgType'] = $alertMsg;
        $message_arr['vTitle'] = $alertMsg;
        $message_arr['eSystem'] = "DeliverAll";
        $message_arr['tRandomCode'] = time();
        $message_arr['fromMemberId'] = $fromMemberId;
        $message_arr['fromMemberType'] = $fromMemberType;
        $message_arr['roomName'] = $roomName;
        $message_arr['isDecline'] = $isDecline;
        $generalDataArr[] = array(
            'eDeviceType' => $toMemberData[0]['eDeviceType'], 'deviceToken' => $toMemberData[0]['iGcmRegId'], 'alertMsg' => $alertMsg, 'eAppTerminate' => $toMemberData[0]['eAppTerminate'], 'eDebugMode' => $toMemberData[0]['eDebugMode'], 'eHmsDevice' => $toMemberData[0]['eHmsDevice'], 'message' => $message_arr, 'channelName' => $channelName
        );
        $EVENT_MSG_OBJ->send(array('GENERAL_DATA' => $generalDataArr), $sendTitle);
    }
    else {
        if (empty($isVideoCall)) {
            $returnArr['Action'] = "1";
            $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
            setDataResponse($returnArr);
        }
        $message_arr['Message'] = "TwilioVideocall";
        $message_arr['MsgType'] = "TwilioVideocall";
        $alertMsg = "TwilioVideocall";
        $message_arr['vTitle'] = $alertMsg;
        $message_arr['eSystem'] = "DeliverAll";
        $message_arr['tRandomCode'] = time();
        $message_arr['fromMemberId'] = $fromMemberId;
        $message_arr['fromMemberType'] = $fromMemberType;
        $message_arr['toMemberId'] = $toMemberId;
        $message_arr['toMemberType'] = $toMemberType;
        $message_arr['Name'] = $fromMemberData[0]['vName'];
        $message_arr['PImage'] = $fromMemberData[0]['vImage'];
        $message_arr['roomName'] = $roomName;
        $message_arr['iTripId'] = $iTripId;
        $message_arr['isVideoCall'] = $isVideoCall;
        $generalDataArr[] = array(
            'eDeviceType' => $toMemberData[0]['eDeviceType'], 'deviceToken' => $toMemberData[0]['iGcmRegId'], 'alertMsg' => $alertMsg, 'eAppTerminate' => $toMemberData[0]['eAppTerminate'], 'eDebugMode' => $toMemberData[0]['eDebugMode'], 'eHmsDevice' => $toMemberData[0]['eHmsDevice'], 'message' => $message_arr, 'channelName' => $channelName
        );

        $EVENT_MSG_OBJ->send(array('GENERAL_DATA' => $generalDataArr), $sendTitle);
    }
    $returnArr['Action'] = "1";
    $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
    setDataResponse($returnArr);
}

if ($type == "sendAuthOtp") {
    $mobileNo = isset($_REQUEST['MobileNo']) ? clean($_REQUEST['MobileNo']) : '';
    $vPhoneCode = isset($_REQUEST['vPhoneCode']) ? clean($_REQUEST['vPhoneCode']) : '';
    $userType = isset($_REQUEST['UserType']) ? clean($_REQUEST['UserType']) : 'Passenger';
    $vLangCode = isset($_REQUEST['vGeneralLang']) ? clean($_REQUEST['vGeneralLang']) : '';
    $SendRequestWeb = isset($_REQUEST['SendRequestWeb']) ? clean($_REQUEST['SendRequestWeb']) : 'No';
    if ($vLangCode == "" || $vLangCode == NULL) {
        $vLangCode = $LANG_OBJ->FetchDefaultLangData("vCode");
    }
    $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLangCode, "1", "");
    $otp = mt_rand(1000, 9999);
    //$str = "SELECT * from send_message_templates where vEmail_Code = 'AUTH_OTP'";
    //$res = $obj->MySQLSelect($str);
    //$prefix = $res[0]['vBody_' . $vLangCode];
    //$message = str_replace(["#OTP#", "#SITE_NAME#"], [$otp, $SITE_NAME], $prefix);
    $SMSdata = array();
    $SMSdata['OTP'] = $otp;
    $message = $COMM_MEDIA_OBJ->GetSMSTemplate("AUTH_OTP", $SMSdata, "", $vLangCode);
    $toMobileNum = "+" . $vPhoneCode . $mobileNo;
    /********************** Firebase SMS Verfication **********************************/
    $returnArr['MOBILE_NO_VERIFICATION_METHOD'] = $MOBILE_NO_VERIFICATION_METHOD;
    /********************** Firebase SMS Verfication **********************************/
    if (strtoupper($MOBILE_NO_VERIFICATION_METHOD) != "FIREBASE") {
        $result = $COMM_MEDIA_OBJ->SendSystemSMS($toMobileNum, $PhoneCode, $message);
    } else {
        $result = 1;
    }

    if($SendRequestWeb == 'Yes'){
        if($userType == "Driver"){
            $obj->sql_query("UPDATE register_driver SET vOTP ='".$otp."' WHERE vPhone='" . $mobileNo . "'");
        } else if ($userType == "Company"){
            $obj->sql_query("UPDATE company SET vOTP ='".$otp."' WHERE vPhone='" . $mobileNo . "'");
        } else {
            $obj->sql_query("UPDATE register_user SET vOTP ='".$otp."' WHERE vPhone='" . $mobileNo . "'");
        }
    }

    if ($result == 0) {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_FAILED_SEND_AUTH_OTP";
        setDataResponse($returnArr);
    } else {
        $returnArr['Action'] = "1";
        if($SendRequestWeb == 'No'){
            $returnArr['message'] = $otp;
        }
        $returnArr['Phone'] = $toMobileNum;
        setDataResponse($returnArr);
    }
}

/* ------------------------- Account Deletion Start ------------------------- */
if ($type == "AccountDelete") {
    $iMemberId = isset($_REQUEST["GeneralMemberId"]) ? $_REQUEST["GeneralMemberId"] : '';
    $UserType = isset($_REQUEST["GeneralUserType"]) ? $_REQUEST["GeneralUserType"] : 'Passenger';
    $DELETE_ACCOUNT_OBJ->accountDelete($iMemberId, $UserType);
}
if ($type == "SignInForAccountDelete") {
    $iMemberId = isset($_REQUEST["GeneralMemberId"]) ? $_REQUEST["GeneralMemberId"] : '';
    $UserType = isset($_REQUEST["GeneralUserType"]) ? $_REQUEST["GeneralUserType"] : '';
    $mobileNo = isset($_REQUEST['MobileNo']) ? clean($_REQUEST['MobileNo']) : '';
    $vPhoneCode = isset($_REQUEST['vPhoneCode']) ? clean($_REQUEST['vPhoneCode']) : '';
    $DELETE_ACCOUNT_OBJ->signIn($iMemberId, $UserType, $mobileNo, $vPhoneCode);
}
/* ------------------------- Account Deletion End ------------------------- */

/*------------------------- Gift Card Start -------------------------*/
if ($type == "SendGiftCard") {
    $GIFT_CARD_OBJ->SendGiftCard();
}
if ($type == "RedeemGiftCard") {
    $GIFT_CARD_OBJ->RedeemGiftCard();
}
if ($type == "PreviewGiftCard") {
    $GIFT_CARD_OBJ->PreviewGiftCard();
}
/*------------------------- Gift Card End -------------------------*/

if ($type == "AuthenticateMember") {
    $mobileNo = isset($_REQUEST['MobileNo']) ? clean($_REQUEST['MobileNo']) : '';
    $vPhoneCode = isset($_REQUEST['vPhoneCode']) ? clean($_REQUEST['vPhoneCode']) : '';
    $userType = isset($_REQUEST['UserType']) ? clean($_REQUEST['UserType']) : 'Passenger';
    $vLangCode = isset($_REQUEST['vGeneralLang']) ? clean($_REQUEST['vGeneralLang']) : '';
    $GeneralDeviceType = isset($_REQUEST['GeneralDeviceType']) ? clean($_REQUEST['GeneralDeviceType']) : '';
    $vPassword = isset($_REQUEST['vPassword']) ? clean($_REQUEST['vPassword']) : '';
    $GCMID = isset($_REQUEST["vDeviceToken"]) ? $_REQUEST["vDeviceToken"] : '';
    $vFirebaseDeviceToken = isset($_REQUEST["vFirebaseDeviceToken"]) ? $_REQUEST["vFirebaseDeviceToken"] : '';
    $CountryCode = isset($_REQUEST["CountryCode"]) ? $_REQUEST["CountryCode"] : '';
    $PhoneCode = isset($_REQUEST["PhoneCode"]) ? $_REQUEST["PhoneCode"] : '';
    $isNewPassword = isset($_REQUEST["isNewPassword"]) ? $_REQUEST["isNewPassword"] : '';
    $vCurrency = isset($_REQUEST["vCurrency"]) ? $_REQUEST["vCurrency"] : '';
    $GeneralAppVersion = isset($_REQUEST["GeneralAppVersion"]) ? $_REQUEST["GeneralAppVersion"] : '';
    
    if (empty($GCMID)) {
        $GCMID = $vFirebaseDeviceToken;
    }
    if ($vLangCode == "" || $vLangCode == NULL) {
        $vLangCode = $LANG_OBJ->FetchDefaultLangData("vCode");
    }
    $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLangCode, "1", "");
    if ($userType == "Driver") {
        $tblname = 'register_driver';
    } elseif ($userType == "Tracking") {
        $tblname = 'track_service_users';
    } else if ($userType == "Company") {
        $tblname = 'company';
    } else {
        $tblname = 'register_user';
    }
    $data = AllowphoneNumWithZero($userType, $mobileNo, 'vPhone', $tblname, $vPhoneCode);
    if ($userType == "Driver") {
        $checkMember = $obj->MySQLSelect("SELECT iDriverId as iMemberId, vPassword, eStatus, iTrackServiceCompanyId FROM register_driver WHERE ( vPhone = '$mobileNo' {$data} ) AND vCode = '$vPhoneCode'");
    } elseif ($userType == "Company") {
        $checkMember = $obj->MySQLSelect("SELECT iCompanyId as iMemberId, vPassword, eStatus, iServiceId FROM company WHERE vPhone = '$mobileNo' AND vCode = '$vPhoneCode' AND eSystem = 'DeliverAll'");
        $iServiceId = $checkMember[0]['iServiceId'];
    } elseif ($userType == "Tracking") {
        $checkMember = $obj->MySQLSelect("SELECT iTrackServiceUserId as iMemberId, vPassword, eStatus FROM track_service_users WHERE ( vPhone = '$mobileNo' {$data} ) AND vPhoneCode = '$vPhoneCode'");
    } else {
        $checkMember = $obj->MySQLSelect("SELECT iUserId as iMemberId, vPassword, eStatus FROM register_user WHERE ( vPhone = '$mobileNo' {$data} ) AND vPhoneCode = '$vPhoneCode'");
    }
    if (!empty($checkMember) && count($checkMember) > 0) {
        if (strtoupper($SIGN_IN_OPTION) == "PASSWORD") {
            $hash = $checkMember[0]['vPassword'];
            $checkValidPass = $AUTH_OBJ->VerifyPassword($vPassword, $hash);
            $checkValidPassNew = 0;
            if (!empty($isNewPassword) && strtoupper($isNewPassword) == "YES") {
                $new_password = encrypt_bycrypt($vPassword);
                if ($userType == "Driver") {
                    $obj->sql_query("UPDATE register_driver SET vPassword = '$new_password' WHERE iDriverId = '" . $checkMember[0]['iMemberId'] . "'");
                } elseif ($userType == "Tracking") {
                    $obj->sql_query("UPDATE track_service_users SET vPassword = '$new_password' WHERE iTrackServiceUserId = '" . $checkMember[0]['iMemberId'] . "'");
                } elseif ($userType == "Company") {
                    $obj->sql_query("UPDATE company SET vPassword = '$new_password' WHERE iCompanyId = '" . $checkMember[0]['iMemberId'] . "'");
                } else {
                    $obj->sql_query("UPDATE register_user SET vPassword = '$new_password' WHERE iUserId = '" . $checkMember[0]['iMemberId'] . "'");
                }
                $checkValidPassNew = 1;
            }
            if ($checkValidPass == 0 && $checkValidPassNew == 0) {
                $returnArr['Action'] = "0";
                $returnArr['message'] = "LBL_WRONG_DETAIL";
                setDataResponse($returnArr);
            }
        }
        if (strtoupper($checkMember[0]['eStatus']) == "ACTIVE" || (strtoupper($checkMember[0]['eStatus']) != "DELETED" && (($userType == "Driver" && $checkMember[0]['iTrackServiceCompanyId'] == 0) || $userType == "Company"))) {
            $Data['vLang'] = $vLangCode;
            $Data['iMemberId'] = $checkMember[0]['iMemberId'];
            $Data['UserType'] = $userType;
            $Data['eDeviceType'] = $GeneralDeviceType;
            $Data['GCMID'] = $GCMID;
            $Data['vCurrency'] = $vCurrency;
            getLoginData($Data);
        } else {
            $returnArr['Action'] = "0";
            $returnArr['RESTRICT_APP'] = "Yes";
            if ($checkMember[0]['eStatus'] != "Deleted") {
                $returnArr['message'] = "LBL_CONTACT_US_STATUS_NOTACTIVE_PASSENGER";
                if(SITE_TYPE == "Demo") {
                    $returnArr['message'] = "Your Account has been Inactivated. Please contact the Sales Team to re-activate it and to continue testing the System.";
                }
                $returnArr['message_title'] = "LBL_ACC_INACTIVE_TITLE";
                $returnArr['eStatus'] = $checkMember[0]['eStatus'];
                $returnArr['isAccountInactive'] = "Yes";
            } else {
                $returnArr['message'] = "LBL_ACC_DELETE_TXT";
                $returnArr['message_title'] = "LBL_ACC_DELETE_TITLE";
                $returnArr['eStatus'] = $checkMember[0]['eStatus'];
                $returnArr['isAccountDeleted'] = "Yes";
            }
            setDataResponse($returnArr);
        }
    } else {
        $returnArr['Action'] = '1';
        $returnArr['IS_REGISTERED'] = 'No';
        
        $demoTypeConfig = $RESTRICT_SIGNUP_ON_DEMO_ANDROID_VERSION_DRIVER_APP;
        if ($userType == "Passenger") {
            $demoTypeConfig = $RESTRICT_SIGNUP_ON_DEMO_ANDROID_VERSION;
        }
        if (strtoupper($GeneralDeviceType) == "IOS") {
            $demoTypeConfig = $RESTRICT_SIGNUP_ON_DEMO_IOS_VERSION_DRIVER_APP;
            if ($userType == "Passenger") {
                $demoTypeConfig = $RESTRICT_SIGNUP_ON_DEMO_IOS_VERSION;
            }
        }
        $displayAlert = 1;
        if ($demoTypeConfig == "-1" || $demoTypeConfig == $GeneralAppVersion) {
            $displayAlert = 0;
        }
        
        if (SITE_TYPE == 'Demo' && $displayAlert > 0) {
            $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLang, "1", $iServiceId);
            $returnArr['Action'] = "0";
            $returnArr['message'] = strip_tags($languageLabelsArr["LBL_SIGNUP_DEMO_CONTENT"]);
        }
        setDataResponse($returnArr);
    }
}

if ($type == "CheckMemberAccount") {
    $iMemberId = isset($_REQUEST["GeneralMemberId"]) ? $_REQUEST["GeneralMemberId"] : '';
    $UserType = isset($_REQUEST["GeneralUserType"]) ? $_REQUEST["GeneralUserType"] : '';
    $mobileNo = isset($_REQUEST['MobileNo']) ? clean($_REQUEST['MobileNo']) : '';
    $vPhoneCode = isset($_REQUEST['vPhoneCode']) ? clean($_REQUEST['vPhoneCode']) : '';
    $vEmail = isset($_REQUEST['vEmail']) ? $_REQUEST['vEmail'] : '';
    $vEmail = strtolower($vEmail);
    $ssql = "";
    if ($UserType == "Driver") {
        $tblname = "register_driver";
        $memberField = "iDriverId";
        $memberPhoneCode = "vCode";
    } elseif ($UserType == "Tracking") {
        $tblname = "track_service_users";
        $memberField = "iTrackServiceUserId";
        $memberPhoneCode = "vPhoneCode";
    } elseif ($UserType == "Company") {
        $tblname = "company";
        $memberField = "iCompanyId";
        $ssql = " AND eSystem = 'DeliverAll'";
        $memberPhoneCode = "vCode";
    } else {
        $tblname = "register_user";
        $memberField = "iUserId";
        $memberPhoneCode = "vPhoneCode";
    }
    if (!empty($vEmail)) {
        $check_member = $obj->MySQLSelect("SELECT $memberField as iMemberId,vEmail FROM $tblname WHERE 1=1 AND vEmail = '$vEmail'");
        if (count($check_member) > 0) {
            $returnArr['Action'] = "0";
            if ($vEmail == strtolower($check_member[0]['vEmail'])) {
                $returnArr['message'] = "LBL_ALREADY_REGISTERED_TXT";
                setDataResponse($returnArr);
            }
        } else {
            $returnArr['Action'] = "1";
            setDataResponse($returnArr);
        }
    }
    $data = AllowphoneNumWithZero($UserType, $mobileNo, 'vPhone', $tblname, $vPhoneCode);
    $checkMember = $obj->MySQLSelect("SELECT $memberField as iMemberId, vPassword, eStatus FROM $tblname WHERE ( vPhone = '$mobileNo' {$data} ) AND $memberPhoneCode = '$vPhoneCode'");
    $returnArr['Action'] = '1';
    $returnArr['showEnterPassword'] = 'Yes';
    if (!empty($checkMember) && count($checkMember) > 0) {
        if (empty($checkMember[0]['vPassword'])) {
            $returnArr['showEnterPassword'] = 'Yes';
        } else {
            $returnArr['showEnterPassword'] = 'No';
        }
    }
    setDataResponse($returnArr);
}

if ($type == "UpdateLiveActivityToken") {
    $GeneralMemberId = isset($_REQUEST['GeneralMemberId']) ? $_REQUEST['GeneralMemberId'] : '';
    $GeneralUserType = isset($_REQUEST['GeneralUserType']) ? $_REQUEST['GeneralUserType'] : '';
    $LiveActivityToken = isset($_REQUEST['LiveActivityToken']) ? $_REQUEST['LiveActivityToken'] : '';
    $iTripId = isset($_REQUEST['iTripId']) ? $_REQUEST['iTripId'] : '0';
    $iOrderId = isset($_REQUEST['iOrderId']) ? $_REQUEST['iOrderId'] : '0';

    if(!empty($LiveActivityToken)) {
        if(!empty($iTripId)) {
            $field = 'iTripId';
            $fieldVal = $iTripId;

        } elseif (!empty($iOrderId)) {
            $field = 'iOrderId';
            $fieldVal = $iOrderId;
        }

        $tokenData = $obj->MySQLSelect("SELECT iDeviceTokenId FROM live_activity_device_tokens WHERE iUserId = '$GeneralMemberId' AND $field = '$fieldVal' ");

        if(!empty($tokenData) && count($tokenData) > 0) {
            $obj->sql_query("UPDATE live_activity_device_tokens SET tDeviceLiveActivityToken = '$LiveActivityToken' WHERE iDeviceTokenId = '" . $tokenData[0]['iDeviceTokenId'] . "' ");    
        } else {
            $Data_insert = array();
            $Data_insert['iUserId'] = $GeneralMemberId;
            $Data_insert[$field] = $fieldVal;
            $Data_insert['tDeviceLiveActivityToken'] = $LiveActivityToken;
            $obj->MySQLQueryPerform("live_activity_device_tokens", $Data_insert, "insert");
        }
        
        $returnArr['Action'] = "1";
        setDataResponse($returnArr);
    }

    $returnArr['Action'] = "0";
    $returnArr['message'] = "LBL_SOMETHING_WENT_WRONG_MSG";
    setDataResponse($returnArr);
}

if ($type == "loadStaticInfo") {
    $OPTIMIZE_DATA_OBJ->loadStaticInfo();
}

if ($type == "loadStaticPages") {
    $OPTIMIZE_DATA_OBJ->loadStaticPages();
}

if ($type == "SetFaqs") {
    $OPTIMIZE_DATA_OBJ->SetFaqs();
}

if ($type == "SetCancelReasons") {
    $OPTIMIZE_DATA_OBJ->SetCancelReasons();
}

if ($type == "loadAppImages") {
    $OPTIMIZE_DATA_OBJ->loadAppImages();
}
?>