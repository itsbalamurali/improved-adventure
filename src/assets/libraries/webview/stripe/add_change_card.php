<?php
include_once('../../../../common.php');
require_once('init.php');

use Stripe\Customer;
use Stripe\SetupIntent;
use Stripe\Stripe;

Stripe::setApiKey($STRIPE_SECRET_KEY);
Stripe::setMaxNetworkRetries(5);

// ini_set('display_errors', 1);
// error_reporting(E_ALL);

$UserType = isset($_REQUEST["GeneralUserType"]) ? $_REQUEST["GeneralUserType"] : '';
$iUserId = isset($_REQUEST["GeneralMemberId"]) ? $_REQUEST["GeneralMemberId"] : '';
$isReturnResult = isset($_REQUEST["IS_RETURN_RESULT"]) ? $_REQUEST["IS_RETURN_RESULT"] : 'No';
$isSelectCard = isset($_REQUEST["IS_SELECT_CARD"]) ? $_REQUEST["IS_SELECT_CARD"] : 'No';
$iPaymentInfoId = isset($_REQUEST["iPaymentInfoId"]) ? $_REQUEST["iPaymentInfoId"] : '';


$page_type = isset($_REQUEST['PAGE_TYPE']) ? $_REQUEST['PAGE_TYPE'] : '';
$failure_url = $tconfig['tsite_url'] . "assets/libraries/webview/failure.php";
$SYSTEM_TYPE = isset($_REQUEST["SYSTEM_TYPE"]) ? $_REQUEST["SYSTEM_TYPE"] : 'WEB';

$APP_RETURN_URL = isset($_REQUEST['APP_RETURN_URL']) ? $_REQUEST['APP_RETURN_URL'] : "";

if ($UserType == "Rider" || $UserType == "Passenger") {
    $dbUserType = "Passenger";
    $sql = "SELECT iUserId,vName,vLastName,vLang,vEmail,vCountry FROM `register_user` WHERE iUserId='" . $iUserId . "'";
    $dbField = "iUserId";
    $tblname = "register_user";
} else {
    $sql = "SELECT iDriverId,vName,vLastName,vLang,vEmail,vCountry FROM `register_driver` WHERE iDriverId='" . $iUserId . "'";
    $dbUserType = "Driver";
    $dbField = "iDriverId";
    $tblname = "register_driver";
}

$userData = $obj->MySQLSelect($sql);
$full_name = $userData[0]['vName'].' '.$userData[0]['vLastName'];

$vLang = $userData[0]['vLang'];
if ($vLang == "") {
    $lang_data = $obj->MySQLSelect("SELECT vCode FROM language_master WHERE eDefault = 'Yes'");
    $vLang = $lang_data[0]['vCode'];
}
$languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLang, "1", $iServiceId);

$vCountry = $userData[0]['vCountry'];

$USER_APP_PAYMENT_METHOD = "Stripe";

$failure_msg = $languageLabelsArr['LBL_SERVER_COMM_ERROR'];
try {
    $intent = SetupIntent::create();
} catch (\Stripe\Exception\ApiConnectionException $e) {
    header('Location:'. $failure_url."?success=0&message=".$failure_msg."&vLang=" . $vLang . "&SYSTEM_TYPE=".$SYSTEM_TYPE);
    exit;
} catch(Exception $e) {
    $failure_msg = $e->getMessage();
    header('Location:'. $failure_url."?success=0&message=".$failure_msg."&vLang=" . $vLang . "&SYSTEM_TYPE=".$SYSTEM_TYPE);
}

$paymentCustomerInfo = getPaymentCustomerInfo($iUserId, $dbUserType);

