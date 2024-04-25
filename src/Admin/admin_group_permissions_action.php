<?php

use Models\AdminProPermission;
use Models\AdminProPermissionDisplayGroup;

include_once '../common.php';
/*if (!$userObj->hasRole(1)) {
    $userObj->redirect();
}*/
// All App Type : Ride,Delivery,Ride-Delivery,UberX,Ride-Delivery-UberX,Foodonly,Deliverall
$id = $_REQUEST['id'] ?? '';
$success = $_REQUEST['success'] ?? 0;
$ksuccess = $_REQUEST['ksuccess'] ?? 0;
$sql = "SELECT iGroupId FROM `administrators` WHERE iAdminId = '".$_SESSION['sess_iAdminUserId']."'";
$administratorsData = $obj->MySQLSelect($sql)[0]['iGroupId'];
$action = ('' !== $id) ? 'Edit' : 'Add';
$tbl_name = 'admin_groups';
$script = 'AdminGroups';
$sql1 = 'SELECT iGroupId,vGroup FROM admin_groups WHERE 1';
$db_group = $obj->MySQLSelect($sql1);
// set all variables with either post (when submit) either blank (when insert)
$vGroup = $_POST['vGroup'] ?? '';
$permission_ids = $_POST['permission_ids'] ?? [];
if (!is_array($permission_ids)) {
    $permission_ids = [$permission_ids];
}
$backlink = $_POST['backlink'] ?? '';
if (isset($_POST['submit'])) {
    if (('' !== $id && SITE_TYPE === 'Demo') || ('' === $id && SITE_TYPE === 'Demo')) {
        $_SESSION['success'] = '2';
        header('location:'.$backlink);

        exit;
    }

    // Add Custom validation
    require_once 'Library/validation.class.php';
    $validobj = new validation();
    $validobj->add_fields($_POST['vGroup'], 'req', 'Group Name is required');
    $error = $validobj->validate();

    $csql = "SELECT count(iGroupId) as TotalAdmin FROM `admin_groups` WHERE vGroup = '".$_POST['vGroup']."' AND iGroupId != '".$id;
    $AdminData = $obj->MySQLSelect($csql);
    if ($AdminData[0]['TotalAdmin'] > 0) {
        $error .= '* Group Name is already exists.<br>';
    }

    if ($error) {
        $success = 3;
        $newError = $error;
    } else {
        $q = 'INSERT INTO ';
        $where = $str = '';
        if ('Edit' === $action) {
            $str = ", eStatus = 'Inactive' ";
        }
        if ('' !== $id) {
            $q = 'UPDATE ';
            $where = " WHERE `iGroupId` = '".$id."'";
        }
        $query = $q.' `'.$tbl_name."` SET
            `vGroup` = '".$vGroup."'
             ".$where;
        $obj->sql_query($query);
        $id = ('' !== $id) ? $id : $obj->GetInsertId();
        if ('Edit' === $action) {
            $obj->sql_query("DELETE FROM admin_pro_group_permission where group_id={$id}");
        }
        if (count($permission_ids) > 0) {
            $insert_format = array_map(static fn ($permission_id) => "{$id}, {$permission_id}", $permission_ids);
            $sql = 'INSERT INTO admin_pro_group_permission (group_id, permission_id) values('.implode('),(', $insert_format).')';
            $obj->sql_query($sql);
        }
        if ('Add' === $action) {
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_INSERT_MSG'];
        } else {
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = $langage_lbl_admin['LBL_Record_Updated_successfully'];
        }
        // header("location:" . $backlink); //Temp
        // header('Location: ' . $_SERVER['REQUEST_URI']);
        header('Location:admin_groups.php');

        exit;
    }
}
// for Edit
if ('Edit' === $action) {
    $sql = 'SELECT * FROM '.$tbl_name." WHERE iGroupId = '".$id."'";
    $db_data = $obj->MySQLSelect($sql);
    // echo "<pre>";print_R($db_data);echo "</pre>";
    // $vPass = decrypt($db_data[0]['vPassword']);
    $edit_data = $db_data[0];
    $sql = "SELECT * FROM admin_pro_group_permission WHERE EXISTS (SELECT * FROM admin_pro_permissions WHERE admin_pro_permissions.id = admin_pro_group_permission.permission_id AND admin_pro_permissions.status = 'Active') AND group_id = {$id}";
    $selected_permition = $obj->MySQLSelect($sql);
    $edit_data['permissions'] = array_map(static fn ($item) => $item['permission_id'], $selected_permition);
}
$ssql = '';
$uberXService = 1;
if ('No' === $MODULES_OBJ->isUfxFeatureAvailable('Yes')) {
    $ssql = " AND eFor != 'UberX'";
    $uberXService = 0;
}
$sql = "SELECT * FROM admin_permissions_pro WHERE status = 'Active' AND (vDispalyAppType='All' OR `vDispalyAppType` REGEXP  '".$APP_TYPE."') {$ssql}";
$all_permissions = $obj->MySQLSelect($sql);
$groupPermissionData = [];
$flymodule = 'No';
if ($MODULES_OBJ->isAirFlightModuleAvailable('', 'Yes')) {
    $flymodule = 'Yes';
}
$uberxService = $MODULES_OBJ->isUberXFeatureAvailable('Yes') ? 'Yes' : 'No';
$rideEnable = $MODULES_OBJ->isRideFeatureAvailable('Yes') ? 'Yes' : 'No';
$deliveryEnable = $MODULES_OBJ->isDeliveryFeatureAvailable('Yes') ? 'Yes' : 'No';
$deliverallEnable = $MODULES_OBJ->isDeliverAllFeatureAvailable('Yes') ? 'Yes' : 'No';
$biddingEnable = $MODULES_OBJ->isEnableBiddingServices('Yes') ? 'Yes' : 'No';
$nearbyEnable = $MODULES_OBJ->isEnableNearByService('Yes') ? 'Yes' : 'No';
$trackServiceEnable = $MODULES_OBJ->isEnableTrackServiceFeature('Yes') ? 'Yes' : 'No';
$rideShareEnable = $MODULES_OBJ->isEnableRideShareService('Yes') ? 'Yes' : 'No';
$rentitemEnable = $MODULES_OBJ->isEnableRentItemService('Yes') ? 'Yes' : 'No';
$rentestateEnable = $MODULES_OBJ->isEnableRentEstateService('Yes') ? 'Yes' : 'No';
$rentcarEnable = $MODULES_OBJ->isEnableRentCarsService('Yes') ? 'Yes' : 'No';
$trackAnyServiceEnable = $MODULES_OBJ->isEnableTrackAnyServiceFeature('Yes') ? 'Yes' : 'No';
$genieEnable = GENIE_ENABLED;
$runnerEnable = RUNNER_ENABLED;
// $groupPermissionData_main = Models\AdminProPermission::with(['group'])->active()->orderBy('display_order')->get()->groupBy('group.id')->toArray();
$groupPermissionData_main = AdminProPermissionDisplayGroup::where([['eStatus', '=', 'Active']])->with(['subgroup.permission'])->orderBy('display_order', 'asc')->get()->sortBy('display_order')->groupBy('id')->toArray();
$FARRAY = [];
// $i = 1;
foreach ($groupPermissionData_main as $key => $main) {
    $main = $main[0];
    if ($main['subgroup'] && null !== !empty($main['subgroup'])) {
    } else {
        // unset($groupPermissionData_main[$i][0]['subgroup']);
        $groupPermissionData_main[$key][0]['permission'] = AdminProPermission::where([['display_group_id', '=', $main['id']], ['status', '=', 'Active']])->with(['permission'])->orderBy('display_order')->get()->toArray();
        unset($groupPermissionData_main[$key][0]['subgroup']);
    }
    $permission = permission_main($main['eFor']);
    if ($permission) {
        $FARRAY[$main['id']] = $groupPermissionData_main[$key][0];
    }
    // $i++;
}
$groupPermissionData_main = $FARRAY;
foreach ($groupPermissionData_main as $key1 => $value1) {
    $noSubCategory = 0;
    if (!empty($value1['subgroup']) && $value1['subgroup']) {
        $permission_arr = [];
        foreach ($value1['subgroup'] as $key11 => $value12) {
            $permission_subgroup = permission_main($value12['eFor']);
            if ($permission_subgroup) {
                $permission = permission($value12['permission']);
                $value1['subgroup'][$key11]['permission'] = $permission;
            } else {
                unset($value1['subgroup'][$key11]);
            }
        }
    } else {
        // $permission = permission($value1['permission']);
        $value1['permission'] = permission($value1['permission']);
        $noSubCategory = 1;
    }
    $groupPermissionData[$key1] = $value1;
    if (empty($value1['permission']) && 1 === $noSubCategory) {
        unset($groupPermissionData[$key1]);
    }
}
function permission_main($eFor)
{
    global $rideEnable, $runnerEnable, $genieEnable, $biddingEnable, $nearbyEnable, $trackServiceEnable, $rideShareEnable, $flymodule, $deliverallEnable, $deliveryEnable, $uberxService, $rentitemEnable, $rentestateEnable, $rentcarEnable, $trackAnyServiceEnable;
    $return = false;
    $eForConfig = explode(',', $eFor);
    if (1 === count($eForConfig)) {
        $eForConfig = $eForConfig[0];
    }

    $eForConfigArr = is_array($eForConfig) ? $eForConfig : [$eForConfig];

    if (('' === $eFor || 'General' === $eFor)
        || ('Yes' === $runnerEnable && 'Runner' === $eFor)
        || ('Yes' === $genieEnable && 'Genie' === $eFor)
        || ('Yes' === $biddingEnable && 'Bidding' === $eFor)
        || ('Yes' === $nearbyEnable && 'NearBy' === $eFor)
        || ('Yes' === $trackServiceEnable && 'TrackService' === $eFor)
        || ('Yes' === $rideShareEnable && 'RideShare' === $eFor)
        || ('Yes' === $flymodule && 'Fly' === $eFor)
        || (ENABLEKIOSKPANEL === 'Yes' && 'Kiosk' === $eFor)
        || ('YES' === strtoupper($deliverallEnable) && ('DeliverAll' === $eForConfig || in_array('DeliverAll', $eForConfigArr, true)))
        || ('YES' === strtoupper($rideEnable) && ('Ride' === $eForConfig || in_array('Ride', $eForConfigArr, true)))
        || ('YES' === strtoupper($deliveryEnable) && ('Delivery' === $eForConfig || 'Multi-Delivery' === $eForConfig || in_array('Delivery', $eForConfigArr, true)))
        || ('YES' === strtoupper($uberxService) && ('UberX' === $eForConfig || in_array('UberX', $eForConfigArr, true)))
        || ('YES' === strtoupper($rentitemEnable) && ('RentItem' === $eForConfig))
        || ('YES' === strtoupper($rentestateEnable) && ('RentEstate' === $eForConfig))
        || ('YES' === strtoupper($rentcarEnable) && ('RentCars' === $eForConfig))
        || ('Yes' === $trackAnyServiceEnable && 'TrackAnyService' === $eFor)) {
        $return = true;
    }

    return $return;
}
function permission($permission)
{
    global $rideEnable, $runnerEnable, $genieEnable, $biddingEnable, $nearbyEnable, $trackServiceEnable, $rideShareEnable, $flymodule, $deliverallEnable, $deliveryEnable, $uberxService, $rentitemEnable, $rentestateEnable, $rentcarEnable, $trackAnyServiceEnable;
    $groupPermissionData = [];
    foreach ($permission as $key11 => $value12) {
        $eForConfig = explode(',', $value12['eFor']);
        if (1 === count($eForConfig)) {
            $eForConfig = $eForConfig[0];
        }

        $eForConfigArr = is_array($eForConfig) ? $eForConfig : [$eForConfig];

        if ('view-user-outstanding-sort-group' === $value12['permission_name'] || 'view-app-screen-label' === $value12['permission_name'] || 'view-app-screenshot' === $value12['permission_name'] || 'view-app-home-settings' === $value12['permission_name'] || 'view-app-screen' === $value12['permission_name']) {
        } elseif (('' === $value12['eFor'] || 'General' === $value12['eFor']) || ('Yes' === $runnerEnable && 'Runner' === $value12['eFor']) || ('Yes' === $genieEnable && 'Genie' === $value12['eFor']) || ('Yes' === $biddingEnable && 'Bidding' === $value12['eFor']) || ('Yes' === $nearbyEnable && 'NearBy' === $value12['eFor']) || ('Yes' === $trackServiceEnable && 'TrackService' === $value12['eFor']) || ('Yes' === $rideShareEnable && 'RideShare' === $value12['eFor']) || ('Yes' === $flymodule && 'Fly' === $value12['eFor']) || (ENABLEKIOSKPANEL === 'Yes' && 'Kiosk' === $value12['eFor']) || ('YES' === strtoupper($deliverallEnable) && ('DeliverAll' === $eForConfig || in_array('DeliverAll', $eForConfigArr, true))) || ('YES' === strtoupper($rideEnable) && ('Ride' === $eForConfig || in_array('Ride', $eForConfigArr, true))) || ('YES' === strtoupper($deliveryEnable) && ('Delivery' === $eForConfig || 'Multi-Delivery' === $eForConfig || in_array('Delivery', $eForConfigArr, true))) || ('YES' === strtoupper($uberxService) && ('UberX' === $eForConfig || in_array('UberX', $eForConfigArr, true))) || ('YES' === strtoupper($rentitemEnable) && ('RentItem' === $eForConfig)) || ('YES' === strtoupper($rentestateEnable) && ('RentEstate' === $eForConfig)) || ('YES' === strtoupper($rentcarEnable) && ('RentCars' === $eForConfig)) || ('Yes' === $trackAnyServiceEnable && 'TrackAnyService' === $value12['eFor'])) {
            $groupPermissionData[$key11] = $value12;
        }
    }

    return $groupPermissionData;
}

