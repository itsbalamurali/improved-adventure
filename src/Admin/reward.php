<?php
include_once '../common.php';
if (!$userObj->hasPermission(['view-driver-reward-setting', 'view-driver-reward-campaign'])) {
    $userObj->redirect();
}
$sql = 'SELECT * FROM `language_master` ORDER BY `iDispOrder`';
$db_master = $obj->MySQLSelect($sql);
$count_all = count($db_master);
$EN_available = $LANG_OBJ->checkLanguageExist();
$id = $_REQUEST['id'] ?? '';
$success = $_REQUEST['success'] ?? 0;
$ksuccess = $_REQUEST['ksuccess'] ?? 0;
$action = ('' !== $id) ? 'Edit' : 'Add';
$tbl_name = 'reward_settings';
$script = 'Reward';
$vLevel = $_POST['vLevel'] ?? '';
$vMinimumTrips = $_POST['vMinimumTrips'] ?? '';
$fRatings = $_POST['fRatings'] ?? '';
$iAcceptanceRate = $_POST['iAcceptanceRate'] ?? '';
$iCancellationRate = $_POST['iCancellationRate'] ?? '';
$iDuration = $_POST['iDuration'] ?? '';
$fCredit = $_POST['fCredit'] ?? '';
if ($_POST['campaign_form']) {
    $eStatus = $_POST['eStatus'] ?? 'Inactive';
    $query_p['vTitle'] = $_POST['vTitle_Default'];
    $query_p['eStatus'] = $eStatus;
    if ('Cancelled' === $eStatus) {
        $DRIVER_REWARD_OBJ->notifyCampaignCancelled($id);
        $query_p['eCurrentActive'] = 'No';
    }
    if (isset($_POST['startDate']) && !empty($_POST['startDate'])) {
        $query_p['dStart_date'] = date('Y-m-d H:i:s', strtotime($_POST['startDate']));
    }
    if (isset($_POST['endDate']) && !empty($_POST['endDate'])) {
        $query_p['dEnd_date'] = date('Y-m-d H:i:s', strtotime($_POST['endDate']));
    }
    if ('' !== $id) {
        $where = " iCampaignId = '{$id}'";
        $obj->MySQLQueryPerform('reward_campaign', $query_p, 'update', $where);
    } else {
        $obj->MySQLQueryPerform('reward_campaign', $query_p, 'insert');
    }
    if ('' !== $id) {
        $_SESSION['success'] = '1';
        $_SESSION['var_msg'] = $langage_lbl_admin['LBL_Record_Updated_successfully'];
    } else {
        $_SESSION['success'] = '1';
        $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_INSERT_MSG'];
    }
    header('Location:reward.php');

    exit;
}
if (isset($_POST['submit']) && !isset($_POST['campaign_form'])) {
    if (SITE_TYPE === 'Demo') {
        $_SESSION['success'] = 2;
        header('Location:reward.php');

        exit;
    }

    require_once 'Library/validation.class.php';
    $validobj = new validation();
    $validobj->add_fields($_POST['vLevel'], 'req', 'Reward label is required');
    $error = $validobj->validate();
    $error .= $validobj->validateFileType($_FILES['vImage'], 'jpg,jpeg,png,gif,bmp', '* Image file is not valid.');
    if ($error) {
        $success = 3;
        $newError = $error;
    } else {
        for ($b = 0; $b < count($db_master); ++$b) {
            $tDescription = '';
            if (isset($_POST['vLevel_'.$id.$db_master[$b]['vCode']])) {
                $tDescription = $_POST['vLevel_'.$id.$db_master[$b]['vCode']];
            }
            $descArr['vLevel_'.$db_master[$b]['vCode']] = $tDescription;
        }
        $jsonDesc = getJsonFromAnArr($descArr);
        $q = 'INSERT INTO ';
        $where = '';
        if ('' !== $id) {
            $q = 'UPDATE ';
            $where = " WHERE `iRewardId` = '".$id."'";
        }
        // `vLevel` = '" . $vLevel . "',
        $query = $q.' `'.$tbl_name."` SET
        `vLevel` = '".$jsonDesc."',
        `vMinimumTrips` = '".$vMinimumTrips."',
        `fRatings` = '".$fRatings."',
        `iAcceptanceRate` = '".$iAcceptanceRate."',
        `iCancellationRate` = '".$iCancellationRate."',
        `iDuration` = '".$iDuration."',
        `fCredit` = '".$fCredit."'".$where;
        $obj->sql_query($query);
        $id = ('' !== $id) ? $id : $obj->GetInsertId();
        if ('' !== $_FILES['vImage']['name']) {
            $Photo_Gallery_folder = $tconfig['tsite_upload_images_reward_path'];
            if (!is_dir($Photo_Gallery_folder)) {
                mkdir($Photo_Gallery_folder, 0777);
                chmod($Photo_Gallery_folder, 0777);
            }
            $image_object = $_FILES['vImage']['tmp_name'];
            $image_name = $_FILES['vImage']['name'];
            $img_path = $tconfig['tsite_upload_images_reward_path'];
            $temp_gallery = $img_path.'/';
            $Photo_Gallery_folder = $img_path.'/'.$id.'/';
            if (!is_dir($Photo_Gallery_folder)) {
                mkdir($Photo_Gallery_folder, 0777);
            }
            $img1 = $UPLOAD_OBJ->GeneralUploadImage($image_object, $image_name, $Photo_Gallery_folder, '', '', '', '', '', '', 'Y', '', $Photo_Gallery_folder);
            $vImgName = $img1;
            $sql = 'UPDATE '.$tbl_name." SET `vImage` = '".$vImgName."' WHERE `iRewardId` = '".$id."'";
            $obj->sql_query($sql);
        }
    }
}
$reward_settings = $obj->MySQLSelect('SELECT * FROM reward_settings');
$status = $_REQUEST['status'] ?? '';
$iCampaignId = $_REQUEST['iCampaignId'] ?? '';
$CurrentActive = $_REQUEST['CurrentActive'] ?? '';
if (isset($_REQUEST['CurrentActive'])) {
    if (!empty($CurrentActive)) {
        $CampaignData = $DRIVER_REWARD_OBJ->updateCurrentActiveCampaign($CurrentActive);
        $success = 10;
        if (0 === $CampaignData['status']) {
            $success = 3;
            $error = 'Record Not Updated. because one Campaign is running.';
            $_SESSION['success'] = '3';
            $_SESSION['var_msg'] = 'Record Not Updated. because one Campaign is running.';
        }
    }
    header('Location:reward.php');

    exit;
}
if (isset($_POST['validation_action']) && 'YES' === strtoupper($_POST['validation_action'])) {
    $start_date = $_POST['startDate'] ?? '';
    $end_date = $_POST['endDate'] ?? '';
    $campaign_id = $_POST['campaign_id'] ?? '';
    if (!$DRIVER_REWARD_OBJ->validateCampaignDates($start_date, $end_date, $campaign_id)) {
        echo 'No';

        exit;
    }
    echo 'Yes';

    exit;
}
$CampaignData = $DRIVER_REWARD_OBJ->getCampaign();
$getActiveCampaign = $DRIVER_REWARD_OBJ->getActiveCampaign();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <title><?php echo $SITE_NAME; ?> | Reward Setting</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <!--[if IE]>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <![endif]-->
    <!-- GLOBAL STYLES -->
    <?php include_once 'global_files.php'; ?>
    <link rel="stylesheet" href="../assets/plugins/switch/static/stylesheets/bootstrap-switch.css"/>
    <link rel="stylesheet" type="text/css" media="screen"
          href="<?php echo $tsiteAdminUrl; ?>css/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css">
    <!-- END THIS PAGE PLUGINS-->
    <!--END GLOBAL STYLES -->
    <!-- PAGE LEVEL STYLES -->
    <!-- END PAGE LEVEL  STYLES -->
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
    <style type="text/css">
        .reward-img {
            text-align: center;
            margin: 0 0 30px 0;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
        }

        .row > [class*='col-'] {
            flex-direction: row;
        }

        .active-campaign {
            float: right;
            font-size: 16px;
            line-height: 40px;
        }

        .active-campaign span {
            font-weight: 600;
        }

        .sub-admin-notes li {
            padding-bottom: 0;
        }
    </style>
