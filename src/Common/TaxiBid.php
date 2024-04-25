<?php



namespace Kesk\Web\Common;

class TaxiBid
{
    public function __construct() {}

    public function getCategories($tCategoryDetails = [])
    {
        $RETURN_CAT = [];
        if (isset($tCategoryDetails) && !empty($tCategoryDetails)) {
            foreach ($tCategoryDetails as $skey => $SubCategories) {
                if (
                    \in_array($SubCategories['eCatType'], [
                        'TaxiBid',
                        'MotoBid',
                    ], true)
                ) {
                    $tCategoryDetails[$skey]['eCatViewType'] = 'List';
                    $RETURN_CAT[] = $tCategoryDetails[$skey];
                }
            }
        }

        return $RETURN_CAT;
    }

    public function saveUserTaxiBidAmount()
    {
        global $obj;
        $OfferFare = $_REQUEST['OfferFare'] ?? '';
        $isTaxiBid = $_REQUEST['isTaxiBid'] ?? 'No';
        $GeneralUserType = isset($_REQUEST['GeneralUserType']) ? trim($_REQUEST['GeneralUserType']) : '';
        $GeneralMemberId = isset($_REQUEST['GeneralMemberId']) ? trim($_REQUEST['GeneralMemberId']) : '';
        $row = $obj->MySQLSelect("SELECT *,iUserId as iMemberId FROM `register_user` WHERE iUserId='{$GeneralMemberId}'");
        $currency = $obj->MySQLSelect("SELECT ratio,vName FROM `currency` WHERE vName='".$row[0]['vCurrencyPassenger']."'");
        $Taxi_bid['fTaxiBidAmount'] = $OfferFare / $currency[0]['ratio'];
        $Taxi_bid['isTaxiBid'] = $isTaxiBid;

        return $Taxi_bid;
    }

