<?php

include_once("common.php");

$script="Home";

$meta_arr = $STATIC_PAGE_OBJ->FetchSeoSetting(7);

$meta1 = $STATIC_PAGE_OBJ->FetchStaticPage(23,$_SESSION['sess_lang']);

$meta2 = $STATIC_PAGE_OBJ->FetchStaticPage(24,$_SESSION['sess_lang']);

$meta3 = $STATIC_PAGE_OBJ->FetchStaticPage(25,$_SESSION['sess_lang']);

$meta4 = $STATIC_PAGE_OBJ->FetchStaticPage(26,$_SESSION['sess_lang']);

$homepage_banner = $STATIC_PAGE_OBJ->FetchStaticPage(19,$_SESSION['sess_lang']);

$meta5 = $STATIC_PAGE_OBJ->FetchStaticPage(27,$_SESSION['sess_lang']);

$meta6 = $STATIC_PAGE_OBJ->FetchStaticPage(28,$_SESSION['sess_lang']);

$image3 = $STATIC_PAGE_OBJ->FetchStaticPage(29,$_SESSION['sess_lang']);

$image4 = $STATIC_PAGE_OBJ->FetchStaticPage(30,$_SESSION['sess_lang']);

$meta7 = $STATIC_PAGE_OBJ->FetchStaticPage(32,$_SESSION['sess_lang']);



$data = $STATIC_PAGE_OBJ->gethomeDataNew($_SESSION['sess_lang']);

//echo"<pre>";print_r($data);die;



?>

<!DOCTYPE html>

<html lang="en" dir="<?=(isset($_SESSION['eDirectionCode']) && $_SESSION['eDirectionCode'] != "")?$_SESSION['eDirectionCode']:'ltr';?>">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width,initial-scale=1">

    <!--<title><?=$SITE_NAME?></title>-->

	<title><?php echo $meta_arr['meta_title'];?></title>

	<meta name="keywords" value="<?=$meta_arr['meta_keyword'];?>"/>

	<meta name="description" value="<?=$meta_arr['meta_desc'];?>"/>

    <!-- Default Top Script and css -->

    <?php //include_once("top/top_script.php");?>

    <!-- End: Default Top Script and css-->

</head>

<body>

    <!-- home page -->

    <div id="main-uber-page">

        <!-- Left Menu --> 

       <?php //include_once("top/left_menu-new.php");?>

       <!-- End: Left Menu-->

        <!-- Top Menu -->

        <?php //include_once("top/header_topbar-new.php");?>

        <!-- End: Top Menu-->

        <!-- First Section -->

		<?php //include_once("top/header-new.php");?>

        <!-- End: First Section -->

        <?php include_once("top/home-new.php");?>

   

    <!-- home page end-->

    <!-- footer part -->

    <?php //include_once('footer/footer_home.php');?>

    

    <div style="clear:both;"></div>

     </div>

    <!-- footer part end -->

<!-- Footer Script -->

<?php //include_once('top/footer_script.php');?>

<!-- End: Footer Script -->

</body>

</html>