function permission_html($permissions)
{
    global $id, $administratorsData, $MODULES_OBJ, $uberxService, $SHOW_CITY_FIELD, $uberXService, $displayGrpArr, $keyIdName, $subKeyIdName, $edit_data, $key;
    $ky = $key;
    $str = '';
    $str .= '<div class="row permissions-input">';
    $countPermission = 0;
    foreach ($permissions as $permission) {
        // added by SP direct use permission name discussed by CD sir
        if ('Yes' !== $SHOW_CITY_FIELD && ('create-city' === $permission['permission_name'] || 'edit-city' === $permission['permission_name'] || 'delete-city' === $permission['permission_name'] || 'view-city' === $permission['permission_name'] || 'update-status-city' === $permission['permission_name'])) {
            continue;
        }

        if ('manage-our-service-menu' === $permission['permission_name'] && ENABLE_OUR_SERVICES_MENU === 'No') {
            continue;
        }

        if (0 === $uberXService && 'UberX' === $permission['eFor']) {
            continue;
        }
        if (in_array($permission['display_group_id'], $displayGrpArr, true)) {
            $permission_name = $permission['permission_name'];
            if ($permission['name']) {
                $permission_name = $permission['name'];  // tem
                // $permission_name = $permission['permission_name'] . " (  "  . $permission['name'] . ") ";
            }
            $disabled = '';
            if ($administratorsData === $id && in_array($permission['id'], $edit_data['permissions'], true)) {
                // $disabled = 'onclick="return false" ';
            }
            ++$countPermission;
            $str .= '<div class="col-sm-3 permitions-item">';
            $str .= '<label class="permission-name" attr-permissions="'.$permission['permission_name'].'" attr-type="'.$permission['eType'].'">';
            $str .= '<input '.$disabled.' class="permitions_chk" type="checkbox" name="permission_ids[]" attr-group="';
            if (!empty($subKeyIdName)) {
                $str .= $subKeyIdName;
            } else {
                $str .= $keyIdName;
            }
            $str .= '"';
            in_array($permission['id'], $edit_data['permissions'], true) ? $str .= ' checked ' : '';
            $str .= ' value='.$permission['id'].' >  '.$permission_name;
            $str .= '</label></div>';
        }
    }
    $permissionCount = [];
    $permissionCount['count'] = $countPermission;
    $permissionCount['id'] = trim($keyIdName);
    $str .= '</div>';
    $arr['str'] = $str;
    $arr['permissionCount'] = $permissionCount;

    return $arr;
}
$getDisplayGroupData = $obj->MySQLSelect("SELECT * FROM admin_pro_permission_display_groups WHERE eStatus='Active' {$ssql}");

