<?php



include_once '../common.php';

$type = $_REQUEST['type'] ?? '';

$GeneralMemberId = $_REQUEST['GeneralMemberId'] ?? '';

$GeneralUserType = $_REQUEST['GeneralUserType'] ?? '';

$tSessionId = $_REQUEST['tSessionId'] ?? '';

$userType = $_REQUEST['userType'] ?? '';

$vCountry = $_REQUEST['vCountry'] ?? 'IN';

if ('RIDER' === strtoupper($userType)) {
    $table_name = 'register_user';

    $field = 'iUserId';

    $selectfield = 'vCurrencyPassenger';
} elseif ('COMPANY' === strtoupper($userType)) {
    $table_name = 'company';

    $field = 'iCompanyId';

    $selectfield = 'vCurrencyCompany';
}

$iUserId = $_SESSION['sess_iUserId'];

// this is for cubex only but not put in the condition bc this variable is used in qry and here condition is not put...

$getMemberData = $obj->MySQLSelect("SELECT `vLang`,`vTimeZone`,{$selectfield} FROM ".$table_name." WHERE {$field}='".$iUserId."'");

$vTimeZone = 'Asia/Kolkata';

$vLang = 'EN';

if (count($getMemberData) > 0) {
    $vLang = $getMemberData[0]['vLang'];

    $vTimeZone = $getMemberData[0]['vTimeZone'];

    $curr = $getMemberData[0][$selectfield];
}

date_default_timezone_set($vTimeZone);

if ('' === $vLang || null === $vLang) {
    $getMemberData = $obj->MySQLSelect("SELECT vCode FROM language_master WHERE eDefault='Yes'");

    $vLang = $getMemberData[0]['vLang'];
}

if (isset($_SESSION['sess_currency']) && !empty($_SESSION['sess_currency'])) {
    $curr = $_SESSION['sess_currency'];
}

if ('' === $curr || null === $curr) {
    $getMemberData = $obj->MySQLSelect("SELECT vName FROM currency WHERE eDefault='Yes'");

    $curr = $getMemberData[0]['vName'];
}

if ('' === $vTimeZone || null === $vTimeZone) {
    $getMemberData = $obj->MySQLSelect("SELECT vTimeZone FROM country WHERE vCountryCode='{$vCountry}'");

    $vTimeZone = $getMemberData[0]['vTimeZone'];
}

// if(empty($iUserId)){

if (isset($_SESSION['sess_lang']) && '' !== trim($_SESSION['sess_lang'])) {
    $vLang = $_SESSION['sess_lang'];
}

