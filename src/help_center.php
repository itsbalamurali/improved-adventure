<?
	include_once("common.php");
	
	$PagesData = $obj->MySQLSelect("SELECT iPageId FROM `pages` WHERE iPageId = 2 AND eStatus = 'Active' ");
	  if(count($PagesData)<=0) {
			header("location: Page-Not-Found");exit;
		}
	$script="Help Center";
	$meta = $STATIC_PAGE_OBJ->FetchStaticPage(2,$_SESSION['sess_lang']);
	 //echo "<pre>";print_r($_);exit;
?>
<!DOCTYPE html>
<html lang="en" dir="<?=(isset($_SESSION['eDirectionCode']) && $_SESSION['eDirectionCode'] != "")?$_SESSION['eDirectionCode']:'ltr';?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?=$meta['meta_title'];?></title>
	<meta name="keywords" value="<?=$meta['meta_keyword'];?>"/>
	<meta name="description" value="<?=$meta['meta_desc'];?>"/>
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
		      <h2 class="header-page trip-detail"><?php } ?><?=$meta['page_title'];?></h2>
		      <!-- trips detail page -->
		      <div class="static-page">
		        <?=$meta['page_desc'];?>
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
