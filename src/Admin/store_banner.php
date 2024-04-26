<?php
include_once '../common.php';
$permission_banner = 'banner-store';

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
// Update eStatus
$iUniqueId = $_GET['iUniqueId'] ?? '';
$status = $_GET['status'] ?? '';
// sort order
$flag = $_GET['flag'] ?? '';
$id = $_GET['id'] ?? '';
$select_cat = isset($_REQUEST['selectcategory']) ? stripslashes($_REQUEST['selectcategory']) : $_REQUEST['select_cat'];
$select_lang = isset($_REQUEST['selectlang']) ? stripslashes($_REQUEST['selectlang']) : '';

$catdata = serviceCategories;
$service_cat_data = json_decode($catdata, true);

$languages = $obj->MySQLSelect("SELECT * FROM language_master WHERE eStatus='Active' ORDER BY iDispOrder");

$tbl_name = 'banners';
$script = 'Store Banner';
$per_page = $DISPLAY_RECORD_NUMBER;

$whereserviceId = '';
$checkCubexThemOn = ('Yes' === $THEME_OBJ->isCubexThemeActive() || 'Yes' === $THEME_OBJ->isCubeXv2ThemeActive()) ? 'Yes' : 'No';
if ('YES' === strtoupper(ONLYDELIVERALL) || 'YES' === strtoupper($checkCubexThemOn)) {
    $whereserviceId = ' AND iServiceId IN (0,'.getCurrentActiveServiceCategoriesIds().')';
} else {
    $whereserviceId = ' AND iServiceId != 0';
}

if ('' !== $select_cat) {
    $whereserviceId = " AND iServiceId = '{$select_cat}'";
} else {
    if (count($service_cat_data) > 1) {
        $select_cat = $service_cat_data[0]['iServiceId'];
        $whereserviceId = " AND iServiceId = '{$select_cat}'";
    }
}

$vCodeLang = $_REQUEST['vCode'] ?? $default_lang;
$ParameterUrl = 'selectcategory='.$select_cat.'&selectlang='.$vCodeLang;
if ('' !== $select_lang) {
    $whereserviceId .= " AND vCode = '{$select_lang}'";
} else {
    $select_lang = $vCodeLang;
    $whereserviceId .= " AND vCode = '{$select_lang}'";
}

$eBuyAnyService = (isset($_REQUEST['eBuyAnyService']) && in_array($_REQUEST['eBuyAnyService'], ['Genie', 'Runner', 'Anywhere'], true)) ? 'eBuyAnyService='.$_REQUEST['eBuyAnyService'] : '';
$ssqlbuyanyservice = '';
if (isset($_REQUEST['eBuyAnyService']) && in_array($_REQUEST['eBuyAnyService'], ['Genie', 'Runner', 'Anywhere'], true) && $MODULES_OBJ->isEnableAnywhereDeliveryFeature()) {
    $ssqlbuyanyservice = " AND eBuyAnyService = '".$_REQUEST['eBuyAnyService']."' ";
    if ('Genie' === $_REQUEST['eBuyAnyService'] || 'Anywhere' === $_REQUEST['eBuyAnyService']) {
        $ssqlbuyanyservice = " AND eBuyAnyService = 'Genie' ";
    }
} else {
    $ssqlbuyanyservice = " AND eBuyAnyService = '' ";
}

// delete record
if ('' !== $hdn_del_id) {
    if (SITE_TYPE !== 'Demo') {
        $data_q = 'SELECT Max(iDisplayOrder) AS iDisplayOrder FROM `'.$tbl_name."` WHERE 1  AND {$whereserviceId} {$ssqlbuyanyservice}";
        $data_rec = $obj->MySQLSelect($data_q);

        $order = $data_rec[0]['iDisplayOrder'] ?? 0;

        $data_logo = $obj->MySQLSelect('SELECT iDisplayOrder FROM '.$tbl_name." WHERE iUniqueId = '".$hdn_del_id."' {$whereserviceId} {$ssqlbuyanyservice}");

        if (count($data_logo) > 0) {
            $iDisplayOrder = $data_logo[0]['iDisplayOrder'] ?? '';

            $obj->sql_query('DELETE FROM `'.$tbl_name."` WHERE iUniqueId = '".$hdn_del_id."' {$whereserviceId} {$ssqlbuyanyservice}");

            if ($iDisplayOrder < $order) {
                for ($i = $iDisplayOrder + 1; $i <= $order; ++$i) {
                    $obj->sql_query('UPDATE '.$tbl_name.' SET iDisplayOrder = '.($i - 1).' WHERE iDisplayOrder = '.$i.$whereserviceId.$ssqlbuyanyservice);
                }
            }
        }
        header('Location:store_banner.php?'.$ParameterUrl);

        exit;
    }
    $_SESSION['success'] = '2';
    header('Location:store_banner.php?'.$ParameterUrl);

    exit;
}

