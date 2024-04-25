<?php
include_once '../common.php';

if ($MODULES_OBJ->isEnableAppHomeScreenLayoutV3()) {
    include_once 'master_service_category_pro.php';

    exit;
}

if (!$userObj->hasPermission('view-master-service-category')) {
    $userObj->redirect();
}

$script = 'VehicleCategory';
$tbl_name = 'master_service_category';

$sql_vehicle_category_table_name = getVehicleCategoryTblName();

$iMasterServiceCategoryId = $_REQUEST['id'] ?? '';
$status = $_REQUEST['status'] ?? '';
$eType = $_REQUEST['eType'] ?? '';

if (!empty($iMasterServiceCategoryId) && !empty($status)) {
    if (SITE_TYPE !== 'Demo') {
        $obj->sql_query('UPDATE '.$tbl_name." SET eStatus = '".$status."' WHERE iMasterServiceCategoryId = '".$iMasterServiceCategoryId."'");

        $ssql = getMasterServiceCategoryQuery($eType, 'Yes');

        if ('Bidding' === $eType || 'MedicalServices' === $eType) {
            header('Location:master_service_category.php');

            exit;
        }

        $vehicle_category_data = $obj->MySQLSelect('SELECT vc.iVehicleCategoryId,vc.vBannerImage, vc.vLogo,vc.vListLogo1,vc.vCategory_'.$default_lang.' as vCategory, vc.eStatus, vc.iDisplayOrder,vc.eCatType,  (select count(iVehicleCategoryId) from '.$sql_vehicle_category_table_name." where iParentId = vc.iVehicleCategoryId AND eStatus != 'Deleted') as SubCategories FROM ".$sql_vehicle_category_table_name." as vc WHERE eStatus != 'Deleted' AND vc.iParentId='0' {$ssql}");

        foreach ($vehicle_category_data as $vehicle_category) {
            $statusNew = $status;
            if ('Active' === $status) {
                $checkLog = $obj->MySQLSelect("SELECT eStatus FROM vehicle_category_status_log WHERE iVehicleCategoryId = '".$vehicle_category['iVehicleCategoryId']."'");

                if (!empty($checkLog) && $checkLog > 0) {
                    $statusNew = $checkLog[0]['eStatus'];
                }
            }

            $obj->sql_query("UPDATE vehicle_category SET eStatus = '".$statusNew."' WHERE iVehicleCategoryId = '".$vehicle_category['iVehicleCategoryId']."'");

            $vehicle_category_new = $obj->MySQLSelect("SELECT iServiceId FROM vehicle_category WHERE iVehicleCategoryId = '".$vehicle_category['iVehicleCategoryId']."'");
            if ($vehicle_category_new[0]['iServiceId'] > 0) {
                $obj->sql_query("UPDATE service_categories SET eStatus = '{$statusNew}' WHERE iServiceId = '".$vehicle_category_new[0]['iServiceId']."'");
            }
        }

        if ('Ride' === $eType) {
            $statusVal = 'Active' === $status ? 'Yes' : 'No';
            $obj->sql_query("UPDATE configurations SET vValue = '{$statusVal}', eAdminDisplay = '{$statusVal}' WHERE vName = 'ENABLE_CORPORATE_PROFILE'");
        }

        header('Location:master_service_category.php');

        exit;
    }
    $_SESSION['success'] = '2';
    header('Location:master_service_category.php');

    exit;
}

$subquery = '';
if (!$MODULES_OBJ->isRideFeatureAvailable('Yes')) {
    $subquery .= " AND eType != 'Ride'";
}
if (!$MODULES_OBJ->isDeliveryFeatureAvailable('Yes') && !$MODULES_OBJ->isDeliverAllFeatureAvailable('Yes')) {
    $subquery .= " AND eType != 'Deliver'";
}
if (!$MODULES_OBJ->isUberXFeatureAvailable('Yes')) {
    $subquery .= " AND eType != 'UberX'";
}
if (!$MODULES_OBJ->isEnableVideoConsultingService()) {
    $subquery .= " AND eType != 'VideoConsult'";
}
if (!$MODULES_OBJ->isEnableBiddingServices()) {
    $subquery .= " AND eType != 'Bidding'";
}
if (!$MODULES_OBJ->isEnableMedicalServices()) {
    $subquery .= " AND eType != 'MedicalServices'";
}
if (!$MODULES_OBJ->isEnableRentItemService()) {
    $subquery .= " AND eType != 'RentItem'";
}
if (!$MODULES_OBJ->isEnableTrackServiceFeature()) {
    $subquery .= " AND eType != 'TrackService'";
}
if (!$MODULES_OBJ->isEnableRideShareService()) {
    $subquery .= " AND eType != 'RideShare'";
}
if (!$MODULES_OBJ->isEnableRentEstateService()) {
    $subquery .= " AND eType != 'RentEstate'";
}
if (!$MODULES_OBJ->isEnableRentCarsService()) {
    $subquery .= " AND eType != 'RentCars'";
}
if (!$MODULES_OBJ->isEnableNearByService()) {
    $subquery .= " AND eType != 'NearBy'";
}
$master_service_categories = $obj->MySQLSelect("SELECT *, JSON_UNQUOTE(JSON_EXTRACT(vCategoryName, '$.vCategoryName_".$default_lang."')) as vCategoryName FROM {$tbl_name} WHERE 1 = 1 {$subquery} AND eStatus != 'Deleted'");

