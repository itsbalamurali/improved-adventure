<?php
include_once '../common.php';
if (!$userObj->hasPermission('view-state')) {
    $userObj->redirect();
}
$script = $tbl_name = 'state';
// Start Sorting
$sortby = $_REQUEST['sortby'] ?? 0;
$order = $_REQUEST['order'] ?? '';
$ord = ' ORDER BY s.vState ASC';
if (1 === $sortby) {
    if (0 === $order) {
        $ord = ' ORDER BY c.vCountry ASC';
    } else {
        $ord = ' ORDER BY c.vCountry DESC';
    }
}
if (2 === $sortby) {
    if (0 === $order) {
        $ord = ' ORDER BY s.vState ASC';
    } else {
        $ord = ' ORDER BY s.vState DESC';
    }
}
if (3 === $sortby) {
    if (0 === $order) {
        $ord = ' ORDER BY s.vStateCode ASC';
    } else {
        $ord = ' ORDER BY s.vStateCode DESC';
    }
}
if (4 === $sortby) {
    if (0 === $order) {
        $ord = ' ORDER BY s.eStatus ASC';
    } else {
        $ord = ' ORDER BY s.eStatus DESC';
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
        if (str_contains($option, 's.eStatus')) {
            $ssql .= ' AND '.stripslashes($option)." LIKE '".stripslashes($keyword)."'";
        } else {
            $ssql .= ' AND '.stripslashes($option)." LIKE '%".stripslashes($keyword)."%'";
        }
    } else {
        $ssql .= " AND (c.vCountry LIKE '%".$keyword."%' OR s.vState LIKE '%".$keyword."%' OR s.vStateCode LIKE '%".$keyword."%' OR s.eStatus LIKE '%".$keyword."%')";
    }
}
// End Search Parameters
// Pagination Start
$per_page = $DISPLAY_RECORD_NUMBER; // number of results to show per page
$sql = "SELECT COUNT(iStateId) AS Total FROM state AS s INNER JOIN country AS c ON c.iCountryId = s.iCountryId WHERE s.eStatus != 'Deleted' {$ssql}";
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
// $sql = "SELECT * FROM ".$tbl_name."  WHERE eStatus != 'Deleted' $ssql $ord LIMIT $start, $per_page ";
$sql = "SELECT s.iStateId, s.vState, c.iCountryId,c.vCountry,s.vStateCode,s.eStatus FROM state AS s INNER JOIN country AS c ON c.iCountryId = s.iCountryId WHERE s.eStatus !=  'Deleted' {$ssql} {$ord} LIMIT {$start}, {$per_page} ";
$data_drv = $obj->MySQLSelect($sql);
// echo '<pre>--->'; print_r($data_drv); //die;
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
    <title><?php echo $SITE_NAME; ?> | State</title>
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
                        <h2><?php echo $langage_lbl_admin['LBL_CAR_STATE_ADMIN']; ?></h2>
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
                        <td width="10%" class=" padding-right10">
                            <select name="option" id="option" class="form-control">
                                <option value="">All</option>
                                <option value="s.vState" <?php
                                if ('vState' === $option) {
                                    echo 'selected';
                                }
?> >State
                                </option>
                                <option value="s.vStateCode" <?php
if ('vStateCode' === $option) {
    echo 'selected';
}
?> >Code
                                </option>
                                <option value="c.vCountry" <?php
if ('c.vCountry' === $option) {
    echo 'selected';
}
?> >Country
                                </option>
                                <?php /* <option value="vStateCodeISO_3" <?php if ($option == 'vContactNo') {echo "selected"; } ?> >ISO_3 Code</option> */ ?>
                                <option value="s.eStatus" <?php
