<?php



namespace Kesk\Web\Common;

class NearBy
{
    public function __construct()
    {
        $this->near_by_category = 'nearby_category';
        $this->near_by_Places = 'nearby_places';
        $this->vCurrentTime = @date('Y-m-d H:i:s');
        $this->languageLabelsArr = [];
        $this->system_default_timezone = date_default_timezone_get();
    }

    public function getNearByCategoryTotalCount($use, $ssql = '')
    {
        global $obj;
        $estatus = '';
        if ('admin' === $use) {
            if (empty($ssql)) {
                $estatus = 'AND ( estatus = "Active" || estatus = "Inactive" )';
            }
        }
        $result = $obj->MySQLSelect("SELECT count(iNearByCategoryId) as count FROM {$this->near_by_category} WHERE 1 = 1 {$estatus} {$ssql}");

        return $result[0]['count'];
    }

    public function getNearByPlacesTotalCount($use, $ssql = '')
    {
        global $obj;
        $estatus = '';
        if ('admin' === $use) {
            if (empty($ssql)) {
                $estatus = 'AND ( np.estatus = "Active" || np.estatus = "Inactive" )';
            }
        }
        $sql = "SELECT count(iNearByPlacesId) as count FROM {$this->near_by_Places} as np JOIN {$this->near_by_category} as nc ON (nc.iNearByCategoryId = np.iNearByCategoryId) WHERE 1 = 1 AND nc.eStatus != 'Deleted' {$estatus} {$ssql} ";
        $result = $obj->MySQLSelect($sql);

        return $result[0]['count'];
    }

    public function getUser($iUserId)
    {
        global $obj;
        $user = $obj->MySQLSelect("SELECT vPhoneCode,vCountry FROM `register_user` WHERE iUserId = '".$iUserId."'");

        return $user[0];
    }

