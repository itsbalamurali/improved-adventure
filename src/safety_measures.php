<?php
include_once 'common.php';

$PagesData = $obj->MySQLSelect("SELECT iPageId FROM `pages` WHERE iPageId = 54 AND eStatus = 'Active' ");
if (count($PagesData) <= 0) {
    header('location: Page-Not-Found');

    exit;
}

$fromapp = !empty($_REQUEST['fromapp']) ? $_REQUEST['fromapp'] : 'No';
$fromweb = !empty($_REQUEST['fromweb']) ? $_REQUEST['fromweb'] : 'No';
$iServiceIdNew = !empty($_REQUEST['iServiceId']) ? $_REQUEST['iServiceId'] : '1';
$iServiceIdNew = base64_decode($iServiceIdNew, true);

if ('Yes' === $fromweb) {
    $lang = !empty($_SESSION['sess_lang']) ? $_SESSION['sess_lang'] : 'EN';
} else {
    $lang = !empty($_REQUEST['fromlang']) ? $_REQUEST['fromlang'] : 'EN';
}
if (empty($lang)) {
    $lang = 'EN';
}

if ('Yes' === $THEME_OBJ->isCubeJekXv3ThemeActive()) {
    $safetyimg = '/webimages/icons/DefaultImg/ic_safety.png';
} else {
    $safetyimg = '/webimages/icons/DefaultImg/ic_store_safety.png';
}

$safetyimgUrl = (file_exists($tconfig['tpanel_path'].$safetyimg)) ? $tconfig['tsite_url'].'resizeImg.php?w=140&src='.$tconfig['tsite_url'].$safetyimg : '';

$meta = $STATIC_PAGE_OBJ->FetchStaticPage(54, $lang);

$iCompanyId = $_REQUEST['id'] ?? '';
$iCompanyId = base64_decode($iCompanyId, true);

$banner_images = 0;
if (!empty($iCompanyId)) {
    $company_data = $obj->MySQLSelect("SELECT eSafetyPractices FROM company WHERE iCompanyId = {$iCompanyId}");
    $eSafetyPractices = $company_data[0]['eSafetyPractices'];

    if ($MODULES_OBJ->isEnableStorePhotoUploadFacility()) {
        $banner_data = $obj->MySQLSelect('SELECT * FROM store_wise_banners WHERE iCompanyId = '.$iCompanyId." AND eStatus = 'Active' GROUP BY iUniqueId ORDER BY iUniqueId DESC");
        if (count($banner_data) > 0) {
            $banner_images = 1;
        }
    }
}

$languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($lang, '1', $iServiceIdNew);

$show_images = 0;
// $eSafetyPractices = 'No';
if (('Yes' === $eSafetyPractices && $MODULES_OBJ->isEnableStoreSafetyProcedure()) || ($MODULES_OBJ->isEnableStorePhotoUploadFacility() && 1 === $banner_images)) {
    $show_images = 1;
}

