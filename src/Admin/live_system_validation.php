<?php
include_once '../common.php';

require_once '../assets/libraries/pubnub/autoloader.php';

function getConfigurations($tabelName, $LABEL)
{
    global $obj;
    $sql1 = 'SELECT vValue FROM `'.$tabelName."` WHERE vName='".$LABEL."'";
    $CData = $obj->MySQLSelect($sql1);
    $Data_value = $CData[0]['vValue'];

    return $Data_value;
}

$ENABLE_PUBNUB1 = getConfigurations('configurations', 'ENABLE_PUBNUB');

$STRIPE_SECRET_KEY1 = getConfigurations('configurations', 'STRIPE_SECRET_KEY');
$STRIPE_PUBLISH_KEY1 = getConfigurations('configurations', 'STRIPE_PUBLISH_KEY');

$XENDIT_PUBLIC_KEY1 = getConfigurations('configurations', 'XENDIT_PUBLIC_KEY');
$XENDIT_SECRET_KEY1 = getConfigurations('configurations', 'XENDIT_SECRET_KEY');

$OMISE_PUBLIC_KEY1 = getConfigurations('configurations', 'OMISE_PUBLIC_KEY');
$OMISE_SECRET_KEY1 = getConfigurations('configurations', 'OMISE_SECRET_KEY');

$BRAINTREE_TOKEN_KEY1 = getConfigurations('configurations', 'BRAINTREE_TOKEN_KEY');
$BRAINTREE_ENVIRONMENT1 = getConfigurations('configurations', 'BRAINTREE_ENVIRONMENT');
$BRAINTREE_MERCHANT_ID1 = getConfigurations('configurations', 'BRAINTREE_MERCHANT_ID');
$BRAINTREE_PUBLIC_KEY1 = getConfigurations('configurations', 'BRAINTREE_PUBLIC_KEY');
$BRAINTREE_PRIVATE_KEY1 = getConfigurations('configurations', 'BRAINTREE_PRIVATE_KEY');
$BRAINTREE_CHARGE_AMOUNT1 = getConfigurations('configurations', 'BRAINTREE_CHARGE_AMOUNT');

$PAYMAYA_API_URL1 = getConfigurations('configurations', 'PAYMAYA_API_URL');
$PAYMAYA_SECRET_KEY1 = getConfigurations('configurations', 'PAYMAYA_SECRET_KEY');
$PAYMAYA_PUBLISH_KEY1 = getConfigurations('configurations', 'PAYMAYA_PUBLISH_KEY');
$PAYMAYA_ENVIRONMENT_MODE1 = getConfigurations('configurations', 'PAYMAYA_ENVIRONMENT_MODE');

$ADYEN_MERCHANT_ACCOUNT1 = getConfigurations('configurations', 'ADYEN_MERCHANT_ACCOUNT');
$ADYEN_CHARGE_AMOUNT1 = getConfigurations('configurations', 'ADYEN_CHARGE_AMOUNT');
$ADYEN_USER_NAME1 = getConfigurations('configurations', 'ADYEN_USER_NAME');
$ADYEN_PASSWORD1 = getConfigurations('configurations', 'ADYEN_PASSWORD');
$ADYEN_API_URL1 = getConfigurations('configurations', 'ADYEN_API_URL');

$uuid = 'fg5k3i7i7l5ghgk1jcv43w0j41';

$pubnub = new Pubnub\Pubnub([
    'publish_key' => $PUBNUB_PUBLISH_KEY,
    'subscribe_key' => $PUBNUB_SUBSCRIBE_KEY,
    'uuid' => $uuid,
]);
$channel_name = 'admin'.$_SESSION['sess_vAdminEmail'];
$messages = $pubnub->publish($channel_name, 'Checking PubNub Credentials');

$geocode = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address=1600+Amphitheatre+Parkway,+Mountain+View,+CA&key='.$GOOGLE_SEVER_API_KEY_WEB);
$output = json_decode($geocode);
$google_status = $output->status;
/*var_dump($messages);
die;*/