$stripeSource = isset($_POST['stripeSource']) ? $_POST['stripeSource'] : "";
if($stripeSource != "") {
    try {
        if(empty($paymentCustomerInfo)) {
            $customer = Customer::create([
                'email' => $userData[0]['vEmail'],
                'metadata'    => array('iUserId' => $userData[0]['iUserId'], 'name' => $userData[0]['vName'].' '.$userData[0]['vLastName']),
            ]);
        } else {
            $customer = Customer::retrieve($paymentCustomerInfo[0]['tCustomerId']);
        }

        $custId = $customer->id;
        $payment_method = \Stripe\PaymentMethod::retrieve($stripeSource);
        $payment_method->attach(['customer' => $custId]);
        $success = '1';
        $var_msg = $languageLabelsArr['LBL_INFO_UPDATED_TXT'];

        if(empty($paymentCustomerInfo)) {
            $data_member = array();
            $data_member['iMemberId'] = $iUserId;
            $data_member['eUserType'] = $dbUserType;
            $data_member['tCustomerId'] = $custId;
            $data_member['vPaymentMethod'] = 'Stripe';
            $data_member['ePaymentEnv'] = $SYSTEM_PAYMENT_ENVIRONMENT;

            $obj->MySQLQueryPerform('payment_customer_info', $data_member, 'insert');
        }

        $sql = "SELECT * FROM user_payment_info WHERE tCardToken = '".$stripeSource."'";
        $sqlData = $obj->MySQLSelect($sql);

        $paymentInfoSql = "SELECT * FROM user_payment_info WHERE iMemberId = ".$iUserId." AND eUserType = '".$dbUserType."' AND vPaymentMethod = '".$USER_APP_PAYMENT_METHOD."' AND eStatus = 'Active' AND eDefault = 'Yes'";
        $paymentInfoData = $obj->MySQLSelect($paymentInfoSql);
        
        // Insert in User payment info
        $user_payment_info_insert['eDefault'] = 'No';
        if(count($paymentInfoData) == 0) {
            $user_payment_info_insert['eDefault'] = 'Yes';
        }
        $user_payment_info_insert['eUserType'] = $dbUserType;
        $user_payment_info_insert['tCardToken'] = $stripeSource;
        $user_payment_info_insert['tCardNum'] = 'XXXX XXXX XXXX '.$payment_method->card->last4;
        $user_payment_info_insert['vCardBrand'] = strtolower($payment_method->card->brand);

        include $tconfig['tpanel_path'].'assets/libraries/webview/capture-payment-details.php';
        exit;
    } catch(Exception $e) {
        $success = '3';
        $var_msg = $e->getMessage();
        // echo $var_msg;
        $_SESSION['error_msg'] = $var_msg;
        // header('Location:'.$failure_url."?success=0&message=LBL_SERVER_COMM_ERROR&SYSTEM_TYPE=".$SYSTEM_TYPE);
        exit;
    }
}

if(isset($_POST['eStatus']) && $_POST['eStatus'] = "Delete") {
    $checkCardTrip = $obj->MySQLSelect("SELECT iTripId FROM trips WHERE iActive IN ('Active', 'On Going Trip') WHERE $dbField = '$iUserId'");
    if(!empty($checkCardTrip) && count($checkCardTrip) > 0) {
        $failure_msg = $languageLabelsArr['LBL_DELETE_CARD_TRIP_ERROR_MSG'];
        $_SESSION['error_msg'] = $failure_msg;
    } else {
        $where_payment_info = " iPaymentInfoId = '" . $_POST['iPaymentInfoId'] . "'";
        $data_payment_info['eStatus'] = 'Deleted';
        $obj->MySQLQueryPerform("user_payment_info", $data_payment_info, 'update', $where_payment_info);

        $cardDetailSql = "SELECT * FROM user_payment_info WHERE iPaymentInfoId = ".$_POST['iPaymentInfoId'];
        $cardDetailData = $obj->MySQLSelect($cardDetailSql);

        if($cardDetailData[0]['eDefault'] == 'Yes') {
            $allCardDetailSql = "SELECT * FROM user_payment_info WHERE iPaymentInfoId != ".$_POST['iPaymentInfoId']." AND iMemberId = ".$iUserId." AND eUserType = '".$dbUserType."' AND vPaymentMethod = '".$USER_APP_PAYMENT_METHOD."' AND eStatus = 'Active' LIMIT 1";
            $allCardDetailData = $obj->MySQLSelect($allCardDetailSql);

            $where_payment_info = " iPaymentInfoId = '" . $allCardDetailData[0]['iPaymentInfoId'] . "'";
            $data_payment_info1['eDefault'] = 'Yes';
            $obj->MySQLQueryPerform("user_payment_info", $data_payment_info1, 'update', $where_payment_info);

            $where_payment_info = "iPaymentInfoId != '" . $allCardDetailData[0]['iPaymentInfoId'] . "' AND iMemberId = ".$iUserId." AND eUserType = '".$dbUserType."' AND vPaymentMethod = '".$USER_APP_PAYMENT_METHOD."'";
            $data_payment_info2['eDefault'] = 'No';
            $obj->MySQLQueryPerform("user_payment_info", $data_payment_info2, 'update', $where_payment_info);

            $iPaymentInfoId = "";
        }

        $_SESSION['success_msg'] = $languageLabelsArr['LBL_DELETE_CARD_SUCCESS_MSG'];

        try {
            $tpayment_method = \Stripe\PaymentMethod::retrieve(
                $_POST['vCardToken']
            );
            $tpayment_method->detach();
        } catch (\Stripe\Exception\ApiConnectionException $e) {
            // Can't communicate to Stripe API
            // $_SESSION['error_msg'] = $failure_msg;
        } catch(Exception $e) {
            $failure_msg = $e->getMessage();
            // echo "$failure_msg"; exit;
            // $_SESSION['error_msg'] = $failure_msg;
        }
    }
}

