<?php

class CommMedia
{
    public function __construct()
    {
    }

    public function GetSMSTemplate($type, $db_rec = '', $newsid = '', $maillanguage = '')
    {
        global $obj, $SITE_NAME;
        $str = "select * from send_message_templates where vEmail_Code='" . $type . "'";
        $res = $obj->MySQLSelect($str);
        $key_arr = $val_arr = array();
        switch ($type) {
            case "DRIVER_SEND_MESSAGE":
                $key_arr = array("#PASSENGER_NAME#", "#BOOKING_DATE#", "#BOOKING_TIME#", "#BOOKING_NUMBER#");
                $val_arr = array($db_rec['PASSENGER_NAME'], $db_rec['BOOKING_DATE'], $db_rec['BOOKING_TIME'], $db_rec['BOOKING_NUMBER']);
                break;
            case "DRIVER_SEND_MESSAGE_SP":
                $key_arr = array("#Booking_Number#");
                $val_arr = array($db_rec['BOOKING_NUMBER']);
                break;
            case "USER_SEND_MESSAGE_AUTOASSIGN":
                $key_arr = array("#PLATE_NUMBER#", "#BOOKING_DATE#", "#BOOKING_TIME#", "#BOOKING_NUMBER#");
                $val_arr = array($db_rec['PLATE_NUMBER'], $db_rec['BOOKING_DATE'], $db_rec['BOOKING_TIME'], $db_rec['BOOKING_NUMBER']);
                break;
            case "USER_SEND_MESSAGE":
                $key_arr = array("#DRIVER_NAME#", "#PLATE_NUMBER#", "#BOOKING_DATE#", "#BOOKING_TIME#", "#BOOKING_NUMBER#");
                $val_arr = array($db_rec['DRIVER_NAME'], $db_rec['PLATE_NUMBER'], $db_rec['BOOKING_DATE'], $db_rec['BOOKING_TIME'], $db_rec['BOOKING_NUMBER']);
                break;
            case "USER_SEND_MESSAGE_APP":
                $key_arr = array("#DRIVER_NAME#", "#BOOKING_DATE#", "#BOOKING_TIME#", "#BOOKING_NUMBER#");
                $val_arr = array($db_rec['DRIVER_NAME'], $db_rec['BOOKING_DATE'], $db_rec['BOOKING_TIME'], $db_rec['BOOKING_NUMBER']);
                break;
            case "USER_SEND_MESSAGE_JOB_CANCEL":
                $key_arr = array("#Driver#", "#Ddate#", "#Bookingtime#", "#BookingNo#");
                $val_arr = array($db_rec['vDriver'], $db_rec['dBookingdate'], $db_rec['dBookingtime'], $db_rec['vBookingNo']);
                break;
            case "DRIVER_SEND_MESSAGE_JOB_CANCEL":
                $key_arr = array("#Rider#", "#Ddate#", "#Bookingtime#", "#BookingNo#");
                $val_arr = array($db_rec['vRider'], $db_rec['dBookingdate'], $db_rec['dBookingtime'], $db_rec['vBookingNo']);
                break;
            case "BOOKING_ACCEPT_BYDRIVER_MESSAGE_SP":
                $key_arr = array("#PROVIDER_NAME#", "#BOOKING_NUMBER#");
                $val_arr = array($db_rec['vDriver'], $db_rec['vBookingNo']);
                break;
            case "BOOKING_DECLINED_BYDRIVER_MESSAGE_SP":
                $key_arr = array("#PROVIDER_NAME#", "#BOOKING_NUMBER#");
                $val_arr = array($db_rec['vDriver'], $db_rec['vBookingNo']);
                break;
            case "BOOKING_CANCEL_BYRIDER_MESSAGE_SP":
                $key_arr = array("#USERNAME#", "#BOOKING_NUMBER#");
                $val_arr = array($db_rec['RiderName'], $db_rec['vBookingNo']);
                break;
            case "BOOKING_CANCEL_BYDRIVER_MESSAGE_SP":
                $key_arr = array("#PROVIDERNAME#", "#BOOKING_NUMBER#");
                $val_arr = array($db_rec['DriverName'], $db_rec['vBookingNo']);
                break;
            case "EMERGENCY_SMS_FOR_USER_SP":
                $key_arr = array("#PassengerName#", "#PassengerPhone#", "#StartDate#", "#Saddress#", "#DriverName#", "#DriverPhone#", "#SITE_NAME#", "#LIVETRACKINGURL#");
                $val_arr = array($db_rec['vPassengerName'], $db_rec['PassengerPhone'], $db_rec['tStartDate'], $db_rec['tSaddress'], $db_rec['vDriverName'], $db_rec['DriverPhone'], $db_rec['SITE_NAME'], $db_rec['trackingURL']);
                break;
            case "EMERGENCY_SMS_FOR_DRIVER_SP":
                $key_arr = array("#PassengerName#", "#PassengerPhone#", "#StartDate#", "#Saddress#", "#DriverName#", "#DriverPhone#", "#SITE_NAME#", "#LIVETRACKINGURL#");
                $val_arr = array($db_rec['vPassengerName'], $db_rec['PassengerPhone'], $db_rec['tStartDate'], $db_rec['tSaddress'], $db_rec['vDriverName'], $db_rec['DriverPhone'], $db_rec['SITE_NAME'], $db_rec['trackingURL']);
                break;
            /*case "EMERGENCY_SMS_FOR_USER_DELIVERY":
               $key_arr = array("#PassengerName#", "#PassengerPhone#", "#StartDate#", "#Saddress#", "#DriverName#", "#DriverPhone#", "#SITE_NAME#", "#LicencePlate#");
               $val_arr = array($db_rec['PassengerName'], $db_rec['PassengerPhone'], $db_rec['StartDate'], $db_rec['Saddress'], $db_rec['DriverName'], $db_rec['DriverPhone'], $db_rec['SITE_NAME'], $db_rec['LicencePlate']);
               break;
            case "EMERGENCY_SMS_FOR_DRIVER_DELIVERY":
               $key_arr = array("#PassengerName#", "#PassengerPhone#", "#StartDate#", "#Saddress#", "#DriverName#", "#DriverPhone#", "#SITE_NAME#", "#LicencePlate#");
               $val_arr = array($db_rec['PassengerName'], $db_rec['PassengerPhone'], $db_rec['StartDate'], $db_rec['Saddress'], $db_rec['DriverName'], $db_rec['DriverPhone'], $db_rec['SITE_NAME'], $db_rec['LicencePlate']);
               break;
            case "EMERGENCY_SMS_FOR_DRIVER_SP_RIDER":
               $key_arr = array("#SITE_NAME#", "#DriverName#", "#DriverPhone#", "#StartDate#", "#Saddress#", "#PassengerName#", "#PassengerPhone#", "#LICENCEPLATE#", "#LIVETRACKINGURL#");
               $val_arr = array($db_rec['SITE_NAME'], $db_rec['vDriverName'], $db_rec['DriverPhone'], $db_rec['tStartDate'], $db_rec['tSaddress'], $db_rec['vPassengerName'], $db_rec['PassengerPhone'], $db_rec['vLicencePlate'], $db_rec['trackingURL']);
               break;
            case "EMERGENCY_SMS_FOR_USER_SP_RIDER":
               $key_arr = array("#SITE_NAME#", "#DriverName#", "#DriverPhone#", "#StartDate#", "#Saddress#", "#PassengerName#", "#PassengerPhone#", "#LICENCEPLATE#", "#LIVETRACKINGURL#");
               $val_arr = array($db_rec['SITE_NAME'], $db_rec['vDriverName'], $db_rec['DriverPhone'], $db_rec['tStartDate'], $db_rec['tSaddress'], $db_rec['vPassengerName'], $db_rec['PassengerPhone'], $db_rec['vLicencePlate'], $db_rec['trackingURL']);
               break;*/
            case "EMERGENCY_SMS_FOR_USER_RIDE":
                $key_arr = array("#PassengerName#", "#PassengerPhone#", "#StartDate#", "#Saddress#", "#DriverName#", "#DriverPhone#", "#SITE_NAME#", "#LicencePlate#");
                $val_arr = array($db_rec['PassengerName'], $db_rec['PassengerPhone'], $db_rec['StartDate'], $db_rec['Saddress'], $db_rec['DriverName'], $db_rec['DriverPhone'], $db_rec['SITE_NAME'], $db_rec['LicencePlate']);
                break;
            case "EMERGENCY_SMS_FOR_DRIVER_RIDE":
                $key_arr = array("#PassengerName#", "#PassengerPhone#", "#StartDate#", "#Saddress#", "#DriverName#", "#DriverPhone#", "#SITE_NAME#", "#LicencePlate#");
                $val_arr = array($db_rec['PassengerName'], $db_rec['PassengerPhone'], $db_rec['StartDate'], $db_rec['Saddress'], $db_rec['DriverName'], $db_rec['DriverPhone'], $db_rec['SITE_NAME'], $db_rec['LicencePlate']);
                break;
            case "BOOK_FOR_SOMEONE_ELSE_SMS":
                $key_arr = array("#VEHICLE_TYPE#", "#CAR_NUMBER#", "#DRIVER_NAME#", "#DRIVER_NUMBER#", "#BOOKER_NAME#", "#BOOK_OTP#", "#PAYMENT_MODE#", "#LIVE_TRACKING_URL#");
                $val_arr = array($db_rec['VEHICLE_TYPE'], $db_rec['CAR_NUMBER'], $db_rec['DRIVER_NAME'], $db_rec['DRIVER_NUMBER'], $db_rec['BOOKER_NAME'], $db_rec['BOOK_OTP'], $db_rec['PAYMENT_MODE'], $db_rec['LIVE_TRACKING_URL']);
                break;
            case "BOOK_FOR_SOMEONE_ELSE_SMS_FLY":
                $key_arr = array("#VEHICLE_TYPE#", "#CAR_NUMBER#", "#DRIVER_NAME#", "#DRIVER_NUMBER#", "#BOOKER_NAME#", "#BOOK_OTP#", "#PAYMENT_MODE#");
                $val_arr = array($db_rec['VEHICLE_TYPE'], $db_rec['CAR_NUMBER'], $db_rec['DRIVER_NAME'], $db_rec['DRIVER_NUMBER'], $db_rec['BOOKER_NAME'], $db_rec['BOOK_OTP'], $db_rec['PAYMENT_MODE']);
                break;
            case "BOOKING_IN_KIOSK":
                $key_arr = array("#vRideNo#", "#Driver_Name#", "#driverno#");
                $val_arr = array($db_rec['vRideNo'], $db_rec['DRIVER_NAME'], $db_rec['DRIVER_NUMBER']);
                break;
            case "EMERGENCY_SMS_FOR_DRIVER_SP_DELIVER":
                $key_arr = array("#SITE_NAME#", "#DriverName#", "#DriverPhone#", "#StartDate#", "#Saddress#", "#PassengerName#", "#PassengerPhone#", "#LICENCEPLATE#", "#LIVETRACKINGURL#");
                $val_arr = array($db_rec['SITE_NAME'], $db_rec['vDriverName'], $db_rec['DriverPhone'], $db_rec['tStartDate'], $db_rec['tSaddress'], $db_rec['vPassengerName'], $db_rec['PassengerPhone'], $db_rec['vLicencePlate'], $db_rec['trackingURL']);
                break;
            case "EMERGENCY_SMS_FOR_USER_SP_DELIVER":
                $key_arr = array("#SITE_NAME#", "#DriverName#", "#DriverPhone#", "#StartDate#", "#Saddress#", "#PassengerName#", "#PassengerPhone#", "#LICENCEPLATE#", "#LIVETRACKINGURL#");
                $val_arr = array($db_rec['SITE_NAME'], $db_rec['vDriverName'], $db_rec['DriverPhone'], $db_rec['tStartDate'], $db_rec['tSaddress'], $db_rec['vPassengerName'], $db_rec['PassengerPhone'], $db_rec['vLicencePlate'], $db_rec['trackingURL']);
                break;
            case "EMERGENCY_SMS_FOR_USER_SP":
                $key_arr = array("#SITE_NAME#", "#DriverName#", "#DriverPhone#", "#StartDate#", "#Saddress#", "#PassengerName#", "#PassengerPhone#", "#LIVETRACKINGURL#");
                $val_arr = array($db_rec['SITE_NAME'], $db_rec['vDriverName'], $db_rec['DriverPhone'], $db_rec['tStartDate'], $db_rec['tSaddress'], $db_rec['vPassengerName'], $db_rec['PassengerPhone'], $db_rec['trackingURL']);
                break;
            case "EMERGENCY_SMS_FOR_DRIVER_SP":
                $key_arr = array("#SITE_NAME#", "#DriverName#", "#DriverPhone#", "#StartDate#", "#Saddress#", "#PassengerName#", "#PassengerPhone#", "#LIVETRACKINGURL#");
                $val_arr = array($db_rec['SITE_NAME'], $db_rec['vDriverName'], $db_rec['DriverPhone'], $db_rec['tStartDate'], $db_rec['tSaddress'], $db_rec['vPassengerName'], $db_rec['PassengerPhone'], $db_rec['trackingURL']);
                break;
            case "DELIVER_SMS_TO_RECEIVER_ONE":
                $key_arr = array("#RECEPIENT_NAME#", "#SENDER_NAME#", "#DELIVERY_ADDRESS#", "#DELIVERY_CONFIRM_CODE#", "#PAGELINK#");
                $val_arr = array($db_rec['recepientName'], $db_rec['SenderName'], $db_rec['deliveryAddress'], $db_rec['vDeliveryConfirmCode'], $db_rec['pageLink']);
                break;
            case "DELIVER_SMS_TO_RECEIVER_TWO":
                $key_arr = array("#RECEPIENT_NAME#", "#SENDER_NAME#", "#DELIVERY_ADDRESS#", "#PAGELINK#");
                $val_arr = array($db_rec['recepientName'], $db_rec['SenderName'], $db_rec['deliveryAddress'], $db_rec['pageLink']);
                break;
            case "DELIVER_SMS_TO_RECEIVER_THREE":
                $key_arr = array("#SENDER_NAME#", "#DELIVERY_ADDRESS#", "#DELIVERY_CONFIRM_CODE#", "#PAGELINK#");
                $val_arr = array($db_rec['SenderName'], $db_rec['deliveryAddress'], $db_rec['vDeliveryConfirmCode'], $db_rec['pageLink']);
                break;
            case "SEND_CHARGES__VARIFICATIONCODE":
                $to_email = $db_rec['vEmail'];
                $key_arr = array("#vName#", "#PROVIDER_NAME#", '#TRIP_NUMBER#', '#MATERIAL_FEE#', '#MISC_FEE#', '#PROVIDER_DISCOUNT#', '#TOTALAMOUNT#', '#SERVICECOST#', '#VERIFICATION_CODE#', '#MAILFOOTER#');
                $val_arr = array($db_rec['vName'], $db_rec['ProviderName'], $db_rec['TripId'], $db_rec['fMaterialFee'], $db_rec['fMiscFee'], $db_rec['fDriverDiscount'], $db_rec['totalAmount'], $db_rec['serviceCost'], $db_rec['VerificationCode'], $MAIL_FOOTER);
                break;
            case "SEND_CHARGES__APPROVE":
                $to_email = $db_rec['vEmail'];
                $key_arr = array("#vName#", "#PROVIDER_NAME#", '#TRIP_NUMBER#', '#MATERIAL_FEE#', '#MISC_FEE#', '#PROVIDER_DISCOUNT#', '#VERIFICATION_CODE#', '#TOTALAMOUNT#', '#SERVICECOST#', '#MAILFOOTER#');
                $val_arr = array($db_rec['vName'], $db_rec['ProviderName'], $db_rec['TripId'], $db_rec['fMaterialFee'], $db_rec['fMiscFee'], $db_rec['fDriverDiscount'], $db_rec['AcceptLink'], $db_rec['totalAmount'], $db_rec['serviceCost'], $MAIL_FOOTER);
                break;
            case "CUSTOMER_RESET_PASSWORD":
                $key_arr = array("#LINK#", "#NAME#");
                $val_arr = array($db_rec['LINK'], $db_rec['NAME']);
                break;
            case "START_TRIP_OTP":
                $key_arr = array("#OTP#", '#SITE_NAME#', '#DRIVER#');
                $val_arr = array($db_rec['OTP'], $SITE_NAME, $db_rec['DRIVER']);
                break;
            case "USER_SEND_MESSAGE_BOOKING_CANCEL":
                $key_arr = array("#BookingNo#", "#Ddate#");
                $val_arr = array($db_rec['vBookingNo'], $db_rec['dCancelDate']);
                break;
            case "DRIVER_SEND_MESSAGE_BOOKING_CANCEL":
                $key_arr = array("#BookingNo#", "#Ddate#");
                $val_arr = array($db_rec['vBookingNo'], $db_rec['dCancelDate']);
                break;
            case "DRIVER_NOTIFY_BID_TASK":
                $key_arr = array("#PASSENGER_NAME#", "#BOOKING_DATE#", "#BOOKING_TIME#", "#TASK_NO#");
                $val_arr = array($db_rec['PASSENGER_NAME'], $db_rec['BOOKING_DATE'], $db_rec['BOOKING_TIME'], $db_rec['TASK_NO']);
                break;
            case "TRACK_COMPANY_USER_INVITECODE_SEND":
                $key_arr = array("#NAME#", "#INVITECODE#", "#SITE_NAME#");
                $val_arr = array($db_rec['NAME'], $db_rec['INVITECODE'], $SITE_NAME);
                break;
            case "GIFT_CARD_RECEIVED":
                $key_arr = array("#RECEIVER_NAME#", "#GIFT_CARD_CODE#", "#SENDER_NAME#", "#AMOUNT#", "#SITE_NAME#");
                $val_arr = array($db_rec['RECEIVER_NAME'], $db_rec['GIFT_CARD_CODE'], $db_rec['SENDER_NAME'], $db_rec['AMOUNT'], $SITE_NAME);
                break;
            case "GIFT_CARD_INFO_SEND":
                $key_arr = array("#SENDER_NAME#", "#AMOUNT#", "#RECEIVER_NAME#", "#GIFT_CARD_CODE#", "#SITE_NAME#");
                $val_arr = array($db_rec['SENDER_NAME'], $db_rec['AMOUNT'], $db_rec['RECEIVER_NAME'], $db_rec['GIFT_CARD_CODE'], $SITE_NAME);
                break;
            case "ORDER_ACCEPTED_KIOSK":
                $key_arr = array("#ORDER_NO#");
                $val_arr = array($db_rec['vOrderNo']);
                break;
            case "RIDE_SHARE_BOOKING_NOTIFY_PUBLISHER":
                $key_arr = array("#PUBLISHER#", "#BOOKING_NO#");
                $val_arr = array($db_rec['vPublisherName'], $db_rec['vBookingNo']);
                break;
            case "USER_RENT_ITEM_INQUIRY":
                $key_arr = array("#USER_NAME#", "#EMAIL#", "#PHONE#","#RENTITEMNAME#","#RENT_POST_NO#");
                $val_arr = array($db_rec['RiderName'], $db_rec['vEmail'], $db_rec['cellno'], $db_rec['RentItemName'], $db_rec['vRentItemPostNo']);
                break;
            case "AUTH_OTP":
                $key_arr = array("#OTP#", "#SITE_NAME#");
                $val_arr = array($db_rec['OTP'], $SITE_NAME);
                break;
        }
        $maillanguage = (isset($maillanguage) && $maillanguage != '') ? $maillanguage : 'EN';
        $mailsubject = $res[0]['vSubject_' . $maillanguage];
        $tMessage = $res[0]['vBody_' . $maillanguage];
        $tMessage = str_replace($key_arr, $val_arr, $tMessage);
        return $tMessage;
    }

