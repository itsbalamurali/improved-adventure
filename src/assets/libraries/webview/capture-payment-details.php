<?php
if ($UserType == "Rider" || $UserType == "Passenger") {
    $UserType = "Rider";
    $eUserType = "Passenger";
    $where = " iUserId = '$iUserId'";
    $tblname = "register_user as ru";
} else {
    $where = " iDriverId = '$iUserId'";
    $tblname = "register_driver as ru";
}
$genUserData = $obj->MySQLSelect("SELECT ru.tSessionId,ru.vLang,co.vPaymentGateway FROM $tblname LEFT JOIN country as co ON ru.vCountry = co.vCountryCode WHERE $where");
$tSessionId = $genUserData[0]['tSessionId'];
$vLang = $genUserData[0]['vLang'];
$USER_APP_PAYMENT_METHOD = $APP_PAYMENT_METHOD;
if ($genUserData[0]['vPaymentGateway'] != "") {
    $USER_APP_PAYMENT_METHOD = $genUserData[0]['vPaymentGateway'];
}
$allCurrentSystemPaymentDetails = $CONFIG_OBJ->getCurrentSystemPaymentDetails();
foreach ($allCurrentSystemPaymentDetails as $zkey => $zValue) {
    if (startsWith(strtoupper($zkey), strtoupper($USER_APP_PAYMENT_METHOD))) {
        $payment_arr[$zkey] = $zValue;
    }
}
$payment_arr['SYSTEM_PAYMENT_ENVIRONMENT'] = $SYSTEM_PAYMENT_ENVIRONMENT;
$payment_arr['APP_PAYMENT_MODE'] = $APP_PAYMENT_MODE;
$payment_arr['APP_PAYMENT_METHOD'] = $USER_APP_PAYMENT_METHOD;
$payment_arr['COMMISION_DEDUCT_ENABLE'] = $COMMISION_DEDUCT_ENABLE;
$payment_arr['WALLET_MIN_BALANCE'] = $WALLET_MIN_BALANCE;
$payment_arr['PAYMENT_ENABLED'] = $PAYMENT_ENABLED;
$tPaymentDetails = json_encode($payment_arr, JSON_UNESCAPED_UNICODE);
$user_payment_info_insert['iMemberId'] = $iUserId;
$user_payment_info_insert['vPaymentMethod'] = $USER_APP_PAYMENT_METHOD;
$user_payment_info_insert['tPaymentDetails'] = $tPaymentDetails;
$user_payment_info_insert['ePaymentEnv'] = $SYSTEM_PAYMENT_ENVIRONMENT;
$iPaymentInfoId = "";
if (count($sqlData) == 0 && !empty($user_payment_info_insert['tCardToken'])) {
    $iPaymentInfoId = $obj->MySQLQueryPerform("user_payment_info", $user_payment_info_insert, 'insert');
}
if ($page_type != "ADD_CARD" && $page_type != "PAYMENT_LIST") {
    if ($page_type == "WALLET_MONEY_ADD") {
        $UserType = ($UserType == "Passenger" || $UserType == "Rider") ? "Rider" : "Driver";
        // $AMOUNT = $AMOUNT / $currencyData[0]['Ratio'];
        $eFor = 'Deposit';
        $eType = 'Credit';
        $tDescription = '#LBL_AMOUNT_CREDIT_BY_USER#';
        $ePaymentStatus = 'Unsettelled';
        $dDate = Date('Y-m-d H:i:s');
        $WalletId = $WALLET_OBJ->PerformWalletTransaction($iUserId, $UserType, $AMOUNT, 'Credit', 0, $eFor, $tDescription, $ePaymentStatus, $dDate);
        if ($payment_id == "") {
            $allCurrentSystemPaymentDetails = $CONFIG_OBJ->getCurrentSystemPaymentDetails();
            $_REQUEST['tPaymentUserID'] = $tPaymentTransactionId;
            $payment_arr = array();
            foreach ($allCurrentSystemPaymentDetails as $zkey => $zValue) {
                if (startsWith(strtoupper($zkey), strtoupper($USER_APP_PAYMENT_METHOD))) {
                    $payment_arr[$zkey] = $zValue;
                }
            }
            $payment_arr['SYSTEM_PAYMENT_ENVIRONMENT'] = $SYSTEM_PAYMENT_ENVIRONMENT;
            $payment_arr['APP_PAYMENT_MODE'] = $APP_PAYMENT_MODE;
            $payment_arr['APP_PAYMENT_METHOD'] = $USER_APP_PAYMENT_METHOD;
            $payment_arr['COMMISION_DEDUCT_ENABLE'] = $COMMISION_DEDUCT_ENABLE;
            $payment_arr['WALLET_MIN_BALANCE'] = $WALLET_MIN_BALANCE;
            $payment_arr['PAYMENT_ENABLED'] = $PAYMENT_ENABLED;
            $payment_arr['CARD_TOKEN'] = $cardToken;
            $tPaymentDetails = json_encode($payment_arr, JSON_UNESCAPED_UNICODE);
            /* Added by HV on 21-02-2020 Common for all Payment Methods End */
            $UserType = ($UserType == "Passenger" || $UserType == "Rider") ? "Passenger" : "Driver";
            $pay_data['tPaymentUserID'] = $_REQUEST['tPaymentUserID'];
            $pay_data['vPaymentUserStatus'] = "approved";
            $pay_data['iAmountUser'] = $AMOUNT;
            $pay_data['tPaymentDetails'] = $tPaymentDetails;
            $pay_data['iOrderId'] = $iOrderId;
            $pay_data['vPaymentMethod'] = $USER_APP_PAYMENT_METHOD;
            $pay_data['iUserId'] = $iUserId;
            $pay_data['eUserType'] = $UserType;
            $pay_data['iUserWalletId'] = $WalletId;
            $pay_data['eEvent'] = "Wallet";
            $id = $obj->MySQLQueryPerform("payments", $pay_data, 'insert');
        } else {
            $where_payments = " iPaymentId = '" . $payment_id . "'";
            $data_payments['iUserWalletId'] = $WalletId;
            $data_payments['eEvent'] = "Wallet";
            $obj->MySQLQueryPerform("payments", $data_payments, 'update', $where_payments);
            $id = $payment_id;
        }
        $payment_data = $obj->MySQLSelect("SELECT tPaymentUserID FROM payments WHERE iPaymentId = $id");
        $transaction_id = $payment_data[0]['tPaymentUserID'];
        if (!empty($APP_RETURN_URL)) {
            header('Location:' . urldecode($APP_RETURN_URL));
            exit;
        }
        $returnUrl = $tconfig['tsite_url'] . "assets/libraries/webview/success.php";
        header('Location:' . $returnUrl . "?message=LBL_WALLET_MONEY_CREDITED&SYSTEM_TYPE=" . $SYSTEM_TYPE . "&vLang=" . $vLang . "&TIME=" . time() . "&PAGE_TYPE=WALLET_MONEY_ADD&iTransactionId=" . $transaction_id);
        exit;
    } elseif ($page_type == "CHARGE_OUTSTANDING_AMT") {
        include_once($tconfig['tpanel_path'] . 'assets/libraries/webview/payment_general_functions.php');
        $iOrganizationId = 0;
        $ePaymentBy = "Passenger";
        /*if($MODULES_OBJ->isOrganizationModuleEnable()) {
            $profileDetails = GetCorporateProfileDetails($iUserId);
            $iOrganizationId = isset($profileDetails['iOrganizationId']) ? $profileDetails['iOrganizationId'] : 0;
            $ePaymentBy = isset($profileDetails['ePaymentBy']) ? $profileDetails['ePaymentBy'] : "Passenger";
        }*/
        $obj->sql_query("UPDATE register_user set fTripsOutStandingAmount = '0' WHERE iUserId = '" . $iUserId . "'");
        $obj->sql_query("UPDATE trip_outstanding_amount set ePaidByPassenger = 'Yes' WHERE iUserId = '" . $iUserId . "' AND iOrganizationId = '" . $iOrganizationId . "' AND ePaymentBy = '" . $ePaymentBy . "'");
        if ($payment_id == "") {
            $allCurrentSystemPaymentDetails = $CONFIG_OBJ->getCurrentSystemPaymentDetails();
            $payment_arr = array();
            foreach ($allCurrentSystemPaymentDetails as $zkey => $zValue) {
                if (startsWith(strtoupper($zkey), strtoupper($USER_APP_PAYMENT_METHOD))) {
                    $payment_arr[$zkey] = $zValue;
                }
            }
            $payment_arr['SYSTEM_PAYMENT_ENVIRONMENT'] = $SYSTEM_PAYMENT_ENVIRONMENT;
            $payment_arr['APP_PAYMENT_MODE'] = $APP_PAYMENT_MODE;
            $payment_arr['APP_PAYMENT_METHOD'] = $USER_APP_PAYMENT_METHOD;
            $payment_arr['COMMISION_DEDUCT_ENABLE'] = $COMMISION_DEDUCT_ENABLE;
            $payment_arr['WALLET_MIN_BALANCE'] = $WALLET_MIN_BALANCE;
            $payment_arr['PAYMENT_ENABLED'] = $PAYMENT_ENABLED;
            $payment_arr['CARD_TOKEN'] = $cardToken;
            $tPaymentDetails = json_encode($payment_arr, JSON_UNESCAPED_UNICODE);
            /* Added by HV on 21-02-2020 Common for all Payment Methods End */
            // $pay_data['tPaymentUserID'] = $iTransactionId;
            $pay_data['tPaymentUserID'] = $_REQUEST['tPaymentUserID'];
            $pay_data['vPaymentUserStatus'] = "approved";
            $pay_data['iAmountUser'] = $fNetTotal;
            $pay_data['tPaymentDetails'] = $tPaymentDetails;
            $pay_data['vPaymentMethod'] = $USER_APP_PAYMENT_METHOD;
            $pay_data['iUserId'] = $iUserId;
            $pay_data['eUserType'] = $UserType;
            $pay_data['eEvent'] = "OutStanding";
            $id = $obj->MySQLQueryPerform("payments", $pay_data, 'insert');
        } else {
            $where_payments = " iPaymentId = '" . $payment_id . "'";
            $data_payments['eEvent'] = "OutStanding";
            $obj->MySQLQueryPerform("payments", $data_payments, 'update', $where_payments);
            $id = $payment_id;
        }
        $payment_data = $obj->MySQLSelect("SELECT tPaymentUserID FROM payments WHERE iPaymentId = $id");
        $transaction_id = $payment_data[0]['tPaymentUserID'];
        $returnUrl = $tconfig['tsite_url'] . "assets/libraries/webview/success.php";
        header('Location:' . $returnUrl . "?message=LBL_PAYMENT_SUCCESS_TXT&SYSTEM_TYPE=" . $SYSTEM_TYPE . "&vLang=" . $vLang . "&TIME=" . time() . "&PAGE_TYPE=CHARGE_OUTSTANDING_AMT&iTransactionId=" . $transaction_id);
        exit;
    } elseif ($page_type == "SUBSCRIBE_PLAN") {
        $isWallet = $_REQUEST['isWallet'];
        $isCard = $_REQUEST['isCard'];
        $iDriverSubscriptionPlanId = $_REQUEST['iDriverSubscriptionPlanId'];
        $isUpgrade = $_REQUEST['isUpgrade'];
        $extraParameters = "?iDriverId=" . $iUserId . "&isWallet=" . $isWallet . "&type=SubscribePlan&tSessionId=" . $tSessionId . "&GeneralMemberId=" . $GeneralMemberId . "&GeneralUserType=" . $GeneralUserType . "&isCard=" . $isCard . "&iDriverSubscriptionPlanId=" . $iDriverSubscriptionPlanId . "&isUpgrade=" . $isUpgrade . "&payment_id=" . $payment_id;
        $redirectUrl = $tconfig['tsite_url'] . $returnUrl . $extraParameters . "&payStatus=succeeded&SYSTEM_TYPE=" . $SYSTEM_TYPE. "&vLang=" . $vLang ;
        header('Location: ' . $redirectUrl);
        exit();
    } elseif ($page_type == "GIFT_CARD_PAYMENT") {
        if ($_REQUEST['eWalletDebit'] == 'Yes') {
            $UserType = ($_REQUEST['GeneralUserType'] == "Passenger" || $_REQUEST['GeneralUserType'] == "Rider") ? "Rider" : "Driver";
            $data_wallet['iUserId'] = $_REQUEST['GeneralMemberId'];
            $data_wallet['eUserType'] = $UserType;
            $data_wallet['iBalance'] = $_REQUEST['eWalletDebitAmount'];
            $data_wallet['eType'] = "Debit";
            $data_wallet['dDate'] = date("Y-m-d H:i:s");
            $data_wallet['iTripId'] = 0;
            $data_wallet['eFor'] = "Gift Card";
            $data_wallet['ePaymentStatus'] = "Settelled";
            $data_wallet['tDescription'] = "#LBL_GIFT_CARD_DEBITED#";

            $data = $WALLET_OBJ->PerformWalletTransaction($data_wallet['iUserId'], $data_wallet['eUserType'], $data_wallet['iBalance'], $data_wallet['eType'], $data_wallet['iTripId'], $data_wallet['eFor'], $data_wallet['tDescription'], $data_wallet['ePaymentStatus'], $data_wallet['dDate']);
        }

        $returnUrl = base64_decode($returnUrl);
        $extraParameters = "&payment_id=" . $payment_id . '&payStatus=succeeded&tSessionId=' . $genUserData[0]['tSessionId'] . "&payStatus=succeeded&SYSTEM_TYPE=" . $SYSTEM_TYPE;
        $RIDIRECT_URL = $tconfig['tsite_url'] . 'webservice_shark.php?' . $returnUrl . $extraParameters;
        header('Location: ' . $RIDIRECT_URL);
        exit();
    } elseif ($page_type == "RIDE_TIP_COLLECT") {
        if ($payment_id == "") {
            $allCurrentSystemPaymentDetails = $CONFIG_OBJ->getCurrentSystemPaymentDetails();
            $payment_arr = array();
            foreach ($allCurrentSystemPaymentDetails as $zkey => $zValue) {
                if (startsWith(strtoupper($zkey), strtoupper($USER_APP_PAYMENT_METHOD))) {
                    $payment_arr[$zkey] = $zValue;
                }
            }
            $payment_arr['SYSTEM_PAYMENT_ENVIRONMENT'] = $SYSTEM_PAYMENT_ENVIRONMENT;
            $payment_arr['APP_PAYMENT_MODE'] = $APP_PAYMENT_MODE;
            $payment_arr['APP_PAYMENT_METHOD'] = $USER_APP_PAYMENT_METHOD;
            $payment_arr['COMMISION_DEDUCT_ENABLE'] = $COMMISION_DEDUCT_ENABLE;
            $payment_arr['WALLET_MIN_BALANCE'] = $WALLET_MIN_BALANCE;
            $payment_arr['PAYMENT_ENABLED'] = $PAYMENT_ENABLED;
            $payment_arr['CARD_TOKEN'] = $cardToken;
            $tPaymentDetails = json_encode($payment_arr, JSON_UNESCAPED_UNICODE);
            /* Added by HV on 21-02-2020 Common for all Payment Methods End */
            // $pay_data['tPaymentUserID'] = $iTransactionId;
            $pay_data['tPaymentUserID'] = $_REQUEST['tPaymentUserID'];
            $pay_data['vPaymentUserStatus'] = "approved";
            $pay_data['iAmountUser'] = $fNetTotal;
            $pay_data['tPaymentDetails'] = $tPaymentDetails;
            $pay_data['vPaymentMethod'] = $USER_APP_PAYMENT_METHOD;
            $pay_data['iUserId'] = $iUserId;
            $pay_data['eUserType'] = $UserType;
            $pay_data['eEvent'] = "TripTip";
            $id = $obj->MySQLQueryPerform("payments", $pay_data, 'insert');
        } else {
            $where_payments = " iPaymentId = '" . $payment_id . "'";
            $data_payments['eEvent'] = "TripTip";
            $obj->MySQLQueryPerform("payments", $data_payments, 'update', $where_payments);
            $id = $payment_id;
        }
        $payment_data = $obj->MySQLSelect("SELECT tPaymentUserID FROM payments WHERE iPaymentId = $id");
        $transaction_id = $payment_data[0]['tPaymentUserID'];
        $debit_amount = isset($_REQUEST['DEBIT_AMOUNT']) ? $_REQUEST['DEBIT_AMOUNT'] : 0;
        $where_trip = " iTripId = '$iTripId'";
        $data_trip['fTipPrice'] = $_REQUEST['DEBIT_AMOUNT'] + $_REQUEST['AMOUNT'];
        $obj->MySQLQueryPerform("trips", $data_trip, 'update', $where_trip);
        $tripsData = $obj->MySQLSelect("SELECT vRideNo FROM trips WHERE iTripId = " . $_REQUEST['iTripId']);
        if ($_REQUEST['DEBIT_AMOUNT'] > 0) {
            $data_wallet['iUserId'] = $iUserId;
            $data_wallet['eUserType'] = $UserType;
            $data_wallet['iBalance'] = $_REQUEST['DEBIT_AMOUNT'];
            $data_wallet['eType'] = "Debit";
            $data_wallet['dDate'] = date("Y-m-d H:i:s");
            $data_wallet['iTripId'] = $_REQUEST['iTripId'];
            $data_wallet['eFor'] = "Booking";
            $data_wallet['ePaymentStatus'] = "Unsettelled";
            $data_wallet['tDescription'] = "#LBL_DEBITED_BOOKING#" . $tripsData[0]['vRideNo'];
            $WALLET_OBJ->PerformWalletTransaction($data_wallet['iUserId'], $data_wallet['eUserType'], $data_wallet['iBalance'], $data_wallet['eType'], $data_wallet['iTripId'], $data_wallet['eFor'], $data_wallet['tDescription'], $data_wallet['ePaymentStatus'], $data_wallet['dDate']);
        }
        if ($MODULES_OBJ->isAutoCreditToDriverModuleAvailable()) {
            include_once($tconfig['tpanel_path'] . 'include/features/include_auto_credit_driver.php');
            $DataTip['price'] = $data_trip['fTipPrice'];
            $DataTip['iTripId'] = $iTripId;
            autoCreditDriverEarning($DataTip, "TripCollectTip");
        }
        $returnUrl = $tconfig['tsite_url'] . "assets/libraries/webview/success.php";
        header('Location:' . $returnUrl . "?message=LBL_PAYMENT_SUCCESS_TXT&SYSTEM_TYPE=" . $SYSTEM_TYPE . "&vLang=" . $vLang  . "&TIME=" . time() . "&PAGE_TYPE=RIDE_TIP_COLLECT&iTransactionId=" . $transaction_id);
        exit;
    } elseif ($page_type == "ORDER_TIP_COLLECT") {
        $iOrderId = $_REQUEST['iOrderId'];
        $where_payments = " iPaymentId = '" . $payment_id . "'";
        $data_payments['eEvent'] = "OrderTip";
        $data_payments['iOrderId'] = $iOrderId;
        $obj->MySQLQueryPerform("payments", $data_payments, 'update', $where_payments);
        $payment_data = $obj->MySQLSelect("SELECT tPaymentUserID FROM payments WHERE iPaymentId = $payment_id");
        $transaction_id = $payment_data[0]['tPaymentUserID'];
        $debit_amount = isset($_REQUEST['DEBIT_AMOUNT']) ? $_REQUEST['DEBIT_AMOUNT'] : 0;
        $orderData = $obj->MySQLSelect("SELECT vOrderNo,fNetTotal,fTotalGenerateFare FROM orders WHERE iOrderId = " . $iOrderId);
        $vOrderNo = $orderData[0]['vOrderNo'];
        $orderTotal = $orderData[0]['fNetTotal'];
        $orderGenerateFareTotal = $orderData[0]['fTotalGenerateFare'];
        $whereOrder = " iOrderId = '$iOrderId'";
        $dataOrder['fTipAmount'] = $_REQUEST['DEBIT_AMOUNT'] + $_REQUEST['AMOUNT'];
        $dataOrder['fNetTotal'] = ($_REQUEST['DEBIT_AMOUNT'] + $_REQUEST['AMOUNT']) + $orderTotal;
        $dataOrder['fTotalGenerateFare'] = ($_REQUEST['DEBIT_AMOUNT'] + $_REQUEST['AMOUNT']) + $orderGenerateFareTotal;
        $dataOrder['eTipIncludedAtOrderRequest'] = 'No';
        $obj->MySQLQueryPerform("orders", $dataOrder, 'update', $whereOrder);
        if ($_REQUEST['DEBIT_AMOUNT'] > 0) {
            $data_wallet['iUserId'] = $iUserId;
            $data_wallet['eUserType'] = $UserType;
            $data_wallet['iBalance'] = $_REQUEST['DEBIT_AMOUNT'];
            $data_wallet['eType'] = "Debit";
            $data_wallet['dDate'] = date("Y-m-d H:i:s");
            $data_wallet['iTripId'] = 0;
            $data_wallet['iOrderId'] = $_REQUEST['iOrderId'];
            $data_wallet['eFor'] = "Booking";
            $data_wallet['ePaymentStatus'] = "Unsettelled";
            $data_wallet['tDescription'] = '#LBL_DEBITED_TIP_AMOUNT_TXT#' . " - " . $vOrderNo;
            $WALLET_OBJ->PerformWalletTransaction($data_wallet['iUserId'], $data_wallet['eUserType'], $data_wallet['iBalance'], $data_wallet['eType'], $data_wallet['iTripId'], $data_wallet['eFor'], $data_wallet['tDescription'], $data_wallet['ePaymentStatus'], $data_wallet['dDate'], $data_wallet['iOrderId']);
        }
        $returnUrl = $tconfig['tsite_url'] . "assets/libraries/webview/success.php";
        header('Location:' . $returnUrl . "?SYSTEM_TYPE=" . $SYSTEM_TYPE . "&vLang=" . $vLang . "&TIME=" . time() . "&iOrderId=" . $_REQUEST['iOrderId'] . "&PAGE_TYPE=ORDER_TIP_COLLECT&iTransactionId=" . $transaction_id);
        exit;
    } elseif ($page_type == "CHARGE_CARD_GENIE") {
        $extraParameters = "iUserId=" . $iUserId . "&order=" . $fromOrder . "&iOrderId=" . $iOrderId . "&amount=" . $AMOUNT . "&ccode=" . $ccode . "&currencyCode=" . $currency . "&ePaymentOption=" . $ePaymentOption . "&type=CollectPaymentBuyAnything&tSessionId=" . $tSessionId . "&GeneralMemberId=" . $GeneralMemberId . "&GeneralUserType=" . $GeneralUserType . "&vPayMethod=" . $vPayMethod . "&iServiceId=" . $iServiceId . "&tPaymentUserID=" . $tPaymentTransactionId . "&cardToken=" . $cardToken . "&tPaymentId=" . $payment_id . "&eSystem=DeliverAll";
        if (isset($extraParams)) {
            $extraParams .= "&tPaymentUserID=" . $tPaymentTransactionId . "&cardToken=" . $cardToken . "&tPaymentId=" . $payment_id;
            $extraParameters = "";
        }
        $redirectUrl = $tconfig['tsite_url'] . 'webservice_shark.php?' . $extraParameters . $extraParams . "&payStatus=succeeded&SYSTEM_TYPE=" . $SYSTEM_TYPE;
        // echo $redirectUrl; exit();
        header('Location: ' . $redirectUrl);
        exit;
    } elseif ($page_type == "CHARGE_RENTITEM_POST_AMT") {
        $iTmpRentItemPostId = $_REQUEST['iTmpRentItemPostId'];
        $iPaymentPlanId = $_REQUEST['iPaymentPlanId'];
        $vLanguage = $_REQUEST['vLanguage'];
        if (!empty($payment_id)) {
            $where_payments = " iPaymentId = '" . $payment_id . "'";
            $data_payments['eEvent'] = "RentItem";
            $data_payments['iTmpRentItemPostId'] = $iTmpRentItemPostId;

            $obj->MySQLQueryPerform("payments", $data_payments, 'update', $where_payments);
            $id = $payment_id;
        }
        $extraParameters = "iMemberId=" . $iUserId . "&iTmpRentItemPostId=" . $iTmpRentItemPostId . "&amount=" . $AMOUNT . "&ccode=" . $ccode . "&currencyCode=" . $currency . "&ePaymentOption=" . $ePaymentOption . "&type=GenerateFinalPost&tSessionId=" . $tSessionId . "&GeneralMemberId=" . $GeneralMemberId . "&GeneralUserType=" . $GeneralUserType . "&vPayMethod=" . $vPayMethod . "&iServiceId=" . $iServiceId . "&tPaymentUserID=" . $tPaymentTransactionId . "&cardToken=" . $cardToken . "&tPaymentId=" . $payment_id . "&iPaymentPlanId=" . $iPaymentPlanId . "&vLanguage=" . $vLanguage;

        if (isset($extraParams)) {
            $extraParams .= "&tPaymentUserID=" . $tPaymentTransactionId . "&cardToken=" . $cardToken . "&tPaymentId=" . $payment_id;
            $extraParameters = "";
        }
        $redirectUrl = $tconfig['tsite_url'] . 'webservice_shark.php?' . $extraParameters . $extraParams . "&payStatus=succeeded&SYSTEM_TYPE=" . $SYSTEM_TYPE;
        // echo $redirectUrl; exit();
        header('Location: ' . $redirectUrl);
        exit;
    } elseif ($page_type == "AUTHORIZE_TRIP_AMOUNT") {
        $payment_data = $obj->MySQLSelect("SELECT tPaymentUserID FROM payments WHERE iPaymentId = '$payment_id' ");
        $transaction_id = $payment_data[0]['tPaymentUserID'];

        $returnUrl = $tconfig['tsite_url'] . "assets/libraries/webview/success.php";
        header('Location:' . $returnUrl . "?success=1&SYSTEM_TYPE=" . $SYSTEM_TYPE . "&vLang=" . $vLang . "&TIME=" . time() . "&PAGE_TYPE=AUTHORIZE_TRIP_AMOUNT&iAuthorizePaymentId=" . $transaction_id);
        exit;
    }
    $extraParameters = "iUserId=" . $iUserId . "&order=" . $fromOrder . "&iOrderId=" . $iOrderId . "&amount=" . $AMOUNT . "&ccode=" . $ccode . "&currencyCode=" . $currency . "&ePaymentOption=" . $ePaymentOption . "&type=CaptureCardPaymentOrder&tSessionId=" . $tSessionId . "&GeneralMemberId=" . $GeneralMemberId . "&GeneralUserType=" . $GeneralUserType . "&vPayMethod=" . $vPayMethod . "&iServiceId=" . $iServiceId . "&tPaymentUserID=" . $tPaymentTransactionId . "&cardToken=" . $cardToken . "&tPaymentId=" . $payment_id . "&eSystem=DeliverAll";
    if (isset($extraParams)) {
        $extraParams .= "&tPaymentUserID=" . $tPaymentTransactionId . "&cardToken=" . $cardToken . "&tPaymentId=" . $payment_id;
        $extraParameters = "";
    }
    $redirectUrl = $tconfig['tsite_url'] . 'webservice_shark.php?' . $extraParameters . $extraParams . "&payStatus=succeeded&SYSTEM_TYPE=" . $SYSTEM_TYPE;
    // echo $redirectUrl; exit();
    header('Location: ' . $redirectUrl);
    exit;
} else {
    $allPaymentInfoSql = "SELECT * FROM user_payment_info WHERE iMemberId = " . $iUserId . " AND eUserType = '" . $dbUserType . "' AND vPaymentMethod = '" . $USER_APP_PAYMENT_METHOD . "' AND eStatus = 'Active' AND ePaymentEnv = '" . $SYSTEM_PAYMENT_ENVIRONMENT . "'";
    $allPaymentInfoData = $obj->MySQLSelect($allPaymentInfoSql);
    if (count($allPaymentInfoData) == 1) {
        $success = "&success=1";
    } else {
        $success = "";
    }
    if ($isReturnResult == "No") {
        $success = "";
    }
    $returnUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $returnUrl = str_replace('&success=1', '', $returnUrl);
    $returnUrl = str_replace('&capture=true', '', $returnUrl);
    $returnUrl = preg_replace('/&transaction_id=\d+/', '', $returnUrl);
    $returnUrl = preg_replace('/response=.*/', '', $returnUrl);
    if (isset($tRequestData)) {
        $returnUrl .= http_build_query($tRequestData);
    }
    $returnUrl = str_replace('ADD_CARD', 'PAYMENT_LIST', $returnUrl);
    $returnUrl .= $success;
    $_SESSION['success_msg'] = $languageLabelsArr['LBL_INFO_UPDATED_TXT'];
    // echo "<pre>"; print_r($returnUrl); exit;
    if (isset($currentUrl) && $currentUrl != "") {
        $currentUrl = str_replace('ADD_CARD', 'PAYMENT_LIST', $currentUrl);
        $currentUrl = str_replace('&success=1', '', $currentUrl);
        $currentUrl .= $success;
        header('Location:' . $currentUrl);
        exit;
    } else {
        header('Location:' . $returnUrl);
        exit;
    }
}
?>