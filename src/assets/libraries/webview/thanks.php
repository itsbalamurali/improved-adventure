<?php
    include_once("../../../common.php");

$order_id = isset($_REQUEST['orderid']) ? $_REQUEST['orderid'] : "";

if($order_id != "")
{
    $order_id = base64_encode(base64_encode(trim($_REQUEST['orderid'])));

    $fromOrder = "guest";
    if (isset($_REQUEST['order']) && $_REQUEST['order'] != "") {
        $fromOrder = $_REQUEST['order'];
    }

    $userSession = "MANUAL_ORDER_" . strtoupper($fromOrder);
    $orderDetailsSession = "ORDER_DETAILS_" . strtoupper($fromOrder);
    $orderUserSession = "MANUAL_ORDER_USER_" . strtoupper($fromOrder);
    $orderServiceSession = "MAUAL_ORDER_SERVICE_" . strtoupper($fromOrder);
    $orderUserIdSession = "MANUAL_ORDER_USERID_" . strtoupper($fromOrder);
    $orderAddressIdSession = "MANUAL_ORDER_ADDRESSID_" . strtoupper($fromOrder);
    $orderCouponSession = "MANUAL_ORDER_PROMOCODE_" . strtoupper($fromOrder);
    $orderCouponNameSession = "MANUAL_ORDER_PROMOCODE_NAME_" . strtoupper($fromOrder);
    $orderCurrencyNameSession = "MANUAL_ORDER_CURRENCY_NAME_" . strtoupper($fromOrder);
    $orderLatitudeSession = "MANUAL_ORDER_LATITUDE_" . strtoupper($fromOrder);
    $orderLongitudeSession = "MANUAL_ORDER_LONGITUDE_" . strtoupper($fromOrder);
    $orderServiceNameSession = "MANUAL_ORDER_SERVICE_NAME_" . strtoupper($fromOrder);
    $orderAddressSession = "MANUAL_ORDER_ADDRESS_" . strtoupper($fromOrder);
    $orderDataSession = "MANUAL_ORDER_DATA_" . strtoupper($fromOrder);
    $orderUserNameSession = "MANUAL_ORDER_USER_NAME_" . strtoupper($fromOrder);
    $orderCompanyNameSession = "MANUAL_ORDER_COMPANY_NAME_" . strtoupper($fromOrder);
    $orderUserEmailSession = "MANUAL_ORDER_USER_EMAIL_" . strtoupper($fromOrder);
    $orderStoreIdSession = "MANUAL_ORDER_STORE_ID_".strtoupper($fromOrder);
    unset($_SESSION[$orderDetailsSession]);
    unset($_SESSION[$userSession]);
    unset($_SESSION[$orderUserSession]);
    unset($_SESSION[$orderServiceSession]);
    unset($_SESSION[$orderUserIdSession]);
    unset($_SESSION[$orderAddressIdSession]);
    unset($_SESSION[$orderCouponSession]);
    unset($_SESSION[$orderCouponNameSession]);

    unset($_SESSION[$orderCurrencyNameSession]);

    unset($_SESSION[$orderLatitudeSession]);
    unset($_SESSION[$orderLongitudeSession]);
    unset($_SESSION[$orderAddressSession]);
    unset($_SESSION[$orderDataSession]);

    unset($_SESSION[$orderUserNameSession]);
    unset($_SESSION[$orderCompanyNameSession]);
    unset($_SESSION[$orderUserEmailSession]);
    unset($_SESSION[$orderStoreIdSession]);
    unset($_SESSION[$orderServiceNameSession]);


    $lang_data = $obj->MySQLSelect("SELECT vCode FROM language_master WHERE eDefault = 'Yes'");
    $vLang = $lang_data[0]['vCode'];

    $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLang, "1", "");

    $success_msg = isset($_REQUEST['message']) ? $langage_lbl[$_REQUEST['message']] : "";
    $SYSTEM_TYPE = isset($_REQUEST['SYSTEM_TYPE']) ? $_REQUEST['SYSTEM_TYPE'] : "WEB";
    $success = isset($_REQUEST['success']) ? $_REQUEST['success'] : "";

    $current_url = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $current_url = preg_replace("/&TIME=\d+/", "", $current_url) . '&TIME='.time() . '&success=1';

    $transaction_id = $_REQUEST['iTransactionId'];
    $redirect_counter = 10;
}

