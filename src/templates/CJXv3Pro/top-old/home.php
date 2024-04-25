<?php

$showSignRegisterLinks = 1;
$how_it_work_section = json_decode($data[0]['lHowitworkSection'], true);
//Added By HJ On 16-05-2020 For Solved Issue to Be Fixed Issue #1330 Start
$vCode = $_SESSION['sess_lang'];
//Added By HJ On 16-05-2020 For Solved Issue to Be Fixed Issue #1330 End
$secure_section = json_decode($data[0]['lSecuresafeSection'], true);
$download_section = json_decode($data[0]['lDownloadappSection'], true);
$call_section = json_decode($data[0]['lCalltobookSection'], true);
$book_section = json_decode($data[0]['lCalculateSection'], true);

$register_section = json_decode($data[0]['lBookServiceSection'], true);
$service_section = $data[0]['lServiceSection'];
$service_section2 = $data[0]['lSafeSection'];
$link_store_home = "sign-up?type=store";
$link_driver_home = "sign-up?type=provider";
$rideTypeEnabled = $motoTypeEnabled = $deliveryTypeEnabled = $deliverAllTypeEnabled = $ufxTypeEnabled = "No";
$etype = "Ride";
$rideModuleAvailable = $MODULES_OBJ->isRideFeatureAvailable();
$rideData = $obj->MySQLSelect("SELECT * FROM `vehicle_category` WHERE `iVehicleCategoryId` = 174 AND `eStatus` = 'Active' AND `eCatType` = 'Ride'");
if (count($rideData) > 0) {
    $rideTypeEnabled = "Yes";
}
$motoData = $obj->MySQLSelect("SELECT * FROM `vehicle_category` WHERE `iParentId` = 0 AND `eStatus` = 'Active' AND `eCatType` = 'MotoRide'");
if (count($motoData) > 0) {
    $motoTypeEnabled = "Yes";
}
$deliveryModuleAvailable = $MODULES_OBJ->isDeliveryFeatureAvailable();
$deliveryData = $obj->MySQLSelect("SELECT * FROM `vehicle_category` WHERE `iParentId` = 0 AND `eStatus` = 'Active' AND `eCatType` = 'MoreDelivery' AND eFor='DeliveryCategory'");
if (count($deliveryData) > 0) {
    $deliveryTypeEnabled = "Yes";
}
$deliverAllModuleAvailable = $MODULES_OBJ->isDeliverAllFeatureAvailable();
$deliverAllData = $obj->MySQLSelect("SELECT * FROM `vehicle_category` WHERE `iParentId` = 0 AND `eStatus` = 'Active' AND `eCatType` = 'DeliverAll' ");

