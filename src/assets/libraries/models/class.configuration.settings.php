<?php
require_once(TPATH_CLASS . 'include_header.php');

use Kesk\Web\Common\EventMessageCls;
use Kesk\Web\Common\SystemInfo;

class ConfigurationSettings {
    
    public function __construct() {
        $this->buildConfigurationSettings();
        $this->initEventMessageObj();
    }
    /**
     * @access    public
     * @Print Element input type
     */
    
    private function buildConfigurationSettings() {
        extract(setCurrentScopeVars(), EXTR_REFS | EXTR_OVERWRITE);
        if (!empty($generalSystemConfigDataArr) && count($generalSystemConfigDataArr) > 0) {
            return true;
        } 
        //Added By HJ On 27-07-2020 For Store configurations Data into Cache Start
        $configurationsApcKey = md5($cacheKeysArr['configurations']);
        $getConfigCacheData = $oCache->getData($configurationsApcKey);
        if(!empty($getConfigCacheData) && count($getConfigCacheData) > 0){
           $wri_ures_config = $getConfigCacheData;
        }else {
            $wri_ures_config = $obj->MySQLSelect("SELECT iSettingId,vName,TRIM(vValue) as vValue,tSelectVal,eFor FROM configurations");
            $setConfigCacheData = $oCache->setData($configurationsApcKey, $wri_ures_config);
        }
        
        //Added By HJ On 27-07-2020 For Store configurations Data into Cache End
        $generalConfigArr = array();
        for ($i = 0; $i < count($wri_ures_config); $i++) {
            $vName = $wri_ures_config[$i]["vName"];
            $vValue = $wri_ures_config[$i]["vValue"];
            $tSelectVal = $wri_ures_config[$i]["tSelectVal"];
            $eFor = $wri_ures_config[$i]["eFor"];
            if ($vName == "APP_TYPE") {
                $vValue = APP_TYPE;
                $wri_ures_config[$i]["vValue"] = $vValue;
            }
            if ($vName == "PACKAGE_TYPE") {
                $vValue = PACKAGE_TYPE;
                $wri_ures_config[$i]["vValue"] = $vValue;
            }
            if ($vName == "APP_PAYMENT_MODE" && !empty($_REQUEST['CUS_APP_PAYMENT_MODE'])) {
                $vValue = $_REQUEST['CUS_APP_PAYMENT_MODE'];
                $wri_ures_config[$i]["vValue"] = $vValue;
            }
            if ($vName == "APP_PAYMENT_METHOD" && !empty($_REQUEST['CUS_APP_PAYMENT_METHOD'])) {
                $vValue = $_REQUEST['CUS_APP_PAYMENT_METHOD'];
                $wri_ures_config[$i]["vValue"] = $vValue;
            }
            if ($vName == "SYSTEM_PAYMENT_FLOW" && !empty($_REQUEST['CUS_SYSTEM_PAYMENT_FLOW'])) {
                $vValue = $_REQUEST['CUS_SYSTEM_PAYMENT_FLOW'];
                $wri_ures_config[$i]["vValue"] = $vValue;
            }
            if ($vName == "ENABLE_CORPORATE_PROFILE" && (strtoupper(APP_TYPE) == "UBERX" || strtoupper(APP_TYPE) == "DELIVERY" || strtoupper(ONLYDELIVERALL) == "YES")) {
                $vValue = "No";
                $wri_ures_config[$i]["vValue"] = $vValue;
            }
            if($vName == "COPYRIGHT_TEXT" || $vName == "COPYRIGHT_TEXT_ADMIN") {
                $vValue = str_replace("#YEAR#", date('Y'), $vValue);
                $wri_ures_config[$i]["vValue"] = $vValue;
            }
            
            if((((strtoupper(RIDE_ENABLED) == "NO" && $eFor == "Ride") 
                    || (strtoupper(DELIVERY_ENABLED) == "NO" && ($eFor == "Delivery" || $eFor == "Multi-Delivery")) 
                    || (strtoupper(UFX_ENABLED) == "NO" && $eFor == "UberX") 
                    || (strtoupper(DELIVERALL_ENABLED) == "NO" && $eFor == "DeliverAll")
                    || (strtoupper(RIDE_ENABLED) == "NO" && strtoupper(DELIVERY_ENABLED) == "NO" && strtoupper(UFX_ENABLED) == "NO" && $eFor == "Ride,Delivery,UberX")
                ) && $tSelectVal == "Yes,No") || (strtoupper(VC_ENABLED) == "NO" && $vName == "ENABLE_VIDEO_CONSULTING_SERVICE") || (strtoupper(BIDDING_ENABLED) == "NO" && $vName == "ENABLE_BIDDING_SERVICES")) {
                $vValue = "No";
                $wri_ures_config[$i]["vValue"] = $vValue;   
            }

            $$vName = $vValue;
            $generalConfigArr[$vName] = $vValue;
        }

        if(empty($getSetupCacheData) || count($getSetupCacheData) == 0) {
            $setupInfoApcKey = md5("setup_info");
            $getSetupCacheData = $oCache->getData($setupInfoApcKey);
            if(!empty($getSetupCacheData) && count($getSetupCacheData) > 0){
                $setupInfoDataArr = $getSetupCacheData;
            }else{
                $setupInfoDataArr = $obj->MySQLSelect("SELECT * FROM setup_info LIMIT 0,1");
                $setSetupCacheData = $oCache->setData($setupInfoApcKey, $setupInfoDataArr);
            }
        } else {
            $setupInfoDataArr = $getSetupCacheData;
        }
        

        $tAppPackageData = json_decode($setupInfoDataArr[0]['tAppPackageData'], true);
        foreach ($tAppPackageData as $pkey => $pValue) {
            $generalConfigArr[$pkey] = $pValue;
        }

        $generalConfigArr['SETUP_INFO_DATA_ARR'] = $setupInfoDataArr;
        
        $generalSystemConfigGeneralDataArr = $generalConfigArr;
        $generalConfigPaymentArr = array();
        //Added By HJ On 27-07-2020 For Store configurations_payment Data into Cache Start
        $configurationsPayApcKey = md5($cacheKeysArr['configurations_payment']);
        $getConfigPayCacheData = $oCache->getData($configurationsPayApcKey);
        if(!empty($getConfigPayCacheData) && count($getConfigPayCacheData) > 0){
           $wri_ures_config_pay = $getConfigPayCacheData;
        }else{
            $wri_ures_config_pay = $obj->MySQLSelect("SELECT vName,TRIM(vValue) as vValue FROM configurations_payment");
            $setConfigPayCacheData = $oCache->setData($configurationsPayApcKey, $wri_ures_config_pay);
        }
        //Added By HJ On 27-07-2020 For Store configurations_payment Data into Cache End
        for ($i = 0; $i < count($wri_ures_config_pay); $i++) {
            $vName = $wri_ures_config_pay[$i]["vName"];
            $vValue = $wri_ures_config_pay[$i]["vValue"];

            if ($vName == "APP_PAYMENT_MODE" && !empty($_REQUEST['CUS_APP_PAYMENT_MODE'])) {
                $vValue = $_REQUEST['CUS_APP_PAYMENT_MODE'];
                $wri_ures_config_pay[$i]["vValue"] = $vValue;
            }
            if ($vName == "APP_PAYMENT_METHOD" && !empty($_REQUEST['CUS_APP_PAYMENT_METHOD'])) {
                $vValue = $_REQUEST['CUS_APP_PAYMENT_METHOD'];
                $wri_ures_config_pay[$i]["vValue"] = $vValue;
            }
            if ($vName == "SYSTEM_PAYMENT_FLOW" && !empty($_REQUEST['CUS_SYSTEM_PAYMENT_FLOW'])) {
                $vValue = $_REQUEST['CUS_SYSTEM_PAYMENT_FLOW'];
                $wri_ures_config_pay[$i]["vValue"] = $vValue;
            }

            $$vName = $vValue;
            if ((strpos($wri_ures_config_pay[$i]["vName"], '_SANDBOX') !== false) == false && (strpos($wri_ures_config_pay[$i]["vName"], '_LIVE') !== false) == false) {
                $generalConfigPaymentArr[$wri_ures_config_pay[$i]["vName"]] = $wri_ures_config_pay[$i]["vValue"];
            }
        }
        $generalConfigPaymentArr['SYSTEM_PAYMENT_ENVIRONMENT'] = $SYSTEM_PAYMENT_ENVIRONMENT;
        $generalConfigPaymentArr['APP_PAYMENT_MODE'] = $APP_PAYMENT_MODE;
        $generalConfigPaymentArr['APP_PAYMENT_METHOD'] = $APP_PAYMENT_METHOD;
        $generalConfigPaymentArr['WALLET_MIN_BALANCE'] = $WALLET_MIN_BALANCE;
        $generalConfigPaymentArr['COMMISION_DEDUCT_ENABLE'] = $COMMISION_DEDUCT_ENABLE;
        $generalConfigPaymentArr['COMMISION_DEDUCT_ENABLE_DELIVERALL'] = $COMMISION_DEDUCT_ENABLE_DELIVERALL;
        $generalConfigPaymentArr['PAYMENT_ENABLED'] = $PAYMENT_ENABLED;
        $generalConfigPaymentArr['BRAINTREE_CHARGE_AMOUNT'] = $BRAINTREE_CHARGE_AMOUNT;
        $generalConfigPaymentArr['ADYEN_CHARGE_AMOUNT'] = $ADYEN_CHARGE_AMOUNT;
        $generalConfigPaymentArr['FLUTTERWAVE_CHARGE_AMOUNT'] = $FLUTTERWAVE_CHARGE_AMOUNT;
        $generalConfigPaymentArr['CREDIT_TO_WALLET_ENABLE'] = $CREDIT_TO_WALLET_ENABLE;
        if ($SYSTEM_PAYMENT_ENVIRONMENT == "Test") {
            $generalConfigPaymentArr['STRIPE_SECRET_KEY'] = $STRIPE_SECRET_KEY_SANDBOX;
            $generalConfigPaymentArr['STRIPE_PUBLISH_KEY'] = $STRIPE_PUBLISH_KEY_SANDBOX;
            $generalConfigPaymentArr['BRAINTREE_TOKEN_KEY'] = $BRAINTREE_TOKEN_KEY_SANDBOX;
            $generalConfigPaymentArr['BRAINTREE_ENVIRONMENT'] = $BRAINTREE_ENVIRONMENT_SANDBOX;
            $generalConfigPaymentArr['BRAINTREE_MERCHANT_ID'] = $BRAINTREE_MERCHANT_ID_SANDBOX;
            $generalConfigPaymentArr['BRAINTREE_PUBLIC_KEY'] = $BRAINTREE_PUBLIC_KEY_SANDBOX;
            $generalConfigPaymentArr['BRAINTREE_PRIVATE_KEY'] = $BRAINTREE_PRIVATE_KEY_SANDBOX;
            $generalConfigPaymentArr['PAYMAYA_API_URL'] = $PAYMAYA_API_URL_SANDBOX;
            $generalConfigPaymentArr['PAYMAYA_SECRET_KEY'] = $PAYMAYA_SECRET_KEY_SANDBOX;
            $generalConfigPaymentArr['PAYMAYA_PUBLISH_KEY'] = $PAYMAYA_PUBLISH_KEY_SANDBOX;
            $generalConfigPaymentArr['PAYMAYA_CHECKOUT_PUBLISH_KEY'] = $PAYMAYA_CHECKOUT_PUBLISH_KEY_SANDBOX;
            $generalConfigPaymentArr['PAYMAYA_ENVIRONMENT_MODE'] = $PAYMAYA_ENVIRONMENT_MODE_SANDBOX;
            $generalConfigPaymentArr['OMISE_SECRET_KEY'] = $OMISE_SECRET_KEY_SANDBOX;
            $generalConfigPaymentArr['OMISE_PUBLIC_KEY'] = $OMISE_PUBLIC_KEY_SANDBOX;
            $generalConfigPaymentArr['ADYEN_MERCHANT_ACCOUNT'] = $ADYEN_MERCHANT_ACCOUNT_SANDBOX;
            $generalConfigPaymentArr['ADYEN_USER_NAME'] = $ADYEN_USER_NAME_SANDBOX;
            $generalConfigPaymentArr['ADYEN_PASSWORD'] = $ADYEN_PASSWORD_SANDBOX;
            $generalConfigPaymentArr['ADYEN_API_URL'] = $ADYEN_API_URL_SANDBOX;
            $generalConfigPaymentArr['XENDIT_SECRET_KEY'] = $XENDIT_SECRET_KEY_SANDBOX;
            $generalConfigPaymentArr['XENDIT_PUBLIC_KEY'] = $XENDIT_PUBLIC_KEY_SANDBOX;
            $generalConfigPaymentArr['FLUTTERWAVE_API_URL'] = $FLUTTERWAVE_API_URL_SANDBOX;
            $generalConfigPaymentArr['FLUTTERWAVE_PUBLIC_KEY'] = $FLUTTERWAVE_PUBLIC_KEY_SANDBOX;
            $generalConfigPaymentArr['FLUTTERWAVE_SECRET_KEY'] = $FLUTTERWAVE_SECRET_KEY_SANDBOX;
            $generalConfigPaymentArr['FLUTTERWAVE_ENCRYPTION_KEY'] = $FLUTTERWAVE_ENCRYPTION_KEY_SANDBOX;
            $generalConfigPaymentArr['FLUTTERWAVE_STAGING_URL'] = $FLUTTERWAVE_STAGING_URL_SANDBOX;

            $generalConfigPaymentArr['PAYFORT_API_URL_TOKEN'] = $PAYFORT_API_URL_TOKEN_SANDBOX;
            $generalConfigPaymentArr['PAYFORT_API_URL_CHARGE'] = $PAYFORT_API_URL_CHARGE_SANDBOX;
            $generalConfigPaymentArr['PAYFORT_MERCHANT_ID'] = $PAYFORT_MERCHANT_ID_SANDBOX;
            $generalConfigPaymentArr['PAYFORT_ACCESS_CODE'] = $PAYFORT_ACCESS_CODE_SANDBOX;
            $generalConfigPaymentArr['PAYFORT_SHA_TYPE'] = $PAYFORT_SHA_TYPE_SANDBOX;
            $generalConfigPaymentArr['PAYFORT_SHA_REQUEST_PHRASE'] = $PAYFORT_SHA_REQUEST_PHRASE_SANDBOX;
            $generalConfigPaymentArr['PAYFORT_SHA_RESPONSE_PHRASE'] = $PAYFORT_SHA_RESPONSE_PHRASE_SANDBOX;

            $generalConfigPaymentArr['PAYULATAM_API_KEY'] = $PAYULATAM_API_KEY_SANDBOX;
            $generalConfigPaymentArr['PAYULATAM_API_LOGIN'] = $PAYULATAM_API_LOGIN_SANDBOX;
            $generalConfigPaymentArr['PAYULATAM_MERCHANT_ID'] = $PAYULATAM_MERCHANT_ID_SANDBOX;
            $generalConfigPaymentArr['PAYULATAM_ACCOUNT_ID'] = $PAYULATAM_ACCOUNT_ID_SANDBOX;
            $generalConfigPaymentArr['PAYULATAM_PAYMENT_URL'] = $PAYULATAM_PAYMENT_URL_SANDBOX;

            $generalConfigPaymentArr['PAYMENTEZ_API_URL'] = $PAYMENTEZ_API_URL_SANDBOX;
            $generalConfigPaymentArr['PAYMENTEZ_CLIENT_APP_CODE'] = $PAYMENTEZ_CLIENT_APP_CODE_SANDBOX;
            $generalConfigPaymentArr['PAYMENTEZ_CLIENT_APP_KEY'] = $PAYMENTEZ_CLIENT_APP_KEY_SANDBOX;
            $generalConfigPaymentArr['PAYMENTEZ_SERVER_APP_CODE'] = $PAYMENTEZ_SERVER_APP_CODE_SANDBOX;
            $generalConfigPaymentArr['PAYMENTEZ_SERVER_APP_KEY'] = $PAYMENTEZ_SERVER_APP_KEY_SANDBOX;

            $generalConfigPaymentArr['SENANGPAY_TOKEN_PAYMENT_URL'] = $SENANGPAY_TOKEN_PAYMENT_URL_SANDBOX;
            $generalConfigPaymentArr['SENANGPAY_GENERATE_TOKEN_URL'] = $SENANGPAY_GENERATE_TOKEN_URL_SANDBOX;
            $generalConfigPaymentArr['SENANGPAY_SECRETKEY'] = $SENANGPAY_SECRETKEY_SANDBOX;
            $generalConfigPaymentArr['SENANGPAY_MERCHANT_ID'] = $SENANGPAY_MERCHANT_ID_SANDBOX;
            $generalConfigPaymentArr['SENANGPAY_PAYMENT_URL'] = $SENANGPAY_PAYMENT_URL_SANDBOX;

            $generalConfigPaymentArr['SERFINSA_MERCHANT_ID'] = $SERFINSA_MERCHANT_ID_SANDBOX;
            $generalConfigPaymentArr['SERFINSA_API_URL'] = $SERFINSA_API_URL_SANDBOX;

            $generalConfigPaymentArr['ORANGEMOBILEMONEY_API_USERNAME'] = $ORANGEMOBILEMONEY_API_USERNAME_SANDBOX;
            $generalConfigPaymentArr['ORANGEMOBILEMONEY_API_PASSWORD'] = $ORANGEMOBILEMONEY_API_PASSWORD_SANDBOX;
            $generalConfigPaymentArr['ORANGEMOBILEMONEY_MERCHANT_ID'] = $ORANGEMOBILEMONEY_MERCHANT_ID_SANDBOX;
            $generalConfigPaymentArr['ORANGEMOBILEMONEY_API_URL'] = $ORANGEMOBILEMONEY_API_URL_SANDBOX;
            $generalConfigPaymentArr['ORANGEMOBILEMONEY_OTP_GENERATE'] = $ORANGEMOBILEMONEY_OTP_GENERATE_SANDBOX;

            $generalConfigPaymentArr['MPESA_FLUTTERWAVE_PUBLIC_KEY'] = $MPESA_FLUTTERWAVE_PUBLIC_KEY_SANDBOX;
            $generalConfigPaymentArr['MPESA_FLUTTERWAVE_SECRET_KEY'] = $MPESA_FLUTTERWAVE_SECRET_KEY_SANDBOX;
            $generalConfigPaymentArr['MPESA_FLUTTERWAVE_ENCRYPTION_KEY'] = $MPESA_FLUTTERWAVE_ENCRYPTION_KEY_SANDBOX;
            $generalConfigPaymentArr['MPESA_CONSUMER_KEY'] = $MPESA_CONSUMER_KEY_SANDBOX;
            $generalConfigPaymentArr['MPESA_CONSUMER_SECRET'] = $MPESA_CONSUMER_SECRET_SANDBOX;
            $generalConfigPaymentArr['MPESA_SHORT_CODE'] = $MPESA_SHORT_CODE_SANDBOX;

            $generalConfigPaymentArr['IUGU_ACCOUNT_ID'] = $IUGU_ACCOUNT_ID_SANDBOX;
            $generalConfigPaymentArr['IUGU_API_KEY'] = $IUGU_API_KEY_SANDBOX;
        } else {
            $generalConfigPaymentArr['STRIPE_SECRET_KEY'] = $STRIPE_SECRET_KEY_LIVE;
            $generalConfigPaymentArr['STRIPE_PUBLISH_KEY'] = $STRIPE_PUBLISH_KEY_LIVE;
            $generalConfigPaymentArr['BRAINTREE_TOKEN_KEY'] = $BRAINTREE_TOKEN_KEY_LIVE;
            $generalConfigPaymentArr['BRAINTREE_ENVIRONMENT'] = $BRAINTREE_ENVIRONMENT_LIVE;
            $generalConfigPaymentArr['BRAINTREE_MERCHANT_ID'] = $BRAINTREE_MERCHANT_ID_LIVE;
            $generalConfigPaymentArr['BRAINTREE_PUBLIC_KEY'] = $BRAINTREE_PUBLIC_KEY_LIVE;
            $generalConfigPaymentArr['BRAINTREE_PRIVATE_KEY'] = $BRAINTREE_PRIVATE_KEY_LIVE;
            $generalConfigPaymentArr['PAYMAYA_API_URL'] = $PAYMAYA_API_URL_LIVE;
            $generalConfigPaymentArr['PAYMAYA_SECRET_KEY'] = $PAYMAYA_SECRET_KEY_LIVE;
            $generalConfigPaymentArr['PAYMAYA_PUBLISH_KEY'] = $PAYMAYA_PUBLISH_KEY_LIVE;
            $generalConfigPaymentArr['PAYMAYA_CHECKOUT_PUBLISH_KEY'] = $PAYMAYA_CHECKOUT_PUBLISH_KEY_LIVE;
            $generalConfigPaymentArr['PAYMAYA_ENVIRONMENT_MODE'] = $PAYMAYA_ENVIRONMENT_MODE_LIVE;
            $generalConfigPaymentArr['OMISE_SECRET_KEY'] = $OMISE_SECRET_KEY_LIVE;
            $generalConfigPaymentArr['OMISE_PUBLIC_KEY'] = $OMISE_PUBLIC_KEY_LIVE;
            $generalConfigPaymentArr['ADYEN_MERCHANT_ACCOUNT'] = $ADYEN_MERCHANT_ACCOUNT_LIVE;
            $generalConfigPaymentArr['ADYEN_USER_NAME'] = $ADYEN_USER_NAME_LIVE;
            $generalConfigPaymentArr['ADYEN_PASSWORD'] = $ADYEN_PASSWORD_LIVE;
            $generalConfigPaymentArr['ADYEN_API_URL'] = $ADYEN_API_URL_LIVE;
            $generalConfigPaymentArr['XENDIT_SECRET_KEY'] = $XENDIT_SECRET_KEY_LIVE;
            $generalConfigPaymentArr['XENDIT_PUBLIC_KEY'] = $XENDIT_PUBLIC_KEY_LIVE;
            $generalConfigPaymentArr['FLUTTERWAVE_API_URL'] = $FLUTTERWAVE_API_URL_LIVE;
            $generalConfigPaymentArr['FLUTTERWAVE_PUBLIC_KEY'] = $FLUTTERWAVE_PUBLIC_KEY_LIVE;
            $generalConfigPaymentArr['FLUTTERWAVE_SECRET_KEY'] = $FLUTTERWAVE_SECRET_KEY_LIVE;
            $generalConfigPaymentArr['FLUTTERWAVE_ENCRYPTION_KEY'] = $FLUTTERWAVE_ENCRYPTION_KEY_LIVE;
            $generalConfigPaymentArr['FLUTTERWAVE_STAGING_URL'] = $FLUTTERWAVE_STAGING_URL_LIVE;

            $generalConfigPaymentArr['PAYFORT_API_URL_TOKEN'] = $PAYFORT_API_URL_TOKEN_LIVE;
            $generalConfigPaymentArr['PAYFORT_API_URL_CHARGE'] = $PAYFORT_API_URL_CHARGE_LIVE;
            $generalConfigPaymentArr['PAYFORT_MERCHANT_ID'] = $PAYFORT_MERCHANT_ID_LIVE;
            $generalConfigPaymentArr['PAYFORT_ACCESS_CODE'] = $PAYFORT_ACCESS_CODE_LIVE;
            $generalConfigPaymentArr['PAYFORT_SHA_TYPE'] = $PAYFORT_SHA_TYPE_LIVE;
            $generalConfigPaymentArr['PAYFORT_SHA_REQUEST_PHRASE'] = $PAYFORT_SHA_REQUEST_PHRASE_LIVE;
            $generalConfigPaymentArr['PAYFORT_SHA_RESPONSE_PHRASE'] = $PAYFORT_SHA_RESPONSE_PHRASE_LIVE;

            $generalConfigPaymentArr['PAYULATAM_API_KEY'] = $PAYULATAM_API_KEY_LIVE;
            $generalConfigPaymentArr['PAYULATAM_API_LOGIN'] = $PAYULATAM_API_LOGIN_LIVE;
            $generalConfigPaymentArr['PAYULATAM_MERCHANT_ID'] = $PAYULATAM_MERCHANT_ID_LIVE;
            $generalConfigPaymentArr['PAYULATAM_ACCOUNT_ID'] = $PAYULATAM_ACCOUNT_ID_LIVE;
            $generalConfigPaymentArr['PAYULATAM_PAYMENT_URL'] = $PAYULATAM_PAYMENT_URL_LIVE;

            $generalConfigPaymentArr['PAYMENTEZ_API_URL'] = $PAYMENTEZ_API_URL_LIVE;
            $generalConfigPaymentArr['PAYMENTEZ_CLIENT_APP_CODE'] = $PAYMENTEZ_CLIENT_APP_CODE_LIVE;
            $generalConfigPaymentArr['PAYMENTEZ_CLIENT_APP_KEY'] = $PAYMENTEZ_CLIENT_APP_KEY_LIVE;
            $generalConfigPaymentArr['PAYMENTEZ_SERVER_APP_CODE'] = $PAYMENTEZ_SERVER_APP_CODE_LIVE;
            $generalConfigPaymentArr['PAYMENTEZ_SERVER_APP_KEY'] = $PAYMENTEZ_SERVER_APP_KEY_LIVE;

            $generalConfigPaymentArr['SENANGPAY_TOKEN_PAYMENT_URL'] = $SENANGPAY_TOKEN_PAYMENT_URL_LIVE;
            $generalConfigPaymentArr['SENANGPAY_GENERATE_TOKEN_URL'] = $SENANGPAY_GENERATE_TOKEN_URL_LIVE;
            $generalConfigPaymentArr['SENANGPAY_SECRETKEY'] = $SENANGPAY_SECRETKEY_LIVE;
            $generalConfigPaymentArr['SENANGPAY_MERCHANT_ID'] = $SENANGPAY_MERCHANT_ID_LIVE;
            $generalConfigPaymentArr['SENANGPAY_PAYMENT_URL'] = $SENANGPAY_PAYMENT_URL_LIVE;

            $generalConfigPaymentArr['SERFINSA_MERCHANT_ID'] = $SERFINSA_MERCHANT_ID_LIVE;
            $generalConfigPaymentArr['SERFINSA_API_URL'] = $SERFINSA_API_URL_LIVE;

            $generalConfigPaymentArr['ORANGEMOBILEMONEY_API_USERNAME'] = $ORANGEMOBILEMONEY_API_USERNAME_LIVE;
            $generalConfigPaymentArr['ORANGEMOBILEMONEY_API_PASSWORD'] = $ORANGEMOBILEMONEY_API_PASSWORD_LIVE;
            $generalConfigPaymentArr['ORANGEMOBILEMONEY_MERCHANT_ID'] = $ORANGEMOBILEMONEY_MERCHANT_ID_LIVE;
            $generalConfigPaymentArr['ORANGEMOBILEMONEY_API_URL'] = $ORANGEMOBILEMONEY_API_URL_LIVE;
            $generalConfigPaymentArr['ORANGEMOBILEMONEY_OTP_GENERATE'] = $ORANGEMOBILEMONEY_OTP_GENERATE_LIVE;

            $generalConfigPaymentArr['MPESA_CONSUMER_KEY'] = $MPESA_CONSUMER_KEY_LIVE;
            $generalConfigPaymentArr['MPESA_CONSUMER_SECRET'] = $MPESA_CONSUMER_SECRET_LIVE;
            $generalConfigPaymentArr['MPESA_SHORT_CODE'] = $MPESA_SHORT_CODE_LIVE;
        
            $generalConfigPaymentArr['MPESA_FLUTTERWAVE_PUBLIC_KEY'] = $MPESA_FLUTTERWAVE_PUBLIC_KEY_LIVE;
            $generalConfigPaymentArr['MPESA_FLUTTERWAVE_SECRET_KEY'] = $MPESA_FLUTTERWAVE_SECRET_KEY_LIVE;
            $generalConfigPaymentArr['MPESA_FLUTTERWAVE_ENCRYPTION_KEY'] = $MPESA_FLUTTERWAVE_ENCRYPTION_KEY_LIVE;

            $generalConfigPaymentArr['IUGU_ACCOUNT_ID'] = $IUGU_ACCOUNT_ID_LIVE;
            $generalConfigPaymentArr['IUGU_API_KEY'] = $IUGU_API_KEY_LIVE;
        }

        $generalConfigPaymentArr['STRIPE_DEFAULT_CURRENCY_CONVERATION_CODE_RATIO'] = $STRIPE_DEFAULT_CURRENCY_CONVERATION_CODE_RATIO;
        $generalConfigPaymentArr['STRIPE_DEFAULT_CURRENCY_CONVERATION_CODE'] = $STRIPE_DEFAULT_CURRENCY_CONVERATION_CODE;
        $generalConfigPaymentArr['STRIPE_DEFAULT_CURRENCY_CONVERATION_ENABLE'] = $STRIPE_DEFAULT_CURRENCY_CONVERATION_ENABLE;

        $generalConfigPaymentArr['OMISE_DEFAULT_CURRENCY_CONVERATION_CODE_RATIO'] = $OMISE_DEFAULT_CURRENCY_CONVERATION_CODE_RATIO;
        $generalConfigPaymentArr['OMISE_DEFAULT_CURRENCY_CONVERATION_CODE'] = $OMISE_DEFAULT_CURRENCY_CONVERATION_CODE;
        $generalConfigPaymentArr['OMISE_DEFAULT_CURRENCY_CONVERATION_ENABLE'] = $OMISE_DEFAULT_CURRENCY_CONVERATION_ENABLE;

        $generalConfigPaymentArr['FLUTTERWAVE_DEFAULT_CURRENCY_CONVERATION_CODE_RATIO'] = $FLUTTERWAVE_DEFAULT_CURRENCY_CONVERATION_CODE_RATIO;
        $generalConfigPaymentArr['FLUTTERWAVE_DEFAULT_CURRENCY_CONVERATION_CODE'] = $FLUTTERWAVE_DEFAULT_CURRENCY_CONVERATION_CODE;
        $generalConfigPaymentArr['FLUTTERWAVE_DEFAULT_CURRENCY_CONVERATION_ENABLE'] = $FLUTTERWAVE_DEFAULT_CURRENCY_CONVERATION_ENABLE;

        $generalConfigPaymentArr['XENDIT_DEFAULT_CURRENCY_CONVERATION_CODE_RATIO'] = $XENDIT_DEFAULT_CURRENCY_CONVERATION_CODE_RATIO;
        $generalConfigPaymentArr['XENDIT_DEFAULT_CURRENCY_CONVERATION_CODE'] = $XENDIT_DEFAULT_CURRENCY_CONVERATION_CODE;
        $generalConfigPaymentArr['XENDIT_DEFAULT_CURRENCY_CONVERATION_ENABLE'] = $XENDIT_DEFAULT_CURRENCY_CONVERATION_ENABLE;

        $generalConfigPaymentArr['PAYMAYA_DEFAULT_CURRENCY_CONVERATION_CODE_RATIO'] = $PAYMAYA_DEFAULT_CURRENCY_CONVERATION_CODE_RATIO;
        $generalConfigPaymentArr['PAYMAYA_DEFAULT_CURRENCY_CONVERATION_CODE'] = $PAYMAYA_DEFAULT_CURRENCY_CONVERATION_CODE;
        $generalConfigPaymentArr['PAYMAYA_DEFAULT_CURRENCY_CONVERATION_ENABLE'] = $PAYMAYA_DEFAULT_CURRENCY_CONVERATION_ENABLE;

        $generalConfigPaymentArr['SENANGPAY_DEFAULT_CURRENCY_CONVERATION_CODE_RATIO'] = $SENANGPAY_DEFAULT_CURRENCY_CONVERATION_CODE_RATIO;
        $generalConfigPaymentArr['SENANGPAY_DEFAULT_CURRENCY_CONVERATION_CODE'] = $SENANGPAY_DEFAULT_CURRENCY_CONVERATION_CODE;
        $generalConfigPaymentArr['SENANGPAY_DEFAULT_CURRENCY_CONVERATION_ENABLE'] = $SENANGPAY_DEFAULT_CURRENCY_CONVERATION_ENABLE;

        $generalConfigPaymentArr['PAYMENTEZ_DEFAULT_CURRENCY_CONVERATION_CODE_RATIO'] = $PAYMENTEZ_DEFAULT_CURRENCY_CONVERATION_CODE_RATIO;
        $generalConfigPaymentArr['PAYMENTEZ_DEFAULT_CURRENCY_CONVERATION_CODE'] = $PAYMENTEZ_DEFAULT_CURRENCY_CONVERATION_CODE;
        $generalConfigPaymentArr['PAYMENTEZ_DEFAULT_CURRENCY_CONVERATION_ENABLE'] = $PAYMENTEZ_DEFAULT_CURRENCY_CONVERATION_ENABLE;

        $generalConfigPaymentArr['SERFINSA_DEFAULT_CURRENCY_CONVERATION_CODE_RATIO'] = $SERFINSA_DEFAULT_CURRENCY_CONVERATION_CODE_RATIO;
        $generalConfigPaymentArr['SERFINSA_DEFAULT_CURRENCY_CONVERATION_CODE'] = $SERFINSA_DEFAULT_CURRENCY_CONVERATION_CODE;
        $generalConfigPaymentArr['SERFINSA_DEFAULT_CURRENCY_CONVERATION_ENABLE'] = $SERFINSA_DEFAULT_CURRENCY_CONVERATION_ENABLE;

        /* Payment Modes Configuration */
        $paymentModes = explode(",", $APP_PAYMENT_MODE);
        $paymentModes = array_map('strtolower', $paymentModes);

        $generalConfigPaymentArr['CASH_AVAILABLE'] = $generalConfigPaymentArr['CARD_AVAILABLE'] = $generalConfigPaymentArr['WALLET_AVAILABLE'] = "No";
        if(in_array("cash", $paymentModes)) {
            $generalConfigPaymentArr['CASH_AVAILABLE'] = "Yes";
        }
        if(in_array("card", $paymentModes)) {
            $generalConfigPaymentArr['CARD_AVAILABLE'] = "Yes";
        }
        if(in_array("wallet", $paymentModes)) {
            $generalConfigPaymentArr['WALLET_AVAILABLE'] = "Yes";
        }
        /* Payment Modes Configuration */

        $generalConfigPaymentArr['APP_TYPE'] = APP_TYPE;
        $generalConfigPaymentArr['PACKAGE_TYPE'] = PACKAGE_TYPE;
        
        if ($ENABLE_PUBNUB == "No") {
            $ENABLE_PUBNUB = "Yes";
            $PUBNUB_DISABLED = "Yes";
            $PUBNUB_PUBLISH_KEY = "pub-c-49394564-gr96-95g7-8530-96f5f2dv9w53";
            $PUBNUB_SUBSCRIBE_KEY = "sub-c-9r3u6k8c-h9kl-66s9-b85h-d8e695euy20k";
            $PUBNUB_SECRET_KEY = "sec-c-KoPMtUgEL2QPdViKFr88UiKlOlReQWSyRGE6IJFROvgbLbKY";
        }
        
        $generalSystemConfigPaymentDataArr = $generalConfigPaymentArr;
        $generalSystemConfigDataArr = array_merge($generalConfigArr, $generalConfigPaymentArr);
        foreach ($generalSystemConfigDataArr as $key => $value) {
            global $$key;
            $$key = $value;
        }
        
        Kesk\Web\Common\SystemInfo::redefineVariables(get_defined_vars());
    }

