<?php



namespace Kesk\Web\Common;

class TrackService
{
    public function __construct() {}

    public function getCategories($tCategoryDetails = [])
    {
        global $languageLabelsArrTrackService, $tconfig, $obj, $master_service_category_tbl;
        if (empty($tCategoryDetails)) {
            $service_details = $obj->MySQLSelect("SELECT tCategoryDetails FROM {$master_service_category_tbl} WHERE eType = 'TrackService' ");
            $tCategoryDetails = json_decode($service_details[0]['tCategoryDetails'], true);
        }
        $vImageTrackService = '';
        if (!empty($tCategoryDetails['TrackService']['vImage']) && file_exists($tconfig['tsite_upload_app_home_screen_images_path'].$tCategoryDetails['TrackService']['vImage'])) {
            $imagedata = getimagesize(str_replace('https', 'http', $tconfig['tsite_upload_app_home_screen_images'].$tCategoryDetails['TrackService']['vImage']));
            $vImageWidthTrackService = (string) $imagedata[0];
            $vImageHeightTrackService = (string) $imagedata[1];
            $vImageTrackService = $tconfig['tsite_upload_app_home_screen_images'].$tCategoryDetails['TrackService']['vImage'];
        }
        $vImageTrackServiceAdd = '';
        if (!empty($tCategoryDetails['TrackServiceAdd']['vImage']) && file_exists($tconfig['tsite_upload_app_home_screen_images_path'].$tCategoryDetails['TrackServiceAdd']['vImage'])) {
            $imagedata = getimagesize(str_replace('https', 'http', $tconfig['tsite_upload_app_home_screen_images'].$tCategoryDetails['TrackServiceAdd']['vImage']));
            $vImageWidthTrackServiceAdd = (string) $imagedata[0];
            $vImageHeightTrackServiceAdd = (string) $imagedata[1];
            $vImageTrackServiceAdd = $tconfig['tsite_upload_app_home_screen_images'].$tCategoryDetails['TrackServiceAdd']['vImage'];
        }
        $category_arr = [['vCategory' => $languageLabelsArrTrackService['LBL_TRACK_SERVICE_TRACK_MEMBER_TXT'], 'vImage' => $vImageTrackService, 'vListLogo' => $vImageTrackService, 'vImageWidth' => $vImageWidthTrackService, 'vImageHeight' => $vImageHeightTrackService, 'eCatType' => 'TrackService'], ['vCategory' => $languageLabelsArrTrackService['LBL_TRACK_SERVICE_SETUP_PROFILE_TXT'], 'vImage' => $vImageTrackServiceAdd, 'vListLogo' => $vImageTrackServiceAdd, 'vImageWidth' => $vImageWidthTrackServiceAdd, 'vImageHeight' => $vImageHeightTrackServiceAdd, 'eCatType' => 'TrackServiceAdd']];
        if (isset($_REQUEST['type']) && 'getServiceCategoriesPro' === $_REQUEST['type']) {
            $tracking_users = $this->listTrackingUsers();
            if (!empty($tracking_users['message']) && \count($tracking_users['message']) > 0) {
                $category_arr = [['vCategory' => $languageLabelsArrTrackService['LBL_TRACK_SERVICE_TRACK_MEMBER_TXT'], 'vImage' => $vImageTrackService, 'vListLogo' => $vImageTrackService, 'vImageWidth' => $vImageWidthTrackService, 'vImageHeight' => $vImageHeightTrackService, 'eCatType' => 'TrackService']];
            } else {
                $category_arr = [['vCategory' => $languageLabelsArrTrackService['LBL_TRACK_SERVICE_SETUP_PROFILE_TXT'], 'vImage' => $vImageTrackServiceAdd, 'vListLogo' => $vImageTrackServiceAdd, 'vImageWidth' => $vImageWidthTrackServiceAdd, 'vImageHeight' => $vImageHeightTrackServiceAdd, 'eCatType' => 'TrackServiceAdd']];
            }
        }

        return $category_arr;
    }

