<?php
include_once('../common.php');
if (!$userObj->hasPermission('view-pages')) {
    $userObj->redirect();
}
$default_lang = $LANG_OBJ->FetchSystemDefaultLang();
$script = 'page';
$tbl_name = 'pages';
//Start Sorting
$sortby = isset($_REQUEST['sortby']) ? $_REQUEST['sortby'] : 0;
$order = isset($_REQUEST['order']) ? $_REQUEST['order'] : '';
$ord = ' ORDER BY vPageName ASC';
if ($sortby == 1) {
    if ($order == 0) $ord = " ORDER BY vPageName ASC"; else
        $ord = " ORDER BY vPageName DESC";
}
if ($sortby == 2) {
    if ($order == 0) $ord = " ORDER BY vPageTitle_" . $default_lang . " ASC"; else
        $ord = " ORDER BY vPageTitle_" . $default_lang . " DESC";
}
//Added By HJ On 04-08-2020 For Remove pages Data From Cache Start
$cacheRebuildButton = 0;
if (isset($_REQUEST['ENABLE_CACHE_RESET_BUTTON']) && strtoupper($_REQUEST['ENABLE_CACHE_RESET_BUTTON']) == "YES") {
    $cacheRebuildButton = 1;
}
if (isset($_POST['rebuildcachebtn'])) {
    $oCache->flushData();
}
//Added By HJ On 04-08-2020 For Remove pages Data From Cache End
/* if($sortby == 3){
  if($order == 0)
  $ord = " ORDER BY vCountryCodeISO_3 ASC";
  else
  $ord = " ORDER BY vCountryCodeISO_3 DESC";
  } */
