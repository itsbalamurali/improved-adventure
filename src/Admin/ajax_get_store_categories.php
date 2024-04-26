<?php



include_once '../common.php';

$iServiceid = $_REQUEST['iServiceid'] ?? '';
$id = $_REQUEST['storeId'] ?? '';
$storecatselectedid = $_REQUEST['selectedcatid'] ?? '';
$store_cat_ids = $_REQUEST['store_cat_ids'] ?? '';
if (!empty($store_cat_ids) && '{}' !== $store_cat_ids) {
    $store_cat_ids = json_decode(stripslashes($store_cat_ids), true);
}
$become_restaurant = 'Store';
if ('YES' === strtoupper(DELIVERALL)) {
    if (1 === $iServiceid) {
        $become_restaurant = $langage_lbl_admin['LBL_RESTAURANT_TXT'];
    } else {
        $become_restaurant = $langage_lbl_admin['LBL_STORE'];
    }
}
$sel_store_cat_txt = $langage_lbl_admin['LBL_SELECT_TXT'].' '.$become_restaurant.' '.$langage_lbl_admin['LBL_CATEGORY_FRONT'];
// echo '<option value="">'.$sel_store_cat_txt.'</option>';
if ('' !== $iServiceid) {
    $storecatselecteddata = [];
    if ('' !== $id) {
        $sql3 = "SELECT iCategoryId FROM `store_category_tags` WHERE iCompanyId = '".$id."'";
        $db_store_category_tags = $obj->MySQLSelect($sql3);
        foreach ($db_store_category_tags as $tkey => $tvalue) {
            $storecatselecteddata[] = $tvalue['iCategoryId'];
        }
    }

    $scSql = "SELECT iCategoryId,JSON_UNQUOTE(JSON_EXTRACT(tCategoryName, '$.tCategoryName_".$default_lang."')) as tCategoryName, service_categories.vServiceName_".$default_lang." as vServiceCategoryName FROM store_categories LEFT JOIN service_categories ON service_categories.iServiceId = store_categories.iServiceId WHERE store_categories.eType = 'manual' AND store_categories.iServiceId IN (".$iServiceid.')';
    $scSqlData = $obj->MySQLSelect($scSql);

    foreach ($scSqlData as $cat) {
        $selected = '';
        if (in_array($cat['iCategoryId'], $storecatselecteddata, true)) {
            $selected = 'selected';
            if (!empty($store_cat_ids) && !in_array($cat['iCategoryId'], $store_cat_ids, true)) {
                $selected = '';
            }
        } elseif (!empty($store_cat_ids) && in_array($cat['iCategoryId'], $store_cat_ids, true)) {
            $selected = 'selected';
        }
        if ($cat['iCategoryId'] === $storecatselectedid) {
            $selected = 'selected';
        }

        $service_cat_name = '';
        if ($MODULES_OBJ->isEnableStoreMultiServiceCategories()) {
            $service_cat_name = ' ('.$cat['vServiceCategoryName'].')';
        }
        echo '<option value="'.$cat['iCategoryId'].'" '.$selected.'>'.$cat['tCategoryName'].$service_cat_name.'</option>';
    }
}

exit;
