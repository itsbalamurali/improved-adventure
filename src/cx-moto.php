<?php
include_once("common.php");

$showSignRegisterLinks = 1;
//$table_name = 'content_cubex_details';
$table_name = getContentCMSHomeTable();
$vCode = $_SESSION['sess_lang'];
$eFor = 'Moto';
$_SESSION["navigatedPage"] = $eFor;

$ride_data = array();
if(ENABLE_DYNAMIC_CREATE_PAGE=="Yes") {
    $sql_ufx_dynamic = " AND iVehicleCategoryId = ".$_REQUEST['iVehicleCategoryId'];
    $ride_data_query = "SELECT * FROM ".$table_name." WHERE eFor = '" . $eFor . "'".$sql_ufx_dynamic;
    $ride_data = $obj->MySQLSelect($ride_data_query);
}
if(empty($ride_data) || empty($ride_data[0]['lBannerSection'])) {
    $sql_ufx_dynamic = " AND iVehicleCategoryId = 0";
    $ride_data_query = "SELECT * FROM ".$table_name." WHERE eFor = '" . $eFor . "'".$sql_ufx_dynamic;
    $ride_data = $obj->MySQLSelect($ride_data_query);
}

$banner_section = json_decode($ride_data[0]['lBannerSection'],true);
if(empty($banner_section['title_'.$vCode])) {
    $vCode = 'EN';
}

$inner_key = array('menu_title_','title_','sub_title_','desc_','img_','title_first_','desc_first_','img_first_','title_sec_','desc_sec_','img_sec_','title_third_','desc_third_','img_third_','title_fourth_','desc_fourth_','img_fourth_','title_fifth_','desc_fifth_','img_fifth_','title_six_','desc_six_','img_six_','main_title_','main_desc_','img2_');

$banner_section = $LANG_OBJ->checkOtherLangDataExist(json_decode($ride_data[0]['lBannerSection'],true),$vCode,$inner_key);
$how_it_work_section = $LANG_OBJ->checkOtherLangDataExist(json_decode($ride_data[0]['lHowitworkSection'],true),$vCode,$inner_key);
$secure_section = $LANG_OBJ->checkOtherLangDataExist(json_decode($ride_data[0]['lSecuresafeSection'],true),$vCode,$inner_key);
$download_section = $LANG_OBJ->checkOtherLangDataExist(json_decode($ride_data[0]['lDownloadappSection'],true),$vCode,$inner_key);
$call_section = $LANG_OBJ->checkOtherLangDataExist(json_decode($ride_data[0]['lCalltobookSection'],true),$vCode,$inner_key);
$earn_section = $LANG_OBJ->checkOtherLangDataExist(json_decode($ride_data[0]['lEarnSection'],true),$vCode,$inner_key);
$calculate_section = $LANG_OBJ->checkOtherLangDataExist(json_decode($ride_data[0]['lCalculateSection'],true),$vCode,$inner_key);
$cartype_section = $LANG_OBJ->checkOtherLangDataExist(json_decode($ride_data[0]['lCartypeSection'],true),$vCode,$inner_key);
$service_section = $LANG_OBJ->checkOtherLangDataExist(json_decode($ride_data[0]['lServiceSection'],true),$vCode,$inner_key);
$benefit_section = $LANG_OBJ->checkOtherLangDataExist(json_decode($ride_data[0]['lBenefitSection'],true),$vCode,$inner_key);

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
if(ENABLE_DYNAMIC_CREATE_PAGE!="Yes") {
?>
<!DOCTYPE html>
<html lang="en" dir="<?=(isset($_SESSION['eDirectionCode']) && $_SESSION['eDirectionCode'] != "")?$_SESSION['eDirectionCode']:'ltr';?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no, target-densityDpi=device-dpi">
    <title><?=$SITE_NAME?> | <?= $langage_lbl['LBL_FOOTER_LINK_MOTO'] ?></title>
	<!--<title><?php echo $meta_arr['meta_title'];?></title>
	<meta name="keywords" value="<?=$meta_arr['meta_keyword'];?>"/>
	<meta name="description" value="<?=$meta_arr['meta_desc'];?>"/>-->
    <!-- Default Top Script and css -->
    <?php include_once("top/top_script.php");?>
    <!-- End: Default Top Script and css-->
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places&language=en&key=<?=$GOOGLE_SEVER_API_KEY_WEB?>"></script>
</head>
<body id="wrapper">
    <!-- home page -->
    <!-- home page -->
    <?php if($template!='taxishark'){?>
    <div id="main-uber-page">
    <?php } ?>
        <!-- Left Menu -->
    <?php include_once("top/left_menu.php");?>
    <!-- End: Left Menu-->
        <!-- Top Menu -->
        <?php include_once("top/header_topbar.php");?>
        <!-- End: Top Menu-->
        <!-- First Section -->
		<?php include_once("top/header.php");?>
        <!-- End: First Section -->
        <? } ?>
