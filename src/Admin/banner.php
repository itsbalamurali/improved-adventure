<?php
include_once '../common.php';
$script = 'Banners';
$eType = $_REQUEST['eType'] ?? '';
$ParameterUrl = $ssqlnearby = $ssqlbuyanyservice = '';
$permission_banner = 'app-home-screen-banner';

$eType = $_REQUEST['eType'] ?? '';

if (isset($_REQUEST['eType']) && in_array($_REQUEST['eType'], ['Genie', 'Runner', 'Anywhere'], true) && $MODULES_OBJ->isEnableAnywhereDeliveryFeature()) {
    $ssqlbuyanyservice = " AND eType = '".$_REQUEST['eType']."' ";
    if ('Genie' === $_REQUEST['eType'] || 'Anywhere' === $_REQUEST['eType']) {
        $ssqlbuyanyservice = " AND eType = 'Genie' ";
    }
    $script = $_REQUEST['eType'].'_banner';
    $ParameterUrl = '&eType='.$_REQUEST['eType'];
    $permission_banner = 'banner-genie-delivery';
    if ('Runner' === $_REQUEST['eType']) {
        $permission_banner = 'banner-runner-delivery';
    }
} elseif (isset($_REQUEST['eForDelivery']) && in_array($_REQUEST['eForDelivery'], ['MoreDelivery', 'ServiceProvider'], true)) {
    $eForBanner = $_REQUEST['eFor'] ?? '';
    $eTypeBanner = '';
    $iVehicleCategoryIdSql = '';
    if ('DeliveryCategory' === $eForBanner) {
        $eTypeBanner = 'Deliver';
        $permission_banner = 'banner-parcel-delivery';
    } elseif ('DeliverAllCategory' === $eForBanner) {
        $eTypeBanner = 'DeliverAll';
        $permission_banner = 'banner-store';
    } elseif ('UberX' === $eForBanner) {
        $eTypeBanner = 'UberX';
        $iVehicleCategoryId = $_REQUEST['iVehicleCategoryId'];
        $iVehicleCategoryIdSql = " AND iVehicleCategoryId = '{$iVehicleCategoryId}' ";
        $eForBanner .= '&iVehicleCategoryId='.$iVehicleCategoryId;
        $_REQUEST['eFor'] .= '&iVehicleCategoryId='.$iVehicleCategoryId;
        // $permission_banner = "banner-uberx";
    }
    $ssqlbuyanyservice = " AND eType = '{$eTypeBanner}' {$iVehicleCategoryIdSql} ";
    $script = 'MoreDelivery_banner';
    $ParameterUrl = '&eFor='.$eForBanner.'&eForDelivery='.$_REQUEST['eForDelivery'];
} elseif (isset($eType) && in_array($eType, ['NearBy'], true)) {
    $eType = 'NearBy';
    $ssqlbuyanyservice = " AND eType = '{$eType}' ";
    $script = 'NearBy_banner';
    $ParameterUrl = '&eFor=NearBy&eType=NearBy';
    $permission_banner = 'banners-nearby';
} else {
    $ssqlbuyanyservice = " AND eType = 'General'";
}
$permission_banner_view = 'view-'.$permission_banner;
$permission_banner_create = 'create-'.$permission_banner;
$permission_banner_edit = 'edit-'.$permission_banner;
$permission_banner_delete = 'delete-'.$permission_banner;
$permission_banner_update_status = 'update-status-'.$permission_banner;
if (!$userObj->hasPermission($permission_banner_view)) {
    $userObj->redirect();
}
$default_lang = $LANG_OBJ->FetchSystemDefaultLang();
// Delete
$hdn_del_id = $_REQUEST['hdn_del_id'] ?? '';
$vCodeDlt = $_REQUEST['vCode'] ?? '';
// Update eStatus
$iUniqueId = $_GET['iUniqueId'] ?? '';
$status = $_GET['status'] ?? '';
// sort order
$flag = $_GET['flag'] ?? '';
$id = $_GET['id'] ?? '';
$pro = $_GET['pro'] ?? '';
$tbl_name = 'banners';
$per_page = $DISPLAY_RECORD_NUMBER;
$langSearch = $default_lang;
if ('' !== $vCodeDlt) {
    $langSearch = $vCodeDlt;
}
if (!empty($_REQUEST['langSearch'])) {
    $langSearch = $_REQUEST['langSearch'];
}
$langsql = " AND vCode = '".$langSearch."'";
$whereserviceId = ' AND iServiceId = 0';
$eBuyAnyService = (isset($_REQUEST['eType']) && in_array($_REQUEST['eType'], [
    'Genie',
    'Runner',
    'Anywhere',
], true)) ? 'eType='.$_REQUEST['eType'] : '';
$eForDelivery = (isset($_REQUEST['eForDelivery']) && in_array($_REQUEST['eForDelivery'], ['MoreDelivery', 'ServiceProvider'], true)) ? 'eForDelivery='.$_REQUEST['eForDelivery'].'&eFor='.$_REQUEST['eFor'] : '';
$vCodeLang = $vCodeDlt;
if ('' === $vCodeDlt) {
    $vCodeLang = $_REQUEST['vCode'] ?? $default_lang;
}

