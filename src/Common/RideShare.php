<?php



namespace Kesk\Web\Common;

class RideShare
{
    public function __construct() {}

    public function getCategories($tCategoryDetails = [])
    {
        global $languageLabelsArrRideShare, $tconfig, $obj, $master_service_category_tbl;
        if (empty($tCategoryDetails)) {
            $service_details = $obj->MySQLSelect("SELECT tCategoryDetails FROM {$master_service_category_tbl} WHERE eType = 'RideShare' ");
            $tCategoryDetails = json_decode($service_details[0]['tCategoryDetails'], true);
        }
        $vImageRideSharePublish = '';
        if (!empty($tCategoryDetails['RideSharePublish']['vImage']) && file_exists($tconfig['tsite_upload_app_home_screen_images_path'].$tCategoryDetails['RideSharePublish']['vImage'])) {
            $imagedata = getimagesize($tconfig['tsite_upload_app_home_screen_images_path'].$tCategoryDetails['RideSharePublish']['vImage']);
            $vImageWidthRideSharePublish = (string) $imagedata[0];
            $vImageHeightRideSharePublish = (string) $imagedata[1];
            $vImageRideSharePublish = $tconfig['tsite_upload_app_home_screen_images'].$tCategoryDetails['RideSharePublish']['vImage'];
        }
        $vImageRideShareBook = '';
        if (!empty($tCategoryDetails['RideShareBook']['vImage']) && file_exists($tconfig['tsite_upload_app_home_screen_images_path'].$tCategoryDetails['RideShareBook']['vImage'])) {
            $imagedata = getimagesize($tconfig['tsite_upload_app_home_screen_images_path'].$tCategoryDetails['RideShareBook']['vImage']);
            $vImageWidthRideShareBook = (string) $imagedata[0];
            $vImageHeightRideShareBook = (string) $imagedata[1];
            $vImageRideShareBook = $tconfig['tsite_upload_app_home_screen_images'].$tCategoryDetails['RideShareBook']['vImage'];
        }
        $vImageRideShareMyRides = '';
        if (!empty($tCategoryDetails['RideShareMyRides']['vImage']) && file_exists($tconfig['tsite_upload_app_home_screen_images_path'].$tCategoryDetails['RideShareMyRides']['vImage'])) {
            $imagedata = getimagesize($tconfig['tsite_upload_app_home_screen_images_path'].$tCategoryDetails['RideShareMyRides']['vImage']);
            $vImageWidthRideShareMyRides = (string) $imagedata[0];
            $vImageHeightRideShareMyRides = (string) $imagedata[1];
            $vImageRideShareMyRides = $tconfig['tsite_upload_app_home_screen_images'].$tCategoryDetails['RideShareMyRides']['vImage'];
        }
        $category_arr = [['vCategory' => $languageLabelsArrRideShare['LBL_RIDE_SHARE_PUBLISH_TXT'], 'vImage' => $vImageRideSharePublish, 'vListLogo' => $vImageRideSharePublish, 'vImageWidth' => $vImageWidthRideSharePublish, 'vImageHeight' => $vImageHeightRideSharePublish, 'eCatType' => 'RideSharePublish'], ['vCategory' => $languageLabelsArrRideShare['LBL_RIDE_SHARE_BOOK_TXT'], 'vImage' => $vImageRideShareBook, 'vListLogo' => $vImageRideShareBook, 'vImageWidth' => $vImageWidthRideShareBook, 'vImageHeight' => $vImageHeightRideShareBook, 'eCatType' => 'RideShareBook'], ['vCategory' => $languageLabelsArrRideShare['LBL_RIDE_SHARE_MY_RIDES_TXT'], 'vImage' => $vImageRideShareMyRides, 'vListLogo' => $vImageRideShareMyRides, 'vImageWidth' => $vImageWidthRideShareMyRides, 'vImageHeight' => $vImageHeightRideShareMyRides, 'eCatType' => 'RideShareMyRides']];

        return $category_arr;
    }

    public function getDriverDetailsFields($iPublishedRideId = 0, $vLang = '', $getData = 'No')
    {
        global $obj, $LANG_OBJ;
        if ('No' === $getData) {
            $vLang = $_REQUEST['vGeneralLang'] ?? '';
            $iPublishedRideId = $_REQUEST['iPublishedRideId'] ?? '';
            $iUserId = $_REQUEST['GeneralMemberId'] ?? '';
        }
        $userData = $obj->MySQLSelect("SELECT vPhone, vName, vLastName FROM register_user WHERE iUserId = '{$iUserId}' ");
        if (empty($vLang)) {
            $vLang = $LANG_OBJ->FetchDefaultLangData('vCode');
        }
        $tDriverDetails = [];
        if (isset($iPublishedRideId) && !empty($iPublishedRideId)) {
            $sql = "SELECT pr.tDriverDetails FROM published_rides as pr WHERE pr.iPublishedRideId = '".$iPublishedRideId."'";
            $data = $obj->MySQLSelect($sql);
            $tDriverDetails = json_decode($data[0]['tDriverDetails'], true);
            $tDriver_Details = [];
            foreach ($tDriverDetails as $Details) {
                $tDriver_Details[$Details['iData']] = $Details['value'];
            }
        }
        $fields = $obj->MySQLSelect("SELECT *, JSON_UNQUOTE(JSON_EXTRACT(tFieldName, '$.tFieldName_".$vLang."')) as tFieldName FROM ride_share_driver_fields WHERE eStatus = 'Active' ");
        $i = 0;
        foreach ($fields as $field) {
            if (isset($tDriver_Details) && !empty($tDriver_Details)) {
                $fields[$i]['value'] = $tDriver_Details[$field['iFieldId']];
            } else {
                if ('Name' === $field['eFor']) {
                    $fields[$i]['value'] = $userData[0]['vName'].' '.$userData[0]['vLastName'];
                } elseif ('Phone' === $field['eFor']) {
                    $fields[$i]['value'] = $userData[0]['vPhone'];
                }
            }
            ++$i;
        }
        if ('Yes' === $getData) {
            return $fields;
        }
        $returnArr['Action'] = '1';
        $returnArr['message'] = $fields;
        setDataResponse($returnArr);
    }

    public function getVerificationDocuments(): void
    {
        global $obj, $LANG_OBJ, $tconfig;
        $iUserId = $_REQUEST['GeneralMemberId'] ?? '';
        $tStartLat = $_REQUEST['tStartLat'] ?? '';
        $tStartLong = $_REQUEST['tStartLong'] ?? '';
        $tEndLat = $_REQUEST['tEndLat'] ?? '';
        $tEndLong = $_REQUEST['tEndLong'] ?? '';
        $dStartDate = $_REQUEST['dStartDate'] ?? '';
        $vLang = $_REQUEST['vGeneralLang'] ?? '';
        if (empty($vLang)) {
            $vLang = $LANG_OBJ->FetchDefaultLangData('vCode');
        }
        $tStartLocation = getLocationNameLatLog($tStartLat, $tStartLong, 'No');
        $tStartLocation = json_decode($tStartLocation, true);
        $tEndLocation = getLocationNameLatLog($tEndLat, $tEndLong, 'No');
        $tEndLocation = json_decode($tEndLocation, true);
        $StartAddress = $EndAddress = $StartCity = $EndCity = '';
        $UserData = get_value('register_user', 'vCountry,vLang', 'iUserId', $iUserId);
        $vCountry = $UserData[0]['vCountry'];
        if (!empty($tStartLocation)) {
            $address_components = $tStartLocation['results'][0]['address_components'];
            foreach ($address_components as $addr) {
                if ('locality' === $addr['types'][0]) {
                    $StartCity = $addr['long_name'];
                } elseif ('administrative_area_level_3' === $addr['types'][0]) {
                    $StartCity = $addr['long_name'];
                }
            }
            $StartAddress = $tStartLocation['results'][0]['formatted_address'];
        }
        if (!empty($tEndLocation)) {
            $address_components = $tEndLocation['results'][0]['address_components'];
            foreach ($address_components as $addr) {
                if ('locality' === $addr['types'][0]) {
                    $EndCity = $addr['long_name'];
                } elseif ('administrative_area_level_3' === $addr['types'][0]) {
                    $EndCity = $addr['long_name'];
                }
            }
            $EndAddress = $tEndLocation['results'][0]['formatted_address'];
        }
        $vLangCodeData = get_value('language_master', 'vCode, vGMapLangCode', 'eDefault', 'Yes');
        $vGMapLangCode = $vLangCodeData[0]['vGMapLangCode'];
        $requestDataArr = [];
        $requestDataArr['SOURCE_LATITUDE'] = $tStartLat;
        $requestDataArr['SOURCE_LONGITUDE'] = $tStartLong;
        $requestDataArr['DEST_LATITUDE'] = $tEndLat;
        $requestDataArr['DEST_LONGITUDE'] = $tEndLong;
        $requestDataArr['LANGUAGE_CODE'] = $vGMapLangCode;
        $direction_data = getPathInfoBetweenLocations($requestDataArr);
        $fDuration = $direction_data['duration'];
        $documents = $obj->MySQLSelect("SELECT rdl.ex_date,rdl.doc_file,rdl.doc_id,(SELECT CASE WHEN COUNT(*) > 0 THEN 'Yes' ELSE 'No' END as 'isUploaded' FROM `ride_share_document_list` WHERE doc_masterid = dm.doc_masterid AND doc_userid = {$iUserId}) as isUploaded,dm.doc_masterid, dm.ex_status, dm.doc_name_{$vLang} as doc_name FROM document_master as dm LEFT JOIN ride_share_document_list rdl ON (rdl.doc_masterid = dm.doc_masterid AND doc_userid = {$iUserId} ) WHERE (dm.country='".$vCountry."' OR dm.country='All') and dm.doc_usertype = 'user' AND dm.status = 'Active' ORDER BY iDisplayOrder ASC ");
        $img_path = $tconfig['tsite_upload_ride_share_documents'];
        $Photo_Gallery_folder = $img_path.'/'.$iUserId.'/';
        if (!empty($documents)) {
            $i = 0;
            foreach ($documents as $document) {
                if (empty($document['doc_id'])) {
                    $documents[$i]['doc_id'] = '';
                }
                if (empty($document['ex_date'])) {
                    $documents[$i]['ex_date'] = '';
                }
                if (empty($document['doc_file'])) {
                    $documents[$i]['doc_file'] = '';
                } else {
                    $documents[$i]['doc_file'] = $Photo_Gallery_folder.$document['doc_file'];
                    $documents[$i]['is_doc'] = 'No';
                    $doc_file_arr = explode('.', $document['doc_file']);
                    $doc_file_ext = strtolower($doc_file_arr[\count($doc_file_arr) - 1]);
                    $images_ext_arr = explode(',', $tconfig['tsite_upload_image_file_extensions']);
                    if (!\in_array($doc_file_ext, $images_ext_arr, true)) {
                        $documents[$i]['is_doc'] = 'Yes';
                    }
                }
                ++$i;
            }
        }
        $returnArr['Action'] = '1';
        $returnArr['message'] = $documents;
        $returnArr['StartCity'] = $StartCity;
        $returnArr['EndCity'] = $EndCity;
        $returnArr['StartAddress'] = $StartAddress;
        $returnArr['EndAddress'] = $EndAddress;
        $returnArr['StartDate'] = date('D d F', strtotime($dStartDate));
        $returnArr['StartDate'] = date('D ,jS M y', strtotime($dStartDate));
        $returnArr['StartTime'] = date('h:i A', strtotime($dStartDate));
        $returnArr['EndTime'] = date('h:i A', strtotime($dStartDate) + $fDuration);
        setDataResponse($returnArr);
    }

