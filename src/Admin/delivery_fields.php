<?php
include_once '../common.php';

$AUTH_OBJ->checkMemberAuthentication();

$script = 'delivery_package';

$userObj->redirect();

// get make

$hdn_del_id = $_POST['hdn_del_id'] ?? '';

$iDeliveryFieldId = $_GET['iDeliveryFieldId'] ?? '';

$status = $_GET['status'] ?? '';

$success = $_REQUEST['success'] ?? 0;

$tbl_name = 'delivery_fields';

// $script			= "Settings";

if ('' !== $hdn_del_id) {
    if (SITE_TYPE !== 'Demo') {
        $query = 'DELETE FROM `'.$tbl_name."` WHERE iDeliveryFieldId = '".$hdn_del_id."'"; // die;

        $obj->sql_query($query);
    } else {
        header('Location:delivery_fields.php?success=2');

        exit;
    }
}

if ('' !== $iDeliveryFieldId && '' !== $status) {
    if (SITE_TYPE !== 'Demo') {
        $query = 'UPDATE `'.$tbl_name."` SET eStatus = '".$status."' WHERE iDeliveryFieldId	 = '".$iDeliveryFieldId."'";

        $obj->sql_query($query);
    } else {
        header('Location:delivery_fields.php?success=2');

        exit;
    }
}

/* $sql = "SELECT * FROM ".$tbl_name." ORDER BY iMakeId DESC";

$db_data = $obj->MySQLSelect($sql); */

// get make

// Start Sorting

$sortby = $_REQUEST['sortby'] ?? 0;

$order = $_REQUEST['order'] ?? '';

$ord = ' ORDER BY vFieldName ASC';

if (1 === $sortby) {
    if (0 === $order) {
        $ord = ' ORDER BY vFieldName ASC';
    } else {
        $ord = ' ORDER BY vFieldName DESC';
    }
}

if (3 === $sortby) {
    if (0 === $order) {
        $ord = ' ORDER BY iOrder ASC';
    } else {
        $ord = ' ORDER BY iOrder DESC';
    }
}

if (4 === $sortby) {
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
        if (str_contains($option, 'eStatus')) {
            $ssql .= ' AND '.stripslashes($option)." LIKE '".stripslashes($keyword)."'";
        } else {
            $ssql .= ' AND '.stripslashes($option)." LIKE '%".stripslashes($keyword)."%'";
        }
    } else {
        $ssql .= " AND vFieldName LIKE '%".$keyword."%' OR eStatus LIKE '%".$keyword."%'";
    }
}

if ('eStatus' === $option) {
    $eStatussql = " AND eStatus = '".ucfirst($keyword)."'";
} else {
    $eStatussql = " AND eStatus != 'Deleted'";
}

// End Search Parameters

// Pagination Start

$per_page = $DISPLAY_RECORD_NUMBER; // number of results to show per page

$sql = "SELECT COUNT(iDeliveryFieldId) AS Total FROM delivery_fields WHERE 1=1 {$eStatussql} {$ssql}";

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

$sql = 'SELECT * FROM '.$tbl_name." WHERE 1=1 {$eStatussql}  {$ssql} {$ord} LIMIT {$start}, {$per_page} ";

$data_drv = $obj->MySQLSelect($sql);

// echo '<pre>--->'; print_r($data_drv); exit;

$endRecord = count($data_drv);

