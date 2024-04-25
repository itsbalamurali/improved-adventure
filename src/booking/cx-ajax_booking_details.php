<?php

include_once '../common.php';

include_once '../include/include_webservice_enterprisefeatures.php';

if ($MODULES_OBJ->isAirFlightModuleAvailable(1)) {
    include_once '../include/features/include_fly_stations.php';
}

$countryId = $_REQUEST['countryId'] ?? '';

$iVehicleTypeId = $_REQUEST['iVehicleTypeId'] ?? '';

$type = $_REQUEST['type'] ?? '';

$eType = $_REQUEST['eType'] ?? 'Ride';

$sql = "SELECT iCountryId FROM country WHERE vCountryCode = '".$countryId."'";

$countryarray = $obj->MySQLSelect($sql);

$countryid = $countryarray[0]['iCountryId'];

// added by SP for get vehicles/services according to the pickup location on 02-08-2019 start

$from_lat = $_REQUEST['from_lat'] ?? '';

$from_long = $_REQUEST['from_long'] ?? '';

$to_lat = $_REQUEST['to_lat'] ?? '';

$to_long = $_REQUEST['to_long'] ?? '';

$from_estimatefare = $_REQUEST['from_estimatefare'] ?? '';

$to_estimatefare = $_REQUEST['to_estimatefare'] ?? '';

$distance = $_REQUEST['distance'] ?? '1';

$duration = $_REQUEST['duration'] ?? '1';

$promoCode = $_REQUEST['promoCode'] ?? '';

$iFromStationId = $_REQUEST['iFromStationId'] ?? '';

$iToStationId = $_REQUEST['iToStationId'] ?? '';

$userType = $_REQUEST['userType'] ?? '';

// added by sunita 11-01-2020

$booking_date = $_REQUEST['booking_date'] ?? '';

$duration = !empty($duration) ? round($duration / 60, 2) : 0;

// $time = round($time / 60);

$distance = !empty($distance) ? round($distance / 1_000, 2) : 0;

$admin = 0;

if ('Admin' === $userType) {
    $admin = 1;
}

$fareEstimate = 0;

if (empty($userType)) {
    $fareEstimate = 1;
}

if ('RIDER' === strtoupper($userType)) {
    $table_name = 'register_user';

    $field = 'iUserId';
} elseif ('COMPANY' === strtoupper($userType)) {
    $table_name = 'company';

    $field = 'iCompanyId';
}

$iUserId = $_SESSION['sess_iUserId'];

// this is for cubex only but not put in the condition bc this variable is used in qry and here condition is not put...

$getMemberData = $obj->MySQLSelect('SELECT vLang,vTimeZone FROM '.$table_name." WHERE {$field}='".$iUserId."'");

$vTimeZone = 'Asia/Kolkata';

$vLang = 'EN';

if (count($getMemberData) > 0) {
    $vLang = $getMemberData[0]['vLang'];

    $vTimeZone = $getMemberData[0]['vTimeZone'];
}

date_default_timezone_set($vTimeZone);

$vLang = get_value($table_name, 'vLang', $field, $_SESSION['sess_iUserId'], 'true'); // get language code of driver

if ('' === $vLang || null === $vLang) {
    $vLang = get_value('language_master', 'vCode', 'eDefault', 'Yes', '', 'true');
}

// if(empty($iUserId)){

if (isset($_SESSION['sess_lang']) && '' !== trim($_SESSION['sess_lang'])) {
    $vLang = $_SESSION['sess_lang'];
}

if (empty($vLang)) {
    $vLang = $LANG_OBJ->FetchDefaultLangData('vCode');
}

$locations_where = '';