    public function SendSystemSMS($mobileNo, $phonecode, $message)
    {
        require_once TPATH_CLASS . 'twilio/Services/Twilio.php';
        global $MOBILE_VERIFY_SID_TWILIO, $MOBILE_VERIFY_TOKEN_TWILIO, $MOBILE_NO_TWILIO, $SITE_ISD_CODE, $CONFIG_OBJ;
        if (empty($MOBILE_VERIFY_SID_TWILIO)) {
            $MOBILE_VERIFY_SID_TWILIO = $CONFIG_OBJ->getConfigurations("configurations", "MOBILE_VERIFY_SID_TWILIO");
        }
        if (empty($MOBILE_VERIFY_TOKEN_TWILIO)) {
            $MOBILE_VERIFY_TOKEN_TWILIO = $CONFIG_OBJ->getConfigurations("configurations", "MOBILE_VERIFY_TOKEN_TWILIO");
        }
        if (empty($MOBILE_NO_TWILIO)) {
            $MOBILE_NO_TWILIO = $CONFIG_OBJ->getConfigurations("configurations", "MOBILE_NO_TWILIO");
        }
        if (empty($SITE_ISD_CODE)) {
            $SITE_ISD_CODE = $CONFIG_OBJ->getConfigurations("configurations", "SITE_ISD_CODE");
        }
        $twilioMobileNum = $MOBILE_NO_TWILIO;
        $success = 0;
        $client = new Services_Twilio($MOBILE_VERIFY_SID_TWILIO, $MOBILE_VERIFY_TOKEN_TWILIO);
        if (strpos($mobileNo, '+') === false) {
            $toMobileNum = "+" . $mobileNo;
        } else {
            $toMobileNum = $mobileNo;
        }
        try {
            $sms = $client->account->messages->sendMessage($twilioMobileNum, $toMobileNum, $message);
            $success = 1;
        } catch (Services_Twilio_RestException $e) {
 		  //echo"<prE>";print_r($e);die;
            $success = 0;
        }
        if ($success == 0) {
            if (!empty($phonecode)) {
                $mobileNo = preg_replace("/[^0-9]/", "", $mobileNo);
                $phonecode = preg_replace("/[^0-9]/", "", $phonecode);
                $toMobileNum = "+" . $phonecode . $mobileNo;
                try {
                    $sms = $client->account->messages->sendMessage($twilioMobileNum, $toMobileNum, $message);
                    $success = 1;
                } catch (Services_Twilio_RestException $e) {
                    $success = 0;
                }
            }
        }
        if ($success == 0) {
            $mobileNo = preg_replace("/[^0-9]/", "", $mobileNo);
            $toMobileNum = "+" . $SITE_ISD_CODE . $mobileNo;
            try {
                $sms = $client->account->messages->sendMessage($twilioMobileNum, $toMobileNum, $message);
                $success = 1;
            } catch (Services_Twilio_RestException $e) {
                $success = 0;
            }
        }


        return $success;
    }

    public function SendMemberSMS($mobileNo, $code, $fpass, $pass = '')
    {
        global $CONFIG_OBJ;
        require_once TPATH_CLASS . 'twilio/Services/Twilio.php';
        $account_sid = $CONFIG_OBJ->getConfigurations("configurations", "MOBILE_VERIFY_SID_TWILIO");
        $auth_token = $CONFIG_OBJ->getConfigurations("configurations", "MOBILE_VERIFY_TOKEN_TWILIO");
        $twilioMobileNum = $CONFIG_OBJ->getConfigurations("configurations", "MOBILE_NO_TWILIO");
        $client = new Services_Twilio($account_sid, $auth_token);
        $toMobileNum = "+" . $code . $mobileNo;
        try {
            $sms = $client->account->messages->sendMessage($twilioMobileNum, $toMobileNum, $fpass);
            $success = "1";
        } catch (Services_Twilio_RestException $e) {
            $success = "0";
        }
        return $success;
    }

    public function orderemaildataDelivered($iOrderId, $sendTo)
    {
        global $tconfig, $obj, $COPYRIGHT_TEXT, $COMPANY_NAME, $EMAIL_FROM_NAME, $NOREPLY_EMAIL, $LANG_OBJ;
        ob_start();
        require_once $tconfig["tpanel_path"] . "orderdetails_mail_format.php";
        $mail_contentdeliverd = ob_get_clean();
        $maildatadeliverd['vEmail'] = $returnArrData['vEmail'];
        $maildatadeliverd['UserName'] = $returnArrData['UserName'];
        $maildatadeliverd['details'] = $mail_contentdeliverd;
        if ($returnArrData['iStatusCode'] == '6') {
            if ($returnArrData['eTakeaway'] == 'Yes') {
                $mailResponse = $this->SendMailToMember("USER_ORDER_DELIVER_INVOICE_TAKEAWAY", $maildatadeliverd);
            } else {
                $mailResponse = $this->SendMailToMember("USER_ORDER_DELIVER_INVOICE", $maildatadeliverd);
            }
        }
        return $mailResponse;
    }

