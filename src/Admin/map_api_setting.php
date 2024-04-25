<?php

use MongoDB\BSON\ObjectID;
use MongoDB\BSON\UTCDateTime;

include_once '../common.php';
global $userObj;
$baseUrl = $tconfig['tsite_url'];
if (!$MODULES_OBJ->mapAPIreplacementAvailable()) {
    header('Location:'.$tconfig['tsite_url_main_admin']);
}
if (!$userObj->hasPermission('view-map-api-service-account')) {
    $userObj->redirect();
}
$success = $_REQUEST['success'] ?? 0;
// ===== Import map API Settings data start
$fileName = $_FILES['import_file_map_api']['tmp_name'];
if (SITE_TYPE === 'Demo') {
    $site_type = SITE_TYPE;
} else {
    $site_type = 'No Demo';
}
if ('' !== $fileName) {
    if (SITE_TYPE === 'Demo') {
        header('Location:map_api_setting.php?id='.$id.'&success=2');

        exit;
    }
    $newServiceData = [];
    $fileData = file_get_contents($fileName);
    $fileDataDecode = json_decode($fileData, true);
    $DbName = TSITE_DB;
    $TableName = 'auth_master_accounts_places';
    $TableNameauthAccPlace = 'auth_accounts_places';
    $Tableauthreportaccountsplaces = 'auth_report_accounts_places';
    $serviceCount = count($fileDataDecode['servicedata']);
    $usage_reportCount = count($fileDataDecode['usage_report']);
    $auth_accounts_placesCount = count($fileDataDecode['auth_accounts_places']);
    if ($serviceCount > 0) {
        $obj->deleteAllRecordsFromMongoDB($DbName, $TableName);
        $obj->deleteAllRecordsFromMongoDB($DbName, $TableNameauthAccPlace);
        $obj->deleteAllRecordsFromMongoDB($DbName, $Tableauthreportaccountsplaces);
        foreach ($fileDataDecode['servicedata'] as $key => $servicedata) {
            $servicedata['_id'] = new ObjectID($servicedata['_id']['$oid']);
            $obj->insertRecordsToMongoDBWithDBName($DbName, $TableName, $servicedata);
        }
        foreach ($fileDataDecode['usage_report'] as $key => $usage_reports) {
            $usage_reports['_id'] = new ObjectID($usage_reports['_id']['$oid']);
            $usage_reports['vUsageDate'] = new UTCDateTime($usage_reports['vUsageDate']['$date']);
            $obj->insertRecordsToMongoDBWithDBName($DbName, $Tableauthreportaccountsplaces, $usage_reports);
        }
        foreach ($fileDataDecode['auth_accounts_places'] as $key => $auth_accounts_placesData) {
            $auth_accounts_placesData['_id'] = new ObjectID($auth_accounts_placesData['_id']['$oid']);
            $obj->insertRecordsToMongoDBWithDBName($DbName, $TableNameauthAccPlace, $auth_accounts_placesData);
        }
    } else {
        echo "<script>
                    alert('Please upload valid file content.');
                   window.location = window.location.href;
                </script>";
    }
}
// ===== Import map API Settings data End
$script = 'map_api_setting';
$iCompanyId = $_REQUEST['iCompanyId'] ?? '';
// Start Sorting
$sortby = $_REQUEST['sortby'] ?? 3;
$order = $_REQUEST['order'] ?? '1';
$ord = ' ORDER BY rd.iDriverId DESC';
if (1 === $sortby) {
    if (0 === $order) {
        $orderByField['vServiceName'] = (int) '-1';
    } else {
        $orderByField['vServiceName'] = (int) '1';
    }
}
if ('2' === $sortby) {
    if (0 === $order) {
        $orderByField['vServiceName'] = (int) '-1';
    } else {
        $orderByField['vServiceName'] = (int) '1';
    }
}
if (3 === $sortby) {
    if (0 === $order) {
        $orderByField['vUsageOrder'] = (int) '-1';
    } else {
        $orderByField['vUsageOrder'] = (int) '1';
    }
}
if (6 === $sortby) {
    if (0 === $order) {
        $orderByField['eStatus'] = (int) '-1';
    } else {
        $orderByField['eStatus'] = (int) '1';
    }
}
// End Sorting
// Start Search Parameters
$option = isset($_REQUEST['option']) ? stripslashes($_REQUEST['option']) : '';
$keyword = isset($_REQUEST['keyword']) ? stripslashes($_REQUEST['keyword']) : '';
$searchDate = $_REQUEST['searchDate'] ?? '';
$eStatus = $_REQUEST['eStatus'] ?? '';
$action = ($_REQUEST['action'] ?? '');
// End Search Parameters
$show_page = 1;
$DbName = TSITE_DB;
$TableName = 'auth_master_accounts_places';
$TableName_Accounts = 'auth_accounts_places';

