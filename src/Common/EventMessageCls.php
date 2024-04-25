<?php



namespace Kesk\Web\Common;

class EventMessageCls
{
    public const RN_USER = 'USER';
    public const RN_PROVIDER = 'PROVIDER';
    public const RN_COMPANY = 'COMPANY';
    public const RN_KIOSK = 'KIOSK';
    public const RN_TRACK_USER = 'TRACK_USER';
    private $key_file;
    private $key_id;
    private $team_id;
    private $app_bundle_id;
    private $apns_url;
    private $notification_sound;
    private $custom_notification;
    private $custom_notification_text;

    public function __construct()
    {
        global $tconfig, $APNS_KEY_ID, $APNS_TEAM_ID, $APNS_PRIVATE_KEY_FILE_NAME, $APP_MODE, $APNS_URL_LIVE, $APNS_URL_SANDBOX;
        $this->key_file = preg_replace('/(\/+)/', '/', $tconfig['tpanel_path']).$APNS_PRIVATE_KEY_FILE_NAME;
        $this->key_id = $APNS_KEY_ID;
        $this->team_id = $APNS_TEAM_ID;
        if ('PRODUCTION' === strtoupper($APP_MODE)) {
            $this->apns_url = $APNS_URL_LIVE;
        } else {
            $this->apns_url = $APNS_URL_SANDBOX;
        }
        $this->notification_sound = 'default';
        $this->custom_notification = 'No';
        $this->custom_notification_text = '';
    }