if ($sortby == 4) {
    if ($order == 0) $ord = " ORDER BY eStatus ASC"; else
        $ord = " ORDER BY eStatus DESC";
}
//End Sorting
// Start Search Parameters
$option = isset($_REQUEST['option']) ? stripslashes($_REQUEST['option']) : "";
$keyword = isset($_REQUEST['keyword']) ? stripslashes($_REQUEST['keyword']) : "";
$searchDate = isset($_REQUEST['searchDate']) ? $_REQUEST['searchDate'] : "";
$eStatus = isset($_REQUEST['eStatus']) ? $_REQUEST['eStatus'] : "";
$ssql = '';
if ($keyword != '') {
    if ($option != '') {
        if ($eStatus != '') {
            $ssql .= " AND " . stripslashes($option) . " LIKE '%" . stripslashes($keyword) . "%'  AND eStatus = '" . clean($eStatus) . "'";
        } else {
            if (strpos($option, 'eStatus') !== false) {
                $ssql .= " AND " . stripslashes($option) . " LIKE '" . stripslashes($keyword) . "'";
            } else {
                $ssql .= " AND " . stripslashes($option) . " LIKE '%" . stripslashes($keyword) . "%'";
            }
        }
    } else {
        if ($eStatus != '') {
            $ssql .= " AND vPageName LIKE '%" . $keyword . "%' OR vPageTitle_" . $default_lang . " LIKE '%" . $keyword . "%' AND eStatus = '" . clean($eStatus) . "' ";
        } else {
            $ssql .= " AND vPageName LIKE '%" . $keyword . "%' OR vPageTitle_" . $default_lang . " LIKE '%" . $keyword . "%' ";
        }
    }
} else if ($eStatus != '' && $keyword == '') {
    $ssql .= " AND eStatus = '" . clean($eStatus) . "'";
}
// End Search Parameters
//Pagination Start
$per_page = $DISPLAY_RECORD_NUMBER; // number of results to show per page
if (!empty($eStatus)) {
    $eStatus_sql = "";
} else {
    $eStatus_sql = " AND eStatus != 'Deleted'";
}
$ssql_hide_kot = "";
if (strtoupper(DELIVERALL) == "YES") {
    $ssql_hide_kot = " AND iPageId != '47'";
}
$serviceArray = $serviceIdArray = array();
$serviceArray = json_decode(serviceCategories, true);
$serviceIdArray = array_column($serviceArray, 'iServiceId');
$note_shown_safety = 0;
$safety_practice_sql = " AND iPageId != '54'";
if (strtoupper(DELIVERALL) == "YES") {
    if (count($serviceIdArray) == 1 && ($serviceIdArray[0] != 1 && $serviceIdArray[0] != 2)) {
        $safety_practice_sql = " AND iPageId != '54'";
        $note_shown_safety = 0;
    } else if (count($serviceIdArray) > 1 && (!in_array("1", $serviceIdArray) && !in_array("2", $serviceIdArray))) {
        $safety_practice_sql = " AND iPageId != '54'";
        $note_shown_safety = 0;
    } else {
        $safety_practice_sql = "";
        if (count($serviceIdArray) == 1) $note_shown_safety = 0; else $note_shown_safety = 1;
    }
}
$sql = "SELECT COUNT(iPageId) AS Total FROM pages WHERE ipageId NOT IN('5','20','21','51') $ssql $eStatus_sql $ssql_hide_kot $safety_practice_sql";
$totalData = $obj->MySQLSelect($sql);
$total_results = $totalData[0]['Total'];
$total_pages = ceil($total_results / $per_page); //total pages we going to have
$show_page = 1;
$start = 0;
$end = $per_page;
//-------------if page is setcheck------------------//
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
$sql = "SELECT iPageId,vPageName,vTitle,eStatus,vPageTitle_" . $default_lang . ",tPageDesc_" . $default_lang . " FROM " . $tbl_name . " where ipageId NOT IN('5','20','21','51') $ssql $eStatus_sql $ssql_hide_kot $safety_practice_sql $ord LIMIT $start, $per_page ";
$data_drv = $obj->MySQLSelect($sql);
$endRecord = count($data_drv);
$var_filter = "";
foreach ($_REQUEST as $key => $val) {
    if ($key != "tpages" && $key != 'page') $var_filter .= "&$key=" . stripslashes($val);
}
$reload = $_SERVER['PHP_SELF'] . "?tpages=" . $tpages . $var_filter;
$cubexthemeon = 'No';
if ($THEME_OBJ->isXThemeActive() == 'Yes') {
    $cubexthemeon = 'Yes';
}
/*$tbl_name_earn = getAppTypeWiseHomeTable();
$earnBusinessDetailsquery = "SELECT learnServiceCatSection,lbusinessServiceCatSection FROM $tbl_name_earn where vCode='EN'";
$earnBusinessData = $obj->MySQLSelect($earnBusinessDetailsquery);*/
/*echo "<pre>";
print_r($earnBusinessData);
exit;*/
?>
<!DOCTYPE html>
<html lang="en">
<!-- BEGIN HEAD-->
<head>
    <meta charset="UTF-8"/>
    <title><?= $SITE_NAME ?> | <?php echo $langage_lbl_admin['LBL_PAGE_ADMIN']; ?></title>
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
                        <h2><?php echo $langage_lbl_admin['LBL_PAGE_ADMIN']; ?></h2>
                        <!--<input type="button" id="" value="ADD A DRIVER" class="add-btn">-->
                    </div>
                </div>
                <hr/>
            </div>
            <?php include('valid_msg.php'); ?>
            <?php if ($cacheRebuildButton > 0) { ?>
                <form name="rebuildcachefrm" id="rebuildcachefrm" method="post">
                    <input type="submit" name="rebuildcachebtn" class="add-btn" value="Rebuild Cache">
                </form>
            <?php } ?>
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
                                <option value="vPageName" <?php
                                if ($option == "vPageName") {
                                    echo "selected";
                                }
                                ?> >Name
                                </option>
                                <option value="<?= 'vPageTitle_' . $default_lang ?>" <?php
                                if ($option == "vPageTitle_.$default_lang") {
                                    echo "selected";
                                }
                                ?> >Page Title
                                </option>
                            </select>
                        </td>
                        <td width="15%">
                            <input type="Text" id="keyword" name="keyword" value="<?php echo $keyword; ?>"
                                   class="form-control"/>
                        </td>
                        <td width="12%" class="estatus_options" id="eStatus_options">
                            <select name="eStatus" id="estatus_value" class="form-control">
                                <option value="">Select Status</option>
                                <option value='Active' <?php
                                if ($eStatus == 'Active') {
                                    echo "selected";
                                }
                                ?> >Active
                                </option>
                                <option value="Inactive" <?php
                                if ($eStatus == 'Inactive') {
                                    echo "selected";
                                }
                                ?> >Inactive
                                </option>
                                <!-- <option value="Deleted" <?php
                                if ($eStatus == 'Deleted') {
                                    echo "selected";
                                }
                                ?> >Delete</option> -->
                            </select>
                        </td>
                        <td>
                            <input type="submit" value="Search" class="btnalt button11" id="Search" name="Search"
                                   title="Search"/>
                            <input type="button" value="Reset" class="btnalt button11"
                                   onClick="window.location.href = 'page.php'"/>
                        </td>
                        <td width="30%">
                            <!--<a class="add-btn" href="page_action.php" style="text-align: center;">Add Pages</a>--></td>
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
                                                <!--<select name="changeStatus" id="changeStatus" class="form-control" onchange="ChangeStatusAll(this.value);">
                                                        <option value="" >Select Action</option>
                                                        <option value='Active' <?php
                                                if ($option == 'Active') {
                                                    echo "selected";
                                                }
                                                ?> >Make Active</option>
                                                        <option value="Inactive" <?php
                                                if ($option == 'Inactive') {
                                                    echo "selected";
                                                }
                                                ?> >Make Inactive</option>
                                                        <option value="Deleted" <?php
                                                if ($option == 'Delete') {
                                                    echo "selected";
                                                }
                                                ?> >Make Delete</option>
                                                </select>-->
                                        </span>
                                    </div>
                                    <?php if (!empty($data_drv)) { ?>
                                        <!-- <div class="panel-heading">
                                            <form name="_export_form" id="_export_form" method="post" >
                                                <button type="button" onclick="showExportTypes('page')" >Export</button>
                                            </form>
                                        </div>-->
                                    <?php } ?>
                                </div>
                                <div style="clear:both;"></div>
                                <div class="table-responsive">
                                    <form class="_list_form" id="_list_form" method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>">
                                        <table class="table table-striped table-bordered table-hover">
                                            <thead>
                                                <tr>

					<th width="20%"><a href="javascript:void(0);" onClick="Redirect(1,<?php
                                                        if ($sortby == '1') {
                                                            echo $order;
                                                        } else {
                                                            ?>0<?php } ?>)">Name <?php if ($sortby == 1) {
                                                            if ($order == 0) {
                                                                ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php }
                                                } else {
                                                    ?>
                                                    <i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a>
                                        </th>
                                        <th width="20%">
                                            <a href="javascript:void(0);" onClick="Redirect(2,<?php
                                            if ($sortby == '2') {
                                                echo $order;
                                            } else {
                                                ?>0<?php } ?>)">Page Title <?php if ($sortby == 2) {
                                                    if ($order == 0) {
                                                        ?>
                                                        <i class="fa fa-sort-amount-asc"
                                                           aria-hidden="true"></i> <?php } else { ?>
                                                        <i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php }
                                                } else {
                                                    ?>
                                                    <i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a>
                                        </th>
                                        <th align="center" style="text-align:center;">Status</th>
                                        <th width="5%" align="center" style="text-align:center;">
                                            <!--<a href="javascript:void(0);" onClick="Redirect(4,<?php
                                            if ($sortby == '4') {
                                                echo $order;
                                            } else {
                                                ?>0<?php } ?>)"> <?php if ($sortby == 4) {
                                                if ($order == 0) {
                                                    ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php }
                                            } else {
                                                ?><i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a>-->
                                            Edit
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                   <!--  <?php if(ENABLE_EARN_PAGE == 'Yes'){?>
                                    <tr>
                                        <td>Earn</td>
                                        <td>Earn</td>
                                        <td style="text-align:center;">--</td>
                                        <td style="text-align:center;">
                                            <a href="<?php echo $tconfig["tsite_url_main_admin"] . "home_content_earn_action.php?id=1" ?> ">
                                                <img src="img/edit-icon.png" class="mCS_img_loaded">
                                            </a>
                                        </td>
                                    </tr>
                                    <?php } ?> -->
                                    <?php
                                    if (!empty($data_drv)) {
                                        for ($i = 0; $i < count($data_drv); $i++) {
                                            if ($cubexthemeon == 'No' && ($data_drv[$i]['iPageId'] == '48' || $data_drv[$i]['iPageId'] == '49' || $data_drv[$i]['iPageId'] == '50' || $data_drv[$i]['iPageId'] == '52')) continue;
                                            if ($cubexthemeon == 'Yes' && ($data_drv[$i]['iPageId'] == 1)) continue;
                                            $default = '';
                                            if ($data_drv[$i]['eDefault'] == 'Yes') {
                                                $default = 'disabled';
                                            }
                                            ?>
                                            <tr class="gradeA">
                                                <td><?= $data_drv[$i]['vPageName']; ?></td>
                                                <td><?php if ($cubexthemeon == 'Yes' && ($data_drv[$i]['iPageId'] == '48' || $data_drv[$i]['iPageId'] == '50')) {
                                                        echo $data_drv[$i]['vPageName'];
                                                    } else {
                                                        echo $data_drv[$i]['vPageTitle_' . $default_lang];
                                                    } ?></td>
                                                <td align="center" width="5%">
                                                    <?
                                                    if ($data_drv[$i]['eStatus'] == 'Active') {
                                                        $dis_img = "img/active-icon.png";
                                                    } else if ($data_drv[$i]['eStatus'] == 'Inactive') {
                                                        $dis_img = "img/inactive-icon.png";
                                                    } else if ($data_drv[$i]['eStatus'] == 'Deleted') {
                                                        $dis_img = "img/delete-icon.png";
                                                    }
                                                    ?>
                                                    <img src="<?= $dis_img; ?>" alt="<?= $data_drv[$i]['eStatus'] ?>"
                                                         data-toggle="tooltip"
                                                         title="<?php echo $data_drv[$i]['eStatus']; ?>">
                                                </td>
                                                <!-- <td align="center" style="text-align:center;" class="action-btn001">

                                                                <div class="share-button openHoverAction-class" style="display: block;">

                                                                    <label class="entypo-twitter" data-network="twitter" style="margin-top: -10px;"><a href="page_action.php?id=<?= $data_drv[$i]['iPageId']; ?>" data-toggle="tooltip" title="Edit">
                                                                            <img src="img/edit-icon.png" alt="Edit">
                                                                        </a></label>
                                                                    <div class="social show-moreOptions openPops_<?= $data_drv[$i]['iPageId']; ?>">
                                                                    </div>
                                                                </div>															
                                                            </td>-->
                                                <td align="center" style="text-align:center;" class="action-btn001">
                                                    <?php if (in_array($data_drv[$i]['iPageId'], [
                                                            22,
                                                            31,
                                                            46,
                                                            48,
                                                            49,
                                                            50,
                                                            53,
                                                            55,
                                                            56,
                                                            57
                                                        ]) || ($userObj->hasPermission('edit-pages') && !$userObj->hasPermission('update-status-pages'))) { ?>
                                                        <?php //if ($data_drv[$i]['iPageId'] != 22 && $data_drv[$i]['iPageId'] != 31 && $data_drv[$i]['iPageId'] != 46 && $data_drv[$i]['iPageId'] != 49 && $data_drv[$i]['iPageId'] != 48 && $data_drv[$i]['iPageId'] != 50 && $data_drv[$i]['iPageId'] != 53) { ?>
                                                        <?php if ($userObj->hasPermission('edit-pages')) { ?>
                                                            <a href="page_action.php?id=<?= $data_drv[$i]['iPageId']; ?>"
                                                               data-toggle="tooltip" title=""
                                                               data-original-title="Edit">
                                                                <img src="img/edit-icon.png" alt="Edit">
                                                            </a>
                                                        <?php } else { ?>
                                                            --
                                                        <?php } ?>
                                                    <? } else { ?>
                                                        <?php if ($userObj->hasPermission([
                                                            'edit-pages',
                                                            'update-status-pages'
                                                        ])) { ?>
                                                            <div class="share-button openHoverAction-class"
                                                                 style="display: block;">
                                                                <label class="entypo-export">
                                                                    <span><img src="images/settings-icon.png"
                                                                               alt=""></span>
                                                                </label>
                                                                <div class="social show-moreOptions for-five openPops_<?= $data_drv[$i]['iPageId']; ?>">
                                                                    <ul>
                                                                        <?php if ($userObj->hasPermission('edit-pages')) { ?>
                                                                            <li class="entypo-twitter"
                                                                                data-network="twitter">
                                                                                <a href="page_action.php?id=<?= $data_drv[$i]['iPageId']; ?>"
                                                                                   data-toggle="tooltip" title="Edit">
                                                                                    <img src="img/edit-icon.png"
                                                                                         alt="Edit">
                                                                                </a>
                                                                            </li>
                                                                        <?php }
                                                                        if ($userObj->hasPermission('update-status-pages')) { ?>
                                                                            <li class="entypo-facebook"
                                                                                data-network="facebook">
                                                                                <a href="javascript:void(0);"
                                                                                   onClick="changeStatus('<?php echo $data_drv[$i]['iPageId']; ?>', 'Inactive')"
                                                                                   data-toggle="tooltip"
                                                                                   title="Activate">
                                                                                    <img src="img/active-icon.png"
                                                                                         alt="<?php echo $data_drv[$i]['eStatus']; ?>">
                                                                                </a>
                                                                            </li>
                                                                            <li class="entypo-gplus"
                                                                                data-network="gplus">
                                                                                <a href="javascript:void(0);"
                                                                                   onClick="changeStatus('<?php echo $data_drv[$i]['iPageId']; ?>', 'Active')"
                                                                                   data-toggle="tooltip"
                                                                                   title="Deactivate">
                                                                                    <img src="img/inactive-icon.png"
                                                                                         alt="<?php echo $data_drv[$i]['eStatus']; ?>">
                                                                                </a>
                                                                            </li>
                                                                        <?php } ?>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        <?php } else { ?>
                                                            --
                                                        <? }
                                                    } ?>
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
                            <?php include('pagination_n.php'); ?>
                        </div>
                    </div> <!--TABLE-END-->
                </div>
            </div>
            <div class="admin-notes">
                <h4>Notes:</h4>
                <ul>
                    <li>
                        Page module will list all pages on this page.
                    </li>
                    <li>
                        Administrator can edit any page.
                    </li>
                    <!--<li>Administrator can export data in XLS or PDF format.</li>-->
                    <li>
                        The page status and display order will only work for website footer, not for the application.
                    </li>
                    <? if ($note_shown_safety == 1) { ?>
                        <li>
                            Safety Measure page would be applicable for the food and grocery only.
                        </li>
                    <? } ?>
                </ul>
            </div>
        </div>
    </div>
    <!--END PAGE CONTENT -->
</div>
<!--END MAIN WRAPPER -->
<form name="pageForm" id="pageForm" action="action/page.php" method="post">
    <input type="hidden" name="page" id="page" value="<?php echo $page; ?>">
    <input type="hidden" name="tpages" id="tpages" value="<?php echo $tpages; ?>">
    <input type="hidden" name="iPageId" id="iMainId01" value="">
    <input type="hidden" name="status" id="status01" value="">
    <input type="hidden" name="statusVal" id="statusVal" value="">
    <input type="hidden" name="option" value="<?php echo $option; ?>">
    <input type="hidden" name="keyword" value="<?php echo $keyword; ?>">
    <input type="hidden" name="eStatus" id="eStatus" value="<?php echo $eStatus; ?>">
    <input type="hidden" name="sortby" id="sortby" value="<?php echo $sortby; ?>">
    <input type="hidden" name="order" id="order" value="<?php echo $order; ?>">
    <input type="hidden" name="method" id="method" value="">
</form>
<?php
include_once('footer.php');
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