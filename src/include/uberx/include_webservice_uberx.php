<?php
############################################ Functions added ############################################
function isProviderEligibleForScheduleJob($iDriverId)
{
    global $SERVICE_PROVIDER_FLOW, $obj, $driverAvailabilityArr;
    if ($iDriverId == "") {
        return false;
    }

    if(!isset($driverAvailabilityArr)) {
        $driverAvailabilityRes = $obj->MySQLSelect("SELECT iDriverId, iDriverTimingId FROM driver_manage_timing WHERE eStatus = 'Active' AND vAvailableTimes != ''");
        $driverAvailabilityArr = array();
        for ($i = 0; $i < count($driverAvailabilityRes); $i++) { 
            $driverAvailabilityArr[$driverAvailabilityRes[$i]['iDriverId']] = $driverAvailabilityRes[$i];
        }
    }

    // $driverAvailabilityArr = $obj->MySQLSelect("SELECT iDriverTimingId FROM driver_manage_timing WHERE iDriverId = '" . $iDriverId . "' AND eStatus = 'Active' AND vAvailableTimes != ''");
    // print_r($driverAvailabilityArr);exit;
    if (isset($driverAvailabilityArr[$iDriverId]) && count($driverAvailabilityArr[$iDriverId]) > 0) {
        return true;
    }
    return false;
}

function isProviderOnline($providerDataArr)
{
    global $SERVICE_PROVIDER_FLOW, $obj, $intervalmins;
    if ($SERVICE_PROVIDER_FLOW != "Provider") {
        return true;
    }
    $cmpMinutes = ceil((fetchtripstatustimeMAXinterval() + $intervalmins) / 60);
    $str_date = @date('Y-m-d H:i:s', strtotime('-' . $cmpMinutes . ' minutes'));
    $providerAvailability = $providerDataArr['vAvailability'];
    if ($providerAvailability == "Available") {
        $vAvailability = $providerDataArr['vAvailability'];
        $vTripStatus = $providerDataArr['vTripStatus'];
        $tLocationUpdateDate = $providerDataArr['tLocationUpdateDate'];
        //echo $tLocationUpdateDate . " > " . $startDate . "<br>";
        if ($vAvailability == "Available" && $vTripStatus != "Active" && $tLocationUpdateDate > $str_date) {
            return true;
        }
    }
    return false;
}

function isProviderEligible($providerDataArr)
{
    global $SERVICE_PROVIDER_FLOW, $obj, $intervalmins;
    if ($SERVICE_PROVIDER_FLOW != "Provider") {
        return true;
    }
    $cmpMinutes = ceil((fetchtripstatustimeMAXinterval() + $intervalmins) / 60);
    $str_date = @date('Y-m-d H:i:s', strtotime('-' . $cmpMinutes . ' minutes'));
    $providerAvailability = $providerDataArr['vAvailability'];
    if ($providerAvailability == "Available") {
        $vAvailability = $providerDataArr['vAvailability'];
        $vTripStatus = $providerDataArr['vTripStatus'];
        $tLocationUpdateDate = $providerDataArr['tLocationUpdateDate'];
        //echo $tLocationUpdateDate . " > " . $startDate . "<br>";
        if ($vAvailability == "Available" && $vTripStatus != "Active" && $tLocationUpdateDate > $str_date) {
            return true;
        }
    }
    /* $driverAvailabilityArr = $obj->MySQLSelect("SELECT count(iDriverTimingId) as TotalData FROM driver_manage_timing WHERE iDriverId = '".$providerDataArr['iDriverId']."' AND eStatus = 'Active'");
      //print_r($providerDataArr['iDriverId']);exit;
      if(count($driverAvailabilityArr) > 0 && $driverAvailabilityArr[0]['TotalData'] > 0){
      return true;
  } */
    return isProviderEligibleForScheduleJob($providerDataArr['iDriverId']);
}

function getOrderDetailsAsPerId($OrderDetails, $iVehicleTypeId)
{
    for ($v = 0; $v < count($OrderDetails); $v++) {
        $iVehicleTypeId_tmp = $OrderDetails[$v]['iVehicleTypeId'];
        if ($iVehicleTypeId_tmp == $iVehicleTypeId) {
            return $OrderDetails[$v];
        }
    }
    return array();
}

