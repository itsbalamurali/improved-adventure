<?php
include_once '../common.php';

if (!$userObj->hasPermission('manage-our-service-menu')) {
    $userObj->redirect();
}
$script = 'masterServiceMenu';
$sql_vehicle_category_table_name = getVehicleCategoryTblName();
$ord = ' ORDER BY msm.iDisplayOrder = 0,msm.iDisplayOrder ASC';
// End Sorting
$rdr_ssql = '';
// Start Search Parameters
$option = isset($_REQUEST['option']) ? stripslashes($_REQUEST['option']) : '';
$keyword = isset($_REQUEST['keyword']) ? stripslashes($_REQUEST['keyword']) : '';
$searchDate = $_REQUEST['searchDate'] ?? '';
$eStatus = $_REQUEST['eStatus'] ?? '';
$eType = $_REQUEST['eType'] ?? '';
$id = $_REQUEST['id'] ?? '';
$menuid = $_REQUEST['menuid'] ?? '';
if (isset($menuid) && !empty($menuid)) {
    $id = $menuid;
}
$ufxEnable = $MODULES_OBJ->isUberXFeatureAvailable() ? 'Yes' : 'No'; // add function to modules availibility
if ('Yes' !== $ufxEnable) {
    $ssql .= " AND eCatType!='ServiceProvider'";
}
if (!$MODULES_OBJ->isAirFlightModuleAvailable(1)) {
    $ssql .= " AND eCatType != 'Fly'";
}
if (!$MODULES_OBJ->isDonationFeatureAvailable()) {
    $ssql .= " AND eCatType != 'Donation'";
}
if (!$MODULES_OBJ->isRideFeatureAvailable()) {
    $ssql .= " AND eCatType != 'Ride' AND eCatType != 'MotoRide' AND eCatType != 'Rental' AND eCatType != 'MotoRental'";
}
if (!$MODULES_OBJ->isDeliveryFeatureAvailable()) {
    $ssql .= " AND eCatType != 'Delivery' AND eCatType != 'MultipleDelivery' AND eCatType != 'MotoDelivery' AND eCatType != 'MoreDelivery'";
}
if (!$MODULES_OBJ->isDeliverAllFeatureAvailable()) {
    $ssql .= " AND eCatType != 'DeliverAll'";
}
if (!$MODULES_OBJ->isEnableAnywhereDeliveryFeature()) {
    $ssql .= " AND eCatType!='Genie' AND eCatType!='Runner' AND eCatType!='Anywhere'";
}
if (!$MODULES_OBJ->isEnableRentItemService()) {
    $ssql .= " AND eCatType != 'RentItem'";
}
if (!$MODULES_OBJ->isEnableTrackAnyServiceFeature()) {
    $ssql .= " AND eCatType != 'TrackAnyService'";
}
if (!$MODULES_OBJ->isEnableRideShareService()) {
    $ssql .= " AND eCatType != 'RideShare'";
}
if (!$MODULES_OBJ->isEnableRentEstateService()) {
    $ssql .= " AND eCatType != 'RentEstate'";
}
if (!$MODULES_OBJ->isEnableRentCarsService()) {
    $ssql .= " AND eCatType != 'RentCars'";
}
if (!$MODULES_OBJ->isEnableNearByService()) {
    $ssql .= " AND eCatType != 'NearBy'";
}
$MasterServiceCategory = '';
if (!empty($eType)) {
    $master_service_category = $obj->MySQLSelect("SELECT JSON_UNQUOTE(JSON_VALUE(vCategoryName, '$.vCategoryName_".$default_lang."')) as vCategoryName FROM master_service_category WHERE eType = '{$eType}' ");
    $MasterServiceCategory = '('.$master_service_category[0]['vCategoryName'].')';
    $ssql = getMasterServiceCategoryQuery($eType, '', $menu = 'Yes');
    if ('VideoConsult' === $eType && $MODULES_OBJ->isEnableVideoConsultingService()) {
        $vc_data = $obj->MySQLSelect("SELECT GROUP_CONCAT(DISTINCT(iParentId)) as ParentIds FROM vehicle_category WHERE eVideoConsultEnable = 'Yes'");
        $ssql .= ' AND iVehicleCategoryId IN ('.$vc_data[0]['ParentIds'].')';
    } elseif ('MedicalServices' === $eType) {
        $ssql .= " AND eForMedicalService = 'Yes' ";
    }
}
$ssql .= $ssqlSearch;
// End Search Parameters
// Pagination Start
$per_page = $DISPLAY_RECORD_NUMBER; // number of results to show per page
// Added By HJ On 14-11-2019 For Manage Service Category Row As Per Design Start
$calSize = 3;
for ($f = $per_page; $f < ($per_page + $calSize); ++$f) {
    $checkZero = $f / $calSize;
    $checkZero = is_numeric($checkZero) && floor($checkZero) !== $checkZero;
    if (empty($checkZero)) {
        $per_page = $f;

        break;
    }
}
// Added By HJ On 14-11-2019 For Manage Service Category Row As Per Design End
if ('' !== $eStatus) {
    $estatusquery = '';
} else {
    $estatusquery = " AND eStatus = 'Active'";
}
$not_sql = ' AND iVehicleCategoryId != 297';
$parent_id_sql = " AND vc.iParentId='0' ";
if ('Ride' === $eType) {
    $ssql .= " AND eForMedicalService = 'No' ";
} elseif (in_array($eType, ['UberX', 'VideoConsult'], true) && $MODULES_OBJ->isEnableMedicalServices('Yes')) {
    $ssql .= ' AND vc.iVehicleCategoryId NOT IN (3,22,26,158) ';
} elseif ('DeliverAll' === $eType && $MODULES_OBJ->isEnableMedicalServices('Yes')) {
    $ssql .= ' AND vc.iServiceId NOT IN (5, 11) ';
} elseif ('MedicalServices' === $eType) {
    $parent_id_sql = " AND (vc.iParentId='0' OR vc.iParentId = '3') ";
}
$sql = 'SELECT COUNT(vc.iVehicleCategoryId) AS Total FROM '.$sql_vehicle_category_table_name." as vc  WHERE  1 = 1 AND vc.iVehicleCategoryId NOT IN (185) {$parent_id_sql} {$estatusquery} {$ssql} {$rdr_ssql} {$not_sql}";
// echo $sql;die;
$totalData = $obj->MySQLSelect($sql);
$total_results = $totalData[0]['Total'];
$total_pages = ceil($total_results / $per_page); // total pages we going to have
$show_page = 1;
$start = 0;
$end = 100;
// -------------if page is setcheck------------------//
if (isset($_GET['page'])) {
    $show_page = $_GET['page'];             // it will telles the current page
    if ($show_page > 0 && $show_page <= $total_pages) {
        $start = ($show_page - 1) * $per_page;
        $end = $start + $per_page;
    }
}
// display pagination
$page = isset($_GET['page']) ? (int) ($_GET['page']) : 0;
$tpages = $total_pages;
if ($page <= 0) {
    $page = 1;
}
// Pagination End
$parent_id_sql = " AND vc.iParentId='0' ";
if ('MedicalServices' === $eType) {
    $parent_id_sql = " AND (vc.iParentId='0' OR vc.iParentId = '3') ";
}
$sql = "SELECT msm.iDisplayOrder as msmiDisplayOrder ,JSON_UNQUOTE(JSON_VALUE(msm.vTitle, '$.vTitle_EN')) as Title,msm.vTitle,vc.eCatType,vc.iVehicleCategoryId,vc.vBannerImage, vc.vLogo,vc.vListLogo1,vc.vListLogo2,vc.vCategory_".$default_lang.' as vCategory, vc.eStatus, vc.iDisplayOrder,vc.eCatType, vc.eVideoConsultEnable,vc.tMedicalServiceInfo,  (select count(iVehicleCategoryId) from '.$sql_vehicle_category_table_name." where iParentId = vc.iVehicleCategoryId AND eStatus != 'Deleted') as SubCategories FROM ".$sql_vehicle_category_table_name." as vc  LEFT JOIN master_service_menu as msm ON msm.iServiceId  = vc.iVehicleCategoryId AND msm.iParentId={$id} WHERE vc.iVehicleCategoryId NOT IN (185) AND vc.eStatus = 'Active'  {$parent_id_sql} {$ssql} {$rdr_ssql} {$not_sql} {$ord} LIMIT {$start}, {$per_page}";
// echo $sql;exit;
$data_drv = $obj->MySQLSelect($sql);
$endRecord = count($data_drv);
$var_filter = '';
foreach ($_REQUEST as $key => $val) {
    if ('tpages' !== $key && 'page' !== $key) {
        $var_filter .= "&{$key}=".stripslashes($val);
    }
}
$reload = $_SERVER['PHP_SELF'].'?tpages='.$tpages.$var_filter;
$ufxEnable = $MODULES_OBJ->isUfxFeatureAvailable(); // Added By HJ On 28-11-2019 For Check UberX Service Status
$eServiceType = !empty($eType) ? '&eServiceType='.$eType : '';
$sql_1 = "SELECT iServiceMenuId,JSON_UNQUOTE(JSON_VALUE(vTitle, '$.vTitle_".$default_lang."')) as vTitle FROM `master_service_menu` WHERE eType = '".$eType."'";
$master_service_menu_ = $obj->MySQLSelect($sql_1);
$MasterServiceCategory = '('.$master_service_menu_[0]['vTitle'].')';
$id = $master_service_menu_[0]['iServiceMenuId'];
$sql_1 = "SELECT * FROM `master_service_menu` WHERE eStatus != 'Inactive' AND iParentId = ".$id;
$master_service_menu = $obj->MySQLSelect($sql_1);
$iServiceId = [];
foreach ($master_service_menu as $key => $a) {
    $iServiceId[$key] = $a['iServiceId'];
}
if (!empty($eType) && 'Bidding' === $eType) {
    $page = 0;
    $total_results = 0;
    $total_pages = 0;
    $endRecord = 0;
    $reload = 0;
    $show_page = 0;
    $total_pages = 0;
    $getBiddingMaster = $BIDDING_OBJ->getBiddingMaster('admin');
    $total_results = count($getBiddingMaster);
    $master_service_menu = [];
    if (isset($getBiddingMaster) && !empty($getBiddingMaster)) {
        $i = 0;
        foreach ($getBiddingMaster as $bidding) {
            $sql_1 = "SELECT *,JSON_UNQUOTE(JSON_VALUE(vTitle, '$.vTitle_EN')) as Title,vTitle FROM `master_service_menu` WHERE iServiceId = ".$bidding['iBiddingId'].' AND iParentId = '.$id;
            $data = $obj->MySQLSelect($sql_1);
            $master_service_menu[$i]['vCategory'] = $bidding['vTitle'];
            $master_service_menu[$i]['iVehicleCategoryId'] = $bidding['iBiddingId'];
            $master_service_menu[$i]['eCatType'] = 'bidding';
            $master_service_menu[$i]['vLogo'] = '';
            $master_service_menu[$i]['vListLogo1'] = '';
            $master_service_menu[$i]['vListLogo2'] = '';
            $master_service_menu[$i]['vBannerImage'] = '';
            $master_service_menu[$i]['eStatus'] = $bidding['eStatus'];
            $master_service_menu[$i]['msmiDisplayOrder'] = $data[0]['iDisplayOrder'];
            $master_service_menu[$i]['Title'] = $data[0]['Title'];
            $master_service_menu[$i]['vTitle'] = $data[0]['vTitle'];
            ++$i;
        }
    }
    $data_drv = $master_service_menu;
}
$sql = 'SELECT * FROM `language_master` ORDER BY `iDispOrder`';
$db_master = $obj->MySQLSelect($sql);
$count_all = count($db_master);
$EN_available = $LANG_OBJ->checkLanguageExist();
$db_master = $LANG_OBJ->getLangDataDefaultFirst($db_master);

