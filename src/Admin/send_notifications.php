<?php
if (isset($_POST['SESSION_DATA'])) {
    session_start();
    $_SESSION = json_decode($_POST['SESSION_DATA'], true);
}

include_once '../common.php';

$APP_MODE = $CONFIG_OBJ->getConfigurations('configurations', 'APP_MODE');
$APP_MODE_TEMP_WEB = '';

global $customNotification;

if (!$userObj->hasPermission('manage-send-push-notification')) {
    $userObj->redirect();
}

$vCountry = $_POST['vCountry'] ?? '';
$whereCountry = $whereCountryCom = '';
if ('' !== $vCountry) {
    $whereCountry = "AND vCountry='".$vCountry."'";
    $whereCountryCom = "AND c.vCountry='".$vCountry."'";
}
// added by SP for comment eStatus active bc for all driver notification send but in log table not inserted so remove condition so inserted on 3-10-2019 as per the discussion with the HJ
// $sql = "select concat(vName,' ',vLastName) as DriverName,iDriverId,eDeviceType,eDebugMode from register_driver where eStatus = 'Active' AND (vEmail != '' OR vPhone != '')  order by vName";

$delsql = " AND eStatus != 'Deleted'";

// when same devicetoken then take only one who is loggedin..
$sql = "select iDriverId,vFirebaseDeviceToken,eLogout from register_driver where (vEmail != '' OR vPhone != '') {$delsql} order by eLogout DESC";
$db_drvlist_temp = $obj->MySQLSelect($sql);
$db_drvlist_temp = array_column($db_drvlist_temp, 'vFirebaseDeviceToken', 'iDriverId');
$db_drvlist_temp1 = array_unique($db_drvlist_temp);
$uniquedriver = implode(', ', array_keys($db_drvlist_temp1));

$sql = "select iUserId,vFirebaseDeviceToken,eLogout from register_user where (vEmail != '' OR vName != '' OR vPhone != '') {$delsql} order by eLogout DESC";
$db_rdrlist_temp = $obj->MySQLSelect($sql);
$db_rdrlist_temp = array_column($db_rdrlist_temp, 'vFirebaseDeviceToken', 'iUserId');
$db_rdrlist_temp1 = array_unique($db_rdrlist_temp);
$uniqueuser = implode(', ', array_keys($db_rdrlist_temp1));

if (DELIVERALL === 'Yes') {
    $sql = "SELECT c.iCompanyId, c.vFirebaseDeviceToken,c.eLogout FROM company AS c left join service_categories as sc on c.iServiceId = sc.iServiceId WHERE sc.eStatus='Active' AND c.iServiceId>0 order by c.eLogout DESC";
    $db_storelist_temp = $obj->MySQLSelect($sql);
    $db_storelist_temp = array_column($db_storelist_temp, 'vFirebaseDeviceToken', 'iCompanyId');
    $db_storelist_temp1 = array_unique($db_storelist_temp);
    $uniquestore = implode(', ', array_keys($db_storelist_temp1));
}

$sql = "select concat(vName,' ',vLastName) as DriverName,iDriverId,eDeviceType,eDebugMode from register_driver where (vEmail != '' OR vPhone != '') {$whereCountry} {$delsql} AND iDriverId IN({$uniquedriver}) order by vName";
$db_drvlist = $obj->MySQLSelect($sql);
$db_drv_list = [];

for ($i = 0; $i < count($db_drvlist); ++$i) {
    $data = [];
    $data['DriverName'] = mb_convert_encoding(clearName(ucfirst($db_drvlist[$i]['DriverName'])), 'utf-8', 'auto');
    $data['iDriverId'] = $db_drvlist[$i]['iDriverId'];
    $data['eDeviceType'] = $db_drvlist[$i]['eDeviceType'];
    $data['eDebugMode'] = $db_drvlist[$i]['eDebugMode'];
    $db_drv_list[] = $data;
}
// $sql = "select concat(vName,' ',vLastName) as riderName,iUserId,eDeviceType from register_user where eStatus = 'Active' AND (vEmail != '' OR vName != '' OR vPhone != '') order by vName";

$sql = "select concat(vName,' ',vLastName) as riderName,iUserId,eDeviceType,vFirebaseDeviceToken from register_user where (vEmail != '' OR vName != '' OR vPhone != '') {$whereCountry} {$delsql} AND iUserId IN({$uniqueuser}) order by vName";
$db_rdrlist = $obj->MySQLSelect($sql);
$db_rdr_list = [];
for ($ii = 0; $ii < count($db_rdrlist); ++$ii) {
    $data = [];

    $data['riderName'] = mb_convert_encoding(clearName(ucfirst($db_rdrlist[$ii]['riderName'])), 'utf-8', 'auto');

    $data['iUserId'] = $db_rdrlist[$ii]['iUserId'];
    $data['eDeviceType'] = $db_rdrlist[$ii]['eDeviceType'];
    $db_rdr_list[] = $data;
}
// echo "<PRE>";print_R($db_rdr_list); exit;
// $sql_drv = "select concat(vName,' ',vLastName) as DriverName,iDriverId,eDeviceType from register_driver where eStatus = 'Active' AND `eLogout` = 'No' AND (vEmail != '' OR vPhone != '') order by vName";
$sql_drv = "select concat(vName,' ',vLastName) as DriverName,iDriverId,eDeviceType from register_driver where `eLogout` = 'No' AND (vEmail != '' OR vPhone != '') {$whereCountry} {$delsql} AND iDriverId IN({$uniquedriver}) order by vName";
$db_login_drvlist = $obj->MySQLSelect($sql_drv);
$db_login_drv_list = [];
for ($iii = 0; $iii < count($db_login_drvlist); ++$iii) {
    $data = [];
    $data['DriverName'] = mb_convert_encoding(clearName(ucfirst($db_login_drvlist[$iii]['DriverName'])), 'utf-8', 'auto');
    $data['iDriverId'] = $db_login_drvlist[$iii]['iDriverId'];
    $data['eDeviceType'] = $db_login_drvlist[$iii]['eDeviceType'];
    $db_login_drv_list[] = $data;
}

// $sql_rdr = "select concat(vName,' ',vLastName) as riderName,iUserId,eDeviceType from register_user where eStatus = 'Active' AND `eLogout` = 'No'  AND (vEmail != '' OR vPhone != '') order by vName";
$sql_rdr = "select concat(vName,' ',vLastName) as riderName,iUserId,eDeviceType from register_user where `eLogout` = 'No'  AND (vEmail != '' OR vPhone != '') {$whereCountry} {$delsql} AND iUserId IN({$uniqueuser}) order by vName";
$db_login_rdrlist = $obj->MySQLSelect($sql_rdr);
$db_login_rdr_list = [];
for ($iv = 0; $iv < count($db_login_rdrlist); ++$iv) {
    $data = [];
    $data['riderName'] = mb_convert_encoding(clearName(ucfirst($db_login_rdrlist[$iv]['riderName'])), 'utf-8', 'auto');
    $data['iUserId'] = $db_login_rdrlist[$iv]['iUserId'];
    $data['eDeviceType'] = $db_login_rdrlist[$iv]['eDeviceType'];
    $db_login_rdr_list[] = $data;
}
$sql_inactive_drv = "select concat(vName,' ',vLastName) as DriverName,iDriverId,eDeviceType from register_driver where eStatus = 'Inactive' AND (vEmail != '' OR vPhone != '') {$whereCountry} AND iDriverId IN({$uniquedriver}) order by vName";
$db_inactive_drvlist = $obj->MySQLSelect($sql_inactive_drv);

