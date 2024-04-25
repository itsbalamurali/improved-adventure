<?php
include_once '../common.php';
$permission_banner = 'hotel-banner';

$permission_banner_view = 'view-'.$permission_banner;
$permission_banner_create = 'create-'.$permission_banner;
$permission_banner_edit = 'edit-'.$permission_banner;
$permission_banner_delete = 'delete-'.$permission_banner;
$permission_banner_update_status = 'update-status-'.$permission_banner;

if (!$userObj->hasPermission($permission_banner_view)) {
    $userObj->redirect();
}
$default_lang = $LANG_OBJ->FetchSystemDefaultLang();
// Delete
$hdn_del_id = $_REQUEST['hdn_del_id'] ?? '';
// Update eStatus
$iUniqueId = $_GET['iUniqueId'] ?? '';
$status = $_GET['status'] ?? '';
// sort order
$flag = $_GET['flag'] ?? '';
$id = $_GET['id'] ?? '';
$tbl_name = 'hotel_banners';
$script = 'hotel_banners';
// delete record
if ('' !== $hdn_del_id) {
    if (SITE_TYPE !== 'Demo') {
        $data_q = 'SELECT Max(iDisplayOrder) AS iDisplayOrder FROM `'.$tbl_name."` WHERE vCode = '".$default_lang."'";
        $data_rec = $obj->MySQLSelect($data_q);
        $order = $data_rec[0]['iDisplayOrder'] ?? 0;
        $data_logo = $obj->MySQLSelect('SELECT iDisplayOrder FROM '.$tbl_name." WHERE iUniqueId = '".$hdn_del_id."' AND vCode = '".$default_lang."'");
        if (count($data_logo) > 0) {
            $iDisplayOrder = $data_logo[0]['iDisplayOrder'] ?? '';
            $obj->sql_query('DELETE FROM `'.$tbl_name."` WHERE iUniqueId = '".$hdn_del_id."'");
            if ($iDisplayOrder < $order) {
                for ($i = $iDisplayOrder + 1; $i <= $order; ++$i) {
                    $obj->sql_query('UPDATE '.$tbl_name.' SET iDisplayOrder = '.($i - 1).' WHERE iDisplayOrder = '.$i);
                }
            }
        }
        $_SESSION['success'] = '1';
        $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_DELETE_MSG'];
        header('Location:hotel_banner.php');

        exit;
    }
    $_SESSION['success'] = '2';
    header('Location:hotel_banner.php');

    exit;
}
if (0 !== $id) {
    if ('up' === $flag) {
        $sel_order = $obj->MySQLSelect('SELECT iDisplayOrder FROM '.$tbl_name." WHERE iUniqueId ='".$id."' AND vCode = '".$default_lang."'");
        $order_data = $sel_order[0]['iDisplayOrder'] ?? 0;
        $val = $order_data - 1;
        if ($val > 0) {
            $obj->MySQLSelect('UPDATE '.$tbl_name." SET iDisplayOrder='".$order_data."' WHERE iDisplayOrder='".$val."'");
            $obj->MySQLSelect('UPDATE '.$tbl_name." SET iDisplayOrder='".$val."' WHERE iUniqueId = '".$id."'");
        }
    } elseif ('down' === $flag) {
        $sel_order = $obj->MySQLSelect('SELECT iDisplayOrder FROM '.$tbl_name." WHERE iUniqueId ='".$id."' AND vCode = '".$default_lang."'");
        $order_data = $sel_order[0]['iDisplayOrder'] ?? 0;
        $val = $order_data + 1;
        $obj->MySQLSelect('UPDATE '.$tbl_name." SET iDisplayOrder='".$order_data."' WHERE iDisplayOrder='".$val."'");
        $obj->MySQLSelect('UPDATE '.$tbl_name." SET iDisplayOrder='".$val."' WHERE iUniqueId = '".$id."'");
    }
    header('Location:hotel_banner.php');
}
if ('' !== $iUniqueId && '' !== $status) {
    if (SITE_TYPE !== 'Demo') {
        $query = 'UPDATE `'.$tbl_name."` SET eStatus = '".$status."' WHERE iUniqueId = '".$iUniqueId."'";
        $obj->sql_query($query);
    } else {
        $_SESSION['success'] = '2';
        header('Location:hotel_banner.php');

        exit;
    }
}
$sql = 'SELECT * FROM '.$tbl_name." WHERE vCode = '".$default_lang."' ORDER BY iDisplayOrder";
$db_data = $obj->MySQLSelect($sql);
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
    <title>Admin | Hotel Banners</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <?php include_once 'global_files.php'; ?>
    <link href="../assets/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet"/>
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
                    <h2>Hotel Banner</h2>
                    <?php if ($userObj->hasPermission($permission_banner_create)) { ?>
                        <a href="hotel_banner_action.php">
                            <input type="button" value="Add Hotel Banner" class="add-btn">
                        </a>
                    <?php } ?>
                </div>
            </div>
            <hr/>
            <?php include 'valid_msg.php'; ?>
            <div class="table-list">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                Hotel Banner
                            </div>
                            <div class="panel-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered table-hover"
                                           id="dataTables-example">
                                        <thead>
                                        <tr>
                                            <th>Image</th>
                                            <th>Title</th>
                                            <th>Order</th>
                                            <!-- <?php if ($userObj->hasPermission('update-status-hotel-banner')) { ?>
                                                <th>Status</th>
                                            <?php } ?>

                                            <?php if ($userObj->hasPermission('edit-hotel-banner')) { ?>
                                                <th>Edit</th>
                                            <?php } ?>

                                            <?php if ($userObj->hasPermission('delete-hotel-banner')) { ?>
                                                <th>Delete</th>
                                            <?php } ?> -->
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $count_all = count($db_data);
if ($count_all > 0) {
    for ($i = 0; $i < $count_all; ++$i) {
        $vTitle = $db_data[$i]['vTitle'];
        $vImage = $db_data[$i]['vImage'];
        $iDisplayOrder = $db_data[$i]['iDisplayOrder'];
        $eStatus = $db_data[$i]['eStatus'];
        $iUniqueId = $db_data[$i]['iUniqueId'];
        $checked = ('Active' === $eStatus) ? 'checked' : '';
        ?>
                                                <tr class="gradeA">
                                                    <td width="10%" align="center">
                                                        <?php if ('' !== $vImage && file_exists($tconfig['tsite_upload_images_hotel_banner_path'].'/'.$vImage)) { ?>
                                                            <img src="<?php echo $tconfig['tsite_url'].'resizeImg.php?h=50&src='.$tconfig['tsite_upload_images_hotel_banner'].'/'.$vImage; ?>">
                                                        <?php } else {
                                                            echo $vImage;
                                                        }
        ?>
                                                    </td>
                                                    <td><?php echo $vTitle; ?></td>
                                                    <td width="10%" align="center">
                                                        <?php if (1 !== $iDisplayOrder) { ?>
                                                            <a href="hotel_banner.php?id=<?php echo $iUniqueId; ?>&flag=up">
                                                                <button class="btn btn-warning">
                                                                    <i class="icon-arrow-up"></i>
                                                                </button>
                                                            </a>
                                                        <?php
                                                        }
        if ($iDisplayOrder !== $count_all) { ?>
                                                            <a href="hotel_banner.php?id=<?php echo $iUniqueId; ?>&flag=down">
                                                                <button class="btn btn-warning">
                                                                    <i class="icon-arrow-down"></i>
                                                                </button>
                                                            </a>
                                                        <?php
        } ?>
                                                    </td>
                                                    <!-- <?php if ($userObj->hasPermission('update-status-hotel-banner')) { ?>
                                                        <td width="10%" align="center">
                                                            <a href="hotel_banner.php?iUniqueId=<?php echo $iUniqueId; ?>&status=<?php echo ('Active' === $eStatus) ? 'Inactive' : 'Active'; ?>">
                                                                <button class="btn">
                                                                    <i class="<?php echo ('Active' === $eStatus) ? 'icon-eye-open' : 'icon-eye-close'; ?>"></i> <?php echo $eStatus; ?>
                                                                </button>
                                                            </a>
                                                        </td>
                                                    <?php } ?>

                                                    <?php if ($userObj->hasPermission('edit-hotel-banner')) { ?>
                                                        <td width="10%" align="center">
                                                            <a href="hotel_banner_action.php?id=<?php echo $iUniqueId; ?>">
                                                                <button class="btn btn-primary">
                                                                    <i class="icon-pencil icon-white"></i>
                                                                    Edit
                                                                </button>
                                                            </a>
                                                        </td>
                                                    <?php } ?>

                                                    <?php if ($userObj->hasPermission('delete-hotel-banner')) { ?>
                                                        <td width="10%" align="center">

                                                            <form name="delete_form" id="delete_form" method="post"
                                                                  action="" onsubmit="return confirm_delete()"
                                                                  class="margin0">
                                                                <input type="hidden" name="hdn_del_id" id="hdn_del_id"
                                                                       value="<?php echo $iUniqueId; ?>">
                                                                <button class="btn btn-danger">
                                                                    <i class="icon-remove icon-white"></i>
                                                                    Delete
                                                                </button>
                                                            </form>
                                                        </td>
                                                    <?php } ?> -->
                                                    <td width="10%"  align="center">
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
                                                     <td width="10%" align="center" style="text-align:center;" class="action-btn001">
                                                            <div class="share-button openHoverAction-class" style="display: block;">
                                                                <label class="entypo-export"><span><img src="images/settings-icon.png"  alt=""></span></label>
                                                                <div class="social show-moreOptions openPops_<?php echo $iUniqueId; ?>">
                                                                    <ul>
                                                                        <?php if ($userObj->hasPermission($permission_banner_edit)) { ?>
                                                                        <li class="entypo-twitter" data-network="twitter">
                                                                            <a href="hotel_banner_action.php?id=<?php echo $iUniqueId; ?>" data-toggle="tooltip" title="Edit">
                                                                            <img src="img/edit-icon.png" alt="Edit">
                                                                            </a></li>
                                                                        <?php }  ?>
                                                                        <?php if ($userObj->hasPermission($permission_banner_update_status)) { ?>
                                                                            <li class="entypo-facebook" data-network="facebook">
                                                                                <a href="javascript:void(0);" onClick='window.location.href="hotel_banner.php?iUniqueId=<?php echo $iUniqueId; ?>&status=Active"' data-toggle="tooltip" title="Activate">
                                                                                    <img src="img/active-icon.png" alt="<?php echo $eStatus; ?>">
                                                                                </a>
                                                                            </li>
                                                                            <li class="entypo-gplus" data-network="gplus">
                                                                                <a href="javascript:void(0);" onClick='window.location.href="hotel_banner.php?iUniqueId=<?php echo $iUniqueId; ?>&status=Inactive"' data-toggle="tooltip" title="Deactivate">
                                                                                    <img src="img/inactive-icon.png" alt="<?php echo $eStatus; ?>">
                                                                                </a>

                                                                            </li>
                                                                        <?php } ?>
                                                                        <?php if ($userObj->hasPermission($permission_banner_delete)) {  ?>
                                                                            <li class="entypo-gplus" data-network="gplus"><a href="javascript:void(0);" onClick="confirm_delete('<?php echo $iUniqueId; ?>');" data-toggle="tooltip"  title="Delete">
                                                                                    <img src="img/delete-icon.png" alt="Delete">
                                                                                </a></li>
                                                                        <?php } ?>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </td>
                                                </tr>
                                            <?php
    }
} ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div> <!--TABLE-END-->
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

        $('#dataTables-example').dataTable({"bSort": false});

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
    function confirm_delete(iUniqueId,vCode) {

        var confirm_ans = confirm("Are You sure You want to Delete Banner?");

        if (confirm_ans == true) {
            window.location.href = 'hotel_banner.php?hdn_del_id='+iUniqueId;
        }
    }
	</script>

</body>

<!-- END BODY-->

</html>