if ('loadAvailableCab' === $type) {
    $iVehicleTypeId = $_REQUEST['iVehicleTypeId'] ?? '';

    $type = $_REQUEST['type'] ?? '';

    $eType = $_REQUEST['eType'] ?? 'Ride';

    $sql = "SELECT iCountryId FROM country WHERE vCountryCode = '".$vCountry."'";

    $countryarray = $obj->MySQLSelect($sql);

    $countryid = $countryarray[0]['iCountryId'];

    // added by SP for get vehicles/services according to the pickup location on 02-08-2019 start

    $from_lat = $_REQUEST['from_lat'] ?? '';

    $from_long = $_REQUEST['from_long'] ?? '';

    $to_lat = $_REQUEST['to_lat'] ?? '';

    $to_long = $_REQUEST['to_long'] ?? '';

    $distance = $_REQUEST['distance'] ?? '1';

    $duration = $_REQUEST['duration'] ?? '1';

    $promoCode = $_REQUEST['promoCode'] ?? '';

    $iFromStationId = $_REQUEST['iFromStationId'] ?? '';

    $iToStationId = $_REQUEST['iToStationId'] ?? '';

    // $userType = isset($_REQUEST['userType']) ? $_REQUEST['userType'] : '';

    // added by sunita 11-01-2020

    $booking_date = $_REQUEST['booking_date'] ?? '';

    $iUserId = $_REQUEST['iUserId'] ?? '';

    $vSourceAddresss = $_REQUEST['vSourceAddresss'] ?? '';

    $iDriverId = $_REQUEST['iDriverId'] ?? '';

    // $vSourceAddresss = replace_content($vSourceAddresss);

    $vSourceAddresss = urlencode($vSourceAddresss);

    // $currentdate = replace_content(date("Y-m-d H:i:s"));

    $currentdate = urlencode(date('Y-m-d H:i:s'));

    $fareEstimate = 0; // for fare estimation

    if (empty($userType)) {
        $fareEstimate = 1;
    }

    $eShowOnlyMoto = $eFly = 'No';

    if ('Moto' === $eType) {
        $eType = 'Ride';

        $eShowOnlyMoto = 'Yes';
    }

    if ('Fly' === $eType) {
        $eType = 'Ride';

        $eFly = 'Yes';
    }

    if ($MODULES_OBJ->isEnableMultiDeliveryInBooking()) {
        if ('Deliver' === $eType) {
            $eShowOnlyMoto = 'Yes';
        }

        if ('Multi-Delivery' === $eType) {
            $eType = 'Deliver';

            $eShowOnlyMoto = 'No';
        }
    }

    $bookingFrom = 'Web'; // here it is passed bcoz pool vehicles not shown to the web site.

    if ('UberX' === $eType) {
        $parentId = '';

        if ($parent_ufx_catid > 0) {
            $parentId = $parent_ufx_catid;
        }

        $url = $tconfig['tsite_url'].WEBSERVICE_API_FILE_NAME."?vSelectedLatitude={$from_lat}&vSelectedLongitude={$from_long}&DEFAULT_SERVICE_CATEGORY_ID=&FOOD_ONLY=&vGeneralLang={$vLang}&ONLYDELIVERALL=&type=getDriverServiceCategories&GeneralUserType={$GeneralUserType}&tSessionId={$tSessionId}&GeneralMemberId={$GeneralMemberId}&UBERX_PARENT_CAT_ID=&SelectedCabType={$eType}&iMemberId={$iUserId}&CUS_IS_SINGLE_STORE_SELECTION=No&vGeneralCurrency={$curr}&parentId={$parentId}&iDriverId={$iDriverId}&vCurrentTime={$currentdate}&vUserDeviceCountry={$vCountry}&vSelectedAddress={$vSourceAddresss}&SelectedVehicleTypeId=&vTimeZone={$vTimeZone}&iServiceId=&DELIVERALL=&APP_TYPE=&bookingFrom={$bookingFrom}";
    } else {
        $url = $tconfig['tsite_url'].WEBSERVICE_API_FILE_NAME."?iVehicleTypeId={$iVehicleTypeId}&PassengerLon={$from_long}&DEFAULT_SERVICE_CATEGORY_ID=&FOOD_ONLY=&eType={$eType}&vGeneralLang={$vLang}&ONLYDELIVERALL=&type=loadAvailableCab&GeneralUserType={$GeneralUserType}&tSessionId={$tSessionId}&GeneralMemberId={$GeneralMemberId}&scheduleDate=&UBERX_PARENT_CAT_ID=&SelectedCabType=&PickUpAddress={$vSourceAddresss}&CUS_IS_SINGLE_STORE_SELECTION=No&vGeneralCurrency={$curr}&iUserId={$iUserId}&vCurrentTime={$currentdate}&vUserDeviceCountry={$vCountry}&PassengerLat={$from_lat}&vTimeZone={$vTimeZone}&iServiceId=&sortby=&DELIVERALL=&APP_TYPE=&eShowOnlyMoto={$eShowOnlyMoto}&eFly={$eFly}&iFromStationId={$iFromStationId}&iToStationId={$iToStationId}&bookingFrom={$bookingFrom}";
    }

    $ch = curl_init();

    $timeout = 0;

    curl_setopt($ch, CURLOPT_URL, $url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    // curl_setopt ($ch, CURLOPT_USERAGENT,"Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1)");

    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

    $rawdata = curl_exec($ch);

    // curl_getinfo($ch);

    curl_close($ch);

    // $rawdata = file_get_contents($url);

    $iMemberId = $iUserId;

    $userType1 = ucfirst($userType);

    if ('Admin' === $userType1) {
        $iMemberId = 0;
    }

    // added for admin country code on 11-01-2020

    if ($iMemberId <= 0 || '' === $iMemberId) {
        $countryCodeAdmin = $vCountry;
    }

    // added for Company country code on 11-01-2020

    if ('Company' === $userType1) {
        $countryCodeAdmin = $vCountry;
    }

    // for rounding

    if ('' === $userType1) {
        $iMemberId = 0;
    }

    if ('Rider' === $userType1 || 'Admin' === $userType1) {
        $userType1 = 'Passenger';
    }

    if ('UberX' !== $eType) {
        // for getting price of vehicles

        $url2 = $tconfig['tsite_url'].WEBSERVICE_API_FILE_NAME."?deviceWidth=1080.0&GeneralAppVersion=1.11&distance={$distance}&PromoCode={$promoCode}&DestLongitude={$to_long}&DEFAULT_SERVICE_CATEGORY_ID=&FOOD_ONLY=&ePaymentMode=cash&vGeneralLang={$vLang}&ONLYDELIVERALL=&type=estimateFareNew&EndLongitude={$from_long}&GeneralUserType={$GeneralUserType}&tSessionId={$tSessionId}&deviceHeight=1794.0&StartLatitude={$from_lat}&GeneralMemberId={$GeneralMemberId}&SelectedCar=&UBERX_PARENT_CAT_ID=&CUS_IS_SINGLE_STORE_SELECTION=No&vGeneralCurrency={$curr}&GeneralDeviceType=Android&iUserId={$iMemberId}&SelectedCarTypeID=&vCurrentTime={$currentdate}&vUserDeviceCountry={$vCountry}&GeneralAppVersionCode=12&vTimeZone={$vTimeZone}&iServiceId=&time={$duration}&DELIVERALL=&APP_TYPE=&DestLatitude={$to_lat}&eFly={$eFly}&iFromStationId={$iFromStationId}&iToStationId={$iToStationId}";

        $ch = curl_init();

        $timeout = 0;

        curl_setopt($ch, CURLOPT_URL, $url2);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);

        // curl_setopt ($ch, CURLOPT_USERAGENT,"Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1)");

        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

        $rawdataPrice = curl_exec($ch);

        curl_close($ch);

        // echo"<pre>";print_R($rawdataPrice); exit;
        $rawdataPrice = json_decode($rawdataPrice, true);

        $price_array = [];

        foreach ($rawdataPrice['message'] as $key => $value) {
            $price_array[$value['iVehicleTypeId']] = $value['total_fare'];
        }
    }

    $rawdata = json_decode($rawdata, true);

    if ('UberX' === $eType) {
        $returnarr .= '<div class="general-form"><div class="form-group"><select name="iVehicleTypeId" id="iVehicleTypeId" class="select2data" required onChange="showAsVehicleType(this.value)">';

        $returnarr .= '<option value="" >Select '.$langage_lbl['LBL_MYTRIP_TRIP_TYPE'].'</option>';

        $data = $rawdata['message'];

        $locationArr = [];

        $sql23 = 'SELECT lm.vLocationName,vt.iVehicleTypeId FROM `vehicle_type` AS vt left join location_master as lm ON lm.iLocationId = vt.iLocationid';

        $db_location = $obj->MySQLSelect($sql23);

        foreach ($db_location as $key => $value) {
            if (!empty($value['vLocationName'])) {
                $locationArr[$value['iVehicleTypeId']] = ' ('.$value['vLocationName'].')';
            } else {
                $locationArr[$value['iVehicleTypeId']] = ' ('.$langage_lbl['LBL_ALL_LOCATIONS'].')';
            }
        }

        $arraynew = [];

        foreach ($data as $k => $val) {
            if (!empty($val)) {
                $arraynew[$val['mainCatiParentId']]['Title'] = $val['vCategoryTitleMain'];

                $arraynew[$val['mainCatiParentId']]['iParentId'] = $val['mainCatiParentId'];

                $arraynew[$val['mainCatiParentId']][] = $val;
            }
        }

        /*if(isset($_REQUEST['test'])){

            echo"<pre>";

            print_r(array_values($arraynew));

            die;

        }*/

        foreach ($arraynew as $ke => $val) {
            if (!empty($val['Title'])) {
                $returnarr .= "<option value='service-main-cat' disabled>".$val['Title'].'</option>';
            }

            foreach ($val as $key1 => $value1) {
                if (!empty($value1['vCategory'])) {
                    $returnarr .= "<option value='service-main-subcat' disabled>".$value1['vCategory'].'</option>';
                }

                foreach ($value1['SubCategories'] as $subkey => $subval) {
                    $parentCat = $subval['vCategory'];

                    $childCat = $subval['vVehicleType'];

                    $selected = '';

                    if ($subval['iVehicleTypeId'] === $iVehicleTypeId) {
                        $selected = 'selected=selected';
                    }

                    $returnarr .= '<option value='.$subval['iVehicleTypeId'].' '.$selected.'>'.$subval['vVehicleType'].$locationArr[$subval['iVehicleTypeId']].'</option>';
                }
            }
        }

        /*foreach($data as $key=>$value) {



            if(!empty($value['vCategory'])){

                if(isset($value['mainCatiParentId'])){

                    $returnarr .= "<optgroup label='".$value['vCategoryTitleMain']."' id=".$key."></optgroup>";

                }

                $returnarr .= "<optgroup label='".$value['vCategory']."'>";

            }

            foreach($value['SubCategories'] as $subkey=>$subval) {

                $parentCat = $subval['vCategory'];

                $childCat = $subval['vVehicleType'];

                $selected = '';

                if ($subval['iVehicleTypeId'] == $iVehicleTypeId) {

                    $selected = "selected=selected";

                }

                //$returnarr .= "<option value=" . $subval['iVehicleTypeId'] . " " . $selected . ">" . $subval['vCategory'] . "-" . $subval['vVehicleType'] . $locationArr[$subval['iVehicleTypeId']] . "</option>";

                $returnarr .= "<option value=" . $subval['iVehicleTypeId'] . " " . $selected . ">" . $subval['vVehicleType'] . $locationArr[$subval['iVehicleTypeId']] . "</option>";

            }

        }*/

        // $iParentId = $db_car['iParentId'];

        // $enableVehile = 1;

        // if ($iParentId > 0) {

        //	$enableVehile = 0;

        //	if (isset($vehicleStatusArr[$iParentId]) && $vehicleStatusArr[$iParentId] == "Active") {

        //		$enableVehile = 1;

        //	}

        // }

        // //Added By HJ On 06-06-2019 For Check Vehicle Category Parent Id Status End

        // if ($enableVehile == 1) {

        // }

        $returnarr .= '</select></div></div><script src="'.$tconfig['tsite_url'].'assets/js/common_all.js"></script><link rel="stylesheet" href="'.$tconfig['tsite_url_main_admin'].'css/select2/select2.min.css" type="text/css"><script type="text/javascript" src="'.$tconfig['tsite_url_main_admin'].'js/plugins/select2.min.js"></script><script>$(".select2data").select2();</script>';
    } else {
        $returnarr .= '<ul id="iVehicleTypeId">';

        foreach ($rawdata['VehicleTypes'] as $db_car) {
            $Photo_Gallery_folder = $tconfig['tsite_upload_images_vehicle_type_path'].'/'.$db_car['iVehicleTypeId'].'/android/'.$db_car['vLogo'];

            if ('' !== $db_car['vLogo'] && file_exists($Photo_Gallery_folder)) {
                $db_car['vLogo'] = $tconfig['tsite_upload_images_vehicle_type'].'/'.$db_car['iVehicleTypeId'].'/android/'.$db_car['vLogo'];

                $db_car['vLogo1'] = $tconfig['tsite_upload_images_vehicle_type'].'/'.$db_car['iVehicleTypeId'].'/android/'.$db_car['vLogo1'];

                $logo = '<img src='.$db_car['vLogo']." width='60' height='60' data-selcetedLogo = ".$db_car['vLogo1']." org-src='".$db_car['vLogo']."' class='logoImgCar'>";
            } else {
                $db_car['vLogo'] = '';

                $logo = '';

                // $db_car['vLogo'] = $tconfig["tsite_url"]."/webimages/icons/DefaultImg/ic_car.png";
            }

            $location = '';

            if ('' !== $db_car['vLocationName']) {
                $location = ' ('.$db_car['vLocationName'].')';
            } else {
                $location = ' ('.$langage_lbl['LBL_ALL_LOCATIONS'].')'; // added by SP when all location is selected then show ALl on 31-07-2019
            }

            $price_display = 'style="display:none"';

            if (!empty($from_lat) && !empty($to_lat)) {
                $price_display = '';
            }

            $selected = '';

            if ($db_car['iVehicleTypeId'] === $iVehicleTypeId) {
                $selected = 'checked';
            }

            $total_fare_amount = $price_array[$db_car['iVehicleTypeId']];

            $delivery_helper_content = '';

            if ($MODULES_OBJ->isEnableDeliveryHelper() && 'Yes' === $db_car['eDeliveryHelper']) {
                if ('Deliver' === $eType) {
                    $delivery_helper_content .= '<div style="white-space: nowrap;" onclick="showInfo(\'deliveryHelperDesc_'.$db_car['iVehicleTypeId'].'\')"><small style="color: #ff0000">'.$langage_lbl['LBL_INC_DEL_HELPER'].' <i class="fa fa-question-circle" style="color: #000000; position: relative; font-size: 14px"></i></small>';

                    $delivery_helper_content .= '<div id="deliveryHelperDesc_'.$db_car['iVehicleTypeId'].'" style="display: none;">'.$db_car['tDeliveryHelperNoteUser'].'</div></div>';
                }
            }

            if (1 === $fareEstimate) {
                $returnarr .= "<li>

						<div class='veh-left'>

							<div class='radio-main'>

								<span class='radio-hold'>



								</span>

							</div

							><i class='vehicle-ico'>{$logo}</i

							><span class='vehicle-name'>".$db_car['vVehicleType_'.$vLang].'<small>'.$location.'</small>'.$delivery_helper_content."</span>



						</div>

						<div class='price-caption' ".$price_display.'>

							<strong>'.$total_fare_amount."</strong>

							<i onclick='showAsVehicleType_all(this);' data-val=".$db_car['iVehicleTypeId']." class='icon-information'></i>



						</div>

					</li>";
            } else {
                $returnarr .= "<li>

						<div class='veh-left'>

							<div class='radio-main'>

								<span class='radio-hold'>

									<input type='radio' name='iVehicleTypeId' required onChange='showAsVehicleType(this.value)' value='".$db_car['iVehicleTypeId']."' {$selected} data-personsize='".$db_car['iPersonSize']."'>

									<span class='radio-button'></span>

								</span>

							</div

							><i class='vehicle-ico'>{$logo}</i

							><span class='vehicle-name'>".$db_car['vVehicleType_'.$vLang].'<small>'.$location.'</small>'.$delivery_helper_content."</span>



						</div>

						<div class='price-caption' ".$price_display.'>

							<strong>'.$total_fare_amount."</strong>

							<i onclick='showAsVehicleType_all(this);' data-val=".$db_car['iVehicleTypeId']." class='icon-information'></i>



						</div>

					</li>";
            }
        }

        $returnarr .= '</ul>';
    }

    echo $returnarr;

    exit;
}

