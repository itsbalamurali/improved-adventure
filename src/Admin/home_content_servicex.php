<?php
include_once '../common.php';
if (!$userObj->hasPermission('view-home-page-content')) {
    $userObj->redirect();
}
$script = 'home_content_service';
$id = $_GET['id'] ?? '';
$status = $_GET['status'] ?? '';
$success = $_REQUEST['success'] ?? 0;
$tbl_name = getAppTypeWiseHomeTable();
if ('' !== $id && '' !== $status) {
    if (SITE_TYPE !== 'Demo') {
        $query = 'UPDATE `'.$tbl_name."` SET eStatus = '".$status."' WHERE id = '".$id."'";
        $obj->sql_query($query);
    } else {
        header('Location:home_content_servicex.php?success=2');

        exit;
    }
}
// Start Sorting
$sortby = $_REQUEST['sortby'] ?? 0;
$order = $_REQUEST['order'] ?? '';
$ord = ' ORDER BY hc.vCode ASC';
if (1 === $sortby) {
    if (0 === $order) {
        $ord = ' ORDER BY hc.vCode ASC';
    } else {
        $ord = ' ORDER BY hc.vCode DESC';
    }
}
if (4 === $sortby) {
    if (0 === $order) {
        $ord = ' ORDER BY hc.eStatus ASC';
    } else {
        $ord = ' ORDER BY hc.eStatus DESC';
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
        $ssql .= " AND vCode LIKE '%".$keyword."%' OR eStatus LIKE '%".$keyword."%'";
    }
}
// End Search Parameters
// Pagination Start
$per_page = $DISPLAY_RECORD_NUMBER; // number of results to show per page
$sql = 'SELECT COUNT(id) AS Total FROM `'.$tbl_name."` WHERE 1=1 AND eStatus = 'Active' {$ssql} {$ord}";
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
$sql = "SELECT hc.*,lm.vTitle,lm.iLanguageMasId FROM {$tbl_name} as hc LEFT JOIN language_master as lm on lm.vCode = hc.vCode WHERE 1 = 1 AND hc.eStatus = 'Active' {$ssql} {$ord} LIMIT {$start}, {$per_page} ";
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
    <meta charset="UTF-8"/>
    <title><?php echo $SITE_NAME; ?> | Home Content</title>
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
        <?php if (0 === $parent_ufx_catid) { ?>
            <div class="inner">
                <div class="row" style="display: flex; align-items: center;">
                    <div class="col-lg-6">
                        <h3 class="mt0 mb0">Our Services Menu</h3>
                    </div>
                    <div class="col-lg-6">
                        <a href="master_service_menu.php" class="add-btn">Manage</a>
                    </div>
                </div>
            </div>
        <?php } ?>
        <div class="inner ">
            <div id="add-hide-show-div">
                <div class="row">
                    <div class="col-lg-12">
                        <h2><?php echo $langage_lbl_admin['LBL_HOME_CONTENT_ADMIN']; ?></h2>
                        <!--<input type="button" id="" value="ADD A DRIVER" class="add-btn">-->
                    </div>
                </div>
                <hr/>
            </div>
            <?php include 'valid_msg.php'; ?>
            <div class="table-list">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="table-responsive">
                            <form class="_list_form" id="_list_form" method="post"
                                  action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th width="58%"><a href="javascript:void(0);" onClick="Redirect(1,<?php
                                            if ('1' === $sortby) {
                                                echo $order;
                                            } else {
                                                ?>0<?php } ?>)">Title<?php
                                                if (1 === $sortby) {
                                                    if (0 === $order) {
                                                        ?><i class="fa fa-sort-amount-asc"
                                                             aria-hidden="true"></i> <?php } else { ?><i
                                                            class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php
                                                             }
                                                } else {
                                                    ?><i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a></th>
                                        <th align="center" style="text-align:center;">Action</th>
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
                                            }
                                            ?>
                                            <tr class="gradeA">
                                                <td>Home Page - <?php echo $data_drv[$i]['vTitle']; ?></td>
                                                <td align="center" style="text-align:center;" class="action-btn001">
                                                    <a href="home_content_servicex_action.php?id=<?php echo $data_drv[$i]['iLanguageMasId']; ?>"
                                                       data-toggle="tooltip" title="Edit">
                                                        <img src="img/edit-icon.png" alt="Edit">
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    } else {
                                        ?>
                                        <tr class="gradeA">
                                            <td colspan="2"> No Records Found.</td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                            </form>
                            <?php // include('pagination_n.php');?>
                        </div>
                    </div> <!--TABLE-END-->
                </div>
            </div>
            <div class="admin-notes">
                <h4>Notes:</h4>
                <ul>
                    <li>Admin can change the Home page content as per language</li>
                    <li>Click on Action "Edit" icon to change the content for home page</li>
                </ul>
            </div>
        </div>
    </div>
    <!--END PAGE CONTENT -->
</div>
<!--END MAIN WRAPPER -->
<form name="pageForm" id="pageForm" action="action/home_content_servicex.php" method="post">
    <input type="hidden" name="page" id="page" value="<?php echo $page; ?>">
    <input type="hidden" name="tpages" id="tpages" value="<?php echo $tpages; ?>">
    <input type="hidden" name="iMakeId" id="iMainId01" value="">
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
    $("#Search").on('click', function () {
        //$('html').addClass('loading');
        var action = $("#_list_form").attr('action');
        // alert(action);
        var formValus = $("#frmsearch").serialize();
        // alert(action+formValus);
        window.location.href = action + "?" + formValus;
    });
</script>
</body>
<!-- END BODY-->
</html>
