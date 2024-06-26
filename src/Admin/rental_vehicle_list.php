<?php
include_once('../common.php');


if (!$userObj->hasPermission('view-rental-packages')) {
    $userObj->redirect();
}
if ($default_lang == "") {
    $default_lang = "EN";
}

$sql_vehicle_category_table_name = getVehicleCategoryTblName();

$script = 'Rental Package';
//Start Sorting
$sortby = isset($_REQUEST['sortby']) ? $_REQUEST['sortby'] : 0;
$order = isset($_REQUEST['order']) ? $_REQUEST['order'] : '';
$iVehicleCategoryId = isset($_REQUEST['iVehicleCategoryId']) ? $_REQUEST['iVehicleCategoryId'] : "";
$ord = ' ORDER BY vt.vVehicleType_' . $default_lang . ' ASC';
if ($sortby == 1) {
    if ($order == 0)
        $ord = " ORDER BY vt.vVehicleType_" . $default_lang . " ASC";
    else
        $ord = " ORDER BY vt.vVehicleType_" . $default_lang . " DESC";
}
if ($sortby == 2) {
    if ($order == 0)
        $ord = " ORDER BY vt.fPricePerKM ASC";
    else
        $ord = " ORDER BY vt.fPricePerKM DESC";
}
if ($sortby == 3) {
    if ($order == 0)
        $ord = " ORDER BY vt.fPricePerMin ASC";
    else
        $ord = " ORDER BY vt.fPricePerMin DESC";
}
// Start Search Parameters
$option = isset($_REQUEST['option']) ? stripslashes($_REQUEST['option']) : "";
$keyword = isset($_REQUEST['keyword']) ? stripslashes($_REQUEST['keyword']) : "";
$eType = isset($_REQUEST['eType']) ? stripslashes($_REQUEST['eType']) : "";
$searchDate = isset($_REQUEST['searchDate']) ? $_REQUEST['searchDate'] : "";
$ssql = '';
if ($keyword != '') {
    if ($option != '') {
        if ($iVehicleCategoryId != '') {
            $ssql .= " AND " . stripslashes($option) . " LIKE '%" . stripslashes($keyword) . "%' AND vt.iVehicleCategoryId = '" . $iVehicleCategoryId . "'";
        } else {
            $ssql .= " AND " . stripslashes($option) . " LIKE '%" . stripslashes($keyword) . "%'";
        }
    } else {
        if ($iVehicleCategoryId != '') {
            $ssql .= " AND (vt.vVehicleType_" . $default_lang . " LIKE '%" . $keyword . "%' OR vt.fPricePerKM LIKE '%" . $keyword . "%' OR vt.fPricePerMin LIKE '%" . $keyword . "%' OR vt.iPersonSize	 LIKE '%" . $keyword . "%') AND vt.iVehicleCategoryId = '" . $iVehicleCategoryId . "'";
        } else {
            $ssql .= " AND (vt.vVehicleType_" . $default_lang . " LIKE '%" . $keyword . "%' OR vt.fPricePerKM LIKE '%" . $keyword . "%' OR vt.fPricePerMin LIKE '%" . $keyword . "%' OR vt.iPersonSize   LIKE '%" . $keyword . "%')";
        }
    }
} else if ($iVehicleCategoryId != '' && $keyword == '') {
    $ssql .= " AND vt.iVehicleCategoryId = '" . $iVehicleCategoryId . "'";
} else if ($eType != '' && $keyword == '') {
    $ssql .= " AND vt.eType = '" . $eType . "'";
}
$locations_where = "";
if (count($userObj->locations) > 0) {
    $locations = implode(', ', $userObj->locations);
    $locations_where = " AND vt.iLocationid IN(-1, {$locations}) ";
    $ssql .= $locations_where;
}
// End Search Parameters

$ssql .= " AND vt.eFly = '0'  AND vt.eIconType != 'Ambulance'";