$permission_groups = $displayGrpArr = [];
for ($r = 0; $r < count($getDisplayGroupData); ++$r) {
    if (isset($getDisplayGroupData[$r]['vDispalyAppType']) && 'All' !== $getDisplayGroupData[$r]['vDispalyAppType']) {
        $groupArr = explode(',', $getDisplayGroupData[$r]['vDispalyAppType']);
        if (in_array($APP_TYPE, $groupArr, true)) {
            $displayGrpArr[] = $getDisplayGroupData[$r]['id'];
        }
    } else {
        $displayGrpArr[] = $getDisplayGroupData[$r]['id'];
    }
    $permission_groups[$getDisplayGroupData[$r]['id']] = $getDisplayGroupData[$r]['name'];
}

?>
<!DOCTYPE html>
<!--[if IE 8]>
<html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]>
<html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en"> <!--<![endif]-->
<!-- BEGIN HEAD-->
<head>
    <meta charset="UTF-8"/>
    <title><?php echo $SITE_NAME; ?> | Admin <?php echo $action; ?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <link href="assets/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet"/>
    <?php
    include_once 'global_files.php';
?>
    <!-- On OFF switch -->
    <link href="../assets/css/jquery-ui.css" rel="stylesheet"/>
    <link rel="stylesheet" href="../assets/plugins/switch/static/stylesheets/bootstrap-switch.css"/>
    <link rel="stylesheet" href="css/select2/select2.min.css"></link>
    <style type="text/css">
        .select2-selection--multiple {
            height: auto !important;
        }

        .form-group .row {
            padding: 0;
        }

        .sub-permission-group-title {
            font-size: 15px;
            font-weight: 600;
            margin: 25px 0 10px;
            padding: 5px 10px;
        }

        .panel-body .sub-permission-group:first-child .sub-permission-group-title {
            margin-top: 0;
        }

        .permissions-input {
            margin: 0 !important;
        }

        .permission-name {
            text-transform: capitalize;
            cursor: pointer;
            position: relative;
            padding-left: 25px;
            margin-bottom: 10px;
        }

        .permission-name .permitions_chk {
            width: 1rem;
            height: 1.25rem;
            position: absolute;
            left: 0;
            margin: 0;
        }

        .permission-note {
            color: #ff0000;
            font-weight: 500;
            padding-left: 15px;
        }
    </style>
