<?php



include_once 'common.php';
$email = $_POST['femail'] ?? '';
$action = $_POST['action'] ?? '';

$sql = "SELECT * from administrator where vEmail = '".$email."' and eStatus != 'Deleted'";
$db_login = $obj->MySQLSelect($sql);
$status = $COMM_MEDIA_OBJ->SendMailToMember('CUSTOMER_FORGETPASSWORD', $db_login);
if (1 === $status) {
    $var_msg = 'Your Password has been sent Successfully.';
    $error_msg = '1';
} else {
    $var_msg = 'Error in Sending password.';
    $error_msg = '0';
}
