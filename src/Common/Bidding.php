<?php



namespace Kesk\Web\Common;

use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFProbe;
use FFMpeg\Format\Video\X264;

class Bidding
{
    public function __construct()
    {
        global $obj;
        $this->tablename = 'bidding_service';
        $this->biddingPostTable = 'bidding_post';
        $this->register_user = 'register_user';
        $this->register_driver = 'register_driver';
        $this->user_address = 'user_address';
        $this->bidding_driver_request = 'bidding_driver_request';
        $this->bidding_driver_service = 'bidding_driver_service';
        $this->bidding_request_to_driver = 'driver_bidding_request';
        $this->bidding_offer = 'bidding_offer';
        $this->bidding_post_media = 'bidding_post_media';
        $bidding = $obj->MySQLSelect("SELECT iBiddingId FROM {$this->tablename} WHERE 1 = 1 AND eOther = 'Yes'");
        $this->other_id = (!empty($bidding) && $bidding[0]['iBiddingId']) ? $bidding[0]['iBiddingId'] : 0;
    }

    public function getBiddingTotalCount($use, $iParent_Id = 0)
    {
        global $obj;
        $estatus = '';
        if ('admin' === $use) {
            $estatus = 'AND ( estatus = "Active" || estatus = "Inactive" )';
        }
        $iParentId = "iParentId = '".$iParent_Id."'";
        $result = $obj->MySQLSelect("SELECT count(iBiddingId) as count FROM {$this->tablename} WHERE 1 = 1 AND ".$iParentId." {$estatus} ");

        return $result[0]['count'];
    }

    public function getBiddingMaster($use, $ssql = '', $start = 0, $per_page = 0, $vLanguage = 'EN', $ord = '', $reqArr = [])
    {
        global $obj, $MODULES_OBJ;
        $limit = '';
        $estatus = '';
        if ('admin' === $use) {
            $limit = "LIMIT {$start}, {$per_page}";
            if (0 === $start && 0 === $per_page) {
                $limit = '';
            }
            $estatus = "AND ( estatus = 'Active' || estatus = 'Inactive' )";
        }
        if ('webservice' === $use) {
            $estatus = "AND estatus = 'Active'";
        }
        if (empty($ord)) {
            $ord = ' ORDER BY iDisplayOrder';
        }
        $db_fields = '';
        if ($MODULES_OBJ->isEnableAppHomeScreenLayoutV3()) {
            $db_fields .= ', vImage1';
        }
        $sql = "SELECT eOther,vTitle,iBiddingId,vImage,vTitle,tDescription,iParentId,eStatus,iDisplayOrder,JSON_UNQUOTE(JSON_VALUE(vTitle, '$.vTitle_".$vLanguage."')) as vTitle,JSON_UNQUOTE(JSON_VALUE(tDescription, '$.tDescription_".$vLanguage."')) as tDescription {$db_fields} FROM ".$this->tablename.' WHERE 1 = 1 '.$estatus.' '.$ssql.' AND iParentId = 0 '.$ord.' '.$limit.'';
        $bidding_master_categories = $obj->MySQLSelect($sql);
        $return_array = [];
        if ('webservice' === $use) {
            foreach ($bidding_master_categories as $key => $mServiceCategory) {
                $return_array[$key] = $this->getBiddingQuery_array($mServiceCategory, $reqArr);
            }
        } else {
            $return_array = $bidding_master_categories;
        }

        return $return_array;
    }

