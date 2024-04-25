<?php
include_once '../common.php';
$AUTH_OBJ->checkMemberAuthentication();
if (!$userObj->hasPermission('view-visit')) {
    $userObj->redirect();
}
$script = 'Visit';
$hdn_del_id = $_POST['hdn_del_id'] ?? '';
$iVisitId = $_GET['iVisitId'] ?? '';
$status = $_GET['status'] ?? '';
$success = $_REQUEST['success'] ?? 0;
$tbl_name = 'visit_address';
if ('' !== $hdn_del_id) {
    if (SITE_TYPE !== 'Demo') {
        $query = 'DELETE FROM `'.$tbl_name."` WHERE iVisitId = '".$hdn_del_id."'"; // die;
        $obj->sql_query($query);
    } else {
        header('Location:visit.php?success=2');

        exit;
    }
}
if ('' !== $iVisitId && '' !== $status) {
    if (SITE_TYPE !== 'Demo') {
        $query = 'UPDATE `'.$tbl_name."` SET eStatus = '".$status."' WHERE iVisitId = '".$iVisitId."'";
        $obj->sql_query($query);
    } else {
        header('Location:visit.php?success=2');

        exit;
    }
}
// added by SP for search related and hotel wise changes on 1-7-2019
// Start Sorting
$sortby = $_REQUEST['sortby'] ?? 0;
$order = $_REQUEST['order'] ?? '';
$eStatus = $_REQUEST['eStatus'] ?? '';
$ord = ' ORDER BY iVisitId DESC';
if (1 === $sortby) {
    if (0 === $order) {
        $ord = ' ORDER BY tDestLocationName ASC';
    } else {
        $ord = ' ORDER BY tDestLocationName DESC';
    }
}
if (2 === $sortby) {
    if (0 === $order) {
        $ord = ' ORDER BY tDestAddress ASC';
    } else {
        $ord = ' ORDER BY tDestAddress DESC';
    }
}
if (3 === $sortby) {
    if (0 === $order) {
        $ord = ' ORDER BY eStatus ASC';
    } else {
        $ord = ' ORDER BY eStatus DESC';
    }
}
// End Sorting
// Start Search Parameters
$option = isset($_REQUEST['option']) ? stripslashes($_REQUEST['option']) : '';
$keyword = isset($_REQUEST['keyword']) ? stripslashes($_REQUEST['keyword']) : '';
$searchDate = $_REQUEST['searchDate'] ?? '';
$ssql = '';
if ('' !== $keyword) {
    if ('' !== $option) {
        $ssql .= ' AND '.stripslashes($option)." LIKE '%".stripslashes($keyword)."%'";
    } else {
        $ssql .= " AND (tDestLocationName LIKE '%".$keyword."%' OR tDestAddress LIKE '%".$keyword."%' OR eStatus LIKE '%".$keyword."%')";
    }
}
if (!empty($eStatus)) {
    $ssql .= " AND eStatus = '".$eStatus."'";
} else {
    $ssql .= " AND eStatus != 'Deleted'";
}
// End Search Parameters
if (isset($_SESSION['SessionUserType']) && 'hotel' === $_SESSION['SessionUserType']) {
    $ssql .= " AND iHotelId = '".$_SESSION['sess_iAdminUserId']."'";
}
// Pagination Start
$per_page = $DISPLAY_RECORD_NUMBER; // number of results to show per page
$sql = "SELECT COUNT(iVisitId) AS Total FROM visit_address WHERE 1 {$ssql}";
$totalData = $obj->MySQLSelect($sql);
$total_results = $totalData[0]['Total'];
$total_pages = ceil($total_results / $per_page); // total pages we going to have
$show_page = 1;
$start = 0;
$end = $per_page;
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
$data_drv = $obj->MySQLSelect('SELECT * FROM '.$tbl_name." WHERE 1 {$ssql} {$ord} LIMIT {$start}, {$per_page} ");
$endRecord = count($data_drv);
// echo '<pre>--->'; print_r($data_drv);
$var_filter = '';
foreach ($_REQUEST as $key => $val) {
    if ('tpages' !== $key && 'page' !== $key) {
        $var_filter .= "&{$key}=".stripslashes($val);
    }
}
$reload = $_SERVER['PHP_SELF'].'?tpages='.$tpages.$var_filter;

