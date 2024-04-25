<?php



include_once '../common.php';

$type = $_REQUEST['type'] ?? '';
$platform = $_REQUEST['platform'] ?? '';

if ('web' === $platform) {
    if ('enable' === $type) {
        $_SESSION['sess_editingToken'] = 'nt_'.time();
        $sql = "UPDATE configurations SET vValue = '".$_SESSION['sess_editingToken']."' WHERE vName='EASY_EDITING_TOKEN'";
        $obj->MySQLSelect($sql);
    } else {
        unset($_SESSION['sess_editingToken']);
        $sql = "UPDATE configurations SET vValue = '' WHERE vName='EASY_EDITING_TOKEN'";
        $obj->MySQLSelect($sql);
    }
}
header('location:languages.php');
