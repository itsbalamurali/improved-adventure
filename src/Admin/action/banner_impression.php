<?php



include_once '../../common.php';

$AUTH_OBJ->checkMemberAuthentication();
$reload = $_SERVER['REQUEST_URI'];
$urlparts = explode('?', $reload);
$parameters = $urlparts[1];
$iAdvertBannerId = $_REQUEST['id'] ?? '';
$iAdvertBannerId = $_REQUEST['iAdvertBannerId'] ?? '';
$status = $_REQUEST['status'] ?? '';
$statusVal = $_REQUEST['statusVal'] ?? '';
$action = $_REQUEST['action'] ?? 'view';
$hdn_del_id = $_REQUEST['hdn_del_id'] ?? '';
$checkbox = isset($_REQUEST['checkbox']) ? implode(',', $_REQUEST['checkbox']) : '';
$method = $_REQUEST['method'] ?? '';
// echo "<pre>";
// print_r($_REQUEST);die;
// Start make deleted
$tableName = 'banner_impression';
$redirectUrl = $tconfig['tsite_url_main_admin'].'banner_impression.php?'.$parameters;
