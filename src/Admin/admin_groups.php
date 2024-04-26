<?php
include_once '../common.php';
if (!$userObj->hasPermission('view-admin-group')) {
    $userObj->redirect();
}
$script = 'AdminGroups';
// Start Sorting
$sortby = $_REQUEST['sortby'] ?? 0;
$order = $_REQUEST['order'] ?? '';
$ord = ' ORDER BY vGroup ASC';
if (2 === $sortby) {
    if (0 === $order) {
        $ord = ' ORDER BY eStatus ASC';
    } else {
        $ord = ' ORDER BY eStatus DESC';
    }
}
if (1 === $sortby) {
    if (0 === $order) {
        $ord = ' ORDER BY vGroup ASC';
    } else {
        $ord = ' ORDER BY vGroup DESC';
    }
}
// End Sorting
// Start Search Parameters
$option = isset($_REQUEST['option']) ? stripslashes($_REQUEST['option']) : '';
$keyword = isset($_REQUEST['keyword']) ? stripslashes($_REQUEST['keyword']) : '';
$eStatus = isset($_REQUEST['eStatus']) ? stripslashes($_REQUEST['eStatus']) : '';
$ssql = '';
if ('' !== $keyword) {
    if ('' !== $option) {
        if (str_contains($option, 'eStatus')) {
            $ssql .= ' AND ag.'.stripslashes($option)." LIKE '".clean($keyword)."'";
        } elseif ('vGroup' === $option) {
            $ssql .= " AND vGroup LIKE '%".clean($keyword)."%'";
        } else {
            $ssql .= ' AND '.stripslashes($option)." LIKE '%".clean($keyword)."%'";
        }
    } else {
        // changed by me
        if (!empty($keyword)) {
            $keyword_qry = $keyword;
            $keyword_qry = str_replace(' ', '_', $keyword_qry);
        }
        $myssql .= " AND permission_name LIKE  '%".clean($keyword_qry)."%' OR name LIKE '%".clean($keyword)."%' OR vGroup LIKE '%".clean($keyword)."%' OR eStatus LIKE '%".clean($keyword)."%'";
        $sql = "SELECT iGroupId
        FROM admin_groups as ag
            LEFT JOIN admin_pro_group_permission as agp ON agp.group_id = ag.iGroupId
            LEFT JOIN admin_pro_permissions as ap ON agp.permission_id = ap.id
        WHERE 1=1 {$eStatussql} {$myssql}";
        $sql_data = $obj->MySQLSelect($sql);
        foreach ($sql_data as $key => $value) {
            $grpid[] = $value['iGroupId'];
        }
        $implode_grpid = implode(',', $grpid);
        $ssql .= " AND iGroupId IN ({$implode_grpid})";
    }
}
if (ONLYDELIVERALL === 'Yes' || 'Yes' === $THEME_OBJ->isRideCXThemeActive() || 'Yes' === $THEME_OBJ->isRideDeliveryXThemeActive() || 'Yes' === $THEME_OBJ->isDeliveryXThemeActive() || 'Yes' === $THEME_OBJ->isServiceXThemeActive() || 'Yes' === $THEME_OBJ->isServiceXv2ThemeActive() || 'Yes' === $THEME_OBJ->isRideCXv2ThemeActive()) {
    // $ssql .= " AND iGroupId != 4";
}
$hotelPanel = ($MODULES_OBJ->isEnableHotelPanel()) ? 'Yes' : 'No';
$kioskPanel = ($MODULES_OBJ->isEnableKioskPanel()) ? 'Yes' : 'No';
if (ONLYDELIVERALL === 'Yes' || ('No' === $hotelPanel && 'No' === $kioskPanel)) {
    $ssql .= ' AND iGroupId != 4';
}

