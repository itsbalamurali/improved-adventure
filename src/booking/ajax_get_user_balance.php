<?php



// error_reporting(E_ALL);
include_once '../common.php';
if (!empty($_SESSION['sess_iAdminUserId'])) {
} else {
    $AUTH_OBJ->checkMemberAuthentication();
}

$iDriverId = $_REQUEST['driverId'] ?? '';
$type = $_REQUEST['type'] ?? '';

$user_available_balance = $WALLET_OBJ->FetchMemberWalletBalance($iDriverId, $type);
$cont = '';
if ('Yes' === $COMMISION_DEDUCT_ENABLE) {
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
