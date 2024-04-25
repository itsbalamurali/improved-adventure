<?php



namespace Kesk\Web\Common;

use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFProbe;

class RentItem
{
    public function __construct()
    {
        global $obj;
        $this->tablename = 'rent_items_category';
        $this->rentitem_post = 'rentitem_post';
        $this->rentitem_post_tmp = 'rentitem_post_tmp';
        $this->PaymentPlanTableName = 'rent_item_payment_plan';
        $this->rentitem_images = 'rentitem_images';
        $this->rent_item_post_status_log = 'rent_item_post_status_log';
        $this->rent_item_sendquery_log = 'rent_item_sendquery_log';
        $this->rentitem_payment_log = 'rentitem_payment_log';
        $rentitem = $obj->MySQLSelect("SELECT iRentItemId FROM {$this->tablename} WHERE 1 = 1 ");
        $this->other_id = (!empty($rentitem) && $rentitem[0]['iRentItemId']) ? $rentitem[0]['iRentItemId'] : 0;
    }

    public function getRentItemTotalCount($use, $iParent_Id = 0, $cat_id = '', $ssql = '')
    {
        global $obj;
        $estatus = '';
        if ('admin' === $use) {
            if (empty($ssql)) {
                $estatus = 'AND ( estatus = "Active" || estatus = "Inactive" )';
            }
        }
        $iParentId = "iParentId = '".$iParent_Id."'";
        if (!empty($cat_id)) {
            $iMasterServiceCategoryId = base64_decode(base64_decode($cat_id, true), true);
            $iMSql = " AND iMasterServiceCategoryId = '".$iMasterServiceCategoryId."'";
        }
        $result = $obj->MySQLSelect("SELECT count(iRentItemId) as count FROM {$this->tablename} WHERE 1 = 1 AND ".$iParentId.$iMSql." {$estatus} {$ssql} ");

        return $result[0]['count'];
    }

    public function getRentItemMaster($use, $ssql = '', $start = 0, $per_page = 0, $vLanguage = 'EN', $ord = '', $reqArr = [])
    {
        global $obj;
        $limit = '';
        $estatus = '';
        if ('admin' === $use) {
            $limit = "LIMIT {$start}, {$per_page}";
            if (0 === $start && 0 === $per_page) {
                $limit = '';
            }
            if (empty($ssql)) {
                $estatus = 'AND ( estatus = "Active" || estatus = "Inactive" )';
            }
        }
        if ('webservice' === $use) {
            $estatus = 'AND estatus = "Active"';
        }
        if (empty($ord)) {
            $ord = ' ORDER BY iDisplayOrder';
        }
        $sql = "SELECT vTitle, iRentItemId, vImage, vImage1, vTitle, tDescription, iParentId, eStatus, iDisplayOrder, JSON_UNQUOTE(JSON_VALUE(vTitle, '$.vTitle_".$vLanguage."')) as vTitle,iMasterServiceCategoryId FROM {$this->tablename} WHERE 1 = 1 {$estatus} {$ssql} AND iParentId = 0 {$ord} ".$limit.'';
        $rentitem_master_categories = $obj->MySQLSelect($sql);
        $return_array = [];
        if ('webservice' === $use) {
            foreach ($rentitem_master_categories as $key => $mServiceCategory) {
                $return_array[$key] = $this->getRentItemQuery_array($mServiceCategory, $reqArr);
            }
        } else {
            $return_array = $rentitem_master_categories;
        }

        return $return_array;
    }

    public function getRentItemQuery_array($rentitemArray, $reqArr = [])
    {
        global $tconfig, $APP_TYPE, $MODULES_OBJ, $master_service_category_tbl;
        $return_array['vTitle'] = $rentitemArray['vTitle'];
        $return_array['iCategoryId'] = $rentitemArray['iRentItemId'];
        $return_array['vCategory'] = $rentitemArray['vTitle'];
        $return_array['vImage'] = $tconfig['tsite_upload_images_rent_item'].$rentitemArray['vImage'];
        if (!empty($rentitemArray['vImage1'])) {
            $return_array['vImage1'] = $tconfig['tsite_upload_images_rent_item'].$rentitemArray['vImage1'];
        }
        $eTypeNew = $this->getRentItemMasterData($rentitemArray['iMasterServiceCategoryId'], 'eType');
        if ('0' !== $rentitemArray['iMasterServiceCategoryId']) {
            $return_array['eCatType'] = $eTypeNew;
        } else {
            $return_array['eCatType'] = 'RentItem';
        }
        $return_array['vListLogo'] = $tconfig['tsite_upload_images_rent_item'].$rentitemArray['vImage'];
        $returnarray = [];
        if (\count($reqArr) > 0) {
            foreach ($return_array as $key => $a) {
                if (\in_array($key, $reqArr, true)) {
                    $returnarray[$key] = $a;
                }
            }
            $return_array = $returnarray;
        }

        return $return_array;
    }

    public function getPaymentPlanTotalCount($use, $ssql = '')
    {
        global $obj;
        $estatus = '';
        if ('admin' === $use) {
            if (empty($ssql)) {
                $estatus = 'AND ( estatus = "Active" || estatus = "Inactive" )';
            }
        }
        $result = $obj->MySQLSelect("SELECT count(iPaymentPlanId) as count FROM {$this->PaymentPlanTableName} WHERE 1 = 1 {$ssql} {$estatus}");

        return $result[0]['count'];
    }

    public function createRentItemPost($use, $RentItemPostData, $iTmpRentItemPostId = '', $vLanguage = 'EN', $reqArr = [])
    {
        global $obj;
        if (!empty($iTmpRentItemPostId)) {
            $where = " iTmpRentItemPostId = '".$iTmpRentItemPostId."'";
            $iTmpRentItemPostIdNew = $obj->MySQLQueryPerform($this->rentitem_post_tmp, $RentItemPostData, 'update', $where);
            $iTmpRentItemPostIdNew = $iTmpRentItemPostId;
        } else {
            $iTmpRentItemPostIdNew = $obj->MySQLQueryPerform($this->rentitem_post_tmp, $RentItemPostData, 'insert');
        }
        $sql = "SELECT * FROM {$this->rentitem_post_tmp} WHERE iTmpRentItemPostId ='".$iTmpRentItemPostIdNew."'";
        $data = $obj->MySQLSelect($sql);
        $allData = [];
        foreach ($data as $key => $value) {
            $currency = $obj->MySQLSelect("SELECT ratio,vName FROM `currency` WHERE vName='".$value['vCurrencyPassenger']."'");
            $allData['iTmpRentItemPostId'] = $value['iTmpRentItemPostId'];
            $allData['iItemCategoryId'] = $value['iItemCategoryId'];
            $allData['iItemSubCategoryId'] = $value['iItemSubCategoryId'];
            $reqArr = ['vTitle'];
            $getrentitem = $this->getrentitem('webservice', $value['iItemCategoryId'], $vLanguage, $reqArr);
            $subsql = " AND iRentItemId = '".$value['iItemSubCategoryId']."'";
            $DatanewArr = $this->getRentItemSubCategory('webservice', $value['iItemCategoryId'], $subsql, '', '', $vLanguage, '', $reqArr);
            $allData['vCatName'] = $getrentitem['vTitle'];
            $allData['vSubCatName'] = $DatanewArr[0]['vTitle'];
            $allData['vRentItemPostNo'] = '# '.$value['vRentItemPostNo'];
            $allData['iUserId'] = $value['iUserId'];
            $allData['vTimeZone'] = $value['vTimeZone'];
            $allData['vLocation'] = $value['vLocation'];
            $allData['vLatitude'] = $value['vLatitude'];
            $allData['vLongitude'] = $value['vLongitude'];
            $allData['vBuildingNo'] = $value['vBuildingNo'];
            $allData['vAddress'] = $value['vAddress'];
            $allData['fAmount'] = formateNumAsPerCurrency($value['fAmount'] * $currency[0]['ratio'], $value['vCurrencyPassenger']);
            $allData['fAmountWithoutSymbol'] = $value['fAmount'];
            $allData['eStatus'] = $value['eStatus'];
            $allData['eRentItemDuration'] = $value['eRentItemDuration'];
            $allData['vCurrencyPassenger'] = $value['vCurrencyPassenger'];
            $allData['eIsUserNumberDisplay'] = $value['eIsUserNumberDisplay'];
            $allData['eIsUserAddressDisplay'] = $value['eIsUserAddressDisplay'];
            $imgIds = explode(',', $value['vImageIds']);
            $getImages = $this->getRentItemImage('webservice', $value['iItemCategoryId'], $value['iUserId'], $vLanguage, $imgIds);
            $imagesarr = [];
            foreach ($getImages as $k => $val) {
                if (!empty($iTmpRentItemPostIdNew) && !empty($val['iRentImageId'])) {
                    $where = " iRentImageId = '".$val['iRentImageId']."'";
                    $RentItemImgData['iTmpRentItemPostId'] = $iTmpRentItemPostIdNew;
                    $rentitemimgData = $obj->MySQLQueryPerform($this->rentitem_images, $RentItemImgData, 'update', $where);
                }
                $imagesarr[$k]['iRentImageId'] = $val['iRentImageId'];
                $imagesarr[$k]['vImage'] = $val['vImage'];
                $imagesarr[$k]['eFileType'] = $val['eFileType'];
                $imagesarr[$k]['ThumbImage'] = $val['ThumbImage'];
            }
            $allData['Images'] = $imagesarr;
            $rentitemFieldarray = $vItemName = [];
            $tFieldsArr = json_decode($value['tFieldsArr'], true);
            $tFields_Details_Arr = [];
            foreach ($tFieldsArr as $k => $arryfields) {
                $tFields_Details_Arr[0][$arryfields['iData']] = $arryfields['value'];
            }
            foreach ($tFields_Details_Arr as $k => $arryfields) {
                foreach ($arryfields as $iRentFieldId => $valuerentfield) {
                    $getRentItemFields = $this->getRentItemFields('webservice', $iRentFieldId, '', '', $vLanguage);
                    if ($iRentFieldId === $getRentItemFields[0]['iRentFieldId'] && 'Yes' === $getRentItemFields[0]['eTitle']) {
                        $rentitemFieldarray[$iRentFieldId]['eName'] = 'Yes';
                        $vItemName[$iRentFieldId] = $valuerentfield;
                    }
                    if ($iRentFieldId === $getRentItemFields[0]['iRentFieldId'] && 'Yes' === $getRentItemFields[0]['eDescription']) {
                        $rentitemFieldarray[$iRentFieldId]['eDescription'] = 'Yes';
                    }
                    $rentitemFieldarray[$iRentFieldId]['iOrder'] = $getRentItemFields[0]['iOrder'];
                    $rentitemFieldarray[$iRentFieldId][$getRentItemFields[0]['tFieldName']] = $valuerentfield;
                    if ('Select' === $getRentItemFields[0]['eInputType'] && $iRentFieldId === $getRentItemFields[0]['iRentFieldId']) {
                        $vFieldName = get_value('rent_item_fields_option', 'JSON_UNQUOTE(JSON_VALUE(tFieldName, "$.tOptionNameLang_'.$vLanguage.'"))', 'iOptionId', $valuerentfield, '', 'true');
                        $rentitemFieldarray[$iRentFieldId][$getRentItemFields[0]['tFieldName']] = $vFieldName;
                    }
                }
            }
            $rentitemFieldarray = array_values($rentitemFieldarray);
            $key_values = array_column($rentitemFieldarray, 'iOrder');
            array_multisort($key_values, SORT_ASC, $rentitemFieldarray);
            $rentitemFieldarrayNew = [];
            foreach ($rentitemFieldarray as $key => $subArr) {
                if (\count($vItemName) > 1 && 'Yes' === $subArr['eName']) {
                    ksort($vItemName, SORT_NUMERIC);
                    $subArr['vItemName'] = implode(' - ', $vItemName);
                }
                unset($subArr['iOrder']);
                $rentitemFieldarrayNew[] = $subArr;
            }
            $allData['RentitemFieldarray'] = $rentitemFieldarrayNew;
            $timeslot = [];
            $weekdayArray = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
            foreach ($weekdayArray as $weekdayname) {
                if ('Monday' === $weekdayname) {
                    if ('00:00:00' !== $value['vMonFromSlot'] && '00:00:00' !== $value['vMonToSlot']) {
                        $timeslot[][$weekdayname] = date('h:i A', strtotime($value['vMonFromSlot'])).' - '.date('h:i A', strtotime($value['vMonToSlot']));
                    }
                } elseif ('Tuesday' === $weekdayname) {
                    if ('00:00:00' !== $value['vTueFromSlot'] && '00:00:00' !== $value['vTueToSlot']) {
                        $timeslot[][$weekdayname] = date('h:i A', strtotime($value['vTueFromSlot'])).' - '.date('h:i A', strtotime($value['vTueToSlot']));
                    }
                } elseif ('Wednesday' === $weekdayname) {
                    if ('00:00:00' !== $value['vWedFromSlot'] && '00:00:00' !== $value['vWedToSlot']) {
                        $timeslot[][$weekdayname] = date('h:i A', strtotime($value['vWedFromSlot'])).' - '.date('h:i A', strtotime($value['vWedToSlot']));
                    }
                } elseif ('Thursday' === $weekdayname) {
                    if ('00:00:00' !== $value['vThuFromSlot'] && '00:00:00' !== $value['vThuToSlot']) {
                        $timeslot[][$weekdayname] = date('h:i A', strtotime($value['vThuFromSlot'])).' - '.date('h:i A', strtotime($value['vThuToSlot']));
                    }
                } elseif ('Friday' === $weekdayname) {
                    if ('00:00:00' !== $value['vFriFromSlot'] && '00:00:00' !== $value['vFriToSlot']) {
                        $timeslot[][$weekdayname] = date('h:i A', strtotime($value['vFriFromSlot'])).' - '.date('h:i A', strtotime($value['vFriToSlot']));
                    }
                } elseif ('Saturday' === $weekdayname) {
                    if ('00:00:00' !== $value['vSatFromSlot'] && '00:00:00' !== $value['vSatToSlot']) {
                        $timeslot[][$weekdayname] = date('h:i A', strtotime($value['vSatFromSlot'])).' - '.date('h:i A', strtotime($value['vSatToSlot']));
                    }
                } elseif ('Sunday' === $weekdayname) {
                    if ('00:00:00' !== $value['vSunFromSlot'] && '00:00:00' !== $value['vSunToSlot']) {
                        $timeslot[][$weekdayname] = date('h:i A', strtotime($value['vSunFromSlot'])).' - '.date('h:i A', strtotime($value['vSunToSlot']));
                    }
                }
            }
            $allData['timeslot'] = $timeslot;
        }

        return $allData;
    }

