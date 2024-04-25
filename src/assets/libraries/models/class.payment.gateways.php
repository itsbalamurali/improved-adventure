<?php
/**
 * This Class is used to perform actions for Payment Gateways.
 *
 * @package        class.payment.gateways.php
 *
 */
class PaymentGateways {
	
	private $paymentDataArr;
	private $generalConfigPaymentArr;
	
	public static function getInstance(){
		return new PaymentGateways();
	}
	
	public function __construct() {
		global $CONFIG_OBJ;
		$this->generalConfigPaymentArr = $CONFIG_OBJ->getGeneralVarAll_Payment_Array();
	}
	
	public function execute($paymentDataArr) {
		global $obj, $tconfig, $USER_APP_PAYMENT_METHOD;
		$APP_PAYMENT_METHOD = $this->generalConfigPaymentArr['APP_PAYMENT_METHOD'];
		$DEFAULT_CURRENCY_CONVERATION_ENABLE = $this->generalConfigPaymentArr['DEFAULT_CURRENCY_CONVERATION_ENABLE'];
		$DEFAULT_CURRENCY_CONVERATION_CODE_RATIO = $this->generalConfigPaymentArr['DEFAULT_CURRENCY_CONVERATION_CODE_RATIO'];
		$DEFAULT_CURRENCY_CONVERATION_CODE = $this->generalConfigPaymentArr['DEFAULT_CURRENCY_CONVERATION_CODE'];

		$USER_APP_PAYMENT_METHOD = $APP_PAYMENT_METHOD;

		$userPaymentInfoSql = "";
		if(isset($paymentDataArr['iPaymentInfoId']) && $paymentDataArr['iPaymentInfoId'] > 0)
		{
			$userPaymentInfoSql = " ,(SELECT tCardToken FROM user_payment_info WHERE iPaymentInfoId = ".$paymentDataArr['iPaymentInfoId'].") as tCardToken ";
		}
		elseif (isset($paymentDataArr['tCardToken']) && $paymentDataArr['tCardToken'] != "") {
			$userPaymentInfoSql = " ,'".$paymentDataArr['tCardToken']."' as tCardToken ";
		}

		$countrySql = " LEFT JOIN country as co ON co.vCountryCode = u.vCountry ";
		if ($paymentDataArr['UserType'] == "Passenger") {
		    $sql = "SELECT u.*, CONCAT(u.vName,u.vLastName) as fullName,u.vCurrencyPassenger as vCurrency, c.Ratio, c.vSymbol, c.vName, co.vPaymentGateway " . $userPaymentInfoSql . " FROM `register_user` u LEFT JOIN currency c ON c.vName = u.vCurrencyPassenger $countrySql WHERE iUserId='" . $paymentDataArr['iMemberId'] . "'";
		} else {
		    $sql = "SELECT u.*, CONCAT(u.vName,u.vLastName) as fullName,u.vCurrencyDriver as vCurrency, c.Ratio, c.vSymbol, c.vName, co.vPaymentGateway " . $userPaymentInfoSql . " FROM `register_driver` u LEFT JOIN currency c ON c.vName = u.vCurrencyDriver $countrySql WHERE iDriverId='" . $paymentDataArr['iMemberId'] . "'";
		}

		$userData = $obj->MySQLSelect($sql);

		if(!empty($userData[0]['vPaymentGateway']))
	    {
	        $USER_APP_PAYMENT_METHOD = $userData[0]['vPaymentGateway'];
	    }

	    $paymentCustomerInfo = getPaymentCustomerInfo($paymentDataArr['iMemberId'], $paymentDataArr['UserType']);
	    $paymentDataArr['tCustomerId'] = $paymentCustomerInfo[0]['tCustomerId'];

	    if($userPaymentInfoSql == "" || empty($userData[0]['tCardToken']))
	    {
	    	$userPaymentInfoData = getPaymentDefaultCard($paymentDataArr['iMemberId'], $paymentDataArr['UserType']);
	     	$userData[0]['tCardToken'] = $userPaymentInfoData[0]['tCardToken'];
	    }

		$paymentDataArr['vCurrency'] = $userData[0]['vCurrency'];
		if($userData[0]['vCurrency'] != "" || $userData[0]['vCurrency'] == NULL)
		{
			$paymentDataArr['vRatio'] = $userData[0]['Ratio'];
			$paymentDataArr['vSymbol'] = $userData[0]['vSymbol'];
		}
		else{
            $defaultCurrencyData = $obj->MySQLSelect("SELECT vName,vSymbol,Ratio from currency WHERE eDefault = 'Yes'");
            $paymentDataArr['vCurrency'] = $defaultCurrencyData[0]['vName'];
            $paymentDataArr['vRatio'] = $defaultCurrencyData[0]['Ratio'];
			$paymentDataArr['vSymbol'] = $defaultCurrencyData[0]['vSymbol'];
		}

        $defaultCurrencyData = $obj->MySQLSelect("SELECT vName,vSymbol,Ratio from currency WHERE eDefault = 'Yes'");
        $paymentDataArr['vCurrency'] = $defaultCurrencyData[0]['vName'];
        $paymentDataArr['vRatio'] = $defaultCurrencyData[0]['Ratio'];
		$paymentDataArr['vSymbol'] = $defaultCurrencyData[0]['vSymbol'];

		if($userData[0]['vLang'] != "" || $userData[0]['vLang'] == NULL)
		{
			$paymentDataArr['vLangCode'] = $userData[0]['vLang'];
		}
		else{
            $defaultLangData = $obj->MySQLSelect("SELECT vCode from language_master WHERE eDefault = 'Yes'");
            $paymentDataArr['vLangCode'] = $defaultLangData[0]['vCode'];
		}

		if (strtoupper($DEFAULT_CURRENCY_CONVERATION_ENABLE) == 'YES' && !empty($DEFAULT_CURRENCY_CONVERATION_CODE_RATIO) && !empty($DEFAULT_CURRENCY_CONVERATION_CODE) && $DEFAULT_CURRENCY_CONVERATION_CODE_RATIO > 0) {
		    $DefaultConverationRatio = $DEFAULT_CURRENCY_CONVERATION_CODE_RATIO;
		    $price_new = $paymentDataArr['amount'];
		    $price_new = (round(($price_new * $DefaultConverationRatio), 2));
		    $currency = $DEFAULT_CURRENCY_CONVERATION_CODE;
		    $paymentDataArr['amount'] = $price_new;
		    $paymentDataArr['vCurrency'] = $currency;
		}

		$paymentDataArr['fullName'] = $userData[0]['fullName'];		
		$paymentDataArr['vEmail'] = $userData[0]['vEmail'];		
		$paymentDataArr['vPhone'] = $userData[0]['vPhone'];
		$paymentDataArr['tCardToken'] = $userData[0]['tCardToken'];
		$paymentDataArr['USER_APP_PAYMENT_METHOD'] = $USER_APP_PAYMENT_METHOD;
		
		$this->paymentDataArr = $paymentDataArr;
		
		switch ($USER_APP_PAYMENT_METHOD) {
	  		case 'Stripe': 
	  			require_once $tconfig['tpanel_path'] . 'assets/libraries/payment_gateways_files/class.stripe.php';
				$responseArr = (CStripePayment::getInstance())->execute($this->paymentDataArr);
	  			break;
	  		
	  		case 'Omise': 
	  			require_once $tconfig['tpanel_path'] . 'assets/libraries/payment_gateways_files/class.omise.php';
				$responseArr = (COmisePayment::getInstance())->execute($this->paymentDataArr);
	  			break;

	  		case 'Flutterwave': 
	  			require_once $tconfig['tpanel_path'] . 'assets/libraries/payment_gateways_files/class.flutterwave.php';
				$responseArr = (CFlutterwavePayment::getInstance())->execute($this->paymentDataArr);
	  			break;

	        case 'Paymaya': 
	            require_once $tconfig['tpanel_path'] . 'assets/libraries/payment_gateways_files/class.paymaya.php';
				$responseArr = (CPaymayaPayment::getInstance())->execute($this->paymentDataArr);
	            break;

	        case 'Xendit': 
	            require_once $tconfig['tpanel_path'] . 'assets/libraries/payment_gateways_files/class.xendit.php';
				$responseArr = (CXenditPayment::getInstance())->execute($this->paymentDataArr);
	            break;

	        case 'Paymentez': 
	            require_once $tconfig['tpanel_path'] . 'assets/libraries/payment_gateways_files/class.paymentez.php';
				$responseArr = (CPaymentezPayment::getInstance())->execute($this->paymentDataArr);
	            break;
	            
	        case 'Senangpay': 
	            require_once $tconfig['tpanel_path'] . 'assets/libraries/payment_gateways_files/class.senangpay.php';
				$responseArr = (CSenangpayPayment::getInstance())->execute($this->paymentDataArr);
	            break;

	        case 'OrangeMobileMoney': 
	            require_once $tconfig['tpanel_path'] . 'assets/libraries/payment_gateways_files/class.orangemobilemoney.php';
				$responseArr = (COrangeMobileMoneyPayment::getInstance())->execute($this->paymentDataArr);
	            break;

	        case 'Mpesa': 
	            require_once $tconfig['tpanel_path'] . 'assets/libraries/payment_gateways_files/class.mpesa.php';
				$responseArr = (CMpesaPayment::getInstance())->execute($this->paymentDataArr);
	            break;

	        case 'Iugu': 
	            require_once $tconfig['tpanel_path'] . 'assets/libraries/payment_gateways_files/class.iugu.php';
				$responseArr = (CIuguPayment::getInstance())->execute($this->paymentDataArr);
	            break;

	  		default:
	  			echo "Payment Gateway is not available or not configured. Please check your configurations.";
	  			break;
		}

		if($responseArr['Action'] == 1 && $responseArr['message'] == "success")
		{
			$payment_id = $this->paymentDetailsInsert($responseArr);
            $responseArr['payment_id'] = $payment_id;
		}
		
		return $responseArr;
	}
	
