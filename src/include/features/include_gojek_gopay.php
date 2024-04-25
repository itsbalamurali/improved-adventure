<?php
###################### Gojek-gopay start by SP ########################

if ($type == "GopayCheckPhoneEmail") { //pass phone or mail, return id of it if it exists
    
    $EmailorPhone = isset($_REQUEST['transferType']) ? trim($_REQUEST['transferType']) : '';
    $emailorphoneData = isset($_REQUEST['vPhoneOrEmailTxt']) ? trim($_REQUEST['vPhoneOrEmailTxt']) : '';
    $fromUserId = isset($_REQUEST['fromUserId']) ? trim($_REQUEST['fromUserId']) : '';
    $fromUserType = isset($_REQUEST['fromUserType']) ? trim($_REQUEST['fromUserType']) : '';
    $searchUserType = isset($_REQUEST['searchUserType']) ? trim($_REQUEST['searchUserType']) : ''; //this is the key, from which table data will be search
   
    $flagerror = 0;
    $returnId = '';
    
    if(is_numeric($emailorphoneData)) $EmailorPhone = 'Phone';
    else $EmailorPhone = 'Email';
    
    if($EmailorPhone == 'Phone') $field = 'vPhone';
    else $field = 'vEmail';
    
    if(!empty($emailorphoneData)) { 
        if($searchUserType=='Passenger') { 
            $tableName = "register_user";

            $getfield = 'vCountry,vPhoneCode,iUserId,vEmail,vName,vLastName,vPhone,vImgName';
            if ($field == 'vPhone') {
                $first = substr($emailorphoneData, 0, 1);
                if ($first == "0") {
                    $emailorphoneData_ = substr($emailorphoneData, 1);
                } else {
                    $emailorphoneData_ = '0' . $emailorphoneData;
                }
                $sql .= " OR {$field} = '$emailorphoneData_' ";
                $get_data = $obj->MySQLSelect("SELECT $getfield FROM " . $tableName . " WHERE $field = '$emailorphoneData' {$sql} AND eStatus = 'Active'");

               
                if (!empty($get_data)) {
                    $CountryData = $obj->MySQLSelect("SELECT eZeroAllowed,vCountryCode FROM `country` WHERE vCountryCode  = '" . $get_data[0]['vCountry'] . "' AND vPhoneCode = '" . $get_data[0]['vPhoneCode'] . "'");

                    if ($CountryData[0]['eZeroAllowed'] == 'No') {
                        $sql = '';
                    }
                }
            }
            $getdata = $obj->MySQLSelect("SELECT $getfield FROM ".$tableName." WHERE ( $field = '$emailorphoneData' {$sql} ) AND eStatus = 'Active'");
            
            if(empty($getdata) && $EmailorPhone=='Phone') $getdata = checkingPhoneValidation($getfield,$tableName,$emailorphoneData,$EmailorPhone,'vPhoneCode,vPhone');

            if(!empty($getdata)) { 
                $returnId = $getdata[0]['iUserId'];
                if($returnId==$fromUserId && ($fromUserType=='Rider' || $fromUserType=='Passenger')) { 
                    $returnId = '';
                } else {   
                    $UserType = 'Rider';
                    $data['iUserId'] = $getdata[0]['iUserId'];
                    $data['eUserType'] = $UserType;
                    $data['vEmail'] = $getdata[0]['vEmail'];
                    $data['vName'] = $getdata[0]['vName']." ".$getdata[0]['vLastName'];
                    $data['vImgName'] = $tconfig['tsite_upload_images_passenger']."/".$data['iUserId']."/".$getdata[0]['vImgName'];
                    $data['vPhone'] = $getdata[0]['vPhone'];
                }
            }
        }
        //if(empty($returnId)) { 
        if($searchUserType=='Driver') { 
            $tableName = "register_driver";
            $getfield = 'iDriverId,vEmail,vName,vLastName,vPhone,vImage';
            
            $getdata = $obj->MySQLSelect("SELECT $getfield FROM ".$tableName." WHERE $field = '$emailorphoneData' AND eStatus = 'Active'");
            
            if(empty($getdata) && $EmailorPhone=='Phone') $getdata = checkingPhoneValidation($getfield,$tableName,$emailorphoneData,$EmailorPhone,'vCode,vPhone');
            if(!empty($getdata)) { 
                $returnId = $getdata[0]['iDriverId'];
                if($returnId==$fromUserId && $fromUserType=='Driver') {
                    $returnId = '';
                } else { 
                    $UserType = 'Driver';
                    $data['iUserId'] = $getdata[0]['iDriverId'];
                    $data['eUserType'] = $UserType;
                    $data['vEmail'] = $getdata[0]['vEmail'];
                    $data['vName'] = $getdata[0]['vName']." ".$getdata[0]['vLastName'];
                    $data['vImgName'] = $tconfig['tsite_upload_images_driver']."/".$data['iUserId']."/".$getdata[0]['vImage'];
                    $data['vPhone'] = $getdata[0]['vPhone'];
                }
            }
        }
        if(!empty($returnId)) {
            $returnArr['message'] = $data;
            $returnArr['Action'] = 1;
        } else {
            $flagerror = 1;
        }
    } else {
        $flagerror = 1;
    }
     
    if($flagerror==1) {
        if($EmailorPhone=='Email') {
            $returnArr['message'] = "LBL_INVALIDE_EMAIL_GOPAY";
            $returnArr['Action'] = 0;
        } elseif($EmailorPhone=='Phone') {
            $returnArr['message'] = "LBL_INVALIDE_PHONE_GOPAY";
            $returnArr['Action'] = 0;
        } else {
            $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
            $returnArr['Action'] = 0;
        }
    }
    
    setDataResponse($returnArr);
}

