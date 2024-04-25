<?php



include_once '../../common.php';

$reload = $_SERVER['REQUEST_URI'];
$urlparts = explode('?', $reload);
$parameters = $urlparts[1];
$id = $_REQUEST['id'] ?? '';
$iCountryId = $_REQUEST['iCountryId'] ?? '';
$status = $_REQUEST['status'] ?? '';
$statusVal = $_REQUEST['statusVal'] ?? '';
$action = $_REQUEST['action'] ?? 'view';
$hdn_del_id = $_REQUEST['hdn_del_id'] ?? '';
$checkbox = isset($_REQUEST['checkbox']) ? implode(',', $_REQUEST['checkbox']) : '';
$method = $_REQUEST['method'] ?? '';
$keyword = $_REQUEST['keyword'] ?? '';
// print_R($_REQUEST);die;
$oCache->flushData();
// Added By HJ On 17-07-2019 For Check Default Country Selected Validation When Inactive Or Delete Start
if ('Inactive' === $status || 'Inactive' === $method || 'Inactive' === $statusVal || 'Deleted' === $statusVal || 'delete' === $method) {
    if ('' !== $iCountryId) {
        $countryIds = $iCountryId;
    } else {
        $countryIds = $checkbox;
    }

    $checkCountry = $obj->MySQLSelect('SELECT iCountryId,vCountryCode FROM country WHERE iCountryId IN ('.$countryIds.')');
    for ($c = 0; $c < count($checkCountry); ++$c) {
        // print_r($checkCountry);die;
        $vCountryCode = '';
        $vCountryCode = trim($checkCountry[$c]['vCountryCode']);
        if (trim($DEFAULT_COUNTRY_CODE_WEB) === trim($vCountryCode) && '' !== trim($DEFAULT_COUNTRY_CODE_WEB) && '' !== $vCountryCode) {
            $_SESSION['success'] = 3;
            $cName = 'This';
            if ('' !== $keyword) {
                $cName = $keyword;
            }
            $_SESSION['var_msg'] = $cName.' country selected as a default. Please change it first.';
            header('Location:'.$tconfig['tsite_url_main_admin'].'country.php?'.$parameters);

            exit;
            // echo "<pre>";print_r($vCountryCode);die;
        }
    }
}
// Added By HJ On 17-07-2019 For Check Default Country Selected Validation When Inactive Or Delete End
// Start country deleted
if (('Deleted' === $statusVal || 'delete' === $method) && ('' !== $iCountryId || '' !== $checkbox)) {
    if (!$userObj->hasPermission('delete-country')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to delete country';
    } else {
        // Added By Hasmukh On 05-10-2018 For Solved Bug Start
        if ('' !== $iCountryId) {
            $countryIds = $iCountryId;
        } else {
            $countryIds = $checkbox;
        }
        // Added By Hasmukh On 05-10-2018 For Solved Bug End
        if (SITE_TYPE !== 'Demo') {
            $query = "UPDATE country SET eStatus = 'Deleted' WHERE iCountryId IN (".$countryIds.')';
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_DELETE_MSG'];
        } else {
            $_SESSION['success'] = '2';
        }
    }

    $GCS_OBJ->updateGCSData();
    header('Location:'.$tconfig['tsite_url_main_admin'].'country.php?'.$parameters);

    exit;
}
// End country deleted
// Start Change single Status
if ('' !== $iCountryId && '' !== $status) {
    if (!$userObj->hasPermission('update-status-country')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to change status of country';
    } else {
        if (SITE_TYPE !== 'Demo') {
            $query = "UPDATE country SET eStatus = '".$status."' WHERE iCountryId = '".$iCountryId."'";
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            if ('Active' === $status) {
                $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_ACTIVATE_MSG'];
            } else {
                $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_INACTIVATE_MSG'];
            }
        } else {
            $_SESSION['success'] = 2;
        }
    }

    $GCS_OBJ->updateGCSData();
    header('Location:'.$tconfig['tsite_url_main_admin'].'country.php?'.$parameters);

    exit;
}
// End Change single Status
// Start Change All Selected Status
if ('' !== $checkbox && '' !== $statusVal) {
    if (!$userObj->hasPermission('update-status-country')) {
        $_SESSION['success'] = 3;
        $_SESSION['var_msg'] = 'You do not have permission to change status of country';
    } else {
        if (SITE_TYPE !== 'Demo') {
            $query = "UPDATE country SET eStatus = '".$statusVal."' WHERE iCountryId IN (".$checkbox.')';
            $obj->sql_query($query);
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = $langage_lbl_admin['LBL_Record_Updated_successfully'];
        } else {
            $_SESSION['success'] = 2;
        }
    }

    $GCS_OBJ->updateGCSData();
    header('Location:'.$tconfig['tsite_url_main_admin'].'country.php?'.$parameters);

    exit;
}