if (count($deliverAllData) > 0) {
    $deliverAllTypeEnabled = "Yes";
}
$ufxModuleAvailable = $MODULES_OBJ->isUberXFeatureAvailable();
$ufx_sql = "";
if($MODULES_OBJ->isEnableMedicalServices()) {
    $ufx_sql = " AND iVehicleCategoryId NOT IN (3,22,26,158) ";
}
$ufxData = $obj->MySQLSelect("SELECT * FROM `vehicle_category` WHERE `iParentId` = 0 AND `eStatus` = 'Active' AND `eCatType` = 'ServiceProvider' $ufx_sql ORDER BY vCategory_"  . $_SESSION['sess_lang']);
if (count($ufxData) > 0) {
    $ufxTypeEnabled = "Yes";
}
$booking_services_arr = array();
$subquery = "";
if (!$MODULES_OBJ->isRideFeatureAvailable()) {
    $subquery .= " AND eType != 'Ride'";
}
if (!$MODULES_OBJ->isDeliveryFeatureAvailable()) {
    $subquery .= " AND eType != 'Deliver'";
}
if(!$MODULES_OBJ->isDeliverAllFeatureAvailable()) {
    $subquery .= " AND eType != 'DeliverAll'";   
}
if (!$MODULES_OBJ->isUberXFeatureAvailable()) {
    $subquery .= " AND eType != 'UberX'";
}
$master_service_categories = $obj->MySQLSelect("SELECT eType FROM $master_service_category_tbl WHERE 1 = 1 $subquery AND eStatus != 'Deleted' ");
$sql_vehicle_category_table_name = getVehicleCategoryTblName();
foreach ($master_service_categories as $key => $value) {
    $ssql = getMasterServiceCategoryQuery($value['eType']);
    if (in_array($value['eType'], ['Ride', 'Deliver', 'DeliverAll', 'UberX'])) {
        $category_data = $obj->MySQLSelect("SELECT iVehicleCategoryId, vCategory_" . $_SESSION['sess_lang'] . " as vCategory, eCatType, iServiceId FROM " . $sql_vehicle_category_table_name . "  WHERE  1 = 1 AND iParentId='0' AND eStatus!='Deleted' $ssql AND iVehicleCategoryId NOT IN (185, 295, 326) AND eVideoConsultEnable = 'No' AND eStatus = 'Active'");

        if ($value['eType'] == "UberX") {
            $new_ufx_arr = array();
            foreach ($category_data as $k => $cat_data) {
                if (in_array($cat_data['iVehicleCategoryId'], [1, 4])) {
                    $new_ufx_arr[] = $cat_data;
                    unset($category_data[$k]);
                }
            }
            $category_data = array_values($category_data);
            $vCategory = array_column($category_data, 'vCategory');
            array_multisort($vCategory, SORT_ASC, $category_data);
            $all_service_title[] = array('iVehicleCategoryId' => 0, 'vCategory' => $langage_lbl['LBL_ALL_SERVICES_TXT'], 'eCatType' => 'ServiceProvider');
            $category_data = array_merge($new_ufx_arr, $all_service_title, $category_data);
        }
        foreach ($category_data as $cat_data) {
            $catDataArr = array();
            $catDataArr['iVehicleCategoryId'] = $cat_data['iVehicleCategoryId'];
            $catDataArr['title'] = $cat_data['vCategory'];
            if ($cat_data['eCatType'] == "Ride") {
                $catDataArr['value'] = "Ride";
            } elseif ($cat_data['eCatType'] == "MoreDelivery") {
                $catDataArr['value'] = "Deliver";
            } elseif ($cat_data['eCatType'] == "DeliverAll") {
                $catDataArr['value'] = $cat_data['iServiceId'];
            } elseif ($cat_data['eCatType'] == "ServiceProvider") {
                $catDataArr['value'] = "UberX";
            }
            $ETYPE = ['Ride', 'MoreDelivery', 'DeliverAll'];
            if (strtoupper(ENABLE_MANUAL_BOOKING_UBERX) == 'YES') {
                $ETYPE = ['Ride', 'MoreDelivery', 'DeliverAll', 'ServiceProvider'];
            }
            //if(in_array($cat_data['eCatType'], ['Ride', 'MoreDelivery', 'DeliverAll', 'ServiceProvider'])) {
            //UFX booking remove from web
            if (in_array($cat_data['eCatType'], $ETYPE)) {
                $booking_services_arr[] = $catDataArr;
            }
        }
    }
}
$tableName = getAppTypeWiseHomeTable();

$book_data = $obj->MySQLSelect("SELECT booking_ids FROM $tableName WHERE vCode = '" . $_SESSION['sess_lang'] . "'");
$vcatdata_first = $vcatdata_sec = $vcatdata = array();
$vcatdata_first = getSeviceCategoryDataForHomepage($book_data[0]['booking_ids'], 0, 1);
if (!empty($book_data[0]['booking_ids'])) {
    $cPage = 0; // for the medical services 
    $vcatdata_sec = getSeviceCategoryDataForHomepage($book_data[0]['booking_ids'], 1, 1);
}
$vcatdata = array_merge($vcatdata_first, $vcatdata_sec);
$ourService = array();
if (isset($vcatdata) && !empty($vcatdata)) {
    foreach ($vcatdata as $v) {
        $ourService[$v['iVehicleCategoryId']] = $v;
    }
}