$db_inactive_drv_list = [];
for ($v = 0; $v < count($db_inactive_drvlist); ++$v) {
    $data = [];
    $data['DriverName'] = mb_convert_encoding(clearName(ucfirst($db_inactive_drvlist[$v]['DriverName'])), 'utf-8', 'auto');
    $data['iDriverId'] = $db_inactive_drvlist[$v]['iDriverId'];
    $data['eDeviceType'] = $db_inactive_drvlist[$v]['eDeviceType'];
    $db_inactive_drv_list[] = $data;
}

$sql_inactive_rdr = "select concat(vName,' ',vLastName) as riderName,iUserId,eDeviceType from register_user where eStatus = 'Inactive' AND (vEmail != '' OR vPhone != '') {$whereCountry} AND iUserId IN({$uniqueuser}) order by vName";
$db_inactive_rdrlist = $obj->MySQLSelect($sql_inactive_rdr);
$db_inactive_rdr_list = [];
for ($vi = 0; $vi < count($db_inactive_rdrlist); ++$vi) {
    $data = [];
    $data['riderName'] = mb_convert_encoding(clearName(ucfirst($db_inactive_rdrlist[$vi]['riderName'])), 'utf-8', 'auto');
    $data['iUserId'] = $db_inactive_rdrlist[$vi]['iUserId'];
    $data['eDeviceType'] = $db_inactive_rdrlist[$vi]['eDeviceType'];
    $db_inactive_rdr_list[] = $data;
}
if (DELIVERALL === 'Yes') {
    $sql = "SELECT c.iCompanyId, c.vCompany,c.eDeviceType FROM company AS c  left join service_categories as sc on c.iServiceId = sc.iServiceId WHERE c.eStatus = 'Active' AND sc.eStatus='Active' AND c.iServiceId>0 {$whereCountryCom} AND c.iCompanyId IN({$uniquestore}) order by c.vCompany";
    $db_storelist = $obj->MySQLSelect($sql);
    $db_store_list = [];
    for ($vii = 0; $vii < count($db_storelist); ++$vii) {
        $data = [];
        $data['vCompany'] = mb_convert_encoding(clearCmpName(ucfirst($db_storelist[$vii]['vCompany'])), 'utf-8', 'auto');
        $data['iCompanyId'] = $db_storelist[$vii]['iCompanyId'];
        $data['eDeviceType'] = $db_storelist[$vii]['eDeviceType'];
        $db_store_list[] = $data;
    }

    $sql = "SELECT c.iCompanyId, c.vCompany,c.eDeviceType FROM company AS c  left join service_categories as sc on c.iServiceId = sc.iServiceId WHERE c.eStatus = 'Active' AND sc.eStatus='Active' AND c.eLogout = 'No'AND  c.iServiceId>0 {$whereCountryCom} AND c.iCompanyId IN({$uniquestore}) order by c.vCompany";
    $db_login_rstlist = $obj->MySQLSelect($sql);
    $db_login_rst_list = [];
    for ($ix = 0; $ix < count($db_login_rstlist); ++$ix) {
        $data = [];
        $data['vCompany'] = mb_convert_encoding(clearCmpName(ucfirst($db_login_rstlist[$ix]['vCompany'])), 'utf-8', 'auto');
        $data['iCompanyId'] = $db_login_rstlist[$ix]['iCompanyId'];
        $data['eDeviceType'] = $db_login_rstlist[$ix]['eDeviceType'];
        $db_login_rst_list[] = $data;
    }

    $sql = "SELECT c.iCompanyId, c.vCompany,c.eDeviceType FROM company AS c  left join service_categories as sc on c.iServiceId = sc.iServiceId WHERE  c.eStatus = 'Inactive' AND sc.eStatus='Active' AND c.eStatus = 'Inactive' AND  c.iServiceId>0 {$whereCountryCom} AND c.iCompanyId IN({$uniquestore}) order by c.vCompany";
    $db_inactive_rstlist = $obj->MySQLSelect($sql);
    $db_inactive_rst_list = [];
    for ($x = 0; $x < count($db_inactive_rstlist); ++$x) {
        $data = [];
        $data['vCompany'] = mb_convert_encoding(clearCmpName(ucfirst($db_inactive_rstlist[$x]['vCompany'])), 'utf-8', 'auto');
        $data['iCompanyId'] = $db_inactive_rstlist[$x]['iCompanyId'];
        $data['eDeviceType'] = $db_inactive_rstlist[$x]['eDeviceType'];
        $db_inactive_rst_list[] = $data;
    }
}

$tbl_name = 'pushnotification_log';
$script = 'Push Notification';
// set all variables with either post (when submit) either blank (when insert)
$eUserType = $_REQUEST['eUserType'] ?? '';
$iDriverId = $_REQUEST['iDriverId'] ?? '';
$iRiderId = $_REQUEST['iRiderId'] ?? '';

$eDeviceType = $_REQUEST['eDeviceType'] ?? '';
$iCompanyId = $_REQUEST['iCompanyId'] ?? '';
$iLoginCompanyId = $_REQUEST['iLoginCompanyId'] ?? '';
$iInactiveCompanyId = $_REQUEST['iInactiveCompanyId'] ?? '';

$iLoginDriverId = $_REQUEST['iLoginDriverId'] ?? '';
$iLoginRiderId = $_REQUEST['iLoginRiderId'] ?? '';

$iInactiveDriverId = $_REQUEST['iInactiveDriverId'] ?? '';
$iInactiveRiderId = $_REQUEST['iInactiveRiderId'] ?? '';