	public function paymentDetailsInsert($data)
	{
		global $obj, $CONFIG_OBJ;

		$allCurrentSystemPaymentDetails = $CONFIG_OBJ->getCurrentSystemPaymentDetails();
		$USER_APP_PAYMENT_METHOD = $this->generalConfigPaymentArr['APP_PAYMENT_METHOD'];
		if(isset($data['USER_APP_PAYMENT_METHOD']) && $data['USER_APP_PAYMENT_METHOD'] != "")
		{
			$USER_APP_PAYMENT_METHOD = $data['USER_APP_PAYMENT_METHOD'];
		}

		$payment_details_arr = array();
        foreach ($allCurrentSystemPaymentDetails as $zkey => $zValue) {
            if(startsWith(strtoupper($zkey), strtoupper($USER_APP_PAYMENT_METHOD)))
            {
                $payment_details_arr[$zkey] =  $zValue;
            }
        }
        $payment_details_arr['SYSTEM_PAYMENT_ENVIRONMENT'] = $this->generalConfigPaymentArr['SYSTEM_PAYMENT_ENVIRONMENT'];
        $payment_details_arr['APP_PAYMENT_MODE'] = $this->generalConfigPaymentArr['APP_PAYMENT_MODE'];
        $payment_details_arr['APP_PAYMENT_METHOD'] = $USER_APP_PAYMENT_METHOD;
        $payment_details_arr['COMMISION_DEDUCT_ENABLE'] = $this->generalConfigPaymentArr['COMMISION_DEDUCT_ENABLE'];
        $payment_details_arr['WALLET_MIN_BALANCE'] = $this->generalConfigPaymentArr['WALLET_MIN_BALANCE'];
        $payment_details_arr['PAYMENT_ENABLED'] = $this->generalConfigPaymentArr['PAYMENT_ENABLED'];
        $payment_details_arr['CARD_TOKEN'] = $data['tCardToken'];

        $tPaymentDetails = json_encode($payment_details_arr, JSON_UNESCAPED_UNICODE);


        $data_payments['tPaymentUserID'] = $data['tPaymentTransactionId'];
        $data_payments['vPaymentUserStatus'] = "approved";
        $data_payments['iAmountUser'] = ($this->paymentDataArr['amount'] != "") ? $this->paymentDataArr['amount'] : $data['amount'];
        $data_payments['tPaymentDetails'] = $tPaymentDetails;
        $data_payments['vPaymentMode'] = $this->generalConfigPaymentArr['SYSTEM_PAYMENT_ENVIRONMENT'];
        $data_payments['vPaymentMethod'] = $USER_APP_PAYMENT_METHOD;
        $data_payments['iUserId'] = $this->paymentDataArr['iMemberId'];
        $data_payments['eUserType'] = ($this->paymentDataArr['UserType'] == "Passenger" || $this->paymentDataArr['UserType'] == "Rider") ? 'Passenger' : 'Driver';

        $id = $obj->MySQLQueryPerform("payments", $data_payments, 'insert');
        return $id;
	}

