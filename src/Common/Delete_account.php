<?php



namespace Kesk\Web\Common;

class Delete_account
{
    public function __construct() {}

    public function accountDelete($iMemberId, $UserType): void
    {
        global $tconfig;
        if ('Passenger' === $UserType) {
            $data = $this->checkAccountDelete_User($iMemberId);
        } elseif ('Driver' === $UserType) {
            $data = $this->checkAccountDelete_Driver($iMemberId);
        } elseif ('Company' === $UserType) {
            $data = $this->checkAccoutDelete_Store($iMemberId);
        } elseif ('Tracking' === $UserType) {
            $data = $this->checkAccoutDelete_Tracking($iMemberId);
        }
        if (!empty($data) && \count($data) > 0) {
            $returnArr['Action'] = $data['status'];
            $returnArr['message'] = $data['message'];
            if (1 === $data['status']) {
                $returnArr['Url'] = $tconfig['tsite_url'].'account_delete_process.php?GeneralMemberId='.$iMemberId.'&GeneralUserType='.$UserType;
            }
        } else {
            $returnArr['Action'] = 0;
            $returnArr['message'] = '--';
        }
        setDataResponse($returnArr);
    }

    public function signIn($iMemberId, $UserType, $mobileNo, $vPhoneCode, $email)
    {
        global $obj, $SIGN_IN_OPTION;
        $checkValid = checkMemberDataInfo($mobileNo, '', $UserType, $vPhoneCode, $iMemberId, '', 1);
        if ('Driver' === $UserType) {
            $getData = $this->getDriverDetails($iMemberId, $mobileNo);
        } elseif ('Company' === $UserType) {
            $getData = $this->getCompanyDetails($iMemberId, $mobileNo);
        } elseif ('Tracking' === $UserType) {
            $getData = $this->getTrackingUserDetails($iMemberId);
        } else {
            $getData = $this->getUserDetails($iMemberId, $mobileNo);
        }
        $returnArr['Action'] = '1';
        if (1 === $checkValid['status']) {
            $returnArr['showEnterPassword'] = 'No';
            $returnArr['showEnterOTP'] = 'No';
            if ('Password' === $SIGN_IN_OPTION) {
                $returnArr['showEnterPassword'] = 'Yes';
            } elseif ('OTP' === $SIGN_IN_OPTION) {
                $returnArr['showEnterOTP'] = 'Yes';
                $this->sendOtp($iMemberId, $UserType, $vPhoneCode, $mobileNo);
            }
        } else {
            $returnArr['message'] = 'LBL_INVALID_PHONE_NUMBER';
            $returnArr['Action'] = '0';
        }

        return $returnArr;
    }

    public function AuthenticateMember($iMemberId, $UserType, $mobileNo, $vPhoneCode, $email, $password)
    {
        global $obj, $SIGN_IN_OPTION, $AUTH_OBJ;
        $checkValid = checkMemberDataInfo($mobileNo, '', $UserType, $vPhoneCode, $iMemberId, '', 1);
        if (1 === $checkValid['status']) {
            if ('Driver' === $UserType) {
                $returnArr['Details'] = $this->getDriverDetails($iMemberId);
            } elseif ('Company' === $UserType) {
                $returnArr['Details'] = $this->getCompanyDetails($iMemberId);
            } elseif ('Tracking' === $UserType) {
                $returnArr['Details'] = $this->getTrackingUserDetails($iMemberId);
            } else {
                $returnArr['Details'] = $this->getUserDetails($iMemberId);
            }
            if ('PASSWORD' === strtoupper($SIGN_IN_OPTION)) {
                $hash = $returnArr['Details']['vPassword'];
                $checkValidPass = $AUTH_OBJ->VerifyPassword($password, $hash);
                if (0 === $checkValidPass) {
                    $returnArr['Action'] = '0';
                    $returnArr['message'] = 'LBL_WRONG_DETAIL';
                } else {
                    $returnArr['Action'] = '1';
                }
            }
        } else {
            $returnArr['Action'] = '0';
            $returnArr['message'] = 'LBL_NOT_FIND';
        }

        return $returnArr;
    }