    public function send($generalData, $UserType)
    {
        global $IOS_USER, $IOS_DRIVER, $IOS_STORE, $IOS_KIOSK, $IOS_TRACK_USER, $APP_MODE;
        $alertMsgArr = $AndroidTokensArr = $IosTokensArr = $AndroidMsgArr = $IosMsgArr = $eAppTerminateArr = $addRequestSentArr = $channelNameArr = $publishMsgArr = $tripStatusMsgArr = $eUrlModeArr = $HmsTokensArr = $HmsMsgArr = [];
        $dataArr = $generalData;
        $getSoundData = getCustomeNotificationSound($dataArr);
        if (self::RN_USER === strtoupper($UserType)) {
            $this->app_bundle_id = $IOS_USER;
            $this->notification_sound = $getSoundData['USER_NOTIFICATION'];
        } elseif (self::RN_PROVIDER === strtoupper($UserType)) {
            $this->app_bundle_id = $IOS_DRIVER;
            $this->notification_sound = $getSoundData['PROVIDER_NOTIFICATION'];
        } elseif (self::RN_COMPANY === strtoupper($UserType)) {
            $this->app_bundle_id = $IOS_STORE;
            $this->notification_sound = $getSoundData['STORE_NOTIFICATION'];
        } elseif (self::RN_KIOSK === strtoupper($UserType)) {
            $this->app_bundle_id = $IOS_KIOSK;
        } elseif (self::RN_TRACK_USER === strtoupper($UserType)) {
            $this->app_bundle_id = $IOS_TRACK_USER;
        } else {
            return false;
        }
        foreach ($generalData['GENERAL_DATA'] as $generalData) {
            $isAlertMsgArr = \is_array($generalData['alertMsg']) ? true : false;
            $isMsgArr = \is_array($generalData['message']) ? true : false;
            if (!$isMsgArr && isJsonTextGT($generalData['message'])) {
                $generalData['message'] = json_decode($generalData['message'], true);
            } elseif (!$isMsgArr && !isJsonTextGT($generalData['message'])) {
                $generalData['message'] = [];
                $generalData['message']['vTitle'] = $generalData['alertMsg'];
                $generalData['message']['vSound'] = $this->notification_sound;
                $generalData['message']['vRandomCode'] = time();
            }
            if ('IOS' === strtoupper($generalData['eDeviceType']) && 'Yes' === $generalData['eAppTerminate']) {
                $explodeData = explode('_', $this->notification_sound);
                if (\count($explodeData) > 1) {
                    $this->notification_sound = $explodeData[1];
                }
            }
            if ($isMsgArr) {
                if (!isset($generalData['message']['CustomNotification'])) {
                    $messageJson = $generalData['message'];
                    if (isJsonText($generalData['message'])) {
                        $messageJson = json_decode($generalData['message']);
                    }
                    $this->custom_notification = !empty($messageJson->CustomNotification) ? $messageJson->CustomNotification : 'No';
                } else {
                    $this->custom_notification = $generalData['message']['CustomNotification'];
                }
                if ('YES' === strtoupper($this->custom_notification)) {
                    $this->custom_notification_text = 'CUSTOM_NOTI_ORDER';
                }
                if (!isset($generalData['message']['vTitle'])) {
                    $generalData['message']['vTitle'] = $generalData['alertMsg'];
                }
                if (!isset($generalData['message']['vSound'])) {
                    $generalData['message']['vSound'] = $this->notification_sound;
                }
            }
            if ('YES' === strtoupper($generalData['eHmsDevice'])) {
                $HmsTokensArr[] = $generalData['deviceToken'];
                $alertMsgArr[] = $generalData['alertMsg'];
                $HmsMsgArr[] = $generalData['message'];
            } elseif ('ANDROID' === strtoupper($generalData['eDeviceType'])) {
                $AndroidTokensArr[] = $generalData['deviceToken'];
                $AndroidMsgArr[] = $generalData['message'];
            } else {
                $IosTokensArr[] = $generalData['deviceToken'];
                $alertMsgArr[] = $generalData['alertMsg'];
                $IosMsgArr[] = $generalData['message'];
                $eAppTerminateArr[] = $generalData['eAppTerminate'];
                if (!empty($generalData['eDebugMode']) && 'YES' === strtoupper($generalData['eDebugMode']) && 'PRODUCTION' === strtoupper($APP_MODE)) {
                    $eUrlModeArr[] = 'sandbox';
                } else {
                    $eUrlModeArr[] = 'PRODUCTION' === strtoupper($APP_MODE) ? 'live' : 'sandbox';
                }
            }
            if (isset($generalData['addRequestSentArr'])) {
                $addRequestSentArr[] = $generalData['addRequestSentArr'];
            }
            if (isset($generalData['orderEventChannelName'])) {
                $channelNameArr[] = $generalData['orderEventChannelName'];
                $publishMsgArr[] = $generalData['message'];
            }
            if (isset($generalData['channelName'])) {
                $channelNameArr[] = $generalData['channelName'];
                $publishMsgArr[] = $generalData['message'];
            }
            if (isset($generalData['tripStatusMsgArr'])) {
                $tripStatusMsgArr[] = $generalData['tripStatusMsgArr'];
            }
        }
        if (isset($channelNameArr) && !empty($channelNameArr)) {
            $this->publishEventMessage($channelNameArr, $publishMsgArr);
        }
        if (isset($AndroidTokensArr) && !empty($AndroidTokensArr)) {
            $this->executeRequestAndroid($AndroidTokensArr, $AndroidMsgArr);
        }
        if (isset($IosTokensArr) && !empty($IosTokensArr)) {
            $this->executeRequestIos($IosTokensArr, $IosMsgArr, $alertMsgArr, $eAppTerminateArr, $eUrlModeArr);
        }
        if (isset($HmsTokensArr) && !empty($HmsTokensArr)) {
            $this->executeRequestHuawei($HmsTokensArr, $HmsMsgArr, $alertMsgArr);
        }
        if (isset($addRequestSentArr) && !empty($addRequestSentArr)) {
            $this->addRequestSent($addRequestSentArr);
        }
        if (isset($tripStatusMsgArr) && !empty($tripStatusMsgArr)) {
            $this->addTripStatusMessage($tripStatusMsgArr);
        }

        return true;
    }