    /**
     * @access    public
     * @Print Element input type
     */
    public function SendMailToMember($type, $db_rec = '', $newsid = '')
    {
        global $MAIL_FOOTER, $EMAIL_FROM_NAME, $SITE_NAME, $obj, $tconfig, $SUPPORT_MAIL, $SEND_EMAIL, $NOREPLY_EMAIL, $ADMIN_EMAIL, $MAILGUN_ENABLE, $LANG_OBJ;

        $replyto = "";
        $str = "select * from email_templates where vEmail_Code='" . $type . "'";
        $res = $obj->MySQLSelect($str);
        $to_email = $orgMailSub = "";
        if (isset($db_rec['iOrganizationId']) && $db_rec['iOrganizationId'] > 0 && $type == "RIDER_INVOICE") {
            if ($MAILGUN_ENABLE == "Yes") {
                $orgMailSub = "[Organization]";
            } else {
                $orgMailSub = "[Personal]";
            }
        }
        switch ($type) {
            case "NEWSLETTER_SUBSCRIBER":
                $to_email = $db_rec['vEmail'];
                $key_arr = array("#MailFooter#");
                $val_arr = array($MAIL_FOOTER);
                break;
            case "CONTACTUS":
                $to_email = $ADMIN_EMAIL;
                $replyto = $db_rec['vEmail'];
                $key_arr = array("#Contact_Name#", "#Contact_Phone#", "#Contact_Email#", "#Contact_Subject#", "#Contact_Message#", "#MailFooter#");
                $val_arr = array($db_rec['vFirstName'] . " " . $db_rec['vLastName'], $db_rec['cellno'], $db_rec['vEmail'], $db_rec['eSubject'], $db_rec['tSubject'], $MAIL_FOOTER);
                break;
            case "CONTACTUSWITHOUTLOGIN":
                $to_email = $ADMIN_EMAIL;
                $replyto = $db_rec['vEmail'];
                $key_arr = array("#Contact_Name#", "#Contact_Subject#", "#Contact_Message#", "#MailFooter#");
                $val_arr = array($db_rec['vFirstName'] . " " . $db_rec['vLastName'], $db_rec['eSubject'], $db_rec['tSubject'], $MAIL_FOOTER);
                break;
            case "WITHDRAWAL_MONEY_REQUEST_ADMIN":
                $to_email = $ADMIN_EMAIL;
                $replyto = $db_rec['User_Email'];
                $key_arr = array("#Member_Name#", "#Member_Phone#", "#Member_Email#", "#Account_Name#", "#Bank_Name#", "#Account_Number#", "#BIC/SWIFT_Code#", "#Bank_Branch#", "#Withdrawal_amount#", "#MailFooter#");
                $val_arr = array($db_rec['User_Name'], $db_rec['User_Phone'], $db_rec['User_Email'], $db_rec['Account_Name'], $db_rec['Bank_Name'], $db_rec['Account_Number'], $db_rec['BIC/SWIFT_Code'], $db_rec['Bank_Branch'], $db_rec['Withdrawal_amount'], $MAIL_FOOTER);
                break;
            case "WITHDRAWAL_MONEY_REQUEST_USER":
                $to_email = $db_rec['User_Email'];
                $key_arr = array("#User_Name#", "#Withdrawal_amount#", "#MailFooter#");
                $val_arr = array($db_rec['User_Name'], $db_rec['Withdrawal_amount'], $MAIL_FOOTER);
                break;
            case "CUSTOMER_FORGETPASSWORD":
                $to_email = $db_rec[0]['vEmail'];
                $key_arr = array("#Name#", "#Email#", "#Password#", "#MailFooter#", "#SITE_NAME#");
                if ($db_rec[0]['vName'] != "" && $db_rec[0]['vLastName'] != "") {
                    $User_Name = $db_rec[0]['vName'] . " " . $db_rec[0]['vLastName'];
                } else {
                    $User_Name = $db_rec[0]['vCompany'];
                }
                $val_arr = array($User_Name, $db_rec[0]['vEmail'], $this->decrypt($db_rec[0]['vPassword']), $MAIL_FOOTER, $SITE_NAME);
                break;
            case "EMAIL_VERIFICATION_USER":
                $to_email = $db_rec['vEmail'];
                $key_arr = array("#Name#", "#activate_account#", "#MailFooter#");
                $val_arr = array($db_rec['vName'], $db_rec['act_link'], $MAIL_FOOTER);
                break;
            case "MEMBER_RECEIVE_RATING":
                $to_email = $db_rec['ToEmail'];
                $key_arr = array("#ToName#", "#FromName#", "#Feedback#", "#Rating#", "#MailFooter#");
                $val_arr = array($db_rec['ToName'], $db_rec['FromName'], $db_rec['Feedback'], $db_rec['iRate'], $MAIL_FOOTER);
                break;
            case "MEMBER_GIVE_RATING":
                $to_email = $db_rec['FromEmail'];
                $key_arr = array("#ToName#", "#FromName#", "#Feedback#", "#Rating#", "#MailFooter#");
                $val_arr = array($db_rec['ToName'], $db_rec['FromName'], $db_rec['Feedback'], $db_rec['iRate'], $MAIL_FOOTER);
                break;
            case "MEMBER_RECEIVE_MESSAGE":
                $to_email = $db_rec['ToEmail'];
                $key_arr = array("#ToName#", "#FromName#", "#Message#", "#MailFooter#");
                $val_arr = array($db_rec['ToName'], $db_rec['FromName'], $db_rec['tMessage'], $MAIL_FOOTER);
                break;
            case "MEMBER_PUBLISH_STORY":
                $to_email = $ADMIN_EMAIL;
                $replyto = $db_rec['FromEmail'];
                $key_arr = array("#FromName#", "#FromEmail#", "#Title#", "#Description#", "#MailFooter#");
                $val_arr = array($db_rec['FromName'], $db_rec['FromEmail'], $db_rec['Title'], $db_rec['tDescription'], $MAIL_FOOTER);
                break;
            case "DELETE_ACCOUNT":
                $to_email = $db_rec['EMAIL'];
                $key_arr = array("#Name#", "#Email#", "#SITE_FOOTER#");
                $val_arr = array($db_rec['NAME'] . " " . $db_rec['LAST_NAME'], $db_rec['EMAIL'], $MAIL_FOOTER);
                break;
            case "NEWRIDEOFFER_MEMBER":
                $to_email = $db_rec['vEmail'];
                $key_arr = array("#details#", "#MailFooter#");
                $val_arr = array($db_rec['details'], $MAIL_FOOTER);
                break;
            case "RIDER_INVOICE":
                $to_email = $db_rec['email'];
                $key_arr = array("#details#", "#MailFooter#");
                $val_arr = array($db_rec['details'], $MAIL_FOOTER);
                break;
            case "NEWRIDEOFFER_ADMIN":
                $to_email = $ADMIN_EMAIL;
                $key_arr = array("#details#", "#MailFooter#");
                $val_arr = array($db_rec['details'], $MAIL_FOOTER);
                break;
            case "BOOKING_PASSENGER":
                $to_email = $db_rec['vBookerEmail'];
                $key_arr = array("#details#", "#MailFooter#");
                $val_arr = array($db_rec['details'], $MAIL_FOOTER);
                break;
            case "BOOKING_DRIVER":
                $to_email = $db_rec['vDriverEmail'];
                $key_arr = array("#details#", "#MailFooter#");
                $val_arr = array($db_rec['details'], $MAIL_FOOTER);
                break;
            case "BOOKING_ADMIN":
                $to_email = $ADMIN_EMAIL;
                $key_arr = array("#Detail#", "#MailFooter#");
                $val_arr = array($db_rec['details'], $MAIL_FOOTER);
                break;
            case "TRIP_COMPLETION_MESSAGE":
                $to_email = $db_rec['vBookerEmail'];
                $key_arr = array("#details#", "#MailFooter#");
                $val_arr = array($db_rec['details'], $MAIL_FOOTER);
                break;
            case "RIDE_COMPLETION_CONFIRMATION_ADMIN":
                $to_email = $ADMIN_EMAIL;
                $key_arr = array("#details#", "#MailFooter#");
                $val_arr = array($db_rec['details'], $MAIL_FOOTER);
                break;
            case "RIDE_COMPLETION_CONFIRMATION_DRIVER":
                $to_email = $db_rec['vDriverEmail'];
                $key_arr = array("#details#", "#MailFooter#");
                $val_arr = array($db_rec['details'], $MAIL_FOOTER);
                break;
            case "MEMBER_REGISTRATION_USER":
                $to_email = $db_rec['EMAIL'];
                $key_arr = array("#NAME#", "#EMAIL#", "#PASSWORD#", "#SOCIALNOTES#", "#MAILFOOTER#");
                $val_arr = array($db_rec['NAME'], $db_rec['EMAIL'], $db_rec['PASSWORD'], $db_rec['SOCIALNOTES'], $MAIL_FOOTER);
                break;
            case "DRIVER_REGISTRATION_USER":
                $to_email = $db_rec['EMAIL'];
                $key_arr = array("#NAME#", "#EMAIL#", "#PASSWORD#", "#SOCIALNOTES#", "#MAILFOOTER#");
                $val_arr = array($db_rec['NAME'], $db_rec['EMAIL'], $db_rec['PASSWORD'], $db_rec['SOCIALNOTES'], $MAIL_FOOTER);
                break;
            case "COMPANY_REGISTRATION_USER":
                $to_email = $db_rec['EMAIL'];
                $key_arr = array("#NAME#", "#EMAIL#", "#PASSWORD#", "#MAILFOOTER#");
                $val_arr = array($db_rec['NAME'], $db_rec['EMAIL'], $db_rec['PASSWORD'], $MAIL_FOOTER);
                break;
            case "STORE_REGISTRATION_USER":
                $to_email = $db_rec['EMAIL'];
                $key_arr = array("#NAME#", "#EMAIL#", "#PASSWORD#", "#MAILFOOTER#");
                $val_arr = array($db_rec['NAME'], $db_rec['EMAIL'], $db_rec['PASSWORD'], $MAIL_FOOTER);
                break;
            case "DRIVER_REGISTRATION_ADMIN":
                $to_email = $ADMIN_EMAIL;
                $replyto = $db_rec['EMAIL'];
                $key_arr = array("#NAME#", "#EMAIL#", "#MAILFOOTER#");
                $val_arr = array($db_rec['NAME'], $db_rec['EMAIL'], $MAIL_FOOTER);
                break;
            case "COMPANY_REGISTRATION_ADMIN":
                $to_email = $ADMIN_EMAIL;
                $replyto = $db_rec['EMAIL'];
                $key_arr = array("#NAME#", "#EMAIL#", "#MAILFOOTER#");
                $val_arr = array($db_rec['NAME'], $db_rec['EMAIL'], $MAIL_FOOTER);
                break;
            case "STORE_REGISTRATION_ADMIN":
                $to_email = $ADMIN_EMAIL;
                $replyto = $db_rec['EMAIL'];
                $key_arr = array("#NAME#", "#EMAIL#", "#MAILFOOTER#");
                $val_arr = array($db_rec['NAME'], $db_rec['EMAIL'], $MAIL_FOOTER);
                break;
            case "RIDE_ALERT_EMAIL":
                $to_email = $db_rec['Email'];
                $key_arr = array("#details#", "#MailFooter#");
                $val_arr = array($db_rec['details'], $MAIL_FOOTER);
                break;
            case "RIDE_PAYMENT_EMAIL_DRIVER":
                $to_email = $db_rec['Email'];
                $key_arr = array("#details#", "#MailFooter#");
                $val_arr = array($db_rec['details'], $MAIL_FOOTER);
                break;
            case "DRIVER_CANCEL_BOOKING_TO_PASSENGER":
                $to_email = $db_rec['vBookerEmail'];
                $key_arr = array("#vBookerName#", "#vBookingNo#", "#vFromPlace#", "#vToPlace#", "#dBookingDate#", "#tCancelreason#", "#vDriverName#", "#MailFooter#");
                $val_arr = array($db_rec['vBookerName'], $db_rec['vBookingNo'], $db_rec['vFromPlace'], $db_rec['vToPlace'], $db_rec['dBookingDate'], $db_rec['tCancelreason'], $db_rec['vDriverName'], $MAIL_FOOTER);
                break;
            case "PASSENGER_CANCEL_BOOKING_TO_DRIVER":
                $to_email = $db_rec['vDriverEmail'];
                $key_arr = array("#vBookerName#", "#vBookingNo#", "#vFromPlace#", "#vToPlace#", "#dBookingDate#", "#tCancelreason#", "#vDriverName#", "#MailFooter#");
                $val_arr = array($db_rec['vBookerName'], $db_rec['vBookingNo'], $db_rec['vFromPlace'], $db_rec['vToPlace'], $db_rec['dBookingDate'], $db_rec['tCancelreason'], $db_rec['vDriverName'], $MAIL_FOOTER);
                break;
            case "CANCELLATION_USER":
                $to_email = $db_rec['email'];
                $key_arr = array("#details#", "#MailFooter#");
                $val_arr = array($db_rec['details'], $MAIL_FOOTER);
                break;
            case "CANCELLATION_ADMIN":
                $to_email = $ADMIN_EMAIL;
                $key_arr = array("#details#", "#MailFooter#");
                $val_arr = array($db_rec['details'], $MAIL_FOOTER);
                break;
            case "REFUND_USER":
                $to_email = $db_rec['email'];
                $key_arr = array("#details#", "#MailFooter#");
                $val_arr = array($db_rec['details'], $MAIL_FOOTER);
                break;
            case "FORGOT_PASSWORD":
                $to_email = $db_rec['email'];
                $key_arr = array("#details#", "#MailFooter#");
                $val_arr = array($db_rec['details'], $MAIL_FOOTER);
                break;
            case "PAYMENT_VERIFICATION":
                $to_email = $db_rec['email'];
                $key_arr = array("#details#", "#MailFooter#");
                $val_arr = array($db_rec['details'], $MAIL_FOOTER);
                break;
            case "ACCOUNT_STATUS":
                $to_email = $db_rec['EMAIL'];
                $key_arr = array("#NAME#", "#EMAIL#", "#details#", "#MAILFOOTER#");
                $val_arr = array($db_rec['NAME'], $db_rec['EMAIL'], $db_rec['DETAIL'], $MAIL_FOOTER);
                break;
            case "VEHICLE_BOOKING":
                $to_email = $db_rec['EMAIL'];
                $key_arr = array("#NAME#", "#EMAIL#", "#details#", "#MAILFOOTER#");
                $val_arr = array($db_rec['NAME'], $db_rec['EMAIL'], $db_rec['DETAIL'], $MAIL_FOOTER);
                break;
            case "VEHICLE_BOOKING_ADMIN":
                $to_email = $ADMIN_EMAIL;
                $key_arr = array("#NAME#", "#EMAIL#", "#MAKE#", "#MODEL#", "#details#", "#MAILFOOTER#");
                $val_arr = array($db_rec['NAME'], $db_rec['EMAIL'], $db_rec['MAKE'], $db_rec['MODEL'], $db_rec['DETAIL'], $MAIL_FOOTER);
                break;
            case "DOCCUMENT_UPLOAD":
                $to_email = $ADMIN_EMAIL;
                $replyto = $db_rec['EMAIL'];
                $key_arr = array("#Company#", "#Name#", "#Email#", "#SITE_FOOTER#");
                $val_arr = array($db_rec['COMPANY'], $db_rec['NAME'], $db_rec['EMAIL'], $SITE_FOOTER);
                break;
            case "PROFILE_UPLOAD":
                $to_email = $ADMIN_EMAIL;
                $replyto = $db_rec['EMAIL'];
                $key_arr = array("#USER#", "#Name#", "#Email#", "#SITE_FOOTER#");
                $val_arr = array($db_rec['USER'], $db_rec['NAME'], $db_rec['EMAIL'], $SITE_FOOTER);
                break;
            case "MANUAL_TAXI_DISPATCH_DRIVER":
                $to_email = $db_rec['vDriverMail'];
                $key_arr = array("#Driver#", "#Rider#", "#BookingNo#", "#SourceAddress#", "#DestinationAddress#", "#Ddate#", "#MailFooter#");
                $val_arr = array($db_rec['vDriver'], $db_rec['vRider'], $db_rec['vBookingNo'], $db_rec['vSourceAddresss'], $db_rec['tDestAddress'], $db_rec['dBookingdate'], $MAIL_FOOTER);
                break;
            case "MANUAL_TAXI_DISPATCH_RIDER":
                $to_email = $db_rec['vRiderMail'];
                $key_arr = array("#Driver#", "#Rider#", "#BookingNo#", "#SourceAddress#", "#DestinationAddress#", "#Ddate#", "#MailFooter#");
                $val_arr = array($db_rec['vDriver'], $db_rec['vRider'], $db_rec['vBookingNo'], $db_rec['vSourceAddresss'], $db_rec['tDestAddress'], $db_rec['dBookingdate'], $MAIL_FOOTER);
                break;
            case "MANUAL_TAXI_DISPATCH_RIDER_SP":
                $to_email = $db_rec['vRiderMail'];
                $key_arr = array("#Driver#", "#Rider#", "#BookingNo#", "#SourceAddress#", "#DestinationAddress#", "#Ddate#", "#MailFooter#");
                $val_arr = array($db_rec['vDriver'], $db_rec['vRider'], $db_rec['vBookingNo'], $db_rec['vSourceAddresss'], $db_rec['tDestAddress'], $db_rec['dBookingdate'], $MAIL_FOOTER);
                break;
            case "MANUAL_TAXI_DISPATCH_RIDER_AUTOASSIGN":
                $to_email = $db_rec['vRiderMail'];
                $key_arr = array("#Rider#", "#BookingNo#", "#SourceAddress#", "#DestinationAddress#", "#Ddate#", "#MailFooter#");
                $val_arr = array($db_rec['vRider'], $db_rec['vBookingNo'], $db_rec['vSourceAddresss'], $db_rec['tDestAddress'], $db_rec['dBookingdate'], $MAIL_FOOTER);
                break;
            case "MANUAL_TAXI_DISPATCH_RIDER_AUTOASSIGN_SP":
                $to_email = $db_rec['vRiderMail'];
                $key_arr = array("#Rider#", "#BookingNo#", "#SourceAddress#", "#DestinationAddress#", "#Ddate#", "#MailFooter#");
                $val_arr = array($db_rec['vRider'], $db_rec['vBookingNo'], $db_rec['vSourceAddresss'], $db_rec['tDestAddress'], $db_rec['dBookingdate'], $MAIL_FOOTER);
                break;
            case "APP_EMAIL_VERIFICATION_USER":
                $to_email = $db_rec['vEmail'];
                $key_arr = array("#NAME#", "#CODE#", "#MAIL_FOOTER#");
                $val_arr = array($db_rec['vName'], $db_rec['CODE'], $MAIL_FOOTER);
                break;
            case "CRON_BOOKING_EMAIL":
                $to_email = $ADMIN_EMAIL;
                $key_arr = array("#details#", "#MailFooter#");
                $val_arr = array($db_rec['details'], $MAIL_FOOTER);
                break;
            case "CUSTOMER_RESET_PASSWORD":
                $to_email = $db_rec['EMAIL'];
                $key_arr = array("#NAME#", "#EMAIL#", "#LINK#", "#MailFooter#");
                $val_arr = array($db_rec['NAME'], $db_rec['EMAIL'], $db_rec['LINK'], $MAIL_FOOTER);
                break;
            case "PAYMENT_REQUEST_ADMIN":
                $to_email = $ADMIN_EMAIL;
                $key_arr = array("#driver#", "#Name#", "#Email#", "#Trips#", "#Amount#", "#Account_Name#", "#Bank_Name#", "#Account_Number#", "#BIC/SWIFT_Code#", "#Bank_Branch#", "#MailFooter#");
                $val_arr = array($db_rec['Name'], $db_rec['Name'], $db_rec['vEmail'], $db_rec['TripIds'], $db_rec['Total_Amount'], $db_rec['Account_Name'], $db_rec['Bank_Name'], $db_rec['Account_Number'], $db_rec['BIC/SWIFT_Code'], $db_rec['Bank_Branch'], $MAIL_FOOTER);
                break;
                case "PAYMENT_REQUEST_FOR_RIDE_SHARE_ADMIN":
                $to_email = $ADMIN_EMAIL;
                $key_arr = array("#driver#", "#Name#", "#Email#", "#vBookingNo#", "#Amount#", "#Account_Name#", "#Bank_Name#", "#Account_Number#", "#BIC/SWIFT_Code#", "#Bank_Branch#", "#MailFooter#");
                $val_arr = array($db_rec['Name'], $db_rec['Name'], $db_rec['vEmail'], $db_rec['vBookingNo'], $db_rec['Total_Amount'], $db_rec['Account_Name'], $db_rec['Bank_Name'], $db_rec['Account_Number'], $db_rec['BIC/SWIFT_Code'], $db_rec['Bank_Branch'], $MAIL_FOOTER);
                break;

            case "MANUAL_TAXI_DISPATCH_DRIVER_APP":
                $to_email = $db_rec['vDriverMail'];
                $key_arr = array("#Driver#", "#Rider#", "#BookingNo#", "#SourceAddress#", "#Ddate#", "#MailFooter#");
                $val_arr = array($db_rec['vDriver'], $db_rec['vRider'], $db_rec['vBookingNo'], $db_rec['vSourceAddresss'], $db_rec['dBookingdate'], $MAIL_FOOTER);
                break;
            case "MANUAL_TAXI_DISPATCH_DRIVER_APP_SP":
                $to_email = $db_rec['vDriverMail'];
                $key_arr = array("#Driver#", "#Rider#", "#Booking_Number#", "#SourceAddress#", "#Ddate#", "#MailFooter#");
                $val_arr = array($db_rec['vDriver'], $db_rec['vRider'], $db_rec['vBookingNo'], $db_rec['vSourceAddresss'], $db_rec['dBookingdate'], $MAIL_FOOTER);
                break;
            case "MANUAL_TAXI_DISPATCH_RIDER_APP":
                $to_email = $db_rec['vRiderMail'];
                $key_arr = array("#Driver#", "#Rider#", "#BookingNo#", "#SourceAddress#", "#Ddate#", "#MailFooter#");
                $val_arr = array($db_rec['vDriver'], $db_rec['vRider'], $db_rec['vBookingNo'], $db_rec['vSourceAddresss'], $db_rec['dBookingdate'], $MAIL_FOOTER);
                break;
            case "MANUAL_CANCEL_TRIP_ADMIN":
                $to_email = $ADMIN_EMAIL;
                $key_arr = array("#BookingNo#", "#Driver#", "#Rider#", "#SourceAddress#", "#DestinationAddress#", "#Ddate#", "#Reason#", "#MailFooter#");
                $val_arr = array($db_rec['vBookingNo'], $db_rec['DriverName'], $db_rec['RiderName'], $db_rec['vSourceAddresss'], $db_rec['tDestAddress'], $db_rec['dBooking_date'], $db_rec['vCancelReason'], $MAIL_FOOTER);
                break;
            case "MANUAL_CANCEL_TRIP_DRIVER":
                $to_email = $db_rec['vDriverMail'];
                $key_arr = array("#BookingNo#", "#Driver#", "#Rider#", "#SourceAddress#", "#DestinationAddress#", "#Ddate#", "#Reason#", "#MailFooter#");
                $val_arr = array($db_rec['vBookingNo'], $db_rec['DriverName'], $db_rec['RiderName'], $db_rec['vSourceAddresss'], $db_rec['tDestAddress'], $db_rec['dBooking_date'], $db_rec['vCancelReason'], $MAIL_FOOTER);
                break;
            case "MANUAL_CANCEL_TRIP_ADMIN_TO_DRIVER":
                $to_email = $db_rec['vDriverMail'];
                $key_arr = array("#Driver#", "#Rider#", "#BookingNo#", "#SourceAddress#", "#Ddate#", "#MailFooter#");
                $val_arr = array($db_rec['vDriver'], $db_rec['vRider'], $db_rec['vBookingNo'], $db_rec['vSourceAddresss'], $db_rec['dBookingdate'], $MAIL_FOOTER);
                break;
            case "MANUAL_CANCEL_TRIP_ADMIN_TO_RIDER":
                $to_email = $db_rec['vRiderMail'];
                $key_arr = array("#Driver#", "#Rider#", "#BookingNo#", "#SourceAddress#", "#Ddate#", "#MailFooter#");
                $val_arr = array($db_rec['vDriver'], $db_rec['vRider'], $db_rec['vBookingNo'], $db_rec['vSourceAddresss'], $db_rec['dBookingdate'], $MAIL_FOOTER);
                break;
            case "MANUAL_CANCEL_TRIP_ADMIN_TO_COMPANY":
                $to_email = $db_rec['vCompanyMail'];
                $key_arr = array("#Driver#", "#Rider#", "#BookingNo#", "#SourceAddress#", "#Ddate#", "#MailFooter#");
                $val_arr = array($db_rec['vDriver'], $db_rec['vRider'], $db_rec['vBookingNo'], $db_rec['vSourceAddresss'], $db_rec['dBookingdate'], $MAIL_FOOTER);
                break;
            case "MANUAL_BOOKING_ACCEPT_BYDRIVER_SP":
                $to_email = $db_rec['vRiderMail'];
                $key_arr = array("#PROVIDER_NAME#", "#Rider#", "#BOOKING_NUMBER#", "#Ddate#", "#MailFooter#");
                $val_arr = array($db_rec['vDriver'], $db_rec['vRider'], $db_rec['vBookingNo'], $db_rec['dBookingdate'], $MAIL_FOOTER);
                break;
            case "MANUAL_BOOKING_DECLINED_BYDRIVER_SP":
                $to_email = $db_rec['vRiderMail'];
                $key_arr = array("#PROVIDER_NAME#", "#Rider#", "#BOOKING_NUMBER#", "#Ddate#", "#MailFooter#");
                $val_arr = array($db_rec['vDriver'], $db_rec['vRider'], $db_rec['vBookingNo'], $db_rec['dBookingdate'], $MAIL_FOOTER);
                break;
            case "MANUAL_BOOKING_CANCEL_BYDRIVER_SP":
                $to_email = $db_rec['vRiderMail'];
                $key_arr = array("#Rider#", "#PROVIDERNAME#", "#BOOKING_NUMBER#", "#Ddate#", "#MailFooter#");
                $val_arr = array($db_rec['RiderName'], $db_rec['DriverName'], $db_rec['vBookingNo'], $db_rec['dBookingdate'], $MAIL_FOOTER);
                break;
            case "MANUAL_BOOKING_CANCEL_BYRIDER_SP":
                $to_email = $db_rec['vDriverMail'];
                $key_arr = array("#Rider#", "#PROVIDERNAME#", "#BOOKING_NUMBER#", "#Ddate#", "#MailFooter#");
                $val_arr = array($db_rec['RiderName'], $db_rec['DriverName'], $db_rec['vBookingNo'], $db_rec['dBookingdate'], $MAIL_FOOTER);
                break;
            case "MEMBER_REGISTRATION_USER_FOR_MANUAL_BOOKING":
                $to_email = $db_rec['EMAIL'];
                $key_arr = array("#NAME#", "#EMAIL#", "#PASSWORD#", "#MAILFOOTER#");
                $val_arr = array($db_rec['NAME'], $db_rec['EMAIL'], $db_rec['PASSWORD'], $MAIL_FOOTER);
                break;
            case "BANK_DETAIL_NOTIFY_ADMIN":
                $to_email = $ADMIN_EMAIL;
                $replyto = $db_rec['EMAIL'];
                $key_arr = array("#NAME#", "#EMAIL#", "#PHONE#", "#MAILFOOTER#");
                $val_arr = array($db_rec['NAME'], $db_rec['EMAIL'], $db_rec['PHONE'], $MAIL_FOOTER);
                break;
            case "CRON_EXPIRY_DOCUMENT_EMAIL":
                $to_email = $ADMIN_EMAIL;
                $key_arr = array("#details#", "#MailFooter#");
                $val_arr = array($db_rec['details'], $MAIL_FOOTER);
                break;
            case "CRON_EXPIRY_DOCUMENT_EMAIL_SEVEN_DAY_AGO":
                $to_email = $ADMIN_EMAIL;
                $key_arr = array("#details#", "#MailFooter#");
                $val_arr = array($db_rec['details'], $MAIL_FOOTER);
                break;
            case "MANUAL_TAXI_DISPATCH_RIDER_RESCEDULE_APP":
                $to_email = $db_rec['vRiderMail'];
                $key_arr = array("#Driver#", "#Rider#", "#BookingNo#", "#SourceAddress#", "#Ddate#", "#MailFooter#");
                $val_arr = array($db_rec['vDriver'], $db_rec['vRider'], $db_rec['vBookingNo'], $db_rec['vSourceAddresss'], $db_rec['dBookingdate'], $MAIL_FOOTER);
                break;
            case "MANUAL_TAXI_DISPATCH_RIDER_RESCEDULE_ADMIN_APP":
                $to_email = $ADMIN_EMAIL;
                $key_arr = array("#Driver#", "#Rider#", "#BookingNo#", "#SourceAddress#", "#Ddate#", "#MailFooter#");
                $val_arr = array($db_rec['vDriver'], $db_rec['vRider'], $db_rec['vBookingNo'], $db_rec['vSourceAddresss'], $db_rec['dBookingdate'], $MAIL_FOOTER);
                break;
            case "DOCCUMENT_UPLOAD_WEB":
                $to_email = $ADMIN_EMAIL;
                $key_arr = array("#NAME#", "#EMAIL#", "#DOC_TYPE#", "#DOC_FOR#", "#MailFooter#");
                $val_arr = array($db_rec['NAME'], $db_rec['EMAIL'], $db_rec['DOCUMENTTYPE'], $db_rec['DOCUMENTFOR'], $MAIL_FOOTER);
                break;
            case "DOCCUMENT_UPLOAD_WEB_COMPANY":
                $to_email = $db_rec['COMPANYEMAIL'];
                $key_arr = array("#COMPANY#", "#NAME#", "#EMAIL#", "#DOC_TYPE#", "#DOC_FOR#", "#MailFooter#");
                $val_arr = array($db_rec['COMPANYNAME'], $db_rec['NAME'], $db_rec['EMAIL'], $db_rec['DOCUMENTTYPE'], $db_rec['DOCUMENTFOR'], $MAIL_FOOTER);
                break;
            case "RIDER_TRIP_HELP_DETAIL":
                $to_email = $ADMIN_EMAIL;
                $key_arr = array("#NAME#", "#iTripId#", "#vComment#", "#Ddate#", "#MailFooter#");
                $val_arr = array($db_rec['NAME'], $db_rec['iTripId'], $db_rec['vComment'], $db_rec['Ddate'], $MAIL_FOOTER);
                break;
            case "USER_ORDER_HELP_DETAIL":
                $to_email = $ADMIN_EMAIL;
                $key_arr = array("#NAME#", "#iTripId#", "#vComment#", "#Ddate#", "#MailFooter#");
                $val_arr = array($db_rec['NAME'], $db_rec['iTripId'], $db_rec['vComment'], $db_rec['Ddate'], $MAIL_FOOTER);
                break;
            case "USER_ORDER_CONFIRM_INVOICE":
                $to_email = $db_rec['vEmail'];
                $key_arr = array("#Rider#", "#details#", "#MailFooter#");
                $val_arr = array($db_rec['UserName'], $db_rec['details'], $MAIL_FOOTER);
                break;
            case "USER_ORDER_INVOICE_RECEIPT":
                $to_email = $db_rec['vEmail'];
                $key_arr = array("#Rider#", "#details#", "#MailFooter#");
                $val_arr = array($db_rec['UserName'], $db_rec['details'], $MAIL_FOOTER);
                break;
            case "USER_ORDER_CONFIRM_INVOICE_ADMIN":
                $to_email = $ADMIN_EMAIL;
                $key_arr = array("#details#", "#MailFooter#");
                $val_arr = array($db_rec['details'], $MAIL_FOOTER);
                break;
            case "USER_ORDER_DELIVER_INVOICE":
                $to_email = $db_rec['vEmail'];
                $key_arr = array("#Rider#", "#RESTAURANTNAME#", "#details#", "#MailFooter#");
                $val_arr = array($db_rec['UserName'], $db_rec['CompanyName'], $db_rec['details'], $MAIL_FOOTER);
                break;
            case "USER_ORDER_CONFIRM_INVOICE_TAKEAWAY":
                $to_email = $db_rec['vEmail'];
                $key_arr = array("#Rider#", "#RESTAURANTNAME#", "#details#", "#MailFooter#");
                $val_arr = array($db_rec['UserName'], $db_rec['CompanyName'], $db_rec['details'], $MAIL_FOOTER);
                break;
            case "USER_ORDER_DELIVER_INVOICE_TAKEAWAY":
                $to_email = $db_rec['vEmail'];
                $key_arr = array("#Rider#", "#RESTAURANTNAME#", "#details#", "#MailFooter#");
                $val_arr = array($db_rec['UserName'], $db_rec['CompanyName'], $db_rec['details'], $MAIL_FOOTER);
                break;
            case "COMPANY_DECLINE_ORDER_TO_USER":
                $to_email = $db_rec['vEmail'];
                $key_arr = array("#USERNAME#", "#RESTAURANTNAME#", "#ORDERNO#", "#MSG#", "#MailFooter#");
                $val_arr = array($db_rec['UserName'], $db_rec['CompanyName'], $db_rec['vOrderNo'], $db_rec['MSG'], $MAIL_FOOTER);
                break;
            case "COMPANY_DECLINE_ORDER":
                $to_email = $ADMIN_EMAIL;
                $key_arr = array("#USERNAME#", "#RESTAURANTNAME#", "#ORDERNO#", "#MSG#", "#MailFooter#");
                $val_arr = array($db_rec['UserName'], $db_rec['CompanyName'], $db_rec['vOrderNo'], $db_rec['MSG'], $MAIL_FOOTER);
                break;
            case "MANUAL_CANCEL_ORDER_ADMIN_TO_DRIVER_COMPANY":
                $to_email = $db_rec['vEmail'];
                $key_arr = array("#USERNAME#", "#projectname#", "#ORDERNO#", "#MSG#", "#MailFooter#");
                $val_arr = array($db_rec['UserName'], $db_rec['ProjectName'], $db_rec['vOrderNo'], $db_rec['MSG'], $MAIL_FOOTER);
                break;
            case "MANUAL_CANCEL_ORDER_ADMIN_TO_RIDER":
                $to_email = $db_rec['vEmail'];
                $key_arr = array("#USERNAME#", "#projectname#", "#ORDERNO#", "#MSG#", "#Currency-charge#", "#MailFooter#");
                $val_arr = array($db_rec['UserName'], $db_rec['ProjectName'], $db_rec['vOrderNo'], $db_rec['MSG'], $db_rec['Charge'], $MAIL_FOOTER);
                break;
            case "COUPON_LIMIT_COMPLETED_TO_ADMIN":
                $to_email = $ADMIN_EMAIL;
                $key_arr = array("#CODE#", "#TOTAL_USAGE#", "#COMPANY_NAME#", "#MailFooter#");
                $val_arr = array($db_rec['vCouponCode'], $db_rec['iUsageLimit'], $db_rec['COMPANY_NAME'], $MAIL_FOOTER);
                break;
            case "USER_REGISTRATION_ORGANIZATION":
                $to_email = $db_rec['Company_Email'];
                $key_arr = array("#ORGANIZATION_NAME#", "#USER_NAME#", "#USER_EMAIL#", "#USER_PHONE#", "#MailFooter#");
                $val_arr = array($db_rec['vCompany'], $db_rec['User_Name'], $db_rec['User_Email'], $db_rec['User_Phone'], $MAIL_FOOTER);
                break;
            case "ORGANIZATION_UPDATE_USERPROFILESTATUS_TO_USER":
                $to_email = $db_rec['User_Profile_Email'];
                $key_arr = array("#USER_NAME#", "#ORGANIZATION_NAME#", "#PROFILE_STATUS#", "#COMPANY_NAME#", "#MailFooter#");
                $val_arr = array($db_rec['User_Name'], $db_rec['Organization_Name'], $db_rec['Profile_Status'], $db_rec['Company_Name'], $MAIL_FOOTER);
                break;
            case "ORGANIZATION_REGISTRATION_ADMIN":
                $to_email = $ADMIN_EMAIL;
                $replyto = $db_rec['EMAIL'];
                $key_arr = array("#NAME#", "#EMAIL#", "#PHONE#", "#MAILFOOTER#");
                $val_arr = array($db_rec['NAME'], $db_rec['EMAIL'], $db_rec['PHONE'], $MAIL_FOOTER);
                break;
            case "ORGANIZATION_REGISTRATION_USER":
                $to_email = $db_rec['EMAIL'];
                $key_arr = array("#NAME#", "#EMAIL#", "#PASSWORD#", "#MAILFOOTER#");
                $val_arr = array($db_rec['NAME'], $db_rec['EMAIL'], $db_rec['PASSWORD'], $MAIL_FOOTER);
                break;
            case "ADMIN_UPDATE_USERPROFILESTATUS_TO_ORGANIZATION":
                $to_email = $db_rec['organization_email'];
                $key_arr = array("#ORGANIZATION_NAME#", "#COMPANY_NAME#", "#PROFILE_STATUS#", "#MailFooter#");
                $val_arr = array($db_rec['Organization_Name'], $db_rec['Company_Name'], $db_rec['Profile_Status'],
                    $MAIL_FOOTER);
                break;
            case "MEMBER_BLOCKED_INACTIVE_USER":
                $to_email = $db_rec['EMAIL'];
                $key_arr = array("#NAME#", "#MAILFOOTER#");
                $val_arr = array($db_rec['NAME'], $MAIL_FOOTER);
                break;
            case "MEMBER_BLOCKED_ACTIVE_USER":
                $to_email = $db_rec['EMAIL'];
                $key_arr = array("#NAME#", "#MAILFOOTER#");
                $val_arr = array($db_rec['NAME'], $MAIL_FOOTER);
                break;
            case "MEMBER_BLOCKED_INACTIVE_DRIVER":
                $to_email = $db_rec['EMAIL'];
                $key_arr = array("#NAME#", "#MAILFOOTER#");
                $val_arr = array($db_rec['NAME'], $MAIL_FOOTER);
                break;
            case "MEMBER_BLOCKED_ACTIVE_DRIVER":
                $to_email = $db_rec['EMAIL'];
                $key_arr = array("#NAME#", "#MAILFOOTER#");
                $val_arr = array($db_rec['NAME'], $MAIL_FOOTER);
                break;
            case "MEMBER_NEWS_SUBSCRIBE_USER":
                $to_email = $db_rec['EMAIL'];
                $key_arr = array("#PHONENO#", "#NAME#", "#EMAILID#", "#MAILFOOTER#");
                $val_arr = array($db_rec['PHONENO'], $db_rec['NAME'], $db_rec['EMAILID'], $MAIL_FOOTER);
                break;
            case "MEMBER_NEWS_UNSUBSCRIBE_USER":
                $to_email = $db_rec['EMAIL'];
                $key_arr = array("#PHONENO#", "#NAME#", "#EMAILID#", "#MAILFOOTER#");
                $val_arr = array($db_rec['PHONENO'], $db_rec['NAME'], $db_rec['EMAILID'], $MAIL_FOOTER);
                break;
            case "OTP_TRANSFER_MONEY":
                $to_email = $db_rec['vEmail'];
                $key_arr = array("#FROMNAME#", "#OTP#", '#TONAME#', "#MAIL_FOOTER#");
                $val_arr = array($db_rec['FromName'], $db_rec['OTP'], $db_rec['ToName'], $MAIL_FOOTER);
                break;
            case "WALLET_AMOUNT_TRANSFER":
                $to_email = $db_rec['vEmail'];
                $key_arr = array("#FROMNAME#", "#AMOUNT#", "#MAIL_FOOTER#");
                $val_arr = array($db_rec['FromName'], $db_rec['amount'], $MAIL_FOOTER);
                break;
            case "WALLET_AMOUNT_TRANSFER_SENDER":
                $to_email = $db_rec['vEmail'];
                $key_arr = array("#FROMNAME#", "#AMOUNT#", "#TONAME#", "#MAIL_FOOTER#");
                $val_arr = array($db_rec['FromName'], $db_rec['amount_sent'], $db_rec['ToName'], $MAIL_FOOTER);
                break;
            case "CRON_SUBSCRIBE_REMAIN_DAYS":
                $to_email = $db_rec['vEmail'];
                $key_arr = array("#FROMNAME#", "#DAYSREMAIN#", "#MAIL_FOOTER#");
                $val_arr = array($db_rec['FromName'], $db_rec['daysRemainTxt'], $MAIL_FOOTER);
                break;
            case "DRIVER_SERVICE_ACCEPTED_REJECT":
                $to_email = $db_rec['vEmail'];
                $key_arr = array("#FROMNAME#", "#SERVICEMSG#", "#MAIL_FOOTER#");
                $val_arr = array($db_rec['FromName'], $db_rec['serviceMsg'], $MAIL_FOOTER);
                break;
           case "DRIVER_SERVICE_REJECTED_BY_ADMIN":
                $to_email = $db_rec['vEmail'];
                $key_arr = array("#FROMNAME#", "#SERVICEMSG#", "#MAIL_FOOTER#");
                $val_arr = array($db_rec['FromName'], $db_rec['serviceMsg'], $MAIL_FOOTER);
                break;
            case "DRIVER_SUBSCRIPTION_SUCCESS":
                $to_email = $db_rec['vEmail'];
                $key_arr = array("#FROMNAME#", "#PLANNAME#", "#MAIL_FOOTER#");
                $val_arr = array($db_rec['FromName'], $db_rec['planName'], $MAIL_FOOTER);
                break;
            case "DRIVER_SUBSCRIPTION_CANCEL":
                $to_email = $db_rec['vEmail'];
                $key_arr = array("#FROMNAME#", "#AMOUNT#", "#MAIL_FOOTER#");
                $val_arr = array($db_rec['FromName'], $db_rec['amount'], $MAIL_FOOTER);
                break;
            case "REFERRAL_AMOUNT_CREDIT_TO_USER":
                $to_email = $db_rec['vEmail'];
                $key_arr = array("#USER_NAME#", "#TRIP_USER_NAME#", "#COMPANY_NAME#", "#AMOUNT#", "#MAIL_FOOTER#");
                $val_arr = array($db_rec['UserName'], $db_rec['TripUserName'], $db_rec['CompanyName'], $db_rec['amount'], $MAIL_FOOTER);
                break;
            case "TRANSACTION_FAILED_OUTSTANDING_AMT":
                $to_email = $db_rec['vEmail'];
                $key_arr = array("#USERNAME#", "#TRIP_NO#", "#MAIL_FOOTER#");
                $val_arr = array($db_rec['username'], $db_rec['TripNo'], $MAIL_FOOTER);
                break;
            case "WALLET_MONEY_CREDITED":
                $to_email = $db_rec['vEmail'];
                $key_arr = array("#USERNAME#", "#AMOUNT#", "#MAIL_FOOTER#");
                $val_arr = array($db_rec['username'], $db_rec['amount'], $MAIL_FOOTER);
                break;
            case "WALLET_MONEY_DEBITED":
                $to_email = $db_rec['vEmail'];
                $key_arr = array("#USERNAME#", "#AMOUNT#", "#MAIL_FOOTER#");
                $val_arr = array($db_rec['username'], $db_rec['amount'], $MAIL_FOOTER);
                break;
            case "CRON_EMAIL_TO_ADMIN":
                $to_email = $ADMIN_EMAIL;
                $key_arr = array("#BookingNo#", "#DRIVER_NAME#", "#RIDER_NAME#", "#SourceAddress#", "#Ddate#", "#MailFooter#");
                $val_arr = array($db_rec['BookingNo'], $db_rec['Driver'], $db_rec['Rider'], $db_rec['SourceAddress'], $db_rec['Ddate'], $MAIL_FOOTER);
                break;
            case "SERVICE_REQUEST_FROM_PROVIDER":
                $to_email = $ADMIN_EMAIL;
                $key_arr = array("#NAME#", "#EMAIL#", "#PHONE#" , "#SERVICE#");
                $val_arr = array($db_rec['name'], $db_rec['email'], $db_rec['phone'],$db_rec['Service'], $MAIL_FOOTER);
                break;
            case "EXPIRED_DOCS_APPROVED_NOTIFICATION":
                $to_email = $db_rec['vEmail'];
                $key_arr = array("#NAME#", "#MAIL_FOOTER#");
                $val_arr = array($db_rec['name'], $MAIL_FOOTER);
                break;
            case "MANUAL_ACCEPT_STORE_ORDER_BY_ADMIN":
                $to_email = $ADMIN_EMAIL;
                $key_arr = array("#ORDERNO#", "#ADMINPANELURL#", "#MailFooter#");
                $val_arr = array($db_rec['ORDER_NO'], $db_rec['ADMIN_URL'], $MAIL_FOOTER);
                break;
            case "SEND_CHARGES__VARIFICATIONCODE":
                $to_email = $db_rec['vEmail'];
                $key_arr = array("#vName#", "#PROVIDER_NAME#", '#TRIP_NUMBER#', '#MATERIAL_FEE#', '#MISC_FEE#', '#PROVIDER_DISCOUNT#', '#TOTALAMOUNT#', '#SERVICECOST#', '#VERIFICATION_CODE#', '#MAILFOOTER#');
                $val_arr = array($db_rec['vName'], $db_rec['ProviderName'], $db_rec['TripId'], $db_rec['fMaterialFee'], $db_rec['fMiscFee'], $db_rec['fDriverDiscount'], $db_rec['totalAmount'], $db_rec['serviceCost'], $db_rec['VerificationCode'], $MAIL_FOOTER);
                break;
            case "SEND_CHARGES__APPROVE":
                $to_email = $db_rec['vEmail'];
                $key_arr = array("#vName#", "#PROVIDER_NAME#", '#TRIP_NUMBER#', '#MATERIAL_FEE#', '#MISC_FEE#', '#PROVIDER_DISCOUNT#', '#VERIFICATION_CODE#', '#TOTALAMOUNT#', '#SERVICECOST#', '#MAILFOOTER#');
                $val_arr = array($db_rec['vName'], $db_rec['ProviderName'], $db_rec['TripId'], $db_rec['fMaterialFee'], $db_rec['fMiscFee'], $db_rec['fDriverDiscount'], $db_rec['AcceptLink'], $db_rec['totalAmount'], $db_rec['serviceCost'], $MAIL_FOOTER);
                break;
            case "MANUAL_TOLL_CHARGES_OTP_MESSAGE":
                $to_email = $db_rec['vEmail'];
                $key_arr = array("#Rider#", "#TOLL_CHARGE#", '#VERIFICATION_CODE#', '#MAILFOOTER#');
                $val_arr = array($db_rec['Rider'], $db_rec['TOLL_CHARGE'], $db_rec['VERIFICATION_CODE'], $MAIL_FOOTER);
                break;
            case "MANUAL_OTHER_CHARGES_OTP_MESSAGE":
                $to_email = $db_rec['vEmail'];
                $key_arr = array("#Rider#", '#OTHER_CHARGE#', '#VERIFICATION_CODE#', '#MAILFOOTER#');
                $val_arr = array($db_rec['Rider'], $db_rec['OTHER_CHARGE'], $db_rec['VERIFICATION_CODE'], $MAIL_FOOTER);
                break;
            case "MANUAL_TOLL_AND_OTHER_CHARGES_OTP_MESSAGE":
                $to_email = $db_rec['vEmail'];
                $key_arr = array("#Rider#", "#TOLL_CHARGE#", '#OTHER_CHARGE#', '#VERIFICATION_CODE#', '#MAILFOOTER#');
                $val_arr = array($db_rec['Rider'], $db_rec['TOLL_CHARGE'], $db_rec['OTHER_CHARGE'], $db_rec['VERIFICATION_CODE'], $MAIL_FOOTER);
                break;
            case "MANUAL_TOLL_AND_OTHER_CHARGES_APPROVE_MESSAGE":
                $to_email = $db_rec['vEmail'];
                $key_arr = array("#vName#", "#PROVIDER_NAME#", '#TRIP_NUMBER#', '#TOLL_CHARGE#', '#OTHER_CHARGE#', '#PROVIDER_DISCOUNT#', '#VERIFICATION_CODE#', '#TOTALAMOUNT#', '#MAILFOOTER#');
                $val_arr = array($db_rec['vName'], $db_rec['ProviderName'], $db_rec['TripId'], $db_rec['TOLL_CHARGE'], $db_rec['OTHER_CHARGE'], $db_rec['fDriverDiscount'], $db_rec['AcceptLink'], $db_rec['totalAmount'], $MAIL_FOOTER);
                break;
            case "START_TRIP_OTP":
                $to_email = $db_rec['vEmail'];
                $key_arr = array("#FROMNAME#", "#OTP#", '#SITE_NAME#', '#DRIVER#', '#MAILFOOTER#');
                $val_arr = array($db_rec['FROMNAME'], $db_rec['OTP'], $SITE_NAME, $db_rec['DRIVER'], $MAIL_FOOTER);
                break;
            case "MANUAL_CANCEL_TRIP_DRIVER_TO_RIDER":
                $to_email = $db_rec['vRiderMail'];
                $key_arr = array("#Rider#", "#BookingNo#", "#Ddate#", "#MailFooter#");
                $val_arr = array($db_rec['RiderName'], $db_rec['vBookingNo'], $db_rec['dCancelDate'], $MAIL_FOOTER);
                break;
            case "ORDER_ACCEPTED_KIOSK":
                $to_email = $db_rec['vEmail'];
                $key_arr = array("#Rider#", "#ORDER_NO#", "#MailFooter#");
                $val_arr = array($db_rec['vName'], $db_rec['vOrderNo'], $MAIL_FOOTER);
                break;
            case "DRIVER_NOTIFY_BID_TASK":
                $to_email = $db_rec['vEmail'];
                $key_arr = array("#PROVIDER_NAME#", "#USER_NAME#", "#TASK_NO#", "#Ddate#", "#SourceAddress#", "#MailFooter#");
                $val_arr = array($db_rec['driverName'], $db_rec['userFullName'], $db_rec['PostNo'], $db_rec['Ddate'], $db_rec['SourceAddress'], $MAIL_FOOTER);
                break;
            case "TRACK_COMPANY_USER_INVITECODE_SEND":
                $to_email = $db_rec['vEmail'];
                $key_arr = array("#NAME#", "#INVITECODE#", "#SITE_NAME#", "#MailFooter#");
                $val_arr = array($db_rec['NAME'], $db_rec['INVITECODE'], $SITE_NAME, $MAIL_FOOTER);
                break;
            case "DRIVER_CANCEL_ORDER":
                $to_email = $ADMIN_EMAIL;
                $key_arr = array("#Driver#", "#BookingNo#", "#Ddate#", "#MailFooter#");
                $val_arr = array($db_rec['vDriver'], $db_rec['vBookingNo'], $db_rec['Ddate'], $MAIL_FOOTER);
                break;
            case "GIFT_CARD_RECEIVED":
                $to_email = $db_rec['vEmail'];
                $key_arr = array("#details#");
                $val_arr = array($db_rec['details']);
                break;
            case "GIFT_CARD_INFO_SEND":
                $to_email = $db_rec['vEmail'];
                $key_arr = array("#details#");
                $val_arr = array($db_rec['details']);
                break;
            case "GIFT_CARD_REDEEM_SUCCESSFULLY_SENT_TO_SENDER":
                $to_email = $db_rec['vEmail'];
                $key_arr = array("#details#");
                $val_arr = array($db_rec['details']);
                break;
            case "GIFT_CARD_REDEEM_SUCCESSFULLY_SENT_TO_RECEIVER":
                $to_email = $db_rec['vEmail'];
                $key_arr = array("#details#");
                $val_arr = array($db_rec['details']);
                break;

            case "USER_RENT_ITEM_INQUIRY":
                $to_email = $db_rec['vOwnerEmail'];
                $key_arr = array("#Rider#", "#USER_NAME#", "#EMAIL#", "#PHONE#", "#RENTITEMNAME#","#RENT_POST_NO#", "#MailFooter#");
                $val_arr = array($db_rec['OwnerName'], $db_rec['RiderName'], $db_rec['vEmail'], $db_rec['cellno'], $db_rec['RentItemName'], $db_rec['vRentItemPostNo'], $MAIL_FOOTER);
                break;

            case "USER_RENT_ITEM_APPROVED":
                $to_email = $db_rec['EMAIL'];
                $key_arr = array("#Rider#", "#RENT_ITEM_NAME#","#RENT_POST_NO#", "#MailFooter#");
                $val_arr = array($db_rec['NAME'], $db_rec['RENT_ITEM_NAME'], $db_rec['RENT_POST_NO'], $MAIL_FOOTER);
                break;

            case "USER_RENT_ITEM_REJECTED":
                $to_email = $db_rec['EMAIL'];
                $key_arr = array("#Rider#", "#RejectReason#", "#RENT_ITEM_NAME#","#RENT_POST_NO#", "#MailFooter#");
                $val_arr = array($db_rec['NAME'], $db_rec['REJECTREASON'], $db_rec['RENT_ITEM_NAME'], $db_rec['RENT_POST_NO'], $MAIL_FOOTER);
                break;

            case "USER_RENT_ITEM_EXPIRED":
                $to_email = $db_rec['EMAIL'];
                $key_arr = array("#Rider#", "#RENT_ITEM_NAME#","#RENT_POST_NO#", "#MailFooter#");
                $val_arr = array($db_rec['NAME'], $db_rec['RENT_ITEM_NAME'], $db_rec['RENT_POST_NO'], $MAIL_FOOTER);
                break;

            case "USER_RENT_ITEM_DELETED":
                $to_email = $db_rec['EMAIL'];
                $key_arr = array("#Rider#", "#RENT_ITEM_NAME#", "#DeleteReason#","#RENT_POST_NO#", "#MailFooter#");
                $val_arr = array($db_rec['NAME'], $db_rec['RENT_ITEM_NAME'], $db_rec['DELETEREASON'], $db_rec['RENT_POST_NO'], $MAIL_FOOTER);
                break;

            case "USER_RENT_ITEM_POSTED":
                $to_email = $ADMIN_EMAIL;
                $key_arr = array("#USER#", "#RENT_ITEM_NAME#","#RENT_POST_NO#", "#MailFooter#");
                $val_arr = array($db_rec['NAME'], $db_rec['RENT_ITEM_NAME'], $db_rec['RENT_POST_NO'], $MAIL_FOOTER);
                break;
            case "PASSENGERS_NOTIFIED_WHEN_RIDE_CANCELED_BY_PUBLISHER":
                $to_email = $db_rec['EMAIL'];
                $key_arr = array("#XXXXX#", "#DATE#", "#USER_TYPE#", "#REASON#","#START_LOCATION#", "#END_LOCATION#" , "#MailFooter#");
                $val_arr = array($db_rec['BOOKING_NO'], $db_rec['DATE'], $db_rec['USER_TYPE'], $db_rec['REASON'],$db_rec['START_LOCATION'],$db_rec['END_LOCATION'], $MAIL_FOOTER);
                break;
            case "PUBLISHERS_NOTIFIED_WHEN_RIDE_CANCELED_BY_PASSENGERS":
                $to_email = $db_rec['EMAIL'];
                $key_arr = array("#XXXXX#", "#DATE#", "#USER_TYPE#", "#REASON#","#START_LOCATION#", "#END_LOCATION#" , "#MailFooter#");
                $val_arr = array($db_rec['PUBLISH_RIDE_NO'], $db_rec['DATE'], $db_rec['USER_TYPE'], $db_rec['REASON'],$db_rec['START_LOCATION'],$db_rec['END_LOCATION'], $MAIL_FOOTER);
                break;
            case "PASSENGERS_NOTIFIED_WHEN_RIDE_ACCEPTED_BY_PUBLISHER":
                $to_email = $db_rec['EMAIL'];
                $key_arr = array("#XXXXX#","#START_LOCATION#", "#END_LOCATION#" , "#MailFooter#");
                $val_arr = array($db_rec['BOOKING_NO'],$db_rec['START_LOCATION'],$db_rec['END_LOCATION'], $MAIL_FOOTER);
                break;
            case "RIDE_PUBLISHED_NOTIFY_ADMIN":
                $to_email = $ADMIN_EMAIL;
                $key_arr = array("#NAME#","#PHONE#", "#SOURCE_ADDRESS#", "#DESTINATION_ADDRESS#", "#DATE#", "#SEATS#", "#PRICE#", "#DRIVER_DETAILS#", "#MAILFOOTER#");
                $val_arr = array($db_rec['vName'], $db_rec['vPhone'], $db_rec['vSourceAddresss'], $db_rec['vDestinationAddress'], $db_rec['Date'], $db_rec['Seats'], $db_rec['PricePerSeat'], $db_rec['DriverDetails'], $MAIL_FOOTER);
                break;
            case "RIDE_SHARE_BOOKING_NOTIFY_PUBLISHER":
                $to_email = $db_rec['vEmail'];
                $key_arr = array("#PUBLISHER#", "#USERNAME#", "#PHONE#", "#SEATS#", "#BOOKING_NO");
                $val_arr = array($db_rec['vPublisherName'], $db_rec['vName'], $db_rec['vPhone'], $db_rec['Seats'], $db_rec['vBookingNo']);
                break;
            case "TRACKING_COMPANY_REGISTRATION_ADMIN":
                $to_email = $ADMIN_EMAIL;
                $replyto = $db_rec['EMAIL'];
                $key_arr = array("#NAME#", "#EMAIL#", "#MAILFOOTER#");
                $val_arr = array($db_rec['NAME'], $db_rec['EMAIL'], $MAIL_FOOTER);
                break;
            case "TRACKING_COMPANY_REGISTRATION_USER":
                $to_email = $db_rec['EMAIL'];
                $key_arr = array("#NAME#", "#EMAIL#", "#MAILFOOTER#");
                $val_arr = array($db_rec['NAME'], $db_rec['EMAIL'], $MAIL_FOOTER);
                break;

            case "REWARD_AMOUNT_CREDIT_TO_USER":
                $to_email = $db_rec['vEmail'];
                $key_arr = array("#USER_NAME#", "#AMOUNT#", "#TRIP_NUMBER#", "#MAIL_FOOTER#");
                $val_arr = array($db_rec['UserName'], $db_rec['amount'], $db_rec['vRideNo'], $MAIL_FOOTER);
                break;

        }

        $emailsend = 0;
        if (trim($to_email) != "") {

            $maillanguage = $LANG_OBJ->FetchMemberSelectedLanguage($to_email);
            $maillanguage = (isset($maillanguage) && $maillanguage != '') ? $maillanguage : 'EN';
            $mailsubject = $orgMailSub . " " . $res[0]['vSubject_' . $maillanguage];

            if ($type == 'GIFT_CARD_RECEIVED') {
                $mailsubject = str_replace('#NAME#', $db_rec['senderName'], $mailsubject);
            }
            
            if(empty($res)) {
                $mailsubject = $type;
            }
            $tMessage = $res[0]['vBody_' . $maillanguage];
            $tMessage = str_replace($key_arr, $val_arr, $tMessage);
            $maillanguagedirection = $LANG_OBJ->FetchMemberSelectedLanguageDir($maillanguage);
            if ($maillanguagedirection == "rtl" && $type != "RIDER_INVOICE") {
                $tMessage = "<div style='text-align:right;direction:rtl;'>" . $tMessage . "</div>";
            }
            $tMessage = $this->general_mail_format_html($tMessage);
            $headers = '';
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= "From: " . $EMAIL_FROM_NAME . "< $NOREPLY_EMAIL >" . "\n";
            /* $headers = "MIME-Version: 1.0\n";
              $headers .= "Content-type: text/html; charset=utf-8\nContent-Transfer-Encoding: 8bit\nX-Priority: 1\nX-MSMail-Priority: High\n";
              $headers .= "From: " . $EMAIL_FROM_NAME . " < $NOREPLY_EMAIL >" . "\n" . "X-Mailer: PHP/" . phpversion() . "\nX-originating-IP: " . $_SERVER['REMOTE_ADDR'] . "\n"; */

            if (!defined('IS_DUMMY_DATA')) {
                if ($MAILGUN_ENABLE == "Yes") {
                    $emailsend = $this->SendMailToMemberSMTP($to_email, $NOREPLY_EMAIL, $EMAIL_FROM_NAME, $mailsubject, $tMessage, $replyto);
                } else {
                    $emailsend = mail($to_email, $mailsubject, $tMessage, $headers);
                }
            }
            /* if ($_SERVER['HTTP_HOST'] == "192.168.1.131" || $_SERVER['HTTP_HOST'] == "192.168.1.141") {
              $emailsend = mail($to_email, $mailsubject, $tMessage, $headers);
              } else {
              if ($MAILGUN_ENABLE == "Yes") {
              $emailsend = $this->send_email_smtp($to_email, $NOREPLY_EMAIL, $EMAIL_FROM_NAME, $mailsubject, $tMessage, $replyto);
              } else {
              $emailsend = mail($to_email, $mailsubject, $tMessage, $headers);
              }
          } */
        }
        return $emailsend;
    }