    public function verifyInviteCode(): void
    {
        global $obj;
        $iMemberId = $_REQUEST['GeneralMemberId'] ?? '';
        $vInviteCode = $_REQUEST['vInviteCode'] ?? '';
        $vPhoneCode = $_REQUEST['vPhoneCode'] ?? '';
        $vPhone = $_REQUEST['MobileNo'] ?? '';
        $tracking_vehicle = $obj->MySQLSelect("SELECT * FROM track_service_users WHERE vInviteCode = '".$vInviteCode."' ");
        if ($tracking_vehicle[0]['iUserId'] === $iMemberId) {
            $returnArr['Action'] = '0';
            $returnArr['message'] = 'LBL_TRACK_SERVICE_INVITE_CODE_IN_USED';
            setDataResponse($returnArr);
        }
        if (!empty($tracking_vehicle) && \count($tracking_vehicle) > 0) {
            if ($tracking_vehicle[0]['vPhoneCode'] === $vPhoneCode && $tracking_vehicle[0]['vPhone'] === $vPhone) {
                $returnArr['Action'] = '1';
                $obj->sql_query("UPDATE track_service_users SET iUserId = '".$iMemberId."' WHERE iTrackServiceUserId = '".$tracking_vehicle[0]['iTrackServiceUserId']."' ");
            } else {
                $returnArr['Action'] = '0';
                $returnArr['message'] = 'LBL_INVALID_PHONE_NUMBER';
            }
        } else {
            $returnArr['Action'] = '0';
            $returnArr['message'] = 'LBL_TRACK_SERVICE_INVITE_CODE_INVALID';
        }
        setDataResponse($returnArr);
    }

    public function initiateTrackingTrip(): void
    {
        global $obj;
        $iDriverId = $_REQUEST['GeneralMemberId'] ?? '';
        $TripType = $_REQUEST['TripType'] ?? '';
        $tStartLat = $_REQUEST['tStartLat'] ?? '';
        $tStartLong = $_REQUEST['tStartLong'] ?? '';
        $driverData = $obj->MySQLSelect("SELECT iDriverVehicleId , iTrackServiceCompanyId FROM register_driver WHERE iDriverId = '".$iDriverId."'");
        $tStartLocation = getLocationNameLatLog($tStartLat, $tStartLong);
        $track_service_users = $this->getDriverConnectedUsers($iDriverId);
        if (empty($track_service_users)) {
            $returnArr['Action'] = '0';
            $returnArr['message'] = 'LBL_TRACK_SERVICE_START_TRIP_VALIDATION_MSG';
            setDataResponse($returnArr);
        }
        $iUserIdsArr = array_column($track_service_users, 'iTrackServiceUserId');
        $iUserIds = implode(',', $iUserIdsArr);
        $Data_trip = [];
        $Data_trip['iDriverId'] = $iDriverId;
        $Data_trip['iUserIds'] = $iUserIds;
        $Data_trip['eTripType'] = $TripType;
        $Data_trip['eTripStatus'] = 'PICKUP' === strtoupper($TripType) ? 'Active' : 'Onboarding';
        $Data_trip['tStartLocation'] = $tStartLocation;
        $Data_trip['tStartLat'] = $tStartLat;
        $Data_trip['tStartLong'] = $tStartLong;
        $Data_trip['dAddedDate'] = date('Y-m-d H:i:s');
        $Data_trip['iDriverVehicleId'] = $driverData[0]['iDriverVehicleId'];
        $Data_trip['iTrackServiceCompanyId'] = $driverData[0]['iTrackServiceCompanyId'];
        $iTrackServiceTripId = $obj->MySQLQueryPerform('track_service_trips', $Data_trip, 'insert');
        $obj->sql_query("UPDATE register_driver SET iTrackServiceTripId = '".$iTrackServiceTripId."' WHERE iDriverId = '".$iDriverId."'");
        $returnArr['Action'] = '1';
        $returnArr['USER_DATA'] = getDriverDetailInfo($iDriverId);
        setDataResponse($returnArr);
    }

    public function getDriverConnectedUsers($iDriverId)
    {
        global $obj;
        $track_service_users = $obj->MySQLSelect("SELECT iTrackServiceUserId, CONCAT(vName, ' ', vLastName) as userName, vLatitude, vLongitude, vAddress FROM track_service_users WHERE iUserId > 0 AND iDriverId = '".$iDriverId."' AND eStatus = 'Active' ");

        return $track_service_users;
    }

