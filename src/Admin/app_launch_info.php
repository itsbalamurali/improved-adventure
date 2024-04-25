<?php
include_once '../common.php';

if ('User' === $_REQUEST['option']) {
    $_REQUEST['option'] = 'Passenger';
}

if ('Provider' === $_REQUEST['option']) {
    $_REQUEST['option'] = 'Driver';
}
$option = $_REQUEST['option'] ?? 'Passenger';
$queryString = '';
if (isset($option) && !empty($option)) {
    $queryString = 'option='.$option;
    $script = 'app_launch_info_'.$option;
    $view_permission = 'manage-'.strtolower($option).'-app-launch-info';
    $create_permission = 'create-'.strtolower($option).'-app-launch-info';
    $edit_permission = 'edit-'.strtolower($option).'-app-launch-info';
    $delete_permission = 'delete-'.strtolower($option).'-app-launch-info';
    $update_status_permission = 'update-status-'.strtolower($option).'-app-launch-info';
}

if (!$userObj->hasPermission($view_permission)) {
    $userObj->redirect();
}
$default_lang = $LANG_OBJ->FetchSystemDefaultLang();
// Delete
$hdn_del_id = $_REQUEST['hdn_del_id'] ?? '';
// Update eStatus
$iImageId = $id = $_REQUEST['id'] ?? '';
$status = $_REQUEST['status'] ?? '';
// sort order
$flag = $_GET['flag'] ?? '';
$tbl_name = 'app_launch_info';
// $script = 'app_launch_info';
$ssql_deliverall = '';

