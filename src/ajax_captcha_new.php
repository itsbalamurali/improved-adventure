<?php





include_once 'common.php';
session_start();
$POST_CAPTCHA = $_REQUEST['POST_CAPTCHA'] ?? '';
$SESS_CAPTCHA = $_SESSION['SESS_CAPTCHA'];
if ($POST_CAPTCHA === $SESS_CAPTCHA) {
    echo 'true';
} else {
    echo 'false';
}