</head>
<!-- END  HEAD-->
<!-- BEGIN BODY-->
<body class="padTop53">
<!-- MAIN WRAPPER -->
<div id="wrap">
    <?php include_once 'header.php'; ?>
    <?php include_once 'left_menu.php'; ?>
    <!--PAGE CONTENT -->
    <div id="content">
        <div class="inner" style="min-height:500px;">
            <div class="row">
                <div class="col-lg-7">
                    <h1> Reward Setting</h1>
                </div>
                <div class="col-lg-5">
                    <?php if (!empty($getActiveCampaign) && count($getActiveCampaign) > 0) { ?>
                        <div class="active-campaign">
                            <span>Active Campaign: </span><?php echo $getActiveCampaign[0]['vTitle']; ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <hr/>
            <?php if (3 === $success) { ?>
                <div class="alert alert-danger alert-dismissable">
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                    <?php print_r($error); ?>
                </div>
                <br/>
            <?php } ?>
            <?php include 'valid_msg.php'; ?>
            <?php if ($userObj->hasPermission(['view-driver-reward-setting'])) { ?>
            <div class="row">
                <?php if (!empty($reward_settings)) {
                    for ($i = 0; $i < count($reward_settings); ++$i) {
                        ?>
                        <?php $vLevel = json_decode($reward_settings[$i]['vLevel'], true); ?>
                        <div class="col-lg-6">
                            <div class="panel panel-primary bg-gray-light">
                                <div class="panel-heading">
                                    <div class="panel-title-box">
                                        <i class="ri-medal-2-line"></i> <?php echo $vLevel['vLevel_'.$default_lang] ?? ''; ?>
                                    </div>
                                </div>
                                <div class="panel-body" style="background-color: #f7f7f7">
                                    <div class="reward-img">
                                        <?php if ('NONE' === $reward_settings[$i]['vImage'] || '' === $reward_settings[$i]['vImage']) { ?>
                                            <img src="../assets/img/profile-user-img.png" alt="">
                                            <?php
                                        } else {
                                            if (file_exists('../webimages/upload/Reward/'.$reward_settings[$i]['iRewardId'].'/'.$reward_settings[$i]['vImage'])) {
                                                ?>
                                                <img src="<?php echo $tconfig['tsite_url'].'resizeImg.php?w=170&src='.$tconfig['tsite_upload_images_reward'].'/'.$reward_settings[$i]['iRewardId'].'/'.$reward_settings[$i]['vImage']; ?>"/>
                                            <?php } else { ?>
                                                <img src="../assets/img/profile-user-img.png" alt="">
                                                <?php
                                            }
                                        }
                        ?>
                                    </div>
                                    <div>
                                        <form class="formreward"
                                              name="_reward_settings_<?php echo $reward_settings[$i]['iRewardId'] ?? ''; ?>"
                                              id="_reward_settings_<?php echo $reward_settings[$i]['iRewardId'] ?? ''; ?>"
                                              method="post" action="" enctype="multipart/form-data">
                                            <input type="hidden" name="id"
                                                   value="<?php echo $reward_settings[$i]['iRewardId'] ?? ''; ?>"
                                                   placeholder="id"/>
                                            <div class="form-group row">
                                                <div class="col-md-6">
                                                    <div class="row pb0">
                                                        <label class="col-md-12">Level
                                                            <span class="red"> *</span>
                                                        </label>
                                                        <div class="input-group col-md-12">
                                                            <input class="form-control" type="text" name="vLevel"
                                                                   id="vLevel_<?php echo $reward_settings[$i]['iRewardId'] ?? ''; ?>Default"
                                                                   id="$('#vLevel_'+id+'<?php echo $default_lang; ?>'"
                                                                   value="<?php echo $vLevel['vLevel_'.$default_lang] ?? ''; ?>"
                                                                   placeholder="Level"
                                                                   onclick="editDescription('Edit' , <?php echo $reward_settings[$i]['iRewardId'] ?? ''; ?>)"/>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row pb0">
                                                        <label class="col-md-12">Minimum Trips
                                                            <span class="red"> *</span>
                                                        </label>
                                                        <div class="input-group col-md-12">
                                                            <input class="form-control" type="Number"
                                                                   name="vMinimumTrips"
                                                                   value="<?php echo $reward_settings[$i]['vMinimumTrips'] ?? ''; ?>"
                                                                   placeholder="Minimum Trips"/>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade"
                                                 id="coupon_desc_Modal<?php echo $reward_settings[$i]['iRewardId'] ?? ''; ?>"
                                                 tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static"
                                                 data-keyboard="false">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content nimot-class">
                                                        <div class="modal-header">
                                                            <h4>
                                                                <span id="modal_action"></span>
                                                                Level
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                        onclick="resetToOriginalValue(this, 'vLevel_')">
                                                                    x
                                                                </button>
                                                            </h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <?php
                                            for ($d = 0; $d < $count_all; ++$d) {
                                                $vCode = $db_master[$d]['vCode'];
                                                $vTitle = $db_master[$d]['vTitle'];
                                                $eDefault = $db_master[$d]['eDefault'];
                                                $descVal = 'vLevel_'.$reward_settings[$i]['iRewardId'].$vCode;
                                                $descVal_ = 'vLevel_'.$vCode;
                                                $required_msg = ('Yes' === $eDefault) ? '<span class="red"> *</span>' : '';
                                                ?>
                                                                <?php
                                                $page_title_class = 'col-lg-12';
                                                if (count($db_master) > 1) {
                                                    if ($EN_available) {
                                                        if ('EN' === $vCode) {
                                                            $page_title_class = 'col-md-9 col-sm-9';
                                                        }
                                                    } else {
                                                        if ($vCode === $default_lang) {
                                                            $page_title_class = 'col-md-9 col-sm-9';
                                                        }
                                                    }
                                                }
                                                ?>
                                                                <div class="form-group row">
                                                                    <div class="col-lg-12">
                                                                        <label>Level (<?php echo $vTitle; ?>
                                                                            ) <?php echo $required_msg; ?></label>
                                                                    </div>
                                                                    <div class="<?php echo $page_title_class; ?>">
                                                                        <input type="text" name="<?php echo $descVal; ?>"
                                                                               class="form-control"
                                                                               id="<?php echo $descVal; ?>"
                                                                               placeholder="<?php echo $vTitle; ?> Value"
                                                                               data-originalvalue="<?php echo $vLevel[$descVal_]; ?>"
                                                                               value="<?php echo $vLevel[$descVal_]; ?>">
                                                                        <div class="text-danger"
                                                                             id="<?php echo $descVal.'_error'; ?>"
                                                                             style="display: none;"><?php echo $langage_lbl_admin['LBL_REQUIRED']; ?></div>
                                                                    </div>
                                                                    <?php
                                                    if (count($db_master) > 1) {
                                                        if ($EN_available) {
                                                            if ('EN' === $vCode) { ?>
                                                                                <div class="col-md-3 col-sm-3">
                                                                                    <button type="button"
                                                                                            name="allLanguage"
                                                                                            id="allLanguage"
                                                                                            class="btn btn-primary"
                                                                                            onClick="getAllLanguageCode('vLevel_<?php echo $reward_settings[$i]['iRewardId'] ?? ''; ?>', '<?php echo $default_lang; ?>');">
                                                                                        Convert To All Language
                                                                                    </button>
                                                                                </div>
                                                                            <?php }
                                                            } else {
                                                                if ($vCode === $default_lang) { ?>
                                                                                <div class="col-md-3 col-sm-3">
                                                                                    <button type="button"
                                                                                            name="allLanguage"
                                                                                            id="allLanguage"
                                                                                            class="btn btn-primary"
                                                                                            onClick="getAllLanguageCode('vLevel_<?php echo $reward_settings[$i]['iRewardId'] ?? ''; ?>', '<?php echo $reward_settings[$i]['iRewardId'] ?? ''; ?><?php echo $default_lang; ?>');">
                                                                                        Convert To All Language
                                                                                    </button>
                                                                                </div>
                                                                            <?php }
                                                                }
                                                    }
                                                ?>
                                                                </div>
                                                                <?php
                                            }
                        ?>
                                                        </div>
                                                        <div class="modal-footer" style="margin-top: 0">
                                                            <h5 class="text-left"
                                                                style="margin-bottom: 15px; margin-top: 0;">
                                                                <strong><?php echo $langage_lbl_admin['LBL_NOTE']; ?>:
                                                                </strong><?php echo $langage_lbl_admin['LBL_SAVE_INFO']; ?>
                                                            </h5>
                                                            <div class="nimot-class-but" style="margin-bottom: 0">
                                                                <button type="button" class="save"
                                                                        style="margin-left: 0 !important"
                                                                        onclick="saveDescription(<?php echo $reward_settings[$i]['iRewardId'] ?? ''; ?>)"><?php echo $langage_lbl_admin['LBL_Save']; ?></button>
                                                                <button type="button" class="btn btn-danger btn-ok"
                                                                        data-dismiss="modal"
                                                                        onclick="resetToOriginalValue(this, 'vLevel_')"><?php echo $langage_lbl_admin['LBL_CANCEL_TXT']; ?></button>
                                                            </div>
                                                        </div>
                                                        <div style="clear:both;"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-md-6">
                                                    <div class="row pb0">
                                                        <label class="col-md-12 col-form-label">Ratings
                                                            <span class="red"> *</span>
                                                        </label>
                                                        <div class="input-group col-md-12">
                                                            <input class="form-control" type="text" name="fRatings"
                                                                   value="<?php echo $reward_settings[$i]['fRatings'] ?? ''; ?>"
                                                                   placeholder="Ratings"/>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row pb0">
                                                        <label class="col-md-12 col-form-label">Acceptance Rate
                                                            <span class="red"> *</span>
                                                        </label>
                                                        <div class="input-group col-md-12">
                                                            <input class="form-control" type="text"
                                                                   name="iAcceptanceRate"
                                                                   value="<?php echo $reward_settings[$i]['iAcceptanceRate'] ?? ''; ?>"
                                                                   placeholder="Acceptance Rate"/>
                                                            <div class="input-group-addon">
                                                                <span class="input-group-text"
                                                                      id="basic-addon1">%</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <div class="col-md-6">
                                                    <div class="row pb0">
                                                        <label class="col-md-12 col-form-label">Cancellation Rate
                                                            <span class="red"> *</span>
                                                        </label>
                                                        <div class="input-group col-md-12">
                                                            <input class="form-control" type="text"
                                                                   name="iCancellationRate"
                                                                   value="<?php echo $reward_settings[$i]['iCancellationRate'] ?? ''; ?>"
                                                                   placeholder="Cancellation Rate"/>
                                                            <div class="input-group-addon">
                                                                <span class="input-group-text"
                                                                      id="basic-addon1">%</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="row pb0">
                                                        <label class="col-md-12 col-form-label">Reward Amount
                                                            <span class="red"> *</span>
                                                        </label>
                                                        <div class="input-group col-md-12">
                                                            <div class="input-group-addon">
                                                                <span class="input-group-text"
                                                                      id="basic-addon1"><?php echo formateNumAsPerCurrency('', ''); ?></span>
                                                            </div>
                                                            <input min="1" type="Number" class="form-control"
                                                                   name="fCredit"
                                                                   value="<?php echo $reward_settings[$i]['fCredit'] ?? ''; ?>"
                                                                   placeholder="Credit"/>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div style="display:none" class="form-group row">
                                                <label class="col-sm-3 col-form-label">Duration
                                                    <span class="red"> *</span>
                                                </label>
                                                <div class="input-group col-sm-9">
                                                    <input type="text" class="form-control" name="iDuration"
                                                           value="<?php echo $reward_settings[$i]['iDuration'] ?? ''; ?>"
                                                           placeholder="Duration"/>
                                                    <div class="input-group-addon">
                                                        <span class="input-group-text" id="basic-addon1">Days</span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-md-12 col-form-label">Image</label>
                                                <div class="col-md-12">
                                                    <input type="file" class="form-control" name="vImage" value=""
                                                           placeholder="Image"/>
                                                </div>
                                            </div>
                                            <?php if ($userObj->hasPermission('update-driver-reward-setting')) { ?>
                                                <input attr-formId="<?php echo $reward_settings[$i]['iRewardId']; ?>"
                                                       type="submit" class="btn btn-primary" name="submit" id="submit"
                                                       value="Update">
                                            <?php } ?>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php }
                    } ?>
            </div>
            <?php } ?>
        </div>
        <div class="inner mt0">
            <?php if ($userObj->hasPermission('view-driver-reward-campaign')) { ?>
                <div id="add-hide-show-div">
                    <div class="row">
                        <div class="col-lg-12">
                            <h2>Campaign</h2>
                            <?php if ($userObj->hasPermission('create-driver-reward-campaign')) { ?>
                                <a href="javascript:void(0);" class="add-btn edit-campaign" data-action="Add">ADD
                                    Campaign
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                    <hr>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <th width="20%">Name</th>
                        <th width="20%">Start Date</th>
                        <th width="20%">End Date</th>
                            <th style="text-align: center;" width="10%">Status</th>
                        <th width="10%">Current Active</th>
                        <?php if ($userObj->hasPermission('edit-driver-reward-campaign')) { ?>
                            <th width="10%" style="text-align: center;">Action</th>
                        <?php } ?>
                        </thead>
                        <tbody>
                            <?php if (count($CampaignData) > 0) { ?>
                                <?php foreach ($CampaignData as $campaign) { ?>
                                    <?php
                                            $dStart_date = date('d-m-Y', strtotime($campaign['dStart_date']));
                                    $dEnd_date = date('d-m-Y', strtotime($campaign['dEnd_date']));
                                    $curr_date = date('Y-m-d');
                                    ?>

                                    <tr>
                                        <td><?php echo $campaign['Title']; ?></td>
                                    <td><?php echo $dStart_date; ?></td>
                                    <td><?php echo $dEnd_date; ?></td>
                                    <td style="text-align: center;">
                                            <?php
                                            if ('Active' === $campaign['eStatus'] && strtotime($curr_date) <= strtotime($dEnd_date)) {
                                                $status_img = 'img/active-icon.png';
                                            } elseif ('Inactive' === $campaign['eStatus'] || ('Active' === $campaign['eStatus'] && strtotime($curr_date) > strtotime($dEnd_date))) {
                                                $status_img = 'img/inactive-icon.png';
                                                $campaign['eStatus'] = 'Inactive';
                                            } else {
                                                $status_img = 'img/delete-icon.png';
                                            }
                                    ?>
                                            <img src="<?php echo $status_img; ?>" alt="image" data-toggle="tooltip"
					    title="<?php echo $campaign['eStatus']; ?>">
                                        </td>
                                        <td><?php echo strtotime($curr_date) >= strtotime($dStart_date) && strtotime($curr_date) <= strtotime($dEnd_date) && 'Cancelled' !== $campaign['eStatus'] && 'Expired' !== $campaign['eStatus'] ? 'Yes' : 'No'; ?></td>
                                    <?php if ($userObj->hasPermission('edit-driver-reward-campaign')) { ?>
                                        <td align="center">
                                            <?php
                                    $date_now = date('Y-m-d');
                                        if (strtotime($date_now) < strtotime($dEnd_date) && 'Cancelled' !== $campaign['eStatus'] && 'Expired' !== $campaign['eStatus']) { ?>
                                                <a href="javascript:void(0);" data-toggle="tooltip" title="Edit"
                                                   data-id="<?php echo $campaign['iCampaignId']; ?>" data-action="Edit"
                                                   data-title="<?php echo $campaign['Title']; ?>"
                                                   data-startdate="<?php echo $dStart_date; ?>"
                                                   data-enddate="<?php echo $dEnd_date; ?>"
                                                   data-estatus="<?php echo $campaign['eStatus']; ?>"
                                                   data-eCurrentActive="<?php echo $campaign['eCurrentActive']; ?>"
                                                   class="edit-campaign" data-action="Edit">
                                                    <img src="img/edit-icon.png" alt="Edit">
                                                </a>
                                            <?php } else { ?>
                                                --
                                            <?php } ?>
                                        </td>
                                <?php } ?>
                                </tr>
                            <?php } ?>
                        <?php } else { ?>
                            <tr>
                                <td colspan="6">No Campaigns found.</td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            <?php } ?>
            <div class="admin-notes">
                <h4>Notes:</h4>
                <ul>
                    <li style="padding-bottom: 0">
                        <strong>Reward Setting:</strong>
                    </li>
                    <li style="padding-bottom: 0">Administrator can modify Reward criteria settings
                        for <?php echo $langage_lbl_admin['LBL_DRIVERS_NAME_ADMIN']; ?>.
                    </li>
                    <li>To achieve a level. For ex: Silver Level
                        <ul class="sub-admin-notes">
                            <li><?php echo $langage_lbl_admin['LBL_DRIVERS_NAME_ADMIN']; ?> has to complete minimum
                                <strong><?php echo $reward_settings[0]['vMinimumTrips']; ?> trips.</strong>
                            </li>
                            <li><?php echo $langage_lbl_admin['LBL_DRIVERS_NAME_ADMIN']; ?> must have average rating
                                <strong>&#8805; <?php echo $reward_settings[0]['fRatings']; ?></strong>
                                .
                            </li>
                            <li><?php echo $langage_lbl_admin['LBL_DRIVERS_NAME_ADMIN']; ?> must have acceptance rate
                                <strong>&#8805; <?php echo $reward_settings[0]['iAcceptanceRate']; ?>%</strong>
                                .
                            </li>
                            <li><?php echo $langage_lbl_admin['LBL_DRIVERS_NAME_ADMIN']; ?> must have cancellation rate
                                <strong>&#8804; <?php echo $reward_settings[0]['iCancellationRate']; ?>%</strong>
                                .
                            </li>
                            <li>All the above criterias must be completed within the active campaign duration.</li>
                        </ul>
                    </li>
                    <?php if ($userObj->hasPermission('view-driver-reward-campaign')) { ?>
                    <li style="padding-bottom: 0">
                        <strong>Campaign Setting:</strong>
                        <ul class="sub-admin-notes">
                            <li>Administrator can add/modify and schedule campaigns.</li>
                            <li>Only a single campaign will be active during a specific duration.</li>
                            <li>Once a campaign is active, the administrator can only extend the end date.</li>
                            <li>Administrator can cancel any campaign at any time.</li>
                            <li>The campaigns becomes inactive after it's duration is over.</li>
                        </ul>
                    </li>
                    <?php }?>
                </ul>
            </div>
            <div class="modal fade" id="add_edit_campaign_modal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content nimot-class">
                        <div class="modal-header">
                            <h4> Campaign
                                <button type="button" class="close" data-dismiss="modal">x</button>
                            </h4>
                        </div>
                        <form method="post" action="" enctype="multipart/form-data" id="campaign_form_">
                            <input id="campaign_form_id" type="hidden" name="id" value=""/>
                            <input type="hidden" name="campaign_form" value="campaign_form"/>
                            <div class="form-group" style="margin-top: 15px">
                                <label class="col-md-12">Title</label>
                                <div class="col-md-12">
                                    <input type="text" class="form-control" id="vTitle_Default" name="vTitle_Default"
                                           value="" data-originalvalue="">
                                    <p>Note: This title only for admin display only.</p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-12">From Date</label>
                                <div class="col-md-12">
                                    <input type="text" id="start_Date" name="startDate" placeholder="From Date"
                                           class="form-control readonly-custom" readonly autocomplete="off"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-12">To Date</label>
                                <div class="col-md-12">
                                    <input type="text" id="end_Date" name="endDate" placeholder="To Date"
                                           class="form-control readonly-custom" readonly autocomplete="off"/>
                                </div>
                            </div>
                            <div class="form-group" id="status-section">
                                <label class="col-md-12">Status</label>
                                <div class="col-md-12">
                                    <select class="form-control" name="eStatus" id="eStatus">
                                        <option value="Active" <?php echo 'Active' === $eStatus ? 'selected' : ''; ?>>Active
                                        </option>
                                        <option value="Cancelled">Cancelled</option>
                                    </select>
                                </div>
                            </div>
                            <hr/>
                            <div class="nimot-class-but" style="margin-bottom: 20px">
                                <button type="submit" class="btn save" id="campaign_btn"
                                        style="margin-left: 15px !important"><?php echo $langage_lbl_admin['LBL_Save']; ?></button>
                                <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal">Close</button>
                            </div>
                        </form>
                        <div style="clear:both;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--END PAGE CONTENT -->
</div>
<div class="row loding-action" id="loaderIcon" style="display:none;">
    <div align="center">
        <img src="default.gif">
        <span>Language Translation is in Process. Please Wait...</span>
    </div>
</div>
<?php include_once 'footer.php'; ?>
<script src="../assets/plugins/switch/static/js/bootstrap-switch.min.js"></script>
<script type="text/javascript" src="<?php echo $tconfig['tsite_url_main_admin']; ?>js/moment.min.js"></script>
<script type="text/javascript"
        src="<?php echo $tconfig['tsite_url_main_admin']; ?>js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript"
        src="<?php echo $tconfig['tsite_url_main_admin']; ?>js/validation/jquery.validate.min.js"></script>
<script>
    /* ---------------------------- for the campaign ---------------------------- */

    var toDayDate = new Date();
    var minDate = moment(toDayDate, "DD-MM-YYYY").add(1, 'days');

    var startDate;
    var endDate;

    var campaign_id;

    $('#start_Date').datetimepicker({
        format: 'DD-MM-YYYY',
        minDate: minDate,
        useCurrent: false,
        ignoreReadonly: true
    }).on('dp.change', function (ev) {
        startDate = new Date(ev.date);
        startDate = Date.parse(ev.date) / 1000;

        if (endDate != null) {
            if (parseInt(startDate) > parseInt(endDate)) {
                alert("From date should be lesser than To date.")
                $('#start_Date').val("");
                startDate = "";
                return false;
            }
        }
    });

    $('#end_Date').datetimepicker({
        format: 'DD-MM-YYYY',
        minDate: minDate,
        useCurrent: false,
        ignoreReadonly: true
    }).on('dp.change', function (ev) {
        endDate = new Date(ev.date);
        endDate = Date.parse(ev.date) / 1000;

        if (startDate != null) {
            if (endDate < startDate) {
                alert("To date should be greater than from date.")
                $('#end_Date').val("");
                endDate = "";
                return false;
            }
        }
    });


    $('.edit-campaign').click(function () {
        $("#campaign_form_").validate().resetForm();

        if ($(this).data('action') == "Add") {
            $('#campaign_form_').trigger("reset");
            $('#eStatus option[value="Cancelled"]').hide();
            $('#eStatus').val('Active').change();
            $('#status-section').hide();
            $('#start_Date').prop('readonly', false);
            $('#start_Date').prop('disabled', false);
        }

        if ($(this).data('action') == "Edit") {
            $('#eStatus option[value="Cancelled"]').show();

            campaign_id = $(this).data('id');
            var title = $(this).data('title');
            var startdate = $(this).data('startdate');
            var enddate = $(this).data('enddate');
            var estatus = $(this).data('estatus');
            var ecurrentactive = $(this).data('ecurrentactive');
            $('#status-section').show();
            $('#start_Date').val(startdate);

            $("#end_Date").val(enddate);

            var toDayDate = new Date();
            toDayDate = Date.parse(toDayDate) / 1000;

            var newDateTime = moment(startdate, "DD-MM-YYYY").toDate();
            startDate = Date.parse(newDateTime) / 1000;

            var newenddate = moment(enddate, "DD-MM-YYYY").toDate();
            endDate = Date.parse(newenddate) / 1000;

            if (ecurrentactive == "Yes") {

                if (toDayDate > startDate) {
                    dp4 = true;
                } else {
                    dp4 = false;
                }

                if (toDayDate > endDate) {
                    dp5 = true;
                } else {
                    dp5 = false;
                }

            } else {

                if (toDayDate > startDate) {
                    dp4 = true;
                } else {
                    dp4 = false;
                }

                if (toDayDate > endDate) {
                    dp5 = true;
                } else {
                    dp5 = false;
                }
            }
            $("#start_Date").prop('disabled', dp4);
            if (dp4 == true) {
                $("#start_Date").removeClass('readonly-custom');
            } else {
                $("#start_Date").addClass('readonly-custom');
            }

            $("#end_Date").prop('disabled', dp5);
            if (dp5 == true) {
                $("#end_Date").removeClass('readonly-custom');
            } else {
                $("#end_Date").addClass('readonly-custom');
            }


            $('#vTitle_Default').val(title);
            $('#campaign_form_id').val(campaign_id);
        }

        $('#add_edit_campaign_modal').modal('show');
    });

    function campaignAction() {

        var vTitle_Default_ = $("#vTitle_Default").val();
        console.log(vTitle_Default_);
        var start_Date_ = $("#start_Date").val();
        var end_Date_ = $("#end_Date").val();
        if (start_Date_ == '') {
            alert("Please Enter From Date.");
            return false;
        } else if (end_Date_ == '') {
            alert("Please Enter To Date.")
            return false;
        } else if ($.trim(vTitle_Default_) == '') {
            alert("Please Enter Title.")
            return false;
        } else if (endDate < startDate) {
            alert("From date should be lesser than To date.")
            return false;
        } else {
            $('#campaign_form_').submit();
        }

    }

    /* ---------------------------- for the campaign ---------------------------- */

    function editDescription(action, id) {
        $('#modal_action').html(action);
        $('#coupon_desc_Modal' + id).modal('show');
    }

    function saveDescription(id) {
        if ($('#vLevel_' + id + '<?php echo $default_lang; ?>').val() == "") {
            $('#vLevel_' + id + '<?php echo $default_lang; ?>_error').show();
            $('#vLevel_' + id + '<?php echo $default_lang; ?>').focus();
            clearInterval(myVar);
            myVar = setTimeout(function () {
                $('#vLevel_' + id + '<?php echo $default_lang; ?>_error').hide();
            }, 5000);
            return false;
        }
        $('#vLevel_' + id + 'Default').val($('#vLevel_' + id + '<?php echo $default_lang; ?>').val());
        $('#vLevel_' + id + 'Default').closest('.row').removeClass('has-error');
        $('#vLevel_' + id + 'Default-error').remove();
        $('#coupon_desc_Modal' + id).modal('hide');
    }

    var errormessage;

    $('#campaign_form_').validate({
        ignore: 'input[type=hidden]',
        errorClass: 'help-block error',
        errorElement: 'span',
        rules: {
            vTitle_Default: {
                required: true,
                normalizer: function (value) {
                    return $.trim(value);
                }, minlength: 2
            },
            startDate: {
                required: true,
                remote: {
                    url: 'reward.php',
                    type: "post",
                    data: {
                        startDate: $('#start_Date').find('input').val(),
                        validation_action: 'Yes',
                        campaign_id: function () {
                            return $('#campaign_form_id').val();
                        }
                    },
                    dataFilter: function (response) {
                        if (response == 'No') {
                            errormessage = 'Campaign "From Date" lies within already defined active campaigns.';
                            return false;
                        } else {
                            return true;
                        }
                    },
                    async: false
                }
            },
            endDate: {
                required: true,
                remote: {
                    url: 'reward.php',
                    type: "post",
                    data: {
                        endDate: $('#end_Date').find('input').val(),
                        validation_action: 'Yes',
                        campaign_id: function () {
                            return $('#campaign_form_id').val();
                        }
                    },
                    dataFilter: function (response) {
                        if (response == 'No') {
                            errormessage = 'Campaign "To Date" lies within already defined active campaigns.';
                            return false;
                        } else {
                            return true;
                        }
                    },
                    async: false
                }
            },
        },
        messages: {
            startDate: {
                remote: function () {
                    return errormessage;
                }
            },
            endDate: {
                remote: function () {
                    return errormessage;
                }
            },
        },
        submitHandler: function (form) {
            if ($(form).valid())
                form.submit();
            return false; // prevent normal form posting
        }
    });
</script>
</body>
</html>
