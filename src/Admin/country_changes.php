<?php
include_once '../common.php';

if (!$userObj->hasPermission('view-country')) {
    $userObj->redirect();
}
$script = $tbl_name = 'country';
// Start Sorting
$sortby = $_REQUEST['sortby'] ?? 0;
$order = $_REQUEST['order'] ?? '';
$ord = ' ORDER BY vCountry ASC';
if (1 === $sortby) {
    if (0 === $order) {
        $ord = ' ORDER BY vCountry ASC';
    } else {
        $ord = ' ORDER BY vCountry DESC';
    }
}

if (2 === $sortby) {
    if (0 === $order) {
        $ord = ' ORDER BY vPhoneCode ASC';
    } else {
        $ord = ' ORDER BY vPhoneCode DESC';
    }
}

if (3 === $sortby) {
    if (0 === $order) {
        $ord = ' ORDER BY eUnit ASC';
    } else {
        $ord = ' ORDER BY eUnit DESC';
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
$eStatus = $_REQUEST['eStatus'] ?? '';

$ssql = '';
if ('' !== $keyword) {
    if ('' !== $option) {
        if ('' !== $eStatus) {
            $ssql .= ' AND '.stripslashes($option)." LIKE '%".stripslashes($keyword)."%' AND eStatus = '".clean($eStatus)."'";
        } else {
            $ssql .= ' AND '.stripslashes($option)." LIKE '%".stripslashes($keyword)."%'";
        }
    } else {
        if ('' !== $eStatus) {
            $ssql .= " AND (vCountry LIKE '%".$keyword."%' OR vPhoneCode LIKE '%".$keyword."%' OR vCountryCodeISO_3 LIKE '%".$keyword."%') AND eStatus = '".clean($eStatus)."'";
        } else {
            $ssql .= " AND (vCountry LIKE '%".$keyword."%' OR vPhoneCode LIKE '%".$keyword."%' OR vCountryCodeISO_3 LIKE '%".$keyword."%')";
        }
    }
} elseif ('' !== $eStatus && '' === $keyword) {
    $ssql .= " AND eStatus = '".clean($eStatus)."'";
}
// End Search Parameters
// Pagination Start
// $per_page = $DISPLAY_RECORD_NUMBER; // number of results to show per page
$per_page = 1_000; // number of results to show per page
if (!empty($eStatus)) {
    $estatusquery = '';
} else {
    $estatusquery = " AND eStatus != 'Deleted'";
}
$sql = "SELECT COUNT(iCountryId) AS Total FROM country WHERE 1=1 {$estatusquery} {$ssql}";
$totalData = $obj->MySQLSelect($sql);
$total_results = $totalData[0]['Total'];
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
if (!empty($eStatus)) {
    $esql = '';
} else {
    $esql = " AND eStatus != 'Deleted'";
}
$sql = 'SELECT * FROM '.$tbl_name."  WHERE 1=1 {$esql} {$ssql} {$ord} LIMIT {$start}, {$per_page}";
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
    <head>
        <meta charset="UTF-8" />
        <title><?php echo $SITE_NAME; ?> | Country</title>
        <meta content="width=device-width, initial-scale=1.0" name="viewport" />
        <?php include_once 'global_files.php'; ?>
    </head>
    <body class="padTop53 " >
        <div id="wrap">
            <?php include_once 'header.php'; ?>
            <?php include_once 'left_menu.php'; ?>
            <!--PAGE CONTENT -->
            <div id="content">
                <div class="inner">
                    <div id="add-hide-show-div">
                        <div class="row">
                            <div class="col-lg-12">
                                <h2><?php echo $langage_lbl_admin['LBL_CAR_COUNTRY_ADMIN']; ?></h2>
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
                                            <option  value="vCountry" <?php
                                            if ('vCountry' === $option) {
                                                echo 'selected';
                                            }
?> >Country</option>
                                            <option value="vPhoneCode" <?php
if ('vEmail' === $option) {
    echo 'selected';
}
?> >Code</option>
                                            <!-- <option value="eStatus" <?php
if ('eStatus' === $option) {
    echo 'selected';
}
?> >Status</option> -->
                                        </select>
                                    </td>
                                    <td width="15%" class="searchform"><input type="Text" id="keyword" name="keyword" value="<?php echo $keyword; ?>"  class="form-control" /></td>
                                    <td width="12%" class="estatus_options" id="eStatus_options" >
                                        <select name="eStatus" id="estatus_value" class="form-control">
                                            <option value="" >Select Status</option>
                                            <option value='Active' <?php
if ('Active' === $eStatus) {
    echo 'selected';
}
?> >Active</option>
                                            <option value="Inactive" <?php
if ('Inactive' === $eStatus) {
    echo 'selected';
}
?> >Inactive</option>
                                            <option value="Deleted" <?php
                                    if ('Deleted' === $eStatus) {
                                        echo 'selected';
                                    }
?> >Delete</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="submit" value="Search" class="btnalt button11" id="Search" name="Search" title="Search" />
                                        <input type="button" value="Reset" class="btnalt button11" onClick="window.location.href = 'country.php'"/>
                                    </td>
<?php if ($userObj->hasPermission('create-country')) { ?>
                                        <td width="30%"><a class="add-btn" href="country_action.php" style="text-align: center;">Add Country</a></td>
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
                                                <?php if ($userObj->hasPermission(['update-status-country', 'delete-country'])) { ?>
                                                <select name="changeStatus" id="changeStatus" class="form-control" onchange="ChangeStatusAll(this.value);">
                                                    <option value="" >Select Action</option>
                                                            <?php if ($userObj->hasPermission('update-status-country')) { ?>
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
    <?php if ('Deleted' !== $eStatus && $userObj->hasPermission('delete-country')) { ?>
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
                                                <button type="button" onclick="showExportTypes('country')" >Export</button>
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
                                                    <th width="20%"><a href="javascript:void(0);" onClick="Redirect(1,<?php
                                                                                                                if ('1' === $sortby) {
                                                                                                                    echo $order;
                                                                                                                } else {
                                                                                                                    ?>0<?php } ?>)">Country <?php if (1 === $sortby) {
                                                                                                                        if (0 === $order) {
                                                                                                                            ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php }
                                                                                                                            } else {
                                                                                                                                ?><i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a></th>
                                                    <th width="20%">Country Code</th>
                                                    <th width="20%"><a href="javascript:void(0);" onClick="Redirect(2,<?php
                                                if ('2' === $sortby) {
                                                    echo $order;
                                                } else {
                                                    ?>0<?php } ?>)">Code <?php if (2 === $sortby) {
                                                        if (0 === $order) {
                                                            ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php }
                                                            } else {
                                                                ?><i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a></th>

                                                    <th width="20%"><a href="javascript:void(0);" onClick="Redirect(3,<?php
                                            if ('3' === $sortby) {
                                                echo $order;
                                            } else {
                                                ?>0<?php } ?>)">Unit(Miles/KMs) <?php if (3 === $sortby) {
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
                                                            <td align="center" style="text-align:center;"><input type="checkbox" id="checkbox" name="checkbox[]" <?php echo $default; ?> value="<?php echo $data_drv[$i]['iCountryId']; ?>" />&nbsp;</td>
                                                            <td><?php echo $data_drv[$i]['vCountry']; ?></td>
                                                            <td><?php echo $data_drv[$i]['vCountryCode']; ?></td>
                                                            <td><?php echo $data_drv[$i]['vPhoneCode']; ?></td>
                                                            <td><?php echo $data_drv[$i]['eUnit']; ?></td>

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
                                                                    <div class="social show-moreOptions openPops_<?php echo $data_drv[$i]['iCountryId']; ?>">
                                                                        <ul>
                                                                            <li class="entypo-twitter" data-network="twitter"><a href="country_action.php?id=<?php echo $data_drv[$i]['iCountryId']; ?>" data-toggle="tooltip" title="Edit">
                                                                                    <img src="img/edit-icon.png" alt="Edit">
                                                                                </a></li>
        <?php if ('Yes' !== $data_drv[$i]['eDefault']) { ?>
            <?php if ($userObj->hasPermission('update-status-country')) { ?>
                                                                                    <li class="entypo-facebook" data-network="facebook"><a href="javascript:void(0);" onclick="changeStatus('<?php echo $data_drv[$i]['iCountryId']; ?>', 'Inactive')"  data-toggle="tooltip" title="Activate">
                                                                                            <img src="img/active-icon.png" alt="<?php echo $data_drv[$i]['eStatus']; ?>" >
                                                                                        </a></li>
                                                                                    <li class="entypo-gplus" data-network="gplus"><a href="javascript:void(0);" onclick="changeStatus('<?php echo $data_drv[$i]['iCountryId']; ?>', 'Active')" data-toggle="tooltip" title="Deactivate">
                                                                                            <img src="img/inactive-icon.png" alt="<?php echo $data_drv[$i]['eStatus']; ?>" >
                                                                                        </a></li>
            <?php } ?>
            <?php if ('Deleted' !== $eStatus && $userObj->hasPermission('delete-country')) { ?>
                                                                                    <li class="entypo-gplus" data-network="gplus"><a href="javascript:void(0);" onclick="changeStatusDelete('<?php echo $data_drv[$i]['iCountryId']; ?>')"  data-toggle="tooltip" title="Delete">
                                                                                            <img src="img/delete-icon.png" alt="Delete" >
                                                                                        </a></li>
            <?php }
            }
                                                                                                    ?>
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
                                Country module will list all countries on this page.
                            </li>
                            <li>
                                Administrator can Activate / Deactivate / Delete any country.
                            </li>
                            <li>
                                Administrator can export data in XLS or PDF format.
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <!--END PAGE CONTENT -->
        </div>
        <!--END MAIN WRAPPER -->

        <form name="pageForm" id="pageForm" action="action/country.php" method="post" >
            <input type="hidden" name="page" id="page" value="<?php echo $page; ?>">
            <input type="hidden" name="tpages" id="tpages" value="<?php echo $tpages; ?>">
            <input type="hidden" name="iCountryId" id="iMainId01" value="" >
            <input type="hidden" name="eStatus" id="eStatus" value="<?php echo $eStatus; ?>" >
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