if (1 !== $_SESSION['sess_iGroupId']) {
    $ssql .= " AND iGroupId = {$_SESSION['sess_iGroupId']}";
}
if ('eStatus' === $option) {
    $eStatussql = " AND ag.eStatus = '".ucfirst($keyword)."'";
} else {
    $eStatussql = " AND ag.eStatus != 'Deleted'";
}
if ('' !== $eStatus) {
    $eStatussql = " AND ag.eStatus = '".ucfirst($eStatus)."'";
}
// End Search Parameters
// Pagination Start
$per_page = $DISPLAY_RECORD_NUMBER; // number of results to show per page
$sql = "SELECT COUNT(iGroupId) AS Total FROM admin_groups as ag WHERE 1=1 {$eStatussql} {$ssql}";

/*$sql = "SELECT COUNT(iGroupId) AS Total
        FROM admin_groups as ag
            LEFT JOIN admin_group_permission as agp ON agp.group_id = ag.iGroupId
            LEFT JOIN admin_permissions as ap ON agp.permission_id = ap.id
        WHERE 1=1 $eStatussql $ssql";*/
$totalData = $obj->MySQLSelect($sql);
$total_results = $totalData[0]['Total'];
$total_pages = ceil($total_results / $per_page); // total pages we going to have
$show_page = 1;
// -------------if page is setcheck------------------//
if (isset($_GET['page'])) {
    $show_page = $_GET['page'];             // it will telles the current page
    if ($show_page > 0 && $show_page <= $total_pages) {
        $start = ($show_page - 1) * $per_page;
        $end = $start + $per_page;
    } else {
        // error - show first set of results
        $start = 0;
        $end = $per_page;
    }
} else {
    // if page isn't set, show first set of results
    $start = 0;
    $end = $per_page;
}
// display pagination
$page = isset($_GET['page']) ? (int) ($_GET['page']) : 0;
$tpages = $total_pages;
if ($page <= 0) {
    $page = 1;
}
// Pagination End
$uberxService = $MODULES_OBJ->isUberXFeatureAvailable('Yes') ? 'Yes' : 'No';
$rideEnable = $MODULES_OBJ->isRideFeatureAvailable('Yes') ? 'Yes' : 'No';
$deliveryEnable = $MODULES_OBJ->isDeliveryFeatureAvailable('Yes') ? 'Yes' : 'No';
$deliverallEnable = $MODULES_OBJ->isDeliverAllFeatureAvailable('Yes') ? 'Yes' : 'No';
$biddingEnable = $MODULES_OBJ->isEnableBiddingServices('Yes') ? 'Yes' : 'No';
$nearbyEnable = $MODULES_OBJ->isEnableNearByService('Yes') ? 'Yes' : 'No';
$trackServiceEnable = $MODULES_OBJ->isEnableTrackServiceFeature('Yes') ? 'Yes' : 'No';
$trackAnyServiceEnable = $MODULES_OBJ->isEnableTrackAnyServiceFeature('Yes') ? 'Yes' : 'No';
$rideShareEnable = $MODULES_OBJ->isEnableRideShareService('Yes') ? 'Yes' : 'No';
$rentitemEnable = $MODULES_OBJ->isEnableRentItemService('Yes') ? 'Yes' : 'No';
$rentestateEnable = $MODULES_OBJ->isEnableRentEstateService('Yes') ? 'Yes' : 'No';
$rentcarEnable = $MODULES_OBJ->isEnableRentCarsService('Yes') ? 'Yes' : 'No';
$genieEnable = GENIE_ENABLED;
$runnerEnable = RUNNER_ENABLED;