    public function getRentItemImage($use, $iItemCategoryId, $iUserId, $vLanguage = 'EN', $iRentImageIdArr = [], $iRentItemPostId = '', $iTmpRentItemPostId = '', $reqArr = [])
    {
        global $obj, $tconfig;
        $ssql = '';
        if (!empty($iUserId) && empty($iRentImageIdArr) && empty($iRentItemPostId) && empty($iTmpRentItemPostId)) {
            $ssql .= ' AND iRentItemPostId = 0 AND iTmpRentItemPostId = 0';
        }
        if ('' !== $iRentItemPostId) {
            $ssql .= " AND iRentItemPostId ='{$iRentItemPostId}'";
        }
        if ('' !== $iTmpRentItemPostId) {
            $ssql .= " AND iTmpRentItemPostId ='{$iTmpRentItemPostId}'";
        }
        if (!empty($iRentImageIdArr)) {
            $iRentImageIds = implode(',', $iRentImageIdArr);
            $getImages = $obj->MySQLSelect("SELECT * FROM rentitem_images WHERE iRentImageId IN ({$iRentImageIds}) {$ssql} ORDER BY `iRentImageId` DESC ");
        } else {
            $getImages = $obj->MySQLSelect("SELECT * FROM rentitem_images WHERE iUserId='".$iUserId."' AND iItemCategoryId = '".$iItemCategoryId."' {$ssql} ORDER BY `iRentImageId` DESC ");
        }
        for ($p = 0; $p < \count($getImages); ++$p) {
            $tmp = explode('.', $getImages[$p]['vImage']);
            for ($i = 0; $i < \count($tmp) - 1; ++$i) {
                $tmp1[] = $tmp[$i];
            }
            $file = implode('_', $tmp1);
            $ext = $tmp[\count($tmp) - 1];
            $videoExt_arr = ['MP4', 'MOV', 'WMV', 'AVI', 'FLV', 'MKV', 'WEBM'];
            $getImages[$p]['eFileType'] = 'Image';
            $getImages[$p]['ThumbImage'] = '';
            if (\in_array(strtoupper($ext), $videoExt_arr, true)) {
                $getImages[$p]['eFileType'] = 'Video';
                $getImages[$p]['ThumbImage'] = $this->getVideoThumbImageProvider($getImages[$p]['vImage']);
            }
            $getImages[$p]['vImage'] = $tconfig['tsite_upload_images_rent_item'].$getImages[$p]['vImage'];
        }

        return $getImages;
    }

    public function getVideoThumbImageProvider($video_file)
    {
        global $tconfig;
        $tmpArr = explode('.', $video_file);
        for ($i = 0; $i < \count($tmpArr) - 1; ++$i) {
            $tmpArr1[] = $tmpArr[$i];
        }
        $file = implode('_', $tmpArr1);
        $thumb_img = $file.'.png';
        if (!is_dir($tconfig['tsite_upload_images_rent_item_path'].'/thumnails/')) {
            mkdir($tconfig['tsite_upload_images_rent_item_path'].'/thumnails/', 0777);
            chmod($tconfig['tsite_upload_images_rent_item_path'].'/thumnails/', 0777);
        }
        $img_path = $tconfig['tsite_upload_images_rent_item_path'].'/thumnails/'.$thumb_img;
        $img_url = $tconfig['tsite_upload_images_rent_item'].'thumnails/'.$thumb_img;
        if (file_exists($img_path)) {
            return $img_url;
        }

        require_once $tconfig['tpanel_path'].'assets/libraries/FFMpeg/autoload.php';
        $sec = 3;
        $vFile = $tconfig['tsite_upload_images_rent_item_path'].$video_file;
        $img_url = '';
        if (file_exists($vFile)) {
            $ffprobe = FFProbe::create();
            $vDuration = $ffprobe->streams($vFile)->videos()->first()->get('duration');
            if ($vDuration < 3) {
                $sec = floor($vDuration);
            }
            $ffmpeg = FFMpeg\FFMpeg::create();
            $video = $ffmpeg->open($vFile);
            $frame = $video->frame(TimeCode::fromSeconds($sec));
            $frame->save($img_path);
        }

        return $img_url;
    }

    public function getRentItemFields($use, $iRentFieldId = '', $iRentItemId = '', $iRentItemPostId = '', $vLanguage = 'EN', $reqArr = [])
    {
        global $obj;
        if (!empty($iRentItemId)) {
            $sql = "SELECT iRentFieldId,vFieldName,iRentItemId,eInputType,eAllowFloat,eRequired,eEditable,iOrder,JSON_UNQUOTE(JSON_VALUE(tFieldName, '$.tFieldName_".$vLanguage."')) as tFieldName,JSON_UNQUOTE(JSON_VALUE(tDesc, '$.tDesc_".$vLanguage."')) as tDesc,eStatus,eDescription,eTitle,eListing from rentitem_fields where eStatus='Active' AND iRentItemId='".$iRentItemId."' order by iOrder ASC";
        } elseif (!empty($iRentFieldId)) {
            $sql = "SELECT iRentFieldId,vFieldName,iRentItemId,eInputType,eAllowFloat,eRequired,eEditable,iOrder,JSON_UNQUOTE(JSON_VALUE(tFieldName, '$.tFieldName_".$vLanguage."')) as tFieldName,JSON_UNQUOTE(JSON_VALUE(tDesc, '$.tDesc_".$vLanguage."')) as tDesc,eStatus,eDescription,eTitle,eListing from rentitem_fields where iRentFieldId='".$iRentFieldId."' order by iOrder ASC";
        } else {
            $sql = "SELECT iRentFieldId,vFieldName,iRentItemId,eInputType,eAllowFloat,eRequired,eEditable,iOrder,JSON_UNQUOTE(JSON_VALUE(tFieldName, '$.tFieldName_".$vLanguage."')) as tFieldName,JSON_UNQUOTE(JSON_VALUE(tDesc, '$.tDesc_".$vLanguage."')) as tDesc,eStatus,eDescription,eTitle,eListing from rentitem_fields where eStatus='Active' order by iOrder ASC";
        }
        $db_fields = $obj->MySQLSelect($sql);
        $getPostFieldVal = "SELECT tFieldsArr FROM `rentitem_post` WHERE iRentItemPostId = '".$iRentItemPostId."'";
        $db_post_fields = $obj->MySQLSelect($getPostFieldVal);
        $tFieldsArr = json_decode($db_post_fields[0]['tFieldsArr'], true);
        $newarray = [];
        foreach ($tFieldsArr as $k => $arryfields) {
            $newarray[$arryfields['iData']] = $arryfields['value'];
        }
        if (\count($db_fields) > 0) {
            for ($i = 0; $i < \count($db_fields); ++$i) {
                $Data_Field[$i]['vFieldName'] = $db_fields[$i]['tFieldName'];
                $Data_Field[$i]['iRentItemId'] = $db_fields[$i]['iRentItemId'];
                $Data_Field[$i]['tFieldName'] = $db_fields[$i]['tFieldName'];
                if (null !== $db_fields[$i]['tDesc']) {
                    $Data_Field[$i]['tDesc'] = $db_fields[$i]['tDesc'];
                } else {
                    $Data_Field[$i]['tDesc'] = '';
                }
                $Data_Field[$i]['eInputType'] = $db_fields[$i]['eInputType'];
                if ('Select' === $db_fields[$i]['eInputType']) {
                    $Data_Field[$i]['Options'] = $this->getSelectOptions($db_fields[$i]['iRentFieldId'], $vLanguage);
                }
                $Data_Field[$i]['eAllowFloat'] = $db_fields[$i]['eAllowFloat'];
                $Data_Field[$i]['eRequired'] = $db_fields[$i]['eRequired'];
                $Data_Field[$i]['eEditable'] = $db_fields[$i]['eEditable'];
                $Data_Field[$i]['iRentFieldId'] = $db_fields[$i]['iRentFieldId'];
                $Data_Field[$i]['eStatus'] = $db_fields[$i]['eStatus'];
                $Data_Field[$i]['eDescription'] = $db_fields[$i]['eDescription'];
                $Data_Field[$i]['eTitle'] = $db_fields[$i]['eTitle'];
                $Data_Field[$i]['eListing'] = $db_fields[$i]['eListing'];
                $Data_Field[$i]['iOrder'] = $db_fields[$i]['iOrder'];
                if (!empty($newarray[$db_fields[$i]['iRentFieldId']])) {
                    $getRentItemFields = $this->getRentItemFields('webservice', $db_fields[$i]['iRentFieldId'], '', '', $vLanguage);
                    if ('Select' === $getRentItemFields[0]['eInputType']) {
                        $vFieldName = get_value('rent_item_fields_option', 'vFieldName', 'iOptionId', $newarray[$db_fields[$i]['iRentFieldId']], '', 'true');
                        $Data_Field[$i]['vAddedValue'] = $vFieldName;
                        $Data_Field[$i]['iSelectedOptioId'] = $newarray[$db_fields[$i]['iRentFieldId']];
                        $eListingTypeOptions = get_value('rent_item_fields_option', 'eListingType', 'iOptionId', $newarray[$db_fields[$i]['iRentFieldId']], '', 'true');
                        if ('Rent' === $eListingTypeOptions) {
                            $Data_Field[$i]['isBuySell'] = 'No';
                        } elseif ('Sale' === $eListingTypeOptions) {
                            $Data_Field[$i]['isBuySell'] = 'Yes';
                        }
                    } else {
                        $Data_Field[$i]['vAddedValue'] = $newarray[$db_fields[$i]['iRentFieldId']];
                    }
                } else {
                    $Data_Field[$i]['vAddedValue'] = '';
                }
            }
        }

        return $Data_Field;
    }

