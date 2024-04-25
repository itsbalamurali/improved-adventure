<?php

/**
 * 
 */

class DriverOpsCls
{
	function __construct() {

	}

	public function GetCabRequestAddress($iCabRequestId, $iDriverId, $vLang) {
        global $obj, $iServiceId, $LANG_OBJ, $oCache, $cacheKeysArr;

        $Data_cab_request = $obj->MySQLSelect("SELECT iCabRequestId,iVehicleTypeId,eType,tSourceAddress,tDestAddress,tUserComment,iRentalPackageId,ePayType,ePayWallet,fDistance,fTripGenerateFare,fDuration,iFare,fDiscount,fWalletDebit,eServiceLocation,tVehicleTypeData, iHotelBookingId,vSourceLatitude AS sourceLatitude,vSourceLongitude AS sourceLongitude,vDestLatitude AS destLatitude,vDestLongitude AS destLongitude,iFromStationId,iToStationId,isVideoCall FROM cab_request_now WHERE iCabRequestId = '$iCabRequestId' ");

        if (!empty($Data_cab_request[0]['iFromStationId']) && !empty($Data_cab_request[0]['iToStationId'])) {
            $Data_cab_request[0]['eFly'] = "Yes";
        } else {
            $Data_cab_request[0]['eFly'] = "No";
        }

        $eType = $Data_cab_request[0]['eType'];

        if ($Data_cab_request[0]['iRentalPackageId'] == 0) {
            $Data_cab_request[0]['iRentalPackageId'] = "";
        }
        $iRentalPackageId = $Data_cab_request[0]['iRentalPackageId'];

        if(empty($vLang)) {
            $vLang = get_value('register_driver', 'vLang', 'iDriverId', $iDriverId, '', 'true');
            if ($vLang == "" || $vLang == NULL) {
                $vLang = $LANG_OBJ->FetchDefaultLangData("vCode");
            }
        }
        
        $eServiceLocation = $Data_cab_request[0]['eServiceLocation'];

        $replacedata = preg_replace('/[[:cntrl:]]/', '\r\n', $Data_cab_request[0]['tVehicleTypeData']); 
        $tVehicleTypeData = (array)(json_decode($replacedata));
        $Data_cab_request[0]['moreServices'] = "No";
        if (count($tVehicleTypeData) > 1) {
            $Data_cab_request[0]['moreServices'] = "Yes";
        } else if (!empty($tVehicleTypeData)) {
            $Data_cab_request[0]['moreServices'] = "Yes";
        }

        $langLabelApcKey = md5($cacheKeysArr['language_label_global_config_'] . $iServiceId . "_" . $vLang);
        $getLabelCacheData = $oCache->getData($langLabelApcKey);
        if (!empty($getLabelCacheData) && count($getLabelCacheData) > 0) {
            $languageLabelsArr = $getLabelCacheData;
        } else {
            $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLang, "1", $iServiceId);
            $setLabelCacheData = $oCache->setData($langLabelApcKey, $languageLabelsArr);
        }

        if ($eServiceLocation == "Driver") {
            $Data_cab_request[0]['tSourceAddress'] = $languageLabelsArr['LBL_AT_YOUR_LOCATION'];
        }
        if ($Data_cab_request[0]['isVideoCall'] == "Yes") {
            $Data_cab_request[0]['tSourceAddress'] = $languageLabelsArr['LBL_VIDEO_CONSULT_AT_YOUR_LOC'];
        }

        if ($iRentalPackageId != '') {
            $Data_Rental = $obj->MySQLSelect("SELECT iRentalPackageId,fPrice,vPackageName_" . $vLang . " FROM rental_package WHERE iRentalPackageId = '$iRentalPackageId' ");
            $PackageName = $Data_Rental[0]['vPackageName_' . $vLang];
            
            $Data_cab_request[0]['PackageName'] = $PackageName;
        }
        $sql_vehicle_category_table_name = getVehicleCategoryTblName();

