<?php
include_once '../common.php';

if (!$userObj->hasPermission('view-donation')) {
    $userObj->redirect();
}

$script = 'donation';
$tbl_name = 'donation';

if ('' === $default_lang) {
    $default_language = 'EN';
} else {
    $default_language = $default_lang;
}

// Start Sorting
$sortby = $_REQUEST['sortby'] ?? 0;
$order = $_REQUEST['order'] ?? '';
$ord = ' ORDER BY tCreatedDate DESC';
if (1 === $sortby) {
    if (0 === $order) {
        $ord = ' ORDER BY tTitle ASC';
    } else {
        $ord = ' ORDER BY tTitle DESC';
    }
}

if (2 === $sortby) {
    if (0 === $order) {
        $ord = ' ORDER BY tCreatedDate ASC';
    } else {
        $ord = ' ORDER BY tCreatedDate DESC';
    }
}
if (3 === $sortby) {
    if (0 === $order) {
        $ord = ' ORDER BY eStatus ASC';
    } else {
        $ord = ' ORDER BY eStatus DESC';
    }
}
if (4 === $sortby) {
    if (0 === $order) {
        $ord = ' ORDER BY iDisplayOrder ASC';
    } else {
        $ord = ' ORDER BY iDisplayOrder DESC';
    }
}

// Start Search Parameters
$option = isset($_REQUEST['option']) ? stripslashes($_REQUEST['option']) : '';
$keyword = isset($_REQUEST['keyword']) ? stripslashes($_REQUEST['keyword']) : '';
$searchDate = $_REQUEST['searchDate'] ?? '';
$eStatus = $_REQUEST['eStatus'] ?? '';
$eUserType = $_REQUEST['eUserType'] ?? '';
$searchusertype = $_REQUEST['searchusertype'] ?? '';
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
        if ('' !== $eStatus) {
            $ssql .= ' AND '.stripslashes($option)." LIKE '%".clean($keyword_new)."%' AND eStatus = '".clean($eStatus)."'";
        } else {
            $ssql .= ' AND '.stripslashes($option)." LIKE '%".clean($keyword_new)."%'";
        }
    } else {
        if ('' !== $eStatus) {
            $ssql .= " AND (tTitle LIKE '%".$keyword_new."%') AND eStatus = '".clean($eStatus)."'";
        } else {
            $ssql .= " AND (tTitle LIKE '%".$keyword_new."%')";
        }
    }
} elseif ('' !== $eStatus && '' === $keyword && '' === $searchusertype) {
    $ssql .= " AND eStatus = '".clean($eStatus)."'";
} elseif ('' !== $searchusertype && '' !== $eStatus && '' === $keyword) {
    $ssql .= " AND eUserType = '".clean($searchusertype)."' AND eStatus = '".clean($eStatus)."'";
} elseif ('' !== $searchusertype && '' === $eStatus && '' === $keyword) {
    $ssql .= " AND eUserType = '".clean($searchusertype)."'";
}

// End Search Parameters

// Pagination Start
$per_page = $DISPLAY_RECORD_NUMBER; // number of results to show per page
$sql = "SELECT COUNT(iDonationId) AS Total FROM donation WHERE eStatus != 'Deleted'  {$ssql}";
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

$sql = "SELECT iDonationId,tTitle,vDonationImage,tDescription,tCreatedDate,eStatus,tLink,iDisplayOrder
FROM donation
WHERE  eStatus != 'Deleted' {$ssql} {$ord} LIMIT {$start}, {$per_page}";

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
        <title><?php echo $SITE_NAME; ?> | Donation </title>
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
                                <h2>Donation</h2>
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
                                          <!-- <option value="">All</option> -->
                                          <option  value="tTitle" <?php if ('tTitle' === $option) {
                                              echo 'selected';
                                          } ?> >Title</option>
                                    </select>
                                    </td>
                                    <td width="15%" class="searchform"><input type="Text" id="keyword" name="keyword" value="<?php echo $keyword; ?>"  class="form-control" /></td>
									<td width="15%" class="usertype_options" id="usertype_options" >
                                        <select class="form-control" name ="searchusertype" id="searchusertype" required="required">
										   <option value="">Select User Type</option>
                                            <option value="all"  <?php if ('all' === $searchusertype) {
                                                echo 'selected';
                                            } ?>>All</option>
                                            <option value="driver" <?php if ('driver' === $searchusertype) {
                                                echo 'selected';
                                            } ?>><?php echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']; ?></option>
                                            <option value="rider" <?php if ('rider' === $searchusertype) {
                                                echo 'selected';
                                            } ?>><?php echo $langage_lbl_admin['LBL_RIDER']; ?></option>
                                            <option value="company" <?php if ('company' === $searchusertype) {
                                                echo 'selected';
                                            } ?>>Store/ Restaurant</option>

										</select>
									</td>


			<td width="12%" class="estatus_options" id="eStatus_options" >
                                        <select name="eStatus" id="eStatus" class="form-control">
                                            <option value="" >Select Status</option>
                                            <option value="Active" <?php
                                            if ('Active' === $eStatus) {
                                                echo 'selected';
                                            }