    public function publishRide(): void
    {
        global $obj, $tconfig, $Data_ALL_currency_Arr, $WALLET_OBJ, $CASH_AVAILABLE, $RIDE_SHARE_BOOKING_FEE, $LANG_OBJ, $COMM_MEDIA_OBJ, $UPLOAD_OBJ;
        $GeneralMemberId = $_REQUEST['GeneralMemberId'] ?? '';
        $tStartLat = $_REQUEST['tStartLat'] ?? '';
        $tStartLong = $_REQUEST['tStartLong'] ?? '';
        $tEndLat = $_REQUEST['tEndLat'] ?? '';
        $tEndLong = $_REQUEST['tEndLong'] ?? '';
        $dStartDate = $_REQUEST['dStartDate'] ?? '';
        $vTimeZone = $_REQUEST['vTimeZone'] ?? '';
        $iAvailableSeats = $_REQUEST['iAvailableSeats'] ?? '';
        $fPrice = $_REQUEST['fPrice'] ?? '';
        $tDriverDetails = $_REQUEST['tDriverDetails'] ?? '';
        $documentIds = $_REQUEST['documentIds'] ?? '';
        $StartCity = $_REQUEST['tStartCity'] ?? '';
        $EndCity = $_REQUEST['tEndCity'] ?? '';
        $vLang = $_REQUEST['vGeneralLang'] ?? '';
        $image_name = $vImage = $_FILES['vImage']['name'] ?? '';
        $image_object = $_FILES['vImage']['tmp_name'] ?? '';
        if (empty($vLang)) {
            $vLang = $LANG_OBJ->FetchDefaultLangData('vCode');
        }
        $langLabels = $LANG_OBJ->FetchLanguageLabels($vLang, '1', '');
        $systemTimeZone = date_default_timezone_get();
        if (!empty($vTimeZone)) {
            $dStartDate = converToTz($dStartDate, $systemTimeZone, $vTimeZone);
        }
        $userData = $obj->MySQLSelect("SELECT CONCAT(ru.vName, ' ', ru.vLastName) as PublisherName, CONCAT('+', ru.vPhoneCode, ru.vPhone) as PhoneNo, ru.vCurrencyPassenger, curr.Ratio FROM register_user as ru LEFT JOIN currency as curr ON ru.vCurrencyPassenger = curr.vName WHERE iUserId = '".$GeneralMemberId."' ");
        $priceRatio = $userData[0]['Ratio'];
        $vCurrencyPassenger = $userData[0]['vCurrencyPassenger'];
        if ('Yes' === $CASH_AVAILABLE) {
            $rider_available_balance = $WALLET_OBJ->FetchMemberWalletBalance($GeneralMemberId, 'Rider');
            $commission = $RIDE_SHARE_BOOKING_FEE * ($iAvailableSeats * ($fPrice / $priceRatio)) / 100;
            if ($commission > $rider_available_balance) {
                $commission = formateNumAsPerCurrency($commission * $priceRatio, $vCurrencyPassenger);
                $rider_available_balance = formateNumAsPerCurrency($rider_available_balance * $priceRatio, $vCurrencyPassenger);
                $returnArr['Action'] = '0';
                $returnArr['LOW_WALLET_BAL'] = 'Yes';
                $returnArr['message'] = str_replace(['#####', '####'], [$commission, $rider_available_balance], $langLabels['LBL_RIDE_SHARE_LOW_WALLET_BAL_NOTE_WITH_WALLET_AMT']);
                setDataResponse($returnArr);
            }
        }
        $tPriceRatioArr = [];
        if (!empty($Data_ALL_currency_Arr) && \count($Data_ALL_currency_Arr) > 0) {
            $currencyList = $Data_ALL_currency_Arr;
        } else {
            $currencyList = $obj->MySQLSelect('SELECT vName, Ratio, eStatus FROM currency ');
        }
        for ($c = 0; $c < \count($currencyList); ++$c) {
            $tPriceRatioArr['fRatio_'.$currencyList[$c]['vName']] = '1.0000';
            if ('ACTIVE' === strtoupper($currencyList[$c]['eStatus'])) {
                $tPriceRatioArr['fRatio_'.$currencyList[$c]['vName']] = $currencyList[$c]['Ratio'];
            }
        }
        $tDriverDetailsDB = '';
        if (!empty($tDriverDetails)) {
            $tDriverDetailsDB = preg_replace('/[[:cntrl:]]/', '\r\n', $tDriverDetails);
            $tDriverDetailsDB = json_decode($tDriverDetailsDB, true);
            if ('' !== $image_name) {
                $filecheck = basename($image_name);
                $fileextarr = explode('.', $filecheck);
                $ext = strtolower($fileextarr[\count($fileextarr) - 1]);
                if ('jpg' !== $ext && 'gif' !== $ext && 'png' !== $ext && 'jpeg' !== $ext && 'bmp' !== $ext) {
                    $var_msg = $langLabels['LBL_FILE_EXT_VALID_ERROR_MSG'].' .jpg, .jpeg, .gif, .png, .bmp';
                    $returnArr['Action'] = '0';
                    $returnArr['message'] = $var_msg;
                    setDataResponse($returnArr);
                }
                $Photo_Gallery_folder = $tconfig['tsite_upload_images_driver_car_ride_share_path'].'/';
                if (!is_dir($Photo_Gallery_folder)) {
                    mkdir($Photo_Gallery_folder, 0777);
                    chmod($Photo_Gallery_folder, 0777);
                }
                $img1 = $UPLOAD_OBJ->GeneralFileUpload($Photo_Gallery_folder, $image_object, $image_name, '', 'jpg,png,gif,jpeg,bmp');
                $vImgName = $img1[0];
                $image = ['iData' => 'cImage', 'value' => $vImgName];
                $tDriverDetailsDB[] = $image;
            }
            $tDriverDetailsDB_ = $tDriverDetailsDB;
            $tDriverDetailsDB = str_replace("'", getJsonFromAnArrWithoutClean($tDriverDetailsDB), "\\'");
        }
        $tStartLocation = getLocationNameLatLog($tStartLat, $tStartLong);
        $tEndLocation = getLocationNameLatLog($tEndLat, $tEndLong);
        $vLangCodeData = get_value('language_master', 'vCode, vGMapLangCode', 'eDefault', 'Yes');
        $vGMapLangCode = $vLangCodeData[0]['vGMapLangCode'];
        $requestDataArr = [];
        $requestDataArr['SOURCE_LATITUDE'] = $tStartLat;
        $requestDataArr['SOURCE_LONGITUDE'] = $tStartLong;
        $requestDataArr['DEST_LATITUDE'] = $tEndLat;
        $requestDataArr['DEST_LONGITUDE'] = $tEndLong;
        $requestDataArr['LANGUAGE_CODE'] = $vGMapLangCode;
        $direction_data = getPathInfoBetweenLocations($requestDataArr);
        $fDuration = $direction_data['duration'];
        $vPublishedRideNo = $this->GenerateUniquePublishedRideNo();
        $Data_publish = [];
        $Data_publish['vPublishedRideNo'] = $vPublishedRideNo;
        $Data_publish['iUserId'] = $GeneralMemberId;
        $Data_publish['dStartDate'] = $dStartDate;
        $Data_publish['dEndDate'] = date('Y-m-d H:i:s', strtotime($dStartDate) + $fDuration);
        $Data_publish['fDuration'] = $fDuration;
        $Data_publish['tStartLat'] = $tStartLat;
        $Data_publish['tStartLong'] = $tStartLong;
        $Data_publish['tEndLat'] = $tEndLat;
        $Data_publish['tEndLong'] = $tEndLong;
        $Data_publish['tStartLocation'] = $tStartLocation;
        $Data_publish['tEndLocation'] = $tEndLocation;
        $Data_publish['fPrice'] = $fPrice / $priceRatio;
        $Data_publish['tPriceRatio'] = json_encode($tPriceRatioArr, JSON_UNESCAPED_UNICODE);
        $Data_publish['iAvailableSeats'] = $iAvailableSeats;
        $Data_publish['dAddedDate'] = date('Y-m-d H:i:s');
        $Data_publish['tDriverDetails'] = $tDriverDetailsDB;
        $Data_publish['tDocumentIds'] = $documentIds;
        $Data_publish['eStatus'] = 'Active';
        $Data_publish['eVerified'] = 'No';
        $Data_publish['tStartCity'] = $StartCity;
        $Data_publish['tEndCity'] = $EndCity;
        $iPublishedRideId = $obj->MySQLQueryPerform('published_rides', $Data_publish, 'insert');
        $Data_Mail = [];
        $Data_Mail['vName'] = $userData[0]['PublisherName'];
        $Data_Mail['vPhone'] = $userData[0]['PhoneNo'];
        $Data_Mail['vSourceAddresss'] = $tStartLocation;
        $Data_Mail['vDestinationAddress'] = $tEndLocation;
        $Data_Mail['Date'] = date('d-m-Y h:i A', strtotime($dStartDate));
        $Data_Mail['Seats'] = $iAvailableSeats;
        $Data_Mail['PricePerSeat'] = formateNumAsPerCurrency($fPrice / $priceRatio, '');
        if (isset($tDriverDetailsDB_) && !empty($tDriverDetailsDB_)) {
            foreach ($tDriverDetailsDB_ as $Details) {
                $iData = $Details['iData'];
                if ('cImage' === $Details['iData']) {
                    $Details['value'] = $tconfig['tsite_upload_images_driver_car_ride_share'].'/'.$Details['value'];
                }
                ${$iData} = $Details['value'];
            }
        }
        $DriverDetailsHtml = '';
        if (isset($dName) && !empty($dName)) {
            $tFieldName = $langLabels['LBL_RIDE_SHARE_DRIVER_NAME_TXT'];
            $value = $dName;
            $DriverDetailsHtml .= '<strong>'.$tFieldName.': </strong>'.$value.'<br>';
        }
        if (isset($dPhoneCode) && !empty($dPhoneCode)) {
            $tFieldName = $langLabels['LBL_RIDE_SHARE_DRIVER_PHONE_NO_TXT'];
            $value = '+'.$dPhoneCode.$dPhone;
            $DriverDetailsHtml .= '<strong>'.$tFieldName.': </strong>'.$value.'<br>';
        }
        if (isset($cMake) && !empty($cMake)) {
            $tFieldName = $langLabels['LBL_RIDE_SHARE_CAR_DETAILS_TITLE'];
            $value = $cMake.' '.$cModel.' - '.$cNumberPlate;
            $DriverDetailsHtml .= '<strong>'.$tFieldName.': </strong>'.$value.'<br>';
        }
        if (isset($cNote) && !empty($cNote)) {
            $tFieldName = $langLabels['LBL_RIDE_SHARE_ADDITIONAL_NOTES_TXT'];
            $value = $cNote;
            $DriverDetailsHtml .= '<strong>'.$tFieldName.': </strong>'.$value.'<br>';
        }
        $Data_Mail['DriverDetails'] = $DriverDetailsHtml;
        $COMM_MEDIA_OBJ->SendMailToMember('RIDE_PUBLISHED_NOTIFY_ADMIN', $Data_Mail);
        $returnArr['Action'] = '1';
        $returnArr['message'] = 'LBL_RIDE_SHARE_PUBLISH_RIDE_SUCCESS_TEXT';
        $returnArr['message_title'] = 'LBL_RIDE_SHARE_PUBLISH_RIDE_SUCCESS_TITLE';
        setDataResponse($returnArr);
    }

    public function fetchPublishedRides(): void
    {
        global $obj, $LANG_OBJ;
        $iUserId = $_REQUEST['GeneralMemberId'] ?? '';
        $vLang = $_REQUEST['vGeneralLang'] ?? '';
        $iPublishedRideId = $_REQUEST['iPublishedRideId'] ?? '';
        $vTimeZone = $_REQUEST['vTimeZone'] ?? '';
        if (empty($vLang)) {
            $vLang = $LANG_OBJ->FetchDefaultLangData('vCode');
        }
        $langLabels = $LANG_OBJ->FetchLanguageLabels($vLang, '1', '');
        $page = isset($_REQUEST['page']) ? trim($_REQUEST['page']) : 1;
        $iPublishedRideId_sql = '';
        if (!empty($iPublishedRideId)) {
            $iPublishedRideId_sql = "AND pr.iPublishedRideId = '".$iPublishedRideId."'";
        }
        $currentDate = date('Y-m-d H:i:s');
        $serverTimeZone = date_default_timezone_get();
        $todaydate = converToTz($currentDate, $serverTimeZone, $vTimeZone, 'Y-m-d');
        $published_rides = $obj->MySQLSelect("SELECT pr.iPublishedRideId, pr.tDocumentIds, pr.tPriceRatio,pr.tStartCity, pr.tEndCity, pr.tStartLocation, pr.tStartLat, pr.tStartLong, pr.tEndLocation, pr.tEndLat, pr.tEndLong, pr.dStartDate, pr.dEndDate, pr.fPrice, pr.iAvailableSeats, pr.tDriverDetails, ru.vCurrencyPassenger, pr.fDuration, pr.vPublishedRideNo FROM published_rides as pr LEFT JOIN register_user as ru ON ru.iUserId = pr.iUserId WHERE pr.eStatus != 'Cancelled' {$iPublishedRideId_sql} AND pr.iUserId = '".$iUserId."' AND pr.dStartDate > '{$todaydate}' ORDER BY pr.dAddedDate DESC");
        if (!empty($published_rides) && \count($published_rides) > 0) {
            $ridesArr = [];
            foreach ($published_rides as $k => $ride) {
                $BookingListArr = $this->BookingList($vLang, $ride['iPublishedRideId'], [], 'PublishedRide');
                $tPriceRatio = $ride['tPriceRatio'];
                $tPriceRatio = json_decode($tPriceRatio, true);
                $vCurrencyPassenger = $ride['vCurrencyPassenger'];
                $fPrice = $ride['fPrice'] * $tPriceRatio['fRatio_'.$vCurrencyPassenger];
                $ridesArr[$k]['tStartLocation'] = $ride['tStartLocation'];
                $ridesArr[$k]['tStartLat'] = $ride['tStartLat'];
                $ridesArr[$k]['tStartLong'] = $ride['tStartLong'];
                $ridesArr[$k]['tEndLocation'] = $ride['tEndLocation'];
                $ridesArr[$k]['tEndLat'] = $ride['tEndLat'];
                $ridesArr[$k]['tEndLong'] = $ride['tEndLong'];
                $ridesArr[$k]['StartDate'] = DateTime(converToTz($ride['dStartDate'], $vTimeZone, $serverTimeZone), 22);
                $ridesArr[$k]['StartTime'] = date('h:i A', strtotime(converToTz($ride['dStartDate'], $vTimeZone, $serverTimeZone)));
                $ridesArr[$k]['EndTime'] = date('h:i A', strtotime(converToTz($ride['dEndDate'], $vTimeZone, $serverTimeZone)));
                $ridesArr[$k]['fDuration'] = $this->convertSecToMin($ride['fDuration']);
                $ridesArr[$k]['fPrice'] = formateNumAsPerCurrency($fPrice, $vCurrencyPassenger);
                $ridesArr[$k]['PriceLabel'] = $langLabels['LBL_RIDE_SHARE_PER_PASSENGER_TXT'];
                $ridesArr[$k]['AvailableSeats'] = $ride['iAvailableSeats'];
                $ridesArr[$k]['tDriverDetails'] = $ride['tDriverDetails'];
                $ridesArr[$k]['carDetails'] = $this->carDetails($ride['tDriverDetails']);
                $ridesArr[$k]['tStartCity'] = $ride['tStartCity'];
                $ridesArr[$k]['tEndCity'] = $ride['tEndCity'];
                $ridesArr[$k]['iPublishedRideId'] = $ride['iPublishedRideId'];
                $ridesArr[$k]['vPublishedRideNo'] = $ride['vPublishedRideNo'];
                $ridesArr[$k]['vPublishedRideNoTxt'] = $langLabels['LBL_RIDE_SHARE_PUBLISH_NO'].' #'.$ride['vPublishedRideNo'];
                $ridesArr[$k]['tDocumentIds'] = $this->userUploadedDocument($vLang, $iUserId);
                $ridesArr[$k]['BookingList'] = $BookingListArr;
                $ridesArr[$k]['eJobType'] = 'RideSharing';
            }
            $total = \count($ridesArr);
            $per_page = 10;
            $totalPages = ceil($total / $per_page);
            $start_limit = ($page - 1) * $per_page;
            $ridesArr = \array_slice($ridesArr, $start_limit, $per_page);
            $returnArr['Action'] = '1';
            $returnArr['message'] = $ridesArr;
            if ($totalPages > $page) {
                $returnArr['NextPage'] = ($page + 1);
            } else {
                $returnArr['NextPage'] = '0';
            }
        } else {
            $returnArr['Action'] = '0';
            $returnArr['message'] = 'LBL_RIDE_SHARE_NO_PUBLISHED_RIDES_FOUND_TXT';
            $returnArr['message_title'] = 'LBL_RIDE_SHARE_NO_PUBLISHED_RIDES_FOUND_TITLE';
        }
        setDataResponse($returnArr);
    }

    public function carDetails($tDriverDetails)
    {
        global $tconfig;
        $tDriverDetails = json_decode($tDriverDetails, true);
        $return = [];
        $cMake = '';
        if (isset($tDriverDetails) && !empty($tDriverDetails)) {
            foreach ($tDriverDetails as $Details) {
                $iData = $Details['iData'];
                if ('cNote' === $Details['iData'] || 'cMake' === $Details['iData'] || 'cModel' === $Details['iData'] || 'cImage' === $Details['iData'] || 'cNumberPlate' === $Details['iData'] || 'cColor' === $Details['iData']) {
                    if ('cImage' === $Details['iData']) {
                        $Details['value'] = $tconfig['tsite_upload_images_driver_car_ride_share'].'/'.$Details['value'];
                    }
                    ${$iData} = $Details['value'];
                }
            }
        }
        $return = ['cNote' => $cNote, 'cModel' => $cModel.' - '.$cColor, 'cMake' => $cMake, 'cNumberPlate' => $cNumberPlate, 'cImage' => $cImage];

        return $return;
    }

