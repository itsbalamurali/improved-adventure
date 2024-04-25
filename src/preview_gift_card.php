<?php
include_once('common.php');
$GeneralMemberId = isset($_REQUEST['GeneralMemberId']) ? $_REQUEST['GeneralMemberId'] : '';
$GeneralUserType = isset($_REQUEST['GeneralUserType']) ? $_REQUEST['GeneralUserType'] : '';
$ReceiverName = isset($_REQUEST['ReceiverName']) ? $_REQUEST['ReceiverName'] : '';
$SenderMsg = isset($_REQUEST['SenderMsg']) ? $_REQUEST['SenderMsg'] : '';
$Amount = isset($_REQUEST['Amount']) ? $_REQUEST['Amount'] : '';
$GiftCardImageId = isset($_REQUEST['GiftCardImageId']) ? $_REQUEST['GiftCardImageId'] : '';
$adminPreview = isset($_REQUEST['adminPreview']) ? $_REQUEST['adminPreview'] : 0;

$image = $GIFT_CARD_OBJ->getGiftCardImages($GiftCardImageId);
$UserData = $GIFT_CARD_OBJ->getUserData($GeneralMemberId, $GeneralUserType);
$SenderName = $UserData['userName'];
$vLang = $UserData['lang'];
if ($vLang == "" || $vLang == NULL) {
    $vLang = $LANG_OBJ->FetchDefaultLangData("vCode");
}

$lang_dir = $LANG_OBJ->FetchMemberSelectedLanguageDir($vLang);
$html_dir = $lang_dir == "rtl" ? 'dir="rtl"' : '';

$Amount = formateNumAsPerCurrency($Amount, $UserData['vCurrency']);

$langage_lbl = $LANG_OBJ->FetchLanguageLabels($vLang, "1", $iServiceId);

$gift_card_content = str_replace(['#RECEIVER_NAME#', '#AMOUNT#', '#SENDER_NAME#', '#APP_NAME#'], [$ReceiverName, '<span>' . $Amount . '</span>', $SenderName, $SITE_NAME], $langage_lbl['LBL_GIFT_CARD_CONTENT_TXT']);
$width = '';
if ($adminPreview == 1) {
    $width = "width:50%";
}
?>
<!DOCTYPE html>
<html <?= $html_dir ?>>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta http-equiv="expires" content="Sun, 01 Jan 2014 00:00:00 GMT"/>
    <meta http-equiv="pragma" content="no-cache"/>
    <title><?= $langage_lbl['LBL_GIFT_CARDT_PREVIEW_BTN_TXT'] ?></title>
    <link href="https://fonts.googleapis.com/css?family=Poppins:100,400,500,600,700,800,900&display=swap"
          rel="stylesheet"/>
    <link rel="stylesheet"
          href="<?= $tconfig['tsite_url'] ?>assets/libraries/webview/pg_assets/css/bootstrap-4.6.min.css">
    <script type="text/javascript"
            src="<?= $tconfig['tsite_url'] ?>assets/libraries/webview/pg_assets/js/jquery.min.js"></script>
    <script type="text/javascript"
            src="<?= $tconfig['tsite_url'] ?>assets/libraries/webview/pg_assets/js/bootstrap.bundle.min.js"></script>
</head>
<style>
    .gift-card-body {
        border: none;
    }

    .gift-card-content br, .gift-card-instructions br {
        margin-bottom: 10px;
        display: block;
        content: "";
    }

    .gift-card-detail {
        padding: 10px;
        border: 1px solid #cccccc;
    }

    .gift-card-detail .gift-card-img {
        width: 75%;
        display: block;
        margin: 0 auto;
        border-radius: 10px;
    }

    .gift-card-body span {
        font-weight: bold;
        color: #cf0000;
    }

    .gift-code-content {
        border-right: 1px solid #cccccc;
    }

    .gift-code-content .gift-code-title {
        font-size: 24px;
    }

    .gift-price-content .price {
        font-size: 24px;
    }

    .gift-card-msg, .terms-condition-subtext {
        color: #00A9B7;
    }

    .terms-condition-subtext, .terms-condition-link {
        font-size: 12px;
    }

    .download-apps-section p {
        font-weight: 500;
    }

    .download-apps-section a {
        display: inline-block;
        margin: 0 10px 15px 0;
    }

    .download-apps-section a img {
        max-width: 100%;
        image-rendering: -webkit-optimize-contrast;
    }

    [dir="rtl"] .gift-card-content, [dir="rtl"] .gift-card-instructions, [dir="rtl"] .terms-condition-link, [dir="rtl"] .gift-price-content, [dir="rtl"] .download-apps-section {
        text-align: right;
    }

    [dir="rtl"] .gift-code-content {
        border-left: 1px solid #cccccc;
        border-right: none;
        text-align: left !important;
    }

    [dir="rtl"] .download-apps-section a {
        margin: 0 0 15px 10px;
    }

    @media screen and (max-width: 576px) {
        .gift-code-content .gift-code-title {
            font-size: 20px;
        }

        .gift-price-content .price {
            font-size: 24px;
        }
    }

    @media screen and (max-width: 630px) {
        .download-apps-section a {
            width: calc(50% - 20px);
            width: -o-calc(50% - 20px);
            width: -moz-calc(50% - 20px);
            width: -webkit-calc(50% - 20px);
            margin: 0 5px 10px 0;
        }

        [dir="rtl"] .download-apps-section a {
            margin: 0 0 10px 5px;
        }
    }
