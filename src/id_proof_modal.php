<?php 
    if(!isset($languageLabelsArr))
    {
        $languageLabelsArr = $langage_lbl;
    }
?>

<div class="custom-modal-main in custom-common-modal" id="id_proof_modal" style="max-width: 172800px; max-height: 84330px;" aria-hidden="false">
    <div class="custom-modal">
        <div class="model-body">
            <div class="lock-img">
                <img src="<?= $tconfig['tsite_url'].'assets/img/save-money-new.svg' ?>">
            </div>
            <div class="delivery-pref-modal-content">
                <div class="delivery-pref-title"><?= $languageLabelsArr['LBL_IDENTIFICATION']; ?></div>
                <div class="delivery-pref-desc" style="text-align: left !important;">
                    <?= str_replace("\n", "<br>", $tProofNote) ?>
                </div>
                <div class="delivery-pref-button">
                    <button type="button" id="delivery_pref_btn" data-dismiss="modal"><?= $languageLabelsArr['LBL_BTN_OK_TXT'] ?></button>
                </div>
            </div>
        </div>
    </div>
</div>