    public function updateTrackingTripStatus(): void
    {
        global $obj, $DRIVER_START_DISTANCE_FROM_START_END_LOCATION, $EVENT_MSG_OBJ, $LANG_OBJ, $iServiceId;
        $iTrackServiceTripId = $_REQUEST['iTrackServiceTripId'] ?? '';
        $tEndLat = $_REQUEST['tEndLat'] ?? '';
        $tEndLong = $_REQUEST['tEndLong'] ?? '';
        $tCancelReason = $_REQUEST['tCancelReason'] ?? '';
        $TripStatus = $_REQUEST['TripStatus'] ?? '';
        $Data_trip_update = [];
        $track_service_trip = $obj->MySQLSelect("SELECT eTripType, eTripStatus, iDriverId, iUserIds FROM track_service_trips WHERE iTrackServiceTripId = '".$iTrackServiceTripId."'");
        $driverData = $obj->MySQLSelect("SELECT tsc.vLatitude, tsc.vLongitude, dv.vLicencePlate FROM register_driver as rd LEFT JOIN track_service_company as tsc ON tsc.iTrackServiceCompanyId = rd.iTrackServiceCompanyId LEFT JOIN driver_vehicle as dv ON dv.iDriverVehicleId = rd.iDriverVehicleId WHERE rd.iDriverId = '".$track_service_trip[0]['iDriverId']."'");
        $compLat = $driverData[0]['vLatitude'];
        $compLong = $driverData[0]['vLongitude'];
        $vLicencePlateNo = $driverData[0]['vLicencePlate'];
        if (!empty($TripStatus) && 'CANCELLED' === strtoupper($TripStatus)) {
            $eTripStatus = 'Cancelled';
        } else {
            $eTripStatus = $track_service_trip[0]['eTripStatus'];
        }
        $eTripType = $track_service_trip[0]['eTripType'];
        $sendNotification = 'No';
        $CLOSED = 0;
        if ('ONBOARDING' === strtoupper($eTripStatus)) {
            $NextTripStatus = 'Active';
            $Data_trip_update['dOnboardingDate'] = date('Y-m-d H:i:s');
            $sendNotification = 'Yes';
            $noti_label = 'LBL_TRACK_SERVICE_ONBOARDING_NOTI_MSG';
        } elseif ('ACTIVE' === strtoupper($eTripStatus)) {
            $driverdistance = distanceByLocation($tEndLat, $tEndLong, $compLat, $compLong, 'K');
            $driverdistance *= 1_000;
            if ($driverdistance > $DRIVER_START_DISTANCE_FROM_START_END_LOCATION) {
            }
            $NextTripStatus = 'OnGoingTrip';
            $Data_trip_update['dStartDate'] = date('Y-m-d H:i:s');
            $sendNotification = 'Yes';
            if ('Pickup' === $eTripType) {
                $noti_label = 'LBL_TRACK_SERVICE_PICKUP_START_NOTI_MSG';
            } else {
                $noti_label = 'LBL_TRACK_SERVICE_DROPOFF_START_NOTI_MSG';
            }
        } elseif ('ONGOINGTRIP' === strtoupper($eTripStatus)) {
            $driverdistance = distanceByLocation($tEndLat, $tEndLong, $compLat, $compLong, 'K');
            $driverdistance *= 1_000;
            if ($driverdistance > $DRIVER_START_DISTANCE_FROM_START_END_LOCATION) {
            }
            $NextTripStatus = 'Finished';
            $tEndLocation = getLocationNameLatLog($tEndLat, $tEndLong);
            $Data_trip_update['tEndLocation'] = $tEndLocation;
            $Data_trip_update['tEndLat'] = $tEndLat;
            $Data_trip_update['tEndLong'] = $tEndLong;
            $Data_trip_update['dEndDate'] = date('Y-m-d H:i:s');
            if ('Pickup' === $eTripType) {
                $sendNotification = 'Yes';
                $noti_label = 'LBL_TRACK_SERVICE_PICKUP_END_NOTI_MSG';
                $CLOSED = 1;
            } else {
                $sendNotification = 'Yes';
                $noti_label = 'LBL_TRACK_SERVICE_DROPOFF_END_NOTI_MSG';
                $CLOSED = 1;
            }
        } elseif ('CANCELLED' === strtoupper($eTripStatus)) {
            $NextTripStatus = 'Cancelled';
            $Data_trip_update['dEndDate'] = date('Y-m-d H:i:s');
            $Data_trip_update['tCancelReason'] = $tCancelReason;
            $sendNotification = 'Yes';
            $noti_label = 'LBL_TRACK_SERVICE_TRIP_CANCELLED_NOTI_MSG';
            $CLOSED = 1;
        }
        $Data_trip_update['eTripStatus'] = $NextTripStatus;
        $where = " iTrackServiceTripId = '".$iTrackServiceTripId."' ";
        $obj->MySQLQueryPerform('track_service_trips', $Data_trip_update, 'update', $where);
        if ('Yes' === $sendNotification) {
            $companyUsers = $obj->MySQLSelect("SELECT CONCAT(tsu.vName, ' ', tsu.vLastName) as childName, tsu.iUserId, ru.eDeviceType, ru.iGcmRegId, ru.eAppTerminate, ru.eDebugMode, ru.eHmsDevice, ru.vLang, ru.tSessionId FROM track_service_users as tsu LEFT JOIN register_user as ru ON ru.iUserId = tsu.iUserId WHERE tsu.iTrackServiceUserId IN (".$track_service_trip[0]['iUserIds'].') AND tsu.iUserId > 0');
            $vMsgCode = (string) time();
            $generalDataArr = [];
            foreach ($companyUsers as $user) {
                $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($user['vLang'], '1', $iServiceId);
                $alertMsg = str_replace(['#VEHICEL_PLATENO#', '#CHILD_NAME#'], [$vLicencePlateNo, $user['childName']], $languageLabelsArr[$noti_label]);
                $message_arr = [];
                $message_arr['vMsgCode'] = $vMsgCode;
                $message_arr['vTitle'] = $alertMsg;
                $message_arr['MsgType'] = 'TrackMember';
                $message_arr['iDriverId'] = $track_service_trip[0]['iDriverId'];
                $message_arr['CLOSED'] = $CLOSED;
                $generalDataArr[] = ['eDeviceType' => $user['eDeviceType'], 'deviceToken' => $user['iGcmRegId'], 'eAppTerminate' => $user['eAppTerminate'], 'eDebugMode' => $user['eDebugMode'], 'eHmsDevice' => $user['eHmsDevice'], 'tSessionId' => $user['tSessionId'], 'alertMsg' => $alertMsg, 'message' => $message_arr, 'channelName' => 'PASSENGER_'.$user['iUserId']];
            }
            $EVENT_MSG_OBJ->send(['GENERAL_DATA' => $generalDataArr], RN_USER);
        }
        $returnArr['Action'] = '1';
        $returnArr['USER_DATA'] = getDriverDetailInfo($track_service_trip[0]['iDriverId']);
        setDataResponse($returnArr);
    }

