<?php
include_once '../common.php';

if (!$MODULES_OBJ->isEnableStorePhotoUploadFacility()) {
    $userObj->redirect();
}
$default_lang = $LANG_OBJ->FetchSystemDefaultLang();
// Delete
$hdn_del_id = $_POST['hdn_del_id'] ?? '';
// Update eStatus
$iUniqueId = $_GET['iUniqueId'] ?? '';
$status = $_GET['status'] ?? '';
// sort order
$flag = $_GET['flag'] ?? '';
$id = $_GET['id'] ?? '';
$sid = $_GET['sid'] ?? '';
$tbl_name = 'store_wise_banners';
$script = 'Store Wise Banner';
$ssql_deliverall = '';

$ssql_deliverall = ' AND iCompanyId = 0';
if (!empty($sid)) {
    $ssql_deliverall = ' AND iCompanyId = '.$sid;
}

$sid = (!empty($sid)) ? 'sid='.$sid : '';
// delete record
if ('' !== $hdn_del_id) {
    if (SITE_TYPE !== 'Demo') {
        $data_q = 'SELECT Max(iDisplayOrder) AS iDisplayOrder FROM `'.$tbl_name.'` WHERE '.$ssql_deliverall;
        $data_rec = $obj->MySQLSelect($data_q);

        $order = $data_rec[0]['iDisplayOrder'] ?? 0;

        $data_logo = $obj->MySQLSelect('SELECT iDisplayOrder FROM '.$tbl_name." WHERE iUniqueId = '".$hdn_del_id."'".$ssql_deliverall);

        if (count($data_logo) > 0) {
            $iDisplayOrder = $data_logo[0]['iDisplayOrder'] ?? '';
            $obj->sql_query('DELETE FROM `'.$tbl_name."` WHERE iUniqueId = '".$hdn_del_id."'".$ssql_deliverall);

            if ($iDisplayOrder < $order) {
                for ($i = $iDisplayOrder + 1; $i <= $order; ++$i) {
                    $obj->sql_query('UPDATE '.$tbl_name.' SET iDisplayOrder = '.($i - 1).' WHERE iDisplayOrder = '.$i.' '.$ssql_deliverall);
                }
            }
        }
    } else {
        $_SESSION['success'] = '2';
        header('Location:store_images.php');

        exit;
    }
}

if (0 !== $id) {
    if ('up' === $flag) {
        $sel_order = $obj->MySQLSelect('SELECT iDisplayOrder FROM '.$tbl_name." WHERE iUniqueId ='".$id."' AND iCompanyId = '".$iCompanyId."'".$ssql_deliverall);
        $order_data = $sel_order[0]['iDisplayOrder'] ?? 0;
        $val = $order_data - 1;
        if ($val > 0) {
            $obj->MySQLSelect('UPDATE '.$tbl_name." SET iDisplayOrder='".$order_data."' WHERE iDisplayOrder='".$val."'".$ssql_deliverall);
            $obj->MySQLSelect('UPDATE '.$tbl_name." SET iDisplayOrder='".$val."' WHERE iUniqueId = '".$id."'".$ssql_deliverall);
        }
    } elseif ('down' === $flag) {
        $sel_order = $obj->MySQLSelect('SELECT iDisplayOrder FROM '.$tbl_name." WHERE iUniqueId ='".$id."' AND iCompanyId = '".$iCompanyId."'".$ssql_deliverall);

        $order_data = $sel_order[0]['iDisplayOrder'] ?? 0;

        $val = $order_data + 1;
        $obj->MySQLSelect('UPDATE '.$tbl_name." SET iDisplayOrder='".$order_data."' WHERE iDisplayOrder='".$val."'".$ssql_deliverall);
        $obj->MySQLSelect('UPDATE '.$tbl_name." SET iDisplayOrder='".$val."' WHERE iUniqueId = '".$id."'".$ssql_deliverall);
    }
    header('Location:store_images.php'.(('' !== $sid) ? '?'.$sid : ''));
}

if ('' !== $iUniqueId && '' !== $status) {
    if (SITE_TYPE !== 'Demo') {
        $query = 'UPDATE `'.$tbl_name."` SET eStatus = '".$status."' WHERE iUniqueId = '".$iUniqueId."'".$ssql_deliverall;
        $obj->sql_query($query);
        header('Location:store_images.php'.(('' !== $sid) ? '?'.$sid : ''));

        exit;
    }
    $_SESSION['success'] = '2';
    header('Location:store_images.php');

    exit;
}

$sql = 'SELECT * FROM '.$tbl_name.' WHERE 1=1 '.$ssql_deliverall.' ORDER BY iDisplayOrder';
$db_data = $obj->MySQLSelect($sql);

    $catdata = serviceCategories;
$service_cat_data = json_decode($catdata, true);

