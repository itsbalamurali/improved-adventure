<?php





include_once 'common.php';

$responce = [];
$fromOrder = 'guest';
if (isset($_REQUEST['fromorder']) && '' !== $_REQUEST['fromorder']) {
    $fromOrder = $_REQUEST['fromorder'];
}
$orderDetailsSession = 'ORDER_DETAILS_'.strtoupper($fromOrder);
$OrderDetails = $_SESSION[$orderDetailsSession];
$count = count($_SESSION[$orderDetailsSession]);
unset($_SESSION[$orderDetailsSession]);
$id = $_REQUEST['id'];
$cart_id = $_REQUEST['cart_id_update'];
$numbercart_update = $_REQUEST['numbercart_update'];
$responce['OrderDetails'] = [];
for ($i = 0; $i < $count; ++$i) {
    $addoptions = [];
    if ($i === $cart_id) {
        $addoptions['iMenuItemId'] = $OrderDetails[$i]['iMenuItemId'];
        $addoptions['iFoodMenuId'] = $OrderDetails[$i]['iFoodMenuId'];
        $addoptions['vOptionId'] = $OrderDetails[$i]['vOptionId'];
        $addoptions['iQty'] = $numbercart_update;
        $addoptions['vAddonId'] = $OrderDetails[$i]['vAddonId'];
        $addoptions['tInst'] = $OrderDetails[$i]['tInst'];
        $addoptions['typeitem'] = $OrderDetails[$i]['typeitem'];
        $addoptions['eFoodType'] = $OrderDetails[$i]['eFoodType'];
    } else {
        $addoptions['iMenuItemId'] = $OrderDetails[$i]['iMenuItemId'];
        $addoptions['iFoodMenuId'] = $OrderDetails[$i]['iFoodMenuId'];
        $addoptions['vOptionId'] = $OrderDetails[$i]['vOptionId'];
        $addoptions['iQty'] = $OrderDetails[$i]['iQty'];
        $addoptions['vAddonId'] = $OrderDetails[$i]['vAddonId'];
        $addoptions['tInst'] = $OrderDetails[$i]['tInst'];
        $addoptions['typeitem'] = $OrderDetails[$i]['typeitem'];
        $addoptions['eFoodType'] = $OrderDetails[$i]['eFoodType'];
    }
    $responce['OrderDetails'][] = $addoptions;
}
$_SESSION[$orderDetailsSession] = $responce['OrderDetails'];
echo json_encode(['count' => '1', 'responce' => $responce['OrderDetails']]);

exit;
