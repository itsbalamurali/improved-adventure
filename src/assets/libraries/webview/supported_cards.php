<div class="row">
    <div class="col-md-12 d-none-alt px-0" id="accepted-cards" <?= ($has_card == 0) ? 'style="display: block"' : '' ?>>
        <div class="card ">
            <div class="card-header bg-white">
                <h5 class="card-title"><?= ucwords(strtolower($languageLabelsArr['LBL_MANUAL_STORE_CREDIT_CARDS_WE_ACCEPT'])).':'; ?></h5>
                
                <ul class="accepted-cards">
                    <?php foreach ($supported_cards as $supported_card) { ?>
                        <li <?= ($supported_card == "mada") ? 'style="width: 100px"' : "" ?>>
                            <img src="<?= $tconfig['tsite_url'].'/webimages/icons/DefaultImg/ic_'.$supported_card.'_system.svg' ?>">
                        </li>
                    <?php } ?>
                </ul>

                <p class="card-text"><strong><?= $languageLabelsArr['LBL_NOTE'] ?>: </strong><?= $languageLabelsArr['LBL_SUPPORTED_CARDS_NOTE'] ?></p>
            </div>
        </div>
    </div>
</div>