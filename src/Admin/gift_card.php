<?php
include_once '../common.php';
if (!$userObj->hasPermission('view-giftcard')) {
    $userObj->redirect();
}
$script = 'GiftCard';
// Start Sorting
$sortby = $_REQUEST['sortby'] ?? 0;
$order = $_REQUEST['order'] ?? '';
$ord = ' ORDER BY iGiftCardId DESC';
if (1 === $sortby) {
    if (0 === $order) {
        $ord = ' ORDER BY vGiftCardCode ASC';
    } else {
        $ord = ' ORDER BY vGiftCardCode DESC';
    }
}
if (2 === $sortby) {
    if (0 === $order) {
        $ord = " ORDER BY JSON_VALUE(tDescription,'$.tDescription_{$default_lang}') ASC";
    } else {
        $ord = " ORDER BY JSON_VALUE(tDescription,'$.tDescription_{$default_lang}') DESC";
    }
}
if (3 === $sortby) {
    if (0 === $order) {
        $ord = ' ORDER BY fAmount ASC';
    } else {
        $ord = ' ORDER BY fAmount DESC';
    }
}
if (4 === $sortby) {
    if (0 === $order) {
        $ord = ' ORDER BY eStatus ASC';
    } else {
        $ord = ' ORDER BY eStatus DESC';
    }
}
// End Sorting
// For Currency
$sql = "select vSymbol from  currency where eDefault='Yes'";
$db_currency = $obj->MySQLSelect($sql);
// Start Search Parameters
$option = isset($_REQUEST['option']) ? stripslashes($_REQUEST['option']) : '';
$GiftCardCode = isset($_REQUEST['GiftCardCode']) ? stripslashes($_REQUEST['GiftCardCode']) : '';
$searchDate = $_REQUEST['searchDate'] ?? '';
$eStatus = $_REQUEST['eStatus'] ?? '';
$eRedeemed = $_REQUEST['eRedeemed'] ?? 'No';
$startDate = $_REQUEST['startDate'] ?? '';
$endDate = $_REQUEST['endDate'] ?? '';
$searchRider = $_REQUEST['searchRider'] ?? '';
$searchDriver = $_REQUEST['searchDriver'] ?? '';
$CreatedBy = $_REQUEST['CreatedBy'] ?? '';
$ssql = '';
if ('' !== $searchRider && 'User' === $CreatedBy) {
    $ssql .= " AND iCreatedById ='".$searchRider."' AND eCreatedBy='User'";
} elseif ('' !== $searchDriver && 'Driver' === $CreatedBy) {
    $ssql .= " AND iCreatedById ='".$searchDriver."' AND eCreatedBy='Driver'";
} elseif ('Admin' === $CreatedBy) {
    $ssql .= "  AND eCreatedBy='Admin'";
}
if ('' !== $startDate) {
    $ssql .= " AND Date(dAddedDate) >='".$startDate."'";
}
if ('' !== $endDate) {
    $ssql .= " AND Date(dAddedDate) <='".$endDate."'";
}
if ('' !== $GiftCardCode) {
    $ssql .= " AND (vGiftCardCode LIKE '%".clean($GiftCardCode)."%')";
}
if ('' !== $eStatus && '' === $keyword) {
    $ssql .= " AND eStatus = '".clean($eStatus)."'";
} elseif ('' !== $eStatus) {
    $ssql .= " AND eStatus = '".$eStatus."'";
} else {
    $ssql .= " AND eStatus != 'Deleted'";
}
if ('Yes' === $eRedeemed) {
    $ssql .= " AND eRedeemed = 'Yes'";
} elseif ('' === $eRedeemed) {
    $ssql .= '';
} else {
    $ssql .= " AND eRedeemed = 'No'";
}
// End Search Parameters
// Pagination Start
$per_page = $DISPLAY_RECORD_NUMBER; // number of results to show per page
$sql = "SELECT COUNT(iGiftCardId) AS Total FROM gift_cards WHERE 1 =1 {$ssql} ";
$totalData = $obj->MySQLSelect($sql);
$total_results = $totalData[0]['Total'];
$total_pages = ceil($total_results / $per_page); // total pages we going to have
$show_page = 1;
// -------------if page is setcheck------------------//
$start = 0;
$end = $per_page;
if (isset($_GET['page'])) {
    $show_page = $_GET['page'];             // it will telles the current page
    if ($show_page > 0 && $show_page <= $total_pages) {
        $start = ($show_page - 1) * $per_page;
        $end = $start + $per_page;
    }
}
// display pagination
$page = isset($_GET['page']) ? (int) ($_GET['page']) : 0;
$tpages = $total_pages;
if ($page <= 0) {
    $page = 1;
}
// Pagination End
$sql = "SELECT *,
        CASE WHEN eCreatedBy='User' THEN (select CONCAT(vName ,' ',vLastName) as name FROM register_user WHERE iUserId = iCreatedById)
         WHEN eCreatedBy='Driver' THEN (select CONCAT(vName ,' ',vLastName) as name FROM register_driver WHERE iDriverId  = iCreatedById)
            ELSE 'Admin'
            END userName,
        JSON_UNQUOTE(JSON_EXTRACT(tDescription, '$.tDescription_".$default_lang."')) as tDescription
        FROM gift_cards
        WHERE 1=1 {$ssql} {$ord} LIMIT {$start}, {$per_page}";