    public function sendDriverQuotationToUser($iCabRequestId, $OfferFare, $driver_id, $vMsgCode): void
    {
        global $obj, $EVENT_MSG_OBJ, $tconfig, $LANG_OBJ;

        if ('' !== $iCabRequestId) {
            $lang = $_REQUEST['vGeneralLang'] ?? '';
            if ('' === $lang || null === $lang) {
                $lang = $LANG_OBJ->FetchDefaultLangData('vCode');
            }
            $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($lang, '1', '');
            $sqldata = "SELECT * FROM `cab_request_now` WHERE  iCabRequestId = {$iCabRequestId} AND eStatus = 'Requesting' ";
            $cab_request_now = $obj->MySQLSelect($sqldata);

            if (!empty($cab_request_now) && \count($cab_request_now) > 0) {
                $cab_request_now = $cab_request_now[0];

                $iUserId = $cab_request_now['iUserId'];
                $iUser_fTaxiBidAmount = $cab_request_now['fTaxiBidAmount'];
                // ------------------vCurrency-----------------
                $driver_data = $obj->MySQLSelect("SELECT vLatitude,vLongitude,CONCAT(vName, ' ', vLastName) as driverName, vImage,vEmail as drivermail,iDriverId,vCurrencyDriver,vAvgRating FROM `register_driver` WHERE iDriverId = '".$driver_id."'");
                $driver_currency = $obj->MySQLSelect("SELECT ratio,vName FROM `currency` WHERE vName='".$driver_data[0]['vCurrencyDriver']."'");
                $org_OfferFare = $OfferFare / $driver_currency[0]['ratio'];
                $amount_driver = formateNumAsPerCurrency($OfferFare, $driver_data[0]['vCurrencyDriver']);
                // ------------------vCurrency-----------------
                // ------------------send noti-----------------

                $isSameFare = 'No';
                if (number_format($iUser_fTaxiBidAmount, 2) === number_format($org_OfferFare, 2)) {
                    $isSameFare = 'Yes';
                }

                $getDurationDistance_data = $this->getDurationDistance($driver_data[0]['vLatitude'], $driver_data[0]['vLongitude'], $cab_request_now['vSourceLatitude'], $cab_request_now['vSourceLongitude']);

                $row1 = $obj->MySQLSelect("SELECT vCurrencyPassenger, vPhoneCode , vPhone, eHmsDevice , iUserId, vCurrencyPassenger, vLang, eDeviceType, iGcmRegId, eAppTerminate, eDebugMode, vName, vLastName FROM `register_user` WHERE iUserId = '".$iUserId."'");
                $passenger_currency = $obj->MySQLSelect("SELECT ratio,vName FROM `currency` WHERE vName='".$row1[0]['vCurrencyPassenger']."'");
                $amount_passenger = formateNumAsPerCurrency($org_OfferFare * $passenger_currency[0]['ratio'], $row1[0]['vCurrencyPassenger']);
                $generalDataArr = $final_message = [];
                $final_message['Message'] = 'TaxiBidDriverQuotation';
                $final_message['MsgType'] = 'TaxiBidDriverQuotation';
                $final_message['time'] = time();
                $final_message['OfferFare'] = $amount_passenger;
                $final_message['eType'] = 'TaxiBid';
                $final_message['vMsgCode'] = $vMsgCode;
                if (!empty($driver_data)) {
                    // $final_message['driverAvgRating'] = $this->getAvgRating($driver_id, 'Driver');
                    $final_message['driverAvgRating'] = $driver_data[0]['vAvgRating'];
                    $final_message['driverName'] = $driver_data[0]['driverName'];
                    $final_message['drivermail'] = $driver_data[0]['drivermail'];
                    // $final_message['driverImage'] = $tconfig["tsite_upload_images_driver"] . "/" . $driver_data[0]['iDriverId'] . "/2_" . $driver_data[0]['vImage'];
                    $final_message['driverImage'] = '';
                    if (file_exists($tconfig['tsite_upload_images_driver_path'].'/'.$driver_data[0]['iDriverId'].'/2_'.$driver_data[0]['vImage'])) {
                        $final_message['driverImage'] = $tconfig['tsite_upload_images_driver'].'/'.$driver_data[0]['iDriverId'].'/2_'.$driver_data[0]['vImage'];
                    }

                    $final_message['driverId'] = $driver_id;
                    $final_message['driverVehicle'] = $this->getDriverVehicle($driver_id)['vehicle'];
                    $final_message['DurationToReach'] = $getDurationDistance_data['distance'];
                    $final_message['TimeToReach'] = $getDurationDistance_data['duration'];
                    $final_message['isSameFare'] = $isSameFare;
                }
                $generalDataArr[] = [
                    'eDeviceType' => $row1[0]['eDeviceType'],
                    'deviceToken' => $row1[0]['iGcmRegId'],
                    'alertMsg' => '',
                    'eAppTerminate' => $row1[0]['eAppTerminate'],
                    'eDebugMode' => $row1[0]['eDebugMode'],
                    'message' => $final_message,
                    'eHmsDevice' => $row1[0]['eHmsDevice'],
                    'channelName' => 'PASSENGER_'.$iUserId,
                ];

                $arr['NOTIFICATION'] = $EVENT_MSG_OBJ->send(['GENERAL_DATA' => $generalDataArr], RN_USER);
                // ------------------send noti-----------------
                // ------------------save amount to driver request-----------------
                $where = " vMsgCode='".$vMsgCode."' AND iDriverId = '".$driver_id."'";
                $data_driver_request['fTaxiBidAmount'] = $org_OfferFare;
                $obj->MySQLQueryPerform('driver_request', $data_driver_request, 'update', $where);
                $returnArr['Action'] = '1';
                $returnArr['message'] = str_replace(['#AMOUNT#'], [$amount_driver], $languageLabelsArr['LBL_WAIT_FOR_USER_REPLY']);
                setDataResponse($returnArr);
            // ------------------save amount to driver request-----------------
            } else {
                $returnArr['Action'] = '0';
                $returnArr['message'] = 'LBL_RIDE_REQUEST_EXPIRED_TAXI_BID_TEXT';
                setDataResponse($returnArr);
            }
        }
    }

