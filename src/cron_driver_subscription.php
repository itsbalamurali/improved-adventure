<?php





include_once 'common.php';

// Cron Log Update
WriteToFile($tconfig['tsite_script_file_path'].'cron_driver_subscription_status.txt', 'running');
// Cron Log Update End

$DRIVER_SUBSCRIPTION_REMINDER_NOTIFICATION_DAYS = '';

$DRIVER_SUBSCRIPTION_REMINDER_NOTIFICATION_DAYS = $CONFIG_OBJ->getConfigurations('configurations', 'DRIVER_SUBSCRIPTION_REMINDER_NOTIFICATION_DAYS');
$DRIVER_SUBSCRIPTION_ENABLE = $CONFIG_OBJ->getConfigurations('configurations', 'DRIVER_SUBSCRIPTION_ENABLE');
$curdate = date('Y-m-d H:i:s');

$tblDetails = 'driver_subscription_details';
// $date = '2019-09-13 00:00:00';
// $selExpired = "SELECT iDriverSubscriptionDetailsId,iDriverId,tExpiryDate >= '$date' AS tExpiryDate, datediff(tExpiryDate,'$date') AS daysRemain,eSubscriptionStatus FROM $tblDetails";

$selExpired = "SELECT iDriverSubscriptionDetailsId,iDriverId,tExpiryDate >= '{$curdate}' AS tExpiryDate, datediff(tExpiryDate,'{$curdate}') AS daysRemain,eSubscriptionStatus FROM {$tblDetails}";
$dataExpired = $obj->MySQLSelect($selExpired);

foreach ($dataExpired as $key => $value) {
    // #####################  UPDATE STATUS WHEN EXPIRY DATE IS OVER  ############################

    if ($value['tExpiryDate'] <= 0 && 'Expired' !== $value['eSubscriptionStatus']) {
        $where = "iDriverSubscriptionDetailsId = '".$value['iDriverSubscriptionDetailsId']."'";
        $DataUpdate['eSubscriptionStatus'] = 'Expired';
        $id = $obj->MySQLQueryPerform($tblDetails, $DataUpdate, 'update', $where);
    }

    // #####################  SEND NOTIFICATION WHEN X DAYS REMAIN IN SUBSCRIPTION  ############################

    if ('Yes' === $DRIVER_SUBSCRIPTION_ENABLE) {
        if (!empty($DRIVER_SUBSCRIPTION_REMINDER_NOTIFICATION_DAYS) && $value['daysRemain'] <= $DRIVER_SUBSCRIPTION_REMINDER_NOTIFICATION_DAYS && 'Subscribed' === $value['eSubscriptionStatus']) {
            $driverData = get_value('register_driver', 'vName,vLastName,vEmail,iAppVersion,eDeviceType,iGcmRegId,vLang,eAppTerminate,eDebugMode,eHmsDevice', 'iDriverId', $value['iDriverId']);

            if (count($driverData) > 0) {
                $deviceTokens_arr_ios = $registation_ids_new = [];

                $iGcmRegId = $driverData[0]['iGcmRegId'];
                $eDeviceType = $driverData[0]['eDeviceType'];
                $eAppTerminate = $driverData[0]['eAppTerminate'];
                $eHmsDevice = $driverData[0]['eHmsDevice'];
                $vLang = $driverData[0]['vLang'];
                if ('' === $vLang || null === $vLang) {
                    $vLang = get_value('language_master', 'vCode', 'eDefault', 'Yes', '', 'true');
                }
                $languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLang, '1');
                $alertMsg = $languageLabelsArr['LBL_SUBSCRIPTION_EXPIRED_REMAIN_DAYS'];

                if (0 === $value['daysRemain']) {
                    $daysRemainTxt = ' '.$languageLabelsArr['LBL_SUBSCRIPTION_MAIL_REMAINTXT_ONE'];
                } else {
                    $daysRemainTxt = $value['daysRemain'].' '.$languageLabelsArr['LBL_SUBSCRIPTION_MAIL_REMAINTXT_SECOND'];
                }
                $message = str_replace('##', $daysRemainTxt, $alertMsg);

                $generalDataArr[] = [
                    'eDeviceType' => $eDeviceType,
                    'deviceToken' => $iGcmRegId,
                    'alertMsg' => $alertMsg,
                    'eAppTerminate' => $eAppTerminate,
                    'eDebugMode' => $eDebugMode,
                    'eHmsDevice' => $eHmsDevice,
                    'message' => $message,
                ];

                $EVENT_MSG_OBJ->send(['GENERAL_DATA' => $generalDataArr], RN_PROVIDER);

                // #####################  SEND MAIL WHEN X DAYS REMAIN IN SUBSCRIPTION  ############################

                $getMaildata['vEmail'] = $driverData[0]['vEmail'];
                if (0 === $value['daysRemain']) {
                    $getMaildata['daysRemainTxt'] = ' '.$languageLabelsArr['LBL_SUBSCRIPTION_MAIL_REMAINTXT_ONE'];
                } else {
                    $getMaildata['daysRemainTxt'] = $value['daysRemain'].' '.$languageLabelsArr['LBL_SUBSCRIPTION_MAIL_REMAINTXT_SECOND'];
                }
                // $getMaildata['daysRemain'] = $value['daysRemain'];
                $getMaildata['FromName'] = $driverData[0]['vName'].' '.$driverData[0]['vLastName'];
                $mail = $COMM_MEDIA_OBJ->SendMailToMember('CRON_SUBSCRIBE_REMAIN_DAYS', $getMaildata);

                if (!empty($mail)) {
                    echo 'Mail has been sent successfully to '.$driverData[0]['vEmail'];
                }
            }
        }
    }
}

// Cron Log Update
WriteToFile($tconfig['tsite_script_file_path'].'cron_driver_subscription_status.txt', 'executed');

$cron_logs = GetFileData($tconfig['tsite_script_file_path'].'system_cron_logs');
$cron_logs = json_decode($cron_logs, true);

foreach ($cron_logs as $ckey => $cfile) {
    if ('cron_driver_subscription.php' === $cfile['filename']) {
        $cron_logs[$ckey]['last_executed'] = date('Y-m-d H:i:s');
    }
}

WriteToFile($tconfig['tsite_script_file_path'].'system_cron_logs', json_encode($cron_logs));
// Cron Log Update End