// delete record
if ('' !== $hdn_del_id) {
    $vImage = $_REQUEST['vImage'] ?? '';
    if (SITE_TYPE !== 'Demo') {
        $data_q = 'SELECT Max(iDisplayOrder) AS iDisplayOrder FROM `'.$tbl_name."` WHERE iImageId = '{$hdn_del_id}'".$ssql_deliverall;
        $data_rec = $obj->MySQLSelect($data_q);
        // echo '<pre>'; print_r($data_rec); echo '</pre>';die;
        $order = $data_rec[0]['iDisplayOrder'] ?? 0;
        $data_logo = $obj->MySQLSelect('SELECT iDisplayOrder FROM '.$tbl_name." WHERE iImageId = '".$hdn_del_id."'".$ssql_deliverall);
        if (count($data_logo) > 0) {
            $iDisplayOrder = $data_logo[0]['iDisplayOrder'] ?? '';
            $obj->sql_query('DELETE FROM `'.$tbl_name."` WHERE iImageId = '".$hdn_del_id."'".$ssql_deliverall);
            if (file_exists($tconfig['tsite_upload_app_launch_images_path'].$vImage)) {
                unlink($tconfig['tsite_upload_app_launch_images_path'].$vImage);
            }
            if ($iDisplayOrder < $order) {
                for ($i = $iDisplayOrder + 1; $i <= $order; ++$i) {
                    $obj->sql_query('UPDATE '.$tbl_name.' SET iDisplayOrder = '.($i - 1).' WHERE iDisplayOrder = '.$i.' '.$ssql_deliverall);
                }
            }
        }
        $_SESSION['success'] = '1';
        $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_DELETE_MSG'];
        header('Location:app_launch_info.php?option='.$option);

        exit;
    }
    $_SESSION['success'] = '2';
    header('Location:app_launch_info.php?option='.$option);

    exit;
}
if (0 !== $iImageId && !empty($flag)) {
    if ('up' === $flag) {
        $sel_order = $obj->MySQLSelect('SELECT iDisplayOrder FROM '.$tbl_name." WHERE iImageId ='".$iImageId."'".$ssql_deliverall);
        $order_data = $sel_order[0]['iDisplayOrder'] ?? 0;
        $val = $order_data - 1;
        if ($val > 0) {
            $obj->MySQLSelect('UPDATE '.$tbl_name." SET iDisplayOrder='".$order_data."' WHERE iDisplayOrder='".$val."'".$ssql_deliverall);
            $obj->MySQLSelect('UPDATE '.$tbl_name." SET iDisplayOrder='".$val."' WHERE iImageId = '".$iImageId."'".$ssql_deliverall);
        }
    } elseif ('down' === $flag) {
        $sel_order = $obj->MySQLSelect('SELECT iDisplayOrder FROM '.$tbl_name." WHERE iImageId ='".$iImageId."'".$ssql_deliverall);
        $order_data = $sel_order[0]['iDisplayOrder'] ?? 0;
        $val = $order_data + 1;
        $obj->MySQLSelect('UPDATE '.$tbl_name." SET iDisplayOrder='".$order_data."' WHERE iDisplayOrder='".$val."'".$ssql_deliverall);
        $obj->MySQLSelect('UPDATE '.$tbl_name." SET iDisplayOrder='".$val."' WHERE iImageId = '".$iImageId."'".$ssql_deliverall);
    }

    $oCache->flushData();
    $GCS_OBJ->updateGCSData();

    $_SESSION['success'] = '1';
    $_SESSION['var_msg'] = $langage_lbl_admin['LBL_Record_Updated_successfully'];
    header('Location:app_launch_info.php?option='.$option);

    exit;
}
if ('' !== $iImageId && '' !== $status) {
    if (SITE_TYPE !== 'Demo') {
        $query = 'UPDATE `'.$tbl_name."` SET eStatus = '".$status."' WHERE iImageId = '".$iImageId."'".$ssql_deliverall;
        $obj->sql_query($query);

        $oCache->flushData();
        $GCS_OBJ->updateGCSData();

        $_SESSION['success'] = '1';
        $_SESSION['var_msg'] = $langage_lbl_admin['LBL_Record_Updated_successfully'];
        header('Location:app_launch_info.php?option='.$option);

        exit;
    }
    $_SESSION['success'] = '2';
    header('Location:app_launch_info.php?option='.$option);

    exit;
}
$sql = "SELECT *, JSON_UNQUOTE(JSON_EXTRACT(tTitle, '$.tTitle_".$default_lang."')) as tTitle, JSON_UNQUOTE(JSON_EXTRACT(tSubtitle, '$.tSubtitle_".$default_lang."')) as tSubtitle FROM ".$tbl_name.' WHERE 1 = 1 '.$ssql_deliverall." AND eUserType = '{$option}' ORDER BY iDisplayOrder";
$db_data = $obj->MySQLSelect($sql);
?>
<!DOCTYPE html>
<!--[if IE 8]>
<html lang="en" class="ie8">
<![endif]-->
<!--[if IE 9]>
<html lang="en" class="ie9">
<![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD-->
<head>
    <meta charset="UTF-8"/>
    <title>Admin | App Intro Screen</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <?php include_once 'global_files.php'; ?>
    <link href="../assets/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet"/>

    <style type="text/css">
        #dataTables-example td {
            vertical-align: middle;
        }
    </style>
</head>
<!-- END  HEAD-->
<!-- BEGIN BODY-->
<body class="padTop53 ">
<!-- MAIN WRAPPER -->
<div id="wrap">
    <?php include_once 'header.php'; ?>
    <?php include_once 'left_menu.php'; ?>
    <!--PAGE CONTENT -->
    <div id="content">
        <div class="inner">
            <div class="row">
                <div class="col-lg-12">
                    <h2>Intro Screen - <?php
                        if ('Passenger' === $option) {
                            echo $langage_lbl_admin['LBL_PASSANGER_TXT_ADMIN'];
                        } elseif ('Driver' === $option) {
                            echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'];
                        } elseif ('Company' === $option) {
                            echo $langage_lbl_admin['LBL_RESTAURANT_TXT_ADMIN'];
                        } elseif ('TrackServiceUser' === $option) {
                            echo 'Tracking';
                        } else {
                            echo 'General';
                        }
