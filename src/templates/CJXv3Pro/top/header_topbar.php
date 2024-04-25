<?php
if (isset($_SESSION['sess_user'])) {
    if ($_SESSION['sess_user'] == 'company') {
        $sql = "select * from company where iCompanyId = '" . $_SESSION['sess_iUserId'] . "'";
        $db_user = $obj->MySQLSelect($sql);
    }
    if ($_SESSION['sess_user'] == 'driver') {
        $sql = "select * from register_driver where iDriverId = '" . $_SESSION['sess_iUserId'] . "'";
        $db_user = $obj->MySQLSelect($sql);
    }
    if ($_SESSION['sess_user'] == 'rider') {
        $sql = "select * from register_user where iUserId = '" . $_SESSION['sess_iUserId'] . "'";
        $db_user = $obj->MySQLSelect($sql);
    }
}
$col_class = "";
if ($user != "") {
    $col_class = "top-inner-color";
}
$langCodeArr = $db_cur_mst_arr = $db_lng_mst = array();
$logo = "logo.svg";
$db_lng_mst = $obj->MySQLSelect("select vTitle, vCode, vCurrencyCode, eDefault,vTitle_EN from language_master where eStatus='Active' ORDER BY iDispOrder ASC");
$count_lang = count($db_lng_mst);
for ($l = 0; $l < $count_lang; $l++) {
    $langCodeArr[$db_lng_mst[$l]['vCode']] = $db_lng_mst[$l]['vTitle'];
}
$db_cur_mst = $obj->MySQLSelect("SELECT iCurrencyId,eDefault,vName,vSymbol FROM currency WHERE eStatus='Active' ORDER BY iDispOrder ASC");
for ($cd = 0; $cd < count($Data_ALL_currency_Arr); $cd++) {
    if ($Data_ALL_currency_Arr[$cd]['eStatus'] == "Active") {
        $db_cur_mst_arr[] = $Data_ALL_currency_Arr[$cd];
    }
}
$count_cur = count($db_cur_mst_arr);
$languageText = "LANGUAGE";
if (isset($langCodeArr[$_SESSION['sess_lang']])) {
    $languageText = $langCodeArr[$_SESSION['sess_lang']];
}
$fromOrder = "guest";
if (isset($_REQUEST['order']) && $_REQUEST['order'] != "") {
    $fromOrder = $_REQUEST['order'];
} else if (isset($_SESSION['MANUAL_ORDER_USER'])) {
    $fromOrder = $_SESSION['MANUAL_ORDER_USER'];
}
$orderDetailsSession = "ORDER_DETAILS_" . strtoupper($fromOrder);
$orderServiceSession = "MAUAL_ORDER_SERVICE_" . strtoupper($fromOrder);
$userSession = "MANUAL_ORDER_" . strtoupper($fromOrder);
$orderServiceNameSession = "MANUAL_ORDER_SERVICE_NAME_" . strtoupper($fromOrder);
$orderLatitudeSession = "MANUAL_ORDER_LATITUDE_" . strtoupper($fromOrder);
$orderAddressSession = "MANUAL_ORDER_ADDRESS_" . strtoupper($fromOrder);
$orderStoreIdSession = "MANUAL_ORDER_STORE_ID_" . strtoupper($fromOrder);
//Added By HJ On 08-07-2019 For Hide/Show Order Address and Cart Icon Start
if (isset($_SESSION[$orderStoreIdSession]) && $_SESSION[$orderStoreIdSession] > 0) {
    $orderCompanyId = $_SESSION[$orderStoreIdSession];
} else if (isset($_REQUEST['id']) && $_REQUEST['id'] > 0) {
    $orderCompanyId = $_REQUEST['id'];
}
$orderItemCount = $confirlAlert = 0;
$orderItemListingUrl = $siteUrl . "order-items?order=" . $fromOrder;
$orderLogin = "order-items";
if ($fromOrder == 'store' || $fromOrder == 'admin') {
    $orderItemListingUrl = $siteUrl . "user-order-information?order=" . $fromOrder;
    $orderLogin = "user-order-information?order=" . $fromOrder;
}
if (isset($_SESSION[$orderDetailsSession]) && count($_SESSION[$orderDetailsSession]) > 0 && $orderCompanyId > 0) {
    $orderItems = $_SESSION[$orderDetailsSession];
    for ($d = 0; $d < count($orderItems); $d++) {
        if (isset($orderItems[$d]['typeitem']) && $orderItems[$d]['typeitem'] == "remove") {
            //Removed Items here
        } else {
            $orderItemCount += 1;
            $confirlAlert += 1;
        }
    }
    $orderItemListingUrl = $siteUrl . "store-items?order=" . $fromOrder . "&id=" . $orderCompanyId;
}
$addressPageArr = array("restaurant_listing.php", "restaurant_place-order.php", "restaurant_menu.php");
$addressEnable = $breadcumbArr = array();
//Added By HJ On 22-06-2019 For Display Breadcumb Start
for ($d = 0; $d < count($addressPageArr); $d++) {
    if (strpos($_SERVER['PHP_SELF'], $addressPageArr[$d]) !== false) {
        $addressEnable[] = 1;
        if ($addressPageArr[$d] == "restaurant_listing.php") {
            if ($fromOrder == 'store') {
                $breadcumbArr = array($langage_lbl['LBL_ORDER_ITEMS_MANUAL_TXT'] => $orderLogin);
            } else {
                $breadcumbArr = array($langage_lbl['LBL_ORDER_ITEMS_MANUAL_TXT'] => $orderLogin, $langage_lbl['LBL_STORE_LISTING_MANUAL_TXT'] => "store-listing");
            }
        } else if ($addressPageArr[$d] == "restaurant_menu.php") {
            if ($fromOrder == 'store') {
                $breadcumbArr = array($langage_lbl['LBL_ORDER_ITEMS_MANUAL_TXT'] => $orderLogin, $langage_lbl['LBL_STORE_ITEMS_MANUAL_TXT'] => $orderItemListingUrl);
            } else {
                $breadcumbArr = array($langage_lbl['LBL_ORDER_ITEMS_MANUAL_TXT'] => $orderLogin, $langage_lbl['LBL_STORE_LISTING_MANUAL_TXT'] => "store-listing", $langage_lbl['LBL_STORE_ITEMS_MANUAL_TXT'] => $orderItemListingUrl);
            }
        } else if ($addressPageArr[$d] == "restaurant_place-order.php") {
            if ($fromOrder == 'store') {
                $breadcumbArr = array($langage_lbl['LBL_ORDER_ITEMS_MANUAL_TXT'] => $orderLogin, $langage_lbl['LBL_STORE_ITEMS_MANUAL_TXT'] => $orderItemListingUrl, $langage_lbl['LBL_CHECKOUT_ORDER_MANUAL_TXT'] => "store-order");
            } else {
                $breadcumbArr = array($langage_lbl['LBL_ORDER_ITEMS_MANUAL_TXT'] => $orderLogin, $langage_lbl['LBL_STORE_LISTING_MANUAL_TXT'] => "store-listing", $langage_lbl['LBL_STORE_ITEMS_MANUAL_TXT'] => $orderItemListingUrl, $langage_lbl['LBL_CHECKOUT_ORDER_MANUAL_TXT'] => "store-order");
            }
        }
    }
}
$breadcumbArr = $service_categories = array();
$breadCumbCount = count($breadcumbArr);
//Added By HJ On 22-06-2019 For Display Breadcumb End
if (isset($serviceCategoriesTmp) && !empty($serviceCategoriesTmp)) {
    $service_categories = $serviceCategoriesTmp;
}

