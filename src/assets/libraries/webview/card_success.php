<?php
include_once("../../../common.php");
// ini_set('display_errors', 1);
//    error_reporting(E_ALL);
$lang_data = $obj->MySQLSelect("SELECT vCode FROM language_master WHERE eDefault = 'Yes'");
$vLang = $lang_data[0]['vCode'];
$languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLang, "1", "");
if (isset($_REQUEST['message']) && $_REQUEST['message'] == "USER_NOT_FOUND") {
    $failure_msg = $langage_lbl['LBL_USER'] . " " . $langage_lbl['LBL_NOT_FOUND'];
}
else {
    $failure_msg = isset($_REQUEST['message']) ? $_REQUEST['message'] : "";
}
$failure_msg = stripslashes($failure_msg);
$SYSTEM_TYPE = isset($_REQUEST['SYSTEM_TYPE']) ? $_REQUEST['SYSTEM_TYPE'] : "WEB";
$giftCode = isset($_REQUEST['giftCode']) ? $_REQUEST['giftCode'] : "";
$failure_url = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$failure_url = str_replace(['&page_action=close', '&page_action=contactus'], "", $failure_url);
$failure_url = preg_replace("/&TIME=\d+/", "", $failure_url) . '&TIME=' . time();
$previous_url = urldecode($_REQUEST['previous_url']);
$previous_url = preg_replace("/&TIME=\d+/", "", $previous_url) . '&TIME=' . time();
?>
<!DOCTYPE html>
<html lang="en" dir="<?= (isset($_SESSION['eDirectionCode']) && $_SESSION['eDirectionCode'] != "") ? $_SESSION['eDirectionCode'] : 'ltr'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?php echo $langage_lbl['LBL_PAYMENT_SUCCESS_TXT']; ?></title>
    <link href="<?= $tconfig['tsite_url'] ?>assets/css/apptype/<?= $template ?>/custom-order/OverlayScrollbars.css" rel="stylesheet">
    <!-- Default Top Script and css -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <?php include_once($tconfig["tpanel_path"] . "top/top_script.php"); ?>
    <!-- End: Default Top Script and css-->
    <style type="text/css">
        html, body{
            height:100%;
            background-color:#ffffff
        }
        .static-page-a{
            margin:0;
            padding:70px 0 0 0;
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
        }
        .page-contant-inner{
            padding-bottom:60px;
            text-align:center;
        }
        .try-again-btn{
            color:#ffffff;
            background-color:#000000;
            font-size:15px;
            font-weight:600;
            padding:10px;
            min-width:150px;
            border:none;
            outline:none;
        }
        .try-again-btn:hover, .try-again-btn:active{
            background-color:#000000
        }
        .try-again{
            text-align:center;
            margin:30px 0 20px;
        }
        .payment-failed-title{
            text-align:center;
            margin-bottom:30px;
            font-size:24px;
            font-weight:600;
        }
        .close-link{
            color:#ffffff;
            text-decoration:none;
            text-align:center;
            font-size:20px;
        }
        .help-block{
            color:#828485;
            margin-top:50px;
            line-height:normal;
        }
        .help-block a{
            color:#0099CC;
            text-decoration:none;
        }
        .overlay{
            left:0;
            top:0;
            width:100%;
            height:100%;
            position:fixed;
            background:rgba(0, 0, 0, 0.9);
            z-index:9999;
            display:none;
        }
        .overlay__inner{
            left:0;
            top:0;
            width:100%;
            height:100%;
            position:absolute;
        }
        .overlay__content{
            left:50%;
            position:absolute;
            top:50%;
            transform:translate(-50%, -50%);
        }
        .spinner{
            width:100px;
            height:100px;
            display:inline-block;
            border-width:5px;
            border-color:rgba(255, 255, 255, 0.1);
            border-top-color:#ffffff;
            animation:spin 1s infinite linear;
            border-radius:100%;
            border-style:solid;
        }
        @keyframes spin{
            100%{
                transform:rotate(360deg);
            }
        }
    </style>
</head>
<body>
<div class="overlay">
    <div class="overlay__inner">
        <div class="overlay__content"><span class="spinner"></span></div>
    </div>
</div>
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
                <div class="payment-failed-title"><?= $languageLabelsArr['LBL_PAYMENT_SUCCESS_TXT'] ?></div>
                <p><?php echo str_replace('#GIFT_CODE#', $giftCode, $languageLabelsArr['LBL_CARD_PAYMENT_SUCCESS_TXT']); ?>  </p>
                <div class="try-again">
                    <a href="<?= $failure_url ?>&gift_action=success" class="try-again-btn close-link">  <?= $languageLabelsArr['LBL_CLOSE_TXT'] ?></a>
                </div>
            </div>
            <!-- home page end-->
        </div>
        <?php if ($SYSTEM_TYPE != "APP") { ?>
            <!-- footer part -->
            <?php include_once($tconfig["tpanel_path"] . 'footer/footer_home.php'); ?>
            <!-- End:contact page-->
        <?php } ?>
        <div style="clear:both;"></div>
        <script type="text/javascript">
            function showLoader() {
                $('.overlay').show();
            }
        </script>
        <?php include_once($tconfig["tpanel_path"] . 'top/footer_script.php'); ?>
</body>
</html>