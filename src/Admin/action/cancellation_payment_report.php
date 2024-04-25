<?php



include_once '../../common.php';
ob_clean();

$reload = $_SERVER['REQUEST_URI'];
// echo "<pre>"; print_r($_REQUEST); die;

$urlparts = explode('?', $reload);
$parameters = $urlparts[1];

$action = $_REQUEST['actionpayment'] ?? 'view';
$ePayDriver = $_REQUEST['ePayDriver'] ?? '';
$organization = $_REQUEST['organization'] ?? '0';
$redirectUrl = $tconfig['tsite_url_main_admin'].'cancellation_payment_report.php?'.$parameters;
if (1 === $organization) {
    $redirectUrl = $tconfig['tsite_url_main_admin'].'org_cancellation_payment_report.php?'.$parameters;
}
if ('pay_driver' === $action && 'Yes' === $_REQUEST['ePayDriver']) {
    if (SITE_TYPE !== 'Demo') {
        $iTripId = $_REQUEST['iTripId'];
        for ($k = 0; $k < count($iTripId); ++$k) {
            $query = "UPDATE trip_outstanding_amount SET ePaidToDriver = 'Yes' WHERE iTripId = '".$iTripId[$k]."'";
            $obj->sql_query($query);

            $query1 = "UPDATE trips SET eDriverPaymentStatus = 'Settelled' WHERE iTripId = '".$iTripId[$k]."'";
            $obj->sql_query($query1);
        }
        $_SESSION['success'] = '1';
        $_SESSION['var_msg'] = 'Record(s) marked as settled successfully.';
    } else {
        $_SESSION['success'] = '2';
    }
    header('Location:'.$redirectUrl);

    exit;
}
