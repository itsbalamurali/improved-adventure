<?php



namespace Kesk\Web\Common;

use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFProbe;
use FFMpeg\Format\Video\X264;

class Parking
{
    public const PARKING_FEE = 5;

    public function __construct() {}

    public function getCategories($tCategoryDetails = [])
    {
        global $languageLabelsArr, $tconfig, $obj, $master_service_category_tbl;

        if (empty($tCategoryDetails)) {
            $service_details = $obj->MySQLSelect("SELECT tCategoryDetails FROM {$master_service_category_tbl} WHERE eType = 'Parking' ");
            $tCategoryDetails = json_decode($service_details[0]['tCategoryDetails'], true);
        }
        $vImageBookParking = '';
        if (!empty($tCategoryDetails['BookParking']['vImage']) && file_exists($tconfig['tsite_upload_app_home_screen_images_path'].$tCategoryDetails['BookParking']['vImage'])) {
            $imagedata = getimagesize(str_replace('https', 'http', $tconfig['tsite_upload_app_home_screen_images'].$tCategoryDetails['BookParking']['vImage']));
            $vImageWidthBookParking = (string) $imagedata[0];
            $vImageHeightBookParking = (string) $imagedata[1];
            $vImageBookParking = $tconfig['tsite_upload_app_home_screen_images'].$tCategoryDetails['BookParking']['vImage'];
        }
        $vImageRentSpace = '';
        if (!empty($tCategoryDetails['RentSpace']['vImage']) && file_exists($tconfig['tsite_upload_app_home_screen_images_path'].$tCategoryDetails['RentSpace']['vImage'])) {
            $imagedata = getimagesize(str_replace('https', 'http', $tconfig['tsite_upload_app_home_screen_images'].$tCategoryDetails['RentSpace']['vImage']));
            $vImageWidthRentSpace = (string) $imagedata[0];
            $vImageHeightRentSpace = (string) $imagedata[1];
            $vImageRentSpace = $tconfig['tsite_upload_app_home_screen_images'].$tCategoryDetails['RentSpace']['vImage'];
        }
        $vImageBookings = '';
        if (!empty($tCategoryDetails['Bookings']['vImage']) && file_exists($tconfig['tsite_upload_app_home_screen_images_path'].$tCategoryDetails['Bookings']['vImage'])) {
            $imagedata = getimagesize(str_replace('https', 'http', $tconfig['tsite_upload_app_home_screen_images'].$tCategoryDetails['Bookings']['vImage']));
            $vImageWidthBookings = (string) $imagedata[0];
            $vImageHeightBookings = (string) $imagedata[1];
            $vImageBookings = $tconfig['tsite_upload_app_home_screen_images'].$tCategoryDetails['Bookings']['vImage'];
        }

        $category_arr = [
            [
                'vCategory' => $languageLabelsArr['LBL_BOOK_PARKING_TXT'],
                'vImage' => $vImageBookParking,
                'vListLogo' => $vImageBookParking,
                'vImageWidth' => $vImageWidthBookParking,
                'vImageHeight' => $vImageHeightBookParking,
                'eCatType' => 'BookParking',
            ],
            [
                'vCategory' => $languageLabelsArr['LBL_PARKING_RENT_YOUR_SPACE_TXT'],
                'vImage' => $vImageRentSpace,
                'vListLogo' => $vImageRentSpace,
                'vImageWidth' => $vImageWidthRentSpace,
                'vImageHeight' => $vImageHeightRentSpace,
                'eCatType' => 'RentSpace',
            ],
            [
                'vCategory' => $languageLabelsArr['LBL_MY_PARKINGS_TXT'],
                'vImage' => $vImageBookings,
                'vListLogo' => $vImageBookings,
                'vImageWidth' => $vImageWidthBookings,
                'vImageHeight' => $vImageHeightBookings,
                'eCatType' => 'MyParkings',
            ],
        ];

        return $category_arr;
    }

    public function AddParkingSpace(): void
    {
        global $obj, $Data_ALL_currency_Arr;

        $GeneralMemberId = $_REQUEST['GeneralMemberId'] ?? '';
        $vLatitude = $_REQUEST['vLatitude'] ?? '';
        $vLongitude = $_REQUEST['vLongitude'] ?? '';
        $tAddress = $_REQUEST['tAddress'] ?? '';
        $tLocation = $_REQUEST['tLocation'] ?? '';
        $iParkingSpaceNo = $_REQUEST['iParkingSpaceNo'] ?? '';
        $iParkingVehicleSizeId = $_REQUEST['iParkingVehicleSizeId'] ?? '';
        $tInstructions = $_REQUEST['tInstructions'] ?? '';
        $tPriceDetails = $_REQUEST['tPriceDetails'] ?? '';
        $TimeIds = $_REQUEST['TimeIds'] ?? '';
        $ParkingSpaceImageIds = $_REQUEST['ParkingSpaceImageIds'] ?? '';

        $userData = $obj->MySQLSelect("SELECT CONCAT(ru.vName, ' ', ru.vLastName) as PublisherName, CONCAT('+', ru.vPhoneCode, ru.vPhone) as PhoneNo, ru.vCurrencyPassenger, curr.Ratio FROM register_user as ru LEFT JOIN currency as curr ON ru.vCurrencyPassenger = curr.vName WHERE iUserId = '".$GeneralMemberId."' ");
        $priceRatio = $userData[0]['Ratio'];

        $vParkingSpaceNo = $this->GenerateUniqueNo();
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

        $Data_insert = [];
        $Data_insert['vParkingSpaceNo'] = $vParkingSpaceNo;
        $Data_insert['iUserId'] = $GeneralMemberId;
        $Data_insert['vLatitude'] = $vLatitude;
        $Data_insert['vLongitude'] = $vLongitude;
        $Data_insert['tAddress'] = $tAddress;
        $Data_insert['tLocation'] = $tLocation;
        $Data_insert['iParkingSpaceTotalNo'] = $iParkingSpaceNo;
        $Data_insert['iParkingSpaceAvailableNo'] = $iParkingSpaceNo;
        $Data_insert['iParkingVehicleSizeId'] = $iParkingVehicleSizeId;
        $Data_insert['tPrice'] = ($tPriceDetails / $priceRatio);
        $Data_insert['tPriceRatio'] = json_encode($tPriceRatioArr, JSON_UNESCAPED_UNICODE);
        $Data_insert['eStatus'] = 'Pending';
        $Data_insert['dAddedDate'] = date('Y-m-d H:i:s');
        $iParkingSpaceId = $obj->MySQLQueryPerform('parking_space', $Data_insert, 'insert');

        $obj->sql_query("UPDATE parking_manage_timing SET iParkingSpaceId = '{$iParkingSpaceId}' WHERE iUserId = '{$GeneralMemberId}' AND iParkingSpaceId = '0' ");

        if (!empty($ParkingSpaceImageIds)) {
            $obj->sql_query("UPDATE parking_space_images SET iParkingSpaceId = '{$iParkingSpaceId}' WHERE iParkingSpaceImageId IN ({$ParkingSpaceImageIds}) ");
        }

        $returnArr['Action'] = '1';
        $returnArr['message'] = 'LBL_PARKING_SPACE_PUBLISH_SUCCESS_TXT';
        $returnArr['message_title'] = 'LBL_PARKING_SPACE_PUBLISH_SUCCESS_TITLE';
        setDataResponse($returnArr);
    }

    public function UploadParkingSpaceMedia(): void
    {
        global $obj, $tconfig, $LANG_OBJ, $UPLOAD_OBJ, $oCache;

        $GeneralMemberId = $_REQUEST['GeneralMemberId'] ?? '';
        $iParkingSpaceId = $_REQUEST['iParkingSpaceId'] ?? '0';
        $vLang = $_REQUEST['vGeneralLang'] ?? '';
        $image_name = $vImage = $_FILES['vImage']['name'] ?? '';
        $image_object = $_FILES['vImage']['tmp_name'] ?? '';

        $oCache->delData(md5('PARKING_SPACE_IMAGES_'.$GeneralMemberId.'_'.$iParkingSpaceId));
        $langLabels = $LANG_OBJ->FetchLanguageLabels($vLang, '1', '');

        if ('' !== $image_name) {
            $filecheck = basename($image_name);
            $fileextarr = explode('.', $filecheck);
            $ext = strtolower($fileextarr[\count($fileextarr) - 1]);
            if (!\in_array($ext, ['jpg', 'png', 'bmp', 'jpeg', 'heic', 'mp4', 'mov', 'wmv', 'avi', 'flv', 'mkv', 'webm'], true)) {
                $var_msg = $langLabels['LBL_FILE_EXT_VALID_ERROR_MSG'].' .jpg, .jpeg, .heic, .png, .bmp, .mp4, .mov, .wmv, .avi, .flv, .mkv, .webm';
                $returnArr['Action'] = '0';
                $returnArr['message'] = $var_msg;
                setDataResponse($returnArr);
            }
            $Photo_Gallery_folder = $tconfig['tsite_upload_images_parking_space_path'].'/';
            if (!is_dir($Photo_Gallery_folder)) {
                mkdir($Photo_Gallery_folder, 0777);
                chmod($Photo_Gallery_folder, 0777);
            }
            $img1 = $UPLOAD_OBJ->GeneralFileUpload($Photo_Gallery_folder, $image_object, $image_name, '', 'jpg,png,gif,jpeg,bmp,mp4,mov,wmv,avi,flv,mkv,webm');
            $vImgName = $img1[0];

            if (!empty($vImgName)) {
                $Data_update_image = [];
                $Data_update_image['vImage'] = $vImageName;
                $Data_update_image['iParkingSpaceId'] = $iParkingSpaceId;
                $Data_update_image['iUserId'] = $GeneralMemberId;
                $Data_update_image['vImage'] = $vImgName;
                $Data_update_image['dAddedDate'] = @date('Y-m-d H:i:s');
                $iParkingSpaceImageId = $obj->MySQLQueryPerform('parking_space_images', $Data_update_image, 'insert');

                $returnArr['Action'] = '1';
                $returnArr['iParkingSpaceImageId'] = $iParkingSpaceImageId;
                $returnArr['message'] = 'LBL_PARKING_IMAGE_UPLOAD_MSG';
            } else {
                $returnArr['Action'] = '0';
                $returnArr['message'] = 'LBL_SOMETHING_WENT_WRONG_MSG';
            }
        } else {
            $returnArr['Action'] = '0';
            $returnArr['message'] = 'LBL_SOMETHING_WENT_WRONG_MSG';
        }

        setDataResponse($returnArr);
    }

