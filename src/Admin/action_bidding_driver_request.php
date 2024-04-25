<?php
include_once '../common.php';

$script = 'biddingDriverRequest';
$lang = $LANG_OBJ->FetchDefaultLangData('vCode');

$did = base64_decode(base64_decode($_REQUEST['did'], true), true);
$sql = 'SELECT vName,vLastName FROM register_driver WHERE iDriverId = '.$did;
$dDetails = $obj->MySQLSelect($sql);

$name = $dDetails[0]['vName'];
$vLastName = $dDetails[0]['vLastName'];

$sql_vehicle_category_table_name = getVehicleCategoryTblName();

$sql = "SELECT dsr.*  FROM register_driver AS rd
        JOIN {$BIDDING_OBJ->bidding_driver_request} AS dsr ON dsr.iDriverId = rd.iDriverId
        WHERE dsr.iDriverId = ".$did." AND dsr.eRequestStatus  = 'Pending'";
$Requests = $obj->MySQLSelect($sql);

$success = $_REQUEST['success'] ?? 0;

if (isset($_POST['submit'])) {
    $status = $_POST['status'];
    $iBiddingId = $_POST['iBiddingId'];
    $driverId = $_POST['driverId'];

    if (SITE_TYPE === 'Demo') {
        header('Location:action_bidding_driver_request.php?did='.$_REQUEST['did'].'&success=2');

        exit;
    }

    $biddingdriverservice = $BIDDING_OBJ->biddingDriverService('webservice', $driverId);

    $existingServices = [];
    if (count($biddingdriverservice)) {
        $existingServices = explode(',', $biddingdriverservice[0]['vBiddingId']);
    }

    $rejectedServices = [];
    $newServices = [];
    foreach ($status as $key => $value) {
        if ('Approve' === $status[$key]) {
            $newServices[] = $iBiddingId[$key];
        }
        if ('Reject' === $status[$key]) {
            $rejectedServices[] = $iBiddingId[$key];
        }
    }
    $allServices = implode(',', array_merge($newServices, $existingServices));

    $sqlu = 'UPDATE '.$BIDDING_OBJ->bidding_driver_service.' SET vBiddingId = "'.$allServices.'" WHERE iDriverId = "'.$did.'"';
    $existingServices = $obj->sql_query($sqlu);

    $existingServicesdb = $obj->MySQLSelect("SELECT *,iDriverId as iMemberId FROM `register_driver` WHERE iDriverId='{$driverId}'");

    if ($existingServices) {
        $rejectedNewServies = array_merge($newServices, $rejectedServices);
        if (!empty($rejectedNewServies)) {
            // Delete Request as Its Processed
            $sqlDel = 'DELETE FROM '.$BIDDING_OBJ->bidding_driver_request.' WHERE iDriverId = "'.$did.'" AND iBiddingId IN ('.implode(',', $rejectedNewServies).')';
            $obj->sql_query($sqlDel);
        }

        if (!empty($newServices) || !empty($rejectedServices)) {
            // Send Email to Driver
            $getMaildata['vEmail'] = $existingServicesdb[0]['vEmail'];
            $getMaildata['FromName'] = $existingServicesdb[0]['vName'].' '.$existingServicesdb[0]['vLastName'];
            $getMaildata['serviceMsg'] = $langage_lbl_admin['LBL_DRIVER_SERVICE_ACCEPTED_REJECT'];
            $mail = $COMM_MEDIA_OBJ->SendMailToMember('DRIVER_SERVICE_ACCEPTED_REJECT', $getMaildata);
        }

        $_SESSION['success'] = '1';
        $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_INSERT_MSG'];
        header('location:bidding_driver_request.php');
    }
}
$title = clearName($name.' '.$vLastName);
?>
<!DOCTYPE html>
<html lang="en">
    <!-- BEGIN HEAD-->
    <head>
        <meta charset="UTF-8" />
        <title><?php echo $SITE_NAME; ?> | Service Request for  <?php echo $title; ?></title>
        <meta content="width=device-width, initial-scale=1.0" name="viewport" />
        <?php include_once 'global_files.php'; ?>
        <style type="text/css">
            .service-table td label {
                font-weight: normal;
                cursor: pointer;
                margin: 0 0 5px 5px;
            }
        </style>
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
                                <h2>Bidding Service Request for  <?php echo $title; ?></h2>

                                <a class="back_link" href="bidding_driver_request.php">
                                <input type="button" value="Back to Listing" class="add-btn">
                            </a>
                            </div>

                        </div>
                        <hr />
                    </div>

                    <div class="table-list">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="admin-nir-export">
                                    <form method="POST">
                                    <input type='hidden' name="driverId" value="<?php echo $did; ?>">
                                    <?php if (2 === $success) { ?>
                                    <div class="alert alert-danger alert-dismissable ">
                                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                                        <?php echo $langage_lbl_admin['LBL_EDIT_DELETE_RECORD']; ?>
                                    </div>
                                    <!-- <br/> -->
                                    <?php } ?>
                                    <?php if (!empty($Requests)) {
                                        // echo '<pre>' ; print_r($Requests);
                                        ?>
                                            <table class="table table-striped table-bordered table-hover service-table">
                                                <thead>
                                                    <tr>
                                                        <th class="align-center">#</th>
                                                        <th>Category </th>
                                                        <th>Service Name </th>
                                                        <th class="align-center">Requested Value</th>
                                                        <th class="align-center">Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                        <?php
                                                            foreach ($Requests as $key => $Request) {
                                                                $sql = "SELECT JSON_UNQUOTE(JSON_EXTRACT(bss.Vtitle, '$.vTitle_".$lang."')) as vTitle,JSON_UNQUOTE(JSON_EXTRACT(bs.Vtitle, '$.vTitle_".$lang."')) as parent FROM {$BIDDING_OBJ->tablename} AS bss
                                                                LEFT JOIN ".$BIDDING_OBJ->tablename.' AS bs ON bs.iBiddingId  = bss.iParentId
                                                                WHERE bss.iBiddingId = '.$Request['iBiddingId'];
                                                                $existingServices = $obj->MySQLSelect($sql);

                                                                echo '<tr>';
                                                                echo '<td class="text-center">'.($key + 1).'</td>';
                                                                echo '<td>'.$existingServices[0]['parent'].'</td>';
                                                                echo '<td><strong>'.$existingServices[0]['vTitle'].'</strong></td>';
                                                                echo '<td class="text-center">Enable </td>';
                                                                echo '<td  class="text-left">
                                                                        <input type="radio" name="status['.$key.']" id="status1_['.$key.']" value="Pending" checked><label for="status1_['.$key.']">Pending  </label><br>
                                                                        <input type="radio" name="status['.$key.']" id="status2_['.$key.']" value="Approve"><label for="status2_['.$key.']">Approve  </label><br>
                                                                        <input type="radio" name="status['.$key.']" id="status3_['.$key.']" value="Reject"><label for="status3_['.$key.']">Reject  </label>
                                                                        <input type="hidden" name="iBiddingId[]" value="'.$Request['iBiddingId'].'">
                                                                        </td>';
                                                                echo '</tr>';
                                                            }
                                        ?>

                                                </tbody>
                                            </table>

                                        <?php if ($userObj->hasPermission('update-providers-bidding-requests')) { ?>
                                        <input type="submit" name="submit" value="Process Request" class="btn btn-primary">
                                            <?php } ?>
                                        <?php } ?>
                                    </form>
                                </div>
                                <div style="clear:both;"></div>
                                <div class="table-responsive">

                                </div>
                            </div> <!--TABLE-END-->
                        </div>
                    </div>
                    <div class="admin-notes">
                        <h4>Notes:</h4>
                        <ul>
                            <li> This module will list the details of all the services requested by the providers.</li>
                            <li> Administrator can take appropriate action (Approve , Reject , Pending). </li>
                            <li> Pending request will remain here, which the admin can approve or reject on later stage. </li>
                        </ul>
                    </div>
                </div>
            </div>
            <!--END PAGE CONTENT -->
        </div>
        <!--END MAIN WRAPPER -->


        <?php
        include_once 'footer.php';
?>
    <!-- END BODY-->
</html>