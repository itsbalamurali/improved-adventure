<div class="row">
    <div class="col-md-12 d-none-alt px-0" id="demo-cards" <?= ($has_card == 0) ? 'style="display: block"' : '' ?>>
        <div class="card ">
            <div class="card-header">
                <h5 class="card-title"><?= ucwords(strtolower($languageLabelsArr['LBL_DUMMY_CREDIT_CARD_TXT'])); ?></h5>
                
                <ul class="list-group demo-cards">
                    <li class="list-group-item">
                        <span class="demo-cards-list-title"><?= $languageLabelsArr['LBL_CARD_NUMBER_TXT'] ?></span>
                        <span class="demo-cards-list-desc">4111 1111 1111 1111<br>4242 4242 4242 4242<br>5555 5555 5555 4444</span>
                    </li>
                    <li class="list-group-item">
                        <span class="demo-cards-list-title"><?= $languageLabelsArr['LBL_EXPIRY'] ?></span>
                        <span class="demo-cards-list-desc"><?= date('m/Y', strtotime("+ 3years")) ?></span>
                    </li>
                    <li class="list-group-item">
                        <span class="demo-cards-list-title"><?= $languageLabelsArr['LBL_CVV'] ?></span>
                        <span class="demo-cards-list-desc">123</span>
                    </li>
                </ul>
            </div>
        </div>


        <p class="mt-3 mb-0"><strong><?= $languageLabelsArr['LBL_NOTE'] ?>: </strong></p>
        <div class="card mt-2">
            <ul class="list-group demo-cards">
                <li class="list-group-item" style="background-color: #F6F6F6"><?= $languageLabelsArr['LBL_DEMO_CARD_DESC'] ?></li>
            </ul>
        </div>
    </div>
</div>