//$Vehicle_type_name = ($APP_TYPE == 'Delivery')? 'Deliver':$APP_TYPE ; 
if ($APP_TYPE == 'Delivery') {
    $Vehicle_type_name = 'Deliver';
} else if ($APP_TYPE == 'Ride-Delivery-UberX') {
    $Vehicle_type_name = 'Ride-Delivery';
} else {
    $Vehicle_type_name = $APP_TYPE;
}
//Pagination Start
$per_page = $DISPLAY_RECORD_NUMBER; // number of results to show per page
$sql = "";
if ($Vehicle_type_name == "Ride-Delivery") {
    /*  if(empty($eType)){
      $ssql .= "AND (vt.eType ='Ride' or vt.eType ='Deliver')";
      } */
    $sql = "SELECT count(iVehicleTypeId) AS Total from  vehicle_type  as vt where 1 = 1 AND vt.eType ='Ride' AND vt.ePoolStatus =  'No' AND vt.estatus ='Active' $ssql";
} else {
    if ($APP_TYPE == 'UberX') {
        $sql = "SELECT count(vt.iVehicleTypeId) as Total,vc.iVehicleCategoryId,vc.vCategory_" . $default_lang . " from  vehicle_type as vt left join " . $sql_vehicle_category_table_name . " as vc on vt.iVehicleCategoryId = vc.iVehicleCategoryId where vt.eType='" . $Vehicle_type_name . "' AND vt.ePoolStatus =  'No' AND vt.estatus ='Active' $ssql";
    } else {
        $sql = "SELECT count(vt.iVehicleTypeId) as Total  from  vehicle_type as vt where vt.eType='" . $Vehicle_type_name . "' AND vt.ePoolStatus =  'No' AND vt.estatus ='Active' $ssql";
    }
}
//echo $sql; die;
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
if ($page <= 0)
    $page = 1;
//Pagination End

$sql = "";

if ($Vehicle_type_name == "Ride-Delivery") {
    /*    if(empty($eType)){
      $ssql .= "AND (vt.eType ='Ride' or vt.eType ='Deliver')";
      } */
    $sql = "SELECT vt.*,lm.vLocationName from  vehicle_type as vt left join location_master as lm ON lm.iLocationId = vt.iLocationid where 1= 1  AND vt.eType='Ride' AND vt.ePoolStatus='No' AND vt.estatus ='Active' $ssql $adm_ssql $ord LIMIT $start, $per_page";
} else {
    if ($APP_TYPE == 'UberX') {
        $sql = "SELECT vt.*,vc.iVehicleCategoryId,vc.vCategory_" . $default_lang . ",lm.vLocationName
		from  vehicle_type as vt  
		left join " . $sql_vehicle_category_table_name . " as vc on vt.iVehicleCategoryId = vc.iVehicleCategoryId 
		left join country as c ON c.iCountryId = vt.iCountryId
		left join state as st ON st.iStateId = vt.iStateId
		left join city as ct ON ct.iCityId = vt.iCityId
    left join location_master as lm ON lm.iLocationId = vt.iLocationid 
		where vt.eType='" . $Vehicle_type_name . "' AND vt.estatus ='Active' AND vt.ePoolStatus='No' $ssql $adm_ssql $ord LIMIT $start, $per_page";
    } else if ($APP_TYPE == 'Ride-Delivery-UberX') {
        $sql = "SELECT vt.*,c.vCountry,ct.vCity,st.vState,lm.vLocationName
		from vehicle_type as vt left join country as c ON c.iCountryId = vt.iCountryId 
		left join state as st ON st.iStateId = vt.iStateId 
		left join city as ct ON ct.iCityId = vt.iCityId 
    left join location_master as lm ON lm.iLocationId = vt.iLocationid 
		where 1=1 AND vt.estatus ='Active' $ssql $adm_ssql $ord LIMIT $start, $per_page";
    } else {
        $sql = "SELECT vt.*,c.vCountry,ct.vCity,st.vState,lm.vLocationName
		from vehicle_type as vt left join country as c ON c.iCountryId = vt.iCountryId 
		left join state as st ON st.iStateId = vt.iStateId 
		left join city as ct ON ct.iCityId = vt.iCityId 
    left join location_master as lm ON lm.iLocationId = vt.iLocationid 
		where eType='" . $Vehicle_type_name . "' AND vt.ePoolStatus='No' AND vt.estatus ='Active' $ssql $adm_ssql $ord LIMIT $start, $per_page";
    }
}