    public function general_mail_format_html($mail_body)
    {
        global $tconfig, $COPYRIGHT_TEXT, $COMPANY_NAME, $logogpath, $template, $maillanguagedirection, $THEME_OBJ;
        include_once($tconfig["tpanel_path"] . 'common.php');
        if ($THEME_OBJ->isCubexThemeActive() == 'Yes' || $THEME_OBJ->isCubeJekXv3ThemeActive() == 'Yes' || $THEME_OBJ->isCubeXv2ThemeActive() == 'Yes' || $THEME_OBJ->isDeliveryKingXv2ThemeActive() == 'Yes') {
            $email_logo = $tconfig['tsite_upload_apptype_images'] . $template . '/admin-logo.png';
        } else if ($THEME_OBJ->isCubeJekXThemeActive() == 'Yes' || $THEME_OBJ->isCubeJekXv2ThemeActive() == 'Yes' || $THEME_OBJ->isCJXDoctorv2ThemeActive() == 'Yes' || $THEME_OBJ->isRideCXThemeActive() == 'Yes' || $THEME_OBJ->isServiceXThemeActive() == 'Yes' || $THEME_OBJ->isDeliveryKingThemeActive() == 'Yes' || $THEME_OBJ->isServiceXv2ThemeActive() == 'Yes' || $THEME_OBJ->isRideCXv2ThemeActive() == 'Yes') {
            $email_logo = $tconfig['tsite_upload_apptype_images'] . $template . '/logo-admin.png';
        } else if ($THEME_OBJ->isDeliverallXThemeActive() == 'Yes' || $THEME_OBJ->isDeliverallXv2ThemeActive() == 'Yes' || $THEME_OBJ->isRideDeliveryXThemeActive() == 'Yes' || $THEME_OBJ->isDeliveryXThemeActive() == 'Yes') {
            $email_logo = $tconfig['tsite_upload_apptype_images'] . $template . '/logo.png';
        } else {
            $email_logo = $tconfig['tsite_upload_apptype_images'] . $template . '/logo.png';
        }
        $mail_str = "";
        $mail_str = '<table style="margin:0 auto;width: 100%; max-width: 800px;" border="0" cellpadding="0" cellspacing="0">
        <tr>
        <td style="background: #54545e;padding: 30px 40px 0 40px;">
        <table cellpadding="0" cellspacing="0" width="100%" style="background-color: #fff; text-align: center; padding: 15px;">
        <tr>
        <td><img src="' . $email_logo . '"  style="vertical-align:top;"></td>
        </tr>
        </table>
        </td>
        </tr>
        <tr>
        <td style="padding: 0px 40px;background-color: #f5f5f5;">
        <table cellpadding="0" cellspacing="0" style="width:100%">
        <tr>
        <td style="padding:25px; border:1px solid #e1e1e1;background-color: #fff; line-height: 25px;font-family:Arial, Helvetica, sans-serif; font-size:16px; color:#4d4d4d; text-align:justify;">
        ' . $mail_body . '
        </td>
        </tr>
        <tr>
        <td style="padding:25px 0; text-align:center">
        ' . str_replace('#YEAR#', date('Y') , $COPYRIGHT_TEXT)  . '<br>
        <a href="' . $tconfig['tsite_url'] . '" style="color:#070707; font-size:13px; font-family:Arial, Helvetica, sans-serif; text-decoration:none;">' . $COMPANY_NAME . '</a>
        </td>
        </tr>
        </table>
        </td>
        </tr>
        </tr>
        </table>';
        return $mail_str;
    }