//Added By HJ On 31-01-2019 For Get Vehicle Type Fare Details Start
function getVehicleTypeFareDetails($OrderDetails = array(), $iMemberId = "")
{
    global $obj, $_REQUEST, $DEFAULT_DISTANCE_UNIT, $MODULES_OBJ, $LANG_OBJ, $VIDEO_CONSULT_OBJ;
    if (empty($OrderDetails)) {
        $OrderDetails = isset($_REQUEST["OrderDetails"]) ? $_REQUEST["OrderDetails"] : '';
    }
    if (empty($iMemberId)) {
        $iMemberId = isset($_REQUEST["iMemberId"]) ? $_REQUEST["iMemberId"] : '';
    }
    $UserType = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : '';
    $iDriverId = isset($_REQUEST["iDriverId"]) ? $_REQUEST["iDriverId"] : '';
    $vCouponCode = isset($_REQUEST["vCouponCode"]) ? $_REQUEST["vCouponCode"] : '';
    $eForVideoConsultation = isset($_REQUEST["eForVideoConsultation"]) ? $_REQUEST['eForVideoConsultation'] : 'No';
    if(isset($_REQUEST['type']) && ($_REQUEST['type'] == "sendRequestToDrivers" || $_REQUEST['type'] == "ScheduleARide")) {
        $eForVideoConsultation = isset($_REQUEST["isVideoCall"]) ? $_REQUEST['isVideoCall'] : 'No';
    }
    if ($iMemberId == "") {
        $iMemberId = isset($_REQUEST["userId"]) ? $_REQUEST["userId"] : '';
    }
    if ($iMemberId == "") {
        $iMemberId = isset($_REQUEST["iUserId"]) ? $_REQUEST["iUserId"] : '';
    }
    if ($iDriverId == "") {
        $iDriverId = isset($_REQUEST["DriverID"]) ? $_REQUEST["DriverID"] : '';
    }
    if ($iDriverId == "") {
        $iDriverId = isset($_REQUEST["DriverId"]) ? $_REQUEST["DriverId"] : '';
    }
    if ($iDriverId == "") {
        $iDriverId = isset($_REQUEST["SelectedDriverId"]) ? $_REQUEST["SelectedDriverId"] : '';
    }
    if ($iDriverId == "") {
        $iDriverId = isset($_REQUEST["driverIds"]) ? $_REQUEST["driverIds"] : '';
        if (!empty($iDriverId)) {
            $iDriverId = explode(",", $iDriverId);
            if (!empty($iDriverId) && count($iDriverId) > 0) {
                $iDriverId = $iDriverId[0];
            }
        }
    }
    if ($vCouponCode == "") {
        $vCouponCode = isset($_REQUEST["PromoCode"]) ? $_REQUEST["PromoCode"] : '';
    }
    $OrderDetails = stripslashes($OrderDetails);
    $OrderDetails = json_decode(preg_replace('/[[:cntrl:]]/', '\r\n', $OrderDetails), true);
    if (empty($OrderDetails)) {
        return array();
    }
    /* Tax Calculation */ // Added by HV on 01-11-2021 to calculate tax based on provider's country as discussed with KS
    // $TaxArr = getMemberCountryTax($iMemberId, "Passenger");
    $TaxArr = getMemberCountryTax($iDriverId, "Driver");
    $fTax1 = $TaxArr['fTax1'];
    $fTax2 = $TaxArr['fTax2'];
    /* Tax Calculation */
    $tableName = "register_user";
    $fieldName = "iUserId";
    /** To Get User Language Code And Currency * */
    $userData = $obj->MySQLSelect("SELECT ru.vCurrencyPassenger, ru.vLang, cu.vSymbol, cu.Ratio, co.eUnit FROM register_user as ru, currency as cu, country as co WHERE ru.iUserId='" . $iMemberId . "' AND cu.vName = ru.vCurrencyPassenger AND co.vCountryCode = ru.vCountry");
    if (empty($userData)) {
        return array();
    }
    $vCurrencyPassenger = "";
    $vCurrencyRatio = "";
    $vCurrencySymbol = "";
    $eUnit = "KMs";
    if (count($userData) > 0) {
        $lang = $userData[0]['vLang'];
        $vCurrencyPassenger = $userData[0]['vCurrencyPassenger'];
        $vCurrencyRatio = $userData[0]['Ratio'];
        $vCurrencySymbol = $userData[0]['vSymbol'];
        $eUnit = $userData[0]['eUnit'];
    }
    if ($lang == "") {
        //$lang = get_value('language_master', 'vCode', 'eDefault', 'Yes', '', 'true');
        //Added By HJ On 22-07-2020 For Optimize language_master Table Query Start
        $lang = $LANG_OBJ->FetchDefaultLangData("vCode");
        //Added By HJ On 22-07-2020 For Optimize language_master Table Query End
    }
    /** To Get User Language Code And Currency * */
    $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($lang, "1");
    $iVehicleTypeId = $iVehicleCategoryIds = "";
    for ($t = 0; $t < count($OrderDetails); $t++) {
        if ($OrderDetails[$t]['fVehicleTypeQty'] < 1) {
            $OrderDetails[$t]['fVehicleTypeQty'] = 1;
        }
        $typeId = $OrderDetails[$t]['iVehicleTypeId'];
        $iVehicleTypeId .= "," . $typeId;
    }
    $iVehicleTypeId = trim($iVehicleTypeId, ",");

    if (!empty($vCouponCode)) {
        $getCouponCode = $obj->MySQLSelect("SELECT fDiscount,eType FROM coupon WHERE vCouponCode='" . $vCouponCode . "' AND eStatus='Active'");
        $discountValue = 0;
        if (count($getCouponCode) > 0) {
            $discountValue = $getCouponCode[0]['fDiscount'];
            $discountValueType = $getCouponCode[0]['eType'];
            if ($discountValueType != "percentage") {
                $discountValue = $discountValue * $vCurrencyRatio;
            }
        }
    }
 
    $getVehicleTypeData = $obj->MySQLSelect("SELECT vc.vCategory_" . $lang . " as CategoryName,vt.iVehicleTypeId,vt.tTypeDesc,vt.iVehicleCategoryId,vt.vVehicleType_" . $lang . " AS vVehicleType, vt.eFareType, vt.fFixedFare, vt.fPricePerHour, vt.fPricePerKM, vt.fPricePerMin, vt.iBaseFare,vt.fCommision, vt.iMinFare,vt.iPersonSize, vt.vLogo as vVehicleTypeImage, vt.eType, vt.eIconType, vt.eAllowQty, vt.fMinHour, vt.iMaxQty, vt.iVehicleTypeId, vt.fTimeSlot,vt.fTimeSlotPrice, (SELECT vcs.ePriceType FROM vehicle_category as vcs WHERE vcs.iVehicleCategoryId = vc.iParentId) as ParentPriceType, (SELECT vcs.vCategory_" . $lang . " FROM vehicle_category as vcs WHERE vcs.iVehicleCategoryId = vc.iParentId) as ParentCategoryName, (SELECT fWaitingFees FROM vehicle_category as vcs WHERE vcs.iVehicleCategoryId = vc.iParentId) as ParentWaitingFees,(SELECT iWaitingFeeTimeLimit FROM vehicle_category as vcs WHERE vcs.iVehicleCategoryId = vc.iParentId) as ParentWaitingTimeLimit,(SELECT fCommision FROM vehicle_category as vcs WHERE vcs.iVehicleCategoryId = vc.iParentId) as ParentCommisionPer,(SELECT eMaterialCommision FROM vehicle_category as vcs WHERE vcs.iVehicleCategoryId = vc.iParentId) as ParentMaterialCommisionEnable,(SELECT fCancellationFare FROM vehicle_category as vcs WHERE vcs.iVehicleCategoryId = vc.iParentId) as ParentCancellationFare,(SELECT iCancellationTimeLimit FROM vehicle_category as vcs WHERE vcs.iVehicleCategoryId = vc.iParentId) as ParentCancellationTimeLimit, vc.iParentId as ParentVehicleCategoryId, IF((SELECT vcs.ePriceType FROM vehicle_category as vcs WHERE vcs.iVehicleCategoryId = vc.iParentId)='Provider', (SELECT spa.fAmount from service_pro_amount as spa WHERE spa.iVehicleTypeId=vt.iVehicleTypeId AND spa.iDriverVehicleId =(SELECT dv.iDriverVehicleId FROM driver_vehicle as dv WHERE dv.iDriverId='" . $iDriverId . "' AND dv.eType='UberX' AND dv.eStatus='Active')), NULL) as ProviderPrice, IF(vt.iLocationid != -1, (SELECT co.eUnit FROM country as co, location_master as lm WHERE co.iCountryId = lm.iCountryId AND lm.iLocationid = vt.iLocationid), '" . $DEFAULT_DISTANCE_UNIT . "') as LocationUnit,vc.eVideoConsultServiceCharge FROM vehicle_type vt, vehicle_category as vc WHERE vt.iVehicleCategoryId >0 AND vt.iVehicleTypeId IN ($iVehicleTypeId) AND vt.eStatus='Active' AND vc.iVehicleCategoryId = vt.iVehicleCategoryId");
    $vehicleTypeArr = $vehiclePriceTypeArr = $vehiclePriceTypeSaveArr = $vehicleCatNameArr = $vehiclePriceTypeArrItems = $vehiclePriceTypeArrCubex = array();
    //added by SP for new design $vehiclePriceTypeArrItems,$vehiclePriceTypeArrCubex here and in type getVehicleTypeFareDetails also on 10-9-2019

    $totalFareOfServices = $totalCommissionOfServices = 0;
    $eFareTypeServices = "";
    $currentPriceTypeArrCounti = $currentPriceTypeArrCountCubex = 0;
    if ($eForVideoConsultation == "Yes") {
        $getVehicleTypeData = $obj->MySQLSelect("SELECT vc.vCategory_" . $lang . " as CategoryName,(SELECT vcs.ePriceType FROM vehicle_category as vcs WHERE vcs.iVehicleCategoryId = vc.iParentId) as ParentPriceType, (SELECT vcs.vCategory_" . $lang . " FROM vehicle_category as vcs WHERE vcs.iVehicleCategoryId = vc.iParentId) as ParentCategoryName, (SELECT fWaitingFees FROM vehicle_category as vcs WHERE vcs.iVehicleCategoryId = vc.iParentId) as ParentWaitingFees,(SELECT iWaitingFeeTimeLimit FROM vehicle_category as vcs WHERE vcs.iVehicleCategoryId = vc.iParentId) as ParentWaitingTimeLimit,(SELECT fCommision FROM vehicle_category as vcs WHERE vcs.iVehicleCategoryId = vc.iParentId) as ParentCommisionPer,(SELECT eMaterialCommision FROM vehicle_category as vcs WHERE vcs.iVehicleCategoryId = vc.iParentId) as ParentMaterialCommisionEnable,(SELECT fCancellationFare FROM vehicle_category as vcs WHERE vcs.iVehicleCategoryId = vc.iParentId) as ParentCancellationFare,(SELECT iCancellationTimeLimit FROM vehicle_category as vcs WHERE vcs.iVehicleCategoryId = vc.iParentId) as ParentCancellationTimeLimit, vc.iParentId as ParentVehicleCategoryId,vc.eVideoConsultServiceCharge, 'Fixed' as eFareType, '$iVehicleTypeId' as iVehicleTypeId  FROM vehicle_category as vc WHERE vc.iVehicleCategoryId = '$iVehicleTypeId'");
        $video_consult_data = $VIDEO_CONSULT_OBJ->getServiceDetails($iDriverId, $iVehicleTypeId);
        $eVideoConsultEnableProvider = $video_consult_data['eVideoConsultEnableProvider'];
        if ($eVideoConsultEnableProvider == "Yes") {
            $totalFareOfServicesVC = $video_consult_data['eVideoConsultServiceCharge'];
        }
        else {
            $totalFareOfServicesVC = $getVehicleTypeData[0]['eVideoConsultServiceCharge'];
        }
    }
    for ($v = 0; $v < count($getVehicleTypeData); $v++) {
        $tTypeDescription = "";
        $tTypeDesc = (array)json_decode($getVehicleTypeData[$v]['tTypeDesc']);
        if (isset($tTypeDesc['tTypeDesc_' . $lang]) && $tTypeDesc['tTypeDesc_' . $lang] != "") {
            $tTypeDescription = $tTypeDesc['tTypeDesc_' . $lang];
        }
        $getVehicleTypeData[$v]['tTypeDesc'] = $tTypeDescription;
        $vehicleTypeArr[$getVehicleTypeData[$v]['iVehicleTypeId']] = $getVehicleTypeData[$v];
        $iVehicleCategoryIds .= "," . $getVehicleTypeData[$v]['iVehicleCategoryId'];
        $OrderDetails_tmp = getOrderDetailsAsPerId($OrderDetails, $getVehicleTypeData[$v]['iVehicleTypeId']);
        if ($getVehicleTypeData[$v]['ProviderPrice'] != NULL) {
            $getVehicleTypeData[$v]['fFixedFare'] = $getVehicleTypeData[$v]['ProviderPrice'];
            $getVehicleTypeData[$v]['fPricePerHour'] = $getVehicleTypeData[$v]['ProviderPrice'];
        }
        unset($getVehicleTypeData[$v]['ProviderPrice']);
        // unset($getVehicleTypeData[$v]['ParentPriceType']);
        $currentPriceTypeArrCount = count($vehiclePriceTypeArr);
        $returnArr['ParentCategoryName'] = $getVehicleTypeData[$v]['ParentCategoryName'];
        $eFareTypeServices = $getVehicleTypeData[$v]['eFareType'];
        $vehiclePriceTypeSaveArr[$currentPriceTypeArrCount]['id'] = $getVehicleTypeData[$v]['iVehicleTypeId'];
        $vehiclePriceTypeSaveArr[$currentPriceTypeArrCount]['qty'] = $OrderDetails_tmp['fVehicleTypeQty'];
        $vehiclePriceTypeSaveArr[$currentPriceTypeArrCount]['eAllowQty'] = $getVehicleTypeData[$v]['eAllowQty'];
        $vehiclePriceTypeSaveArr[$currentPriceTypeArrCount]['MinimumHour'] = $getVehicleTypeData[$v]['eFareType'] == "Hourly" ? $getVehicleTypeData[$v]['fMinHour'] : 0;
        $vehiclePriceTypeSaveArr[$currentPriceTypeArrCount]['amount'] = $getVehicleTypeData[$v][$getVehicleTypeData[$v]['eFareType'] == "Fixed" ? 'fFixedFare' : ($getVehicleTypeData[$v]['eFareType'] == "Hourly" ? 'fPricePerHour' : 'iBaseFare')];
        $vehiclePriceTypeSaveArr[$currentPriceTypeArrCount]['fCommision'] = round(((($vehiclePriceTypeSaveArr[$currentPriceTypeArrCount]['amount'] * $OrderDetails_tmp['fVehicleTypeQty']) * $getVehicleTypeData[$v]['fCommision']) / 100), 2);
        $returnArr['tripFareDetailsSaveArr']['ParentWaitingFees'] = $getVehicleTypeData[$v]['ParentWaitingFees'];
        $returnArr['tripFareDetailsSaveArr']['ParentWaitingTimeLimit'] = $getVehicleTypeData[$v]['ParentWaitingTimeLimit'];
        $returnArr['tripFareDetailsSaveArr']['ParentCommision'] = $getVehicleTypeData[$v]['ParentCommisionPer'];
        $returnArr['tripFareDetailsSaveArr']['ParentMaterialCommisionEnable'] = $getVehicleTypeData[$v]['ParentMaterialCommisionEnable'];
        $returnArr['tripFareDetailsSaveArr']['ParentCancellationFare'] = $getVehicleTypeData[$v]['ParentCancellationFare'];
        $returnArr['tripFareDetailsSaveArr']['ParentCancellationTimeLimit'] = $getVehicleTypeData[$v]['ParentCancellationTimeLimit'];
        $returnArr['tripFareDetailsSaveArr']['eFareTypeServices'] = $eFareTypeServices;
        $returnArr['tripFareDetailsSaveArr']['ParentPriceType'] = $getVehicleTypeData[$v]['ParentPriceType'];
        $returnArr['tripFareDetailsSaveArr']['ParentVehicleCategoryId'] = $getVehicleTypeData[$v]['ParentVehicleCategoryId'];
        $totalCommissionOfServices += $vehiclePriceTypeSaveArr[$currentPriceTypeArrCount]['fCommision'];
        if ($getVehicleTypeData[$v]['eFareType'] != "Fixed" && count($vehiclePriceTypeArr) == 0) {
            $vehiclePriceTypeArr[$currentPriceTypeArrCount]['Title'] = $getVehicleTypeData[$v]['vVehicleType'];
            $vehiclePriceTypeArr[$currentPriceTypeArrCount]['Amount'] = "";
            $vehiclePriceTypeArr[$currentPriceTypeArrCount]['Qty'] = "";
            $vehiclePriceTypeArr[$currentPriceTypeArrCount]['vVehicleCategory'] = $getVehicleTypeData[$v]['ParentCategoryName'];
            $vehiclePriceTypeArr[$currentPriceTypeArrCount]['iVehicleTypeId'] = $getVehicleTypeData[$v]['iVehicleTypeId'];
            $currentPriceTypeArrCount = count($vehiclePriceTypeArr);
        }
        if ($getVehicleTypeData[$v]['eFareType'] == "Fixed") {
            $fFixedFare = round($getVehicleTypeData[$v]['fFixedFare'] * $vCurrencyRatio, 2);
            $fFixedFare = $fFixedFare * $OrderDetails_tmp['fVehicleTypeQty'];
            $totalFareOfServices = $totalFareOfServices + $fFixedFare;
            $totalFareOfServices_orig = $totalFareOfServices;
            $fFixedFare_formmated = formateNumAsPerCurrency($fFixedFare, $vCurrencyPassenger);
            $vehiclePriceTypeArr[$currentPriceTypeArrCount]['Title'] = $getVehicleTypeData[$v]['vVehicleType'];
            $vehiclePriceTypeArr[$currentPriceTypeArrCount]['Amount'] = $fFixedFare_formmated;
            // $vehiclePriceTypeArr[$currentPriceTypeArrCount]['Qty'] = "x" . strval($OrderDetails_tmp['fVehicleTypeQty']);
            $vehiclePriceTypeArr[$currentPriceTypeArrCount]['Qty'] = "";
            $vehiclePriceTypeArr[$currentPriceTypeArrCount]['vVehicleCategory'] = $getVehicleTypeData[$v]['ParentCategoryName'];
            $vehiclePriceTypeArr[$currentPriceTypeArrCount]['iVehicleTypeId'] = $getVehicleTypeData[$v]['iVehicleTypeId'];
            $vehiclePriceTypeArrItems[$currentPriceTypeArrCounti]['Title'] = $getVehicleTypeData[$v]['vVehicleType'];
            $vehiclePriceTypeArrItems[$currentPriceTypeArrCounti]['Amount'] = $fFixedFare_formmated;
            // $vehiclePriceTypeArrItems[$currentPriceTypeArrCounti]['Qty'] = "x " . strval($OrderDetails_tmp['fVehicleTypeQty']);
            $vehiclePriceTypeArrItems[$currentPriceTypeArrCounti]['Qty'] = "";
            $vehiclePriceTypeArrItems[$currentPriceTypeArrCounti]['vVehicleCategory'] = $getVehicleTypeData[$v]['ParentCategoryName'];
            $vehiclePriceTypeArrItems[$currentPriceTypeArrCounti]['eFareType'] = $getVehicleTypeData[$v]['eFareType'];
            $vehiclePriceTypeArrItems[$currentPriceTypeArrCounti]['iVehicleTypeId'] = $getVehicleTypeData[$v]['iVehicleTypeId'];
            if ($eForVideoConsultation == "Yes") {
                $vehiclePriceTypeArr[$currentPriceTypeArrCount]['Title'] = $getVehicleTypeData[$v]['CategoryName'];
                $vehiclePriceTypeArr[$currentPriceTypeArrCount]['Amount'] = "";
                $vehiclePriceTypeArr[$currentPriceTypeArrCount]['Qty'] = "";
                $vehiclePriceTypeArrItems[$currentPriceTypeArrCounti]['Title'] = $getVehicleTypeData[$v]['CategoryName'];
                $vehiclePriceTypeArrItems[$currentPriceTypeArrCounti]['Amount'] = "";
                $vehiclePriceTypeArrItems[$currentPriceTypeArrCounti]['Qty'] = "";
            }
            $currentPriceTypeArrCounti = $currentPriceTypeArrCounti + 1;
            // $fFixedFare1 = $fFixedFare + $fFixedFare1;
            $fFixedFare1 = $fFixedFare;
            $qty1 = $OrderDetails_tmp['fVehicleTypeQty'];
            if ($eForVideoConsultation == "Yes") {
                $totalFareOfServices = $totalFareOfServices_orig = $fFixedFare1 = $totalFareOfServicesVC;
            }

            if ($v == (count($getVehicleTypeData) - 1)) {
                $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Title'] = $languageLabelsArr['LBL_SERVICE_COST'];
                $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Amount'] = formateNumAsPerCurrency($totalFareOfServices, $vCurrencyPassenger);
                // $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Qty'] = "x " . strval($qty1);
                $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Qty'] = "";
                $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['vVehicleCategory'] = $getVehicleTypeData[$v]['ParentCategoryName'];
                $currentPriceTypeArrCountCubex = $currentPriceTypeArrCountCubex + 1;

                $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['eDisplaySeperator'] = "Yes";
                $currentPriceTypeArrCountCubex = $currentPriceTypeArrCountCubex + 1;

                if (isset($discountValue) && $discountValue != 0) {
                    if ($discountValueType == "percentage") {
                        $discountValue = (round(($totalFareOfServices * $discountValue), 2) / 100);
                        $vDiscount = "- " . formateNumAsPerCurrency($discountValue, $vCurrencyPassenger);
                    }
                    else {
                        $discountValue = (round($discountValue > $totalFareOfServices ? $totalFareOfServices : $discountValue, 2));
                        $vDiscount = "- " . formateNumAsPerCurrency($discountValue, $vCurrencyPassenger);
                    }
                    $vehiclePriceTypeArr[$currentPriceTypeArrCount + 1]['Title'] = $languageLabelsArr['LBL_PROMO_DISCOUNT_TITLE'];
                    $vehiclePriceTypeArr[$currentPriceTypeArrCount + 1]['Amount'] = $vDiscount;
                    $vehiclePriceTypeArr[$currentPriceTypeArrCount + 1]['Qty'] = "";
                    $vehiclePriceTypeArr[$currentPriceTypeArrCount + 1]['vVehicleCategory'] = "";
                    $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Title'] = $languageLabelsArr['LBL_PROMO_DISCOUNT_TITLE'];
                    $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Amount'] = $vDiscount;
                    $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Qty'] = "";
                    $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['vVehicleCategory'] = "";
                    $currentPriceTypeArrCountCubex = count($vehiclePriceTypeArrCubex);

                    $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['eDisplaySeperator'] = "Yes";
                    $currentPriceTypeArrCountCubex = $currentPriceTypeArrCountCubex + 1;
                    //$final_price_formatted = $vCurrencySymbol . formatNum($totalFareOfServices - $discountValue);
                    if ($_REQUEST['type'] == 'getVehicleTypeFareDetails') {
                        $totalFareOfServices = $totalFareOfServices - $discountValue;
                    }
                    else {
                        $totalFareOfServices = $totalFareOfServices;
                    }
                    // added for tax
                    if ($fTax1 > 0) {
                        $taxamount1 = round((($totalFareOfServices * $fTax1) / 100), 2);
                        $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Title'] = $languageLabelsArr['LBL_TAX1_TXT'] . " @ " . $fTax1 . " % ";
                        $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Amount'] = formateNumAsPerCurrency($taxamount1, $vCurrencyPassenger);
                        $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Qty'] = " ";
                        $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['vVehicleCategory'] = $getVehicleTypeData[$v]['ParentCategoryName'];
                        $currentPriceTypeArrCountCubex = count($vehiclePriceTypeArrCubex);
                        /*if ($_REQUEST['type'] == 'getVehicleTypeFareDetails') {
                            $totalFareOfServices = $totalFareOfServices + $taxamount1;
                        }*/
                        $totalFareOfServices = $totalFareOfServices + $taxamount1;

                        $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['eDisplaySeperator'] = "Yes";
                        $currentPriceTypeArrCountCubex = $currentPriceTypeArrCountCubex + 1;
                    }
                    if ($fTax2 > 0) {
                        $taxamount2 = round((($totalFareOfServices * $fTax2) / 100), 2);
                        $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Title'] = $languageLabelsArr['LBL_TAX2_TXT'] . " @ " . $fTax2 . " % ";
                        //$vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Amount'] = $vCurrencySymbol . formatNum($taxamount2);
                        $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Amount'] = formateNumAsPerCurrency($taxamount2, $vCurrencyPassenger);
                        $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Qty'] = " ";
                        $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['vVehicleCategory'] = $getVehicleTypeData[$v]['ParentCategoryName'];
                        $currentPriceTypeArrCountCubex = count($vehiclePriceTypeArrCubex);
                        /*if ($_REQUEST['type'] == 'getVehicleTypeFareDetails') {
                             $totalFareOfServices = $totalFareOfServices + $taxamount2;
                         }*/
                        $totalFareOfServices = $totalFareOfServices + $taxamount2;

                        $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['eDisplaySeperator'] = "Yes";
                        $currentPriceTypeArrCountCubex = $currentPriceTypeArrCountCubex + 1;
                    }
                    // added for tax
                    $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Title'] = $languageLabelsArr['LBL_SUBTOTAL_TXT'];
                    //$vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Amount'] = $vCurrencySymbol . formatNum($totalFareOfServices);
                    $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Amount'] = formateNumAsPerCurrency($totalFareOfServices, $vCurrencyPassenger);
                    $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Qty'] = "";
                    $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['vVehicleCategory'] = "";
                    $currentPriceTypeArrCountCubex = count($vehiclePriceTypeArrCubex);

                    $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['eDisplaySeperator'] = "Yes";
                    $currentPriceTypeArrCountCubex = $currentPriceTypeArrCountCubex + 1;
                    //$currentPriceTypeArrCountCubex = $currentPriceTypeArrCountCubex + 1;
                    //$fFixedFare_serv = $fFixedFare + $fFixedFare_serv;
                    //$vehiclePriceTypeArr[$currentPriceTypeArrCount]['Title'] = $languageLabelsArr['LBL_SERVICE_COST']; //$getVehicleTypeData[$v]['vVehicleType'];
                    //$vehiclePriceTypeArr[$currentPriceTypeArrCount]['Amount'] = $vCurrencySymbol . formatNum($fFixedFare_serv);
                    //$vehiclePriceTypeArr[$currentPriceTypeArrCount]['Qty'] = "x" . strval($OrderDetails_tmp['fVehicleTypeQty']);
                    //$vehiclePriceTypeArr[$currentPriceTypeArrCount]['vVehicleCategory'] = $getVehicleTypeData[$v]['ParentCategoryName'];
                    $vehiclePriceTypeArr[$currentPriceTypeArrCount + 2]['Title'] = $languageLabelsArr['LBL_SUBTOTAL_TXT'];
                    $vehiclePriceTypeArr[$currentPriceTypeArrCount + 2]['Amount'] = formateNumAsPerCurrency($totalFareOfServices, $vCurrencyPassenger);
                    $vehiclePriceTypeArr[$currentPriceTypeArrCount + 2]['Qty'] = "";
                    $vehiclePriceTypeArr[$currentPriceTypeArrCount + 2]['vVehicleCategory'] = "";
                    if ($UserType == "Driver") {
                        $sqlp = "SELECT cu.vName, cu.iCurrencyId, cu.eRoundingOffEnable FROM register_driver AS rd LEFT JOIN currency AS cu ON rd.vCurrencyDriver = cu.vName WHERE rd.iDriverId = '" . $iDriverId . "'";
                        $currData = $obj->MySQLSelect($sqlp);
                        $vCurrency = $currData[0]['vName'];
                    }
                    else {
                        $sqlp = "SELECT cu.vName, cu.iCurrencyId, cu.eRoundingOffEnable FROM register_user AS ru LEFT JOIN currency AS cu ON ru.vCurrencyPassenger = cu.vName WHERE ru.iUserId = '" . $iMemberId . "'";
                        $currData = $obj->MySQLSelect($sqlp);
                        $vCurrency = $currData[0]['vName'];
                    }
                    if ($currData[0]['eRoundingOffEnable'] == "Yes" && $MODULES_OBJ->isEnableRoundingMethod()) {
                        $roundingOffTotal_fare_amountArr = getRoundingOffAmount($totalFareOfServices, $vCurrency);
                        $roundingOffTotal_fare_amount = $roundingOffTotal_fare_amountArr['finalFareValue'];
                        if ($roundingOffTotal_fare_amountArr['method'] == "Addition") {
                            $roundingMethod = "";
                        }
                        else {
                            $roundingMethod = "-";
                        }
                        $rounding_diff = $roundingMethod . ' ' . formateNumAsPerCurrency($roundingOffTotal_fare_amountArr['differenceValue'], $vCurrency);
                        $totalFareOfServices_orig = $roundingOffTotal_fare_amount;
                        $totalFareOfServices = formateNumAsPerCurrency($roundingOffTotal_fare_amount, $vCurrency);
                        if (!empty($roundingOffTotal_fare_amountArr['differenceValue']) && $roundingOffTotal_fare_amountArr['differenceValue'] != 0) {
                            $vehiclePriceTypeArr[$currentPriceTypeArrCount + 3]['Title'] = $languageLabelsArr['LBL_ROUNDING_DIFF_TXT'];
                            $vehiclePriceTypeArr[$currentPriceTypeArrCount + 3]['Amount'] = $rounding_diff;
                            $vehiclePriceTypeArr[$currentPriceTypeArrCount + 3]['Qty'] = "";
                            $vehiclePriceTypeArr[$currentPriceTypeArrCount + 3]['vVehicleCategory'] = "";
                            $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Title'] = $languageLabelsArr['LBL_ROUNDING_DIFF_TXT'];
                            $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Amount'] = $rounding_diff;
                            $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Qty'] = "";
                            $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['vVehicleCategory'] = "";
                            $currentPriceTypeArrCountCubex = count($vehiclePriceTypeArrCubex);

                            $vehiclePriceTypeArr[$currentPriceTypeArrCount + 4]['Title'] = $languageLabelsArr['LBL_FINAL_TOTAL'];
                            $vehiclePriceTypeArr[$currentPriceTypeArrCount + 4]['Amount'] = $totalFareOfServices;
                            $vehiclePriceTypeArr[$currentPriceTypeArrCount + 4]['Qty'] = "";
                            $vehiclePriceTypeArr[$currentPriceTypeArrCount + 4]['vVehicleCategory'] = "";
                            $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Title'] = $languageLabelsArr['LBL_FINAL_TOTAL'];
                            $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Amount'] = $totalFareOfServices;
                            $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Qty'] = "";
                            $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['vVehicleCategory'] = "";
                            $currentPriceTypeArrCountCubex = count($vehiclePriceTypeArrCubex);
                        }
                    }
                }
                else {
                    if ($eForVideoConsultation == "Yes") {
                        $totalFareOfServices = $totalFareOfServicesVC;
                    }
                    // added for tax
                    if ($fTax1 > 0) {
                        $taxamount1 = round((($totalFareOfServices * $fTax1) / 100), 2);
                        $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Title'] = $languageLabelsArr['LBL_TAX1_TXT'] . " @ " . $fTax1 . " % ";
                        //$vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Amount'] = $vCurrencySymbol . formatNum($taxamount1);
                        $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Amount'] = formateNumAsPerCurrency($taxamount1, $vCurrencyPassenger);
                        $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Qty'] = " ";
                        $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['vVehicleCategory'] = $getVehicleTypeData[$v]['ParentCategoryName'];
                        $currentPriceTypeArrCountCubex = count($vehiclePriceTypeArrCubex);
                        /*if ($_REQUEST['type'] == 'getVehicleTypeFareDetails') {
                            $totalFareOfServices = $totalFareOfServices + $taxamount1;
                        }*/
                        $totalFareOfServices = $totalFareOfServices + $taxamount1;

                        $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['eDisplaySeperator'] = "Yes";
                        $currentPriceTypeArrCountCubex = $currentPriceTypeArrCountCubex + 1;
                    }
                    if ($fTax2 > 0) {
                        $taxamount2 = round((($totalFareOfServices * $fTax2) / 100), 2);
                        $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Title'] = $languageLabelsArr['LBL_TAX2_TXT'] . " @ " . $fTax2 . " % ";
                        $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Amount'] = formateNumAsPerCurrency($taxamount2, $vCurrencyPassenger);
                        $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Qty'] = " ";
                        $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['vVehicleCategory'] = $getVehicleTypeData[$v]['ParentCategoryName'];
                        $currentPriceTypeArrCountCubex = count($vehiclePriceTypeArrCubex);
                        /*if ($_REQUEST['type'] == 'getVehicleTypeFareDetails') {
                            $totalFareOfServices = $totalFareOfServices + $taxamount2;
                        }*/
                        $totalFareOfServices = $totalFareOfServices + $taxamount2;

                        $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['eDisplaySeperator'] = "Yes";
                        $currentPriceTypeArrCountCubex = $currentPriceTypeArrCountCubex + 1;
                    }
                    // added for tax
                    if ($UserType == "Driver") {
                        $sqlp = "SELECT cu.vName, cu.iCurrencyId, cu.eRoundingOffEnable FROM register_driver AS rd LEFT JOIN currency AS cu ON rd.vCurrencyDriver = cu.vName WHERE rd.iDriverId = '" . $iDriverId . "'";
                        $currData = $obj->MySQLSelect($sqlp);
                        $vCurrency = $currData[0]['vName'];
                    }
                    else {
                        $sqlp = "SELECT cu.vName, cu.iCurrencyId, cu.eRoundingOffEnable FROM register_user AS ru LEFT JOIN currency AS cu ON ru.vCurrencyPassenger = cu.vName WHERE ru.iUserId = '" . $iMemberId . "'";
                        $currData = $obj->MySQLSelect($sqlp);
                        $vCurrency = $currData[0]['vName'];
                    }
                    $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Title'] = $languageLabelsArr['LBL_SUBTOTAL_TXT'];
                    $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Amount'] = formateNumAsPerCurrency($totalFareOfServices, $vCurrencyPassenger);
                    $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Qty'] = "";
                    $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['vVehicleCategory'] = "";
                    $currentPriceTypeArrCountCubex = count($vehiclePriceTypeArrCubex);

                    // $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['eDisplaySeperator'] = 'Yes';
                    // $currentPriceTypeArrCountCubex++;

                    //$currentPriceTypeArrCountCubex = $currentPriceTypeArrCountCubex + 1;
                    //$fFixedFare_serv = $fFixedFare + $fFixedFare_serv;
                    //$vehiclePriceTypeArr[$currentPriceTypeArrCount]['Title'] = $languageLabelsArr['LBL_SERVICE_COST']; //$getVehicleTypeData[$v]['vVehicleType'];
                    //$vehiclePriceTypeArr[$currentPriceTypeArrCount]['Amount'] = $vCurrencySymbol . formatNum($fFixedFare_serv);
                    //$vehiclePriceTypeArr[$currentPriceTypeArrCount]['Qty'] = "x" . strval($OrderDetails_tmp['fVehicleTypeQty']);
                    //$vehiclePriceTypeArr[$currentPriceTypeArrCount]['vVehicleCategory'] = $getVehicleTypeData[$v]['ParentCategoryName'];
                    $vehiclePriceTypeArr[$currentPriceTypeArrCount + 1]['Title'] = $languageLabelsArr['LBL_SUBTOTAL_TXT'];
                    $vehiclePriceTypeArr[$currentPriceTypeArrCount + 1]['Amount'] = formateNumAsPerCurrency($totalFareOfServices, $vCurrencyPassenger);
                    $vehiclePriceTypeArr[$currentPriceTypeArrCount + 1]['Qty'] = "";
                    $vehiclePriceTypeArr[$currentPriceTypeArrCount + 1]['vVehicleCategory'] = "";
                    if ($currData[0]['eRoundingOffEnable'] == "Yes" && $MODULES_OBJ->isEnableRoundingMethod()) {
                        $roundingOffTotal_fare_amountArr = getRoundingOffAmount($totalFareOfServices, $vCurrency);
                        $roundingOffTotal_fare_amount = $roundingOffTotal_fare_amountArr['finalFareValue'];
                        if ($roundingOffTotal_fare_amountArr['method'] == "Addition") {
                            $roundingMethod = "";
                        }
                        else {
                            $roundingMethod = "-";
                        }
                        $rounding_diff = $roundingMethod . ' ' . formateNumAsPerCurrency($roundingOffTotal_fare_amountArr['differenceValue'], $vCurrency);
                        //$totalFareOfServices_orig = $roundingOffTotal_fare_amount;
                        $totalFareOfServices = formateNumAsPerCurrency($roundingOffTotal_fare_amount, $vCurrency);
                        if (!empty($roundingOffTotal_fare_amountArr['differenceValue']) && $roundingOffTotal_fare_amountArr['differenceValue'] != 0) {
                            $vehiclePriceTypeArr[$currentPriceTypeArrCount + 2]['Title'] = $languageLabelsArr['LBL_ROUNDING_DIFF_TXT'];
                            $vehiclePriceTypeArr[$currentPriceTypeArrCount + 2]['Amount'] = $rounding_diff;
                            $vehiclePriceTypeArr[$currentPriceTypeArrCount + 2]['Qty'] = "";
                            $vehiclePriceTypeArr[$currentPriceTypeArrCount + 2]['vVehicleCategory'] = "";
                            $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Title'] = $languageLabelsArr['LBL_ROUNDING_DIFF_TXT'];
                            $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Amount'] = $rounding_diff;
                            $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Qty'] = "";
                            $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['vVehicleCategory'] = "";
                            $currentPriceTypeArrCountCubex = count($vehiclePriceTypeArrCubex);

                            $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['eDisplaySeperator'] = 'Yes';
                            $currentPriceTypeArrCountCubex++;

                            $vehiclePriceTypeArr[$currentPriceTypeArrCount + 3]['Title'] = $languageLabelsArr['LBL_FINAL_TOTAL'];
                            $vehiclePriceTypeArr[$currentPriceTypeArrCount + 3]['Amount'] = $totalFareOfServices;
                            $vehiclePriceTypeArr[$currentPriceTypeArrCount + 3]['Qty'] = "";
                            $vehiclePriceTypeArr[$currentPriceTypeArrCount + 3]['vVehicleCategory'] = "";
                            $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Title'] = $languageLabelsArr['LBL_FINAL_TOTAL'];
                            $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Amount'] = $totalFareOfServices;
                            $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Qty'] = "";
                            $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['vVehicleCategory'] = "";
                            $currentPriceTypeArrCountCubex = count($vehiclePriceTypeArrCubex);
                        }
                    }
                }
            }
        }
        else if ($getVehicleTypeData[$v]['eFareType'] == "Hourly") {
            $fPricePerHour = round($getVehicleTypeData[$v]['fPricePerHour'] * $vCurrencyRatio, 2);
            $totalFareOfServices = $totalFareOfServices + $fPricePerHour;
            $totalFareOfServices_orig = $totalFareOfServices;
            $fPricePerHour_formatted = formateNumAsPerCurrency($fPricePerHour, $vCurrencyPassenger);
            $vehiclePriceTypeArrItems[$currentPriceTypeArrCounti]['Title'] = $getVehicleTypeData[$v]['vVehicleType'];
            $vehiclePriceTypeArrItems[$currentPriceTypeArrCounti]['Amount'] = $fPricePerHour_formatted;
            $vehiclePriceTypeArrItems[$currentPriceTypeArrCounti]['Qty'] = " ";
            $vehiclePriceTypeArrItems[$currentPriceTypeArrCounti]['vVehicleCategory'] = $getVehicleTypeData[$v]['ParentCategoryName'];
            $vehiclePriceTypeArrItems[$currentPriceTypeArrCounti]['eFareType'] = $getVehicleTypeData[$v]['eFareType'];
            $vehiclePriceTypeArrItems[$currentPriceTypeArrCounti]['iVehicleTypeId'] = $getVehicleTypeData[$v]['iVehicleTypeId'];
            $currentPriceTypeArrCounti = $currentPriceTypeArrCounti + 1;
            $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Title'] = $languageLabelsArr['LBL_SERVICE_COST']; //$getVehicleTypeData[$v]['vVehicleType'];
            $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Amount'] = $fPricePerHour_formatted;
            $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Qty'] = " ";
            $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['vVehicleCategory'] = $getVehicleTypeData[$v]['ParentCategoryName'];
            $currentPriceTypeArrCountCubex = $currentPriceTypeArrCountCubex + 1;
            $vehiclePriceTypeArr[$currentPriceTypeArrCount]['Title'] = $languageLabelsArr['LBL_SERVICE_CHARGE_PER_HOUR'];
            $vehiclePriceTypeArr[$currentPriceTypeArrCount]['Amount'] = $fPricePerHour_formatted;
            $vehiclePriceTypeArr[$currentPriceTypeArrCount]['Qty'] = "";
            $vehiclePriceTypeArr[$currentPriceTypeArrCount]['vVehicleCategory'] = $getVehicleTypeData[$v]['ParentCategoryName'];
            $currentPriceTypeArrCount = count($vehiclePriceTypeArr);

            

            $vehiclePriceTypeArr[$currentPriceTypeArrCount]['Title'] = $languageLabelsArr['LBL_MIN_HOUR'];
            $vehiclePriceTypeArr[$currentPriceTypeArrCount]['Amount'] = $getVehicleTypeData[$v]['fMinHour'];
            $vehiclePriceTypeArr[$currentPriceTypeArrCount]['Qty'] = "";
            $vehiclePriceTypeArr[$currentPriceTypeArrCount]['vVehicleCategory'] = $getVehicleTypeData[$v]['ParentCategoryName'];
            $currentPriceTypeArrCount = count($vehiclePriceTypeArr);

            $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['eDisplaySeperator'] = 'Yes';
            $currentPriceTypeArrCountCubex++;

            $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Title'] = $languageLabelsArr['LBL_MIN_HOUR'];
            $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Amount'] = $getVehicleTypeData[$v]['fMinHour'];
            $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Qty'] = " ";
            $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['vVehicleCategory'] = $getVehicleTypeData[$v]['ParentCategoryName'];
            $currentPriceTypeArrCountCubex = count($vehiclePriceTypeArrCubex);

            $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['eDisplaySeperator'] = 'Yes';
            $currentPriceTypeArrCountCubex++;
            // added for tax
            if ($fTax1 > 0) {
                $taxamount1 = round((($getVehicleTypeData[$v]['fMinHour'] * $fPricePerHour * $fTax1) / 100), 2);
                $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Title'] = $languageLabelsArr['LBL_TAX1_TXT'] . " @ " . $fTax1 . " % ";
                $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Amount'] = formateNumAsPerCurrency($taxamount1, $vCurrencyPassenger);
                $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Qty'] = " ";
                $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['vVehicleCategory'] = $getVehicleTypeData[$v]['ParentCategoryName'];
                $currentPriceTypeArrCountCubex = count($vehiclePriceTypeArrCubex);

                $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['eDisplaySeperator'] = 'Yes';
                $currentPriceTypeArrCountCubex++;
            }
            if ($fTax2 > 0) {
                $taxamount2 = round((($getVehicleTypeData[$v]['fMinHour'] * $fPricePerHour * $fTax2) / 100), 2);
                $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Title'] = $languageLabelsArr['LBL_TAX2_TXT'] . " @ " . $fTax2 . " % ";
                $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Amount'] = formateNumAsPerCurrency($taxamount2, $vCurrencyPassenger);
                $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Qty'] = " ";
                $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['vVehicleCategory'] = $getVehicleTypeData[$v]['ParentCategoryName'];
                $currentPriceTypeArrCountCubex = count($vehiclePriceTypeArrCubex);

                $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['eDisplaySeperator'] = 'Yes';
                $currentPriceTypeArrCountCubex++;
            }
            // added for tax
            $finalprice = ($getVehicleTypeData[$v]['fMinHour'] * $fPricePerHour) + $taxamount1 + $taxamount2;
            $final_price_formatted = formateNumAsPerCurrency($finalprice, $vCurrencyPassenger);
            $vehiclePriceTypeArr[$currentPriceTypeArrCount]['Title'] = $languageLabelsArr['LBL_ESTIMATED_CHARGE'];
            $vehiclePriceTypeArr[$currentPriceTypeArrCount]['Amount'] = $final_price_formatted;
            $vehiclePriceTypeArr[$currentPriceTypeArrCount]['Qty'] = "";
            $vehiclePriceTypeArr[$currentPriceTypeArrCount]['vVehicleCategory'] = $getVehicleTypeData[$v]['ParentCategoryName'];
            $vehiclePriceTypeArr[$currentPriceTypeArrCount]['iVehicleTypeId'] = $getVehicleTypeData[$v]['iVehicleTypeId'];
            $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Title'] = $languageLabelsArr['LBL_ESTIMATED_CHARGE'];
            $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Amount'] = $final_price_formatted;
            $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Qty'] = " ";
            $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['vVehicleCategory'] = $getVehicleTypeData[$v]['ParentCategoryName'];
        }
        else if ($getVehicleTypeData[$v]['eFareType'] == "Regular") {
            $iBaseFare = round($getVehicleTypeData[$v]['iBaseFare'] * $vCurrencyRatio, 2);
            $totalFareOfServices = $totalFareOfServices + $iBaseFare;
            $iBaseFare_formatted = formateNumAsPerCurrency($iBaseFare, $vCurrencyPassenger);
            $fPricePerMin = round($getVehicleTypeData[$v]['fPricePerMin'] * $vCurrencyRatio, 2);
            //$totalFareOfServices = $totalFareOfServices + $fPricePerMin;
            $fPricePerMin_formatted = formateNumAsPerCurrency($fPricePerMin, $vCurrencyPassenger);
            if ($eUnit != "KMs" && $getVehicleTypeData[$v]['LocationUnit'] == "KMs") {
                $getVehicleTypeData[$v]['fPricePerKM'] = $getVehicleTypeData[$v]['fPricePerKM'] * 0.621371;
            }
            else if ($eUnit == "KMs" && $getVehicleTypeData[$v]['LocationUnit'] == "Miles") {
                $getVehicleTypeData[$v]['fPricePerKM'] = $getVehicleTypeData[$v]['fPricePerKM'] * 1.60934;
            }
            $fPricePerKM = round($getVehicleTypeData[$v]['fPricePerKM'] * $vCurrencyRatio, 2);
            //$totalFareOfServices = $totalFareOfServices + $fPricePerKM;
            $fPricePerKM_formatted = formateNumAsPerCurrency($fPricePerKM, $vCurrencyPassenger);
            $iMinFare = round($getVehicleTypeData[$v]['iMinFare'] * $vCurrencyRatio, 2);
            //$totalFareOfServices = $totalFareOfServices + $iMinFare;
            $iMinFare_formatted = formateNumAsPerCurrency($iMinFare, $vCurrencyPassenger);
            $vehiclePriceTypeArr[$currentPriceTypeArrCount]['Title'] = $languageLabelsArr['LBL_BASE_FARE_SMALL_TXT'];
            if ($iMinFare > $iBaseFare) {
                $totalFareOfServices = $totalFareOfServices + $iMinFare - $iBaseFare;
                $iBaseFare_formatted = $iMinFare_formatted;
                $vehiclePriceTypeArr[$currentPriceTypeArrCount]['Title'] = $languageLabelsArr['LBL_MINIMUM_FARE'];
            }
            $totalFareOfServices_orig = $totalFareOfServices;
            $vehiclePriceTypeArr[$currentPriceTypeArrCount]['Amount'] = $iBaseFare_formatted;
            $vehiclePriceTypeArr[$currentPriceTypeArrCount]['Qty'] = "";
            $vehiclePriceTypeArr[$currentPriceTypeArrCount]['vVehicleCategory'] = $getVehicleTypeData[$v]['ParentCategoryName'];
            $vehiclePriceTypeArr[$currentPriceTypeArrCount]['iVehicleTypeId'] = $getVehicleTypeData[$v]['iVehicleTypeId'];
            $currentPriceTypeArrCount = count($vehiclePriceTypeArr);

            $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['eDisplaySeperator'] = 'Yes';
            $currentPriceTypeArrCountCubex++;

            $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Title'] = $languageLabelsArr['LBL_MINIMUM_FARE'];
            $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Amount'] = $iBaseFare_formatted;
            $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Qty'] = "";
            $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['vVehicleCategory'] = $getVehicleTypeData[$v]['ParentCategoryName'];
            $currentPriceTypeArrCountCubex = count($vehiclePriceTypeArrCubex);

            $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['eDisplaySeperator'] = 'Yes';
            $currentPriceTypeArrCountCubex++;

            $vehiclePriceTypeArr[$currentPriceTypeArrCount]['Title'] = $languageLabelsArr['LBL_PRICE_PER_MINUTE'];
            $vehiclePriceTypeArr[$currentPriceTypeArrCount]['Amount'] = $fPricePerMin_formatted;
            $vehiclePriceTypeArr[$currentPriceTypeArrCount]['Qty'] = "";
            $vehiclePriceTypeArr[$currentPriceTypeArrCount]['vVehicleCategory'] = $getVehicleTypeData[$v]['ParentCategoryName'];
            $currentPriceTypeArrCount = count($vehiclePriceTypeArr);

            $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['eDisplaySeperator'] = 'Yes';
            $currentPriceTypeArrCountCubex++;

            $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Title'] = $languageLabelsArr['LBL_PRICE_PER_MINUTE'];
            $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Amount'] = $fPricePerMin_formatted;
            $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Qty'] = "";
            $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['vVehicleCategory'] = $getVehicleTypeData[$v]['ParentCategoryName'];
            $currentPriceTypeArrCountCubex = count($vehiclePriceTypeArrCubex);

            $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['eDisplaySeperator'] = 'Yes';
            $currentPriceTypeArrCountCubex++;

            $vehiclePriceTypeArr[$currentPriceTypeArrCount]['Title'] = $eUnit == "KMs" ? $languageLabelsArr['LBL_PRICE_PER_KM'] : $languageLabelsArr['LBL_PRICE_PER_MILES'];
            $vehiclePriceTypeArr[$currentPriceTypeArrCount]['Amount'] = $fPricePerKM_formatted;
            $vehiclePriceTypeArr[$currentPriceTypeArrCount]['Qty'] = "";
            $vehiclePriceTypeArr[$currentPriceTypeArrCount]['vVehicleCategory'] = $getVehicleTypeData[$v]['ParentCategoryName'];
            $currentPriceTypeArrCount = count($vehiclePriceTypeArr);

            $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['eDisplaySeperator'] = 'Yes';
            $currentPriceTypeArrCountCubex++;

            $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Title'] = $eUnit == "KMs" ? $languageLabelsArr['LBL_PRICE_PER_KM'] : $languageLabelsArr['LBL_PRICE_PER_MILES'];
            $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Amount'] = $fPricePerKM_formatted;
            $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Qty'] = "";
            $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['vVehicleCategory'] = $getVehicleTypeData[$v]['ParentCategoryName'];
            $currentPriceTypeArrCountCubex = count($vehiclePriceTypeArrCubex);

            $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['eDisplaySeperator'] = 'Yes';
            $currentPriceTypeArrCountCubex++;

            $surge_data = checkSurgePrice($getVehicleTypeData[$v]['iVehicleTypeId']);
            if ($surge_data['Action'] == 0) {
                $surgeamount = round(($totalFareOfServices * $surge_data['SurgePriceValue']), 2) - $totalFareOfServices;
                $totalFareOfServices += $surgeamount;
                $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Title'] = $languageLabelsArr['LBL_SURGE'] . ' ' . $surge_data['SurgePrice'];
                $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Amount'] = formateNumAsPerCurrency($surgeamount, $vCurrencyPassenger);
                $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Qty'] = " ";
                $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['vVehicleCategory'] = $getVehicleTypeData[$v]['ParentCategoryName'];
                $currentPriceTypeArrCountCubex = count($vehiclePriceTypeArrCubex);

                $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['eDisplaySeperator'] = 'Yes';
                $currentPriceTypeArrCountCubex++;
            }
            // added for tax
            if ($fTax1 > 0) {
                $taxamount1 = round((($totalFareOfServices * $fTax1) / 100), 2);
                $totalFareOfServices = $totalFareOfServices + $taxamount1;
                $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Title'] = $languageLabelsArr['LBL_TAX1_TXT'] . " @ " . $fTax1 . " % ";
                $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Amount'] = formateNumAsPerCurrency($taxamount1, $vCurrencyPassenger);
                $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Qty'] = " ";
                $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['vVehicleCategory'] = $getVehicleTypeData[$v]['ParentCategoryName'];
                $currentPriceTypeArrCountCubex = count($vehiclePriceTypeArrCubex);

                $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['eDisplaySeperator'] = 'Yes';
                $currentPriceTypeArrCountCubex++;
            }
            if ($fTax2 > 0) {
                $taxamount2 = round((($totalFareOfServices * $fTax2) / 100), 2);
                $totalFareOfServices = $totalFareOfServices + $taxamount2;
                $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Title'] = $languageLabelsArr['LBL_TAX2_TXT'] . " @ " . $fTax2 . " % ";
                $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Amount'] = formateNumAsPerCurrency($taxamount2, $vCurrencyPassenger);
                $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Qty'] = " ";
                $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['vVehicleCategory'] = $getVehicleTypeData[$v]['ParentCategoryName'];
                $currentPriceTypeArrCountCubex = count($vehiclePriceTypeArrCubex);

                $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['eDisplaySeperator'] = 'Yes';
                $currentPriceTypeArrCountCubex++;
            }
            // added for tax
            //$finalpriceformated = $totalFareOfServices + $taxamount1 + $taxamount2;
            $finalpriceformated = $totalFareOfServices;
            $final_price_formatted = formateNumAsPerCurrency($finalpriceformated, $vCurrencyPassenger);
            $vehiclePriceTypeArr[$currentPriceTypeArrCount]['Title'] = $languageLabelsArr['LBL_ESTIMATED_CHARGE'];
            $vehiclePriceTypeArr[$currentPriceTypeArrCount]['Amount'] = $final_price_formatted;
            $vehiclePriceTypeArr[$currentPriceTypeArrCount]['Qty'] = "";
            $vehiclePriceTypeArr[$currentPriceTypeArrCount]['vVehicleCategory'] = $getVehicleTypeData[$v]['ParentCategoryName'];
            $vehiclePriceTypeArr[$currentPriceTypeArrCount]['iVehicleTypeId'] = $getVehicleTypeData[$v]['iVehicleTypeId'];
            $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Title'] = $languageLabelsArr['LBL_ESTIMATED_CHARGE'];
            $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Amount'] = $final_price_formatted;
            $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['Qty'] = "";
            $vehiclePriceTypeArrCubex[$currentPriceTypeArrCountCubex]['vVehicleCategory'] = $getVehicleTypeData[$v]['ParentCategoryName'];
            $vehiclePriceTypeArrItems[$currentPriceTypeArrCounti]['Title'] = $getVehicleTypeData[$v]['vVehicleType'];
            $vehiclePriceTypeArrItems[$currentPriceTypeArrCounti]['Amount'] = '';
            $vehiclePriceTypeArrItems[$currentPriceTypeArrCounti]['Qty'] = " ";
            $vehiclePriceTypeArrItems[$currentPriceTypeArrCounti]['vVehicleCategory'] = $getVehicleTypeData[$v]['ParentCategoryName'];
            $vehiclePriceTypeArrItems[$currentPriceTypeArrCounti]['eFareType'] = $getVehicleTypeData[$v]['eFareType'];
            $vehiclePriceTypeArrItems[$currentPriceTypeArrCounti]['iVehicleTypeId'] = $getVehicleTypeData[$v]['iVehicleTypeId'];
            if ($eForVideoConsultation == "Yes") {
                $vehiclePriceTypeArr[$currentPriceTypeArrCount]['Amount'] = "";
                $vehiclePriceTypeArr[$currentPriceTypeArrCount]['Qty'] = "";
                $vehiclePriceTypeArrItems[$currentPriceTypeArrCounti]['Amount'] = "";
                $vehiclePriceTypeArrItems[$currentPriceTypeArrCounti]['Qty'] = "";
            }
            $currentPriceTypeArrCounti = $currentPriceTypeArrCounti + 1;
        }
    }

    $returnArr['eFareTypeServices'] = $eFareTypeServices;
    $returnArr['tripFareDetailsArr'] = $vehiclePriceTypeArr;
    $returnArr['vehiclePriceTypeArrItems'] = $vehiclePriceTypeArrItems;
    $returnArr['vehiclePriceTypeArrCubex'] = $vehiclePriceTypeArrCubex;
    $returnArr['tripFareDetailsSaveArr']['FareData'] = $vehiclePriceTypeSaveArr;
    $returnArr['tripFareDetailsSaveArr']['subTotal'] = $totalFareOfServices;
    $returnArr['tripFareDetailsSaveArr']['originalTotalCommissionOfServices'] = $totalCommissionOfServices;
    $returnArr['tripFareDetailsSaveArr']['originalFareTotal'] = round(($totalFareOfServices_orig / $vCurrencyRatio), 2);
    $returnArr['tripFareDetailsSaveArr']['eFareTypeServices'] = $eFareTypeServices;
    // echo "<pre>"; print_r($returnArr);die;
    return $returnArr;
}