if(isset($_POST['set_as_default']) && $_POST['set_as_default'] == 1) {
    $where_payment_info = " iPaymentInfoId = '" . $_POST['default_iPaymentInfoId'] . "'";
    $data_payment_info['eDefault'] = 'Yes';
    $obj->MySQLQueryPerform("user_payment_info", $data_payment_info, 'update', $where_payment_info);

    $where_payment_info = "iPaymentInfoId != '" . $_POST['default_iPaymentInfoId'] . "' AND iMemberId = ".$iUserId." AND eUserType = '".$dbUserType."' AND vPaymentMethod = '".$USER_APP_PAYMENT_METHOD."'";
    $data_payment_info['eDefault'] = 'No';
    $obj->MySQLQueryPerform("user_payment_info", $data_payment_info, 'update', $where_payment_info);

    $_SESSION['success_msg'] = $languageLabelsArr['LBL_INFO_UPDATED_TXT'];

    $extraParams = "&success=1";
    $return_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    $return_url .= $extraParams;
    $return_url = preg_replace("/&success=1/", "", $return_url);
    
    header('Location:'.$return_url);
    exit;
}

if(isset($_POST['select_card']) && $_POST['select_card'] == 1) {
    $extraParams = "&success=1&iPaymentInfoId=".$_POST['selected_iPaymentInfoId'];
    $return_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    if(!empty($APP_RETURN_URL)) {
        $return_url = urldecode($APP_RETURN_URL);
        // echo $return_url."<br><br>";
    }
    
    $return_url = preg_replace('/&iPaymentInfoId=\d+/', "", $return_url);
    $return_url .= $extraParams;
    $return_url = preg_replace("/&success=1/", "", $return_url);
    // echo $return_url; exit;
    header('Location:'.$return_url . '&CARD_SELECTED=Yes');
    exit;
}

$userPaymentInfoSql = "SELECT * FROM user_payment_info WHERE iMemberId = ".$iUserId." AND eUserType = '".$dbUserType."' AND vPaymentMethod = '".$USER_APP_PAYMENT_METHOD."' AND eStatus = 'Active' AND ePaymentEnv = '".$SYSTEM_PAYMENT_ENVIRONMENT."' ";
$userPaymentInfoData = $obj->MySQLSelect($userPaymentInfoSql);

if(!empty($iPaymentInfoId)) {
    $checkCardExist = $obj->MySQLSelect("SELECT iPaymentInfoId FROM user_payment_info WHERE iPaymentInfoId = '$iPaymentInfoId' AND eStatus != 'Deleted'");
    if(empty($checkCardExist)) {
        $iPaymentInfoId = "";
    }
}

