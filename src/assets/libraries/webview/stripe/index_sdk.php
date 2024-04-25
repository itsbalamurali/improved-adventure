<?php
include_once('../../../../common.php');
require_once('init.php');

    use Stripe\Customer;
    use Stripe\Exception\ApiConnectionException;
    use Stripe\PaymentIntent;
    use Stripe\PaymentMethod;
    use Stripe\SetupIntent;
    use Stripe\Stripe;

    // ini_set('display_errors', 1);
// error_reporting(E_ALL);
$UserType = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : $_REQUEST["GeneralUserType"];
$iUserId = isset($_REQUEST["iUserId"]) ? $_REQUEST["iUserId"] : $_REQUEST["GeneralMemberId"];
$iServiceId = isset($_REQUEST["iServiceId"]) ? $_REQUEST["iServiceId"] : '';
$SYSTEM_TYPE = isset($_REQUEST["SYSTEM_TYPE"]) ? $_REQUEST["SYSTEM_TYPE"] : 'APP';
Stripe::setApiKey($STRIPE_SECRET_KEY);
Stripe::setMaxNetworkRetries(5);
$fromOrder = "guest";
if (isset($_REQUEST['order']) && $_REQUEST['order'] != "") {
    $fromOrder = $_REQUEST['order'];
}
if ($UserType == "Rider" || $UserType == "Passenger") {
    $dbUserType = "Passenger";
    $sql = "SELECT iUserId,vName,vLastName,vLang,vCurrencyPassenger as userCurrency,vEmail,vCountry FROM `register_user` WHERE iUserId='" . $iUserId . "'";
    $dbField = "iUserId";
    $tblname = "register_user";
} else {
    $dbUserType = "Driver";
    $sql = "SELECT iDriverId,vName,vLastName,vLang,vCurrencyDriver as userCurrency,vEmail,vCountry FROM `register_driver` WHERE iDriverId='" . $iUserId . "'";
    $dbField = "iDriverId";
    $tblname = "register_driver";
}
$userData = $obj->MySQLSelect($sql);
$full_name = $userData[0]['vName'] . ' ' . $userData[0]['vLastName'];
$vLang = $userData[0]['vLang'];
if ($vLang == "") {
    $lang_data = $obj->MySQLSelect("SELECT vCode FROM language_master WHERE eDefault = 'Yes'");
    $vLang = $lang_data[0]['vCode'];
}
$languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLang, "1", $iServiceId);
$vCountry = $userData[0]['vCountry'];
$USER_APP_PAYMENT_METHOD = "Stripe";
$tSessionId = isset($_REQUEST['tSessionId']) ? trim($_REQUEST['tSessionId']) : '';
$GeneralMemberId = isset($_REQUEST['GeneralMemberId']) ? trim($_REQUEST['GeneralMemberId']) : '';
$GeneralUserType = isset($_REQUEST['GeneralUserType']) ? trim($_REQUEST['GeneralUserType']) : '';
$AMOUNT = isset($_REQUEST["AMOUNT"]) ? $_REQUEST["AMOUNT"] : 1;
$currency = isset($_REQUEST["currencyCode"]) ? $_REQUEST["currencyCode"] : 'USD';
$returnUrl = isset($_REQUEST['returnUrl']) ? trim($_REQUEST['returnUrl']) : 'webservice_shark.php';
$iOrderId = isset($_REQUEST["iOrderId"]) ? $_REQUEST["iOrderId"] : '';
$orderNo = isset($_REQUEST["orderNo"]) ? $_REQUEST["orderNo"] : '';
$description = isset($_REQUEST["description"]) ? $_REQUEST["description"] : 'Payment';
$ePaymentOption = isset($_REQUEST["ePaymentOption"]) ? $_REQUEST["ePaymentOption"] : '';
$type = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : 'CaptureCardPaymentOrder';
$CheckUserWallet = isset($_REQUEST["CheckUserWallet"]) ? $_REQUEST["CheckUserWallet"] : 'No';
$vPayMethod = isset($_REQUEST["vPayMethod"]) ? $_REQUEST["vPayMethod"] : 'Instant'; // Instant,Manual
$UserType = $GeneralUserType;
$iTripId = isset($_REQUEST["iTripId"]) ? $_REQUEST["iTripId"] : '0';
$cancelUrl = isset($_REQUEST["cancelUrl"]) ? $_REQUEST["cancelUrl"] : '';
$page_type = isset($_REQUEST["PAGE_TYPE"]) ? $_REQUEST["PAGE_TYPE"] : '';
$payment_intent = isset($_REQUEST["payment_intent"]) ? $_REQUEST["payment_intent"] : '';
$payment_intent_client_secret = isset($_REQUEST["payment_intent_client_secret"]) ? $_REQUEST["payment_intent_client_secret"] : '';
$stripe_return_url = $current_url = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '&TIME=' . time();
$stripe_return_url = $current_url = preg_replace("/&TIME=\d+/", "", $current_url) . '&TIME=' . time();
$APP_RETURN_URL = isset($_REQUEST['APP_RETURN_URL']) ? $_REQUEST['APP_RETURN_URL'] : "";
$failure_url = $tconfig['tsite_url'] . "assets/libraries/webview/failure.php";
$failure_msg = $languageLabelsArr['LBL_SERVER_COMM_ERROR'];
try {
    $intent = SetupIntent::create();
} catch (ApiConnectionException $e) {
    header('Location:' . $failure_url . "?success=0&message=" . $failure_msg . "&vLang=" . $vLang . "&SYSTEM_TYPE=" . $SYSTEM_TYPE . "&previous_url=" . urlencode($current_url));
    exit;
} catch (Exception $e) {
    $failure_msg = $e->getMessage();
    header('Location:' . $failure_url . "?success=0&message=" . $failure_msg . "&vLang=" . $vLang . "&SYSTEM_TYPE=" . $SYSTEM_TYPE . "&previous_url=" . urlencode($current_url));
    exit;
}
if (!empty($iOrderId) && $iOrderId > 0) {
    $orderData = $obj->MySQLSelect("SELECT * FROM orders WHERE iOrderId = '$iOrderId' ");
    if ($orderData[0]['eProcessed'] == "Yes" && $page_type == "CHARGE_CARD") {
        $failure_msg = "Invalid Request. Please check your order status in your orders history.";
        header('Location:' . $failure_url . "?success=0&message=" . $failure_msg . "&vLang=" . $vLang . "&SYSTEM_TYPE=" . $SYSTEM_TYPE . "&INVALID_REQUEST=Yes");
        exit;
    }
}
if ($page_type == "WALLET_MONEY_ADD") {
    $description = "Added money to wallet";
}
if ($page_type == "AUTHORIZE_TRIP_AMOUNT") {
    $vRideBookingNo = isset($_REQUEST['vRideBookingNo']) ? $_REQUEST['vRideBookingNo'] : "";
    // $description = "Authorized amount for Ride Booking - " . $vRideBookingNo;
    $description = "Authorized amount for Ride Booking";
}
$currencySql = "SELECT * FROM currency WHERE vName = '" . $userData[0]['userCurrency'] . "'";
$currencyData = $obj->MySQLSelect($currencySql);
$ccode = $currencyData[0]['vSymbol'];
$defCurrencySql = "SELECT * FROM currency WHERE eDefault = 'Yes'";
$defCurrencyData = $obj->MySQLSelect($defCurrencySql);
$status = "failed";
$STRIPE_AMOUNT = $AMOUNT;
if ($AMOUNT < $STRIPE_MINIMUM_AMOUNT) {
    $STRIPE_AMOUNT = $STRIPE_MINIMUM_AMOUNT;
}
//$STRIPE_AMOUNT = setTwoDecimalPoint($STRIPE_AMOUNT);
$paymentCustomerInfo = getPaymentCustomerInfo($iUserId, $dbUserType);
if (!empty($_REQUEST['payMethod'])) {
    $name = isset($_POST["name"]) ? $_POST["name"] : '';
    $email = $userData[0]['vEmail'];
    $paymentMethod = $_REQUEST['payMethod'];
    if (!empty($paymentCustomerInfo) && count($paymentCustomerInfo) > 0) {
        $custId = $paymentCustomerInfo[0]['tCustomerId'];
        $payment_method = PaymentMethod::retrieve($paymentMethod);
        $payment_method->attach(['customer' => $custId]);
    } else {
        try {
            $datae = Customer::create(['payment_method' => $paymentMethod]);
            $custId = $datae->id;
            $data_member = array();
            $data_member['iMemberId'] = $iUserId;
            $data_member['eUserType'] = $dbUserType;
            $data_member['tCustomerId'] = $custId;
            $data_member['vPaymentMethod'] = 'Stripe';
            $data_member['ePaymentEnv'] = $SYSTEM_PAYMENT_ENVIRONMENT;
            $obj->MySQLQueryPerform('payment_customer_info', $data_member, 'insert');
        } catch (Exception $e) {
            $failure_msg = $e->getMessage();
            header('Location:' . $failure_url . "?success=0&message=" . $failure_msg . "&vLang=" . $vLang . "&SYSTEM_TYPE=" . $SYSTEM_TYPE . "&previous_url=" . urlencode($current_url));
            exit;
        }
    }
    // Create charge
    $iMemberId = $iUserId;
    $paymentData = array("amount" => $STRIPE_AMOUNT, "description" => $description, "iMemberId" => $iMemberId, "UserType" => $dbUserType, "tCardToken" => $paymentMethod, "tCustomerId" => $custId, "iOrderId" => $iOrderId,);
    if ($STRIPE_TOKENIZED == "No") {
        $paymentData['return_url'] = $stripe_return_url;
    }
    if ($page_type == "AUTHORIZE_TRIP_AMOUNT") {
        $paymentData['isAuthorize'] = "Yes";
    }
    $result = (PaymentGateways::getInstance())->execute($paymentData);
    if ($result['Action'] == "1") {
        $sql = "SELECT * FROM user_payment_info WHERE tCardToken = '" . $paymentMethod . "'";
        $sqlData = $obj->MySQLSelect($sql);
        $paymentInfoSql = "SELECT * FROM user_payment_info WHERE iMemberId = " . $iUserId . " AND eUserType = '" . $dbUserType . "' AND vPaymentMethod = '" . $USER_APP_PAYMENT_METHOD . "' AND eStatus = 'Active' AND eDefault = 'Yes'";
        $paymentInfoData = $obj->MySQLSelect($paymentInfoSql);
        // Insert in User payment info
        $user_payment_info_insert['eDefault'] = 'No';
        if (count($paymentInfoData) == 0) {
            $user_payment_info_insert['eDefault'] = 'Yes';
        }
        $user_payment_info_insert['eUserType'] = $dbUserType;
        $user_payment_info_insert['tCardToken'] = $paymentMethod;
        $user_payment_info_insert['tCardNum'] = 'XXXX XXXX XXXX ' . $result['last4digits'];
        $user_payment_info_insert['vCardBrand'] = strtolower($result['vCardBrand']);
        $payment_arr['CARD_TOKEN'] = $paymentMethod;
        $payment_id = $result['payment_id'];
        $AMOUNT = $STRIPE_AMOUNT;
        // Update details in db
        include $tconfig['tpanel_path'] . 'assets/libraries/webview/capture-payment-details.php';
        exit;
    } else {
        $returnArr = $result;
        $failure_msg = $result['message'];
        if (isset($result['status']) && $result['status'] == "failed") {
            $failure_msg = $languageLabelsArr['LBL_CHARGE_COLLECT_FAILED'];
        }
        header('Location:' . $failure_url . "?success=0&message=" . $failure_msg . "&vLang=" . $vLang . "&SYSTEM_TYPE=" . $SYSTEM_TYPE . "&previous_url=" . urlencode($current_url));
        exit;
    }
}
if ($payment_intent != "" && $payment_intent_client_secret != "") {
    $payment_intent_data = PaymentIntent::retrieve($payment_intent, ['client_secret' => $payment_intent_client_secret]);
    if ($payment_intent_data['status'] == "succeeded" && $payment_intent_data['charges']['data'][0]['paid'] == "1") {
        $responseArr['Action'] = 1;
        $responseArr['tPaymentTransactionId'] = $payment_intent_data['charges']['data'][0]['id'];
        $responseArr['tCardToken'] = $payment_intent_data['charges']['data'][0]['payment_method'];
        $responseArr['vCardBrand'] = $payment_intent_data['charges']['data'][0]['payment_method_details']['card']['brand'];
        $responseArr['last4digits'] = $payment_intent_data['charges']['data'][0]['payment_method_details']['card']['last4'];
        $responseArr['message'] = "success";
        $responseArr['USER_APP_PAYMENT_METHOD'] = "Stripe";
        $responseArr['iMemberId'] = $iUserId;
        $responseArr['UserType'] = $dbUserType;
        $responseArr['amount'] = $STRIPE_AMOUNT;
        $payment_id = (PaymentGateways::getInstance())->paymentDetailsInsert($responseArr);
        $sql = "SELECT * FROM user_payment_info WHERE tCardToken = '" . $responseArr['tCardToken'] . "'";
        $sqlData = $obj->MySQLSelect($sql);
        if (!isset($_REQUEST['saved_card'])) {
            $paymentInfoSql = "SELECT * FROM user_payment_info WHERE iMemberId = " . $iUserId . " AND eUserType = '" . $dbUserType . "' AND vPaymentMethod = '" . $USER_APP_PAYMENT_METHOD . "' AND eStatus = 'Active' AND eDefault = 'Yes'";
            $paymentInfoData = $obj->MySQLSelect($paymentInfoSql);
            // Insert in User payment info
            $user_payment_info_insert['eDefault'] = 'No';
            if (count($paymentInfoData) == 0) {
                $user_payment_info_insert['eDefault'] = 'Yes';
            }
            $user_payment_info_insert['eUserType'] = $dbUserType;
            $user_payment_info_insert['tCardToken'] = $responseArr['tCardToken'];
            $user_payment_info_insert['tCardNum'] = 'XXXX XXXX XXXX ' . $responseArr['last4digits'];
            $user_payment_info_insert['vCardBrand'] = strtolower($responseArr['vCardBrand']);
        }
        $payment_arr['CARD_TOKEN'] = $responseArr['tCardToken'];
        $AMOUNT = $STRIPE_AMOUNT;
        // Update details in db
        include $tconfig['tpanel_path'] . 'assets/libraries/webview/capture-payment-details.php';
        exit;
    } else {
        // $payment_intent_data = $payment_intent_data->jsonSerialize();
        $failure_msg = $payment_intent_data['last_payment_error']['message'];
        header('Location:' . $failure_url . "?success=0&message=" . $failure_msg . "&vLang=" . $vLang . "&SYSTEM_TYPE=" . $SYSTEM_TYPE . "&previous_url=" . urlencode($current_url));
        exit;
    }
}
if (isset($_POST['saved_card']) && $_POST['saved_card'] != "") {
    $paymentMethod = $_POST['saved_card'];
    // Create charge
    $iMemberId = $iUserId;
    $paymentData = array("amount" => $STRIPE_AMOUNT, "description" => $description, "iMemberId" => $iMemberId, "UserType" => $dbUserType, "tCardToken" => $paymentMethod, "tCustomerId" => $paymentCustomerInfo[0]['tCustomerId'], "iOrderId" => $iOrderId,);
    if ($STRIPE_TOKENIZED == "No") {
        $paymentData['return_url'] = $stripe_return_url . "&saved_card=1";
    }
    if ($page_type == "AUTHORIZE_TRIP_AMOUNT") {
        $paymentData['isAuthorize'] = "Yes";
    }
    // echo "<pre>"; print_r($paymentData); exit;
    $result = (PaymentGateways::getInstance())->execute($paymentData);
    if ($result['Action'] == "1") {
        $sql = "SELECT * FROM user_payment_info WHERE tCardToken = '" . $paymentMethod . "'";
        $sqlData = $obj->MySQLSelect($sql);
        $payment_arr['CARD_TOKEN'] = $paymentMethod;
        $payment_id = $result['payment_id'];
        $AMOUNT = $STRIPE_AMOUNT;
        // Update details in db
        include $tconfig['tpanel_path'] . 'assets/libraries/webview/capture-payment-details.php';
        exit;
    } else {
        $returnArr = $result;
        $failure_msg = $result['message'];
        if (isset($result['status']) && $result['status'] == "failed") {
            $failure_msg = $languageLabelsArr['LBL_CHARGE_COLLECT_FAILED'];
        }
        header('Location:' . $failure_url . "?success=0&message=" . $failure_msg . "&vLang=" . $vLang . "&SYSTEM_TYPE=" . $SYSTEM_TYPE . "&previous_url=" . urlencode($current_url));
        exit;
    }
}
if (isset($_POST['eStatus']) && $_POST['eStatus'] = "Delete") {
    try {
        $tpayment_method = PaymentMethod::retrieve($_POST['vCardToken']);
        $tpayment_method->detach();
    } catch (ApiConnectionException $e) {
        // Can't communicate to Stripe API
        $_SESSION['error_msg'] = $failure_msg;
    } catch (Exception $e) {
        // $failure_msg = $e->getMessage();
        // $_SESSION['error_msg'] = $failure_msg;
    }
    $where_payment_info = " iPaymentInfoId = '" . $_POST['iPaymentInfoId'] . "'";
    $data_payment_info['eStatus'] = 'Deleted';
    $obj->MySQLQueryPerform("user_payment_info", $data_payment_info, 'update', $where_payment_info);
    $cardDetailSql = "SELECT * FROM user_payment_info WHERE iPaymentInfoId = " . $_POST['iPaymentInfoId'];
    $cardDetailData = $obj->MySQLSelect($cardDetailSql);
    if ($cardDetailData[0]['eDefault'] == 'Yes') {
        $allCardDetailSql = "SELECT * FROM user_payment_info WHERE iPaymentInfoId != " . $_POST['iPaymentInfoId'] . " AND iMemberId = " . $iUserId . " AND eUserType = '" . $dbUserType . "' AND vPaymentMethod = '" . $USER_APP_PAYMENT_METHOD . "' AND eStatus = 'Active' LIMIT 1";
        $allCardDetailData = $obj->MySQLSelect($allCardDetailSql);
        $where_payment_info = " iPaymentInfoId = '" . $allCardDetailData[0]['iPaymentInfoId'] . "'";
        $data_payment_info1['eDefault'] = 'Yes';
        $obj->MySQLQueryPerform("user_payment_info", $data_payment_info1, 'update', $where_payment_info);
        $where_payment_info = "iPaymentInfoId != '" . $allCardDetailData[0]['iPaymentInfoId'] . "' AND iMemberId = " . $iUserId . " AND eUserType = '" . $dbUserType . "' AND vPaymentMethod = '" . $USER_APP_PAYMENT_METHOD . "'";
        $data_payment_info2['eDefault'] = 'No';
        $obj->MySQLQueryPerform("user_payment_info", $data_payment_info2, 'update', $where_payment_info);
    }
    $_SESSION['success_msg'] = $languageLabelsArr['LBL_DELETE_CARD_SUCCESS_MSG'];
}
if (isset($_POST['set_as_default']) && $_POST['set_as_default'] == 1) {
    $where_payment_info = " iPaymentInfoId = '" . $_POST['default_iPaymentInfoId'] . "'";
    $data_payment_info['eDefault'] = 'Yes';
    $obj->MySQLQueryPerform("user_payment_info", $data_payment_info, 'update', $where_payment_info);
    $where_payment_info = "iPaymentInfoId != '" . $_POST['default_iPaymentInfoId'] . "' AND iMemberId = " . $iUserId . " AND eUserType = '" . $dbUserType . "' AND vPaymentMethod = '" . $USER_APP_PAYMENT_METHOD . "'";
    $data_payment_info['eDefault'] = 'No';
    $obj->MySQLQueryPerform("user_payment_info", $data_payment_info, 'update', $where_payment_info);
    $_SESSION['success_msg'] = $languageLabelsArr['LBL_DEFAULT_CARD_SET_SUCCESS_MSG'];
}
$userPaymentInfoSql = "SELECT * FROM user_payment_info WHERE iMemberId = " . $iUserId . " AND eUserType = '" . $dbUserType . "' AND vPaymentMethod = '" . $USER_APP_PAYMENT_METHOD . "' AND eStatus = 'Active' AND ePaymentEnv = '" . $SYSTEM_PAYMENT_ENVIRONMENT . "' ";
$userPaymentInfoData = $obj->MySQLSelect($userPaymentInfoSql);
// echo $userPaymentInfoSql; exit;
?>
<!DOCTYPE html>
<html>
<head>
    <?php include_once($tconfig['tpanel_path'] . 'assets/libraries/webview/payment_head.php'); ?>
    <script src="https://js.stripe.com/v3/"></script>
