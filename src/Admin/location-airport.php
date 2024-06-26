<?php
include_once '../common.php';

$script = 'Location';

// get Location
$hdn_del_id = $_POST['hdn_del_id'] ?? '';
$iAirportLocationId = $_GET['iAirportLocationId'] ?? '';
$status = $_GET['status'] ?? '';
$success = $_REQUEST['success'] ?? 0;
$tbl_name = 'airport_location_master';
// $script           = "Settings";

if ('' !== $hdn_del_id) {
    if (SITE_TYPE !== 'Demo') {
        $query = 'DELETE FROM `'.$tbl_name."` WHERE iAirportLocationId = '".$hdn_del_id."'"; // die;
        $obj->sql_query($query);
    } else {
        header('Location:location-airport.php?success=2');

        exit;
    }
}

// get Location

// Start Sorting
$sortby = $_REQUEST['sortby'] ?? 0;
$order = $_REQUEST['order'] ?? '';
$ord = ' ORDER BY lm.vLocationName ASC';
if (1 === $sortby) {
    if (0 === $order) {
        $ord = ' ORDER BY lm.vLocationName ASC';
    } else {
        $ord = ' ORDER BY lm.vLocationName DESC';
    }
}

if (2 === $sortby) {
    if (0 === $order) {
        $ord = ' ORDER BY c.vCountry ASC';
    } else {
        $ord = ' ORDER BY c.vCountry DESC';
    }
}

if (4 === $sortby) {
    if (0 === $order) {
        $ord = ' ORDER BY lm.eStatus ASC';
    } else {
        $ord = ' ORDER BY lm.eStatus DESC';
    }
}
// End Sorting

// Start Search Parameters
$option = isset($_REQUEST['option']) ? stripslashes($_REQUEST['option']) : '';
$keyword = isset($_REQUEST['keyword']) ? stripslashes($_REQUEST['keyword']) : '';

$ssql = '';
if ('' !== $keyword) {
    if ('' !== $option) {
        if (str_contains($option, 'eStatus')) {
            $ssql .= ' AND '.stripslashes($option)." LIKE '".stripslashes($keyword)."'";
        } else {
            $ssql .= ' AND '.stripslashes($option)." LIKE '%".stripslashes($keyword)."%'";
        }
    } else {
        $ssql .= " AND lm.vLocationName LIKE '%".$keyword."%' OR c.vCountry LIKE '%".$keyword."%'OR lm.eStatus LIKE '%".$keyword."%'";
    }
}
// End Search Parameters

// Pagination Start
$per_page = $DISPLAY_RECORD_NUMBER; // number of results to show per page
$sql = "SELECT COUNT(lm.iAirportLocationId) AS Total FROM airport_location_master as lm LEFT JOIN country as c on c.iCountryId=lm.iCountryId WHERE lm.eStatus != 'Deleted' {$ssql}";
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
$sql = "SELECT lm.*,c.vCountry FROM airport_location_master as lm LEFT JOIN country as c on c.iCountryId=lm.iCountryId WHERE lm.eStatus != 'Deleted' {$ssql} {$ord} LIMIT {$start}, {$per_page} ";

