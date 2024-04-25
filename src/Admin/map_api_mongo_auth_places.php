<?php
include_once '../common.php';
global $userObj;

if (!$userObj->hasPermission('view-map-api-service-account')) {
    $userObj->redirect();
}
if (!$MODULES_OBJ->mapAPIreplacementAvailable()) {
    header('Location:'.$tconfig['tsite_url_main_admin']);
}

$success = $_REQUEST['success'] ?? 0;

// $defaultcurename = get_default_currency();
$script = 'map_api_setting';
$iCompanyId = $_REQUEST['iCompanyId'] ?? '';
// Start Sorting
$sortby = $_REQUEST['sortby'] ?? 0;
$order = $_REQUEST['order'] ?? '';
$ord = ' ORDER BY rd.iDriverId DESC';
if ('4' === $sortby) {
    if (0 === $order) {
        $orderByField['vUsageOrder'] = (int) '-1';
    } else {
        $orderByField['vUsageOrder'] = (int) '1';
    }
}
if ('3' === $sortby) {
    if (0 === $order) {
        $orderByField['eStatus'] = (int) '-1';
    } else {
        $orderByField['eStatus'] = (int) '1';
    }
}

// End Sorting
// Start Search Parameters
$id = isset($_REQUEST['id']) ? stripslashes($_REQUEST['id']) : '';
$sid = isset($_REQUEST['sid']) ? stripslashes($_REQUEST['sid']) : '';
$option = isset($_REQUEST['option']) ? stripslashes($_REQUEST['option']) : '';
$keyword = isset($_REQUEST['keyword']) ? stripslashes($_REQUEST['keyword']) : '';
$searchDate = $_REQUEST['searchDate'] ?? '';
$eStatus = $_REQUEST['eStatus'] ?? '';
$EntityType_option = $_REQUEST['EntityType_option'] ?? '';
$action = ($_REQUEST['action'] ?? '');
$ssql = '';
$cmp_name = '';
// End Search Parameters
$ssql1 = "AND (rd.vEmail != '' OR rd.vPhone != '')";
// Pagination Start
$per_page = $DISPLAY_RECORD_NUMBER; // number of results to show per page
if ('' !== $eStatus) {
    $eStatussql = '';
} else {
    $eStatussql = " AND rd.eStatus != 'Deleted'";
}
$show_page = 1;
// -------------if page is setcheck------------------//
if (isset($_GET['page'])) {
    $show_page = $_GET['page']; // it will telles the current page
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
// for MongoDB
// $DbName = "PlacesDataCollection";
$DbName = TSITE_DB;
$TableName = 'auth_accounts_places';
if ('' !== $id || 0 !== $id || '' !== $eStatus || '' !== $keyword || '' !== $EntityType_option) {
    if ('' !== $id) {
        $searchQuery['vServiceId'] = (int) $id;
    }
    // $regex = new Regex($text, 's');
    if ('' !== $keyword) {
        $searchQuery['auth_key'] = $keyword;
    }
    if ('' !== $eStatus) {
        $searchQuery['eStatus'] = $eStatus;
    }
    // if ($EntityType_option != '') {$searchQuery['EntityType'] = $EntityType_option;}

    if ('' !== $orderByField) {
        $data_drv = $obj->fetchAllRecordsFromMongoDBWithSortParams($DbName, $TableName, $searchQuery, $orderByField);
    } else {
        $data_drv = $obj->fetchAllRecordsFromMongoDBWithDBName($DbName, $TableName, $searchQuery);
    }
} else {
    $data_drv = $obj->fetchAllCollectionFromMongoDB($DbName, $TableName);
}

// Added by HV on 13-05-2021 To restrict addition of accounts if GOOGLE_PLAN_ACCOUNTS_LIMIT reached
$total_accounts = count($data_drv);
$lAddOnConfiguration = json_decode($SETUP_INFO_DATA_ARR[0]['lAddOnConfiguration'], true);
$restrict_account_add = 'No';
if (isset($lAddOnConfiguration['GOOGLE_PLAN']) && in_array($lAddOnConfiguration['GOOGLE_PLAN'], [1, 2], true) && GOOGLE_PLAN_ACCOUNTS_LIMIT === $total_accounts) {
    $restrict_account_add = 'Yes';
}
// Added by HV on 13-05-2021 To restrict addition of accounts if GOOGLE_PLAN_ACCOUNTS_LIMIT reached End

$active_accounts_temp = $data_drv;
array_multisort(array_map(static fn ($element) => $element['vUsageOrder'], $active_accounts_temp), SORT_ASC, $active_accounts_temp);

$active_accounts = [];
foreach ($active_accounts_temp as $k => $data_acc) {
    if ('Active' === $data_acc['eStatus']) {
        $active_accounts[$k] = $data_acc;
    }
}

$total_active_accounts = count($active_accounts);
if ($total_active_accounts > 0) {
    $total_days = date('t');
    $month = date('M');

    $days_per_account = floor($total_days / $total_active_accounts);

    $days_arr = [];
    for ($i = 1; $i <= $total_days; ++$i) {
        $days_arr[] = addOrdinalNumberSuffix($i).' '.$month;
    }

    $days_per_account_arr = partition($days_arr, $total_active_accounts);
    $days_per_account_arr_new = [];
    $c = 0;
    foreach ($active_accounts as $key1 => $v) {
        $days_per_account_arr_new[$v['_id']['$oid']] = $days_per_account_arr[$c];
        ++$c;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
    <!-- BEGIN HEAD-->
    <head>
        <meta charset="UTF-8" />
        <title><?php echo $SITE_NAME; ?> |  <?php echo $langage_lbl_admin['LBL_MAP_API_AUTH_MASTER_ACCOUNT_PLACES']; ?></title>
        <meta content="width=device-width, initial-scale=1.0" name="viewport" />
        <?php include_once 'global_files.php'; ?>
    </head>
    <!-- END  HEAD-->
    <!-- BEGIN BODY-->
    <body class="padTop53">
        <!-- Main Loading -->
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
                                <?php
                                $DbNameTitle = TSITE_DB;
$TableNameMaster = 'auth_master_accounts_places';
$searchQueryServiceID['vServiceId'] = (int) $id;
$data_Service_names = $obj->fetchAllRecordsFromMongoDBWithDBName($DbNameTitle, $TableNameMaster, $searchQueryServiceID);
if ('' !== $data_Service_names[0]['vServiceName']) {
    $Servicetitle = $data_Service_names[0]['vServiceName'];
} else {
    $Servicetitle = $langage_lbl_admin['LBL_MAP_API_AUTH_MASTER_ACCOUNT_PLACES'].$company_name;
}
?>
                                <h2><?php echo $Servicetitle; ?></h2>
                                <a href = "map_api_setting.php" type="button" id="" value="BACK TO MAP API SETTINGS" class="add-btn">BACK TO MAP API SETTINGS</a>
                            </div>
                        </div>
                        <hr />
                    </div>
                    <?php include 'valid_msg.php'; ?>
                    <form name="frmsearch" id="frmsearch" action="javascript:void(0);" method="post">
                        <input type="hidden" name="iDriverId" value="<?php echo $iDriverId; ?>" >
                        <table width="100%" border="0" cellpadding="0" cellspacing="0" class="admin-nir-table">
                            <tbody>
                                <tr>
                                    <td width="5%"><label for="textfield"><strong>Search:</strong></label></td>
                                    <td width="10%" class="padding-right10">
                                        <select name="option" id="option" class="form-control">
                                            <option value="">All</option>
                                        </select>
                                    </td>
                                    <td width="15%" class="searchform"><input type="Text" id="keyword" name="keyword" value="<?php
    if (!empty($keyword)) {
        echo clearName($keyword);
    }
?>"  class="form-control" /></td>
                                    <!-- <td width="15%" class="etype_options" id="etype_options" >
                                        <select class="form-control" name = 'EntityType_option' id="EntityType_option" class="form-control">
                                                <option value=''>Select entity type</option>
                                                <option <?php echo ('Guest' === $EntityType_option) ? 'selected' : ''; ?> value='Guest'>Guest</option>
                                                <option <?php echo ('Admin' === $EntityType_option) ? 'selected' : ''; ?> value='Admin'>Admin</option>
                                                <option <?php echo ('Store' === $EntityType_option) ? 'selected' : ''; ?> value='Store'>Store</option>
                                                <option <?php echo ('User' === $EntityType_option) ? 'selected' : ''; ?> value='User'>User</option>
                                                <option <?php echo ('Provider' === $EntityType_option) ? 'selected' : ''; ?> value='Provider'>Provider</option>
                                                <option <?php echo ('Organization' === $EntityType_option) ? 'selected' : ''; ?> value='Organization'>Organization</option>
                                                <option <?php echo ('Hotel' === $EntityType_option) ? 'selected' : ''; ?> value='Hotel'>Hotel</option>
                                           </select>
                                        </td> -->
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
                                            <!-- <option value="Deleted" <?php
if ('Deleted' === $eStatus) {
    echo 'selected';
}
?> >Delete</option> -->
                                        </select>
                                    </td>
                                    <td>
                                        <input type="submit" value="Search" class="btnalt button11" id="Search" name="Search" title="Search" />
                                        <input type="button" value="Reset" class="btnalt button11" onClick="window.location.href = 'map_api_mongo_auth_places.php?id=<?php echo $id; ?>'"/>
                                        <input type="hidden" name="id" value="<?php echo $id; ?>" />
                                    </td>
                                    <td width="30%">
                                        <?php if ($userObj->hasPermission('create-map-api-service-account') && 'No' === $restrict_account_add) {?>
                                        <a class="add-btn" href="map_api_mongo_auth_places_action.php?sid=<?php echo $id; ?>" style="text-align: center;">Add <?php echo $langage_lbl_admin['LBL_MAP_API_AUTH_MASTER_ACCOUNT_PLACES']; ?></a>
                                        <?php }?>
                                    </td>
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
                                            <?php if ($userObj->hasPermission(['update-status-map-api-service-account', 'delete-map-api-service-account'])) {?>
                                            <select name="changeStatus" id="changeStatus" class="form-control" onChange="ChangeStatusAll(this.value);">
                                                <option value="" >Select Action</option>
                                                <?php if ($userObj->hasPermission('update-status-map-api-service-account')) {?>
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
                                                <?php }?>
                                                <?php if ($userObj->hasPermission('delete-map-api-service-account')) {?>
                                                <!-- <option value="Deleted" <?php
                                                    if ('Delete' === $option) {
                                                        echo 'selected';
                                                    }
                                                    ?> >Delete</option> -->
                                                <?php }
                                                ?>
                                            </select>
                                            <?php }?>
                                        </span>
                                    </div>
                                    <?php if (!empty($data_drv)) {?>
                                    <div class="panel-heading">
                                        <form name="_export_form" id="_export_form" method="post" >
                                            <!-- <button type="button" onClick="showExportTypes('driver')" >Export</button> -->
                                        </form>
                                    </div>
                                    <?php }?>
                                </div>
                                <div style="clear:both;"></div>
                                <div class="table-responsive">
                                    <form class="_list_form" id="_list_form" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                        <table class="table table-striped table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th width="3%" class="align-center"><input type="checkbox" id="setAllCheck" ></th>
                                                    <th width="13%" class="align-center">Title </th>
                                                    <th width="13%" class="align-center">Auth Key </th>
                                                    <!-- <th width="13%" class="align-center">Entity Type </th> -->
                                                    <th width="12%" class="align-center"><a href="javascript:void(0);" onClick="Redirect(4,<?php
                                                    if ('4' === $sortby) {
                                                        echo $order;
                                                    } else {
                                                        ?>0<?php }?>)">Usage Order <?php
                                                        if (4 === $sortby) {
                                                            if (0 === $order) {
                                                                ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php } else {?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php
                                                                }
                                                        } else {
                                                            ?><i class="fa fa-sort" aria-hidden="true"></i> <?php }?></a></th>
                                                    <th width="12%" class="align-center"><a href="javascript:void(0);" onClick="Redirect(3,<?php
                                                        if ('3' === $sortby) {
                                                            echo $order;
                                                        } else {
                                                            ?>0<?php }?>)"> Status <?php
                                                        if (3 === $sortby) {
                                                            if (0 === $order) {
                                                                ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php } else {?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php
                                                                }
                                                        } else {
                                                            ?><i class="fa fa-sort" aria-hidden="true"></i> <?php }?></a></th>
                                                    <th width="8%" class="align-center">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                    if (!empty($data_drv)) {
                                                        for ($i = 0; $i < count($data_drv); ++$i) {
                                                            $range = '';
                                                            if ('Active' === $data_drv[$i]['eStatus']) {
                                                                $range = '<br>(Used between '.$days_per_account_arr_new[$data_drv[$i]['_id']['$oid']][0].' - '.$days_per_account_arr_new[$data_drv[$i]['_id']['$oid']][count($days_per_account_arr_new[$data_drv[$i]['_id']['$oid']]) - 1].')';
                                                            }
                                                            ?>
                                                <tr class="gradeA" >
                                                    <td align="center"><input type="checkbox" id="checkbox" name="checkbox[]" <?php echo $default; ?> value="<?php echo $data_drv[$i]['_id']['$oid']; ?>" />&nbsp;</td>
                                                    <td style="word-break: break-all;" class="align-center"><?php echo $data_drv[$i]['vTitle'] ?: '--'; ?><?php echo $range; ?></td>
                                                    <td style="word-break: break-all;" class="align-center"><?php echo $data_drv[$i]['auth_key'] ?: '--'; ?></td>
                                                    <!--  <td style="word-break: break-all;" class="align-center"><?php echo $data_drv[$i]['EntityType'] ?: '--'; ?></td> -->
                                                    <td style="text-align:center;">
                                                        <?php // if ($userObj->hasPermission(['view-rental-packages', 'create-rental-packages'])) {?>
                                                        <?php echo $data_drv[$i]['vUsageOrder']; ?>
                                                        <?php //     }?>
                                                    </td>
                                                    <td align="center">
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
                                                    <td align="center" class="action-btn001">
                                                        <div class="share-button openHoverAction-class" style="display: block;">
                                                            <label class="entypo-export"><span><img src="images/settings-icon.png" alt=""></span></label>
                                                            <div class="social show-moreOptions for-five openPops_<?php echo $data_drv[$i]['_id']['$oid']; ?>">
                                                                <ul>
                                                                    <?php // if($userObj->hasPermission('edit-providers')){?>
                                                                    <?php // }?>
                                                                    <?php if ($userObj->hasPermission('update-status-map-api-service-account')) {?>
                                                                    <li class="entypo-twitter" data-network="twitter"><a href="map_api_mongo_auth_places_action.php?id=<?php echo $data_drv[$i]['_id']['$oid']; ?>" data-toggle="tooltip" title="Edit">
                                                                        <img src="img/edit-icon.png" alt="Edit">
                                                                        </a>
                                                                    </li>
                                                                    <li class="entypo-facebook" data-network="facebook"><a href="javascript:void(0);" onClick="changeStatus('<?php echo $data_drv[$i]['_id']['$oid']; ?>', 'Inactive')"  data-toggle="tooltip" title="Activate">
                                                                        <img src="img/active-icon.png" alt="<?php echo $data_drv[$i]['eStatus']; ?>" >
                                                                        </a>
                                                                    </li>
                                                                    <li class="entypo-gplus" data-network="gplus"><a href="javascript:void(0);" onClick="changeStatus('<?php echo $data_drv[$i]['_id']['$oid']; ?>', 'Active')" data-toggle="tooltip" title="Deactivate">
                                                                        <img src="img/inactive-icon.png" alt="<?php echo $data_drv[$i]['eStatus']; ?>" >
                                                                        </a>
                                                                    </li>
                                                                    <li class="entypo-gplus" data-network="gplus"><a href="javascript:void(0);" onClick="changeStatusDelete('<?php echo $data_drv[$i]['_id']['$oid']; ?>')"  data-toggle="tooltip" title="Delete">
                                                                        <img src="img/delete-icon.png" alt="Delete" >
                                                                        </a>
                                                                    </li>
                                                                    <?php }?>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <?php
                                                        }
                                                    } else {
                                                        ?>
                                                <tr class="gradeA">
                                                    <td colspan="14"> No Records Found.</td>
                                                </tr>
                                                <?php }?>
                                            </tbody>
                                        </table>
                                    </form>
                                    <?php include 'pagination_n.php'; ?>
                                </div>
                            </div>
                            <!--TABLE-END-->
                        </div>
                    </div>
                    <div class="admin-notes">
                        <h4>Notes:</h4>
                        <ul>
                            <li>Administrator can Activate / Deactivate / Delete any Account.</li>
                            <li>All active Auth Keys must be working.</li>
                            <li>All active Auth Keys must have enabled billing account.</li>
                            <li style="color: #da2c43">If an Auth Key in a particular day slot is in use and is not working, then system may not work properly for those days.</li>
                        </ul>
                    </div>
                </div>
            </div>
            <!--END PAGE CONTENT -->
        </div>
        <!--END MAIN WRAPPER -->
        <form name="pageForm" id="pageForm" action="action/map_api_mongo_auth_places.php" method="post" >
            <input type="hidden" name="id" id="id" value="<?php echo $id; ?>" >
            <input type="hidden" name="page" id="page" value="<?php echo $page; ?>">
            <input type="hidden" name="tpages" id="tpages" value="<?php echo $tpages; ?>">
            <input type="hidden" name="iOid" id="iMainId01" value="" >
            <input type="hidden" name="iCompanyId" id="iCompanyId" value="<?php echo $iCompanyId; ?>" >
            <input type="hidden" name="eStatus" id="eStatus" value="<?php echo $eStatus; ?>" >
            <input type="hidden" name="status" id="status01" value="" >
            <input type="hidden" name="statusVal" id="statusVal" value="" >
            <input type="hidden" name="option" value="<?php echo $option; ?>" >
            <input type="hidden" name="keyword" value="<?php echo $keyword; ?>" >
            <input type="hidden" name="sortby" id="sortby" value="<?php echo $sortby; ?>" >
            <input type="hidden" name="order" id="order" value="<?php echo $order; ?>" >
            <input type="hidden" name="method" id="method" value="" >
        </form>
        <div  class="modal fade" id="detail_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
            <div class="modal-dialog" >
                <div class="modal-content">
                    <div class="modal-header">
                        <h4>
                            <i style="margin:2px 5px 0 2px;"><img src="images/icon/driver-icon.png" alt=""></i>
                            <?php echo $langage_lbl_admin['LBL_MAP_API_AUTH_MASTER_ACCOUNT_PLACES']; ?> Details
                            <button type="button" class="close" data-dismiss="modal">x</button>
                        </h4>
                    </div>
                    <div class="modal-body" style="max-height: 450px;overflow: auto;">
                        <div id="imageIcons" style="display:none">
                            <div align="center">
                                <img src="default.gif"><br/>
                                <span>Retrieving details,please Wait...</span>
                            </div>
                        </div>
                        <div id="driver_detail"></div>
                    </div>
                </div>
            </div>
        </div>
        <div  class="modal fade" id="driver_add_wallet_money" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
            <div class="modal-dialog" >
                <div class="modal-content nimot-class">
                    <div class="modal-header">
                        <h4><i style="margin:2px 5px 0 2px;" class= "fa fa-google-wallet"></i>Add Balance
                            <button type="button" class="close" data-dismiss="modal">x</button>
                        </h4>
                    </div>
                    <form class="form-horizontal" id="add_money_frm" method="POST" enctype="multipart/form-data"    action="" name="add_money_frm">
                        <input type="hidden" id="action" name="action" value="addmoney">
                        <input type="hidden"  name="eTransRequest" id="eTransRequest" value="">
                        <input type="hidden"  name="eType" id="eType" value="Credit">
                        <input type="hidden"  name="eFor" id="eFor" value="Deposit">
                        <input type="hidden"  name="iDriverId" id="iDriver-Id" value="">
                        <input type="hidden"  name="eUserType" id="eUserType" value="Driver">
                        <div class="col-lg-12">
                            <div class="input-group input-append" >
                                <h5><?php echo $langage_lbl['LBL_ADD_WALLET_DESC_TXT']; ?></h5>
                                <div class="ddtt">
                                    <h4><?php echo $langage_lbl['LBL_ENTER_AMOUNT']; ?></h4>
                                    <input type="text" name="iBalance" id="iBalance" class="form-control iBalance add-ibalance" onKeyup="checkzero(this.value);">
                                </div>
                                <div id="iLimitmsg"></div>
                            </div>
                        </div>
                        <div class="nimot-class-but">
                            <input type="button" onClick="check_add_money();" class="save"  id="add_money" name="<?php echo $langage_lbl['LBL_save']; ?>" value="<?php echo $langage_lbl['LBL_Save']; ?>">
                            <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal">Close</button>
                        </div>
                    </form>
                    <div style="clear:both;"></div>
                </div>
            </div>
        </div>
        <?php include_once 'footer.php'; ?>
        <script>
            /*$(document).ready(function() {
             $('#eStatus_options').hide();
             $('#option').each(function(){
             if (this.value == 'rd.eStatus') {
             $('#eStatus_options').show();
             $('.searchform').hide();
             }
             });
             });
             $(function() {
             $('#option').change(function(){
             if($('#option').val() == 'rd.eStatus') {
             $('#eStatus_options').show();
             $("input[name=keyword]").val("");
             $('.searchform').hide();
             } else {
             $('#eStatus_options').hide();
             $("#estatus_value").val("");
             $('.searchform').show();
             }
             });
             });*/
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
            function Add_money_driver(driverid) {
                $("#driver_add_wallet_money").modal('show');
                $(".add-ibalance").val("");
                if (driverid != "") {
                    var setDriverId = $('#iDriver-Id').val(driverid);
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
                    alert("Please enter amount");
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
            function checkzero(userlimit)
            {
                if (userlimit != "") {
                    if (userlimit == 0)
                    {
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
        </script>
    </body>
    <!-- END BODY-->
</html>