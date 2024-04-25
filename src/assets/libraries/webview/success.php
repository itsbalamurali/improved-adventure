<?php
include_once("../../../common.php");
// ini_set('display_errors', 1);
//    error_reporting(E_ALL);
    $vLang = isset($_REQUEST['vLang']) ? $_REQUEST['vLang'] : "";
	if(empty($vLang)){
$lang_data = $obj->MySQLSelect("SELECT vCode FROM language_master WHERE eDefault = 'Yes'");
$vLang = $lang_data[0]['vCode'];
	}
$languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLang, "1", "");
    $success_msg = isset($_REQUEST['message']) ? $languageLabelsArr[$_REQUEST['message']] : "";
$SYSTEM_TYPE = isset($_REQUEST['SYSTEM_TYPE']) ? $_REQUEST['SYSTEM_TYPE'] : "WEB";
$PAGE_TYPE = isset($_REQUEST['PAGE_TYPE']) ? $_REQUEST['PAGE_TYPE'] : "";
$success = isset($_REQUEST['success']) ? $_REQUEST['success'] : "";
$transaction_id = $_REQUEST['iTransactionId'];
if ($PAGE_TYPE == "GIFT_CARD_PAYMENT") {
    $giftCode = isset($_REQUEST['giftCode']) ? $_REQUEST['giftCode'] : "";
    $returnUrl = $tconfig['tsite_url'] . "assets/libraries/webview/card_success.php";
    header('Location:' . $returnUrl . "?message=LBL_PAYMENT_SUCCESS_TXT&SYSTEM_TYPE=" . $SYSTEM_TYPE . "&TIME=" . time() . "&PAGE_TYPE=GIFT_CARD_PAYMENT&iTransactionId=" . $transaction_id . "&giftCode=" . $giftCode);
    exit;
}
if ($PAGE_TYPE == "AUTHORIZE_TRIP_AMOUNT") {
    exit;
}
$current_url = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$current_url = preg_replace("/&TIME=\d+/", "", $current_url) . '&TIME=' . time() . '&success=1';
$redirect_counter = 10;
if (isset($_REQUEST['iOrderId']) && !empty($_REQUEST['iOrderId'])) {
    $order_id = base64_encode(base64_encode($_REQUEST['iOrderId']));
}
?>
<!DOCTYPE html>
<html lang="en" dir="<?= (isset($_SESSION['eDirectionCode']) && $_SESSION['eDirectionCode'] != "") ? $_SESSION['eDirectionCode'] : 'ltr'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
        <title><?php echo $languageLabelsArr['LBL_PAYMENT_SUCCESS_TXT']; ?></title>
    <link href="<?= $tconfig['tsite_url'] ?>assets/css/apptype/<?= $template ?>/custom-order/OverlayScrollbars.css" rel="stylesheet">
    <!-- Default Top Script and css -->
    <?php include_once($tconfig["tpanel_path"] . "top/top_script.php"); ?>
    <!-- End: Default Top Script and css-->
    <style type="text/css">
        html, body{
            height:100%;
            background-color:#ffffff
        }
        #main-uber-page{
            display:flex;
        }
        .static-page-a{
            margin:0;
            padding:0;
            width:100%;
        }
        .static-page-a, .static-page-aa, .static-page-a h2, .static-page-a p{
            float:none;
        }
        .static-page-a p{
            font-size:16px;
            line-height:normal;
        }
        .static-page-aa img{
            width:50px;
        }
        .page-contant-new{
            padding:0 30px 0;
            margin:auto;
        }
        <?php if($SYSTEM_TYPE != "APP") { ?>
        .page-contant-new{
            padding:0;
            margin:auto;
            display:flex;
            align-items:center;
            height:calc(100vh - 360px);
        }
        <?php } ?>
        .page-contant-inner{
            padding:50px 0;
            text-align:center;
        }
        .transaction-id-title{
            margin-top:50px !important;
            font-weight:600;
            color:#0D2366;
        }
        .transaction-id{
            font-weight:600;
            color:#32BEA6;
            word-break:break-all;
        }
        .mix-content{
            width:100%;
        }
    </style>
</head>
<body>
<div id="main-uber-page">
    <?php if ($SYSTEM_TYPE != "APP") { ?>
        <!-- Left Menu -->
        <?php include_once($tconfig["tpanel_path"] . "top/left_menu.php"); ?>
        <!-- End: Left Menu--><!-- home page --><!-- Top Menu -->
        <?php include_once($tconfig["tpanel_path"] . "top/header_topbar.php"); ?>
        <!-- End: Top Menu-->
    <?php } ?>
    <!-- contact page-->
    <div class="page-contant page-contant-new">
        <div class="page-contant-inner clearfix">
            <!-- trips detail page -->
            <div class="static-page-a">
                <div class="static-page-aa">
                    <img src="<?= $tconfig['tsite_url'] ?>assets/img/checked.svg" alt=""/>
                </div>
                <?php /*<h2> <?php echo $langage_lbl['LBL_MANUAL_STORE_THANK_YOU']; ?>!</h2>*/ ?>
                <p><?php echo $success_msg; ?></p>
                <p class="transaction-id-title">Transaction ID:</p>
                <p class="transaction-id"><?= $transaction_id ?></p>
                <?php if ($success != 1) { ?>
                    <p style="margin-top: 50px"><?= $languageLabelsArr['LBL_REDIRECTING_TXT'] ?>
                        <span id="redirecting"><?= $redirect_counter ?></span></p>
                    <p style="margin-top: 50px"><?= $languageLabelsArr['LBL_DO_NOT_CLOSE_APP_MSG'] ?></span></p>
                <?php } ?>
            </div>
            <!-- home page end-->
        </div>
        <?php if ($SYSTEM_TYPE != "APP") { ?>
            <!-- footer part -->
            <?php include_once($tconfig["tpanel_path"] . 'footer/footer_home.php'); ?>
            <!-- End:contact page-->
        <?php } ?>
        <div style="clear:both;"></div>
    </div>
    <?php include_once($tconfig["tpanel_path"] . 'top/footer_script.php'); ?>
    <?php if ($success != 1) { ?>
        <script type="text/javascript">
            var counter = parseFloat("<?= $redirect_counter ?>");
            var interval = setInterval(function () {
                counter--;
                // Display 'counter' wherever you want to display it.
                $('#redirecting').text(counter);
                if (counter == 0) {
                    // Display a login box
                    clearInterval(interval);
                    <?php if($SYSTEM_TYPE == "APP") { ?>
                    window.location.href = "<?= $current_url ?>";
                    <?php } else {
                    if($PAGE_TYPE != "WALLET_MONEY_ADD") {
                    ?>
                    window.location.href = "<?= $tconfig["tsite_url"]; ?>invoice_deliverall.php?iOrderId=<?= $order_id; ?>&PAGE_TYPE=ORDER_TIP_COLLECT&success=1";
                    <?php } else { ?>
                    window.location.href = "<?= $current_url ?>";
                    <?php } ?>
                    <?php } ?>
                }
            }, 1000);
        </script>
    <?php } ?>
</div>
</body>
</html>