if ($type == "GoPayVerifyAmount") { //its return otp after checking all constraints of balance..otp send to mail and sms to "from user"
    $fromUserId = isset($_REQUEST['fromUserId']) ? trim($_REQUEST['fromUserId']) : '';
    $toUserId = isset($_REQUEST['toUserId']) ? trim($_REQUEST['toUserId']) : '';
    $fromUserType = isset($_REQUEST['fromUserType']) ? trim($_REQUEST['fromUserType']) : '';
    $toUserType = isset($_REQUEST['toUserType']) ? trim($_REQUEST['toUserType']) : '';
    $iBalance = isset($_REQUEST['fAmount']) ? trim($_REQUEST['fAmount']) : '';
    $isRegenerate = isset($_REQUEST['isRegenerate']) ? trim($_REQUEST['isRegenerate']) : 'No';
    if($fromUserType=='Passenger') $fromUserType = 'Rider';
    if($toUserType=='Passenger') $toUserType = 'Rider';
    
    $tableName = "register_driver";
    $fieldName = "iDriverId";
    if($fromUserType=='Rider'){
        $tableName = "register_user";
        $fieldName = "iUserId";
        //$userlangcode = get_value('register_user', 'vLang', 'iUserId', $fromUserId, '', 'true'); //get language code of user
    }
    //Added By HJ On 24-07-2020 For Optimize register_user/register_driver Table Query Start
    if(isset($userDetailsArr[$tableName.'_'.$fromUserId])){
        $memberData = $userDetailsArr[$tableName.'_'.$fromUserId];
    }else{
        $memberData = $obj->MySQLSelect("SELECT *,$fieldName as iMemberId FROM ".$tableName." WHERE $fieldName='".$fromUserId."'");
        $userDetailsArr[$tableName.'_'.$fromUserId] = $memberData;
    }
    //echo "<pre>";print_r($memberData);die;
    $userlangcode = $memberData[0]['vLang'];
    //Added By HJ On 24-07-2020 For Optimize register_user/register_driver Table Query End
    if ($userlangcode == "" || $userlangcode == NULL) {
        //Added By HJ On 24-07-2020 For Optimize language_master Table Query Start
        $userlangcode = $LANG_OBJ->FetchDefaultLangData("vCode");
        //Added By HJ On 24-07-2020 For Optimize language_master Table Query End
    }
    $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($userlangcode, "1");
    $fromTotalBalance = $WALLET_OBJ->FetchMemberWalletBalance($fromUserId, $fromUserType, $fromUserType=='Rider' ? true : false);
    if(isset($fromTotalBalance['CurrentBalance'])) {
        $fromTotalBalance = $fromTotalBalance['CurrentBalance'];
    }
    $toTotalBalance = $WALLET_OBJ->FetchMemberWalletBalance($toUserId, $toUserType);
    //$WALLET_MINIMUM_BALANCE_GOPAY = get_value('configurations_payment','vValue', 'vName' , 'WALLET_MINIMUM_BALANCE_GOPAY')[0]['vValue']; 
    //$COMMISION_DEDUCT_ENABLE = get_value('configurations_payment','vValue', 'vName' , 'COMMISION_DEDUCT_ENABLE')[0]['vValue'];
    //$WALLET_MIN_BALANCE = get_value('configurations_payment','vValue', 'vName' , 'WALLET_MIN_BALANCE')[0]['vValue']; 
    //$GOPAY_MAXIMUM_LIMIT_PER_TRANSACTION = get_value('configurations_payment','vValue', 'vName' , 'GOPAY_MAXIMUM_LIMIT_PER_TRANSACTION')[0]['vValue']; 
    //$GOPAY_MAXIMUM_LIMIT_PER_DAY = get_value('configurations_payment','vValue', 'vName' , 'GOPAY_MAXIMUM_LIMIT_PER_DAY')[0]['vValue']; 
    $enableCommisionDeduct = $MODULES_OBJ->autoDeductDriverCommision("Ride"); // Added By HJ On 16-10-2020 For get Auto Deduct Driver Commision Configuration As Per eSystem
    if($fromUserType=='Driver' && $enableCommisionDeduct=='Yes') {
        if($WALLET_MINIMUM_BALANCE_GOPAY==0) {
            $WALLET_MINIMUM_BALANCE_GOPAY = $WALLET_MIN_BALANCE;
        } else if($WALLET_MIN_BALANCE>$WALLET_MINIMUM_BALANCE_GOPAY) {
            $WALLET_MINIMUM_BALANCE_GOPAY = $WALLET_MIN_BALANCE;
        }
    }
    $flagerror = 0;
    $tableName = "register_driver";
    $tblField = "iDriverId";
    $currencyField = "vCurrencyDriver";
    if($fromUserType=='Rider') {
        $tableName = "register_user";
        $tblField = "iUserId";
        $currencyField = "vCurrencyPassenger";
        //$curDetail = get_value('register_user AS rd LEFT JOIN currency AS c ON c.vName=rd.vCurrencyPassenger', 'rd.vCurrencyPassenger as curencycode,c.Ratio,c.vSymbol', 'rd.iUserId', $fromUserId);
    } /*else {
        $curDetail = get_value('register_driver AS rd LEFT JOIN currency AS c ON c.vName=rd.vCurrencyDriver', 'rd.vCurrencyDriver as curencycode,c.Ratio,c.vSymbol', 'rd.iDriverId', $fromUserId);
    }*/
    //Added By HJ On 25-07-2020 For Optimize register_user/register_driver Table Query Start
    if(isset($userDetailsArr[$tableName.'_'.$fromUserId])){
        $curDetail = $userDetailsArr[$tableName.'_'.$fromUserId];
    }else{
        $curDetail = $obj->MySQLSelect("SELECT *,$tblField as iMemberId FROM ".$tableName." WHERE $tblField='".$fromUserId."'");
        $userDetailsArr[$tableName.'_'.$fromUserId] = $curDetail;
    }
    $vCurrencyName = $curDetail[0][$currencyField];
    //Added By HJ On 25-07-2020 For Optimize register_user/register_driver Table Query End
    //Added By HJ On 25-07-2020 For Optimize currency Table Query Start
    if(isset($currencyAssociateArr[$vCurrencyName])){
        $curDetail[0]['Ratio'] = $currencyAssociateArr[$vCurrencyName]['Ratio'];
        $curDetail[0]['vSymbol'] = $currencyAssociateArr[$vCurrencyName]['vSymbol'];
    }else{
        $currencydata = $obj->MySQLSelect("SELECT Ratio,vSymbol from currency WHERE vName= '" . $vCurrencyName . "'");
        $curDetail[0]['Ratio'] = $currencydata[0]['Ratio'];
        $curDetail[0]['vSymbol'] = $currencydata[0]['vSymbol'];
    }
    //Added By HJ On 25-07-2020 For Optimize currency Table Query End
    //echo "<pre>";print_r($curDetail);die;
    $ratio = $curDetail[0]['Ratio'];
    $currencySymbol = $curDetail[0]['vSymbol'];
    if($fromUserType=='Rider') {
         $curencycode = $curDetail[0]['vCurrencyPassenger'];
    } else {
         $curencycode = $curDetail[0]['vCurrencyDriver'];
    }
   
    $WALLET_MINIMUM_BALANCE_GOPAY = $WALLET_MINIMUM_BALANCE_GOPAY * $ratio;
    $fromTotalBalance = $fromTotalBalance * $ratio;
    $GOPAY_MAXIMUM_LIMIT_PER_TRANSACTION = $GOPAY_MAXIMUM_LIMIT_PER_TRANSACTION * $ratio;
    $GOPAY_MAXIMUM_LIMIT_PER_DAY = $GOPAY_MAXIMUM_LIMIT_PER_DAY * $ratio;

    if($GOPAY_MAXIMUM_LIMIT_PER_TRANSACTION < $iBalance) {
        $lbl = $languageLabelsArr['LBL_MAXIMUM_LIMIT_PER_TRANSACTION'];
        $GOPAY_MAXIMUM_LIMIT_PER_TRANSACTION_msg = round($GOPAY_MAXIMUM_LIMIT_PER_TRANSACTION,2);
        //$lbl = str_replace('###',$currencySymbol.$GOPAY_MAXIMUM_LIMIT_PER_TRANSACTION_msg,$lbl);
        $gopaymaximumamount = formateNumAsPerCurrency($GOPAY_MAXIMUM_LIMIT_PER_TRANSACTION_msg,$curencycode);
        $lbl = str_replace('###',$gopaymaximumamount,$lbl);
        $returnArr['message'] = $lbl;
        $returnArr['Action'] = 2;
        setDataResponse($returnArr);
    }
    $userBalancebyDate = get_user_currency_balance_byDate($fromUserId,$fromUserType);
    $userBalancebyDate = $userBalancebyDate + $iBalance;
    if($GOPAY_MAXIMUM_LIMIT_PER_DAY < $userBalancebyDate) {
        $GOPAY_MAXIMUM_LIMIT_PER_DAY_msg = round($GOPAY_MAXIMUM_LIMIT_PER_DAY,2);
        $lbl = $languageLabelsArr['LBL_MAXIMUM_LIMIT_PER_DAY'];
        $gopaymaximumamountperday = formateNumAsPerCurrency($GOPAY_MAXIMUM_LIMIT_PER_DAY_msg,$curencycode);
        $lbl = str_replace('###',$gopaymaximumamountperday,$lbl);
        //$lbl = str_replace('###',$currencySymbol.$GOPAY_MAXIMUM_LIMIT_PER_DAY_msg,$lbl);
        $returnArr['message'] = $lbl;
        $returnArr['Action'] = 2;
        setDataResponse($returnArr);
    }
    
    $WALLET_MINIMUM_BALANCE_GOPAY_msg = round($WALLET_MINIMUM_BALANCE_GOPAY,2);
    $fromTotalBalance_msg = round($fromTotalBalance,2);
    $iBalance_msg = round($iBalance,2);
    if($fromTotalBalance<$iBalance) {
        if($WALLET_MINIMUM_BALANCE_GOPAY == 0) {
            $returnArr['message'] = "LBL_WALLET_AMOUNT_GREATER_THAN_ZERO";
            $returnArr['Action'] = 0;
            $returnArr['showAddMoney'] = 'Yes';
            setDataResponse($returnArr);
        } else if($WALLET_MINIMUM_BALANCE_GOPAY < 0) {
            $lbl = $languageLabelsArr['LBL_TOTBALANCE_GREATER_THAN_WALLET_AMOUNT'];
            $remainBal = $fromTotalBalance_msg - $WALLET_MINIMUM_BALANCE_GOPAY_msg;
            //$lbl = str_replace('###',$currencySymbol.$remainBal,$lbl);
            $lbl = str_replace('###',formateNumAsPerCurrency($remainBal,$curencycode),$lbl);
            $lbl = str_replace('##',formateNumAsPerCurrency($iBalance_msg,$curencycode),$lbl);
            //$lbl = str_replace('##',$currencySymbol.$iBalance_msg,$lbl);
            $returnArr['message'] = $lbl;
            $returnArr['Action'] = 2; 
            $returnArr['showAddMoney'] = 'Yes';
            setDataResponse($returnArr);
        }
    }
    
    if($WALLET_MINIMUM_BALANCE_GOPAY > $fromTotalBalance) { 
        $lbl = $languageLabelsArr['LBL_TOTBALANCE_LESS_THAN_WALLET_AMOUNT'];
        $remainBal = $fromTotalBalance_msg - $WALLET_MINIMUM_BALANCE_GOPAY_msg;
        /*$lbl = str_replace('####',$currencySymbol.$remainBal,$lbl);
        $lbl = str_replace('###',$currencySymbol.$WALLET_MINIMUM_BALANCE_GOPAY_msg,$lbl);
        $lbl = str_replace('##',$currencySymbol.$iBalance_msg,$lbl);*/
        $lbl = str_replace('####',formateNumAsPerCurrency($remainBal,$curencycode),$lbl);
        $lbl = str_replace('###',formateNumAsPerCurrency($WALLET_MINIMUM_BALANCE_GOPAY_msg,$curencycode),$lbl);
        $lbl = str_replace('##',formateNumAsPerCurrency($iBalance_msg,$curencycode),$lbl);
        $returnArr['message'] = $lbl;
        $returnArr['Action'] = 2;
        $returnArr['showAddMoney'] = 'Yes';
        setDataResponse($returnArr);    
    }
    
    if($fromTotalBalance>0) { 
        if($fromTotalBalance >= $WALLET_MINIMUM_BALANCE_GOPAY) { 
             $availableBalance = $fromTotalBalance - $iBalance;
            
            if($availableBalance >= $WALLET_MINIMUM_BALANCE_GOPAY) { 
                /* get needed details from user or driver table start */
                if ($toUserType == "Rider") {
                    $totblname = "register_user";
                    $tocondfield = 'iUserId';  
                } else {
                    $totblname = "register_driver"; 
                    $tocondfield = 'iDriverId';
                } 
                if ($fromUserType == "Rider") {
                    $fromtblname = "register_user";
                    $fromcondfield = 'iUserId';
                    $fieldphone =  'vPhoneCode as vPhoneCode';
                } else {
                    $fromtblname = "register_driver";
                    $fromcondfield = 'iDriverId';
                    $fieldphone =  'vCode as vPhoneCode';
                }
                //Added By HJ On 24-07-2020 For Optimize register_user/register_driver Table Query Start
                if(isset($userDetailsArr[$totblname.'_'.$toUserId])){
                    $db_tomember = $userDetailsArr[$totblname.'_'.$toUserId];
                }else{
                    $db_tomember = $obj->MySQLSelect("SELECT *,$tocondfield as iMemberId FROM ".$totblname." WHERE $tocondfield='".$toUserId."'");
                    $userDetailsArr[$totblname.'_'.$toUserId] = $db_tomember;
                }
                $db_tomember[0]['name'] =$toName= $db_tomember[0]['vName']." ".$db_tomember[0]['vLastName'];
                //$db_tomember = $obj->MySQLSelect("select CONCAT(vName,' ', vLastName) AS name from $totblname where $tocondfield = '" . $toUserId . "'");
                if(isset($userDetailsArr[$fromtblname.'_'.$fromUserId])){
                    $db_frommember = $userDetailsArr[$fromtblname.'_'.$fromUserId];
                }else{
                    $db_frommember = $obj->MySQLSelect("SELECT *,$fieldphone,$fromcondfield as iMemberId FROM ".$fromtblname." WHERE $fromcondfield='".$fromUserId."'");
                    $userDetailsArr[$fromtblname.'_'.$fromUserId] = $db_frommember;
                }
                //echo "<pre>";print_r($db_frommember);die;
                $db_frommember[0]['name'] =$fromName= $db_frommember[0]['vName']." ".$db_frommember[0]['vLastName'];
                //$db_frommember = $obj->MySQLSelect("select CONCAT(vName,' ', vLastName) AS name,vEmail,vPhone,$fieldphone,vLang from $fromtblname where $fromcondfield = '" . $fromUserId . "'");
                //Added By HJ On 24-07-2020 For Optimize register_user/register_driver Table Query End
                //added By SP for sms functionality on 13-7-2019 start
                $fromCountry = $db_frommember[0]['vCountry'];
                //Added By HJ On 24-07-2020 For Optimize country Table Query Start
                if(isset($country_data_arr[$fromCountry])){
                    $passengerData = array();
                    $passengerData[] = $country_data_arr[$fromCountry];
                } else {
                    $passengerData = $obj->MySQLSelect("SELECT * FROM country WHERE vCountryCode='".$fromCountry."'");
                }
                //$passengerData = $obj->MySQLSelect("SELECT * FROM country WHERE vCountryCode='".$fromCountry."'");
                //echo "<pre>";print_r($passengerData);die;
                //$passengerData = $obj->MySQLSelect("SELECT r.vPhone,c.vPhoneCode FROM  $fromtblname AS r, `country` AS c WHERE r.$fromcondfield = $fromUserId AND r.vCountry = c.vCountryCode");
                //Added By HJ On 24-07-2020 For Optimize country Table Query End
                $PhoneCode = $passengerData[0]['vPhoneCode'];
                //added By SP for sms functionality on 13-7-2019 end

                /* get needed details from user or driver table end */
                
                /* pass details to sendemailphoneOTP start */
                $datatransfer['fromUserId'] = $fromUserId;
                $datatransfer['toUserId'] = $toUserId;
                $datatransfer['fromUserType'] = $fromUserType;
                $datatransfer['toUserType'] = $toUserType;
                $datatransfer['toName'] = $toName;
                $datatransfer['fromName'] = $fromName;
                $datatransfer['walletId'] = '';
                $datatransfer['vEmail'] = $db_frommember[0]['vEmail'];
                //$datatransfer['mobileNo'] = $db_frommember[0]['vPhoneCode'].$db_frommember[0]['vPhone'];
                $datatransfer['mobileNo'] = $db_frommember[0]['vPhone'];
                $datatransfer['vLang'] = $db_frommember[0]['vLang'];
                $datatransfer['phoneCode'] = $PhoneCode;
                
                /* pass details to sendemailphoneOTP end */
                $sendemailphoneOTPResponse =  sendemailphoneOTP($datatransfer,'OTP_TRANSFER_MONEY');
                $message = $sendemailphoneOTPResponse['phonemessage'];

                if($message==-1) { //if message recieve -1 in both mail and sms otp is not send
                    //if sms and mail not send then push notification send start
                    $alertMsg = $sendemailphoneOTPResponse['OTP_template'];
                    if ($toUserType == "Rider") {
                        $channelName = 'PASSENGER_' . $datatransfer['toUserId'];
                        $NOTI_USER_TYPE = RN_USER;
                        $final_message_new['iUserId'] = $toUserId;
                    }else {
                        $final_message_new['iDriverId'] = $toUserId;
                        $channelName = 'CAB_REQUEST_DRIVER_' . $datatransfer['toUserId'];
                        $NOTI_USER_TYPE = RN_PROVIDER;
                    }
                    $final_message_new['Message'] = "GoPayVerifyAmount";
                    $final_message_new['MsgType'] = "GoPayVerifyAmount";
                    $final_message_new['time'] = time();
                    $final_message_new['vTitle'] = $alertMsg;
                    $final_message_new['tRandomCode'] = time();
                    $generalDataArr[] = array(
                        'eDeviceType'       => $db_tomember[0]['eDeviceType'],
                        'deviceToken'       => $db_tomember[0]['iGcmRegId'],
                        'alertMsg'          => $alertMsg,
                        'eAppTerminate'     => $db_tomember[0]['eAppTerminate'],
                        'eDebugMode'        => $db_tomember[0]['eDebugMode'],
                        'eHmsDevice'        => $db_tomember[0]['eHmsDevice'],
                        'message'           => $final_message_new,
                        'channelName'       => $channelName
                    );

                    $data = $EVENT_MSG_OBJ->send(array('GENERAL_DATA' => $generalDataArr), $NOTI_USER_TYPE);
                    //if sms and mail not send then push notification send end
                    if($data == 1) {
                        $message = $sendemailphoneOTPResponse['OTP'];
                        $flagerror = 1;
                    }else{
                        $returnArr['message'] = "LBL_EMAIL_MOBILE_OTP_FAILED_TXT";
                        $returnArr['Action'] = 0;
                        $flagerror = 2;
                    }
                } else { 
                    if($isRegenerate=='Yes') { 
                        $whereOtpData = "iUserId='" . $toUserId . "' AND eUserType='".$toUserType."' AND fromUserId='".$fromUserId."' AND fromUserType='".$fromUserType."'";
                        $updateOtpData['datetime'] = date('Y-m-d H:i:s');
                        $result =  $obj->MySQLQueryPerform('gopay_otp_logs', $updateOtpData, 'update', $whereOtpData);
                        if(!empty($result)) $flagerror = 1; 
                        
                    } else { 
                        $getOtpData = $obj->MySQLSelect("SELECT datetime FROM gopay_otp_logs WHERE iUserId=$toUserId AND eUserType='$toUserType' AND fromUserId=$fromUserId AND fromUserType='$fromUserType'");
                        if(!empty($getOtpData)) {
                            $whereOtpData = "iUserId='" . $toUserId . "' AND eUserType='".$toUserType."' AND fromUserId='".$fromUserId."' AND fromUserType='".$fromUserType."'";
                            $updateOtpData['datetime'] = date('Y-m-d H:i:s');
                            $result =  $obj->MySQLQueryPerform('gopay_otp_logs', $updateOtpData, 'update', $whereOtpData);
                        } else {
                            // solve otp expire issue first time
                            $datetimeNow = date('Y-m-d H:i:s');
                            $sql = "INSERT INTO `gopay_otp_logs` (`iUserId`,`eUserType`,`fromUserId`,`fromUserType`,`datetime`) VALUES ('" . $toUserId . "','" . $toUserType . "', '" . $fromUserId . "','" . $fromUserType . "','".$datetimeNow."')";                            $result = $obj->MySQLInsert($sql); 
                        }
                        if(!empty($result)) $flagerror = 1; 
                    }           
                    
                } 
                 
            } else {
                $lbl = $languageLabelsArr['LBL_TOTBALANCE_GREATER_THAN_WALLET_AMOUNT'];
                $remainBal = $fromTotalBalance_msg - $WALLET_MINIMUM_BALANCE_GOPAY_msg;
                /*$lbl = str_replace('####',$currencySymbol.$remainBal,$lbl);
                $lbl = str_replace('###',$currencySymbol.$WALLET_MINIMUM_BALANCE_GOPAY_msg,$lbl);
                $lbl = str_replace('##',$currencySymbol.$iBalance_msg,$lbl);*/
                $lbl = str_replace('####',formateNumAsPerCurrency($remainBal,$curencycode),$lbl);
                $lbl = str_replace('###',formateNumAsPerCurrency($WALLET_MINIMUM_BALANCE_GOPAY_msg,$curencycode),$lbl);
                $lbl = str_replace('##',formateNumAsPerCurrency($iBalance_msg,$curencycode),$lbl);
                //$lbl = $languageLabelsArr['LBL_MINBAL_GREATER_THAN_WALLET_AMOUNT']." ".$WALLET_MINIMUM_BALANCE_GOPAY;
                //$lbl = "You don't have enough wallet balance to transfer $20. Minimum wallet balance $2 must be maintained. Maximum eligible transfer amount is $18.";
                $returnArr['message'] = $lbl;
                $returnArr['Action'] = 2; //pass 2 when string pass in message instead of label in action 0
                $flagerror = 2;
                $returnArr['showAddMoney'] = 'No';
            }
        } else {
            $lbl = $languageLabelsArr['LBL_MINBALANCE_GREATER_THAN_WALLET_AMOUNT'];
            $remainBal = $fromTotalBalance_msg - $WALLET_MINIMUM_BALANCE_GOPAY_msg;
            /*$lbl = str_replace('####',$currencySymbol.$remainBal,$lbl);
            $lbl = str_replace('###',$currencySymbol.$WALLET_MINIMUM_BALANCE_GOPAY_msg,$lbl);
            $lbl = str_replace('##',$currencySymbol.$iBalance_msg,$lbl);*/
            $lbl = str_replace('####',formateNumAsPerCurrency($remainBal,$curencycode),$lbl);
            $lbl = str_replace('###',formateNumAsPerCurrency($WALLET_MINIMUM_BALANCE_GOPAY_msg,$curencycode),$lbl);
            $lbl = str_replace('##',formateNumAsPerCurrency($iBalance_msg,$curencycode),$lbl);
            //$lbl = $languageLabelsArr['LBL_TOTBAL_GREATER_THAN_WALLET_AMOUNT']." ".$WALLET_MINIMUM_BALANCE_GOPAY;
            //$lbl = "You don't have enough wallet balance to transfer $20. Maximum eligible transfer amount is $18.";
            $returnArr['message'] = $lbl;
            $returnArr['Action'] = 2;
            $returnArr['showAddMoney'] = 'No';
            $flagerror = 2;
        } 
    } else {
        $returnArr['message'] = "LBL_WALLET_AMOUNT_GREATER_THAN_ZERO";
        $returnArr['Action'] = 0;
        $returnArr['showAddMoney'] = 'Yes';
        $flagerror = 2;
    }
    if($flagerror==1) { 
        $returnArr['message']['verificationCode'] = $message;
        $returnArr['Action'] = 1;
    } else if($flagerror==0) {
        $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
        $returnArr['Action'] = 0;
    } 
    setDataResponse($returnArr);
}