foreach ($master_service_categories as $key => $value) {
    $category_data = [];
    $not_sql = ' AND iVehicleCategoryId != 297';
    $ssql = getMasterServiceCategoryQuery($value['eType'], 'Yes');

    if ('Ride' === $value['eType']) {
        $ssql .= " AND eForMedicalService = 'No' ";
    } elseif ('VideoConsult' === $value['eType']) {
        $ssql = getMasterServiceCategoryQuery($value['eType']);
        $vc_data = $obj->MySQLSelect("SELECT GROUP_CONCAT(DISTINCT(iParentId)) as ParentIds FROM vehicle_category WHERE eVideoConsultEnable = 'Yes'");
        $ssql .= ' AND iVehicleCategoryId IN ('.$vc_data[0]['ParentIds'].')';
    } elseif ('Bidding' === $value['eType']) {
        $category_data[0]['Total'] = $BIDDING_OBJ->getBiddingTotalCount('admin');
    } elseif ('RentItem' === $value['eType']) {
        $RentCatId = base64_encode(base64_encode($value['iMasterServiceCategoryId']));
        $category_data[0]['Total'] = $RENTITEM_OBJ->getRentItemTotalCount('admin', '', $RentCatId);
    } elseif ('RentEstate' === $value['eType']) {
        $EstateCatId = base64_encode(base64_encode($value['iMasterServiceCategoryId']));
        $category_data[0]['Total'] = $RENTITEM_OBJ->getRentItemTotalCount('admin', '', $EstateCatId);
    } elseif ('RentCars' === $value['eType']) {
        $CarCatId = base64_encode(base64_encode($value['iMasterServiceCategoryId']));
        $category_data[0]['Total'] = $RENTITEM_OBJ->getRentItemTotalCount('admin', '', $CarCatId);
    } elseif ('NearBy' === $value['eType']) {
        $category_data[0]['Total'] = $NEARBY_OBJ->getNearByCategoryTotalCount('admin');
    } elseif ('MedicalServices' === $value['eType']) {
        $ssql .= " AND eForMedicalService = 'Yes' ";
    }
    if (!in_array($value['eType'], ['Bidding', 'TrackService', 'RideShare', 'RentItem', 'RentEstate', 'RentCars', 'NearBy'], true)) {
        $parent_id_sql = " AND iParentId='0' ";
        if ('MedicalServices' === $value['eType']) {
            $parent_id_sql = " AND (iParentId='0' OR iParentId = '3') ";
        }
        $category_data = $obj->MySQLSelect('SELECT COUNT(iVehicleCategoryId) AS Total FROM '.$sql_vehicle_category_table_name."  WHERE  1 = 1 {$parent_id_sql} AND eStatus!='Deleted' {$ssql} {$not_sql}");
    }

    $master_service_categories[$key]['SubCategories'] = $category_data[0]['Total'];
}