    public function getSelectOptions($iRentFieldId, $vLanguage = 'EN')
    {
        global $obj;
        $sql = "SELECT iOptionId,vFieldName,iRentFieldId,eStatus,JSON_UNQUOTE(JSON_VALUE(tFieldName, '$.tOptionNameLang_".$vLanguage."')) as tFieldName,eListingType FROM rent_item_fields_option WHERE iRentFieldId='".$iRentFieldId."' AND eStatus = 'Active' ORDER BY iOptionId ASC";
        $db_fields = $obj->MySQLSelect($sql);
        $dbFieldsArray = [];
        foreach ($db_fields as $key => $value) {
            $dbFieldsArray[$key]['iOptionId'] = $value['iOptionId'];
            if ('' !== $value['tFieldName']) {
                $dbFieldsArray[$key]['vTitle'] = $value['tFieldName'];
                $dbFieldsArray[$key]['vFieldName'] = $value['tFieldName'];
            } else {
                $dbFieldsArray[$key]['vTitle'] = $value['vFieldName'];
                $dbFieldsArray[$key]['vFieldName'] = $value['vFieldName'];
            }
            $dbFieldsArray[$key]['iRentFieldId'] = $value['iRentFieldId'];
            if (!empty($value['eListingType'])) {
                $dbFieldsArray[$key]['eListingTypeOptions'] = $value['eListingType'];
            }
        }

        return $dbFieldsArray;
    }

    public function createRentItemFinalPost($use, $iTmpRentItemPostId, $iRentItemPostId, $iMemberId, $iPaymentPlanId, $vLanguage = 'EN', $isfree = 'No', $finalarray = [], $reqArr = [])
    {
        global $obj;
        $postarray = [];
        if (('' !== $iTmpRentItemPostId) && empty($finalarray)) {
            $getPostData = $this->getRentItemPostData('webservice', $iTmpRentItemPostId, $iMemberId, $vLanguage);
            $postarray['iItemCategoryId'] = $getPostData[0]['iItemCategoryId'];
            $postarray['iItemSubCategoryId'] = $getPostData[0]['iItemSubCategoryId'];
            $postarray['iTmpRentItemPostId'] = $getPostData[0]['iTmpRentItemPostId'];
            $postarray['vTimeZone'] = $getPostData[0]['vTimeZone'];
            $postarray['iUserId'] = $getPostData[0]['iUserId'];
            $postarray['vRentItemPostNo'] = random_int(10_000_000, 99_999_999);
            $postarray['vLocation'] = $getPostData[0]['vLocation'];
            $postarray['vLatitude'] = $getPostData[0]['vLatitude'];
            $postarray['vLongitude'] = $getPostData[0]['vLongitude'];
            $postarray['vBuildingNo'] = $getPostData[0]['vBuildingNo'];
            $postarray['vAddress'] = $getPostData[0]['vAddress'];
            $postarray['fAmount'] = $getPostData[0]['fAmount'];
            $postarray['dPickupAvailableDate'] = $getPostData[0]['dPickupAvailableDate'];
            $postarray['dPickupAvailableTime'] = $getPostData[0]['dPickupAvailableTime'];
            $postarray['eStatus'] = 'Pending';
            if (SITE_TYPE === 'Demo') {
                $getRentItemPaymentPlanAmount = $this->getRentItemPlan('webservice', $iPaymentPlanId, $vLanguage);
                $iTotalDays = $getRentItemPaymentPlanAmount['iTotalDays'];
                $addeddate = ' +'.$iTotalDays.' days';
                $renewdate = date('Y-m-d H:i:s', strtotime($addeddate));
                $dApprovedDate = date('Y-m-d H:i:s');
                $postarray['eStatus'] = 'Approved';
                $postarray['dApprovedDate'] = $dApprovedDate;
                $postarray['dRenewDate'] = $renewdate;
            }
            $postarray['iPaymentPlanId'] = $iPaymentPlanId;
            $postarray['eRentItemDuration'] = $getPostData[0]['eRentItemDuration'];
            $postarray['vImageIds'] = $getPostData[0]['vImageIds'];
            $postarray['tFieldsArr'] = $getPostData[0]['tFieldsArr'];
            $postarray['dRentItemPostDateTmp'] = $getPostData[0]['dRentItemPostDate'];
            $postarray['eIsFavourite'] = 'No';
            $postarray['eIsUserNumberDisplay'] = $getPostData[0]['eIsUserNumberDisplay'];
            $postarray['eIsUserAddressDisplay'] = $getPostData[0]['eIsUserAddressDisplay'];
            $postarray['vMonFromSlot'] = $getPostData[0]['vMonFromSlot'];
            $postarray['vMonToSlot'] = $getPostData[0]['vMonToSlot'];
            $postarray['vTueFromSlot'] = $getPostData[0]['vTueFromSlot'];
            $postarray['vTueToSlot'] = $getPostData[0]['vTueToSlot'];
            $postarray['vWedFromSlot'] = $getPostData[0]['vWedFromSlot'];
            $postarray['vWedToSlot'] = $getPostData[0]['vWedToSlot'];
            $postarray['vThuFromSlot'] = $getPostData[0]['vThuFromSlot'];
            $postarray['vThuToSlot'] = $getPostData[0]['vThuToSlot'];
            $postarray['vFriFromSlot'] = $getPostData[0]['vFriFromSlot'];
            $postarray['vFriToSlot'] = $getPostData[0]['vFriToSlot'];
            $postarray['vSatFromSlot'] = $getPostData[0]['vSatFromSlot'];
            $postarray['vSatToSlot'] = $getPostData[0]['vSatToSlot'];
            $postarray['vSunFromSlot'] = $getPostData[0]['vSunFromSlot'];
            $postarray['vSunToSlot'] = $getPostData[0]['vSunToSlot'];
        } else {
            $postarray = $finalarray;
        }
        if (!empty($iRentItemPostId)) {
            $where = " iRentItemPostId = '".$iRentItemPostId."'";
            $iRentItemPostIdNew = $obj->MySQLQueryPerform($this->rentitem_post, $postarray, 'update', $where);
            $iRentItemPostIdNew = $iRentItemPostId;
        } else {
            $iRentItemPostIdNew = $obj->MySQLQueryPerform($this->rentitem_post, $postarray, 'insert');
        }
        if ('Yes' === $isfree) {
            $where_rentitem = " iRentItemPostId = '".$iRentItemPostIdNew."'";
            $data_rentitems['ePaid'] = 'Yes';
            $data_rentitems['eUserPayment'] = 'Settled';
            $obj->MySQLQueryPerform($this->rentitem_post, $data_rentitems, 'update', $where_rentitem);
        }
        if (!empty($iTmpRentItemPostId) && !empty($iRentItemPostIdNew)) {
            $where = " iTmpRentItemPostId = '".$iTmpRentItemPostId."'";
            $RentItemPostData['iPaymentPlanId'] = $iPaymentPlanId;
            $RentItemPostData['eStatus'] = 'Completed';
            $RentItemPostData['iRentItemPostId'] = $iRentItemPostIdNew;
            $iTmpRentItemPostIdNew = $obj->MySQLQueryPerform($this->rentitem_post_tmp, $RentItemPostData, 'update', $where);
        }
        $sql = "SELECT * FROM {$this->rentitem_post} WHERE iRentItemPostId ='".$iRentItemPostIdNew."'";
        $data = $obj->MySQLSelect($sql);
        $allData = [];
        foreach ($data as $key => $value) {
            $iUserId = $value['iUserId'];
            $tblName = 'register_user';
            if (isset($userDetailsArr[$tblName.'_'.$iUserId]) && \count($userDetailsArr[$tblName.'_'.$iUserId]) > 0) {
                $Data = $userDetailsArr[$tblName.'_'.$iUserId];
            } else {
                $Data = $obj->MySQLSelect('SELECT *,iUserId as iMemberId FROM '.$tblName." WHERE iUserId='".$iUserId."'");
                $userDetailsArr[$tblName.'_'.$iUserId] = $Data;
            }
            $currency = $obj->MySQLSelect("SELECT ratio,vName FROM `currency` WHERE vName='".$Data[0]['vCurrencyPassenger']."'");
            $allData['iRentItemPostId'] = $value['iRentItemPostId'];
            $allData['iTmpRentItemPostId'] = $value['iTmpRentItemPostId'];
            $allData['iItemCategoryId'] = $value['iItemCategoryId'];
            $allData['iItemSubCategoryId'] = $value['iItemSubCategoryId'];
            $reqArr = ['vTitle'];
            $getrentitem = $this->getrentitem('webservice', $value['iItemCategoryId'], $vLanguage, $reqArr);
            $subsql = " AND iRentItemId = '".$value['iItemSubCategoryId']."'";
            $DatanewArr = $this->getRentItemSubCategory('webservice', $value['iItemCategoryId'], $subsql, '', '', $vLanguage, '', $reqArr);
            $allData['vCatName'] = $getrentitem['vTitle'];
            $allData['vSubCatName'] = $DatanewArr[0]['vTitle'];
            $allData['vRentItemPostNo'] = '# '.$value['vRentItemPostNo'];
            $allData['iUserId'] = $value['iUserId'];
            $allData['vTimeZone'] = $value['vTimeZone'];
            $allData['vLocation'] = $value['vLocation'];
            $allData['vLatitude'] = $value['vLatitude'];
            $allData['vLongitude'] = $value['vLongitude'];
            $allData['vBuildingNo'] = $value['vBuildingNo'];
            $allData['eIsFavourite'] = $value['eIsFavourite'];
            $allData['vAddress'] = $value['vAddress'];
            $allData['fAmount'] = formateNumAsPerCurrency($value['fAmount'] * $currency[0]['ratio'], $Data[0]['vCurrencyPassenger']);
            $allData['fAmountWithoutSymbol'] = $value['fAmount'];
            $allData['eStatus'] = $value['eStatus'];
            $allData['eRentItemDuration'] = $value['eRentItemDuration'];
            $allData['dRentItemPostDateTmp'] = $value['dRentItemPostDate'];
            $imgIds = explode(',', $value['vImageIds']);
            $getImages = $this->getRentItemImage('webservice', $value['iItemCategoryId'], $value['iUserId'], $vLanguage, $imgIds);
            $imagesarr = [];
            foreach ($getImages as $k => $val) {
                if (!empty($value['iRentItemPostId']) && !empty($val['iRentImageId'])) {
                    $where = " iRentImageId = '".$val['iRentImageId']."'";
                    $RentItemImgData['iRentItemPostId'] = $value['iRentItemPostId'];
                    $rentitemimgData = $obj->MySQLQueryPerform($this->rentitem_images, $RentItemImgData, 'update', $where);
                }
                $imagesarr[$k]['iRentImageId'] = $val['iRentImageId'];
                $imagesarr[$k]['vImage'] = $val['vImage'];
                $imagesarr[$k]['eFileType'] = $val['eFileType'];
                $imagesarr[$k]['ThumbImage'] = $val['ThumbImage'];
            }
            $allData['Images'] = $imagesarr;
            $rentitemFieldarray = [];
            $tFieldsArr = json_decode($value['tFieldsArr'], true);
            $tFields_Details_Arr = [];
            foreach ($tFieldsArr as $k => $arryfields) {
                $tFields_Details_Arr[0][$arryfields['iData']] = $arryfields['value'];
            }
            foreach ($tFields_Details_Arr as $k => $arryfields) {
                foreach ($arryfields as $iRentFieldId => $valuerentfield) {
                    $getRentItemFields = $this->getRentItemFields('webservice', $iRentFieldId, '', '', $vLanguage);
                    if ($iRentFieldId === $getRentItemFields[0]['iRentFieldId'] && 'Yes' === $getRentItemFields[0]['eTitle']) {
                        $rentitemFieldarray[$iRentFieldId]['eName'] = 'Yes';
                        $allData['vItemName'] = $valuerentfield;
                    }
                    if ($iRentFieldId === $getRentItemFields[0]['iRentFieldId'] && 'Yes' === $getRentItemFields[0]['eDescription']) {
                        $rentitemFieldarray[$iRentFieldId]['eDescription'] = 'Yes';
                    }
                    $rentitemFieldarray[$iRentFieldId][$getRentItemFields[0]['tFieldName']] = $valuerentfield;
                    if ('Select' === $getRentItemFields[0]['eInputType'] && $iRentFieldId === $getRentItemFields[0]['iRentFieldId']) {
                        $vFieldName = get_value('rent_item_fields_option', 'vFieldName', 'iOptionId', $valuerentfield, '', 'true');
                        $rentitemFieldarray[$iRentFieldId][$getRentItemFields[0]['tFieldName']] = $vFieldName;
                    }
                }
            }
            $allData['RentitemFieldarray'] = array_values($rentitemFieldarray);
            $weekdayArray = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
            $timeslot = [];
            foreach ($weekdayArray as $weekdayname) {
                if ('Monday' === $weekdayname) {
                    if ('00:00:00' !== $value['vMonFromSlot'] && '00:00:00' !== $value['vMonToSlot']) {
                        $timeslot[][$weekdayname] = date('h:i A', strtotime($value['vMonFromSlot'])).' - '.date('h:i A', strtotime($value['vMonToSlot']));
                    }
                } elseif ('Tuesday' === $weekdayname) {
                    if ('00:00:00' !== $value['vTueFromSlot'] && '00:00:00' !== $value['vTueToSlot']) {
                        $timeslot[][$weekdayname] = date('h:i A', strtotime($value['vTueFromSlot'])).' - '.date('h:i A', strtotime($value['vTueToSlot']));
                    }
                } elseif ('Wednesday' === $weekdayname) {
                    if ('00:00:00' !== $value['vWedFromSlot'] && '00:00:00' !== $value['vWedToSlot']) {
                        $timeslot[][$weekdayname] = date('h:i A', strtotime($value['vWedFromSlot'])).' - '.date('h:i A', strtotime($value['vWedToSlot']));
                    }
                } elseif ('Thursday' === $weekdayname) {
                    if ('00:00:00' !== $value['vThuFromSlot'] && '00:00:00' !== $value['vThuToSlot']) {
                        $timeslot[][$weekdayname] = date('h:i A', strtotime($value['vThuFromSlot'])).' - '.date('h:i A', strtotime($value['vThuToSlot']));
                    }
                } elseif ('Friday' === $weekdayname) {
                    if ('00:00:00' !== $value['vFriFromSlot'] && '00:00:00' !== $value['vFriToSlot']) {
                        $timeslot[][$weekdayname] = date('h:i A', strtotime($value['vFriFromSlot'])).' - '.date('h:i A', strtotime($value['vFriToSlot']));
                    }
                } elseif ('Saturday' === $weekdayname) {
                    if ('00:00:00' !== $value['vSatFromSlot'] && '00:00:00' !== $value['vSatToSlot']) {
                        $timeslot[][$weekdayname] = date('h:i A', strtotime($value['vSatFromSlot'])).' - '.date('h:i A', strtotime($value['vSatToSlot']));
                    }
                } elseif ('Sunday' === $weekdayname) {
                    if ('00:00:00' !== $value['vSunFromSlot'] && '00:00:00' !== $value['vSunToSlot']) {
                        $timeslot[][$weekdayname] = date('h:i A', strtotime($value['vSunFromSlot'])).' - '.date('h:i A', strtotime($value['vSunToSlot']));
                    }
                }
            }
            $allData['timeslot'] = $timeslot;
        }

        return $allData;
    }