    public function getDriverVehicle($iDriverId)
    {
        global $obj;
        $ssql = " AND dv.eType != 'UberX'";
        $sql = "SELECT register_driver.iDriverVehicleId as DriverSelectedVehicleId,make.vMake, model.vTitle, dv.* FROM `driver_vehicle` dv, make, model,register_driver WHERE dv.iDriverId='{$iDriverId}' AND register_driver.iDriverId = '{$iDriverId}' AND dv.`iMakeId` = make.`iMakeId` AND dv.`iModelId` = model.`iModelId` AND dv.`eStatus`='Active'".$ssql.' Order By dv.iDriverVehicleId desc';
        $Data_Car = $obj->MySQLSelect($sql);
        $Data_Car = $Data_Car[0];
        $arr['vehicle'] = $Data_Car['vMake'].'('.$Data_Car['vTitle'].')';

        return $arr;
    }

    public function getAvgRating($iMemberId, $UserType): void
    {
        global $obj;
        /*global $obj;
        if ($UserType == "Passenger") {
            $iMemberField = 'iUserId';
            $OtherUserType = "Driver";
        } else {
            $iMemberField = 'iDriverId';
            $OtherUserType = "Passenger";
        }
        $bidding_posts = $obj->MySQLSelect("SELECT GROUP_CONCAT(iBiddingPostId) as iBiddingPostIds FROM $this->biddingPostTable WHERE $iMemberField = '$iMemberId' AND eStatus = 'Completed'");
        $iBiddingPostIds = $bidding_posts[0]['iBiddingPostIds'];
        $average_rating = "0.0";
        if (!empty($iBiddingPostIds)) {
            $total_ratings_count = $obj->MySQLSelect("SELECT COUNT(iRatingId) as rating_count, SUM(fRating) as total_rating FROM bidding_service_ratings WHERE iBiddingPostId IN ($iBiddingPostIds) AND eUserType = '$OtherUserType'");
            $rating_count = $total_ratings_count[0]['rating_count'];
            $total_rating = $total_ratings_count[0]['total_rating'];
            if ($rating_count > 0) {
                $average_rating = round($total_rating / $rating_count, 1);
            }
        }*/
        /*$bidding_posts = $obj->MySQLSelect("SELECT * FROM `register_driver` WHERE `iDriverId` = '$iMemberId'");
        return $average_rating;*/
    }