    public function AuthenticateMemberWithOtp($iMemberId, $UserType, $mobileNo, $vPhoneCode, $otp)
    {
        global $obj;
        $checkValid = checkMemberDataInfo($mobileNo, '', $UserType, $vPhoneCode, $iMemberId, '');
        if (1 === $checkValid['status']) {
            if ('Driver' === $UserType) {
                $query_1 = 'SELECT iDriverId FROM register_driver WHERE 1 = 1 AND iDriverId = '.$iMemberId.' AND iOTP = '.$otp;
            } else {
                $query_1 = "SELECT iUserId FROM register_user WHERE iUserId = '".$iMemberId."' AND iOTP =".$otp;
            }
            $data = $obj->MySQLSelect($query_1);
            if (0 === \count($data)) {
                $returnArr['Action'] = '0';
                $returnArr['message'] = 'LBL_VERIFICATION_CODE_INVALID';
            } else {
                if ('Driver' === $UserType) {
                    $returnArr['Details'] = $this->getDriverDetails($iMemberId);
                } else {
                    $returnArr['Details'] = $this->getUserDetails($iMemberId);
                }
                $returnArr['Action'] = '1';
            }
        } else {
            $returnArr['Action'] = '0';
            $returnArr['message'] = 'LBL_NOT_FIND';
        }

        return $returnArr;
    }

    public function updateUser($iMemberId): void
    {
        global $obj;
        $vPhone = get_value('register_user', 'vPhone', 'iUserId', $iMemberId, '', 'true');
        $where = " iUserId = '{$iMemberId}' ";
        $Data_update_member['eStatus'] = 'Deleted';
        $Data_update_member['vPhone'] = $vPhone.'(Deleted)';
        $register_user = $obj->MySQLQueryPerform('register_user', $Data_update_member, 'update', $where);
    }

    public function updateDriver($iMemberId): void
    {
        global $obj;
        $vPhone = get_value('register_driver', 'vPhone', 'iDriverId', $iMemberId, '', 'true');
        $where = " iDriverId = '{$iMemberId}' ";
        $Data_update_member['eStatus'] = $vPhone.'(Deleted)';
        $obj->MySQLQueryPerform('register_driver', $Data_update_member, 'update', $where);
    }

    public function updateCompany($iMemberId): void
    {
        global $obj;
        $vPhone = get_value('company', 'vPhone', 'iCompanyId', $iMemberId, '', 'true');
        $where = " iCompanyId = '{$iMemberId}' ";
        $Data_update_member['eStatus'] = $vPhone.'(Deleted)';
        $obj->MySQLQueryPerform('company', $Data_update_member, 'update', $where);
    }

    public function updateTrackingUser($iMemberId): void
    {
        global $obj;
        $vPhone = get_value('track_service_users', 'vPhone', 'iTrackServiceUserId', $iMemberId, '', 'true');
        $where = " iTrackServiceUserId = '{$iMemberId}' ";
        $Data_update_member['eStatus'] = $vPhone.'(Deleted)';
        $obj->MySQLQueryPerform('track_service_users', $Data_update_member, 'update', $where);
    }

    private function checkAccountDelete_User($iMemberId)
    {
        global $obj, $MODULES_OBJ;
        $OutstandingAmount = GetPassengerOutstandingAmount($iMemberId);
        $register_user = $obj->MySQLSelect("SELECT iUserId, vTripStatus FROM `register_user` WHERE iUserId = '".$iMemberId."'");
        $trips = $obj->MySQLSelect("SELECT iTripId,iActive FROM `trips` WHERE iUserId = '".$iMemberId."' AND iActive IN ('Active' , 'On Going Trip','Arrived') ");
        $cab_request_now = $obj->MySQLSelect("SELECT iTripId,eStatus FROM `cab_request_now` WHERE iUserId = '".$iMemberId."' AND iTripId != '' AND eStatus IN ('Pending')");
        $cab_booking = $obj->MySQLSelect("SELECT iTripId,eStatus FROM `cab_booking` WHERE iUserId = '".$iMemberId."' AND eStatus IN ('Pending','Assign','Accepted')");
        $orders = [];
        if ($MODULES_OBJ->isDeliverAllFeatureAvailable()) {
            $orders = $obj->MySQLSelect("SELECT iOrderId,iStatusCode FROM `orders` WHERE `iUserId` = '".$iMemberId."' AND iStatusCode IN (1,2,4,5)");
        }
        $Arr_Return['status'] = 0;
        if (0 === $OutstandingAmount && 0 === \count($trips) && 0 === \count($cab_request_now) && 0 === \count($cab_booking) && 0 === \count($orders)) {
            $Arr_Return['status'] = 1;
            $Arr_Return['message'] = 'LBL_CONTINUE_DELETE_ACCOUNT';
        } else {
            if ($OutstandingAmount > 0) {
                $LBL_MES = 'LBL_DELETE_ACCOUNT_PENDING_OUTSTANDING_AMOUNT';
            } elseif (\count($trips) > 0 || \count($cab_request_now) > 0 || \count($cab_booking) > 0 || \count($orders) > 0) {
                $LBL_MES = 'LBL_DELETE_ACCOUNT_PENDING_TRIP_ORDER_JOB';
            }
            $Arr_Return['message'] = $LBL_MES;
        }

        return $Arr_Return;
    }