if ($type == "GoPayTransferAmount") {
    $fromUserId = isset($_REQUEST['fromUserId']) ? trim($_REQUEST['fromUserId']) : '';
    $toUserId = isset($_REQUEST['toUserId']) ? trim($_REQUEST['toUserId']) : '';
    $fromUserType = isset($_REQUEST['fromUserType']) ? trim($_REQUEST['fromUserType']) : '';
    $toUserType = isset($_REQUEST['toUserType']) ? trim($_REQUEST['toUserType']) : '';
    $iBalance = isset($_REQUEST['fAmount']) ? trim($_REQUEST['fAmount']) : '';
    $eTypeDebit = 'Debit'; 
    $eTypeCredit = 'Credit';
    $eFor = 'Transfer';
    $date = date("Y-m-d H:i:s");
    $flagerror = 0;
    
    $ePaymentStatus = "Unsettelled";
    
    if($fromUserType=='Passenger') $fromUserType = 'Rider';
    if($toUserType=='Passenger') $toUserType = 'Rider';
    /* Check whether OTP expired or not start */
    //$OTP_EXPIRED_GOJEK = get_value('configurations_payment','vValue', 'vName' , 'OTP_EXPIRED_GOJEK')[0]['vValue']; 

    $getOtpData = $obj->MySQLSelect("SELECT datetime FROM gopay_otp_logs WHERE iUserId=$toUserId AND eUserType='$toUserType' AND fromUserId=$fromUserId AND fromUserType='$fromUserType'");
    $getDate = $getOtpData[0]['datetime'];
    $currentDateFormat = new DateTime($date); 
    $getDateFormat = new DateTime($getDate); 
    $diffDate = $getDateFormat->diff($currentDateFormat);
    if($diffDate->y == 0 && $diffDate->m == 0 && $diffDate->d == 0 && $diffDate->h == 0) {
        if($diffDate->i <= $OTP_EXPIRED_GOJEK) {
            $flagerror = 2;
        } 
    }
    //$flagerror = 2;
    if($flagerror==0) {
        $returnArr['message'] = "LBL_OTP_EXPIRED";
        $returnArr['Action'] = 0;
        setDataResponse($returnArr);
    }
    /* Check whether OTP expired or not end */
    if ($iBalance > 0) {
        /* get needed details from user or driver table start */
        $tableName = "register_driver";
        $fieldName = "iDriverId";
        if($fromUserType=='Rider'){
            $tableName = "register_user";
            $fieldName = "iUserId";
        }
        //Added By HJ On 25-07-2020 For Optimize register_user/register_driver Table Query Start
        if(isset($userDetailsArr[$tableName.'_'.$fromUserId])){
            $memberData = $userDetailsArr[$tableName.'_'.$fromUserId];
        }else{
            $memberData = $obj->MySQLSelect("SELECT *,$fieldName as iMemberId FROM ".$tableName." WHERE $fieldName='".$fromUserId."'");
            $userDetailsArr[$tableName.'_'.$fromUserId] = $memberData;
        }
        $userlangcode = $memberData[0]['vLang']; 
        //Added By HJ On 25-07-2020 For Optimize register_user/register_driver Table Query End
        if ($userlangcode == "" || $userlangcode == NULL) {
            //Added By HJ On 24-07-2020 For Optimize language_master Table Query Start
            $userlangcode = $LANG_OBJ->FetchDefaultLangData("vCode");
            //Added By HJ On 24-07-2020 For Optimize language_master Table Query End
        }
        $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($userlangcode, "1");
        if ($toUserType == "Rider") {
            $totblname = "register_user";
            $tocondfield = 'iUserId';
            $fieldphone =  'vPhoneCode as vPhoneCode';
            $todisplaytype = $languageLabelsArr['LBL_RIDER'];
            $todisplaytypenew = '#LBL_RIDER#';
        } else {
            $totblname = "register_driver";
            $tocondfield = 'iDriverId';
            $fieldphone =  'vCode as vPhoneCode';
            $todisplaytype = $languageLabelsArr['LBL_DRIVER'];
            $todisplaytypenew = '#LBL_DRIVER#';
        }

        if ($fromUserType == "Rider") {
            $fromtblname = "register_user";
            $fromcondfield = 'iUserId';
            $fromdisplaytype = $languageLabelsArr['LBL_RIDER'];
            $fieldCur = 'vCurrencyPassenger';
            $fromdisplaytypenew = '#LBL_RIDER#';
        } else {
            $fromtblname = "register_driver";
            $fromcondfield = 'iDriverId';
            $fromdisplaytype = $languageLabelsArr['LBL_DRIVER'];
            $fieldCur = 'vCurrencyDriver';
            $fromdisplaytypenew = '#LBL_DRIVER#';
        }
        //Added By HJ On 25-07-2020 For Optimize register_user/register_driver Table Query Start
        if(isset($userDetailsArr[$totblname.'_'.$toUserId])){
            $db_tomember = $userDetailsArr[$totblname.'_'.$toUserId];
        }else{
            $db_tomember = $obj->MySQLSelect("SELECT *,$tocondfield as iMemberId FROM ".$totblname." WHERE $tocondfield='".$toUserId."'");
            $userDetailsArr[$totblname.'_'.$toUserId] = $db_tomember;
        }
        $db_tomember[0]['name'] =$toName= $db_tomember[0]['vName']." ".$db_tomember[0]['vLastName'];
        //$db_tomember = $obj->MySQLSelect("select CONCAT(vName,' ', vLastName) AS name,vEmail,vPhone,$fieldphone,vLang from $totblname where $tocondfield = '" . $toUserId . "'");
        //$toName = $db_tomember[0]['name'];
        
        if(isset($userDetailsArr[$fromtblname.'_'.$fromUserId])){
            $db_frommember = $userDetailsArr[$fromtblname.'_'.$fromUserId];
        }else{
            $db_frommember = $obj->MySQLSelect("SELECT *,$fromcondfield as iMemberId FROM ".$fromtblname." WHERE $fromcondfield='".$fromUserId."'");
            $userDetailsArr[$fromtblname.'_'.$fromUserId] = $db_frommember;
        }
        $db_frommember[0]['name'] =$fromName= $db_frommember[0]['vName']." ".$db_frommember[0]['vLastName'];
        //$db_frommember = $obj->MySQLSelect("select CONCAT(vName,' ', vLastName) AS name, $fieldCur as currencycode from $fromtblname where $fromcondfield = '" . $fromUserId . "'");
        //$fromName = $db_frommember[0]['name'];
        //echo "<pre>";print_r($db_frommember);die;
        //Added By HJ On 25-07-2020 For Optimize register_user/register_driver Table Query End
        $userCurrencyCode = $db_frommember[0][$fieldCur];
        //Added By HJ On 25-07-2020 For Optimize currency Table Query Start
        if(isset($currencyAssociateArr[$userCurrencyCode])){
            $userCurrencyData = array();
            $userCurrencyData[0]['Ratio'] = $currencyAssociateArr[$userCurrencyCode]['Ratio'];
            $userCurrencyData[0]['vSymbol'] = $currencyAssociateArr[$userCurrencyCode]['vSymbol'];
        }else{
            $userCurrencyData = $obj->MySQLSelect("SELECT Ratio,vSymbol from currency WHERE vName= '" . $vCurrencyName . "'");
        }
        //$userCurrencyData = get_value('currency', 'Ratio,vSymbol', 'vName', $userCurrencyCode);
        //Added By HJ On 25-07-2020 For Optimize currency Table Query End
        $userCurrencyRatio = $userCurrencyData[0]['Ratio'];
        $userCurrencySymbol = $userCurrencyData[0]['vSymbol'];
        $iBalance = round($iBalance / $userCurrencyRatio, 2);
        //$iBalance = round($iBalance * $userCurrencyRatio,2);
        if ($iBalance <= 0) {
            $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
            $returnArr['Action'] = 0;
            setDataResponse($returnArr);
        }
        
        /* get needed details from user or driver table end */
        //$descDebit = "Transferred money to $toName($todisplaytype) by wallet.";
        //$descCredit = "Received money from $fromName($fromdisplaytype) by wallet.";
        
        //added by SP on 12-11-2020 for transfer description converted from label.
        $descDebit = "#LBL_GOPAY_TRANSFER_TXT# $toName($todisplaytypenew) #LBL_BY_WALLET_TRANSTER_TXT#.";
        $descCredit = "#LBL_RECEIVED_MONEY_FROM# $fromName($fromdisplaytypenew)  #LBL_BY_WALLET_TRANSTER_TXT#.";
        
        ### Debit  User Wallet  ###
        $resultDebit = InsertIntoUserWalletforGopay($fromUserId, $fromUserType, $iBalance, $eTypeDebit, $eFor, $descDebit, $ePaymentStatus, $date, $toUserId,$toUserType);
        
        ### Credit Driver Wallet ###
        $resultCredit = InsertIntoUserWalletforGopay($toUserId, $toUserType, $iBalance, $eTypeCredit, $eFor, $descCredit, $ePaymentStatus, $date, $fromUserId,$fromUserType);

        //added By SP for sms functionality on 13-7-2019 start
        //Added By HJ On 25-07-2020 For Optimize country Table Query Start
        $memberCountry = $db_tomember[0]['vCountry'];
        if(isset($country_data_arr[$memberCountry])){
            $passengerData = array();
            $passengerData[] = $country_data_arr[$memberCountry];
        } else {
            $passengerData = $obj->MySQLSelect("SELECT * FROM country WHERE vCountryCode='".$memberCountry."'");
        }
        //echo "<pre>";print_r($passengerData);die;
        //$passengerData = $obj->MySQLSelect("SELECT r.vPhone,c.vPhoneCode FROM $totblname AS r, `country` AS c WHERE r.$tocondfield = $toUserId AND r.vCountry = c.vCountryCode");
        //Added By HJ On 25-07-2020 For Optimize country Table Query End
        $PhoneCode = $passengerData[0]['vPhoneCode'];
        //added By SP for sms functionality on 13-7-2019 end
        if(!empty($resultDebit) && !empty($resultCredit)) { 
            /* pass details to sendemailphoneOTP start */
            $datatransfer['fromUserId'] = $fromUserId;
            $datatransfer['toUserId'] = $toUserId; 
            $datatransfer['fromUserType'] = $fromUserType;
            $datatransfer['toUserType'] = $toUserType;
            $datatransfer['toName'] = $toName."(".$todisplaytype.")";
            $datatransfer['fromName'] = $fromName."(".$fromdisplaytype.")";
            $datatransfer['fromName1'] = $fromName;
            $datatransfer['walletId'] = $resultCredit;
            $datatransfer['vEmail'] = $db_tomember[0]['vEmail'];
            //$datatransfer['mobileNo'] = $db_tomember[0]['vPhoneCode'].$db_tomember[0]['vPhone'];
            $datatransfer['mobileNo'] = $db_tomember[0]['vPhone'];
            $datatransfer['vLang'] = $db_tomember[0]['vLang'];
            $datatransfer['phoneCode'] = $PhoneCode;
            //$datatransfer['amount'] = $userCurrencySymbol.round($_REQUEST['fAmount'],2);
            $datatransfer['amount'] =formateNumAsPerCurrency($_REQUEST['fAmount'],$userCurrencyCode);
            /* pass details to sendemailphoneOTP end */
            $sendemailphoneOTPResponse = sendemailphoneOTP($datatransfer,'WALLET_AMOUNT_TRANSFER');

            $message = $sendemailphoneOTPResponse['phonemessage'];

            $alertMsg = $sendemailphoneOTPResponse['OTP_template'];
            if ($toUserType == "Rider") {
                $channelName = 'PASSENGER_' . $datatransfer['toUserId'];
                $NOTI_USER_TYPE = RN_USER;
                $final_message_new['iUserId'] = $toUserId;
            }else {
                $final_message_new['iDriverId'] = $toUserId;
                $channelName = 'CAB_REQUEST_DRIVER_' . $datatransfer['toUserId'];
                $NOTI_USER_TYPE = RN_PROVIDER;
            }
            $final_message_new['Message'] = "GoPayVerifyAmount";
            $final_message_new['MsgType'] = "GoPayVerifyAmount";
            $final_message_new['time'] = time();
            $final_message_new['vTitle'] = $alertMsg;
            $final_message_new['tRandomCode'] = time();
            $generalDataArr[] = array(
                'eDeviceType'       => $db_tomember[0]['eDeviceType'],
                'deviceToken'       => $db_tomember[0]['iGcmRegId'],
                'alertMsg'          => $alertMsg,
                'eAppTerminate'     => $db_tomember[0]['eAppTerminate'],
                'eDebugMode'        => $db_tomember[0]['eDebugMode'],
                'eHmsDevice'        => $db_tomember[0]['eHmsDevice'],
                'message'           => $final_message_new,
                'channelName'       => $channelName
            );

            $data = $EVENT_MSG_OBJ->send(array('GENERAL_DATA' => $generalDataArr), $NOTI_USER_TYPE);

            //$lbl = $languageLabelsArr['LBL_SUCCESSFULLY_TRANFERRED']." ".$toName;
            $lbl = $languageLabelsArr['LBL_SUCCESSFULLY_TRANFERRED'];
            //$returnArr['transactionDate'] = date("d F,Y");
            $returnArr['fAmountWithSymbol'] = formateNumAsPerCurrency($_REQUEST['fAmount'],$userCurrencyCode);
            $returnArr['transactionDate'] = $date;
            $returnArr['message'] = $lbl;
            $returnArr['Action'] = 2;
            if ($fromUserType == "Rider") {
                $returnArr['message_profile_data'] = getPassengerDetailInfo($fromUserId, "", "");
            } else {
                $returnArr['message_profile_data'] = getDriverDetailInfo($fromUserId);
            }
        } else {
            $flagerror = 1;
        }
    } else {
        $flagerror = 1;
    } 
    if($flagerror==1) {
        $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
        $returnArr['Action'] = 0;
    }
    setDataResponse($returnArr);
}

