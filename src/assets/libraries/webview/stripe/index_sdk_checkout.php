<?php
include_once('../../../../common.php');
require_once('init.php');

    use Stripe\SetupIntent;
    use Stripe\Stripe;

    Stripe::setApiKey($STRIPE_SECRET_KEY);
Stripe::setMaxNetworkRetries(5);

// ini_set('display_errors', 1);
// error_reporting(E_ALL);

$generalConfigPaymentArr = $CONFIG_OBJ->getGeneralVarAll_Payment_Array();
$STRIPE_SECRET_KEY = $generalConfigPaymentArr['STRIPE_SECRET_KEY'];
$STRIPE_PUBLISH_KEY = $generalConfigPaymentArr['STRIPE_PUBLISH_KEY'];

$stripe_system_payment_flow = explode(',', $STRIPE_SYSTEM_PAYMENT_FLOW);

$default_system_payment_flow = false;
if(in_array($SYSTEM_PAYMENT_FLOW, $stripe_system_payment_flow))
{
    $default_system_payment_flow = true;
}

$statusMsg = "Transaction has been failed";
$UserType = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : $_REQUEST["GeneralUserType"];
$iUserId = isset($_REQUEST["iUserId"]) ? $_REQUEST["iUserId"] : $_REQUEST["GeneralMemberId"];
$iServiceId = isset($_REQUEST["iServiceId"]) ? $_REQUEST["iServiceId"] : '';
$SYSTEM_TYPE = isset($_REQUEST["SYSTEM_TYPE"]) ? $_REQUEST["SYSTEM_TYPE"] : 'APP';

$fromOrder = "guest";
if (isset($_REQUEST['order']) && $_REQUEST['order'] != "") {
    $fromOrder = $_REQUEST['order'];
}

if ($UserType == "Rider" || $UserType == "Passenger") {
    $dbUserType = "Passenger";
    $sql = "SELECT iUserId,eStatus,vLang,vCurrencyPassenger as userCurrency,vStripeCusId,vEmail,vCountry FROM `register_user` WHERE iUserId='" . $iUserId . "'";
    $dbField = "iUserId";
    $tblname = "register_user";
} else {
    $dbUserType = "Driver";
    $sql = "SELECT iDriverId,eStatus,vLang,vCurrencyDriver as userCurrency,vStripeCusId,vEmail,vCountry FROM `register_driver` WHERE iDriverId='" . $iUserId . "'";
    $dbField = "iDriverId";
    $tblname = "register_driver";
}

$userData = $obj->MySQLSelect($sql);

$vLang = $userData[0]['vLang'];
if ($vLang == "") {
    $lang_data = $obj->MySQLSelect("SELECT vCode FROM language_master WHERE eDefault = 'Yes'");
    $vLang = $lang_data[0]['vCode'];
}
$languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLang, "1", $iServiceId);

$vCountry = $userData[0]['vCountry'];

$USER_APP_PAYMENT_METHOD = "Stripe";


$failure_url = $tconfig['tsite_url'] . "assets/libraries/webview/failure.php";
$failure_msg = $languageLabelsArr['LBL_SERVER_COMM_ERROR'];

try{
    $intent = SetupIntent::create();    
} catch (\Stripe\Exception\ApiConnectionException $e) {
    header('Location:'. $failure_url."?success=0&message=".$failure_msg."&SYSTEM_TYPE=".$SYSTEM_TYPE);
    exit;
} catch(Exception $e) {
    $failure_msg = $e->getMessage();
    header('Location:'. $failure_url."?success=0&message=".$failure_msg."&SYSTEM_TYPE=".$SYSTEM_TYPE);
}


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

$stripe_return_url = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
if($page_type == "WALLET_MONEY_ADD")
{
    $description = "Added money to wallet";
}
$currencySql = "SELECT * FROM currency WHERE vName = '".$userData[0]['userCurrency']."'";
$currencyData = $obj->MySQLSelect($currencySql);
$ccode = $currencyData[0]['vSymbol'];

$defCurrencySql = "SELECT * FROM currency WHERE eDefault = 'Yes'";
$defCurrencyData = $obj->MySQLSelect($defCurrencySql);

$status = "failed";
$custId = $userData[0]['vStripeCusId'];
/*if($page_type == "RIDE_TIP_COLLECT" && $_REQUEST['eForTip'] == "Yes")
{
    if($CheckUserWallet == "Yes")
    {
        $user_available_balance_new = round($user_available_balance, 2);
        $user_wallet_debit_amount = $user_available_balance_new;
        $price_new = $price_new - $user_available_balance_new;
    }
}*/