</head>
<!-- END  HEAD-->
<!-- BEGIN BODY-->
<body class="padTop53 ">
<!-- MAIN WRAPPER -->
<div id="wrap">
    <?php
include_once 'header.php';

include_once 'left_menu.php';
?>
    <!--PAGE CONTENT -->
    <div id="content">
        <div class="inner">
            <div class="row">
                <div class="col-lg-12">
                    <h2><?php echo $action; ?> Admin Groups</h2>
                    <a class="back_link" href="admin_groups.php">
                        <input type="button" value="Back to Listing" class="add-btn">
                    </a>
                </div>
            </div>
            <hr/>
            <div class="body-div">
                <div class="form-group">
                    <?php if (2 === $success) { ?>
                        <div class="alert alert-danger alert-dismissable">
                            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                            <?php echo $langage_lbl_admin['LBL_EDIT_DELETE_RECORD']; ?>
                        </div>
                        <br/>
                    <?php } ?>
                    <?php if (3 === $success) { ?>
                        <div class="alert alert-danger alert-dismissable">
                            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                            <?php print_r($error); ?>
                        </div>
                        <br/>
                    <?php } ?>
                    <form name="_admin_form" id="_admin_form" method="post" action="" enctype="multipart/form-data">
                        <input type="hidden" name="actionOf" id="actionOf" value="<?php echo $action; ?>"/>
                        <input type="hidden" name="id" id="iGroupId" value="<?php echo $id; ?>"/>
                        <input type="hidden" name="previousLink" id="previousLink"
                               value=""/>
                        <input type="hidden" name="backlink" value="admin_groups.php"/>
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label>Group Name
                                        <span class="red"> *</span>
                                    </label>
                                    <input type="text" class="form-control" name="vGroup" id="vGroup"
                                           value="<?php echo $edit_data['vGroup']; ?>" placeholder="Group Name">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="panel panel-primary panel-group-permission">
                                    <div class="panel-heading clearfix" style="padding: 3px 3px 3px 15px;">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <h4 style="margin: 8px 0; ">Group Permissions</h4>
                                            </div>
                                            <div class="col-sm-6 input-group">
                                                <input type="text" class="serach_permission form-control" name=""
                                                       placeholder="Search in All Permissions">
                                                <span class="input-group-btn">
                                                    <button type="button" class="btn btn-info" onclick="selectAll()">Select All</button>
                                                    <button type="button" class="btn btn-danger"
                                                            onclick="deselectAll()">De-select All</button>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="panel-body">
                                        <?php
                                    $countArr = [];