$data_drv = $obj->MySQLSelect($sql);

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
        <meta charset="UTF-8" />
       <title><?php echo $SITE_NAME; ?> | Airport FIFO Zone</title>
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
                                <h2>Airport FIFO Zone</h2>
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
                                          <option value="lm.vLocationName" <?php if ('lm.vLocationName' === $option) {
                                              echo 'selected';
                                          } ?> >Location</option>
                                          <option value="c.vCountry" <?php if ('c.vCountry' === $option) {
                                              echo 'selected';
                                          } ?> >Country</option>
                                          <option value="lm.eStatus" <?php if ('lm.eStatus' === $option) {
                                              echo 'selected';
                                          } ?> >Status</option>
                                    </select>
                                    </td>
                                    <td width="15%" class="searchform"><input type="Text" id="keyword" name="keyword" value="<?php echo $keyword; ?>"  class="form-control" /></td>
                                    <td width="12%">
                                      <input type="submit" value="Search" class="btnalt button11" id="Search" name="Search" title="Search" />
                                      <input type="button" value="Reset" class="btnalt button11" onClick="window.location.href='location-airport.php'"/>
                                    </td>
                                    <td width="30%"><a class="add-btn" href="location_action_airport.php" style="text-align: center;">Add Airport FIFO Zone</a></td>
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
                                            <select name="changeStatus" id="changeStatus" class="form-control" onchange="ChangeStatusAll(this.value);">
                                                    <option value="" >Select Action</option>
                                                    <option value='Active' <?php if ('Active' === $option) {
                                                        echo 'selected';
                                                    } ?> >Make Active</option>
                                                    <option value="Inactive" <?php if ('Inactive' === $option) {
                                                        echo 'selected';
                                                    } ?> >Make Inactive</option>
                                                    <option value="Deleted" <?php if ('Delete' === $option) {
                                                        echo 'selected';
                                                    } ?> >Make Delete</option>
                                            </select>
                                    </span>
                                    </div>
                                    </div>
                                    <div style="clear:both;"></div>
                                        <div class="table-responsive">
                                            <form class="_list_form" id="_list_form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                            <table class="table table-striped table-bordered table-hover">
                                                <thead>
                                                    <tr>
                                                        <th align="center" width="3%" style="text-align:center;"><input type="checkbox" id="setAllCheck" ></th>
                                                        <th><a href="javascript:void(0);" onClick="Redirect(1,<?php if ('1' === $sortby) {
                                                            echo $order;
                                                        } else { ?>0<?php } ?>)">Airport FIFO Zone Name<?php if (1 === $sortby) {
                                                            if (0 === $order) { ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php }
                                                            } else { ?><i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a></th>

                                                        <th align="center"><a href="javascript:void(0);" onClick="Redirect(2,<?php if ('2' === $sortby) {
                                                            echo $order;
                                                        } else { ?>0<?php } ?>)">Country <?php if (2 === $sortby) {
                                                            if (0 === $order) { ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php }
                                                            } else { ?><i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a></th>

                                                        <th align="center" style="text-align:center;"><a href="javascript:void(0);" onClick="Redirect(4,<?php if ('4' === $sortby) {
                                                            echo $order;
                                                        } else { ?>0<?php } ?>)">Status <?php if (4 === $sortby) {
                                                            if (0 === $order) { ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php }
                                                            } else { ?><i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a></th>
                                                        <th align="center" style="text-align:center;">Action</th>
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
                                                        <td align="center" style="text-align:center;"><input type="checkbox" id="checkbox" name="checkbox[]" <?php echo $default; ?> value="<?php echo $data_drv[$i]['iAirportLocationId']; ?>" />&nbsp;</td>
                                                        <td><?php echo $data_drv[$i]['vLocationName']; ?></td>
                                                        <td><?php echo $data_drv[$i]['vCountry']; ?></td>
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
                                                            <td align="center" style="text-align:center;" class="action-btn001">
                                                                <div class="share-button openHoverAction-class" style="display: block;">
                                                                    <label class="entypo-export"><span><img src="images/settings-icon.png" alt=""></span></label>
                                                                    <div class="social show-moreOptions openPops_<?php echo $data_drv[$i]['iAirportLocationId']; ?>">
                                                                        <ul>
                                                                            <li class="entypo-twitter" data-network="twitter"><a href="location_action_airport.php?id=<?php echo $data_drv[$i]['iAirportLocationId']; ?>" data-toggle="tooltip" title="Edit">
                                                                                <img src="img/edit-icon.png" alt="Edit">
                                                                            </a></li>
                                                                            <?php if ('Yes' !== $data_drv[$i]['eDefault']) { ?>
                                                                            <li class="entypo-facebook" data-network="facebook"><a href="javascript:void(0);" onclick="changeStatus('<?php echo $data_drv[$i]['iAirportLocationId']; ?>','Inactive')"  data-toggle="tooltip" title="Make Active">
                                                                                <img src="img/active-icon.png" alt="<?php echo $data_drv[$i]['eStatus']; ?>" >
                                                                            </a></li>
                                                                            <li class="entypo-gplus" data-network="gplus"><a href="javascript:void(0);" onclick="changeStatus('<?php echo $data_drv[$i]['iAirportLocationId']; ?>','Active')" data-toggle="tooltip" title="Make Inactive">
                                                                                <img src="img/inactive-icon.png" alt="<?php echo $data_drv[$i]['eStatus']; ?>" >
                                                                            </a></li>
                                                                            <li class="entypo-gplus" data-network="gplus"><a href="javascript:void(0);" onclick="changeStatusDelete('<?php echo $data_drv[$i]['iAirportLocationId']; ?>')"  data-toggle="tooltip" title="Delete">
                                                                                <img src="img/delete-icon.png" alt="Delete" >
                                                                            </a></li>
                                                                            <?php } ?>
                                                                        </ul>
                                                                    </div>
                                                                </div>
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
                    </div>
                </div>
                <!--END PAGE CONTENT -->
            </div>
            <!--END MAIN WRAPPER -->

<form name="pageForm" id="pageForm" action="action/location-airport.php" method="post" >
<input type="hidden" name="page" id="page" value="<?php echo $page; ?>">
<input type="hidden" name="tpages" id="tpages" value="<?php echo $tpages; ?>">
<input type="hidden" name="iAirportLocationId" id="iMainId01" value="" >
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
        var action = $("#_list_form").attr('action');
        var formValus = $("#frmsearch").serialize();
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