<?php
include_once("common.php");
$showSignRegisterLinks = 1;

$table_name = getContentCMSHomeTable();
$vCode = $_SESSION['sess_lang'];
$eFor = 'DeliverAll';
$_SESSION["navigatedPage"] = 'UberX';
if($_REQUEST['iVehicleCategoryId']==295) {
    $_SESSION["navigatedPage"] = 'Ride';    
}

$ride_data = array();
if(ENABLE_DYNAMIC_CREATE_PAGE=="Yes") {
    $sql_ufx_dynamic = " AND iVehicleCategoryId = ".$_REQUEST['iVehicleCategoryId'];
    $ride_data_query = "SELECT * FROM ".$table_name." WHERE eFor = '" . $eFor . "'".$sql_ufx_dynamic;
    $ride_data = $obj->MySQLSelect($ride_data_query);
    $catname = getCatNameForTitle($_REQUEST['iVehicleCategoryId']);
}
$emptyData = 0;
if(empty($ride_data) || empty($ride_data[0]['lBannerSection'])) {
    if($THEME_OBJ->isCJXDoctorv2ThemeActive()=='Yes') {
        $emptyData = 1;
    }
    $sql_ufx_dynamic = " AND iVehicleCategoryId = 0";
    $ride_data_query = "SELECT * FROM ".$table_name." WHERE eFor = '" . $eFor . "'".$sql_ufx_dynamic;
    $ride_data = $obj->MySQLSelect($ride_data_query);
}

$banner_section = json_decode($ride_data[0]['lBannerSection'],true);
if(empty($banner_section['title_'.$vCode])) {
    $vCode = 'EN';
}

$inner_key = array('menu_title_','title_','sub_title_','desc_','img_','title_first_','desc_first_','img_first_','title_sec_','desc_sec_','img_sec_','title_third_','desc_third_','img_third_','title_fourth_','desc_fourth_','img_fourth_','title_fifth_','desc_fifth_','img_fifth_','title_six_','desc_six_','img_six_','main_title_','main_desc_','img2_','hiw_img1_','hiw_img2_','hiw_img3_','hiw_img4_','hiw_img5_','hiw_img6_','cuisines_img1_','cuisines_img2_','cuisines_img3_','cuisines_img4_','cuisines_img5_','cuisines_img6_');

$download_section = $LANG_OBJ->checkOtherLangDataExist(json_decode($ride_data[0]['lDownloadappSection'],true),$vCode,$inner_key);
$secure_section = $LANG_OBJ->checkOtherLangDataExist(json_decode($ride_data[0]['lSecuresafeSection'],true),$vCode,$inner_key);
$banner_section = $LANG_OBJ->checkOtherLangDataExist(json_decode($ride_data[0]['lBannerSection'],true),$vCode,$inner_key);
$how_it_work_section = $LANG_OBJ->checkOtherLangDataExist(json_decode($ride_data[0]['lHowitworkSection'],true),$vCode,$inner_key);
$call_section = $LANG_OBJ->checkOtherLangDataExist(json_decode($ride_data[0]['lCalltobookSection'],true),$vCode,$inner_key);
$earn_section = $LANG_OBJ->checkOtherLangDataExist(json_decode($ride_data[0]['lEarnSection'],true),$vCode,$inner_key);
$calculate_section = $LANG_OBJ->checkOtherLangDataExist(json_decode($ride_data[0]['lCalculateSection'],true),$vCode,$inner_key);
$cartype_section = $LANG_OBJ->checkOtherLangDataExist(json_decode($ride_data[0]['lCartypeSection'],true),$vCode,$inner_key);
$service_section = $LANG_OBJ->checkOtherLangDataExist(json_decode($ride_data[0]['lServiceSection'],true),$vCode,$inner_key);
$benefit_section = $LANG_OBJ->checkOtherLangDataExist(json_decode($ride_data[0]['lBenefitSection'],true),$vCode,$inner_key);
//echo "<pre>";print_r($benefit_section);die;