$moreSelectServiceArr = array();
if ($rideModuleAvailable == 1 && $motoTypeEnabled == "Yes") { 
    $moreSelectServiceArr[] = array(
        'service_url' => $ourService[$motoData[0]['iVehicleCategoryId']]['url'],
        'service_value' => 'Moto',
        'service_name' => 'Moto',
        'service_title' => $langage_lbl['LBL_HEADER_RDU_MOTO_RIDE']
    );
}

if ($deliverAllModuleAvailable == 1 && $deliverAllTypeEnabled == "Yes") {
    for ($i = 0; $i < count($service_categories); $i++) {
        $iServiceId = $service_categories[$i]['iServiceId'];
        if(in_array($iServiceId, [7,11])) {
            if ($service_categories[$i]['vImage'] == "") {
                $service_categories[$i]['vImage'] = $siteUrl . 'assets/img/burger.jpg';
            }

            $moreSelectServiceArr[] = array(
                'service_url' => $ourService[$motoData[0]['iVehicleCategoryId']]['url'],
                'service_value' => $iServiceId,
                'service_name' => $iServiceId,
                'service_title' => ucfirst($service_categories[$i]['vServiceName']) . ' ' . $langage_lbl['LBL_DELIVERY']
            );
        } 
    } 
}

if ($ufxModuleAvailable == 1 && $ufxTypeEnabled == "Yes") {
    for ($i = 0; $i < count($ufxData); $i++) {
        $iVehicleCategoryId = $ufxData[$i]['iVehicleCategoryId'];
        $Serviceurl =  $ourService[$ufxData[$i]['iVehicleCategoryId']]['url'];

        $moreSelectServiceArr[] = array(
            'service_url' => $ourService[$ufxData[$i]['iVehicleCategoryId']]['url'],
            'service_value' => 'UberX',
            'service_name' => 'UberX',
            'service_vehicle_category_id' => $ufxData[$i]['iVehicleCategoryId'],
            'service_title' => ucfirst($ufxData[$i]['vCategory_' . $_SESSION['sess_lang']])
        );
    }
}

