<?php
header("Expires: on, 01 Jan 1970 00:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
include_once('../../../common.php');

if($THEME_OBJ->isProThemeActive() == "Yes") {
    include_once($tconfig['tpanel_path'] . 'assets/libraries/webview/payment_mode_select_v1.php');
    exit;
}

include_once($tconfig['tpanel_path'] . 'assets/libraries/webview/payment_general_functions.php');
$GeneralMemberId = isset($_REQUEST['GeneralMemberId']) ? $_REQUEST['GeneralMemberId'] : "";
$GeneralUserType = isset($_REQUEST['GeneralUserType']) ? $_REQUEST['GeneralUserType'] : "";
$eType = isset($_REQUEST['eType']) ? $_REQUEST['eType'] : "";
$ePaymentType = isset($_REQUEST['ePaymentType']) ? $_REQUEST['ePaymentType'] : "";
$isPayBySender = isset($_REQUEST['isPayBySender']) ? $_REQUEST['isPayBySender'] : "No";
$iPaymentInfoId = isset($_REQUEST['iPaymentInfoId']) ? $_REQUEST['iPaymentInfoId'] : "";
$isCardSelected = isset($_REQUEST['CARD_SELECTED']) ? $_REQUEST['CARD_SELECTED'] : "No";
$eForVideoConsultation = isset($_REQUEST['eForVideoConsultation']) ? $_REQUEST['eForVideoConsultation'] : "No";
$isContactlessDelivery = isset($_REQUEST['isContactlessDelivery']) ? $_REQUEST['isContactlessDelivery'] : "No";
$eTakeAway = isset($_REQUEST['eTakeAway']) ? $_REQUEST['eTakeAway'] : "No";
$iDriverId = isset($_REQUEST['iDriverId']) ? $_REQUEST['iDriverId'] : "";
$SYSTEM_DEFAULT_AMOUNT = isset($_REQUEST['SYSTEM_DEFAULT_AMOUNT']) ? $_REQUEST['SYSTEM_DEFAULT_AMOUNT'] : "";
$returnUrl = isset($_REQUEST['returnUrl']) ? $_REQUEST['returnUrl'] : "";


if (isset($_REQUEST['success']) && $_REQUEST['success'] == 1 && isset($_REQUEST['paymentModeSelected']) && $_REQUEST['paymentModeSelected'] == "Yes") {
    exit;
}
if ((isset($_REQUEST['OPEN_IN_APP_WALLET']) && strtoupper($_REQUEST['OPEN_IN_APP_WALLET']) == "YES") || (isset($_REQUEST['BUSINESS_PROFILE_SETUP']) && strtoupper($_REQUEST['BUSINESS_PROFILE_SETUP']) == "YES")) {
    exit;
}
$failure_url = $tconfig['tsite_url'] . "assets/libraries/webview/failure.php?";
if (empty($GeneralMemberId) || empty($GeneralUserType)) {
    header('Location: ' . $failure_url . 'success=0&page_action=close&message=' . $langage_lbl['LBL_ERROR_OCCURED']);
    exit;
}
if (strtolower($GeneralUserType) == "driver") {
    $tbl_name = "register_driver";
    $iMemberId = "iDriverId";
    $eMemberType = "Driver";
    $eMemberTypeAlt = "Driver";
    $eMemberCurrency = "vCurrencyDriver";
} else {
    $tbl_name = "register_user";
    $iMemberId = "iUserId";
    $eMemberType = "Passenger";
    $eMemberTypeAlt = "Rider";
    $eMemberCurrency = "vCurrencyPassenger";
}
$userData = $obj->MySQLSelect("SELECT * FROM $tbl_name WHERE $iMemberId = '$GeneralMemberId'");
$vLang = $userData[0]['vLang'];
if ($vLang == "" || $vLang == NULL) {
    $vLang = $LANG_OBJ->FetchDefaultLangData("vCode");
}
$langage_lbl = $LANG_OBJ->FetchLanguageLabels($vLang, "1", $iServiceId);
$payment_mode_user_data = $obj->MySQLSelect("SELECT * FROM payment_mode_user_log WHERE iMemberId = '$GeneralMemberId' AND eUserType = '$eMemberType' ORDER BY dDateTime DESC LIMIT 1");
$PaymentMode = $BusinessReasonId = $BusinessReasonTitle = $BusinessReasonOther = $PromoCode = "";
$Profile = "personal";
$isPaymentModeDataAvailable = "No";
$OrganizationId = "";
if (!empty($payment_mode_user_data) && count($payment_mode_user_data) > 0) {
    if (!empty($payment_mode_user_data[0]['tRequestData'])) {
        $tRequestData = json_decode($payment_mode_user_data[0]['tRequestData'], true);
        // echo "<pre>"; print_r($tRequestData); exit;
        $Profile = $tRequestData['Profile'];
        $PaymentMode = $tRequestData['PaymentMode'];
        $OrganizationId = $tRequestData['OrganizationId'];
        $OrganizationTitle = $tRequestData['OrganizationTitle'];
        $BusinessReasonId = $tRequestData['BusinessReasonId'];
        $BusinessReasonTitle = $tRequestData['BusinessReasonTitle'];
        $BusinessReasonOther = $tRequestData['BusinessReasonOther'];
        $isPaymentModeDataAvailable = "Yes";
        $iPaymentInfoId = !empty($iPaymentInfoId) ? $iPaymentInfoId : $tRequestData['iPaymentInfoId'];
    }
    $PromoCode = $payment_mode_user_data[0]['vPromocode'];
}
if ($isCardSelected == "Yes") {
    $PaymentMode = "card";
    $isPaymentModeDataAvailable = "Yes";
}
if (isset($_REQUEST['form_return'])) {
    $Profile = isset($_REQUEST['profile']) ? $_REQUEST['profile'] : $Profile;
    $PaymentMode = isset($_REQUEST['payment_mode']) ? $_REQUEST['payment_mode'] : $PaymentMode;
    $OrganizationId = isset($_REQUEST['org_id']) ? $_REQUEST['org_id'] : $OrganizationId;
    $OrganizationTitle = isset($_REQUEST['org_title']) ? $_REQUEST['org_title'] : $OrganizationTitle;
    $BusinessReasonId = isset($_REQUEST['business_reason_id']) ? $_REQUEST['business_reason_id'] : $BusinessReasonId;
    $BusinessReasonTitle = isset($_REQUEST['business_reason_title']) ? $_REQUEST['business_reason_title'] : $BusinessReasonTitle;
    $BusinessReasonOther = isset($_REQUEST['business_reason_other']) ? $_REQUEST['business_reason_other'] : $BusinessReasonOther;
    if ($PaymentMode == "card") {
        $isPaymentModeDataAvailable = "Yes";
    }
    $PromoCode = isset($_REQUEST['promocode_val']) ? $_REQUEST['promocode_val'] : $PromoCode;
}
if (!empty($ePaymentType) && ($ePaymentType == "ChargeOutstandingAmount" || $ePaymentType == "GiftCardPayment")) {
    $CASH_AVAILABLE = "No";
    $eType = "";
}
if (!empty($ePaymentType) && $ePaymentType == "ChargeRentItemPostAmount") {
    $CASH_AVAILABLE = "No";
    $eType = "";
}
$mb4 = $eType == "Ride" ? "mb-4" : "";
$selected_payment_mode = "";
$cash_checked = $card_checked = $wallet_checked = $cash_active = $card_active = $wallet_active = "";
if (strtoupper($CASH_AVAILABLE) == "YES" && $PaymentMode == "cash" && strtoupper($isPaymentModeDataAvailable) == "YES") {
    $cash_checked = "checked";
    $cash_active = "active";
    $selected_payment_mode = "cash";
} elseif (strtoupper($CARD_AVAILABLE) == "YES" && $PaymentMode == "card" && strtoupper($isPaymentModeDataAvailable) == "YES") {
    $card_checked = "checked";
    $card_active = "active";
    $selected_payment_mode = "card";
} elseif (strtoupper($WALLET_AVAILABLE) == "YES" && $PaymentMode == "wallet" && strtoupper($isPaymentModeDataAvailable) == "YES") {
    $wallet_checked = "checked";
    $wallet_active = "active";
    $selected_payment_mode = "wallet";
}
if (!empty($eType) && $eType == "Multi-Delivery" && $isPayBySender == "No") {
    $CARD_AVAILABLE = $WALLET_AVAILABLE = $WALLET_ENABLE = "No";
    $cash_checked = "checked";
    $cash_active = "active";
    $selected_payment_mode = "cash";
}
$personal_checked = "checked";
$personal_active = "active";
$personal_display = $eType == "Ride" ? "" : "d-none-alt";
$business_checked = "";
$business_active = "";
if ($Profile == "business" && $eType == "Ride" && ($ePaymentType != "ChargeOutstandingAmount" || $ePaymentType != "GiftCardPayment")) {
    $personal_checked = "";
    $personal_active = "";
    $business_checked = "checked";
    $business_active = "active";
}
$pay_by_organization = "No";
$isOrgAvailable = "No";
$tripReasonArr = array();
$iOrganizationId = 0;
$ePaymentBy = "Passenger";
if (strtolower($GeneralUserType) != "driver" && $MODULES_OBJ->isOrganizationModuleEnable() && $eType == "Ride") {
    $profileDetails = GetCorporateProfileDetails($GeneralMemberId, $vLang, $eType);
    if ($profileDetails['isOrgAvailable'] == "Yes") {
        $org_reasons_arr = array();
        foreach ($profileDetails['ORG_DATA'] as $org_reasons) {
            $org_reasons_arr['org_' . $org_reasons['iOrganizationId']] = !empty($org_reasons['tripReasonArr']) ? $org_reasons['tripReasonArr'] : "";
            if (!empty($OrganizationId) && $OrganizationId == $org_reasons['iOrganizationId']) {
                $pay_by_organization = strtolower($org_reasons['ePaymentBy']) == "organization" ? "Yes" : "No";
            }
        }
    }
    // echo "<pre>"; print_r($profileDetails); exit;
    $isOrgAvailable = isset($profileDetails['isOrgAvailable']) ? $profileDetails['isOrgAvailable'] : "No";
}



$user_available_balance = $WALLET_OBJ->FetchMemberWalletBalanceApp($GeneralMemberId, $eMemberTypeAlt);
$user_available_balance_value = $WALLET_OBJ->FetchMemberWalletBalance($GeneralMemberId, "Rider", true);
/*if ($eType == 'RideShare') {
    $ePayWallet = "Yes";
    $user_available_balance_wallet = $WALLET_OBJ->FetchMemberWalletBalance($GeneralMemberId, "Rider", true);
    $walletDataArr = array();
    if (is_array($user_available_balance_wallet)) {
        $walletDataArr = $user_available_balance_wallet;
        $user_available_balance_wallet = $walletDataArr['CurrentBalance'];
    }

    if (isset($currencyAssociateArr[$userData[0][$eMemberCurrency]])) {
        $currencyData = array();
        $currencyData = $currencyAssociateArr[$userData[0][$eMemberCurrency]];
    } else {
        $currencyData = $obj->MySQLSelect("SELECT vName,vSymbol,Ratio FROM currency WHERE vName = '" . $userData[0][$eMemberCurrency] . "'");
        $currencyData = $currencyData[0];
    }

    $ratio = $currencyData['Ratio'];
    $currency_vSymbol = $currencyData['vSymbol'];
    $currencycode = $currencyData['vName'];
    $user_available_balance_value = setTwoDecimalPoint($user_available_balance_wallet * $ratio);
    $user_available_balance = formateNumAsPerCurrency($user_available_balance_value, $currencycode);
}*/


$countryPaymentMethod = $obj->MySQLSelect("SELECT vPaymentGateway FROM country WHERE vCountryCode = '" . $userData[0]['vCountry'] . "'");
$USER_APP_PAYMENT_METHOD = $APP_PAYMENT_METHOD;
if (!empty($countryPaymentMethod[0]['vPaymentGateway'])) {
    $USER_APP_PAYMENT_METHOD = $countryPaymentMethod[0]['vPaymentGateway'];
}
if (!empty($iPaymentInfoId)) {
    $userPaymentInfo = $obj->MySQLSelect("SELECT tCardNum FROM user_payment_info WHERE iPaymentInfoId = '$iPaymentInfoId' AND eStatus != 'Deleted' AND ePaymentEnv = '$SYSTEM_PAYMENT_ENVIRONMENT' ");
}
$vCreditCard = (count($userPaymentInfo) > 0) ? $userPaymentInfo[0]['tCardNum'] : "";

if (empty($vCreditCard)) {
    $userPaymentInfo = getPaymentDefaultCard($GeneralMemberId, $eMemberType);
    $vCreditCard = (count($userPaymentInfo) > 0) ? $userPaymentInfo[0]['tCardNum'] : "";
    $iPaymentInfoId = !empty($userPaymentInfo) && count($userPaymentInfo) > 0 ? $userPaymentInfo[0]['iPaymentInfoId'] : 0;
}
$TOKENIZED_STATUS = strtoupper($USER_APP_PAYMENT_METHOD) . '_TOKENIZED';
$IS_TOKENIZED = $$TOKENIZED_STATUS;
if (strtoupper($IS_TOKENIZED) == "NO" && $eType != "DeliverAll") {
    // $CARD_AVAILABLE = "No";
}
$ACCEPT_CASH_TRIPS = "Yes";
if ($eType == "UberX" && !empty($iDriverId) && $iDriverId > 0) {
    $enableCommisionDeduct = $MODULES_OBJ->autoDeductDriverCommision("Ride");
    if ($enableCommisionDeduct == 'Yes') {
        $driver_available_balance = $WALLET_OBJ->FetchMemberWalletBalance($iDriverId, "Driver");
        if ($WALLET_MIN_BALANCE > $driver_available_balance) {
            $ACCEPT_CASH_TRIPS = "No";
        }
    }
}
if (strtoupper($eForVideoConsultation) == "YES" || strtoupper($isContactlessDelivery) == "YES" || strtoupper($eTakeAway) == "YES" || strtoupper($ACCEPT_CASH_TRIPS) == "NO") {
    $CASH_AVAILABLE = "No";
    $PAYMENT_MODE_RESTRICT_TO_WALLET = "Yes";
}
$low_balance = "No";
$walletNeedAmt = 0;
if ((!empty($ePaymentType) && ($ePaymentType == "ChargeOutstandingAmount" || $ePaymentType == "GiftCardPayment")) || $eType == "DeliverAll") {
    $ePayWallet = "Yes";
    $fUserOutStandingAmount = GetPassengerOutstandingAmountPayment($GeneralMemberId);
    if ($ePaymentType == "GiftCardPayment") {
        $fUserOutStandingAmount = $SYSTEM_DEFAULT_AMOUNT;
    }
    if (isset($currencyAssociateArr[$userData[0][$eMemberCurrency]])) {
        $currencyData = array();
        $currencyData = $currencyAssociateArr[$userData[0][$eMemberCurrency]];
    } else {
        $currencyData = $obj->MySQLSelect("SELECT vName,vSymbol,Ratio FROM currency WHERE vName = '" . $userData[0][$eMemberCurrency] . "'");
    }

    if ($ePaymentType == "GiftCardPayment") {
        $user_available_balance_wallet = $WALLET_OBJ->FetchMemberWalletBalance($GeneralMemberId, $eMemberTypeAlt, true);
    } else {
        $user_available_balance_wallet = $WALLET_OBJ->FetchMemberWalletBalance($GeneralMemberId, "Rider", true);
    }

    if (is_array($user_available_balance_wallet)) {
        $user_available_balance_wallet = $user_available_balance_wallet['CurrentBalance'];
    }
    // $user_available_balance_wallet = 10;

    $user_available_balance = formateNumAsPerCurrency($user_available_balance_wallet * $currencyData['Ratio'], $userData[0][$eMemberCurrency]);
    $user_available_balance_wallet = setTwoDecimalPoint($user_available_balance_wallet);
    if ($user_available_balance_wallet < $fUserOutStandingAmount) {
        $walletNeedAmt = $fUserOutStandingAmount - $user_available_balance_wallet;
        $content_msg_low_balance = $langage_lbl['LBL_LOW_WALLET_BAL_NOTE_WITH_WALLET_AMT'];
        $content_msg_low_balance = str_replace("#####", formateNumAsPerCurrency($walletNeedAmt * $currencyData['Ratio'], $userData[0][$eMemberCurrency]), $content_msg_low_balance);
        $content_msg_low_balance = str_replace("####", formateNumAsPerCurrency($user_available_balance_wallet * $currencyData['Ratio'], $userData[0][$eMemberCurrency]), $content_msg_low_balance);
        $low_balance_content_msg = str_replace("##", '\n', $content_msg_low_balance);
        $low_balance = "Yes";
    }
    if ($fUserOutStandingAmount > 0) {
        // $CASH_AVAILABLE = "No";
    }
}
if (!empty($ePaymentType) && $ePaymentType == "ChargeRentItemPostAmount") {
    $ePayWallet = "Yes";
    $fAmount = $_REQUEST['AMOUNT'];

    if (isset($currencyAssociateArr[$userData[0][$eMemberCurrency]])) {
        $currencyData = array();
        $currencyData = $currencyAssociateArr[$userData[0][$eMemberCurrency]];
    } else {
        $currencyData = $obj->MySQLSelect("SELECT vName,vSymbol,Ratio FROM currency WHERE vName = '" . $userData[0][$eMemberCurrency] . "'");
    }

    $user_available_balance_wallet = $WALLET_OBJ->FetchMemberWalletBalance($GeneralMemberId, "Rider", true);

    if (is_array($user_available_balance_wallet)) {
        $user_available_balance_wallet = $user_available_balance_wallet['CurrentBalance'];
    }

    // $user_available_balance_wallet = 10;
    $user_available_balance = formateNumAsPerCurrency($user_available_balance_wallet * $currencyData['Ratio'], $userData[0][$eMemberCurrency]);

    $user_available_balance_wallet = setTwoDecimalPoint($user_available_balance_wallet);

    if ($user_available_balance_wallet < $fAmount) {

        $walletNeedAmt = $fAmount - $user_available_balance_wallet;
        $content_msg_low_balance = $langage_lbl['LBL_LOW_WALLET_BAL_NOTE_WITH_WALLET_AMT'];

        $content_msg_low_balance = str_replace("#####", formateNumAsPerCurrency($walletNeedAmt * $currencyData['Ratio'], $userData[0][$eMemberCurrency]), $content_msg_low_balance);

        $content_msg_low_balance = str_replace("####", formateNumAsPerCurrency($user_available_balance_wallet * $currencyData['Ratio'], $userData[0][$eMemberCurrency]), $content_msg_low_balance);
        $low_balance_content_msg = str_replace("##", '\n', $content_msg_low_balance);

        $low_balance = "Yes";
    }

    if ($fAmount > 0) {
        $CASH_AVAILABLE = "No";
    }
}

$IS_SELECT_CARD = "Yes";
if ($eType == "DeliverAll") {
    $IS_SELECT_CARD = "No";
}
$APP_RETURN_URL = $tconfig['tsite_url'] . 'assets/libraries/webview/payment_mode_select.php?' . http_build_query($_GET);
$extraParamsCard = "tSessionId=" . $userData[0]['tSessionId'] . "&GeneralMemberId=" . $GeneralMemberId . "&GeneralUserType=" . $eMemberType . "&PAGE_TYPE=PAYMENT_LIST&SYSTEM_TYPE=APP&IS_RETURN_RESULT=No&IS_SELECT_CARD=$IS_SELECT_CARD&iPaymentInfoId=$iPaymentInfoId&APP_RETURN_URL=" . urlencode($APP_RETURN_URL);
$ADD_CARD_URL = $tconfig['tsite_url'] . 'assets/libraries/webview/system_payment.php?' . $extraParamsCard;
if (isset($_REQUEST['form_submit']) && strtoupper($_REQUEST['form_submit']) == "YES") {
    $payment_mode = $_REQUEST['payment_mode'];
    if (!empty($ePaymentType) && ($ePaymentType == "ChargeOutstandingAmount" || $ePaymentType == "GiftCardPayment") && ($payment_mode == "card" || $payment_mode == "wallet")) {
        if ($payment_mode == "card") {
            $ePayWallet = "No";
            $fUserOutStandingAmount = GetPassengerOutstandingAmountPayment($GeneralMemberId);
            $tDescription = "Amount charge for Outstanding";
            if ($ePaymentType == "ChargeOutstandingAmount") {
                $extraParams = "tSessionId=" . $userData[0]['tSessionId'] . "&GeneralMemberId=" . $GeneralMemberId . "&GeneralUserType=" . $eMemberType . "&AMOUNT=" . $fUserOutStandingAmount . "&PAGE_TYPE=CHARGE_OUTSTANDING_AMT&SYSTEM_TYPE=APP&IS_RETURN_RESULT=Yes&description=" . urlencode($tDescription);
            }
            if ($ePaymentType == "GiftCardPayment") {
                $GiftCardPaymentAmount = isset($_REQUEST['AMOUNT']) ? $_REQUEST['AMOUNT'] : "";
                $GiftCardPaymentAmount_default = $GiftCardPaymentAmount / $currencyData['Ratio'];

                $eWalletDebit = 'No';
                $card_amount = 0;
                if ($_REQUEST['eWalletDebit'] == 'Yes') {
                    if ($user_available_balance_wallet >= $GiftCardPaymentAmount_default) {
                        $UserType = ($GeneralUserType == "Passenger" || $GeneralUserType == "Rider") ? "Rider" : "Driver";
                        $user_wallet_debit_amount = $GiftCardPaymentAmount_default;

                        if ($user_wallet_debit_amount > 0) {
                            $data_wallet['iUserId'] = $GeneralMemberId;
                            $data_wallet['eUserType'] = $UserType;
                            $data_wallet['iBalance'] = $user_wallet_debit_amount;
                            $data_wallet['eType'] = "Debit";
                            $data_wallet['dDate'] = date("Y-m-d H:i:s");
                            $data_wallet['iTripId'] = 0;
                            $data_wallet['eFor'] = "Gift Card";
                            $data_wallet['ePaymentStatus'] = "Settelled";
                            $data_wallet['tDescription'] = "#LBL_GIFT_CARD_DEBITED#";

                            $data = $WALLET_OBJ->PerformWalletTransaction($data_wallet['iUserId'], $data_wallet['eUserType'], $data_wallet['iBalance'], $data_wallet['eType'], $data_wallet['iTripId'], $data_wallet['eFor'], $data_wallet['tDescription'], $data_wallet['ePaymentStatus'], $data_wallet['dDate']);
                        }
                        $returnUrl = base64_decode($returnUrl);
                        $RIDIRECT_URL = $tconfig['tsite_url'] . 'webservice_shark.php?' . $returnUrl . '&payStatus=succeeded&tSessionId=' . $userData[0]['tSessionId'];
                        header('Location: ' . $RIDIRECT_URL);
                        exit();
                    } else {
                        $card_amount = $GiftCardPaymentAmount_default - $user_available_balance_wallet;
                        $GiftCardPaymentAmount = $card_amount * $currencyData['Ratio'];
                        $eWalletDebitAmount = $user_available_balance_wallet;
                        $eWalletDebit = 'Yes';
                    }
                }
                $extraParams = "tSessionId=" . $userData[0]['tSessionId'] . "&eWalletDebit=".$eWalletDebit."&eWalletDebitAmount=" . $eWalletDebitAmount . "&GeneralMemberId=" . $GeneralMemberId . "&GeneralUserType=" . $eMemberType . "&AMOUNT=" . $GiftCardPaymentAmount . "&PAGE_TYPE=GIFT_CARD_PAYMENT&SYSTEM_TYPE=APP&IS_RETURN_RESULT=Yes&description=" . urlencode($tDescription) . "&returnUrl=" . $returnUrl;
            }
            header('Location: ' . $tconfig['tsite_url'] . 'assets/libraries/webview/system_payment.php?' . $extraParams . '&APP_RETURN_URL=' . urlencode($APP_RETURN_URL));
            exit;
        } else {
            if ($low_balance == "Yes") {
                if ($ePaymentType == "ChargeOutstandingAmount") {
                    $AMOUNT = $walletNeedAmt * $currencyData['Ratio'];
                    $params = "tSessionId=" . $userData[0]['tSessionId'] . "&GeneralMemberId=" . $GeneralMemberId . "&GeneralUserType=" . $eMemberType . "&AMOUNT=" . $AMOUNT . "&PAGE_TYPE=WALLET_MONEY_ADD&SYSTEM_TYPE=APP&TIME=" . time();
                    header('Location: ' . $tconfig['tsite_url'] . 'assets/libraries/webview/system_payment.php?' . $params . '&APP_RETURN_URL=' . urlencode($APP_RETURN_URL));
                    exit;
                }
                if ($ePaymentType == "GiftCardPayment") {
                    $AMOUNT = $walletNeedAmt * $currencyData['Ratio'];
                    $params = "tSessionId=" . $userData[0]['tSessionId'] . "&GeneralMemberId=" . $GeneralMemberId . "&GeneralUserType=" . $eMemberType . "&AMOUNT=" . $AMOUNT . "&PAGE_TYPE=WALLET_MONEY_ADD&SYSTEM_TYPE=APP&TIME=" . time() . "&returnUrl=" . $returnUrl;
                    header('Location: ' . $tconfig['tsite_url'] . 'assets/libraries/webview/system_payment.php?' . $params . '&APP_RETURN_URL=' . urlencode($APP_RETURN_URL));
                    exit;
                }
            } else {
                if ($ePaymentType == "ChargeOutstandingAmount") {
                    $user_wallet_debit_amount = $fUserOutStandingAmount;
                    if ($user_wallet_debit_amount > 0) {
                        $data_wallet['iUserId'] = $GeneralMemberId;
                        $data_wallet['eUserType'] = "Rider";
                        $data_wallet['iBalance'] = $user_wallet_debit_amount;
                        $data_wallet['eType'] = "Debit";
                        $data_wallet['dDate'] = date("Y-m-d H:i:s");
                        $data_wallet['iTripId'] = 0;
                        $data_wallet['eFor'] = "Outstanding";
                        $data_wallet['ePaymentStatus'] = "Settelled";
                        $data_wallet['tDescription'] = "#LBL_DEBITED_FOR_OUTSTANDING#";
                        $WALLET_OBJ->PerformWalletTransaction($data_wallet['iUserId'], $data_wallet['eUserType'], $data_wallet['iBalance'], $data_wallet['eType'], $data_wallet['iTripId'], $data_wallet['eFor'], $data_wallet['tDescription'], $data_wallet['ePaymentStatus'], $data_wallet['dDate']);
                    }
                    $iOrganizationId = 0;
                    $ePaymentBy = "Passenger";
                    $obj->sql_query("UPDATE register_user set fTripsOutStandingAmount = '0' WHERE iUserId = '" . $GeneralMemberId . "'");
                    $obj->sql_query("UPDATE trip_outstanding_amount set ePaidByPassenger = 'Yes' WHERE iUserId = '" . $GeneralMemberId . "' AND iOrganizationId = '" . $iOrganizationId . "' AND ePaymentBy = '" . $ePaymentBy . "'");
                    $params = "success=1&message=LBL_OUTSTANDING_AMOUT_PAID_TXT&SYSTEM_TYPE=APP&TIME=" . time();
                    header('Location: ' . $tconfig['tsite_url'] . 'assets/libraries/webview/success.php?' . $params);
                    exit;
                }
                if ($ePaymentType == "GiftCardPayment") {
                    $UserType = ($GeneralUserType == "Passenger" || $GeneralUserType == "Rider") ? "Rider" : "Driver";
                    $user_wallet_debit_amount = $SYSTEM_DEFAULT_AMOUNT;
                    if ($user_wallet_debit_amount > 0) {
                        $data_wallet['iUserId'] = $GeneralMemberId;
                        $data_wallet['eUserType'] = $UserType;
                        $data_wallet['iBalance'] = $user_wallet_debit_amount;
                        $data_wallet['eType'] = "Debit";
                        $data_wallet['dDate'] = date("Y-m-d H:i:s");
                        $data_wallet['iTripId'] = 0;
                        $data_wallet['eFor'] = "Gift Card";
                        $data_wallet['ePaymentStatus'] = "Settelled";
                        $data_wallet['tDescription'] = "#LBL_GIFT_CARD_DEBITED#";
                        $WALLET_OBJ->PerformWalletTransaction($data_wallet['iUserId'], $data_wallet['eUserType'], $data_wallet['iBalance'], $data_wallet['eType'], $data_wallet['iTripId'], $data_wallet['eFor'], $data_wallet['tDescription'], $data_wallet['ePaymentStatus'], $data_wallet['dDate']);
                    }
                    $returnUrl = base64_decode($returnUrl);
                    $RIDIRECT_URL = $tconfig['tsite_url'] . 'webservice_shark.php?' . $returnUrl . '&payStatus=succeeded&tSessionId=' . $userData[0]['tSessionId'];
                    header('Location: ' . $RIDIRECT_URL);
                    exit();
                }
            }
        }
    }

    if (!empty($ePaymentType) && $ePaymentType == "ChargeRentItemPostAmount" && ($payment_mode == "card" || $payment_mode == "wallet")) {

        if ($payment_mode == "card") {
            $ePayWallet = "No";
            $AMOUNT = $fAmount;
            $tDescription = "Amount charge for RentItem Post";

            $extraParams = "tSessionId=" . $userData[0]['tSessionId'] . "&GeneralMemberId=" . $GeneralMemberId . "&GeneralUserType=" . $eMemberType . "&AMOUNT=" . $AMOUNT . "&PAGE_TYPE=CHARGE_RENTITEM_POST_AMT&iTmpRentItemPostId=" . $_REQUEST['iTmpRentItemPostId'] . "&ePaymentOption=" . $payment_mode . "&iPaymentPlanId=" . $_REQUEST['iPaymentPlanId'] . "&SYSTEM_TYPE=APP&IS_RETURN_RESULT=Yes&vLanguage=" . $_REQUEST['vLanguage'] . "&description=" . urlencode($tDescription);

            header('Location: ' . $tconfig['tsite_url'] . 'assets/libraries/webview/system_payment.php?' . $extraParams . '&APP_RETURN_URL=' . urlencode($APP_RETURN_URL));
            exit;
        } else {
            if ($low_balance == "Yes") {
                $AMOUNT = $walletNeedAmt * $currencyData['Ratio'];
                $params = "tSessionId=" . $userData[0]['tSessionId'] . "&iTmpRentItemPostId=" . $_REQUEST['iTmpRentItemPostId'] . "&ePaymentOption=" . $payment_mode . "&iPaymentPlanId=" . $_REQUEST['iPaymentPlanId'] . "&GeneralMemberId=" . $GeneralMemberId . "&GeneralUserType=" . $eMemberType . "&AMOUNT=" . $AMOUNT . "&PAGE_TYPE=WALLET_MONEY_ADD&SYSTEM_TYPE=APP&vLanguage=" . $_REQUEST['vLanguage'] . "&TIME=" . time();

                header('Location: ' . $tconfig['tsite_url'] . 'assets/libraries/webview/system_payment.php?' . $params . '&APP_RETURN_URL=' . urlencode($APP_RETURN_URL));
                exit;
            } else {

                $user_wallet_debit_amount = $fAmount;
                if ($user_wallet_debit_amount > 0) {
                    $data_wallet['iUserId'] = $GeneralMemberId;
                    $data_wallet['eUserType'] = "Rider";
                    $data_wallet['iBalance'] = $user_wallet_debit_amount;
                    $data_wallet['eType'] = "Debit";
                    $data_wallet['dDate'] = date("Y-m-d H:i:s");
                    $data_wallet['iRentItemPostId'] = 0;
                    $data_wallet['iTripId'] = 0;
                    $data_wallet['iTmpRentItemPostId'] = $_REQUEST['iTmpRentItemPostId'];
                    $data_wallet['eFor'] = "Booking";
                    $data_wallet['ePaymentStatus'] = "Settelled";
                    $data_wallet['tDescription'] = "#LBL_DEBITED_RENT_POST#";

                    $walletid = $WALLET_OBJ->PerformWalletTransaction($data_wallet['iUserId'], $data_wallet['eUserType'], $data_wallet['iBalance'], $data_wallet['eType'], $data_wallet['iTripId'], $data_wallet['eFor'], $data_wallet['tDescription'], $data_wallet['ePaymentStatus'], $data_wallet['dDate'], "", $_REQUEST['iTmpRentItemPostId']);
                }

                $iOrganizationId = 0;
                $ePaymentBy = "Passenger";

                $extraParameters = "iMemberId=" . $GeneralMemberId . "&iTmpRentItemPostId=" . $_REQUEST['iTmpRentItemPostId'] . "&ePaymentOption=" . $payment_mode . "&type=GenerateFinalPost&tSessionId=" . $userData[0]['tSessionId'] . "&GeneralMemberId=" . $GeneralMemberId . "&GeneralUserType=" . $GeneralUserType . "&vLanguage=" . $_REQUEST['vLanguage'] . "&iPaymentPlanId=" . $_REQUEST['iPaymentPlanId'] . "&iUserWalletId=" . $walletid;
                $redirectUrl = $tconfig['tsite_url'] . 'webservice_shark.php?' . $extraParameters . $extraParams . "&payStatus=succeeded&SYSTEM_TYPE=" . $SYSTEM_TYPE;

                header('Location: ' . $redirectUrl);
                exit;
                //$params = "success=1&message=LBL_RENT_ITEM_AMOUT_PAID_TXT&iTmpRentItemPostId=".$_REQUEST['iTmpRentItemPostId']."&SYSTEM_TYPE=APP&TIME=" . time();
                //header('Location: ' . $tconfig['tsite_url'] . 'assets/libraries/webview/success.php?' . $params);

            }
        }
    }

    if ($payment_mode == "card" && empty($vCreditCard)) {
        header('Location: ' . $ADD_CARD_URL);
        exit;
    }
    $payment_mode_user_data = $obj->MySQLSelect("SELECT * FROM payment_mode_user_log WHERE iMemberId = '$GeneralMemberId' AND eUserType = '$eMemberType' AND eType = '$eType'");
    // echo "<pre>"; print_r($_REQUEST); exit;
    $Data_payment_mode = array();
    $tRequestData = array(
        'Profile' => $_REQUEST['profile'],
        'PaymentMode' => !empty($payment_mode) ? $payment_mode : "",
        'OrganizationId' => $_REQUEST['profile'] == "personal" ? "" : $_REQUEST['org_id'],
        'OrganizationTitle' => $_REQUEST['profile'] == "personal" ? "" : $_REQUEST['org_title'],
        'BusinessReasonId' => $_REQUEST['profile'] == "personal" ? "" : $_REQUEST['business_reason_id'],
        'BusinessReasonTitle' => $_REQUEST['profile'] == "personal" ? "" : $_REQUEST['business_reason_title'],
        'BusinessReasonOther' => $_REQUEST['profile'] == "personal" ? "" : $_REQUEST['business_reason_other'],
        'PAYMENT_MODE_RESTRICT_TO_WALLET' => $PAYMENT_MODE_RESTRICT_TO_WALLET,
        'EXTRA_MONEY_CASH_OR_OUTSTANDING' => $EXTRA_MONEY_CASH_OR_OUTSTANDING,
        'iPaymentInfoId' => $iPaymentInfoId,
        'eWalletDebit' => isset($_REQUEST['eWalletDebit']) ? $_REQUEST['eWalletDebit'] : "No",
    );
    if ($tRequestData['Profile'] == "personal") {
        $tRequestData['BusinessReasonId'] = 0;
        $tRequestData['BusinessReasonTitle'] = "";
        $tRequestData['BusinessReasonOther'] = "";
    }
    $Data_payment_mode['iMemberId'] = $GeneralMemberId;
    $Data_payment_mode['eUserType'] = $GeneralUserType;
    $Data_payment_mode['eType'] = $eType;
    $Data_payment_mode['tRequestData'] = json_encode($tRequestData, JSON_UNESCAPED_UNICODE);
    $Data_payment_mode['dDateTime'] = date('Y-m-d H:i:s');
    // echo "<pre>"; print_r($Data_payment_mode); exit;
    if (!empty($eType)) {
        if (!empty($payment_mode_user_data) && count($payment_mode_user_data) > 0) {
            $where = " iMemberId = '$GeneralMemberId' AND eUserType = '$eMemberType' AND eType = '$eType'";
            $obj->MySQLQueryPerform("payment_mode_user_log", $Data_payment_mode, 'update', $where);
        } else {
            $obj->MySQLQueryPerform('payment_mode_user_log', $Data_payment_mode, 'insert');
        }
    }
    // $params = "";
    header('Location: ' . $tconfig['tsite_url'] . 'assets/libraries/webview/payment_mode_select.php?success=1&paymentModeSelected=Yes');
    exit;
}
$payment_mode_btn_txt = $langage_lbl['LBL_DONE'];
if (!empty($ePaymentType) && ($ePaymentType == "ChargeOutstandingAmount" || $ePaymentType == "GiftCardPayment")) {
    $payment_mode_btn_txt = str_replace("####", formateNumAsPerCurrency($fUserOutStandingAmount * $currencyData['Ratio'], $userData[0][$eMemberCurrency]), $langage_lbl['LBL_PAY_AMOUNT_TXT']);
    $payment_mode_btn_add_money_txt = str_replace("####", formateNumAsPerCurrency($walletNeedAmt * $currencyData['Ratio'], $userData[0][$eMemberCurrency]), $langage_lbl['LBL_ADD_AMOUNT_TXT']);
}
$org_info_desc = explode('<br>', $langage_lbl['LBL_ORG_INFO_DESC']);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta http-equiv="expires" content="Sun, 01 Jan 2014 00:00:00 GMT"/>
    <meta http-equiv="pragma" content="no-cache"/>
    <title><?= $langage_lbl['LBL_PROFILE_PAYMENT'] ?></title>
    <link href="https://fonts.googleapis.com/css?family=Poppins:100,400,500,600,700,800,900&display=swap"
          rel="stylesheet"/>
    <link rel="stylesheet"
          href="<?= $tconfig['tsite_url'] ?>assets/libraries/webview/pg_assets/css/bootstrap-4.6.min.css">
    <link rel="stylesheet" type="text/css"
          href="<?= $tconfig['tsite_url'] ?>assets/libraries/webview/pg_assets/css/owl.carousel.min.css">
    <link rel="stylesheet" type="text/css"
          href="<?= $tconfig['tsite_url'] ?>assets/libraries/webview/pg_assets/css/owl.theme.default.min.css">
    <link rel="stylesheet" type="text/css"
          href="<?= $tconfig['tsite_url'] ?>assets/libraries/webview/pg_assets/css/snackbar.min.css">
    <link rel="stylesheet" type="text/css"
          href="<?= $tconfig['tsite_url'] ?>assets/libraries/webview/pg_assets/css/style.css">
    <link rel="stylesheet"
          href="<?= $tconfig['tsite_url'] ?>assets/css/apptype/<?= $template; ?>/style.less?time=<?= time() ?>"
          type="text/less">
    <script type="text/javascript"
            src="<?= $tconfig['tsite_url'] ?>assets/libraries/webview/pg_assets/js/jquery.min.js"></script>
    <script type="text/javascript"
            src="<?= $tconfig['tsite_url'] ?>assets/libraries/webview/pg_assets/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript"
            src="<?= $tconfig['tsite_url'] ?>assets/libraries/webview/pg_assets/js/owl.carousel.min.js"></script>
    <script type="text/javascript"
            src="<?= $tconfig['tsite_url'] ?>assets/libraries/webview/pg_assets/js/snackbar.min.js"></script>
    <script src="<?= $tconfig['tsite_url'] . $templatePath; ?>assets/js/less.min.js"></script>
    <script type="text/javascript">
        var readyStateCheckInterval = setInterval(function () {
            // console.log(document.readyState);
            if (document.readyState === "complete") {
                clearInterval(readyStateCheckInterval);
            } else {
                location.reload();
            }
        }, 10000);
    </script>
</head>
<body class="payment-mode">
<div class="overlay" style="display: block;">
    <div class="overlay__inner" style="display: none;">
        <div class="overlay__content">
            <span class="spinner"></span>
        </div>
    </div>
</div>
<div class="container py-4 px-0 custom-scroll-div">
    <div class="row payment-header">
        <div class="col-lg-8 mx-auto text-center">
            <h1 class="display-4 payment-title"><?= $langage_lbl['LBL_SELECT_PAY_MODE'] ?>
                <span class="float-right" id="close-action">
                    <a href="<?= $failure_url ?>success=0&page_action=close">
                        <img src="<?= $tconfig['tsite_url'] ?>assets/libraries/webview/pg_assets/images/quit.svg">
                    </a>
                </span>
            </h1>
            <div class="payment-title-border"></div>
        </div>
        <input type="hidden" name="pay_by_organization" id="pay_by_organization" value="<?= $pay_by_organization ?>">
        <input type="hidden" id="selected_payment_mode" value="<?= $selected_payment_mode ?>">
    </div>
    <form method="POST" action="" id="payment_mode_form">
        <input type="hidden" name="form_submit" value="Yes">
        <input type="hidden" name="form_return" value="Yes">
        <input type="hidden" name="returnUrl" value="<?php echo $returnUrl; ?>">
        <div class="row custom-mt-header custom-mb-footer mx-0">
            <ul class="nav nav-pills <?= $mb4 ?> justify-content-center w-100 profile-select" id="profile-select">
                <li class="nav-item mr-3 <?= $personal_display ?>">
                    <label class="mb-0">
                        <a class="nav-link <?= $personal_active ?>" id="personal-tab">
                            <img src="<?= $tconfig['tsite_url'] ?>assets/libraries/webview/pg_assets/images/user.svg"> <?= $langage_lbl['LBL_PERSONAL'] ?>
                            <input type="radio" name="profile" value="personal" <?= $personal_checked ?>>
                        </a>
                    </label>
                </li>
                <?php if ($eType == "Ride") { ?>
                    <li class="nav-item">
                        <label class="mb-0">
                            <a class="nav-link <?= $business_active ?>" id="business-tab">
                                <img src="<?= $tconfig['tsite_url'] ?>assets/libraries/webview/pg_assets/images/briefcase.svg"> <?= $langage_lbl['LBL_ORGANIZATION'] ?>
                                <input type="radio" name="profile" value="business" <?= $business_checked ?>>
                            </a>
                        </label>
                    </li>
                <?php } ?>
            </ul>
            <?php if (strtoupper($WALLET_ENABLE) == "YES" && strtoupper($WALLET_AVAILABLE) == "NO") { ?>
                <div class="in-app-wallet mb-3 w-100" id="in-app-wallet">
                    <ul class="list-group">
                        <li class="list-group-item d-flex align-items-center justify-content-between">
                            <span>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="eWalletDebit"
                                           id="eWalletDebit"
                                           value="Yes" <?= $user_available_balance_value > 0 ? 'checked' : '' ?>>
                                    <label class="custom-control-label font-weight-bold pl-2"
                                           for="eWalletDebit"><?= $langage_lbl['LBL_USE_WALLET_BALANCE'] ?>
                                        <br> <span class="in-app-wallet-amount"><?= $user_available_balance ?></span>
                                    </label>
                                </div>
                            </span>
                            <span>
                                <button type="button" class="btn btn-link font-weight-bold add-money-btn"
                                        id="add_money_btn"><?= $langage_lbl['LBL_ACTION_ADD'] ?></button>
                            </span>
                        </li>
                    </ul>
                </div>
            <?php } ?>

            <?php if (!empty($ePaymentType) && ($ePaymentType == "ChargeOutstandingAmount")) { ?>
                <ul class="nav nav-pills mb-4 justify-content-center w-100 profile-select">
                    <li class="nav-item mx-3">
                        <a class="nav-link text-dark"><?= $langage_lbl['LBL_OUTSTANDING_AMOUNT_TXT'] ?>:
                            <strong><?= formateNumAsPerCurrency($fUserOutStandingAmount * $currencyData['Ratio'], $userData[0][$eMemberCurrency]) ?></strong>
                        </a>
                    </li>
                </ul>
            <?php } ?>

            <?php /*if($eType == "DeliverAll" && $fUserOutStandingAmount > 0) { ?>
                        <ul class="nav nav-pills mb-4 justify-content-center w-100 profile-select">
                            <li class="nav-item mx-3">
                                <a class="nav-link text-dark"><?= $langage_lbl['LBL_COD_NOT_AVAILABLE_TXT'] ?> <strong><?= formateNumAsPerCurrency($fUserOutStandingAmount * $currencyData['Ratio'], $userData[0][$eMemberCurrency]) ?></strong></a>
                            </li>
                        </ul>
                    <?php }*/ ?>
            <div class="business-tab-content w-100 px-3"
                 id="business-tab-content" <?= $Profile == "business" && $eType == "Ride" ? 'style="display: block;"' : '' ?>>
                <?php if (strtoupper($isOrgAvailable) == "YES") { ?>
                    <div class="business-reason-info mb-4">
                        <ul class="list-group">
                            <li class="list-group-item d-flex align-items-center justify-content-between">
                                <span class="font-weight-bold mr-4"><?= $langage_lbl['LBL_ORGANIZATION'] ?></span>
                                <span>
                                    <input type="text" class="form-control custom-bg-white" name="org_title"
                                           id="org_title" readonly="readonly"
                                           placeholder="<?= $langage_lbl['LBL_SELECT_TXT'] ?>" data-toggle="modal"
                                           data-target="#select_org"
                                           data-errormsg="<?= $langage_lbl['LBL_SELECT_ORGANIZATION'] ?>"
                                           value="<?= $OrganizationTitle ?>">
                                    <input type="hidden" name="org_id" id="org_id" value="<?= $OrganizationId ?>">
                                </span>
                            </li>
                            <li class="list-group-item align-items-center justify-content-between d-none-alt" <?= !empty($OrganizationTitle) ? 'style="display: flex;"' : '' ?>
                                id="org_reason">
                                <span class="font-weight-bold mr-4"><?= $langage_lbl['LBL_REASON'] ?></span>
                                <span>
                                    <input type="text" class="form-control custom-bg-white" name="business_reason_title"
                                           id="business_reason_title" readonly="readonly"
                                           placeholder="<?= $langage_lbl['LBL_SELECT_REASON'] ?>" data-toggle="modal"
                                           data-target="#select_business_reason"
                                           data-errormsg="<?= $langage_lbl['LBL_SELECT_REASON_RIDE'] ?>"
                                           value="<?= $BusinessReasonTitle ?>">
                                    <input type="hidden" name="business_reason_id" id="business_reason_id"
                                           value="<?= $BusinessReasonId ?>">
                                </span>
                            </li>
                            <li class="list-group-item border-top-0 d-none-alt"
                                id="business_reason_other" <?= $BusinessReasonId == 0 ? 'style="display: block;"' : '' ?>>
                                <label class="font-weight-bold w-100"><?= $langage_lbl['LBL_WRITE_REASON_BELOW'] ?></label>
                                <textarea class="form-control" rows="3" name="business_reason_other"
                                          data-errormsg="<?= $langage_lbl['LBL_ENTER_REASON_RIDE'] ?>"><?= $BusinessReasonOther ?></textarea>
                            </li>
                        </ul>
                    </div>
                    <textarea class="d-none" id="org_reasons_arr"><?= getJsonFromAnArr($org_reasons_arr); ?></textarea>
                    <input type="hidden" id="other_val" value="<?= $langage_lbl['LBL_OTHER_TXT'] ?>">
                    <input type="hidden" id="selected_org_title" value="<?= $OrganizationTitle ?>">
                    <input type="hidden" id="selected_org_id" value="<?= $OrganizationId ?>">
                    <input type="hidden" id="selected_business_reason_title" value="<?= $BusinessReasonTitle ?>">
                    <input type="hidden" id="selected_business_reason_id" value="<?= $BusinessReasonId ?>">
                    <input type="hidden" id="selected_business_reason_other" value="<?= $BusinessReasonOther ?>">
                <?php } else { ?>
                    <div class="alert alert-secondary" id="org-info" style="background-color: #eeeeee;">
                        <div class="org-info">
                            <img src="<?= $tconfig['tsite_url'] ?>assets/libraries/webview/pg_assets/images/briefcase.png">
                            <p class="org-info-title"><?= $langage_lbl['LBL_ORG_INFO_TITLE'] ?></p>
                        </div>
                        <div class="org-info-details">
                            <ul>
                                <?php foreach ($org_info_desc as $desc_val) { ?>
                                    <li><?= $desc_val ?></li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                <?php } ?>
                <div class="alert alert-secondary d-none-alt" role="alert"
                     id="org_note" <?= $pay_by_organization == "Yes" ? 'style="display: block;"' : '' ?>>
                    <img src="<?= $tconfig['tsite_url'] ?>assets/libraries/webview/pg_assets/images/exclamation-mark.svg"> <?= $langage_lbl['LBL_ORGANIZATION_NOTE'] ?>
                </div>
            </div>
            <div class="modal fade custom-select-modal" tabindex="-1" role="dialog" data-backdrop="static"
                 data-keyboard="false" id="select_org">
                <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><?= $langage_lbl['LBL_SELECT_ORGANIZATION_TITLE'] ?></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <ul class="list-group">
                                <?php foreach ($profileDetails['ORG_DATA'] as $org) { ?>
                                    <li class="list-group-item <?= $OrganizationId == $org['iOrganizationId'] ? 'active' : '' ?>"
                                        data-val="<?= $org['vProfileName'] ?>" data-id="<?= $org['iOrganizationId'] ?>"
                                        data-payment="<?= strtolower($org['ePaymentBy']) ?>"
                                        onclick="selectOrganization(this)">
                                        <span><?= $org['vProfileName'] ?></span>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade custom-select-modal" tabindex="-1" role="dialog" data-backdrop="static"
                 data-keyboard="false" id="select_business_reason">
                <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><?= $langage_lbl['LBL_SELECT_REASON'] ?></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <ul class="list-group">
                                <?php if (!empty($OrganizationId)) {
                                    foreach ($org_reasons_arr['org_' . $OrganizationId] as $reason) { ?>
                                        <li class="list-group-item <?= $BusinessReasonId == $reason['iTripReasonId'] ? 'active' : '' ?>"
                                            data-val="<?= $reason['vReasonTitle'] ?>"
                                            data-id="<?= $reason['iTripReasonId'] ?>"
                                            onclick="selectBusinessReason(this)">
                                            <span><?= $reason['vReasonTitle'] ?></span>
                                        </li>
                                    <?php } ?>
                                    <li class="list-group-item <?= $BusinessReasonId == 0 ? 'active' : '' ?>"
                                        data-val="Other" data-id="0" onclick="selectBusinessReason(this)">
                                        <span><?= $langage_lbl['LBL_OTHER_TXT'] ?></span>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="owl-carousel owl-theme"
                 id="personal-tab-content" <?= $pay_by_organization == "Yes" && $Profile == "business" && $eType == "Ride" ? 'style="display: none;"' : '' ?>>
                <?php if (strtoupper($CASH_AVAILABLE) == "YES") { ?>
                    <div class="item" data-hash="cash">
                        <label class="payment-mode-block mb-0 <?= $cash_active ?>" id="payment_mode_cash" href="#cash">
                            <img class="payment-mode"
                                 src="<?= $tconfig['tsite_url'] ?>assets/libraries/webview/pg_assets/images/cash.svg"
                                 alt="Cash">
                            <div class="payment-mode-content">
                                <div class="payment-mode-title"><?= $langage_lbl['LBL_CASH_TXT'] ?></div>
                            </div>
                            <input type="radio" name="payment_mode" value="cash" <?= $cash_checked ?>>
                        </label>
                    </div>
                <?php }
                if (strtoupper($CARD_AVAILABLE) == "YES") { ?>
                    <div class="item" data-hash="card">
                        <label class="payment-mode-block mb-0 <?= $card_active ?>" id="payment_mode_card" href="#card">
                            <img class="payment-mode"
                                 src="<?= $tconfig['tsite_url'] ?>assets/libraries/webview/pg_assets/images/card.svg"
                                 alt="Card">
                            <div class="payment-mode-content">
                                <div class="payment-mode-title"><?= $langage_lbl['LBL_CARD'] ?></div>
                            </div>
                            <input type="radio" name="payment_mode" value="card" <?= $card_checked ?>>
                        </label>
                    </div>
                <?php }
                if (strtoupper($WALLET_AVAILABLE) == "YES") { ?>
                    <div class="item" data-hash="wallet">
                        <label class="payment-mode-block mb-0 <?= $wallet_active ?>" id="payment_mode_wallet"
                               href="#wallet">
                            <img class="payment-mode"
                                 src="<?= $tconfig['tsite_url'] ?>assets/libraries/webview/pg_assets/images/wallet.svg"
                                 alt="Wallet">
                            <div class="payment-mode-content">
                                <div class="payment-mode-title"><?= $langage_lbl['LBL_WALLET_TXT'] ?>
                                    <span>(<?= $user_available_balance ?>)</span>
                                </div>
                            </div>
                            <input type="radio" name="payment_mode" value="wallet" <?= $wallet_checked ?>>
                        </label>
                    </div>
                <?php } ?>
            </div>
            <input type="hidden" id="payment_mode_error" value="<?= $langage_lbl['LBL_SELECT_PAYMENT_MODE_MSG'] ?>">
            <?php if (!(!empty($ePaymentType) && ($ePaymentType == "ChargeOutstandingAmount" || $ePaymentType == "GiftCardPayment")) && strtoupper($CARD_AVAILABLE) == "YES") { ?>
                <div class="card-info my-4 w-100 px-3" <?= $pay_by_organization == "Yes" && $Profile == "business" && $eType == "Ride" ? 'style="display: none;"' : ($PaymentMode == "card" && $Profile == "personal" ? 'style="display: block;"' : "") ?>>
                    <ul class="list-group">
                        <li class="list-group-item d-flex align-items-center justify-content-between">
                            <?php if (!empty($vCreditCard)) { ?>
                                <span><?= $vCreditCard ?></span>
                                <a href="javascript:void(0);" class="btn btn-link"
                                   onclick="changePaymentCard('<?= $ADD_CARD_URL ?>');"><?= $langage_lbl['LBL_CHANGE_CARD'] ?></a>
                            <?php } else { ?>
                                <span><?= ucwords(strtolower($langage_lbl['LBL_NO_CARD_AVAIL_HEADER_NOTE'])) ?></span>
                                <a href="<?= $ADD_CARD_URL ?>" class="btn btn-link"
                                   onclick="showOverlay()"><?= $langage_lbl['LBL_ADD_CARD'] ?></a>
                            <?php } ?>
                        </li>
                    </ul>
                </div>
            <?php } ?>
        </div>
        <input type="hidden" id="promocode_val" name="promocode_val"
               value="<?= !empty($PromoCode) ? $PromoCode : '' ?>">
        <input type="hidden" id="promocode_val_temp"
               value="<?= !empty($PromoCode) ? 'OPEN_PROMOCODE_' . $PromoCode : '' ?>">
    </form>
    <div class="row payment-footer">
        <div class="col-lg-8 mx-auto text-center pt-3">
            <?php if ($eType == "Ride") { ?><?php if (!empty($PromoCode)) { ?>
                <div class="promocode-section justify-content-between">
                    <div id="promocode_applied">
                        <img src="<?= $tconfig['tsite_url'] ?>assets/libraries/webview/pg_assets/images/promocode.png">
                        <div id="promocode"><?= $langage_lbl['LBL_APPLIED_PROMO_CODE'] ?>
                            <br>
                            <span class="promocode-val"><?= $PromoCode ?></span>
                        </div>
                    </div>
                    <div class="change-promocode" id="change_promocode">
                        <a href="javascript:void(0);" class="btn btn-link"
                           onclick="openPromoCode()"><?= $langage_lbl['LBL_CHANGE'] ?></a>
                    </div>
                </div>
            <?php } else { ?>
                <div class="promocode-section">
                    <div id="promocode_applied" onclick="openPromoCode()">
                        <img src="<?= $tconfig['tsite_url'] ?>assets/libraries/webview/pg_assets/images/promocode.png">
                        <div id="promocode"><?= $langage_lbl['LBL_GOT_PROMOCODE_TXT'] ?></div>
                    </div>
                    <div class="change-promocode" id="change_promocode" style="display: none;">
                        <a href="javascript:void(0);" class="btn btn-link"
                           onclick="openPromoCode()"><?= $langage_lbl['LBL_CHANGE'] ?></a>
                    </div>
                </div>
            <?php } ?><?php } ?>
            <input type="hidden" id="ePaymentType" value="<?= $ePaymentType ?>">
            <?php if ($ePaymentType != "ChargeOutstandingAmount" || $ePaymentType != "GiftCardPayment") { ?>
                <button type="button" class="btn btn-primary btn-block btn-lg text-uppercase payment-mode-btn"
                        id="payment_mode_btn" onclick="paymentModeForm()"><?= $langage_lbl['LBL_DONE'] ?></button>
            <?php } else { ?>
                <button type="button" class="btn btn-primary btn-block btn-lg text-uppercase payment-mode-btn"
                        id="payment_mode_btn" onclick="paymentModeForm()"><?= $payment_mode_btn_txt ?></button>
            <?php } ?>
        </div>
    </div>
</div>
<script src="<?= $tconfig['tsite_url'] ?>assets/libraries/webview/pg_assets/js/custom.js"></script>
<script type="text/javascript">
    var org_available = '<?= $isOrgAvailable ?>';

    function setPaymentModeBtnText() {
        <?php if(!empty($ePaymentType) && ($ePaymentType == "ChargeOutstandingAmount" || $ePaymentType == "GiftCardPayment")) { ?>
        if ($('#ePaymentType').val().trim() == "ChargeOutstandingAmount" || $('#ePaymentType').val().trim() == "GiftCardPayment") {
            if ($('[name="payment_mode"]:checked').val() == "card") {
                $('#payment_mode_btn').html("<?= $payment_mode_btn_txt ?>");
            } else {

                $('#payment_mode_btn').html("<?= $payment_mode_btn_add_money_txt ?>");
                <?php if($low_balance == "Yes") { ?>
                if ($('#ePaymentType').val().trim() == "ChargeOutstandingAmount") {
                    showSnackbar("<?= $low_balance_content_msg ?>");
                }
                return false;
                <?php } else { ?>
                $('#payment_mode_btn').html("<?= $payment_mode_btn_txt ?>");
                <?php } ?>
            }
        }
        <?php } ?>

        <?php if(empty($vCreditCard)) { ?>
        if ($('[name="payment_mode"]:checked').val() == "card") {
            $('#payment_mode_btn').html("<?= $langage_lbl['LBL_ADD_CARD'] ?>");
        } else {
            $('#payment_mode_btn').html("<?= $langage_lbl['LBL_DONE'] ?>");
        }
        <?php } ?>

        if ($('[name="profile"]:checked').val() == "business" && org_available == "No") {
            $('#payment_mode_btn').html("<?= $langage_lbl['LBL_PROFILE_SETUP_BTN_TXT'] ?>");
            $('#payment_mode_btn').attr("onclick", "profileAdd()");
        } else {
            $('#payment_mode_btn').html("<?= $langage_lbl['LBL_DONE'] ?>");
            $('#payment_mode_btn').attr("onclick", "paymentModeForm()");

            <?php if(empty($vCreditCard)) { ?>
            if ($('[name="payment_mode"]:checked').val() == "card") {
                $('#payment_mode_btn').html("<?= $langage_lbl['LBL_ADD_CARD'] ?>");
            } else {
                $('#payment_mode_btn').html("<?= $langage_lbl['LBL_DONE'] ?>");
            }
            <?php } ?>
        }
    }

    $('#add_money_btn').click(function () {
        redirectToUrl("<?= $tconfig['tsite_url'] ?>assets/libraries/webview/payment_mode_select.php?OPEN_IN_APP_WALLET=Yes");
    });

    function profileAdd() {
        redirectToUrl("<?= $tconfig['tsite_url'] ?>assets/libraries/webview/payment_mode_select.php?BUSINESS_PROFILE_SETUP=Yes");
    }

    function openPromoCode() {
        var promocode_val = $('#promocode_val').val();
        var promocode_val_temp = $('#promocode_val_temp').val();
        if (promocode_val_temp == "") {
            promocode_val_temp = "OPEN_PROMOCODE";
            promocode_val = "";
        }
        var promocode = prompt("Enter Promocode", promocode_val_temp);
        if (promocode == "" || promocode == null) {
            promocode = "<?= addslashes($langage_lbl['LBL_GOT_PROMOCODE_TXT']) ?>";
            promocode_val_temp = promocode_val = "";
            $('#change_promocode').hide();
            $('.promocode-section').removeClass('justify-content-between');
            $('#promocode_applied').attr('onclick', 'openPromoCode()');
        } else {
            promocode_val_temp = "OPEN_PROMOCODE_" + promocode;
            promocode_val = promocode;
            promocode = "<?= addslashes($langage_lbl['LBL_APPLIED_PROMO_CODE']) ?>: <br><span class=\"promocode-val\">" + promocode + "<span>";
            $('#change_promocode').show();
            $('.promocode-section').addClass('justify-content-between');
            $('#promocode_applied').removeAttr('onclick');
        }
        $('#promocode').html(promocode);
        $('#promocode_val_temp').val(promocode_val_temp);
        $('#promocode_val').val(promocode_val);
    }
</script>
</body>
</html>