if (!empty($_REQUEST['payMethod'])) {
    $token = isset($_POST["stripeToken"]) ? $_POST["stripeToken"] : '';
    $vStripeCusId = isset($_POST["stripeCustid"]) ? $_POST["stripeCustid"] : '';
    $name = isset($_POST["name"]) ? $_POST["name"] : '';

    $email = $userData[0]['vEmail'];
    $card_num = isset($_POST["card_num"]) ? $_POST["card_num"] : '';
    $card_cvc = isset($_POST["cvc"]) ? $_POST["cvc"] : '';
    $card_exp_month = isset($_POST["exp_month"]) ? $_POST["exp_month"] : '';
    $card_exp_year = isset($_POST["exp_year"]) ? $_POST["exp_year"] : '';
    $DefaultCurrencyData = $obj->MySQLSelect("SELECT vName,Ratio FROM currency WHERE vName='" . $currency . "'");
    $currencyratio = 1;
    $currencyCode = $currency;
    if (count($DefaultCurrencyData) > 0) {
        $currencyCode = $DefaultCurrencyData[0]['vName'];
        $currencyratio = $DefaultCurrencyData[0]['Ratio'];
    }

    $paymentMethod = $_REQUEST['payMethod'];

    if($userData[0]['vStripeCusId'] != "") {
        
        $payment_method = \Stripe\PaymentMethod::retrieve($paymentMethod);
        $payment_method->attach(['customer' => $custId]);

    } else {
        // Add payment method/Card to customer
        try{
            $datae = \Stripe\Customer::create(['payment_method' => $paymentMethod]);
            $custId = $datae->id;

            $data_member['vStripeCusId'] = $custId;

            if ($UserType == "Rider" || $UserType == "Passenger") {
                $where = " iUserId = '$iUserId'";
                $updateData = $obj->MySQLQueryPerform("register_user", $data_member, 'update', $where);
            } else {
                $where = " iDriverId = '$iUserId'";
                $updateData = $obj->MySQLQueryPerform("register_driver", $data_member, 'update', $where);
            }
        }
        catch(Exception $e)
        {
            $failure_msg = $e->getMessage();
            header('Location:'. $failure_url."?success=0&message=".$failure_msg."&SYSTEM_TYPE=".$SYSTEM_TYPE);
            exit;
        }
    }

    // Create charge
    $iMemberId = $iUserId;
    $paymentData = array(
        "amount"               => $AMOUNT,
        "description"          => $description,
        "iMemberId"            => $iMemberId,
        "UserType"             => $dbUserType,
        "tCardToken"           => $paymentMethod,
        "return_url"           => $stripe_return_url
    );

    $result = (PaymentGateways::getInstance())->execute($paymentData);

    if ($result['Action'] == "1") 
    {
        $data_member['vStripeCusId'] = $custId;

        $where_user = "$dbField = '" . $iUserId . "'";
        $obj->MySQLQueryPerform($tblname, $data_member, 'update', $where_user);

        $sql = "SELECT * FROM user_payment_info WHERE tCardToken = '".$paymentMethod."'";
        $sqlData = $obj->MySQLSelect($sql);

        $paymentInfoSql = "SELECT * FROM user_payment_info WHERE iMemberId = ".$iUserId." AND eUserType = '".$dbUserType."' AND vPaymentMethod = '".$USER_APP_PAYMENT_METHOD."' AND eStatus = 'Active' AND eDefault = 'Yes'";
        $paymentInfoData = $obj->MySQLSelect($paymentInfoSql);
        
        // Insert in User payment info
        $user_payment_info_insert['eDefault'] = 'No';
        if(count($paymentInfoData) == 0)
        {
            $user_payment_info_insert['eDefault'] = 'Yes';    
        }
        $user_payment_info_insert['eUserType'] = $dbUserType;
        $user_payment_info_insert['tCardToken'] = $paymentMethod;
        $user_payment_info_insert['tCardNum'] = 'XXXX XXXX XXXX '.$result['last4digits'];
        $user_payment_info_insert['vCardBrand'] = strtolower($result['vCardBrand']);

        $payment_arr['CARD_TOKEN'] = $paymentMethod;
        $payment_id = $result['payment_id'];

        // Update details in db
        include $tconfig['tpanel_path'].'assets/libraries/webview/capture-payment-details.php';
        exit;
    }
    else {
        $returnArr = $result;
        $failure_msg = $result['message'];
        if(isset($result['status']) && $result['status'] == "failed")
        {
            $failure_msg = $languageLabelsArr['LBL_CHARGE_COLLECT_FAILED'];
        }

        header('Location:'. $failure_url."?success=0&message=".$failure_msg."&SYSTEM_TYPE=".$SYSTEM_TYPE);
            exit;
    }
}

