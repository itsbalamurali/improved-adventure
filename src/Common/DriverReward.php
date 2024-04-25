<?php



namespace Kesk\Web\Common;

class DriverReward
{
    public function __construct()
    {
        $this->campaign = 'reward_campaign';
        $this->LastRewardId = 4;
    }

    public function getDriverRewardInfo($driverId, $vLanguage)
    {
        global $obj, $tconfig, $LANG_OBJ;
        $row = [];
        $driverReward = $this->driverAchiveLetestReward($driverId, $vLanguage);
        $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLanguage, '1', '');
        $completedRewardinfo = $this->completedRewardinfo($driverReward, $vLanguage, $driverId, 'nextReward');
        $row = $completedRewardinfo;
        if (!empty($driverReward) && $driverReward[0]['eRewardLevel'] === $this->LastRewardId) {
            $row['reward_completed_text'] = $languageLabelsArr['LBL_REWARD_COMPLETED_TEXT'];
            $row['all_reward_completed'] = 'yes';
            $row['reward_details'] = [];
        } else {
            $row['all_reward_completed'] = 'no';
        }
        $rewarimage = $tconfig['tsite_upload_images_reward'].'/';
        $rewardSettings = $obj->MySQLSelect("SELECT * , CONCAT('".$rewarimage."',iRewardId,'/',vImage) as vImage, JSON_UNQUOTE(JSON_VALUE(vLevel, '$.vLevel_".$vLanguage."')) as vLevel FROM `reward_settings`");
        $vCurrency = get_value('register_driver', 'vCurrencyDriver', 'iDriverId', $driverId, '', 'true');
        $currency_ratio = get_value('currency', 'Ratio', 'vName', $vCurrency, '', 'true');
        $rewardSettings_ = [];
        for ($i = 0; $i < \count($rewardSettings); ++$i) {
            $localdata = $rewardSettings[$i];
            $rewardSettings_[$i]['level_criteria'][] = ['vTitle' => str_replace('#LABEL#', $localdata['vLevel'], $languageLabelsArr['LBL_REWARD_LABEL']), 'vValue' => ''];
            $rewardSettings_[$i]['level_criteria'][] = ['vTitle' => $languageLabelsArr['LBL_MINIMUM_TRIPS_INFO'], 'vValue' => '≥'.$localdata['vMinimumTrips']];
            $rewardSettings_[$i]['level_criteria'][] = ['vTitle' => $languageLabelsArr['LBL_RATINGS_INFO'], 'vValue' => '≥'.$localdata['fRatings'].' ★'];
            $rewardSettings_[$i]['level_criteria'][] = ['vTitle' => $languageLabelsArr['LBL_ACCEPTANCE_RATE_INFO'], 'vValue' => '≥'.$localdata['iAcceptanceRate'].'%'];
            $rewardSettings_[$i]['level_criteria'][] = ['vTitle' => $languageLabelsArr['LBL_CANCELLATION_RATE_INFO'], 'vValue' => '≤'.$localdata['iCancellationRate'].'%'];
            $rewardSettings_[$i]['level_criteria'][] = ['vTitle' => $languageLabelsArr['LBL_REWARD_AMOUNT_TXT'], 'vValue' => formateNumAsPerCurrency($localdata['fCredit'] * $currency_ratio, $vCurrency)];
            $rewardSettings_[$i]['level_criteria'][] = ['content' => $languageLabelsArr['LBL_REWARD_AMOUNT_DESC']];
            $driverReward_ = $obj->MySQLSelect("SELECT rs.fRatings as reward_fRatings,rs.iAcceptanceRate as reward_iAcceptanceRate,rs.iCancellationRate as reward_iCancellationRate, JSON_UNQUOTE(JSON_VALUE(rs.vLevel, '$.vLevel_".$vLanguage."')) as vLevel,rs.vImage,dr.iRewardId,dr.tDate,dr.iAcceptanceRate,dr.iCancellationRate,dr.fRatings FROM `driver_reward` as dr JOIN reward_settings as rs ON dr.iRewardId = rs.iRewardId WHERE dr.iRewardId = ".$localdata['iRewardId'].' AND dr.iDriverId = '.$driverId.' ');
            if (\count($driverReward_) > 0) {
                $d = $this->completedRewardinfo($driverReward_, $vLanguage, $driverId, 'detailsReward');
                $rewardSettings_[$i]['status'] = '1';
                $rewardSettings_[$i]['data'] = $d;
            }
        }
        $row['reward_earned'] = !empty($driverReward) && $driverReward[0]['iRewardId'] ? 'Yes' : 'No';
        $row['default_reward_image'] = $tconfig['tsite_url'].'assets/img/reward.png';
        $row['default_reward_title'] = $languageLabelsArr['LBL_REWARD_FEATURE_TITLE'];
        $row['rewards_to_achieve'] = $rewardSettings_;