function InsertIntoUserWalletforGopay($iUserId, $eUserType, $iBalance, $eType, $eFor, $tDescription, $ePaymentStatus, $dDate,$fromUserId,$fromUserType) {
    global $obj,$Data_ALL_currency_Arr;
    $escaped_item = $obj->SqlEscapeString($tDescription);
    $sql = "INSERT INTO `user_wallet` (`iUserId`,`eUserType`,`iBalance`,`eType`, `eFor`, `tDescription`, `ePaymentStatus`, `dDate`, `fromUserId`,`fromUserType`) VALUES ('" . $iUserId . "','" . $eUserType . "', '" . $iBalance . "','" . $eType . "', '" . $eFor . "', '" . $escaped_item . "', '" . $ePaymentStatus . "', '" . $dDate . "','".$fromUserId."','".$fromUserType."')";
    $result = $obj->MySQLInsert($sql);
    
    //Added By HJ On 25-07-2020 For Optimize currency Table Query Start
    if(count($Data_ALL_currency_Arr) > 0){
        $db_curr = array();
        for($c=0;$c<count($Data_ALL_currency_Arr);$c++){
            if(strtoupper($Data_ALL_currency_Arr[$c]['eStatus']) == "ACTIVE"){
                $db_curr[] = $Data_ALL_currency_Arr[$c];
            }
        }
    } else { 
        $db_curr = $obj->MySQLSelect("SELECT * FROM currency WHERE eStatus = 'Active'");
    }
    //Added By HJ On 25-07-2020 For Optimize currency Table Query End
    //echo "<pre>";print_r($db_curr);die;
    
    $where = " iUserWalletId = '" . $result . "'";
    for ($i = 0; $i < count($db_curr); $i++) {
        $data_currency_ratio['fRatio_' . $db_curr[$i]['vName']] = $db_curr[$i]['Ratio'];
    }
    //echo "<pre>";print_r($data_currency_ratio);die;
    $obj->MySQLQueryPerform("user_wallet", $data_currency_ratio, 'update', $where);
    return $result;
}
function checkingPhoneValidation($getfield,$tableName,$emailorphoneData,$EmailorPhone,$concatfield) {
    global $obj;
    $getdata = $obj->MySQLSelect("SELECT $getfield FROM ".$tableName." WHERE CONCAT( $concatfield ) = '$emailorphoneData' AND eStatus = 'Active'");
    if(empty($getdata) && $EmailorPhone=='Phone') {
        $emailorphoneData = ltrim($emailorphoneData,0);
        $getdata = $obj->MySQLSelect("SELECT $getfield FROM ".$tableName." WHERE Phone = '$emailorphoneData' AND eStatus = 'Active'");
    } 
    return $getdata;
}
function sendemailphoneOTP($datatransfer,$template_name = '') {
    global $obj,$userDetailsArr,$vSystemDefaultCurrencyName,$vSystemDefaultCurrencySymbol,$COMM_MEDIA_OBJ,$LANG_OBJ;
    $iMemberId = $datatransfer['fromUserId'];
    $toUserId = $datatransfer['toUserId'];
    $user_type = $datatransfer['fromUserType'];
    $toUserType = $datatransfer['toUserType'];
    $walletid = $datatransfer['walletId'];

    $Data_Mail['vEmail'] = $datatransfer['vEmail'];
    $Data_Mail['ToName'] = $datatransfer['toName'];
    $Data_Mail['FromName'] = $datatransfer['fromName'];
    //$toMobileNum = "+" .$datatransfer['mobileNo'];
    $toMobileNum = $datatransfer['mobileNo'];
    $phoneCode = $datatransfer['phoneCode'];
    $vLangCode = $datatransfer['vLang'];
    $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLangCode, "1");
    //$prefix = $datatransfer['prefix'];
    $res = $obj->MySQLSelect("select * from send_message_templates where vEmail_Code='$template_name' AND eStatus = 'Active'");
    $prefix = $res[0]['vBody_' . $vLangCode];
    $phonemessage = "";
    if($template_name=='WALLET_AMOUNT_TRANSFER') {        
        /* get currency start */
        // if ($toUserType == "Rider") { this line changed by sunita 13-01-2020
        if ($user_type == "Rider") {
            $tblnamecur = "register_user";
            $vLang = "vLang";
            $iUserId = "iUserId";
            $vCurrency = "vCurrencyPassenger";
            $vPhoneCode = "vPhoneCode";
            /*$sqlp = "SELECT ru.vCurrencyPassenger,ru.vLang,cu.vSymbol,ru.vEmail FROM register_user as ru LEFT JOIN currency as cu ON ru.vCurrencyPassenger = cu.vName WHERE iUserId = '" . $iMemberId . "'"; //$toUserId changed to from by sunita 13-01-2020
            $passengerData = $obj->MySQLSelect($sqlp);
            $currencycode = $passengerData[0]['vCurrencyPassenger'];
            $userlangcode = $passengerData[0]['vLang'];
            $currencySymbol = $passengerData[0]['vSymbol'];
            $vEmailSender = $passengerData[0]['vEmail'];*/
        } else {
            $tblnamecur = "register_driver";
            $vLang = "vLang";      
            $iUserId = "iDriverId";
            $vCurrency = "vCurrencyDriver";
            $vPhoneCode = "vCode";
            /*$sqld = "SELECT rd.vCurrencyDriver,rd.vLang,cu.vSymbol,rd.vEmail FROM register_driver as rd LEFT JOIN currency as cu ON rd.vCurrencyDriver = cu.vName WHERE iDriverId = '" . $iMemberId . "'"; //$toUserId changed to from by sunita 13-01-2020
            $driverData = $obj->MySQLSelect($sqld);
            $currencycode = $driverData[0]['vCurrencyDriver'];
            $userlangcode = $driverData[0]['vLang'];
            $currencySymbol = $driverData[0]['vSymbol'];
            $vEmailSender = $driverData[0]['vEmail'];*/
        }
        //Added By HJ On 25-07-2020 For Optimize register_user/register_driver Table Query Start
        if(isset($userDetailsArr[$tblnamecur.'_'.$iMemberId])){
            $memberData = $userDetailsArr[$tblnamecur.'_'.$iMemberId];
        }else{
            $memberData = $obj->MySQLSelect("SELECT *,$iUserId as iMemberId FROM ".$tblnamecur." WHERE $iUserId='".$iMemberId."'");
            $userDetailsArr[$tblnamecur.'_'.$iMemberId] = $memberData;
        }
        //echo "<pre>";print_r($memberData);die;
        $currencycode = $memberData[0][$vCurrency];
        $userlangcode = $memberData[0]['vLang'];
        $currencySymbol = $memberData[0]['vSymbol'];
        $vEmailSender = $memberData[0]['vEmail'];
        //Added By HJ On 25-07-2020 For Optimize register_user/register_driver Table Query End
        if ($currencycode == "" || $currencycode == NULL) {
            //Added By HJ On 25-07-2020 For Optimization currency Table Query Start
            if (!empty($vSystemDefaultCurrencyName) && !empty($vSystemDefaultCurrencySymbol)) {
                $currencyData = array();
                $currencyData[0]['vName'] = $vSystemDefaultCurrencyName;
                $currencyData[0]['vSymbol'] = $vSystemDefaultCurrencySymbol;
            }else{
                $currencyData = $obj->MySQLSelect("SELECT vName,vSymbol from currency WHERE eDefault = 'Yes'");
            }
            $currencycode = $currencyData[0]['vName'];
            $currencySymbol = $currencyData[0]['vSymbol'];
        }
        else {
            $currencyData = $obj->MySQLSelect("SELECT vName,vSymbol from currency WHERE vName = '$currencycode'");
            $currencycode = $currencyData[0]['vName'];
            $currencySymbol = $currencyData[0]['vSymbol'];
        }
        $getfieldcur = 'fRatio_' . $currencycode;
        $getfieldcur .= ',iBalance';
        $getuserwallet = get_value('user_wallet',$getfieldcur, 'iUserWalletId' , $walletid);
        $priceRatio = $getuserwallet[0]['fRatio_' . $currencycode];
        $amount = round($getuserwallet[0]['iBalance'] * $priceRatio,2);
        //$Data_Mail['amount'] = $currencySymbol.$amount;
        $Data_Mail['amount'] = formateNumAsPerCurrency($amount,$currencycode);
        //$Data_Mail['amount'] = round($Data_Mail['amount'],2);
        /* Get currency end */ 
          
        $key_arr = array('#AMOUNT#','#FROMNAME#');
        $val_arr = array($Data_Mail['amount'], $Data_Mail['FromName']);
        $notifymessage = '123';
        $message = str_replace($key_arr, $val_arr, $prefix);
        //$result = sendEmeSms($toMobileNum, $message);
        $result = $COMM_MEDIA_OBJ->SendSystemSMS($toMobileNum,$phoneCode,$message);
        $Data_Mail['OTP'] = $notifymessage;
        $sendemail = $COMM_MEDIA_OBJ->SendMailToMember($template_name, $Data_Mail);
        
        $Data_Mail['amount_sent'] = $datatransfer['amount'];
        $Data_Mail['FromName'] = $datatransfer['fromName1'];
        $Data_Mail['vEmail'] = $vEmailSender;
        $sendemail = $COMM_MEDIA_OBJ->SendMailToMember('WALLET_AMOUNT_TRANSFER_SENDER', $Data_Mail);

        $returnArr['OTP_template'] = $message;
        $returnArr['OTP'] = $phonemessage;
    } else {
        $phonemessage = mt_rand(1000, 9999);
        $key_arr = array('#OTP#','#TONAME#');
        $val_arr = array($phonemessage, $Data_Mail['ToName']);
        $message = str_replace($key_arr, $val_arr, $prefix);
        //$result = sendEmeSms($toMobileNum, $message);

        $result = $COMM_MEDIA_OBJ->SendSystemSMS($toMobileNum,$phoneCode,$message);
        $Data_Mail['OTP'] = $phonemessage;
        $sendemail = $COMM_MEDIA_OBJ->SendMailToMember($template_name, $Data_Mail);
        /* return appropriate msg if not sent OTP properly */
        if ($result == 0 && empty($sendemail)) { 
            $notifymessage = "-1";
        } else { 
            $notifymessage = $phonemessage;
        }

        $returnArr['OTP_template'] = $message;
        $returnArr['OTP'] = $phonemessage;
    }
    $returnArr['phonemessage'] = $notifymessage;
    return $returnArr;
}