?> App
                    </h2>
                    <?php if ($userObj->hasPermission($create_permission)) { ?>
                    <a href="app_launch_info_action.php?<?php echo $queryString; ?>">
                        <input type="button" value="Add Image" class="add-btn">
                    </a>
                    <?php } ?>
                </div>
            </div>
            <hr/>
            <?php include 'valid_msg.php'; ?>
            <form name="frmsearch" id="frmsearch" action="" method="post">
                <table style="display: none" width="100%" border="0" cellpadding="0" cellspacing="0"
                       class="admin-nir-table">
                    <tbody>
                    <tr>
                        <td width="100px" style="padding-top: 5px">
                            <label for="textfield">
                                <strong>Applicable For:</strong>
                            </label>
                        </td>
                        <td width="160px" class="padding-right10">
                            <select name="option" id="option" class="form-control">
                                <option value="Passenger" <?php
        if ('Passenger' === $option) {
            echo 'selected';
        }
?> ><?php echo $langage_lbl_admin['LBL_PASSANGER_TXT_ADMIN']; ?></option>
                                <option value="Driver" <?php
if ('Driver' === $option) {
    echo 'selected';
}
?> ><?php echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']; ?></option>
                                <?php if ($MODULES_OBJ->isDeliverAllFeatureAvailable()) { ?>
                                    <option value="Company" <?php
    if ('Company' === $option) {
        echo 'selected';
    }
                                    ?> ><?php echo $langage_lbl_admin['LBL_RESTAURANT_TXT_ADMIN']; ?></option>
                                <?php } ?>
                                <option value="General" <?php
                                if ('General' === $option) {
                                    echo 'selected';
                                }
