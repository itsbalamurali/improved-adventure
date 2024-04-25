<?php
include_once '../common.php';

$vCountryCode = $_REQUEST['vCountryCode'] ?? '';
$userType = $_REQUEST['userType'] ?? '';
$checkusedata = $_REQUEST['checkusedata'] ?? '';
// $action = isset($_REQUEST['action'])?$_REQUEST['action']:'Add';

$delsql = " AND eStatus != 'Deleted'";

$alluserdata = [];
$sql = "select concat(vName,' ',vLastName) as DriverName,iDriverId,eDeviceType,eDebugMode from register_driver where (vEmail != '' OR vPhone != '')  AND vCountry='".$vCountryCode."' {$delsql} order by vName";
$db_drvlist = $obj->MySQLSelect($sql);
$db_drv_list = [];

for ($i = 0; $i < count($db_drvlist); ++$i) {
    $data = [];
    $data['DriverName'] = mb_convert_encoding(clearName(ucfirst($db_drvlist[$i]['DriverName'])), 'utf-8', 'auto');
    $data['iDriverId'] = $db_drvlist[$i]['iDriverId'];
    $data['eDeviceType'] = $db_drvlist[$i]['eDeviceType'];
    $data['eDebugMode'] = $db_drvlist[$i]['eDebugMode'];
    $db_drv_list[] = $data;
}

$sql = "select concat(vName,' ',vLastName) as riderName,iUserId,eDeviceType from register_user where (vEmail != '' OR vName != '' OR vPhone != '') AND vCountry='".$vCountryCode."' {$delsql} order by vName";
$db_rdrlist = $obj->MySQLSelect($sql);
$db_rdr_list = [];
for ($ii = 0; $ii < count($db_rdrlist); ++$ii) {
    $data = [];
    $data['riderName'] = mb_convert_encoding(clearName(ucfirst($db_rdrlist[$ii]['riderName'])), 'utf-8', 'auto');
    $data['iUserId'] = $db_rdrlist[$ii]['iUserId'];
    $data['eDeviceType'] = $db_rdrlist[$ii]['eDeviceType'];
    $db_rdr_list[] = $data;
}

$sql_drv = "select concat(vName,' ',vLastName) as DriverName,iDriverId,eDeviceType from register_driver where `eLogout` = 'No' AND (vEmail != '' OR vPhone != '') AND vCountry='".$vCountryCode."' {$delsql} order by vName";
$db_login_drvlist = $obj->MySQLSelect($sql_drv);
$db_login_drv_list = [];
for ($iii = 0; $iii < count($db_login_drvlist); ++$iii) {
    $data = [];
    $data['DriverName'] = mb_convert_encoding(clearName(ucfirst($db_login_drvlist[$iii]['DriverName'])), 'utf-8', 'auto');
    $data['iDriverId'] = $db_login_drvlist[$iii]['iDriverId'];
    $data['eDeviceType'] = $db_login_drvlist[$iii]['eDeviceType'];
    $db_login_drv_list[] = $data;
}

$sql_rdr = "select concat(vName,' ',vLastName) as riderName,iUserId,eDeviceType from register_user where `eLogout` = 'No'  AND (vEmail != '' OR vPhone != '') AND vCountry='".$vCountryCode."' {$delsql} order by vName";
$db_login_rdrlist = $obj->MySQLSelect($sql_rdr);
$db_login_rdr_list = [];
for ($iv = 0; $iv < count($db_login_rdrlist); ++$iv) {
    $data = [];
    $data['riderName'] = mb_convert_encoding(clearName(ucfirst($db_login_rdrlist[$iv]['riderName'])), 'utf-8', 'auto');
    $data['iUserId'] = $db_login_rdrlist[$iv]['iUserId'];
    $data['eDeviceType'] = $db_login_rdrlist[$iv]['eDeviceType'];
    $db_login_rdr_list[] = $data;
}

