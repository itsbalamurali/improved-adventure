<?php



$myearnigMenuHide = 1; // 0- Hide,1- Show
if (isset($_SESSION['sess_user']) && 'DRIVER' === strtoupper($_SESSION['sess_user'])) {
    $driverStoreId = 0;
    if (isset($_SESSION['sess_iCompanyId']) && $_SESSION['sess_iCompanyId'] > 0) {
        $driverStoreId = $_SESSION['sess_iCompanyId'];
    } elseif (isset($_SESSION['sess_iUserId']) && $_SESSION['sess_iUserId'] > 0) {
        $storeDriverId = $_SESSION['sess_iUserId'];
        $getDriver = $obj->MySQLSelect("SELECT iCompanyId FROM register_driver WHERE iDriverId = '".$storeDriverId."'");
        if (count($getDriver) > 0) {
            $driverStoreId = $getDriver[0]['iCompanyId'];
        }
    }
    if ($driverStoreId > 0) {
        $getStore = $obj->MySQLSelect("SELECT eSystem FROM company WHERE iCompanyId = '".$driverStoreId."'");
        if (isset($getStore[0]['eSystem']) && 'DELIVERALL' === strtoupper($getStore[0]['eSystem'])) {
            $myearnigMenuHide = 0; // 0- Hide,1- Show
        }
    }
}

// Added By HJ On 30-04-2020 As Per Discuss With KS For Hide My earning Left Menu When eSystem = Deliverall End
include $tconfig['tpanel_path'].$templatePath.'top/left_menu.php';
