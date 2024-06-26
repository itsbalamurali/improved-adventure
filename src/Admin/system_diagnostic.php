<?php
include_once '../common.php';

require_once '../assets/libraries/pubnub/autoloader.php';

function getConfigurations($tabelName, $LABEL)
{
    global $obj;
    $sql1 = 'SELECT vValue FROM `'.$tabelName."` WHERE vName='".$LABEL."'";
    $CData = $obj->MySQLSelect($sql1);
    $Data_value = 'No';
    if (isset($CData[0]['vValue'])) {
        $Data_value = $CData[0]['vValue'];
    }

    return $Data_value;
}

// echo "<pre>";
$getConfiguration = $obj->MySQLSelect('SELECT vValue,vName FROM configurations');
$getConfiguration_payment = $obj->MySQLSelect('SELECT vValue,vName FROM configurations_payment');
$configArr = [];
for ($c = 0; $c < count($getConfiguration); ++$c) {
    $configArr[$getConfiguration[$c]['vName']] = $getConfiguration[$c]['vValue'];
}
for ($pa = 0; $pa < count($getConfiguration_payment); ++$pa) {
    $configArr[$getConfiguration_payment[$pa]['vName']] = $getConfiguration_payment[$pa]['vValue'];
}
$ENABLE_PUBNUB1 = $configArr['ENABLE_PUBNUB'];
$PUBNUB_PUBLISH_KEY = $configArr['PUBNUB_PUBLISH_KEY'];
$PUBNUB_SUBSCRIBE_KEY = $configArr['PUBNUB_SUBSCRIBE_KEY'];
$PUBNUB_SECRET_KEY = $configArr['PUBNUB_SECRET_KEY'];
$PUBSUB_TECHNIQUE = $configArr['PUBSUB_TECHNIQUE'];
$BRAINTREE_CHARGE_AMOUNT1 = $configArr['BRAINTREE_CHARGE_AMOUNT'];
$ADYEN_CHARGE_AMOUNT1 = $configArr['ADYEN_CHARGE_AMOUNT'];
$DRIVER_TWITTER_LOGIN = $configArr['DRIVER_TWITTER_LOGIN'];
$PASSENGER_TWITTER_LOGIN = $configArr['PASSENGER_TWITTER_LOGIN'];
$APP_PAYMENT_METHOD = $configArr['APP_PAYMENT_METHOD'];
$PASSENGER_GOOGLE_LOGIN = $configArr['PASSENGER_GOOGLE_LOGIN'];
$DRIVER_GOOGLE_LOGIN = $configArr['DRIVER_GOOGLE_LOGIN'];
$DRIVER_FACEBOOK_LOGIN = $configArr['DRIVER_FACEBOOK_LOGIN'];
$PASSENGER_FACEBOOK_LOGIN = $configArr['PASSENGER_FACEBOOK_LOGIN'];
if (isset($configArr['SYSTEM_PAYMENT_ENVIRONMENT']) && 'Live' === $configArr['SYSTEM_PAYMENT_ENVIRONMENT']) {
    $STRIPE_SECRET_KEY1 = $configArr['STRIPE_SECRET_KEY_LIVE'];
    $STRIPE_PUBLISH_KEY1 = $configArr['STRIPE_PUBLISH_KEY_LIVE'];

    $XENDIT_PUBLIC_KEY1 = $configArr['XENDIT_PUBLIC_KEY_LIVE'];
    $XENDIT_SECRET_KEY1 = $configArr['XENDIT_SECRET_KEY_LIVE'];

    $OMISE_PUBLIC_KEY1 = $configArr['OMISE_PUBLIC_KEY_LIVE'];
    $OMISE_SECRET_KEY1 = $configArr['OMISE_SECRET_KEY_LIVE'];

    $BRAINTREE_TOKEN_KEY1 = $configArr['BRAINTREE_TOKEN_KEY_LIVE'];
    $BRAINTREE_ENVIRONMENT1 = $configArr['BRAINTREE_ENVIRONMENT_LIVE'];
    $BRAINTREE_MERCHANT_ID1 = $configArr['BRAINTREE_MERCHANT_ID_LIVE'];
    $BRAINTREE_PUBLIC_KEY1 = $configArr['BRAINTREE_PUBLIC_KEY_LIVE'];
    $BRAINTREE_PRIVATE_KEY1 = $configArr['BRAINTREE_PRIVATE_KEY_LIVE'];

    $PAYMAYA_API_URL1 = $configArr['PAYMAYA_API_URL_LIVE'];
    $PAYMAYA_SECRET_KEY1 = $configArr['PAYMAYA_SECRET_KEY_LIVE'];
    $PAYMAYA_PUBLISH_KEY1 = $configArr['PAYMAYA_PUBLISH_KEY_LIVE'];
    $PAYMAYA_ENVIRONMENT_MODE1 = $configArr['PAYMAYA_ENVIRONMENT_MODE_LIVE'];

    $ADYEN_MERCHANT_ACCOUNT1 = $configArr['ADYEN_MERCHANT_ACCOUNT_LIVE'];
    $ADYEN_CHARGE_AMOUNT1 = $configArr['ADYEN_CHARGE_AMOUNT_LIVE'];
    $ADYEN_USER_NAME1 = $configArr['ADYEN_USER_NAME_LIVE'];
    $ADYEN_PASSWORD1 = $configArr['ADYEN_PASSWORD_LIVE'];
    $ADYEN_API_URL1 = $configArr['ADYEN_API_URL_LIVE'];

    $ADYEN_API_URL1 = $configArr['ADYEN_API_URL_LIVE'];
} else {
    $STRIPE_SECRET_KEY1 = $configArr['STRIPE_SECRET_KEY_SANDBOX'];
    $STRIPE_PUBLISH_KEY1 = $configArr['STRIPE_PUBLISH_KEY_SANDBOX'];

    $XENDIT_PUBLIC_KEY1 = $configArr['XENDIT_PUBLIC_KEY_SANDBOX'];
    $XENDIT_SECRET_KEY1 = $configArr['XENDIT_SECRET_KEY_SANDBOX'];

    $OMISE_PUBLIC_KEY1 = $configArr['OMISE_PUBLIC_KEY_SANDBOX'];
    $OMISE_SECRET_KEY1 = $configArr['OMISE_SECRET_KEY_SANDBOX'];

    $BRAINTREE_TOKEN_KEY1 = $configArr['BRAINTREE_TOKEN_KEY_SANDBOX'];
    $BRAINTREE_ENVIRONMENT1 = $configArr['BRAINTREE_ENVIRONMENT_SANDBOX'];
    $BRAINTREE_MERCHANT_ID1 = $configArr['BRAINTREE_MERCHANT_ID_SANDBOX'];
    $BRAINTREE_PUBLIC_KEY1 = $configArr['BRAINTREE_PUBLIC_KEY_SANDBOX'];
    $BRAINTREE_PRIVATE_KEY1 = $configArr['BRAINTREE_PRIVATE_KEY_SANDBOX'];

    $PAYMAYA_API_URL1 = $configArr['PAYMAYA_API_URL_SANDBOX'];
    $PAYMAYA_SECRET_KEY1 = $configArr['PAYMAYA_SECRET_KEY_SANDBOX'];
    $PAYMAYA_PUBLISH_KEY1 = $configArr['PAYMAYA_PUBLISH_KEY_SANDBOX'];
    $PAYMAYA_ENVIRONMENT_MODE1 = $configArr['PAYMAYA_ENVIRONMENT_MODE_SANDBOX'];

    $ADYEN_MERCHANT_ACCOUNT1 = $configArr['ADYEN_MERCHANT_ACCOUNT_SANDBOX'];

    $ADYEN_USER_NAME1 = $configArr['ADYEN_USER_NAME_SANDBOX'];
    $ADYEN_PASSWORD1 = $configArr['ADYEN_PASSWORD_SANDBOX'];
    $ADYEN_API_URL1 = $configArr['ADYEN_API_URL_SANDBOX'];

    $ADYEN_API_URL1 = $configArr['ADYEN_API_URL_SANDBOX'];
}
$uuid = 'fg5k3i7i7l5ghgk1jcv43w0j41';
if ('Yes' === $ENABLE_PUBNUB1) {
    $pubnub = new Pubnub\Pubnub([
        'publish_key' => $PUBNUB_PUBLISH_KEY,
        'subscribe_key' => $PUBNUB_SUBSCRIBE_KEY,
        'uuid' => $uuid,
    ]);
    $channel_name = 'admin'.$_SESSION['sess_vAdminEmail'];
    $messages = $pubnub->publish($channel_name, 'Checking PubNub Credentials');
}
$geocode = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address=1600+Amphitheatre+Parkway,+Mountain+View,+CA&key='.$GOOGLE_SEVER_API_KEY_WEB);
$output = json_decode($geocode);
$google_status = $output->status;