foreach ($moreSelectServiceArr as $key => $value) {
    $sort_data[$key] = $value['service_title'];
}
array_multisort($sort_data, SORT_ASC, $moreSelectServiceArr);
?>
<script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places&language=en&key=<?= $GOOGLE_SEVER_API_KEY_WEB ?>"></script>
<!-- ************* services section section start ************* -->
<div class="location-area">
    <div class="location-area-inner">
        <?php if (($rideModuleAvailable == 1 && $rideTypeEnabled == "Yes") || ($deliveryModuleAvailable == 1 && $deliveryTypeEnabled == "Yes") || ($deliverAllModuleAvailable == 1 && $deliverAllTypeEnabled == "Yes") || ($ufxModuleAvailable == 1 && $ufxTypeEnabled == "Yes")) { ?>
            <div class="common-title havedesc">
                <h3><?= $book_section['title'] ?></h3>
                <p><?= $book_section['subtitle'] ?></p>
            </div>
            <form name="_fare_estimate_form" id="_fare_estimate_form" method="post" action="cx-fareestimate.php">
                <div class="element-holder">
                    <div class="select_service">
                        <div class="selected_service"><?= $langage_lbl['LBL_SELECT_SERVICE_TXT'] ?></div>
                        <ul id="standard-select" name="select" onchange="selectbookingorder(this.value)">
                            <div class="services_part">
                                <strong class="services_part_title"><?= $langage_lbl['LBL_MAJOR_SERVICES_TITLE'] ?></strong>
                                <?php
                                if ($rideModuleAvailable == 1 && $rideTypeEnabled == "Yes") {
                                    $etype = "Ride";
                                    ?>
                                    <li value="Ride" data-servicename="Ride"><a><?php echo $langage_lbl['LBL_TAXI_RIDE']; ?></a></li>
                                <? }
                                if ($deliveryModuleAvailable == 1 && $deliveryTypeEnabled == "Yes") {
                                    $etype = "Deliver";
                                    $Serviceurl =  $ourService[$deliveryData[0]['iVehicleCategoryId']]['url'];
                                    ?>
                                    <li value="Deliver" data-servicename="Deliver" data-url="<?php echo $Serviceurl;?>"><a><?php echo $langage_lbl['LBL_PARCEL_DELIVERY']; ?></a></li>
                                <? } 
                                if ($deliverAllModuleAvailable == 1 && $deliverAllTypeEnabled == "Yes") {

                                    for ($i = 0; $i < count($service_categories); $i++) {
                                        $iServiceId = $service_categories[$i]['iServiceId'];
                                        if(!in_array($iServiceId, [7,11])) {
                                        if ($service_categories[$i]['vImage'] == "") {
                                            $service_categories[$i]['vImage'] = $siteUrl . 'assets/img/burger.jpg';
                                        }

                                        $deliverAllData = $obj->MySQLSelect("SELECT iVehicleCategoryId FROM `vehicle_category` WHERE `iParentId` = 0 AND `eStatus` = 'Active' AND `eCatType` = 'DeliverAll' AND iServiceId ='".$iServiceId."' ");

                                        $Serviceurl =  $ourService[$deliverAllData[0]['iVehicleCategoryId']]['url'];
                                        ?>
                                        <li value="<?php echo $iServiceId; ?>" data-servicename="<?php echo $iServiceId; ?>" data-url="<?php echo $Serviceurl;?>">
                                            <a><?php echo ucfirst($service_categories[$i]['vServiceName']) . ' ' . $langage_lbl['LBL_DELIVERY']; ?></a>
                                        </li>
                                <?php } } } ?>
                            </div>
                            <div class="services_part">
                            <strong class="services_part_title"><?= $langage_lbl['LBL_MORE_SERVICES'] ?></strong>
                            <?php foreach ($moreSelectServiceArr as $mServiceArr) { ?>
                                <li value="<?= $mServiceArr['service_value'] ?>" data-servicename="<?= $mServiceArr['service_name'] ?>" data-url="<?= $mServiceArr['service_url'] ?>" <?php if(isset($mServiceArr['service_vehicle_category_id'])) { ?> data-vehiclecategoryid="<?= $mServiceArr['service_vehicle_category_id'] ?>" <?php } ?>><a><?= $mServiceArr['service_title'] ?></a></li>
                            <?php } ?>
                            </div>
                        </ul>
                 
                    </div>
                </div>
                <div class="element-holder address-input">
                    <input id="servicename" name="servicename" type="hidden" />
                    <input type="hidden" name="SUBMIT" id="BTNSUBMIT" value="1">
                    <input type="hidden" name="serviceid" id="serviceid" value="">
                    <input type="hidden" name="distance" id="distance" value="">
                    <input type="hidden" name="duration" id="duration" value="">
                    <input type="hidden" name="from_lat_long" id="from_lat_long" value="">
                    <input type="hidden" name="from_lat" id="from_lat" value="">
                    <input type="hidden" name="from_long" id="from_long" value="">
                    <input type="hidden" name="to_lat_long" id="to_lat_long" value="">
                    <input type="hidden" name="to_lat" id="to_lat" value="">
                    <input type="hidden" name="to_long" id="to_long" value="">
                    <input type="hidden" name="location_found" id="location_found" value="">
                    <input type="hidden" name="etype" id="etype" value="Ride">
                    <input type="hidden" name="navigatedPage" id="navigatedPage" value="Ride">
                    <input type="text" class="searchTerm" name="vPickup" id="from"
                           placeholder="<?= $langage_lbl['LBL_MANUAL_STORE_ENTER_ADRESS_TEXT'] ?>" autocorrect="off"
                           spellcheck="false" autocomplete="off">
                    <img src="<?= $siteUrl ?>assets/img/detect_loc.svg" class="detect-loc" onclick="fetchLocation()"
                         title="<?= $langage_lbl['LBL_FETCH_LOCATION_HINT'] ?>">
                    <button type="submit" class="searchButton" name="submitbtn"></button>
                    <a target="_blank" href="#" class="goto-service-page">Click Here to Procced</a>
                </div>
            </form>
        <?php } ?>
    </div>
