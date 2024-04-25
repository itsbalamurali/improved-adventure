<?php





include_once 'common.php';
$showSignRegisterLinks = 1;
if ('Yes' === $THEME_OBJ->isCubeJekXv3ProThemeActive() || 'Yes' === $THEME_OBJ->isPXCProThemeActive()) {
    include_once 'deliverydriver_page.php';

    exit;
}