	public function capturePayment($paymentDataArr) {
		global $obj, $tconfig, $USER_APP_PAYMENT_METHOD;
		
		$APP_PAYMENT_METHOD = $this->generalConfigPaymentArr['APP_PAYMENT_METHOD'];

		$USER_APP_PAYMENT_METHOD = $APP_PAYMENT_METHOD;

		$countrySql = " LEFT JOIN country as co ON co.vCountryCode = u.vCountry ";
		if ($paymentDataArr['UserType'] == "Passenger") {
		    $sql = "SELECT u.*, CONCAT(u.vName,u.vLastName) as fullName,u.vCurrencyPassenger as vCurrency, c.Ratio, c.vSymbol, c.vName, co.vPaymentGateway FROM `register_user` u LEFT JOIN currency c ON c.vName = u.vCurrencyPassenger $countrySql WHERE iUserId='" . $paymentDataArr['iMemberId'] . "'";
		} else {
		    $sql = "SELECT u.*, CONCAT(u.vName,u.vLastName) as fullName,u.vCurrencyDriver as vCurrency, c.Ratio, c.vSymbol, c.vName, co.vPaymentGateway FROM `register_driver` u LEFT JOIN currency c ON c.vName = u.vCurrencyDriver $countrySql WHERE iDriverId='" . $paymentDataArr['iMemberId'] . "'";
		}

		$userData = $obj->MySQLSelect($sql);

		if(!empty($userData[0]['vPaymentGateway']))
	    {
	        $USER_APP_PAYMENT_METHOD = $userData[0]['vPaymentGateway'];
	    }

	    
		if($userData[0]['vLang'] != "" || $userData[0]['vLang'] == NULL)
		{
			$paymentDataArr['vLangCode'] = $userData[0]['vLang'];
		}
		else{
            $defaultLangData = $obj->MySQLSelect("SELECT vCode from language_master WHERE eDefault = 'Yes'");
            $paymentDataArr['vLangCode'] = $defaultLangData[0]['vCode'];
		}

		$paymentDataArr['iAuthorizePaymentId'] = $paymentDataArr['iAuthorizePaymentId'];
		$paymentDataArr['USER_APP_PAYMENT_METHOD'] = $USER_APP_PAYMENT_METHOD;

		$this->paymentDataArr = $paymentDataArr;
		
		switch ($USER_APP_PAYMENT_METHOD) {
	  		case 'Stripe': 
	  			require_once $tconfig['tpanel_path'] . 'assets/libraries/payment_gateways_files/class.stripe.php';
				$responseArr = (CStripePayment::getInstance())->capturePayment($this->paymentDataArr);
	  			break;
	  		
	  		case 'Omise': 
	  			require_once $tconfig['tpanel_path'] . 'assets/libraries/payment_gateways_files/class.omise.php';
				$responseArr = (COmisePayment::getInstance())->capturePayment($this->paymentDataArr);
	  			break;

	  		case 'Flutterwave': 
	  			require_once $tconfig['tpanel_path'] . 'assets/libraries/payment_gateways_files/class.flutterwave.php';
				$responseArr = (CFlutterwavePayment::getInstance())->capturePayment($this->paymentDataArr);
	  			break;

	        case 'Paymaya': 
	            require_once $tconfig['tpanel_path'] . 'assets/libraries/payment_gateways_files/class.paymaya.php';
				$responseArr = (CPaymayaPayment::getInstance())->capturePayment($this->paymentDataArr);
	            break;

	        case 'Xendit': 
	            require_once $tconfig['tpanel_path'] . 'assets/libraries/payment_gateways_files/class.xendit.php';
				$responseArr = (CXenditPayment::getInstance())->capturePayment($this->paymentDataArr);
	            break;

	        case 'Paymentez': 
	            require_once $tconfig['tpanel_path'] . 'assets/libraries/payment_gateways_files/class.paymentez.php';
				$responseArr = (CPaymentezPayment::getInstance())->capturePayment($this->paymentDataArr);
	            break;
	            
	        case 'Senangpay': 
	            require_once $tconfig['tpanel_path'] . 'assets/libraries/payment_gateways_files/class.senangpay.php';
				$responseArr = (CSenangpayPayment::getInstance())->capturePayment($this->paymentDataArr);
	            break;

	        case 'OrangeMobileMoney': 
	            require_once $tconfig['tpanel_path'] . 'assets/libraries/payment_gateways_files/class.orangemobilemoney.php';
				$responseArr = (COrangeMobileMoneyPayment::getInstance())->capturePayment($this->paymentDataArr);
	            break;

	        case 'Mpesa': 
	            require_once $tconfig['tpanel_path'] . 'assets/libraries/payment_gateways_files/class.mpesa.php';
				$responseArr = (CMpesaPayment::getInstance())->capturePayment($this->paymentDataArr);
	            break;

	        case 'Iugu': 
	            require_once $tconfig['tpanel_path'] . 'assets/libraries/payment_gateways_files/class.iugu.php';
				$responseArr = (CIuguPayment::getInstance())->capturePayment($this->paymentDataArr);
	            break;

	  		default:
	  			echo "Payment Gateway is not available or not configured. Please check your configurations.";
	  			break;
		}
		
		return $responseArr;
	}