</div>
<section class="rootservices homepage page-section our-services" id="our-services">
    <span class="round-shape"></span>
    <div class="rootservices-inner">
        <div class="common-title havedesc">
            <h3><?= $service_section; ?></h3>
            <p><?= $service_section2; ?></p>
        </div>
        <?php $vcatdata = getSeviceCategoryDataForHomepage($vehicleFirstImage, 1, 1);
        if (count($vcatdata) > 1) { ?>
            <ul>
            <?php if (!empty($data[0]['booking_ids'])) {
                $vehicleFirstImage = $data[0]['booking_ids'];
                $lang = isset($_SESSION['sess_lang']) ? $_SESSION['sess_lang'] : "EN";
                $vcatdata = getSeviceCategoryDataForHomepage($vehicleFirstImage, 0, 1);
                $vCatTitleHomepage = json_decode($vcatdata[$i]['vCatTitleHomepage'], true);
                if (empty($vCatTitleHomepage['vCatTitleHomepage_' . $lang])) {

                    $lang = 'EN';
                    $vcatdata = getSeviceCategoryDataForHomepage($vehicleFirstImage, 0, 1);
                }
                ?>
                <?php for ($i = 0; $i < count($vcatdata); $i++) {
                    if (file_exists($tconfig["tsite_upload_home_page_service_images_panel"] . '/' . $vcatdata[$i]['vServiceHomepageBanner']) && !empty($vcatdata[$i]['vServiceHomepageBanner'])) {

                        $back_img = "";
                    } else {

                        $back_img = "";
                    }
                ?>
                    <li>
                        <div class="servicesdata-caption-main">
                            <div class="servicesdata-caption">
                                <i>
                                    <? if (file_exists($tconfig["tsite_upload_home_page_service_images_panel"] . '/' . $vcatdata[$i]['vHomepageLogoOurServices']) && !empty($vcatdata[$i]['vHomepageLogoOurServices'])) { ?>
                                        <img src="<?= $tconfig["tsite_url"] . 'resizeImg.php?h=80&src=' . $tconfig["tsite_upload_home_page_service_images"] . '/' . $vcatdata[$i]['vHomepageLogoOurServices']; ?>"
                                            alt="">
                                    <? } ?>
                                </i>
                                <strong><?= $vcatdata[$i]['vCatName']; ?></strong>
                                <p><?= $vcatdata[$i]['vCatSubName']; ?></p>
                            </div>
                            <?php if (strtoupper(ENABLE_SUB_PAGES) == strtoupper('Yes')) { ?>
                                <a class="readmorebtn" href="<?= $vcatdata[$i]['url']; ?>" target="_blank"><?//= $langage_lbl['LBL_READ_MORE'] ?></a>
                            <?php } ?>
                        </div>
                    </li>
                    <?php } ?>
                    <li> 
                        <div class="sermore">
                        <div class="servicesdata-caption">
                                <i><img src="assets/img/apptype/CJXv3Pro/more-services.svg" alt=""></i>
                                <strong><?= $langage_lbl['LBL_MORE_SERVICES'] ?></strong>
                                <p><?= $langage_lbl['LBL_MORE_SERVICES_DESC'] ?></p>
                        </div>
                        <a class="readmorebtn" href="javascript:void(0);"><?//= $langage_lbl['LBL_ALL_SERVICES_TXT']; ?></a>
                        </div>
                    </li>
                </ul>
            <?php } ?>
            <?php 
            if(!empty($vehicleFirstImage)) {
                $vehicleFirstImage .= ",326";
            } else {
                $vehicleFirstImage = "326";
            }
            if($MODULES_OBJ->isEnableMedicalServices()) {
                $vehicleFirstImage .= ",3,22,26,158";
            }
            $vcatdata = getSeviceCategoryDataForHomepage($vehicleFirstImage, 1, 1);
           /* if(isset($_REQUEST['test'])){
                echo"<pre>";print_R($vcatdata);die;
            }*/
            if (count($vcatdata) > 0) {
                ?>
                <div class="hiddenrootser">
                    <div class="common-title">
                        <h3><?= $langage_lbl['LBL_MORE_SERVICES_SPAN']; ?></h3>
                    </div>
                    <div class="servicelist">
                        <?php
                        $iDisplayOrderHomepage = array_column($vcatdata, 'iDisplayOrderHomepage');
                        array_multisort($iDisplayOrderHomepage, SORT_ASC, $vcatdata);
                        $j = 0;
                        for ($i = 0; $i < count($vcatdata); $i++) {

                            $j++;
                              
                            $logoPath =$tconfig["tsite_upload_home_page_service_images"] . '/' . $vcatdata[$i]['vHomepageLogoOurServices'];
                            $logoOrgPath = $tconfig["tsite_upload_home_page_service_images_panel"] . '/' . $vcatdata[$i]['vHomepageLogoOurServices'];
                            $Weblogo = $vcatdata[$i]['vHomepageLogoOurServices'];

                            if ($MODULES_OBJ->isEnableAppHomeScreenLayoutV2()) {
                                $logoPath = $tconfig['tsite_upload_images_vehicle_category'] . "/" . $vcatdata[$i]['iVehicleCategoryId'] . "/" . $vcatdata[$i]['vListLogo2'];
                                $logoOrgPath = $tconfig["tsite_upload_images_vehicle_category_path"] . "/" . $vcatdata[$i]['iVehicleCategoryId'] . "/" . $vcatdata[$i]['vListLogo2'];
                                $Weblogo = $vcatdata[$i]['vListLogo2'];
                            }
                            if ($MODULES_OBJ->isEnableAppHomeScreenLayoutV3()) {
                                $logoPath = $tconfig['tsite_upload_images_vehicle_category'] . "/" . $vcatdata[$i]['iVehicleCategoryId'] . "/" . $vcatdata[$i]['vListLogo3'];
                                $logoOrgPath = $tconfig["tsite_upload_images_vehicle_category_path"]  . "/" . $vcatdata[$i]['iVehicleCategoryId'] . "/" . $vcatdata[$i]['vListLogo3'];
                                $Weblogo = $vcatdata[$i]['vListLogo3'];
                            } 

                            //if (file_exists($tconfig["tsite_upload_home_page_service_images_panel"] . '/' . $vcatdata[$i]['vHomepageLogoOurServices']) && !empty($vcatdata[$i]['vHomepageLogoOurServices'])) {

                                ?>
                                <div class="serviceblock">
                                    <a class="serviceblock-inner" <?php if (strtoupper(ENABLE_SUB_PAGES) == strtoupper('No')) { ?> href="javascript:void(0);"
                                        <?php } else { ?> href="<?= $vcatdata[$i]['url']; ?>"  target="_blank" <?php } ?>
                                       style="text-decoration: none;">
                                        <?php if (file_exists($logoOrgPath) && !empty($Weblogo)) { ?>
                                        <i>
                                            <img src="<?= $tconfig["tsite_url"] . 'resizeImg.php?w=140&src=' . $logoPath; ?>" alt="<?= $vcatdata[$i]['vCatName'] ?>" >
                                        </i>
                                        <?php } ?>
                                        <strong><?= $vcatdata[$i]['vCatName'] ?></strong>
                                    </a>
                                </div>
                            <?php //}
                        } ?>
                    </div>
                </div>
            <?php }
        } ?>
    </div>
