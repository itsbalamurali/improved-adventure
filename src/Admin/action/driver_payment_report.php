<?php



include_once '../../common.php';
ob_clean();

$reload = $_SERVER['REQUEST_URI'];
// echo "<pre>"; print_r($_REQUEST); die;

$urlparts = explode('?', $reload);
$parameters = $urlparts[1];

$action = $_REQUEST['actionpayment'] ?? 'view';
$ePayDriver = $_REQUEST['ePayDriver'] ?? '';

if ('pay_driver' === $action && 'Yes' === $_REQUEST['ePayDriver']) {
    if (SITE_TYPE !== 'Demo') {
        $iTripId = $_REQUEST['iTripId'];
        for ($k = 0; $k < count($iTripId); ++$k) {
            /*$sql = "SELECT ePaymentDriverStatus from payments WHERE iTripId = '".$iTripId[$k]."' and ePaymentDriverStatus = 'UnPaid'";
            $db_pay = $obj->MySQLSelect($sql);
            if(count($db_pay) > 0){
              $query = "UPDATE payments SET ePaymentDriverStatus = 'Paid' WHERE iTripId = '" .$iTripId[$k]. "'";
              $obj->sql_query($query);

              $query = "UPDATE trips SET eDriverPaymentStatus = 'Settelled', ePayment_request = 'Yes' WHERE iTripId = '" .$iTripId[$k]. "'";
              $obj->sql_query($query);
            }else{*/
            $query = "UPDATE trips SET eDriverPaymentStatus = 'Settelled' WHERE iTripId = '".$iTripId[$k]."'";
            $obj->sql_query($query);
            // }
        }
        $_SESSION['success'] = '1';
        $_SESSION['var_msg'] = 'Record(s) marked as settled successfully.';
    } else {
        $_SESSION['success'] = '2';
    }
    header('Location:'.$tconfig['tsite_url_main_admin'].'driver_payment_report.php?'.$parameters);

    exit;
}