$data_drv = $obj->MySQLSelect($sql);
$endRecord = count($data_drv);
$var_filter = "";
foreach ($_REQUEST as $key => $val) {
    if ($key != "tpages" && $key != 'page')
        $var_filter .= "&$key=" . stripslashes($val);
}
$reload = $_SERVER['PHP_SELF'] . "?tpages=" . $tpages . $var_filter;
if ($APP_TYPE == 'UberX') {
    $sql_cat = "select *  from " . $sql_vehicle_category_table_name . " where iParentId='0'";
    $db_data_cat = $obj->MySQLSelect($sql_cat);
}
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->
    <!-- BEGIN HEAD-->
    <head>
        <meta charset="UTF-8" />
        <title><?= $SITE_NAME; ?> |<?= $langage_lbl_admin['LBL_VEHICLE_TYPE_RENTAL_TXT']; ?></title>
        <meta content="width=device-width, initial-scale=1.0" name="viewport" />
        <link href="../assets/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet" />
        <? include_once('global_files.php'); ?>
    </head>
    <!-- END  HEAD-->
    <!-- BEGIN BODY-->
    <body class="padTop53 " >
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
                                <h2>Manage <?= $langage_lbl_admin['LBL_VEHICLE_TYPE_RENTAL_TXT']; ?></h2>
                            </div>
                        </div>
                        <hr />
                    </div>
                    <?php include('valid_msg.php'); ?>
                    <form name="frmsearch" id="frmsearch" action="javascript:void(0);" method="post">
                        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="admin-nir-table">
                            <tbody>
                                <tr>
                                    <td width="5%"><label for="textfield"><strong>Search:</strong></label></td>
                                    <!-- 										            <?php if ($APP_TYPE != 'UberX') { ?>                                                                                                                                                   <td width="10%" class=" padding-right10">                                                                                                                                                     <select name="option" id="option" class="form-control">
                                                                                          <option value="">All</option>
                                                                                          <option value="vt.vVehicleType_<?= $default_lang ?>" <?php
                                        if ($option == "vt.vVehicleType_" . $default_lang) {
                                            echo "selected";
                                        }
                                        ?> >Type</option>
                                                                                    </select>
                                                                                    </td>
                                    <?php } else { ?>
                                                                                    <input type="hidden" name="option" id="option" value="vVehicleType_<?= $default_lang ?>">
                                    <?php } ?> -->
                                    <td width="15%" class="searchform"><input type="Text" id="keyword" name="keyword" value="<?= $keyword; ?>"  class="form-control" /></td>
                                    <?php if ($Vehicle_type_name == 'Ride-Delivery') { ?>
                                        <td width="16%" class="eType_options" id="eType_options" >
                                            <select name="eType" id="eType_value" class="form-control">
                                                <option value=''>Select Vehicle Type</option>
                                                <option value='Ride' <?php
                                                if ($eType == 'Ride') {
                                                    echo "selected";
                                                }
                                                ?> >Ride</option>
                                                <option value="Deliver" <?php
                                                if ($eType == 'Deliver') {
                                                    echo "selected";
                                                }
                                                ?> >Deliver</option>
                                            </select>
                                        </td>
                                    <?php } ?>

                                    <td>
                                        <input type="submit" value="Search" class="btnalt button11" id="Search" name="Search" title="Search" />
                                        <input type="button" value="Reset" class="btnalt button11" onClick="window.location.href = 'rental_vehicle_list.php'"/>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                    </form>
                    <div class="table-list">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="admin-nir-export">
                                    <!-- <div class="changeStatus col-lg-12 option-box-left">
                                    <span class="col-lg-2 new-select001">
                                    <?php if ($userObj->hasPermission('delete-rental-packages')) { ?>
                                                        <select name="changeStatus" id="changeStatus" class="form-control" onChange="ChangeStatusAll(this.value);">
                                                                <option value="" >Select Action</option>
                                                                <option value="Deleted" <?php
                                        if ($option == 'Delete') {
                                            echo "selected";
                                        }
                                        ?> >Make Delete</option>
                                                        </select>
                                    <?php } ?>
                                    </span>
                                    </div> -->
                                    <?php if (!empty($data_drv)) { ?>
                                        <!--<div class="panel-heading">
                                            <form name="_export_form" id="_export_form" method="post" >
                                                <button type="button" onclick="showExportTypes('vehicle_rental_package')" >Export</button>
                                            </form>
                                       </div>-->
                                    <?php } ?>
                                </div>
                                <div style="clear:both;"></div>
                                <div class="table-responsive">
                                    <form class="_list_form" id="_list_form" method="post" action="<?= $_SERVER['PHP_SELF'] ?>">
                                        <table class="table table-striped table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                   <!--  <th align="center" width="3%" style="text-align:center;"><input type="checkbox" id="setAllCheck" ></th> -->

                                                    <th width="15%" align="center" style="text-align:center;"><a href="javascript:void(0);" onClick="Redirect(1,<?php
                                                        if ($sortby == '1') {
                                                            echo $order;
                                                        } else {
                            ?>0<?php } ?>)">Vehicle Type<?php   if ($sortby == 1) {
                                                                                         if ($order == 0) {
                                                                                             ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php
                                                                }
                                                            } else {
                                                                ?>  <i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a></th>
                                                    <th width="10%" style="text-align:center;">Rental Packages</th>                                                                                                                                      <!--   <th width="8%" style="text-align:center;">Action</th> -->
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                if (!empty($data_drv)) {
                                                    //Added By HJ ON 21-09-2020 For Optimize For Loop Query 
                                                    $rentalDataArr = array();
                                                    $getRentalData = $obj->MySQLSelect("SELECT count(iRentalPackageId) AS TotalPackage,iVehicleTypeId from  rental_package where 1=1 GROUP BY iVehicleTypeId");
                                                    //echo "<pre>";print_r($getRentalData);die;
                                                    for($m=0;$m<count($getRentalData);$m++){
                                                        $rentalDataArr[$getRentalData[$m]['iVehicleTypeId']] = $getRentalData[$m]['TotalPackage'];
                                                    }
                                                    for ($i = 0; $i < count($data_drv); $i++) {
                                                        //$rental_package = $obj->MySQLSelect("SELECT count(iRentalPackageId) AS TotalPackage from  rental_package where iVehicleTypeId = '" . $data_drv[$i]['iVehicleTypeId'] . "'");
                                                        //$total_rental_package = $rental_package[0]['TotalPackage'];
                                                        $total_rental_package = 0;
                                                        if(isset($rentalDataArr[$data_drv[$i]['iVehicleTypeId']])){
                                                            $total_rental_package = $rentalDataArr[$data_drv[$i]['iVehicleTypeId']];
                                                        }
                                                        ?>
                                                        <tr class="gradeA">                                                                                                                                                        <!--  <td style="text-align:center;"><input type="checkbox" id="checkbox" name="checkbox[]" <?= $default; ?> value="<?= $data_drv[$i]['iVehicleTypeId']; ?>" />&nbsp;</td> -->
                                                            <td style="text-align:center;"><?= $data_drv[$i]['vVehicleType_' . $default_lang] ?></td>
                                                            <td style="text-align:center;">
                                                                <?php if ($userObj->hasPermission(['view-rental-packages', 'create-rental-packages'])) { ?>
                                                                    <a href="rental_package.php?id=<?= $data_drv[$i]['iVehicleTypeId']; ?>" class="add-btn-sub">Add/View (<?= $total_rental_package; ?>) </a> 
        <?php } ?>
                                                            </td>
                                                                    <!-- <td style="text-align:center;" class="action-btn001">
                                                                    <div class="share-button openHoverAction-class" style="display: block;">
                                                            <label class="entypo-export"><span><img src="images/settings-icon.png" alt=""></span></label>
                                                            <div class="social show-moreOptions for-two openPops_<?= $data_drv[$i]['iVehicleTypeId']; ?>">
                                                            <ul>
                                                            <li class="entypo-twitter" data-network="twitter"><a href="vehicle_type_action.php?id=<?= $data_drv[$i]['iVehicleTypeId']; ?>" data-toggle="tooltip" title="Edit">
                                                            <img src="img/edit-icon.png" alt="Edit">
                                                            </a></li>
                                                            <li class="entypo-gplus" data-network="gplus"><a href="javascript:void(0);" onClick="changeStatusDelete('<?= $data_drv[$i]['iVehicleTypeId']; ?>')"data-toggle="tooltip" title="Delete">
                                                            <img src="img/delete-icon.png" alt="Delete" >
                                                            </a></li>
                                                            </ul>
                                                            </div>
                                                            </div>

                                                                    </td> -->
                                                        </tr>    
                                                    <?
                                                    }
                                                } else {
                                                    ?>
                                                    <tr class="gradeA">
                                                        <td colspan="4"> No Records Found.</td>
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
                                Rental <?= $langage_lbl_admin['LBL_Vehicle']; ?> Type  module will list all Rental <?= $langage_lbl_admin['LBL_Vehicle']; ?> Types on this page.
                            </li>
                            <!-- <li>
                              Administrator can Edit / Delete any Rental <?= $langage_lbl_admin['LBL_Vehicle']; ?> type. 
                            </li> -->
                        </ul>
                    </div>
                </div>
            </div>
            <!--END PAGE CONTENT -->
        </div>
        <!--END MAIN WRAPPER -->

        <form name="pageForm" id="pageForm" action="action/vehicle_type.php" method="post" >
            <input type="hidden" name="page" id="page" value="<?= $page; ?>">
            <input type="hidden" name="tpages" id="tpages" value="<?= $tpages; ?>">
            <input type="hidden" name="iVehicleTypeId" id="iMainId01" value="" >
            <input type="hidden" name="status" id="status01" value="" >
            <input type="hidden" name="eType" id="eType" value="<?= $eType; ?>" >
            <input type="hidden" name="statusVal" id="statusVal" value="" >
            <input type="hidden" name="option" value="<?= $option; ?>" >
            <input type="hidden" name="keyword" value="<?= $keyword; ?>" >
            <input type="hidden" name="sortby" id="sortby" value="<?= $sortby; ?>" >
            <input type="hidden" name="order" id="order" value="<?=$order; ?>" >
            <input type="hidden" name="method" id="method" value="" >
        <?php if ($APP_TYPE == 'UberX') { ?>
                <input type="hidden" name="iVehicleCategoryId" id="iVehicleCategoryId" value="<?= $iVehicleCategoryId; ?>" >
        <?php } ?>
        </form>
<?php include_once('footer.php'); ?>
        <script>
            $(document).ready(function () {
                $('#eType_options').hide();
                $('#option').each(function () {
                    if (this.value == 'vt.eType') {
                        $('#eType_options').show();
                        $('.searchform').hide();
                    }
                });
            });
            $(function () {
                $('#option').change(function () {
                    if ($('#option').val() == 'vt.eType') {
                        $('#eType_options').show();
                        $("input[name=keyword]").val("");
                        $('.searchform').hide();
                    } else {
                        $('#eType_options').hide();
                        $("#eType_value").val("");
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