<?php
include_once '../common.php';

include_once '../send_bidding_service_receipt.php';

if (!$userObj->hasPermission('view-bids-invoice')) {
    $userObj->redirect();
}
$script = 'Bids';
$iBiddingPostId = $_REQUEST['iBiddingPostId'] ?? '';
$db_trip_data = $BIDDING_OBJ->getFareDetailsGeneral($iBiddingPostId, '', '');
if (isset($_REQUEST['test'])) {
    // echo"<pre>";print_r($db_trip_data);die;
}

?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD-->

<head>
    <meta charset="UTF-8" />
    <title>Admin | Invoice</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />
    <meta content="" name="keywords" />
    <meta content="" name="description" />
    <meta content="" name="author" />
    <?php include_once 'global_files.php'; ?>
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places&key=<?php echo $GOOGLE_SEVER_API_KEY_WEB; ?>"></script>
    <link href="../assets/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet" />
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
            <div class="inner" id="page_height" style="">
                <div class="row">
                    <div class="col-lg-12">
                        <h2>Invoice</h2>
                        <input type="button" class="add-btn" value="Close" onClick="javascript:window.top.close();">
                        <div style="clear:both;"></div>
                    </div>
                </div>
                <hr />
                <?php if (isset($_REQUEST['success']) && 1 === $_REQUEST['success']) { ?>
                    <div class="alert alert-success paddiing-10">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                        Email has been sent successfully to the respective E-mail address.
                    </div>
                <?php } elseif (isset($_REQUEST['fail']) && 0 === $_REQUEST['fail']) { ?>
                    <div class="alert alert-danger paddiing-10">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                        It seems you doesn't added email in profile so we can't proceed to send email.
                    </div>
                <?php } ?>
                <?php
                // echo "<pre>";print_r($db_trip_data);die;
                $systemTimeZone = date_default_timezone_get();
$dBookingDate = $endDate = converToTz($db_trip_data['dBiddingDate'], $db_trip_data['vTimeZone'], $systemTimeZone);

?>
                <div class="table-list">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <b>Your <?php echo $langage_lbl_admin['LBL_BIDDING_TXT']; ?> </b>
                                    <?php
                    if ('0000-00-00 00:00:00' === $db_trip_data['dBiddingDate']) {
                        echo 'Was Cancelled.';
                    } else {
                        echo @date('h:i A', @strtotime($dBookingDate));
                        ?> on <?php echo @date('d M Y', @strtotime($dBookingDate));
                    }?>
                                </div>
                                <div class="panel-body rider-invoice-new">
                                    <div class="row">
                                    <div class="col-sm-6 rider-invoice-new-left">
                                        <span class="location-from"><i class="icon-map-marker"></i>
                                            <b><?php echo @date('h:i A', @strtotime($dBookingDate)); ?></b>
                                            <p><?php echo $db_trip_data['tSaddress']; ?></p>
                                        </span>
                                        <?php $class_name = 'col-sm-6';
