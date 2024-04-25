<?php
include_once '../common.php';
$default_lang = $LANG_OBJ->FetchSystemDefaultLang();
$iParentId = $_REQUEST['iParentId'] ?? '';

$iMasterServiceCategoryId = $_REQUEST['iMasterServiceCategoryId'] ?? '';

$iItemSubCategoryId = $_REQUEST['iItemSubCategoryId'] ?? '';

$selected = 'selected';

if ('' !== $iMasterServiceCategoryId && '' !== $iParentId) {
    $ordersql = ' ORDER BY iMasterServiceCategoryId,iDisplayOrder';
    $rSql = "AND iMasterServiceCategoryId = '".$iMasterServiceCategoryId."' AND iParentId = '".$iParentId."' AND ( estatus = 'Active' || estatus = 'Inactive' )";

    $rentitem = $RENTITEM_OBJ->getRentItemSubCategory('admin', $iParentId, $rSql);

    $cont = '';

    $cont .= '<select class="validate[required] form-control custom-select-new" id="iItemSubCategoryId" name="iItemSubCategoryId">';

    $cont .= '<option value="">Select Subcategory</option>';

    for ($i = 0; $i < count($rentitem); ++$i) {
        if ($rentitem[$i]['iRentItemId'] === $iItemSubCategoryId) {
            $cont .= '<option value="'.$rentitem[$i]['iRentItemId'].'"  '.$selected.'>'.$rentitem[$i]['vTitle'].'</option>';
        } else {
            $cont .= '<option value="'.$rentitem[$i]['iRentItemId'].'">'.$rentitem[$i]['vTitle'].'</option>';
        }
    }

    $cont .= '</select>';

    echo $cont;

    exit;
}

?>

