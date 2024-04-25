<?php



include_once '../common.php';

$id = $_REQUEST['id'] ?? '';
if ('Yes' === $THEME_OBJ->isProThemeActive()) {
    header("location:delivery_driver_content_action.php?id={$id}");

    exit;
}