if (SITE_TYPE === 'Demo') {
    $_SESSION['success'] = '2';
}
?>
<!DOCTYPE html>
<html lang="en">
<!-- BEGIN HEAD-->

<head>
    <meta charset="UTF-8"/>
    <title><?php echo $SITE_NAME; ?> | <?php echo $langage_lbl_admin['LBL_VEHICLE_CATEGORY_TXT_ADMIN']; ?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <?php include_once 'global_files.php'; ?>
    <style type="text/css">
        .medical-service-title {
            padding: 10px;
            font-size: 20px;
            font-weight: 600;
            border-radius: 5px;
            margin: 0;
            width: 100%;
        }

        hr.medical-service-line {
            border: 1px solid;
            width: calc(100% - 20px);
            margin: 0 0 20px 10px;
        }

        .medical-service-note {
            margin-top: 10px;
        }
    </style>
</head>
<!-- END  HEAD-->
<!-- BEGIN BODY-->

<body class="padTop53 ">
<!-- Main LOading -->
<!-- MAIN WRAPPER -->
<div id="wrap">
    <?php include_once 'header.php'; ?>
    <?php include_once 'left_menu.php'; ?>
    <!--PAGE CONTENT -->
    <div id="content">
        <div class="inner">
            <div id="add-hide-show-div" class="vehicleCategorylist">
                <div class="row">
                    <div class="col-lg-12">
                        <h2><?php echo $langage_lbl_admin['LBL_VEHICLE_CATEGORY_TXT_ADMIN']; ?> <?php echo $MasterServiceCategory; ?></h2>
                    </div>
                </div>
                <hr/>
            </div>

            <?php include 'valid_msg.php'; ?>

            <?php if ('MedicalServices' !== $eType) { ?>
                <div class="table-list">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="admin-nir-export vehicle-cat">
                                <div class="table-responsive1">
                                    <div class="table table-striped  table-hover">
                                        <div class="profile-earning">
                                            <div class="partation">
                                                <ul style="padding-left: 0px;" class="setings-list">
                                                    <?php if (!empty($data_drv)) {
                                                        for ($i = 0; $i < count($data_drv); ++$i) {
                                                            $buttonStatus = $data_drv[$i]['eStatus'];
                                                            $btnChecked = 0;
                                                            if ('Active' === $data_drv[$i]['eStatus']) {
                                                                $btnChecked = 1;
                                                            }
                                                            $btnChecked = 0;
                                                            $buttonStatus = 'Inactive';
                                                            if (in_array($data_drv[$i]['iVehicleCategoryId'], $iServiceId, true)) {
                                                                $btnChecked = 1;
                                                                $buttonStatus = 'Active';
                                                            }
                                                            $vLevel = json_decode($data_drv[$i]['vTitle'], true);
                                                            ?>
                                                        <li>
                                                            <form class="_list_form<?php echo $data_drv[$i]['iVehicleCategoryId']; ?>"
                                                                  id="_list_form<?php echo $data_drv[$i]['iVehicleCategoryId']; ?>"
                                                                  method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                                                <input type="hidden" name="menuid" value="<?php echo $id; ?>"
                                                                       id="menuid">
                                                                <input type="hidden" name="iVehicleCategoryId"
                                                                       value="<?php echo $data_drv[$i]['iVehicleCategoryId']; ?>"
                                                                       id="iVehicleCategoryId">

                                                                <div class="toggle-list-inner">
                                                                    <div class="toggle-combo">
                                                                        <label>
                                                                            <div align="center">
                                                                                <!-- <img src="<?php echo $logoPath; ?>" style="width:100px;"> -->
                                                                            </div>
                                                                            <div style="margin: 0 0 0 10px;">
                                                                                <td><?php echo $data_drv[$i]['vCategory']; ?> </td>
                                                                            </div>
                                                                        </label>

                                                                        <span class="toggle-switch">
                                                                            <input type="checkbox"
                                                                                   <?php if ($btnChecked > 0) { ?>checked="" <?php } ?>
                                                                                   onClick="changeMenuStatus('<?php echo $id; ?>','<?php echo $data_drv[$i]['iVehicleCategoryId']; ?>', '<?php echo $buttonStatus; ?>')"
                                                                                   id="statusbutton" class="chk"
                                                                                   name="statusbutton" value="246">
                                                                            <span class="toggle-base"></span>
                                                                        </span>

                                                                    </div>
                                                                    <div class="">
                                                                        <div class="form-group">
                                                                            <label class="col-sm-12">Name<span
                                                                                        class="red"> *</span></label>
                                                                            <div class="input-group col-sm-12">
                                                                                <input class="form-control" type="text"
                                                                                       name="vLevel"
                                                                                       id="vTitle_<?php echo $data_drv[$i]['iVehicleCategoryId'] ?? ''; ?>Default"
                                                                                       id="$('#vTitle_'+id+'<?php echo $default_lang; ?>'"
                                                                                       value="<?php echo $vLevel['vTitle_'.$default_lang] ?? ''; ?>"
                                                                                       placeholder="Name"
                                                                                       onclick="editDescription('Edit' , <?php echo $data_drv[$i]['iVehicleCategoryId'] ?? ''; ?>)"/>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="col-sm-12">Display
                                                                                Order</label>
                                                                            <div class="input-group col-sm-12">
                                                                                <select name="iDisplayOrder"
                                                                                        class="form-control">
                                                                                    <?php for ($j = 1; $j <= $total_results; ++$j) { ?>
                                                                                        <option value="<?php echo $j; ?>" <?php echo $data_drv[$i]['msmiDisplayOrder'] === $j ? 'selected' : ''; ?>>
                                                                                            <?php echo $j; ?></option>
                                                                                    <?php } ?>
                                                                                </select>
                                                                            </div>
                                                                            <input type="hidden" name="oldDisplayOrder"
                                                                                   id="oldDisplayOrder"
                                                                                   value="<?php echo $data_drv[$i]['msmiDisplayOrder']; ?>">
                                                                        </div>

                                                                        <div class="form-group"
                                                                             style="padding-bottom: 15px">
                                                                            <div class="input-group col-sm-12">
                                                                                <input attr-formid='<?php echo $data_drv[$i]['iVehicleCategoryId']; ?>'
                                                                                       type="submit" name="save"
                                                                                       value="Save"
                                                                                       class="btn btn-default">
                                                                            </div>
                                                                        </div>
                                                                        <div class="modal fade"
                                                                             id="coupon_desc_Modal<?php echo $data_drv[$i]['iVehicleCategoryId'] ?? ''; ?>"
                                                                             tabindex="-1" role="dialog"
                                                                             aria-hidden="true" data-backdrop="static"
                                                                             data-keyboard="false">
                                                                            <div class="modal-dialog modal-lg">
                                                                                <div class="modal-content nimot-class">
                                                                                    <div class="modal-header">
                                                                                        <h4>
                                                                                            <span id="modal_action"></span>
                                                                                            Name
                                                                                            <button type="button"
                                                                                                    class="close"
                                                                                                    data-dismiss="modal"
                                                                                                    onclick="resetToOriginalValue(this, 'vTitle_')">
                                                                                                x
                                                                                            </button>
                                                                                        </h4>
                                                                                    </div>
                                                                                    <div class="modal-body">

                                                                                        <?php
                                                                                            for ($d = 0; $d < $count_all; ++$d) {
                                                                                                $vCode = $db_master[$d]['vCode'];
                                                                                                $vTitle = $db_master[$d]['vTitle'];
                                                                                                $eDefault = $db_master[$d]['eDefault'];
                                                                                                $descVal = 'vTitle_'.$data_drv[$i]['iVehicleCategoryId'].$vCode;
                                                                                                $descVal_ = 'vTitle_'.$vCode;
                                                                                                $required_msg = ('Yes' === $eDefault) ? '<span class="red"> *</span>' : '';
                                                                                                ?>
                                                                                            <?php
                                                                                                $page_title_class = 'col-lg-12';
                                                                                                if (count($db_master) > 1) {
                                                                                                    if ($EN_available) {
                                                                                                        if ('EN' === $vCode) {
                                                                                                            $page_title_class = 'col-md-9 col-sm-9';
                                                                                                        }
                                                                                                    } else {
                                                                                                        if ($vCode === $default_lang) {
                                                                                                            $page_title_class = 'col-md-9 col-sm-9';
                                                                                                        }
                                                                                                    }
                                                                                                }
                                                                                                ?>
                                                                                            <div class="form-group row">
                                                                                                <div class="col-lg-12">
                                                                                                    <label>Name
                                                                                                        (<?php echo $vTitle; ?>
                                                                                                        ) <?php echo $required_msg; ?></label>
                                                                                                </div>
                                                                                                <div class="<?php echo $page_title_class; ?>">
                                                                                                    <input type="text"
                                                                                                           name="<?php echo $descVal; ?>"
                                                                                                           class="form-control"
                                                                                                           id="<?php echo $descVal; ?>"
                                                                                                           placeholder="<?php echo $vTitle; ?> Value"
                                                                                                           data-originalvalue="<?php echo $vLevel[$descVal_]; ?>"
                                                                                                           value="<?php echo $vLevel[$descVal_]; ?>">
                                                                                                    <div class="text-danger"
                                                                                                         id="<?php echo $descVal.'_error'; ?>"
                                                                                                         style="display: none;"><?php echo $langage_lbl_admin['LBL_REQUIRED']; ?></div>
                                                                                                </div>
                                                                                                <?php
                                                                                                    if (count($db_master) > 1) {
                                                                                                        if ($EN_available) {
                                                                                                            if ('EN' === $vCode) { ?>
                                                                                                            <div class="col-md-3 col-sm-3">
                                                                                                                <button type="button"
                                                                                                                        name="allLanguage"
                                                                                                                        id="allLanguage"
                                                                                                                        class="btn btn-primary"
                                                                                                                        onClick="getAllLanguageCode('vTitle_<?php echo $data_drv[$i]['iVehicleCategoryId'] ?? ''; ?>', '<?php echo $default_lang; ?>');">
                                                                                                                    Convert
                                                                                                                    To
                                                                                                                    All
                                                                                                                    Language
                                                                                                                </button>
                                                                                                            </div>
                                                                                                        <?php }
                                                                                                            } else {
                                                                                                                if ($vCode === $default_lang) { ?>
                                                                                                            <div class="col-md-3 col-sm-3">
                                                                                                                <button type="button"
                                                                                                                        name="allLanguage"
                                                                                                                        id="allLanguage"
                                                                                                                        class="btn btn-primary"
                                                                                                                        onClick="getAllLanguageCode('vTitle_<?php echo $data_drv[$i]['iVehicleCategoryId'] ?? ''; ?>', '<?php echo $default_lang; ?>');">
                                                                                                                    Convert
                                                                                                                    To
                                                                                                                    All
                                                                                                                    Language
                                                                                                                </button>
                                                                                                            </div>
                                                                                                        <?php }
                                                                                                                }
                                                                                                    }
                                                                                                ?>
                                                                                            </div>
                                                                                            <?php
                                                                                            }
                                                            ?>
                                                                                    </div>
                                                                                    <div class="modal-footer"
                                                                                         style="margin-top: 0">
                                                                                        <h5 class="text-left"
                                                                                            style="margin-bottom: 15px; margin-top: 0;">
                                                                                            <strong><?php echo $langage_lbl['LBL_NOTE']; ?>
                                                                                                : </strong><?php echo $langage_lbl['LBL_SAVE_INFO']; ?>
                                                                                        </h5>
                                                                                        <div class="nimot-class-but"
                                                                                             style="margin-bottom: 0">
                                                                                            <button type="button"
                                                                                                    class="save"
                                                                                                    style="margin-left: 0 !important"
                                                                                                    onclick="saveDescription(<?php echo $data_drv[$i]['iVehicleCategoryId'] ?? ''; ?>)"><?php echo $langage_lbl['LBL_Save']; ?></button>
                                                                                            <button type="button"
                                                                                                    class="btn btn-danger btn-ok"
                                                                                                    data-dismiss="modal"
                                                                                                    onclick="resetToOriginalValue(this, 'vTitle_')"><?php echo $langage_lbl['LBL_CANCEL_TXT']; ?></button>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div style="clear:both;"></div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div style="display:none" class="check-combo">
                                                                        <label id="defaultText_246">
                                                                            <ul>

                                                                                    <li class="entypo-twitter"
                                                                                        data-network="twitter"><a
                                                                                                href="vehicle_category_action.php?id=<?php echo $data_drv[$i]['iVehicleCategoryId'].$eServiceType; ?>"
                                                                                                data-toggle="tooltip"
                                                                                                title="Edit">
                                                                                            <img src="img/edit-new.png"
                                                                                                 alt="Edit">
                                                                                        </a></li>


                                                                            </ul>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </li>
                                                    <?php } ?>
                                                </ul>
                                            </div>
                                        </div><?php
                                                    } else {
                                                        ?>
                                            <tr class="gradeA">
                                                <td colspan="8"> No Records Found.</td>
                                            </tr>
                                        <?php } ?>
                                    </div>
                                </div>
                                <!-- </form> -->
                                <?php include 'pagination_n.php'; ?>


                            </div>
                        </div>
                        <!--TABLE-END-->
                    </div>
                </div>
            <?php } else {
                $OnDemandServicesArr = $VideoConsultServicesArr = $MoreServicesArr = [];
                if (!empty($data_drv)) {
                    foreach ($data_drv as $med_service) {
                        if (!empty($med_service['tMedicalServiceInfo'])) {
                            $tMedicalServiceInfoArr = json_decode($med_service['tMedicalServiceInfo'], true);
                            if ('Yes' === $tMedicalServiceInfoArr['BookService']) {
                                $med_service['ms_display_order'] = $tMedicalServiceInfoArr['iDisplayOrderBS'];
                                $OnDemandServicesArr[] = $med_service;
                            }
                            if ('Yes' === $med_service['eVideoConsultEnable'] && 'Yes' === $tMedicalServiceInfoArr['VideoConsult']) {
                                $med_service['ms_display_order'] = $tMedicalServiceInfoArr['iDisplayOrderVC'];
                                $VideoConsultServicesArr[] = $med_service;
                            }
                            if ('Yes' === $tMedicalServiceInfoArr['MoreService']) {
                                $med_service['ms_display_order'] = $tMedicalServiceInfoArr['iDisplayOrderMS'];
                                $MoreServicesArr[] = $med_service;
                            }
                        }
                    }
                    $ms_display_order = array_column($OnDemandServicesArr, 'ms_display_order');
                    array_multisort($ms_display_order, SORT_ASC, $OnDemandServicesArr);
                    $ms_display_order = array_column($VideoConsultServicesArr, 'ms_display_order');
                    array_multisort($ms_display_order, SORT_ASC, $VideoConsultServicesArr);
                    $ms_display_order = array_column($MoreServicesArr, 'ms_display_order');
                    array_multisort($ms_display_order, SORT_ASC, $MoreServicesArr);
                }
                $MEDICAL_SERVICES_ARR = [
                    [
                        'ServiceTitle' => $langage_lbl_admin['LBL_ON_DEMAND_MEDICAL_SERVICES_TITLE'],
                        'ServicesArr' => $OnDemandServicesArr,
                    ],
                    [
                        'ServiceTitle' => $langage_lbl_admin['LBL_VIDEO_CONSULT_MEDICAL_SERVICES_TITLE'],
                        'ServicesArr' => $VideoConsultServicesArr,
                    ],
                    [
                        'ServiceTitle' => $langage_lbl_admin['LBL_MEDICAL_MORE_SERVICES_TITLE'],
                        'ServicesArr' => $MoreServicesArr,
                    ],
                ];
                ?>
                <div class="table-list">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="admin-nir-export vehicle-cat">
                                <div class="table-responsive1">
                                    <div class="table table-striped  table-hover">
                                        <div class="profile-earning">
                                            <?php foreach ($MEDICAL_SERVICES_ARR as $MEDICAL_SERVICE) { ?>
                                                <div class="partation">
                                                    <div class="medical-service-title">
                                                        <span><?php echo $MEDICAL_SERVICE['ServiceTitle']; ?></span>
                                                    </div>
                                                    <hr class="medical-service-line"/>
                                                    <ul style="padding-left: 0px;" class="setings-list">

                                                        <?php if (!empty($MEDICAL_SERVICE['ServicesArr'])) {
                                                            foreach ($MEDICAL_SERVICE['ServicesArr'] as $MedService) {
                                                                $buttonStatus = $MedService['eStatus'];
                                                                $btnChecked = 0;
                                                                if ('Active' === $MedService['eStatus']) {
                                                                    $btnChecked = 1;
                                                                }
                                                                $btnChecked = 0;
                                                                $buttonStatus = 'Inactive';
                                                                if (in_array($MedService['iVehicleCategoryId'], $iServiceId, true)) {
                                                                    $btnChecked = 1;
                                                                    $buttonStatus = 'Active';
                                                                }
                                                                $vLevel = json_decode($MedService['vTitle'], true);
                                                                ?>
                                                                <li>
                                                                    <form class="_list_form<?php echo $MedService['iVehicleCategoryId']; ?>"
                                                                          id="_list_form<?php echo $MedService['iVehicleCategoryId']; ?>"
                                                                          method="post"
                                                                          action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                                                        <input type="hidden" name="menuid"
                                                                               value="<?php echo $id; ?>" id="menuid">
                                                                        <input type="hidden" name="iVehicleCategoryId"
                                                                               value="<?php echo $MedService['iVehicleCategoryId']; ?>"
                                                                               id="iVehicleCategoryId">

                                                                        <input type="hidden" name="eType"
                                                                               value="<?php echo $eType; ?>"
                                                                               id="eType">
                                                                        <div class="toggle-list-inner">
                                                                            <div class="toggle-combo">
                                                                                <label>
                                                                                    <div style="margin: 0 0 0 10px;">
                                                                                        <td><?php echo $MedService['vCategory']; ?> </td>
                                                                                    </div>
                                                                                </label>

                                                                                <span class="toggle-switch">
                                                                            <input type="checkbox"
                                                                                   <?php if ($btnChecked > 0) { ?>checked="" <?php } ?>
                                                                                   onClick="changeMenuStatus('<?php echo $id; ?>','<?php echo $MedService['iVehicleCategoryId']; ?>', '<?php echo $buttonStatus; ?>')"
                                                                                   id="statusbutton" class="chk"
                                                                                   name="statusbutton" value="246">
                                                                            <span class="toggle-base"></span>
                                                                        </span>
                                                                            </div>
                                                                            <div class="">
                                                                                <div class="form-group">
                                                                                    <label class="col-sm-12">Name<span
                                                                                                class="red"> *</span></label>
                                                                                    <div class="input-group col-sm-12">
                                                                                        <input class="form-control"
                                                                                               type="text" name="vLevel"
                                                                                               id="vTitle_<?php echo $MedService['iVehicleCategoryId'] ?? ''; ?>Default"
                                                                                               id="$('#vTitle_'+id+'<?php echo $default_lang; ?>'"
                                                                                               value="<?php echo $vLevel['vTitle_'.$default_lang] ?? ''; ?>"
                                                                                               placeholder="Name"
                                                                                               onclick="editDescription('Edit' , <?php echo $MedService['iVehicleCategoryId'] ?? ''; ?>)"/>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="form-group">
                                                                                    <label class="col-sm-12">Display
                                                                                        Order</label>
                                                                                    <div class="input-group col-sm-12">
                                                                                        <select name="iDisplayOrder"
                                                                                                class="form-control">
                                                                                            <?php for ($j = 1; $j <= $total_results; ++$j) { ?>
                                                                                                <option value="<?php echo $j; ?>" <?php echo $MedService['msmiDisplayOrder'] === $j ? 'selected' : ''; ?>>
                                                                                                    <?php echo $j; ?></option>
                                                                                            <?php } ?>
                                                                                        </select>
                                                                                    </div>
                                                                                    <input type="hidden"
                                                                                           name="oldDisplayOrder"
                                                                                           id="oldDisplayOrder"
                                                                                           value="<?php echo $MedService['msmiDisplayOrder']; ?>">
                                                                                </div>

                                                                                <div class="form-group"
                                                                                     style="padding-bottom: 15px">
                                                                                    <div class="input-group col-sm-12">
                                                                                        <input attr-formid='<?php echo $MedService['iVehicleCategoryId']; ?>'
                                                                                               type="submit" name="save"
                                                                                               value="Save"
                                                                                               class="btn btn-default">
                                                                                    </div>
                                                                                </div>
                                                                                <div class="modal fade"
                                                                                     id="coupon_desc_Modal<?php echo $MedService['iVehicleCategoryId'] ?? ''; ?>"
                                                                                     tabindex="-1" role="dialog"
                                                                                     aria-hidden="true"
                                                                                     data-backdrop="static"
                                                                                     data-keyboard="false">
                                                                                    <div class="modal-dialog modal-lg">
                                                                                        <div class="modal-content nimot-class">
                                                                                            <div class="modal-header">
                                                                                                <h4>
                                                                                                    <span id="modal_action"></span>
                                                                                                    Name
                                                                                                    <button type="button"
                                                                                                            class="close"
                                                                                                            data-dismiss="modal"
                                                                                                            onclick="resetToOriginalValue(this, 'vTitle_')">
                                                                                                        x
                                                                                                    </button>
                                                                                                </h4>
                                                                                            </div>
                                                                                            <div class="modal-body">

                                                                                                <?php
                                                                                                for ($d = 0; $d < $count_all; ++$d) {
                                                                                                    $vCode = $db_master[$d]['vCode'];
                                                                                                    $vTitle = $db_master[$d]['vTitle'];
                                                                                                    $eDefault = $db_master[$d]['eDefault'];
                                                                                                    $descVal = 'vTitle_'.$MedService['iVehicleCategoryId'].$vCode;
                                                                                                    $descVal_ = 'vTitle_'.$vCode;
                                                                                                    $required_msg = ('Yes' === $eDefault) ? '<span class="red"> *</span>' : '';
                                                                                                    ?>
                                                                                                    <?php
                                                                                                    $page_title_class = 'col-lg-12';
                                                                                                    if (count($db_master) > 1) {
                                                                                                        if ($EN_available) {
                                                                                                            if ('EN' === $vCode) {
                                                                                                                $page_title_class = 'col-md-9 col-sm-9';
                                                                                                            }
                                                                                                        } else {
                                                                                                            if ($vCode === $default_lang) {
                                                                                                                $page_title_class = 'col-md-9 col-sm-9';
                                                                                                            }
                                                                                                        }
                                                                                                    }
                                                                                                    ?>
                                                                                                    <div class="form-group row">
                                                                                                        <div class="col-lg-12">
                                                                                                            <label>Name
                                                                                                                (<?php echo $vTitle; ?>
                                                                                                                ) <?php echo $required_msg; ?></label>
                                                                                                        </div>
                                                                                                        <div class="<?php echo $page_title_class; ?>">
                                                                                                            <input type="text"
                                                                                                                   name="<?php echo $descVal; ?>"
                                                                                                                   class="form-control"
                                                                                                                   id="<?php echo $descVal; ?>"
                                                                                                                   placeholder="<?php echo $vTitle; ?> Value"
                                                                                                                   data-originalvalue="<?php echo $vLevel[$descVal_]; ?>"
                                                                                                                   value="<?php echo $vLevel[$descVal_]; ?>">
                                                                                                            <div class="text-danger"
                                                                                                                 id="<?php echo $descVal.'_error'; ?>"
                                                                                                                 style="display: none;"><?php echo $langage_lbl_admin['LBL_REQUIRED']; ?></div>
                                                                                                        </div>
                                                                                                        <?php
                                                                                                        if (count($db_master) > 1) {
                                                                                                            if ($EN_available) {
                                                                                                                if ('EN' === $vCode) { ?>
                                                                                                                    <div class="col-md-3 col-sm-3">
                                                                                                                        <button type="button"
                                                                                                                                name="allLanguage"
                                                                                                                                id="allLanguage"
                                                                                                                                class="btn btn-primary"
                                                                                                                                onClick="getAllLanguageCode('vTitle_<?php echo $MedService['iVehicleCategoryId'] ?? ''; ?>', '<?php echo $default_lang; ?>');">
                                                                                                                            Convert
                                                                                                                            To
                                                                                                                            All
                                                                                                                            Language
                                                                                                                        </button>
                                                                                                                    </div>
                                                                                                                <?php }
                                                                                                                } else {
                                                                                                                    if ($vCode === $default_lang) { ?>
                                                                                                                    <div class="col-md-3 col-sm-3">
                                                                                                                        <button type="button"
                                                                                                                                name="allLanguage"
                                                                                                                                id="allLanguage"
                                                                                                                                class="btn btn-primary"
                                                                                                                                onClick="getAllLanguageCode('vTitle_<?php echo $MedService['iVehicleCategoryId'] ?? ''; ?>', '<?php echo $default_lang; ?>');">
                                                                                                                            Convert
                                                                                                                            To
                                                                                                                            All
                                                                                                                            Language
                                                                                                                        </button>
                                                                                                                    </div>
                                                                                                                <?php }
                                                                                                                    }
                                                                                                        }
                                                                                                    ?>
                                                                                                    </div>
                                                                                                    <?php
                                                                                                }
                                                                ?>
                                                                                            </div>
                                                                                            <div class="modal-footer"
                                                                                                 style="margin-top: 0">
                                                                                                <h5 class="text-left"
                                                                                                    style="margin-bottom: 15px; margin-top: 0;">
                                                                                                    <strong><?php echo $langage_lbl['LBL_NOTE']; ?>
                                                                                                        : </strong><?php echo $langage_lbl['LBL_SAVE_INFO']; ?>
                                                                                                </h5>
                                                                                                <div class="nimot-class-but"
                                                                                                     style="margin-bottom: 0">
                                                                                                    <button type="button"
                                                                                                            class="save"
                                                                                                            style="margin-left: 0 !important"
                                                                                                            onclick="saveDescription(<?php echo $MedService['iVehicleCategoryId'] ?? ''; ?>)"><?php echo $langage_lbl['LBL_Save']; ?></button>
                                                                                                    <button type="button"
                                                                                                            class="btn btn-danger btn-ok"
                                                                                                            data-dismiss="modal"
                                                                                                            onclick="resetToOriginalValue(this, 'vTitle_')"><?php echo $langage_lbl['LBL_CANCEL_TXT']; ?></button>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div style="clear:both;"></div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </form>
                                                                </li>
                                                            <?php }
                                                            } ?>
                                                    </ul>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                                <!-- </form> -->
                            </div>
                        </div>
                        <!--TABLE-END-->
                    </div>
                </div>
            <?php } ?>
            <div class="admin-notes">
                <h4>Notes:</h4>
                <ul>
                    <li>Manage "Our Services" menu content and display order for home page.</li>
                </ul>
            </div>
        </div>
    </div>
    <!--END PAGE CONTENT -->
</div>
<!--END MAIN WRAPPER -->
<form name="pageForm" id="pageForm" action="action/menu_service.php" method="post">
    <input type="hidden" name="page" id="page" value="<?php echo $page; ?>">
    <input type="hidden" name="tpages" id="tpages" value="<?php echo $tpages; ?>">
    <input type="hidden" name="menuid" id="menuid" value="<?php echo $id; ?>">
    <input type="hidden" name="iVehicleCategoryId" id="iMainId01" value="">
    <input type="hidden" name="eStatus" id="eStatus" value="<?php echo $eStatus; ?>">
    <input type="hidden" name="status" id="status01" value="">
    <input type="hidden" name="statusVal" id="statusVal" value="">
    <input type="hidden" name="option" value="<?php echo $option; ?>">
    <input type="hidden" name="keyword" value="<?php echo $keyword; ?>">
    <input type="hidden" name="sortby" id="sortby" value="<?php echo $sortby; ?>">
    <input type="hidden" name="order" id="order" value="<?php echo $order; ?>">
    <input type="hidden" name="method" id="method" value="">
    <input type="hidden" name="eType" value="<?php echo $eType; ?>">
</form>
<div class="row loding-action" id="loaderIcon" style="display:none;">
    <div align="center">
        <img src="default.gif">
        <span>Language Translation is in Process. Please Wait...</span>
    </div>
</div>
<?php include_once 'footer.php'; ?>
<script>
    $("#setAllCheck").on('click', function () {
        if ($(this).prop("checked")) {
            jQuery("#_list_form input[type=checkbox]").each(function () {
                if ($(this).attr('disabled') != 'disabled') {
                    this.checked = 'true';
                }
            });
        } else {
            jQuery("#_list_form input[type=checkbox]").each(function () {
                this.checked = '';
            });
        }
    });
    $("#Search").on('click', function () {
        //$('html').addClass('loading');
        var action = $("#_list_form").attr('action');
        //alert(action);
        var formValus = $("#frmsearch").serialize();
        window.location.href = action + "?" + formValus;
    });
    $('.entypo-export').click(function (e) {
        e.stopPropagation();
        var $this = $(this).parent().find('div');
        $(".openHoverAction-class div").not($this).removeClass('active');
        $this.toggleClass('active');
    });
    $(document).on("click", function (e) {
        if ($(e.target).is(".openHoverAction-class,.show-moreOptions,.entypo-export") === false) {
            $(".show-moreOptions").removeClass("active");
        }
    });
</script>
<script>
    function saveMenuText(id) {
        var formListData = $("#_list_form" + id).serialize() + '&ajax=1';
        var ajaxData = {
            'URL': _system_admin_url + "action/menu_service.php",
            'AJAX_DATA': formListData,
        };
        $('#loaderIcon').find('span').hide();
        $('#loaderIcon').show();
        getDataFromAjaxCall(ajaxData, function (response) {
            $('#loaderIcon').find('span').show();
            $('#loaderIcon').hide();
            if (response.action == "1") {
                location.reload();
            } else {
                alert("Something went wrong.");
            }
        });
    }

    function editDescription(action, id) {
        $('#modal_action').html(action);
        $('#coupon_desc_Modal' + id).modal('show');
    }

    function saveDescription(id) {
        if ($('#vTitle_' + id + '<?php echo $default_lang; ?>').val() == "") {
            $('#vTitle_' + id + '<?php echo $default_lang; ?>_error').show();
            $('#vTitle_' + id + '<?php echo $default_lang; ?>').focus();
            clearInterval(myVar);
            myVar = setTimeout(function () {
                $('#vTitle_' + id + '<?php echo $default_lang; ?>_error').hide();
            }, 5000);
            return false;
        }
        $('#vTitle_' + id + 'Default').val($('#vTitle_' + id + '<?php echo $default_lang; ?>').val());
        $('#vTitle_' + id + 'Default').closest('.row').removeClass('has-error');
        $('#vTitle_' + id + 'Default-error').remove();
        $('#coupon_desc_Modal' + id).modal('hide');
    }
</script>
</body>
<!-- END BODY-->

</html>