</style>
<body>
<div style="<?php echo $width; ?>" class="container-fluid px-4 py-5">
    <div class="card gift-card-body">
        <?php if ($adminPreview == 0) { ?>
            <div class="card-body px-0">
                <p class="gift-card-content"><?= $gift_card_content ?></p>
            </div>
        <?php } ?>
        <div class="gift-card-detail">
            <img class="card-img-top gift-card-img" src="<?= $image[0]['vImage'] ?>" alt="Card image">
            <div class="card-body">
                <h5 class="card-title text-center gift-card-msg"><?= $SenderMsg ?></h5>
                <h6 class="card-title text-center"><?= $langage_lbl['LBL_GIFT_CARD_TXT'] ?></h6>
                <div class="row">
                    <div class="col gift-code-content text-right">
                        <p class="mb-0 font-weight-bold gift-code-title"><?= $langage_lbl['LBL_GIFT_CARD_CODE_TXT'] ?></p>
                        <strong>123XYZ4567</strong>
                    </div>
                    <div class="col gift-price-content">
                        <p class="mb-0 font-weight-bold price">
                            <?php $Amount =  "<span style='margin-right: 4px;color: black;'>" .str_replace(' ','</span>' ,$Amount );?>
                            <?= $Amount ?></p>
                        <p><?= str_replace('#APP_NAME#', $SITE_NAME, $langage_lbl['LBL_GIFT_CARD_USAGE_SUB_TXT']) ?></p>
                        <p class="terms-condition-subtext mb-0"><?= $langage_lbl['LBL_GIFT_CARD_CONDITIONS_APPLY_TXT'] ?></p>
                    </div>
                </div>
            </div>
            <?php if ($adminPreview == 0) { ?>
            <div class="gift-card-instructions mt-3">
                <?= str_replace('#APP_NAME#', $SITE_NAME, $langage_lbl['LBL_GIFT_CARD_REDEEM_INSTRUCTIONS_TXT']) ?>
            </div>
            <?php } ?>
            <?php if ($adminPreview == 0) { ?>
                <?php if (!empty($IPHONE_APP_LINK) || !empty($ANDROID_APP_LINK) || !empty($HUAWEI_APP_LINK)) { ?>
                    <div class="download-apps-section mt-4">
                        <p class="mb-2"><?= $langage_lbl['LBL_CLICK_TO_DOWNLOAD_APP'] ?></p>
                        <?php if (!empty($IPHONE_APP_LINK)) { ?>
                            <a href="<?= $IPHONE_APP_LINK ?>" target="_blank">
                                <img alt=""
                                     src="<?= $tconfig['tsite_url'] ?>assets/img/apptype/<?= $template ?>/ios-store.png">
                            </a>
                        <?php } ?>

                        <?php if (!empty($ANDROID_APP_LINK)) { ?>
                            <a href="<?= $ANDROID_APP_LINK ?>" target="_blank">
                                <img alt=""
                                     src="<?= $tconfig['tsite_url'] ?>assets/img/apptype/<?= $template ?>/google-play_.png">
                            </a>
                        <?php } ?>

                        <?php if (!empty($HUAWEI_APP_LINK)) { ?>
                            <a href="<?= $HUAWEI_APP_LINK ?>" target="_blank">
                                <img alt=""
                                     src="<?= $tconfig['tsite_url'] ?>assets/img/apptype/<?= $template ?>/huawe.png">
                            </a>
                        <?php } ?>
                    </div>
                <?php } ?>

            <div class="terms-condition-link mt-5">
                <a href="<?= $tconfig['tsite_url'] ?>terms-condition"
                   target="_blank"><?= $langage_lbl['LBL_GIFT_CARD_TERMS_CONDITIONS_LINK_TXT'] ?></a>
            </div>
            <?php } ?>
        </div>
    </div>
</div>
</body>
</html>