<!-- *************banner section start************* -->
<section class="banner-section taxi-app bannermenu">
    <div class="tab-row-holding">
        <ul class="tab-row">
            <li data-src="1" class="tab active">
                <a href="#how-it-works"><?= $menutitleHow ?></a>
            </li>
            <li data-src="2" class="tab">
                <a href="#fare-estimate"><?= $menutitleCalc ?></a>
            </li>
            <li data-src="3" class="tab">
                <a href="#our-services"><?= $menutitleService; ?></a>
            </li>
            <li data-src="6" class="tab">
                <a href="#download-apps"><?= $menutitleDown ?></a>
            </li>
            <li data-src="7" class="tab">
                <a href="#earn"><?= $menutitleEarn ?></a>
            </li>
            <li data-src="7" class="tab">
                <a href="#security"><?= $menutitleSecure ?></a>
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

<!-- *************hot it works section start************* -->
<section class="how-it-works-section taxi-variant page-section" id="how-it-works">
    <div class="how-it-works-section-inner">
        <div class="how-it-works-left">
            <h3><?php echo $how_it_work_section['title_'.$vCode];?></h3>
            <?php echo $how_it_work_section['desc_'.$vCode];?>

            <!-- How it Works sub Topics -->
            <ul>
            <?php for ($i=1; $i <= 6; $i++) { ?>
                <?php if (!empty($how_it_work_section['hiw_title'.$i.'_'.$vCode]) && !empty($how_it_work_section['hiw_desc'.$i.'_'.$vCode])) { ?>
                    <li data-number="<?php echo $i; ?>">
                        <img alt="" class="proc_ico" src="<?php echo $tconfig["tsite_upload_apptype_page_images"].$template.'/'.$how_it_work_section['hiw_img'.$i.'_'.$vCode];?>" xss="removed"> 
                        <strong><?php echo $how_it_work_section['hiw_title'.$i.'_'.$vCode];?></strong> 
                        <span><?php echo $how_it_work_section['hiw_desc'.$i.'_'.$vCode];?></span>
                    </li>
                <?php } ?>
            <?php } ?>
            </ul>
            <!-- How it Works sub Topics End -->

        </div>
        <div class="how-it-works-right">
            <img src="<?php echo $tconfig["tsite_upload_apptype_page_images"].$template.'/'.$how_it_work_section['img_'.$vCode]; ?>" alt="">
        </div>
    </div>
   
</section>
<!-- *************hot it works section end************* -->

<!-- *************safty-section section start************* -->
<section class="safety-section taxi-variant page-section" id="fare-estimate">
    <div class="safety-section-inner">
        <div class="safety-section-left">
            <div class="safty-image-hold" style="background-image:url(<?php  echo $tconfig["tsite_upload_apptype_page_images"].$template.'/'.$calculate_section['img_'.$vCode]; ?>)"></div>
        </div>
        <div class="safety-section-right">
            <h3><?php echo $calculate_section['title_'.$vCode];?></h3>
            <?php echo $calculate_section['desc_'.$vCode];?>
            <form name="_fare_estimate_form" id="_fare_estimate_form" method="post" action="cx-fareestimate.php" class="gen-from">
				<input type="hidden" name="distance" id="distance" value="">
				<input type="hidden" name="duration" id="duration" value="">
				<input type="hidden" name="from_lat_long" id="from_lat_long" value="" >
				<input type="hidden" name="from_lat" id="from_lat" value="" >
				<input type="hidden" name="from_long" id="from_long" value="" >
				<input type="hidden" name="to_lat_long" id="to_lat_long" value="" >
				<input type="hidden" name="to_lat" id="to_lat" value="" >
				<input type="hidden" name="to_long" id="to_long" value="" >
				<input type="hidden" name="location_found" id="location_found" value="" >
				<input type="hidden" name="etype" id="etype" value="Moto" >
                
                <div class="form-group pickup-location">
                    <label><?=$langage_lbl['LBL_HOME_ADD_PICKUP_LOC']; ?></label>
                    <input name="vPickup" type="text" id="from" placeholder="" />
                </div>
                <div class="form-group drop-location">
                    <label><?=$langage_lbl['LBL_ADD_DESTINATION_LOCATION_TXT']; ?></label>
                    <input name="vDest" type="text" id="to" placeholder="" />
                </div>
                <div class="button-block">
                    <div class="btn-hold">
                        <input type="submit" name="btn_submit" value="Submit">
                        <img src="assets/img/apptype/<?php echo $template;?>/arrow.svg" alt="">
                    </div>
                </div>    
			</form>
        </div>
    </div>