    private function checkAccountDelete_Driver($iMemberId)
    {
        global $obj;
        $vTimeZone = $_REQUEST['vTimeZone'] ?? '';
        $serverTimeZone = date_default_timezone_get();
        $date_ = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s')));
        $TimeZoneOffset = converToTz($date_, $serverTimeZone, $vTimeZone, 'P');
        $subSqlBook = " AND dBooking_date > ((CONVERT_TZ(NOW(), 'SYSTEM', '".$TimeZoneOffset."')) - INTERVAL 30 MINUTE)";
        $trips = $obj->MySQLSelect("SELECT iTripId,iDriverId,iActive,eDriverPaymentStatus FROM `trips` WHERE iDriverId = '".$iMemberId."' AND iActive != 'Canceled' AND eDriverPaymentStatus = 'Unsettelled' ");
        $cab_booking = $obj->MySQLSelect("SELECT iTripId,eStatus FROM `cab_booking` WHERE iDriverId = '".$iMemberId."' AND eStatus IN ('Pending','Assign','Accepted') {$subSqlBook}");
        $Arr_Return['status'] = 0;
        if (0 === \count($trips) && 0 === \count($cab_booking)) {
            $Arr_Return['status'] = 1;
            $Arr_Return['message'] = 'LBL_CONTINUE_DELETE_ACCOUNT';
        } else {
            if (\count($trips) > 0 || \count($cab_booking) > 0) {
                $LBL_MES = 'LBL_DELETE_ACCOUNT_PENDING_TRIP_ORDER_JOB';
            }
            $Arr_Return['message'] = $LBL_MES;
        }

        return $Arr_Return;
    }

    private function checkAccoutDelete_Store($iMemberId)
    {
        global $obj;
        $statusCode = '1,2,4,5';
        $orders = $obj->MySQLSelect("SELECT iOrderId FROM orders WHERE iCompanyId='".$iMemberId."' AND iStatusCode IN ({$statusCode})");
        $Arr_Return['status'] = 0;
        if (0 === \count($orders)) {
            $Arr_Return['status'] = 1;
            $Arr_Return['message'] = 'LBL_CONTINUE_DELETE_ACCOUNT';
        } else {
            if (\count($orders) > 0) {
                $LBL_MES = 'LBL_DELETE_ACCOUNT_PENDING_TRIP_ORDER_JOB';
            }
            $Arr_Return['message'] = $LBL_MES;
        }

        return $Arr_Return;
    }

    private function checkAccoutDelete_Tracking($iMemberId)
    {
        global $obj;
        $Tracking_user = $obj->MySQLSelect("SELECT iTrackServiceUserId FROM track_service_users WHERE iTrackServiceUserId='".$iMemberId."'");
        $Arr_Return['status'] = 0;
        if (\count($Tracking_user) > 0) {
            $Arr_Return['status'] = 1;
            $Arr_Return['message'] = 'LBL_CONTINUE_DELETE_ACCOUNT';
        } else {
            $Arr_Return['message'] = '--';
        }

        return $Arr_Return;
    }

    private function sendOtp($iMemberId, $UserType, $vPhoneCode, $mobileNo)
    {
        global $LANG_OBJ, $obj, $MOBILE_NO_VERIFICATION_METHOD, $COMM_MEDIA_OBJ, $SITE_NAME;
        $otp = random_int(1_000, 9_999);
        $Data_update_member['iOTP'] = $otp;
        $vLangCode = $LANG_OBJ->FetchDefaultLangData('vCode');
        $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLangCode, '1', '');
        $str = "SELECT * from send_message_templates where vEmail_Code = 'AUTH_OTP'";
        $res = $obj->MySQLSelect($str);
        $prefix = $res[0]['vBody_'.$vLangCode];
        $message = str_replace(['#OTP#', '#SITE_NAME#'], [$otp, $SITE_NAME], $prefix);
        $toMobileNum = '+'.$vPhoneCode.$mobileNo;
        $returnArr['MOBILE_NO_VERIFICATION_METHOD'] = $MOBILE_NO_VERIFICATION_METHOD;
        if ('FIREBASE' !== strtoupper($MOBILE_NO_VERIFICATION_METHOD)) {
            $result = $COMM_MEDIA_OBJ->SendSystemSMS($toMobileNum, $mobileNo, $message);
        } else {
            $result = 1;
        }
        if (0 === $result) {
            if ('Driver' === $UserType) {
                $where = " iDriverId = '{$iMemberId}' ";
                $obj->MySQLQueryPerform('register_driver', $Data_update_member, 'update', $where);
            } else {
                $where = " iUserId = '{$iMemberId}' ";
                $obj->MySQLQueryPerform('register_user', $Data_update_member, 'update', $where);
            }
        }