############################################################## Display Trip Charge To Driver For UberX Trip ####################################################################################
function DisplayTripChargeForUberX($TripID)
{
    global $obj, $tconfig, $SERVICE_PROVIDER_FLOW;
    $returnArr = array();
    $where = " iTripId = '" . $TripID . "'";
    //Added By HJ On 13-06-2020 For Optimization trips Table Query Start
    if (isset($tripDetailsArr["trips_" . $TripID])) {
        $tripData = $tripDetailsArr["trips_" . $TripID];
    }
    else {
        $tripData = $obj->MySQLSelect("SELECT * from trips WHERE iTripId = '" . $TripID . "'");
        $tripDetailsArr["trips_" . $TripID] = $tripData;
    }
    //Added By HJ On 13-06-2020 For Optimization trips Table Query End
    // echo "<pre>"; print_r($tripData); die;
    $eType = $tripData[0]['eType'];
    if ($eType == "UberX") {
        if ($SERVICE_PROVIDER_FLOW == "Provider" && isset($tripData[0]['tVehicleTypeFareData']) && $tripData[0]['tVehicleTypeFareData'] != "" && $tripData[0]['eFareType'] == 'Fixed') {
            $userData = $obj->MySQLSelect("SELECT rd.vCurrencyDriver, rd.vLang, cu.vSymbol, cu.Ratio, co.eUnit FROM register_driver as rd, currency as cu, country as co WHERE rd.iDriverId='" . $tripData[0]['iDriverId'] . "' AND cu.vName = rd.vCurrencyDriver AND co.vCountryCode = rd.vCountry");
            $priceRatio = $userData[0]['Ratio'];
            $vSymbol = $userData[0]['vSymbol'];
            $vCurrencyDriver = $userData[0]['vCurrencyDriver'];
            $tVehicleTypeFareData = (array)json_decode($tripData[0]['tVehicleTypeFareData']);
            $tVehicleTypeFareData = (array)$tVehicleTypeFareData['FareData'];
            $totalFareOfServices = 0;
            for ($fd = 0; $fd < count($tVehicleTypeFareData); $fd++) {
                $eAllowQty = $tVehicleTypeFareData[$fd]->eAllowQty;
                $typeQty = $tVehicleTypeFareData[$fd]->qty;
                $typeamountcal = $tVehicleTypeFareData[$fd]->amount * $priceRatio;
                $typeAmount = formateNumAsPerCurrency($typeamountcal, $vCurrencyDriver);
                if ($typeQty < 1) {
                    $typeQty = 1;
                }
                $amountOfService = $tVehicleTypeFareData[$fd]->amount;
                $amountOfService = $amountOfService * $typeQty;
                $totalFareOfServices = $totalFareOfServices + $amountOfService;
            }
            /*--------------------- total fare SystemDefault--------------------*/
            $returnArr['TotalFareUberXSystemDefault'] = $totalFareOfServices;
            /*--------------------- total fare SystemDefault--------------------*/
            $totalFareOfServices = $totalFareOfServices * $priceRatio;
            $totalFareOfServices = round($totalFareOfServices, 2);
            $returnArr['TotalFareUberX'] = formateNumAsPerCurrency($totalFareOfServices, $vCurrencyDriver);
            $returnArr['TotalFareUberXValue'] = $totalFareOfServices;
            $returnArr['UberXFareCurrencySymbol'] = $vSymbol;
            return $returnArr;
        }
        $iDriverVehicleId = $tripData[0]['iDriverVehicleId'];
        $iVehicleTypeId = $tripData[0]['iVehicleTypeId'];
        $fVisitFee = $tripData[0]['fVisitFee'];
        $startDate = $tripData[0]['tStartDate'];
        $endDateOfTrip = $tripData[0]['tEndDate'];
        $iQty = $tripData[0]['iQty'];
        $destination_lat = $tripData[0]['tEndLat'];
        $destination_lon = $tripData[0]['tEndLong'];
        //$endDateOfTrip=@date("Y-m-d H:i:s");
        /* $iVehicleCategoryId=get_value('vehicle_type', 'iVehicleCategoryId', 'iVehicleTypeId',$iVehicleTypeId,'','true');
        $iParentId = get_value('vehicle_category', 'iParentId', 'iVehicleCategoryId', $iVehicleCategoryId,'','true'); */
        $sql = "SELECT vc.iParentId from vehicle_category as vc LEFT JOIN vehicle_type as vt ON vc.iVehicleCategoryId=vt.iVehicleCategoryId WHERE vt.iVehicleTypeId = '" . $iVehicleTypeId . "'";
        $VehicleCategoryData = $obj->MySQLSelect($sql);
        $iParentId = $VehicleCategoryData[0]['iParentId'];
        if ($iParentId == 0) {
            $ePriceType = get_value('vehicle_category', 'ePriceType', 'iVehicleCategoryId', $iVehicleCategoryId, '', 'true');
        }
        else {
            $ePriceType = get_value('vehicle_category', 'ePriceType', 'iVehicleCategoryId', $iParentId, '', 'true');
        }
        //$ePriceType=get_value('vehicle_category', 'ePriceType', 'iVehicleCategoryId',$iVehicleCategoryId,'','true');
        $ALLOW_SERVICE_PROVIDER_AMOUNT = $ePriceType == "Provider" ? "Yes" : "No";
        if ($tripData[0]['eFareType'] == 'Hourly') {
            $sql22 = "SELECT * FROM `trip_times` WHERE iTripId='$TripID'";
            $db_tripTimes = $obj->MySQLSelect($sql22);
            $totalSec = 0;
            $iTripTimeId = '';
            foreach ($db_tripTimes as $dtT) {
                if ($dtT['dPauseTime'] != '' && $dtT['dPauseTime'] != '0000-00-00 00:00:00') {
                    $totalSec += strtotime($dtT['dPauseTime']) - strtotime($dtT['dResumeTime']);
                }
            }
            $totalTimeInMinutes_trip = @round(abs($totalSec) / 60, 2);
        }
        else {
            $totalTimeInMinutes_trip = @round(abs(strtotime($startDate) - strtotime($endDateOfTrip)) / 60, 2);
        }
        $totalHour = $totalTimeInMinutes_trip / 60;
        $tripDistance = calcluateTripDistance($TripID);
        $sourcePointLatitude = $tripData[0]['tStartLat'];
        $sourcePointLongitude = $tripData[0]['tStartLong'];
        if ($totalTimeInMinutes_trip <= 1) {
            $FinalDistance = $tripDistance;
        }
        else {
            $FinalDistance = getDistanceInfoFromGoogleDirections($tripDistance, $sourcePointLatitude, $sourcePointLongitude, $destination_lat, $destination_lon);
        }
        $tripDistance = $FinalDistance;
        $fPickUpPrice = $tripData[0]['fPickUpPrice'];
        $fNightPrice = $tripData[0]['fNightPrice'];
        $eFareType = get_value('trips', 'eFareType', 'iTripId', $TripID, '', 'true');
        $surgePrice = $fPickUpPrice > 1 ? $fPickUpPrice : ($fNightPrice > 1 ? $fNightPrice : 1);
        $fAmount = 0;
        $Fare_data = getVehicleCostData("vehicle_type", $iVehicleTypeId);
        // echo "<pre>"; print_r($tripData); die;
        $fPricePerKM = getVehicleCountryUnit_PricePerKm($iVehicleTypeId, $Fare_data[0]['fPricePerKM']);
        $Minute_Fare = $Fare_data[0]['fPricePerMin'] * $totalTimeInMinutes_trip;
        $Distance_Fare = $fPricePerKM * $tripDistance;
        $iBaseFare = $Fare_data[0]['iBaseFare'];
        $total_fare = $iBaseFare + $Minute_Fare + $Distance_Fare;
        $fSurgePriceDiff = (($total_fare * $surgePrice) - $total_fare);
        $total_fare = $total_fare + $fSurgePriceDiff;
        $iMinFare = round($Fare_data[0]['iMinFare'] * $surgePrice, 2);
        if ($iMinFare > $total_fare) {
            $total_fare = $iMinFare;
        }
        $fMinHour = $Fare_data[0]['fMinHour'];
        if ($totalHour > $fMinHour) {
            $miniminutes = $fMinHour * 60;
            $TripTimehours = $totalTimeInMinutes_trip / 60;
            $tothours = intval($TripTimehours);
            $extrahours = $TripTimehours - $tothours;
            $extraminutes = $extrahours * 60;
        }
        if ($ALLOW_SERVICE_PROVIDER_AMOUNT == "Yes") {
            $sqlServicePro = "SELECT * FROM `service_pro_amount` WHERE iDriverVehicleId='" . $iDriverVehicleId . "' AND iVehicleTypeId='" . $iVehicleTypeId . "'";
            $serviceProData = $obj->MySQLSelect($sqlServicePro);
            if (count($serviceProData) > 0) {
                $fAmount = $serviceProData[0]['fAmount'];
                if ($eFareType == "Fixed") {
                    $fAmount = $fAmount * $iQty;
                }
                else if ($eFareType == "Hourly") {
                    if ($totalHour > $fMinHour) {
                        $fTimeSlot = $Fare_data[0]['fTimeSlot'];
                        $extraprice = 0;
                        if ($fTimeSlot > 0) {
                            $pricetimeslot = 60 / $fTimeSlot;
                            $pricepertimeslot = $fAmount / $pricetimeslot;
                            $fTimeSlotPrice = $pricepertimeslot;
                            $extraprice = 0;
                            if ($fTimeSlot > 0) {
                                $extratimeslot = ceil($extraminutes / $fTimeSlot);
                                $extraprice = $extratimeslot * $fTimeSlotPrice;
                            }
                            else if ($extraminutes > 0) {
                                $extraprice = ($fAmount / 60) * $extraminutes;
                            }
                        }
                        $fAmount = ($fAmount * $tothours) + $extraprice;
                    }
                    else {
                        $fAmount = $fAmount * $fMinHour;
                    }
                    //$fAmount = $fAmount * $totalHour;
                }
                else {
                    $fAmount = $total_fare;
                }
            }
            else {
                if ($eFareType == "Fixed") {
                    $fAmount = round($Fare_data[0]['fFixedFare'] * $iQty, 2);
                }
                else if ($eFareType == "Hourly") {
                    if ($totalHour > $fMinHour) {
                        $fTimeSlot = $Fare_data[0]['fTimeSlot'];
                        $pricetimeslot = 60 / $fTimeSlot;
                        $pricepertimeslot = $Fare_data[0]['fPricePerHour'] / $pricetimeslot;
                        $fTimeSlotPrice = $pricepertimeslot;
                        //$fTimeSlotPrice = $Fare_data[0]['fTimeSlotPrice'];
                        $extraprice = 0;
                        if ($fTimeSlot > 0) {
                            $extratimeslot = ceil($extraminutes / $fTimeSlot);
                            $extraprice = $extratimeslot * $fTimeSlotPrice;
                        }
                        else if ($extraminutes > 0) {
                            $extraprice = ($Fare_data[0]['fPricePerHour'] / 60) * $extraminutes;
                        }
                        $fAmount = round((($Fare_data[0]['fPricePerHour'] * $tothours) + $extraprice), 2);
                    }
                    else {
                        $fAmount = round($Fare_data[0]['fPricePerHour'] * $fMinHour, 2);
                        // $fAmount = round($Fare_data[0]['fPricePerHour'] * $totalHour,2);
                    }
                }
                else {
                    $fAmount = $total_fare;
                }
            }
        }
        else {
            if ($eFareType == "Fixed") {
                $fAmount = round($Fare_data[0]['fFixedFare'] * $iQty, 2);
            }
            else if ($eFareType == "Hourly") {
                if ($totalHour > $fMinHour) {
                    $fTimeSlot = $Fare_data[0]['fTimeSlot'];
                    $pricetimeslot = 60 / $fTimeSlot;
                    $pricepertimeslot = $Fare_data[0]['fPricePerHour'] / $pricetimeslot;
                    $fTimeSlotPrice = $pricepertimeslot;
                    //$fTimeSlotPrice = $Fare_data[0]['fTimeSlotPrice'];
                    $extraprice = 0;
                    if ($fTimeSlot > 0) {
                        $extratimeslot = ceil($extraminutes / $fTimeSlot);
                        $extraprice = $extratimeslot * $fTimeSlotPrice;
                    }
                    else if ($extraminutes > 0) {
                        $extraprice = ($Fare_data[0]['fPricePerHour'] / 60) * $extraminutes;
                    }
                    $fAmount = round((($Fare_data[0]['fPricePerHour'] * $tothours) + $extraprice), 2);
                }
                else {
                    $fAmount = round($Fare_data[0]['fPricePerHour'] * $fMinHour, 2);
                    //$fAmount = round($Fare_data[0]['fPricePerHour'] * $totalHour,2);
                }
            }
            else {
                $fAmount = $total_fare;
            }
        }
        $final_display_charge = $fAmount + $fVisitFee;
        $returnArr['Action'] = "1";
        /*--------------------- total fare SystemDefault--------------------*/
        $returnArr['TotalFareUberXSystemDefault'] = $final_display_charge;
        /*--------------------- total fare SystemDefault--------------------*/
        $vCurrencyDriver = get_value('register_driver', 'vCurrencyDriver', 'iDriverId', $tripData[0]['iDriverId'], '', 'true');
        $currencySymbolRationDriver = get_value('currency', 'vSymbol,Ratio', 'vName', $vCurrencyDriver);
        $currencySymbol = $currencySymbolRationDriver[0]['vSymbol'];
        $currencyRationDriver = $currencySymbolRationDriver[0]['Ratio'];
        $final_display_charge = $final_display_charge * $currencyRationDriver;
        $final_display_charge = round($final_display_charge, 2);
        //$final_display_charge = formatNum($final_display_charge);
        $returnArr['TotalFareUberX'] = formateNumAsPerCurrency($final_display_charge, $vCurrencyDriver);
        $returnArr['TotalFareUberXValue'] = $final_display_charge;
        $returnArr['UberXFareCurrencySymbol'] = $currencySymbol;
    }
    else {
        $returnArr['TotalFareUberX'] = $returnArr['TotalFareUberXValue'] = $returnArr['UberXFareCurrencySymbol'] = "";
    }
    return $returnArr;
}

############################################################## Display Trip Charge To Driver For UberX Trip ####################################################################################
//Added By HJ On 24-01-2019 For Check Driver Vehicle Service Available Or Not Start
function getServiceProviderVehicleDataOld($driverVehicles, $iVehicleTypeIds)
{
    global $obj;
    //echo "<pre>";
    $mainArr = array();
    for ($d = 0; $d < count($driverVehicles); $d++) {
        $carTypeArr = explode(",", $driverVehicles[$d]['vCarType']);
        $driverId = $driverVehicles[$d]['iDriverId'];
        $explodeTypeIds = explode(",", $iVehicleTypeIds);
        for ($t = 0; $t < count($explodeTypeIds); $t++) {
            $resultStatusArr = array();
            //if ($driverId == "117") {
            $getVehicleType = $obj->MySQLSelect("SELECT iVehicleTypeId,iVehicleCategoryId FROM vehicle_type WHERE `iVehicleCategoryId`='" . $explodeTypeIds[$t] . "' AND eStatus='Active'");
            $typeArr = array();
            for ($v = 0; $v < count($getVehicleType); $v++) {
                $iVehicleCategoryId = $getVehicleType[$v]['iVehicleCategoryId'];
                $iVehicleTypeId = $getVehicleType[$v]['iVehicleTypeId'];
                $typeArr[$iVehicleCategoryId][] = $iVehicleTypeId;
            }
            foreach ($typeArr as $key => $value) {
                $result = !empty(array_intersect($value, $carTypeArr));
                //echo $result;die;
                $foundArr = array();
                for ($r = 0; $r < count($value); $r++) {
                    $typeId = $value[$r];
                    if (in_array($typeId, $carTypeArr)) {
                        $foundArr[] = 1;
                    }
                }
                $resultStatusArr[] = $result;
                //echo $result."<br>";
                /* if (in_array(1, $foundArr)) {
                  $resultStatusArr[] = 1;
                  } else {
                  $resultStatusArr[] = 0;
              } */
            }
            if (in_array(1, $resultStatusArr)) {
                $mainArr[] = 1;
            }
            else {
                $mainArr[] = 0;
            }
        }
    }
    //echo "<pre>";print_r($mainArr);die;
    $status = "Success";
    if (in_array(0, $mainArr)) {
        $status = "Failed";
    }
    return $status;
}

function getServiceProviderVehicleData($driverVehicles, $iVehicleTypeIds)
{
    global $obj;
    
    $mainArr = array();
    for ($d = 0; $d < count($driverVehicles); $d++) {
        $carTypeArr = array();
        if(isset($driverVehicles[$d]['vCarType'])) {
            $carTypeArr = explode(",", $driverVehicles[$d]['vCarType']);
        }

        $explodeTypeIds = explode(",", $iVehicleTypeIds);

        $result = array_intersect($explodeTypeIds, $carTypeArr);

        if (!empty($result) && count($result) > 0) {
            $mainArr[] = 1;
        }
        else {
            $mainArr[] = 0;
        }
        
    }
    
    $status = "Success";
    if (in_array(0, $mainArr)) {
        $status = "Failed";
    }
    return $status;
}

//Added By HJ On 24-01-2019 For Check Driver Vehicle Service Available Or Not End
function getVideoThumbImageProvider($video_file)
{
    global $tconfig;
    $tmpArr = explode(".", $video_file);
    for ($i = 0; $i < count($tmpArr) - 1; $i++) {
        $tmpArr1[] = $tmpArr[$i];
    }
    $file = implode("_", $tmpArr1);
    $thumb_img = $file . '.png';
    if (!is_dir($tconfig["tsite_upload_provider_image_path"] . '/thumnails/')) {
        mkdir($tconfig["tsite_upload_provider_image_path"] . '/thumnails/', 0777);
        chmod($tconfig["tsite_upload_provider_image_path"] . '/thumnails/', 0777);
    }
    $img_path = $tconfig["tsite_upload_provider_image_path"] . '/thumnails/' . $thumb_img;
    $img_url = $tconfig["tsite_upload_provider_image"] . '/thumnails/' . $thumb_img;
    if (file_exists($img_path)) {
        return $img_url;
    }
    else {
        require_once $tconfig['tpanel_path'] . 'assets/libraries/FFMpeg/autoload.php';
        $sec = 3;
        $vFile = $tconfig["tsite_upload_provider_image_path"] . '/' . $video_file;
        $img_url = "";
        if (file_exists($vFile)) {
            $ffprobe = FFMpeg\FFProbe::create();
            $vDuration = $ffprobe->streams($vFile)->videos()->first()->get('duration');
            if ($vDuration < 3) {
                $sec = floor($vDuration);
            }
            $ffmpeg = FFMpeg\FFMpeg::create();
            $video = $ffmpeg->open($vFile);
            $frame = $video->frame(FFMpeg\Coordinate\TimeCode::fromSeconds($sec));
            $frame->save($img_path);
        }
        return $img_url;
    }
}

function getServiceCategoriesUberX($Data, $lang)
{
    global $obj, $MODULES_OBJ, $VIDEO_CONSULT_OBJ, $BIDDING_OBJ, $languageLabelsArrCubeX, $tconfig, $CONFIG_OBJ;
    $DataNewArr = array();
    $ssql = "";
    if($CONFIG_OBJ->isOnlyCashPaymentModeAvailable()) {
        $ssql = " AND eType != 'VideoConsult' ";
    }
    $master_service_categories = $obj->MySQLSelect("SELECT *, JSON_UNQUOTE(JSON_EXTRACT(vCategoryName, '$.vCategoryName_" . $lang . "')) as vCategoryName FROM master_service_category WHERE eStatus = 'Active' $ssql");
    foreach ($master_service_categories as $mServiceCategory) {
        $mServiceCategoryArr = array();
        $mServiceCategoryArr['vTitle'] = $mServiceCategory['vCategoryName'];
        $mServiceCategoryArr['eShowType'] = 'Header';
        $mServiceCategoryHeader = $mServiceCategoryArr;
        // $DataNewArr[] = $mServiceCategoryArr;
        $mServiceCategoryArr['vIconImage'] = "";
        $service_banner_path = "";
        if (!empty($mServiceCategory['vIconImage1']) && file_exists($tconfig["tsite_upload_app_home_screen_images_path"] . $mServiceCategory['vIconImage1'])) {
            $mServiceCategoryArr['vIconImage'] = $tconfig["tsite_upload_app_home_screen_images"] . $mServiceCategory['vIconImage1'];
            $service_banner_path = $tconfig["tsite_upload_app_home_screen_images_path"] . $mServiceCategory['vIconImage1'];
        }
        $service_banner = $mServiceCategoryArr['vIconImage'];
        $vImageWidth = $vImageHeight = "";
        if (!empty($service_banner)) {
            $imagedata = getimagesize($service_banner_path);
            $vImageWidth = strval($imagedata[0]);
            $vImageHeight = strval($imagedata[1]);
        }
        if ($mServiceCategory['eType'] != "UberX") {

            /*$DataNewArr[] = array(
                'vCategoryName' => $mServiceCategory['vCategoryName'], 'vCategory' => $mServiceCategory['vCategoryName'], 'vBannerImage' => $service_banner, 'vImageWidth' => $vImageWidth, 'vImageHeight' => $vImageHeight, 'eShowType' => 'Banner', 'vBgColor' => '#f0f7f5'
            );*/

            $mServiceCategoryBanner = array(
                'vCategoryName' => $mServiceCategory['vCategoryName'], 'vCategory' => $mServiceCategory['vCategoryName'], 'vBannerImage' => $service_banner, 'vImageWidth' => $vImageWidth, 'vImageHeight' => $vImageHeight, 'eShowType' => 'Banner', 'vBgColor' => '#f0f7f5'
            );
        }
        $mServiceCategoryArr = array();
        $mServiceCategoryArr['vCategoryName'] = "";
        $mServiceCategoryArr['vCategory'] = "";
        $mServiceCategoryArr['eShowType'] = "Grid";
        $mServiceCategoryArr['eCatType'] = "ServiceProvider";
        $mServiceCategoryArr['eType'] = $mServiceCategory['eType'];
        if ($mServiceCategory['eType'] == "UberX") {
            $mServiceCategoryArr['vBgColor'] = "#ffffff";
        }
        elseif ($mServiceCategory['eType'] == "VideoConsult") {
            $mServiceCategoryArr['vBgColor'] = "#f0f7f5";
            $mServiceCategoryArr['VideoConsultSection'] = "Yes";
        }
        elseif ($mServiceCategory['eType'] == "Bidding") {
            $mServiceCategoryArr['vBgColor'] = "#f0f7f5";
            $mServiceCategoryArr['biddingSection'] = "Yes";
            $mServiceCategoryArr['eCatType'] = "Bidding";
        }
        $mServiceCategoryArr['vIconImage'] = $mServiceCategoryArr['vBgImage'] = "";
        if (!empty($mServiceCategory['vIconImage']) && file_exists($tconfig["tsite_upload_app_home_screen_images_path"] . $mServiceCategory['vIconImage'])) {
            $mServiceCategoryArr['vIconImage'] = $tconfig["tsite_upload_app_home_screen_images"] . $mServiceCategory['vIconImage1'];
        }
        $mServiceCategoryArr['SubCategories'] = array();
        foreach ($Data as $skey => $SubCategories) {
            $Data[$skey]['eShowType'] = "Icon";
            $Data[$skey]['vLogo_image'] = $Data[$skey]['vListLogo'];
            $Data[$skey]['isVideoConsultEnable'] = "No";
            if ($MODULES_OBJ->isEnableVideoConsultingService()) {
                $Data[$skey]['isVideoConsultEnable'] = $VIDEO_CONSULT_OBJ->checkVideoConsultEnable($SubCategories['iVehicleCategoryId']);
            }
            if ($mServiceCategory['eType'] == "UberX" && in_array($SubCategories['eCatType'], ['ServiceProvider'])) {
                $Data[$skey]['eCatViewType'] = "List";
                $mServiceCategoryArr['SubCategories'][] = $Data[$skey];
            }
            elseif ($mServiceCategory['eType'] == "VideoConsult" && $MODULES_OBJ->isEnableVideoConsultingService()) {
                if ($Data[$skey]['isVideoConsultEnable'] == "Yes") {
                    $Data[$skey]['eCatViewType'] = "List";
                    $mServiceCategoryArr['SubCategories'][] = $Data[$skey];
                }
            }
        }

        if ($mServiceCategory['eType'] == "Bidding" && $MODULES_OBJ->isEnableBiddingServices()) {
            $mServiceCategoryArr['SubCategories'] = $BIDDING_OBJ->getBiddingMaster('webservice', '', '', '', $lang);
        }
        if ((($mServiceCategory['eType'] == "UberX" && $MODULES_OBJ->isUberXFeatureAvailable()) || ($mServiceCategory['eType'] == "VideoConsult" && $MODULES_OBJ->isEnableVideoConsultingService()) || ($mServiceCategory['eType'] == "Bidding" && $MODULES_OBJ->isEnableBiddingServices())) && count($mServiceCategoryArr['SubCategories']) > 0) {
            $DataNewArr[] = $mServiceCategoryHeader;
            if ($mServiceCategory['eType'] != "UberX") {
                $DataNewArr[] = $mServiceCategoryBanner;
            }
            $DataNewArr[] = $mServiceCategoryArr;
        }
    }
    return $DataNewArr;
}

function getServiceCategoriesProSP($Data, $lang)
{
    global $obj, $MODULES_OBJ, $VIDEO_CONSULT_OBJ, $BIDDING_OBJ, $languageLabelsArr, $tconfig, $CONFIG_OBJ, $master_service_category_tbl;
    $DataNewArr = array();
    $ssql = "";
    if($CONFIG_OBJ->isOnlyCashPaymentModeAvailable() || !$MODULES_OBJ->isEnableVideoConsultingService()) {
        $ssql = " AND eType != 'VideoConsult' ";
    }
    if(!$MODULES_OBJ->isEnableBiddingServices()) {
        $ssql = " AND eType != 'Bidding' ";
    }
    $master_service_categories = $obj->MySQLSelect("SELECT *, JSON_UNQUOTE(JSON_EXTRACT(vCategoryName, '$.vCategoryName_" . $lang . "')) as vCategoryName FROM $master_service_category_tbl WHERE eStatus = 'Active' $ssql ORDER BY iDisplayOrder");

    foreach ($master_service_categories as $mServiceCategory) {
        $iListMaxCount = $mServiceCategory['iListMaxCount'];

        $mServiceCategoryArr = array();
        $mServiceCategoryArr['vTitle'] = $mServiceCategory['vCategoryName'];
        $mServiceCategoryArr['eViewType'] = 'TitleView';
        $mServiceCategoryArr['AddBottomPadding'] = "No";
        $DataNewArr[] = $mServiceCategoryArr;

        if($mServiceCategory['eType'] != "UberX" && !empty($mServiceCategory['vIconImage1']) && file_exists($tconfig["tsite_upload_app_home_screen_images_path"] . $mServiceCategory['vIconImage1'])) {
            $mServiceCategoryArr = array();
            $mServiceCategoryArr['eViewType'] = 'BannerView';
            $mServiceCategoryArr['isScroll'] = "No";
            $mServiceCategoryArr['displayCount'] = "1";
            $mServiceCategoryArr['isFullView'] = "No";
            $mServiceCategoryArr['isOnlyImage'] = "Yes";
            $mServiceCategoryArr['AddTopPadding'] = "Yes";
            $mServiceCategoryArr['AddBottomPadding'] = "No";

            $bannerArr = array(
                'vImage' => $tconfig["tsite_upload_app_home_screen_images"] . $mServiceCategory['vIconImage1'],
                'isClickable' => 'No',
            );

            $imagedata = getimagesize($tconfig["tsite_upload_app_home_screen_images_path"] . $mServiceCategory['vIconImage1']);

            $bannerArr['vImageWidth'] = strval($imagedata[0]);
            $bannerArr['vImageHeight'] = strval($imagedata[1]);
            $mServiceCategoryArr['vImageWidth'] = $bannerArr['vImageWidth'];
            $mServiceCategoryArr['vImageHeight'] = $bannerArr['vImageHeight'];
            $mServiceCategoryArr['imagesArr'][] = $bannerArr;

            $DataNewArr[] = $mServiceCategoryArr;
        }

        $mServiceCategoryArr = array();
        $mServiceCategoryArr['eViewType'] = "GridView";

        if ($mServiceCategory['eType'] == "Bidding" && $MODULES_OBJ->isEnableBiddingServices()) {
            $biddingServicesArr = $BIDDING_OBJ->getBiddingMaster('webservice', '', '', '', $lang);
            foreach($biddingServicesArr as $bKey => $bidService) {
                $biddingServicesArr[$bKey]['vCategoryName'] = $bidService['vCategory'];
                $biddingServicesArr[$bKey]['vImage'] = $bidService['vLogo_image'];
            }
            $mServiceCategoryArr['servicesArr'] = $biddingServicesArr;
        } else {
            $serviceDataArr = array();
            foreach ($Data as $skey => $SubCategories) {
                $Data[$skey]['eShowType'] = "Icon";
                $Data[$skey]['vImage'] = $Data[$skey]['vListLogo'];
                $Data[$skey]['vCategoryName'] = $Data[$skey]['vCategory'];
                $Data[$skey]['isVideoConsultEnable'] = "No";
                if ($MODULES_OBJ->isEnableVideoConsultingService() && $mServiceCategory['eType'] == "VideoConsult") {
                    $Data[$skey]['isVideoConsultEnable'] = $VIDEO_CONSULT_OBJ->checkVideoConsultEnable($SubCategories['iVehicleCategoryId']);
                }
                if ($mServiceCategory['eType'] == "UberX" && in_array($SubCategories['eCatType'], ['ServiceProvider'])) {
                    $Data[$skey]['eCatViewType'] = "List";
                    $serviceDataArr[] = $Data[$skey];
                }
                elseif ($mServiceCategory['eType'] == "VideoConsult" && $MODULES_OBJ->isEnableVideoConsultingService()) {
                    if ($Data[$skey]['isVideoConsultEnable'] == "Yes") {
                        $Data[$skey]['eCatViewType'] = "List";
                        $serviceDataArr[] = $Data[$skey];
                    }
                }
            }

            $mServiceCategoryArr['servicesArr'] = $serviceDataArr;
        }

        $mServiceCategoryArr['ListMaxCount'] = $iListMaxCount;
        $mServiceCategoryArr['isShowMore'] = "No";
        if(count($mServiceCategoryArr['servicesArr']) > $iListMaxCount) {
            $serviceDataArrNew = array_slice($mServiceCategoryArr['servicesArr'], 0, $iListMaxCount - 1);
            $serviceDataMoreArr = array(
                'vCategoryName' => $languageLabelsArr['LBL_MORE'],
                'vImage' => $tconfig['tsite_url'] . 'webimages/icons/DefaultImg/ic_more_services_sp.png',
                'SubCategories' => array_slice($mServiceCategoryArr['servicesArr'], $iListMaxCount - 1)
            );

            $serviceDataArrNew[] = $serviceDataMoreArr;
            $mServiceCategoryArr['servicesArr'] = $serviceDataArrNew;
        }

        $DataNewArr[] = $mServiceCategoryArr;
    }
    return $DataNewArr;
}