    public function getBiddingQuery_array($biddingArray, $reqArr = [])
    {
        global $tconfig, $APP_TYPE, $MODULES_OBJ;
        $return_array['vListLogo'] = $tconfig['tsite_upload_images_bidding'].$biddingArray['vImage'];
        $return_array['tListDescription'] = $biddingArray['tDescription'];
        $return_array['vCategory'] = $biddingArray['vTitle'];
        $return_array['iBiddingId'] = $biddingArray['iBiddingId'];
        $return_array['eCatType'] = 'Bidding';
        $return_array['eCatViewType'] = 'UBERX' === strtoupper($APP_TYPE) ? 'Icon' : 'List';
        $return_array['other'] = $biddingArray['eOther'];
        $return_array['iParentId'] = $biddingArray['iParentId'];
        $return_array['vTitle'] = $biddingArray['vTitle'];
        $return_array['vLogo_image'] = $tconfig['tsite_upload_images_bidding'].$biddingArray['vImage'];
        $return_array['vLogo'] = $tconfig['tsite_upload_images_bidding'].$biddingArray['vImage'];
        if ($MODULES_OBJ->isEnableAppHomeScreenLayoutV3() && 0 === $biddingArray['iParentId']) {
            $return_array['vListLogo'] = $tconfig['tsite_upload_images_bidding'].$biddingArray['vImage1'];
            $return_array['vLogo_image'] = $tconfig['tsite_upload_images_bidding'].$biddingArray['vImage1'];
            $return_array['vLogo'] = $tconfig['tsite_upload_images_bidding'].$biddingArray['vImage1'];
        }
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

    public function getBiddingSubCategory($use, $iParent_Id, $ssql = '', $start = 0, $per_page = 0, $vLanguage = 'EN', $ord = '', $reqArr = [])
    {
        global $obj, $MODULES_OBJ;
        $limit = '';
        $estatus = '';
        if ('admin' === $use) {
            $limit = "LIMIT {$start}, {$per_page}";
            if (0 === $start && 0 === $per_page) {
                $limit = '';
            }
            $estatus = 'AND ( estatus = "Active" || estatus = "Inactive" )';
        }
        if ('webservice' === $use) {
            $estatus = 'AND estatus = "Active"';
        }
        $iParentId = 'iParentId IN ('.$iParent_Id.')';
        $db_fields = '';
        if ($MODULES_OBJ->isEnableAppHomeScreenLayoutV3()) {
            $db_fields .= ', vImage1';
        }
        $sql = "SELECT eOther,iBiddingId,vImage,vTitle,tDescription,iParentId,eStatus,iDisplayOrder,JSON_UNQUOTE(JSON_VALUE(vTitle, '$.vTitle_".$vLanguage."')) as vTitle,JSON_UNQUOTE(JSON_VALUE(tDescription, '$.tDescription_".$vLanguage."')) as tDescription {$db_fields} FROM {$this->tablename} WHERE 1 = 1 {$estatus} {$ssql} AND ".$iParentId." {$ord} ".$limit.'';
        $bidding_master_subcategories = $obj->MySQLSelect($sql);
        $return_array = [];
        if ('webservice' === $use) {
            foreach ($bidding_master_subcategories as $key => $mServiceSubCategory) {
                $return_array[$key] = $this->getBiddingQuery_array($mServiceSubCategory, $reqArr);
            }
        } else {
            $return_array = $bidding_master_subcategories;
        }

        return $return_array;
    }

    public function createOtherSubcategory($use, $biddingPost, $vLanguage = 'EN')
    {
        global $obj;
        $db_master = $obj->MySQLSelect('SELECT * FROM `language_master` ORDER BY `iDispOrder`');
        for ($i = 0; $i < \count($db_master); ++$i) {
            if ($db_master[$i]['vCode'] === $vLanguage || 'EN' === $db_master[$i]['vCode']) {
                $vCategoryNameArr['vTitle_'.$db_master[$i]['vCode']] = $biddingPost['vServiceName'];
                $tDescriptionArr['tDescription_'.$db_master[$i]['vCode']] = $biddingPost['tDescription'];
            } else {
                $vCategoryNameArr['vTitle_'.$db_master[$i]['vCode']] = '';
                $tDescriptionArr['tDescription_'.$db_master[$i]['vCode']] = '';
            }
        }
        $jsonDescription = getJsonFromAnArr($tDescriptionArr);
        $jsonCategoryName = getJsonFromAnArr($vCategoryNameArr);
        $query_p['iParentId'] = $this->other_id;
        $query_p['vTitle'] = $jsonCategoryName;
        $query_p['eStatus'] = 'Active';
        $query_p['iDisplayOrder'] = 1;
        $query_p['vImage'] = '';

        return $obj->MySQLQueryPerform($this->tablename, $query_p, 'insert');
    }

    public function createBiddingPost($use, $biddingPost, $vLanguage = 'EN')
    {
        global $obj;

        return $obj->MySQLQueryPerform($this->biddingPostTable, $biddingPost, 'insert');
    }

    public function updateBiddingPost($use, $biddingPost, $where, $vLanguage = 'EN')
    {
        global $obj;
        $id = $obj->MySQLQueryPerform($this->biddingPostTable, $biddingPost, 'update', $where);
        if ('Accepted' === $biddingPost['eStatus']) {
            $obj->sql_query('UPDATE '.$this->bidding_request_to_driver." SET eStatus = 'Accepted' WHERE iBiddingPostId = '".$biddingPost['iBiddingPostId']."' AND iDriverId = '".$biddingPost['iDriverId']."'");
            $obj->sql_query("UPDATE bidding_offer as bo, (SELECT IOfferId FROM bidding_offer WHERE iBiddingPostId = '".$biddingPost['iBiddingPostId']."' ORDER BY IOfferId DESC LIMIT 1) as bo1 SET bo.eStatus = 'Accepted' WHERE bo.IOfferId = bo1.IOfferId");
        } elseif ('Cancelled' === $biddingPost['eStatus']) {
            $this->sendCancelNotification($biddingPost['iBiddingPostId']);
        }

        return $id;
    }

    public function biddingdriverrequest($use, $iDriverId = '', $iBiddingId = '', $sql1 = '')
    {
        global $obj;
        if ('webservice' === $use) {
            if (isset($iDriverId) && !empty($iDriverId)) {
                $sql1 .= "AND iDriverId = '".$iDriverId."'";
            }
            if (isset($iBiddingId) && !empty($iBiddingId)) {
                $sql1 .= "AND iBiddingId = '".$iBiddingId."'";
            }
        }
        $bidding = $obj->MySQLSelect("SELECT iRequestId,iBiddingId FROM {$this->bidding_driver_request} WHERE 1 = 1 {$sql1}");

        return $bidding;
    }

    public function biddingdriverrequestcount($use, $iDriverId, $iBiddingId, $sql1 = '')
    {
        global $obj;
        if ('webservice' === $use) {
            $sql1 .= "AND iDriverId = '".$iDriverId."' AND iBiddingId = '".$iBiddingId."'";
        }
        $bidding = $obj->MySQLSelect("SELECT count(iRequestId) as count FROM {$this->bidding_driver_request} WHERE 1 = 1 {$sql1}");

        return $bidding[0]['count'];
    }

    public function createbiddingdriverrequest($use, $creDataArr)
    {
        global $obj;
        $id = $obj->MySQLQueryPerform($this->bidding_driver_request, $creDataArr, 'insert');

        return $id;
    }

    public function createbiddingDriverService($use, $creDataArr)
    {
        global $obj;
        $data = $this->biddingDriverService($use, $creDataArr['iDriverId']);
        if (0 === \count($data)) {
            $id = $obj->MySQLQueryPerform($this->bidding_driver_service, $creDataArr, 'insert');

            return $id;
        }
    }

    public function biddingDriverService($use, $iDriverId = '', $iBiddingId = '', $sql1 = '')
    {
        global $obj;
        if ('webservice' === $use) {
            if (isset($iDriverId) && !empty($iDriverId)) {
                $sql1 .= "AND iDriverId = '".$iDriverId."'";
            }
        }
        $query = "SELECT iDriverId,vBiddingId FROM {$this->bidding_driver_service} WHERE 1 = 1 {$sql1}";
        $bidding = $obj->MySQLSelect($query);

        return $bidding;
    }

    public function updatebiddingDriverService($use, $data, $where)
    {
        global $obj;
        $id = $obj->MySQLQueryPerform($this->bidding_driver_service, $data, 'update', $where);

        return $id;
    }

    public function sendRequestToDriver($use, $biddingPostId = '')
    {
        global $obj, $LIST_DRIVER_LIMIT_BY_DISTANCE;
        $vLatitude = 'vWorkLocationLatitude';
        $vLongitude = 'vWorkLocationLongitude';
        $getBiddingPostData = $this->getBiddingPost($use, $biddingPostId);
        $iBiddingId = $getBiddingPostData[0]['iBiddingId'];
        $iBiddingIds = explode(',', $iBiddingId);
        $s = '';
        foreach ($iBiddingIds as $b) {
            $s .= "AND FIND_IN_SET({$b}, vBiddingId) > 0 ";
        }
        $getbiddingDriverService = $this->biddingDriverService($use, '', '', $s);
        $iDriverId = '';
        if (\count($getbiddingDriverService)) {
            $iDriverId = $this->multiToSingle($getbiddingDriverService, 'iDriverId');
            $iDriverId = implode(',', $iDriverId);
        }
        $sourceLat = $getBiddingPostData[0]['vLatitude'];
        $sourceLon = $getBiddingPostData[0]['vLongitude'];
        $sql = 'SELECT *, ROUND(( 6371 * acos( cos( radians('.$sourceLat.') ) * cos( radians( ROUND('.$vLatitude.',8) ) ) * cos( radians( ROUND('.$vLongitude.',8) ) - radians('.$sourceLon.') ) + sin( radians('.$sourceLat.') ) * sin( radians( ROUND('.$vLatitude.',8) ) ) ) ),2) AS distance,iDriverId FROM `register_driver` WHERE ('.$vLatitude." != '' AND ".$vLongitude." != '' AND iDriverId IN ({$iDriverId}) AND eStatus='active' AND eIsBlocked = 'No' AND eLogout = 'No') HAVING distance < ".$LIST_DRIVER_LIMIT_BY_DISTANCE.'';
        if ($_REQUEST['test']) {
            echo $sql;

            exit;
        }
        $Data = $obj->MySQLSelect($sql);

        return $Data;
    }

    public function getBiddingPost($use, $biddingPostid = '', $iUserId = '', $vLanguage = 'EN', $ord = '', $farray = [], $iDriverId = '', $ListBidding = 0)
    {
        global $obj, $userDeviceData, $setupInfoDataArr;
        $vTimeZone = $_REQUEST['vTimeZone'] ?? '';
        $date_ = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s')));
        $serverTimeZone = date_default_timezone_get();
        if (!empty($vTimeZone)) {
            $TimeZoneOffset = converToTz($date_, $serverTimeZone, $vTimeZone, 'P');
        }
        $sql_1 = '';
        $join = '';
        $join_1 = '';
        $order_by = 'iBiddingPostId';
        $limit = '';
        $select_parameter = '';
        if ('webservice' === $use) {
            if (!empty($biddingPostid)) {
                $sql_1 .= 'AND bp.iBiddingPostId = '.$biddingPostid.'';
            }
            if (!empty($iUserId)) {
                $sql_1 .= 'AND bp.iUserId = '.$iUserId.'';
                $join = "LEFT JOIN {$this->register_driver} as rd ON (bp.iDriverId = rd.iDriverId ) ";
                $select_parameter .= ",rd.vAvgRating, CONCAT(rd.vName ,' ', rd.vLastName ) as driverFullName , rd.vImage as driverImage";
            }
            if (isset($iDriverId) && !empty($iDriverId)) {
                $join = "JOIN {$this->bidding_request_to_driver} as brd ON (brd.iBiddingPostId = bp.iBiddingPostId ) ";
                $sql_1 .= ' AND brd.iDriverId = '.$iDriverId." AND brd.DeclineByUser != 'Driver'";
                $select_parameter .= ',brd.eStatus as eStatus';
            }
            if (1 === $userDeviceData) {
                if (isset($iDriverId) && !empty($iDriverId)) {
                    $select_parameter .= ',brd.eDeviceType,brd.iGcmRegId,brd.eDebugMode,brd.eAppTerminate,brd.eHmsDevice';
                }
            }
            $missing_Date = $isExpired = $having = '';
            $isExpired = "CASE WHEN bp.vTaskStatus ='Pending' THEN bp.dBiddingDate < ((CONVERT_TZ(NOW(), 'SYSTEM', '".$TimeZoneOffset."')) - INTERVAL 30 MINUTE) ELSE '0' END as isExpired,";
            if (isset($farray['vFilterParam']) && !empty($farray['vFilterParam'])) {
                if ('Inprocess' === $farray['vFilterParam']) {
                    $sql_1 .= " AND bp.vTaskStatus IN ('Active', 'Arrived', 'Ongoing') ";
                } elseif ('Accepted' === $farray['vFilterParam']) {
                    if (isset($iDriverId) && !empty($iDriverId)) {
                        $sql_1 .= " AND bp.eStatus = 'Accepted' AND bp.iDriverId = '{$iDriverId}'";
                        $sql_1 .= " AND bp.dBiddingDate > ((CONVERT_TZ(NOW(), 'SYSTEM', '".$TimeZoneOffset."')) - INTERVAL 30 MINUTE)";
                    } else {
                        $sql_1 .= " AND bp.eStatus = 'Accepted'";
                        $sql_1 .= " AND bp.dBiddingDate > ((CONVERT_TZ(NOW(), 'SYSTEM', '".$TimeZoneOffset."')) - INTERVAL 30 MINUTE)";
                        $isExpired = '';
                    }
                } elseif ('Closed' === $farray['vFilterParam']) {
                    $sql_1 .= " AND brd.eStatus IN ('Closed') AND bp.eStatus = 'Accepted' AND bp.iDriverId != '{$iDriverId}'";
                    $isExpired = '';
                } elseif ('Expired' === $farray['vFilterParam']) {
                    $sql_1 .= " AND bp.eStatus IN ('Pending' ,'Accepted') AND bp.dBiddingDate < ((CONVERT_TZ(NOW(), 'SYSTEM', '".$TimeZoneOffset."')) - INTERVAL 30 MINUTE)";
                    if (isset($iDriverId) && !empty($iDriverId)) {
                    }
                } elseif ('Cancelled' === $farray['vFilterParam']) {
                    if (isset($iDriverId) && !empty($iDriverId)) {
                        $sql_1 .= " AND ( brd.eStatus IN ('Decline') OR bp.eStatus = 'Cancelled' ) ";
                    } else {
                        $sql_1 .= " AND bp.eStatus = 'Cancelled'";
                    }
                    $isExpired = '';
                } elseif ('Completed' === $farray['vFilterParam']) {
                    if (isset($iDriverId) && !empty($iDriverId)) {
                        $sql_1 .= " AND bp.eStatus = 'Completed' AND bp.iDriverId = '{$iDriverId}'";
                    } else {
                        $sql_1 .= " AND bp.eStatus = 'Completed'";
                    }
                } else {
                    if (isset($iDriverId) && !empty($iDriverId)) {
                        $sql_1 .= " AND brd.eStatus IN ('Pending','Reoffer','Accepted')";
                    }
                    $sql_1 .= " AND bp.eStatus = '".$farray['vFilterParam']."'";
                    $sql_1 .= " AND bp.eStatus IN ('Pending') AND bp.dBiddingDate > CONVERT_TZ(NOW(), 'SYSTEM', '".$TimeZoneOffset."')";
                }
            }

            if (!isset($farray['missingDate']) && isset($iDriverId) && !empty($iDriverId) && isset($farray['vFromDate']) && !empty($farray['vFromDate']) && \in_array($farray['vFilterParam'], ['Inprocess', 'Expired', 'Completed', 'Cancelled'], true)) {
                $date = $farray['vFromDate'].' '.'12:01:00';
                $date = date('Y-m-d H:i:s', strtotime($date));
                $serverTimeZone = date_default_timezone_get();
                $date = converToTz($date, $serverTimeZone, $vTimeZone, 'Y-m-d');
                $sql_1 .= "AND DATE(bp.dBiddingDate) = '".$date."' ";
            }
            if (isset($farray['missingDate'], $farray['vFromDate']) && !empty($farray['vFromDate'])) {
                $dSetupDate = $setupInfoDataArr[0]['dSetupDate'];
                $yearFromDate = date('Y-m-d 00:00:00', strtotime($dSetupDate));
                $yearToDate = date('Y-m-d 23:59:59', strtotime($farray['vFromDate']));
                $missing_Date = ', DATE(bp.dBiddingDate) as StartDate';
                $sql_1 .= " AND (bp.dBiddingDate BETWEEN '{$yearFromDate}' AND '{$yearToDate}') GROUP BY DATE(bp.dBiddingDate) ";
            }
            $ord = 'ORDER BY '.$order_by.' DESC';
        }
        $sql = "SELECT bp.eStatus as eStatusMain, JSON_UNQUOTE(JSON_VALUE(vTitle, '$.vTitle_".$vLanguage."')) as vTitle, {$isExpired} bp.*,user_address.vServiceAddress, user_address.vBuildingNo, user_address.vLandmark, user_address.vAddressType, user_address.vServiceAddress, user_address.vLatitude,user_address.vLongitude {$missing_Date} {$select_parameter} FROM {$this->biddingPostTable} as bp JOIN {$this->register_user} as user ON (bp.iUserId = user.iUserId ) JOIN {$this->tablename} as bs ON (bp.iBiddingId = bs.iBiddingId ) {$join} {$join_1} LEFT JOIN {$this->user_address} as user_address ON (bp.iAddressId = user_address.iUserAddressId ) WHERE 1 = 1 {$sql_1} {$having} {$ord} {$limit}";
        if (1 === $ListBidding) {
            $sql = "SELECT bp.iBiddingPostId FROM {$this->biddingPostTable} as bp JOIN {$this->register_user} as user ON (bp.iUserId = user.iUserId ) JOIN {$this->tablename} as bs ON (bp.iBiddingId = bs.iBiddingId ) {$join} {$join_1} LEFT JOIN {$this->user_address} as user_address ON (bp.iAddressId = user_address.iUserAddressId ) WHERE 1 = 1 {$sql_1} {$ord}";
        }
        $bidding = $obj->MySQLSelect($sql);
        if (isset($farray['missingDate'])) {
            $bidding = array_column($bidding, 'StartDate');
        }

        return $bidding;
    }

    public function multiToSingle($array, $k)
    {
        $returnarr = [];
        foreach ($array as $key => $a) {
            $returnarr[$key] = $a[$k];
        }

        return $returnarr;
    }

    public function saveBiddingRequestToDriver($use, $creDataArr)
    {
        global $obj;
        $id = $obj->MySQLQueryPerform($this->bidding_request_to_driver, $creDataArr, 'insert');

        return $id;
    }

    public function getDriverBiddingRequest($use, $biddingPostId, $vCurrency, $lang, $fBiddingAmount, $iDriverId = '', $getBiddingPost = [])
    {
        global $obj, $tconfig, $LANG_OBJ, $GeneralUserType;
        $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($lang, '1', '');
        $sql1 = '';
        if ('webservice' === $use) {
            if (isset($biddingPostId) && !empty($biddingPostId)) {
                $sql1 .= "AND iBiddingPostId = '".$biddingPostId."'";
            }
            if (isset($iDriverId) && !empty($iDriverId)) {
                $sql1 .= "AND iDriverId = '".$iDriverId."'";
            }
        }
        $query = "SELECT iDriverId,eStatus,dLUpdateDate,iCancelReasonId,DeclineByUser FROM {$this->bidding_request_to_driver} WHERE 1 = 1 {$sql1}";
        $bidding_request_to_driver = $obj->MySQLSelect($query);
        $return = [];
        if (\count($bidding_request_to_driver) > 0) {
            foreach ($bidding_request_to_driver as $key => $value) {
                $currency = $obj->MySQLSelect("SELECT ratio,vName FROM `currency` WHERE vName='".$vCurrency."'");
                if ('Accepted' === $getBiddingPost[0]['eStatus']) {
                    $query_4 = "SELECT * FROM {$this->bidding_offer} WHERE 1 = 1 AND eStatus = 'Reoffer' AND iDriverId = ".$value['iDriverId'].' AND iBiddingPostId = '.$biddingPostId.' ORDER BY `IOfferId` DESC LIMIT 1';
                    $bidding_final_offer = $obj->MySQLSelect($query_4);
                    if (empty($bidding_final_offer)) {
                        $bidding_final_offer = $getBiddingPost;
                        $bidding_final_offer[0]['amount'] = $getBiddingPost[0]['fBiddingAmount'];
                    }
                }
                $query_3 = "SELECT * FROM {$this->bidding_offer} WHERE 1 = 1 AND eStatus = 'Reoffer' AND UserType = 'Passenger' AND iDriverId = ".$value['iDriverId'].' AND iBiddingPostId = '.$biddingPostId.' ORDER BY `IOfferId` DESC';
                $bidding_offer_by_user = $obj->MySQLSelect($query_3);
                $bidding_request_to_driver[$key]['iBiddingPostId'] = $biddingPostId;
                $bidding_request_to_driver[$key]['vBiddingPostNo'] = $getBiddingPost[0]['vBiddingPostNo'];
                $bidding_request_to_driver[$key]['biddingAmountTitle'] = '';
                $bidding_request_to_driver[$key]['biddingAmount'] = '';
                $bidding_request_to_driver[$key]['biddingReofferAmountTitle'] = $languageLabelsArr['LBL_AMOUNT_BY_DRIVER'];
                $bidding_request_to_driver[$key]['biddingfinalAmountTitle'] = $languageLabelsArr['LBL_BIDDING_FINAL_TASK_AMOUNT'];
                if (\count($bidding_offer_by_user) > 0) {
                    $bidding_request_to_driver[$key]['biddingAmountTitle'] = $languageLabelsArr['LBL_AMOUNT_BY_ME'];
                    $bidding_request_to_driver[$key]['biddingAmount'] = formateNumAsPerCurrency($bidding_offer_by_user[0]['amount'] * $currency[0]['ratio'], $vCurrency);
                    $bidding_request_to_driver[$key]['description_user'] = $bidding_offer_by_user[0]['description'];
                } elseif ('Driver' === $GeneralUserType) {
                    $bidding_request_to_driver[$key]['biddingAmount'] = $fBiddingAmount;
                }
                $query_1 = "SELECT vName,vLastName,vCurrencyDriver,vImage FROM {$this->register_driver} WHERE 1 = 1 AND iDriverId = ".$value['iDriverId'].' ';
                $register_driver = $obj->MySQLSelect($query_1);
                $bidding_request_to_driver[$key]['vName'] = $register_driver[0]['vName'].' '.$register_driver[0]['vLastName'];
                $bidding_request_to_driver[$key]['vAvgRating'] = $this->getAvgRating($value['iDriverId'], 'Driver');
                if (isset($register_driver[0]['vImage']) && !empty($register_driver[0]['vImage'])) {
                    $bidding_request_to_driver[$key]['vImage'] = $tconfig['tsite_upload_images_driver'].'/'.$value['iDriverId'].'/3_'.$register_driver[0]['vImage'];
                } else {
                    $bidding_request_to_driver[$key]['vImage'] = '';
                }
                $query_2 = "SELECT * FROM {$this->bidding_offer} WHERE 1 = 1 AND eStatus = 'Reoffer' AND UserType = 'Driver' AND iDriverId = ".$value['iDriverId'].' AND iBiddingPostId = '.$biddingPostId.' ORDER BY `IOfferId` DESC';
                $bidding_offer = $obj->MySQLSelect($query_2);
                $bidding_offer_ = $bidding_offer[0]['amount'];
                $bidding_offer_by_user_ = $bidding_offer_by_user[0]['amount'] * $currency[0]['ratio'];
                $bidding_final_offer_ = $bidding_final_offer[0]['amount'] * $currency[0]['ratio'];
                $bidding_request_to_driver[$key]['description_driver'] = '';
                if ($bidding_offer_ > 0) {
                    if ('Accepted' === $getBiddingPost[0]['eStatus']) {
                        $bidding_request_to_driver[$key]['biddingReofferAmount_'] = $bidding_final_offer_;
                    } else {
                        $bidding_request_to_driver[$key]['biddingAmountTitle'] = $languageLabelsArr['LBL_AMOUNT_BY_ME'];
                        if (\count($bidding_offer_by_user) > 0) {
                            $bidding_request_to_driver[$key]['biddingAmount'] = formateNumAsPerCurrency($bidding_offer_by_user[0]['amount'] * $currency[0]['ratio'], $vCurrency);
                        } else {
                            $bidding_request_to_driver[$key]['biddingAmount'] = $fBiddingAmount;
                        }
                        $bidding_request_to_driver[$key]['biddingReofferAmount_'] = formateNumAsPerCurrency($bidding_offer_ * $currency[0]['ratio'], $vCurrency);
                        $bidding_request_to_driver[$key]['description_driver'] = !empty($bidding_offer[0]['description']) ? $bidding_offer[0]['description'] : '';
                    }
                } else {
                    $bidding_request_to_driver[$key]['biddingReofferAmount_'] = formateNumAsPerCurrency($getBiddingPost[0]['fBiddingAmount'] * $currency[0]['ratio'], $vCurrency);
                }
                if ('Pending' === $value['eStatus']) {
                    $bidding_request_to_driver[$key]['showAcceptBtn'] = 'No';
                    $bidding_request_to_driver[$key]['showDeclineBtn'] = 'No';
                    $bidding_request_to_driver[$key]['showReofferBtn'] = 'No';
                    $bidding_request_to_driver[$key]['eStatusMsg'] = $languageLabelsArr['LBL_WAITING_PROVIDER_RESPONSE_MSG'];
                    $return[] = $bidding_request_to_driver[$key];
                }
                if ('Reoffer' === $value['eStatus']) {
                    if (\count($bidding_offer) > 0) {
                        $bidding_request_to_driver[$key]['eStatus'] = 'Reoffer';
                        $bidding_request_to_driver[$key]['biddingReofferAmount'] = formateNumAsPerCurrency($bidding_offer[0]['amount'] * $currency[0]['ratio'], $vCurrency);
                        $bidding_request_to_driver[$key]['showAcceptBtn'] = 'Yes';
                        $bidding_request_to_driver[$key]['showDeclineBtn'] = 'Yes';
                        $bidding_request_to_driver[$key]['showReofferBtn'] = 'Yes';
                        if ($bidding_offer_by_user_ > 0 && $bidding_offer[0]['IOfferId'] < $bidding_offer_by_user[0]['IOfferId']) {
                            $bidding_request_to_driver[$key]['showAcceptBtn'] = 'No';
                            $bidding_request_to_driver[$key]['showDeclineBtn'] = 'No';
                            $bidding_request_to_driver[$key]['showReofferBtn'] = 'No';
                            $bidding_request_to_driver[$key]['eStatusMsg'] = $languageLabelsArr['LBL_WAITING_PROVIDER_RESPONSE_MSG'];
                        }
                        $return[] = $bidding_request_to_driver[$key];
                    }
                }
                if ('Accepted' === $value['eStatus']) {
                    if (\count($bidding_offer) > 0) {
                        $bidding_request_to_driver[$key]['biddingReofferAmount'] = formateNumAsPerCurrency($bidding_offer[0]['amount'] * $currency[0]['ratio'], $vCurrency);
                    } else {
                        $bidding_request_to_driver[$key]['biddingReofferAmount'] = '';
                    }
                    if ('Accepted' === $getBiddingPost[0]['eStatus']) {
                        $bidding_request_to_driver[$key]['biddingFinalAmount'] = formateNumAsPerCurrency($bidding_final_offer[0]['amount'] * $currency[0]['ratio'], $vCurrency);
                        $bidding_request_to_driver[$key]['biddingReofferAmountTitle'] = '';
                        $bidding_request_to_driver[$key]['biddingReofferAmount_'] = '';
                        $bidding_request_to_driver[$key]['biddingReofferAmount'] = '';
                        $bidding_request_to_driver[$key]['biddingAmountTitle'] = '';
                        $bidding_request_to_driver[$key]['biddingAmount'] = '';
                        $bidding_request_to_driver[$key]['showConfirmBtn'] = 'No';
                        $bidding_request_to_driver[$key]['showDeclineBtn'] = 'No';
                        $bidding_request_to_driver[$key]['showReofferBtn'] = 'No';
                        $bidding_request_to_driver[$key]['eStatusMsg'] = $languageLabelsArr['LBL_START_BIDDING_TASK_MSG'];
                    } else {
                        $bidding_request_to_driver[$key]['showConfirmBtn'] = 'Yes';
                        $bidding_request_to_driver[$key]['showDeclineBtn'] = 'Yes';
                        $bidding_request_to_driver[$key]['showReofferBtn'] = 'No';
                    }
                    $return[] = $bidding_request_to_driver[$key];
                }
                if ('Decline' === $value['eStatus']) {
                    $cancel_reason = $obj->MySQLSelect('SELECT vTitle_'.$lang.' FROM `cancel_reason` WHERE `iCancelReasonId` = '.$value['iCancelReasonId']);
                    if (isset($cancel_reason[0]['vTitle_'.$lang]) && !empty($cancel_reason[0]['vTitle_'.$lang])) {
                        $bidding_request_to_driver[$key]['cancelReason'] = $languageLabelsArr['LBL_TASK_CANCELLED_TXT'].': '.$cancel_reason[0]['vTitle_'.$lang];
                    } else {
                        $bidding_request_to_driver[$key]['cancelReason'] = '';
                    }
                    if ('Driver' === $bidding_request_to_driver[$key]['DeclineByUser']) {
                        $bidding_request_to_driver[$key]['eStatusMsg'] = $languageLabelsArr['LBL_BIDDING_DECLINED_PROVIDER_MSG'];
                    } else {
                        $bidding_request_to_driver[$key]['eStatusMsg'] = $languageLabelsArr['LBL_BIDDING_DECLINED_USER_MSG'];
                    }
                    $bidding_request_to_driver[$key]['showConfirmBtn'] = 'No';
                    $bidding_request_to_driver[$key]['showDeclineBtn'] = 'No';
                    $return[] = $bidding_request_to_driver[$key];
                }
            }
            if (!empty($return)) {
                $dLUpdateDate = array_column($return, 'dLUpdateDate');
                array_multisort($dLUpdateDate, SORT_DESC, $return);
            }
        }

        return $return;
    }

    public function getAvgRating($iMemberId, $UserType)
    {
        global $obj;
        if ('Passenger' === $UserType) {
            $iMemberField = 'iUserId';
            $OtherUserType = 'Driver';
        } else {
            $iMemberField = 'iDriverId';
            $OtherUserType = 'Passenger';
        }
        $bidding_posts = $obj->MySQLSelect("SELECT GROUP_CONCAT(iBiddingPostId) as iBiddingPostIds FROM {$this->biddingPostTable} WHERE {$iMemberField} = '{$iMemberId}' AND eStatus = 'Completed'");
        $iBiddingPostIds = $bidding_posts[0]['iBiddingPostIds'];
        $average_rating = '0.0';
        if (!empty($iBiddingPostIds)) {
            $total_ratings_count = $obj->MySQLSelect("SELECT COUNT(iRatingId) as rating_count, SUM(fRating) as total_rating FROM bidding_service_ratings WHERE iBiddingPostId IN ({$iBiddingPostIds}) AND eUserType = '{$OtherUserType}'");
            $rating_count = $total_ratings_count[0]['rating_count'];
            $total_rating = $total_ratings_count[0]['total_rating'];
            if ($rating_count > 0) {
                $average_rating = round($total_rating / $rating_count, 1);
            }
        }

        return $average_rating;
    }

    public function UserBiddingRequest($biddingPostId, $vCurrency, $notification = '', $iDriverId = '')
    {
        global $tconfig, $obj, $LANG_OBJ, $GeneralUserType;
        $lang = $_REQUEST['vGeneralLang'] ?? '';
        if ('' === $lang || null === $lang) {
            $lang = $LANG_OBJ->FetchDefaultLangData('vCode');
        }
        $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($lang, '1', '');
        $sql1 = '';
        if (isset($biddingPostId) && !empty($biddingPostId)) {
            $sql1 .= "AND iBiddingPostId = '".$biddingPostId."'";
        }
        if (isset($iDriverId) && !empty($iDriverId)) {
            $sql1 .= "AND iDriverId = '".$iDriverId."'";
        }
        $query = "SELECT iDriverId,eStatus,dLUpdateDate,iCancelReasonId,DeclineByUser FROM {$this->bidding_request_to_driver} WHERE 1 = 1 {$sql1}";
        $bidding_request_to_driver = $obj->MySQLSelect($query);
        $getBiddingPost = $this->getBiddingPost('webservice', $biddingPostId);
        $return = [];
        if (\count($bidding_request_to_driver) > 0) {
            foreach ($bidding_request_to_driver as $key => $value) {
                $query_1 = "SELECT vName,vLastName,vCurrencyDriver,vImage FROM {$this->register_driver} WHERE 1 = 1 AND iDriverId = ".$value['iDriverId'].' ';
                $register_driver = $obj->MySQLSelect($query_1);
                $bidding_request_to_driver[$key]['vName'] = $register_driver[0]['vName'].' '.$register_driver[0]['vLastName'];
                $bidding_request_to_driver[$key]['vAvgRating'] = $this->getAvgRating($value['iDriverId'], 'Driver');
                if (isset($register_driver[0]['vImage']) && !empty($register_driver[0]['vImage'])) {
                    $bidding_request_to_driver[$key]['vImage'] = $tconfig['tsite_upload_images_driver'].'/'.$value['iDriverId'].'/3_'.$register_driver[0]['vImage'];
                } else {
                    $bidding_request_to_driver[$key]['vImage'] = '';
                }
                $bidding_request_to_driver[$key]['eJobType'] = 'Bidding';
                $currency = $obj->MySQLSelect("SELECT ratio,vName FROM `currency` WHERE vName='".$vCurrency."'");
                $bidding_request_to_driver[$key]['iBiddingPostId'] = $biddingPostId;
                $bidding_request_to_driver[$key]['vBiddingPostNo'] = $getBiddingPost[0]['vBiddingPostNo'];
                $bidding_request_to_driver[$key]['biddingAmountTitle'] = '';
                $bidding_request_to_driver[$key]['biddingAmount'] = '';
                $bidding_request_to_driver[$key]['biddingReofferAmountTitle'] = $languageLabelsArr['LBL_AMOUNT_BY_DRIVER'];
                $bidding_request_to_driver[$key]['biddingfinalAmountTitle'] = $languageLabelsArr['LBL_BIDDING_FINAL_TASK_AMOUNT'];
                if ('Accepted' === $getBiddingPost[0]['eStatus']) {
                    $query_4 = "SELECT * FROM {$this->bidding_offer} WHERE 1 = 1 AND eStatus = 'Accepted' AND iDriverId = ".$value['iDriverId'].' AND iBiddingPostId = '.$biddingPostId.' ORDER BY `IOfferId` DESC LIMIT 1';
                    $bidding_final_offer = $obj->MySQLSelect($query_4);
                    if (empty($bidding_final_offer)) {
                        $bidding_final_offer = $getBiddingPost;
                        $bidding_final_offer[0]['amount'] = $getBiddingPost[0]['fBiddingAmount'];
                    }
                }
                $query_3 = "SELECT * FROM {$this->bidding_offer} WHERE 1 = 1 AND (eStatus = 'Reoffer' || eStatus = 'Accepted')AND UserType = 'Passenger' AND iDriverId = ".$value['iDriverId'].' AND iBiddingPostId = '.$biddingPostId.' ORDER BY `IOfferId` DESC';
                $bidding_offer_by_user = $obj->MySQLSelect($query_3);
                $query_2 = "SELECT * FROM {$this->bidding_offer} WHERE 1 = 1 AND eStatus = 'Reoffer' AND UserType = 'Driver' AND iDriverId = ".$value['iDriverId'].' AND iBiddingPostId = '.$biddingPostId.' ORDER BY `IOfferId` DESC';
                $bidding_offer = $obj->MySQLSelect($query_2);
                if (\count($bidding_offer_by_user) > 0) {
                    $bidding_request_to_driver[$key]['biddingAmountTitle'] = $languageLabelsArr['LBL_AMOUNT_BY_ME'];
                    $bidding_request_to_driver[$key]['biddingAmount'] = formateNumAsPerCurrency($bidding_offer_by_user[0]['amount'] * $currency[0]['ratio'], $vCurrency);
                    $bidding_request_to_driver[$key]['description_user'] = $bidding_offer_by_user[0]['description'];
                } elseif ('Driver' === $GeneralUserType) {
                    $bidding_request_to_driver[$key]['biddingAmount'] = formateNumAsPerCurrency($getBiddingPost[0]['fBiddingAmount'] * $currency[0]['ratio'], $vCurrency);
                }
                $bidding_request_to_driver[$key]['description_driver'] = '';
                if ('Pending' === $value['eStatus']) {
                    $bidding_request_to_driver[$key]['showAcceptBtn'] = 'No';
                    $bidding_request_to_driver[$key]['showDeclineBtn'] = 'No';
                    $bidding_request_to_driver[$key]['showReofferBtn'] = 'No';
                    $bidding_request_to_driver[$key]['eStatusMsg'] = $languageLabelsArr['LBL_WAITING_PROVIDER_RESPONSE_MSG'];
                    $return[] = $bidding_request_to_driver[$key];
                }
                if ('Reoffer' === $value['eStatus']) {
                    if (\count($bidding_offer) > 0) {
                        $bidding_request_to_driver[$key]['eStatus'] = 'Reoffer';
                        $bidding_request_to_driver[$key]['biddingReofferAmount'] = formateNumAsPerCurrency($bidding_offer[0]['amount'] * $currency[0]['ratio'], $vCurrency);
                        $bidding_request_to_driver[$key]['showAcceptBtn'] = 'Yes';
                        $bidding_request_to_driver[$key]['showDeclineBtn'] = 'Yes';
                        $bidding_request_to_driver[$key]['showReofferBtn'] = 'Yes';
                        $bidding_request_to_driver[$key]['description_driver'] = !empty($bidding_offer[0]['description']) ? $bidding_offer[0]['description'] : '';
                        if ($bidding_offer[0]['IOfferId'] < $bidding_offer_by_user[0]['IOfferId']) {
                            $bidding_request_to_driver[$key]['showAcceptBtn'] = 'No';
                            $bidding_request_to_driver[$key]['showDeclineBtn'] = 'No';
                            $bidding_request_to_driver[$key]['showReofferBtn'] = 'No';
                            $bidding_request_to_driver[$key]['eStatusMsg'] = $languageLabelsArr['LBL_WAITING_PROVIDER_RESPONSE_MSG'];
                        }
                        $return[] = $bidding_request_to_driver[$key];
                    }
                }
                if ('Accepted' === $value['eStatus']) {
                    $bidding_request_to_driver[$key]['biddingConfirmAmount'] = formateNumAsPerCurrency($getBiddingPost[0]['fBiddingAmount'] * $currency[0]['ratio'], $vCurrency);
                    if (\count($bidding_offer) > 0) {
                        $bidding_request_to_driver[$key]['biddingReofferAmount'] = formateNumAsPerCurrency($bidding_offer[0]['amount'] * $currency[0]['ratio'], $vCurrency);
                        if ('Accepted' === $getBiddingPost[0]['eStatus'] && $bidding_offer[0]['IOfferId'] < $bidding_offer_by_user[0]['IOfferId']) {
                            $bidding_request_to_driver[$key]['biddingReofferAmount'] = formateNumAsPerCurrency($bidding_final_offer[0]['amount'] * $currency[0]['ratio'], $vCurrency);
                            $bidding_request_to_driver[$key]['biddingConfirmAmount'] = $bidding_request_to_driver[$key]['biddingReofferAmount'];
                        } else {
                            $bidding_request_to_driver[$key]['biddingConfirmAmount'] = $bidding_request_to_driver[$key]['biddingAmount'];
                        }
                    } else {
                        $bidding_request_to_driver[$key]['biddingReofferAmount'] = '';
                    }
                    $bidding_request_to_driver[$key]['description_driver'] = !empty($bidding_offer[0]['description']) ? $bidding_offer[0]['description'] : '';
                    if ('Accepted' === $getBiddingPost[0]['eStatus']) {
                        $bidding_request_to_driver[$key]['biddingFinalAmount'] = formateNumAsPerCurrency($bidding_final_offer[0]['amount'] * $currency[0]['ratio'], $vCurrency);
                        $bidding_request_to_driver[$key]['biddingConfirmAmount'] = '';
                        $bidding_request_to_driver[$key]['description_driver'] = '';
                        $bidding_request_to_driver[$key]['description_user'] = '';
                        $bidding_request_to_driver[$key]['biddingReofferAmountTitle'] = '';
                        $bidding_request_to_driver[$key]['biddingReofferAmount_'] = '';
                        $bidding_request_to_driver[$key]['biddingReofferAmount'] = '';
                        $bidding_request_to_driver[$key]['biddingAmountTitle'] = '';
                        $bidding_request_to_driver[$key]['biddingAmount'] = '';
                        $bidding_request_to_driver[$key]['showConfirmBtn'] = 'No';
                        $bidding_request_to_driver[$key]['showDeclineBtn'] = 'No';
                        $bidding_request_to_driver[$key]['showReofferBtn'] = 'No';
                        $bidding_request_to_driver[$key]['eStatusMsg'] = $languageLabelsArr['LBL_START_BIDDING_TASK_MSG'];
                    } else {
                        $bidding_request_to_driver[$key]['showConfirmBtn'] = 'Yes';
                        $bidding_request_to_driver[$key]['showDeclineBtn'] = 'Yes';
                        $bidding_request_to_driver[$key]['showReofferBtn'] = 'No';
                    }
                    $return[] = $bidding_request_to_driver[$key];
                }
                if ('Decline' === $value['eStatus']) {
                    if (\count($bidding_offer) > 0) {
                        $bidding_request_to_driver[$key]['biddingReofferAmount'] = formateNumAsPerCurrency($bidding_offer[0]['amount'] * $currency[0]['ratio'], $vCurrency);
                    }
                    $cancel_reason = $obj->MySQLSelect('SELECT vTitle_'.$lang.' FROM `cancel_reason` WHERE `iCancelReasonId` = '.$value['iCancelReasonId']);
                    if (isset($cancel_reason[0]['vTitle_'.$lang]) && !empty($cancel_reason[0]['vTitle_'.$lang])) {
                        $bidding_request_to_driver[$key]['cancelReason'] = $languageLabelsArr['LBL_TASK_CANCELLED_TXT'].': '.$cancel_reason[0]['vTitle_'.$lang];
                    } else {
                        $bidding_request_to_driver[$key]['cancelReason'] = '';
                    }
                    if ('Driver' === $bidding_request_to_driver[$key]['DeclineByUser']) {
                        $bidding_request_to_driver[$key]['eStatusMsg'] = $languageLabelsArr['LBL_BIDDING_DECLINED_PROVIDER_MSG'];
                    } else {
                        $bidding_request_to_driver[$key]['eStatusMsg'] = $languageLabelsArr['LBL_BIDDING_DECLINED_USER_MSG'];
                    }
                    $bidding_request_to_driver[$key]['showConfirmBtn'] = 'No';
                    $bidding_request_to_driver[$key]['showDeclineBtn'] = 'No';
                    $return[] = $bidding_request_to_driver[$key];
                }
            }
            if (!empty($return)) {
                $dLUpdateDate = array_column($return, 'dLUpdateDate');
                array_multisort($dLUpdateDate, SORT_DESC, $return);
            }
        }

        return $return;
    }

    public function DriverBiddingRequest($biddingPostId, $vCurrency, $iDriverId, $notification = '')
    {
        global $obj, $tconfig, $LANG_OBJ, $GeneralUserType;
        $lang = $_REQUEST['vGeneralLang'] ?? '';
        if ('' === $lang || null === $lang) {
            $lang = $LANG_OBJ->FetchDefaultLangData('vCode');
        }
        $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($lang, '1', '');
        $sql1 = '';
        $sql1 .= "AND iBiddingPostId = '".$biddingPostId."' AND iDriverId = '".$iDriverId."'";
        $query = "SELECT * FROM {$this->bidding_request_to_driver} WHERE 1 = 1 {$sql1}";
        $bidding_request_to_driver = $obj->MySQLSelect($query);
        $getBiddingPost = $this->getBiddingPost('webservice', $biddingPostId);
        $return = [];
        $value = $bidding_request_to_driver[0];
        $key = 0;
        $iBiddingId = explode(',', $getBiddingPost[0]['iBiddingId']);
        $_title = $getBiddingPost[0]['vTitle'];
        if (\count($iBiddingId) > 1) {
            $getbidding_ = $this->getbidding('webservice', $getBiddingPost[0]['iBiddingId'], $lang);
            $vtitle_ = $this->multiToSingle($getbidding_, 'vCategory');
            $_title = implode(',', $vtitle_);
        }
        $currency = $obj->MySQLSelect("SELECT ratio,vName FROM `currency` WHERE vName='".$vCurrency."'");
        $biddingPostIdArr[$key]['vServiceName'] = $_title;
        $biddingPostIdArr[$key]['fBiddingAmount'] = formateNumAsPerCurrency($getBiddingPost[0]['fBiddingAmount'] * $currency[0]['ratio'], $vCurrency);
        $biddingPostIdArr[$key]['tDescription'] = $getBiddingPost[0]['tDescription'];
        $biddingPostIdArr[$key]['eStatus'] = $getBiddingPost[0]['eStatus'];
        $systemTimeZone = date_default_timezone_get();
        $biddingPostIdArr[$key]['dBiddingDate'] = converToTz($getBiddingPost[0]['dBiddingDate'], $getBiddingPost[0]['vTimeZone'], $systemTimeZone);
        $userdata = $obj->MySQLSelect('SELECT vName,vLastName FROM `register_user` WHERE iUserId='.$getBiddingPost[0]['iUserId']);
        $biddingPostIdArr[$key]['Name'] = $userdata[0]['vName'].' '.$userdata[0]['vLastName'];
        $biddingPostIdArr[$key]['vServiceAddress'] = $getBiddingPost[0]['vServiceAddress'];
        $biddingPostIdArr[$key]['vBiddingPostNo'] = $getBiddingPost[0]['vBiddingPostNo'];
        $biddingPostIdArr[$key]['biddingReofferAmountTitle'] = $languageLabelsArr['LBL_BIDDING_TASK_BUDGET_TXT'];
        $biddingPostIdArr[$key]['biddingAmountTitle'] = $languageLabelsArr['LBL_AMOUNT_BY_ME'];
        $biddingPostIdArr[$key]['biddingfinalAmountTitle'] = $languageLabelsArr['LBL_BIDDING_FINAL_TASK_AMOUNT'];
        $biddingPostIdArr[$key]['iUserId'] = $getBiddingPost[0]['iUserId'];
        $biddingPostIdArr[$key]['iBiddingPostId'] = $biddingPostId;
        $buttonCheck = $this->buttonCheck('webservice', $biddingPostId, $iDriverId);
        $biddingPostIdArr[$key]['showAcceptBtn'] = 'No' === $buttonCheck['showAcceptBtn'] ? 'No' : 'Yes';
        $biddingPostIdArr[$key]['showDeclineBtn'] = 'No' === $buttonCheck['showDeclineBtn'] ? 'No' : 'Yes';
        $biddingPostIdArr[$key]['showReOfferBtn'] = 'No';
        if ('Yes' === $buttonCheck['showAcceptBtn'] && 'Yes' === $buttonCheck['showDeclineBtn']) {
            $biddingPostIdArr[$key]['showReOfferBtn'] = 'Yes';
        } else {
            $biddingPostIdArr[$key]['eStatusMsg'] = $languageLabelsArr['LBL_WAITING_USER_CONFIRM_MSG'];
        }
        if ('Accepted' === $getBiddingPost[0]['eStatus']) {
            $biddingPostIdArr[$key]['eStatusMsg'] = '';
            $biddingPostIdArr[$key]['showStartTaskBtn'] = 'Yes';
            if ($getBiddingPost[0]['iDriverId'] !== $iDriverId) {
                $biddingPostIdArr[$key]['showAcceptBtn'] = $biddingPostIdArr[$key]['showDeclineBtn'] = $biddingPostIdArr[$key]['showReOfferBtn'] = $biddingPostIdArr[$key]['showStartTaskBtn'] = 'No';
                $biddingPostIdArr[$key]['eStatusMsg'] = $languageLabelsArr['LBL_SAME_TASK_EXIST_TXT'];
            }
        } elseif ('Cancelled' === $getBiddingPost[0]['eStatus']) {
            $biddingPostIdArr[$key]['showAcceptBtn'] = 'No';
            $biddingPostIdArr[$key]['showDeclineBtn'] = 'No';
            $biddingPostIdArr[$key]['showReOfferBtn'] = 'No';
            $cancel_reason = $getBiddingPost[0]['iCancelReasonId'] > 0 ? $this->getCancelReason($getBiddingPost[0]['iCancelReasonId'], $lang) : $getBiddingPost[0]['vCancelReason'];
            $biddingPostIdArr[$key]['cancelReason'] = $languageLabelsArr['LBL_TASK_CANCELLED_TXT'].': '.$cancel_reason;
        }
        $query_3 = "SELECT * FROM {$this->bidding_offer} WHERE 1 = 1 AND (eStatus = 'Reoffer' || eStatus = 'Accepted') AND UserType = 'Passenger' AND iDriverId = ".$value['iDriverId'].' AND iBiddingPostId = '.$biddingPostId.' ORDER BY `IOfferId` DESC';
        $bidding_offer_by_user = $obj->MySQLSelect($query_3);
        $query_2 = "SELECT * FROM {$this->bidding_offer} WHERE 1 = 1 AND (eStatus = 'Reoffer'|| eStatus = 'Accepted') AND UserType = 'Driver' AND iDriverId = ".$value['iDriverId'].' AND iBiddingPostId = '.$biddingPostId.' ORDER BY `IOfferId` DESC';
        $bidding_offer = $obj->MySQLSelect($query_2);
        if ('Accepted' === $getBiddingPost[0]['eStatus'] || 'Accepted' === $value['eStatus']) {
            $query_4 = "SELECT * FROM {$this->bidding_offer} WHERE 1 = 1 AND iDriverId = ".$value['iDriverId'].' AND iBiddingPostId = '.$biddingPostId.' ORDER BY `IOfferId` DESC LIMIT 1';
            $bidding_final_offer = $obj->MySQLSelect($query_4);
            if (empty($bidding_final_offer)) {
                $bidding_final_offer = $getBiddingPost;
                $bidding_final_offer[0]['amount'] = $getBiddingPost[0]['fBiddingAmount'];
            }
            $bid_wallet_amount = $bidding_final_offer[0]['amount'];
        }
        if (\count($bidding_offer_by_user) > 0) {
            $biddingPostIdArr[$key]['biddingReofferAmountTitle'] = $languageLabelsArr['LBL_AMOUNT_BY_USER'];
            $biddingPostIdArr[$key]['biddingAmount'] = formateNumAsPerCurrency($bidding_offer_by_user[0]['amount'] * $currency[0]['ratio'], $vCurrency);
            $bidding_offer_by_user_ = $bidding_offer_by_user[0]['amount'] * $currency[0]['ratio'];
        } else {
            $biddingPostIdArr[$key]['biddingAmount'] = '';
            $bidding_offer_by_user_ = 0;
        }
        $biddingPostIdArr[$key]['biddingReofferAmount'] = formateNumAsPerCurrency($getBiddingPost[0]['fBiddingAmount'] * $currency[0]['ratio'], $vCurrency);
        $biddingPostIdArr[$key]['description_user'] = !empty($bidding_offer_by_user[0]['description']) ? $bidding_offer_by_user[0]['description'] : '';
        $biddingPostIdArr[$key]['description_driver'] = !empty($bidding_offer[0]['description']) ? $bidding_offer[0]['description'] : '';
        $biddingPostIdArr[$key]['biddingWalletMsg'] = '';
        if ('Reoffer' === $value['eStatus']) {
            if (\count($bidding_offer) > 0) {
                if ($bidding_offer[0]['amount'] > 0) {
                    $biddingPostIdArr[$key]['biddingAmount'] = formateNumAsPerCurrency($bidding_offer[0]['amount'] * $currency[0]['ratio'], $vCurrency);
                }
                if ($bidding_offer_by_user[0]['amount'] > 0) {
                    $biddingPostIdArr[$key]['biddingReofferAmount'] = formateNumAsPerCurrency($bidding_offer_by_user[0]['amount'] * $currency[0]['ratio'], $vCurrency);
                }
                if ($bidding_offer[0]['IOfferId'] < $bidding_offer_by_user[0]['IOfferId']) {
                }
            }
        }
        if ('Accepted' === $value['eStatus']) {
            $biddingPostIdArr[$key]['biddingConfirmAmount'] = formateNumAsPerCurrency($getBiddingPost[0]['fBiddingAmount'] * $currency[0]['ratio'], $vCurrency);
            $finalAmount = $getBiddingPost[0]['fBiddingAmount'];
            if (isset($bidding_offer[0]['amount']) && $bidding_offer[0]['amount'] > 0) {
                $biddingPostIdArr[$key]['biddingAmount'] = formateNumAsPerCurrency($bidding_offer[0]['amount'] * $currency[0]['ratio'], $vCurrency);
            }
            if (isset($bidding_offer_by_user[0]['amount']) && $bidding_offer_by_user[0]['amount'] > 0) {
                $biddingPostIdArr[$key]['biddingReofferAmount'] = formateNumAsPerCurrency($bidding_offer_by_user[0]['amount'] * $currency[0]['ratio'], $vCurrency);
                $biddingPostIdArr[$key]['biddingConfirmAmount'] = $biddingPostIdArr[$key]['biddingReofferAmount'];
                $finalAmount = $bidding_offer_by_user[0]['amount'];
            }
            if ('Accepted' === $getBiddingPost[0]['eStatus']) {
                $biddingPostIdArr[$key]['biddingFinalAmount'] = formateNumAsPerCurrency($bidding_final_offer[0]['amount'] * $currency[0]['ratio'], $vCurrency);
                $biddingPostIdArr[$key]['biddingConfirmAmount'] = formateNumAsPerCurrency($bidding_final_offer[0]['amount'] * $currency[0]['ratio'], $vCurrency);
                $finalAmount = $bidding_final_offer[0]['amount'];
            }
            $fCommissionAmount = ($finalAmount * $getBiddingPost[0]['fCommission']) / 100;
            $fCommissionAmount = formateNumAsPerCurrency($fCommissionAmount * $currency[0]['ratio'], $vCurrency);
            $biddingPostIdArr[$key]['biddingWalletMsg'] = str_replace('#AMOUNT#', $fCommissionAmount, $languageLabelsArr['LBL_MAINTAIN_WALLET_BALANCE_BID_MSG']);
        }
        if ('Decline' === $value['eStatus']) {
            if ('Passenger' === $value['DeclineByUser']) {
                if ($bidding_offer[0]['amount'] > 0) {
                    $biddingPostIdArr[$key]['biddingAmount'] = formateNumAsPerCurrency($bidding_offer[0]['amount'] * $currency[0]['ratio'], $vCurrency);
                }
                $biddingPostIdArr[$key]['eStatusMsg'] = $languageLabelsArr['LBL_BIDDING_USER_DECLINED_MSG'];
                $cancel_reason = $value['iCancelReasonId'] > 0 ? $this->getCancelReason($value['iCancelReasonId'], $lang) : '';
                $biddingPostIdArr[$key]['biddingWalletMsg'] = $languageLabelsArr['LBL_TASK_CANCELLED_TXT'].': '.$cancel_reason;
            }
        }
        $biddingPostIdArr[$key]['showChatIcon'] = 'No';
        if ('Reoffer' === $value['eStatus'] || 'Accepted' === $value['eStatus'] || 'Pending' === $value['eStatus']) {
            $biddingPostIdArr[$key]['showChatIcon'] = 'Yes';
        }
        if ('Closed' === $value['eStatus']) {
            $biddingPostIdArr[$key]['showAcceptBtn'] = 'No';
            $biddingPostIdArr[$key]['showDeclineBtn'] = 'No';
            $biddingPostIdArr[$key]['showReOfferBtn'] = 'No';
            $biddingPostIdArr[$key]['eStatusMsg'] = '';
            $biddingPostIdArr[$key]['showChatIcon'] = 'No';
            $biddingPostIdArr[$key]['biddingWalletMsg'] = $languageLabelsArr['LBL_SAME_TASK_EXIST_MSG'];
        }
        if ('1' === $getBiddingPost[0]['isExpired']) {
            $biddingPostIdArr[$key]['showAcceptBtn'] = 'No';
            $biddingPostIdArr[$key]['showDeclineBtn'] = 'No';
            $biddingPostIdArr[$key]['showReOfferBtn'] = 'No';
            $biddingPostIdArr[$key]['eStatusMsg'] = '';
        }
        if ('notification' === $notification) {
            if (\count($bidding_offer) > 0) {
                $biddingPostIdArr[$key]['biddingAmount'] = formateNumAsPerCurrency($bidding_offer[0]['amount'] * $currency[0]['ratio'], $vCurrency);
                if ($bidding_offer_by_user_ > 0 && $bidding_offer[0]['IOfferId'] < $bidding_offer_by_user[0]['IOfferId']) {
                    $biddingPostIdArr[$key]['biddingAmount'] = formateNumAsPerCurrency($bidding_offer_by_user[0]['amount'] * $currency[0]['ratio'], $vCurrency);
                }
            } else {
                $biddingPostIdArr[$key]['biddingAmount'] = formateNumAsPerCurrency($getBiddingPost[0]['fBiddingAmount'] * $currency[0]['ratio'], $vCurrency);
            }
        }

        return $biddingPostIdArr;
    }

    public function getbidding($use, $iBiddingId, $vLanguage = 'EN', $reqArr = [])
    {
        global $obj, $MODULES_OBJ;
        $sql = '';
        $iBiddingId_ = explode(',', $iBiddingId);
        if (\count($iBiddingId_) > 1) {
            $sql .= ' iBiddingId IN ('.$iBiddingId.')';
        } else {
            $sql .= "iBiddingId = '".$iBiddingId."'";
        }
        $db_fields = '';
        if ($MODULES_OBJ->isEnableAppHomeScreenLayoutV3()) {
            $db_fields .= ', vImage1';
        }
        $bidding = $obj->MySQLSelect("SELECT iDisplayOrder,eOther,iBiddingId,vImage,JSON_UNQUOTE(JSON_VALUE(vTitle, '$.vTitle_".$vLanguage."')) as vTitle,JSON_UNQUOTE(JSON_VALUE(tDescription, '$.tDescription_".$vLanguage."')) as tDescription,iParentId,tDescription as tDescription_json,vTitle as vTitle_json,fCommission {$db_fields} FROM {$this->tablename} WHERE 1 = 1 AND {$sql}");
        $return_array = [];
        if ('webservice' === $use) {
            foreach ($bidding as $key => $mServiceCategory) {
                $return_array[$key] = $this->getBiddingQuery_array($mServiceCategory, $reqArr);
            }
        } else {
            $return_array = $bidding;
        }
        if (\count($iBiddingId_) > 1) {
            return $return_array;
        }

        return $return_array[0];
    }

    public function buttonCheck($use, $biddingPostId, $iDriverId, $iUserId = '')
    {
        global $obj;
        $sql1 = '';
        if ('webservice' === $use) {
            if (isset($biddingPostId) && !empty($biddingPostId)) {
                $sql1 .= "AND iBiddingPostId = '".$biddingPostId."' AND iDriverId = '{$iDriverId}'";
            }
        }
        $query = "SELECT iDriverId,eStatus,dLUpdateDate,DeclineByUser FROM {$this->bidding_request_to_driver} WHERE 1 = 1 {$sql1}";
        $bidding_request_to_driver = $obj->MySQLSelect($query);
        $bidding_request_to_driver = $bidding_request_to_driver[0];
        if ('Accepted' === $bidding_request_to_driver['eStatus']) {
            $btnStatus['showAcceptBtn'] = 'No';
            $btnStatus['showDeclineBtn'] = 'No';
            $btnStatus['showDetailBtn'] = 'Yes';
        }
        if ('Reoffer' === $bidding_request_to_driver['eStatus']) {
            $sql = '';
            if (isset($iDriverId) && !empty($iDriverId)) {
                $sql .= 'AND iDriverId = '.$iDriverId.'';
            }
            if (isset($iUserId) && !empty($iUserId)) {
                $sql .= 'AND iUserId = '.$iUserId.'';
            }
            $query_2 = "SELECT UserType,iDriverId,iUserId FROM {$this->bidding_offer} WHERE 1 = 1 {$sql} AND iBiddingPostId = ".$biddingPostId.' ORDER BY `IOfferId` DESC LIMIT 1';
            $bidding_offer = $obj->MySQLSelect($query_2);
            if (\count($bidding_offer) > 0) {
                if (isset($iDriverId) && !empty($iDriverId)) {
                    if ('Driver' === $bidding_offer[0]['UserType']) {
                        $btnStatus['showAcceptBtn'] = 'No';
                        $btnStatus['showDeclineBtn'] = 'No';
                        $btnStatus['showDetailBtn'] = 'Yes';
                    } else {
                        $btnStatus['showAcceptBtn'] = 'Yes';
                        $btnStatus['showDeclineBtn'] = 'Yes';
                        $btnStatus['showDetailBtn'] = 'Yes';
                    }
                }
                if (isset($iUserId) && !empty($iUserId)) {
                    if ('Passenger' === $bidding_offer[0]['UserType']) {
                        $btnStatus['showAcceptBtn'] = 'No';
                        $btnStatus['showDeclineBtn'] = 'No';
                        $btnStatus['showDetailBtn'] = 'Yes';
                    } else {
                        $btnStatus['showAcceptBtn'] = 'Yes';
                        $btnStatus['showDeclineBtn'] = 'Yes';
                        $btnStatus['showDetailBtn'] = 'Yes';
                    }
                }
            } else {
                $btnStatus['showAcceptBtn'] = 'Yes';
                $btnStatus['showDeclineBtn'] = 'Yes';
                $btnStatus['showDetailBtn'] = 'Yes';
            }
        }
        if ('Pending' === $bidding_request_to_driver['eStatus']) {
            $btnStatus['showAcceptBtn'] = 'Yes';
            $btnStatus['showDeclineBtn'] = 'Yes';
            $btnStatus['showDetailBtn'] = 'Yes';
        }
        if ('Decline' === $bidding_request_to_driver['eStatus']) {
            if ('Passenger' === $bidding_request_to_driver['DeclineByUser']) {
                $btnStatus['showAcceptBtn'] = 'No';
                $btnStatus['showDeclineBtn'] = 'No';
                $btnStatus['showDetailBtn'] = 'Yes';
            } else {
                $btnStatus['showAcceptBtn'] = 'No';
                $btnStatus['showDeclineBtn'] = 'No';
                $btnStatus['showDetailBtn'] = 'Yes';
            }
        }

        return $btnStatus;
    }

    public function getCancelReason($iCancelReasonId, $vLang)
    {
        global $obj;
        $cancel_reason_data = $obj->MySQLSelect("SELECT vTitle_{$vLang} as vTitle FROM cancel_reason WHERE iCancelReasonId = '{$iCancelReasonId}'");

        return $cancel_reason_data[0]['vTitle'];
    }

    public function updateBiddingRequestTo($use, $data, $where)
    {
        global $obj;
        $id = $obj->MySQLQueryPerform($this->bidding_request_to_driver, $data, 'update', $where);
        if ('Accepted' === $data['eStatus']) {
            $obj->sql_query("UPDATE bidding_offer as bo, (SELECT IOfferId FROM bidding_offer WHERE iBiddingPostId = '".$data['iBiddingPostId']."' ORDER BY IOfferId DESC LIMIT 1) as bo1 SET bo.eStatus = 'Accepted' WHERE bo.IOfferId = bo1.IOfferId");
        }

        return $id;
    }

    public function create_bidding_offer($use, $creDataArr)
    {
        global $obj;
        $id = $obj->MySQLQueryPerform($this->bidding_offer, $creDataArr, 'insert');
        $obj->sql_query('UPDATE '.$this->bidding_request_to_driver." SET eStatus = 'Reoffer' WHERE iBiddingPostId = '".$creDataArr['iBiddingPostId']."' AND iDriverId = '".$creDataArr['iDriverId']."'");

        return $id;
    }

    public function getBiddingLastAmount($iDriverId, $biddingPostId)
    {
        global $obj;
        $query_4 = "SELECT * FROM {$this->bidding_offer} WHERE 1 = 1 AND iDriverId = ".$iDriverId.' AND iBiddingPostId = '.$biddingPostId.' ORDER BY `IOfferId` DESC LIMIT 1';

        return $obj->MySQLSelect($query_4);
    }

    public function updateTaskStatus($iBiddingPostId, $iDriverId, $status)
    {
        global $obj, $LANG_OBJ, $ENABLE_OTP_AFTER_BIDDING, $COMM_MEDIA_OBJ, $REFERRAL_OBJ;
        $biddingPostData = $obj->MySQLSelect("SELECT iUserId FROM bidding_post WHERE iBiddingPostId = '{$iBiddingPostId}'");
        $userData = $obj->MySQLSelect("SELECT vLang,eDeviceType,iGcmRegId,eAppTerminate,eDebugMode,eHmsDevice FROM register_user WHERE iUserId = '".$biddingPostData[0]['iUserId']."'");
        $driverData = $obj->MySQLSelect("SELECT CONCAT(vName, ' ', vLastName) as driverName FROM register_driver WHERE iDriverId = '".$iDriverId."'");
        $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($userData[0]['vLang'], '1', '');
        $Data_update = $notifiactondata = $final_message = [];
        if ('Active' === $status) {
            $this->checkWalletBalanceDriver($iBiddingPostId, $iDriverId);
            $this->checkAvailability($iDriverId);
            $this->checkStartingTime($iBiddingPostId, $iDriverId);
            $Data_update['dStartDate'] = date('Y-m-d H:i:s');
            if ('Yes' === $ENABLE_OTP_AFTER_BIDDING) {
                $Data_update['eAskCodeToUser'] = 'Yes';
                $Data_update['vRandomCode'] = $vRandomCode = generateCommonRandom();
                $passangerPhoneCode = $userData[0]['vPhoneCode'];
                $passangervPhone = $userData[0]['vPhone'];
                $DRIVER_TXT = $languageLabelsArr['LBL_PROVIDER'];
                $Data_SMS['OTP'] = $vRandomCode;
                $Data_SMS['DRIVER'] = $DRIVER_TXT;
                $sms_message_layout = $COMM_MEDIA_OBJ->GetSMSTemplate('START_TRIP_OTP', $Data_SMS, '', $userData[0]['vLang']);
                $result_sms = $COMM_MEDIA_OBJ->SendSystemSMS($passangervPhone, $passangerPhoneCode, $sms_message_layout);
                $userData[0]['bookerName'] = $userData[0]['vName'].' '.$userData[0]['vLastName'];
                $Data_Mail['OTP'] = $vRandomCode;
                $Data_Mail['FROMNAME'] = $userData[0]['bookerName'];
                $Data_Mail['vEmail'] = $userData[0]['vEmail'];
                $Data_Mail['DRIVER'] = $DRIVER_TXT;
                $sendemail = $COMM_MEDIA_OBJ->SendMailToMember('START_TRIP_OTP', $Data_Mail);
            }
            $alertMsg_db = $languageLabelsArr['LBL_PROVIDER'].' '.$driverData[0]['driverName'].' '.$languageLabelsArr['LBL_PROVIDER_ARRIVING_BIDDING_TXT'];
            $final_message['Message'] = 'BiddingTaskStarted';
            $final_message['MsgType'] = 'BiddingTaskStarted';
        } elseif ('Arrived' === $status) {
            $Data_update['dTaskArrivedDate'] = date('Y-m-d H:i:s');
            $alertMsg_db = $languageLabelsArr['LBL_PROVIDER_ARRIVED_BIDDING_TXT'];
            $final_message['Message'] = 'BiddingTaskArrived';
            $final_message['MsgType'] = 'BiddingTaskArrived';
        } elseif ('Ongoing' === $status) {
            $Data_update['dTaskStartDate'] = date('Y-m-d H:i:s');
            $alertMsg_db = $languageLabelsArr['LBL_USER_START_TASK_TXT'];
            $final_message['Message'] = 'BiddingTaskOngoing';
            $final_message['MsgType'] = 'BiddingTaskOngoing';
        } elseif ('Finished' === $status) {
            $Data_update['dTaskEndDate'] = date('Y-m-d H:i:s');
            $alertMsg_db = $languageLabelsArr['LBL_USER_START_END_TXT'];
            $final_message['Message'] = 'BiddingTaskFinished';
            $final_message['MsgType'] = 'BiddingTaskFinished';
            $Data_update['eStatus'] = 'Completed';
        }
        $Data_update['vTaskStatus'] = $status;
        $where = " iBiddingPostId = '{$iBiddingPostId}'";
        $obj->MySQLQueryPerform($this->biddingPostTable, $Data_update, 'update', $where);
        $obj->sql_query("UPDATE {$this->register_driver} SET vTaskStatus = '{$status}', iBiddingPostId = '{$iBiddingPostId}' WHERE iDriverId = '{$iDriverId}'");
        $obj->sql_query("UPDATE {$this->register_user} SET vTaskStatus = '{$status}', iBiddingPostId = '{$iBiddingPostId}' WHERE iUserId = '".$biddingPostData[0]['iUserId']."'");
        if ('Finished' === $status) {
            $REFERRAL_OBJ->CreditReferralAmountBidding($iBiddingPostId);
        }
        $final_message['iBiddingPostId'] = $iBiddingPostId;
        $final_message['time'] = time();
        $final_message['vTitle'] = $alertMsg_db;
        $final_message['eType'] = 'Bidding';
        $notifiactondata['eDeviceType'] = $userData[0]['eDeviceType'];
        $notifiactondata['iGcmRegId'] = $userData[0]['iGcmRegId'];
        $notifiactondata['eAppTerminate'] = $userData[0]['eAppTerminate'];
        $notifiactondata['eDebugMode'] = $userData[0]['eDebugMode'];
        $notifiactondata['eHmsDevice'] = $userData[0]['eHmsDevice'];
        $notifiactondata['alertMsg_db'] = $alertMsg_db;
        $notifiactondata['final_message'] = $final_message;
        $notifiactondata['channelName'] = 'PASSENGER_'.$biddingPostData[0]['iUserId'];
        $notifiactondata['NOTI_USER_TYPE'] = RN_USER;
        $this->sendNotification($notifiactondata);

        return true;
    }

    public function checkWalletBalanceDriver($iBiddingPostId, $iDriverId): void
    {
        global $obj, $tconfig, $LANG_OBJ, $MODULES_OBJ, $WALLET_OBJ;
        $row = $obj->MySQLSelect("SELECT vLang,vCurrencyDriver FROM `register_driver` WHERE iDriverId='".$iDriverId."'");
        $lang = $row[0]['vLang'];
        $vCurrency = $row[0]['vCurrencyDriver'];
        if ('' === $lang || null === $lang) {
            $lang = $LANG_OBJ->FetchDefaultLangData('vCode');
        }
        $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($lang, '1', '');
        $currency = $obj->MySQLSelect("SELECT ratio,vName FROM `currency` WHERE vName='".$vCurrency."'");
        $enableCommisionDeduct = $MODULES_OBJ->autoDeductDriverCommision('General');
        if ('Yes' === $enableCommisionDeduct) {
            $user_available_balance = $WALLET_OBJ->FetchMemberWalletBalance($iDriverId, 'Driver');
            $getbiddingFinalAmount = $this->getbiddingFinalAmount($iBiddingPostId);
            $getBiddingPost = $this->getBiddingPost('webservice', $iBiddingPostId);
            $ePaymentOption = $getBiddingPost[0]['ePaymentOption'];
            $fCommissionAmount = ($getbiddingFinalAmount * $getBiddingPost[0]['fCommission']) / 100;
            if ('cash' === strtolower($ePaymentOption)) {
                if ($fCommissionAmount > $user_available_balance) {
                    $returnArr = [];
                    $returnArr['Action'] = '0';
                    $fCommissionAmountdisplay = formateNumAsPerCurrency($fCommissionAmount * $currency[0]['ratio'], $vCurrency);
                    $walletmsg = str_replace('#AMOUNT#', $fCommissionAmountdisplay, $languageLabelsArr['LBL_MAINTAIN_WALLET_BALANCE_BID_MSG']);
                    $returnArr['message'] = $walletmsg;
                    setDataResponse($returnArr);
                }
            }
        }
    }

    public function getbiddingFinalAmount($iBiddingPostId)
    {
        global $obj;
        $query = "SELECT amount FROM {$this->bidding_offer} WHERE `eStatus` = 'Accepted' AND iBiddingPostId = ".$iBiddingPostId.' ORDER BY `IOfferId` DESC LIMIT 1';
        $bidding_final_offer = $obj->MySQLSelect($query);
        if (0 === \count($bidding_final_offer)) {
            $query = 'SELECT fBiddingAmount FROM '.$this->biddingPostTable.' WHERE iBiddingPostId = '.$iBiddingPostId.'';
            $bidding_final_offer = $obj->MySQLSelect($query);
            $bidding_final_offer[0]['amount'] = $bidding_final_offer[0]['fBiddingAmount'];
        }
        $return = $bidding_final_offer[0]['amount'];

        return $return;
    }

    public function checkAvailability($iDriverId): void
    {
        global $obj;
        $driverData = $obj->MySQLSelect("SELECT vTripStatus, vTaskStatus FROM register_driver WHERE iDriverId = '{$iDriverId}'");
        if (\in_array($driverData[0]['vTripStatus'], ['Active', 'Arrived', 'On Going Trip'], true) || \in_array($driverData[0]['vTaskStatus'], ['Active', 'Arrived', 'Ongoing', 'Finished'], true)) {
            $returnArr['Action'] = '0';
            $returnArr['message'] = 'LBL_START_BIDDING_TASK_VALIDATION_MSG';
            setDataResponse($returnArr);
        }
    }

    public function checkStartingTime($iBiddingPostId, $iDriverId): void
    {
        global $obj, $BIDDING_LATER_ACCEPT_BEFORE_INTERVAL, $CONFIG_OBJ;
        $driverData = $obj->MySQLSelect("SELECT vLang FROM register_driver WHERE iDriverId = '{$iDriverId}'");
        $vDriverLangCode = $driverData[0]['vLang'];
        if (empty($BIDDING_LATER_ACCEPT_BEFORE_INTERVAL)) {
            $CONFIG_OBJ->getConfigurations('configurations', 'BIDDING_LATER_ACCEPT_BEFORE_INTERVAL');
        }
        $additional_mins = $BIDDING_LATER_ACCEPT_BEFORE_INTERVAL;
        $additional_mins_into_secs = $additional_mins * 60;
        $getBiddingPost = $this->getBiddingPost('webservice', $iBiddingPostId);
        $dBooking_date = $getBiddingPost[0]['dBiddingDate'];
        $currDate = date('Y-m-d H:i:s');
        $datediff = abs(strtotime($dBooking_date) - strtotime($currDate));
        if ($datediff > $additional_mins_into_secs) {
            if (isset($langLabelArr['LBL_MINUTES_TXT'])) {
                $mins = $langLabelArr['LBL_MINUTES_TXT'];
            } else {
                $mins = get_value('language_label', 'vValue', 'vLabel', 'LBL_MINUTES_TXT', " and vCode='".$vDriverLangCode."'", 'true');
            }
            if (isset($langLabelArr['LBL_HOURS_TXT'])) {
                $hrs = $langLabelArr['LBL_HOURS_TXT'];
            } else {
                $hrs = get_value('language_label', 'vValue', 'vLabel', 'LBL_HOURS_TXT', " and vCode='".$vDriverLangCode."'", 'true');
            }
            if (isset($langLabelArr['LBL_RIDE_LATER_START_VALIDATION_TXT'])) {
                $LBL_RIDE_LATER_START_VALIDATION_TXT = $langLabelArr['LBL_RIDE_LATER_START_VALIDATION_TXT'];
            } else {
                $LBL_RIDE_LATER_START_VALIDATION_TXT = get_value('language_label', 'vValue', 'vLabel', 'LBL_RIDE_LATER_START_VALIDATION_TXT', " and vCode='".$vDriverLangCode."'", 'true');
            }
            if ($additional_mins <= 60) {
                $beforetext = $additional_mins.' '.$mins;
                $message = str_replace('####', $beforetext, $LBL_RIDE_LATER_START_VALIDATION_TXT);
            } else {
                $hours = floor($additional_mins / 60);
                $beforetext = $hours.' '.$hrs;
                $message = str_replace('####', $beforetext, $LBL_RIDE_LATER_START_VALIDATION_TXT);
            }
            $returnArr['Action'] = '0';
            $returnArr['message'] = $message;
            setDataResponse($returnArr);
        }
    }

    public function sendNotification($usersData): void
    {
        global $EVENT_MSG_OBJ;
        $generalDataArr[] = ['eDeviceType' => $usersData['eDeviceType'], 'deviceToken' => $usersData['iGcmRegId'], 'alertMsg' => $usersData['alertMsg_db'], 'eAppTerminate' => $usersData['eAppTerminate'], 'eDebugMode' => $usersData['eDebugMode'], 'eHmsDevice' => $usersData['eHmsDevice'], 'message' => $usersData['final_message'], 'channelName' => $usersData['channelName']];
        $EVENT_MSG_OBJ->send(['GENERAL_DATA' => $generalDataArr], $usersData['NOTI_USER_TYPE']);
    }

    public function getTaskDetails($iBiddingPostId, $vLang = '')
    {
        global $obj, $ENABLE_OTP_AFTER_BIDDING, $MODULES_OBJ;
        $bidding_post_data = $obj->MySQLSelect("SELECT bid.isSkipRating,bid.iUserId,bid.iDriverId,bid.eAskCodeToUser,bid.vRandomCode, bid.vBiddingPostNo, bid.vTaskStatus, bid.ePaid, bid.dBiddingDate, bid.iBiddingId, ru.vName, ru.vLastName, ru.iGcmRegId, ru.vPhone, ru.vPhoneCode, ru.vImgName, ua.vLatitude, ua.vLongitude, ua.vAddressType, ua.vServiceAddress, JSON_UNQUOTE(JSON_VALUE(bs.vTitle, '$.vTitle_".$vLang."')) as vTitle FROM {$this->biddingPostTable} as bid LEFT JOIN {$this->register_user} as ru ON ru.iUserId = bid.iUserId LEFT JOIN user_address as ua ON ua.iUserAddressId = bid.iAddressId LEFT JOIN {$this->tablename} as bs ON bs.iBiddingId = bid.iBiddingId WHERE bid.iBiddingPostId = '{$iBiddingPostId}'");
        $returnArr = [];
        $returnArr['vName'] = $bidding_post_data[0]['vName'];
        $returnArr['vLastName'] = $bidding_post_data[0]['vLastName'];
        $returnArr['vAvgRating'] = $this->getAvgRating($bidding_post_data[0]['iUserId'], 'Passenger');
        $returnArr['iUserId'] = $bidding_post_data[0]['iUserId'];
        $returnArr['iGcmRegId'] = $bidding_post_data[0]['iGcmRegId'];
        $returnArr['vPhone'] = $bidding_post_data[0]['vPhone'];
        $returnArr['vPhoneCode'] = $bidding_post_data[0]['vPhoneCode'];
        $returnArr['vImgName'] = $bidding_post_data[0]['vImgName'];
        $returnArr['sourceLatitude'] = $bidding_post_data[0]['vLatitude'];
        $returnArr['sourceLongitude'] = $bidding_post_data[0]['vLongitude'];
        $returnArr['tSaddress'] = !empty($bidding_post_data[0]['vAddressType']) ? $bidding_post_data[0]['vAddressType']."\n".$bidding_post_data[0]['vServiceAddress'] : $bidding_post_data[0]['vServiceAddress'];
        $returnArr['eType'] = 'Bidding';
        $returnArr['iBiddingPostId'] = $iBiddingPostId;
        $returnArr['vBiddingPostNo'] = $bidding_post_data[0]['vBiddingPostNo'];
        $returnArr['ePaymentCollect'] = $bidding_post_data[0]['ePaid'];
        $returnArr['dBiddingDate'] = $bidding_post_data[0]['dBiddingDate'];
        $returnArr['isSkipRating'] = $bidding_post_data[0]['isSkipRating'];
        if ('Yes' === $ENABLE_OTP_AFTER_BIDDING) {
            if ('Ongoing' === $bidding_post_data[0]['vTaskStatus']) {
                $returnArr['eAskCodeToUser'] = $bidding_post_data[0]['eAskCodeToUser'];
            }
            $returnArr['vText'] = (!empty($bidding_post_data[0]['vRandomCode'])) ? encodeVerificationCode($bidding_post_data[0]['vRandomCode']) : '';
        }
        $returnArr['vServiceName'] = $this->getServiceTitle('webservice', $bidding_post_data[0]['iBiddingId'], $bidding_post_data[0]['vTitle'], $vLang);
        if ('Finished' === $bidding_post_data[0]['vTaskStatus']) {
            $returnArr['Ratings_From_Driver'] = 'Not Done';
            if ($this->checkRatingDone($bidding_post_data[0]['iDriverId'], 'Driver', $iBiddingPostId)) {
                $returnArr['Ratings_From_Driver'] = 'Done';
            }
        }
        if ('Yes' === $bidding_post_data[0]['isSkipRating'] && $MODULES_OBJ->isEnableSkipRatingRide()) {
            $returnArr['Ratings_From_Driver'] = 'Done';
        }

        return $returnArr;
    }

    public function getServiceTitle($use, $iBiddingId, $vTitle, $lang)
    {
        $iBiddingId = explode(',', $iBiddingId);
        $_title = $vTitle;
        if (\count($iBiddingId) > 1) {
            $getbidding_ = $this->getbidding('webservice', $iBiddingId, $lang);
            $vtitle_ = $this->multiToSingle($getbidding_, 'vCategory');
            $_title = implode(',', $vtitle_);
        }

        return $_title;
    }

    public function getDriverTaskDetails($iBiddingPostId)
    {
        global $obj, $LANG_OBJ;
        $vTimeZone = $_REQUEST['vTimeZone'] ?? '';
        $Data = [];
        $sql = "SELECT ru.iUserId,ru.vImgName as riderImage,concat(ru.vName,' ',ru.vLastName) as riderName, ru.vPhoneCode ,ru.vPhone as riderMobile,ru.vTaskStatus as driverStatus,bid.vBiddingPostNo, bid.iDriverId, bid.vContactName, bid.vTaskStatus,bid.dAddedDate,bid.dConfirmDate,bid.dStartDate, bid.dTaskStartDate, bid.dTaskArrivedDate, bid.dTaskEndDate, ua.vAddressType,ua.vServiceAddress FROM bidding_post as bid LEFT JOIN register_user as ru ON ru.iUserId=bid.iUserId LEFT JOIN user_address as ua ON ua.iUserAddressId = bid.iAddressId WHERE bid.iBiddingPostId = '".$iBiddingPostId."'";
        $dataUser = $obj->MySQLSelect($sql);
        $dataUser[0]['tSaddress'] = !empty($dataUser[0]['vAddressType']) ? $dataUser[0]['vAddressType']."\n".$dataUser[0]['vServiceAddress'] : $dataUser[0]['vServiceAddress'];
        $dataUser[0]['riderRating'] = $this->getAvgRating($dataUser[0]['iUserId'], 'Passenger');
        $Data['driverDetails'] = $dataUser[0];
        $vLangCode = get_value('register_driver', 'vLang', 'iDriverId', $dataUser[0]['iDriverId'], '', 'true');
        if ('' === $vLangCode || null === $vLangCode) {
            $vLangCode = $LANG_OBJ->FetchDefaultLangData('vCode');
        }
        $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLangCode, '1', '');
        $lbl_at = $languageLabelsArr['LBL_AT_GENERAL'];
        $lbl_minago = $languageLabelsArr['LBL_MIN_AGO'];
        $Driver_Task_Posted = $languageLabelsArr['LBL_USER_BID_POST_TXT'];
        $Driver_Accept_Request = $languageLabelsArr['LBL_PROVIDER_ACCEPTED_TASK_SELF_TXT'];
        $Driver_Confirm_Request = $languageLabelsArr['LBL_PROVIDER_CONFIRMED_TASK_TXT'];
        $Driver_Arrived_Pick_Location = $languageLabelsArr['LBL_PROVIDER_ARRIVED_SERVICE_LOCATION_SELF_TXT'];
        $Driver_Start_job = $languageLabelsArr['LBL_PROVIDER_START_TASK_BIDDING_SELF_TXT'];
        $Driver_Finished_job = $languageLabelsArr['LBL_PROVIDER_FINISHED_TASK_SELF_TXT'];
        $testBool = 1;
        if (\count($dataUser) > 0) {
            $Data['States'] = [];
            $Data_dAddedDate = $dataUser[0]['dAddedDate'];
            $Data_dAddedDate_convert = $dataUser[0]['dAddedDate'];
            $Data_dConfirmDate = $dataUser[0]['dConfirmDate'];
            $Data_dConfirmDate_convert = $dataUser[0]['dConfirmDate'];
            $Data_dStartDate = $dataUser[0]['dStartDate'];
            $Data_dStartDate_convert = $dataUser[0]['dStartDate'];
            $Data_dTaskArrivedDate = $dataUser[0]['dTaskArrivedDate'];
            $Data_dTaskArrivedDate_convert = $dataUser[0]['dTaskArrivedDate'];
            $Data_dTaskStartDate = $dataUser[0]['dTaskStartDate'];
            $Data_dTaskStartDate_convert = $dataUser[0]['dTaskStartDate'];
            $Data_dTaskEndDate = $dataUser[0]['dTaskEndDate'];
            $Data_dTaskEndDate_convert = $dataUser[0]['dTaskEndDate'];
            if (!empty($vTimeZone)) {
                if ('' !== $Data_dAddedDate && '0000-00-00 00:00:00' !== $Data_dAddedDate) {
                    $Data_dAddedDate_convert = converToTz($Data_dAddedDate_convert, $vTimeZone, date_default_timezone_get());
                }
                if ('' !== $Data_dConfirmDate && '0000-00-00 00:00:00' !== $Data_dConfirmDate) {
                    $Data_dConfirmDate_convert = converToTz($Data_dConfirmDate_convert, $vTimeZone, date_default_timezone_get());
                }
                if ('' !== $Data_dStartDate && '0000-00-00 00:00:00' !== $Data_dStartDate) {
                    $Data_dStartDate_convert = converToTz($Data_dStartDate_convert, $vTimeZone, date_default_timezone_get());
                }
                if ('' !== $Data_dTaskArrivedDate && '0000-00-00 00:00:00' !== $Data_dTaskArrivedDate) {
                    $Data_dTaskArrivedDate_convert = converToTz($Data_dTaskArrivedDate_convert, $vTimeZone, date_default_timezone_get());
                }
                if ('' !== $Data_dTaskStartDate && '0000-00-00 00:00:00' !== $Data_dTaskStartDate) {
                    $Data_dTaskStartDate_convert = converToTz($Data_dTaskStartDate_convert, $vTimeZone, date_default_timezone_get());
                }
                if ('' !== $Data_dTaskEndDate && '0000-00-00 00:00:00' !== $Data_dTaskEndDate) {
                    $Data_dTaskEndDate_convert = converToTz($Data_dTaskEndDate_convert, $vTimeZone, date_default_timezone_get());
                }
            }
            $i = 0;
            if ('' !== $Data_dAddedDate && '0000-00-00 00:00:00' !== $Data_dAddedDate && 1 === $testBool) {
                $Data['States'][$i]['text'] = $Driver_Task_Posted;
                $Data['States'][$i]['time'] = date('h:i A', strtotime($Data_dAddedDate_convert));
                $Data['States'][$i]['dateOrig'] = $Data_dAddedDate_convert;
                $Data['States'][$i]['timediff'] = @round(abs(strtotime($Data_dAddedDate) - strtotime(date('Y-m-d H:i:s'))) / 60, 0).' '.$lbl_minago;
                $Data['States'][$i]['type'] = 'Added';
                $Data['States'][$i]['eType'] = 'Bidding';
                ++$i;
            } else {
                $testBool = 0;
            }
            if ('' !== $Data_dStartDate && '0000-00-00 00:00:00' !== $Data_dStartDate && 1 === $testBool) {
                $Data['States'][$i]['text'] = $Driver_Accept_Request;
                $Data['States'][$i]['time'] = date('h:i A', strtotime($Data_dStartDate_convert));
                $Data['States'][$i]['dateOrig'] = $Data_dStartDate_convert;
                $Data['States'][$i]['timediff'] = @round(abs(strtotime($Data_dStartDate) - strtotime(date('Y-m-d H:i:s'))) / 60, 0).' '.$lbl_minago;
                $Data['States'][$i]['type'] = 'Accept';
                $Data['States'][$i]['eType'] = 'Bidding';
                ++$i;
            } else {
                $testBool = 0;
            }
            if ('' !== $Data_dConfirmDate && '0000-00-00 00:00:00' !== $Data_dConfirmDate && 1 === $testBool) {
                $Data['States'][$i]['text'] = $Driver_Confirm_Request;
                $Data['States'][$i]['time'] = date('h:i A', strtotime($Data_dConfirmDate_convert));
                $Data['States'][$i]['dateOrig'] = $Data_dConfirmDate_convert;
                $Data['States'][$i]['timediff'] = @round(abs(strtotime($Data_dConfirmDate) - strtotime(date('Y-m-d H:i:s'))) / 60, 0).' '.$lbl_minago;
                $Data['States'][$i]['type'] = 'Confirm';
                $Data['States'][$i]['eType'] = 'Bidding';
                ++$i;
            } else {
                $testBool = 0;
            }
            if ('' !== $Data_dTaskArrivedDate && '0000-00-00 00:00:00' !== $Data_dTaskArrivedDate && 1 === $testBool) {
                $Data['States'][$i]['text'] = $Driver_Arrived_Pick_Location;
                $Data['States'][$i]['time'] = date('h:i A', strtotime($Data_dTaskArrivedDate_convert));
                $Data['States'][$i]['dateOrig'] = $Data_dTaskArrivedDate_convert;
                $Data['States'][$i]['timediff'] = @round(abs(strtotime($Data_dTaskArrivedDate_convert) - strtotime(date('Y-m-d H:i:s'))) / 60, 0).' '.$lbl_minago;
                $Data['States'][$i]['type'] = 'Arrived';
                $Data['States'][$i]['eType'] = 'Bidding';
                ++$i;
            } else {
                $testBool = 0;
            }
            if ('' !== $Data_dTaskStartDate && '0000-00-00 00:00:00' !== $Data_dTaskStartDate && 1 === $testBool) {
                $Data['States'][$i]['text'] = $Driver_Start_job;
                $Data['States'][$i]['time'] = date('h:i A', strtotime($Data_dTaskStartDate_convert));
                $Data['States'][$i]['dateOrig'] = $Data_dTaskStartDate_convert;
                $Data['States'][$i]['timediff'] = @round(abs(strtotime($Data_dTaskStartDate) - strtotime(date('Y-m-d H:i:s'))) / 60, 0).' '.$lbl_minago;
                $Data['States'][$i]['type'] = 'Ongoing';
                $Data['States'][$i]['eType'] = 'Bidding';
                ++$i;
            } else {
                $testBool = 0;
            }
            if ('' !== $Data_dTaskEndDate && '0000-00-00 00:00:00' !== $Data_dTaskEndDate && 1 === $testBool && 'Finished' === $dataUser[0]['vTaskStatus']) {
                $Data['States'][$i]['text'] = $Driver_Finished_job;
                $Data['States'][$i]['time'] = date('h:i A', strtotime($Data_dTaskEndDate_convert));
                $Data['States'][$i]['dateOrig'] = $Data_dTaskEndDate_convert;
                $Data['States'][$i]['timediff'] = @round(abs(strtotime($Data_dTaskEndDate) - strtotime(date('Y-m-d H:i:s'))) / 60, 0).' '.$lbl_minago;
                $Data['States'][$i]['type'] = 'Finished';
                $Data['States'][$i]['eType'] = 'Bidding';
                ++$i;
            }
        } else {
            $Data['States'] = [];
        }

        return $Data;
    }