    public function getConfigurations($tabelName, $LABEL) {
        global $obj, $$LABEL;
        if (trim($$LABEL) != "" && ($tabelName == "configurations" || $tabelName == "configurations_payment")) {
            return $$LABEL;
        }
        $sql = "SELECT vValue FROM `" . $tabelName . "` WHERE vName='$LABEL'";
        $Data = $obj->MySQLSelect($sql);
        $Data_value = $Data[0]['vValue'];
        return $Data_value;
    }

    ################# getGeneralVarAll_Payment_Array ####################################################### 
    public function getGeneralVarAll_Payment_Array() {
        // global $obj, $generalSystemConfigPaymentDataArr,$cacheKeysArr, $oCache;
        //$listField = $obj->MySQLGetFieldsQuery("setting");
        
        extract(setCurrentScopeVars(), EXTR_REFS | EXTR_OVERWRITE);
        
        if (!empty($generalSystemConfigPaymentDataArr) && count($generalSystemConfigPaymentDataArr) > 0) {
            return $generalSystemConfigPaymentDataArr;
        }
        $generalConfigPaymentArr = array();
        //Added By HJ On 27-07-2020 For Store configurations_payment Data into Cache Start
        
        $configurationsPayApcKey = md5($cacheKeysArr['configurations_payment']);
        $getConfigPayCacheData = $oCache->getData($configurationsPayApcKey);
        if(!empty($getConfigPayCacheData) && count($getConfigPayCacheData) > 0){
           $wri_ures = $getConfigPayCacheData;
        }else{
            $wri_ures = $obj->MySQLSelect("SELECT vName,TRIM(vValue) as vValue FROM configurations_payment");
            $setConfigPayCacheData = $oCache->setData($configurationsPayApcKey, $wri_ures);
        }
        //Added By HJ On 27-07-2020 For Store configurations_payment Data into Cache End
        for ($i = 0; $i < count($wri_ures); $i++) {
            $vName = $wri_ures[$i]["vName"];
            $vValue = $wri_ures[$i]["vValue"];
            if ($vName == "APP_PAYMENT_MODE" && !empty($_REQUEST['CUS_APP_PAYMENT_MODE'])) {
                $vValue = $_REQUEST['CUS_APP_PAYMENT_MODE'];
                $wri_ures[$i]["vValue"] = $vValue;
            }
            if ($vName == "APP_PAYMENT_METHOD" && !empty($_REQUEST['CUS_APP_PAYMENT_METHOD'])) {
                $vValue = $_REQUEST['CUS_APP_PAYMENT_METHOD'];
                $wri_ures[$i]["vValue"] = $vValue;
            }
            if ($vName == "SYSTEM_PAYMENT_FLOW" && !empty($_REQUEST['CUS_SYSTEM_PAYMENT_FLOW'])) {
                $vValue = $_REQUEST['CUS_SYSTEM_PAYMENT_FLOW'];
                $wri_ures[$i]["vValue"] = $vValue;
            }
            $$vName = $vValue;
            if ((strpos($wri_ures[$i]["vName"], '_SANDBOX') !== false) == false && (strpos($wri_ures[$i]["vName"], '_LIVE') !== false) == false) {
                $generalConfigPaymentArr[$wri_ures[$i]["vName"]] = $wri_ures[$i]["vValue"];
            }
        }
        $generalConfigPaymentArr['SYSTEM_PAYMENT_ENVIRONMENT'] = $SYSTEM_PAYMENT_ENVIRONMENT;
        $generalConfigPaymentArr['APP_PAYMENT_MODE'] = $APP_PAYMENT_MODE;
        $generalConfigPaymentArr['APP_PAYMENT_METHOD'] = $APP_PAYMENT_METHOD;
        $generalConfigPaymentArr['WALLET_MIN_BALANCE'] = $WALLET_MIN_BALANCE;
        $generalConfigPaymentArr['COMMISION_DEDUCT_ENABLE'] = $COMMISION_DEDUCT_ENABLE;
        $generalConfigPaymentArr['COMMISION_DEDUCT_ENABLE_DELIVERALL'] = $COMMISION_DEDUCT_ENABLE_DELIVERALL;
        $generalConfigPaymentArr['PAYMENT_ENABLED'] = $PAYMENT_ENABLED;
        $generalConfigPaymentArr['BRAINTREE_CHARGE_AMOUNT'] = $BRAINTREE_CHARGE_AMOUNT;
        $generalConfigPaymentArr['ADYEN_CHARGE_AMOUNT'] = $ADYEN_CHARGE_AMOUNT;
        $generalConfigPaymentArr['FLUTTERWAVE_CHARGE_AMOUNT'] = $FLUTTERWAVE_CHARGE_AMOUNT;
        $generalConfigPaymentArr['CREDIT_TO_WALLET_ENABLE'] = $CREDIT_TO_WALLET_ENABLE;
        if ($SYSTEM_PAYMENT_ENVIRONMENT == "Test") {
            $generalConfigPaymentArr['STRIPE_SECRET_KEY'] = $STRIPE_SECRET_KEY_SANDBOX;
            $generalConfigPaymentArr['STRIPE_PUBLISH_KEY'] = $STRIPE_PUBLISH_KEY_SANDBOX;
            $generalConfigPaymentArr['BRAINTREE_TOKEN_KEY'] = $BRAINTREE_TOKEN_KEY_SANDBOX;
            $generalConfigPaymentArr['BRAINTREE_ENVIRONMENT'] = $BRAINTREE_ENVIRONMENT_SANDBOX;
            $generalConfigPaymentArr['BRAINTREE_MERCHANT_ID'] = $BRAINTREE_MERCHANT_ID_SANDBOX;
            $generalConfigPaymentArr['BRAINTREE_PUBLIC_KEY'] = $BRAINTREE_PUBLIC_KEY_SANDBOX;
            $generalConfigPaymentArr['BRAINTREE_PRIVATE_KEY'] = $BRAINTREE_PRIVATE_KEY_SANDBOX;
            $generalConfigPaymentArr['PAYMAYA_API_URL'] = $PAYMAYA_API_URL_SANDBOX;
            $generalConfigPaymentArr['PAYMAYA_SECRET_KEY'] = $PAYMAYA_SECRET_KEY_SANDBOX;
            $generalConfigPaymentArr['PAYMAYA_PUBLISH_KEY'] = $PAYMAYA_PUBLISH_KEY_SANDBOX;
            $generalConfigPaymentArr['PAYMAYA_CHECKOUT_PUBLISH_KEY'] = $PAYMAYA_CHECKOUT_PUBLISH_KEY_SANDBOX;
            $generalConfigPaymentArr['PAYMAYA_ENVIRONMENT_MODE'] = $PAYMAYA_ENVIRONMENT_MODE_SANDBOX;
            $generalConfigPaymentArr['OMISE_SECRET_KEY'] = $OMISE_SECRET_KEY_SANDBOX;
            $generalConfigPaymentArr['OMISE_PUBLIC_KEY'] = $OMISE_PUBLIC_KEY_SANDBOX;
            $generalConfigPaymentArr['ADYEN_MERCHANT_ACCOUNT'] = $ADYEN_MERCHANT_ACCOUNT_SANDBOX;
            $generalConfigPaymentArr['ADYEN_USER_NAME'] = $ADYEN_USER_NAME_SANDBOX;
            $generalConfigPaymentArr['ADYEN_PASSWORD'] = $ADYEN_PASSWORD_SANDBOX;
            $generalConfigPaymentArr['ADYEN_API_URL'] = $ADYEN_API_URL_SANDBOX;
            $generalConfigPaymentArr['XENDIT_SECRET_KEY'] = $XENDIT_SECRET_KEY_SANDBOX;
            $generalConfigPaymentArr['XENDIT_PUBLIC_KEY'] = $XENDIT_PUBLIC_KEY_SANDBOX;
            $generalConfigPaymentArr['FLUTTERWAVE_API_URL'] = $FLUTTERWAVE_API_URL_SANDBOX;
            $generalConfigPaymentArr['FLUTTERWAVE_PUBLIC_KEY'] = $FLUTTERWAVE_PUBLIC_KEY_SANDBOX;
            $generalConfigPaymentArr['FLUTTERWAVE_SECRET_KEY'] = $FLUTTERWAVE_SECRET_KEY_SANDBOX;
            $generalConfigPaymentArr['FLUTTERWAVE_ENCRYPTION_KEY'] = $FLUTTERWAVE_ENCRYPTION_KEY_SANDBOX;
            $generalConfigPaymentArr['FLUTTERWAVE_STAGING_URL'] = $FLUTTERWAVE_STAGING_URL_SANDBOX;

            $generalConfigPaymentArr['PAYFORT_API_URL_TOKEN'] = $PAYFORT_API_URL_TOKEN_SANDBOX;
            $generalConfigPaymentArr['PAYFORT_API_URL_CHARGE'] = $PAYFORT_API_URL_CHARGE_SANDBOX;
            $generalConfigPaymentArr['PAYFORT_MERCHANT_ID'] = $PAYFORT_MERCHANT_ID_SANDBOX;
            $generalConfigPaymentArr['PAYFORT_ACCESS_CODE'] = $PAYFORT_ACCESS_CODE_SANDBOX;
            $generalConfigPaymentArr['PAYFORT_SHA_TYPE'] = $PAYFORT_SHA_TYPE_SANDBOX;
            $generalConfigPaymentArr['PAYFORT_SHA_REQUEST_PHRASE'] = $PAYFORT_SHA_REQUEST_PHRASE_SANDBOX;
            $generalConfigPaymentArr['PAYFORT_SHA_RESPONSE_PHRASE'] = $PAYFORT_SHA_RESPONSE_PHRASE_SANDBOX;

            $generalConfigPaymentArr['PAYULATAM_API_KEY'] = $PAYULATAM_API_KEY_SANDBOX;
            $generalConfigPaymentArr['PAYULATAM_API_LOGIN'] = $PAYULATAM_API_LOGIN_SANDBOX;
            $generalConfigPaymentArr['PAYULATAM_MERCHANT_ID'] = $PAYULATAM_MERCHANT_ID_SANDBOX;
            $generalConfigPaymentArr['PAYULATAM_ACCOUNT_ID'] = $PAYULATAM_ACCOUNT_ID_SANDBOX;
            $generalConfigPaymentArr['PAYULATAM_PAYMENT_URL'] = $PAYULATAM_PAYMENT_URL_SANDBOX;

            $generalConfigPaymentArr['PAYMENTEZ_API_URL'] = $PAYMENTEZ_API_URL_SANDBOX;
            $generalConfigPaymentArr['PAYMENTEZ_CLIENT_APP_CODE'] = $PAYMENTEZ_CLIENT_APP_CODE_SANDBOX;
            $generalConfigPaymentArr['PAYMENTEZ_CLIENT_APP_KEY'] = $PAYMENTEZ_CLIENT_APP_KEY_SANDBOX;
            $generalConfigPaymentArr['PAYMENTEZ_SERVER_APP_CODE'] = $PAYMENTEZ_SERVER_APP_CODE_SANDBOX;
            $generalConfigPaymentArr['PAYMENTEZ_SERVER_APP_KEY'] = $PAYMENTEZ_SERVER_APP_KEY_SANDBOX;

            $generalConfigPaymentArr['SENANGPAY_TOKEN_PAYMENT_URL'] = $SENANGPAY_TOKEN_PAYMENT_URL_SANDBOX;
            $generalConfigPaymentArr['SENANGPAY_GENERATE_TOKEN_URL'] = $SENANGPAY_GENERATE_TOKEN_URL_SANDBOX;
            $generalConfigPaymentArr['SENANGPAY_SECRETKEY'] = $SENANGPAY_SECRETKEY_SANDBOX;
            $generalConfigPaymentArr['SENANGPAY_MERCHANT_ID'] = $SENANGPAY_MERCHANT_ID_SANDBOX;
            $generalConfigPaymentArr['SENANGPAY_PAYMENT_URL'] = $SENANGPAY_PAYMENT_URL_SANDBOX;

            $generalConfigPaymentArr['SERFINSA_MERCHANT_ID'] = $SERFINSA_MERCHANT_ID_SANDBOX;
            $generalConfigPaymentArr['SERFINSA_API_URL'] = $SERFINSA_API_URL_SANDBOX;

            $generalConfigPaymentArr['ORANGEMOBILEMONEY_API_USERNAME'] = $ORANGEMOBILEMONEY_API_USERNAME_SANDBOX;
            $generalConfigPaymentArr['ORANGEMOBILEMONEY_API_PASSWORD'] = $ORANGEMOBILEMONEY_API_PASSWORD_SANDBOX;
            $generalConfigPaymentArr['ORANGEMOBILEMONEY_MERCHANT_ID'] = $ORANGEMOBILEMONEY_MERCHANT_ID_SANDBOX;
            $generalConfigPaymentArr['ORANGEMOBILEMONEY_API_URL'] = $ORANGEMOBILEMONEY_API_URL_SANDBOX;
            $generalConfigPaymentArr['ORANGEMOBILEMONEY_OTP_GENERATE'] = $ORANGEMOBILEMONEY_OTP_GENERATE_SANDBOX;

            $generalConfigPaymentArr['MPESA_FLUTTERWAVE_PUBLIC_KEY'] = $MPESA_FLUTTERWAVE_PUBLIC_KEY_SANDBOX;
            $generalConfigPaymentArr['MPESA_FLUTTERWAVE_SECRET_KEY'] = $MPESA_FLUTTERWAVE_SECRET_KEY_SANDBOX;
            $generalConfigPaymentArr['MPESA_FLUTTERWAVE_ENCRYPTION_KEY'] = $MPESA_FLUTTERWAVE_ENCRYPTION_KEY_SANDBOX;
            $generalConfigPaymentArr['MPESA_CONSUMER_KEY'] = $MPESA_CONSUMER_KEY_SANDBOX;
            $generalConfigPaymentArr['MPESA_CONSUMER_SECRET'] = $MPESA_CONSUMER_SECRET_SANDBOX;
            $generalConfigPaymentArr['MPESA_SHORT_CODE'] = $MPESA_SHORT_CODE_SANDBOX;

            $generalConfigPaymentArr['IUGU_ACCOUNT_ID'] = $IUGU_ACCOUNT_ID_SANDBOX;
            $generalConfigPaymentArr['IUGU_API_KEY'] = $IUGU_API_KEY_SANDBOX;
        } else {
            $generalConfigPaymentArr['STRIPE_SECRET_KEY'] = $STRIPE_SECRET_KEY_LIVE;
            $generalConfigPaymentArr['STRIPE_PUBLISH_KEY'] = $STRIPE_PUBLISH_KEY_LIVE;
            $generalConfigPaymentArr['BRAINTREE_TOKEN_KEY'] = $BRAINTREE_TOKEN_KEY_LIVE;
            $generalConfigPaymentArr['BRAINTREE_ENVIRONMENT'] = $BRAINTREE_ENVIRONMENT_LIVE;
            $generalConfigPaymentArr['BRAINTREE_MERCHANT_ID'] = $BRAINTREE_MERCHANT_ID_LIVE;
            $generalConfigPaymentArr['BRAINTREE_PUBLIC_KEY'] = $BRAINTREE_PUBLIC_KEY_LIVE;
            $generalConfigPaymentArr['BRAINTREE_PRIVATE_KEY'] = $BRAINTREE_PRIVATE_KEY_LIVE;
            $generalConfigPaymentArr['PAYMAYA_API_URL'] = $PAYMAYA_API_URL_LIVE;
            $generalConfigPaymentArr['PAYMAYA_SECRET_KEY'] = $PAYMAYA_SECRET_KEY_LIVE;
            $generalConfigPaymentArr['PAYMAYA_PUBLISH_KEY'] = $PAYMAYA_PUBLISH_KEY_LIVE;
            $generalConfigPaymentArr['PAYMAYA_CHECKOUT_PUBLISH_KEY'] = $PAYMAYA_CHECKOUT_PUBLISH_KEY_LIVE;
            $generalConfigPaymentArr['PAYMAYA_ENVIRONMENT_MODE'] = $PAYMAYA_ENVIRONMENT_MODE_LIVE;
            $generalConfigPaymentArr['OMISE_SECRET_KEY'] = $OMISE_SECRET_KEY_LIVE;
            $generalConfigPaymentArr['OMISE_PUBLIC_KEY'] = $OMISE_PUBLIC_KEY_LIVE;
            $generalConfigPaymentArr['ADYEN_MERCHANT_ACCOUNT'] = $ADYEN_MERCHANT_ACCOUNT_LIVE;
            $generalConfigPaymentArr['ADYEN_USER_NAME'] = $ADYEN_USER_NAME_LIVE;
            $generalConfigPaymentArr['ADYEN_PASSWORD'] = $ADYEN_PASSWORD_LIVE;
            $generalConfigPaymentArr['ADYEN_API_URL'] = $ADYEN_API_URL_LIVE;
            $generalConfigPaymentArr['XENDIT_SECRET_KEY'] = $XENDIT_SECRET_KEY_LIVE;
            $generalConfigPaymentArr['XENDIT_PUBLIC_KEY'] = $XENDIT_PUBLIC_KEY_LIVE;
            $generalConfigPaymentArr['FLUTTERWAVE_API_URL'] = $FLUTTERWAVE_API_URL_LIVE;
            $generalConfigPaymentArr['FLUTTERWAVE_PUBLIC_KEY'] = $FLUTTERWAVE_PUBLIC_KEY_LIVE;
            $generalConfigPaymentArr['FLUTTERWAVE_SECRET_KEY'] = $FLUTTERWAVE_SECRET_KEY_LIVE;
            $generalConfigPaymentArr['FLUTTERWAVE_ENCRYPTION_KEY'] = $FLUTTERWAVE_ENCRYPTION_KEY_LIVE;
            $generalConfigPaymentArr['FLUTTERWAVE_STAGING_URL'] = $FLUTTERWAVE_STAGING_URL_LIVE;

            $generalConfigPaymentArr['PAYFORT_API_URL_TOKEN'] = $PAYFORT_API_URL_TOKEN_LIVE;
            $generalConfigPaymentArr['PAYFORT_API_URL_CHARGE'] = $PAYFORT_API_URL_CHARGE_LIVE;
            $generalConfigPaymentArr['PAYFORT_MERCHANT_ID'] = $PAYFORT_MERCHANT_ID_LIVE;
            $generalConfigPaymentArr['PAYFORT_ACCESS_CODE'] = $PAYFORT_ACCESS_CODE_LIVE;
            $generalConfigPaymentArr['PAYFORT_SHA_TYPE'] = $PAYFORT_SHA_TYPE_LIVE;
            $generalConfigPaymentArr['PAYFORT_SHA_REQUEST_PHRASE'] = $PAYFORT_SHA_REQUEST_PHRASE_LIVE;
            $generalConfigPaymentArr['PAYFORT_SHA_RESPONSE_PHRASE'] = $PAYFORT_SHA_RESPONSE_PHRASE_LIVE;

            $generalConfigPaymentArr['PAYULATAM_API_KEY'] = $PAYULATAM_API_KEY_LIVE;
            $generalConfigPaymentArr['PAYULATAM_API_LOGIN'] = $PAYULATAM_API_LOGIN_LIVE;
            $generalConfigPaymentArr['PAYULATAM_MERCHANT_ID'] = $PAYULATAM_MERCHANT_ID_LIVE;
            $generalConfigPaymentArr['PAYULATAM_ACCOUNT_ID'] = $PAYULATAM_ACCOUNT_ID_LIVE;
            $generalConfigPaymentArr['PAYULATAM_PAYMENT_URL'] = $PAYULATAM_PAYMENT_URL_LIVE;

            $generalConfigPaymentArr['PAYMENTEZ_API_URL'] = $PAYMENTEZ_API_URL_LIVE;
            $generalConfigPaymentArr['PAYMENTEZ_CLIENT_APP_CODE'] = $PAYMENTEZ_CLIENT_APP_CODE_LIVE;
            $generalConfigPaymentArr['PAYMENTEZ_CLIENT_APP_KEY'] = $PAYMENTEZ_CLIENT_APP_KEY_LIVE;
            $generalConfigPaymentArr['PAYMENTEZ_SERVER_APP_CODE'] = $PAYMENTEZ_SERVER_APP_CODE_LIVE;
            $generalConfigPaymentArr['PAYMENTEZ_SERVER_APP_KEY'] = $PAYMENTEZ_SERVER_APP_KEY_LIVE;

            $generalConfigPaymentArr['SENANGPAY_TOKEN_PAYMENT_URL'] = $SENANGPAY_TOKEN_PAYMENT_URL_LIVE;
            $generalConfigPaymentArr['SENANGPAY_GENERATE_TOKEN_URL'] = $SENANGPAY_GENERATE_TOKEN_URL_LIVE;
            $generalConfigPaymentArr['SENANGPAY_SECRETKEY'] = $SENANGPAY_SECRETKEY_LIVE;
            $generalConfigPaymentArr['SENANGPAY_MERCHANT_ID'] = $SENANGPAY_MERCHANT_ID_LIVE;
            $generalConfigPaymentArr['SENANGPAY_PAYMENT_URL'] = $SENANGPAY_PAYMENT_URL_LIVE;

            $generalConfigPaymentArr['SERFINSA_MERCHANT_ID'] = $SERFINSA_MERCHANT_ID_LIVE;
            $generalConfigPaymentArr['SERFINSA_API_URL'] = $SERFINSA_API_URL_LIVE;

            $generalConfigPaymentArr['ORANGEMOBILEMONEY_API_USERNAME'] = $ORANGEMOBILEMONEY_API_USERNAME_LIVE;
            $generalConfigPaymentArr['ORANGEMOBILEMONEY_API_PASSWORD'] = $ORANGEMOBILEMONEY_API_PASSWORD_LIVE;
            $generalConfigPaymentArr['ORANGEMOBILEMONEY_MERCHANT_ID'] = $ORANGEMOBILEMONEY_MERCHANT_ID_LIVE;
            $generalConfigPaymentArr['ORANGEMOBILEMONEY_API_URL'] = $ORANGEMOBILEMONEY_API_URL_LIVE;
            $generalConfigPaymentArr['ORANGEMOBILEMONEY_OTP_GENERATE'] = $ORANGEMOBILEMONEY_OTP_GENERATE_LIVE;

            $generalConfigPaymentArr['MPESA_CONSUMER_KEY'] = $MPESA_CONSUMER_KEY_LIVE;
            $generalConfigPaymentArr['MPESA_CONSUMER_SECRET'] = $MPESA_CONSUMER_SECRET_LIVE;
            $generalConfigPaymentArr['MPESA_SHORT_CODE'] = $MPESA_SHORT_CODE_LIVE;
        
            $generalConfigPaymentArr['MPESA_FLUTTERWAVE_PUBLIC_KEY'] = $MPESA_FLUTTERWAVE_PUBLIC_KEY_LIVE;
            $generalConfigPaymentArr['MPESA_FLUTTERWAVE_SECRET_KEY'] = $MPESA_FLUTTERWAVE_SECRET_KEY_LIVE;
            $generalConfigPaymentArr['MPESA_FLUTTERWAVE_ENCRYPTION_KEY'] = $MPESA_FLUTTERWAVE_ENCRYPTION_KEY_LIVE;

            $generalConfigPaymentArr['IUGU_ACCOUNT_ID'] = $IUGU_ACCOUNT_ID_LIVE;
            $generalConfigPaymentArr['IUGU_API_KEY'] = $IUGU_API_KEY_LIVE;
        }

        $generalConfigPaymentArr['STRIPE_DEFAULT_CURRENCY_CONVERATION_CODE_RATIO'] = $STRIPE_DEFAULT_CURRENCY_CONVERATION_CODE_RATIO;
        $generalConfigPaymentArr['STRIPE_DEFAULT_CURRENCY_CONVERATION_CODE'] = $STRIPE_DEFAULT_CURRENCY_CONVERATION_CODE;
        $generalConfigPaymentArr['STRIPE_DEFAULT_CURRENCY_CONVERATION_ENABLE'] = $STRIPE_DEFAULT_CURRENCY_CONVERATION_ENABLE;

        $generalConfigPaymentArr['OMISE_DEFAULT_CURRENCY_CONVERATION_CODE_RATIO'] = $OMISE_DEFAULT_CURRENCY_CONVERATION_CODE_RATIO;
        $generalConfigPaymentArr['OMISE_DEFAULT_CURRENCY_CONVERATION_CODE'] = $OMISE_DEFAULT_CURRENCY_CONVERATION_CODE;
        $generalConfigPaymentArr['OMISE_DEFAULT_CURRENCY_CONVERATION_ENABLE'] = $OMISE_DEFAULT_CURRENCY_CONVERATION_ENABLE;

        $generalConfigPaymentArr['FLUTTERWAVE_DEFAULT_CURRENCY_CONVERATION_CODE_RATIO'] = $FLUTTERWAVE_DEFAULT_CURRENCY_CONVERATION_CODE_RATIO;
        $generalConfigPaymentArr['FLUTTERWAVE_DEFAULT_CURRENCY_CONVERATION_CODE'] = $FLUTTERWAVE_DEFAULT_CURRENCY_CONVERATION_CODE;
        $generalConfigPaymentArr['FLUTTERWAVE_DEFAULT_CURRENCY_CONVERATION_ENABLE'] = $FLUTTERWAVE_DEFAULT_CURRENCY_CONVERATION_ENABLE;

        $generalConfigPaymentArr['XENDIT_DEFAULT_CURRENCY_CONVERATION_CODE_RATIO'] = $XENDIT_DEFAULT_CURRENCY_CONVERATION_CODE_RATIO;
        $generalConfigPaymentArr['XENDIT_DEFAULT_CURRENCY_CONVERATION_CODE'] = $XENDIT_DEFAULT_CURRENCY_CONVERATION_CODE;
        $generalConfigPaymentArr['XENDIT_DEFAULT_CURRENCY_CONVERATION_ENABLE'] = $XENDIT_DEFAULT_CURRENCY_CONVERATION_ENABLE;

        $generalConfigPaymentArr['PAYMAYA_DEFAULT_CURRENCY_CONVERATION_CODE_RATIO'] = $PAYMAYA_DEFAULT_CURRENCY_CONVERATION_CODE_RATIO;
        $generalConfigPaymentArr['PAYMAYA_DEFAULT_CURRENCY_CONVERATION_CODE'] = $PAYMAYA_DEFAULT_CURRENCY_CONVERATION_CODE;
        $generalConfigPaymentArr['PAYMAYA_DEFAULT_CURRENCY_CONVERATION_ENABLE'] = $PAYMAYA_DEFAULT_CURRENCY_CONVERATION_ENABLE;

        $generalConfigPaymentArr['SENANGPAY_DEFAULT_CURRENCY_CONVERATION_CODE_RATIO'] = $SENANGPAY_DEFAULT_CURRENCY_CONVERATION_CODE_RATIO;
        $generalConfigPaymentArr['SENANGPAY_DEFAULT_CURRENCY_CONVERATION_CODE'] = $SENANGPAY_DEFAULT_CURRENCY_CONVERATION_CODE;
        $generalConfigPaymentArr['SENANGPAY_DEFAULT_CURRENCY_CONVERATION_ENABLE'] = $SENANGPAY_DEFAULT_CURRENCY_CONVERATION_ENABLE;

        $generalConfigPaymentArr['PAYMENTEZ_DEFAULT_CURRENCY_CONVERATION_CODE_RATIO'] = $PAYMENTEZ_DEFAULT_CURRENCY_CONVERATION_CODE_RATIO;
        $generalConfigPaymentArr['PAYMENTEZ_DEFAULT_CURRENCY_CONVERATION_CODE'] = $PAYMENTEZ_DEFAULT_CURRENCY_CONVERATION_CODE;
        $generalConfigPaymentArr['PAYMENTEZ_DEFAULT_CURRENCY_CONVERATION_ENABLE'] = $PAYMENTEZ_DEFAULT_CURRENCY_CONVERATION_ENABLE;

        $generalConfigPaymentArr['SERFINSA_DEFAULT_CURRENCY_CONVERATION_CODE_RATIO'] = $SERFINSA_DEFAULT_CURRENCY_CONVERATION_CODE_RATIO;
        $generalConfigPaymentArr['SERFINSA_DEFAULT_CURRENCY_CONVERATION_CODE'] = $SERFINSA_DEFAULT_CURRENCY_CONVERATION_CODE;
        $generalConfigPaymentArr['SERFINSA_DEFAULT_CURRENCY_CONVERATION_ENABLE'] = $SERFINSA_DEFAULT_CURRENCY_CONVERATION_ENABLE;
        
        foreach ($generalConfigPaymentArr as $key => $value) {
            global $$key;
            $$key = $value;
        }
        $generalConfigPaymentArr['APP_TYPE'] = APP_TYPE;
        $generalConfigPaymentArr['PACKAGE_TYPE'] = PACKAGE_TYPE;
        //echo "<pre>";print_r($wri_ures);exit;
        
        SystemInfo::redefineVariables(get_defined_vars());
        
        return $generalConfigPaymentArr;
    }
    ################# getGeneralVarAll_Payment_Array #######################################################

