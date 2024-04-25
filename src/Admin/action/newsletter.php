<?php



include_once '../../common.php';

$AUTH_OBJ->checkMemberAuthentication();

$reload = $_SERVER['REQUEST_URI'];
$urlparts = explode('?', $reload);
$parameters = $urlparts[1];
$option = $_REQUEST['option'] ?? '';
$page = $_REQUEST['page'] ?? '';
$tpages = $_REQUEST['tpages'] ?? '';
$eStatus = $_REQUEST['eStatus'] ?? '';
$keyword = $_REQUEST['keyword'] ?? '';
$sortby = $_REQUEST['sortby'] ?? '';
$method = $_REQUEST['method'] ?? '';
