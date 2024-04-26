<?php
include_once '../common.php';
if (!$userObj->hasPermission('view-company')) {
    $userObj->redirect();
}

// print_r($_SESSION);
$script = 'Company';
$eSystem = " AND  c.eSystem ='General'";
// Start Sorting
$sortby = $_REQUEST['sortby'] ?? 0;
$order = $_REQUEST['order'] ?? '';
$ord = ' ORDER BY c.iCompanyId DESC';
if (1 === $sortby) {
    if (0 === $order) {
        $ord = ' ORDER BY c.vCompany ASC';
    } else {
        $ord = ' ORDER BY c.vCompany DESC';
    }
}
if (2 === $sortby) {
    if (0 === $order) {
        $ord = ' ORDER BY c.vEmail ASC';
    } else {
        $ord = ' ORDER BY c.vEmail DESC';
    }
}
if (3 === $sortby) {
    if (0 === $order) {
        $ord = ' ORDER BY `count` ASC';
    } else {
        $ord = ' ORDER BY `count` DESC';
    }
}
if (4 === $sortby) {
    if (0 === $order) {
        $ord = ' ORDER BY c.eStatus ASC';
    } else {
        $ord = ' ORDER BY c.eStatus DESC';
    }
}
// End Sorting
$cmp_ssql = '';
// if (SITE_TYPE == 'Demo') {
// $cmp_ssql = " And c.tRegistrationDate > '" . WEEK_DATE . "'";
// }
$dri_ssql = '';
$dri_ssql .= " AND (rd.vEmail != '' OR rd.vPhone != '')";
if (SITE_TYPE === 'Demo') {
    $dri_ssql .= " And rd.tRegistrationDate > '".WEEK_DATE."'";
}
// Start Search Parameters
$option = $_REQUEST['option'] ?? '';
$keyword = isset($_REQUEST['keyword']) ? stripslashes($_REQUEST['keyword']) : '';
$searchDate = $_REQUEST['searchDate'] ?? '';
$eStatus = $_REQUEST['eStatus'] ?? '';
$ssql = '';
if ('' !== $keyword) {
    $keyword_new = $keyword;
    $chracters = [
        '(',
        '+',
        ')',
    ];
    $removespacekeyword = preg_replace('/\s+/', '', $keyword);
    $keyword_new = trim(str_replace($chracters, '', $removespacekeyword));
    if (is_numeric($keyword_new)) {
        $keyword_new = $keyword_new;
    } else {
        $keyword_new = $keyword;
    }
    if ('' !== $option) {
        $option_new = $option;
        if ('MobileNumber' === $option) {
            $option_new = "CONCAT(c.vCode,'',c.vPhone)";
        }
        if ('' !== $eStatus) {
            $ssql .= ' AND '.stripslashes($option_new)." LIKE '%".clean($keyword_new)."%' AND c.eStatus = '".clean($eStatus)."'";
            if (SITE_TYPE === 'Demo') {
                $ssql .= ' AND '.stripslashes($option_new)." = '".clean($keyword_new)."' AND c.eStatus = '".clean($eStatus)."'";
            }
        } else {
            $ssql .= ' AND '.stripslashes($option_new)." LIKE '%".clean($keyword_new)."%'";
            if (SITE_TYPE === 'Demo') {
                $ssql .= ' AND '.stripslashes($option_new)." = '".clean($keyword_new)."'";
            }
        }
    } else {
        if ('' !== $eStatus) {
            $ssql .= " AND (c.vCompany LIKE '%".clean($keyword_new)."%' OR c.vEmail LIKE '%".clean($keyword_new)."%' OR (concat(c.vCode,'',c.vPhone) LIKE '%".clean($keyword_new)."%')) AND c.eStatus = '".clean($eStatus)."'";
            if (SITE_TYPE === 'Demo') {
                $ssql .= " AND (c.vCompany LIKE '%".clean($keyword_new)."%' OR c.vEmail LIKE '%".clean($keyword_new)."%' OR (concat(c.vCode,'',c.vPhone) = '".clean($keyword_new)."')) AND c.eStatus = '".clean($eStatus)."'";
            }
        } else {
            $ssql .= " AND (c.vCompany LIKE '%".clean($keyword_new)."%' OR c.vEmail LIKE '%".clean($keyword_new)."%' OR (concat(c.vCode,'',c.vPhone) LIKE '%".clean($keyword_new)."%'))";
            if (SITE_TYPE === 'Demo') {
                $ssql .= " AND (c.vCompany LIKE '%".clean($keyword_new)."%' OR c.vEmail LIKE '%".clean($keyword_new)."%' OR (concat(c.vCode,'',c.vPhone) = '".clean($keyword_new)."'))";
            }
        }
    }
} elseif ('' !== $eStatus && '' === $keyword) {
    $ssql .= " AND c.eStatus = '".clean($eStatus)."'";
}
// End Search Parameters
// Pagination Start
$per_page = $DISPLAY_RECORD_NUMBER; // number of results to show per page
if (!empty($eStatus)) {
    $eStatus_sql = '';
} else {
    $eStatus_sql = " AND eStatus != 'Deleted'";
}
$sql = "SELECT COUNT(iCompanyId) AS Total FROM company AS c WHERE 1 = 1 AND c.eBuyAnyService = 'No' {$eSystem} {$eStatus_sql} {$ssql} {$cmp_ssql}";
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
if (!empty($eStatus)) {
    $equery = '';
} else {
    $equery = " AND  c.eStatus != 'Deleted'";
}
$sql = "SELECT c.iCompanyId, c.vCompany, c.vEmail,(SELECT count(rd.iDriverId) FROM register_driver AS rd WHERE rd.iCompanyId=c.iCompanyId AND rd.eStatus != 'Deleted' {$dri_ssql}) AS `count`, c.vCode,c.vPhone, c.eStatus FROM company AS c WHERE 1 = 1 AND c.eBuyAnyService = 'No' {$eSystem} {$equery} {$ssql} {$cmp_ssql} {$ord} LIMIT {$start}, {$per_page}";
$data_drv = $obj->MySQLSelect($sql);
$endRecord = count($data_drv);
$doc_count_query = $obj->MySQLSelect("SELECT doc_masterid as total FROM `document_master` WHERE `doc_usertype` ='company' AND status = 'Active'");
$doc_count = count($doc_count_query);
$var_filter = '';
foreach ($_REQUEST as $key => $val) {
    if ('tpages' !== $key && 'page' !== $key) {
        $var_filter .= "&{$key}=".stripslashes($val);
    }
}
$reload = $_SERVER['PHP_SELF'].'?tpages='.$tpages.$var_filter;
$ufxService = $MODULES_OBJ->isUfxFeatureAvailable();
?>
<!DOCTYPE html>
<html lang="en">
<!-- BEGIN HEAD-->
<head>
    <meta charset="UTF-8"/>
    <title><?php echo $SITE_NAME; ?> | <?php echo $langage_lbl_admin['LBL_COMPANY_ADMIN_TXT']; ?></title>
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
        <div class="inner">
            <div id="add-hide-show-div">
                <div class="row">
                    <div class="col-lg-12">
                        <h2><?php echo $langage_lbl_admin['LBL_COMPANY_ADMIN_TXT']; ?></h2>
                    </div>
                </div>
                <hr/>
            </div>
            <?php include 'valid_msg.php'; ?>
            <form name="frmsearch" id="frmsearch" action="javascript:void(0);" method="post">
                <table width="100%" border="0" cellpadding="0" cellspacing="0" class="admin-nir-table">
                    <tbody>
                    <tr>
                        <td width="5%">
                            <label for="textfield">
                                <strong>Search:</strong>
                            </label>
                        </td>
                        <td width="10%" class="padding-right10">
                            <select name="option" id="option" class="form-control">
                                <option value="">All</option>
                                <option value="c.vCompany" <?php
                                if ('c.vCompany' === $option) {
                                    echo 'selected';
                                }
