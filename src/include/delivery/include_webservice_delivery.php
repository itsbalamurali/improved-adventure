<?php
############################################ Functions added ############################################
function calculateFareEstimateAllMultiDelivery($totalTimeInMinutes_trip, $tripDistance, $vehicleTypeID, $iUserId, $priceRatio, $startDate = "", $endDate = "", $couponCode = "", $surgePrice = 1, $fMaterialFee = 0, $fMiscFee = 0, $fDriverDiscount = 0, $DisplySingleVehicleFare = "", $eUserType = "Passenger", $iQty = 1, $SelectedCarTypeID = "", $isDestinationAdded = "Yes", $eFlatTrip = "No", $fFlatTripPrice = 0, $sourceLocationArr, $destinationLocationArr, $DisplayMultiDeliveryFare = "", $RideType = "Ride", $scheduleDate = "", $ePaymentBy = "Sender", $eWalletDebitAllow = "Yes")
{
    //                                             1                   2               3            4           5           6                7               8                 9                   10             11                12                  13                             14                   15              16                   17                      18                  19                     20                     21                                    22                   23               24             25
    global $obj, $tconfig, $APPLY_SURGE_ON_FLAT_FARE, $MODULES_OBJ, $WALLET_OBJ, $LANG_OBJ;
    if ($eUserType == "Passenger") {
        $vCurrencyPassenger = get_value('register_user', 'vCurrencyPassenger', 'iUserId', $iUserId, '', 'true');
        $userlangcode = get_value("register_user", "vLang", "iUserId", $iUserId, '', 'true');
        $eUnit = getMemberCountryUnit($iUserId, "Passenger");
        $TaxArr = getMemberCountryTax($iUserId, "Passenger");
    }
    else {
        $vCurrencyPassenger = get_value('register_driver', 'vCurrencyDriver', 'iDriverId', $iUserId, '', 'true');
        $userlangcode = get_value("register_driver", "vLang", "iDriverId", $iUserId, '', 'true');
        $eUnit = getMemberCountryUnit($iUserId, "Driver");
        $TaxArr = getMemberCountryTax($iUserId, "Driver");
    }
    if ($vCurrencyPassenger == "" || $vCurrencyPassenger == NULL) {
        $vCurrencyPassenger = get_value('currency', 'vName', 'eDefault', 'Yes', '', 'true');
    }
    $priceRatio = get_value('currency', 'Ratio', 'vName', $vCurrencyPassenger, '', 'true');
    $priceRatio = 1;
    $vSymbol = get_value('currency', 'vSymbol', 'vName', $vCurrencyPassenger, '', 'true');
    if ($userlangcode == "" || $userlangcode == NULL) {
        $userlangcode = get_value('language_master', 'vCode', 'eDefault', 'Yes', '', 'true');
    }
    //$eUnit = getMemberCountryUnit($iUserId,"Passenger");
    $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($userlangcode, "1");
    $Fare_data = getVehicleCostData("vehicle_type", $vehicleTypeID);
    $fPickUpPrice = 1;
    $fNightPrice = 1;
    $data_surgePrice = checkSurgePrice($Fare_data[0]['iVehicleTypeId'], $scheduleDate);
    if ($data_surgePrice['Action'] == "0") {
        if ($data_surgePrice['message'] == "LBL_PICK_SURGE_NOTE") {
            $fPickUpPrice = $data_surgePrice['SurgePriceValue'];
        }
        else {
            $fNightPrice = $data_surgePrice['SurgePriceValue'];
        }
    }
    if ($APPLY_SURGE_ON_FLAT_FARE == "No" && $eFlatTrip == "Yes") {
        $fPickUpPrice = 1;
        $fNightPrice = 1;
    }
    $surgePrice = $fPickUpPrice > 1 ? $fPickUpPrice : ($fNightPrice > 1 ? $fNightPrice : 1);
    $Fare_data[0]['TripTimeMinutes'] = $totalTimeInMinutes_trip;
    $Fare_data[0]['TripDistance'] = $tripDistance;
    //$result = FetchTripFareData($Fare_data[0], $surgePrice);
    /** calculate fare * */
    $Fare_data[0]['iBaseFare'] = $Fare_data[0]['iBaseFare'];
    $Fare_data[0]['fPricePerMin'] = $Fare_data[0]['fPricePerMin'];
    $Fare_data[0]['fPricePerKM'] = getVehicleCountryUnit_PricePerKm($Fare_data[0]['iVehicleTypeId'], $Fare_data[0]['fPricePerKM']);
    $Fare_data[0]['fPricePerKM'] = $Fare_data[0]['fPricePerKM'];
    $Fare_data[0]['iMinFare'] = $Fare_data[0]['iMinFare'];
    $iBaseFare = $Fare_data[0]['iBaseFare'];
    $fPricePerKM = $Fare_data[0]['fPricePerKM'];
    $fPricePerMin = $Fare_data[0]['fPricePerMin'];
    $Minute_Fare = round(($fPricePerMin * $totalTimeInMinutes_trip) * $priceRatio, 2);
    $Distance_Fare = round(($fPricePerKM * $tripDistance) * $priceRatio, 2);
    $iBaseFare_Ori = $iBaseFare;
    $Minute_Fare_Ori = round(($fPricePerMin * $totalTimeInMinutes_trip), 2);
    $Distance_Fare_Ori = round(($fPricePerKM * $tripDistance), 2);
    $iBaseFare = round($iBaseFare * $priceRatio, 2);
    $fMaterialFee = round($fMaterialFee * $priceRatio, 2);
    $fMiscFee = round($fMiscFee * $priceRatio, 2);
    $fDriverDiscount = round($fDriverDiscount * $priceRatio, 2);
    $fVisitFee = round($Fare_data[0]['fVisitFee'] * $priceRatio, 2);
    if ($eFlatTrip == "No") {
        $total_fare = ($iBaseFare + $Minute_Fare + $Distance_Fare + $fMaterialFee + $fMiscFee + $fVisitFee) - $fDriverDiscount;
        $fSurgePriceDiff = round(($total_fare * $surgePrice) - $total_fare, 2);
        $SurgePriceFactor = strval($surgePrice);
        $total_fare = $total_fare + $fSurgePriceDiff;
        $minimamfare = round($Fare_data[0]['iMinFare'] * $priceRatio, 2);
        if ($minimamfare > $total_fare && $Fare_data[0]['iRentalPackageId'] == 0) {
            $fMinFareDiff = $minimamfare - $total_fare;
            $total_fare = $minimamfare;
            $Fare_data[0]['FinalFare'] = $total_fare;
        }
        else {
            $fMinFareDiff = 0;
        }
    }
    else {
        $total_fare = round($fFlatTripPrice * $priceRatio, 2);
        $fSurgePriceDiff = round(($total_fare * $surgePrice) - $total_fare, 2);
        $SurgePriceFactor = strval($surgePrice);
        $total_fare = $total_fare + $fSurgePriceDiff;
        $Fare_data[0]['FinalFare'] = $total_fare;
        $fMinFareDiff = 0;
        $Minute_Fare = 0;
        $Minute_Fare_Ori = 0;
        $Distance_Fare = 0;
        $Distance_Fare_Ori = 0;
        $iBaseFare_Ori = $fFlatTripPrice;
    }
    $Commision_Fare = round((($total_fare * $Fare_data[0]['fCommision']) / 100), 2);
    $Commision_Fare_Ori = round($Commision_Fare / $priceRatio, 2);
    $Generated_Fare = $total_fare;
    ## Calculate for Discount ##
    $discountValue = $discountValue_Ori = $Fare_data[0]['fDiscount_Ori'] = 0;
    $discountValueType = "cash";
    if ($couponCode != "") {
        //Added By HJ On 18-01-2019 For Check and Get Active Coupon Data Start
        $getCouponCode = $obj->MySQLSelect("SELECT fDiscount,eType,iLocationId FROM coupon WHERE vCouponCode='" . $couponCode . "' AND eStatus='Active'");
        if (count($getCouponCode) > 0) {
            $discountValue = $getCouponCode[0]['fDiscount'];
            $discountValueType = $getCouponCode[0]['eType'];
            if ($MODULES_OBJ->isEnableLocationWisePromoCode() && $getCouponCode[0]['iLocationId'] > 0) {
                $tDeliveryLocations = $_REQUEST['delivery_arr'];
                // $tDeliveryLocations = json_decode(str_replace("\\", "", $tDeliveryLocations), true);
                $tDeliveryLocations = json_decode($tDeliveryLocations, true);
                $tDeliveryLocationsArr = array();
                $tDeliveryLocationsArr[] = array('latitude' => $_REQUEST['PickUpLatitude'], 'longitude' => $sourceLocationArr['PickUpLongitude'],);
                foreach ($tDeliveryLocations as $location) {
                    if (isset($location['vReceiverLatitude'])) {
                        $tDeliveryLocationsArr[] = array('latitude' => $location['vReceiverLatitude'], 'longitude' => $location['vReceiverLongitude'],);
                    }
                }
                // echo "<pre>";print_r($tDeliveryLocations);die;
                foreach ($tDeliveryLocationsArr as $loc) {
                    $Address_Array = array($loc['latitude'], $loc['longitude']);
                    $iLocationIdArr = GetUserGeoLocationIdPromoCode($Address_Array);
                    if (!in_array($getCouponCode[0]['iLocationId'], $iLocationIdArr)) {
                        $discountValue = $discountValue_Ori = 0;
                        $discountValueType = "cash";
                        break;
                    }
                }
            }
        }
        // echo $discountValue . ' >>> ' . $discountValueType; exit;
        //Added By HJ On 18-01-2019 For Check and Get Active Coupon Data End
        //$discountValue = get_value('coupon', 'fDiscount', 'vCouponCode', $couponCode, '', 'true'); //Commented By HJ On 18-01-2019
        //$discountValueType = get_value('coupon', 'eType', 'vCouponCode', $couponCode, '', 'true'); //Commented By HJ On 18-01-2019
        if ($discountValueType == "percentage") {
            $vDiscount = round($discountValue, 1) . ' ' . "%";
            $discountValue = round(($total_fare * $discountValue), 1) / 100;
            $discountValue_Ori = round((round($total_fare / $priceRatio, 2) * $discountValue), 1) / 100;
        }
        else {
            $curr_sym = get_value('currency', 'vSymbol', 'eDefault', 'Yes', '', 'true');
            $discountValue = round(($discountValue * $priceRatio), 2);
            if ($discountValue > $total_fare) {
                $vDiscount = round($total_fare, 1) . ' ' . $curr_sym;
            }
            else {
                $vDiscount = round($discountValue, 1) . ' ' . $curr_sym;
            }
            $discountValue_Ori = $discountValue;
        }
        $total_fare = $total_fare - $discountValue;
        $Fare_data[0]['fDiscount_fixed'] = $discountValue;
        if ($total_fare < 0) {
            $total_fare = 0;
            //$discountValue = $total_fare;
        }
        if ($Fare_data[0]['eFareType'] == "Regular") {
            $Fare_data[0]['fDiscount'] = $discountValue;
            $Fare_data[0]['vDiscount'] = $vDiscount;
        }
        else {
            $Fare_data[0]['fDiscount'] = $Fare_data[0]['fDiscount_fixed'];
            $Fare_data[0]['vDiscount'] = $vDiscount;
        }
        $Fare_data[0]['fDiscount_Ori'] = round($discountValue_Ori, 2);
    }
    ## Calculate for Discount ##
    //$fSurgePriceDiff = $farewithsurcharge - $minimamfare;
    /* Checking For Passenger Outstanding Amount */
    $fOutStandingAmount = 0;
    if ($ePaymentBy == "Sender") {
        $fOutStandingAmount = GetPassengerOutstandingAmount($iUserId);
    }
    if ($fOutStandingAmount > 0) {
        $total_fare = $total_fare + $fOutStandingAmount;
        $Generated_Fare = $Generated_Fare + $fOutStandingAmount;
        $Fare_data[0]['FinalFare'] = $total_fare;
    }
    /* Checking For Passenger Outstanding Amount */
    /* Tax Calculation */
    $fTax1 = $TaxArr['fTax1'];
    $fTax2 = $TaxArr['fTax2'];
    if ($fTax1 > 0) {
        //$fTaxAmount1 = round(((($total_fare - $discountValue) * $fTax1) / 100), 2); //Discount Value Commented BY HJ As Per Discuss With KS On 31-10-2019 141 Mantis Bug
        $fTaxAmount1 = round((($total_fare * $fTax1) / 100), 2); //Discount Value Added BY HJ As Per Discuss With KS On 31-10-2019 141 Mantis Bug
        $total_fare = $total_fare + $fTaxAmount1;
        $Generated_Fare = $Generated_Fare + $fTaxAmount1;
        //$Fare_data[0]['fTax1'] = $vSymbol . " " . number_format($fTaxAmount1, 2);
        $Fare_data[0]['fTax1'] = formateNumAsPerCurrency($fTaxAmount1, $vCurrencyPassenger);
        $Fare_data[0]['fTax1_Ori'] = round($fTaxAmount1, 2);
    }
    if ($fTax2 > 0) {
        //$total_fare_new = $total_fare - $discountValue - $fTaxAmount1; //Discount Value Commented BY HJ As Per Discuss With KS On 31-10-2019 141 Mantis Bug
        $total_fare_new = $total_fare - $fTaxAmount1; //Discount Value Added BY HJ As Per Discuss With KS On 31-10-2019 141 Mantis Bug
        $fTaxAmount2 = round((($total_fare_new * $fTax2) / 100), 2);
        $total_fare = $total_fare + $fTaxAmount2;
        $Generated_Fare = $Generated_Fare + $fTaxAmount2;
        //$Fare_data[0]['fTax2'] = $vSymbol . " " . number_format($fTaxAmount2, 2);
        $Fare_data[0]['fTax2'] = formateNumAsPerCurrency($fTaxAmount2, $vCurrencyPassenger);
        $Fare_data[0]['fTax2_Ori'] = round($fTaxAmount2, 2);
    }
    /* Tax Calculation */
    /* Check debit wallet For Count Total Fare  Start */
    $user_wallet_debit_amount = 0;
    if ($ePaymentBy == "Sender" && $eWalletDebitAllow == "Yes") {
        //$eWalletAdjustment = get_value('register_user', 'eWalletAdjustment', 'iUserId', $iUserId, '', 'true');
        $eWalletAdjustment = $eWalletDebitAllow;
        if ($eWalletAdjustment == "Yes") {
            $user_available_balance = $WALLET_OBJ->FetchMemberWalletBalance($iUserId, "Rider");
            if ($total_fare > $user_available_balance) {
                $total_fare = $total_fare - $user_available_balance;
                $user_wallet_debit_amount = $user_available_balance;
            }
            else {
                $user_wallet_debit_amount = $total_fare;
                $total_fare = 0;
            }
        }
    }
    /* Check debit wallet For Count Total Fare  Start */
    /** calculate fare * */
    $Fare_data[0]['FareOfMinutes'] = $Minute_Fare;
    $Fare_data[0]['FareOfDistance'] = $Distance_Fare;
    $Fare_data[0]['FareOfCommision'] = $Commision_Fare;
    $Fare_data[0]['fPricePerMin'] = $Fare_data[0]['fPricePerMin'];
    $Fare_data[0]['fPricePerKM'] = $Fare_data[0]['fPricePerKM'];
    $Fare_data[0]['fCommision'] = $Fare_data[0]['fCommision'];
    $Fare_data[0]['FinalFare'] = $total_fare;
    $Fare_data[0]['iBaseFare'] = ($Fare_data[0]['eFareType'] == 'Fixed') ? 0 : $iBaseFare;
    $Fare_data[0]['iMinFare'] = round($Fare_data[0]['iMinFare'] * $priceRatio, 2);
    if ($Fare_data[0]['eFareType'] == "Regular") {
        //$Fare_data[0]['total_fare'] = $vSymbol." ".number_format($total_fare,2);
        $Fare_data[0]['total_fare'] = formateNumAsPerCurrency($total_fare, $vCurrencyPassenger);
        $Fare_data[0]['total_fare_amount'] = number_format($total_fare, 2);
    }
    else {
        $Fare_data[0]['total_fare_amount'] = number_format($Fare_data[0]['FinalFare'], 2);
    }
    $TotalGenratedFare = $Generated_Fare;
    $Fare_data[0]['fMinFareDiff'] = $fMinFareDiff;
    $Fare_data[0]['fTax1'] = $fTax1;
    $Fare_data[0]['fTax2'] = $fTax2;
    $Fare_data[0]['fSurgePriceDiff'] = $fSurgePriceDiff;
    $Fare_data[0]['TotalGenratedFare'] = round($TotalGenratedFare, 2);
    //$Fare_data[0]['TotalGenratedFare'] = round($TotalGenratedFare/$priceRatio,2);
    $Fare_data[0]['iFare_Ori'] = round($total_fare / $priceRatio, 2);
    $Fare_data[0]['iBaseFare_AMT'] = ($Fare_data[0]['eFareType'] == 'Fixed') ? 0 : $iBaseFare_Ori;
    $Fare_data[0]['FareOfMinutes_Ori'] = $Minute_Fare_Ori;
    $Fare_data[0]['FareOfDistance_Ori'] = $Distance_Fare_Ori;
    $Fare_data[0]['fCommision_AMT'] = $Commision_Fare_Ori;
    $Fare_data[0]['fSurgePriceDiff_Ori'] = round($fSurgePriceDiff / $priceRatio, 2);
    //$Fare_data[0]['fTax1_Ori'] = round($fTax1/$priceRatio,2);
    //$Fare_data[0]['fTax2_Ori'] = round($fTax2/$priceRatio,2);
    $Fare_data[0]['fTax1Percentage'] = $TaxArr['fTax1'];
    $Fare_data[0]['fTax2Percentage'] = $TaxArr['fTax2'];
    $Fare_data[0]['fOutStandingAmount'] = $fOutStandingAmount;
    $Fare_data[0]['fWalletDebit'] = $user_wallet_debit_amount;
    $Fare_data[0]['fMinFareDiff_Ori'] = round($fMinFareDiff / $priceRatio, 2);
    $Fare_data[0]['fDiscount_Ori'] = ($Fare_data[0]['fDiscount_Ori'] > $Fare_data[0]['TotalGenratedFare']) ? $Fare_data[0]['TotalGenratedFare'] : $Fare_data[0]['fDiscount_Ori'];
    $Fare_data[0]['iBaseFare'] = formateNumAsPerCurrency($Fare_data[0]['iBaseFare'], $vCurrencyPassenger);
    //$Fare_data[0]['fPricePerMin'] = $vSymbol . " " . number_format(round($Fare_data[0]['fPricePerMin'] * $priceRatio, 1), 2);
    $fPricePerMin = round($Fare_data[0]['fPricePerMin'] * $priceRatio, 1);
    $Fare_data[0]['fPricePerMin'] = formateNumAsPerCurrency($fPricePerMin, $vCurrencyPassenger);
    $fPricePerKM = round($Fare_data[0]['fPricePerKM'] * $priceRatio, 1);
    $Fare_data[0]['fPricePerKM'] = formateNumAsPerCurrency($fPricePerKM, $vCurrencyPassenger);
    $fCommision = round($Fare_data[0]['fCommision'] * $priceRatio, 1);
    $Fare_data[0]['fCommision'] = formateNumAsPerCurrency($fCommision, $vCurrencyPassenger);
    //added by SP for rounding off on 7-11-2019 for multi delivery start
    $sqlp = "SELECT cu.vName, cu.iCurrencyId, cu.eRoundingOffEnable, cu.Ratio FROM register_user AS ru LEFT JOIN currency AS cu ON ru.vCurrencyPassenger = cu.vName WHERE ru.iUserId = '" . $iUserId . "'";
    $currData = $obj->MySQLSelect($sqlp);
    $vCurrency = $currData[0]['vName'];
    $Data_update_trips_round = array();
    $total_fare_round = $Fare_data[0]['iFare_Ori'];
    //if(($dataUser[0]['vTripPaymentMode'] == "Cash")) {
    // if($eHailTrip=='Yes') {} else {
    if ($currData[0]['eRoundingOffEnable'] == "Yes" && $MODULES_OBJ->isEnableRoundingMethod()) {
        //&& $trip_start_data_arr[0]['vCurrencyPassenger']==$trip_start_data_arr[0]['vCurrencyDriver']
        $userCurrencyRatio = get_value('currency', 'Ratio', 'vName', $vCurrency, '', 'true');
        $roundingOffTotal_fare_amountArr = getRoundingOffAmount($total_fare_round * $userCurrencyRatio, $vCurrency);
        //$userCurrencyRatio = $currData[0]['Ratio'];
        $roundingOffTotal_fare_amount = $roundingOffTotal_fare_amountArr['finalFareValue'];
        if ($roundingOffTotal_fare_amountArr['method'] == "Addition") {
            $eRoundingType = "Addition";
        }
        else {
            $eRoundingType = "Substraction";
        }
        $fRoundingAmount = setTwoDecimalPoint($roundingOffTotal_fare_amountArr['differenceValue']);
        //$Data_update_trips['iFare'] = setTwoDecimalPoint($roundingOffTotal_fare_amount / $priceRatio);
        $Fare_data[0]['fRoundingAmount'] = $fRoundingAmount;
        $Fare_data[0]['eRoundingType'] = $eRoundingType;
    }
    // }
    //}
    //added by SP for rounding off on 7-11-2019 for multi delivery end
    //echo "<pre>";print_R($Fare_data);exit;
    // return $Fare_data;
    if ($DisplayMultiDeliveryFare == "Yes") {
        //return $Fare_data_amounts;
        return $Fare_data;
    }
    else {
        return $Fare_data;
    }
}

