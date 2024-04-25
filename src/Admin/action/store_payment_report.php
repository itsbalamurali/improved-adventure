<?php



include_once '../../common.php';
ob_clean();

$reload = $_SERVER['REQUEST_URI'];

$urlparts = explode('?', $reload);
$parameters = $urlparts[1];

$action = $_REQUEST['actionpayment'] ?? 'view';
$ePayRestaurant = $_REQUEST['ePayRestaurant'] ?? '';

if ('pay_restaurant' === $action && 'Yes' === $_REQUEST['ePayRestaurant']) {
    if (SITE_TYPE !== 'Demo') {
        $iOrderId = $_REQUEST['iOrderId'];
        for ($k = 0; $k < count($iOrderId); ++$k) {
            /* $sql = "SELECT ePaymentDriverStatus from payments WHERE iOrderId = '".$iOrderId[$k]."' and ePaymentDriverStatus = 'UnPaid'";
             $db_pay = $obj->MySQLSelect($sql);
             if(count($db_pay) > 0){
               $query = "UPDATE payments SET ePaymentDriverStatus = 'Paid' WHERE iOrderId = '" .$iOrderId[$k]. "'";
               $obj->sql_query($query);

               $query = "UPDATE trips SET eDriverPaymentStatus = 'Settelled', ePayment_request = 'Yes' WHERE iOrderId = '" .$iOrderId[$k]. "'";
               $obj->sql_query($query);
             }else{*/
            $query = "UPDATE orders SET eRestaurantPaymentStatus = 'Settled' WHERE iOrderId = '".$iOrderId[$k]."'";
            $obj->sql_query($query);
            // }
        }
        $_SESSION['success'] = '1';
        $_SESSION['var_msg'] = 'Record(s) mark as settlled successful.';
    } else {
        $_SESSION['success'] = '2';
    }
    header('Location:'.$tconfig['tsite_url_main_admin'].'store_payment_report.php?'.$parameters);

    exit;
}