?> >Name
                                </option>
                                <option value="c.vEmail" <?php
if ('c.vEmail' === $option) {
    echo 'selected';
}
?> >E-mail
                                </option>
                                <option value="MobileNumber" <?php
if ('MobileNumber' === $option) {
    echo 'selected';
}
?> >Mobile
                                </option>
                                <!--<option value="c.eStatus" <?php
if ('c.eStatus' === $option) {
    echo 'selected';
}
?> >Status</option>-->
                            </select>
                        </td>
                        <td width="15%" class="searchform">
                            <input type="Text" id="keyword" name="keyword" value="<?php echo $keyword; ?>"
                                   class="form-control"/>
                        </td>
                        <td width="13%" class="estatus_options" id="eStatus_options">
                            <select name="eStatus" id="estatus_value" class="form-control">
                                <option value="">Select Status</option>
                                <option value='Active' <?php
if ('Active' === $eStatus) {
    echo 'selected';
}
?> >Active
                                </option>
                                <option value="Inactive" <?php
if ('Inactive' === $eStatus) {
    echo 'selected';
}
?> >Inactive
                                </option>
                                <option value="Deleted" <?php
if ('Deleted' === $eStatus) {
    echo 'selected';
}
?> >Delete
                                </option>
                            </select>
                        </td>
                        <td>
                            <input type="submit" value="Search" class="btnalt button11" id="Search" name="Search"
                                   title="Search"/>
                            <input type="button" value="Reset" class="btnalt button11"
                                   onClick="window.location.href = 'company.php'"/>
                        </td>
                        <?php if ($userObj->hasPermission('create-company')) { ?>
                            <td width="30%">
                                <a class="add-btn" href="company_action.php" style="text-align: center;">Add Company</a>
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
                            <!-- <div class="changeStatus col-lg-12 option-box-left">
                                        <span class="col-lg-2 new-select001">
                                    <?php if ($userObj->hasPermission([
        'update-status-company',
        'delete-company',
    ])) { ?>
                                                    <select name="changeStatus" id="changeStatus" class="form-control" onChange="ChangeStatusAll(this.value);">
                                                        <option value="" >Select Action</option>
                                        <?php if ($userObj->hasPermission('update-status-company')) { ?>
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
    <?php if ('Deleted' !== $eStatus && $userObj->hasPermission('delete-company')) { ?>
                                                                                                        <option value="Deleted" <?php
                                            if ('Delete' === $option) {
                                                echo 'selected';
                                            }
        ?> >Delete</option>
                                        <?php } ?>
                                                    </select>
<?php } ?>
                                        </span>
                                    </div> -->
                            <?php if (!empty($data_drv)) { ?>
                                <!--<div class="panel-heading">
                                    <form name="_export_form" id="_export_form" method="post" >
                                        <button type="button" onClick="showExportTypes('company')">Export</button>
                                    </form>
                                </div>-->
                            <?php } ?>
                        </div>
                        <div style="clear:both;"></div>
                        <div class="table-responsive">
                            <form class="_list_form" id="_list_form" method="post"
                                  action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <?php if ($userObj->hasPermission([
                    'update-status-company',
                    'delete-company',
                ])) { ?>
                                        <th align="center" width="3%" style="text-align:center;">
                                            <input type="checkbox" id="setAllCheck">
                                        </th>
                                        <?php }?>
                                        <th width="20%">
                                            <a href="javascript:void(0);" onClick="Redirect(1,<?php
                    if ('1' === $sortby) {
                        echo $order;
                    } else {
                        ?>0<?php } ?>)">Company Name<?php
                        if (1 === $sortby) {
                            if (0 === $order) {
                                ?>
                                                        <i class="fa fa-sort-amount-asc"
                                                           aria-hidden="true"></i> <?php } else { ?>
                                                        <i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php
                                                           }
                        } else {
                            ?>
                                                    <i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a>
                                        </th>
                                        <th width="6%" class='align-center'>
                                            <a href="javascript:void(0);" onClick="Redirect(3,<?php
                                            if ('3' === $sortby) {
                                                echo $order;
                                            } else {
                                                ?>0<?php } ?>)"><?php echo $langage_lbl_admin['LBL_DASHBOARD_DRIVERS_ADMIN']; ?><?php
if (3 === $sortby) {
    if (0 === $order) {
        ?>
                                                        <i class="fa fa-sort-amount-asc"
                                                           aria-hidden="true"></i> <?php } else { ?>
                                                        <i class="fa fa-sort-amount-desc" aria-hidden="true"></i>
                                                        <?php
                                                           }
} else {
    ?>
                                                    <i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a>
                                        </th>
                                        <th width="20%">
                                            <a href="javascript:void(0);" onClick="Redirect(2,<?php
                                            if ('2' === $sortby) {
                                                echo $order;
                                            } else {
                                                ?>0<?php } ?>)">Email <?php
                                                if (2 === $sortby) {
                                                    if (0 === $order) {
                                                        ?>
                                                        <i class="fa fa-sort-amount-asc"
                                                           aria-hidden="true"></i> <?php } else { ?>
                                                        <i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php
                                                           }
                                                } else {
                                                    ?>
                                                    <i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a>
                                        </th>
                                        <th width="15%">Mobile</th>
                                        <?php if (0 !== $doc_count && $userObj->hasPermission('manage-company-document')) { ?>
                                            <th width="8%" class='align-center'>View/Edit Documents</th>
                                        <?php } ?>
                                        <th width="6%" class='align-center' style="text-align:center;">
                                            <a href="javascript:void(0);" onClick="Redirect(4,<?php
                                            if ('4' === $sortby) {
                                                echo $order;
                                            } else {
                                                ?>0<?php } ?>)">Status <?php
                                                if (4 === $sortby) {
                                                    if (0 === $order) {
                                                        ?>
                                                        <i class="fa fa-sort-amount-asc"
                                                           aria-hidden="true"></i> <?php } else { ?>
                                                        <i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php
                                                           }
                                                } else {
                                                    ?>
                                                    <i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a>
                                        </th>
                                        <?php if ($userObj->hasPermission(['edit-company', 'update-status-company', 'delete-company'])) { ?>
                                        <th width="6%" align="center" style="text-align:center;">Action</th>
                                        <?php } ?>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if (!empty($data_drv)) {
                                        for ($i = 0; $i < count($data_drv); ++$i) {
                                            $default = '';
                                            if (1 === $data_drv[$i]['iCompanyId']) {
                                                $default = 'disabled';
                                            }
                                            ?>
                                            <tr class="gradeA">
                                                <?php if ($userObj->hasPermission([
                                                    'update-status-company',
                                                    'delete-company',
                                                ])) { ?>
                                                <td align="center" style="text-align:center;">
                                                    <input type="checkbox" id="checkbox"
                                                           name="checkbox[]" <?php echo $default; ?>
                                                           value="<?php echo $data_drv[$i]['iCompanyId']; ?>"/>&nbsp;
                                                </td>
                                                <?php } ?>
                                                <td>
                                                    <?php if ('Ride' === $APP_TYPE) { ?>
                                                        <a href="javascript:void(0);"
                                                           onClick="show_company_details('<?php echo $data_drv[$i]['iCompanyId']; ?>')"
                                                           style="text-decoration: underline;"><?php echo clearCmpName($data_drv[$i]['vCompany']); ?></a>
                                                    <?php } else { ?>
                                                         <a href="javascript:void(0);"
                                                           onClick="show_company_details('<?php echo $data_drv[$i]['iCompanyId']; ?>')"
                                                           style="text-decoration: underline;"><?php echo clearCmpName($data_drv[$i]['vCompany']); ?></a>
                                                    <?php } ?>
                                                </td>
                                                <?php if ($userObj->hasPermission('view-providers') && $data_drv[$i]['count'] > 0) { ?>
                                                    <td align="center">
                                                        <a href="driver.php?iCompanyId=<?php echo $data_drv[$i]['iCompanyId']; ?>"
                                                           target="_blank"><?php echo $data_drv[$i]['count']; ?></a>
                                                    </td>
                                                <?php } else { ?>
                                                    <td align="center"><?php echo $data_drv[$i]['count']; ?></td>
                                                <?php } ?>
                                                <td>
                                                    <?php if ('' !== $data_drv[$i]['vEmail']) { ?>
                                                        <?php echo clearEmail($data_drv[$i]['vEmail']); ?><?php } else {
                                                            echo '--';
                                                        } ?>
                                                </td>
                                                <td>
                                                    <?php if (!empty($data_drv[$i]['vPhone'])) { ?>
                                                        (+<?php echo $data_drv[$i]['vCode']; ?>) <?php echo clearPhone($data_drv[$i]['vPhone']); ?><?php } ?>
                                                </td>
                                                <?php if (0 !== $doc_count && $userObj->hasPermission('manage-company-document')) { ?>
                                                    <td align="center">
                                                        <a href="company_document_action.php?id=<?php echo $data_drv[$i]['iCompanyId']; ?>&action=edit">
                                                            <img src="img/edit-doc.png" alt="Edit Document">
                                                        </a>
                                                    </td>
                                                <?php } ?>
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
                                                    <img src="<?php echo $dis_img; ?>" alt="image" data-toggle="tooltip"
                                                         title="<?php echo $data_drv[$i]['eStatus']; ?>">
                                                </td>

                                                <?php if ($userObj->hasPermission(['edit-company', 'update-status-company', 'delete-company'])) { ?>


                                                <td align="center" style="text-align:center;" class="action-btn001">
                                                    <?php if (1 !== $data_drv[$i]['iCompanyId']) { ?>
                                                        <div class="share-button share-button4 openHoverAction-class"
                                                             style="display:block;">
                                                            <label class="entypo-export"><span>
                                                                    <img src="images/settings-icon.png" alt="">
                                                                </span>
                                                            </label>
                                                            <div class="social show-moreOptions openPops_<?php echo $data_drv[$i]['iCompanyId']; ?>">
                                                                <ul>
                                                                    <?php if ($userObj->hasPermission('edit-company')) { ?>
                                                                        <li class="entypo-twitter"
                                                                            data-network="twitter">
                                                                            <a href="company_action.php?id=<?php echo $data_drv[$i]['iCompanyId']; ?>"
                                                                               data-toggle="tooltip" title="Edit">
                                                                                <img src="img/edit-icon.png" alt="Edit">
                                                                            </a>
                                                                        </li>
                                                                    <?php } ?>
                                                                    <?php if ($userObj->hasPermission('update-status-company')) { ?>
                                                                        <li class="entypo-facebook"
                                                                            data-network="facebook">
                                                                            <a href="javascript:void(0);"
                                                                               onClick="changeStatus('<?php echo $data_drv[$i]['iCompanyId']; ?>', 'Inactive')"
                                                                               data-toggle="tooltip" title="Activate">
                                                                                <img src="img/active-icon.png"
                                                                                     alt="<?php echo $data_drv[$i]['eStatus']; ?>">
                                                                            </a>
                                                                        </li>
                                                                        <li class="entypo-gplus" data-network="gplus">
                                                                            <a href="javascript:void(0);"
                                                                               onClick="changeStatus('<?php echo $data_drv[$i]['iCompanyId']; ?>', 'Active')"
                                                                               data-toggle="tooltip" title="Deactivate">
                                                                                <img src="img/inactive-icon.png"
                                                                                     alt="<?php echo $data_drv[$i]['eStatus']; ?>">
                                                                            </a>
                                                                        </li>
                                                                    <?php } ?>
                                                                    <?php if ('Deleted' !== $eStatus && $userObj->hasPermission('delete-company')) { ?>
                                                                        <li class="entypo-gplus" data-network="gplus">
                                                                            <a href="javascript:void(0);"
                                                                               onClick="changeStatusDeletecd('<?php echo $data_drv[$i]['iCompanyId']; ?>')"
                                                                               data-toggle="tooltip" title="Delete">
                                                                                <img src="img/delete-icon.png"
                                                                                     alt="Delete">
                                                                            </a>
                                                                        </li>
                                                                    <?php } ?>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    <?php } else { ?>
                                                        <?php if ($userObj->hasPermission('edit-company')) { ?>
                                                            <a href="company_action.php?id=<?php echo $data_drv[$i]['iCompanyId']; ?>"
                                                               data-toggle="tooltip" title="Edit">
                                                                <img src="img/edit-icon.png" alt="Edit">
                                                            </a>
                                                        <?php } ?>
                                                    <?php } ?>
                                                </td>
                                                <?php } ?>
                                            </tr>
                                            <?php
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
                            <?php include 'pagination_n.php'; ?>
                        </div>
                    </div> <!--TABLE-END-->
                </div>
            </div>
            <div class="admin-notes">
                <h4>Notes:</h4>
                <ul>
                    <li>Company module will list all companies on this page.</li>
                    <li>Admin can Activate , Deactivate , Delete any companies.</li>
                    <li>Default company cannot be Activated , Deactivated or Deleted.</li>
                    <?php if ('YES' === strtoupper($ufxService)) { ?>
                        <li>This module will list all the companies registered
                            as <?php if ('Yes' !== $THEME_OBJ->isServiceXThemeActive() && 'Yes' !== $THEME_OBJ->isServiceXv2ThemeActive() && 'Yes' !== $THEME_OBJ->isProSPThemeActive()) { ?>a Taxi/Ride, Common Delivery, and<?php } ?>
                            Other Services.
                        </li>
                    <?php } else { ?>
                        <li>This module will list all the companies registered as a particular service.</li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </div>
    <!--END PAGE CONTENT -->
</div>
<!--END MAIN WRAPPER -->
<form name="pageForm" id="pageForm" action="action/company.php" method="post">
    <input type="hidden" name="page" id="page" value="<?php echo $page; ?>">
    <input type="hidden" name="tpages" id="tpages" value="<?php echo $tpages; ?>">
    <input type="hidden" name="iCompanyId" id="iMainId01" value="">
    <input type="hidden" name="eStatus" id="eStatus" value="<?php echo $eStatus; ?>">
    <input type="hidden" name="status" id="status01" value="">
    <input type="hidden" name="statusVal" id="statusVal" value="">
    <input type="hidden" name="option" value="<?php echo $option; ?>">
    <input type="hidden" name="keyword" value="<?php echo $keyword; ?>">
    <input type="hidden" name="sortby" id="sortby" value="<?php echo $sortby; ?>">
    <input type="hidden" name="order" id="order" value="<?php echo $order; ?>">
    <input type="hidden" name="method" id="method" value="">
</form>
<div class="modal fade" id="detail_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4>
                    <i aria-hidden="true" class="fa fa-building-o" style="margin:2px 5px 0 2px;"></i>
                    Company Details
                    <button type="button" class="close" data-dismiss="modal">x</button>
                </h4>
            </div>
            <div class="modal-body" style="max-height: 450px;overflow: auto;">
                <div id="imageIcons" style="display:none">
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

    function show_company_details(companyid) {
        $("#comp_detail").html('');
        $("#imageIcons").show();
        $("#detail_modal").modal('show');
        if (companyid != "") {
            var ajaxData = {
                'URL': '<?php echo $tconfig['tsite_url_main_admin']; ?>ajax_company_details.php',
                'AJAX_DATA': "iCompanyId=" + companyid,
                'REQUEST_DATA_TYPE': 'html'
            };
            getDataFromAjaxCall(ajaxData, function (response) {
                if (response.action == "1") {
                    var data = response.result;
                    $("#comp_detail").html(data);
                    $("#imageIcons").hide();
                } else {
                    console.log(response.result);
                    $("#imageIcons").hide();
                }
            });
        }
    }
</script>
</body>
<!-- END BODY-->
</html>