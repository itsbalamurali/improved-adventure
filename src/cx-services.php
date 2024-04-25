<?php
include_once("common.php");
$showSignRegisterLinks = 1;
$innerPage = "Yes";
if (ENABLE_DYNAMIC_CREATE_PAGE == "Yes") {
    if ($THEME_OBJ->isMedicalServicev2ThemeActive() == "No") {
        $cPage = 1;
    }

    if( isset($_REQUEST['service-bid']))
    {
        $serviceBidPage = 1;
    }
    $getCategoryData = getSeviceCategoryDataForHomepage($_REQUEST['iVehicleCategoryId'], 0, 1);

    $catname = $getCategoryData[0]['vCatName'];
    if(empty($getCategoryData)){
        header('Location:' . $tconfig['tsite_url'] . 'Page-Not-Found');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en"
      dir="<?= (isset($_SESSION['eDirectionCode']) && $_SESSION['eDirectionCode'] != "") ? $_SESSION['eDirectionCode'] : 'ltr'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no, target-densityDpi=device-dpi">
    <title><?= $SITE_NAME ?> | <?= $catname ?></title>
    <!-- Default Top Script and css -->
    <?php include_once("top/top_script.php"); ?>
    <!-- End: Default Top Script and css-->
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places&language=en&key=<?= $GOOGLE_SEVER_API_KEY_WEB ?>"></script>
</head>
<body id="wrapper">
<!-- home page -->
<!-- home page -->
<?php if ($template != 'taxishark'){ ?>
<div id="main-uber-page">
    <?php } ?>
    <!-- Left Menu -->
    <?php include_once("top/left_menu.php"); ?>
    <!-- End: Left Menu-->
    <!-- Top Menu -->
    <?php include_once("top/header_topbar.php"); ?>
    <!-- End: Top Menu-->
    <!-- First Section -->
    <?php include_once("top/header.php");

    // echo "<pre>";print_r($getCategoryData[0]['includeurl']);die;
    include_once($getCategoryData[0]['includeurl']);
    if ($THEME_OBJ->isCubeJekXv3ProThemeActive() == 'Yes' || $THEME_OBJ->isPXCProThemeActive() == 'Yes'  || $THEME_OBJ->isProSPThemeActive() == 'Yes') {
        include_once('include_download_section.php');
    }
    ?>
    <!-- home page end-->
    <!-- footer part -->
    <?php include_once('footer/footer_home.php'); ?>
    <div style="clear:both;"></div>
    <?php if ($template != 'taxishark'){ ?>
</div>
<?php } ?>
<!-- footer part end -->
<!-- Footer Script -->
<?php include_once('top/footer_script.php'); ?>
<!-- End: Footer Script -->
</body>
</html>