function deliverySmsToReceiver($iTripId, $del_locId = '')
{
    global $obj, $tconfig, $COMM_MEDIA_OBJ;
    $sql = "SELECT * from trips WHERE iTripId = '" . $iTripId . "'";
    $tripData = $obj->MySQLSelect($sql);
    $SenderName = get_value("register_user", "vName,vLastName", "iUserId", $tripData[0]['iUserId']);
    $SenderName = $SenderName[0]['vName'] . " " . $SenderName[0]['vLastName'];
    $delivery_address = $tripData[0]['tDaddress'];
    $vDeliveryConfirmCode = $tripData[0]['vDeliveryConfirmCode'];
    $page_link = $tconfig['tsite_url'] . "trip_tracking.php?iTripId=" . base64_encode(base64_encode($iTripId));
    /* -----------------For multi delivery --------------------------- */
    if ($del_locId != "") {
        $sql = "SELECT vDeliveryConfirmCode,vReceiverAddress FROM `trips_delivery_locations` WHERE iTripId = '$iTripId' and iTripDeliveryLocationId='$del_locId'";
        $Data_delivery_locations = $obj->MySQLSelect($sql);
        $sql = "select vValue from trip_delivery_fields where iDeliveryFieldId='2' and iTripId = '$iTripId' and iTripDeliveryLocationId='$del_locId'";
        $Data_delivery_values = $obj->MySQLSelect($sql);
        $vDeliveryConfirmCode = $Data_delivery_locations[0]['vDeliveryConfirmCode'];
        $delivery_address = $Data_delivery_locations[0]['vReceiverAddress'];
        $recepient_name = $Data_delivery_values[0]['vValue'];
        $page_link = $tconfig['tsite_url'] . "trip_tracking_multi_delivery.php?iTripId=" . base64_encode(base64_encode($iTripId)) . "&Del_loc_id=" . base64_encode(base64_encode($del_locId));
    }
    $vLangCode = get_value('register_user', 'vLang', 'iUserId', $tripData[0]['iUserId'], '', 'true');
    if ($vLangCode == "" || $vLangCode == NULL) {
        $vLangCode = get_value('language_master', 'vCode', 'eDefault', 'Yes', '', 'true');
    }
    $page_link = get_tiny_url($page_link);
    $dataArraySMSNew['recepientName'] = $recepient_name;
    $dataArraySMSNew['SenderName'] = $SenderName;
    $dataArraySMSNew['deliveryAddress'] = $delivery_address;
    $dataArraySMSNew['vDeliveryConfirmCode'] = $vDeliveryConfirmCode;
    $dataArraySMSNew['pageLink'] = $page_link;
    /* -----------------For multi delivery end--------------------------- */
    if ($del_locId != "") {
        //for multi delivery flow
        if ($tripData[0]['vVerificationMethod'] == "Code") {
            //$message_deliver = "Dear " . $recepient_name . ", " . $SenderName . " has send you the parcel on below address." . $delivery_address . ". Upon Receiving the parcel, please provide below verification code to Delivery Driver. Verification Code: " . $vDeliveryConfirmCode . ". click on link below to track your parcel. " . $page_link;
            $message_deliver = $COMM_MEDIA_OBJ->GetSMSTemplate('DELIVER_SMS_TO_RECEIVER_ONE', $dataArraySMSNew, "", $vLangCode);
        }
        else {
            //$message_deliver = "Dear " . $recepient_name . ", " . $SenderName . " has send you the parcel on below address." . $delivery_address . ". click on link below to track your parcel. " . $page_link;
            $message_deliver = $COMM_MEDIA_OBJ->GetSMSTemplate('DELIVER_SMS_TO_RECEIVER_TWO', $dataArraySMSNew, "", $vLangCode);
        }
    }
    else {
        //for normal delivery flow
        //$message_deliver = $SenderName . " has send you the parcel on below address." . $delivery_address . ". Upon Receiving the parcel, please provide below verification code to Delivery Driver. Verification Code: " . $vDeliveryConfirmCode . ". click on link below to track your parcel. " . $page_link;
        $message_deliver = $COMM_MEDIA_OBJ->GetSMSTemplate('DELIVER_SMS_TO_RECEIVER_THREE', $dataArraySMSNew, "", $vLangCode);
    }
    //echo $message_deliver;exit;
    return $message_deliver;
}

function gettexttripdeliverydetails($iTripId, $eUserType = "Passenger", $Checkfor = "Starttrip")
{
    global $obj, $userDetailsArr, $vSystemDefaultLangCode, $languageLabelDataArr, $REFERRAL_OBJ, $LANG_OBJ;
    $returnArr = array();
    //Added By HJ On 23-06-2020 For Optimize trips Table Query Start
    if (isset($tripDetailsArr['trips_' . $iTripId])) {
        $tripData = $tripDetailsArr['trips_' . $iTripId];
    }
    else {
        $tripData = $obj->MySQLSelect("SELECT * from trips WHERE iTripId = '" . $iTripId . "'");
        $tripDetailsArr['trips_' . $iTripId] = $tripData;
    }
    //Added By HJ On 23-06-2020 For Optimize trips Table Query End
    $iActive = $tripData[0]['iActive'];
    if ($eUserType == "Passenger") {
        $tblname = "register_user";
        $vLang = "vLang";
        $iUserId = "iUserId";
        $iMemberId = $tripData[0]['iUserId'];
    }
    else {
        $tblname = "register_driver";
        $vLang = "vLang";
        $iUserId = "iDriverId";
        $iMemberId = $tripData[0]['iDriverId'];
    }
    //Added By HJ On 23-06-2020 For Optimize register_driver/user Table Query Start
    if (isset($userDetailsArr[$tblname . "_" . $iMemberId])) {
        $userlangcode = $userDetailsArr[$tblname . "_" . $iMemberId][0]['vLang'];
    }
    else {
        $userlangcode = get_value($tblname, $vLang, $iUserId, $iMemberId, '', 'true');
    }
    //Added By HJ On 23-06-2020 For Optimize register_driver/user Table Query End
    if ($userlangcode == "" || $userlangcode == NULL) {
        //Added By HJ On 23-06-2020 For Optimize language_master Table Query Start
        if (!empty($vSystemDefaultLangCode)) {
            $userlangcode = $vSystemDefaultLangCode;
        }
        else {
            $userlangcode = get_value('language_master', 'vCode', 'eDefault', 'Yes', '', 'true');
        }
        //Added By HJ On 23-06-2020 For Optimize language_master Table Query End
    }
    //Added By HJ On 23-06-2020 For Optimize register_driver Table Query Start
    if (isset($userDetailsArr["register_driver_" . $tripData[0]['iDriverId']])) {
        $result22 = $userDetailsArr["register_driver_" . $tripData[0]['iDriverId']];
    }
    else {
        $result22 = $obj->MySQLSelect("SELECT * FROM register_driver WHERE iDriverId = '" . $tripData[0]['iDriverId'] . "'");
    }
    if (count($result22) > 0) {
        $result22[0]['driverName'] = $result22[0]['vName'] . " " . $result22[0]['vLastName'];
    }
    //Added By HJ On 23-06-2020 For Optimize register_driver Table Query End
    //Added By HJ On 24-06-2020 For Optimize language label Table Query Start
    if (isset($languageLabelDataArr[$userlangcode])) {
        $languageLabelsArr = $languageLabelDataArr[$userlangcode];
    }
    else {
        $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($userlangcode, "1");
        $languageLabelDataArr[$userlangcode] = $languageLabelsArr;
    }
    //Added By HJ On 24-06-2020 For Optimize language label Table Query End
    $eType = $tripData[0]['eType'];
    if ($eType == "Multi-Delivery") {
        // Added By HJ On 23-06-2020 For Optimize trips_delivery_locations Table Query Start
        $totalTripDeliveryCount = 0;
        if (isset($tripDetailsArr["trips_delivery_locations_" . $iTripId])) {
            $sqldeliverydata = $tripDetailsArr["trips_delivery_locations_" . $iTripId];
            $totalTripDeliveryCount = count($sqldeliverydata);
        }
        else {
            $sqldeliverydata = $obj->MySQLSelect("SELECT * FROM `trips_delivery_locations` WHERE iTripId='" . $iTripId . "' ORDER BY  iTripDeliveryLocationId ASC");
            $tripDetailsArr["trips_delivery_locations_" . $iTripId] = $sqldeliverydata;
            $totalTripDeliveryCount = count($sqldeliverydata);
        }
        //echo "<pre>";print_r($sqldeliverydata);die;
        // Added By HJ On 23-06-2020 For Optimize trips_delivery_locations Table Query End
        //$TotalTripDeliveryData = $obj->MySQLSelect("SELECT count(iTripDeliveryLocationId) AS TotalTripDelivery FROM trips_delivery_locations WHERE iTripId='" . $iTripId . "'");
        //$TotalTripDelivery = $TotalTripDeliveryData[0]['TotalTripDelivery'];
        $TotalTripDelivery = $totalTripDeliveryCount;
        $ssql = "";
        $TripDeliveryData = $price = array();
        $recordFound = 1;
        foreach ($sqldeliverydata as $key => $row) {
            $price[$key] = $row['iTripDeliveryLocationId'];
        }
        if ($Checkfor == "Starttrip") {
            $ssql .= "AND iActive = 'Active' ORDER BY iTripDeliveryLocationId ASC LIMIT 0,1"; // Put Order By Condition By HJ On 14-02-2020 For Solved Quiqo Project Issue As Per Discuss with KS Sir
            array_multisort($price, SORT_DESC, $sqldeliverydata);
        }
        else {
            $ssql .= "AND iActive = 'Finished' AND eSignVerification = 'No' ORDER BY iTripDeliveryLocationId DESC LIMIT 0,1"; // Put Order By Condition By HJ On 14-02-2020 For Solved Quiqo Project Issue As Per Discuss with KS Sir
            array_multisort($price, SORT_ASC, $sqldeliverydata);
        }
        for ($k = 0; $k < count($sqldeliverydata); $k++) {
            $status = $sqldeliverydata[$k]['iActive'];
            $eSignVerification = $sqldeliverydata[$k]['eSignVerification'];
            if ($Checkfor == "Starttrip") {
                //$ssql .= "AND iActive = 'Active' ";
                if (strtoupper($status) == "ACTIVE" && $recordFound > 0) {
                    $recordFound = 0;
                    $TripDeliveryData[] = $sqldeliverydata[$k];
                }
            }
            else {
                if (strtoupper($status) == "FINISHED" && strtoupper($eSignVerification) == "NO" && $recordFound > 0) {
                    $recordFound = 0;
                    $TripDeliveryData[] = $sqldeliverydata[$k];
                }
                //$ssql .= "AND iActive = 'Finished' AND eSignVerification = 'No'";
            }
        }
        //echo $ssql;die;
        $sqldeliverydata = "SELECT * FROM `trips_delivery_locations` WHERE  iTripId='" . $iTripId . "' $ssql";
        $TripDeliveryData = $obj->MySQLSelect($sqldeliverydata);
        //added by SP on 13-01-2021 first delivery at that time $TripDeliveryData is empty so do it to solve null values in iTripDeliveryLocationId
        if (empty($TripDeliveryData)) {
            $ssql_first = '';
            $ssql_first .= "AND iActive = 'Finished' ORDER BY iTripDeliveryLocationId DESC LIMIT 0,1";
            $sqldeliverydata = "SELECT * FROM `trips_delivery_locations` WHERE  iTripId='" . $iTripId . "' $ssql_first";
            $TripDeliveryData = $obj->MySQLSelect($sqldeliverydata);
        }
        $iRunningTripDeliveryNo = $tripData[0]['iRunningTripDeliveryNo'];
        if ($iActive == "Active") {
            $iRunningTripDeliveryNo = $iRunningTripDeliveryNo + 1;
        }
        if ($iRunningTripDeliveryNo > $TotalTripDelivery) {
            $iRunningTripDeliveryNo = $TotalTripDelivery;
        }
        $Is_Last_Delivery = "No";
        if ($iRunningTripDeliveryNo == $TotalTripDelivery) {
            //added by SP on 13-01-2021 last delivery is considered only when endtrip is done and confirm code is done
            $sqldeliverydatalast = "SELECT * FROM `trips_delivery_locations` WHERE  iTripId='" . $iTripId . "' AND iActive = 'Finished' AND eSignVerification = 'Yes'";
            $TripDeliveryDatalast = $obj->MySQLSelect($sqldeliverydatalast);
            if (count($TripDeliveryDatalast) == $TotalTripDelivery) {
                $Is_Last_Delivery = "Yes";
                //added by SP on 13-01-2021, last delivery confirmation code is done then only delivery is completed so status changed and put in processendtrip that not status change when trip ends in multi delivery.
                $Data_update_trips = $Data_update_passenger = $Data_update_driver = array();
                $Data_update_trips['iActive'] = 'Finished';
                $where = " iTripId = '" . $iTripId . "'";
                $id = $obj->MySQLQueryPerform("trips", $Data_update_trips, 'update', $where);
                $trip_status = $trip_status_driver = "Not Active";
                $vCallFromDriver = 'Not Assigned';
                $userId = $tripData[0]['iUserId'];
                $driverId = $tripData[0]['iDriverId'];
                $where = " iUserId = '$userId'";
                $Data_update_passenger['iTripId'] = $iTripId;
                $Data_update_passenger['vTripStatus'] = $trip_status;
                $Data_update_passenger['vCallFromDriver'] = 'Not Assigned';
                $Data_update_passenger['fTripsOutStandingAmount'] = '0';
                $id = $obj->MySQLQueryPerform("register_user", $Data_update_passenger, 'update', $where);
                $where = " iDriverId = '$driverId'";
                $Data_update_driver['iTripId'] = $iTripId;
                $Data_update_driver['vTripStatus'] = $trip_status_driver;
                $id = $obj->MySQLQueryPerform("register_driver", $Data_update_driver, 'update', $where);
                $REFERRAL_OBJ->CreditReferralAmount($iTripId);
            }
        }
        $returnArr['Running_Delivery_Txt'] = $languageLabelsArr['LBL_CURRENT_DELIVERY_NUMBER'] . " " . $iRunningTripDeliveryNo . " " . $languageLabelsArr['LBL_OUT_OF_TXT'] . " " . $TotalTripDelivery;
        $returnArr['iTripDeliveryLocationId'] = $TripDeliveryData[0]['iTripDeliveryLocationId'];
        $vReceiverName = "";
        if ($TripDeliveryData[0]['iTripDeliveryLocationId'] > 0) {
            $sql = "select vValue from trip_delivery_fields where iTripDeliveryLocationId = '" . $TripDeliveryData[0]['iTripDeliveryLocationId'] . "' and iDeliveryFieldId='2'";
            $Data_Name = $obj->MySQLSelect($sql);
            $vReceiverName = $Data_Name[0]['vValue'];
        }
        $returnArr['vReceiverName'] = $vReceiverName;
        $returnArr['Delivery_Start_Txt'] = $languageLabelsArr['LBL_DELIVERY_TO_RECEIPENT_TXT'] . " " . $vReceiverName . " " . $languageLabelsArr['LBL_DELIVERY_TO_RECEIPENT_START_TXT'] . " " . $result22[0]['driverName'];
        if ($Is_Last_Delivery == "No") {
            $returnArr['Delivery_End_Txt'] = $languageLabelsArr['LBL_DELIVERY_TO_RECEIPENT_TXT'] . " " . $vReceiverName . " " . $languageLabelsArr['LBL_DELIVERY_TO_RECEIPENT_END_TXT'] . " " . $result22[0]['driverName'];
        }
        else {
            $returnArr['Delivery_End_Txt'] = $languageLabelsArr["LBL_DELIVERY_TO_RECEIPENT_TXT"] . ' ' . $vReceiverName . ' ' . $languageLabelsArr["LBL_DELIVERY_TO_RECEIPENT_END_TXT"] . ' ' . $result22[0]["driverName"] . '. ' . $languageLabelsArr["LBL_VIEW_REPORTS_TXT"] . ' "' . $languageLabelsArr["LBL_BOOKING"] . '"'; // LBL_YOUR_TRIPS
        }
        $returnArr['Is_Last_Delivery'] = $Is_Last_Delivery;
    }
    else {
        $returnArr['Running_Delivery_Txt'] = $returnArr['vReceiverName'] = $returnArr['Delivery_Start_Txt'] = $returnArr['Delivery_End_Txt'] = "";
        $returnArr['iTripDeliveryLocationId'] = 0;
        $returnArr['Is_Last_Delivery'] = "Yes";
    }
    return $returnArr;
}