$selectedServiceId = 1;
if (isset($_SESSION[$orderServiceSession]) && $_SESSION[$orderServiceSession] > 0) {
    $selectedServiceId = $_SESSION[$orderServiceSession];
}
$service_data = $obj->MySQLSelect("SELECT iVehicleCategoryId FROM vehicle_category WHERE iServiceId = '$selectedServiceId' AND iParentId = '0' ");
$tableName = getAppTypeWiseHomeTable();
$book_data = $obj->MySQLSelect("SELECT booking_ids FROM $tableName WHERE vCode = '" . $_SESSION['sess_lang'] . "'");
$vcatdata_first = $vcatdata_sec = $vcatdata = array();
$serviceBidPage = 0;
$vcatdata_first = getSeviceCategoryDataForHomepage($book_data[0]['booking_ids'], 0, 1);

if (!empty($book_data[0]['booking_ids'])) {
    $cPage = 0; // for the medical services 
    $vcatdata_sec = getSeviceCategoryDataForHomepage($book_data[0]['booking_ids'], 1, 1);
}
$vcatdata = array_merge($vcatdata_first, $vcatdata_sec);
$iDisplayOrderHomepage = array_column($vcatdata, 'iDisplayOrderHomepage');
array_multisort($iDisplayOrderHomepage, SORT_ASC, $vcatdata);
/* ---------------------- for the new our service menu ---------------------- */
//  TODO: variable 
$newOurService = ourService();

$ourService = array();
if (isset($vcatdata) && !empty($vcatdata)) {
    foreach ($vcatdata as $v) {
        $ourService[$v['iVehicleCategoryId']] = $v;
    }
}