	public function cancelAuthorizedPayment($paymentDataArr) {
		global $obj, $tconfig, $USER_APP_PAYMENT_METHOD;
		
		$APP_PAYMENT_METHOD = $this->generalConfigPaymentArr['APP_PAYMENT_METHOD'];

		$USER_APP_PAYMENT_METHOD = $APP_PAYMENT_METHOD;

		$countrySql = " LEFT JOIN country as co ON co.vCountryCode = u.vCountry ";
		if ($paymentDataArr['UserType'] == "Passenger") {
		    $sql = "SELECT u.*, CONCAT(u.vName,u.vLastName) as fullName,u.vCurrencyPassenger as vCurrency, c.Ratio, c.vSymbol, c.vName, co.vPaymentGateway FROM `register_user` u LEFT JOIN currency c ON c.vName = u.vCurrencyPassenger $countrySql WHERE iUserId='" . $paymentDataArr['iMemberId'] . "'";
		} else {
		    $sql = "SELECT u.*, CONCAT(u.vName,u.vLastName) as fullName,u.vCurrencyDriver as vCurrency, c.Ratio, c.vSymbol, c.vName, co.vPaymentGateway FROM `register_driver` u LEFT JOIN currency c ON c.vName = u.vCurrencyDriver $countrySql WHERE iDriverId='" . $paymentDataArr['iMemberId'] . "'";
		}

		$userData = $obj->MySQLSelect($sql);

		if(!empty($userData[0]['vPaymentGateway']))
	    {
	        $USER_APP_PAYMENT_METHOD = $userData[0]['vPaymentGateway'];
	    }

	    
		if($userData[0]['vLang'] != "" || $userData[0]['vLang'] == NULL)
		{
			$paymentDataArr['vLangCode'] = $userData[0]['vLang'];
		}
		else{
            $defaultLangData = $obj->MySQLSelect("SELECT vCode from language_master WHERE eDefault = 'Yes'");
            $paymentDataArr['vLangCode'] = $defaultLangData[0]['vCode'];
		}

		$paymentDataArr['iAuthorizePaymentId'] = $paymentDataArr['iAuthorizePaymentId'];
		$paymentDataArr['USER_APP_PAYMENT_METHOD'] = $USER_APP_PAYMENT_METHOD;

		$this->paymentDataArr = $paymentDataArr;
		
		switch ($USER_APP_PAYMENT_METHOD) {
	  		case 'Stripe': 
	  			require_once $tconfig['tpanel_path'] . 'assets/libraries/payment_gateways_files/class.stripe.php';
				$responseArr = (CStripePayment::getInstance())->cancelAuthorizedPayment($this->paymentDataArr);
	  			break;
	  		
	  		case 'Omise': 
	  			require_once $tconfig['tpanel_path'] . 'assets/libraries/payment_gateways_files/class.omise.php';
				$responseArr = (COmisePayment::getInstance())->cancelAuthorizedPayment($this->paymentDataArr);
	  			break;

	  		case 'Flutterwave': 
	  			require_once $tconfig['tpanel_path'] . 'assets/libraries/payment_gateways_files/class.flutterwave.php';
				$responseArr = (CFlutterwavePayment::getInstance())->cancelAuthorizedPayment($this->paymentDataArr);
	  			break;

	        case 'Paymaya': 
	            require_once $tconfig['tpanel_path'] . 'assets/libraries/payment_gateways_files/class.paymaya.php';
				$responseArr = (CPaymayaPayment::getInstance())->cancelAuthorizedPayment($this->paymentDataArr);
	            break;

	        case 'Xendit': 
	            require_once $tconfig['tpanel_path'] . 'assets/libraries/payment_gateways_files/class.xendit.php';
				$responseArr = (CXenditPayment::getInstance())->cancelAuthorizedPayment($this->paymentDataArr);
	            break;

	        case 'Paymentez': 
	            require_once $tconfig['tpanel_path'] . 'assets/libraries/payment_gateways_files/class.paymentez.php';
				$responseArr = (CPaymentezPayment::getInstance())->cancelAuthorizedPayment($this->paymentDataArr);
	            break;
	            
	        case 'Senangpay': 
	            require_once $tconfig['tpanel_path'] . 'assets/libraries/payment_gateways_files/class.senangpay.php';
				$responseArr = (CSenangpayPayment::getInstance())->cancelAuthorizedPayment($this->paymentDataArr);
	            break;

	        case 'OrangeMobileMoney': 
	            require_once $tconfig['tpanel_path'] . 'assets/libraries/payment_gateways_files/class.orangemobilemoney.php';
				$responseArr = (COrangeMobileMoneyPayment::getInstance())->cancelAuthorizedPayment($this->paymentDataArr);
	            break;

	        case 'Mpesa': 
	            require_once $tconfig['tpanel_path'] . 'assets/libraries/payment_gateways_files/class.mpesa.php';
				$responseArr = (CMpesaPayment::getInstance())->cancelAuthorizedPayment($this->paymentDataArr);
	            break;

	        case 'Iugu': 
	            require_once $tconfig['tpanel_path'] . 'assets/libraries/payment_gateways_files/class.iugu.php';
				$responseArr = (CIuguPayment::getInstance())->cancelAuthorizedPayment($this->paymentDataArr);
	            break;

	  		default:
	  			echo "Payment Gateway is not available or not configured. Please check your configurations.";
	  			break;
		}
		
		return $responseArr;
	}
}

?>