    public function getRentItemPostData($use, $iTmpRentItemPostId, $iMemberId, $vLanguage = 'EN', $reqArr = [])
    {
        global $obj;
        $sql = "SELECT * FROM {$this->rentitem_post_tmp} WHERE iTmpRentItemPostId ='".$iTmpRentItemPostId."'";
        $allData = $obj->MySQLSelect($sql);

        return $allData;
    }

    public function getRentItemPostFinalOwner($use, $iRentItemPostId, $iUserId, $vLanguage = 'EN', $For = '', $iCategoryId = '', $reqArr = [])
    {
        global $obj, $LANG_OBJ, $tconfig;
        $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLanguage, '1', '0');
        $where = '';
        if (!empty($iUserId)) {
            $where .= " AND iUserId ='".$iUserId."'";
        }
        if (!empty($iCategoryId)) {
            $where .= " AND iItemCategoryId = '".$iCategoryId."'";
        }
        if (!empty($iRentItemPostId)) {
            $where .= " AND iRentItemPostId !='".$iRentItemPostId."' AND eStatus = 'Approved' ";
        }
        $sql = "SELECT * FROM {$this->rentitem_post} WHERE 1 = 1 {$where} AND eStatus != 'Deleted'";
        $data = $obj->MySQLSelect($sql);
        $allData = $alldatanewArr = $rentitemDataArr = [];
        foreach ($data as $key => $value) {
            $iUserId = $value['iUserId'];
            $tblName = 'register_user';
            if (isset($userDetailsArr[$tblName.'_'.$iUserId]) && \count($userDetailsArr[$tblName.'_'.$iUserId]) > 0) {
                $Data = $userDetailsArr[$tblName.'_'.$iUserId];
            } else {
                $Data = $obj->MySQLSelect('SELECT *,iUserId as iMemberId FROM '.$tblName." WHERE iUserId='".$iUserId."'");
                $userDetailsArr[$tblName.'_'.$iUserId] = $Data;
            }
            $currency = $obj->MySQLSelect("SELECT ratio,vName FROM `currency` WHERE vName='".$Data[0]['vCurrencyPassenger']."'");
            $allData['iRentItemPostId'] = $value['iRentItemPostId'];
            $allData['iTmpRentItemPostId'] = $value['iTmpRentItemPostId'];
            $allData['iItemCategoryId'] = $value['iItemCategoryId'];
            $allData['iItemSubCategoryId'] = $value['iItemSubCategoryId'];
            $reqArr = ['vTitle'];
            $getrentitem = $this->getrentitem('webservice', $value['iItemCategoryId'], $vLanguage, $reqArr);
            $subsql = " AND iRentItemId = '".$value['iItemSubCategoryId']."'";
            $DatanewArr = $this->getRentItemSubCategory('webservice', $value['iItemCategoryId'], $subsql, '', '', $vLanguage, '', $reqArr);
            $allData['vCatName'] = $getrentitem['vTitle'].' - '.$DatanewArr[0]['vTitle'];
            $allData['vRentItemPostNo'] = '# '.$value['vRentItemPostNo'];
            $allData['iUserId'] = $value['iUserId'];
            $allData['vUserName'] = $Data[0]['vName'].' '.$Data[0]['vLastName'];
            $allData['vUserEmail'] = $Data[0]['vEmail'];
            $allData['vUserPhone'] = '+'.$Data[0]['vPhoneCode'].' '.$Data[0]['vPhone'];
            $allData['eIsUserAddressDisplay'] = $value['eIsUserAddressDisplay'];
            $allData['eIsUserNumberDisplay'] = $value['eIsUserNumberDisplay'];
            $Photo_Gallery_folder_path = $tconfig['tsite_upload_images_passenger_path'].'/'.$iUserId.'/';
            $Photo_Gallery_folder = $tconfig['tsite_upload_images_passenger'].'/'.$iUserId.'/';
            $imgpath = $Photo_Gallery_folder_path.'2_'.$Data[0]['vImgName'];
            if ('' !== $Data[0]['vImgName'] && file_exists($imgpath)) {
                $imgpath1 = $Photo_Gallery_folder.'2_'.$Data[0]['vImgName'];
            } else {
                $imgpath1 = '';
            }
            $allData['vUserImage'] = $imgpath1;
            $allData['vTimeZone'] = $value['vTimeZone'];
            $allData['vLocation'] = $value['vLocation'];
            $allData['vLatitude'] = $value['vLatitude'];
            $allData['vLongitude'] = $value['vLongitude'];
            $allData['vBuildingNo'] = $value['vBuildingNo'];
            $allData['eIsFavourite'] = $value['eIsFavourite'];
            $allData['vAddress'] = $value['vAddress'];
            $allData['iPaymentPlanId'] = $value['iPaymentPlanId'];
            $allData['fAmount'] = formateNumAsPerCurrency($value['fAmount'] * $currency[0]['ratio'], $Data[0]['vCurrencyPassenger']);
            $allData['fAmountWithoutSymbol'] = $value['fAmount'];
            if ('Pending' === $value['eStatus']) {
                $eStatus = addslashes($languageLabelsArr['LBL_RENT_PENDING']);
            } elseif ('Approved' === $value['eStatus'] && $date2 >= $date_now) {
                $eStatus = addslashes($languageLabelsArr['LBL_RENT_APPROVED']);
            } elseif ('Approved' === $value['eStatus'] && $date2 < $date_now) {
                $eStatus = addslashes($languageLabelsArr['LBL_RENT_EXPIRED']);
            } elseif ('Deleted' === $value['eStatus']) {
                $eStatus = addslashes($languageLabelsArr['LBL_RENT_DELETED']);
            } else {
                $eStatus = addslashes($languageLabelsArr['LBL_RENT_REJECT']);
            }
            $allData['eStatus'] = $value['eStatus'];
            $allData['eStatusOrg'] = $value['eStatus'];
            $allData['dRentItemPostDate'] = $value['dRentItemPostDate'];
            $allData['PostedTxt'] = 'Posted By '.clearName($Data[0]['vName'].' '.$Data[0]['vLastName']).', '.date_format(date_create($value['dRentItemPostDate']), 'd F, Y');
            $allData['eRentItemDuration'] = $value['eRentItemDuration'];
            $addday = '+ 1 '.strtolower($value['eRentItemDuration']);
            $NewDate = date('Y-m-d h:i:s', strtotime($value['dRentItemPostDate'].$addday));
            $allData['eRentItemDurationDate'] = $NewDate;
            $date_now = new DateTime();
            $date2 = new DateTime($NewDate);
            if ('Pending' === $value['eStatus']) {
                $allData['eRentItemDurationDateTxt'] = $languageLabelsArr['LBL_RENT_ITEM_WAITING_APPROVAL'];
            } elseif ('Approved' === $value['eStatus'] && $date2 >= $date_now) {
                $allData['eRentItemDurationDateTxt'] = addslashes($languageLabelsArr['LBL_VALID_TILL_TXT']).' '.date_format(date_create($NewDate), 'd F, Y');
            } elseif ('Approved' === $value['eStatus'] && $date2 < $date_now) {
                $allData['eRentItemDurationDateTxt'] = addslashes($languageLabelsArr['LBL_EXPIRED_TXT']);
            } else {
                $allData['eRentItemDurationDateTxt'] = '';
            }
            $imgIds = explode(',', $value['vImageIds']);
            $getImages = $this->getRentItemImage('webservice', $value['iItemCategoryId'], $value['iUserId'], $vLanguage, $imgIds);
            $imagesarr = [];
            foreach ($getImages as $k => $val) {
                $imagesarr[$k]['iRentImageId'] = $val['iRentImageId'];
                $imagesarr[$k]['vImage'] = $val['vImage'];
                $imagesarr[$k]['eFileType'] = $val['eFileType'];
                $imagesarr[$k]['ThumbImage'] = $val['ThumbImage'];
            }
            $allData['Images'] = $imagesarr;
            $rentitemFieldarray = [];
            $tFieldsArr = json_decode($value['tFieldsArr'], true);
            $tFields_Details_Arr = [];
            foreach ($tFieldsArr as $k => $arryfields) {
                $tFields_Details_Arr[0][$arryfields['iData']] = $arryfields['value'];
            }
            foreach ($tFields_Details_Arr as $k => $arryfields) {
                foreach ($arryfields as $iRentFieldId => $value1) {
                    $getRentItemFields = $this->getRentItemFields('webservice', $iRentFieldId, '', '', $vLanguage);
                    if ($iRentFieldId === $getRentItemFields[0]['iRentFieldId'] && 'Yes' === $getRentItemFields[0]['eTitle']) {
                        $rentitemFieldarray[$iRentFieldId]['eName'] = 'Yes';
                        $allData['vItemName'] = $value1;
                    }
                    if ($iRentFieldId === $getRentItemFields[0]['iRentFieldId'] && 'Yes' === $getRentItemFields[0]['eDescription']) {
                        $rentitemFieldarray[$iRentFieldId]['eDescription'] = 'Yes';
                    }
                    $rentitemFieldarray[$iRentFieldId]['iOrder'] = $getRentItemFields[0]['iOrder'];
                    $rentitemFieldarray[$iRentFieldId][$getRentItemFields[0]['tFieldName']] = $value1;
                    if ('Select' === $getRentItemFields[0]['eInputType'] && $iRentFieldId === $getRentItemFields[0]['iRentFieldId']) {
                        $vFieldName = get_value('rent_item_fields_option', 'vFieldName', 'iOptionId', $value1, '', 'true');
                        $rentitemFieldarray[$iRentFieldId][$getRentItemFields[0]['tFieldName']] = $vFieldName;
                    }
                }
            }
            $rentitemFieldarray = array_values($rentitemFieldarray);
            $key_values = array_column($rentitemFieldarray, 'iOrder');
            array_multisort($key_values, SORT_ASC, $rentitemFieldarray);
            $rentitemFieldarrayNew = [];
            foreach ($rentitemFieldarray as $key => $subArr) {
                unset($subArr['iOrder']);
                $rentitemFieldarrayNew[] = $subArr;
            }
            $allData['RentitemFieldarray'] = $rentitemFieldarrayNew;
            $timeslot = [];
            $weekdayArray = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
            foreach ($weekdayArray as $weekdayname) {
                if ('Monday' === $weekdayname) {
                    $timeslot[][$weekdayname] = date('h:i A', strtotime($value['vMonFromSlot'])).' - '.date('h:i A', strtotime($value['vMonToSlot']));
                } elseif ('Tuesday' === $weekdayname) {
                    $timeslot[][$weekdayname] = date('h:i A', strtotime($value['vTueFromSlot'])).' - '.date('h:i A', strtotime($value['vTueToSlot']));
                } elseif ('Wednesday' === $weekdayname) {
                    $timeslot[][$weekdayname] = date('h:i A', strtotime($value['vWedFromSlot'])).' - '.date('h:i A', strtotime($value['vWedToSlot']));
                } elseif ('Thursday' === $weekdayname) {
                    $timeslot[][$weekdayname] = date('h:i A', strtotime($value['vThuFromSlot'])).' - '.date('h:i A', strtotime($value['vThuToSlot']));
                } elseif ('Friday' === $weekdayname) {
                    $timeslot[][$weekdayname] = date('h:i A', strtotime($value['vFriFromSlot'])).' - '.date('h:i A', strtotime($value['vFriToSlot']));
                } elseif ('Saturday' === $weekdayname) {
                    $timeslot[][$weekdayname] = date('h:i A', strtotime($value['vSatFromSlot'])).' - '.date('h:i A', strtotime($value['vSatToSlot']));
                } elseif ('Sunday' === $weekdayname) {
                    $timeslot[][$weekdayname] = date('h:i A', strtotime($value['vSunFromSlot'])).' - '.date('h:i A', strtotime($value['vSunToSlot']));
                }
            }
            $allData['timeslot'] = $timeslot;
            $alldatanewArr[] = $allData;
        }

