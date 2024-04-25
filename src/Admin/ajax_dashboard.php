<?php



include_once '../common.php';

include_once $tconfig['tpanel_path'].'assets/libraries/Models/class.dashboard.php';
$Dashboard_OBJ = new Dashboard();

$chart_type = (isset($_REQUEST['chart_type']) && !empty($_REQUEST['chart_type'])) ? $_REQUEST['chart_type'] : '';
$year = (isset($_REQUEST['year']) && !empty($_REQUEST['year'])) ? $_REQUEST['year'] : date('Y');
$getMonth = (isset($_REQUEST['getMonth']) && !empty($_REQUEST['getMonth'])) ? $_REQUEST['getMonth'] : 0;

if ('Earning_Report' === $chart_type) {
    $earning_month = [];
    $total_Earns = [];
    // for ($i = $getMonth; $i > 0; $i--) {

    for ($i = 1; $i <= $getMonth; ++$i) {
        $getMonthTimeStamp = mktime(0, 0, 0, $i, 1, $year);
        $month = date('F', $getMonthTimeStamp);

        $startdate = date($year.'-'.$i.'-01 00:00:00', strtotime("-{$i} month"));
        $enddate = date($year.'-'.$i.'-t 23:59:59', strtotime($month.'-'.$year));
        // $enddate = date($year.'-'.$i.'-t 23:59:59', strtotime("-$i month"));

        // $earning_month[] = date('M', strtotime("-$i month"));
        $earning_month[] = date('M', strtotime($startdate));
        $total_Earns[] = getTotalEarns($startdate, $enddate);
    }

    $data['earning_month'] = $earning_month;
    $data['total_Earns'] = $total_Earns;
}

if ('Earning_Report_six' === $chart_type) {
    $earning_month = [];
    $total_Earns = [];
    // $earning_month[] = date('M');
    // $total_Earns[] = getStoreTripStates(date($year . "-m-01 00:00:00"), date($year . "-m-t 23:59:59"));
    $getSetupInfo = $Dashboard_OBJ->getSetupInfo();

    for ($i = $getMonth - 1; $i >= 0; --$i) {
        $startdate = date('Y-m-01 00:00:00', strtotime("-{$i} month"));
        $enddate = date('Y-m-t 23:59:59', strtotime("-{$i} month"));
        if (date('Ym', strtotime($getSetupInfo[0]['dSetupDate'])) <= date('Ym', strtotime($startdate))) {
            $earning_month[] = date('Y-M', strtotime("-{$i} month"));
            if (ONLYDELIVERALL === 'Yes') {
                $total_Earns[] = getStoreTotalEarns($startdate, $enddate);
            } else {
                $total_Earns[] = getTotalEarns($startdate, $enddate);
            }
        }
    }

    $data['earning_month'] = $earning_month;
    $data['total_Earns'] = $total_Earns;
}

if ('Total_Ride_jobs' === $chart_type) {
    $month = [];
    $finishRidetotalByMonth = [];
    $cancelledRidetotalByMonth = [];
    // for ($i = 12; $i > 0; $i--) {
    //     $startdate = date($year . '-m-01 00:00:00', strtotime("-$i month"));
    //     $enddate = date($year . '-m-t 23:59:59', strtotime("-$i month"));

    //     $month[] = date('M', strtotime("-$i month"));
    //     $cancelledRidetotalByMonth[] = getTripStates('cancelled', $startdate, $enddate, '1');
    //     $finishRidetotalByMonth[] = getTripStates('finished', $startdate, $enddate, '1');
    // }

    for ($i = 1; $i <= 12; ++$i) {
        $getMonthTimeStamp = mktime(0, 0, 0, $i, 1, $year);
        $currentmonth = date('F', $getMonthTimeStamp);

        $startdate = date($year.'-'.$i.'-01 00:00:00', strtotime("-{$i} month"));
        $enddate = date($year.'-'.$i.'-t 23:59:59', strtotime($currentmonth.'-'.$year));
        // $enddate = date($year.'-'.$i.'-t 23:59:59', strtotime("-$i month"));

        $month[] = date('M', strtotime($startdate));
        $cancelledRidetotalByMonth[] = getTripStates('cancelled', $startdate, $enddate, '1');
        $finishRidetotalByMonth[] = getTripStates('finished', $startdate, $enddate, '1');
    }
    $data['month'] = $month;
    $data['cancelledRidetotalByMonth'] = $cancelledRidetotalByMonth;
    $data['finishRidetotalByMonth'] = $finishRidetotalByMonth;
}

