<?php

include 'common.php';
if(!empty($_REQUEST['id'])){
    $sql = "select vCurrency from country where vCountryCode = '" . $_REQUEST['id'] . "'";
    $db_data = $obj->MySQLSelect($sql);

    $sql = "select * from currency where `vName` = '" . $db_data[0]['vCurrency'] . "' AND eStatus='Active' ";
    $edit_data = $obj->sql_query($sql);

    $sqldef = "select * from  currency where eStatus='Active' && eDefault='Yes' ORDER BY  iDispOrder ASC";
    $db_defcurrency = $obj->MySQLSelect($sqldef);

    if(!empty($edit_data)){
        $defaultCurrency = $db_data[0]['vCurrency'];
    } else {
         $defaultCurrency = $db_defcurrency[0]['vName'];
    }

}
$sql = "SELECT * FROM currency WHERE eStatus='Active' ORDER BY iDispOrder ASC";
$db_currency = $obj->MySQLSelect($sql);
?>
<label><?= $langage_lbl['LBL_SELECT_CURRENCY_SIGNUP']; ?></label>
<select class="" required name = 'vCurrencyPassenger'>
        <?php for ($i = 0; $i < count($db_currency); $i++) { ?>
            <option value = "<?= $db_currency[$i]['vName'] ?>" <? if ($defaultCurrency == $db_currency[$i]['vName']) { ?>selected<? } ?>>
            <?= $db_currency[$i]['vName'] ?>
            </option>
        <? } ?>
</select>