// delete record
if ('' !== $hdn_del_id) {
    if (SITE_TYPE !== 'Demo') {
        $data_q = 'SELECT Max(iDisplayOrder) AS iDisplayOrder FROM `'.$tbl_name."` WHERE 1=1 AND vCode = '".$vCodeLang."' {$whereserviceId} {$ssqlbuyanyservice}";
        $data_rec = $obj->MySQLSelect($data_q);
        $order = $data_rec[0]['iDisplayOrder'] ?? 0;
        $data_logo = $obj->MySQLSelect('SELECT iDisplayOrder FROM '.$tbl_name." WHERE iUniqueId = '".$hdn_del_id."' AND vCode = '".$vCodeLang."'{$whereserviceId} {$ssqlbuyanyservice}");
        if (count($data_logo) > 0) {
            $iDisplayOrder = $data_logo[0]['iDisplayOrder'] ?? '';
            $obj->sql_query('DELETE FROM `'.$tbl_name."` WHERE iUniqueId = '".$hdn_del_id."' AND vCode = '".$vCodeLang."'{$whereserviceId} {$ssqlbuyanyservice}");
            if ($iDisplayOrder < $order) {
                for ($i = $iDisplayOrder + 1; $i <= $order; ++$i) {
                    $obj->sql_query('UPDATE '.$tbl_name.' SET iDisplayOrder = '.($i - 1).' WHERE iDisplayOrder = '.$i." AND vCode = '".$vCodeLang."'".$whereserviceId.$ssqlbuyanyservice);
                }
            }
        }
        $_SESSION['success'] = '1';
        $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_DELETE_MSG'];
        header('Location:banner.php?langSearch='.$vCodeLang.$ParameterUrl);

        exit;
    }
    $_SESSION['success'] = '2';
    header('Location:banner.php?langSearch='.$vCodeLang.$ParameterUrl);

    exit;
}
if (0 !== $id) {
    if (SITE_TYPE !== 'Demo') {
        $updateSql = '';
        if ('NearBy' === $eType) {
            $updateSql = " AND eType IN('NearBy')";
        } elseif ('Genie' === $eType) {
            $updateSql = " AND eType IN('Genie')";
        } elseif ('Runner' === $eType) {
            $updateSql = " AND eType IN('Runner')";
        }
        if ('up' === $flag) {
            $sel_order = $obj->MySQLSelect('SELECT iDisplayOrder FROM '.$tbl_name." WHERE iUniqueId ='".$id."' AND vCode = '".$vCodeLang."'{$whereserviceId} {$ssqlbuyanyservice} AND eFor = 'General'");
            $order_data = $sel_order[0]['iDisplayOrder'] ?? 0;
            $val = $order_data - 1;
            if ($val > 0) {
                $obj->MySQLSelect('UPDATE '.$tbl_name." SET iDisplayOrder='".$order_data."' WHERE eType NOT IN('RentItem','RentEstate','RentCars') ".$updateSql." AND  iDisplayOrder='".$val."' AND vCode = '".$vCodeLang."' {$whereserviceId} {$ssqlbuyanyservice}");
                $obj->MySQLSelect('UPDATE '.$tbl_name." SET iDisplayOrder='".$val."' WHERE eType NOT IN('RentItem','RentEstate','RentCars') ".$updateSql." AND iUniqueId = '".$id."' AND vCode = '".$vCodeLang."'{$whereserviceId} {$ssqlbuyanyservice}");
            }
        } elseif ('down' === $flag) {
            $sel_order = $obj->MySQLSelect('SELECT iDisplayOrder FROM '.$tbl_name." WHERE iUniqueId ='".$id."' AND vCode = '".$vCodeLang."'{$whereserviceId} {$ssqlbuyanyservice} AND eFor = 'General'");
            $order_data = $sel_order[0]['iDisplayOrder'] ?? 0;
            $val = $order_data + 1;
            $obj->MySQLSelect('UPDATE '.$tbl_name." SET iDisplayOrder='".$order_data."' WHERE eType NOT IN('RentItem','RentEstate','RentCars') ".$updateSql." AND iDisplayOrder='".$val."' AND vCode = '".$vCodeLang."' {$whereserviceId} {$ssqlbuyanyservice}");
            $obj->MySQLSelect('UPDATE '.$tbl_name." SET iDisplayOrder='".$val."' WHERE eType NOT IN('RentItem','RentEstate','RentCars') ".$updateSql." AND iUniqueId = '".$id."' AND vCode = '".$vCodeLang."'{$whereserviceId} {$ssqlbuyanyservice}");
        }
        header('Location:banner.php?langSearch='.$vCodeLang.$ParameterUrl);

        exit;
    }
    $_SESSION['success'] = '2';
    header('Location:banner.php?langSearch='.$vCodeLang.$ParameterUrl);

    exit;
}
if ('' !== $iUniqueId && '' !== $status) {
    if (SITE_TYPE !== 'Demo') {
        $query = 'UPDATE `'.$tbl_name."` SET eStatus = '".$status."' WHERE iUniqueId = '".$iUniqueId."' AND vCode = '".$vCodeLang."'{$whereserviceId}";
        $obj->sql_query($query);
        header('Location:banner.php?langSearch='.$vCodeLang.$ParameterUrl);

        exit;
    }
    $_SESSION['success'] = '2';
    // header("Location:banner.php");
    header('Location:banner.php?langSearch='.$vCodeLang.$ParameterUrl);

    exit;
}
$db_dataAll = $obj->MySQLSelect('SELECT * FROM '.$tbl_name." WHERE 1 {$whereserviceId} {$langsql} {$ssqlbuyanyservice} AND eFor = 'General' ORDER BY iDisplayOrder");
$total_results = count($db_dataAll);
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
$db_data = array_slice($db_dataAll, $start, $per_page);
$endRecord = count($db_data);
$var_filter = '';
foreach ($_REQUEST as $key => $val) {
    if ('tpages' !== $key && 'page' !== $key) {
        $var_filter .= "&{$key}=".stripslashes($val);
    }
}
$reload = $_SERVER['PHP_SELF'].'?tpages='.$tpages.$var_filter;
$db_langdata = $obj->MySQLSelect("SELECT vCode,vTitle FROM language_master WHERE eStatus = 'Active' ORDER BY iDispOrder");
?>
<!DOCTYPE html>
<!--[if IE 8]>
<html lang="en" class="ie8">
<![endif]-->
<!--[if IE 9]>
<html lang="en" class="ie9">
<![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD-->
<head>
    <meta charset="UTF-8"/>
    <title>Admin | Home Page Banners</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <?php include_once 'global_files.php'; ?>
    <link href="../assets/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet"/>
</head>
<!-- END  HEAD-->
<!-- BEGIN BODY-->
<body class="padTop53">
<!-- MAIN WRAPPER -->
<div id="wrap">
    <?php include_once 'header.php'; ?>

    <?php include_once 'left_menu.php'; ?>
    <!--PAGE CONTENT -->
    <div id="content">
        <div class="inner">
            <div class="row">
                <div class="col-lg-12">
                    <h2>Home Page Banners</h2>
                    <?php if ($userObj->hasPermission($permission_banner_create)) {
                        if ('' !== $langSearch) {
                            if ('' !== $eBuyAnyService) {
                                $add_banner = '?vCode='.$langSearch;
                            } else {
                                $add_banner = '?vCode='.$langSearch;
                            }
                        }
                        ?>
                        <a href="banner_action.php<?php echo $add_banner; ?><?php echo ('' !== $eBuyAnyService) ? '&'.$eBuyAnyService : (('' !== $eForDelivery) ? '&'.$eForDelivery : ''); ?><?php echo (in_array($eType, [
                            'NearBy',
                            'Runner',
                            'Genie',
                        ], true)) ? '&eType='.$eType : ''; ?>">
                            <input type="button" value="Add Banner" class="add-btn">
                        </a>
                    <?php } ?>
                </div>
            </div>
            <hr/>
            <?php include 'valid_msg.php'; ?>
            <div class="form-group">
                <div class="row">
                    <form action="" method="POST" name="frm_searchlang">
                        <!--<div class="col-lg-1">

                            <span>Language:</span>

                            </div>-->
                        <div class="col-lg-2">
                            <select name="langSearch" class="form-control">
                                <?php foreach ($db_langdata as $key => $value) { ?>
                                    <option value="<?php echo $value['vCode']; ?>" <?php if ($value['vCode'] === $langSearch) {
                                        echo 'selected';
                                    } ?>><?php echo $value['vTitle']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="col-lg-1">
                            <input type="submit" name="btn_search" id="btn_search" value="Search"
                                   class="btnalt button11">
                        </div>
                    </form>
                </div>
            </div>
            <div class="table-list">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                Banner
                            </div>
                            <div class="panel-body">
                                <div>
                                    <table class="table responsive table-striped table-bordered table-hover"
                                           id="dataTables-example">
                                        <thead>
                                        <tr>
                                            <th width="10%" style="text-align:center;">Image</th>
                                            <th width="15%">Title</th>
                                            <th width="8%" style="text-align:center;">Language</th>
                                            <th  width="8%" style="text-align:center;">Display Order</th>
                                            <!-- <?php if ($userObj->hasPermission($permission_banner_update_status)) { ?>
                                                <th style="text-align:center;">Status</th>
                                            <?php } ?>

                                            <?php if ($userObj->hasPermission($permission_banner_edit)) { ?>
                                                <th style="text-align:center;">Edit</th>
                                            <?php } ?>

                                            <?php if ($userObj->hasPermission($permission_banner_delete)) { ?>
                                                <th style="text-align:center;">Delete</th>
                                            <?php } ?> -->
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $count_all = count($db_data);
if ($count_all > 0) {
    for ($i = 0; $i < $count_all; ++$i) {
        $vTitle = $db_data[$i]['vTitle'];
        $vImage = $db_data[$i]['vImage'];
        $vCode = $db_data[$i]['vCode'];
        $iDisplayOrder = $db_data[$i]['iDisplayOrder'];
        $eStatus = $db_data[$i]['eStatus'];
        $iUniqueId = $db_data[$i]['iUniqueId'];
        $checked = ('Active' === $eStatus) ? 'checked' : '';
        ?>
                                                <tr class="gradeA">
                                                    <td width="10%" align="center">
                                                        <?php if ('' !== $vImage && file_exists($tconfig['tsite_upload_images_panel'].'/'.$vImage)) { ?>
                                                            <img src="<?php echo $tconfig['tsite_url'].'resizeImg.php?h=100&src='.$tconfig['tsite_upload_images'].$vImage; ?>"
                                                                 height="50">
                                                            <?php
                                                        } else {
                                                            echo $vImage;
                                                        }
        ?>
                                                    </td>
                                                    <td><?php echo $vTitle; ?></td>
                                                    <td align="center" width="15%"><?php echo $vCode; ?></td>
                                                    <td align="center" width="15%" >
                                                        <?php
        $db_dataCnt = $obj->MySQLSelect('SELECT * FROM '.$tbl_name." WHERE 1 {$whereserviceId} AND vCode = '".$vCode."' {$ssqlbuyanyservice}");
        $countData = count($db_dataCnt);

        if (1 === $countData) {
            echo '-';
        } else {
            if (1 !== $iDisplayOrder) { ?>
                                                                <a href="banner.php?id=<?php echo $iUniqueId; ?>&flag=up&vCode=<?php echo $vCode; ?>&langSearch=<?php echo $vCode; ?><?php echo (in_array($eType, [
                    'NearBy',
                    'Runner',
                    'Genie',
                ], true)) ? '&eType='.$eType : ''; ?><?php echo (in_array($_REQUEST['eForDelivery'], [
                    'MoreDelivery',
                ], true)) ? '&eForDelivery='.$_REQUEST['eForDelivery'] : ''; ?><?php echo (in_array($_REQUEST['eFor'], [
                    'DeliveryCategory',
                ], true)) ? '&eFor='.$_REQUEST['eFor'] : ''; ?>">
                                                                    <button class="btn btn-warning">
                                                                        <i class="icon-arrow-up"></i>
                                                                    </button>
                                                                </a>
                                                            <?php }
            if ($iDisplayOrder !== $countData) { ?>
                                                                <a href="banner.php?id=<?php echo $iUniqueId; ?>&flag=down&vCode=<?php echo $vCode; ?>&langSearch=<?php echo $vCode; ?><?php echo (in_array($eType, [
                    'NearBy',
                    'Runner',
                    'Genie',
                ], true)) ? '&eType='.$eType : ''; ?><?php echo (in_array($_REQUEST['eForDelivery'], [
                    'MoreDelivery',
                ], true)) ? '&eForDelivery='.$_REQUEST['eForDelivery'] : ''; ?><?php echo (in_array($_REQUEST['eFor'], [
                    'DeliveryCategory',
                ], true)) ? '&eFor='.$_REQUEST['eFor'] : ''; ?>">
                                                                    <button class="btn btn-warning">
                                                                        <i class="icon-arrow-down"></i>
                                                                    </button>
                                                                </a>
                                                            <?php }
            } ?>
                                                    </td>
                                                   <!--  <?php if ($userObj->hasPermission($permission_banner_update_status)) { ?>
                                                        <td width="10%" align="center">
                                                            <a href="banner.php?iUniqueId=<?php echo $iUniqueId; ?>&status=<?php echo ('Active' === $eStatus) ? 'Inactive' : 'Active'; ?><?php echo ('' !== $eBuyAnyService) ? '&'.$eBuyAnyService : (('' !== $eForDelivery) ? '&'.$eForDelivery : ''); ?>&langSearch=<?php echo $vCode; ?>&vCode=<?php echo $vCode; ?><?php echo (in_array($eType, [
                    'NearBy',
                    'Runner',
                    'Genie',
                ], true)) ? '&eType='.$eType : ''; ?>">
                                                                <button class="btn">
                                                                    <i class="<?php echo ('Active' === $eStatus) ? 'icon-eye-open' : 'icon-eye-close'; ?>"></i> <?php echo $eStatus; ?>
                                                                </button>
                                                            </a>
                                                        </td>
                                                    <?php } ?>

                                                    <?php if ($userObj->hasPermission($permission_banner_edit)) { ?>
                                                        <td width="10%" align="center">
                                                            <a href="banner_action.php?id=<?php echo $iUniqueId; ?>&vCode=<?php echo $vCode; ?>&langSearch=<?php echo $vCode; ?><?php echo ('' !== $eBuyAnyService) ? '&'.$eBuyAnyService : (('' !== $eForDelivery) ? '&'.$eForDelivery : ''); ?>">
                                                                <button class="btn btn-primary">
                                                                    <i class="icon-pencil icon-white"></i>
                                                                    Edit
                                                                </button>
                                                            </a>
                                                        </td>
                                                    <?php } ?>

                                                    <?php if ($userObj->hasPermission($permission_banner_delete)) { ?>
                                                        <td width="10%" align="center">
                                                            <form name="delete_form" id="delete_form" method="post"
                                                                  action="" onsubmit="return confirm_delete()"
                                                                  class="margin0">
                                                                <input type="hidden" name="hdn_del_id" id="hdn_del_id"
                                                                       value="<?php echo $iUniqueId; ?>">
                                                                <input type="hidden" name="vCode" id="vCode"
                                                                       value="<?php echo $vCode; ?>">
                                                                <button class="btn btn-danger">
                                                                    <i class="icon-remove icon-white"></i>
                                                                    Delete
                                                                </button>
                                                            </form>
                                                        </td>
                                                    <?php } ?> -->
                                                <td width="10%"  align="center">
                                                    <?php
                                                    if ('Active' === $eStatus) {
                                                        $dis_img = 'img/active-icon.png';
                                                    } elseif ('Inactive' === $eStatus) {
                                                        $dis_img = 'img/inactive-icon.png';
                                                    } elseif ('Deleted' === $eStatus) {
                                                        $dis_img = 'img/delete-icon.png';
                                                    }
        ?>
                                                    <img src="<?php echo $dis_img; ?>" alt="<?php echo $eStatus; ?>" data-toggle="tooltip" title="<?php echo $eStatus; ?>">
                                                </td>

                                                <td width="10%" align="center" style="text-align:center;" class="action-btn001">
                                                    <div class="share-button openHoverAction-class" style="display: block;">
                                                        <label class="entypo-export"><span><img src="images/settings-icon.png"  alt=""></span></label>
                                                        <div class="social show-moreOptions openPops_<?php echo $iUniqueId; ?>">
                                                            <ul>
                                                                <?php if ($userObj->hasPermission($permission_banner_edit)) { ?>
                                                                <li class="entypo-twitter" data-network="twitter">
                                                                    <a href="banner_action.php?id=<?php echo $iUniqueId; ?>&vCode=<?php echo $vCode; ?>&langSearch=<?php echo $vCode; ?><?php echo ('' !== $eBuyAnyService) ? '&'.$eBuyAnyService : (('' !== $eForDelivery) ? '&'.$eForDelivery : ''); ?>" data-toggle="tooltip" title="Edit">
                                                                    <img src="img/edit-icon.png" alt="Edit">
                                                                    </a></li>
                                                                <?php }  ?>
                                                                <?php if ($userObj->hasPermission($permission_banner_update_status)) { ?>
                                                                    <li class="entypo-facebook" data-network="facebook">
                                                                        <a href="javascript:void(0);" onClick='window.location.href="banner.php?iUniqueId=<?php echo $iUniqueId; ?>&status=Active<?php echo ('' !== $eBuyAnyService) ? '&'.$eBuyAnyService : (('' !== $eForDelivery) ? '&'.$eForDelivery : ''); ?>&langSearch=<?php echo $vCode; ?>&vCode=<?php echo $vCode; ?><?php echo (in_array($eType, ['NearBy', 'Runner', 'Genie'], true)) ? '&eType='.$eType : ''; ?>"' data-toggle="tooltip" title="Activate">
                                                                            <img src="img/active-icon.png" alt="<?php echo $eStatus; ?>">
                                                                        </a>
                                                                    </li>
                                                                    <li class="entypo-gplus" data-network="gplus">

                                                                        <a href="javascript:void(0);" onClick='window.location.href="banner.php?iUniqueId=<?php echo $iUniqueId; ?>&status=Inactive<?php echo ('' !== $eBuyAnyService) ? '&'.$eBuyAnyService : (('' !== $eForDelivery) ? '&'.$eForDelivery : ''); ?>&langSearch=<?php echo $vCode; ?>&vCode=<?php echo $vCode; ?><?php echo (in_array($eType, ['NearBy', 'Runner', 'Genie'], true)) ? '&eType='.$eType : ''; ?>"' data-toggle="tooltip" title="Deactivate">
                                                                            <img src="img/inactive-icon.png" alt="<?php echo $eStatus; ?>">
                                                                        </a>

                                                                    </li>
                                                                <?php } ?>
                                                                <?php if ($userObj->hasPermission($permission_banner_delete)) {  ?>
                                                                    <li class="entypo-gplus" data-network="gplus"><a href="javascript:void(0);" onClick="confirm_delete('<?php echo $iUniqueId; ?>','<?php echo $vCode; ?>','<?php echo ('' !== $eBuyAnyService) ? '&' : $eBuyAnyService; ?>','<?php echo ('' !== $eForDelivery) ? '&'.$eForDelivery : ''; ?>','<?php echo $eType; ?>');" data-toggle="tooltip"  title="Delete">
                                                                            <img src="img/delete-icon.png"   alt="Delete">
                                                                        </a></li>
                                                                <?php } ?>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </td>
                                                </tr>
                                            <?php }
    }