function getServiceTypeDetails($iVehicleCategoryId, $iDriverId, $iVehicleTypeId) {
    global $obj, $userDetailsArr, $LANG_OBJ, $currencyAssociateArr, $country_data_arr, $vSystemDefaultCurrencyName, $APP_TYPE;

    $languageCode = "";
    if ($iDriverId > 0) {
        if (isset($userDetailsArr["register_driver_" . $iDriverId])) {
            $driverData = $userDetailsArr["register_driver_" . $iDriverId];
        }
        else {
            $driverData = $obj->MySQLSelect("SELECT *,iDriverId as iMemberId FROM register_driver WHERE iDriverId='" . $iDriverId . "' ");
            $userDetailsArr["register_driver_" . $iDriverId] = $driverData;
        }
        $languageCode = $driverData[0]['vLang'];
    }
    if ($languageCode == "" || $languageCode == NULL) {
        $languageCode = $LANG_OBJ->FetchDefaultLangData("vCode");
    }

    //$db_driverdetail = $obj->MySQLSelect("SELECT * FROM `register_driver` where iDriverId ='" . $iDriverId . "'");
    $vCountry = $driverData[0]['vCountry'];
    $vCurrencyDriver = $driverData[0]['vCurrencyDriver'];
    $iDriverVehicleId = $driverData[0]['iDriverVehicleId'];
    $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($languageCode, "1", $iServiceId);
    $lbl_all = $languageLabelsArr['LBL_ALL'];
    $ssql = "";
    $getLocationData = $obj->MySQLSelect("SELECT * FROM location_master");
    if ($vCountry != "") {
        if (isset($country_data_arr[$vCountry])) {
            $iCountryId = $country_data_arr[$vCountry]['iCountryId'];
        }
        else {
            $iCountryId = get_value('country', 'iCountryId', 'vCountryCode', $vCountry, '', 'true');
        }

        $db_country = array();
        for ($d = 0; $d < count($getLocationData); $d++) {
            if ($getLocationData[$d]['eStatus'] == "Active" && $getLocationData[$d]['iCountryId'] == $iCountryId && $getLocationData[$d]['eFor'] == "VehicleType") {
                $db_country[] = $getLocationData[$d];
            }
        }

        $country_str = "-1";
        if (count($db_country) > 0) {
            for ($i = 0; $i < count($db_country); $i++) {
                $country_str .= "," . $db_country[$i]['iLocationId'];
            }
        }
        $ssql .= " AND iLocationid IN ($country_str) ";
    }
    $vehicleDetail = $obj->MySQLSelect("SELECT iVehicleTypeId, vVehicleType_" . $languageCode . " as vTitle,eFareType,eAllowQty,iMaxQty,fFixedFare,fPricePerHour,iLocationid,fMinHour from vehicle_type where iVehicleCategoryId in($iVehicleCategoryId) AND eStatus = 'Active' AND iVehicleTypeId = '$iVehicleTypeId' " . $ssql . " ORDER BY iDisplayOrder");

    if ($vCurrencyDriver == "" || $vCurrencyDriver == NULL) {
        if (!empty($vSystemDefaultCurrencyName)) {
            $vCurrencyDriver = $vSystemDefaultCurrencyName;
        }
        else {
            $vCurrencyDriver = get_value('currency', 'vName', 'eDefault', 'Yes', '', 'true');
        }
    }

    if (isset($currencyAssociateArr[$vCurrencyDriver])) {
        $vCurrencyData = array();
        $vCurrencyData[] = $currencyAssociateArr[$vCurrencyDriver];
    }
    else {
        $vCurrencyData = get_value('currency', 'vSymbol, Ratio', 'vName', $vCurrencyDriver);
    }
    $vCurrencySymbol = $vCurrencyData[0]['vSymbol'];
    $vCurrencyRatio = $vCurrencyData[0]['Ratio'];

    $getVehicleCatData = $obj->MySQLSelect("SELECT iParentId,ePriceType,iVehicleCategoryId,eVideoConsultEnable,eVideoConsultServiceCharge,eVideoServiceDescription FROM vehicle_category");
    $iParentId = 0;
    $vehicleCatDataArr = array();
    for ($c = 0; $c < count($getVehicleCatData); $c++) {
        $vehicleCatDataArr[$getVehicleCatData[$c]['iVehicleCategoryId']] = $getVehicleCatData[$c];
    }
    if (isset($vehicleCatDataArr[$iVehicleCategoryId])) {
        $iParentId = $vehicleCatDataArr[$iVehicleCategoryId]['iParentId'];
        $eVideoConsultEnable = $vehicleCatDataArr[$iVehicleCategoryId]['eVideoConsultEnable'];
        $eVideoServiceDescription = $vehicleCatDataArr[$iVehicleCategoryId]['eVideoServiceDescription'];
    }

    if ($iParentId == 0) {
        if (isset($vehicleCatDataArr[$iVehicleCategoryId])) {
            $ePriceType = $vehicleCatDataArr[$iVehicleCategoryId]['ePriceType'];
        }
    }
    else {
        if (isset($vehicleCatDataArr[$iParentId])) {
            $ePriceType = $vehicleCatDataArr[$iParentId]['ePriceType'];
        }
    }

    if ($APP_TYPE == "Ride-Delivery-UberX") {
        $result = $obj->MySQLSelect("SELECT iDriverVehicleId FROM `driver_vehicle` WHERE iDriverId = '" . $iDriverId . "' AND eType = 'UberX' ORDER BY iDriverVehicleId DESC LIMIT 0,1");
        $iDriverVehicleId = $result[0]['iDriverVehicleId'];
    }


    $ReqServices = $obj->MySQLSelect('SELECT iVehicleCategoryId FROM driver_service_request WHERE iDriverId = "' . $iDriverId . '" ');
    $requestedServices = array();
    foreach ($ReqServices as $key => $ReqService) {
        $requestedServices[] = $ReqService['iVehicleCategoryId'];
    }

    $db_vCarType = $obj->MySQLSelect("SELECT vCarType FROM `driver_vehicle` where iDriverId ='" . $iDriverId . "' AND iDriverVehicleId = '" . $iDriverVehicleId . "'");
    if (count($db_vCarType) > 0) {
        $vehicle_service_id = explode(",", $db_vCarType[0]['vCarType']);

        for ($l = 0; $l < count($getLocationData); $l++) {
            $locationDataArr[$getLocationData[$l]['iLocationId']] = $getLocationData[$l]['vLocationName'];
        }

        $db_serviceproviderid = $obj->MySQLSelect("SELECT * FROM `service_pro_amount` where iDriverVehicleId ='" . $iDriverVehicleId . "'");
        $serviceProDataArr = array();
        for ($s = 0; $s < count($db_serviceproviderid); $s++) {
            $serviceProDataArr[$db_serviceproviderid[$s]['iVehicleTypeId']][] = $db_serviceproviderid[$s];
        }

        for ($i = 0; $i < count($vehicleDetail); $i++) {
            $db_serviceproviderid = array();
            if (isset($serviceProDataArr[$vehicleDetail[$i]['iVehicleTypeId']])) {
                $db_serviceproviderid = $serviceProDataArr[$vehicleDetail[$i]['iVehicleTypeId']];
            }

            if (count($db_serviceproviderid) > 0) {
                $vehicleDetail[$i]['fAmount'] = strval($db_serviceproviderid[0]['fAmount']);
            }
            else {
                if ($vehicleDetail[$i]['eFareType'] == "Hourly") {
                    $vehicleDetail[$i]['fAmount'] = strval($vehicleDetail[$i]['fPricePerHour']);
                }
                else {
                    $vehicleDetail[$i]['fAmount'] = strval($vehicleDetail[$i]['fFixedFare']);
                }
            }

            $fAmount = round($vehicleDetail[$i]['fAmount'] * $vCurrencyRatio, 2);
            $vehicleDetail[$i]['fAmount'] = strval($fAmount);
            $vehicleDetail[$i]['fAmountWithSymbol'] = formateNumAsPerCurrency($fAmount, $vCurrencyDriver);
            $vehicleDetail[$i]['fFixedFareWithSymbol'] = formateNumAsPerCurrency($vehicleDetail[$i]['fFixedFare'], $vCurrencyDriver);
            $vehicleDetail[$i]['fPricePerHourWithSymbol'] = formateNumAsPerCurrency($vehicleDetail[$i]['fPricePerHour'], $vCurrencyDriver);
            $vehicleDetail[$i]['ePriceType'] = $ePriceType;
            $vehicleDetail[$i]['vCurrencySymbol'] = $vCurrencySymbol;
            $data_service[$i] = $vehicleDetail[$i];
            if (in_array($data_service[$i]['iVehicleTypeId'], $vehicle_service_id)) {
                $vehicleDetail[$i]['VehicleServiceStatus'] = 'true';
            }
            else {
                $vehicleDetail[$i]['VehicleServiceStatus'] = 'false';
            }

            if ($APP_TYPE == 'UberX' || $APP_TYPE == 'Ride-Delivery-UberX') {
                if (in_array($data_service[$i]['iVehicleTypeId'], $vehicle_service_id)) {
                    $vehicleDetail[$i]['eServiceRequest'] = 'Active';
                }
                else if (in_array($data_service[$i]['iVehicleTypeId'], $requestedServices)) {
                    $vehicleDetail[$i]['eServiceRequest'] = 'Pending';
                }
                else {
                    $vehicleDetail[$i]['eServiceRequest'] = 'Inactive';
                }
                $vehicleDetail[$i]['VehicleServiceId'] = $data_service[$i]['iVehicleTypeId'];
            }
            if ($vehicleDetail[$i]['iLocationid'] == "-1") {
                $vehicleDetail[$i]['SubTitle'] = $lbl_all;
            }
            else {
                $vLocationName = "";
                if (isset($locationDataArr[$vehicleDetail[$i]['iLocationid']])) {
                    $vLocationName = $locationDataArr[$vehicleDetail[$i]['iLocationid']];
                }
                $vehicleDetail[$i]['SubTitle'] = $locationname[0]['vLocationName'];
            }
        }
    }

    return $vehicleDetail;
}

