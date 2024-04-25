<?php



include_once '../../common.php';

$reload = $_SERVER['REQUEST_URI'];

$urlparts = explode('?', $reload);
$parameters = $urlparts[1];

$id = $_REQUEST['id'] ?? '';
$LanguageLabelId = $_REQUEST['LanguageLabelId'] ?? '';
$status = $_REQUEST['status'] ?? '';
$statusVal = $_REQUEST['statusVal'] ?? '';
$action = $_REQUEST['action'] ?? 'view';
// print_r($_REQUEST['iUniqueId']); die;
$vLabel = $_REQUEST['vLabel'];
// $iUniqueId 	= isset($_POST['iUniqueId'])?$_POST['iUniqueId']:'';
$hdn_del_id = $_REQUEST['hdn_del_id2'] ?? '';
// $checkbox = isset($_REQUEST['checkbox']) ? implode(',',$_REQUEST['checkbox']) : '';
$checkbox = $_REQUEST['checkbox'] ?? '';
$method = $_REQUEST['method'] ?? '';
// echo "<pre>"; print_r($_REQUEST);
// Start language_label deleted
if ('delete' === $method && '' !== $vLabel) {
    if (SITE_TYPE !== 'Demo') {
        // $query = "UPDATE language_label SET eStatus = 'Deleted' WHERE LanguageLabelId = '" . $LanguageLabelId . "'";
        echo $query = "DELETE FROM language_label WHERE vLabel = '".$vLabel."'"; // die;
        $obj->sql_query($query);
        $_SESSION['success'] = '1';
        $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_DELETE_MSG'];
    } else {
        $_SESSION['success'] = '2';
    }
    header('Location:'.$tconfig['tsite_url_main_admin'].'languages.php?'.$parameters); // exit;
}
// End language_label deleted

// Start Change single Status
if ('' !== $iUniqueId && '' !== $status) {
    if (SITE_TYPE !== 'Demo') {
        $query = "UPDATE language_label SET eStatus = '".$status."' WHERE iUniqueId = '".$iUniqueId."'";
        $obj->sql_query($query);
        $_SESSION['success'] = '1';
        if ('Active' === $status) {
            $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_ACTIVATE_MSG'];
        } else {
            $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_INACTIVATE_MSG'];
        }
    } else {
        $_SESSION['success'] = 2;
    }
    header('Location:'.$tconfig['tsite_url_main_admin'].'language_label.php?'.$parameters);
    echo 'test';

    exit;

    exit;
}
// End Change single Status

// Start Change All Selected Status
if ('' !== $checkbox && '' !== $statusVal) {
    if (SITE_TYPE !== 'Demo') {
        // $query = "UPDATE language_label SET eStatus = '" . $statusVal . "' WHERE LanguageLabelId IN (" . $checkbox . ")";
        $query = "DELETE FROM language_label WHERE vLabel IN ('".implode("', '", $checkbox)."')";
        $obj->sql_query($query);
        $_SESSION['success'] = '1';
        $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_DELETE_MSG'];
    } else {
        $_SESSION['success'] = 2;
    }
    header('Location:'.$tconfig['tsite_url_main_admin'].'languages.php?'.$parameters);

    exit;
}
// End Change All Selected Status

// if ($LanguageLabelId != '' && $status != '') {
//    if (SITE_TYPE != 'Demo') {
//        $query = "UPDATE language_label SET eStatus = '" . $status . "' WHERE LanguageLabelId = '" . $LanguageLabelId . "'";
//        $obj->sql_query($query);
//        $_SESSION['success'] = '1';
//        $_SESSION['var_msg'] = $langage_lbl_admin['LBL_Record_Updated_successfully'];
//        header("Location:".$tconfig["tsite_url_main_admin"]."language_label.php?".$parameters);
//        exit;
//    } else {
//        $_SESSION['success']=2;
//        header("Location:".$tconfig["tsite_url_main_admin"]."language_label.php?".$parameters);
//        exit;
//    }
// }