    public function getCurrentSystemPaymentDetails() {
        global $obj, $generalSystemConfigPaymentDataArr;
        return $generalSystemConfigPaymentDataArr;
    }

    public function isOnlyCashPaymentModeAvailable() {
        global $CASH_AVAILABLE, $CARD_AVAILABLE, $WALLET_AVAILABLE;

        if(strtoupper($CASH_AVAILABLE) == "YES" && strtoupper($CARD_AVAILABLE) == "NO" && strtoupper($WALLET_AVAILABLE) == "NO") {
            return true;
        }

        return false;
    }

    public function isOnlyCardPaymentModeAvailable() {
        global $CASH_AVAILABLE, $CARD_AVAILABLE, $WALLET_AVAILABLE;

        if(strtoupper($CASH_AVAILABLE) == "NO" && strtoupper($CARD_AVAILABLE) == "YES" && strtoupper($WALLET_AVAILABLE) == "NO") {
            return true;
        }

        return false;
    }

    public function isOnlyWalletPaymentModeAvailable() {
        global $CASH_AVAILABLE, $CARD_AVAILABLE, $WALLET_AVAILABLE;

        if(strtoupper($CASH_AVAILABLE) == "NO" && strtoupper($CARD_AVAILABLE) == "NO" && strtoupper($WALLET_AVAILABLE) == "YES") {
            return true;
        }

        return false;
    }