$menutitleHow = !empty($how_it_work_section['menu_title_'.$vCode]) ? $how_it_work_section['menu_title_'.$vCode] : $how_it_work_section['title_'.$vCode]; 
$menutitleSecure = !empty($secure_section['menu_title_'.$vCode]) ? $secure_section['menu_title_'.$vCode] : $secure_section['title_'.$vCode]; 
$menutitleDown = !empty($download_section['menu_title_'.$vCode]) ? $download_section['menu_title_'.$vCode] : $download_section['title_'.$vCode]; 
$menutitleCall = !empty($call_section['menu_title_'.$vCode]) ? $call_section['menu_title_'.$vCode] : $call_section['title_'.$vCode]; 
$menutitleEarn = !empty($earn_section['menu_title_'.$vCode]) ? $earn_section['menu_title_'.$vCode] : $earn_section['title_'.$vCode]; 
$menutitleCalc = !empty($calculate_section['menu_title_'.$vCode]) ? $calculate_section['menu_title_'.$vCode] : $calculate_section['title_'.$vCode]; 
$menutitleCar = !empty($cartype_section['menu_title_'.$vCode]) ? $cartype_section['menu_title_'.$vCode] : $cartype_section['title_'.$vCode];
$menutitleService = !empty($service_section['menu_title_'.$vCode]) ? $service_section['menu_title_'.$vCode] : $service_section['title_'.$vCode];
$menutitleBenefit = !empty($benefit_section['menu_title_'.$vCode]) ? $benefit_section['menu_title_'.$vCode] : $benefit_section['title_'.$vCode];

$btitle = $bdesc = $bimg = array();
if(!empty($benefit_section['title_first_'.$vCode])) {
    $btitle[] = $benefit_section['title_first_'.$vCode];
    $bdesc[] = $benefit_section['desc_first_'.$vCode];
    $bimg[] = $benefit_section['img_first_'.$vCode];
}
if(!empty($benefit_section['title_sec_'.$vCode])) {
    $btitle[] = $benefit_section['title_sec_'.$vCode];
    $bdesc[] = $benefit_section['desc_sec_'.$vCode];
    $bimg[] = $benefit_section['img_sec_'.$vCode];
}
if(!empty($benefit_section['title_third_'.$vCode])) {
    $btitle[] = $benefit_section['title_third_'.$vCode];
    $bdesc[] = $benefit_section['desc_third_'.$vCode];
    $bimg[] = $benefit_section['img_third_'.$vCode];
}
if(!empty($benefit_section['title_fourth_'.$vCode])) {
    $btitle[] = $benefit_section['title_fourth_'.$vCode];
    $bdesc[] = $benefit_section['desc_fourth_'.$vCode];
    $bimg[] = $benefit_section['img_fourth_'.$vCode];
}
if(!empty($benefit_section['title_fifth_'.$vCode])) {
    $btitle[] = $benefit_section['title_fifth_'.$vCode];
    $bdesc[] = $benefit_section['desc_fifth_'.$vCode];
    $bimg[] = $benefit_section['img_fifth_'.$vCode];
}
if(!empty($benefit_section['title_six_'.$vCode])) {
    $btitle[] = $benefit_section['title_six_'.$vCode];
    $bdesc[] = $benefit_section['desc_six_'.$vCode];
    $bimg[] = $benefit_section['img_six_'.$vCode];
}
$key_arr = array("#SUPPORT_PHONE#","#SUPPORT_ADDRESS#","#SUPPORT_EMAIL#","#ANDROID_APP_LINK#","#IPHONE_APP_LINK#");
$val_arr = array($SUPPORT_PHONE,$COMPANY_ADDRESS,$SUPPORT_MAIL,$ANDROID_APP_LINK,$IPHONE_APP_LINK);
//if($emptyData==1) {
//    
//   echo "<section class='no-data'><div class='no-data-inner'><h1>".$langage_lbl['LBL_CONTENT_GOES_HERE']."</h1></div></section>"; 
//} else {
?>
<!-- *************banner section start************* -->
<section class="banner-section taxi-app bannermenu">
    <div class="tab-row-holding">
        <ul class="tab-row">
            <? if($emptyData==1) { } else { ?>
            <li data-src="3" class="tab">
                <a href="#our-restaurant"><?= $menutitleService; ?></a>
            </li>
            <? } ?>
            <li data-src="6" class="tab">
                <a href="#download-apps"><?= $menutitleDown ?></a>
            </li>
            <li data-src="7" class="tab">
                <a href="#contact-sec"><?= $menutitleSecure ?></a>
            </li>
            
            <!-- <li class="tab mob">
                <a href="#">&nbsp</a>
            </li> -->
        </ul>
    </div>
    <div class="banner-section-inner">
        <div class="categories-block">
            <div class="categories-caption active">
                <h2><?php echo $banner_section['title_'.$vCode];?> <span><?php echo $banner_section['sub_title_'.$vCode];?></span></h2>
                <?php echo $banner_section['desc_'.$vCode];?>
            </div>
        </div>
    </div>
    <div class="banner-back">
        <div class="banner-image" id="1" style="background-image: url(<?php  echo $tconfig["tsite_upload_apptype_page_images"].$template.'/'.$banner_section['img_'.$vCode]; ?>); display: block;"></div>
    </div>
