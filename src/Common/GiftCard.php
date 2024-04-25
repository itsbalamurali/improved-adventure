<?php



namespace Kesk\Web\Common;

class GiftCard
{
    public function __construct()
    {
        $this->GiftCardImageTable = 'gift_card_images';
        $this->GiftCardTable = 'gift_cards';
    }

    public function getGiftCardImages($id = '')
    {
        global $obj, $tconfig, $LANG_OBJ, $Data_ALL_langArr;
        if (isset($_REQUEST['type']) && 'loadStaticInfo' === $_REQUEST['type']) {
            $all_images = $obj->MySQLSelect("SELECT * FROM {$this->GiftCardImageTable} WHERE eStatus = 'Active' ");
            $imageArr = [];
            foreach ($all_images as $image) {
                $imageArr[$image['vCode']][] = ['vImage' => $tconfig['tsite_upload_images_gift_card'].'/'.$image['vImage'], 'iGiftCardImageId' => $image['iGiftCardImageId']];
            }
            if (!empty($Data_ALL_langArr) && \count($Data_ALL_langArr) > 0) {
                $language_master = $Data_ALL_langArr;
            } else {
                $language_master = $obj->MySQLSelect('SELECT vCode FROM language_master ORDER BY iDispOrder ASC');
            }
            $langImagesArr = [];
            foreach ($language_master as $lang) {
                $langImagesArr[$lang['vCode']] = $imageArr[$lang['vCode']];
            }

            return $langImagesArr;
        }
        $vLangCode = isset($_REQUEST['vGeneralLang']) ? clean($_REQUEST['vGeneralLang']) : 'EN';
        if ('' === $vLangCode || null === $vLangCode) {
            $vLangCode = $LANG_OBJ->FetchDefaultLangData('vCode');
        }
        $sql = '';
        if ($id > 0) {
            $sql .= " AND iGiftCardImageId = {$id} ";
        }
        $sql = "SELECT * FROM {$this->GiftCardImageTable} WHERE vCode = '".$vLangCode."' AND eStatus = 'Active' {$sql} ORDER BY iDisplayOrder ASC";
        $data = $obj->MySQLSelect($sql);
        $i = 0;
        $imageArr = [];
        foreach ($data as $d) {
            $imageArr[$i]['vImage'] = $tconfig['tsite_upload_images_gift_card'].'/'.$d['vImage'];
            $imageArr[$i]['iGiftCardImageId'] = $d['iGiftCardImageId'];
            ++$i;
        }

        return $imageArr;
    }