</head>
<body class="stripe-pg">
<div class="overlay">
    <div class="overlay__inner">
        <div class="overlay__content">
            <span class="spinner"></span>
            <br>
            <div class="mt-3 px-3" id="loader-msg"></div>
        </div>
    </div>
</div>
<div class="container py-4 custom-scroll-div">
    <?php include_once($tconfig['tpanel_path'] . 'assets/libraries/webview/payment_header.php'); ?>
    <div class="row justify-content-center add-card-form" <?= $header_margin ?>>
        <div class="col-lg-5 col-md-6 col-sm-12 mb-2 p-0">
            <div class="card card-main">
                <div class="card-header amt-header d-flex align-items-center justify-content-between">
                    <div class="float-left"><?= $languageLabelsArr['LBL_PAYABLE_AMOUNT_TXT'] ?></div>
                    <div class="float-right h6 mb-0">
                        <?php
                        if ($AMOUNT < $STRIPE_MINIMUM_AMOUNT) {
                            echo '<span class="currency-symbol">' . $ccode . '</span>' . number_format($STRIPE_MINIMUM_AMOUNT * $currencyData[0]['Ratio'], 2, '.', '') . '<a href="javascript:void(0);" class="info-alert"><img src="' . $tconfig['tsite_url'] . 'assets/img/warning.svg" onclick="scrollToInfo()"></a>';
                        } else {
                            echo '<span class="currency-symbol">' . $ccode . '</span>' . number_format($AMOUNT * $currencyData[0]['Ratio'], 2, '.', '');
                        }
                        ?>
                    </div>
                </div>
                <div class="card-header p-0">
                    <?php
                    $has_card = 0;
                    if (count($userPaymentInfoData) > 0) {
                        $has_card = 1;
                        ?>
                        <ul role="tablist" class="nav bg-light nav-pills rounded nav-fill">
                            <li class="nav-item">
                                <a data-toggle="pill" href="#saved-cards" class="nav-link active"
                                   id="saved_card_tab"><?= $languageLabelsArr['LBL_SAVED_CARD'] ?></a>
                            </li>
                            <li class="nav-item">
                                <a data-toggle="pill" href="#credit-debit-card" class="nav-link"
                                   id="new_card_tab"><?= $languageLabelsArr['LBL_NEW_CARD'] ?></a>
                            </li>
                        </ul>
                    <?php } ?>

                    <div class="tab-content">
                        <?php
                        if (count($userPaymentInfoData) > 0) {
                            $has_card = 1;
                            $count_card = 1;
                            ?>
                            <div id="saved-cards" class="tab-pane show active pt-3">
                                <form action="" method="post" id="saved_card_form">
                                    <div class="list-group card-list saved-cards-list">
                                        <?php foreach ($userPaymentInfoData as $paymentInfoData) { ?>
                                            <div class="list-group-item list-group-item-action">
                                                <div class="d-flex align-items-center card-text">
                                                    <div class="custom-control custom-radio saved-card-checkbox">
                                                        <input type="radio" type="radio" name="saved_card" <?= ($paymentInfoData['eDefault'] == 'Yes') ? 'checked' : '' ?> value="<?= $paymentInfoData['tCardToken'] ?>" class="custom-control-input" id="saved_card_<?= $paymentInfoData['iPaymentInfoId'] ?>">
                                                        <label class="custom-control-label" for="saved_card_<?= $paymentInfoData['iPaymentInfoId'] ?>">
                                                            <span class="card-brand">
                                                                <img src="<?= $tconfig['tsite_url'].'webimages/icons/DefaultImg/ic_'.$paymentInfoData['vCardBrand'].'_system.svg' ?>" class="" onerror="this.src='<?= $tconfig["tsite_url"]."webimages/icons/DefaultImg/ic_card_default.svg" ?>'">
                                                                <span>
                                                                <?= str_replace('X', '*', $paymentInfoData['tCardNum']); ?>
                                                                <?php /*<br>
                                                                <?php if($paymentInfoData['eDefault'] != 'Yes') { ?>
                                                                <button type="button" class="btn btn-secondary btn-sm set-default-card" data-cardId="<?= $paymentInfoData['iPaymentInfoId'] ?>"><?= $languageLabelsArr['LBL_SET_AS_DEFAULT_TXT'] ?></button>
                                                                <?php } else { ?>
                                                                <span class="default-text">
                                                                    <?= $languageLabelsArr['LBL_PRIMARY_TXT'] ?>
                                                                </span>
                                                                <?php } ?>*/ ?>
                                                                </span>
                                                                </span>
                                                            </span>
                                                        </label>
                                                    </div>

                                                    <span>
                                                        <img src="<?= $tconfig['tsite_url'] . 'resizeImg.php?w=28&src=' . $tconfig['tsite_url'] . 'assets/libraries/webview/pg_assets/images/delete.png' ?>" data-toggle="tooltip" title="<?= $languageLabelsArr['LBL_DELETE_CARD_TXT'] ?>" data-cardId="<?= $paymentInfoData['iPaymentInfoId'] ?>" data-cardToken="<?= $paymentInfoData['tCardToken'] ?>" data-cardNo="<?= str_replace('X', '*', $paymentInfoData['tCardNum']); ?>" class="delete-card" />
                                                    </span>
                                                </div>
                                            </div>
                                            <?php $count_card++;
                                        } ?>
                                    </div>
                                    <div class="card-footer">
                                        <?php if ($SYSTEM_TYPE == "WEB") { ?>
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <button type="button" class="btn btn-primary shadow-sm btn-block"
                                                            id="cancel-btn"> <?= $languageLabelsArr['LBL_CANCEL_TXT'] ?> </button>
                                                </div>
                                                <div class="col-sm-6 pl-0">
                                                    <button type="button" id="saved_card_btn"
                                                            class="btn btn-primary btn-block confirm-btn"> <?= $languageLabelsArr['LBL_BTN_CONFIRM_TXT'] ?></button>
                                                </div>
                                            </div>
                                        <?php } else { ?>
                                            <button type="button" id="saved_card_btn"
                                                    class="btn btn-primary btn-block confirm-btn"> <?= $languageLabelsArr['LBL_BTN_CONFIRM_TXT'] ?></button>
                                        <?php } ?>
                                    </div>
                                </form>
                            </div>
                            <?php include($tconfig['tpanel_path'] . 'assets/libraries/webview/delete_card_modal.php'); ?>
                        <?php } ?>
                        <!-- credit card info-->
                        <?php $show_tab = "";
                        if ($has_card == 0) {
                            $show_tab = " show active";
                        } ?>
                        <div id="credit-debit-card" class="tab-pane <?= $show_tab ?>  pt-3">
                            <form id="payment-form" action="" method="POST">
                                <div class="form-group">
                                    <label for="cardholder-name">
                                        <h6 class="mb-0"><?= $languageLabelsArr['LBL_CARD_HOLDER_NAME_TXT'] ?></h6>
                                    </label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="cardholder-name" required=""
                                               id="cardholder-name"
                                               placeholder="<?= $languageLabelsArr['LBL_CARD_HOLDER_NAME_TXT'] ?>"
                                               value="<?= $full_name ?>" onkeypress="return alphabetsOnly(this, event)">
                                    </div>
                                    <small id="card-name-error" class="text-danger mb-2 float-left"></small>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="form-group">
                                    <label for="cardNumber">
                                        <h6 class="mb-0"><?= $languageLabelsArr['LBL_CARD_NUMBER_TXT'] ?></h6>
                                    </label>
                                    <div class="input-group-prepend">
                                        <div id="cardNumber"></div>
                                        <span class="input-group-text card-type">
                                                    <img src="<?= $tconfig['tsite_url'] . 'webimages/icons/DefaultImg/ic_card_default.svg' ?>"/>
                                                </span>
                                    </div>
                                    <small id="card-num-error" class="text-danger mb-2 float-left"></small>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="form-group">
                                    <label for="cardNumber">
                                        <h6 class="mb-0"><?= $languageLabelsArr['LBL_EXPIRY'] . ' (' . $languageLabelsArr['LBL_EXP_MONTH_HINT_TXT'] . ' / ' . $languageLabelsArr['LBL_EXP_YEAR_HINT_SHORT_TXT'] . ')' ?></h6>
                                    </label>
                                    <div class="input-group-prepend">
                                        <div id="cardExpiry" tabindex="3"></div>
                                        <span class="input-group-text card-expiry">
                                            <img src="<?= $tconfig['tsite_url'].'assets/libraries/webview/pg_assets/images/expiry-calendar.png' ?>" />
                                        </span>
                                    </div>
                                    <div class="clearfix"></div>
                                    <small id="card-exp-error" class="text-danger mb-2 float-left"></small>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="form-group">
                                    <label for="cardNumber">
                                        <h6 class="mb-0"><?= $languageLabelsArr['LBL_CVV'] ?>
                                            <span class="cvv-info"
                                                  data-toggle="tooltip"
                                                  title="<?= $languageLabelsArr['LBL_CVV_INFO_TXT'] ?>"><i
                                                        class="fa fa-question-circle d-inline"></i></span>
                                        </h6>
                                    </label>
                                    <div id="cardCvc"></div>
                                    <div class="clearfix"></div>
                                    <small id="card-cvc-error" class="text-danger mb-2 float-left"></small>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="card-footer">
                                    <?php if ($SYSTEM_TYPE == "WEB") { ?>
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <button type="button" class="btn btn-primary shadow-sm btn-block"
                                                        id="cancel-btn"> <?= $languageLabelsArr['LBL_CANCEL_TXT'] ?> </button>
                                            </div>
                                            <div class="col-sm-6 pl-0">
                                                <button type="button" class="btn btn-primary btn-block shadow-sm submit-card-btn confirm-btn"
                                                        id="card-button"> <?= $languageLabelsArr['LBL_BTN_CONFIRM_TXT'] ?> </button>
                                            </div>
                                        </div>
                                    <?php } else { ?>
                                        <button type="button" class="btn btn-primary btn-block confirm-btn"
                                                id="card-button"> <?= $languageLabelsArr['LBL_BTN_CONFIRM_TXT'] ?> </button>
                                    <?php } ?>
                                </div>
                            </form>
                        </div>
                        <!-- End -->
                    </div>
                </div>
            </div>
        </div>
        <?php
        $supported_cards = array('visa', 'mastercard', 'jcb', 'amex', 'discover', 'dinersclub', 'unionpay');
        $display_pg_info = 'style="display: none"';
        if ($AMOUNT < $STRIPE_MINIMUM_AMOUNT) {
            $display_pg_info = "";
        }
        ?>
        <div class="col-lg-5 col-md-6 col-sm-12" id="general_pg_info" <?= $display_pg_info ?>>
            <?php if ($AMOUNT < $STRIPE_MINIMUM_AMOUNT) { ?>
                <div class="row">
                    <div class="col-md-12 mb-2" id="min_amount_info" <?= $display_pg_info ?>>
                        <div class="card ">
                            <div class="card-header bg-white">
                                <p class="card-text">
                                    <strong><?= $languageLabelsArr['LBL_NOTE'] ?>
                                        :
                                    </strong><?= str_replace("####", '<strong><span class="currency-symbol-bottom">' . $ccode . '</span>' . number_format($STRIPE_MINIMUM_AMOUNT * $currencyData[0]['Ratio'], 2, '.', '') . '</strong>', $languageLabelsArr['LBL_PAYMENT_MIN_AMOUNT_INFO']) ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php }
            include $tconfig['tpanel_path'] . 'assets/libraries/webview/supported_cards.php';
            if (strtoupper(SITE_TYPE) == "DEMO") {
                include $tconfig['tpanel_path'] . 'assets/libraries/webview/demo_cards.php';
            }
            ?>
        </div>
        <?php include $tconfig['tpanel_path'] . 'assets/libraries/webview/secure_section.php'; ?>
    </div>