    public function BookingList($vLang, $iPublishedRideId, $notAllowedUserId, $type)
    {
        global $obj, $tconfig, $LANG_OBJ, $currencyAssociateArr;
        $langLabels = $LANG_OBJ->FetchLanguageLabels($vLang, '1', '');
        $ssql = '';
        if ('SearchRide' === $type || 'Booking' === $type) {
            $ssql .= " AND rsb.eStatus = 'Approved' ";
        }
        $sql = "SELECT CONCAT(riderDriver.vName,' ',riderDriver.vLastName) AS driver_Name, CONCAT(riderUser.vName,' ',riderUser.vLastName) AS rider_Name, CONCAT('+', riderUser.vPhoneCode,riderUser.vPhone) AS rider_Phone, riderUser.vImgName as rider_ProfileImg, riderUser.iUserId as rider_iUserId, riderDriver.iUserId as driver_iUserId, riderDriver.vCurrencyPassenger, pr.tStartLocation,pr.tStartLat,pr.tStartLong,pr.tEndLocation,pr.tEndLat,pr.tEndLong,pr.dStartDate,pr.dStartDate,pr.dEndDate,pr.tPriceRatio, pr.tEndCity,pr.tStartCity, pr.iAvailableSeats, rsb.iPublishedRideId,rsb.eStatus,rsb.fTotal,rsb.iBookedSeats,pr.tDriverDetails,rsb.iBookingId,rsb.iCancelReasonId,rsb.tCancelReason, rsb.vBookingNo, rsb.fTotal, rsb.fBookingFee, rsb.fPricePerSeat, rsb.fWalletDebit, rsb.ePaymentOption FROM ride_share_bookings rsb LEFT JOIN published_rides pr ON (pr.iPublishedRideId = rsb.iPublishedRideId) LEFT JOIN register_user riderUser ON (riderUser.iUserId = rsb.iUserId) LEFT JOIN register_user riderDriver ON (riderDriver.iUserId = pr.iUserId) WHERE pr.iPublishedRideId = {$iPublishedRideId} {$ssql} ORDER BY rsb.eStatus ASC";
        $rideShareBookings = $obj->MySQLSelect($sql);
        $bookingArr = [];
        if (isset($rideShareBookings) && !empty($rideShareBookings)) {
            $i = 0;
            foreach ($rideShareBookings as $booking) {
                if (\in_array($booking['rider_iUserId'], $notAllowedUserId, true)) {
                    continue;
                }
                $profile = '';
                if (isset($booking['rider_ProfileImg']) && !empty($booking['rider_ProfileImg'])) {
                    if (file_exists($tconfig['tsite_upload_images_passenger_path'].'/'.$booking['rider_iUserId'].'/3_'.$booking['rider_ProfileImg'])) {
                        $profile = $tconfig['tsite_upload_images_passenger'].'/'.$booking['rider_iUserId'].'/3_'.$booking['rider_ProfileImg'];
                    }
                }
                $bookingArr[$i]['driver_Name'] = $booking['driver_Name'];
                $bookingArr[$i]['rider_Name'] = $booking['rider_Name'];
                $bookingArr[$i]['rider_Phone'] = $booking['rider_Phone'];
                $bookingArr[$i]['rider_ProfileImg'] = $profile;
                $bookingArr[$i]['eStatus'] = $booking['eStatus'];
                $bookingArr[$i]['iBookingId'] = $booking['iBookingId'];
                $bookingArr[$i]['vBookingNo'] = $booking['vBookingNo'];
                $bookingArr[$i]['vBookingNoTxt'] = $langLabels['LBL_RIDE_SHARE_BOOKING_NO'].' #'.$booking['vBookingNo'];
                $bookingArr[$i]['tEndCity'] = $booking['tEndCity'];
                $bookingArr[$i]['tStartCity'] = $booking['tStartCity'];
                $bookingArr[$i]['rider_iUserId'] = $booking['rider_iUserId'];
                $bookingArr[$i]['iAvailableSeats'] = $booking['iAvailableSeats'];
                $bookingArr[$i]['iBookedSeats'] = $booking['iBookedSeats'];
                $bookingArr[$i]['BookedSeatsTxt'] = str_replace('#SEATS#', $booking['iBookedSeats'], $langLabels['LBL_RIDE_SHARE_BOOKED_PASSENGERS_TXT']);
                if ('PublishedRide' === $type) {
                    if ('Pending' === $booking['eStatus']) {
                        $statusMessage = $langLabels['LBL_RIDE_SHARE_PENDING_MESSAGE'];
                    } elseif ('Approved' === $booking['eStatus']) {
                        $statusMessage = str_replace('#USERNAME#', $booking['rider_Name'], $langLabels['LBL_RIDE_SHARE_APPROVED_MESSAGE']);
                    } elseif ('Declined' === $booking['eStatus']) {
                        $statusMessage = str_replace('#USERNAME#', $booking['rider_Name'], $langLabels['LBL_RIDE_SHARE_DECLINE_MESSAGE']);
                        if (!empty($booking['iCancelReasonId'])) {
                            $vCancelReason = get_value('cancel_reason', 'vTitle_'.$vLang, 'iCancelReasonId', $booking['iCancelReasonId'], '', 'true');
                        } else {
                            $vCancelReason = $booking['tCancelReason'];
                        }
                        $bookingArr[$i]['DeclineReason'] = $vCancelReason;
                    } elseif ('Cancelled' === $booking['eStatus']) {
                        $statusMessage = str_replace('#USERNAME#', $booking['rider_Name'], $langLabels['LBL_RIDE_SHARE_CANCEL_MESSAGE']);
                        if (!empty($booking['iCancelReasonId'])) {
                            $vCancelReason = get_value('cancel_reason', 'vTitle_'.$vLang, 'iCancelReasonId', $booking['iCancelReasonId'], '', 'true');
                        } else {
                            $vCancelReason = $booking['tCancelReason'];
                        }
                        $bookingArr[$i]['DeclineReason'] = $vCancelReason;
                    }
                    $bookingArr[$i]['statusMessage'] = $statusMessage;
                }
                $priceRatio = $currencyAssociateArr[$booking['vCurrencyPassenger']]['Ratio'];
                $fPricePerSeatTotal = $booking['fPricePerSeat'] * $booking['iBookedSeats'] * $priceRatio;
                $fBookingFee = $booking['fBookingFee'] * $priceRatio;
                $fWalletDebit = $booking['fWalletDebit'] * $priceRatio;
                $fTotalAmount = ($booking['fTotal'] - $booking['fWalletDebit']) * $priceRatio;
                if ('Cash' === $booking['ePaymentOption']) {
                    $bookingArr[$i]['PaymentLabel'] = $langLabels['LBL_RIDE_SHARE_PASSENGER_PAY_IN_CASH'];
                    $bookingArr[$i]['PaymentModeLabel'] = $langLabels['LBL_CASH_TXT'];
                } elseif ('Card' === $booking['ePaymentOption']) {
                    $bookingArr[$i]['PaymentLabel'] = $langLabels['LBL_RIDE_SHARE_PASSENGER_PAY_CARD'];
                    $bookingArr[$i]['PaymentModeLabel'] = $langLabels['LBL_CARD'];
                } else {
                    $bookingArr[$i]['PaymentLabel'] = $langLabels['LBL_RIDE_SHARE_PASSENGER_PAY_WALLET'];
                    $bookingArr[$i]['PaymentModeLabel'] = $langLabels['LBL_WALLET_TXT'];
                }
                $bookingArr[$i]['PaymentModeTitle'] = $langLabels['LBL_PAYMENT_MODE_TXT'];
                $bookingArr[$i]['PriceBreakdown'] = '';
                $bookingArr[$i]['TotalFare'] = formateNumAsPerCurrency($fTotalAmount, $booking['vCurrencyPassenger']);
                $bookingArr[$i]['PriceBreakdownTitle'] = $langLabels['LBL_FARE_BREAKDOWN_TXT'];
                $bookingArr[$i]['PaymentNote'] = '';
                $arrindex = 0;
                $priceBreakdownArr = [];
                $priceBreakdownArr[$arrindex][$langLabels['LBL_RIDE_SHARE_PRICE_PER_SEAT_TOTAL_TXT'].' (X '.$booking['iBookedSeats'].')'] = formateNumAsPerCurrency($fPricePerSeatTotal, $booking['vCurrencyPassenger']);
                ++$arrindex;
                $priceBreakdownArr[$arrindex][$langLabels['LBL_RIDE_SHARE_BOOKING_FEES_TXT']] = formateNumAsPerCurrency($fBookingFee, $booking['vCurrencyPassenger']);
                ++$arrindex;
                if ($booking['fWalletDebit'] > 0) {
                    $priceBreakdownArr[$arrindex][$langLabels['LBL_WALLET_ADJUSTMENT']] = '-'.formateNumAsPerCurrency($fWalletDebit, $booking['vCurrencyPassenger']);
                    ++$arrindex;
                }
                $priceBreakdownArr[$arrindex]['eDisplaySeperator'] = 'Yes';
                ++$arrindex;
                if ('Cash' === $booking['ePaymentOption']) {
                    $priceBreakdownArr[$arrindex][$langLabels['LBL_RIDE_SHARE_COLLECT_FROM_USER_TXT']] = formateNumAsPerCurrency($fTotalAmount, $booking['vCurrencyPassenger']);
                    ++$arrindex;
                } else {
                    $bookingArr[$i]['PaymentNote'] = $langLabels['LBL_RIDE_SHARE_PAYMENT_NOTE'];
                    $priceBreakdownArr[$arrindex][$langLabels['LBL_RIDE_SHARE_TOTAL_PRICE']] = formateNumAsPerCurrency($fTotalAmount, $booking['vCurrencyPassenger']);
                    ++$arrindex;
                }
                $bookingArr[$i]['PriceBreakdown'] = $priceBreakdownArr;
                $bookingArr[$i]['eShowCallImg'] = 'No';
                if ('Approved' === $booking['eStatus']) {
                    $bookingArr[$i]['eShowCallImg'] = 'Yes';
                }
                ++$i;
            }
        }

        return $bookingArr;
    }

    public function userUploadedDocument($vLang, $iUserId)
    {
        global $obj, $tconfig;
        $documents = $obj->MySQLSelect("SELECT rdl.ex_date,rdl.doc_file,rdl.doc_id,dm.doc_masterid, dm.ex_status, dm.doc_name_{$vLang} as doc_name FROM ride_share_document_list rdl LEFT JOIN document_master as dm ON (rdl.doc_masterid = dm.doc_masterid) WHERE dm.doc_usertype = 'user' AND rdl.doc_userid = {$iUserId} AND dm.status = 'Active' ORDER BY iDisplayOrder ASC ");
        $img_path = $tconfig['tsite_upload_ride_share_documents'];
        $Photo_Gallery_folder = $img_path.'/'.$iUserId.'/';
        if (!empty($documents)) {
            $i = 0;
            foreach ($documents as $document) {
                if (empty($document['doc_id'])) {
                    $documents[$i]['doc_id'] = '';
                }
                if (empty($document['ex_date'])) {
                    $documents[$i]['ex_date'] = '';
                }
                if (empty($document['doc_file'])) {
                    $documents[$i]['doc_file'] = '';
                } else {
                    $documents[$i]['doc_file'] = $Photo_Gallery_folder.$document['doc_file'];
                }
                ++$i;
            }
        }

        return $documents;
    }