    public function getUserTaskDetails($iBiddingPostId)
    {
        global $obj, $LANG_OBJ, $tconfig, $ENABLE_OTP_AFTER_BIDDING;
        $vTimeZone = $_REQUEST['vTimeZone'] ?? '';
        $sql = "SELECT bid.eAskCodeToUser,bid.vRandomCode,rd.iDriverId,rd.vImage as driverImage,concat(rd.vName,' ',rd.vLastName) as driverName, rd.vCode ,rd.vPhone as driverMobile,rd.vTaskStatus as driverStatus, rd.vImage, bid.iUserId, bid.vBiddingPostNo, bid.vContactName, bid.vTaskStatus,bid.dAddedDate,bid.dConfirmDate,bid.dStartDate, bid.dTaskStartDate, bid.dTaskArrivedDate, bid.dTaskEndDate, ua.vAddressType,ua.vServiceAddress FROM bidding_post as bid LEFT JOIN register_driver as rd ON rd.iDriverId=bid.iDriverId LEFT JOIN user_address as ua ON ua.iUserAddressId = bid.iAddressId WHERE bid.iBiddingPostId = '".$iBiddingPostId."'";
        $dataUser = $obj->MySQLSelect($sql);
        $dataUser[0]['tSaddress'] = !empty($dataUser[0]['vAddressType']) ? $dataUser[0]['vAddressType']."\n".$dataUser[0]['vServiceAddress'] : $dataUser[0]['vServiceAddress'];
        $dataUser[0]['driverImage'] = !empty($dataUser[0]['vImage']) ? $tconfig['tsite_upload_images_driver'].'/'.$dataUser[0]['iDriverId'].'/2_'.$dataUser[0]['vImage'] : '';
        $dataUser[0]['driverRating'] = $this->getAvgRating($dataUser[0]['iDriverId'], 'Driver');
        $Data['driverDetails'] = $dataUser[0];
        $vLangCode = get_value('register_user', 'vLang', 'iUserId', $dataUser[0]['iUserId'], '', 'true');
        if ('' === $vLangCode || null === $vLangCode) {
            $vLangCode = $LANG_OBJ->FetchDefaultLangData('vCode');
        }
        $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLangCode, '1', '');
        $lbl_at = $languageLabelsArr['LBL_AT_GENERAL'];
        $lbl_minago = $languageLabelsArr['LBL_MIN_AGO'];
        $Driver_Task_Posted = $languageLabelsArr['LBL_USER_BID_POST_SELF_TXT'];
        $Driver_Accept_Request = $languageLabelsArr['LBL_PROVIDER_ACCEPTED_TASK_TXT'];
        $Driver_Confirm_Request = $languageLabelsArr['LBL_PROVIDER_CONFIRMED_TASK_SELF_TXT'];
        $Driver_Arrived_Pick_Location = $languageLabelsArr['LBL_PROVIDER_ARRIVED_SERVICE_LOCATION_TXT'];
        $Driver_Start_job = $languageLabelsArr['LBL_PROVIDER_START_TASK_BIDDING_TXT'];
        $Driver_Finished_job = $languageLabelsArr['LBL_PROVIDER_FINISHED_TASK_TXT'];
        if ('Yes' === $ENABLE_OTP_AFTER_BIDDING) {
            $Driver_Start_job = $Driver_Start_job.$languageLabelsArr['LBL_YOUR_TRIP_OTP_TXT'].' : '.$dataUser[0]['vRandomCode'];
        }
        $testBool = 1;
        if (\count($dataUser) > 0) {
            $Data['States'] = [];
            $Data_dAddedDate = $dataUser[0]['dAddedDate'];
            $Data_dAddedDate_convert = $dataUser[0]['dAddedDate'];
            $Data_dConfirmDate = $dataUser[0]['dConfirmDate'];
            $Data_dConfirmDate_convert = $dataUser[0]['dConfirmDate'];
            $Data_dStartDate = $dataUser[0]['dStartDate'];
            $Data_dStartDate_convert = $dataUser[0]['dStartDate'];
            $Data_dTaskArrivedDate = $dataUser[0]['dTaskArrivedDate'];
            $Data_dTaskArrivedDate_convert = $dataUser[0]['dTaskArrivedDate'];
            $Data_dTaskStartDate = $dataUser[0]['dTaskStartDate'];
            $Data_dTaskStartDate_convert = $dataUser[0]['dTaskStartDate'];
            $Data_dTaskEndDate = $dataUser[0]['dTaskEndDate'];
            $Data_dTaskEndDate_convert = $dataUser[0]['dTaskEndDate'];
            if (!empty($vTimeZone)) {
                if ('' !== $Data_dAddedDate && '0000-00-00 00:00:00' !== $Data_dAddedDate) {
                    $Data_dAddedDate_convert = converToTz($Data_dAddedDate_convert, $vTimeZone, date_default_timezone_get());
                }
                if ('' !== $Data_dConfirmDate && '0000-00-00 00:00:00' !== $Data_dConfirmDate) {
                    $Data_dConfirmDate_convert = converToTz($Data_dConfirmDate_convert, $vTimeZone, date_default_timezone_get());
                }
                if ('' !== $Data_dStartDate && '0000-00-00 00:00:00' !== $Data_dStartDate) {
                    $Data_dStartDate_convert = converToTz($Data_dStartDate_convert, $vTimeZone, date_default_timezone_get());
                }
                if ('' !== $Data_dTaskArrivedDate && '0000-00-00 00:00:00' !== $Data_dTaskArrivedDate) {
                    $Data_dTaskArrivedDate_convert = converToTz($Data_dTaskArrivedDate_convert, $vTimeZone, date_default_timezone_get());
                }
                if ('' !== $Data_dTaskStartDate && '0000-00-00 00:00:00' !== $Data_dTaskStartDate) {
                    $Data_dTaskStartDate_convert = converToTz($Data_dTaskStartDate_convert, $vTimeZone, date_default_timezone_get());
                }
                if ('' !== $Data_dTaskEndDate && '0000-00-00 00:00:00' !== $Data_dTaskEndDate) {
                    $Data_dTaskEndDate_convert = converToTz($Data_dTaskEndDate_convert, $vTimeZone, date_default_timezone_get());
                }
            }
            $i = 0;
            if ('' !== $Data_dAddedDate && '0000-00-00 00:00:00' !== $Data_dAddedDate && 1 === $testBool) {
                $Data['States'][$i]['text'] = $Driver_Task_Posted;
                $Data['States'][$i]['time'] = date('h:i A', strtotime($Data_dAddedDate_convert));
                $Data['States'][$i]['dateOrig'] = $Data_dAddedDate_convert;
                $Data['States'][$i]['timediff'] = @round(abs(strtotime($Data_dAddedDate) - strtotime(date('Y-m-d H:i:s'))) / 60, 0).' '.$lbl_minago;
                $Data['States'][$i]['type'] = 'Added';
                $Data['States'][$i]['eType'] = 'Bidding';
                ++$i;
            } else {
                $testBool = 0;
            }
            if ('' !== $Data_dStartDate && '0000-00-00 00:00:00' !== $Data_dStartDate && 1 === $testBool) {
                $Data['States'][$i]['text'] = $Driver_Accept_Request;
                $Data['States'][$i]['time'] = date('h:i A', strtotime($Data_dStartDate_convert));
                $Data['States'][$i]['dateOrig'] = $Data_dStartDate_convert;
                $Data['States'][$i]['timediff'] = @round(abs(strtotime($Data_dStartDate) - strtotime(date('Y-m-d H:i:s'))) / 60, 0).' '.$lbl_minago;
                $Data['States'][$i]['type'] = 'Accept';
                $Data['States'][$i]['eType'] = 'Bidding';
                ++$i;
            } else {
                $testBool = 0;
            }
            if ('' !== $Data_dConfirmDate && '0000-00-00 00:00:00' !== $Data_dConfirmDate && 1 === $testBool) {
                $Data['States'][$i]['text'] = $Driver_Confirm_Request;
                $Data['States'][$i]['time'] = date('h:i A', strtotime($Data_dConfirmDate_convert));
                $Data['States'][$i]['dateOrig'] = $Data_dConfirmDate_convert;
                $Data['States'][$i]['timediff'] = @round(abs(strtotime($Data_dConfirmDate) - strtotime(date('Y-m-d H:i:s'))) / 60, 0).' '.$lbl_minago;
                $Data['States'][$i]['type'] = 'Confirm';
                $Data['States'][$i]['eType'] = 'Bidding';
                ++$i;
            } else {
                $testBool = 0;
            }
            if ('' !== $Data_dTaskArrivedDate && '0000-00-00 00:00:00' !== $Data_dTaskArrivedDate && 1 === $testBool) {
                $Data['States'][$i]['text'] = $Driver_Arrived_Pick_Location;
                $Data['States'][$i]['time'] = date('h:i A', strtotime($Data_dTaskArrivedDate_convert));
                $Data['States'][$i]['dateOrig'] = $Data_dTaskArrivedDate_convert;
                $Data['States'][$i]['timediff'] = @round(abs(strtotime($Data_dTaskArrivedDate_convert) - strtotime(date('Y-m-d H:i:s'))) / 60, 0).' '.$lbl_minago;
                $Data['States'][$i]['type'] = 'Arrived';
                $Data['States'][$i]['eType'] = 'Bidding';
                ++$i;
            } else {
                $testBool = 0;
            }
            if ('' !== $Data_dTaskStartDate && '0000-00-00 00:00:00' !== $Data_dTaskStartDate && 1 === $testBool) {
                $Data['States'][$i]['text'] = $Driver_Start_job;
                $Data['States'][$i]['time'] = date('h:i A', strtotime($Data_dTaskStartDate_convert));
                $Data['States'][$i]['dateOrig'] = $Data_dTaskStartDate_convert;
                $Data['States'][$i]['timediff'] = @round(abs(strtotime($Data_dTaskStartDate) - strtotime(date('Y-m-d H:i:s'))) / 60, 0).' '.$lbl_minago;
                $Data['States'][$i]['type'] = 'Ongoing';
                $Data['States'][$i]['eType'] = 'Bidding';
                ++$i;
            } else {
                $testBool = 0;
            }
            if ('' !== $Data_dTaskEndDate && '0000-00-00 00:00:00' !== $Data_dTaskEndDate && 1 === $testBool && 'Finished' === $dataUser[0]['vTaskStatus']) {
                $Data['States'][$i]['text'] = $Driver_Finished_job;
                $Data['States'][$i]['time'] = date('h:i A', strtotime($Data_dTaskEndDate_convert));
                $Data['States'][$i]['dateOrig'] = $Data_dTaskEndDate_convert;
                $Data['States'][$i]['timediff'] = @round(abs(strtotime($Data_dTaskEndDate) - strtotime(date('Y-m-d H:i:s'))) / 60, 0).' '.$lbl_minago;
                $Data['States'][$i]['type'] = 'Finished';
                $Data['States'][$i]['eType'] = 'Bidding';
                ++$i;
            }
        } else {
            $Data['States'] = [];
        }

