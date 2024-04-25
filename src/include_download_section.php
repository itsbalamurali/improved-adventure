<?php
include_once("common.php");
?>
<!-- *************download section section start************* -->
<?php


if (!empty($IPHONE_APP_LINK) || !empty($ANDROID_APP_LINK)) { ?>
<section class="get_app_area sec_pad page-section" id="download-apps">
    <div class="get_app_area-inner">
        <div class="get_app_area-left">
            <div class="get_app_content">
                <div class="section_title">
                    <h2><?= $langage_lbl['LBL_DOWNLOAD_ANDROID_IOS_APPS_TXT']; ?></h2>
                </div>
            </div>
        </div>
        <div class="get_app_area-right app_image">
            <?php if (!empty($IPHONE_APP_LINK)) { ?>
            <div class="image_first">
                <a href="<?= $IPHONE_APP_LINK ?>" target="_blank"><img src="assets/img/footer-ios-store.svg" alt="" ></a>
            </div>
            <?php } ?>
            <?php if (!empty($ANDROID_APP_LINK)) { ?>
            <div class="image_two">
                <a href="<?= $ANDROID_APP_LINK ?>" target="_blank"><img src="assets/img/footer-google-play.svg" alt=""></a>
            </div>
            <?php } ?>
        </div>
    </div>
</section>
<?php } ?>
<!-- *************download section section end************* -->