        return $alldatanewArr;
    }

    public function getrentitem($use, $iRentItemId, $vLanguage = 'EN', $reqArr = [])
    {
        global $obj;
        $sql = '';
        $iRentItemId = explode(',', $iRentItemId);
        if (\count($iRentItemId_) > 1) {
            $sql .= ' iRentItemId IN ('.$iRentItemId.')';
        } else {
            $sql .= "iRentItemId = '".$iRentItemId[0]."'";
        }
        $rentitem = $obj->MySQLSelect("SELECT eStatus,iDisplayOrder,iRentItemId,vImage,vImage1,JSON_UNQUOTE(JSON_VALUE(vTitle, '$.vTitle_".$vLanguage."')) as vTitle,iParentId,vTitle as vTitle_json,fCommission FROM {$this->tablename} WHERE 1 = 1 AND {$sql}");
        $return_array = [];
        if ('webservice' === $use) {
            foreach ($rentitem as $key => $mServiceCategory) {
                $return_array[$key] = $this->getRentItemQuery_array($mServiceCategory, $reqArr);
            }
        } else {
            $return_array = $rentitem;
        }
        if (\count($iRentItemId_) > 1) {
            return $return_array;
        }

        return $return_array[0];
    }

    public function getRentItemSubCategory($use, $iParent_Id, $ssql = '', $start = 0, $per_page = 0, $vLanguage = 'EN', $ord = '', $reqArr = [])
    {
        global $obj;
        $limit = '';
        $estatus = '';
        if ('admin' === $use) {
            $limit = "LIMIT {$start}, {$per_page}";
            if (0 === $start && 0 === $per_page) {
                $limit = '';
            }
            if (empty($ssql)) {
                $estatus = 'AND ( estatus = "Active" || estatus = "Inactive" )';
            }
        }
        if ('webservice' === $use) {
            $estatus = 'AND estatus = "Active"';
        }
        $iParentId = "iParentId = '".$iParent_Id."'";
        $sql = "SELECT iRentItemId, vImage, vImage1 , iParentId, eStatus, iDisplayOrder,JSON_UNQUOTE(JSON_VALUE(vTitle, '$.vTitle_".$vLanguage."')) as vTitle FROM {$this->tablename} WHERE 1 = 1 {$estatus} {$ssql} AND ".$iParentId." {$ord} ".$limit.'';
        $rent_item_master_subcategories = $obj->MySQLSelect($sql);
        $return_array = [];
        if ('webservice' === $use) {
            foreach ($rent_item_master_subcategories as $key => $mServiceSubCategory) {
                $return_array[$key] = $this->getRentItemQuery_array($mServiceSubCategory, $reqArr);
            }
        } else {
            $return_array = $rent_item_master_subcategories;
        }

        return $return_array;
    }

    public function rentPostDelete($iMemberId, $iRentItemPostId, $eDeletedBy = '', $vLanguage = 'EN'): void
    {
        global $obj;
        if (isset($iRentItemPostId) && !empty($iRentItemPostId)) {
            $where = " iRentItemPostId = '".$iRentItemPostId."'";
            $RentItemPostData['eStatus'] = 'Deleted';
            $RentItemPostData['eDeletedBy'] = $eDeletedBy;
            $RentItemPostData1 = $obj->MySQLQueryPerform($this->rentitem_post, $RentItemPostData, 'update', $where);
            if (1 === $RentItemPostData1) {
                $returnArr['Action'] = '1';
                $returnArr['message2'] = 'LBL_RENT_POST_DELETE_TXT';
            } else {
                $returnArr['Action'] = 0;
                $returnArr['message'] = 'LBL_NO_DATA_AVAIL';
            }
        } else {
            $returnArr['Action'] = 0;
            $returnArr['message'] = 'LBL_NO_DATA_AVAIL';
        }
        setDataResponse($returnArr);
    }

    public function getRentItemPostFinal($use, $iRentItemPostId, $iUserId, $vLanguage = 'EN', $For = '', $iCategoryId = '', $eType = '', $reqArr = [])
    {
        global $obj, $LANG_OBJ, $tconfig, $MODULES_OBJ, $master_service_category_tbl, $_REQUEST;
        $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLanguage, '1', '0');
        $postpage = isset($_REQUEST['page']) ? trim($_REQUEST['page']) : 1;
        $postlimit = '';
        if ('GetRentPostForAllUser' !== $_REQUEST['type']) {
            $post_per_page = 5;
            $post_start_limit = ($postpage - 1) * $post_per_page;
            $postlimit = ' LIMIT '.$post_start_limit.', '.$post_per_page;
        }
        $where = '';
        if (!empty($iUserId) && 'All' !== $For) {
            $where .= " AND rp.iUserId ='".$iUserId."'";
        }
        if (!empty($iUserId) && 'All' === $For) {
            $where .= " AND rp.eStatus = 'Approved' ";
        }
        if (!empty($iCategoryId)) {
            $where .= " AND rp.iItemCategoryId = '".$iCategoryId."'";
        }
        if (!empty($iRentItemPostId)) {
            $where .= " AND rp.iRentItemPostId ='".$iRentItemPostId."'";
        }
        if ('Web' !== $use) {
            $where .= " AND ((rp.eStatus != 'Deleted') OR (rp.eStatus = 'Deleted' AND rp.eDeletedBy = 'Admin'))";
        }
        $msql = '';
        if (!empty($eType)) {
            $iMasterServiceCategoryId = $this->getRentItemMasterData($eType, 'iMasterServiceCategoryId');
            if (!empty($iMasterServiceCategoryId)) {
                $msql = " AND rc.iMasterServiceCategoryId='".$iMasterServiceCategoryId."'";
            }
        }
        if ('GetRentPostForAllUser' !== $_REQUEST['type']) {
            $tsql = "SELECT iRentItemPostId FROM {$this->rentitem_post} as rp LEFT JOIN rent_items_category as rc on rc.iRentItemId =rp.iItemCategoryId WHERE 1 = 1 {$where} {$msql} ORDER BY rp.iRentItemPostId DESC";
            $TotalPostData = $obj->MySQLSelect($tsql);
            $totalNum = \count($TotalPostData);
            $TotalPages = ceil($totalNum / $post_per_page);
        }
        $sql = "SELECT rp.*,rc.iMasterServiceCategoryId FROM {$this->rentitem_post} as rp LEFT JOIN rent_items_category as rc on rc.iRentItemId =rp.iItemCategoryId WHERE 1 = 1 {$where} {$msql} ORDER BY rp.dRentItemPostDate DESC {$postlimit}";
        $data = $obj->MySQLSelect($sql);
        $allData = $alldatanewArr = $rentitemDataArr = [];
        $userData = $obj->MySQLSelect("SELECT vCurrencyPassenger,vEmail,iUserId as iMemberId FROM register_user WHERE iUserId='".$iUserId."'");
        $currency = $obj->MySQLSelect("SELECT ratio,vName FROM `currency` WHERE vName='".$userData[0]['vCurrencyPassenger']."'");
        if ('All' === $For && 'Web' === $use) {
            $currency = get_value('currency', 'ratio,vName', 'eDefault', 'Yes', '');
            $userData[0]['vCurrencyPassenger'] = $currency[0]['vName'];
        }
        foreach ($data as $key => $value) {
            if ('' !== $value['iRentItemPostId']) {
                $sendcontactQuerylog = $obj->MySQLSelect("SELECT count(iLogId) as Total FROM {$this->rent_item_sendquery_log} WHERE iRentItemPostId='".$value['iRentItemPostId']."' AND iMemberId = '".$iUserId."'");
                if ($sendcontactQuerylog[0]['Total'] > 0) {
                    $allData['isOwnerPostSendInquiry'] = 'Yes';
                } else {
                    $allData['isOwnerPostSendInquiry'] = 'No';
                }
            }
            $tblName = 'register_user';
            if ('All' === $For) {
                if (isset($userDetailsArr[$tblName.'_'.$value['iUserId']]) && \count($userDetailsArr[$tblName.'_'.$value['iUserId']]) > 0) {
                    $PostUserData = $userDetailsArr[$tblName.'_'.$value['iUserId']];
                } else {
                    $PostUserData = $obj->MySQLSelect('SELECT vName,vLastName,vEmail,vPhoneCode,vPhone,vImgName,iUserId as iMemberId FROM '.$tblName." WHERE iUserId='".$value['iUserId']."'");
                    $userDetailsArr[$tblName.'_'.$value['iUserId']] = $PostUserData;
                }
            } else {
                if (isset($userDetailsArr[$tblName.'_'.$iUserId]) && \count($userDetailsArr[$tblName.'_'.$iUserId]) > 0) {
                    $PostUserData = $userDetailsArr[$tblName.'_'.$iUserId];
                } else {
                    $PostUserData = $obj->MySQLSelect('SELECT vName,vLastName,vEmail,vPhoneCode,vPhone,vImgName,iUserId as iMemberId FROM '.$tblName." WHERE iUserId='".$iUserId."'");
                    $userDetailsArr[$tblName.'_'.$iUserId] = $PostUserData;
                }
            }
            $allData['iRentItemPostId'] = $value['iRentItemPostId'];
            $allData['iTmpRentItemPostId'] = $value['iTmpRentItemPostId'];
            $allData['iItemCategoryId'] = $value['iItemCategoryId'];
            $allData['iItemSubCategoryId'] = $value['iItemSubCategoryId'];
            if (!empty($eType)) {
                $allData['eType'] = $eType;
            }
            $reqArr1 = ['vTitle'];
            $getrentitem = $obj->MySQLSelect("SELECT iRentItemId,JSON_UNQUOTE(JSON_VALUE(vTitle, '$.vTitle_".$vLanguage."')) as vTitle FROM {$this->tablename} WHERE 1 = 1 AND iRentItemId = '".$value['iItemCategoryId']."'");
            $subsql = " AND iRentItemId = '".$value['iItemSubCategoryId']."'";
            $DatanewArr = $this->getRentItemSubCategory('webservice', $value['iItemCategoryId'], $subsql, '', '', $vLanguage, '', $reqArr1);
            if (!empty($DatanewArr[0]['vTitle'])) {
                $allData['vCatName'] = $getrentitem[0]['vTitle'].' - '.$DatanewArr[0]['vTitle'];
            } else {
                $allData['vCatName'] = $getrentitem[0]['vTitle'];
            }
            $allData['vRentItemPostNo'] = addslashes($languageLabelsArr['LBL_RENT_POST_NO_TXT']).'# '.$value['vRentItemPostNo'];
            $allData['vRentItemPostNoMail'] = $value['vRentItemPostNo'];
            $allData['iUserId'] = $value['iUserId'];
            $allData['vUserName'] = $PostUserData[0]['vName'].' '.$PostUserData[0]['vLastName'];
            $allData['vUserEmail'] = $PostUserData[0]['vEmail'];
            $allData['vUserPhone'] = '+'.$PostUserData[0]['vPhoneCode'].$PostUserData[0]['vPhone'];
            $Photo_Gallery_folder_path = $tconfig['tsite_upload_images_passenger_path'].'/'.$iUserId.'/';
            $Photo_Gallery_folder = $tconfig['tsite_upload_images_passenger'].'/'.$iUserId.'/';
            $imgpath = $Photo_Gallery_folder_path.'2_'.$PostUserData[0]['vImgName'];
            if ('' !== $PostUserData[0]['vImgName'] && file_exists($imgpath)) {
                $imgpath1 = $Photo_Gallery_folder.'2_'.$PostUserData[0]['vImgName'];
            } else {
                $imgpath1 = '';
            }
            $allData['vUserImage'] = $imgpath1;
            $allData['vTimeZone'] = $value['vTimeZone'];
            $allData['vLocation'] = $value['vLocation'];
            $allData['vLatitude'] = $value['vLatitude'];
            $allData['vLongitude'] = $value['vLongitude'];
            $allData['vBuildingNo'] = $value['vBuildingNo'];
            $allData['eIsFavourite'] = $value['eIsFavourite'];
            $allData['vAddress'] = $value['vAddress'];
            $allData['eIsUserNumberDisplay'] = $value['eIsUserNumberDisplay'];
            $allData['eIsUserAddressDisplay'] = $value['eIsUserAddressDisplay'];
            $allData['iPaymentPlanId'] = $value['iPaymentPlanId'];
            $sql = " AND iPaymentPlanId='".$value['iPaymentPlanId']."'";
            $rent_item_payment_plan = $this->getRentItemPaymentPlan('webservice', $sql, '', '', $vLanguage);
            $allData['eFreePlan'] = $rent_item_payment_plan[0]['eFreePlan'];
            if ('no' === strtolower($rent_item_payment_plan[0]['eFreePlan'])) {
                $allData['eFeatured'] = addslashes($languageLabelsArr['LBL_RENT_FEATURED']);
            } else {
                $allData['eFeatured'] = '';
            }
            $dataarray = [];
            if (!empty($rent_item_payment_plan)) {
                $dataarray['vPlanName'] = $rent_item_payment_plan[0]['vPlanName'];
                $dataarray['iPaymentPlanId'] = $rent_item_payment_plan[0]['iPaymentPlanId'];
                if ('0.00' === $rent_item_payment_plan[0]['fAmount']) {
                    $dataarray['fAmount'] = '';
                } else {
                    $dataarray['fAmount'] = formateNumAsPerCurrency($rent_item_payment_plan[0]['fAmount'] * $currency[0]['ratio'], $userData[0]['vCurrencyPassenger']);
                }
            }
            $allData['RentItemPlanData'] = $dataarray;
            $allData['fAmount'] = formateNumAsPerCurrency($value['fAmount'] * $currency[0]['ratio'], $userData[0]['vCurrencyPassenger']);
            $allData['fAmountWithoutSymbol'] = setTwoDecimalPoint($value['fAmount'] * $currency[0]['ratio']);
            $date_now = new DateTime();
            $date2 = new DateTime($NewDate);
            if ('Pending' === $value['eStatus']) {
                $eStatus = addslashes($languageLabelsArr['LBL_RENT_PENDING']);
            } elseif ('Approved' === $value['eStatus']) {
                $eStatus = addslashes($languageLabelsArr['LBL_RENT_APPROVED']);
            } elseif ('Expired' === $value['eStatus']) {
                $eStatus = addslashes($languageLabelsArr['LBL_RENT_EXPIRED']);
            } elseif ('Deleted' === $value['eStatus']) {
                $eStatus = addslashes($languageLabelsArr['LBL_RENT_DELETED']);
            } else {
                $eStatus = addslashes($languageLabelsArr['LBL_RENT_REJECT']);
            }
            $allData['eStatus'] = $eStatus;
            $allData['eStatusOrg'] = $value['eStatus'];
            if ('Deleted' === $value['eStatus']) {
                $allData['vRejectReason'] = $value['vDeletedReason'];
            } else {
                $allData['vRejectReason'] = $value['vRejectReason'];
            }
            $allData['dRentItemPostDate'] = $value['dRentItemPostDate'];
            $allData['dApprovedDate'] = $value['dApprovedDate'];
            $allData['dRenewDate'] = $value['dRenewDate'];
            $allData['PostedTxt'] = 'Posted By '.clearName($PostUserData[0]['vName'].' '.$PostUserData[0]['vLastName']).', '.date_format(date_create($value['dRentItemPostDate']), 'd F, Y');
            $allData['eRentItemDuration'] = $value['eRentItemDuration'];
            $addday = '+ 1 '.strtolower($value['eRentItemDuration']);
            $NewDate = date('Y-m-d h:i:s', strtotime($value['dRenewDate']));
            $allData['eRentItemDurationDate'] = $NewDate;
            $date_now = new DateTime();
            $date2 = new DateTime($NewDate);
            if ('Pending' === $value['eStatus']) {
                $allData['eRentItemDurationDateTxt'] = $languageLabelsArr['LBL_RENT_ITEM_WAITING_APPROVAL'];
            } elseif ('Approved' === $value['eStatus'] && $date2 >= $date_now) {
                $allData['eRentItemDurationDateTxt'] = addslashes($languageLabelsArr['LBL_VALID_TILL_TXT']).' '.date_format(date_create($NewDate), 'd F, Y');
            } elseif ('Approved' === $value['eStatus'] && $date2 < $date_now) {
                $allData['eRentItemDurationDateTxt'] = addslashes($languageLabelsArr['LBL_EXPIRED_TXT']);
            } elseif ('Deleted' === $value['eStatus'] && 'Admin' === $value['eDeletedBy']) {
                $allData['eRentItemDurationDateTxt'] = $languageLabelsArr['LBL_RENT_DELETED_BY_ADMIN'] ?? 'Deleted By Admin';
            } else {
                $allData['eRentItemDurationDateTxt'] = '';
            }
            $imgIds = explode(',', $value['vImageIds']);
            $getImages = $this->getRentItemImage('webservice', $value['iItemCategoryId'], $value['iUserId'], $vLanguage, $imgIds);
            $imagesarr = [];
            if (!empty($getImages)) {
                foreach ($getImages as $k => $val) {
                    $imagesarr[$k]['iRentImageId'] = $val['iRentImageId'];
                    $imagesarr[$k]['vImage'] = $val['vImage'];
                    $imagesarr[$k]['eFileType'] = $val['eFileType'];
                    $imagesarr[$k]['ThumbImage'] = $val['ThumbImage'];
                }
            } else {
                $imagesarr[0]['iRentImageId'] = '';
                $imagesarr[0]['vImage'] = '';
                $imagesarr[0]['eFileType'] = '';
                $imagesarr[0]['ThumbImage'] = '';
            }
            $allData['Images'] = $imagesarr;
            $rentitemFieldarray = $itemname = [];
            $tFieldsArr = json_decode($value['tFieldsArr'], true);
            $tFields_Details_Arr = [];
            foreach ($tFieldsArr as $k => $arryfields) {
                $tFields_Details_Arr[0][$arryfields['iData']] = $arryfields['value'];
            }
            foreach ($tFields_Details_Arr as $k => $arryfields) {
                foreach ($arryfields as $iRentFieldId => $value1) {
                    $getRentItemFields = $this->getRentItemFields('webservice', $iRentFieldId, '', '', $vLanguage);
                    if ($iRentFieldId === $getRentItemFields[0]['iRentFieldId'] && 'Yes' === $getRentItemFields[0]['eTitle']) {
                        $rentitemFieldarray[$iRentFieldId]['eName'] = 'Yes';
                        $itemname[] = $value1;
                        $allData['vItemName'] = $value1;
                    }
                    if ($iRentFieldId === $getRentItemFields[0]['iRentFieldId'] && 'Yes' === $getRentItemFields[0]['eDescription']) {
                        $rentitemFieldarray[$iRentFieldId]['eDescription'] = 'Yes';
                    }
                    if ($iRentFieldId === $getRentItemFields[0]['iRentFieldId'] && 'Yes' === $getRentItemFields[0]['eListing'] && 'Select' === $getRentItemFields[0]['eInputType']) {
                        $sql = "SELECT iOptionId,vFieldName,iRentFieldId,eStatus,JSON_UNQUOTE(JSON_VALUE(tFieldName, '$.tOptionNameLang_".$vLanguage."')) as tFieldName,eListingType FROM rent_item_fields_option WHERE iOptionId='".$value1."'";
                        $db_fields = $obj->MySQLSelect($sql);
                        $tFieldName = $db_fields[0]['tFieldName'];
                        $vFieldName = $db_fields[0]['vFieldName'];
                        $eListingTypeOptions = $db_fields[0]['eListingType'];
                        if ('Rent' === $eListingTypeOptions) {
                            $allData['isBuySell'] = 'No';
                        } elseif ('Sale' === $eListingTypeOptions) {
                            $allData['isBuySell'] = 'Yes';
                        }
                        $allData['eListingType'] = !empty($tFieldName) ? $tFieldName : $vFieldName;
                        $allData['eListingTypeWeb'] = $vFieldName;
                    }
                    $rentitemFieldarray[$iRentFieldId]['iOrder'] = $getRentItemFields[0]['iOrder'];
                    $rentitemFieldarray[$iRentFieldId][$getRentItemFields[0]['tFieldName']] = $value1;
                    if ('Make' === $getRentItemFields[0]['vFieldName']) {
                        $allData['vCarMake'] = $value1;
                    }
                    if ('Model' === $getRentItemFields[0]['vFieldName']) {
                        $allData['vCarModel'] = $value1;
                    }
                    if ('Select' === $getRentItemFields[0]['eInputType'] && $iRentFieldId === $getRentItemFields[0]['iRentFieldId']) {
                        $vFieldName = get_value('rent_item_fields_option', 'vFieldName', 'iOptionId', $value1, '', 'true');
                        $rentitemFieldarray[$iRentFieldId][$getRentItemFields[0]['tFieldName']] = $vFieldName;
                        if ('Property Type' === $getRentItemFields[0]['vFieldName']) {
                            $allData['vPropertyTypeName'] = $vFieldName;
                        }
                    }
                }
            }
            if (\is_array($itemname)) {
                $allData['vItemName'] = implode(' - ', $itemname);
            }
            $rentitemFieldarray = array_values($rentitemFieldarray);
            $key_values = array_column($rentitemFieldarray, 'iOrder');
            array_multisort($key_values, SORT_ASC, $rentitemFieldarray);
            $rentitemFieldarrayNew = [];
            foreach ($rentitemFieldarray as $key => $subArr) {
                unset($subArr['iOrder']);
                $rentitemFieldarrayNew[] = $subArr;
            }
            $allData['RentitemFieldarray'] = $rentitemFieldarrayNew;
            $allData['tFieldsArr'] = array_values($tFields_Details_Arr[0]);
            $weekdayArray = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
            $timeslot = [];
            if ('Approved' === $value['eStatus'] || 'Web' === $use) {
                foreach ($weekdayArray as $weekdayname) {
                    if ('Monday' === $weekdayname) {
                        if ('00:00:00' !== $value['vMonFromSlot'] && '00:00:00' !== $value['vMonToSlot']) {
                            $timeslot[][$weekdayname] = date('h:i A', strtotime($value['vMonFromSlot'])).' - '.date('h:i A', strtotime($value['vMonToSlot']));
                        }
                    } elseif ('Tuesday' === $weekdayname) {
                        if ('00:00:00' !== $value['vTueFromSlot'] && '00:00:00' !== $value['vTueToSlot']) {
                            $timeslot[][$weekdayname] = date('h:i A', strtotime($value['vTueFromSlot'])).' - '.date('h:i A', strtotime($value['vTueToSlot']));
                        }
                    } elseif ('Wednesday' === $weekdayname) {
                        if ('00:00:00' !== $value['vWedFromSlot'] && '00:00:00' !== $value['vWedToSlot']) {
                            $timeslot[][$weekdayname] = date('h:i A', strtotime($value['vWedFromSlot'])).' - '.date('h:i A', strtotime($value['vWedToSlot']));
                        }
                    } elseif ('Thursday' === $weekdayname) {
                        if ('00:00:00' !== $value['vThuFromSlot'] && '00:00:00' !== $value['vThuToSlot']) {
                            $timeslot[][$weekdayname] = date('h:i A', strtotime($value['vThuFromSlot'])).' - '.date('h:i A', strtotime($value['vThuToSlot']));
                        }
                    } elseif ('Friday' === $weekdayname) {
                        if ('00:00:00' !== $value['vFriFromSlot'] && '00:00:00' !== $value['vFriToSlot']) {
                            $timeslot[][$weekdayname] = date('h:i A', strtotime($value['vFriFromSlot'])).' - '.date('h:i A', strtotime($value['vFriToSlot']));
                        }
                    } elseif ('Saturday' === $weekdayname) {
                        if ('00:00:00' !== $value['vSatFromSlot'] && '00:00:00' !== $value['vSatToSlot']) {
                            $timeslot[][$weekdayname] = date('h:i A', strtotime($value['vSatFromSlot'])).' - '.date('h:i A', strtotime($value['vSatToSlot']));
                        }
                    } elseif ('Sunday' === $weekdayname) {
                        if ('00:00:00' !== $value['vSunFromSlot'] && '00:00:00' !== $value['vSunToSlot']) {
                            $timeslot[][$weekdayname] = date('h:i A', strtotime($value['vSunFromSlot'])).' - '.date('h:i A', strtotime($value['vSunToSlot']));
                        }
                    }
                }
            } else {
                $timingArray = ['vMonFromSlot', 'vMonToSlot', 'vTueFromSlot', 'vTueToSlot', 'vWedFromSlot', 'vWedToSlot', 'vThuFromSlot', 'vThuToSlot', 'vFriFromSlot', 'vFriToSlot', 'vSatFromSlot', 'vSatToSlot', 'vSunFromSlot', 'vSunToSlot'];
                $arrayKey = $tempCntr = 0;
                $timeslot = [];
                for ($j = 0; $j < \count($timingArray); ++$j) {
                    if ('00:00:00' !== $value[$timingArray[$j]] && '00:00:00' !== $value[$timingArray[$j + 1]]) {
                        $newdateFromTime = date('h:i A', strtotime($value[$timingArray[$j]]));
                    } else {
                        $newdateFromTime = $languageLabelsArr['LBL_From'];
                    }
                    $tempArry = ['dayname' => getDaynameBylabel($timingArray[$j]), 'field' => getDaynameBylabel($timingArray[$j], 'field'), $timingArray[$j] => $newdateFromTime];
                    $returnArr['timeslot'][$arrayKey][$tempCntr] = $tempArry;
                    ++$j;
                    if ('00:00:00' !== $value[$timingArray[$j - 1]] && '00:00:00' !== $value[$timingArray[$j]]) {
                        $newdateToTime = date('h:i A', strtotime($value[$timingArray[$j]]));
                    } else {
                        $newdateToTime = $languageLabelsArr['LBL_To'];
                    }
                    $returnArr['timeslot'][$arrayKey][$tempCntr][$timingArray[$j]] = $newdateToTime;
                    $timeslot[] = $returnArr['timeslot'][$arrayKey][$tempCntr];
                }
            }
            $allData['timeslot'] = $timeslot;
            $vBgColor = RANDOM_COLORS_ARR[array_rand(RANDOM_COLORS_ARR, 1)];
            $allData['typeBGColor'] = $vBgColor;
            if ('GetRentPostForAllUser' !== $_REQUEST['type']) {
                $allData['totalNum'] = $totalNum;
                $allData['TotalPages'] = $TotalPages;
            }
            $alldatanewArr[] = $allData;
        }
        $returnarray = [];
        if (\count($reqArr) > 0 && !empty($reqArr)) {
            foreach ($alldatanewArr as $key => $a) {
                foreach ($a as $k => $val) {
                    if (\in_array($k, $reqArr, true)) {
                        $returnarray[$k] = $val;
                    }
                }
            }
            $alldatanewArr = $returnarray;
        }
        if (isset($_REQUEST['test'])) {
        }

        return $alldatanewArr;
    }

    public function getRentItemPaymentPlan($use, $ssql = '', $start = 0, $per_page = 0, $vLanguage = 'EN', $iRentItemPostId = '', $ord = '', $reqArr = [])
    {
        global $obj, $LANG_OBJ;
        $limit = '';
        $eStatus = '';
        if ('admin' === $use) {
            $limit = "LIMIT {$start}, {$per_page}";
            if (0 === $start && 0 === $per_page) {
                $limit = '';
            }
            if (empty($ssql)) {
                $eStatus = 'AND ( eStatus = "Active" || eStatus = "Inactive" )';
            }
        }
        if ('webservice' === $use) {
            $eStatus = 'AND eStatus = "Active"';
        }
        if (empty($ord)) {
            $ord = ' ORDER BY iPaymentPlanId';
        }
        $pSql = '';
        if (!empty($iRentItemPostId)) {
            $iPaymentPlanId = get_value($this->rentitem_payment_log, 'iPaymentPlanId', 'iRentItemPostId', $iRentItemPostId, '', 'true');
            if ('' !== $iPaymentPlanId) {
                $paymnetstring = '1,'.$iPaymentPlanId;
            } else {
                $paymnetstring = '1';
            }
            $pSql = ' AND iPaymentPlanId NOT IN ('.$paymnetstring.')';
        }
        $sql = "SELECT iPaymentPlanId, vPlanName, iTotalDays,iTotalPost, fAmount, eStatus, JSON_UNQUOTE(JSON_VALUE(vPlanName, '$.vPlanName_".$vLanguage."')) as vPlanName, JSON_UNQUOTE(JSON_VALUE(tDescription, '$.tDescription_".$vLanguage."')) as tDescription, eAvailability, eFreePlan FROM {$this->PaymentPlanTableName} WHERE 1 = 1 {$pSql} {$eStatus} {$ssql} {$ord} ".$limit.'';
        $rentitem_plan_master_categories = $obj->MySQLSelect($sql);
        foreach ($rentitem_plan_master_categories as $key => $value) {
            if ('' === trim($value['tDescription']) || null === $value['tDescription']) {
                $rentitem_plan_master_categories[$key]['tDescription'] = '';
            }
        }
        $return_array = [];
        $return_array = $rentitem_plan_master_categories;

        return $return_array;
    }

    public function UpdateUserFavouriteRentPost($iMemberId, $iRentItemPostId, $eIsFavourite = 'No'): void
    {
        global $obj;
        if (isset($iRentItemPostId) && !empty($iRentItemPostId)) {
            $where = " iRentItemPostId = '".$iRentItemPostId."'";
            $RentItemPostData['eIsFavourite'] = $eIsFavourite;
            $RentItemPostData1 = $obj->MySQLQueryPerform($this->rentitem_post, $RentItemPostData, 'update', $where);
            if (1 === $RentItemPostData1) {
                $returnArr['Action'] = '1';
                $getRentItemPostData = $this->getRentItemPostFinal('webservice', '', $iMemberId, $vLanguage);
                $returnArr['message'] = $getRentItemPostData;
                if ('Yes' === $eIsFavourite) {
                    $returnArr['message2'] = 'LBL_RENT_ITEM_FAVOURITE_ADDED_SUCESSFULLY';
                } else {
                    $returnArr['message2'] = 'LBL_RENT_ITEM_FAVOURITE_REMOVE_SUCESSFULLY';
                }
            } else {
                $returnArr['Action'] = 0;
                $returnArr['message'] = 'LBL_NO_DATA_AVAIL';
            }
        } else {
            $returnArr['Action'] = 0;
            $returnArr['message'] = 'LBL_NO_DATA_AVAIL';
        }
        setDataResponse($returnArr);
    }

    public function createRentItemlog($use, $iRentItemPostId, $iMemberId = '', $vLanguage = 'EN')
    {
        global $obj;
        $postlogarray = [];
        if ('' !== $iRentItemPostId) {
            $getPostData = $this->getRentItemPostFinal('webservice', $iRentItemPostId, $iMemberId, $vLanguage);
            $postlogarray['iRentItemPostId'] = $getPostData[0]['iRentItemPostId'];
            $postlogarray['eStatus'] = $getPostData[0]['eStatusOrg'];
            $iRentItemPostLogId = $obj->MySQLQueryPerform($this->rent_item_post_status_log, $postlogarray, 'insert');
        }

        return $iRentItemPostLogId;
    }

    public function createRentItemPaymentlog($use, $iPaymentPlanId, $iRentItemPostId, $iMemberId = '', $vLanguage = 'EN')
    {
        global $obj;
        $postlogarray = [];
        $getRentItemPaymentPlanAmount = $this->getRentItemPlan('webservice', $iPaymentPlanId, $vLanguage);
        if (!empty($iPaymentPlanId) && !empty($iMemberId)) {
            $paymentlogData = $obj->MySQLSelect("SELECT * FROM {$this->rentitem_payment_log} WHERE iUserId = '".$iMemberId."' AND iPaymentPlanId = '".$iPaymentPlanId."' AND eStatus = 'Active' ORDER BY iUserPaymentPlanId DESC LIMIT 1");
            if (!empty($paymentlogData) && $paymentlogData[0]['iTotalPost'] > 0) {
                $iTotalPost = $paymentlogData[0]['iTotalPost'];
            } else {
                $iTotalPost = $getRentItemPaymentPlanAmount['iTotalPost'];
            }
        } else {
            $iTotalPost = $getRentItemPaymentPlanAmount['iTotalPost'];
        }
        $SelectpaymentlogData = $obj->MySQLSelect("SELECT * FROM {$this->rentitem_payment_log} WHERE iUserId = '".$iMemberId."' AND iRentItemPostId = '".$iRentItemPostId."'");
        $postlogarray['iUserId'] = $iMemberId;
        $postlogarray['tPlanPurchaseDay'] = date('Y-m-d H:i:s');
        $postlogarray['iRentItemPostId'] = $iRentItemPostId;
        $postlogarray['iPaymentPlanId'] = $iPaymentPlanId;
        $postlogarray['eStatus'] = 'Active';
        $postlogarray['iTotalDays'] = $getRentItemPaymentPlanAmount['iTotalDays'];
        if ($iTotalPost > 0) {
            $postlogarray['iTotalPost'] = $iTotalPost - 1;
        } else {
            $postlogarray['iTotalPost'] = $iTotalPost;
        }
        $iRentItemPaymentLogId = $obj->MySQLQueryPerform($this->rentitem_payment_log, $postlogarray, 'insert');

        return $iRentItemPaymentLogId;
    }

    public function getRentItemPlan($use, $iPaymentPlanId, $vLanguage = 'EN', $reqArr = [])
    {
        global $obj;
        $sql = '';
        $iPaymentPlanId = explode(',', $iPaymentPlanId);
        if (\count($iPaymentPlanId) > 1) {
            $sql .= ' iPaymentPlanId IN ('.$iPaymentPlanId.')';
        } else {
            $sql .= "iPaymentPlanId = '".$iPaymentPlanId[0]."'";
        }
        $rentitem_plan_master_categories = $obj->MySQLSelect("SELECT iPaymentPlanId, vPlanName,tDescription, iTotalDays, iTotalPost, fAmount, eStatus, JSON_UNQUOTE(JSON_VALUE(vPlanName, '$.vPlanName_".$vLanguage."')) as vPlanNameDefault, JSON_UNQUOTE(JSON_VALUE(tDescription, '$.tDescription_".$vLanguage."')) as tDescriptionDefault, eFreePlan, eFeaturedPlan, eAvailability,iMasterServiceCategoryId FROM {$this->PaymentPlanTableName} WHERE 1 = 1 AND {$sql}");
        $return_array = [];
        $return_array = $rentitem_plan_master_categories;
        if (\count($iRentItemId_) > 1) {
            return $return_array;
        }

        return $return_array[0];
    }

    public function createItemInquirylog($use, $reqArr = [], $vLanguage = 'EN')
    {
        global $obj;
        if ('' !== $reqArr) {
            $postlogarray = $reqArr;
            $iRentItemPostLogId = $obj->MySQLQueryPerform($this->rent_item_sendquery_log, $postlogarray, 'insert');
        }

        return $iRentItemPostLogId;
    }

    public function getRentItemMasterData($column, $field)
    {
        global $obj, $master_service_category_tbl, $oCache;
        $RentItemMasterApcKey = md5('RENT_ITEM_MASTER');
        $getRentItemMasterCacheData = $oCache->getData($RentItemMasterApcKey);
        if (!empty($getRentItemMasterCacheData) && \count($getRentItemMasterCacheData) > 0) {
            $rent_item_master = $getRentItemMasterCacheData;
        } else {
            $rent_item_master = $obj->MySQLSelect("SELECT iMasterServiceCategoryId, eType FROM {$master_service_category_tbl} WHERE eType IN ('RentItem', 'RentCars', 'RentEstate') ");
            $oCache->setData($RentItemMasterApcKey, $rent_item_master);
        }
        $master_data = [];
        if ('eType' === $field) {
            foreach ($rent_item_master as $master_service) {
                $master_data[$master_service['iMasterServiceCategoryId']] = $master_service['eType'];
            }
        } else {
            foreach ($rent_item_master as $master_service) {
                $master_data[$master_service['eType']] = $master_service['iMasterServiceCategoryId'];
            }
        }

        return $master_data[$column];
    }
}
