
<?php
include_once('../common.php');
$AUTH_OBJ->checkMemberAuthentication();
if (!$userObj->hasPermission('view-airport-surcharge')) {
    $userObj->redirect();
}
$script = 'airportsurcharge_fare';
$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : '';
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'view';
$iLocatioId = isset($_REQUEST['iLocatioId']) ? $_REQUEST['iLocatioId'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';
$success = isset($_REQUEST['success']) ? $_REQUEST['success'] : 0;
$tbl_name = 'airportsurcharge_fare';
//Start Sorting
$sortby = isset($_REQUEST['sortby']) ? $_REQUEST['sortby'] : 0;
$order = isset($_REQUEST['order']) ? $_REQUEST['order'] : '';
$ord = ' ORDER BY ls.iLocatioId DESC';
if ($sortby == 1) {
    if ($order == 0) $ord = " ORDER BY ls.iLocationIds ASC"; else
        $ord = " ORDER BY ls.iLocationIds DESC";
}
if ($sortby == 2) {
    if ($order == 0) $ord = " ORDER BY ls.fpickupsurchargefare ASC"; else
        $ord = " ORDER BY ls.fpickupsurchargefare DESC";
}
if ($sortby == 3) {
    if ($order == 0) $ord = " ORDER BY ls.fdropoffsurchargefare ASC"; else
        $ord = " ORDER BY ls.fdropoffsurchargefare DESC";
}
if ($sortby == 4) {
    if ($order == 0) $ord = " ORDER BY ls.eStatus ASC"; else
        $ord = " ORDER BY ls.eStatus DESC";
}
if ($sortby == 5) {
    if ($order == 0) $ord = " ORDER BY vt.vVehicleType ASC"; else
        $ord = " ORDER BY vt.vVehicleType DESC";
}
//End Sorting
// Start Search Parameters
$option = isset($_REQUEST['option']) ? stripslashes($_REQUEST['option']) : "";
$keyword = isset($_REQUEST['keyword']) ? stripslashes($_REQUEST['keyword']) : "";
$searchDate = isset($_REQUEST['searchDate']) ? $_REQUEST['searchDate'] : "";
$searchVehicleType = isset($_REQUEST['searchVehicleType']) ? $_REQUEST['searchVehicleType'] : "";
$eStatus = isset($_REQUEST['eStatus']) ? $_REQUEST['eStatus'] : "";
$ssql = $eStatussql = '';
if ($keyword != '') {
    if ($option != '') {
        if ($eStatus != '') {
            $ssql .= " AND " . stripslashes($option) . " LIKE '%" . clean($keyword) . "%' AND ls.eStatus = '" . clean($eStatus) . "'";
        } else {
            $ssql .= " AND " . stripslashes($option) . " LIKE '%" . clean($keyword) . "%' AND ls.eStatus != 'Deleted'";
        }
    } else {
        if ($eStatus != '') {
            $ssql .= " AND (lm2.vLocationName LIKE '%" . $keyword . "%' OR vt.vVehicleType LIKE '%" . $keyword . "%') AND ls.eStatus = '" . clean($eStatus) . "'";
        } else {
            $ssql .= " AND lm2.vLocationName LIKE '%" . $keyword . "%' OR vt.vVehicleType LIKE '%" . $keyword . "%' AND ls.eStatus != 'Deleted'";
        }
    }
} else if (!empty($searchVehicleType)) {
    if ($eStatus != '') {
        $ssql .= " AND vt.iVehicleTypeId = '" . ($searchVehicleType) . "' AND ls.eStatus = '" . clean($eStatus) . "'";
    } else {
        $ssql .= " AND vt.iVehicleTypeId = '" . ($searchVehicleType) . "' AND ls.eStatus != 'Deleted'";
    }
} else {
    if ($eStatus != '') {
        $ssql .= " AND ls.eStatus = '" . clean($eStatus) . "'";
    } else {
        $eStatussql = " AND ls.eStatus != 'Deleted'";
    }
}
// End Search Parameters
//Pagination Start
$per_page = $DISPLAY_RECORD_NUMBER; // number of results to show per page
$sql = "SELECT COUNT(ls.iLocatioId) as Total FROM `airportsurcharge_fare` ls  left join location_master lm2 on ls.iLocationIds = lm2.iLocationId left join vehicle_type as vt on vt.iVehicleTypeId=ls.iVehicleTypeId WHERE 1 = 1 $eStatussql $ssql";
$totalData = $obj->MySQLSelect($sql);
$total_results = $totalData[0]['Total'];
$total_pages = ceil($total_results / $per_page); //total pages we going to have
$show_page = 1;
//-------------if page is setcheck------------------//
if (isset($_GET['page'])) {
    $show_page = $_GET['page'];             //it will telles the current page
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
$page = isset($_GET['page']) ? intval($_GET['page']) : 0;
$tpages = $total_pages;
if ($page <= 0) $page = 1;
//Pagination End
$sql = "SELECT ls.*,lm2.vLocationName as vToname,lm2.vLocationName as vFromname,vt.vVehicleType FROM `airportsurcharge_fare` ls left join location_master lm2 on ls.iLocationIds = lm2.iLocationId left join vehicle_type as vt on vt.iVehicleTypeId=ls.iVehicleTypeId WHERE 1 = 1 $eStatussql $ssql $ord LIMIT $start, $per_page";
$data_drv = $obj->MySQLSelect($sql);
$endRecord = count($data_drv);
$var_filter = "";
foreach ($_REQUEST as $key => $val) {
    if ($key != "tpages" && $key != 'page') $var_filter .= "&$key=" . stripslashes($val);
}
$reload = $_SERVER['PHP_SELF'] . "?tpages=" . $tpages . $var_filter;
$query = "SELECT DISTINCT (af.iVehicleTypeId),vt.vVehicleType FROM  `airportsurcharge_fare` as af  left join vehicle_type as vt on vt.iVehicleTypeId=af.iVehicleTypeId ORDER BY  `vt`.`vVehicleType` ASC";
$db_vehicle = $obj->MySQLSelect($query);
?>
<!DOCTYPE html>
<html lang="en">
<!-- BEGIN HEAD-->
<head>
    <meta charset="UTF-8"/>
    <title><?= $SITE_NAME ?> | Airport Surcharge</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <?php include_once('global_files.php'); ?>
</head>
<!-- END  HEAD-->
<!-- BEGIN BODY-->
<body class="padTop53 ">
<!-- Main LOading -->
<!-- MAIN WRAPPER -->
<div id="wrap">
    <?php include_once('header.php'); ?>
    <?php include_once('left_menu.php'); ?>
    <!--PAGE CONTENT -->
    <div id="content">
        <div class="inner">
            <div id="add-hide-show-div">
                <div class="row">
                    <div class="col-lg-12">
                        <h2>Airport Surcharge</h2>
                        <!--<input type="button" id="" value="ADD A DRIVER" class="add-btn">-->
                    </div>
                </div>
                <hr/>
            </div>
                    <?php include('valid_msg.php'); ?>
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
                                      <option value="lm2.vLocationName" <?php if ($option == "lm2.vLocationName") { 
				      echo "selected"; 
				      } ?> >Airport Location
				      </option>
                                    <option value="vt.vVehicleType" <?php if ($option == "vt.vVehicleType") { 
                                     echo "selected"; 
				     } ?> >Vehicle Type
				     </option> 
                                    </select>
                                    </td>
				     <td width="15%" class="searchform">
				     <input type="Text" id="keyword" name="keyword" value="<?php echo $keyword; ?>"  
				     class="form-control" />
				     </td>
                                    <?php   
                                    if(!empty($db_vehicle)) {?>
                                    <td width="12%" class="estatus_options" id="eStatus_options" >
                                        <select class="form-control" name ="searchVehicleType" id="searchVehicleType" 
					required="required">
                                            <option value="">Select Vehicle Type</option>
                                           <?php 
                                                    for ($ii = 0; $ii < count($db_vehicle); $ii++) { 
                                                    $VehicleTypeId=$db_vehicle[$ii]['iVehicleTypeId'];
                                                    $VehicleType=$db_vehicle[$ii]['vVehicleType'];
                                                ?> 
						 <option value ="<?php echo $VehicleTypeId; ?>" 
						 <?php if ($VehicleTypeId == "$searchVehicleType") { ?>selected<? } ?>><?php echo $VehicleType; ?></option>
                                    <?php } ?>
                                </select>
                            </td>
                        <?php } ?>
                        <td width="12%" class="estatus_otions" id="eStatus_otions">
                            <select name="eStatus" id="estatus_value" class="form-control">
                                <option value="">Select Status</option>
                                <option value='Active' <?php if ($eStatus == 'Active') {
                                    echo "selected";
                                } ?> >Active
                                </option>
                                <option value="Inactive" <?php if ($eStatus == 'Inactive') {
                                    echo "selected";
                                } ?> >Inactive
                                </option>
                                <option value="Deleted" <?php if ($eStatus == 'Deleted') {
                                    echo "selected";
                                } ?> >Delete
                                </option>
                            </select>
                        </td>
                        <td>
                            <input type="submit" value="Search" class="btnalt button11" id="Search" name="Search"
                                   title="Search"/>
                            <input type="button" value="Reset" class="btnalt button11"
                                   onClick="window.location.href='airport_surcharge.php'"/>
                        </td>
                        <?php if ($userObj->hasPermission('create-airport-surcharge')) { ?>
                            <td width="20%">
                                <a class="add-btn" href="airport_surcharge_action.php" style="text-align: center;">Add
                                    Surcharge
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
                                      <?php if ($userObj->hasPermission(['update-status-airport-surcharge', 'delete-airport-surcharge'])) { ?>
                                          <select name="changeStatus" id="changeStatus" class="form-control"
                                                  onchange="ChangeStatusAll(this.value);">
                                              <option value="">Select Action</option>
                                              <?php if ($userObj->hasPermission('update-status-airport-surcharge')) { ?>
                                                  <option value='Active' <?php if ($option == 'Active') {
                                                      echo "selected";
                                                  } ?> >Activate</option>
                                                  <option value="Inactive" <?php if ($option == 'Inactive') {
                                                      echo "selected";
                                                  } ?> >Deactivate</option>
                                              <?php } ?>
                                              <?php if ($userObj->hasPermission('delete-airport-surcharge')) { ?>
                                                  <option value="Deleted" <?php if ($option == 'Delete') {
                                                      echo "selected";
                                                  } ?> >Delete</option>
                                              <?php } ?>
                                            </select>
                                      <?php } ?>
                                    </span>
                                    </div>
                                    </div>
                                    <div style="clear:both;"></div>
                                        <div class="table-responsive">
                                            <form class="_list_form" id="_list_form" method="post" 
					    action="<?php echo $_SERVER['PHP_SELF'] ?>">
                                            <table class="table table-striped table-bordered table-hover">
                                                <thead>
                                                    <tr>
                                        <?php if ($userObj->hasPermission(['update-status-airport-surcharge', 'delete-airport-surcharge'])) { ?>
                                                        <th align="center" width="3%" style="text-align:center;">
							<input type="checkbox" id="setAllCheck" >
							</th>
					        <?php } ?>

                                        <th width="20%">
                                            <a href="javascript:void(0);"
                                               onClick="Redirect(1,<?php if ($sortby == '1') {
                                                   echo $order;
                                               } else { ?>0<?php } ?>)">Airport Location <?php if ($sortby == 1) {
                                                    if ($order == 0) { ?>
                                                        <i class="fa fa-sort-amount-asc"
                                                           aria-hidden="true"></i> <?php } else { ?>
                                                        <i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php }
                                                } else { ?>
                                                    <i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a>
                                        </th>
                                        <th width="20%">
                                            <a href="javascript:void(0);"
                                               onClick="Redirect(2,<?php if ($sortby == '2') {
                                                   echo $order;
                                               } else { ?>0<?php } ?>)">Pickup Surcharge <?php if ($sortby == 2) {
                                                    if ($order == 0) { ?>
                                                        <i class="fa fa-sort-amount-asc"
                                                           aria-hidden="true"></i> <?php } else { ?>
                                                        <i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php }
                                                } else { ?>
                                                    <i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a>
                                        </th>
                                        <th width="20%">
                                            <a href="javascript:void(0);"
                                               onClick="Redirect(3,<?php if ($sortby == '3') {
                                                   echo $order;
                                               } else { ?>0<?php } ?>)">Dropoff Surcharge <?php if ($sortby == 3) {
                                                    if ($order == 0) { ?>
                                                        <i class="fa fa-sort-amount-asc"
                                                           aria-hidden="true"></i> <?php } else { ?>
                                                        <i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php }
                                                } else { ?>
                                                    <i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a>
                                        </th>
                                        <th width="20%">
                                            <a href="javascript:void(0);"
                                               onClick="Redirect(5,<?php if ($sortby == '5') {
                                                   echo $order;
                                               } else { ?>0<?php } ?>)">Vehicle Type <?php if ($sortby == 5) {
                                                    if ($order == 0) { ?>
                                                        <i class="fa fa-sort-amount-asc"
                                                           aria-hidden="true"></i> <?php } else { ?>
                                                        <i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php }
                                                } else { ?>
                                                    <i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a>
                                        </th>
                                        <th width="8%" align="center" style="text-align:center;">
                                            <a href="javascript:void(0);"
                                               onClick="Redirect(4,<?php if ($sortby == '4') {
                                                   echo $order;
                                               } else { ?>0<?php } ?>)">Status <?php if ($sortby == 4) {
                                                    if ($order == 0) { ?>
                                                        <i class="fa fa-sort-amount-asc"
                                                           aria-hidden="true"></i> <?php } else { ?>
                                                        <i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php }
                                                } else { ?>
                                                    <i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a>
                                        </th>
                                        <?php if ($userObj->hasPermission(['edit-airport-surcharge', 'update-status-airport-surcharge', 'delete-airport-surcharge'])) { ?>
                                                        <th width="8%" align="center" style="text-align:center;">Action</th>
                                        <?php } ?>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 
                                                    if(!empty($data_drv)) {
                                                    for ($i = 0; $i < count($data_drv); $i++) { 
                                                        
                                                        $default = '';
                                                        if($data_drv[$i]['eDefault']=='Yes'){
                                                                $default = 'disabled';
                                                        } ?>
                                                    <tr class="gradeA">
                                                <?php if ($userObj->hasPermission(['update-status-airport-surcharge', 'delete-airport-surcharge'])) { ?>
                                                    <td align="center" style="text-align:center;">
                                                        <input type="checkbox" id="checkbox"
                                                               name="checkbox[]" <?php echo $default; ?>
                                                               value="<?php echo $data_drv[$i]['iLocatioId']; ?>"/>&nbsp;
                                                    </td>
                                                <?php } ?>
                                                        <td><?= $data_drv[$i]['vFromname']; ?></td> 
                                                <td>
                                                    X <?= setTwoDecimalPoint($data_drv[$i]['fpickupsurchargefare']); ?></td>
                                                <td>
                                                    X <?= setTwoDecimalPoint($data_drv[$i]['fdropoffsurchargefare']); ?></td>
                                                        <td><?= $data_drv[$i]['vVehicleType']; ?></td>
                                                        <td align="center" style="text-align:center;">
                                                            <?php if($data_drv[$i]['eStatus'] == 'Active') {
                                                            $dis_img = "img/active-icon.png";
                                                            }else if($data_drv[$i]['eStatus'] == 'Inactive'){
                                                            $dis_img = "img/inactive-icon.png";
                                                            }else if($data_drv[$i]['eStatus'] == 'Deleted'){
                                                            $dis_img = "img/delete-icon.png";
                                                            }?>
                                                    <img src="<?= $dis_img; ?>" alt="<?= $data_drv[$i]['eStatus']; ?>"
                                                         data-toggle="tooltip" title="<?= $data_drv[$i]['eStatus']; ?>">
                                                        </td>
                                                <?php if ($userObj->hasPermission(['edit-airport-surcharge', 'update-status-airport-surcharge', 'delete-airport-surcharge'])) { ?>
                                                        <td align="center" style="text-align:center;" class="action-btn001">
                                                            <div class="share-button openHoverAction-class" 
							    style="display: block;">
							     <label class="entypo-export">
							     <span><img src="images/settings-icon.png" alt=""></span>
							     </label>
                                                                <div class="social show-moreOptions openPops_<?= $data_drv[$i]['iLocatioId']; ?>">
                                                                    <ul>
                                                                          <?php if($userObj->hasPermission('edit-airport-surcharge')){ ?>
									  <li class="entypo-twitter" 
									  data-network="twitter">
									  <a href="airport_surcharge_action.php?id=<?= $data_drv[$i]['iLocatioId']; ?>" 
									  data-toggle="tooltip" title="Edit">
                                                                            <img src="img/edit-icon.png" alt="Edit">
									    </a>
									    </li>
                                                                          <?php } ?>
                                                                        <?php if ($data_drv[$i]['eDefault'] != 'Yes') { ?>

                                                                          <?php if($userObj->hasPermission('update-status-airport-surcharge')){ ?>
                                                                          <li class="entypo-facebook" 
									  data-network="facebook">
									  <a href="javascript:void(0);" 
									  onclick="changeStatus('<?php echo $data_drv[$i]['iLocatioId']; ?>','Inactive')"  
									  data-toggle="tooltip" 
									  title="Activate">
									    <img src="img/active-icon.png" 
									    alt="<?php echo $data_drv[$i]['eStatus']; ?>" >
									    </a>
									    </li>
                                                                          <li class="entypo-gplus" 
									  data-network="gplus">
                                                                                <a href="javascript:void(0);"
                                                                                   onclick="changeStatus('<?php echo $data_drv[$i]['iLocatioId']; ?>','Active')"
                                                                                   data-toggle="tooltip"
                                                                                   title="Deactivate">
                                                                                    <img src="img/inactive-icon.png"
                                                                                         alt="<?php echo $data_drv[$i]['eStatus']; ?>">
                                                                                </a>
                                                                            </li>
                                                                        <?php } ?>
                                                                        <?php if ($userObj->hasPermission('delete-airport-surcharge')) { ?>
                                                                            <li class="entypo-gplus"
                                                                                data-network="gplus">
                                                                                <a href="javascript:void(0);"
                                                                                   onclick="changeStatusDelete('<?php echo $data_drv[$i]['iLocatioId']; ?>')"
                                                                                   data-toggle="tooltip" title="Delete">
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
                            <?php include('pagination_n.php'); ?>
                        </div>
                    </div> <!--TABLE-END-->
                </div>
            </div>
            <div class="admin-notes">
                <h4>Notes:</h4>
                <ul>
                    <li>
                        Airport Surcharge module will list all locations for airport surcharge on this page.
                    </li>
                    <li>
                        Administrator can Activate / Deactivate / Delete any location for Airport Surcharge .
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <!--END PAGE CONTENT -->
</div>
<!--END MAIN WRAPPER -->
<form name="pageForm" id="pageForm" action="action/airport_surcharge.php" method="post">
    <input type="hidden" name="page" id="page" value="<?php echo $page; ?>">
    <input type="hidden" name="tpages" id="tpages" value="<?php echo $tpages; ?>">
    <input type="hidden" name="iLocatioId" id="iMainId01" value="">
    <input type="hidden" name="status" id="status01" value="">
    <input type="hidden" name="statusVal" id="statusVal" value="">
    <input type="hidden" name="option" value="<?php echo $option; ?>">
    <input type="hidden" name="keyword" value="<?php echo $keyword; ?>">
    <input type="hidden" name="sortby" id="sortby" value="<?php echo $sortby; ?>">
    <input type="hidden" name="order" id="order" value="<?php echo $order; ?>">
    <input type="hidden" name="method" id="method" value="">
</form>
<?php
include_once('footer.php');
?>
<script>
    $(document).ready(function () {
        $('#eStatus_options').hide();
        $('#option').each(function () {
            if (this.value == 'vt.vVehicleType') {
                $('#eStatus_options').show();
                $('.searchform').hide();
            }
        });
    });

    $(function () {
        $('#option').change(function () {
            if ($('#option').val() == 'vt.vVehicleType') {
                $('#eStatus_options').show();
                $("input[name=keyword]").val("");
                $('.searchform').hide();
            } else {
                $('#eStatus_options').hide();
                $("#estatus_value").val("");
                $('.searchform').show();
            }
        });
    });

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
//               alert(action+formValus);
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