    public function saveUserAcceptedAmount($RequestMsgCode, $driverId): void
    {
        global $obj, $EVENT_MSG_OBJ, $LANG_OBJ;
        $GeneralMemberId = isset($_REQUEST['GeneralMemberId']) ? trim($_REQUEST['GeneralMemberId']) : '';
        $GeneralUserType = isset($_REQUEST['GeneralUserType']) ? trim($_REQUEST['GeneralUserType']) : '';

        $lang = $_REQUEST['vGeneralLang'] ?? '';
        if ('' === $lang || null === $lang) {
            $lang = $LANG_OBJ->FetchDefaultLangData('vCode');
        }
        $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($lang, '1', '');

        $sqldata = "SELECT fTaxiBidAmount FROM `driver_request` WHERE  vMsgCode='".$RequestMsgCode."' AND iDriverId = '".$driverId."' ";
        $driver_request = $obj->MySQLSelect($sqldata);
        $fTaxiBidAmount = $driver_request[0]['fTaxiBidAmount'];
        // ------------------cab request driver-----------------
        $where = " tMsgCode='".$RequestMsgCode."'";
        $data_driver_request['fUserAcceptedTaxiBidAmount'] = $fTaxiBidAmount;
        $obj->MySQLQueryPerform('cab_request_now', $data_driver_request, 'update', $where);
        $sqldata = "SELECT iCabRequestId,fTaxiBidAmount,vSourceLatitude,vSourceLongitude,vDestLatitude,vDestLongitude,tSourceAddress,tDestAddress FROM `cab_request_now` WHERE tMsgCode='".$RequestMsgCode."'";
        $cab_request_now = $obj->MySQLSelect($sqldata);
        $cab_request_now = $cab_request_now[0];
        $sourceLoc = $cab_request_now['vSourceLatitude'].','.$cab_request_now['vSourceLongitude'];
        $destLoc = $cab_request_now['vDestLatitude'].','.$cab_request_now['vDestLongitude'];
        $PickUpAddress = $cab_request_now['tSourceAddress'];
        $DestAddress = $cab_request_now['tDestAddress'];
        // ------------------cab request driver-----------------

        $sql_driver_status_chk = 'SELECT vLastName,vName,vTripStatus,iGcmRegId,eDeviceType,iDriverId,vLang,tSessionId,iAppVersion,eAppTerminate,eDebugMode,eHmsDevice FROM register_driver WHERE  iDriverId IN ('.$driverId.')   ';
        $result_driverData = $obj->MySQLSelect($sql_driver_status_chk);
        foreach ($result_driverData as $item) {
            $alertMsg_db = '';
            $generalDataArr[] = [
                'eDeviceType' => $item['eDeviceType'],
                'deviceToken' => $item['iGcmRegId'],
                'alertMsg' => '',
                'eAppTerminate' => $item['eAppTerminate'],
                'eDebugMode' => $item['eDebugMode'],
                'eHmsDevice' => $item['eHmsDevice'],
                'message' => [
                    'iUserId' => $GeneralMemberId,
                    'iDriverId' => $driverId,
                    'tMessage' => 'WaitTripGenerateProcessRunning',
                    'iMsgCode' => $RequestMsgCode,
                    'MsgType' => 'WaitTripGenerateProcessRunning',
                    'vStartLatlong' => $sourceLoc,
                    'vEndLatlong' => $destLoc,
                    'tStartAddress' => $PickUpAddress,
                    'tEndAddress' => $DestAddress,
                    'eType' => 'TaxiBid',
                ],
                'channelName' => 'DRIVER_'.$item['iDriverId'],
            ];
        }

        $data = $EVENT_MSG_OBJ->send(['GENERAL_DATA' => $generalDataArr], RN_PROVIDER);

        // ------------------callGenerateTripType-----------------
        $driver_data = $obj->MySQLSelect("SELECT tSessionId,vCurrencyDriver FROM `register_driver` WHERE iDriverId = '".$driverId."'");
        $driver_data = $driver_data[0];

        $driver_currency = $obj->MySQLSelect("SELECT ratio,vName FROM `currency` WHERE vName='".$driver_data['vCurrencyDriver']."'");
        $driver_fTaxiBidAmount = $fTaxiBidAmount * $driver_currency[0]['ratio'];
        $amount_driver = formateNumAsPerCurrency($driver_fTaxiBidAmount, $driver_data[0]['vCurrencyDriver']);

        $DATA_ARR['PassengerID'] = $GeneralMemberId;
        $DATA_ARR['DriverID'] = $driverId;
        $DATA_ARR['iCabRequestId'] = $cab_request_now['iCabRequestId'];
        $DATA_ARR['start_lat'] = $cab_request_now['vSourceLatitude'];
        $DATA_ARR['start_lon'] = $cab_request_now['vSourceLongitude'];
        $DATA_ARR['sAddress'] = $PickUpAddress;
        $DATA_ARR['vMsgCode'] = $RequestMsgCode;
        $DATA_ARR['ride_type'] = 'Ride';
        $DATA_ARR['REQUEST_TYPE'] = 'Ride';
        $DATA_ARR['GeneralMemberId'] = $driverId;
        $DATA_ARR['tSessionId'] = $driver_data['tSessionId'];
        $DATA_ARR['GeneralUserType'] = 'Driver';
        $DATA_ARR['type'] = 'GenerateTrip';
        $DATA_ARR['OfferAccepted'] = 'Yes';
        $DATA_ARR['isTaxiBid'] = 'Yes';
        $DATA_ARR['DRIVER_NAME'] = $result_driverData[0]['vName'].' '.$result_driverData[0]['vLastName'];
        $DATA_ARR['FINAL_AMOUNT'] = $amount_driver;

        $GenerateTripData = $this->callGenerateTripType($DATA_ARR);
        // ------------------callGenerateTripType-----------------

        /*$sql_driver_status_chk = "SELECT vTripStatus,iGcmRegId,eDeviceType,iDriverId,vLang,tSessionId,iAppVersion,eAppTerminate,eDebugMode,eHmsDevice FROM register_driver WHERE  iDriverId IN (" . $driverId . ")   ";
        $result_driverData = $obj->MySQLSelect($sql_driver_status_chk);*/

        $userData = $obj->MySQLSelect("SELECT  vName,vLastName FROM `register_user` WHERE iUserId='{$GeneralMemberId}'");
        $userData = $userData[0];
        $User_Name = $userData['vName'].' '.$userData['vLastName'];
        $vtitle = str_replace(['#DRIVER#', '#AMOUNT#'], [$User_Name, $amount_driver], $languageLabelsArr['LBL_USER_ACCEPT_DRIVER_REQUEST_TEXI_BID_TEXT']);
        // $generalDataArr = [];
        foreach ($result_driverData as $item) {
            $alertMsg_db = '';
            $generalDataArr[] = [
                'eDeviceType' => $item['eDeviceType'],
                'deviceToken' => $item['iGcmRegId'],
                'alertMsg' => '',
                'eAppTerminate' => $item['eAppTerminate'],
                'eDebugMode' => $item['eDebugMode'],
                'eHmsDevice' => $item['eHmsDevice'],
                'vTitle' => $vtitle,
                'message' => [
                    'iUserId' => $GeneralMemberId,
                    'iDriverId' => $driverId,
                    'tMessage' => 'UserAccepDriverOffer',
                    'iMsgCode' => $RequestMsgCode,
                    'MsgType' => 'UserAccepDriverOffer',
                    'vStartLatlong' => $sourceLoc,
                    'vEndLatlong' => $destLoc,
                    'tStartAddress' => $PickUpAddress,
                    'tEndAddress' => $DestAddress,
                    'eType' => 'TaxiBid',
                    'vTitle' => $vtitle,
                ],
                'channelName' => 'DRIVER_'.$item['iDriverId'],
            ];
        }

        $data = $EVENT_MSG_OBJ->send(['GENERAL_DATA' => $generalDataArr], RN_PROVIDER);

        $returnArr['Action'] = '1';
        setDataResponse($returnArr);
    }

