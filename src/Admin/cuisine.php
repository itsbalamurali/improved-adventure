<?php
include_once '../common.php';
if (!$userObj->hasPermission('view-item-type')) {
    $userObj->redirect();
}
$script = 'Cuisine';
// get cusine
$hdn_del_id = $_POST['hdn_del_id'] ?? '';
$cuisineId = $_REQUEST['cuisineId'] ?? '';
$status = $_REQUEST['status'] ?? '';
$success = $_REQUEST['success'] ?? 0;
$tbl_name = 'cuisine';
if ('' !== $hdn_del_id) {
    if (SITE_TYPE !== 'Demo') {
        $query = 'DELETE FROM `'.$tbl_name."` WHERE cuisineId = '".$hdn_del_id."'"; // die;
        $obj->sql_query($query);
    } else {
        header('Location:cuisine.php?success=2');

        exit;
    }
}
if ('' !== $cuisineId && '' !== $status) {
    if (SITE_TYPE !== 'Demo') {
        $query = 'UPDATE `'.$tbl_name."` SET eStatus = '".$status."' WHERE cuisineId = '".$cuisineId."'";
        $obj->sql_query($query);
    } else {
        header('Location:cuisine.php?success=2');

        exit;
    }
}
// get cusine
// Start Sorting
$sortby = $_REQUEST['sortby'] ?? 0;
$order = $_REQUEST['order'] ?? '';
$ord = ' ORDER BY c.cuisineName_'.$default_lang.' ASC';
if (1 === $sortby) {
    if (0 === $order) {
        $ord = ' ORDER BY c.cuisineName_'.$default_lang.' ASC';
    } else {
        $ord = ' ORDER BY c.cuisineName_'.$default_lang.' DESC';
    }
}
if (4 === $sortby) {
    if (0 === $order) {
        $ord = ' ORDER BY c.eStatus ASC';
    } else {
        $ord = ' ORDER BY c.eStatus DESC';
    }
}
if (5 === $sortby) {
    if (0 === $order) {
        $ord = ' ORDER BY c.iDisplayOrder ASC';
    } else {
        $ord = ' ORDER BY c.iDisplayOrder DESC';
    }
}
// End Sorting
// Start Search Parameters
$option = isset($_REQUEST['option']) ? stripslashes($_REQUEST['option']) : '';
$keyword = isset($_REQUEST['keyword']) ? stripslashes($_REQUEST['keyword']) : '';
$searchDate = $_REQUEST['searchDate'] ?? '';
$eStatus = $_REQUEST['eStatus'] ?? '';
$select_cat = isset($_REQUEST['selectcategory']) ? stripslashes($_REQUEST['selectcategory']) : '';
$ssql = '';
if ('' !== $keyword) {
    if ('' !== $option) {
        if ('' !== $eStatus && '' !== $select_cat) {
            $ssql .= ' AND '.stripslashes($option)." LIKE '%".clean($keyword)."%' AND c.eStatus = '".clean($eStatus)."' AND c.iServiceId = '{$select_cat}'";
        } elseif ('' !== $eStatus && $select_cat = '') {
            $ssql .= ' AND '.stripslashes($option)." LIKE '%".clean($keyword)."%' AND c.eStatus = '".clean($eStatus)."'";
        } else {
            $ssql .= ' AND '.stripslashes($option)." LIKE '%".stripslashes($keyword)."%'";
        }
    } else {
        if ('' !== $eStatus && '' !== $select_cat) {
            $ssql .= ' AND (c.cuisineName_'.$default_lang." LIKE '%".$keyword."%' OR sc.vServiceName_".$default_lang." LIKE '%".$keyword."%') AND c.eStatus = '".clean($eStatus)."' AND c.iServiceId = '{$select_cat}'";
        } elseif ('' !== $eStatus) {
            $ssql .= ' AND (c.cuisineName_'.$default_lang." LIKE '%".$keyword."%' OR sc.vServiceName_".$default_lang." LIKE '%".$keyword."%') AND c.eStatus = '".clean($eStatus)."'";
        } else {
            $ssql .= ' AND (c.cuisineName_'.$default_lang." LIKE '%".$keyword."%' OR sc.vServiceName_".$default_lang." LIKE '%".$keyword."%') ";
        }
    }
} elseif ('' !== $eStatus && '' !== $select_cat && '' === $keyword) {
    $ssql .= " AND c.eStatus = '".clean($eStatus)."' AND c.iServiceId = '{$select_cat}' ";
} elseif ('' !== $eStatus && '' === $keyword && '' === $select_cat) {
    $ssql .= " AND c.eStatus = '".clean($eStatus)."'";
} elseif ('' === $eStatus && '' === $keyword && '' !== $select_cat) {
    $ssql .= " AND c.iServiceId = '{$select_cat}'";
}
if ('' !== $eStatus) {
    $eStatussql = '';
} else {
    $eStatussql = " AND c.eStatus != 'Deleted'";
}
// End Search Parameters
$ssql .= ' AND c.iServiceId IN('.$enablesevicescategory.')';
// Pagination Start
$per_page = $DISPLAY_RECORD_NUMBER; // number of results to show per page
$sql = "SELECT COUNT(c.cuisineId) AS Total FROM cuisine as c LEFT JOIN service_categories as sc on sc.iServiceId=c.iServiceId WHERE 1=1 {$eStatussql} {$ssql}";
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
$sql = 'SELECT c.*,sc.vServiceName_'.$default_lang." as serviceName FROM cuisine as c LEFT JOIN service_categories as sc on sc.iServiceId=c.iServiceId WHERE 1=1 {$eStatussql}  {$ssql} {$ord} LIMIT {$start}, {$per_page} ";
$data_drv = $obj->MySQLSelect($sql);
$endRecord = count($data_drv);
$var_filter = '';
foreach ($_REQUEST as $key => $val) {
    if ('tpages' !== $key && 'page' !== $key) {
        $var_filter .= "&{$key}=".stripslashes($val);
    }
}
$reload = $_SERVER['PHP_SELF'].'?tpages='.$tpages.$var_filter;
$catdata = serviceCategories;
$service_cat_data = json_decode($catdata, true);
?>
<!DOCTYPE html>
<html lang="en">
<!-- BEGIN HEAD-->
<head>
    <meta charset="UTF-8"/>
    <title><?php echo $SITE_NAME; ?> | Item Type</title>
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
                        <h2>Item Type</h2>
                    </div>
                </div>
                <hr/>
            </div>
            <?php include 'valid_msg.php'; ?>
            <form name="frmsearch" id="frmsearch" action="javascript:void(0);" method="post">
                <table width="100%" border="0" cellpadding="0" cellspacing="0" class="admin-nir-table">
                    <tbody>
                    <tr>
                        <td width="5%">
                            <label for="textfield">
                                <strong>Search:</strong>
                            </label>
                        </td>
                        <td width="10%" class=" padding-right10">
                            <select name="option" id="option" class="form-control">
                                <option value="">All</option>
                                <option value="cuisineName_<?php echo $default_lang; ?>" <?php if ($option === 'cuisineName_'.$default_lang) {
                                    echo 'selected';
                                } ?> >Item Type
                                </option>
                            </select>
                        </td>
                        <td width="15%">
                            <input type="Text" id="keyword" name="keyword" value="<?php echo $keyword; ?>"
                                   class="form-control"/>
                        </td>
                        <td width="12%" class="estatus_options" id="eStatus_options">
                            <select name="eStatus" id="estatus_value" class="form-control">
                                <option value="">Select Status</option>
                                <option value='Active' <?php if ('Active' === $eStatus) {
                                    echo 'selected';
                                } ?> >Active
                                </option>
                                <option value="Inactive" <?php if ('Inactive' === $eStatus) {
                                    echo 'selected';
                                } ?> >Inactive
                                </option>
                                <option value="Deleted" <?php if ('Deleted' === $eStatus) {
                                    echo 'selected';
                                } ?> >Delete
                                </option>
                            </select>
                        </td>
                        <?php if (count($service_cat_data) > 1) { ?>
                            <td width="200px" class="estatus_options" id="ecategory_options">
                                <select name="selectcategory" id="selectcategory" class="form-control">
                                    <option value="">Select Category</option>
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
                        <td>
                            <input type="submit" value="Search" class="btnalt button11" id="Search" name="Search"
                                   title="Search"/>
                            <input type="button" value="Reset" class="btnalt button11"
                                   onClick="window.location.href='cuisine.php'"/>
                        </td>
                        <?php if ($userObj->hasPermission('create-item-type')) { ?>
                            <td width="30%">
                                <a class="add-btn" href="cuisine_action.php" style="text-align: center;">Add Item Type
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
                            <div class="changeStatus col-lg-12 option-box-left">
                                    <span class="col-lg-2 new-select001">
                                        <?php if ($userObj->hasPermission(['update-status-item-type', 'delete-item-type'])) { ?>
                                            <select name="changeStatus" id="changeStatus" class="form-control"
                                                    onchange="ChangeStatusAll(this.value);">
                                            <option value="">Select Action</option>
                                            <?php if ($userObj->hasPermission('update-status-item-type')) { ?>
                                                <option value='Active' <?php if ('Active' === $option) {
                                                    echo 'selected';
                                                } ?> >Activate</option>
                                                <option value="Inactive" <?php if ('Inactive' === $option) {
                                                    echo 'selected';
                                                } ?> >Deactivate</option>
                                            <?php } ?>
                                                <?php if ('Deleted' !== $eStatus && $userObj->hasPermission('delete-item-type')) { ?>
                                                    <option value="Deleted" <?php if ('Delete' === $option) {
                                                        echo 'selected';
                                                    } ?> >Delete</option>
                                                <?php } ?>
                                        </select>
                                        <?php } ?>
                                    </span>
                                    </div>
                                    <?php if (!empty($data_drv)) {?>
<!--                                    <div class="panel-heading">
                                        <form name="_export_form" id="_export_form" method="post" >
                                            <button type="button" onclick="showExportTypes('cuisine')" >Export</button>
                                        </form>
                                   </div>-->
                                   <?php } ?>
                                    </div>
                                    <div style="clear:both;"></div>
                                        <div class="table-responsive less-child">
                                            <form class="_list_form" id="_list_form" method="post"
					    action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                            <table class="table table-striped table-bordered table-hover">
                                                <thead>
                                                    <tr>
                                        <?php if ($userObj->hasPermission(['update-status-item-type', 'delete-item-type'])) { ?>
                                                        <th align="center" width="3%" style="text-align:center;">
                                                        <input type="checkbox" id="setAllCheck" >
							</th>
                                        <?php } ?>
                                        <th>Image</th>
							<th width="18%">
							<a href="javascript:void(0);"
							onClick="Redirect(1,<?php if ('1' === $sortby) {
							    echo $order;
							} else { ?>0<?php } ?>)">Item Type <?php if (1 === $sortby) {
							    if (0 === $order) { ?>
							<i class="fa fa-sort-amount-asc"
							aria-hidden="true"></i> <?php } else { ?>
							<i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php }
							} else { ?>
							<i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a>
							</th>
                                                        <?php if (count($service_cat_data) > 1) { ?>
							<th width="15%">Service Category</th>
                                                        <?php } ?>
                                                        <th width="8%">
                                                        <a href="javascript:void(0);"
							onClick="Redirect(5,<?php if ('5' === $sortby) {
							    echo $order;
							} else { ?>0<?php } ?>)">Display Order <?php if (1 === $sortby) {
							    if (0 === $order) { ?>
							<i class="fa fa-sort-amount-asc"
							aria-hidden="true"></i> <?php } else { ?>
							<i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php }
							} else { ?>
							<i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a>
							</th>
							<th width="8%" align="center" style="text-align:center;">
							<a href="javascript:void(0);"
							onClick="Redirect(4,<?php if ('4' === $sortby) {
							    echo $order;
							} else { ?>0<?php } ?>)">Status <?php if (4 === $sortby) {
							    if (0 === $order) { ?>
							<i class="fa fa-sort-amount-asc"
							aria-hidden="true"></i> <?php } else { ?>
							<i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php }
							} else { ?>
							<i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a>
							</th>
                                        <?php
							                    if ($userObj->hasPermission(['edit-item-type', 'update-status-item-type', 'delete-item-type'])) { ?>
                                                        <th width="8%" align="center" style="text-align:center;">Action</th>
                                        <?php } ?>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
							                        if (!empty($data_drv)) {
							                            for ($i = 0; $i < count($data_drv); ++$i) {
							                                $default = '';
							                                if ('Yes' === $data_drv[$i]['eDefault']) {
							                                    $default = 'disabled';
							                                } ?>
                                                    <tr class="gradeA">
                                                        <?php if ($userObj->hasPermission(['update-status-item-type', 'delete-item-type'])) { ?>
							<td align="center" style="text-align:center;">
							<input type="checkbox" id="checkbox"
							name="checkbox[]" <?php echo $default; ?>
							value="<?php echo $data_drv[$i]['cuisineId']; ?>" />&nbsp;
							</td>
                                                <?php } ?>
                                                <td><img src="<?php echo $tconfig['tsite_url'].'resizeImg.php?w=50&src='.$tconfig['tsite_upload_images_menu_item_type'].'/'.$data_drv[$i]['vImage']; ?>"></td>
                                                        <td><?php echo $data_drv[$i]['cuisineName_'.$default_lang]; ?></td>
                                                        <?php if (count($service_cat_data) > 1) { ?>
                                                        <td>
						<?php foreach ($service_cat_data as $servicedata) { ?>
								<?php if ($servicedata['iServiceId'] === $data_drv[$i]['iServiceId']) { ?>
								<span><?php echo $servicedata['vServiceName'] ?? ''; ?></span><?php } ?>
														<?php } ?>
														</td>
                                                        <?php } ?>
                                                        <td><?php echo ('Yes' === $data_drv[$i]['eDefault']) ? '--' : $data_drv[$i]['iDisplayOrder']; ?></td>
                                                        <td align="center" style="text-align:center;">
                                                            <?php if ('Active' === $data_drv[$i]['eStatus']) {
                                                                $dis_img = 'img/active-icon.png';
                                                            } elseif ('Inactive' === $data_drv[$i]['eStatus']) {
                                                                $dis_img = 'img/inactive-icon.png';
                                                            } elseif ('Deleted' === $data_drv[$i]['eStatus']) {
                                                                $dis_img = 'img/delete-icon.png';
                                                            }?>
                                                            <img src="<?php echo $dis_img; ?>" alt="<?php echo $data_drv[$i]['eStatus']; ?>"
							    data-toggle="tooltip" title="<?php echo $data_drv[$i]['eStatus']; ?>">
                                                        </td>
                                                <?php
                                                if ($userObj->hasPermission(['edit-item-type', 'update-status-item-type', 'delete-item-type'])) {
                                                    if ('Yes' === $data_drv[$i]['eDefault']) {
                                                        if ($userObj->hasPermission('edit-item-type')) { ?>
                                                        <td align="center" style="text-align:center;"
							class="action-btn001">
                                                            <a href="cuisine_action.php?id=<?php echo $data_drv[$i]['cuisineId']; ?>"
							    data-toggle="tooltip" title="Edit">
                                                                <img src="img/edit-icon.png" alt="Edit">
                                                            </a>
                                                        </td>
                                                        <?php } else { ?>
                                                        <td align="center" style="text-align:center;"
							class="action-btn001">
                                                                --
                                                            </td>
                                                       <?php }
                                                        } else { ?>
                                                        <td align="center" style="text-align:center;"
                                                            class="action-btn001">
                                                            <div class="share-button openHoverAction-class"
                                                                style="display: block;">
								<label class="entypo-export">
								<span><img src="images/settings-icon.png"
								alt=""></span>
								</label>
                                                                <div class="social show-moreOptions for-five openPops_<?php echo $data_drv[$i]['cuisineId']; ?>">
                                                                    <ul>
                                                                        <?php if ($userObj->hasPermission('edit-item-type')) { ?>
                                                                            <li class="entypo-twitter"
                                                                                data-network="twitter">
                                                                                <a href="cuisine_action.php?id=<?php echo $data_drv[$i]['cuisineId']; ?>"
                                                                                   data-toggle="tooltip" title="Edit">
                                                                                    <img src="img/edit-icon.png"
                                                                                         alt="Edit">
                                                                                </a>
                                                                            </li>
                                                                        <?php } ?>
                                                                        <?php if ('Yes' !== $data_drv[$i]['eDefault']) { ?>
                                                                            <?php if ($userObj->hasPermission('update-status-item-type')) { ?>
                                                                                <li class="entypo-facebook"
                                                                                    data-network="facebook">
                                                                                    <a href="javascript:void(0);"
                                                                                       onclick="changeStatus('<?php echo $data_drv[$i]['cuisineId']; ?>','Inactive')"
                                                                                       data-toggle="tooltip"
                                                                                       title="Activate">
                                                                                        <img src="img/active-icon.png"
                                                                                             alt="<?php echo $data_drv[$i]['eStatus']; ?>">
                                                                                    </a>
                                                                                </li>
                                                                                <li class="entypo-gplus"
                                                                                    data-network="gplus">
                                                                                    <a href="javascript:void(0);"
                                                                                       onclick="changeStatus('<?php echo $data_drv[$i]['cuisineId']; ?>','Active')"
                                                                                       data-toggle="tooltip"
                                                                                       title="Deactivate">
                                                                                        <img src="img/inactive-icon.png"
                                                                                             alt="<?php echo $data_drv[$i]['eStatus']; ?>">
                                                                                    </a>
                                                                                </li>
                                                                            <?php } ?>
                                                                            <?php if ('Deleted' !== $eStatus && $userObj->hasPermission('delete-item-type')) { ?>
                                                                                <li class="entypo-gplus"
                                                                                    data-network="gplus">
                                                                                    <a href="javascript:void(0);"
                                                                                       onclick="changeStatusDelete('<?php echo $data_drv[$i]['cuisineId']; ?>')"
                                                                                       data-toggle="tooltip"
                                                                                       title="Delete">
                                                                                        <img src="img/delete-icon.png"
                                                                                             alt="Delete">
                                                                                    </a>
                                                                                </li>
                                                                            <?php } ?>
                                                                        <?php } ?>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    <?php } ?>
                                                <?php } ?>
                                            </tr>
                                        <?php }
							                            } else { ?>
                                        <tr class="gradeA">
                                            <td colspan="7"> No Records Found.</td>
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
                        Item Type module will list all Item Types on this page.
                    </li>
                    <li>
                        Administrator can Activate / Deactivate / Delete any cuisine.
                    </li>
                    <!-- <li>
                        Administrator can export data in XLS or PDF format.
                    </li> -->
                </ul>
            </div>
        </div>
    </div>
    <!--END PAGE CONTENT -->
</div>
<!--END MAIN WRAPPER -->
<form name="pageForm" id="pageForm" action="action/cuisine.php" method="post">
    <input type="hidden" name="page" id="page" value="<?php echo $page; ?>">
    <input type="hidden" name="tpages" id="tpages" value="<?php echo $tpages; ?>">
    <input type="hidden" name="cuisineId" id="iMainId01" value="">
    <input type="hidden" name="eStatus" id="eStatus" value="<?php echo $eStatus; ?>">
    <input type="hidden" name="status" id="status01" value="">
    <input type="hidden" name="statusVal" id="statusVal" value="">
    <input type="hidden" name="option" value="<?php echo $option; ?>">
    <input type="hidden" name="keyword" value="<?php echo $keyword; ?>">
    <input type="hidden" name="sortby" id="sortby" value="<?php echo $sortby; ?>">
    <input type="hidden" name="order" id="order" value="<?php echo $order; ?>">
    <input type="hidden" name="method" id="method" value="">
    <input type="hidden" name="selectcategory" value="<?php echo $select_cat; ?>">
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
</script>
</body>
<!-- END BODY-->
</html>