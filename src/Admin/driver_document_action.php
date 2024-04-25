<?php
include_once '../common.php';
if (!$userObj->hasPermission('view-providers')) {
    $userObj->redirect();
}

require_once TPATH_CLASS.'/Imagecrop.class.php';
$script = 'Driver';
$thumb = new thumbnail();
$sql = 'select * from country';
$db_country = $obj->MySQLSelect($sql);
if (SITE_TYPE === 'Demo') {
    $_SESSION['success'] = 2;
    header('location:'.$LOCATION_FILE_ARRAY['DRIVER.PHP']);

    exit;
}
$id = $_REQUEST['id'] ?? '';
$success = $_REQUEST['success'] ?? 0;
$action = (isset($_REQUEST['action']) && '' !== $_REQUEST['action']) ? 'Edit' : 'Add';
// $action = isset($_REQUEST['action']) && $_REQUEST['action'] != '';
$doc_type = isset($_REQUEST['doc_type']) && '' !== $_REQUEST['doc_type'];
$backlink = $_POST['backlink'] ?? '';
$previousLink = $_POST['previousLink'] ?? '';
$user_type = $_REQUEST['user_type'] ?? '';
if ('tracking_driver' === $user_type) {
    $script = 'TrackServiceDriver';
}
$sql = "select * from register_driver where iDriverId = '".$_REQUEST['id']."'";
$db_user = $obj->MySQLSelect($sql);
$sql = "select * from language_master where eStatus = 'Active'";
$db_lang = $obj->MySQLSelect($sql);
if ('Ride-Delivery' === $APP_TYPE) {
    $eTypeQuery = " AND (eType='Ride' OR eType='Delivery')";
} elseif ('Ride-Delivery-UberX' === $APP_TYPE) {
    $eTypeQuery = " AND (eType='Ride' OR eType='Delivery' OR eType='UberX')";
} else {
    $eTypeQuery = " AND eType='".$APP_TYPE."'";
}
$default_lang = ('' !== $default_lang) ? $default_lang : 'EN';
/*$sql1= "SELECT dm.doc_masterid masterid, dm.doc_usertype , dm.doc_name ,dm.ex_status,dm.status, dl.doc_masterid masterid_list ,dl.ex_date,dl.doc_file , dl.status,dm.eType FROM document_master dm left join (SELECT * FROM `document_list` where doc_userid='" . $_REQUEST['id'] . "' ) dl on dl.doc_masterid=dm.doc_masterid
where dm.doc_usertype='driver' and dm.status='Active' and (dm.country ='".$db_user[0]['vCountry']."' OR dm.country ='All')  $eTypeQuery";*/
// Added By HJ On 16-05-2020 For Checked Ubex Doc Data Start
$ufxEnable = 'No';
$ufxDocCondition = " AND dm.eDocServiceType!='ServiceSpecific' AND dm.eType!='UberX'";
if ('Yes' === $MODULES_OBJ->isUfxFeatureAvailable()) {
    $ufxEnable = 'Yes';
    $ufxDocCondition = '';
}
// Added By HJ On 16-05-2020 For Checked Ubex Doc Data End
$sql1 = 'SELECT dm.iBiddingId iBiddingId,IF(dm.iVehicleCategoryId > 0,vc.vCategory_'.$default_lang.", '') as eServiceTypeWiseDoc  ,dm.doc_masterid masterid, dm.doc_usertype , dm.doc_name ,dm.ex_status,dm.status, dl.doc_masterid masterid_list ,dl.ex_date,dl.doc_file ,dl.req_date,dl.doc_id,dl.req_file, dl.status,dm.eType,dm.iVehicleCategoryId,vc.vCategory_".$default_lang." as vCategory,dm.eDocServiceType FROM document_master dm left join (SELECT * FROM `document_list` where doc_userid='".$_REQUEST['id']."' ) dl on dl.doc_masterid=dm.doc_masterid LEFT JOIN vehicle_category vc on vc.iVehicleCategoryId = dm.iVehicleCategoryId where dm.doc_usertype='driver' and dm.status='Active' {$ufxDocCondition} and (dm.country ='".$db_user[0]['vCountry']."' OR dm.country ='All') ORDER BY dm.iDisplayOrder";
$db_userdoc = $obj->MySQLSelect($sql1);
if ('Yes' === $ENABLE_BIDDING_WISE_PROVIDER_DOC) {
    $db_vehicle_bidding = $BIDDING_OBJ->getBiddingDocument($id, $db_userdoc);
}
$db_userdoc = getProviderDocumentServiceWise($id, $db_userdoc);