    public function getParkingSpaceImages($iUserId, $iParkingSpaceId = 0)
    {
        global $obj, $tconfig, $oCache;

        $ParkingSpaceImgApcKey = md5('PARKING_SPACE_IMAGES_'.$iUserId.'_'.$iParkingSpaceId);
        $getParkingSpaceImgCacheData = $oCache->getData($ParkingSpaceImgApcKey);
        if (!empty($getParkingSpaceImgCacheData) && \count($getParkingSpaceImgCacheData) > 0) {
            $parking_space_images = $getParkingSpaceImgCacheData;
        } else {
            $ssql = " AND iParkingSpaceId = '0' ";
            if ($iParkingSpaceId > 0) {
                $ssql = " AND iParkingSpaceId = '{$iParkingSpaceId}' ";
            }
            $parking_space_images = $obj->MySQLSelect("SELECT iParkingSpaceImageId,vImage FROM parking_space_images WHERE iUserId = '{$iUserId}' {$ssql} ");
            $oCache->setData($ParkingSpaceImgApcKey, $parking_space_images);
        }

        $ParkingSpaceImageUrl = $tconfig['tsite_upload_images_parking_space'];
        $ParkingSpaceImagesArr = [];
        if (!empty($parking_space_images)) {
            $mCount = 0;
            foreach ($parking_space_images as $image) {
                $tmp = explode('.', $image['vImage']);
                $ext = $tmp[\count($tmp) - 1];
                $videoExt_arr = ['MP4', 'MOV', 'WMV', 'AVI', 'FLV', 'MKV', 'WEBM'];
                $ParkingSpaceImagesArr[$mCount]['iParkingSpaceImageId'] = $image['iParkingSpaceImageId'];
                $ParkingSpaceImagesArr[$mCount]['vImage'] = $ParkingSpaceImageUrl.'/'.$image['vImage'];
                $ParkingSpaceImagesArr[$mCount]['eFileType'] = 'Image';
                $ParkingSpaceImagesArr[$mCount]['ThumbImage'] = '';
                if (\in_array(strtoupper($ext), $videoExt_arr, true)) {
                    $ParkingSpaceImagesArr[$mCount]['eFileType'] = 'Video';
                    $ParkingSpaceImagesArr[$mCount]['ThumbImage'] = $this->getVideoThumbImage($image['vImage']);
                }
                ++$mCount;
            }
        }

        return $ParkingSpaceImagesArr;
    }

    public function getVideoThumbImage($video_file)
    {
        global $tconfig;

        $tmpArr = explode('.', $video_file);
        $thumb_img = $tmpArr[0].'.png';
        $img_path = $tconfig['tsite_upload_images_parking_space_path'].'/thumnails/'.$thumb_img;
        $img_url = $tconfig['tsite_upload_images_parking_space'].'/thumnails/'.$thumb_img;
        if (!is_dir($tconfig['tsite_upload_images_parking_space_path'].'/thumnails/')) {
            mkdir($tconfig['tsite_upload_images_parking_space_path'].'/thumnails/', 0777);
            chmod($tconfig['tsite_upload_images_parking_space_path'].'/thumnails/', 0777);
        }
        if (file_exists($img_path)) {
            return $img_url;
        }

        require_once $tconfig['tpanel_path'].'assets/libraries/FFMpeg/autoload.php';
        $sec = 3;
        $vFile = $tconfig['tsite_upload_images_parking_space_path'].'/'.$video_file;
        if (file_exists($vFile)) {
            // --------------------- mkv to mp4 --------------------
            $thumb_video = $tmpArr[0].'.mp4';
            if ('mkv' === $tmpArr[1]) {
                if (!file_exists($tconfig['tsite_upload_images_parking_space_path'].'/'.$thumb_video)) {
                    $ffmpeg = FFMpeg\FFMpeg::create();
                    $video = $ffmpeg->open($vFile);
                    $format = new X264();
                    $format->setAudioCodec('libmp3lame');
                    $vFile = $tconfig['tsite_upload_images_parking_space_path'].'/'.$thumb_video;
                    $video->save($format, $vFile);
                } else {
                    $vFile = $tconfig['tsite_upload_images_parking_space_path'].'/'.$thumb_video;
                }
            }
            // --------------------- mkv to mp4 --------------------
            $ffprobe = FFProbe::create();
            $vDuration = $ffprobe->streams($vFile)->videos()->first()->get('duration');
            if ($vDuration < 3) {
                $sec = floor($vDuration);
            }
            $ffmpeg = FFMpeg\FFMpeg::create();
            $video = $ffmpeg->open($vFile);
            $frame = $video->frame(TimeCode::fromSeconds($sec));
            $frame->save($img_path);

            return $img_url;
        }

        return '';
    }

    public function DeleteParkingSpaceMedia(): void
    {
        global $obj, $oCache;

        $GeneralMemberId = $_REQUEST['GeneralMemberId'] ?? '';
        $iParkingSpaceImageId = $_REQUEST['iParkingSpaceImageId'] ?? '0';

        $parking_space_image = $obj->MySQLSelect("SELECT vImage FROM parking_space_images WHERE iParkingSpaceImageId = '{$iParkingSpaceImageId}' ");
        unlink($tconfig['tsite_upload_images_parking_space_path'].'/'.$parking_space_image[0]['vImage']);

        $obj->sql_query("DELETE FROM parking_space_images WHERE iParkingSpaceImageId = '{$iParkingSpaceImageId}' ");

        $oCache->delData(md5('PARKING_SPACE_IMAGES_'.$GeneralMemberId.'_0'));

        $returnArr['Action'] = '1';
        $returnArr['message'] = 'LBL_PARKING_IMAGE_DELETED_MSG';
        setDataResponse($returnArr);
    }

    public function DisplayAvailabilityForParking(): void
    {
        global $obj, $LANG_OBJ;

        $GeneralMemberId = $_REQUEST['GeneralMemberId'] ?? '';
        $iParkingSpaceId = $_REQUEST['iParkingSpaceId'] ?? '0';
        $vDay = $_REQUEST['vDay'] ?? '';
        $vLang = $_REQUEST['vGeneralLang'] ?? '';
        $GeneralDeviceType = $_REQUEST['GeneralDeviceType'] ?? 'Android';

        if (empty($vDay)) {
            $dAddedDate = @date('Y-m-d');
            $vDay = @date('l', strtotime($dAddedDate));
            $returnArr['vDay'] = $vDay;
        }

        $ssql = " AND iParkingSpaceId = '0' ";
        if ($iParkingSpaceId > 0) {
            $ssql = " AND iParkingSpaceId = '{$iParkingSpaceId}' ";
        }

        $db_data = $obj->MySQLSelect("SELECT * FROM parking_manage_timing WHERE iUserId = '".$GeneralMemberId."' AND vDay LIKE '".$vDay."' {$ssql} ORDER BY iTimeId DESC");

        if ('Android' === $GeneralDeviceType) {
            $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLang, '1', '');
            $db_data_lang = $db_data;
            if (!empty($db_data) && \count($db_data) > 0) {
                foreach ($db_data as $key => $value) {
                    $day = 'LBL_'.strtoupper($value['vDay']).'_TXT';
                    $db_data_lang[$key]['vDay'] = $languageLabelsArr[$day];
                }
                $db_data = $db_data_lang;
                $day = 'LBL_'.strtoupper($vDay).'_TXT';
            }
        }
        if (!isset($returnArr['vDay'])) {
            $returnArr['vDay'] = $vDay;
        }

        $returnArr['AvailabilityAdded'] = 'No';
        if (0 === $iParkingSpaceId) {
            $db_data_all = $obj->MySQLSelect("SELECT iTimeId FROM parking_manage_timing WHERE iUserId = '".$GeneralMemberId."' AND iParkingSpaceId = '0' ");
            if (!empty($db_data_all) && \count($db_data_all) > 0) {
                $returnArr['AvailabilityAdded'] = 'Yes';
            }
        }