$style = "style='text-align:center;width:100%;'"; ?>
                                            <div class="rider-invoice-bottom">
                                                <div class="<?php echo $class_name; ?>" <?php echo $style; ?>>
                                                    <?php echo $langage_lbl_admin['LBL_BIDDING_TXT']; ?>
                                                    <br />
                                                    <b>
                                                    <?php echo $db_trip_data['vServiceDetailTitle']; ?>
                                                    </b>
                                                    <br />
                                                </div>
                                            </div>

                                            <div class="rider-invoice-bottom row">
                                                <?php if ($db_trip_data['iDriverId'] > 0) { ?>
                                                <div class="col-sm-6">
                                                    <div class="row">
                                                        <div class="left col-sm-3">
                                                        <?php
                if (remote_file_exists($db_trip_data['driverImage'])) {
                    $img = $db_trip_data['driverImage'];
                } else {
                    $img = $tconfig['tsite_url'].'webimages/icons/help/driver.png';
                } ?>
                                                            <img src="<?php echo $img; ?>" style="outline:none;text-decoration:none;display:inline-block;width:45px!important;min-height:45px!important;border-radius:50em;max-width:45px!important;min-width:45px!important;border:1px solid #d7d7d7" align="left" height="45" width="45" class="CToWUd">
                                                        </div>
                                                        <div class="right col-sm-9" style="word-wrap: break-word;">
                                                            <div><b><?php echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']; ?></b></div>

                                                            <div><?php echo clearName($db_trip_data['driverName']); ?></div>
                                                            <div><?php echo clearEmail($db_trip_data['drivermail']); ?></div>
                                                            <?php if (!empty($db_trip_data['driverAvgRating'])) { ?>
                                                                <br>
                                                                <div><b>Rating</b></div>
                                                                <div><img src="<?php echo $tconfig['tsite_url'].'assets/img/star.jpg'; ?>" style="margin: 0 2px 4px 0"> <?php echo $db_trip_data['driverAvgRating']; ?></div>
                                                            <?php } ?>

                                                        </div>
                                                    </div>
                                                </div>
                                                <?php } ?>
                                                <div class="col-sm-6">
                                                    <div class="row">
                                                        <div class="left col-sm-3">
                                                            <?php
                    if (remote_file_exists($db_trip_data['userImage'])) {
                        $img1 = $db_trip_data['userImage'];
                    } else {
                        $img1 = $tconfig['tsite_url'].'webimages/icons/help/taxi_passanger.png';
                    } ?>
                                                            <img src="<?php echo $img1; ?>" style="outline:none;text-decoration:none;display:inline-block;width:45px!important;min-height:45px!important;border-radius:50em;max-width:45px!important;min-width:45px!important;border:1px solid #d7d7d7" align="left" height="45" width="45" class="CToWUd">
                                                        </div>
                                                        <div class="right col-sm-9" style="word-wrap: break-word;">
                                                            <div><b><?php echo $langage_lbl_admin['LBL_RIDER']; ?></b></div>

                                                            <div><?php echo clearName($db_trip_data['userName']); ?></div>
                                                            <div><?php echo clearEmail($db_trip_data['usermail']); ?></div>
                                                            <?php if (!empty($db_trip_data['userAvgRating'])) { ?>
                                                                <br>
                                                                <div><b>Rating</b></div>
                                                                <div><img src="<?php echo $tconfig['tsite_url'].'assets/img/star.jpg'; ?>" style="margin: 0 2px 4px 0"> <?php echo $db_trip_data['userAvgRating']; ?></div>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-sm-6 rider-invoice-new-right">

                                            <h4 style="text-align:center;"> <?php echo $langage_lbl_admin['LBL_FARE_BREAKDOWN_BID_NO_TXT']; ?> :<?php echo $db_trip_data['vBiddingPostNo']; ?></h4>
                                            <hr />

                                            <table style="width:100%" cellpadding="5" cellspacing="0" border="0">

                                                <tbody>
                                                    <?php
                                                    $userlangcode = $_SESSION['sess_lang'];
$languageLabelsArr = $LANG_OBJ->FetchLanguageLabelsWeb($userlangcode, '1');
foreach ($db_trip_data['FareDetailsNewArr'] as $key => $value) {
    foreach ($value as $k => $val) {
        if ($k === $langage_lbl_admin['LBL_SUBTOTAL_TXT']) {
            continue;
        }
        if ('eDisplaySeperator' === $k) {
            echo '<tr><td colspan="2"><div style="border-top:1px dashed #d1d1d1"></div></td></tr>';
        } else {
            ?>
                                                                <tr>
                                                                    <td><?php echo $k; ?></td>
                                                                    <td align="right"><?php echo $val; ?></td>
                                                                </tr>
                                                    <?php
        }
    }
}
?>

                                                    <tr>
                                                        <td><b>
                                                                <?php echo $langage_lbl_admin['LBL_Total_Fare_TXT']; ?> (Via <?php echo $db_trip_data['vBiddingPaymentMode']; ?>)
                                                            </b>
                                                        </td>
                                                        <td align="right">
                                                            <b>
                                                                <?php echo $db_trip_data['FareSubTotal']; ?>
                                                            </b>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <?php if ('Cancelled' === $db_trip_data['eStatus']) {
                                                ?>
                                                <table style="border:dotted 2px #000000;" cellpadding="5px" cellspacing="0" width="100%">
                                                    <tr>
                                                        <td>
                                                            <b>
                                                                <?php
                                                                    if ('Driver' === $db_trip_data['eCancelledBy']) {
                                                                        echo $langage_lbl_admin['LBL_TRIP_CANCELLED_BY_DRIVER_ADMIN'];
                                                                        echo '<br/>';
                                                                        if (!empty($db_trip_data['vCancelReason'])) {
                                                                            echo 'Reason: '.$db_trip_data['vCancelReason'];
                                                                        }
                                                                    } elseif ('User' === $db_trip_data['eCancelledBy']) {
                                                                        echo $langage_lbl_admin['LBL_TRIP_CANCELLED_BY_PASSANGER_ADMIN'];
                                                                        echo '<br/>';
                                                                        if (!empty($db_trip_data['vCancelReason'])) {
                                                                            echo 'Reason: '.$db_trip_data['vCancelReason'];
                                                                        }
                                                                    } else {
                                                                        echo $langage_lbl_admin['LBL_CANCELED_TRIP_ADMIN_TXT'];
                                                                    }
                                                ?>
                                                            </b>
                                                        </td>
                                                    </tr>
                                                </table><br>
                                            <?php } ?>
                                            <?php if (isset($db_trip_data['fCommission'])) {
                                                ?>
                                                <table style="border:dotted 2px #000000;" cellpadding="5px" cellspacing="2px" width="100%">
                                                    <tr>
                                                        <td><b><?php echo $langage_lbl_admin['LBL_Commision']; ?></b></td>
                                                        <td align="right"><b><?php echo $db_trip_data['fCommission']; ?></b></td>
                                                    </tr>
                                                </table><br>
                                            <?php } ?>

                                            <div style="clear:both;"></div>
                                        </div>
                                        <div class="clear"></div>


                                            <div class="row invoice-email-but">
                                                <span>
                                                    <a href="../send_bidding_service_receipt.php?action_from=mail&iBiddingPostId=<?php echo $iBiddingPostId; ?>"><button class="btn btn-primary ">E-mail</button></a>
                                                </span>
                                            </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="clear"></div>
                </div>
            </div>
        </div>
        <!--END PAGE CONTENT -->
    </div>
    <!--END MAIN WRAPPER -->

    <?php include_once 'footer.php'; ?>
</body>
<!-- END BODY-->
</html>