if (0 === $show_images) {
    header('Location:profile');

    exit;
}
?>
<!DOCTYPE html>
<html lang="en" dir="<?php echo (isset($_SESSION['eDirectionCode']) && '' !== $_SESSION['eDirectionCode']) ? $_SESSION['eDirectionCode'] : 'ltr'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?php echo $meta['meta_title']; ?></title>
    <meta name="keywords" value="<?php echo $meta['meta_keyword']; ?>"/>
    <meta name="description" value="<?php echo $meta['meta_desc']; ?>"/>
    <!-- Default Top Script and css -->
    <?php include_once 'top/top_script.php'; ?>
    <link rel="stylesheet" href="<?php echo $tconfig['tsite_url_main_admin']; ?>css/fancybox.css" />
    <!-- End: Default Top Script and css-->
    <style type="text/css">
        html, body {
            background-color: #ffffff;
        }

        .banner-img-section {
            margin-bottom: 30px;
            display: flex;
            overflow-y: auto;
            padding: 30px 10px 10px;
        }

        .banner-img {
            width: 200px;
            height: auto;
            padding: 0;
            margin-right: 30px;
            flex: 1 0 auto;
            display: flex;
            max-width: 200px;
            max-height: 200px;
            -webkit-box-shadow: 0px 0px 5px 5px rgba(0,0,0,0.1);
            -moz-box-shadow: 0px 0px 5px 5px rgba(0,0,0,0.1);
            box-shadow: 0px 0px 5px 5px rgba(0,0,0,0.1);
            border-radius: 10px;
            transition: 0.3s;
        }

        .banner-img img {
            width: 100%;
            height: 100%;
            border-radius: 10px;
            border: 1px solid rgba(0,0,0,0.1);
        }

        .banner-img:hover {
            cursor: pointer;
            -webkit-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            -moz-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
        }

        .banner-images-header-web {
            padding: 0 10px 20px 10px;
        }
        .banner-images-header-web .banner-images-title-border {
            border-bottom: 5px solid #007BFF;
            width: 30px;
            margin: 10px 0;
        }

        .banner-images-header-web .banner-images-title {
            color: #0D2366;
            font-weight: 600;
            font-size: 1.5rem;
        }

        <?php if (!($MODULES_OBJ->isEnableStoreSafetyProcedure() && 'Yes' === $eSafetyPractices)) { ?>
            .gen-cms-page h2.header-page {
                border: none;
            }

            .banner-img-section {
                display: block;
                text-align: center;
                margin-top: 5rem;
                padding-top: 0;
            }

            .banner-img {
                width: 30%;
                max-width: 30%;
                height: auto;
                max-height: fit-content;
                margin-bottom: 30px;
                display: inline-block;
                box-shadow: none;
                height: 300px;
                max-height: 300px;
            }

            .banner-img:nth-child(3n+3) {
                margin-right: 0
            }

            .banner-img img {
                -webkit-box-shadow: 0px 0px 5px 5px rgba(0,0,0,0.1);
                -moz-box-shadow: 0px 0px 5px 5px rgba(0,0,0,0.1);
                box-shadow: 0px 0px 5px 5px rgba(0,0,0,0.1);
                object-fit: cover;
            }

            .banner-images-header {
                position: fixed;
                top: 0;
                right: 0;
                left: 0;
                padding: 30px 15px 10px;
                z-index: 1030;
                background-color: #f5f5f5;
                box-shadow: 0px -5px 15px -10px rgb(0 0 0 / 75%);
                -webkit-box-shadow: 0px 5px 15px -10px rgb(0 0 0 / 75%);
                -moz-box-shadow: 0px -5px 15px -10px rgba(0,0,0,0.75);
            }

            .banner-images-title-border {
                border-bottom: 5px solid #007BFF;
                width: 30px;
                margin: 10px 0;
            }

            .banner-images-title {
                color: #0D2366;
                font-weight: 600;
                font-size: 1.5rem;
            }

            #main-uber-page {
                height: 100vh;
                overflow-y: auto;
            }

            @media screen and (max-device-width: 767px) {
                .banner-img {
                    width: 45%;
                    max-width: 45%;
                    height: 250px;
                    max-height: 250px;
                    margin-bottom: 30px;
                }

                .banner-img:nth-child(3n+3) {
                    margin-right: 30px;
                }

                .banner-img:nth-child(2n+2) {
                    margin-right: 0
                }
            }

            @media screen and (max-device-width: 480px) {

                .banner-img-section {
                    display: block;
                    margin-bottom: 0
                }

                .banner-img {
                    width: 100%;
                    max-width: 100%;
                    height: auto;
                    max-height: fit-content;
                    margin-bottom: 30px;
                    margin-right: 0 !important;
                }

                .banner-img:last-child {
                    margin-bottom: 0
                }
            }
        <?php } ?>
        <?php if ('No' === $THEME_OBJ->isCubeJekXv3ThemeActive()) { ?>
            .header-page {
                font-size: 20px !important;
                font-weight: bold !important;
                line-height: 22px;
                color: #000000 !important;
                padding-bottom: 0 !important;
            }

            .header-page:after, .gen-cms-page ul li:before {
                display: none;
            }

            .gen-cms-page p {
                line-height: 22px;
            }

            .gen-cms-page ul {
                list-style-type: disc;
                padding: 30px 30px 15px;
                border: 1px solid #E6E6E6;
                background-color: #F6F6F6;
                border-radius: 10px;
            }

            .gen-cms-page ul li {
                padding: 0;
                font-size: 15px;
                margin-bottom: 15px;
            }

            .gen-cms-page ul li strong {
                font-weight: bold;
                font-size: 18px;
            }

        <?php } ?>
    </style>
