<?php
include_once '../common.php';

require_once TPATH_CLASS.'/Imagecrop.class.php';
$thumb = new thumbnail();

$selectedServicecType = '';

    $selectedServicecType = '?iVehicleCategoryId='.$_REQUEST['iVehicleCategoryId'].'&option='.$_REQUEST['vVehicleType_EN'].'&eStatus='.$_REQUEST['eStatus'].'';
if ('' !== $_REQUEST['parent']) {
    $selectedServicecType = '?iVehicleCategoryId='.$_REQUEST['parent'].'&option='.$_REQUEST['vVehicleType_EN'].'&eStatus='.$_REQUEST['eStatus'].'';
}

    $sql_vehicle_category_table_name = getVehicleCategoryTblName();

$sql = "SELECT iCountryId,vCountry,vCountryCode FROM country WHERE eStatus = 'Active'";
$db_country = $obj->MySQLSelect($sql);

$sql_location = "SELECT * FROM location_master WHERE eStatus = 'Active' AND eFor = 'VehicleType' ORDER BY  vLocationName ASC ";
$db_location = $obj->MySQLSelect($sql_location);

$sql = "SELECT vName,vSymbol FROM currency WHERE eDefault = 'Yes'";
$db_currency = $obj->MySQLSelect($sql);

// to fetch max iDisplayOrder from table for insert
$select_order = $obj->MySQLSelect("SELECT count(iDisplayOrder) AS iDisplayOrder FROM vehicle_type where eType ='UberX'");
$iDisplayOrder = $select_order[0]['iDisplayOrder'] ?? 0;
$iDisplayOrder_max = $iDisplayOrder + 1; // Maximum order number

$id = $_REQUEST['id'] ?? '';

$message_print_id = $id;
$success = $_REQUEST['success'] ?? 0;
$action = ('' !== $id) ? 'Edit' : 'Add';

$tbl_name = 'vehicle_type';
$script = 'ServiceType';

if ('Ride-Delivery-UberX' === $APP_TYPE) {
    $app_type_service = 'UberX';
} else {
    $app_type_service = $APP_TYPE;
}

    $vVehicleType = $_POST['vVehicleType'] ?? '';
$iVehicleCategoryId = $_POST['iVehicleCategoryId'] ?? '';
// Added By HJ On 30-07-2019 For Get Vehicle/Service Parent Category Id Start
if ((isset($_REQUEST['parent']) && $_REQUEST['parent'] > 0) && '' === $iVehicleCategoryId) {
    $iVehicleCategoryId = $_REQUEST['parent'];
}
// Added By HJ On 30-07-2019 For Get Vehicle/Service Parent Category Id End
$fPricePerKM = $_POST['fPricePerKM'] ?? '';
$fPricePerMin = $_POST['fPricePerMin'] ?? '';
$iBaseFare = $_POST['iBaseFare'] ?? '';
$iMinFare = $_POST['iMinFare'] ?? '';
$fCommision = $_POST['fCommision'] ?? '';
$iPersonSize = $_POST['iPersonSize'] ?? 1;
// $fPickUpPrice = isset($_POST['fPickUpPrice']) ? $_POST['fPickUpPrice'] : '';
$fNightPrice = $_POST['fNightPrice'] ?? '';
// $tPickStartTime = isset($_POST['tPickStartTime']) ? $_POST['tPickStartTime'] : '';

    $tMonPickStartTime = $_POST['tMonPickStartTime'] ?? '';
$tMonPickEndTime = $_POST['tMonPickEndTime'] ?? '';
$fMonPickUpPrice = $_POST['fMonPickUpPrice'] ?? '';

$tTuePickStartTime = $_POST['tTuePickStartTime'] ?? '';
$tTuePickEndTime = $_POST['tTuePickEndTime'] ?? '';
$fTuePickUpPrice = $_POST['fTuePickUpPrice'] ?? '';

$tWedPickStartTime = $_POST['tWedPickStartTime'] ?? '';
$tWedPickEndTime = $_POST['tWedPickEndTime'] ?? '';
$fWedPickUpPrice = $_POST['fWedPickUpPrice'] ?? '';

$tThuPickStartTime = $_POST['tThuPickStartTime'] ?? '';
$tThuPickEndTime = $_POST['tThuPickEndTime'] ?? '';
$fThuPickUpPrice = $_POST['fThuPickUpPrice'] ?? '';

$tFriPickStartTime = $_POST['tFriPickStartTime'] ?? '';
$tFriPickEndTime = $_POST['tFriPickEndTime'] ?? '';
$fFriPickUpPrice = $_POST['fFriPickUpPrice'] ?? '';

$tSatPickStartTime = $_POST['tSatPickStartTime'] ?? '';
$tSatPickEndTime = $_POST['tSatPickEndTime'] ?? '';
$fSatPickUpPrice = $_POST['fSatPickUpPrice'] ?? '';

$tSunPickStartTime = $_POST['tSunPickStartTime'] ?? '';
$tSunPickEndTime = $_POST['tSunPickEndTime'] ?? '';
$fSunPickUpPrice = $_POST['fSunPickUpPrice'] ?? '';

    // $tPickEndTime = isset($_POST['tPickEndTime']) ? $_POST['tPickEndTime'] : '';
$tNightStartTime = $_POST['tNightStartTime'] ?? '';
$tNightEndTime = $_POST['tNightEndTime'] ?? '';
$eStatus_picktime = $_POST['ePickStatus'] ?? 'off';
$ePickStatus = ('on' === $eStatus_picktime) ? 'Active' : 'Inactive';
$eStatus_nighttime = $_POST['eNightStatus'] ?? 'off';
$eNightStatus = ('on' === $eStatus_nighttime) ? 'Active' : 'Inactive';
$eType = $_POST['eType'] ?? '';

$eFareType = $_POST['eFareType'] ?? '';
$fFixedFare = $_POST['fFixedFare'] ?? '';
$eAllowQty = $_POST['eAllowQty'] ?? 'No';
$iMaxQty = $_POST['iMaxQty'] ?? '';
$fPricePerHour = $_POST['fPricePerHour'] ?? '';
$fMinHour = $_POST['fMinHour'] ?? '';
$fTimeSlot = $_POST['fTimeSlot'] ?? '';
$fTimeSlotPrice = $_POST['fTimeSlotPrice'] ?? '';

// $fVisitFee = isset($_POST['fVisitFee']) ? $_POST['fVisitFee'] : '';
$iCancellationTimeLimit = $_POST['iCancellationTimeLimit'] ?? '';
$fCancellationFare = $_POST['fCancellationFare'] ?? '';

$iWaitingFeeTimeLimit = $_POST['iWaitingFeeTimeLimit'] ?? '';
$fWaitingFees = $_POST['fWaitingFees'] ?? '';

$iCountryId = $_POST['iCountryId'] ?? '';
$iStateId = $_POST['iStateId'] ?? '';
$iCityId = $_POST['iCityId'] ?? '';
$iLocationId = $_POST['iLocationId'] ?? '';

$backlink = $_POST['backlink'] ?? '';
$previousLink = $_POST['backlink'] ?? '';

    //  for ordering
$iDisplayOrder = $_POST['iDisplayOrder'] ?? $iDisplayOrder;
$temp_order = $_POST['temp_order'] ?? '';