</section>
<!-- ************* services section section end ************* -->
<!-- *************hot it works section start************* -->
<section class="howworks">
    <span class="strock-round"></span>
    <div class="howworks-inner">
        <div class="common-title havedesc">
            <h3><?= $how_it_work_section['title'] ?></h3>
            <p><?= $how_it_work_section['subtitle'] ?></p>
        </div>
        <ul>  
            <?php for ($i = 1; $i <= 4; $i++) { ?>
                <?php if (!empty($how_it_work_section['hiw_title' . $i . '_' . $vCode]) && !empty($how_it_work_section['hiw_desc' . $i . '_' . $vCode])) { ?>
                    <li data-number="<?php echo $i; ?>">
                        <div class="howworks-block">
                            <i><img alt="<?php echo $how_it_work_section['hiw_img' . $i . '_' . $vCode];?>" class="proc_ico" src="<?= $tconfig["tsite_upload_apptype_page_images"] . $template . '/' . $how_it_work_section['hiw_img' . $i . '_' . $vCode]; ?>" ></i>
                            <strong><?php echo $how_it_work_section['hiw_title' . $i . '_' . $vCode]; ?></strong>
                            <p><?php echo $how_it_work_section['hiw_desc' . $i . '_' . $vCode]; ?></p>
                        </div>
                    </li>
                <?php } ?>
            <?php } ?>
        </ul>
        <?= $how_it_work_section['desc'] ?>
    </div>