?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php // include('pagination_n.php');?>
                            </div>
                        </div>
                    </div>
                    <!--TABLE-END-->
                </div>
            </div>
        </div>
    </div>
    <!--END PAGE CONTENT -->
</div>
<!--END MAIN WRAPPER -->
<form name="pageForm" id="pageForm" action="" method="post">
    <input type="hidden" name="page" id="page" value="<?php echo $page; ?>">
    <input type="hidden" name="tpages" id="tpages" value="<?php echo $tpages; ?>">
    <input type="hidden" name="iFaqcategoryId" id="iFaqcategoryId" value="">
    <input type="hidden" name="status" id="status01" value="">
    <input type="hidden" name="statusVal" id="statusVal" value="">
    <input type="hidden" name="option" value="<?php echo $option; ?>">
    <input type="hidden" name="keyword" value="<?php echo $keyword; ?>">
    <input type="hidden" name="sortby" id="sortby" value="<?php echo $sortby; ?>">
    <input type="hidden" name="order" id="order" value="<?php echo $order; ?>">
    <input type="hidden" name="method" id="method" value="">
</form>
<?php include_once 'footer.php'; ?>
<script src="../assets/plugins/dataTables/jquery.dataTables.js"></script>
<script src="../assets/plugins/dataTables/dataTables.bootstrap.js"></script>
<script>
    $(document).ready(function () {

        $('#dataTables-example').dataTable({

            //null,

            "aoColumns": [

                {"bSortable": false},

                null,

                {"bSortable": false},

                {"bSortable": false},

                null,

                {"bSortable": false},
            ],
            columnDefs: [{
                "defaultContent": "-",
                "targets": "_all"
            }]

        });

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


    function confirm_delete(iUniqueId,vCode,eBuyAnyService,eForDelivery,eType) {

        var confirm_ans = confirm("Are You sure You want to Delete Banner?");

        if (confirm_ans == true) {
            window.location.href = 'banner.php?hdn_del_id='+iUniqueId+'&vCode='+vCode+eBuyAnyService+eForDelivery+'&eType='+eType;
        }
    }


</script>
</body>
<!-- END BODY-->
</html>