$storeListArr = [];
$getStoreList = $obj->MySQLSelect("SELECT iServiceId,iCompanyId,vCompany,eStatus FROM company WHERE eStatus = 'Active' AND vCompany != '' AND iServiceId > 0 ORDER BY vCompany ASC");
foreach ($getStoreList as $value) {
    $storeListArr[$value['iCompanyId']] = $value['vCompany'];
}

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
                <meta charset="UTF-8" />
                <title>Admin | Store Images</title>
                <meta content="width=device-width, initial-scale=1.0" name="viewport" />
                <?php include_once 'global_files.php'; ?>
                <link href="../assets/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet" />
                <script type="text/javascript">
                    function confirm_delete()
                    {
                        var confirm_ans = confirm("Are You sure You want to Store image?");
                        return confirm_ans;
                        //document.getElementById(id).submit();
                    }
                </script>
                <style type="text/css">
                    #dataTables-example td {
                        vertical-align: middle;
                    }
                </style>
            </head>
            <!-- END  HEAD-->
            <!-- BEGIN BODY-->
            <body class="padTop53 " >
                <!-- MAIN WRAPPER -->
                <div id="wrap">
                    <?php include_once 'header.php'; ?>
                    <?php include_once 'left_menu.php'; ?>
                    <!--PAGE CONTENT -->
                    <div id="content">
                        <div class="inner">
                            <div class="row">
                                <div class="col-lg-12">
                                    <h2><?php echo $langage_lbl_admin['LBL_RESTAURANT_TXT_ADMIN']; ?> Images</h2>
                                    <a href="store_images_action.php<?php echo ('' !== $sid) ? '?'.$sid : ''; ?>"> <input type="button" value="Add Image" class="add-btn"> </a>
                                </div>
                            </div>
                            <hr />
                            <?php include 'valid_msg.php'; ?>
                            <div class="table-list">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="panel panel-default">
                                            <div class="panel-heading">
                                                <?php echo $langage_lbl_admin['LBL_RESTAURANT_TXT_ADMIN']; ?> Images
                                            </div>
                                            <div class="panel-body">
                                                <div class="table-responsive">
                                                    <table class="table table-striped table-bordered table-hover" id="dataTables-example"  align="center">
                                                        <thead>
                                                            <tr>
                                                                <th class="text-center">Image</th>
                                                                <!-- <th class="text-center">Title</th> -->
                                                                <th class="text-center"><?php echo $langage_lbl_admin['LBL_RESTAURANT_TXT_ADMIN']; ?> Name</th>
                                                                <!-- <th class="text-center">Order</th> -->
                                                                <th class="text-center">Status</th>
                                                                <th class="text-center">Edit</th>
                                                                <th class="text-center">Delete</th>
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
        $iCompanyId = $db_data[$i]['iCompanyId'];
        ?>
                                                            <tr class="gradeA">
                                                                <td width="10%" align="center">
                                                                    <?php if ('' !== $vImage && file_exists($tconfig['tsite_upload_images_panel'].'/'.$vImage)) { ?>
                                                                    <!-- <img src="<?php echo $tconfig['tsite_upload_images'].$vImage; ?>"  width="50"> -->
                                                                    <img src="<?php echo $tconfig['tsite_url'].'resizeImg.php?w=100&src='.$tconfig['tsite_upload_images'].$vImage; ?>"  width="50">
                                                                    <?php } else {
                                                                        echo $vImage;
                                                                    } ?>
                                                                </td>
                                                                <?php/*<td  align="center"><?php echo $vTitle; ?></td>*/ ?>
                                                                <td  align="center">
                                                                    <?php echo $storeListArr[$iCompanyId]; ?>
                                                                </td>
                                                                <?php/*<td width="10%" align="center">
                                                                    <?php if (1 !== $iDisplayOrder) { ?>
                                                                    <a href="store_images.php?id=<?php echo $iUniqueId; ?>&flag=up<?php echo ('' !== $sid) ? '&'.$sid : ''; ?>">
                                                                    <button class="btn btn-warning">
                                                                    <i class="icon-arrow-up"></i>
                                                                    </button>
                                                                    </a>
                                                                    <?php } if ($iDisplayOrder !== $count_all) { ?>
                                                                    <a href="store_images.php?id=<?php echo $iUniqueId; ?>&flag=down<?php echo ('' !== $sid) ? '&'.$sid : ''; ?>">
                                                                    <button class="btn btn-warning">
                                                                    <i class="icon-arrow-down"></i>
                                                                    </button>
                                                                    </a>
                                                                    <?php } ?>
                                                                </td>*/?>
                                                                <td width="10%" align="center">
                                                                    <a href="store_images.php?iUniqueId=<?php echo $iUniqueId; ?>&status=<?php echo ('Active' === $eStatus) ? 'Inactive' : 'Active'; ?><?php echo ('' !== $sid) ? '&'.$sid : ''; ?>">
                                                                        <!-- <button class="btn <?php echo ('Active' === $eStatus) ? 'btn-success' : 'btn-danger'; ?>"> -->
                                                                        <button class="btn">
                                                                        <i class="<?php echo ('Active' === $eStatus) ? 'icon-eye-open' : 'icon-eye-close'; ?>"></i> <?php echo $eStatus; ?>
                                                                        </button>
                                                                    </a>
                                                                </td>
                                                                <td width="10%" align="center">
                                                                    <a href="store_images_action.php?id=<?php echo $iUniqueId; ?><?php echo ('' !== $sid) ? '&'.$sid : ''; ?>">
                                                                    <button class="btn btn-primary">
                                                                    <i class="icon-pencil icon-white"></i> Edit
                                                                    </button>
                                                                    </a>
                                                                </td>
                                                                <td width="10%" align="center">
                                                                    <!-- <a href="languages.php?id=<?php echo $id; ?>&action=delete"><i class="icon-trash"></i> Delete</a>-->
                                                                    <form name="delete_form" id="delete_form" method="post" action="" onsubmit="return confirm_delete()" class="margin0">
                                                                        <input type="hidden" name="hdn_del_id" id="hdn_del_id" value="<?php echo $iUniqueId; ?>">
                                                                        <button class="btn btn-danger">
                                                                        <i class="icon-remove icon-white"></i> Delete
                                                                        </button>
                                                                    </form>
                                                                </td>
                                                            </tr>
                                                            <?php }
    }
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
                        $('#dataTables-example').dataTable({"bSort": false});
                    });
                </script>
            </body>
            <!-- END BODY-->
        </html>