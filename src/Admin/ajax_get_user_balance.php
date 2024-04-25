<?php



include_once '../common.php';

$iDriverId = $_REQUEST['driverId'] ?? '';
$type = $_REQUEST['type'] ?? '';

$user_available_balance = $WALLET_OBJ->FetchMemberWalletBalance($iDriverId, $type);
$cont = '';
$enableCommisionDeduct = $MODULES_OBJ->autoDeductDriverCommision('Ride'); // Added By HJ On 16-10-2020 For get Auto Deduct Driver Commision Configuration As Per eSystem
if ('Yes' === $enableCommisionDeduct) {
    if ($user_available_balance > $WALLET_MIN_BALANCE) {
        $cont .= 1;
        $cont .= '|'.$user_available_balance;
    } else {
        $cont .= 0;
        $cont .= '|'.$user_available_balance;
    }
} else {
    $cont .= 1;
    $cont .= '|'.$user_available_balance;
}

echo $cont;

exit;