if (0 !== $id) {
    if (SITE_TYPE !== 'Demo') {
        if ('up' === $flag) {
            $sel_order = $obj->MySQLSelect('SELECT iDisplayOrder FROM '.$tbl_name." WHERE iUniqueId ='".$id."' {$whereserviceId} {$ssqlbuyanyservice}");
            $order_data = $sel_order[0]['iDisplayOrder'] ?? 0;
            $val = $order_data - 1;
            if ($val > 0) {
                $obj->MySQLSelect('UPDATE '.$tbl_name." SET iDisplayOrder='".$order_data."' WHERE iDisplayOrder='".$val."' {$whereserviceId} {$ssqlbuyanyservice}");
                $obj->MySQLSelect('UPDATE '.$tbl_name." SET iDisplayOrder='".$val."' WHERE iUniqueId = '".$id."' {$whereserviceId} {$ssqlbuyanyservice}");
            }
        } elseif ('down' === $flag) {
            $sel_order = $obj->MySQLSelect('SELECT iDisplayOrder FROM '.$tbl_name." WHERE iUniqueId ='".$id."' {$whereserviceId} {$ssqlbuyanyservice}");

            $order_data = $sel_order[0]['iDisplayOrder'] ?? 0;

            $val = $order_data + 1;
            $obj->MySQLSelect('UPDATE '.$tbl_name." SET iDisplayOrder='".$order_data."' WHERE iDisplayOrder='".$val."'  {$whereserviceId} {$ssqlbuyanyservice}");
            $obj->MySQLSelect('UPDATE '.$tbl_name." SET iDisplayOrder='".$val."' WHERE iUniqueId = '".$id."' {$whereserviceId} {$ssqlbuyanyservice}");
        }
        header('Location:store_banner.php?'.$ParameterUrl);

        exit;
    }
    $_SESSION['success'] = '2';
    header('Location:store_banner.php?'.$ParameterUrl);

    exit;
}

if ('' !== $iUniqueId && '' !== $status) {
    if (SITE_TYPE !== 'Demo') {
        $query = 'UPDATE `'.$tbl_name."` SET eStatus = '".$status."' WHERE iUniqueId = '".$iUniqueId."'{$whereserviceId}";
        $obj->sql_query($query);
        header('Location:store_banner.php?'.$ParameterUrl);

        exit;
    }
    $_SESSION['success'] = '2';
    header('Location:store_banner.php?'.$ParameterUrl);

    exit;
}
// $sql = "SELECT * FROM " . $tbl_name . " WHERE vCode = '" . $default_lang . "' AND iServiceId = 0 $ssql ORDER BY iDisplayOrder";
$db_data1 = $obj->MySQLSelect('SELECT * FROM '.$tbl_name." WHERE 1 {$whereserviceId} {$ssqlbuyanyservice} AND eFor = 'General' ORDER BY iDisplayOrder");
// $db_data2 = $obj->MySQLSelect("SELECT * FROM " . $tbl_name . " WHERE 1 $whereserviceId AND vCode != '" . $default_lang . "' $ssqlbuyanyservice AND eFor = 'General' ORDER BY vCode,iDisplayOrder");

