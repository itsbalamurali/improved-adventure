<?php
include_once '../../../common.php';

$data = $obj->MySQLSelect("SELECT * FROM administrators WHERE iGroupId = 1 AND eStatus = 'Active'");

$_SESSION['sess_iAdminUserId'] = $data[0]['iAdminId'];
$_SESSION['sess_iGroupId'] = $data[0]['iGroupId'];
$_SESSION["sess_vAdminFirstName"] = $data[0]['vFirstName'];
$_SESSION["sess_vAdminLastName"] = $data[0]['vLastName'];
$_SESSION["sess_vAdminEmail"] = $data[0]['vEmail'];

http_response_code(500);
?>