<?php



include_once '../common.php';
$_SESSION['sess_iAdminUserId'] = '';
$_SESSION['sess_vAdminFirstName'] = '';
$_SESSION['sess_vAdminLastName'] = '';
$_SESSION['sess_vAdminEmail'] = '';
$_SESSION['current_link'] = '';
unset($_SESSION['OrderDetails'], $_SESSION['sess_iServiceId_mr'], $_SESSION['sess_iUserId_mr'], $_SESSION['sess_iUserAddressId_mr'], $_SESSION['sess_promoCode'], $_SESSION['sess_vCurrency_mr'], $_SESSION['sess_currentpage_url_mr'], $_SESSION['sess_vLatitude_mr'], $_SESSION['sess_vLongitude_mr'], $_SESSION['sess_vServiceAddress_mr'], $_SESSION['sess_vName_mr'], $_SESSION['sess_company_mr'], $_SESSION['sess_vEmail_mr'], $_SESSION['sess_user_mr'], $_SESSION['sess_userby_mr'], $_SESSION['sess_userby_id'], $_SESSION['server_requirements_modal']);

    if ('hotel' === $_SESSION['SessionUserType']) {
    $_SESSION['SessionUserType'] = '';
    if ('Yes' === $_SESSION['SessionRedirectUserPanel']) {
        $_SESSION['SessionRedirectUserPanel'] = '';
        header('location:../sign-in?type=hotel');
    } else {
        header('location:../hotel');
    }
} else {
    // print_r($_SESSION);print_r($_SERVER); exit;
    $_SESSION['login_redirect_url'] = $_SERVER['HTTP_REFERER'];
    header('location:index.php');
}

exit;