$vTitle_store = $tTypeDescArr = [];
$sql = 'SELECT * FROM `language_master` ORDER BY `iDispOrder`';
$db_master = $obj->MySQLSelect($sql);
$count_all = count($db_master);
if ($count_all > 0) {
    for ($i = 0; $i < $count_all; ++$i) {
        $vValue = 'vVehicleType_'.$db_master[$i]['vCode'];
        $vTitle_store[] = $vValue;
        ${$vValue} = $_POST[$vValue] ?? '';
        $tTypeDesc = '';
        if (isset($_POST['tTypeDesc_'.$db_master[$i]['vCode']])) {
            $tTypeDesc = htmlspecialchars($_POST['tTypeDesc_'.$db_master[$i]['vCode']], ENT_IGNORE);
        }
        $tTypeDescArr['tTypeDesc_'.$db_master[$i]['vCode']] = $tTypeDesc;

        $db_tInfoText = 'tInfoText_'.$db_master[$i]['vCode'];
        $tInfoTextUserArr[$db_tInfoText] = $_POST[$db_tInfoText] ?? '';
    }
}
$weekDaysArr = ['Monday' => 'Mon', 'Tuesday' => 'Tue', 'Wednesday' => 'Wed', 'Thursday' => 'Thu', 'Friday' => 'Fri', 'Saturday' => 'Sat', 'Sunday' => 'Sun'];
$nightTimeArr = $nightSurgeDataArr = [];
if (isset($_POST['btnsubmit'])) {
    if ('Add' === $action && !$userObj->hasPermission('create-service-type')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to create service type.';
        header('Location:service_type.php'.$selectedServicecType);

        exit;
    }
    if ('Edit' === $action && !$userObj->hasPermission('edit-service-type')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to update service type.';
        header('Location:service_type.php'.$selectedServicecType);

        exit;
    }
    foreach ($weekDaysArr as $day => $dval) {
        $dayStartIndex = 't'.$dval.'NightStartTime';
        $dayEndIndex = 't'.$dval.'NightEndTime';
        $priceIndex = 'f'.$dval.'NightPrice';
        $nStartTime = $_POST[$dayStartIndex] ?? '00:00:00';
        $nEndTime = $_POST[$dayEndIndex] ?? '';
        $nPrice = $_POST[$priceIndex] ?? '1.00';
        if ('' === $nStartTime) {
            // $nStartTime = '00:00:00';
        }
        if ('' === $nEndTime) {
            // $nEndTime = '00:00:00';
        }
        if ('' === $nPrice) {
            $nPrice = '1.00';
        }
        $nightTimeArr[$dayStartIndex] = $nStartTime;
        $nightTimeArr[$dayEndIndex] = $nEndTime;
        $nightTimeArr[$priceIndex] = $nPrice;
    }
    if ('Fixed' === $eFareType) {
        $ePickStatus = 'Inactive';
        $eNightStatus = 'Inactive';
        $iMinFare = 0;
    }
    if ('Regular' !== $eFareType) {
        $iMinFare = 0;
    }
    if ('Regular' === $eFareType || 'Hourly' === $eFareType) {
        $eAllowQty = 'No';
        $iMaxQty = '1';
    }
    $insertUpdateNightJson = '';
    if (count($nightTimeArr) > 0 && 'Active' === $eNightStatus) {
        $insertUpdateNightJson = ",tNightSurgeData='".json_encode($nightTimeArr)."'";
    }
    // echo $insertUpdateNightJson;die;
    if (isset($_FILES['vLogo']) && '' !== $_FILES['vLogo']['name']) {
        $filecheck = basename($_FILES['vLogo']['name']);
        $fileextarr = explode('.', $filecheck);
        $ext = strtolower($fileextarr[count($fileextarr) - 1]);
        $flag_error = 0;
        if ('png' !== $ext) {
            $flag_error = 1;
            $var_msg = 'Upload only png image';
        }
        $data = getimagesize($_FILES['vLogo']['tmp_name']);
        $width = $data[0];
        $height = $data[1];
        if (360 !== $width && 360 !== $height) {
            $flag_error = 1;
            $var_msg = 'Please Upload image only 360px * 360px';
        }
        if (1 === $flag_error) {
            if ('Add' === $action) {
                header('Location:service_type_action.php?varmsg='.$var_msg.'&success=3');

                exit;
            }
            header('Location:service_type_action.php?id='.$id.'&varmsg='.$var_msg.'&success=3');

            exit;

            // getPostForm($_POST, $var_msg, "vehicle_type_action.php?success=0&var_msg=".$var_msg);
            // exit;
        }
    }
    if (isset($_FILES['vLogo1']) && '' !== $_FILES['vLogo1']['name']) {
        $filecheck = basename($_FILES['vLogo1']['name']);
        $fileextarr = explode('.', $filecheck);
        $ext = strtolower($fileextarr[count($fileextarr) - 1]);
        $flag_error = 0;
        if ('png' !== $ext) {
            $flag_error = 1;
            $var_msg = 'Upload only png image';
        }
        $data = getimagesize($_FILES['vLogo1']['tmp_name']);
        $width = $data[0];
        $height = $data[1];
        if (360 !== $width && 360 !== $height) {
            $flag_error = 1;
            $var_msg = 'Please Upload image only 360px * 360px';
        }
        if (1 === $flag_error) {
            if ('Add' === $action) {
                header('Location:service_type_action.php?varmsg='.$var_msg.'&success=3');

                exit;
            }
            header('Location:service_type_action.php?id='.$id.'&varmsg='.$var_msg.'&success=3');

            exit;

            exit;
        }
    }

    $vVehicleType = $_POST['vVehicleType_'.$default_lang];

    if ('Active' === $ePickStatus) {
        /*  if($tPickStartTime > $tPickEndTime){
          header("Location:vehicle_type_action.php?id=".$id."&success=3");exit;
          } */

        /* if ($tMonPickStartTime > $tMonPickEndTime) {

          $varmsg = "Please Select  Monday Peak Start Time less than Monday Peak End Time.";
          header("Location:vehicle_type_action.php?id=" . $id . "&success=3&varmsg=" . $varmsg);
          exit;
          }

          if ($tTuePickStartTime > $tTuePickEndTime) {
          $varmsg = "Please Select  Tuesday Peak Start Time less than Tuesday Peak End Time.";
          header("Location:vehicle_type_action.php?id=" . $id . "&success=3&varmsg=" . $varmsg);
          exit;
          }

          if ($tWedPickStartTime > $tWedPickEndTime) {
          $varmsg = "Please Select  Wednesday Peak Start Time less than Wednesday Peak End Time.";
          header("Location:vehicle_type_action.php?id=" . $id . "&success=3&varmsg=" . $varmsg);
          exit;
          }

          if ($tThuPickStartTime > $tThuPickEndTime) {
          $varmsg = "Please Select  Thursday Peak Start Time less than Thursday Peak End Time.";
          header("Location:vehicle_type_action.php?id=" . $id . "&success=3&varmsg=" . $varmsg);
          exit;
          }

          if ($tFriPickStartTime > $tFriPickEndTime) {
          $varmsg = "Please Select  Friday Peak Start Time less than Friday Peak End Time.";
          header("Location:vehicle_type_action.php?id=" . $id . "&success=3&varmsg=" . $varmsg);
          exit;
          }

          if ($tSatPickStartTime > $tSatPickEndTime) {
          $varmsg = "Please Select  Saturday Peak Start Time less than Saturday Peak End Time.";
          header("Location:vehicle_type_action.php?id=" . $id . "&success=3&varmsg=" . $varmsg);
          exit;
          }

          if ($tSunPickStartTime > $tSunPickEndTime) {
          $varmsg = "Please Select  Sunday Peak Start Time less than Sunday Peak End Time.";
          header("Location:vehicle_type_action.php?id=" . $id . "&success=3&varmsg=" . $varmsg);
          exit;
          } */
    }

    if ('Active' === $eNightStatus) {
        /* if ($tNightStartTime > $tNightEndTime) {
          header("Location:vehicle_type_action.php?id=" . $id . "&success=4");
          exit;
          } */
    }
    if (SITE_TYPE === 'Demo') {
        header('Location:service_type_action.php?id='.$id.'&success=2');

        exit;
    }

    if ('1' === $temp_order && 'Add' === $action) {
        $temp_order = $iDisplayOrder_max;
    }
    /* if ($temp_order > $iDisplayOrder) {
      for ($f = $temp_order - 1; $f >= $iDisplayOrder; $f--) {
      $sql = "UPDATE " . $tbl_name . " SET iDisplayOrder = '" . ($f + 1) . "' WHERE iDisplayOrder = '" . $f . "' AND eType ='UberX'";
      $obj->sql_query($sql);
      }
      } else if ($temp_order < $iDisplayOrder) {
      for ($m = $temp_order + 1; $m <= $iDisplayOrder; $m++) {
      $sql = "UPDATE " . $tbl_name . " SET iDisplayOrder = '" . ($m - 1) . "' WHERE iDisplayOrder = '" . $m . "' AND eType ='UberX'";
      $obj->sql_query($sql);
      }
      } */

    /*$q = "INSERT INTO ";
    $where = '';
    if ($id != '') {
        $q = "UPDATE ";
        $where = " WHERE `iVehicleTypeid` = '" . $id . "'";
    }*/

    $update = [];
    $where = " iVehicleTypeid = '".$id."'";

    $sql_str = $jsonTypeDesc = '';
    if (count($tTypeDescArr) > 0) {
        // for solve \r\n issue added by sunita
        $description = str_ireplace(["\r", "\n", '\r', '\n'], '', $tTypeDescArr);
        // $jsonTypeDesc = getJsonFromAnArr($description);
        $jsonTypeDesc = getJsonFromAnArrWithoutClean($description);
    }

    if (count($vTitle_store) > 0) {
        for ($i = 0; $i < count($vTitle_store); ++$i) {
            $vValue = 'vVehicleType_'.$db_master[$i]['vCode'];
            $sql_str .= $vValue." = '".$_POST[$vTitle_store[$i]]."',";
            $update['vVehicleType_'.$db_master[$i]['vCode']] = $_POST[$vTitle_store[$i]];
        }
    }

    $insertUpdateNightJson = '';
    if (count($nightTimeArr) > 0 && 'Active' === $eNightStatus) {
        $update['tNightSurgeData'] = $obj->getJsonFromAnArr($nightTimeArr);
    }
    // for ($i = 0; $i < count($vTitle_store); $i++) {
    // $vValue = 'vVehicleType_' . $db_master[$i]['vCode'];
    // echo $_POST[$vTitle_store[$i]] ; exit;
    /*$query = $q . " `" . $tbl_name . "` SET
                `vVehicleType` = '" . $vVehicleType . "',
                `iVehicleCategoryId` = '" . $iVehicleCategoryId . "',
                `eFareType` = '" . $eFareType . "',
                `fFixedFare` = '" . $fFixedFare . "',
                `fPricePerKM` = '" . $fPricePerKM . "',
                `fPricePerMin` = '" . $fPricePerMin . "',
                `iBaseFare` = '" . $iBaseFare . "',
                `iMinFare` = '" . $iMinFare . "',
                `fCommision` = '" . $fCommision . "',
                `iPersonSize` = '" . $iPersonSize . "',
                `fNightPrice` = '" . $fNightPrice . "',
                `tNightStartTime` = '" . $tNightStartTime . "',
                `tNightEndTime` = '" . $tNightEndTime . "',
                `ePickStatus` = '" . $ePickStatus . "',
                `eAllowQty` = '" . $eAllowQty . "',
                `fPricePerHour` = '" . $fPricePerHour . "',
                `fMinHour` = '" . $fMinHour . "',
                `fTimeSlot` = '" . $fTimeSlot . "',
                `fTimeSlotPrice` = '" . $fTimeSlotPrice . "',
                `iMaxQty` = '" . $iMaxQty . "',
                `eType` = '" . $eType . "',
                `iCountryId` = '" . $iCountryId . "',
                `iLocationid` = '" . $iLocationId . "',
                `iStateId` = '" . $iStateId . "',
                `iCityId` = '" . $iCityId . "',
                `eNightStatus` = '" . $eNightStatus . "',
                `tMonPickStartTime` = '" . $tMonPickStartTime . "',
                `tMonPickEndTime` = '" . $tMonPickEndTime . "',
                `fMonPickUpPrice` = '" . $fMonPickUpPrice . "',
                `tTuePickStartTime` = '" . $tTuePickStartTime . "',
                `tTuePickEndTime` = '" . $tTuePickEndTime . "',
                `fTuePickUpPrice` = '" . $fTuePickUpPrice . "',
                `tWedPickStartTime` = '" . $tWedPickStartTime . "',
                `tWedPickEndTime` = '" . $tWedPickEndTime . "',
                `fWedPickUpPrice` = '" . $fWedPickUpPrice . "',
                `tThuPickStartTime` = '" . $tThuPickStartTime . "',
                `tThuPickEndTime` = '" . $tThuPickEndTime . "',
                `fThuPickUpPrice` = '" . $fThuPickUpPrice . "',
                `tFriPickStartTime` = '" . $tFriPickStartTime . "',
                `tFriPickEndTime` = '" . $tFriPickEndTime . "',
                `fFriPickUpPrice` = '" . $fFriPickUpPrice . "',
                `tSatPickStartTime` = '" . $tSatPickStartTime . "',
                `tSatPickEndTime` = '" . $tSatPickEndTime . "',
                `fSatPickUpPrice` = '" . $fSatPickUpPrice . "',
                `tSunPickStartTime` = '" . $tSunPickStartTime . "',
                `tSunPickEndTime` = '" . $tSunPickEndTime . "',
                `fSunPickUpPrice` = '" . $fSunPickUpPrice . "',
                `iCancellationTimeLimit` = '" . $iCancellationTimeLimit . "',
                `fCancellationFare` = '" . $fCancellationFare . "',
                `iWaitingFeeTimeLimit` = '" . $iWaitingFeeTimeLimit . "',
                `fWaitingFees` = '" . $fWaitingFees . "',
                `tTypeDesc` = '" . $jsonTypeDesc . "',
                " . $sql_str . "
                `iDisplayOrder` = '" . $iDisplayOrder . "'
        $insertUpdateNightJson"
            . $where;*/
    // " . $vValue . " = '" . $_POST[$vTitle_store[$i]] . "'"
    // echo $query;die;
    // $obj->sql_query($query);

    $update['vVehicleType'] = $vVehicleType;
    $update['iVehicleCategoryId'] = $iVehicleCategoryId;
    $update['eFareType'] = $eFareType;
    $update['fFixedFare'] = $fFixedFare;
    $update['fPricePerKM'] = $fPricePerKM;
    $update['fPricePerMin'] = $fPricePerMin;
    $update['iBaseFare'] = $iBaseFare;
    $update['iMinFare'] = $iMinFare;
    $update['fCommision'] = $fCommision;
    $update['iPersonSize'] = $iPersonSize;
    $update['fNightPrice'] = $fNightPrice;
    $update['tNightStartTime'] = $tNightStartTime;
    $update['tNightEndTime'] = $tNightEndTime;
    $update['ePickStatus'] = $ePickStatus;
    $update['eAllowQty'] = $eAllowQty;
    $update['fPricePerHour'] = $fPricePerHour;
    $update['fMinHour'] = $fMinHour;
    $update['fTimeSlot'] = $fTimeSlot;
    $update['fTimeSlotPrice'] = $fTimeSlotPrice;
    $update['iMaxQty'] = $iMaxQty;
    $update['eType'] = $eType;
    $update['iCountryId'] = $iCountryId;
    $update['iLocationid'] = $iLocationId;
    $update['iStateId'] = $iStateId;
    $update['iCityId'] = $iCityId;
    $update['eNightStatus'] = $eNightStatus;
    $update['tMonPickStartTime'] = $tMonPickStartTime;
    $update['tMonPickEndTime'] = $tMonPickEndTime;
    $update['fMonPickUpPrice'] = $fMonPickUpPrice;
    $update['tTuePickStartTime'] = $tTuePickStartTime;
    $update['tTuePickEndTime'] = $tTuePickEndTime;
    $update['fTuePickUpPrice'] = $fTuePickUpPrice;
    $update['tWedPickStartTime'] = $tWedPickStartTime;
    $update['tWedPickEndTime'] = $tWedPickEndTime;
    $update['fWedPickUpPrice'] = $fWedPickUpPrice;
    $update['tThuPickStartTime'] = $tThuPickStartTime;
    $update['tThuPickEndTime'] = $tThuPickEndTime;
    $update['fThuPickUpPrice'] = $fThuPickUpPrice;
    $update['tFriPickStartTime'] = $tFriPickStartTime;
    $update['tFriPickEndTime'] = $tFriPickEndTime;
    $update['fFriPickUpPrice'] = $fFriPickUpPrice;
    $update['tSatPickStartTime'] = $tSatPickStartTime;
    $update['tSatPickEndTime'] = $tSatPickEndTime;
    $update['fSatPickUpPrice'] = $fSatPickUpPrice;
    $update['tSunPickStartTime'] = $tSunPickStartTime;
    $update['tSunPickEndTime'] = $tSunPickEndTime;
    $update['fSunPickUpPrice'] = $fSunPickUpPrice;
    $update['iCancellationTimeLimit'] = $iCancellationTimeLimit;
    $update['fCancellationFare'] = $fCancellationFare;
    $update['iWaitingFeeTimeLimit'] = $iWaitingFeeTimeLimit;
    $update['fWaitingFees'] = $fWaitingFees;
    $update['tTypeDesc'] = $jsonTypeDesc;
    $update['iDisplayOrder'] = $iDisplayOrder;
    $update['tInfoText'] = getJsonFromAnArr($tInfoTextUserArr);

    if ('' !== $id) {
        $id = $obj->MySQLQueryPerform($tbl_name, $update, 'update', $where);
    } else {
        $insert_id = $obj->MySQLQueryPerform($tbl_name, $update, 'insert');
    }

    $id = ('' !== $id) ? $id : $insert_id;
    // }
    // exit;
    if (isset($_FILES['vLogo']) && '' !== $_FILES['vLogo']['name']) {
        $currrent_upload_time = time();
        $img_path = $tconfig['tsite_upload_images_vehicle_type_path'];
        $temp_gallery = $img_path.'/';
        $image_object = $_FILES['vLogo']['tmp_name'];
        $image_name = $_FILES['vLogo']['name'];

        $check_file_query = 'select iVehicleTypeId,vLogo from vehicle_type where iVehicleTypeId='.$id;
        $check_file = $obj->sql_query($check_file_query);

        if ('' !== $image_name) {
            $img = $UPLOAD_OBJ->GeneralImageUploadVehicleType($message_print_id, $image_name, $image_object, $check_file[0]['vLogo']);
            $img_time = explode('_', $img);
            $time_val = $img_time[0];
            $vImage = $time_val.'.png';
            // $vImage = "ic_car_" . $vVehicleType1 . ".png";

            $sql = 'UPDATE '.$tbl_name." SET `vLogo` = '".addslashes($vImage)."' WHERE `iVehicleTypeId` = '".$id."'";

            $obj->sql_query($sql);
        }
    }

    if (isset($_FILES['vLogo1']) && '' !== $_FILES['vLogo1']['name']) {
        $img_path = $tconfig['tsite_upload_images_vehicle_type_path'];
        $temp_gallery = $img_path.'/';
        $image_object = $_FILES['vLogo1']['tmp_name'];
        $image_name = $_FILES['vLogo1']['name'];
        $check_file_query = 'select iVehicleTypeId,vLogo1 from vehicle_type where iVehicleTypeId='.$id;
        $check_file = $obj->sql_query($check_file_query);
        if ('' !== $image_name) {
            $img = $UPLOAD_OBJ->GeneralImageUploadVehicleType($message_print_id, $image_name, $image_object, $check_file[0]['vLogo1']);
            $img_time = explode('_', $img);
            $time_val = $img_time[0];
            $vImage1 = $time_val.'.png';
            // $vImage1 = "ic_car_" . $vVehicleType1 . ".png";

            $sql = 'UPDATE '.$tbl_name." SET `vLogo1` = '".addslashes($vImage1)."' WHERE `iVehicleTypeId` = '".$id."'";
            $obj->sql_query($sql);
        }
    }

    // $obj->sql_query($query);
    if ('Add' === $action) {
        $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_INSERT_MSG'];
        $_SESSION['success'] = '1';
    } else {
        $_SESSION['var_msg'] = $langage_lbl_admin['LBL_Record_Updated_successfully'];
        $_SESSION['success'] = '1';
    }

    // header("Location:" . $backlink);
    header('Location:service_type.php'.$selectedServicecType);

    exit;
}

