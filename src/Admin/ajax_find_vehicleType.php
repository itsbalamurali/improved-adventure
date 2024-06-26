<?php
include_once("../common.php");

$iDriverId = isset($_REQUEST['iDriverId']) ? $_REQUEST['iDriverId'] : '';
$vCarType = isset($_REQUEST['selected']) ? $_REQUEST['selected'] : '';
$vCarTyp = explode(",", $vCarType);

$vRentalCarType = isset($_REQUEST['rentalselected']) ? $_REQUEST['rentalselected'] : '';
$vRentalCarTyp = explode(",", $vRentalCarType);

$eType = isset($_REQUEST['eType']) ? $_REQUEST['eType'] : '';
$Front = isset($_REQUEST['Front']) ? $_REQUEST['Front'] : '';
$fly = isset($_REQUEST['fly']) ? $_REQUEST['fly'] : ''; //added by SP for fly on 6-9-2019
$serviceId = isset($_REQUEST['serviceId']) ? $_REQUEST['serviceId'] : '0'; 

$eStatus = $eStatusQuery = $eQuery = '';
//Added By HJ On 10-01-2019 For Solve Bug - 6166 Start
/* if(DELIVERALL == "Yes"){
  $eQuery .= " AND eType = 'DeliverAll'";
  } else { */
if (ONLYDELIVERALL == "Yes" || $eType == "DeliverAll" || $serviceId>0) {
    $eQuery .= " AND eType = 'DeliverAll'";
} else {
    if ($APP_TYPE == "Delivery") {
        $eQuery .= " AND eType = 'Deliver'";
    } else if ($APP_TYPE == "Ride-Delivery" || $APP_TYPE == "Ride-Delivery-UberX") {
        $isMultipleVehicleCategoryAvailable = true;
        $eQuery .= " AND eType != 'UberX'";
        ### Checking Vehicles For Ride , Delivery Icons and Banners Availability ##
        $CheckRideDeliveryFeatureDisable_Arr = CheckRideDeliveryFeatureDisable();
        $eShowRideVehicles = $CheckRideDeliveryFeatureDisable_Arr['eShowRideVehicles'];
        $eShowDeliveryVehicles = $CheckRideDeliveryFeatureDisable_Arr['eShowDeliveryVehicles'];
        $eShowDeliverAllVehicles = $CheckRideDeliveryFeatureDisable_Arr['eShowDeliverAllVehicles'];
        if ($eShowRideVehicles == "No") {
            $eQuery .= " AND eType != 'Ride'";
        }
        if ($eShowDeliveryVehicles == "No") {
            $eQuery .= " AND eType != 'Deliver'";
        }
        if ($eShowDeliverAllVehicles == "No" || DELIVERALL == "No") {
            $eQuery .= " AND eType != 'DeliverAll'";
        }
    } else {

        if ($APP_TYPE == "Delivery" && $MODULES_OBJ->isDeliveryFeatureAvailable()) {
            $eQuery .= " AND eType = 'Deliver'";
        } else if ($APP_TYPE == "Ride-Delivery" || $APP_TYPE == "Ride-Delivery-UberX") {
            $isMultipleVehicleCategoryAvailable = true;
            $eQuery .= " AND eType != 'UberX'";
    ### Checking Vehicles For Ride , Delivery Icons and Banners Availability ##
            $CheckRideDeliveryFeatureDisable_Arr = CheckRideDeliveryFeatureDisable();
            $eShowRideVehicles = $CheckRideDeliveryFeatureDisable_Arr['eShowRideVehicles'];
            $eShowDeliveryVehicles = $CheckRideDeliveryFeatureDisable_Arr['eShowDeliveryVehicles'];
            $eShowDeliverAllVehicles = $CheckRideDeliveryFeatureDisable_Arr['eShowDeliverAllVehicles'];
            if ($eShowRideVehicles == "No") {
                $eQuery .= " AND eType != 'Ride'";
            }
            if ($eShowDeliveryVehicles == "No") {
                $eQuery .= " AND eType != 'Deliver'";
            }
            if ($eShowDeliverAllVehicles == "No" || DELIVERALL == "No") {
                $eQuery .= " AND eType != 'DeliverAll'";
            }
        } else {
            $eQuery .= " AND eType = '" . $APP_TYPE . "'";
        }

    }
}
//added by SP for fly on 6-9-2019
if ($fly != 'Yes') {
    $eQuery .= " AND vt.eFly='0'";
}

if(!$MODULES_OBJ->isEnableMedicalServices()) {
    $eQuery .= " AND vt.eIconType != 'Ambulance' ";
}
//}
//Added By HJ On 10-01-2019 For Solve Bug - 6166 End
if ($eStatus == '') {
    $eStatusQuery = "AND vt.eStatus = 'Active'"; //added by SP on 2-7-2019 for only display active vehicle
}
/* $locations_where = "";
  if(count($userObj->locations) > 0){
  $locations = implode(', ', $userObj->locations);
  $locations_where = " AND vt.iLocationid IN(-1, {$locations}) ";
  } */