?> >General
                                </option>
                            </select>
                        </td>
                        <td width="">
                            <input type="submit" value="Search" class="btnalt button11" id="Search" name="Search"
                                   title="Search"/>
                            <input type="button" value="Reset" class="btnalt button11"
                                   onClick="window.location.href = 'app_launch_info.php'"/>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>
            <div class="table-list">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                App Intro Screen
                            </div>
                            <div class="panel-body">
                                <div>
                                    <table class="table responsive table-striped table-bordered table-hover"
                                           id="dataTables-example" align="center">
                                        <thead>
                                        <tr>
                                            <th class="text-center">Image</th>
                                            <th class="text-center">Title</th>
                                            <th class="text-center">Description</th>
                                            <th style="display: none" class="text-center">Applicable For</th>
                                            <th class="text-center">Display Order</th>
                                           <!--  <?php if ($userObj->hasPermission($update_status_permission)) { ?>
                                            <th class="text-center">Status</th>
                                            <?php } if ($userObj->hasPermission($edit_permission)) { ?>
                                            <th class="text-center">Edit</th>
                                            <?php } if ($userObj->hasPermission($delete_permission)) { ?>
                                            <th class="text-center">Delete</th>
                                            <?php } ?> -->
                                            <th class="text-center">Status</th>
                                             <th class="text-center">Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
        for ($i = 0; $i < count($db_data); ++$i) {
            $iImageId = $db_data[$i]['iImageId'];
            $vImage = $db_data[$i]['vImage'];
            $tTitle = $db_data[$i]['tTitle'];
            $tSubtitle = $db_data[$i]['tSubtitle'];
            $eApplicableFor = $db_data[$i]['eUserType'];
            $iDisplayOrder = $db_data[$i]['iDisplayOrder'];
            $eStatus = $db_data[$i]['eStatus'];
            $checked = ('Active' === $eStatus) ? 'checked' : '';
            ?>
                                            <tr class="gradeA">
                                                <td width="10%" align="center">
                                                    <?php if ('' !== $vImage && file_exists($tconfig['tsite_upload_app_launch_images_path'].$vImage)) { ?>
                                                        <img src="<?php echo $tconfig['tsite_url'].'resizeImg.php?w=150&src='.$tconfig['tsite_upload_app_launch_images'].$vImage; ?>"
                                                             width="50">
                                                    <?php } else {
                                                        echo $vImage;
                                                    } ?>
                                                </td>
                                                <td width="20%" align="center"><?php echo $tTitle; ?></td>
                                                <td align="center">
                                                    <?php if (str_word_count($tSubtitle) <= 20) {
                                                        echo $tSubtitle;
                                                    } else {
                                                        echo implode(' ', array_slice(explode(' ', $tSubtitle), 0, 20)).' ...';
                                                    } ?>
                                                </td>
                                                <td style="display: none" align="center">
                                                    <?php
                                                    if ('Passenger' === $eApplicableFor) {
                                                        echo $langage_lbl_admin['LBL_PASSANGER_TXT_ADMIN'];
                                                    } elseif ('Driver' === $eApplicableFor) {
                                                        echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'];
                                                    } elseif ('Company' === $eApplicableFor) {
                                                        echo $langage_lbl_admin['LBL_RESTAURANT_TXT_ADMIN'];
                                                    } else {
                                                        echo $langage_lbl_admin['LBL_ALL'];
                                                    }
            ?>
                                                </td>
                                                <td width="10%" align="center">
                                                    <?php if (1 !== $iDisplayOrder && $i > 0) { ?>
                                                        <a href="app_launch_info.php?id=<?php echo $iImageId; ?>&flag=up<?php echo ('' !== $sid) ? '&'.$sid : ''; ?>&option=<?php echo $option; ?>"
                                                           class="btn btn-warning">
                                                            <i class="icon-arrow-up"></i>
                                                        </a>
                                                    <?php }
                                                    if ($iDisplayOrder !== $count_all && $i < count($db_data) - 1) { ?>
                                                        <a href="app_launch_info.php?id=<?php echo $iImageId; ?>&flag=down<?php echo ('' !== $sid) ? '&'.$sid : ''; ?>&option=<?php echo $option; ?>"
                                                           class="btn btn-warning">
                                                            <i class="icon-arrow-down"></i>
                                                        </a>
                                                    <?php } ?>
                                                </td>
                                                <!-- <?php if ($userObj->hasPermission($update_status_permission)) { ?>
                                                <td width="10%" align="center">
                                                    <a href="app_launch_info.php?id=<?php echo $iImageId; ?>&status=<?php echo ('Active' === $eStatus) ? 'Inactive' : 'Active'; ?>&option=<?php echo $option; ?>">
                                                        <button class="btn">
                                                            <i class="<?php echo ('Active' === $eStatus) ? 'icon-eye-open' : 'icon-eye-close'; ?>"></i> <?php echo $eStatus; ?>
                                                        </button>
                                                    </a>
                                                </td>
                                                <?php } if ($userObj->hasPermission($edit_permission)) { ?>
                                                <td width="5%" align="center">
                                                    <a href="app_launch_info_action.php?id=<?php echo $iImageId; ?>&<?php echo $queryString; ?>">
                                                        <button class="btn btn-primary">
                                                            <i class="icon-pencil icon-white"></i>
                                                            Edit
                                                        </button>
                                                    </a>
                                                </td>
                                                <?php } if ($userObj->hasPermission($delete_permission)) { ?>
                                                <td width="5%" align="center">
                                                    <form name="delete_form" id="delete_form" method="post" action=""
                                                          onsubmit="return confirm_delete()" class="margin0">
                                                        <input type="hidden" name="hdn_del_id" id="hdn_del_id"
                                                               value="<?php echo $iImageId; ?>">
                                                        <input type="hidden" name="vImage" id="vImage"
                                                               value="<?php echo $vImage; ?>">
                                                        <button class="btn btn-danger">
                                                            <i class="icon-remove icon-white"></i>
                                                            Delete
                                                        </button>
                                                    </form>
                                                </td>
                                                <?php } ?> -->
                                                <td align="center">
                                                    <?php
                                                    if ('Active' === $eStatus) {
                                                        $dis_img = 'img/active-icon.png';
                                                    } elseif ('Inactive' === $eStatus) {
                                                        $dis_img = 'img/inactive-icon.png';
                                                    } elseif ('Deleted' === $eStatus) {
                                                        $dis_img = 'img/delete-icon.png';
                                                    }
            ?>
                                                    <img src="<?php echo $dis_img; ?>" alt="<?php echo $eStatus; ?>" data-toggle="tooltip" title="<?php echo $eStatus; ?>">
                                                </td>
                                                <td align="center" style="text-align:center;" class="action-btn001">
                                                    <div class="share-button openHoverAction-class" style="display: block;">
                                                        <label class="entypo-export"><span><img src="images/settings-icon.png"  alt=""></span></label>
                                                        <div class="social show-moreOptions openPops_<?php echo $iImageId; ?>">
                                                            <ul>
                                                                <?php if ($userObj->hasPermission($edit_permission)) { ?>
                                                                <li class="entypo-twitter" data-network="twitter">
                                                                    <a href="app_launch_info_action.php?id=<?php echo $iImageId; ?>&<?php echo $queryString; ?>" data-toggle="tooltip" title="Edit">
                                                                    <img src="img/edit-icon.png" alt="Edit">
                                                                    </a></li>
                                                                <?php }  ?>
                                                                <?php if ($userObj->hasPermission($update_status_permission)) { ?>
                                                                    <li class="entypo-facebook" data-network="facebook">

                                                                        <a href="javascript:void(0);" onClick="window.location.href='app_launch_info.php?id=<?php echo $iImageId; ?>&status=Active&option=<?php echo $option; ?>'" data-toggle="tooltip" title="Activate">
                                                                            <img src="img/active-icon.png" alt="<?php echo $eStatus; ?>">
                                                                        </a>
                                                                    </li>
                                                                    <li class="entypo-gplus" data-network="gplus">
                                                                        <a href="javascript:void(0);" onClick="window.location.href='app_launch_info.php?id=<?php echo $iImageId; ?>&status=Inactive&option=<?php echo $option; ?>'" data-toggle="tooltip" title="Deactivate">
                                                                            <img src="img/inactive-icon.png" alt="<?php echo $eStatus; ?>">
                                                                        </a>
                                                                    </li>
                                                                <?php } ?>
                                                                <?php if ($userObj->hasPermission($delete_permission)) {  ?>
                                                                    <li class="entypo-gplus" data-network="gplus"><a href="javascript:void(0);" onClick="confirm_delete('<?php echo $iImageId; ?>','<?php echo $vImage; ?>','<?php echo $option; ?>');" data-toggle="tooltip"  title="Delete">
                                                                            <img src="img/delete-icon.png"   alt="Delete">
                                                                        </a></li>
                                                                <?php } ?>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php }
        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--TABLE-END-->
                </div>
            </div>
        </div>
    </div>
    <!--END PAGE CONTENT -->
</div>
<!--END MAIN WRAPPER -->
<?php include_once 'footer.php'; ?>
<script src="../assets/plugins/dataTables/jquery.dataTables.js"></script>
<script src="../assets/plugins/dataTables/dataTables.bootstrap.js"></script>
<script>
    $(document).ready(function () {
        $('#dataTables-example').dataTable(
            {
                "bSort": false,
            }
        );
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

    function confirm_delete(iImageId,vImage,option) {
        var confirm_ans = confirm("Are You sure You want to Delete Banner?");
        if (confirm_ans == true) {
            window.location.href = 'app_launch_info.php?hdn_del_id='+iImageId+'&vImage='+vImage+'&option='+option;
        }
    }

</script>
</body>
<!-- END BODY-->
</html>