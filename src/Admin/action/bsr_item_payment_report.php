<?php



include_once '../../common.php';

$reload = $_SERVER['REQUEST_URI'];

$urlparts = explode('?', $reload);

$parameters = $urlparts[1];

$actionpay = $_REQUEST['actionpay'] ?? 'view';

$ePayUser = $_REQUEST['ePayUser'] ?? '';

if ('pay_user' === $actionpay && 'Yes' === $_REQUEST['ePayUser']) {
    if (SITE_TYPE !== 'Demo') {
        foreach ($_REQUEST['iRentItemPostId'] as $ids) {
            $sql1 = " UPDATE rentitem_post set eUserPayment = 'Settled' WHERE iRentItemPostId = '".$ids."' AND eUserPayment = 'Unsettled' {$ssql}";

            $obj->sql_query($sql1);
        }

        // echo "<pre>";print_r($db_payment1);exit;

        $_SESSION['success'] = '1';

        $_SESSION['var_msg'] = 'Record(s) marked as settled successfully.';
    } else {
        $_SESSION['success'] = '2';
    }

    header('Location:'.$tconfig['tsite_url_main_admin'].'bsr_item_payment_report.php?'.$parameters);

    exit;
}
