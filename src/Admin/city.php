<?php
include_once '../common.php';

if (!$userObj->hasPermission('view-city')) {
    $userObj->redirect();
}
$script = $tbl_name = 'city';
// Start Sorting
$sortby = $_REQUEST['sortby'] ?? 0;
$order = $_REQUEST['order'] ?? '';
$ord = ' ORDER BY vCity ASC';
if (1 === $sortby) {
    if (0 === $order) {
        $ord = ' ORDER BY st.vState ASC';
    } else {
        $ord = ' ORDER BY st.vState DESC';
    }
}

if (2 === $sortby) {
    if (0 === $order) {
        $ord = ' ORDER BY ct.vCity ASC';
    } else {
        $ord = ' ORDER BY ct.vCity DESC';
    }
}

if (3 === $sortby) {
    if (0 === $order) {
        $ord = ' ORDER BY c.vCountry ASC';
    } else {
        $ord = ' ORDER BY c.vCountry DESC';
    }
}

if (4 === $sortby) {
    if (0 === $order) {
        $ord = ' ORDER BY ct.eStatus ASC';
    } else {
        $ord = ' ORDER BY ct.eStatus DESC';
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
        $ssql .= " AND (ct.vCity LIKE '%".$keyword."%' OR st.vState LIKE '%".$keyword."%' OR c.vCountry LIKE '%".$keyword."%' OR ct.eStatus LIKE '%".$keyword."%')";
    }
}
// End Search Parameters
// Pagination Start
$per_page = $DISPLAY_RECORD_NUMBER; // number of results to show per page
$sql = "SELECT COUNT(ct.iCityId) AS Total FROM city as ct INNER JOIN country AS c ON c.iCountryId =ct.iCountryId INNER JOIN state AS st ON st.iStateId=ct.iStateId WHERE ct.eStatus != 'Deleted' {$ssql}";
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

$sql = "SELECT ct.*,st.iStateId,st.vState,c.iCountryId,c.vCountry FROM city AS ct INNER JOIN country AS c ON c.iCountryId =ct.iCountryId INNER JOIN state AS st ON st.iStateId=ct.iStateId WHERE ct.eStatus != 'Deleted' {$ssql} {$ord} LIMIT {$start}, {$per_page}";
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
        <meta charset="UTF-8" />
        <title><?php echo $SITE_NAME; ?> | City</title>
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
                                <h2><?php echo $langage_lbl_admin['LBL_CAR_CITY_ADMIN']; ?></h2>
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
                                            <option  value="ct.vCity" <?php
                                            if ('ct.vCity' === $option) {
                                                echo 'selected';
                                            }
?> >City</option>
                                            <option  value="st.vState" <?php
if ('st.vState' === $option) {
    echo 'selected';
}
?> >State</option>
                                            <option value="c.vCountry" <?php
        if ('c.vCountry' === $option) {
            echo 'selected';
        }
?> >Country</option>
                                            <option value="ct.eStatus" <?php
if ('ct.eStatus' === $option) {
    echo 'selected';
}
?> >Status</option>
                                        </select>
                                    </td>
                                    <td width="15%"><input type="Text" id="keyword" name="keyword" value="<?php echo $keyword; ?>"  class="form-control" /></td>
                                    <td width="12%">
                                        <input type="submit" value="Search" class="btnalt button11" id="Search" name="Search" title="Search" />
                                        <input type="button" value="Reset" class="btnalt button11" onClick="window.location.href = 'city.php'"/>
                                    </td>
