<?php
include_once('../common.php');

if (!$userObj->hasPermission('view-users')) {
    $userObj->redirect();
}
$script = 'Rider';
//Start Sorting
$sortby = isset($_REQUEST['sortby']) ? $_REQUEST['sortby'] : 0;
$order = isset($_REQUEST['order']) ? $_REQUEST['order'] : '';
$action = (isset($_REQUEST['action']) ? $_REQUEST['action'] : '');
$ord = ' ORDER BY iUserId DESC';
if ($sortby == 1) {
    if ($order == 0) $ord = " ORDER BY vName ASC"; else

        $ord = " ORDER BY vName DESC";
}
if ($sortby == 2) {
    if ($order == 0) $ord = " ORDER BY vEmail ASC"; else

        $ord = " ORDER BY vEmail DESC";
}
if ($sortby == 3) {
    if ($order == 0) $ord = " ORDER BY tRegistrationDate ASC"; else

        $ord = " ORDER BY tRegistrationDate DESC";
}
if ($sortby == 4) {
    if ($order == 0) $ord = " ORDER BY eStatus ASC"; else

        $ord = " ORDER BY eStatus DESC";
}
//End Sorting
$rdr_ssql = "";
if (SITE_TYPE == 'Demo') {
    $rdr_ssql = " And tRegistrationDate > '" . WEEK_DATE . "'";
}
// Start Search Parameters
$option = isset($_REQUEST['option']) ? stripslashes($_REQUEST['option']) : "";
$keyword = isset($_REQUEST['keyword']) ? stripslashes($_REQUEST['keyword']) : "";
$searchDate = isset($_REQUEST['searchDate']) ? $_REQUEST['searchDate'] : "";
$eStatus = isset($_REQUEST['eStatus']) ? $_REQUEST['eStatus'] : "";
$ssql = '';
if ($keyword != '') {
    $keyword_new = $keyword;
    $chracters = array(
        "(",
        "+",
        ")"
    );
    $removespacekeyword = preg_replace('/\s+/', '', $keyword);
    $keyword_new = trim(str_replace($chracters, "", $removespacekeyword));
    if (is_numeric($keyword_new)) {
        $keyword_new = $keyword_new;
    } else {
        $keyword_new = $keyword;
    }
    if ($option != '') {
        $option_new = $option;
        if ($option == 'RiderName') {
            $option_new = "CONCAT(vName,' ',vLastName)";
        }
        if ($option == 'MobileNumber') {
            $option_new = "CONCAT(vPhoneCode,'',vPhone)";
        }
        if ($eStatus != '') {
            $ssql .= " AND " . stripslashes($option_new) . " LIKE '%" . clean($keyword_new) . "%' AND eStatus = '" . clean($eStatus) . "'";
            if (SITE_TYPE == 'Demo') {
                $ssql .= " AND " . stripslashes($option_new) . " = '" . clean($keyword_new) . "' AND eStatus = '" . clean($eStatus) . "'";
            }
        } else {
            $ssql .= " AND " . stripslashes($option_new) . " LIKE '%" . clean($keyword_new) . "%'";
            if (SITE_TYPE == 'Demo') {
                $ssql .= " AND " . stripslashes($option_new) . " = '" . clean($keyword_new) . "'";
            }
        }
    } else {
        if ($eStatus != '') {
            $ssql .= " AND (concat(vName,' ',vLastName) LIKE '%" . clean($keyword_new) . "%' OR vEmail LIKE '%" . clean($keyword_new) . "%' OR (CONCAT(vPhoneCode,'',vPhone) LIKE '%" . clean($keyword_new) . "%')) AND eStatus = '" . clean($eStatus) . "'";
            if (SITE_TYPE == 'Demo') {
                $ssql .= " AND (concat(vName,' ',vLastName) LIKE '%" . clean($keyword_new) . "%' OR vEmail LIKE '%" . clean($keyword_new) . "%' OR (CONCAT(vPhoneCode,'',vPhone) = '" . clean($keyword_new) . "')) AND eStatus = '" . clean($eStatus) . "'";
            }
        } else {
            $ssql .= " AND (concat(vName,' ',vLastName) LIKE '%" . clean($keyword_new) . "%' OR vEmail LIKE '%" . clean($keyword_new) . "%' OR (CONCAT(vPhoneCode,'',vPhone) LIKE '%" . clean($keyword_new) . "%'))";
            if (SITE_TYPE == 'Demo') {
                $ssql .= " AND (concat(vName,' ',vLastName) LIKE '%" . clean($keyword_new) . "%' OR vEmail LIKE '%" . clean($keyword_new) . "%' OR (CONCAT(vPhoneCode,'',vPhone) = '" . clean($keyword_new) . "'))";
            }
        }
    }
} else if ($eStatus != '' && $keyword == '') {
    $ssql .= " AND eStatus = '" . clean($eStatus) . "'";
}
$ssql1 = "AND (vEmail != '' OR vPhone != '') AND eHail='No'";
if (isset($_POST['action']) && $_POST['action'] == "addmoney") {
    if (SITE_TYPE == 'Demo') {
        $_SESSION['success'] = 2;
        //header("Location:" . $tconfig["tsite_url_main_admin"] . "rider.php?" . $parameters);
        header("Location:" . $tconfig["tsite_url_main_admin"] . $LOCATION_FILE_ARRAY['RIDER.PHP']."?" . $parameters);
        exit;
    }
    if (isset($_REQUEST['iBalance']) && $_REQUEST['iBalance'] > 0) {
        $eUserType = $_REQUEST['eUserType'];
        $iUserId = $_REQUEST['iUserId-id'];
        $iBalance = $_REQUEST['iBalance'];
        $eFor = $_REQUEST['eFor'];
        $eType = $_REQUEST['eType'];
        $iTripId = 0;
        $tDescription = '#LBL_AMOUNT_CREDIT_BY_ADMIN#';
        $ePaymentStatus = 'Unsettelled';
        $dDate = Date('Y-m-d H:i:s');
        $WALLET_OBJ->PerformWalletTransaction($iUserId, $eUserType, $iBalance, $eType, $iTripId, $eFor, $tDescription, $ePaymentStatus, $dDate);
        $_SESSION['success'] = '1';
        $_SESSION['var_msg'] = "Amount has been successfully added to the " . $langage_lbl_admin["LBL_RIDER_NAME_TXT_ADMIN"] . "'s wallet.";
        header("Location:" . $tconfig["tsite_url_main_admin"] . $LOCATION_FILE_ARRAY['RIDER.PHP']);
        exit;
        //exit;
    }
}
// End Search Parameters
//Pagination Start
$per_page = $DISPLAY_RECORD_NUMBER; // number of results to show per page
if ($eStatus != '') {
    $estatusquery = "";
} else {
    $estatusquery = " AND eStatus != 'Deleted'";
}
$sql = "SELECT COUNT(iUserId) AS Total FROM register_user WHERE 1=1 $estatusquery $ssql $ssql1 $rdr_ssql";
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
if (!empty($eStatus)) {
    $esql = "";
} else {
    $esql = " AND eStatus != 'Deleted'";
}
$sql = "SELECT iUserId,CONCAT(vName,' ',vLastName) AS name, vEmail, vPhone AS mobile,vPhoneCode,tRegistrationDate,eStatus FROM register_user WHERE 1=1 $esql $ssql $ssql1 $rdr_ssql $ord LIMIT $start, $per_page";
$data_drv = $obj->MySQLSelect($sql);
$endRecord = count($data_drv);
$var_filter = "";
foreach ($_REQUEST as $key => $val) {
    if ($key != "tpages" && $key != 'page') $var_filter .= "&$key=" . stripslashes($val);
}
$reload = $_SERVER['PHP_SELF'] . "?tpages=" . $tpages . $var_filter;
?>
<!DOCTYPE html>
<html lang="en">
<!-- BEGIN HEAD-->
<head>
    <meta charset="UTF-8"/>
    <title><?= $SITE_NAME ?> | <?php echo $langage_lbl_admin['LBL_EDIT_RIDERS_TXT_ADMIN']; ?></title>
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
                        <h2><?php echo $langage_lbl_admin['LBL_EDIT_RIDERS_TXT_ADMIN']; ?></h2>
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
                                <option value="RiderName" <?php
                                if ($option == "RiderName") {
                                    echo "selected";
                                }
                                ?> >Name
                                </option>
                                <option value="vEmail" <?php
                                if ($option == 'vEmail') {
                                    echo "selected";
                                }
                                ?> >E-mail
                                </option>
                                <option value="MobileNumber" <?php
                                if ($option == 'MobileNumber') {
                                    echo "selected";
                                }
                                ?> >Mobile
                                </option>
                                <!-- <option value="eStatus" <?php
                                if ($option == 'eStatus') {
                                    echo "selected";
                                }
                                ?> >Status</option> -->
                            </select>
                        </td>
                        <td width="15%" class="searchform">
                            <input type="Text" id="keyword" name="keyword"
                                   value="<?php echo $keyword; ?>" class="form-control"/>
                        </td>
                        <td width="13%" class="estatus_options" id="eStatus_options">
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
                                <option value="Deleted" <?php
                                if ($eStatus == 'Deleted') {
                                    echo "selected";
                                }
                                ?> >Delete
                                </option>
                            </select>
                        </td>
                        <td>
                            <input type="submit" value="Search" class="btnalt button11" id="Search" name="Search"
                                   title="Search"/>
                            <input type="button" value="Reset" class="btnalt button11"
                                   onClick="window.location.href = '<?= $LOCATION_FILE_ARRAY['RIDER.PHP']; ?>'"/>
                        </td>
                        <?php if ($userObj->hasPermission('create-users')) { ?>
                            <td width="30%">
                                <a class="add-btn" href="<?= $LOCATION_FILE_ARRAY['RIDER_ACTION']; ?>"
                                   style="text-align: center;">
                                    Add <?= $langage_lbl_admin['LBL_RIDER_NAME_TXT_ADMIN']; ?></a>
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

                                                <?php if ($userObj->hasPermission([
                                                    'update-status-users',
                                                    'delete-users'
                                                ]) && $eStatus != 'Deleted') { ?>
                                                    <select name="changeStatus" id="changeStatus" class="form-control"
                                                            onChange="ChangeStatusAll(this.value);">

                                                    <option value="">Select Action</option>

                                                <?php if ($userObj->hasPermission('update-status-users')) { ?>
                                                    <option value='Active' <?php
                                                    if ($option == 'Active') {
                                                        echo "selected";
                                                    }
                                                    ?> >Activate</option>
                                                    <option value="Inactive" <?php
                                                    if ($option == 'Inactive') {
                                                        echo "selected";
                                                    }
                                                    ?> >Deactivate</option>
                                                <?php } ?>

                                                        <?php if ($eStatus != 'Deleted' && $userObj->hasPermission('delete-users')) { ?>
                                                            <option value="Deleted" <?php
                                                            if ($option == 'Delete') {
                                                                echo "selected";
                                                            }
                                                            ?> >Delete</option>
                                                        <?php } ?>

                                                </select>
                                                <?php } ?>

                                        </span>
                            </div>
                            <?php if (!empty($data_drv) && $userObj->hasPermission('export-users')) { ?>
                                <div class="panel-heading">
                                    <form name="_export_form" id="_export_form" method="post">
                                        <button type="button" onClick="showExportTypes('rider')">Export</button>
                                    </form>
                                </div>
                            <?php } ?>
                        </div>
                        <div style="clear:both;"></div>
                        <div class="table-responsive">
                            <form class="_list_form" id="_list_form" method="post"
                                  action="<?php echo $_SERVER['PHP_SELF'] ?>">
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <?php if ($userObj->hasPermission([
                                            'update-status-users',
                                            'delete-users'
                                        ]) && $eStatus != 'Deleted') { ?>
                                        <th align="center" width="3%" style="text-align:center;">
                                            <input type="checkbox"
                                                   id="setAllCheck">
                                        </th>
                                        <?php } ?>
					 <th width="18%">
					 <a href="javascript:void(0);" onClick="Redirect(1,<?php

                                            if ($sortby == '1') {
                                                echo $order;
                                            } else {
                                                ?>0<?php } ?>)"><?= $langage_lbl_admin['LBL_RIDER_NAME_TXT_ADMIN']; ?>
                                                Name <?php if ($sortby == 1) {
                                                    if ($order == 0) {
                                                        ?>
                                                        <i class="fa fa-sort-amount-asc"
                                                           aria-hidden="true"></i> <?php } else { ?>
                                                        <i
                                                                class="fa fa-sort-amount-desc"
                                                                aria-hidden="true"></i><?php }
                                                } else {
                                                    ?>
                                                    <i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a>
                                        </th>
                                        <th width="22%">
                                            <a href="javascript:void(0);" onClick="Redirect(2,<?php
                                            if ($sortby == '2') {
                                                echo $order;
                                            } else {
                                                ?>0<?php } ?>)">Email <?php if ($sortby == 2) {
                                                    if ($order == 0) {
                                                        ?>
                                                        <i class="fa fa-sort-amount-asc"
                                                           aria-hidden="true"></i> <?php } else { ?>
                                                        <i
                                                                class="fa fa-sort-amount-desc"
                                                                aria-hidden="true"></i><?php }
                                                } else {
						 ?>
						 <i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a>
					</th>
					 <th width="13%">
					 <a href="javascript:void(0);" onClick="Redirect(3,<?php

                                            if ($sortby == '3') {
                                                echo $order;
                                            } else {
                                                ?>0<?php } ?>)">Sign Up Date <?php if ($sortby == 3) {
                                                    if ($order == 0) {
                                                        ?>
                                                        <i class="fa fa-sort-amount-asc"
                                                           aria-hidden="true"></i> <?php } else { ?>
                                                        <i
                                                                class="fa fa-sort-amount-desc"
                                                                aria-hidden="true"></i><?php }
                                                } else {
                                                    ?>
                                                    <i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a>
                                        </th>
                                        <th width="14%">Mobile</th>
                                        <th width="15%" class="align-center">Wallet Balance</th>
                                        <th width="8%" align="center" style="text-align:center;">
                                            <a
                                                    href="javascript:void(0);" onClick="Redirect(4,<?php
                                            if ($sortby == '4') {
                                                echo $order;
                                            } else {
                                                ?>0<?php } ?>)">Status <?php if ($sortby == 4) {
                                                    if ($order == 0) {
                                                        ?>
                                                        <i class="fa fa-sort-amount-asc"
                                                           aria-hidden="true"></i> <?php } else { ?>
                                                        <i
                                                                class="fa fa-sort-amount-desc"
                                                                aria-hidden="true"></i><?php }
                                                } else {
                                                    ?>
                                                    <i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a>
                                        </th>
                                        <?php if ($userObj->hasPermission(['edit-users','update-status-users','delete-users'])) { ?>
                                        <th width="8%" align="center" style="text-align:center;">Action</th>
                                        <?php } ?>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if (!empty($data_drv)) {
                                        for ($i = 0; $i < count($data_drv); $i++) {
                                            ?>
                                            <tr class="gradeA">
                                                <?php if ($userObj->hasPermission([
                                                    'update-status-users',
                                                    'delete-users'
                                                ]) && $eStatus != 'Deleted') { ?>
                                                <td align="center" style="text-align:center;">
                                                    <input type="checkbox" id="checkbox"
                                                           name="checkbox[]"
                                                           value="<?php echo $data_drv[$i]['iUserId']; ?>"/>&nbsp;
                                                </td>
                                                <?php } ?>
                                                <td>
                                                    <a href="javascript:void(0);"
                                                       onClick="show_rider_details('<?= $data_drv[$i]['iUserId']; ?>')"
                                                       style="text-decoration: underline;"><?= clearName($data_drv[$i]['name']); ?></a>
                                                </td>
                                                <? if ($data_drv[$i]['vEmail'] != '') { ?>
                                                    <td><? echo clearEmail($data_drv[$i]['vEmail']); ?></td>
                                                <? } else { ?>
                                                    <td>--</td>
                                                <? } ?>
                                                <td data-order="<?= $data_drv[$i]['iUserId']; ?>"><? echo DateTime($data_drv[$i]['tRegistrationDate'],7) ?></td>
                                                <td>
                                                    <?php if (!empty($data_drv[$i]['mobile'])) { ?>
                                                        (+<?= $data_drv[$i]['vPhoneCode'] ?>) <?= clearPhone($data_drv[$i]['mobile']); ?>
                                                    <?php } ?>
                                                </td>
                                                <td align="center">
                                                    <?php
                                                    $user_available_balance = $WALLET_OBJ->FetchMemberWalletBalance($data_drv[$i]['iUserId'], "Rider");
                                                    if ($data_drv[$i]['eStatus'] != "Deleted") {
                                                        echo formateNumAsPerCurrency($user_available_balance, "");
                                                        ?></br>
                                                        <?php if ($userObj->hasPermission('add-wallet-balance')) { ?>
                                                            <button type="button"
                                                                    onClick="Add_money_driver('<?= $data_drv[$i]['iUserId']; ?>')"
                                                                    class="btn btn-success btn-xs">Add Balance
                                                            </button>
                                                        <?php } ?>
                                                        <?php
                                                    } else {
                                                        echo formateNumAsPerCurrency($user_available_balance, "");
                                                    }
                                                    ?>
                                                </td>
                                                <td width="10%" align="center">
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
                                                <?php if ($userObj->hasPermission(['edit-users','update-status-users','delete-users'])) { ?>


                                                <td align="center" style="text-align:center;" class="action-btn001">
                                                    <div class="share-button openHoverAction-class"
                                                         style="display: block;">
                                                        <label class="entypo-export"><span><img
                                                                        src="images/settings-icon.png"
                                                                        alt=""></span>
                                                        </label>
                                                        <div class="social show-moreOptions for-five openPops_<?= $data_drv[$i]['iUserId']; ?>">
                                                            <ul>

                                                                <?php if ($userObj->hasPermission('edit-users')) { ?>
                                                                <li class="entypo-twitter" data-network="twitter">
                                                                    <a
                                                                            href="<?= $LOCATION_FILE_ARRAY['RIDER_ACTION']; ?>?id=<?= $data_drv[$i]['iUserId']; ?>"
                                                                            data-toggle="tooltip" title="Edit">
                                                                        <img src="img/edit-icon.png" alt="Edit">
                                                                    </a>
                                                                </li>
                                                                <?php } ?>
                                                                <?php if ($userObj->hasPermission('update-status-users')) { ?>
                                                                    <li class="entypo-facebook" data-network="facebook">
                                                                        <a href="javascript:void(0);"
                                                                           onClick="changeStatus('<?php echo $data_drv[$i]['iUserId']; ?>', 'Inactive')"
                                                                           data-toggle="tooltip" title="Activate">
                                                                            <img src="img/active-icon.png"
                                                                                 alt="<?php echo $data_drv[$i]['eStatus']; ?>">
                                                                        </a>
                                                                    </li>
                                                                    <li class="entypo-gplus" data-network="gplus">
                                                                        <a
                                                                                href="javascript:void(0);"
                                                                                onClick="changeStatus('<?php echo $data_drv[$i]['iUserId']; ?>', 'Active')"
                                                                                data-toggle="tooltip"
                                                                                title="Deactivate">
                                                                            <img src="img/inactive-icon.png"
                                                                                 alt="<?php echo $data_drv[$i]['eStatus']; ?>">
                                                                        </a>
                                                                    </li>
                                                                <?php } ?>

                                                                <?php if ($eStatus != 'Deleted' && $userObj->hasPermission('delete-users')) { ?>
                                                                    <li class="entypo-gplus" data-network="gplus">
                                                                        <a
                                                                                href="javascript:void(0);"
                                                                                onClick="changeStatusDelete('<?php echo $data_drv[$i]['iUserId']; ?>')"
                                                                                data-toggle="tooltip" title="Delete">
                                                                            <img src="img/delete-icon.png" alt="Delete">
                                                                        </a>
                                                                    </li>
                                                                <?php } ?>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </td>

                                                <?php  } ?>
                                            </tr>
                                            <?
                                        }
                                    } else {
                                        ?>
                                        <tr class="gradeA">
                                            <td colspan="8"> No Records Found.</td>
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
                        <?php echo $langage_lbl_admin['LBL_EDIT_RIDERS_TXT_ADMIN']; ?> module will list
                        all <?php echo $langage_lbl_admin['LBL_EDIT_RIDERS_TXT_ADMIN']; ?> on this page.
                    </li>
                    <li>
                        Administrator can Activate / Deactivate / Delete
                        any <?php echo $langage_lbl_admin['LBL_EDIT_RIDERS_TXT_ADMIN']; ?>
                    </li>
                    <li>
                        Administrator can export data in XLS format.
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <!--END PAGE CONTENT -->
</div>
<!--END MAIN WRAPPER -->
<form name="pageForm" id="pageForm" action="action/rider.php" method="post">
    <input type="hidden" name="page" id="page" value="<?php echo $page; ?>">
    <input type="hidden" name="tpages" id="tpages" value="<?php echo $tpages; ?>">
    <input type="hidden" name="iUserId" id="iMainId01" value="">
    <input type="hidden" name="eStatus" id="eStatus" value="<?php echo $eStatus; ?>">
    <input type="hidden" name="status" id="status01" value="">
    <input type="hidden" name="statusVal" id="statusVal" value="">
    <input type="hidden" name="option" value="<?php echo $option; ?>">
    <input type="hidden" name="keyword" value="<?php echo $keyword; ?>">
    <input type="hidden" name="sortby" id="sortby" value="<?php echo $sortby; ?>">
    <input type="hidden" name="order" id="order" value="<?php echo $order; ?>">
    <input type="hidden" name="method" id="method" value="">
</form>

<div class="modal fade " id="detail_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4>
                    <!--<i aria-hidden="true" class="fa fa-building-o" style="margin:2px 5px 0 2px;"></i>-->
                    <i style="margin:2px 5px 0 2px;">
                        <img src="images/rider-icon.png" alt="">
                    </i>
                    <?php echo $langage_lbl_admin['LBL_RIDER']; ?> Details
                    <button type="button" class="close" data-dismiss="modal">x</button>
                </h4>
            </div>
            <div class="modal-body" style="max-height: 450px;overflow: auto;">
                <div id="imageIcons">
                    <div align="center">
                        <img src="default.gif">
                        <br/>
                        <span>Retrieving details,please Wait...</span>
                    </div>
                </div>
                <div id="rider_detail"></div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="rider_add_wallet_money" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content nimot-class">
            <div class="modal-header">
                <h4>
                    <i style="margin:2px 5px 0 2px;" class="fa fa-google-wallet"></i>
                    Add Balance
                    <button type="button" class="close" data-dismiss="modal">x</button>
                </h4>
            </div>
            <form class="form-horizontal" id="add_money_frm" method="POST" enctype="multipart/form-data" action=""
                  name="add_money_frm">
                <input type="hidden" id="action" name="action" value="addmoney">
                <input type="hidden" name="eTransRequest" id="eTransRequest" value="">
                <input type="hidden" name="eType" id="eType" value="Credit">
                <input type="hidden" name="eFor" id="eFor" value="Deposit">
                <input type="hidden" name="iUserId-id" id="iRider-Id" value="">
                <input type="hidden" name="eUserType" id="eUserType" value="Rider">
                <div class="col-lg-12">
                    <div class="input-group input-append">
                        <h5><?= $langage_lbl_admin['LBL_ADD_WALLET_DESC1_TXT']; ?></h5>
                        <div class="ddtt">
                            <h4><?= $langage_lbl_admin['LBL_ENTER_AMOUNT']; ?></h4>
                            <input type="text" name="iBalance" id="iBalance" class="form-control iBalance add-ibalance"
                                   onKeyup="checkzero(this.value);">
                        </div>
                        <div id="iLimitmsg"></div>
                    </div>
                </div>
                <div class="nimot-class-but">
                    <input type="button" onClick="check_add_money();" class="save" id="add_money" value="<?= $langage_lbl_admin['LBL_Save']; ?>">
                    <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal">Close</button>
                </div>
            </form>
            <div style="clear:both;"></div>
        </div>
    </div>
</div>
<?php include_once('footer.php'); ?>
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

        //alert(action);

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



    function Add_money_driver(riderid) {

        $("#rider_add_wallet_money").modal('show');

        $(".add-ibalance").val("");

        if (riderid != "") {

            var riderid = $('#iRider-Id').val(riderid);


        }

    }


    function changeOrder(iAdminId) {

        $('#is_dltSngl_modal').modal('show');

        $(".action_modal_submit").unbind().click(function () {

            var action = $("#pageForm").attr('action');

            var page = $("#pageId").val();

            $("#pageId01").val(page);

            $("#iMainId01").val(iAdminId);

            $("#method").val('delete');

            var formValus = $("#pageForm").serialize();

            window.location.href = action + "?" + formValus;

        });

    }


    function check_add_money() {


        var iBalance = $(".add-ibalance").val();

        if (iBalance == '') {

            alert("Please Enter Amount");

            return false;

        } else if (iBalance == 0) {

            alert("You Can Not Enter Zero Number");

            return false;

        } else {

            $("#add_money").val('Please wait ...').attr('disabled', 'disabled');

            $('#add_money_frm').submit();

        }

    }


    $(".iBalance").keydown(function (e) {

        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||

            (e.keyCode == 65 && e.ctrlKey === true) ||

            (e.keyCode == 67 && e.ctrlKey === true) ||

            (e.keyCode == 88 && e.ctrlKey === true) ||

            (e.keyCode >= 35 && e.keyCode <= 39)) {

            return;

        }

        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {

            e.preventDefault();

        }

    });


    function checkzero(userlimit) {

        if (userlimit != "") {

            if (userlimit == 0) {

                $('#iLimitmsg').html('<span class="red">You Can Not Enter Zero Number</span>');

            } else if (userlimit <= 0) {

                $('#iLimitmsg').html('<span class="red">You Can Not Enter Negative Number</span>');

            } else {

                $('#iLimitmsg').html('');

            }

        } else {

            $('#iLimitmsg').html('');

        }


    }
    
    function show_rider_details(userid) {

        $("#rider_detail").html('');

        $("#imageIcons").show();

        $("#detail_modal").modal('show');



        if (userid != "") {


            var ajaxData = {

                'URL': '<?= $tconfig['tsite_url_main_admin'] ?>ajax_rider_details.php',

                'AJAX_DATA': "iUserId=" + userid,

                'REQUEST_DATA_TYPE': 'html'

            };

            getDataFromAjaxCall(ajaxData, function (response) {

                if (response.action == "1") {

                    var data = response.result;

                    $("#rider_detail").html(data);

                    $("#imageIcons").hide();

                } else {

                    console.log(response.result);

                    $("#detail_modal").modal('hide');

                }

            });

        }

    }


</script>
</body>
<!-- END BODY-->
</html>