// echo "<pre>";print_r($_SESSION);die;
?>
<!DOCTYPE html>
<html lang="en">
<!-- BEGIN HEAD-->
<head>
    <meta charset="UTF-8"/>
    <title><?php echo $SITE_NAME; ?> | Visit Location</title>
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
                        <h2>Kiosk predefined destination</h2>
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
                                    <td width="5%">
				    <label for="textfield">
				    <strong>Search:</strong>
				    </label>
				    </td>
				     <td width="20%" class=" padding-right10">
				     <select name="option" id="option" class="form-control">
                                            <option value="">All</option>
                                            <option value="tDestLocationName" <?php if ('tDestLocationName' === $option) {
                                                echo 'selected';
                                            } ?> >Destination Location Title
</option>
                                            <option value="tDestAddress" <?php if ('tDestAddress' === $option) {
                                                echo 'selected';
                                            } ?> >Destination Location
</option>
<!--                                            <option value="eStatus" <?php if ('eStatus' === $option) {
    echo 'selected';
} ?> >Status</option>-->
                                        </select>
                                    </td>
				    <td width="15%">
				    <input type="Text" id="keyword" name="keyword" value="<?php echo $keyword; ?>"
				    class="form-control" />
				    </td>
                                    <td width="12%" class="estatus_options" id="eStatus_options" >
                                        <select name="eStatus" id="estatus_value" class="form-control">
                                            <option value="" >Select Status</option>
                                            <option value='Active' <?php
if ('Active' === $eStatus) {
    echo 'selected';
}
?> >Active
</option>
                                            <option value="Inactive" <?php
                                                    if ('Inactive' === $eStatus) {
                                                        echo 'selected';
                                                    }
?> >Inactive
						    </option>
                                            <option value="Deleted" <?php
