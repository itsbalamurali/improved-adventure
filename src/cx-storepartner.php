<?php
include_once("common.php");
$showSignRegisterLinks = 1;
if($THEME_OBJ->isCubeJekXv3ProThemeActive() == 'Yes' || $THEME_OBJ->isPXCProThemeActive() == "Yes" ) {
    include_once('storepartner_page.php'); exit;
}
?>