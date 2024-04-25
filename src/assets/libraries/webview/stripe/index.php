<?php
require_once('init.php');
include_once('../../../../common.php');
$generalConfigPaymentArr = $CONFIG_OBJ->getGeneralVarAll_Payment_Array();
$STRIPE_SECRET_KEY = $generalConfigPaymentArr['STRIPE_SECRET_KEY'];
$STRIPE_PUBLISH_KEY = $generalConfigPaymentArr['STRIPE_PUBLISH_KEY'];
$stripe = array("secret_key" => $STRIPE_SECRET_KEY, "publishable_key" => $STRIPE_PUBLISH_KEY);
$statusMsg = "Transaction has been failed";
$UserType = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : $_REQUEST["GeneralUserType"];
$iUserId = isset($_REQUEST["iUserId"]) ? $_REQUEST["iUserId"] : '';
$iServiceId = isset($_REQUEST["iServiceId"]) ? $_REQUEST["iServiceId"] : '';
//echo "<pre>";print_r($_REQUEST);die;
if ($UserType == "Rider" || $UserType == "Passenger") {
    $sql = "SELECT iUserId,eStatus,vLang,vCreditCard,vExpMonth,vExpYear,vCvv,vStripeToken,vStripeCusId,vEmail,vStripePaymentMethod,vCreditCard FROM `register_user` WHERE iUserId='" . $iUserId . "'";
} else {
    $sql = "SELECT iDriverId,eStatus,vLang,vCreditCard,vExpMonth,vExpYear,vCvv,vStripeToken,vStripeCusId,vEmail,vStripePaymentMethod,vCreditCard FROM `register_driver` WHERE iDriverId='" . $iUserId . "'";
}

$userData = $obj->MySQLSelect($sql);

$vLang = $userData[0]['vLang'];
if ($vLang == "") {
    $lang_data = $obj->MySQLSelect("SELECT vCode FROM language_master WHERE eDefault = 'Yes'");
    $vLang = $lang_data[0]['vCode'];
    //$vLang = get_value('language_master', 'vCode', 'eDefault', 'Yes', '', 'true');
}
$languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLang, "1", $iServiceId);

    use Stripe\PaymentIntent;
    use Stripe\SetupIntent;
    use Stripe\Stripe;

    Stripe::setApiKey($stripe['secret_key']);
$redirectUrl = $tconfig['tsite_url']."assets/libraries/webview/stripe/index.php";

$intent = SetupIntent::create();
//$redirectUrl = "http://192.168.1.131/cubejekdev_food/assets/libraries/webview/stripe-v1/index.php";
//$redirectUrl = "http://192.168.1.131/cubejekdev_payment_methods/assets/libraries/webview/stripe/index.php";

