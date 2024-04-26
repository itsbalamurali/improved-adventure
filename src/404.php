<?php

include_once 'common.php';

$order_id = $_REQUEST['orderid'];

// $order_id = base64_encode(base64_encode(trim($_REQUEST['orderid'])));

// invoice_deliverall.php?iOrderId=T1Rreg==

$meta = $STATIC_PAGE_OBJ->FetchStaticPage(22, $_SESSION['sess_lang']);

?>

<!DOCTYPE html>

<html lang="en" dir="<?php echo (isset($_SESSION['eDirectionCode']) && '' !== $_SESSION['eDirectionCode']) ? $_SESSION['eDirectionCode'] : 'ltr'; ?>">

    <head>

        <meta charset="UTF-8">

        <meta name="viewport" content="width=device-width,initial-scale=1">

        <title><?php echo $langage_lbl['LBL_NOT_FOUND']; ?></title>

        <meta name="keywords" value="<?php echo $meta_arr['meta_keyword']; ?>"/>

        <meta name="description" value="<?php echo $meta_arr['meta_desc']; ?>"/>

        <link href="assets/css/custom-order/OverlayScrollbars.css" rel="stylesheet">

        <!-- Default Top Script and css -->

        <?php include_once 'top/top_script.php'; ?>

        <?php include_once 'top/validation.php'; ?>

        <!-- End: Default Top Script and css-->



    </head>

    <body>

        <div id="main-uber-page">

            <!-- Left Menu -->

            <?php include_once 'top/left_menu.php'; ?>

            <!-- End: Left Menu-->

            <!-- home page -->

            <!-- Top Menu -->

            <?php include_once 'top/header_topbar.php'; ?>

            <!-- End: Top Menu-->

            <!-- contact page-->

            <div class="page-contant page-contant-new">

                <div class="page-contant-inner clearfix">



                    <!-- trips detail page -->

                    <div class="page404-holder">

                        <div class="page404-caption">

                            <img src="assets/img/apptype/<?php echo $template.'/'.$meta['vImage1']; ?>" alt="" />

                        </div>

                        <h2><?php echo $meta['page_title']; ?></h2>

                        <!-- trips detail page -->

                        <p><?php echo $meta['page_desc']; ?></p>

                        <!--<h2> 404 <?php echo $langage_lbl['LBL_NOT_FOUND']; ?>!</h2>-->

                        <!--<p><?php // echo $langage_lbl['LBL_DEST_ROUTE_NOT_FOUND'];?></p>-->

                    </div>

                </div>

            </div>

            <!-- home page end-->

            <!-- footer part -->

            <?php include_once 'footer/footer_home.php'; ?>

            <!-- End:contact page-->

            <div style="clear:both;"></div>

        </div>

        <?php include_once 'top/footer_script.php'; ?>

    </body>

</html>

