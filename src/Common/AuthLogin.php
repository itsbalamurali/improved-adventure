<?php



namespace Kesk\Web\Common;

class AuthLogin
{
    public function __construct()
    {
        $_SESSION['sess_signin'] = '';
        if (isset($_SESSION['sess_iAdminUserId']) && !empty($_SESSION['sess_iAdminUserId'])) {
            global $obj;
            $iAdminUserId = $_SESSION['sess_iAdminUserId'];
            $sql = "select vCode from language_master where eDefault='Yes'";
            $db_lbl = $obj->MySQLSelect($sql);
            $_SESSION['sess_lang'] = $db_lbl[0]['vCode'];
            $cmp_ssql = '';
            $sql = 'SELECT COUNT(iAdminId) AS Total,eStatus FROM administrators WHERE iAdminId='.$iAdminUserId;
            $data = $obj->MySQLSelect($sql);
            $checkadmin = $data[0]['Total'];
            $eStatus = $data[0]['eStatus'];
            if ('Deleted' === $eStatus) {
                $checkadmin = 0;
            } elseif ('Inactive' === $eStatus) {
                $checkadmin = 0;
            } else {
                $checkadmin = 1;
                $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 'xmlhttprequest' === strtolower($_SERVER['HTTP_X_REQUESTED_WITH']);
                if (!$isAjax) {
                    $_SESSION['login_redirect_url'] = $_SERVER['REQUEST_URI'];
                }
            }
            if ($checkadmin <= 0) {
                $_SESSION['sess_iAdminUserId'] = '';
                $_SESSION['sess_vAdminFirstName'] = '';
                $_SESSION['sess_vAdminLastName'] = '';
                $_SESSION['sess_vAdminEmail'] = '';
                $_SESSION['current_link'] = '';
                unset($_SESSION['OrderDetails'], $_SESSION['sess_iServiceId_mr'], $_SESSION['sess_iUserId_mr'], $_SESSION['sess_iUserAddressId_mr'], $_SESSION['sess_promoCode'], $_SESSION['sess_vCurrency_mr'], $_SESSION['sess_currentpage_url_mr'], $_SESSION['sess_vLatitude_mr'], $_SESSION['sess_vLongitude_mr'], $_SESSION['sess_vServiceAddress_mr'], $_SESSION['sess_vName_mr'], $_SESSION['sess_company_mr'], $_SESSION['sess_vEmail_mr'], $_SESSION['sess_user_mr'], $_SESSION['sess_userby_mr'], $_SESSION['sess_userby_id']);

                if ('Deleted' === $eStatus) {
                    $_SESSION['checkadminmsg'] = 'Your account has been deleted.Please contact administrator to activate your account.';
                } else {
                    $_SESSION['checkadminmsg'] = 'Your account has been disabled.Please contact administrator to activate your account.';
                }
                if ('hotel' === $_SESSION['SessionUserType']) {
                    $_SESSION['SessionUserType'] = '';
                    header('location:../hotel');
                } else {
                    header('location:index.php');
                }
            }
            $this->checkAuthAdmin();
        }
    }

    public function checkAuthAdmin(): void
    {
        global $THEME_OBJ;
        $url = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
        if ('Yes' === $THEME_OBJ->isRideCXThemeActive() || 'Yes' === $THEME_OBJ->isRideDeliveryXThemeActive() || 'Yes' === $THEME_OBJ->isRideCXv2ThemeActive()) {
            if ((true === strpos($url, 'driver_service_request.php')) || (true === strpos($url, 'user-order-information')) || (true === strpos($url, 'store_vehicle_type.php'))) {
                header('location:index.php');

                exit;
            }
        }
    }