$data_drv = $obj->MySQLSelect($sql);
$endRecord = count($data_drv);
$var_filter = '';
foreach ($_REQUEST as $key => $val) {
    if ('tpages' !== $key && 'page' !== $key) {
        $var_filter .= "&{$key}=".stripslashes($val);
    }
}
$reload = $_SERVER['PHP_SELF'].'?tpages='.$tpages.$var_filter;
$driverId = $userId = [];
foreach ($data_drv as $data) {
    if ('Admin' === $data['eCreatedBy'] || 'Yes' === $data['eRedeemed']) {
        if ('DriverSpecific' === $data['eUserType']) {
            $driverId[] = $data['iMemberId'];
        }
        if ('UserSpecific' === $data['eUserType']) {
            $userId[] = $data['iMemberId'];
        }
        if ('Anyone' === $data['eUserType'] || 'Yes' === $data['eRedeemed']) {
            if ('Driver' === $data['eReceiverUserType']) {
                $driverId[] = $data['eReceiverId'];
            }
            if ('Passenger' === $data['eReceiverUserType']) {
                $userId[] = $data['eReceiverId'];
            }
        }
    }
}
$driverId = implode(',', $driverId);
$userId = implode(',', $userId);
$sql = "SELECT iDriverId, concat(vName,' ',vLastName) as tReceiverName,vEmail AS tReceiverEmail,vCode AS vReceiverPhoneCode ,vPhone AS vReceiverPhone from  register_driver WHERE iDriverId IN ({$driverId})";
$registerDriver = $obj->MySQLSelect($sql);
$sql = "SELECT iUserId,  concat(vName,' ',vLastName) as tReceiverName,vEmail AS tReceiverEmail,vPhoneCode AS vReceiverPhoneCode ,vPhone AS vReceiverPhone from  register_user WHERE iUserId IN ({$userId})";
$registerUser = $obj->MySQLSelect($sql);
$registerDriverArr = [];
if (isset($registerDriver) && !empty($registerDriver)) {
    foreach ($registerDriver as $Driver) {
        $registerDriverArr[$Driver['iDriverId']] = $Driver;
    }
}
$registerUserArr = [];
if (isset($registerUser) && !empty($registerUser)) {
    foreach ($registerUser as $User) {
        $registerUserArr[$User['iUserId']] = $User;
    }
}
$Today = date('Y-m-d');
$tdate = date('d') - 1;
$mdate = date('d');
$Yesterday = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') - 1, date('Y')));
$curryearFDate = date('Y-m-d', mktime(0, 0, 0, '1', '1', date('Y')));
$curryearTDate = date('Y-m-d', mktime(0, 0, 0, '12', '31', date('Y')));
$prevyearFDate = date('Y-m-d', mktime(0, 0, 0, '1', '1', date('Y') - 1));
$prevyearTDate = date('Y-m-d', mktime(0, 0, 0, '12', '31', date('Y') - 1));
$currmonthFDate = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') - $tdate, date('Y')));
$currmonthTDate = date('Y-m-d', mktime(0, 0, 0, date('m') + 1, date('d') - $mdate, date('Y')));
$prevmonthFDate = date('Y-m-d', mktime(0, 0, 0, date('m') - 1, date('d') - $tdate, date('Y')));
$prevmonthTDate = date('Y-m-d', mktime(0, 0, 0, date('m'), date('d') - $mdate, date('Y')));

$monday = date('Y-m-d', strtotime('monday this week'));
$sunday = date('Y-m-d', strtotime('sunday this week'));
$Pmonday = date('Y-m-d', strtotime('monday this week -1 week'));
$Psunday = date('Y-m-d', strtotime('sunday this week -1 week'));

?>
<!DOCTYPE html>
<html lang="en">
<!-- BEGIN HEAD-->
<head>
    <meta charset="UTF-8"/>
    <title><?php echo $SITE_NAME; ?> | Gift Cards</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <?php include_once 'global_files.php'; ?>
    <link rel="stylesheet" href="../assets/css/modal_alert.css"/>
    <style type="text/css">
        .form-group .row {
            padding: 0;
        }
    </style>
</head>
<!-- END  HEAD-->
<!-- BEGIN BODY-->
<body class="padTop53 ">
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
                        <h2>Gift Cards</h2>
                        <a class="add-btn" href="gift_card_action.php" style="text-align: center;">Add GIFT CARD
                        </a>
                    </div>
                </div>
                <hr/>
            </div>
            <?php include 'valid_msg.php'; ?>
            <form name="frmsearch" id="frmsearch" action="javascript:void(0);" method="post">
                <div class="Posted-date mytrip-page">
                    <input type="hidden" name="action" value="search"/>
                    <h3>Search Gift Card code ...</h3>
                    <span>
                            <a style="cursor:pointer"
                               onClick="return todayDate('dp4', 'dp5');"><?php echo $langage_lbl_admin['LBL_MYTRIP_Today']; ?></a>

                            <a style="cursor:pointer"
                               onClick="return yesterdayDate('dFDate', 'dTDate');"><?php echo $langage_lbl_admin['LBL_MYTRIP_Yesterday']; ?></a>

                            <a style="cursor:pointer"
                               onClick="return currentweekDate('dFDate', 'dTDate');"><?php echo $langage_lbl_admin['LBL_MYTRIP_Current_Week']; ?></a>

                            <a style="cursor:pointer"
                               onClick="return previousweekDate('dFDate', 'dTDate');"><?php echo $langage_lbl_admin['LBL_MYTRIP_Previous_Week']; ?></a>

                            <a style="cursor:pointer"
                               onClick="return currentmonthDate('dFDate', 'dTDate');"><?php echo $langage_lbl_admin['LBL_MYTRIP_Current_Month']; ?></a>

                            <a style="cursor:pointer"
                               onClick="return previousmonthDate('dFDate', 'dTDate');"><?php echo $langage_lbl_admin['LBL_MYTRIP_Previous Month']; ?></a>

                            <a style="cursor:pointer"
                               onClick="return currentyearDate('dFDate', 'dTDate');"><?php echo $langage_lbl_admin['LBL_MYTRIP_Current_Year']; ?></a>

                            <a style="cursor:pointer"
                               onClick="return previousyearDate('dFDate', 'dTDate');"><?php echo $langage_lbl_admin['LBL_MYTRIP_Previous_Year']; ?></a>

                        </span>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-lg-3">
                            <input type="text" id="dp4" name="startDate" placeholder="From Date" class="form-control"
                                   value="" readonly="" style="cursor:default; background-color: #fff"/>
                        </div>
                        <div class="col-lg-3">
                            <input type="text" id="dp5" name="endDate" placeholder="To Date" class="form-control"
                                   value="" readonly="" style="cursor:default; background-color: #fff"/>
                        </div>
                        <div class="col-lg-3">
                            <select name="eStatus" id="estatus_value" class="form-control">
                                <option value="">Select Status</option>
                                <option value='Active' <?php
                                if ('Active' === $eStatus) {
                                    echo 'selected';
                                }