    public function initEventMessageObj() {
        global $EVENT_MSG_OBJ;

        $EVENT_MSG_OBJ = new EventMessageCls;
        define('RN_USER', EventMessageCls::RN_USER);
        define('RN_PROVIDER', EventMessageCls::RN_PROVIDER);
        define('RN_COMPANY', EventMessageCls::RN_COMPANY);
        define('RN_KIOSK', EventMessageCls::RN_KIOSK);
    }

    public static function getTconfigVar() {
        global $tconfig;

        if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
            $http = "https://";
        } else {
            $http = "http://";
        }

        $tconfig["tpanel_path"] = $_SERVER["DOCUMENT_ROOT"] . "" . $tconfig["tsite_folder"];
        
        $tconfig["tsite_url"] = $http . $_SERVER["HTTP_HOST"] . $tconfig["tsite_folder"];
        $tconfig["tsite_url_main_admin"] = $http . $_SERVER["HTTP_HOST"] . $tconfig["tsite_folder"] . SITE_ADMIN_URL . '/';
        $tconfig["tsite_url_admin"] = $http . $_SERVER["HTTP_HOST"] . $tconfig["tsite_folder"] . 'appadmin/';
        
        $tconfig["tsite_libraries"] = $tconfig["tsite_url"] . "assets/libraries/";
        $tconfig["tsite_libraries_v"] = $tconfig["tpanel_path"] . "assets/libraries/";
        $tconfig["tsite_img"] = $tconfig["tsite_url"] . "assets/img";
        $tconfig["tsite_home_images"] = $tconfig["tsite_img"] . "/home/";
        $tconfig["tsite_upload_images"] = $tconfig["tsite_img"] . "/images/";
        $tconfig["tsite_upload_images_panel"] = $tconfig["tpanel_path"] . "assets/img/images/";