$sql_inactive_drv = "select concat(vName,' ',vLastName) as DriverName,iDriverId,eDeviceType from register_driver where eStatus = 'Inactive' AND (vEmail != '' OR vPhone != '') AND vCountry='".$vCountryCode."' order by vName";
$db_inactive_drvlist = $obj->MySQLSelect($sql_inactive_drv);

$db_inactive_drv_list = [];
for ($v = 0; $v < count($db_inactive_drvlist); ++$v) {
    $data = [];
    $data['DriverName'] = mb_convert_encoding(clearName(ucfirst($db_inactive_drvlist[$v]['DriverName'])), 'utf-8', 'auto');
    $data['iDriverId'] = $db_inactive_drvlist[$v]['iDriverId'];
    $data['eDeviceType'] = $db_inactive_drvlist[$v]['eDeviceType'];
    $db_inactive_drv_list[] = $data;
}

$sql_inactive_rdr = "select concat(vName,' ',vLastName) as riderName,iUserId,eDeviceType from register_user where eStatus = 'Inactive' AND (vEmail != '' OR vPhone != '') AND vCountry='".$vCountryCode."' order by vName";
$db_inactive_rdrlist = $obj->MySQLSelect($sql_inactive_rdr);
$db_inactive_rdr_list = [];
for ($vi = 0; $vi < count($db_inactive_rdrlist); ++$vi) {
    $data = [];
    $data['riderName'] = mb_convert_encoding(clearName(ucfirst($db_inactive_rdrlist[$vi]['riderName'])), 'utf-8', 'auto');
    $data['iUserId'] = $db_inactive_rdrlist[$vi]['iUserId'];
    $data['eDeviceType'] = $db_inactive_rdrlist[$vi]['eDeviceType'];
    $db_inactive_rdr_list[] = $data;
}

$sql = "SELECT c.iCompanyId, c.vCompany,c.eDeviceType FROM company AS c  left join service_categories as sc on c.iServiceId = sc.iServiceId WHERE  c.eStatus = 'Active' AND sc.eStatus='Active' AND  c.iServiceId > 0  AND c.vCountry='".$vCountryCode."' order by c.vCompany";
$db_storelist = $obj->MySQLSelect($sql);
$db_store_list = [];
for ($vii = 0; $vii < count($db_storelist); ++$vii) {
    $data = [];
    $data['vCompany'] = mb_convert_encoding(clearCmpName(ucfirst($db_storelist[$vii]['vCompany'])), 'utf-8', 'auto');
    $data['iCompanyId'] = $db_storelist[$vii]['iCompanyId'];
    $data['eDeviceType'] = $db_storelist[$vii]['eDeviceType'];
    $db_store_list[] = $data;
}

$sql = "SELECT c.iCompanyId, c.vCompany,c.eDeviceType FROM company AS c  left join service_categories as sc on c.iServiceId = sc.iServiceId WHERE  c.eStatus = 'Active' AND sc.eStatus='Active' AND c.eLogout = 'No'AND  c.iServiceId>0  AND c.vCountry='".$vCountryCode."' order by c.vCompany";
$db_login_rstlist = $obj->MySQLSelect($sql);
$db_login_rst_list = [];
for ($ix = 0; $ix < count($db_login_rstlist); ++$ix) {
    $data = [];
    $data['vCompany'] = mb_convert_encoding(clearCmpName(ucfirst($db_login_rstlist[$ix]['vCompany'])), 'utf-8', 'auto');
    $data['iCompanyId'] = $db_login_rstlist[$ix]['iCompanyId'];
    $data['eDeviceType'] = $db_login_rstlist[$ix]['eDeviceType'];
    $db_login_rst_list[] = $data;
}

$sql = "SELECT c.iCompanyId, c.vCompany,c.eDeviceType FROM company AS c  left join service_categories as sc on c.iServiceId = sc.iServiceId WHERE  c.eStatus = 'Inactive' AND sc.eStatus='Active' AND  c.eStatus = 'Inactive' AND  c.iServiceId>0  AND c.vCountry='".$vCountryCode."' order by c.vCompany";
$db_inactive_rstlist = $obj->MySQLSelect($sql);
$db_inactive_rst_list = [];
for ($x = 0; $x < count($db_inactive_rstlist); ++$x) {
    $data = [];
    $data['vCompany'] = mb_convert_encoding(clearCmpName(ucfirst($db_inactive_rstlist[$x]['vCompany'])), 'utf-8', 'auto');
    $data['iCompanyId'] = $db_inactive_rstlist[$x]['iCompanyId'];
    $data['eDeviceType'] = $db_inactive_rstlist[$x]['eDeviceType'];
    $db_inactive_rst_list[] = $data;
}

