<?php
include_once '../common.php';

if (!$userObj->hasPermission('view-rating-feedback-ques')) {
    $userObj->redirect();
}
$default_lang = $LANG_OBJ->FetchSystemDefaultLang();
// get make
$tbl_name = 'rating_feedback_questions';
$script = 'RatingFeedbackQuestions';
// Start Sorting
$sortby = $_REQUEST['sortby'] ?? 0;
$order = $_REQUEST['order'] ?? '';
$ord = ' ORDER BY iDisplayOrder';
if (1 === $sortby) {
    if (0 === $order) {
        $ord = ' ORDER BY tQuestion ASC';
    } else {
        $ord = ' ORDER BY tQuestion DESC';
    }
}

if (2 === $sortby) {
    if (0 === $order) {
        $ord = ' ORDER BY iDisplayOrder ASC';
    } else {
        $ord = ' ORDER BY iDisplayOrder DESC';
    }
}

if (3 === $sortby) {
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
$ssql = '';
if ('' !== $keyword) {
    if ('' !== $option) {
        if ('eStatus' === $option) {
            $ssql .= ' AND '.stripslashes($option)." LIKE '".stripslashes($keyword)."'";
        } else {
            $ssql .= ' AND '.stripslashes($option)." LIKE '%".stripslashes($keyword)."%'";
        }
    } else {
        $ssql .= " AND (tQuestion LIKE '%".$keyword."%' OR eStatus LIKE '%".$keyword."%')";
    }
}
// End Search Parameters
// Pagination Start
$per_page = $DISPLAY_RECORD_NUMBER; // number of results to show per page
$sql = "SELECT COUNT(iFeedbackId) AS Total FROM {$tbl_name} WHERE eStatus != 'Deleted' {$ssql}";
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

$data_drv = $obj->MySQLSelect("SELECT iFeedbackId, JSON_UNQUOTE(JSON_EXTRACT(tQuestion, '$.tQuestion_".$default_lang."')) as tQuestion, eStatus, iDisplayOrder FROM {$tbl_name} WHERE eStatus != 'Deleted' {$ssql} {$ord}");
// echo "<pre>";print_r($data_drv);die;
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
        <title><?php echo $SITE_NAME; ?> | Rating Feedback Questions</title>
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
                                <h2>Rating Feedback Questions</h2>
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
                                    <td width="15%" class=" padding-right10"><select name="option" id="option" class="form-control">
                                            <option value="">All</option>
                                            <option value="tQuestion" <?php
                                            if ('tQuestion' === $option) {
                                                echo 'selected';
                                            }
?> >Feedback Question</option>

                                            <option value="eStatus" <?php
if ('eStatus' === $option) {
    echo 'selected';
}
?> >Status</option>
                                        </select>
                                    </td>
                                    <td width="15%"><input type="Text" id="keyword" name="keyword" value="<?php echo $keyword; ?>"  class="form-control" /></td>
                                    <td>
                                        <input type="submit" value="Search" class="btnalt button11" id="Search" name="Search" title="Search" />
                                        <input type="button" value="Reset" class="btnalt button11" onClick="window.location.href = 'rating_feedback_ques.php'"/>
                                    </td>
                                    <?php if ($userObj->hasPermission('create-rating-feedback-ques')) { ?>
                                    <td width="30%"><a class="add-btn" href="rating_feedback_ques_action.php" style="text-align: center;">Add Question</a></td>
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
                                            <?php if ($userObj->hasPermission(['update-status-rating-feedback-ques', 'delete-rating-feedback-ques'])) { ?>
                                            <select name="changeStatus" id="changeStatus" class="form-control" onchange="ChangeStatusAll(this.value);">
                                                <option value="" >Select Action</option>
                                                    <?php if ($userObj->hasPermission('update-status-rating-feedback-ques')) { ?>
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
                                                <?php if ('Deleted' !== $eStatus && $userObj->hasPermission('delete-rating-feedback-ques')) { ?>
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
                                </div>
                                <div style="clear:both;"></div>
                                <div class="table-responsive">
                                    <form class="_list_form" id="_list_form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                        <table class="table table-striped table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <?php if ($userObj->hasPermission(['update-status-rating-feedback-ques', 'delete-rating-feedback-ques'])) { ?>
                                                    <th align="center" width="3%" style="text-align:center;"><input type="checkbox" id="setAllCheck" ></th>
                                                    <?php } ?>
                                                    <th width="20%"><a href="javascript:void(0);" onClick="Redirect(1,<?php
                                                            if ('1' === $sortby) {
                                                                echo $order;
                                                            } else { ?>0<?php } ?>)">Feedback Questions <?php
                                                               if (1 === $sortby) {
                                                                   if (0 === $order) {
                                                                       ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php
                                                                       }
                                                               } else {
                                                                   ?><i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a>
                                                    </th>

                                                    <th width="5%" class="align-center"><a href="javascript:void(0);" onClick="Redirect(3,<?php
                                                        if ('2' === $sortby) {
                                                            echo $order;
                                                        } else {
                                                            ?>0<?php } ?>)">Order <?php
                                                                if (2 === $sortby) {
                                                                    if (0 === $order) {
                                                                        ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php
                                                                        }
                                                                } else {
                                                                    ?><i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a>
                                                    </th>

                                                    <th width="8%" align="center" style="text-align:center;"><a href="javascript:void(0);" onClick="Redirect(3,<?php
                                                        if ('3' === $sortby) {
                                                            echo $order;
                                                        } else {
                                                            ?>0<?php } ?>)">Status <?php
                                                                if (3 === $sortby) {
                                                                    if (0 === $order) {
                                                                        ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php
                                                                        }
                                                                } else {
                                                                    ?><i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a>
                                                    </th>
                                                    <?php if ($userObj->hasPermission(['edit-rating-feedback-ques', 'update-status-rating-feedback-ques', 'delete-rating-feedback-ques'])) { ?>
                                                    <th width="8%" align="center" style="text-align:center;">Action</th>
                                                    <?php } ?>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $count_all = count($data_drv);
if (!empty($data_drv)) {
    for ($i = 0; $i < $count_all; ++$i) {
        $tQuestion = $data_drv[$i]['tQuestion'];
        $iDisplayOrder = $data_drv[$i]['iDisplayOrder'];
        $eStatus = $data_drv[$i]['eStatus'];
        ?>
                                                        <tr class="gradeA">
                                                            <?php if ($userObj->hasPermission(['update-status-rating-feedback-ques', 'delete-rating-feedback-ques'])) { ?>
                                                            <td align="center" style="text-align:center;"><input type="checkbox" id="checkbox" name="checkbox[]" <?php echo $default; ?> value="<?php echo $data_drv[$i]['iFeedbackId']; ?>" />&nbsp;</td>
                                                            <?php } ?>
                                                            <td><?php echo $tQuestion; ?></td>
                                                            <td width="15%" align="center">
                                                                <?php echo $iDisplayOrder; ?>
                                                            </td>
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
                                                            <?php if ($userObj->hasPermission(['edit-rating-feedback-ques', 'update-status-rating-feedback-ques', 'delete-rating-feedback-ques'])) { ?>


                                                            <td align="center" style="text-align:center;" class="action-btn001">
                                                                <div class="share-button openHoverAction-class" style="display: block;">
                                                                    <label class="entypo-export"><span><img src="images/settings-icon.png" alt=""></span></label>
                                                                    <div class="social show-moreOptions openPops_<?php echo $data_drv[$i]['iFeedbackId']; ?>">
                                                                        <ul>
                                                                            <?php if ($userObj->hasPermission('edit-rating-feedback-ques')) { ?>
                                                                            <li class="entypo-twitter" data-network="twitter">
                                                                                <a href="rating_feedback_ques_action.php?id=<?php echo $data_drv[$i]['iFeedbackId']; ?>" data-toggle="tooltip" title="Edit">
                                                                                    <img src="img/edit-icon.png" alt="Edit">
                                                                                </a>
                                                                            </li>
                                                                            <?php } ?>
                                                                            <?php if ($userObj->hasPermission('update-status-rating-feedback-ques')) { ?>
                                                                                <li class="entypo-facebook" data-network="facebook"><a href="javascript:void(0);" onclick="changeStatus('<?php echo $data_drv[$i]['iFeedbackId']; ?>', 'Inactive')"  data-toggle="tooltip" title="Activate">
                                                                                        <img src="img/active-icon.png" alt="<?php echo $data_drv[$i]['eStatus']; ?>" >
                                                                                    </a></li>
                                                                                <li class="entypo-gplus" data-network="gplus"><a href="javascript:void(0);" onclick="changeStatus('<?php echo $data_drv[$i]['iFeedbackId']; ?>', 'Active')" data-toggle="tooltip" title="Deactivate">
                                                                                        <img src="img/inactive-icon.png" alt="<?php echo $data_drv[$i]['eStatus']; ?>" >
                                                                                    </a></li>
                                                                            <?php } ?>
                                                                            <?php if ($userObj->hasPermission('delete-rating-feedback-ques')) { ?>
                                                                                <li class="entypo-gplus" data-network="gplus"><a href="javascript:void(0);" onclick="changeStatusDelete('<?php echo $data_drv[$i]['iFeedbackId']; ?>')"  data-toggle="tooltip" title="Delete">
                                                                                        <img src="img/delete-icon.png" alt="Delete" >
                                                                                    </a></li>
                                                                            <?php } ?>

                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <?php }  ?>

                                                        </tr>
                                                        <?php
    }
} else {
    ?>
                                                    <tr class="gradeA">
                                                        <td colspan="5"> No Records Found.</td>
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
                        <ul><li>Rating Feedback Questions module will list all feedback questions on this page.</li>
                            <li>Administrator can Activate / Deactivate / Delete any feedback questions.</li>
                            <!--<li>Administrator can export data in XLS or PDF format.</li>-->
                        </ul>
                    </div>
                </div>
            </div>
            <!--END PAGE CONTENT -->
        </div>
        <!--END MAIN WRAPPER -->
        <form name="pageForm" id="pageForm" action="action/rating_feedback_ques.php" method="post" >
            <input type="hidden" name="page" id="page" value="<?php echo $page; ?>">
            <input type="hidden" name="tpages" id="tpages" value="<?php echo $tpages; ?>">
            <input type="hidden" name="iFeedbackId" id="iMainId01" value="" >
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