if ('Yes' === $ENABLE_BIDDING_WISE_PROVIDER_DOC) {
    $db_userdoc = array_merge($db_vehicle_bidding, $db_userdoc);
}
$count_all = count($db_userdoc);
// Query for Requested review Expired Docs
$sql2 = "SELECT dm.doc_masterid masterid, dm.doc_usertype , dm.doc_name ,dm.ex_status,dm.status, dl.doc_masterid masterid_list ,dl.ex_date,dl.doc_file ,dl.req_date,dl.doc_id,dl.req_file, dl.status,dm.eType FROM document_master dm left join (SELECT * FROM `document_list` where doc_userid='".$_REQUEST['id']."' ) dl on dl.doc_masterid=dm.doc_masterid
where dl.req_date != '' AND dl.req_date != '0000-00-00' and dm.doc_usertype='driver' and dm.status='Active' {$ufxDocCondition} and (dm.country ='".$db_user[0]['vCountry']."' OR dm.country ='All') ORDER BY dm.iDisplayOrder";
$db_userdoc1 = $obj->MySQLSelect($sql2);
$count_all1 = count($db_userdoc1);
$sql = "select * from register_driver where iDriverId = '".$_REQUEST['id']."'";
$db_user = $obj->MySQLSelect($sql);
$vName = $db_user[0]['vName'];
$action = $_REQUEST['action'] ?? '';
$driver_old_document = $_REQUEST['driver_old_document'] ?? '';
$success = $_REQUEST['success'] ?? '';
$var_msg = $_REQUEST['var_msg'] ?? '';
if ($action = 'document' && isset($_POST['doc_type'])) {
    $expDate = $_POST['dLicenceExp'];
    if (SITE_TYPE === 'Demo') {
        header('location:'.$LOCATION_FILE_ARRAY['DRIVER_DOCUMENT_ACTION'].'?success=2&id='.$_REQUEST['id'].'&var_msg='.$var_msg);

        exit;
    }
    $masterid = $_REQUEST['doc_type'];
    if (isset($_POST['doc_path'])) {
        $doc_path = $_POST['doc_path'];
    }
    $temp_gallery = $doc_path.'/';
    $image_object = $_FILES['driver_doc']['tmp_name'];
    $image_name = $_FILES['driver_doc']['name'];
    if (empty($image_name)) {
        $image_name = $_POST['driver_doc_hidden'];
    }
    if ('' === $image_name) {
        if ('' !== $expDate) {
            /*
                        $sql = "select ex_date from document_list where doc_userid='".$_REQUEST['id']."' and doc_masterid='".$masterid."'";
                        $query = mysqli_query($sql);
                        $fetch = mysqli_fetch_array($query);
                        if($fetch['ex_date'] == "0000-00-00"){

                            if($fetch['ex_date'] != $expDate){
                                $sql="UPDATE `document_list` SET  ex_date='".$expDate."' WHERE doc_userid='".$_REQUEST['id']."' and doc_masterid='".$masterid."'";
                                $query= mysqli_query($sql);
                            }
                        }
                    }

                      $var_msg = "Please Upload valid file format for Image. Valid formats are pdf,doc,docx,jpg,jpeg,gif,png";
                     header("location:driver_document_action.php?success=3&id=" . $_REQUEST['id'] . "&var_msg=" . $var_msg);
                    exit;  */
            $sql = "select ex_date from document_list where doc_userid='".$_REQUEST['id']."' and doc_masterid='".$masterid."'";
            $db_licence = $obj->sql_query($sql);
            if ($db_licence[0]['ex_date'] === $expDate) {
                $var_msg = 'Document updated successfully';
            } else {
                if ('' !== $_FILES['driver_doc']['name']) {
                    $filecheck = basename($_FILES['driver_doc']['name']);
                    $fileextarr = explode('.', $filecheck);
                    $ext = strtolower($fileextarr[count($fileextarr) - 1]);
                    $var_msg1 = '';
                    if ('jpg' !== $ext && 'gif' !== $ext && 'png' !== $ext && 'jpeg' !== $ext && 'bmp' !== $ext && 'pdf' !== $ext && 'doc' !== $ext && 'docx' !== $ext) {
                        // $flag_error = 1;
                        $var_msg1 = 'You have selected wrong file format for Image. Valid formats are pdf,doc,docx,jpg,jpeg,gif,png';
                    } else {
                        $var_msg1 = 'Document updated successfully';
                    }
                }
                $var_msg = 'Document updated successfully'.$var_msg1;
                $tbl = 'document_list';
                if (0 !== count($db_licence)) {
                    $where = " WHERE `doc_userid` = '".$_REQUEST['id']."' and doc_masterid='".$masterid."' ";
                    $q = 'UPDATE ';
                    $query = $q.' `'.$tbl."` SET `ex_date` = '".$expDate."'  ".$where;
                } else {
                    $q = 'INSERT INTO';
                    $query = $q.' `'.$tbl."` ( `doc_masterid`, `doc_usertype`, `doc_userid`, `ex_date`, `doc_file`, `status`, `edate`) VALUES ( '".$_REQUEST['doc_type']."', 'driver', '".$_REQUEST['id']."', '".$expDate."', '', 'Inactive', CURRENT_TIMESTAMP)";
                }
                $obj->sql_query($query);
            }
            header('location:'.$LOCATION_FILE_ARRAY['DRIVER_DOCUMENT_ACTION'].'?success=1&id='.$_REQUEST['id'].'&var_msg='.$var_msg.'&user_type='.$user_type);

            exit;
        }
        $var_msg = 'Please Upload valid file format for Image. Valid formats are pdf,doc,docx,jpg,jpeg,gif,png';
        header('location:'.$LOCATION_FILE_ARRAY['DRIVER_DOCUMENT_ACTION'].'?success=3&id='.$_REQUEST['id'].'&var_msg='.$var_msg.'&user_type='.$user_type);

        exit;
    }
    if ('' !== $_FILES['driver_doc']['name']) {
        $check_file_query = "select doc_file,doc_userid from document_list where doc_masterid='".$masterid."'AND doc_userid=".$_REQUEST['id'];
        $check_file = $obj->sql_query($check_file_query);
        $check_file['doc_file'] = $doc_path.'/'.$_REQUEST['id'].'/'.$check_file[0]['doc_file'];
        $filecheck = basename($_FILES['driver_doc']['name']);
        $fileextarr = explode('.', $filecheck);
        $ext = strtolower($fileextarr[count($fileextarr) - 1]);
        $flag_error = 0;
        if ('jpg' !== $ext && 'gif' !== $ext && 'png' !== $ext && 'jpeg' !== $ext && 'bmp' !== $ext && 'pdf' !== $ext && 'doc' !== $ext && 'docx' !== $ext) {
            $flag_error = 1;
            $var_msg = 'You have selected wrong file format for Image. Valid formats are pdf,doc,docx,jpg,jpeg,gif,png';
        }
        if (1 === $flag_error) {
            $var_msg = 'You have selected wrong file format for Image. Valid formats are pdf,doc,docx,jpg,jpeg,gif,png';
            header('location:'.$LOCATION_FILE_ARRAY['DRIVER_DOCUMENT_ACTION'].'?success=3&id='.$_REQUEST['id'].'&var_msg='.$var_msg.'&user_type='.$user_type);

            exit;
        }

        $Photo_Gallery_folder = $doc_path.'/'.$_REQUEST['id'].'/';
        if (!is_dir($Photo_Gallery_folder)) {
            mkdir($Photo_Gallery_folder, 0777);
        }
        $vFile = $UPLOAD_OBJ->GeneralFileUpload($Photo_Gallery_folder, $image_object, $image_name, $prefix = '', $vaildExt = 'pdf,doc,docx,jpg,jpeg,gif,png');
        $vImage = $vFile[0];
        $var_msg = 'Document updated successfully';
        $tbl = 'document_list';
        $sql = 'select doc_id from  '.$tbl."  where doc_userid='".$_REQUEST[id]."' and doc_usertype='driver'  and doc_masterid=".$_REQUEST['doc_type'];
        $db_data = $obj->MySQLSelect($sql);
        // print_r($db_data); exit;
        if (count($db_data) > 0) {
            $query = 'UPDATE `'.$tbl."` SET `doc_file`='".$vImage."' , `ex_date`='".$expDate."' WHERE doc_userid='".$_REQUEST[id]."' and doc_usertype='driver'  and doc_masterid=".$_REQUEST['doc_type'];
        } else {
            $query = ' INSERT INTO `'.$tbl.'` ( `doc_masterid`, `doc_usertype`, `doc_userid`, `ex_date`, `doc_file`, `status`, `edate`) '.'VALUES '."( '".$_REQUEST['doc_type']."', 'driver', '".$_REQUEST['id']."', '".$expDate."', '".$vImage."', 'Inactive', CURRENT_TIMESTAMP)";
        }
        $obj->sql_query($query);
        // Start :: Log Data Save
        if (empty($check_file[0]['doc_file'])) {
            $vNocPath = $vImage;
        } else {
            $vNocPath = $check_file[0]['doc_file'];
        }
        save_log_data($_SESSION['sess_iUserId'], $_REQUEST['id'], 'company', 'noc', $vNocPath);
        // End :: Log Data Save
        // Start :: Status in edit a Document upload time
        // $set_value = "`eStatus` ='inactive'";
        // estatus_change('register_driver','iDriverId',$_REQUEST['id'],$set_value);
        // End :: Status in edit a Document upload time
        header('location:'.$LOCATION_FILE_ARRAY['DRIVER_DOCUMENT_ACTION'].'?success=1&id='.$_REQUEST['id'].'&var_msg='.$var_msg.'&user_type='.$user_type);
    } else {
        $vImage = $_POST['driver_doc_hidden'];
        $check_file_query = "select doc_file,doc_userid from document_list where doc_masterid='".$masterid."'AND doc_userid=".$_REQUEST['id'];
        $check_file = $obj->sql_query($check_file_query);
        $check_file['doc_file'] = $doc_path.'/'.$_REQUEST['id'].'/'.$check_file[0]['doc_file'];
        $tbl = 'document_list';
        $sql = 'select doc_id from  '.$tbl."  where doc_userid='".$_REQUEST[id]."' and doc_usertype='driver'  and doc_masterid=".$_REQUEST['doc_type'];
        $db_data = $obj->MySQLSelect($sql);
        if (count($db_data) > 0) {
            $query = 'UPDATE `'.$tbl."` SET `doc_file`='".$vImage."' , `ex_date`='".$expDate."' WHERE doc_userid='".$_REQUEST[id]."' and doc_usertype='driver'  and doc_masterid=".$_REQUEST['doc_type'];
        } else {
            $query = ' INSERT INTO `'.$tbl.'` ( `doc_masterid`, `doc_usertype`, `doc_userid`, `ex_date`, `doc_file`, `status`, `edate`) '.'VALUES '."( '".$_REQUEST['doc_type']."', 'driver', '".$_REQUEST['id']."', '".$expDate."', '".$vImage."', 'Inactive', CURRENT_TIMESTAMP)";
        }
        $obj->sql_query($query);
        $var_msg = 'Document updated successfully';
        // Start :: Log Data Save
        if (empty($check_file[0]['doc_file'])) {
            $vNocPath = $vImage;
        } else {
            $vNocPath = $check_file[0]['doc_file'];
        }
        save_log_data($_SESSION['sess_iUserId'], $_REQUEST['id'], 'company', 'noc', $vNocPath);
        header('location:'.$LOCATION_FILE_ARRAY['DRIVER_DOCUMENT_ACTION'].'?success=1&id='.$_REQUEST['id'].'&var_msg='.$var_msg.'&user_type='.$user_type);
    }
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
    <title><?php echo $SITE_NAME; ?> | <?php echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']; ?> <?php echo $action; ?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta content="" name="keywords"/>
    <meta content="" name="description"/>
    <meta content="" name="author"/>
    <link href="../assets/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet"/>
    <?php include_once 'global_files.php'; ?>
    <!-- On OFF switch -->
    <link href="../assets/css/jquery-ui.css" rel="stylesheet"/>
    <link rel="stylesheet" href="../assets/plugins/switch/static/stylesheets/bootstrap-switch.css"/>
    <link rel="stylesheet" href="../assets/css/bootstrap-fileupload.min.css">
    <script src="../assets/plugins/jasny/js/bootstrap-fileupload.js"></script>
    <script>
        // $(document).ready(function () {
        // $('#uiModal').on('show.bs.modal', function (e) {
        // var rowid = $(e.relatedTarget).data('id');
        // $.ajax({
        // type: 'post',
        // url: 'driver_document_fetch.php', //Here you will fetch records
        // data: 'rowid=' + rowid + '-' + <?php echo $_REQUEST['id']; ?>, //Pass $id
        // success: function (data) {
        // $('.fetched-data').html(data);//Show fetched data from database
        // $('#dp3').datepicker();
        // }
        // });
        // });
        // });
        /*
            $(document).ready(function() {
                var referrer;
                if($("#previousLink").val() == "" ){
                    referrer =  document.referrer;
                }else {
                    referrer = $("#previousLink").val();
                }
                if(referrer == "") {
                    referrer = "driver.php";
                }else {
                    var str = "dashboard.php";
                    if(referrer.indexOf(str) != -1){
                        $(".add-btn").val('Back');
                    }
                    $("#backlink").val(referrer);
                }
                $(".back_link").attr('href',referrer);
            });*/
        //$(document).ready(function() {
        function setModel001(idVal) {
            $('.fetched-data').html('');
            // $('#uiModal').on('show.bs.modal', function (e) {
            // var rowid = $(e.relatedTarget).data('id');
            // $.ajax({
            // 	type: 'post',
            // 	url: 'driver_document_fetch.php', //Here you will fetch records
            // 	data: 'rowid=' + idVal + '-' + '<?php echo $_REQUEST['id']; ?>', //Pass $id
            // 	success: function (data) {

            // 		$('#uiModal').modal('show');
            // 		$('.fetched-data').html(data);//Show fetched data from database

            // 	}
            // });

            var ajaxData = {
                'URL': '<?php echo $tconfig['tsite_url_main_admin']; ?>driver_document_fetch.php',
                'AJAX_DATA': 'rowid=' + idVal + '-' + '<?php echo $_REQUEST['id']; ?>' + '&userType=<?php echo $_REQUEST['user_type']; ?>', //Pass $id
            };
            getDataFromAjaxCall(ajaxData, function (response) {
                if (response.action == "1") {
                    var data = response.result;
                    $('#uiModal').modal('show');
                    $('.fetched-data').html(data);
                } else {
                    console.log(response.result);
                }
            });
        }

        //});
    </script>
</head>
<!-- END  HEAD-->
<!-- BEGIN BODY-->
<body class="padTop53 ">
<!-- MAIN WRAPPER -->
<div id="wrap">
    <?php
    include_once 'header.php';
?>
    <?php
include_once 'left_menu.php';
?>
    <!--PAGE CONTENT -->
    <div id="content">
        <div class="inner">
            <div class="row">
                <div class="col-lg-12">
                    <h2><?php echo ucfirst($action); ?> Document of <?php echo $vName; ?></h2>
                    <a class="back_link"
                        <?php
                    $file = $LOCATION_FILE_ARRAY['DRIVER.PHP'];
if ('tracking_driver' === $user_type) {
    $file = 'track_service_driver.php';
} ?>
                       href="<?php echo $file; ?><?php echo isset($_REQUEST['type']) ? '?type='.$_REQUEST['type'] : ''; ?>"
                    ">
                    <input type="button" value="Back to Listing" class="add-btn">
                    </a>
                    <!-- <input type="button" class="add-btn" value="Close" onClick="javascript:window.top.close();"> -->
                </div>
            </div>
            <hr/>
            <div class="body-div">
                <div class="form-group">
                    <?php if (1 === $success) { ?>
                        <div class="alert alert-success alert-dismissable">
                            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                            <?php echo $var_msg; ?>
                        </div>
                        <br/>
                    <?php } ?>

                    <?php if (2 === $success) { ?>
                        <div class="alert alert-danger alert-dismissable">
                            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                            "Edit / Delete Record Feature" has been disabled on the Demo Admin Panel. This feature will
                            be enabled on the main script we will provide you.
                        </div>
                        <br/>
                    <?php } ?>

                    <?php if (3 === $success) { ?>
                        <div class="alert alert-danger alert-dismissable">
                            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                            <?php echo $var_msg; ?>
                        </div>
                        <br/>
                    <?php } ?>

                    <?php if (4 === $success) { ?>
                        <div class="alert alert-success alert-dismissable">
                            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                            Document Approved Successfully..
                        </div>
                        <br/>
                    <?php } ?>
                    <input type="hidden" name="id" value="<?php echo $id; ?>"/>
                    <input type="hidden" name="previousLink" id="previousLink" value="<?php echo $previousLink; ?>"/>
                    <input type="hidden" name="user_type" id="user_type" value="<?php echo $user_type; ?>"/>
                    <input type="hidden" name="backlink" id="backlink" value="<?php echo $LOCATION_FILE_ARRAY['DRIVER.PHP']; ?>"/>
                    <div class="row">
                        <div class="col-sm-12">
                            <h4 style="margin-top:0px;">DOCUMENTS</h4>
                        </div>
                    </div>
                    <div class="row company-document-action">
                        <?php for ($i = 0; $i < $count_all; ++$i) {
                            if ('UberX' === $db_userdoc[$i]['eType']) {
                                $etypeName = 'Service';
                            } else {
                                $etypeName = $db_userdoc[$i]['eType'];
                            }
                            ?>
                            <div class="col-lg-3">
                                <div class="panel panel-default upload-clicking">
                                    <div class="panel-heading new">
                                        <div>   <?php echo $db_userdoc[$i]['doc_name']; ?>
                                            <?php if ('Yes' === $MODULES_OBJ->isEnableServiceTypeWiseProviderDocument() && '' !== $db_userdoc[$i]['eServiceTypeWiseDoc'] && 'Yes' === $db_userdoc[$i]['isShowInfo']) { ?>
                                                <i class="icon-question-sign" data-placement="top" data-toggle="tooltip"
                                                   data-original-title="<?php echo $langage_lbl_admin['LBL_DOCUMENT_FOR'].' '.$db_userdoc[$i]['eServiceTypeWiseDoc']; ?>"></i>
                                            <?php } ?>
                                        </div>
                                        <?php if ('Ride-Delivery' === $APP_TYPE || 'Ride-Delivery-UberX' === $APP_TYPE) { ?>
                                            <!--  <div style="font-size: 10px;">(For <?php echo $etypeName; ?>)</div> -->
                                        <?php } ?>
                                    </div>
                                    <div class="panel-body">
                                        <?php if ('' !== $db_userdoc[$i]['doc_file'] && file_exists('../webimages/upload/documents/driver/'.$_REQUEST['id'].'/'.$db_userdoc[$i]['doc_file'])) { ?>
                                            <?php
                                            $file_ext = $UPLOAD_OBJ->GetFileExtension($db_userdoc[$i]['doc_file']);
                                            $file_ext = file_ext_new($db_userdoc[$i]['doc_file']);
                                            if ('is_image' === $file_ext) {
                                                $imgpath = $tconfig['tsite_upload_driver_doc'].'/'.$_REQUEST['id'].'/'.$db_userdoc[$i]['doc_file'];
                                                $resizeimgpath = $tconfig['tsite_url'].'resizeImg.php?src='.$imgpath.'&w=200';
                                                ?>
                                                <a href="<?php echo $tconfig['tsite_upload_driver_doc'].'/'.$_REQUEST['id'].'/'.$db_userdoc[$i]['doc_file']; ?>"
                                                   target="_blank">
                                                    <img src="<?php echo $resizeimgpath; ?>" style="cursor:pointer;"
                                                         alt="YOUR DRIVING LICENCE"/>
                                                </a>
                                            <?php } elseif ('is_pdf' === $file_ext) {
                                                $imgpath = $tconfig['tsite_url'].'/assets/img/pdf.jpg';
                                                $resizeimgpath = $tconfig['tsite_url'].'resizeImg.php?src='.$imgpath.'&w=150';
                                                ?>
                                                <p>
                                                    <a href="<?php echo $tconfig['tsite_upload_driver_doc'].'/'.$_REQUEST['id'].'/'.$db_userdoc[$i]['doc_file']; ?>"
                                                       target="_blank" title="<?php echo $db_userdoc[$i]['doc_file']; ?>">
                                                        <?php echo $db_userdoc[$i]['doc_name']; ?>
                                                    </a>
                                                </p>
                                            <?php } else {
                                                $imgpath = $tconfig['tsite_url'].'/assets/img/document.png';
                                                $resizeimgpath = $tconfig['tsite_url'].'resizeImg.php?src='.$imgpath.'&w=150';
                                                ?>
                                                <p>
                                                    <a href="<?php echo $tconfig['tsite_upload_driver_doc'].'/'.$_REQUEST['id'].'/'.$db_userdoc[$i]['doc_file']; ?>"
                                                       target="_blank" title="<?php echo $db_userdoc[$i]['doc_file']; ?>">
                                                        <img
                                                                src="<?php echo $resizeimgpath; ?>" style="cursor:pointer;"
                                                                alt="YOUR DRIVING LICENCE"/>
                                                        <?php // echo $db_userdoc[$i]['doc_name'];?>
                                                    </a>
                                                </p>
                                            <?php } ?>
                                            <?php /* else { ?>
                                    <p>
                                        <a href="<?= $tconfig["tsite_upload_driver_doc"] . '/' . $_REQUEST['id'] . '/' . $db_userdoc[$i]['doc_file'] ?>"
                                           target="_blank"><?php echo $db_userdoc[$i]['doc_name']; ?></a></p>
                                    <?php } */ ?>
                                            <?php
                                        } else {
                                            echo '<p>'.$db_userdoc[$i]['doc_name'].' not found'.'</p>';
                                        }
                            ?>
                                        <br/>
                                        <?php if ($userObj->hasPermission('edit-provider-document')) { ?>
                                            <b>
                                                <button class="btn btn-info" data-toggle="modal" data-target="#uiModal"
                                                        id="custId"
                                                        onClick="setModel001('<?php echo $db_userdoc[$i]['masterid']; ?>');">
                                                    <?php
                                        if ('' !== $db_userdoc[$i]['doc_name']) {
                                            echo $db_userdoc[$i]['doc_name'];
                                        }
                                            ?>
                                                </button>
                                            </b>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="col-lg-12">
                            <div class="modal fade" id="uiModal" tabindex="-1" role="dialog"
                                 aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-content image-upload-1">
                                    <div class="fetched-data"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Expired Documents start  -->
            <?php if (0 !== $count_all1 && 'Yes' === $SET_DRIVER_OFFLINE_AS_DOC_EXPIRED) { ?>
                <div class="body-div">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-sm-12">
                                <h4 style="margin-top:0px;">NEW UPLOADED DOCUMENTS</h4>
                                <input type="button" name="approveDoc" id="approveDoc" value="APPROVE DOCUMENTS"
                                       class="btn btn-success pull-right">
                            </div>
                        </div>
                        <div class="row company-document-action">
                            <?php for ($i = 0; $i < $count_all1; ++$i) {
                                if ('UberX' === $db_userdoc1[$i]['eType'] && 'YES' === strtoupper($ufxEnable)) {
                                    $etypeName = 'Service';
                                } else {
                                    $etypeName = $db_userdoc1[$i]['eType'];
                                }
                                if ($db_userdoc1[$i]['req_date'] && '0000-00-00' !== $db_userdoc1[$i]['req_date']) {
                                    ?>
                                    <div class="col-lg-3">
                                        <div class="panel panel-default upload-clicking">
                                            <div class="panel-heading">
                                                <div><?php echo $db_userdoc1[$i]['doc_name']; ?></div>
                                                <?php if ('Ride-Delivery' === $APP_TYPE || 'Ride-Delivery-UberX' === $APP_TYPE) { ?>
                                                    <!--  <div style="font-size: 10px;">(For <?php echo $etypeName; ?>)</div> -->
                                                <?php } ?>
                                            </div>
                                            <div class="panel-body" style="display: inline-block;">
                                                <?php if ('' !== $db_userdoc1[$i]['req_file'] && file_exists('../webimages/upload/documents/driver/'.$_REQUEST['id'].'/'.$db_userdoc1[$i]['req_file'])) { ?>
                                                    <?php
                                                    $file_ext = $UPLOAD_OBJ->GetFileExtension($db_userdoc1[$i]['req_file']);
                                                    if ('is_image' === $file_ext) {
                                                        $imgpath1 = $tconfig['tsite_upload_driver_doc'].'/'.$_REQUEST['id'].'/'.$db_userdoc1[$i]['req_file'];
                                                        $resizeimgpath1 = $tconfig['tsite_url'].'resizeImg.php?src='.$imgpath1.'&w=200';
                                                        ?>
                                                        <a href="<?php echo $tconfig['tsite_upload_driver_doc'].'/'.$_REQUEST['id'].'/'.$db_userdoc1[$i]['req_file']; ?>"
                                                           target="_blank">
                                                            <img src="<?php echo $resizeimgpath1; ?>" style="cursor:pointer;"
                                                                 alt="YOUR DRIVING LICENCE"/>
                                                        </a>
                                                    <?php } else { ?>
                                                        <p>
                                                            <a href="<?php echo $tconfig['tsite_upload_driver_doc'].'/'.$_REQUEST['id'].'/'.$db_userdoc1[$i]['req_file']; ?>"
                                                               target="_blank"><?php echo $db_userdoc1[$i]['doc_name']; ?></a>
                                                        </p>
                                                    <?php } ?>
                                                    <?php
                                                } else {
                                                    echo '<p>'.$db_userdoc1[$i]['doc_name'].' not found'.'</p>';
                                                }
                                    ?>
                                                <?php if (!empty($db_userdoc1[$i]['req_date'])) { ?>
                                                    <h5>Requested Date
                                                        : <?php echo $db_userdoc1[$i]['req_date']; ?></h5>
                                                    <input type="hidden" name="approvedIds[]" class="approvedIds"
                                                           value="<?php echo $db_userdoc1[$i]['doc_id']; ?>">
                                                <?php } ?>
                                                <br/>
                                            </div>
                                        </div>
                                    </div>
                                <?php }
                                } ?>
                            <div class="col-lg-12">
                                <div class="modal fade" id="uiModal" tabindex="-1" role="dialog"
                                     aria-labelledby="myModalLabel" aria-hidden="true">
                                    <div class="modal-content image-upload-1">
                                        <div class="fetched-data"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <!-- End Expired Documents  -->
        </div>
    </div>
</div>
<!--END PAGE CONTENT -->
</div>
<!--END MAIN WRAPPER -->
<!-- Modal -->
<?php include_once 'footer.php'; ?>
<link rel="stylesheet" type="text/css" media="screen"
      href="css/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css">
<script type="text/javascript" src="js/moment.min.js"></script>
<script type="text/javascript" src="js/bootstrap-datetimepicker.min.js"></script>
<script src="../assets/plugins/switch/static/js/bootstrap-switch.min.js"></script>
<!-- Start :: Datepicker css-->
<link rel="stylesheet" href="../assets/plugins/datepicker/css/datepicker.css"/>
<!-- Start :: Datepicker-->
<script>
    $('[data-toggle="tooltip"]').tooltip();
</script>
<!-- Start :: Datepicker Script-->
<script src="../assets/js/jquery-ui.min.js"></script>
<script src="../assets/plugins/uniform/jquery.uniform.min.js"></script>
<script src="../assets/plugins/inputlimiter/jquery.inputlimiter.1.3.1.min.js"></script>
<script src="../assets/plugins/chosen/chosen.jquery.min.js"></script>
<script src="../assets/plugins/colorpicker/js/bootstrap-colorpicker.js"></script>
<script src="../assets/plugins/tagsinput/jquery.tagsinput.min.js"></script>
<script src="../assets/plugins/validVal/js/jquery.validVal.min.js"></script>
<script src="../assets/plugins/datepicker/js/bootstrap-datepicker.js"></script>
<script src="../assets/plugins/timepicker/js/bootstrap-timepicker.min.js"></script>
<script src="../assets/plugins/autosize/jquery.autosize.min.js"></script>
<script src="../assets/plugins/jasny/js/bootstrap-inputmask.js"></script>
<script src="../assets/js/formsInit.js"></script>
<script>
    $(document).on('click', '#approveDoc', function (event) {

        var docsIds = $('input[name="approvedIds[]"]').map(function () {
            return this.value;
        }).get();

        //    var request = $.ajax({
        // 	type: "POST",
        // 	url: 'ajax_approve_docs.php',
        // 	data: 'docsIds=' + docsIds,
        // 	success: function (data)
        // 	{
        // 		window.location = 'driver_document_action.php?success=4&id=<?php echo $_REQUEST['id']; ?>';
        // 	}
        // });

        var ajaxData = {
            'URL': '<?php echo $tconfig['tsite_url_main_admin']; ?>ajax_approve_docs.php',
            'AJAX_DATA': 'docsIds=' + docsIds,
        };
        getDataFromAjaxCall(ajaxData, function (response) {
            if (response.action == "1") {
                var data = response.result;
                window.location = '<?php echo $LOCATION_FILE_ARRAY['DRIVER_DOCUMENT_ACTION']; ?>?success=4&id=<?php echo $_REQUEST['id']; ?>';
            } else {
                console.log(response.result);
            }
        });

    });    // $(function () {

    // var nowTemp = new Date();
    // var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);

    // $('#dp3').datepicker({
    // onRender: function (date) {
    // return date.valueOf() < now.valueOf() ? 'disabled' : '';
    // }
    // });
    // formInit();
    // });
</script>
</body>
<!-- END BODY-->
</html>
