<?php



include_once '../../common.php';

$reload = $_SERVER['REQUEST_URI'];

$urlparts = explode('?', $reload);
$parameters = $urlparts[1];

$action = $_REQUEST['action'] ?? 'view';
$ePayDriver = $_REQUEST['ePayDriver'] ?? '';

if ('pay_driver' === $action && 'Yes' === $ePayDriver) {
    if (SITE_TYPE !== 'Demo') {
        $startDate = $_REQUEST['startDate'] ?? '';
        $endDate = $_REQUEST['endDate'] ?? '';
        $ssql = '';
        if ('' !== $startDate) {
            $ssql .= " AND Date(tTripRequestDate) >='".$startDate."'";
        }
        if ('' !== $endDate) {
            $ssql .= " AND Date(tTripRequestDate) <='".$endDate."'";
        }

        $ssql .= " AND eDriverPaymentStatus='Unsettelled' AND eSystem = 'DeliverAll'";

        foreach ($_REQUEST['iDriverId'] as $ids) {
            $sql1 = " UPDATE trips set eDriverPaymentStatus = 'Settelled'
			WHERE iDriverId = '".$ids."' AND eDriverPaymentStatus='Unsettelled' {$ssql}";
            $obj->sql_query($sql1);
        }
        $_SESSION['success'] = '1';
        $_SESSION['var_msg'] = 'Record(s) mark as settlled successful.';
    } else {
        $_SESSION['success'] = '2';
    }
    header('Location:'.$tconfig['tsite_url_main_admin'].'store_driver_pay_report.php?'.$parameters);

    exit;
}