if ('Deleted' === $eStatus) {
    echo 'selected';
}
?> >Delete
						    </option>
                                        </select>
                                    </td>
                                    <td >
                                        <input type="submit" value="Search" class="btnalt button11" id="Search" name="Search"
					title="Search" />
                                        <input type="button" value="Reset" class="btnalt button11"
					onClick="window.location.href = 'visit.php'"/>
					 <td width="30%">
                            <?php if ($userObj->hasPermission('create-visit')) { ?>
                                <a class="add-btn" href="visit_address_action.php" style="text-align: center;">Add Visit
                                    Location
                                </a>
                            <?php } ?>
                                    </td>
                        </td>
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
<?php if ($userObj->hasPermission([
    'update-status-visit',
    'delete-visit',
])) { ?>
    <select name="changeStatus" id="changeStatus" class="form-control" onchange="ChangeStatusAll(this.value);">
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
<?php } ?>
                                        </span>
                                    </div>
                                                    <?php if (!empty($data_drv)) { ?>
                                        <!--                                   <div class="panel-heading">
                                                                                <form name="_export_form" id="_export_form" method="post" >
                                                                                    <button type="button" onclick="showExportTypes('visitlocation')" >Export</button>
                                                                                </form>
                                                                           </div>-->
<?php } ?>

                        </div>
                        <div style="clear:both;"></div>
                        <div class="table-responsive">
                            <form class="_list_form" id="_list_form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th align="center" width="3%" style="text-align:center;">
                                            <input type="checkbox" id="setAllCheck">
                                        </th>
                                        <?php if ('hotel' !== $_SESSION['SessionUserType']) { ?>
                                        <th>Hotel Name</th>
                                        <?php } ?>
                                        <th width="20%">
                                            <a href="javascript:void(0);"
                                               onClick="Redirect(1,<?php if ('1' === $sortby) {
                                                   echo $order;
                                               } else { ?>0<?php } ?>)">Destination location
                                                Title<?php if (1 === $sortby) {
                                                    if (0 === $order) { ?>
                                                        <i class="fa fa-sort-amount-asc"
                                                           aria-hidden="true"></i> <?php } else { ?>
                                                        <i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php }
                                                           } else { ?>
                                                    <i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a>
                                        </th>
                                        <th width="20%">
                                            <a href="javascript:void(0);"
                                               onClick="Redirect(2,<?php if ('2' === $sortby) {
                                                   echo $order;
                                               } else { ?>0<?php } ?>)">Destination <?php if (2 === $sortby) {
                                                   if (0 === $order) { ?>
                                                        <i class="fa fa-sort-amount-asc"
                                                           aria-hidden="true"></i> <?php } else { ?>
                                                        <i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php }
                                                           } else { ?>
                                                    <i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a>
                                        </th>
                                        <th width="8%" align="center" style="text-align:center;">
                                            <a href="javascript:void(0);"
                                               onClick="Redirect(3,<?php if ('3' === $sortby) {
                                                   echo $order;
                                               } else { ?>0<?php } ?>)">Status <?php if (3 === $sortby) {
                                                   if (0 === $order) { ?>
                                                        <i class="fa fa-sort-amount-asc"
                                                           aria-hidden="true"></i> <?php } else { ?>
                                                        <i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php }
                                                           } else { ?>
                                                    <i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a>
                                        </th>
                                        <th width="8%" align="center" style="text-align:center;">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if (!empty($data_drv)) {
                                        // Added By HJ On 23-09-2020 For Optimize Loop Query Start
                                        $adminIdArr = array_column($data_drv, 'iHotelId');
                                        $adminDataArr = [];
                                        if (count($adminIdArr) > 0) {
                                            $hotelIds = implode(',', $adminIdArr);
                                            $db_visithotel = $obj->MySQLSelect("SELECT * FROM administrators WHERE iAdminId IN ({$hotelIds})");
                                            for ($k = 0; $k < count($db_visithotel); ++$k) {
                                                $adminDataArr[$db_visithotel[$k]['iAdminId']][] = $db_visithotel[$k];
                                            }
                                            // echo "<pre>";print_r($adminDataArr);die;
                                        }
                                        // Added By HJ On 23-09-2020 For Optimize Loop Query End
                                        for ($i = 0; $i < count($data_drv); ++$i) {
                                            $default = '';
                                            if ('Yes' === $data_drv[$i]['eDefault']) {
                                                $default = 'disabled';
                                            }
                                            // Added By HJ On 23-09-2020 For Optimize Loop Query Start
                                            $db_visithotel = [];
                                            if (isset($adminDataArr[$data_drv[$i]['iHotelId']])) {
                                                $db_visithotel = $adminDataArr[$data_drv[$i]['iHotelId']];
                                            }
                                            // $db_visithotel = $obj->MySQLSelect("SELECT * FROM administrators WHERE iAdminId = '" . $data_drv[$i]['iHotelId'] . "'");
                                            // Added By HJ On 23-09-2020 For Optimize Loop Query End
                                            ?>
                                            <tr class="gradeA">
                                                <td align="center" style="text-align:center;">
                                                    <input type="checkbox" id="checkbox"
                                                           name="checkbox[]" <?php echo $default; ?>
                                                           value="<?php echo $data_drv[$i]['iVisitId']; ?>"/>&nbsp;
                                                </td>
                                                <?php if ('hotel' !== $_SESSION['SessionUserType']) { ?>
                                                    <td><?php echo clearName(' '.$db_visithotel[0]['vFirstName'].' '.$db_visithotel[0]['vLastName']); ?></td><?php } ?>
                                                <td><?php echo $data_drv[$i]['tDestLocationName']; ?></td>
                                                <td><?php echo $data_drv[$i]['tDestAddress']; ?></td>
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
                                                    <img src="<?php echo $dis_img; ?>" alt="<?php echo $data_drv[$i]['eStatus']; ?>"
                                                         data-toggle="tooltip" title="<?php echo $data_drv[$i]['eStatus']; ?>">
                                                </td>
                                                <td align="center" style="text-align:center;" class="action-btn001">
                                                    <?php if ($userObj->hasPermission([
                                                'edit-visit',
                                                'delete-visit',
                                                'update-status-visit',
                                            ])) { ?>
                                                        <div class="share-button openHoverAction-class"
                                                             style="display: block;">
                                                            <label class="entypo-export">
                                                                <span><img src="images/settings-icon.png" alt=""></span>
                                                            </label>
                                                            <div class="social show-moreOptions openPops_<?php echo $data_drv[$i]['iVisitId']; ?>">
                                                                <ul>
                                                                    <?php if ($userObj->hasPermission('edit-visit')) { ?>
                                                                        <li class="entypo-twitter"
                                                                            data-network="twitter">
                                                                            <a href="visit_address_action.php?id=<?php echo $data_drv[$i]['iVisitId']; ?>"
                                                                               data-toggle="tooltip" title="Edit">
                                                                                <img src="img/edit-icon.png" alt="Edit">
                                                                            </a>
                                                                        </li>
                                                                    <?php } ?>
                                                                    <?php if ('Yes' !== $data_drv[$i]['eDefault'] && $userObj->hasPermission('update-status-visit')) { ?>
                                                                        <li class="entypo-facebook"
                                                                            data-network="facebook">
                                                                            <a href="javascript:void(0);"
                                                                               onclick="changeStatus('<?php echo $data_drv[$i]['iVisitId']; ?>', 'Inactive')"
                                                                               data-toggle="tooltip"
                                                                               title="Visit Location Active">
                                                                                <img src="img/active-icon.png"
                                                                                     alt="<?php echo $data_drv[$i]['eStatus']; ?>">
                                                                            </a>
                                                                        </li>
                                                                        <li class="entypo-gplus" data-network="gplus">
                                                                            <a href="javascript:void(0);" onclick="changeStatus('<?php echo $data_drv[$i]['iVisitId']; ?>', 'Active')" data-toggle="tooltip"  title="Visit Location Inactive">
                                                                                <img src="img/inactive-icon.png" alt="<?php echo $data_drv[$i]['eStatus']; ?>">
                                                                            </a>
                                                                        </li>
                                                                        <?php if ($userObj->hasPermission('delete-visit')) { ?>
                                                                        <li class="entypo-gplus" data-network="gplus">
                                                                            <a href="javascript:void(0);" onclick="changeStatusDelete('<?php echo $data_drv[$i]['iVisitId']; ?>')" data-toggle="tooltip" title="Delete">
                                                                                <img src="img/delete-icon.png" alt="Delete">
                                                                            </a>
                                                                        </li>
                                                                        <?php } ?>
                                                                    <?php } ?>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    <?php } else {
                                                        echo '--';
                                                    } ?>
                                                </td>
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
                    <li>Visit Location module will list all makes on this page.</li>
                    <li><?php if (isset($_SESSION['SessionUserType']) && 'hotel' === $_SESSION['SessionUserType']) { ?>Hotel<?php } else { ?>Administrator<?php } ?>
                        can Activate / Deactivate / Delete any Visit Location.
                    </li>
                    <li>This Module will list the Pre defined location for hotels.</li>
                    <li>Admin can add the location on behalf of hotel.</li>
                    <li>Also, hotel can add their predefined location from their hotel session.</li>
                </ul>
            </div>
        </div>
    </div>
    <!--END PAGE CONTENT -->
</div>
<!--END MAIN WRAPPER -->
<form name="pageForm" id="pageForm" action="action/visit.php" method="post">
    <input type="hidden" name="page" id="page" value="<?php echo $page; ?>">
    <input type="hidden" name="tpages" id="tpages" value="<?php echo $tpages; ?>">
    <input type="hidden" name="iVisitId" id="iMainId01" value="">
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
</script>
</body>
<!-- END BODY-->
</html>