    public function checkMemberAuthentication(): void
    {
        global $tconfig, $obj, $langage_lbl;
        $sess_iUserId = $_SESSION['sess_iUserId'] ?? '';
        $sess_iAdminUserId = $_SESSION['sess_iAdminUserId'] ?? '';
        if ('' === $sess_iUserId && '' === $sess_iAdminUserId && 'login.php' !== basename($_SERVER['PHP_SELF'])) {
            if (isset($_SERVER['REQUEST_URI'])) {
                setcookie('login_redirect_url_user', $_SERVER['REQUEST_URI'], time() + 2 * 24 * 60 * 60);
            }
            header('Location:'.$tconfig['tsite_url'].'sign-in.php');
        } else {
            $SESSION_MEMBER = '';
            if (!empty($_SESSION['sess_user']) && 'rider' === $_SESSION['sess_user'] && !empty($_SESSION['sess_iUserId'])) {
                $SESSION_MEMBER = 'User';
            } elseif (!empty($_SESSION['sess_user']) && 'driver' === $_SESSION['sess_user'] && !empty($_SESSION['sess_iUserId'])) {
                $SESSION_MEMBER = 'Driver';
            } elseif (!empty($_SESSION['sess_user']) && 'company' === $_SESSION['sess_user'] && !empty($_SESSION['sess_iCompanyId'])) {
                $SESSION_MEMBER = 'Company';
            } elseif (!empty($_SESSION['sess_user']) && 'organization' === $_SESSION['sess_user'] && !empty($_SESSION['sess_iOrganizationId'])) {
                $SESSION_MEMBER = 'Organization';
            } elseif (!empty($_SESSION['sess_user']) && 'tracking_company' === $_SESSION['sess_user'] && !empty($_SESSION['sess_iTrackServiceCompanyId'])) {
                $SESSION_MEMBER = 'TrackingCompany';
            } else {
                $SESSION_MEMBER = 'Admin';
            }

            switch ($SESSION_MEMBER) {
                case 'User':
                    $sess_iUserId = $_SESSION['sess_iUserId'];
                    $sql = "SELECT COUNT(iUserId) AS Total,eStatus FROM register_user WHERE eStatus != 'Deleted' AND eStatus != 'Inactive' AND iUserId=".$sess_iUserId;
                    $data = $obj->MySQLSelect($sql);
                    $checkuser = $data[0]['Total'];
                    $eStatusUser = $data[0]['eStatus'];
                    if ($checkuser <= 0) {
                        session_start();
                        unset($_SESSION['sess_iUserId'], $_SESSION['sess_iCompanyId'], $_SESSION['sess_vName'], $_SESSION['sess_vEmail'], $_SESSION['sess_user'], $_SESSION['sess_iMemberId'], $_SESSION['sess_eGender'], $_SESSION['sess_vImage'], $_SESSION['fb_user'], $_SESSION['linkedin_user'], $_SESSION['oauth_access_token'], $_SESSION['oauth_verifier'], $_SESSION['requestToken'], $_SESSION['sess_currentpage_url_ub']);

                        $_SESSION['sess_currentpage_url_ub'] = '';
                        unset($_SESSION['sess_iServiceId_mr'], $_SESSION['sess_iUserId_mr'], $_SESSION['sess_iUserAddressId_mr'], $_SESSION['sess_promoCode'], $_SESSION['sess_vCurrency_mr'], $_SESSION['sess_currentpage_url_mr'], $_SESSION['sess_vLatitude_mr'], $_SESSION['sess_vLongitude_mr'], $_SESSION['sess_vServiceAddress_mr'], $_SESSION['sess_vName_mr'], $_SESSION['sess_company_mr'], $_SESSION['sess_vEmail_mr'], $_SESSION['sess_user_mr'], $_SESSION['sess_userby_mr'], $_SESSION['sess_userby_id']);

                        if (isset($_SERVER['HTTP_COOKIE'])) {
                            $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
                            foreach ($cookies as $cookie) {
                                $parts = explode('=', $cookie);
                                $name = trim($parts[0]);
                                setcookie($name, '', time() - 1_000);
                                setcookie($name, '', time() - 1_000, ' /');
                            }
                        }
                        session_destroy();
                        if ('Deleted' === $eStatusUser) {
                            $_SESSION['checkadminmsg'] = $langage_lbl['LBL_ACCOUNT_NOT_ACTIVE_ERROR_MSG'];
                        } else {
                            $_SESSION['checkadminmsg'] = $langage_lbl['LBL_ACCOUNT_NOT_ACTIVE_ERROR_MSG'];
                        }
                        if (isset($_REQUEST['depart']) && 'mobi' === $_REQUEST['depart']) {
                            header('Location:mobi');
                        } else {
                            $url = $tconfig['tsite_url'].'user-login';
                            header("Location:{$url}");
                        }

                        exit;
                    }

                    break;

                case 'Driver':
                    $sess_iDriverId = $_SESSION['sess_iUserId'];
                    $sql = "SELECT COUNT(iDriverId) AS Total,eStatus FROM register_driver WHERE eStatus != 'Deleted' AND eStatus != 'Suspend' AND iDriverId =".$sess_iDriverId;
                    $data = $obj->MySQLSelect($sql);
                    $checkdriver = $data[0]['Total'];
                    $eStatusdriver = $data[0]['eStatus'];
                    if ($checkdriver <= 0) {
                        session_start();
                        unset($_SESSION['sess_iUserId'], $_SESSION['sess_iCompanyId'], $_SESSION['sess_vName'], $_SESSION['sess_vEmail'], $_SESSION['sess_user'], $_SESSION['sess_iMemberId'], $_SESSION['sess_eGender'], $_SESSION['sess_vImage'], $_SESSION['fb_user'], $_SESSION['linkedin_user'], $_SESSION['oauth_access_token'], $_SESSION['oauth_verifier'], $_SESSION['requestToken'], $_SESSION['sess_currentpage_url_ub']);

                        $_SESSION['sess_currentpage_url_ub'] = '';
                        unset($_SESSION['sess_iServiceId_mr'], $_SESSION['sess_iUserId_mr'], $_SESSION['sess_iUserAddressId_mr'], $_SESSION['sess_promoCode'], $_SESSION['sess_vCurrency_mr'], $_SESSION['sess_currentpage_url_mr'], $_SESSION['sess_vLatitude_mr'], $_SESSION['sess_vLongitude_mr'], $_SESSION['sess_vServiceAddress_mr'], $_SESSION['sess_vName_mr'], $_SESSION['sess_company_mr'], $_SESSION['sess_vEmail_mr'], $_SESSION['sess_user_mr'], $_SESSION['sess_userby_mr'], $_SESSION['sess_userby_id']);

                        if (isset($_SERVER['HTTP_COOKIE'])) {
                            $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
                            foreach ($cookies as $cookie) {
                                $parts = explode('=', $cookie);
                                $name = trim($parts[0]);
                                setcookie($name, '', time() - 1_000);
                                setcookie($name, '', time() - 1_000, ' /');
                            }
                        }
                        session_destroy();
                        if ('Deleted' === $eStatusdriver) {
                            $_SESSION['checkadminmsg'] = $langage_lbl['LBL_ACCOUNT_NOT_ACTIVE_ERROR_MSG'];
                        } elseif ('Suspend' === $eStatusdriver) {
                            $_SESSION['checkadminmsg'] = $langage_lbl['LBL_ACCOUNT_NOT_ACTIVE_ERROR_MSG'];
                        } else {
                            $_SESSION['checkadminmsg'] = $langage_lbl['LBL_ACCOUNT_NOT_ACTIVE_ERROR_MSG'];
                        }
                        if (isset($_REQUEST['depart']) && 'mobi' === $_REQUEST['depart']) {
                            header('Location:mobi');
                        } else {
                            $url = $tconfig['tsite_url'].'provider-login';
                            header("Location:{$url}");
                        }

                        exit;
                    }

                    break;

                case 'Company':
                    $sess_iCompanyId = $_SESSION['sess_iCompanyId'];
                    $sql = "SELECT COUNT(iCompanyId) AS Total,eStatus FROM company WHERE eStatus != 'Deleted' AND iCompanyId=".$sess_iCompanyId;
                    $data = $obj->MySQLSelect($sql);
                    $checkcompany = $data[0]['Total'];
                    $eStatuscompany = $data[0]['eStatus'];
                    if ($checkcompany <= 0) {
                        session_start();
                        unset($_SESSION['sess_iUserId'], $_SESSION['sess_iCompanyId'], $_SESSION['sess_vName'], $_SESSION['sess_vEmail'], $_SESSION['sess_user'], $_SESSION['sess_iMemberId'], $_SESSION['sess_eGender'], $_SESSION['sess_vImage'], $_SESSION['fb_user'], $_SESSION['linkedin_user'], $_SESSION['oauth_access_token'], $_SESSION['oauth_verifier'], $_SESSION['requestToken'], $_SESSION['sess_currentpage_url_ub']);

                        $_SESSION['sess_currentpage_url_ub'] = '';
                        unset($_SESSION['sess_iServiceId_mr'], $_SESSION['sess_iUserId_mr'], $_SESSION['sess_iUserAddressId_mr'], $_SESSION['sess_promoCode'], $_SESSION['sess_vCurrency_mr'], $_SESSION['sess_currentpage_url_mr'], $_SESSION['sess_vLatitude_mr'], $_SESSION['sess_vLongitude_mr'], $_SESSION['sess_vServiceAddress_mr'], $_SESSION['sess_vName_mr'], $_SESSION['sess_company_mr'], $_SESSION['sess_vEmail_mr'], $_SESSION['sess_user_mr'], $_SESSION['sess_userby_mr'], $_SESSION['sess_userby_id']);

                        if (isset($_SERVER['HTTP_COOKIE'])) {
                            $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
                            foreach ($cookies as $cookie) {
                                $parts = explode('=', $cookie);
                                $name = trim($parts[0]);
                                setcookie($name, '', time() - 1_000);
                                setcookie($name, '', time() - 1_000, ' /');
                            }
                        }
                        session_destroy();
                        if ('Deleted' === $eStatusdriver) {
                            $_SESSION['checkadminmsg'] = $langage_lbl['LBL_ACCOUNT_NOT_ACTIVE_ERROR_MSG'];
                        } else {
                            $_SESSION['checkadminmsg'] = $langage_lbl['LBL_ACCOUNT_NOT_ACTIVE_ERROR_MSG'];
                        }
                        if (isset($_REQUEST['depart']) && 'mobi' === $_REQUEST['depart']) {
                            header('Location:mobi');
                        } else {
                            $url = $tconfig['tsite_url'].'company-login';
                            header("Location:{$url}");
                        }

                        exit;
                    }

                    break;

                case 'Organization':
                    $sess_iOrganizationId = $_SESSION['sess_iOrganizationId'];
                    $sql = "SELECT COUNT(iOrganizationId) AS Total,eStatus FROM organization WHERE eStatus != 'Deleted' AND iOrganizationId=".$sess_iOrganizationId;
                    $data = $obj->MySQLSelect($sql);
                    $checkorganization = $data[0]['Total'];
                    $eStatusorganization = $data[0]['eStatus'];
                    if ($checkorganization <= 0) {
                        session_start();
                        unset($_SESSION['sess_iUserId'], $_SESSION['sess_iCompanyId'], $_SESSION['sess_vName'], $_SESSION['sess_vEmail'], $_SESSION['sess_user'], $_SESSION['sess_iMemberId'], $_SESSION['sess_eGender'], $_SESSION['sess_vImage'], $_SESSION['fb_user'], $_SESSION['linkedin_user'], $_SESSION['oauth_access_token'], $_SESSION['oauth_verifier'], $_SESSION['requestToken'], $_SESSION['sess_currentpage_url_ub']);

                        $_SESSION['sess_currentpage_url_ub'] = '';
                        unset($_SESSION['sess_iServiceId_mr'], $_SESSION['sess_iUserId_mr'], $_SESSION['sess_iUserAddressId_mr'], $_SESSION['sess_promoCode'], $_SESSION['sess_vCurrency_mr'], $_SESSION['sess_currentpage_url_mr'], $_SESSION['sess_vLatitude_mr'], $_SESSION['sess_vLongitude_mr'], $_SESSION['sess_vServiceAddress_mr'], $_SESSION['sess_vName_mr'], $_SESSION['sess_company_mr'], $_SESSION['sess_vEmail_mr'], $_SESSION['sess_user_mr'], $_SESSION['sess_userby_mr'], $_SESSION['sess_userby_id']);

                        if (isset($_SERVER['HTTP_COOKIE'])) {
                            $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
                            foreach ($cookies as $cookie) {
                                $parts = explode('=', $cookie);
                                $name = trim($parts[0]);
                                setcookie($name, '', time() - 1_000);
                                setcookie($name, '', time() - 1_000, ' /');
                            }
                        }
                        session_destroy();
                        if ('Deleted' === $eStatusdriver) {
                            $_SESSION['checkadminmsg'] = $langage_lbl['LBL_ACCOUNT_NOT_ACTIVE_ERROR_MSG'];
                        } else {
                            $_SESSION['checkadminmsg'] = $langage_lbl['LBL_ACCOUNT_NOT_ACTIVE_ERROR_MSG'];
                        }
                        if (isset($_REQUEST['depart']) && 'mobi' === $_REQUEST['depart']) {
                            header('Location:mobi');
                        } else {
                            $url = $tconfig['tsite_url'].'organization-login';
                            header("Location:{$url}");
                        }

                        exit;
                    }

                    break;

                case 'TrackingCompany':
                    $sess_iTrackServiceCompanyId = $_SESSION['sess_iTrackServiceCompanyId'];
                    $sql = "SELECT COUNT(iTrackServiceCompanyId) AS Total,eStatus FROM track_service_company WHERE eStatus != 'Deleted' AND iTrackServiceCompanyId=".$sess_iTrackServiceCompanyId;
                    $data = $obj->MySQLSelect($sql);
                    $checkorganization = $data[0]['Total'];
                    $eStatusorganization = $data[0]['eStatus'];
                    if ($checkorganization <= 0) {
                        session_start();
                        unset($_SESSION['sess_iUserId'], $_SESSION['sess_iCompanyId'], $_SESSION['sess_vName'], $_SESSION['sess_vEmail'], $_SESSION['sess_user'], $_SESSION['sess_iMemberId'], $_SESSION['sess_eGender'], $_SESSION['sess_vImage'], $_SESSION['fb_user'], $_SESSION['linkedin_user'], $_SESSION['oauth_access_token'], $_SESSION['oauth_verifier'], $_SESSION['requestToken'], $_SESSION['sess_currentpage_url_ub']);

                        $_SESSION['sess_currentpage_url_ub'] = '';
                        unset($_SESSION['sess_iServiceId_mr'], $_SESSION['sess_iUserId_mr'], $_SESSION['sess_iUserAddressId_mr'], $_SESSION['sess_promoCode'], $_SESSION['sess_vCurrency_mr'], $_SESSION['sess_currentpage_url_mr'], $_SESSION['sess_vLatitude_mr'], $_SESSION['sess_vLongitude_mr'], $_SESSION['sess_vServiceAddress_mr'], $_SESSION['sess_vName_mr'], $_SESSION['sess_company_mr'], $_SESSION['sess_vEmail_mr'], $_SESSION['sess_user_mr'], $_SESSION['sess_userby_mr'], $_SESSION['sess_userby_id']);

                        if (isset($_SERVER['HTTP_COOKIE'])) {
                            $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
                            foreach ($cookies as $cookie) {
                                $parts = explode('=', $cookie);
                                $name = trim($parts[0]);
                                setcookie($name, '', time() - 1_000);
                                setcookie($name, '', time() - 1_000, ' /');
                            }
                        }
                        session_destroy();
                        if ('Deleted' === $eStatusdriver) {
                            $_SESSION['checkadminmsg'] = $langage_lbl['LBL_ACCOUNT_NOT_ACTIVE_ERROR_MSG'];
                        } else {
                            $_SESSION['checkadminmsg'] = $langage_lbl['LBL_ACCOUNT_NOT_ACTIVE_ERROR_MSG'];
                        }
                        if (isset($_REQUEST['depart']) && 'mobi' === $_REQUEST['depart']) {
                            header('Location:mobi');
                        } else {
                            $url = $tconfig['tsite_url'].'organization-login';
                            header("Location:{$url}");
                        }

                        exit;
                    }

                    break;

                case 'Admin':
                default:
                    break;
            }
        }
    }