<?php if ($userObj->hasPermission('create-city')) { ?>
                                        <td width="30%"><a class="add-btn" href="city_action.php" style="text-align: center;">Add City</a></td>
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
                                                <?php if ($userObj->hasPermission(['update-status-city', 'delete-city'])) { ?>
                                                <select name="changeStatus" id="changeStatus" class="form-control" onchange="ChangeStatusAll(this.value);">
                                                    <option value="" >Select Action</option>
                                                            <?php if ($userObj->hasPermission('update-status-city')) { ?>
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
    <?php if ('Deleted' !== $eStatus && $userObj->hasPermission('delete-city')) { ?>
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
                                            <form name="_export_form" id="_export_form" method="post" >
                                                <!-- <a href="city_import.php" class="btn btn-default"> Import </a> -->
                                                <button type="button" onclick="showExportTypes('city')" >Export</button>
                                            </form>
                                        </div>
                                                        <?php } ?>
                                </div>
                                <div style="clear:both;"></div>
                                <div class="table-responsive">
                                    <form class="_list_form" id="_list_form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                        <table class="table table-striped table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th align="center" width="3%" style="text-align:center;"><input type="checkbox" id="setAllCheck" ></th>
                                                    <th width="20%"><a href="javascript:void(0);" onClick="Redirect(2,<?php
                                                            if ('2' === $sortby) {
                                                                echo $order;
                                                            } else {
                                                                ?>0<?php } ?>)">City <?php if (2 === $sortby) {
                                                                    if (0 === $order) {
                                                                        ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php }
                                                                        } else {
                                                                            ?><i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a></th>
                                                    <th width="20%"><a href="javascript:void(0);" onClick="Redirect(1,<?php
                                                    if ('1' === $sortby) {
                                                        echo $order;
                                                    } else {
                                                        ?>0<?php } ?>)">State <?php if (1 === $sortby) {
                                                            if (0 === $order) {
                                                                ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php }
                                                                } else {
                                                                    ?><i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a></th>
                                                    <th width="20%"><a href="javascript:void(0);" onClick="Redirect(3,<?php
                                                if ('3' === $sortby) {
                                                    echo $order;
                                                } else {
                                                    ?>0<?php } ?>)">Country <?php if (3 === $sortby) {
                                                        if (0 === $order) {
                                                            ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php }
                                                            } else {
                                                                ?><i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a></th>

                                                    <th width="8%" align="center" style="text-align:center;"><a href="javascript:void(0);" onClick="Redirect(4,<?php
                                                                    if ('4' === $sortby) {
                                                                        echo $order;
                                                                    } else {
                                                                        ?>0<?php } ?>)">Status <?php if (4 === $sortby) {
                                                                            if (0 === $order) {
                                                                                ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php }
                                                                                } else {
                                                                                    ?><i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a></th>
                                                    <th width="8%" align="center" style="text-align:center;">Action</th>
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
                                                            <td align="center" style="text-align:center;"><input type="checkbox" id="checkbox" name="checkbox[]" <?php echo $default; ?> value="<?php echo $data_drv[$i]['iCityId']; ?>" />&nbsp;</td>
                                                            <td><?php echo $data_drv[$i]['vCity']; ?></td>
                                                            <td><?php echo $data_drv[$i]['vState']; ?></td>
                                                            <td><?php echo $data_drv[$i]['vCountry']; ?></td>
                                                                            <?php /* <td><?= $data_drv[$i]['vCityCodeISO_3']; ?></td> */ ?>

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
                                                                <img src="<?php echo $dis_img; ?>" alt="<?php echo $data_drv[$i]['eStatus']; ?>" data-toggle="tooltip" title="<?php echo $data_drv[$i]['eStatus']; ?>">
                                                            </td>
                                                            <td align="center" style="text-align:center;" class="action-btn001">
                                                                <div class="share-button openHoverAction-class" style="display: block;">
                                                                    <label class="entypo-export"><span><img src="images/settings-icon.png" alt=""></span></label>
                                                                    <div class="social show-moreOptions openPops_<?php echo $data_drv[$i]['iCityId']; ?>">
                                                                        <ul>
                                                                            <li class="entypo-twitter" data-network="twitter"><a href="city_action.php?id=<?php echo $data_drv[$i]['iCityId']; ?>" data-toggle="tooltip" title="Edit">
                                                                                    <img src="img/edit-icon.png" alt="Edit">
                                                                                </a></li>
        <?php if ($userObj->hasPermission('update-status-city')) { ?>
                                                                                <li class="entypo-facebook" data-network="facebook"><a href="javascript:void(0);" onclick="changeStatus('<?php echo $data_drv[$i]['iCityId']; ?>', 'Inactive')"  data-toggle="tooltip" title="Activate">
                                                                                        <img src="img/active-icon.png" alt="<?php echo $data_drv[$i]['eStatus']; ?>" >
                                                                                    </a></li>
                                                                                <li class="entypo-gplus" data-network="gplus"><a href="javascript:void(0);" onclick="changeStatus('<?php echo $data_drv[$i]['iCityId']; ?>', 'Active')" data-toggle="tooltip" title="Deactivate">
                                                                                        <img src="img/inactive-icon.png" alt="<?php echo $data_drv[$i]['eStatus']; ?>" >
                                                                                    </a></li>
        <?php } ?>
        <?php if ($userObj->hasPermission('delete-city')) { ?>
                                                                                <li class="entypo-gplus" data-network="gplus"><a href="javascript:void(0);" onclick="changeStatusDelete('<?php echo $data_drv[$i]['iCityId']; ?>')"  data-toggle="tooltip" title="Delete">
                                                                                        <img src="img/delete-icon.png" alt="Delete" >
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
                                City module will list all countries on this page.
                            </li>
                            <li>

                                Can Activate / Deactivate / Delete any city.
                            </li>
                            <li>
                            <li>
                                Administrator can export data in XLS or PDF format.
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

        <form name="pageForm" id="pageForm" action="action/city.php" method="post" >
            <input type="hidden" name="page" id="page" value="<?php echo $page; ?>">
            <input type="hidden" name="tpages" id="tpages" value="<?php echo $tpages; ?>">
            <input type="hidden" name="iCityId" id="iMainId01" value="" >
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