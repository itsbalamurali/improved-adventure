<?php
/**
 * This Class is used to perform actions for Stripe Payment Gateway.
 *
 * @package        class.stripe.php
 *
 */
require_once($tconfig['tpanel_path'] . 'assets/libraries/webview/stripe/init.php');

class CStripePayment {
	public static function getInstance(){
		return new CStripePayment();
	}

	public function __construct() {
		
	}

	public function execute($paymentData) {
		global $iServiceId, $STRIPE_SECRET_KEY, $tconfig;

		Stripe\Stripe::setApiKey($STRIPE_SECRET_KEY);
		
		$AMOUNT = round($paymentData['amount'], 2);
		$currency = $paymentData['vCurrency'];
		$custId = $paymentData['tCustomerId'];
		$cardToken = $paymentData['tCardToken'];
		$description = $paymentData['description'];
		$return_url = "";
		if(isset($paymentData['return_url']) && !empty($paymentData['return_url']))
		{
			$return_url = $paymentData['return_url'];	
		}

		$meta_data = array();
		$meta_data['iMemberId'] = $paymentData['iMemberId'];
		$meta_data['UserType'] = $paymentData['UserType'];
		if(!empty($paymentData['iOrderId'])) {
			$meta_data['iOrderId'] = $paymentData['iOrderId'];
		}
		
		/*if ($AMOUNT < 0.51) {
	        $currencycode = $paymentData['vCurrency'];
	        $currencySymbol = $paymentData['vSymbol'];
	        $currencyratio = $paymentData['vRatio'];
	        $vLangCode = $paymentData['vLangCode'];
	        $userLanguageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLangCode, "1", $iServiceId);
	        $responseArr["Action"] = "0";
	        $minValue = $currencySymbol . " " . strval(round(0.51 * $currencyratio, 2));
	        $responseArr['message'] = $userLanguageLabelsArr["LBL_REQUIRED_MINIMUM_AMOUT"] . " " . $minValue;
	        return $responseArr;
	    }*/

		try {
			$payment_method_arr = array(
				'amount' => ($AMOUNT * 100),
                'currency' => $currency,
                'customer' => $custId,
                'payment_method' => $cardToken,
                'off_session' => true,
                'confirm' => true,
                'description' => $description,
                'metadata' => $meta_data
			);

			if(!empty($paymentData['return_url']))
			{
				unset($payment_method_arr['off_session']);
				$payment_method_arr['return_url'] = $return_url;
			}

			if(!empty($paymentData['isAuthorize'])) {
				$payment_method_arr['capture_method'] = "manual";
			}

	        $payment_method = Stripe\PaymentIntent::create($payment_method_arr);

	        $result = $payment_method->jsonSerialize();

	        $payment_method_details = Stripe\PaymentMethod::retrieve($cardToken)->jsonSerialize();
	        // echo "<pre>"; print_r($payment_method_details); exit;
	        if(isset($result['next_action']))
        	{
        		if(isset($paymentData['eType']) && $paymentData['eType'] != "DeliverAll") {
        			$responseArr['Action'] = "1";
        			$responseArr['AUTHENTICATION_REQUIRED'] = "Yes";
        			$responseArr['AUTHENTICATION_URL'] = $result['next_action']['redirect_to_url']['url'];
        			return $responseArr;
        		}
        		else {
        			$redirect_to_url = $result['next_action']['redirect_to_url']['url'];
	        		header('Location: '.$redirect_to_url);
	        		exit;	
        		}
        	}

        	// echo "<pre>"; print_r($result); exit;
	        
	        if ($result['status'] == "succeeded" || $result['status'] == "requires_capture") {
	        	$responseArr['Action'] = "1";
	        	$responseArr['tPaymentTransactionId'] = $result['id'];
	        	$responseArr['tCardToken'] = $cardToken;
	        	$responseArr['vCardBrand'] = $payment_method_details['card']['brand'];
	        	$responseArr['last4digits'] = $payment_method_details['card']['last4'];
		        $responseArr['message'] = "success";
		        $responseArr['USER_APP_PAYMENT_METHOD'] = "Stripe";
		        return $responseArr;
            } else {
                $responseArr['Action'] = "0";
                $responseArr['status'] = "failed";
                return $responseArr;
            }

	    } catch (\Stripe\Exception\CardException $e) {
	    	// echo "<pre>"; print_r($e); exit;
	        $failure_msg = $e->getMessage();
	        $responseArr['Action'] = "0";
	        $responseArr['message'] = $failure_msg;
	        return $responseArr;
	        
	    } catch (\Stripe\Exception\ApiConnectionException $e) {
	        // Can't communicate to Stripe API
	        $failure_msg = $e->getMessage();
	        $responseArr['Action'] = "0";
	        $responseArr['message'] = $failure_msg;
	        return $responseArr;
	    } catch(Exception $e) {
	    	// echo "<pre>"; print_r($e); exit;
	        $failure_msg = $e->getMessage();
	        $responseArr['Action'] = "0";
	        $responseArr['message'] = $failure_msg;
	        return $responseArr;
	    }
	}

	public function capturePayment($paymentData) {
		global $STRIPE_SECRET_KEY, $tconfig;

        Stripe\Stripe::setApiKey($STRIPE_SECRET_KEY);

        $intent = Stripe\PaymentIntent::retrieve($paymentData['iAuthorizePaymentId']);
        $intent_data = $intent->jsonSerialize();
        $stripe_capture_amount = $intent_data['amount_capturable'];
        $capture_amount = $stripe_capture_amount;
        try {
            $intent->capture(['amount_to_capture' => $capture_amount]);
            $intent_result = Stripe\PaymentIntent::retrieve($paymentData['iAuthorizePaymentId'])->jsonSerialize();

            $responseArr['Action'] = "1";
        	$responseArr['tPaymentTransactionId'] = $intent_result['id'];
	        $responseArr['message'] = "success";
	        $responseArr['USER_APP_PAYMENT_METHOD'] = "Stripe";
	        return $responseArr;
	        
        } catch (Exception $e) {
            // echo "<pre>"; print_r($e); exit;
            $transMsg = "LBL_CHARGE_COLLECT_FAILED";
            $responseArr['Action'] = "0";
            $responseArr['message'] = $e->getMessage();
            return $responseArr;
        }
	}

	public function cancelAuthorizedPayment($paymentData) {
		global $STRIPE_SECRET_KEY, $tconfig;

        Stripe\Stripe::setApiKey($STRIPE_SECRET_KEY);

        $intent = \Stripe\PaymentIntent::retrieve($paymentData['iAuthorizePaymentId']);
        try {
            $intent->cancel(['cancellation_reason' => 'abandoned']);
            $responseArr['Action'] = "1";
            return $responseArr;
        }
        catch(Exception $e) {
            // echo "<pre>"; print_r($e); exit;
            $responseArr['Action'] = "0";
            return $responseArr;
        }
	}
}