?> >Active
                                </option>
                                <option value="Inactive" <?php
if ('Inactive' === $eStatus) {
    echo 'selected';
}
?> >Inactive
                                </option>
                            </select>
                        </div>
                        <div class="col-lg-3">
                            <select name="eRedeemed" id="eRedeemed" class="form-control">
                                <option value="">Select Redeem Status</option>
                                <option value='Yes' <?php
if ('Yes' === $eRedeemed) {
    echo 'selected';
}
?> >Redeemed
                                </option>
                                <option value="No" <?php
if ('No' === $eRedeemed) {
    echo 'selected';
}
?> >Not Redeemed
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-3">
                        <td width="15%">
                            <input placeholder="Gift Card Code" type="Text" id="GiftCardCode" name="GiftCardCode"
                                   value="<?php echo $GiftCardCode; ?>"
                                   class="form-control"/>
                        </td>
                    </div>
                    <div class="col-lg-3">
                        <select onchange="chnageUserType(this)" name="CreatedBy" id="CreatedBy" class="form-control">
                            <option value="">Created By</option>
                            <option value='User' <?php
                            if ('User' === $CreatedBy) {
                                echo 'selected';
                            }
?> >User
                            </option>
                            <option value="Driver" <?php
if ('Driver' === $CreatedBy) {
    echo 'selected';
}
?> >Driver
                            </option>
                            <option value="Admin" <?php