</section>
<!-- *************safty-section section end************* -->

<!-- ************* services section section start ************* -->
<section class="services page-section" id="our-services">
    <div class="services-inner">
        <h3><?= $service_section['main_title_'.$vCode]; ?></h3>
        <strong><?= $service_section['main_desc_'.$vCode]; ?></strong>
        <ul>
            <?php if(!empty($service_section['title_first_'.$vCode])) { ?>
            <li>
                <div class="service-block">
                    <i><img src="<?php  echo $tconfig["tsite_upload_apptype_page_images"].$template.'/'.$service_section['img_first_'.$vCode]; ?>" alt=""></i>
                    <strong><?= $service_section['title_first_'.$vCode]; ?></strong>
                    <p><?= $service_section['desc_first_'.$vCode]; ?></p>
                    <!--<a href="#"><img src="assets/img/apptype/<?php echo $template;?>/right-arrow_.svg" alt=""></a>-->
                </div>
            </li>
            <?php } if(!empty($service_section['title_sec_'.$vCode])) { ?>
            <li>
                <div class="service-block">
                    <i><img src="<?php  echo $tconfig["tsite_upload_apptype_page_images"].$template.'/'.$service_section['img_sec_'.$vCode]; ?>" alt=""></i>
                    <strong><?= $service_section['title_sec_'.$vCode]; ?></strong>
                    <p><?= $service_section['desc_sec_'.$vCode]; ?></p>
                    <!--<a href="#"><img src="assets/img/apptype/<?php echo $template;?>/right-arrow_.svg" alt=""></a>-->
                </div>
            </li>
            <?php } if(!empty($service_section['title_third_'.$vCode])) { ?>
            <li>
                <div class="service-block">
                    <i><img src="<?php  echo $tconfig["tsite_upload_apptype_page_images"].$template.'/'.$service_section['img_third_'.$vCode]; ?>" alt=""></i>
                    <strong><?= $service_section['title_third_'.$vCode]; ?></strong>
                    <p><?= $service_section['desc_third_'.$vCode]; ?></p>
                    <!--<a href="#"><img src="assets/img/apptype/<?php echo $template;?>/right-arrow_.svg" alt=""></a>-->
                </div>
            </li>
            <?php } if(!empty($service_section['title_fourth_'.$vCode])) { ?>
            <li>
                <div class="service-block">
                    <i><img src="<?php  echo $tconfig["tsite_upload_apptype_page_images"].$template.'/'.$service_section['img_fourth_'.$vCode]; ?>" alt=""></i>
                    <strong><?= $service_section['title_fourth_'.$vCode]; ?></strong>
                    <p><?= $service_section['desc_fourth_'.$vCode]; ?></p>
                    <!--<a href="#"><img src="assets/img/apptype/<?php echo $template;?>/right-arrow_.svg" alt=""></a>-->
                </div>
            </li>
            <?php } if(!empty($service_section['title_fifth_'.$vCode])) { ?>
            <li>
                <div class="service-block">
                    <i><img src="<?php  echo $tconfig["tsite_upload_apptype_page_images"].$template.'/'.$service_section['img_fifth_'.$vCode]; ?>" alt=""></i>
                    <strong><?= $service_section['title_fifth_'.$vCode]; ?></strong>
                    <p><?= $service_section['desc_fifth_'.$vCode]; ?></p>
                    <!--<a href="#"><img src="assets/img/apptype/<?php echo $template;?>/right-arrow_.svg" alt=""></a>-->
                </div>
            </li>
            <?php } if(!empty($service_section['title_six_'.$vCode])) { ?>
            <li>
                <div class="service-block">
                    <i><img src="<?php  echo $tconfig["tsite_upload_apptype_page_images"].$template.'/'.$service_section['img_six_'.$vCode]; ?>" alt=""></i>
                    <strong><?= $service_section['title_six_'.$vCode]; ?></strong>
                    <p><?= $service_section['desc_six_'.$vCode]; ?></p>
                    <!--<a href="#"><img src="assets/img/apptype/<?php echo $template;?>/right-arrow_.svg" alt=""></a>-->
                </div>
            </li>  
        <?php } ?>    
        </ul>
    </div>