        return $Data;
    }

    public function getFareDetails($iBiddingPostId, $iMemberId, $GeneralUserType, $use = '', $PAGE_MODE = 'History')
    {
        global $obj, $LANG_OBJ, $tconfig, $WALLET_OBJ;
        if ('user' === $use) {
            $vlang = $_SESSION['sess_lang'];
            $getBiddingPost = $this->getBiddingPost('webservice', $iBiddingPostId, '', $vlang);
        } else {
            $getBiddingPost = $this->getBiddingPost('webservice', $iBiddingPostId, '');
        }
        $sql = "SELECT amount FROM {$this->bidding_offer} WHERE 1 = 1 AND eStatus = 'Accepted' AND iBiddingPostId = ".$iBiddingPostId.' ORDER BY `IOfferId` DESC LIMIT 1';
        $bidding_final_offer = $obj->MySQLSelect($sql);
        if (empty($bidding_final_offer)) {
            $bidding_final_offer[0]['amount'] = $getBiddingPost[0]['fBiddingAmount'];
        }
        $total_fare = $bidding_final_offer[0]['amount'];
        $fWalletDebit = 0;
        if ('Driver' === $GeneralUserType) {
            $row = $obj->MySQLSelect("SELECT vLang, vCurrencyDriver, vEmail FROM `register_driver` WHERE iDriverId='{$iMemberId}'");
            $vCurrency = $row[0]['vCurrencyDriver'];
            $lang = $row[0]['vLang'];
            $vEmail = $row[0]['vEmail'];
        } else {
            $row = $obj->MySQLSelect("SELECT vLang, vCurrencyPassenger, vEmail FROM `register_user` WHERE iUserId='{$iMemberId}'");
            $lang = $row[0]['vLang'];
            $vCurrency = $row[0]['vCurrencyPassenger'];
            $vEmail = $row[0]['vEmail'];
        }
        if ('user' === $use) {
            $lang = $_SESSION['sess_lang'];
        }
        $user_wallet_debit_amount = 0;
        $eWalletDebitAllow = $getBiddingPost[0]['eWalletDebit'];
        $tUserWalletBalance = $getBiddingPost[0]['tUserWalletBalance'];
        if ('Yes' === $eWalletDebitAllow) {
            $user_available_balance = $WALLET_OBJ->FetchMemberWalletBalance($getBiddingPost[0]['iUserId'], 'Rider');
            if ($user_available_balance > 0) {
                $totalCurrentActiveTripsArr = FetchTotalOngoingTrips($iMemberId);
                $totalCurrentActiveTripsIdsArr = $totalCurrentActiveTripsArr['ActiveTripIds'];
                $totalCurrentActiveOrderIdsArr = $totalCurrentActiveTripsArr['ActiveOrderIds'];
                $totalCurrentActiveBidIdsArr = $totalCurrentActiveTripsArr['ActiveBidIds'];
                $totalCurrentActiveTripsCount = $totalCurrentActiveTripsArr['TotalCount'];
                if (($totalCurrentActiveTripsCount > 1 || false === \in_array($iBiddingPostId, $totalCurrentActiveBidIdsArr, true)) && 'Wallet' === $ePaymentOption && $tUserWalletBalance > 0) {
                    $user_available_balance = $tUserWalletBalance;
                }
                $wallet_fare = $total_fare;
                $total_fare = 0;
                if ($wallet_fare > $user_available_balance) {
                    $user_wallet_debit_amount = $user_available_balance;
                } else {
                    $user_wallet_debit_amount = $wallet_fare;
                    $total_fare = 0;
                }
            }
            $fWalletDebit = $user_wallet_debit_amount;
        }
        $fWalletDebit = $getBiddingPost[0]['fWalletDebit'] > 0 ? $getBiddingPost[0]['fWalletDebit'] : $fWalletDebit;
        $currency = $obj->MySQLSelect("SELECT ratio,vName FROM `currency` WHERE vName='".$vCurrency."'");
        $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($lang, '1', '');
        $vTitle = $this->getServiceTitle('webservice', $getBiddingPost[0]['iBiddingId'], $getBiddingPost[0]['vTitle'], $lang);
        $fOutStandingAmount = $getBiddingPost[0]['fOutStandingAmount'];
        $fCommission = round(($bidding_final_offer[0]['amount'] * $getBiddingPost[0]['fCommission']) / 100, 2);
        $bidding_final_offer_amount = round($bidding_final_offer[0]['amount'], 2);
        if ('Driver' === $GeneralUserType) {
            if ('History' === $PAGE_MODE) {
                $Subtotal = $bidding_final_offer_amount - $fCommission + $fOutStandingAmount;
            } else {
                $Subtotal = $bidding_final_offer_amount - $fWalletDebit + $fOutStandingAmount;
            }
        } else {
            $Subtotal = $bidding_final_offer_amount - $fWalletDebit + $fOutStandingAmount;
        }
        $return_arr = [];
        $return_arr['FareDetailsNewArr'][][$languageLabelsArr['LBL_BIDDING_FINAL_AMOUNT']] = formateNumAsPerCurrency($bidding_final_offer[0]['amount'] * $currency[0]['ratio'], $vCurrency);
        if ($fOutStandingAmount > 0) {
            $return_arr['FareDetailsNewArr'][][$languageLabelsArr['LBL_OUTSTANDING_AMOUNT_TXT']] = formateNumAsPerCurrency($fOutStandingAmount * $currency[0]['ratio'], $vCurrency);
        }
        if ('Driver' === $GeneralUserType) {
            if ($fWalletDebit > 0 && 'Display' === $PAGE_MODE) {
                $return_arr['FareDetailsNewArr'][][$languageLabelsArr['LBL_WALLET_ADJUSTMENT']] = '-'.formateNumAsPerCurrency($fWalletDebit * $currency[0]['ratio'], $vCurrency);
            }
        } else {
            if ($fWalletDebit > 0) {
                $return_arr['FareDetailsNewArr'][][$languageLabelsArr['LBL_WALLET_ADJUSTMENT']] = '-'.formateNumAsPerCurrency($fWalletDebit * $currency[0]['ratio'], $vCurrency);
            }
        }
        if ($fCommission > 0 && 'Driver' === $GeneralUserType && 'History' === $PAGE_MODE) {
            $return_arr['FareDetailsNewArr'][][$languageLabelsArr['LBL_Commission']] = '-'.formateNumAsPerCurrency($fCommission * $currency[0]['ratio'], $vCurrency);
        }
        $return_arr['FareDetailsNewArr'][]['eDisplaySeperator'] = 'Yes';
        if ('Driver' === $GeneralUserType) {
            $return_arr['FareDetailsNewArr'][][$languageLabelsArr['LBL_EARNED_AMOUNT']] = formateNumAsPerCurrency($Subtotal * $currency[0]['ratio'], $vCurrency);
        } else {
            $return_arr['FareDetailsNewArr'][]['Subtotal'] = formateNumAsPerCurrency($Subtotal * $currency[0]['ratio'], $vCurrency);
        }
        $return_arr['FareSubTotal'] = formateNumAsPerCurrency($Subtotal * $currency[0]['ratio'], $vCurrency);
        $return_arr['vBiddingPostNo'] = $getBiddingPost[0]['vBiddingPostNo'];
        if ('0000-00-00 00:00:00' === $getBiddingPost[0]['dStartDate']) {
            $return_arr['dBiddingDate'] = $getBiddingPost[0]['dBiddingDate'];
        } else {
            $return_arr['dBiddingDate'] = $getBiddingPost[0]['dStartDate'];
        }
        $return_arr['tSaddress'] = $getBiddingPost[0]['vServiceAddress'];
        $return_arr['vServiceDetailTitle'] = $vTitle ?? '';
        $return_arr['vBiddingPaymentMode'] = $getBiddingPost[0]['ePaymentOption'];
        $return_arr['vTaskStatus'] = $getBiddingPost[0]['vTaskStatus'];
        $return_arr['eStatus'] = $getBiddingPost[0]['eStatus'];
        $return_arr['ePayWallet'] = 'Wallet' === $getBiddingPost[0]['ePaymentOption'] ? 'Yes' : 'No';
        $return_arr['fWalletAmountAdjusted'] = formateNumAsPerCurrency($fWalletDebit * $currency[0]['ratio'], $vCurrency);
        $return_arr['userlang'] = $lang;
        $return_arr['usercurrency'] = $vCurrency;
        $return_arr['userEmail'] = $vEmail;
        $return_arr['eWalletAmtAdjusted'] = 'No';
        if ('Yes' === $eWalletDebitAllow) {
            $return_arr['eWalletAmtAdjusted'] = 'Yes';
        }
        if ($Subtotal > 0 && 'Yes' === $return_arr['ePayWallet']) {
            $return_arr['ePayWallet'] = 'No';
        }
        if ('Cancelled' === $getBiddingPost[0]['eStatus']) {
            $return_arr['vCancelReason'] = $getBiddingPost[0]['iCancelReasonId'] > 0 ? $this->getCancelReason($getBiddingPost[0]['iCancelReasonId'], $lang) : $getBiddingPost[0]['vCancelReason'];
            $return_arr['eCancelledBy'] = 'User' === $getBiddingPost[0]['eCancelBy'] ? $languageLabelsArr['LBL_RIDER'] : $languageLabelsArr['LBL_PROVIDER'];
        }
        if ('Passenger' === $GeneralUserType) {
            $userData = $obj->MySQLSelect("SELECT CONCAT(vName, ' ', vLastName) as driverName, vImage FROM register_driver WHERE iDriverId = '".$getBiddingPost[0]['iDriverId']."' ");
            if (!empty($userData)) {
                $return_arr['driverAvgRating'] = $this->getAvgRating($getBiddingPost[0]['iDriverId'], 'Driver');
                $return_arr['driverName'] = $userData[0]['driverName'];
                $return_arr['driverImage'] = $tconfig['tsite_upload_images_driver'].'/'.$getBiddingPost[0]['iDriverId'].'/2_'.$userData[0]['vImage'];
                $return_arr['is_rating'] = ($this->checkRatingDone($iMemberId, 'Passenger', $iBiddingPostId)) ? 'Yes' : 'No';
            }
            $TripRatingData = $this->getBiddingRating('Passenger', $iBiddingPostId);
        } else {
            $userData = $obj->MySQLSelect("SELECT CONCAT(vName, ' ', vLastName) as userName, vImgName FROM register_user WHERE iUserId = '".$getBiddingPost[0]['iUserId']."' ");
            $return_arr['userAvgRating'] = $this->getAvgRating($getBiddingPost[0]['iUserId'], 'Passenger');
            $return_arr['userName'] = $userData[0]['userName'];
            $return_arr['userImage'] = $tconfig['tsite_upload_images_passenger'].'/'.$getBiddingPost[0]['iUserId'].'/'.$userData[0]['vImgName'];
            $return_arr['is_rating'] = ($this->checkRatingDone($iMemberId, 'Driver', $iBiddingPostId)) ? 'Yes' : 'No';
            $TripRatingData = $this->getBiddingRating('Driver', $iBiddingPostId);
        }
        $return_arr['TripRating'] = 0;
        if (!empty($TripRatingData) && \count($TripRatingData) > 0) {
            $return_arr['TripRating'] = $TripRatingData[0]['fRating'];
        }
        $return_arr['iBiddingPostId'] = $iBiddingPostId;
        $returnArr['Action'] = '1';
        $returnArr['message'] = $return_arr;
        if ('mail' === $use || 'user' === $use) {
            return $return_arr;
        }
        setDataResponse($returnArr);
    }

    public function getBiddingRating($UserType, $iBiddingPostId)
    {
        global $obj;
        $check_rating = $obj->MySQLSelect("SELECT fRating FROM bidding_service_ratings WHERE eFromUserType = '{$UserType}' AND iBiddingPostId = '{$iBiddingPostId}'");

        return $check_rating;
    }

    public function collectPayment($iBiddingPostId, $iDriverId): void
    {
        global $obj, $WALLET_OBJ, $APP_PAYMENT_METHOD, $EVENT_MSG_OBJ, $LANG_OBJ, $EXTRA_MONEY_CASH_OR_OUTSTANDING, $MODULES_OBJ;
        $bidding_post_data = $obj->MySQLSelect("SELECT iUserId, ePaymentOption, eWalletDebit, vBiddingPostNo, iPaymentInfoId, tUserWalletBalance, fOutStandingAmount,fCommission FROM bidding_post WHERE iBiddingPostId = '{$iBiddingPostId}'");
        $ePaymentOption = $bidding_post_data[0]['ePaymentOption'];
        $eWalletDebitAllow = $bidding_post_data[0]['eWalletDebit'];
        $iUserId = $bidding_post_data[0]['iUserId'];
        $vBiddingPostNo = $bidding_post_data[0]['vBiddingPostNo'];
        $fCommission = $bidding_post_data[0]['fCommission'];
        $tUserWalletBalance = $bidding_post_data[0]['tUserWalletBalance'];
        $fOutStandingAmount = $bidding_post_data[0]['fOutStandingAmount'];
        $isCollectCash = $_REQUEST['isCollectCash'] ?? '';
        $getbiddingFinalAmount = $this->getbiddingFinalAmount($iBiddingPostId);
        $total_fare = $getbiddingFinalAmount + $fOutStandingAmount;
        $userData = $obj->MySQLSelect("SELECT vCountry, eDeviceType, iGcmRegId, eAppTerminate, eDebugMode, vLang, eHmsDevice FROM register_user WHERE iUserId='{$iUserId}'");
        $vLangCode = $userData[0]['vLang'];
        $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLangCode, '1', '');
        $countryPaymentMethod = $obj->MySQLSelect("SELECT vPaymentGateway FROM country WHERE vCountryCode = '".$userData[0]['vCountry']."'");
        $USER_APP_PAYMENT_METHOD = $APP_PAYMENT_METHOD;
        if (!empty($countryPaymentMethod[0]['vPaymentGateway'])) {
            $USER_APP_PAYMENT_METHOD = $countryPaymentMethod[0]['vPaymentGateway'];
        }
        $TOKENIZED_STATUS = strtoupper($USER_APP_PAYMENT_METHOD).'_TOKENIZED';
        global ${$TOKENIZED_STATUS};
        $IS_TOKENIZED = ${$TOKENIZED_STATUS};
        $user_wallet_debit_amount = 0;
        if ('Yes' === $eWalletDebitAllow) {
            $user_available_balance = $WALLET_OBJ->FetchMemberWalletBalance($iUserId, 'Rider');
            if ($user_available_balance > 0) {
                $totalCurrentActiveTripsArr = FetchTotalOngoingTrips($iUserId);
                $totalCurrentActiveTripsIdsArr = $totalCurrentActiveTripsArr['ActiveTripIds'];
                $totalCurrentActiveOrderIdsArr = $totalCurrentActiveTripsArr['ActiveOrderIds'];
                $totalCurrentActiveBidIdsArr = $totalCurrentActiveTripsArr['ActiveBidIds'];
                $totalCurrentActiveTripsCount = $totalCurrentActiveTripsArr['TotalCount'];
                if (($totalCurrentActiveTripsCount > 1 || false === \in_array($iBiddingPostId, $totalCurrentActiveBidIdsArr, true)) && 'Wallet' === $ePaymentOption && $tUserWalletBalance > 0) {
                    $user_available_balance = $tUserWalletBalance;
                }
                $wallet_fare = $total_fare;
                $total_fare = 0;
                if ($wallet_fare > $user_available_balance) {
                    $user_wallet_debit_amount = $user_available_balance;
                    $total_fare = $wallet_fare - $user_wallet_debit_amount;
                } else {
                    $user_wallet_debit_amount = $wallet_fare;
                    $total_fare = 0;
                }
            }
            if ($user_wallet_debit_amount > 0) {
                $data_wallet['iUserId'] = $iUserId;
                $data_wallet['eUserType'] = 'Rider';
                $data_wallet['iBalance'] = $user_wallet_debit_amount;
                $data_wallet['eType'] = 'Debit';
                $data_wallet['dDate'] = date('Y-m-d H:i:s');
                $data_wallet['eFor'] = 'Booking';
                $data_wallet['ePaymentStatus'] = 'Settelled';
                $data_wallet['tDescription'] = '#LBL_DEBITED_BOOKING_BIDDING# '.$vBiddingPostNo;
                $getPaymentStatus = $obj->MySQLSelect("SELECT iBiddingPostId,eUserType,ePaymentStatus,iUserWalletId,eType,eFor FROM user_wallet WHERE iBiddingPostId='".$iBiddingPostId."'");
                $walletArr = [];
                for ($h = 0; $h < \count($getPaymentStatus); ++$h) {
                    $walletArr[$getPaymentStatus[$h]['eType']][$getPaymentStatus[$h]['eUserType']][$getPaymentStatus[$h]['iBiddingPostId']][$getPaymentStatus[$h]['eFor']] = $getPaymentStatus[$h]['eType'];
                }
                if (!isset($walletArr[$data_wallet['eType']][$data_wallet['eUserType']][$iBiddingPostId][$data_wallet['eFor']])) {
                    $wallet_id = $WALLET_OBJ->PerformWalletTransaction($data_wallet['iUserId'], $data_wallet['eUserType'], $data_wallet['iBalance'], $data_wallet['eType'], 0, $data_wallet['eFor'], $data_wallet['tDescription'], $data_wallet['ePaymentStatus'], $data_wallet['dDate']);
                    $obj->sql_query("UPDATE user_wallet SET iTripId = 0, iBiddingPostId = '".$iBiddingPostId."' WHERE iUserWalletId = '{$wallet_id}'");
                }
                $data_update_task['fWalletDebit'] = $user_wallet_debit_amount;
                $where = " iBiddingPostId = '{$iBiddingPostId}'";
                $obj->MySQLQueryPerform('bidding_post', $data_update_task, 'update', $where);
            }
        }
        if ('Card' === $ePaymentOption && '' === $isCollectCash) {
            if ($total_fare > 0) {
                $AMOUNT = $total_fare;
                $iMemberId = $iUserId;
                $UserType = 'Passenger';
                $iPaymentInfoId = $bidding_post_data[0]['iPaymentInfoId'];
                $paymentData = ['amount' => $AMOUNT, 'description' => $description, 'iMemberId' => $iMemberId, 'UserType' => $UserType, 'iPaymentInfoId' => $iPaymentInfoId];
                if ('NO' === strtoupper($IS_TOKENIZED)) {
                    $paymentData['return_url'] = $tconfig['tsite_url'];
                    $paymentData['eType'] = $eType;
                }
                $result = PaymentGateways::getInstance()->execute($paymentData);
                if ('1' === $result['Action']) {
                    if (isset($result['AUTHENTICATION_REQUIRED'])) {
                        $fOutStandingAmount = $total_fare;
                        $tDescription = 'Amount charge for bid oustanding balance';
                        $extraParams = 'eType='.$eType.'&ePaymentType=ChargeOutstandingAmount&tSessionId='.$userData[0]['tSessionId'].'&GeneralMemberId='.$iUserId.'&GeneralUserType=Passenger&iServiceId=&AMOUNT='.$fOutStandingAmount.'&PAGE_TYPE=CHARGE_OUTSTANDING_AMT&SYSTEM_TYPE=APP&IS_RETURN_RESULT=Yes&description='.urlencode($tDescription);
                        $OUTSTANDING_PAYMENT_URL = $tconfig['tsite_url'].'assets/libraries/webview/payment_mode_select.php?'.$extraParams;
                        $alertMsg = str_replace('#TASK_NO#', $vBiddingPostNo, $languageLabelsArr['LBL_BIDDING_OUTSTANDING_PAYMENT_PENDING_MSG']);
                        $message_arr = [];
                        $message_arr['Message'] = 'OutstandingGenerated';
                        $message_arr['iBiddingPostId'] = $iBiddingPostId;
                        $message_arr['iUserId'] = $iUserId;
                        $message_arr['vBiddingPostNo'] = $vBiddingPostNo;
                        $message_arr['vTitle'] = $alertMsg;
                        $message_arr['tSessionId'] = $userData[0]['tSessionId'];
                        $message_arr['PAYMENT_URL'] = $OUTSTANDING_PAYMENT_URL;
                        $message_arr['vMsgCode'] = (string) time();
                        $channelName = 'PASSENGER_'.$iUserId;
                        $generalDataArr[] = ['eDeviceType' => $userData[0]['eDeviceType'], 'deviceToken' => $userData[0]['iGcmRegId'], 'alertMsg' => $alertMsg, 'eAppTerminate' => $userData[0]['eAppTerminate'], 'eDebugMode' => $userData[0]['eDebugMode'], 'eHmsDevice' => $userData[0]['eHmsDevice'], 'message' => $message_arr, 'channelName' => $channelName];
                        $EVENT_MSG_OBJ->send(['GENERAL_DATA' => $generalDataArr], RN_USER);
                    } else {
                        $payment_id = $result['payment_id'];
                        $where_payments = " iPaymentId = '".$payment_id."'";
                        $data_payments['iBiddingPostId'] = $iBiddingPostId;
                        $data_payments['eEvent'] = 'Bidding';
                        $obj->MySQLQueryPerform('payments', $data_payments, 'update', $where_payments);
                    }
                } else {
                    $returnArr['message1'] = 'LBL_COLLECT_CASH';
                    if ('OUTSTANDING' === strtoupper($EXTRA_MONEY_CASH_OR_OUTSTANDING)) {
                        $returnArr['message1'] = 'LBL_PAY_LATER_TXT';
                    }
                    $returnArr = array_merge($returnArr, $result);
                    $returnArr['Action'] = '0';
                    $returnArr['message'] = $languageLabelsArr['LBL_CHARGE_COLLECT_FAILED'];
                    if (isset($result['message'])) {
                        $returnArr['message'] = $result['message'];
                    }
                    setDataResponse($returnArr);
                }
            }
        } elseif (('Card' === $ePaymentOption || 'Wallet' === $ePaymentOption) && 'true' === $isCollectCash) {
            if ('CASH' === strtoupper($EXTRA_MONEY_CASH_OR_OUTSTANDING)) {
                $data_update_task['ePaymentOption'] = 'Cash';
            } else {
                $fOutStandingAmount = $total_fare;
            }
        } elseif ($total_fare > 0 && 'Wallet' === $ePaymentOption && '' === $isCollectCash) {
            $returnArr['Action'] = '0';
            if ('OUTSTANDING' === strtoupper($EXTRA_MONEY_CASH_OR_OUTSTANDING)) {
                $returnArr['message1'] = 'LBL_PAY_LATER_TXT';
            }
            $returnArr['message'] = $languageLabelsArr['LBL_LOW_WALLET_BALANCE'];
            setDataResponse($returnArr);
        }
        if ('Cash' === $ePaymentOption && $fOutStandingAmount > 0) {
            $obj->sql_query("UPDATE trip_outstanding_amount SET ePaidByPassenger = 'Yes',iBiddingPostId='".$iBiddingPostId."', vBidAdjusmentId = '".$vBiddingPostNo."' WHERE iUserId = '".$iUserId."' AND ePaymentBy = 'Passenger'");
        }
        $enableCommisionDeduct = $MODULES_OBJ->autoDeductDriverCommision('General');
        if ('Yes' === $enableCommisionDeduct) {
            if ('Cash' === $ePaymentOption && '' === $isCollectCash) {
                $fCommissiondebit = round(($total_fare * $fCommission) / 100, 2);
                $iBalance = $fCommissiondebit + $fOutStandingAmount;
                $data_wallet['iUserId'] = $iDriverId;
                $data_wallet['eUserType'] = 'Driver';
                $data_wallet['iBalance'] = $iBalance;
                $data_wallet['eType'] = 'Debit';
                $data_wallet['dDate'] = date('Y-m-d H:i:s');
                $data_wallet['eFor'] = 'Withdrawl';
                $data_wallet['ePaymentStatus'] = 'Settelled';
                $data_wallet['tDescription'] = '#LBL_DEBITED_SITE_EARNING_BOOKING# '.$vBiddingPostNo;
                $getPaymentStatus = $obj->MySQLSelect("SELECT iBiddingPostId,eUserType,ePaymentStatus,iUserWalletId,eType,eFor FROM user_wallet WHERE iBiddingPostId='".$iBiddingPostId."'");
                $walletArr = [];
                for ($h = 0; $h < \count($getPaymentStatus); ++$h) {
                    $walletArr[$getPaymentStatus[$h]['eType']][$getPaymentStatus[$h]['eUserType']][$getPaymentStatus[$h]['iBiddingPostId']][$getPaymentStatus[$h]['eFor']] = $getPaymentStatus[$h]['eType'];
                }
                if ($MODULES_OBJ->isAutoCreditToDriverModuleAvailable()) {
                    $Where = " iBiddingPostId = '".$iBiddingPostId."'";
                    $Data_update_driver_paymentstatus = [];
                    $Data_update_driver_paymentstatus['eDriverPaymentStatus'] = 'Settelled';
                    $Update_Payment_Id = $obj->MySQLQueryPerform('bidding_post', $Data_update_driver_paymentstatus, 'update', $Where);
                    if (!isset($walletArr[$data_wallet['eType']][$data_wallet['eUserType']][$iBiddingPostId][$data_wallet['eFor']])) {
                        $wallet_id = $WALLET_OBJ->PerformWalletTransaction($data_wallet['iUserId'], $data_wallet['eUserType'], $data_wallet['iBalance'], $data_wallet['eType'], 0, $data_wallet['eFor'], $data_wallet['tDescription'], $data_wallet['ePaymentStatus'], $data_wallet['dDate']);
                        $obj->sql_query("UPDATE user_wallet SET iTripId = 0, iBiddingPostId = '".$iBiddingPostId."' WHERE iUserWalletId = '{$wallet_id}'");
                    }
                } else {
                    $Where = " iBiddingPostId = '".$iBiddingPostId."'";
                    $Data_update_driver_paymentstatus = [];
                    if ($total_fare >= $iBalance) {
                        $Data_update_driver_paymentstatus['eDriverPaymentStatus'] = 'Settelled';
                        $Update_Payment_Id = $obj->MySQLQueryPerform('bidding_post', $Data_update_driver_paymentstatus, 'update', $Where);
                        if (!isset($walletArr[$data_wallet['eType']][$data_wallet['eUserType']][$iBiddingPostId][$data_wallet['eFor']])) {
                            $wallet_id = $WALLET_OBJ->PerformWalletTransaction($data_wallet['iUserId'], $data_wallet['eUserType'], $data_wallet['iBalance'], $data_wallet['eType'], 0, $data_wallet['eFor'], $data_wallet['tDescription'], $data_wallet['ePaymentStatus'], $data_wallet['dDate']);
                            $obj->sql_query("UPDATE user_wallet SET iTripId = 0, iBiddingPostId = '".$iBiddingPostId."' WHERE iUserWalletId = '{$wallet_id}'");
                        }
                    }
                }
            }
        }
        if ($MODULES_OBJ->isAutoCreditToDriverModuleAvailable()) {
            if ($MODULES_OBJ->isAutoCreditToDriverModuleAvailable()) {
                include_once 'include/features/include_auto_credit_driver.php';
            }
            $Data = [];
            $Data['ePaymentStatus'] = 'Settelled';
            $Data['isCollectCash'] = $isCollectCash;
            $Data['iUserId'] = $iUserId;
            $Data['iBiddingPostId'] = $iBiddingPostId;
            $Data['vBiddingPostNo'] = $vBiddingPostNo;
            autoCreditDriverEarningBidding($Data, 'CollectPayment');
        }
        $data_update_task['ePaid'] = 'Yes';
        $where = " iBiddingPostId = '{$iBiddingPostId}'";
        $obj->MySQLQueryPerform('bidding_post', $data_update_task, 'update', $where);
        $returnArr['Action'] = '1';
        $returnArr['USER_DATA'] = getDriverDetailInfo($iDriverId);
        setDataResponse($returnArr);
    }

    public function submitRating($iBiddingPostId, $iMemberId, $UserType): void
    {
        global $obj, $MODULES_OBJ;
        $rating = $_REQUEST['rating'] ?? '';
        $message = $_REQUEST['message'] ?? '';
        $isSkipRating = $_REQUEST['isSkipRating'] ?? 'No';
        $bidding_post_data = $obj->MySQLSelect("SELECT iUserId, iDriverId FROM bidding_post WHERE iBiddingPostId = '{$iBiddingPostId}'");
        if ('Passenger' === $UserType) {
            $tableName = 'register_user';
            $where = "iUserId='".$bidding_post_data[0]['iUserId']."'";
            $eFromUserType = $UserType;
            $eToUserType = 'Driver';
            $avgRatingiMemberId = $bidding_post_data[0]['iDriverId'];
            $tableNameMember = 'register_driver';
            $where_member = " iDriverId = '{$avgRatingiMemberId}'";
        } else {
            $tableName = 'register_driver';
            $where = "iDriverId='".$bidding_post_data[0]['iDriverId']."'";
            $eFromUserType = $UserType;
            $eToUserType = 'Passenger';
            $avgRatingiMemberId = $bidding_post_data[0]['iUserId'];
            $tableNameMember = 'register_user';
            $where_member = " iUserId = '{$avgRatingiMemberId}'";
        }
        if ($this->checkRatingDone($iMemberId, $UserType, $iBiddingPostId)) {
            $Data_update['vTaskStatus'] = 'NONE';
            $obj->MySQLQueryPerform($tableName, $Data_update, 'update', $where);
            $returnArr['Action'] = '1';
            $returnArr['message'] = 'LBL_TASK_FINISHED_TXT';
            if ('Passenger' === $UserType) {
                $returnArr['USER_DATA'] = getPassengerDetailInfo($iMemberId);
            } else {
                $returnArr['USER_DATA'] = getDriverDetailInfo($iMemberId);
            }
        } else {
            if (!$MODULES_OBJ->isEnableSkipRatingRide()) {
                $isSkipRating = 'No';
            }
            if ('No' === $isSkipRating) {
                $Data_update_ratings = [];
                $Data_update_ratings['iBiddingPostId'] = $iBiddingPostId;
                $Data_update_ratings['fRating'] = $rating;
                $Data_update_ratings['tMessage'] = $message;
                $Data_update_ratings['eUserType'] = $UserType;
                $Data_update_ratings['eFromUserType'] = $eFromUserType;
                $Data_update_ratings['eToUserType'] = $eToUserType;
                $id = $obj->MySQLQueryPerform('bidding_service_ratings', $Data_update_ratings, 'insert');
                $Data_update['vTaskStatus'] = 'NONE';
                $obj->MySQLQueryPerform($tableName, $Data_update, 'update', $where);
                $Data_update_member['vAvgRating'] = FetchUserAvgRating($avgRatingiMemberId, $UserType);
                $obj->MySQLQueryPerform($tableNameMember, $Data_update_member, 'update', $where_member);
            }
            if ('Yes' === $isSkipRating) {
                $iUserId = $bidding_post_data[0]['iUserId'];
                $trip_where = "iBiddingPostId='".$iBiddingPostId."'";
                $trip_data_update['isSkipRating'] = 'Yes';
                $id = $obj->MySQLQueryPerform('bidding_post', $trip_data_update, 'update', $trip_where);
            }
            if ($id > 0) {
                $returnArr['Action'] = '1';
                $returnArr['message'] = 'LBL_TASK_FINISHED_TXT';
                if ('Passenger' === $UserType) {
                    $returnArr['USER_DATA'] = getPassengerDetailInfo($iMemberId);
                } else {
                    $returnArr['USER_DATA'] = getDriverDetailInfo($iMemberId);
                }
            } else {
                $returnArr['Action'] = '0';
                $returnArr['message'] = 'LBL_TRY_AGAIN_LATER_TXT';
            }
        }
        setDataResponse($returnArr);
    }

    public function getParentBiddingCategory($iBiddingId)
    {
        global $obj, $MODULES_OBJ;
        $bidService = $obj->MySQLSelect("SELECT iParentId FROM {$this->tablename} WHERE iBiddingId = '{$iBiddingId}'");
        $iParentId = $bidService[0]['iParentId'];
        $db_fields = '';
        if ($MODULES_OBJ->isEnableAppHomeScreenLayoutV3()) {
            $db_fields .= ', vImage1';
        }
        $bidServiceArr = $obj->MySQLSelect("SELECT vImage,JSON_UNQUOTE(JSON_VALUE(vTitle, '$.vTitle_".$vLanguage."')) as vTitle,JSON_UNQUOTE(JSON_VALUE(tDescription, '$.tDescription_".$vLanguage."')) as tDescription, fCommission {$db_fields} FROM {$this->tablename} WHERE iBiddingId = '{$iParentId}'");

        return $bidServiceArr[0];
    }

    public function GenerateBidOutstandingAmount($iBiddingPostId): void
    {
        global $obj;
    }

    public function getFareDetailsGeneral($iBiddingPostId, $iMemberId = '', $GeneralUserType = '')
    {
        global $obj, $LANG_OBJ, $tconfig, $WALLET_OBJ;
        $getBiddingPost = $this->getBiddingPost('webservice', $iBiddingPostId);
        $sql = "SELECT amount FROM {$this->bidding_offer} WHERE 1 = 1 AND eStatus = 'Accepted' AND iBiddingPostId = ".$iBiddingPostId.' ORDER BY `IOfferId` DESC LIMIT 1';
        $bidding_final_offer = $obj->MySQLSelect($sql);
        if (empty($bidding_final_offer)) {
            $bidding_final_offer[0]['amount'] = $getBiddingPost[0]['fBiddingAmount'];
        }
        $total_fare = $bidding_final_offer[0]['amount'];
        $fWalletDebit = 0;
        $currencyData = $obj->MySQLSelect("SELECT vName,vSymbol from currency WHERE eDefault = 'Yes'");
        $vCurrency = $currencyData[0]['vName'];
        $lang = $LANG_OBJ->FetchDefaultLangData('vCode');
        $user_wallet_debit_amount = 0;
        $eWalletDebitAllow = $getBiddingPost[0]['eWalletDebit'];
        $tUserWalletBalance = $getBiddingPost[0]['tUserWalletBalance'];
        if ('Yes' === $eWalletDebitAllow) {
            $user_available_balance = $WALLET_OBJ->FetchMemberWalletBalance($getBiddingPost[0]['iUserId'], 'Rider');
            if ($user_available_balance > 0) {
                $totalCurrentActiveTripsArr = FetchTotalOngoingTrips($iMemberId);
                $totalCurrentActiveTripsIdsArr = $totalCurrentActiveTripsArr['ActiveTripIds'];
                $totalCurrentActiveOrderIdsArr = $totalCurrentActiveTripsArr['ActiveOrderIds'];
                $totalCurrentActiveBidIdsArr = $totalCurrentActiveTripsArr['ActiveBidIds'];
                $totalCurrentActiveTripsCount = $totalCurrentActiveTripsArr['TotalCount'];
                if (($totalCurrentActiveTripsCount > 1 || false === \in_array($iBiddingPostId, $totalCurrentActiveBidIdsArr, true)) && 'Wallet' === $ePaymentOption && $tUserWalletBalance > 0) {
                    $user_available_balance = $tUserWalletBalance;
                }
                $wallet_fare = $total_fare;
                $total_fare = 0;
                if ($wallet_fare > $user_available_balance) {
                    $user_wallet_debit_amount = $user_available_balance;
                } else {
                    $user_wallet_debit_amount = $wallet_fare;
                    $total_fare = 0;
                }
            }
            $fWalletDebit = $user_wallet_debit_amount;
        }
        $fWalletDebit = $getBiddingPost[0]['fWalletDebit'] > 0 ? $getBiddingPost[0]['fWalletDebit'] : $fWalletDebit;
        $currency = $obj->MySQLSelect("SELECT ratio,vName FROM `currency` WHERE vName='".$vCurrency."'");
        $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($lang, '1', '');
        $vTitle = $this->getServiceTitle('webservice', $getBiddingPost[0]['iBiddingId'], $getBiddingPost[0]['vTitle'], $lang);
        $fOutStandingAmount = $getBiddingPost[0]['fOutStandingAmount'];
        $fCommission = round(($bidding_final_offer[0]['amount'] * $getBiddingPost[0]['fCommission']) / 100, 2);
        $bidding_final_offer_amount = round($bidding_final_offer[0]['amount'], 2);
        if ('Driver' === $GeneralUserType) {
            $Subtotal = $bidding_final_offer_amount - $fWalletDebit - $fCommission + $fOutStandingAmount;
        } else {
            $Subtotal = $bidding_final_offer_amount - $fWalletDebit + $fOutStandingAmount;
        }
        $return_arr = [];
        $return_arr['FareDetailsNewArr'][][$languageLabelsArr['LBL_BIDDING_FINAL_AMOUNT']] = formateNumAsPerCurrency($bidding_final_offer[0]['amount'] * $currency[0]['ratio'], $vCurrency);
        if ($fOutStandingAmount > 0) {
            $return_arr['FareDetailsNewArr'][][$languageLabelsArr['LBL_OUTSTANDING_AMOUNT_TXT']] = formateNumAsPerCurrency($fOutStandingAmount * $currency[0]['ratio'], $vCurrency);
        }
        if ($fWalletDebit > 0) {
            $return_arr['FareDetailsNewArr'][][$languageLabelsArr['LBL_WALLET_ADJUSTMENT']] = '-'.formateNumAsPerCurrency($fWalletDebit * $currency[0]['ratio'], $vCurrency);
        }
        $return_arr['FareDetailsNewArr'][]['eDisplaySeperator'] = 'Yes';
        $return_arr['FareDetailsNewArr'][]['Subtotal'] = formateNumAsPerCurrency($Subtotal * $currency[0]['ratio'], $vCurrency);
        $return_arr['FareSubTotal'] = formateNumAsPerCurrency($Subtotal * $currency[0]['ratio'], $vCurrency);
        $return_arr['vBiddingPostNo'] = $getBiddingPost[0]['vBiddingPostNo'];
        $return_arr['dStartDate'] = $getBiddingPost[0]['dStartDate'];
        if ('0000-00-00 00:00:00' === $getBiddingPost[0]['dStartDate']) {
            $return_arr['dBiddingDate'] = $getBiddingPost[0]['dBiddingDate'];
        } else {
            $return_arr['dBiddingDate'] = $getBiddingPost[0]['dStartDate'];
        }
        $return_arr['vTimeZone'] = $getBiddingPost[0]['vTimeZone'];
        $return_arr['tSaddress'] = $getBiddingPost[0]['vServiceAddress'];
        $return_arr['vServiceDetailTitle'] = $vTitle ?? '';
        $return_arr['vBiddingPaymentMode'] = $getBiddingPost[0]['ePaymentOption'];
        $return_arr['vTaskStatus'] = $getBiddingPost[0]['vTaskStatus'];
        $return_arr['eStatus'] = $getBiddingPost[0]['eStatus'];
        $return_arr['fCommission'] = formateNumAsPerCurrency($fCommission * $currency[0]['ratio'], $vCurrency);
        $return_arr['ePayWallet'] = 'Wallet' === $getBiddingPost[0]['ePaymentOption'] ? 'Yes' : 'No';
        if ($Subtotal > 0 && 'Yes' === $return_arr['ePayWallet']) {
            $return_arr['ePayWallet'] = 'No';
        }
        if ('Cancelled' === $getBiddingPost[0]['eStatus']) {
            $return_arr['vCancelReason'] = $getBiddingPost[0]['iCancelReasonId'] > 0 ? $this->getCancelReason($getBiddingPost[0]['iCancelReasonId'], $lang) : $getBiddingPost[0]['vCancelReason'];
            $return_arr['eCancelledBy'] = 'User' === $getBiddingPost[0]['eCancelBy'] ? $languageLabelsArr['LBL_RIDER'] : $languageLabelsArr['LBL_PROVIDER'];
        }
        $driverData = $obj->MySQLSelect("SELECT CONCAT(vName, ' ', vLastName) as driverName, vImage,vEmail as drivermail,iDriverId FROM register_driver WHERE iDriverId = '".$getBiddingPost[0]['iDriverId']."' ");
        $return_arr['iDriverId'] = $getBiddingPost[0]['iDriverId'];
        if (!empty($driverData)) {
            $return_arr['driverAvgRating'] = $this->getAvgRating($getBiddingPost[0]['iDriverId'], 'Driver');
            $return_arr['driverName'] = $driverData[0]['driverName'];
            $return_arr['drivermail'] = $driverData[0]['drivermail'];
            $return_arr['driverImage'] = $tconfig['tsite_upload_images_driver'].'/'.$getBiddingPost[0]['iDriverId'].'/2_'.$driverData[0]['vImage'];
            $return_arr['is_rating'] = ($this->checkRatingDone($iMemberId, 'Passenger', $iBiddingPostId)) ? 'Yes' : 'No';
        }
        $userData = $obj->MySQLSelect("SELECT CONCAT(vName, ' ', vLastName) as userName, vImgName,vEmail as usermail,iUserId FROM register_user WHERE iUserId = '".$getBiddingPost[0]['iUserId']."' ");
        $return_arr['iUserId'] = $getBiddingPost[0]['iUserId'];
        $return_arr['userAvgRating'] = $this->getAvgRating($getBiddingPost[0]['iUserId'], 'Passenger');
        $return_arr['userName'] = $userData[0]['userName'];
        $return_arr['usermail'] = $userData[0]['usermail'];
        $return_arr['userImage'] = $tconfig['tsite_upload_images_passenger'].'/'.$getBiddingPost[0]['iUserId'].'/'.$userData[0]['vImgName'];
        $PassengerRatingData = $this->getBiddingRating('Passenger', $iBiddingPostId);
        $DriverRatingData = $this->getBiddingRating('Driver', $iBiddingPostId);
        $return_arr['PassengerTripRating'] = 0;
        if (!empty($PassengerRatingData) && \count($PassengerRatingData) > 0) {
            $return_arr['PassengerTripRating'] = $PassengerRatingData[0]['fRating'];
        }
        $return_arr['DriverTripRating'] = 0;
        if (!empty($DriverRatingData) && \count($DriverRatingData) > 0) {
            $return_arr['DriverTripRating'] = $DriverRatingData[0]['fRating'];
        }

        return $return_arr;
    }

    public function uploadbiddingmedia($image_name, $image_object, $iImageId, $iBiddingPostId)
    {
        global $tconfig, $UPLOAD_OBJ, $obj, $eMediaType, $iUserId;
        $vImageName = $message = '';
        $id = 0;
        $valid_Ext = 'jpg,jpeg,gif,png,mp4,webm,mov,wmv,avi,flv,mkv,mp3,wav';
        if ('Video' === $eMediaType) {
            $message = 'LBL_VIDEO_UPLOAD_SUCCESS_NOTE';
            $valid_Ext = $tconfig['tsite_upload_video_file_extensions'];
        }
        if ('Audio' === $eMediaType) {
            $message = 'LBL_AUDIO_UPLOAD_SUCCESS_NOTE';
            $valid_Ext = $tconfig['tsite_upload_audio_file_extensions'];
        }
        if ('Image' === $eMediaType) {
            $message = 'LBL_IMAGE_UPLOAD_SUCCESS_NOTE';
            $valid_Ext = $tconfig['tsite_upload_image_file_extensions'];
        }
        if ('' !== $image_name) {
            $Photo_Gallery_folder = $tconfig['tsite_upload_bidding_media_image_path'];
            if (!is_dir($Photo_Gallery_folder)) {
                mkdir($Photo_Gallery_folder, 0777);
                chmod($Photo_Gallery_folder, 0777);
            }
            $imgext = explode('.', $image_name);
            $unique = uniqid('', true);
            $file_name = substr($unique, \strlen($unique) - 4, \strlen($unique));
            $new_imagename = $file_name.'.'.$imgext[1];
            $vFile = $UPLOAD_OBJ->GeneralFileUpload($Photo_Gallery_folder, $image_object, $new_imagename, $prefix = '', $valid_Ext);
            $vImageName = $vFile[0];
        }
        if ('' !== $vImageName) {
            if ('Video' === $eMediaType) {
                $message = 'LBL_VIDEO_UPLOAD_SUCCESS_NOTE';
            }
            if ('Audio' === $eMediaType) {
                $message = 'LBL_AUDIO_UPLOAD_SUCCESS_NOTE';
            }
            if ('Image' === $eMediaType) {
                $message = 'LBL_IMAGE_UPLOAD_SUCCESS_NOTE';
            }
            $Data_update_images['vImage'] = $vImageName;
            $Data_update_images['eMediaType'] = $eMediaType;
            $Data_update_images['iBiddingPostId'] = $iBiddingPostId;
            $Data_update_images['iUserId'] = $iUserId;
            $Data_update_images['tAddedDate'] = @date('Y-m-d H:i:s');
            $id = $obj->MySQLQueryPerform($this->bidding_post_media, $Data_update_images, 'insert');
        }
        if ($id > 0 && '' !== $vImageName) {
            $returnArr['Action'] = '1';
            $returnArr['ibiddingPostMediaId'] = $id;
            $returnArr['message'] = $message;
        } else {
            $returnArr['Action'] = '0';
            $returnArr['message'] = 'LBL_TRY_AGAIN_LATER_TXT';
        }

        return $returnArr;
    }

    public function EditBiddingPostMedia($ibiddingPostMediaId, $iBiddingPostId): void
    {
        global $obj;
        $data_update_bidding_post_media['iBiddingPostId'] = $iBiddingPostId;
        $where = " ibiddingPostMediaId IN ({$ibiddingPostMediaId}) AND iBiddingPostId = 0";
        $obj->MySQLQueryPerform($this->bidding_post_media, $data_update_bidding_post_media, 'update', $where);
    }

    public function deleteallbiddingmedia($iBiddingPostId, $iUserId): array
    {
        $bidding_post_media = $this->getBiddingMediaSQLDATA($iBiddingPostId, $iUserId);
        $i = 0;
        $action_type = 'DELETEALL';
        foreach ($bidding_post_media as $media) {
            $this->deletebiddingmedia($media['ibiddingPostMediaId'], $action_type);
        }
        $getBiddingMedia = $this->getbiddingmedia($iBiddingPostId, $iUserId);
        $returnArr = [];
        if (\count($bidding_post_media) > 0) {
            $returnArr['Action'] = '1';
            $returnArr['BiddingPostMedia'] = $getBiddingMedia;
            $returnArr['message'] = 'LBL_DELETE_ALL_MEDIA_SUCCESS_NOTE';
        } else {
            $returnArr['Action'] = '0';
            $returnArr['message'] = 'LBL_TRY_AGAIN_LATER_TXT';
        }

        return $returnArr;
    }

    public function getBiddingMediaSQLDATA($iBiddingPostId, $iUserId, $GeneralUserType = '')
    {
        global $obj, $tconfig;
        $Photo_Gallery_folder = $tconfig['tsite_upload_bidding_media_image'];
        if (empty($iBiddingPostId)) {
            $iBiddingPostId = 0;
        }
        $iUserId_sql = '';
        if ('Driver' === $GeneralUserType) {
            $iUserId_sql = '';
        } else {
            $iUserId_sql = 'AND iUserId = '.$iUserId;
        }
        $sql = "SELECT *,'' as vFileName,'' as thumnails,vImage as vImageName,CONCAT('".$Photo_Gallery_folder."','/',vImage) AS vImage FROM ".$this->bidding_post_media.' Where iBiddingPostId = '.$iBiddingPostId.' '.$iUserId_sql;

        return $obj->MySQLSelect($sql);
    }

    public function deletebiddingmedia($iImageId, $action_type = 'DELETE', $iBiddingPostId = 0, $iUserId = 0): array
    {
        global $tconfig, $obj, $eMediaType;
        $message = '';
        $Photo_Gallery_folder = $tconfig['tsite_upload_bidding_media_image_path'];
        $OldImageName = get_value($this->bidding_post_media, 'vImage', 'ibiddingPostMediaId', $iImageId, '', 'true');
        if ('' !== $OldImageName) {
            unlink($Photo_Gallery_folder.$OldImageName);
            $tmpArr = explode('.', $OldImageName);
            $thumb_img = $tmpArr[0].'.png';
            unlink($Photo_Gallery_folder.'thumnails/'.$thumb_img);
        }
        $sql = "DELETE FROM {$this->bidding_post_media} WHERE `ibiddingPostMediaId`='".$iImageId."'";
        $id = $obj->sql_query($sql);
        $returnArr = [];
        if ('DELETE' === $action_type) {
            if ('Video' === $eMediaType) {
                $message = 'LBL_VIDEO_DELETE_SUCCESS_NOTE';
            }
            if ('Audio' === $eMediaType) {
                $message = 'LBL_AUDIO_DELETE_SUCCESS_NOTE';
            }
            if ('Image' === $eMediaType) {
                $message = 'LBL_IMAGE_DELETE_SUCCESS_NOTE';
            }
            $getBiddingMedia = $this->getbiddingmedia($iBiddingPostId, $iUserId);
            if ($id > 0) {
                $returnArr['BiddingPostMedia'] = $getBiddingMedia['BiddingPostMedia'];
                $returnArr['Action'] = '1';
                $returnArr['message'] = $message;
            } else {
                $returnArr['Action'] = '0';
                $returnArr['message'] = 'LBL_TRY_AGAIN_LATER_TXT';
            }
        }

        return $returnArr;
    }

    public function getbiddingmedia($iBiddingPostId, $iUserId, $UserType = '')
    {
        global $tconfig;
        $Photo_Gallery_folder = $tconfig['tsite_upload_bidding_media_image'];
        $bidding_post_media = $this->getBiddingMediaSQLDATA($iBiddingPostId, $iUserId, $UserType);
        $BiddingPostMedia = [];
        $BiddingPostMedia['Video'] = [];
        $BiddingPostMedia['Audio'] = [];
        $BiddingPostMedia['Image'] = [];
        $i = 0;
        foreach ($bidding_post_media as $media) {
            if ('Video' === $media['eMediaType']) {
                $media['thumnails'] = $this->getVideoThumbBiddingMedia($media['vImageName']);
                $media['vImage'] = $this->videoConvertTomp4($media['vImageName']);
                $BiddingPostMedia['Video'][] = $media;
            }
            if ('Audio' === $media['eMediaType']) {
                $audioCount = \count($BiddingPostMedia['Audio']) + 1;
                $media['vFileName'] = 'Audio File '.$audioCount;
                $BiddingPostMedia['Audio'][] = $media;
            }
            if ('Image' === $media['eMediaType']) {
                $BiddingPostMedia['Image'][] = $media;
            }
            ++$i;
        }
        if ($BiddingPostMedia) {
            $returnArr['Action'] = '1';
            $returnArr['BiddingPostMedia'] = $BiddingPostMedia;
        } else {
            $returnArr['Action'] = '0';
        }

        return $returnArr;
    }

    public function getVideoThumbBiddingMedia($video_file)
    {
        global $tconfig;
        $tmpArr = explode('.', $video_file);
        $thumb_img = $tmpArr[0].'.png';
        $img_url = '';
        if (!is_dir($tconfig['tsite_upload_bidding_media_image_path'].'thumnails/')) {
            mkdir($tconfig['tsite_upload_bidding_media_image_path'].'thumnails/', 0777);
            chmod($tconfig['tsite_upload_bidding_media_image_path'].'thumnails/', 0777);
        }
        $img_path = $tconfig['tsite_upload_bidding_media_image_path'].'thumnails/'.$thumb_img;
        $img_url = $tconfig['tsite_upload_bidding_media_image'].'/thumnails/'.$thumb_img;
        if (!file_exists($img_path)) {
            require_once $tconfig['tpanel_path'].'assets/libraries/FFMpeg/autoload.php';
            $sec = 3;
            $vFile = $tconfig['tsite_upload_bidding_media_image_path'].$video_file;
            if ('mkv' === $tmpArr[1]) {
                $img_path = $tconfig['tsite_upload_bidding_media_image_path'].'thumnails/'.$thumb_img;
                $ffmpeg = FFMpeg\FFMpeg::create();
                $video = $ffmpeg->open($vFile);
                $format = new X264();
                $format->setAudioCodec('libmp3lame');
                $thumb_video = $tmpArr[0].'.mp4';
                $vFile = $tconfig['tsite_upload_bidding_media_image_path'].$thumb_video;
                $video->save($format, $vFile);
            }
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

    public function videoConvertTomp4($video_file)
    {
        global $tconfig;
        $tmpArr = explode('.', $video_file);
        $thumb_img = $tmpArr[0].'.mp4';
        $vFile = '';
        $img_path = $tconfig['tsite_upload_bidding_media_image_path'].$thumb_img;
        $img_url = $tconfig['tsite_upload_bidding_media_image'].'/'.$thumb_img;
        if (!file_exists($img_path)) {
            require_once $tconfig['tpanel_path'].'assets/libraries/FFMpeg/autoload.php';
            $vFile = $tconfig['tsite_upload_bidding_media_image_path'].$video_file;
            $ffmpeg = FFMpeg\FFMpeg::create();
            $video = $ffmpeg->open($vFile);
            $format = new X264();
            $format->setAudioCodec('libmp3lame');
            $thumb_video = $tmpArr[0].'.mp4';
            $vFile = $tconfig['tsite_upload_bidding_media_image_path'].$thumb_video;
            $video->save($format, $vFile);
        }

        return $img_url;
    }

    public function getBiddingDriverStats($use, $biddingPostid = '', $iUserId = '', $vLanguage = 'EN', $ord = '', $farray = [], $iDriverId = '')
    {
        global $BIDDING_OBJ, $currency, $register_driver, $obj;
        $data = $this->getBiddingPost($use, $biddingPostid, $iUserId, $vLanguage, $ord, $farray, $iDriverId, $ListBidding = 1);
        $return['TripCount'] = \count($data);
        $totalEarnings = 0;
        for ($t = 0; $t < \count($data); ++$t) {
            $iFare = $BIDDING_OBJ->getDriverEarning($data[$t]['iBiddingPostId'])['DriverEarning'];
            $iFareSum = str_replace(',', '', $iFare);
            $totalEarnings += $iFareSum;
        }
        $return['TotalEarning'] = $totalEarnings;
        $return['TotalEarningAmount'] = formateNumAsPerCurrency($totalEarnings * $currency[0]['ratio'], $register_driver[0]['vCurrencyDriver']);
        $iBiddingPostId = array_column($data, 'iBiddingPostId');
        $bid = implode(',', $iBiddingPostId);
        $avgRating_query = 'SELECT SUM(fRating) as fRating FROM `bidding_service_ratings` WHERE eToUserType = "Driver" AND iBiddingPostId IN ("'.$bid.'")';
        $avgRating = $obj->sql_query($avgRating_query);
        $return['AvgRating'] = 0;
        if (!empty($avgRating) && 0 !== $return['TripCount']) {
            $return['AvgRating'] = $avgRating[0]['fRating'] / $return['TripCount'];
        }

        return $return;
    }

    public function getDriverEarning($iBiddingPostId)
    {
        global $obj;
        $biddingFinalAmount = $this->getbiddingFinalAmount($iBiddingPostId);
        $sql = "SELECT fCommission FROM bidding_post WHERE iBiddingPostId = '{$iBiddingPostId}' ";
        $biddingData = $obj->MySQLSelect($sql);
        $fCommission = round(($biddingFinalAmount * $biddingData[0]['fCommission']) / 100, 2);
        $data['DriverEarning'] = $biddingFinalAmount - $fCommission;

        return $data;
    }

    public function getFilterPriority($driverId)
    {
        global $obj;
        $vTimeZone = $_REQUEST['vTimeZone'] ?? '';
        $date_ = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s')));
        $serverTimeZone = date_default_timezone_get();
        if (!empty($vTimeZone)) {
            $TimeZoneOffset = converToTz($date_, $serverTimeZone, $vTimeZone, 'P');
        }
        $sql = 'SELECT count(*) as count FROM `driver_bidding_request` dbp JOIN bidding_post bp on(bp.iBiddingPostId = dbp.iBiddingPostId) WHERE dbp.iDriverId = '.$driverId." AND bp.eStatus = 'Pending' AND dbp.eStatus IN ('Pending' , 'Reoffer','Accepted') AND bp.dBiddingDate > CONVERT_TZ(NOW(), 'SYSTEM', '".$TimeZoneOffset."')";
        $data = $obj->MySQLSelect($sql);
        $pandingCount = $data[0]['count'];
        $filter = 'Pending';
        if (empty($pandingCount) || 0 === $pandingCount) {
            $sql = "SELECT count(*) as count FROM bidding_post bp WHERE bp.eStatus = 'Accepted' AND bp.iDriverId = '".$driverId."'";
            $Accepteddata = $obj->MySQLSelect($sql);
            $AcceptedCount = $Accepteddata[0]['count'];
            $filter = 'Accepted';
            if (empty($AcceptedCount) || 0 === $AcceptedCount) {
                $filter = 'Pending';
            }
        }
        $res['filter'] = $filter;

        return $res;
    }

    public function getBiddingDocument($diverId, $db_vehicle)
    {
        global $obj;
        $sql = "SELECT vBiddingId FROM `bidding_driver_service` WHERE iDriverId = {$diverId}";
        $bidding_driver_service = $obj->MySQLSelect($sql);
        $sql = "SELECT iBiddingId FROM `bidding_driver_request` WHERE iDriverId = {$diverId}";
        $bidding_driver_request = $obj->MySQLSelect($sql);
        $iBiddingId_2 = $iBiddingId_1 = $iBiddingId_arr = [];
        if (isset($bidding_driver_request) && !empty($bidding_driver_request)) {
            $iBiddingId_1 = array_column($bidding_driver_request, 'iBiddingId');
        }
        if (isset($bidding_driver_service) && !empty($bidding_driver_service)) {
            $iBiddingId_2 = explode(',', $bidding_driver_service[0]['vBiddingId']);
        }
        $iBiddingId_arr = array_merge($iBiddingId_1, $iBiddingId_2);
        $iBiddingId_arr = array_values(array_unique($iBiddingId_arr, SORT_REGULAR));
        $docNameArray = array_column($db_vehicle, 'doc_name');
        $docNameArrayCount = array_count_values($docNameArray);
        $iParentId_arr = $biddingCategoryIdArray = [];
        for ($i = 0; $i < \count($iBiddingId_arr); ++$i) {
            $bid = $iBiddingId_arr[$i];
            $iParentId = get_value('bidding_service', 'iParentId', 'iBiddingId', $bid, '', 'true');
            $iBidding_Id = get_value('bidding_service', 'iBiddingId', 'iBiddingId', $iParentId, 'and eStatus = "Active"', 'true');
            if (\in_array($iParentId, $iParentId_arr, true) || empty($iBidding_Id)) {
            } else {
                $iParentId_arr[] = $iParentId;
                foreach ($db_vehicle as $key => $value) {
                    if ($value['iBiddingId'] === $iParentId) {
                        $biddingCategoryIdArray[] = $value;
                    }
                }
            }
        }

        return $biddingCategoryIdArray;
    }

    public function driverPaymentCal($driver_payment)
    {
        if (isset($driver_payment) && !empty($driver_payment)) {
            $total_fOutStandingAmount = $total_driver_payment = $total_Amount = $Outstanding_amount_cash_trip = $total_tip = $total_cash_received = $final_amount_pay_provider = $final_amount_take_from_provider = $total_admin_commission = 0;
            foreach ($driver_payment as $payment) {
                $fOutStandingAmount = $payment['fOutStandingAmount'];
                $fBiddingAmount = $payment['fBiddingAmount'];
                $fCommission_percentage = $payment['fCommission'];
                $fCommission_percentage = ($fBiddingAmount * $fCommission_percentage) / 100;
                if ('Cash' === $payment['ePaymentOption']) {
                    $total_cash_received += $fBiddingAmount;
                    $final_amount_take_from_provider += $fCommission_percentage;
                    $total_driver_payment += ($fBiddingAmount - $fCommission_percentage) - $fBiddingAmount;
                } elseif ('Card' === $payment['ePaymentOption']) {
                    $total_driver_payment = $final_amount_pay_provider += ($fBiddingAmount - $fCommission_percentage);
                } elseif ('Wallet' === $payment['ePaymentOption']) {
                    $total_driver_payment = $final_amount_pay_provider += ($fBiddingAmount - $fCommission_percentage);
                }
                $total_admin_commission += $fCommission_percentage;
                $total_Amount += $fBiddingAmount;
                $total_fOutStandingAmount += $fOutStandingAmount;
            }
            $driverPayment['total_cash_received'] = $total_cash_received;
            $driverPayment['final_amount_pay_provider'] = $final_amount_pay_provider;
            $driverPayment['final_amount_take_from_provider'] = $final_amount_take_from_provider;
            $driverPayment['total_admin_commission'] = $total_admin_commission;
            $driverPayment['total_amount'] = $total_Amount;
            $driverPayment['total_tip'] = 0;
            $driverPayment['total_Outstanding'] = $total_fOutStandingAmount;
            $driverPayment['total_driver_payment'] = $total_driver_payment;

            return $driverPayment;
        }
    }

    private function sendCancelNotification($iBiddingPostId): void
    {
        global $obj, $LANG_OBJ, $EVENT_MSG_OBJ;
        $all_providers = $obj->MySQLSelect("SELECT rd.eDeviceType, rd.iGcmRegId, rd.eAppTerminate, rd.eDebugMode, rd.eHmsDevice, dbr.iDriverId, rd.vLang, bp.vBiddingPostNo FROM {$this->bidding_request_to_driver} as dbr LEFT JOIN {$this->register_driver} as rd ON rd.iDriverId = dbr.iDriverId LEFT JOIN {$this->biddingPostTable} as bp ON bp.iBiddingPostId = dbr.iBiddingPostId WHERE dbr.iBiddingPostId = '{$iBiddingPostId}' AND dbr.eStatus != 'Decline'");
        $notifiactondata = $final_message = [];
        $final_message['Message'] = 'BiddingTaskCancelled';
        $final_message['MsgType'] = 'BiddingTaskCancelled';
        $final_message['iBiddingPostId'] = $iBiddingPostId;
        $final_message['time'] = time();
        $final_message['eType'] = 'Bidding';
        foreach ($all_providers as $provider) {
            $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($provider['vLang'], '1', '');
            $alertMsg_db = str_replace('#TASK_NO#', $provider['vBiddingPostNo'], $languageLabelsArr['LBL_BIDDING_TASK_CANCELLED_MSG']);
            $final_message['vTitle'] = $alertMsg_db;
            $notifiactondata[] = ['eDeviceType' => $provider['eDeviceType'], 'deviceToken' => $provider['iGcmRegId'], 'alertMsg' => $alertMsg_db, 'eAppTerminate' => $provider['eAppTerminate'], 'eDebugMode' => $provider['eDebugMode'], 'eHmsDevice' => $provider['eHmsDevice'], 'message' => $final_message, 'channelName' => 'CAB_REQUEST_DRIVER_'.$provider['iDriverId']];
        }
        $EVENT_MSG_OBJ->send(['GENERAL_DATA' => $notifiactondata], RN_PROVIDER);
    }

    private function checkRatingDone($iMemberId, $UserType, $iBiddingPostId)
    {
        global $obj;
        $check_rating = $obj->MySQLSelect("SELECT iRatingId FROM bidding_service_ratings WHERE eFromUserType = '{$UserType}' AND iBiddingPostId = '{$iBiddingPostId}'");
        if (!empty($check_rating) && \count($check_rating) > 0) {
            return true;
        }

        return false;
    }
}
