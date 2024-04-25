<form method="POST" id="set-default-card-form" style="display: none;">
    <input type="hidden" name="set_as_default" value="1">
    <input type="hidden" name="default_iPaymentInfoId">
</form>
<form method="POST" id="select-card-form" style="display: none;">
    <input type="hidden" name="select_card" value="1">
    <input type="hidden" name="selected_iPaymentInfoId">
</form>
<div class="modal fade payment-modal" tabindex="-1" role="dialog" id="delete_card_modal" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="" method="post" id="delete-form" class="mb-0">
                <input type="hidden" name="eStatus" value="Delete">
                <input type="hidden" name="iPaymentInfoId" id="iPaymentInfoId" value="">
                <input type="hidden" name="vCardToken" id="vCardToken" value="">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Card</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&#x2715;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p class="text-center"><?= $languageLabelsArr['LBL_DELETE_CONFIRM_MSG'] ?></p>
                    <h5 id="card_no" class="text-center"></h5>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?= $languageLabelsArr['LBL_CANCEL_TXT'] ?></button>
                    <button type="submit" class="btn btn-danger" id="delete_card_btn"><?= $languageLabelsArr['LBL_DELETE'] ?></button>
                </div>
            </form>
        </div>
    </div>
</div>