    public function SendGiftCard(): void
    {
        global $obj, $tconfig, $COMM_MEDIA_OBJ, $LANG_OBJ;
        $MemberId = $_REQUEST['GeneralMemberId'] ?? '';
        $UserType = $_REQUEST['GeneralUserType'] ?? '';
        $fAmount = $_REQUEST['fAmount'] ?? '';
        $tReceiverName = $_REQUEST['tReceiverName'] ?? '';
        $tReceiverEmail = $_REQUEST['tReceiverEmail'] ?? '';
        $tReceiverMessage = $_REQUEST['tReceiverMessage'] ?? '';
        $vPhone = $_REQUEST['vReceiverPhone'] ?? '';
        $vPhoneCode = $_REQUEST['vReceiverPhoneCode'] ?? '';
        $iGiftCardImageId = $_REQUEST['iGiftCardImageId'] ?? '';
        $vTimeZone = $_REQUEST['vTimeZone'] ?? '';
        $payStatus = $_REQUEST['payStatus'] ?? '';
        $iPaymentId = $_REQUEST['payment_id'] ?? '';
        $SYSTEM_TYPE = $_REQUEST['SYSTEM_TYPE'] ?? 'APP';
        $vGiftCardCode = $this->GenerateGiftCardCode();
        $vLangCode = $LANG_OBJ->FetchDefaultLangData('vCode');
        if ('' === $vLangCode || null === $vLangCode) {
            $vLangCode = $LANG_OBJ->FetchDefaultLangData('vCode');
        }
        if ('Passenger' === $UserType) {
            $MemberData = $this->getUserData($MemberId);
            $UserTypeForDB = 'User';
        } else {
            $MemberData = $this->getUserData($MemberId, 'Driver');
            $UserTypeForDB = 'Driver';
        }
        $currency = $obj->MySQLSelect("SELECT ratio,vName FROM `currency` WHERE vName='".$MemberData['vCurrency']."'");
        $returnArr = $GenerateGiftCard = $ReceiverDetails = [];
        $id = '';
        $ReceiverDetails['tReceiverName'] = $tReceiverName;
        $ReceiverDetails['tReceiverEmail'] = $tReceiverEmail;
        $ReceiverDetails['tReceiverMessage'] = $tReceiverMessage;
        $ReceiverDetails['vReceiverPhone'] = $vPhone;
        $ReceiverDetails['vReceiverPhoneCode'] = $vPhoneCode;
        $ReceiverDetails = json_encode($ReceiverDetails);
        if ('succeeded' === $payStatus) {
            $fAmount_org = $fAmount;
            $fAmount /= $currency[0]['ratio'];
            $GenerateGiftCard['vGiftCardCode'] = $vGiftCardCode;
            $GenerateGiftCard['fAmount'] = $fAmount;
            $GenerateGiftCard['eStatus'] = 'Active';
            $GenerateGiftCard['dAddedDate'] = date('Y-m-d H:i:s');
            $GenerateGiftCard['iCreatedById'] = $MemberId;
            $GenerateGiftCard['eCreatedBy'] = $UserTypeForDB;
            $GenerateGiftCard['tReceiverDetails'] = $ReceiverDetails;
            $GenerateGiftCard['iGiftCardImageId'] = $iGiftCardImageId;
            if ($iPaymentId > 0) {
                $GenerateGiftCard['iPaymentId'] = $iPaymentId;
                $GenerateGiftCard['ePaymentOption'] = 'Card';
            }
            $GenerateGiftCard['ePaymentOption'] = 'Wallet';
            $id = $obj->MySQLQueryPerform($this->GiftCardTable, $GenerateGiftCard, 'insert');
            if ($iPaymentId > 0) {
                $where_payments = " iPaymentId = '".$iPaymentId."'";
                $data_payments['iGiftCardId'] = $id;
                $data_payments['eEvent'] = 'giftCard';
                $obj->MySQLQueryPerform('payments', $data_payments, 'update', $where_payments);
            }
            $payment_data = $obj->MySQLSelect("SELECT tPaymentUserID FROM payments WHERE iPaymentId = {$iPaymentId}");
            $transaction_id = $payment_data[0]['tPaymentUserID'];
            if (!empty($tReceiverEmail)) {
                $maildata['vEmail'] = $tReceiverEmail;
                $data = $COMM_MEDIA_OBJ->giftCardeMailSent($_REQUEST, $vGiftCardCode, $maildata, 'GIFT_CARD_RECEIVED');
            }
            $dataArraySMSNew['RECEIVER_NAME'] = $tReceiverName;
            $dataArraySMSNew['GIFT_CARD_CODE'] = $vGiftCardCode;
            $dataArraySMSNew['SENDER_NAME'] = $MemberData['userName'];
            $dataArraySMSNew['AMOUNT'] = formateNumAsPerCurrency($fAmount_org, $MemberData['vCurrency']);
            $message = $COMM_MEDIA_OBJ->GetSMSTemplate('GIFT_CARD_RECEIVED', $dataArraySMSNew, '', $vLangCode);
            $result = $COMM_MEDIA_OBJ->SendSystemSMS($vPhone, $vPhoneCode, $message);
            if (!empty($MemberData['vEmail'])) {
                $_REQUEST['EMAIL_TYPE'] = 'GiftCardGenerate';
                $maildata = [];
                $maildata['vEmail'] = $MemberData['vEmail'];
                $data = $COMM_MEDIA_OBJ->giftCardeMailSent($_REQUEST, $vGiftCardCode, $maildata, 'GIFT_CARD_INFO_SEND');
            }
            $dataArraySMSNew = [];
            $dataArraySMSNew['SENDER_NAME'] = $MemberData['userName'];
            $dataArraySMSNew['AMOUNT'] = formateNumAsPerCurrency($fAmount_org, $MemberData['vCurrency']);
            $dataArraySMSNew['RECEIVER_NAME'] = $tReceiverName;
            $dataArraySMSNew['GIFT_CARD_CODE'] = $vGiftCardCode;
            $message = $COMM_MEDIA_OBJ->GetSMSTemplate('GIFT_CARD_INFO_SEND', $dataArraySMSNew, '', $vLangCode);
            $result = $COMM_MEDIA_OBJ->SendSystemSMS($MemberData['vPhone'], $MemberData['vPhoneCode'], $message);
            $returnUrl = $tconfig['tsite_url'].'assets/libraries/webview/success.php';
            header('Location:'.$returnUrl.'?message=LBL_PAYMENT_SUCCESS_TXT&SYSTEM_TYPE='.$SYSTEM_TYPE.'&TIME='.time().'&PAGE_TYPE=GIFT_CARD_PAYMENT&iTransactionId='.$transaction_id.'.&giftCode='.$vGiftCardCode);
        } else {
            $current_url_arr['type'] = $_REQUEST['type'];
            $current_url_arr['iMemberId'] = $MemberId;
            $current_url_arr['tReceiverEmail'] = $tReceiverEmail;
            $current_url_arr['vReceiverPhone'] = $vPhone;
            $current_url_arr['iGiftCardImageId'] = $iGiftCardImageId;
            $current_url_arr['fAmount'] = $fAmount;
            $current_url_arr['vReceiverPhoneCode'] = $vPhoneCode;
            $current_url_arr['tReceiverName'] = $tReceiverName;
            $current_url_arr['tReceiverMessage'] = $tReceiverMessage;
            $current_url_arr['GeneralMemberId'] = $MemberId;
            $current_url_arr['GeneralUserType'] = $UserType;
            $current_url_arr['vTimeZone'] = $vTimeZone;
            $current_url_arr['SYSTEM_TYPE'] = $SYSTEM_TYPE;
            $current_url_arr = http_build_query($current_url_arr);
            $GiftCardAmount = $fAmount;
            $system_default_GiftCardAmount = $fAmount / $currency[0]['ratio'];
            $tDescription = 'Payment for gift card.';
            $current_url = (isset($_SERVER['HTTPS']) ? 'https' : 'http').'://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'&TIME='.time();
            $extraParams = '&ePaymentType=GiftCardPayment&tSessionId='.$MemberData['tSessionId'].'&GeneralMemberId='.$MemberId.'&GeneralUserType='.$UserType.'&iServiceId=&AMOUNT='.$GiftCardAmount.'&SYSTEM_DEFAULT_AMOUNT='.$system_default_GiftCardAmount.'&PAGE_TYPE=GIFT_CARD_PAYMENT&SYSTEM_TYPE=APP&IS_RETURN_RESULT=Yes&description='.urlencode($tDescription).'&IsReturnUrlEncode=1&returnUrl='.base64_encode($current_url_arr);
            $returnArr['GIFT_CARD_PAYMENT_URL'] = $tconfig['tsite_url'].'assets/libraries/webview/payment_mode_select.php?'.$extraParams;
            $returnArr['Action'] = '1';
        }
        setDataResponse($returnArr);
    }

