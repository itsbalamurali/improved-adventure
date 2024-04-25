<?php

function GetPassengerOutstandingAmountPayment($iUserId) {
    global $obj, $PACKAGE_TYPE, $ePayWallet, $iOrganizationId, $ePaymentBy;

    if ($PACKAGE_TYPE == "SHARK") {
        if ($iOrganizationId == "" || $iOrganizationId == NULL || $ePaymentType = "ChargeOutstandingAmount") {
            $iOrganizationId = 0;
        }
        if ($ePaymentBy == "" || $ePaymentBy == NULL || $ePaymentType = "ChargeOutstandingAmount") {
            $ePaymentBy = "Passenger";
        }

        $outStandingSql = "";
        if(!empty($ePayWallet) && strtoupper($ePayWallet) == "YES") {
            $outStandingSql = " AND eAuthoriseIdName='No' AND iAuthoriseId ='0'";
        }
        if ($ePaymentBy == "Passenger" || $ePaymentBy == "Sender") {
            $sql = "SELECT SUM(fPendingAmount) as fPendingAmount FROM trip_outstanding_amount WHERE iUserId='" . $iUserId . "' AND iUserId > 0 AND ePaidByPassenger = 'No' AND ePaymentBy = 'Passenger' $outStandingSql";
            
            if ($iOrganizationId > 0) {
                $sql = $sql . " AND iOrganizationId ='" . $iOrganizationId . "'";
            } else {
                $sql = $sql . " AND iOrganizationId ='0'";
            }
        } else {
            $sql = "SELECT SUM(fPendingAmount) as fPendingAmount FROM trip_outstanding_amount WHERE iUserId = '" . $iUserId . "' AND iUserId > 0 AND iOrganizationId    ='" . $iOrganizationId . "' AND ePaidByOrganization = 'No' AND ePaymentBy = 'Organization' AND eBillGenerated = 'No' $outStandingSql";
        }

        $tripoutstandingdata = $obj->MySQLSelect($sql);

        $fPendingAmount = round($tripoutstandingdata[0]['fPendingAmount'], 2);
        if ($fPendingAmount == "" || $fPendingAmount == NULL) {
            $fPendingAmount = 0;
        }
        return $fPendingAmount;
    }
    else {
        $outStandingSql = "";
        if(!empty($ePayWallet) && strtoupper($ePayWallet) == "YES") {
            $outStandingSql = " AND eAuthoriseIdName='No' AND iAuthoriseId ='0'";
        }
        
        $sql = "SELECT SUM(fPendingAmount) as fPendingAmount FROM trip_outstanding_amount WHERE iUserId='" . $iUserId . "' AND iUserId > 0 AND ePaidByPassenger = 'No' $outStandingSql";

        $tripoutstandingdata = $obj->MySQLSelect($sql);

        $fPendingAmount = round($tripoutstandingdata[0]['fPendingAmount'], 2);
        if ($fPendingAmount == "" || $fPendingAmount == NULL) {
            $fPendingAmount = 0;
        }
        return $fPendingAmount;
    }
}

function GetCorporateProfileDetails($GeneralMemberId, $vLang = "", $eType = "") {
    global $obj, $tconfig;
    
    $returnArr = array();
    $returnArr['isOrgAvailable'] = "No";

    if($eType != "Ride") {
        return $returnArr;
    }

    $iUserId = $GeneralMemberId;
    $iUserProfileId = '';
    
    $ssql = "";
    if ($iUserProfileId != "") {
        $ssql .= " AND up.iUserProfileId = '" . $iUserProfileId . "'";
    }
    $sql1 = "SELECT up.*, upm.vProfileName, upm.vShortProfileName, upm.vImage, org.vCompany,org.ePaymentBy FROM user_profile as up LEFT JOIN user_profile_master as upm ON up.iUserProfileMasterId=upm.iUserProfileMasterId LEFT JOIN organization as org ON up.iOrganizationId=org.iOrganizationId where upm.eStatus = 'Active' AND up.eStatus = 'Active' AND org.eStatus = 'Active' AND up.iUserId = '" . $iUserId . "'" . $ssql;
    $db_data = $obj->MySQLSelect($sql1);

    
    if (count($db_data) > 0) {
        $vProfileName = "vProfileName_" . $vLang;
        $vShortProfileName = "vShortProfileName_" . $vLang;
        //Added By HJ On 18-07-2020 For Optimize trip_reason Table Query Start
        $tripreasonsArr = array();
        $tripreasonData = $obj->MySQLSelect("SELECT iUserProfileMasterId,iTripReasonId,vReasonTitle from  trip_reason  WHERE eStatus='Active'");
        for($t=0;$t<count($tripreasonData);$t++){
            $tripreasonsArr[$tripreasonData[$t]['iUserProfileMasterId']][] = $tripreasonData[$t];
        }
        //echo "<pre>";print_r($tripreasonsArr);die;
        //Added By HJ On 18-07-2020 For Optimize trip_reason Table Query End
        for ($i = 0; $i < count($db_data); $i++) {
            $vProfileNameArr = json_decode($db_data[$i]['vProfileName'], true);
            $db_data[$i]['vProfileName'] = $vProfileNameArr[$vProfileName];
            $vShortProfileNameArr = json_decode($db_data[$i]['vShortProfileName'], true);
            $db_data[$i]['vShortProfileName'] = $vShortProfileNameArr[$vShortProfileName];
            $Photo_Gallery_folder = $tconfig["tsite_upload_profile_master_path"] . '/' . $db_data[$i]['vImage'];
            if ($db_data[$i]['vImage'] != "" && file_exists($Photo_Gallery_folder)) {
                $db_data[$i]['vImage'] = $tconfig["tsite_upload_images_profile_master"] . "/" . $db_data[$i]['vImage'];
            } else {
                $db_data[$i]['vImage'] = $tconfig["tsite_upload_images_profile_master"] . "/defaulticon.png";
            }
            ## Trip Reasons ##
            //Added By HJ On 18-07-2020 For Optimize trip_reason Table Query Start
            $tripreasons = array();
            //$tripreasons = $obj->MySQLSelect("SELECT iTripReasonId,vReasonTitle from  trip_reason  WHERE iUserProfileMasterId ='" . $db_data[$i]['iUserProfileMasterId'] . "' AND eStatus='Active'");
            if(isset($tripreasonsArr[$db_data[$i]['iUserProfileMasterId']])){
                $tripreasons = $tripreasonsArr[$db_data[$i]['iUserProfileMasterId']];
            }
            //Added By HJ On 18-07-2020 For Optimize trip_reason Table Query End
            if (count($tripreasons) > 0) {
                $vReasonTitle = "vReasonTitle_" . $vLang;
                for ($j = 0; $j < count($tripreasons); $j++) {
                    $vReasonTitleArr = json_decode($tripreasons[$j]['vReasonTitle'], true);
                    $tripreasons[$j]['vReasonTitle'] = $vReasonTitleArr[$vReasonTitle];
                }
            }
            $db_data[$i]['tripReasonArr'] = $tripreasons;
            ## Trip Reasons ##
            $db_data[$i]['pay_by_organization'] = strtolower($db_data[$i]['ePaymentBy']) == "organization" ? "Yes" : "No";
        }
    }


    $returnArr['isOrgAvailable'] = count($db_data) > 0 ? "Yes" : "No";
    $returnArr['ORG_DATA'] = $db_data;
    return $returnArr;
}

?>