</section>
<!-- *************banner section end************* -->
<? if($emptyData == 1) { } else { ?>
<!-- *************restaurant section start************* -->
<section class="restaurant page-section" id="our-restaurant">
    <div class="restaurant-inner">
        <h3><?= $service_section['main_title_'.$vCode]; ?></h3>
        <!-- <strong><?= $service_section['main_desc_'.$vCode]; ?></strong> -->
        <ul>
                <?php if(!empty($service_section['title_first_'.$vCode])) { ?>

                <li>
                    <div class="rest-caption" style="background-image:url('<?php  echo $tconfig["tsite_upload_apptype_page_images"].$template.'/'.$service_section['img_first_'.$vCode]; ?>');">
                        <div class="box-content">
                            <strong><?= $service_section['title_first_'.$vCode]; ?></strong>
                            <p><?= $service_section['desc_first_'.$vCode]; ?></p>
                        </div>
                    </div>
                    <div class="rest-caption">
                        <strong><?= $service_section['title_first_'.$vCode]; ?></strong>
                        <p><?= $service_section['desc_first_'.$vCode]; ?></p>
                    </div>
                </li>

                <?php } if(!empty($service_section['title_sec_'.$vCode])) { ?>

                <li>
                    <div class="rest-caption" style="background-image:url('<?php  echo $tconfig["tsite_upload_apptype_page_images"].$template.'/'.$service_section['img_sec_'.$vCode]; ?>');">
                        <div class="box-content">
                            <strong><?= $service_section['title_sec_'.$vCode]; ?></strong>
                            <p><?= $service_section['desc_sec_'.$vCode]; ?></p>
                        </div>
                    </div>
                    <div class="rest-caption">
                        <strong><?= $service_section['title_sec_'.$vCode]; ?></strong>
                        <p><?= $service_section['desc_sec_'.$vCode]; ?></p>
                    </div>
                </li>

                <?php } if(!empty($service_section['title_third_'.$vCode])) { ?>

                <li>
                    <div class="rest-caption" style="background-image:url('<?php  echo $tconfig["tsite_upload_apptype_page_images"].$template.'/'.$service_section['img_third_'.$vCode]; ?>');">
                        <div class="box-content">
                            <strong><?= $service_section['title_third_'.$vCode]; ?></strong>
                            <p><?= $service_section['desc_third_'.$vCode]; ?></p>                        
                        </div>
                    </div>
                    <div class="rest-caption">
                        <strong><?= $service_section['title_third_'.$vCode]; ?></strong>
                        <p><?= $service_section['desc_third_'.$vCode]; ?></p>  
                    </div>
                </li>
                <?php } ?>  
        </ul>
    </div>
</section>
<!-- *************restaurant section End ************* -->
<? } ?>
<!-- *************download section section start************* -->
<?php
$tMessage_link1 = $download_section['link1_'.$vCode];
$tMessage_link1 = str_replace($key_arr, $val_arr, $tMessage_link1);
$tMessage_link2 = $download_section['link2_'.$vCode];
$tMessage_link2 = str_replace($key_arr, $val_arr, $tMessage_link2); 
?>
<section class="get_app_area sec_pad page-section" id="download-apps">
    <div class="get_app_area-inner">
        <div class="get_app_area-left">
            <div class="get_app_content">
                <div class="section_title">
                    <h2><?php echo $download_section['title_'.$vCode];?></h2>
                </div>
                <?php echo $download_section['desc_'.$vCode];?>
                <a href="<?= $tMessage_link1; ?>" class="app_btn slider_btn" target="_blank"><img src="assets/img/apptype/<?php echo $template;?>/play-store.png" alt=""><?=$langage_lbl['LBL_GOOGLE_PLAY']; ?></a>
                <a href="<?= $tMessage_link2; ?>" class="app_btn_two slider_btn" target="_blank"><img src="assets/img/apptype/<?php echo $template;?>/apple-store.png" alt=""><?=$langage_lbl['LBL_APP_STORE']; ?></a>
            </div>
        </div>
        <div class="get_app_area-right app_image">
            <div class="image_first">
                <img src="<?php  echo $tconfig["tsite_upload_apptype_page_images"].$template.'/'.$download_section['img_'.$vCode]; ?>" alt="">
                <div class="shadow_bottom"></div>
            </div>
            <div class="image_two">
                <img src="<?php  echo $tconfig["tsite_upload_apptype_page_images"].$template.'/'.$download_section['img2_'.$vCode]; ?>" alt="">
                <div class="shadow_bottom"></div>
            </div>
        </div>
    </div>
