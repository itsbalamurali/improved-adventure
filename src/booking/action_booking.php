<?php



include_once '../common.php';

if ('SHARK' === strtoupper(PACKAGE_TYPE)) {
    include_once '../include/include_webservice_sharkfeatures.php'; // for 22 feature
}
if (!empty($_SESSION['sess_iAdminUserId'])) {
} else {
    $AUTH_OBJ->checkMemberAuthentication();
}

$tbl_name = 'register_user';
$tbl_name1 = 'cab_booking';
$sql = "select vName from currency where eStatus='Active' AND eDefault='Yes'";
$db_currency = $obj->MySQLSelect($sql);
$sql1 = "select vCode from language_master where eStatus='Active' AND eDefault='Yes'";
$db_language = $obj->MySQLSelect($sql1);
$sql = "select cn.vCountry,cn.vPhoneCode from country cn inner join configurations c on c.vValue=cn.vCountryCode where c.vName='DEFAULT_COUNTRY_CODE_WEB'";
$db_con = $obj->MySQLSelect($sql);
// echo "<pre>";print_r($_POST);die;
if (isset($_REQUEST['submitbtn'])) {
    // echo "<pre>";print_r($_POST);die;
    $vName = $_POST['vName'] ?? '';
    $vLastName = $_POST['vLastName'] ?? '';
    $vEmail = $_POST['vEmail'] ?? '';
    $vPassword = $_POST['vPassword'] ?? '';
    $vPhone = $_POST['vPhone'] ?? '';
    $vPhoneCode = $_POST['vPhoneCode'] ?? '';
    $vCountry = $_POST['vCountry'] ?? '';
    $vCity = $_POST['vCity'] ?? '';
    $eStatus = 'Active';
    $vInviteCode = $_POST['vInviteCode'] ?? '';
    $vImgName = $_POST['vImgName'] ?? '';
    $tPackageDetails = $_POST['tPackageDetails'] ?? '';
    $tDeliveryIns = $_POST['tDeliveryIns'] ?? '';
    $tPickUpIns = $_POST['tPickUpIns'] ?? '';
    $vCurrencyPassenger = $_POST['vCurrencyPassenger'] ?? '';
    $vPass = encrypt_bycrypt($vPassword);
    $eType = $_POST['eType'] ?? 'Ride';

    $fTollPrice = $_REQUEST['fTollPrice'] ?? '';
    $vTollPriceCurrencyCode = $_REQUEST['vTollPriceCurrencyCode'] ?? '';
    $eTollSkipped = $_REQUEST['eTollSkipped'] ?? 'Yes';
    $eFemaleDriverRequest = $_REQUEST['eFemaleDriverRequest'] ?? '';
    $eHandiCapAccessibility = $_REQUEST['eHandiCapAccessibility'] ?? '';
    $eChildSeatAvailable = $_REQUEST['eChildSeatAvailable'] ?? '';
    $eBookingFrom = $_POST['eBookingFrom'] ?? 'Admin';

    $pickups = explode(',', $_POST['from_lat_long']); // from latitude-Longitude
    $dropoff = explode(',', $_POST['to_lat_long']); // To latitude-Longitude
    $vSourceLatitude = isset($pickups[0]) ? trim(str_replace('(', '', $pickups[0])) : '';
    $vSourceLongitude = isset($pickups[1]) ? trim(str_replace(')', '', $pickups[1])) : '';

    $vDestLatitude = isset($dropoff[0]) ? trim(str_replace('(', '', $dropoff[0])) : '';
    $vDestLongitude = isset($dropoff[1]) ? trim(str_replace(')', '', $dropoff[1])) : '';

    $iUserId = $_POST['iUserId'] ?? '';
    $iDriverId = $_POST['iDriverId'] ?? '';
    $dBooking_date = $_POST['dBooking_date'] ?? '';
    $vSourceAddresss = $_POST['vSourceAddresss'] ?? '';
    $tDestAddress = $_POST['tDestAddress'] ?? '';
    $eAutoAssign = $_POST['eAutoAssign'] ?? 'No';
    $eStatus1 = ('Yes' === $eAutoAssign) ? 'Pending' : 'Assign';

    $iPackageTypeId = $_POST['iPackageTypeId'] ?? '0';
    $vReceiverName = $_POST['vReceiverName'] ?? '';
    $vReceiverMobile = $_POST['vReceiverMobile'] ?? '';

    $tPackageDetails = $_POST['tPackageDetails'] ?? '';
    $tDeliveryIns = $_POST['tDeliveryIns'] ?? '';
    $tPickUpIns = $_POST['tPickUpIns'] ?? '';
    $iVehicleTypeId = $_POST['iVehicleTypeId'] ?? '';
    $iCabBookingId = $_POST['iCabBookingId'] ?? '';
    $fNightPrice = $_POST['fNightPrice'] ?? '1';
    $fPickUpPrice = $_POST['fPickUpPrice'] ?? '1';
    $vTimeZone = $_POST['vTimeZone'] ?? '';
    $vRideCountry = $_POST['vRideCountry'] ?? '';
    $backlink = $_POST['backlink'] ?? '';
    $previousLink = $_POST['backlink'] ?? '';
    $tUserComment = $_POST['tUserComment'] ?? '';

    $orgDistance = $_POST['distance'];
    $orgDuration = $_POST['duration'];
    $delivery_arr = $_POST['delivery_arr'] ?? '';

    /*$multideliveryEnabled = $MODULES_OBJ->isEnableMultiDeliveryInBooking();
    if($multideliveryEnabled=='Yes') {
        $delivery_arr = isset($_POST['multideliveryJsonDetails']) ? $_POST['multideliveryJsonDetails'] : '';
        if($eType == 'Multi-Delivery') {
            $eTypeinsert = "Multi-Delivery";
        }
    }*/
    $delivery_arr = isset($_POST['multideliveryJsonDetails']) ? stripslashes($_POST['multideliveryJsonDetails']) : '';

    // distance , duration is again divided with 1000 and 60..so put condition on it in fly..

    $vDistance = isset($_POST['distance']) ? setTwoDecimalPoint($_POST['distance'] / 1_000) : '';
    $vDuration = isset($_POST['duration']) ? setTwoDecimalPoint($_POST['duration'] / 60) : '';

    /*  if ($iCabBookingId != "" && $iCabBookingId != '0') {
          // $vDistance = $_POST['distance'];
          // $vDuration = $_POST['duration'];
          $vDistance = isset($_POST['distance']) ? setTwoDecimalPoint($_POST['distance'] / 1000) : '';
          $vDuration = isset($_POST['duration']) ? setTwoDecimalPoint($_POST['duration'] / 60) : '';
      } else {
          // $vDistance = isset($_POST['distance']) ? setTwoDecimalPoint($_POST['distance'] / 1000) : '';
          // $vDuration = isset($_POST['duration']) ? setTwoDecimalPoint($_POST['duration'] / 60) : '';
          $vDistance = isset($_POST['distance']) ? setTwoDecimalPoint($_POST['distance']) : '';
          $vDuration = isset($_POST['duration']) ? setTwoDecimalPoint($_POST['duration']) : '';
      }*/

    $fWalletMinBalance = $WALLET_MIN_BALANCE;
    $fWalletBalance = 0;
    // Added By HJ On 14-12-2019 For Get User Detail When Site Type Is Demo B'coz In Demo Type Data Post in masking Start
    if (SITE_TYPE === 'Demo' && $iUserId > 0) {
        $getUserData = $obj->MySQLSelect('SELECT vPhone,vName,vLastName,vEmail,iUserId,tSessionId FROM '.$tbl_name." WHERE iUserId='".$iUserId."'");
        if (count($getUserData) > 0) {
            $vPhone = $getUserData[0]['vPhone'];
            $vName = $getUserData[0]['vName'];
            $vLastName = $getUserData[0]['vLastName'];
            $vEmail = $getUserData[0]['vEmail'];
        }
    }
    // Added By HJ On 14-12-2019 For Get User Detail When Site Type Is Demo B'coz In Demo Type Data Post in masking End
    if ('' !== $iDriverId) {
        $user_available_balance = $WALLET_OBJ->FetchMemberWalletBalance($iDriverId, 'Driver');
        $fWalletBalance = $user_available_balance;

        $countryTimeZone = $obj->MySQLSelect("SELECT vTimeZone FROM register_driver WHERE iDriverId='".$iDriverId."'");
        if (count($countryTimeZone) > 0) {
            $vTimeZone = $countryTimeZone[0]['vTimeZone'];
        }
    }

    $eFlatTrip = $_POST['eFlatTrip'] ?? 'No';
    $fFlatTripPrice = $_POST['fFlatTripPrice'] ?? 0;

    $vCouponCode = $_POST['vCouponCode'] ?? '';
    $vTripPaymentMode = $_POST['vTripPaymentMode'] ?? '';

    $iVehicleTypeIdNew = $_POST['iVehicleTypeId'] ?? '';

    $iCompanyId = $_POST['iCompanyId'] ?? '';
    $eRideType = $_POST['eRideType'] ?? '';

    $iFromStationId = $_POST['iFromStationId'] ?? '';
    $iToStationId = $_POST['iToStationId'] ?? '';

    if (!empty($iFromStationId) && !empty($iToStationId)) {
        $dbFromStation = $obj->MySQLSelect("SELECT * FROM location_master WHERE iLocationId = {$iFromStationId}");
        $dbToStation = $obj->MySQLSelect("SELECT * FROM location_master WHERE iLocationId = {$iToStationId}");
        $vSourceAddresss = addslashes($dbFromStation[0]['vLocationName']).' - '.addslashes($dbFromStation[0]['vLocationAddress']);
        $tDestAddress = addslashes($dbToStation[0]['vLocationName']).' - '.addslashes($dbToStation[0]['vLocationAddress']);
    }

    $iAdminId = $_POST['iAdminId'] ?? '';
    $vRiderRoomNubmer = $_POST['vRiderRoomNubmer'] ?? '';
    $iHotelBookingId = $_POST['iHotelBookingId'] ?? '';

    $iHotelId = 0;
    $isFromHotelPanel = 'No';
    if ('hotel' === $_SESSION['SessionUserType']) {
        $isFromHotelPanel = 'Yes';
        if (!empty($iHotelBookingId)) {
            $sql1 = 'select iHotelId from hotel where iAdminId='.$iHotelBookingId." AND eStatus='Active'";
            $db_hotel = $obj->MySQLSelect($sql1);
            if (isset($db_hotel) && !empty($db_hotel)) {
                $iHotelId = $db_hotel[0]['iHotelId'];
            }
        }
    }
    if ('Fly' === $eType) {
        $eFly = 'Yes';
    } else {
        $eFly = 'No';
    }
    if ('Moto' === $eType || 'Fly' === $eType) {
        $eType = 'Ride';
    }

    if ('Multi-Delivery' === $eType) {
        $eType = 'Deliver';
    }

    if ('Ride' === $eType) {
        $iVehicleTypeId = $_POST['iDriverVehicleId_ride'] ?? '';
        // $eRideType = isset($_POST['eRideType']) ? $_POST['eRideType'] : '';
    }
    if ('Deliver' === $eType) {
        $iVehicleTypeId = $_POST['iDriverVehicleId_delivery'] ?? '';
        $eDeliveryType = $_POST['eDeliveryType'] ?? '';
    }
    if ('' === $iVehicleTypeId) {
        $iVehicleTypeId = $iVehicleTypeIdNew;
    }
    $eTypeinsert = $eType;
    if ('Deliver' === $eType && 'Yes' === $ENABLE_MULTI_VIEW_IN_SINGLE_DELIVERY) {
        $eTypeinsert = 'Multi-Delivery';
        $total_del_dist = round($_POST['distance'] / 1_000, 2);
        $total_del_time = round($_POST['duration'] / 60, 2);
    }

    $systemTimeZone = date_default_timezone_get();
    // $SQL1 = "SELECT vValue FROM configurations WHERE vName = 'COMMISION_DEDUCT_ENABLE'";
    // $config_data = $obj->MySQLSelect($SQL1);
    // $eCommisionDeductEnable = $config_data[0]['vValue'];
    $eCommisionDeductEnable = $MODULES_OBJ->autoDeductDriverCommision('Ride'); // Added By HJ On 16-10-2020 For get Auto Deduct Driver Commision Configuration As Per eSystem

    if ('UberX' === $eType) {
        include_once '../include/uberx/include_webservice_uberx.php';
    }

    $ePhoneVerified = ",`ePhoneVerified` = 'Yes'";
    if ('Yes' === $RIDER_PHONE_VERIFICATION) {
        $ePhoneVerified = '';
    }

    if (!empty($vEmail)) {
        $SQL1 = "SELECT vName,vLastName,vEmail,iUserId,tSessionId FROM {$tbl_name} WHERE vEmail = '{$vEmail}'";
        $email_exist = $obj->MySQLSelect($SQL1);
        $iUserId = $email_exist[0]['iUserId'];

        $SQL3 = "UPDATE {$tbl_name} SET `eEmailVerified` = 'Yes' {$ePhoneVerified} WHERE vEmail = '{$vEmail}'";
        $obj->sql_query($SQL3);

        if ('' === $email_exist[0]['tSessionId']) {
            $SQL1 = "UPDATE {$tbl_name} SET `tSessionId` = '".session_id().time()."' WHERE vEmail = '{$vEmail}'";
            $obj->sql_query($SQL1);

            $SQL2 = "SELECT vName,vLastName,vEmail,iUserId,tSessionId FROM {$tbl_name} WHERE vEmail = '{$vEmail}'";
            $email_exist = $obj->MySQLSelect($SQL2);
        }
    } else {
        $SQL1 = "SELECT vName,vLastName,vEmail,iUserId,tSessionId FROM {$tbl_name} WHERE vPhone = '".$vPhone."' AND vPhoneCode = '".$vPhoneCode."'";
        $email_exist = $obj->MySQLSelect($SQL1);
        $iUserId = $email_exist[0]['iUserId'];

        $SQL3 = "UPDATE {$tbl_name} SET `eEmailVerified` = 'Yes' {$ePhoneVerified}  WHERE vPhone = '".$vPhone."' AND vPhoneCode = '".$vPhoneCode."'";
        $obj->sql_query($SQL3);

        if ('' === $email_exist[0]['tSessionId']) {
            $SQL1 = "UPDATE {$tbl_name} SET `tSessionId` = '".session_id().time()."'  WHERE vPhone = '".$vPhone."' AND vPhoneCode = '".$vPhoneCode."'";
            $obj->sql_query($SQL1);

            $SQL2 = "SELECT vName,vLastName,vEmail,iUserId,tSessionId FROM {$tbl_name}  WHERE vPhone = '".$vPhone."' AND vPhoneCode = '".$vPhoneCode."'";
            $email_exist = $obj->MySQLSelect($SQL2);
        }
    }
    // Added By HJ On 28-08-2019 For Get Country Timezone If Not Found Start
    if ('' === trim($vTimeZone)) {
        $countryTimeZone = $obj->MySQLSelect("SELECT vTimeZone FROM country WHERE vPhoneCode='".$vPhoneCode."'");
        if (count($countryTimeZone) > 0) {
            $vTimeZone = $countryTimeZone[0]['vTimeZone'];
        }
    }
    // Added By HJ On 28-08-2019 For Get Country Timezone If Not Found End

    // if ($iCabBookingId != "" && $iCabBookingId != '0') {
    //    $SQLti1 = "SELECT vTimeZone,eBookingFrom,dBooking_date FROM cab_booking WHERE iCabBookingId = '$iCabBookingId'";
    //    $time_data = $obj->MySQLSelect($SQLti1);
    //    $eBookingFrom = $time_data[0]['eBookingFrom'];
    //    if ($eBookingFrom != "Admin") {
    //        $vTimeZone = $time_data[0]['vTimeZone'];
    //    }
    //    $dBooking_date = $time_data[0]['dBooking_date'];
    //    $dBooking_date = converToTz($dBooking_date, $vTimeZone, $systemTimeZone);
    // }

    if (0 === count($email_exist) && '' === $iCabBookingId) { //  && !empty($vEmail)
        $eReftype = 'Rider';
        $vRefCode = $REFERRAL_OBJ->GenerateReferralCode($eReftype);
        $vRefCodePara = "`vRefCode` = '".$vRefCode."',";
        $vPassword = encrypt_bycrypt('123456');
        $q = 'INSERT INTO ';
        $where = '';
        $query = $q.' `'.$tbl_name."` SET
			`vName` = '".$vName."',
			`vLastName` = '".$vLastName."',
			`vEmail` = '".$vEmail."',
			`vPassword` = '".$vPassword."',
			`vPhone` = '".$vPhone."',
			`vCountry` = '".$vCountry."',
			`vPhoneCode` = '".$vPhoneCode."',
            {$vRefCodePara}
			`eStatus` = '".$eStatus."',
			`vImgName` = '".$vImgName."',
			`vCurrencyPassenger` = '".$db_currency[0]['vName']."',
			`vLang` = '".$db_language[0]['vCode']."',
			`tRegistrationDate` = '".date('Y-m-d H:i:s')."',
			`tSessionId` = '".session_id().time()."',
			`eEmailVerified` = 'Yes',
			`ePhoneVerified` = 'Yes',
			`vInviteCode` = '".$vInviteCode."'";
        $obj->sql_query($query);
        $iUserId = $obj->GetInsertId();
        if ('' !== $iUserId) {
            $maildata['EMAIL'] = $vEmail;
            $maildata['NAME'] = $vName.' '.$vLastName;
            $maildata['PASSWORD'] = '123456';
            $COMM_MEDIA_OBJ->SendMailToMember('MEMBER_REGISTRATION_USER_FOR_MANUAL_BOOKING', $maildata);
        }
        if ('Yes' === $ENABLE_EMAIL_OPTIONAL) {
            $SQL1 = "SELECT vName,vLastName,vEmail,iUserId,tSessionId FROM {$tbl_name} WHERE vPhone = '".$vPhone."' AND vPhoneCode = '".$vPhoneCode."'";
        } else {
            $SQL1 = "SELECT vName,vLastName,vEmail,iUserId,tSessionId FROM {$tbl_name} WHERE vEmail = '{$vEmail}'";
        }
        $email_exist = $obj->MySQLSelect($SQL1);
    } else {
        $SQL1 = "UPDATE {$tbl_name} SET eStatus='".$eStatus."' WHERE vEmail = '".$vEmail."'";
        $obj->sql_query($SQL1);
    }

    if (('' === $iUserId || '0' === $iUserId || '' === $vSourceAddresss || '' === $tDestAddress) && 'UberX' !== $eType && 'Multi-Delivery' !== $eTypeinsert) {
        $var_msg = 'Booking details is not add/update because missing information';
        echo 'aaaa';

        exit;
        if ('' === $iCabBookingId) {
            header('location:add_booking.php?success=0&var_msg='.$var_msg);

            exit;
        }
        header('location:add_booking.php?booking_id='.$iCabBookingId.'success=0&var_msg='.$var_msg);

        exit;
    }
    if (('' === $iUserId || '0' === $iUserId || '' === $vSourceAddresss) && 'UberX' === $eType) {
        $var_msg = 'Booking details is not add/update because missing information';
        echo 'bbbb';

        exit;
        if ('' === $iCabBookingId) {
            header('location:add_booking.php?success=0&var_msg='.$var_msg);

            exit;
        }
        header('location:add_booking.php?booking_id='.$iCabBookingId.'success=0&var_msg='.$var_msg);

        exit;
    }

    if (('Ride' === $eType && 'later' === $eRideType) || ('Deliver' === $eType && 'later' === $eDeliveryType) || ('UberX' === $eType)) {
        $rand_num = random_int(10_000_000, 99_999_999);
        // $systemTimeZone = date_default_timezone_get();
        $dBookingDate = converToTz($dBooking_date, $systemTimeZone, $vTimeZone);
        $dBookingDate_new = date('Y-m-d H:i', strtotime($dBookingDate));
        // $dBookingDate_new_mail = date('Y-m-d H:i A', strtotime($dBookingDate)); //added by SP for date format in mail from issue#332 on 03-10-2019
        $dBookingDate_new_mail = date('jS F, Y', strtotime($dBooking_date));
        $dBookingDate_new_mail_time = date('h:i A', strtotime($dBooking_date));
        $dBookingDate_new_mail_date = $dBookingDate_new_mail;
        $dBookingDate_new_mail = $dBookingDate_new_mail.' '.$langage_lbl_admin['LBL_AT_TXT'].' '.$dBookingDate_new_mail_time;

        $q1 = 'INSERT INTO ';
        $whr = ",`vBookingNo`='".$rand_num."'";
        $edit = '';
        if ('' !== $iCabBookingId && '0' !== $iCabBookingId) {
            $q1 = 'UPDATE ';
            $whr = " WHERE `iCabBookingId` = '".$iCabBookingId."'";
            $edit = '1';
        }
        /*if ($APP_TYPE == 'UberX' && !empty($iDriverId)) {
            $eStatus1 = "Accepted";
        }*/
        if ('No' === $eTollSkipped || '' !== $fTollPrice) {
            $fTollPrice_Original = $fTollPrice;
            $vTollPriceCurrencyCode = strtoupper($vTollPriceCurrencyCode);
            $default_currency = $db_currency[0]['vName'];
            $sql = " SELECT round(({$fTollPrice}/(SELECT Ratio FROM currency where vName='".$vTollPriceCurrencyCode."'))*(SELECT Ratio FROM currency where vName='".$default_currency."' ) ,2)  as price FROM currency  limit 1";
            $result_toll = $obj->MySQLSelect($sql);
            $fTollPrice = $result_toll[0]['price'];
            if (0 === $fTollPrice) {
                $fTollPrice = FetchTollPrice($vTollPriceCurrencyCode, $default_currency, $fTollPrice_Original);
            }
        } else {
            $fTollPrice = '0';
            $vTollPriceCurrencyCode = '';
            $eTollSkipped = 'No';
        }
        $tVehicleTypeData = $tVehicleTypeFareData = '';
        if ('' === $eFlatTrip || empty($eFlatTrip)) {
            $eFlatTrip = 'No';
        }

        // ///////////////////////////////////////////////
        $TripData = $fareDetails = [];
        if ('Provider' === $SERVICE_PROVIDER_FLOW && 'UberX' === $eType) {
            $fareDetails = getVehicleTypeFareDetails();
            // $typeDataArr = json_decode($orderDetails);
        }
        $selectedCarTypeID = $iVehicleTypeId;
        $ALLOW_SERVICE_PROVIDER_AMOUNT = 'No';
        $vVehicleTypeName = $eFareType = '';
        $fAmount = 0;
        $sql_vehicle_category_table_name = getVehicleCategoryTblName();
        if ('UberX' === $eType) {
            $eFareType = '';
            if (isset($fareDetails['originalFareTotal']) && !empty($fareDetails['originalFareTotal'])) {
                $eFareType = $fareDetails['eFareTypeServices'];
            } elseif ($selectedCarTypeID > 0) {
                $sqlv = 'SELECT vt.iVehicleCategoryId,vt.vVehicleType_'.$db_language[0]['vCode'].' as vVehicleTypeName,vc.iParentId,vt.eFareType,vt.ePickStatus,vt.eNightStatus,vt.fFixedFare, vt.iBaseFare, vt.iMinFare, vt.fMinHour, vc.ePriceType From vehicle_type as vt LEFT JOIN '.$sql_vehicle_category_table_name." as vc ON  vc.iVehicleCategoryId = vt.iVehicleCategoryId WHERE vt.iVehicleTypeId = '".$selectedCarTypeID."'";
                $tripVehicleData = $obj->MySQLSelect($sqlv);
                $eFareType = $tripVehicleData[0]['eFareType'];
            }
        }
        $fPickUpPrice = $fNightPrice = 1;
        $sourceLocationArr = [$vSourceLatitude, $vSourceLongitude];
        $destinationLocationArr = [$vDestLatitude, $vDestLongitude];

        $data_flattrip['eFlatTrip'] = 'No';
        $data_flattrip['Flatfare'] = 0;

        if ('Ride' === $eType && 'STANDARD' !== strtoupper(PACKAGE_TYPE)) {
            $data_flattrip = checkFlatTripnew($sourceLocationArr, $destinationLocationArr, $selectedCarTypeID, 0);
        }

        if ('UberX' !== $eType || ('UberX' === $eType && !empty($eFareType) && 'Regular' === $eFareType)) {
            $data_surgePrice = checkSurgePrice($selectedCarTypeID, $dBooking_date, 0);
            if ('0' === $data_surgePrice['Action']) {
                if ('LBL_PICK_SURGE_NOTE' === $data_surgePrice['message']) {
                    $fPickUpPrice = $data_surgePrice['SurgePriceValue'];
                } else {
                    $fNightPrice = $data_surgePrice['SurgePriceValue'];
                }
            }
        }
        if ('No' === $APPLY_SURGE_ON_FLAT_FARE && 'Yes' === $data_flattrip['eFlatTrip']) {
            $fPickUpPrice = $fNightPrice = 1;
        }
        $eFlatTrip = $data_flattrip['eFlatTrip'];
        $fFlatTripPrice = $data_flattrip['Flatfare'];

        $fpickupsurchargefare = $fdropoffsurchargefare = 0;
        if ('SHARK' === strtoupper(PACKAGE_TYPE)) {
            if ('Yes' === $ENABLE_AIRPORT_SURCHARGE_SECTION && 'UberX' !== $eType) {
                $GetVehicleIdfromGeoLocation = CheckSurgeAirportFromGeoLocation($sourceLocationArr, $destinationLocationArr, $iVehicleTypeId);
                $fpickupsurchargefare = $GetVehicleIdfromGeoLocation['fpickupsurchargefare'];
                $fdropoffsurchargefare = $GetVehicleIdfromGeoLocation['fdropoffsurchargefare'];
                // $airportsurgetype = $AIRPORT_SURGE_ADD_OR_OVERRIDE;
            }
        }
        // /////////////////////////////////////////////
        if ('UberX' === $eType) {
            $tVehicleTypeData = '[{"iVehicleTypeId":"'.$iVehicleTypeId.'","fVehicleTypeQty":"1","tUserComment":"'.$tUserComment.'"}]';

            // get detail tripFareDetailsSaveArr data
            $getVehicleTypeFareDetailsArr = getVehicleTypeFareDetails($tVehicleTypeData, $iUserId);

            // variable set tripFareDetailsSaveArr and data encode
            $tVehicleTypeFareData = $getVehicleTypeFareDetailsArr['tripFareDetailsSaveArr'];

            $tVehicleTypeFareData = json_encode($tVehicleTypeFareData);

            $query1 = $q1.' `'.$tbl_name1."` SET
                `iUserId` = '".$iUserId."',
                `iCompanyId` = '".$iCompanyId."',
                `iDriverId` = '".$iDriverId."',
                `vSourceLatitude` = '".$vSourceLatitude."',
                `vSourceLongitude` = '".$vSourceLongitude."',
                `vDestLatitude` = '".$vDestLatitude."',
                `vDestLongitude` = '".$vDestLongitude."',
                `vDistance` = '".$vDistance."',
                `vDuration` = '".$vDuration."',
                `tTotalDistance` = '".$vDistance."',
                `tTotalDuration` = '".$vDuration."',
                `dBooking_date` = '".$dBookingDate_new."',
                `vSourceAddresss` = '".$vSourceAddresss."',
                `tPackageDetails` = '".$tPackageDetails."',
                `iPackageTypeId` = '".$iPackageTypeId."',
                `tDeliveryIns` = '".$tDeliveryIns."',
                `tPickUpIns` = '".$tPickUpIns."',
                `vReceiverName` = '".$vReceiverName."',
                `vReceiverMobile` = '".$vReceiverMobile."',
                `tDestAddress` = '".$tDestAddress."',
                `eType` = '".$eType."',
                `eStatus`='".$eStatus1."',
                `eAutoAssign`='".$eAutoAssign."',
                `fPickUpPrice`='".$fPickUpPrice."',
                `fNightPrice`='".$fNightPrice."',
                `eCancelBy`='',
                `fWalletMinBalance`='".$fWalletMinBalance."',
                `fWalletBalance`='".$fWalletBalance."',
                `vRideCountry`='".$vRideCountry."',
                `vTimeZone`='".$vTimeZone."',
                `fTollPrice`='".$fTollPrice."',
                `vTollPriceCurrencyCode` = '".$vTollPriceCurrencyCode."',
                `eTollSkipped` = '".$eTollSkipped."',
                `eCommisionDeductEnable`='".$eCommisionDeductEnable."',
                `eFlatTrip`='".$eFlatTrip."',
                `fFlatTripPrice` = '".$fFlatTripPrice."',
                `eFemaleDriverRequest`= '".$eFemaleDriverRequest."',
                `eHandiCapAccessibility`= '".$eHandiCapAccessibility."',
                `eBookingFrom`= '".$eBookingFrom."',
                    `vCouponCode`= '".$vCouponCode."',
                `tVehicleTypeData`= '".$tVehicleTypeData."',
                `tVehicleTypeFareData`= '".$tVehicleTypeFareData."',
                `vRiderRoomNubmer`= '".$vRiderRoomNubmer."',
				`iAdminId`= '".$iAdminId."',
				`iHotelBookingId`= '".$iHotelBookingId."',
                `iVehicleTypeId` = '".$iVehicleTypeId."'".$whr;
        } else {
            $query1 = $q1.' `'.$tbl_name1."` SET
                `iUserId` = '".$iUserId."',
                `iCompanyId` = '".$iCompanyId."',
                `iDriverId` = '".$iDriverId."',
                `vSourceLatitude` = '".$vSourceLatitude."',
                `vSourceLongitude` = '".$vSourceLongitude."',
                `vDestLatitude` = '".$vDestLatitude."',
                `vDestLongitude` = '".$vDestLongitude."',
                `vDistance` = '".$vDistance."',
                `vDuration` = '".$vDuration."',
                `tTotalDistance` = '".$vDistance."',
                `tTotalDuration` = '".$vDuration."',
                `dBooking_date` = '".$dBookingDate_new."',
                `vSourceAddresss` = '".$vSourceAddresss."',
                `tPackageDetails` = '".$tPackageDetails."',
                `iPackageTypeId` = '".$iPackageTypeId."',
                `tDeliveryIns` = '".$tDeliveryIns."',
                `tPickUpIns` = '".$tPickUpIns."',
                `vReceiverName` = '".$vReceiverName."',
                `vReceiverMobile` = '".$vReceiverMobile."',
                `tDestAddress` = '".$tDestAddress."',
                `eType` = '".$eTypeinsert."',
                `eStatus`='".$eStatus1."',
                `eAutoAssign`='".$eAutoAssign."',
                `fPickUpPrice`='".$fPickUpPrice."',
                `fNightPrice`='".$fNightPrice."',
                `eCancelBy`='',
                `fWalletMinBalance`='".$fWalletMinBalance."',
                `fWalletBalance`='".$fWalletBalance."',
                `vRideCountry`='".$vRideCountry."',
                `vTimeZone`='".$vTimeZone."',
                `fTollPrice`='".$fTollPrice."',
                `vTollPriceCurrencyCode` = '".$vTollPriceCurrencyCode."',
                `eTollSkipped` = '".$eTollSkipped."',
                `eCommisionDeductEnable`='".$eCommisionDeductEnable."',
                `eFlatTrip`='".$eFlatTrip."',
                `fFlatTripPrice` = '".$fFlatTripPrice."',
                `fAirportPickupSurge` = '".$fpickupsurchargefare."',
                `fAirportDropoffSurge` = '".$fdropoffsurchargefare."',
                `eFemaleDriverRequest`= '".$eFemaleDriverRequest."',
                `eHandiCapAccessibility`= '".$eHandiCapAccessibility."',
                `eBookingFrom`= '".$eBookingFrom."',
                `iFromStationId` = '".$iFromStationId."',
                `iToStationId` = '".$iToStationId."',
                `vCouponCode`= '".$vCouponCode."',
                `vRiderRoomNubmer`= '".$vRiderRoomNubmer."',
				`iAdminId`= '".$iAdminId."',
                `tDeliveryData` = '".$delivery_arr."',
				`iHotelBookingId`= '".$iHotelBookingId."',
                `iVehicleTypeId` = '".$iVehicleTypeId."'".$whr;
        }
        // `ePayType` = '".$vTripPaymentMode."',
        $obj->sql_query($query1);
        $insert_id = ('' !== $iCabBookingId) ? $iCabBookingId : $obj->GetInsertId();

        if ('Multi-Delivery' === $eTypeinsert || 'Multi-Delivery' === $eType) {
            include '../include/delivery/include_webservice_delivery.php';

            include_once '../include/include_webservice_sharkfeatures.php';
            $eWalletDebitAllow = 'No';
            $vDistance1 = isset($_POST['distance']) ? (round(number_format($_POST['distance'] / 1_000))) : '';
            $vDuration1 = isset($_POST['duration']) ? (round(number_format($_POST['duration'] / 60))) : '';
            $data_fare = calculateFareEstimateAllMultiDelivery($vDuration1, $vDistance1, $iVehicleTypeId, $iUserId, 1, '', '', $vCouponCode, 1, 0, 0, 0, 'DisplySingleVehicleFare', 'Passenger', 1, '', '', $eFlatTrip, $fFlatTripPrice, '', '', 'Yes', $eType, $dBookingDate, 'Sender', $eWalletDebitAllow);
            if (!empty($Data['fTollPrice']) && $Data['fTollPrice'] > 0 && !empty($Data['eTollSkipped']) && 'NO' === strtoupper($Data['eTollSkipped'])) {
                $data_fare[0]['TotalGenratedFare'] = $data_fare[0]['TotalGenratedFare'] + $Data['fTollPrice'] + $userOutStandingAmt; // $userOutStandingAmt Added By HJ On 10-06-2019 For Payment Flow 2/3
            }
            // echo "<pre>";print_r($data_fare);exit;
            $fTripGenerateFare = $data_fare[0]['TotalGenratedFare'];
            $Data['iFare'] = $data_fare[0]['iFare_Ori'];
            $Data['iBaseFare'] = $data_fare[0]['iBaseFare_AMT'];
            $Data['fPricePerMin'] = $data_fare[0]['FareOfMinutes_Ori'];
            $Data['fPricePerKM'] = $data_fare[0]['FareOfDistance_Ori'];
            $Data['fCommision'] = $data_fare[0]['fCommision_AMT'];
            $Data['fSurgePriceDiff'] = $data_fare[0]['fSurgePriceDiff_Ori'];
            $Data['fTax1'] = $data_fare[0]['fTax1_Ori'];
            $Data['fTax2'] = $data_fare[0]['fTax2_Ori'];
            $Data['fDiscount'] = $data_fare[0]['fDiscount_Ori'];
            $Data['vDiscount'] = $data_fare[0]['vDiscount'];
            $Data['fMinFareDiff'] = $data_fare[0]['fMinFareDiff_Ori'];
            $Data['fTripGenerateFare'] = $fTripGenerateFare;
            $Data['vDistance'] = $vDistance;
            $Data['vDuration'] = $vDuration;
            // added by SP for rounding off on 7-11-2019 for multi delivery
            $Data['fRoundingAmount'] = $data_fare[0]['fRoundingAmount'];
            $Data['eRoundingType'] = $data_fare[0]['eRoundingType'];
            $where = " iCabBookingId = '".$insert_id."'";
            $id = $obj->MySQLQueryPerform('cab_booking', $Data, 'update', $where);
        }
        // ------------ Added By HJ On 28-09-2020 For Ride Later Booking Of Multi Delivery Type ------------
        // if($iCabBookingId == ''){
        if ('' !== $delivery_arr && ('Multi-Delivery' === $eType || 'Deliver' === $eType)) {
            // $insert_id = ($iCabBookingId != '') ? $iCabBookingId : $obj->GetInsertId();
            $details_arr = json_decode($delivery_arr, true);
            $j = 0;
            $last_key = end(array_keys($details_arr));
            $dsql = 'SELECT * FROM `trips_delivery_locations` WHERE iCabBookingId ="'.$iCabBookingId.'"';
            $tripdeliverylocationdata = $obj->MySQLSelect($dsql);
            foreach ($tripdeliverylocationdata as $key => $value) {
                $dfsql = 'DELETE FROM `trip_delivery_fields` WHERE iTripDeliveryLocationId ="'.$value['iTripDeliveryLocationId'].'"';
                $dId = $obj->MySQLSelect($dfsql);
            }
            $delsql = 'DELETE FROM `trips_delivery_locations` WHERE iCabBookingId ="'.$iCabBookingId.'"';
            $delId = $obj->MySQLSelect($delsql);
            foreach ($details_arr as $key123 => $values1) {
                $i = 0;
                $insert_did = [];
                foreach ($values1 as $key => $value) {
                    /*echo "==>".$key."<br>";
                    print_r($value);*/
                    // $Data_trip_locations = array();
                    if ('vReceiverAddress' === $key || 'vReceiverLatitude' === $key || 'vReceiverLongitude' === $key || 'ePaymentByReceiver' === $key) {
                        $Data_trip_locations[$key] = $value;
                        if ('vReceiverLatitude' === $key) {
                            $Old_end_lat = $Data_trip_locations['tEndLat'];
                            $Data_trip_locations['tEndLat'] = $value;
                        } elseif ('vReceiverLongitude' === $key) {
                            $Old_end_long = $Data_trip_locations['tEndLong'];
                            $Data_trip_locations['tEndLong'] = $value;
                        } elseif ('vReceiverAddress' === $key) {
                            $Old_end_address = $Data_trip_locations['tDaddress'];
                            $Data_trip_locations['tDaddress'] = $value;
                        } elseif ('ePaymentByReceiver' === $key) {
                            $Data_trip_locations['ePaymentByReceiver'] = $value;
                        }
                        if (('Sender' === $ePaymentBy || 'Receiver' === $ePaymentBy) && 0 !== $key123) {
                            $Data_trip_locations['tStartLat'] = $Old_end_lat;
                            $Data_trip_locations['tStartLong'] = $Old_end_long;
                            $Data_trip_locations['tSaddress'] = $Old_end_address;
                        } else {
                            $Data_trip_locations['tStartLat'] = $vSourceLatitude;
                            $Data_trip_locations['tStartLong'] = $vSourceLongitude;
                            $Data_trip_locations['tSaddress'] = $vSourceAddresss;
                        }
                    } else {
                        if ('distance' !== $key) {
                            $deliveryDataArr[$key] = $value;
                            $Data_delivery['iDeliveryFieldId'] = $key;
                            $Data_delivery['iCabBookingId'] = $insert_id;
                            $Data_delivery['vValue'] = $value;
                            $insert_did[] = $obj->MySQLQueryPerform('trip_delivery_fields', $Data_delivery, 'insert');
                        }
                    }
                }
                $Data_trip_locations['iCabBookingId'] = $insert_id;
                $Data_trip_locations['ePaymentBy'] = $ePaymentBy;
                $Data_trip_locations['eRequestType'] = 'Ridelater';
                $insert_dfid = $obj->MySQLQueryPerform('trips_delivery_locations', $Data_trip_locations, 'insert');
                $delivery_ids = implode("','", $insert_did);
                $where = " iTripDeliveryFieldId in ('".$delivery_ids."')";
                $data_update['iTripDeliveryLocationId'] = $insert_dfid;
                $obj->MySQLQueryPerform('trip_delivery_fields', $data_update, 'update', $where);
                if ($last_key === $key123) {
                    $data_update_cab = [];
                    $where = " iCabBookingId='".$insert_id."'";
                    if (isset($_REQUEST['test'])) {
                        // echo "<pre>";print_r($deliveryDataArr);die;
                    }
                    if (isset($deliveryDataArr[1])) {
                        $data_update_cab['iPackageTypeId'] = $deliveryDataArr[1];
                    }
                    if (isset($deliveryDataArr[2])) {
                        $data_update_cab['vReceiverName'] = $deliveryDataArr[2];
                    }
                    if (isset($deliveryDataArr[3])) {
                        $data_update_cab['vReceiverMobile'] = $deliveryDataArr[3];
                    }
                    if (isset($deliveryDataArr[4])) {
                        $data_update_cab['tDeliveryIns'] = $deliveryDataArr[4];
                    }
                    if (isset($deliveryDataArr[5])) {
                        $data_update_cab['tPackageDetails'] = $deliveryDataArr[5];
                    }
                    $data_update_cab['vDestLatitude'] = $Data_trip_locations['tEndLat'];
                    $data_update_cab['vDestLongitude'] = $Data_trip_locations['tEndLong'];
                    $data_update_cab['tDestAddress'] = $Data_trip_locations['tDaddress'];
                    if (isset($_REQUEST['test'])) {
                        // echo "<pre>";print_r($data_update_cab);die;
                    }
                    $obj->MySQLQueryPerform('cab_booking', $data_update_cab, 'update', $where);
                }
            }
        }
        // }
        // ------------ Added By HJ On 28-09-2020 For Ride Later Booking Of Multi Delivery Type ------------

        $sql = 'select vName,vLastName,vEmail,iDriverVehicleId,vPhone,vcode,vLang from register_driver where iDriverId='.$iDriverId;
        $driver_db = $obj->MySQLSelect($sql);

        $Data1['vRider'] = $email_exist[0]['vName'].' '.$email_exist[0]['vLastName'];
        $Data1['vDriver'] = $driver_db[0]['vName'].' '.$driver_db[0]['vLastName'];
        $Data1['vDriverMail'] = $driver_db[0]['vEmail'];
        $Data1['vRiderMail'] = $email_exist[0]['vEmail'];
        $Data1['vSourceAddresss'] = $vSourceAddresss;
        $Data1['tDestAddress'] = $tDestAddress;
        $Data1['dBookingdate'] = $dBookingDate_new_mail;
        $Data1['vBookingNo'] = $rand_num;

        if ('1' === $edit) {
            $sql = "select vBookingNo from cab_booking where `iCabBookingId` = '".$iCabBookingId."'";
            $cab_id = $obj->MySQLSelect($sql);
            $Data1['vBookingNo'] = $cab_id[0]['vBookingNo'];
        }
        // $Data1['vDistance']=$vDistance;
        // $Data1['vDuration']=$vDuration;
        // $return = $COMM_MEDIA_OBJ->SendMailToMember("MANUAL_TAXI_DISPATCH_DRIVER", $Data1);
        // $return1 = $COMM_MEDIA_OBJ->SendMailToMember("MANUAL_TAXI_DISPATCH_RIDER", $Data1);

        if ('UberX' === $eType) {
            $return = $COMM_MEDIA_OBJ->SendMailToMember('MANUAL_TAXI_DISPATCH_DRIVER_APP_SP', $Data1);
        } else {
            $return = $COMM_MEDIA_OBJ->SendMailToMember('MANUAL_TAXI_DISPATCH_DRIVER', $Data1);
        }

        if ('Yes' === $eAutoAssign) {
            if ('UberX' === $eType) {
                $return1 = $COMM_MEDIA_OBJ->SendMailToMember('MANUAL_TAXI_DISPATCH_RIDER_AUTOASSIGN_SP', $Data1);
            } else {
                $return1 = $COMM_MEDIA_OBJ->SendMailToMember('MANUAL_TAXI_DISPATCH_RIDER_AUTOASSIGN', $Data1);
            }
        } else {
            if ('UberX' === $eType) {
                $return1 = $COMM_MEDIA_OBJ->SendMailToMember('MANUAL_TAXI_DISPATCH_RIDER_SP', $Data1);
            } else {
                $return1 = $COMM_MEDIA_OBJ->SendMailToMember('MANUAL_TAXI_DISPATCH_RIDER', $Data1);
            }
        }
        // echo "OUT";exit;
        // Start Send SMS
        $query = 'SELECT vLicencePlate FROM driver_vehicle WHERE iDriverVehicleId='.$driver_db[0]['iDriverVehicleId'];
        $db_driver_vehicles = $obj->MySQLSelect($query);

        $vPhone = $vPhone;
        $vcode = $db_con[0]['vPhoneCode'];
        $Booking_Date = @date('d-m-Y', strtotime($dBookingDate));
        $Booking_Time = @date('H:i:s', strtotime($dBookingDate));

        $query = 'SELECT vPhoneCode,vLang FROM register_user WHERE iUserId='.$iUserId;
        $db_user = $obj->MySQLSelect($query);

        $maillanguage = $db_user[0]['vLang'];

        $Pass_name = $vName.' '.$vLastName;
        $vcode = $db_user[0]['vPhoneCode'];
        $maildata['DRIVER_NAME'] = $Data1['vDriver'];
        $maildata['PLATE_NUMBER'] = $db_driver_vehicles[0]['vLicencePlate'];
        $maildata['BOOKING_DATE'] = $dBookingDate_new_mail_date;
        $maildata['BOOKING_TIME'] = $dBookingDate_new_mail_time;
        $maildata['BOOKING_NUMBER'] = $Data1['vBookingNo'];
        // Send sms to User
        // added by SP for sms functionality on 13-7-2019 start
        $passengerData = $obj->MySQLSelect("SELECT r.vPhone,c.vPhoneCode FROM `register_user` AS r, `country` AS c WHERE r.iUserId = {$iUserId} AND r.vCountry = c.vCountryCode");
        $PhoneCodeP = $passengerData[0]['vPhoneCode'];
        $DriverData = $obj->MySQLSelect("SELECT r.vPhone,c.vPhoneCode FROM  `register_driver` AS r, `country` AS c WHERE r.iDriverId = {$iDriverId} AND r.vCountry = c.vCountryCode");
        $PhoneCodeD = $DriverData[0]['vPhoneCode'];
        // added by SP for sms functionality on 13-7-2019 end

        if ('Yes' === $eAutoAssign) {
            $message_layout = $COMM_MEDIA_OBJ->GetSMSTemplate('USER_SEND_MESSAGE_AUTOASSIGN', $maildata, '', $maillanguage);
        } else {
            if ('UberX' === $eType) {
                $message_layout = $COMM_MEDIA_OBJ->GetSMSTemplate('USER_SEND_MESSAGE_APP', $maildata, '', $maillanguage);
            } else {
                $message_layout = $COMM_MEDIA_OBJ->GetSMSTemplate('USER_SEND_MESSAGE', $maildata, '', $maillanguage);
            }
        }
        // $return4 = $COMM_MEDIA_OBJ->SendMemberSMS($vPhone, $vcode, $message_layout, "");
        $return4 = $COMM_MEDIA_OBJ->SendSystemSMS($vPhone, $PhoneCodeP, $message_layout); // added by SP for sms functionality on 13-7-2019
        // Send sms to Driver

        $vPhone = $driver_db[0]['vPhone'];
        $vcode1 = $driver_db[0]['vcode'];
        $maillanguage1 = $driver_db[0]['vLang'];

        $maildata1['PASSENGER_NAME'] = $Pass_name;
        $maildata1['BOOKING_DATE'] = $dBookingDate_new_mail_date;
        $maildata1['BOOKING_TIME'] = $dBookingDate_new_mail_time;
        $maildata1['BOOKING_NUMBER'] = $Data1['vBookingNo'];

        $message_layout = $COMM_MEDIA_OBJ->GetSMSTemplate('DRIVER_SEND_MESSAGE', $maildata1, '', $maillanguage1);
        // $return5 = $COMM_MEDIA_OBJ->SendMemberSMS($vPhone, $vcode1, $message_layout, "");
        $return5 = $COMM_MEDIA_OBJ->SendSystemSMS($vPhone, $PhoneCodeD, $message_layout); // added by SP for sms functionality on 13-7-2019

        if ('' === $iCabBookingId) {
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = $langage_lbl['LBL_MANUAL_BOOKING_ADDED_SUCESSFULLY'].'.';
        } else {
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = $langage_lbl['LBL_MANUAL_BOOKING_UPDETED_SUCESSFULLY'].'.';
        }
        // echo "1";exit;
        $path = $_SERVER['SERVER_NAME'];
        if (!empty($_SESSION['sess_iAdminUserId'])) {
            header('location:'.$tconfig['tsite_url_main_admin'].'cab_booking.php');
        } else {
            header('location:'.$path.'/cabbooking.php');
        }

        if ($return && $return1) {
            $success = 1;
            $var_msg = $langage_lbl['LBL_MANUAL_BOOKING_ADDED_SUCESSFULLY'].'.';
            if (!empty($_SESSION['sess_iAdminUserId'])) {
                header('location:'.$tconfig['tsite_url_main_admin'].'cab_booking.php');
            } else {
                header("location:../cabbooking.php?success=1&vassign={$edit}");
            }

            exit;
        }
        $error = 1;
        $var_msg = $langage_lbl['LBL_ERROR_OCCURED'];

        if (!empty($_SESSION['sess_iAdminUserId'])) {
            header('location:'.$tconfig['tsite_url_main_admin'].'cab_booking.php');
        } else {
            header("location:../cabbooking.php?success=1&vassign={$edit}");
        }

        exit;
    }
    $dataArray = [];
    $dataArray['tSessionId'] = $email_exist[0]['tSessionId'];
    $dataArray['iUserId'] = $iUserId;
    $dataArray['vTimeZone'] = $vTimeZone;
    $dataArray['iVehicleTypeId'] = $iVehicleTypeId;
    $dataArray['vSourceLatitude'] = $vSourceLatitude;
    $dataArray['vSourceLongitude'] = $vSourceLongitude;
    $dataArray['vSourceAddresss'] = $vSourceAddresss;
    $dataArray['vDestLatitude'] = $vDestLatitude;
    $dataArray['vDestLongitude'] = $vDestLongitude;
    $dataArray['tDestAddress'] = $tDestAddress;
    $dataArray['fTollPrice'] = $fTollPrice;
    $dataArray['vTollPriceCurrencyCode'] = $vTollPriceCurrencyCode;
    $dataArray['eTollSkipped'] = $eTollSkipped;
    $dataArray['eType'] = $eTypeinsert;
    $dataArray['eBookingFrom'] = $eBookingFrom;
    $dataArray['eRental'] = 'No';
    $dataArray['eShowOnlyMoto'] = 'No';
    $dataArray['vCouponCode'] = $vCouponCode;

    $dataArray['iHotelBookingId'] = $iHotelBookingId;
    $dataArray['iHotelId'] = $iHotelId;
    $dataArray['isFromHotelPanel'] = $isFromHotelPanel;

    $dataArray['tPackageDetails'] = $tPackageDetails;
    $dataArray['iPackageTypeId'] = $iPackageTypeId;
    $dataArray['tDeliveryIns'] = $tDeliveryIns;
    $dataArray['tPickUpIns'] = $tPickUpIns;
    $dataArray['vReceiverName'] = $vReceiverName;
    $dataArray['vReceiverMobile'] = $vReceiverMobile;

    $dataArray['eFemaleDriverRequest'] = $eFemaleDriverRequest;
    $dataArray['eHandiCapAccessibility'] = $eHandiCapAccessibility;
    $dataArray['eChildSeatAvailable'] = $eChildSeatAvailable;
    $dataArray['iCompanyId'] = $iCompanyId;
    $dataArray['isFromAdminPanel'] = 'Yes';
    $dataArray['eFly'] = $eFly;
    $dataArray['iFromStationId'] = $iFromStationId;
    $dataArray['iToStationId'] = $iToStationId;
    $dataArray['vDistance'] = $orgDistance;
    $dataArray['vDuration'] = $orgDuration;
    $dataArray['delivery_arr'] = $delivery_arr;

    $dataArray['total_del_dist'] = $total_del_dist;
    $dataArray['total_del_time'] = $total_del_time;
    /* $dataArray['ePayType']= $vTripPaymentMode;
      if($vTripPaymentMode == 'Cash') {
      $dataArray['CashPayment']= 'true';
      } else {
      $dataArray['CashPayment']= 'false';
      } */
    echo json_encode($dataArray);

    exit;
}
header("location:../cabbooking.php?success=1&vassign={$edit}");

exit;