function chkServer($host, $port)
{
    $hostip = @gethostbyname($host);
    if ($hostip === $host) {
        return 'SERVER_DOWN_OR_NOTEXISTS';
    }
    if (!$x = @fsockopen($hostip, $port, $errno, $errstr, 5)) {
        return 'PORTCLOSE';
    }
    if ($x) {
        @fclose($x);
    }

    return 'PORTOPEN';
}

function getSocketURL()
{
    global $tconfig;
    $url = $tconfig['tsite_sc_protocol'].$tconfig['tsite_sc_host'].':'.$tconfig['tsite_host_sc_port'];

    return $url;
}

$stocketURL = getSocketURL();

function checkStocketURL($url)
{
    $array = get_headers($url);
    $string = $array[0];
    if (strpos($string, '200')) {
        return true;
    }

    return false;
}
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->
    <!-- BEGIN HEAD-->
    <head>
        <meta charset="UTF-8" />
        <title><?php echo $SITE_NAME; ?> | System Configuration check</title>
        <meta content="width=device-width, initial-scale=1.0" name="viewport" />
        <?php include_once 'global_files.php'; ?>
        <link rel="stylesheet" href="css/style.css" />
        <link rel="stylesheet" href="css/new_main.css" />
        <link rel="stylesheet" href="css/adminLTE/AdminLTE.min.css" />
    </head>
    <style>
        ul.list-group li {
            margin-bottom: 15px;
        }
        .succesli{
            background: lightgreen;
        }
        .failli{
            background: lightpink;
        }
    </style>
    <!-- END  HEAD-->
    <!-- BEGIN BODY-->
    <body class="padTop53">
        <?php include_once 'header.php'; ?>
        <?php include_once 'left_menu.php'; ?>
        <div id="content">
            <div class="inner" style="min-height: 700px;">
                <div class="row">
                    <div class="col-lg-12">
                        <h1>System Configuration check</h1>
                    </div>
                </div>
                <hr />
                <?php $error = 0; ?>
                <ul class="list-group">
                    <?php