</section>
<!-- *************download section section end************* -->

<!-- *************contact section start************* -->
<?php
$tMessage_sec = $secure_section['desc_'.$vCode];
$tMessage_sec = str_replace($key_arr, $val_arr, $tMessage_sec); ?>
<section class="contact-article page-section" id="contact-sec" style="background-image:url('<?php  echo $tconfig["tsite_upload_apptype_page_images"].$template.'/'.$secure_section['img_'.$vCode]; ?>')">
    <div class="contact-article-inner">
        <div class="contact-information">
        <?php echo $tMessage_sec; ?>    
        </div>
    </div>
</section>
<!-- *************contact section end************* -->
<script>
	var autocomplete_from;
	var autocomplete_to;
	$(function () {
		
		var from = document.getElementById('from');
		autocomplete_from = new google.maps.places.Autocomplete(from);
		google.maps.event.addListener(autocomplete_from, 'place_changed', function() {
			var place = autocomplete_from.getPlace();
			$("#from_lat_long").val(place.geometry.location);
			$("#from_lat").val(place.geometry.location.lat());
			$("#from_long").val(place.geometry.location.lng());
		});
		
		var to = document.getElementById('to');
		autocomplete_to = new google.maps.places.Autocomplete(to);
		google.maps.event.addListener(autocomplete_to, 'place_changed', function() {
			var place = autocomplete_to.getPlace();
			$("#to_lat_long").val(place.geometry.location);
			$("#to_lat").val(place.geometry.location.lat());
			$("#to_long").val(place.geometry.location.lng());
		});
	});
    /* for do not fire enter key to submit the form */
    document.getElementById('from').addEventListener('keypress', function(event) {
        if (event.keyCode == 13) {
            event.preventDefault();
        }
    });
    document.getElementById('to').addEventListener('keypress', function(event) {
        if (event.keyCode == 13) {
            event.preventDefault();
        }
    });
</script>
<? //} ?>