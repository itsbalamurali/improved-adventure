<?php
include_once '../common.php';
$tbl_name = 'trips';
if (!$userObj->hasPermission('manage-store-payment')) {
    $userObj->redirect();
}
$script = 'Restaurant Payment Report';
$eSystem = " AND eSystem = 'DeliverAll'";
$action = $_REQUEST['action'] ?? '';
$searchCompany = $_REQUEST['searchCompany'] ?? '';
$searchServiceType = $_REQUEST['searchServiceType'] ?? '';
$startDate = $_REQUEST['startDate'] ?? '';
$endDate = $_REQUEST['endDate'] ?? '';
// Start Sorting
$sortby = $_REQUEST['sortby'] ?? 0;
$order = $_REQUEST['order'] ?? '';
$ord = ' ORDER BY c.iCompanyId DESC';
if (2 === $sortby) {
    if (0 === $order) {
        $ord = ' ORDER BY c.vCompany ASC';
    } else {
        $ord = ' ORDER BY c.vCompany DESC';
    }
}
if (3 === $sortby) {
    if (0 === $order) {
        $ord = ' ORDER BY c.vAcctHolderName ASC';
    } else {
        $ord = ' ORDER BY c.vAcctHolderName DESC';
    }
}
if (4 === $sortby) {
    if (0 === $order) {
        $ord = ' ORDER BY c.vBankName ASC';
    } else {
        $ord = ' ORDER BY c.vBankName DESC';
    }
}
// End Sorting
// Start Search Parameters
$ssql = $ssql1 = $ssql2 = '';
if ('search' === $action) {
    if ('' !== $startDate) {
        $ssql .= " AND Date(o.tOrderRequestDate) >='".$startDate."'";
        $ssql2 .= " AND Date(o.tOrderRequestDate) >='".$startDate."'";
    }
    if ('' !== $endDate) {
        $ssql .= " AND Date(o.tOrderRequestDate) <='".$endDate."'";
        $ssql2 .= " AND Date(o.tOrderRequestDate) <='".$endDate."'";
    }
    if ('' !== $searchCompany) {
        $ssql1 .= " AND c.iCompanyId ='".$searchCompany."'";
    }
    if ('' !== $searchServiceType) {
        $ssql .= " AND sc.iServiceId ='".$searchServiceType."'";
        $ssql2 .= " AND o.iServiceId ='".$searchServiceType."'";
    }
}
// Select dates
$Today = date('Y-m-d');
$tdate = date('d') - 1;
$mdate = date('d');
$Yesterday = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') - 1, date('Y')));
$curryearFDate = date('Y-m-d', mktime(0, 0, 0, '1', '1', date('Y')));
$curryearTDate = date('Y-m-d', mktime(0, 0, 0, '12', '31', date('Y')));
$prevyearFDate = date('Y-m-d', mktime(0, 0, 0, '1', '1', date('Y') - 1));
$prevyearTDate = date('Y-m-d', mktime(0, 0, 0, '12', '31', date('Y') - 1));
$currmonthFDate = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') - $tdate, date('Y')));
$currmonthTDate = date('Y-m-d', mktime(0, 0, 0, date('m') + 1, date('d') - $mdate, date('Y')));
$prevmonthFDate = date('Y-m-d', mktime(0, 0, 0, date('m') - 1, date('d') - $tdate, date('Y')));
$prevmonthTDate = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') - $mdate, date('Y')));