    public function SendMailToMemberSMTP($to, $from, $fromname, $subject, $body, $replyto, $attachment_path1 = "", $attachment_path2 = "", $pdf_attach = "")
    {
        global $site_path, $emailattach_dir, $MAILGUN_USER, $MAILGUN_KEY, $MAILGUN_HOST, $tconfig;
        require_once $tconfig['tpanel_path'] . 'assets/libraries/class.phpmailer.php';
        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->Host = $MAILGUN_HOST;
        $mail->IsSMTP(); 
        $mail->Host = $MAILGUN_HOST; 

        $expresion_regular = '/^[a-zA-Z0-9._%+-]+@gmail\.com$/';            
        if (preg_match($expresion_regular, $MAILGUN_USER)) {
            $mail->Host = "smtp.gmail.com"; 
        }  
        
        $mail->SMTPDebug = false;
        /* $mail->SMTPDebug = 1;
         $mail->SMTPDebug = true;*/
        /*1 = errors and messages
        2 = messages only */
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = "tls";
        /* sets GMAIL as the SMTP server */
        /* $mail->Host = "smtp.mailgun.org"; */
        /*$mail->Port = 587;*/
		$mail->Port = 2525;
        if ($_SERVER['HTTP_HOST'] == "webprojectsdemo.com") {
            $mail->Username = $MAILGUN_USER;
            $mail->Password = $MAILGUN_KEY;
        } else {
            $mail->Username = $MAILGUN_USER;
            $mail->Password = $MAILGUN_KEY;
        }
        if ($replyto != "") {
            $mail->AddReplyTo($replyto, "");
        }
        $mail->SetFrom($from, $fromname);
        $mail->Subject = $subject;
        $mail->MsgHTML($body);
        $mail->AddAddress($to, "");
        if ($attachment_path1 != "") {
            $mail->AddAttachment($emailattach_dir . $attachment_path1);
        }
        if ($attachment_path2 != "") {
            $mail->AddAttachment($emailattach_dir . $attachment_path2);
        }
        if ($pdf_attach != "") {
            $mail->AddAttachment($pdf_attach);
        }
        if (!$mail->Send()) {
				if (isset($_REQUEST['testPM']) && !empty($_REQUEST['testPM'])) {
					echo 'Mailer Error: ' . $mail->ErrorInfo;
				}
            /* echo 'Message could not be sent.';
              echo 'Mailer Error: ' . $mail->ErrorInfo;

              exit;*/
            return false;
        } else {
            return true;
        }
    }