        return $row;
    }

    public function completedRewardinfo($driverReward, $vLanguage, $driverId, $type)
    {
        global $obj, $tconfig, $LANG_OBJ;
        $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLanguage, '1', '');
        $driverRewardId = !empty($driverReward) ? $driverReward[0]['iRewardId'] : 0;
        $getnextachivement = $this->getNextAchivementLevel($driverRewardId, $driverId, $vLanguage);
        $row['vTitle'] = !empty($driverReward) && $driverReward[0]['vLevel'] ? $driverReward[0]['vLevel'] : str_replace('#REWARD#', $getnextachivement['vLevel'], $languageLabelsArr['LBL_REWARD_UNLOCK_TXT']);
        if (!empty($driverReward)) {
            $row['vImage'] = $tconfig['tsite_upload_images_reward'].'/'.$driverReward[0]['iRewardId'].'/'.$driverReward[0]['vImage'];
        } else {
            $row['vImage'] = '';
        }
        $ActiveCampaign = $this->getActiveCampaign();
        $duration_sql = '';
        if (\count($ActiveCampaign) > 0) {
            $duration_sql = " AND tEndDate BETWEEN '".$ActiveCampaign[0]['dStart_date']."' AND '".$ActiveCampaign[0]['dEnd_date']."' ";
        }
        $reward_date = !empty($driverReward) && $driverReward[0]['tDate'] ? date('M d', strtotime($driverReward[0]['tDate'])) : '';
        $Total_trip = $obj->MySQLSelect('SELECT iDriverId FROM `trips` WHERE `iDriverId` = '.$driverId.'');
        $completed_trip = $obj->MySQLSelect('SELECT iDriverId FROM `trips` WHERE `iDriverId` = '.$driverId." AND iActive = 'Finished' {$duration_sql} ");
        $uncompleted_trip = $obj->MySQLSelect('SELECT iDriverId FROM `trips` WHERE `iDriverId` = '.$driverId." AND iActive NOT IN ('Finished') ");
        $row['completed_trip'] = (\count($completed_trip) > $getnextachivement['vMinimumTrips']) ? $getnextachivement['vMinimumTrips'] : \count($completed_trip);
        $row['completed_trip'] = !empty($row['completed_trip']) ? $row['completed_trip'] : '';
        $row['Total_trip'] = !empty($getnextachivement['vMinimumTrips']) ? $getnextachivement['vMinimumTrips'] : '';
        $row['Uncompleted_trip'] = $row['Total_trip'] - $row['completed_trip'];
        $getnextachivementdate = !empty($getnextachivement['tDate']) ? date('M d', strtotime($getnextachivement['tDate'])) : '';
        $co = date('y-m-d H:i:s', strtotime('+'.$getnextachivement['iDuration'].'days'));
        $getnextachivementdate = $co ? date('d M', strtotime($co)) : '';
        $row['unlock_date'] = str_replace(['#TITLE#'], [$getnextachivement['vLevel']], $languageLabelsArr['LBL_REWARD_UNLOCK_DATE']);
        $completed_trip_percentage = round($row['completed_trip'] / $row['Total_trip'] * 100);
        $uncompleted_trip_percentage = round($row['Uncompleted_trip'] / $row['Total_trip'] * 100);
        $row['completed_trip_percentage'] = ($completed_trip_percentage > 0) ? $completed_trip_percentage : 0;
        $row['uncompleted_trip_percentage'] = ($uncompleted_trip_percentage > 0) ? $uncompleted_trip_percentage : 0;
        if ('detailsReward' === $type) {
            $row['reward_details'][0] = ['vTitle' => $languageLabelsArr['LBL_STAR_RATING'], 'vValue' => !empty($driverReward[0]['reward_fRatings']) ? $driverReward[0]['reward_fRatings'] : 0, 'is_completed' => 'Yes'];
            $row['reward_details'][1] = ['vTitle' => $languageLabelsArr['LBL_ACCEPTANCE_RATE_REWARD'], 'vValue' => $driverReward[0]['reward_iAcceptanceRate'].'%', 'is_completed' => 'Yes'];
            $row['reward_details'][2] = ['vTitle' => $languageLabelsArr['LBL_CANCELLATION_RATE_REWARD'], 'vValue' => $driverReward[0]['reward_iCancellationRate'].'%', 'is_completed' => 'Yes'];
        }
        if ('nextReward' === $type) {
            $row['reward_details'][0] = ['vTitle' => $languageLabelsArr['LBL_STAR_RATING'], 'vValue' => !empty($getnextachivement['fRatings']) ? $getnextachivement['fRatings'] : 0, 'is_completed' => $this->checkRewardCriteria($driverId, 'rating', $getnextachivement['fRatings'])];
            $row['reward_details'][1] = ['vTitle' => $languageLabelsArr['LBL_ACCEPTANCE_RATE_REWARD'], 'vValue' => $getnextachivement['iAcceptanceRate'].'%', 'is_completed' => $this->checkRewardCriteria($driverId, 'acceptance_rate', $getnextachivement['iAcceptanceRate'])];
            $row['reward_details'][2] = ['vTitle' => $languageLabelsArr['LBL_CANCELLATION_RATE_REWARD'], 'vValue' => $getnextachivement['iCancellationRate'].'%', 'is_completed' => $this->checkRewardCriteria($driverId, 'cancellation_rate', $getnextachivement['iCancellationRate'])];
        }
        $row['REWARD_HOW_IT_WORKS'] = $tconfig['tsite_url'].'reward_how_it_work.php?iPageId=57&vLang='.$vLanguage;

        return $row;
    }

    public function getNextAchivementLevel($iRewardId, $driverId, $vLanguage)
    {
        $driverReward = $this->driverAchiveLetestReward($driverId, $vLanguage);
        if (\count($driverReward) > 0) {
            $driverReward_ = $driverReward[0];
            if (1 === $driverReward_['eRewardLevel']) {
                $iRewardId = 2;
            }
            if (2 === $driverReward_['eRewardLevel']) {
                $iRewardId = 3;
            }
            if (3 === $driverReward_['eRewardLevel']) {
                $iRewardId = 4;
            }
            $rewarddata = $this->getrewardLevel($iRewardId, $vLanguage);
        } else {
            $iRewardId = 1;
            $rewarddata = $this->getrewardLevel($iRewardId, $vLanguage);
        }

        return $rewarddata;
    }

    public function assignDriverReward($driverId)
    {
        global $obj;
        $ActiveCampaign = $this->getActiveCampaign();
        if (0 === \count($ActiveCampaign)) {
            return 0;
        }
        $duration_sql = " AND tDate BETWEEN '".$ActiveCampaign[0]['dStart_date']."' AND '".$ActiveCampaign[0]['dEnd_date']."' ";
        $sql = "SELECT rs.iDriverId,COUNT(case when rs.eStatus = 'Accept' then 1 else NULL end) `Accept` , COUNT(case when rs.eStatus != '' then 1 else NULL end) `Total Request` , COUNT(case when (rs.eStatus = 'Decline' AND rs.eAcceptAttempted = 'No') then 1 else NULL end) `Decline` , COUNT(case when rs.eAcceptAttempted = 'Yes' then 1 else NULL end) `Missed` FROM driver_request rs WHERE iDriverId = ".$driverId.$duration_sql;
        $driver_request = $obj->MySQLSelect($sql);
        $Accept = $driver_request[0]['Accept'];
        $Request = $driver_request[0]['Total Request'];
        $missed = $driver_request[0]['Missed'];
        $aceptance_percentage = round((100 * ($Accept + $missed)) / $Request, 2);
        $duration_sql_1 = " AND tEndDate BETWEEN '".$ActiveCampaign[0]['dStart_date']."' AND '".$ActiveCampaign[0]['dEnd_date']."' ";
        $trips = $obj->MySQLSelect("SELECT iDriverId ,COUNT(case when iActive = 'Canceled' then 1 else NULL end) `Total_Canceled` , COUNT(case when iActive != '' then 1 else NULL end) `Total_Request` , COUNT(case when iActive != '' AND iActive = 'Finished' then 1 else NULL end) `minimum_trip` FROM `trips` WHERE `iDriverId` = ".$driverId." {$duration_sql_1}");
        $cancellation_rate = round($trips[0]['Total_Canceled'] / $trips[0]['Total_Request'] * 100);
        $minimum_trip = $trips[0]['minimum_trip'];
        $register_driver_vAvgRating = CalculateMemberAvgRating($driverId, 'Driver', $ActiveCampaign[0]['dStart_date'], $ActiveCampaign[0]['dEnd_date']);

        return $this->rewardCalculation($driverId, $day = 0, $minimum_trip, $register_driver_vAvgRating, $aceptance_percentage, $cancellation_rate);
    }

    public function driverAchiveLetestReward($driverId, $vLanguage)
    {
        global $obj;
        $ActiveCampaign = $this->getActiveCampaign();
        $sql1 = '';
        if (0 === \count($ActiveCampaign)) {
            $ActiveCampaignId = 0;
            $sql1 .= 'AND dr.iCampaignId = "'.$ActiveCampaignId.'"';
        } else {
            $ActiveCampaignId = $ActiveCampaign[0]['iCampaignId'];
            $sql1 .= 'AND dr.iCampaignId = "'.$ActiveCampaignId.'"';
        }
        $driverReward = $obj->MySQLSelect("SELECT JSON_UNQUOTE(JSON_VALUE(rs.vLevel, '$.vLevel_".$vLanguage."')) as vLevel ,rs.vImage,dr.eRewardLevel,dr.iRewardId,dr.tDate,dr.iAcceptanceRate,dr.iCancellationRate,dr.fRatings,dr.iCampaignId FROM `driver_reward` as dr JOIN reward_settings as rs ON dr.iRewardId = rs.iRewardId WHERE dr.iDriverId = ".$driverId." {$sql1} ORDER BY dr.iDriverReward DESC");

        return $driverReward;
    }

    public function getrewardLevel($iRewardId, $vLanguage)
    {
        global $obj;
        $sql = '';
        $rewardSettings = $obj->MySQLSelect("SELECT * , JSON_UNQUOTE(JSON_VALUE(vLevel, '$.vLevel_".$vLanguage."')) as vLevel FROM `reward_settings` WHERE `eRewardLevel` = ".$iRewardId."{$sql}");

        return $rewardSettings[0];
    }

    public function rewardCalculation($driverId, $day, $minimum_trip, $register_driver_vAvgRating, $aceptance_percentage, $cancellation_rate)
    {
        global $WALLET_OBJ, $obj;
        $vLanguage = '';
        $driverReward = $this->driverAchiveLetestReward($driverId, $vLanguage);
        $rewarddata = $this->getNextAchivementLevel($driverReward[0]['iRewardId'], $driverId, $vLanguage);
        $yes_to_reaward = [];
        if (\count($driverReward) > 0) {
            $driverReward_ = $driverReward[0];
            if ($driverReward_['eRewardLevel'] === $this->LastRewardId) {
                $yes_to_reaward['status'] = 2;

                return $yes_to_reaward;
            }
        }
        if ($rewarddata['vMinimumTrips'] <= $minimum_trip && $rewarddata['fRatings'] <= $register_driver_vAvgRating && $rewarddata['iAcceptanceRate'] <= $aceptance_percentage && $rewarddata['iCancellationRate'] >= $cancellation_rate) {
            $tbl_name = 'driver_reward';
            $ActiveCampaign = $this->getActiveCampaign();
            $Campaign = '';
            if (\count($ActiveCampaign) > 0) {
                $Campaign = "`iCampaignId` = '".$ActiveCampaign[0]['iCampaignId']."',";
            }
            $yes_to_reaward['status'] = 1;
            $q = 'INSERT INTO ';
            $where = '';
            $query = $q.' `'.$tbl_name."` SET `iDriverId` = '".$driverId."', `iRewardId` = '".$rewarddata['iRewardId']."', `eRewardLevel` = '".$rewarddata['eRewardLevel']."', `vMinimumTrips` = '".$minimum_trip."', `fRatings` = '".$register_driver_vAvgRating."', `iAcceptanceRate` = '".$aceptance_percentage."', `iCancellationRate` = '".$cancellation_rate."', `iDuration` = '".$day."', {$Campaign} `fCredit` = '".$rewarddata['fCredit']."', `tDate` = '".date('y-m-d H:i:s')."'".$where;
            $obj->sql_query($query);
            $data_wallet['iUserId'] = $driverId;
            $data_wallet['eUserType'] = 'Driver';
            $data_wallet['iBalance'] = $rewarddata['fCredit'];
            $data_wallet['eType'] = 'Credit';
            $data_wallet['dDate'] = date('Y-m-d H:i:s');
            $data_wallet['iTripId'] = 0;
            $data_wallet['eFor'] = 'Deposit';
            $data_wallet['ePaymentStatus'] = 'Settelled';
            $data_wallet['tDescription'] = '#LBL_REWARD_AMOUNT_CREDITED# '.$rewarddata['vLevel'];
            $WALLET_OBJ->PerformWalletTransaction($data_wallet['iUserId'], $data_wallet['eUserType'], $data_wallet['iBalance'], $data_wallet['eType'], $data_wallet['iTripId'], $data_wallet['eFor'], $data_wallet['tDescription'], $data_wallet['ePaymentStatus'], $data_wallet['dDate']);
        } else {
            $yes_to_reaward['status'] = 0;
        }

        return $yes_to_reaward;
    }

    public function getAllCampaign()
    {
        global $obj, $LANG_OBJ;
        $vLanguage = $LANG_OBJ->FetchSystemDefaultLang();
        $campaign = $obj->MySQLSelect('SELECT iCampaignId,eCurrentActive,vTitle as Title FROM '.$this->campaign." WHERE eStatus = 'Active' AND dEnd_date >= CURDATE()");

        return $campaign;
    }

    public function getActiveCampaign()
    {
        global $obj;
        $todayDate = date('Y-m-d');
        $campaign = $obj->MySQLSelect('SELECT iCampaignId,vTitle,dStart_date,dEnd_date FROM '.$this->campaign." WHERE dStart_date <= '{$todayDate}' AND dEnd_date >= '{$todayDate}' AND eStatus = 'Active' LIMIT 1");

        return $campaign;
    }

    public function updateCurrentActiveCampaign($CurrentActive)
    {
        global $obj, $LANG_OBJ;
        $vLanguage = $LANG_OBJ->FetchSystemDefaultLang();
        $campaign = $obj->MySQLSelect('SELECT iCampaignId,eCurrentActive,vTitle as Title FROM '.$this->campaign." WHERE eCurrentActive = 'Yes' AND dEnd_date >= CURDATE()");
        $arr = [];
        if (0 === \count($campaign)) {
            $obj->sql_query("UPDATE campaign SET eCurrentActive = 'No'");
            $query_p['eCurrentActive'] = 'Yes';
            $where = " iCampaignId = '{$CurrentActive}'";
            $obj->MySQLQueryPerform($this->campaign, $query_p, 'update', $where);
            $arr['status'] = 1;
        } else {
            $arr['status'] = 0;
        }

        return $arr;
    }

    public function getCampaign()
    {
        global $obj;
        $campaign = $obj->MySQLSelect('SELECT iCampaignId,dStart_date,dEnd_date,eStatus,eCurrentActive,vTitle as Title FROM '.$this->campaign);

        return $campaign;
    }

    public function getCampaignById($id)
    {
        global $obj, $LANG_OBJ;
        $vLanguage = $LANG_OBJ->FetchSystemDefaultLang();
        $campaign = $obj->MySQLSelect("SELECT vTitle as vTitle_json,iCampaignId,dStart_date,dEnd_date,eStatus,eCurrentActive,JSON_UNQUOTE(JSON_VALUE(vTitle, '$.vTitle_".$vLanguage."')) as Title FROM ".$this->campaign.' WHERE iCampaignId = '.$id.'');

        return $campaign[0];
    }

    public function notifyCampaignCancelled($campaign_id): void
    {
        global $obj, $EVENT_MSG_OBJ, $LANG_OBJ, $default_lang;
        $campaign = $obj->MySQLSelect('SELECT iCampaignId FROM '.$this->campaign." WHERE dStart_date <= CURDATE() AND dEnd_date >= CURDATE() AND iCampaignId = '{$campaign_id}'");
        if (!empty($campaign) && \count($campaign) > 0) {
            $all_drivers = $obj->MySQLSelect("SELECT iDriverId, iGcmRegId, eDebugMode, eAppTerminate, eDeviceType, eHmsDevice FROM register_driver WHERE eStatus = 'Active'");
            $lang_labels = $LANG_OBJ->FetchLanguageLabels($default_lang, '1', '');
            $final_message = [];
            $final_message['Message'] = 'RewardProgramCancelled';
            $final_message['MsgType'] = 'RewardProgramCancelled';
            $final_message['vTitle'] = $lang_labels['LBL_REWARD_PROGRAM_CANCELLED_ADMIN'];
            $final_message['time'] = time();
            $generalDataArr = [];
            foreach ($all_drivers as $driver) {
                $generalDataArr[] = ['eDeviceType' => $driver['eDeviceType'], 'deviceToken' => $driver['iGcmRegId'], 'alertMsg' => $lang_labels['LBL_REWARD_PROGRAM_CANCELLED_ADMIN'], 'eAppTerminate' => $driver['eAppTerminate'], 'eDebugMode' => $driver['eDebugMode'], 'eHmsDevice' => $driver['eHmsDevice'], 'message' => $final_message, 'channelName' => 'DRIVER_'.$driver['iDriverId']];
            }
            $EVENT_MSG_OBJ->send(['GENERAL_DATA' => $generalDataArr], RN_PROVIDER);
        }
    }

    public function validateCampaignDates($start_date, $end_date, $campaign_id)
    {
        global $obj;
        $ssql = '';
        if (!empty($campaign_id)) {
            $ssql = " AND iCampaignId != '{$campaign_id}' ";
        }
        if (!empty($start_date)) {
            $start_date = date('Y-m-d', strtotime($start_date));
            $campaign = $obj->MySQLSelect("SELECT iCampaignId FROM reward_campaign WHERE '{$start_date}' >= dStart_date AND '{$start_date}' <= dEnd_date AND eStatus = 'Active' {$ssql}");
            if (!empty($campaign) && \count($campaign) > 0) {
                return false;
            }
        }
        if (!empty($end_date)) {
            $end_date = date('Y-m-d', strtotime($end_date));
            $campaign = $obj->MySQLSelect("SELECT iCampaignId FROM reward_campaign WHERE '{$end_date}' >= dStart_date AND '{$end_date}' <= dEnd_date AND eStatus = 'Active' {$ssql}");
            if (!empty($campaign) && \count($campaign) > 0) {
                return false;
            }
        }

        return true;
    }

    private function checkRewardCriteria($iDriverId, $criteria, $value)
    {
        global $obj;
        $ActiveCampaign = $this->getActiveCampaign();
        if (0 === \count($ActiveCampaign)) {
            return 0;
        }
        if ('rating' === $criteria) {
            $register_driver_vAvgRating = CalculateMemberAvgRating($iDriverId, 'Driver', $ActiveCampaign[0]['dStart_date'], $ActiveCampaign[0]['dEnd_date']);
            if ($register_driver_vAvgRating >= $value) {
                return 'Yes';
            }
        } elseif ('acceptance_rate' === $criteria) {
            $duration_sql = " AND tDate BETWEEN '".$ActiveCampaign[0]['dStart_date']."' AND '".$ActiveCampaign[0]['dEnd_date']."' ";
            $sql = "SELECT rs.iDriverId,COUNT(case when rs.eStatus = 'Accept' then 1 else NULL end) `Accept` , COUNT(case when rs.eStatus != '' then 1 else NULL end) `Total Request` , COUNT(case when (rs.eStatus = 'Decline' AND rs.eAcceptAttempted = 'No') then 1 else NULL end) `Decline` , COUNT(case when rs.eAcceptAttempted = 'Yes' then 1 else NULL end) `Missed` FROM driver_request rs WHERE iDriverId = ".$iDriverId.$duration_sql;
            $driver_request = $obj->MySQLSelect($sql);
            $Accept = $driver_request[0]['Accept'];
            $Request = $driver_request[0]['Total Request'];
            $missed = $driver_request[0]['Missed'];
            $acceptance_percentage = round((100 * ($Accept + $missed)) / $Request, 2);
            if ($acceptance_percentage >= $value) {
                return 'Yes';
            }
        } elseif ('cancellation_rate' === $criteria) {
            $duration_sql_1 = " AND tEndDate BETWEEN '".$ActiveCampaign[0]['dStart_date']."' AND '".$ActiveCampaign[0]['dEnd_date']."' ";
            $trips = $obj->MySQLSelect("SELECT iDriverId ,COUNT(case when iActive = 'Canceled' then 1 else NULL end) `Total_Canceled` , COUNT(case when iActive != '' then 1 else NULL end) `Total_Request` , COUNT(case when iActive != '' AND iActive = 'Finished' then 1 else NULL end) `minimum_trip` FROM `trips` WHERE `iDriverId` = ".$iDriverId." {$duration_sql_1}");
            $cancellation_rate = round($trips[0]['Total_Canceled'] / $trips[0]['Total_Request'] * 100);
            if ($cancellation_rate <= $value) {
                return 'Yes';
            }
        }

        return 'No';
    }
}