function getServiceCategoriesProDY($iUserId) {
    global $obj, $tconfig, $LANG_OBJ, $MODULES_OBJ, $iServiceId;

    $userData = $obj->MySQLSelect("SELECT vName, vLastName, vLang FROM `register_user` WHERE iUserId = '$iUserId' ");
    $lang = $userData[0]['vLang'];
    $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($lang, "1", "");

    $MASTER_SERVICE_CATEGORIES = array();

    $whereloc = " AND iLocationid IN ('-1')";
    if ($MODULES_OBJ->isEnableLocationwiseBanner()) {
        $vLatitude = isset($_REQUEST["vLatitude"]) ? $_REQUEST["vLatitude"] : '';
        $vLongitude = isset($_REQUEST["vLongitude"]) ? $_REQUEST["vLongitude"] : '';
        $User_Address_Banner = array($vLatitude, $vLongitude);
        if (!empty($User_Address_Banner)) {
            $iLocationIdBanner = GetUserGeoLocationIdBanner($User_Address_Banner);
            $country_str_banner = "'-1'";
            if (count($iLocationIdBanner) > 0) {
                foreach ($iLocationIdBanner as $key => $value) {
                    $country_str_banner .= ", '" . $value . "'";
                }
                $whereloc = " AND iLocationid IN (" . $country_str_banner . ")";
            }
        }
    }

    $Data_banners = $obj->MySQLSelect("SELECT vImage FROM banners WHERE vCode = '" . $lang . "' AND vImage != '' AND eStatus = 'Active' $whereloc ORDER BY iDisplayOrder ASC");
    if(!empty($Data_banners) && count($Data_banners) > 0) {
        $dataOfBanners = array();
        $count = 0;
        for ($i = 0; $i < count($Data_banners); $i++) {
            if (isset($Data_banners[$i]['vImage']) && $Data_banners[$i]['vImage'] != "") {
                $dataOfBanners[$count]['vImage'] = $tconfig["tsite_url"] . 'assets/img/images/' . $Data_banners[$i]['vImage'];
                $banner_img_path = $tconfig['tpanel_path'] . 'assets/img/images/' . $Data_banners[$i]['vImage'];

                $imagedata = getimagesize($banner_img_path);
                $dataOfBanners[$count]['vImageWidth'] = strval($imagedata[0]);
                $dataOfBanners[$count]['vImageHeight'] = strval($imagedata[1]);
                $dataOfBanners[$count]['isClickable'] = "No";
                $count++;
            }
        }

        $mServiceCategoryArr = array();
        $mServiceCategoryArr['eViewType'] = "BannerView";
        $mServiceCategoryArr['isScroll'] = "Yes";
        $mServiceCategoryArr['displayCount'] = "1";
        $mServiceCategoryArr['AddTopPadding'] = "No";
        $mServiceCategoryArr['AddBottomPadding'] = "No";
        $mServiceCategoryArr['isFullView'] = "No";
        $mServiceCategoryArr['vImageWidth'] = $dataOfBanners[0]['vImageWidth'];
        $mServiceCategoryArr['vImageHeight'] = $dataOfBanners[0]['vImageHeight'];
        $mServiceCategoryArr['imagesArr'] = $dataOfBanners;
    }
    $MASTER_SERVICE_CATEGORIES[] = $mServiceCategoryArr;

    $mServiceCategoryArr = array();
    $mServiceCategoryArr['vTitle'] = $languageLabelsArr['LBL_DELIVERY_PICK_DROP_THINGS_TITLE'];
    $mServiceCategoryArr['eViewType'] = 'TitleView';
    $mServiceCategoryArr['AddBottomPadding'] = "No";
    $MASTER_SERVICE_CATEGORIES[] = $mServiceCategoryArr;

    $parcel_delivery_items = $obj->MySQLSelect("SELECT tItemName, vImage FROM parcel_delivery_items WHERE eStatus = 'Active' ORDER BY iDisplayOrder");
    $serviceDataArr = array();
    foreach ($parcel_delivery_items as $k => $item) {
        $tItemNameArr = json_decode($item['tItemName'], true);
        $serviceDataArr[$k]['itemName'] = $tItemNameArr['tItemName_' . $lang];
        $serviceDataArr[$k]['vImage'] = !empty($item['vImage']) ? $tconfig['tsite_upload_images_parcel_delivery_items'] . '/' . $item['vImage'] : '';
    }
    $mServiceCategoryArr = array();
    $mServiceCategoryArr['eViewType'] = "GridIconView";
    $mServiceCategoryArr['servicesArr'] = $serviceDataArr;
    $MASTER_SERVICE_CATEGORIES[] = $mServiceCategoryArr;

    $mServiceCategoryArr = array();
    $mServiceCategoryArr['vTitle'] = $languageLabelsArr['LBL_DELIVERY_USE_APP_FOR_NEEDS_TITLE'];
    $mServiceCategoryArr['eViewType'] = 'TitleView';
    $mServiceCategoryArr['AddBottomPadding'] = "No";
    $mServiceCategoryArr['AddTopPadding'] = "Yes";
    $MASTER_SERVICE_CATEGORIES[] = $mServiceCategoryArr;

    $parcel_delivery_items_size = $obj->MySQLSelect("SELECT tTitle, tSubtitle, vImage FROM parcel_delivery_items_size_info WHERE eStatus = 'Active' ORDER BY iDisplayOrder");
    $serviceDataArr = array();
    foreach ($parcel_delivery_items_size as $k => $item_size) {
        $tTitleArr = json_decode($item_size['tTitle'], true);
        $tSubtitleArr = json_decode($item_size['tSubtitle'], true);
        $serviceDataArr[$k]['vCategory'] = $tTitleArr['tTitle_' . $lang];
        $serviceDataArr[$k]['tListDescription'] = $tSubtitleArr['tSubtitle_' . $lang];
        $serviceDataArr[$k]['vImage'] = !empty($item_size['vImage']) ? $tconfig['tsite_upload_images_parcel_delivery_items_size'] . '/' . $item_size['vImage'] : '';
    }
    $mServiceCategoryArr = array();
    $mServiceCategoryArr['eViewType'] = "ListView";
    $mServiceCategoryArr['servicesArr'] = $serviceDataArr;
    $MASTER_SERVICE_CATEGORIES[] = $mServiceCategoryArr;

    $returnArr['Action'] = "1";
    $returnArr['HOME_SCREEN_DATA'] = $MASTER_SERVICE_CATEGORIES;
    setDataResponse($returnArr);
}
############################################ Functions added ############################################
if ($type == "loadPackageTypes") {
    // packagename changes
    $GeneralMemberId = isset($_REQUEST['GeneralMemberId']) ? trim($_REQUEST['GeneralMemberId']) : '';
    $GeneralUserType = isset($_REQUEST['GeneralUserType']) ? trim($_REQUEST['GeneralUserType']) : 'Passenger';
    if ($GeneralUserType == "Passenger") {
        $vLang = get_value("register_user", "vLang", "iUserId", $GeneralMemberId, '', 'true');
    }
    else {
        $vLang = get_value("register_driver", "vLang", "iDriverId", $GeneralMemberId, '', 'true');
    }
    if ($vLang == "" || $vLang == NULL) {
        $vLang = get_value('language_master', 'vCode', 'eDefault', 'Yes', '', 'true');
    }
    $vehicleTypes = get_value('package_type', 'iPackageTypeId,eStatus,vName_' . $vLang . ' as vName', 'eStatus', 'Active');
    if (count($vehicleTypes) > 0) {
        $returnArr['Action'] = "1";
        $returnArr['message'] = $vehicleTypes;
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
    }
    setDataResponse($returnArr);
}
###########################################################################
if ($type == "loadDeliveryDetails") {
    $iDriverId = isset($_REQUEST["iDriverId"]) ? $_REQUEST["iDriverId"] : '';
    $iTripId = isset($_REQUEST["iTripId"]) ? $_REQUEST["iTripId"] : '';
    // packagename changes
    $vLang = get_value("register_driver", "vLang", "iDriverId", $iDriverId, '', 'true');
    if ($vLang == "" || $vLang == NULL) {
        $vLang = get_value('language_master', 'vCode', 'eDefault', 'Yes', '', 'true');
    }
    $sql = "SELECT tr.iUserId,tr.vReceiverName,tr.vReceiverMobile,tr.tPickUpIns,tr.tDeliveryIns,tr.tPackageDetails,pt.vName_" . $vLang . " as packageType,concat(ru.vName,' ',ru.vLastName) as senderName, concat('+',ru.vPhoneCode,'',ru.vPhone) as senderMobile, concat('" . $tconfig['tsite_upload_images_passenger'] . "/',ru.iUserId,'/',ru.vImgName) as vImage from trips as tr, register_user as ru, package_type as pt WHERE ru.iUserId = tr.iUserId AND tr.iTripId = '" . $iTripId . "' AND pt.iPackageTypeId = tr.iPackageTypeId";
    $Data = $obj->MySQLSelect($sql);
    if (count($Data) > 0 && $iTripId != "") {
        $iUserId = $Data[0]['iUserId'];
        $sql = "SELECT co.vPhoneCode FROM country as co LEFT JOIN register_user as ru ON co.vCountryCode = ru.vCountry WHERE ru.iUserId = '" . $iUserId . "'";
        $sqlcountryCode = $obj->MySQLSelect($sql);
        $vPhoneCode = $sqlcountryCode[0]['vPhoneCode'];
        if ($vPhoneCode == "" || $vPhoneCode == NULL) {
            $vPhoneCode = $SITE_ISD_CODE;
        }
        $Data[0]['vReceiverMobile'] = "+" . $vPhoneCode . $Data[0]['vReceiverMobile'];
        $Data[0]['vReceiverMobileOriginal'] = $Data[0]['vReceiverMobile'];
        if ($CALLMASKING_ENABLED == "Yes") {
            $Data[0]['senderMobile'] = substr($Data[0]['senderMobile'], 0, -5) . 'XXXXX';
            $Data[0]['vReceiverMobile'] = substr($Data[0]['vReceiverMobile'], 0, -5) . 'XXXXX';
        }
        $returnArr['Action'] = "1";
        $returnArr['message'] = $Data[0];
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
    }
    setDataResponse($returnArr);
}
############################# get dynamic delivery fields ############################################################
if ($type == "getDeliveryFormFields") {
    global $obj;
    $GeneralMemberId = isset($_REQUEST['GeneralMemberId']) ? clean($_REQUEST['GeneralMemberId']) : '';
    $iMemberId = isset($_REQUEST['iMemberId']) ? clean($_REQUEST['iMemberId']) : '';
    $GeneralUserType = isset($_REQUEST['GeneralUserType']) ? clean($_REQUEST['GeneralUserType']) : '';
    if ($GeneralUserType == "Passenger") {
        $vLanguage = get_value('register_user', 'vLang', 'iUserId', $GeneralMemberId, '', 'true');
    }
    else {
        $vLanguage = get_value('register_driver', 'vLang', 'iDriverId', $iMemberId, '', 'true');
    }
    if ($vLanguage == "" || $vLanguage == NULL) {
        $vLanguage = get_value('language_master', 'vCode', 'eDefault', 'Yes', '', 'true');
    }
    $sql = "SELECT vFieldName_$vLanguage as vFieldName,eInputType,eAllowFloat,eRequired,eEditable,iOrder, CASE WHEN JSON_VALID(tDesc) THEN JSON_UNQUOTE(JSON_EXTRACT(tDesc, '$.tDesc_" . $vLanguage . "')) ELSE tDesc END AS tDesc,eStatus,iDeliveryFieldId from delivery_fields where eStatus='Active' ORDER BY iOrder ASC";
    $db_fields = $obj->MySQLSelect($sql);
    if (count($db_fields) > 0) {
        for ($i = 0; $i < count($db_fields); $i++) {
            if ($db_fields[$i]['eInputType'] == "Select") {
                $sql = "SELECT iPackageTypeId,vName_$vLanguage as vName FROM `package_type` where iDeliveryFieldId='" . $db_fields[$i]['iDeliveryFieldId'] . "' and eStatus='Active'";
                $db_field_values = $obj->MySQLSelect($sql);
                $Data_Field[$i]['Options'] = $db_field_values;
            }
            $Data_Field[$i]['vFieldName'] = $db_fields[$i]['vFieldName'];
            $Data_Field[$i]['eInputType'] = $db_fields[$i]['eInputType'];
            $Data_Field[$i]['eAllowFloat'] = $db_fields[$i]['eAllowFloat'];
            $Data_Field[$i]['eRequired'] = $db_fields[$i]['eRequired'];
            // $Data_Field[$i]['tDesc'] = !empty($db_fields[$i]['tDesc']) ? $db_fields[$i]['tDesc'] : "";
            $Data_Field[$i]['tDesc'] = "";
            // $Data_Field[$i]['tDesc'] = $db_fields[$i]['vFieldName'];
            $Data_Field[$i]['iDeliveryFieldId'] = $db_fields[$i]['iDeliveryFieldId'];
        }
        // echo "<pre>";print_r($Data_Field);exit;
        $returnArr['Action'] = "1";
        $returnArr['message'] = $Data_Field;
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
    }
    setDataResponse($returnArr);
}
#############################################################################################################
if ($type == "get_delivery_details") {
    global $obj;
    $iCabRequestId = isset($_REQUEST['iCabRequestId']) ? clean($_REQUEST['iCabRequestId']) : '';
    $iCabBookingId = isset($_REQUEST['iCabBookingId']) ? clean($_REQUEST['iCabBookingId']) : '';
    $iTripId = isset($_REQUEST['iTripId']) ? clean($_REQUEST['iTripId']) : '';
    // $vLanguage = get_value('register_driver', 'vLang', 'iMemberId',$iMemberId,'','true');
    if ($iCabBookingId != "" && $iCabBookingId > 0) {
        $ssql = " iCabBookingId='$iCabBookingId'";
    }
    else if ($iTripId != "" && $iTripId > 0) {
        $ssql = " iTripId='$iTripId'";
    }
    else {
        $ssql = " iCabRequestId='$iCabRequestId'";
    }
    $sql = "select vFieldName,iOrder,eInputType,tDesc,eAllowFloat,eStatus,eRequired,eEditable from trip_delivery_fields where $ssql";
    $db_trip_fields = $obj->MySQLSelect($sql);
    if (count($db_trip_fields) > 0) {
        for ($i = 0; $i < count($db_trip_fields); $i++) {
            $sql = "select vFieldName,eInputType from delivery_fields where iDeliveryFieldId='" . $db_trip_fields[$i]['iDeliveryFieldId'] . "'";
            $db_fields_data = $obj->MySQLSelect($sql);
            // echo "<pre>";print_r($db_fields_data);
            $Data_Field[$i]['vFieldName'] = $db_fields_data[0]['vFieldName'];
            $Data_Field[$i]['vValue'] = $db_trip_fields[$i]['vValue'];
            $Data_Field[$i]['iDeliveryFieldId'] = $db_trip_fields[$i]['iDeliveryFieldId'];
            if ($db_fields_data[0]['eInputType'] == "Select") {
                $sql = "SELECT vName FROM `package_type` where iDeliveryFieldId='" . $db_trip_fields[$i]['iDeliveryFieldId'] . "' and iPackageTypeId='" . $db_trip_fields[$i]['vValue'] . "'";
                $db_data = $obj->MySQLSelect($sql);
                $Data_Field[$i]['vValue'] = $db_data[0]['vName'];
            }
        }
        // echo "<pre>";print_r($Data_Field);exit;
        $returnArr['Action'] = "1";
        $returnArr['message'] = $Data_Field;
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
    }
    setDataResponse($returnArr);
}
#############################################################################################################
###########################################################################
if ($type == "getTripDeliveryDetails") {
    $iTripId = isset($_REQUEST["iTripId"]) ? $_REQUEST["iTripId"] : '';
    $userType = isset($_REQUEST["userType"]) ? $_REQUEST["userType"] : 'Passenger';
    $iCabRequestId = isset($_REQUEST["iCabRequestId"]) ? $_REQUEST["iCabRequestId"] : '';
    $iDriverId = isset($_REQUEST["iDriverId"]) ? $_REQUEST["iDriverId"] : '';
    $Data = array();
    if ($iTripId != "") {
        if ($userType == 'Driver') {
            $sql = "SELECT ru.iUserId,ru.vImgName as vUserImage,concat(ru.vName,' ',ru.vLastName) as vName, ru.vPhoneCode as vCode ,ru.vPhone as vMobile,ru.vTripStatus as vTripStatus, ru.vAvgRating as MemberRating, tr.* from trips as tr 
                LEFT JOIN register_user as ru ON ru.iUserId=tr.iUserId
                WHERE tr.iTripId = '" . $iTripId . "'";
            $dataUser = $obj->MySQLSelect($sql);
            //$Data['senderDetails'] = $obj->MySQLSelect($sql);
            $iMemberId = get_value('trips', 'iDriverId', 'iTripId', $iTripId, '', 'true');
            $vLangCode = get_value('register_driver', 'vLang', 'iDriverId', $iMemberId, '', 'true');
            //added by SP on 19-02-2021 for custom notification button of view details.
            $sqlNoti = "SELECT ru.iUserId,ru.vimgname as riderImage,concat(ru.vName,' ',ru.vLastName) as riderName, ru.vPhoneCode ,ru.vPhone as riderMobile,ru.vTripStatus as driverStatus, ru.vAvgRating as riderRating,tr.* from trips as tr LEFT JOIN register_user as ru ON ru.iUserId=tr.iUserId WHERE tr.iTripId = '" . $iTripId . "'";
            $dataUserNoti = $obj->MySQLSelect($sqlNoti);
            $dataUserNoti[0]['vOrderNoUnique'] = !empty($dataUserNoti[0]['vOrderNoUnique']) ? $dataUserNoti[0]['vOrderNoUnique'] : "";
            $Data['driverDetails'] = $dataUserNoti[0];
        }
        else {
            $sql = "SELECT rd.iDriverId,rd.vImage as vUserImage,concat(rd.vName,' ',rd.vLastName) as vName, rd.vCode as vCode,rd.vPhone as vMobile,rd.vTripStatus as vTripStatus, rd.vAvgRating as MemberRating, tr.* from trips as tr 
                LEFT JOIN register_driver as rd ON rd.iDriverId=tr.iDriverId
                WHERE tr.iTripId = '" . $iTripId . "'";
            $dataUser = $obj->MySQLSelect($sql);
            $iMemberId = get_value('trips', 'iUserId', 'iTripId', $iTripId, '', 'true');
            $vLangCode = get_value('register_user', 'vLang', 'iUserId', $iMemberId, '', 'true');
            //added by SP on 19-02-2021 for custom notification button of view details.
            $sqlNoti = "SELECT rd.iDriverId,rd.vImage as driverImage,concat(rd.vName,' ',rd.vLastName) as driverName, rd.vCode ,rd.vPhone as driverMobile,rd.vTripStatus as driverStatus, rd.vAvgRating as driverRating,tr.* from trips as tr LEFT JOIN register_driver as rd ON rd.iDriverId=tr.iDriverId WHERE tr.iTripId = '" . $iTripId . "'";
            $dataUserNoti = $obj->MySQLSelect($sqlNoti);
            $Data['driverDetails'] = $dataUserNoti[0];
        }
        $vCurrencyDriver = get_value('register_driver', 'vCurrencyDriver', 'iDriverId', $iDriverId, '', 'true');
        if ($vCurrencyDriver == "" || $vCurrencyDriver == NULL) {
            $vCurrencyDriver = get_value('currency', 'vName', 'eDefault', 'Yes', '', 'true');
        }
        $UserCurrencyData = get_value('currency', 'vSymbol, Ratio', 'vName', $vCurrencyDriver);
        $Sender_Signature = "";
        if ((file_exists($tconfig["tsite_upload_trip_signature_images_path"] . $dataUser[0]['vSignImage'])) && $dataUser[0]['vSignImage'] != "") {
            $Sender_Signature = $tconfig["tsite_upload_trip_signature_images"] . $dataUser[0]['vSignImage'];
        }
        $dataUser[0]['Sender_Signature'] = $Sender_Signature;
        $dataUser[0]['vImage'] = $dataUser[0]['vUserImage'];
        //Added By HJ On 29-12-2018 For Get Mobile No With Mask Character Start
        $dataUser[0]['vOrgMobile'] = $dataUser[0]['vMobile']; // 756466
        $mobileLength = strlen($dataUser[0]['vMobile']) - 2;
        $maskMobileNo = substr($dataUser[0]['vMobile'], 0, 2);
        //echo str_repeat("X", $mobileLength);die;
        $dataUser[0]['vMobile'] = $maskMobileNo . str_repeat("X", $mobileLength);
        //Added By HJ On 29-12-2018 For Get Mobile No With Mask Character End
        $dataUser[0]['vOrderNoUnique'] = !empty($dataUser[0]['vOrderNoUnique']) ? $dataUser[0]['vOrderNoUnique'] : "";
        $Data['MemberDetails'] = $dataUser[0];
        $eAskCodeToUser = $dataUser[0]['eAskCodeToUser'];
        $vRandomCode = $dataUser[0]['vRandomCode'];
        $vCurrencyDriver = get_value('register_driver', 'vCurrencyDriver', 'iDriverId', $dataUser[0]['iTripId'], '', 'true');
        //echo $vLangCode;
        if ($vLangCode == "" || $vLangCode == NULL) {
            $vLangCode = get_value('language_master', 'vCode', 'eDefault', 'Yes', '', 'true');
        }
        $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLangCode, "1", $iServiceId);
        $Driver_Acceprt_Delivery_Request = $languageLabelsArr['LBL_DRIVER_ACCEPTED_DELIVERY_REQUEST_TXT'];
        $Driver_Arrived_Pick_Location = $languageLabelsArr['LBL_DRIVER_ARRIVED_PICK_LOCATION_TXT'];
        if ($dataUser[0]['eType'] == "Multi-Delivery") {
            $Driver_Acceprt_Delivery_Request = $languageLabelsArr['LBL_DELIVERY_DRIVER_ACCEPT_REQUEST_MULTI'];
            $Driver_Arrived_Pick_Location = $languageLabelsArr['LBL_DELIVERY_DRIVER_ARRIVED_MULTI'];
        }
        $Driver_Onway_Delivery = $languageLabelsArr['LBL_DRIVER_ONWAY_DELIVERY_TXT'];
        $Driver_Arrived_Delivery_Parcel_User = $languageLabelsArr['LBL_DRIVER_ARRIVED_DELIVERY_PARCEL_USER_TXT'];
        $Driver_CANCEL_TRIP = $languageLabelsArr['LBL_CANCEL_START_TRIP'];
        $LBL_RECEIPENT_TXT = $languageLabelsArr['LBL_RECEIPENT_TXT'];
        $LBL_EACH_RECIPIENT = $languageLabelsArr['LBL_EACH_RECIPIENT'];
        $LBL_SENDER = $languageLabelsArr['LBL_SENDER'];
        // $DELIVERY_VERIFICATION_METHOD=$CONFIG_OBJ->getConfigurations("configurations","DELIVERY_VERIFICATION_METHOD");
        $sql = "SELECT iTripDeliveryLocationId FROM trips_delivery_locations WHERE `iActive`='On Going Trip' AND iTripId='" . $iTripId . "' LIMIT 1";
        $ongoingTrip = $obj->MySQLSelect($sql);
        //$Data['onGoingTripLocationId'] = $ongoingTrip[0]['iTripDeliveryLocationId'];
        $Data['onGoingTripLocationId'] = empty($ongoingTrip[0]['iTripDeliveryLocationId']) ? "" : $ongoingTrip[0]['iTripDeliveryLocationId'];
        //$sql = "SELECT tdl.*,r.*,pt.vName as PackageName FROM trips_delivery_locations as tdl LEFT JOIN recipients as r ON r.iRecipientId=tdl.iRecipientId LEFT JOIN package_type as pt ON pt.iPackageTypeId=tdl.iPackageTypeId  WHERE tdl.`iTripId`='".$iTripId."' order by tdl.iTripDeliveryLocationId ASC";
        $sql = "SELECT tdl.* FROM trips_delivery_locations as tdl WHERE tdl.`iTripId`='" . $iTripId . "' order by tdl.iTripDeliveryLocationId ASC";
        $Data_tripLocations = $obj->MySQLSelect($sql);
        // $Data['Deliveries'] = $Data_tripLocations;
        $returnArr['ePaymentBySender'] = 'Yes';
        // echo "<pre>";print_r($Data_tripLocations);exit;
        $sql = "select * from trip_delivery_fields where iTripId ='$iTripId' order by iDeliveryFieldId";
        $db_trip_fields = $obj->MySQLSelect($sql);
        /*---------------------  --------------------*/
        $DriverCurrencyData = get_value('currency', 'vSymbol, Ratio', 'vName', $dataUser[0]['vCurrencyDriver']);
        if ($Data_tripLocations[0]['ePaymentBy'] == "Individual") {
            $total_reci_count = count($Data_tripLocations);
            $fare_individual = round(($dataUser[0]['iFare'] / $total_reci_count), 2);
            // $fare_individual = $Data_tripLocations[$j]['fIndividualAmount'];
            $fare_individual = round($fare_individual * $DriverCurrencyData[0]['Ratio'], 2);
            //added by SP for rounding off on 08-11-2019 for multi delivery driver side start
            if ($userType == "Driver") {
                $sqlp = "SELECT cu.vName, cu.iCurrencyId, cu.eRoundingOffEnable,rd.vCurrencyDriver FROM register_driver AS rd LEFT JOIN currency AS cu ON rd.vCurrencyDriver = cu.vName WHERE rd.iDriverId = '" . $iMemberId . "'";
                $currData = $obj->MySQLSelect($sqlp);
                $vCurrency = $currData[0]['vName'];
                $samecur = ($dataUser[0]['vCurrencyDriver'] == $currData[0]['vCurrencyDriver'] && $dataUser[0]['vCurrencyDriver'] == $dataUser[0]['vCurrencyPassenger']) ? 1 : 0;
            }
            if ($currData[0]['eRoundingOffEnable'] == "Yes" && $samecur == 1 && $MODULES_OBJ->isEnableRoundingMethod()) {
                $roundingOffTotal_fare_amountArr = getRoundingOffAmount($fare_individual, $vCurrency);
                $roundingOffTotal_fare_amount = $roundingOffTotal_fare_amountArr['finalFareValue'];
                if ($roundingOffTotal_fare_amountArr['method'] == "Addition") {
                    $roundingMethod = "";
                }
                else {
                    $roundingMethod = "-";
                }
                $fare_individual = $roundingOffTotal_fare_amount;
            }
            if ($currData[0]['eRoundingOffEnable'] == "Yes" && $dataUser[0]['vCurrencyDriver'] == $currData[0]['vCurrencyDriver'] && $dataUser[0]['vCurrencyDriver'] != $dataUser[0]['vCurrencyPassenger'] && $MODULES_OBJ->isEnableRoundingMethod()) {
                $roundingOffTotal_fare_amountArr = getRoundingOffAmount($fare_individual, $vCurrency);
                $roundingOffTotal_fare_amount = $roundingOffTotal_fare_amountArr['finalFareValue'];
                if ($roundingOffTotal_fare_amountArr['method'] == "Addition") {
                    $roundingMethod = "";
                }
                else {
                    $roundingMethod = "-";
                }
                $fare_individual = $roundingOffTotal_fare_amount;
            }
            // $returnArr['Fare_Payable'] = $DriverCurrencyData[0]['vSymbol'] . " " . formatNum($fare_individual);
            $returnArr['Fare_Payable'] = formateNumAsPerCurrency($fare_individual, $dataUser[0]['vCurrencyDriver']);
        }
        else {
            // $returnArr['Fare_Payable'] = $DriverCurrencyData[0]['vSymbol'] . " " . formatNum(round($dataUser[0]['iFare'] * $DriverCurrencyData[0]['Ratio'], 2));
            $returnArr['Fare_Payable'] = formateNumAsPerCurrency(round($dataUser[0]['iFare'] * $DriverCurrencyData[0]['Ratio'], 2), $dataUser[0]['vCurrencyDriver']);
        }
        
        /*---------------------  --------------------*/
        if (count($db_trip_fields) > 0) {
            for ($j = 0; $j < count($Data_tripLocations); $j++) {
                $k = 0;
                for ($i = 0; $i < count($db_trip_fields); $i++) {
                    if ($Data_tripLocations[$j]['iTripDeliveryLocationId'] == $db_trip_fields[$i]['iTripDeliveryLocationId']) {
                        if ($db_trip_fields[$i]['vValue'] != "") {
                            $sql = "select vFieldName_$vLangCode as vFieldName,eInputType from delivery_fields where iDeliveryFieldId='" . $db_trip_fields[$i]['iDeliveryFieldId'] . "'";
                            $db_fields_data = $obj->MySQLSelect($sql);
                            $Data_Field[$j][$k]['vFieldName'] = $db_fields_data[0]['vFieldName'];
                            $Data_Field[$j][$k]['vValue'] = $db_trip_fields[$i]['vValue'];
                            if ($db_trip_fields[$i]['iDeliveryFieldId'] == "3") {
                                //$Data_Field[$j][$k]['vValue'] = "+" . $dataUser[0]['vCode'] . $db_trip_fields[$i]['vValue'];
                                $Data_Field[$j][$k]['vValue'] = "+" . $db_trip_fields[$i]['vValue']; // Removed Country Code By HJ On 01-01-2019 As Per Discuss WIth DT As Per Above Line
                                $mobileLength1 = strlen($db_trip_fields[$i]['vValue']) - 2;
                                $maskMobileNo1 = substr($db_trip_fields[$i]['vValue'], 0, 2);
                                $Data_Field[$j][$k]['vMaskValue'] = "+" . $maskMobileNo1 . str_repeat("X", $mobileLength1);
                            }
                            $Data_Field[$j][$k]['iDeliveryFieldId'] = $db_trip_fields[$i]['iDeliveryFieldId'];
                            if ($db_trip_fields[$i]['iDeliveryFieldId'] == "2") {
                                if ($Data_tripLocations[0]['ePaymentBy'] == "Receiver" && $Data_tripLocations[$j]['ePaymentByReceiver'] == "Yes") {
                                    $PaymentPerson = $db_trip_fields[$i]['vValue'];
                                    $ReceNo = $j + 1;
                                }
                                $vReceiverName = $db_trip_fields[$i]['vValue'];

                            }
                            if ($db_fields_data[0]['eInputType'] == "Select") {
                                $sql = "SELECT vName_$vLangCode as vName FROM `package_type` where iDeliveryFieldId='" . $db_trip_fields[$i]['iDeliveryFieldId'] . "' and iPackageTypeId='" . $db_trip_fields[$i]['vValue'] . "'";
                                $db_data = $obj->MySQLSelect($sql);
                                $Data_Field[$j][$k]['vValue'] = $db_data[0]['vName'];
                            }
                            //added by SP for giving currency symbol infront of price like material price on 15-10-2019
                            if ($db_trip_fields[$i]['iDeliveryFieldId'] == "7") {
                                $fmatprice = round(($Data_Field[$j][$k]['vValue'] * $UserCurrencyData[0]['Ratio']), 2);
                                //$Data_Field[$j][$k]['vValue'] = $UserCurrencyData[0]['vSymbol'] . " " . formatnum($fmatprice);
                                $Data_Field[$j][$k]['vValue'] = formateNumAsPerCurrency($fmatprice, $vCurrencyDriver);
                                //$Data_Field[$j][$k]['vValue'] = $Data_Field[$j][$k]['vValue'];
                            }
                            $k++;
                        }
                    }
                }



                $Data_Field[$j][$k]['vFieldName'] = $languageLabelsArr['LBL_MULTI_AMOUNT_COLLECT_TXT'];
                $Data_Field[$j][$k]['vValue'] = $returnArr['Fare_Payable'];
                $k++;
                $Data_Field[$j][$k]['vFieldName'] = "Address";
                $Data_Field[$j][$k]['tSaddress'] = $Data_tripLocations[$j]['tSaddress'];
                $Data_Field[$j][$k]['tStartLat'] = $Data_tripLocations[$j]['tStartLat'];
                $Data_Field[$j][$k]['tStartLong'] = $Data_tripLocations[$j]['tStartLong'];
                $Data_Field[$j][$k]['tDaddress'] = $Data_tripLocations[$j]['tDaddress'];
                $Data_Field[$j][$k]['tEndLat'] = $Data_tripLocations[$j]['tEndLat'];
                $Data_Field[$j][$k]['tEndLong'] = $Data_tripLocations[$j]['tEndLong'];
                $Data_Field[$j][$k]['ePaymentByReceiver'] = $Data_tripLocations[$j]['ePaymentByReceiver'];
                $Data_Field[$j][$k]['iTripDeliveryLocationId'] = $Data_tripLocations[$j]['iTripDeliveryLocationId'];
                $Data_Field[$j][$k]['iActive'] = $Data_tripLocations[$j]['iActive'];
                $Data_Field[$j][$k]['tStartTime'] = $Data_tripLocations[$j]['tStartTime'];
                $Data_Field[$j][$k]['tEndTime'] = $Data_tripLocations[$j]['tEndTime'];
                $Data_Field[$j][$k]['vReceiverName'] = $vReceiverName;
                $Data_Field[$j][$k]['eCancelled'] = $Data_tripLocations[$j]['eCancelled'];
                $Data_Field[$j][$k]['vDeliveryConfirmCode'] = $Data_tripLocations[$j]['vDeliveryConfirmCode'];
                $Data_Field[$j][$k]['BG_color'] = APP_THEME_COLOR;
                $Data_Field[$j][$k]['TEXT_color'] = "#FFFFFF";

                $Data_Field[$j][$k]['isActiveDelivery'] = 'No';
                $jj = 0;
                if($j != 0) {
                    $jj = $j - 1;
                }
                if (in_array($Data_tripLocations[$j]['iActive'], ['On Going Trip']) || (in_array($Data_tripLocations[$j]['iActive'], ['Active']) && in_array($Data_tripLocations[$jj]['iActive'], ['Finished', 'Canceled']))) {
                    $Data_Field[$j][$k]['isActiveDelivery'] = 'Yes';
                }

                $returnArr['ePaymentBy'] = $Data_tripLocations[$j]['ePaymentBy'];
                $Receipent_Signature = "";
                if ($Data_tripLocations[$j]['ePaymentByReceiver'] == 'Yes') {
                    $returnArr['ePaymentBySender'] = 'No';
                    if ($Data_tripLocations[0]['ePaymentBy'] == "Individual") {
                        $returnArr['PaymentPerson'] = $LBL_EACH_RECIPIENT;
                    }
                    else {
                        $returnArr['PaymentPerson'] = $LBL_RECEIPENT_TXT . $ReceNo . " (" . $PaymentPerson . " )";
                    }
                }
                if ((file_exists($tconfig["tsite_upload_trip_signature_images_path"] . $Data_tripLocations[$j]['vSignImage'])) && $Data_tripLocations[$j]['vSignImage'] != "") {
                    $Receipent_Signature = $tconfig["tsite_upload_trip_signature_images"] . $Data_tripLocations[$j]['vSignImage'];
                }
                $Data_Field[$j][$k]['Receipent_Signature'] = $Receipent_Signature;

            }
        }


        $Data['Deliveries'] = !empty($Data_Field) ? $Data_Field : "";
        /*echo "<pre>";
        print_r();
        exit;*/
        foreach ($Data_Field as $key => $value) {
            foreach ($value as $v1) {
                if ($v1['vFieldName'] != "Address") {
                    $Data1[$key][$v1['vFieldName']] = $v1['vValue'];
                }
                else {
                    foreach ($v1 as $k1 => $v2) {
                        $Data1[$key][$k1] = $v2;
                    }
                }
            }
        }
        if ($returnArr['ePaymentBySender'] == 'Yes') {
            $iUserId = get_value('trips', 'iUserId', 'iTripId', $iTripId, '', 'true');
            $Name = get_value('register_user', 'concat(vName," ",vLastName)', 'iUserId', $iUserId, '', 'true');
            $returnArr['PaymentPerson'] = $LBL_SENDER . " (" . $Name . " )";
        }
        $fTripGenerateFare = $dataUser[0]['iFare'];
        $fTripGenerateFare = round(($fTripGenerateFare * $DriverCurrencyData[0]['Ratio']), 2);
        //added by SP for rounding off on 07-11-2019 for multi delivery driver side start
        if ($userType == "Driver") {
            $sqlp = "SELECT cu.vName, cu.iCurrencyId, cu.eRoundingOffEnable, rd.vCurrencyDriver, cu.ratio FROM register_driver AS rd LEFT JOIN currency AS cu ON rd.vCurrencyDriver = cu.vName WHERE rd.iDriverId = '" . $iMemberId . "'";
            $currData = $obj->MySQLSelect($sqlp);
            $vCurrency = $currData[0]['vName'];
            $samecur = ($dataUser[0]['vCurrencyDriver'] == $currData[0]['vCurrencyDriver'] && $dataUser[0]['vCurrencyDriver'] == $dataUser[0]['vCurrencyPassenger']) ? 1 : 0;
        }
        else {
            $sqlp = "SELECT cu.vName, cu.iCurrencyId, cu.eRoundingOffEnable, ru.vCurrencyPassenger, cu.ratio FROM register_user AS ru LEFT JOIN currency AS cu ON ru.vCurrencyPassenger = cu.vName WHERE ru.iUserId = '" . $iMemberId . "'";
            $currData = $obj->MySQLSelect($sqlp);
            $vCurrency = $currData[0]['vName'];
            $samecur = ($dataUser[0]['vCurrencyPassenger'] == $currData[0]['vCurrencyPassenger']) ? 1 : 0;
        }
        /*new add by for driver rounding*/
        if ($dataUser[0]['vCurrencyDriver'] == $currData[0]['vCurrencyDriver'] && $currData[0]['eRoundingOffEnable'] == "Yes" && $dataUser[0]['vCurrencyDriver'] != $dataUser[0]['vCurrencyPassenger'] && $MODULES_OBJ->isEnableRoundingMethod()) {
            if (isset($dataUser[0]['fRoundingAmountDriver']) && !empty($dataUser[0]['fRoundingAmountDriver']) && $dataUser[0]['fRoundingAmountDriver'] != 0) {
                $roundingOffTotal_fare_amountArrDriver['method'] = $dataUser[0]['eRoundingTypeDriver'];
                $roundingOffTotal_fare_amountArrDriver['differenceValue'] = $dataUser[0]['fRoundingAmountDriver'];
                $roundingOffTotal_fare_amountArrDriver = getRoundingOffAmounttrip($fTripGenerateFare, $dataUser[0]['fRoundingAmountDriver'], $dataUser[0]['eRoundingTypeDriver']); ////start
                if ($roundingOffTotal_fare_amountArrDriver['method'] == "Addition") {
                    $roundingMethod = "";
                }
                else {
                    $roundingMethod = "-";
                }
                $roundingOffTotal_fare_amount = isset($roundingOffTotal_fare_amountArrDriver['finalFareValue']) && $roundingOffTotal_fare_amountArrDriver['finalFareValue'] != '' ? $roundingOffTotal_fare_amountArrDriver['finalFareValue'] : "0.00";
                $rounding_diff = isset($roundingOffTotal_fare_amountArrDriver['differenceValue']) && $roundingOffTotal_fare_amountArrDriver['differenceValue'] != '' ? $roundingOffTotal_fare_amountArrDriver['differenceValue'] : "0.00";
                $fTripGenerateFare = $roundingOffTotal_fare_amount;
            }
        }
        /*new add by for driver rounding*/
        if (isset($dataUser[0]['fRoundingAmount']) && !empty($dataUser[0]['fRoundingAmount']) && $dataUser[0]['fRoundingAmount'] != 0 && $samecur == 1 && $currData[0]['eRoundingOffEnable'] == "Yes" && $MODULES_OBJ->isEnableRoundingMethod()) {
            $roundingOffTotal_fare_amountArr['method'] = $dataUser[0]['eRoundingType'];
            $roundingOffTotal_fare_amountArr['differenceValue'] = $dataUser[0]['fRoundingAmount'];
            $roundingOffTotal_fare_amountArr = getRoundingOffAmounttrip($fTripGenerateFare, $dataUser[0]['fRoundingAmount'], $dataUser[0]['eRoundingType']); ////start
            //$returnArr['roundingOffAmountArr'] = $roundingOffTotal_fare_amount;
            if ($roundingOffTotal_fare_amountArr['method'] == "Addition") {
                $roundingMethod = "";
            }
            else {
                $roundingMethod = "-";
            }
            $roundingOffTotal_fare_amount = isset($roundingOffTotal_fare_amountArr['finalFareValue']) && $roundingOffTotal_fare_amountArr['finalFareValue'] != '' ? $roundingOffTotal_fare_amountArr['finalFareValue'] : "0.00";
            $rounding_diff = isset($roundingOffTotal_fare_amountArr['differenceValue']) && $roundingOffTotal_fare_amountArr['differenceValue'] != '' ? $roundingOffTotal_fare_amountArr['differenceValue'] : "0.00";
            $fTripGenerateFare = $roundingOffTotal_fare_amount;
        }
        //added by SP for rounding off on 07-11-2019 for multi delivery driver side end
        //$returnArr['DriverPaymentAmount'] = $DriverCurrencyData[0]['vSymbol'] . " " . formatnum($fTripGenerateFare);
        $returnArr['DriverPaymentAmount'] = formateNumAsPerCurrency($fTripGenerateFare, $dataUser[0]['vCurrencyDriver']);
        $testBool = 1;
        if ($dataUser[0]['iActive'] == "Canceled") {
            $returnArr['PaymentPerson'] = $returnArr['Sender_Signature'] = "";
        }
        if (count($dataUser) > 0) {
            $Data['States'] = array();
            $Data_tTripRequestDate = $dataUser[0]['tTripRequestDate'];
            $Data_tDriverArrivedDate = $dataUser[0]['tDriverArrivedDate'];
            // $Data_dDeliveredDate= $dataUser[0]['dDeliveredDate'];
            $Data_tStartDate = $dataUser[0]['tStartDate'];
            $Data_tEndDate = $dataUser[0]['tEndDate'];
            $systemTimeZone = date_default_timezone_get();
            $Data_tTripRequestDate_converttz = converToTz($Data_tTripRequestDate, $vTimeZone, $systemTimeZone);
            $Data_tDriverArrivedDate_converttz = converToTz($Data_tDriverArrivedDate, $vTimeZone, $systemTimeZone);
            $i = 0;
            if ($Data_tTripRequestDate != "" && $Data_tTripRequestDate != "0000-00-00 00:00:00" && $testBool == 1) {
                $msg = 'Provider accepted the request.';
                if ($userType != 'Passenger') {
                    $msg = 'You accepted the request.';
                }
                // echo "==".$Driver_Acceprt_Delivery_Request."==";exit;
                $Data['States'][$i]['text'] = $Driver_Acceprt_Delivery_Request;
                $Data['States'][$i]['time'] = "at " . date("h:i A", strtotime($Data_tTripRequestDate_converttz));
                $Data['States'][$i]['timediff'] = @round(abs(strtotime($Data_tTripRequestDate) - strtotime(date("Y-m-d H:i:s"))) / 60, 0) . " mins ago";
                $Data['States'][$i]['type'] = "Accept";
                $i++;
            }
            else {
                $testBool = 0;
            }
            if ($Data_tDriverArrivedDate != "" && $Data_tDriverArrivedDate != "0000-00-00 00:00:00" && $testBool == 1) {
                $msg = "Provider arrived to your location.";
                if ($userType != 'Passenger') {
                    $msg = "You arrived to user's location.";
                }
                $Data['States'][$i]['text'] = $Driver_Arrived_Pick_Location;
                if ($userType == "Passenger" && $eAskCodeToUser == "Yes") {
                    $Data['States'][$i]['text'] = $Driver_Arrived_Pick_Location . " " . $languageLabelsArr['LBL_YOUR_TRIP_OTP_TXT'] . " " . $vRandomCode . ".";
                }
                $Data['States'][$i]['time'] = "at " . date("h:i A", strtotime($Data_tDriverArrivedDate_converttz));
                $Data['States'][$i]['timediff'] = @round(abs(strtotime($Data_tDriverArrivedDate) - strtotime(date("Y-m-d H:i:s"))) / 60, 0) . " mins ago";
                $Data['States'][$i]['type'] = "Arrived";
                $i++;
                foreach ($Data1 as $value) {
                    // echo "<pre>";print_r($Data1);exit;
                    $Data_tStartTime_converttz = converToTz($value['tStartTime'], $vTimeZone, $systemTimeZone);
                    $Data_tEndTime_converttz = converToTz($value['tEndTime'], $vTimeZone, $systemTimeZone);
                    // $Data_tDeliveredTime_converttz = converToTz($value['tDeliveredTime'], $vTimeZone, $systemTimeZone);
                    $vDeliveryConfirmCode = ($DELIVERY_VERIFICATION_METHOD == "Code") ? $value['vDeliveryConfirmCode'] : "";
                    if ($value['iActive'] == "Canceled" && $value['eCancelled'] == "Yes") {
                        if ($value['tEndTime'] != "0000-00-00 00:00:00") {
                            $Data['States'][$i]['text'] = $Driver_CANCEL_TRIP . " " . $value['vReceiverName'];
                            $Data['States'][$i]['time'] = "at " . date("h:i A", strtotime($Data_tEndTime_converttz));
                            $Data['States'][$i]['timediff'] = @round(abs(strtotime($value['tEndTime']) - strtotime(date("Y-m-d H:i:s"))) / 60, 0) . " mins ago";
                            $Data['States'][$i]['vDeliveryConfirmCode'] = $vDeliveryConfirmCode;
                            $Data['States'][$i]['type'] = "Canceled";
                            $i++;
                        }
                        else {
                            break;
                        }
                    }
                    else if ($value['iActive'] == "Finished" && $value['eCancelled'] == "Yes") {
                        if ($value['tStartTime'] != "0000-00-00 00:00:00") {
                            $Data['States'][$i]['text'] = $Driver_Onway_Delivery . " " . $value['vReceiverName'];
                            $Data['States'][$i]['time'] = "at " . date("h:i A", strtotime($Data_tStartTime_converttz));
                            $Data['States'][$i]['timediff'] = @round(abs(strtotime($value['tStartTime']) - strtotime(date("Y-m-d H:i:s"))) / 60, 0) . " mins ago";
                            $Data['States'][$i]['vDeliveryConfirmCode'] = $vDeliveryConfirmCode;
                            $Data['States'][$i]['type'] = "Onway";
                            $i++;
                        }
                        else {
                            break;
                        }
                        if ($value['tEndTime'] != "0000-00-00 00:00:00") {
                            $Data['States'][$i]['text'] = $Driver_CANCEL_TRIP . " " . $value['vReceiverName'];
                            $Data['States'][$i]['time'] = "at " . date("h:i A", strtotime($Data_tEndTime_converttz));
                            $Data['States'][$i]['timediff'] = @round(abs(strtotime($value['tEndTime']) - strtotime(date("Y-m-d H:i:s"))) / 60, 0) . " mins ago";
                            $Data['States'][$i]['vDeliveryConfirmCode'] = $vDeliveryConfirmCode;
                            $Data['States'][$i]['type'] = "Canceled";
                            $i++;
                        }
                        else {
                            break;
                        }
                    }
                    else {
                        if ($value['tStartTime'] != "0000-00-00 00:00:00") {
                            $Data['States'][$i]['text'] = $Driver_Onway_Delivery . " " . $value['vReceiverName'];
                            $Data['States'][$i]['time'] = "at " . date("h:i A", strtotime($Data_tStartTime_converttz));
                            $Data['States'][$i]['timediff'] = @round(abs(strtotime($value['tStartTime']) - strtotime(date("Y-m-d H:i:s"))) / 60, 0) . " mins ago";
                            $Data['States'][$i]['vDeliveryConfirmCode'] = $vDeliveryConfirmCode;
                            $Data['States'][$i]['type'] = "Onway";
                            $i++;
                        }
                        else {
                            break;
                        }
                        if ($value['tEndTime'] != "0000-00-00 00:00:00") {
                            $Data['States'][$i]['text'] = $Driver_Arrived_Delivery_Parcel_User . " " . $value['vReceiverName'];
                            $Data['States'][$i]['time'] = "at " . date("h:i A", strtotime($Data_tEndTime_converttz));
                            $Data['States'][$i]['timediff'] = @round(abs(strtotime($value['tEndTime']) - strtotime(date("Y-m-d H:i:s"))) / 60, 0) . " mins ago";
                            $Data['States'][$i]['vDeliveryConfirmCode'] = $vDeliveryConfirmCode;
                            $Data['States'][$i]['type'] = "Delivered";
                            $i++;
                        }
                        else {
                            break;
                        }
                    }
                }
            }
            else {
                $testBool = 0;
            }
        }
        else {
            $Data['States'] = array();
        }
        if (count($Data) > 0) {
            $returnArr['Action'] = "1";
            $returnArr['DELIVERY_VERIFICATION_METHOD'] = $DELIVERY_VERIFICATION_METHOD;
            //Added By HJ On 24-04-2020 As Per Discuss With GP Start
            if (strtoupper($Data['MemberDetails']['ePayWallet']) == 'YES' && strtoupper($Data['MemberDetails']['eWalletDebitAllow']) == 'YES') {
                $Data['MemberDetails']['vTripPaymentMode'] = $languageLabelsArr['LBL_WALLET_TXT'];
            }
            //Added By HJ On 24-04-2020 As Per Discuss With GP End
            if ($Data['MemberDetails']['vTripPaymentMode'] == 'Cash') {
                $Data['MemberDetails']['vTripPaymentMode'] = $languageLabelsArr['LBL_CASH_TXT'];
            }
            if ($Data['MemberDetails']['vTripPaymentMode'] == 'Card') {
                $Data['MemberDetails']['vTripPaymentMode'] = $languageLabelsArr['LBL_CARD'];
            }
            $returnArr['message'] = $Data;
            $TripDetailsArr = FetchTripFareDetails($iTripId, $iMemberId, $userType, "DISPLAY");
            $FareDetailsArr = array($TripDetailsArr['FareDetailsNewArr']);
            $FareSubTotal = $TripDetailsArr['FareSubTotal'];
            $HistoryFareDetailsArr = array();
            foreach ($FareDetailsArr as $inner) {
                $HistoryFareDetailsArr = array_merge($HistoryFareDetailsArr, $inner);
            }
            //$HistoryFareDetailsArr[count($HistoryFareDetailsArr)][$languageLabelsArr['LBL_SUBTOTAL_TXT']] = $FareSubTotal;
            $returnArr['FareDetailsNewArr'] = $HistoryFareDetailsArr;
            $iVehicleTypeId = get_value('trips', 'iVehicleTypeId', 'iTripId', $iTripId, '', 'true');
            $vVehicleType = get_value('vehicle_type', "vVehicleType_" . $vLangCode, 'iVehicleTypeId', $iVehicleTypeId, '', 'true');
            $returnArr['carTypeName'] = $vVehicleType;
        }
        else {
            $returnArr['Action'] = "0";
            $returnArr['message'] = "LBL_NO_DRIVER_FOUND";
        }
    }
    else if ($iCabRequestId != "") {
        if ($userType == 'Driver') {
            $sql = "SELECT ru.vCurrencyPassenger as vCurrency, ru.iUserId,ru.vImgName as vImage,concat(ru.vName,' ',ru.vLastName) as vName, ru.vPhoneCode as vCode ,ru.vPhone as vMobile,ru.vTripStatus as vTripStatus, ru.vAvgRating as MemberRating, cr.iCabRequestId,cr.iCabBookingId,cr.iDriverId,cr.iTripId,cr.eStatus,cr.vSourceLatitude,cr.vSourceLongitude,cr.tSourceAddress,cr.vDestLatitude,cr.vDestLongitude,cr.tDestAddress,cr.ePayType,cr.iVehicleTypeId,cr.fPickUpPrice,cr.fNightPrice,cr.iFare,cr.fTripGenerateFare,cr.eFlatTrip from cab_request_now as cr 
                LEFT JOIN register_user as ru ON ru.iUserId=cr.iUserId
                WHERE cr.iCabRequestId = '" . $iCabRequestId . "'";
            $dataUser = $obj->MySQLSelect($sql);
            //$Data['senderDetails'] = $obj->MySQLSelect($sql);
            $iMemberId = get_value('cab_request_now', 'iUserId', 'iCabRequestId', $iCabRequestId, '', 'true');
            $vLangCode = get_value('register_user', 'vLang', 'iUserId', $iMemberId, '', 'true');
        }
        else {
            $sql = "SELECT rd.vCurrencyDriver as vCurrency,rd.iDriverId,rd.vImage as vImage,concat(rd.vName,' ',rd.vLastName) as vName, rd.vCode as vCode,rd.vPhone as vMobile,rd.vTripStatus as vTripStatus, rd.vAvgRating as MemberRating, cr.iCabRequestId,cr.iCabBookingId,cr.iDriverId,cr.iTripId,cr.eStatus,cr.vSourceLatitude,cr.vSourceLongitude,cr.tSourceAddress,cr.vDestLatitude,cr.vDestLongitude,cr.tDestAddress,cr.ePayType,cr.iVehicleTypeId,cr.fPickUpPrice,cr.fNightPrice,cr.iFare,cr.fTripGenerateFare,cr.eFlatTrip from cab_request_now as cr 
                LEFT JOIN register_driver as rd ON rd.iDriverId=cr.iDriverId
                WHERE cr.iCabRequestId = '" . $iCabRequestId . "'";
            $dataUser = $obj->MySQLSelect($sql);
            $iMemberId = get_value('cab_request_now', 'iDriverId', 'iCabRequestId', $iCabRequestId, '', 'true');
            $vLangCode = get_value('register_driver', 'vLang', 'iDriverId', $iMemberId, '', 'true');
        }
        $vCurrency = $dataUser[0]['vCurrency'];
        //$vCurrencyDriver = get_value('register_driver', 'vCurrencyDriver', 'iDriverId', $iMemberId, '', 'true');
        if ($vCurrency == "" || $vCurrency == NULL) {
            $vCurrency = get_value('currency', 'vName', 'eDefault', 'Yes', '', 'true');
        }
        $UserCurrencyData = get_value('currency', 'vSymbol, Ratio', 'vName', $vCurrency);
        if ($vLangCode == "" || $vLangCode == NULL) {
            $vLangCode = get_value('language_master', 'vCode', 'eDefault', 'Yes', '', 'true');
        }
        $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLangCode, "1", $iServiceId);
        $LBL_RECEIPENT_TXT = $languageLabelsArr['LBL_RECEIPENT_TXT'];
        $LBL_SENDER = $languageLabelsArr['LBL_SENDER'];
        $LBL_EACH_RECIPIENT = $languageLabelsArr['LBL_EACH_RECIPIENT'];
        $sql = "SELECT tdl.iTripDeliveryLocationId,tdl.vReceiverLatitude,tdl.vReceiverLongitude,tdl.vReceiverAddress,tdl.tSaddress,tdl.tDaddress,tdl.ePaymentBy,tdl.ePaymentByReceiver,tdl.tStartLat,tdl.tStartLong,tdl.tEndLat,tdl.tEndLong FROM trips_delivery_locations as tdl WHERE tdl.`iCabBookingId`='" . $iCabRequestId . "' AND eRequestType = 'Ridenow' order by tdl.iTripDeliveryLocationId ASC";
        $Data_tripLocations = $obj->MySQLSelect($sql);
        // echo "<pre>";print_r($Data_tripLocations);exit;
        $sql = "select * from trip_delivery_fields where iCabRequestId='$iCabRequestId' order by iDeliveryFieldId";
        $db_trip_fields = $obj->MySQLSelect($sql);
        if (count($db_trip_fields) > 0) {
            for ($j = 0; $j < count($Data_tripLocations); $j++) {
                $k = 0;
                for ($i = 0; $i < count($db_trip_fields); $i++) {
                    if ($Data_tripLocations[$j]['iTripDeliveryLocationId'] == $db_trip_fields[$i]['iTripDeliveryLocationId']) {
                        if ($db_trip_fields[$i]['vValue'] != "") {
                            $sql = "select vFieldName_$vLangCode as vFieldName,eInputType from delivery_fields where iDeliveryFieldId='" . $db_trip_fields[$i]['iDeliveryFieldId'] . "'";
                            $db_fields_data = $obj->MySQLSelect($sql);
                            $Data_Field[$j][$k]['vFieldName'] = $db_fields_data[0]['vFieldName'];
                            $Data_Field[$j][$k]['vValue'] = $db_trip_fields[$i]['vValue'];
                            if ($db_trip_fields[$i]['iDeliveryFieldId'] == "3") {
                                //$Data_Field[$j][$k]['vValue'] = "+" . $dataUser[0]['vCode'] . $db_trip_fields[$i]['vValue'];
                                $Data_Field[$j][$k]['vValue'] = "+" . $db_trip_fields[$i]['vValue']; // Removed Country Code By HJ On 01-01-2019 As Per Discuss WIth DT As Per Above Line
                                $mobileLength1 = strlen($db_trip_fields[$i]['vValue']) - 2;
                                $maskMobileNo1 = substr($db_trip_fields[$i]['vValue'], 0, 2);
                                $Data_Field[$j][$k]['vMaskValue'] = "+" . $maskMobileNo1 . str_repeat("X", $mobileLength1);
                            }
                            $Data_Field[$j][$k]['iDeliveryFieldId'] = $db_trip_fields[$i]['iDeliveryFieldId'];
                            if ($Data_tripLocations[0]['ePaymentBy'] == "Receiver" && $Data_tripLocations[$j]['ePaymentByReceiver'] == "Yes") {
                                if ($db_trip_fields[$i]['iDeliveryFieldId'] == "2") {
                                    $PaymentPerson = $db_trip_fields[$i]['vValue'];
                                    $ReceNo = $j + 1;
                                }
                            }
                            if ($db_fields_data[0]['eInputType'] == "Select") {
                                $sql = "SELECT vName_$vLangCode as vName FROM `package_type` where iDeliveryFieldId='" . $db_trip_fields[$i]['iDeliveryFieldId'] . "' and iPackageTypeId='" . $db_trip_fields[$i]['vValue'] . "'";
                                $db_data = $obj->MySQLSelect($sql);
                                $Data_Field[$j][$k]['vValue'] = $db_data[0]['vName'];
                            }
                            $k++;
                        }
                    }
                }
                $Data_Field[$j][$k]['vFieldName'] = "Address";
                $Data_Field[$j][$k]['tSaddress'] = $Data_tripLocations[$j]['tSaddress'];
                $Data_Field[$j][$k]['tStartLat'] = $Data_tripLocations[$j]['tStartLat'];
                $Data_Field[$j][$k]['tStartLong'] = $Data_tripLocations[$j]['tStartLong'];
                $Data_Field[$j][$k]['tDaddress'] = $Data_tripLocations[$j]['tDaddress'];
                $Data_Field[$j][$k]['tEndLat'] = $Data_tripLocations[$j]['tEndLat'];
                $Data_Field[$j][$k]['tEndLong'] = $Data_tripLocations[$j]['tEndLong'];
                $Data_Field[$j][$k]['ePaymentByReceiver'] = $Data_tripLocations[$j]['ePaymentByReceiver'];
                $Data_Field[$j][$k]['iTripDeliveryLocationId'] = $Data_tripLocations[$j]['iTripDeliveryLocationId'];
                $Data_Field[$j][$k]['iActive'] = $Data_tripLocations[$j]['iActive'];
            }
        }
        // $Data_Field[$i]['ePaymentBy'] = $Data_tripLocations[0]['ePaymentBy'];
        $returnArr['ePaymentBy'] = $Data_tripLocations[0]['ePaymentBy'];
        $returnArr['PaymentPerson'] = $LBL_RECEIPENT_TXT . $ReceNo . " " . $PaymentPerson;
        // echo "<pre>";print_r($returnArr);exit;
        $Data['Deliveries'] = $Data_Field;
        $Data['MemberDetails'] = $dataUser[0];
        if ($returnArr['ePaymentBy'] == 'Sender') {
            $iUserId = get_value('cab_request_now', 'iUserId', 'iCabRequestId', $iCabRequestId, '', 'true');
            $Name = get_value('register_user', 'concat(vName," ",vLastName)', 'iUserId', $iUserId, '', 'true');
            $returnArr['PaymentPerson'] = $LBL_SENDER . " (" . $Name . " )";
        }
        else if ($returnArr['ePaymentBy'] == 'Individual') {
            $returnArr['PaymentPerson'] = $LBL_EACH_RECIPIENT;
        }
        // $vCurrencyPassenger=get_value('register_user', 'vCurrencyPassenger', 'iUserId', $dataUser[0]['iUserId'],'','true');
        // $UserCurrencyData = get_value('currency', 'vSymbol, Ratio', 'vName', $vCurrencyPassenger);
        $fTripGenerateFare = $dataUser[0]['iFare'];
        $fTripGenerateFare = round(($fTripGenerateFare * $UserCurrencyData[0]['Ratio']), 2);
        // $returnArr['DriverPaymentAmount'] = $UserCurrencyData[0]['vSymbol'] . " " . formatnum($fTripGenerateFare);
        $returnArr['DriverPaymentAmount'] = formateNumAsPerCurrency($fTripGenerateFare, $vCurrency);
        // echo "<pre>";print_r($Data);exit;
        if (count($Data) > 0) {
            $returnArr['Action'] = "1";
            $returnArr['DELIVERY_VERIFICATION_METHOD'] = $DELIVERY_VERIFICATION_METHOD;
            $returnArr['message'] = $Data;
            $iVehicleTypeId = get_value('cab_request_now', 'iVehicleTypeId', 'iCabRequestId', $iCabRequestId, '', 'true');
            $vVehicleType = get_value('vehicle_type', "vVehicleType_" . $vLangCode, 'iVehicleTypeId', $iVehicleTypeId, '', 'true');
            $returnArr['carTypeName'] = $vVehicleType;
        }
        else {
            $returnArr['Action'] = "0";
            $returnArr['message'] = "LBL_NO_DRIVER_FOUND";
        }
    }
    else {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "LBL_NO_TRIP_FOUND";
    }
    // echo "<pre>";print_r($returnArr);exit;
    setDataResponse($returnArr);
}
###########################################################################
#####################################################################
if ($type == "ConfirmDelivery") {
    $iTripId = isset($_REQUEST['iTripId']) ? clean($_REQUEST['iTripId']) : '';
    $UserType = isset($_REQUEST['UserType']) ? clean($_REQUEST['UserType']) : ''; //Passenger OR Driver
    $CheckFor = isset($_REQUEST['CheckFor']) ? clean($_REQUEST['CheckFor']) : ''; //Sender OR Receipent
    $vTripDeliveryConfirmCode_Receipent = isset($_REQUEST['vDeliveryConfirmCode']) ? clean($_REQUEST['vDeliveryConfirmCode']) : '';
    $vTripDeliveryConfirmCode_Receipent = trim($vTripDeliveryConfirmCode_Receipent);
    $image_name = $vImage = isset($_FILES['vImage']['name']) ? $_FILES['vImage']['name'] : '';
    $image_object = isset($_FILES['vImage']['tmp_name']) ? $_FILES['vImage']['tmp_name'] : '';
    $image_name = time() . ".jpg";
    $sql = "SELECT ePaymentByReceiver,vTripPaymentMode,iUserId,iDriverId,iFare,vRideNo,fWalletDebit,fTripGenerateFare,fDiscount,fCommision,fTollPrice,eHailTrip,ePaymentCollect,ePaymentCollect_Delivery,fHotelCommision,fOutStandingAmount,eWalletDebitAllow, vSignImage, tUserWalletBalance,fTax1,fTax2,eType,iPaymentInfoId FROM trips WHERE iTripId='" . $iTripId . "'";
    $tripData = $obj->MySQLSelect($sql);
    $iDriverId = $tripData[0]['iDriverId'];
    $iUserId = $tripData[0]['iUserId'];
    $vTripPaymentMode = $tripData[0]['vTripPaymentMode'];
    $ePaymentByReceiver = $tripData[0]['ePaymentByReceiver'];
    $ePaymentCollect = $tripData[0]['ePaymentCollect'];
    $ePaymentCollect_Delivery = $tripData[0]['ePaymentCollect_Delivery'];
    $eWalletDebitAllow = $tripData[0]['eWalletDebitAllow'];
    $current_vSignImage = $tripData[0]['vSignImage'];
    $totalTax = $tripData[0]['fTax1'] + $tripData[0]['fTax2'];
    $walletamountofcreditcard = $tripData[0]['fTripGenerateFare'];
    $iFare = $tripData[0]['iFare'];
    $vRideNo = $tripData[0]['vRideNo'];
    $tUserWalletBalance = $tripData[0]['tUserWalletBalance'];
    if (empty($tUserWalletBalance)) {
        $tUserWalletBalance = 0;
    }
    if ($CheckFor == "Sender") {
        if ($image_name != "") {
            $Photo_Gallery_folder = $tconfig['tsite_upload_trip_signature_images_path'];
            if (!is_dir($Photo_Gallery_folder)) {
                mkdir($Photo_Gallery_folder, 0777);
            }
            if (!empty($current_vSignImage) && file_exists($Photo_Gallery_folder . $current_vSignImage)) {
                @unlink($Photo_Gallery_folder . $current_vSignImage);
            }
            $vFile = $UPLOAD_OBJ->GeneralFileUpload($Photo_Gallery_folder, $image_object, $image_name, $prefix = '', $vaildExt = "pdf,doc,docx,jpg,jpeg,gif,png");
            $vImageName = $vFile[0];
            $Data_update_trips['vSignImage'] = $vImageName;
            $Data_update_trips['eSignVerification'] = "Yes";
            $where = " iTripId = '$iTripId'";
            $Data_update_trips_id = $obj->MySQLQueryPerform("trips", $Data_update_trips, 'update', $where);
            //$trip_delivery_loc = $obj->MySQLSelect("SELECT iTripDeliveryLocationId FROM trips_delivery_locations WHERE iTripId = '$iTripId'");
            $trip_delivery_loc = $obj->MySQLSelect("SELECT iTripDeliveryLocationId FROM trips_delivery_locations WHERE iActive = 'Finished' AND iTripId = '$iTripId'");
            if (!empty($trip_delivery_loc) && count($trip_delivery_loc) == 1) {
                // added to solve issue receipent sign same as sender  
                $Data_update_trips['vSignImage'] = "";
                $obj->MySQLQueryPerform("trips_delivery_locations", $Data_update_trips, 'update', $where);
            }
            $walletOption = 0;
            
            if (strtoupper($vTripPaymentMode) == "WALLET") {
                $walletOption = 1;
            }
            ## Charge For Card Payment of Trip ##
            if ($vTripPaymentMode == "Card" && $ePaymentByReceiver == "No" && $ePaymentCollect == "No" && $ePaymentCollect_Delivery == "No" && $walletOption == 0) {
                if (isset($userDetailsArr['register_user_' . $iUserId])) {
                    $userData = $userDetailsArr['register_user_' . $iUserId];
                }
                else {
                    $userData = $obj->MySQLSelect("SELECT *,iUserId as iMemberId FROM register_user WHERE iUserId='" . $iUserId . "'");
                    $userDetailsArr['register_user_' . $iUserId] = $userData;
                }
                //$vStripeCusId = get_value('register_user', 'vStripeCusId', 'iUserId', $iUserId, '', 'true');
                //$vBrainTreeToken = get_value('register_user', 'vBrainTreeToken', 'iUserId', $iUserId, '', 'true');
                $vStripeCusId = $userData[0]['vStripeCusId'];
                $vBrainTreeToken = $userData[0]['vBrainTreeToken'];
                if (!empty($vSystemDefaultCurrencyName)) {
                    $currency = $vSystemDefaultCurrencyName;
                }
                else {
                    $currency = get_value('currency', 'vName', 'eDefault', 'Yes', '', 'true');
                }
                $price_new = $iFare * 100;
                //$vLangCode = get_value('language_master', 'vCode', 'eDefault', 'Yes', '', 'true');
                //Added By HJ On 17-06-2020 For Optimize language_master Table Query Start
                $vLangCode = $LANG_OBJ->FetchDefaultLangData("vCode");
                //Added By HJ On 17-06-2020 For Optimize language_master Table Query End
                $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLangCode, "1", $iServiceId);
                $description = $languageLabelsArr['LBL_TRIP_PAYMENT_RECEIVED'] . " " . $vRideNo;
                $AMOUNT = $iFare;
                $iMemberId = $iUserId;
                $UserType = "Passenger";
                $iPaymentInfoId = $tripData[0]['iPaymentInfoId'];
                $paymentData = array("amount" => $AMOUNT, "description" => $description, "iMemberId" => $iMemberId, "UserType" => $UserType, "iPaymentInfoId" => $iPaymentInfoId);
                // echo "<pre>"; print_r($paymentData); exit;
                $result = (PaymentGateways::getInstance())->execute($paymentData);
                //echo "<pre>"; print_r($result); exit;
                if ($result['Action'] == "1") {
                    $payment_id = $result['payment_id'];
                    $where_payments = " iPaymentId = '" . $payment_id . "'";
                    $data_payments['iTripId'] = $iTripId;
                    $data_payments['eEvent'] = "Trip";
                    $obj->MySQLQueryPerform("payments", $data_payments, 'update', $where_payments);
                    $where = " iTripId = '$iTripId'";
                    //$data['ePaymentCollect']="Yes";
                    $data['ePaymentCollect_Delivery'] = "Yes";
                    $trip_payment_id = $obj->MySQLQueryPerform("trips", $data, 'update', $where);
                    $fWalletDebit = $tripData[0]['fWalletDebit'];
                    if ($fWalletDebit > 0) {
                        $data_wallet['iUserId'] = $iUserId;
                        $data_wallet['eUserType'] = "Rider";
                        $data_wallet['iBalance'] = $fWalletDebit;
                        $data_wallet['eType'] = "Debit";
                        $data_wallet['dDate'] = date("Y-m-d H:i:s");
                        $data_wallet['iTripId'] = $iTripId;
                        $data_wallet['eFor'] = "Booking";
                        $data_wallet['ePaymentStatus'] = "Unsettelled";
                        $data_wallet['tDescription'] = "#LBL_DEBITED_BOOKING#" . $vRideNo;
                        $WALLET_OBJ->PerformWalletTransaction($data_wallet['iUserId'], $data_wallet['eUserType'], $data_wallet['iBalance'], $data_wallet['eType'], $data_wallet['iTripId'], $data_wallet['eFor'], $data_wallet['tDescription'], $data_wallet['ePaymentStatus'], $data_wallet['dDate']);
                    }
                    $where = " iTripId = '$iTripId'";
                    //$data['ePaymentCollect']="Yes";
                    $data['ePaymentCollect_Delivery'] = "Yes";
                    $trip_payment_id = $obj->MySQLQueryPerform("trips", $data, 'update', $where);
                    $returnArr['Action'] = "1";
                    //$returnArr['message'] = getDriverDetailInfo($iDriverId);
                    $returnArr['message'] = "LBL_COLLECT_CARD_DELIVERY_SUCCESS_TXT";
                    $returnArr['USER_DATA'] = getDriverDetailInfo($iDriverId);
                    setDataResponse($returnArr);
                }
                else {
                    $returnArr['Action'] = "0";
                    $returnArr['message'] = "LBL_CHARGE_COLLECT_FAILED";
                    setDataResponse($returnArr);
                }
            }
            else if ($vTripPaymentMode == "Cash" && $ePaymentByReceiver == "No" && $ePaymentCollect == "No" && $ePaymentCollect_Delivery == "No") {
                $fWalletDebit = $tripData[0]['fWalletDebit'];
                //added by SP bc multidelivery + cash + wallet trip started and then wallet balance is debited then also this trip deduct that amt..
                //Commented By HJ On 17-09-2019 For Check User Wallet Amount Start
                /*$user_available_balance = $WALLET_OBJ->FetchMemberWalletBalance($iUserId, "Rider", true);
                //echo $user_available_balance."======".$fWalletDebit;exit;
                if (is_array($user_available_balance)) {
                    $walletDataArr = $user_available_balance;
                    $user_available_balance = $walletDataArr['CurrentBalance'];
                }
                if ($fWalletDebit > $user_available_balance) {
                    $fWalletDebit = $user_available_balance;
                }*/
                //Commented By HJ On 17-09-2019 For Check User Wallet Amount End
                //Added By HJ On 17-09-2019 For Check User Wallet Amount Start
                $user_available_balance = $WALLET_OBJ->FetchMemberWalletBalance($iUserId, "Rider");
                if ($user_available_balance > 0) {
                    $totalCurrentActiveTripsArr = FetchTotalOngoingTrips($iUserId);
                    $totalCurrentActiveTripsIdsArr = $totalCurrentActiveTripsArr['ActiveTripIds'];
                    $totalCurrentActiveOrderIdsArr = $totalCurrentActiveTripsArr['ActiveOrderIds'];
                    $totalCurrentActiveTripsCount = $totalCurrentActiveTripsArr['TotalCount'];
                    /*   By HJ On 17-09-2020 Start * ******** Replace current wallet balance of user when System payment flow is Method-2/Method-3 ***** */
                    // Charge an amount that is autorized when trip was initially requested in case when multiple jobs are going on.
                    
                    if (($totalCurrentActiveTripsCount > 1 || in_array($iTripId, $totalCurrentActiveTripsIdsArr) == false) && strtoupper($vTripPaymentMode) == "WALLET") {
                        $fWalletDebit = $tUserWalletBalance;
                    }
                    /*  By HJ On 17-09-2020 END  * ******** Replace current wallet balance of user when System payment flow is Method-2/Method-3 ***** */
                }
                else {
                    $fWalletDebit = $user_available_balance;
                }
                //Added By HJ On 17-09-2019 For Check User Wallet Amount End
                if ($fWalletDebit > 0) {
                    $data_wallet['iUserId'] = $iUserId;
                    $data_wallet['eUserType'] = "Rider";
                    $data_wallet['iBalance'] = $fWalletDebit;
                    $data_wallet['eType'] = "Debit";
                    $data_wallet['dDate'] = date("Y-m-d H:i:s");
                    $data_wallet['iTripId'] = $iTripId;
                    $data_wallet['eFor'] = "Booking";
                    $data_wallet['ePaymentStatus'] = "Unsettelled";
                    $data_wallet['tDescription'] = "#LBL_DEBITED_BOOKING#" . $vRideNo;
                    $WALLET_OBJ->PerformWalletTransaction($data_wallet['iUserId'], $data_wallet['eUserType'], $data_wallet['iBalance'], $data_wallet['eType'], $data_wallet['iTripId'], $data_wallet['eFor'], $data_wallet['tDescription'], $data_wallet['ePaymentStatus'], $data_wallet['dDate']);
                }
                $fOutStandingAmount = $tripData[0]['fOutStandingAmount'];
                $fDiscount = $tripData[0]['fDiscount'];
                $discountValue = $fWalletDebit + $fDiscount;
                $enableCommisionDeduct = $MODULES_OBJ->autoDeductDriverCommision("Ride"); // Added By HJ On 16-10-2020 For get Auto Deduct Driver Commision Configuration As Per eSystem
                if ($enableCommisionDeduct == "Yes") {
                    //$iDriverId = get_value('trips', 'iDriverId', 'iTripId', $iTripId, '', 'true');
                    // $vLang =get_value('register_driver', 'vLang', 'iDriverId',$iDriverId,'','true');
                    $vRideNo = $tripData[0]['vRideNo'];
                    //$iBalance = $tripData[0]['fCommision'];
                    $iBalance = $tripData[0]['fCommision'] + $tripData[0]['fOutStandingAmount'] + $tripData[0]['fHotelCommision'] + $totalTax;
                    $eFor = "Withdrawl";
                    $eType = "Debit";
                    //$tDescription = 'Debited for booking#'.$vRideNo;
                    $tDescription = '#LBL_DEBITED_SITE_EARNING_BOOKING# ' . $vRideNo;
                    $ePaymentStatus = 'Settelled';
                    $dDate = Date('Y-m-d H:i:s');
                    $totalUserFare = $iFare;
                    // Added By HJ On 18-12-2019 For Prevent Duplication Issue Dicuss with KS Sir Start
                    $getPaymentStatus = $obj->MySQLSelect("SELECT eUserType,ePaymentStatus,iUserWalletId,eType FROM user_wallet WHERE iTripId='" . $iTripId . "'");
                    $walletArr = array();
                    for ($h = 0; $h < count($getPaymentStatus); $h++) {
                        $walletArr[$getPaymentStatus[$h]['eType']][$getPaymentStatus[$h]['eUserType']] = $getPaymentStatus[$h]['eType'];
                    }
                    // Added By HJ On 18-12-2019 For Prevent Duplication Issue Dicuss with KS Sir End
                    if ($discountValue > 0) {
                        $eFor_credit = "Deposit";
                        $eType_credit = "Credit";
                        $tDescription_credit = '#LBL_CREDITED_BOOKING# ' . $vRideNo;
                        //$tDescription_credit = 'Credited for booking#'.$vRideNo;
                        if (!isset($walletArr[$eType_credit]['Driver'])) {
                            $WALLET_OBJ->PerformWalletTransaction($iDriverId, "Driver", $discountValue, $eType_credit, $iTripId, $eFor_credit, $tDescription_credit, $ePaymentStatus, $dDate);
                        }
                        $totalUserFare += $discountValue;
                    }
                    $Where = " iTripId = '$iTripId'";
                    $Data_update_driver_paymentstatus = array();
                    if ($totalUserFare >= $iBalance) {
                        $Data_update_driver_paymentstatus['eDriverPaymentStatus'] = "Settelled";
                        if ($walletamountofcreditcard > $totalUserFare) {
                            $Data_update_driver_paymentstatus['eDriverPaymentStatus'] = "Unsettelled";
                        }
                        $Update_Payment_Id = $obj->MySQLQueryPerform("trips", $Data_update_driver_paymentstatus, 'update', $Where); // Added By HJ On 08-08-2019 As Per Discuss With KS and BM mam
                        //print_r($iBalance);die;
                        if (!isset($walletArr[$eType]['Driver'])) {
                            $WALLET_OBJ->PerformWalletTransaction($iDriverId, "Driver", $iBalance, $eType, $iTripId, $eFor, $tDescription, $ePaymentStatus, $dDate);
                        }
                    }
                    //$WALLET_OBJ->PerformWalletTransaction($iDriverId, "Driver", $iBalance, $eType, $iTripId, $eFor, $tDescription, $ePaymentStatus, $dDate);
                    //$data['eDriverPaymentStatus'] = "Settelled";
                }
                $data = array();
                $data['ePaymentCollect_Delivery'] = "Yes";
                $data['fWalletDebit'] = $fWalletDebit;  //added by SP bc multidelivery + cash + wallet trip started and then wallet balance is debited then also this trip deduct that amt.
                $trip_payment_id = $obj->MySQLQueryPerform("trips", $data, 'update', $where);
                $returnArr['Action'] = "1";
                $returnArr['USER_DATA'] = getDriverDetailInfo($iDriverId);
                //$returnArr['message'] = getDriverDetailInfo($iDriverId);
                setDataResponse($returnArr);
            }
            else if ($walletOption == 1) {
                $eFor_credit = "Booking";
                $eType_credit = "Debit";
                $vRideNo = $tripData[0]['vRideNo'];
                //$fWalletDebit = $tripData[0]['iFare']; // Commented By HJ On 17-09-2020 B'Coz In System Payment Flow and Pay Metod Wallet
                $fWalletDebit = $tripData[0]['fWalletDebit']; // Added By HJ On 17-09-2020 B'Coz In System Payment Flow and Pay Metod Wallet
                $tDescription_credit = "#LBL_DEBITED_BOOKING#" . $vRideNo;
                $ePaymentStatus = 'Settelled';
                $dDate = date("Y-m-d H:i:s");
                //Commented By HJ On 17-09-2019 For Check User Wallet Amount Start
                /*$user_available_balance = $WALLET_OBJ->FetchMemberWalletBalance($iUserId, "Rider", true);
                if (is_array($user_available_balance)) {
                    $walletDataArr = $user_available_balance;
                    $user_available_balance = $walletDataArr['CurrentBalance'];
                }
                if ($fWalletDebit > $user_available_balance) {
                    $fWalletDebit = $user_available_balance;
                }*/
                //Commented By HJ On 17-09-2019 For Check User Wallet Amount End
                //Added By HJ On 17-09-2019 For Check User Wallet Amount Start
                $user_available_balance = $WALLET_OBJ->FetchMemberWalletBalance($iUserId, "Rider");
                if ($user_available_balance > 0) {
                    $totalCurrentActiveTripsArr = FetchTotalOngoingTrips($iUserId);
                    $totalCurrentActiveTripsIdsArr = $totalCurrentActiveTripsArr['ActiveTripIds'];
                    $totalCurrentActiveOrderIdsArr = $totalCurrentActiveTripsArr['ActiveOrderIds'];
                    $totalCurrentActiveTripsCount = $totalCurrentActiveTripsArr['TotalCount'];
                    /*   By HJ On 17-09-2020 Start * ******** Replace current wallet balance of user when System payment flow is Method-2/Method-3 ***** */
                    // Charge an amount that is autorized when trip was initially requested in case when multiple jobs are going on.
                    
                    if (($totalCurrentActiveTripsCount > 1 || in_array($iTripId, $totalCurrentActiveTripsIdsArr) == false) && strtoupper($vTripPaymentMode) == "WALLET") {
                        $fWalletDebit = $tUserWalletBalance;
                    }
                    /*  By HJ On 17-09-2020 END  * ******** Replace current wallet balance of user when System payment flow is Method-2/Method-3 ***** */
                }
                else {
                    $fWalletDebit = $user_available_balance;
                }
                //Added By HJ On 17-09-2019 For Check User Wallet Amount End
                if ($fWalletDebit > 0) {
                    $WALLET_OBJ->PerformWalletTransaction($iUserId, "Rider", $fWalletDebit, $eType_credit, $iTripId, $eFor_credit, $tDescription_credit, $ePaymentStatus, $dDate);
                }
                $where = " iTripId = '$iTripId'";
                $data = array();
                $data['eDriverPaymentStatus'] = "Settelled";
                $data['ePaymentCollect_Delivery'] = "Yes";
                $trip_payment_id = $obj->MySQLQueryPerform("trips", $data, 'update', $where);
                $returnArr['Action'] = "1";
                //$returnArr['message'] = getDriverDetailInfo($iDriverId);
                $returnArr['message'] = "LBL_COLLECT_CARD_DELIVERY_SUCCESS_TXT";
                $returnArr['USER_DATA'] = getDriverDetailInfo($iDriverId);
                setDataResponse($returnArr);
            }
            else {
                $returnArr['Action'] = "1";
                // $returnArr['message'] = getDriverDetailInfo($iDriverId);
                $returnArr['USER_DATA'] = getDriverDetailInfo($iDriverId);
                setDataResponse($returnArr);
            }
            ## Charge For Card Payment of Trip ##
        }
        else {
            $returnArr['Action'] = "0";
            $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
            setDataResponse($returnArr);
        }
    }
    else {
        // $DELIVERY_VERIFICATION_METHOD = $CONFIG_OBJ->getConfigurations("configurations", "DELIVERY_VERIFICATION_METHOD");
        $sqldelivdata = "SELECT iTripDeliveryLocationId,vDeliveryConfirmCode FROM `trips_delivery_locations` WHERE ( iActive='Canceled' OR iActive='Finished')  AND eSignVerification = 'No' AND iTripId='" . $iTripId . "' ORDER BY iTripDeliveryLocationId DESC LIMIT 0,1";
        $TripDeliData = $obj->MySQLSelect($sqldelivdata);
        //echo "<pre>";print_r($TripDeliData);die;
        $iTripDeliveryLocationId = $TripDeliData[0]['iTripDeliveryLocationId'];
        if ($DELIVERY_VERIFICATION_METHOD == "Code") {
            $vDeliveryConfirmCode = trim($TripDeliData[0]['vDeliveryConfirmCode']);
            //echo $iTripId."===".$vTripDeliveryConfirmCode_Receipent."====".$vDeliveryConfirmCode;die;
            if ($vTripDeliveryConfirmCode_Receipent == $vDeliveryConfirmCode) {
                $Data_update_trip_delivery['eSignVerification'] = "Yes";
                $where = " iTripDeliveryLocationId = '$iTripDeliveryLocationId'";
                $Data_update_trip_delivery_id = $obj->MySQLQueryPerform("trips_delivery_locations", $Data_update_trip_delivery, 'update', $where);
                sendnotificationfordeliveryend($iUserId, $iDriverId, $iTripId, $iServiceId, $tripData[0]['eType']);
                $returnArr['Action'] = "1";
                // $returnArr['message'] = getDriverDetailInfo($iDriverId);
                $returnArr['USER_DATA'] = getDriverDetailInfo($iDriverId);
                //sendnotificationfordeliveryend($iUserId,$iDriverId,$iTripId,$iServiceId,$tripData[0]['eType']);
            }
            else {
                $returnArr['Action'] = "0";
                $returnArr['message'] = "LBL_DELIVERY_CODE_MISMATCH_RECEIPENT";
            }
            setDataResponse($returnArr);
        }
        else if ($DELIVERY_VERIFICATION_METHOD == "Signature") {
            if ($image_name != "") {
                //$Photo_Gallery_folder = $tconfig['tsite_upload_trip_images_path']."/".$TripID."/";
                $Photo_Gallery_folder = $tconfig['tsite_upload_trip_signature_images_path'];
                if (!is_dir($Photo_Gallery_folder)) mkdir($Photo_Gallery_folder, 0777);
                $vFile = $UPLOAD_OBJ->GeneralFileUpload($Photo_Gallery_folder, $image_object, $image_name, $prefix = '', $vaildExt = "pdf,doc,docx,jpg,jpeg,gif,png");
                $vImageName = $vFile[0];
                $Data_update_trip_delivery['vSignImage'] = $vImageName;
                $Data_update_trip_delivery['eSignVerification'] = "Yes";
                $where = " iTripDeliveryLocationId = '$iTripDeliveryLocationId'";
                $Data_update_trip_delivery_id = $obj->MySQLQueryPerform("trips_delivery_locations", $Data_update_trip_delivery, 'update', $where);
                sendnotificationfordeliveryend($iUserId, $iDriverId, $iTripId, $iServiceId, $tripData[0]['eType']);
                $returnArr['Action'] = "1";
                // $returnArr['message'] = getDriverDetailInfo($iDriverId);
                $returnArr['USER_DATA'] = getDriverDetailInfo($iDriverId);
                //sendnotificationfordeliveryend($iUserId,$iDriverId,$iTripId,$iServiceId,$tripData[0]['eType']);
            }
            else {
                $returnArr['Action'] = "0";
                $returnArr['message'] = "LBL_TRY_AGAIN_LATER_TXT";
            }
            setDataResponse($returnArr);
        }
        $returnArr['Action'] = "1";
        // $returnArr['message'] = getDriverDetailInfo($iDriverId);
        $returnArr['USER_DATA'] = getDriverDetailInfo($iDriverId);
        setDataResponse($returnArr);
    }
}
############################################################################
function sendnotificationfordeliveryend($iUserId, $iDriverId, $iTripId, $iServiceId, $eType)
{
    global $obj, $userDetailsArr, $vSystemDefaultLangCode, $languageLabelDataArr, $EVENT_MSG_OBJ, $LANG_OBJ, $MODULES_OBJ;
    $returnArr = array();
    /******************* ADDED BY SP NOTIFICATION SENT THAT DELIVERY IS COMPLETED START AT 08-08-2020 ***************************/
    $tripId = $iTripId;
    $driverId = $iDriverId;
    $userId = $iUserId;
    //Added By HJ On 22-06-2020 For Optimize register_driver Table Query Start
    if (isset($userDetailsArr['register_driver_' . $driverId])) {
        $vCurrencyDriver = $userDetailsArr['register_driver_' . $driverId][0]['vCurrencyDriver'];
    }
    else {
        $vCurrencyDriver = get_value('register_driver', 'vCurrencyDriver', 'iDriverId', $driverId, '', 'true');
    }
    //Added By HJ On 22-06-2020 For Optimize trips Table Query Start
    if (isset($tripDetailsArr['trips_' . $tripId])) {
        $UberXTripData = $tripDetailsArr['trips_' . $tripId];
    }
    else {
        $UberXTripData = $obj->MySQLSelect("SELECT *,fRatio_" . $vCurrencyDriver . " as fRatioDriver FROM trips WHERE iTripId='" . $tripId . "'");
        $tripDetailsArr['trips_' . $tripId] = $UberXTripData;
    }
    //Added By HJ On 22-06-2020 For Optimize trips Table Query End
    //$UberXTripData = get_value('trips', 'eServiceEnd,eType,tEndDate,vCancelReason,vCancelComment,eCancelled,eCancelledBy,tEndLat,tEndLong,tDaddress,vAfterImage,eFareType,iCancelReasonId,ePoolRide,iPersonSize,iVehicleTypeId,fPoolDuration,fPoolDistance,eServiceLocation,tVehicleTypeData,tStartLat,tStartLong', 'iTripId', $tripId);
    $eType = $UberXTripData[0]['eType'];
    $eServiceEnd = $UberXTripData[0]['eServiceEnd'];
    $UberXtripEndDate = $UberXTripData[0]['tEndDate'];
    $ePoolRide = $UberXTripData[0]['ePoolRide'];
    $iPersonSize = $UberXTripData[0]['iPersonSize'];
    $iVehicleTypeId = $UberXTripData[0]['iVehicleTypeId'];
    //Added By HJ On 22-07-2019 For Get Pickup Airport Sircharge Value Start
    $tStartLat = $UberXTripData[0]['tStartLat'];
    $tStartLong = $UberXTripData[0]['tStartLong'];
    $eCancelled = $UberXTripData[0]['eCancelled'];
    $iCancelReasonId = $UberXTripData[0]['iCancelReasonId'];
    $isTripCanceled = "";
    if ($eServiceEnd == "Yes" && $eType == "UberX") {
        if ($eCancelled == "Yes") {
            $isTripCanceled = "true";
        }
    }
    if (isset($userDetailsArr['register_user_' . $iUserId])) {
        $getUserData = $userDetailsArr['register_user_' . $iUserId];
    }
    else {
        $getUserData = $obj->MySQLSelect("SELECT *,iUserId as iMemberId FROM register_user WHERE iUserId='" . $iUserId . "'");
        $userDetailsArr['register_user_' . $iUserId] = $getUserData;
    }
    //$vLangCode = get_value('register_user', 'vLang', 'iUserId', $iUserId, '', 'true');
    $tSessionId = $vLangCode = "";
    if (count($getUserData) > 0) {
        $vLangCode = $getUserData[0]['vLang'];
        $tSessionId = $getUserData[0]['tSessionId'];
    }
    if ($vLangCode == "" || $vLangCode == NULL) {
        //Added By HJ On 22-06-2020 For Optimize language_master Table Query Start
        $vLangCode = $LANG_OBJ->FetchDefaultLangData("vCode");
        //Added By HJ On 22-06-2020 For Optimize language_master Table Query End
    }
    $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLangCode, "1", $iServiceId);
    $tripcancelbydriver = $languageLabelsArr['LBL_TRIP_CANCEL_BY_DRIVER'];
    $tripfinish = $languageLabelsArr['LBL_DRIVER_END_NOTIMSG'];
    $tripfinish_ride = $languageLabelsArr['LBL_TRIP_FINISH'];
    $tripfinish_delivery = $languageLabelsArr['LBL_DELIVERY_FINISH'];
    $message_arr = array();
    $message_arr['ShowTripFare'] = "true";
    $message = "TripEnd";
    if ($isTripCanceled == "true") {
        $message = "TripCancelledByDriver";
    }
    if ($iCancelReasonId != "") {
        $driverReason = get_value('cancel_reason', "vTitle_" . $vLangCode, 'iCancelReasonId', $iCancelReasonId, '', 'true');
    }
    if (isset($userDetailsArr['register_driver_' . $driverId])) {
        $result22 = $userDetailsArr['register_driver_' . $driverId];
    }
    else {
        $result22 = $obj->MySQLSelect("SELECT *,iDriverId as iMemberId FROM register_driver iDriverId='" . $driverId . "'");
        $userDetailsArr['register_driver_' . $driverId] = $result22;
    }
    if (count($result22) > 0) {
        if (isset($tripDetailsArr['trips_' . $result22[0]['iTripId']])) {
            $tripData = $tripDetailsArr['trips_' . $result22[0]['iTripId']];
        }
        else {
            $tripData = $obj->MySQLSelect("SELECT * FROM trips iTripId='" . $result22[0]['iTripId'] . "'");
        }
        if (count($tripData) > 0) {
            $result22[0]['vRideNo'] = $tripData[0]['vRideNo'];
            $result22[0]['driverName'] = $result22[0]['vName'] . " " . $result22[0]['vLastName'];
        }
    }
    if ($isTripCanceled == "true") {
        // $alertMsg = $tripcancelbydriver;
        /*if ($eType == "UberX") {
            $usercanceltriplabel = $result22[0]['driverName'] . ':' . $result22[0]['vRideNo'] . '-' . $languageLabelsArr['LBL_PREFIX_JOB_CANCEL_DRIVER'] . ' ' . $driverReason;
        } else*/
        if ($eType == "Ride") {
            $usercanceltriplabel = $languageLabelsArr['LBL_PREFIX_TRIP_CANCEL_DRIVER'] . ' ' . $driverReason;
        }
        else {
            $usercanceltriplabel = $languageLabelsArr['LBL_PREFIX_DELIVERY_CANCEL_DRIVER'] . ' ' . $driverReason;
        }
        $alertMsg = $usercanceltriplabel;
    }
    else {
        $alertMsg = $tripfinish_delivery;
        /*if ($eType == "UberX") {
            //$alertMsg = $tripfinish;
            $alertMsg = $result22[0]['driverName'] . " " . $tripfinish . " " . $result22[0]['vRideNo'];
        } else*/
        if ($eType == "Ride") {
            $alertMsg = $tripfinish_ride;
        }
    }
    $message_arr['Message'] = $message;
    $message_arr['iTripId'] = $tripId;
    $message_arr['iDriverId'] = $driverId;
    $message_arr['driverName'] = $result22[0]['driverName'];
    $message_arr['tSessionId'] = $tSessionId;
    $message_arr['vRideNo'] = $result22[0]['vRideNo'];
    if ($isTripCanceled == "true") {
        $message_arr['Reason'] = $driverReason;
        $message_arr['isTripStarted'] = "true";
    }
    if ($eType == "Multi-Delivery") {
        $msgarr = gettexttripdeliverydetails($tripId, "Passenger", "Processendtrip");
        $message_arr['iTripDeliveryLocationId'] = $msgarr['iTripDeliveryLocationId'];
        $message_arr['vTitle'] = $msgarr['Delivery_End_Txt'];
        $message_arr['Is_Last_Delivery'] = $msgarr['Is_Last_Delivery'];
        $alertMsg = $msgarr['Delivery_End_Txt'];
    }
    else {
        $message_arr['iTripDeliveryLocationId'] = 0;
        $message_arr['vTitle'] = $alertMsg;
        $message_arr['Is_Last_Delivery'] = "Yes";
    }
    // $message_arr['vTitle'] = $alertMsg;
    $message_arr['eType'] = $eType;
    //added by SP on 17-02-2021 for custom notification
    $message_arr['CustomNotification'] = $MODULES_OBJ->isEnableCustomNotification() ? "Yes" : "No";
    //these two btn CustomViewBtn,CustomTrackDetails whether shown in app or not
    $message_arr['CustomViewBtn'] = "Yes";
    $message_arr['CustomTrackDetails'] = "No";
    $message_arr['LBL_VIEW_DETAILS'] = $languageLabelsArr['LBL_VIEW_DETAILS'];
    $message_arr['LBL_TRACK_ORDER'] = $languageLabelsArr['LBL_TRACK_ORDER'];
    $customNotiArray = GetCustomNotificationDetails($tripId, $message_arr['vTitle'], $vLangCode);
    //title and sub description shown in custom notification
    $message_arr['CustomTitle'] = $customNotiArray[0]['vCurrentStatus'];
    $message_arr['CustomSubTitle'] = $customNotiArray[0]['vCurrentStatus_Track'];
    $message_arr['CustomMessage'] = $customNotiArray;
    $message = json_encode($message_arr, JSON_UNESCAPED_UNICODE);
    #####################Add Status Message#########################
    $DataTripMessages['tMessage'] = $message;
    $DataTripMessages['iDriverId'] = $driverId;
    $DataTripMessages['iTripId'] = $tripId;
    $DataTripMessages['iUserId'] = $iUserId;
    $DataTripMessages['eFromUserType'] = "Driver";
    $DataTripMessages['eToUserType'] = "Passenger";
    $DataTripMessages['eReceived'] = "No";
    $DataTripMessages['dAddedDate'] = @date("Y-m-d H:i:s");
    $obj->MySQLQueryPerform("trip_status_messages", $DataTripMessages, 'insert');
    ################################################################
    //if($_SERVER['HTTP_HOST'] == "cubejekdev.bbcsproducts.net"){
    //$sql_request_data = "REPLACE INTO request_data_debug_notification (`tTitle`, `tType`, `tDescription`, `tPurpose`, `tCallToAction`, `tRequestParam`, `tResponse`, `tErrorResponse`) VALUES('', '" . $tripId . "', '','','','" . $message . "','','')";
    //$obj->sql_query($sql_request_data);
    //}
    $tableName = "register_user";
    $iMemberId_VALUE = $iUserId;
    $iMemberId_KEY = "iUserId";
    //Added By HJ On 22-06-2020 For Optimize register_user Table Query Start
    if (isset($userDetailsArr[$tableName . "_" . $iMemberId_VALUE])) {
        $AppData = $userDetailsArr[$tableName . "_" . $iMemberId_VALUE];
    }
    else {
        $AppData = get_value($tableName, '*,' . $iMemberId_KEY . ' as iMemberId', $iMemberId_KEY, $iMemberId_VALUE);
        $userDetailsArr[$tableName . "_" . $iMemberId_VALUE] = $AppData;
    }
    //Added By HJ On 22-06-2020 For Optimize register_user Table Query End
    $iAppVersion = $AppData[0]['iAppVersion'];
    $eDeviceType = $AppData[0]['eDeviceType'];
    $eLogout = $AppData[0]['eLogout'];
    $tLocationUpdateDate = $AppData[0]['tLocationUpdateDate'];
    $iGcmRegId = $AppData[0]['iGcmRegId'];
    $tSessionId = $AppData[0]['tSessionId'];
    $eAppTerminate = $AppData[0]['eAppTerminate'];
    $eDebugMode = $AppData[0]['eDebugMode'];
    $eHmsDevice = $AppData[0]['eHmsDevice'];
    /* For PubNub Setting Finished */
    $cmpMinutes = ceil((fetchtripstatustimeMAXinterval() + $intervalmins) / 60);
    $compare_date = @date('Y-m-d H:i:s', strtotime('-' . $cmpMinutes . ' minutes'));
    //$alertSendAllowed = false;
    $alertSendAllowed = true;
    //$message = $alertMsg;
    $tLocUpdateDate = date("Y-m-d H:i:s", strtotime($tLocationUpdateDate));
    if ($tLocUpdateDate < $compare_date) {
        $alertSendAllowed = true;
    }
    $channelName = "PASSENGER_" . $iUserId;
    $message_arr['tSessionId'] = $tSessionId;
    $message_arr['eSystem'] = "";
    $generalDataArr[] = array('eDeviceType' => $eDeviceType, 'deviceToken' => $iGcmRegId, 'alertMsg' => $alertMsg, 'eAppTerminate' => $eAppTerminate, 'eDebugMode' => $eDebugMode, 'eHmsDevice' => $eHmsDevice, 'message' => $message_arr, 'channelName' => $channelName, 'tripStatusMsgArr' => array('tMessage' => $message_arr, 'iDriverId' => $driverId, 'iTripId' => $tripId, 'iUserId' => $iUserId, 'eFromUserType' => "Driver", 'eToUserType' => "Passenger", 'eReceived' => "No"));
    $EVENT_MSG_OBJ->send(array('GENERAL_DATA' => $generalDataArr), RN_USER);
    /******************* ADDED BY SP NOTIFICATION SENT THAT DELIVERY IS COMPLETED END AT 08-08-2020 ***************************/
    return;
}

?>