$monday = date('Y-m-d', strtotime('monday this week'));
$sunday = date('Y-m-d', strtotime('sunday this week'));
$Pmonday = date('Y-m-d', strtotime('monday this week -1 week'));
$Psunday = date('Y-m-d', strtotime('sunday this week -1 week'));
$ssql .= ' AND sc.iServiceId IN('.$enablesevicescategory.')';
$ssql2 .= ' AND o.iServiceId IN('.$enablesevicescategory.')';
$per_page = $DISPLAY_RECORD_NUMBER;
$sql = 'SELECT c.iCompanyId,o.eRestaurantPaymentStatus,sc.vServiceName_'.$default_lang." as vServiceName,c.vCompany,c.vPaymentEmail,c.vAcctHolderName,c.vAcctNo,c.vBankName,c.vBankLocation,c.vSwiftCode,CONCAT(c.vCode,' ',c.vPhone) as resturant_phone FROM company as c LEFT JOIN orders as o on o.iCompanyId= c.iCompanyId LEFT JOIN service_categories as sc on sc.iServiceid = o.iServiceid WHERE o.eRestaurantPaymentStatus='Unsettled' AND o.eBuyAnyService = 'No' {$ssql} {$ssql1} GROUP BY c.iCompanyId";
$totalData = $obj->MySQLSelect($sql);
$total_results = count($totalData);
// $total_results = $totalData[0]['Total'];
$total_pages = ceil($total_results / $per_page); // total pages we going to have
$show_page = 1;
// -------------if page is setcheck------------------//
$start = 0;
$end = $per_page;
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
$sql = 'SELECT c.iCompanyId,o.eRestaurantPaymentStatus,sc.vServiceName_'.$default_lang." as vServiceName,c.vCompany,c.vPaymentEmail,c.vAcctHolderName,c.vAcctNo,c.vBankName,c.vBankLocation,c.vSwiftCode,CONCAT(c.vCode,' ',c.vPhone) as resturant_phone FROM company as c LEFT JOIN orders as o on o.iCompanyId= c.iCompanyId LEFT JOIN service_categories as sc on sc.iServiceid = o.iServiceid WHERE o.eRestaurantPaymentStatus='Unsettled' AND o.eBuyAnyService = 'No' {$ssql} {$ssql1} GROUP BY c.iCompanyId {$ord} LIMIT {$start}, {$per_page} ";
$db_payment = $obj->MySQLSelect($sql);
$endRecord = count($db_payment);
$var_filter = '';
foreach ($_REQUEST as $key => $val) {
    if ('tpages' !== $key && 'page' !== $key) {
        $var_filter .= "&{$key}=".stripslashes($val);
    }
}
$reload = $_SERVER['PHP_SELF'].'?tpages='.$tpages.$var_filter;
// Added By HJ On 21-09-2020 For Optimized For Loop Query Start
$tripCompanyIdArr = array_column($db_payment, 'iCompanyId');
$storeTransferAmtArr = getTransforAmountbyRestaurant($tripCompanyIdArr, $ssql2);
// echo "<pre>";print_r($db_payment);die;
$storeExpectedAmtArr = CalculateStoreExpectedAmount($tripCompanyIdArr, $ssql2);
// echo "<pre>";print_r($storeExpectedAmtArr);die;
for ($i = 0; $i < count($db_payment); ++$i) {
    $iCompanyId = $db_payment[$i]['iCompanyId'];
    $transferAmount = $expectedAmount = 0;
    if (isset($storeTransferAmtArr[$iCompanyId])) {
        $transferAmount = $storeTransferAmtArr[$iCompanyId];
    }
    if (isset($storeExpectedAmtArr[$iCompanyId])) {
        $expectedAmount = $storeExpectedAmtArr[$iCompanyId];
    }
    $db_payment[$i]['transferAmount'] = $transferAmount;
    $db_payment[$i]['expectedAmount'] = $expectedAmount;
    // $db_payment[$i]['transferAmount'] = getTransforAmountbyRestaurant($db_payment[$i]['iCompanyId'],$ssql2);
    // $db_payment[$i]['expectedAmount'] = CalculateStoreExpectedAmount($db_payment[$i]['iCompanyId'],$ssql2);
    // echo $iCompanyId."===".$transferAmount."===".$expectedAmount;die;
}
// Added By HJ On 21-09-2020 For Optimized For Loop Query End
$catdata = serviceCategories;
$allservice_cat_data = json_decode($catdata, true);
?>
<!DOCTYPE html>
<html lang="en">
<!-- BEGIN HEAD-->
<head>
    <meta charset="UTF-8"/>
    <title><?php echo $SITE_NAME; ?> | <?php echo $langage_lbl_admin['LBL_RESTAURANT_TXT_ADMIN']; ?> Payout Report</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta content="" name="keywords"/>
    <meta content="" name="description"/>
    <meta content="" name="author"/>
    <?php include_once 'global_files.php'; ?>
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
                    <h2><?php echo $langage_lbl_admin['LBL_RESTAURANT_TXT_ADMIN']; ?> Payout Report</h2>
                </div>
            </div>
            <hr/>
            <?php include 'valid_msg.php'; ?>
            <form name="frmsearch" id="frmsearch" action="javascript:void(0);" method="post">
                <div class="Posted-date mytrip-page">
                    <input type="hidden" name="action" value="search"/>
                    <h3>Search by Date...</h3>
                    <span>
								<a onClick="return todayDate('dp4','dp5');"><?php echo $langage_lbl_admin['LBL_MYTRIP_Today']; ?></a>
								<a onClick="return yesterdayDate('dFDate','dTDate');"><?php echo $langage_lbl_admin['LBL_MYTRIP_Yesterday']; ?></a>
								<a onClick="return currentweekDate('dFDate','dTDate');"><?php echo $langage_lbl_admin['LBL_MYTRIP_Current_Week']; ?></a>
								<a onClick="return previousweekDate('dFDate','dTDate');"><?php echo $langage_lbl_admin['LBL_MYTRIP_Previous_Week']; ?></a>
								<a onClick="return currentmonthDate('dFDate','dTDate');"><?php echo $langage_lbl_admin['LBL_MYTRIP_Current_Month']; ?></a>
								<a onClick="return previousmonthDate('dFDate','dTDate');"><?php echo $langage_lbl_admin['LBL_MYTRIP_Previous Month']; ?></a>
								<a onClick="return currentyearDate('dFDate','dTDate');"><?php echo $langage_lbl_admin['LBL_MYTRIP_Current_Year']; ?></a>
								<a onClick="return previousyearDate('dFDate','dTDate');"><?php echo $langage_lbl_admin['LBL_MYTRIP_Previous_Year']; ?></a>
								</span>
                    <span>
                                                                <!-- changed by me -->
                        <input type="text" id="dp4" name="startDate"
                               placeholder="From Date" class="form-control"
                               value="" readonly=""
                               style="cursor:default; background-color: #fff"/>
                        <input type="text" id="dp5" name="endDate"
                               placeholder="To Date" class="form-control"
                               value="" readonly=""
                               style="cursor:default; background-color: #fff"/>
                        <!--<input type="text" id="dp4" name="startDate" placeholder="From Date" class="form-control" value=""/>
                        <input type="text" id="dp5" name="endDate" placeholder="To Date" class="form-control" value="" />-->

 								<div class="col-lg-3 select001">
                                    <select class="form-control filter-by-text" name='searchCompany'
                                            data-text="Select <?php echo $langage_lbl_admin['LBL_RESTAURANT_TXT_ADMIN']; ?>"
                                            id="searchCompany">
                                        <option value="">Select <?php echo $langage_lbl_admin['LBL_RESTAURANT_TXT_ADMIN']; ?></option>
                                    </select>
                                </div>
                                <?php if (count($allservice_cat_data) > 1) { ?>
                                    <div class="col-lg-3 select001" style="padding-right:15px;">
		                            <select class="form-control filter-by-text" name="searchServiceType"
                                            data-text="Select Serivce Type" id="searchServiceType">
		                                <option value="">Select <?php echo $langage_lbl_admin['LBL_RIDER_NAME_TXT_ADMIN']; ?></option>
		                               <?php foreach ($allservice_cat_data as $value) { ?>
                                           <option value="<?php echo $value['iServiceId']; ?>" <?php if ($searchServiceType === $value['iServiceId']) {
                                               echo 'selected';
                                           } ?>><?php echo clearName($value['vServiceName']); ?></option>
                                       <?php } ?>
		                            </select>
		                        </div>
                                <?php } ?>
                                <div class="tripBtns001">
                                <b>
									<input type="submit" value="Search" class="btnalt button11" id="Search"
                                           name="Search" title="Search"/>
									<input type="button" value="Reset" class="btnalt button11"
                                           onClick="window.location.href = '<?php echo $LOCATION_FILE_ARRAY['RESTAURANTS_PAY_REPORT.PHP']; ?>'"/>
									 <?php if (count($db_payment) > 0 && SITE_TYPE !== 'Demo' && $userObj->hasPermission('export-store-payment')) { ?>
                                         <button type="button" onClick="exportlist()"
                                                 class="export-btn001">Export</button>
                                     <?php } ?> </b>
                                </div>
							</span>
                    <div class="tripBtns001">
                    </div>
                </div>
            </form>
            <form name="_list_form" id="_list_form" class="_list_form" method="post"
                  action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <input type="hidden" id="actionpay" name="action" value="pay_restaurant">
                <input type="hidden" name="ePayRestaurant" id="ePayRestaurant" value="">
                <input type="hidden" name="prev_start" id="prev_start" value="<?php echo $startDate; ?>">
                <input type="hidden" name="prev_end" id="prev_end" value="<?php echo $endDate; ?>">
                <input type="hidden" name="prev_order" id="prev_order" value="<?php echo $order; ?>">
                <input type="hidden" name="prev_sortby" id="prev_sortby" value="<?php echo $sortby; ?>">
                <input type="hidden" name="prevsearchCompany" id="prevsearchCompany" value="<?php echo $searchCompany; ?>">
                <table class="table table-striped table-bordered table-hover" id="dataTables-example123">
                    <thead>
                    <tr>
                        <?php if (count($allservice_cat_data) > 1) { ?>
                            <th>Service type</th>
                        <?php } ?>
                        <th>
                            <a href="javascript:void(0);" onClick="Redirect(2,<?php if ('2' === $sortby) {
                                echo $order;
                            } else { ?>0<?php } ?>)"><?php echo $langage_lbl_admin['LBL_RESTAURANT_TXT_ADMIN']; ?>
                                Name <?php if (2 === $sortby) {
                                    if (0 === $order) { ?>
                                        <i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php } else { ?>
                                        <i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php }
                                        } else { ?>
                                    <i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a>
                        </th>
                        <th>
                            <a href="javascript:void(0);" onClick="Redirect(3,<?php if ('3' === $sortby) {
                                echo $order;
                            } else { ?>0<?php } ?>)"><?php echo $langage_lbl_admin['LBL_RESTAURANT_TXT_ADMIN']; ?>
                                <br/>
                                Account Name <?php if (3 === $sortby) {
                                    if (0 === $order) { ?>
                                        <i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php } else { ?>
                                        <i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php }
                                        } else { ?>
                                    <i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a>
                        </th>
                        <th>
                            <a href="javascript:void(0);" onClick="Redirect(4,<?php if ('4' === $sortby) {
                                echo $order;
                            } else { ?>0<?php } ?>)">Bank Name <?php if (4 === $sortby) {
                                if (0 === $order) { ?>
                                        <i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php } else { ?>
                                        <i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php }
                                        } else { ?>
                                    <i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a>
                        </th>
                        <th>Account Number</th>
                        <th>Sort Code</th>
                        <!-- <th>Expected Amount Pay <br/> to Restaurant</th> -->
                        <th style="text-align:center;">Final Amount Pay
                            <br/>
                            to <?php echo $langage_lbl_admin['LBL_RESTAURANT_TXT_ADMIN']; ?></th>
                        <th style="text-align:center;"><?php echo $langage_lbl_admin['LBL_RESTAURANT_TXT_ADMIN']; ?>
                            <br/>
                            Payment Status
                        </th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (count($db_payment) > 0) {
                        for ($i = 0; $i < count($db_payment); ++$i) { ?>
                            <tr class="gradeA">
                                <?php if (count($allservice_cat_data) > 1) { ?>
                                    <td><?php echo $db_payment[$i]['vServiceName']; ?></td>
                                <?php } ?>
                                <td>
                                    <?php if ('' !== $db_payment[$i]['resturant_phone']) {
                                        ?>
										 <?php if ($userObj->hasPermission('view-store')) { ?><a href="javascript:void(0);" onClick="show_store_details('<?php echo $db_payment[$i]['iCompanyId']; ?>')" style="text-decoration: underline;"><?php } ?><?php echo clearName(stripslashes($db_payment[$i]['vCompany'])); ?><?php if ($userObj->hasPermission('view-store')) { ?></a><?php } ?>
                                        <?php
                                        echo '<br>';
                                        echo '<b>Phone: </b> +'.clearPhone($db_payment[$i]['resturant_phone']);
                                    } else {
                                        ?>
                                         <?php if ($userObj->hasPermission('view-store')) { ?><a href="javascript:void(0);" onClick="show_store_details('<?php echo $db_payment[$i]['iCompanyId']; ?>')" style="text-decoration: underline;"><?php } ?><?php echo clearName(stripslashes($db_payment[$i]['vCompany'])); ?><?php if ($userObj->hasPermission('view-store')) { ?></a><?php } ?>
                                    <?php } ?>
                                </td>
                                <td><?php echo ('' !== $db_payment[$i]['vAcctHolderName']) ? clearName($db_payment[$i]['vAcctHolderName']) : '---'; ?></td>
                                <td><?php echo ('' !== $db_payment[$i]['vBankName']) ? clearName($db_payment[$i]['vBankName']) : '---'; ?></td>
                                <td><?php echo ('' !== $db_payment[$i]['vAcctNo']) ? clearName($db_payment[$i]['vAcctNo']) : '---'; ?></td>
                                <td><?php echo ('' !== $db_payment[$i]['vSwiftCode']) ? clearName($db_payment[$i]['vSwiftCode']) : '---'; ?></td>
                                <!--   <td style="text-align:right;">
									  <?php
                                if ($db_payment[$i]['expectedAmount'] > 0) {
                                    echo formateNumAsPerCurrency($db_payment[$i]['expectedAmount'], '');
                                } else {
                                    echo '---';
                                }
                            ?>
									  </td> -->
                                <td align="center">
                                    <?php
                                if ($db_payment[$i]['transferAmount'] > 0) {
                                    echo formateNumAsPerCurrency($db_payment[$i]['transferAmount'], '');
                                } else {
                                    echo '---';
                                }
                            ?>
                                </td>
                                <td align="center"><?php echo $db_payment[$i]['eRestaurantPaymentStatus']; ?>
                                    <br/>
                                    <a href="store_payment_report.php?action=search&startDate=<?php echo $startDate; ?>&endDate=<?php echo $endDate; ?>&searchCompany=<?php echo $db_payment[$i]['iCompanyId']; ?>&searchRestaurantPayment=Unsettled"
                                       target="_blank">[View Detail]
                                    </a>
                                </td>
                                <td>
                                    <?php if ('Unsettled' === $db_payment[$i]['eRestaurantPaymentStatus']) { ?>
                                        <input class="validate[required]" type="checkbox"
                                               value="<?php echo $db_payment[$i]['iCompanyId']; ?>"
                                               id="iTripId_<?php echo $db_payment[$i]['iCompanyId']; ?>" name="iCompanyId[]">
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                        <tr class="gradeA">
                            <td colspan="14" align="right">
                                <div class="row">
									<span style="margin:26px 13px 0 0;">
										<a onClick="javascript:PaytoRestaurant(); return false;"
                                           href="javascript:void(0);"><button
                                                    class="btn btn-primary ">Mark As Settled</button></a>
									</span>
                                </div>
                            </td>
                        </tr>
                    <?php } else { ?>
                        <tr class="gradeA">
                            <td colspan="13" style="text-align:center;"> No Payment Details Found.</td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </form>
            <?php include 'pagination_n.php'; ?>
        </div>
    </div>
</div>
<!--END PAGE CONTENT -->
</div>
<!--END MAIN WRAPPER -->
<form name="pageForm" id="pageForm" action="action/restaurants_pay_report.php" method="post">
    <input type="hidden" name="page" id="page" value="<?php echo $page; ?>">
    <input type="hidden" name="action" value="pay_restaurant">
    <input type="hidden" name="tpages" id="tpages" value="<?php echo $tpages; ?>">
    <input type="hidden" name="sortby" id="sortby" value="<?php echo $sortby; ?>">
    <input type="hidden" name="action1" value="<?php echo $action; ?>">
    <input type="hidden" name="order" id="order" value="<?php echo $order; ?>">
    <input type="hidden" name="searchServiceType" value="<?php echo $searchServiceType; ?>">
    <input type="hidden" name="searchCompany" value="<?php echo $searchCompany; ?>">
    <input type="hidden" name="startDate" value="<?php echo $startDate; ?>">
    <input type="hidden" name="endDate" value="<?php echo $endDate; ?>">
    <input type="hidden" name="method" id="method" value="">
</form>
<div class="modal fade" id="detail_modal2" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4>
                    <i aria-hidden="true" class="fa fa-building-o" style="margin:2px 5px 0 2px;"></i>
                    <?php echo $langage_lbl_admin['LBL_RESTAURANT_TXT_ADMIN']; ?> Details
                    <button type="button" class="close" data-dismiss="modal">x</button>
                </h4>
            </div>
            <div class="modal-body" style="max-height: 450px;overflow: auto;">
                <div id="imageIcons2" style="display:none">
                    <div align="center">
                        <img src="default.gif">
                        <br/>
                        <span>Retrieving details,please Wait...</span>
                    </div>
                </div>
                <div id="comp_detail"></div>
            </div>
        </div>
    </div>
</div>
<?php include_once 'footer.php'; ?>
<link rel="stylesheet" href="../assets/plugins/datepicker/css/datepicker.css"/>
<link rel="stylesheet" href="css/select2/select2.min.css"/>
<script src="js/plugins/select2.min.js"></script>
<script src="../assets/plugins/datepicker/js/bootstrap-datepicker.js"></script>
<script>
    $('#dp4').datepicker()
        .on('changeDate', function (ev) {
            var endDate = $('#dp5').val();
            if (ev.date.valueOf() < endDate.valueOf()) {
                $('#alert').show().find('strong').text('The start date can not be greater then the end date');
            } else {
                $('#alert').hide();
                var startDate = new Date(ev.date);
                $('#startDate').text($('#dp4').data('date'));
            }
            $('#dp4').datepicker('hide');
        });
    $('#dp5').datepicker()
        .on('changeDate', function (ev) {
            var startDate = $('#dp4').val();
            if (ev.date.valueOf() < startDate.valueOf()) {
                $('#alert').show().find('strong').text('The end date can not be less then the start date');
            } else {
                $('#alert').hide();
                var endDate = new Date(ev.date);
                $('#endDate').text($('#dp5').data('date'));
            }
            $('#dp5').datepicker('hide');
        });

    $(document).ready(function () {
        $("#dp5").click(function () {
            $('#dp5').datepicker('show');
            $('#dp4').datepicker('hide');
        });

        $("#dp4").click(function () {
            $('#dp4').datepicker('show');
            $('#dp5').datepicker('hide');
        });

        if ('<?php echo $startDate; ?>' != '') {
            $("#dp4").val('<?php echo $startDate; ?>');
            $("#dp4").datepicker('update', '<?php echo $startDate; ?>');
        }
        if ('<?php echo $endDate; ?>' != '') {
            $("#dp5").datepicker('update', '<?php echo $endDate; ?>');
            $("#dp5").val('<?php echo $endDate; ?>');
        }
    });

    function setRideStatus(actionStatus) {
        window.location.href = "trip.php?type=" + actionStatus;
    }

    function todayDate() {
        $("#dp4").val('<?php echo $Today; ?>');
        $("#dp5").val('<?php echo $Today; ?>');
    }

    function reset() {
        location.reload();

    }

    function yesterdayDate() {
        $("#dp4").val('<?php echo $Yesterday; ?>');
        $("#dp4").datepicker('update', '<?php echo $Yesterday; ?>');
        $("#dp5").datepicker('update', '<?php echo $Yesterday; ?>');
        $("#dp4").change();
        $("#dp5").change();
        $("#dp5").val('<?php echo $Yesterday; ?>');
    }

    function currentweekDate(dt, df) {
        $("#dp4").val('<?php echo $monday; ?>');
        $("#dp4").datepicker('update', '<?php echo $monday; ?>');
        $("#dp5").datepicker('update', '<?php echo $sunday; ?>');
        $("#dp5").val('<?php echo $sunday; ?>');
    }

    function previousweekDate(dt, df) {
        $("#dp4").val('<?php echo $Pmonday; ?>');
        $("#dp4").datepicker('update', '<?php echo $Pmonday; ?>');
        $("#dp5").datepicker('update', '<?php echo $Psunday; ?>');
        $("#dp5").val('<?php echo $Psunday; ?>');
    }

    function currentmonthDate(dt, df) {
        $("#dp4").val('<?php echo $currmonthFDate; ?>');
        $("#dp4").datepicker('update', '<?php echo $currmonthFDate; ?>');
        $("#dp5").datepicker('update', '<?php echo $currmonthTDate; ?>');
        $("#dp5").val('<?php echo $currmonthTDate; ?>');
    }

    function previousmonthDate(dt, df) {
        $("#dp4").val('<?php echo $prevmonthFDate; ?>');
        $("#dp4").datepicker('update', '<?php echo $prevmonthFDate; ?>');
        $("#dp5").datepicker('update', '<?php echo $prevmonthTDate; ?>');
        $("#dp5").val('<?php echo $prevmonthTDate; ?>');
    }

    function currentyearDate(dt, df) {
        $("#dp4").val('<?php echo $curryearFDate; ?>');
        $("#dp4").datepicker('update', '<?php echo $curryearFDate; ?>');
        $("#dp5").datepicker('update', '<?php echo $curryearTDate; ?>');
        $("#dp5").val('<?php echo $curryearTDate; ?>');
    }

    function previousyearDate(dt, df) {
        $("#dp4").val('<?php echo $prevyearFDate; ?>');
        $("#dp4").datepicker('update', '<?php echo $prevyearFDate; ?>');
        $("#dp5").datepicker('update', '<?php echo $prevyearTDate; ?>');
        $("#dp5").val('<?php echo $prevyearTDate; ?>');
    }

    function exportlist() {
        $("#actionpay").val("export");
        $("#pageForm").attr("action", "export_restaurants_pay_report.php");
        document.pageForm.submit();
    }

    $("#Search").on('click', function () {
        if ($("#dp5").val() < $("#dp4").val()) {
            alert("From date should be lesser than To date.")
            return false;
        } else {
            var action = $("#_list_form").attr('action');
            var formValus = $("#frmsearch").serialize();
            window.location.href = action + "?" + formValus;
        }
    });
    $(function () {
        $("select.filter-by-text#searchServiceType").each(function () {
            $(this).select2({
                placeholder: $(this).attr('data-text'),
                allowClear: true
            }); //theme: 'classic'
        });
    });

    $('body').on('keyup', '.select2-search__field', function () {
        $(".select2-container .select2-dropdown .select2-results .select2-results__options").addClass("hideoptions");
        if ($(".select2-results__options").is(".select2-results__message")) {
            $(".select2-container .select2-dropdown .select2-results .select2-results__options").removeClass("hideoptions");
        }
    });

    function formatDesign(item) {
        $(".select2-container .select2-dropdown .select2-results .select2-results__options").removeClass("hideoptions");
        if (!item.id) {
            return item.text;
        }
        //console.log(item);
        var selectionText = item.text.split("--");
        if (selectionText[2] != null && selectionText[1] != null) {
            var $returnString = $('<span>' + selectionText[0] + '</br>' + selectionText[1] + "</br>" + selectionText[2] + '</span>');
        } else if (selectionText[2] == null && selectionText[1] != null) {
            var $returnString = $('<span>' + selectionText[0] + '</br>' + selectionText[1] + '</span>');
        } else if (selectionText[2] != null && selectionText[1] == null) {
            var $returnString = $('<span>' + selectionText[0] + '</br>' + selectionText[2] + '</span>');
        }
        //$(".select2-container .select2-dropdown .select2-results .select2-results__options").removeClass("hideoptions");
        return $returnString;
    }

    function formatDesignnew(item) {
        if (!item.id) {
            return item.text;
        }
        var selectionText = item.text.split("--");
        return selectionText[0];
    }

    $(function () {
        $("select.filter-by-text#searchCompany").each(function () {
            $(this).select2({
                allowClear: true,
                placeholder: $(this).attr('data-text'),
                // minimumInputLength: 2,
                templateResult: formatDesign,
                templateSelection: formatDesignnew,
                ajax: {
                    url: 'ajax_getdriver_detail_search.php',
                    dataType: "json",
                    type: "POST",
                    async: true,
                    delay: 250,
                    // quietMillis:100,
                    data: function (params) {
                        // console.log(params);
                        var queryParameters = {
                            term: params.term,
                            page: params.page || 1,
                            usertype: 'Store'
                        }
                        //console.log(queryParameters);
                        return queryParameters;
                    },
                    processResults: function (data, params) {
                        //console.log(data);
                        params.page = params.page || 1;

                        if (data.length < 10) {
                            var more = false;
                        } else {
                            var more = (params.page * 10) <= data[0].total_count;
                        }

                        $(".select2-container .select2-dropdown .select2-results .select2-results__options").removeClass("hideoptions");

                        return {
                            results: $.map(data, function (item) {

                                if (item.Phoneno != '' && item.vEmail != '') {
                                    var textdata = item.fullName + "--" + "Email: " + item.vEmail + "--" + "Phone: +" + item.Phoneno;
                                } else if (item.Phoneno == '' && item.vEmail != '') {
                                    var textdata = item.fullName + "--" + "Email: " + item.vEmail;
                                } else if (item.Phoneno != '' && item.vEmail == '') {
                                    var textdata = item.fullName + "--" + "Phone: +" + item.Phoneno;
                                }
                                return {
                                    text: textdata,
                                    id: item.id
                                }
                            }),
                            pagination: {
                                more: more
                            }
                        };

                    },
                    cache: false
                }
            }); //theme: 'classic'
        });
    });
    var sIdCompany = '<?php echo $searchCompany; ?>';
    var sSelectCompany = $('select.filter-by-text#searchCompany');
    var itemname;
    var itemid;
    if (sIdCompany != '') {
        // $.ajax({
        //     type: 'POST',
        //     dataType: "json",
        //     url: 'ajax_getdriver_detail_search.php?id=' + sIdCompany + '&usertype=Store'
        // }).then(function (data) {
        //     // create the option and append to Select2
        //     $.map(data, function (item) {
        //         if(item.Phoneno != '' && item.vEmail != ''){
        //             var textdata = item.fullName  + "--" + "Email: " + item.vEmail + "--" + "Phone: +" + item.Phoneno;
        //         } else if(item.Phoneno == '' && item.vEmail != ''){
        //             var textdata = item.fullName  + "--" + "Email: " + item.vEmail;
        //         } else if(item.Phoneno != '' && item.vEmail == ''){
        //             var textdata = item.fullName  + "--" + "Phone: +" + item.Phoneno;
        //         }
        //         var textdata = item.fullName;
        //         itemname = textdata;
        //         itemid = item.id;
        //     });
        //     var option = new Option(itemname, itemid, true, true);
        //     sSelectCompany.append(option);
        // });

        var ajaxData = {
            'URL': '<?php echo $tconfig['tsite_url_main_admin']; ?>ajax_getdriver_detail_search.php?id=' + sIdCompany + '&usertype=Store',
            'AJAX_DATA': "",
            'REQUEST_DATA_TYPE': 'json'
        };
        getDataFromAjaxCall(ajaxData, function (response) {
            if (response.action == "1") {
                var data = response.result;
                $.map(data, function (item) {
                    if (item.Phoneno != '' && item.vEmail != '') {
                        var textdata = item.fullName + "--" + "Email: " + item.vEmail + "--" + "Phone: +" + item.Phoneno;
                    } else if (item.Phoneno == '' && item.vEmail != '') {
                        var textdata = item.fullName + "--" + "Email: " + item.vEmail;
                    } else if (item.Phoneno != '' && item.vEmail == '') {
                        var textdata = item.fullName + "--" + "Phone: +" + item.Phoneno;
                    }
                    var textdata = item.fullName;
                    itemname = textdata;
                    itemid = item.id;
                });
                var option = new Option(itemname, itemid, true, true);
                sSelectCompany.append(option);
            } else {
                console.log(response.result);
            }
        });
    }
</script>
</body>
<!-- END BODY-->
</html>