    public function GenerateGiftCardCode()
    {
        global $obj;
        $random = RandomString(10, 'Yes');
        $db_str = $obj->MySQLSelect("SELECT vGiftCardCode FROM {$this->GiftCardTable} WHERE vGiftCardCode ='".$random."'");
        if (!empty($db_str) && \count($db_str) > 0) {
            $code = GenerateGiftCardCode();
        } else {
            $code = $random;
        }

        return $code;
    }

    public function getUserData($UserId, $userType = 'Passenger')
    {
        global $obj;
        if ('Passenger' === $userType || 'User' === $userType) {
            $row = $obj->MySQLSelect("SELECT vEmail,vPhoneCode,vPhone,tSessionId, vCurrencyPassenger as vCurrency, vName, vLastName, CONCAT(vName , ' ' , vLastName) as userName , vLang as lang FROM `register_user` WHERE iUserId = '{$UserId}'");
        } else {
            $row = $obj->MySQLSelect("SELECT vEmail,vCode as vPhoneCode , vPhone,tSessionId, vCurrencyDriver as vCurrency, vName, vLastName,CONCAT(vName , ' ' , vLastName) as userName ,vLang as lang FROM `register_driver` WHERE iDriverId = '{$UserId}'");
        }

        return $row[0];
    }

    public function duplicateCode($code)
    {
        global $obj;
        $db_str = $obj->MySQLSelect("SELECT vGiftCardCode FROM {$this->GiftCardTable} WHERE vGiftCardCode ='".$code."'");
        if (!empty($db_str) && \count($db_str) > 0) {
            $code = 0;
        } else {
            $code = 1;
        }

        return $code;
    }