if (!empty($from_lat)) {
    $vSelectedLatitude = $from_lat;

    $vSelectedLongitude = $from_long;

    $pickuplocationarr = [$vSelectedLatitude, $vSelectedLongitude];

    $GetVehicleIdfromGeoLocation = FetchVehicleTypeFromGeoLocation($pickuplocationarr);

    if (!empty($GetVehicleIdfromGeoLocation)) {
        $locations_where = " AND vt.iLocationid IN(-1, {$GetVehicleIdfromGeoLocation}) ";
    }

    $vSelectedLatitude = $to_lat;

    $vSelectedLongitude = $to_long;

    $dropofflocationarr = [$vSelectedLatitude, $vSelectedLongitude];
}

// added by SP for get vehicles/services according to the pickup location on 02-08-2019 end

$returnarr = '';

$sql_vehicle_category_table_name = getVehicleCategoryTblName();

$getAllVehicleData = $obj->MySQLSelect('SELECT iVehicleCategoryId,eStatus FROM '.$sql_vehicle_category_table_name);

$vehicleStatusArr = [];

for ($r = 0; $r < count($getAllVehicleData); ++$r) {
    $vehicleStatusArr[$getAllVehicleData[$r]['iVehicleCategoryId']] = $getAllVehicleData[$r]['eStatus'];
}

