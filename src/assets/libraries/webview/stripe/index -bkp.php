<?php
//ini_set('display_errors', 1);
//error_reporting(E_ALL);
require_once('init.php');
include_once('../../../../common.php');
$generalConfigPaymentArr = $CONFIG_OBJ->getGeneralVarAll_Payment_Array();
$STRIPE_SECRET_KEY = $generalConfigPaymentArr['STRIPE_SECRET_KEY'];
$STRIPE_PUBLISH_KEY = $generalConfigPaymentArr['STRIPE_PUBLISH_KEY'];
$stripe = array("secret_key" => $STRIPE_SECRET_KEY, "publishable_key" => $STRIPE_PUBLISH_KEY);

$statusMsg = "Transaction has been failed";
$UserType = isset($_REQUEST["UserType"]) ? $_REQUEST["UserType"] : '';
$iUserId = isset($_REQUEST["iUserId"]) ? $_REQUEST["iUserId"] : '';
$iServiceId = isset($_REQUEST["iServiceId"]) ? $_REQUEST["iServiceId"] : '';

if ($UserType == "Rider") {
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

    use Stripe\Customer;
    use Stripe\PaymentIntent;
    use Stripe\SetupIntent;
    use Stripe\Stripe;

    Stripe::setApiKey($stripe['secret_key']);

$intent = SetupIntent::create();
$redirectUrl = "http://192.168.1.131/cubejekdev_food/assets/libraries/webview/stripe-v1/index.php";

$itemPrice = isset($_REQUEST["amount"]) ? $_REQUEST["amount"] : 1;
$currency = isset($_REQUEST["ccode"]) ? $_REQUEST["ccode"] : '';
$themeColor = isset($_REQUEST["AppThemeColor"]) ? $_REQUEST["AppThemeColor"] : '000000';
$textColor = isset($_REQUEST["AppThemeTxtColor"]) ? $_REQUEST["AppThemeTxtColor"] : 'FFFFFF';
$stripeamount = isset($_REQUEST["stripeamount"]) ? $_REQUEST["stripeamount"] : 1;
$UniqueCode = isset($_REQUEST["UniqueCode"]) ? $_REQUEST["UniqueCode"] : '';
$eForTip = isset($_REQUEST["eForTip"]) ? $_REQUEST["eForTip"] : 'No';
$DebitAmt = isset($_REQUEST["DebitAmt"]) ? $_REQUEST["DebitAmt"] : '';
$iTripId = isset($_REQUEST["iTripId"]) ? $_REQUEST["iTripId"] : '';
$vUserDeviceCountry = isset($_REQUEST["vUserDeviceCountry"]) ? $_REQUEST["vUserDeviceCountry"] : '';
$returnUrl = isset($_REQUEST['returnUrl']) ? trim($_REQUEST['returnUrl']) : 'result.php';

$extraParameters = "?iUserId=" . $iUserId . "&UserType=" . $UserType . "&amount=" . $itemPrice . "&ccode=" . $currency . "&vUserDeviceCountry=" . $vUserDeviceCountry . "&returnUrl=" . urlencode($returnUrl) . "&AppThemeColor=" . $themeColor . "&AppThemeTxtColor=" . $textColor . "&UniqueCode=" . $UniqueCode . "&eForTip=" . $eForTip . "&iTripId=" . $iTripId . "&DebitAmt=" . $DebitAmt;

if(!empty($userData[0]['vStripePaymentMethod']) && !empty($userData[0]['vStripeCusId'])) {
    //header("location: ". $redirectUrl.$extraParameters);
    //exit;
    //foreach($_REQUEST as $key=>$val) {
    //    $$key = $value;
    //}
}

if(!empty($_REQUEST['payMethod'])) {
        $token = isset($_POST["stripeToken"]) ? $_POST["stripeToken"] : '';
        $vStripeCusId = isset($_POST["stripeCustid"]) ? $_POST["stripeCustid"] : '';
        $name = isset($_POST["name"]) ? $_POST["name"] : '';
        //$currency = isset($_POST["ccurrency"]) ? $_POST["ccurrency"] : '';
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
        
        if ($eForTip == 'Yes') {
            $tDescription = "Amount Debit for " . $UserType;
        } else {
            $tDescription = "Amount Add for " . $UserType;
        }
        $paymentMethod = $_REQUEST['payMethod'];
        
        if (isset($_REQUEST['custId'])) {
            $custId = $_REQUEST['custId'];
        } else {
            $datae = Customer::create(['payment_method' => $paymentMethod]);
            $custId = $datae->id;
        }
        
        //echo "Customer Id : " . $custId . "======= Payment Method : " . $paymentMethod . "<br>";
        
        try {
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
        if ($UserType == "Rider") {
            $where = " iUserId = '$iUserId'";
            $updateData = $obj->MySQLQueryPerform("register_user", $data_member, 'update',$where);
        } else {
            $where = " iDriverId = '$iUserId'";
            $updateData = $obj->MySQLQueryPerform("register_driver", $data_member, 'update',$where);    
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

                $extraParameters = "?iUserId=" . $iUserId . "&UserType=" . $UserType . "&amount=" . $itemPrice . "&ccode=" . $currency . "&vUserDeviceCountry=" . $vUserDeviceCountry . "&returnUrl=" . urlencode($returnUrl) . "&AppThemeColor=" . $themeColor . "&AppThemeTxtColor=" . $textColor . "&UniqueCode=" . $UniqueCode . "&eForTip=" . $eForTip . "&iTripId=" . $iTripId . "&DebitAmt=" . $DebitAmt;

                $redirectUrl = $returnUrl . $extraParameters . "&payStatus=" . $status;
                header('Location: ' . $redirectUrl);  exit;
                ?><script>window.location.replace("<?php echo $redirectUrl; ?>");
                </script>
                <?php
                //header('Location: ' . $redirectUrl); //Redirect for Update Database Table Process
                die;
            } else {
                header('Location: ' . $returnUrl . "?payStatus=Failed?success=0&message=" . $failedLabelValue); //Redirect for Update Database Table Process
                echo $statusMsg;
                die;
            }
        } else {
            //echo "<pre>";print_r($chargeJson);die;
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
} ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
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
.back-img{float:none; display: block;}
.our-work-new{float:none; display: block;}
.our-text{float:none; display: block;}
.field{float:none; display: block;}
.card-num-a-b{margin: 25px 0 0 0;}
    </style>
      <body>
        <div class="main-part"> 
            <div class="page-contant">  
                <div class="page-contant-inner"> 
                    <label class="card-form">  
<?php if ($itemPrice != "") { ?>
    <!-- <div class="our-work-new" style="background-color: #<?php echo $themeColor; ?>;color: #<?php echo $textColor; ?>;">
        <span class="our-text" style="font-size: 18px;padding: 10px;">Pay : <?php echo round($itemPrice, 2); ?>(<?= $currency; ?>)<? if ($eForTip == 'Yes') { ?><br/><?
                echo round($DebitAmt, 2) . " " . $languageLabelsArr['LBL_WALLET_DEDUCT_TXT'];
            }
            ?></span>
    </div> -->
<?php }
if(!empty($userData[0]['vStripePaymentMethod']) && !empty($userData[0]['vStripeCusId']) && !empty($userData[0]['vCreditCard'])) {
    $returnUrl = "http://192.168.1.131/cubejekdev_food/assets/libraries/webview/stripe-v1/index.php".$extraParameters."&payMethod=".$userData[0]['vStripePaymentMethod']."&custId=".$userData[0]['vStripeCusId'];
?>
<form action="<?= $returnUrl ?>" method="post">
    <h2 class="credit-new">Pay with existing card </h2>
            <label class="work-card" align="center" style="padding-top: 20px;"> 
                <div class="card-num-a" align="center">
                   <div class="card-number-d">
                    <label>Credit card number: </label><span><?= "**** **** **** **** ".$userData[0]['vCreditCard']; ?></span>
                    </div>
                    <button type="submit" class="button-num" name="btn_payment">Pay</button>
                </div>
            </label>
            <label class="credit-or">OR</label>
    </form>
    <!--<form action="<?= $returnUrl ?>" method="post">
    <h2 class="credit-new">Credit card number: <?= "**** **** **** **** ".$userData[0]['vCreditCard']; ?></h2>
            <label class="work-card" align="center" style="padding-top: 20px;"> 
                <div class="card-num-a" align="center">     
                    <button type="submit" class="button-num" name="btn_payment">Pay with existing card</button>
                </div>
            </label>
            <label class="credit-or">OR</label>
    </form>-->
<?php } ?>
<label class="back-img" style="padding-top: 20px;"><img src="img/card.png"></label>
<label class="our-work-new new-one">
    <span class="our-text">Card Holder Name</span>
</label>
<label class="field"><b class="class-box" ><input id="cardholder-name" type="text"></b></label>
<!-- placeholder for Elements -->
<div id="card-element" ></div>
<!--<button id="card-button" data-secret="<?= $intent->client_secret ?>">-->
<!--    Save Card-->
<!--</button>-->
<label class="work-card" align="center" style="padding-top: 20px;"> 
    <div class="card-num-a-b" align="center">     
        <button id="card-button"data-secret="<?= $intent->client_secret ?>" class="button-num" name="btn_payment">Submit Payment</button>
    </div>
</label> 
<script src="https://js.stripe.com/v3/"></script>
<script>
    var stripe = Stripe('<?= $STRIPE_PUBLISH_KEY ?>');
    var elements = stripe.elements();
    var cardElement = elements.create('card');
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
            if (result.error) {
                // Display error.message in your UI.
            } else {
                console.log(result);
                if (result.setupIntent.status === 'succeeded') {
                    // The setup has succeeded. Display a success message.
                    //alert("<?= $redirectUrl; ?>?payMethod=" + result.setupIntent.payment_method + '<?= $extraParameters; ?>');
                    window.location.replace("<?= $redirectUrl.$extraParameters; ?>" + "&payMethod=" + result.setupIntent.payment_method);
                }
            }
        });
    });
</script>