if($payment_intent != "" && $payment_intent_client_secret != "")
{
    $payment_intent_data = \Stripe\PaymentIntent::retrieve($payment_intent, [
        'client_secret' => $payment_intent_client_secret
    ]);

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
        $responseArr['amount'] = $AMOUNT;

        $payment_id = (PaymentGateways::getInstance())->paymentDetailsInsert($responseArr);

        $sql = "SELECT * FROM user_payment_info WHERE tCardToken = '".$responseArr['tCardToken']."'";
        $sqlData = $obj->MySQLSelect($sql);

        if(!isset($_REQUEST['saved_card']))
        {
            $data_member['vStripeCusId'] = $custId;

            $where_user = "$dbField = '" . $iUserId . "'";
            $obj->MySQLQueryPerform($tblname, $data_member, 'update', $where_user);

            $paymentInfoSql = "SELECT * FROM user_payment_info WHERE iMemberId = ".$iUserId." AND eUserType = '".$dbUserType."' AND vPaymentMethod = '".$USER_APP_PAYMENT_METHOD."' AND eStatus = 'Active' AND eDefault = 'Yes'";
            $paymentInfoData = $obj->MySQLSelect($paymentInfoSql);
            
            // Insert in User payment info
            $user_payment_info_insert['eDefault'] = 'No';
            if(count($paymentInfoData) == 0)
            {
                $user_payment_info_insert['eDefault'] = 'Yes';    
            }
            $user_payment_info_insert['eUserType'] = $dbUserType;
            $user_payment_info_insert['tCardToken'] = $responseArr['tCardToken'];
            $user_payment_info_insert['tCardNum'] = 'XXXX XXXX XXXX '.$responseArr['last4digits'];
            $user_payment_info_insert['vCardBrand'] = strtolower($payment_intent_data['vCardBrand']);
        }
        

        $payment_arr['CARD_TOKEN'] = $responseArr['tCardToken'];

        // Update details in db
        include $tconfig['tpanel_path'].'assets/libraries/webview/capture-payment-details.php';
        exit;
    } else {
        
        // $payment_intent_data = $payment_intent_data->jsonSerialize();
        $failure_msg = $payment_intent_data['last_payment_error']['message'];
        header('Location:'. $failure_url."?success=0&message=".$failure_msg."&SYSTEM_TYPE=".$SYSTEM_TYPE);
        exit;
    }
    
}

if(isset($_POST['saved_card']) && $_POST['saved_card'] != "")
{
    $custId = $userData[0]['vStripeCusId'];
    $paymentMethod = $_POST['saved_card'];
    // Create charge
    $iMemberId = $iUserId;
    $paymentData = array(
        "amount"               => $AMOUNT,
        "description"          => $description,
        "iMemberId"            => $iMemberId,
        "UserType"             => $dbUserType,
        "tCardToken"           => $paymentMethod,
        "return_url"           => $stripe_return_url."&saved_card=1"
    );

    $result = (PaymentGateways::getInstance())->execute($paymentData);

    if ($result['Action'] == "1") 
    {

        $sql = "SELECT * FROM user_payment_info WHERE tCardToken = '".$paymentMethod."'";
        $sqlData = $obj->MySQLSelect($sql);

        $payment_arr['CARD_TOKEN'] = $paymentMethod;
        $payment_id = $result['payment_id'];

        // Update details in db
        include $tconfig['tpanel_path'].'assets/libraries/webview/capture-payment-details.php';
        exit;
    }
    else {
        $returnArr = $result;
        $failure_msg = $result['message'];
        if(isset($result['status']) && $result['status'] == "failed")
        {
            $failure_msg = $languageLabelsArr['LBL_CHARGE_COLLECT_FAILED'];
        }

        header('Location:'. $failure_url."?success=0&message=".$failure_msg."&SYSTEM_TYPE=".$SYSTEM_TYPE);
            exit;
    }
}


