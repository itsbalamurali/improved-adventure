<?php

use Models\Administrator;

include_once '../common.php';
$admin = $_REQUEST['admin'] ?? '';

if (!$userObj->hasPermission('view-admin') && 'hotels' !== $admin) {
    $userObj->redirect();
} elseif (!$userObj->hasPermission('view-hotel') && 'hotels' === $admin) {
    $userObj->redirect();
}
$create = 'create-admin';
$edit = 'edit-admin';
$delete = 'delete-admin';
$updateStatus = 'update-status-admin';
$urlAppend = '';
if ('hotels' === $admin) {
    $create = 'create-hotel';
    $edit = 'edit-hotel';
    $delete = 'delete-hotel';
    $updateStatus = 'update-status-hotel';
    $urlAppend = '&admin=hotels';
}
$script = 'Admin';
$query = Administrator::with([
    'roles',
    'locations',
]);
$sortby = $_REQUEST['sortby'] ?? 0;
$order = $_REQUEST['order'] ?? '';

switch ($sortby) {
    case 1:
        $query->orderBy('vFirstName', $order);

        break;

    case 2:
        $query->orderBy('vEmail', $order);

        break;

    case 3:
        // $query->orderBy('iGroupId', $order);
        break;

    case 4:
        $query->orderBy('eStatus', $order);

        break;

    default:
        break;
}
$hotelPanel = ($MODULES_OBJ->isEnableHotelPanel()) ? 'Yes' : 'No';
$kioskPanel = ($MODULES_OBJ->isEnableKioskPanel()) ? 'Yes' : 'No';
$ssql = '';
if (ONLYDELIVERALL === 'Yes' || 'Yes' === $THEME_OBJ->isRideCXThemeActive() || 'Yes' === $THEME_OBJ->isRideDeliveryXThemeActive() || 'Yes' === $THEME_OBJ->isDeliveryXThemeActive() || 'Yes' === $THEME_OBJ->isDeliveryXv2ThemeActive() || 'Yes' === $THEME_OBJ->isServiceXThemeActive() || 'Yes' === $THEME_OBJ->isServiceXv2ThemeActive() || 'Yes' === $THEME_OBJ->isRideCXv2ThemeActive() || 'No' === $hotelPanel) {
    $ssql .= ' AND iGroupId != 4';
}
$ssql .= ' AND iGroupId != 4';
$role_sql = "select * from admin_groups where eStatus = 'Active'".$ssql;
$role_sql_data = $obj->MySQLSelect($role_sql);
$option = isset($_REQUEST['option']) ? stripslashes($_REQUEST['option']) : '';
$keyword = isset($_REQUEST['keyword']) ? stripslashes($_REQUEST['keyword']) : '';
$searchDate = $_REQUEST['searchDate'] ?? '';
$eStatus = $_REQUEST['eStatus'] ?? '';
$eRole = isset($_REQUEST['eRole']) ? stripslashes($_REQUEST['eRole']) : '';
if (!empty($keyword)) {
    if (!empty($option)) {
        if ('eStatus' === $option) {
            // $query->where('eStatus', $StatusValue);
        } elseif ("concat(vFirstName,' ',vLastName)" === $option) {
            $query->where(DB::raw('concat(`vFirstName`," ",`vLastName`)'), 'LIKE', "%{$keyword}%");
        } elseif ('vContactNo' === $option) {
            $query->where('vContactNo', 'LIKE', "%{$keyword}%");
        } elseif ('vEmail' === $option) {
            $query->where('vEmail', 'LIKE', "%{$keyword}%");
        } elseif ('vGroup' === $option) {
            $sql = "SELECT iGroupId FROM admin_groups WHERE vGroup LIKE '%".$keyword."%' AND eStatus != 'Deleted'";
            $totalData = $obj->MySQLSelect($sql);
            $iGroupIdArr = [];
            if (count($totalData) > 0) {
                for ($t = 0; $t < count($totalData); ++$t) {
                    $iGroupIdArr[$t] = $totalData[$t]['iGroupId'];
                    $query->orWhere('iGroupId', $totalData[$t]['iGroupId']);
                }
            } elseif (0 === count($totalData)) {  // changed by me
                $query->orWhere('iGroupId', '');
            }
        } else {
            $query->where(static function ($q) use ($keyword): void {
                $q->where(DB::raw('concat(`vFirstName`," ",`vLastName`)'), 'LIKE', "%{$keyword}%");
                $q->orWhere('vEmail', 'LIKE', "%{$keyword}%");
                $q->orwhere('vContactNo', 'LIKE', "%{$keyword}%");
            });
            $sql = "SELECT iGroupId FROM admin_groups WHERE vGroup LIKE '%".$keyword."%' AND eStatus != 'Deleted'";
            $totalData = $obj->MySQLSelect($sql);
            $iGroupIdArr = [];
            if (count($totalData) > 0) {
                for ($t = 0; $t < count($totalData); ++$t) {
                    $iGroupIdArr[$t] = $totalData[$t]['iGroupId'];
                    $query->orWhere('iGroupId', $totalData[$t]['iGroupId']);
                }
            }
        }
    } else {
        $query->where(static function ($q) use ($keyword): void {
            $q->where(DB::raw('concat(`vFirstName`," ",`vLastName`)'), 'LIKE', "%{$keyword}%");
            $q->orWhere('vEmail', 'LIKE', "%{$keyword}%");
            $q->orwhere('vContactNo', 'LIKE', "%{$keyword}%");
            // $q->orwhere('eStatus', "LIKE", "%{$keyword}%");
        });
        $sql = "SELECT iGroupId FROM admin_groups WHERE vGroup LIKE '%".$keyword."%' AND eStatus != 'Deleted'";
        $totalData = $obj->MySQLSelect($sql);
        $iGroupIdArr = [];
        if (count($totalData) > 0) {
            for ($t = 0; $t < count($totalData); ++$t) {
                $iGroupIdArr[$t] = $totalData[$t]['iGroupId'];
                if (ONLYDELIVERALL === 'Yes' || ('No' === $hotelPanel && 'No' === $kioskPanel && '4' === $totalData[$t]['iGroupId'])) {
                    $query->where('iGroupId', '!=', '4');
                } else {
                    $query->orWhere('iGroupId', $totalData[$t]['iGroupId']);
                }
            }
        }
    }
} else {
    if ('eStatus' === $option) {
        $query->where('eStatus', $StatusValue);
    }
}
if ('' !== $eRole) {
    $query->where('iGroupId', $eRole);
}
if ('' !== $eStatus) {
    $query->where('eStatus', $eStatus);
}
if (!$userObj->hasRole(1)) {
    $query->where('iGroupId', $userObj->role_id);
}
if ('eStatus' !== $option && 'Deleted' !== $eStatus) {
    $query->where('eStatus', '!=', 'Deleted');
}
/*if (ONLYDELIVERALL == 'Yes' || ($hotelPanel == "No" && $kioskPanel == "No")) {
    $query->where('iGroupId', '!=', "4");
}*/
if ('hotels' === $admin) {
    $query->where('iGroupId', '=', '4');
    $script = 'Hotels';
} else {
    $query->where('iGroupId', '!=', '4');
}
$per_page = $DISPLAY_RECORD_NUMBER;
// Added By HJ On 18-10-2019 For Get Admin Data By Pagination Start
$start = 0;
if (isset($_REQUEST['page']) && $_REQUEST['page']) {
    $start = ($_REQUEST['page'] - 1) * $per_page;
}
// Added By HJ On 18-10-2019 For Get Admin Data By Pagination End
$query->take($per_page);
// exit;
$total_results = $query->count();
$total_pages = ceil($total_results / $per_page);
$data_drv = $query->offset($start)->get();
$endRecord = $data_drv->count();
$var_filter = '';
$end = $per_page;
if (isset($_GET['page'])) {
    $show_page = $_GET['page'];             // it will telles the current page
    if ($show_page > 0 && $show_page <= $total_pages) {
        $start = ($show_page - 1) * $per_page;
        $end = $start + $per_page;
    }
}
$page = isset($_GET['page']) ? (int) ($_GET['page']) : 0;
$tpages = $total_pages;
if ($page <= 0) {
    $page = 1;
}
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
    <title><?php echo $SITE_NAME; ?> | <?php if ('hotels' === $admin) { ?>
            Hotels
        <?php } else { ?>
            Administrator
        <?php } ?></title>
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
                        <?php if ('hotels' === $admin) { ?>
                            <h2>Hotels</h2>
                        <?php } else { ?>
                            <h2>Administrator</h2>
                        <?php } ?>
                        <!--<input type="button" id="" value="ADD A DRIVER" class="add-btn">-->
                    </div>
                </div>
                <hr/>
            </div>
            <?php include 'valid_msg.php'; ?>
            <form name="frmsearch" id="frmsearch" action="javascript:void(0);" method="post">
                <input type="hidden" name="admin" id="admin" value="<?php echo $admin; ?>">
                <table width="100%" border="0" cellpadding="0" cellspacing="0" class="admin-nir-table">
                    <tbody>
                    <tr>
                        <td width="1%">
                            <label for="textfield">
                                <strong>Search:</strong>
                            </label>
                        </td>
                        <td width="8%" class=" padding-right10">
                            <select name="option" id="option" class="form-control"
                                    onChange="return changeMyStatus(this);">
                                <option value="">All</option>
                                <option value="concat(vFirstName,' ',vLastName)" <?php
                                if ("concat(vFirstName,' ',vLastName)" === $option) {
                                    echo 'selected';
                                }