        return $result;
    }

    private function getDriverDetails($iMemberId, $phoneNo = '')
    {
        global $obj, $tconfig;
        $sql = '';
        if ('' !== $phoneNo) {
            $sql .= "AND vPhone = '{$phoneNo}'";
        }
        $query_1 = 'SELECT vName,vLastName,vCurrencyDriver,vImage,vPassword,eStatus FROM register_driver WHERE 1 = 1 AND iDriverId = '.$iMemberId." {$sql}";
        $register_driver = $obj->MySQLSelect($query_1);
        $return_arr['vPassword'] = $register_driver[0]['vPassword'];
        $return_arr['eStatus'] = $register_driver[0]['eStatus'];
        $return_arr['userName'] = $register_driver[0]['vName'].' '.$register_driver[0]['vLastName'];
        $return_arr['userImage'] = '';
        if (isset($register_driver[0]['vImage']) && !empty($register_driver[0]['vImage'])) {
            $return_arr['userImage'] = $tconfig['tsite_upload_images_driver'].'/'.$iMemberId.'/3_'.$register_driver[0]['vImage'];
        } else {
            $return_arr['userImage'] = 'assets/img/profile-user-img.png';
        }

        return $return_arr;
    }

    private function getCompanyDetails($iCompanyId, $phoneNo = '')
    {
        global $obj, $tconfig;
        $sql = '';
        if ('' !== $phoneNo) {
            $sql .= "AND vPhone = '{$phoneNo}'";
        }
        $query_1 = 'SELECT vCompany,vImage,vPassword,eStatus FROM company WHERE 1 = 1 AND iCompanyId = '.$iCompanyId." {$sql}";
        $company = $obj->MySQLSelect($query_1);
        $return_arr['userName'] = $company[0]['vCompany'];
        $return_arr['vPassword'] = $company[0]['vPassword'];
        $return_arr['eStatus'] = $company[0]['eStatus'];
        if (isset($company[0]['vImage']) && !empty($company[0]['vImage'])) {
            $return_arr['userImage'] = $tconfig['tsite_upload_images_compnay'].'/'.$iCompanyId.'/3_'.$company[0]['vImage'];
        } else {
            $return_arr['userImage'] = 'assets/img/profile-user-img.png';
        }

        return $return_arr;
    }

    private function getUserDetails($iMemberId, $phoneNo = '')
    {
        global $obj, $tconfig;
        $sql = '';
        if ('' !== $phoneNo) {
            $sql .= "AND vPhone = '{$phoneNo}'";
        }
        $userData = $obj->MySQLSelect("SELECT CONCAT(vName, ' ', vLastName) as userName, vImgName,vPassword,eStatus FROM register_user WHERE iUserId = '".$iMemberId."' {$sql}");
        $return_arr['userName'] = $userData[0]['userName'];
        $return_arr['vPassword'] = $userData[0]['vPassword'];
        $return_arr['eStatus'] = $userData[0]['eStatus'];
        $return_arr['userImage'] = '';
        if (isset($userData[0]['vImgName']) && !empty($userData[0]['vImgName'])) {
            $return_arr['userImage'] = $tconfig['tsite_upload_images_passenger'].'/'.$iMemberId.'/'.$userData[0]['vImgName'];
        } else {
            $return_arr['userImage'] = 'assets/img/profile-user-img.png';
        }

        return $return_arr;
    }

    private function getTrackingUserDetails($iMemberId, $phoneNo = '')
    {
        global $obj, $tconfig;
        $sql = '';
        if ('' !== $phoneNo) {
            $sql .= "AND vPhone = '{$phoneNo}'";
        }
        $userData = $obj->MySQLSelect("SELECT CONCAT(vName, ' ', vLastName) as userName, vImage,vPassword,eStatus FROM track_service_users WHERE iTrackServiceUserId = '".$iMemberId."' {$sql}");
        $return_arr['userName'] = $userData[0]['userName'];
        $return_arr['vPassword'] = $userData[0]['vPassword'];
        $return_arr['eStatus'] = $userData[0]['eStatus'];
        $return_arr['userImage'] = '';
        if (isset($userData[0]['vImage']) && !empty($userData[0]['vImage'])) {
            $return_arr['userImage'] = $tconfig['tsite_upload_images_track_company_user'].'/'.$iMemberId.'/'.$userData[0]['vImage'];
        } else {
            $return_arr['userImage'] = 'assets/img/profile-user-img.png';
        }

        return $return_arr;
    }
}
