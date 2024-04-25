<?php





include_once 'common.php';

// Cron Log Update
WriteToFile($tconfig['tsite_script_file_path'].'cron_delete_cab_requests_status.txt', 'running');
// Cron Log Update End

$cmpDate = date('Y-m-d H:i:s', strtotime('-24 hours', time()));
$obj->deleteManyRecordsFromMongoDB(TSITE_DB, 'cab_request_now', ['dAddedDate' => ['$lt' => $cmpDate]]);

// Cron Log Update
WriteToFile($tconfig['tsite_script_file_path'].'cron_delete_cab_requests_status.txt', 'executed');

$cron_logs = GetFileData($tconfig['tsite_script_file_path'].'system_cron_logs');
$cron_logs = json_decode($cron_logs, true);

foreach ($cron_logs as $ckey => $cfile) {
    if ('cron_delete_cab_requests.php' === $cfile['filename']) {
        $cron_logs[$ckey]['last_executed'] = date('Y-m-d H:i:s');
    }
}

WriteToFile($tconfig['tsite_script_file_path'].'system_cron_logs', json_encode($cron_logs));
// Cron Log Update End