if (!empty($iDriverId)) {
    $userSQL = "SELECT c.iCountryId FROM register_driver AS rd LEFT JOIN country AS c ON c.vCountryCode=rd.vCountry where rd.iDriverId='" . $iDriverId . "'";
    $drivers = $obj->MySQLSelect($userSQL);
    $iCountryId = $drivers[0]['iCountryId'];
    if ($iCountryId != '') {
        $vehicle_type_sql = "SELECT vt.*,c.vCountry,ct.vCity,st.vState,lm.vLocationName from  vehicle_type as vt left join country as c ON c.iCountryId = vt.iCountryId left join state as st ON st.iStateId = vt.iStateId left join city as ct ON ct.iCityId = vt.iCityId left join location_master as lm ON lm.iLocationId = vt.iLocationid where vt.eType !='UberX' AND (lm.iCountryId='" . $iCountryId . "'OR vt.iLocationId = '-1') $eQuery $eStatusQuery";
    }
} else {
    $vehicle_type_sql = "SELECT vt.*,c.vCountry,ct.vCity,st.vState,lm.vLocationName from  vehicle_type as vt left join country as c ON c.iCountryId = vt.iCountryId left join state as st ON st.iStateId = vt.iStateId left join city as ct ON ct.iCityId = vt.iCityId left join location_master as lm ON lm.iLocationId = vt.iLocationid where vt.eType !='UberX' $eQuery $eStatusQuery";
}

$vehicle_type_data = $obj->MySQLSelect($vehicle_type_sql);

function filterElementFromArray($array, $key, $value, $New = '') {
    $DeliverAllvehicles = array();
    foreach ($array as $subKey => $subArray) {
        if ($subArray[$key] == $value) {
            if ($New == 'New') {
                $DeliverAllvehicles[] = $subArray;
            } else {
                unset($array[$subKey]);
            }
        }
    }
    if ($New == 'New') {
        return $DeliverAllvehicles;
    } else {
        return $array;
    }
}

$vehicle_type_data_new = filterElementFromArray($vehicle_type_data, "eType", "DeliverAll");
$DeliverAllvehiclesData = filterElementFromArray($vehicle_type_data, "eType", "DeliverAll", "New");

foreach ($vehicle_type_data_new as $key => $value) {
    if($value['eType'] == "Ride") {
        $eTypeNew = 1;
    }
    if($value['eType'] == "Deliver") {
        $eTypeNew = 2;
    }
    if($value['eType'] == 'Ride' && $value['eFly'] == '1')
    {
        $eTypeNew = 3;
    }
    $vehicle_type_data_new[$key]['eTypeNew'] = $eTypeNew;
}

$sort_eType = array_column($vehicle_type_data_new, 'eTypeNew');
array_multisort($sort_eType, SORT_ASC, $vehicle_type_data_new);