if ('Yes' !== $checkusedata) {
    if ('driver' === $userType) {
        $alluserdata['driverlist'] = $db_drv_list;
    } elseif ('rider' === $userType) {
        $alluserdata['userlist'] = $db_rdr_list;
    } elseif ('logged_driver' === $userType) {
        $alluserdata['loggedindriverlist'] = $db_login_drv_list;
    } elseif ('logged_rider' === $userType) {
        $alluserdata['loggedinriderlist'] = $db_login_rdr_list;
    } elseif ('inactive_driver' === $userType) {
        $alluserdata['inactivedriverlist'] = $db_inactive_drv_list;
    } elseif ('inactive_rider' === $userType) {
        $alluserdata['inactiveuserlist'] = $db_inactive_rdr_list;
    } elseif ('store' === $userType) {
        $alluserdata['storelist'] = $db_store_list;
    } elseif ('logged_store' === $userType) {
        $alluserdata['loginstorelist'] = $db_login_rst_list;
    } elseif ('inactive_store' === $userType) {
        $alluserdata['inactivestorelist'] = $db_inactive_rst_list;
    }
    // returns data as JSON format
    echo json_encode($alluserdata, JSON_UNESCAPED_UNICODE);

    exit;
}
?>
<?php

if ('Yes' === $checkusedata) {
    // if(empty($db_drv_list) && empty($db_rdr_list) && empty($db_login_drv_list) && empty($db_login_rdr_list) && empty($db_inactive_drv_list) && empty($db_inactive_rdr_list) && empty($db_store_list) && empty($db_login_rst_list) && empty($db_inactive_rst_list)){
    echo '<option value="">Select Type</option>';
    // }
    if (!empty($db_drv_list)) {
        echo '<option value="driver">All '.$langage_lbl_admin['LBL_DRIVERS_NAME_ADMIN'].'</option>';
    }
    if (!empty($db_rdr_list)) {
        echo '<option value="rider">All '.$langage_lbl_admin['LBL_RIDERS_ADMIN'].'</option>';
    }
    if (!empty($db_login_drv_list)) {
        echo '<option value="logged_driver">All Logged in '.$langage_lbl_admin['LBL_DRIVERS_NAME_ADMIN'].'</option>';
    }
    if (!empty($db_login_rdr_list)) {
        echo '<option value="logged_rider">All Logged in '.$langage_lbl_admin['LBL_RIDERS_ADMIN'].'</option>';
    }
    if (!empty($db_inactive_drv_list)) {
        echo '<option value="inactive_driver">All Inactive '.$langage_lbl_admin['LBL_DRIVERS_NAME_ADMIN'].'</option>';
    }
    if (!empty($db_inactive_rdr_list)) {
        echo '<option value="inactive_rider">All Inactive '.$langage_lbl_admin['LBL_RIDERS_ADMIN'].'</option>';
    }
    if (DELIVERALL === 'Yes') {
        if (!empty($db_store_list)) {
            echo '<option value="store">All '.$langage_lbl_admin['LBL_RESTAURANT_TXT_ADMIN'].'</option>';
        }
        if (!empty($db_login_rst_list)) {
            echo '<option value="logged_store">All Logged in '.$langage_lbl_admin['LBL_RESTAURANT_TXT_ADMIN'].'</option>';
        }
        if (!empty($db_inactive_rst_list)) {
            echo '<option value="inactive_store">All Inactive '.$langage_lbl_admin['LBL_RESTAURANT_TXT_ADMIN'].'</option>';
        }
    }

    exit;
}
?>