$tMessage = $_REQUEST['tMessage'] ?? '';
$dDate = date('Y-m-d H:i:s');
// $ipAddress = $_SERVER['REMOTE_HOST'];
$ipAddress = get_client_ip();
if (isset($_REQUEST['submit'])) {
    // ini_set('display_errors', 1);
    // error_reporting(E_ALL);
    // for news feed table entry
    if (isset($tMessage) && '' !== $tMessage) {
        $tPublishdate = date('Y-m-d H:i:s');
        // Commented By HJ On 26-12-2018 As Per Discuss With CD Start
        /* $queryNews = "INSERT INTO `newsfeed` SET
          `vTitle` = '',
          `tDescription` = '" . $tMessage . "',
          `tPublishdate` = '".$tPublishdate."', eStatus = 'Active', eType = 'Notification'";

          $obj->sql_query($queryNews); */
        // Commented By HJ On 26-12-2018 As Per Discuss With CD End
    }
    if (SITE_TYPE === 'Demo') {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'Sending push notification has been disabled on the Demo Admin Panel. This feature will be enabled on the main script we will provide you.';
        header('Location:send_notifications.php');

        exit;
    }

    $userArr = [];

    if ('driver' === $eUserType) {
        $set_table = 'register_driver';
        $set_userId = 'iDriverId';
        if ('' !== $iDriverId) {
            // $userArr = explode(",", $iDriverId);
            $userArrTemp = explode(',', $iDriverId);
            foreach ($userArrTemp as $kk => $vv) {
                $match = array_search($vv, array_column($db_drv_list, 'iDriverId'), true);
                if (empty($match) && 0 !== $match) {
                    continue;
                }
                $userArr[] = $vv;
            }
        } else {
            foreach ($db_drv_list as $dbd) {
                $userArr[] = $dbd['iDriverId'];
            }
        }
    } elseif ('rider' === $eUserType) {
        $set_table = 'register_user';
        $set_userId = 'iUserId';
        if ('' !== $iRiderId) {
            $userArrTemp = explode(',', $iRiderId);
            foreach ($userArrTemp as $kk => $vv) {
                $match = array_search($vv, array_column($db_rdr_list, 'iUserId'), true);
                if (empty($match) && 0 !== $match) {
                    continue;
                }
                $userArr[] = $vv;
            }
        } else {
            foreach ($db_rdr_list as $dbr) {
                $userArr[] = $dbr['iUserId'];
            }
        }
    } elseif ('logged_driver' === $eUserType) {
        $eUserType = 'driver';
        $set_table = 'register_driver';
        $set_userId = 'iDriverId';
        if ('' !== $iLoginDriverId) {
            // $userArr = explode(",", $iLoginDriverId);
            $userArrTemp = explode(',', $iLoginDriverId);
            foreach ($userArrTemp as $kk => $vv) {
                $match = array_search($vv, array_column($db_login_drv_list, 'iDriverId'), true);
                if (empty($match) && 0 !== $match) {
                    continue;
                }
                $userArr[] = $vv;
            }
        } else {
            foreach ($db_login_drv_list as $dbd) {
                $userArr[] = $dbd['iDriverId'];
            }
        }
    } elseif ('logged_rider' === $eUserType) {
        $eUserType = 'rider';
        $set_table = 'register_user';
        $set_userId = 'iUserId';
        if ('' !== $iLoginRiderId) {
            // $userArr = explode(",", $iLoginRiderId);
            $userArrTemp = explode(',', $iLoginRiderId);
            foreach ($userArrTemp as $kk => $vv) {
                $match = array_search($vv, array_column($db_login_rdr_list, 'iUserId'), true);
                if (empty($match) && 0 !== $match) {
                    continue;
                }
                $userArr[] = $vv;
            }
        } else {
            foreach ($db_login_rdr_list as $dbr) {
                $userArr[] = $dbr['iUserId'];
            }
        }
    } elseif ('inactive_driver' === $eUserType) {
        $eUserType = 'driver';
        $set_table = 'register_driver';
        $set_userId = 'iDriverId';
        if ('' !== $iInactiveDriverId) {
            // $userArr = explode(",", $iInactiveDriverId);
            $userArrTemp = explode(',', $iInactiveDriverId);
            foreach ($userArrTemp as $kk => $vv) {
                $match = array_search($vv, array_column($db_inactive_drv_list, 'iDriverId'), true);
                if (empty($match) && 0 !== $match) {
                    continue;
                }
                $userArr[] = $vv;
            }
        } else {
            foreach ($db_inactive_drv_list as $dbd) {
                $userArr[] = $dbd['iDriverId'];
            }
        }
    } elseif ('inactive_rider' === $eUserType) {
        $eUserType = 'rider';
        $set_table = 'register_user';
        $set_userId = 'iUserId';
        if ('' !== $iInactiveRiderId) {
            // $userArr = explode(",", $iInactiveRiderId);
            $userArrTemp = explode(',', $iInactiveRiderId);
            foreach ($userArrTemp as $kk => $vv) {
                $match = array_search($vv, array_column($db_inactive_rdr_list, 'iUserId'), true);
                if (empty($match) && 0 !== $match) {
                    continue;
                }
                $userArr[] = $vv;
            }
        } else {
            foreach ($db_inactive_rdr_list as $dbr) {
                $userArr[] = $dbr['iUserId'];
            }
        }
    } elseif ('store' === $eUserType) {
        $eUserType = 'company';
        $set_table = 'company';
        $set_userId = 'iCompanyId';
        if ('' !== $iCompanyId) {
            // $userArr = explode(",", $iCompanyId);
            $userArrTemp = explode(',', $iCompanyId);
            foreach ($userArrTemp as $kk => $vv) {
                $match = array_search($vv, array_column($db_store_list, 'iCompanyId'), true);
                if (empty($match) && 0 !== $match) {
                    continue;
                }
                $userArr[] = $vv;
            }
        } else {
            foreach ($db_store_list as $dbr) {
                $userArr[] = $dbr['iCompanyId'];
            }
        }
    } elseif ('logged_store' === $eUserType) {
        $eUserType = 'company';
        $set_table = 'company';
        $set_userId = 'iCompanyId';
        if ('' !== $iLoginCompanyId) {
            // $userArr = explode(",", $iLoginCompanyId);
            $userArrTemp = explode(',', $iLoginCompanyId);
            foreach ($userArrTemp as $kk => $vv) {
                $match = array_search($vv, array_column($db_login_rst_list, 'iCompanyId'), true);
                if (empty($match) && 0 !== $match) {
                    continue;
                }
                $userArr[] = $vv;
            }
        } else {
            foreach ($db_login_rst_list as $dbd) {
                $userArr[] = $dbd['iCompanyId'];
            }
        }
    } elseif ('inactive_store' === $eUserType) {
        $eUserType = 'company';
        $set_table = 'company';
        $set_userId = 'iCompanyId';
        if ('' !== $iInactiveCompanyId) {
            // $userArr = explode(",", $iInactiveCompanyId);
            $userArrTemp = explode(',', $iInactiveCompanyId);
            foreach ($userArrTemp as $kk => $vv) {
                $match = array_search($vv, array_column($db_inactive_rst_list, 'iCompanyId'), true);
                if (empty($match) && 0 !== $match) {
                    continue;
                }
                $userArr[] = $vv;
            }
        } else {
            foreach ($db_inactive_rst_list as $dbd) {
                $userArr[] = $dbd['iCompanyId'];
            }
        }
    }

    $getUserData = $obj->MySQLSelect("SELECT eDeviceType,iGcmRegId,eDebugMode,{$set_userId},tSessionId,eAppTerminate,eDebugMode,eHmsDevice FROM ".$set_table);
    $notificationDataArr = [];
    for ($f = 0; $f < count($getUserData); ++$f) {
        $notificationDataArr[$getUserData[$f][$set_userId]] = $getUserData[$f];
    }

    $tMessage = preg_replace('/\\\\{2,}/', '\\', $tMessage);
    $tMessage = str_replace(['\r', '\n'], [chr(13), chr(10)], trim(getProperDataValueWithoutClean($tMessage)));

    $deviceTokens_arr_ios = $deviceTokens_arr_ios_pro = $registation_ids_new = $db_insert_arr = $eAppTerminateArr = $generalDataArr = [];

    foreach ($userArr as $usAr) {
        $db_insert_arr_tmp = [];
        $db_insert_arr_tmp['eUserType'] = $eUserType;
        $db_insert_arr_tmp['iUserId'] = $usAr;
        $db_insert_arr_tmp['tMessage'] = $obj->SqlEscapeString($tMessage);
        $db_insert_arr_tmp['dDateTime'] = $dDate;
        $db_insert_arr_tmp['IP_ADDRESS'] = $ipAddress;

        $eDeviceType = $iGcmRegId = $eDebugMode = '';
        if (isset($notificationDataArr[$usAr])) {
            $eDeviceType = $notificationDataArr[$usAr]['eDeviceType'];
            $iGcmRegId = $notificationDataArr[$usAr]['iGcmRegId'];
            $eDebugMode = $notificationDataArr[$usAr]['eDebugMode'];
            $eAppTerminate = $notificationDataArr[$usAr]['eAppTerminate'];
            $eDebugMode = $notificationDataArr[$usAr]['eDebugMode'];
            $eHmsDevice = $notificationDataArr[$usAr]['eHmsDevice'];
        }
        if (empty($iGcmRegId)) {
            continue;
        }

        $db_insert_arr[] = $db_insert_arr_tmp;
        $generalDataArr[] = [
            'eDeviceType' => $eDeviceType,
            'deviceToken' => $iGcmRegId,
            'alertMsg' => $tMessage,
            'eAppTerminate' => $eAppTerminate,
            'eDebugMode' => $eDebugMode,
            'eHmsDevice' => $eHmsDevice,
            'message' => $tMessage,
        ];
    }

    $db_insert_arr_final = array_chunk($db_insert_arr, 250);
    $generalDataArrNew = array_chunk($generalDataArr, 250);

    $page = $_REQUEST['page'] ?? 0;

    if ($page < count($db_insert_arr_final) && isset($_REQUEST['SEND_NOTI']) && 'Yes' === $_REQUEST['SEND_NOTI']) {
        $db_insert_arr_final_item_arr = $db_insert_arr_final[$page];

        $ins_query = 'INSERT INTO `'.$tbl_name.'` (`'.implode('`,`', array_keys($db_insert_arr_final_item_arr[0])).'`) VALUES ';
        $isFirstItem = true;
        foreach ($db_insert_arr_final_item_arr as $db_insert_arr_final_item_arr_item) {
            $data = ' ('.implode(', ', array_map(
                static fn ($v, $k) => sprintf("'%s'", $v),
                $db_insert_arr_final_item_arr_item,
                array_keys($db_insert_arr_final_item_arr_item)
            )).')';
            $ins_query .= false === $isFirstItem ? ', '.$data : $data;
            $isFirstItem = false;
        }
        $obj->sql_query($ins_query);

        $EVENT_MSG_OBJ->send(['GENERAL_DATA' => $generalDataArrNew[$page]], ('RIDER' === strtoupper($eUserType)) ? RN_USER : ('COMPANY' === strtoupper($eUserType) ? RN_COMPANY : RN_PROVIDER));
        echo 'Success';

        exit;
    }

    $notification_urls = [];
    $_REQUEST['tMessage'] = preg_replace('/\\\\{2,}/', '\\', $_REQUEST['tMessage']);
    $_REQUEST['tMessage'] = str_replace(['\r', '\n'], [chr(13), chr(10)], trim(getProperDataValueWithoutClean($_REQUEST['tMessage'])));
    $_REQUEST['SEND_NOTI'] = 'Yes';
    // $_REQUEST['debug_noti'] = "1";

    for ($c = 0; $c < count($db_insert_arr_final); ++$c) {
        $_REQUEST['page'] = $c;
        $http_query = http_build_query($_REQUEST);
        $notification_urls[] = $tconfig['tsite_url_main_admin'].'send_notifications.php?'.$http_query;
    }

    $mh = curl_multi_init();

    $ADMIN_SESSION['SESSION_DATA'] = json_encode($_SESSION);
    // Loop through each URL.
    foreach ($notification_urls as $k => $url) {
        $requests[$k] = [];
        $requests[$k]['url'] = $url;

        // Create a normal cURL handle for this particular request.
        $requests[$k]['curl_handle'] = curl_init($url);
        // Configure the options for this request.
        curl_setopt($requests[$k]['curl_handle'], CURLOPT_RETURNTRANSFER, true);
        curl_setopt($requests[$k]['curl_handle'], CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($requests[$k]['curl_handle'], CURLOPT_HEADER, true);
        curl_setopt($requests[$k]['curl_handle'], CURLOPT_POST, true);
        curl_setopt($requests[$k]['curl_handle'], CURLOPT_POSTFIELDS, $ADMIN_SESSION);
        // Add our normal / single cURL handle to the cURL multi handle.
        curl_multi_add_handle($mh, $requests[$k]['curl_handle']);
    }

    // Execute our requests using curl_multi_exec.
    $stillRunning = false;
    do {
        curl_multi_exec($mh, $stillRunning);
    } while ($stillRunning);

    // close the handles
    $error = 0;
    $result = [];
    foreach ($requests as $k => $request) {
        $result = curl_multi_getcontent($request['curl_handle']);
        $header_size = curl_getinfo($request['curl_handle'], CURLINFO_HEADER_SIZE);
        $headerstring = substr($result, 0, $header_size);
        $result = substr($result, $header_size);
        curl_multi_remove_handle($mh, $request['curl_handle']);
        curl_close($requests[$k]['curl_handle']);
        if (isset($_REQUEST['debug_noti'])) {
            echo $result.'<br>';
        }
    }

    curl_multi_close($mh);
    if (isset($_REQUEST['debug_noti'])) {
        exit;
    }
    // Added by HV on 10-03-2021 for push notification both android and ios devices

    $_SESSION['success'] = '1';
    $_SESSION['var_msg'] = 'Push Notifications sent successfully.';
    header('location:send_notifications.php');

    exit;
}
$sql_country = "SELECT * FROM country WHERE vCountry != '' ORDER BY vCountry ASC";
$dbcountry_data = $obj->MySQLSelect($sql_country);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <title><?php echo $SITE_NAME; ?> | Send Push-Notification </title>
        <meta content="width=device-width, initial-scale=1.0" name="viewport" />
        <?php
        include_once 'global_files.php';
?>
        <!-- On OFF switch -->
        <link href="../assets/css/jquery-ui.css" rel="stylesheet" />
        <link rel="stylesheet" href="../assets/plugins/switch/static/stylesheets/bootstrap-switch.css" />
    </head>
    <!-- END  HEAD-->
    <!-- BEGIN BODY-->
    <body class="padTop53 " >
        <!-- MAIN WRAPPER -->
        <div id="wrap">
            <?php
    include_once 'header.php';

include_once 'left_menu.php';
?>
            <!--PAGE CONTENT -->
            <div id="content">
                <div class="inner">
                    <div class="row">
                        <div class="col-lg-12">
                            <h2>Send Push-Notification </h2>
                        </div>
                    </div>
                    <hr />
                    <div class="body-div">
                        <div class="form-group">
                            <?php include 'valid_msg.php'; ?>
                            <div class="clear"></div>
                            <form id="_notification_form" name="_notification_form" method="post" action="javascript:void(0);" >
                                <?php if ($MODULES_OBJ->isEnableCountrywiseNotification()) { ?>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Country Name<span class="red"> *</span></label>
                                    </div>
                                    <div class="col-lg-6">
                                        <select name="vCountry" class="form-control" id="vCountrySelected" required="required">
                                            <option value="">Select Country</option>
                                            <?php foreach ($dbcountry_data as $country) { ?>
                                                <?php if ($country['vCountryCode'] === $vCountry) { ?>
                                                    <option selected="selected" value="<?php echo $country['vCountryCode']; ?>"><?php echo $country['vCountry']; ?></option>
                                                <?php } else { ?>
                                                    <option value="<?php echo $country['vCountryCode']; ?>"><?php echo $country['vCountry']; ?></option>
                                                <?php } ?>
                                            <?php } ?>

                                        </select>
                                    </div>
                                </div>
                                <?php } ?>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Select Type<?php if ($MODULES_OBJ->isEnableCountrywiseNotification()) { ?><span class="red"> *</span><?php } ?></label>
                                    </div>
                                    <div class="col-lg-6">
                                        <?php if ($MODULES_OBJ->isEnableCountrywiseNotification()) { ?>
                                        <select class="form-control" name = 'eUserType' id="eUserType" onChange="alluserdata(this.value);showUsers(this.value);" required>
                                            <option value="">Select Type</option>
                                        </select>
                                        <?php } else { ?>
                                        <select class="form-control" name = 'eUserType' id="eUserType" onChange="showUsers(this.value);">
                                            <option value="driver">All <?php echo $langage_lbl_admin['LBL_DRIVERS_NAME_ADMIN']; ?></option>
                                            <option value="rider">All <?php echo $langage_lbl_admin['LBL_RIDERS_ADMIN']; ?></option>
                                            <?php if (!empty($db_login_drv_list)) { ?>
                                                <option value="logged_driver">All Logged in <?php echo $langage_lbl_admin['LBL_DRIVERS_NAME_ADMIN']; ?></option>
                                            <?php } ?>
                                            <?php if (!empty($db_login_rdr_list)) { ?>
                                                <option value="logged_rider">All Logged in <?php echo $langage_lbl_admin['LBL_RIDERS_ADMIN']; ?></option>
                                            <?php } ?>
                                            <?php if (!empty($db_inactive_drv_list)) { ?>
                                                <option value="inactive_driver">All Inactive <?php echo $langage_lbl_admin['LBL_DRIVERS_NAME_ADMIN']; ?></option>
                                            <?php } ?>
                                            <?php if (!empty($db_inactive_rdr_list)) { ?>
                                                <option value="inactive_rider">All Inactive <?php echo $langage_lbl_admin['LBL_RIDERS_ADMIN']; ?></option>
                                            <?php } ?>
                                            <?php if (DELIVERALL === 'Yes') { ?>
                                                <?php if (!empty($db_store_list)) { ?>
                                                    <option value="store">All <?php echo $langage_lbl_admin['LBL_RESTAURANT_TXT_ADMIN']; ?></option>
                                                <?php } ?>
                                                <?php if (!empty($db_login_rst_list)) { ?>
                                                    <option value="logged_store">All Logged in <?php echo $langage_lbl_admin['LBL_RESTAURANT_TXT_ADMIN']; ?></option>
                                                <?php } ?>
                                                <?php if (!empty($db_inactive_rst_list)) { ?>
                                                    <option value="inactive_store">All Inactive <?php echo $langage_lbl_admin['LBL_RESTAURANT_TXT_ADMIN']; ?></option>
                                                <?php } ?>
                                            <?php } ?>
                                        </select>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="row set-dd-css" id="driverRw" <?php if ($MODULES_OBJ->isEnableCountrywiseNotification()) { ?>style="display:none;"<?php } ?>>
                                    <div class="col-lg-12">
                                        <label>Select <?php echo $langage_lbl_admin['LBL_DRIVERS_NAME_ADMIN']; ?><span class="red"> *</span></label>
                                    </div>
                                    <div class="col-lg-6">
                                        <input type="text" name="iDriverId" id="iDriverId" class="form-control magicsearch" style="width:600px !important;" placeholder="Select <?php echo $langage_lbl_admin['LBL_DRIVERS_NAME_ADMIN']; ?>" value=""/>
                                    </div>
                                </div>
                                <div class="row set-dd-css" id="riderRw" style="display:none;">
                                    <div class="col-lg-12">
                                        <label>Select <?php echo $langage_lbl_admin['LBL_RIDERS_ADMIN']; ?><span class="red"> *</span></label>
                                    </div>
                                    <div class="col-lg-6">
                                        <input type="text" name="iRiderId" id="iRiderId" class="form-control magicsearch" style="width:600px !important;" placeholder="Select <?php echo $langage_lbl_admin['LBL_RIDERS_ADMIN']; ?>" value=""/>
                                    </div>
                                </div>
                                <?php if (!empty($db_login_drv_list)) { ?>
                                    <div class="row set-dd-css" id="logindriverRw" style="display:none;">
                                        <div class="col-lg-12">
                                            <label>Select Logged in <?php echo $langage_lbl_admin['LBL_DRIVERS_NAME_ADMIN']; ?><span class="red"> *</span></label>
                                        </div>
                                        <div class="col-lg-6">
                                            <input type="text" name="iLoginDriverId" id="iLoginDriverId" class="form-control magicsearch" style="width:600px !important;" placeholder="Select <?php echo $langage_lbl_admin['LBL_DRIVERS_NAME_ADMIN']; ?>" value=""/>
                                        </div>
                                    </div>
                                <?php } ?>
                                <?php if (!empty($db_login_rdr_list)) { ?>
                                    <div class="row set-dd-css" id="loginriderRw" style="display:none;">
                                        <div class="col-lg-12">
                                            <label>Select Logged in <?php echo $langage_lbl_admin['LBL_RIDERS_ADMIN']; ?><span class="red"> *</span></label>
                                        </div>
                                        <div class="col-lg-6">
                                            <input type="text" name="iLoginRiderId" id="iLoginRiderId" class="form-control magicsearch" style="width:600px !important;" placeholder="Select <?php echo $langage_lbl_admin['LBL_RIDERS_ADMIN']; ?>" value=""/>
                                        </div>
                                    </div>
                                <?php } ?>
                                <?php if (!empty($db_inactive_drv_list)) { ?>
                                    <div class="row set-dd-css" id="inactive_driverRw" style="display:none;">
                                        <div class="col-lg-12">
                                            <label>Select Inactive <?php echo $langage_lbl_admin['LBL_DRIVERS_NAME_ADMIN']; ?><span class="red"> *</span></label>
                                        </div>
                                        <div class="col-lg-6">
                                            <input type="text" name="iInactiveDriverId" id="iInactiveDriverId" class="form-control magicsearch" style="width:600px !important;" placeholder="Select <?php echo $langage_lbl_admin['LBL_DRIVERS_NAME_ADMIN']; ?>" value=""/>
                                        </div>
                                    </div>
                                <?php } ?>
                                <?php if (!empty($db_inactive_rdr_list)) { ?>
                                    <div class="row set-dd-css" id="inactive_riderRw" style="display:none;">
                                        <div class="col-lg-12">
                                            <label>Select Inactive <?php echo $langage_lbl_admin['LBL_RIDERS_ADMIN']; ?><span class="red"> *</span></label>
                                        </div>
                                        <div class="col-lg-6">
                                            <input type="text" name="iInactiveRiderId" id="iInactiveRiderId" class="form-control magicsearch" style="width:600px !important;" placeholder="Select <?php echo $langage_lbl_admin['LBL_RIDERS_ADMIN']; ?>" value=""/>
                                        </div>
                                    </div>
                                <?php } ?>
                                <?php if (DELIVERALL === 'Yes') { ?>

                                    <div class="row set-dd-css" id="storeRw" style="display:none;">
                                        <div class="col-lg-12">
                                            <label>Select <?php echo $langage_lbl_admin['LBL_RESTAURANT_TXT_ADMIN']; ?><span class="red"> *</span></label>
                                        </div>
                                        <div class="col-lg-6">
                                            <input type="text" name="iCompanyId" id="iCompanyId" class="form-control magicsearch" style="width:600px !important;" placeholder="Select <?php echo $langage_lbl_admin['LBL_RESTAURANT_TXT_ADMIN']; ?>" value=""/>
                                        </div>
                                    </div>
                                    <div class="row set-dd-css" id="loginstoreRw" style="display:none;">
                                        <div class="col-lg-12">
                                            <label>Select <?php echo $langage_lbl_admin['LBL_RESTAURANT_TXT_ADMIN']; ?><span class="red"> *</span></label>
                                        </div>
                                        <div class="col-lg-6">
                                            <input type="text" name="iLoginCompanyId" id="iLoginCompanyId" class="form-control magicsearch" style="width:600px !important;" placeholder="Select <?php echo $langage_lbl_admin['LBL_RESTAURANT_TXT_ADMIN']; ?>" value=""/>
                                        </div>
                                    </div>
                                    <div class="row set-dd-css" id="inactive_storeRw" style="display:none;">
                                        <div class="col-lg-12">
                                            <label>Select <?php echo $langage_lbl_admin['LBL_RESTAURANT_TXT_ADMIN']; ?><span class="red"> *</span></label>
                                        </div>
                                        <div class="col-lg-6">
                                            <input type="text" name="iInactiveCompanyId" id="iInactiveCompanyId" class="form-control magicsearch" style="width:600px !important;" placeholder="Select <?php echo $langage_lbl_admin['LBL_RESTAURANT_TXT_ADMIN']; ?>" value=""/>
                                        </div>
                                    </div>
                                <?php } ?>

                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>Message<span class="red"> *</span></label>
                                    </div>
                                    <div class="col-lg-6">
                                        <textarea name="tMessage" class="form-control" id="tMessage" required maxlength="100" ></textarea>
                                        <div>Note:Do not include any special characters, symbols, emoji. This may break push notification.</div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-lg-12">
                                        <input type="submit" class="btn btn-default" name="submit" id="submit" onClick="submit_form();" value="Send Notification" >
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!--END PAGE CONTENT -->
        </div>
        <div class="row loding-action" id="loaderIcon" style="display:none;">
            <div align="center">
                <img src="default.gif">
                <span>Please Wait...</span>
            </div>
        </div>
        <!--END MAIN WRAPPER -->
        <?php include_once 'footer.php'; ?>
        <link href="css/jquery.magicsearch.css" rel="stylesheet">
        <script src="js/jquery.magicsearch.js"></script>
        <style>
            .error {
                color: red;
                font-weight: normal;
                font-size: 13px !important;
                font-family: 'poppins' !important;
            }
            .select2-container--default .select2-search--inline .select2-search__field{
                width:500px !important;
            }
        </style>
        <script src="../assets/plugins/switch/static/js/bootstrap-switch.min.js"></script>
        <script>
            var allDriverArr,existsDriverArr = [];
            var allRiderArr,existsRiderArr = [];
            var loggedInDriverArr,existsloggedInDriverArr = [];
            var loggedInRiderArr,existsloggedInRiderArr = [];
            var inactiveDriverArr,existsinactiveDriverArr = [];
            var inactiveRiderArr,existsinactiveRiderArr = [];
            var allStoreArr,existsallStoreArr = [];
            var loggedInStoreArr,existsloggedInStoreArr = [];
            var inactiveStoreArr,existsinactiveStoreArr  = [];
            var deliverAll = '<?php echo DELIVERALL; ?>';

            <?php if (!$MODULES_OBJ->isEnableCountrywiseNotification()) { ?>
            allDriverArr = <?php echo json_encode($db_drv_list, JSON_UNESCAPED_UNICODE); ?>;
            allRiderArr = <?php echo json_encode($db_rdr_list, JSON_UNESCAPED_UNICODE); ?>;
            loggedInDriverArr = <?php echo json_encode($db_login_drv_list, JSON_UNESCAPED_UNICODE); ?>;
            loggedInRiderArr = <?php echo json_encode($db_login_rdr_list, JSON_UNESCAPED_UNICODE); ?>;
            inactiveDriverArr = <?php echo json_encode($db_inactive_drv_list, JSON_UNESCAPED_UNICODE); ?>;
            inactiveRiderArr = <?php echo json_encode($db_inactive_rdr_list, JSON_UNESCAPED_UNICODE); ?>;
            if (deliverAll == "Yes") {
                allStoreArr = <?php echo json_encode($db_store_list, JSON_UNESCAPED_UNICODE); ?>;
                loggedInStoreArr = <?php echo json_encode($db_login_rst_list, JSON_UNESCAPED_UNICODE); ?>;
                inactiveStoreArr = <?php echo json_encode($db_inactive_rst_list, JSON_UNESCAPED_UNICODE); ?>;
            }

            $(function () {
                //setDropDownData("iDriverId", "alldriver");
                setTimeout(function () {
                    $('#iDriverId').magicsearch({
                        dataSource: allDriverArr,
                        fields: ['DriverName'],
                        id: 'iDriverId',
                        format: '%DriverName%',
                        multiple: true,
                        multiField: 'DriverName'
                    });
                    $('#iRiderId').magicsearch({
                        dataSource: allRiderArr,
                        fields: ['riderName'],
                        id: 'iUserId',
                        format: '%riderName%',
                        multiple: true,
                        multiField: 'riderName'
                    });
                    $('#iLoginDriverId').magicsearch({
                        dataSource: loggedInDriverArr,
                        fields: ['DriverName'],
                        id: 'iDriverId',
                        format: '%DriverName%',
                        multiple: true,
                        multiField: 'DriverName'
                    });
                    $('#iLoginRiderId').magicsearch({
                        dataSource: loggedInRiderArr,
                        fields: ['riderName'],
                        id: 'iUserId',
                        format: '%riderName%',
                        multiple: true,
                        multiField: 'riderName'
                    });
                    $('#iInactiveDriverId').magicsearch({
                        dataSource: inactiveDriverArr,
                        fields: ['DriverName'],
                        id: 'iDriverId',
                        format: '%DriverName%',
                        multiple: true,
                        multiField: 'DriverName'
                    });
                    $('#iInactiveRiderId').magicsearch({
                        dataSource: inactiveRiderArr,
                        fields: ['riderName'],
                        id: 'iUserId',
                        format: '%riderName%',
                        multiple: true,
                        multiField: 'riderName'
                    });
                    if (deliverAll == "Yes") {
                        $('#iCompanyId').magicsearch({
                            dataSource: allStoreArr,
                            fields: ['vCompany'],
                            id: 'iCompanyId',
                            format: '%vCompany%',
                            multiple: true,
                            multiField: 'vCompany'
                        });
                        $('#iLoginCompanyId').magicsearch({
                            dataSource: loggedInStoreArr,
                            fields: ['vCompany'],
                            id: 'iCompanyId',
                            format: '%vCompany%',
                            multiple: true,
                            multiField: 'vCompany'
                        });
                        $('#iInactiveCompanyId').magicsearch({
                            dataSource: inactiveStoreArr,
                            fields: ['vCompany'],
                            id: 'iCompanyId',
                            format: '%vCompany%',
                            multiple: true,
                            multiField: 'vCompany'
                        });
                    }
                }, 1000);
            });
            <?php } ?>
            function setDropDownData(dpId, requestType) {
                notificationArr = [];
                $(".loader-default").fadeOut("slow");

                var ajaxData = {
                    'URL': '<?php echo $tconfig['tsite_url_main_admin']; ?>ajax_get_notification_details.php?qt=' + requestType,
                    'AJAX_DATA': "iUserId=" + userid,
                    'REQUEST_DATA_TYPE': 'JSON'
                };
                getDataFromAjaxCall(ajaxData, function(response) {
                    if(response.action == "1") {
                        var data = response.result;
                        notificationArr = data;
                        console.log(dpId + " Data Count :" + data.length);
                    }
                    else {
                        console.log(response.result);
                    }
                });
            }
            function submit_form() {
                var joinTxt = '';
                if ($("#_notification_form").valid()) {
                    var userType = $("#eUserType").val();
                    if (userType == 'rider') {
                        if ($("#iRiderId").val() == '' || $("#iRiderId").val() == null) {
                            joinTxt = 'All <?php echo $langage_lbl_admin['LBL_RIDERS_ADMIN']; ?>';
                        } else {
                            var len = $('#iRiderId option:selected').length;
                            joinTxt = 'Selected ' + len + ' <?php echo $langage_lbl_admin['LBL_RIDERS_ADMIN']; ?>(s)';
                        }
                    } else if (userType == 'driver') {
                        if ($("#iDriverId").val() == '' || $("#iDriverId").val() == null) {
                            joinTxt = '<?php echo $langage_lbl_admin['LBL_DRIVERS_NAME_ADMIN']; ?>';
                        } else {
                            var len = $('#iDriverId option:selected').length;
                            joinTxt = 'Selected ' + len + ' <?php echo $langage_lbl_admin['LBL_DRIVERS_NAME_ADMIN']; ?>(s)';
                        }
                    } else if (userType == 'logged_driver') {
                        if ($("#iLoginDriverId").val() == '' || $("#iLoginDriverId").val() == null) {
                            joinTxt = 'All Logged In <?php echo $langage_lbl_admin['LBL_DRIVERS_NAME_ADMIN']; ?>';
                        } else {
                            var len = $('#iLoginDriverId option:selected').length;
                            joinTxt = 'Selected ' + len + ' Logged In <?php echo $langage_lbl_admin['LBL_DRIVERS_NAME_ADMIN']; ?>(s)';
                        }
                    } else if (userType == 'logged_rider') {
                        if ($("#iLoginRiderId").val() == '' || $("#iLoginRiderId").val() == null) {
                            joinTxt = 'All Logged In <?php echo $langage_lbl_admin['LBL_RIDERS_ADMIN']; ?>';
                        } else {
                            var len = $('#iLoginRiderId option:selected').length;
                            joinTxt = 'Selected ' + len + ' Logged In <?php echo $langage_lbl_admin['LBL_RIDERS_ADMIN']; ?>(s)';
                        }
                    } else if (userType == 'inactive_driver') {
                        if ($("#iInactiveDriverId").val() == '' || $("#iInactiveDriverId").val() == null) {
                            joinTxt = 'All Inactive <?php echo $langage_lbl_admin['LBL_DRIVERS_NAME_ADMIN']; ?>';
                        } else {
                            var len = $('#iInactiveDriverId option:selected').length;
                            joinTxt = 'Selected ' + len + ' Inactive <?php echo $langage_lbl_admin['LBL_DRIVERS_NAME_ADMIN']; ?>(s)';
                        }
                    } else if (userType == 'inactive_rider') {
                        if ($("#iInactiveRiderId").val() == '' || $("#iInactiveRiderId").val() == null) {
                            joinTxt = 'All Inactive <?php echo $langage_lbl_admin['LBL_RIDERS_ADMIN']; ?>';
                        } else {
                            var len = $('#iInactiveRiderId option:selected').length;
                            joinTxt = 'Selected ' + len + ' Inactive <?php echo $langage_lbl_admin['LBL_RIDERS_ADMIN']; ?>(s)';
                        }
                    } else if (userType == 'store' && deliverAll == "Yes") {
                        if ($("#iCompanyId").val() == '' || $("#iCompanyId").val() == null) {
                            joinTxt = 'All <?php echo $langage_lbl_admin['LBL_RESTAURANT_TXT_ADMIN']; ?>';
                        } else {
                            var len = $('#iCompanyId option:selected').length;
                            joinTxt = 'Selected ' + len + ' <?php echo $langage_lbl_admin['LBL_RESTAURANT_TXT_ADMIN']; ?>(s)';
                        }
                    } else if (userType == 'logged_store' && deliverAll == "Yes") {
                        if ($("#iLoginCompanyId").val() == '' || $("#iLoginCompanyId").val() == null) {
                            joinTxt = 'All Logged In <?php echo $langage_lbl_admin['LBL_RESTAURANT_TXT_ADMIN']; ?>';
                        } else {
                            var len = $('#iLoginCompanyId option:selected').length;
                            joinTxt = 'Selected ' + len + ' <?php echo $langage_lbl_admin['LBL_RESTAURANT_TXT_ADMIN']; ?>(s)';
                        }
                    } else if (userType == 'inactive_store' && deliverAll == "Yes") {
                        if ($("#iInactiveCompanyId").val() == '' || $("#iInactiveCompanyId").val() == null) {
                            joinTxt = 'All Inactive <?php echo $langage_lbl_admin['LBL_RESTAURANT_TXT_ADMIN']; ?>';
                        } else {
                            var len = $('#iInactiveCompanyId option:selected').length;
                            joinTxt = 'Selected ' + len + ' <?php echo $langage_lbl_admin['LBL_RESTAURANT_TXT_ADMIN']; ?>(s)';
                        }
                    }

                    if (confirm("Confirm to send push notification to " + joinTxt + "?")) {
                        $('#loaderIcon').show();
                        $("#_notification_form").attr('action', '');
                        $("#_notification_form").submit();
                    }
                }
            }
            function showUsers(userType) {
                if (userType == 'driver') {
                    $("#driverRw").show();


                    $("#riderRw,#logindriverRw,#loginriderRw,#inactive_driverRw,#inactive_riderRw").hide();
                    if (deliverAll == "Yes") {
                        $("#inactive_storeRw,#loginstoreRw,#storeRw").hide();
                    }
                } else if (userType == 'rider') {
                    $("#riderRw").show();


                    $("#driverRw,#logindriverRw,#loginriderRw,#inactive_driverRw,#inactive_riderRw").hide();
                    if (deliverAll == "Yes") {
                        $("#inactive_storeRw,#loginstoreRw,#storeRw").hide();
                    }
                } else if (userType == 'logged_driver') {
                    $("#logindriverRw").show();



                    $("#riderRw,#driverRw,#loginriderRw,#inactive_driverRw,#inactive_riderRw").hide();
                    if (deliverAll == "Yes") {
                        $("#inactive_storeRw,#loginstoreRw,#storeRw").hide();
                    }
                } else if (userType == 'logged_rider') {
                    $("#loginriderRw").show();


                    $("#riderRw,#driverRw,#logindriverRw,#inactive_driverRw,#inactive_riderRw").hide();
                    if (deliverAll == "Yes") {
                        $("#inactive_storeRw,#loginstoreRw,#storeRw").hide();
                    }
                } else if (userType == 'inactive_driver') {
                    $("#inactive_driverRw").show();


                    $("#riderRw,#driverRw,#logindriverRw,#loginriderRw,#inactive_riderRw").hide();
                    if (deliverAll == "Yes") {
                        $("#inactive_storeRw,#loginstoreRw,#storeRw").hide();
                    }
                } else if (userType == 'inactive_rider') {
                    $("#inactive_riderRw").show();


                    $("#loginriderRw,#riderRw,#driverRw,#logindriverRw,#inactive_driverRw").hide();
                    if (deliverAll == "Yes") {
                        $("#inactive_storeRw,#loginstoreRw,#storeRw").hide();
                    }
                } else if (userType == 'store' && deliverAll == "Yes") {
                    $("#storeRw").show();


                    $("#inactive_riderRw,#loginriderRw,#riderRw,#driverRw,#logindriverRw,#inactive_driverRw,#inactive_storeRw,#loginstoreRw").hide();
                } else if (userType == 'logged_store' && deliverAll == "Yes") {
                    $("#loginstoreRw").show();


                    $("#inactive_riderRw,#loginriderRw,#riderRw,#driverRw,#logindriverRw,#inactive_driverRw,#inactive_storeRw,#storeRw").hide();
                } else if (userType == 'inactive_store' && deliverAll == "Yes") {
                    $("#inactive_storeRw").show();


                    $("#loginstoreRw,#storeRw,#inactive_riderRw,#loginriderRw,#riderRw,#driverRw,#logindriverRw,#inactive_driverRw").hide();
                }
            }
        function alluserdata(eUserType) {
                $.ajax({
                    type: "POST",
                    url: "<?php echo $tconfig['tsite_url_main_admin']; ?>alluserdata.php",
                    dataType: 'json',
                    async: false,
                    data: {
                        vCountryCode: $("#vCountrySelected").val(),
                        userType: eUserType
                    },
                    success: function (dataHtml2) {
                        exists = dataHtml2;
                         $.each(exists, function(index, element) {
                            if(index == 'driverlist'){
                                existsDriverArr = element;
                            } else if (index == 'userlist'){
                                existsRiderArr = element;
                            } else if (index == 'loggedindriverlist') {
                                existsloggedInDriverArr = element;
                            }  else if (index == 'loggedinriderlist') {
                                existsloggedInRiderArr = element;
                            }  else if (index == 'inactivedriverlist') {
                                existsinactiveDriverArr = element;
                            }  else if (index == 'inactiveuserlist') {
                                existsinactiveRiderArr = element;
                            } else if (index == 'storelist') {
                                existsallStoreArr = element;
                            } else if (index == 'loginstorelist') {
                                existsloggedInStoreArr = element;
                            } else if (index == 'inactivestorelist') {
                                existsinactiveStoreArr = element;
                            }

                         });
                        set_exists();
                    }, error: function (dataHtml2) {
                    }
                });
                return true;
            }
            //function to call inside ajax callback
            function set_exists(){
                allDriverArr = existsDriverArr;
                allRiderArr = existsRiderArr;
                loggedInDriverArr = existsloggedInDriverArr;
                loggedInRiderArr = existsloggedInRiderArr;
                inactiveDriverArr = existsinactiveDriverArr;
                inactiveRiderArr = existsinactiveRiderArr;
                if (deliverAll == "Yes") {
                    allStoreArr = existsallStoreArr;
                    loggedInStoreArr = existsloggedInStoreArr;
                    inactiveStoreArr = existsinactiveStoreArr;
                }

                //$(function () {
                //setDropDownData("iDriverId", "alldriver");
                setTimeout(function () {
                    $('#iDriverId').magicsearch({
                        dataSource: allDriverArr,
                        fields: ['DriverName'],
                        id: 'iDriverId',
                        format: '%DriverName%',
                        multiple: true,
                        multiField: 'DriverName'
                    });
                    $('#iRiderId').magicsearch({
                        dataSource: allRiderArr,
                        fields: ['riderName'],
                        id: 'iUserId',
                        format: '%riderName%',
                        multiple: true,
                        multiField: 'riderName'
                    });
                    $('#iLoginDriverId').magicsearch({
                        dataSource: loggedInDriverArr,
                        fields: ['DriverName'],
                        id: 'iDriverId',
                        format: '%DriverName%',
                        multiple: true,
                        multiField: 'DriverName'
                    });
                    $('#iLoginRiderId').magicsearch({
                        dataSource: loggedInRiderArr,
                        fields: ['riderName'],
                        id: 'iUserId',
                        format: '%riderName%',
                        multiple: true,
                        multiField: 'riderName'
                    });
                    $('#iInactiveDriverId').magicsearch({
                        dataSource: inactiveDriverArr,
                        fields: ['DriverName'],
                        id: 'iDriverId',
                        format: '%DriverName%',
                        multiple: true,
                        multiField: 'DriverName'
                    });
                    $('#iInactiveRiderId').magicsearch({
                        dataSource: inactiveRiderArr,
                        fields: ['riderName'],
                        id: 'iUserId',
                        format: '%riderName%',
                        multiple: true,
                        multiField: 'riderName'
                    });
                    if (deliverAll == "Yes") {
                        $('#iCompanyId').magicsearch({
                            dataSource: allStoreArr,
                            fields: ['vCompany'],
                            id: 'iCompanyId',
                            format: '%vCompany%',
                            multiple: true,
                            multiField: 'vCompany'
                        });
                        $('#iLoginCompanyId').magicsearch({
                            dataSource: loggedInStoreArr,
                            fields: ['vCompany'],
                            id: 'iCompanyId',
                            format: '%vCompany%',
                            multiple: true,
                            multiField: 'vCompany'
                        });
                        $('#iInactiveCompanyId').magicsearch({
                            dataSource: inactiveStoreArr,
                            fields: ['vCompany'],
                            id: 'iCompanyId',
                            format: '%vCompany%',
                            multiple: true,
                            multiField: 'vCompany'
                        });
                    }
                }, 1000);
            }

            $( "select[name='vCountry']" ).change(function () {
                var vCountryCode = $(this).val();
                if(vCountryCode) {
                    $.ajax({
                        url: "alluserdata.php",
                        dataType: 'html',
                        type: "POST",
                        async: false,
                        data: {'vCountryCode':vCountryCode,'checkusedata':'Yes'},
                        success: function(data) {
                           // $('select[name="eUserType"]').empty();
                            //$.each(data, function(key, value) {
                                if(data != ''){
                                    $('select[name="eUserType"]').empty().append(data);
                                } else {
                                    $('select[name="eUserType"]').empty();
                                }
                            //});
                        }
                    });
                }else{
                   // $('select[name="eUserType"]').empty();
                }
                showUsers('');
            });
        </script>
    </body>
    <!-- END BODY-->
</html>
