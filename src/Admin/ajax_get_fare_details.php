<?php



include_once '../common.php';

$action = $_REQUEST['action'] ?? '';
$iTripId = $_REQUEST['tripId'] ?? '';
$userId = $_REQUEST['userId'] ?? '';

if ('' !== $action) {
    $orgDataArr = $subTotalChange = [];
    $orgData = $obj->MySQLSelect('SELECT vCompany,iOrganizationId FROM organization ORDER BY iOrganizationId ASC');
    for ($g = 0; $g < count($orgData); ++$g) {
        $orgDataArr[$orgData[$g]['iOrganizationId']] = $orgData[$g]['vCompany'];
    }
    $fWalletDebit = $fCommision = 0;

    if ('cashreceived' === $action) {
        $db_trip_data = FetchTripFareDetailsWeb($iTripId, $userId, 'Passenger', '', '', 'Admin');
    // echo "<pre>";print_r($db_trip_data);die;
    } else {
        $db_trip_data = FetchTripFareDetailsWeb($iTripId, '', '');
        if (isset($db_trip_data['fWalletDebit']) && $db_trip_data['fWalletDebit'] > 0) {
            // $fWalletDebit = $db_trip_data['fWalletDebit'];
            $fWalletDebit = $db_trip_data['fWalletDebit_value'];
        }
        if (isset($db_trip_data['fCommision']) && $db_trip_data['fCommision'] > 0) {
            $fCommision = $db_trip_data['fCommision'];
        }
    }
    // echo "<pre>";print_r($db_trip_data);die;
    // echo "<pre>";print_r($db_trip_data['HistoryFareDetailsNewArr']);die;
    $currencySymbol = '$';
    if (isset($db_trip_data['CurrencySymbol']) && !empty($db_trip_data['CurrencySymbol'])) {
        $currencySymbol = $db_trip_data['CurrencySymbol'];
    }
    $organizationName = '';
    if (isset($orgDataArr[$db_trip_data['iOrganizationId']]) && '' !== $orgDataArr[$db_trip_data['iOrganizationId']] && 'Organization' === $db_trip_data['ePaymentBy']) {
        $organizationName = $orgDataArr[$db_trip_data['iOrganizationId']];
    }
    $invoiceHtml = '<table style="width:100%" cellpadding="5" cellspacing="0" border="0"><tbody>';
    $invoiceHtml .= '<tr><td><b>Description</b></td><td align="right"><b>Amount</b></td></tr>';
    $invoiceHtml .= '<tr><td colspan="2"><div style="border-top:1px dashed #d1d1d1"></div></td></tr>';
    foreach ($db_trip_data['HistoryFareDetailsNewArr'] as $key => $value) {
        // echo "<pre>";print_r($value);die;
        foreach ($value as $k => $val) {
            if ('totalFare' === $action && ($k === $langage_lbl_admin['LBL_Commision'] || $k === $langage_lbl_admin['LBL_PROMO_DISCOUNT_TITLE'] || $k === $langage_lbl_admin['LBL_WALLET_ADJUSTMENT'])) {
                // For Total Fare
            } else {
                if ($k === $langage_lbl_admin['LBL_EARNED_AMOUNT']) {
                    continue;
                }
                if ($k === $langage_lbl_admin['LBL_SUBTOTAL_TXT']) {
                    continue;
                }
                if ('eDisplaySeperator' === $k) {
                    $invoiceHtml .= '<tr><td colspan="2"><div style="border-top:1px dashed #d1d1d1"></div></td></tr>';
                } else {
                    $invoiceHtml .= '<tr><td>'.$k.'</td><td align="right">'.$val.'</td></tr>';
                }
            }
        }
    }
    $invoiceHtml .= '<tr><td colspan = "2"><hr style = "margin-bottom:0px"/></td></tr><tr><td><b>';
    $invoiceHtml .= $langage_lbl_admin['LBL_Total_Fare_TXT'].'(Via';
    if ('Card' === $db_trip_data['vTripPaymentMode'] && 'Yes' === $db_trip_data['ePayWallet']) {
        $invoiceHtml .= $langage_lbl_admin['LBL_WALLET_TXT'];
    } else {
        $invoiceHtml .= $db_trip_data['vTripPaymentMode'];
    }
    $invoiceHtml .= ')';
    if ('' !== $organizationName) {
        $invoiceHtml .= '<br>Organization :'.$organizationName;
    }
    $FareSubTotal = $db_trip_data['FareSubTotal'];
    if ('totalFare' === $action) {
        // $FareSubTotal = $currencySymbol . ($db_trip_data['iOriginalFare'] + $fWalletDebit);
        $totalfare = $db_trip_data['iOriginalFare_value'] + $fWalletDebit + $db_trip_data['fDiscount_value'];
        // $FareSubTotal = $currencySymbol . (formatNum($totalfare));
        $FareSubTotal = formateNumAsPerCurrency($totalfare, '');
    }
    $invoiceHtml .= '</b></td><td align="right"><b>'.$FareSubTotal.'</b></td></tr>';
    $invoiceHtml .= '</tbody></table>';
    echo $invoiceHtml;

    exit;
}