    public function searchRide(): void
    {
        global $obj, $LIST_RIDES_LIMIT_BY_SOURCE_DISTANCE, $LIST_RIDES_LIMIT_BY_DEST_DISTANCE, $LANG_OBJ, $tconfig, $RIDE_SHARE_BOOKING_FEE;
        $iUserId = $_REQUEST['GeneralMemberId'] ?? '';
        $tStartLat = $_REQUEST['tStartLat'] ?? '';
        $tStartLong = $_REQUEST['tStartLong'] ?? '';
        $tEndLat = $_REQUEST['tEndLat'] ?? '';
        $tEndLong = $_REQUEST['tEndLong'] ?? '';
        $dStartDate = $_REQUEST['dStartDate'] ?? '';
        $vTimeZone = $_REQUEST['vTimeZone'] ?? '';
        $NoOfSeats = $_REQUEST['NoOfSeats'] ?? '1';
        $vFilterParam = $_REQUEST['vFilterParam'] ?? '';
        $vLangCode = $_REQUEST['vGeneralLang'] ?? '1';
        $dStartDate = date('Y-m-d', strtotime($dStartDate));
        $serverTimeZone = date_default_timezone_get();
        $currentDate = date('Y-m-d H:i:s');
        $startTime = $dStartDate.' 00:00:00';
        $endTime = $dStartDate.' 23:59:00';
        $startTimeServer = converToTz($startTime, $serverTimeZone, $vTimeZone);
        $endTimeeServer = converToTz($endTime, $serverTimeZone, $vTimeZone);
        $sql_cur = " pr.dStartDate BETWEEN '".$startTimeServer."' AND '".$endTimeeServer."'";
        if ($dStartDate === date('Y-m-d')) {
            $sql_cur = $sql_cur." AND pr.dStartDate >= '".$currentDate."'";
        }
        if ('' === $vLangCode || null === $vLangCode) {
            $vLangCode = $LANG_OBJ->FetchDefaultLangData('vCode');
        }
        $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLangCode, '1', '');
        $page = isset($_REQUEST['page']) ? trim($_REQUEST['page']) : 1;
        $order_param = ' dStartDate ASC ';
        if (!empty($vFilterParam)) {
            if ('TIME' === strtoupper($vFilterParam)) {
                $order_param = ' pr.dStartDate ASC ';
            } elseif ('DURATION' === strtoupper($vFilterParam)) {
                $order_param = ' pr.fDuration ASC ';
            } elseif ('PRICELOW' === strtoupper($vFilterParam)) {
                $order_param = ' pr.fPrice ASC ';
            } elseif ('PRICEHIGH' === strtoupper($vFilterParam)) {
                $order_param = ' pr.fPrice DESC ';
            } elseif ('RATING' === strtoupper($vFilterParam)) {
            }
        }
        $userData = $obj->MySQLSelect("SELECT vCurrencyPassenger FROM register_user WHERE iUserId = '{$iUserId}' ");
        $vCurrencyPassenger = $userData[0]['vCurrencyPassenger'];
        $serverTimeZone = date_default_timezone_get();
        $searchSql = "SELECT pr.*, rd.iUserId as iDriverId, CONCAT(rd.vName,' ',rd.vLastName) AS DriverName, CONCAT('+',rd.vPhoneCode,rd.vPhone) AS DriverPhone, rd.iUserId as DriveriUserId , rd.vImgName as DrivervImgName, ROUND(( 6371 * acos( cos( radians(".$tStartLat.') ) * cos( radians( ROUND(pr.tStartLat, 8) ) ) * cos( radians( ROUND(pr.tStartLong, 8) ) - radians('.$tStartLong.') ) + sin( radians('.$tStartLat.') ) * sin( radians( ROUND(pr.tStartLat, 8) ) ) ) ), 2) AS startDistance, ROUND(( 6371 * acos( cos( radians('.$tEndLat.') ) * cos( radians( ROUND(pr.tEndLat, 8) ) ) * cos( radians( ROUND(pr.tEndLong, 8) ) - radians('.$tEndLong.') ) + sin( radians('.$tEndLat.") ) * sin( radians( ROUND(pr.tEndLat, 8) ) ) ) ), 2) AS endDistance FROM published_rides pr LEFT JOIN register_user rd ON (rd.iUserId = pr.iUserId) WHERE {$sql_cur} AND pr.iUserId != {$iUserId} AND pr.eStatus = 'Active' AND (pr.iAvailableSeats - pr.iBookedSeats) >= {$NoOfSeats} HAVING startDistance <= ".$LIST_RIDES_LIMIT_BY_SOURCE_DISTANCE.' AND endDistance <= '.$LIST_RIDES_LIMIT_BY_DEST_DISTANCE." ORDER BY {$order_param} ";
        $published_rides = $obj->MySQLSelect($searchSql);
        $ridesArr = [];
        foreach ($published_rides as $k => $ride) {
            $profile = '';
            if (isset($ride['DrivervImgName']) && !empty($ride['DrivervImgName'])) {
                if (file_exists($tconfig['tsite_upload_images_passenger_path'].'/'.$ride['DriveriUserId'].'/3_'.$ride['DrivervImgName'])) {
                    $profile = $tconfig['tsite_upload_images_passenger'].'/'.$ride['DriveriUserId'].'/3_'.$ride['DrivervImgName'];
                }
            }
            $tPriceRatio = $ride['tPriceRatio'];
            $tPriceRatio = json_decode($tPriceRatio, true);
            $BookingListArr = $this->BookingList($vLangCode, $ride['iPublishedRideId'], [], 'SearchRide');
            $fPrice = $NoOfSeats * $ride['fPrice'];
            $commission = 0;
            $fPrice = ($fPrice + $commission) * $tPriceRatio['fRatio_'.$vCurrencyPassenger];
            $iAvailableSeats = str_replace('#NUMBER#', $ride['iAvailableSeats'], $languageLabelsArr['LBL_RIDE_SHARE_NUMBER_MAX_BOOK_SET']);
            $vNoOfPassenger = str_replace('#NUMBER#', $NoOfSeats, $languageLabelsArr['LBL_RIDE_SHARE_FOR_NO_PASSENGER']);
            $ridesArr[$k]['tStartLocation'] = $ride['tStartLocation'];
            $ridesArr[$k]['tStartLat'] = $ride['tStartLat'];
            $ridesArr[$k]['tStartLong'] = $ride['tStartLong'];
            $ridesArr[$k]['tEndLocation'] = $ride['tEndLocation'];
            $ridesArr[$k]['tEndLat'] = $ride['tEndLat'];
            $ridesArr[$k]['tEndLong'] = $ride['tEndLong'];
            $ridesArr[$k]['fDuration'] = $this->convertSecToMin($ride['fDuration']);
            $ridesArr[$k]['StartDate'] = DateTime(converToTz($ride['dStartDate'], $vTimeZone, $serverTimeZone), 22);
            $ridesArr[$k]['StartTime'] = date('h:i A', strtotime(converToTz($ride['dStartDate'], $vTimeZone, $serverTimeZone)));
            $ridesArr[$k]['EndTime'] = date('h:i A', strtotime(converToTz($ride['dEndDate'], $vTimeZone, $serverTimeZone)));
            $ridesArr[$k]['fPrice'] = formateNumAsPerCurrency($fPrice, $vCurrencyPassenger);
            $ridesArr[$k]['PriceLabel'] = $languageLabelsArr['LBL_RIDE_SHARE_PER_PASSENGER_TXT'];
            $ridesArr[$k]['AvailableSeats'] = $ride['iAvailableSeats'];
            $ridesArr[$k]['tDriverDetails'] = $ride['tDriverDetails'];
            $ridesArr[$k]['carDetails'] = $this->carDetails($ride['tDriverDetails']);
            $ridesArr[$k]['iPublishedRideId'] = $ride['iPublishedRideId'];
            $ridesArr[$k]['DriverName'] = $ride['DriverName'];
            $ridesArr[$k]['DriverPhone'] = $ride['DriverPhone'];
            $ridesArr[$k]['iDriverId'] = $ride['iDriverId'];
            $ridesArr[$k]['DriverRating'] = 0;
            $ridesArr[$k]['DriverImg'] = $profile;
            $ridesArr[$k]['tStartCity'] = $ride['tStartCity'];
            $ridesArr[$k]['tEndCity'] = $ride['tEndCity'];
            $ridesArr[$k]['iAvailableSeatsText'] = $iAvailableSeats;
            $ridesArr[$k]['vNoOfPassengerText'] = $vNoOfPassenger;
            $ridesArr[$k]['vPublishedRideNo'] = $ride['vPublishedRideNo'];
            $ridesArr[$k]['vPublishedRideNoTxt'] = $languageLabelsArr['LBL_RIDE_SHARE_PUBLISH_NO'].' #'.$ride['vPublishedRideNo'];
            $ridesArr[$k]['BookingList'] = $BookingListArr;
            $ridesArr[$k]['isBookedByOthers'] = !empty($BookingListArr) && \count($BookingListArr) > 0 ? 'Yes' : 'No';
        }
        $total = \count($ridesArr);
        $per_page = 20;
        $totalPages = ceil($total / $per_page);
        $start_limit = ($page - 1) * $per_page;
        $ridesArr = \array_slice($ridesArr, $start_limit, $per_page);
        if ($totalPages > $page) {
            $returnArr['NextPage'] = ($page + 1);
        } else {
            $returnArr['NextPage'] = '0';
        }
        if (\count($ridesArr) > 0) {
            $returnArr['Action'] = '1';
            $returnArr['message'] = $ridesArr;
        } else {
            $returnArr['Action'] = '0';
            $returnArr['message'] = 'LBL_RIDE_SHARE_RIDES_NOT_FOUND';
            $returnArr['message_title'] = 'LBL_RIDE_SHARE_RIDES_NOT_FOUND_TITLE';
        }
        setDataResponse($returnArr);
    }

    public function convertSecToMin($seconds)
    {
        $minutes = floor($seconds / 60);

        return convertMinToHoursToDays($minutes, 'Minutes', 1);
    }

    public function bookRide(): void
    {
        global $obj, $LANG_OBJ, $RIDE_SHARE_BOOKING_FEE, $EVENT_MSG_OBJ, $tconfig, $WALLET_OBJ, $COMM_MEDIA_OBJ, $ePayWallet;
        $iUserId = $_REQUEST['GeneralMemberId'] ?? '';
        $iBookNoOfSeats = $_REQUEST['iBookNoOfSeats'] ?? '0';
        $iPublishedRideId = $_REQUEST['iPublishedRideId'] ?? '0';
        $isFareAuthorized = $_REQUEST['isFareAuthorized'] ?? 'No';
        $iAuthorizePaymentId = $_REQUEST['iAuthorizePaymentId'] ?? '';
        $vLangCode = $_REQUEST['vGeneralLang'] ?? '';
        $eWalletIgnore = $_REQUEST['eWalletIgnore'] ?? 'No';
        if ('' === $vLangCode || null === $vLangCode) {
            $vLangCode = $LANG_OBJ->FetchDefaultLangData('vCode');
        }
        $userData = $obj->MySQLSelect("SELECT CONCAT(ru.vName, ' ', ru.vLastName) as RiderName, CONCAT('+', ru.vPhoneCode, ru.vPhone) as RiderPhone, ru.tSessionId, ru.vCurrencyPassenger, curr.Ratio FROM register_user as ru LEFT JOIN currency as curr ON ru.vCurrencyPassenger = curr.vName WHERE iUserId = '".$iUserId."' ");
        $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLangCode, '1', '');
        $user_available_balance = $WALLET_OBJ->FetchMemberWalletBalance($iUserId, 'Rider');
        $PublishRideData = $this->getPublishRideById($iPublishedRideId)[0];
        $params = ['iMemberId' => $iUserId, 'eUserType' => 'Passenger', 'eType' => 'RideShare', 'GET_DATA' => 'Yes'];
        $payment_mode_data = GetPaymentModeDetails($params);
        $ePaymentMode = !empty($payment_mode_data['PaymentMode']) ? $payment_mode_data['PaymentMode'] : 'cash';
        $cashPayment = 'cash' === $ePaymentMode ? 'Yes' : 'No';
        $ePayWallet = 'wallet' === $ePaymentMode ? 'Yes' : 'No';
        $eWalletDebitAllow = 'wallet' === $ePaymentMode ? 'Yes' : ('Yes' === $payment_mode_data['eWalletDebit'] ? 'Yes' : 'No');
        $isRestrictToWallet = $payment_mode_data['PAYMENT_MODE_RESTRICT_TO_WALLET'];
        $iPaymentInfoId = $payment_mode_data['iPaymentInfoId'];
        $sql = "SELECT iBookingId, vBookingNo FROM ride_share_bookings rsb WHERE iUserId = '".$iUserId."' AND rsb.iPublishedRideId = '".$iPublishedRideId."' AND eStatus = 'Pending' LIMIT 1";
        $ride_share_bookings = $obj->MySQLSelect($sql);
        $allowed = $PublishRideData['iAvailableSeats'] - $PublishRideData['iBookedSeats'];
        if ($allowed >= $iBookNoOfSeats) {
            $fBookingFee = $RIDE_SHARE_BOOKING_FEE * ($PublishRideData['fPrice'] * $iBookNoOfSeats) / 100;
            $tPriceRatio = $PublishRideData['tPriceRatio'];
            $tPriceRatio = json_decode($tPriceRatio, true);
            $UserData = get_value('register_user', 'vCurrencyPassenger', 'iUserId', $iUserId);
            $vCurrencyPassenger = $UserData[0]['vCurrencyPassenger'];
            $fTotal = ($PublishRideData['fPrice'] * $iBookNoOfSeats) + $fBookingFee;
            if ('card' === $ePaymentMode && 'No' === $isFareAuthorized) {
                $returnArr['Action'] = '1';
                $returnArr['WebviewPayment'] = 'Yes';
                $returnArr['message'] = $tconfig['tsite_url'].'assets/libraries/webview/system_payment.php?PAGE_TYPE=AUTHORIZE_TRIP_AMOUNT&SYSTEM_TYPE=APP&GeneralMemberId='.$iUserId.'&tSessionId='.$userData[0]['tSessionId'].'&GeneralUserType=Passenger&AMOUNT='.$fTotal;
                $tEstimatedFareUser = formateNumAsPerCurrency($fTotal * $tPriceRatio['fRatio_'.$vCurrencyPassenger], $userData[0]['vCurrencyPassenger']);
                $returnArr['message1'] = str_replace('#AUTHORIZE_AMOUNT#', $tEstimatedFareUser, $languageLabelsArr['LBL_RIDE_SHARE_AUTHORIZE_AMOUNT_MSG']);
                setDataResponse($returnArr);
            }
            $user_available_balance_wallet = $WALLET_OBJ->FetchMemberWalletBalance($iUserId, 'Rider', true);
            $walletDataArr = [];
            if (\is_array($user_available_balance_wallet)) {
                $walletDataArr = $user_available_balance_wallet;
                $user_available_balance_wallet = $walletDataArr['CurrentBalance'];
            }
            $ratio = $userData[0]['Ratio'];
            $currency_vSymbol = $userData[0]['vSymbol'];
            $currencycode = $userData[0]['vCurrencyPassenger'];
            $user_available_balance_wallet = setTwoDecimalPoint($user_available_balance_wallet * $ratio);
            if ('wallet' === $ePaymentMode && $user_available_balance_wallet < $fTotal && 'No' === $eWalletIgnore) {
                $returnArr['Action'] = '0';
                $returnArr['message'] = 'LOW_WALLET_AMOUNT';
                $auth_wallet_amount = 0;
                $returnArr['ORIGINAL_WALLET_BALANCE'] = formateNumAsPerCurrency(0, $currencycode);
                $returnArr['ORIGINAL_WALLET_BALANCE_VALUE'] = setTwoDecimalPoint(0);
                if (!empty($walletDataArr) && \count($walletDataArr) > 0) {
                    $auth_wallet_amount = (string) setTwoDecimalPoint(($walletDataArr['TotalAuthorizedAmount'] ?? 0) * $ratio);
                    $returnArr['AUTH_AMOUNT'] = $auth_wallet_amount > 0 ? formateNumAsPerCurrency($auth_wallet_amount, $currencycode) : '';
                    $returnArr['AUTH_AMOUNT_VALUE'] = $auth_wallet_amount > 0 ? setTwoDecimalPoint($auth_wallet_amount) : '';
                    $returnArr['ORIGINAL_WALLET_BALANCE'] = isset($walletDataArr['WalletBalance']) ? formateNumAsPerCurrency($walletDataArr['WalletBalance'] * $ratio, $currencycode) : formateNumAsPerCurrency(0, $currencycode);
                    $returnArr['ORIGINAL_WALLET_BALANCE_VALUE'] = (string) setTwoDecimalPoint((isset($walletDataArr['WalletBalance']) ? setTwoDecimalPoint($walletDataArr['WalletBalance']) : 0) * $ratio);
                }
                $returnArr['CURRENT_JOB_EST_CHARGE'] = formateNumAsPerCurrency($fTotal, $currencycode);
                $returnArr['CURRENT_JOB_EST_CHARGE_VALUE'] = (string) setTwoDecimalPoint($fTotal);
                $returnArr['WALLET_AMOUNT_NEEDED'] = formateNumAsPerCurrency($fTotal - $user_available_balance_wallet, $currencycode);
                $returnArr['WALLET_AMOUNT_NEEDED_VALUE'] = (string) setTwoDecimalPoint($fTotal - $user_available_balance_wallet);
                if (!empty($walletDataArr) && \count($walletDataArr) > 0 && $auth_wallet_amount > 0) {
                    $content_msg_low_balance = $languageLabelsArr['LBL_LOW_WALLET_BAL_NOTE_WITH_AUTH_AMT'];
                    if ('YES' === strtoupper($ePayWallet) && 'YES' === strtoupper($isRestrictToWallet)) {
                        $content_msg_low_balance = $languageLabelsArr['LBL_LOW_WALLET_BAL_NOTE_WITH_AUTH_AMT_NO_CASH'];
                    }
                    $content_msg_low_balance = str_replace('#####', $returnArr['WALLET_AMOUNT_NEEDED'], $content_msg_low_balance);
                    if (!empty($returnArr['ORIGINAL_WALLET_BALANCE'])) {
                        $content_msg_low_balance = str_replace('####', $returnArr['ORIGINAL_WALLET_BALANCE'], $content_msg_low_balance);
                    }
                    if (!empty($returnArr['AUTH_AMOUNT'])) {
                        $content_msg_low_balance = str_replace('###', $returnArr['AUTH_AMOUNT'], $content_msg_low_balance);
                    }
                    $content_msg_low_balance = str_replace('##', "\n\n", $content_msg_low_balance);
                    $returnArr['low_balance_content_msg'] = $content_msg_low_balance;
                } else {
                    $content_msg_low_balance = $languageLabelsArr['LBL_LOW_WALLET_BAL_NOTE_WITH_AMT'];
                    if ('YES' === strtoupper($ePayWallet) && 'YES' === strtoupper($isRestrictToWallet)) {
                        $content_msg_low_balance = $languageLabelsArr['LBL_LOW_WALLET_BAL_NOTE_WITH_AMT_NO_CASH'];
                    }
                    $content_msg_low_balance = str_replace('#####', $returnArr['WALLET_AMOUNT_NEEDED'], $content_msg_low_balance);
                    if (!empty($returnArr['ORIGINAL_WALLET_BALANCE'])) {
                        $content_msg_low_balance = str_replace('####', $returnArr['ORIGINAL_WALLET_BALANCE'], $content_msg_low_balance);
                    }
                    if (!empty($returnArr['CURRENT_JOB_EST_CHARGE'])) {
                        $content_msg_low_balance = str_replace('###', $returnArr['CURRENT_JOB_EST_CHARGE'], $content_msg_low_balance);
                    }
                    $content_msg_low_balance = str_replace('##', "\n\n", $content_msg_low_balance);
                    $returnArr['low_balance_content_msg'] = $content_msg_low_balance;
                }
                if ('YES' === strtoupper($ePayWallet) && 'YES' === strtoupper($isRestrictToWallet)) {
                    $returnArr['IS_RESTRICT_TO_WALLET_AMOUNT'] = 'Yes';
                } else {
                    $returnArr['IS_RESTRICT_TO_WALLET_AMOUNT'] = 'No';
                }
                setDataResponse($returnArr);
            }
            $vBookingNo = $this->GenerateUniqueBookingNo();
            $Update_ride_share = [];
            $Update_ride_share['iUserId'] = $iUserId;
            $Update_ride_share['iPublishedRideId'] = $iPublishedRideId;
            $Update_ride_share['iBookedSeats'] = $iBookNoOfSeats;
            $Update_ride_share['dBookingDate'] = date('Y-m-d H:i:s');
            $Update_ride_share['fPricePerSeat'] = $PublishRideData['fPrice'];
            $Update_ride_share['fTotal'] = $fTotal;
            $Update_ride_share['fBookingFee'] = $fBookingFee;
            $Update_ride_share['ePaymentOption'] = ucfirst($ePaymentMode);
            $Update_ride_share['ePaid'] = 'No';
            $Update_ride_share['eStatus'] = 'Pending';
            if ('Yes' === $isFareAuthorized) {
                $Update_ride_share['iAuthorizePaymentId'] = $iAuthorizePaymentId;
            }
            if ('card' === $ePaymentMode) {
                $Update_ride_share['iPaymentInfoId'] = $iPaymentInfoId;
            } elseif ('wallet' === $ePaymentMode || 'Yes' === $eWalletDebitAllow) {
                $fWalletDebit = 0;
                if ($user_available_balance > 0) {
                    if ($fTotal > $user_available_balance) {
                        $fWalletDebit = $user_available_balance;
                    } else {
                        $fWalletDebit = $fTotal;
                    }
                }
                $Update_ride_share['fWalletDebit'] = $fWalletDebit;
                $Update_ride_share['ePayWallet'] = $fWalletDebit > 0 ? 'Yes' : 'No';
                $Update_ride_share['tUserWalletBalance'] = $walletDataArr['AutorizedWalletBalance'];
            }
            $Update_ride_share['vBookingNo'] = $vBookingNo;
            $id = $obj->MySQLQueryPerform('ride_share_bookings', $Update_ride_share, 'insert');
            $row1 = $obj->MySQLSelect("SELECT eHmsDevice , iUserId, vCurrencyPassenger, vLang, eDeviceType, iGcmRegId, eAppTerminate, eDebugMode, vName, vLastName FROM `register_user` WHERE iUserId = '".$PublishRideData['iUserId']."'");
            $generalDataArr = $final_message = [];
            $final_message = $this->fetchPublishedRidesNotification($iPublishedRideId, $id);
            $alertMsg = $languageLabelsArr['LBL_RIDE_SHARE_NEW_BOOKING'];
            $generalDataArr[] = ['eDeviceType' => $row1[0]['eDeviceType'], 'deviceToken' => $row1[0]['iGcmRegId'], 'alertMsg' => $alertMsg, 'eAppTerminate' => $row1[0]['eAppTerminate'], 'eDebugMode' => $row1[0]['eDebugMode'], 'message' => $final_message, 'eHmsDevice' => $row1['eHmsDevice'], 'channelName' => 'PASSENGER_'.$PublishRideData['iUserId']];
            $EVENT_MSG_OBJ->send(['GENERAL_DATA' => $generalDataArr], RN_USER);
            $Data_Mail = [];
            $Data_Mail['vEmail'] = $PublishRideData['vName'];
            $Data_Mail['vPublisherName'] = $PublishRideData['vName'];
            $Data_Mail['vName'] = $userData[0]['RiderName'];
            $Data_Mail['vPhone'] = $userData[0]['RiderPhone'];
            $Data_Mail['vBookingNo'] = $vBookingNo;
            $Data_Mail['Seats'] = $iBookNoOfSeats;
            $COMM_MEDIA_OBJ->SendMailToMember('RIDE_SHARE_BOOKING_NOTIFY_PUBLISHER', $Data_Mail);
            $Data_SMS = [];
            $Data_SMS['vPublisherName'] = $PublishRideData['vName'];
            $message_layout = $COMM_MEDIA_OBJ->GetSMSTemplate('RIDE_SHARE_BOOKING_NOTIFY_PUBLISHER', $Data_SMS, '', $vLangCode);
            $COMM_MEDIA_OBJ->SendSystemSMS($PublishRideData['vPhone'], $PublishRideData['vPhoneCode'], $message_layout);
            $returnArr['Action'] = '1';
            $returnArr['message'] = 'LBL_RIDE_SHARE_WAIT_FOR_THE_APPROVAL_TXT';
            $returnArr['message_title'] = 'LBL_RIDE_SHARE_WAIT_FOR_THE_APPROVAL_TITLE';
        } else {
            $message = str_replace(['#REQUESTED_SITE#', '#AVAILABLE_SITES#'], [$iBookNoOfSeats, $allowed], $languageLabelsArr['LBL_RIDE_SHARE_NOT_AVAILABLE_SITES_TXT']);
            $returnArr['Action'] = '0';
            $returnArr['message'] = $message;
        }
        setDataResponse($returnArr);
    }

    public function getPublishRideById($iPublishedRideId)
    {
        global $obj;
        $sql = "SELECT ru.vName, CONCAT(ru.vName, ' ', ru.vLastName) as PublisherName, ru.vEmail, ru.vPhone, ru.vPhoneCode, pr.iUserId,pr.tPriceRatio,pr.iBookedSeats,pr.fPrice,pr.tStartLocation, pr.tStartLat, pr.tStartLong, pr.tEndLocation, pr.tEndLat, pr.tEndLong, pr.dStartDate, pr.dEndDate, pr.fPrice, pr.iAvailableSeats, pr.tDriverDetails, ru.vCurrencyPassenger FROM published_rides as pr LEFT JOIN register_user as ru ON ru.iUserId = pr.iUserId WHERE pr.iPublishedRideId = '".$iPublishedRideId."' ORDER BY pr.dAddedDate DESC";

        return $obj->MySQLSelect($sql);
    }

    public function GenerateUniqueBookingNo()
    {
        global $obj, $tconfig;
        $random = substr(number_format(time() * random_int(0, getrandmax()), 0, '', ''), 0, 10);
        $str = "select iBookingId from ride_share_bookings where vBookingNo ='".$random."'";
        $db_str = $obj->MySQLSelect($str);
        if (!empty($db_str) && \count($db_str) > 0) {
            $Generateuniqueorderno = GenerateUniqueBookingNo();
        } else {
            $Generateuniqueorderno = $random;
        }

        return $Generateuniqueorderno;
    }

    public function GenerateUniquePublishedRideNo()
    {
        global $obj, $tconfig;
        $random = substr(number_format(time() * random_int(0, getrandmax()), 0, '', ''), 0, 10);
        $str = "select iPublishedRideId from published_rides where vPublishedRideNo ='".$random."'";
        $db_str = $obj->MySQLSelect($str);
        if (!empty($db_str) && \count($db_str) > 0) {
            $Generateuniqueno = GenerateUniquePublishedRideNo();
        } else {
            $Generateuniqueno = $random;
        }

        return $Generateuniqueno;
    }

    public function fetchPublishedRidesNotification($iPublishedRideId, $iBookingId)
    {
        global $obj, $LANG_OBJ, $tconfig;
        $vLang = $_REQUEST['vGeneralLang'] ?? '';
        if (empty($vLang)) {
            $vLang = $LANG_OBJ->FetchDefaultLangData('vCode');
        }
        $sql = "SELECT ru.tSessionId AS DrivertSessionId, CONCAT(ru.vName,' ',ru.vLastName) AS DriverName, ru.iUserId as DriveriUserId , ru.vImgName as DrivervImgName ,pr.iPublishedRideId, pr.tDocumentIds, pr.tPriceRatio,pr.tStartCity, pr.tEndCity, pr.tStartLocation, pr.tStartLat, pr.tStartLong, pr.tEndLocation, pr.tEndLat, pr.tEndLong, pr.dStartDate, pr.dEndDate, pr.fPrice, pr.iAvailableSeats, pr.tDriverDetails, ru.vCurrencyPassenger FROM published_rides as pr LEFT JOIN register_user as ru ON ru.iUserId = pr.iUserId WHERE pr.eStatus != 'Cancelled' AND pr.iPublishedRideId = '".$iPublishedRideId."' ORDER BY pr.dAddedDate DESC";
        $ride = $obj->MySQLSelect($sql);
        $ride = $ride[0];
        $sql = "SELECT iBookingId,iBookedSeats FROM ride_share_bookings rsb WHERE rsb.iBookingId = '".$iBookingId."' LIMIT 1";
        $ride_share_bookings = $obj->MySQLSelect($sql);
        $ride_share_bookings = $ride_share_bookings[0];
        $profile = '';
        if (isset($ride['DrivervImgName']) && !empty($ride['DrivervImgName'])) {
            if (file_exists($tconfig['tsite_upload_images_passenger_path'].'/'.$ride['DriveriUserId'].'/3_'.$ride['DrivervImgName'])) {
                $profile = $tconfig['tsite_upload_images_passenger'].'/'.$ride['DriveriUserId'].'/3_'.$ride['DrivervImgName'];
            }
        }
        $tPriceRatio = $ride['tPriceRatio'];
        $tPriceRatio = json_decode($tPriceRatio, true);
        $vCurrencyPassenger = $ride['vCurrencyPassenger'];
        $fPrice = $ride['fPrice'] * $tPriceRatio['fRatio_'.$vCurrencyPassenger];
        $fPrice *= $ride_share_bookings['iBookedSeats'];
        $ridesArr['tStartLocation'] = $ride['tStartLocation'];
        $ridesArr['tStartLat'] = $ride['tStartLat'];
        $ridesArr['tStartLong'] = $ride['tStartLong'];
        $ridesArr['tEndLocation'] = $ride['tEndLocation'];
        $ridesArr['tEndLat'] = $ride['tEndLat'];
        $ridesArr['tEndLong'] = $ride['tEndLong'];
        $ridesArr['StartDate'] = date('D d F', strtotime($ride['dStartDate']));
        $ridesArr['StartTime'] = date('h:i A', strtotime($ride['dStartDate']));
        $ridesArr['EndTime'] = date('h:i A', strtotime($ride['dEndDate']));
        $ridesArr['AvailableSeats'] = $ride['iAvailableSeats'];
        $ridesArr['tDriverDetails'] = $ride['tDriverDetails'];
        $ridesArr['tStartCity'] = $ride['tStartCity'];
        $ridesArr['tEndCity'] = $ride['tEndCity'];
        $ridesArr['tEndCity'] = $ride['tEndCity'];
        $ridesArr['iPublishedRideId'] = $ride['iPublishedRideId'];
        $ridesArr['fPrice'] = formateNumAsPerCurrency($fPrice, $vCurrencyPassenger);
        $ridesArr['NoOfSeatTxt'] = $languageLabelsArr['LBL_RIDE_SHARE_FOR_TXT'].' '.$ride_share_bookings['iBookedSeats'].' '.$languageLabelsArr['LBL_RIDE_SHARE_PRICE_SEAT_TXT'];
        $ridesArr['DriverName'] = $ride['DriverName'];
        $ridesArr['DriverRating'] = 0;
        $ridesArr['DriverImg'] = $profile;
        $ridesArr['iBookingId'] = $iBookingId;
        $ridesArr['iPublishedRideId'] = $iPublishedRideId;
        $ridesArr['eJobType'] = 'RideSharing';
        $Arr['tSessionId'] = $ride['DrivertSessionId'];
        $Arr['Message'] = 'RiderShareBooking';
        $Arr['MsgType'] = 'RiderShareBooking';
        $Arr['time'] = time();
        $Arr['notiData'] = $ridesArr;
        $Arr['eType'] = 'RideShare';

        return $Arr;
    }

    public function fetchBookings(): void
    {
        global $obj, $tconfig, $LANG_OBJ;
        $iUserId = $_REQUEST['GeneralMemberId'] ?? '';
        $vLangCode = $_REQUEST['vGeneralLang'] ?? '';
        $vTimeZone = $_REQUEST['vTimeZone'] ?? '';
        $page = isset($_REQUEST['page']) ? trim($_REQUEST['page']) : 1;
        if ('' === $vLangCode || null === $vLangCode) {
            $vLangCode = $LANG_OBJ->FetchDefaultLangData('vCode');
        }
        $serverTimeZone = date_default_timezone_get();
        $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLangCode, '1', '');
        $sql = "SELECT riderDriver.vName as driver_Name, riderUser.vName as rider_Name, CONCAT(riderDriver.vName,' ',riderDriver.vLastName) AS DriverName, CONCAT('+',riderDriver.vPhoneCode,riderDriver.vPhone) AS DriverPhone, riderDriver.iUserId as DriveriUserId , riderDriver.vImgName as DrivervImgName, pr.fDuration,pr.fPrice , pr.tStartCity ,pr.tEndCity , pr.iAvailableSeats,pr.tStartLocation,pr.tStartLat,pr.tStartLong,pr.tEndLocation,pr.tEndLat,pr.tEndLong,pr.dStartDate,pr.dStartDate,pr.dEndDate,pr.tPriceRatio, rsb.iPublishedRideId,rsb.eStatus,rsb.fTotal,rsb.iBookedSeats,pr.tDriverDetails,rsb.fBookingFee,rsb.ePaymentOption,riderDriver.iUserId as iDriverId, rsb.iBookingId, rsb.tCancelReason, rsb.iCancelReasonId, cr.vTitle_{$vLangCode} as vCancelReason, rsb.vBookingNo, rsb.fWalletDebit, rsb.fPricePerSeat FROM ride_share_bookings rsb LEFT JOIN published_rides pr ON (pr.iPublishedRideId = rsb.iPublishedRideId) LEFT JOIN register_user riderUser ON (riderUser.iUserId = rsb.iUserId) LEFT JOIN register_user riderDriver ON (riderDriver.iUserId = pr.iUserId) LEFT JOIN cancel_reason cr ON (cr.iCancelReasonId = rsb.iCancelReasonId) WHERE rsb.iUserId = {$iUserId} ORDER BY rsb.dBookingDate DESC";
        $rideShareBookings = $obj->MySQLSelect($sql);
        if (!empty($rideShareBookings) && \count($rideShareBookings) > 0) {
            $userData = $obj->MySQLSelect("SELECT vCurrencyPassenger FROM register_user WHERE iUserId = '{$iUserId}' ");
            $vCurrencyPassenger = $userData[0]['vCurrencyPassenger'];
            $ridesArr = [];
            foreach ($rideShareBookings as $k => $ride) {
                $profile = '';
                if (isset($ride['DrivervImgName']) && !empty($ride['DrivervImgName'])) {
                    if (file_exists($tconfig['tsite_upload_images_passenger_path'].'/'.$ride['DriveriUserId'].'/3_'.$ride['DrivervImgName'])) {
                        $profile = $tconfig['tsite_upload_images_passenger'].'/'.$ride['DriveriUserId'].'/3_'.$ride['DrivervImgName'];
                    }
                }
                $tPriceRatio = json_decode($ride['tPriceRatio'], true);
                $fTotal = $ride['fTotal'] * $tPriceRatio['fRatio_'.$vCurrencyPassenger];
                $iAvailableSeats = str_replace('#NUMBER#', $ride['iAvailableSeats'], $languageLabelsArr['LBL_RIDE_SHARE_NUMBER_MAX_BOOK_SET']);
                $vNoOfPassenger = str_replace('#NUMBER#', $ride['iBookedSeats'], $languageLabelsArr['LBL_RIDE_SHARE_FOR_NO_PASSENGER']);
                $BookingListArr = $this->BookingList($vLangCode, $ride['iPublishedRideId'], [$iUserId], 'Booking');
                $ridesArr[$k]['tDriverDetails'] = $ride['tDriverDetails'];
                $ridesArr[$k]['carDetails'] = $this->carDetails($ride['tDriverDetails']);
                $ridesArr[$k]['tStartLocation'] = $ride['tStartLocation'];
                $ridesArr[$k]['tStartLat'] = $ride['tStartLat'];
                $ridesArr[$k]['tStartLong'] = $ride['tStartLong'];
                $ridesArr[$k]['tEndLocation'] = $ride['tEndLocation'];
                $ridesArr[$k]['tEndLat'] = $ride['tEndLat'];
                $ridesArr[$k]['tEndLong'] = $ride['tEndLong'];
                $ridesArr[$k]['StartDate'] = DateTime($ride['dStartDate'], 22);
                $ridesArr[$k]['StartTime'] = date('h:i A', strtotime($ride['dStartDate']));
                $ridesArr[$k]['EndTime'] = date('h:i A', strtotime($ride['dEndDate']));
                $ridesArr[$k]['StartDate'] = DateTime(converToTz($ride['dStartDate'], $vTimeZone, $serverTimeZone), 22);
                $ridesArr[$k]['StartTime'] = date('h:i A', strtotime(converToTz($ride['dStartDate'], $vTimeZone, $serverTimeZone)));
                $ridesArr[$k]['EndTime'] = date('h:i A', strtotime(converToTz($ride['dEndDate'], $vTimeZone, $serverTimeZone)));
                $ridesArr[$k]['fPrice'] = formateNumAsPerCurrency($fTotal, $vCurrencyPassenger);
                $ridesArr[$k]['PriceLabel'] = $languageLabelsArr['LBL_RIDE_SHARE_PER_PASSENGER_TXT'];
                $ridesArr[$k]['iPublishedRideId'] = $ride['iPublishedRideId'];
                $ridesArr[$k]['vBookingNo'] = $ride['vBookingNo'];
                $ridesArr[$k]['eStatus'] = $ride['eStatus'];
                $ridesArr[$k]['AvailableSeats'] = $ride['iAvailableSeats'];
                $ridesArr[$k]['DriverName'] = $ride['driver_Name'];
                $ridesArr[$k]['DriverPhone'] = $ride['DriverPhone'];
                $ridesArr[$k]['iDriverId'] = $ride['iDriverId'];
                $ridesArr[$k]['DriverRating'] = 0;
                $ridesArr[$k]['DriverImg'] = $profile;
                $ridesArr[$k]['tStartCity'] = $ride['tStartCity'];
                $ridesArr[$k]['tEndCity'] = $ride['tEndCity'];
                $ridesArr[$k]['iAvailableSeatsText'] = $iAvailableSeats;
                $ridesArr[$k]['vNoOfPassengerText'] = $vNoOfPassenger;
                $ridesArr[$k]['fDuration'] = $this->convertSecToMin($ride['fDuration']);
                $ridesArr[$k]['BookingList'] = $BookingListArr;
                $ridesArr[$k]['isBookedByOthers'] = !empty($BookingListArr) && \count($BookingListArr) > 0 ? 'Yes' : 'No';
                $ridesArr[$k]['vBookingNoTxt'] = $languageLabelsArr['LBL_RIDE_SHARE_BOOKING_NO'].' #'.$ride['vBookingNo'];
                $ridesArr[$k]['vStatusBgColor'] = RANDOM_COLORS_ARR[array_rand(RANDOM_COLORS_ARR, 1)];
                if ('Cash' === $ride['ePaymentOption']) {
                    $ridesArr[$k]['PaymentText'] = $languageLabelsArr['LBL_RIDE_SHARE_PAY_CASH_TXT'];
                    $ridesArr[$k]['PaymentModeLabel'] = $languageLabelsArr['LBL_CASH_TXT'];
                } elseif ('Card' === $ride['ePaymentOption']) {
                    $ridesArr[$k]['PaymentText'] = $languageLabelsArr['LBL_RIDE_SHARE_PAY_CARD_TXT'];
                    $ridesArr[$k]['PaymentModeLabel'] = $languageLabelsArr['LBL_CARD'];
                } else {
                    $ridesArr[$k]['PaymentText'] = $languageLabelsArr['LBL_RIDE_SHARE_PAY_WALLET_TXT'];
                    $ridesArr[$k]['PaymentModeLabel'] = $languageLabelsArr['LBL_WALLET_TXT'];
                }
                $ridesArr[$k]['iBookingId'] = $ride['iBookingId'];
                $ridesArr[$k]['PaymentModeTitle'] = $languageLabelsArr['LBL_PAYMENT_MODE_TXT'];
                $ridesArr[$k]['PriceBreakdown'] = '';
                $ridesArr[$k]['eShowCallImg'] = 'No';
                if ('Pending' === $ride['eStatus']) {
                    $ridesArr[$k]['eStatusText'] = $languageLabelsArr['LBL_RIDE_SHARE_PENDING_STATUS_TXT'];
                } elseif ('Approved' === $ride['eStatus']) {
                    $ridesArr[$k]['eStatusText'] = $languageLabelsArr['LBL_RIDE_SHARE_ACCEPT_STATUS_TXT'];
                } elseif ('Declined' === $ride['eStatus']) {
                    $ridesArr[$k]['eStatusText'] = $languageLabelsArr['LBL_RIDE_SHARE_DECLINE_STATUS_TXT'];
                    $ridesArr[$k]['tCancelReason'] = $ride['iCancelReasonId'] > 0 ? $ride['vCancelReason'] : $ride['tCancelReason'];
                    $ridesArr[$k]['PaymentText'] = $languageLabelsArr['LBL_RIDE_SHARE_DECLINE_STATUS_TXT'];
                    $ridesArr[$k]['vNoOfPassengerText'] = '';
                } else {
                    $ridesArr[$k]['eStatusText'] = $languageLabelsArr['LBL_RIDE_SHARE_CANCEL_STATUS_TXT'];
                    $ridesArr[$k]['tCancelReason'] = $ride['iCancelReasonId'] > 0 ? $ride['vCancelReason'] : $ride['tCancelReason'];
                    $ridesArr[$k]['PaymentText'] = $languageLabelsArr['LBL_RIDE_SHARE_CANCEL_STATUS_TXT'];
                    $ridesArr[$k]['vNoOfPassengerText'] = '';
                }
                $ridesArr[$k]['PriceBreakdownTitle'] = $languageLabelsArr['LBL_PYMENT_DETAILS'];
                $ridesArr[$k]['PaymentNote'] = '';
                $arrindex = 0;
                $priceBreakdownArr = [];
                $fPricePerSeat = $ride['fPricePerSeat'] * $tPriceRatio['fRatio_'.$vCurrencyPassenger];
                $fPricePerSeatTotal = $ride['fPricePerSeat'] * $ride['iBookedSeats'] * $tPriceRatio['fRatio_'.$vCurrencyPassenger];
                $fTotalAmount = ($ride['fTotal'] - $ride['fWalletDebit']) * $tPriceRatio['fRatio_'.$vCurrencyPassenger];
                $fBookingFee = $ride['fBookingFee'] * $tPriceRatio['fRatio_'.$vCurrencyPassenger];
                $fWalletDebit = $ride['fWalletDebit'] * $tPriceRatio['fRatio_'.$vCurrencyPassenger];
                $priceBreakdownArr[$arrindex][$ride['iBookedSeats'].' '.$languageLabelsArr['LBL_RIDE_SHARE_PRICE_SEAT_TXT'].' X '.formateNumAsPerCurrency($fPricePerSeat, $vCurrencyPassenger)] = formateNumAsPerCurrency($fPricePerSeatTotal, $ride['vCurrencyPassenger']);
                ++$arrindex;
                $priceBreakdownArr[$arrindex][$languageLabelsArr['LBL_RIDE_SHARE_BOOKING_FEES_TXT']] = formateNumAsPerCurrency($fBookingFee, $vCurrencyPassenger);
                ++$arrindex;
                if ($ride['fWalletDebit'] > 0) {
                    $priceBreakdownArr[$arrindex][$languageLabelsArr['LBL_WALLET_ADJUSTMENT']] = '-'.formateNumAsPerCurrency($fWalletDebit, $vCurrencyPassenger);
                    ++$arrindex;
                }
                $priceBreakdownArr[$arrindex]['eDisplaySeperator'] = 'Yes';
                ++$arrindex;
                $priceBreakdownArr[$arrindex][$languageLabelsArr['LBL_RIDE_SHARE_TOTAL_PRICE']] = formateNumAsPerCurrency($fTotalAmount, $ride['vCurrencyPassenger']);
                ++$arrindex;
                $ridesArr[$k]['PriceBreakdown'] = $priceBreakdownArr;
                $ridesArr[$k]['eJobType'] = 'RideSharing';
            }
            $total = \count($ridesArr);
            $per_page = 10;
            $totalPages = ceil($total / $per_page);
            $start_limit = ($page - 1) * $per_page;
            $ridesArr = \array_slice($ridesArr, $start_limit, $per_page);
            $returnArr['Action'] = '1';
            $returnArr['message'] = $ridesArr;
            if ($totalPages > $page) {
                $returnArr['NextPage'] = ($page + 1);
            } else {
                $returnArr['NextPage'] = '0';
            }
        } else {
            $returnArr['Action'] = '0';
            $returnArr['message'] = 'LBL_RIDE_SHARE_NO_RIDES_FOUND_TXT';
            $returnArr['message_title'] = 'LBL_RIDE_SHARE_NO_RIDES_FOUND_TITLE';
        }
        setDataResponse($returnArr);
    }

    public function cancelPublishRide(): void
    {
        global $obj, $LANG_OBJ;
        $iUserId = $_REQUEST['GeneralMemberId'] ?? '';
        $GeneralUserType = $_REQUEST['GeneralUserType'] ?? '';
        $iPublishedRideId = $_REQUEST['iPublishedRideId'] ?? '';
        $iCancelReasonId = $_REQUEST['iCancelReasonId'] ?? '';
        $tCancelReason = $_REQUEST['tCancelReason'] ?? '';
        $vLangCode = $_REQUEST['vGeneralLang'] ?? '1';
        if ('' === $vLangCode || null === $vLangCode) {
            $vLangCode = $LANG_OBJ->FetchDefaultLangData('vCode');
        }
        $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLangCode, '1', '');
        $publishRideData = $this->getPublishRideById($iPublishedRideId);
        $where = " iPublishedRideId = '".$iPublishedRideId."' AND iUserId = '".$iUserId."'";
        $data['eStatus'] = 'Cancelled';
        $data['eCancelledBy'] = $GeneralUserType;
        $data['tCancelReason'] = $tCancelReason;
        $data['iCancelReasonId'] = $iCancelReasonId;
        $data['dCancelDate'] = date('Y-m-d H:i:s');
        $obj->MySQLQueryPerform('published_rides', $data, 'update', $where);
        $bookingData = $obj->MySQLSelect("SELECT riderUser.vEmail AS rider_vEmail, riderUser.iUserId AS rider_iUserId, riderDriver.iUserId AS driver_iUserId, rsb.iBookingId, rsb.vBookingNo, rsb.eStatus, rsb.ePaid, rsb.fWalletDebit, rsb.fBookingFee, rsb.ePaymentOption, rsb.iAuthorizePaymentId, rsb.fTotal, pr.vPublishedRideNo FROM ride_share_bookings as rsb LEFT JOIN published_rides as pr ON pr.iPublishedRideId = rsb.iPublishedRideId LEFT JOIN register_user as riderDriver ON riderDriver.iUserId = pr.iUserId LEFT JOIN register_user as riderUser ON riderUser.iUserId = rsb.iUserId WHERE rsb.iPublishedRideId = {$iPublishedRideId} AND rsb.eStatus = 'Approved' ");
        foreach ($bookingData as $booking) {
            if (!empty($iCancelReasonId)) {
                $vCancelReason = get_value('cancel_reason', 'vTitle_'.$vLangCode, 'iCancelReasonId', $iCancelReasonId, '', 'true');
            } else {
                $vCancelReason = $tCancelReason;
            }
            $alertMsg = str_replace('#BOOKING_NO#', $booking['vBookingNo'], $languageLabelsArr['LBL_RIDE_SHARE_CANCEL_BOOKING_PUBLISHER']);
            $mailArr = $smsArr = [];
            $rider_iUserId = $booking['rider_iUserId'];
            $mailArr['BOOKING_NO'] = $booking['vBookingNo'];
            $mailArr['DATE'] = date('D d F h:i A');
            $mailArr['USER_TYPE'] = 'Driver';
            $mailArr['REASON'] = $vCancelReason;
            $mailArr['START_LOCATION'] = $publishRideData[0]['tStartLocation'];
            $mailArr['END_LOCATION'] = $publishRideData[0]['tEndLocation'];
            $mailArr['EMAIL'] = $booking['rider_vEmail'];
            $mailArr['RIDER_USERID'] = $booking['rider_iUserId'];
            $data = $this->notify($rider_iUserId, 'NOTIFICATION,MAIL', '', 'PASSENGERS_NOTIFIED_WHEN_RIDE_CANCELED_BY_PUBLISHER', 'CancelPublishRide', $mailArr, $smsArr, $alertMsg, $iPublishedRideId, $booking['iBookingId']);
            $where = " iBookingId = '".$booking['iBookingId']."' ";
            $data = [];
            $data['eStatus'] = 'Cancelled';
            $data['tCancelReason'] = $tCancelReason;
            $data['iCancelReasonId'] = $iCancelReasonId;
            $data['dCancelDate'] = date('Y-m-d H:i:s');
            $data['ePaymentStatus'] = 'Settled';
            $obj->MySQLQueryPerform('ride_share_bookings', $data, 'update', $where);
            if ('Pending' === $booking['eStatus']) {
                if ('Card' === $booking['ePaymentOption']) {
                    $paymentData = ['iMemberId' => $booking['rider_iUserId'], 'UserType' => 'Passenger', 'iAuthorizePaymentId' => $booking['iAuthorizePaymentId']];
                    $resultArr = PaymentGateways::getInstance()->cancelAuthorizedPayment($paymentData);
                    if ('0' === $resultArr['Action']) {
                        $returnArr['Action'] = '0';
                        $returnArr['message'] = 'LBL_SOMETHING_WENT_WRONG_MSG';
                        setDataResponse($returnArr);
                    }
                }
            } elseif ('Approved' === $booking['eStatus']) {
                $this->CreditDriverCommission($booking['fBookingFee'], $booking['driver_iUserId'], $booking['iBookingId'], $booking['vBookingNo']);
                $driverDebitAmt = $booking['fTotal'] - $booking['fBookingFee'];
                if ($booking['fWalletDebit'] > 0) {
                    $this->CreditMemberPayment($booking['fWalletDebit'], $booking['rider_iUserId'], $booking['iBookingId'], $booking['vBookingNo'], 'User');
                }
            }
        }
        $returnArr['Action'] = '1';
        $returnArr['message'] = 'LBL_RIDE_SHARE_PUBLISH_RIDE_CANCEL_SUCCESSFULLY_TXT';
        setDataResponse($returnArr);
    }

    public function notify($userId, $type, $SMS_TEMPLATE, $MAIL_TEMPLATE, $NOTI_TEMPLATE, $mailArr, $smsArr, $alertMsg = '', $iPublishedRideId = '', $iBookingId = '')
    {
        global $obj, $EVENT_MSG_OBJ, $COMM_MEDIA_OBJ, $LANG_OBJ;
        $vLangCode = $_REQUEST['vGeneralLang'] ?? '1';
        if ('' === $vLangCode || null === $vLangCode) {
            $vLangCode = $LANG_OBJ->FetchDefaultLangData('vCode');
        }
        $type = explode(',', $type);
        $row1 = $obj->MySQLSelect("SELECT vPhoneCode , vPhone, eHmsDevice , iUserId, vCurrencyPassenger, vLang, eDeviceType, iGcmRegId, eAppTerminate, eDebugMode, vName, vLastName FROM `register_user` WHERE iUserId = '".$userId."'");
        if (\in_array('NOTIFICATION', $type, true)) {
            $generalDataArr = $final_message = [];
            $final_message['Message'] = $NOTI_TEMPLATE;
            $final_message['MsgType'] = $NOTI_TEMPLATE;
            $final_message['time'] = time();
            $final_message['eType'] = 'RideShare';
            $generalDataArr[] = ['eDeviceType' => $row1[0]['eDeviceType'], 'deviceToken' => $row1[0]['iGcmRegId'], 'alertMsg' => $alertMsg, 'eAppTerminate' => $row1[0]['eAppTerminate'], 'eDebugMode' => $row1[0]['eDebugMode'], 'message' => $final_message, 'eHmsDevice' => $row1[0]['eHmsDevice'], 'channelName' => 'PASSENGER_'.$userId];
            $arr['NOTIFICATION'] = $EVENT_MSG_OBJ->send(['GENERAL_DATA' => $generalDataArr], RN_USER);
        }
        if (\in_array('MAIL', $type, true)) {
            $arr['MAIL'] = $COMM_MEDIA_OBJ->SendMailToMember($MAIL_TEMPLATE, $mailArr);
        }
        if (\in_array('SMS', $type, true)) {
            $message_layout = $COMM_MEDIA_OBJ->GetSMSTemplate($SMS_TEMPLATE, $smsArr, '', $vLangCode);
            $arr['SMS'] = $COMM_MEDIA_OBJ->SendSystemSMS($row1[0]['vPhone'], $row1[0]['vPhoneCode'], $message_layout);
        }

        return $arr;
    }

    public function UpdateBookingsStatus(): void
    {
        global $obj, $LANG_OBJ, $WALLET_OBJ;
        $iUserId = $_REQUEST['GeneralMemberId'] ?? '';
        $GeneralUserType = $_REQUEST['GeneralUserType'] ?? '';
        $iPublishedRideId = $_REQUEST['iPublishedRideId'] ?? '';
        $iBookingId = $_REQUEST['iBookingId'] ?? '';
        $eStatus = $_REQUEST['eStatus'] ?? '';
        $vLangCode = $_REQUEST['vGeneralLang'] ?? '1';
        $iCancelReasonId = $_REQUEST['iCancelReasonId'] ?? '';
        $tCancelReason = $_REQUEST['tCancelReason'] ?? '';
        if ('' === $vLangCode || null === $vLangCode) {
            $vLangCode = $LANG_OBJ->FetchDefaultLangData('vCode');
        }
        $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLangCode, '1', '');
        $getBookingData = $this->getBookingById($iBookingId);
        $getBookingData = $getBookingData[0];
        $date = date('Y-m-d H:i:s');
        if (strtotime($getBookingData['dStartDate']) < strtotime($date)) {
            $returnArr['Action'] = '0';
            $returnArr['message'] = $languageLabelsArr['LBL_EXPIRED_TXT'];
        }
        if ('Approved' === $eStatus && $getBookingData['iBookedSeats'] <= ($getBookingData['iAvailableSeats'] - $getBookingData['pr_iBookedSeats'])) {
            $update_status = 'No';
            $commissionDeducted = 'No';
            $settle_payment = 'No';
            if ('Cash' === $getBookingData['ePaymentOption']) {
                $fWalletDebitCommission = $getBookingData['fBookingFee'];
                $eCommissionDeduct = $getBookingData['eCommissionDeduct'];
                $this->adminCommission($eCommissionDeduct, $fWalletDebitCommission, $iUserId, $iBookingId);
                $update_status = 'Yes';
            }
            if ('Wallet' === $getBookingData['ePaymentOption'] || 'Yes' === $getBookingData['ePayWallet']) {
                $this->DebitMemberPayment($getBookingData['fWalletDebit'], $getBookingData['rider_iUserId'], $iBookingId, $getBookingData['vBookingNo'], 'User');
                if ('No' === $commissionDeducted) {
                    $fWalletDebitCommission = $getBookingData['fBookingFee'];
                    $eCommissionDeduct = $getBookingData['eCommissionDeduct'];
                    $this->adminCommission($eCommissionDeduct, $fWalletDebitCommission, $iUserId, $iBookingId);
                }
                $update_status = 'Yes';
            }
            if ('Card' === $getBookingData['ePaymentOption']) {
                $paymentData = ['iMemberId' => $getBookingData['rider_iUserId'], 'UserType' => 'Passenger', 'iAuthorizePaymentId' => $getBookingData['iAuthorizePaymentId']];
                $resultArr = PaymentGateways::getInstance()->capturePayment($paymentData);
                if (1 === $resultArr['Action']) {
                    $where_payments = " tPaymentUserID = '".$resultArr['tPaymentTransactionId']."'";
                    $data_payments['iBookingId'] = $iBookingId;
                    $data_payments['eEvent'] = 'RideShareBooking';
                    $obj->MySQLQueryPerform('payments', $data_payments, 'update', $where_payments);
                    $fWalletDebitCommission = $getBookingData['fBookingFee'];
                    $eCommissionDeduct = $getBookingData['eCommissionDeduct'];
                    $this->adminCommission($eCommissionDeduct, $fWalletDebitCommission, $iUserId, $iBookingId);
                    $update_status = 'Yes';
                } else {
                    $transMsg = 'LBL_CHARGE_COLLECT_FAILED';
                    $returnArr['Action'] = '0';
                    $returnArr['message'] = $resultArr['message'];
                    setDataResponse($returnArr);
                }
            }
            if ('Yes' === $update_status) {
                $where = " iBookingId = '".$iBookingId."'";
                $Update_ride_share = [];
                $Update_ride_share['eStatus'] = $eStatus;
                $Update_ride_share['eCommissionDeduct'] = 'Yes';
                $Update_ride_share['ePaid'] = 'Yes';
                if ('Cash' === $getBookingData['ePaymentOption'] && 0 === $getBookingData['fWalletDebit']) {
                    $Update_ride_share['ePaymentStatus'] = 'Settled';
                } elseif ('Wallet' === $getBookingData['ePaymentOption']) {
                }
                $id = $obj->MySQLQueryPerform('ride_share_bookings', $Update_ride_share, 'update', $where);
                $where = " iPublishedRideId = '".$iPublishedRideId."'";
                $update_published_rides = [];
                $update_published_rides['iBookedSeats'] = $getBookingData['pr_iBookedSeats'] + $getBookingData['iBookedSeats'];
                $id = $obj->MySQLQueryPerform('published_rides', $update_published_rides, 'update', $where);
                $returnArr['Action'] = '1';
                $returnArr['message'] = str_replace('#RIDER_NAME#', $getBookingData['rider_Name'], $languageLabelsArr['LBL_RIDE_SHARE_BOOKING_APPROVED_SUCCESSFULLY_TXT']);
            }
            $rider_iUserId = $getBookingData['rider_iUserId'];
            $mailArr = [];
            $mailArr['BOOKING_NO'] = $getBookingData['vBookingNo'];
            $mailArr['START_LOCATION'] = $getBookingData['tStartLocation'];
            $mailArr['END_LOCATION'] = $getBookingData['tEndLocation'];
            $mailArr['EMAIL'] = $getBookingData['rider_vEmail'];
            $alertMeg = str_replace('#XXXXX#', $getBookingData['vBookingNo'], $languageLabelsArr['LBL_RIDE_SHARE_PASSENGERS_NOTIFIED_AFTER_THE_ACCEPTED']);
            $this->notify($rider_iUserId, 'MAIL,NOTIFICATION', '', 'PASSENGERS_NOTIFIED_WHEN_RIDE_ACCEPTED_BY_PUBLISHER', 'AcceptPublishRide', $mailArr, '', $alertMeg, '', $iBookingId);
        } elseif ('Declined' === $eStatus) {
            $where = " iBookingId = '".$iBookingId."'";
            $Update_ride_share = [];
            $Update_ride_share['eStatus'] = $eStatus;
            $Update_ride_share['tCancelReason'] = $tCancelReason;
            $Update_ride_share['iCancelReasonId'] = $iCancelReasonId;
            $id = $obj->MySQLQueryPerform('ride_share_bookings', $Update_ride_share, 'update', $where);
            if ('Card' === $getBookingData['ePaymentOption']) {
                $paymentData = ['iMemberId' => $getBookingData['rider_iUserId'], 'UserType' => 'Passenger', 'iAuthorizePaymentId' => $getBookingData['iAuthorizePaymentId']];
                $resultArr = PaymentGateways::getInstance()->cancelAuthorizedPayment($paymentData);
                if ('0' === $resultArr['Action']) {
                    $returnArr['Action'] = '0';
                    $returnArr['message'] = 'LBL_SOMETHING_WENT_WRONG_MSG';
                    setDataResponse($returnArr);
                }
            }
            $alertMeg = str_replace(['#XXXXX#', '#USERTYPE#'], [$getBookingData['vBookingNo'], $languageLabelsArr['LBL_DRIVER']], $languageLabelsArr['LBL_RIDE_SHARE_BOOKING_DECLINED_BY_PUBLISHER']);
            $rider_iUserId = $getBookingData['rider_iUserId'];
            $this->notify($rider_iUserId, 'NOTIFICATION', '', '', 'DeclinePublishRide', '', '', $alertMeg, '', $iBookingId);
            if (isset($id) && !empty($id)) {
                $returnArr['Action'] = '1';
                $returnArr['message'] = 'LBL_RIDE_SHARE_BOOKING_DECLINED_SUCCESSFULLY_TXT';
            } else {
                $returnArr['Action'] = '0';
                $returnArr['message'] = 'LBL_SOMETHING_WENT_WRONG_MSG';
            }
        } else {
            $returnArr['Action'] = '0';
            $returnArr['message'] = 'LBL_RIDE_SHARE_BOOKING_CANT_UPDATE_STATUS_TXT';
        }
        setDataResponse($returnArr);
    }

    public function getBookingById($iBookingId)
    {
        global $obj;
        $sql = "SELECT rsb.eStatus AS bookingStatus , riderUser.vEmail AS rider_vEmail, riderDriver.vEmail AS driver_vEmail,pr.vPublishedRideNo , pr.iPublishedRideId , rsb.fBookingFee ,riderUser.iUserId AS rider_iUserId , riderDriver.iUserId AS driver_iUserId ,riderUser.vName AS rider_Name, riderUser.vLastName as rider_LastName, pr.tStartLocation, pr.tEndLocation, riderDriver.vName AS driver_Name , pr.iAvailableSeats , pr.iBookedSeats as pr_iBookedSeats , rsb.iBookedSeats , pr.dStartDate, rsb.eCommissionDeduct, rsb.ePaymentOption, rsb.ePayWallet, rsb.fWalletDebit, rsb.vBookingNo, rsb.iAuthorizePaymentId, rsb.fTotal FROM ride_share_bookings rsb LEFT JOIN published_rides as pr ON pr.iPublishedRideId = rsb.iPublishedRideId LEFT JOIN register_user as riderDriver ON riderDriver.iUserId = pr.iUserId LEFT JOIN register_user as riderUser ON riderUser.iUserId = rsb.iUserId WHERE rsb.iBookingId = '".$iBookingId."' ORDER BY pr.dAddedDate DESC";

        return $obj->MySQLSelect($sql);
    }

    public function adminCommission($eCommissionDeduct, $fWalletDebit, $iUserId, $iBookingId)
    {
        global $WALLET_OBJ;
        if ($fWalletDebit > 0 && 'No' === $eCommissionDeduct) {
            $bookingData = $this->getBookingById($iBookingId);
            $data_wallet = [];
            $data_wallet['iUserId'] = $iUserId;
            $data_wallet['eUserType'] = 'Rider';
            $data_wallet['iBalance'] = $fWalletDebit;
            $data_wallet['eType'] = 'Debit';
            $data_wallet['dDate'] = date('Y-m-d H:i:s');
            $data_wallet['iBookingId'] = $iBookingId;
            $data_wallet['eFor'] = 'Booking';
            $data_wallet['ePaymentStatus'] = 'Settelled';
            $data_wallet['tDescription'] = '#LBL_RIDE_SHARE_COMMISSION_PUBLISHES_RIDES# #'.$bookingData[0]['vPublishedRideNo'].' ('.$bookingData[0]['rider_Name'].' '.$bookingData[0]['rider_LastName'].')';
            $WALLET_OBJ->PerformWalletTransaction($data_wallet['iUserId'], $data_wallet['eUserType'], $data_wallet['iBalance'], $data_wallet['eType'], 0, $data_wallet['eFor'], $data_wallet['tDescription'], $data_wallet['ePaymentStatus'], $data_wallet['dDate'], '', '', $data_wallet['iBookingId']);
        }

        return $data_wallet;
    }

    public function BookRideDetails(): void
    {
        global $RIDE_SHARE_BOOKING_FEE, $LANG_OBJ;
        $iUserId = $_REQUEST['GeneralMemberId'] ?? '';
        $iBookNoOfSeats = $_REQUEST['iBookNoOfSeats'] ?? '1';
        $iPublishedRideId = $_REQUEST['iPublishedRideId'] ?? '1';
        $vLangCode = $_REQUEST['vGeneralLang'] ?? '1';
        if ('' === $vLangCode || null === $vLangCode) {
            $vLangCode = $LANG_OBJ->FetchDefaultLangData('vCode');
        }
        $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLangCode, '1', '');
        $PublishedRide = $this->getPublishRideById($iPublishedRideId)[0];
        $UserData = get_value('register_user', 'vCurrencyPassenger', 'iUserId', $iUserId);
        $vCountry = $UserData[0]['vCurrencyPassenger'];
        $vCurrencyPassenger = $UserData[0]['vCurrencyPassenger'];
        $tPriceRatio = $PublishedRide['tPriceRatio'];
        $tPriceRatio = json_decode($tPriceRatio, true);
        $fPrice = $PublishedRide['fPrice'] * $tPriceRatio['fRatio_'.$vCurrencyPassenger];
        $totalPrice = $iBookNoOfSeats * $fPrice;
        $BookingFees = $RIDE_SHARE_BOOKING_FEE * $totalPrice / 100;
        $Summary = [];
        $Summary[][$iBookNoOfSeats.' '.$languageLabelsArr['LBL_RIDE_SHARE_PRICE_SEAT_TXT'].' X '.formateNumAsPerCurrency($fPrice, $vCurrencyPassenger)] = formateNumAsPerCurrency($totalPrice, $vCurrencyPassenger);
        $Summary[]['eDisplaySeperator'] = 'Yes';
        if ($RIDE_SHARE_BOOKING_FEE > 0) {
            $Summary[][$languageLabelsArr['LBL_RIDE_SHARE_BOOKING_FEES_TXT']] = formateNumAsPerCurrency($BookingFees, $vCurrencyPassenger);
        }
        $returnArr['message']['Summary'] = $Summary;
        $returnArr['message']['dStartDate'] = DateTime($PublishedRide['dStartDate'], 22).' '.$languageLabelsArr['LBL_AT_TXT'].' '.DateTime($PublishedRide['dStartDate'], 18);
        $returnArr['message']['TotalPrice'] = formateNumAsPerCurrency($BookingFees + $totalPrice, $vCurrencyPassenger);
        $returnArr['Action'] = '1';
        setDataResponse($returnArr);
    }

    public function cancelRideShareBooking(): void
    {
        global $obj, $LANG_OBJ;
        $iUserId = $_REQUEST['GeneralMemberId'] ?? '';
        $GeneralUserType = $_REQUEST['GeneralUserType'] ?? '';
        $iBookingId = $_REQUEST['iBookingId'] ?? '';
        $iCancelReasonId = $_REQUEST['iCancelReasonId'] ?? '';
        $tCancelReason = $_REQUEST['tCancelReason'] ?? '';
        $getBookingData = $this->getBookingById($iBookingId);
        $vLangCode = $_REQUEST['vGeneralLang'] ?? '1';
        if ('' === $vLangCode || null === $vLangCode) {
            $vLangCode = $LANG_OBJ->FetchDefaultLangData('vCode');
        }
        $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLangCode, '1', '');
        if ('Cancelled' === $getBookingData[0]['bookingStatus']) {
            $returnArr['Action'] = '1';
            $returnArr['message'] = 'LBL_RIDE_SHARE_BOOKING_CANCEL_SUCCESS_TXT';
            setDataResponse($returnArr);
        }
        $where = " iBookingId = '".$iBookingId."' AND iUserId = '".$iUserId."'";
        $data['eStatus'] = 'Cancelled';
        $data['tCancelReason'] = $tCancelReason;
        $data['iCancelReasonId'] = $iCancelReasonId;
        $data['dCancelDate'] = date('Y-m-d H:i:s');
        $obj->MySQLQueryPerform('ride_share_bookings', $data, 'update', $where);
        if ('Approved' === $getBookingData[0]['bookingStatus']) {
            $where = " iPublishedRideId = '".$getBookingData[0]['iPublishedRideId']."'";
            $update_published_rides = [];
            $update_published_rides['iBookedSeats'] = $getBookingData[0]['pr_iBookedSeats'] - $getBookingData[0]['iBookedSeats'];
            $obj->MySQLQueryPerform('published_rides', $update_published_rides, 'update', $where);
            $this->CreditDriverCommission($getBookingData[0]['fBookingFee'], $getBookingData[0]['driver_iUserId'], $iBookingId, $getBookingData[0]['vPublishedRideNo']);
            $driverDebitAmt = $getBookingData[0]['fTotal'] - $getBookingData[0]['fBookingFee'];
            if ($getBookingData[0]['fWalletDebit'] > 0) {
                $this->CreditMemberPayment($getBookingData[0]['fWalletDebit'], $getBookingData[0]['rider_iUserId'], $iBookingId, $getBookingData[0]['vBookingNo'], 'User');
            }
        }
        $driver_iUserId = $getBookingData[0]['driver_iUserId'];
        if (!empty($iCancelReasonId)) {
            $vCancelReason = get_value('cancel_reason', 'vTitle_'.$vLangCode, 'iCancelReasonId', $iCancelReasonId, '', 'true');
        } else {
            $vCancelReason = $tCancelReason;
        }
        if ('Card' === $getBookingData[0]['ePaymentOption']) {
            $paymentData = ['iMemberId' => $getBookingData[0]['rider_iUserId'], 'UserType' => 'Passenger', 'iAuthorizePaymentId' => $getBookingData[0]['iAuthorizePaymentId']];
            $resultArr = PaymentGateways::getInstance()->cancelAuthorizedPayment($paymentData);
            if ('0' === $resultArr['Action']) {
                $returnArr['Action'] = '0';
                $returnArr['message'] = 'LBL_SOMETHING_WENT_WRONG_MSG';
                setDataResponse($returnArr);
            }
        }
        $mailArr = [];
        $mailArr['PUBLISH_RIDE_NO'] = $getBookingData[0]['vPublishedRideNo'];
        $mailArr['RIDE_NO'] = $getBookingData[0]['vPublishedRideNo'];
        $mailArr['REASON'] = $vCancelReason;
        $mailArr['START_LOCATION'] = $getBookingData[0]['tStartLocation'];
        $mailArr['END_LOCATION'] = $getBookingData[0]['tEndLocation'];
        $mailArr['EMAIL'] = $getBookingData[0]['driver_vEmail'];
        $alertMsg = str_replace('#RIDE_NO#', '#'.$getBookingData[0]['vPublishedRideNo'], $languageLabelsArr['LBL_RIDE_SHARE_CANCEL_BOOKING_PASSENGER']);
        $data = $this->notify($driver_iUserId, 'MAIL,NOTIFICATION', '', 'PUBLISHERS_NOTIFIED_WHEN_RIDE_CANCELED_BY_PASSENGERS', 'CancelPublishRide', $mailArr, '', $alertMsg, '', '');
        $returnArr['Action'] = '1';
        $returnArr['message'] = 'LBL_RIDE_SHARE_BOOKING_CANCEL_SUCCESS_TXT';
        setDataResponse($returnArr);
    }

    public function GetRideShareRecommendedPrice(): void
    {
        global $obj, $RIDE_SHARE_RECOMNENDED_PRICE, $LANG_OBJ;
        $iUserId = $_REQUEST['GeneralMemberId'] ?? '';
        $tStartLat = $_REQUEST['tStartLat'] ?? '';
        $tStartLong = $_REQUEST['tStartLong'] ?? '';
        $tEndLat = $_REQUEST['tEndLat'] ?? '';
        $tEndLong = $_REQUEST['tEndLong'] ?? '';
        $tStartLocation = $_REQUEST['tStartLocation'] ?? '';
        $tEndLocation = $_REQUEST['tEndLocation'] ?? '';
        $distance = $_REQUEST['distance'] ?? '0';
        $duration = $_REQUEST['duration'] ?? '0';
        $vLangCode = $_REQUEST['vGeneralLang'] ?? '1';
        if ('' === $vLangCode || null === $vLangCode) {
            $vLangCode = $LANG_OBJ->FetchDefaultLangData('vCode');
        }
        $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLangCode, '1', '');
        $userData = $obj->MySQLSelect("SELECT ru.vCurrencyPassenger, curr.Ratio FROM register_user as ru LEFT JOIN currency as curr ON ru.vCurrencyPassenger = curr.vName WHERE iUserId = '".$iUserId."' ");
        $priceRatio = $userData[0]['Ratio'];
        $vCurrency = $userData[0]['vCurrencyPassenger'];
        $recommended_price = 0;
        if (!empty($distance)) {
            $recommended_price = ($distance / 1_000) * $RIDE_SHARE_RECOMNENDED_PRICE;
        }
        $recommended_price_1 = $recommended_price - ($recommended_price * 0.2);
        $recommended_price_2 = $recommended_price + ($recommended_price * 0.2);
        $recommended_price_text = formateNumAsPerCurrency($recommended_price_1 * $priceRatio, $vCurrency).' - '.formateNumAsPerCurrency($recommended_price_2 * $priceRatio, $vCurrency);
        $passenger_nos_arr = [];
        for ($i = 1; $i <= RIDE_SHARE_PASSENGER_NOS; ++$i) {
            $passenger_nos_arr[] = $i;
        }
        $returnArr['Action'] = '1';
        $returnArr['RecommdedPrice'] = round($recommended_price);
        $returnArr['RecommdedPriceText'] = $languageLabelsArr['LBL_RIDE_SHARE_RECOMMENDED_PRICE_TXT'];
        $returnArr['RecommdedPriceRange'] = $recommended_price_text;
        $returnArr['PassengerNo'] = $passenger_nos_arr;
        $returnArr['message'] = strtoupper($languageLabelsArr['LBL_RIDE_SHARE_RECOMMENDED_PRICE_TXT']).' '.$recommended_price_text;
        setDataResponse($returnArr);
    }

    public function getCommission($iBookingId, $ssql)
    {
        global $obj;
        $iBookingId = implode(',', array_unique($iBookingId));
        $sql = "SELECT IFNULL( SUM( IFNULL( fBookingFee, 0 ) ), 0 ) as commission FROM ride_share_bookings rsb JOIN published_rides pr ON (pr.iPublishedRideId = rsb.iPublishedRideId) JOIN register_user riderUser ON (riderUser.iUserId = rsb.iUserId) JOIN register_user riderDriver ON (riderDriver.iUserId = pr.iUserId) WHERE 1=1 AND iBookingId IN ({$iBookingId}) {$ssql} ";
        $data_drv = $obj->MySQLSelect($sql);
        $arr['commission'] = $data_drv[0]['commission'];

        return $arr;
    }

    public function getTotalFare($iBookingId, $ssql)
    {
        global $obj;
        $iBookingId = implode(',', array_unique($iBookingId));
        $sql = "SELECT IFNULL( SUM( IFNULL( fTotal, 0 ) ), 0 ) as Total FROM ride_share_bookings rsb JOIN published_rides pr ON (pr.iPublishedRideId = rsb.iPublishedRideId) JOIN register_user riderUser ON (riderUser.iUserId = rsb.iUserId) JOIN register_user riderDriver ON (riderDriver.iUserId = pr.iUserId) WHERE 1=1 AND iBookingId IN ({$iBookingId}) {$ssql} ";
        $data_drv = $obj->MySQLSelect($sql);
        $arr = $data_drv[0]['Total'];

        return $arr;
    }

    public function CreditMemberPayment($fAmount, $iUserId, $iBookingId, $vBookingNo, $UserType): void
    {
        global $WALLET_OBJ;
        $data_wallet = [];
        $data_wallet['iUserId'] = $iUserId;
        $data_wallet['eUserType'] = 'Rider';
        $data_wallet['iBalance'] = $fAmount;
        $data_wallet['eType'] = 'Credit';
        $data_wallet['dDate'] = date('Y-m-d H:i:s');
        $data_wallet['iBookingId'] = $iBookingId;
        $data_wallet['eFor'] = 'Booking';
        $data_wallet['ePaymentStatus'] = 'Settelled';
        if ('User' === $UserType) {
            $data_wallet['tDescription'] = '#LBL_RIDE_SHARE_CREDITED_BOOKING_REFUND#'.$vBookingNo;
        } else {
            $data_wallet['tDescription'] = '#LBL_RIDE_SHARE_CREDITED_EARNING#'.$vBookingNo;
        }
        $WALLET_OBJ->PerformWalletTransaction($data_wallet['iUserId'], $data_wallet['eUserType'], $data_wallet['iBalance'], $data_wallet['eType'], 0, $data_wallet['eFor'], $data_wallet['tDescription'], $data_wallet['ePaymentStatus'], $data_wallet['dDate'], '', '', $data_wallet['iBookingId']);
    }

    public function DebitMemberPayment($fAmount, $iUserId, $iBookingId, $vBookingNo, $UserType): void
    {
        global $WALLET_OBJ;
        $data_wallet = [];
        $data_wallet['iUserId'] = $iUserId;
        $data_wallet['eUserType'] = 'Rider';
        $data_wallet['iBalance'] = $fAmount;
        $data_wallet['eType'] = 'Debit';
        $data_wallet['dDate'] = date('Y-m-d H:i:s');
        $data_wallet['iBookingId'] = $iBookingId;
        $data_wallet['eFor'] = 'Booking';
        $data_wallet['ePaymentStatus'] = 'Settelled';
        if ('User' === $UserType) {
            $data_wallet['tDescription'] = '#LBL_RIDE_SHARE_DEBITED_BOOKING# '.$vBookingNo;
        } else {
            $data_wallet['tDescription'] = '#LBL_RIDE_SHARE_BOOKING_REFUND_DEBITED# '.$vBookingNo;
        }
        $WALLET_OBJ->PerformWalletTransaction($data_wallet['iUserId'], $data_wallet['eUserType'], $data_wallet['iBalance'], $data_wallet['eType'], 0, $data_wallet['eFor'], $data_wallet['tDescription'], $data_wallet['ePaymentStatus'], $data_wallet['dDate'], '', '', $data_wallet['iBookingId']);
    }

    public function CreditDriverCommission($fAmount, $iUserId, $iBookingId, $vBookingNo): void
    {
        global $WALLET_OBJ;
        $data_wallet = [];
        $data_wallet['iUserId'] = $iUserId;
        $data_wallet['eUserType'] = 'Rider';
        $data_wallet['iBalance'] = $fAmount;
        $data_wallet['eType'] = 'Credit';
        $data_wallet['dDate'] = date('Y-m-d H:i:s');
        $data_wallet['iBookingId'] = $iBookingId;
        $data_wallet['eFor'] = 'Booking';
        $data_wallet['ePaymentStatus'] = 'Settelled';
        $data_wallet['tDescription'] = '#LBL_RIDE_SHARE_CREDITED_BOOKING#'.$vBookingNo;
        $WALLET_OBJ->PerformWalletTransaction($data_wallet['iUserId'], $data_wallet['eUserType'], $data_wallet['iBalance'], $data_wallet['eType'], 0, $data_wallet['eFor'], $data_wallet['tDescription'], $data_wallet['ePaymentStatus'], $data_wallet['dDate'], '', '', $data_wallet['iBookingId']);
    }
}