// Start check curl
                    if (extension_loaded('curl')) {
                        echo '<li class="list-group-item list-group-item-warning succesli">CURL is install.</li>';
                    } else {
                        echo '<li class="list-group-item list-group-item-warning failli">CURL is not install. CURL should be Install</li>';
                        ++$error;
                    }
// End check curl
?>
                    <?php
// Start check allow_url_fopen
if (ini_get('allow_url_fopen')) {
    echo '<li class="list-group-item list-group-item-warning succesli">allow_url_fopen is ON.</li>';
} else {
    echo '<li class="list-group-item list-group-item-warning failli">allow_url_fopen is OFF.allow_url_fopen should be ON</li>';
    ++$error;
}
// End check allow_url_fopen
?>
                    <?php
// Start check 2195 port in gateway.sandbox.push.apple.com
$server_response = chkServer('gateway.sandbox.push.apple.com', 2_195);
if (!empty($server_response)) {
    if ('SERVER_DOWN_OR_NOTEXISTS' === $server_response) {
        echo '<li class="list-group-item list-group-item-warning failli">gateway.sandbox.push.apple.com server is down or does not exist</li>';
        ++$error;
    } elseif ('PORTCLOSE' === $server_response) {
        echo '<li class="list-group-item list-group-item-warning failli">2195 port is close. Please open 2195 port</li>';
        ++$error;
    } elseif ('PORTOPEN' === $server_response) {
        echo '<li class="list-group-item list-group-item-warning succesli">2195 port is open</li>';
    } else {
        echo '<li class="list-group-item list-group-item-warning failli">gateway.sandbox.push.apple.com server is down or does not exist</li>';
        ++$error;
    }
} else {
    echo '<li class="list-group-item list-group-item-warning failli">gateway.sandbox.push.apple.com server is down or does not exist</li>';
    ++$error;
}
// End check 2195 port in gateway.sandbox.push.apple.com
?>
                    <?php