if(isset($_REQUEST['cancel']) && $_REQUEST['cancel'] == "Yes" && empty($userPaymentInfoData)) {
    header('Location: ' . $failure_url . '?success=0&page_action=close');
    exit;
}
?>
<html>
    <head>
        <?php include_once($tconfig['tpanel_path'] . 'assets/libraries/webview/payment_head.php'); ?>
        <script src="https://js.stripe.com/v3/"></script>
    </head>
    <body class="stripe-pg">
        <div class="overlay">
            <div class="overlay__inner">
                <div class="overlay__content"><span class="spinner"></span></div>
            </div>
        </div>
        <?php $no_cards = 0;
if($page_type == "PAYMENT_LIST" && count($userPaymentInfoData) > 0) { ?>
        <div class="container py-4 custom-scroll-div">
            <?php
        include_once($tconfig['tpanel_path'] . 'assets/libraries/webview/payment_header.php');
    include $tconfig['tpanel_path'].'assets/libraries/webview/saved_cards.php';
    ?>
        </div>

        <?php include($tconfig['tpanel_path'].'assets/libraries/webview/delete_card_modal.php'); ?>

        <script type="text/javascript">
            $('#confirm_card').click(function() {
                $('[name="selected_iPaymentInfoId"]').val($('[name="saved_card"]:checked').val());                
                $('#select-card-form').submit();
                $('.overlay').show();
                $('body').css('overflow', 'hidden');
            });
        </script>
        <?php $no_cards = 1;
} ?>

        <?php if($page_type == "ADD_CARD" || $no_cards == 0) { ?>
        <div class="container py-4 custom-scroll-div">
            <?php include_once($tconfig['tpanel_path'] . 'assets/libraries/webview/payment_header.php'); ?>

            <div class="row justify-content-center custom-mt-header add-card-form">
                <div class="col-lg-5 col-md-6 col-sm-12 p-0">
                    <div class="card">
                        <div class="card-header p-0">
                            <!-- Credit card form content -->
                            <?php include_once($tconfig['tpanel_path'] . 'assets/libraries/webview/alert_msg.php'); ?>
                            
                            <div class="tab-content">
                                <!-- credit card info-->
                                <div id="credit-debit-card" class="tab-pane fade show active">
                                    <form id="payment-form" class="mb-0" action="" method="POST">
                                        <div class="form-group">
                                            <label for="cardholder-name">
                                                <h6 class="mb-0"><?= $languageLabelsArr['LBL_CARD_HOLDER_NAME_TXT'] ?></h6>
                                            </label>
                                            <div class="input-group">
                                                <!-- <div class="input-group-prepend">
                                                    <span class="input-group-text"><i class="fa fa-user"></i></span>
                                                </div> -->
                                                <input type="text" class="form-control" name="cardholder-name" id="cardholder-name" placeholder="<?= $languageLabelsArr['LBL_CARD_HOLDER_NAME_TXT'] ?>" value="<?= $full_name ?>" onkeypress="return alphabetsOnly(this, event)" tabindex="1">
                                            </div>
                                            <small id="card-name-error" class="text-danger mb-2 float-left"></small>
                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="form-group">
                                            <label for="cardNumber">
                                                <h6 class="mb-0"><?= $languageLabelsArr['LBL_CARD_NUMBER_TXT'] ?></h6>
                                            </label>

                                            <div class="input-group-prepend">
                                                <div id="cardNumber" tabindex="2"></div>
                                                <span class="input-group-text card-type">
                                                    <img src="<?= $tconfig['tsite_url'].'webimages/icons/DefaultImg/ic_card_default.svg' ?>" />
                                                </span>
                                            </div>
                                            <small id="card-num-error" class="text-danger mb-2 float-left"></small>
                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="row">
                                            <div class="col-sm-7">
                                                <div class="form-group">
                                                    <label>
                                                        <span class="hidden-xs">
                                                            <h6 class="mb-0"><?= $languageLabelsArr['LBL_EXPIRY'].' ('.$languageLabelsArr['LBL_EXP_MONTH_HINT_TXT'].' / '.$languageLabelsArr['LBL_EXP_YEAR_HINT_SHORT_TXT'].')' ?></h6>
                                                        </span>
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
                                            </div>
                                            <div class="col-sm-5">
                                                <div class="form-group mb-0">
                                                    <label>
                                                        <h6 class="mb-0"><?= $languageLabelsArr['LBL_CVV'] ?> <span class="cvv-info" data-toggle="tooltip" title="<?= $languageLabelsArr['LBL_CVV_INFO_TXT'] ?>"><i class="fa fa-question-circle d-inline"></i></span>
                                                        </h6>
                                                    </label>
                                                    <div id="cardCvc" tabindex="4"></div>
                                                    <div class="clearfix"></div>
                                                    <small id="card-cvc-error" class="text-danger mb-2 float-left"></small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer"> 
                                            <div class="row">
                                                <div class="col-sm-6 pr-2" style="width: 50%;">
                                                    <button type="button" class="btn btn-primary shadow-sm btn-block" id="cancel-btn"> <?= $languageLabelsArr['LBL_CANCEL_TXT'] ?> </button>
                                                </div>
                                                <div class="col-sm-6 pl-2" style="width: 50%;">
                                                    <button type="submit" class="btn btn-primary btn-block shadow-sm submit-card-btn" id="card-button"><?= $languageLabelsArr['LBL_SUBMIT_BUTTON_TXT'] ?> </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <!-- End -->
                            </div>
                        </div>
                    </div>
                </div>

                <?php $supported_cards = array('visa','mastercard','jcb','amex','discover','dinersclub','unionpay');
            $has_card = 0; ?>
                <div class="col-lg-5 col-md-6 col-sm-12">
                <?php
                include $tconfig['tpanel_path'].'assets/libraries/webview/supported_cards.php';

            if(strtoupper(SITE_TYPE) == "DEMO") {
                include $tconfig['tpanel_path'].'assets/libraries/webview/demo_cards.php';
            }
            ?>
                </div>

                <?php include $tconfig['tpanel_path'].'assets/libraries/webview/secure_section.php'; ?>
            </div>
        </div>
        
        <script type="text/javascript">
            // Create a Stripe client.
            var stripe = Stripe('<?= $STRIPE_PUBLISH_KEY ?>');
           
            // Create an instance of Elements.
            var elements = stripe.elements();

            // Custom styling can be passed to options when creating an Element.
            // (Note that this demo uses a wider set of styles than the guide below.)
            var style = {
                base: {
                    color: '#000000',
                    backgroundColor: '#ffffff',
                    fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                    fontSmoothing: 'antialiased',
                    fontSize: '16px',
                    padding: '.375rem .75rem',
                    '::placeholder': {
                        color: '#a1a1a1'
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

            var cardExpiryElement = elements.create('cardExpiry', {style: style, placeholder: '<?= addslashes($languageLabelsArr['LBL_EXP_MONTH_HINT_TXT']).' / '.addslashes($languageLabelsArr['LBL_EXP_YEAR_HINT_SHORT_TXT']) ?>'});
            cardExpiryElement.mount('#cardExpiry');

            var cardCvcElement = elements.create('cardCvc', {style: style, placeholder: 'XXX'});
            cardCvcElement.mount('#cardCvc');
            
            // Handle real-time validation errors from the card Element.
            cardNumberElement.addEventListener('change', function(event) {
                if (event.error) {
                    if(event.empty == true)
                    {
                        $('#card-num-error').text("<?= addslashes($languageLabelsArr['LBL_REQUIRED']) ?>");
                    }
                    else{
                        $('#card-num-error').text("<?= addslashes($languageLabelsArr['LBL_INVALIED']) ?>");
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
                    $('.card-type').find('img').attr('src', '<?= $tconfig['tsite_url'].'webimages/icons/DefaultImg/ic_' ?>'+cardType+'_system.svg');
                }
            });

            cardExpiryElement.addEventListener('change', function(event) {
                if (event.error) {
                    if(event.empty == true)
                    {
                        $('#card-exp-error').text("<?= addslashes($languageLabelsArr['LBL_REQUIRED']) ?>");
                    }
                    else{
                        $('#card-exp-error').text("<?= addslashes($languageLabelsArr['LBL_INVALIED']) ?>");
                    }
                } else {
                     $('#card-exp-error').text('');
                }
            });

            cardCvcElement.addEventListener('change', function(event) {
                if (event.error) {
                    if(event.empty == true)
                    {
                        $('#card-cvc-error').text("<?= addslashes($languageLabelsArr['LBL_REQUIRED']) ?>");
                    }
                    else{
                        $('#card-cvc-error').text("<?= addslashes($languageLabelsArr['LBL_INVALIED']) ?>");
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

            var cardholderName = document.getElementById('cardholder-name');
            // Handle form submission.
            var form = document.getElementById('payment-form');
            form.addEventListener('submit', function(event) {
                event.preventDefault();
                // $('.overlay').show();
                // $('body').css('overflow', 'hidden');
                var ownerInfo = {
                    billing_details: {
                        name: $('#cardholder-name').val(),
                        email: '<?= $userData[0]["vEmail"] ?>'
                    },
                };
                if($('#cardholder-name').val() == "")
                {
                    $('#card-name-error').text("<?= addslashes($languageLabelsArr['LBL_REQUIRED']) ?>");
                } else {
                     $('#card-name-error').text('');
                }
                $('#card-button').append(' <i class="fa fa-spinner fa-spin"></i>');
                $('#cardholder-name').prop('readonly', true);
                $('#card-button').prop('disabled', true);
               
                showOverlay();
                var clientSecret = '<?= $intent->client_secret ?>';
                stripe.confirmCardSetup(
                        clientSecret,
                        {
                            payment_method: {
                                card: cardNumberElement,
                                billing_details: {name: cardholderName.value, email: '<?= $userData[0]["vEmail"] ?>'}
                            }
                        }
                ).then(function (result) {
                    
                    if (result.error) {
                        hideOverlay();
                        // Display error.message in your UI.
                        cardNumberElement.update({disabled:false});
                        cardExpiryElement.update({disabled:false});
                        cardCvcElement.update({disabled:false});
                        $('#card-button').find('.fa-spinner').remove();
                        $('#cardholder-name').prop('readonly', false);
                        $('#card-button, #cancel-btn').prop('disabled', false);
                        $('#saved_card_tab').removeClass('disabled');

                        showSnackbar(result.error.message);
                        
                        return false;
                    } else {
                        if (result.setupIntent.status === 'succeeded') {
                            showOverlay();
                            // displayError.textContent = '';
                            // The setup has succeeded. Display a success message.
                            var form = document.getElementById('payment-form');
                            var hiddenInput = document.createElement('input');
                            hiddenInput.setAttribute('type', 'hidden');
                            hiddenInput.setAttribute('name', 'stripeSource');
                            hiddenInput.setAttribute('value', result.setupIntent.payment_method);
                            form.appendChild(hiddenInput);

                            form.submit();
                        } else {
                            hideOverlay();
                            // displayError.textContent = result.setupIntent.status;
                            $('#card-num-error').text(result.setupIntent.status);
                        }
                    }
                });
            });

            function stripeSourceHandler(source) {
                if($('#cardholder-name').val() == "")
                {
                    $('#card-name-error').text("<?= addslashes($languageLabelsArr['LBL_REQUIRED']) ?>");
                    return false;
                } else {
                     $('#card-name-error').text('');
                }
                // Insert the source ID into the form so it gets submitted to the server
                var form = document.getElementById('payment-form');
                var hiddenInput = document.createElement('input');
                hiddenInput.setAttribute('type', 'hidden');
                hiddenInput.setAttribute('name', 'stripeSource');
                hiddenInput.setAttribute('value', source.id);
                form.appendChild(hiddenInput);

                // Submit the form
                form.submit();
            }

            $('#cancel-btn').click(function() {
                backToPaymentList();
            });
        </script>
        <?php } ?>
        
        <script src="<?= $tconfig['tsite_url'] ?>assets/libraries/webview/pg_assets/js/custom.js"></script>
        <?php include_once($tconfig['tpanel_path'] . 'assets/libraries/webview/alert_msg.php'); ?>
    </body>
</html>