    public function fetchTrackingTripStatus($iTrackServiceTripId)
    {
        global $obj;
        $TripDetails = '';
        if ($iTrackServiceTripId > 0) {
            $track_service_trip = $obj->MySQLSelect("SELECT tst.iDriverId, tst.eTripType, tst.eTripStatus, tst.tStartLocation, tst.tEndLocation, CONCAT(rd.vName, ' ', rd.vLastName) as driverName, tsc.vCompany, CONCAT('+', tsc.vCode, tsc.vPhone) as orgPhone, tsc.vLatitude, tsc.vLongitude FROM track_service_trips as tst LEFT JOIN register_driver as rd ON rd.iDriverId = tst.iDriverId LEFT JOIN track_service_company as tsc ON tsc.iTrackServiceCompanyId = rd.iTrackServiceCompanyId WHERE tst.iTrackServiceTripId = '".$iTrackServiceTripId."' AND tst.eTripStatus NOT IN ('Finished', 'Cancelled') ");
            if (!empty($track_service_trip) && \count($track_service_trip) > 0) {
                $track_service_users = $this->getDriverConnectedUsers($track_service_trip[0]['iDriverId']);
                $TripDetails = [];
                $TripDetails['TripType'] = $track_service_trip[0]['eTripType'];
                $TripDetails['TripStatus'] = $track_service_trip[0]['eTripStatus'];
                $TripDetails['tStartAddress'] = $track_service_trip[0]['tStartLocation'];
                $TripDetails['tEndAddress'] = $track_service_trip[0]['tEndLocation'];
                $TripDetails['TripType'] = $track_service_trip[0]['eTripType'];
                $TripDetails['iTrackServiceTripId'] = $iTrackServiceTripId;
                $TripDetails['driverName'] = $track_service_trip[0]['driverName'];
                $TripDetails['orgName'] = $track_service_trip[0]['vCompany'];
                $TripDetails['orgPhone'] = $track_service_trip[0]['orgPhone'];
                $TripDetails['orgLatitude'] = $track_service_trip[0]['vLatitude'];
                $TripDetails['orgLongitude'] = $track_service_trip[0]['vLongitude'];
                $TripDetails['userList'] = $track_service_users;
            }
        }

        return $TripDetails;
    }