        $iVehicleTypeId = $Data_cab_request[0]['iVehicleTypeId'];
        if ($iVehicleTypeId > 0) {
            $sqlv = "SELECT iVehicleCategoryId,vVehicleType_" . $vLang . " as vVehicleTypeName from vehicle_type WHERE iVehicleTypeId = '" . $iVehicleTypeId . "'";
            $tripVehicleData = $obj->MySQLSelect($sqlv);
            $iVehicleCategoryId = $tripVehicleData[0]['iVehicleCategoryId'];
            $vVehicleTypeName = $tripVehicleData[0]['vVehicleTypeName'];
            if ($iVehicleCategoryId != 0) {
                $vVehicleCategoryName = get_value($sql_vehicle_category_table_name, 'vCategory_' . $vLang, 'iVehicleCategoryId', $iVehicleCategoryId, '', 'true');
                $vVehicleTypeName = $vVehicleCategoryName . "-" . $vVehicleTypeName;
            }
        }
        if (count($tVehicleTypeData) > 0) {
            $getMainCat = $obj->MySQLSelect("SELECT VC.vCategory_" . $vLang . " AS vVehicleCategory,VT.iVehicleCategoryId,if(VC.iParentId >0,(SELECT vCategory_" . $vLang . " FROM " . $sql_vehicle_category_table_name . " VC1 WHERE VC.iParentId=VC1.iVehicleCategoryId),'') AS vVehicleCategory FROM vehicle_type VT INNER JOIN " . $sql_vehicle_category_table_name . " VC ON VT.iVehicleCategoryId=VC.iVehicleCategoryId WHERE iVehicleTypeId='" . $tVehicleTypeData[0]->iVehicleTypeId . "'");
            if (count($getMainCat) > 0) {
                $vVehicleTypeName = $getMainCat[0]['vVehicleCategory'];
            }
        }
        if ($eType == "UberX") {
            $Data_cab_request[0]['SelectedTypeName'] = $vVehicleTypeName;
        }
        $Data_cab_request[0]['VehicleTypeName'] = $vVehicleTypeName;
        /* -------------------------------for multi delivery----------------------------------------- */
        if ($eType == "Multi-Delivery") {
            $db_trip_fields = $obj->MySQLSelect("select iTripDeliveryLocationId from trip_delivery_fields where iCabRequestId = '$iCabRequestId' group by iTripDeliveryLocationId");
            $vCurrencyDriver = get_value('register_driver', 'vCurrencyDriver', 'iDriverId', $iDriverId, '', 'true');
            //$vCurrencyDriver = $data[0]['vCurrencyDriver'];
            if ($vCurrencyDriver == '' || $vCurrencyDriver == NULL) {
                $vCurrencyDriver = get_value('currency', 'vName', 'eDefault', 'Yes', '', 'true');
            }
            $sql = "SELECT Ratio,vSymbol from currency WHERE vName= '" . $vCurrencyDriver . "'";
            $currencydata = $obj->MySQLSelect($sql);
            $priceRatio = $currencydata[0]['Ratio'];
            $vSymbol = $currencydata[0]['vSymbol'];
            $eUnit = getMemberCountryUnit($iDriverId, "Driver");
            $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLang, "1", $iServiceId);
            $DisplayDistanceTxt = $languageLabelsArr['LBL_MILE_DISTANCE_TXT'];
            if ($eUnit == "Miles") {
                $tripDistanceDisplay = $Data_cab_request[0]['fDistance'] * 0.621371;
                $tripDistanceDisplay = round($tripDistanceDisplay, 2);
                $DisplayDistanceTxt = $languageLabelsArr['LBL_MILE_DISTANCE_TXT'];
            } else {
                $tripDistanceDisplay = $Data_cab_request[0]['fDistance'];
                $DisplayDistanceTxt = $languageLabelsArr['LBL_KM_DISTANCE_TXT'];
            }
            $tripDistanceDisplay = $tripDistanceDisplay . " " . $DisplayDistanceTxt;
            $hours = floor($Data_cab_request[0]['fDuration'] / 60); // No. of mins/60 to get the hours and round down
            $mins = $Data_cab_request[0]['fDuration'] % 60; // No. of mins/60 - remainder (modulus) is the minutes
            if ($hours >= 1) {
                $tripDurationDisplay = $hours . " " . $languageLabelsArr['LBL_HOURS_TXT'] . ", " . $mins . " " . $languageLabelsArr['LBL_MINUTES_TXT'];
            } else {
                $tripDurationDisplay = $Data_cab_request[0]['fDuration'] . " " . $languageLabelsArr['LBL_MINUTES_TXT'];
            }
            $Data_cab_request[0]['Total_Delivery'] = count($db_trip_fields);
            $fTripGenerateFare = (($Data_cab_request[0]['fTripGenerateFare'] - $Data_cab_request[0]['fDiscount'] - $Data_cab_request[0]['fWalletDebit']) * $priceRatio);
            $fTripGenerateFare = round($fTripGenerateFare, 2);
            //$fTripGenerateFare = $vSymbol . " " . formatNum($fTripGenerateFare);
            $fTripGenerateFare = formateNumAsPerCurrency($fTripGenerateFare, $vCurrencyDriver);
            $Data_cab_request[0]['fDuration'] = $tripDurationDisplay;
            $Data_cab_request[0]['fDistance'] = $tripDistanceDisplay;
            $Data_cab_request[0]['fTripGenerateFare'] = $fTripGenerateFare;
        }
        $Data_delivery = $Data_cab_request[0];
        /* -------------------------------for multi delivery----------------------------------------- */
        if (!empty($Data_cab_request)) {
            $returnArr['Action'] = "1";
            $returnArr['message'] = $Data_cab_request[0];
        } else {
            $returnArr['Action'] = "0";
            $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
        }

        return $returnArr;
    }

    public function GetCabRequestAddressDeliverAll($iOrderId, $iDriverId, $vLang) {
        global $obj;

        $Data_cab_request = $obj->MySQLSelect("SELECT iUserId,iCompanyId,iStatusCode,iUserAddressId FROM orders WHERE iOrderId = '$iOrderId'");
        $iCompanyId = $Data_cab_request[0]['iCompanyId'];
        $iUserAddressId = $Data_cab_request[0]['iUserAddressId'];

        $Data_cab_requestcompany = $obj->MySQLSelect("SELECT vCompany,vRestuarantLocation,vRestuarantLocationLat,vRestuarantLocationLong,vCaddress FROM company WHERE iCompanyId = '$iCompanyId'");
        
        $Data_cab_requestuser = $obj->MySQLSelect("SELECT vServiceAddress,vBuildingNo,vLatitude,vLongitude FROM user_address WHERE iUserAddressId = '$iUserAddressId'");
        if (!empty($Data_cab_requestcompany)) {
            $vRestuarantLocation = ($Data_cab_requestcompany[0]['vCaddress'] != '') ? $Data_cab_requestcompany[0]['vCaddress'] : ''; // Added By HJ On 21-10-2020 As Per Discuss with KS sir
            $vRestuarantLocationLat = ($Data_cab_requestcompany[0]['vRestuarantLocationLat'] != '') ? $Data_cab_requestcompany[0]['vRestuarantLocationLat'] : '';
            $vRestuarantLocationLong = ($Data_cab_requestcompany[0]['vRestuarantLocationLong'] != '') ? $Data_cab_requestcompany[0]['vRestuarantLocationLong'] : '';
            if (!empty($Data_cab_requestuser[0]['vBuildingNo'])) {
                $tDestAddress = $Data_cab_requestuser[0]['vBuildingNo'] . ", " . $Data_cab_requestuser[0]['vServiceAddress'];
            }
            else {
                $tDestAddress = $Data_cab_requestuser[0]['vServiceAddress'];
            }
            $UserAddressArr = FetchMemberAddressData($Data_cab_request[0]['iUserId'], "Passenger", $iUserAddressId);
            $vLatitude = ($Data_cab_requestuser[0]['vLatitude'] != '') ? $Data_cab_requestuser[0]['vLatitude'] : '';
            $vLongitude = ($Data_cab_requestuser[0]['vLongitude'] != '') ? $Data_cab_requestuser[0]['vLongitude'] : '';
        }
        $Data_cab_request[0]['tSourceAddress'] = $vRestuarantLocation;
        $Data_cab_request[0]['tSourceLat'] = $Data_cab_request[0]['sourceLatitude'] = $vRestuarantLocationLat;
        $Data_cab_request[0]['tSourceLong'] = $Data_cab_request[0]['sourceLongitude'] = $vRestuarantLocationLong;
        $Data_cab_request[0]['tDestAddress'] = $UserAddressArr['UserAddress'];
        $Data_cab_request[0]['tDestLatitude'] = $Data_cab_request[0]['destLatitude'] = $vLatitude;
        $Data_cab_request[0]['tDestLongitude'] = $Data_cab_request[0]['destLongitude'] = $vLongitude;
        $Data_cab_request[0]['eType'] = "DeliverAll"; // Added By HJ On 23-09-2019 As Per Discuss With CS
        if (!empty($Data_cab_request)) {
            $returnArr['Action'] = "1";
            $returnArr['message'] = $Data_cab_request[0];
        }
        else {
            $returnArr['Action'] = "0";
            $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
        }
        return $returnArr;
    }
}
?>