    public function orderemaildata($iOrderId, $sendTo)
    {
        global $tconfig, $obj, $COPYRIGHT_TEXT, $COMPANY_NAME, $EMAIL_FROM_NAME, $NOREPLY_EMAIL, $LANG_OBJ;
        ob_start();
        require_once $tconfig["tpanel_path"] . "orderdetails_mail_format.php";
        $mail_content = ob_get_clean();
        $maildata['vEmail'] = $returnArrData['vEmail'];
        $maildata['UserName'] = $returnArrData['UserName'];
        $maildata['details'] = $mail_content;
        if ($returnArrData['iStatusCode'] == '2') {
            $maildata['CompanyName'] = $returnArrData['CompanyName'];
            if ($returnArrData['eTakeaway'] == 'Yes') {
                $mailResponse = $this->SendMailToMember("USER_ORDER_CONFIRM_INVOICE_TAKEAWAY", $maildata);
            } else {
                $mailResponse = $this->SendMailToMember("USER_ORDER_CONFIRM_INVOICE", $maildata);
            }
            $mailResponse = $this->SendMailToMember("USER_ORDER_CONFIRM_INVOICE_ADMIN", $maildata);
        }
        return $mailResponse;
    }

    public function orderemaildataRecipt($iOrderId, $sendTo, $iServiceId)
    {
        global $tconfig, $obj, $COPYRIGHT_TEXT, $COMPANY_NAME, $EMAIL_FROM_NAME, $NOREPLY_EMAIL, $LANG_OBJ;
        ob_start();
        require_once $tconfig["tpanel_path"] . "orderdetails_mail_format.php";
        
        if (!empty($returnArrData['vEmail'])) {
            $mailResponse = '';
            $mail_content = ob_get_clean();
            $maildata['vEmail'] = $returnArrData['vEmail'];
            $maildata['UserName'] = $returnArrData['UserName'];
            $maildata['CompanyName'] = $returnArrData['CompanyName'];
            $maildata['details'] = $mail_content;
            if ($returnArrData['eTakeaway'] == 'Yes') {
                $mailResponse = $this->SendMailToMember("USER_ORDER_DELIVER_INVOICE_TAKEAWAY", $maildata);
            } else {
                $mailResponse = $this->SendMailToMember("USER_ORDER_DELIVER_INVOICE", $maildata);
            }
        } else {
            $mailResponse = '3';
        }
        ob_end_clean();
        return $mailResponse;
    }

