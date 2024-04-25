<?php
include_once("common.php");

$vCode = $_SESSION['sess_lang'];
$showSignRegisterLinks = 1; 
$db_about = $STATIC_PAGE_OBJ->FetchStaticPage(52,$_SESSION['sess_lang']);

$page_title = $db_about['page_title'];
$pagesubtitle = json_decode($db_about[0]['pageSubtitle'],true);
if(empty($pagesubtitle["pageSubtitle_".$vCode])) {
    $vCode = 'EN';
    $db_about = $STATIC_PAGE_OBJ->FetchStaticPage(52,$vCode);
    $page_title = $db_about['page_title'];
}
$pagesubtitle_lang = $pagesubtitle["pageSubtitle_".$vCode];


?>
<!DOCTYPE html>
<html lang="en" dir="<?=(isset($_SESSION['eDirectionCode']) && $_SESSION['eDirectionCode'] != "")?$_SESSION['eDirectionCode']:'ltr';?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?=$db_about['meta_title'];?></title>
    <meta name="keywords" content="<?=$db_about['meta_keyword'];?>"/>
    <meta name="description" content="<?=$db_about['meta_desc'];?>"/>
    <!-- Default Top Script and css -->
    <?php include_once("top/top_script.php");?>
    <!-- End: Default Top Script and css-->
</head>
<body>
   <!-- home page -->
   <div id="main-uber-page">
    <!-- Left Menu -->
    <?php include_once("top/left_menu.php");?>
    <!-- End: Left Menu-->
    <!-- Top Menu -->
    <?php include_once("top/header_topbar.php");?>
    <!-- End: Top Menu-->
    <!-- contact page-->
    <?php if($THEME_OBJ->isXThemeActive() == 'Yes') { ?>
        <div class="gen-cms-page">
            <div class="gen-cms-page-inner">
                <h2 class="header-page">
    <?php } else { ?>
            <div class="page-contant">
                <div class="page-contant-inner">
                  <h2 class="header-page trip-detail"><?php } ?><?=$page_title;?></h2>
                  <!-- trips detail page -->
                  <div class="static-page">
                    <p><?= $pagesubtitle_lang;?></p>
                </div>
            </div>
        </div>
        <!-- footer part -->
        <?php include_once('footer/footer_home.php');?>
        <!-- footer part end -->
        <!-- End:contact page-->
        <div style="clear:both;"></div>
    </div>
    <!-- home page end-->
    <!-- Footer Script -->
    <?php include_once('top/footer_script.php');?>
    <!-- End: Footer Script -->
</body>
</html>