?> >Name
                                </option>
                                <option value="vEmail" <?php
if ('vEmail' === $option) {
    echo 'selected';
}
?> >E-mail
                                </option>
                                <option value="vGroup" <?php
if ('vGroup' === $option) {
    echo 'selected';
}
?> >Role
                                </option>
                                <!--<option value="vContactNo" <?php
if ('vContactNo' === $option) {
    echo 'selected';
}
?> >Mobile</option>

                                            <option value="eStatus" <?php
if ('eStatus' === $option) {
    echo 'selected';
}
?> >Status</option>-->
                            </select>
                        </td>
                        <td width="10%">
                            <input type="Text" id="keyword" name="keyword" value="<?php echo $keyword; ?>"
                                   class="form-control"/>
                        </td>
                        <td width="13%">
                            <select name="eStatus" id="StatusValue" class="form-control">
                                <option value="">Select Status</option>
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
                                <option value="Deleted" <?php
if ('Deleted' === $eStatus) {
    echo 'selected';
}
?>>Deleted
                                </option>
                            </select>
                        </td>
                        <?php if ('' === $admin) { ?>
                            <td width="15%">
                                <select name="eRole" id="RoleValue" class="form-control">
                                    <option value="">Select Role</option>
                                    <?php foreach ($role_sql_data as $role_value) { ?>
                                        <option value="<?php echo $role_value['iGroupId']; ?>" <?php if ($eRole === $role_value['iGroupId']) {
                                            echo 'selected';
                                        } ?>><?php echo $role_value['vGroup']; ?></option>
                                    <?php } ?>
                                </select>
                            </td>
                        <?php } ?>
                        <td>
                            <input type="submit" value="Search" class="btnalt button11" id="Search" name="Search"
                                   title="Search"/>
                            <input type="button" value="Reset" class="btnalt button11"
                                   onClick="window.location.href = 'admin.php'"/>
                        </td>
                        <?php if ($userObj->hasPermission($create)) { ?>
                            <td width="22%">
                                <?php if ('hotels' === $admin) { ?>
                                    <a class="add-btn" href="admin_action.php?admin=hotels" style="text-align: center;">
                                        Add
                                    </a>
                                <?php } else { ?>
                                    <a class="add-btn" href="admin_action.php" style="text-align: center;">Add</a>
                                <?php } ?>
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

                                            <?php if ($userObj->hasPermission([
                                                $updateStatus,
                                                $delete,
                                            ])) { ?>
                                                <select name="changeStatus" id="changeStatus" class="form-control"
                                                        onChange="ChangeStatusAll(this.value);">

                                                    <option value="">Select Action</option>

                                                    <?php if ($userObj->hasPermission($updateStatus)) { ?>
                                                        <option value='Active' <?php
                                                        if ('Active' === $option) {
                                                            echo 'selected';
                                                        }
                                                        ?> >Activate</option>
                                                        <option value="Inactive" <?php
                                                        if ('Inactive' === $option) {
                                                            echo 'selected';
                                                        }
                                                        ?> >Deactivate</option>
                                                    <?php } ?>

                                                    <?php if ($userObj->hasPermission($delete) && 'Deleted' !== $eStatus) { ?>
                                                        <option value="Deleted" <?php
                                                        if ('Delete' === $option) {
                                                            echo 'selected';
                                                        }
                                                        ?> >Delete</option>
                                                    <?php } ?>

                                                </select>
                                            <?php } ?>

                                        </span>
                            </div>
                            <?php if (!empty($data_drv)) { ?>
                                <!--<div class="panel-heading">

                                    <form name="_export_form" id="_export_form" method="post" >

                                        <button type="button" onClick="showExportTypes('admin')" >Export</button>

                                    </form>

                                </div>-->
                            <?php } ?>
                        </div>
                        <div style="clear:both;"></div>
                        <div class="table-responsive">
                            <form class="_list_form" id="_list_form" method="post"
                                  action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                    <tr>

                                        <?php if ($userObj->hasPermission([
                                            $updateStatus,
                                            $delete,
                                        ])) { ?>
                                        <th align="center" width="3%" style="text-align:center;">
                                            <input type="checkbox" id="setAllCheck">
                                        </th>
                                        <?php } ?>
                                        <th width="20%">
                                            <a href="javascript:void(0);" onClick="Redirect(1,<?php
                                            if ('1' === $sortby) {
                                                echo $order;
                                            } else {
                                                ?>0<?php } ?>)">Name <?php
                                                if (1 === $sortby) {
                                                    if (0 === $order) {
                                                        ?>
                                                        <i class="fa fa-sort-amount-asc"
                                                           aria-hidden="true"></i> <?php } else { ?>
                                                        <i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php
                                                           }
                                                } else {
                                                    ?>
                                                    <i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a>
                                        </th>
                                        <th width="20%">
                                            <a href="javascript:void(0);" onClick="Redirect(2,<?php
                                            if ('2' === $sortby) {
                                                echo $order;
                                            } else {
                                                ?>0<?php } ?>)">Email <?php
                                                if (2 === $sortby) {
                                                    if (0 === $order) {
                                                        ?>
                                                        <i class="fa fa-sort-amount-asc"
                                                           aria-hidden="true"></i> <?php } else { ?>
                                                        <i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php
                                                           }
                                                } else {
                                                    ?>
                                                    <i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a>
                                        </th>
                                        <th width="20%">
                                            <a href="javascript:void(0);" onClick="Redirect(3,<?php
                                            if ('3' === $sortby) {
                                                echo $order;
                                            } else {
                                                ?>0<?php } ?>)">Roles <?php
                                                if (3 === $sortby) {
                                                    if (0 === $order) {
                                                        ?>
                                                        <i class="fa fa-sort-amount-asc"
                                                           aria-hidden="true"></i> <?php } else { ?>
                                                        <i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php
                                                           }
                                                } else {
                                                    ?>
                                                    <i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a>
                                        </th>
                                        <!--  <th width="15%"><a href="javascript:void(0);" >Locations </th> -->
                                        <th width="8%" align="center" style="text-align:center;">
                                            <a href="javascript:void(0);" onClick="Redirect(4,<?php
                                            if ('4' === $sortby) {
                                                echo $order;
                                            } else {
                                                ?>0<?php } ?>)">Status <?php
                                                if (4 === $sortby) {
                                                    if (0 === $order) {
                                                        ?>
                                                        <i class="fa fa-sort-amount-asc"
                                                           aria-hidden="true"></i> <?php } else { ?>
                                                        <i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php
                                                           }
                                                } else {
                                                    ?>
                                                    <i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a>
                                        </th>
                                        <?php if ($userObj->hasPermission([$edit, $updateStatus, $delete])) { ?>
                                            <th width="8%" align="center" style="text-align:center;">Action</th>
                                        <?php } ?>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if (!empty($data_drv)) {
                                        foreach ($data_drv as $key => $row) {
                                            $default = '';
                                            if ($_SESSION['sess_iAdminUserId'] === $row['iAdminId']) {
                                                $default = 'disabled';
                                            }
                                            if ('' === $eStatus && 'Deleted' === $row['eStatus']) {
                                                continue;
                                            }
                                            if ('' !== $eStatus && $eStatus !== $row['eStatus']) {
                                                continue;
                                            }
                                            ?>
                                            <tr class="gradeA">

                                                <?php if ($userObj->hasPermission([
                                                    $updateStatus,
                                                    $delete,
                                                ])) { ?>
                                                <?php if (($_SESSION['sess_iAdminUserId'] === $row['iAdminId']) || 'Yes' === $row['eDefault']) { ?>
                                                    <td align="center" style="text-align:center;"></td>
                                                <?php } else { ?>
                                                    <td align="center" style="text-align:center;">
                                                        <input type="checkbox" id="checkbox"
                                                               name="checkbox[]" <?php echo $default; ?>
                                                               value="<?php echo $row['iAdminId']; ?>"/>&nbsp;
                                                    </td>
                                                <?php }
                                                } ?>
                                                <td><?php echo clearName($row['vFirstName'].' '.$row['vLastName']); ?></td>
                                                <td><?php echo clearEmail($row['vEmail']); ?></td>
                                                <td><?php echo $row->roles->vGroup; ?></td>
                                                <!-- <td><?php echo implode(', ', $row->locations->pluck('vLocationName')->toArray()); ?></td> -->
                                                <!--  <td><?php echo clearPhone($row['vContactNo']); ?></td> -->
                                                <td align="center" style="text-align:center;">
                                                    <?php
                                                    if ('Active' === $row['eStatus']) {
                                                        $dis_img = 'img/active-icon.png';
                                                    } elseif ('Inactive' === $row['eStatus']) {
                                                        $dis_img = 'img/inactive-icon.png';
                                                    } elseif ('Deleted' === $row['eStatus']) {
                                                        $dis_img = 'img/delete-icon.png';
                                                    }
                                            ?>
                                                    <img src="<?php echo $dis_img; ?>" alt="image" data-toggle="tooltip"
                                                         title="<?php echo $row['eStatus']; ?>">
                                                </td>
                                                <?php if ($userObj->hasPermission([
                                            $edit,
                                            $updateStatus,
                                            $delete,
                                                ])) { ?>
                                                <td align="center" style="text-align:center;" class="action-btn001">
                                                    <?php if ($_SESSION['sess_iAdminUserId'] === $row['iAdminId'] || 'Yes' === $row['eDefault']) { ?>

                                                            <?php if ($userObj->hasPermission($edit)) { ?>


                                                        <a href="admin_action.php?id=<?php echo $row['iAdminId']; ?><?php echo $urlAppend; ?>"
                                                           data-toggle="tooltip" title="Edit">
                                                            <img src="img/edit-icon.png" alt="Edit">
                                                        </a>
                                                        <?php } else {
                                                            echo '---';
                                                        } ?>
                                                    <?php } else { ?>
                                                        <?php if ($userObj->hasPermission([
                                                            $edit,
                                                            $updateStatus,
                                                            $delete,
                                                        ])) { ?>
                                                            <div class="share-button share-button4 openHoverAction-class"
                                                                 style="display: block;">
                                                                <label class="entypo-export">
                                                                    <span><img src="images/settings-icon.png"
                                                                               alt=""></span>
                                                                </label>
                                                                <div class="social show-moreOptions openPops_<?php echo $row['iAdminId']; ?>">
                                                                    <ul>
                                                                        <?php if ($userObj->hasPermission($edit)) { ?>
                                                                            <li class="entypo-twitter"
                                                                                data-network="twitter">
                                                                                <a href="admin_action.php?id=<?php echo $row['iAdminId']; ?><?php echo $urlAppend; ?>"
                                                                                   data-toggle="tooltip" title="Edit">
                                                                                    <img src="img/edit-icon.png"
                                                                                         alt="Edit">
                                                                                </a>
                                                                            </li>
                                                                        <?php } ?>

                                                                        <?php if ($userObj->hasPermission($updateStatus)) { ?>
                                                                            <li class="entypo-facebook"
                                                                                data-network="facebook">
                                                                                <a href="javascript:void(0);"
                                                                                   onClick="changeStatus('<?php echo $row['iAdminId']; ?>', 'Inactive')"
                                                                                   data-toggle="tooltip"
                                                                                   title="Activate">
                                                                                    <img src="img/active-icon.png"
                                                                                         alt="<?php echo $row['eStatus']; ?>">
                                                                                </a>
                                                                            </li>
                                                                            <li class="entypo-gplus"
                                                                                data-network="gplus">
                                                                                <a href="javascript:void(0);"
                                                                                   onClick="changeStatus('<?php echo $row['iAdminId']; ?>', 'Active')"
                                                                                   data-toggle="tooltip"
                                                                                   title="Deactivate">
                                                                                    <img src="img/inactive-icon.png"
                                                                                         alt="<?php echo $row['eStatus']; ?>">
                                                                                </a>
                                                                            </li>
                                                                        <?php } ?>

                                                                        <?php if ($userObj->hasPermission($delete) && $_SESSION['sess_iAdminUserId'] !== $row['iAdminId'] && 'Deleted' !== $row['eStatus']) { ?>
                                                                            <li class="entypo-gplus"
                                                                                data-network="gplus">
                                                                                <a href="javascript:void(0);"
                                                                                   onClick="changeStatusDelete('<?php echo $row['iAdminId']; ?>')"
                                                                                   data-toggle="tooltip" title="Delete">
                                                                                    <img src="img/delete-icon.png"
                                                                                         alt="Delete">
                                                                                </a>
                                                                            </li>
                                                                        <?php } ?>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        <?php } ?>
                                                    <?php } ?>
                                                </td>
                                                <?php } ?>
                                            </tr>
                                            <?php
                                        }
                                    } else {
                                        ?>
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
                    <?php if (!(ONLYDELIVERALL === 'Yes' || 'Yes' === $THEME_OBJ->isRideCXThemeActive() || 'Yes' === $THEME_OBJ->isRideDeliveryXThemeActive() || 'Yes' === $THEME_OBJ->isDeliveryXThemeActive() || 'Yes' === $THEME_OBJ->isDeliveryXv2ThemeActive() || 'Yes' === $THEME_OBJ->isRideCXv2ThemeActive())) { ?>
                        <li>
                            <?php echo 'hotels' === $admin ? 'Hotels' : 'Administrator'; ?> module will list
                            all <?php echo 'hotels' === $admin ? 'hotel' : 'admin'; ?> users on
                            this page.
                        </li>
                    <?php } ?>
                    <li>
                        Administrator can Activate , Deactivate , Delete any
                        other <?php echo 'hotels' === $admin ? 'hotel' : ''; ?> admin users.
                    </li>
                    <?php if ('hotels' !== $admin) { ?>
                        <li>Super Admin cannot be Activated , Deactivated or Deleted.</li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </div>
    <!--END PAGE CONTENT -->
</div>
<!--END MAIN WRAPPER -->
<form name="pageForm" id="pageForm" action="action/admin.php" method="post">
    <input type="hidden" name="page" id="page" value="<?php echo $page; ?>">
    <input type="hidden" name="tpages" id="tpages" value="<?php echo $tpages; ?>">
    <input type="hidden" name="iAdminId" id="iMainId01" value="">
    <input type="hidden" name="status" id="status01" value="">
    <input type="hidden" name="statusVal" id="statusVal" value="">
    <input type="hidden" name="option" value="<?php echo $option; ?>">
    <input type="hidden" name="keyword" value="<?php echo $keyword; ?>">
    <input type="hidden" name="sortby" id="sortby" value="<?php echo $sortby; ?>">
    <input type="hidden" name="order" id="order" value="<?php echo $order; ?>">
    <input type="hidden" name="admin" id="admin" value="<?php echo $admin; ?>">
    <input type="hidden" name="method" id="method" value="">
</form>
<?php
include_once 'footer.php';
?>
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
</script>
</body>
<!-- END BODY-->
</html>