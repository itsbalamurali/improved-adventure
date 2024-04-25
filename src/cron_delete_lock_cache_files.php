<?php 
include 'common.php';

/* Cron Log Update */
WriteToFile($tconfig['tsite_script_file_path'] . "cron_delete_lock_cache_files_status.txt", "running");
/* Cron Log Update End */

$directories = array('lockFile', 'cache_files');

$all_files = array();
foreach ($directories as $directory) 
{
	$dir_path = $tconfig['tpanel_path'].'webimages/'.$directory;
	if(is_dir($dir_path))
	{
		$files = array_diff(scandir($dir_path), array('.', '..'));
		$all_files[$directory] = $files;
	}
}

$files_delete = array();
foreach ($all_files as $dir => $files) 
{
	foreach ($files as $file) 
	{
		$file_path = $tconfig['tpanel_path'].'webimages/'.$dir.'/'.$file;
		$fileinfo = stat($file_path);
		$file_time = $fileinfo['mtime'];

		if((((strtotime(date('Y-m-d H:i:s')) - $file_time) / 60) / 60) > 24)
		{
			if(file_exists($file_path))
			{
				unlink($file_path);
			}
		}
	}
}

/* Cron Log Update */
WriteToFile($tconfig['tsite_script_file_path'] . "cron_delete_lock_cache_files_status.txt", "executed");

$cron_logs = GetFileData($tconfig['tsite_script_file_path'] . 'system_cron_logs');
$cron_logs = json_decode($cron_logs, true); 

foreach ($cron_logs as $ckey => $cfile) 
{
    if($cfile['filename'] == "cron_delete_lock_cache_files.php")
    {
        $cron_logs[$ckey]['last_executed'] = date('Y-m-d H:i:s');
    }
}

WriteToFile($tconfig['tsite_script_file_path'] . "system_cron_logs", json_encode($cron_logs));
/* Cron Log Update End */
?>