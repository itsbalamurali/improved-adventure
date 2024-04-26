<?php
/*include 'common.php';*/

$GeneralMemberId = isset($_REQUEST['GeneralMemberId']) ? $_REQUEST['GeneralMemberId'] : '';
$GeneralUserType = isset($_REQUEST['GeneralUserType']) ? $_REQUEST['GeneralUserType'] : '';
$ReceiverName = isset($_REQUEST['tReceiverName']) ? $_REQUEST['tReceiverName'] : '';
$SenderMsg = isset($_REQUEST['tReceiverMessage']) ? $_REQUEST['tReceiverMessage'] : '';
$Amount = isset($_REQUEST['fAmount']) ? $_REQUEST['fAmount'] : '';
$GiftCardImageId = isset($_REQUEST['iGiftCardImageId']) ? $_REQUEST['iGiftCardImageId'] : '';
$EMAIL_TYPE = isset($_REQUEST['EMAIL_TYPE']) ? $_REQUEST['EMAIL_TYPE'] : '';
$vReceiverPhoneCode = isset($_REQUEST['vReceiverPhoneCode']) ? $_REQUEST['vReceiverPhoneCode'] : '0';
$vReceiverPhone = isset($_REQUEST['vReceiverPhone']) ? $_REQUEST['vReceiverPhone'] : '0';
$tReceiverEmail = isset($_REQUEST['tReceiverEmail']) ? $_REQUEST['tReceiverEmail'] : '0';
$iGiftCardId = isset($_REQUEST['iGiftCardId']) ? $_REQUEST['iGiftCardId'] : '';

$image = $GIFT_CARD_OBJ->getGiftCardImages($GiftCardImageId);
$UserData = $GIFT_CARD_OBJ->getUserData($GeneralMemberId, $GeneralUserType);

$SenderName = $UserData['userName'];
$vLang = $UserData['lang'];
if ($vLang == "" || $vLang == NULL) {
    $vLang = $LANG_OBJ->FetchDefaultLangData("vCode");
}

$Amount = formateNumAsPerCurrency($Amount, $UserData['vCurrency']);
$langage_lbl = $LANG_OBJ->FetchLanguageLabels($vLang, "1", $iServiceId);

if ($EMAIL_TYPE == "GiftCardGenerate") {
    $gift_card_content = str_replace(['#RECEIVER_NAME#', '#AMOUNT#', '#SENDER_NAME#', '#APP_NAME#'], [$ReceiverName, '<span>' . $Amount . '</span>', $SenderName, $SITE_NAME], $langage_lbl['LBL_GIFT_CARD_SENDER_CONTENT_TXT']);
    $search_arr = array('<br>', '<span>');
    $replace_arr = array(
        '<br style="margin-bottom: 10px; display: block; content: \'\';">',
        '<span style="font-weight: bold; color: #ff0000;">'
    );
    $gift_card_content = str_replace($search_arr, $replace_arr, $gift_card_content);
} else if ($EMAIL_TYPE == "GiftCardRedeemMailToSender") {
    $iGiftCardData = $GIFT_CARD_OBJ->getGiftCardById($iGiftCardId);
    $iGiftCardData[0]['eReceiverId'] = 947;

    $UserData = $GIFT_CARD_OBJ->getUserData($iGiftCardData[0]['iCreatedById'], $iGiftCardData[0]['eCreatedBy']);
    $ReceiverData = $GIFT_CARD_OBJ->getUserData($iGiftCardData[0]['eReceiverId'], $iGiftCardData[0]['eReceiverUserType']);

    $Amount = formateNumAsPerCurrency($iGiftCardData[0]['fAmount'], $UserData['vCurrency']);

    $gift_card_content = str_replace(['#NAME#', '#AMOUNT#', '#CODE#'], [$ReceiverData['userName'], '<span>' . $Amount . '</span>', $iGiftCardData[0]['vGiftCardCode']], $langage_lbl['LBL_GIFT_CARD_SENDER_REDEEM_SUCCESSFULLY_CONTENT_TXT']);
    $search_arr = array('<br>', '<span>');
    $replace_arr = array(
        '<br style="margin-bottom: 10px; display: block; content: \'\';">',
        '<span style="font-weight: bold; color: #ff0000;">'
    );
    $gift_card_content = str_replace($search_arr, $replace_arr, $gift_card_content);
} else if ($EMAIL_TYPE == "GiftCardRedeemMailToReceiver") {
    $iGiftCardData = $GIFT_CARD_OBJ->getGiftCardById($iGiftCardId);
    $UserData = $GIFT_CARD_OBJ->getUserData($iGiftCardData[0]['iCreatedById'], $iGiftCardData[0]['eCreatedBy']);
    $ReceiverData = $GIFT_CARD_OBJ->getUserData($GeneralMemberId, $GeneralUserType);

    $Amount = formateNumAsPerCurrency($iGiftCardData[0]['fAmount'], $UserData['vCurrency']);

    $gift_card_content = str_replace(['#SENDER_NAME#', '#AMOUNT#', '#CODE#'], [$UserData['userName'], '<span>' . $Amount . '</span>', $iGiftCardData[0]['vGiftCardCode']], $langage_lbl['LBL_GIFT_CARD_RECEIVER_REDEEM_SUCCESSFULLY_CONTENT_TXT']);
    $search_arr = array('<br>', '<span>');
    $replace_arr = array(
        '<br style="margin-bottom: 10px; display: block; content: \'\';">',
        '<span style="font-weight: bold; color: #ff0000;">'
    );
    $gift_card_content = str_replace($search_arr, $replace_arr, $gift_card_content);

} else {
    $gift_card_content = str_replace(['#RECEIVER_NAME#', '#AMOUNT#', '#SENDER_NAME#', '#APP_NAME#'], [$ReceiverName, '<span>' . $Amount . '</span>', $SenderName, $SITE_NAME], $langage_lbl['LBL_GIFT_CARD_CONTENT_TXT']);

    $search_arr = array('<br>', '<span>');
    $replace_arr = array(
        '<br style="margin-bottom: 10px; display: block; content: \'\';">',
        '<span style="font-weight: bold; color: #ff0000;">'
    );
    $gift_card_content = str_replace($search_arr, $replace_arr, $gift_card_content);
}