// for Edit
$userEditDataArr = [];
if ('Edit' === $action) {
    $sql = 'SELECT * FROM '.$tbl_name." WHERE iVehicleTypeid = '".$id."'";
    $db_data = $obj->MySQLSelect($sql);
    $vLabel = $id;
    if (count($db_data) > 0) {
        if (isset($db_data[0]['tNightSurgeData'])) {
            $nightSurgeDataArr = (array) json_decode($db_data[0]['tNightSurgeData']);
        }
        for ($i = 0; $i < count($db_master); ++$i) {
            foreach ($db_data as $key => $value) {
                $tTypeDesc = json_decode($value['tTypeDesc'], true);
                foreach ($tTypeDesc as $key6 => $value6) {
                    $userEditDataArr[$key6] = $value6;
                }
                $vValue = 'vVehicleType_'.$db_master[$i]['vCode'];
                ${$vValue} = $value[$vValue];
                $vVehicleType = $value['vVehicleType'];
                $iVehicleCategoryId = $value['iVehicleCategoryId'];
                $fPricePerKM = $value['fPricePerKM'];
                $fPricePerMin = $value['fPricePerMin'];
                $iBaseFare = $value['iBaseFare'];
                $iMinFare = $value['iMinFare'];
                $fCommision = $value['fCommision'];
                $iPersonSize = $value['iPersonSize'];
                $fPricePerHour = $value['fPricePerHour'];
                $fMinHour = $value['fMinHour'];
                $fTimeSlot = $value['fTimeSlot'];
                $fTimeSlotPrice = $value['fTimeSlotPrice'];
                $fNightPrice = (0 === $value['fNightPrice']) ? '' : $value['fNightPrice'];
                $tNightStartTime = $value['tNightStartTime'];
                $tNightEndTime = $value['tNightEndTime'];
                $ePickStatus = $value['ePickStatus'];
                $eNightStatus = $value['eNightStatus'];
                $tMonPickStartTime = $value['tMonPickStartTime'];
                $tMonPickEndTime = $value['tMonPickEndTime'];
                $fMonPickUpPrice = (0 === $value['fMonPickUpPrice']) ? '' : $value['fMonPickUpPrice'];
                $tTuePickStartTime = $value['tTuePickStartTime'];
                $tTuePickEndTime = $value['tTuePickEndTime'];
                $fTuePickUpPrice = (0 === $value['fTuePickUpPrice']) ? '' : $value['fTuePickUpPrice'];
                $tWedPickStartTime = $value['tWedPickStartTime'];
                $tWedPickEndTime = $value['tWedPickEndTime'];
                $fWedPickUpPrice = (0 === $value['fWedPickUpPrice']) ? '' : $value['fWedPickUpPrice'];
                $tThuPickStartTime = $value['tThuPickStartTime'];
                $tThuPickEndTime = $value['tThuPickEndTime'];
                $fThuPickUpPrice = (0 === $value['fThuPickUpPrice']) ? '' : $value['fThuPickUpPrice'];
                $tFriPickStartTime = $value['tFriPickStartTime'];
                $tFriPickEndTime = $value['tFriPickEndTime'];
                $fFriPickUpPrice = (0 === $value['fFriPickUpPrice']) ? '' : $value['fFriPickUpPrice'];
                $tSatPickStartTime = $value['tSatPickStartTime'];
                $tSatPickEndTime = $value['tSatPickEndTime'];
                $fSatPickUpPrice = (0 === $value['fSatPickUpPrice']) ? '' : $value['fSatPickUpPrice'];
                $tSunPickStartTime = $value['tSunPickStartTime'];
                $tSunPickEndTime = $value['tSunPickEndTime'];
                $fSunPickUpPrice = (0 === $value['fSunPickUpPrice']) ? '' : $value['fSunPickUpPrice'];
                $vLogo = $value['vLogo'];
                $vLogo1 = $value['vLogo1'];
                $eType = $value['eType'];
                $fFixedFare = $value['fFixedFare'];
                $eFareType = $value['eFareType'];
                $eAllowQty = $value['eAllowQty'];
                $iMaxQty = $value['iMaxQty'];
                $iCancellationTimeLimit = (0 === $value['iCancellationTimeLimit']) ? '' : $value['iCancellationTimeLimit'];
                $fCancellationFare = (0 === $value['fCancellationFare']) ? '' : $value['fCancellationFare'];

                $iWaitingFeeTimeLimit = (0 === $value['iWaitingFeeTimeLimit']) ? '' : $value['iWaitingFeeTimeLimit'];
                $fWaitingFees = (0 === $value['fWaitingFees']) ? '' : $value['fWaitingFees'];

                $iCountryId = $value['iCountryId'];
                $iStateId = $value['iStateId'];
                $iCityId = $value['iCityId'];
                $iLocationId = $value['iLocationid'];
                $iDisplayOrder_db = $value['iDisplayOrder'];

                $tInfoText = (!empty($value['tInfoText'])) ? json_decode($value['tInfoText'], true) : '';
                $userEditDataArr['tInfoText_'.$db_master[$i]['vCode']] = $tInfoText['tInfoText_'.$db_master[$i]['vCode']];
            }
        }
    }
}
$db_data_cat = [];
if ('UberX' === $app_type_service) {
    $ufxServiceId = '';
    if ($parent_ufx_catid > 0) {
        $ufxServiceId = "AND iVehicleCategoryId='".$parent_ufx_catid."'";
    }
    $sql_cat = 'select *  from '.$sql_vehicle_category_table_name." where iParentId='0' AND eStatus != 'Deleted' AND eCatType='ServiceProvider' {$ufxServiceId}";
    $db_data_cat = $obj->MySQLSelect($sql_cat);
}

$EN_available = $LANG_OBJ->checkLanguageExist();
$db_master = $LANG_OBJ->getLangDataDefaultFirst($db_master);
?>
<!DOCTYPE html>
<!--[if IE 8]>
<html lang="en" class="ie8">
    <![endif]-->
    <!--[if IE 9]>
    <html lang="en" class="ie9">
        <![endif]-->
        <!--[if !IE]><!-->
        <html lang="en">
            <!--<![endif]-->
            <!-- BEGIN HEAD-->
            <head>
                <meta charset="UTF-8" />
                <title>Admin | <?php echo $action; ?> <?php echo $langage_lbl_admin['LBL_SERVICE_TXT']; ?> Type</title>
                <meta content="width=device-width, initial-scale=1.0" name="viewport" />
                <link href="assets/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet" />
                <?php
                include_once 'global_files.php';
?>
                <!-- On OFF switch -->
                <link href="../assets/css/jquery-ui.css" rel="stylesheet" />
                <link rel="stylesheet" href="../assets/plugins/switch/static/stylesheets/bootstrap-switch.css" />
                <!--Added By Hasmukh On 11-10-2018 For Clock Time Picker Start Css-->
                <!-- <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css"> -->
                <link rel="stylesheet" type="text/css" href="css/bootstrap-clockpicker.min.css">
                <!--Added By Hasmukh On 11-10-2018 For Clock Time Picker Start Css-->
            </head>
            <!-- END  HEAD-->
            <!-- BEGIN BODY-->
            <body class="padTop53 " >
                <!-- MAIN WRAPPER -->
                <div id="wrap">
                    <?php
    include_once 'header.php';