############################################ Functions added ############################################
if ($type == "getServiceCategoryTypes") {
    // // Commented By HJ On 22-07-2020 Bcoz Not Required
    $iVehicleCategoryId = isset($_REQUEST['iVehicleCategoryId']) ? clean($_REQUEST['iVehicleCategoryId']) : 0;
    $vLatitude = isset($_REQUEST["vLatitude"]) ? $_REQUEST["vLatitude"] : '';
    $vLongitude = isset($_REQUEST["vLongitude"]) ? $_REQUEST["vLongitude"] : '';
    $userId = isset($_REQUEST['userId']) ? clean($_REQUEST['userId']) : '';
    $eCheck = isset($_REQUEST['eCheck']) ? clean($_REQUEST['eCheck']) : 'No';
    $pickuplocationarr = array($vLatitude, $vLongitude);
    $GetVehicleIdfromGeoLocation = FetchVehicleTypeFromGeoLocation($pickuplocationarr);
    if ($eCheck == "" || $eCheck == NULL) {
        $eCheck = "No";
    }
    if ($eCheck == "Yes") {
        //$allowed_ans = checkRestrictedAreaNew($pickuplocationarr,"No");
        $allowed_ans = checkAreaRestriction($pickuplocationarr, "No");
        if ($allowed_ans == "Yes") {
            $sql23 = "SELECT iVehicleTypeId FROM `vehicle_type` WHERE iLocationid IN ($GetVehicleIdfromGeoLocation) AND iVehicleCategoryId = '" . $iVehicleCategoryId . "' ORDER BY iDisplayOrder ASC";
            $vehicleTypes = $obj->MySQLSelect($sql23);
            if (count($vehicleTypes) > 0) {
                $returnArr['Action'] = "1";
            }
            else {
                $returnArr['Action'] = "0";
                $returnArr['message'] = "LBL_NO_SERVICES_AVAIL_FOR_JOB_LOC";
            }
        }
        else {
            $returnArr['Action'] = "0";
            $returnArr['message'] = "LBL_JOB_LOCATION_NOT_ALLOWED";
        }
        setDataResponse($returnArr);
    }
    else {
        if ($userId != "") {
            $sql1 = "SELECT vLang,vCurrencyPassenger FROM `register_user` WHERE iUserId='$userId'";
            $row = $obj->MySQLSelect($sql1);
            $lang = $row[0]['vLang'];
            if ($lang == "" || $lang == NULL) {
                //$lang = get_value('language_master', 'vCode', 'eDefault', 'Yes', '', 'true');
                //Added By HJ On 22-07-2020 For Optimize language_master Table Query Start
                $lang = $LANG_OBJ->FetchDefaultLangData("vCode");
                //Added By HJ On 22-07-2020 For Optimize language_master Table Query End
            }
            $vCurrencyPassenger = $row[0]['vCurrencyPassenger'];
            if ($vCurrencyPassenger == "" || $vCurrencyPassenger == NULL) {
                $vCurrencyPassenger = get_value('currency', 'vName', 'eDefault', 'Yes', '', 'true');
            }
            $UserCurrencyData = get_value('currency', 'vSymbol, Ratio', 'vName', $vCurrencyPassenger);
            $priceRatio = $UserCurrencyData[0]['Ratio'];
            $vSymbol = $UserCurrencyData[0]['vSymbol'];
            $vehicleCategoryData = get_value('vehicle_category', "vCategoryTitle_" . $lang . " as vCategoryTitle, tCategoryDesc_" . $lang . " as tCategoryDesc", 'iVehicleCategoryId', $iVehicleCategoryId);
            $vCategoryTitle = $vehicleCategoryData[0]['vCategoryTitle'];
            $vCategoryDesc = $vehicleCategoryData[0]['tCategoryDesc'];
            $sql2 = "SELECT vc.iVehicleCategoryId, vc.iParentId,vc.vCategory_" . $lang . " as vCategory, vc.ePriceType, vt.iVehicleTypeId, vt.vVehicleType_" . $lang . " as vVehicleType, vt.eFareType, vt.fFixedFare, vt.fPricePerHour, vt.fPricePerKM, vt.fPricePerMin, vt.iBaseFare,vt.fCommision, vt.iMinFare,vt.iPersonSize, vt.vLogo as vVehicleTypeImage, vt.eType, vt.eIconType, vt.eAllowQty, vt.fMinHour, vt.iMaxQty, vt.iVehicleTypeId, fFixedFare FROM vehicle_category as vc LEFT JOIN vehicle_type AS vt ON vt.iVehicleCategoryId = vc.iVehicleCategoryId WHERE vc.eStatus='Active'  AND vt.eStatus='Active' AND vt.iVehicleCategoryId='$iVehicleCategoryId' AND vt.iLocationid IN ($GetVehicleIdfromGeoLocation) ORDER BY vt.iDisplayOrder ASC";
            //AND vt.eType='UberX'
            $Data = $obj->MySQLSelect($sql2);
            if (!empty($Data)) {
                for ($i = 0; $i < count($Data); $i++) {
                    $Data[$i]['fFixedFare_value'] = round($Data[$i]['fFixedFare'] * $priceRatio, 2);
                    $fFixedFare = round($Data[$i]['fFixedFare'] * $priceRatio, 2);
                    $Data[$i]['fFixedFare'] = formateNumAsPerCurrency($fFixedFare, $vCurrencyPassenger);
                    $Data[$i]['fPricePerHour_value'] = round($Data[$i]['fPricePerHour'] * $priceRatio, 2);
                    $fPricePerHour = round($Data[$i]['fPricePerHour'] * $priceRatio, 2);
                    $Data[$i]['fPricePerHour'] = formateNumAsPerCurrency($fPricePerHour, $vCurrencyPassenger);
                    $Data[$i]['fPricePerKM'] = getVehicleCountryUnit_PricePerKm($Data[$i]['iVehicleTypeId'], $Data[$i]['fPricePerKM'], $userId, "Passenger");
                    $fPricePerKM = round($Data[$i]['fPricePerKM'] * $priceRatio, 2);
                    $Data[$i]['fPricePerKM'] = formateNumAsPerCurrency($fPricePerKM, $vCurrencyPassenger);
                    $fPricePerMin = round($Data[$i]['fPricePerMin'] * $priceRatio, 2);
                    $Data[$i]['fPricePerMin'] = formateNumAsPerCurrency($fPricePerMin, $vCurrencyPassenger);
                    $iBaseFare = round($Data[$i]['iBaseFare'] * $priceRatio, 2);
                    //$Data[$i]['iBaseFare'] = $vSymbol . formatNum($iBaseFare);
                    $Data[$i]['iBaseFare'] = formateNumAsPerCurrency($iBaseFare, $vCurrencyPassenger);
                    $fCommision = round($Data[$i]['fCommision'] * $priceRatio, 2);
                    //$Data[$i]['fCommision'] = $vSymbol . formatNum($fCommision);
                    $Data[$i]['fCommision'] = formateNumAsPerCurrency($fCommision, $vCurrencyPassenger);
                    $iMinFare = round($Data[$i]['iMinFare'] * $priceRatio, 2);
                    //$Data[$i]['iMinFare'] = $vSymbol . formatNum($iMinFare);
                    $Data[$i]['iMinFare'] = formateNumAsPerCurrency($iMinFare, $vCurrencyPassenger);
                    $Data[$i]['vSymbol'] = $vSymbol;
                    $Data[$i]['vCategoryTitle'] = $vCategoryTitle;
                    $Data[$i]['vCategoryDesc'] = $vCategoryDesc;
                    $Data[$i]['vCategoryShortDesc'] = strip_tags($vCategoryDesc);
                    $iParentId = $Data[$i]['iParentId'];
                    if ($iParentId == 0) {
                        $ePriceType = $Data[$i]['ePriceType'];
                    }
                    else {
                        $ePriceType = get_value('vehicle_category', 'ePriceType', 'iVehicleCategoryId', $iParentId, '', 'true');
                    }
                    $Data[$i]['ePriceType'] = $ePriceType;
                    $Data[$i]['ALLOW_SERVICE_PROVIDER_AMOUNT'] = $ePriceType == "Provider" ? "Yes" : "No";
                    //$Data[$i]['ALLOW_SERVICE_PROVIDER_AMOUNT']= $Data[$i]['ePriceType'] == "Provider"? "Yes" :"No";
                }
                $returnArr['Action'] = "1";
                $returnArr['message'] = $Data;
                //$returnArr['ALLOW_SERVICE_PROVIDER_AMOUNT'] = $ALLOW_SERVICE_PROVIDER_AMOUNT;
                $returnArr['vCategoryTitle'] = $vCategoryTitle;
                $returnArr['vCategoryDesc'] = $vCategoryDesc;
            }
            else {
                $returnArr['Action'] = "0";
                $returnArr['message'] = "LBL_NO_DATA_AVAIL";
            }
        }
        else {
            $returnArr['Action'] = "0";
            $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
        }
    }
    setDataResponse($returnArr);
}
##########################################################################
if ($type == "getBanners") {
    $iMemberId = isset($_REQUEST['iMemberId']) ? clean($_REQUEST['iMemberId']) : '';
    $iServiceId = isset($_REQUEST['iServiceId']) ? clean($_REQUEST['iServiceId']) : '';
    $eCatType = isset($_REQUEST['eCatType']) ? clean($_REQUEST['eCatType']) : '';
    if ($iMemberId != "") {
        $vLanguage = get_value('register_user', 'vLang', 'iUserId', $iMemberId, '', 'true');
        if ($vLanguage == "" || $vLanguage == NULL) {
            //$vLanguage = get_value('language_master', 'vCode', 'eDefault', 'Yes', '', 'true');
            //Added By HJ On 22-07-2020 For Optimize language_master Table Query Start
            $vLanguage = $LANG_OBJ->FetchDefaultLangData("vCode");
            //Added By HJ On 22-07-2020 For Optimize language_master Table Query End
        }
        //$banners = get_value('banners', 'vImage', 'vCode', $vLanguage, ' ORDER BY iDisplayOrder ASC');
        $ssql = "";
        if ($iServiceId != "") {
            $ssql = " AND iServiceId = '" . $iServiceId . "' ";
        }
        $sql = "SELECT vImage FROM `banners` WHERE vCode = '" . $vLanguage . "' AND eStatus = 'Active' AND iServiceId = '" . $iServiceId . "' eType = 'General' ORDER BY iDisplayOrder ASC";
        if (in_array($eCatType, ['Genie', 'Runner', 'Anywhere']) && $MODULES_OBJ->isEnableAnywhereDeliveryFeature()) {
            if ($eCatType == "Genie" || $eCatType == "Anywhere") {
                $eCatType = "Genie";
            }
            $sql = "SELECT vImage FROM `banners` WHERE vCode = '" . $vLanguage . "' AND eStatus = 'Active' AND eType = '" . $eCatType . "' ORDER BY iDisplayOrder ASC";
        }
        $banners = $obj->MySQLSelect($sql);
        $data = array();
        $count = 0;
        for ($i = 0; $i < count($banners); $i++) {
            if ($banners[$i]['vImage'] != "") {
                $data[$count]['vImage'] = $tconfig["tsite_url"] . 'assets/img/images/' . $banners[$i]['vImage'];
                $count++;
            }
        }
        $returnArr['Action'] = "1";
        $returnArr['message'] = $data;
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
    }
    setDataResponse($returnArr);
}
#########################################################################
if ($type == "getvehicleCategory") {
    $iDriverId = isset($_REQUEST['iDriverId']) ? $_REQUEST['iDriverId'] : '';
    $page = isset($_REQUEST['page']) ? trim($_REQUEST['page']) : 1;
    $iVehicleCategoryId = isset($_REQUEST['iVehicleCategoryId']) ? trim($_REQUEST['iVehicleCategoryId']) : 0;
    if(empty($iVehicleCategoryId)) {
        $iVehicleCategoryId = "0";
    }
    $languageCode = "";
    if ($iDriverId != "") {
        //Added By HJ On 22-06-2020 For Optimization register_driver Table Query Start
        if (isset($userDetailsArr["register_driver_" . $iDriverId])) {
            $driverData = $userDetailsArr["register_driver_" . $iDriverId];
        }
        else {
            $driverData = $obj->MySQLSelect("SELECT *,iDriverId as iMemberId FROM register_driver WHERE iDriverId='" . $iDriverId . "' ");
            $userDetailsArr["register_driver_" . $iDriverId] = $driverData;
        }
        $languageCode = $driverData[0]['vLang'];
        //Added By HJ On 22-06-2020 For Optimization register_driver Table Query End
        //$languageCode = get_value('register_driver', 'vLang', 'iDriverId', $iDriverId, '', 'true');
    }
    if ($languageCode == "" || $languageCode == NULL) {
        //$languageCode = get_value('language_master', 'vCode', 'eDefault', 'Yes', '', 'true');
        //Added By HJ On 22-07-2020 For Optimize language_master Table Query Start
        $languageCode = $LANG_OBJ->FetchDefaultLangData("vCode");
        //Added By HJ On 22-07-2020 For Optimize language_master Table Query End
    }
    $ssql_category = "";
    $returnName = "vTitle";
    if ($iVehicleCategoryId != "" && ($iVehicleCategoryId == 0 || $iVehicleCategoryId == "0")) {
        $ssql_category = " and (select count(iVehicleCategoryId) from vehicle_category where iParentId=vc.iVehicleCategoryId AND eCatType='ServiceProvider' AND eStatus='Active') > 0";
        $returnName = "vCategory";
    }

    $isEnableVideoConsultingService = $MODULES_OBJ->isEnableVideoConsultingService();

    $per_page = 200;
    $sql_all = "SELECT COUNT(iVehicleCategoryId) As TotalIds FROM vehicle_category as vc WHERE vc.eStatus='Active' AND vc.eCatType='ServiceProvider' AND vc.iParentId='" . $iVehicleCategoryId . "'" . $ssql_category;
    $data_count_all = $obj->MySQLSelect($sql_all);
    $TotalPages = ceil($data_count_all[0]['TotalIds'] / $per_page);
    $start_limit = ($page - 1) * $per_page;
    $limit = " LIMIT " . $start_limit . ", " . $per_page;
    $sql = "SELECT vc.iVehicleCategoryId, vc.vCategory_" . $languageCode . " as '" . $returnName . "', vc.vLogo, vc.eVideoConsultEnable FROM vehicle_category as vc WHERE vc.eStatus='Active' AND vc.eCatType='ServiceProvider' AND vc.iParentId='" . $iVehicleCategoryId . "'" . $ssql_category . " ORDER BY iDisplayOrder " . $limit;
    $vehicleCategoryDetail = $obj->MySQLSelect($sql);
    $vehicleCategoryData = array();
    if (count($vehicleCategoryDetail) > 0) {
        //Added By HJ On 11-07-2019 For Get Vehicle Type Data Start
        $Data3 = $obj->MySQLSelect("SELECT iVehicleTypeId,iVehicleCategoryId FROM vehicle_type WHERE eStatus='Active' ORDER BY iDisplayOrder ASC");
        $categoryArr = array();
        for ($vc = 0; $vc < count($Data3); $vc++) {
            $categoryArr[$Data3[$vc]['iVehicleCategoryId']][] = $Data3[$vc];
        }
        //Added By HJ On 11-07-2019 For Get Vehicle Type Data End
        //Added By HJ On 06-08-2019 For Check Vehicle Category's Vehicle Type Exists Or Not Start Discuss with KS Sir Start
        /* for ($sd = 0; $sd < count($vehicleCategoryDetail); $sd++) {
          $iVehicleCategoryId = $vehicleCategoryDetail[$sd]['iVehicleCategoryId'];
          //print_r($categoryArr[$iVehicleCategoryId]);die;
          if (empty($categoryArr[$iVehicleCategoryId])) {
          unset($vehicleCategoryDetail[$sd]);
          }
          }
          $vehicleCategoryDetail = array_values($vehicleCategoryDetail); */
        //Added By HJ On 06-08-2019 For Check Vehicle Category's Vehicle Type Exists Or Not Start Discuss with KS Sir End
        //echo "<pre>";print_R($categoryArr);die;
        $vehicleCategoryData = $vehicleCategoryDetail;
        if ($iVehicleCategoryId != "" && ($iVehicleCategoryId == 0 || $iVehicleCategoryId == "0")) {
            $i = 0;
            //Added By HJ On 22-07-2020 For Optimize vehicle_category Table Query Start
            $subCategoryData = $obj->MySQLSelect("SELECT vCategory_" . $languageCode . " as vTitle,iParentId,iVehicleCategoryId, vLogo,vLogo2,eVideoConsultEnable,eVideoConsultServiceCharge FROM `vehicle_category` WHERE eCatType='ServiceProvider' AND eStatus='Active'");
            $vehicleCatDataArr = array();
            for ($h = 0; $h < count($subCategoryData); $h++) {
                $vehicleCatDataArr[$subCategoryData[$h]['iParentId']][] = $subCategoryData[$h];
            }
            ///echo "<pre>";print_r($vehicleCatDataArr);die;
            //Added By HJ On 22-07-2020 For Optimize vehicle_category Table Query End
            while (count($vehicleCategoryDetail) > $i) {
                $iVehicleCategoryId = $vehicleCategoryDetail[$i]['iVehicleCategoryId'];
                //Added By HJ On 22-07-2020 For Optimize vehicle_category Table Query Start
                //$subCategoryData = $obj->MySQLSelect("SELECT vCategory_" . $languageCode . " as vTitle,iVehicleCategoryId, vLogo FROM `vehicle_category` WHERE iParentId='" . $iVehicleCategoryId . "' AND eCatType='ServiceProvider' AND eStatus='Active'");             
                $subCategoryData = array();
                if (isset($vehicleCatDataArr[$iVehicleCategoryId])) {
                    $subCategoryData = $vehicleCatDataArr[$iVehicleCategoryId];
                }
                //echo "<pre>";print_r($subCategoryData);die;
                //Added By HJ On 22-07-2020 For Optimize vehicle_category Table Query End
                //Added By HJ On 11-07-2019 For Check Category's Service Exists Or Not Start
                $subCatArr = array();
                for ($d = 0; $d < count($subCategoryData); $d++) {
                    //print_r($subCategoryData);die;
                    if($MODULES_OBJ->isEnableAppHomeScreenLayoutV3()) {
                        $subCategoryData[$d]['vLogo'] = $subCategoryData[$d]['vLogo2'];
                    } else {
                        $subCategoryData[$d]['vLogo'] = $subCategoryData[$d]['vLogo '];
                    }
                    $subCategoryData[$d]['vLogo_image'] = $tconfig['tsite_upload_images_vehicle_category'] . '/' . $subCategoryData[$d]['iVehicleCategoryId'] . '/android/' . $subCategoryData[$d]['vLogo'];
                    $subCategoryData[$d]['vLogo_BG_color'] = RANDOM_COLORS_ARR[array_rand(RANDOM_COLORS_ARR, 1)];
                    $subCategoryData[$d]['vLogo_TINT_color'] = "#FFFFFF";
                    $subCategoryData[$d]['eVideoConsultEnable'] = $isEnableVideoConsultingService ? $subCategoryData[$d]['eVideoConsultEnable'] : "No";
                    if ($isEnableVideoConsultingService) {
                        $video_consult_data = $VIDEO_CONSULT_OBJ->getServiceDetails($iDriverId, $subCategoryData[$d]['iVehicleCategoryId']);
                        $subCategoryData[$d]['eVideoConsultEnableProvider'] = $video_consult_data['eVideoConsultEnableProvider'];
                        if ($video_consult_data['eVideoConsultServiceCharge'] > 0) {
                            $subCategoryData[$d]['eVideoConsultServiceCharge'] = formateNumAsPerCurrency($video_consult_data['eVideoConsultServiceCharge'], $driverData[0]['vCurrencyDriver']);
                        }
                        else {
                            $subCategoryData[$d]['eVideoConsultServiceCharge'] = formateNumAsPerCurrency($subCategoryData[$d]['eVideoConsultServiceCharge'], $driverData[0]['vCurrencyDriver']);
                        }
                    }
                    $serviceArr = array();
                    if (isset($categoryArr[$subCategoryData[$d]['iVehicleCategoryId']])) {
                        $serviceArr = $categoryArr[$subCategoryData[$d]['iVehicleCategoryId']];
                    }
                    if (count($serviceArr) > 0 || ($isEnableVideoConsultingService && $subCategoryData[$d]['eVideoConsultEnable'] == 'Yes')) {
                        $subCatArr[] = $subCategoryData[$d];
                    }
                }
                if (count($subCatArr) > 0 || $isEnableVideoConsultingService) {
                    $vehicleCategoryData[$i]['SubCategory'] = $subCatArr;
                }
                else {
                    unset($vehicleCategoryData[$i]);
                }
                //Added By HJ On 11-07-2019 For Check Category's Service Exists Or Not End
                $i++;
            }
        }
        else {
            //Added By HJ On 30-10-2020 For New UFX Design As Per Discuss with KS Sir Start
            for ($h = 0; $h < count($vehicleCategoryData); $h++) {
                $vehicleCategoryData[$h]['eVideoConsultEnable'] = $isEnableVideoConsultingService ? $subCategoryData[$d]['eVideoConsultEnable'] : "No";
                $vehicleCategoryData[$h]['vLogo_image'] = $tconfig['tsite_upload_images_vehicle_category'] . '/' . $vehicleCategoryData[$h]['iVehicleCategoryId'] . '/android/' . $vehicleCategoryData[$h]['vLogo'];
                $vehicleCategoryData[$h]['vLogo_BG_color'] = RANDOM_COLORS_ARR[array_rand(RANDOM_COLORS_ARR, 1)];
                $vehicleCategoryData[$h]['vLogo_TINT_color'] = "#FFFFFF";
            }
            //Added By HJ On 30-10-2020 For New UFX Design As Per Discuss with KS Sir End
        }

        /* Added by HV on 28-05-2021 for searching services */
        $search_keyword = isset($_REQUEST['search_keyword']) ? $_REQUEST['search_keyword'] : "";
        if (!empty($search_keyword) && $MODULES_OBJ->isEnableSearchUfxServices()) {
            foreach ($vehicleCategoryData as $key => $value) {
                $main_cat = $subcat = 0;
                if ((isset($value['vCategory']) && stripos($value['vCategory'], $search_keyword) !== false) || (isset($value['vTitle']) && stripos($value['vTitle'], $search_keyword) !== false && $parent_ufx_catid > 0)) {
                    $main_cat = 1;
                }
                if (isset($value['SubCategory']) && $main_cat == 0) {
                    foreach ($value['SubCategory'] as $skey => $sCategory) {
                        if (stripos($sCategory['vTitle'], $search_keyword) !== false) {
                            $subcat = 1;
                        }
                        else {
                            unset($vehicleCategoryData[$key]['SubCategory'][$skey]);
                        }
                    }
                    if (!empty($vehicleCategoryData[$key]['SubCategory'])) {
                        $vehicleCategoryData[$key]['SubCategory'] = array_values($vehicleCategoryData[$key]['SubCategory']);
                    }
                }
                
                if (($main_cat == 0 && $subcat == 0) || (empty($vehicleCategoryData[$key]['SubCategory']) && $parent_ufx_catid == 0)) {
                    unset($vehicleCategoryData[$key]);
                }
            }
        }
        /* Added by HV on 28-05-2021 for searching services End */
        if (count($vehicleCategoryData) > 0) {
            $returnArr['Action'] = "1";
            if ($TotalPages > $page) {
                $returnArr['NextPage'] = "" . ($page + 1);
            }
            else {
                $returnArr['NextPage'] = "0";
            }
            $returnArr['message'] = array_values($vehicleCategoryData);
        }
        else {
            $returnArr['Action'] = "0";
            $returnArr['message'] = "LBL_NO_SERVICE_AVAIL";
        }
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_NO_SERVICE_AVAIL";
    }
    setDataResponse($returnArr);
}
###########################################################################
if ($type == "getServiceTypes") {
    $iVehicleCategoryId = isset($_REQUEST['iVehicleCategoryId']) ? $_REQUEST['iVehicleCategoryId'] : '';
    $iDriverId = isset($_REQUEST['iDriverId']) ? $_REQUEST['iDriverId'] : '';
    $languageCode = "";
    if ($iDriverId > 0) {
        //Added By HJ On 22-06-2020 For Optimization register_driver Table Query Start
        if (isset($userDetailsArr["register_driver_" . $iDriverId])) {
            $driverData = $userDetailsArr["register_driver_" . $iDriverId];
        }
        else {
            $driverData = $obj->MySQLSelect("SELECT *,iDriverId as iMemberId FROM register_driver WHERE iDriverId='" . $iDriverId . "' ");
            $userDetailsArr["register_driver_" . $iDriverId] = $driverData;
        }
        $languageCode = $driverData[0]['vLang'];
        //Added By HJ On 22-06-2020 For Optimization register_driver Table Query End
        //$languageCode = get_value('register_driver', 'vLang', 'iDriverId', $iDriverId, '', 'true');
    }
    if ($languageCode == "" || $languageCode == NULL) {
        //$languageCode = get_value('language_master', 'vCode', 'eDefault', 'Yes', '', 'true');
        //Added By HJ On 22-07-2020 For Optimize language_master Table Query Start
        $languageCode = $LANG_OBJ->FetchDefaultLangData("vCode");
        //Added By HJ On 22-07-2020 For Optimize language_master Table Query End
    }
    //$db_driverdetail = $obj->MySQLSelect("SELECT * FROM `register_driver` where iDriverId ='" . $iDriverId . "'");
    $vCountry = $driverData[0]['vCountry'];
    $vCurrencyDriver = $driverData[0]['vCurrencyDriver'];
    $iDriverVehicleId = $driverData[0]['iDriverVehicleId'];
    $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($languageCode, "1", $iServiceId);
    $lbl_all = $languageLabelsArr['LBL_ALL'];
    $ssql = "";
    $getLocationData = $obj->MySQLSelect("SELECT * FROM location_master");
    if ($vCountry != "") {
        //Added By HJ On 22-07-2020 For Optimize country Table Query Start
        if (isset($country_data_arr[$vCountry])) {
            $iCountryId = $country_data_arr[$vCountry]['iCountryId'];
        }
        else {
            $iCountryId = get_value('country', 'iCountryId', 'vCountryCode', $vCountry, '', 'true');
        }
        //Added By HJ On 22-07-2020 For Optimize country Table Query End
        //Added By HJ On 22-07-2020 For Optimize language_master Table Query Start
        $db_country = array();
        for ($d = 0; $d < count($getLocationData); $d++) {
            if ($getLocationData[$d]['eStatus'] == "Active" && $getLocationData[$d]['iCountryId'] == $iCountryId && $getLocationData[$d]['eFor'] == "VehicleType") {
                $db_country[] = $getLocationData[$d];
            }
        }

        //Added By HJ On 22-07-2020 For Optimize language_master Table Query End
        $country_str = "-1";
        if (count($db_country) > 0) {
            for ($i = 0; $i < count($db_country); $i++) {
                $country_str .= "," . $db_country[$i]['iLocationId'];
            }
        }
        $ssql .= " AND iLocationid IN ($country_str) ";
    }
    $vehicleDetail = $obj->MySQLSelect("SELECT iVehicleTypeId, vVehicleType_" . $languageCode . " as vTitle,eFareType,eAllowQty,iMaxQty,fFixedFare,fPricePerHour,iLocationid,fMinHour from vehicle_type where iVehicleCategoryId in($iVehicleCategoryId) AND eStatus = 'Active' " . $ssql . " ORDER BY iDisplayOrder");
    //$vCurrencyDriver = get_value('register_driver', 'vCurrencyDriver', 'iDriverId', $driverData[0]['iDriverId'], '', 'true');
    if ($vCurrencyDriver == "" || $vCurrencyDriver == NULL) {
        //Added By HJ On 22-07-2020 For Optimization currency Table Query Start
        if (!empty($vSystemDefaultCurrencyName)) {
            $vCurrencyDriver = $vSystemDefaultCurrencyName;
        }
        else {
            $vCurrencyDriver = get_value('currency', 'vName', 'eDefault', 'Yes', '', 'true');
        }
        //Added By HJ On 22-07-2020 For Optimization currency Table Query End
    }
    //Added By HJ On 22-07-2020 For Optimization currency Table Query Start
    if (isset($currencyAssociateArr[$vCurrencyDriver])) {
        $vCurrencyData = array();
        $vCurrencyData[] = $currencyAssociateArr[$vCurrencyDriver];
    }
    else {
        $vCurrencyData = get_value('currency', 'vSymbol, Ratio', 'vName', $vCurrencyDriver);
    }
    $vCurrencySymbol = $vCurrencyData[0]['vSymbol'];
    $vCurrencyRatio = $vCurrencyData[0]['Ratio'];
    //Added By HJ On 22-07-2020 For Optimization currency Table Query End
    //Added By HJ On 22-07-2020 For Optimization vehicle_category Table Query Start
    $getVehicleCatData = $obj->MySQLSelect("SELECT iParentId,ePriceType,iVehicleCategoryId,eVideoConsultEnable,eVideoConsultServiceCharge,eVideoServiceDescription FROM vehicle_category");
    $iParentId = 0;
    $vehicleCatDataArr = array();
    for ($c = 0; $c < count($getVehicleCatData); $c++) {
        $vehicleCatDataArr[$getVehicleCatData[$c]['iVehicleCategoryId']] = $getVehicleCatData[$c];
    }
    if (isset($vehicleCatDataArr[$iVehicleCategoryId])) {
        $iParentId = $vehicleCatDataArr[$iVehicleCategoryId]['iParentId'];
        $eVideoConsultEnable = $vehicleCatDataArr[$iVehicleCategoryId]['eVideoConsultEnable'];
        $eVideoServiceDescription = $vehicleCatDataArr[$iVehicleCategoryId]['eVideoServiceDescription'];
    }
    //$iParentId = get_value('vehicle_category', 'iParentId,ePriceType', 'iVehicleCategoryId', $iVehicleCategoryId, '', 'true');
    if ($iParentId == 0) {
        if (isset($vehicleCatDataArr[$iVehicleCategoryId])) {
            $ePriceType = $vehicleCatDataArr[$iVehicleCategoryId]['ePriceType'];
        }
        //$ePriceType = get_value('vehicle_category', 'ePriceType', 'iVehicleCategoryId', $iVehicleCategoryId, '', 'true');
    }
    else {
        if (isset($vehicleCatDataArr[$iParentId])) {
            $ePriceType = $vehicleCatDataArr[$iParentId]['ePriceType'];
        }
        //$ePriceType = get_value('vehicle_category', 'ePriceType', 'iVehicleCategoryId', $iParentId, '', 'true');
    }
    //Added By HJ On 22-07-2020 For Optimization vehicle_category Table Query End
    //$iDriverVehicleId = get_value('register_driver', 'iDriverVehicleId', 'iDriverId',$iDriverId,'','true');
    if ($APP_TYPE == "Ride-Delivery-UberX") {
        $result = $obj->MySQLSelect("SELECT iDriverVehicleId FROM `driver_vehicle` WHERE iDriverId = '" . $iDriverId . "' AND eType = 'UberX' ORDER BY iDriverVehicleId DESC LIMIT 0,1");
        $iDriverVehicleId = $result[0]['iDriverVehicleId'];
    }
    else {
        //$iDriverVehicleId = get_value('register_driver', 'iDriverVehicleId', 'iDriverId', $iDriverId, '', 'true');
    }
    /* Added By PJ for get pending services status */
    $ReqServices = $obj->MySQLSelect('SELECT iVehicleCategoryId FROM driver_service_request WHERE iDriverId = "' . $iDriverId . '" ');
    $requestedServices = array();
    foreach ($ReqServices as $key => $ReqService) {
        $requestedServices[] = $ReqService['iVehicleCategoryId'];
    }
    /* END pending services status */
    $db_vCarType = $obj->MySQLSelect("SELECT vCarType FROM `driver_vehicle` where iDriverId ='" . $iDriverId . "' AND iDriverVehicleId = '" . $iDriverVehicleId . "'");
    if (count($db_vCarType) > 0) {
        $vehicle_service_id = explode(",", $db_vCarType[0]['vCarType']);
        //Added By HJ On 22-07-2020 For Optimize language_master Table Query Start
        //echo "<pre>";print_r($vehicleTypeDataArr);die;
        for ($l = 0; $l < count($getLocationData); $l++) {
            $locationDataArr[$getLocationData[$l]['iLocationId']] = $getLocationData[$l]['vLocationName'];
        }
        //echo "<pre>";print_r($locationDataArr);die;
        //Added By HJ On 22-07-2020 For Optimize location_master Table Query End
        //Added By HJ On 22-07-2020 For Optimize service_pro_amount Table Query Start
        $db_serviceproviderid = $obj->MySQLSelect("SELECT * FROM `service_pro_amount` where iDriverVehicleId ='" . $iDriverVehicleId . "'");
        $serviceProDataArr = array();
        for ($s = 0; $s < count($db_serviceproviderid); $s++) {
            $serviceProDataArr[$db_serviceproviderid[$s]['iVehicleTypeId']][] = $db_serviceproviderid[$s];
        }
        //echo "<pre>";print_r($db_serviceproviderid);die;
        //Added By HJ On 22-07-2020 For Optimize service_pro_amount Table Query End
        for ($i = 0; $i < count($vehicleDetail); $i++) {
            //Added By HJ On 22-07-2020 For Optimize service_pro_amount Table Query Start
            //$db_serviceproviderid = $obj->MySQLSelect("SELECT * FROM `service_pro_amount` where iDriverVehicleId ='" . $iDriverVehicleId . "' AND iVehicleTypeId='" . $vehicleDetail[$i]['iVehicleTypeId'] . "'");
            $db_serviceproviderid = array();
            if (isset($serviceProDataArr[$vehicleDetail[$i]['iVehicleTypeId']])) {
                $db_serviceproviderid = $serviceProDataArr[$vehicleDetail[$i]['iVehicleTypeId']];
            }
            //Added By HJ On 22-07-2020 For Optimize service_pro_amount Table Query End
            if (count($db_serviceproviderid) > 0) {
                $vehicleDetail[$i]['fAmount'] = strval($db_serviceproviderid[0]['fAmount']);
            }
            else {
                if ($vehicleDetail[$i]['eFareType'] == "Hourly") {
                    $vehicleDetail[$i]['fAmount'] = strval($vehicleDetail[$i]['fPricePerHour']);
                }
                else {
                    $vehicleDetail[$i]['fAmount'] = strval($vehicleDetail[$i]['fFixedFare']);
                }
            }
            // $vehicleDetail[$i]['iDriverVehicleId']=$driverData[0]['iDriverVehicleId'];
            $fAmount = round($vehicleDetail[$i]['fAmount'] * $vCurrencyRatio, 2);
            $vehicleDetail[$i]['fAmount'] = strval($fAmount);
            $vehicleDetail[$i]['fAmountWithSymbol'] = formateNumAsPerCurrency($fAmount, $vCurrencyDriver);
            $vehicleDetail[$i]['fFixedFareWithSymbol'] = formateNumAsPerCurrency($vehicleDetail[$i]['fFixedFare'], $vCurrencyDriver);
            $vehicleDetail[$i]['fPricePerHourWithSymbol'] = formateNumAsPerCurrency($vehicleDetail[$i]['fPricePerHour'], $vCurrencyDriver);
            $vehicleDetail[$i]['ePriceType'] = $ePriceType;
            $vehicleDetail[$i]['vCurrencySymbol'] = $vCurrencySymbol;
            $data_service[$i] = $vehicleDetail[$i];
            if (in_array($data_service[$i]['iVehicleTypeId'], $vehicle_service_id)) {
                $vehicleDetail[$i]['VehicleServiceStatus'] = 'true';
            }
            else {
                $vehicleDetail[$i]['VehicleServiceStatus'] = 'false';
            }
            /* Added By PJ for get pending services status */
            if ($APP_TYPE == 'UberX' || $APP_TYPE == 'Ride-Delivery-UberX') {
                if (in_array($data_service[$i]['iVehicleTypeId'], $vehicle_service_id)) {
                    $vehicleDetail[$i]['eServiceRequest'] = 'Active';
                }
                else if (in_array($data_service[$i]['iVehicleTypeId'], $requestedServices)) {
                    $vehicleDetail[$i]['eServiceRequest'] = 'Pending';
                }
                else {
                    $vehicleDetail[$i]['eServiceRequest'] = 'Inactive';
                }
                $vehicleDetail[$i]['VehicleServiceId'] = $data_service[$i]['iVehicleTypeId'];
            }
            if ($vehicleDetail[$i]['iLocationid'] == "-1") {
                $vehicleDetail[$i]['SubTitle'] = $lbl_all;
            }
            else {
                //$locationname = $obj->MySQLSelect("SELECT vLocationName FROM location_master WHERE iLocationId = '" . $vehicleDetail[$i]['iLocationid'] . "'");
                $vLocationName = "";
                if (isset($locationDataArr[$vehicleDetail[$i]['iLocationid']])) {
                    $vLocationName = $locationDataArr[$vehicleDetail[$i]['iLocationid']];
                }
                $vehicleDetail[$i]['SubTitle'] = $locationname[0]['vLocationName'];
            }
        }
    }
    if ($MODULES_OBJ->isEnableVideoConsultingService()) {
        $video_consult_data = $VIDEO_CONSULT_OBJ->getServiceDetails($iDriverId, $iVehicleCategoryId);
        $returnArr['eVideoConsultEnable'] = $eVideoConsultEnable;
        $returnArr['eVideoServiceDescription'] = $eVideoServiceDescription;
        $returnArr['eVideoConsultEnableProvider'] = $video_consult_data['eVideoConsultEnableProvider'];
        $returnArr['eServiceRequest'] = $video_consult_data['eVideoConsultStatus'];
        if ($video_consult_data['eVideoConsultServiceCharge'] > 0) {
            $returnArr['eVideoConsultServiceCharge'] = formateNumAsPerCurrency($video_consult_data['eVideoConsultServiceCharge'] * $vCurrencyRatio, $vCurrencyDriver);
            $returnArr['eVideoConsultServiceChargeAmount'] = setTwoDecimalPoint($video_consult_data['eVideoConsultServiceCharge'] * $vCurrencyRatio);
        }
        else {
            $getVehicleCatData_ = $obj->MySQLSelect("SELECT iParentId,ePriceType,iVehicleCategoryId,eVideoConsultEnable,eVideoConsultServiceCharge FROM vehicle_category WHERE iVehicleCategoryId = '$iVehicleCategoryId' ");
            $returnArr['eVideoConsultServiceCharge'] = formateNumAsPerCurrency($getVehicleCatData_[0]['eVideoConsultServiceCharge'] * $vCurrencyRatio, $vCurrencyDriver);
            $returnArr['eVideoConsultServiceChargeAmount'] = setTwoDecimalPoint($getVehicleCatData_[0]['eVideoConsultServiceCharge'] * $vCurrencyRatio);
        }
        if (!empty($video_consult_data['eVideoServiceDescription'])) {
            $returnArr['eVideoServiceDescription'] = $video_consult_data['eVideoServiceDescription'];
        }
    }
    if (count($vehicleDetail) > 0 || $MODULES_OBJ->isEnableVideoConsultingService()) {
        $returnArr['Action'] = "1";
        $returnArr['ENABLE_DRIVER_SERVICE_REQUEST_MODULE'] = $ENABLE_DRIVER_SERVICE_REQUEST_MODULE ? $ENABLE_DRIVER_SERVICE_REQUEST_MODULE : 'Feature Not Avialable.';
        $returnArr['ePriceType'] = $ePriceType;
        $returnArr['message'] = $vehicleDetail;
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_NO_DATA_AVAIL";
    }
    setDataResponse($returnArr);
}
###########################################################################
if ($type == "UpdateDriverServiceAmount") {
    $iVehicleTypeId = isset($_REQUEST['iVehicleTypeId']) ? $_REQUEST['iVehicleTypeId'] : '';
    $iDriverVehicleId = isset($_REQUEST['iDriverVehicleId']) ? $_REQUEST['iDriverVehicleId'] : '';
    $iDriverId = isset($_REQUEST['GeneralMemberId']) ? $_REQUEST['GeneralMemberId'] : '';
    $UserType = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : 'Driver';
    $fAmount = isset($_REQUEST['fAmount']) ? $_REQUEST['fAmount'] : '';
    $isForVideoConsultant = isset($_REQUEST['isForVideoConsultant']) ? $_REQUEST['isForVideoConsultant'] : 'No';
    $iVehicleCategoryId = isset($_REQUEST['iVehicleCategoryId']) ? $_REQUEST['iVehicleCategoryId'] : 'No';
    if ($iDriverVehicleId == "" || $iDriverVehicleId == 0 || $iDriverVehicleId == NULL) {
        $iDriverVehicleId = get_value('register_driver', 'iDriverVehicleId', 'iDriverId', $iDriverId, '', 'true');
    }
    if ($APP_TYPE == "Ride-Delivery-UberX") {
        $query = "SELECT iDriverVehicleId FROM `driver_vehicle` WHERE iDriverId = '" . $iDriverId . "' AND eType = 'UberX'";
        $result = $obj->MySQLSelect($query);
        $iDriverVehicleId = $result[0]['iDriverVehicleId'];
    }
    $vCurrencyDriver = get_value('register_driver', 'vCurrencyDriver', 'iDriverId', $iDriverId, '', 'true');
    if ($vCurrencyDriver == "" || $vCurrencyDriver == NULL) {
        $vCurrencyDriver = get_value('currency', 'vName', 'eDefault', 'Yes', '', 'true');
    }
    $vCurrencyData = get_value('currency', 'vSymbol, Ratio', 'vName', $vCurrencyDriver);
    $vCurrencyRatio = $vCurrencyData[0]['Ratio'];
    $Amount = $fAmount / $vCurrencyRatio;
    $Amount = round($Amount, 2);
    if (strtoupper($isForVideoConsultant) == "NO") {
        $sqlServicePro = "SELECT * FROM `service_pro_amount` WHERE iDriverVehicleId='" . $iDriverVehicleId . "' AND iVehicleTypeId='" . $iVehicleTypeId . "'";
        $serviceProData = $obj->MySQLSelect($sqlServicePro);
        if (count($serviceProData) > 0) {
            $updateQuery = "UPDATE service_pro_amount set fAmount='" . $Amount . "' WHERE iDriverVehicleId='" . $iDriverVehicleId . "' AND iVehicleTypeId='" . $iVehicleTypeId . "'";
            $id = $obj->sql_query($updateQuery);
        }
        else {
            $Data["iDriverVehicleId"] = $iDriverVehicleId;
            $Data["iVehicleTypeId"] = $iVehicleTypeId;
            $Data["fAmount"] = $Amount;
            $id = $obj->MySQLQueryPerform("service_pro_amount", $Data, 'insert');
        }
        $returnArr['DisplayAmount'] = formateNumAsPerCurrency($fAmount, $vCurrencyDriver);
        $iVehicleCategoryId = get_value('vehicle_type', 'iVehicleCategoryId', 'iVehicleTypeId', $iVehicleTypeId, '', 'true');
        $returnArr['message1'] = getServiceTypeDetails($iVehicleCategoryId, $iDriverId, $iVehicleTypeId)[0];
    }
    else {
        $Data = array('iDriverId' => $iDriverId, 'iVehicleCategoryId' => $iVehicleCategoryId, 'eVideoConsultServiceCharge' => $Amount);
        $id = $VIDEO_CONSULT_OBJ->updateVideoConsultService($Data);
        $returnArr['DisplayAmount'] = formateNumAsPerCurrency($fAmount, $vCurrencyDriver);
    }
    if ($id) {
        $returnArr['Action'] = "1";
        $returnArr['message'] = "LBL_SERVICE_AMOUT_UPDATED";
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
    }
    setDataResponse($returnArr);
}
###########################################################################
##############################Update Driver Manage Timing #################################################################
if ($type == "UpdateDriverManageTiming") {
    $iDriverTimingId = isset($_REQUEST['iDriverTimingId']) ? $_REQUEST['iDriverTimingId'] : '';
    $iDriverId = isset($_REQUEST["iDriverId"]) ? $_REQUEST["iDriverId"] : '';
    $vAvailableTimes = isset($_REQUEST["vAvailableTimes"]) ? $_REQUEST["vAvailableTimes"] : ''; // 4-5,5-6,7-8,11-12,14-15
    $scheduleDate = isset($_REQUEST["scheduleDate"]) ? $_REQUEST["scheduleDate"] : ''; // 2017-10-18
    $eStatus = isset($_REQUEST["eStatus"]) ? $_REQUEST["eStatus"] : 'Active';
    $vDay = date('l', strtotime($scheduleDate));
    $dAddedDate = @date("Y-m-d H:i:s");
    $vAvailableTimes = CheckAvailableTimes($vAvailableTimes); // Convert to 04-05,05-06,07-08,11-12,14-15
    $action = ($iDriverTimingId != '') ? 'Edit' : 'Add';
    $Data_Update_Timing['iDriverId'] = $iDriverId;
    $Data_Update_Timing['vDay'] = $vDay;
    $Data_Update_Timing['vAvailableTimes'] = $vAvailableTimes;
    $Data_Update_Timing['dAddedDate'] = $dAddedDate;
    $Data_Update_Timing['eStatus'] = $eStatus;
    if ($action == "Add") {
        $insertid = $obj->MySQLQueryPerform("driver_manage_timing", $Data_Update_Timing, 'insert');
    }
    else {
        $where = " iDriverTimingId = '" . $iDriverTimingId . "'";
        $insertid = $obj->MySQLQueryPerform("driver_manage_timing", $Data_Update_Timing, 'update', $where);
    }
    if ($insertid > 0) {
        $returnArr['Action'] = "1";
        $returnArr['message'] = getDriverDetailInfo($iDriverId);
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
    }
    setDataResponse($returnArr);
}
##############################Update Driver Manage Timing Ends#################################################################
###########################Display Availability##########################################################
if ($type == "DisplayAvailability") {
    $iDriverId = isset($_REQUEST['iDriverId']) ? clean($_REQUEST['iDriverId']) : '';
    $vDay = isset($_REQUEST['vDay']) ? clean($_REQUEST['vDay']) : '';
    //Added By HJ On 02-09-2019 For Get Current Day Name If Day Not Found Start
    if ($vDay == "" || $vDay == null) {
        $dAddedDate = @date("Y-m-d");
        $vDay = @date("l", strtotime($dAddedDate));
        $returnArr['vDay'] = $vDay;
    }
    //Added By HJ On 02-09-2019 For Get Current Day Name If Day Not Found End
    $db_data = $obj->MySQLSelect("select * from `driver_manage_timing` where iDriverId = '" . $iDriverId . "' AND vDay LIKE '" . $vDay . "' ORDER BY iDriverTimingId DESC");
    //added by SP on 06-11-2020, when android device then language wise labels are shown
    if ($GeneralDeviceType == 'Android') {
        $UserDetailsArr = getDriverCurrencyLanguageDetails($iDriverId);
        $vLang = $UserDetailsArr['vLang'];
        $db_data_lang = $db_data;
        if (!empty($db_data) && count($db_data) > 0) {
            foreach ($db_data as $key => $value) {
                $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLang, "1", "");
                $day = "LBL_" . strtoupper($value['vDay']) . "_TXT";
                $db_data_lang[$key]['vDay'] = $languageLabelsArr[$day];
            }
            $db_data = $db_data_lang;
            $day = "LBL_" . strtoupper($vDay) . "_TXT";
            // $returnArr['vDay'] = $languageLabelsArr[$day];
        }
    }
    if (!isset($returnArr['vDay'])) {
        $returnArr['vDay'] = $vDay;
    }
    if (count($db_data) > 0) {
        $returnArr['Action'] = "1";
        $returnArr['message'] = $db_data[0];
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_NO_AVAILABILITY_FOUND";
    }
    setDataResponse($returnArr);
}
###########################Display Availability End######################################################
###########################Add/Update Availability ##########################################################
if ($type == "UpdateAvailability") {
    $iDriverTimingId = isset($_REQUEST['iDriverTimingId']) ? $_REQUEST['iDriverTimingId'] : '';
    $iDriverId = isset($_REQUEST["iDriverId"]) ? $_REQUEST["iDriverId"] : '';
    $vDay = isset($_REQUEST["vDay"]) ? $_REQUEST["vDay"] : '';
    $vAvailableTimes = isset($_REQUEST["vAvailableTimes"]) ? $_REQUEST["vAvailableTimes"] : '';
    $eStatus = isset($_REQUEST["eStatus"]) ? $_REQUEST["eStatus"] : 'Active';
    $dAddedDate = @date("Y-m-d H:i:s");
    $vAvailableTimes = CheckAvailableTimes($vAvailableTimes); // Convert to 04-05,05-06,07-08,11-12,14-15
    $db_data = $obj->MySQLSelect("select iDriverTimingId from `driver_manage_timing` where iDriverId = '" . $iDriverId . "' AND vDay LIKE '" . $vDay . "'");
    //$action = ($iDriverTimingId != '')?'Edit':'Add';
    if (count($db_data) > 0) {
        $action = "Edit";
        $iDriverTimingId = $db_data[0]['iDriverTimingId'];
    }
    else {
        $action = "Add";
    }
    $Data_driver_timing['iDriverId'] = $iDriverId;
    $Data_driver_timing['vDay'] = $vDay;
    $Data_driver_timing['vAvailableTimes'] = $vAvailableTimes;
    $Data_driver_timing['dAddedDate'] = $dAddedDate;
    $Data_driver_timing['eStatus'] = $eStatus;
    if ($action == "Add") {
        $insertid = $obj->MySQLQueryPerform("driver_manage_timing", $Data_driver_timing, 'insert');
        $TimingId = $insertid;
    }
    else {
        $where = " iDriverTimingId = '" . $iDriverTimingId . "'";
        $insertid = $obj->MySQLQueryPerform("driver_manage_timing", $Data_driver_timing, 'update', $where);
        $TimingId = $iDriverTimingId;
    }
    if ($insertid > 0) {
        $returnArr['Action'] = "1";
        $returnArr['TimingId'] = $insertid;
        $returnArr['message'] = "LBL_TIMESLOT_ADD_SUCESS_MSG";
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
    }
    setDataResponse($returnArr);
}
###########################Display Driver Day Availability##########################################################
if ($type == "DisplayDriverDaysAvailability") {
    $iDriverId = isset($_REQUEST['iDriverId']) ? clean($_REQUEST['iDriverId']) : '';
    $GeneralDeviceType = isset($_REQUEST['GeneralDeviceType']) ? clean($_REQUEST['GeneralDeviceType']) : '';
    $db_data = $obj->MySQLSelect("select vDay from `driver_manage_timing` where iDriverId = '" . $iDriverId . "' AND  vAvailableTimes <> '' ORDER BY iDriverTimingId DESC");
    //added by SP on 06-11-2020, when android device then language wise labels are shown
    if ($GeneralDeviceType == 'Android') {
        $UserDetailsArr = getDriverCurrencyLanguageDetails($iDriverId);
        $vLang = $UserDetailsArr['vLang'];
        $db_data_lang = $db_data;
        foreach ($db_data as $key => $value) {
            $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLang, "1", $iServiceId);
            if ($key != 0) {
                $day = "LBL_" . strtoupper($value['vDay']) . "_TXT";
                $db_data_lang[$key]['vDay'] = $languageLabelsArr[$day];
            }
        }
        $db_data = $db_data_lang;
    }
    if (count($db_data) > 0) {
        $returnArr['Action'] = "1";
        $returnArr['message'] = $db_data;
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_NO_AVAILABILITY_FOUND";
    }
    setDataResponse($returnArr);
}
###########################Display Driver Day Availability Ends##########################################################
###########################Check  Schedule Booking Time Availability##########################################################
if ($type == "CheckScheduleTimeAvailability") {
    $scheduleDate = isset($_REQUEST["scheduleDate"]) ? $_REQUEST["scheduleDate"] : '';
    $vTimeZone = isset($_REQUEST["vTimeZone"]) ? $_REQUEST["vTimeZone"] : '';
    $systemTimeZone = date_default_timezone_get();
    // echo "hererrrrr:::".$systemTimeZone;exit;
    $currentdate = date("Y-m-d H:i:s");
    $currentdate = converToTz($currentdate, $vTimeZone, $systemTimeZone);
    $sdate = explode(" ", $scheduleDate);
    $shour = explode("-", $sdate[1]);
    $shour1 = $shour[0];
    $shour2 = $shour[1];
    if ($shour1 == "12" && $shour2 == "01") {
        $shour1 = 00;
    }
    $scheduleDate = $sdate[0] . " " . $shour1 . ":00:00";
    $datediff = strtotime($scheduleDate) - strtotime($currentdate);
    if ($datediff > 3600) {
        $returnArr['Action'] = "1";
        $returnArr['message'] = "";
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_SCHEDULE_TIME_NOT_AVAILABLE";
    }
    setDataResponse($returnArr);
}
############################Check  Schedule Booking Time Availability Ends#####################################################
################################################UBERX Driver Update worklocation address, lat, long########################################################
if ($type == "UpdateDriverWorkLocationUFX") {
    $iDriverId = isset($_REQUEST["iDriverId"]) ? $_REQUEST["iDriverId"] : '';
    $vWorkLocationLatitude = isset($_REQUEST["vWorkLocationLatitude"]) ? $_REQUEST["vWorkLocationLatitude"] : '';
    $vWorkLocationLongitude = isset($_REQUEST["vWorkLocationLongitude"]) ? $_REQUEST["vWorkLocationLongitude"] : '';
    $vWorkLocation = isset($_REQUEST["vWorkLocation"]) ? $_REQUEST["vWorkLocation"] : '';
    $where = " iDriverId='$iDriverId'";
    $Data_update_driver['vWorkLocationLatitude'] = $vWorkLocationLatitude;
    $Data_update_driver['vWorkLocationLongitude'] = $vWorkLocationLongitude;
    $Data_update_driver['vWorkLocation'] = $vWorkLocation;
    $id = $obj->MySQLQueryPerform("register_driver", $Data_update_driver, 'update', $where);
    if ($id) {
        $returnArr['Action'] = "1";
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
    }
    setDataResponse($returnArr);
}
################################################UBERX Driver Update worklocation address, lat, long########################################################
################################################UBERX Get Driver worklocation address, lat, long, worklocation radius########################################################
if ($type == "getDriverWorkLocationUFX") {
    $iDriverId = isset($_REQUEST["iDriverId"]) ? $_REQUEST["iDriverId"] : '';
    $Data = $obj->MySQLSelect("SELECT vWorkLocation,vWorkLocationLatitude,vWorkLocationLongitude,vWorkLocationRadius,eSelectWorkLocation FROM `register_driver` WHERE iDriverId = '" . $iDriverId . "'");
    if (count($Data) > 0) {
        $vWorkLocationRadius = $Data[0]['vWorkLocationRadius'];
        $vCountryUnitDriver = getMemberCountryUnit($iDriverId, "Driver");
        $Data[0]['vCountryUnitDriver'] = $vCountryUnitDriver;
        if ($vCountryUnitDriver == "Miles") {
            $Data[0]['vWorkLocationRadius'] = round($vWorkLocationRadius * 0.6213711, 2); // convert miles to km
            $vWorkLocationRadius = $Data[0]['vWorkLocationRadius'];
        }
        $radiusArr = array(5, 10, 15);
        if (!in_array($vWorkLocationRadius, $radiusArr)) {
            array_push($radiusArr, $vWorkLocationRadius);
        }
        $radusArr = array();
        for ($i = 0; $i < count($radiusArr); $i++) {
            $radusArr[$i]['value'] = $radiusArr[$i];
            $radusArr[$i]['eUnit'] = $vCountryUnitDriver;
            $radusArr[$i]['eSelected'] = ($vWorkLocationRadius == $radiusArr[$i]) ? "Yes" : "No";
        }
        $Data[0]['RadiusList'] = $radusArr;
        $returnArr['Action'] = "1";
        $returnArr['message'] = $Data[0];
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
    }
    setDataResponse($returnArr);
}
################################################UBERX Get Driver worklocation address, lat, long, worklocation radius########################################################
################################################UBERX Driver Update selection of worklocation 'Dynamic', 'Fixed'########################################################
if ($type == "UpdateDriverWorkLocationSelectionUFX") {
    $iDriverId = isset($_REQUEST["iDriverId"]) ? $_REQUEST["iDriverId"] : '';
    $eSelectWorkLocation = isset($_REQUEST["eSelectWorkLocation"]) ? $_REQUEST['eSelectWorkLocation'] : 'Dynamic';
    $vWorkLocation = isset($_REQUEST["vWorkLocation"]) ? $_REQUEST['vWorkLocation'] : '';
    $vWorkLocationLatitude = isset($_REQUEST["vWorkLocationLatitude"]) ? $_REQUEST['vWorkLocationLatitude'] : '';
    $vWorkLocationLongitude = isset($_REQUEST["vWorkLocationLongitude"]) ? $_REQUEST['vWorkLocationLongitude'] : '';
    $where = " iDriverId = '$iDriverId'";
    $tableName = "register_driver";
    $Data_update_driver['eSelectWorkLocation'] = $eSelectWorkLocation;
    if ($vWorkLocation != "" && $vWorkLocationLatitude != "" && $vWorkLocationLongitude != "") {
        $Data_update_driver['vWorkLocation'] = $vWorkLocation;
        $Data_update_driver['vWorkLocationLatitude'] = $vWorkLocationLatitude;
        $Data_update_driver['vWorkLocationLongitude'] = $vWorkLocationLongitude;
    }
    $id = $obj->MySQLQueryPerform($tableName, $Data_update_driver, 'update', $where);
    if ($id > 0) {
        $returnArr['Action'] = "1";
        $returnArr['message'] = getDriverDetailInfo($iDriverId);
        $returnArr['message1'] = "LBL_WORKLOCATION_UPDATE_TXT";
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
    }
    setDataResponse($returnArr);
}
################################################UBERX Driver Update selection of worklocation 'Dynamic', 'Fixed'########################################################
##############################Update Radius ##########################################################
if ($type == "UpdateRadius") {
    $iDriverId = isset($_REQUEST["iDriverId"]) ? $_REQUEST["iDriverId"] : '';
    $vWorkLocationRadius = isset($_REQUEST["vWorkLocationRadius"]) ? $_REQUEST["vWorkLocationRadius"] : '';
    $eStatus = isset($_REQUEST["eStatus"]) ? $_REQUEST["eStatus"] : 'Active';
    $Data_register_driver['vWorkLocationRadius'] = $vWorkLocationRadius;
    $eUnit = getMemberCountryUnit($iDriverId, "Driver");
    if ($eUnit == "Miles") {
        $Data_register_driver['vWorkLocationRadius'] = round($vWorkLocationRadius * 1.60934, 2); // convert miles to km
        $LIST_DRIVER_LIMIT_BY_DISTANCE = round($LIST_DRIVER_LIMIT_BY_DISTANCE * 0.621371, 2);
    }
    else {
        $Data_register_driver['vWorkLocationRadius'] = round($vWorkLocationRadius, 2); // convert miles to km
        $LIST_DRIVER_LIMIT_BY_DISTANCE = round($LIST_DRIVER_LIMIT_BY_DISTANCE, 2);
    }
    $where = " iDriverId = '" . $iDriverId . "'";
    $updateid = $obj->MySQLQueryPerform("register_driver", $Data_register_driver, 'update', $where);
    if ($updateid > 0) {
        $returnArr['Action'] = "1";
        $returnArr['UpdateId'] = $iDriverId;
        $returnArr['message'] = getDriverDetailInfo($iDriverId);
        $returnArr['message1'] = "LBL_INFO_UPDATED_TXT";
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
    }
    setDataResponse($returnArr);
}
##############################Update Radius  End##########################################################
################################################CheckPendingBooking UBERX########################################################
if ($type == "CheckPendingBooking") {
    $iCabBookingId = isset($_REQUEST["iCabBookingId"]) ? $_REQUEST["iCabBookingId"] : '';
    $eStatus = isset($_REQUEST["eStatus"]) ? $_REQUEST["eStatus"] : '';
    $iDriverId = isset($_REQUEST['iDriverId']) ? $_REQUEST['iDriverId'] : '';
    $checkbooking = $obj->MySQLSelect("SELECT dBooking_date from cab_booking WHERE iCabBookingId ='" . $iCabBookingId . "'");
    $dBooking_date = $checkbooking[0]['dBooking_date'];
    $sql = "SELECT iCabBookingId from cab_booking WHERE iDriverId ='" . $iDriverId . "' AND dBooking_date = '" . $dBooking_date . "' AND eStatus = 'Accepted' AND iCabBookingId != '" . $iCabBookingId . "'";
    $pendingacceptdriverbooking = $obj->MySQLSelect($sql);
    if (count($pendingacceptdriverbooking) > 0) {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_PENDING_PLUS_ACCEPT_BOOKING_AVAIL_TXT";
        $returnArr['message1'] = "Accept";
    }
    else {
        $pendingdriverbooking = $obj->MySQLSelect("SELECT iCabBookingId from cab_booking WHERE iDriverId ='" . $iDriverId . "' AND dBooking_date = '" . $dBooking_date . "' AND eStatus = 'Pending' AND iCabBookingId != '" . $iCabBookingId . "'");
        if (count($pendingdriverbooking) > 0) {
            $returnArr['Action'] = "0";
            $returnArr['message'] = "LBL_PENDING_BOOKING_AVAIL_TXT";
            $returnArr['message1'] = "Pending";
        }
        else {
            $returnArr['Action'] = "1";
        }
    }
    setDataResponse($returnArr);
}
################################################CheckPendingBooking UBERX########################################################
if ($type == 'displaytripcharges') {
    $TripID = isset($_REQUEST["TripID"]) ? $_REQUEST["TripID"] : '';
    $destination_lat = isset($_REQUEST["dest_lat"]) ? $_REQUEST["dest_lat"] : '';
    $destination_lon = isset($_REQUEST["dest_lon"]) ? $_REQUEST["dest_lon"] : '';
    $iTripTimeId = isset($_REQUEST["iTripTimeId"]) ? $_REQUEST["iTripTimeId"] : '';
    //$ALLOW_SERVICE_PROVIDER_AMOUNT = $CONFIG_OBJ->getConfigurations("configurations", "ALLOW_SERVICE_PROVIDER_AMOUNT");
    $where = " iTripId = '" . $TripID . "'";
    $data_update['tEndDate'] = @date("Y-m-d H:i:s");
    $data_update['tEndLat'] = $destination_lat;
    $data_update['tEndLong'] = $destination_lon;
    //$obj->MySQLQueryPerform("trips",$data_update,'update',$where);
    if ($iTripTimeId != "") {
        $where = " iTripTimeId = '$iTripTimeId'";
        $Data_update['dPauseTime'] = $data_update['tEndDate'];
        $Data_update['iTripId'] = $TripID;
        //$id = $obj->MySQLQueryPerform("trip_times",$Data_update,'update',$where);
    }
    $sql = "SELECT * from trips WHERE iTripId = '" . $TripID . "'";
    $tripData = $obj->MySQLSelect($sql);
    $iDriverVehicleId = $tripData[0]['iDriverVehicleId'];
    $iVehicleTypeId = $tripData[0]['iVehicleTypeId'];
    $fVisitFee = $tripData[0]['fVisitFee'];
    $startDate = $tripData[0]['tStartDate'];
    $endDateOfTrip = $tripData[0]['tEndDate'];
    $iQty = $tripData[0]['iQty'];
    $destination_lat = $tripData[0]['tEndLat'];
    $destination_lon = $tripData[0]['tEndLong'];
    //$endDateOfTrip=@date("Y-m-d H:i:s");
    /* $iVehicleCategoryId=get_value('vehicle_type', 'iVehicleCategoryId', 'iVehicleTypeId',$iVehicleTypeId,'','true');
    $iParentId = get_value('vehicle_category', 'iParentId', 'iVehicleCategoryId', $iVehicleCategoryId,'','true'); */
    $VehicleCategoryData = $obj->MySQLSelect("SELECT vc.iParentId from vehicle_category as vc LEFT JOIN vehicle_type as vt ON vc.iVehicleCategoryId=vt.iVehicleCategoryId WHERE vt.iVehicleTypeId = '" . $iVehicleTypeId . "'");
    $iParentId = $VehicleCategoryData[0]['iParentId'];
    if ($iParentId == 0) {
        $ePriceType = get_value('vehicle_category', 'ePriceType', 'iVehicleCategoryId', $iVehicleCategoryId, '', 'true');
    }
    else {
        $ePriceType = get_value('vehicle_category', 'ePriceType', 'iVehicleCategoryId', $iParentId, '', 'true');
    }
    //$ePriceType=get_value('vehicle_category', 'ePriceType', 'iVehicleCategoryId',$iVehicleCategoryId,'','true');
    $ALLOW_SERVICE_PROVIDER_AMOUNT = $ePriceType == "Provider" ? "Yes" : "No";
    if ($tripData[0]['eFareType'] == 'Hourly') {
        $sql22 = "SELECT * FROM `trip_times` WHERE iTripId='$TripID'";
        $db_tripTimes = $obj->MySQLSelect($sql22);
        $totalSec = 0;
        $iTripTimeId = '';
        foreach ($db_tripTimes as $dtT) {
            if ($dtT['dPauseTime'] != '' && $dtT['dPauseTime'] != '0000-00-00 00:00:00') {
                $totalSec += strtotime($dtT['dPauseTime']) - strtotime($dtT['dResumeTime']);
            }
        }
        $totalTimeInMinutes_trip = @round(abs($totalSec) / 60, 2);
    }
    else {
        $totalTimeInMinutes_trip = @round(abs(strtotime($startDate) - strtotime($endDateOfTrip)) / 60, 2);
    }
    $totalHour = $totalTimeInMinutes_trip / 60;
    $tripDistance = calcluateTripDistance($tripId);
    $sourcePointLatitude = $tripData[0]['tStartLat'];
    $sourcePointLongitude = $tripData[0]['tStartLong'];
    if ($totalTimeInMinutes_trip <= 1) {
        $FinalDistance = $tripDistance;
    }
    else {
        $FinalDistance = getDistanceInfoFromGoogleDirections($tripDistance, $sourcePointLatitude, $sourcePointLongitude, $destination_lat, $destination_lon);
    }
    $tripDistance = $FinalDistance;
    $fPickUpPrice = $tripData[0]['fPickUpPrice'];
    $fNightPrice = $tripData[0]['fNightPrice'];
    $eFareType = get_value('trips', 'eFareType', 'iTripId', $TripID, '', 'true');
    $surgePrice = $fPickUpPrice > 1 ? $fPickUpPrice : ($fNightPrice > 1 ? $fNightPrice : 1);
    $fAmount = 0;
    $Fare_data = getVehicleCostData("vehicle_type", $iVehicleTypeId);
    //echo "<pre>"; print_r($Fare_data); die;
    $fPricePerKM = getVehicleCountryUnit_PricePerKm($iVehicleTypeId, $Fare_data[0]['fPricePerKM']);
    /* $Minute_Fare = round($Fare_data[0]['fPricePerMin'] * $totalTimeInMinutes_trip * $surgePrice,2);
      $Distance_Fare = round($fPricePerKM * $tripDistance * $surgePrice,2);
      $iBaseFare = round($Fare_data[0]['iBaseFare'] * $surgePrice,2);
      $total_fare = $iBaseFare + $Minute_Fare + $Distance_Fare; */
    $Minute_Fare = $Fare_data[0]['fPricePerMin'] * $totalTimeInMinutes_trip;
    $Distance_Fare = $fPricePerKM * $tripDistance;
    $iBaseFare = $Fare_data[0]['iBaseFare'];
    $total_fare = $iBaseFare + $Minute_Fare + $Distance_Fare;
    $fSurgePriceDiff = (($total_fare * $surgePrice) - $total_fare);
    $total_fare = $total_fare + $fSurgePriceDiff;
    $iMinFare = round($Fare_data[0]['iMinFare'] * $surgePrice, 2);
    if ($iMinFare > $total_fare) {
        $total_fare = $iMinFare;
    }
    $fMinHour = $Fare_data[0]['fMinHour'];
    if ($totalHour > $fMinHour) {
        $miniminutes = $fMinHour * 60;
        $TripTimehours = $totalTimeInMinutes_trip / 60;
        $tothours = intval($TripTimehours);
        $extrahours = $TripTimehours - $tothours;
        $extraminutes = $extrahours * 60;
    }
    if ($ALLOW_SERVICE_PROVIDER_AMOUNT == "Yes") {
        $serviceProData = $obj->MySQLSelect("SELECT * FROM `service_pro_amount` WHERE iDriverVehicleId='" . $iDriverVehicleId . "' AND iVehicleTypeId='" . $iVehicleTypeId . "'");
        if (count($serviceProData) > 0) {
            $fAmount = $serviceProData[0]['fAmount'];
            if ($eFareType == "Fixed") {
                $fAmount = $fAmount * $iQty;
            }
            else if ($eFareType == "Hourly") {
                if ($totalHour > $fMinHour) {
                    $fTimeSlot = $Fare_data[0]['fTimeSlot'];
                    $pricetimeslot = 60 / $fTimeSlot;
                    $pricepertimeslot = $fAmount / $pricetimeslot;
                    $fTimeSlotPrice = $pricepertimeslot;
                    $extraprice = 0;
                    if ($fTimeSlot > 0) {
                        $extratimeslot = ceil($extraminutes / $fTimeSlot);
                        $extraprice = $extratimeslot * $fTimeSlotPrice;
                    }
                    else if ($extraminutes > 0) {
                        $extraprice = ($fAmount / 60) * $extraminutes;
                    }
                    $fAmount = ($fAmount * $tothours) + $extraprice;
                }
                else {
                    $fAmount = $fAmount * $fMinHour;
                    //$fAmount = $fAmount * $totalHour;
                }
            }
            else {
                $fAmount = $total_fare;
            }
        }
        else {
            if ($eFareType == "Fixed") {
                $fAmount = round($Fare_data[0]['fFixedFare'] * $iQty, 2);
            }
            else if ($eFareType == "Hourly") {
                if ($totalHour > $fMinHour) {
                    $fTimeSlot = $Fare_data[0]['fTimeSlot'];
                    $pricetimeslot = 60 / $fTimeSlot;
                    $pricepertimeslot = $Fare_data[0]['fPricePerHour'] / $pricetimeslot;
                    $fTimeSlotPrice = $pricepertimeslot;
                    $extraprice = 0;
                    if ($fTimeSlot > 0) {
                        //$fTimeSlotPrice = $Fare_data[0]['fTimeSlotPrice'];
                        $extratimeslot = ceil($extraminutes / $fTimeSlot);
                        $extraprice = $extratimeslot * $fTimeSlotPrice;
                    }
                    else if ($extraminutes > 0) {
                        $extraprice = ($Fare_data[0]['fPricePerHour'] / 60) * $extraminutes;
                    }
                    $fAmount = round((($Fare_data[0]['fPricePerHour'] * $tothours) + $extraprice), 2);
                }
                else {
                    $fAmount = round($Fare_data[0]['fPricePerHour'] * $fMinHour, 2);
                    // $fAmount = round($Fare_data[0]['fPricePerHour'] * $totalHour,2);
                }
            }
            else {
                $fAmount = $total_fare;
            }
        }
    }
    else {
        if ($eFareType == "Fixed") {
            $fAmount = round($Fare_data[0]['fFixedFare'] * $iQty, 2);
        }
        else if ($eFareType == "Hourly") {
            if ($totalHour > $fMinHour) {
                $fTimeSlot = $Fare_data[0]['fTimeSlot'];
                $pricetimeslot = 60 / $fTimeSlot;
                $pricepertimeslot = $Fare_data[0]['fPricePerHour'] / $pricetimeslot;
                $fTimeSlotPrice = $pricepertimeslot;
                $extraprice = 0;
                if ($fTimeSlot > 0) {
                    //$fTimeSlotPrice = $Fare_data[0]['fTimeSlotPrice'];
                    $extratimeslot = ceil($extraminutes / $fTimeSlot);
                    $extraprice = $extratimeslot * $fTimeSlotPrice;
                }
                else if ($extraminutes > 0) {
                    $extraprice = ($Fare_data[0]['fPricePerHour'] / 60) * $extraminutes;
                }
                $fAmount = round((($Fare_data[0]['fPricePerHour'] * $tothours) + $extraprice), 2);
            }
            else {
                $fAmount = round($Fare_data[0]['fPricePerHour'] * $fMinHour, 2);
                //$fAmount = round($Fare_data[0]['fPricePerHour'] * $totalHour,2);
            }
        }
        else {
            $fAmount = $total_fare;
        }
    }
    $final_display_charge = $fAmount + $fVisitFee;
    $returnArr['Action'] = "1";
    /* $vCurrencyDriver=get_value('register_driver', 'vCurrencyDriver', 'iDriverId', $tripData[0]['iDriverId'],'','true');
      $currencySymbolRationDriver = get_value('currency', 'vSymbol,Ratio', 'vName', $vCurrencyDriver);
      $returnArr['message']=$currencySymbolRationDriver[0]['vSymbol']." ".number_format(round($final_display_charge * $currencySymbolRationDriver[0]['Ratio'],1),2); */
    //$currencySymbol = get_value('currency', 'vSymbol', 'eDefault', 'Yes','',true);
    $vCurrencyDriver = get_value('register_driver', 'vCurrencyDriver', 'iDriverId', $tripData[0]['iDriverId'], '', 'true');
    $currencySymbolRationDriver = get_value('currency', 'vSymbol,Ratio', 'vName', $vCurrencyDriver);
    $currencySymbol = $currencySymbolRationDriver[0]['vSymbol'];
    $currencyRationDriver = $currencySymbolRationDriver[0]['Ratio'];
    $final_display_charge = $final_display_charge * $currencyRationDriver;
    $final_display_charge = round($final_display_charge, 2);
    //$final_display_charge = formatNum($final_display_charge);
    $returnArr['message'] = formateNumAsPerCurrency($final_display_charge, $vCurrencyDriver);
    $returnArr['FareValue'] = $final_display_charge;
    $returnArr['CurrencySymbol'] = $currencySymbol;
    setDataResponse($returnArr);
}
###########################################################################
##############################Add/Update User Address End##########################################################
if ($type == "GetUserStats") {
    $iDriverId = isset($_REQUEST['iDriverId']) ? clean($_REQUEST['iDriverId']) : '';
    //Added By HJ On 22-07-2020 For Optimization cab_booking Table Query Start
    $currDate = date('Y-m-d H:i:s');
    $ssql1 = " AND dBooking_date > '" . $currDate . "'";
    $getBookingData = $obj->MySQLSelect("SELECT iCabBookingId,eStatus FROM cab_booking WHERE iDriverId != '' AND iDriverId = '" . $iDriverId . "' " . $ssql1 . " ORDER BY iCabBookingId DESC");
    $db_data_pending = $db_data_assign = array();
    for ($g = 0; $g < count($getBookingData); $g++) {
        $eStatus = $getBookingData[$g]['eStatus'];
        if (strtoupper($eStatus) == "PENDING") {
            $db_data_pending[] = $getBookingData[$g]['iCabBookingId'];
        }
        if (strtoupper($eStatus) == "ACCEPTED" || strtoupper($eStatus) == "ASSIGN") {
            $db_data_assign[] = $getBookingData[$g]['iCabBookingId'];
        }
    }
    //$db_data_pending = $obj->MySQLSelect("select count(iCabBookingId) as Total_Pending from `cab_booking` where iDriverId != '' AND eStatus = 'Pending' AND iDriverId = '" . $iDriverId . "' " . $ssql1 . " ORDER BY iCabBookingId DESC");
    //$db_data_assign = $obj->MySQLSelect("select count(iCabBookingId) as Total_Upcoming from `cab_booking` where  iDriverId != '' AND ( eStatus = 'Accepted' || eStatus = 'Assign' ) AND iDriverId='" . $iDriverId . "' " . $ssql1 . " ORDER BY iCabBookingId DESC");
    //Added By HJ On 22-07-2020 For Optimization cab_booking Table Query End
    //$db_data_radius = $obj->MySQLSelect("SELECT vWorkLocationRadius as Radius FROM register_driver where iDriverId = '" . $iDriverId . "' ORDER BY iDriverId DESC ");
    //Added By HJ On 22-07-2020 For Optimization register_driver Table Query Start
    $tblName = "register_driver";
    if (isset($userDetailsArr[$tblName . "_" . $iDriverId]) && count($userDetailsArr[$tblName . "_" . $iDriverId]) > 0) {
        $db_data_radius = $userDetailsArr[$tblName . "_" . $iDriverId];
    }
    else {
        $db_data_radius = $obj->MySQLSelect("SELECT *,iDriverId as iMemberId FROM " . $tblName . " WHERE iDriverId='" . $iDriverId . "'");
        $userDetailsArr[$tblName . "_" . $iDriverId] = $db_data_radius;
    }
    //echo "<pre>";print_r($db_data_radius);die;
    $db_data_radius[0]['Radius'] = $db_data_radius[0]['vWorkLocationRadius'];
    //Added By HJ On 22-07-2020 For Optimization register_driver Table Query End
    // $radius = ($db_data_radius[0] != "") ?  $db_data_radius[0] : array("Radius"=>"0");
    $eUnit = getMemberCountryUnit($iDriverId, "Driver");
    if ($eUnit == "Miles") {
        $db_data_radius[0]['Radius'] = round($db_data_radius[0]['Radius'] * 0.621371);
    }
    $returnArr['Action'] = "1";
    $returnArr['Pending_Count'] = (count($db_data_pending) > 0 && empty($db_data_pending) == false) ? count($db_data_pending) : 0;
    $returnArr['Upcoming_Count'] = (count($db_data_assign) > 0 && empty($db_data_assign) == false) ? count($db_data_assign) : 0;
    $returnArr['Radius'] = count($db_data_radius) > 0 ? $db_data_radius[0]['Radius'] : 0;
    setDataResponse($returnArr);
}
//Added By HJ On 24-01-2019 For Get Provider Images Data Start For UFX
if ($type == "getProviderImages") {
    $iDriverId = isset($_REQUEST["iDriverId"]) ? $_REQUEST['iDriverId'] : '';
    $SelectedCabType = isset($_REQUEST["SelectedCabType"]) ? $_REQUEST["SelectedCabType"] : '';
    $getImages = array();
    if ($SERVICE_PROVIDER_FLOW == "Provider" && $SelectedCabType = "UberX") {
        $getImages = $obj->MySQLSelect("SELECT * FROM provider_images WHERE eStatus='Active' AND iDriverId='" . $iDriverId . "'");
        for ($p = 0; $p < count($getImages); $p++) {
            $tmp = explode(".", $getImages[$p]['vImage']);
            for ($i = 0; $i < count($tmp) - 1; $i++) {
                $tmp1[] = $tmp[$i];
            }
            $file = implode("_", $tmp1);
            $ext = $tmp[count($tmp) - 1];
            $videoExt_arr = array('MP4', 'MOV', 'WMV', 'AVI', 'FLV', 'MKV', 'WEBM');
            $getImages[$p]['eFileType'] = 'Image';
            $getImages[$p]['ThumbImage'] = '';
            if (in_array(strtoupper($ext), $videoExt_arr)) {
                $getImages[$p]['eFileType'] = 'Video';
                $getImages[$p]['ThumbImage'] = getVideoThumbImageProvider($getImages[$p]['vImage']);
            }
            $getImages[$p]['vImage'] = $tconfig['tsite_upload_provider_image'] . '/' . $getImages[$p]['vImage'];
        }
    } //Provider_Images
    $returnArr['Action'] = "1";
    $returnArr['message'] = $getImages;
    setDataResponse($returnArr);
}
//Added By HJ On 24-01-2019 For Get Provider Images Data End For UFX
##############################Display user status End##########################################################
if ($type == "configProviderImages") {
    // // Commented By HJ On 22-07-2020 Bcoz Not Required
    $action_type = isset($_REQUEST["action_type"]) ? $_REQUEST["action_type"] : 'ADD';
    $image_name = $vImage = isset($_FILES['vImage']['name']) ? $_FILES['vImage']['name'] : '';
    $image_object = isset($_FILES['vImage']['tmp_name']) ? $_FILES['vImage']['tmp_name'] : '';
    $iImageId = isset($_REQUEST["iImageId"]) ? $_REQUEST["iImageId"] : '';
    $iDriverId = isset($_REQUEST["iDriverId"]) ? $_REQUEST["iDriverId"] : '';
    if ($action_type == "ADD") {
        /* Code for Upload StartImage of trip Start */
        if ($image_name != "") {
            $Photo_Gallery_folder = $tconfig['tsite_upload_provider_image_path'];
            if (!is_dir($Photo_Gallery_folder)) {
                mkdir($Photo_Gallery_folder, 0777);
            }
            $vFile = $UPLOAD_OBJ->GeneralFileUpload($Photo_Gallery_folder, $image_object, $image_name, $prefix = '', "jpg,jpeg,gif,png,mp4,mov,wmv,avi,flv,mkv,webm");
            if ($vFile[2] > 0) {
                $returnArr['Action'] = "0";
                $returnArr['message'] = "LBL_FILE_UPLOADED_UNSUCCESS_MSG";
                setDataResponse($returnArr);
            }
            $vImageName = $vFile[0];
            $Data_update_images['vImage'] = $vImageName;
        }
        $Data_update_images['iDriverId'] = $iDriverId;
        $Data_update_images['tAddedDate'] = @date("Y-m-d H:i:s");
        /* Code for Upload StartImage of trip End */
        $id = $obj->MySQLQueryPerform("provider_images", $Data_update_images, 'insert');
        $tmp = explode(".", $vImageName);
        for ($i = 0; $i < count($tmp) - 1; $i++) {
            $tmp1[] = $tmp[$i];
        }
        $file = implode("_", $tmp1);
        $ext = $tmp[count($tmp) - 1];
        $videoExt_arr = array('MP4', 'MOV', 'WMV', 'AVI', 'FLV', 'MKV', 'WEBM');
        $message = "LBL_IMAGE_UPLOAD_SUCCESS_NOTE";
        if (in_array(strtoupper($ext), $videoExt_arr)) {
            $message = "LBL_VIDEO_UPLOAD_SUCCESS_NOTE";
        }
        if ($id > 0) {
            $returnArr['Action'] = "1";
            $returnArr['message'] = $message;
        }
        else {
            $returnArr['Action'] = "0";
            $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
        }
    }
    else if ($action_type == "DELETE" && $iImageId != "") {
        $Photo_Gallery_folder = $tconfig['tsite_upload_provider_image_path'];
        $OldImageName = get_value('provider_images', 'vImage', 'iImageId', $iImageId, '', 'true');
        if ($OldImageName != '') {
            unlink($Photo_Gallery_folder . $OldImageName);
        }
        $sql = "DELETE FROM provider_images WHERE `iImageId`='" . $iImageId . "'";
        $id = $obj->sql_query($sql);
        if ($id > 0) {
            $returnArr['Action'] = "1";
            $returnArr['message'] = "LBL_IMAGE_DELETE_SUCCESS_NOTE";
        }
        else {
            $returnArr['Action'] = "0";
            $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
        }
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
    }
    setDataResponse($returnArr);
}
//Added By HJ On 24-01-2019 For Get Service Category Data Start For UFX
if ($type == "getDriverServiceCategories") {
    $iDriverId = isset($_REQUEST["iDriverId"]) ? $_REQUEST['iDriverId'] : '';
    $iMemberId = isset($_REQUEST["iMemberId"]) ? $_REQUEST['iMemberId'] : '';
    $SelectedCabType = isset($_REQUEST["SelectedCabType"]) ? $_REQUEST["SelectedCabType"] : '';
    $vSelectedLatitude = isset($_REQUEST["vSelectedLatitude"]) ? $_REQUEST["vSelectedLatitude"] : '';
    $vSelectedLongitude = isset($_REQUEST["vSelectedLongitude"]) ? $_REQUEST["vSelectedLongitude"] : '';
    $parentId = isset($_REQUEST["parentId"]) ? $_REQUEST["parentId"] : 0;
    $SelectedVehicleTypeId = isset($_REQUEST["SelectedVehicleTypeId"]) ? $_REQUEST["SelectedVehicleTypeId"] : '';
    $eForVideoConsultation = isset($_REQUEST["eForVideoConsultation"]) ? $_REQUEST['eForVideoConsultation'] : 'No';
    //added by SP on 02-11-2020 for manualbooking from web, changes bcoz parentid,driverid may not passed and also from admin side booking then iMemberId will not be there...
    $bookingFrom = isset($_REQUEST["bookingFrom"]) ? $_REQUEST["bookingFrom"] : 'App';
    if ($iMemberId == "" && $bookingFrom != 'Web') {
        $iMemberId = isset($_REQUEST["GeneralMemberId"]) ? $_REQUEST['GeneralMemberId'] : '';
    }

    $isEnableVideoConsultingService = $MODULES_OBJ->isEnableVideoConsultingService();
    $categoryArr = array();
    if ($parentId > 0 || $SelectedVehicleTypeId != "" || $bookingFrom == 'Web') { //here bookingfrom passed for old booking code..when ufxservice=1 code applied to in maincopy then it is not needed so remove it
        if ($SERVICE_PROVIDER_FLOW == "Provider" && $SelectedCabType = "UberX") {
            //$getDriveVehicleType = $obj->MySQLSelect("SELECT GROUP_CONCAT(vCarType)as typeIds FROM driver_vehicle WHERE `iDriverId`='" . $iDriverId . "' AND eStatus='Active' AND vCarType != '' GROUP BY iDriverId");
            $getDriveVehicleType = $obj->MySQLSelect("SELECT GROUP_CONCAT(trim(',' FROM vCarType))as typeIds, iDriverVehicleId FROM driver_vehicle WHERE `iDriverId`='" . $iDriverId . "' AND eStatus='Active' AND vCarType != '' GROUP BY iDriverId");
            if (count($getDriveVehicleType) > 0 || $bookingFrom == 'Web') { //here bookingfrom passed for old booking code..when ufxservice=1 code applied to in maincopy then it is not needed so remove it
                $iDriverVehicleId = $getDriveVehicleType[0]['iDriverVehicleId'];
                $userData = $obj->MySQLSelect("SELECT ru.vCurrencyPassenger, ru.vLang, cu.vSymbol, cu.Ratio, co.eUnit FROM register_user as ru, currency as cu, country as co WHERE ru.iUserId='" . $iMemberId . "' AND cu.vName = ru.vCurrencyPassenger AND co.vCountryCode = ru.vCountry");
                $lang = "EN";
                $vSymbol = "$";
                $priceRatio = 1;
                if (count($userData) > 0) {
                    $lang = $userData[0]['vLang'];
                    $priceRatio = $userData[0]['Ratio'];
                    $vSymbol = $userData[0]['vSymbol'];
                    $vCurrencyPassenger = $userData[0]['vCurrencyPassenger'];
                }
                //added by SP on 02-11-2020 for manualbooking from web
                if ($vCurrencyPassenger == "" || $vCurrencyPassenger == NULL) {
                    $vCurrencyPassenger = get_value('currency', 'vName', 'eDefault', 'Yes', '', 'true');
                    $priceRatio = get_value('currency', 'Ratio', 'vName', $vCurrencyPassenger, '', 'true');
                    $vSymbol = get_value('currency', 'vSymbol', 'vName', $vCurrencyPassenger, '', 'true');
                }
                if ($lang == "" || $lang == NULL) {
                    $lang = $LANG_OBJ->FetchDefaultLangData("vCode");
                }
                if (isset($_SESSION['sess_lang']) && trim($_SESSION['sess_lang']) != "" && $bookingFrom == 'Web') {
                    $lang = $_SESSION['sess_lang'];
                }
                $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($lang, "1");
                $getMainCat = $obj->MySQLSelect("SELECT iVehicleCategoryId,vCategory_" . $lang . " AS catName,vCategoryTitle_" . $lang . " as vCategoryTitle FROM vehicle_category WHERE eStatus='Active'");
                $cateNameArr = $cateTitleArr = array();
                for ($n = 0; $n < count($getMainCat); $n++) {
                    $mainCatId = $getMainCat[$n]['iVehicleCategoryId'];
                    $cateNameArr[$mainCatId] = $getMainCat[$n]['catName'];
                    $cateTitleArr[$mainCatId] = $getMainCat[$n]['vCategoryTitle'];
                }
                $sql_typeIds = "";
                if ($iDriverId != "") { //added by SP on 02-11-2020 for manualbooking from web put this cond.
                    $typeIds = str_replace(",,", ",", trim($getDriveVehicleType[0]['typeIds'], ",")); // Added By HJ On 06-12-2019 For Solved issue #588 Of Sheet
                    $sql_typeIds = " AND vt.iVehicleTypeId IN ($typeIds)";
                }
                if (($parentId == "" || $parentId == 0) && $SelectedVehicleTypeId != "") {
                    $tmpSelectedTypeIdArr = explode(",", $SelectedVehicleTypeId);
                    if (count($tmpSelectedTypeIdArr) > 0) {
                        $parentId = get_value('vehicle_category', 'iParentId', 'iVehicleCategoryId', $tmpSelectedTypeIdArr[0], '', 'true');
                    }
                }
                $ssql_parentCategoryIds = "";
                if ($parentId != '') { //added by SP on 02-11-2020 for manualbooking from web put this cond.
                    $parentCategoryIdsArr = $obj->MySQLSelect("SELECT GROUP_CONCAT(  `iVehicleCategoryId` ) AS parentCategoryIds FROM  `vehicle_category` WHERE `iParentId` = '" . $parentId . "' AND eStatus = 'Active'") ;
                    if (count($parentCategoryIdsArr) > 0) {
                        $ssql_parentCategoryIds = " AND vt.iVehicleCategoryId IN (" . $parentCategoryIdsArr[0]['parentCategoryIds'] . ")";
                    }
                }
                if ($isEnableVideoConsultingService && $eForVideoConsultation == "Yes") {
                    $ssql_parentCategoryIds = "";
                    if ($parentId != '') {
                        // $parentCategoryIdsArr = $obj->MySQLSelect("SELECT GROUP_CONCAT(  `iVehicleCategoryId` ) AS parentCategoryIds FROM  `vehicle_type` WHERE `iVehicleCategoryId` = '" . $SelectedVehicleTypeId . "' LIMIT 0,1");
                        // if (count($parentCategoryIdsArr) > 0) {
                        //     $ssql_parentCategoryIds = " AND vt.iVehicleCategoryId IN (" . $parentCategoryIdsArr[0]['parentCategoryIds'] . ")";
                        // }
                        $ssql_parentCategoryIds = " iVehicleCategoryId = " . $SelectedVehicleTypeId . "";
                        $sql_typeIds = '';
                    }
                }
                if ($bookingFrom == 'Web') {
                    $ordsql = " ORDER BY iMainDisplayOrder ASC, vc.iDisplayOrder ASC, vt.iDisplayOrder ASC";
                }
                else {
                    $ordsql = " ORDER BY vt.iDisplayOrder ASC";
                }
                // $getTypeIds = $obj->MySQLSelect("SELECT vt.tTypeDesc,vt.iVehicleCategoryId,vt.vVehicleType_" . $lang . " AS vVehicleType, vt.eFareType, vt.fFixedFare, vt.fPricePerHour, vt.fPricePerKM, vt.fPricePerMin, vt.iBaseFare,vt.fCommision, vt.iMinFare,vt.iPersonSize, vt.vLogo as vVehicleTypeImage, vt.eType, vt.eIconType, vt.eAllowQty, vt.fMinHour, vt.iMaxQty, vt.iVehicleTypeId, fFixedFare FROM vehicle_type vt WHERE iVehicleCategoryId >0 AND iVehicleTypeId IN ($typeIds) AND eStatus='Active' ".$ssql_parentCategoryIds);
                $pickuplocationarr = array($vSelectedLatitude, $vSelectedLongitude);
                $GetVehicleIdfromGeoLocation = FetchVehicleTypeFromGeoLocation($pickuplocationarr);
                //print_r($GetVehicleIdfromGeoLocation);die;
                $getTypeIds = $obj->MySQLSelect("SELECT vt.iVehicleTypeId,vc.iParentId,vt.tTypeDesc,vt.iVehicleCategoryId,vt.vVehicleType_" . $lang . " AS vVehicleType, vt.eFareType, vt.fFixedFare, vt.fPricePerHour, vt.fPricePerKM, vt.fPricePerMin, vt.iBaseFare,vt.fCommision, vt.iMinFare,vt.iPersonSize, vt.vLogo as vVehicleTypeImage, vt.eType, vt.eIconType, vt.eAllowQty, vt.fMinHour, vt.iMaxQty, vt.iVehicleTypeId, vt.fTimeSlot,vt.fTimeSlotPrice, vcs.iDisplayorder as iMainDisplayOrder, vcs.ePriceType as ParentPriceType,  vc.eVideoConsultEnable, vc.eVideoConsultServiceCharge, vc.eVideoServiceDescription FROM vehicle_type vt, vehicle_category as vc LEFT JOIN vehicle_category as vcs ON vcs.iVehicleCategoryId = vc.iParentId WHERE vt.iVehicleCategoryId >0 " . $sql_typeIds . " AND vt.iLocationid IN ($GetVehicleIdfromGeoLocation) AND vt.eStatus='Active' AND vc.iVehicleCategoryId = vt.iVehicleCategoryId " . $ssql_parentCategoryIds . $ordsql);

                $serviceProData = $obj->MySQLSelect("SELECT * FROM `service_pro_amount` WHERE iDriverVehicleId = '$iDriverVehicleId' AND iVehicleTypeId IN ($typeIds)");
                $serviceProAmtArr = array();
                for ($n = 0; $n < count($serviceProData); $n++) {
                    $serviceProAmtArr[$serviceProData[$n]['iVehicleTypeId']] = $serviceProData[$n];
                }

                // echo "TotalCOunt::" . count($getTypeIds) . "<BR/>";
                $sortarray = array();
       
                if(!empty($SelectedVehicleTypeId)){
                    $sortArrayVehicleSelected = explode(',', $SelectedVehicleTypeId);
                }
                if ($isEnableVideoConsultingService && $eForVideoConsultation == "Yes") {
                    $tProfileDescription = get_value('register_driver', 'tProfileDescription', 'iDriverId', $iDriverId, '', 'true');
                    if (!empty($tProfileDescription)) {
                        $categoryArr[0] = array(
                            'vCategory' => $languageLabelsArr['LBL_ABOUT_EXPERT'], 'SubCategories' => array(
                                array(
                                    'vCategoryDesc' => $tProfileDescription
                                )
                            )
                        );
                    }
                    $getTypeIds = $obj->MySQLSelect("SELECT iVehicleCategoryId, eVideoConsultEnable, eVideoConsultServiceCharge, eVideoServiceDescription FROM vehicle_category WHERE " . $ssql_parentCategoryIds);

                    $service_data = $obj->MySQLSelect("SELECT * FROM driver_services_video_consult_charges WHERE iDriverId = '$iDriverId'");

                    $driver_services_vc = array();
                    for ($n = 0; $n < count($service_data); $n++) {
                        $driver_services_vc['VC_' . $service_data[$n]['iVehicleCategoryId']] = $service_data[$n];
                    }
                }
                for ($c = 0; $c < count($getTypeIds); $c++) {
                    $sortarray[] = $getTypeIds[$c]['iVehicleCategoryId'];
                    if (!in_array($getTypeIds[$c]['iVehicleCategoryId'], $sortArrayVehicleSelected)) {
                        $sortArrayVehicleSelected[] = $getTypeIds[$c]['iVehicleCategoryId'];
                    }
                          
                    $catId = $getTypeIds[$c]['iVehicleCategoryId'];
                    $mainCatiParentId = $getTypeIds[$c]['iParentId'];
                    $tTypeDescription = "";
                    $tTypeDesc = json_decode($getTypeIds[$c]['tTypeDesc'], true);
                    if (isset($tTypeDesc['tTypeDesc_' . $lang]) && $tTypeDesc['tTypeDesc_' . $lang] != "") {
                        $tTypeDescription = $tTypeDesc['tTypeDesc_' . $lang];
                    }
                    ########################################## Check Fare Of Provider ##########################################
                    // if ($getTypeIds[$c]['ProviderPrice'] != NULL) {
                    //     $getTypeIds[$c]['fFixedFare'] = $getTypeIds[$c]['ProviderPrice'];
                    //     $getTypeIds[$c]['fPricePerHour'] = $getTypeIds[$c]['ProviderPrice'];
                    // }

                    if($getTypeIds[$c]['ParentPriceType'] == "Provider") {
                        if(isset($serviceProAmtArr[$getTypeIds[$c]['iVehicleTypeId']])) {
                            $getTypeIds[$c]['fFixedFare'] = $serviceProAmtArr[$getTypeIds[$c]['iVehicleTypeId']]['fAmount'];
                            $getTypeIds[$c]['fPricePerHour'] = $serviceProAmtArr[$getTypeIds[$c]['iVehicleTypeId']]['fAmount'];
                        }
                    }
                    if ($getTypeIds[$c]['fMinHour'] == 0) {
                        $getTypeIds[$c]['fMinHour'] = 1;
                    }
                    // $getTypeIds[$c]['fPricePerHour'] = $getTypeIds[$c]['fPricePerHour'] * $getTypeIds[$c]['fMinHour'];
                    unset($getTypeIds[$c]['ProviderPrice']);
                    unset($getTypeIds[$c]['ParentPriceType']);
                    ########################################## Check Fare Of Provider ##########################################
                    $mainCatName = $cateNameArr[$catId];

                    $vCategoryTitleMain = $cateNameArr[$mainCatiParentId];
                    $vCategoryTitle = $cateTitleArr[$catId];
                    $subTypeArr = array();
                    $subTypeArr['iVehicleCategoryId'] = $catId;
                    $subTypeArr['vCategory'] = isset($mainCatName) ? $mainCatName : "";
                    $subTypeArr['iVehicleTypeId'] = $getTypeIds[$c]['iVehicleTypeId'];
                    $subTypeArr['iPersonSize'] = $getTypeIds[$c]['iPersonSize'];
                    $subTypeArr['eType'] = $getTypeIds[$c]['eType'];
                    $subTypeArr['eIconType'] = $getTypeIds[$c]['eIconType'];
                    $subTypeArr['eAllowQty'] = $getTypeIds[$c]['eAllowQty'];
                    $subTypeArr['fMinHour'] = $getTypeIds[$c]['fMinHour'];
                    $subTypeArr['iMaxQty'] = $getTypeIds[$c]['iMaxQty'];
                    $subTypeArr['vVehicleType'] = $getTypeIds[$c]['vVehicleType'];
                    $subTypeArr['eFareType'] = $getTypeIds[$c]['eFareType'];
                    $fFixedFare_value = setTwoDecimalPoint($getTypeIds[$c]['fFixedFare'] * $priceRatio);
                    $subTypeArr['fFixedFare_value'] = $fFixedFare_value;
                    // $subTypeArr['fFixedFare'] = $vSymbol . formatNum($fFixedFare_value);
                    $subTypeArr['fFixedFare'] = formateNumAsPerCurrency($fFixedFare_value, $vCurrencyPassenger);
                    $fPricePerHour_value = setTwoDecimalPoint($getTypeIds[$c]['fPricePerHour'] * $priceRatio);
                    $subTypeArr['fPricePerHour_value'] = $fPricePerHour_value;
                    $subTypeArr['fPricePerHour'] = formateNumAsPerCurrency($fPricePerHour_value, $vCurrencyPassenger);
                    $fPricePerKM = getVehicleCountryUnit_PricePerKm($getTypeIds[$c]['iVehicleTypeId'], $getTypeIds[$c]['fPricePerKM'], $iDriverId, "Passenger");
                    $fPricePerKM = setTwoDecimalPoint($fPricePerKM * $priceRatio);
                    $subTypeArr['fPricePerKM'] = formateNumAsPerCurrency($fPricePerKM, $vCurrencyPassenger);
                    $fPricePerMin = setTwoDecimalPoint($getTypeIds[$c]['fPricePerMin'] * $priceRatio);
                    $subTypeArr['fPricePerMin'] = formateNumAsPerCurrency($fPricePerMin, $vCurrencyPassenger);
                    $iBaseFare = setTwoDecimalPoint($getTypeIds[$c]['iBaseFare'] * $priceRatio);
                    $subTypeArr['iBaseFare'] = formateNumAsPerCurrency($iBaseFare, $vCurrencyPassenger);
                    $fCommision = setTwoDecimalPoint($getTypeIds[$c]['fCommision'] * $priceRatio);
                    $subTypeArr['fCommision'] = formateNumAsPerCurrency($fCommision, $vCurrencyPassenger);
                    $iMinFare = setTwoDecimalPoint($getTypeIds[$c]['iMinFare'] * $priceRatio);
                    $subTypeArr['iMinFare'] = formateNumAsPerCurrency($iMinFare, $vCurrencyPassenger);
                    $subTypeArr['vSymbol'] = $vSymbol;
                    $subTypeArr['vCategoryTitle'] = isset($vCategoryTitle) ? $vCategoryTitle : "";
                    $subTypeArr['vCategoryDesc'] = $tTypeDescription;
                    // $subTypeArr['vCategoryShortDesc'] = strip_tags($tTypeDescription);
                    $subTypeArr['vCategoryShortDesc'] = !empty($getTypeIds[$c]['tInfoText']) ? nl2br($getTypeIds[$c]['tInfoText']) : '';
                    $subTypeArr['vRating'] = "0.00";
                    //$subTypeArr['vLogo'] = $Data[$j]['vVehicleTypeImage'];
                    //$subTypeArr['vImage'] = $tconfig['tsite_upload_images_vehicle_category'] . '/' . $subTypeArr['iVehicleTypeId'] . '/android/' . $getTypeIds[$c]['vVehicleTypeImage'];
                    if ($bookingFrom == 'Web') {
                        $categoryArr[$catId]['vCategoryTitleMain'] = isset($vCategoryTitleMain) ? $vCategoryTitleMain : "";
                        $categoryArr[$catId]['mainCatiParentId'] = isset($mainCatiParentId) ? $mainCatiParentId : "";
                    }
                    $categoryArr[$catId]['vCategory'] = isset($mainCatName) ? $mainCatName : "";
                    $categoryArr[$catId]['iVehicleCategoryId'] = $catId;
                    if ($isEnableVideoConsultingService && $eForVideoConsultation == "Yes") {
                        $catId = $SelectedVehicleTypeId;
                        $categoryArr[$catId]['vCategory'] = $languageLabelsArr['LBL_SERVICE_DESCRIPTION'];
                        $categoryArr[$catId]['eVideoConsultEnable'] = $getTypeIds[$c]['eVideoConsultEnable'];
                        $categoryArr[$catId]['eVideoConsultServiceCharge'] = formateNumAsPerCurrency($getTypeIds[$c]['eVideoConsultServiceCharge'] * $priceRatio, $vCurrencyPassenger);
                        $vCategoryDesc = $getTypeIds[$c]['eVideoServiceDescription'];
                        // $video_consult_data = $VIDEO_CONSULT_OBJ->getServiceDetails($iDriverId, $catId);
                        // echo "$catId <pre>"; print_r($driver_services_vc); exit;
                        if(isset($driver_services_vc['VC_' . $catId])) {
                            $video_consult_data = $driver_services_vc['VC_' . $catId];
                            $video_consult_data['eVideoConsultStatus'] = $driver_services_vc['VC_' . $catId]['eStatus'];
                        }
                        if ($video_consult_data['eVideoConsultEnableProvider'] == "Yes") {
                            $categoryArr[$catId]['eVideoConsultServiceCharge'] = formateNumAsPerCurrency($video_consult_data['eVideoConsultServiceCharge'] * $priceRatio, $vCurrencyPassenger);
                            $vCategoryDesc = $video_consult_data['eVideoServiceDescription'];
                        }
                        $subTypeArr['fFixedFare_value'] = $subTypeArr['fFixedFare'] = $subTypeArr['fPricePerHour_value'] = $subTypeArr['fPricePerHour'] = $subTypeArr['eIconType'] = $subTypeArr['iPersonSize'] = "";
                        $subTypeArr['vVehicleType'] = isset($mainCatName) ? $mainCatName : "";
                        $subTypeArr['vCategoryDesc'] = isset($vCategoryDesc) && !empty($vCategoryDesc) ? $vCategoryDesc : $languageLabelsArr['LBL_SERVICE_DESCRIPTION_NOT_AVAILABLE'];
                        $subTypeArr['eFareType'] = "Fixed";
                        $subTypeArr['iMaxQty'] = "1";
                        $subTypeArr['eAllowQty'] = "No";
                        $subTypeArr['eType'] = "UberX";
                        $subTypeArr['iVehicleCategoryId'] = $catId;
                        $subTypeArr['iVehicleTypeId'] = $catId;
                        $categoryArr[$catId]['SubCategories'][] = $subTypeArr;
                        //$categoryArr[$catId]['SubCategories_'][] = $subTypeArr; 
                        if (isset($categoryArr[0]['SubCategories'])) {
                            $categoryArr[0]['iVehicleCategoryId'] = $categoryArr[$catId]['iVehicleCategoryId'];
                            $categoryArr[0]['eVideoConsultEnable'] = $categoryArr[$catId]['eVideoConsultEnable'];
                            $categoryArr[0]['eVideoConsultServiceCharge'] = $categoryArr[$catId]['eVideoConsultServiceCharge'];
                            $about_desc = $categoryArr[0]['SubCategories'][0]['vCategoryDesc'];
                            $categoryArr[0]['SubCategories'][0] = $subTypeArr;
                            $categoryArr[0]['SubCategories'][0]['vCategoryDesc'] = $about_desc;
                        }
                        // echo "<pre>"; print_r($categoryArr); exit;
                        break;
                    }
                    $categoryArr[$catId]['SubCategories'][] = $subTypeArr;
                }
            }
        }
    }

    if ($MODULES_OBJ->isEnableVideoConsultingService() && $eForVideoConsultation == "No") {
        $categoryArr = array_replace(array_flip($sortArrayVehicleSelected), $categoryArr);
    }
    $categoryArr = array_values($categoryArr);

    /* Added by HV on 12-06-2021 for searching services */
    if ($MODULES_OBJ->isEnableSearchUfxServices() && isset($_REQUEST['search_keyword'])) {
        $search_keyword = isset($_REQUEST['search_keyword']) ? $_REQUEST['search_keyword'] : "";
        if (!empty($search_keyword)) {
            $vehicleCategoryData = $categoryArr;
            foreach ($vehicleCategoryData as $key => $value) {
                $main_cat = $subcat = 0;
                if (stripos($value['vCategory'], $search_keyword) !== false) {
                    $main_cat = 1;
                }
                if (isset($value['SubCategories']) && $main_cat == 0) {
                    foreach ($value['SubCategories'] as $skey => $sCategory) {
                        if (stripos($sCategory['vVehicleType'], $search_keyword) !== false) {
                            $subcat = 1;
                        }
                        else {
                            unset($vehicleCategoryData[$key]['SubCategories'][$skey]);
                        }
                    }
                    if (!empty($vehicleCategoryData[$key]['SubCategories'])) {
                        $vehicleCategoryData[$key]['SubCategories'] = array_values($vehicleCategoryData[$key]['SubCategories']);
                    }
                }
                if (($main_cat == 0 && $subcat == 0) || empty($vehicleCategoryData[$key]['SubCategories'])) {
                    unset($vehicleCategoryData[$key]);
                }
            }
            $categoryArr = array_values($vehicleCategoryData);
        }
        else {
            $categoryArr = array();
        }
    }
    /* Added by HV on 12-06-2021 for searching services End */

    if (!empty($categoryArr)) {
        $returnArr['Action'] = "1";
        $returnArr['message'] = $categoryArr;
        setDataResponse($returnArr);
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_NO_DATA_AVAIL";
        setDataResponse($returnArr);
    }
}
//Added By HJ On 24-01-2019 For Get Service Category Data Start For UFX
//Added By HJ On 25-01-2019 For Get Service Details Start For UFX
if ($type == "getVehicleTypeDetails") {
    $iDriverId = isset($_REQUEST["iDriverId"]) ? $_REQUEST['iDriverId'] : '';
    $iMemberId = isset($_REQUEST["iMemberId"]) ? $_REQUEST['iMemberId'] : '';
    $iVehicleTypeId = isset($_REQUEST["iVehicleTypeId"]) ? $_REQUEST['iVehicleTypeId'] : '';
    $SelectedCabType = isset($_REQUEST["SelectedCabType"]) ? $_REQUEST["SelectedCabType"] : '';
    $bookingFrom = isset($_REQUEST["bookingFrom"]) ? $_REQUEST["bookingFrom"] : 'App'; //added by SP for manual booking from admin side memberid not get.
    $eForVideoConsultation = isset($_REQUEST["eForVideoConsultation"]) ? $_REQUEST['eForVideoConsultation'] : 'No';
    $iVehicleCategoryId = isset($_REQUEST["iVehicleCategoryId"]) ? $_REQUEST['iVehicleCategoryId'] : '';
    if ($iMemberId == "" && $bookingFrom == 'App') {
        $iMemberId = isset($_REQUEST["GeneralMemberId"]) ? $_REQUEST['GeneralMemberId'] : '';
    }
    if ($SERVICE_PROVIDER_FLOW == "Provider" && $SelectedCabType = "UberX") {
        if (!empty($iMemberId)) {
            $userData = $obj->MySQLSelect("SELECT ru.vCurrencyPassenger, ru.vLang, cu.vSymbol, cu.Ratio, co.eUnit FROM register_user as ru, currency as cu, country as co WHERE ru.iUserId='" . $iMemberId . "' AND cu.vName = ru.vCurrencyPassenger AND co.vCountryCode = ru.vCountry");
            $lang = $userData[0]['vLang'];
            $priceRatio = $userData[0]['Ratio'];
            $vSymbol = $userData[0]['vSymbol'];
            $vCurrencyPassenger = $userData[0]['vCurrencyPassenger'];
        }
        else {
            //added by SP on 07-12-2020 for manualbooking from web
            $vCurrencyPassenger = get_value('currency', 'vName', 'eDefault', 'Yes', '', 'true');
            $priceRatio = get_value('currency', 'Ratio', 'vName', $vCurrencyPassenger, '', 'true');
            $vSymbol = get_value('currency', 'vSymbol', 'vName', $vCurrencyPassenger, '', 'true');
            $lang = $LANG_OBJ->FetchDefaultLangData("vCode");
        }
        $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($lang, "1");
        /* $getVehicleTypeData = $obj->MySQLSelect("SELECT vt.iVehicleTypeId,vt.tTypeDesc,vt.iVehicleCategoryId,vt.vVehicleType_" . $lang . " AS vVehicleType, vt.eFareType, vt.fFixedFare, vt.fPricePerHour, vt.fPricePerKM, vt.fPricePerMin, vt.iBaseFare,vt.fCommision, vt.iMinFare,vt.iPersonSize, vt.vLogo as vVehicleTypeImage, vt.eType, vt.eIconType, vt.eAllowQty, vt.fMinHour, vt.iMaxQty, vt.iVehicleTypeId, fFixedFare,vt.fTimeSlot,vt.fTimeSlotPrice, (SELECT vcs.ePriceType FROM vehicle_category as vcs WHERE vcs.iVehicleCategoryId = vc.iParentId) as ParentPriceType, IF(ParentPriceType='Provider', (SELECT spa.fAmount from service_pro_amount as spa WHERE spa.iVehicleTypeId=vt.iVehicleTypeId AND spa.iDriverVehicleId =4), NULL) as ProviderPrice FROM vehicle_type vt, vehicle_category as vc WHERE vt.iVehicleCategoryId >0 AND vt.iVehicleTypeId='" . $iVehicleTypeId . "' AND vt.eStatus='Active' AND vc.iVehicleCategoryId = vt.iVehicleCategoryId"); */
        $getVehicleTypeData = $obj->MySQLSelect("SELECT vt.iVehicleTypeId,vt.tTypeDesc,vt.iVehicleCategoryId,vt.vVehicleType_" . $lang . " AS vVehicleType, vt.eFareType, vt.fFixedFare, vt.fPricePerHour, vt.fPricePerKM, vt.fPricePerMin, vt.iBaseFare,vt.fCommision, vt.iMinFare,vt.iPersonSize, vt.vLogo as vVehicleTypeImage, vt.eType, vt.eIconType, vt.eAllowQty, vt.fMinHour, vt.iMaxQty, vt.iVehicleTypeId, vt.fTimeSlot,vt.fTimeSlotPrice, (SELECT vcs.ePriceType FROM vehicle_category as vcs WHERE vcs.iVehicleCategoryId = vc.iParentId) as ParentPriceType, IF((SELECT vcs.ePriceType FROM vehicle_category as vcs WHERE vcs.iVehicleCategoryId = vc.iParentId)='Provider', (SELECT spa.fAmount from service_pro_amount as spa WHERE spa.iVehicleTypeId=vt.iVehicleTypeId AND spa.iDriverVehicleId =(SELECT dv.iDriverVehicleId FROM driver_vehicle as dv WHERE dv.iDriverId='" . $iDriverId . "' AND dv.eType='UberX' AND dv.eStatus='Active')), NULL) as ProviderPrice, IF(vt.iLocationid != -1, (SELECT co.eUnit FROM country as co, location_master as lm WHERE co.iCountryId = lm.iCountryId AND lm.iLocationid = vt.iLocationid), '" . $DEFAULT_DISTANCE_UNIT . "') as LocationUnit, vc.eVideoConsultEnable,vc.eVideoConsultServiceCharge,vc.vCategory_$lang as vCategory,vc.eVideoServiceDescription FROM vehicle_type vt, vehicle_category as vc WHERE vt.iVehicleCategoryId >0 AND vt.iVehicleTypeId='" . $iVehicleTypeId . "' AND vt.eStatus='Active' AND vc.iVehicleCategoryId = vt.iVehicleCategoryId");
        if ($MODULES_OBJ->isEnableVideoConsultingService() && $eForVideoConsultation == "Yes") {
            $getVehicleTypeData = $obj->MySQLSelect("SELECT eVideoConsultEnable, eVideoConsultServiceCharge, vCategory_$lang as vCategory,eVideoServiceDescription FROM vehicle_category WHERE iVehicleCategoryId = '$iVehicleTypeId'");
        }
        if (count($getVehicleTypeData) > 0) {
            for ($r = 0; $r < count($getVehicleTypeData); $r++) {
                $catId = $getVehicleTypeData[$r]['iVehicleCategoryId'];
                /* echo "<pre>";
                print_r($getVehicleTypeData);exit; */
                ########################################## Check Fare Of Provider ##########################################
                if ($getVehicleTypeData[$r]['ProviderPrice'] != NULL) {
                    $getVehicleTypeData[$r]['fFixedFare'] = $getVehicleTypeData[$r]['ProviderPrice'];
                    $getVehicleTypeData[$r]['fPricePerHour'] = $getVehicleTypeData[$r]['ProviderPrice'];
                }
                unset($getVehicleTypeData[$r]['ProviderPrice']);
                unset($getVehicleTypeData[$r]['ParentPriceType']);
                ########################################## Check Fare Of Provider ##########################################
                $tTypeDescription = "";
                $tTypeDesc = json_decode($getVehicleTypeData[$r]['tTypeDesc'], true);
                if (isset($tTypeDesc['tTypeDesc_' . $lang]) && $tTypeDesc['tTypeDesc_' . $lang] != "") {
                    $tTypeDescription = $tTypeDesc['tTypeDesc_' . $lang];
                }
                $fFixedFare_value = setTwoDecimalPoint($getVehicleTypeData[$r]['fFixedFare'] * $priceRatio);
                $getVehicleTypeData[$r]['fFixedFare_value'] = $fFixedFare_value;
                //$getVehicleTypeData[$r]['fFixedFare'] = $vSymbol . formatNum($fFixedFare_value);
                $getVehicleTypeData[$r]['fFixedFare'] = formateNumAsPerCurrency($fFixedFare_value, $vCurrencyPassenger);
                if ($getVehicleTypeData[$r]['fMinHour'] == 0) {
                    $getVehicleTypeData[$r]['fMinHour'] = 1;
                }
                $getVehicleTypeData[$r]['fPricePerHourOrig'] = $getVehicleTypeData[$r]['fPricePerHour'];
                $getVehicleTypeData[$r]['fPricePerHour'] = $getVehicleTypeData[$r]['fPricePerHour'] * $getVehicleTypeData[$r]['fMinHour'];
                $fPricePerHour_value = setTwoDecimalPoint($getVehicleTypeData[$r]['fPricePerHour'] * $priceRatio);
                $fPricePerHourOrig_value = setTwoDecimalPoint($getVehicleTypeData[$r]['fPricePerHourOrig'] * $priceRatio);
                $getVehicleTypeData[$r]['fPricePerHour_value'] = $fPricePerHour_value;
                //$getVehicleTypeData[$r]['fPricePerHour'] = $vSymbol . formatNum($fPricePerHour_value);
                $getVehicleTypeData[$r]['fPricePerHour'] = formateNumAsPerCurrency($fPricePerHour_value, $vCurrencyPassenger);
                $getVehicleTypeData[$r]['fPricePerHourOrig'] = formateNumAsPerCurrency($fPricePerHourOrig_value, $vCurrencyPassenger);
                // echo "Unit:".$getVehicleTypeData[$r]['LocationUnit'];exit;
                if ($userData[0]['eUnit'] != "KMs" && $getVehicleTypeData[$r]['LocationUnit'] == "KMs") {
                    $getVehicleTypeData[$r]['fPricePerKM'] = $getVehicleTypeData[$r]['fPricePerKM'] * 0.621371;
                }
                else if ($userData[0]['eUnit'] == "KMs" && $getVehicleTypeData[$r]['LocationUnit'] == "Miles") {
                    $getVehicleTypeData[$r]['fPricePerKM'] = $getVehicleTypeData[$r]['fPricePerKM'] * 1.60934;
                }
                $fPricePerKM = $getVehicleTypeData[$r]['fPricePerKM'];
                $fPricePerKM = setTwoDecimalPoint($fPricePerKM * $priceRatio);
                $getVehicleTypeData[$r]['fPricePerKM'] = formateNumAsPerCurrency($fPricePerKM, $vCurrencyPassenger);
                $fPricePerMin = setTwoDecimalPoint($getVehicleTypeData[$r]['fPricePerMin'] * $priceRatio);
                $getVehicleTypeData[$r]['fPricePerMin'] = formateNumAsPerCurrency($fPricePerMin, $vCurrencyPassenger);
                $iBaseFare = setTwoDecimalPoint($getVehicleTypeData[$r]['iBaseFare'] * $priceRatio);
                $getVehicleTypeData[$r]['iBaseFare'] = formateNumAsPerCurrency($iBaseFare, $vCurrencyPassenger);
                $fCommision = setTwoDecimalPoint($getVehicleTypeData[$r]['fCommision'] * $priceRatio);
                $getVehicleTypeData[$r]['fCommision'] = formateNumAsPerCurrency($fCommision, $vCurrencyPassenger);
                $iMinFare = setTwoDecimalPoint($getVehicleTypeData[$r]['iMinFare'] * $priceRatio);
                $getVehicleTypeData[$r]['iMinFare'] = formateNumAsPerCurrency($iMinFare, $vCurrencyPassenger);
                $getVehicleTypeData[$r]['vSymbol'] = $vSymbol;
                // $getVehicleTypeData[$r]['vCategoryDesc'] = $tTypeDescription;
                $tTypeDescriptionUrl = $tconfig['tsite_url'] . 'service_description_app.php?iVehicleTypeId=' . $getVehicleTypeData[$r]['iVehicleTypeId'] . '&vLang=' . $lang;
                $getVehicleTypeData[$r]['vCategoryDesc'] = empty($tTypeDescription) ? "" : $tTypeDescriptionUrl;
                $getVehicleTypeData[$r]['vRating'] = "0.00";
                unset($getVehicleTypeData[$r]['tTypeDesc']);
                $tripFareDetailsArr = array();
                $i = 0;
                if ($getVehicleTypeData[$r]['eFareType'] == "Regular") {
                    $fareDetailsKey = 0;
                    $tripFareDetailsArr[$fareDetailsKey][$languageLabelsArr['LBL_BASE_FARE_SMALL_TXT']] = formateNumAsPerCurrency($iBaseFare, $vCurrencyPassenger);
                    $fareDetailsKey++;

                    $tripFareDetailsArr[$fareDetailsKey]['eDisplaySeperator'] = "Yes";
                    $fareDetailsKey++;

                    $tripFareDetailsArr[$fareDetailsKey][$languageLabelsArr['LBL_PRICE_PER_MINUTE']] = formateNumAsPerCurrency($fPricePerMin, $vCurrencyPassenger);
                    $fareDetailsKey++;
    
                    $tripFareDetailsArr[$fareDetailsKey]['eDisplaySeperator'] = "Yes";
                    $fareDetailsKey++;

                    $tripFareDetailsArr[$fareDetailsKey][$languageLabelsArr[$userData[0]['eUnit'] == "KMs" ? 'LBL_PRICE_PER_KM' : 'LBL_PRICE_PER_MILES']] = formateNumAsPerCurrency($fPricePerKM, $vCurrencyPassenger);

                    if ($SelectedCabType != "UberX") {
                        $tripFareDetailsArr[$fareDetailsKey]['eDisplaySeperator'] = "Yes";
                        $fareDetailsKey++;
                        
                        $tripFareDetailsArr[$fareDetailsKey][$languageLabelsArr['LBL_ESTIMATED_CHARGE']] = formateNumAsPerCurrency($iBaseFare, $vCurrencyPassenger);
                    }
                }
                else if ($getVehicleTypeData[$r]['eFareType'] == "Hourly") {
                    $fareDetailsKey = 0;
                    $tmp_min_hour_charges = formateNumAsPerCurrency($fPricePerHour_value, $vCurrencyPassenger);
                    //$tripFareDetailsArr[][$languageLabelsArr['LBL_SERVICE_CHARGE_PER_HOUR']] = $getVehicleTypeData[$r]['fPricePerHourOrig'];
                    $tripFareDetailsArr[$fareDetailsKey][$languageLabelsArr['LBL_SERVICE_CHARGE_PER_HOUR']] = $vSymbol . formatNum($fPricePerHourOrig_value);
                    $fareDetailsKey++;

                    $tripFareDetailsArr[$fareDetailsKey]['eDisplaySeperator'] = "Yes";
                    $fareDetailsKey++;

                    $tripFareDetailsArr[$fareDetailsKey][$languageLabelsArr['LBL_MINIMUM'] . " " . $languageLabelsArr['LBL_HOUR']] = $getVehicleTypeData[$r]['fMinHour'];
                    
                    $CURRENCY_DATA_ARR_FORMATTER_TMP = $obj->MySQLSelect("SELECT eReverseformattingEnable,vSymbol,vName,eReverseSymbolEnable,eDefault from  `currency` WHERE vName = '" . $vCurrencyPassenger . "'");
                    $tripFareDetailsArr[$fareDetailsKey]['eReverseformattingEnable'] = "Yes";
                    $fareDetailsKey++;

                    /*if ($CURRENCY_DATA_ARR_FORMATTER_TMP[0]['eReverseformattingEnable'] == 'Yes') {
                        $tripFareDetailsArr[1]['eReverseformattingEnable'] = $CURRENCY_DATA_ARR_FORMATTER_TMP[0]['eReverseformattingEnable'];
                    }*/
                    // $tripFareDetailsArr[1]['Extra Price Slot (' . $getVehicleTypeData[$r]['fTimeSlot'] . ' Min)'] = $vSymbol . " " . formatNum($getVehicleTypeData[$r]['fTimeSlotPrice']);
                    // $tripFareDetailsArr[2][$languageLabelsArr['LBL_BASE_FARE_SMALL_TXT']] = $vSymbol . " " . formatNum($iBaseFare);
                    $tripFareDetailsArr[$fareDetailsKey]['eDisplaySeperator'] = "Yes";
                    $fareDetailsKey++;
                    // $tripFareDetailsArr[3][$languageLabelsArr['LBL_MIN_CHARGE_TXT']] = $vSymbol . " " . formatNum($iBaseFare + $iMinFare);
                    //$tripFareDetailsArr[][$languageLabelsArr['LBL_ESTIMATED_CHARGE']] = $tmp_min_hour_charges;
                    $tripFareDetailsArr[$fareDetailsKey][$languageLabelsArr['LBL_ESTIMATED_CHARGE']] = $vSymbol . formatNum($fPricePerHour_value);
                }
                else {
                    //if($getVehicleTypeData[$r]['eAllowQty'] == 'Yes'){
                    $fareDetailsKey = 0;
                    $tripFareDetailsArr[$fareDetailsKey][$languageLabelsArr['LBL_SERVICE_CHARGE']] = $vSymbol . formatNum($fFixedFare_value);
                    //} else {
                    //$tripFareDetailsArr[0][$languageLabelsArr['LBL_SERVICE_CHARGE']] = formateNumAsPerCurrency($fFixedFare_value,$vCurrencyPassenger);
                    //}
                }
                if ($MODULES_OBJ->isEnableVideoConsultingService() && $eForVideoConsultation == "Yes") {
                    $getVehicleTypeData[$r]['vVehicleType'] = $getVehicleTypeData[$r]['vCategory'];
                    $getVehicleTypeData[$r]['iVehicleTypeId'] = $iVehicleTypeId;
                    $tripFareDetailsArr = "";
                    if ($getVehicleTypeData[$r]['eVideoConsultEnable'] == "Yes") {
                        $getVehicleTypeData[$r]['fFixedFare'] = $getVehicleTypeData[$r]['fFixedFare_value'] = $getVehicleTypeData[$r]['fPricePerHour'] = "";
                        $video_consult_data = $VIDEO_CONSULT_OBJ->getServiceDetails($iDriverId, $iVehicleTypeId);
                        // $getVehicleTypeData[$r]['vCategoryDesc'] = $getVehicleTypeData[$r]['eVideoServiceDescription'];
                        $getVehicleTypeData[$r]['vCategoryDesc'] = "";
                        if ($video_consult_data['eVideoConsultEnableProvider'] == "Yes") {
                            $getVehicleTypeData[$r]['eVideoConsultServiceCharge'] = setTwoDecimalPoint($video_consult_data['eVideoConsultServiceCharge'] * $priceRatio);
                            // $getVehicleTypeData[$r]['vCategoryDesc'] = $video_consult_data['eVideoServiceDescription'];
                            $tTypeDescriptionUrl = $tconfig['tsite_url'] . 'service_description_app.php?iVehicleTypeId=' . $iVehicleTypeId . '&vLang=' . $lang . '&eForVideoConsultation=Yes&iDriverId=' . $iDriverId;
                            $getVehicleTypeData[$r]['vCategoryDesc'] = empty($tTypeDescription) ? "" : $tTypeDescriptionUrl;
                        }
                    }
                }
                $getVehicleTypeData[$r]['fareDetails'] = $tripFareDetailsArr;
                //echo "<pre>";print_r($getVehicleTypeData);die;
            }
            $getVehicleTypeData = $getVehicleTypeData[0];
        }
        if (($getVehicleTypeData['eAllowQty'] == "Yes" && $getVehicleTypeData['iMaxQty'] < 2) || $getVehicleTypeData['eFareType'] == "Regular" || $getVehicleTypeData['eFareType'] == "Hourly") {
            $getVehicleTypeData['eAllowQty'] = "No";
        }
        /*if($MODULES_OBJ->isEnableVideoConsultingService() && $eForVideoConsultation == "Yes") {
            $vehicleCategoryData = $obj->MySQLSelect("SELECT vCategory_$lang as vCategory FROM vehicle_category WHERE iVehicleCategoryId = '$iVehicleCategoryId'");
            $getVehicleTypeData['vVehicleType'] = $vehicleCategoryData[0]['vCategory'];
        }*/
        $returnArr['Action'] = "1";
        $returnArr['message'] = $getVehicleTypeData;
        setDataResponse($returnArr);
    }
}
//Added By HJ On 25-01-2019 For Get Service Details End For UFX
//Added By HJ On 31-01-2019 For Get Vehicle Type Fare Details Start
if ($type == "getVehicleTypeFareDetails") {
    $OrderDetails = isset($_REQUEST['OrderDetails']) ? $_REQUEST['OrderDetails'] : array();
    $iMemberId = isset($_REQUEST['iMemberId']) ? clean($_REQUEST['iMemberId']) : '';
    $couponCode = isset($_REQUEST['vCouponCode']) ? clean($_REQUEST['vCouponCode']) : '';
    $UserType = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : 'Passenger';
    $iDriverId = isset($_REQUEST["iDriverId"]) ? $_REQUEST["iDriverId"] : 0;
    $iUserAddressId = isset($_REQUEST["iUserAddressId"]) ? $_REQUEST["iUserAddressId"] : '';
    $eForVideoConsultation = isset($_REQUEST["eForVideoConsultation"]) ? $_REQUEST['eForVideoConsultation'] : 'No';
    $tripFareDetailsArr = array();
    $fareDetails = getVehicleTypeFareDetails();
    if (isset($fareDetails['tripFareDetailsArr'])) {
        $tripFareDetailsArr = $fareDetails['tripFareDetailsArr'];
    }
    if (isset($fareDetails['vehiclePriceTypeArrItems'])) {
        $vehiclePriceTypeArrItems = $fareDetails['vehiclePriceTypeArrItems'];
    }
    if (isset($fareDetails['vehiclePriceTypeArrCubex'])) {
        $vehiclePriceTypeArrCubex = $fareDetails['vehiclePriceTypeArrCubex'];
    }
    $totalAddressCount = 0;
    $vServiceFullAddress = $vServiceAddressLatitude = $vServiceAddressLongitude = "";
    if ($iMemberId > 0) {
        $cmpMinutes = ceil((fetchtripstatustimeMAXinterval() + $intervalmins) / 60);
        $str_date = @date('Y-m-d H:i:s', strtotime('-' . $cmpMinutes . ' minutes'));
        $ssql_address = "";
        if ($iUserAddressId != "") {
            $ssql_address = " AND iUserAddressId = " . $iUserAddressId;
        }
        if (!empty($iUserAddressId)) {
            $sql = "SELECT iUserAddressId FROM `user_address` WHERE iUserAddressId = '" . $iUserAddressId . "' AND eStatus='Active'";
            $data_user_address_data = $obj->MySQLSelect($sql);
            if (empty($data_user_address_data) || count($data_user_address_data) == 0) {
                $iUserAddressId = "";
            }
        }
        $getAddressCount = $obj->MySQLSelect("SELECT iUserAddressId,vServiceAddress,vAddressType,vBuildingNo,vLandmark,vLatitude,vLongitude FROM `user_address` WHERE iUserId='" . $iMemberId . "' AND eStatus='Active' " . $ssql_address . " AND vLatitude != '' AND vLongitude != '' AND vServiceAddress != '' ORDER BY iUserAddressId DESC");
        if (isset($Check_Driver_UFX) && $Check_Driver_UFX == "No") {
            $ssql_available .= " AND vAvailability = 'Available' AND vTripStatus != 'Active' AND tLocationUpdateDate > '$str_date' ";
        }
        $getLocaionData = $obj->MySQLSelect("SELECT eSelectWorkLocation,vLatitude,vLongitude,vWorkLocationLatitude,vWorkLocationLongitude,vWorkLocationRadius,vAvailability,vTripStatus,tLocationUpdateDate, eEnableServiceAtLocation FROM register_driver WHERE iDriverId='" . $iDriverId . "'");
        $eEnableServiceAtLocation = $getLocaionData[0]['eEnableServiceAtLocation'];
        $startDate = date("Y-m-d H:i:s");
        $isAvailabel = "No";
        for ($e = 0; $e < count($getLocaionData); $e++) {
            $vAvailability = $getLocaionData[$e]['vAvailability'];
            $vTripStatus = $getLocaionData[$e]['vTripStatus'];
            $tLocationUpdateDate = $getLocaionData[$e]['tLocationUpdateDate'];
            //echo $tLocationUpdateDate . " > " . $startDate . "<br>";
            if ($vAvailability == "Available" && $vTripStatus != "Active" && $tLocationUpdateDate > $str_date) {
                $isAvailabel = "Yes";
            }
        }
        if (count($getAddressCount) > 0) {
            $totalAddressCount = count($getAddressCount);
            if ($SERVICE_PROVIDER_FLOW == "Provider" && $iDriverId > 0) {
                if (count($getLocaionData) > 0) {
                    $vLatitude = $getLocaionData[0]['vLatitude'];
                    $vLongitude = $getLocaionData[0]['vLongitude'];
                    $eSelectWorkLocation = $getLocaionData[0]['eSelectWorkLocation'];
                    $vWorkLocationRadius = $RESTRICTION_KM_NEAREST_TAXI;
                    if (isset($getLocaionData[0]['vWorkLocationRadius']) && $getLocaionData[0]['vWorkLocationRadius'] > 0) {
                        $vWorkLocationRadius = $getLocaionData[0]['vWorkLocationRadius'];
                    }
                    if ($eSelectWorkLocation == "Fixed") {
                        $vLatitude = $getLocaionData[0]['vWorkLocationLatitude'];
                        $vLongitude = $getLocaionData[0]['vWorkLocationLongitude'];
                    }
                }
                $isRemoveAddressFromList = $addressArr = array();
                for ($r = 0; $r < count($getAddressCount); $r++) {
                    $userLat = $getAddressCount[$r]['vLatitude'];
                    $userLang = $getAddressCount[$r]['vLongitude'];
                    $distance = distanceByLocation($vLatitude, $vLongitude, $userLat, $userLang, "K");
                    if ($distance <= $vWorkLocationRadius) {
                        $isRemoveAddressFromList[] = 1;
                        $addressArr[] = $getAddressCount[$r];
                    }
                }
                if (in_array(1, $isRemoveAddressFromList)) {
                    $getAddressCount[$r]['eLocatonAvailable'] = $isRemoveAddressFromList;
                }
                else {
                    $getAddressCount = array();
                    //$totalAddressCount = 0; //commented bc when distance is greater than locationradius at that time if 0 given then no data in app, so commented so in app disable address are shown
                }
            }
            if (count($addressArr) > 0) {
                $getAddressCount = $addressArr;
            }
            if (count($getAddressCount) > 0) {
                //$totalAddressCount = $getAddressCount[0]['Total'];
                $iUserAddressId = $getAddressCount[0]['iUserAddressId'];
                $vServiceAddress = trim($getAddressCount[0]['vServiceAddress']);
                $vServiceAddressLatitude = trim($getAddressCount[0]['vLatitude']);
                $vServiceAddressLongitude = trim($getAddressCount[0]['vLongitude']);
                $vAddressType = trim($getAddressCount[0]['vAddressType']);
                $vBuildingNo = trim($getAddressCount[0]['vBuildingNo']);
                $vLandmark = trim($getAddressCount[0]['vLandmark']);
                $vServiceFullAddress = ($vAddressType != "") ? $vAddressType . "\n" : "";
                $vServiceFullAddress .= ($vBuildingNo != "") ? $vBuildingNo . "," : "";
                $vServiceFullAddress .= ($vLandmark != "") ? $vLandmark . "\n" : "";
                $vServiceFullAddress .= ($vServiceAddress != "") ? $vServiceAddress : "";
            }
            else {
                if (strtoupper($_REQUEST['GeneralDeviceType']) == "IOS" && $eForVideoConsultation == "Yes") {
                    $addrData = getaddress($_REQUEST['vSelectedLatitude'], $_REQUEST['vSelectedLongitude']);
                    $vServiceFullAddress = "Video Address";
                    $vServiceAddressLatitude = $_REQUEST['vSelectedLatitude'];
                    $vServiceAddressLongitude = $_REQUEST['vSelectedLongitude'];
                    $iUserAddressId = $totalAddressCount = "1";
                }
            }
        }
        else {
            if (strtoupper($_REQUEST['GeneralDeviceType']) == "IOS" && $eForVideoConsultation == "Yes") {
                $addrData = getaddress($_REQUEST['vSelectedLatitude'], $_REQUEST['vSelectedLongitude']);
                $vServiceFullAddress = "Video Address";
                $vServiceAddressLatitude = $_REQUEST['vSelectedLatitude'];
                $vServiceAddressLongitude = $_REQUEST['vSelectedLongitude'];
                $iUserAddressId = $totalAddressCount = "1";
            }
        }
    }
    $returnArr['iUserAddressId'] = $iUserAddressId;
    $returnArr['vServiceAddress'] = $vServiceFullAddress;
    $returnArr['vServiceAddressLatitude'] = $vServiceAddressLatitude;
    $returnArr['vServiceAddressLongitude'] = $vServiceAddressLongitude;
    $returnArr['eEnableServiceAtProviderLocation'] = $fareDetails['eFareTypeServices'] == "Regular" ? "No" : $eEnableServiceAtLocation;
    if ($PROVIDER_AVAIL_LOC_CUSTOMIZE == 'No' && $returnArr['eEnableServiceAtProviderLocation'] != 'No') {
        $returnArr['eEnableServiceAtProviderLocation'] = 'No';
    }
    if ($MODULES_OBJ->isEnableVideoConsultingService() && $eForVideoConsultation == "Yes") {
        $returnArr['eEnableServiceAtProviderLocation'] = 'No';
    }
    $returnArr['totalAddressCount'] = $totalAddressCount;
    $returnArr['vAvailability'] = $isAvailabel;
    $returnArr['vScheduleAvailability'] = isProviderEligibleForScheduleJob($iDriverId) == false ? "No" : "Yes";
    $returnArr['Action'] = "1";
    $returnArr['message'] = $tripFareDetailsArr;
    $returnArr['items'] = $vehiclePriceTypeArrItems;
    $returnArr['vehiclePriceTypeArrCubex'] = $vehiclePriceTypeArrCubex;
    //$returnArr['eEnableServiceAtProviderLocation'] = 'Yes';
    //print_r($returnArr);die;
    setDataResponse($returnArr);
}
//Added By HJ On 31-01-2019 For Get Vehicle Type Fare Details End
//Added By HJ On 01-02-2019 For Get Driver Special Instruction Start
if ($type == "getSpecialInstructionData") {
    $iCabRequestId = isset($_REQUEST["iCabRequestId"]) ? $_REQUEST["iCabRequestId"] : '';
    $iTripId = isset($_REQUEST["iTripId"]) ? $_REQUEST["iTripId"] : '';
    $iCabBookingId = isset($_REQUEST["iCabBookingId"]) ? $_REQUEST["iCabBookingId"] : '';
    $iMemberId = isset($_REQUEST["iMemberId"]) ? $_REQUEST["iMemberId"] : '';
    $UserType = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : '';
    if ($iTripId > 0) {
        $tableName = "trips";
        $whereCond = "iTripId ='" . $iTripId . "'";
    }
    else if ($iCabBookingId > 0) {
        $tableName = "cab_booking";
        $whereCond = "iCabBookingId ='" . $iCabBookingId . "'";
    }
    else {
        $tableName = "cab_request_now";
        $whereCond = "iCabRequestId ='" . $iCabRequestId . "'";
    }
    $lang = "";
    if ($UserType == "Driver") {
        $lang = get_value('register_driver', 'vLang', 'iDriverId', $iMemberId, '', 'true');
    }
    else {
        $lang = get_value('register_user', 'vLang', 'iUserId', $iMemberId, '', 'true');
    }
    if ($lang == "") {
        //$lang = get_value('language_master', 'vCode', 'eDefault', 'Yes', '', 'true');
        //Added By HJ On 22-07-2020 For Optimize language_master Table Query Start
        $lang = $LANG_OBJ->FetchDefaultLangData("vCode");
        //Added By HJ On 22-07-2020 For Optimize language_master Table Query End
    }
    $getData = $obj->MySQLSelect("SELECT tVehicleTypeFareData, tVehicleTypeData, isVideoCall FROM " . $tableName . " WHERE $whereCond");
    $instructionArr = $vehicleTypeNameArr = $typeQtyArr = $typeNameArr = array();
    $iVehicleTypeIds = "";
    if (count($getData) > 0) {
        $getData[0]['tVehicleTypeFareData'] = preg_replace('/[[:cntrl:]]/', '\r\n', $getData[0]['tVehicleTypeFareData']);
        $tVehicleTypeFareData = json_decode($getData[0]['tVehicleTypeFareData'], true);
        $tVehicleTypeFareData = $tVehicleTypeFareData['FareData'];
        $replacedata = preg_replace('/[[:cntrl:]]/', '\r\n', $getData[0]['tVehicleTypeData']); //apply this when from app enter key is used in special instruction
        $tVehicleTypeData = json_decode($replacedata, true);
        //Added By HJ On 10-12-2019 For Get User Comment From json Data End
        for ($h = 0; $h < count($tVehicleTypeData); $h++) {
            $typeQtyArr[$tVehicleTypeData[$h]['iVehicleTypeId']] = $tVehicleTypeData[$h];
        }
        $iVehicleTypeIds_str = "";
        for ($fd = 0; $fd < count($tVehicleTypeFareData); $fd++) {
            $iVehicleTypeIds_str = $iVehicleTypeIds_str == "" ? $tVehicleTypeFareData[$fd]['id'] : $iVehicleTypeIds_str . "," . $tVehicleTypeFareData[$fd]['id'];
        }
        if ((empty($tVehicleTypeFareData) || count($tVehicleTypeFareData) > 0) && !empty($getData[0]['iVehicleTypeId']) && empty($iVehicleTypeIds_str)) {
            $iVehicleTypeIds_str = $getData[0]['iVehicleTypeId'];
        }
        $sql_vehicleTypeNames = "SELECT vt.vVehicleType_" . $lang . " as vVehicleType,vt.iVehicleTypeId,vt.iVehicleCategoryId,vc.vCategory_$lang as vCategory FROM vehicle_type as vt LEFT JOIN vehicle_category as vc ON vc.iVehicleCategoryId = vt.iVehicleCategoryId WHERE vt.iVehicleTypeId IN ($iVehicleTypeIds_str)";
        if ($getData[0]['isVideoCall'] == "Yes") {
            $sql_vehicleTypeNames = "SELECT vCategory_$lang as vCategory, iVehicleCategoryId as iVehicleTypeId FROM vehicle_category WHERE iVehicleCategoryId IN ($iVehicleTypeIds_str)";
        }
        $data_vehicleTypeNames = $obj->MySQLSelect($sql_vehicleTypeNames);
        for ($k = 0; $k < count($data_vehicleTypeNames); $k++) {
            $typeNameArr[$data_vehicleTypeNames[$k]['iVehicleTypeId']] = $data_vehicleTypeNames[$k]['vVehicleType'];
            if ($getData[0]['isVideoCall'] == "Yes") {
                $typeNameArr[$data_vehicleTypeNames[$k]['iVehicleTypeId']] = $data_vehicleTypeNames[$k]['vCategory'];
            }
        }
        // echo "<pre>"; print_r($typeNameArr); exit;
        for ($t = 0; $t < count($tVehicleTypeData); $t++) {
            $iVehicleTypeIds .= "," . $tVehicleTypeData[$t]['iVehicleTypeId'];
            $commentDataArr = array();
            $qtyType = 0;
            $vTypeName = "";
            if (isset($typeQtyArr[$tVehicleTypeData[$t]['iVehicleTypeId']])) {
                $qtyType = $typeQtyArr[$tVehicleTypeData[$t]['iVehicleTypeId']]['fVehicleTypeQty'];
            }
            if (isset($typeNameArr[$tVehicleTypeData[$t]['iVehicleTypeId']])) {
                $vTypeName = $typeNameArr[$tVehicleTypeData[$t]['iVehicleTypeId']];
            }
            $commentDataArr['iVehicleTypeId'] = $tVehicleTypeData[$t]['iVehicleTypeId'];
            $commentDataArr['title'] = $vTypeName;
            for ($fd = 0; $fd < count($tVehicleTypeFareData); $fd++) {
                if ($tVehicleTypeData[$t]['iVehicleTypeId'] == $tVehicleTypeFareData[$fd]['id']) {
                    if ($tVehicleTypeFareData[$fd]['eAllowQty'] == "Yes") {
                        // $commentDataArr['Qty'] = "x" . $qtyType;
                        $commentDataArr['Qty'] = "";
                    }
                    else {
                        $commentDataArr['Qty'] = "";
                    }
                    break;
                }
            }
            $commentDataArr['comment'] = $tVehicleTypeData[$t]['tUserComment'];
            $instructionArr[] = $commentDataArr;
        }
    }
    $returnArr['Action'] = "1";
    $returnArr['message'] = $instructionArr;
    //print_r($returnArr);die;
    setDataResponse($returnArr);
    //print_r($instructionArr);die;
}
//Added By HJ On 05-02-2019 For Get Driver Availability For Later Booking Service Start
if ($type == "getDriverAvailability") {
    $iDriverId = isset($_REQUEST['iDriverId']) ? clean($_REQUEST['iDriverId']) : '';
    $UserType = isset($_REQUEST['UserType']) ? clean($_REQUEST['UserType']) : '';
    //Added By HJ On 08-08-2020 For Unset Driver Time Slot If already Booked By User Start
    $getAvalabilityData = $removeSlotArr = array();
    $systemTimeZone = date_default_timezone_get();
    $data_drv = $obj->MySQLSelect("SELECT * FROM cab_booking WHERE eStatus='Accepted' AND iDriverId='" . $iDriverId . "'");
    $setcurrentTime = strtotime(date('Y-m-d H:i:s'));
    for ($i = 0; $i < count($data_drv); $i++) {
        $vTimeZone = $data_drv[$i]['vTimeZone'];
        $bookingdate = date("Y-m-d H:i", strtotime('+30 minutes', strtotime($data_drv[$i]['dBooking_date'])));
        $bookingdatecmp = strtotime($bookingdate);
        //echo $data_drv[$i]['dBooking_date']."<br>";
        if ($bookingdatecmp > $setcurrentTime && $data_drv[$i]['iDriverId'] > 0) {
            if ($data_drv[$i]['dBooking_date'] != "" && $data_drv[$i]['vTimeZone'] != "") {
                $dBookingDate = converToTz($data_drv[$i]['dBooking_date'], $data_drv[$i]['vTimeZone'], $systemTimeZone);
            }
            else {
                $dBookingDate = $data_drv[$i]['dBooking_date'];
            }
            $dayname = date('l', strtotime($dBookingDate));
            $fromSlotHour = date("h", strtotime($dBookingDate));
            $amPmSlot = date("A", strtotime($dBookingDate));
            if ($data_drv[$i]['iCabBookingId'] == 87) {
                //echo $dBookingDate;die;
            }
            $toSlotHour = sprintf("%02d", $fromSlotHour + 1);
            if ($amPmSlot == "PM" && $fromSlotHour != 12) {
                $fromSlotHour += 12;
                $toSlotHour += 12;
            }
            if ($fromSlotHour == 12) {
                $toSlotHour = 13;
            }
            if ($data_drv[$i]['iCabBookingId'] == 87) {
                //echo $amPmSlot."==".$fromSlotHour."==".$toSlotHour;die;
            }
            $timeSlot = $fromSlotHour . "-" . $toSlotHour;
            $currSlotArr = array();
            $currSlotArr['slot'] = $timeSlot;
            $currSlotArr['day'] = $dayname;
            $removeSlotArr[] = $currSlotArr;
        }
    }
    //echo "<pre>";print_r($removeSlotArr);die;
    //Added By HJ On 08-08-2020 For Unset Driver Time Slot If already Booked By User End
    if ($iDriverId > 0) {
        $getAvalabilityData = $obj->MySQLSelect("SELECT vDay,iDriverTimingId,vAvailableTimes FROM driver_manage_timing WHERE eStatus='Active' AND iDriverId='" . $iDriverId . "'");
        //Added By HJ On 08-08-2020 For Unset Driver Time Slot If already Booked By User Start
        for ($g = 0; $g < count($getAvalabilityData); $g++) {
            $timeSlotArr = explode(",", $getAvalabilityData[$g]['vAvailableTimes']);
            $bookedDay = $getAvalabilityData[$g]['vDay'];
            for ($n = 0; $n < count($removeSlotArr); $n++) {
                //echo $bookedDay."===".$removeSlotArr[$n]['day']."<br>";
                if (strtoupper($bookedDay) == strtoupper($removeSlotArr[$n]['day']) && in_array($removeSlotArr[$n]['slot'], $timeSlotArr)) {
                    $key = array_search($removeSlotArr[$n]['slot'], $timeSlotArr);
                    unset($timeSlotArr[$key]);
                }
            }
            //current day time before slot remove
            if (date('l') == $getAvalabilityData[$g]['vDay']) {
                $register_driver = $obj->MySQLSelect("SELECT vTimeZone FROM register_driver WHERE iDriverId='" . $iDriverId . "'");
                if ($register_driver[0]['vTimeZone'] != "") {
                    $h = date('H', strtotime(converToTz(date('Y-m-d H:i:s'), $register_driver[0]['vTimeZone'], $systemTimeZone)));
                }
                else {
                    $h = date('H');
                }
                $key_i = array_search($h . "-" . ($h + 1), $timeSlotArr);
                foreach ($timeSlotArr as $key => $item) {
                    if ($key <= $key_i) {
                        unset($timeSlotArr[$key]);
                    }
                }
                $key1201 = array_search("12-01", $timeSlotArr);
                unset($timeSlotArr[$key1201]);
            }
            $getAvalabilityData[$g]['vAvailableTimes'] = implode(",", $timeSlotArr);
        }
        //echo "<pre>";print_r($timeSlotArr);die;
        //echo "<pre>";print_r($getAvalabilityData);die;
        //Added By HJ On 08-08-2020 For Unset Driver Time Slot If already Booked By User End
    }
    $returnArr['Action'] = "1";
    $returnArr['message'] = $getAvalabilityData;
    setDataResponse($returnArr);
}
//Added By HJ On 05-02-2019 For Get Driver Availability For Later Booking Service End
if ($type == "getProviderServiceDescription") {
    $iDriverId = isset($_REQUEST['iDriverId']) ? clean($_REQUEST['iDriverId']) : '';
    $UserType = isset($_REQUEST['UserType']) ? clean($_REQUEST['UserType']) : '';
    $getDescriptionData = "";
    if ($iDriverId > 0) {
        $getDescriptionData = get_value('register_driver', 'tProfileDescription', 'iDriverId', $iDriverId, '', 'true');
        $returnArr['Action'] = "1";
        $returnArr['message'] = $getDescriptionData;
    }
    if ($getDescriptionData == "") {
        $returnArr['message'] = "LBL_NO_DATA_AVAIL";
        $returnArr['Action'] = "0";
    }
    setDataResponse($returnArr);
}
if ($type == "configureProviderServiceLocation") {
    $iDriverId = isset($_REQUEST['iDriverId']) ? clean($_REQUEST['iDriverId']) : '';
    $UserType = isset($_REQUEST['UserType']) ? clean($_REQUEST['UserType']) : '';
    $eEnableServiceAtLocation = isset($_REQUEST['eEnableServiceAtLocation']) ? clean($_REQUEST['eEnableServiceAtLocation']) : 'No';
    if ($eEnableServiceAtLocation == "") {
        $eEnableServiceAtLocation = "No";
    }
    $where = "iDriverId = '$iDriverId'";
    $updateData['eEnableServiceAtLocation'] = $eEnableServiceAtLocation;
    $obj->MySQLQueryPerform("register_driver", $updateData, 'update', $where);
    $returnArr['Action'] = "1";
    $returnArr['message'] = getDriverDetailInfo($iDriverId);
    setDataResponse($returnArr);
}
function removeInvalidChars($text)
{
    $regex = '/( [\x00-\x7F] | [\xC0-\xDF][\x80-\xBF] | [\xE0-\xEF][\x80-\xBF]{2} | [\xF0-\xF7][\x80-\xBF]{3} ) | ./x';
    return preg_replace($regex, '$1', $text);
}