    public function getUserNearByPlaces($use): void
    {
        global $LIST_PLACES_LIMIT_BY_DISTANCE, $obj, $tconfig, $LANG_OBJ, $DRIVER_ARRIVED_MIN_TIME_PER_MINUTE, $MODULES_OBJ, $_REQUEST;
        $GeneralMemberId = isset($_REQUEST['GeneralMemberId']) ? clean($_REQUEST['GeneralMemberId']) : '';
        $vLongitude = isset($_REQUEST['vLongitude']) ? clean($_REQUEST['vLongitude']) : '';
        $vLatitude = isset($_REQUEST['vLatitude']) ? clean($_REQUEST['vLatitude']) : '';
        $iCategoryId = isset($_REQUEST['iCategoryId']) ? clean($_REQUEST['iCategoryId']) : '';
        $lang = isset($_REQUEST['vGeneralLang']) ? clean($_REQUEST['vGeneralLang']) : '';
        $searchWord = $_REQUEST['searchWord'] ?? '';
        $iNearByPlacesId = $_REQUEST['iNearByPlacesId'] ?? '';
        $page = isset($_REQUEST['page']) ? trim($_REQUEST['page']) : 1;
        $userData = $this->getUser($GeneralMemberId);
        $sql = "SELECT eUnit FROM `country` WHERE vCountryCode = '".$userData['vCountry']."'";
        $CountryData = $obj->MySQLSelect($sql);
        $eUnit = $CountryData[0]['eUnit'];
        if ('' === $lang || null === $lang) {
            $lang = $LANG_OBJ->FetchDefaultLangData('vCode');
        }
        if ('' !== $iNearByPlacesId) {
            $place_display = 1;
        }
        $this->languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($lang, '1', '');
        $sourceLat = $vLatitude;
        $sourceLon = $vLongitude;
        $vLatitude = 'vPlacesLocationLat';
        $vLongitude = 'vPlacesLocationLong';
        $sql_1 = '';
        if ('' !== $iNearByPlacesId) {
            $sql_1 .= " AND iNearByPlacesId = '".$iNearByPlacesId."'";
        }
        $sql = 'SELECT ROUND(( 6371 * acos( cos( radians('.$sourceLat.') ) * cos( radians( ROUND('.$vLatitude.',8) ) ) * cos( radians( ROUND('.$vLongitude.',8) ) - radians('.$sourceLon.') ) + sin( radians('.$sourceLat.') ) * sin( radians( ROUND('.$vLatitude.',8) ) ) ) ),2) AS distance,iNearByPlacesId FROM `nearby_places` WHERE ('.$vLatitude." != '' AND ".$vLongitude." != '' AND eStatus='active') AND iNearByCategoryId = ".$iCategoryId." {$sql_1} HAVING distance < ".$LIST_PLACES_LIMIT_BY_DISTANCE.'';
        $data = $obj->MySQLSelect($sql);
        $iNearByPlacesId = implode(',', array_column($data, 'iNearByPlacesId'));
        $PlacesData = $this->getNearByPlace($use, $iNearByPlacesId, $lang, $searchWord);
        if (1 !== $place_display) {
            $Category = $this->getNearByCategory('webservice', '', '', '', $lang, '', ['iCategoryId', 'vTitle', 'vListLogo']);
            $key = array_search($iCategoryId, array_column($Category, 'iCategoryId'), true);
            if ($key >= 0) {
                $selectedCategory = $Category[$key];
                unset($Category[$key]);
                array_unshift($Category, $selectedCategory);
            }
            $returnArr['BANNER_DATA'] = $this->getBanner($lang);
            $returnArr['CATEGORY'] = $Category;
            $returnArr['SELECTED_CATEGORY_ID'] = $iCategoryId;
        }
        if (isset($PlacesData) && !empty($PlacesData)) {
            $i = 0;
            foreach ($PlacesData as $Places) {
                $keys = array_search($Places['iNearByPlacesId'], array_column($data, 'iNearByPlacesId'), true);
                if ('Miles' === $eUnit) {
                    $distance = number_format(round($data[$keys]['distance'] * 0.621_371, 2)).' mi';
                } else {
                    $distance = number_format($data[$keys]['distance']).' km';
                }
                $PlacesData[$i]['duration'] = $distance;
                $PlacesData[$i]['distance'] = $data[$keys]['distance'];
                if (!empty($Places['vImage']) && file_exists($tconfig['tsite_upload_images_nearby_item_path'].$Places['vImage'])) {
                    $PlacesData[$i]['vImages'][0] = $tconfig['tsite_upload_images_nearby_item'].$Places['vImage'];
                } else {
                    $PlacesData[$i]['vImages'] = [$tconfig['tsite_img'].'/placeholderimage.jpg'];
                }
                $WorkingHoursDetails = $this->GetPlacesWorkingHoursDetails($Places['vWorkingHours']);
                $PlacesData[$i]['placesStatus'] = $WorkingHoursDetails['placesStatus'];
                $PlacesData[$i]['statusMessage'] = $WorkingHoursDetails['statusMessage'];
                if (1 === $place_display) {
                    $PlacesData[$i]['duration'] = $this->languageLabelsArr['LBL_APPROX_DISTANCE_TXT'].' '.str_replace('#HOURS#', $distance, $this->languageLabelsArr['LBL_AWAY_NEARBY']);
                    foreach ($WorkingHoursDetails as $key => $Places_) {
                        $PlacesData[$i][$key] = $Places_;
                    }
                    $PlacesData[$i]['placesStatus'] = $WorkingHoursDetails['placesStatus'];
                    $PlacesData[$i]['statusMessage'] = $WorkingHoursDetails['statusMessage'];
                    $PlacesData[$i]['openCloseTimeMessage'] = $WorkingHoursDetails['openCloseTimeMessage'];
                    $PlacesData[$i]['ServiceAction'] = $this->serviceAction($lang, $Places);
                }
                if ($Places['iCompanyId'] > 0 && $MODULES_OBJ->isDeliverAllFeatureAvailable()) {
                    $getCompanyDetails = $this->getCompanyDetails($Places['iCompanyId'], $place_display);
                    if (1 === $place_display) {
                        foreach ($getCompanyDetails as $key => $Places_) {
                            if (isset($PlacesData[$i][$key])) {
                                $PlacesData[$i][$key] = $Places_;
                            }
                        }
                    } else {
                        $PlacesData[$i]['placesStatus'] = $getCompanyDetails['placesStatus'];
                        $PlacesData[$i]['statusMessage'] = $getCompanyDetails['statusMessage'];
                    }
                }
                if ('Inactive' === $Places['storeActive']) {
                    $PlacesData[$i]['openCloseTimeMessage'] = '';
                }
                unset($PlacesData[$i]['vWorkingHours'], $PlacesData[$i]['vImage']);
                ++$i;
            }
            $total = \count($PlacesData);
            $per_page = 7;
            $totalPages = ceil($total / $per_page);
            $start_limit = ($page - 1) * $per_page;
            $PlacesData = \array_slice($PlacesData, $start_limit, $per_page);
            $returnArr['Action'] = '1';
            $returnArr['Places'] = $PlacesData;
            $returnArr['totalPages'] = $totalPages;
            if ($totalPages > $page) {
                $returnArr['NextPage'] = ($page + 1);
            } else {
                $returnArr['NextPage'] = '0';
            }
            $returnArr['message'] = '';
            setDataResponse($returnArr);
        } else {
            $returnArr['Action'] = '1';
            $returnArr['message'] = 'LBL_NO_DATA_AVAIL';
            $returnArr['Places'] = [];
            setDataResponse($returnArr);
        }
    }