</section>
<!-- *************hot it works section end************* -->
<!-- *************Become a Partner section start************* -->
<section class="become">
    <span class="become-shape"></span>
    <div class="become-inner">
        <div class="common-title havedesc">
            <h3><?= $register_section['main_title'] ?></h3>
            <!-- <p><?= $register_section['main_subtitle'] ?></p> -->
            <p><?= $register_section['main_desc'] ?></p>
        </div>
        <ul>
            <li>
                <a href="<?= $link_driver_home; ?>">
                    <div class="became-img-block" style="background-image:url(<?php echo $tconfig["tsite_upload_apptype_page_images"] . $template . '/' . $register_section['img_first'] ?>)"></div>
                    <strong><?= $register_section['title_first'] ?></strong>
                </a>
            </li>
            <li>
                <a href="<?= $link_store_home; ?>">
                    <div class="became-img-block" style="background-image:url(<?php echo $tconfig["tsite_upload_apptype_page_images"] . $template . '/' . $register_section['img_sec'] ?>)"></div>
                    <strong><?= $register_section['title_sec'] ?></strong>
                </a>
            </li>
        </ul>
    </div>
</section>
<!-- *************Become a Partner section end************* -->
</div>
<script>
    var autocomplete_from;
    $(function () {
        $('#from').keyup(function (e) {
            buildAutoComplete("from", e, "<?= $MIN_CHAR_REQ_GOOGLE_AUTO_COMPLETE; ?>", "<?= $_SESSION['sess_lang']; ?>", function (latitude, longitude, address) {
                $("#from_lat").val(latitude);
                $("#from_long").val(longitude);
                $("#from_lat_long").val("(" + latitude + "," + longitude + ")");
                $('#from-error').remove();
            }); // (orignal function)
        });
        if (typeof $.fn.tooltip.noConflict === "function") {
            var bootstrapTooltip = $.fn.tooltip.noConflict();
            $.fn.bootstrapTooltip = bootstrapTooltip;
        }
        $(document).tooltip({
            position: {
                my: "center bottom-20",
                at: "center top",
                using: function (position, feedback) {
                    $(this).css(position);
                    $("<div>")
                        .addClass("arrow")
                        .addClass(feedback.vertical)
                        .addClass(feedback.horizontal)
                        .appendTo(this);
                }
            }
        });
        checkNavigatorPermissionStatus();
        setInterval(function () {
            checkNavigatorPermissionStatus();
        }, 1000);
    });
    /* for do not fire enter key to submit the form */
    document.getElementById('from').addEventListener('keypress', function (event) {
        if (event.keyCode == 13) {
            event.preventDefault();
        }
        //it is because only space is not allowed but write address at that time space is allowed..
        if (event.keyCode == 32 && $("#from").val() == '') {
            event.preventDefault();
        }
    });
    
    $('#from').on('keyup', function() {
        if($(this).val() == '') {
            $("#from_lat").val('');
            $("#from_long").val('');
            $("#from_lat_long").val('');
        }
    });

    function selectbookingorder(value) {
        console.log(value);
        if (value == 'Ride'  || value == 'Moto') {
            $("#_fare_estimate_form").attr("action", "cx-fareestimate.php");
            $("#from").attr("name", "vPickup");
            $("#etype").val(value);
            $("#navigatedPage").val(value);
            $('.goto-service-page').css('display', 'none');
        } else if (value == 'UberX' || value == 'Deliver') {
            /*$("#_fare_estimate_form").attr("action", "userbooking");
            $("#from").attr("name", "vPickup");
            $("#etype").val(value);
            $("#navigatedPage").val(value);*/
            $('.goto-service-page').css('display', 'flex');
        } else {
            <?php if(isset($_SESSION['sess_user']) && ($_SESSION['sess_user'] == "driver" || $_SESSION['sess_user'] == "company" || $_SESSION['sess_user'] == "organization" 
                            || $_SESSION['sess_user'] == "tracking_company")) { ?>
                $('.goto-service-page').css('display', 'flex');
            <?php } else { ?>
                $("#_fare_estimate_form").attr("action", "user_info_action.php");
                $("#from").attr("name", "vServiceAddress");
                $("#serviceid").val(value);
                $('.goto-service-page').css('display', 'none');
            <?php } ?>
        }
    }

    function checkNavigatorPermissionStatus() {
        navigator.permissions && navigator.permissions.query({name: 'geolocation'}).then(function (PermissionStatus) {
            if (PermissionStatus.state == 'granted') {
                $('.detect-loc').attr('title', '<?= addslashes($langage_lbl['LBL_FETCH_LOCATION_HINT']) ?>');
            } else if (PermissionStatus.state == 'prompt') {
                // prompt - not yet grated or denied
            } else {
                $('.detect-loc').attr('title', '<?= str_replace("#SITE_NAME#", $SITE_NAME, addslashes($langage_lbl['LBL_LOCATION_BLOCKED_MSG'])) ?>');
            }
        });
    }

    function fetchLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition, showError, {
                maximumAge: 0,
                timeout: 1000,
                enableHighAccuracy: true
            });
        }
    }

    function showPosition(position) {
        var geo_latitude = position.coords.latitude;
        var geo_longitude = position.coords.longitude;
        var geo_lat_lng = "(" + geo_latitude + "," + geo_longitude + ")";
        var oldlat = "";
        var oldlong = "";
        var oldlatlong = "";
        var oldAddress = "";
        SetGeoCookie('GEO_LATITUDE', geo_latitude, 1);
        SetGeoCookie('GEO_LONGITUDE', geo_longitude, 1);
        SetGeoCookie('GEO_LATLNG', geo_lat_lng, 1);
        $("#from_lat").val(geo_latitude);
        $("#from_long").val(geo_longitude);
        $("#from_lat_long").val(geo_lat_lng);
        $('.detect-loc').attr('title', '<?= addslashes($langage_lbl['LBL_FETCH_LOCATION_HINT']) ?>');
        getReverseGeoCode('from', 'from_lat_long', "<?=$_SESSION['sess_lang'];?>", geo_latitude, geo_longitude, oldlat, oldlong, oldlatlong, oldAddress, function (latitude, longitude, address) {
            $('#from').trigger('blur');
            $('#from-error').remove();
        });
    }

    function showError(error) {
        switch (error.code) {
            case error.PERMISSION_DENIED:
                $('.detect-loc').attr('title', '<?= str_replace("#SITE_NAME#", $SITE_NAME, addslashes($langage_lbl['LBL_LOCATION_BLOCKED_MSG'])) ?>');
                break;
            case error.POSITION_UNAVAILABLE:
                $('.detect-loc').attr('title', '<?= addslashes($langage_lbl['LBL_NO_LOCATION_FOUND_TXT']) ?>');
                break;
            case error.TIMEOUT:
                $('.detect-loc').attr('title', '<?= str_replace("#SITE_NAME#", $SITE_NAME, addslashes($langage_lbl['LBL_LOCATION_BLOCKED_MSG'])) ?>');
                break;
            case error.UNKNOWN_ERROR:
                $('.detect-loc').attr('title', '<?= str_replace("#SITE_NAME#", $SITE_NAME, addslashes($langage_lbl['LBL_LOCATION_BLOCKED_MSG'])) ?>');
                break;
        }
    }

    var allOptions = $("ul#standard-select").children('li');
    $("ul#standard-select").on("click", "li", function() {
        allOptions.removeClass('selected');
        $(this).addClass('selected');
        $('.selected_service').html($(this).html());
        $('.select_service').toggleClass('active');
        $('#servicename').val($(this).data('servicename'));
        selectbookingorder($(this).data('servicename'));
        $("a.goto-service-page").attr("href", $(this).data('url'));
    });
</script>