    public function getTrackingTripsDriver(): void
    {
        global $obj, $LANG_OBJ, $iServiceId, $setupInfoDataArr;
        $iDriverId = $_REQUEST['GeneralMemberId'] ?? '';
        $vLang = $_REQUEST['vGeneralLang'] ?? '';
        $page = isset($_REQUEST['page']) ? trim($_REQUEST['page']) : 1;
        $vFromDate = $_REQUEST['vFromDate'] ?? '';
        $vTimeZone = $_REQUEST['vTimeZone'] ?? '';
        $systemTimeZone = date_default_timezone_get();
        if (!empty($vTimeZone)) {
            $vConvertFromDate = converToTz($vFromDate, $vTimeZone, $systemTimeZone, 'Y-m-d');
        }
        if (empty($vLang)) {
            $vLang = $LANG_OBJ->FetchDefaultLangData('vCode');
        }
        $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLang, '1', $iServiceId);
        $track_service_trips = $obj->MySQLSelect("SELECT tst.*, dv.vLicencePlate, ma.vMake, mo.vTitle FROM track_service_trips as tst LEFT JOIN driver_vehicle as dv ON dv.iDriverVehicleId = tst.iDriverVehicleId LEFT JOIN make as ma ON ma.iMakeId = dv.iMakeId LEFT JOIN model as mo ON mo.iModelId = dv.iModelId WHERE tst.iDriverId = '{$iDriverId}' AND DATE(tst.dAddedDate) = '".$vFromDate."' AND tst.eTripStatus IN ('Finished', 'Cancelled') ORDER BY tst.dAddedDate DESC");
        if (!empty($track_service_trips) && \count($track_service_trips) > 0) {
            $tripsArr = [];
            foreach ($track_service_trips as $k => $trip) {
                $tripsArr[$k]['TripType'] = $trip['eTripType'];
                $tripsArr[$k]['tStartAddress'] = $trip['tStartLocation'];
                $tripsArr[$k]['tStartLat'] = $trip['tStartLat'];
                $tripsArr[$k]['tStartLong'] = $trip['tStartLong'];
                $tripsArr[$k]['tEndAddress'] = $trip['tEndLocation'];
                $tripsArr[$k]['tEndLat'] = $trip['tEndLat'];
                $tripsArr[$k]['tEndLong'] = $trip['tEndLong'];
                $tripsArr[$k]['vMake'] = $trip['vMake'];
                $tripsArr[$k]['vModel'] = $trip['vTitle'];
                $tripsArr[$k]['vLicencePlateNo'] = $trip['vLicencePlate'];
                $tripsArr[$k]['dAddedDate'] = $trip['dAddedDate'];
                if ('Finished' === $trip['eTripStatus']) {
                    $tripsArr[$k]['TripStatus'] = $languageLabelsArr['LBL_TRACK_SERVICE_FINISHED_STATUS'];
                } else {
                    $tripsArr[$k]['TripStatus'] = $languageLabelsArr['LBL_TRACK_SERVICE_CANCELLED_STATUS'];
                }
            }
            $total = \count($tripsArr);
            $per_page = 10;
            $totalPages = ceil($total / $per_page);
            $start_limit = ($page - 1) * $per_page;
            $tripsArr = \array_slice($tripsArr, $start_limit, $per_page);
            $returnArr['Action'] = '1';
            $returnArr['message'] = $tripsArr;
            if ($totalPages > $page) {
                $returnArr['NextPage'] = ($page + 1);
            } else {
                $returnArr['NextPage'] = '0';
            }
        } else {
            $returnArr['Action'] = '0';
            $returnArr['message'] = 'LBL_TRACK_SERVICE_NO_TRIPS_FOUND';
        }
        $dSetupDate = $setupInfoDataArr[0]['dSetupDate'];
        $yearFromDate = date('Y-m-d 00:00:00', strtotime($dSetupDate));
        $yearToDate = date('Y-m-d 23:59:59', strtotime($vConvertFromDate));
        $getAllTrips = $obj->MySQLSelect("SELECT dAddedDate FROM track_service_trips WHERE (dAddedDate BETWEEN '{$yearFromDate}' AND '{$yearToDate}') AND iDriverId = '{$iDriverId}' AND eTripStatus IN ('Finished', 'Cancelled') ");
        $getAllTripsArr = [];
        foreach ($getAllTrips as $Trip) {
            $getAllTripsArr[] = date('Y-m-d', strtotime($Trip['dAddedDate']));
        }
        $returnArr['EARNING_DATA'] = getEarningDates($getAllTripsArr);
        setDataResponse($returnArr);
    }

    public function uploadImageForTrackingUser(): void
    {
        global $obj, $tconfig, $UPLOAD_OBJ;
        $iUserId = isset($_REQUEST['GeneralMemberId']) ? clean($_REQUEST['GeneralMemberId']) : '';
        $iMemberId = isset($_REQUEST['iTrackServiceUserId']) ? clean($_REQUEST['iTrackServiceUserId']) : '';
        $image_name = $vImage = $_FILES['vImage']['name'] ?? '';
        $image_object = $_FILES['vImage']['tmp_name'] ?? '';
        $check_file_query = 'SELECT iTrackServiceUserId, vImage from track_service_users where iTrackServiceUserId = '.$iMemberId;
        $check_file = $obj->sql_query($check_file_query);
        $img_path = $tconfig['tsite_upload_images_track_company_user_path'];
        if ('' !== $image_name) {
            $check_file['vImage'] = $img_path.'/'.$iMemberId.'/'.$check_file[0]['vImage'];
            if ('' !== $check_file['vImage'] && file_exists($check_file['vImage'])) {
                unlink($img_path.'/'.$iMemberId.'/'.$check_file[0]['vImage']);
                unlink($img_path.'/'.$iMemberId.'/1_'.$check_file[0]['vImage']);
                unlink($img_path.'/'.$iMemberId.'/2_'.$check_file[0]['vImage']);
                unlink($img_path.'/'.$iMemberId.'/3_'.$check_file[0]['vImage']);
            }
            $filecheck = basename($_FILES['vImage']['name']);
            $fileextarr = explode('.', $filecheck);
            $ext = strtolower($fileextarr[\count($fileextarr) - 1]);
            $flag_error = 0;
            if ('jpg' !== $ext && 'gif' !== $ext && 'png' !== $ext && 'jpeg' !== $ext && 'bmp' !== $ext && 'heic' !== $ext) {
                $returnArr['Action'] = '0';
                $returnArr['message'] = 'LBL_UPLOAD_IMG_ERROR';
                setDataResponse($returnArr);
            }
            $Photo_Gallery_folder = $img_path.'/'.$iMemberId.'/';
            if (!is_dir($Photo_Gallery_folder)) {
                mkdir($Photo_Gallery_folder, 0777);
                chmod($Photo_Gallery_folder, 0777);
            }
            $img1 = $UPLOAD_OBJ->GeneralUploadImage($image_object, $image_name, $Photo_Gallery_folder, '', '', '', '', '', '', 'Y', '', $Photo_Gallery_folder);
            if ('' !== $img1) {
                if (is_file($Photo_Gallery_folder.$img1)) {
                    include_once TPATH_CLASS.'/SimpleImage.class.php';
                    $img = new SimpleImage();
                    [$width, $height, $type, $attr] = getimagesize($Photo_Gallery_folder.$img1);
                    if ($width < $height) {
                        $final_width = $width;
                    } else {
                        $final_width = $height;
                    }
                    $img->load($Photo_Gallery_folder.$img1)->crop(0, 0, $final_width, $final_width)->save($Photo_Gallery_folder.$img1);
                    $img1 = $UPLOAD_OBJ->img_data_upload($Photo_Gallery_folder, $img1, $Photo_Gallery_folder, $tconfig['tsite_upload_images_member_size1'], $tconfig['tsite_upload_images_member_size2'], $tconfig['tsite_upload_images_member_size3'], '');
                }
            }
            $vImage = $img1;
        } else {
            $vImage = $check_file[0]['vImage'];
        }
        $obj->sql_query("UPDATE track_service_users SET vImage = '{$vImage}' WHERE iTrackServiceUserId = '{$iMemberId}' ");
        $returnArr['Action'] = '1';
        $returnArr['message'] = $this->listTrackingUsers($iMemberId);
        setDataResponse($returnArr);
    }

    public function listTrackingUsers($iTrackServiceUserId = 0)
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
        $tracking_users = $obj->MySQLSelect("SELECT tsu.iTrackServiceUserId, CONCAT(tsu.vName, ' ', tsu.vLastName) as userName, CONCAT('+', tsu.vPhoneCode, tsu.vPhone) as userPhone, tsu.vImage, tsu.vInviteCode, tsu.vLocation as userLocation, tsu.vAddress as userAddress, tsu.vLatitude as userLatitude, tsu.vLongitude as userLongitude, tsu.iDriverId, CONCAT(rd.vName, ' ', rd.vLastName) as driverName, CONCAT('+', rd.vCode, rd.vPhone) as driverPhone, rd.vLatitude as driverLatitude, rd.vLongitude as driverLongitude, dv.vLicencePlate as vLicencePlateNo, ma.vMake, mo.vTitle as vModel, tsc.vCompany, CONCAT('+', tsc.vCode, tsc.vPhone) as orgPhone, tsc.vLocation, tsc.vAddress, tsc.vLatitude as orgLatitude, tsc.vLongitude as orgLongitude FROM track_service_users as tsu LEFT JOIN register_driver as rd ON rd.iDriverId = tsu.iDriverId LEFT JOIN driver_vehicle as dv ON dv.iDriverVehicleId = rd.iDriverVehicleId LEFT JOIN make as ma ON ma.iMakeId = dv.iMakeId LEFT JOIN model as mo ON mo.iModelId = dv.iModelId LEFT JOIN track_service_company as tsc ON tsc.iTrackServiceCompanyId = tsu.iTrackServiceCompanyId WHERE tsu.iUserId = '{$iMemberId}' AND tsu.iDriverId > 0 AND tsu.eStatus = 'Active' {$ssql} ");
        if (!empty($tracking_users) && \count($tracking_users) > 0) {
            $userArr = [];
            foreach ($tracking_users as $k => $user) {
                $userArr[$k]['iTrackServiceUserId'] = $user['iTrackServiceUserId'];
                $userArr[$k]['userName'] = $user['userName'];
                $userArr[$k]['userPhone'] = $user['userPhone'];
                $userArr[$k]['userAddress'] = !empty($user['userAddress']) ? $user['userAddress'] : $user['userLocation'];
                $userArr[$k]['userLatitude'] = $user['userLatitude'];
                $userArr[$k]['userLongitude'] = $user['userLongitude'];
                $userArr[$k]['vInviteCode'] = $user['vInviteCode'];
                $userArr[$k]['iDriverId'] = $user['iDriverId'];
                $userArr[$k]['driverName'] = $user['driverName'];
                $userArr[$k]['driverPhone'] = $user['driverPhone'];
                $userArr[$k]['driverLatitude'] = $user['driverLatitude'];
                $userArr[$k]['driverLongitude'] = $user['driverLongitude'];
                $userArr[$k]['orgName'] = $user['vCompany'];
                $userArr[$k]['orgPhone'] = $user['orgPhone'];
                $userArr[$k]['orgAddress'] = !empty($user['vAddress']) ? $user['vAddress'] : $user['vLocation'];
                $userArr[$k]['orgLatitude'] = $user['orgLatitude'];
                $userArr[$k]['orgLongitude'] = $user['orgLongitude'];
                $userArr[$k]['vLicencePlateNo'] = $user['vLicencePlateNo'];
                $userArr[$k]['vMake'] = $user['vMake'];
                $userArr[$k]['vModel'] = $user['vModel'];
                $userArr[$k]['vImage'] = '';
                $userArr[$k]['userList'] = '';
                $image_path = $tconfig['tsite_upload_images_track_company_user_path'].'/'.$user['iTrackServiceUserId'].'/'.$user['vImage'];
                if (!empty($user['vImage']) && file_exists($image_path)) {
                    $userArr[$k]['vImage'] = $tconfig['tsite_upload_images_track_company_user'].'/'.$user['iTrackServiceUserId'].'/3_'.$user['vImage'];
                }
                $track_service_trip = $obj->MySQLSelect('SELECT iTrackServiceTripId, eTripStatus, eTripType FROM track_service_trips WHERE FIND_IN_SET('.$user['iTrackServiceUserId'].", iUserIds) AND eTripStatus NOT IN ('Finished', 'Cancelled') ORDER BY iTrackServiceTripId DESC LIMIT 1");
                $track_service_trip_last = $obj->MySQLSelect('SELECT iTrackServiceTripId, eTripStatus, eTripType FROM track_service_trips WHERE FIND_IN_SET('.$user['iTrackServiceUserId'].", iUserIds) AND eTripStatus IN ('Finished') ORDER BY iTrackServiceTripId DESC LIMIT 1");
                $userArr[$k]['TripStatus'] = '';
                $userArr[$k]['TripStatusDisplay'] = '';
                if (!empty($track_service_trip_last) && \count($track_service_trip_last) > 0) {
                    $userArr[$k]['TripStatus'] = '';
                    $userArr[$k]['TripStatusDisplay'] = $languageLabelsArr['LBL_TRACK_SERVICE_NOT_ACTIVE_STATUS'];
                }
                if (!empty($track_service_trip) && \count($track_service_trip) > 0) {
                    $userArr[$k]['iTrackServiceTripId'] = $track_service_trip[0]['iTrackServiceTripId'];
                    $eTripStatus = $track_service_trip[0]['eTripStatus'];
                    $eTripType = $track_service_trip[0]['eTripType'];
                    if ('ONBOARDING' === strtoupper($eTripStatus)) {
                        $userArr[$k]['TripStatus'] = 'Onboarding';
                        $userArr[$k]['TripStatusDisplay'] = $languageLabelsArr['LBL_TRACK_SERVICE_ONBOARDING_STATUS'];
                        if ('Dropoff' === $eTripType) {
                            $userArr[$k]['TripStatus'] = '';
                            $userArr[$k]['TripStatusDisplay'] = $languageLabelsArr['LBL_TRACK_SERVICE_NOT_ACTIVE_STATUS'];
                        }
                    } elseif ('ACTIVE' === strtoupper($eTripStatus)) {
                        if ('Pickup' === $eTripType) {
                            $userArr[$k]['TripStatus'] = '';
                            $userArr[$k]['TripStatusDisplay'] = $languageLabelsArr['LBL_TRACK_SERVICE_NOT_ACTIVE_STATUS'];
                        } else {
                            $userArr[$k]['TripStatus'] = 'Onboarding';
                            $userArr[$k]['TripStatusDisplay'] = $languageLabelsArr['LBL_TRACK_SERVICE_ONBOARDING_STATUS'];
                        }
                    } elseif ('ONGOINGTRIP' === strtoupper($eTripStatus)) {
                        $userArr[$k]['TripStatus'] = 'OnGoingTrip';
                        if ('Pickup' === $eTripType) {
                            $userArr[$k]['TripStatusDisplay'] = $languageLabelsArr['LBL_TRACK_SERVICE_PICKUP_STATUS'];
                        } else {
                            $userArr[$k]['TripStatusDisplay'] = $languageLabelsArr['LBL_TRACK_SERVICE_DROPOFF_STATUS'];
                        }
                    } else {
                        $userArr[$k]['TripStatus'] = '';
                        $userArr[$k]['TripStatusDisplay'] = $languageLabelsArr['LBL_TRACK_SERVICE_NOT_ACTIVE_STATUS'];
                    }
                    $track_service_users = $this->getDriverConnectedUsers($user['iDriverId']);
                    $userArr[$k]['userList'] = $track_service_users;
                }
            }
            if ($iTrackServiceUserId > 0) {
                return $userArr[0];
            }
            $returnArr['message'] = $userArr;
        } else {
            $returnArr['message'] = '';
        }
        if (isset($_REQUEST['type']) && 'getServiceCategoriesPro' === $_REQUEST['type']) {
            return $returnArr;
        }
        setDataResponse($returnArr);
    }

    public function GenerateInviteCode()
    {
        global $obj;
        $random = RandomString(10, 'Yes');
        $db_str = $obj->MySQLSelect("SELECT vInviteCode FROM track_service_users WHERE vInviteCode ='".$random."'");
        if (!empty($db_str) && \count($db_str) > 0) {
            $code = GenerateInviteCode();
        } else {
            $code = $random;
        }

        return $code;
    }
}
