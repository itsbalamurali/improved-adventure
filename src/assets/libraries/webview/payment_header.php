<?php 

if($THEME_OBJ->isProThemeActive() == "Yes") {
    $topBarHeight = isset($_REQUEST['topBarHeight']) ? $_REQUEST['topBarHeight'] : "0";
    $header_padding = "";
    $header_margin = 'style="margin-top: 1rem"';
    if($topBarHeight > 0) {
        $header_padding = 'style="padding-top: calc(' . $topBarHeight . 'px - 46px)"';    
        $header_margin = 'style="margin-top: 6rem"';
    } 
?>
<div class="row payment-header" <?= $header_padding ?>>
    <div class="col-lg-8 py-2 payment-header-content">
        <div class="payment-title">
            <?php 
                $href_return = urldecode($APP_RETURN_URL);
                $onclick_event = "showOverlay()";
                if($page_type == "ADD_CARD") {
                    $payment_head_title = $languageLabelsArr['LBL_ADD_CARD'];
                    if(count($userPaymentInfoData) > 0) {
                        $href_return = "javascript:void(0);";
                        $onclick_event = "backToPaymentList()";    
                    }
                }
                elseif ($page_type == "PAYMENT_LIST") {
                    $payment_head_title = $languageLabelsArr['LBL_MANUAL_STORE_CREDIT_CARDS'];
                }
                else {
                    $payment_head_title = $languageLabelsArr['LBL_PAYMENT'];
                }
            ?>
            <?php $border_margin = ""; if($page_type == "CHARGE_OUTSTANDING_AMT" || !empty($APP_RETURN_URL)) { $border_margin = 'style="margin-left: 40px"'; ?>
                <a class="text-reset float-left" href="<?= $href_return ?>" onclick="<?= $onclick_event ?>" style="width: 40px"><img src="<?= $tconfig['tsite_url'] ?>assets/libraries/webview/pg_assets/images/back.png"></a>
            <?php } else { ?>
                <span class="float-left" style="width: 40px">&nbsp;</span>
            <?php } ?>
            <div class="mx-auto text-center"><?= $payment_head_title ?></div>
            <div id="close-action">
                <?php if($SYSTEM_TYPE == "WEB") { ?>
                <a href="<?= $cancelUrl ?>&status=failure"><?= $languageLabelsArr['LBL_CLOSE_TXT'] ?></a>
                <?php } else { ?>
                <a href="<?= $failure_url ?>?success=0&page_action=close"><?= $languageLabelsArr['LBL_CLOSE_TXT'] ?></a>
                <?php } ?>
            </div>
        </div>
        
        <div class="payment-title-border" <?= $border_margin ?>></div>
    </div>
</div>
<?php } else { ?>
<div class="row payment-header">
    <div class="col-lg-8 mx-auto text-center">
        <h1 class="display-4 payment-title">
            <?php 
                $href_return = urldecode($APP_RETURN_URL);
                $onclick_event = "showOverlay()";
                if($page_type == "ADD_CARD") {
                    $payment_head_title = $languageLabelsArr['LBL_ADD_CARD'];
                    if(count($userPaymentInfoData) > 0) {
                        $href_return = "javascript:void(0);";
                        $onclick_event = "backToPaymentList()";    
                    }
                }
                elseif ($page_type == "PAYMENT_LIST") {
                    $payment_head_title = $languageLabelsArr['LBL_MANUAL_STORE_CREDIT_CARDS'];
                }
                else {
                    $payment_head_title = $languageLabelsArr['LBL_PAYMENT'];
                }
            ?>
        	<?php $border_margin = ""; if($page_type == "CHARGE_OUTSTANDING_AMT" || !empty($APP_RETURN_URL)) { $border_margin = 'style="margin-left: 40px"'; ?>
        		<a class="mr-1 text-reset" href="<?= $href_return ?>" onclick="<?= $onclick_event ?>" style="padding-right: 15px;"><i class="fas fa-chevron-left"></i></a>
        	<?php } ?>
        	<?= $payment_head_title ?>
            <span class="float-right" id="close-action">
                <a href="<?= $failure_url ?>?success=0&page_action=close"><img src="<?= $tconfig['tsite_url'] ?>assets/libraries/webview/pg_assets/images/quit.svg"></a>
            </span>
        </h1>
        <div class="payment-title-border" <?= $border_margin ?>></div>
    </div>
</div>
<?php } ?>