    public function checkManualTaxiMemberAuthentication(): void
    {
        global $tconfig, $_REQUEST;
        $userType = $_REQUEST['userType1'] ?? '';
        if (isset($userType) && !empty($userType)) {
            $redirect_file_name = 'sign-in.php';
            if ('company' === $userType) {
                $redirect_file_name = 'company-login';
            } elseif ('rider' === $userType) {
                $redirect_file_name = 'user-login';
            }
        }
        $sess_iUserId = $_SESSION['sess_iUserId'] ?? '';
        if ('' === $sess_iUserId && 'login.php' !== basename($_SERVER['PHP_SELF'])) {
            header('Location:'.$tconfig['tsite_url'].$redirect_file_name);
        }
    }

    public function AuthMemberRedirect(): void
    {
        global $tconfig;
        $sess_iUserId = $_SESSION['sess_iUserId'] ?? '';
        $sess_user = $_SESSION['sess_user'] ?? '';
        $url = '';
        if ('' !== $sess_iUserId && '' !== $sess_user) {
            switch ($sess_user) {
                case 'driver':
                    $url = 'profile.php';

                    break;

                case 'rider':
                    $url = 'profile_rider.php';

                    break;

                case 'company':
                    $url = 'profile.php';

                    break;

                case 'organization':
                    $url = 'organization-profile';

                    break;

                default:
                    $url = 'index.php';

                    break;
            }
        }
        if ('' !== $url && basename($_SERVER['PHP_SELF']) !== $url) {
            header('Location:'.$url);
        }
    }

    public function AuthAdminRedirect(): void
    {
        global $tconfig;
        $sess_iAdminUserId = $_SESSION['sess_iAdminUserId'] ?? '';
        $sess_iGroupId = $_SESSION['sess_iGroupId'] ?? '';
        if ('4' === $sess_iGroupId) {
            if ('' !== $sess_iAdminUserId) {
                $url = $tconfig['tsite_url_main_admin'].'create_request.php';
            }
        } else {
            if ('' !== $sess_iAdminUserId) {
                $url = $tconfig['tsite_url_main_admin'].'dashboard.php';
            }
        }
        if (isset($url) && '' !== $url && basename($_SERVER['PHP_SELF']) !== $url) {
            echo '<script>window.location="'.$url.'";</script>';
            @header('Location:'.$url);

            exit;
        }
    }

    public function VerifyPassword($pass, $hash)
    {
        if (password_verify($pass, $hash)) {
            $test = 1;
        } else {
            $test = 0;
        }

        return $test;
    }
}