foreach ($vehicle_type_data_new as $key => $value) {

    $vname = $value['vVehicleType_' . $default_lang];
    $vCountry = $value['vCountry'];
    $vCity = $value['vCity'];
    $vState = $value['vState'];
    $db_etype = $value['eType'];

    if($value['eIconType'] == "Ambulance")
    {
        $db_etype = $langage_lbl_admin['LBL_VEHICLE_TYPE_AMBULANCE_TXT'];
    }
    //added by SP for fly on 6-9-2019
    if ($fly == 'Yes') {
        if ($value['eType'] == 'Ride' && $value['eFly'] == '1') {
            $db_etype = 'Fly';
        }
    }


    ?>

    <div class="row">
        <div class="col-lg-8">
            <div class="col-lg-6">
                <div><?php echo $vname . " (" . $db_etype . ")"; ?></div>
                <div style="font-size: 12px;">
                    <?php
                    $localization = '';
                    if (!empty($value['vLocationName'])) {
                        $localization .= $value['vLocationName'];
                    }
                    if (!empty($value['vLocationName'])) {
                        echo "( Location : " . $localization . ")";
                    } else if ($value['iLocationid'] == "-1") {
                        echo "( All Locations )";
                    }
                    ?>
                </div>
                <?php if (ENABLE_RENTAL_OPTION == 'Yes') { ?>
                    <?php
                    $checkrentalquery = "SELECT count(iRentalPackageId) as totalrental FROM  `rental_package` WHERE iVehicleTypeId = '" . $value['iVehicleTypeId'] . "'";
                    $rental_data = $obj->MySQLSelect($checkrentalquery);
                    if ($rental_data[0]['totalrental'] > 0) {
                        ?>
                        <div id="<?= 'RentalVehicleType_' . $key; ?>" style="display: none;">
                            <div>
                                <input type="checkbox" class="chk" name="vRentalCarType[]" <?php if (in_array($value['iVehicleTypeId'], $vRentalCarTyp)) { ?>checked<?php } ?> value="<?= $value['iVehicleTypeId'] ?>"/> Accept rental request for <?php echo $vname; ?> vehicle type?
                            </div>
                        </div>
                        <?
                    }
                }
                ?>
                <?php if($MODULES_OBJ->isEnableDeliveryHelper() && $db_etype == "Deliver" && $value['eDeliveryHelper'] == "Yes") { 
                    if(!empty($value['tDeliveryHelperNoteDriver'])) {
                        $value['tDeliveryHelperNoteDriver'] = json_decode($value['tDeliveryHelperNoteDriver'], true);
                        $value['tDeliveryHelperNoteDriver'] = $value['tDeliveryHelperNoteDriver']['tDeliveryHelperNoteDriver_'.$default_lang];
                    }
                    if(!empty($value['tDeliveryHelperNoteDriver'])){
                    ?>
                    <div id="<?= 'RentalVehicleType_' . $key; ?>" style="display: none;"><span style="color: #ff0000"><?= $langage_lbl_admin['LBL_INC_DEL_HELPER'] ?></span><i class="fa fa-question-circle" onclick="showInfo('<?= 'RentalVehicleType_desc' . $key; ?>')" style="font-size: 16px; margin-left: 5px; cursor: pointer;"></i></div>
                    <div id="<?= 'RentalVehicleType_desc' . $key; ?>" style="display: none;"><?= $value['tDeliveryHelperNoteDriver'] ?></div>
                <?php } } ?>
            </div>

            <div class="col-lg-6">
                <div class="make-switch make-swith001" data-on="success" data-off="warning">
                    <input type="checkbox" class="chk" name="vCarType[]" id="vCarType_<?= $key; ?>" <?php if (in_array($value['iVehicleTypeId'], $vCarTyp)) { ?>checked<?php } ?> value="<?= $value['iVehicleTypeId'] ?>"/>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<?php if (count($DeliverAllvehiclesData) > 0) { ?>
    <div class="row">
        <h4 class="col-lg-6">DeliverAll Vehicle Type <small>(Only Choose One Vehicle)</small></h4>
    </div>
<?php } ?>
<?php
$vIds = "";
foreach ($DeliverAllvehiclesData as $key => $value) {
    $vname = $value['vVehicleType_' . $default_lang];
    $vCountry = $value['vCountry'];
    $vCity = $value['vCity'];
    $vState = $value['vState'];
    $db_etype = $value['eType'];
    $vIds .= "," . $value['iVehicleTypeId'];
    ?>
    <div class="row">
        <div class="col-lg-8">
            <div class="col-lg-6">
                <div><?php echo $vname . " (" . $db_etype . ")"; ?></div>
                <div style="font-size: 12px;">
                    <?php
                    $localization = '';
                    if (!empty($value['vLocationName'])) {
                        $localization .= $value['vLocationName'];
                    }
                    if (!empty($value['vLocationName'])) {
                        echo "( Location : " . $localization . ")";
                    } else if ($value['iLocationid'] == "-1") {
                        echo "( All Locations )";
                    }
                    ?>
                </div>
            </div>			
            <div class="col-lg-6">
                <div class="make-switch make-swith001 radio2" data-on="success" data-off="warning" style="">
                    <input type="radio" name="vCarType[]" id="vCarType" class="vCarType" <?php if (in_array($value['iVehicleTypeId'], $vCarTyp)) { ?>checked<?php } ?> value="<?= $value['iVehicleTypeId'] ?>">
                </div>
            </div>
        </div>
    </div>
    <?php
}
?>
<input type="hidden" name="deliverall" value="<?php echo trim($vIds, ","); ?>">
<?php
if (ENABLE_RENTAL_OPTION == 'Yes' || $MODULES_OBJ->isEnableDeliveryHelper()) {
    foreach ($vehicle_type_data_new as $key => $value) {
        ?>
        <script>
            $(function () {
                if ($("#vCarType_" +<?= $key ?>).is(':checked')) {
                    $("#RentalVehicleType_" +<?= $key ?>).show();
                } else {
                    $("#RentalVehicleType_" +<?= $key ?>).hide();
                }

                $("#vCarType_" +<?= $key ?>).on('change.bootstrapSwitch', function (event)
                {
                    if ($(this).is(':checked'))
                    {
                        $("#RentalVehicleType_" +<?= $key ?>).show();
                    } else {
                        $("#RentalVehicleType_" +<?= $key ?>).hide();
                    }
                })
            });
        </script>
        <?php
    }
}
?>
<script>

    $(".make-swith001").bootstrapSwitch();
    $(".radio2").on('switch-change', function () {
        $(".radio2").bootstrapSwitch('toggleRadioStateAllowUncheck', true);
    });

    function showInfo(descid)
    {
        var info_img = '<img src="<?= $tconfig['tsite_url'].'assets/img/delivery-helper.svg' ?>" class="popup-img">';
        var info_description = '<div class="popup-title"><?= $langage_lbl_admin['LBL_DEL_HELPER'] ?></div><div class="popup-desc delivery-helper-info">' + $('#'+descid).html();
        var info_btn = '<?= $langage_lbl_admin['LBL_BTN_OK_TXT'] ?>'
        show_modal_popup(info_img, info_description,"","", info_btn);
    }
    
</script>

