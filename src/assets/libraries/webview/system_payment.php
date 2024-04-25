<?php

include_once('../../../common.php');

global $dataHelperObj;
if(empty($dataHelperObj))
{
    include_once "../../../DataHelper.php";
    $dataHelperObj = new DataHelper();
}

$page_type = isset($_REQUEST['PAGE_TYPE']) ? $_REQUEST['PAGE_TYPE'] : "";

if($page_type != "")
{
    
    /* Check user session */
    $tSessionId = isset($_REQUEST['tSessionId']) ? trim($_REQUEST['tSessionId']) : '';
    $GeneralMemberId = isset($_REQUEST['GeneralMemberId']) ? trim($_REQUEST['GeneralMemberId']) : '';
    $GeneralUserType = isset($_REQUEST['GeneralUserType']) ? trim($_REQUEST['GeneralUserType']) : '';

    if ($tSessionId == "" || $GeneralMemberId == "" || $GeneralUserType == "") {
        $returnArr['Action'] = "0";
        $returnArr['message'] = "SESSION_OUT";
        setDataResponse($returnArr);
    } else {
        if (strtolower($GeneralUserType) == "hotel" || strtolower($GeneralUserType) == "kiosk") {
            $userData = get_value("hotel", "iHotelId as iMemberId,tSessionId", "iHotelId", $GeneralMemberId);
        } else {
            $userData = get_value($GeneralUserType == "Driver" ? "register_driver" : "register_user", $GeneralUserType == "Driver" ? "iDriverId as iMemberId,tSessionId" : "iUserId as iMemberId,tSessionId", $GeneralUserType == "Driver" ? "iDriverId" : "iUserId", $GeneralMemberId);
        }

        if ($userData[0]['iMemberId'] != $GeneralMemberId || $userData[0]['tSessionId'] != $tSessionId) {
            $returnArr['Action'] = "0";
            $returnArr['message'] = "SESSION_OUT";
            setDataResponse($returnArr);
        }
    }
    /* End check user session */
    unset($_REQUEST['currency']);
    $REQUEST_PARAMS = $_REQUEST;
    $AMOUNT = $REQUEST_PARAMS['AMOUNT'];
    
    if(isset($_REQUEST['amount']))
    {
        $AMOUNT = $_REQUEST['amount'];
        $REQUEST_PARAMS['AMOUNT'] = $AMOUNT;
    }
    $GeneralMemberId = trim($_REQUEST['GeneralMemberId']);
    $GeneralUserType = trim($_REQUEST['GeneralUserType']);
    if ($GeneralUserType == "Rider" || $GeneralUserType == "Passenger") {
        $sql = "SELECT ru.iUserId,ru.vCurrencyPassenger as vCurrency,ru.vLang,cu.vSymbol,cu.Ratio,cu.eRoundingOffEnable,co.vPaymentGateway FROM `register_user` as ru LEFT JOIN currency as cu ON ru.vCurrencyPassenger = cu.vName LEFT JOIN country as co ON ru.vCountry = co.vCountryCode WHERE ru.iUserId='" . $GeneralMemberId . "'";
    } else {
        $sql = "SELECT rd.iDriverId,rd.vCurrencyDriver as vCurrency,rd.vLang,cu.vSymbol,cu.Ratio,cu.eRoundingOffEnable,co.vPaymentGateway FROM `register_driver` as rd LEFT JOIN currency as cu ON rd.vCurrencyDriver = cu.vName LEFT JOIN country as co ON rd.vCountry = co.vCountryCode WHERE rd.iDriverId='" . $GeneralMemberId . "'";
    }
    $userData = $obj->MySQLSelect($sql);

    $DefaultCurrencyData = get_value('currency', 'vName,Ratio,eRoundingOffEnable', 'eDefault', 'Yes');
    $currencyCode = $DefaultCurrencyData[0]['vName'];
    $currencyratio = $DefaultCurrencyData[0]['Ratio'];
    // $AMOUNT = $AMOUNT * $currencyratio;
    // $REQUEST_PARAMS['AMOUNT'] = $AMOUNT;

    if($page_type == "WALLET_MONEY_ADD" || $page_type == "RIDE_TIP_COLLECT" || $page_type == "ORDER_TIP_COLLECT"  || $page_type == "GIFT_CARD_PAYMENT")
    {
        $Ratio = $userData[0]['Ratio'];
        $currencySymbol = $userData[0]['vSymbol'];
        $vLang = $userData[0]['vLang'];
        $userCurrencyRatio = $Ratio;
        $price_new = $AMOUNT / $userCurrencyRatio;
        $price_new = $price_new * $currencyratio;
        
        $AMOUNT = $price_new;
        $REQUEST_PARAMS['AMOUNT'] = (string)$AMOUNT;
    }

    // echo "<pre>"; print_r($REQUEST_PARAMS); exit();
    if (strtoupper($DEFAULT_CURRENCY_CONVERATION_ENABLE) == 'YES' && !empty($DEFAULT_CURRENCY_CONVERATION_CODE_RATIO) && !empty($DEFAULT_CURRENCY_CONVERATION_CODE) && $DEFAULT_CURRENCY_CONVERATION_CODE_RATIO > 0) {
        $DefaultConverationRatio = $DEFAULT_CURRENCY_CONVERATION_CODE_RATIO;
        $price_new = $AMOUNT / 100;
        $price_new = (round(($price_new * $DefaultConverationRatio), 2) * 100);
        $currency = $DEFAULT_CURRENCY_CONVERATION_CODE;
        $REQUEST_PARAMS['AMOUNT'] = $price_new;
    }

    /*if(strtoupper($userData[0]['eRoundingOffEnable']) == 'YES')
    {
        $REQUEST_PARAMS['AMOUNT'] = round($REQUEST_PARAMS['AMOUNT']);
    }*/

    
    if(($page_type == "RIDE_TIP_COLLECT" || $page_type == "ORDER_TIP_COLLECT") && isset($_REQUEST['eForTip']) && $_REQUEST['eForTip'] == "Yes" && isset($_REQUEST['CheckUserWallet']) && $_REQUEST['CheckUserWallet'] == "Yes")
    {
        $UserType1 = ($GeneralUserType == "Rider" || $GeneralUserType == "Passenger") ? "Rider" : "Driver";
        $iUserId = $GeneralMemberId;
        $price_new = $REQUEST_PARAMS['AMOUNT'];
        $user_available_balance = $WALLET_OBJ->FetchMemberWalletBalance($iUserId, $UserType1, true);
        // echo $user_available_balance; exit();
        if ($user_available_balance >= $price_new && $price_new > 0) {
            if($page_type == "RIDE_TIP_COLLECT")
            {
                $iTripId = $_REQUEST['iTripId'];
                $where = " iTripId = '$iTripId'";
                $data['fTipPrice'] = $price_new;
                $id = $obj->MySQLQueryPerform("trips", $data, 'update', $where);
                $vRideNo = get_value('trips', 'vRideNo', 'iTripId', $iTripId, '', 'true');

                $wallet_description =  "#LBL_DEBITED_BOOKING#" . $vRideNo;
            }
            else {
                $iOrderId = $_REQUEST['iOrderId'];
                $orderData = get_value('orders', 'vOrderNo,fNetTotal,fTotalGenerateFare', 'iOrderId', $iOrderId);
                $vOrderNo = $orderData[0]['vOrderNo'];
                $orderTotal = $orderData[0]['fNetTotal'];
                $orderGenerateFareTotal = $orderData[0]['fTotalGenerateFare'];
                
                $whereOrder = " iOrderId = '$iOrderId'";
                $dataOrder['fTipAmount'] = $price_new;
                $dataOrder['fNetTotal'] = $price_new + $orderTotal;
                $dataOrder['fTotalGenerateFare'] = $price_new + $orderGenerateFareTotal;
                $dataOrder['eTipIncludedAtOrderRequest'] = 'No';
                $obj->MySQLQueryPerform("orders", $dataOrder, 'update', $whereOrder);

                $wallet_description =  '#LBL_DEBITED_TIP_AMOUNT_TXT#' . " - " . $vOrderNo;  
            }
            
            $data_wallet['iUserId'] = $iUserId;
            $data_wallet['eUserType'] = $UserType1;
            $data_wallet['iBalance'] = $price_new;
            $data_wallet['eType'] = "Debit";
            $data_wallet['dDate'] = date("Y-m-d H:i:s");
            $data_wallet['iTripId'] = $iTripId;
            $data_wallet['iOrderId'] = $iOrderId;
            $data_wallet['eFor'] = "Charges";
            $data_wallet['ePaymentStatus'] = "Unsettelled";
            $data_wallet['tDescription'] = $wallet_description;
            $WALLET_OBJ->PerformWalletTransaction($data_wallet['iUserId'], $data_wallet['eUserType'], $data_wallet['iBalance'], $data_wallet['eType'], $data_wallet['iTripId'], $data_wallet['eFor'], $data_wallet['tDescription'], $data_wallet['ePaymentStatus'], $data_wallet['dDate'], $data_wallet['iOrderId']);
            
            if($MODULES_OBJ->isAutoCreditToDriverModuleAvailable()) {
                include_once($tconfig['tpanel_path'] . 'include/features/include_auto_credit_driver.php');
                $DataTip['price'] = $price_new;
                $DataTip['iTripId'] = $iTripId;
                autoCreditDriverEarning($DataTip, "TripCollectTip");
            }
            

            // $returnUrl = $tconfig['tsite_url'] . "assets/libraries/webview/success.php";
        
            // header('Location:'.$returnUrl."?success=1&SYSTEM_TYPE=" . $_REQUEST['SYSTEM_TYPE']);
            $returnArr = array();
            $returnArr['Action'] = "1";
            setDataResponse($returnArr);
            exit;
        } else {
            $user_available_balance_new = round($user_available_balance, 2);
            $user_wallet_debit_amount = $user_available_balance_new;
            $price_new = $price_new - $user_available_balance_new;

            $REQUEST_PARAMS['AMOUNT'] = round($price_new, 2);
            $REQUEST_PARAMS['DEBIT_AMOUNT'] = $user_wallet_debit_amount;
        }
    }

    if($page_type == "GIFT_CARD_PAYMENT"){
        $REQUEST_PARAMS['returnUrl'] = $_REQUEST['returnUrl'];
    }
    
    $_REQUEST = $REQUEST_PARAMS;

    $request_params = http_build_query($_REQUEST);

    $USER_APP_PAYMENT_METHOD = $APP_PAYMENT_METHOD;
    if(!empty($userData[0]['vPaymentGateway']))
    {
        $USER_APP_PAYMENT_METHOD = $userData[0]['vPaymentGateway'];
    }
    
    switch ($USER_APP_PAYMENT_METHOD) {
        case 'Stripe': 
            $getWayUrl = $tconfig['tsite_url'] . "assets/libraries/webview/stripe/index_sdk.php?" . $request_params;
            if($page_type == "ADD_CARD" || $page_type == "PAYMENT_LIST")
            {
                $getWayUrl = $tconfig['tsite_url'] . "assets/libraries/webview/stripe/add_change_card.php?" . $request_params;
            }
            header('Location: ' . $getWayUrl);
            break;
        
        case 'Omise': 
            $getWayUrl = $tconfig['tsite_url'] . "assets/libraries/webview/omise/index_sdk.php?" . $request_params;
            if($page_type == "ADD_CARD" || $page_type == "PAYMENT_LIST")
            {
                $getWayUrl = $tconfig['tsite_url'] . "assets/libraries/webview/omise/add_change_card.php?" . $request_params;
            }
            header('Location: ' . $getWayUrl);
            break;

        case 'Flutterwave': 
            $getWayUrl = $tconfig['tsite_url'] . "assets/libraries/webview/flutterwave/index_sdk.php?" . $request_params;
            if($page_type == "ADD_CARD" || $page_type == "PAYMENT_LIST")
            {
                $getWayUrl = $tconfig['tsite_url'] . "assets/libraries/webview/flutterwave/add_change_card.php?" . $request_params;
            }
            header('Location: ' . $getWayUrl);
            break;

        case 'Paymaya': 
            $getWayUrl = $tconfig['tsite_url'] . "assets/libraries/webview/paymaya/index_sdk.php?" . $request_params;
            if($page_type == "ADD_CARD" || $page_type == "PAYMENT_LIST")
            {
                $getWayUrl = $tconfig['tsite_url'] . "assets/libraries/webview/paymaya/add_change_card.php?" . $request_params;
            }
            header('Location: ' . $getWayUrl);
            break;

        case 'Xendit': 
            $getWayUrl = $tconfig['tsite_url'] . "assets/libraries/webview/xendit/index_sdk.php?" . $request_params;
            if($page_type == "ADD_CARD" || $page_type == "PAYMENT_LIST")
            {
                $getWayUrl = $tconfig['tsite_url'] . "assets/libraries/webview/xendit/add_change_card.php?" . $request_params;
            }
            header('Location: ' . $getWayUrl);
            break;

        case 'Paymentez': 
            $getWayUrl = $tconfig['tsite_url'] . "assets/libraries/webview/paymentez/index_sdk.php?" . $request_params;
            if($page_type == "ADD_CARD" || $page_type == "PAYMENT_LIST")
            {
                $getWayUrl = $tconfig['tsite_url'] . "assets/libraries/webview/paymentez/add_change_card.php?" . $request_params;
            }
            header('Location: ' . $getWayUrl);
            break;

        case 'Senangpay': 
            $getWayUrl = $tconfig['tsite_url'] . "assets/libraries/webview/senangpay/index_sdk.php?" . $request_params;
            if($page_type == "ADD_CARD" || $page_type == "PAYMENT_LIST")
            {
                $getWayUrl = $tconfig['tsite_url'] . "assets/libraries/webview/senangpay/add_change_card.php?" . $request_params;
            }
            header('Location: ' . $getWayUrl);
            break;

        case 'Payfort': 
            $getWayUrl = $tconfig['tsite_url'] . "assets/libraries/webview/payfort/index_sdk.php?" . $request_params;
            if($page_type == "ADD_CARD" || $page_type == "PAYMENT_LIST")
            {
                $getWayUrl = $tconfig['tsite_url'] . "assets/libraries/webview/payfort/add_change_card.php?" . $request_params;
            }
            header('Location: ' . $getWayUrl);
            break;

        case 'Payulatam': 
            $getWayUrl = $tconfig['tsite_url'] . "assets/libraries/webview/payulatam/index_sdk.php?" . $request_params;
            if($page_type == "ADD_CARD" || $page_type == "PAYMENT_LIST")
            {
                $getWayUrl = $tconfig['tsite_url'] . "assets/libraries/webview/payulatam/add_change_card.php?" . $request_params;
            }
            header('Location: ' . $getWayUrl);
            break;

        
        case 'Serfinsa': 
            $getWayUrl = $tconfig['tsite_url'] . "assets/libraries/webview/serfinsa/index_sdk.php?" . $request_params;
            if($page_type == "ADD_CARD" || $page_type == "PAYMENT_LIST")
            {
                $getWayUrl = $tconfig['tsite_url'] . "assets/libraries/webview/serfinsa/add_change_card.php?" . $request_params;
            }
            header('Location: ' . $getWayUrl);
            break;

        case 'OrangeMobileMoney': 
            $getWayUrl = $tconfig['tsite_url'] . "assets/libraries/webview/orange_mobile_money/index_sdk.php?" . $request_params;
            
            header('Location: ' . $getWayUrl);
            break;

        case 'Mpesa': 
            $getWayUrl = $tconfig['tsite_url'] . "assets/libraries/webview/mpsa/index_sdk.php?" . $request_params;
            
            header('Location: ' . $getWayUrl);
            break;

        case 'Iugu': 
            $getWayUrl = $tconfig['tsite_url'] . "assets/libraries/webview/iugu/index_sdk.php?" . $request_params;
            if($page_type == "ADD_CARD" || $page_type == "PAYMENT_LIST")
            {
                $getWayUrl = $tconfig['tsite_url'] . "assets/libraries/webview/iugu/add_change_card.php?" . $request_params;
            }
            header('Location: ' . $getWayUrl);
            break;

        case 'Iyzico': 
            $getWayUrl = $tconfig['tsite_url'] . "assets/libraries/webview/iyzico/index_sdk.php?" . $request_params;
            if($page_type == "ADD_CARD" || $page_type == "PAYMENT_LIST")
            {
                $getWayUrl = $tconfig['tsite_url'] . "assets/libraries/webview/iyzico/add_change_card.php?" . $request_params;
            }
            header('Location: ' . $getWayUrl);
            break;

        default:
            echo "Payment type is not available or not configured. Please check with configurations.";
            break;
    }
    exit;
}

?>