$flymodule = 'No';
if ($MODULES_OBJ->isAirFlightModuleAvailable('', 'Yes')) {
    $flymodule = 'Yes';
}
$sql_permission = " AND apdg.eStatus = 'Active' AND ap.status = 'Active' ";
if ('No' === $uberxService) {
    $sql_permission .= " AND !FIND_IN_SET('UberX', ap.eFor)  ";
}
if ('No' === $rideEnable) {
    $sql_permission .= " AND !FIND_IN_SET('Ride', ap.eFor) AND !FIND_IN_SET('Kiosk', ap.eFor) ";
}
if ('No' === $deliveryEnable) {
    $sql_permission .= " AND !FIND_IN_SET('Delivery', ap.eFor) ";
}
if ('No' === $deliverallEnable) {
    $sql_permission .= " AND !FIND_IN_SET('DeliverAll', ap.eFor) ";
}
if ('No' === $biddingEnable) {
    $sql_permission .= " AND ap.eFor != 'Bidding' ";
}
if ('No' === $nearbyEnable) {
    $sql_permission .= " AND ap.eFor != 'NearBy' ";
}
if ('No' === $trackServiceEnable) {
    $sql_permission .= " AND ap.eFor != 'TrackService' ";
}
if ('No' === $trackAnyServiceEnable) {
    $sql_permission .= " AND ap.eFor != 'TrackAnyService' ";
}
if ('No' === $rideShareEnable) {
    $sql_permission .= " AND ap.eFor != 'RideShare' ";
}
if ('No' === $rentitemEnable) {
    $sql_permission .= " AND ap.eFor != 'RentItem' ";
}
if ('No' === $rentestateEnable) {
    $sql_permission .= " AND ap.eFor != 'RentEstate' ";
}
if ('No' === $rentcarEnable) {
    $sql_permission .= " AND ap.eFor != 'RentCars' ";
}

if ('No' === $genieEnable) {
    $sql_permission .= " AND ap.eFor != 'Delivery' ";
}

$value1 = $obj->MySQLSelect('SELECT * from admin_permissions');

$permission_id = '';
foreach ($value1 as $key => $value12) {
    $eForConfig = explode(',', $value12['eFor']);
    if (1 === count($eForConfig)) {
        $eForConfig = $eForConfig[0];
    }

    $eForConfigArr = is_array($eForConfig) ? $eForConfig : [$eForConfig];

    if ('view-user-outstanding-sort-group' === $value12['permission_name'] || 'view-app-screen-label' === $value12['permission_name'] || 'view-app-screenshot' === $value12['permission_name'] || 'view-app-home-settings' === $value12['permission_name'] || 'view-app-screen' === $value12['permission_name']) {
    } elseif (('' === $value12['eFor'] || 'General' === $value12['eFor']) || ('Yes' === $biddingEnable && 'Bidding' === $value12['eFor']) || ('Yes' === $nearbyEnable && 'NearBy' === $value12['eFor']) || ('Yes' === $trackServiceEnable && 'TrackService' === $value12['eFor']) || ('Yes' === $rideShareEnable && 'RideShare' === $value12['eFor']) || ('Yes' === $flymodule && 'Fly' === $value12['eFor']) || (ENABLEKIOSKPANEL === 'Yes' && 'Kiosk' === $value12['eFor']) || ('YES' === strtoupper($deliverallEnable) && ('DeliverAll' === $eForConfig || in_array('DeliverAll', $eForConfigArr, true))) || ('YES' === strtoupper($rideEnable) && ('Ride' === $eForConfig || in_array('Ride', $eForConfigArr, true))) || ('YES' === strtoupper($deliveryEnable) && ('Delivery' === $eForConfig || 'Multi-Delivery' === $eForConfig || in_array('Delivery', $eForConfigArr, true))) || ('YES' === strtoupper($uberxService) && ('UberX' === $eForConfig || in_array('UberX', $eForConfigArr, true)))) {
        $permission_id .= ','.$value12['id'];
    }
}

$sql = "SELECT ag.*, REPLACE(GROUP_CONCAT(ap.permission_name ORDER BY ap.id ASC SEPARATOR ', '), '-', ' ') permission_name, GROUP_CONCAT(CONCAT(ap.name, '|', apdg.name, IF(apdsg.name != '', CONCAT(' ➜ ', apdsg.name), '')) ORDER BY ap.id ASC SEPARATOR '##') as name
        FROM admin_groups as ag
            LEFT JOIN admin_pro_group_permission as agp ON agp.group_id = ag.iGroupId
            LEFT JOIN admin_pro_permissions as ap ON agp.permission_id = ap.id
            LEFT JOIN admin_pro_permission_display_groups as apdg ON apdg.id = ap.display_group_id
            LEFT JOIN admin_pro_permission_display_sub_groups as apdsg ON apdsg.id = ap.display_sub_group_id
        WHERE 1=1 {$eStatussql} {$ssql} {$sql_permission} GROUP BY ag.iGroupId {$ord} LIMIT {$start}, {$per_page}";

