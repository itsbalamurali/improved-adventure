<?
include_once("common.php");

$script = "Maintanance";
$PagesData = $obj->MySQLSelect("SELECT iPageId FROM `pages` WHERE iPageId = 44 AND eStatus = 'Active' ");
	if(count($PagesData)<=0) {
		  header("location: Page-Not-Found");exit;
	}
$meta = $STATIC_PAGE_OBJ->FetchStaticPage(44, $_SESSION['sess_lang']);
//echo "<pre>";print_r($meta);exit;
?>
<!DOCTYPE html>
<html lang="en" dir="<?= (isset($_SESSION['eDirectionCode']) && $_SESSION['eDirectionCode'] != "") ? $_SESSION['eDirectionCode'] : 'ltr'; ?>">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title>
            <?= $meta['meta_title']; ?>
        </title>
        <meta name="keywords" value="<?= $meta['meta_keyword']; ?>"/>
        <meta name="description" value="<?= $meta['meta_desc']; ?>"/>
        <!-- Default Top Script and css -->
        <?php include_once("top/top_script.php"); ?>
        <!-- End: Default Top Script and css-->
    </head>
    <body>
        <div id="main-uber-page">
            <!-- Left Menu -->
            <?php include_once("top/left_menu.php"); ?>
            <!-- End: Left Menu-->
            <!-- home page -->
            <!-- Top Menu -->
            <?php include_once("top/header_topbar.php"); ?>
            <!-- End: Top Menu-->
            <!-- contact page-->
            <div class="gen-cms-page">
                <div class="gen-cms-page-inner">
                    <h2 class="header-page trip-detail">
                        <?= $meta['page_title']; ?>
                    </h2>
                    <!-- trips detail page -->
                    <div class="static-page">
                        <?= $meta['page_desc']; ?>
                    </div>
                </div>
            </div>
            <!-- home page end-->
            <!-- footer part -->
            <?php include_once('footer/footer_home.php'); ?>
            <!-- End:contact page-->
            <div style="clear:both;"></div>
        </div>
        <!-- footer part end -->
        <!-- Footer Script -->
        <?php include_once('top/footer_script.php'); ?>
        <!-- End: Footer Script -->
        <!-- Powered by cubejekshark.com -->
    </body>
</html>