/*echo "<pre>";
print_r(var_dump($eStatus));
print_r(var_dump($keyword));
print_r(var_dump($orderByField));
exit;*/
if ('' !== $eStatus || '' !== $keyword && count($orderByField) > 0) {
    // if ($eStatus != '' || $keyword != '') {
    $searchQuery = [];
    if ('' !== $keyword) {
        $searchQuery['vServiceName'] = $keyword;
    }
    if ('' !== $eStatus) {
        $searchQuery['eStatus'] = $eStatus;
    }
    if ('' !== $orderByField) {
        $data_drv = $obj->fetchAllRecordsFromMongoDBWithSortParams($DbName, $TableName, $searchQuery, $orderByField);
    } else {
        $data_drv = $obj->fetchAllRecordsFromMongoDBWithDBName($DbName, $TableName, $searchQuery);
    }
} else {
    $less = 0;
    $data_drv = $obj->fetchAllCollectionFromMongoDB($DbName, $TableName);
    if (count($data_drv) < 2) {
        if ('1' === $data_drv[0]['vServiceId'] && 'OpenMap' === $data_drv[0]['vServiceName']) {
            echo 'Map API Setting is invalid, kindly setup in proper.';

            exit;
        }
    }
    // // if ((count($data_drv) < 2) && (count($data_drv) != 0)) {
    if (count($data_drv) < 2) {
        foreach ($data_drv as $data_drv_value) {
            if ('OpenMap' === $data_drv_value['vServiceName']) {
            } else {
                $sid = $data_drv_value['vServiceId'];
                $oid = $data_drv_value['_id']['$oid'];
            }
        }
        $duration = isset($_REQUEST['duration']) ? ($_REQUEST['duration']) : '';
        $less = 1;

        include 'usage_report.php';

        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<!-- BEGIN HEAD-->
<head>
    <meta charset="UTF-8"/>
    <title><?php echo $SITE_NAME; ?> | <?php echo $langage_lbl_admin['LBL_MAP_API_SETTING_TXT_ADMIN']; ?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
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
                        $company_name = ('' !== $cmp_name) ? ' of '.$cmp_name : '';
?>
                        <h2><?php echo $langage_lbl_admin['LBL_MAP_API_SETTING_TXT_ADMIN'].$company_name; ?></h2>
                        <!--<input type="button" id="" value="ADD A DRIVER" class="add-btn">-->
                    </div>
                </div>
                <hr/>
            </div>
            <?php include 'valid_msg.php'; ?>
            <form name="frmsearch" id="frmsearch" action="javascript:void(0);" method="post">
                <input type="hidden" name="iDriverId" value="<?php echo $iDriverId; ?>">
                <table width="100%" border="0" cellpadding="0" cellspacing="0" class="admin-nir-table">
                    <tbody>
                    <tr>
                        <td width="5%">
                            <label for="textfield">
                                <strong>Search:</strong>
                            </label>
                        </td>
                        <td width="10%" class="padding-right10">
                            <select name="option" id="option"
                                    class="form-control">
                                <option value="">All</option>
                            </select>
                        </td>
                        <td width="15%" class="searchform">
                            <input type="Text" id="keyword" name="keyword" value="<?php
    if (!empty($keyword)) {
        echo clearName($keyword);
    }
?>" class="form-control"/>
                        </td>
                        <td width="12%" class="estatus_options" id="eStatus_options">
                            <select name="eStatus" id="estatus_value" class="form-control">
                                <option value="">Select Status</option>
                                <option value='Active' <?php if ('Active' === $eStatus) {
                                    echo 'selected';
                                } ?>>
                                    Active
                                </option>
                                <option value="Inactive" <?php if ('Inactive' === $eStatus) {
                                    echo 'selected';
                                } ?>>
                                    Inactive
                                </option>
                                <!-- <option value="Deleted" <?php if ('Deleted' === $eStatus) {
                                    echo 'selected';
                                } ?> >Delete</option> -->
                            </select>
                        </td>
                        <td>
                            <input type="submit" value="Search" class="btnalt button11" id="Search"
                                   name="Search" title="Search"/>
                            <input type="button" value="Reset" class="btnalt button11"
                                   onClick="window.location.href = 'map_api_setting.php'"/>
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

                                        <?php if ($userObj->hasPermission(['update-status-map-api-service-account', 'delete-map-api-service-account'])) { ?>
                                            <select name="changeStatus" id="changeStatus" class="form-control"
                                                    onChange="ChangeStatusAll(this.value);">

                                            <option value="">Select Action</option>

                                            <option value='Active' <?php if ('Active' === $option) {
                                                echo 'selected';
                                            } ?>>

                                                Activate</option>
                                                <option value="Inactive"

                                                <?php if ('Inactive' === $option) {
                                                    echo 'selected';
                                                } ?>>Deactivate

                                            </option>


                                            <?php if (isset($_REQUEST['ENABLE_DELETE_ADMIN_XX4AT3LM']) && 'YES' === strtoupper($_REQUEST['ENABLE_DELETE_ADMIN_XX4AT3LM'])) { ?>
                                                <option value="Delete" <?php if ('Delete' === $option) {
                                                    echo 'selected';
                                                } ?> >Delete</option>
                                            <?php } ?>

                                        </select>
                                        <?php } ?>

                                    </span>
                            </div>
                            <?php // if (!empty($data_drv)) {?>
                            <div class="panel-heading">
                                <form name="_export_form" id="_export_form" method="post" style="display: inline-flex;">



                                    <?php if ($userObj->hasPermission('export-map-api-service-account')) { ?>
                                    <button type="button" style="width: 106px !important;" id="exportall">Export All
                                    </button>
                                    <?php } ?>
                                    <!-- <button type="button" style="width: 106px !important;" onClick="showExportTypes('map_api')" >Export All</button> -->
                                    <?php if ($userObj->hasPermission('import-map-api-service-account')) { ?>
                                    <button type="button" style="width: 100px !important;"
                                            onClick="showImportTypes('map_api')">Import All
                                    </button>
                                    <?php } ?>
                                </form>
                            </div>
                            <?php php; // }?>
                        </div>
                        <?php if (2 === $success) { ?>
                            <div class="alert alert-danger alert-dismissable ">
                                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                                <?php echo $langage_lbl_admin['LBL_EDIT_DELETE_RECORD']; ?>
                            </div>
                            <br/>
                        <?php } ?>
                        <div style="clear:both;"></div>
                        <div class="table-responsive">
                            <form class="_list_form" id="_list_form" method="post"
                                  action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <?php if ($userObj->hasPermission(['update-status-map-api-service-account', 'delete-map-api-service-account'])) { ?>
                                            <th width="3%" class="align-center">
                                                <input type="checkbox"
                                                       id="setAllCheck">
                                            </th>
                                        <?php } ?>
                                        <?php if (ONLYDELIVERALL === 'No') { ?>
                                            <th width="15%">
                                                <a href="javascript:void(0);"
                                                   onClick="Redirect(1,<?php if ('1' === $sortby) {
                                                       echo $order;
                                                   } else { ?>0<?php } ?>)">Service Name <?php
                                                    if ('1' === $sortby) {
                                                        if (0 === $order) { ?>
                                                            <i class="fa fa-sort-amount-asc"
                                                               aria-hidden="true"></i> <?php } else { ?>
                                                            <i
                                                                    class="fa fa-sort-amount-desc"
                                                                    aria-hidden="true"></i>
                                                        <?php }
                                                               } else {
                                                                   ?>
                                                        <i class="fa fa-sort" aria-hidden="true"></i> <?php } ?>
                                                </a>
                                            </th>
                                        <?php } ?>
                                        <th width="18%" class="align-center">
                                            Active Services
                                        </th>
                                        <th width="13%" class="align-center">
                                            <a href="javascript:void(0);"
                                               onClick="Redirect(3,<?php
                                               if ('3' === $sortby) {
                                                   echo $order;
                                               } else {
                                                   ?>0<?php } ?>)">Usage Order <?php
                                                if (3 === $sortby) {
                                                    if (0 === $order) {
                                                        ?>
                                                        <i class="fa fa-sort-amount-asc"
                                                           aria-hidden="true"></i> <?php } else { ?>
                                                        <i class="fa fa-sort-amount-desc"
                                                           aria-hidden="true"></i><?php
                                                           }
                                                } else {
                                                    ?>
                                                    <i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a>
                                        </th>
                                        <th width="13%" class="align-center">Accounts</th>

                                        <?php if ($userObj->hasPermission('viewused-map-api-service-account')) { ?>
                                        <th width="12%" class="align-center">Usage Report</th>
                                        <?php } ?>
                                        <th width="12%" class="align-center">
                                            <a href="javascript:void(0);" onClick="Redirect(6,<?php
                                            if ('6' === $sortby) {
                                                echo $order;
                                            } else {
                                                ?>0<?php } ?>)">Status <?php
                                                if (6 === $sortby) {
                                                    if (0 === $order) {
                                                        ?>
                                                        <i class="fa fa-sort-amount-asc"
                                                           aria-hidden="true"></i> <?php } else { ?>
                                                        <i class="fa fa-sort-amount-desc"
                                                           aria-hidden="true"></i><?php
                                                           }
                                                } else { ?>
                                                    <i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a>
                                        </th>
                                        <?php if ($userObj->hasPermission(['edit-map-api-service-account', 'update-status-map-api-service-account'])) { ?>
                                        <th width="8%" class="align-center">Action</th>
                                        <?php } ?>
                                    </tr>
                                    </thead>
                                        <tbody>
                                            <?php
                                // header('Location:usage_report.php');
                                if (!empty($data_drv)) {
                                    $activeServices = 0;

                                    $activeServicesArray = [];

                                    for ($j = 0; $j < count($data_drv); ++$j) {
                                        if ('Active' === $data_drv[$j]['eStatus']) {
                                            ++$activeServices;

                                            $data_drv[$j]['eStatus'];

                                            $activeServiesList[$j] = explode(',', $data_drv[$j]['vActiveServices']);
                                        }
                                    }

                                    // foreach ($activeServiesList as $key => $values) {

                                    //     foreach ($values as $newKey => $finalVal) {

                                    //         $activeServicesArray[] = $finalVal;

                                    //     }

                                    // }

                                    ?>

                                <script type="text/javascript">var AllActiveServices = <?php echo json_encode($activeServiesList); ?>;</script>

                                <script type="text/javascript">var AllActiveServicesTemp = <?php echo json_encode($activeServiesList); ?>;</script>

                                <?php

                            $searchQueryActive['eStatus'] = 'Active';

                                    $data_active_status = $obj->fetchAllRecordsFromMongoDBWithDBName($DbName, $TableName_Accounts, $searchQueryActive);

                                    $ActiveStatusAcounts = [];

                                    foreach ($data_active_status as $accountActiveData) {
                                        $ActiveStatusAcounts[$accountActiveData['vServiceId']] = empty($ActiveStatusAcounts[$accountActiveData['vServiceId']]) ? 1 : ($ActiveStatusAcounts[$accountActiveData['vServiceId']] + 1);
                                    }

                                    $data_drvAcc = $obj->fetchAllCollectionFromMongoDB($DbName, $TableName_Accounts);

                                    $serviceIDAccArr = [];

                                    foreach ($data_drvAcc as $accountData) {
                                        $serviceIDAccArr[$accountData['vServiceId']] = empty($serviceIDAccArr[$accountData['vServiceId']]) ? 1 : ($serviceIDAccArr[$accountData['vServiceId']] + 1);
                                    }

                                    for ($i = 0; $i < count($data_drv); ++$i) {
                                        $vAvailableServicesArry = explode(',', $data_drv[$i]['vAvailableServices']);

                                        $serviceid['vServiceId'] = $data_drv[$i]['vServiceId'];

                                        $authAccPlacesCount = $serviceIDAccArr[$data_drv[$i]['vServiceId']];

                                        $ActiveStatusAcountsCount = $ActiveStatusAcounts[$data_drv[$i]['vServiceId']];

                                        ?>

                                            <tr class="gradeA">
                                            <?php if ($userObj->hasPermission(['update-status-map-api-service-account', 'delete-map-api-service-account'])) { ?>
                                                <td style="vertical-align: middle;" align="center">
							<input type="checkbox" id="checkbox"
                                                        name="checkbox[]" <?php echo $default; ?>

                                                        value="<?php echo $data_drv[$i]['vServiceName']; ?>" />&nbsp;

                                                </td>
                                            <?php } ?>
                                            <td style="vertical-align: middle;">
                                                <a target="_blank"
                                                   href="<?php echo clearName($data_drv[$i]['vServiceURL']); ?>">
                                                    <?php echo clearName($data_drv[$i]['vServiceName']); ?></a>
                                            </td>
                                            <td style="vertical-align: middle;" align="center">
                                                <?php
                                                    $activeServieListArry = explode(',', $data_drv[$i]['vActiveServices']);
                                        $key = array_search('PlaceDetails', $activeServieListArry, true);
                                        if (false !== $key) {
                                            unset($activeServieListArry[$key]);
                                        }
                                        echo ($data_drv[$i]['vActiveServices']) ? implode(', ', $activeServieListArry) : '--';
                                        if (count($vAvailableServicesArry) > 1) {
                                            ?>
                                                    <div style="vertical-align: middle;margin-top:5px;">
                                                        <?php if ('Google' !== $data_drv[$i]['vServiceName']) { ?>
                                                            <a href="javascript:void(0);"
                                                               onclick="show_services_config('<?php echo $data_drv[$i]['_id']['$oid']; ?>','<?php echo $i; ?>','<?php echo $data_drv[$i]['eStatus']; ?>','<?php echo $activeServices; ?>')"
                                                               class="add-btn-sub">Config
                                                            </a>
                                                        <?php } else { ?>
                                                            <a href="javascript:void(0);" data-toggle="tooltip"
                                                               title="Google Maps API will remain as a base to provide backup support in the failure event of other Maps API services. So, it's not possible to alter existing configuration of Google Maps API services."
                                                               disabled='disabled' style="cursor: no-drop;"
                                                               class="add-btn-sub">Config
                                                            </a>
                                                        <?php } ?>
                                                    </div>
                                                <?php } ?>
                                            </td>
                                            <td style="word-break: break-all;vertical-align: middle;"
                                                class="align-center">
                                                <?php echo clearName($data_drv[$i]['vUsageOrder']); ?></td>
                                            <td style="vertical-align: middle;text-align:center">
                                                <?php
                                                if ('OpenMap' === $data_drv[$i]['vServiceName']) {
                                                    echo '<span style="text-align:center;">--</span>';
                                                } else {
                                                    $svgicon = '<span style="position: absolute;" data-toggle="tooltip" title="Accounts are not available. Please add it."><img style="width:20px; height:20px; margin-left: 5px" src="'.$baseUrl.'assets/img/danger-new.svg" /></span>'; ?>
                                                    <a style="margin-left:5px;" target="_blank"
                                                       href="map_api_mongo_auth_places.php?id=<?php echo $data_drv[$i]['vServiceId']; ?>"
                                                       class="add-btn-sub">Add/View
                                                        (<?php echo ('' !== $authAccPlacesCount) ? $authAccPlacesCount : 0; ?>
                                                        )
                                                    </a>
                                                    <?php echo (0 === $authAccPlacesCount) ? $svgicon : '';
                                                }
                                        // }?>
                                            </td>
                                            <?php if ($userObj->hasPermission('viewused-map-api-service-account')) { ?>
                                            <td style="vertical-align: middle;" align="center">
                                                <a class="add-btn-sub"
                                                   href="usage_report.php?oid=<?php echo $data_drv[$i]['_id']['$oid']; ?>&sid=<?php echo $data_drv[$i]['vServiceId']; ?>"><?php echo ' View '; ?></a>
                                            </td>
                                            <?php } ?>
                                            <td style="vertical-align: middle;" align="center">
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
                                            <?php if ('Google' !== $data_drv[$i]['vServiceName']) { ?>

                                                    <?php if ($userObj->hasPermission(['edit-map-api-service-account', 'update-status-map-api-service-account', ''])) { ?>


                                                <td style="vertical-align: middle;" align="center"
                                                    class="action-btn001">
                                                    <div class="share-button openHoverAction-class"
                                                         style="display: block;">
                                                        <label class="entypo-export">
                                                            <span><img src="images/settings-icon.png" alt=""></span>
                                                        </label>
                                                        <div class="social show-moreOptions for-five openPops_<?php echo $data_drv[$i]['vUsageOrder']; ?>">
                                                            <ul>
                                                                <?php if ('Yes' !== $data_drv[$i]['eDefault']) { ?>
                                                                    <?php if ($userObj->hasPermission('edit-map-api-service-account')) { ?>
                                                                        <li class="entypo-twitter"
                                                                            data-network="twitter">
                                                                            <a href="map_api_setting_action.php?id=<?php echo $data_drv[$i]['_id']['$oid']; ?>&sid=<?php echo $data_drv[$i]['vServiceId']; ?>"
                                                                               data-toggle="tooltip" title="Edit">
                                                                                <img src="img/edit-icon.png" alt="Edit">
                                                                            </a>
                                                                        </li>
                                                                    <?php } ?>
                                                                    <?php if ('Google' !== $data_drv[$i]['vServiceName']) { ?>
                                                                        <?php if ($userObj->hasPermission('update-status-map-api-service-account')) { ?>
                                                                            <li class="entypo-facebook"
                                                                                data-network="facebook">
                                                                                <a
                                                                                        href="javascript:void(0);"
                                                                                    <?php
                                                                            if ($ActiveStatusAcountsCount > 0 && 1 !== $data_drv[$i]['vServiceId']) { ?>
                                                                                        onClick="changeStatusForMapAPI('<?php echo $data_drv[$i]['vServiceName']; ?>', 'Inactive','<?php echo $activeServices; ?>','<?php echo $i; ?>','<?php echo $site_type; ?>')"
                                                                                        title="Activate"
                                                                                    <?php } elseif (1 === $data_drv[$i]['vServiceId']) { ?>
                                                                                        onClick="changeStatusForMapAPI('<?php echo $data_drv[$i]['vServiceName']; ?>', 'Inactive','<?php echo $activeServices; ?>','<?php echo $i; ?>','<?php echo $site_type; ?>')"
                                                                                        title="Activate"
                                                                                    <?php } else { ?>
                                                                                        title="No active account available. So you can not make it active."
                                                                                    <?php } ?>
                                                                                        data-toggle="tooltip">
                                                                                    <img src="img/active-icon.png"
                                                                                         alt="<?php echo $data_drv[$i]['eStatus']; ?>">
                                                                                </a>
                                                                            </li>
                                                                            <li class="entypo-gplus"
                                                                                data-network="gplus">
                                                                                <a
                                                                                        href="javascript:void(0);"
                                                                                        onClick="changeStatusForMapAPI('<?php echo $data_drv[$i]['vServiceName']; ?>', 'Active','<?php echo $activeServices; ?>','<?php echo $i; ?>','<?php echo $site_type; ?>')"
                                                                                        data-toggle="tooltip"
                                                                                        title="Deactivate">
                                                                                    <img src="img/inactive-icon.png"
                                                                                         alt="<?php echo $data_drv[$i]['eStatus']; ?>">
                                                                                </a>
                                                                            </li>
                                                                        <?php } ?>
                                                                        <?php if (isset($_REQUEST['ENABLE_DELETE_ADMIN_XX4AT3LM']) && 'YES' === strtoupper($_REQUEST['ENABLE_DELETE_ADMIN_XX4AT3LM'])) { ?>
                                                                            <li class="entypo-gplus"
                                                                                data-network="gplus">
                                                                                <a
                                                                                        href="javascript:void(0);"
                                                                                        onClick="changeStatusForMapAPI('<?php echo $data_drv[$i]['vServiceName']; ?>', 'Delete','<?php echo $activeServices; ?>','<?php echo $i; ?>','<?php echo $site_type; ?>')"
                                                                                        data-toggle="tooltip"
                                                                                        title="Delete">
                                                                                    <img src="img/delete-icon.png"
                                                                                         alt="<?php echo $data_drv[$i]['eStatus']; ?>">
                                                                                </a>
                                                                            </li>
                                                                        <?php } ?>
                                                                    <?php } ?>
                                                                    <?php // if (SITE_TYPE == 'Demo') {?>
                                                                    <!--   <li class="entypo-gplus" data-network="gplus"><a href="javascript:void(0);" onClick="resetTripStatus('<?php echo $data_drv[$i]['vUsageOrder']; ?>')"  data-toggle="tooltip" title="Reset"><img src="img/reset-icon.png" alt="Reset"></a></li> -->
                                                                    <?php // }?>
                                                                <?php } else { ?>
                                                                    --
                                                                <?php } ?>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </td>

                                                <?php } ?>
                                            <?php } else { ?>
                                            <?php if ($userObj->hasPermission(['edit-map-api-service-account', 'update-status-map-api-service-account'])) { ?>
                                                <td align="center" class="action-btn001">
                                                    <!-- <a href="map_api_setting_action.php?id=<?php echo $data_drv[$i]['_id']['$oid']; ?>&sid=<?php echo $data_drv[$i]['vServiceId']; ?>" data-toggle="tooltip" title="Edit">

                                                                                        <img src="img/edit-icon.png" alt="Edit">

                                                                    </a> -->
                                                    <div data-toggle="tooltip"
                                                         title="Google Maps API will remain as a base to provide backup support in the failure event of other Maps API services. So, it's not possible to alter existing configuration of Google Maps API services."
                                                         class="share-button openHoverAction-class"
                                                         style="display: block;">
                                                        <label disabled style="cursor: no-drop;" class="entypo-export">
                                                            <span><img style="cursor: no-drop;"
                                                                       src="images/settings-icon.png" alt=""></span>
                                                        </label>
                                                    </div>
                                                </td>
                                            <?php }
                                            } ?>
                                        </tr>
                                    <?php
                                    }
                                } else {
                                    // header('Location:usage_report.php');
                                    ?>
                                        <tr class="gradeA">
                                            <td colspan="14"> No Records Found.</td>
                                        </tr>
                                    <?php } ?>
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
                    <li>
                        <strong>Geocoding:</strong>
                        System is using this Service to convert location into address.
                    </li>
                    <li>
                        <strong>Direction:</strong>
                        System is using this Service to draw route b/w two locations on map.
                    </li>
                    <li>
                        <strong>AutoComplete:</strong>
                        System is using this Service to give suggestion of different places.
                    </li>
                    <li>
                        <strong>Google Maps API:</strong>
                        Google Maps API will remain as a base to provide backup support in the failure event of other
                        Maps API services. So, it's not possible to alter existing configuration of Google Maps API
                        services.
                    </li>
                    <li>
                        <strong>Import and Export Feature:</strong>
                        Use Import and Export feature when you are going to change the hosting server. This feature will
                        help you to set the existing API configuration into the new hosting server.
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <!--END PAGE CONTENT -->
</div>
<!--END MAIN WRAPPER -->
<form name="pageForm" id="pageForm" action="action/map_api_setting.php" method="post">
    <input type="hidden" name="page" id="page" value="<?php echo $page; ?>">
    <input type="hidden" name="tpages" id="tpages" value="<?php echo $tpages; ?>">
    <input type="hidden" name="iMongoName" id="iMainId01" value="">
    <input type="hidden" name="iCompanyId" id="iCompanyId" value="<?php echo $iCompanyId; ?>">
    <input type="hidden" name="eStatus" id="eStatus" value="<?php echo $eStatus; ?>">
    <input type="hidden" name="status" id="status01" value="">
    <input type="hidden" name="statusVal" id="statusVal" value="">
    <input type="hidden" name="option" value="<?php echo $option; ?>">
    <input type="hidden" name="keyword" value="<?php echo $keyword; ?>">
    <input type="hidden" name="sortby" id="sortby" value="<?php echo $sortby; ?>">
    <input type="hidden" name="order" id="order" value="<?php echo $order; ?>">
    <input type="hidden" name="method" id="method" value="">
</form>
<div class="modal fade" id="detail_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4>
                    <i style="margin:2px 5px 0 2px;">
                        <img src="images/icon/driver-icon.png" alt="">
                    </i>
                    <?php echo $langage_lbl_admin['LBL_MAP_API_SETTING_TXT_ADMIN']; ?> Details
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
                <div id="driver_detail"></div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="import_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4>
                    Import All Map API Settings
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
                <div>
                    <form name="_import_map_api_settings" id="_import_map_api_settings" enctype="multipart/form-data"
                          method="POST"
                          onsubmit="return confirm('Are you sure to remove your current map api setting data?');">
                        <div style="color:#1fbad6;">
                            <b>Note:</b>
                            Please note that all map api setting data will be removed.
                        </div>
                        <br>
                        <input type="file" name="import_file_map_api" id="import_file_map_api" required>
                        <br>
                        <input type="submit" value="Submit" class="btnalt button11" id="import_submit" name="Submit"
                               title="Submit"/>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="services_config_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4>
                    <!-- <i style="margin:2px 5px 0 2px;"><img src="images/icon/driver-icon.png" alt=""></i> -->
                    <?php echo $langage_lbl_admin['LBL_MAP_API_SERVICES_CONFIGURATION']; ?> Details
                    <button type="button" class="close" data-dismiss="modal">x</button>
                </h4>
            </div>
            <div class="modal-body" style="max-height: 450px;overflow: auto;">
                <div id="imageIcons" style="display:none">
                    <div align="center">
                        <img src="default.gif">
                        <br/>
                    </div>
                </div>
                <div id="services_config"></div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="driver_add_wallet_money" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
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
                <input type="hidden" name="iDriverId" id="iDriver-Id" value="">
                <input type="hidden" name="eUserType" id="eUserType" value="Driver">
                <div class="col-lg-12">
                    <div class="input-group input-append">
                        <h5><?php echo $langage_lbl['LBL_ADD_WALLET_DESC_TXT']; ?></h5>
                        <div class="ddtt">
                            <h4><?php echo $langage_lbl['LBL_ENTER_AMOUNT']; ?></h4>
                            <input type="text" name="iBalance" id="iBalance"
                                   class="form-control iBalance add-ibalance" onKeyup="checkzero(this.value);">
                        </div>
                        <div id="iLimitmsg"></div>
                    </div>
                </div>
                <div class="nimot-class-but">
                    <input type="button" onClick="check_add_money();" class="save" id="add_money"
                           name="<?php echo $langage_lbl['LBL_save']; ?>" value="<?php echo $langage_lbl['LBL_Save']; ?>">
                    <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal">Close</button>
                </div>
            </form>
            <div style="clear:both;"></div>
        </div>
    </div>
</div>
<?php include_once 'footer.php'; ?>
<script>
    $(document).ready(function () {

        $('[data-toggle="tooltip"]').tooltip();
        $("#exportall").on('click', function () {

            var action = "main_export.php";
            var section = "map_api";
            var formValus = $("#_export_form, #pageForm, #show_export_modal_form_json").serialize();


            window.location.href = action + '?section=' + section + '&' + formValus;

            $("#show_export_types_modal_json").modal('hide');
            return false;

        });

    });

    $('INPUT[type="file"]').change(function (e) {

        var ext = this.value.match(/\.(.+)$/)[1];

        switch (ext) {


            case 'json':

                $('#import_submit').attr('disabled', false);

                break;

            default:

                alert('Please upload json file.');

                this.value = '';

        }

        var fileName = e.target.files[0].name;
        var myresult = 'false';
        // $.ajax({
        //   url: fileName,
        //   dataType: 'json',
        //   // headers: {  'Access-Control-Allow-Origin': '*' },
        //   success: function(data,textStatus,xhr){
        //         if(data.servicedata.length > 0){
        //             console.log('more 0');
        //             $('#import_submit').attr('disabled', false);
        //         }else{
        //             console.log('count faile() 0');
        //              fail();
        //          }
        //     },
        //     error: function(xhr,textStatus,errorThrown){
        //         console.log(xhr);
        //                  fail();
        //              },
        // });

    });

    function fail() {
        $('#import_submit').attr('disabled', true);
        $('#import_file_map_api').value = '';
        alert('Please upload valid content json file.');

    }

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


    function show_services_config(oid, row_id, status, countActiveServices) {

        $("#services_config").html('');

        $("#services_config_modal").modal('show');


        if (oid != "") {

            // var request = $.ajax({

            //     type: "POST",

            //     url: "ajax_services_config.php",

            //     data: "iServiceOid=" + oid +"&row_id="+row_id +"&status="+status+"&countActiveServices="+countActiveServices,

            //     datatype: "html",

            //     success: function(data) {

            //         $("#services_config").html(data);

            //         // $("#imageIcons").hide();

            //     }

            // });

            var ajaxData = {
                'URL': '<?php echo $tconfig['tsite_url_main_admin']; ?>ajax_services_config.php',
                'AJAX_DATA': "iServiceOid=" + oid + "&row_id=" + row_id + "&status=" + status + "&countActiveServices=" + countActiveServices,
                'REQUEST_DATA_TYPE': 'html'
            };
            getDataFromAjaxCall(ajaxData, function (response) {
                if (response.action == "1") {
                    var data = response.result;
                    $("#services_config").html(data);
                } else {
                    console.log(response.result);
                }
            });

        }

    }


    function update_service_config() {


        // console.log("before");

        // console.log(AllActiveServices);

        var activeDataForCurrnetRowID = [];

        var status = $('#status').val();

        var row_id = $('#row_id').val();

        var countActiveServices = $('#countActiveServices').val();

        var serialdataAry = $("#service_config_frm").serializeArray();

        $.each(serialdataAry, function (i, field) {

            if (field.name == 'selectedval[]') {

                activeDataForCurrnetRowID.push(field.value);

            }

        });

        AllActiveServices[row_id] = [];

        for (var n = 0; n < activeDataForCurrnetRowID.length; n++) {

            // AllActiveServices[row_id].push(activeDataForCurrnetRowID[n]);

            AllActiveServices[row_id][n] = (activeDataForCurrnetRowID[n]);

        }

        var result = ValidateMeConfig(AllActiveServices, status, countActiveServices, row_id);

        if (result != false) {

            // $.ajax({

            //     type: 'POST',

            //     url: 'ajax_services_config_update.php',

            //     data: $('#service_config_frm').serialize(),

            //     success: function(data) {

            //         window.location.href = "<?php echo $tconfig['tsite_url_main_admin '].'map_api_setting.php '; ?>";

            //     }

            // });

            var ajaxData = {
                'URL': '<?php echo $tconfig['tsite_url_main_admin']; ?>ajax_services_config_update.php',
                'AJAX_DATA': $('#service_config_frm').serialize(),
            };
            getDataFromAjaxCall(ajaxData, function (response) {
                if (response.action == "1") {
                    var data = response.result;
                    window.location.href = "<?php echo $tconfig['tsite_url_main_admin '].'map_api_setting.php '; ?>";
                } else {
                    console.log(response.result);
                }
            });

        }

    }


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

    function validateandconfirm() {

        var result = confirm("Are you sure? your map api data will be removed.");

        if (result == true || result == "Yes") {

            $().submit;

        }

    }
</script>
</body>
<!-- END BODY-->
</html>
