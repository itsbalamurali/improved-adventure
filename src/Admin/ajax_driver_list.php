<?php



include_once '../common.php';

$companyId = $_REQUEST['id'] ?? '';
$iDriverId = $_REQUEST['iDriverId'] ?? '';
$sql = "SELECT vName,vLastName,iDriverId FROM register_driver where iCompanyId='".$companyId."'";
$db_driver = $obj->MySQLSelect($sql);

if (count($db_driver) > 0) {
    echo "<option value=''>Search By Driver</option>";
    for ($i = 0; $i < count($db_driver); ++$i) {
        $selected = '';
        if ($db_driver[$i]['iDriverId'] === $iDriverId) {
            $selected = 'selected=selected';
        }
        echo '<option value='.$db_driver[$i]['iDriverId'].' '.$selected.'>'.clearName($db_driver[$i]['vName'].' '.$db_driver[$i]['vLastName']).'</option>';
    }

    exit;
}
