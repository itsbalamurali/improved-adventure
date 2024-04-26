<?php
include_once("common.php");
$showSignRegisterLinks = 1;

$table_name = getContentCMSHomeTable();
$vCode = $_SESSION['sess_lang'];
$eFor = 'Delivery';
$_SESSION["navigatedPage"] = $eFor;

$ride_data_query = "SELECT * FROM ".$table_name." WHERE eFor = '" . $eFor . "'";
$ride_data = $obj->MySQLSelect($ride_data_query);

$banner_section = json_decode($ride_data[0]['lBannerSection'],true);
if(empty($banner_section['title_'.$vCode])) {
    $vCode = 'EN';
}

$inner_key = array('menu_title_','title_','sub_title_','desc_','img_','title_first_','desc_first_','img_first_','title_sec_','desc_sec_','img_sec_','title_third_','desc_third_','img_third_','title_fourth_','desc_fourth_','img_fourth_','title_fifth_','desc_fifth_','img_fifth_','title_six_','desc_six_','img_six_','main_title_','main_desc_','img2_');

$banner_section = $LANG_OBJ->checkOtherLangDataExist(json_decode($ride_data[0]['lBannerSection'],true),$vCode,$inner_key);
$calculate_section = $LANG_OBJ->checkOtherLangDataExist(json_decode($ride_data[0]['lCalculateSection'],true),$vCode,$inner_key);

$key_arr = array("#SUPPORT_PHONE#","#SUPPORT_ADDRESS#","#SUPPORT_EMAIL#","#ANDROID_APP_LINK#","#IPHONE_APP_LINK#");
$val_arr = array($SUPPORT_PHONE,$COMPANY_ADDRESS,$SUPPORT_MAIL,$ANDROID_APP_LINK,$IPHONE_APP_LINK);
?>
<div class="common-inner-heading-section">
    <div class="common-inner-heading-section-inner">
        <h2 class="common-inner-heading"><?= $catname ?></h2>
    </div>
</div>
<!-- *************banner section start************* -->
<section class="banner-section taxi-app bannermenu">
    <div class="banner-section-inner">
        <div class="banner-back">
            <div class="banner-image" id="1">
                <img src="<?php  echo $tconfig["tsite_upload_apptype_page_images"].$template.'/'.$banner_section['img_'.$vCode]; ?>" alt="">
            </div>
        </div>
        <div class="categories-block">
            <div class="categories-caption active">
                <h2><?php echo $banner_section['title_'.$vCode];?></h2>
                <?php echo $banner_section['desc_'.$vCode];?>
            </div>
        </div>
    </div>
</section>

<section class="safety-section taxi-variant page-section" id="fare-estimate">
    <div class="safety-section-inner">
        <div class="safety-section-right">
            <h3><?php echo $calculate_section['title_'.$vCode];?></h3>
            <?php echo $calculate_section['desc_'.$vCode];?>
            <?php if(strtoupper(ENABLE_MANUAL_BOOKING_DELIVERY) == "YES") { ?>
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
                <input type="hidden" name="etype" id="etype" value="Deliver" >

                <div class="form-group pickup-location">
                    <label><?=$langage_lbl['LBL_HOME_ADD_PICKUP_LOC']; ?></label>
                    <input name="vPickup" type="text" id="from" placeholder="" required=""  autocorrect="off" spellcheck="false" autocomplete="off"/>
                    <img src="<?= $siteUrl ?>assets/img/detect_loc.svg" class="detect-loc" onclick="fetchLocation()" title="<?= $langage_lbl['LBL_FETCH_LOCATION_HINT'] ?>" style="width: 36px; right: 16px; top: 6px">
                </div>
                <div class="form-group drop-location">
                    <label><?=$langage_lbl['LBL_ADD_DESTINATION_LOCATION_TXT']; ?></label>
                    <input name="vDest" type="text" id="to"  placeholder="" required="" />
                </div>
                <div class="button-block">
                    <div class="btn-hold">
                        <input type="submit" name="btn_submit" value="Submit">
                        <img src="assets/img/apptype/<?php echo $template;?>/arrow.svg" alt="">
                    </div>
                </div>
			</form>
            <?php } ?>
        </div>
        <div class="safety-section-left">
            <div class="safty-image-hold">
                <img src="<?= $tconfig["tsite_upload_apptype_page_images"].$template.'/'.$calculate_section['img_'.$vCode]; ?>" alt="">
            </div>
        </div>

    </div>
</section>
<!-- ************* benefits section section start ************* -->

<script>
	var autocomplete_from;
	var autocomplete_to;
	$(function () {

		$('#from').keyup(function (e) {
            buildAutoComplete("from",e, "<?=$MIN_CHAR_REQ_GOOGLE_AUTO_COMPLETE;?>","<?=$_SESSION['sess_lang'];?>");
        });

        $('#to').keyup(function (e) {
            buildAutoCompleteLat("to",e, "<?=$MIN_CHAR_REQ_GOOGLE_AUTO_COMPLETE;?>","<?=$_SESSION['sess_lang'];?>", $('#from_lat').val(), $('#from_long').val());
        });

        if(typeof $.fn.tooltip.noConflict === "function") {
            var bootstrapTooltip = $.fn.tooltip.noConflict();
            $.fn.bootstrapTooltip = bootstrapTooltip;
        }
        $(document).tooltip({
            position: {
                my: "center bottom-20",
                at: "center top",
                using: function(position, feedback) {
                  $(this).css( position );
                  $("<div>")
                    .addClass("arrow")
                    .addClass(feedback.vertical)
                    .addClass(feedback.horizontal)
                    .appendTo(this);
                }
            }
        });

        checkNavigatorPermissionStatus();

        setInterval(function() {
            checkNavigatorPermissionStatus();
        }, 1000);

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

    function checkNavigatorPermissionStatus() {
        navigator.permissions && navigator.permissions.query({name: 'geolocation'}).then(function(PermissionStatus) {
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
            navigator.geolocation.getCurrentPosition(showPosition, showError, {maximumAge: 0, timeout:1000, enableHighAccuracy: true});
        }
    }

    function showPosition(position) {
        var geo_latitude = position.coords.latitude;
        var geo_longitude = position.coords.longitude;
        var geo_lat_lng = "("+geo_latitude+","+geo_longitude+")";

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

        getReverseGeoCode('from', 'from_lat_long',"<?=$_SESSION['sess_lang'];?>", geo_latitude, geo_longitude, oldlat, oldlong, oldlatlong, oldAddress, function(latitude, longitude, address){
            $('.pickup-location').addClass('floating');
            $('#from').trigger('blur');
        });
    }

    function showError(error) {
        switch(error.code) {
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
    
</script>