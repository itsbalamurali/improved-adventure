<?php



include_once '../../common.php';
$iServiceId = $_REQUEST['iVehicleCategoryId'] ?? 0;
$iParentId = $_REQUEST['menuid'] ?? 0;
$status = $_REQUEST['status'] ?? 0;
$iDisplayOrder = $_REQUEST['iDisplayOrder'] ?? 0;
$oldDisplayOrder = $_REQUEST['oldDisplayOrder'] ?? 0;
$eType = $_REQUEST['eType'] ?? '';
$oldDisplayOrder = 2;
$reload = $_SERVER['REQUEST_URI'];
$urlparts = explode('?', $reload);
$parameters = $urlparts[1];
$db_master = $obj->MySQLSelect('SELECT * FROM `language_master` ORDER BY `iDispOrder`');
$count_all = count($db_master);
for ($i = 0; $i < count($db_master); ++$i) {
    $Title = '';
    if (isset($_POST['vTitle_'.$iServiceId.$db_master[$i]['vCode']])) {
        $Title = $_POST['vTitle_'.$iServiceId.$db_master[$i]['vCode']];
    }
    $TitleArr['vTitle_'.$db_master[$i]['vCode']] = $Title;
}
$jsonTitle = getJsonFromAnArr($TitleArr);
$sql = "SELECT *  FROM `master_service_menu` WHERE iParentId = '".$iParentId."' AND iServiceId = '".$iServiceId."'";
$master_service_menu = $obj->MySQLSelect($sql);
if (1 === $_REQUEST['ajax']) {
    if (isset($iServiceId) && !empty($iParentId)) {
        $i = $iDisplayOrder;
        $temp_order = $_REQUEST['oldDisplayOrder'];
        $where = 'iParentId = '.$iParentId.'';

        if ('MedicalServices' !== $eType) {
            if ($temp_order > $iDisplayOrder) {
                for ($i = $temp_order - 1; $i >= $iDisplayOrder; --$i) {
                    $obj->sql_query("UPDATE master_service_menu SET iDisplayOrder = '".($i + 1)."' WHERE iDisplayOrder = '".$i."' AND {$where}");
                }
            } elseif ($temp_order < $iDisplayOrder) {
                for ($i = $temp_order + 1; $i <= $iDisplayOrder; ++$i) {
                    $obj->sql_query("UPDATE master_service_menu SET iDisplayOrder = '".($i - 1)."' WHERE iDisplayOrder = '".$i."' AND {$where}");
                }
            }
        }
        if (count($master_service_menu) > 0) {
            $Data['iDisplayOrder'] = $iDisplayOrder;
            $Data['vTitle'] = $jsonTitle;
            $where = 'iParentId = '.$iParentId." AND iServiceId = '".$iServiceId."'";
            $id = $obj->MySQLQueryPerform('master_service_menu', $Data, 'update', $where);
        } else {
            $sql = "SELECT MAX(iDisplayOrder) as count  FROM `master_service_menu` WHERE iParentId = '".$iParentId."'";
            $count = $obj->MySQLSelect($sql);
            $data_version['iParentId'] = $iParentId;
            $data_version['iServiceId'] = $iServiceId;
            $data_version['eStatus'] = $eStatus;
            $data_version['eType'] = 'services';
            $data_version['vTitle'] = $jsonTitle;
            $data_version['iDisplayOrder'] = $count[0]['count'] + 1;
            $id = $obj->MySQLQueryPerform('master_service_menu', $data_version, 'insert');
        }
        if ('' !== $id) {
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = $langage_lbl_admin['LBL_Record_Updated_successfully'];
        } else {
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_INSERT_MSG'];
        }
    }
} else {
    if (SITE_TYPE === 'Demo') {
        $_SESSION['success'] = '2';
        header('Location:'.$tconfig['tsite_url_main_admin'].'menu_service.php?eType='.$eType.'&id='.$iParentId);

        exit;
    }
    if ('Active' === $status) {
        if (count($master_service_menu) > 0) {
            $Data['eStatus'] = 'Active';
            $where = "iParentId = '".$iParentId."' AND iServiceId = '".$iServiceId."'";
            $id = $obj->MySQLQueryPerform('master_service_menu', $Data, 'update', $where);
        } else {
            $sql = "SELECT MAX(iDisplayOrder) as count  FROM `master_service_menu` WHERE iParentId = '".$iParentId."'";
            $count = $obj->MySQLSelect($sql);
            $data_version['iParentId'] = $iParentId;
            $data_version['iServiceId'] = $iServiceId;
            $data_version['eStatus'] = $eStatus;
            $data_version['eType'] = 'services';
            $data_version['iDisplayOrder'] = $count[0]['count'] + 1;
            $obj->MySQLQueryPerform('master_service_menu', $data_version, 'insert');
        }
    } else {
        $Data['eStatus'] = 'Inactive';
        $where = "iParentId = '".$iParentId."' AND iServiceId = '".$iServiceId."'";
        $id = $obj->MySQLQueryPerform('master_service_menu', $Data, 'update', $where);
    }
    header('Location:'.$tconfig['tsite_url_main_admin'].'menu_service.php?eType='.$eType.'&id='.$iParentId);

    exit;
}