?> >Active</option>
                                            <option value="Inactive" <?php
if ('Inactive' === $eStatus) {
    echo 'selected';
}
?> >Inactive</option>
                                        </select>
                                    </td>
									<td width="20%">
                                      <input type="submit" value="Search" class="btnalt button11" id="Search" name="Search" title="Search" />
                                      <input type="button" value="Reset" class="btnalt button11" onClick="window.location.href='donation.php'"/>
                                    </td>
                                    <?php if ($userObj->hasPermission('create-donation')) { ?>
                                        <td width="30%"><a class="add-btn" href="donation_action.php" style="text-align: center;">Add Donation</a></td>
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
                                        <?php if ($userObj->hasPermission(['update-status-donation', 'delete-donation'])) { ?>
                                            <select name="changeStatus" id="changeStatus" class="form-control" onchange="ChangeStatusAll(this.value);">
                                                <option value="" >Select Action</option>
                                                <?php if ($userObj->hasPermission('update-status-donation')) { ?>
                                                    <option value='Active' <?php if ('Active' === $option) {
                                                        echo 'selected';
                                                    } ?> >Activate</option>
                                                    <option value="Inactive" <?php if ('Inactive' === $option) {
                                                        echo 'selected';
                                                    } ?> >Deactivate</option>
                                                <?php } ?>
                                                <?php if ('Deleted' !== $eStatus && $userObj->hasPermission('delete-donation')) { ?>
                                                    <option value="Deleted" <?php if ('Delete' === $option) {
                                                        echo 'selected';
                                                    } ?> >Delete</option>
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
                                                        <th align="center" width="3%" style="text-align:center;"><input type="checkbox" id="setAllCheck" ></th>
                                                        <th width="40%"><a href="javascript:void(0);" onClick="Redirect(1,<?php if ('1' === $sortby) {
                                                            echo $order;
                                                        } else { ?>0<?php } ?>)">Title <?php if (1 === $sortby) {
                                                            if (0 === $order) { ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php }
                                                            } else { ?><i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a></th>
                                                        <th width="10%"  style="text-align:center;"><a href="javascript:void(0);" onClick="Redirect(2,<?php if ('2' === $sortby) {
                                                            echo $order;
                                                        } else { ?>0<?php } ?>)">Published date <?php if (2 === $sortby) {
                                                            if (0 === $order) { ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php }
                                                            } else { ?><i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a></th>

                                                        <th width="8%" align="center" style="text-align:center;"><a href="javascript:void(0);" onClick="Redirect(3,<?php if ('3' === $sortby) {
                                                            echo $order;
                                                        } else { ?>0<?php } ?>)">Status <?php if (3 === $sortby) {
                                                            if (0 === $order) { ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php }
                                                            } else { ?><i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a></th>

                                                        <th width="6%" style="text-align:center;"><a href="javascript:void(0);" onClick="Redirect(4,<?php
                                                                if ('4' === $sortby) {
                                                                    echo $order;
                                                                } else {
                                                                    ?>0<?php } ?>)">Order By <?php
                                                                       if (4 === $sortby) {
                                                                           if (0 === $order) {
                                                                               ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php
                                                                               }
                                                                       } else {
                                                                           ?><i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a></th>
                                                        <th width="6%" align="center" style="text-align:center;">Action</th>
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
                                                        <td align="center" style="text-align:center;"><input type="checkbox" id="checkbox" name="checkbox[]" <?php echo $default; ?> value="<?php echo $data_drv[$i]['iDonationId']; ?>" />&nbsp;</td>
                                                        <td><?php
                                                            $donationTitle = '';
                                                            $tTitleArr = (array) json_decode($data_drv[$i]['tTitle']);
                                                            if (isset($tTitleArr['tTitle_'.$default_language]) && '' !== $tTitleArr['tTitle_'.$default_language]) {
                                                                $donationTitle = $tTitleArr['tTitle_'.$default_language];
                                                            }
                                                            echo $donationTitle;
                                                            ?>

                                                          </td>
                                                        <td  align="center"><?php echo DateTime($data_drv[$i]['tCreatedDate'], 'No'); ?></td>

                                                        <?php /* <td><?= $data_drv[$i]['vCityCodeISO_3']; ?></td> */ ?>

                                                        <td align="center">
                                                                <?php if ('Active' === $data_drv[$i]['eStatus']) {
                                                                    $dis_img = 'img/active-icon.png';
                                                                } elseif ('Inactive' === $data_drv[$i]['eStatus']) {
                                                                    $dis_img = 'img/inactive-icon.png';
                                                                } elseif ('Deleted' === $data_drv[$i]['eStatus']) {
                                                                    $dis_img = 'img/delete-icon.png';
                                                                }?>
                                                                <img src="<?php echo $dis_img; ?>" alt="<?php echo $data_drv[$i]['eStatus']; ?>" data-toggle="tooltip" title="<?php echo $data_drv[$i]['eStatus']; ?>">
                                                            </td>
                                                            <td align="center" ><?php echo $data_drv[$i]['iDisplayOrder']; ?></td>
                                                                <td align="center" style="text-align:center;" class="action-btn001">
                                                                <div class="share-button openHoverAction-class" style="display: block;">
                                                                    <label class="entypo-export"><span><img src="images/settings-icon.png" alt=""></span></label>
                                                                    <div class="social show-moreOptions openPops_<?php echo $data_drv[$i]['iDonationId']; ?>">
                                                                        <ul>
                                                                            <li class="entypo-twitter" data-network="twitter"><a href="donation_action.php?id=<?php echo $data_drv[$i]['iDonationId']; ?>" data-toggle="tooltip" title="Edit">
                                                                                <img src="img/edit-icon.png" alt="Edit">
                                                                            </a></li>
                                                                            <?php if ($userObj->hasPermission('update-status-donation')) { ?>
                                                                                    <li class="entypo-facebook" data-network="facebook"><a href="javascript:void(0);" onclick="changeStatus('<?php echo $data_drv[$i]['iDonationId']; ?>','Inactive')"  data-toggle="tooltip" title="Activate">
                                                                                        <img src="img/active-icon.png" alt="<?php echo $data_drv[$i]['eStatus']; ?>" >
                                                                                    </a></li>
                                                                                    <li class="entypo-gplus" data-network="gplus"><a href="javascript:void(0);" onclick="changeStatus('<?php echo $data_drv[$i]['iDonationId']; ?>','Active')" data-toggle="tooltip" title="Deactivate">
                                                                                        <img src="img/inactive-icon.png" alt="<?php echo $data_drv[$i]['eStatus']; ?>" >
                                                                                    </a></li>
                                                                            <?php } ?>
                                                                            <?php if ($userObj->hasPermission('delete-donation')) { ?>
                                                                                    <li class="entypo-gplus" data-network="gplus"><a href="javascript:void(0);" onclick="changeStatusDelete('<?php echo $data_drv[$i]['iDonationId']; ?>')"  data-toggle="tooltip" title="Delete">
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
                                                            <td colspan="7" align="center"> No Records Found.</td>
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
                                            Donation module will list all donation on this page.
                                    </li>
                                    <li>
                                            Administrator Activate / Deactivate / Delete any donation.
                                    </li>
                                    <li>
                            </ul>
                    </div>
                    </div>
                </div>
                <!--END PAGE CONTENT -->
            </div>
            <!--END MAIN WRAPPER -->

<form name="pageForm" id="pageForm" action="action/donation.php" method="post" >
<input type="hidden" name="page" id="page" value="<?php echo $page; ?>">
<input type="hidden" name="tpages" id="tpages" value="<?php echo $tpages; ?>">
<input type="hidden" name="iDonationId" id="iMainId01" value="" >
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
              $(document).ready(function () {
                $('#usertype_options').hide();
                $('#option').each(function () {
					if (this.value == 'eUserType') {
                             $('#usertype_options').show();
                        $('.searchform').hide();
                    }
                });
            });

            $(function () {
                $('#option').change(function () {
                    if ($('#option').val() == 'eUserType') {
                        $('#usertype_options').show();
                        $("input[name=keyword]").val("");
                        $('.searchform').hide();
                    } else {
                        $('#usertype_options').hide();
                        $("#estatus_value").val("");
                        $('.searchform').show();
                    }
                });
            });
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
//                alert(action+formValus);
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