    public function getDriverData($DriverId)
    {
        global $obj;
        $row = $obj->MySQLSelect("SELECT tSessionId, vCurrencyDriver, vName, vLastName,vLang FROM `register_driver` WHERE iDriverId = '{$DriverId}'");
        $arr = [];
        $arr['vCurrency'] = $row[0]['vCurrencyDriver'];
        $arr['lang'] = $row[0]['vLang'];

        return $arr;
    }

    public function RedeemGiftCard(): void
    {
        global $obj, $WALLET_OBJ, $COMM_MEDIA_OBJ;
        $MemberId = $_REQUEST['GeneralMemberId'] ?? '';
        $UserType = $_REQUEST['UserType'] ?? '';
        $vGiftCardCode = $_REQUEST['vGiftCardCode'] ?? '';
        if ('Passenger' === $UserType) {
            $UserTypeForDB = 'Passenger';
            $eUserType_DB = 'UserSpecific';
            $wallet_user_type = 'Rider';
        } else {
            $wallet_user_type = $UserTypeForDB = 'Driver';
            $eUserType_DB = 'DriverSpecific';
        }
        $GiftCardData = $obj->MySQLSelect("SELECT eRedeemed,iCreatedById, eCreatedBy,fAmount,eCreatedBy,iGiftCardId, eUserType FROM {$this->GiftCardTable} WHERE vGiftCardCode = '".$vGiftCardCode."' AND eStatus = 'Active' ");
        if (!empty($GiftCardData) && \count($GiftCardData) > 0 && 'No' === $GiftCardData[0]['eRedeemed']) {
            $dRedeemedDate = date('Y-m-d H:i:s');
            $updateArr['eRedeemed'] = 'Yes';
            $updateArr['dRedeemedDate'] = $dRedeemedDate;
            $updateArr['eReceiverId'] = $MemberId;
            $updateArr['eReceiverUserType'] = $UserTypeForDB;
            if ('Admin' === $GiftCardData[0]['eCreatedBy'] && 'Anyone' !== $GiftCardData[0]['eUserType']) {
                $WHERE = " vGiftCardCode = '".$vGiftCardCode."' AND iMemberId = '".$MemberId."' AND eUserType = '".$eUserType_DB."' ";
                $validGiftCard = $obj->MySQLSelect("SELECT eCreatedBy,iGiftCardId FROM {$this->GiftCardTable} WHERE eRedeemed = 'No' AND {$WHERE}");
                if (!empty($validGiftCard) && \count($validGiftCard) > 0) {
                } else {
                    $returnArr['Action'] = '0';
                    $returnArr['message'] = 'LBL_GIFT_CARD_CODE_INVALID';
                    setDataResponse($returnArr);
                }
            } else {
                $WHERE = " vGiftCardCode = '".$vGiftCardCode."'";
            }
            $obj->MySQLQueryPerform($this->GiftCardTable, $updateArr, 'update', $WHERE);
            $data_user_wallet['iUserId'] = $MemberId;
            $data_user_wallet['eUserType'] = $wallet_user_type;
            $data_user_wallet['iBalance'] = $GiftCardData[0]['fAmount'];
            $data_user_wallet['eType'] = 'Credit';
            $data_user_wallet['dDate'] = @date('Y-m-d H:i:s');
            $data_user_wallet['iTripId'] = '';
            $data_user_wallet['eFor'] = 'Gift card';
            $data_user_wallet['ePaymentStatus'] = 'Settelled';
            $data_user_wallet['tDescription'] = '#LBL_GIFT_CARD_CREDITED#'.'Code : '.strtoupper($vGiftCardCode);
            $data_user_wallet['iOrderId'] = '';
            $WALLET_OBJ->PerformWalletTransaction($data_user_wallet['iUserId'], $data_user_wallet['eUserType'], $data_user_wallet['iBalance'], $data_user_wallet['eType'], $data_user_wallet['iTripId'], $data_user_wallet['eFor'], $data_user_wallet['tDescription'], $data_user_wallet['ePaymentStatus'], $data_user_wallet['dDate'], $data_user_wallet['iOrderId']);
            $MemberData = $this->getUserData($GiftCardData[0]['iCreatedById'], $GiftCardData[0]['eCreatedBy']);
            if (!empty($MemberData['vEmail'])) {
                $_REQUEST['EMAIL_TYPE'] = 'GiftCardRedeemMailToSender';
                $_REQUEST['iGiftCardId'] = $GiftCardData[0]['iGiftCardId'];
                $maildata = [];
                $maildata['vEmail'] = $MemberData['vEmail'];
                $data = $COMM_MEDIA_OBJ->giftCardeMailSent($_REQUEST, $vGiftCardCode, $maildata, 'GIFT_CARD_REDEEM_SUCCESSFULLY_SENT_TO_SENDER');
            }
            $MemberData = $this->getUserData($MemberId, $UserType);
            if (!empty($MemberData['vEmail'])) {
                $_REQUEST['EMAIL_TYPE'] = 'GiftCardRedeemMailToReceiver';
                $_REQUEST['iGiftCardId'] = $GiftCardData[0]['iGiftCardId'];
                $maildata = [];
                $maildata['vEmail'] = $MemberData['vEmail'];
                $data = $COMM_MEDIA_OBJ->giftCardeMailSent($_REQUEST, $vGiftCardCode, $maildata, 'GIFT_CARD_REDEEM_SUCCESSFULLY_SENT_TO_SENDER');
            }
            $returnArr['Action'] = '1';
            $returnArr['message'] = 'LBL_GIFT_CARD_CODE_REDEEM_SUCCESS_MSG';
            $returnArr['message_title'] = 'LBL_GIFT_CARD_CODE_REDEEM_SUCCESS_TITLE';
        } elseif (!empty($GiftCardData) && \count($GiftCardData) > 0 && 'Yes' === $GiftCardData[0]['eRedeemed']) {
            $returnArr['Action'] = '0';
            $returnArr['message'] = 'LBL_GIFT_CARD_CODE_ALREADY_USED';
        } else {
            $returnArr['Action'] = '0';
            $returnArr['message'] = 'LBL_GIFT_CARD_CODE_INVALID';
        }
        setDataResponse($returnArr);
    }