?>
<!DOCTYPE html>
<html lang="en" dir="<?= (isset($_SESSION['eDirectionCode']) && $_SESSION['eDirectionCode'] != "") ? $_SESSION['eDirectionCode'] : 'ltr'; ?>">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <title><?php echo $langage_lbl['LBL_MANUAL_STORE_THANK_YOU']; ?></title>

        <link href="<?= $tconfig['tsite_url'] ?>assets/css/apptype/<?= $template ?>/custom-order/OverlayScrollbars.css" rel="stylesheet">
        <!-- Default Top Script and css -->
        <?php include_once($tconfig["tpanel_path"]."top/top_script.php"); ?>
        <!-- End: Default Top Script and css-->
        <style type="text/css">
            html, body {
                height: 100%;
                background-color: #ffffff
            }
            #main-uber-page {
                display: flex;
            }
            .static-page-a {
                margin: 0;
                padding: 0;
                width: 100%;
            }
            .static-page-a, .static-page-aa, .static-page-a h2, .static-page-a p {
                float: none; 
            }
            .static-page-a p {
                font-size: 16px;
                line-height: normal;
            }
            .static-page-aa img {
                width: 50px;
            }

            .page-contant-new {
                padding: 0 30px 0;
                margin: auto;
            }
            <?php if($SYSTEM_TYPE != "APP") { ?>
                .page-contant-new {
                    padding: 0;
                    margin: auto;
                    display: flex;
                    align-items: center;
                    height: calc(100vh - 444px);
                }
            <?php } ?>

            .page-contant-inner {
                padding-bottom: 60px;
                text-align: center;
            }

            .transaction-id-title {
                margin-top: 50px !important;
                font-weight: 600;
                color: #0D2366;
            }

            .transaction-id {
                font-weight: 600;
                color: #32BEA6;
                word-break: break-all;
            }

            .mix-content {
                width: 100%;
            }
        </style>
    </head>
    <body>
        <div id="main-uber-page">
            <?php if($SYSTEM_TYPE != "APP") { ?>
            <!-- Left Menu -->
            <?php include_once($tconfig["tpanel_path"]."top/left_menu.php"); ?>
            <!-- End: Left Menu-->
            <!-- home page -->
            <!-- Top Menu -->
            <?php include_once($tconfig["tpanel_path"]."top/header_topbar.php"); ?>
            <!-- End: Top Menu-->
            <?php } ?>
            <!-- contact page-->
            <div class="page-contant page-contant-new">
                <div class="page-contant-inner clearfix">
                    <!-- trips detail page -->
                    <div class="static-page-a">
                        <div class="static-page-aa">

                            <img src="<?= $tconfig['tsite_url'] ?>assets/img/checked.svg" alt="" />
                        </div>
                        <?php /*<h2> <?php echo $langage_lbl['LBL_MANUAL_STORE_THANK_YOU']; ?>!</h2>*/ ?>
                        <p><?php echo $success_msg; ?></p>
                        <p class="transaction-id-title">Transaction ID:</p>
                        <p class="transaction-id"><?= $transaction_id ?></p>
                        <?php if($success != 1) { ?>
                        <p style="margin-top: 50px"><?= $languageLabelsArr['LBL_REDIRECTING_TXT'] ?> <span id="redirecting"><?= $redirect_counter ?></span></p>
                        <?php } ?>
                    </div>
                    <!-- home page end-->
                </div>
                <?php if($SYSTEM_TYPE != "APP") { ?>
                <!-- footer part -->
                <?php include_once($tconfig["tpanel_path"].'footer/footer_home.php'); ?>
                <!-- End:contact page-->
                <?php } ?>
                <div style="clear:both;"></div>
            </div>

            <?php include_once($tconfig["tpanel_path"].'top/footer_script.php'); ?>
            <?php if($success != 1) { ?>
            <script type="text/javascript">
                var counter = parseFloat("<?= $redirect_counter ?>");
                var interval = setInterval(function() {
                    counter--;
                    // Display 'counter' wherever you want to display it.
                    $('#redirecting').text(counter);
                    if (counter == 0) {
                        // Display a login box
                        clearInterval(interval);
                        <?php if($SYSTEM_TYPE == "APP") { ?>
                            window.location.href = "<?= $current_url ?>";    
                        <?php } else { ?>
                            window.location.href = "<?= $tconfig["tsite_url"]; ?>invoice_deliverall.php?iOrderId=<?= $order_id; ?>";
                        <?php } ?>
                    }
                }, 1000);
            </script>
            <?php } ?>
        </div>
    </body>
</html>