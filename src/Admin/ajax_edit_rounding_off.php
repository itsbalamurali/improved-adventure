<?php



include_once 'common.php';

$iCurrencyId = $_POST['iCurrencyId'] ?? '0';
$fMiddleRangeValue = $_POST['fMiddleRangeValue'] ?? '0';
$fMiddleRangeValue = $_POST['iFirstRangeValue1'] ?? '';
$iFirstRangeValue = $_POST['iFirstRangeValue'] ?? '';
$iSecRangeValue = $_POST['iSecRangeValue'] ?? '';

$tbl_name = 'currency';

$q = 'UPDATE ';
$where = " WHERE `iCurrencyId` = '".$iCurrencyId."'";
$query = $q.' `'.$tbl_name."` SET
			`fMiddleRangeValue` = '".$fMiddleRangeValue."',
            `fFirstRangeValue` = '".$iFirstRangeValue."',
            `fSecRangeValue` = '".$iSecRangeValue."'"
            .$where;

$obj->sql_query($query);
$id = ('' !== $id) ? $id : $obj->GetInsertId();
echo $id;

exit;