    public function giftcardemaildataRecipt($GenerateGiftCard, $vGiftCardCode)
    {
        global $tconfig, $obj, $GIFT_CARD_OBJ, $LANG_OBJ, $SITE_NAME;
        
        if ($GenerateGiftCard['tReceiverEmail'] != '') {
            ob_start();
            require_once $tconfig["tpanel_path"] . "gift_card_email.php";
            $mailResponse = '';
            $mail_content = ob_get_contents();
            $UserData = $GIFT_CARD_OBJ->getUserData($GeneralMemberId, $GeneralUserType);
            $maildata['vEmail'] = $GenerateGiftCard['tReceiverEmail'];
            $maildata['senderName'] = $UserData['userName'];
            $maildata['details'] = $mail_content;

            $mailResponse = $this->SendMailToMember("GIFT_CARD_RECEIVED", $maildata);
        } else {
            $mailResponse = '3';
        }
        ob_end_clean();
        return $mailResponse;
    }


    public function giftCardeMailSent($GenerateGiftCard, $vGiftCardCode, $maildata, $type)
    {
        global $tconfig, $obj, $GIFT_CARD_OBJ, $LANG_OBJ, $SITE_NAME;
        
        if ($maildata['vEmail'] != '') {
            ob_start();
            include $tconfig["tpanel_path"] . "gift_card_email.php";
            $mailResponse = '';
            $mail_content = ob_get_contents();
            $maildata['vEmail'] = $maildata['vEmail'];
            $maildata['details'] = $mail_content;
            if($type == 'GIFT_CARD_RECEIVED'){
                $UserData = $GIFT_CARD_OBJ->getUserData($GeneralMemberId, $GeneralUserType);
                $maildata['senderName'] = $UserData['userName'];
            }

            $mailResponse = $this->SendMailToMember($type, $maildata);
        } else {
            $mailResponse = '3';
        }
        ob_end_clean();
        return $mailResponse;
    }

