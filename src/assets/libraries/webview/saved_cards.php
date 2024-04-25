<div class="row justify-content-center custom-mt-header saved-cards-list">
    <div class="col-lg-5 col-md-6 col-sm-12 p-0">
        <div class="card">
            <div class="tab-content">
                <div class="tab-pane fade show active">
                    <div class="list-group card-list">
                        <?php foreach ($userPaymentInfoData as $paymentInfoData) { ?>
                        <div class="list-group-item list-group-item-action">
                            <div class="d-flex align-items-center card-text">
                                <?php if($isSelectCard == "Yes") { ?>
                                    <div class="custom-control custom-radio saved-card-checkbox">
                                        <input type="radio" type="radio" name="saved_card" <?= ($paymentInfoData['eDefault'] == 'Yes' && empty($iPaymentInfoId)) ? 'checked' : ($iPaymentInfoId == $paymentInfoData['iPaymentInfoId'] ? 'checked' : '') ?> value="<?= $paymentInfoData['iPaymentInfoId'] ?>" class="custom-control-input" id="saved_card_<?= $paymentInfoData['iPaymentInfoId'] ?>">
                                        <label class="custom-control-label" for="saved_card_<?= $paymentInfoData['iPaymentInfoId'] ?>">
                                            <span class="card-brand">
                                                <img src="<?= $tconfig['tsite_url'].'webimages/icons/DefaultImg/ic_'.$paymentInfoData['vCardBrand'].'_system.svg' ?>" class="mr-2" onerror="this.src='<?= $tconfig["tsite_url"]."webimages/icons/DefaultImg/ic_card_default.svg" ?>'">
                                                <?= str_replace('X', '*', $paymentInfoData['tCardNum']); ?>
                                            </span>
                                        </label>
                                    </div>
                                     
                                    <?php /*<label class="radio-inline mb-0" style="width: calc(100% - 30px)"> 
                                        <input type="radio" name="saved_card" <?= ($paymentInfoData['eDefault'] == 'Yes' && empty($iPaymentInfoId)) ? 'checked' : ($iPaymentInfoId == $paymentInfoData['iPaymentInfoId'] ? 'checked' : '') ?> value="<?= $paymentInfoData['iPaymentInfoId'] ?>">
                                        <span class="card-brand">
                                            <img src="<?= $tconfig['tsite_url'].'webimages/icons/DefaultImg/ic_'.$paymentInfoData['vCardBrand'].'_system.svg' ?>" class="mr-2" onerror="this.src='<?= $tconfig["tsite_url"]."webimages/icons/DefaultImg/ic_card_default.svg" ?>'">
                                            <?= str_replace('X', '*', $paymentInfoData['tCardNum']); ?>
                                        </span>  
                                    </label>*/ ?>
                                <?php } else { ?>
                                    <span class="card-brand">
                                        <img src="<?= $tconfig['tsite_url'].'webimages/icons/DefaultImg/ic_'.$paymentInfoData['vCardBrand'].'_system.svg' ?>" class="mr-3" onerror="this.src='<?= $tconfig["tsite_url"]."webimages/icons/DefaultImg/ic_card_default.svg" ?>'">
                                        <span>
                                        <?= str_replace('X', '*', $paymentInfoData['tCardNum']); ?>
                                        <br>
                                        <?php if($paymentInfoData['eDefault'] != 'Yes') { ?>
                                        <button type="button" class="btn btn-secondary btn-sm set-default-card" data-cardId="<?= $paymentInfoData['iPaymentInfoId'] ?>"><?= $languageLabelsArr['LBL_SET_AS_DEFAULT_TXT'] ?></button>
                                        <?php } else { ?>
                                        <span class="default-text">
                                            <?= $languageLabelsArr['LBL_PRIMARY_TXT'] ?>
                                        </span>
                                        <?php } ?>
                                        </span>
                                    </span>
                                <?php } ?>
                                
                                <span>
                                    <img src="<?= $tconfig['tsite_url'] . 'resizeImg.php?w=28&src=' . $tconfig['tsite_url'] . 'assets/libraries/webview/pg_assets/images/delete.png' ?>" data-toggle="tooltip" title="<?= $languageLabelsArr['LBL_DELETE_CARD_TXT'] ?>" data-cardId="<?= $paymentInfoData['iPaymentInfoId'] ?>" data-cardToken="<?= $paymentInfoData['tCardToken'] ?>" data-cardNo="<?= str_replace('X', '*', $paymentInfoData['tCardNum']); ?>" class="delete-card" />
                                </span>
                            </div>
                        </div>
                        <?php } ?>
                    </div>
                    <div class="card-footer"> 
                        <div class="row">
                            <?php if($isSelectCard == "Yes") { ?>
                            <div class="col-sm-6 pr-2" style="width: 50%;">
                                <a href="javascript:void(0);" class="btn btn-primary btn-block shadow-sm" id="add-card-button"> <?= $languageLabelsArr['LBL_ADD_CARD'] ?> </a>
                            </div>
                            <div class="col-sm-6 pl-2" style="width: 50%">
                                <button type="submit" class="btn btn-primary btn-block shadow-sm confirm-btn" id="confirm_card"> <?= $languageLabelsArr['LBL_CONFIRM_TXT'] ?> </button>
                            </div>
                            <?php } else { ?>
                            <div class="col-sm-12">
                                <a href="javascript:void(0);" class="btn btn-primary btn-block shadow-sm" id="add-card-button"> <?= $languageLabelsArr['LBL_ADD_CARD'] ?> </a>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include $tconfig['tpanel_path'].'assets/libraries/webview/secure_section.php'; ?>