// ksort($groupPermissionData);
foreach ($groupPermissionData as $key => $subCategory) {
    $keys = $key;
    $key = $permission_groups[$key];
    $keyIdName = str_replace([' ', '/'], '', $key);
    $countPermission = 0;
    ?>
                                            <div class="panel panel-info" id="<?php echo $keyIdName; ?>">
                                                <div class="panel-heading clearfix" style="padding: 3px 3px 3px 10px;">
                                                    <div class="row">
                                                        <div class="col-sm-8">
                                                            <h4 style="margin: 6px 0; "><?php echo empty($key) ? 'All' : $key; ?></h4>
                                                        </div>
                                                        <div class="col-md-4 input-group input-group-sm">
                                                            <input type="text" class="serach_permission form-control"
                                                                   name=""
                                                                   placeholder="Search <?php echo empty($key) ? '' : 'in '.$key; ?>">
                                                            <span class="input-group-btn">
                                                                <button type="button" class="btn btn-info"
                                                                        onclick="selectAll(this)">Select All</button>
                                                                <button type="button" class="btn btn-danger"
                                                                        onclick="deselectAll(this)">De-select All</button>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="panel-body">
                                                    <?php
            if (isset($subCategory['subgroup'])) {
                foreach ($subCategory['subgroup'] as $sub) {
                    $subKeyIdName = str_replace([' ', '/', ','], '', $sub['name'].random_int(0, getrandmax()));
                    ?>
                                                        <div class="sub-permission-group"
                                                             id="<?php echo $subKeyIdName; ?>">
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="well well-sm sub-permission-group-title">
                                                                        <?php echo $sub['name']; ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <?php
                    if ($sub['permission']) {
                        $permission_html = permission_html($sub['permission']);
                        echo $permission_html['str'];
                        $countArr[] = $permission_html['permissionCount'];
                    } ?> </div><?php
                }
            } else {
                $subKeyIdName = '';
                $permission_html = permission_html($subCategory['permission']);
                echo $permission_html['str'];
                $countArr[] = $permission_html['permissionCount'];
            } ?>
                                                    <br>
                                                    <span class="permission-note">Note : Please make sure to select "view-" permission if you are selecting any other permission for any section.</span>
                                                </div>
                                            </div>
                                            <?php
}
?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <input type="submit" class="btn btn-default" name="submit" id="submit"
                                       value="<?php if ('Add' === $action) { ?><?php echo $action; ?> Admin Group<?php } else { ?>Update<?php } ?>">
                                <input type="reset" value="Reset" class="btn btn-default">
                                <a href="admin_groups.php" class="btn btn-default back_link">Cancel</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--END PAGE CONTENT -->