$search_arr = array('#APP_NAME#', '<br>');
$replace_arr = array($SITE_NAME, '<br/>');
$gift_card_instructions = str_replace($search_arr, $replace_arr, $langage_lbl['LBL_GIFT_CARD_REDEEM_INSTRUCTIONS_TXT']);

?>
<!DOCTYPE html>
<html>
<body>
<table align="center" width="600px" style="font-family: Arial, Helvetica, sans-serif; font-size: 14px;">
    <tr>
        <td><?= $gift_card_content ?></td>
    </tr>
    <tr>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td style="padding: 10px; border: 1px solid #cccccc;">
            <table>
                <?php if (in_array($EMAIL_TYPE, ["GiftCardGenerate"])) { ?>
                    <tr>
                        <td style="text-align: right;" colspan="2">&nbsp;<?= $langage_lbl['LBL_DATE_TXT'] ?>
                            : <?= date('d-m-Y') ?></td>
                    </tr>
                <?php } ?>
                <tr>
                    <td colspan="2">
                        <img src="<?= $image[0]['vImage'] ?>" alt="Gift Card Image"
                             style="width: 90%; display: block; margin: 0 auto; border-radius: 10px;">
                    </td>
                </tr>
                <tr>
                    <td colspan="2" align="center"
                        style="font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 20px;"
                    ><?= $SenderMsg ?></td>
                </tr>
                <tr>
                    <td colspan="2" align="center"
                        style="font-size: 20px; padding: 6px 0; font-weight: bold;"><?= $langage_lbl['LBL_GIFT_CARD_TXT'] ?></td>
                </tr>
                <tr>
                    <td align="right" style="padding-top: 12px; border-right: 1px solid #cccccc; padding: 0 15px 12px;">
                        <span style="font-size: 20px; font-weight: bold;"><?= $langage_lbl['LBL_GIFT_CARD_CODE_TXT'] ?></span>
                        <br style="margin-bottom: 5px; display: block; content: '';">
                        <span style="font-size: 16px; font-weight: bold;"><?= $vGiftCardCode ?></span>
                    </td>
                    <td align="left" style="padding: 0 15px 12px">
                        <span style="font-size: 28px; font-weight: bold;"><?= $Amount ?></span>
                        <br style="margin-bottom: 5px; display: block; content: '';">
                        <span style="font-size: 12px;"><?= str_replace('#APP_NAME#', $SITE_NAME, $langage_lbl['LBL_GIFT_CARD_USAGE_SUB_TXT']) ?></span>
                        <p style="font-size: 12px; color: #00A9B7; margin: -14px 0 0 0;"><?= $langage_lbl['LBL_GIFT_CARD_CONDITIONS_APPLY_TXT'] ?></span>
                    </td>
                </tr>
                <?php if (!in_array($EMAIL_TYPE, ["GiftCardGenerate", "GiftCardRedeemMailToSender","GiftCardRedeemMailToReceiver"])) { ?>
                    <tr>
                        <td colspan="2"><?= $gift_card_instructions ?></td>
                    </tr>
                <?php } ?>
                <tr>
                    <td colspan="2" style="font-size: 12px;">
                        <a href="<?= $tconfig['tsite_url'] ?>gift-card-terms-condition" target="_blank"
                           style="text-decoration: none; color: #007bff;"><?= $langage_lbl['LBL_GIFT_CARD_TERMS_CONDITIONS_LINK_TXT'] ?></a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td>&nbsp;</td>
    </tr>
    <?php if (in_array($EMAIL_TYPE, ["GiftCardGenerate"])) { ?>
        <tr>
            <td style="padding: 10px; border: 1px solid #cccccc;">
                <table style="font-family: Arial, Helvetica, sans-serif; font-size: 14px;">
                    <tr>
                        <td colspan="2"
                            style="font-size: 16px; padding: 6px 0; font-weight: bold;"><?= $langage_lbl['LBL_GIFT_CARD_RECEIVER_DETAILS']; ?></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <b><?= $langage_lbl['LBL_NAME_TXT']; ?>:</b> <?= $ReceiverName ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <b><?= $langage_lbl['LBL_MOB_NO']; ?>:</b>
                            +<?= $vReceiverPhoneCode ?> <?= $vReceiverPhone ?>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <b><?= $langage_lbl['LBL_EMAIL_TEXT']; ?>:</b> <?= $tReceiverEmail ?>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    <?php } ?>
</table>
</body>
</html>