if ('Admin' === $CreatedBy) {
    echo 'selected';
}
?> >Admin
                            </option>
                        </select>
                    </div>
                    <div class="col-lg-3 searchDriver_div">
                        <select class="form-control filter-by-text driver_container" name='searchDriver'
                                data-text="Select <?php echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']; ?>"
                                id="searchDriver">
                            <option value="">Select <?php echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']; ?></option>
                        </select>
                    </div>
                    <div class="col-lg-3 searchRider_div">
                        <select class="form-control filter-by-text" name='searchRider'
                                data-text="Select <?php echo $langage_lbl_admin['LBL_RIDER_NAME_TXT_ADMIN']; ?>"
                                id="searchRider">
                            <option value="">Select <?php echo $langage_lbl_admin['LBL_RIDER_NAME_TXT_ADMIN']; ?></option>
                        </select>
                    </div>
                </div>
                <div class="tripBtns001">
                    <b>
                        <input type="submit" value="Search" class="btnalt button11" id="Search" name="Search"
                               title="Search"/>
                        <input type="button" value="Reset" class="btnalt button11"
                               onClick="window.location.href = 'gift_card.php'"/>
                    </b>
                </div>
            </form>
            <div class="table-list">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="admin-nir-export">
                            <div class="changeStatus col-lg-12 option-box-left">
                                <span class="col-lg-2 new-select001">
                                    <?php if ($userObj->hasPermission(['delete-giftcard', 'update-status-giftcard']) && 'Yes' !== $eRedeemed) { ?>
                                        <select name="changeStatus" id="changeStatus" class="form-control"
                                                onchange="ChangeStatusAll(this.value);">
                                            <option value="">Select Action</option>
                                            <?php if ($userObj->hasPermission('update-status-giftcard')) { ?>
                                                <option value='Active' <?php
                    if ('Active' === $option) {
                        echo 'selected';
                    }
                                                ?> >Activate
                                                </option>
                                                <option value="Inactive" <?php
                                                if ('Inactive' === $option) {
                                                    echo 'selected';
                                                }
                                                ?> >Deactivate
                                                </option>
                                            <?php } ?>
                                        </select>
                                    <?php } ?>
                                </span>
                            </div>
                            <?php if (!empty($data_drv)) { ?>
                                <!--<div style = "disply:none" class="panel-heading">
                                    <form name="_export_form" id="_export_form" method="post">
                                        <button type="button" onclick="reportExportTypes('gift_card')">Export</button>
                                    </form>
                                </div>-->
                            <?php } ?>
                        </div>
                        <div style="clear:both;"></div>
                        <div class="table-responsive">
                            <form class="_list_form" id="_list_form" method="post"
                                  action="<?php echo $_SERVER['PHP_SELF']; ?>">
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                    <tr>
                                        <?php if ($userObj->hasPermission(['delete-giftcard', 'update-status-giftcard']) && 'Yes' !== $eRedeemed) { ?>
                                            <th align="center" width="" style="text-align:center;">
                                                <input type="checkbox" id="setAllCheck">
                                            </th>
                                        <?php } ?>
                                        <th width="">
                                            <a href="javascript:void(0);" onClick="Redirect(1,<?php
                                            if ('1' === $sortby) {
                                                echo $order;
                                            } else {
                                                ?>0<?php } ?>)">Gift Card Code <?php
                                                if (1 === $sortby) {
                                                    if (0 === $order) {
                                                        ?>
                                                        <i class="fa fa-sort-amount-asc"
                                                           aria-hidden="true"></i> <?php } else { ?>
                                                        <i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php
                                                           }
                                                } else {
                                                    ?>
                                                    <i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a>
                                        </th>
                                        <?php /* ?><th width=""><a href="javascript:void(0);" onClick="Redirect(2,<?php
                                                        if ($sortby == 2) {
                                                            echo $order;
                                                        } else {
                                                            ?>0<?php } ?>)">Gift Card Name <?php
                                                        if ($sortby == 2) {
                                                            if ($order == 0) {
                                                                ?><i class="fa fa-sort-amount-asc" aria-hidden="true"></i> <?php } else { ?><i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php
                                                                }
                                                            } else {
                                                                ?><i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a></th> <?php */ ?>
                                        <th width="">
                                            <a href="javascript:void(0);" onClick="Redirect(3,<?php
                                            if ('3' === $sortby) {
                                                echo $order;
                                            } else {
                                                ?>0<?php } ?>)">Amount <?php
                                                if (3 === $sortby) {
                                                    if (0 === $order) {
                                                        ?>
                                                        <i class="fa fa-sort-amount-asc"
                                                           aria-hidden="true"></i> <?php } else { ?>
                                                        <i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php
                                                           }
                                                } else {
                                                    ?>
                                                    <i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a>
                                        </th>
                                        <th width="">Created By</th>
                                        <th width="">Receiver Details</th>
                                        <th width="">Is Redeemed?</th>
                                        <th width="">Redeemed By</th>
                                        <th width="">Send Gift Card
                                            <i class="icon-question-sign" data-placement="bottom" data-toggle="tooltip"
                                               data-original-title="System will send an email & sms to the Receiver by pressing 'Send' button."></i>
                                        </th>
                                        <th width="" align="center" style="text-align:center;">
                                            <a href="javascript:void(0);" onClick="Redirect(4,<?php
                                            if ('4' === $sortby) {
                                                echo $order;
                                            } else {
                                                ?>0<?php } ?>)">Status <?php
                                                if (4 === $sortby) {
                                                    if (0 === $order) {
                                                        ?>
                                                        <i class="fa fa-sort-amount-asc"
                                                           aria-hidden="true"></i> <?php } else { ?>
                                                        <i class="fa fa-sort-amount-desc" aria-hidden="true"></i><?php
                                                           }
                                                } else {
                                                    ?>
                                                    <i class="fa fa-sort" aria-hidden="true"></i> <?php } ?></a>
                                        </th>
                                        <th style="display: none">Preview</th>
                                        <th>Created Date</th>
                                        <?php if ($userObj->hasPermission(['edit-giftcard', 'update-status-giftcard', 'delete-giftcard'])) { ?>
                                            <th width="" align="center" style="text-align:center;">Action</th>
                                        <?php } ?>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    if (!empty($data_drv)) {
                                        for ($i = 0; $i < count($data_drv); ++$i) {
                                            $tReceiverDetails_ORG = $tReceiverDetails = json_decode($data_drv[$i]['tReceiverDetails'], true);
                                            if ('Admin' === $data_drv[$i]['eCreatedBy'] || 'Yes' === $data_drv[$i]['eRedeemed']) {
                                                if ('DriverSpecific' === $data_drv[$i]['eUserType']) {
                                                    $tReceiverDetails = $registerDriverArr[$data_drv[$i]['iMemberId']];
                                                    $tReceiverDetails['usertype'] = 'Driver';
                                                }
                                                if ('UserSpecific' === $data_drv[$i]['eUserType']) {
                                                    $tReceiverDetails = $registerUserArr[$data_drv[$i]['iMemberId']];
                                                    $tReceiverDetails['usertype'] = 'User';
                                                }
                                                if ('Anyone' === $data_drv[$i]['eUserType'] || 'Yes' === $data_drv[$i]['eRedeemed']) {
                                                    if ('Driver' === $data_drv[$i]['eReceiverUserType']) {
                                                        $tReceiverDetails = $registerDriverArr[$data_drv[$i]['eReceiverId']];
                                                        $tReceiverDetails['usertype'] = 'Driver';
                                                    }
                                                    if ('Passenger' === $data_drv[$i]['eReceiverUserType']) {
                                                        $tReceiverDetails = $registerUserArr[$data_drv[$i]['eReceiverId']];
                                                        $tReceiverDetails['usertype'] = 'User';
                                                    }
                                                }
                                            }
                                            ?>
                                            <tr class="gradeA">
                                                <?php if ($userObj->hasPermission(['delete-giftcard', 'update-status-giftcard']) && 'Yes' !== $eRedeemed) { ?>
                                                <td align="center" style="text-align:center;">
                                                    <?php if ('Yes' !== $data_drv[$i]['eRedeemed']) { ?>
                                                        <input type="checkbox" id="checkbox"
                                                               name="checkbox[]" <?php echo $default; ?>
                                                               value="<?php echo $data_drv[$i]['iGiftCardId']; ?>"/>&nbsp;
                                                    <?php } ?>
                                                </td>
                                                <?php } ?>
                                                <td style="text-transform: uppercase;"><?php echo $data_drv[$i]['vGiftCardCode']; ?></td>
                                                <td><?php echo formateNumAsPerCurrency($data_drv[$i]['fAmount'], $db_currency[0]['vCode']); ?></td>
                                                <td><?php
                                                    if (strtoupper($data_drv[$i]['eCreatedBy']) === strtoupper('admin')) {
                                                        echo $data_drv[$i]['eCreatedBy'];
                                                    } else {
                                                        if ('User' === $data_drv[$i]['eCreatedBy']) {
                                                            ?>
                                                            <?php if ($userObj->hasPermission('view-users')) { ?><a href="javascript:void(0);" onClick="show_rider_details('<?php echo $data_drv[$i]['iCreatedById']; ?>')" style="text-decoration: underline;"><?php } ?><?php echo clearName($data_drv[$i]['userName']).' ('.$data_drv[$i]['eCreatedBy'].')'; ?><?php if ($userObj->hasPermission('view-users')) { ?></a><?php } ?>
                                                            <?php
                                                        } else {
                                                            ?>
                                                            <?php if ($userObj->hasPermission('view-providers')) { ?><a href="javascript:void(0);" onClick="show_driver_details('<?php echo $data_drv[$i]['iCreatedById']; ?>')" style="text-decoration: underline;"><?php } ?><?php echo clearName($data_drv[$i]['userName']).' ('.$data_drv[$i]['eCreatedBy'].')'; ?><?php if ($userObj->hasPermission('view-providers')) { ?></a> <?php } ?>
                                                            <?php
                                                        }
                                                    }
                                            ?>
                                                </td>
                                                <td><?php if ('Admin' !== $data_drv[$i]['eCreatedBy']) { ?>
                                                        <a href="javascript:void(0);"
                                                           onclick="viewReceiverDetails(this)" class="btn btn-info"
                                                           data-vReceiverPhoneCode="<?php echo $tReceiverDetails_ORG['vReceiverPhoneCode']; ?>"
                                                           data-vReceiverPhone="<?php echo clearPhone($tReceiverDetails_ORG['vReceiverPhone']); ?>"
                                                           data-tReceiverMessage="<?php echo $tReceiverDetails_ORG['tReceiverMessage']; ?>"
                                                           data-tReceiverEmail="<?php echo clearEmail($tReceiverDetails_ORG['tReceiverEmail']); ?>"
                                                           data-tReceiverName="<?php echo clearName($tReceiverDetails_ORG['tReceiverName']); ?>"
                                                           data-code="<?php echo $data_drv[$i]['vGiftCardCode']; ?>"> Receiver
                                                            Details
                                                        </a>
                                                    <?php } ?></td>
                                                <td><?php echo $data_drv[$i]['eRedeemed']; ?></td>
                                                <td>
                                                    <?php if ('Yes' === $data_drv[$i]['eRedeemed'] || ('Admin' === $data_drv[$i]['eCreatedBy'] && in_array($data_drv[$i]['eUserType'], ['DriverSpecific', 'UserSpecific'], true))) { ?>
                                                        <!-- <a href="javascript:void(0);"
                                                           onclick="viewReceiverDetails(this)" class="btn btn-info"
                                                           data-vReceiverPhoneCode="<?php /* = $tReceiverDetails['vReceiverPhoneCode']; */ ?>"
                                                           data-vReceiverPhone="<?php /* = $tReceiverDetails['vReceiverPhone']; */ ?>"
                                                           data-tReceiverMessage="<?php /* = $tReceiverDetails['tReceiverMessage']; */ ?>"
                                                           data-tReceiverEmail="<?php /* = $tReceiverDetails['tReceiverEmail']; */ ?>"
                                                           data-tReceiverName="<?php /* = $tReceiverDetails['tReceiverName']; */ ?>">
                                                            Redeemed By
                                                        </a>-->
                                                        <?php if ('User' === $tReceiverDetails['usertype']) {
                                                            ?>
                                                            <?php if ($userObj->hasPermission('view-users')) { ?><a href="javascript:void(0);" onClick="show_rider_details('<?php echo $tReceiverDetails['iUserId']; ?>')" style="text-decoration: underline;"><?php } ?><?php echo clearName($tReceiverDetails['tReceiverName']).' ('.$tReceiverDetails['usertype'].')'; ?><?php if ($userObj->hasPermission('view-users')) { ?></a><?php } ?>
                                                             <?php
                                                        } else {
                                                            ?>
                                                            <?php if ($userObj->hasPermission('view-providers')) { ?><a href="javascript:void(0);" onClick="show_driver_details('<?php echo $tReceiverDetails['iDriverId']; ?>')" style="text-decoration: underline;"><?php } ?><?php echo clearName($tReceiverDetails['tReceiverName']).' ('.$tReceiverDetails['usertype'].')'; ?><?php if ($userObj->hasPermission('view-providers')) { ?></a> <?php } ?>
                                                         <?php
                                                        }
                                                        ?>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <?php if ('No' === $data_drv[$i]['eRedeemed'] && 'Anyone' !== $data_drv[$i]['eUserType']) { ?>
                                                        <a href="javascript:void(0);" onclick="sendTheInfo(this)"
                                                           class="btn btn-info"
                                                           data-tReceiverEmail="<?php echo $tReceiverDetails['tReceiverEmail']; ?>"
                                                           data-iGiftCardId="<?php echo $data_drv[$i]['iGiftCardId']; ?>"
                                                           data-toggle="modal" data-target="#uiModal1_13043">
                                                            Send
                                                        </a>
                                                    <?php } else {
                                                        echo '--';
                                                    } ?>
                                                </td>
                                                <td width="10%" align="center">
                                                    <?php
                                                    if ('No' === $data_drv[$i]['eRedeemed']) {
                                                        if ('Active' === $data_drv[$i]['eStatus']) {
                                                            $dis_img = 'img/active-icon.png';
                                                        } elseif ('Inactive' === $data_drv[$i]['eStatus']) {
                                                            $dis_img = 'img/inactive-icon.png';
                                                        } elseif ('Deleted' === $data_drv[$i]['eStatus']) {
                                                            $dis_img = 'img/delete-icon.png';
                                                        }
                                                        ?>
                                                        <img src="<?php echo $dis_img; ?>"
                                                             alt="<?php echo $data_drv[$i]['eStatus']; ?>" data-toggle="tooltip"
                                                             title="<?php echo $data_drv[$i]['eStatus']; ?>">
                                                    <?php } else {
                                                        echo '--';
                                                    } ?>
                                                </td>
                                                <td style="display: none">
                                                    <a class="btn btn-info" target="_blank"
                                                       href="<?php echo $tconfig['tsite_url']; ?>preview_gift_card.php?adminPreview=1&GiftCardImageId=<?php echo $data_drv[$i]['iGiftCardImageId']; ?>&GeneralMemberId=<?php echo $data_drv[$i]['iCreatedById']; ?>&GeneralUserType=<?php echo $data_drv[$i]['eCreatedBy']; ?>&SenderMsg=<?php echo $tReceiverDetails_ORG['tReceiverMessage']; ?>&Amount=<?php echo $data_drv[$i]['fAmount']; ?>">
                                                        Preview
                                                    </a>
                                                </td>
                                                <td width="10%" align="center">
                                                    <?php echo date('M d, Y', strtotime($data_drv[$i]['dAddedDate'])); ?>
                                                </td>
                                                <?php if ($userObj->hasPermission(['edit-giftcard', 'update-status-giftcard', 'delete-giftcard'])) { ?>
                                                    <td align="center" style="text-align:center;" class="action-btn001">
                                                        <?php if ('No' === $data_drv[$i]['eRedeemed']) { ?>
                                                            <div class="share-button openHoverAction-class"
                                                                 style="display: block;">
                                                                <label class="entypo-export">
                                                                    <span><img src="images/settings-icon.png"
                                                                               alt=""></span>
                                                                </label>
                                                                <div class="social show-moreOptions openPops_<?php echo $data_drv[$i]['iGiftCardId']; ?>">
                                                                    <ul>
                                                                        <?php if ('Admin' === $data_drv[$i]['eCreatedBy']) { ?>
                                                                            <li class="entypo-twitter"
                                                                                data-network="twitter">
                                                                                <a href="gift_card_action.php?iGiftCardId=<?php echo $data_drv[$i]['iGiftCardId']; ?>"
                                                                                   data-toggle="tooltip" title="Edit">
                                                                                    <img src="img/edit-icon.png"
                                                                                         alt="Edit">
                                                                                </a>
                                                                            </li>
                                                                        <?php } ?>
                                                                        <?php if ('Yes' !== $data_drv[$i]['eDefault']) { ?><?php if ($userObj->hasPermission('update-status-giftcard')) { ?>
                                                                            <li class="entypo-facebook"
                                                                                data-network="facebook">
                                                                                <a href="javascript:void(0);"
                                                                                   onclick="changeStatus('<?php echo $data_drv[$i]['iGiftCardId']; ?>', 'Inactive')"
                                                                                   data-toggle="tooltip"
                                                                                   title="Activate">
                                                                                    <img src="img/active-icon.png"
                                                                                         alt="<?php echo $data_drv[$i]['eStatus']; ?>">
                                                                                </a>
                                                                            </li>
                                                                            <li class="entypo-gplus"
                                                                                data-network="gplus">
                                                                                <a href="javascript:void(0);"
                                                                                   onclick="changeStatus('<?php echo $data_drv[$i]['iGiftCardId']; ?>', 'Active')"
                                                                                   data-toggle="tooltip"
                                                                                   title="Deactivate">
                                                                                    <img src="img/inactive-icon.png"
                                                                                         alt="<?php echo $data_drv[$i]['eStatus']; ?>">
                                                                                </a>
                                                                            </li>
                                                                        <?php } ?><?php if ($userObj->hasPermission('delete-giftcard')) { ?><?php if ('Admin' === $data_drv[$i]['eCreatedBy']) { ?>
                                                                            <li class="entypo-gplus"
                                                                                data-network="gplus">
                                                                                <a href="javascript:void(0);"
                                                                                   onclick="changeStatusDelete('<?php echo $data_drv[$i]['iGiftCardId']; ?>')"
                                                                                   data-toggle="tooltip" title="Delete">
                                                                                    <img src="img/delete-icon.png"
                                                                                         alt="Delete">
                                                                                </a>
                                                                            </li>
                                                                        <?php } ?><?php } ?><?php } ?>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        <?php } else {
                                                            echo '--';
                                                        } ?>
                                                    </td>
                                                <?php } ?>
                                            </tr>
                                            <?php
                                        }
                                    } else {
                                        ?>
                                        <tr class="gradeA">
                                            <td colspan="7"> No Records Found.</td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                            </form>
                            <?php include 'pagination_n.php'; ?>
                        </div>
                    </div> <!--TABLE-END-->
                </div>
            </div>
            <div class="admin-notes">
                <h4>Notes:</h4>
                <ul>
                    <li> Gift Card module will list all gift card on this page.</li>
                    <li> Administrator can Activate / Deactivate any gift card.</li>
                    <li> Administrator can delete any gift card.</li>
                    <?php if ($CONFIG_OBJ->isOnlyCashPaymentModeAvailable()) { ?>
                        <li>
                            <strong>Gift Card</strong>
                            feature is not available in the applications as only
                            <strong>Cash</strong>
                            payment option is available in the system.
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </div>
    <!--END PAGE CONTENT -->
</div>
<!--END MAIN WRAPPER -->
<div class="row loding-action" id="loaderIcon" style="display:none;">
    <div align="center">
        <img src="default.gif">
    </div>
</div>
<form name="pageForm" id="pageForm" action="action/gift_card.php" method="post">
    <input type="hidden" name="page" id="page" value="<?php echo $page; ?>">
    <input type="hidden" name="tpages" id="tpages" value="<?php echo $tpages; ?>">
    <input type="hidden" name="iGiftCardId" id="iMainId01" value="">
    <input type="hidden" name="status" id="status01" value="">
    <input type="hidden" name="statusVal" id="statusVal" value="">
    <input type="hidden" name="option" value="<?php echo $option; ?>">
    <input type="hidden" name="keyword" value="<?php echo $keyword; ?>">
    <input type="hidden" name="sortby" id="sortby" value="<?php echo $sortby; ?>">
    <input type="hidden" name="order" id="order" value="<?php echo $order; ?>">
    <input type="hidden" name="searchDriver" value="<?php echo $searchDriver; ?>">
    <input type="hidden" name="searchRider" value="<?php echo $searchRider; ?>">
    <input type="hidden" name="method" id="method" value="">
</form>
<div class="modal fade " id="detail_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4>
                    <!--<i aria-hidden="true" class="fa fa-building-o" style="margin:2px 5px 0 2px;"></i>-->
                    <i style="margin:2px 5px 0 2px;">
                        <img src="images/rider-icon.png" alt="">
                    </i>
                    <?php echo $langage_lbl_admin['LBL_RIDER']; ?> Details
                    <button type="button" class="close" data-dismiss="modal">x</button>
                </h4>
            </div>
            <div class="modal-body" style="max-height: 450px;overflow: auto;">
                <div id="imageIcons">
                    <div align="center">
                        <img src="default.gif">
                        <br/>
                        <span>Retrieving details,please Wait...</span>
                    </div>
                </div>
                <div id="rider_detail"></div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="detail_modal1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4>
                    <i style="margin:2px 5px 0 2px;">
                        <img src="images/icon/driver-icon.png" alt="">
                    </i>
                    <?php echo $langage_lbl_admin['LBL_DRIVER_TXT_ADMIN']; ?> Details
                    <button type="button" class="close" data-dismiss="modal">x</button>
                </h4>
            </div>
            <div class="modal-body" style="max-height: 450px;overflow: auto;">
                <div id="imageIcons1">
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
<?php
include_once 'footer.php';
?>
<?php include_once 'searchfunctions.php'; ?>
<script src="../assets/js/modal_alert.js"></script>
<link rel="stylesheet" href="../assets/plugins/datepicker/css/datepicker.css"/>
<script src="../assets/plugins/datepicker/js/bootstrap-datepicker.js"></script>
<script>
    $('.searchRider_div , .searchDriver_div').hide();
    if ("<?php echo $CreatedBy; ?>" == 'User') {
        $('.searchRider_div').show();
    } else if ("<?php echo $CreatedBy; ?>" == 'Driver') {
        $('.searchDriver_div').show();
    }

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

        console.log('hhhhhh');
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

    function viewReceiverDetails(elem) {
        var driverList = '';
        driverList += "<table class='table table-bordered' width='100%' align='center'>";
        driverList += "<tr>";
        driverList += "<td> Name</td><td>" + $(elem).data('treceivername') + "</td>";
        driverList += "</tr>";
        driverList += "<tr>";
        driverList += "<td> Phone No.</td><td>+" + $(elem).data('vreceiverphonecode') + " " + $(elem).data('vreceiverphone') + "</td>";
        driverList += "</tr>";
        driverList += "<tr>";
        driverList += "<td> Email</td><td>" + $(elem).data('treceiveremail') + "</td>";
        driverList += "</tr>";
        driverList += "</table>";
        show_alert("Receiver Details( Gift Card: " + $(elem).data('code') + ")", driverList, "", "", "<?php echo $langage_lbl_admin['LBL_BTN_OK_TXT']; ?>", undefined, true, true, true);
    }

    function sendTheInfo(elem) {
        <?php if (SITE_TYPE === 'Demo') { ?>
        setTimeout(function () {
            show_alert("", "This Feature has been disabled on the Demo Admin Panel. This feature will be enabled on the main script we will provide you.", "", "", "<?php echo addslashes($langage_lbl['LBL_BTN_OK_TXT']); ?>");
        }, 500);

        <?php } else { ?>
        $('#loaderIcon').show();
        var tReceiverEmail = $(elem).data('treceiveremail');
        var ajaxData = {
            'URL': '<?php echo $tconfig['tsite_url_main_admin']; ?>ajax_gift_card.php',
            'AJAX_DATA': {type: 'sendInfo', iGiftCardId: $(elem).data('igiftcardid')},
            'REQUEST_DATA_TYPE': 'json'
        };

        getDataFromAjaxCall(ajaxData, function (response) {
            $('#loaderIcon').hide();
            var dataHtml2 = response.result;

            var mes = '';
            if (typeof dataHtml2.mail != 'undefined') {
                mes += "<?php echo addslashes($langage_lbl['LBL_EMAIL_SENT_TO']); ?>  " + dataHtml2.mailSend + "<br>";
            }
            if (typeof dataHtml2.sms != 'undefined') {
                mes += "<?php echo addslashes($langage_lbl['LBL_SMS_SENT_TO']); ?>  " + dataHtml2.smsSend;
            }

            show_alert("", mes, "", "", "<?php echo addslashes($langage_lbl['LBL_BTN_OK_TXT']); ?>");
        });
        <?php } ?>
    }

    function show_rider_details(userid) {
        $("#detail_modal1").modal('hide');
        $("#rider_detail").html('');
        $("#imageIcons").show();
        $("#detail_modal").modal('show');
        if (userid != "") {
            var ajaxData = {
                'URL': '<?php echo $tconfig['tsite_url_main_admin']; ?>ajax_rider_details.php',
                'AJAX_DATA': "iUserId=" + userid,
                'REQUEST_DATA_TYPE': 'html'
            };
            getDataFromAjaxCall(ajaxData, function (response) {
                if (response.action == "1") {
                    var data = response.result;
                    $("#rider_detail").html(data);
                    $("#imageIcons").hide();
                } else {
                    console.log(response.result);
                    $("#detail_modal").modal('hide');
                }
            });
        }
    }

    function show_driver_details(driverid) {
        $("#detail_modal").modal('hide');
        $("#driver_detail").html('');
        $("#imageIcons1").show();
        $("#detail_modal1").modal('show');

        if (driverid != "") {
            var ajaxData = {
                'URL': '<?php echo $tconfig['tsite_url_main_admin']; ?>ajax_driver_details.php',
                'AJAX_DATA': "iDriverId=" + driverid,
                'REQUEST_DATA_TYPE': 'html'
            };
            getDataFromAjaxCall(ajaxData, function (response) {
                if (response.action == "1") {
                    var data = response.result;
                    $("#driver_detail").html(data);
                    $("#imageIcons1").hide();
                } else {
                    $("#imageIcons1").hide();
                }
            });
        }
    }


    /* ----------------------date filter --------------------*/
    var startDate;
    var endDate;
    $('#dp4').datepicker()
        .on('changeDate', function (ev) {
            startDate = new Date(ev.date);
            if (endDate != null) {
                if (ev.date.valueOf() < endDate.valueOf()) {
                    $('#alert').show().find('strong').text('The start date can not be greater then the end date');
                } else {
                    $('#alert').hide();
                    $('#startDate').text($('#dp4').data('date'));
                }
            }
            $('#dp4').datepicker('hide');
        });
    $('#dp5').datepicker()
        .on('changeDate', function (ev) {
            endDate = new Date(ev.date);
            if (startDate != null) {
                if (ev.date.valueOf() < startDate.valueOf()) {
                    $('#alert').show().find('strong').text('The end date can not be less then the start date');
                } else {
                    $('#alert').hide();
                    $('#endDate').text($('#dp5').data('date'));
                }
            }
            $('#dp5').datepicker('hide');
        });
    $(document).ready(function () {
        if ('<?php echo $startDate; ?>' != '') {
            $("#dp4").val('<?php echo $startDate; ?>');
            $("#dp4").datepicker('update', '<?php echo $startDate; ?>');
        }
        if ('<?php echo $endDate; ?>' != '') {
            $("#dp5").datepicker('update', '<?php echo $endDate; ?>');
            $("#dp5").val('<?php echo $endDate; ?>');
        }
    });

    function todayDate() {
        $("#dp4").val('<?php echo $Today; ?>');
        $("#dp5").val('<?php echo $Today; ?>');
    }

    function reset() {
        location.reload();
    }

    function yesterdayDate() {
        $("#dp4").val('<?php echo $Yesterday; ?>');
        $("#dp4").datepicker('update', '<?php echo $Yesterday; ?>');
        $("#dp5").datepicker('update', '<?php echo $Yesterday; ?>');
        $("#dp4").change();
        $("#dp5").change();
        $("#dp5").val('<?php echo $Yesterday; ?>');
    }

    function currentweekDate(dt, df) {
        $("#dp4").val('<?php echo $monday; ?>');
        $("#dp4").datepicker('update', '<?php echo $monday; ?>');
        $("#dp5").datepicker('update', '<?php echo $sunday; ?>');
        $("#dp5").val('<?php echo $sunday; ?>');
    }

    function previousweekDate(dt, df) {
        $("#dp4").val('<?php echo $Pmonday; ?>');
        $("#dp4").datepicker('update', '<?php echo $Pmonday; ?>');
        $("#dp5").datepicker('update', '<?php echo $Psunday; ?>');
        $("#dp5").val('<?php echo $Psunday; ?>');
    }

    function currentmonthDate(dt, df) {
        $("#dp4").val('<?php echo $currmonthFDate; ?>');
        $("#dp4").datepicker('update', '<?php echo $currmonthFDate; ?>');
        $("#dp5").datepicker('update', '<?php echo $currmonthTDate; ?>');
        $("#dp5").val('<?php echo $currmonthTDate; ?>');
    }

    function previousmonthDate(dt, df) {
        $("#dp4").val('<?php echo $prevmonthFDate; ?>');
        $("#dp4").datepicker('update', '<?php echo $prevmonthFDate; ?>');
        $("#dp5").datepicker('update', '<?php echo $prevmonthTDate; ?>');
        $("#dp5").val('<?php echo $prevmonthTDate; ?>');
    }

    function currentyearDate(dt, df) {
        $("#dp4").val('<?php echo $curryearFDate; ?>');
        $("#dp4").datepicker('update', '<?php echo $curryearFDate; ?>');
        $("#dp5").datepicker('update', '<?php echo $curryearTDate; ?>');
        $("#dp5").val('<?php echo $curryearTDate; ?>');
    }

    function previousyearDate(dt, df) {
        $("#dp4").val('<?php echo $prevyearFDate; ?>');
        $("#dp4").datepicker('update', '<?php echo $prevyearFDate; ?>');
        $("#dp5").datepicker('update', '<?php echo $prevyearTDate; ?>');
        $("#dp5").val('<?php echo $prevyearTDate; ?>');
    }

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

    /* ----------------------date filter --------------------*/


    function chnageUserType(e) {
        $('.searchRider_div , .searchDriver_div').hide();
        if (e.value == 'User') {
            $('.searchRider_div').show();
        } else if (e.value == 'Driver') {
            $('.searchDriver_div').show();
        }
    }
</script>
</body>
<!-- END BODY-->
</html>