if ('Total_Order' === $chart_type) {
    $order_month = [];
    $finishOrdertotalByMonth = [];
    $cancelledOrdertotalByMonth = [];
    // for ($i = 12; $i > 0; $i--) {
    //     $startdate = date($year . '-m-01 00:00:00', strtotime("-$i month"));
    //     $enddate = date($year . '-m-t 23:59:59', strtotime("-$i month"));

    //     $order_month[] = date('M', strtotime("-$i month"));
    //     $cancelledOrdertotalByMonth[] = getStoreTripStates('Cancelled', $startdate, $enddate, '1');
    //     $finishOrdertotalByMonth[] = getStoreTripStates('Delivered', $startdate, $enddate, '1');
    // }

    for ($i = 1; $i <= 12; ++$i) {
        $getMonthTimeStamp = mktime(0, 0, 0, $i, 1, $year);
        $month = date('F', $getMonthTimeStamp);

        $startdate = date($year.'-'.$i.'-01 00:00:00', strtotime("-{$i} month"));
        $enddate = date($year.'-'.$i.'-t 23:59:59', strtotime($month.'-'.$year));
        // $enddate = date($year.'-'.$i.'-t 23:59:59', strtotime("-$i month"));

        $order_month[] = date('M', strtotime($startdate));
        $cancelledOrdertotalByMonth[] = getStoreTripStates('Cancelled', $startdate, $enddate, '1');
        $finishOrdertotalByMonth[] = getStoreTripStates('Delivered', $startdate, $enddate, '1');
    }

    $data['order_month'] = $order_month;
    $data['cancelledOrdertotalByMonth'] = $cancelledOrdertotalByMonth;
    $data['finishOrdertotalByMonth'] = $finishOrdertotalByMonth;
}
if ('user_and_provider' === $chart_type) {
    // for ($i = 12; $i > 0; $i--) {
    for ($i = 1; $i <= 12; ++$i) {
        $getMonthTimeStamp = mktime(0, 0, 0, $i, 1, $year);
        $currentmonth = date('F', $getMonthTimeStamp);

        $startdate = date($year.'-'.$i.'-01 00:00:00', strtotime("-{$i} month"));
        $enddate = date($year.'-'.$i.'-t 23:59:59', strtotime($currentmonth.'-'.$year));
        // $enddate = date($year . '-'.$i.'-t 23:59:59', strtotime("-$i month"));

        // $months[] = date('M', strtotime("-$i month"));
        $months[] = date('M', strtotime($startdate));
        $getRiderCount = getRiderCount('finished', $startdate, $enddate, '1');
        $user[] = $getRiderCount[0]['count(iUserId)'];
        $provider[] = getDriverDetailsDashboard('active', $startdate, $enddate);
        $store[] = getStoreDetailsDashboard('active', $startdate, $enddate);
    }

    $data['months'] = $months;
    $data['user'] = $user;
    $data['provider'] = $provider;
    $data['store'] = $store;
}
if ('server_status_chart' === $chart_type) {
    $working = 0;
    $missing = 0;
    $server_settings = $Dashboard_OBJ->server_settings();
    $phpini_settings = $Dashboard_OBJ->phpini_settings();
    $php_modules = $Dashboard_OBJ->php_modules();
    $mysql_settings = $Dashboard_OBJ->mysql_settings();
    $mysql_suggestions = $Dashboard_OBJ->mysql_suggestions();
    $folder_permissions = $Dashboard_OBJ->folder_permissions();

    if (1 === $server_settings) {
        ++$working;
    } else {
        ++$missing;
    }
    if (1 === $phpini_settings) {
        ++$working;
    } else {
        ++$missing;
    }
    if (1 === $php_modules) {
        ++$working;
    } else {
        ++$missing;
    }
    if (1 === $mysql_settings) {
        ++$working;
    } else {
        ++$missing;
    }

    if (1 === $mysql_suggestions) {
        ++$working;
    } else {
        ++$missing;
    }

    if (1 === $folder_permissions) {
        ++$working;
    } else {
        ++$missing;
    }

    $data['status'] = ['working', 'missing'];
    $data['number'] = [$working, $missing];

    $data['working'] = 6;
    $data['missing'] = 3;
}
$returnArr['Action'] = 1;
$returnArr['data'] = $data;
echo json_encode($returnArr);

exit;