</div>
<script src="<?= $tconfig['tsite_url'] ?>assets/libraries/webview/pg_assets/js/custom.js"></script>
<script type="text/javascript">
    var form_submit = 0;
    // Create a Stripe client.
    var stripe = Stripe('<?= $STRIPE_PUBLISH_KEY ?>');
    // Create an instance of Elements.
    var elements = stripe.elements();
    // Custom styling can be passed to options when creating an Element.
    // (Note that this demo uses a wider set of styles than the guide below.)
    var style = {
        base: {
            color: '#495057',
            backgroundColor: '#ffffff',
            fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
            fontSmoothing: 'antialiased',
            fontSize: '16px',
            padding: '.375rem .75rem',
            '::placeholder': {
                color: '#6c757d'
            },
            lineHeight: '1.5'
        },
        invalid: {
            color: '#fa755a',
            iconColor: '#fa755a'
        }
    };
    // Add an instance of the card Element into the `card-element` <div>.
    var cardNumberElement = elements.create('cardNumber', {style: style, placeholder: 'XXXX XXXX XXXX XXXX'});
    cardNumberElement.mount('#cardNumber');
    var cardExpiryElement = elements.create('cardExpiry', {
        style: style,
        placeholder: '<?= addslashes($languageLabelsArr['LBL_EXP_MONTH_HINT_TXT']) . ' / ' . addslashes($languageLabelsArr['LBL_EXP_YEAR_HINT_SHORT_TXT']) ?>'
    });
    cardExpiryElement.mount('#cardExpiry');
    var cardCvcElement = elements.create('cardCvc', {style: style, placeholder: 'XXX'});
    cardCvcElement.mount('#cardCvc');
    cardNumberElement.addEventListener('change', function (event) {
        if (event.error) {
            if (event.empty == true) {
                $('#card-num-error').text("<?= addslashes($languageLabelsArr['LBL_REQUIRED']) ?>");
            } else {
                $('#card-num-error').text("<?= addslashes($languageLabelsArr['LBL_INVALIED']) ?>");
            }
        } else {
            $('#card-num-error').text('');
        }
        var cardType = event.brand;
        if (cardType == "unknown" || cardType == undefined) {
            $('.card-type').find('img').attr('src', '<?= $tconfig['tsite_url'] . 'webimages/icons/DefaultImg/ic_card_default.svg' ?>');
        } else {
            $('.card-type').find('img').attr('src', '<?= $tconfig['tsite_url'] . 'webimages/icons/DefaultImg/ic_'?>' + cardType + '_system.svg');
        }
    });
    cardExpiryElement.addEventListener('change', function (event) {
        if (event.error) {
            if (event.empty == true) {
                $('#card-exp-error').text("<?= addslashes($languageLabelsArr['LBL_REQUIRED']) ?>");
            } else {
                $('#card-exp-error').text("<?= addslashes($languageLabelsArr['LBL_INVALIED']) ?>");
            }
        } else {
            $('#card-exp-error').text('');
        }
    });
    cardCvcElement.addEventListener('change', function (event) {
        if (event.error) {
            if (event.empty == true) {
                $('#card-cvc-error').text("<?= addslashes($languageLabelsArr['LBL_REQUIRED']) ?>");
            } else {
                $('#card-cvc-error').text("<?= addslashes($languageLabelsArr['LBL_INVALIED']) ?>");
            }
        } else {
            $('#card-cvc-error').text('');
        }
    });
    $('#cardholder-name').on('change, keyup', function () {
        if ($(this).val().length > 0) {
            $('#card-name-error').text("");
        }
    });
    $('#cancel-btn').click(function () {
        showOverlay();
        window.location.href = "<?= $cancelUrl . '&status=failure' ?>";
    });
    var cardButton = document.getElementById('card-button');
    var clientSecret = '<?= $intent->client_secret ?>';
    cardButton.addEventListener('click', function (ev) {
        showOverlay();
        if ($('#cardholder-name').val() == "") {
            $('#card-name-error').text("<?= addslashes($languageLabelsArr['LBL_REQUIRED']) ?>");
        } else {
            $('#card-name-error').text('');
        }
        $('#loader-msg').html('<?= addslashes($languageLabelsArr['LBL_TRANSACTION_INPROCESS_MSG']) ?>');
        $('#cardholder-name').prop('readonly', true);
        $('#card-button, #cancel-btn').prop('disabled', true);
        $('#saved_card_tab').addClass('disabled');
        form_submit = 1;
        var cardholderName = document.getElementById('cardholder-name');
        cardNumberElement.update({disabled: true});
        cardExpiryElement.update({disabled: true});
        cardCvcElement.update({disabled: true});
        stripe.confirmCardSetup(
            clientSecret,
            {
                payment_method: {
                    card: cardNumberElement,
                    billing_details: {name: cardholderName.value, email: '<?= $userData[0]["vEmail"] ?>'},
                }
            }
        ).then(function (result) {
            if ("error" in result) {
                hideOverlay();
                // Display error.message in your UI.
                cardNumberElement.update({disabled: false});
                cardExpiryElement.update({disabled: false});
                cardCvcElement.update({disabled: false});
                $('#card-button').find('i').remove();
                $('#cardholder-name').prop('readonly', false);
                $('#card-button, #cancel-btn').prop('disabled', false);
                $('#card-button').find('i').remove();
                $('#saved_card_tab').removeClass('disabled');
                form_submit = 0;
                showSnackbar(result.error.message);
                return false;
            } else {
                if (result.setupIntent.status === 'succeeded') {
                    // displayError.textContent = '';
                    // The setup has succeeded. Display a success message.
                    var form = document.getElementById('payment-form');
                    var hiddenInput = document.createElement('input');
                    hiddenInput.setAttribute('type', 'hidden');
                    hiddenInput.setAttribute('name', 'payMethod');
                    hiddenInput.setAttribute('value', result.setupIntent.payment_method);
                    form.appendChild(hiddenInput);
                    form.submit();
                } else {
                    // displayError.textContent = result.setupIntent.status;
                    $('#card-num-error').text(result.setupIntent.status);
                    hideOverlay();
                }
            }
        });
    });
    $('#saved_card_btn').click(function () {
        $('#loader-msg').html('<?= addslashes($languageLabelsArr['LBL_TRANSACTION_INPROCESS_MSG']) ?>');
        showOverlay();
        $(this).prop('disabled', true);
        $('#new_card_tab').addClass('disabled');
        form_submit = 1;
        $('#saved_card_form').submit();
    });
    $('#saved_card_tab, #new_card_tab').click(function () {
        if ($(this).hasClass('disabled')) {
            alert("Please wait. We are processing your previous request.");
            return false;
        }
        if ($(this).attr('id') == "new_card_tab") {
            $('#accepted-cards, #general_pg_info, #demo-cards').show();
        } else {
            $('#accepted-cards, #general_pg_info, #demo-cards').hide();
            <?php if($AMOUNT < $STRIPE_MINIMUM_AMOUNT) { ?>
            $('#general_pg_info, #min_amount_info').show();
            <?php } ?>
        }
    });
</script>
</body>
</html>