/* ---------------------- for the new our service menu ---------------------- */
$userTypeX = isset($_REQUEST['userType1']) ? $_REQUEST['userType1'] : '';
$userTypeOrderX = isset($_REQUEST['order']) ? $_REQUEST['order'] : '';
$otherservicepage = 0;
if (strpos($_SERVER['REQUEST_URI'], 'otherservices')) {
    $otherservicepage = 1;
}
$link_user = "sign-up?type=user";
$our_service_shown = "Yes";
$createorderlink = "order-items/" . $service_data[0]['iVehicleCategoryId'];
if ($THEME_OBJ->isDeliveryXThemeActive() == 'Yes') {
    $link_user = "sign-up?type=sender";
    $our_service_shown = "No";
}
if ($THEME_OBJ->isDeliverallXThemeActive() == 'Yes') {
    $our_service_shown = "No";
    $createorderlink = $tconfig['tsite_url'];
}
if ($THEME_OBJ->isRideDeliveryXThemeActive() == 'Yes' || $THEME_OBJ->isRideCXThemeActive() == 'Yes') {
    $our_service_shown = "No";
}
if(strtoupper(ENABLE_OUR_SERVICES_MENU) == strtoupper('No')){
    $our_service_shown = "No";
}
$singleStore = $MODULES_OBJ->isSingleStoreSelection();
$enableCategoryHeader = 1;
if ($singleStore > 0) {
    $enableCategoryHeader = 0;
}

