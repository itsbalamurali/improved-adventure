<?php





if (!session_id()) {
    session_start();
}

include_once '../common.php';

require_once 'FbLib/autoload.php';

$sql = "SELECT vValue FROM configurations WHERE vName='FACEBOOK_APP_ID'";
$db_appid = $obj->MySQLSelect($sql);

$sql = "SELECT vValue FROM configurations WHERE vName='FACEBOOK_APP_SECRET_KEY'";
$db_key = $obj->MySQLSelect($sql);

$appId = $db_appid[0]['vValue'];
$appsecretkey = $db_key[0]['vValue'];

/*$appId = '231522618286279';
$appsecretkey = '05936d5b7fff9956a21eaa7a3a862165';*/
/*if($appId == ''){
  $appId = '231522618286279';
  $appsecretkey = '05936d5b7fff9956a21eaa7a3a862165';
}*/

if ('' === $appId && '' === $appsecretkey) {
    $loginUrl1 = $tconfig['tsite_url'];
    header('location: '.$loginUrl1);

    exit;
}

$fb = new Facebook\Facebook([
    'app_id' => $appId,
    'app_secret' => $appsecretkey,
    'default_graph_version' => 'v17.0',
]);

$helper = $fb->getRedirectLoginHelper();

if (isset($_GET['state'])) {
    $helper->getPersistentDataHandler()->set('state', $_GET['state']);
}

// Try to get access token
try {
    if (isset($_SESSION['facebook_access_token'])) {
        $accessToken = $_SESSION['facebook_access_token'];
    } else {
        $accessToken = $helper->getAccessToken();
    }
} catch (FacebookResponseException $e) {
    echo 'Graph returned an error: '.$e->getMessage();

    exit;
} catch (FacebookSDKException $e) {
    echo 'Facebook SDK returned an error: '.$e->getMessage();

    exit;
}

/*$permissions = ['email'];
$loginUrl = $helper->getLoginUrl('https://cubejekdev.bbcsproducts.net/testfblogin/fbconfig.php', $permissions);
header("location: ".$loginUrl);*/