?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->
    <!-- BEGIN HEAD-->
    <head>
        <meta charset="UTF-8" />
        <title><?php echo $SITE_NAME; ?> | System Diagnostic</title>
        <meta content="width=device-width, initial-scale=1.0" name="viewport" />
        <!--[if IE]>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <![endif]-->
        <!-- GLOBAL STYLES -->
        <?php include_once 'global_files.php'; ?>
        <link rel="stylesheet" href="css/style.css" />
        <link rel="stylesheet" href="css/new_main.css" />
        <link rel="stylesheet" href="css/adminLTE/AdminLTE.min.css" />
        <!-- END THIS PAGE PLUGINS-->
        <!--END GLOBAL STYLES -->

        <!-- PAGE LEVEL STYLES -->
        <!-- END PAGE LEVEL  STYLES -->
        <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
                <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
                <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
        <![endif]-->
    </head>
    <!-- END  HEAD-->
    <!-- BEGIN BODY-->
    <body class="padTop53">
    	<div class="container">
	    	<h3>Live Site Error check</h3>
	    	<?php $error = 0; ?>
		    	<ul class="list-group">
		    		<?php // Start PubNub
                    if ('Yes' === $ENABLE_PUBNUB1) {
                        if ('' === $PUBNUB_PUBLISH_KEY || '' === $PUBNUB_SUBSCRIBE_KEY || '' === $PUBNUB_SECRET_KEY) { ?>
				  			<li class="list-group-item list-group-item-warning">Please Add Valid Pubnub keys</li>
		    		<?php ++$error;
                        }
                    }