        if (\count($db_data) > 0) {
            $returnArr['Action'] = '1';
            $returnArr['message'] = $db_data[0];
        } else {
            $returnArr['Action'] = '0';
            $returnArr['message'] = 'LBL_NO_AVAILABILITY_FOUND';
        }
        setDataResponse($returnArr);
    }

    public function UpdateAvailabilityForParking(): void
    {
        global $obj, $LANG_OBJ;

        $GeneralMemberId = $_REQUEST['GeneralMemberId'] ?? '';
        $iParkingSpaceId = $_REQUEST['iParkingSpaceId'] ?? '0';
        $vDay = $_REQUEST['vDay'] ?? '';
        $vAvailableTimes = $_REQUEST['vAvailableTimes'] ?? '';
        $eStatus = $_REQUEST['eStatus'] ?? 'Active';
        $dAddedDate = @date('Y-m-d H:i:s');
        $vAvailableTimes = CheckAvailableTimes($vAvailableTimes);

        $ssql = " AND iParkingSpaceId = '0' ";
        if ($iParkingSpaceId > 0) {
            $ssql = " AND iParkingSpaceId = '{$iParkingSpaceId}' ";
        }
        $db_data = $obj->MySQLSelect("SELECT iTimeId FROM parking_manage_timing WHERE iUserId = '".$GeneralMemberId."' AND vDay LIKE '".$vDay."' {$ssql}");

        if (\count($db_data) > 0) {
            $action = 'Edit';
            $iTimeId = $db_data[0]['iTimeId'];
        } else {
            $action = 'Add';
        }
        $Data_parking_timing = [];
        $Data_parking_timing['iUserId'] = $GeneralMemberId;
        $Data_parking_timing['iParkingSpaceId'] = $iParkingSpaceId;
        $Data_parking_timing['vDay'] = $vDay;
        $Data_parking_timing['vAvailableTimes'] = $vAvailableTimes;
        $Data_parking_timing['dAddedDate'] = $dAddedDate;
        $Data_parking_timing['eStatus'] = $eStatus;

        if ('Add' === $action && !empty($vAvailableTimes)) {
            $insertid = $obj->MySQLQueryPerform('parking_manage_timing', $Data_parking_timing, 'insert');
            $iTimeId = $insertid;
        } else {
            if (!empty($vAvailableTimes)) {
                $where = " iTimeId = '".$iTimeId."'";
                $obj->MySQLQueryPerform('parking_manage_timing', $Data_parking_timing, 'update', $where);
            } else {
                $obj->sql_query("DELETE FROM parking_manage_timing WHERE iTimeId = '{$iTimeId}' ");
            }
        }

        $returnArr['AvailabilityAdded'] = 'No';
        if (0 === $iParkingSpaceId) {
            $db_data = $obj->MySQLSelect("SELECT iTimeId FROM parking_manage_timing WHERE iUserId = '".$GeneralMemberId."' AND iParkingSpaceId = '0' ");
            if (!empty($db_data) && \count($db_data) > 0) {
                $returnArr['AvailabilityAdded'] = 'Yes';
            }
        }

        $returnArr['Action'] = '1';
        setDataResponse($returnArr);
    }

    public function GetVerificationDocuments($returnData = 'No')
    {
        global $obj, $LANG_OBJ, $tconfig;

        $iUserId = $_REQUEST['GeneralMemberId'] ?? '';
        $vLang = $_REQUEST['vGeneralLang'] ?? '';
        if (empty($vLang)) {
            $vLang = $LANG_OBJ->FetchDefaultLangData('vCode');
        }

        $UserData = get_value('register_user', 'vCountry,vLang', 'iUserId', $iUserId);
        $vCountry = $UserData[0]['vCountry'];

        $vLangCodeData = get_value('language_master', 'vCode, vGMapLangCode', 'eDefault', 'Yes');
        $vGMapLangCode = $vLangCodeData[0]['vGMapLangCode'];

        $documents = $obj->MySQLSelect("SELECT  rdl.ex_date,rdl.doc_file,rdl.doc_id,(SELECT CASE WHEN COUNT(*) > 0 THEN 'Yes' ELSE 'No' END  as 'isUploaded' FROM `parking_space_document_list` WHERE doc_masterid = dm.doc_masterid AND doc_userid = {$iUserId}) as isUploaded,dm.doc_masterid, dm.ex_status, dm.doc_name_{$vLang} as doc_name FROM document_master as dm
            LEFT JOIN parking_space_document_list rdl ON (rdl.doc_masterid = dm.doc_masterid AND doc_userid = {$iUserId} )
             WHERE (dm.country='".$vCountry."' OR dm.country='All') and dm.doc_usertype = 'parking' AND dm.status = 'Active' ORDER BY iDisplayOrder ASC ");
        $img_path = $tconfig['tsite_upload_parking_space_documents'];
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

        if ('Yes' === $returnData) {
            return $documents;
        }

        $returnArr['Action'] = '1';
        $returnArr['message'] = $documents;
        setDataResponse($returnArr);
    }

    public function UploadParkingSpaceDocuments(): void
    {
        global $obj, $tconfig, $LANG_OBJ, $UPLOAD_OBJ;

        $iUserId = $_REQUEST['GeneralMemberId'] ?? '';
        $vLang = $_REQUEST['vGeneralLang'] ?? '';
        $iDocumentId = $_REQUEST['doc_id'] ?? '';
        $doc_masterid = $_REQUEST['doc_masterid'] ?? '';
        $ex_date = $_REQUEST['ex_date'] ?? '';
        $image_name = $_FILES['vImage']['name'] ?? '';
        $image_object = $_FILES['vImage']['tmp_name'] ?? '';

        $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLang, '1', '');

        if (!empty($image_name)) {
            $img_path = $tconfig['tsite_upload_parking_space_documents_path'];
            $temp_gallery = $img_path.'/';
            $filecheck = basename($image_name);
            $fileextarr = explode('.', $filecheck);
            $ext = strtolower($fileextarr[\count($fileextarr) - 1]);
            if ('jpg' !== $ext && 'png' !== $ext && 'jpeg' !== $ext && 'bmp' !== $ext && 'heic' !== $ext && 'pdf' !== $ext && 'doc' !== $ext && 'docx' !== $ext) {
                $var_msg = $languageLabelsArr['LBL_FILE_EXT_VALID_ERROR_MSG'].' .jpg, .jpeg, .png, .bmp, .heic, .pdf, .doc, .docx';
                $returnArr['Action'] = '0';
                $returnArr['message'] = $var_msg;
                setDataResponse($returnArr);
            }
            $Photo_Gallery_folder = $img_path.'/'.$iUserId.'/';
            $Photo_Gallery_folder_temp = $img_path.'/'.$iUserId.'/';
            if (!is_dir($img_path.'/')) {
                mkdir($img_path.'/', 0777);
                chmod($img_path.'/', 0777);
            }
            if (!is_dir($Photo_Gallery_folder)) {
                mkdir($Photo_Gallery_folder, 0777);
                chmod($Photo_Gallery_folder, 0777);
            }
            $img1 = $UPLOAD_OBJ->GeneralFileUpload($Photo_Gallery_folder_temp, $image_object, $image_name, '', 'jpg,png,jpeg,bmp,heic,pdf,doc,docx');
            $vImgName = $img1[0];
            if (!empty($vImgName)) {
                $Data_insert['doc_masterid'] = $doc_masterid;
                $Data_insert['doc_userid'] = $iUserId;
                if (!empty($ex_date)) {
                    $Data_insert['ex_date'] = date('Y-m-d', strtotime($ex_date));
                }
                $Data_insert['doc_userid'] = $iUserId;
                $Data_insert['doc_file'] = $vImgName;
                $Data_insert['doc_file'] = $vImgName;
                $Data_insert['dAddedDate'] = date('Y-m-d H:i:s');
                $Data_insert['eApproveDoc'] = 'No';

                if (!empty($iDocumentId) && $iDocumentId > 0) {
                    $where = " doc_id = '{$iDocumentId}' ";
                    $obj->MySQLQueryPerform('parking_space_document_list', $Data_insert, 'update', $where);
                    $document_id = $iDocumentId;
                } else {
                    $document_id = $obj->MySQLQueryPerform('parking_space_document_list', $Data_insert, 'insert');
                }
                $returnArr['Action'] = '1';
                $returnArr['message'] = 'LBL_FILE_UPLOADED_SUCCESS_MSG';
            } else {
                $returnArr['Action'] = '0';
                $returnArr['message'] = 'LBL_FILE_UPLOADED_UNSUCCESS_MSG';
            }
        } else {
            $returnArr['Action'] = '0';
            $returnArr['message'] = 'LBL_FILE_UPLOADED_UNSUCCESS_MSG';
        }
        setDataResponse($returnArr);
    }

    public function FetchParkingSpaces(): void
    {
        global $obj, $tconfig, $LANG_OBJ;

        $GeneralMemberId = $_REQUEST['GeneralMemberId'] ?? '';
        $vLang = $_REQUEST['vGeneralLang'] ?? '';
        $vTimeZone = $_REQUEST['vTimeZone'] ?? '';
        $vFilterParam = $_REQUEST['vFilterParam'] ?? '';
        $page = isset($_REQUEST['page']) ? trim($_REQUEST['page']) : 1;

        $filterquery = '';
        if (!empty($vFilterParam)) {
            if ('PENDING' === strtoupper($vFilterParam)) {
                $filterquery = " AND eStatus = 'Pending' ";
            } elseif ('APPROVED' === strtoupper($vFilterParam)) {
                $filterquery = " AND eStatus = 'Approved' ";
            } elseif ('DECLINED' === strtoupper($vFilterParam)) {
                $filterquery = " AND eStatus = 'Declined' ";
            } else {
                $filterquery = " AND eStatus != 'Deleted' ";
            }
        }

        $parking_spaces_count = $obj->MySQLSelect("SELECT COUNT(iParkingSpaceId) as count FROM parking_space WHERE iUserId = '{$GeneralMemberId}' {$filterquery}");

        $total = $parking_spaces_count[0]['count'];
        $per_page = 10;
        $totalPages = ceil($total / $per_page);
        $start_limit = ($page - 1) * $per_page;

        if ($parking_spaces_count[0]['count'] > 0) {
            $userData = $obj->MySQLSelect("SELECT CONCAT(ru.vName, ' ', ru.vLastName) as PublisherName, CONCAT('+', ru.vPhoneCode, ru.vPhone) as PhoneNo, ru.vCurrencyPassenger, curr.Ratio FROM register_user as ru LEFT JOIN currency as curr ON ru.vCurrencyPassenger = curr.vName WHERE iUserId = '".$GeneralMemberId."' ");
            $vCurrencyPassenger = $userData[0]['vCurrencyPassenger'];

            $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLang, '1', '');
            $serverTimeZone = date_default_timezone_get();

            $parking_spaces = $obj->MySQLSelect("SELECT iParkingSpaceId, vParkingSpaceNo, tAddress, tLocation, eStatus, dAddedDate, tPrice, tPriceRatio FROM parking_space WHERE iUserId = '{$GeneralMemberId}' {$filterquery} ORDER BY dAddedDate LIMIT {$start_limit}, {$per_page} ");

            $parkingSpacesArr = [];
            foreach ($parking_spaces as $space) {
                $tPriceRatio = $space['tPriceRatio'];
                $tPriceRatio = json_decode($tPriceRatio, true);

                $spaceArr = [];
                $spaceArr['iParkingSpaceId'] = $space['iParkingSpaceId'];
                $spaceArr['vParkingSpaceNo'] = $space['vParkingSpaceNo'];
                $spaceArr['tAddress'] = $space['tAddress'];
                if ('Pending' === $space['eStatus']) {
                    $spaceArr['status'] = $languageLabelsArr['LBL_PARKING_PENDING_STATUS_TXT'];
                    $spaceArr['StatusBgcolor'] = '#EF9007';
                } elseif ('Approved' === $space['eStatus']) {
                    $spaceArr['status'] = $languageLabelsArr['LBL_PARKING_APPROVED_STATUS_TXT'];
                    $spaceArr['StatusBgcolor'] = '#008000';
                } elseif ('Declined' === $space['eStatus']) {
                    $spaceArr['status'] = $languageLabelsArr['LBL_PARKING_DECLINED_STATUS_TXT'];
                    $spaceArr['StatusBgcolor'] = '#CA3939';
                }

                $spaceArr['dAddedDate'] = DateTime(converToTz($space['dAddedDate'], $vTimeZone, $serverTimeZone), 22);
                $tPrice = $space['tPrice'] * $tPriceRatio['fRatio_'.$vCurrencyPassenger];
                $spaceArr['Price'] = formateNumAsPerCurrency($tPrice, $vCurrencyPassenger);
                $spaceArr['PriceSubText'] = $languageLabelsArr['LBL_PARKING_PER_HOUR_TXT'];

                $parkingSpacesArr[] = $spaceArr;
            }

            $returnArr['Action'] = '1';
            $returnArr['message'] = $parkingSpacesArr;
            if ($totalPages > $page) {
                $returnArr['NextPage'] = ($page + 1);
            } else {
                $returnArr['NextPage'] = '0';
            }
        } else {
            $returnArr['Action'] = '0';
            $returnArr['message'] = 'LBL_NO_PUBLISHED_PARKING_SPACES_FOUND_TXT';
            $returnArr['message_title'] = 'LBL_NO_PUBLISHED_PARKING_SPACES_FOUND_TITLE';
        }

        $FilterArr = [];
        $FilterArr[] = ['vFilterParam' => 'All', 'vTitle' => $languageLabelsArr['LBL_ALL']];
        $FilterArr[] = ['vFilterParam' => 'Pending', 'vTitle' => $languageLabelsArr['LBL_PARKING_PENDING_STATUS_TXT']];
        $FilterArr[] = ['vFilterParam' => 'Approved', 'vTitle' => $languageLabelsArr['LBL_PARKING_APPROVED_STATUS_TXT']];
        $FilterArr[] = ['vFilterParam' => 'Declined', 'vTitle' => $languageLabelsArr['LBL_PARKING_DECLINED_STATUS_TXT']];
        $returnArr['FilterOption'] = $FilterArr;
        $returnArr['eFilterSel'] = !empty($vFilterParam) ? $vFilterParam : 'All';

        setDataResponse($returnArr);
    }

    public function GetParkingVehicleSize($returnData = 'No')
    {
        global $obj, $tconfig, $oCache;

        $vLang = $_REQUEST['vGeneralLang'] ?? '';

        $ParkingVehicleSizeApcKey = md5('PARKING_VEHICLE_SIZE');
        $getParkingVehicleSizeCacheData = $oCache->getData($ParkingVehicleSizeApcKey);
        if (!empty($getParkingVehicleSizeCacheData) && \count($getParkingVehicleSizeCacheData) > 0) {
            $vehicle_size = $getParkingVehicleSizeCacheData;
        } else {
            $vehicle_size = $obj->MySQLSelect("SELECT iParkingVehicleSizeId, tTitle, tSubtitle, tInfo, vImage FROM parking_vehicle_size WHERE eStatus = 'Active' ORDER BY iDisplayOrder");
            $oCache->setData($ParkingVehicleSizeApcKey, $vehicle_size);
        }

        $vehicle_size_arr = [];

        foreach ($vehicle_size as $size) {
            $tTitleArr = !empty($size['tTitle']) ? json_decode($size['tTitle'], true) : [];
            $tTitle = !empty($tTitleArr) ? $tTitleArr['tTitle_'.$vLang] : '';

            $tSubtitleArr = !empty($size['tSubtitle']) ? json_decode($size['tSubtitle'], true) : [];
            $tSubtitle = !empty($tSubtitleArr) ? $tSubtitleArr['tSubtitle_'.$vLang] : '';

            $tInfoArr = !empty($size['tInfo']) ? json_decode($size['tInfo'], true) : [];
            $tInfo = !empty($tInfoArr) ? $tInfoArr['tInfo_'.$vLang] : '';

            $vImage = '';
            if (file_exists($tconfig['tsite_upload_parking_space_vehicle_size_images_path'].'/'.$size['vImage'])) {
                $vImage = $tconfig['tsite_upload_parking_space_vehicle_size_images'].'/'.$size['vImage'];
            }
            $vehicle_size_arr[] = [
                'iParkingVehicleSizeId' => $size['iParkingVehicleSizeId'],
                'tTitle' => $tTitle,
                'tSubtitle' => $tSubtitle,
                'tInfo' => $tInfo,
                'vImage' => $vImage,
            ];
        }

        $ParkingDurationApcKey = md5('PARKING_DURATIONS');
        $getParkingDurationCacheData = $oCache->getData($ParkingDurationApcKey);
        if (!empty($getParkingDurationCacheData) && \count($getParkingDurationCacheData) > 0) {
            $duration = $getParkingDurationCacheData;
        } else {
            $duration = $obj->MySQLSelect("SELECT iDurationId, tDuration, iDurationVal FROM parking_durations WHERE eStatus = 'Active' ORDER BY iDisplayOrder");
            $oCache->setData($ParkingDurationApcKey, $duration);
        }

        $duration_arr = [];
        foreach ($duration as $d) {
            $tDurationArr = !empty($d['tDuration']) ? json_decode($d['tDuration'], true) : [];
            $tDuration = !empty($tDurationArr) ? $tDurationArr['tDuration_'.$vLang] : '';

            $duration_arr[] = [
                'iDurationId' => $d['iDurationId'],
                'tDuration' => $tDuration,
                'iDurationVal' => $d['iDurationVal'],
            ];
        }
        $returnArr['Action'] = '1';
        $returnArr['message'] = $vehicle_size_arr;
        $returnArr['Duration'] = $duration_arr;

        if ('Yes' === $returnData) {
            return $returnArr;
        }
        setDataResponse($returnArr);
    }

    public function FetchAvailableParkingSpace(): void
    {
        global $obj, $LIST_PARKING_SPACE_LIMIT_BY_DISTANCE, $LANG_OBJ;

        $GeneralMemberId = $_REQUEST['GeneralMemberId'] ?? '';
        $vLang = $_REQUEST['vGeneralLang'] ?? '';
        $iParkingVehicleSizeId = $_REQUEST['iParkingVehicleSizeId'] ?? '';
        $ArrivalDate = $_REQUEST['ArrivalDate'] ?? '';
        $vLatitude = $_REQUEST['vLatitude'] ?? '';
        $vLongitude = $_REQUEST['vLongitude'] ?? '';
        $iDurationId = $_REQUEST['iDurationId'] ?? '';
        $page = isset($_REQUEST['page']) ? trim($_REQUEST['page']) : 1;

        $vDay = date('l', strtotime($ArrivalDate));
        $durationArr = $this->GetParkingVehicleSize('Yes')['Duration'];
        $durationArr = array_values(array_filter($durationArr, static fn ($item) => $item['iDurationId'] === $iDurationId));

        $iDuration = 0;
        if (!empty($durationArr) && \count($durationArr) > 0) {
            $iDuration = $durationArr[0]['iDurationVal'];
        }

        $userData = $obj->MySQLSelect("SELECT vCurrencyPassenger FROM register_user WHERE iUserId = '{$GeneralMemberId}' ");
        $vCurrencyPassenger = $userData[0]['vCurrencyPassenger'];
        $eUnit = getMemberCountryUnit($GeneralMemberId, 'Passenger');

        $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLang, '1', '');

        $searchSql = 'SELECT *,
                    ROUND(( 6371 * acos( cos( radians('.$vLatitude.') )
                    * cos( radians( ROUND(vLatitude, 8) ) )
                    * cos( radians( ROUND(vLongitude, 8) ) - radians('.$vLongitude.') )
                    + sin( radians('.$vLatitude.") )
                    * sin( radians( ROUND(vLatitude, 8) ) ) ) ), 2) AS distance
                    FROM parking_space
                    WHERE iParkingVehicleSizeId <= '{$iParkingVehicleSizeId}' AND iParkingSpaceAvailableNo > 0 AND eStatus = 'Approved' AND iUserId != '{$GeneralMemberId}'
                    HAVING distance <= ".$LIST_PARKING_SPACE_LIMIT_BY_DISTANCE.' ORDER BY tPrice ';

        $published_parking_spaces = $obj->MySQLSelect($searchSql);

        $returnArr['Action'] = '0';

        $parkingSpaceArr = [];
        foreach ($published_parking_spaces as $k => $parking_space) {
            // $isAvailable = $this->checkParkingSpaceAvailability($parking_space['iParkingSpaceId'], $vDay, $duration);
            // if($isAvailable == "No")
            //     continue;

            $tPriceRatio = $parking_space['tPriceRatio'];
            $tPriceRatio = json_decode($tPriceRatio, true);

            $parkingSpaceArr[$k]['iParkingSpaceId'] = $parking_space['iParkingSpaceId'];
            $parkingSpaceArr[$k]['tAddress'] = $parking_space['tAddress'];
            $parkingSpaceArr[$k]['vLatitude'] = $parking_space['vLatitude'];
            $parkingSpaceArr[$k]['vLongitude'] = $parking_space['vLongitude'];
            $parkingSpaceArr[$k]['tInstructions'] = $parking_space['tInstructions'];
            if ('MILES' === strtoupper($eUnit)) {
                $parkingSpaceArr[$k]['distance'] = $parking_space['distance'] * KM_TO_MILES_RATIO;
                $parkingSpaceArr[$k]['DistanceSubText'] = $languageLabelsArr['LBL_PARKING_MILES_DISTANCE_TXT'];
            } else {
                $parkingSpaceArr[$k]['distance'] = $parking_space['distance'];
                $parkingSpaceArr[$k]['DistanceSubText'] = $languageLabelsArr['LBL_PARKING_KM_DISTANCE_TXT'];
            }

            $parkingSpaceArr[$k]['tPrice'] = formateNumAsPerCurrency($parking_space['tPrice'] * $tPriceRatio['fRatio_'.$vCurrencyPassenger], $vCurrencyPassenger);
            $parkingSpaceArr[$k]['tPriceSubText'] = $languageLabelsArr['LBL_PARKING_PER_HOUR_TXT'];
            $parkingSpaceArr[$k]['tPriceShortSubText'] = $languageLabelsArr['LBL_PARKING_PER_HOUR_SHORT_XT'];
            $parkingSpaceArr[$k]['ParkingSpaceImages'] = $this->getParkingSpaceImages($parking_space['iUserId'], $parking_space['iParkingSpaceId']);
            $parkingSpaceArr[$k]['vAvgRating'] = round(random_int(4 * 10, 5 * 10) / 10, 1);
            $parkingSpaceArr[$k]['TotalRatings'] = '10 '.$languageLabelsArr['LBL_RATINGS_REWARD'];
        }

        // $total = count($parkingSpaceArr);
        // $per_page = 20;
        // $totalPages = ceil($total / $per_page);
        // $start_limit = ($page - 1) * $per_page;
        // $parkingSpaceArr = array_slice($parkingSpaceArr, $start_limit, $per_page);
        // if ($totalPages > $page) {
        //     $returnArr['NextPage'] = ($page + 1);
        // } else {
        //     $returnArr['NextPage'] = "0";
        // }

        if (\count($parkingSpaceArr) > 0) {
            $returnArr['Action'] = '1';
            $returnArr['message'] = $parkingSpaceArr;
        } else {
            $returnArr['Action'] = '0';
            $returnArr['message'] = 'LBL_PARKING_SPACE_AVAILABLE_NOT_FOUND';
            $returnArr['message_title'] = 'LBL_PARKING_SPACE_AVAILABLE_NOT_FOUND_TITLE';
        }

        setDataResponse($returnArr);
    }

    public function FetchParkingSpaceDetails(): void
    {
        global $obj, $LANG_OBJ;

        $GeneralMemberId = $_REQUEST['GeneralMemberId'] ?? '';
        $vLang = $_REQUEST['vGeneralLang'] ?? '';
        $iParkingSpaceId = $_REQUEST['iParkingSpaceId'] ?? '';
        $ArrivalDate = $_REQUEST['ArrivalDate'] ?? '';
        $vLatitude = $_REQUEST['vLatitude'] ?? '';
        $vLongitude = $_REQUEST['vLongitude'] ?? '';
        $iDurationId = $_REQUEST['iDurationId'] ?? '';
        $vTimeZone = $_REQUEST['vTimeZone'] ?? '';

        $vDay = date('l', strtotime($ArrivalDate));
        $durationArr = $this->GetParkingVehicleSize('Yes')['Duration'];
        $durationArr = array_values(array_filter($durationArr, static fn ($item) => $item['iDurationId'] === $iDurationId));

        $iDuration = 0;
        $Duration = '';
        if (!empty($durationArr) && \count($durationArr) > 0) {
            $iDuration = $durationArr[0]['iDurationVal'];
            $Duration = $durationArr[0]['tDuration'];
        }

        $userData = $obj->MySQLSelect("SELECT vCurrencyPassenger FROM register_user WHERE iUserId = '{$GeneralMemberId}' ");
        $vCurrencyPassenger = $userData[0]['vCurrencyPassenger'];
        $eUnit = getMemberCountryUnit($GeneralMemberId, 'Passenger');

        $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLang, '1', '');

        $systemTimeZone = date_default_timezone_get();

        $fromDate = date('Y-m-d H:i:s', strtotime($ArrivalDate));
        $fromDateConvert = converToTz($fromDate, $systemTimeZone, $vTimeZone);
        $fromDateTxt = date('d M', strtotime($fromDateConvert)).' '.$languageLabelsArr['LBL_AT_TXT'].' '.date('h:i A', strtotime($fromDateConvert));

        $toDate = date('Y-m-d H:i:s', strtotime("+{$iDuration} hours", strtotime($ArrivalDate)));
        $toDateConvert = converToTz($toDate, $systemTimeZone, $vTimeZone);
        $toDateTxt = date('d M', strtotime($toDateConvert)).' '.$languageLabelsArr['LBL_AT_TXT'].' '.date('h:i A', strtotime($toDateConvert));

        $sql = 'SELECT *,
                    ROUND(( 6371 * acos( cos( radians('.$vLatitude.') )
                    * cos( radians( ROUND(vLatitude, 8) ) )
                    * cos( radians( ROUND(vLongitude, 8) ) - radians('.$vLongitude.') )
                    + sin( radians('.$vLatitude.") )
                    * sin( radians( ROUND(vLatitude, 8) ) ) ) ), 2) AS distance
                    FROM parking_space
                    WHERE iParkingSpaceId = '{$iParkingSpaceId}' ";

        $parking_space_details = $obj->MySQLSelect($sql);

        $tPriceRatio = $parking_space_details[0]['tPriceRatio'];
        $tPriceRatio = json_decode($tPriceRatio, true);
        $fRatio = $tPriceRatio['fRatio_'.$vCurrencyPassenger];

        $tPrice = $parking_space_details[0]['tPrice'];

        $parkingSpaceArr = [];
        $parkingSpaceArr['iParkingSpaceId'] = $parking_space_details[0]['iParkingSpaceId'];
        $parkingSpaceArr['tAddress'] = $parking_space_details[0]['tAddress'];
        $parkingSpaceArr['vLatitude'] = $parking_space_details[0]['vLatitude'];
        $parkingSpaceArr['vLongitude'] = $parking_space_details[0]['vLongitude'];
        $parkingSpaceArr['tInstructions'] = $parking_space_details[0]['tInstructions'];
        $parkingSpaceArr['iParkingVehicleSizeId'] = $parking_space_details[0]['iParkingVehicleSizeId'];

        $parkingSpaceArr['ParkingFromTitle'] = $languageLabelsArr['LBL_PARKING_FROM_DURATION_TXT'];
        $parkingSpaceArr['ParkingFromDateTime'] = $fromDateTxt;
        $parkingSpaceArr['ParkingToTitle'] = $languageLabelsArr['LBL_PARKING_TO_DURATION_TXT'];
        $parkingSpaceArr['ParkingToDateTime'] = $toDateTxt;

        $parkingSpaceArr['Duration'] = $Duration;
        $parkingSpaceArr['DurationSubText'] = $languageLabelsArr['LBL_PARKING_TOTAL_DURATION_TXT'];

        $parkingSpaceArr['tPrice'] = formateNumAsPerCurrency($tPrice * $fRatio, $vCurrencyPassenger).' '.$languageLabelsArr['LBL_PARKING_PER_HOUR_SHORT_XT'];
        $parkingSpaceArr['tPriceSubText'] = $languageLabelsArr['LBL_PARKING_FEE_TXT'];

        if ('MILES' === strtoupper($eUnit)) {
            $parkingSpaceArr['distance'] = setTwoDecimalPoint($parking_space_details[0]['distance'] * KM_TO_MILES_RATIO).' '.$languageLabelsArr['LBL_MILE_DISTANCE_TXT'];
        } else {
            $parkingSpaceArr['distance'] = setTwoDecimalPoint($parking_space_details[0]['distance']).' '.$languageLabelsArr['LBL_KM_DISTANCE_TXT'];
        }
        $parkingSpaceArr['DistanceSubText'] = $languageLabelsArr['LBL_PARKING_TO_DESTINATION_TXT'];

        $parkingSpaceArr['ParkingSpaceImages'] = $this->getParkingSpaceImages($parking_space_details[0]['iUserId'], $parking_space_details[0]['iParkingSpaceId']);

        $ParkingSpaceReviews = $this->GetParkingSpaceReviews($parking_space_details[0]['iParkingSpaceId']);
        $parkingSpaceArr['ParkingSpaceReviews'] = $ParkingSpaceReviews['reviews'];
        $parkingSpaceArr['vAvgRating'] = $ParkingSpaceReviews['AvgRating'];

        $TotalParkingFees = $tPrice * $iDuration;
        $TransactionFee = ($TotalParkingFees * self::PARKING_FEE) / 100;
        $FinalParkingFare = $TotalParkingFees + $TransactionFee;

        $parkingSpaceArr['PriceInfo'] = [
            [
                $languageLabelsArr['LBL_PARKING_FEE_TXT'] => formateNumAsPerCurrency($TotalParkingFees * $fRatio, $vCurrencyPassenger),
            ],
            [
                'eDisplaySeperator' => 'Yes',
            ],
            [
                $languageLabelsArr['LBL_PARKING_TRANSACTION_FEE_TXT'] => formateNumAsPerCurrency($TransactionFee * $fRatio, $vCurrencyPassenger),
            ],
            [
                'eDisplaySeperator' => 'Yes',
            ],
            [
                $languageLabelsArr['LBL_PARKING_FINAL_FARE_TXT'] => formateNumAsPerCurrency($FinalParkingFare * $fRatio, $vCurrencyPassenger),
            ],
        ];

        $parkingSpaceArr['ParkingUserVehicles'] = $this->GetUserVehicleForParkingSpace('Yes');

        $returnArr['Action'] = '1';
        $returnArr['message'] = $parkingSpaceArr;
        setDataResponse($returnArr);
    }

    public function AddUserVehicleForParkingSpace(): void
    {
        global $obj, $tconfig, $UPLOAD_OBJ;

        $GeneralMemberId = $_REQUEST['GeneralMemberId'] ?? '';
        $iParkingVehicleId = $_REQUEST['iParkingVehicleId'] ?? '0';
        $vMake = $_REQUEST['vMake'] ?? '';
        $vModel = $_REQUEST['vModel'] ?? '';
        $iParkingVehicleSizeId = $_REQUEST['iParkingVehicleSizeId'] ?? '';
        $vCarNumberPlate = $_REQUEST['vCarNumberPlate'] ?? '';
        $vCarColor = $_REQUEST['vCarColor'] ?? '';
        $image_name = $vImage = $_FILES['vImage']['name'] ?? '';
        $image_object = $_FILES['vImage']['tmp_name'] ?? '';

        $vCarImage = '';
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
            $Photo_Gallery_folder = $tconfig['tsite_upload_images_parking_user_vehicle_path'].'/';
            if (!is_dir($Photo_Gallery_folder)) {
                mkdir($Photo_Gallery_folder, 0777);
                chmod($Photo_Gallery_folder, 0777);
            }
            $img1 = $UPLOAD_OBJ->GeneralFileUpload($Photo_Gallery_folder, $image_object, $image_name, '', 'jpg,png,gif,jpeg,bmp');
            $vCarImage = $img1[0];
        }

        $Data_insert_vehicle = [];
        $Data_insert_vehicle['iUserId'] = $GeneralMemberId;
        $Data_insert_vehicle['vMake'] = $vMake;
        $Data_insert_vehicle['vModel'] = $vModel;
        $Data_insert_vehicle['iParkingVehicleSizeId'] = $iParkingVehicleSizeId;
        $Data_insert_vehicle['vCarNumberPlate'] = $vCarNumberPlate;
        $Data_insert_vehicle['vCarColor'] = $vCarColor;
        $Data_insert_vehicle['eStatus'] = 'Active';

        if ($iParkingVehicleId > 0) {
            if (!empty($vCarImage)) {
                $Data_insert_vehicle['vImage'] = $vCarImage;
            }
            $where = " iParkingVehicleId = '{$iParkingVehicleId}' ";
            $iParkingVehicleId = $obj->MySQLQueryPerform('parking_user_vehicle', $Data_insert_vehicle, 'update', $where);
            $returnArr['message'] = 'LBL_VEHICLE_UPDATE_SUCCESS';
        } else {
            $Data_insert_vehicle['vImage'] = $vCarImage;
            $iParkingVehicleId = $obj->MySQLQueryPerform('parking_user_vehicle', $Data_insert_vehicle, 'insert');
            $returnArr['message'] = 'LBL_VEHICLE_ADD_SUCCESS';
        }

        $returnArr['Action'] = '1';
        $returnArr['ParkingUserVehicles'] = $this->GetUserVehicleForParkingSpace('Yes');
        setDataResponse($returnArr);
    }

    public function GetUserVehicleForParkingSpace($returnData = 'No', $iParkingVehicleIdVal = 0)
    {
        global $obj, $tconfig, $LANG_OBJ;

        $GeneralMemberId = $_REQUEST['GeneralMemberId'] ?? '';
        $vLang = $_REQUEST['vGeneralLang'] ?? '';
        $iParkingVehicleId = $_REQUEST['iParkingVehicleId'] ?? '';
        $iParkingVehicleSizeId = $_REQUEST['iParkingVehicleSizeId'] ?? '';

        $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLang, '1', '');

        $vehicle_size = $obj->MySQLSelect("SELECT JSON_UNQUOTE(JSON_VALUE(tTitle, '$.tTitle_{$vLang}')) as tTitle FROM parking_vehicle_size WHERE iParkingVehicleSizeId = '{$iParkingVehicleSizeId}'");

        $ssql = '';
        if ($iParkingVehicleIdVal > 0) {
            $ssql = " AND puv.iParkingVehicleId = '{$iParkingVehicleId}' ";
        }
        $vehicles_arr = $obj->MySQLSelect("SELECT puv.*, pvs.tTitle FROM parking_user_vehicle as puv LEFT JOIN parking_vehicle_size as pvs ON pvs.iParkingVehicleSizeId = puv.iParkingVehicleSizeId WHERE puv.iUserId = '{$GeneralMemberId}' AND puv.eStatus = 'Active' {$ssql} ");

        $UserVehicleArr = [];
        foreach ($vehicles_arr as $k => $vehicle) {
            $tTitleArr = !empty($vehicle['tTitle']) ? json_decode($vehicle['tTitle'], true) : [];
            $tTitle = !empty($tTitleArr) ? $tTitleArr['tTitle_'.$vLang] : '';

            $UserVehicleArr[$k]['iParkingVehicleId'] = $vehicle['iParkingVehicleId'];
            $UserVehicleArr[$k]['vMake'] = $vehicle['vMake'];
            $UserVehicleArr[$k]['vModel'] = $vehicle['vModel'];
            $UserVehicleArr[$k]['iParkingVehicleSizeId'] = $vehicle['iParkingVehicleSizeId'];
            $UserVehicleArr[$k]['VehicleMakeModel'] = $vehicle['vMake'].' / '.$vehicle['vModel'];
            $UserVehicleArr[$k]['vCarNumberPlate'] = $vehicle['vCarNumberPlate'];
            $UserVehicleArr[$k]['vCarSize'] = $tTitle;
            $UserVehicleArr[$k]['vCarColor'] = $vehicle['vCarColor'];
            $UserVehicleArr[$k]['vImage'] = '';
            if (!empty($vehicle['vImage'])) {
                $UserVehicleArr[$k]['vImage'] = $tconfig['tsite_upload_images_parking_user_vehicle'].'/'.$vehicle['vImage'];
            }

            $UserVehicleArr[$k]['isSelected'] = 'No';

            $tTitleSize = $vehicle_size[0]['tTitle'];
            $UserVehicleArr[$k]['Note'] = '';
            $UserVehicleArr[$k]['isAllowSelection'] = 'Yes';
            if ($vehicle['iParkingVehicleSizeId'] > $iParkingVehicleSizeId) {
                $UserVehicleArr[$k]['Note'] = str_replace('#VEHICLE_SIZE#', $tTitleSize, $languageLabelsArr['LBL_PARKING_INVALID_VEHICLE_SELECTED_MSG']);
                $UserVehicleArr[$k]['isAllowSelection'] = 'No';
            } elseif ($vehicle['iParkingVehicleSizeId'] <= $iParkingVehicleSizeId) {
                $UserVehicleArr[$k]['isSelected'] = $vehicle['eSelected'];
            }
        }

        if ('Yes' === $returnData) {
            return $UserVehicleArr;
        }

        if (!empty($UserVehicleArr) && \count($UserVehicleArr) > 0) {
            $returnArr['Action'] = '1';
            $returnArr['message'] = $UserVehicleArr;
        } else {
            $returnArr['Action'] = '0';
            $returnArr['message'] = 'LBL_PARKING_NO_USER_VEHICLES_FOUND';
            $returnArr['message_title'] = 'LBL_PARKING_NO_USER_VEHICLES_FOUND_TITLE';
        }
        setDataResponse($returnArr);
    }

    public function DeleteUserVehicleForParkingSpace(): void
    {
        global $obj;

        $GeneralMemberId = $_REQUEST['GeneralMemberId'] ?? '';
        $iParkingVehicleId = $_REQUEST['iParkingVehicleId'] ?? '';

        $obj->sql_query("UPDATE parking_user_vehicle SET eStatus = 'Deleted' WHERE iParkingVehicleId = '{$iParkingVehicleId}'");

        $returnArr['Action'] = '1';
        $returnArr['message'] = 'LBL_DELETE_VEHICLE';
        $returnArr['ParkingUserVehicles'] = $this->GetUserVehicleForParkingSpace('Yes');
        setDataResponse($returnArr);
    }

    public function SelectUserVehicleForParkingSpace(): void
    {
        global $obj, $LANG_OBJ;

        $GeneralMemberId = $_REQUEST['GeneralMemberId'] ?? '';
        $iParkingVehicleId = $_REQUEST['iParkingVehicleId'] ?? '';
        $iParkingVehicleSizeId = $_REQUEST['iParkingVehicleSizeId'] ?? '';
        $vLang = $_REQUEST['vGeneralLang'] ?? '';

        $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLang, '1', '');

        $checkVehicle = $obj->MySQLSelect("SELECT iParkingVehicleId FROM parking_user_vehicle WHERE iParkingVehicleId = '{$iParkingVehicleId}' AND iParkingVehicleSizeId <= '{$iParkingVehicleSizeId}'");
        if (!empty($checkVehicle) && \count($checkVehicle) > 0) {
            $obj->sql_query("UPDATE parking_user_vehicle SET eSelected = CASE WHEN iParkingVehicleId = '{$iParkingVehicleId}' THEN 'Yes' WHEN iParkingVehicleId != '{$iParkingVehicleId}' THEN 'No' ELSE eSelected END WHERE iUserId = '{$GeneralMemberId}'");

            $returnArr['Action'] = '1';
            $returnArr['message'] = $this->GetUserVehicleForParkingSpace('Yes', $iParkingVehicleId)[0];
        } else {
            $vehicle_size_arr = $this->GetParkingVehicleSize('Yes')['message'];
            $key = array_search($iParkingVehicleSizeId, array_column($vehicle_size_arr, 'iParkingVehicleSizeId'), true);
            $vehicle_size = $vehicle_size_arr[$key];

            $message = str_replace('#VEHICLE_SIZE#', $vehicle_size['tTitle'], $languageLabelsArr['LBL_PARKING_INVALID_VEHICLE_SELECTED_MSG']);
            $returnArr['Action'] = '0';
            $returnArr['message'] = $message;
        }

        setDataResponse($returnArr);
    }

    public function ReserveParking(): void
    {
        global $obj, $LANG_OBJ;

        $GeneralMemberId = $_REQUEST['GeneralMemberId'] ?? '';
        $vLang = $_REQUEST['vGeneralLang'] ?? '';
        $iParkingSpaceId = $_REQUEST['iParkingSpaceId'] ?? '';
        $ArrivalDate = $_REQUEST['ArrivalDate'] ?? '';
        $iDurationId = $_REQUEST['iDurationId'] ?? '';
        $iParkingVehicleId = $_REQUEST['iParkingVehicleId'] ?? '';
        $vUserName = $_REQUEST['vUserName'] ?? '';
        $vUserPhone = $_REQUEST['vUserPhone'] ?? '';
        $vTimeZone = $_REQUEST['vTimeZone'] ?? '';

        $params = ['iMemberId' => $iUserId, 'eUserType' => 'Passenger', 'eType' => 'Parking', 'GET_DATA' => 'Yes'];
        $payment_mode_data = GetPaymentModeDetails($params);
        $ePaymentMode = !empty($payment_mode_data['PaymentMode']) ? $payment_mode_data['PaymentMode'] : 'cash';
        $cashPayment = 'cash' === $ePaymentMode ? 'Yes' : 'No';
        $ePayWallet = 'wallet' === $ePaymentMode ? 'Yes' : 'No';
        $eWalletDebitAllow = 'wallet' === $ePaymentMode ? 'Yes' : ('Yes' === $payment_mode_data['eWalletDebit'] ? 'Yes' : 'No');
        $isRestrictToWallet = $payment_mode_data['PAYMENT_MODE_RESTRICT_TO_WALLET'];

        $vDay = date('l', strtotime($ArrivalDate));
        $durationArr = $this->GetParkingVehicleSize('Yes')['Duration'];
        $durationArr = array_values(array_filter($durationArr, static fn ($item) => $item['iDurationId'] === $iDurationId));

        $iDuration = 0;
        $Duration = '';
        if (!empty($durationArr) && \count($durationArr) > 0) {
            $iDuration = $durationArr[0]['iDurationVal'];
            $Duration = $durationArr[0]['tDuration'];
        }

        $userData = $obj->MySQLSelect("SELECT vCurrencyPassenger FROM register_user WHERE iUserId = '{$GeneralMemberId}' ");
        $vCurrencyPassenger = $userData[0]['vCurrencyPassenger'];

        $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLang, '1', '');

        $systemTimeZone = date_default_timezone_get();
        $fromDate = date('Y-m-d H:i:s', strtotime($ArrivalDate));
        $fromDateConvert = converToTz($fromDate, $systemTimeZone, $vTimeZone);

        $parking_space_details = $obj->MySQLSelect("SELECT tPrice, tPriceRatio FROM parking_space WHERE iParkingSpaceId = '{$iParkingSpaceId}'");
        $tPrice = $parking_space_details[0]['tPrice'];

        $TotalParkingFees = $tPrice * $iDuration;
        $TransactionFee = ($TotalParkingFees * PARKING_FEE) / 100;
        $FinalParkingFare = $TotalParkingFees + $TransactionFee;

        $Data_reserve = [];
        $Data_reserve['vParkingSpaceBookingNo'] = $this->GenerateUniqueNo('ParkingSpaceBooking');
        $Data_reserve['iUserId'] = $GeneralMemberId;
        $Data_reserve['iParkingSpaceId'] = $iParkingSpaceId;
        $Data_reserve['dBookingDate'] = date('Y-m-d H:i:s');
        $Data_reserve['dArrivalDate'] = $fromDateConvert;
        $Data_reserve['iDurationId'] = $iDurationId;
        $Data_reserve['ePaymentOption'] = ucwords($ePaymentMode);
        $Data_reserve['fPricePerHour'] = $tPrice;
        $Data_reserve['tPriceRatio'] = $parking_space_details[0]['tPriceRatio'];
        $Data_reserve['iParkingVehicleId'] = $iParkingVehicleId;
        $Data_reserve['tUserDetails'] = getJsonFromAnArrWithoutClean(
            [
                'vUserName' => $vUserName,
                'vUserPhone' => $vUserPhone,
            ]
        );
        $Data_reserve['fTotalParkingFee'] = $TotalParkingFees;
        $Data_reserve['fTransactionFee'] = $TransactionFee;
        $Data_reserve['fTotalFare'] = $FinalParkingFare;

        $iParkingBookingId = $obj->MySQLQueryPerform('parking_reserved_bookings', $Data_reserve, 'insert');

        $obj->MySQLSelect("UPDATE parking_space SET iParkingSpaceAvailableNo = (iParkingSpaceAvailableNo - 1) WHERE iParkingSpaceId = '{$iParkingSpaceId}' ");

        $returnArr['Action'] = '1';
        $returnArr['message'] = 'Parking reserved successfully.';
        setDataResponse($returnArr);
    }

    public function FetchParkingSpaceBookings(): void
    {
        global $obj, $tconfig, $LANG_OBJ;

        $GeneralMemberId = $_REQUEST['GeneralMemberId'] ?? '';
        $vTimeZone = $_REQUEST['vTimeZone'] ?? '';
        $vFilterParam = $_REQUEST['vFilterParam'] ?? '';
        $page = isset($_REQUEST['page']) ? trim($_REQUEST['page']) : 1;

        $userData = $obj->MySQLSelect("SELECT vLang, vCurrencyPassenger FROM register_user WHERE iUserId = '{$GeneralMemberId}' ");
        $vLang = $userData[0]['vLang'];
        $vCurrencyPassenger = $userData[0]['vCurrencyPassenger'];

        $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLang, '1', '');

        $systemTimeZone = date_default_timezone_get();

        $parking_space_bookings = $obj->MySQLSelect("SELECT psb.iParkingBookingId, psb.vParkingSpaceBookingNo, psb.iParkingSpaceId, psb.dBookingDate, psb.dArrivalDate, psb.iDurationId, psb.ePaymentOption, psb.fTotalParkingFee, psb.fTransactionFee, psb.fTotalFare, psb.tPriceRatio, psb.tUserDetails, psb.eStatus, psb.iCancelReasonId, psb.tCancelReason, ps.tAddress, ps.vLatitude, ps.vLongitude, puv.vMake, puv.vModel, puv.vCarColor, puv.vCarNumberPlate, puv.vImage FROM parking_reserved_bookings as psb LEFT JOIN parking_space as ps ON ps.iParkingSpaceId = psb.iParkingSpaceId LEFT JOIN parking_user_vehicle as puv ON puv.iParkingVehicleId = psb.iParkingVehicleId WHERE psb.iUserId = '{$GeneralMemberId}' ");

        $bookingsArr = [];
        if (!empty($parking_space_bookings) && \count($parking_space_bookings) > 0) {
            foreach ($parking_space_bookings as $k => $booking) {
                $tPriceRatio = $booking['tPriceRatio'];
                $tPriceRatio = json_decode($tPriceRatio, true);
                $fRatio = $tPriceRatio['fRatio_'.$vCurrencyPassenger];

                $iDurationId = $booking['iDurationId'];
                $vDay = date('l', strtotime($ArrivalDate));
                $durationArr = $this->GetParkingVehicleSize('Yes')['Duration'];
                $durationArr = array_values(array_filter($durationArr, static fn ($item) => $item['iDurationId'] === $iDurationId));

                $iDuration = 0;
                $Duration = '';
                if (!empty($durationArr) && \count($durationArr) > 0) {
                    $iDuration = $durationArr[0]['iDurationVal'];
                    $Duration = $durationArr[0]['tDuration'];
                }

                $fromDateConvert = converToTz($booking['dArrivalDate'], $vTimeZone, $systemTimeZone);
                $fromDateTxt = date('d M', strtotime($fromDateConvert)).' '.$languageLabelsArr['LBL_AT_TXT'].' '.date('h:i A', strtotime($fromDateConvert));

                $toDate = date('Y-m-d H:i:s', strtotime("+{$iDuration} hours", strtotime($booking['dArrivalDate'])));
                $toDateConvert = converToTz($toDate, $vTimeZone, $systemTimeZone);
                $toDateTxt = date('d M', strtotime($toDateConvert)).' '.$languageLabelsArr['LBL_AT_TXT'].' '.date('h:i A', strtotime($toDateConvert));

                $tUserDetails = json_decode($booking['tUserDetails'], true);

                $bookingsArr[$k]['iParkingBookingId'] = $booking['iParkingBookingId'];
                $bookingsArr[$k]['iParkingSpaceId'] = $booking['iParkingSpaceId'];
                $bookingsArr[$k]['vParkingSpaceBookingNo'] = $booking['vParkingSpaceBookingNo'];
                $bookingsArr[$k]['tAddress'] = $booking['tAddress'];
                $bookingsArr[$k]['vLatitude'] = $booking['vLatitude'];
                $bookingsArr[$k]['vLongitude'] = $booking['vLongitude'];
                $bookingsArr[$k]['BookingDate'] = converToTz($booking['dBookingDate'], $vTimeZone, $systemTimeZone);
                $bookingsArr[$k]['TotalFare'] = formateNumAsPerCurrency($booking['fTotalFare'] * $fRatio, $vCurrencyPassenger);
                $bookingsArr[$k]['FareSubText'] = $languageLabelsArr['LBL_Total_Fare_TXT'];
                $bookingsArr[$k]['ParkingFromTitle'] = $languageLabelsArr['LBL_PARKING_FROM_DURATION_TXT'];
                $bookingsArr[$k]['ParkingFromDateTime'] = $fromDateTxt;
                $bookingsArr[$k]['ParkingToTitle'] = $languageLabelsArr['LBL_PARKING_TO_DURATION_TXT'];
                $bookingsArr[$k]['ParkingToDateTime'] = $toDateTxt;

                $bookingsArr[$k]['Duration'] = $Duration;
                $bookingsArr[$k]['DurationSubText'] = $languageLabelsArr['LBL_PARKING_SELECTED_DURATION_TXT'];

                $bookingsArr[$k]['PriceInfo'] = [
                    [
                        $languageLabelsArr['LBL_PARKING_FEE_TXT'] => formateNumAsPerCurrency($booking['fTotalParkingFee'] * $fRatio, $vCurrencyPassenger),
                    ],
                    [
                        'eDisplaySeperator' => 'Yes',
                    ],
                    [
                        $languageLabelsArr['LBL_PARKING_TRANSACTION_FEE_TXT'] => formateNumAsPerCurrency($booking['fTransactionFee'] * $fRatio, $vCurrencyPassenger),
                    ],
                    [
                        'eDisplaySeperator' => 'Yes',
                    ],
                    [
                        $languageLabelsArr['LBL_PARKING_FINAL_FARE_TXT'] => formateNumAsPerCurrency($booking['fTotalFare'] * $fRatio, $vCurrencyPassenger),
                    ],
                ];

                if ('Cash' === $booking['ePaymentOption']) {
                    $bookingsArr[$k]['PaymentMode'] = $languageLabelsArr['LBL_CASH_TXT'];
                    $bookingsArr[$k]['PaymentModeImg'] = $tconfig['tsite_url'].'webimages/icons/DefaultImg/ic_cash.png';
                } elseif ('Card' === $booking['ePaymentOption']) {
                    $bookingsArr[$k]['PaymentMode'] = $languageLabelsArr['LBL_CARD'];
                    $bookingsArr[$k]['PaymentModeImg'] = $tconfig['tsite_url'].'webimages/icons/DefaultImg/ic_card.png';
                } else {
                    $bookingsArr[$k]['PaymentMode'] = $languageLabelsArr['LBL_WALLET_TXT'];
                    $bookingsArr[$k]['PaymentModeImg'] = $tconfig['tsite_url'].'webimages/icons/DefaultImg/ic_wallet.png';
                }

                $bookingsArr[$k]['CarDetails'] = [
                    'vMake' => $booking['vMake'],
                    'vModel' => $booking['vModel'],
                    'vCarColor' => $booking['vCarColor'],
                    'vCarNumberPlate' => $booking['vCarNumberPlate'],
                    'vImage' => $tconfig['tsite_upload_images_parking_user_vehicle'].'/'.$booking['vImage'],
                ];

                $bookingsArr[$k]['tUserDetails'] = $tUserDetails;
                $bookingsArr[$k]['ShowCancelBtn'] = 'Yes';
                $bookingsArr[$k]['ShowRating'] = 'No';

                $currDate = date('Y-m-d H:i:s');
                if (strtotime($currDate) > strtotime($fromDateConvert) || 'Cancelled' === $booking['eStatus']) {
                    $bookingsArr[$k]['ShowCancelBtn'] = 'No';
                }

                $booking_status = $booking['eStatus'];

                if ('Cancelled' !== $booking['eStatus']) {
                    if (strtotime($currDate) < strtotime($fromDateConvert)) {
                        $booking_status = 'Upcoming';
                    } elseif (strtotime($currDate) >= strtotime($fromDateConvert) && strtotime($currDate) <= strtotime($toDateConvert)) {
                        $booking_status = 'Inprocess';
                        $bookingsArr[$k]['ShowRating'] = 'Yes';
                    } else {
                        $booking_status = 'Completed';
                        $bookingsArr[$k]['ShowRating'] = 'Yes';
                    }
                }

                if ($this->checkRating($booking['iParkingBookingId'])) {
                    $bookingsArr[$k]['ShowRating'] = 'No';
                }

                if ('Upcoming' === $booking_status) {
                    $bookingsArr[$k]['status'] = $languageLabelsArr['LBL_PARKING_UPCOMING_STATUS_TXT'];
                    $bookingsArr[$k]['StatusBgcolor'] = '#1B2A3B';
                } elseif ('Inprocess' === $booking_status) {
                    $bookingsArr[$k]['status'] = $languageLabelsArr['LBL_PARKING_INPROCESS_STATUS_TXT'];
                    $bookingsArr[$k]['StatusBgcolor'] = '#EF9007';
                } elseif ('Completed' === $booking_status) {
                    $bookingsArr[$k]['status'] = $languageLabelsArr['LBL_PARKING_COMPLETED_STATUS_TXT'];
                    $bookingsArr[$k]['StatusBgcolor'] = '#008000';
                } elseif ('Cancelled' === $booking_status) {
                    $bookingsArr[$k]['status'] = $languageLabelsArr['LBL_PARKING_CANCELLED_STATUS_TXT'];
                    $bookingsArr[$k]['StatusBgcolor'] = '#CA3939';
                }

                $bookingsArr[$k]['CancelReason'] = $this->getCancelReason($booking['iCancelReasonId'], $vLang, $booking['tCancelReason']);
            }

            $total = \count($bookingsArr);
            $per_page = 5;
            $totalPages = ceil($total / $per_page);
            $start_limit = ($page - 1) * $per_page;
            $bookingsArr = \array_slice($bookingsArr, $start_limit, $per_page);
            if ($totalPages > $page) {
                $returnArr['NextPage'] = ($page + 1);
            } else {
                $returnArr['NextPage'] = '0';
            }

            $returnArr['Action'] = '1';
            $returnArr['message'] = $bookingsArr;
        } else {
            $returnArr['Action'] = '0';
            $returnArr['message'] = 'LBL_PARKING_NO_BOOKINGS_FOUND_TXT';
            $returnArr['message_title'] = 'LBL_PARKING_NO_BOOKINGS_FOUND_TITLE';
        }

        $FilterArr = [];
        $FilterArr[] = ['vFilterParam' => 'Upcoming', 'vTitle' => $languageLabelsArr['LBL_PARKING_UPCOMING_STATUS_TXT']];
        $FilterArr[] = ['vFilterParam' => 'Inprocess', 'vTitle' => $languageLabelsArr['LBL_PARKING_INPROCESS_STATUS_TXT']];
        $FilterArr[] = ['vFilterParam' => 'Completed', 'vTitle' => $languageLabelsArr['LBL_PARKING_COMPLETED_STATUS_TXT']];
        $FilterArr[] = ['vFilterParam' => 'Cancelled', 'vTitle' => $languageLabelsArr['LBL_PARKING_CANCELLED_STATUS_TXT']];
        $returnArr['FilterOption'] = $FilterArr;
        $returnArr['eFilterSel'] = !empty($vFilterParam) ? $vFilterParam : 'Upcoming';
        setDataResponse($returnArr);
    }

    public function CancelParkingSpaceBooking(): void
    {
        global $obj;

        $iParkingBookingId = $_REQUEST['iParkingBookingId'] ?? '';
        $iCancelReasonId = $_REQUEST['iCancelReasonId'] ?? '0';
        $tCancelReason = $_REQUEST['reason'] ?? '';

        $Data_update = [];
        $Data_update['eStatus'] = 'Cancelled';
        $Data_update['iCancelReasonId'] = $iCancelReasonId;
        $Data_update['tCancelReason'] = $tCancelReason;
        $where = " iParkingBookingId = '{$iParkingBookingId}'";
        $obj->MySQLQueryPerform('parking_reserved_bookings', $Data_update, 'update', $where);

        $returnArr['Action'] = '1';
        $returnArr['message'] = 'LBL_PARKING_BOOKING_CANCELLED';
        setDataResponse($returnArr);
    }

    public function SubmitRating(): void
    {
        global $obj;

        $GeneralMemberId = $_REQUEST['GeneralMemberId'] ?? '';
        $iParkingBookingId = $_REQUEST['iParkingBookingId'] ?? '';
        $iParkingSpaceId = $_REQUEST['iParkingSpaceId'] ?? '';
        $Feedback = $_REQUEST['Feedback'] ?? '';
        $Rating = $_REQUEST['Rating'] ?? '0.0';

        if ($this->checkRating($iParkingBookingId)) {
            $returnArr['Action'] = '1';
            $returnArr['message'] = '';
            setDataResponse($returnArr);
        }

        $Data_insert_rating = [];
        $Data_insert_rating['iParkingBookingId'] = $iParkingBookingId;
        $Data_insert_rating['iParkingSpaceId'] = $iParkingSpaceId;
        $Data_insert_rating['iUserId'] = $GeneralMemberId;
        $Data_insert_rating['vRating'] = $Rating;
        $Data_insert_rating['tFeedback'] = $Feedback;
        $Data_insert_rating['dAddedDate'] = date('Y-m-d H:i:s');

        $obj->MySQLQueryPerform('parking_space_ratings', $Data_insert_rating, 'insert');

        $returnArr['Action'] = '1';
        $returnArr['message'] = '';
        setDataResponse($returnArr);
    }

    private function GenerateUniqueNo($eFor = 'ParkingSpace')
    {
        global $obj;

        $random = substr(number_format(time() * random_int(0, getrandmax()), 0, '', ''), 0, 10);
        if ('ParkingSpace' === $eFor) {
            $db_str = $obj->MySQLSelect("SELECT iParkingSpaceId FROM parking_space WHERE vParkingSpaceNo = '".$random."'");
        } else {
            $db_str = $obj->MySQLSelect("SELECT iParkingBookingId FROM parking_reserved_bookings WHERE vParkingSpaceBookingNo = '".$random."'");
        }

        if (!empty($db_str) && \count($db_str) > 0) {
            $Generateuniqueno = GenerateUniqueNo();
        } else {
            $Generateuniqueno = $random;
        }

        return $Generateuniqueno;
    }

    private function getCancelReason($iCancelReasonId, $vLang, $other_reason)
    {
        global $obj, $oCache;

        $ParkingCancelReasonApcKey = md5('PARKING_CANCEL_REASON_'.$vLang);
        $getParkingCancelReasonCacheData = $oCache->getData($ParkingCancelReasonApcKey);
        if (!empty($getParkingCancelReasonCacheData) && \count($getParkingCancelReasonCacheData) > 0) {
            $cancel_reasons = $getParkingCancelReasonCacheData;
        } else {
            $cancel_reasons = $obj->MySQLSelect("SELECT iCancelReasonId, vTitle_{$vLang} as vTitle FROM cancel_reason WHERE eType = 'Parking' AND eStatus = 'Active' ");
            $oCache->setData($ParkingCancelReasonApcKey, $cancel_reasons);
        }

        foreach ($cancel_reasons as $reason) {
            if ($reason['iCancelReasonId'] === $iCancelReasonId) {
                return $reason['vTitle'];
            }
        }

        return $other_reason;
    }

    private function checkRating($iParkingBookingId)
    {
        global $obj;

        $checkRating = $obj->MySQLSelect("SELECT iParkingSpaceRatingId FROM parking_space_ratings WHERE iParkingBookingId = '{$iParkingBookingId}' ");
        if (!empty($checkRating) && \count($checkRating) > 0) {
            return true;
        }

        return false;
    }

    private function GetParkingSpaceReviews($iParkingSpaceId)
    {
        global $obj, $tconfig;

        $reviews = $obj->MySQLSelect("SELECT psr.vRating, psr.tFeedback, ru.iUserId, ru.vName, ru.vImgName FROM parking_space_ratings as psr LEFT JOIN register_user as ru ON ru.iUserId = psr.iUserId WHERE iParkingSpaceId = '{$iParkingSpaceId}' ");
        $reviewsArr = [];
        $AvgRating = '0.0';
        if (!empty($reviews) && \count($reviews) > 0) {
            $TotalRating = 0;
            foreach ($reviews as $review) {
                $reviewsArr[] = [
                    'vName' => $review['vName'],
                    'Rating' => $review['vRating'],
                    'Message' => $review['tFeedback'],
                    'vImage' => !empty($review['vImgName']) ? $tconfig['tsite_upload_images_passenger'].'/'.$review['iUserId'].'/3_'.$review['vImgName'] : '',
                ];

                $TotalRating += $review['vRating'];
            }

            $AvgRating = round($TotalRating / \count($reviewsArr), 1);
        }

        $reviewsRatingsArr = [
            'reviews' => $reviewsArr,
            'AvgRating' => $AvgRating,
        ];

        return $reviewsRatingsArr;
    }

    private function checkParkingSpaceAvailability($iParkingSpaceId, $vDay): void {}
}