if ('getVehicles' === $type) {
    $eFly = '';

    if ('UberX' === $eType) {
        $whereParentId = '';

        if ($parent_ufx_catid > 0) {
            $whereParentId = " AND vc.iParentId='".$parent_ufx_catid."'";
        }

        $sql23 = "SELECT vt.iVehicleTypeId,vc.iParentId,vt.tTypeDesc,vt.iVehicleCategoryId,vt.vVehicleType_{$vLang}, vt.eFareType, vt.fFixedFare, vt.fPricePerHour, vt.fPricePerKM, vt.fPricePerMin, vt.iBaseFare,vt.fCommision, vt.iMinFare,vt.iPersonSize, vt.vLogo as vVehicleTypeImage, vt.eType, vt.eIconType, vt.eAllowQty, vt.fMinHour, vt.iMaxQty, vt.iVehicleTypeId, vt.fTimeSlot,vt.fTimeSlotPrice,vc.iParentId,vc.vCategory_{$vLang},lm.vLocationName,(SELECT vcs.iDisplayorder FROM vehicle_category as vcs WHERE vcs.iVehicleCategoryId = vc.iParentId) as iMainDisplayOrder FROM `vehicle_type` AS vt LEFT JOIN `country` AS c ON c.iCountryId=vt.iCountryId LEFT JOIN ".$sql_vehicle_category_table_name." as vc on vc.iVehicleCategoryId = vt.iVehicleCategoryId left join location_master as lm ON lm.iLocationId = vt.iLocationid WHERE 1 {$whereParentId} AND vt.eType='".$eType."' AND vt.ePoolStatus='No' AND vc.eStatus = 'Active' AND vt.eStatus = 'Active' {$locations_where} ORDER BY  iMainDisplayOrder ASC, vc.iDisplayOrder ASC, vt.iDisplayOrder ASC";

    // ORDER BY vt.iVehicleTypeId ASC
    } else {
        if ('Ride' === $eType) {
            if ('Ride' !== $APP_TYPE) {
                $sql_other = " AND vt.eFly='0' AND vt.eIconType != 'Bike' AND vt.eIconType != 'Cycle' AND vt.eIconType != 'Ambulance' ";
            } else {
                $sql_other = " AND vt.eFly='0' AND vt.eIconType != 'Ambulance' ";
            }
        } elseif ('Fly' === $eType) {
            $sql_other = " AND vt.eFly='1'";

            $eFly = 'Yes';

            $eType = 'Ride';
        } elseif ('Moto' === $eType) {
            $sql_other .= " AND (vt.eIconType = 'Bike' OR vt.eIconType = 'Cycle')";

            $eType = 'Ride';
        } else {
            $sql_other = '';
        }

        // $sql23 = "SELECT vt.*,lm.vLocationName FROM `vehicle_type` AS vt LEFT JOIN `country` AS c ON c.iCountryId=vt.iCountryId left join location_master as lm ON lm.iLocationId = vt.iLocationid WHERE (lm.iCountryId='" . $countryid . "' OR vt.iLocationid = '-1') AND vt.eType='" . $eType . "'".$sql_other." AND vt.eStatus = 'Active' AND ePoolStatus = 'No' $locations_where ORDER BY vt.iVehicleTypeId ASC";

        $sql23 = "SELECT vt.*,lm.vLocationName FROM `vehicle_type` AS vt LEFT JOIN `country` AS c ON c.iCountryId=vt.iCountryId left join location_master as lm ON lm.iLocationId = vt.iLocationid WHERE vt.eType='".$eType."'".$sql_other." AND vt.eStatus = 'Active' AND ePoolStatus = 'No' {$locations_where} ORDER BY vt.iDisplayOrder ASC";
    }

    $db_carType = $obj->MySQLSelect($sql23);

    // added by SP for fly stations on 19-08-2019, its bc fly vehicles are shown only if price in location wise fare is entered

    $iFromLocationId = $iToLocationId = '';

    if ('Yes' === $eFly) {
        $ssql1 = '';

        $iFromLocationId = $iFromStationId;

        $iToLocationId = $iToStationId;

        // fly_location_wise_fare.iFromLocationId

        if (!empty($iFromLocationId)) {
            $ssql1 .= " AND fl.iFromLocationId = {$iFromLocationId} AND fl.iToLocationId = {$iToLocationId}"; // becoz vehicles are shown of source location only..if enter iscon then show vehicles which have from station iscon, and also add for it destination
        } /* else {

          exit;

          } */

        $db_carType = [];

        $FlylocationData = $obj->MySQLSelect("SELECT GROUP_CONCAT(DISTINCT(vt.iVehicleTypeId)) as vehicle FROM fly_location_wise_fare as fl LEFT JOIN vehicle_type as vt on vt.iVehicleTypeId = fl.iVehicleTypeId WHERE 1 {$ssql1} AND vt.eStatus = 'Active' AND fl.eStatus='Active' AND vt.eFly = 1");

        foreach ($FlylocationData as $row) {
            $FlyVehicleIds = $row['vehicle'];
        }

        if (!empty($FlyVehicleIds)) {
            $db_carType = $obj->MySQLSelect("SELECT vt.*,lm.vLocationName FROM vehicle_type as vt left join location_master as lm ON lm.iLocationId = vt.iLocationid WHERE 1 {$locations_where} AND vt.eStatus = 'Active' AND vt.iVehicleTypeId IN (".$FlyVehicleIds.')');
        } else {
            if (!empty($iFromLocationId) && !empty($iToLocationId)) {
                echo -1;

                exit;
            }
        }

        // $db_carType = $obj->MySQLSelect("SELECT DISTINCT(vt.iVehicleTypeId),vt.*,lm.vLocationName FROM vehicle_type as vt RIGHT JOIN fly_location_wise_fare ON vt.iVehicleTypeId = fly_location_wise_fare.iVehicleTypeId left join location_master as lm ON lm.iLocationId = vt.iLocationid WHERE 1 $locations_where $ssql AND vt.eStatus = 'Active' AND fly_location_wise_fare.eStatus = 'Active'");
    }

    if ('UberX' === $eType) {
        $returnarr .= '<div class="general-form"><div class="form-group"><select name="iVehicleTypeId" id="iVehicleTypeId" required onChange="showAsVehicleType(this.value)" class="select2data">';

        $returnarr .= '<option value="" > '.$langage_lbl['LBL_SELECT_TXT'].' '.$langage_lbl['LBL_MYTRIP_TRIP_TYPE'].'</option>';
    } else {
        $returnarr .= '<ul id="iVehicleTypeId">';
    }

    if ('UberX' === $eType) {
        $getMainCat = $obj->MySQLSelect('SELECT iVehicleCategoryId,vCategory_'.$vLang.' AS catName,vCategoryTitle_'.$vLang." as vCategoryTitle FROM vehicle_category WHERE eStatus='Active'");

        $cateNameArr = $cateTitleArr = [];

        for ($n = 0; $n < count($getMainCat); ++$n) {
            $mainCatId = $getMainCat[$n]['iVehicleCategoryId'];

            $cateNameArr[$mainCatId] = $getMainCat[$n]['catName'];

            $cateTitleArr[$mainCatId] = $getMainCat[$n]['vCategoryTitle'];
        }

        $arraynew = $ufxdata = [];

        foreach ($db_carType as $key => $value) {
            $arraynew[$value['iVehicleCategoryId']]['vCategory'] = $value['vCategory_'.$vLang];

            $arraynew[$value['iVehicleCategoryId']]['iParentId'] = $value['iParentId'];

            $arraynew[$value['iVehicleCategoryId']]['Subcategories'][] = $value;
        }

        $mainarray = array_values($arraynew);

        foreach ($mainarray as $key1 => $value1) {
            $vCategoryTitleMain = $cateNameArr[$value1['iParentId']];

            $ufxdata[$value1['iParentId']]['maincatName'] = $vCategoryTitleMain;

            $ufxdata[$value1['iParentId']][] = $value1;
        }

        $db_carType = array_values($ufxdata);
    }

    foreach ($db_carType as $db_car) {
        $selected = '';

        // if ($db_car['iVehicleTypeId'] == $iVehicleTypeId || ($kk==0 && empty($iVehicleTypeId))) {

        if ($db_car['iVehicleTypeId'] === $iVehicleTypeId) {
            // $selected = "selected=selected";

            $selected = 'checked';
        }

        // $kk++;

        $location = '';

        if ('' !== $db_car['vLocationName']) {
            $location = ' ('.$db_car['vLocationName'].')';
        } else {
            $location = ' ('.$langage_lbl['LBL_ALL_LOCATIONS'].')'; // added by SP when all location is selected then show ALl on 31-07-2019
        }

        if ('UberX' === $eType) {
            if (!empty($db_car['maincatName'])) {
                $returnarr .= "<option value='service-main-cat' disabled>".$db_car['maincatName'].'</option>';
            }

            foreach ($db_car as $k => $val) {
                if (!empty($val['vCategory'])) {
                    $returnarr .= "<option value='service-main-subcat' disabled>".$val['vCategory'].'</option>';
                }

                foreach ($val['Subcategories'] as $subkey => $subval) {
                    $parentCat = $subval['vCategory'];

                    $selected = '';

                    if ($subval['iVehicleTypeId'] === $iVehicleTypeId) {
                        $selected = 'selected=selected';
                    }

                    $returnarr .= '<option value='.$subval['iVehicleTypeId'].' '.$selected.'>'.$subval['vVehicleType_'.$vLang].$locationArr[$subval['iVehicleTypeId']].'</option>';
                }
            }

        // echo"<pre>";print_r($db_carType);die;

        /*  $iParentId = $db_car['iParentId'];

          $enableVehile = 1;

          if ($iParentId > 0) {

              $enableVehile = 0;

              if (isset($vehicleStatusArr[$iParentId]) && $vehicleStatusArr[$iParentId] == "Active") {

                  $enableVehile = 1;

              }

          }



          //Added By HJ On 06-06-2019 For Check Vehicle Category Parent Id Status End

          if ($enableVehile == 1) {

              $selected = '';

              if ($db_car['iVehicleTypeId'] == $iVehicleTypeId) {

                  $selected = "selected=selected";

              }

              $returnarr .= "<option value=" . $db_car['iVehicleTypeId'] . " " . $selected . ">" . $db_car['vCategory_' . $vLang] . "-" . $db_car['vVehicleType_' . $vLang] . $location . "</option>";

          }*/
        } else {
            $eFlatTrip = 'No';

            $fFlatTripPrice = 0;

            if (!empty($pickuplocationarr) && !empty($dropofflocationarr)) {
                $data_flattrip = checkFlatTripnew($pickuplocationarr, $dropofflocationarr, $db_car['iVehicleTypeId']);

                $eFlatTrip = $data_flattrip['eFlatTrip'];

                $fFlatTripPrice = $data_flattrip['Flatfare'];
            }

            if (!empty($from_lat)) {
                $iUserId = $_SESSION['sess_iUserId'];

                $sourceLocationArr = [$from_lat, $from_long];

                $destinationLocationArr = [$to_lat, $to_long];

                // $duration = round(($duration), 2);

                // $distance = round(($distance), 2);

                $Fare_data[0]['total_fare_amount'] = 0;

                // added by sunita 11-01-2020

                if ('' === $booking_date) {
                    $booking_date = date('Y-m-d H:i:s');
                }

                // $booking_date = date("Y-m-d H:i:s");

                if ('Admin' === $userType) {
                    $iUserId = 0;
                }

                // added for admin country code on 11-01-2020

                if ($iUserId <= 0 || '' === $iUserId) {
                    $countryCodeAdmin = $countryId;
                }

                // added for Company country code on 11-01-2020

                if ('Company' === $userType) {
                    $countryCodeAdmin = $countryId;

                    $ePaymentModerounding = 'cash';
                }

                // for rounding

                if ('' === $userType) {
                    $iUserId = 0;

                    $ePaymentModerounding = 'cash';
                }

                if ('Rider' === $userType || 'Admin' === $userType) {
                    $userType = 'Passenger';

                    $ePaymentModerounding = 'cash';
                }

                $Fare_data = calculateApproximateFareGeneral($duration, $distance, $db_car['iVehicleTypeId'], $iUserId, 1, '', '', $promoCode, 1, 0, 0, 0, 'DisplySingleVehicleFare', $userType, '', $db_car['iVehicleTypeId'], 'Yes', $eFlatTrip, $fFlatTripPrice, $sourceLocationArr, $destinationLocationArr, '', '', $booking_date, $eFly, $iFromLocationId, $iToLocationId);

                if (1 === $admin) {
                    $userType = 'Admin';
                }

                $totalFare = 0;

                $subtotalLbl = $langage_lbl['LBL_SUBTOTAL_TXT'];

                $nettotalLbl = $langage_lbl['LBL_ROUNDING_NET_TOTAL_TXT'];

                $getSymbol = '';

                $roundoff = $totalnetFare = 0;

                $totalFareData = end($Fare_data);

                $totalFare = current(array_slice($totalFareData, -1));

                for ($r = 0; $r < count($Fare_data); ++$r) {
                    foreach ($Fare_data[$r] as $key => $val) {
                        if ('' === $getSymbol) {
                            $getSymbol = explode(' ', $val);
                        }

                        if ('total_fare_amount' === $key || 'eDisplaySeperator' === $key) {
                            if ('total_fare_amount' === $key) {
                                // $totalFare = $getSymbol[0] . " " . $val;

                                $totalFare = $val;
                            }
                        } else {
                            $fareArr = [];

                            $fareArr['key'] = $key;

                            $fareArr['value'] = $val;

                            $estimateArr[] = $fareArr;
                        }

                        if ($key === $subtotalLbl) {
                            $totalFare = $val;
                        }

                        if ($key === $langage_lbl['LBL_ROUNDING_DIFF_TXT']) {
                            $roundoff = 1;
                        }

                        if ($key === $nettotalLbl) {
                            $totalnetFare = $val;
                        }
                    }
                }

                if (1 === $roundoff) {
                    $totalFare = $totalnetFare;
                }

                // $total_fare_amount_sub = array_column($Fare_data, 'Subtotal');

                $total_fare_amount_sub = array_column($Fare_data, 'total_fare_amount');

                // $total_fare_amount = $Fare_data[count($Fare_data) - 2]['Subtotal'];

                // $total_fare_amount = $total_fare_amount_sub[0];

                $total_fare_amount = $totalFare;
            } else {
                $total_fare_amount = 0;
            }

            $Photo_Gallery_folder = $tconfig['tsite_upload_images_vehicle_type_path'].'/'.$db_car['iVehicleTypeId'].'/android/'.$db_car['vLogo'];

            if ('' !== $db_car['vLogo'] && file_exists($Photo_Gallery_folder)) {
                $db_car['vLogo'] = $tconfig['tsite_upload_images_vehicle_type'].'/'.$db_car['iVehicleTypeId'].'/android/'.$db_car['vLogo'];

                $db_car['vLogo1'] = $tconfig['tsite_upload_images_vehicle_type'].'/'.$db_car['iVehicleTypeId'].'/android/'.$db_car['vLogo1'];

                $logo = '<img src='.$db_car['vLogo']." width='60' height='60' data-selcetedLogo = ".$db_car['vLogo1'].' org-src= '.$db_car['vLogo']." class='logoImgCar'>";
            } else {
                $db_car['vLogo'] = '';

                $logo = '';

                // $db_car['vLogo'] = $tconfig["tsite_url"]."/webimages/icons/DefaultImg/ic_car.png";
            }

            $price_display = 'style="display:none"';

            if (!empty($from_lat) && !empty($to_lat)) {
                $price_display = '';
            }

            $delivery_helper_content = '';

            if ($MODULES_OBJ->isEnableDeliveryHelper() && 'Yes' === $db_car['eDeliveryHelper']) {
                if ('Deliver' === $eType) {
                    if (!empty($db_car['tDeliveryHelperNoteUser'])) {
                        $db_car['tDeliveryHelperNoteUser'] = json_decode($db_car['tDeliveryHelperNoteUser'], true);

                        $db_car['tDeliveryHelperNoteUser'] = $db_car['tDeliveryHelperNoteUser']['tDeliveryHelperNoteUser_'.$_SESSION['sess_lang']];
                    }

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

                                    <input type='radio' name='iVehicleTypeId' required onChange='showAsVehicleType(this.value)' value='".$db_car['iVehicleTypeId']."' ".$selected." data-personsize='".$db_car['iPersonSize']."'>

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
    }

    if ('UberX' === $eType) {
        $returnarr .= '</select></div></div><script src="'.$tconfig['tsite_url'].'assets/js/common_all.js"></script><link rel="stylesheet" href="'.$tconfig['tsite_url_main_admin'].'css/select2/select2.min.css" type="text/css"><script type="text/javascript" src="'.$tconfig['tsite_url_main_admin'].'js/plugins/select2.min.js"></script><script>$(".select2data").select2();</script>';
    } else {
        if (empty($db_carType)) {
            $returnarr .= '<li>'.$langage_lbl['LBL_NO_VEHICLES_FOUND'].'</li>';
        }

        $returnarr .= '</ul>';
    }

    echo $returnarr;

    exit;
}

if ('getVehicleEstimate' === $type) {
    $_SESSION['fareestimate_eType'] = $eType;

    $_SESSION['fareestimate_redirect'] = 'Yes';

    $_SESSION['fareestimate_from_lat'] = $from_lat;

    $_SESSION['fareestimate_from_long'] = $from_long;

    $_SESSION['fareestimate_to_lat'] = $to_lat;

    $_SESSION['fareestimate_to_long'] = $to_long;

    $_SESSION['fareestimate_sourceaddress'] = $from_estimatefare;

    $_SESSION['fareestimate_destaddress'] = $to_estimatefare;

    echo '1';

    exit;
}

unset($_SESSION['fareestimate_eType'], $_SESSION['fareestimate_redirect'], $_SESSION['fareestimate_from_lat'], $_SESSION['fareestimate_from_long'], $_SESSION['fareestimate_to_lat'], $_SESSION['fareestimate_to_long'], $_SESSION['fareestimate_sourceaddress'], $_SESSION['fareestimate_destaddress']);

?>