$language_flag_code = strtolower($_SESSION['sess_lang']);
if(in_array($language_flag_code, ['enus'])) {
    $language_flag_code = "en-us";
} elseif (in_array($language_flag_code, ['enuk'])) {
    $language_flag_code = "en";
} elseif (in_array($language_flag_code, ['zhcn', 'zhtw', 'zhhk'])) {
    $language_flag_code = "zh";
} elseif (in_array($language_flag_code, ['zhsg'])) {
    $language_flag_code = "sg";
} elseif (in_array($language_flag_code, ['ptpt'])) {
    $language_flag_code = "pt";
} elseif (in_array($language_flag_code, ['ptbr'])) {
    $language_flag_code = "pt-br";
}
$PagesData = $obj->MySQLSelect("SELECT iPageId FROM `pages` WHERE iPageId IN (52) AND eStatus = 'Active' ");
if (!empty($_SESSION['sess_iAdminUserId']) && (strpos($_SERVER['SCRIPT_FILENAME'], 'userbooking.php') !== false || strpos($_SERVER['SCRIPT_FILENAME'], 'customer_info.php') !== false || strpos($_SERVER['SCRIPT_FILENAME'], 'restaurant_listing.php') !== false || strpos($_SERVER['SCRIPT_FILENAME'], 'restaurant_menu.php') !== false || strpos($_SERVER['SCRIPT_FILENAME'], 'restaurant_place-order.php') !== false || strpos($_SERVER['SCRIPT_FILENAME'], 'safety_measures.php') !== false) && ($userTypeX == "admin" || $userTypeOrderX == "admin")) { 
    include_once("admin_header_topbar.php");
} else if(strpos($_SERVER['SCRIPT_FILENAME'], 'reset_admin_password.php') !== false){ 
    include_once("admin_header_topbar.php");
} else {
    ?>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:100,300,400,500,600,700" rel="stylesheet">


    <header class="fixed <? if (!empty($_SESSION['sess_user'])) { ?> loggedin<?php } ?>">
        <div class="header-inner wrapper">
            <div class="header-left">
                <? if (!empty($_SESSION['sess_user']) && $_SESSION["IsTrackServiceDriver"] !=  'Yes') { ?>
                    <div class="menu-icoholder-side">
                        <i class="menu-ico">
                            <span></span>
                        </i>
                    </div>
                <?php }
                $url = explode('/', $_SERVER['REQUEST_URI']);
                $url_name = $url[count($url) - 1];

                $onclickLogoURL = trim($tconfig['tsite_url'], '/');
                $logo = "logo_top.png";
                if ($url_name == "index.php" || $url_name == '') {
                    $logo = "logo.svg";
                } $logo = "logo.svg";
                ?>
                <div class="logo">
                    <a href="<?= $onclickLogoURL; ?>">
                        <img src="<?= $tconfig['tsite_url'] ?>assets/img/apptype/<?php echo $template; ?>/<?php echo $logo; ?>" alt="" class="logo-1">
                    </a>
                </div>

                <?php if (function_exists('restaurant_listing_page')) { ?>
                    <ul class="navmenu-links location-service-element">
                        <li class="service-categories-dropdown">
                            <select name="servicename" id="servicename" onchange="resetServiceCatagory()"
                                    style="display: none;">
                                <?php
                                $selectedService = "";
                                for ($s = 0; $s < count($service_categories); $s++) {
                                    $iServiceId = $service_categories[$s]['iServiceId'];
                                    $selectedTxt = "";
                                    if ($selectedServiceId == $iServiceId) {
                                        $selectedTxt = "selected";
                                        $selectedService = ucfirst($service_categories[$s]['vServiceName']);
                                    }
                                    ?>
                                    <option value="<?php echo $iServiceId; ?>" <?= $selectedTxt; ?>
                                            data-servicename="<?php echo ucfirst($service_categories[$s]['vServiceName']); ?>"><?php echo ucfirst($service_categories[$s]['vServiceName']); ?> </option>
                                <?php } ?>
                            </select>
                            <a href="javascript:void(0);" id="servicenamedropdown"><?= $selectedService; ?> <i
                                        class="fa fa-chevron-down"></i></a>
                            <ul class="drop">
                                <?php
                                for ($s = 0; $s < count($service_categories); $s++) {
                                    $iServiceId = $service_categories[$s]['iServiceId'];
                                    $selectedclass = "";
                                    if ($selectedServiceId == $iServiceId) {
                                        $selectedclass = "active";
                                    }
                                    ?>
                                    <li data-value="<?php echo $iServiceId; ?>" class='<?= $selectedclass; ?>'
                                        data-servicename="<?php echo ucfirst($service_categories[$s]['vServiceName']); ?>"><?php echo ucfirst($service_categories[$s]['vServiceName']); ?>
                                    </li>
                                <?php } ?>
                            </ul>
                        </li>
                        <li class="address-nav">
                            <a href="javascript:void(0);" onclick="window.location.href = '<?= $createorderlink; ?>'">
                                <i class="fa fa-map-marker location-icon"></i>
                                <span title="<?= $_SESSION[$orderAddressSession]; ?>"><?= $_SESSION[$orderAddressSession]; ?></span>
                                <i class="fa fa-chevron-down"></i>
                            </a>
                        </li>
                    </ul>
                <?php } ?>
            </div>
            <ul class="mobile_language_cur">
                <?php if ($fromOrder != "admin") { if ($count_lang > 1) { ?>
                <li class="lang"><a><img src="<?= $tconfig['tsite_url'] ?>webimages/icons/language_flags/<?//= $language_flag_code ?>globe.svg" alt="" onerror="this.src='<?= $tconfig['tsite_url'] ?>webimages/icons/language_flags/globe.svg'" /><?= $_SESSION['sess_lang'] ?><img class="drop_arrow" src="<?= $tconfig['tsite_url'] ?>assets/img/apptype/<?php echo $template; ?>/drop_arrow.svg" alt=""/></a>
                    <? if (count($db_lng_mst) == 1) { ?><?php } else { ?>
                        <div class="dropdown-content 555555">
                            <div class="row">
                                <h3>
                                    <?= $langage_lbl['LBL_SELECT_LANGUAGE_TXT'] ?>
                                     <span>×</span>
                                </h3>
                                <?php $i = 0;
                                echo '<div class="column">';
                                foreach ($db_lng_mst as $key => $value) { 
                                    $language_flag_code_sel = strtolower($value['vCode']);
                                    if(in_array($language_flag_code_sel, ['enus'])) {
                                        $language_flag_code_sel = "en-us";
                                    } elseif (in_array($language_flag_code_sel, ['enuk'])) {
                                        $language_flag_code_sel = "en";
                                    } elseif (in_array($language_flag_code_sel, ['zhcn', 'zhtw', 'zhhk'])) {
                                        $language_flag_code_sel = "zh";
                                    } elseif (in_array($language_flag_code_sel, ['zhsg'])) {
                                        $language_flag_code_sel = "sg";
                                    } elseif (in_array($language_flag_code_sel, ['ptpt'])) {
                                        $language_flag_code_sel = "pt";
                                    } elseif (in_array($language_flag_code_sel, ['ptbr'])) {
                                        $language_flag_code_sel = "pt-br";
                                    } ?>
                                    <a href="javascript:void(0)" onclick="change_language('<?php echo $value['vCode']; ?>')" <?php if ($value['vCode'] == $_SESSION['sess_lang']) { ?> class="active" <? } ?>>
                                        <b><img src="<?= $tconfig['tsite_url'] ?>webimages/icons/language_flags/<?= $language_flag_code_sel ?>.svg" alt="" onerror="this.src='<?= $tconfig['tsite_url'] ?>webimages/icons/language_flags/globe.svg'" /></b>
                                        <?php if($value['vTitle'] != $value['vTitle_EN']){?>
                                            <?php echo ucfirst(strtolower($value['vTitle_EN'])); ?> <small>(<?php echo ucfirst(strtolower($value['vTitle']));?>)</small> 
                                        <?php } else { ?>
                                            <?php echo ucfirst(strtolower($value['vTitle_EN'])); ?>
                                        <?php } ?>
                                        <!-- <?php echo ucfirst(strtolower($value['vTitle'])); ?> --></a>
                                <?php }
                                echo '</div>'; ?>
                            </div>
                        </div>
                    <? } ?>
                <?php } } ?>
                </li>
                <li class="curr">
                    <a><img src="<?= $tconfig['tsite_url'] ?>assets/img/currency-note.svg" alt=""><?= strtoupper($_SESSION['sess_currency']) ?><img class="drop_arrow" src="<?= $tconfig['tsite_url'] ?>assets/img/apptype/<?php echo $template; ?>/drop_arrow.svg" alt=""></a>
                    <?php if (count($db_cur_mst_arr) == 1) { ?><?php } else { ?>
                        <div class="dropdown-content">
                            <div class="row">
                                <h3>
                                    <?= $langage_lbl['LBL_SELECT_CURRENCY'] ?>
                                     <span>×</span>
                                </h3>
                                <div class="column">
                                    <?php foreach ($db_cur_mst_arr as $key => $value) { ?>
                                    <a href="javascript:void(0)" onclick="change_curr('<?php echo $value['vName']; ?>')" <?php if ($value['vName'] == $_SESSION['sess_currency']) { ?> class="active" <? } ?>>
                                        <?php echo strtoupper($value['vName']); ?> <small>(<?php echo $value['vSymbol']; ?>)</small></a>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </li>
            </ul>
            <div class="menu-icoholder">
                <i class="menu-ico">
                    <span></span>
                </i>
            </div>
            <div class="header-right">
                <div class="header-menu">
                <?php if (function_exists('restaurant_listing_page')) { } else { $onclickLogoURL = $tconfig['tsite_url']; ?>
                    <ul class="navmenu-links nav-links">
                        <li>
                            <a href="<?= $onclickLogoURL; ?>" <? if ($url_name == "index.php" || $url_name == '') { ?> class="active" <?php } ?>><?= $langage_lbl['LBL_HOME']; ?></a>
                        </li>
                        <? if ($our_service_shown == "Yes") { ?>
                            <!-- New Menu -->
                            <li class="has-level-menu">
                                <!-- TODO: menu -->
                                <a onclick="javascript:void(0);"><?= $langage_lbl['LBL_OUR_PRODUCTS']; ?></a>
                                <div class="dropdown-content">
                                    <div class="row">
                                        <h3><?= $langage_lbl['LBL_SELECT_SERVICE_TXT'] ?><span>&#215;</span></h3>
                                        <div class="column">


                                            <?php

                                        

                                            if (isset($newOurService) && !empty($newOurService)) {
                                                foreach ($newOurService as $newOs) {
                                                    if (count($newOs['subcategory']) > 0) { ?>
                                                        <strong class="subcategoryptitle"><?php echo $newOs['vTitle'] ?></strong>
                                                        <?php if ($newOs['eType'] == "MedicalServices") { ?>
                                                            <div class="secondlavelcatrow">
                                                        <?php }
                                                        foreach ($newOs['subcategory'] as $subcategory) {
                                                            if(isset($subcategory['iVehicleCategoryId']) && in_array($subcategory['iVehicleCategoryId'], [339])) {
                                                                continue;
                                                            }
                                                            $url = "javascript:void(0);";
                                                            if(isset($subcategory['iVehicleCategoryId']) && isset($ourService[$subcategory['iVehicleCategoryId']])) {
                                                                $url = $ourService[$subcategory['iVehicleCategoryId']]['url'];    
                                                            }
                                                            
                                                            $Title = isset($subcategory['vCategory']) ? $subcategory['vCategory'] : "";
                                                            if(isset($subcategory['iVehicleCategoryId']) && in_array($subcategory['iVehicleCategoryId'], [182, 280])) {
                                                                $url = 'services/' . replace_content($Title) . '/' . $subcategory['iVehicleCategoryId'];
                                                            }
                                                            if (isset($subcategory['vTitle']) && !empty($subcategory['vTitle'])) {
                                                                $Title = $subcategory['vTitle'];
                                                            }
                                                            if ($newOs['eType'] == 'VideoConsult') {
                                                                $url = 'video-consulting/' . replace_content($Title) . '/' . $subcategory['iServiceId'];
                                                            }
                                                            if ($newOs['eType'] == 'Bidding') {
                                                                $url = 'service-bid/' . replace_content($Title) . '/' . $subcategory['iServiceId'];
                                                            }
                                                            if (empty($subcategory['Services'])) {
                                                                ?>
                                                                <a href="<?php echo $url; ?>" class="" data-name="<?php echo $Title ?>" target="_blank"> <?php echo $Title; ?></a>
                                                            <?php } else { ?>
                                                                <div class="thirdlavelcatblock">
                                                                    <strong class="thirdlavelcattitle"><?php echo $Title ?></strong>
                                                                    <?php
                                                                    foreach ($subcategory['Services'] as $Services) {

                                                                        $Title = $Services['vCategory'];
                                                                        if (isset($Services['vTitle']) && !empty($Services['vTitle'])) {
                                                                            $Title = $Services['vTitle'];
                                                                        }
                                                                        $url = "";
                                                                        if(isset($Services['iVehicleCategoryId']) && isset($ourService[$Services['iVehicleCategoryId']])) {
                                                                            $url = $ourService[$Services['iVehicleCategoryId']]['url'];
                                                                        }                                                                        
                                                                        if (empty($url)) {
                                                                            $url = 'medical-services/' . replace_content($Title) . '/' . $Services['iServiceId'];
                                                                        }
                                                                        if ($Services['eVideoConsultEnable'] == 'Yes') {
                                                                            $url = 'video-consulting/' . replace_content($Title) . '/' . $Services['iServiceId'];
                                                                        }
                                                                        ?>
                                                                        <a href="<?php echo $url; ?>" class="" data-name="<?php echo $Title ?>" target="_blank"><?php echo $Title; ?></a>
                                                                    <?php } ?>
                                                                </div>
                                                            <?php }
                                                        } ?>
                                                        <?= $newOs['eType'] == "MedicalServices" ? '</div>' : '' ?>
                                                    <?php } ?>
                                                        
                                                    <?php 
                                                }
                                            } ?>
                                            </div>
                                        </div>
                                    </div>
                            </li>
                            <!-- New Menu End -->
                            <li>                                
                                <!-- <a class="" href="javascript:void(0);" <? if ($url_name == "earn") { ?> class="active" <?php } ?>><?php echo $langage_lbl['LBL_FOOTER_LINK_EARN']; ?> <img class="drop_arrow" src="<?= $tconfig['tsite_url'] ?>assets/img/apptype/<?php echo $template; ?>/drop_arrow.svg" alt=""/></a> -->

                                <a href="#" class="desktop-item"><?= $langage_lbl['LBL_FOOTER_LINK_EARN']; ?>
                                <img class="drop_arrow" src="<?= $tconfig['tsite_url'] ?>assets/img/apptype/<?php echo $template; ?>/drop_arrow.svg" alt="">
                                </a>
                                    <input type="checkbox" id="showDrop">
                                <label for="showDrop" class="mobile-item"><?= $langage_lbl['LBL_FOOTER_LINK_EARN']; ?>
                                <img class="drop_arrow" src="<?= $tconfig['tsite_url'] ?>assets/img/apptype/<?php echo $template; ?>/drop_arrow.svg" alt="">
                                </label> 
                                <ul class="drop-menu">
                                    <h3><?= $langage_lbl['LBL_JOIN_US_TXT']; ?></h3>
									<li><a href="DriverPartner" class=""><?= $langage_lbl['LBL_DRIVER_PARTNER_TXT']; ?></a></li>
									<li><a href="DeliverPartner" class=""><?= $langage_lbl['LBL_DELIVER_PARTNER_TXT']; ?></a></li>
									<li><a href="ServicePartner" class=""><?= $langage_lbl['LBL_SERVICE_PARTNER_TXT']; ?></a></li>
									<li><a href="MerchantPartner" class=""><?= $langage_lbl['LBL_MERCHANT_PARTNER_TXT']; ?></a></li> 
                                </ul>
                            </li>
                         <? } ?>
                     </ul>
                <?php } ?>
                <ul>
                    <?php
                    if (function_exists('restaurant_listing_page')) {
                        ?>
                        <li class="service-location-icon">
                            <a href="javascript:void(0);" onclick="window.location.href = '<?= $createorderlink ?>'">
                                <img src="<?= $tconfig['tsite_url'] ?>assets/img/location-on-map.svg" alt=""/>
                                <?= $langage_lbl['LBL_LOCATION_FOR_FRONT'] ?>
                            </a>
                        </li>
                    <?php } ?>
                    <?php if ($fromOrder != "admin") {
                        if ($count_lang > 1) { ?>
                            <li class="lang" id = "mainLangDropdown">
                                <?php if ($fromOrder != "admin") { ?>
                                    <div class="dynamic-data" style="display: none">
                                        <select name="language" id="lang_select" onchange="change_lang(this.value);">
                                            <?php
                                            $srNo = 1;
                                            foreach ($db_lng_mst as $key => $value) {
                                                $totlLang = count($db_lng_mst);
                                                $status_lang = "";
                                                if ($_SESSION['sess_lang'] == $value['vCode']) {
                                                    $status_lang = "selected";
                                                }
                                                $addStyle = "";
                                                if ($totlLang == $srNo && SITE_TYPE != "Demo") {
                                                    $addStyle = 'style="width:14.6%;"';
                                                }
                                                $srNo++;
                                                ?>
                                                <option <?php echo $status_lang; ?>
                                                        value="<?php echo $value['vCode']; ?>"><?php echo ucfirst(strtolower($value['vTitle'])); ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                <?php } ?>
                                <a><img src="<?= $tconfig['tsite_url'] ?>webimages/icons/language_flags/<?//= $language_flag_code ?>globe.svg" alt="" onerror="this.src='<?= $tconfig['tsite_url'] ?>webimages/icons/language_flags/globe.svg'" /><?= $_SESSION['sess_lang'] ?><img class="drop_arrow" src="<?= $tconfig['tsite_url'] ?>assets/img/apptype/<?php echo $template; ?>/drop_arrow.svg" alt=""/></a>
                                <? if (count($db_lng_mst) == 1) { ?><?php } else { ?>

                                    <div id = "langDropdown" class="dropdown-content">
                                        <div class="row">
                                            <h3>
                                                <?= $langage_lbl['LBL_SELECT_LANGUAGE_TXT'] ?>
                                            </h3>
                                            <?php $i = 0; 
                                            echo '<div class="column">';
                                            foreach ($db_lng_mst as $key => $value) { 
                                                $language_flag_code_sel = strtolower($value['vCode']);
                                                if(in_array($language_flag_code_sel, ['enus'])) {
                                                    $language_flag_code_sel = "en-us";
                                                } elseif (in_array($language_flag_code_sel, ['enuk'])) {
                                                    $language_flag_code_sel = "en";
                                                } elseif (in_array($language_flag_code_sel, ['zhcn', 'zhtw', 'zhhk'])) {
                                                    $language_flag_code_sel = "zh";
                                                } elseif (in_array($language_flag_code_sel, ['zhsg'])) {
                                                    $language_flag_code_sel = "sg";
                                                } elseif (in_array($language_flag_code_sel, ['ptpt'])) {
                                                    $language_flag_code_sel = "pt";
                                                } elseif (in_array($language_flag_code_sel, ['ptbr'])) {
                                                    $language_flag_code_sel = "pt-br";
                                                } ?>
                                                <a href="javascript:void(0)" onclick="change_language('<?php echo $value['vCode']; ?>')" <?php if ($value['vCode'] == $_SESSION['sess_lang']) { ?> class="active" <? } ?>>
                                                    <b><img src="<?= $tconfig['tsite_url'] ?>webimages/icons/language_flags/<?= $language_flag_code_sel ?>.svg" alt="" onerror="this.src='<?= $tconfig['tsite_url'] ?>webimages/icons/language_flags/globe.svg'" /></b>
                                                   <!--  <?php echo ucfirst(strtolower($value['vTitle'])); ?> --> 
                                                   <?php if($value['vTitle'] != $value['vTitle_EN']){?>
                                                        <?php echo ucfirst(strtolower($value['vTitle_EN'])); ?> <small>(<?php echo ucfirst(strtolower($value['vTitle']));?>)</small>
                                                    <?php } else { ?>
                                                        <?php echo ucfirst(strtolower($value['vTitle_EN'])); ?>
                                                    <?php } ?>
                                            </a>
                                            <?php }
                                            echo '</div>'; ?>
                                        </div>
                                    </div>
                                <? } ?>
                            </li>
                            <li class="curr" id = "mainCurrDropdown">
                                <a><img src="<?= $tconfig['tsite_url'] ?>assets/img/currency-note.svg" alt=""><?= strtoupper($_SESSION['sess_currency']) ?><img class="drop_arrow" src="<?= $tconfig['tsite_url'] ?>assets/img/apptype/<?php echo $template; ?>/drop_arrow.svg" alt=""/></a>
                                <?php if (count($db_cur_mst_arr) == 1) { ?><?php } else { ?>

                                    <div class="dropdown-content" id = "currDropdown">
                                        <div class="row">
                                            <h3>
                                                <?= $langage_lbl['LBL_SELECT_CURRENCY'] ?>
                                            </h3>
                                            <div class="column">
                                                <?php foreach ($db_cur_mst_arr as $key => $value) { ?>
                                                <a href="javascript:void(0)" onclick="change_curr('<?php echo $value['vName']; ?>')" <?php if ($value['vName'] == $_SESSION['sess_currency']) { ?> class="active" <? } ?>>
                                                    <?php echo strtoupper($value['vName']); ?> <small>(<?php echo $value['vSymbol']; ?>)</small></a>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </li>
                        <?php }
                        if (!empty($_SESSION[$orderLatitudeSession]) && ($_SESSION[$userSession] == 'user' || $_SESSION[$userSession] == 'guest') || (isset($_SESSION['sess_iAdminUserId']) && $_SESSION['sess_iAdminUserId'] > 0)) { ?>
                            <?php if (in_array(1, $addressEnable) && $enableCategoryHeader > 0) { ?>
                            <?php } else if ($orderItemCount > 0) { ?>
                                <li>
                                    <a href="<?= $orderItemListingUrl; ?>">
                                        <span class="deliveraddressIcon-clo-av">
                                            <span class="header_cart_icon">
                                                <img class="cart" src="<?= $tconfig['tsite_url'] ?>assets/img/apptype/<?php echo $template; ?>/cart-icon.svg" alt=""/>
                                            </span>
                                            <b class="cart_b">
                                                <h5 class="cart_count"><?= $orderItemCount; ?></h5>
                                            </b>
                                        </span>
                                    </a>
                                </li>
                                <?php
                            }
                        }
                        ?>
                        <?php
                    }
                    if (!empty($_SESSION['sess_user'])) { ?>
                        <li class="login active">
                            <a href="Logout"><img src="<?= $tconfig['tsite_url'] ?>assets/img/apptype/<?php echo $template; ?>/power.svg" alt=""/><?= $langage_lbl['LBL_HEADER_LOGOUT']; ?></a>
                        </li>
                    <?php } else { ?>
                        <li class="login <?php if (strpos($_SERVER['REQUEST_URI'], 'sign-in')) {
                            echo 'active';
                        } else if (strpos($_SERVER['REQUEST_URI'], 'sign-up-rider') || strpos($_SERVER['REQUEST_URI'], 'sign-up?type=user') || strpos($_SERVER['REQUEST_URI'], 'sign-up?type=sender')) {
                        } else {
                            echo "active";
                        } ?>">
                            <a href="sign-in"><?= $langage_lbl['LBL_HEADER_TOPBAR_SIGN_IN_TXT']; ?></a>
                        </li>
                        <li class="login <?php if (strpos($_SERVER['REQUEST_URI'], 'sign-up-rider') || strpos($_SERVER['REQUEST_URI'], 'sign-up?type=user') || strpos($_SERVER['REQUEST_URI'], 'sign-up?type=sender')) {
                            echo 'active';
                        } ?>"><a href="<?= $link_user ?>"><?= $langage_lbl['LBL_SIGNUP']; ?></a></li>
                    <?php } ?>
                </ul>
                </div>
            </div>
        </div>
    </header>

<?php } ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js"></script><!-- used for cookie for otherservice clicked and stored in footer home file-->
<script>

    $(document).ready(function(){
        $("#mainextraDropdown").click(function(){
            $("#mainextraDropdown").addClass("active");
        });
    });

    $(document).ready(function(){
        $("#mainextraDropdown span").click(function(){
            $("#mainextraDropdown span").addClass("close");
        });
    });
    
    $(document).ready(function(){
        $("#mainextraDropdown span").click(function(){
            $("#mainextraDropdown span").addClass("close");
        });
    });

            $(document).ready(function(){
                $("#mainextraDropdown span").click(function(){
                $("p").toggleClass("taggle");
            });

            $("#mainextraDropdown h3").click(function(){
                $("#mainextraDropdown .active .level-menu").css("display", "none");
                });
            });       
            

    function change_language(lang) {
        $('#lang_select').val(lang).trigger('change');
    }

    function change_curr(currency) {
        var ajaxData = {
            'URL': '<?= $tconfig['tsite_url'] ?>ajax_fpass_action.php',
            'AJAX_DATA': {
                action: 'changecurrency',
                q: currency
            },
            'REQUEST_DATA_TYPE': 'json'
        };
        getDataFromAjaxCall(ajaxData, function (response) {
            if (response.action == "1") {
                var data = response.result;
                location.reload();
            } else {
                console.log(response.result);
            }
        });
    }

    // $(document).ready(function(){
    //     if ($('header').length > 0) {
    //         window.onscroll = function () {
    //             myFunctionHeader();
    //         };
    //         var header = $('header');
    //         var sticky = header.offset().top;
    //         function myFunctionHeader() {
    //             if ($(window).scrollTop() > sticky) {
    //                 header.addClass("sticky");
    //             } else {
    //                 header.removeClass("sticky");
    //             }
    //         }
    //     }
    // });
    <? if ($otherservicepage == 0) { ?>
    $.cookie('ServiceName', null);
    <? } ?>
</script>
<?php
$store_order = "";
$ignore_files_arr = array('store-items', 'store-order');
if (stripos_arr($_SERVER['REQUEST_URI'], $ignore_files_arr) === true) {
    $store_order = "store-order";
}
?>
<div class="dashboard <?= $store_order ?>" id="wrapper">