$itemPrice = isset($_REQUEST["amount"]) ? $_REQUEST["amount"] : 1;
$currency = isset($_REQUEST["ccode"]) ? $_REQUEST["ccode"] : 'USD';
$themeColor = isset($_REQUEST["AppThemeColor"]) ? $_REQUEST["AppThemeColor"] : '000000';
$textColor = isset($_REQUEST["AppThemeTxtColor"]) ? $_REQUEST["AppThemeTxtColor"] : 'FFFFFF';
$stripeamount = isset($_REQUEST["userAmount"]) ? $_REQUEST["userAmount"] : '';
//$stripeamount = isset($_REQUEST["stripeamount"]) ? $_REQUEST["stripeamount"] : 1;
$UniqueCode = isset($_REQUEST["UniqueCode"]) ? $_REQUEST["UniqueCode"] : '';
$eForTip = isset($_REQUEST["eForTip"]) ? $_REQUEST["eForTip"] : 'No';
$DebitAmt = isset($_REQUEST["DebitAmt"]) ? $_REQUEST["DebitAmt"] : '';
$iTripId = isset($_REQUEST["iTripId"]) ? $_REQUEST["iTripId"] : '';
$vUserDeviceCountry = isset($_REQUEST["vUserDeviceCountry"]) ? $_REQUEST["vUserDeviceCountry"] : '';
$returnUrl = isset($_REQUEST['returnUrl']) ? trim($_REQUEST['returnUrl']) : 'result.php';
$returnUrl = isset($_REQUEST['returnUrl']) ? trim($_REQUEST['returnUrl']) : 'webservice_shark.php';
$iOrderId = isset($_REQUEST["iOrderId"]) ? $_REQUEST["iOrderId"] : '';
$vOrderNo = isset($_REQUEST["vOrderNo"]) ? $_REQUEST["vOrderNo"] : '';
$ePaymentOption = isset($_REQUEST["ePaymentOption"]) ? $_REQUEST["ePaymentOption"] : '';
$vStripeToken = isset($_REQUEST["vStripeToken"]) ? $_REQUEST["vStripeToken"] : '';
$CheckUserWallet = isset($_REQUEST["CheckUserWallet"]) ? $_REQUEST["CheckUserWallet"] : 'No';
$type = isset($_REQUEST['type']) ? trim($_REQUEST['type']) : '';
$eSystem = isset($_REQUEST["eSystem"]) ? $_REQUEST["eSystem"] : '';
$tSessionId = isset($_REQUEST['tSessionId']) ? trim($_REQUEST['tSessionId']) : '';
$GeneralMemberId = isset($_REQUEST['GeneralMemberId']) ? trim($_REQUEST['GeneralMemberId']) : '';
$GeneralUserType = isset($_REQUEST['GeneralUserType']) ? trim($_REQUEST['GeneralUserType']) : '';
$GeneralAppVersion = isset($_REQUEST['GeneralAppVersion']) ? trim($_REQUEST['GeneralAppVersion']) : '';
$Platform = isset($_REQUEST['Platform']) ? trim($_REQUEST['Platform']) : 'Android';
$vTimeZone = isset($_REQUEST["vTimeZone"]) ? $_REQUEST["vTimeZone"] : '';
$iServiceId = isset($_REQUEST["iServiceId"]) ? $_REQUEST["iServiceId"] : '';
$vUserDeviceCountry = isset($_REQUEST["vUserDeviceCountry"]) ? $_REQUEST["vUserDeviceCountry"] : '';
$vCurrentTime = isset($_REQUEST["vCurrentTime"]) ? $_REQUEST["vCurrentTime"] : '';
$GeneralDeviceType = isset($_REQUEST['GeneralDeviceType']) ? trim($_REQUEST['GeneralDeviceType']) : '';
$vPayMethod = isset($_REQUEST["vPayMethod"]) ? $_REQUEST["vPayMethod"] : ''; // Instant,Manual
$themeColor = isset($_REQUEST["AppThemeColor"]) ? $_REQUEST["AppThemeColor"] : '000000';
$textColor = isset($_REQUEST["AppThemeTxtColor"]) ? $_REQUEST["AppThemeTxtColor"] : 'FFFFFF';
$UserType = $GeneralUserType;
//$extraParameters = "?iUserId=" . $iUserId . "&UserType=" . $UserType . "&amount=" . $itemPrice . "&ccode=" . $currency . "&vUserDeviceCountry=" . $vUserDeviceCountry . "&returnUrl=" . urlencode($returnUrl) . "&AppThemeColor=" . $themeColor . "&AppThemeTxtColor=" . $textColor . "&UniqueCode=" . $UniqueCode . "&eForTip=" . $eForTip . "&iTripId=" . $iTripId . "&DebitAmt=" . $DebitAmt;

$status = "failed";
$extraParameters = "?iUserId=" . $iUserId . "&iOrderId=" . $iOrderId . "&amount=" . $itemPrice . "&ccode=" . $currency . "&vOrderNo=" . $vOrderNo . "&ePaymentOption=" . $ePaymentOption . "&CheckUserWallet=" . $CheckUserWallet . "&eSystem=" . $eSystem . "&vStripeToken=" . $vStripeToken . "&type=" . $type . "&Platform=" . $Platform . "&tSessionId=" . $tSessionId . "&GeneralMemberId=" . $GeneralMemberId . "&GeneralUserType=" . $GeneralUserType . "&GeneralDeviceType=" . $GeneralDeviceType . "&GeneralAppVersion=" . $GeneralAppVersion . "&vTimeZone=" . $vTimeZone . "&vUserDeviceCountry=" . $vUserDeviceCountry . "&iServiceId=" . $iServiceId . "&vCurrentTime=" . $vCurrentTime . "&payStatus=" . $status . "&vPayMethod=" . $vPayMethod . "&AppThemeColor=" . $themeColor . "&AppThemeTxtColor=" . $textColor;

