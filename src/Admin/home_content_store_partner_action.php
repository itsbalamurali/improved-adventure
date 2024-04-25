<?php



include_once '../common.php';

$id = $_REQUEST['id'] ?? '';
if ('Yes' === $THEME_OBJ->isProThemeActive()) {
    header("location:store_partner_content_action.php?id={$id}");

    exit;
}