    public function getDurationDistance($tStartLat, $tStartLong, $tEndLat, $tEndLong)
    {
        /* $tEndLat = "23.083487";
         $tEndLong = "72.614733";*/
        $requestDataArr = [];
        $vLangCodeData = get_value('language_master', 'vCode, vGMapLangCode', 'eDefault', 'Yes');
        $vGMapLangCode = $vLangCodeData[0]['vGMapLangCode'];
        $requestDataArr['SOURCE_LATITUDE'] = $tStartLat;
        $requestDataArr['SOURCE_LONGITUDE'] = $tStartLong;
        $requestDataArr['DEST_LATITUDE'] = $tEndLat;
        $requestDataArr['DEST_LONGITUDE'] = $tEndLong;
        $requestDataArr['LANGUAGE_CODE'] = $vGMapLangCode;
        $direction_data = getPathInfoBetweenLocations($requestDataArr);

        $returnArr = [];
        if (isset($direction_data) && !empty($direction_data)) {
            $returnArr['distance'] = number_format($direction_data['distance'] / 1_000, 2).' km';
            /* if($returnArr['distance'] < 1){
                 $returnArr['distance'] = trim(number_format($returnArr['distance'] * 1000 ,2 ) . ' m');
             }*/

            $returnArr['duration'] = trim($this->convertSecToMin($direction_data['duration']));
        }

        return $returnArr;
    }

    public function convertSecToMin($seconds)
    {
        $minutes = floor($seconds / 60);

        return convertMinToHoursToDays($minutes, 'Minutes', 1);
    }

    public function callGenerateTripType($data)
    {
        global $tconfig, $AUTH_MEMBER_OBJ;

        $webservice_url = $tconfig['tsite_url'].WEBSERVICE_API_FILE_NAME;

        $token = $AUTH_MEMBER_OBJ->getPackageData()['ANDROID_DRIVER'];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $webservice_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer '.encrypt_bycrypt($token),
        ]);
        $response = curl_exec($ch);
        $error_msg = curl_error($ch);
        if (!empty($error_msg)) {
        }
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (!empty($response) && isJsonTextGT($response)) {
            $returnArr = json_decode($response, true);

            $ARR_RETURN['iTripId'] = $returnArr['message']['iTripId'];
            $ARR_RETURN['Action'] = '1';
        } else {
            $ARR_RETURN['Action'] = '0';
        }

        return $ARR_RETURN;
    }
}