if (!empty($_REQUEST['payMethod'])) {
    $token = isset($_POST["stripeToken"]) ? $_POST["stripeToken"] : '';
    $vStripeCusId = isset($_POST["stripeCustid"]) ? $_POST["stripeCustid"] : '';
    $name = isset($_POST["name"]) ? $_POST["name"] : '';
    $currency = isset($_POST["ccode"]) ? $_POST["ccode"] : 'USD';
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
    $DB_Default_Currency = $obj->MySQLSelect("SELECT vName FROM currency WHERE eDefault = 'Yes'");
    $currency = $DB_Default_Currency[0]['vName'];
    if ($eForTip == 'Yes') {
        $tDescription = "Amount Debit for " . $UserType;
    } else {
        $tDescription = "Amount Add for " . $UserType;
    }
    $paymentMethod = $_REQUEST['payMethod'];

    //if (isset($_REQUEST['custId'])) {
    if($userData[0]['vStripeCusId'] != "") {
        $custId = $userData[0]['vStripeCusId'];


        $tpayment_method = \Stripe\PaymentMethod::retrieve(
            $userData[0]['vStripePaymentMethod']
        );
        $tpayment_method->detach();
    } else {
        $datae = \Stripe\Customer::create(['payment_method' => $paymentMethod]);
        $custId = $datae->id;
    }

    try {
        $tpayment_method = \Stripe\PaymentMethod::retrieve(
            $paymentMethod
        );
        $tpayment_method->attach([
          'customer' => $custId,
        ]);
        $payment_method = PaymentIntent::create([
                    'amount' => $itemPrice,
                    'currency' => $currency,
                    'customer' => $custId,
                    'payment_method' => $paymentMethod,
                    'off_session' => true,
                    'confirm' => true,
        ]);
    } catch (\Stripe\Exception\CardException $e) {
        // Error code will be authentication_required if authentication is needed
        echo 'Error code is:' . $e->getError()->code;
        $payment_intent_id = $e->getError()->payment_intent->id;
        $payment_intent = \Stripe\PaymentIntent::retrieve($payment_intent_id);
    }

    $chargeJsonData = $payment_method->jsonSerialize();
    $chargeJson = $chargeJsonData['charges']['data'][0];
    $card_num = $chargeJson['payment_method_details']['card']['last4'];

    $data_member['vStripeCusId'] = $custId;
    $data_member['vStripePaymentMethod'] = $paymentMethod;
    $data_member['vCreditCard'] = $card_num;
     
    if ($UserType == "Rider" || $UserType == "Passenger") {
        $where = " iUserId = '$iUserId'";
        $updateData = $obj->MySQLQueryPerform("register_user", $data_member, 'update', $where); //commented line by mrunalbhai //removed comment by SP bc not updated vStripePaymentMethod so use existing card is not shown..
    } else {
        $where = " iDriverId = '$iUserId'";
        $updateData = $obj->MySQLQueryPerform("register_driver", $data_member, 'update', $where); //commented line by mrunalbhai //removed comment by SP bc not updated vStripePaymentMethod so use existing card is not shown..
    }
    $returnArr["Action"] = "0";
    $returnArr['message'] = "LBL_REQUIRED_MINIMUM_AMOUT";
    $minValue = strval(round(0.51 * $currencyratio, 2));
    $labelValue = $languageLabelsArr['LBL_REQUIRED_MINIMUM_AMOUT'];
    $failedLabelValue = $languageLabelsArr['LBL_CHARGE_COLLECT_FAILED'];

    if (isset($chargeJson['amount_refunded']) && $chargeJson['amount_refunded'] == 0 && empty($chargeJson['failure_code']) && $chargeJson['paid'] == 1 && $chargeJson['captured'] == 1) {
        //order details 
        $amount = $chargeJson['amount'];
        $balance_transaction = $chargeJson['balance_transaction'];
        $currency = $chargeJson['currency'];
        $status = $chargeJson['status'];
        $date = date("Y-m-d H:i:s");
        $statusMsg = "Transaction has been " . $status;
        //echo $returnUrl."======";
        if ($status == "succeeded") {
            //$iServiceId = isset($_REQUEST["iServiceId"]) ? $_REQUEST["iServiceId"] : '';
            $vUserDeviceCountry = isset($_REQUEST["vUserDeviceCountry"]) ? $_REQUEST["vUserDeviceCountry"] : '';

            //$extraParameters = "?iUserId=" . $iUserId . "&UserType=" . $UserType . "&amount=" . $itemPrice . "&ccode=" . $currency . "&vUserDeviceCountry=" . $vUserDeviceCountry . "&returnUrl=" . urlencode($returnUrl) . "&AppThemeColor=" . $themeColor . "&AppThemeTxtColor=" . $textColor . "&UniqueCode=" . $UniqueCode . "&eForTip=" . $eForTip . "&iTripId=" . $iTripId . "&DebitAmt=" . $DebitAmt;
            $extraParameters = "?iUserId=" . $iUserId . "&iOrderId=" . $iOrderId . "&amount=" . $itemPrice . "&ccode=" . $currency . "&vOrderNo=" . $vOrderNo . "&ePaymentOption=" . $ePaymentOption . "&CheckUserWallet=" . $CheckUserWallet . "&eSystem=" . $eSystem . "&vStripeToken=" . $vStripeToken . "&type=" . $type . "&Platform=" . $Platform . "&tSessionId=" . $tSessionId . "&GeneralMemberId=" . $GeneralMemberId . "&GeneralUserType=" . $GeneralUserType . "&GeneralDeviceType=" . $GeneralDeviceType . "&GeneralAppVersion=" . $GeneralAppVersion . "&vTimeZone=" . $vTimeZone . "&vUserDeviceCountry=" . $vUserDeviceCountry . "&iServiceId=" . $iServiceId . "&vCurrentTime=" . $vCurrentTime . "&payStatus=" . $status . "&vPayMethod=" . $vPayMethod . "&AppThemeColor=" . $themeColor . "&AppThemeTxtColor=" . $textColor;
            $redirectUrl = $tconfig['tsite_url'].$returnUrl . $extraParameters . "&payStatus=" . $status;
            header('Location: ' . $redirectUrl);
            exit;
            ?><script>window.location.replace("<?php echo $redirectUrl; ?>");
            </script>
            <?php
            //header('Location: ' . $redirectUrl); //Redirect for Update Database Table Process
            die;
        } else {
            header('Location: ' . $tconfig['tsite_url'].$returnUrl . "?payStatus=Failed?success=0&message=" . $failedLabelValue); //Redirect for Update Database Table Process
            echo $statusMsg;
            die;
        }
    } else {
        echo "<pre>";print_r($chargeJson);die;
        $errorMdsg = $labelValue . " " . $minValue . " " . $currencyCode;
        if (isset($chargeJson['message']) && $chargeJson['message'] != "") {
            $errorMdsg = $chargeJson['message'];
        }
        if (isset($chargeJson['error'])) {
            $errorMdsg = $chargeJson['error']['message'];
        }
        //echo $errorMdsg;die;
        header('Location: ' . $returnUrl . "?payStatus=Failed&success=0&message=" . $errorMdsg); //Redirect for Update Database Table Process
        echo $statusMsg;
        die;
    }
}
?>
<html>
    <meta http-equiv="content-type" content="text/html; charset=windows-1250">
    <meta name="generator" content="PSPad editor, www.pspad.com">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stripe Payment</title>
    <script type="text/javascript" src="js/validation_stripe_js.js"></script>
    <script type="text/javascript" src="js/jquery.min.js"></script>
    <link rel="stylesheet" media="screen" type="text/css" href="css/screen.css" />
    <style>
        .credit-new{
            margin: 0px;
            padding: 15px 0 30px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: 600;
            text-transform: capitalize;
        }

        .card-number-d{
            margin: 0px;
            padding: 15px;
            display: flex;
            justify-content: flex-start;
            align-items: center;
            font-weight: 600;
            text-transform: capitalize;
            flex-wrap: wrap;
            width: 40%;
        }

        .card-number-d label {
            margin: 0px 0 8px!important;
            padding: 0px;
            display: flex;
            flex-wrap: wrap;
        }

        .card-number-d span{
            display: flex;
            justify-content: flex-start;
        }

        .credit-or{
            margin: 0px 0 12px!important;
            padding: 10px 0 10px 0 !important;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: 600;
            text-transform: capitalize;
            width: 100%!important;
            font-size: 20px;
        }

        .card-form{float:none; display: block;}
        .work-card {
            float: none;
            display: block;
            padding: 0px 0 0 0 !important;
            margin: 0px;
        }
        .card-num-a{
            float: none;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0px 0px 3px #989898;
            margin: 0px 0 15px;
            border-radius: 5px;
        }
        .card-num-a label{
            float: none;
            font-size: 20px;
            font-weight: 600;
            margin: 0 15px 0 0px;
        }
        .button-num {
            width: auto;
            background: #343434;
            border: none;
            color: #ffffff;
            margin: 0px 20px 0 0px;
            padding: 10px 40px 10px 40px;
            font-size: 20px;
        }
        .button-num-new-a{
            width: auto;
            background: #343434;
            border: none;
            color: #ffffff;
            margin: 0px 20px 0 0px;
            padding: 10px 40px 10px 40px;
            font-size: 20px;
        }
       
        .back-img{float:none; display: block;}
        .our-work-new{float:none; display: block;}
        .our-text{float:none; display: block;}
        .field{float:none; display: block;}
        .card-num-a-b{margin: 25px 0 0 0;}
        .StripeElement{margin: 16px 0 0;}
    </style>
    <body>
        <div class="main-part"> 
            <div class="page-contant">  
                <div class="page-contant-inner"> 

                    <?php
                    if (!empty($userData[0]['vStripePaymentMethod']) && !empty($userData[0]['vStripeCusId']) && !empty($userData[0]['vCreditCard'])) {
                        $redirectUrl = $tconfig['tsite_url']."assets/libraries/webview/stripe/index.php";
                        $returnUrl = $redirectUrl . $extraParameters . "&payMethod=" . $userData[0]['vStripePaymentMethod'] . "&custId=" . $userData[0]['vStripeCusId'];
                        ?>
                        <form action="<?= $returnUrl ?>" method="post">
                            <h2 class="credit-new"><?= $languageLabelsArr['LBL_PAY_WITH_EXISTING_CARD'] ?></h2>
                            <label class="work-card" align="center" style="padding-top: 20px;"> 
                                <div class="card-num-a" align="center">
                                    <div class="card-number-d">
                                        <label><?= $languageLabelsArr['LBL_CARD_NUMBER_TXT'] ?>: </label><span><?= "**** **** **** **** " . $userData[0]['vCreditCard']; ?></span>
                                    </div>
                                    <button type="submit" class="button-num-new-a" name="btn_payment"><?= $languageLabelsArr['LBL_BTN_PAYMENT_TXT'] ?></button>
                                </div>
                            </label>
                            <label class="credit-or">------------ <?= $languageLabelsArr['LBL_OR_TXT'] ?> ------------</label>
                        </form>
                        <label class="back-img" style="padding-top: 20px;"><h2 class="credit-new"><?= $languageLabelsArr['LBL_PAY_WITH_NEW_CARD'] ?><!--<img src="img/card.png">--></h2></label>
                         <p id="messageId" style="display: none;color: red;"></p>
                        <label class="our-work-new">
                    <?php } else { ?>
                    <label class="back-img" style="padding-top: 20px;"><img src="img/card.png"></label>
                    <p id="messageId" style="display: none;color: red;"></p>
                    <label class="our-work-new new-one">
                    <?php } ?>
                        <span class="our-text"><?= $languageLabelsArr['LBL_CARD_HOLDER_NAME_TXT'] ?></span>
                    </label>
                    <b class="" ><input id="cardholder-name" type="text"></b>
                    <!-- placeholder for Elements -->
                    <div id="card-element"></div>
                    <label class="work-card" align="center" style="padding-top: 20px;"> 
                        <div class="card-num-a-b" align="center">     
                            <button id="card-button"data-secret="<?= $intent->client_secret ?>" class="button-num" name="btn_payment"><?= $languageLabelsArr['LBL_BTN_SUBMIT_TXT'] ?></button>
                        </div>
                    </label> 
                    <script src="https://js.stripe.com/v3/"></script>
                    <script type="text/javascript" src="js/validation_stripe_js.js"></script>
                    <script type="text/javascript" src="js/jquery.min.js"></script>
                    <link rel="stylesheet" media="screen" type="text/css" href="css/screen.css" />
                    <script>
                var stripe = Stripe('<?= $STRIPE_PUBLISH_KEY ?>');
                var elements = stripe.elements({locale: '<?= $vLang; ?>'});
                var cardElement = elements.create('card',{hidePostalCode: true});
                cardElement.mount('#card-element');
                var cardholderName = document.getElementById('cardholder-name');
                var cardButton = document.getElementById('card-button');
                var clientSecret = cardButton.dataset.secret;
                cardButton.addEventListener('click', function (ev) {
                    stripe.confirmCardSetup(
                            clientSecret,
                            {
                                payment_method: {
                                    card: cardElement,
                                    billing_details: {name: cardholderName.value}
                                }
                            }
                    ).then(function (result) {
                        console.log(result);
                        if (result.error) {
                            // Display error.message in your UI.
                            $("#messageId").show();
                            $("#messageId").text("Error : " + result.error.message);
                        } else {
                            if (result.setupIntent.status === 'succeeded') {
                                // The setup has succeeded. Display a success message.
                                window.location.replace("<?= $redirectUrl.$extraParameters; ?>&payMethod=" + result.setupIntent.payment_method);
                            } else {
                                $("#messageId").show();
                                $("#messageId").text("Error : " + result.setupIntent.status);
                            }
                        }
                    });
                });
                    </script>
                </div>
            </div>
        </div>
    </body>
</html>