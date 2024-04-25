<?php



include_once '../common.php';

if ('Yes' === $THEME_OBJ->isCubeJekXv3ProThemeActive()) {
    include_once 'home_content_cubejekxv3pro_action.php';

    exit;
}
if ('Yes' === $THEME_OBJ->isPXRDProThemeActive()) {
    include_once 'home_content_pxrdpro_action.php';

    exit;
}
if ('Yes' === $THEME_OBJ->isPXTProThemeActive()) {
    include_once 'home_content_pxtpro_action.php';

    exit;
}
if ('Yes' === $THEME_OBJ->isPXCProThemeActive()) {
    include_once 'home_content_pxcpro_action.php';

    exit;
}
if ('Yes' === $THEME_OBJ->isProSPThemeActive()) {
    include_once 'home_content_prosp_action.php';

    exit;
}

if ('Yes' === $THEME_OBJ->isProDeliverallThemeActive()) {
    include_once 'home_content_prodeliverall_action.php';

    exit;
}

if ('Yes' === $THEME_OBJ->isProDeliveryThemeActive()) {
    include_once 'home_content_prodelivery_action.php';

    exit;
}

if ('Yes' === $THEME_OBJ->isProDeliveryKingThemeActive()) {
    include_once 'home_content_prodeliveryking_action.php';

    exit;
}
