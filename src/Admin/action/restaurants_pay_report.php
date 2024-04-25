	<?php
include_once '../../common.php';

$reload = $_SERVER['REQUEST_URI'];

$urlparts = explode('?', $reload);
$parameters = $urlparts[1];

$action = $_REQUEST['action'] ?? 'view';
$ePayRestaurant = $_REQUEST['ePayRestaurant'] ?? '';

if ('pay_restaurant' === $action && 'Yes' === $_REQUEST['ePayRestaurant']) {
    if (SITE_TYPE !== 'Demo') {
        $startDate = $_REQUEST['startDate'] ?? '';
        $endDate = $_REQUEST['endDate'] ?? '';
        $ssql = '';
        if ('' !== $startDate) {
            $ssql .= " AND Date(tOrderRequestDate) >='".$startDate."'";
        }
        if ('' !== $endDate) {
            $ssql .= " AND Date(tOrderRequestDate) <='".$endDate."'";
        }

        foreach ($_REQUEST['iCompanyId'] as $ids) {
            $sql1 = " UPDATE orders set eRestaurantPaymentStatus = 'Settled'
			WHERE iCompanyId = '".$ids."' AND eRestaurantPaymentStatus='Unsettled' {$ssql}";
            $obj->sql_query($sql1);
        }
        $_SESSION['success'] = '1';
        $_SESSION['var_msg'] = 'Record(s) mark as settlled successful.';
    } else {
        $_SESSION['success'] = '2';
    }
    header('Location:'.$tconfig['tsite_url_main_admin'].'restaurants_pay_report.php?'.$parameters);

    exit;
}
?>