include_once 'left_menu.php';
?>
                    <!--PAGE CONTENT -->
                    <div id="content">
                        <div class="inner">
                            <div class="row">
                                <div class="col-lg-12">
                                    <h2><?php echo $action; ?> <?php echo $langage_lbl_admin['LBL_SERVICE_TXT']; ?> Type </h2>
                                    <!-- <a href="vehicle_type.php">
                                        <input type="button" value="Back to Listing" class="add-btn">
                                        </a> -->
                                    <a href="javascript:void(0);" class="back_link">
                                    <input type="button" value="Back to Listing" class="add-btn">
                                    </a>
                                </div>
                            </div>
                            <hr />
                            <div class="body-div">
                                <div class="form-group">
                                    <?php if (1 === $success) { ?>
                                    <div class="alert alert-success alert-dismissable msgs_hide">
                                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                                        <?php echo $langage_lbl_admin['LBL_Record_Updated_successfully']; ?>
                                    </div>
                                    <br/>
                                    <?php } elseif (2 === $success) { ?>
                                    <div class="alert alert-danger alert-dismissable ">
                                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                                        <?php echo $langage_lbl_admin['LBL_EDIT_DELETE_RECORD']; ?>
                                    </div>
                                    <br/>
                                    <?php } elseif (3 === $success) { ?>
                                    <div class="alert alert-danger alert-dismissable">
                                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                                        <?php echo $_REQUEST['varmsg']; ?>
                                    </div>
                                    <br/>
                                    <?php } elseif (4 === $success) { ?>
                                    <div class="alert alert-danger alert-dismissable">
                                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                                        "Please select night start time less than night end time."
                                    </div>
                                    <br/>
                                    <?php } ?>
                                    <?php if (null !== $_REQUEST['var_msg']) { ?>
                                    <div class="alert alert-danger alert-dismissable">
                                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                                        Record  Not Updated .
                                    </div>
                                    <br/>
                                    <?php } ?>
                                    <div id="price1" ></div>
                                    <form id="_vehicleType_form" name="_vehicleType_form" method="post" action="" enctype="multipart/form-data">
                                        <input type="hidden" name="id" value="<?php echo $id; ?>"/>
                                        <?php if ('Ride-Delivery-UberX' === $APP_TYPE) { ?>
                                        <input type="hidden" name="APP_TYPE" value="<?php echo $app_type_service; ?>"/>
                                        <?php } else { ?>
                                        <input type="hidden" name="APP_TYPE" value="<?php echo $APP_TYPE; ?>"/>
                                        <?php } ?>
                                        <input type="hidden" name="previousLink" id="previousLink" value="<?php echo $previousLink; ?>"/>
                                        <input type="hidden" name="backlink" id="backlink" value="service_type.php"/>
                                        <div class="row">
                                            <div class="col-lg-12" id="errorMessage">
                                            </div>
                                        </div>
                                        <?php if ('UberX' === $app_type_service) { ?>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label><?php echo $langage_lbl_admin['LBL_SERVICE_TXT']; ?> Category <span class="red"> *</span></label>
                                            </div>
                                            <div class="col-md-6 col-sm-6">
                                                <select  class="form-control" name = 'iVehicleCategoryId' required onchange="getordering(this.value);">
                                                    <option value="">--select--</option>
                                                    <?php for ($i = 0; $i < count($db_data_cat); ++$i) { ?>
                                                    <optgroup label="<?php echo $db_data_cat[$i]['vCategory_'.$default_lang]; ?>">
                                                        <!--  <option value = "<?php echo $db_data_cat[$i]['iVehicleCategoryId']; ?>" <?php echo ($db_data_cat[$i]['iVehicleCategoryId'] === $iVehicleCategoryId) ? 'selected' : ''; ?>><?php echo $db_data_cat[$i]['vCategory_'.$default_lang]; ?>
                                                            </option> -->
                                                        <?php
                                    $sql = 'SELECT * FROM  `'.$sql_vehicle_category_table_name."` WHERE  `iParentId` = '".$db_data_cat[$i]['iVehicleCategoryId']."' AND eStatus != 'Deleted'";
                                                        $db_data2 = $obj->MySQLSelect($sql);
                                                        for ($j = 0; $j < count($db_data2); ++$j) {
                                                            ?>
                                                        <option value = "<?php echo $db_data2[$j]['iVehicleCategoryId']; ?>"
                                                            <?php
                                                            if ($db_data2[$j]['iVehicleCategoryId'] === $iVehicleCategoryId) {
                                                                echo 'selected';
                                                            }
                                                            ?> >
                                                            <?php echo '&nbsp;&nbsp;|-- '.$db_data2[$j]['vCategory_'.$default_lang]; ?>
                                                        </option>
                                                        <?php } ?>
                                                    </optgroup>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <?php } ?>
                                        <?php
                                            $Vehicle_type_name = ('Delivery' === $APP_TYPE) ? 'Deliver' : $app_type_service;
if ('Ride-Delivery' === $APP_TYPE) {
    ?>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>Vehicle Category Type<span class="red"> *</span></label>
                                            </div>
                                            <div class="col-md-6 col-sm-6">
                                                <select  class="form-control" name = 'eType' required id='etypedelivery'>
                                                    <option value="Ride" <?php if ('Ride' === $eType) {
                                                        echo 'selected="selected"';
                                                    } ?> >Ride</option>
                                                    <option value="Deliver"<?php if ('Deliver' === $eType) {
                                                        echo 'selected="selected"';
                                                    } ?>>Delivery</option>
                                                </select>
                                            </div>
                                        </div>
                                        <?php } else {
                                            ?>
                                        <input type="hidden" name="eType" value="<?php echo $Vehicle_type_name; ?>"/>
                                        <?php } ?>
                                        <div class="row" style="display: none;">
                                            <div class="col-lg-12">
                                                <label><?php echo $langage_lbl_admin['LBL_SERVICE_TXT']; ?> Type<span class="red"> *</span>
                                                </label>
                                            </div>
                                            <div class="col-md-6 col-sm-6">
                                                <input type="text" class="form-control" name="vVehicleType"  id="vVehicleType"  value="<?php echo $vVehicleType; ?>" >
                                            </div>
                                        </div>

                                        <?php if (count($db_master) > 1) { ?>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label><?php echo $langage_lbl_admin['LBL_SERVICE_TXT']; ?> Type <span class="red"> *</span></label>
                                            </div>
                                            <div class="col-md-6 col-sm-6">
                                                <input type="text" class="form-control <?php echo ('' === $id) ? 'readonly-custom' : ''; ?>" id="vVehicleType_Default" value="<?php echo $db_data[0]['vVehicleType_'.$default_lang]; ?>" data-originalvalue="<?php echo $db_data[0]['vVehicleType_'.$default_lang]; ?>" readonly="readonly" <?php if ('' === $id) { ?> onclick="editServiceType('Add')" <?php } ?> required>
                                            </div>
                                            <?php if ('' !== $id) { ?>
                                            <div class="col-lg-2">
                                                <button type="button" class="btn btn-info" data-toggle="tooltip" data-original-title="Edit" onclick="editServiceType('Edit')"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>
                                            </div>
                                            <?php } ?>
                                        </div>
                                        <?php if ('UberX' === $app_type_service && 'Provider' === $SERVICE_PROVIDER_FLOW) { ?>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>Short Description <i class="icon-question-sign" data-placement="top" data-toggle="tooltip" data-original-title="This will be displayed on Service Detail screen"></i></label>
                                            </div>
                                            <div class="col-md-6 col-sm-6">
                                                <textarea class="form-control <?php echo ('' === $id) ? 'readonly-custom' : ''; ?>" name="tInfoText_Default"  id="tInfoText_Default" readonly="readonly" data-originalvalue="<?php echo $tInfoText['tInfoText_'.$default_lang]; ?>" <?php if ('' === $id) { ?> onclick="editVehicleDescInfo('Add')" <?php } ?>> <?php echo $tInfoText['tInfoText_'.$default_lang]; ?></textarea>
                                            </div>
                                            <?php if ('' !== $id) { ?>
                                            <div class="col-lg-1">
                                                <button type="button" class="btn btn-info" data-toggle="tooltip" data-original-title="Edit" onclick="editVehicleDescInfo('Edit', 'tInfoText_Modal')"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>
                                            </div>
                                            <?php } ?>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label><?php echo $langage_lbl_admin['LBL_SERVICE_TXT'].' '.$langage_lbl_admin['LBL_DESCRIPTION']; ?></label>
                                            </div>
                                            <div class="col-md-6 col-sm-6">
                                                <textarea class="form-control ckeditor" rows="10" id="tTypeDesc_Default" readonly="readonly"><?php echo $userEditDataArr['tTypeDesc_'.$default_lang]; ?></textarea>
                                            </div>

                                            <div class="col-lg-2">
                                                <button type="button" class="btn btn-info" data-toggle="tooltip" data-original-title="Edit" onclick="editDescWeb('Edit', 'ServiceTypeDesc_Modal')"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>
                                            </div>

                                        </div>
                                        <?php } ?>

                                        <div  class="modal fade" id="ServiceType_Modal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                            <div class="modal-dialog modal-lg" >
                                                <div class="modal-content nimot-class">
                                                    <div class="modal-header">
                                                        <h4>
                                                            <span id="modal_action"></span> <?php echo $langage_lbl_admin['LBL_SERVICE_TXT']; ?> Type
                                                            <button type="button" class="close" data-dismiss="modal" onclick="resetToOriginalValue(this, 'vVehicleType_')">x</button>
                                                        </h4>
                                                    </div>

                                                    <div class="modal-body">
                                                        <?php

                                                            for ($i = 0; $i < $count_all; ++$i) {
                                                                $vCode = $db_master[$i]['vCode'];
                                                                $vTitle = $db_master[$i]['vTitle'];
                                                                $eDefault = $db_master[$i]['eDefault'];
                                                                $vValue = 'vVehicleType_'.$vCode;
                                                                $required = ('Yes' === $eDefault) ? 'required' : '';
                                                                $required_msg = ('Yes' === $eDefault) ? '<span class="red"> *</span>' : '';
                                                                $vValue_desc = 'tCategoryDesc_'.$vCode;
                                                                $lableName = 'tTypeDesc_'.$vCode;
                                                                ${$lableName} = $userEditDataArr[$lableName];
                                                                ?>
                                                                <?php
                                                                        $page_title_class = 'col-lg-12';
                                                                if (count($db_master) > 1) {
                                                                    if ($EN_available) {
                                                                        if ('EN' === $vCode) {
                                                                            $page_title_class = 'col-lg-9';
                                                                        }
                                                                    } else {
                                                                        if ($vCode === $default_lang) {
                                                                            $page_title_class = 'col-lg-9';
                                                                        }
                                                                    }
                                                                }
                                                                ?>
                                                                <div class="row">
                                                                    <div class="col-lg-12">
                                                                        <label><?php echo $langage_lbl_admin['LBL_SERVICE_TXT']; ?> (<?php echo $vTitle; ?>) <?php echo $required_msg; ?></label>

                                                                    </div>
                                                                    <div class="<?php echo $page_title_class; ?>">
                                                                        <input type="text" class="form-control" name="<?php echo $vValue; ?>" id="<?php echo $vValue; ?>" value="<?php echo ${$vValue}; ?>" data-originalvalue="<?php echo ${$vValue}; ?>" placeholder="<?php echo $vTitle; ?> Value">
                                                                        <div class="text-danger" id="<?php echo $vValue.'_error'; ?>" style="display: none;"><?php echo $langage_lbl_admin['LBL_REQUIRED']; ?></div>
                                                                    </div>

                                                                    <?php
                                                                    if (count($db_master) > 1) {
                                                                        if ($EN_available) {
                                                                            if ('EN' === $vCode) { ?>
                                                                            <div class="col-lg-3">
                                                                                <button type ="button" name="allLanguage" id="allLanguage" class="btn btn-primary" onClick="getAllLanguageCode('vVehicleType_', 'EN');" >Convert To All Language</button>
                                                                            </div>
                                                                        <?php }
                                                                            } else {
                                                                                if ($vCode === $default_lang) { ?>
                                                                            <div class="col-lg-3">
                                                                                <button type ="button" name="allLanguage" id="allLanguage" class="btn btn-primary" onClick="getAllLanguageCode('vVehicleType_', '<?php echo $default_lang; ?>');" >Convert To All Language</button>
                                                                            </div>
                                                                        <?php }
                                                                                }
                                                                    }
                                                                ?>
                                                                </div>

                                                            <?php
                                                            }
                                            ?>
                                                    </div>
                                                    <div class="modal-footer" style="margin-top: 0">
                                                        <h5 class="text-left" style="margin-bottom: 15px; margin-top: 0;"><strong><?php echo $langage_lbl['LBL_NOTE']; ?>: </strong><?php echo $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                                        <div class="nimot-class-but" style="margin-bottom: 0">
                                                            <button type="button" class="save" id="tPageDesc_btn"  style="margin-left: 0 !important" onclick="saveServiceType()"><?php echo $langage_lbl['LBL_Save']; ?></button>
                                                            <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal" onclick="resetToOriginalValue(this, 'vVehicleType_')">Cancel</button>
                                                        </div>
                                                    </div>

                                                    <div style="clear:both;"></div>
                                                </div>
                                            </div>

                                        </div>
                                        <div  class="modal fade" id="ServiceTypeDesc_Modal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                            <div class="modal-dialog modal-lg" >
                                                <div class="modal-content nimot-class">
                                                    <div class="modal-header">
                                                        <h4>
                                                            <span id="modal_action"></span> <?php echo $langage_lbl_admin['LBL_SERVICE_TXT']; ?> Description
                                                            <button type="button" class="close" data-dismiss="modal">x</button>
                                                        </h4>
                                                    </div>

                                                    <div class="modal-body">
                                                        <?php

                                                for ($i = 0; $i < $count_all; ++$i) {
                                                    $vCode = $db_master[$i]['vCode'];
                                                    $vTitle = $db_master[$i]['vTitle'];
                                                    $eDefault = $db_master[$i]['eDefault'];
                                                    $vValue = 'vVehicleType_'.$vCode;
                                                    $required = ('Yes' === $eDefault) ? 'required' : '';
                                                    $required_msg = ('Yes' === $eDefault) ? '<span class="red"> *</span>' : '';
                                                    $vValue_desc = 'tCategoryDesc_'.$vCode;
                                                    $lableName = 'tTypeDesc_'.$vCode;
                                                    ${$lableName} = $userEditDataArr[$lableName];
                                                    ?>


                                                                <?php if ('UberX' === $app_type_service && 'Provider' === $SERVICE_PROVIDER_FLOW) { ?>
                                                                <div class="row">
                                                                    <div class="col-lg-12">
                                                                        <label><?php echo $langage_lbl_admin['LBL_SERVICE_TXT'].' '.$langage_lbl_admin['LBL_DESCRIPTION']; ?> (<?php echo $vTitle; ?>)</label>

                                                                    </div>
                                                                    <div class="col-lg-12">
                                                                        <textarea class="form-control ckeditor" rows="10" name="<?php echo $lableName; ?>"  id="<?php echo $lableName; ?>"  placeholder="<?php echo $vTitle; ?> Value"> <?php echo ${$lableName}; ?></textarea>
                                                                        <div class="text-danger" id="<?php echo $lableName.'_error'; ?>" style="display: none;"><?php echo $langage_lbl_admin['LBL_REQUIRED']; ?></div>
                                                                    </div>
                                                                </div>
                                                            <?php }
                                                                }
                                            ?>
                                                    </div>
                                                    <div class="modal-footer" style="margin-top: 0">
                                                        <h5 class="text-left" style="margin-bottom: 15px; margin-top: 0;"><strong><?php echo $langage_lbl['LBL_NOTE']; ?>: </strong><?php echo $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                                        <div class="nimot-class-but" style="margin-bottom: 0">
                                                            <button type="button" class="save" id="tPageDesc_btn"  style="margin-left: 0 !important" onclick="saveDescWeb('tTypeDesc_', 'ServiceTypeDesc_Modal')"><?php echo $langage_lbl['LBL_Save']; ?></button>
                                                            <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal">Cancel</button>
                                                        </div>
                                                    </div>

                                                    <div style="clear:both;"></div>
                                                </div>
                                            </div>

                                        </div>

                                        <div  class="modal fade" id="tInfoText_Modal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                            <div class="modal-dialog modal-lg" >
                                                <div class="modal-content nimot-class">
                                                    <div class="modal-header">
                                                        <h4>
                                                            <span id="modal_action"></span> <label>Short Description</label>
                                                            <button type="button" class="close" data-dismiss="modal" onclick="resetToOriginalValue(this, 'tInfoText_')">x</button>
                                                        </h4>
                                                    </div>

                                                    <div class="modal-body">
                                                        <?php

                                                for ($i = 0; $i < $count_all; ++$i) {
                                                    $vCode = $db_master[$i]['vCode'];
                                                    $vLTitle = $db_master[$i]['vTitle'];
                                                    $eDefault = $db_master[$i]['eDefault'];

                                                    $tInfoText = 'tInfoText_'.$vCode;
                                                    ${$tInfoText} = $userEditDataArr[$tInfoText];

                                                    $page_title_class = 'col-lg-12';
                                                    if (count($db_master) > 1) {
                                                        if ($EN_available) {
                                                            if ('EN' === $vCode) {
                                                                $page_title_class = 'col-md-9 col-sm-9';
                                                            }
                                                        } else {
                                                            if ($vCode === $default_lang) {
                                                                $page_title_class = 'col-md-9 col-sm-9';
                                                            }
                                                        }
                                                    }
                                                    ?>

                                                                <div class="row">
                                                                    <div class="col-lg-12">
                                                                        <label>Short Description (<?php echo $vLTitle; ?>)</label>
                                                                    </div>
                                                                    <div class="<?php echo $page_title_class; ?> desc-block">
                                                                        <textarea class="form-control" name="<?php echo $tInfoText; ?>"  id="<?php echo $tInfoText; ?>" data-originalvalue="<?php echo ${$tInfoText}; ?>" placeholder="<?php echo $vLTitle; ?> Value"> <?php echo ${$tInfoText}; ?></textarea>
                                                                        <div class="text-danger" id="<?php echo $tInfoText.'_error'; ?>" style="display: none;"><?php echo $langage_lbl_admin['LBL_REQUIRED']; ?></div>
                                                                        <div class="desc_counter pull-right" style="margin-top: 5px">120/120</div>
                                                                        <div class="text-danger" id="<?php echo $tInfoText.'_error'; ?>" style="display: none;"><?php echo $langage_lbl_admin['LBL_REQUIRED']; ?></div>
                                                                    </div>
                                                                    <?php
                                                                if (count($db_master) > 1) {
                                                                    if ($EN_available) {
                                                                        if ('EN' === $vCode) { ?>
                                                                            <div class="col-md-3 col-sm-3">
                                                                                <button type ="button" name="allLanguage" id="allLanguage" class="btn btn-primary" onClick="getAllLanguageCode('tInfoText_', 'EN');" >Convert To All Language</button>
                                                                            </div>
                                                                        <?php }
                                                                        } else {
                                                                            if ($vCode === $default_lang) { ?>
                                                                            <div class="col-md-3 col-sm-3">
                                                                                <button type ="button" name="allLanguage" id="allLanguage" class="btn btn-primary" onClick="getAllLanguageCode('tInfoText_', '<?php echo $default_lang; ?>');" >Convert To All Language</button>
                                                                            </div>
                                                                        <?php }
                                                                            }
                                                                }
                                                    ?>
                                                                </div>
                                                            <?php
                                                }
                                            ?>
                                                    </div>
                                                    <div class="modal-footer" style="margin-top: 0">
                                                        <h5 class="text-left" style="margin-bottom: 15px; margin-top: 0;"><strong><?php echo $langage_lbl['LBL_NOTE']; ?>: </strong><?php echo $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                                        <div class="nimot-class-but" style="margin-bottom: 0">
                                                            <button type="button" class="save"  style="margin-left: 0 !important" onclick="saveVehicleDescInfo()"><?php echo $langage_lbl['LBL_Save']; ?></button>
                                                            <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal" onclick="resetToOriginalValue(this, 'tInfoText_')"><?php echo $langage_lbl['LBL_CANCEL_TXT']; ?></button>
                                                        </div>
                                                    </div>

                                                    <div style="clear:both;"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php } else { ?>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label><?php echo $langage_lbl_admin['LBL_SERVICE_TXT']; ?> Type <span class="red"> *</span></label>
                                            </div>
                                            <div class="col-md-6 col-sm-6">
                                                <input type="text" class="form-control" id="vVehicleType_<?php echo $default_lang; ?>" name="vVehicleType_<?php echo $default_lang; ?>" value="<?php echo $db_data[0]['vVehicleType_'.$default_lang]; ?>" required>
                                            </div>
                                        </div>
                                        <?php if ('UberX' === $app_type_service && 'Provider' === $SERVICE_PROVIDER_FLOW) { ?>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>Short Description <i class="icon-question-sign" data-placement="top" data-toggle="tooltip" data-original-title="This will be displayed on Service Detail screen"></i></label>
                                            </div>
                                            <div class="col-md-6 col-sm-6 desc-block">
                                                <textarea class="form-control" name="tInfoText_<?php echo $default_lang; ?>"  id="tInfoText_<?php echo $default_lang; ?>"> <?php echo $tInfoText['tInfoText_'.$default_lang]; ?></textarea>
                                                <div class="desc_counter pull-right" style="margin-top: 5px">120/120</div>
                                                <div class="text-danger" id="tInfoText_<?php echo $default_lang; ?>" style="display: none;"><?php echo $langage_lbl_admin['LBL_REQUIRED']; ?></div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label><?php echo $langage_lbl_admin['LBL_SERVICE_TXT'].' '.$langage_lbl_admin['LBL_DESCRIPTION']; ?></label>
                                            </div>
                                            <div class="col-md-6 col-sm-6">
                                                <textarea class="form-control ckeditor" rows="10" id="tTypeDesc_<?php echo $default_lang; ?>" name="tTypeDesc_<?php echo $default_lang; ?>"><?php echo $userEditDataArr['tTypeDesc_'.$default_lang]; ?></textarea>
                                            </div>
                                        </div>
                                        <?php } ?>
                                        <?php } ?>

                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>Select Location <span class="red"> *</span> <i class="icon-question-sign" data-placement="top" data-toggle="tooltip" data-original-title='Select the location in which you would like to provide this Services.
                                                    e.g.: "Car Washing" services to be provided for any specific city , state or country. You can manage these locations from "Manage Locations >> Geo Fence Location" section'></i></label>
                                            </div>
                                            <div class="col-md-6 col-sm-6">
                                                <select class="form-control" name = 'iLocationId' id="iLocationId" required="" onchange="changeCode_distance(this.value);">
                                                    <option value="">Select Location</option>
                                                    <option value="-1" <?php if ('-1' === $iLocationId) { ?>selected<?php } ?>>All</option>
                                                    <?php
                                            foreach ($db_location as $i => $row) {
                                                if (count($userObj->locations) > 0 && !in_array($row['iLocationId'], $userObj->locations, true)) {
                                                    continue;
                                                }
                                                ?>
                                                    <option value = "<?php echo $row['iLocationId']; ?>" <?php if ($iLocationId === $row['iLocationId']) { ?>selected<?php } ?>><?php echo $row['vLocationName']; ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                            <?php if ($userObj->hasPermission('view-geo-fence-locations')) { ?>
                                            <div class="col-md-6 col-sm-6">
                                                <a class="btn btn-primary" href="location.php" target="_blank">Enter New Location</a>
                                            </div>
                                            <?php } ?>
                                        </div>
                                        <?php if ('UberX' === $app_type_service) { ?>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label><?php echo $langage_lbl_admin['LBL_FARE_TYPE_TXT_ADMIN']; ?> <span class="red"> *</span> <i class="icon-question-sign" data-placement="top" data-toggle="tooltip" data-original-title="1. Fixed - Set this method for the fixed Charges of the services. 2. Hourly - Set this method if the service charges apply based on time spent. The timer will be used to calculate the service charge. 3. Time and Distance - Set this method where traveling time and distance needs to calculate for the service charge."></i></label>
                                            </div>
                                            <div class="col-md-6 col-sm-6">
                                                <select  class="form-control" name='eFareType' id="eFareType" required onchange="get_faretype(this.value)">
                                                    <option value="Fixed"<?php
                                                        if ('Fixed' === $eFareType) {
                                                            echo 'selected="selected"';
                                                        }
                                            ?>>Fixed</option>
                                                    <option value="Hourly"<?php
                                            if ('Hourly' === $eFareType) {
                                                echo 'selected="selected"';
                                            }
                                            ?>>Hourly</option>
                                                    <option value="Regular"<?php
                                            if ('Regular' === $eFareType) {
                                                echo 'selected="selected"';
                                            }
                                            ?>>Time And Distance</option>
                                                </select>
                                            </div>
                                        </div>
                                        <?php } else { ?>
                                        <input type="hidden" name="eFareType" value="Regular"/>
                                        <?php } ?>
                                        <div class="row" id="fixed_div" style="display:none;">
                                            <div class="col-lg-12">
                                                <label><?php echo $langage_lbl_admin['LBL_SERVICE_CHARGE']; ?><span class="red"> *</span></label>
                                            </div>
                                            <div class="col-md-6 col-sm-6">
                                                <input type="text" class="form-control" name="fFixedFare" placeholder="Enter Service Charge"  id="fFixedFare" value="<?php echo $fFixedFare; ?>" ><!-- onChange="getpriceCheck(this.value)" -->
                                            </div>
                                        </div>
                                        <div id="Regular_div1">
                                            <?php // if($APP_TYPE != 'UberX'){?>
                                            <div class="row" id="hide-km">
                                                <div class="col-lg-12">
                                                    <label> Price Per <em id="change_eUnit" style="font-style: normal"><?php echo $DEFAULT_DISTANCE_UNIT; ?></em>  (Price In <?php echo $db_currency[0]['vName']; ?>)<span class="red"> *</span></label>
                                                </div>
                                                <div class="col-md-6 col-sm-6">
                                                    <input type="text" class="form-control" name="fPricePerKM"  id="fPricePerKM" value="<?php echo $fPricePerKM; ?>" >  <!-- onChange="getpriceCheck_digit(this.value)" -->
                                                </div>
                                            </div>
                                            <?php // }?>
                                            <div class="row" id="hide-price">
                                                <div class="col-lg-12">
                                                    <label><?php echo $langage_lbl_admin['LBL_PRICE_MIN_TXT_ADMIN']; ?> (Price In <?php echo $db_currency[0]['vName']; ?>)<span class="red"> *</span></label>
                                                </div>
                                                <div class="col-md-6 col-sm-6">
                                                    <input type="text" class="form-control" name="fPricePerMin"  id="fPricePerMin" value="<?php echo $fPricePerMin; ?>" > <!-- onChange="getpriceCheck_digit(this.value)" -->
                                                </div>
                                            </div>
                                            <div id="hide-priceHour">
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <label>Service Charge Per Hour  (Price In <?php echo $db_currency[0]['vName']; ?>)<span class="red"> *</span></label>
                                                    </div>
                                                    <div class="col-md-6 col-sm-6">
                                                        <input type="text" class="form-control" name="fPricePerHour"  id="fPricePerHour" value="<?php echo $fPricePerHour; ?>">  <!--  onChange="getpriceCheck_digit(this.value)" -->
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <label>Minimum Hour<span class="red"> *</span></label>
                                                    </div>
                                                    <div class="col-md-6 col-sm-6">
                                                        <input type="number" class="form-control" name="fMinHour"  id="fMinHour" value="<?php echo $fMinHour; ?>" required>
                                                        <b>Note :</b> For hourly service, atleast Minimun Hour charges will be applied. Also, total service charges will be Service Charge Per Hour charge X Minimum Hour.
                                                    </div>
                                                </div>
                                                <!--Commented By HJ On 15-02-2019 As Per Discuss With KS Sir For Solved Bug - 6262<div class="row">
                                                    <div class="col-lg-12">
                                                        <label>Timeslot (in minutes) after min hour<span class="red"> *</span></label>
                                                    </div>
                                                    <div class="col-md-6 col-sm-6">
                                                        <input class="form-control" min="1" onkeypress="return isNumberKey(event)" type="text" name="fTimeSlot" id="fTimeSlot" required="" value="<?php echo $fTimeSlot; ?>">
                                                    <?php
                                            $timeslot = 60 / $fTimeSlot;
$price = $fPricePerHour / $timeslot;
?>
                                                        <input class="form-control" type="hidden" name="fTimeSlotPrice" value="<?php echo $price; ?>">
                                                    </div>
                                                    </div>-->
                                            </div>
                                            <?php // if($APP_TYPE != 'UberX'){?>
                                            <div class="row" id="hide-minimumfare">
                                                <div class="col-lg-12">
                                                    <label>Minimum Fare  (Price In <?php echo $db_currency[0]['vName']; ?>)<span class="red"> *</span> <i class="icon-question-sign" data-placement="top" data-toggle="tooltip" data-original-title='The minimum fare is the least amount you have to pay. For eg : if you travel a distance of 1 km  , the actual fare will be $10 (base fare $6 + $2/km + $2/min) assuming that it takes 1 min to travel but still you are liable to pay the minimum fare which is $15 for example.'></i></label>
                                                </div>
                                                <div class="col-md-6 col-sm-6">
                                                    <input type="text" class="form-control" name="iMinFare"  id="iMinFare" value="<?php echo $iMinFare; ?>" >
                                                    <!-- onChange="getpriceCheck_digit(this.value)" -->
                                                </div>
                                            </div>
                                            <div class="row" id="hide-basefare">
                                                <div class="col-lg-12">
                                                    <label> Base Fare  (Price In <?php echo $db_currency[0]['vName']; ?>)<span class="red"> *</span> <i class="icon-question-sign" data-placement="top" data-toggle="tooltip" data-original-title='Base fare is the price that the taxi meter will start at a certain point. Let say if you set base fare $3 then the meter will be set at $3 to begin, and not $0.'></i></label>
                                                </div>
                                                <div class="col-md-6 col-sm-6">
                                                    <input type="text" class="form-control" name="iBaseFare"  id="iBaseFare" value="<?php echo $iBaseFare; ?>" > <!-- onChange="getpriceCheck_digit(this.value)" -->
                                                </div>
                                            </div>
                                            <?php // }?>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label> Commision (%)<span class="red"> *</span> <i class="icon-question-sign" data-placement="top" data-toggle="tooltip" data-original-title='Enter Commission percentage you want to earn from this service.'></i></label>
                                            </div>
                                            <div class="col-md-6 col-sm-6">
                                                <input type="text" class="form-control" name="fCommision"  id="fCommision" value="<?php echo $fCommision; ?>" required >
                                            </div>
                                        </div>
                                        <?php if ('Provider' !== $SERVICE_PROVIDER_FLOW) { ?>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label> <?php echo $langage_lbl_admin['LBL_PASSANGER_TXT_ADMIN']; ?> Cancellation Time Limit ( in minute )<span class="red"></span> <i class="icon-question-sign" data-placement="top" data-toggle="tooltip" data-original-title="This is the timelimit based on which the <?php echo $langage_lbl_admin['LBL_PASSANGER_TXT_ADMIN']; ?> would be charged if he/she cancel's the ride after the specified period limit."></i></label>
                                            </div>
                                            <div class="col-md-6 col-sm-6">
                                                <input type="text" class="form-control" name="iCancellationTimeLimit"  id="iCancellationTimeLimit" value="<?php echo $iCancellationTimeLimit; ?>" >
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label> <?php echo $langage_lbl_admin['LBL_PASSANGER_TXT_ADMIN']; ?> Cancellation Charges  (Price In <?php echo $db_currency[0]['vName']; ?>)<span class="red"></span> <i class="icon-question-sign" data-placement="top" data-toggle="tooltip" data-original-title="Below mentioned charges would be applied to the <?php echo $langage_lbl_admin['LBL_PASSANGER_TXT_ADMIN']; ?>s when the <?php echo $langage_lbl_admin['LBL_PASSANGER_TXT_ADMIN']; ?> cancel's the ride after the specific period of time."></i></label>
                                            </div>
                                            <div class="col-md-6 col-sm-6">
                                                <input type="text" class="form-control" name="fCancellationFare"  id="fCancellationFare" value="<?php echo $fCancellationFare; ?>"> <!-- onchange="getpriceCheck_digit(this.value)" -->
                                                Note : Cancellation charges would only be applied if the mode of payment is CreditCard.
                                            </div>
                                        </div>
                                        <?php if ('UberX' === $Vehicle_type_name) { ?>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label> Waiting Time Limit ( in minute )<span class="red"></span> <i class="icon-question-sign" data-placement="top" data-toggle="tooltip" data-original-title="Waiting charge will be applied if duration exceeds than the defined.
                                                    e.g.: Let's say that the 'Waiting Time Limit' has set to 5 Minutes. From the app, the '<?php echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']; ?>' has marked as arrived and if the <?php echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']; ?> has to wait for 8 minutes which is more than 5 minutes(Waiting Time Limit) then in that case the <?php echo $langage_lbl_admin['LBL_PASSANGER_TXT_ADMIN']; ?> has to pay for the exceeded 3 minutes based on defined 'Waiting Charges' fees."></i></label>
                                            </div>
                                            <div class="col-md-6 col-sm-6">
                                                <input type="text" class="form-control" name="iWaitingFeeTimeLimit" min="1" onkeypress="return isNumberKey(event)"  id="iWaitingFeeTimeLimit" value="<?php echo $iWaitingFeeTimeLimit; ?>" >
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label> Waiting Charges  (Price In <?php echo $db_currency[0]['vName']; ?>)<span class="red"></span> <i class="icon-question-sign" data-placement="top" data-toggle="tooltip" data-original-title="The defined charges would be applied to the invoice into the total fare when the <?php echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']; ?> has to wait for more than the specific defined waiting time prior to starting the <?php echo $langage_lbl_admin['LBL_TRIP_TXT_ADMIN']; ?>"></i></label>
                                            </div>
                                            <div class="col-md-6 col-sm-6">
                                                <input type="text" class="form-control" name="fWaitingFees" min="1" onkeypress="return isNumberKey(event)"  id="fWaitingFees" value="<?php echo $fWaitingFees; ?>">
                                            </div>
                                        </div>
                                        <?php
                                        }
                                        }
?>
                                        <div id="Regular_div2">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label>Peak Time Surcharge On/Off <i class="icon-question-sign" data-placement="top" data-toggle="tooltip" data-original-title='This is a multiplier X  to the standard fares causing the fare to be higher than the standard fare during certain times the day; i.e. if X is 1.2 during some point of time then the standard fare will be multiplied by 1.2 to get the final fare.'></i></label>
                                                </div>
                                                <div class="col-md-6 col-sm-6">
                                                    <div class="make-switch" data-on="success" data-off="warning">
                                                        <input type="checkbox" id="ePickStatus" onChange="showhidepickuptime();" name="ePickStatus" <?php echo ('' !== $id && 'Active' === $ePickStatus) ? 'checked' : ''; ?>/>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="showpickuptime" style="display:none;">
                                                <div class="row">
                                                    <div class="col-lg-12 main-table001">
                                                        <div class="main-table001">
                                                            <table class="col-lg-2">
                                                                <tr>
                                                                    <td align="center"><b>Monday</b></td>
                                                                </tr>
                                                                <tr>
                                                                    <td> Start Time</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <div class="input-group clockpicker-with-callbacks">
                                                                            <input type="text" class="form-control" name="tMonPickStartTime"  id="tMonPickStartTime" value="<?php
                                    if ('00:00:00' !== $tMonPickStartTime) {
                                        echo $tMonPickStartTime;
                                    }
?>" placeholder="Pickup Start Time" readonly>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td> End Time</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <div class="input-group clockpicker-with-callbacks">
                                                                            <input type="text" class="form-control" name="tMonPickEndTime"  id="tMonPickEndTime" value="<?php
if ('00:00:00' !== $tMonPickEndTime) {
    echo $tMonPickEndTime;
}
?>" placeholder="Pickup End Time" readonly>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td> Price</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>  <input type="text" class="form-control" name="fMonPickUpPrice"  id="fMonPickUpPrice" value="<?php echo $fMonPickUpPrice; ?>" placeholder="Enter Price"></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><input type="button" name="RMonday" id="RMonday" value="reset" /></td>
                                                                </tr>
                                                            </table>
                                                            <table class="col-lg-2">
                                                                <tr>
                                                                    <td align="center"><b>Tuesday</b></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>  Start Time</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <div class="input-group clockpicker-with-callbacks">
                                                                            <input type="text" class="form-control" name="tTuePickStartTime"  id="tTuePickStartTime" value="<?php
if ('00:00:00' !== $tTuePickStartTime) {
    echo $tTuePickStartTime;
}
?>" placeholder="Pickup Start Time" readonly>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td> End Time</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <div class="input-group clockpicker-with-callbacks">
                                                                            <input type="text" class="form-control" name="tTuePickEndTime"  id="tTuePickEndTime" value="<?php
if ('00:00:00' !== $tTuePickEndTime) {
    echo $tTuePickEndTime;
}
?>" placeholder="Pickup End Time" readonly>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td> Price</td>
                                                                </tr>
                                                                <tr>
                                                                    <td> <input type="text" class="form-control" name="fTuePickUpPrice"  id="fTuePickUpPrice" value="<?php echo $fTuePickUpPrice; ?>" placeholder="Enter Price" ></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><input type="button" name="RTuesday" id="RTuesday" value="reset" /></td>
                                                                </tr>
                                                            </table>
                                                            <table class="col-lg-2">
                                                                <tr>
                                                                    <td align="center"><b>Wednesday</b></td>
                                                                </tr>
                                                                <tr>
                                                                    <td> Start Time</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <div class="input-group clockpicker-with-callbacks">
                                                                            <input type="text" class="form-control" name="tWedPickStartTime"  id="tWedPickStartTime" value="<?php
if ('00:00:00' !== $tWedPickStartTime) {
    echo $tWedPickStartTime;
}
?>" placeholder="Pickup Start Time" readonly>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td> End Time</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <div class="input-group clockpicker-with-callbacks">
                                                                            <input type="text" class="form-control" name="tWedPickEndTime"  id="tWedPickEndTime" value="<?php
if ('00:00:00' !== $tWedPickEndTime) {
    echo $tWedPickEndTime;
}
?>" placeholder="Pickup End Time" readonly>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td> Price</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><input type="text" class="form-control" name="fWedPickUpPrice"  id="fWedPickUpPrice" value="<?php echo $fWedPickUpPrice; ?>"  placeholder="Enter Price" ></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><input type="button" name="RWednesday" id="RWednesday" value="reset" /></td>
                                                                </tr>
                                                            </table>
                                                            <table class="col-lg-2">
                                                                <tr>
                                                                    <td align="center"><b>Thursday</b></td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Start Time</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <div class="input-group clockpicker-with-callbacks">
                                                                            <input type="text" class="form-control" name="tThuPickStartTime"  id="tThuPickStartTime" value="<?php
if ('00:00:00' !== $tThuPickStartTime) {
    echo $tThuPickStartTime;
}
?>" placeholder="Pickup Start Time" readonly>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td> End Time</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <div class="input-group clockpicker-with-callbacks">
                                                                            <input type="text" class="form-control" name="tThuPickEndTime"  id="tThuPickEndTime" value="<?php
if ('00:00:00' !== $tThuPickEndTime) {
    echo $tThuPickEndTime;
}
?>" placeholder="Pickup End Time" readonly>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td> Price</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><input type="text" class="form-control" name="fThuPickUpPrice"  id="fThuPickUpPrice" value="<?php echo $fThuPickUpPrice; ?>" placeholder="Enter Price" ></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><input type="button" name="RThursday" id="RThursday" value="reset" /></td>
                                                                </tr>
                                                            </table>
                                                            <table class="col-lg-2">
                                                                <tr>
                                                                    <td align="center"><b>Friday</b></td>
                                                                </tr>
                                                                <tr>
                                                                    <td> Start Time</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <div class="input-group clockpicker-with-callbacks">
                                                                            <input type="text" class="form-control" name="tFriPickStartTime"  id="tFriPickStartTime" value="<?php
if ('00:00:00' !== $tFriPickStartTime) {
    echo $tFriPickStartTime;
}
?>" placeholder="Pickup Start Time" readonly>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td> End Time</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <div class="input-group clockpicker-with-callbacks">
                                                                            <input type="text" class="form-control" name="tFriPickEndTime"  id="tFriPickEndTime" value="<?php
if ('00:00:00' !== $tFriPickEndTime) {
    echo $tFriPickEndTime;
}
?>" placeholder="Pickup End Time" readonly>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td> Price</td>
                                                                </tr>
                                                                <tr>
                                                                    <td> <input type="text" class="form-control" name="fFriPickUpPrice"  id="fFriPickUpPrice" value="<?php echo $fFriPickUpPrice; ?>" placeholder="Enter Price"  ></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><input type="button" name="RFriday" id="RFriday" value="reset" /></td>
                                                                </tr>
                                                            </table>
                                                            <table class="col-lg-2">
                                                                <tr>
                                                                    <td align="center"><b>Saturday</b></td>
                                                                </tr>
                                                                <tr>
                                                                    <td> Start Time</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <div class="input-group clockpicker-with-callbacks">
                                                                            <input type="text" class="form-control" name="tSatPickStartTime"  id="tSatPickStartTime" value="<?php
if ('00:00:00' !== $tSatPickStartTime) {
    echo $tSatPickStartTime;
}
?>" placeholder="Pickup Start Time" readonly>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td> End Time</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <div class="input-group clockpicker-with-callbacks">
                                                                            <input type="text" class="form-control" name="tSatPickEndTime"  id="tSatPickEndTime" value="<?php
if ('00:00:00' !== $tSatPickEndTime) {
    echo $tSatPickEndTime;
}
?>" placeholder="Pickup End Time" readonly>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td> Price</td>
                                                                </tr>
                                                                <tr>
                                                                    <td> <input type="text" class="form-control" name="fSatPickUpPrice"  id="fSatPickUpPrice" value="<?php echo $fSatPickUpPrice; ?>" placeholder="Enter Price" ></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><input type="button" name="RSaturday" id="RSaturday" value="reset" /></td>
                                                                </tr>
                                                            </table>
                                                            <table class="col-lg-2">
                                                                <tr>
                                                                    <td align="center"><b>Sunday</b></td>
                                                                </tr>
                                                                <tr>
                                                                    <td> Start Time</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <div class="input-group clockpicker-with-callbacks">
                                                                            <input type="text" class="form-control" name="tSunPickStartTime"  id="tSunPickStartTime" value="<?php
if ('00:00:00' !== $tSunPickStartTime) {
    echo $tSunPickStartTime;
}
?>" placeholder="Pickup Start Time" readonly>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td> End Time</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <div class="input-group clockpicker-with-callbacks">
                                                                            <input type="text" class="form-control" name="tSunPickEndTime"  id="tSunPickEndTime" value="<?php echo $tSunPickEndTime; ?>" placeholder="Pickup End Time" readonly>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td> Price</td>
                                                                </tr>
                                                                <tr>
                                                                    <td><input type="text" class="form-control" name="fSunPickUpPrice"  id="fSunPickUpPrice" value="<?php echo $fSunPickUpPrice; ?>" placeholder="Enter Price" ></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><input type="button" name="RSunday" id="RSunday" value="reset" /></td>
                                                                </tr>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label> Night Charges On/Off <i class="icon-question-sign" data-placement="top" data-toggle="tooltip" data-original-title='This is a multiplier X  to the standard fares causing the fare to be higher than the standard fare during night time; i.e. if X is 1.2 during some point of time then the standard fare will be multiplied by 1.2 to get the final fare.'></i></label>
                                                </div>
                                                <div class="col-md-6 col-sm-6">
                                                    <div class="make-switch" data-on="success" data-off="warning">
                                                        <input type="checkbox" id="eNightStatus" onChange="showhidenighttime();" name="eNightStatus" <?php echo ('' !== $id && 'Active' === $eNightStatus) ? 'checked' : ''; ?>/>
                                                    </div>
                                                </div>
                                            </div>
                                            <!--Added By Hasmukh On 10-10-2018 For Store Night Charges Details In Json Start !-->
                                            <div id="shownighttime" style="display:none;">
                                                <div class="row">
                                                    <div class="col-lg-12 main-table001">
                                                        <div class="main-table001">
                                                            <?php
                                                                foreach ($weekDaysArr as $dayKey => $dayVal) {
                                                                    $dayStartId = 't'.$dayVal.'NightStartTime';
                                                                    $dayEndId = 't'.$dayVal.'NightEndTime';
                                                                    $priceId = 'f'.$dayVal.'NightPrice';
                                                                    ?>
                                                            <table  class="col-lg-2">
                                                                <tr>
                                                                    <td align="center"><b><?php echo $dayKey; ?></b></td>
                                                                </tr>
                                                                <tr>
                                                                    <td> Start Time</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <div class="input-group clockpicker-with-callbacks">
                                                                            <input type="text" class="form-control" name="<?php echo $dayStartId; ?>"  id="<?php echo $dayStartId; ?>" value="<?php
                                                                                if (isset($nightSurgeDataArr[$dayStartId])) {
                                                                                    echo $nightSurgeDataArr[$dayStartId];
                                                                                }
                                                                    ?>" placeholder="Night Start Time" readonly>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td> End Time</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>
                                                                        <div class="input-group clockpicker-with-callbacks">
                                                                            <input type="text" class="form-control" name="<?php echo $dayEndId; ?>"  id="<?php echo $dayEndId; ?>" value="<?php
                                                                    if (isset($nightSurgeDataArr[$dayEndId])) {
                                                                        echo $nightSurgeDataArr[$dayEndId];
                                                                    }
                                                                    ?>" placeholder="Night End Time" readonly>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td> Price</td>
                                                                </tr>
                                                                <tr>
                                                                    <td>  <input type="text" class="form-control" name="<?php echo $priceId; ?>"  id="<?php echo $priceId; ?>" value="<?php
                                                                        if (isset($nightSurgeDataArr[$priceId])) {
                                                                            echo $nightSurgeDataArr[$priceId];
                                                                        }
                                                                    ?>" placeholder="Enter Price"  ></td>
                                                                </tr>
                                                                <tr>
                                                                    <td><input type="button" name="RNight<?php echo $dayKey; ?>" id="RNight<?php echo $dayKey; ?>" value="reset" /></td>
                                                                </tr>
                                                            </table>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!--Added By Hasmukh On 10-10-2018 For Store Night Charges Details In Json End !-->
                                            <!--<div id="shownighttime" style="display:none;">
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <label> Night Charges Start Time</label>
                                                    </div>
                                                    <div class="col-md-6 col-sm-6">
                                                        <input type="text" readonly class=" form-control" name="tNightStartTime"  id="tNightStartTime" value="<?php echo $tNightStartTime; ?>" placeholder="Select Night Start Time"  >
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <label> Night Charges End Time</label>
                                                    </div>
                                                    <div class="col-md-6 col-sm-6">
                                                        <input type="text" readonly class=" form-control" name="tNightEndTime"  id="tNightEndTime" value="<?php echo $tNightEndTime; ?>" placeholder="Select Night End Time" >
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <label> Night Time Surcharge (X)</label>
                                                    </div>
                                                    <div class="col-md-6 col-sm-6">
                                                        <input type="text" class="form-control" name="fNightPrice"  id="fNightPrice" value="<?php echo $fNightPrice; ?>" placeholder="Enter Price" >

                                                </div>
                                                </div>
                                                </div> -->
                                            <?php // }?>
                                        </div>
                                        <?php if ('UberX' !== $app_type_service) { ?>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label><?php echo $langage_lbl_admin['LBL_SERVICE_TXT']; ?> Type Picture (Gray image) <i class="icon-question-sign" data-placement="top" data-toggle="tooltip" data-original-title='This is used to represent the vehicle type as a icon in application.'></i></label>
                                            </div>
                                            <div class="col-md-6 col-sm-6">
                                                <?php
                                                    $rand = random_int(1_000, 9_999);
                                            if ('' !== $vLogo) {
                                                ?>
                                                <img src="<?php echo $tconfig['tsite_upload_images_vehicle_type'].'/'.$id.'/ios/3x_'.$vLogo."?dm={$rand}"; ?>" style="width:100px;height:100px;">
                                                <?php } ?>
                                                <input type="file" class="form-control" name="vLogo" <?php echo $required_rule; ?> id="vLogo" placeholder="" style="padding-bottom: 4%; height:5%;">
                                                <br/>
                                                [Note: Upload only png image size of 360px*360px.]
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label><?php echo $langage_lbl_admin['LBL_SERVICE_TXT']; ?> Type Picture (Orange image) <i class="icon-question-sign" data-placement="top" data-toggle="tooltip" data-original-title='This is used to represent the vehicle type as a icon in application. Oragen icon is used to represent the vehicle type as a selected.'></i></label>
                                            </div>
                                            <div class="col-md-6 col-sm-6">
                                                <?php if ('' !== $vLogo1) { ?>
                                                <img src="<?php echo $tconfig['tsite_upload_images_vehicle_type'].'/'.$id.'/ios/3x_hover_'.$vLogo1."?dm={$rand}"; ?>" style="width:100px;height:100px;">
                                                <?php } ?>
                                                <input type="file" class="form-control" name="vLogo1" <?php echo $required_rule; ?> id="vLogo1" placeholder="" style="padding-bottom: 4%; height: 5%;">
                                                <br/>
                                                [Note: Upload only png image size of 360px*360px.]
                                            </div>
                                        </div>
                                        <?php } ?>
                                        <?php if ('UberX' === $app_type_service) { ?>
                                        <div id="show-in-fixed">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <label>Allow Quantity <span class="red"> *</span></label>
                                                </div>
                                                <div class="col-md-6 col-sm-6">
                                                    <select  class="form-control" name='eAllowQty' id="AllowQty" onchange="get_AllowQty(this.value)">
                                                        <option value="Yes"<?php
                                                    if ('Yes' === $eAllowQty) {
                                                        echo 'selected="selected"';
                                                    }
                                            ?>>Yes</option>
                                                        <option value="No"<?php
                                            if ('No' === $eAllowQty) {
                                                echo 'selected="selected"';
                                            }
                                            ?>>No</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="row" id="iMaxQty-div">
                                                <div class="col-lg-12">
                                                    <label>Maximum Quantity<span class="red"> *</span></label>
                                                </div>
                                                <div class="col-md-6 col-sm-6">
                                                    <input type="text" class="form-control" name="iMaxQty"  id="iMaxQty" value="<?php echo $iMaxQty; ?>"  onchange="getpriceCheck(this.value)" >
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <label>Display Order</label>
                                            </div>
                                            <div class="col-md-6 col-sm-6">
                                                <input type="hidden" name="temp_order" id="temp_order" value="<?php echo ('Edit' === $action) ? $iDisplayOrder_db : '1'; ?>">
                                                <?php
                                                    $display_numbers = ('Add' === $action) ? $iDisplayOrder_max : $iDisplayOrder;
                                            ?>
                                                <select name="iDisplayOrder" class="form-control">
                                                    <?php for ($i = 1; $i <= $display_numbers; ++$i) { ?>
                                                    <option value="<?php echo $i; ?>" <?php
                                                if ($i === $iDisplayOrder_db) {
                                                    echo 'selected';
                                                }
                                                        ?>> -- <?php echo $i; ?> --</option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <?php } ?>
                                        <div id="price" style="margin: 10px;"></div>
                                        <br/>
                                        <div class="col-lg-12">
                                            <?php if (('Edit' === $action && $userObj->hasPermission('edit-service-type')) || ('Add' === $action && $userObj->hasPermission('create-service-type'))) { ?>
                                            <input type="submit" class="btn btn-default" name="btnsubmit" id="btnsubmit"  value="<?php if ('Add' === $action) { ?><?php echo $action; ?> Service Type<?php } else { ?>Update<?php } ?>">
                                            <input type="reset" value="Reset" class="btn btn-default">
                                            <?php } ?>
                                            <!-- <a href="javascript:void(0);" onclick="reset_form('_vehicleType_form');" class="btn btn-default">Reset</a> -->
                                            <a href="service_type.php" class="btn btn-default back_link">Cancel</a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div style="clear:both;"></div>
                        </div>
                    </div>
                    <!--END PAGE CONTENT -->
                </div>
                <!--END MAIN WRAPPER -->
                <div class="row loding-action" id="loaderIcon" style="display:none;">
                    <div align="center">
                        <img src="default.gif">
                        <span>Language Translation is in Process. Please Wait...</span>
                    </div>
                </div>
                <?php include_once 'footer.php'; ?>
                <script type="text/javascript" src="js/validation/jquery.validate.min.js" ></script>
                <script type="text/javascript" src="js/validation/additional-methods.min.js" ></script>
                <script src="../assets/plugins/switch/static/js/bootstrap-switch.min.js"></script>
                <link rel="stylesheet" type="text/css" media="screen" href="css/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css">
                <script type="text/javascript" src="js/moment.min.js"></script>
                <script type="text/javascript" src="js/bootstrap-datetimepicker.min.js"></script>
                <!--Added By Hasmukh On 11-10-2018 For Clock Time Picker Start Js-->
                <script type="text/javascript" src="js/bootstrap-clockpicker.min.js"></script>
                <!--Added By Hasmukh On 11-10-2018 For Clock Time Picker End Js -->
                <!--For Faretype-->
                <script src="../assets/plugins/ckeditor/ckeditor.js"></script>
                <script src="../assets/plugins/ckeditor/config.js"></script>
                <script>
                    document.getElementById('RMonday').onclick = function () {
                        var tMonPickStartTime = document.getElementById('tMonPickStartTime');
                        var tMonPickEndTime = document.getElementById('tMonPickEndTime');
                        var fMonPickUpPrice = document.getElementById('fMonPickUpPrice');

                        tMonPickStartTime.value = tMonPickEndTime.value = fMonPickUpPrice.value = '';
                        //fMonPickUpPrice.value= fMonPickUpPrice.defaultValue;
                    };

                    document.getElementById('RTuesday').onclick = function () {
                        var tTuePickStartTime = document.getElementById('tTuePickStartTime');
                        var tTuePickEndTime = document.getElementById('tTuePickEndTime');
                        var fTuePickUpPrice = document.getElementById('fTuePickUpPrice');

                        tTuePickStartTime.value = tTuePickEndTime.value = fTuePickUpPrice.value = '';
                        //fTuePickUpPrice.value= fTuePickUpPrice.defaultValue;
                    };

                    document.getElementById('RWednesday').onclick = function () {
                        var tWedPickStartTime = document.getElementById('tWedPickStartTime');
                        var tWedPickEndTime = document.getElementById('tWedPickEndTime');
                        var fWedPickUpPrice = document.getElementById('fWedPickUpPrice');

                        tWedPickStartTime.value = tWedPickEndTime.value = fWedPickUpPrice.value = '';
                        //fWedPickUpPrice.value= fWedPickUpPrice.defaultValue;
                    };

                    document.getElementById('RThursday').onclick = function () {
                        var tThuPickStartTime = document.getElementById('tThuPickStartTime');
                        var tThuPickEndTime = document.getElementById('tThuPickEndTime');
                        var fThuPickUpPrice = document.getElementById('fThuPickUpPrice');

                        tThuPickStartTime.value = tThuPickEndTime.value = fThuPickUpPrice.value = '';
                        //fThuPickUpPrice.value= fThuPickUpPrice.defaultValue;
                    };


                    document.getElementById('RFriday').onclick = function () {
                        var tFriPickStartTime = document.getElementById('tFriPickStartTime');
                        var tFriPickEndTime = document.getElementById('tFriPickEndTime');
                        var fFriPickUpPrice = document.getElementById('fFriPickUpPrice');

                        tFriPickStartTime.value = tFriPickEndTime.value = fFriPickUpPrice.value = '';
                        //fFriPickUpPrice.value= fFriPickUpPrice.defaultValue;
                    };

                    document.getElementById('RSaturday').onclick = function () {
                        var tSatPickStartTime = document.getElementById('tSatPickStartTime');
                        var tSatPickEndTime = document.getElementById('tSatPickEndTime');
                        var fSatPickUpPrice = document.getElementById('fSatPickUpPrice');

                        tSatPickStartTime.value = tSatPickEndTime.value = fSatPickUpPrice.value = '';
                        //fSatPickUpPrice.value= fSatPickUpPrice.defaultValue;
                    };

                    document.getElementById('RSunday').onclick = function () {
                        var tSunPickStartTime = document.getElementById('tSunPickStartTime');
                        var tSunPickEndTime = document.getElementById('tSunPickEndTime');
                        var fSunPickUpPrice = document.getElementById('fSunPickUpPrice');

                        tSunPickStartTime.value = tSunPickEndTime.value = fSunPickUpPrice.value = '';
                        //fSunPickUpPrice.value= fSunPickUpPrice.defaultValue;
                    };
                    // just for the demos, avoids form submit
                    if (_system_script == 'ServiceType') {
                        if ($('#_vehicleType_form').length !== 0) {
                            $("#_vehicleType_form").validate({
                                rules: {
                                    fPricePerKM: {
                                        number: true,
                                        min: 0
                                    },
                                    fPricePerMin: {
                                        number: true,
                                        min: 0
                                    },
                                    fPricePerHour: {
                                        number: true,
                                        min: 1
                                    },
                                    fFixedFare: {
                                        number: true,
                                        min: 0.1
                                    },
                                    fMinHour: {
                                        number: true,
                                        min: 1
                                    },
                                    iMinFare: {
                                        number: true,
                                        min: 0
                                    },
                                    iBaseFare: {
                                        number: true,
                                        min: 0
                                    },
                                    fCommision: {
                                        number: true,
                                        min: 0
                                    },
                                    iCancellationTimeLimit: {
                                        number: true,
                                        min: 1
                                    },
                                    fCancellationFare: {
                                        number: true,
                                        min: 1
                                    },
                                    iWaitingFeeTimeLimit: {
                                        number: true,
                                        min: 1
                                    },
                                    fWaitingFees: {
                                        number: true,
                                        min: 1
                                    },
                                    fMonPickUpPrice: {
                                        required: function () {
                                            var tMonPickStartTime = $("#tMonPickStartTime").val();
                                            var tMonPickEndTime = $("#tMonPickEndTime").val();
                                            if (tMonPickStartTime != '' && tMonPickEndTime != '') {
                                                return true;
                                            } else {
                                                return false;
                                            }
                                        },
                                        number: true,
                                        minStrict: 1
                                    },
                                    fTuePickUpPrice: {
                                        required: function () {
                                            var tTuePickStartTime = $("#tTuePickStartTime").val();
                                            var tTuePickEndTime = $("#tTuePickEndTime").val();
                                            if (tTuePickStartTime != '' && tTuePickEndTime != '') {
                                                return true;
                                            } else {
                                                return false;
                                            }
                                        },
                                        number: true,
                                        minStrict: 1
                                    },
                                    fWedPickUpPrice: {
                                        required: function () {
                                            var tWedPickStartTime = $("#tWedPickStartTime").val();
                                            var tWedPickEndTime = $("#tWedPickEndTime").val();
                                            if (tWedPickStartTime != '' && tWedPickEndTime != '') {
                                                return true;
                                            } else {
                                                return false;
                                            }
                                        },
                                        number: true,
                                        minStrict: 1
                                    },
                                    fThuPickUpPrice: {
                                        required: function () {
                                            var tThuPickStartTime = $("#tThuPickStartTime").val();
                                            var tThuPickEndTime = $("#tThuPickEndTime").val();
                                            if (tThuPickStartTime != '' && tThuPickEndTime != '') {
                                                return true;
                                            } else {
                                                return false;
                                            }
                                        },
                                        number: true,
                                        minStrict: 1
                                    },
                                    fFriPickUpPrice: {
                                        required: function () {
                                            var tFriPickStartTime = $("#tFriPickStartTime").val();
                                            var tFriPickEndTime = $("#tFriPickEndTime").val();
                                            if (tFriPickStartTime != '' && tFriPickEndTime != '') {
                                                return true;
                                            } else {
                                                return false;
                                            }
                                        },
                                        number: true,
                                        minStrict: 1
                                    },
                                    fSatPickUpPrice: {
                                        required: function () {
                                            var tSatPickStartTime = $("#tSatPickStartTime").val();
                                            var tSatPickEndTime = $("#tSatPickEndTime").val();
                                            if (tSatPickStartTime != '' && tSatPickEndTime != '') {
                                                return true;
                                            } else {
                                                return false;
                                            }
                                        },
                                        number: true,
                                        minStrict: 1
                                    },
                                    fSunPickUpPrice: {
                                        required: function () {
                                            var tSunPickStartTime = $("#tSunPickStartTime").val();
                                            var tSunPickEndTime = $("#tSunPickEndTime").val();
                                            if (tSunPickStartTime != '' && tSunPickEndTime != '') {
                                                return true;
                                            } else {
                                                return false;
                                            }
                                        },
                                        number: true,
                                        minStrict: 1
                                    },
                                    fMonNightPrice: {
                                        required: function () {
                                            var tMonNightStartTime = $("#tMonNightStartTime").val();
                                            var tMonNightEndTime = $("#tMonNightEndTime").val();
                                            if (tMonNightStartTime != '' && tMonNightEndTime != '') {
                                                return true;
                                            } else {
                                                return false;
                                            }
                                        },
                                        number: true,
                                        minStrict: 1
                                    },
                                    fTueNightPrice: {
                                        required: function () {
                                            var tTueNightStartTime = $("#tTueNightStartTime").val();
                                            var tTueNightEndTime = $("#tTueNightEndTime").val();
                                            if (tTueNightStartTime != '' && tTueNightEndTime != '') {
                                                return true;
                                            } else {
                                                return false;
                                            }
                                        },
                                        number: true,
                                        minStrict: 1
                                    },
                                    fWedNightPrice: {
                                        required: function () {
                                            var tWedNightStartTime = $("#tWedNightStartTime").val();
                                            var tWedNightEndTime = $("#tWedNightEndTime").val();
                                            if (tWedNightStartTime != '' && tWedNightEndTime != '') {
                                                return true;
                                            } else {
                                                return false;
                                            }
                                        },
                                        number: true,
                                        minStrict: 1
                                    },
                                    fThuNightPrice: {
                                        required: function () {
                                            var tThuNightStartTime = $("#tThuNightStartTime").val();
                                            var tThuNightEndTime = $("#tThuNightEndTime").val();
                                            if (tThuNightStartTime != '' && tThuNightEndTime != '') {
                                                return true;
                                            } else {
                                                return false;
                                            }
                                        },
                                        number: true,
                                        minStrict: 1
                                    },
                                    fFriNightPrice: {
                                        required: function () {
                                            var tFriNightStartTime = $("#tFriNightStartTime").val();
                                            var tFriNightEndTime = $("#tFriNightEndTime").val();
                                            if (tFriNightStartTime != '' && tFriNightEndTime != '') {
                                                return true;
                                            } else {
                                                return false;
                                            }
                                        },
                                        number: true,
                                        minStrict: 1
                                    },
                                    fSatNightPrice: {
                                        required: function () {
                                            var tSatNightStartTime = $("#tSatNightStartTime").val();
                                            var tSatNightEndTime = $("#tSatNightEndTime").val();
                                            if (tSatNightStartTime != '' && tSatNightEndTime != '') {
                                                return true;
                                            } else {
                                                return false;
                                            }
                                        },
                                        number: true,
                                        minStrict: 1
                                    },
                                    fSunNightPrice: {
                                        required: function () {
                                            var tSunNightStartTime = $("#tSunNightStartTime").val();
                                            var tSunNightEndTime = $("#tSunNightEndTime").val();
                                            if (tSunNightStartTime != '' && tSunNightEndTime != '') {
                                                return true;
                                            } else {
                                                return false;
                                            }
                                        },
                                        number: true,
                                        minStrict: 1
                                    }
                                }
                            });
                        }
                    }
                    jQuery.extend(jQuery.validator.messages, {
                        number: "Please enter a valid number.",
                        min: jQuery.validator.format("Value must be greater than 0.")
                    });
                </script>
                <script>
                    $('[data-toggle="tooltip"]').tooltip();
                    window.onload = function () {

                        var vid = $("#vid").val();
                        var eFareType = $("#eFareType").val();
                        var AllowQty = $("#AllowQty").val();
                        if (vid == '')
                        {
                            get_faretype('Regular');
                        } else
                        {
                            get_faretype(eFareType);
                        }

                        if (AllowQty == 'Yes') {
                            $("#iMaxQty-div").show();
                            $("#iMaxQty").attr('required', 'required');
                        } else {
                            $("#iMaxQty-div").hide();
                            $("#iMaxQty").removeAttr('required');

                        }
                        var AppTypenew = '<?php echo $APP_TYPE; ?>';
                        if (AppTypenew == 'Ride-Delivery-UberX') {
                            var appTYpe = '<?php echo $app_type_service; ?>';
                        } else {
                            var appTYpe = '<?php echo $APP_TYPE; ?>';
                        }
                        /*appTYpe == 'UberX' && eFareType == 'Regular'*/
                        if (appTYpe == 'UberX' && eFareType == 'Regular') {
                            $("#Regular_div2").show();
                            $("#Regular_div1").show();

                        } else if (appTYpe == 'Ride' || appTYpe == 'Delivery' || appTYpe == 'Ride-Delivery') {
                            $("#Regular_div2").show();
                            $("#Regular_div1").show();

                        } else {
                            $("#Regular_div2").hide();
                            $("#Regular_div1").show();

                        }

                        if (appTYpe == 'Delivery') {
                            $("#Regular_subdiv").hide();
                        } else if (appTYpe == 'Ride-Delivery') {
                            $('#etypedelivery').on('change', function () {
                                eTypedeliver = this.value;
                                if (eTypedeliver == 'Deliver') {
                                    $("#Regular_subdiv").hide();
                                } else {
                                    $("#Regular_subdiv").show();
                                }
                            });
                        } else {
                            $("#Regular_subdiv").show();
                        }
                    };
                    var successMSG1 = '<?php echo $success; ?>';

                    if (successMSG1 != '') {
                        setTimeout(function () {
                            $(".msgs_hide").hide(1000)
                        }, 5000);
                    }

                    function get_faretype(val) {
                        var AppTypenew = '<?php echo $APP_TYPE; ?>';
                        if (AppTypenew == 'Ride-Delivery-UberX') {
                            var appTYpe = '<?php echo $app_type_service; ?>';
                        } else {
                            var appTYpe = '<?php echo $APP_TYPE; ?>';
                        }
                        //var appTYpe = '<?php echo $APP_TYPE; ?>';
                        if (appTYpe == 'UberX') {
                            if (val == "Fixed") {
                                $("#fixed_div").show();
                                $("#Regular_div1").hide();
                                $("#Regular_div2").hide();
                                $("#hide-priceHour").hide();
                                $("#hide-basefare").hide();
                                $("#hide-minimumfare").hide();
                                $("#hide-price").hide();
                                $("#hide-km").hide();
                                $("#show-in-fixed").show();
                                $("#fFixedFare").attr('required', 'required');
                                $("#iMaxQty").attr('required', 'required');
                                $("#fPricePerKM").removeAttr('required');
                                $("#fPricePerMin").removeAttr('required');
                                $("#iBaseFare").removeAttr('required');
                                $("#fPickUpPrice").removeAttr('required');
                                $("#tPickStartTime").removeAttr('required');
                                $("#tPickEndTime").removeAttr('required');
                                $("#tNightStartTime").removeAttr('required');
                                $("#tNightEndTime").removeAttr('required');
                                $("#fPricePerHour").removeAttr('required');
                                $("#fMinHour").removeAttr('required');
                                $("#fTimeSlot").removeAttr('required');
                                $("#iMinFare").removeAttr('required');
                                //$("#fVisitFee_div").show();
                                //$("#fVisitFee").attr('required', 'required');
                            } else if (val == "Regular") {
                                $("#fixed_div").hide();
                                $("#Regular_div2").show();
                                $("#Regular_div1").show();
                                $("#show-in-fixed").hide();
                                $("#hide-priceHour").hide();
                                $("#hide-km").show();
                                $("#hide-basefare").show();
                                $("#hide-minimumfare").show();
                                $("#hide-price").show();
                                $("#fPricePerHour").removeAttr('required');
                                $("#fMinHour").removeAttr('required');
                                $("#fTimeSlot").removeAttr('required');
                                $("#iMaxQty").removeAttr('required');
                                $("#fFixedFare").removeAttr('required');
                                $("#fPricePerKM").attr('required', 'required');
                                $("#iMinFare").attr('required', 'required');
                                $("#fPricePerMin").attr('required', 'required');
                                $("#iBaseFare").attr('required', 'required');
                                $("#fPickUpPrice").attr('required', 'required');
                                $("#tPickStartTime").attr('required', 'required');
                                $("#tPickEndTime").attr('required', 'required');
                                $("#tNightStartTime").attr('required', 'required');
                                $("#tNightEndTime").attr('required', 'required');
                                //$("#fVisitFee_div").hide();
                                //$("#fVisitFee").removeAttr('required');
                            } else {
                                $("#fixed_div").hide();
                                $("#Regular_div1").show();
                                $("#Regular_div2").hide();
                                $("#hide-basefare").hide();
                                $("#hide-minimumfare").hide();
                                $("#hide-price").hide();
                                $("#hide-km").hide();
                                $("#hide-priceHour").show();
                                $("#show-in-fixed").hide();
                                $("#fFixedFare").removeAttr('required');
                                $("#iMaxQty").removeAttr('required');
                                $("#iMinFare").removeAttr('required');
                                $("#fPricePerHour").attr('required', 'required');
                                $("#fMinHour").attr('required', 'required');
                                $("#fTimeSlot").attr('required', 'required');
                                //$("#fVisitFee_div").hide();
                                //$("#fVisitFee").removeAttr('required');
                                /* $("#fPricePerKM").attr('required','required');
                                 $("#fPricePerMin").attr('required','required');
                                 $("#iBaseFare").attr('required','required');
                                 $("#fPickUpPrice").attr('required','required');
                                 $("#tPickStartTime").attr('required','required');
                                 $("#tPickEndTime").attr('required','required');
                                 $("#tNightStartTime").attr('required','required');
                                 $("#tNightEndTime").attr('required','required'); */

                                $("#iBaseFare").removeAttr('required');
                                $("#fPricePerKM").removeAttr('required');
                                $("#fPricePerMin").removeAttr('required');
                                $("#fPickUpPrice").removeAttr('required');
                                $("#tPickStartTime").removeAttr('required');
                                $("#tPickEndTime").removeAttr('required');
                                $("#tNightStartTime").removeAttr('required');
                                $("#tNightEndTime").removeAttr('required');
                            }
                        } else {
                            $("#Regular_div1").show();
                            $("#Regular_div2").show();
                            $("#fFixedFare").hide();
                            $("#show-in-fixed").hide();
                            $("#hide-priceHour").hide();
                            $("#fFixedFare").removeAttr('required');
                            $("#iMaxQty").removeAttr('required');
                            $("#fPricePerHour").removeAttr('required');
                            $("#fMinHour").removeAttr('required');
                            $("#fTimeSlot").removeAttr('required');
                            $("#fPricePerKM").attr('required', 'required');
                            $("#iMinFare").attr('required', 'required');
                            $("#fPricePerMin").attr('required', 'required');
                            $("#iBaseFare").attr('required', 'required');
                            $("#fPickUpPrice").attr('required', 'required');
                            $("#tPickStartTime").attr('required', 'required');
                            $("#tPickEndTime").attr('required', 'required');
                            /*                  $("#tNightStartTime").attr('required', 'required');
                             $("#tNightEndTime").attr('required', 'required');*/
                        }
                    }
                    function get_AllowQty(val) {
                        if (val == "Yes") {
                            $("#iMaxQty-div").show();
                            $("#iMaxQty").attr('required', 'required');
                        } else {
                            $("#iMaxQty-div").hide();
                            $("#iMaxQty").removeAttr('required');
                        }
                    }
                </script>
                <!--For Faretype End-->
                <script>
                    function changeCode(id)
                    {
                        var ajaxData = {
                            'URL': '<?php echo $tconfig['tsite_url_main_admin']; ?>change_code.php',
                            'AJAX_DATA': 'id=' + id,
                        };
                        getDataFromAjaxCall(ajaxData, function(response) {
                            if(response.action == "1") {
                                var data = response.result;
                                document.getElementById("code").value = data;
                            }
                            else {
                                console.log(response.result);
                            }
                        });
                    }


                    function getpriceCheck(id)
                    {
                        if (id > 0)
                        {
                            $('#price').html('');
                            $('input[type="submit"]').removeAttr('disabled');
                        } else
                        {
                            $('#price').html('<i class="alert-danger alert"> You can not enter any price as Zero or Letter.</i>');
                            $('input[type="submit"]').attr('disabled', 'disabled');
                        }
                    }

                    function getpriceCheck_digit(id)
                    {
                        var check = isNaN(id);
                        if (check === false)
                        {
                            $('#price').html('');
                            $('input[type="submit"]').removeAttr('disabled');
                        } else {
                            $('#price').html('<i class="alert-danger alert"> You can not enter any price as Zero or Letter.</i>');
                            $('input[type="submit"]').attr('disabled', 'disabled');
                        }
                    }
                    function onlydigit(id)
                    {
                        var digi = /^[1-9]{1}$/;
                        result = digi.test(id);
                        if (result == true)
                        {
                            $('#digit').html('');
                            $('input[type="submit"]').removeAttr('disabled');
                        } else
                        {
                            $('#digit').html('<i class="alert-danger alert">Only Decimal Number less Than 10</i>');
                            $('input[type="submit"]').attr('disabled', 'disabled');
                        }
                    }
                    $(function () {
                        newDate = new Date('Y-M-D');
                        $('#tNightStartTime').datetimepicker({
                            format: 'HH:mm:ss',
                            //minDate: moment().format('l'),
                            ignoreReadonly: true,
                            //sideBySide: true,
                        });
                        $('#tNightEndTime').datetimepicker({
                            format: 'HH:mm:ss',
                            //minDate: moment().format('l'),
                            ignoreReadonly: true,
                            useCurrent: false
                                    //sideBySide: true,
                        });
                    });

                    function showhidepickuptime() {
                        if ($('input[name=ePickStatus]').is(':checked')) {
                            //alert('Checked');
                            $("#showpickuptime").show();
                        } else {
                            //alert('Not checked');
                            $("#showpickuptime").hide();
                        }
                    }

                    function showhidenighttime() {
                        if ($('input[name=eNightStatus]').is(':checked')) {
                            $("#shownighttime").show();
                            $("#tNightStartTime").attr('required');
                            $("#tNightEndTime").attr('required');
                        } else {
                            //alert('Not checked');
                            $("#shownighttime").hide();
                            $("#tNightStartTime").removeAttr('required');
                            $("#tNightEndTime").removeAttr('required');
                        }
                    }

                    function setCity(id, selected)
                    {
                        var ajaxData = {
                            'URL': '<?php echo $tconfig['tsite_url_main_admin']; ?>change_stateCity.php',
                            'AJAX_DATA': {stateId: id, selected: selected},
                        };
                        getDataFromAjaxCall(ajaxData, function(response) {
                            if(response.action == "1") {
                                var dataHtml = response.result;
                                $("#iCityId").html(dataHtml);
                            }
                            else {
                                console.log(response.result);
                            }
                        });
                    }

                    function setState(id, selected)
                    {
                        var ajaxData = {
                            'URL': '<?php echo $tconfig['tsite_url_main_admin']; ?>change_stateCity.php',
                            'AJAX_DATA': {countryId: id, selected: selected},
                        };
                        getDataFromAjaxCall(ajaxData, function(response) {
                            if(response.action == "1") {
                                var dataHtml = response.result;
                                $("#iStateId").html(dataHtml);
                                if (selected == '')
                                    setCity('', selected);
                            }
                            else {
                                console.log(response.result);
                            }
                        });
                        changeCode(id);
                    }

                    function changeCode(id) {
                        var ajaxData = {
                            'URL': '<?php echo $tconfig['tsite_url_main_admin']; ?>change_code.php',
                            'AJAX_DATA': {id: id, eUnit: 'yes'},
                            'REQUEST_DATA_TYPE': 'json'
                        };
                        getDataFromAjaxCall(ajaxData, function(response) {
                            if(response.action == "1") {
                                var dataHTML2 = response.result;
                                if (dataHTML2 != null)
                                    $("#change_eUnit").text(dataHTML2.eUnit);
                            }
                            else {
                                console.log(response.result);
                            }
                        });
                    }

                    function changeCode_distance(id) {
                        var ajaxData = {
                            'URL': '<?php echo $tconfig['tsite_url_main_admin']; ?>ajax_get_unit.php',
                            'AJAX_DATA': {id: id},
                        };
                        getDataFromAjaxCall(ajaxData, function(response) {
                            if(response.action == "1") {
                                var dataHTML2 = response.result;
                                if (dataHTML2 != null)
                                    $("#change_eUnit").text(dataHTML2);
                            }
                            else {
                                console.log(response.result);
                            }
                        });
                    }

                    setState('<?php echo $iCountryId; ?>', '<?php echo $iStateId; ?>');
                    setCity('<?php echo $iStateId; ?>', '<?php echo $iCityId; ?>');
                    showhidepickuptime();
                    showhidenighttime();

                    changeCode_distance('<?php echo $iLocationId; ?>');
                </script>
                <script type="text/javascript" language="javascript">

                      var myVar;
                      $(document).ready(function () {
                          var referrer;
                          if ($("#previousLink").val() == "") {
                              referrer = document.referrer;
                          } else {
                              referrer = $("#previousLink").val();
                          }
                          if (referrer == "") {
                              referrer = "service_type.php";
                          } else {
                              $("#backlink").val(referrer);
                          }
                          $(".back_link").attr('href', referrer);
                      });

                      var iVehicleCategoryId = '<?php echo $iVehicleCategoryId; ?>';
                      if (iVehicleCategoryId != '') {
                          getordering(iVehicleCategoryId);
                      }
                      function getordering(iVehicleCategoryId) {
                          var action = '<?php echo $action; ?>';
                          var iDisplayOrder = '<?php echo $iDisplayOrder; ?>';

                          var ajaxData = {
                                'URL': '<?php echo $tconfig['tsite_url_main_admin']; ?>ajax_get_service_order.php',
                                'AJAX_DATA': {iVehicleCategoryId: iVehicleCategoryId, action: action, iDisplayOrder: iDisplayOrder},
                            };
                            getDataFromAjaxCall(ajaxData, function(response) {
                                if(response.action == "1") {
                                    var dataHTML2 = response.result;
                                    if (dataHTML2 != null)
                                        $("#change_order").html(dataHTML2);
                                }
                                else {
                                    console.log(response.result);
                                }
                            });
                      }
                      function isNumberKey(evt)
                      {
                          var charCode = (evt.which) ? evt.which : event.keyCode
                          //if (charCode > 31 && (charCode < 48 || charCode > 57))
                          //return false;
                          //return true;
                          if (charCode > 47 && charCode < 58 || charCode == 46 || charCode == 127 || charCode == 8)
                              return true;
                          return false;
                      }
                </script>
                <script type="text/javascript">
                    var input = $('.clockpicker-with-callbacks').clockpicker({
                        donetext: 'Done',
                        init: function () {
                            console.log("colorpicker initiated");
                        },
                        beforeShow: function () {
                            console.log("before show");
                        },
                        afterShow: function () {
                            console.log("after show");
                        },
                        beforeHide: function () {
                            console.log("before hide");
                        },
                        afterHide: function () {
                            console.log("after hide");
                        },
                        beforeHourSelect: function () {
                            console.log("before hour selected");
                        },
                        afterHourSelect: function () {
                            console.log("after hour selected");
                        },
                        beforeDone: function () {
                            console.log("before done");
                        },
                        afterDone: function () {
                            console.log("after done");
                        }
                    });

                    document.getElementById('RNightMonday').onclick = function () {
                        var tMonNightStartTime = document.getElementById('tMonNightStartTime');
                        var tMonNightEndTime = document.getElementById('tMonNightEndTime');
                        var fMonNightPrice = document.getElementById('fMonNightPrice');

                        tMonNightStartTime.value = tMonNightEndTime.value = fMonNightPrice.value = '';
                    };

                    document.getElementById('RNightTuesday').onclick = function () {
                        var tTueNightStartTime = document.getElementById('tTueNightStartTime');
                        var tTueNightEndTime = document.getElementById('tTueNightEndTime');
                        var fTueNightPrice = document.getElementById('fTueNightPrice');

                        tTueNightStartTime.value = tTueNightEndTime.value = fTueNightPrice.value = '';
                    };

                    document.getElementById('RNightWednesday').onclick = function () {
                        var tWedNightStartTime = document.getElementById('tWedNightStartTime');
                        var tWedNightEndTime = document.getElementById('tWedNightEndTime');
                        var fWedNightPrice = document.getElementById('fWedNightPrice');

                        tWedNightStartTime.value = tWedNightEndTime.value = fWedNightPrice.value = '';
                    };

                    document.getElementById('RNightThursday').onclick = function () {
                        var tThuNightStartTime = document.getElementById('tThuNightStartTime');
                        var tThuNightEndTime = document.getElementById('tThuNightEndTime');
                        var fThuNightPrice = document.getElementById('fThuNightPrice');

                        tThuNightStartTime.value = tThuNightEndTime.value = fThuNightPrice.value = '';
                    };


                    document.getElementById('RNightFriday').onclick = function () {
                        var tFriNightStartTime = document.getElementById('tFriNightStartTime');
                        var tFriNightEndTime = document.getElementById('tFriNightEndTime');
                        var fFriNightPrice = document.getElementById('fFriNightPrice');

                        tFriNightStartTime.value = tFriNightEndTime.value = fFriNightPrice.value = '';
                    };

                    document.getElementById('RNightSaturday').onclick = function () {
                        var tSatNightStartTime = document.getElementById('tSatNightStartTime');
                        var tSatNightEndTime = document.getElementById('tSatNightEndTime');
                        var fSatNightPrice = document.getElementById('fSatNightPrice');

                        tSatNightStartTime.value = tSatNightEndTime.value = fSatNightPrice.value = '';
                    };

                    document.getElementById('RNightSunday').onclick = function () {
                        var tSunNightStartTime = document.getElementById('tSunNightStartTime');
                        var tSunNightEndTime = document.getElementById('tSunNightEndTime');
                        var fSunNightPrice = document.getElementById('fSunNightPrice');

                        tSunNightStartTime.value = tSunNightEndTime.value = fSunNightPrice.value = '';
                    };

                    function editServiceType(action)
                    {
                        $('#modal_action').html(action);
                        $('#ServiceType_Modal').modal('show');
                    }

                    function saveServiceType()
                    {
                        var check_service_desc = 0;
                        <?php if ('UberX' === $app_type_service && 'Provider' === $SERVICE_PROVIDER_FLOW) { ?>
                            check_service_desc = 1;
                        <?php } ?>


                        if($('#vVehicleType_<?php echo $default_lang; ?>').val() == "") {
                            $('#vVehicleType_<?php echo $default_lang; ?>_error').show();
                            $('#vVehicleType_<?php echo $default_lang; ?>').focus();
                            clearInterval(langVar);
                            langVar = setTimeout(function() {
                                $('#vVehicleType_<?php echo $default_lang; ?>_error').hide();
                            }, 5000);
                            return false;
                        }


                        $('#vVehicleType_Default').val($('#vVehicleType_<?php echo $default_lang; ?>').val());
                        $('#vVehicleType_Default').closest('.row').removeClass('has-error');
                        $('#vVehicleType_Default-error').remove();

                        $('#ServiceType_Modal').modal('hide');
                    }

                    function editDescWeb(action, modal_id)
                    {
                        $('#'+modal_id).find('#modal_action').html(action);
                        $('#'+modal_id).modal('show');
                    }

                    function saveDescWeb(input_id, modal_id)
                    {
                        console.log(input_id);
                        console.log(modal_id);
                        var DescLength = CKEDITOR.instances[input_id+'<?php echo $default_lang; ?>'].getData().replace(/<[^>]*>/gi, '').length;
                        if(!DescLength) {
                            $('#'+input_id+'<?php echo $default_lang; ?>_error').show();
                            $('#'+input_id+'<?php echo $default_lang; ?>').focus();
                            clearInterval(myVar);
                            myVar = setTimeout(function() {
                                $('#'+input_id+'<?php echo $default_lang; ?>_error').hide();
                            }, 5000);
                            e.preventDefault();
                            return false;
                        }

                        var DescHTML = CKEDITOR.instances[input_id + '<?php echo $default_lang; ?>'].getData();
                        CKEDITOR.instances[input_id+'Default'].setData(DescHTML);
                        $('#'+modal_id).modal('hide');
                    }

                    function editVehicleDescInfo(action)
                    {
                        $('#modal_action').html(action);
                        $('#tInfoText_Modal').modal('show');
                    }

                    function saveVehicleDescInfo()
                    {
                        if($('#tInfoText_<?php echo $default_lang; ?>').val().trim() == "") {
                            $('#tInfoText_<?php echo $default_lang; ?>_error').show();
                            $('#tInfoText_<?php echo $default_lang; ?>').focus();
                            clearInterval(langVar);
                            langVar = setTimeout(function() {
                                $('#tInfoText_<?php echo $default_lang; ?>_error').hide();
                            }, 5000);
                            return false;
                        }

                        $('#tInfoText_Default').val($('#tInfoText_<?php echo $default_lang; ?>').val());
                        $('#tInfoText_Default').closest('.row').removeClass('has-error');
                        $('#tInfoText_Default-error').remove();
                        $('#tInfoText_Modal').modal('hide');
                    }

                    $(document).on('keyup paste', 'textarea:not([name^=tTypeDesc_], .cke_source)', function(e) {
                        var tval = $(this).val(),
                        tlength = tval.length,
                        set = 100,
                        remain = parseInt(set - tlength);
                        if (tlength > 0) {
                            $(this).closest('.desc-block').find('.desc_counter').text(remain + "/120");
                            if (remain <= 0) {
                                $(this).val((tval).substring(0, set));
                                $(this).closest('.desc-block').find('.desc_counter').text("0/120");
                                return false;
                            }
                        } else {
                            $(this).closest('.desc-block').find('.desc_counter').text("120/120");
                            return false;
                        }
                    });
                </script>
            </body>
            <!-- END BODY-->
        </html>