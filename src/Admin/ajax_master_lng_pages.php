<?php
include_once '../common.php';

if (isset($_POST['id']) && '' !== $_POST['id']) {
    $id = $_POST['id'];
    $sql = 'SELECT * FROM `language_master` ORDER BY `iDispOrder`';
    $db_master = $obj->MySQLSelect($sql);
    $count_all = count($db_master);

    if ($count_all > 0) {
        for ($i = 0; $i < $count_all; ++$i) {
            $vValue = 'vValue_'.$db_master[$i]['vCode'];
            ${$vValue} = $_POST[$vValue] ?? '';
        }
    }

    $sql = "SELECT vLabel FROM app_screen_language_label WHERE LanguageLabelId = '".$id."'";
    $db_data = $obj->MySQLSelect($sql);

    $sql = "SELECT * FROM app_screen_language_label WHERE vLabel = '".$db_data[0]['vLabel']."'";
    $db_data = $obj->MySQLSelect($sql);

    $vLabel = $db_data[0]['vLabel'];
    if (count($db_data) > 0) {
        foreach ($db_data as $key => $value) {
            $vValue = 'vValue_'.$value['vCode'];
            ${$vValue} = $value['vValue'];
        }
    }

    $EN_available = $LANG_OBJ->checkLanguageExist();
    foreach ($db_master as $dkey => $dval) {
        if ($EN_available) {
            if ('EN' === $dval['vCode']) {
                unset($db_master[$dkey]);
                array_unshift($db_master, $dval);
            }
        } else {
            if ($dval['vCode'] === $default_lang) {
                unset($db_master[$dkey]);
                array_unshift($db_master, $dval);
            }
        }
    }
    ?>
<div class="modal-body-form">
    <form method="post" name="_languages_form" id="_languages_form" action="">
        <input type="hidden" name="id" value="<?php echo $id; ?>"/>
        <div class="row" id="errorMessageRow" style="display: none;">
            <div class="col-lg-12" id="errorMessage">
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <label>Language Label<?php echo ('' !== $id) ? '' : '<span class="red"> *</span>'; ?></label>
            </div>
            <div class="col-lg-6">
                <input type="text" class="form-control" name="vLabel"  id="vLabel" value="<?php echo $vLabel; ?>" placeholder="Language Label" disabled>
            </div>
        </div>

        <?php
            if ($count_all > 0) {
                for ($i = 0; $i < $count_all; ++$i) {
                    $vCode = $db_master[$i]['vCode'];
                    $vTitle = $db_master[$i]['vTitle'];
                    $eDefault = $db_master[$i]['eDefault'];

                    $vValue = 'vValue_'.$vCode;

                    $required = ('Yes' === $eDefault) ? 'required' : '';
                    $required_msg = ('Yes' === $eDefault) ? '<span class="red"> *</span>' : '';
                    ?>
                <div class="row">
                    <div class="col-lg-12">
                        <label><?php echo $vTitle; ?> Value <?php echo $required_msg; ?></label>
                    </div>
                    <div class="col-lg-12">
                        <input type="text" class="form-control" name="<?php echo $vValue; ?>" id="<?php echo $vValue; ?>" value="<?php echo htmlentities(${$vValue}); ?>" placeholder="<?php echo $vTitle; ?> Value" <?php echo $required; ?>>
                        <div class="text-danger" id="<?php echo $vValue.'_error'; ?>" style="display: none;"><?php echo $langage_lbl_admin['LBL_REQUIRED']; ?></div>
                    </div>
                    <?php
                        if (count($db_master) > 1) {
                            if ($EN_available) {
                                if ('EN' === $vCode) { ?>
                            <div class="col-lg-12" style="margin-top: 10px">
                                <button type ="button" name="allLanguage" id="allLanguage" class="btn btn-primary" onClick="getAllLanguageCode('vValue_', 'EN');">Convert To All Language <span data-container="#lang_code_modal" data-toggle="popover" data-trigger="hover" data-placement="right" data-content="We are using free api service for translation which converts accurate to 60%. For more accurate results please visit https://translate.google.com/"><i class="fa fa-info-circle"></i></span></button>
                            </div>
                        <?php }
                                } else {
                                    if ($vCode === $default_lang) { ?>
                            <div class="col-lg-12" style="margin-top: 10px">
                                <button type ="button" name="allLanguage" id="allLanguage" class="btn btn-primary" onClick="getAllLanguageCode('vValue_', '<?php echo $default_lang; ?>');">Convert To All Language <span data-container="#lang_code_modal" data-toggle="popover" data-trigger="hover" data-placement="right" data-content="We are using free api service for translation which converts accurate to 60%. For more accurate results please visit https://translate.google.com/"><i class="fa fa-info-circle"></i></span></button>
                            </div>
                        <?php }
                                    }
                        }
                    ?>
                </div>
            <?php }
                }
    ?>
        <div class="row">
            <div class="col-lg-12">
    			<?php if ($userObj->hasPermission('edit-general-label')) { ?>
                    <input type="submit" class="btn btn-default" name="submit_lang_code" id="submit" value="Edit Label">
    			<?php } ?>

                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
    </form>
</div>
<script type="text/javascript">
	$(function () {
	  $('[data-toggle="popover"]').popover();
	})
</script>
<?php } ?>