function get_user_currency_balance_byDate($sess_iMemberId, $type) {
    global $obj, $SYSTEM_PAYMENT_FLOW,$userDetailsArr,$currencyAssociateArr,$vSystemDefaultCurrencyName,$vSystemDefaultCurrencySymbol;
    $tableName = "register_driver";
    $tblField = "iDriverId";
    $currencyField = "vCurrencyDriver";
    if ($type == "Rider") {
        $tableName = "register_user";
        $tblField = "iUserId";
        $currencyField = "vCurrencyPassenger";
    }
    //$db_currency = $obj->MySQLSelect($sqld);
    //Added By HJ On 25-07-2020 For Optimize register_user/register_driver Table Query Start
    if(isset($userDetailsArr[$tableName.'_'.$sess_iMemberId])){
        $db_currency = $userDetailsArr[$tableName.'_'.$sess_iMemberId];
    }else{
        $db_currency = $obj->MySQLSelect("SELECT *,$tblField as iMemberId FROM ".$tableName." WHERE $tblField='".$sess_iMemberId."'");
        $userDetailsArr[$tableName.'_'.$sess_iMemberId] = $db_currency;
    }
    $vCurrencyName = $db_currency[0][$currencyField];
    //Added By HJ On 25-07-2020 For Optimize register_user/register_driver Table Query End
    //Added By HJ On 25-07-2020 For Optimize currency Table Query Start
    if(isset($currencyAssociateArr[$vCurrencyName])){
        $db_currency[0]['Ratio'] = $currencyAssociateArr[$vCurrencyName]['Ratio'];
        $db_currency[0]['vSymbol'] = $currencyAssociateArr[$vCurrencyName]['vSymbol'];
    }else{
        $currencydata = $obj->MySQLSelect("SELECT Ratio,vSymbol from currency WHERE vName= '" . $vCurrencyName . "'");
        $db_currency[0]['Ratio'] = $currencydata[0]['Ratio'];
        $db_currency[0]['vSymbol'] = $currencydata[0]['vSymbol'];
    }
    //Added By HJ On 25-07-2020 For Optimize currency Table Query End
    //echo "<pre>";print_r($db_currency);die;
    // $vCurrency = $db_currency[0]['vCurrency'];
    $vCurrency = $vCurrencyName;
    $vSymbol = $db_currency[0]['vSymbol'];
    if ($vCurrency == "" || $vCurrency == null) {
         //Added By HJ On 25-07-2020 For Optimization currency Table Query Start
        if (!empty($vSystemDefaultCurrencyName) && !empty($vSystemDefaultCurrencySymbol)) {
            $currencyData = array();
            $currencyData[0]['vName'] = $vSystemDefaultCurrencyName;
            $currencyData[0]['vSymbol'] = $vSystemDefaultCurrencySymbol;
        }else{
            $currencyData = $obj->MySQLSelect("SELECT vName,vSymbol from currency WHERE eDefault = 'Yes'");
        }
        //Added By HJ On 25-07-2020 For Optimization currency Table Query End
        $vCurrency = $currencyData[0]['vName'];
        $vSymbol = $currencyData[0]['vSymbol'];
    }
    $getUserWallet = $obj->MySQLSelect("SELECT eType,SUM(iBalance*fRatio_" . $vCurrency . ") as totBalance FROM user_wallet WHERE iUserId = '" . $sess_iMemberId . "' AND eUserType = '" . $type . "' AND DATE(dDate) = CURDATE() AND eFor = 'Transfer' AND eType = 'Debit'");
    $debitBalance = $creditBalance = 0;
    $totBalance = $getUserWallet[0]['totBalance'];
    if(empty($totBalance)) $totBalance = 0;
    return $totBalance;
}
###################### Gojek-gopay end by SP ########################
?>