function cleanString($val)
{
    $non_displayables = array(
        '/%0[0-8bcef]/', # url encoded 00-08, 11, 12, 14, 15
        '/%1[0-9a-f]/', # url encoded 16-31
        '/[\x00-\x08]/', # 00-08
        '/\x0b/', # 11
        '/\x0c/', # 12
        '/[\x0e-\x1f]/', # 14-31
        '/x7F/'                     # 127
    );
    foreach ($non_displayables as $regex) {
        $val = preg_replace($regex, '', $val);
    }
    $search = array("\0", "\r", "\x1a", "\t", "\n");
    return $a = trim(str_replace($search, '', $val));
}

function json_clean_decode($json, $assoc = false, $depth = 512, $options = 0)
{
    // search and remove comments like /* */ and //
    $json = preg_replace("#(/\*([^*]|[\r\n]|(\*+([^*/]|[\r\n])))*\*+/)|([\s\t]//.*)|(^//.*)#", '', $json);
    if (version_compare(phpversion(), '5.4.0', '>=')) {
        return json_decode($json, $assoc, $depth, $options);
    }
    elseif (version_compare(phpversion(), '5.3.0', '>=')) {
        return json_decode($json, $assoc, $depth);
    }
    else {
        return json_decode($json, $assoc);
    }
}
?>