?>
<!DOCTYPE html>
<html lang="en">
    <!-- BEGIN HEAD-->
    <head>
        <meta charset="UTF-8" />
        <title><?php echo $SITE_NAME; ?> | Master Service Categories</title>
        <meta content="width=device-width, initial-scale=1.0" name="viewport" />
        <?php include_once 'global_files.php'; ?>
        <style type="text/css">
            .table > tbody > tr > td {
                vertical-align: middle;
            }

            .vc-info {
                font-size: 20px;
                margin: 0 0 0 10px;
                position: absolute;
                color: #ff0000;
                cursor: pointer;
            }
        </style>
    </head>
    <!-- END  HEAD-->

    <!-- BEGIN BODY-->
    <body class="padTop53 " >
        <!-- Main LOading -->
        <!-- MAIN WRAPPER -->
        <div id="wrap">
            <?php include_once 'header.php'; ?>
            <?php include_once 'left_menu.php'; ?>

            <!--PAGE CONTENT -->
            <div id="content">
                <div class="inner">
                    <div id="add-hide-show-div">
                        <div class="row">
                            <div class="col-lg-12">
                                <h2>Master Service Categories</h2>
                            </div>
                        </div>
                        <hr />
                    </div>
                    <?php include 'valid_msg.php'; ?>

                    <div class="table-list">
                        <div class="row">
                            <div class="col-lg-12">
                                <div style="clear:both;"></div>
                                <div class="table-responsive">
                                    <form class="_list_form" id="_list_form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                        <table class="table table-striped table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <?php if ('Yes' === $THEME_OBJ->isCubeJekXv3ThemeActive()) { ?>
                                                    <th style="width: 100px; text-align: center;">Icon</th>
                                                    <?php } else { ?>
                                                    <th style="width: 100px; text-align: center;">Banner</th>
                                                    <?php } ?>
                                                    <th>Category Name</th>
                                                    <th style="width: 200px; text-align: center;">Service Category</th>
                                                    <th style="width: 150px; text-align: center;">Display Order</th>
                                                    <th style="text-align: center;">Status</th>
                                                    <th style="text-align: center;">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($master_service_categories) && count($master_service_categories) > 0) {
                                                    foreach ($master_service_categories as $service_category) {
                                                        $iMasterServiceCategoryId = $service_category['iMasterServiceCategoryId'];
                                                        $eStatus = $service_category['eStatus'];
                                                        $eType = $service_category['eType'];
                                                        $vIconImage = $service_category['vIconImage'];
                                                        if ($MODULES_OBJ->isEnableAppHomeScreenLayout()) {
                                                            $vIconImage = $service_category['vIconImage1'];
                                                        }
                                                        ?>
                                                    <tr>
                                                        <td style="text-align: center;">
                                                            <?php if ('Yes' === $THEME_OBJ->isServiceXv2ThemeActive() || 'Yes' === $THEME_OBJ->isCubeXv2ThemeActive()) {
                                                                if ('UberX' === $eType) {
                                                                    echo '--';
                                                                } else { ?>
                                                                <img src="<?php echo $tconfig['tsite_url'].'resizeImg.php?h=50&src='.$tconfig['tsite_upload_app_home_screen_images'].$vIconImage; ?>">
                                                            <?php }
                                                                } else { ?>
                                                            <img src="<?php echo $tconfig['tsite_url'].'resizeImg.php?w=50&src='.$tconfig['tsite_upload_app_home_screen_images'].$vIconImage; ?>">
                                                            <?php } ?>
                                                        </td>
                                                        <td><?php echo $service_category['vCategoryName']; ?></td>
                                                        <td style="text-align: center;">
                                                            <?php if ('Bidding' === $eType) { ?>
                                                                <a class="add-btn-sub" href="bidding_master_category.php" target="_blank">Add/View (<?php echo $service_category['SubCategories']; ?>) </a>
                                                             <?php } elseif ('RentItem' === $eType) { ?>
                                                                 <a class="add-btn-sub" href="bsr_master_category.php?eType=RentItem" target="_blank"><?php echo 'UberX' === $eType ? 'Add/View' : $langage_lbl_admin['LBL_SERVICES']; ?> (<?php echo $service_category['SubCategories']; ?>) </a>
                                                             <?php } elseif ('RentEstate' === $eType) { ?>
                                                                 <a class="add-btn-sub" href="bsr_master_category.php?eType=RentEstate" target="_blank"><?php echo 'UberX' === $eType ? 'Add/View' : $langage_lbl_admin['LBL_SERVICES']; ?> (<?php echo $service_category['SubCategories']; ?>) </a>
                                                             <?php } elseif ('RentCars' === $eType) { ?>
                                                                 <a class="add-btn-sub" href="bsr_master_category.php?eType=RentCars" target="_blank"><?php echo 'UberX' === $eType ? 'Add/View' : $langage_lbl_admin['LBL_SERVICES']; ?> (<?php echo $service_category['SubCategories']; ?>) </a>
                                                             <?php } elseif ('NearBy' === $eType) { ?>
                                                                <a class="add-btn-sub" href="near_by_category.php" target="_blank">Add/View (<?php echo $service_category['SubCategories']; ?>) </a>
                                                            <?php } elseif (in_array($eType, ['TrackService', 'RideShare'], true)) {
                                                                echo '--';
                                                            } else { ?>
                                                                <a class="add-btn-sub" href="vehicle_category.php?eType=<?php echo $eType.$eVideoConsult; ?>" target="_blank"><?php echo 'UberX' === $eType ? 'Add/View' : $langage_lbl_admin['LBL_SERVICES']; ?> (<?php echo $service_category['SubCategories']; ?>) </a>
                                                            <?php } ?>
                                                        </td>
                                                        <td style="text-align: center;"><?php echo $service_category['iDisplayOrder']; ?></td>
                                                        <td style="text-align: center;">
                                                            <?php
                                                            if ('Active' === $service_category['eStatus']) {
                                                                $status_img = 'img/active-icon.png';
                                                            } else {
                                                                $status_img = 'img/inactive-icon.png';
                                                            }
                                                        ?>
                                                            <img src="<?php echo $status_img; ?>" alt="image" data-toggle="tooltip" title="<?php echo $service_category['eStatus']; ?>">
                                                        </td>
                                                        <td align="center" style="text-align:center;" class="action-btn001">
                                                            <div class="share-button openHoverAction-class" style="display: block;">
                                                                <label class="entypo-export"><span><img src="images/settings-icon.png" alt=""></span></label>
                                                                <div class="social show-moreOptions for-two openPops_<?php echo $iMasterServiceCategoryId; ?>">
                                                                    <ul>
                                                                        <li class="entypo-twitter" data-network="twitter">
                                                                            <a href="master_service_category_action.php?id=<?php echo $iMasterServiceCategoryId; ?>" data-toggle="tooltip" title="Edit">
                                                                                <img src="img/edit-icon.png" alt="Edit">
                                                                            </a>
                                                                        </li>
                                                                        <?php if ($userObj->hasPermission('update-status-master-service-category')) { ?>
                                                                            <li class="entypo-facebook" data-network="facebook">
                                                                                <a href="javascript:void(0);" onClick="window.location.href='master_service_category.php?id=<?php echo $iMasterServiceCategoryId; ?>&status=Active&eType=<?php echo $eType; ?>'"  data-toggle="tooltip" title="Activate"><img src="img/active-icon.png" alt="<?php echo $eStatus; ?>" ></a>
                                                                            </li>
                                                                            <li class="entypo-gplus" data-network="gplus">
                                                                                <a href="javascript:void(0);" onClick="window.location.href='master_service_category.php?id=<?php echo $iMasterServiceCategoryId; ?>&status=Inactive&eType=<?php echo $eType; ?>'" data-toggle="tooltip" title="Deactivate">
                                                                                    <img src="img/inactive-icon.png" alt="<?php echo $eStatus; ?>" >
                                                                                </a>
                                                                            </li>
                                                                        <?php } ?>
                                                                    </ul>
                                                                </div>
                                                                <?php if ($CONFIG_OBJ->isOnlyCashPaymentModeAvailable() && $MODULES_OBJ->isEnableVideoConsultingService() && 'VideoConsult' === $eType) { ?>
                                                                <span class="vc-info" data-toggle="tooltip" data-original-title="Video Consulting Service is not available in the applications as only Cash payment option is available in the system."><i class="fa fa-exclamation-triangle"></i></span>
                                                                <?php } ?>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php }
                                                    } else { ?>
                                                <tr>
                                                    <td colspan="5">No records found.</td>
                                                </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </form>
                                    <?php include 'pagination_n.php'; ?>
                                </div>
                            </div> <!--TABLE-END-->
                        </div>
                    </div>
                    <div class="admin-notes">
                        <h4>Notes:</h4>
                        <ul>
                            <li>Administrator can Activate / Deactivate / Modify any Master Service Category.</li>
                            <?php if ($CONFIG_OBJ->isOnlyCashPaymentModeAvailable() && $MODULES_OBJ->isEnableVideoConsultingService()) { ?>
                            <li><strong>Video Consulting</strong> Service is not available in the applications as only <strong>Cash</strong> payment option is available in the system.</li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            </div>
            <!--END PAGE CONTENT -->
        </div>
        <!--END MAIN WRAPPER -->

        <?php include_once 'footer.php'; ?>
        <script>
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
    </body>
    <!-- END BODY-->
</html>