<?php



include_once '../common.php';

$iServiceid = $_REQUEST['iServiceid'] ?? '';
$iCompanyId = $_REQUEST['iCompanyId'] ?? '';
$cuisine_ids = $_REQUEST['cuisine_ids'] ?? '';
if (!empty($cuisine_ids) && '{}' !== $cuisine_ids) {
    $cuisine_ids = json_decode(stripslashes($cuisine_ids), true);
}

$selectcuisine_sql = 'SELECT cuisineId,cuisineName_'.$default_lang.' FROM cuisine WHERE  iServiceId IN ('.$iServiceid.") AND eStatus = 'Active'";
$db_cuisine = $obj->MySQLSelect($selectcuisine_sql);

$sql1 = "SELECT cuisineId FROM `company_cuisine` WHERE iCompanyId = '".$iCompanyId."'";
$db_cusinedata = $obj->MySQLSelect($sql1);
foreach ($db_cusinedata as $key => $value) {
    $cusineselecteddata[] = $value['cuisineId'];
}

if (count($db_cuisine) > 0) {
    foreach ($db_cuisine as $cuisinedata) {
        $selected = '';
        if (isset($cusineselecteddata) && in_array($cuisinedata['cuisineId'], $cusineselecteddata, true)) {
            $selected = 'selected=selected';
            if (!empty($cuisine_ids) && !in_array($cuisinedata['cuisineId'], $cuisine_ids, true)) {
                $selected = '';
            }
        } elseif (!empty($cuisine_ids) && in_array($cuisinedata['cuisineId'], $cuisine_ids, true)) {
            $selected = 'selected';
        }

        echo "<option name='".$cuisinedata['cuisineId']."' value='".$cuisinedata['cuisineId']."' ".$selected.' >'.$cuisinedata['cuisineName_'.$default_lang].'</option>';
    }

    exit;
}