// echo '<pre>--->'; print_r($data_drv);

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

        <meta charset="UTF-8" />

       <title><?php echo $SITE_NAME; ?> | <?php echo $langage_lbl_admin['LBL_MULTI_DELIVERY_FORM']; ?></title>

        <meta content="width=device-width, initial-scale=1.0" name="viewport" />

        <?php include_once 'global_files.php'; ?>

    </head>

    <!-- END  HEAD-->



    <!-- BEGIN BODY-->

    <body class="padTop53 " >

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

                                <h2><?php echo $langage_lbl_admin['LBL_MULTI_DELIVERY_FORM']; ?></h2>

                               <!--  <h2><?php echo $langage_lbl_admin['LBL_PACKAGE_TYPE_ADMIN']; ?></h2> -->

                                <!--<input type="button" id="" value="ADD A DRIVER" class="add-btn">-->

                            </div>

                        </div>

                        <hr />

                    </div>

                    <?php include 'valid_msg.php'; ?>

                    <form name="frmsearch" id="frmsearch" action="javascript:void(0);" method="post">

                        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="admin-nir-table">

                              <tbody>

                                <tr>

                                    <td width="5%"><label for="textfield"><strong>Search:</strong></label></td>

                                    <td width="10%" class=" padding-right10"><select name="option" id="option" class="form-control">

                                          <option value="">All</option>

                                          <option value="vFieldName" <?php if ('vFieldName' === $option) {
                                              echo 'selected';
                                          } ?> >Delivery Package Type</option>

                                          <option value="eStatus" <?php if ('eStatus' === $option) {
                                              echo 'selected';
                                          } ?> >Status</option>

                                    </select>

                                    </td>

                                    <td width="15%"><input type="Text" id="keyword" name="keyword" value="<?php echo $keyword; ?>"  class="form-control" /></td>

                                    <td width="12%">

                                      <input type="submit" value="Search" class="btnalt button11" id="Search" name="Search" title="Search" />

                                      <input type="button" value="Reset" class="btnalt button11" onClick="window.location.href='delivery_fields.php'"/>

                                    </td>

                                    <!--<td width="30%"><a class="add-btn" href="delivery_package_action.php" style="text-align: center;">Add Package Type</a></td>-->

                                </tr>

                              </tbody>

                        </table>



                      </form>

                    <div class="table-list">

                        <div class="row">

                            <div class="col-lg-12">

                                <?php /*<div class="admin-nir-export">

                                    <div class="changeStatus col-lg-12 option-box-left">

                                    <span class="col-lg-2 new-select001">

                                            <select name="changeStatus" id="changeStatus" class="form-control" onchange="ChangeStatusAll(this.value);">

                                                    <option value="" >Select Action</option>

                                                    <option value='Active' <?php if ($option == 'Active') { echo "selected"; } ?> >Package Type Active</option>

                                                    <option value="Inactive" <?php if ($option == 'Inactive') {echo "selected"; } ?> >Package Type Inactive</option>

                                                    <option value="Deleted" <?php if ($option == 'Delete') {echo "selected"; } ?> >Package Type Delete</option>

                                            </select>

                                    </span>

                                    </div>

                                    <?php if(!empty($data_drv)) {?>

                                    <!--<div class="panel-heading">

                                        <form name="_export_form" id="_export_form" method="post" >

                                            <button type="button" onclick="showExportTypes('package_type')" >Export</button>

                                        </form>

                                   </div>-->

                                   <?php } ?>

                                    </div>*/ ?>

                                    <div style="clear:both;"></div>

                                        <div class="table-responsive">

                                            <form class="_list_form" id="_list_form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">

                                            <table class="table table-striped table-bordered table-hover">

                                                <thead>

                                                    <tr>

                                                        <th width="20%"><a href="javascript:void(0);" onClick="Redirect(1,<?php if ('1' === $sortby) {
                                                            echo $order;
                                                        } else { ?>0<?php } ?>)">Field Name <?php if (1 === $sortby) {
                                                            if (0 === $order) { ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php }
                                                            } else { ?><i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a></th>



														 <th width="8%" align="center" style="text-align:center;"><a href="javascript:void(0);" onClick="Redirect(3,<?php if ('3' === $sortby) {
														     echo $order;
														 } else { ?>0<?php } ?>)">Order <?php if (3 === $sortby) {
														     if (0 === $order) { ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php }
														     } else { ?><i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a></th>





                                                        <th width="8%" align="center" style="text-align:center;"><a href="javascript:void(0);" onClick="Redirect(4,<?php if ('4' === $sortby) {
                                                            echo $order;
                                                        } else { ?>0<?php } ?>)">Status <?php if (4 === $sortby) {
                                                            if (0 === $order) { ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php }
                                                            } else { ?><i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a></th>

                                                        <?php/*<th width="8%" align="center" style="text-align:center;">Action</th>*/ ?>

                                                    </tr>

                                                </thead>

                                                <tbody>

                                                    <?php

                                                    // echo '<pre>--->';print_r($data_drv);

                                                    if (!empty($data_drv)) {
                                                        for ($i = 0; $i < count($data_drv); ++$i) {
                                                            $default = '';

                                                            if ('Yes' === $data_drv[$i]['eDefault']) {
                                                                $default = 'disabled';
                                                            } ?>

                                                    <tr class="gradeA">

                                                        <td><?php echo $data_drv[$i]['vFieldName']; ?></td>

                                                        <td><?php echo $data_drv[$i]['iOrder']; ?></td>



                                                        <td align="center" style="text-align:center;">

                                                                <?php if ('Active' === $data_drv[$i]['eStatus']) {
                                                                    $dis_img = 'img/active-icon.png';
                                                                } elseif ('Inactive' === $data_drv[$i]['eStatus']) {
                                                                    $dis_img = 'img/inactive-icon.png';
                                                                } elseif ('Deleted' === $data_drv[$i]['eStatus']) {
                                                                    $dis_img = 'img/delete-icon.png';
                                                                }?>

                                                                <img src="<?php echo $dis_img; ?>" alt="<?php echo $data_drv[$i]['eStatus']; ?>" data-toggle="tooltip" title="<?php echo $data_drv[$i]['eStatus']; ?>">

                                                            </td>

                                                            <?php/*<td align="center" style="text-align:center;" class="action-btn001">

                                                                <div class="share-button openHoverAction-class" style="display: block;">

                                                                    <label class="entypo-export"><span><img src="images/settings-icon.png" alt=""></span></label>

                                                                    <div class="social show-moreOptions openPops_<?php echo $data_drv[$i]['iDeliveryFieldId']; ?>">

                                                                        <ul>

                                                                            <li class="entypo-twitter" data-network="twitter"><a href="delivery_package_action.php?id=<?php echo $data_drv[$i]['iDeliveryFieldId']; ?>" data-toggle="tooltip" title="Edit">

                                                                                <img src="img/edit-icon.png" alt="Edit">

                                                                            </a></li>

                                                                            <?php if ('Yes' !== $data_drv[$i]['eDefault']) { ?>

                                                                            <li class="entypo-facebook" data-network="facebook"><a href="javascript:void(0);" onclick="changeStatus('<?php echo $data_drv[$i]['iDeliveryFieldId']; ?>','Inactive')"  data-toggle="tooltip" title="Package Type Active">

                                                                                <img src="img/active-icon.png" alt="<?php echo $data_drv[$i]['eStatus']; ?>" >

                                                                            </a></li>

                                                                            <li class="entypo-gplus" data-network="gplus"><a href="javascript:void(0);" onclick="changeStatus('<?php echo $data_drv[$i]['iDeliveryFieldId']; ?>','Active')" data-toggle="tooltip" title="Package Type Inactive">

                                                                                <img src="img/inactive-icon.png" alt="<?php echo $data_drv[$i]['eStatus']; ?>" >

                                                                            </a></li>

                                                                            <li class="entypo-gplus" data-network="gplus"><a href="javascript:void(0);" onclick="changeStatusDelete('<?php echo $data_drv[$i]['iDeliveryFieldId']; ?>')"  data-toggle="tooltip" title="Delete">

                                                                                <img src="img/delete-icon.png" alt="Delete" >

                                                                            </a></li>

                                                                            <?php } ?>

                                                                        </ul>

                                                                    </div>

                                                                </div>

                                                            </td>*/?>

                                                        </tr>

                                                    <?php }
                                                        } else { ?>

                                                        <tr class="gradeA">

                                                            <td colspan="3"> No Records Found.</td>

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

                    <?php/*<div class="admin-notes">

                            <h4>Notes:</h4>

                            <ul>

                                    <li>

                                            Package Type module will list all package type on this page.

                                    </li>

                                    <li>

                                            Administrator can Activate / Deactivate / Delete any Package Type.

                                    </li>

                                    <li>

                                            Administrator can export data in XLS or PDF format.

                                    </li>

                                    <!--li>

                                            "Export by Search Data" will export only search result data in XLS or PDF format.

                                    </li-->

                            </ul>

                    </div>*/?>

                    </div>

                </div>

                <!--END PAGE CONTENT -->

            </div>

            <!--END MAIN WRAPPER -->



<form name="pageForm" id="pageForm" action="action/delivery_fields.php" method="post" >

<input type="hidden" name="page" id="page" value="<?php echo $page; ?>">

<input type="hidden" name="tpages" id="tpages" value="<?php echo $tpages; ?>">

<input type="hidden" name="iDeliveryFieldId" id="iMainId01" value="" >

<input type="hidden" name="status" id="status01" value="" >

<input type="hidden" name="statusVal" id="statusVal" value="" >

<input type="hidden" name="option" value="<?php echo $option; ?>" >

<input type="hidden" name="keyword" value="<?php echo $keyword; ?>" >

<input type="hidden" name="sortby" id="sortby" value="<?php echo $sortby; ?>" >

<input type="hidden" name="order" id="order" value="<?php echo $order; ?>" >

<input type="hidden" name="method" id="method" value="" >

</form>

    <?php

    include_once 'footer.php';

?>

        <script>



            $("#setAllCheck").on('click',function(){

                if($(this).prop("checked")) {

                    jQuery("#_list_form input[type=checkbox]").each(function() {

                        if($(this).attr('disabled') != 'disabled'){

                            this.checked = 'true';

                        }

                    });

                }else {

                    jQuery("#_list_form input[type=checkbox]").each(function() {

                        this.checked = '';

                    });

                }

            });



            $("#Search").on('click', function(){

                //$('html').addClass('loading');

                var action = $("#_list_form").attr('action');

               // alert(action);

                var formValus = $("#frmsearch").serialize();

//               alert(action+formValus);

                window.location.href = action+"?"+formValus;

            });



            $('.entypo-export').click(function(e){

                 e.stopPropagation();

                 var $this = $(this).parent().find('div');

                 $(".openHoverAction-class div").not($this).removeClass('active');

                 $this.toggleClass('active');

            });



            $(document).on("click", function(e) {

                if ($(e.target).is(".openHoverAction-class,.show-moreOptions,.entypo-export") === false) {

                  $(".show-moreOptions").removeClass("active");

                }

            });



        </script>

    </body>

    <!-- END BODY-->

</html>