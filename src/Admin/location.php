<?php
include_once('../common.php');
if (!$userObj->hasPermission('view-geo-fence-locations')) {
    $userObj->redirect();
}
$script = 'Location';
/* get Location */
$hdn_del_id = isset($_POST['hdn_del_id']) ? $_POST['hdn_del_id'] : '';
$iLocationId = isset($_GET['iLocationId']) ? $_GET['iLocationId'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';
$success = isset($_REQUEST['success']) ? $_REQUEST['success'] : 0;
$tbl_name = 'location_master';
//$script           = "Settings";
if ($hdn_del_id != '') {
    if (SITE_TYPE != 'Demo') {
        $query = "DELETE FROM `" . $tbl_name . "` WHERE iLocationId = '" . $hdn_del_id . "'"; //die;
        $obj->sql_query($query);
    } else {
        header("Location:location.php?success=2");
        exit;
    }
}
/*    if($iLocationId != '' && $status != ''){
  if(SITE_TYPE !='Demo'){
  $query = "UPDATE `".$tbl_name."` SET eStatus = '".$status."' WHERE iLocationId = '".$iLocationId."'";
  $obj->sql_query($query);
  }
  else{
  header("Location:location.php?success=2");exit;
  }
  } */
/* get Location */
//Start Sorting
$sortby = isset($_REQUEST['sortby']) ? $_REQUEST['sortby'] : 0;
$order = isset($_REQUEST['order']) ? $_REQUEST['order'] : '';
$ord = ' ORDER BY lm.vLocationName ASC';
if ($sortby == 1) {
    if ($order == 0) $ord = " ORDER BY lm.vLocationName ASC"; else
        $ord = " ORDER BY lm.vLocationName DESC";
}
if ($sortby == 2) {
    if ($order == 0) $ord = " ORDER BY c.vCountry ASC"; else
        $ord = " ORDER BY c.vCountry DESC";
}
if ($sortby == 3) {
    if ($order == 0) $ord = " ORDER BY lm.eFor ASC"; else
        $ord = " ORDER BY lm.eFor DESC";
}
if ($sortby == 4) {
    if ($order == 0) $ord = " ORDER BY lm.eStatus ASC"; else
        $ord = " ORDER BY lm.eStatus DESC";
}
//End Sorting
// Start Search Parameters
$option = isset($_REQUEST['option']) ? stripslashes($_REQUEST['option']) : "";
$keyword = isset($_REQUEST['keyword']) ? stripslashes($_REQUEST['keyword']) : "";
$eFor = isset($_REQUEST['eFor']) ? $_REQUEST['eFor'] : "";
$eStatus = isset($_REQUEST['eStatus']) ? $_REQUEST['eStatus'] : "";
$ssql = $ssql1 = '';
if ($keyword != '') {
    if ($option != '') {
        if ($eStatus != '') {
            $ssql .= " AND " . stripslashes($option) . " LIKE '%" . clean($keyword) . "%' AND lm.eStatus = '" . clean($eStatus) . "'";
        } else {
            $ssql .= " AND " . stripslashes($option) . " LIKE '%" . clean($keyword) . "%' AND lm.eStatus != 'Deleted'";
        }
    } else {
        if ($eStatus != '') {
            $ssql .= " AND (lm.vLocationName LIKE '%" . $keyword . "%' OR lm.eFor LIKE '%" . $keyword . "%'  OR c.vCountry LIKE '%" . $keyword . "%')  AND lm.eStatus = '" . clean($eStatus) . "'";
        } else {
            $ssql .= " AND (lm.vLocationName LIKE '%" . $keyword . "%' OR lm.eFor LIKE '%" . $keyword . "%'  OR c.vCountry LIKE '%" . $keyword . "%') AND lm.eStatus != 'Deleted'";
        }
    }
} else if (!empty($eFor)) {
    if ($eStatus != '') {
        $ssql .= " AND " . stripslashes($option) . " LIKE '" . clean($eFor) . "' AND lm.eStatus = '" . clean($eStatus) . "'";
    } else {
        $ssql .= " AND " . stripslashes($option) . " LIKE '" . clean($eFor) . "' AND lm.eStatus != 'Deleted'";
    }
} else {
    if ($eStatus != '') {
        $ssql .= " AND lm.eStatus = '" . clean($eStatus) . "'";
    } else {
        $ssql .= " AND lm.eStatus != 'Deleted'";
    }
}
// End Search Parameters
//Pagination Start
if ($ENABLE_AIRPORT_SURCHARGE_SECTION == "No") {
    $ssql1 .= " AND (eFor != 'AirportSurcharge')";
}
if (!$MODULES_OBJ->isRideFeatureAvailable()) {
    $ssql1 .= " AND (eFor != 'FixFare')";
}
$ssql1 .= "AND (eFor != 'FlyStation')"; //added by SP for fly stations on 13-08-2019
$per_page = $DISPLAY_RECORD_NUMBER; // number of results to show per page
$sql = "SELECT COUNT(lm.iLocationId) AS Total FROM location_master as lm LEFT JOIN country as c on c.iCountryId=lm.iCountryId WHERE 1=1 $ssql1 $ssql";
$totalData = $obj->MySQLSelect($sql);
$total_results = $totalData[0]['Total'];
$total_pages = ceil($total_results / $per_page); //total pages we going to have
$show_page = 1;
//-------------if page is setcheck------------------//
$start = 0;
$end = $per_page;
if (isset($_GET['page'])) {
    $show_page = $_GET['page'];             //it will telles the current page
    if ($show_page > 0 && $show_page <= $total_pages) {
        $start = ($show_page - 1) * $per_page;
        $end = $start + $per_page;
    }
}
// display pagination
$page = isset($_GET['page']) ? intval($_GET['page']) : 0;
$tpages = $total_pages;
if ($page <= 0) $page = 1;
//Pagination End
$sql = "SELECT lm.*,c.vCountry FROM location_master as lm LEFT JOIN country as c on c.iCountryId=lm.iCountryId WHERE 1=1 $ssql1 $ssql $ord LIMIT $start, $per_page ";
$data_drv = $obj->MySQLSelect($sql);
$endRecord = count($data_drv);
//echo '<pre>--->'; print_r($data_drv);
$var_filter = "";
foreach ($_REQUEST as $key => $val) {
    if ($key != "tpages" && $key != 'page') $var_filter .= "&$key=" . stripslashes($val);
}
$reload = $_SERVER['PHP_SELF'] . "?tpages=" . $tpages . $var_filter;
$cubeDeliverallOnly = $MODULES_OBJ->isOnlyDeliverAllSystem();
$onlyDeliverallModule = strtoupper(ONLYDELIVERALL);
if ($cubeDeliverallOnly > 0) {
    $onlyDeliverallModule = "YES";
}
?>
<!DOCTYPE html>
<html lang="en">
<!-- BEGIN HEAD-->
<head>
    <meta charset="UTF-8"/>
    <title><?= $SITE_NAME ?> | Location</title>
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
                        <h2><?php echo $langage_lbl_admin['LBL_LOCATION_PAGE_ADMIN']; ?></h2>
                        <!--<input type="button" id="" value="ADD A DRIVER" class="add-btn">-->
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
                                            <option value="lm.vLocationName" <?php
                                            if ($option == "lm.vLocationName") {
                                                echo "selected";
                                            }
                                            ?> >Location
					    </option>
                                            <option value="c.vCountry" <?php
                                            if ($option == "c.vCountry") {
                                                echo "selected";
                                            }
                                            ?> >Country
					    </option>
                                            <option value="lm.eFor" <?php
                                            if ($option == "lm.eFor") {
                                                echo "selected";
                                            }
                                            ?> >Location For
					    </option>
                                          <!--<option value="lm.eStatus" <?php
                                            if ($option == 'lm.eStatus') {
                                                echo "selected";
                                            }
                                            ?> >Status</option>-->
                                        </select>
                                    </td>
                        <td width="15%" class="searchform">
					<input type="Text" id="keyword" name="keyword" value="<?php echo $keyword; ?>"  
					class="form-control" />
					</td>
					<td width="12%" class="estatus_eFor" id="estatus_eFor" >
                                        <select class="form-control" name ="eFor" id="eFor" required="required">
                                            <option value="">Select Location For</option>
					    <option value ="Restrict" 
					    <? if ($eFor == 'Restrict') { ?>selected<? } ?>><?php echo $langage_lbl_admin['LBL_LOCATION_AREA_RESTRICTION']; ?></option>
					     <option value ="VehicleType" 
					     <? if ($eFor == 'VehicleType') { ?>selected<? } ?> ><?php echo $langage_lbl_admin['LBL_VEHICLE_SERVICE_TYPE_TXT']; ?></option>
                                            <?php if ($APP_TYPE != 'UberX' && $APP_TYPE != 'Delivery' && $MODULES_OBJ->isRideFeatureAvailable()) { ?>
                                                <?php if ($onlyDeliverallModule == 'NO') { ?>
						 <option value ="FixFare" 
						 <? if ($eFor == 'FixFare') { ?>selected<? } ?> ><?php echo $langage_lbl_admin['LBL_FIXFARE_SMALL_TXT']; ?></option>
                                                <? } ?>
                                            <? } ?>
                                            <?php if ($MODULES_OBJ->isDeliverAllFeatureAvailable() ||  $onlyDeliverallModule == "YES") { ?>  
                                    <option value="UserDeliveryCharge"
                                            <? if ($eFor == 'UserDeliveryCharge') { ?>selected<? } ?> ><?php echo $langage_lbl_admin['LBL_LOCATION_USER_DELIVERY_CHARGE']; ?></option>
                                <?php } ?>

                                <?php if ($ENABLE_AIRPORT_SURCHARGE_SECTION == "Yes" && $APP_TYPE != 'UberX' && $APP_TYPE != 'Delivery' && $MODULES_OBJ->isRideFeatureAvailable()) { ?>
                                    <?php if ($onlyDeliverallModule == 'NO') { ?>
                                        <option value="AirportSurcharge"
                                                <? if ($eFor == 'AirportSurcharge') { ?>selected<? } ?> ><?php echo 'Airport Surcharge'; ?></option>
                                    <? } ?>
                                <? }
                                if ($MODULES_OBJ->isEnableLocationwiseBanner()) { ?>
                                    <option value="Banner"
                                            <? if ($eFor == 'Banner') { ?>selected<? } ?> ><?php echo 'Banner'; ?></option>
                                <? }
                                if ($MODULES_OBJ->isEnableLocationWisePromoCode()) { ?>
                                    <option value="PromoCode" <?php if ($eFor == 'PromoCode') { ?>selected<?php } ?>>
                                        PromoCode
                                    </option>
                                <?php } ?>
                            </select>
                        </td>
                        <td width="12%" class="estatus_options" id="eStatus_options">
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
                                   onClick="window.location.href = 'location.php'"/>
                        </td>
                        <?php if ($userObj->hasPermission('create-geo-fence-locations')) { ?>
                            <td width="20%">
                                <a class="add-btn" href="location_action.php" style="text-align: center;">Add Location
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
                                            <?php if ($userObj->hasPermission('update-status-geo-fence-locations')) { ?>
                                                <select name="changeStatus" id="changeStatus" class="form-control"
                                                        onchange="ChangeStatusAll(this.value);">
                                                    <option value="">Select Action</option>
                                                    <option value='Active' <?php
                                                    if ($option == 'Active') {
                                                        echo "selected";
                                                    }
                                                    ?> >Activate</option>
                                                    <!-- <option value="Inactive" <?php
                                                    if ($option == 'Inactive') {
                                                        echo "selected";
                                                    }
                                                    ?> >Deactivate</option>
                                                    <option value="Deleted" <?php
                                                    if ($option == 'Delete') {
                                                        echo "selected";
                                                    }
                                                    ?> >Delete</option> -->
                                                </select>
                                            <?php } ?>
                                        </span>
                                    </div>
                                    <!--                                     <div class="panel-heading">
                                                                            <form name="_export_form" id="_export_form" method="post" >
                                                                                <button type="button" onclick="showExportTypes('location')" >Export</button>
                                                                            </form>
                                                                       </div> -->
                                </div>
                                <div style="clear:both;"></div>
                                <div class="table-responsive">
                                    <form class="_list_form" id="_list_form" method="post" 
				    action="<?php echo $_SERVER['PHP_SELF'] ?>">
                                        <table class="table table-striped table-bordered table-hover">
                                            <thead>
                                                <tr>
                                        <?php if ($userObj->hasPermission('update-status-geo-fence-locations')) { ?>
                                                    <th align="center" width="3%" style="text-align:center;">
						    <input type="checkbox" id="setAllCheck" >
						    </th>
                                        <?php } ?>
                                        <th>
					<a href="javascript:void(0);" onClick="Redirect(1,<?php
                                                        if ($sortby == '1') {
                                                            echo $order;
                                                        } else {
                                                            ?>0<?php } ?>)">Location Name<?php
                                                               if ($sortby == 1) {
                                                                   if ($order == 0) {
								    ?>
								    <i class="fa fa-sort-amount-asc" 
								    aria-hidden="true"></i> <?php } else { ?>
								    <i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php
                                                                }
                                                            } else {
							      ?>
							      <i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a>
							</th> 
							<th align="center">
							<a href="javascript:void(0);" onClick="Redirect(2,<?php
                                                        if ($sortby == '2') {
                                                            echo $order;
                                                        } else {
                                                            ?>0<?php } ?>)">Country <?php
                                                            if ($sortby == 2) {
                                                                if ($order == 0) {
								 ?>
								 <i class="fa fa-sort-amount-asc" 
								 aria-hidden="true"></i> <?php } else { ?>
								 <i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php
                                                                }
                                                            } else {
							      ?>
							      <i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a>
								</th>
								 <th  align="center">
								 <a href="javascript:void(0);" onClick="Redirect(3,<?php
                                                                              if ($sortby == '3') {
                                                                                  echo $order;
                                                                              } else {
                                                                                  ?>0<?php } ?>)">Location For <?php
                                                            if ($sortby == 3) {
                                                                if ($order == 0) {
								 ?>
								 <i class="fa fa-sort-amount-asc" 
								 aria-hidden="true"></i> <?php } else { ?>
								 <i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php
                                                            }
                                                        } else {
							  ?>
							  <i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a>
							</th>
							 <th  align="center" style="text-align:center;">
							 <a href="javascript:void(0);" onClick="Redirect(4,<?php
                                                            if ($sortby == '4') {
                                                                echo $order;
                                                            } else {
                                                                ?>0<?php } ?>)">Status <?php
                                                            if ($sortby == 4) {
                                                                if ($order == 0) {
								 ?>
								 <i class="fa fa-sort-amount-asc" 
								 aria-hidden="true"></i> <?php } else { ?>
								 <i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php
                                                    }
                                                } else {
						 ?>
						 <i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a>
						 </th>
                                        <?php if ($userObj->hasPermission(['edit-geo-fence-locations', 'update-status-geo-fence-locations', 'delete-geo-fence-locations'])) { ?>
                                        <th align="center" style="text-align:center;">Action</th>
                                        <?php } ?>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                if (!empty($data_drv)) {
                                                    for ($i = 0; $i < count($data_drv); $i++) { ?>
                                                        <tr class="gradeA">
                                                <?php if ($userObj->hasPermission('update-status-geo-fence-locations')) { ?>
                                                <td align="center" style="text-align:center;">
                                                    <input type="checkbox" id="checkbox" name="checkbox[]" value="<?php echo $data_drv[$i]['iLocationId']; ?>"/>&nbsp;
                                                </td>
                                                <?php } ?>
                                                            <td><?= ucfirst($data_drv[$i]['vLocationName']); ?></td>
                                                <td><?= $data_drv[$i]['vCountry']; ?></td>
                                                            <td><?php
                                                                if ($data_drv[$i]['eFor'] == 'VehicleType') {
                                                                    echo $langage_lbl_admin['LBL_VEHICLE_SERVICE_TYPE_TXT'];
                                                                } elseif ($data_drv[$i]['eFor'] == 'Restrict') {
                                                                    echo $langage_lbl_admin['LBL_LOCATION_AREA_RESTRICTION'];
                                                                } elseif ($data_drv[$i]['eFor'] == 'FixFare') {
                                                                    echo $langage_lbl_admin['LBL_LOCATIONWISE_FARE'];
                                                                } elseif ($data_drv[$i]['eFor'] == 'UserDeliveryCharge') {
                                                                    echo $langage_lbl_admin['LBL_LOCATION_USER_DELIVERY_CHARGE'];
                                                                } elseif ($data_drv[$i]['eFor'] == 'AirportSurcharge') {
                                                                    echo 'Airport Surcharge';
                                                                } else {
                                                                    echo $data_drv[$i]['eFor'];
                                                                }
                                                                ?></td>
                                                            <td align="center" style="text-align:center;">
                                                                <?php
                                                                if ($data_drv[$i]['eStatus'] == 'Active') {
                                                                    $dis_img = "img/active-icon.png";
                                                                } else if ($data_drv[$i]['eStatus'] == 'Inactive') {
                                                                    $dis_img = "img/inactive-icon.png";
                                                                } else if ($data_drv[$i]['eStatus'] == 'Deleted') {
                                                                    $dis_img = "img/delete-icon.png";
                                                                }
                                                                ?>
                                                    <img src="<?= $dis_img; ?>" alt="<?= $data_drv[$i]['eStatus']; ?>"
                                                         data-toggle="tooltip" title="<?= $data_drv[$i]['eStatus']; ?>">
                                                            </td>
                                                <?php if ($userObj->hasPermission(['edit-geo-fence-locations', 'update-status-geo-fence-locations', 'delete-geo-fence-locations'])) { ?>
                                                            <td align="center" style="text-align:center;" class="action-btn001">
                                                        <div class="share-button openHoverAction-class"
                                                             style="display: block;">
                                                            <label class="entypo-export">
                                                                <span><img src="images/settings-icon.png" alt=""></span>
                                                            </label>
                                                                    <div class="social show-moreOptions openPops_<?= $data_drv[$i]['iLocationId']; ?>">
                                                                        <ul>
                                                                    <?php if ($userObj->hasPermission('edit-geo-fence-locations')) { ?>
                                                                        <li class="entypo-twitter"
                                                                            data-network="twitter">
                                                                            <a href="location_action.php?id=<?= $data_drv[$i]['iLocationId']; ?>"
                                                                               data-toggle="tooltip" title="Edit">
                                                                                    <img src="img/edit-icon.png" alt="Edit">
                                                                            </a>
                                                                        </li>
                                                                    <?php } ?>
                                                                            <?php if ($userObj->hasPermission('update-status-geo-fence-locations')) { ?>
                                                                            <li class="entypo-facebook"
                                                                                data-network="facebook">
                                                                                <a href="javascript:void(0);"
                                                                                   onclick="changeStatus('<?php echo $data_drv[$i]['iLocationId']; ?>', 'Inactive')"
                                                                                   data-toggle="tooltip"
                                                                                   title="Activate">
                                                                                    <img src="img/active-icon.png"
                                                                                         alt="<?php echo $data_drv[$i]['eStatus']; ?>">
                                                                                </a>
                                                                            </li>
                                                                            <li class="entypo-gplus"
                                                                                data-network="gplus">
                                                                                <a href="javascript:void(0);"
                                                                                   onclick="changeStatus('<?php echo $data_drv[$i]['iLocationId']; ?>', 'Active')"
                                                                                   data-toggle="tooltip"
                                                                                   title="Deactivate">
                                                                                    <img src="img/inactive-icon.png"
                                                                                         alt="<?php echo $data_drv[$i]['eStatus']; ?>">
                                                                                </a>
                                                                            </li>
            <?php } ?>
            <?php if ($userObj->hasPermission('delete-geo-fence-locations')) { ?>
                                                                            <li class="entypo-gplus"
                                                                                data-network="gplus">
                                                                                <a href="javascript:void(0);"
                                                                                   onclick="changeStatusDelete('<?php echo $data_drv[$i]['iLocationId']; ?>')"
                                                                                   data-toggle="tooltip" title="Delete">
                                                                                    <img src="img/delete-icon.png"
                                                                                         alt="Delete">
                                                                                </a>
                                                                            </li>
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
<?php include('pagination_n.php'); ?>
                        </div>
                    </div> <!--TABLE-END-->
                </div>
            </div>
        </div>
    </div>
    <!--END PAGE CONTENT -->
</div>
<!--END MAIN WRAPPER -->
<form name="pageForm" id="pageForm" action="action/location.php" method="post">
    <input type="hidden" name="page" id="page" value="<?php echo $page; ?>">
    <input type="hidden" name="tpages" id="tpages" value="<?php echo $tpages; ?>">
    <input type="hidden" name="iLocationId" id="iMainId01" value="">
    <input type="hidden" name="eFor" id="eFor" value="<?php echo $eFor ?>">
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
        $('#estatus_eFor').hide();
        $('#option').each(function () {
            if (this.value == 'lm.eFor') {
                $('#estatus_eFor').show();
                $('.searchform').hide();
            }
        });
    });
    $(function () {
        $('#option').change(function () {
            if ($('#option').val() == 'lm.eFor') {
                $('#estatus_eFor').show();
                $("input[name=keyword]").val("");
                $('.searchform').hide();
            } else {
                $('#estatus_eFor').hide();
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