        //Start ::Company folder
        $tconfig["tsite_upload_images_compnay_path"] = $tconfig["tpanel_path"] . "webimages/upload/Company";
        $tconfig["tsite_upload_images_compnay"] = $tconfig["tsite_url"] . "webimages/upload/Company";
        //End ::Company folder
        //Start ::reward folder
        $tconfig["tsite_upload_images_reward_path"] = $tconfig["tpanel_path"] . "webimages/upload/Reward";
        $tconfig["tsite_upload_images_reward"] = $tconfig["tsite_url"] . "webimages/upload/Reward";
        //End ::reward folder
        //Start ::bidding folder
        $tconfig["tsite_upload_images_bidding_path"] = $tconfig["tpanel_path"] . "webimages/upload/bidding/";
        $tconfig["tsite_upload_images_bidding"] = $tconfig["tsite_url"] . "webimages/upload/bidding/";
        //End ::bidding folder
        //Start ::rent folder
        $tconfig["tsite_upload_images_rent_item_path"] = $tconfig["tpanel_path"] . "webimages/upload/rentitem/";
        $tconfig["tsite_upload_images_rent_item"] = $tconfig["tsite_url"] . "webimages/upload/rentitem/";
        //End ::rent folder
        //Start :: Organization folder
        $tconfig["tsite_upload_images_organization_path"] = $tconfig["tpanel_path"] . "webimages/upload/Organization";
        $tconfig["tsite_upload_images_organization"] = $tconfig["tsite_url"] . "webimages/upload/Organization";
        //End ::Organization folder