if(isset($_POST['eStatus']) && $_POST['eStatus'] = "Delete")
{
    try{
        $tpayment_method = \Stripe\PaymentMethod::retrieve(
            $_POST['vCardToken']
        );
        $tpayment_method->detach();

        $where_payment_info = " iPaymentInfoId = '" . $_POST['iPaymentInfoId'] . "'";
        $data_payment_info['eStatus'] = 'Deleted';
        $obj->MySQLQueryPerform("user_payment_info", $data_payment_info, 'update', $where_payment_info);

        $cardDetailSql = "SELECT * FROM user_payment_info WHERE iPaymentInfoId = ".$_POST['iPaymentInfoId'];
        $cardDetailData = $obj->MySQLSelect($cardDetailSql);

        if($cardDetailData[0]['eDefault'] == 'Yes')
        {
            $allCardDetailSql = "SELECT * FROM user_payment_info WHERE iPaymentInfoId != ".$_POST['iPaymentInfoId']." AND iMemberId = ".$iUserId." AND eUserType = '".$dbUserType."' AND vPaymentMethod = '".$USER_APP_PAYMENT_METHOD."' AND eStatus = 'Active' LIMIT 1";
            $allCardDetailData = $obj->MySQLSelect($allCardDetailSql);

            $where_payment_info = " iPaymentInfoId = '" . $allCardDetailData[0]['iPaymentInfoId'] . "'";
            $data_payment_info1['eDefault'] = 'Yes';
            $obj->MySQLQueryPerform("user_payment_info", $data_payment_info1, 'update', $where_payment_info);

            $where_payment_info = "iPaymentInfoId != '" . $allCardDetailData[0]['iPaymentInfoId'] . "' AND iMemberId = ".$iUserId." AND eUserType = '".$dbUserType."' AND vPaymentMethod = '".$USER_APP_PAYMENT_METHOD."'";
            $data_payment_info2['eDefault'] = 'No';
            $obj->MySQLQueryPerform("user_payment_info", $data_payment_info2, 'update', $where_payment_info);
        }

        $_SESSION['success_msg'] = "Card deleted successfully.";
    }
    catch (\Stripe\Exception\ApiConnectionException $e) {
        // Can't communicate to Stripe API
        $_SESSION['error_msg'] = $failure_msg;
    }
    catch(Exception $e)
    {
        $failure_msg = $e->getMessage();
        $_SESSION['error_msg'] = $failure_msg;
    }
}


if(isset($_POST['set_as_default']) && $_POST['set_as_default'] == 1)
{
    $where_payment_info = " iPaymentInfoId = '" . $_POST['default_iPaymentInfoId'] . "'";
    $data_payment_info['eDefault'] = 'Yes';
    $obj->MySQLQueryPerform("user_payment_info", $data_payment_info, 'update', $where_payment_info);

    $where_payment_info = "iPaymentInfoId != '" . $_POST['default_iPaymentInfoId'] . "' AND iMemberId = ".$iUserId." AND eUserType = '".$dbUserType."' AND vPaymentMethod = '".$USER_APP_PAYMENT_METHOD."'";
    $data_payment_info['eDefault'] = 'No';
    $obj->MySQLQueryPerform("user_payment_info", $data_payment_info, 'update', $where_payment_info);

    $_SESSION['success_msg'] = "Default card set successfully.";
}


$userPaymentInfoSql = "SELECT * FROM user_payment_info WHERE iMemberId = ".$iUserId." AND eUserType = '".$dbUserType."' AND vPaymentMethod = '".$USER_APP_PAYMENT_METHOD."' AND eStatus = 'Active'";
$userPaymentInfoData = $obj->MySQLSelect($userPaymentInfoSql);
// echo $userPaymentInfoSql; exit;