$db_dataAll = $db_data1;

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
        <meta charset="UTF-8" />
        <title>Admin | Banners</title>
        <meta content="width=device-width, initial-scale=1.0" name="viewport" />
        <?php include_once 'global_files.php'; ?>
        <link href="../assets/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet" />
        <!-- <script type="text/javascript">
            function confirm_delete()
            {
                var confirm_ans = confirm("Are You sure You want to Delete Banner?");
                return confirm_ans;
                //document.getElementById(id).submit();
            }
        </script> -->
    </head>
    <!-- END  HEAD-->
    <!-- BEGIN BODY-->
    <body class="padTop53" >
        <!-- MAIN WRAPPER -->
        <div id="wrap">
            <?php include_once 'header.php'; ?>
            <?php include_once 'left_menu.php'; ?>
            <!--PAGE CONTENT -->
            <div id="content">
                <div class="inner">
                    <div class="row">
                        <div class="col-lg-12">
                            <h2>Banner</h2>
                            <?php if ($userObj->hasPermission($permission_banner_create)) { ?>
                            <a href="store_banner_action.php<?php echo ('' !== $eBuyAnyService) ? '?'.$eBuyAnyService : ''; ?>">
                            <input type="button" value="Add Banner" class="add-btn">
                            </a>
                            <?php } ?>
                        </div>
                    </div>
                    <hr />
                    <?php include 'valid_msg.php'; ?>
                    <form name="frmsearch" id="frmsearch" action="" >
                        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="admin-nir-table">
                            <tbody>
                                <tr>
                                    <td width="5%"><label for="textfield"><strong>Search:</strong></label></td>

                                    <?php if (count($service_cat_data) > 1) { ?>
                                    <td width="200px" class="estatus_options" id="ecategory_options" >
                                        <select name="selectcategory" id="selectcategory" class="form-control">
                                            <option value="" disabled>Select Category</option>
                                            <!-- <option value="0">General</option> -->
                                            <?php foreach ($service_cat_data as $servicedata) { ?>
                                            <option value="<?php echo $servicedata['iServiceId']; ?>" <?php
                                                if ($select_cat === $servicedata['iServiceId']) {
                                                    echo 'selected';
                                                }
                                                ?> > <?php echo $servicedata['vServiceName']; ?></option>
                                            <?php } ?>
                                        </select>
                                    </td>
                                    <?php } ?>

                                    <?php if (count($languages) > 1) { ?>
                                    <td width="200px" class="estatus_options">
                                        <select name="selectlang" id="selectlang" class="form-control">
                                            <option value="" disabled>Select Language</option>
                                            <?php foreach ($languages as $lang) { ?>
                                            <option value="<?php echo $lang['vCode']; ?>" <?php
                                                if ($select_lang === $lang['vCode']) {
                                                    echo 'selected';
                                                }
                                                ?> > <?php echo $lang['vTitle'].' ('.$lang['vCode'].')'; ?></option>
                                            <?php } ?>
                                        </select>
                                    </td>
                                    <?php } ?>
                                    <td>
                                      <input type="submit" value="Search" class="btnalt button11" id="Search" name="Search" title="Search" />
                                      <input type="button" value="Reset" class="btnalt button11" onClick="window.location.href='store_banner.php'"/>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                      </form>
                    <div class="table-list">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        Banner
                                    </div>
                                    <div class="panel-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                                                <thead>
                                                    <tr>
                                                        <th>Image</th>
                                                        <th>Title</th>
                                                        <?php if (count($service_cat_data) > 1) { ?>
                                                        <th class="text-center">Service Category</th>
                                                        <?php } ?>
                                                        <th>Language</th>
                                                        <th style="text-align:center;">Order</th>
                                                        <!-- <?php if ($userObj->hasPermission('update-status-banner-store')) { ?>
                                                        <th style="text-align:center;">Status</th>
                                                        <?php } ?>
                                                        <?php if ($userObj->hasPermission('edit-banner-store')) { ?>
                                                        <th style="text-align:center;">Edit</th>
                                                        <?php } ?>
                                                        <?php if ($userObj->hasPermission('delete-banner-store')) { ?>
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
                                                            <img src="<?php echo $tconfig['tsite_url'].'resizeImg.php?h=100&src='.$tconfig['tsite_upload_images'].$vImage; ?>" height="50" >
                                                            <?php
                                                            } else {
                                                                echo $vImage;
                                                            }
        ?>
                                                        </td>
                                                        <td><?php echo $vTitle; ?></td>
                                                        <?php if (count($service_cat_data) > 1) { ?>
                                                        <td  align="center">
                                                            <?php foreach ($service_cat_data as $servicedata) { ?>
                                                            <?php if ($servicedata['iServiceId'] === $db_data[$i]['iServiceId']) { ?><span><?php echo $servicedata['vServiceName'] ?? ''; ?></span><?php } ?>
                                                            <?php } ?>
                                                        </td>
                                                        <?php } ?>
                                                        <td><?php echo $vCode; ?></td>
                                                        <td width="10%" align="center">
                                                            <?php
        $db_dataCnt = $obj->MySQLSelect('SELECT * FROM '.$tbl_name." WHERE 1 {$whereserviceId} AND vCode = '".$vCode."' {$ssqlbuyanyservice}");
        $countData = count($db_dataCnt);
        if (1 === $countData) {
            echo '-';
        }
        if (1 !== $iDisplayOrder) { ?>
                                                            <a href="store_banner.php?id=<?php echo $iUniqueId; ?>&flag=up&vCode=<?php echo $vCode; ?>">
                                                            <button class="btn btn-warning">
                                                            <i class="icon-arrow-up"></i>
                                                            </button>
                                                            </a>
                                                            <?php }if ($iDisplayOrder !== $countData) { ?>
                                                            <a href="store_banner.php?id=<?php echo $iUniqueId; ?>&flag=down&vCode=<?php echo $vCode; ?>">
                                                            <button class="btn btn-warning">
                                                            <i class="icon-arrow-down"></i>
                                                            </button>
                                                            </a>
                                                            <?php } ?>
                                                        </td>
                                                        <!-- <?php if ($userObj->hasPermission('update-status-banner-store')) { ?>
                                                        <td width="10%" align="center">
                                                            <a href="store_banner.php?iUniqueId=<?php echo $iUniqueId; ?>&status=<?php echo ('Active' === $eStatus) ? 'Inactive' : 'Active'; ?><?php echo ('' !== $eBuyAnyService) ? '&'.$eBuyAnyService : ''; ?><?php echo $var_filter; ?>">
                                                                <button class="btn">
                                                                <i class="<?php echo ('Active' === $eStatus) ? 'icon-eye-open' : 'icon-eye-close'; ?>"></i> <?php echo $eStatus; ?>
                                                                </button>
                                                            </a>
                                                        </td>
                                                        <?php } ?>
                                                        <?php if ($userObj->hasPermission('edit-banner-store')) { ?>
                                                        <td width="10%" align="center">
                                                            <a href="store_banner_action.php?id=<?php echo $iUniqueId; ?>&vCode=<?php echo $vCode; ?><?php echo ('' !== $eBuyAnyService) ? '&'.$eBuyAnyService : ''; ?>">
                                                            <button class="btn btn-primary">
                                                            <i class="icon-pencil icon-white"></i> Edit
                                                            </button>
                                                            </a>
                                                        </td>
                                                        <?php } ?>
                                                        <?php if ($userObj->hasPermission('delete-banner-store')) { ?>
                                                        <td width="10%" align="center">
                                                            <form name="delete_form" id="delete_form" method="post" action="" onsubmit="return confirm_delete()" class="margin0">
                                                                <input type="hidden" name="hdn_del_id" id="hdn_del_id" value="<?php echo $iUniqueId; ?>">
                                                                <input type="hidden" name="vCode" id="vCode" value="<?php echo $vCode; ?>">
                                                                <button class="btn btn-danger">
                                                                <i class="icon-remove icon-white"></i> Delete
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
                                                                            <a href="store_banner_action.php?id=<?php echo $iUniqueId; ?>&vCode=<?php echo $vCode; ?><?php echo ('' !== $eBuyAnyService) ? '&'.$eBuyAnyService : ''; ?>" data-toggle="tooltip" title="Edit">
                                                                            <img src="img/edit-icon.png" alt="Edit">
                                                                            </a></li>
                                                                        <?php }  ?>
                                                                        <?php if ($userObj->hasPermission($permission_banner_update_status)) { ?>
                                                                            <li class="entypo-facebook" data-network="facebook">
                                                                                <a href="javascript:void(0);" onClick='window.location.href="store_banner.php?iUniqueId=<?php echo $iUniqueId; ?>&status=Active&selectcategory=<?php echo $select_cat; ?><?php echo ('' !== $eBuyAnyService) ? '&'.$eBuyAnyService : (('' !== $eForDelivery) ? '&'.$eForDelivery : ''); ?>&langSearch=<?php echo $vCode; ?>&vCode=<?php echo $vCode; ?><?php echo (in_array($eType, ['NearBy', 'Runner', 'Genie'], true)) ? '&eType='.$eType : ''; ?>"' data-toggle="tooltip" title="Activate">
                                                                                    <img src="img/active-icon.png" alt="<?php echo $eStatus; ?>">
                                                                                </a>
                                                                            </li>
                                                                            <li class="entypo-gplus" data-network="gplus">
                                                                                <a href="javascript:void(0);" onClick='window.location.href="store_banner.php?iUniqueId=<?php echo $iUniqueId; ?>&status=Inactive&selectcategory=<?php echo $select_cat; ?><?php echo ('' !== $eBuyAnyService) ? '&'.$eBuyAnyService : (('' !== $eForDelivery) ? '&'.$eForDelivery : ''); ?>&langSearch=<?php echo $vCode; ?>&vCode=<?php echo $vCode; ?><?php echo (in_array($eType, ['NearBy', 'Runner', 'Genie'], true)) ? '&eType='.$eType : ''; ?>"' data-toggle="tooltip" title="Deactivate">
                                                                                    <img src="img/inactive-icon.png" alt="<?php echo $eStatus; ?>">
                                                                                </a>

                                                                            </li>
                                                                        <?php } ?>
                                                                        <?php if ($userObj->hasPermission($permission_banner_delete)) {  ?>
                                                                            <li class="entypo-gplus" data-network="gplus"><a href="javascript:void(0);" onClick="confirm_delete('<?php echo $iUniqueId; ?>','<?php echo $vCode; ?>','<?php echo $select_cat; ?>','<?php echo $eBuyAnyService; ?>','<?php echo $eForDelivery; ?>','<?php echo $eType; ?>');" data-toggle="tooltip"  title="Delete">
                                                                                    <img src="img/delete-icon.png" alt="Delete">
                                                                                </a></li>
                                                                        <?php } ?>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <?php }
    } else {
        ?>
                                                    <tr class="gradeA">
                                                        <td colspan="10" style="text-align:center;"> No Records Found.</td>
                                                    </tr>
													<?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                        <?php include 'pagination_n.php'; ?>
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
        <form name="pageForm" id="pageForm" action="" method="post" >
            <input type="hidden" name="page" id="page" value="<?php echo $page; ?>">
            <input type="hidden" name="tpages" id="tpages" value="<?php echo $tpages; ?>">
            <input type="hidden" name="iFaqcategoryId" id="iFaqcategoryId" value="" >
            <input type="hidden" name="status" id="status01" value="" >
            <input type="hidden" name="statusVal" id="statusVal" value="" >
            <input type="hidden" name="option" value="<?php echo $option; ?>" >
            <input type="hidden" name="keyword" value="<?php echo $keyword; ?>" >
            <input type="hidden" name="sortby" id="sortby" value="<?php echo $sortby; ?>" >
            <input type="hidden" name="order" id="order" value="<?php echo $order; ?>" >
            <input type="hidden" name="method" id="method" value="" >
        </form>
        <?php include_once 'footer.php'; ?>
        <script src="../assets/plugins/dataTables/jquery.dataTables.js"></script>
        <script src="../assets/plugins/dataTables/dataTables.bootstrap.js"></script>
        <script>
            // $(document).ready(function () {
            //     $('#dataTables-example').dataTable({
            //         // null,
            //         "aoColumns": [
            //             {"bSortable": false},
            //             null,
            //             {"bSortable": false},
            //             {"bSortable": false},
            //             null,
            //             {"bSortable": false},
            //             {"bSortable": false},
            //         ]
            //     });
            // });

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


    function confirm_delete(iUniqueId,vCode,select_cat='',eBuyAnyService='',eForDelivery='',eType='') {

        var confirm_ans = confirm("Are You sure You want to Delete Banner?");

        if (confirm_ans == true) {
            window.location.href = 'store_banner.php?hdn_del_id='+iUniqueId+'&vCode='+vCode+'&select_cat='+select_cat+'&eBuyAnyService='+eBuyAnyService+'&eForDelivery='+eForDelivery+'&eType='+eType;
        }
    }
        </script>
    </body>
    <!-- END BODY-->
</html>