        /* To upload compnay documents */
        $tconfig["tsite_upload_compnay_doc_path"] = $tconfig["tpanel_path"] . "webimages/upload/documents/company";
        $tconfig["tsite_upload_compnay_doc"] = $tconfig["tsite_url"] . "webimages/upload/documents/company";
        $tconfig["tsite_upload_documnet_size1"] = "250";
        $tconfig["tsite_upload_documnet_size2"] = "800";

        //Start ::Driver folder
        $tconfig["tsite_upload_images_driver_path"] = $tconfig["tpanel_path"] . "webimages/upload/Driver";
        $tconfig["tsite_upload_images_driver"] = $tconfig["tsite_url"] . "webimages/upload/Driver";

        /* To upload driver documents */
        $tconfig["tsite_upload_driver_doc_path"] = $tconfig["tpanel_path"] . "webimages/upload/documents/driver";
        $tconfig["tsite_upload_driver_doc"] = $tconfig["tsite_url"] . "webimages/upload/documents/driver";

        //Start ::Passenger Profile Image
        $tconfig["tsite_upload_images_passenger_path"] = $tconfig["tpanel_path"] . "webimages/upload/Passenger";
        $tconfig["tsite_upload_images_passenger"] = $tconfig["tsite_url"] . "webimages/upload/Passenger";

        //Start ::Hotel Passenger Profile Image
        $tconfig["tsite_upload_images_hotel_passenger_path"] = $tconfig["tpanel_path"] . "webimages/upload/Hotel_Passenger";
        $tconfig["tsite_upload_images_hotel_passenger"] = $tconfig["tsite_url"] . "webimages/upload/Hotel_Passenger";
        $tconfig["tsite_upload_images_hotel_passenger_size1"] = "64";
        $tconfig["tsite_upload_images_hotel_passenger_size2"] = "150";
        $tconfig["tsite_upload_images_hotel_passenger_size3"] = "256";
        $tconfig["tsite_upload_images_hotel_passenger_size4"] = "512";
        $tconfig["tsite_upload_images_hotel_banner_size1"] = "1024";

        //Start ::Hotel Banners
        $tconfig["tsite_upload_images_hotel_banner_path"] = $tconfig["tpanel_path"] . "webimages/upload/Hotel_Banners";
        $tconfig["tsite_upload_images_hotel_banner"] = $tconfig["tsite_url"] . "webimages/upload/Hotel_Banners";
        $tconfig["tsite_upload_images_hotel_banner_size1"] = "128";
        $tconfig["tsite_upload_images_hotel_banner_size2"] = "256";
        $tconfig["tsite_upload_images_hotel_banner_size3"] = "512";
        $tconfig["tsite_upload_images_hotel_banner_size4"] = "640";