if ('getEstimateFareDetailsArr' === $type) {
    $distance = $_REQUEST['distance'] ?? '1';

    $promoCode = $_REQUEST['promoCode'] ?? '';

    $promocodeapplied = $_REQUEST['promocodeapplied'] ?? '';

    $from_lat = $_REQUEST['from_lat'] ?? '';

    $from_long = $_REQUEST['from_long'] ?? '';

    $to_lat = $_REQUEST['to_lat'] ?? '';

    $to_long = $_REQUEST['to_long'] ?? '';

    $iMemberId = $_REQUEST['iMemberId'] ?? '';

    // $userType1 = isset($_REQUEST['userType1']) ? $_REQUEST['userType1'] : '';

    // $iUserId = isset($_REQUEST['iUserId']) ? $_REQUEST['iUserId'] : '';

    $vehicleId = $_REQUEST['vehicleId'] ?? '';

    $eType = $_REQUEST['eType'] ?? 'Ride';

    // $eFly = isset($_REQUEST['eFly']) ? trim($_REQUEST['eFly']) : '';

    $iFromStationId = isset($_REQUEST['iFromStationId']) ? trim($_REQUEST['iFromStationId']) : '';

    $iToStationId = isset($_REQUEST['iToStationId']) ? trim($_REQUEST['iToStationId']) : '';

    $timeduration = $_REQUEST['timeduration'] ?? '1';

    // $varfrom = isset($_REQUEST['varfrom']) ? $_REQUEST['varfrom'] : '';

    // $booking_date = isset($_REQUEST['booking_date']) ? $_REQUEST['booking_date'] : '';

    $eFly = '';

    if ('Fly' === $eType) {
        $eFly = 'Yes';

        $eType = 'Ride';

        $iFromLocationId = $iFromStationId;

        $iToLocationId = $iToStationId;
    }

    if ('Yes' === $THEME_OBJ->isXThemeActive()) {
        if (!empty($promocodeapplied) && 1 === $promocodeapplied && !empty($promoCode)) {
            $promoCode = $promoCode;
        } else {
            $promoCode = '';
        }
    }

    if ('' === $booking_date) {
        $booking_date = date('Y-m-d H:i:s');
    }

    $currentdate = urlencode(date('Y-m-d H:i:s'));

    $timeduration = round($timeduration, 2);

    $distance = round($distance, 2);

    $userType1 = ucfirst($userType);

    $bookingFrom = 'Web'; // because pass to the webservice and then when admin then no need to pass getpassengerdetails etc..even no need to pass when this type call from web.so pass web

    if ('Admin' === $userType1) {
        $iMemberId = 0;
    }

    // added for admin country code on 11-01-2020

    if ($iMemberId <= 0 || '' === $iMemberId) {
        $countryCodeAdmin = $vCountry;
    }

    // added for Company country code on 11-01-2020

    if ('Company' === $userType1) {
        $countryCodeAdmin = $vCountry;
    }

    // for rounding

    if ('' === $userType1) {
        $iMemberId = 0;
    }

    if ('Rider' === $userType1 || 'Admin' === $userType1) {
        $userType1 = 'Passenger';
    }

    // UserTypeWeb parameter passed instead of UserType bcoz when from company panel, then it will be from dl file.

    $url = $tconfig['tsite_url'].WEBSERVICE_API_FILE_NAME."?deviceWidth=720.0&GeneralAppVersion=1.11&distance={$distance}&PromoCode={$promoCode}&DestLongitude={$to_long}&DestLatitude={$to_lat}&EndLongitude={$from_long}&StartLatitude={$from_lat}&DEFAULT_SERVICE_CATEGORY_ID=&FOOD_ONLY=&ePaymentMode=cash&vGeneralLang={$vLang}&ONLYDELIVERALL=&type=getEstimateFareDetailsArr&GeneralUserType={$GeneralUserType}&tSessionId={$tSessionId}&isDestinationAdded=Yes&deviceHeight=1436.0&GeneralMemberId={$GeneralMemberId}&SelectedCar={$vehicleId}&UBERX_PARENT_CAT_ID=&vFirebaseDeviceToken=epb8puanWuE%3AAPA91bEA6P7Xz_htL1BOy1ubXdYNAIYyVsLA6fOmRqXV9x2soDUaxmiML89bBxWXGWPDzzMUr2fG7jgr4AFDfe1wPXc-BMFossy1pxIWRWBFak02F700ARfZSPAaAD0vlyLBjoWDBx7G&CUS_IS_SINGLE_STORE_SELECTION=No&vGeneralCurrency={$curr}&GeneralDeviceType=Android&iUserId={$iMemberId}&vCurrentTime={$currentdate}&vUserDeviceCountry={$vCountry}&GeneralAppVersionCode=12&vTimeZone={$vTimeZone}&iServiceId=&time={$timeduration}&DELIVERALL=&APP_TYPE=&eType={$eType}&eFly={$eFly}&iFromStationId={$iFromStationId}&iToStationId={$iToStationId}&UserTypeWeb={$userType1}&bookingFrom={$bookingFrom}";

    $ch = curl_init();

    $timeout = 0;

    curl_setopt($ch, CURLOPT_URL, $url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    // curl_setopt ($ch, CURLOPT_USERAGENT,"Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1)");

    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

    $rawdata = curl_exec($ch);

    curl_close($ch);

    $rawdata = json_decode($rawdata, true);

    $returnArr = [];

    if (1 === $rawdata['Action']) {
        // $returnArr['estimateArr'] = $rawdata['message'];

        // $returnArr['totalFare'] = $rawdata['vSymbol']." ".$rawdata['total_fare_amount'];

        $estimateArr = [];

        $totalFare = $roundoff = $totalnetFare = $eFlatTrip = $returnArr['fFlatTripPrice'] = $returnArr['fPickUpPrice'] = $returnArr['fNightPrice'] = 0;

        $returnArr['eFlatTrip'] = 'No';

        $Fare_data = $rawdata['message'];

        $totalFareData = end($Fare_data);

        $totalFare = current(array_slice($totalFareData, -1));

        for ($r = 0; $r < count($Fare_data); ++$r) {
            foreach ($Fare_data[$r] as $key => $val) {
                if ('total_fare_amount' === $key || 'eDisplaySeperator' === $key) {
                } else {
                    $fareArr = [];

                    $fareArr['key'] = $key;

                    $fareArr['value'] = $val;

                    $estimateArr[] = $fareArr;
                }

                if ($key === $langage_lbl_admin['LBL_SUBTOTAL_TXT']) {
                    $totalFare = $val;
                }

                if ($key === $langage_lbl_admin['LBL_ROUNDING_DIFF_TXT']) {
                    $roundoff = 1;
                }

                if ($key === $langage_lbl_admin['LBL_ROUNDING_NET_TOTAL_TXT']) {
                    $totalnetFare = $val;
                }

                // flat trip related changes

                // if ($key == $langage_lbl_admin['LBL_FLAT_TRIP_FARE_TXT']) {

                //	$returnArr['fFlatTripPrice'] = $val;

                //	$eFlatTrip = 1;

                // }

                // if((strpos($key, $langage_lbl_admin['LBL_SURGE']) !== false) && $eFlatTrip==1) {

                //	echo $returnArr['fFlatTripPrice']."====".$val;

                //	$returnArr['fFlatTripPrice'] = $returnArr['fFlatTripPrice'] + $val;

                // }
            }
        }

        // echo $returnArr['fFlatTripPrice'];exit;

        if (1 === $roundoff) {
            $totalFare = $totalnetFare;
        }

        $returnArr['estimateArr'] = $estimateArr;

        $returnArr['totalFare'] = $totalFare;

        $sql = "select vVehicleType_{$vLang} as vVehicleType from vehicle_type where iVehicleTypeId = '".$vehicleId."' LIMIT 1";

        $db_model = $obj->MySQLSelect($sql);

        $returnArr['vehicleName'] = $db_model[0]['vVehicleType'];

        $returnArr['vehicleImage'] = '';

        if (!empty($rawdata['vehicleImage'])) {
            $returnArr['vehicleImage'] = $rawdata['vehicleImage'];
        }

        if (1 === $eFlatTrip || 'Yes' === $eFly) {
            $returnArr['faretxt'] = $langage_lbl_admin['LBL_GENERAL_NOTE_FLAT_FARE_EST'];
        } else {
            $returnArr['faretxt'] = $langage_lbl_admin['LBL_GENERAL_NOTE_FARE_EST'];
        }

        // if ($eFlatTrip == 1) {

        //	$returnArr['eFlatTrip'] = "Yes";

        //	//$returnArr['fFlatTripPrice'] = $totalFare;

        // }
    }

    // flattrip, surgecharge popup shown using this type

    $url = $tconfig['tsite_url'].WEBSERVICE_API_FILE_NAME."?deviceWidth=720.0&GeneralAppVersion=1.11&SelectedCarTypeID={$vehicleId}&SelectedTime=&vTimeZone={$vTimeZone}&PickUpLatitude={$from_lat}&PickUpLongitude={$from_long}&DestLongitude={$to_long}&DestLatitude={$to_lat}&iMemberId={$iMemberId}&UserType={$userType1}&vGeneralLang={$vLang}&ONLYDELIVERALL=&type=checkSurgePrice&GeneralUserType={$GeneralUserType}&tSessionId={$tSessionId}&deviceHeight=1436.0&GeneralMemberId={$GeneralMemberId}&UBERX_PARENT_CAT_ID=&vFirebaseDeviceToken=epb8puanWuE%3AAPA91bEA6P7Xz_htL1BOy1ubXdYNAIYyVsLA6fOmRqXV9x2soDUaxmiML89bBxWXGWPDzzMUr2fG7jgr4AFDfe1wPXc-BMFossy1pxIWRWBFak02F700ARfZSPAaAD0vlyLBjoWDBx7G&CUS_IS_SINGLE_STORE_SELECTION=No&vGeneralCurrency={$curr}&GeneralDeviceType=Android&vCurrentTime={$currentdate}&vUserDeviceCountry={$vCountry}&GeneralAppVersionCode=12&iServiceId=&time={$timeduration}&DELIVERALL=&APP_TYPE=&eType={$eType}";

    $ch = curl_init();

    $timeout = 0;

    curl_setopt($ch, CURLOPT_URL, $url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    // curl_setopt ($ch, CURLOPT_USERAGENT,"Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1)");

    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

    $rawdata = curl_exec($ch);

    curl_close($ch);

    $rawdata = json_decode($rawdata, true);

    if (!empty($rawdata['SurgePrice'])) {
        if ('LBL_PICK_SURGE_NOTE' === $rawdata['message']) {
            $returnArr['fPickUpPrice'] = $rawdata['SurgePriceValue'];
        } else {
            $returnArr['fNightPrice'] = $rawdata['SurgePriceValue'];
        }
    }

    if (!empty($rawdata['eFlatTrip']) && 'Yes' === $rawdata['eFlatTrip']) {
        $returnArr['eFlatTrip'] = $rawdata['eFlatTrip'];

        $returnArr['fFlatTripPrice'] = $rawdata['fFlatTripPricewithsymbol'];
    }

    echo json_encode($returnArr);

    exit;
}

if ('loadAvailableCabDriver' === $type) {
    $iVehicleTypeId = $_REQUEST['iVehicleTypeId'] ?? '';

    $type = $_REQUEST['type'] ?? '';

    $eType = $_REQUEST['eType'] ?? 'Ride';

    $sql = "SELECT iCountryId FROM country WHERE vCountryCode = '".$vCountry."'";

    $countryarray = $obj->MySQLSelect($sql);

    $countryid = $countryarray[0]['iCountryId'];

    // added by SP for get vehicles/services according to the pickup location on 02-08-2019 start

    $from_lat = $_REQUEST['from_lat'] ?? '';

    $from_long = $_REQUEST['from_long'] ?? '';

    $to_lat = $_REQUEST['to_lat'] ?? '';

    $to_long = $_REQUEST['to_long'] ?? '';

    $distance = $_REQUEST['distance'] ?? '1';

    $duration = $_REQUEST['duration'] ?? '1';

    // $promoCode = isset($_REQUEST['promoCode']) ? $_REQUEST['promoCode'] : '';

    // $iFromStationId = isset($_REQUEST['iFromStationId']) ? $_REQUEST['iFromStationId'] : '';

    // $iToStationId = isset($_REQUEST['iToStationId']) ? $_REQUEST['iToStationId'] : '';

    // $userType = isset($_REQUEST['userType']) ? $_REQUEST['userType'] : '';

    // added by sunita 11-01-2020

    $booking_date = $_REQUEST['booking_date'] ?? '';

    // $iUserId = isset($_REQUEST['iUserId']) ? $_REQUEST['iUserId'] : '';

    $vSourceAddresss = $_REQUEST['vSourceAddresss'] ?? '';

    // $vSourceAddresss = replace_content($vSourceAddresss);

    $vSourceAddresss = urlencode($vSourceAddresss);

    // $currentdate = replace_content(date("Y-m-d H:i:s"));

    $currentdate = urlencode(date('Y-m-d H:i:s'));

    $fareEstimate = 0; // for fare estimation it will be 1 but it will checked later on

    $eShowOnlyMoto = $eFly = 'No';

    if ('Moto' === $eType) {
        $eType = 'Ride';

        $eShowOnlyMoto = 'Yes';
    }

    if ('Fly' === $eType) {
        $eType = 'Ride';

        $eFly = 'Yes';
    }

    $bookingFrom = 'Web'; // here it is passed bcoz pool vehicles not shown to the web site.

    $url = $tconfig['tsite_url'].WEBSERVICE_API_FILE_NAME."?iVehicleTypeId={$iVehicleTypeId}&PassengerLon={$from_long}&deviceWidth=1080.0&GeneralAppVersion=1.11&DEFAULT_SERVICE_CATEGORY_ID=&FOOD_ONLY=&eType={$eType}&vGeneralLang={$vLang}&ONLYDELIVERALL=&type=loadAvailableCab&GeneralUserType={$GeneralUserType}&tSessionId={$tSessionId}&deviceHeight=1794.0&GeneralMemberId={$GeneralMemberId}&scheduleDate=&UBERX_PARENT_CAT_ID=&SelectedCabType=&PickUpAddress={$vSourceAddresss}&CUS_IS_SINGLE_STORE_SELECTION=No&vGeneralCurrency={$curr}&GeneralDeviceType=Android&iUserId={$iUserId}&vCurrentTime={$currentdate}&vUserDeviceCountry={$vCountry}&PassengerLat={$from_lat}&GeneralAppVersionCode=12&vTimeZone={$vTimeZone}&iServiceId=&sortby=&DELIVERALL=&APP_TYPE=&eShowOnlyMoto={$eShowOnlyMoto}&eFly={$eFly}&bookingFrom={$bookingFrom}";

    $ch = curl_init();

    $timeout = 0;

    curl_setopt($ch, CURLOPT_URL, $url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    // curl_setopt ($ch, CURLOPT_USERAGENT,"Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1)");

    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

    $rawdata = curl_exec($ch);

    curl_close($ch);

    $rawdata = json_decode($rawdata, true);

    $dbDrivers = $rawdata['AvailableCabList'];

    // echo "<PRE>"; print_R($dbDrivers); exit;

    $con = '';

    foreach ($dbDrivers as $key => $value) {
        if ('Available' === $value['vAvailability']) {
            $statusIcon = $tconfig['tsite_url'].'booking/img/green-icon.png';
        } elseif ('Active' === $value['vAvailability']) {
            $statusIcon = $tconfig['tsite_url'].'booking/img/red.png';
        } elseif ('On Going Trip' === $value['vAvailability']) {
            $statusIcon = $tconfig['tsite_url'].'booking/img/yellow.png';
        } elseif ('Arrived' === $value['vAvailability']) {
            $statusIcon = $tconfig['tsite_url'].'booking/img/blue.png';
        } else {
            $statusIcon = $tconfig['tsite_url'].'booking/img/offline-icon.png';
        }

        if (empty($value['vImage'])) {
            $DriverImage = $tconfig['tsite_url'].'assets/img/profile-user-img.png';
        } else {
            $DriverImage = $value['vImage'];
        }

        $phone = '';

        if ('admin' === $_SESSION['sess_signin']) {
            $phone = ' <b>+'.clearMobile($value['vCode'].$value['vPhone']).'</b>';
        }

        $fullname = $value['vName'].''.$value['vLastName'];

        if ('Yes' === $THEME_OBJ->isXThemeActive()) {
            $con .= '<li onclick="showPopupDriver('.$value['iDriverId'].');"><label class="map-tab-img"><label class="map-tab-img1"><img src="'.$DriverImage.'"></label><img src="'.$statusIcon.'"></label><p class="driver_'.$value['iDriverId'].'">'.clearName($fullname).$phone.' </p><button type="button" href="javascript:void(0)" class="assign-driverbtn gen-btn xs-small-btn" onClick=\'checkUserBalance('.$value['iDriverId'].');\'>'.$langage_lbl_admin['LBL_ASSIGN_DRIVER_BUTTON'].'</button></li>';
        } else {
            $con .= '<li onclick="showPopupDriver('.$value['iDriverId'].');"><label class="map-tab-img"><label class="map-tab-img1"><img src="'.$DriverImage.'"></label><img src="'.$statusIcon.'"></label><p class="driver_'.$value['iDriverId'].'">'.clearName($fullname).$phone.' </p><a href="javascript:void(0)" class="btn btn-success assign-driverbtn" onClick=\'checkUserBalance('.$value['iDriverId'].');\'>'.$langage_lbl_admin['LBL_ASSIGN_DRIVER_BUTTON'].'</a></li>';
        }
    }

    echo $con;

    exit;
}

if ('GetMemberWalletBalance' === $type) {
    $iDriverId = $_REQUEST['driverId'] ?? '';

    $usertype = $_REQUEST['usertype'] ?? '';

    $bookingFrom = 'Web'; // here it is passed bcoz here currency returns in default currency only.

    // cubejekdev.bbcsproducts.net/webservice_shark.php?type=GetMemberWalletBalance&iUserId=39&UserType=Driver&vGeneralLang=EN&GeneralUserType=Passenger&tSessionId=afkk1r68qgonk4jn4e7ceeh0nc1601642150&GeneralMemberId=4&vGeneralCurrency=USD&GeneralDeviceType=Android&vUserDeviceCountry=IN&GeneralAppVersionCode=12&iServiceId=&sortby=&DELIVERALL=&APP_TYPE=

    $url = $tconfig['tsite_url'].WEBSERVICE_API_FILE_NAME."?type=GetMemberWalletBalance&iUserId={$iDriverId}&UserType={$usertype}&vGeneralLang={$vLang}&GeneralUserType={$GeneralUserType}&tSessionId={$tSessionId}&GeneralMemberId={$GeneralMemberId}&vGeneralCurrency={$curr}&GeneralDeviceType=Android&vUserDeviceCountry={$vCountry}&GeneralAppVersionCode=12&iServiceId=&sortby=&DELIVERALL=&APP_TYPE=&bookingFrom={$bookingFrom}";

    $ch = curl_init();

    $timeout = 0;

    curl_setopt($ch, CURLOPT_URL, $url);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    // curl_setopt ($ch, CURLOPT_USERAGENT,"Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1)");

    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);

    $rawdata = curl_exec($ch);

    curl_close($ch);

    $rawdata = json_decode($rawdata, true);

    $cont = '';

    $user_available_balance = $rawdata['user_available_balance_web'];

    if ('Yes' === $COMMISION_DEDUCT_ENABLE) {
        if ($user_available_balance > $WALLET_MIN_BALANCE) {
            $cont .= 1;

            $cont .= '|'.$user_available_balance;
        } else {
            $cont .= 0;

            $cont .= '|'.$user_available_balance;
        }
    } else {
        $cont .= 1;

        $cont .= '|'.$user_available_balance;
    }

    echo $cont;

    exit;
}

if ('saveMultiDeliveryDetails' === $type) {
    $AllData = $_REQUEST['AllData'] ?? '';

    if (empty($AllData)) {
        $AllData = [];
    } else {
        $AllData = json_decode(str_replace('\\', '', $AllData), true);

        if (empty($AllData)) {
            $AllData = [];
        }
    }

    $details_arr = [];

    // $details_arr[0]['iPackageTypeId'] = isset($_REQUEST['iPackageTypeId']) ? $_REQUEST['iPackageTypeId'] : '';

    // $details_arr[0]['vReceiverName'] = isset($_REQUEST['vReceiverName']) ? $_REQUEST['vReceiverName'] : '';
    $phonecode = str_replace('+', '', $_REQUEST['vPhoneCode']);
    $phone = str_replace($phonecode, '', $AllData[0]['3']);

    $details_arr[0]['1'] = !empty($_REQUEST['1']) && isset($_REQUEST['1']) ? $_REQUEST['1'] : $AllData[0]['1'];

    $details_arr[0]['2'] = !empty($_REQUEST['2']) && isset($_REQUEST['2']) ? $_REQUEST['2'] : $AllData[0]['2'];

    $details_arr[0]['3'] = !empty($_REQUEST['3']) && isset($_REQUEST['3']) ? $_REQUEST['3'] : $phone;

    $details_arr[0]['4'] = !empty($_REQUEST['4']) && isset($_REQUEST['4']) ? $_REQUEST['4'] : $AllData[0]['4'];

    $details_arr[0]['vReceiverAddress'] = $_REQUEST['vReceiverAddress'] ?? $AllData[0]['vReceiverAddress'];

    $details_arr[0]['vReceiverLatitude'] = $_REQUEST['vReceiverLatitude'] ?? $AllData[0]['vReceiverLatitude'];

    $details_arr[0]['vReceiverLongitude'] = $_REQUEST['vReceiverLongitude'] ?? $AllData[0]['vReceiverLongitude'];

    $vPhoneCode = $_REQUEST['vPhoneCode'] ?? '';

    if (empty($AllData)) {
        $details_arr[0]['3'] = $vPhoneCode.$details_arr[0]['3'];
    }

    // $AllDataJson[] = $details_arr;

    $AllDataJson = $details_arr;

    // $AllDataJson = array_merge($AllDataJson, $details_arr);
    // print_R($AllDataJson);
    echo json_encode($AllDataJson);

    exit;
}
