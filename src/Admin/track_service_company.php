<?php
include_once '../common.php';
if (!$userObj->hasPermission('view-company-trackservice')) {
    $userObj->redirect();
}
$script = 'TrackServiceCompany';
$eSystem = " AND  c.eSystem ='General'";
$sortby = $_REQUEST['sortby'] ?? 0;
$order = $_REQUEST['order'] ?? '';
$ord = ' ORDER BY c.iTrackServiceCompanyId DESC';
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
$cmp_ssql = '';
$dri_ssql = '';
$dri_ssql .= " AND (rd.vEmail != '' OR rd.vPhone != '')";
if (SITE_TYPE === 'Demo') {
    $dri_ssql .= " And rd.tRegistrationDate > '".WEEK_DATE."'";
}
$option = $_REQUEST['option'] ?? '';
$keyword = isset($_REQUEST['keyword']) ? stripslashes($_REQUEST['keyword']) : '';
$searchDate = $_REQUEST['searchDate'] ?? '';
$eStatus = $_REQUEST['eStatus'] ?? '';
$ssql = '';
if ('' !== $keyword) {
    $keyword_new = $keyword;
    $chracters = ['(', '+', ')'];
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
$per_page = $DISPLAY_RECORD_NUMBER;
if (!empty($eStatus)) {
    $eStatus_sql = '';
} else {
    $eStatus_sql = " AND eStatus != 'Deleted'";
}
$sql = "SELECT COUNT(iTrackServiceCompanyId) AS Total FROM track_service_company AS c WHERE 1 = 1 AND 1=1 {$eStatus_sql} {$ssql} {$cmp_ssql}";
$totalData = $obj->MySQLSelect($sql);
$total_results = $totalData[0]['Total'];
$total_pages = ceil($total_results / $per_page);
$show_page = 1;
$start = 0;
$end = $per_page;
if (isset($_GET['page'])) {
    $show_page = $_GET['page'];
    if ($show_page > 0 && $show_page <= $total_pages) {
        $start = ($show_page - 1) * $per_page;
        $end = $start + $per_page;
    }
}
$page = isset($_GET['page']) ? (int) ($_GET['page']) : 0;
$tpages = $total_pages;
if ($page <= 0) {
    $page = 1;
}
if (!empty($eStatus)) {
    $equery = '';
} else {
    $equery = " AND  c.eStatus != 'Deleted'";
}
$sql = "SELECT c.iTrackServiceCompanyId,  c.vCompany,c.vEmail,(SELECT count(rd.iDriverId) FROM register_driver AS rd WHERE rd.iTrackServiceCompanyId=c.iTrackServiceCompanyId AND rd.eStatus != 'Deleted' {$dri_ssql}) AS `count`, c.vCode,c.vPhone, c.eStatus FROM track_service_company AS c WHERE 1 = 1  {$equery} {$ssql} {$cmp_ssql} {$ord} LIMIT {$start}, {$per_page}";
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
<head>
    <meta charset="UTF-8"/>
    <title><?php echo $SITE_NAME; ?> | Track Service Company</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <?php include_once 'global_files.php'; ?>
</head>
<body class="padTop53 ">
<div id="wrap">
    <?php include_once 'header.php'; ?>
    <?php include_once 'left_menu.php'; ?>
    <div id="content">
        <div class="inner">
            <div id="add-hide-show-div">
                <div class="row">
                    <div class="col-lg-12">
                        <h2>Track Service Company</h2>
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
                            <label for="textfield"><strong>Search:</strong></label>
                        </td>
                        <td width="10%" class="padding-right10"><select name="option" id="option" class="form-control">
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
                            </select>
                        </td>
                        <td width="15%" class="searchform">
                            <input type="Text" id="keyword" name="keyword" value="<?php echo $keyword; ?>" class="form-control"/>
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
                            <input type="submit" value="Search" class="btnalt button11" id="Search" name="Search" title="Search"/>
                            <input type="button" value="Reset" class="btnalt button11" onClick="window.location.href = 'company.php'"/>
                        </td>
                        <?php if ($userObj->hasPermission('create-company-trackservice')) { ?>
                            <td width="30%">
                                <a class="add-btn" href="track_service_company_action.php" style="text-align: center;">
                                    Add Track Service Company
                                </a>
                            </td>
                        <?php } ?>
                    </tr>
                    </tbody>
                </table>
            </form>
            <div class="table-list">
                <div class="row">
                    <div class="col-lg-12">
                        <div style="clear:both;"></div>
                        <div class="table-responsive">
                            <form class="_list_form" id="_list_form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <th width="20%">
                                            <a href="javascript:void(0);" onClick="Redirect(1,<?php
            if ('1' === $sortby) {
                echo $order;
            } else {
                ?>0<?php } ?>)">Company Name<?php
                if (1 === $sortby) {
                    if (0 === $order) {
                        ?>
                                                        <i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php } else { ?>
                                                        <i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php
                                                        }
                } else {
                    ?><i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a>
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
                                                        <i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php } else { ?>
                                                        <i class="fa fa-sort-amount-desc" aria-hidden="true"></i>
                                                        <?php
                                                        }
} else {
    ?>  <i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a>
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
                                                        <i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php } else { ?>
                                                        <i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php
                                                        }
                                                } else {
                                                    ?><i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a>
                                        </th>
                                        <th width="15%">Mobile</th>
                                        <?php if (0 !== $doc_count) { ?>
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
                                                        <i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php } else { ?>
                                                        <i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php
                                                        }
                                                } else {
                                                    ?><i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a>
                                        </th>
                                        <th width="6%" align="center" style="text-align:center;">Action</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if (!empty($data_drv)) {
                                        for ($i = 0; $i < count($data_drv); ++$i) {
                                            ?>
                                            <tr class="gradeA">
 												<td> <?php if ($userObj->hasPermission('view-track-service-company')) { ?><a href="javascript:void(0);" onClick="show_track_company_details('<?php echo $data_drv[$i]['iTrackServiceCompanyId']; ?>')" style="text-decoration: underline;"><?php } ?><?php echo clearCmpName($data_drv[$i]['vCompany']); ?><?php if ($userObj->hasPermission('view-track-service-company')) { ?></a><?php } ?></td>
                                                 <?php if ($data_drv[$i]['count'] > 0) { ?>
                                                    <td align="center">
                                                        <a href="track_service_driver.php?iTrackServiceCompanyId=<?php echo $data_drv[$i]['iTrackServiceCompanyId']; ?>" target="_blank"><?php echo $data_drv[$i]['count']; ?></a>
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
                                                <?php if (0 !== $doc_count) { ?>
                                                    <td align="center">
                                                        <a href="company_document_action.php?id=<?php echo $data_drv[$i]['iTrackServiceCompanyId']; ?>&action=edit&user_type=tracking_company">
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
                                                    <img src="<?php echo $dis_img; ?>" alt="image" data-toggle="tooltip" title="<?php echo $data_drv[$i]['eStatus']; ?>">
                                                </td>
                                                <td align="center" style="text-align:center;" class="action-btn001">
                                                    <?php if (1 !== $data_drv[$i]['iCompanyId']) { ?>
                                                        <div class="share-button share-button4 openHoverAction-class" style="display:block;">
                                                            <label class="entypo-export"><span>
                                                                    <img src="images/settings-icon.png" alt="">
                                                                </span></label>
                                                            <div class="social show-moreOptions openPops_<?php echo $data_drv[$i]['iTrackServiceCompanyId']; ?>">
                                                                <ul>
                                                                    <li class="entypo-twitter" data-network="twitter">
                                                                        <a href="track_service_company_action.php?id=<?php echo $data_drv[$i]['iTrackServiceCompanyId']; ?>" data-toggle="tooltip" title="Edit">
                                                                            <img src="img/edit-icon.png" alt="Edit">
                                                                        </a>
                                                                    </li>
                                                                    <?php if ($userObj->hasPermission('update-status-company-trackservice')) { ?>
                                                                        <li class="entypo-facebook" data-network="facebook">
                                                                            <a href="javascript:void(0);" onClick="changeStatus('<?php echo $data_drv[$i]['iTrackServiceCompanyId']; ?>', 'Inactive')" data-toggle="tooltip" title="Activate">
                                                                                <img src="img/active-icon.png" alt="<?php echo $data_drv[$i]['eStatus']; ?>">
                                                                            </a>
                                                                        </li>
                                                                        <li class="entypo-gplus" data-network="gplus">
                                                                            <a href="javascript:void(0);" onClick="changeStatus('<?php echo $data_drv[$i]['iTrackServiceCompanyId']; ?>', 'Active')" data-toggle="tooltip" title="Deactivate">
                                                                                <img src="img/inactive-icon.png" alt="<?php echo $data_drv[$i]['eStatus']; ?>">
                                                                            </a>
                                                                        </li>
                                                                    <?php } ?>
                                                                    <?php if ('Deleted' !== $eStatus && $userObj->hasPermission('delete-company-trackservice')) { ?>
                                                                        <li class="entypo-gplus" data-network="gplus">
                                                                            <a href="javascript:void(0);" onClick="changeStatusDeletecd('<?php echo $data_drv[$i]['iTrackServiceCompanyId']; ?>')" data-toggle="tooltip" title="Delete">
                                                                                <img src="img/delete-icon.png" alt="Delete">
                                                                            </a>
                                                                        </li>
                                                                    <?php } ?>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    <?php } else { ?>
                                                        <a href="track_service_company_action.php?id=<?php echo $data_drv[$i]['iCompanyId']; ?>" data-toggle="tooltip" title="Edit">
                                                            <img src="img/edit-icon.png" alt="Edit">
                                                        </a>
                                                    <?php } ?>
                                                </td>
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
                    </div>
                </div>
            </div>
            <div class="admin-notes">
                <h4>Notes:</h4>
                <ul>
                    <li>Track Service Company module will list all Track Service Company on this page.</li>
                    <li>Admin can Activate , Deactivate , Delete any Track Service Company.</li>
                    <li>Default Track Service Company cannot be Activated , Deactivated or Deleted.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<form name="pageForm" id="pageForm" action="action/track_service_company.php" method="post">
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

<div class="modal fade" id="track_company_detail_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4>
					<i aria-hidden="true" class="fa fa-building-o" style="margin:2px 5px 0 2px;"></i>Track Service Company Details
					<button type="button" class="close" data-dismiss="modal">x</button>
				</h4>
			</div>
			<div class="modal-body" style="max-height: 450px;overflow: auto;">
				<div id="track_company_imageIcons" style="display:none">
					<div align="center">
						<img src="default.gif">
						<br/> <span>Retrieving details,please Wait...</span>
					</div>
				</div>
				<div id="track_comp_detail"></div>
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
        }
        else {
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
</html>