?>
<html>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <title>Stripe <?= $languageLabelsArr['LBL_PAYMENT'] ?></title>
    
    <link rel="stylesheet" href='https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css'>
    <link rel="stylesheet" href='https://use.fontawesome.com/releases/v5.8.1/css/all.css'>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <script src="js/popper.min.js" type="text/javascript"></script>
    <script src='https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js'></script>
    <script src="https://js.stripe.com/v3/"></script>
    <style type="text/css">
        .card-text-input:nth-child(<?= count($userPaymentInfoData) ?>) {
            margin-bottom: 1rem;
            border-bottom: none;
        }
    </style>
    <body>
        <?php if($default_system_payment_flow == true) { ?>
        <div class="overlay">
            <div class="overlay__inner">
                <div class="overlay__content"><span class="spinner"></span></div>
            </div>
        </div>
        <div class="container py-3 ">
            <!-- For demo purpose -->
            <div class="row mb-4">
                <div class="col-lg-8 mx-auto text-center">
                    <h1 class="display-4"> <?= $languageLabelsArr['LBL_PAYMENT'] ?> </h1>
                </div>
            </div>
            <!-- End -->
            <div class="row justify-content-center">
                <div class="col-lg-5 col-md-6 col-sm-12 mb-4">
                    <div class="card card-main">
                        <div class="card-header amt-header d-flex align-items-center justify-content-between">
                            <div class="float-left"><?= $languageLabelsArr['LBL_PAYABLE_AMOUNT_TXT'] ?></div>
                            <div class="float-right h3 mb-0">
                                <?php if($page_type == "WALLET_MONEY_ADD") { ?>
                                    <?= $ccode.' '.ROUND($AMOUNT, 2) ?>
                                <?php } else { ?>
                                    <?= $ccode.' '.ROUND(($AMOUNT * $currencyData[0]['Ratio']), 2) ?>
                                <?php } ?>
                                </div>
                        </div>

                        <div class="card-header">
                            <?php
                                $has_card = 0;
                                if (count($userPaymentInfoData) > 0) {
                                    
                                    $has_card = 1;
                            ?>
                            <div class="bg-white shadow-sm pt-4 pl-2 pr-2 pb-2">
                                <!-- Credit card form tabs -->
                                <ul role="tablist" class="nav bg-light nav-pills rounded nav-fill mb-3">
                                    <li class="nav-item"> 
                                        <a data-toggle="pill" href="#saved-cards" class="nav-link active" id="saved_card_tab">    <i class="fas fa-credit-card mr-2"></i> Saved Card
                                        </a> 
                                    </li>
                                    <li class="nav-item"> 
                                        <a data-toggle="pill" href="#credit-debit-card" class="nav-link  " id="new_card_tab"> 
                                            <i class="fas fa-credit-card mr-2"></i> Use New Card 
                                        </a> 
                                    </li>
                                </ul>
                            </div>
                            <!-- End -->
                            <?php } ?>
                            

                            <?php if(isset($_SESSION['error_msg']) && $_SESSION['error_msg'] != "") { ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="margin: 1rem 0 0 0">
                                <span class="small"><?= $_SESSION['error_msg'] ?></span>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="padding: 9px;">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <?php $_SESSION['error_msg'] = ""; } ?>

                            <?php if(isset($_SESSION['success_msg']) && $_SESSION['success_msg'] != "") { ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert" style="margin: 1rem 0 0 0;">
                                <span class="small"><?= $_SESSION['success_msg'] ?></span>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="padding: 9px;">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <?php $_SESSION['success_msg'] = ""; } ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="margin: 1rem 0 0 0; display: none;" id="api_error">
                                <span class="small" id="api_error_msg"></span>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="padding: 9px;">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="tab-content">
                                <?php
                                    if (count($userPaymentInfoData) > 0) {
                                        $has_card = 1;
                                        $count_card = 1;
                                ?>
                                <!-- Paypal info -->
                                <div id="saved-cards" class="tab-pane fade show active pt-3">
                                    <h6 class="pb-2 saved-cards-title">Select your saved card</h6>
                                    <form action="" method="post" id="saved_card_form">
                                        <div class="all-saved-cards">
                                            <?php foreach ($userPaymentInfoData as $paymentInfoData) { ?>
                                            <div class="card-text-input">
                                                <div class="form-group"> 
                                                    <label class="radio-inline" style="letter-spacing: 2px; width: calc(100% - 30px)"> 
                                                        <input type="radio" name="saved_card" <?= ($paymentInfoData['eDefault'] == 'Yes') ? 'checked' : '' ?> value="<?= $paymentInfoData['tCardToken'] ?>"> <?= $paymentInfoData['tCardNum']; ?>
                                                    </label>
                                                    <img src="<?= $tconfig['tsite_url'] ?>webimages/icons/DefaultImg/delete.svg" data-toggle="tooltip" title="Delete Card" data-cardId="<?= $paymentInfoData['iPaymentInfoId'] ?>" data-cardToken="<?= $paymentInfoData['tCardToken'] ?>" data-cardNo="<?= $paymentInfoData['tCardNum'] ?>" class="delete-card" />
                                                    <?php if($paymentInfoData['eDefault'] != 'Yes') { ?>
                                                    <button type="button" class="btn btn-secondary btn-sm ml-4 set-default-card" data-cardId="<?= $paymentInfoData['iPaymentInfoId'] ?>"><?= $languageLabelsArr['LBL_SET_AS_DEFAULT_TXT'] ?></button>
                                                    <?php } else { ?>
                                                    <span class="default-text ml-4 pt-1 pb-1 pl-3 pr-3">
                                                        <?= $languageLabelsArr['LBL_DEFAULT_TXT'] ?>
                                                    </span>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                            <?php $count_card++; } ?>
                                        </div>
                                        <div class="card-footer"> 
                                            <button type="button" id="saved_card_btn" class="btn btn-primary btn-block shadow-sm"> <?= $languageLabelsArr['LBL_BTN_CONFIRM_TXT'].' '.$languageLabelsArr['LBL_PAYMENT'] ?></button>
                                        </div>
                                    </form>
                                </div>

                                <?php include ($tconfig['tpanel_path'].'assets/libraries/webview/delete_card_modal.php'); ?>
                                
                                <!-- End -->
                                <?php } ?>
                                <!-- credit card info-->
                                <?php $show_tab = ""; if($has_card == 0) { $show_tab = " show active"; } ?>
                                <div id="credit-debit-card" class="tab-pane fade <?= $show_tab ?>  pt-3">
                                    <form id="payment-form" action="" method="POST">
                                        <div class="form-group">
                                            <label for="cardholder-name">
                                                <h6><?= $languageLabelsArr['LBL_CARD_HOLDER_NAME_TXT'] ?></h6>
                                            </label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fa fa-user"></i></span>
                                                </div>
                                                <input type="text" class="form-control" name="cardholder-name" required="" id="cardholder-name" placeholder="<?= $languageLabelsArr['LBL_CARD_HOLDER_NAME_TXT'] ?>">
                                            </div>
                                            <small id="card-name-error" class="text-danger mb-2 float-left"></small>
                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="form-group">
                                            <label for="cardNumber">
                                                <h6><?= $languageLabelsArr['LBL_CARD_NUMBER_TXT'] ?></h6>
                                            </label>

                                            <div class="input-group-prepend">
                                                <span class="input-group-text card-type">
                                                    <img src="<?= $tconfig['tsite_url'].'webimages/icons/DefaultImg/ic_card_default.svg' ?>" />
                                                </span>
                                                <div id="cardNumber"></div>
                                            </div>
                                            <small id="card-num-error" class="text-danger mb-2 float-left"></small>
                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-7">
                                                <div class="form-group">
                                                    <label>
                                                        <span class="hidden-xs">
                                                            <h6><?= $languageLabelsArr['LBL_EXPIRATION_DATE_TXT'] ?></h6>
                                                        </span>
                                                    </label>
                                                    <div id="cardExpiry"></div>
                                                    <div class="clearfix"></div>
                                                    <small id="card-exp-error" class="text-danger mb-2 float-left"></small>
                                                    <div class="clearfix"></div>
                                                </div>
                                            </div>
                                            <div class="col-sm-5">
                                                <div class="form-group mb-4">
                                                    <label >
                                                        <h6><?= $languageLabelsArr['LBL_CVV'] ?> <i class="fa fa-question-circle d-inline" data-toggle="tooltip" title="Three digit CV code on the back of your card"></i></h6>
                                                    </label>
                                                    <div id="cardCvc"></div>
                                                    <div class="clearfix"></div>
                                                    <small id="card-cvc-error" class="text-danger mb-2 float-left"></small>
                                                    <div class="clearfix"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer">
                                            <?php if($SYSTEM_TYPE == "WEB") { ?>
                                            <div class="row">
                                                <div class="col-sm-6">
                                                    <button type="button" class="btn btn-primary shadow-sm btn-block" id="cancel-btn"> <?= $languageLabelsArr['LBL_CANCEL_TXT'] ?> </button>
                                                </div>
                                                <div class="col-sm-6 pl-0">
                                                    <button type="button" class="btn btn-primary btn-block shadow-sm" id="card-button"> <?= $languageLabelsArr['LBL_BTN_CONFIRM_TXT'].' '.$languageLabelsArr['LBL_PAYMENT'] ?> </button>
                                                </div>
                                            </div>
                                            <?php } else { ?>
                                                <button type="button" class="btn btn-primary btn-block shadow-sm" id="card-button"> <?= $languageLabelsArr['LBL_BTN_CONFIRM_TXT'].' '.$languageLabelsArr['LBL_PAYMENT'] ?> </button>
                                            <?php } ?>
                                        </div>
                                    </form>
                                </div>
                                <!-- End -->
                            </div>
                        </div>
                    </div>
                </div>

                <?php $supported_cards = array('visa','mastercard','jcb','amex','discover','dinersclub','unionpay'); ?>
                <div class="col-lg-5 col-md-6 col-sm-12">
                    <div class="row">
                        <div class="col-md-12 mb-4">
                            <div class="card ">
                                <div class="card-header bg-white">
                                    <p class="card-text"><strong><?= $languageLabelsArr['LBL_NOTE'] ?>: </strong>The minimum amount accpeted by payment processor is <strong><?= $ccode.' '.number_format($STRIPE_MINIMUM_AMOUNT * $currencyData[0]['Ratio'], 2, '.', '') ?></strong>. So the minimum amount will be deducted from your card.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 d-none-alt" id="accepted-cards">
                            <div class="card ">
                                <div class="card-header bg-white">
                                    <h5 class="card-title"><?= ucwords(strtolower($languageLabelsArr['LBL_MANUAL_STORE_CREDIT_CARDS_WE_ACCEPT'])).':'; ?></h5>
                                    
                                    <ul class="accepted-cards">
                                        <?php foreach ($supported_cards as $supported_card) { ?>
                                            <li>
                                                <img src="<?= $tconfig['tsite_url'].'/webimages/icons/DefaultImg/ic_'.$supported_card.'_system.svg' ?>">
                                            </li>
                                        <?php } ?>
                                    </ul>

                                    <p class="card-text"><strong><?= $languageLabelsArr['LBL_NOTE'] ?>: </strong><?= $languageLabelsArr['LBL_SUPPORTED_CARDS_NOTE'] ?></p>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>

        <?php $failure_msg = $languageLabelsArr['LBL_REQUEST_FAILED_PROCESS']; if($default_system_payment_flow == false) {  ?>
        <div class="container" style="margin-top: calc((100vh - 209px) / 2);">
            <div class="row justify-content-center">
                <div class="col-lg-5 col-md-6 col-sm-12 mx-auto">
                    <div class="card ">
                        <div class="card-header bg-warning">
                            <div class="float-left"><?= $languageLabelsArr['LBL_SYSTEM_CONF_WARN_TITLE'] ?></div>
                        </div>
                        <div class="card-body">
                            <p class="text-danger"><?= $languageLabelsArr['LBL_SYSTEM_CONF_WARN_DESC'] ?></p>

                            <div class="col-md-3 text-center mt-4 mb-2 mx-auto">
                                <button type="button" class="btn btn-warning btn-block" onclick="window.location.href = '<?= $tconfig['tsite_url']."assets/libraries/webview/failure.php?success=0&message=".urlencode($failure_msg) ?>';"><?= strtoupper($languageLabelsArr['LBL_OK']) ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php } ?>
        <script type="text/javascript">
            $(function () {
                $('[data-toggle="tooltip"]').tooltip();

                $('[data-toggle="tooltip"]').click(function () {
                    $('[data-toggle="tooltip"]').tooltip("hide");
                });

                if($(".alert").css('display') != "none")
                {
                    setTimeout(function() {
                        $(".alert").alert('close');
                    }, 10000);
                }
            });
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

            // Create an instance of the card Element.
            // var card = elements.create('card', {style: style});

            // Add an instance of the card Element into the `card-element` <div>.
            var cardNumberElement = elements.create('cardNumber', {style: style, placeholder: '4111 1111 1111 1111'});
            cardNumberElement.mount('#cardNumber');

            var cardExpiryElement = elements.create('cardExpiry', {style: style, placeholder: '<?= $languageLabelsArr['LBL_EXP_MONTH_HINT_TXT'].' / '.$languageLabelsArr['LBL_EXP_YEAR_HINT_TXT'] ?>'});
            cardExpiryElement.mount('#cardExpiry');

            var cardCvcElement = elements.create('cardCvc', {style: style, placeholder: 'XXX'});
            cardCvcElement.mount('#cardCvc');


            cardNumberElement.addEventListener('change', function(event) {
              
                if (event.error) {
                    if(event.empty == true)
                    {
                        $('#card-num-error').text("<?= $languageLabelsArr['LBL_REQUIRED'] ?>");
                    }
                    else{
                        $('#card-num-error').text("<?= $languageLabelsArr['LBL_INVALIED'] ?>");
                    }
                } else {
                     $('#card-num-error').text('');
                }

                var cardType = event.brand;
                if(cardType == "unknown" || cardType == undefined)
                {
                    $('.card-type').find('img').attr('src', '<?= $tconfig['tsite_url'].'webimages/icons/DefaultImg/ic_card_default.svg' ?>');
                }
                else{
                    $('.card-type').find('img').attr('src', '<?= $tconfig['tsite_url'].'webimages/icons/DefaultImg/ic_'?>'+cardType+'_system.svg');
                }
            });

            cardExpiryElement.addEventListener('change', function(event) {
                if (event.error) {
                    if(event.empty == true)
                    {
                        $('#card-exp-error').text("<?= $languageLabelsArr['LBL_REQUIRED'] ?>");
                    }
                    else{
                        $('#card-exp-error').text("<?= $languageLabelsArr['LBL_INVALIED'] ?>");
                    }
                } else {
                     $('#card-exp-error').text('');
                }
            });

            cardCvcElement.addEventListener('change', function(event) {
                if (event.error) {
                    if(event.empty == true)
                    {
                        $('#card-cvc-error').text("<?= $languageLabelsArr['LBL_REQUIRED'] ?>");
                    }
                    else{
                        $('#card-cvc-error').text("<?= $languageLabelsArr['LBL_INVALIED'] ?>");
                    }
                } else {
                     $('#card-cvc-error').text('');
                }
            });

            $('#cardholder-name').on('change, keyup', function(){
                if($(this).val().length > 0)
                {
                    $('#card-name-error').text("");
                }
            });

            $('#cancel-btn').click(function(){
                window.location.href = "<?= $cancelUrl.'&status=failure' ?>";
            });
            var cardButton = document.getElementById('card-button');
            var clientSecret = '<?= $intent->client_secret ?>';
            
            
            cardButton.addEventListener('click', function (ev) {
                $('.overlay').show();
                $('body').css('overflow', 'hidden');
                if($('#cardholder-name').val() == "")
                {
                    $('#card-name-error').text("<?= $languageLabelsArr['LBL_REQUIRED'] ?>");
                } else {
                     $('#card-name-error').text('');
                }
                $('#card-button').append(' <i class="fa fa-spinner fa-spin"></i>');
                $('#cardholder-name').prop('readonly', true);
                $('#card-button, #cancel-btn').prop('disabled', true);
                $('#saved_card_tab').addClass('disabled');
                form_submit = 1;
                var cardholderName = document.getElementById('cardholder-name');
                
                cardNumberElement.update({disabled:true});
                cardExpiryElement.update({disabled:true});
                cardCvcElement.update({disabled:true});
                stripe.confirmCardSetup(
                        clientSecret,
                        {
                            payment_method: {
                                card: cardNumberElement,
                                billing_details: {name: cardholderName.value, email: '<?= $userData[0]["vEmail"] ?>'}
                            }
                        }
                ).then(function (result) {
                    
                    if ("error" in result) {
                        $('.overlay').hide();
                        $('body').css('overflow', 'auto');
                        $('#api_error_msg').text(result.error.message);
                        $('#api_error').show();
                        setTimeout(function() {
                            $('#api_error').hide();
                        }, 5000);
                        
                        // Display error.message in your UI.
                        cardNumberElement.update({disabled:false});
                        cardExpiryElement.update({disabled:false});
                        cardCvcElement.update({disabled:false});
                        $('#card-button').find('i').remove();
                        $('#cardholder-name').prop('readonly', false);
                        $('#card-button, #cancel-btn').prop('disabled', false);
                        $('#card-button').find('i').remove();
                        $('#saved_card_tab').removeClass('disabled');
                        form_submit = 0;
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
                            $('.overlay').hide();
                            $('body').css('overflow', 'auto');
                        }
                    }
                });
            });

            $('#saved_card_btn').click(function() {
                $('.overlay').show();
                $('body').css('overflow', 'hidden');
                $(this).prop('disabled', true);
                $('#new_card_tab').addClass('disabled');
                form_submit = 1;
                $('#saved_card_form').submit();
            });

            $('#saved_card_tab, #new_card_tab').click(function() {
                if($(this).hasClass('disabled')) {
                    alert("Please wait. We are processing your previous request.");
                    return false;
                }

                if($(this).attr('id') == "new_card_tab")
                {
                    $('#accepted-cards').show();
                }
                else {
                    $('#accepted-cards').hide();
                }
            });

            // Handle form submission.
            /*var form = document.getElementById('payment-form');
            form.addEventListener('submit', function(event) {
              event.preventDefault();


                var ownerInfo = {
                  owner: {
                    name: $('[name="card_holder_name"]').val(),
                    email: 'test@gmail.com'
                  },
                };
                stripe.createSource(cardNumberElement, ownerInfo).then(function(result) {
                    if (result.error) {
                      // Inform the user if there was an error
                      var errorElement = document.getElementById('card-errors');
                      errorElement.textContent = result.error.message;
                    } else {
                      // Send the source to your server
                      stripeSourceHandler(result.source);
                    }
                  });         
            });

            function stripeSourceHandler(source) {
              // Insert the source ID into the form so it gets submitted to the server
              var form = document.getElementById('payment-form');
              var hiddenInput = document.createElement('input');
              hiddenInput.setAttribute('type', 'hidden');
              hiddenInput.setAttribute('name', 'stripeSource');
              hiddenInput.setAttribute('value', source.id);
              form.appendChild(hiddenInput);

              // Submit the form
              form.submit();
            }*/

            $('.delete-card').click(function() {
                $('#iPaymentInfoId').val($(this).data('cardid'));
                $('#vCardToken').val($(this).data('cardtoken'));
                $('#card_no').text($(this).data('cardno'));
                $('#delete_card_modal').modal('show');
            });

            $('#delete_card_btn').click(function() {
                $('#delete_card_modal').modal('hide');
                $('.overlay').show();
                $('body').css('overflow', 'hidden');
            });

            $('.set-default-card').click(function() {
                $('[name="default_iPaymentInfoId"]').val($(this).data('cardid'));                
                $('#set-default-card-form').submit();
                $('.overlay').show();
                $('body').css('overflow', 'hidden');
            });
        </script>
    </body>
</html>