    public function getNearByPlace($use, $iNearByPlacesId, $vLanguage = 'EN', $searchWord = '')
    {
        global $obj;
        $HAVING = $sql = '';
        $iNearByPlacesId_ = explode(',', $iNearByPlacesId);
        if (\count($iNearByPlacesId_) > 1) {
            $sql .= ' np.iNearByPlacesId IN ('.$iNearByPlacesId.')';
        } else {
            $sql .= " np.iNearByPlacesId = '".$iNearByPlacesId."'";
        }
        if ('' !== $searchWord) {
            $sql .= " AND (np.vTitle like '%{$searchWord}%')";
        }
        if ('webservice' === $use) {
        }
        $sql = "SELECT CASE WHEN c.iCompanyId > 0 THEN c.eStatus ELSE 'Active' END as storeActive, np.iServiceId,JSON_UNQUOTE(JSON_VALUE(nc.vTitle, '$.vTitle_".$vLanguage."')) as vCategoryName , np.iNearByPlacesId, np.iNearByCategoryId, np.vPlacesLocation, np.vPlacesLocationLat, np.vPlacesLocationLong, np.vAddress, np.vWorkingHours, np.vPhone,CONCAT( np.vCode,np.vPhone ) as vPhoneWithCode, np.iCompanyId, np.vOfferDiscount, JSON_UNQUOTE(JSON_VALUE(np.vAboutPlaces, '$.vAboutPlaces_".$vLanguage."')) as vAboutPlaces, np.vAboutPlaces as vAboutPlacesOrg, np.eStatus, np.vImage, np.vTitle ,np.vCode FROM {$this->near_by_Places} as np JOIN {$this->near_by_category} as nc ON (nc.iNearByCategoryId = np.iNearByCategoryId) LEFT JOIN company as c ON (c.iCompanyId = np.iCompanyId) WHERE 1 = 1 AND {$sql} {$HAVING}";
        $nearbyPlaces = $obj->MySQLSelect($sql);
        $return_array = $nearbyPlaces;
        if (\count($iNearByPlacesId_) > 1 || 'webservice' === $use) {
            return $return_array;
        }

        return $return_array[0];
    }

    public function getNearByCategory($use, $ssql = '', $start = 0, $per_page = 0, $vLanguage = 'EN', $ord = '', $reqArr = [])
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
        if (empty($ord)) {
            $ord = ' ORDER BY iDisplayOrder';
        }
        if ('webservice' === $use) {
            $estatus = 'AND estatus = "Active"';
        }
        $sql = "SELECT iNearByCategoryId, iNearByCategoryId as near_by_cat ,(SELECT count(np.iNearByCategoryId) FROM nearby_places np WHERE np.iNearByCategoryId = near_by_cat AND eStatus != 'Deleted') as totalPlaces , vImage, eStatus, iDisplayOrder, vTitle as vTitle_json, JSON_UNQUOTE(JSON_VALUE(vTitle, '$.vTitle_".$vLanguage."')) as vTitle, vTextColor, vBgColor FROM {$this->near_by_category} WHERE 1 = 1 {$estatus} {$ssql} {$ord} ".$limit.'';
        $nearby_master_categories = $obj->MySQLSelect($sql);
        if ('webservice' === $use) {
            foreach ($nearby_master_categories as $key => $mServiceCategory) {
                $return_array[$key] = $this->getNearByQuery_array($mServiceCategory, $reqArr);
            }
        } else {
            $return_array = $nearby_master_categories;
        }