    public function getGiftCardById($iGiftCardId)
    {
        global $obj;
        $GiftCardData = $obj->MySQLSelect("SELECT vGiftCardCode , eReceiverUserType , eReceiverId, iCreatedById, eCreatedBy,fAmount,eCreatedBy,iGiftCardId, eUserType FROM {$this->GiftCardTable} WHERE iGiftCardId = '".$iGiftCardId."' ");

        return $GiftCardData;
    }

    public function PreviewGiftCard(): void
    {
        global $tconfig;
        $ReceiverName = $_REQUEST['ReceiverName'] ?? '';
        $Amount = $_REQUEST['Amount'] ?? '';
        $GiftCardImageId = $_REQUEST['GiftCardImageId'] ?? '';
        $GeneralMemberId = $_REQUEST['GeneralMemberId'] ?? '';
        $GeneralUserType = $_REQUEST['GeneralUserType'] ?? '';
        $SenderMsg = $_REQUEST['SenderMsg'] ?? '';
        $returnArr['Action'] = '1';
        $returnArr['GIFT_CARD_PREVIEW_URL'] = $tconfig['tsite_url'].'preview_gift_card.php?ReceiverName='.$ReceiverName.'&Amount='.$Amount.'&GiftCardImageId='.$GiftCardImageId.'&GeneralMemberId='.$GeneralMemberId.'&GeneralUserType='.$GeneralUserType.'&SenderMsg='.$SenderMsg;
        setDataResponse($returnArr);
    }