if ('s.eStatus' === $option) {
    echo 'selected';
}
?> >Status
                                </option>
                            </select>
                        </td>
                        <td width="15%">
                            <input type="Text" id="keyword" name="keyword" value="<?php echo $keyword; ?>"
                                   class="form-control"/>
                        </td>
                        <td>
                            <input type="submit" value="Search" class="btnalt button11" id="Search" name="Search"
                                   title="Search"/>
                            <input type="button" value="Reset" class="btnalt button11"
                                   onClick="window.location.href = 'state.php'"/>
                        </td>
                        <?php if ($userObj->hasPermission('create-state')) { ?>
                            <td width="30%">
                                <a class="add-btn" href="state_action.php" style="text-align: center;">Add State</a>
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
                                            <?php if ($userObj->hasPermission(['update-status-state', 'delete-state'])) { ?>
                                                <select name="changeStatus" id="changeStatus" class="form-control"
                                                        onchange="ChangeStatusAll(this.value);">
                                                    <option value="">Select Action</option>
                                                    <?php if ($userObj->hasPermission('update-status-state')) { ?>
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
                                                    <?php if ($userObj->hasPermission('delete-state')) { ?>
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
                                <div class="panel-heading">
                                    <form name="_export_form" id="_export_form" method="post">
                                        <button type="button" onclick="showExportTypes('state')">Export</button>
                                    </form>
                                </div>
                            <?php } ?>
                        </div>
                                <div style="clear:both;"></div>
                                <div class="table-responsive">
                                    <form class="_list_form" id="_list_form" method="post"
				    action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                        <table class="table table-striped table-bordered table-hover">
                                            <thead>
                                                <tr>
                                        <?php if ($userObj->hasPermission(['update-status-state', 'delete-state'])) { ?>
                                                    <th align="center" width="3%" style="text-align:center;">
						    <input type="checkbox" id="setAllCheck" >
						    </th>
                                        <?php } ?>
					<th width="22%">
					<a href="javascript:void(0);" onClick="Redirect(2,<?php
                                                        if ('2' === $sortby) {
                                                            echo $order;
                                                        } else {
                                                            ?>0<?php } ?>)">State <?php
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
							     <th width="10%" style="text-align:center;">
							     <a href="javascript:void(0);" onClick="Redirect(3,<?php
                                                                                                       if ('3' === $sortby) {
                                                                                                           echo $order;
                                                                                                       } else {
                                                                                                           ?>0<?php } ?>)">State Code <?php
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
							   <th width="15%">
							   <a href="javascript:void(0);" onClick="Redirect(1,<?php
                                                                                                                                                           if ('1' === $sortby) {
                                                                                                                                                               echo $order;
                                                                                                                                                           } else {
                                                                                                                                                               ?>0<?php } ?>)">Country <?php
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
                                        <?php if ($userObj->hasPermission(['edit-state', 'update-status-state', 'delete-state'])) { ?>
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
        }
        ?>
                                                        <tr class="gradeA">
                                                            <?php if ($userObj->hasPermission(['update-status-state', 'delete-state'])) { ?>
							    <td align="center" style="text-align:center;">
							    <input type="checkbox" id="checkbox"
							    name="checkbox[]" <?php echo $default; ?>
							    value="<?php echo $data_drv[$i]['iStateId']; ?>" />&nbsp;
							    </td>
                                                <?php } ?>
                                                            <td><?php echo $data_drv[$i]['vState']; ?></td>
                                                            <td align="center"><?php echo $data_drv[$i]['vStateCode']; ?></td>
                                                            <td><?php echo $data_drv[$i]['vCountry']; ?></td>
                                                                <?php /* <td><?= $data_drv[$i]['vStateCodeISO_3']; ?></td> */ ?>

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
                                                <?php if ($userObj->hasPermission(['edit-state', 'update-status-state', 'delete-state'])) { ?>
                                                            <td align="center" style="text-align:center;" class="action-btn001">
                                                                <div class="share-button openHoverAction-class"
								style="display: block;">
								<label class="entypo-export">
								<span><img src="images/settings-icon.png" alt=""></span>
								</label>
                                                                    <div class="social show-moreOptions openPops_<?php echo $data_drv[$i]['iStateId']; ?>">
                                                                        <ul>
                                                                            <?php if ($userObj->hasPermission(['update-status-state', 'delete-state'])) { ?>
									    <li class="entypo-twitter"
									    data-network="twitter">
									    <a href="state_action.php?id=<?php echo $data_drv[$i]['iStateId']; ?>"
									    data-toggle="tooltip" title="Edit">
                                                                                    <img src="img/edit-icon.png" alt="Edit">
										    </a>
										    </li>
                                                                    <?php } ?>
                                                                            <?php if ('Yes' !== $data_drv[$i]['eDefault']) { ?>
                                                                                <?php if ($userObj->hasPermission('update-status-state')) { ?>
                                                                                    <li class="entypo-facebook"
										    data-network="facebook">
										    <a href="javascript:void(0);"
										    onclick="changeStatus('<?php echo $data_drv[$i]['iStateId']; ?>', 'Inactive')"
										    data-toggle="tooltip"
										    title="Activate">
										    <img src="img/active-icon.png"
										    alt="<?php echo $data_drv[$i]['eStatus']; ?>" >
										     </a>
										     </li>
                                                                                    <li class="entypo-gplus"
										    data-network="gplus">
										    <a href="javascript:void(0);"
                                                                                            onclick="changeStatus('<?php echo $data_drv[$i]['iStateId']; ?>', 'Active')"
											    data-toggle="tooltip"
											    title="Deactivate">
											    <img src="img/inactive-icon.png"
											    alt="<?php echo $data_drv[$i]['eStatus']; ?>" >
											     </a>
											     </li>
            <?php } ?>
            <?php if ($userObj->hasPermission('delete-state')) { ?>
                                                                                    <li class="entypo-gplus"
										    data-network="gplus">
										    <a href="javascript:void(0);"
										    onclick="changeStatusDelete('<?php echo $data_drv[$i]['iStateId']; ?>')"
										    data-toggle="tooltip" title="Delete">
                                                                                            <img src="img/delete-icon.png"
											    alt="Delete" >
											    </a>
											    </li>
                                                            <?php } ?>
                                                        <?php } ?>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                <?php } ?>
                                                        </tr>
    <?php
    }
} else {
    ?>
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
                        State module will list all states on this page.
                    </li>
                    <li>
                        Can Activate / Deactivate / Delete any state.
                    </li>
                    <li>
                    <li>
                        Administrator can export data in XLS format.
                    </li>
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
<form name="pageForm" id="pageForm" action="action/state.php" method="post">
    <input type="hidden" name="page" id="page" value="<?php echo $page; ?>">
    <input type="hidden" name="tpages" id="tpages" value="<?php echo $tpages; ?>">
    <input type="hidden" name="iStateId" id="iMainId01" value="">
    <input type="hidden" name="status" id="status01" value="">
    <input type="hidden" name="statusVal" id="statusVal" value="">
    <input type="hidden" name="option" value="<?php echo $option; ?>">
    <input type="hidden" name="keyword" value="<?php echo $keyword; ?>">
    <input type="hidden" name="sortby" id="sortby" value="<?php echo $sortby; ?>">
    <input type="hidden" name="order" id="order" value="<?php echo $order; ?>">
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
        //$('html').addClass('loading');
        var action = $("#_list_form").attr('action');
        // alert(action);
        var formValus = $("#frmsearch").serialize();
//                alert(action+formValus);
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