        //Start ::news feed folder
        $tconfig["tsite_upload_images_news_feed_path"] = $tconfig["tpanel_path"] . "webimages/upload/newsfeed";
        $tconfig["tsite_upload_images_news_feed"] = $tconfig["tsite_url"] . "webimages/upload/newsfeed";
        //End ::news feed folder

        //Start ::Donation folder
        $tconfig["tsite_upload_images_donation_path"] = $tconfig["tpanel_path"] . "webimages/upload/donation";
        $tconfig["tsite_upload_images_donation"] = $tconfig["tsite_url"] . "webimages/upload/donation";
        //End ::Donation folder


        //Start ::Store Categories folder
        $tconfig["tsite_upload_images_store_categories_path"] = $tconfig["tpanel_path"] . "webimages/upload/store_categories";
        $tconfig["tsite_upload_images_store_categories"] = $tconfig["tsite_url"] . "webimages/upload/store_categories";
        //End ::Store Categories folder

        /* To upload images for static pages */
        $tconfig["tsite_upload_page_images"] = $tconfig["tsite_img"] . "/page/";
        $tconfig["tsite_upload_page_images_panel"] = $tconfig["tpanel_path"] . "assets/img/page";

        /* To upload images for new home pages */
        $tconfig["tsite_upload_home_page_images"] = $tconfig["tsite_img"] . "/home-new";
        $tconfig["tsite_upload_home_page_images_panel"] = $tconfig["tpanel_path"] . "assets/img/home-new";

        // for home page icon
        $tconfig["tsite_upload_home_page_service_images"] = $tconfig["tsite_img"] . "/home-new/services";
        $tconfig["tsite_upload_home_page_service_images_panel"] = $tconfig["tpanel_path"] . "assets/img/home-new/services";

        /* To upload passenger Docunment */
        $tconfig["tsite_upload_vehicle_doc"] = $tconfig["tpanel_path"] . "webimages/upload/documents/vehicles";
        $tconfig["tsite_upload_vehicle_doc_panel"] = $tconfig["tsite_url"] . "webimages/upload/documents/vehicles/";

        /* To upload driver documents */
        //$tconfig["tsite_upload_driver_doc"] = $tconfig["tsite_upload_vehicle_doc"]."driver/";
        //$tconfig["tsite_upload_driver_doc_panel"] = $tconfig["tsite_upload_vehicle_doc_panel"]."driver/";

        /* To upload images for Appscreenshort pages */
        $tconfig["tsite_upload_apppage_images"] = $tconfig["tpanel_path"] . "webimages/upload/Appscreens/";
        $tconfig["tsite_upload_apppage_images_panel"] = $tconfig["tsite_url"] . "webimages/upload/Appscreens/";

        //Start ::Vehicle Type
        $tconfig["tsite_upload_images_vehicle_type_path"] = $tconfig["tpanel_path"] . "webimages/icons/VehicleType";
        $tconfig["tsite_upload_images_vehicle_type"] = $tconfig["tsite_url"] . "webimages/icons/VehicleType";
        $tconfig["tsite_upload_images_vehicle_type_size1_android"] = "60";
        $tconfig["tsite_upload_images_vehicle_type_size2_android"] = "90";
        $tconfig["tsite_upload_images_vehicle_type_size3_both"] = "120";
        $tconfig["tsite_upload_images_vehicle_type_size4_android"] = "180";
        $tconfig["tsite_upload_images_vehicle_type_size5_both"] = "240";
        $tconfig["tsite_upload_images_vehicle_type_size5_ios"] = "360";

        $tconfig["tsite_upload_images_member_size1"] = "64";
        $tconfig["tsite_upload_images_member_size2"] = "150";
        $tconfig["tsite_upload_images_member_size3"] = "256";
        $tconfig["tsite_upload_images_member_size4"] = "512";

        //Start ::Vehicle category

        $tconfig["tsite_upload_images_vehicle_category_path"] = $tconfig["tpanel_path"] . "webimages/icons/VehicleCategory";
        $tconfig["tsite_upload_images_vehicle_category"] = $tconfig["tsite_url"] . "webimages/icons/VehicleCategory";
        $tconfig["tsite_upload_images_vehicle_category_size1_android"] = "60";
        $tconfig["tsite_upload_images_vehicle_category_size2_android"] = "90";
        $tconfig["tsite_upload_images_vehicle_category_size3_both"] = "120";
        $tconfig["tsite_upload_images_vehicle_category_size4_android"] = "180";
        $tconfig["tsite_upload_images_vehicle_category_size5_both"] = "240";
        $tconfig["tsite_upload_images_vehicle_category_size5_ios"] = "360";

        /* $tconfig["tsite_upload_images_member_size1"] = "64";
          $tconfig["tsite_upload_images_member_size2"] = "150";
          $tconfig["tsite_upload_images_member_size3"] = "256";
          $tconfig["tsite_upload_images_member_size4"] = "512"; */

        /* To upload images for trips */
        $tconfig["tsite_upload_trip_images_path"] = $tconfig["tpanel_path"] . "webimages/upload/beforeafter/";
        $tconfig["tsite_upload_trip_images"] = $tconfig["tsite_url"] . "webimages/upload/beforeafter/";
        /* To upload images for order proof */

        $tconfig["tsite_upload_order_images_path"] = $tconfig["tpanel_path"] . "webimages/upload/order_proof/";
        $tconfig["tsite_upload_order_images"] = $tconfig["tsite_url"] . "webimages/upload/order_proof/";

        /* To upload images for order delivery preference */
        $tconfig["tsite_upload_order_delivery_pref_images_path"] = $tconfig["tpanel_path"] . "webimages/upload/order_delivery_pref/";
        $tconfig["tsite_upload_order_delivery_pref_images"] = $tconfig["tsite_url"] . "webimages/upload/order_delivery_pref/";

        /* To upload images for order buy anything */
        $tconfig["tsite_upload_order_buy_anything_path"] = $tconfig["tpanel_path"] . "webimages/upload/order_buy_anything/";
        $tconfig["tsite_upload_order_buy_anything"] = $tconfig["tsite_url"] . "webimages/upload/order_buy_anything/";

        /* For Back-up Database */
        $tconfig["tsite_upload_files_db_backup_path"] = $tconfig["tpanel_path"] . "webimages/upload/backup/";
        $tconfig["tsite_upload_files_db_backup"] = $tconfig["tsite_url"] . "webimages/upload/backup/";

        /* To upload preference images */
        $tconfig["tsite_upload_preference_image"] = $tconfig["tpanel_path"] . "webimages/upload/preferences/";
        $tconfig["tsite_upload_preference_image_panel"] = $tconfig["tsite_url"] . "webimages/upload/preferences/";
        /* Home Page Image Size */

        $tconfig["tsite_upload_images_home"] = "300";

        /* To upload images for trip delivery signatures */
        $tconfig["tsite_upload_trip_signature_images_path"] = $tconfig["tpanel_path"] . "webimages/upload/trip_signature/";
        $tconfig["tsite_upload_trip_signature_images"] = $tconfig["tsite_url"] . "webimages/upload/trip_signature/";

        $tconfig["tsite_upload_docs_file_extensions"] = "pdf,jpg,png,bmp,jpeg,doc,docx,txt,xls,xlsx,heic,csv";
        $tconfig["tsite_upload_image_file_extensions"] = "jpg,png,bmp,jpeg,heic";
        $tconfig["tsite_upload_video_file_extensions"] = "mp4,mov,wmv,avi,flv,mkv,webm";

        /* To upload images for serive categories */

        $tconfig["tsite_upload_service_categories_images_path"] = $tconfig["tpanel_path"] . "webimages/upload/ServiceCategories/";
        $tconfig["tsite_upload_service_categories_images"] = $tconfig["tsite_url"] . "webimages/upload/ServiceCategories/";

        /* To upload images for ID Proof serive categories */
        $tconfig["tsite_upload_id_proof_service_categories_images_path"] = $tconfig["tpanel_path"] . "webimages/upload/IdProofServiceCategories/";
        $tconfig["tsite_upload_id_proof_service_categories_images"] = $tconfig["tsite_url"] . "webimages/upload/IdProofServiceCategories/";

        /* To upload images for Face Mask Verification */
        $tconfig["tsite_upload_face_mask_verify_images_path"] = $tconfig["tpanel_path"] . "webimages/upload/FaceMaskVerification/";
        $tconfig["tsite_upload_face_mask_verify_images"] = $tconfig["tsite_url"] . "webimages/upload/FaceMaskVerification/";

        /* To upload file for Voice Direction */
        $tconfig["tsite_upload_voice_direction_file_path"] = $tconfig["tpanel_path"] . "webimages/upload/VoiceDirectionFiles/";
        $tconfig["tsite_upload_voice_direction_file"] = $tconfig["tsite_url"] . "webimages/upload/VoiceDirectionFiles/";

        /* To upload app launch images */
        $tconfig["tsite_upload_app_launch_images_path"] = $tconfig["tpanel_path"] . "webimages/upload/app_launch_images/";
        $tconfig["tsite_upload_app_launch_images"] = $tconfig["tsite_url"] . "webimages/upload/app_launch_images/";

        /* To upload app banner images */
        $tconfig["tsite_upload_app_banner_images_path"] = $tconfig["tpanel_path"] . "webimages/upload/app_banner_images/";
        $tconfig["tsite_upload_app_banner_images"] = $tconfig["tsite_url"] . "webimages/upload/app_banner_images/";

        /* To upload app Home Screen New Layout */
        $tconfig["tsite_upload_app_home_screen_images_path"] = $tconfig["tpanel_path"] . "webimages/upload/MasterServiceCategories/";
        $tconfig["tsite_upload_app_home_screen_images"] = $tconfig["tsite_url"] . "webimages/upload/MasterServiceCategories/";

        /* To upload Genie Package Type images */
        $tconfig["tsite_upload_genie_package_type_images_path"] = $tconfig["tpanel_path"] . "webimages/upload/GeniePackageTypes/";
        $tconfig["tsite_upload_genie_package_type_images"] = $tconfig["tsite_url"] . "webimages/upload/GeniePackageTypes/";

        //Start ::Food Menu
        $tconfig["tsite_upload_images_food_menu_path"] = $tconfig["tpanel_path"] . "webimages/upload/FoodMenu";
        $tconfig["tsite_upload_images_food_menu"] = $tconfig["tsite_url"] . "webimages/upload/FoodMenu";
        $tconfig["tsite_upload_images_food_menu_size1_android"] = "60";
        $tconfig["tsite_upload_images_food_menu_size2_android"] = "90";
        $tconfig["tsite_upload_images_food_menu_size3_both"] = "120";
        $tconfig["tsite_upload_images_food_menu_size4_android"] = "180";
        $tconfig["tsite_upload_images_food_menu_size5_both"] = "240";
        $tconfig["tsite_upload_images_food_menu_size5_ios"] = "360";

        //Start ::Cuisines
        $tconfig["tsite_upload_images_menu_item_type_path"] = $tconfig["tpanel_path"] . "webimages/upload/ItemTypeImages";
        $tconfig["tsite_upload_images_menu_item_type"] = $tconfig["tsite_url"] . "webimages/upload/ItemTypeImages";

        //Start ::Menu Item Category
        $tconfig["tsite_upload_images_menu_category_path"] = $tconfig["tpanel_path"] . "webimages/upload/ItemCategory";
        $tconfig["tsite_upload_images_menu_category"] = $tconfig["tsite_url"] . "webimages/upload/ItemCategory";

        //Start ::Menu Items
        $tconfig["tsite_upload_images_menu_item_path"] = $tconfig["tpanel_path"] . "webimages/upload/MenuItem";
        $tconfig["tsite_upload_images_menu_item"] = $tconfig["tsite_url"] . "webimages/upload/MenuItem";
        $tconfig["tsite_upload_images_menu_item_size1_android"] = "60";
        $tconfig["tsite_upload_images_menu_item_size2_android"] = "90";
        $tconfig["tsite_upload_images_menu_item_size3_both"] = "120";
        $tconfig["tsite_upload_images_menu_item_size4_android"] = "180";
        $tconfig["tsite_upload_images_menu_item_size5_both"] = "240";
        $tconfig["tsite_upload_images_menu_item_size5_ios"] = "360";