    public function GetGiftCardDetails()
    {
        global $obj, $tconfig, $GIFT_CARD_MAX_AMOUNT, $Data_ALL_currency_Arr;
        $GeneralMemberId = $_REQUEST['GeneralMemberId'] ?? '';
        $GeneralUserType = $_REQUEST['GeneralUserType'] ?? '';
        $vCurrency = $_REQUEST['vCurrency'] ?? '';
        $vGeneralLang = $_REQUEST['vGeneralLang'] ?? '';
        if ('Driver' === $GeneralUserType) {
            $tbl_name = 'register_driver';
            $currencyField = 'vCurrencyDriver';
            $memberField = 'iDriverId';
        } else {
            $tbl_name = 'register_user';
            $currencyField = 'vCurrencyPassenger';
            $memberField = 'iUserId';
        }
        if (!empty($GeneralMemberId)) {
            $memberData = $obj->MySQLSelect("SELECT {$currencyField} as vCurrency FROM {$tbl_name} WHERE {$memberField} = '{$GeneralMemberId}' ");
            $vCurrency = $memberData[0]['vCurrency'];
        }
        $returnData['GIFT_CARD_IMAGES'] = $this->getGiftCardImages();
        $returnData['GIFT_CARD_MAX_AMOUNT'] = $GIFT_CARD_MAX_AMOUNT;
        $returnData['GIFT_CARD_MAX_AMOUNT_WITH_SYMBOL'] = formateNumAsPerCurrency($GIFT_CARD_MAX_AMOUNT, $vCurrency);
        $returnData['PREVIEW_GIFT_CARD_URL'] = $tconfig['tsite_url'].'preview_gift_card.php?';
        $returnData['TERMS_&_CONDITIONS_GIFT_CARD_URL'] = $tconfig['tsite_url'].'terms_conditions_gift_card.php';
        if (isset($_REQUEST['type']) && 'loadStaticInfo' === $_REQUEST['type']) {
            if (!empty($Data_ALL_currency_Arr) && \count($Data_ALL_currency_Arr) > 0) {
                $currency_master = $Data_ALL_currency_Arr;
            } else {
                $currency_master = $obj->MySQLSelect('SELECT vName FROM currency ORDER BY iDispOrder ASC');
            }
            $returnData['GIFT_CARD_MAX_AMOUNT_WITH_SYMBOL'] = [];
            foreach ($currency_master as $currency) {
                $returnData['GIFT_CARD_MAX_AMOUNT_WITH_SYMBOL'][$currency['vName']] = formateNumAsPerCurrency($GIFT_CARD_MAX_AMOUNT, $currency['vName']);
            }
        }

        return $returnData;
    }
}