    public function sendCode($mobileNo, $code, $fpass = 'code', $pass = '')
    {
        global $site_path, $langage_lbl, $CONFIG_OBJ;
        $mobileNo = clearPhone($mobileNo);
        $mobileNo = $code . $mobileNo;
        require_once TPATH_CLASS . 'twilio/Services/Twilio.php';
        $account_sid = $CONFIG_OBJ->getConfigurations("configurations", "MOBILE_VERIFY_SID_TWILIO");
        $auth_token = $CONFIG_OBJ->getConfigurations("configurations", "MOBILE_VERIFY_TOKEN_TWILIO");
        $twilioMobileNum = $CONFIG_OBJ->getConfigurations("configurations", "MOBILE_NO_TWILIO");
        $client = new Services_Twilio($account_sid, $auth_token);
        $toMobileNum = "+" . $mobileNo;
        if ($fpass == "forgot") {
            $text_prefix_reset_pass = $CONFIG_OBJ->getConfigurations("configurations", "PREFIX_PASS_RESET_SMS");
            $code = decrypt($pass);
            $verificationCode = $text_prefix_reset_pass . ' ' . $code;
        } else {
            $str = "select * from send_message_templates where vEmail_Code='VERIFICATION_CODE_MESSAGE'";
            $res = $obj->MySQLSelect($str);
            $text_prefix_verification_code = $res[0]['vBody_EN'];
            $code = mt_rand(1000, 9999);
            $verificationCode = $text_prefix_verification_code . ' ' . $code;
        }
        try {
            $sms = $client->account->messages->sendMessage($twilioMobileNum, $toMobileNum, $verificationCode);
            $returnArr['action'] = "1";
        } catch (Services_Twilio_RestException $e) {
            $returnArr['action'] = "0";
        }
        $returnArr['verificationCode'] = $code;
        return $returnArr;
    }

    public function CheckMobileVerification($userid, $type)
    {
        global $obj, $tconfig;
        if ($type == 'rider') {
            $where = " iUserId = '$userid'";
            $table = "register_user";
            $cur = 'vCurrencyPassenger';
        } else {
            $where = "iDriverId = '$userid'";
            $table = "register_driver";
            $cur = 'vCurrencyDriver';
        }
        $db_sql = "select * from $table WHERE $where";
        $db_user = $obj->MySQLSelect($db_sql);
        if ($db_user[0]['vPhone'] != "" && $db_user[0]['vEmail'] != "") {
            $_SESSION['sess_iMemberId'] = $userid;
            $_SESSION['sess_iUserId'] = $userid;
            $_SESSION["sess_vName"] = $db_user[0]['vName'];
            $_SESSION["sess_vLastName"] = isset($db_user[0]['vName']) ? mb_convert_case($db_user[0]['vLastName'], MB_CASE_TITLE, 'UTF-8'): '';
          //   $_SESSION["sess_vLastName"] = isset($db_user[0]['vName']) ? ucfirst($db_user[0]['vLastName']) : '';
            $_SESSION["sess_vEmail"] = $db_user[0]['vEmail'];
            $_SESSION["sess_eGender"] = $db_user[0]['eGender'];
            $_SESSION["sess_vCurrency"] = $db_user[0][$cur];
            if ($type == 'rider') {
                $_SESSION["sess_user"] = "rider";
                $_SESSION["sess_vImage"] = $db_user[0]['vImgName'];
                $link = $tconfig["tsite_url"] . "profile_rider.php";
                unset($_SESSION['fb_user']);
                header("Location:" . $link);
                exit;
            } else {
                $_SESSION["sess_user"] = "driver";
                $_SESSION["sess_vImage"] = $db_user[0]['vImage'];
                $link = $tconfig["tsite_url"] . "profile.php";
                unset($_SESSION['fb_user']);
                header("Location:" . $link);
                exit;
            }
        } else {
            $type = base64_encode(base64_encode($type));
            $id = encrypt($userid);
            $link = $tconfig["tsite_url"] . "phone_number_verification.php?action=" . $type . "&id=" . $id;
            header("Location:" . $link);
            exit;
        }
    }
}

?>