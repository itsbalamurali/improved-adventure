<div class="modal custom-select-modal select-month" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false" id="select_month">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= $languageLabelsArr['LBL_SELECT_TXT'] . ' '. $languageLabelsArr['LBL_MONTH_SIGNUP'] ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&#x2715;</span>
                </button>
            </div>
            <div class="modal-body">
                <ul class="list-group">
                    <?php for($m = 1; $m <= 12; $m++) { ?>
                    <li class="list-group-item" data-val="<?= $m ?>" onclick="selectMonth(this)">
                        <span><?= $m ?></span>
                    </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<div class="modal custom-select-modal select-year" tabindex="-1" role="dialog" data-backdrop="static" data-keyboard="false" id="select_year">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?= $languageLabelsArr['LBL_SELECT_YEAR'] ?></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&#x2715;</span>
                </button>
            </div>
            <div class="modal-body">
                <ul class="list-group">
                    <?php for($y = date('Y'); $y <= date('Y', strtotime("+20 years")); $y++) { ?>
                    <li class="list-group-item" data-val="<?= $y ?>" onclick="selectYear(this)">
                        <span><?= $y ?></span>
                    </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </div>
</div>