// End PubNub
// Start Google
if ('Yes' === $PASSENGER_GOOGLE_LOGIN || 'Yes' === $DRIVER_GOOGLE_LOGIN) {
    if ('' === $GOOGLE_SENDER_ID || '' === $GOOGLE_SEVER_GCM_API_KEY || '' === $GOOGLE_SEVER_API_KEY_WEB || '' === $GOOGLE_PLUS_APP_NAME || '' === $GOOGLE_PLUS_OAUTH_CLIENT_ID || '' === $GOOGLE_PLUS_OAUTH_CLIENT_SECRET || '' === $GOOGLE_PLUS_OAUTH_REDIRECT_URI || '' === $GOOGLE_PLUS_OAUTH_REDIRECT_URI) { ?>
				  			<li class="list-group-item list-group-item-warning">Please Add Valid Google keys for App and Web and if you do not want to add Keys then make sure PASSENGER_GOOGLE_LOGIN and DRIVER_GOOGLE_LOGIN option will be set as No.</li>
		    		<?php ++$error;
    }
}
// End Google
// Start Twillio
if ('' === $MOBILE_VERIFY_TOKEN_TWILIO || '' === $MOBILE_VERIFY_SID_TWILIO || '' === $MOBILE_NO_TWILIO) { ?>
				  		<li class="list-group-item list-group-item-warning">Please Add Valid Twillio keys otherwise sms for application will not working.</li>
		    		<?php ++$error;
}
// End Twillio
// Start Facebook
if ('Yes' === $DRIVER_FACEBOOK_LOGIN || 'Yes' === $PASSENGER_FACEBOOK_LOGIN) {
    if ('' === $FACEBOOK_APP_SECRET_KEY || '' === $FACEBOOK_APP_ID) { ?>
		    				<li class="list-group-item list-group-item-warning">Please Add Valid Facebook keys for App and Web and if you do not want to add Keys then make sure DRIVER_FACEBOOK_LOGIN and PASSENGER_FACEBOOK_LOGIN option will be set as No.</li>
		    					<?php ++$error;
    }
}
// End Facebook
// Start Twitter
if ('Yes' === $DRIVER_TWITTER_LOGIN || 'Yes' === $PASSENGER_TWITTER_LOGIN) {
    if ('' === $TWITTER_CONSUMER_KEY || '' === $TWITTER_CONSUMER_SECRET || '' === $TWITTER_OAUTH_ACCESS_TOKEN || '' === $TWITTER_OAUTH_ACCESS_TOKEN_SECRET) { ?>
		    				<li class="list-group-item list-group-item-warning">Please Add Valid Twitter keys for App and Web and if you do not want to add Keys then make sure DRIVER_TWITTER_LOGIN and PASSENGER_TWITTER_LOGIN option will be set as No.</li>
		    					<?php ++$error;
    }
}
// End Twitter
// check payment method
if ('Cash-Card' === $APP_PAYMENT_MODE || 'Card' === $APP_PAYMENT_MODE) {
    if ('Stripe' === $APP_PAYMENT_METHOD) {
        if ('' === $STRIPE_SECRET_KEY1 || '' === $STRIPE_PUBLISH_KEY1) { ?>
		    					<li class="list-group-item list-group-item-warning">Please Add Valid Stripe keys</li>
		    				<?php ++$error;
        }
        if (str_contains($STRIPE_SECRET_KEY1, 'test') || str_contains($STRIPE_PUBLISH_KEY1, 'test')) { ?>
								<li class="list-group-item list-group-item-warning">Stripe have test keys please add live keys in configuration tabel.</li>
						<?php	++$error;
        }
    } elseif ('Braintree' === $APP_PAYMENT_METHOD) {
        if ('' === $BRAINTREE_TOKEN_KEY1 || '' === $BRAINTREE_ENVIRONMENT1 || '' === $BRAINTREE_MERCHANT_ID1 || '' === $BRAINTREE_PUBLIC_KEY1 || '' === $BRAINTREE_PRIVATE_KEY1 || '' === $BRAINTREE_CHARGE_AMOUNT1) { ?>
		    					<li class="list-group-item list-group-item-warning">Please Add Valid BrainTree keys</li>
		    				<?php ++$error;
        }
        if ('sandbox' === $BRAINTREE_ENVIRONMENT1) { ?>
		    				<li class="list-group-item list-group-item-warning">Braintree have test keys please add live keys in configuration tabel.</li>
		    			<?php	++$error;
        }
    } elseif ('Paymaya' === $APP_PAYMENT_METHOD) {
        if ('' === $PAYMAYA_API_URL1 || '' === $PAYMAYA_SECRET_KEY1 || '' === $PAYMAYA_PUBLISH_KEY1 || '' === $PAYMAYA_ENVIRONMENT_MODE1) { ?>
		    					<li class="list-group-item list-group-item-warning">Please Add Valid PayMaya keys</li>
		    				<?php ++$error;
        }
        if ('Sandbox' === $PAYMAYA_ENVIRONMENT_MODE1) { ?>
		    				<li class="list-group-item list-group-item-warning">>Paymaya have test keys please add live keys in configuration tabel.</li>
		    			<?php	++$error;
        }
    } elseif ('Omise' === $APP_PAYMENT_METHOD) {
        if ('' === $OMISE_PUBLIC_KEY1 || '' === $OMISE_SECRET_KEY1) { ?>
		    					<li class="list-group-item list-group-item-warning">Please Add Valid Omise keys</li>
		    				<?php ++$error;
        }
        if (str_contains($OMISE_PUBLIC_KEY1, 'test') || str_contains($OMISE_SECRET_KEY1, 'test')) { ?>
		    				<li class="list-group-item list-group-item-warning">>Omise have test keys please add live keys in configuration tabel.</li>
		    			<?php ++$error;
        }
    } elseif ('Adyen' === $APP_PAYMENT_METHOD) {
        if ('' === $ADYEN_MERCHANT_ACCOUNT1 || '' === $ADYEN_CHARGE_AMOUNT1 || '' === $ADYEN_USER_NAME1 || '' === $ADYEN_PASSWORD1 || '' === $ADYEN_API_URL1) { ?>
		    					<li class="list-group-item list-group-item-warning">Please Add Valid adyen keys</li>
		    				<?php ++$error;
        }
    } elseif ('Xendit' === $APP_PAYMENT_METHOD) {
        if ('' === $XENDIT_PUBLIC_KEY1 || '' === $XENDIT_SECRET_KEY1) { ?>
		    					<li class="list-group-item list-group-item-warning">Please Add Valid Xendit keys</li>
		    				<?php ++$error;
        }
        if (str_contains($XENDIT_PUBLIC_KEY1, 'development') || str_contains($XENDIT_SECRET_KEY1, 'development')) { ?>
		    				<li class="list-group-item list-group-item-warning">>Xendit have test keys please add live keys in configuration tabel.</li>
		    			<?php ++$error;
        }
    }
}
// End payment method
// pubnub check
if ('Sent' !== $messages[1]) { ?>
		    			<li class="list-group-item list-group-item-warning">Please check pubnub keys are valid or not.</li>
		    		<?php ++$error;
}
// pubnunb check end
// google daily Quotes exceed
if ('OVER_QUERY_LIMIT' === $google_status) { ?>
		    			<li class="list-group-item list-group-item-warning">google Daily limit has been reached.</li>
		    		<?php ++$error;
}
?>
				</ul>
			<div>
	    		<a href="dashboard.php" class="btn btn-primary btn-lg active" role="button" aria-pressed="true">Continue <?php echo $error; ?></a>
	    	</div>
    	</div>
    </body>
</html>
<?php if (0 === $error) {
    header('Location: dashboard.php');

    exit;
} ?>