</head>
<body>
    <!-- home page -->
    <div id="main-uber-page">
    <!-- Left Menu -->
    <?php include_once 'top/left_menu.php'; ?>
    <!-- End: Left Menu-->
    <!-- Top Menu -->
    <?php if ('Yes' === $fromweb) {
        include_once 'top/header_topbar.php';
    } ?>
    <!-- End: Top Menu-->
    <!-- contact page-->
    <?php if ('Yes' === $THEME_OBJ->isXThemeActive()) { ?>
    <div class="gen-cms-page">
    <div class="gen-cms-page-inner">
        <?php if ($MODULES_OBJ->isEnableStoreSafetyProcedure() && 'Yes' === $eSafetyPractices) { ?>
        <div style="text-align: center; margin-bottom: 30px"><img src="<?php echo $safetyimgUrl; ?>"></div>
        <h2 class="header-page"><?php echo $meta['page_title']; ?></h2>
        <?php } ?>

        <?php } else { ?>
        <div class="page-contant">
            <div class="page-contant-inner">
                <?php if ($MODULES_OBJ->isEnableStoreSafetyProcedure() && 'Yes' === $eSafetyPractices) { ?>
                <h2 class="header-page trip-detail"><?php echo $meta['page_title']; ?></h2>
                <?php } ?>

                <?php } ?>

                <?php if (!($MODULES_OBJ->isEnableStoreSafetyProcedure() && 'Yes' === $eSafetyPractices) && 'Yes' !== $fromweb) { ?>
                <div class="banner-images-header">
                    <div class="banner-images-title"><?php echo $languageLabelsArr['LBL_RESTAURANT_TXT_ADMIN']; ?> Images</div>
                    <div class="banner-images-title-border"></div>
                </div>
                <?php } ?>

                <?php if ('Yes' === $fromweb && !($MODULES_OBJ->isEnableStoreSafetyProcedure() && 'Yes' === $eSafetyPractices)) { ?>
                    <div class="banner-images-header-web">
                        <div class="banner-images-title"><?php echo $languageLabelsArr['LBL_RESTAURANT_TXT_ADMIN']; ?> Images</div>
                        <div class="banner-images-title-border"></div>
                    </div>
                <?php } ?>

                <?php if (1 === $banner_images) {
                    $img_count = $img_count1 = 1; ?>
                <div class="banner-img-section">
                    <?php foreach ($banner_data as $banner) { ?>
                        <?php if (!empty($banner['vImage'])) { ?>
                            <div class="banner-img">
                                <img src="<?php echo $tconfig['tsite_url'].'resizeImg.php?w=400&h=400&src='.$tconfig['tsite_upload_images'].$banner['vImage']; ?>" data-fancybox="gallery" data-src="<?php echo $tconfig['tsite_upload_images'].$banner['vImage']; ?>">
                            </div>
                        <?php ++$img_count;
                        } ?>
                    <?php } ?>
                </div>
                <?php } ?>

                <?php if ($MODULES_OBJ->isEnableStoreSafetyProcedure() && 'Yes' === $eSafetyPractices) { ?>
                <div class="static-page">
                    <?php echo $meta['page_desc']; ?>
                </div>
                <?php } ?>
            </div>
        </div>
        <!-- footer part -->
        <?php if ('Yes' === $fromweb) {
            include_once 'footer/footer_home.php';
        } ?>
        <!-- footer part end -->
        <!-- End:contact page-->
        <div style="clear:both;"></div>
    </div>
    <!-- home page end-->
    <!-- Footer Script -->
    <?php include_once 'top/footer_script.php'; ?>
    <!-- End: Footer Script -->
    <script type="text/javascript" src="<?php echo $tconfig['tsite_url_main_admin']; ?>js/fancybox.umd.js"></script>
    <script>
        /*function openImagePreview() {
            $('#gallery_modal').show();
            $('body').css('overflow', 'hidden');
        }

        function closeImagePreview() {
            $('#gallery_modal').hide();
            $('body').css('overflow', 'auto');
        }

        var slideIndex = 1;
        showSlides(slideIndex);

        function plusSlides(n) {
            showSlides(slideIndex += n);
        }

        function currentSlide(n) {
            showSlides(slideIndex = n);
        }

        function showSlides(n) {
            var i;
            var slides = document.getElementsByClassName("mySlides");
            var dots = document.getElementsByClassName("demo");
            var captionText = document.getElementById("caption");

            if (n > slides.length) {
                slideIndex = 1
            }
            if (n < 1) {
                slideIndex = slides.length
            }
            for (i = 0; i < slides.length; i++) {
                slides[i].style.display = "none";
            }
            for (i = 0; i < dots.length; i++) {
                dots[i].className = dots[i].className.replace(" active", "");
            }
            slides[slideIndex-1].style.display = "block";
            dots[slideIndex-1].className += " active";
            captionText.innerHTML = dots[slideIndex-1].alt;
        }*/
    </script>
</body>
</html>