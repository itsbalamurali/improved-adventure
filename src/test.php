<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// date_default_timezone_set('Asia/Kuala_Lumpur');
$keyfile = $_SERVER['DOCUMENT_ROOT'] . 'AuthKey_ST2PGKJL34.p8';     # <- Your AuthKey file
$keyid = 'ST2PGKJL34';                                              # <- Your Key ID
$teamid = 'EV4QR9FNKX';                                             # <- Your Team ID (see Developer Portal)
$bundleid = 'com.kingxpro.user.LiveActivityData';                                  # <- Your Bundle ID
$url = 'https://api.development.push.apple.com';                    # <- development url, or use http://api.push.apple.com for production environment
$token = '801f25db0fdeef06aae4559d9b30ae901a7a627a3aa3835bb389bed9fd657e9039bad33ef5dce2eaf87de06f75511e2e680033706fa61419fa3b9243623456bac3001e0f5cd4b733154b8d700408a278';                                # <- Device Token

// $message = '{"aps":{"alert":"Hi there!","sound":"default"}}';

$message = '{"aps":{"alert":"","content-available":1,"body":{"vTitle":"test'.mt_rand(0000,22222).'","vSound":"","vRandomCode":163792s0934},"sound":"","category":"","apns-push-type":"background"}}';
//echo $message."<br>";
//exit;
$key = openssl_pkey_get_private('file://'.$keyfile);

$header = ['alg'=>'ES256','kid'=>$keyid];
$claims = ['iss'=>$teamid,'iat'=>time()];

$header_encoded = base64($header);
$claims_encoded = base64($claims);



$signature = '';
openssl_sign($header_encoded . '.' . $claims_encoded, $signature, $key, 'sha256');
$jwt = $header_encoded . '.' . $claims_encoded . '.' . base64_encode($signature);

// only needed for PHP prior to 5.5.24
if (!defined('CURL_HTTP_VERSION_2_0')) {
    define('CURL_HTTP_VERSION_2_0', 3);
}
// echo $jwt; exit;

// echo "$url/3/device/$token"; exit;
$http2ch = curl_init();
curl_setopt_array($http2ch, array(
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_2_0,
    CURLOPT_URL => "$url/3/device/$token",
    CURLOPT_PORT => 443,
    CURLOPT_HTTPHEADER => array(
        "apns-topic: {$bundleid}",
        "authorization: bearer $jwt"
    ),
    CURLOPT_POST => TRUE,
    CURLOPT_POSTFIELDS => $message,
    CURLOPT_RETURNTRANSFER => TRUE,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HEADER => 1
));

$result = curl_exec($http2ch);
echo "$result";
if ($result === FALSE) {
    throw new Exception("Curl failed: ".curl_error($http2ch));
}

$status = curl_getinfo($http2ch, CURLINFO_HTTP_CODE);
echo $status;

function base64($data) {
    return rtrim(strtr(base64_encode(json_encode($data)), '+/', '-_'), '=');
}

?>