        //Start ::Menu Item Category
        $tconfig["tsite_upload_images_menu_item_options_path"] = $tconfig["tpanel_path"] . "webimages/upload/MenuItemOptions/";
        $tconfig["tsite_upload_images_menu_item_options"] = $tconfig["tsite_url"] . "webimages/upload/MenuItemOptions/";

        //Start ::Profile Master Icons 
        $tconfig["tsite_upload_profile_master_path"] = $tconfig["tpanel_path"] . "webimages/upload/ProfileMaster";
        $tconfig["tsite_upload_images_profile_master"] = $tconfig["tsite_url"] . "webimages/upload/ProfileMaster";
        $tconfig["tsite_upload_images_profile_master_size1"] = "16";
        $tconfig["tsite_upload_images_profile_master_size2"] = "32";
        $tconfig["tsite_upload_images_profile_master_size3"] = "48";
        $tconfig["tsite_upload_images_profile_master_size4"] = "64";
        $tconfig["tsite_upload_advertise_banner_path"] = $tconfig["tpanel_path"] . "webimages/upload/AdvImages"; //Added By HJ On 12-12-2018 For Advertisement Banners Path
        $tconfig["tsite_upload_advertise_banner"] = $tconfig["tsite_url"] . "webimages/upload/AdvImages"; //Added By HJ On 12-12-2018 For Advertisement Banners URL
        $tconfig["tsite_upload_manage_app_screen_path"] = $tconfig["tpanel_path"] . "webimages/upload/AppScreenImages";
        $tconfig["tsite_upload_manage_app_screen"] = $tconfig["tsite_url"] . "webimages/upload/AppScreenImages"; //Added By HJ On 12-12-2018 For Advertisement Banners URL Y:\cubejekdev\webimages\upload\AppScreenImages

        $tconfig["tsite_upload_provider_image_path"] = $tconfig["tpanel_path"] . "webimages/upload/Provider_Images/"; //Added By Hasmukh On 24-01-2019 For Provider Image Path
        $tconfig["tsite_upload_provider_image"] = $tconfig["tsite_url"] . "webimages/upload/Provider_Images"; //Added By Hasmukh On 24-01-2019 For Provider Image URL

        $tconfig["tsite_upload_prescription_image_path"] = $tconfig["tpanel_path"] . "webimages/upload/Prescription_Images/"; //For Prescription required added by Sneha
        $tconfig["tsite_upload_prescription_image"] = $tconfig["tsite_url"] . "webimages/upload/Prescription_Images"; //For Prescription required added by Sneha

        //Added By HJ On 26-06-2019 For Define Store Demo Image Folder Path and URL Start

        $tconfig["tsite_upload_demo_compnay_doc_path"] = $tconfig["tpanel_path"] . "webimages/upload/demo_store_img/";
        $tconfig["tsite_upload_demo_compnay_doc"] = $tconfig["tsite_url"] . "webimages/upload/demo_store_img/";

        //Added By HJ On 26-06-2019 For Define Store Demo Image Folder Path and URL End

        //store sample image path
        $tconfig["tsite_sample_images_store_path"] = $tconfig["tpanel_path"] . "webimages/icons/company_sample_images/";

        $tconfig["tmongodb_port"] = "27017";
        $tconfig["tmongodb_databse"] = TSITE_DB;


        $tconfig["tsite_upload_country_images_path"] = $tconfig["tpanel_path"] . "webimages/icons/country_flags/";
        $tconfig["tsite_upload_country_images"] = $tconfig["tsite_url"] . "webimages/icons/country_flags/";
        //added by SP for country images on 07-10-2019 end

        //Added By HJ for App Type Wise Home Content Images Start
        $tconfig["tsite_upload_apptype_page_images"] = $tconfig["tsite_img"] . "/page/home/apptype/";
        $tconfig["tsite_upload_apptype_page_images_panel"] = $tconfig["tpanel_path"] . "assets/img/page/home/apptype/";
        //Added By HJ for App Type Wise Home Content Images End

        $tconfig["tsite_upload_apptype_images"] = $tconfig["tsite_img"] . "/apptype/";
        $tconfig["tsite_upload_apptype_images_panel"] = $tconfig["tpanel_path"] . "assets/img/apptype/";

        $tconfig["tsite_upload_bulk_item_csv_path"] = $tconfig["tpanel_path"] . "webimages/upload/import_items"; //Added By HJ On 07-08-2020 For Upload Bulk Item CSV File Folder

        // $tconfig["tsite_upload_images_lng_page"] = $tconfig["tsite_url"] . "/webimages/upload/lng_pages/";
        $tconfig["tsite_upload_images_lng_page"] = $tconfig["tsite_url"] . "webimages/upload/apptype/lng_pages/";
        $tconfig["tsite_upload_images_lng_page_path"] = $tconfig["tpanel_path"] . "webimages/upload/apptype/lng_pages/";


        /* SERVER Script File Path - DO NOT CHANGE */
        $tconfig["tsite_script_file_path"] = $tconfig["tpanel_path"] . "webimages/script_files/";

        return $tconfig;
    }

    private function ip_visitor_country($vIP) {
        // $IPSTACK_ACCESS_KEY = "26fa0d22dbc6e33eefa349bd43fa8023";
        $IPSTACK_ACCESS_KEY = "a8ac8e0bfea573246bf1517098e12798";

        $response = array();
        try{
            $curl = curl_init();
            curl_setopt_array($curl, array(
              CURLOPT_URL => "http://api.ipstack.com/".$vIP."?access_key=".$IPSTACK_ACCESS_KEY."&format=1",
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_CUSTOMREQUEST => "POST",
            ));

            $api_response = curl_exec($curl);

            curl_close($curl);
            $response = json_decode($api_response, true);       
        }catch(Exception $error){   
            $response = json_decode($error, true);
        }
        
        return $response;

    }

    public function validateLocation() {
        global $oCache;

        $VISITOR_IP = get_client_ip(); 

        $VisitorIpApcKey = md5(str_replace(".", "_", $VISITOR_IP));
        $getVisitorIpData = $oCache->getData($VisitorIpApcKey);

        $NOT_ALLOWED_INHOUSE_DEMOS = array('kingx.v3cube.in', 'plusxcubex.v3cube.in', 'plusxtaxi.v3cube.in', 'superxsp.v3cube.in');
        if(basename($_SERVER['SCRIPT_FILENAME']) == "sys_cube_inhouse_24966546.php" || basename($_SERVER['SCRIPT_FILENAME']) == "sys_cube_inhouse_all_917464.php" || basename($_SERVER['SCRIPT_FILENAME']) == "sys_cube_97146654564.php" || strpos_arr($_SERVER["HTTP_HOST"], $NOT_ALLOWED_INHOUSE_DEMOS) === false) {
            /*if($VISITOR_IP == "14.102.161.227") {
                return true;    
            }
            else {
                $this->blockVisitorIp();
            }*/
            return true; 
        }
            
        if(!empty($getVisitorIpData) && $getVisitorIpData == "Yes"){
            return true;
        } else if(!empty($getVisitorIpData) && $getVisitorIpData == "No") {
            $this->blockVisitorIp(); 
        }

        if(defined('SITE_TYPE') && SITE_TYPE != "Demo" && strpos($_SERVER["HTTP_HOST"], 'bbcsproducts.net') !== false) {
            $setSetupCacheData = $oCache->setData(md5(str_replace(".", "_", $VISITOR_IP)), "No");
            $this->blockVisitorIp();   
        }

        // $ALLOWED_VISITOR_IP_ARR = ["14.102.161.227", "106.214.114.131", "49.206.49.127", "106.214.115.231", "117.99.98.131", "124.123.162.154", "117.99.105.65"];
        $ALLOWED_VISITOR_IP_ARR = array();
        
        $visitor_details = $this->ip_visitor_country($VISITOR_IP);
            
        if($visitor_details['country_code'] == "IN" && !in_array($VISITOR_IP, $ALLOWED_VISITOR_IP_ARR)) {
            $setSetupCacheData = $oCache->setData($VisitorIpApcKey, "No");
            $this->blockVisitorIp();
        } else {
            $setSetupCacheData = $oCache->setData($VisitorIpApcKey, "Yes");
        }
    }

    private function blockVisitorIp() {
         if(basename($_SERVER['SCRIPT_FILENAME']) == "webservice_shark.php") {
            $returnArr['Action'] = "0";
            $returnArr['message'] = "This App is Country Restricted and thus does not work in your Region.";
            setDataResponse($returnArr);
        } else {
            if (stripos($_SERVER['SCRIPT_FILENAME'], 'demo-details/kingx-demo/index.php') !== false) {
                // $this->deleteDemoDetailsData($_REQUEST['id']);
            }
            http_response_code(403);
            echo "<h3>This App is Country Restricted and thus does not work in your Region.</h3>";
            exit;
        }
    }

    private function deleteDemoDetailsData($demo_details_id) {
        global $obj;
        $demo_details_data = $obj->MySQLSelect("SELECT * FROM demo_details_cj WHERE vRandomCode = '$demo_details_id' AND eStatus = 'Active'");

        if(!empty($demo_details_data) && count($demo_details_data) > 0) {
            $vDriverEmail = $demo_details_data[0]['vDriverEmail'];
            $driverData = $obj->MySQLSelect("SELECT iDriverId, vEmail FROM register_driver WHERE vEmail = '$vDriverEmail'");
            if(!empty($driverData) && count($driverData) > 0) {
                $iDriverId = $driverData[0]['iDriverId'];
                $obj->sql_query("UPDATE register_driver SET eStatus = 'Deleted' WHERE iDriverId = '$iDriverId'");
            }

            $vRiderEmail = $demo_details_data[0]['vRiderEmail'];
            $riderData = $obj->MySQLSelect("SELECT iUserId, vEmail FROM register_user WHERE vEmail = '$vRiderEmail'");
            if(!empty($riderData) && count($riderData) > 0) {
                $iUserId = $riderData[0]['iUserId'];
                $obj->sql_query("UPDATE register_user SET eStatus = 'Deleted' WHERE iUserId = '$iUserId'");
            }

            $vHotelEmail = $demo_details_data[0]['vHotelEmail'];
            $hotelData = $obj->MySQLSelect("SELECT iAdminId, vEmail FROM administrators WHERE vEmail = '$vHotelEmail'");
            if(!empty($hotelData) && count($hotelData) > 0) {
                $iAdminId = $hotelData[0]['iAdminId'];
                $obj->sql_query("UPDATE administrators SET eStatus = 'Deleted' WHERE iAdminId = '$iAdminId'");
            }

            $vStoreDataArr = json_decode($demo_details_data[0]['vStoreEmail'], true);
            foreach ($vStoreDataArr as $vStoreData) {
                $vStoreEmail = $vStoreData['vEmail'];
                $storeData = $obj->MySQLSelect("SELECT iCompanyId, vEmail FROM company WHERE vEmail = '$vStoreEmail'");
                if(!empty($storeData) && count($storeData) > 0) {
                    $iCompanyId = $storeData[0]['iCompanyId'];
                    $obj->sql_query("UPDATE company SET eStatus = 'Deleted' WHERE iCompanyId = '$iCompanyId'");
                }
            }

            $Data_arr = array();
            $Data_arr['iDemoDetailsId'] = $demo_details_data[0]['iDemoDetailsId'];
            $Data_arr['vRandomCode'] = $demo_details_id;
            $Data_arr['vIP'] = get_client_ip();
            $Data_arr['dDate'] = date('Y-m-d H:i:s');
            
            $id = $obj->MySQLQueryPerform('demo_views', $Data_arr, 'insert');

            $obj->sql_query("UPDATE demo_details_cj SET eStatus = 'Deleted' WHERE vRandomCode = '$demo_details_id'");
        }
    }
}
?>