$data_drv = $obj->MySQLSelect($sql);

$endRecord = count($data_drv);
$var_filter = '';
foreach ($_REQUEST as $key => $val) {
    if ('tpages' !== $key && 'page' !== $key) {
        $var_filter .= "&{$key}=".stripslashes($val);
    }
}
$reload = $_SERVER['PHP_SELF'].'?tpages='.$tpages.$var_filter;
?>
<!DOCTYPE html>
<html lang="en">
<!-- BEGIN HEAD-->
<head>
    <meta charset="UTF-8"/>
    <title><?php echo $SITE_NAME; ?> | Admin Groups</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <?php include_once 'global_files.php'; ?>
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
            <div id="add-hide-show-div">
                <div class="row">
                    <div class="col-lg-12">
                        <h2>Admin Groups</h2>
                        <!--<input type="button" id="" value="ADD A DRIVER" class="add-btn">-->
                    </div>
                </div>
                <hr/>
            </div>
            <?php include 'valid_msg.php'; ?>
            <form name="frmsearch" id="frmsearch" action="javascript:void(0);" method="post">
                <table width="100%" border="0" cellpadding="0" cellspacing="0" class="admin-nir-table">
                    <tbody>
                    <tr>
                        <td width="1%">
                            <label for="textfield">
                                <strong>Search:</strong>
                            </label>
                        </td>
                        <td width="8%" class=" padding-right10">
                            <select name="option" id="option" class="form-control">
                                <option value="">All</option>
                                <option value="vGroup" <?php if ('vGroup' === $option) {
                                    echo 'selected';
                                } ?> >Group Name
                                </option>
                                <!--<option value="eStatus" <?php if ('eStatus' === $option) {
                                    echo 'selected';
                                } ?> >Status</option>-->
                            </select>
                        </td>
                        <td width="10%">
                            <input type="Text" id="keyword" name="keyword" value="<?php echo $keyword; ?>"
                                   class="form-control"/>
                        </td>
                        <?php if ($userObj->hasPermission(['update-status-admin-group', 'delete-admin-group'])) { ?>
                            <td width="13%">
                                <select name="eStatus" id="eStatus" class="form-control">
                                    <option value="">Select Status</option>
                                    <?php if ($userObj->hasPermission('update-status-admin-group')) { ?>
                                        <option value="Active" <?php
                                        if ('Active' === $eStatus) {
                                            echo 'selected';
                                        }
                                        ?>>Active
                                        </option>
                                        <option value="Inactive" <?php
                                        if ('Inactive' === $eStatus) {
                                            echo 'selected';
                                        }
                                        ?>>Inactive
                                        </option>
                                    <?php }
                                    if ($userObj->hasPermission('delete-admin-group')) { ?>
                                        <option value="Deleted" <?php // changed by me
                                        if ('Deleted' === $eStatus) { // changed by me
                                            echo 'selected';
                                        }
                                        ?>>Delete
                                        </option>
                                    <?php } ?>
                                </select>
                            </td>
                        <?php } ?>
                        <td>
                            <input type="submit" value="Search" class="btnalt button11" id="Search" name="Search"
                                   title="Search"/>
                            <input type="button" value="Reset" class="btnalt button11"
                                   onClick="window.location.href = 'admin_groups.php'"/>
                        </td>
                        <?php if ($userObj->hasPermission('create-admin-group')) { ?>
                            <td width="30%">
                                <a class="add-btn" href="admin_group_action.php" style="text-align: center;">Add Admin
                                    Group
                                </a>
                            </td>
                        <?php } ?>
                    </tr>
                    </tbody>
                </table>
            </form>
            <div class="table-list">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="admin-nir-export">
                            <div class="changeStatus col-lg-6 option-box-left">
                                        <span class="col-lg-3 new-select001">
                                            <select name="changeStatus" id="changeStatus" class="form-control"
                                                    onChange="ChangeStatusAll(this.value);">
                                                <option value="">Select Action</option>
                                                <option value='Active' <?php if ('Active' === $option) {
                                                    echo 'selected';
                                                } ?> >Activate</option>
                                                <option value="Inactive" <?php if ('Inactive' === $option) {
                                                    echo 'selected';
                                                } ?> >Deactivate</option>
                                                <option value="Deleted" <?php if ('Delete' === $option) {
                                                    echo 'selected';
                                                } ?> >Delete</option>
                                            </select>
                                        </span>
                            </div>
                        </div>
                        <div style="clear:both;"></div>
                        <div class="table-responsive">
                            <form class="_list_form" id="_list_form" method="post"
                                  action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                <table class="table table-striped table-bordered table-hover main">
                                    <thead>
                                    <tr>
                                        <th align="center" width="3%" style="text-align:center;">
                                            <input type="checkbox" id="setAllCheck">
                                        </th>
                                        <th width="20%">
                                            <a href="javascript:void(0);"
                                               onClick="Redirect(1,<?php if ('1' === $sortby) {
                                                   echo $order;
                                               } else { ?>0<?php } ?>)">Group Name <?php if (1 === $sortby) {
                                                   if (0 === $order) { ?>
                                                        <i class="fa fa-sort-amount-asc"
                                                           aria-hidden="true"></i> <?php } else { ?>
                                                        <i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php }
                                                           } else { ?>
                                                    <i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a>
                                        </th>
                                        <th width="60%">
                                            <a href="javascript:void(0);">Permissions
                                        </th>
                                        <th width="10%" align="center" style="text-align:center;">
                                            <a href="javascript:void(0);"
                                               onClick="Redirect(2,<?php if ('2' === $sortby) {
                                                   echo $order;
                                               } else { ?>0<?php } ?>)">Status <?php if (2 === $sortby) {
                                                   if (0 === $order) { ?>
                                                        <i class="fa fa-sort-amount-asc"
                                                           aria-hidden="true"></i> <?php } else { ?>
                                                        <i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php }
                                                           } else { ?>
                                                    <i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a>
                                        </th>
                                        <th width="10%" align="center" style="text-align:center;">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if (!empty($data_drv)) {
                                        for ($i = 0; $i < count($data_drv); ++$i) {
                                            $default = '';
                                            if ($_SESSION['sess_iAdminUserId'] === $data_drv[$i]['iGroupId']) {
                                                $default = 'disabled';
                                            }
                                            $group_permissions_arr = explode('##', $data_drv[$i]['name']);
                                            ?>
                                            <tr class="gradeA">
                                                <td align="center" style="text-align:center;">
                                                    <?php if ($_SESSION['sess_iAdminUserId'] === $data_drv[$i]['iGroupId'] || $data_drv[$i]['iGroupId'] <= 5) {
                                                    } else {
                                                        ?>
                                                        <input type="checkbox" id="checkbox"
                                                               name="checkbox[]" <?php echo $default; ?>
                                                               value="<?php echo $data_drv[$i]['iGroupId']; ?>"/>&nbsp;
                                                    <?php } ?>
                                                </td>
                                                <td><?php echo $data_drv[$i]['vGroup']; ?></td>
                                                <td>
                                                    <a href="javascript:void(0);" data-toggle="modal"
                                                       data-target="#permissions_modal_<?php echo $data_drv[$i]['iGroupId']; ?>">
                                                        View Permissions
                                                    </a>
                                                    <div class="modal fade"
                                                         id="permissions_modal_<?php echo $data_drv[$i]['iGroupId']; ?>"
                                                         tabindex="-1" role="dialog" aria-hidden="true"
                                                         data-backdrop="static" data-keyboard="false">
                                                        <div class="modal-dialog modal-lg">
                                                            <div class="modal-content nimot-class">
                                                                <div class="modal-header">
                                                                    <h4>
                                                                        Permissions (<?php echo $data_drv[$i]['vGroup']; ?>)
                                                                        <button type="button" class="close"
                                                                                data-dismiss="modal">x
                                                                        </button>
                                                                    </h4>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <table class="table table-striped table-bordered table-hover"
                                                                           style="margin-bottom: 0;">
                                                                        <thead>
                                                                        <tr>
                                                                            <th style="border-right: none;">
                                                                                <span style="display: flex; align-items: center;">Search:&nbsp;&nbsp;<input
                                                                                            class="form-control search-permission"
                                                                                            type="text"
                                                                                            placeholder="Search Permission"></span>
                                                                            </th>
                                                                            <th style="border-left: none;"></th>
                                                                        </tr>
                                                                        </thead>
                                                                        <thead>
                                                                        <tr>
                                                                            <th>Permission Name</th>
                                                                            <th>Permission Group</th>
                                                                        </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                        <?php
                                                                        foreach ($group_permissions_arr as $permission_name) {
                                                                            $permission_name_arr = explode('|', $permission_name);
                                                                            ?>
                                                                            <tr class="permissions-row"
                                                                                style="text-transform: capitalize;">
                                                                                <td><?php echo $permission_name_arr[0]; ?></td>
                                                                                <td><?php echo $permission_name_arr[1]; ?></td>
                                                                            </tr>
                                                                        <?php } ?>
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                                <div class="modal-footer" style="text-align: left">
                                                                    <button type="button" class="btn btn-default"
                                                                            data-dismiss="modal">Close
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td align="center" style="text-align:center;">
                                                    <?php
                                                    if ('Active' === $data_drv[$i]['eStatus']) {
                                                        $dis_img = 'img/active-icon.png';
                                                    } elseif ('Inactive' === $data_drv[$i]['eStatus']) {
                                                        $dis_img = 'img/inactive-icon.png';
                                                    } elseif ('Deleted' === $data_drv[$i]['eStatus']) {
                                                        $dis_img = 'img/delete-icon.png';
                                                    }
                                            ?>
                                                    <img src="<?php echo $dis_img; ?>" alt="image" data-toggle="tooltip"
                                                         title="<?php echo $data_drv[$i]['eStatus']; ?>">
                                                </td>
                                                <td align="center" style="text-align:center;" class="action-btn001">
                                                    <?php if ($_SESSION['sess_iAdminUserId'] === $data_drv[$i]['iGroupId'] || $data_drv[$i]['iGroupId'] <= 5) {
                                                        if ($userObj->hasPermission('edit-admin-group')) { ?>
                                                            <a href="admin_group_action.php?id=<?php echo $data_drv[$i]['iGroupId']; ?>"
                                                               data-toggle="tooltip" title="Edit">
                                                                <img src="img/edit-icon.png" alt="Edit">
                                                            </a>
                                                        <?php } else { ?>
                                                            --
                                                        <?php } ?>
                                                    <?php } else { ?>
                                                        <?php if ($userObj->hasPermission(['edit-admin-group', 'update-status-admin-group', 'delete-admin-group'])) { ?>
                                                            <div class="share-button share-button4 openHoverAction-class"
                                                                 style="display: block;">
                                                                <label class="entypo-export">
                                                                    <span><img src="images/settings-icon.png"
                                                                               alt=""></span>
                                                                </label>
                                                                <div class="social show-moreOptions openPops_<?php echo $data_drv[$i]['iGroupId']; ?>">
                                                                    <ul>
                                                                        <?php if ($userObj->hasPermission('edit-admin-group')) { ?>
                                                                            <li class="entypo-twitter"
                                                                                data-network="twitter">
                                                                                <a href="admin_group_action.php?id=<?php echo $data_drv[$i]['iGroupId']; ?>"
                                                                                   data-toggle="tooltip" title="Edit">
                                                                                    <img src="img/edit-icon.png"
                                                                                         alt="Edit">
                                                                                </a>
                                                                            </li>
                                                                        <?php }
                                                                        if ($userObj->hasPermission('update-status-admin-group')) { ?>
                                                                            <li class="entypo-facebook"
                                                                                data-network="facebook">
                                                                                <a href="javascript:void(0);"
                                                                                   onClick="changeStatus('<?php echo $data_drv[$i]['iGroupId']; ?>', 'Inactive')"
                                                                                   data-toggle="tooltip"
                                                                                   title="Activate">
                                                                                    <img src="img/active-icon.png"
                                                                                         alt="<?php echo $data_drv[$i]['eStatus']; ?>">
                                                                                </a>
                                                                            </li>
                                                                            <li class="entypo-gplus"
                                                                                data-network="gplus">
                                                                                <a href="javascript:void(0);"
                                                                                   onClick="changeStatus('<?php echo $data_drv[$i]['iGroupId']; ?>', 'Active')"
                                                                                   data-toggle="tooltip"
                                                                                   title="Deactivate">
                                                                                    <img src="img/inactive-icon.png"
                                                                                         alt="<?php echo $data_drv[$i]['eStatus']; ?>">
                                                                                </a>
                                                                            </li>
                                                                        <?php }
                                                                        if ($userObj->hasPermission('delete-admin-group') && 'Deleted' !== $eStatus) { ?>
                                                                            <li class="entypo-gplus"
                                                                                data-network="gplus">
                                                                                <a href="javascript:void(0);"
                                                                                   onClick="changeStatusDelete('<?php echo $data_drv[$i]['iGroupId']; ?>')"
                                                                                   data-toggle="tooltip" title="Delete">
                                                                                    <img src="img/delete-icon.png"
                                                                                         alt="Delete">
                                                                                </a>
                                                                            </li>
                                                                        <?php } ?>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        <?php } else { ?>
                                                            --
                                                        <?php } ?>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                        <?php }
                                        } else { ?>
                                        <tr class="gradeA">
                                            <td colspan="7"><?php echo $langage_lbl_admin['LBL_NO_RECORDS_FOUND1']; ?></td>
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
                    <li>
                        Administrator module will list all administrators on this page.
                    </li>
                    <li>
                        Administrator can Activate / Deactivate / Delete any administrator. Super Admin cannot be
                        Activated / Deactivated / Deleted.
                    </li>
                    <!-- <li>
                        Administrator can export data in XLS or PDF format.
                    </li> -->
                    <!--li>
                            "Export by Search Data" will export only search result data in XLS or PDF format.
                    </li-->
                </ul>
            </div>
        </div>
    </div>
    <!--END PAGE CONTENT -->
</div>
<!--END MAIN WRAPPER -->
<form name="pageForm" id="pageForm" action="action/admin_groups.php" method="post">
    <input type="hidden" name="page" id="page" value="<?php echo $page; ?>">
    <input type="hidden" name="tpages" id="tpages" value="<?php echo $tpages; ?>">
    <input type="hidden" name="iGroupId" id="iMainId01" value="">
    <input type="hidden" name="status" id="status01" value="">
    <input type="hidden" name="statusVal" id="statusVal" value="">
    <input type="hidden" name="option" value="<?php echo $option; ?>">
    <input type="hidden" name="keyword" value="<?php echo $keyword; ?>">
    <input type="hidden" name="sortby" id="sortby" value="<?php echo $sortby; ?>">
    <input type="hidden" name="order" id="order" value="<?php echo $order; ?>">
    <input type="hidden" name="method" id="method" value="">
</form>
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
        var action = $("#_list_form").attr('action');
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

    $(document).ready(function () {
        $('.search-permission').keyup(function () {
            var value = $(this).val();
            var items = $(this).closest('.table').find('.permissions-row')
            if (value != "" && value != undefined && value != null) {
                items.hide();
                items.each(function () {
                    var text = $(this).find('td').text().toLowerCase();

                    value = value.toLowerCase();

                    if (text.search(value) >= 0) {
                        $(this).show();
                    }
                })
            } else {
                items.show();
            }
        });
    });

    $(document).ready(function () {
        $('.table-responsive .main > tbody > tr').length <= 2 ? $('.table-responsive').addClass('less-child') : $('.table-responsive').removeClass('less-child');
    });
</script>
</body>
<!-- END BODY-->
</html>