</section>
<!-- ************* services section section end ************* -->

<!-- ************* benefits section section start ************* -->

<!-- ************* benefits section section end ************* -->

<!-- ************* cartype section section start ************* -->

<!-- ************* cartype section section end ************* -->

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
                <a href="<?= $tMessage_link1; ?>" class="app_btn slider_btn"><img src="assets/img/apptype/<?php echo $template;?>/play-store.png" alt=""><?=$langage_lbl['LBL_GOOGLE_PLAY']; ?></a>
                <a href="<?= $tMessage_link2; ?>" class="app_btn_two slider_btn"><img src="assets/img/apptype/<?php echo $template;?>/apple-store.png" alt=""><?=$langage_lbl['LBL_APP_STORE']; ?></a>
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

<!-- *************earn section section start************* -->
<article class="blog-article">
    <div class="article-inner">
        <div class="article-row page-section" id="earn">
            <div class="article-left">
                <div class="article-mage" style="background-image:url(<?php  echo $tconfig["tsite_upload_apptype_page_images"].$template.'/'.$earn_section['img_'.$vCode]; ?>)"></div>
            </div>
            <div class="article-right">
                <h4><?php echo $earn_section['title_'.$vCode];?></h4>
                <?php echo $earn_section['desc_'.$vCode];?>
            </div>
        </div>
        <div class="article-row invert page-section" id="security">
            <div class="article-left">
                <div class="article-mage" style="background-image:url(<?php  echo $tconfig["tsite_upload_apptype_page_images"].$template.'/'.$secure_section['img_'.$vCode]; ?>)"></div>
            </div>
            <div class="article-right">
                <h4><?php echo $secure_section['title_'.$vCode];?></h4>
                <?php echo $secure_section['desc_'.$vCode];?>
            </div>
        </div>
    </div>
</article>
<!-- *************earn section section end************* -->

<!-- *************call to section end************* -->
<?php
/*$tMessage_call = $call_section['desc_'.$vCode];
$tMessage_call = str_replace($key_arr, $val_arr, $tMessage_call); */?>
<!--<section class="call-section page-section taxi-variant" id="booktaxi">  
    <div class="call-section-inner">
        <div class="call-section-right">
            <div class="call-section-image" style="background-image:url(<?php  echo $tconfig["tsite_upload_apptype_page_images"].$template.'/'.$call_section['img_'.$vCode]; ?>)"></div>
        </div>
        <div class="call-section-left">
            <h3><?php echo $call_section['title_'.$vCode];?></h3>
            <?php echo $tMessage_call;?>
        </div>
    </div>
</section>-->
<!-- *************call to section end************* -->
<!-- *************call to section start************* -->
<?php
$tMessage_call = $call_section['desc_'.$vCode];
$tMessage_call = str_replace($key_arr, $val_arr, $tMessage_call); ?>
<? if(ENABLE_DYNAMIC_CREATE_PAGE=="Yes") { ?>

<!-- *************call to section end************* -->

<? } if(ENABLE_DYNAMIC_CREATE_PAGE!="Yes") { ?>
<section class="call-section page-section taxi-variant" id="booktaxi">  
    <div class="call-section-inner">
        <div class="call-section-right">
            <div class="call-section-image" style="background-image:url(<?php  echo $tconfig["tsite_upload_apptype_page_images"].$template.'/'.$call_section['img_'.$vCode]; ?>)"></div>
        </div>
        <div class="call-section-left">
            <h3><?php echo $call_section['title_'.$vCode];?></h3>
            <?php echo $tMessage_call;?>
        </div>
    </div>
</section>
 <!-- home page end-->
    <!-- footer part -->
    <?php include_once('footer/footer_home.php');?>

    <div style="clear:both;"></div>
     <?php if($template!='taxishark'){?>
     </div>
     <?php } ?>
    <!-- footer part end -->
<!-- Footer Script -->
<?php include_once('top/footer_script.php');?>
<!-- End: Footer Script -->
</body>
</html>
<? } ?>
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