// Start PubNub
if ('Yes' === $ENABLE_PUBNUB1) {
    if ('' === $PUBNUB_PUBLISH_KEY || '' === $PUBNUB_SUBSCRIBE_KEY || '' === $PUBNUB_SECRET_KEY) {
        ?>
                            <li class="list-group-item list-group-item-warning failli">Please Add Valid Pubnub keys</li>
                            <?php
        ++$error;
    } else {
        echo '<li class="list-group-item list-group-item-warning succesli">Pubnub Keys are valid.</li>';
    }
}
// End PubNub
// Start SocketCluster URL
if ('SocketCluster' === $PUBSUB_TECHNIQUE) {
    if (checkStocketURL($stocketURL)) {
        echo '<li class="list-group-item list-group-item-warning succesli">SocketCluster URL is working</li>';
        ++$error;
    } else {
        echo '<li class="list-group-item list-group-item-warning failli">SocketCluster URL is not working.</li>';
    }
}
// End SocketCluster URL
// Start SocketCluster URL
if ('Voip' === $RIDE_DRIVER_CALLING_METHOD) {
    if ('' === $SINCH_APP_KEY || '' === $SINCH_APP_ENVIRONMENT_HOST || '' === $SINCH_APP_SECRET_KEY) {
        echo '<li class="list-group-item list-group-item-warning failli">Please Add Valid Sinch SecretKey,Host,App Key for App</li>';
        ++$error;
    } else {
        echo '<li class="list-group-item list-group-item-warning succesli">Sinch SecretKey,Host,App is Valid.</li>';
    }
}
// End SocketCluster URL
// Start Google
if ('Yes' === $PASSENGER_GOOGLE_LOGIN || 'Yes' === $DRIVER_GOOGLE_LOGIN) {
    if ('' === $GOOGLE_SENDER_ID || '' === $GOOGLE_SEVER_GCM_API_KEY || '' === $GOOGLE_SEVER_API_KEY_WEB || '' === $GOOGLE_PLUS_APP_NAME || '' === $GOOGLE_PLUS_OAUTH_CLIENT_ID || '' === $GOOGLE_PLUS_OAUTH_CLIENT_SECRET || '' === $GOOGLE_PLUS_OAUTH_REDIRECT_URI || '' === $GOOGLE_PLUS_OAUTH_REDIRECT_URI) {
        ?>
                            <li class="list-group-item list-group-item-warning failli">Please Add Valid Google keys for App and Web and if you do not want to add Keys then make sure PASSENGER_GOOGLE_LOGIN and DRIVER_GOOGLE_LOGIN option will be set as No.</li>
                            <?php
        ++$error;
    } else {
        echo '<li class="list-group-item list-group-item-warning succesli">Google Keys are valid.</li>';
    }
}
// End Google
// Start LinkedIn
if ('Yes' === $PASSENGER_LINKEDIN_LOGIN || 'Yes' === $DRIVER_LINKEDIN_LOGIN) {
    if ('' === $LINKEDIN_APP_SECRET_KEY || '' === $LINKEDIN_APP_ID) {
        ?>
                            <li class="list-group-item list-group-item-warning failli">Please Add Valid LinkedIn keys for App and Web and if you do not want to add Keys then make sure DRIVER_LINKEDIN_LOGIN and PASSENGER_LINKEDIN_LOGIN option will be set as No.</li>
                            <?php
        ++$error;
    } else {
        echo '<li class="list-group-item list-group-item-warning succesli">LinkedIn Keys are valid.</li>';
    }
}
// End Google
// Start Twillio
if ('' === $MOBILE_VERIFY_TOKEN_TWILIO || '' === $MOBILE_VERIFY_SID_TWILIO || '' === $MOBILE_NO_TWILIO) {
    ?>
                        <li class="list-group-item list-group-item-warning failli">Please Add Valid Twillio keys otherwise sms for application will not working.</li>
                        <?php
    ++$error;
}
// End Twillio
// Start Facebook
if ('Yes' === $DRIVER_FACEBOOK_LOGIN || 'Yes' === $PASSENGER_FACEBOOK_LOGIN) {
    if ('' === $FACEBOOK_APP_SECRET_KEY || '' === $FACEBOOK_APP_ID) {
        ?>
                            <li class="list-group-item list-group-item-warning failli">Please Add Valid Facebook keys for App and Web and if you do not want to add Keys then make sure DRIVER_FACEBOOK_LOGIN and PASSENGER_FACEBOOK_LOGIN option will be set as No.</li>
                            <?php
        ++$error;
    } else {
        echo '<li class="list-group-item list-group-item-warning succesli">Facebook Keys are valid.</li>';
    }
}
// End Facebook
// Start Twitter
if ('Yes' === $DRIVER_TWITTER_LOGIN || 'Yes' === $PASSENGER_TWITTER_LOGIN) {
    if ('' === $TWITTER_CONSUMER_KEY || '' === $TWITTER_CONSUMER_SECRET || '' === $TWITTER_OAUTH_ACCESS_TOKEN || '' === $TWITTER_OAUTH_ACCESS_TOKEN_SECRET) {
        ?>
                            <li class="list-group-item list-group-item-warning failli">Please Add Valid Twitter keys for App and Web and if you do not want to add Keys then make sure DRIVER_TWITTER_LOGIN and PASSENGER_TWITTER_LOGIN option will be set as No.</li>
                            <?php
        ++$error;
    } else {
        echo '<li class="list-group-item list-group-item-warning succesli">Twitter Keys are valid.</li>';
    }
}
// End Twitter
// check payment method
if ('Cash-Card' === $APP_PAYMENT_MODE || 'Card' === $APP_PAYMENT_MODE) {
    if ('Stripe' === $APP_PAYMENT_METHOD) {
        if ('' === $STRIPE_SECRET_KEY1 || '' === $STRIPE_PUBLISH_KEY1) {
            ?>
                                <li class="list-group-item list-group-item-warning failli">Please Add Valid Stripe keys</li>
                                <?php
            ++$error;
        } else {
            echo '<li class="list-group-item list-group-item-warning succesli">Stripe Keys are valid.</li>';
        }
        if (str_contains($STRIPE_SECRET_KEY1, 'test') || str_contains($STRIPE_PUBLISH_KEY1, 'test')) {
            ?>
                                <li class="list-group-item list-group-item-warning failli">Stripe have test keys please add live keys in configuration tabel.</li>
                                <?php
            ++$error;
        } else {
            echo '<li class="list-group-item list-group-item-warning succesli">Stripe have live keys.</li>';
        }
    } elseif ('Braintree' === $APP_PAYMENT_METHOD) {
        if ('' === $BRAINTREE_TOKEN_KEY1 || '' === $BRAINTREE_ENVIRONMENT1 || '' === $BRAINTREE_MERCHANT_ID1 || '' === $BRAINTREE_PUBLIC_KEY1 || '' === $BRAINTREE_PRIVATE_KEY1 || '' === $BRAINTREE_CHARGE_AMOUNT1) {
            ?>
                                <li class="list-group-item list-group-item-warning failli">Please Add Valid BrainTree keys</li>
                                <?php
            ++$error;
        } else {
            echo '<li class="list-group-item list-group-item-warning succesli">Braintree keys are valid.</li>';
        }
        if ('sandbox' === $BRAINTREE_ENVIRONMENT1) {
            ?>
                                <li class="list-group-item list-group-item-warning failli">Braintree have test keys please add live keys in configuration tabel.</li>
                                <?php
            ++$error;
        } else {
            echo '<li class="list-group-item list-group-item-warning succesli">Braintree have live keys.</li>';
        }
    } elseif ('Paymaya' === $APP_PAYMENT_METHOD) {
        if ('' === $PAYMAYA_API_URL1 || '' === $PAYMAYA_SECRET_KEY1 || '' === $PAYMAYA_PUBLISH_KEY1 || '' === $PAYMAYA_ENVIRONMENT_MODE1) {
            ?>
                                <li class="list-group-item list-group-item-warning">Please Add Valid PayMaya keys</li>
                                <?php
            ++$error;
        } else {
            echo '<li class="list-group-item list-group-item-warning succesli">PayMaya keys are valid.</li>';
        }
        if ('Sandbox' === $PAYMAYA_ENVIRONMENT_MODE1) {
            ?>
                                <li class="list-group-item list-group-item-warning failli">>Paymaya have test keys please add live keys in configuration tabel.</li>
                                <?php
            ++$error;
        } else {
            echo '<li class="list-group-item list-group-item-warning succesli">PayMayahave live keys.</li>';
        }
    } elseif ('Omise' === $APP_PAYMENT_METHOD) {
        if ('' === $OMISE_PUBLIC_KEY1 || '' === $OMISE_SECRET_KEY1) {
            ?>
                                <li class="list-group-item list-group-item-warning failli">Please Add Valid Omise keys</li>
                                <?php
            ++$error;
        } else {
            echo '<li class="list-group-item list-group-item-warning succesli">Omise keys are valid.</li>';
        }
        if (str_contains($OMISE_PUBLIC_KEY1, 'test') || str_contains($OMISE_SECRET_KEY1, 'test')) {
            ?>
                                <li class="list-group-item list-group-item-warning failli">>Omise have test keys please add live keys in configuration tabel.</li>
                                <?php
            ++$error;
        } else {
            echo '<li class="list-group-item list-group-item-warning succesli">Omise keys are live.</li>';
        }
    } elseif ('Adyen' === $APP_PAYMENT_METHOD) {
        if ('' === $ADYEN_MERCHANT_ACCOUNT1 || '' === $ADYEN_CHARGE_AMOUNT1 || '' === $ADYEN_USER_NAME1 || '' === $ADYEN_PASSWORD1 || '' === $ADYEN_API_URL1) {
            ?>
                                <li class="list-group-item list-group-item-warning failli">Please Add Valid adyen keys</li>
                                <?php
            ++$error;
        } else {
            echo '<li class="list-group-item list-group-item-warning succesli">Adyen keys are added.</li>';
        }
    } elseif ('Xendit' === $APP_PAYMENT_METHOD) {
        if ('' === $XENDIT_PUBLIC_KEY1 || '' === $XENDIT_SECRET_KEY1) {
            ?>
                                <li class="list-group-item list-group-item-warning failli">Please Add Valid Xendit keys</li>
                                <?php
            ++$error;
        } else {
            echo '<li class="list-group-item list-group-item-warning succesli">Xendit keys are added.</li>';
        }
        if (str_contains($XENDIT_PUBLIC_KEY1, 'development') || str_contains($XENDIT_SECRET_KEY1, 'development')) {
            ?>
                                <li class="list-group-item list-group-item-warning failli">>Xendit have test keys please add live keys in configuration tabel.</li>
                                <?php
            ++$error;
        } else {
            echo '<li class="list-group-item list-group-item-warning succesli">Xendit keys are live mode.</li>';
        }
    }
}
// End payment method
// pubnub check
if (isset($messages[1]) && 'Sent' !== $messages[1]) {
    ?>
                        <li class="list-group-item list-group-item-warning failli">Please check pubnub keys are valid or not.</li>
                        <?php
    ++$error;
} else {
    ?>
                        <li class="list-group-item list-group-item-warning succesli">Pubnub working proper.</li>
                        <?php
}
if ('OVER_QUERY_LIMIT' === $google_status) {
    ?>
                        <li class="list-group-item list-group-item-warning failli">google Daily limit has been reached.</li>
                        <?php
    ++$error;
} else {
    ?>
                        <li class="list-group-item list-group-item-warning succesli">google working proper.</li>
                        <?php }
?>
                </ul>
                <div class="admin-notes">
                        <h4>Notes:</h4>
                        <ul>
                            <li>
                                Green denotes that configuration is fine.
                            </li>
                            <li>
                                Red denotes that there is some configuration issue.
                            </li>
                        </ul>
                    </div>
            </div>
        </div>
    </body>
</html>