</div>
<!--END MAIN WRAPPER -->
<?php
include_once 'footer.php';
?>
<script type="text/javascript" src="js/plugins/select2.min.js"></script>
</body>
<!-- END BODY-->
</html>
<script>
    //Added BY HJ On 07-03-2019 For Hide Main Group Section If No Any Permission Found Start
    var groupArr = '<?php echo str_replace("'", "\\'", json_encode($countArr)); ?>';
    var countJsonArr = JSON.parse(groupArr);
    for (var r = 0; r < countJsonArr.length; r++) {
        //console.log(countJsonArr[r]['count']);
        if (countJsonArr[r]['count'] <= 0) {
            //$("#" + countJsonArr[r]['id']).hide();
        }
    }
    //Added BY HJ On 07-03-2019 For Hide Main Group Section If No Any Permission Found End
    $(document).ready(function () {
        $('#permitions').select2({
            allowClear: true,
        });
        $('.serach_permission').keyup(function () {
            var value = $(this).val();
            var items = $(this).closest('.panel').find('.permitions-item')
            if (value != "" && value != undefined && value != null) {
                items.hide();
                items.each(function () {
                    var text = $(this).find('label').text().toLowerCase();

                    value = value.replace(' ', '-').replace('_', '-').toLowerCase();

                    if (text.search(value) >= 0) {
                        $(this).show();
                    }
                })
            } else {
                items.show();

            }

            if ($(this).closest('.panel').find('.panel').length > 0) {

                $(this).closest('.panel').find('.panel').find('.serach_permission').val("");

                $(this).closest('.panel').find('.panel').show();
                $(this).closest('.panel').find('.panel').each(function () {
                    if ($(this).find(".permitions-item:visible").length == 0) {
                        $(this).hide();
                    }
                })
            }

        });


        $('.permitions_chk').click(function () {

            if ($(this).is(":checked")) {

                var group = $(this).attr('attr-group');
                var items = $('#' + group).find('.permitions-item')

                var myString = $(this).parent().attr('attr-permissions');
                myString = indexingString('', myString);

                var i = 0;
                items.each(function () {
                    var text = $(this).find('label').attr('attr-type');
                    if (text == 'List') {
                        i++;
                    }
                })

                items.each(function () {
                    var text = $(this).find('label').attr('attr-type');
                    if (i == 1) {
                        if (text == 'List') {
                            $(this).find('label').find('input').prop('checked', true);
                        }
                    } else {
                        var strng = $(this).find('label').attr('attr-permissions');
                        var incStr = indexingString(strng, myString);

                        if (text == 'List' && incStr) {
                            $(this).find('label').find('input').prop('checked', true);
                        }
                    }
                })
            } else {
                var myString = $(this).parent().attr('attr-permissions');
                myString = indexingString('', myString);
                var group = $(this).attr('attr-group');
                var items = $('#' + group).find('.permitions_chk')
                var view_check = 0;
                var i = 0;
                items.each(function () {
                    var text = $(this).parent('label').attr('attr-type');
                    if (text == 'List') {
                        i++;
                    }
                })

                items.each(function () {
                    var incStr;
                    if (i == 1) {
                        if ($(this).is(":checked") && $(this).parent('label').attr('attr-type') != 'List') {
                            view_check = 1;
                        }
                    } else {
                        var strng = $(this).parent('label').attr('attr-permissions');
                        incStr = indexingString(strng, myString);
                        if ($(this).is(":checked") && $(this).parent('label').attr('attr-type') != 'List' && incStr) {
                            view_check = 1;
                        }
                    }
                })


                if (view_check == 1) {
                    var items = $('#' + group).find('.permitions-item')
                    items.each(function () {
                        var text = $(this).find('label').attr('attr-type');
                        let incStr;
                        if (i == 1) {
                            if (text == 'List') {
                                $(this).find('label').find('input').prop('checked', true);
                            }
                        } else {
                            var strng = $(this).find('label').attr('attr-permissions');
                            incStr = indexingString(strng, myString);
                            if (text == 'List' && incStr) {
                                $(this).find('label').find('input').prop('checked', true);
                            }
                        }


                    })
                }
            }

        });
    });


    function indexingString(strng, myString) {

        let incStr;
        if (strng != '') {
            incStr = strng.includes('status');
            if (incStr) {
                incStr = (myString == strng.substring(strng.indexOf('status-') + 7));
            } else {
                incStr = (myString == strng.substring(strng.indexOf('-') + 1));
            }
        } else {

            incStr = myString.includes('status');
            if (incStr) {
                incStr = myString.substring(myString.indexOf('status-') + 7);
            } else {
                incStr = myString.substring(myString.indexOf('-') + 1);
            }
        }
        return incStr;
    }

    // jquery validation
    $('#_admin_form').validate({
        rules: {
            vGroup: {
                required: true
            }
        },
        messages: {
            vGroup: {
                /*  required: 'Please Enter Group Name.' */
            }
        }
    });

    function selectAll(ele) {
        if (ele == undefined) {
            $('.permitions_chk').prop('checked', true);
        } else {
            $(ele).closest('.panel').find('.permitions_chk:visible').prop('checked', true);
        }
    }

    function deselectAll(ele) {
        if (ele == undefined) {
            $('.permitions_chk').prop('checked', false);
        } else {
            $(ele).closest('.panel').find('.permitions_chk:visible').prop('checked', false);
        }
    }
</script>