    public static function executeRequest($channelNames, $message)
    {
        global $tconfig;
        $url = $tconfig['tsite_sc_php_protocol'].$tconfig['tsite_sc_php_host'].':'.$tconfig['tsite_host_sc_php_port'].$tconfig['tsite_host_sc_php_path'];
        $fields = ['CHANNEL_NAME' => $channelNames, 'DATA_TO_PUBLISH' => $message, 'SERVICE_AUTH_KEY' => 'SERVICE_AUTH_KEY_XXXXXXXX', 'PORT_TO_CONNECT' => $tconfig['tsite_host_sc_port'], 'SOCKET_CLS_HOST' => $tconfig['tsite_sc_host'], 'SOCKET_CLS_PROTOCOL' => $tconfig['tsite_sc_protocol']];
        $fields_string = http_build_query($fields);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);

        return $result;
    }

    private function executeRequestIos($registration_ids, $message, $alertMsg, $eAppTerminate, $eUrlMode): void
    {
        $notifications = [];
        foreach ($registration_ids as $key => $deviceToken) {
            $body = [];
            $body['aps'] = ['alert' => $alertMsg[$key], 'content-available' => 1, 'body' => $message[$key], 'sound' => $this->notification_sound, 'category' => $this->custom_notification_text];
            if ('NO' === strtoupper($eAppTerminate[$key])) {
                $body['aps']['alert'] = '';
                $body['aps']['sound'] = '';
                $body['aps']['apns-push-type'] = 'background';
            }
            $LiveActivity = 'No';
            if (isset($message[$key]['LiveActivity']) && 'Yes' === $message[$key]['LiveActivity']) {
                $LiveActivity = 'Yes';
                $body['aps']['timestamp'] = time();
                if (isset($message[$key]['LiveActivityEnd']) && 'Yes' === $message[$key]['LiveActivityEnd']) {
                    $body['aps']['event'] = 'end';
                    $body['aps']['dismissal-date'] = strtotime('-1 minute', time());
                } else {
                    $body['aps']['event'] = 'update';
                }
                $body['aps']['content-state'] = $message[$key]['LiveActivityData'];
            }
            $notifications[] = ['token' => $deviceToken, 'body' => json_encode($body, JSON_UNESCAPED_UNICODE), 'eUrlMode' => $eUrlMode[$key], 'LiveActivity' => $LiveActivity];
        }
        $this->executeRequestMultiIos($notifications);
    }

    private function executeRequestMultiIos($notifications): void
    {
        global $APNS_URL_LIVE, $APNS_URL_SANDBOX;
        if (!\defined('CURL_HTTP_VERSION_2_0')) {
            \define('CURL_HTTP_VERSION_2_0', 3);
        }
        $requests = [];
        $mh = curl_multi_init();
        foreach ($notifications as $k => $notification) {
            if ('SANDBOX' === strtoupper($notification['eUrlMode'])) {
                $this->apns_url = $APNS_URL_SANDBOX;
            } else {
                $this->apns_url = $APNS_URL_LIVE;
            }
            $url = $this->apns_url.'/3/device/'.$notification['token'];
            $bundleid = $this->app_bundle_id;
            if ('Yes' === $notification['LiveActivity']) {
                $bundleid .= '.push-type.liveactivity';
            }
            $jwt = $this->getAuthorizationToken();
            $requests[$k] = [];
            $requests[$k]['url'] = $url;
            $requests[$k]['body'] = $notification['body'];
            $requests[$k]['curl_handle'] = curl_init($url);
            $http_header_arr = ["apns-topic: {$bundleid}", "authorization: bearer {$jwt}"];
            if ('Yes' === $notification['LiveActivity']) {
                $http_header_arr[] = 'apns-push-type: liveactivity';
            }
            curl_setopt($requests[$k]['curl_handle'], CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_2_0);
            curl_setopt($requests[$k]['curl_handle'], CURLOPT_PORT, 443);
            curl_setopt($requests[$k]['curl_handle'], CURLOPT_HTTPHEADER, $http_header_arr);
            curl_setopt($requests[$k]['curl_handle'], CURLOPT_POST, true);
            curl_setopt($requests[$k]['curl_handle'], CURLOPT_POSTFIELDS, $notification['body']);
            curl_setopt($requests[$k]['curl_handle'], CURLOPT_RETURNTRANSFER, true);
            curl_setopt($requests[$k]['curl_handle'], CURLOPT_HEADER, true);
            curl_multi_add_handle($mh, $requests[$k]['curl_handle']);
        }
        $stillRunning = false;
        do {
            curl_multi_exec($mh, $stillRunning);
        } while ($stillRunning);
        $error = 0;
        $result = [];
        foreach ($requests as $k => $request) {
            $result = curl_multi_getcontent($request['curl_handle']);
            curl_multi_remove_handle($mh, $request['curl_handle']);
            curl_close($requests[$k]['curl_handle']);
            if (isset($_REQUEST['debug_noti'])) {
                echo $result.'<br><br>';
                echo $request['url'].'<br><br>';
                echo $request['body'].'<br><br>';
                echo json_encode($http_header_arr).'<br><br>';
                echo 'Bundle ID: '.$bundleid.'<br>';
                echo 'Key ID: '.$this->key_id.'<br>';
                echo 'Team ID: '.$this->team_id.'<br>';
                echo 'Key File: '.$this->key_file.'<br><br>';
            }
        }
        curl_multi_close($mh);
        if (isset($_REQUEST['debug_noti'])) {
            exit;
        }
    }

    private function getAuthorizationToken()
    {
        $key = openssl_pkey_get_private('file://'.$this->key_file);
        $header = ['alg' => 'ES256', 'kid' => $this->key_id];
        $claims = ['iss' => $this->team_id, 'iat' => time()];
        $header_encoded = $this->base64($header);
        $claims_encoded = $this->base64($claims);
        $signature = '';
        openssl_sign($header_encoded.'.'.$claims_encoded, $signature, $key, 'sha256');

        return $header_encoded.'.'.$claims_encoded.'.'.base64_encode($signature);
    }

    private function base64($data)
    {
        return rtrim(strtr(base64_encode(json_encode($data)), '+/', '-_'), '=');
    }

    private function executeRequestAndroid($registration_ids, $message)
    {
        global $obj, $FIREBASE_API_ACCESS_KEY, $PUBSUB_TECHNIQUE, $CONFIG_OBJ;
        if (empty($FIREBASE_API_ACCESS_KEY)) {
            $FIREBASE_API_ACCESS_KEY = $CONFIG_OBJ->getConfigurations('configurations', 'FIREBASE_API_ACCESS_KEY');
        }
        if (empty($PUBSUB_TECHNIQUE)) {
            $PUBSUB_TECHNIQUE = $CONFIG_OBJ->getConfigurations('configurations', 'PUBSUB_TECHNIQUE');
        }
        if (\count($registration_ids) > 999) {
            $newArr = array_chunk($registration_ids, 999);
            $newArrMsg = array_chunk($message, 999);
            foreach ($newArr as $k => $newRegistration_ids) {
                $result = $this->executeRequestAndroid($newRegistration_ids, $newArrMsg[$k]);
            }
        }
        if (\count($registration_ids) > 1 && \count($message) > 1 && !isAssocArrGT($message)) {
            $this->executeRequestMultiAndroid($registration_ids, $message);
        } else {
            $message = isJsonText($message) ? json_decode($message, true) : (!isAssocArrGT($message) ? $message[0] : $message);
            $fields = ['registration_ids' => $registration_ids, 'click_action' => '.MainActivity', 'priority' => 'high', 'data' => ['message' => $message]];
            $finalFields = json_encode($fields, JSON_UNESCAPED_UNICODE);
            $headers = ['Authorization: key='.$FIREBASE_API_ACCESS_KEY, 'Content-Type: application/json'];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $finalFields);
            $response = curl_exec($ch);
            if (false === $response) {
            }
            $responseArr = json_decode($response);
            $success = $responseArr->success;
            if (isset($_REQUEST['debug_noti'])) {
                echo '
                        <pre>';
                print_r($responseArr);
                print_r($message);

                exit;
            }
            curl_close($ch);

            return true;
        }
    }

    private function executeRequestMultiAndroid($registration_ids, $message)
    {
        global $obj, $FIREBASE_API_ACCESS_KEY, $PUBSUB_TECHNIQUE, $CONFIG_OBJ;
        if (empty($FIREBASE_API_ACCESS_KEY)) {
            $FIREBASE_API_ACCESS_KEY = $CONFIG_OBJ->getConfigurations('configurations', 'FIREBASE_API_ACCESS_KEY');
        }
        if (empty($PUBSUB_TECHNIQUE)) {
            $PUBSUB_TECHNIQUE = $CONFIG_OBJ->getConfigurations('configurations', 'PUBSUB_TECHNIQUE');
        }
        $requests = [];
        $mh = curl_multi_init();
        $headers = ['Authorization: key='.$FIREBASE_API_ACCESS_KEY, 'Content-Type: application/json'];
        foreach ($registration_ids as $key => $value) {
            $fields = ['registration_ids' => [$value], 'click_action' => '.MainActivity', 'priority' => 'high', 'data' => ['message' => $message[$key]]];
            $finalFields = json_encode($fields, JSON_UNESCAPED_UNICODE);
            $requests[$key] = [];
            $requests[$key]['curl_handle'] = curl_init('https://fcm.googleapis.com/fcm/send');
            curl_setopt($requests[$key]['curl_handle'], CURLOPT_HTTPHEADER, $headers);
            curl_setopt($requests[$key]['curl_handle'], CURLOPT_POST, true);
            curl_setopt($requests[$key]['curl_handle'], CURLOPT_POSTFIELDS, $finalFields);
            curl_setopt($requests[$key]['curl_handle'], CURLOPT_RETURNTRANSFER, true);
            curl_setopt($requests[$key]['curl_handle'], CURLOPT_HEADER, true);
            curl_setopt($requests[$key]['curl_handle'], CURLOPT_SSL_VERIFYPEER, false);
            curl_multi_add_handle($mh, $requests[$key]['curl_handle']);
        }
        $stillRunning = false;
        do {
            curl_multi_exec($mh, $stillRunning);
        } while ($stillRunning);
        $error = 0;
        $result = [];
        foreach ($requests as $k => $request) {
            $result = curl_multi_getcontent($request['curl_handle']);
            curl_multi_remove_handle($mh, $request['curl_handle']);
            curl_close($requests[$k]['curl_handle']);
            if (isset($_REQUEST['debug_noti'])) {
                echo '<pre>';
                print_r($result);
            }
        }
        curl_multi_close($mh);
        if (isset($_REQUEST['debug_noti'])) {
            exit;
        }

        return true;
    }

    private function getAccessTokenHuawei()
    {
        global $HMS_CLIENT_ID, $HMS_SECRET_KEY;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://oauth-login.cloud.huawei.com/oauth2/v3/token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type' => 'application/x-www-form-urlencoded']);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'grant_type=client_credentials&client_id='.$HMS_CLIENT_ID.'&client_secret='.$HMS_SECRET_KEY);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $response = curl_exec($ch);
        curl_close($ch);
        $response = json_decode($response, true);

        return $response['access_token'];
    }

    private function executeRequestHuawei($registration_ids, $message, $alertMsg)
    {
        global $HMS_PROJECT_ID;
        if (\count($registration_ids) > 999) {
            $newArr = array_chunk($registration_ids, 999);
            $newArrMsg = array_chunk($message, 999);
            foreach ($newArr as $k => $newRegistration_ids) {
                $result = $this->executeRequestHuawei($newRegistration_ids, $newArrMsg[$k], $alertMsg);
            }
        }
        if (\count($registration_ids) > 1 && \count($message) > 1 && !isAssocArrGT($message)) {
            $this->executeRequestMultiHuawei($registration_ids, $message, $alertMsg);
        } else {
            $message = isJsonText($message) ? json_decode($message, true) : (!isAssocArrGT($message) ? $message[0] : $message);
            $post_data = ['validate_only' => false, 'message' => ['android' => ['urgency' => 'NORMAL', 'ttl' => '10000s'], 'data' => json_encode($message), 'token' => $registration_ids]];
            $finalFields = json_encode($post_data);
            $headers = ['Authorization: Bearer '.$this->getAccessTokenHuawei(), 'Content-Type: application/json'];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://push-api.cloud.huawei.com/v2/'.$HMS_PROJECT_ID.'/messages:send');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $finalFields);
            $response = curl_exec($ch);
            if (false === $response) {
            }
            $responseArr = json_decode($response);
            $success = $responseArr->success;
            if (isset($_REQUEST['debug_noti'])) {
                echo '<pre>';
                print_r($responseArr);
                print_r($registration_ids);
                print_r($message);

                exit;
            }
            curl_close($ch);

            return true;
        }
    }

    private function executeRequestMultiHuawei($registration_ids, $message, $alertMsg)
    {
        global $HMS_PROJECT_ID;
        $requests = [];
        $mh = curl_multi_init();
        $headers = ['Authorization: Bearer '.$this->getAccessTokenHuawei(), 'Content-Type: application/json'];
        foreach ($registration_ids as $key => $value) {
            $post_data = ['validate_only' => false, 'message' => ['notification' => ['title' => $alertMsg[$key], 'body' => $message[$key], 'click_action' => ['type' => 3]], 'android' => ['urgency' => 'NORMAL', 'ttl' => '10000s', 'notification' => ['title' => $alertMsg[$key], 'body' => $message[$key], 'click_action' => ['type' => 3]]], 'data' => $message[$key], 'foreground_show' => false, 'token' => [$value]]];
            $finalFields = json_encode($post_data, JSON_UNESCAPED_UNICODE);
            $requests[$key] = [];
            $requests[$key]['curl_handle'] = curl_init('https://push-api.cloud.huawei.com/v2/'.$HMS_PROJECT_ID.'/messages:send');
            curl_setopt($requests[$key]['curl_handle'], CURLOPT_HTTPHEADER, $headers);
            curl_setopt($requests[$key]['curl_handle'], CURLOPT_POST, true);
            curl_setopt($requests[$key]['curl_handle'], CURLOPT_POSTFIELDS, $finalFields);
            curl_setopt($requests[$key]['curl_handle'], CURLOPT_RETURNTRANSFER, true);
            curl_setopt($requests[$key]['curl_handle'], CURLOPT_HEADER, true);
            curl_setopt($requests[$key]['curl_handle'], CURLOPT_SSL_VERIFYPEER, false);
            curl_multi_add_handle($mh, $requests[$key]['curl_handle']);
        }
        $stillRunning = false;
        do {
            curl_multi_exec($mh, $stillRunning);
        } while ($stillRunning);
        $error = 0;
        $result = [];
        foreach ($requests as $k => $request) {
            $result = curl_multi_getcontent($request['curl_handle']);
            curl_multi_remove_handle($mh, $request['curl_handle']);
            curl_close($requests[$k]['curl_handle']);
            if (isset($_REQUEST['debug_noti'])) {
                echo '<pre>';
                print_r($result);
            }
        }
        curl_multi_close($mh);
        if (isset($_REQUEST['debug_noti'])) {
            exit;
        }

        return true;
    }

    private function publishEventMessage($channelNameArr, $messageArr)
    {
        global $PUBSUB_TECHNIQUE, $PUBNUB_PUBLISH_KEY, $PUBNUB_SUBSCRIBE_KEY, $uuid;
        foreach ($channelNameArr as $key => $channelName) {
            $message = json_encode($messageArr[$key], JSON_UNESCAPED_UNICODE);
            if ('SocketCluster' === $PUBSUB_TECHNIQUE) {
                $this->executeRequest($channelName, $message);
            } elseif ('PubNub' === $PUBSUB_TECHNIQUE) {
                global $pubNubClsObj;
                if (empty($pubNubClsObj)) {
                    $pubnub = new Pubnub\Pubnub(['publish_key' => $PUBNUB_PUBLISH_KEY, 'subscribe_key' => $PUBNUB_SUBSCRIBE_KEY, 'uuid' => $uuid]);
                    $pubNubClsObj = $pubnub;
                } else {
                    $pubnub = $pubNubClsObj;
                }
                $info = $pubnub->publish($channelName, $message);
            }
        }

        return true;
    }

    private function addRequestSent($addRequestSentArr)
    {
        global $obj;
        foreach ($addRequestSentArr as $addRequestSent) {
            $data_userRequest = $data_driverRequest = [];
            $data_userRequest['iUserId'] = $addRequestSent['iUserId'];
            $data_userRequest['iDriverId'] = $addRequestSent['iDriverId'];
            $data_userRequest['tMessage'] = json_encode($addRequestSent['tMessage'], JSON_UNESCAPED_UNICODE);
            $data_userRequest['iMsgCode'] = $addRequestSent['iMsgCode'];
            $data_userRequest['dAddedDate'] = @date('Y-m-d H:i:s');
            $dataId = $obj->MySQLQueryPerform('passenger_requests', $data_userRequest, 'insert');
            $data_driverRequest['iDriverId'] = $addRequestSent['iDriverId'];
            $data_driverRequest['iRequestId'] = $dataId;
            $data_driverRequest['iUserId'] = $addRequestSent['iUserId'];
            $data_driverRequest['iTripId'] = 0;
            $data_driverRequest['eStatus'] = 'Timeout';
            $data_driverRequest['vMsgCode'] = $addRequestSent['iMsgCode'];
            $data_driverRequest['vStartLatlong'] = $addRequestSent['vStartLatlong'];
            $data_driverRequest['vEndLatlong'] = $addRequestSent['vEndLatlong'];
            $data_driverRequest['tStartAddress'] = $addRequestSent['tStartAddress'];
            $data_driverRequest['tEndAddress'] = $addRequestSent['tEndAddress'];
            $data_driverRequest['tDate'] = @date('Y-m-d H:i:s');
            $data_driverRequest['dAddedDate'] = @date('Y-m-d H:i:s');
            if (isset($addRequestSent['iOrderId'])) {
                $data_driverRequest['iOrderId'] = $addRequestSent['iOrderId'];
            }
            $obj->MySQLQueryPerform('driver_request', $data_driverRequest, 'insert');
        }

        return true;
    }

    private function addTripStatusMessage($tripStatusMsgArr)
    {
        global $obj;
        foreach ($tripStatusMsgArr as $tripStatusMsg) {
            $DataTripMessages = [];
            $DataTripMessages['tMessage'] = json_encode($tripStatusMsg['tMessage'], JSON_UNESCAPED_UNICODE);
            $DataTripMessages['iDriverId'] = $tripStatusMsg['iDriverId'];
            $DataTripMessages['iTripId'] = $tripStatusMsg['iTripId'];
            $DataTripMessages['iUserId'] = $tripStatusMsg['iUserId'];
            $DataTripMessages['eFromUserType'] = $tripStatusMsg['eFromUserType'];
            $DataTripMessages['eToUserType'] = $tripStatusMsg['eToUserType'];
            $DataTripMessages['eReceived'] = $tripStatusMsg['eReceived'];
            $DataTripMessages['dAddedDate'] = @date('Y-m-d H:i:s');
            if (isset($tripStatusMsg['iOrderId'])) {
                $DataTripMessages['iOrderId'] = $tripStatusMsg['iOrderId'];
            }
            $obj->MySQLQueryPerform('trip_status_messages', $DataTripMessages, 'insert');
        }

        return true;
    }
}