        return $return_array;
    }

    public function getNearByQuery_array($mServiceCategory, $reqArr)
    {
        global $tconfig, $APP_TYPE;
        $return_array = [];
        $return_array['iCategoryId'] = $mServiceCategory['iNearByCategoryId'];
        $return_array['vTitle'] = $mServiceCategory['vTitle'];
        $return_array['vCategory'] = $mServiceCategory['vTitle'];
        $return_array['vTextColor'] = $mServiceCategory['vTextColor'];
        $return_array['vBgColor'] = getColorFromImage($tconfig['tsite_upload_images_nearby_item_path'].$mServiceCategory['vImage']);
        $return_array['vImage'] = $tconfig['tsite_upload_images_nearby_item'].$mServiceCategory['vImage'];
        $return_array['eCatType'] = 'NearBy';
        $return_array['vListLogo'] = $tconfig['tsite_upload_images_nearby_item'].$mServiceCategory['vImage'];
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

    public function getBanner($vLanguage)
    {
        global $obj, $tconfig;
        $whereloc = " AND iLocationid IN ('-1')";
        $Data_banners = $obj->MySQLSelect("SELECT vImage,vStatusBarColor,iUniqueId FROM banners WHERE vCode= '".$vLanguage."' AND vImage != '' AND eStatus = 'Active' AND (iServiceId = '0' OR iServiceId = '') AND eBuyAnyService NOT IN ('Genie', 'Runner') AND eType = 'NearBy' AND eFor = 'General' {$whereloc} ORDER BY iDisplayOrder ASC");
        $dataOfBanners = [];
        $count = 0;
        for ($i = 0; $i < \count($Data_banners); ++$i) {
            if (isset($Data_banners[$i]['vImage']) && '' !== $Data_banners[$i]['vImage']) {
                $dataOfBanners[$count]['vImage'] = $tconfig['tsite_url'].'assets/img/images/'.$Data_banners[$i]['vImage'];
                $dataOfBanners[$count]['vStatusBarColor'] = $Data_banners[$i]['vStatusBarColor'];
                $banner_img_path = $tconfig['tpanel_path'].'assets/img/images/'.$Data_banners[$i]['vImage'];
                if (file_exists($banner_img_path) && empty($Data_banners[$i]['vStatusBarColor'])) {
                    $dataOfBanners[$count]['vStatusBarColor'] = getColorFromImage($banner_img_path);
                    $obj->sql_query("UPDATE banners SET vStatusBarColor = '".$dataOfBanners[$count]['vStatusBarColor']."' WHERE iUniqueId = '".$Data_banners[$i]['iUniqueId']."'");
                }
                ++$count;
            }
        }

        return $dataOfBanners;
    }

    public function ConvertMinutestoHumanReadable($minutes)
    {
        $days = floor($minutes / 1_440);
        $hours = floor(($minutes - $days * 1_440) / 60);
        $minutes = $minutes - ($days * 1_440) - ($hours * 60);
        $returnText = '';
        if ($days >= 1) {
            if (1 === $days) {
                $day_text = $this->languageLabelsArr['LBL_DAY_TXT'];
            } else {
                $day_text = $this->languageLabelsArr['LBL_DAYS_TXT'];
            }
            $returnText .= $days.' '.$day_text;
        }
        if ($hours >= 1) {
            if (1 === $hours) {
                $hour_text = $this->languageLabelsArr['LBL_HOUR_TXT'];
            } else {
                $hour_text = $this->languageLabelsArr['LBL_HOURS_TXT'];
            }
            $returnText .= ' '.$hours.' '.$hour_text;
        }
        if ($minutes >= 0) {
            $returnText .= ' '.round($minutes).' '.$this->languageLabelsArr['LBL_MINUTES_TXT'];
        }

        return trim($returnText);
    }

    public function getDayWiseKey($day = '')
    {
        $vCurrentTime = @date('Y-m-d H:i:s');
        if ('' === $day) {
            $day = date('l', strtotime($this->vCurrentTime));
        }

        switch ($day) {
            case 'Monday' === $day:
                $vFromTimeSlot1 = 'vMonFromSlot1';
                $vToTimeSlot1 = 'vMonToSlot1';

                break;

            case 'Tuesday' === $day:
                $vFromTimeSlot1 = 'vTueFromSlot1';
                $vToTimeSlot1 = 'vTueToSlot1';

                break;

            case 'Wednesday' === $day:
                $vFromTimeSlot1 = 'vWedFromSlot1';
                $vToTimeSlot1 = 'vWedToSlot1';

                break;

            case 'Thursday' === $day:
                $vFromTimeSlot1 = 'vThuFromSlot1';
                $vToTimeSlot1 = 'vThuToSlot1';

                break;

            case 'Friday' === $day:
                $vFromTimeSlot1 = 'vFriFromSlot1';
                $vToTimeSlot1 = 'vFriToSlot1';

                break;

            case 'Saturday' === $day:
                $vFromTimeSlot1 = 'vSatFromSlot1';
                $vToTimeSlot1 = 'vSatToSlot1';

                break;

            case 'Sunday' === $day:
                $vFromTimeSlot1 = 'vSunFromSlot1';
                $vToTimeSlot1 = 'vSunToSlot1';

                break;

            default:
                echo 'break in generalFunction() @ 54XX';

                exit;

                break;
        }
        $arr['vFromTimeSlot1'] = $vFromTimeSlot1;
        $arr['vToTimeSlot1'] = $vToTimeSlot1;

        return $arr;
    }

    public function getDayWiseKey2($day = '')
    {
        if ('' === $day) {
            $day = date('l', strtotime($this->vCurrentTime));
        }

        switch ($day) {
            case 'Monday' === $day:
                $vFromTimeSlot1 = 'vMonFromSlot2';
                $vToTimeSlot1 = 'vMonToSlot2';

                break;

            case 'Tuesday' === $day:
                $vFromTimeSlot1 = 'vTueFromSlot2';
                $vToTimeSlot1 = 'vTueToSlot2';

                break;

            case 'Wednesday' === $day:
                $vFromTimeSlot1 = 'vWedFromSlot2';
                $vToTimeSlot1 = 'vWedToSlot2';

                break;

            case 'Thursday' === $day:
                $vFromTimeSlot1 = 'vThuFromSlot2';
                $vToTimeSlot1 = 'vThuToSlot2';

                break;

            case 'Friday' === $day:
                $vFromTimeSlot1 = 'vFriFromSlot2';
                $vToTimeSlot1 = 'vFriToSlot2';

                break;

            case 'Saturday' === $day:
                $vFromTimeSlot1 = 'vSatFromSlot2';
                $vToTimeSlot1 = 'vSatToSlot2';

                break;

            case 'Sunday' === $day:
                $vFromTimeSlot1 = 'vSunFromSlot2';
                $vToTimeSlot1 = 'vSunToSlot2';

                break;

            default:
                echo 'break in generalFunction() @ 54XX';

                exit;

                break;
        }
        $arr['vFromTimeSlot2'] = $vFromTimeSlot1;
        $arr['vToTimeSlot2'] = $vToTimeSlot1;

        return $arr;
    }

    public function serviceAction($lang, $PlacesData)
    {
        global $tconfig, $MODULES_OBJ;
        $i = 0;
        $result_arr = [];
        $result_arr[$i]['vTitle'] = $this->languageLabelsArr['LBL_CALL_THIS_PLACE_NEARBY'];
        $result_arr[$i]['eType'] = 'Call';
        $result_arr[$i]['vImage'] = $tconfig['tsite_img'].'/customer-service.png';
        $result_arr[$i]['vPhone'] = '+'.$PlacesData['vPhoneWithCode'];
        ++$i;
        $isEnableGenieFeature = $MODULES_OBJ->isEnableGenieFeature();
        if ($MODULES_OBJ->isRideFeatureAvailable() && (($PlacesData['iCompanyId'] > 0 && 'Active' === $PlacesData['storeActive']) || 0 === $PlacesData['iCompanyId'])) {
            $result_arr[$i]['vTitle'] = $this->languageLabelsArr['LBL_BOOK_TAXI_NEARBY'];
            $result_arr[$i]['eType'] = 'Taxi';
            $result_arr[$i]['vImage'] = $tconfig['tsite_img'].'/taxi.png';
            $result_arr[$i]['vPlacesLocationLat'] = $PlacesData['vPlacesLocationLat'];
            $result_arr[$i]['vPlacesLocationLong'] = $PlacesData['vPlacesLocationLong'];
            $result_arr[$i]['vPlacesLocation'] = $PlacesData['vPlacesLocation'];
            ++$i;
        }
        if (!empty($PlacesData['iCompanyId']) && $MODULES_OBJ->isDeliverAllFeatureAvailable() && 'Active' === $PlacesData['storeActive']) {
            if (1 === $PlacesData['iServiceId']) {
                $result_arr[$i]['vTitle'] = $this->languageLabelsArr['LBL_ORDER_FOOD_NEARBY'];
                $result_arr[$i]['vImage'] = $tconfig['tsite_img'].'/fast-food.png';
            } else {
                $result_arr[$i]['vTitle'] = $this->languageLabelsArr['LBL_ORDER_ITEMS_NEARBY'];
                $result_arr[$i]['vImage'] = $tconfig['tsite_img'].'/grocery.png';
            }
            $result_arr[$i]['eType'] = 'DeliveryAll';
            $result_arr[$i]['CompanyDetails'] = $this->getCompanyDetails($PlacesData['iCompanyId']);
            ++$i;
        } elseif ($MODULES_OBJ->isEnableAnywhereDeliveryFeature() && $isEnableGenieFeature && 0 === $PlacesData['iCompanyId']) {
            $result_arr[$i]['vTitle'] = $this->languageLabelsArr['LBL_ORDER_ANYTHING_NEARBY'];
            $result_arr[$i]['eType'] = 'Genie';
            $result_arr[$i]['vPlacesLocationLat'] = $PlacesData['vPlacesLocationLat'];
            $result_arr[$i]['vPlacesLocationLong'] = $PlacesData['vPlacesLocationLong'];
            $result_arr[$i]['vPlacesLocation'] = $PlacesData['vPlacesLocation'];
            $result_arr[$i]['vStoreName'] = $PlacesData['vTitle'];
            $result_arr[$i]['vImage'] = $tconfig['tsite_img'].'/grocery.png';
            ++$i;
        }
        $result_arr[$i]['vTitle'] = $this->languageLabelsArr['LBL_LOCATION_NEARBY'];
        $result_arr[$i]['eType'] = 'Location';
        $result_arr[$i]['vImage'] = $tconfig['tsite_img'].'/map.png';
        $result_arr[$i]['vPlacesLocationLat'] = $PlacesData['vPlacesLocationLat'];
        $result_arr[$i]['vPlacesLocationLong'] = $PlacesData['vPlacesLocationLong'];
        $result_arr[$i]['vPlacesLocation'] = $PlacesData['vPlacesLocation'];
        ++$i;
        if ('' !== $PlacesData['vOfferDiscount']) {
            $result_arr[$i]['vTitle'] = $this->languageLabelsArr['LBL_DISCOUNT_AND_OFFER_NEARBY'];
            $result_arr[$i]['eType'] = 'DiscountOffer';
            $result_arr[$i]['vImage'] = $tconfig['tsite_img'].'/discount.png';
            $result_arr[$i]['vOfferDiscount'] = $PlacesData['vOfferDiscount'];
        }

        return $result_arr;
    }

    public function getCompanyDetails($iCompanyId, $slot = 1)
    {
        global $obj, $iServiceId;
        $iUserId = $this->iUserId;
        $getCompanyDetails = FetchNearByStores('', '', $this->iUserId, '', '', '', $iServiceId, $iCompanyId);
        $ServiceCategoryData = $obj->MySQLSelect("SELECT eType FROM service_categories WHERE iServiceId = {$iServiceId}");
        $getCompanyDetails[0]['ispriceshow'] = $ServiceCategoryData[0]['eType'];
        $CompanyDetails = $getCompanyDetails[0];
        $returnArr['Restaurant_Safety_URL'] = $CompanyDetails['Restaurant_Safety_URL'];
        $returnArr['Restaurant_Safety_Icon'] = $CompanyDetails['Restaurant_Safety_Icon'];
        $returnArr['Restaurant_Safety_Status'] = $CompanyDetails['Restaurant_Safety_Status'];
        $returnArr['Restaurant_Cuisine'] = $CompanyDetails['Restaurant_Cuisine'];
        $returnArr['vAvgRating'] = $CompanyDetails['vAvgRating'];
        $returnArr['vCaddress'] = $CompanyDetails['vCaddress'];
        $returnArr['vCoverImage'] = $CompanyDetails['vCoverImage'];
        $returnArr['iCompanyId'] = $CompanyDetails['iCompanyId'];
        $returnArr['vCompany'] = $CompanyDetails['vCompany'];
        $returnArr['restaurantstatus'] = $CompanyDetails['restaurantstatus'];
        $returnArr['ispriceshow'] = $CompanyDetails['ispriceshow'];
        $returnArr['eAvailable'] = $CompanyDetails['eAvailable'];
        $returnArr['timeslotavailable'] = $CompanyDetails['timeslotavailable'];
        $returnArr['iServiceId'] = $CompanyDetails['iServiceId'];
        $DayWiseKey = $this->getDayWiseKey();
        $fromTime = date('H:i', strtotime($this->timezone($CompanyDetails[$DayWiseKey['vFromTimeSlot1']])));
        $toTime = date('H:i', strtotime($this->timezone($CompanyDetails[$DayWiseKey['vToTimeSlot1']])));
        $DayWiseKey2 = $this->getDayWiseKey2();
        $vFromTimeSlot2_org = $CompanyDetails[$DayWiseKey2['vFromTimeSlot2']];
        $vToTimeSlot2_org = $CompanyDetails[$DayWiseKey2['vToTimeSlot2']];
        $fromTime2 = date('H:i', strtotime($this->timezone($CompanyDetails[$DayWiseKey2['vFromTimeSlot2']])));
        $toTime2 = date('H:i', strtotime($this->timezone($CompanyDetails[$DayWiseKey2['vToTimeSlot2']])));
        $date = @date('H:i', strtotime($this->timezone($this->vCurrentTime)));
        $NextDayWiseKey = date('l', strtotime('+1 day', strtotime($date)));
        $NextDayWiseKey = $this->getDayWiseKey($NextDayWiseKey);
        $NextfromTime = date('H:i', strtotime($CompanyDetails[$NextDayWiseKey['vFromTimeSlot1']]));
        $status = 'Closed';
        $message = $this->languageLabelsArr['LBL_NEARBY_PLACE_CLOSED_STATUS_TXT'];
        $open_close_time = str_replace('#TIME#', date('h:i A', strtotime($NextfromTime)), $this->languageLabelsArr['LBL_CLOSED_TIME_NEARBY']);
        if (1 === isBetween($fromTime, $toTime, $date)) {
            $status = 'Open';
            $message = $this->languageLabelsArr['LBL_NEARBY_PLACE_OPEN_STATUS_TXT'];
            $open_close_time = str_replace('#TIME#', date('h:i A', strtotime($toTime)), $this->languageLabelsArr['LBL_OPEN_TIME_NEARBY']);
        } elseif (1 === isBetween($fromTime2, $toTime2, $date) && '00:00' !== $vFromTimeSlot2_org) {
            $status = 'Open';
            $message = $this->languageLabelsArr['LBL_NEARBY_PLACE_OPEN_STATUS_TXT'];
            $open_close_time = str_replace('#TIME#', date('h:i A', strtotime($toTime2)), $this->languageLabelsArr['LBL_OPEN_TIME_NEARBY']);
        } elseif (!empty($fromTime2) && '00:00' !== $vFromTimeSlot2_org && !empty($toTime2)) {
            $solt2DayWiseKey = $this->getDayWiseKey2();
            $NextfromTime = date('H:i', strtotime($this->timezone($CompanyDetails[$solt2DayWiseKey['vFromTimeSlot2']])));
            $status = 'Closed';
            $message = $this->languageLabelsArr['LBL_NEARBY_PLACE_CLOSED_STATUS_TXT'];
            $open_close_time = str_replace('#TIME#', date('h:i A', strtotime($NextfromTime)), $this->languageLabelsArr['LBL_CLOSED_TIME_NEARBY']);
        }
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        foreach ($days as $day) {
            $DayWiseKey = $this->getDayWiseKey($day);
            $fromTime = date('h:i A', strtotime(str_replace(['PM', 'AM', ''], ['', '', ''], $this->timezone($CompanyDetails[$DayWiseKey['vFromTimeSlot1']]))));
            $toTime = date('h:i A', strtotime(str_replace(['PM', 'AM', ''], ['', '', ''], $this->timezone($CompanyDetails[$DayWiseKey['vToTimeSlot1']]))));
            $DayWiseKey2 = $this->getDayWiseKey2($day);
            $fromTime2 = date('h:i A', strtotime(str_replace(['PM', 'AM', ''], ['', '', ''], $this->timezone($CompanyDetails[$DayWiseKey2['vFromTimeSlot2']]))));
            $toTime2 = date('h:i A', strtotime(str_replace(['PM', 'AM', ''], ['', '', ''], $this->timezone($CompanyDetails[$DayWiseKey2['vToTimeSlot2']]))));
            $returnArr[$DayWiseKey['vToTimeSlot1']] = $fromTime.'-'.$toTime."\n".$fromTime2.'-'.$toTime2;
        }
        $returnArr['placesStatus'] = $status;
        $returnArr['statusMessage'] = $message;
        $returnArr['openCloseTimeMessage'] = $open_close_time;
        if (0 === $slot) {
            $returnArr = [];
            $returnArr['placesStatus'] = $status;
            $returnArr['statusMessage'] = $message;
        }

        return $returnArr;
    }

    public function getNearByPlaces($use, $ssql = '', $start = 0, $per_page = 0, $vLanguage = 'EN', $ord = '', $reqArr = [])
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
                $estatus = 'AND ( np.estatus = "Active" || np.estatus = "Inactive" )';
            }
        }
        if (empty($ord)) {
            $ord = ' ORDER BY iDisplayOrder';
        }
        $sql = "SELECT JSON_UNQUOTE(JSON_VALUE(nc.vTitle, '$.vTitle_".$vLanguage."')) as categoryName , nc.iNearByCategoryId, nc.eStatus as categoryStatus, np.iNearByPlacesId, np.iNearByCategoryId, np.vPlacesLocation, np.vPlacesLocationLat, np.vPlacesLocationLong, np.vAddress, np.vWorkingHours, np.vPhone, np.iCompanyId, np.vOfferDiscount, JSON_UNQUOTE(JSON_VALUE(np.vAboutPlaces, '$.vAboutPlaces_".$vLanguage."')) as vAboutPlaces, np.eStatus, np.vImage, np.vTitle FROM {$this->near_by_Places} as np JOIN {$this->near_by_category} as nc ON (nc.iNearByCategoryId = np.iNearByCategoryId) WHERE 1 = 1 AND nc.eStatus != 'Deleted' {$estatus} {$ssql} {$ord} ".$limit.'';
        $places = $obj->MySQLSelect($sql);
        $return_array = $places;

        return $return_array;
    }

    public function getNearByCat($use, $iNearByCategoryId, $vLanguage = 'EN')
    {
        global $obj;
        $sql = '';
        $iNearByCategoryId_ = explode(',', $iNearByCategoryId);
        if (\count($iNearByCategoryId_) > 1) {
            $sql .= ' iNearByCategoryId IN ('.$iNearByCategoryId.')';
        } else {
            $sql .= "iNearByCategoryId = '".$iNearByCategoryId."'";
        }
        $nearbyCat = $obj->MySQLSelect("SELECT eStatus,iDisplayOrder,iNearByCategoryId,vImage,JSON_UNQUOTE(JSON_VALUE(vTitle, '$.vTitle_".$vLanguage."')) as vTitle,vTitle as vTitle_json, vTextColor, vBgColor FROM {$this->near_by_category} WHERE 1 = 1 AND {$sql}");
        $return_array = $nearbyCat;
        if (\count($iNearByCategoryId_) > 1) {
            return $return_array;
        }

        return $return_array[0];
    }

    public function getStore($use, $iNearByPlacesId = '')
    {
        global $obj;
        $sql = '';
        if (!empty($iNearByPlacesId)) {
            $sql = " AND iNearByPlacesId != '".$iNearByPlacesId."'";
        }
        $sql = "SELECT GROUP_CONCAT(DISTINCT(iCompanyId)) as iCompanyId FROM `nearby_places` WHERE 1 = 1 {$sql}";
        $db_storelist = $obj->MySQLSelect($sql);

        return $db_storelist[0];
    }

    private function GetPlacesWorkingHoursDetails($vWorkingHours)
    {
        $vWorkingHours = json_decode($vWorkingHours, true);
        $DayWiseKey = $this->getDayWiseKey();
        $fromTime = str_replace(['PM', 'AM'], ['', ''], $vWorkingHours[$DayWiseKey['vFromTimeSlot1']]);
        $toTime = str_replace(['PM', 'AM'], ['', ''], $vWorkingHours[$DayWiseKey['vToTimeSlot1']]);
        $fromTime = date('H:i', strtotime($this->timezone($fromTime)));
        $toTime = date('H:i', strtotime($this->timezone($toTime)));
        $DayWiseKey2 = $this->getDayWiseKey2();
        $fromTime2_org = $vWorkingHours[$DayWiseKey2['vFromTimeSlot2']];
        $toTime2_org = $vWorkingHours[$DayWiseKey2['vToTimeSlot2']];
        $fromTime2 = str_replace(['PM', 'AM'], ['', ''], $vWorkingHours[$DayWiseKey2['vFromTimeSlot2']]);
        $toTime2 = str_replace(['PM', 'AM'], ['', ''], $vWorkingHours[$DayWiseKey2['vToTimeSlot2']]);
        $fromTime2 = date('H:i', strtotime($this->timezone($fromTime2)));
        $toTime2 = date('H:i', strtotime($this->timezone($toTime2)));
        $date = @date('H:i', strtotime($this->timezone($this->vCurrentTime)));
        $status = 'Closed';
        $message = $this->languageLabelsArr['LBL_NEARBY_PLACE_CLOSED_STATUS_TXT'];
        $open_close_time = str_replace('#TIME#', date('h:i A', strtotime($fromTime)), $this->languageLabelsArr['LBL_CLOSED_TIME_NEARBY']);
        if (1 === isBetween($fromTime, $toTime, $date)) {
            $status = 'Open';
            $message = $this->languageLabelsArr['LBL_NEARBY_PLACE_OPEN_STATUS_TXT'];
            $open_close_time = str_replace('#TIME#', date('h:i A', strtotime($toTime)), $this->languageLabelsArr['LBL_OPEN_TIME_NEARBY']);
        } elseif (1 === isBetween($fromTime2, $toTime2, $date) && '' !== $fromTime2_org) {
            $status = 'Open';
            $message = $this->languageLabelsArr['LBL_NEARBY_PLACE_OPEN_STATUS_TXT'];
            $open_close_time = str_replace('#TIME#', date('h:i A', strtotime($toTime2)), $this->languageLabelsArr['LBL_OPEN_TIME_NEARBY']);
        } elseif (!empty($fromTime2_org) && '' !== $fromTime2_org && !empty($toTime2_org)) {
            $solt2DayWiseKey = $this->getDayWiseKey2();
            $NextfromTime = str_replace(['PM', 'AM'], ['', ''], $vWorkingHours[$solt2DayWiseKey['vFromTimeSlot2']]);
            $NextfromTime = date('H:i', strtotime($this->timezone($NextfromTime)));
            $status = 'Closed';
            $message = $this->languageLabelsArr['LBL_NEARBY_PLACE_CLOSED_STATUS_TXT'];
            $open_close_time = str_replace('#TIME#', date('h:i A', strtotime($NextfromTime)), $this->languageLabelsArr['LBL_CLOSED_TIME_NEARBY']);
        }
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        foreach ($days as $day) {
            $DayWiseKey = $this->getDayWiseKey($day);
            $fromTime = date('h:i A', strtotime($this->timezone(str_replace(['PM', 'AM', ''], ['', '', ''], $vWorkingHours[$DayWiseKey['vFromTimeSlot1']]))));
            $toTime = date('h:i A', strtotime($this->timezone(str_replace(['PM', 'AM', ''], ['', '', ''], $vWorkingHours[$DayWiseKey['vToTimeSlot1']]))));
            $DayWiseKey2 = $this->getDayWiseKey2($day);
            if (!isset($vWorkingHours[$DayWiseKey2['vFromTimeSlot2']]) || empty($vWorkingHours[$DayWiseKey2['vFromTimeSlot2']])) {
                $vWorkingHours[$DayWiseKey2['vFromTimeSlot2']] = '00:00';
                $vWorkingHours[$DayWiseKey2['vToTimeSlot2']] = '00:00';
                $from2to2 = '';
            } else {
                $fromTime2 = date('h:i A', strtotime($this->timezone(str_replace(['PM', 'AM', ''], ['', '', ''], $vWorkingHours[$DayWiseKey2['vFromTimeSlot2']]))));
                $toTime2 = date('h:i A', strtotime($this->timezone(str_replace(['PM', 'AM', ''], ['', '', ''], $vWorkingHours[$DayWiseKey2['vToTimeSlot2']]))));
                $from2to2 = $fromTime2.'-'.$toTime2;
            }
            $returnArr[$DayWiseKey['vToTimeSlot1']] = $fromTime.'-'.$toTime."\n".$from2to2;
        }
        $returnArr['placesStatus'] = $status;
        $returnArr['statusMessage'] = $message;
        $returnArr['openCloseTimeMessage'] = $open_close_time;

        return $returnArr;
    }

    private function timezone($time)
    {
        $vTimeZone = isset($_REQUEST['vTimeZone']) ? clean($_REQUEST['vTimeZone']) : '';

        return $time;
    }
}
