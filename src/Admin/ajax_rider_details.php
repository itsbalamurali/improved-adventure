<?php
include_once '../common.php';
$iUserId = $_REQUEST['iUserId'] ?? '';
$trackingCompany = $_REQUEST['trackingCompany'] ?? '';
// clearName(
$sql = "select ru.iUserId,ru.eRefType,ru.iRefUserId,concat(ru.vName,' ',ru.vLastName) as Name,ru.vEmail,ru.vPhoneCode,ru.vPhone,ru.vImgName,ru.eStatus,ru.eSignUpType,ru.vAvgRating,ru.vZip,ru.tRegistrationDate,ru.vRefCode,ru.eEmailVerified,ru.ePhoneVerified,ru.eGender,cn.vCountry as country,st.vState as state from register_user ru left join country cn on cn.vCountryCode = ru.vCountry left join state st on st.iStateId = ru.vState where iUserId = '{$iUserId}'";
$data_user = $obj->MySQLSelect($sql);
if ('0' !== $data_user[0]['iRefUserId'] && '' !== $data_user[0]['eRefType']) {
    $ref_id = $data_user[0]['iRefUserId'];
    $tbl = 'register_user';
    $field = "concat(vName,' ',vLastName) as RName,vEmail as REmail,vRefCode as Code";
    $cnd = 'iUserId';
    if ('Driver' === $data_user[0]['eRefType']) {
        $tbl = 'register_driver';
        $field = "concat(vName,' ',vLastName) as RName,vEmail as REmail,vRefCode as Code";
        $cnd = 'iDriverId';
    }
    $sql = "select {$field} from {$tbl} where {$cnd} = '{$ref_id}'";
    $data_referral = $obj->MySQLSelect($sql);
}
$reg_date1 = $data_user[0]['tRegistrationDate'];
if ('0000-00-00 00:00:00' !== $reg_date1) {
    // $reg_date = date("l, M d \<\s\u\p\>S\<\/\s\u\p\>\ Y",strtotime($reg_date1));
    $reg_date = DateTime($reg_date1);
} else {
    $reg_date = '';
}
if ('' !== $data_user[0]['vImgName'] && file_exists($tconfig['tsite_upload_images_passenger_path'].'/'.$iUserId.'/2_'.$data_user[0]['vImgName'])) {
    $image_path = $tconfig['tsite_upload_images_passenger'].'/'.$iUserId.'/2_'.$data_user[0]['vImgName'];
} else {
    $image_path = '../assets/img/profile-user-img.png';
}
$rating_width = ($data_user[0]['vAvgRating'] * 100) / 5;
if ($data_user[0]['vAvgRating'] > 0) {
    $Rating = '<span title="'.$data_user[0]['vAvgRating'].'" style="display: block; width: 65px; height: 13px; background: url('.$tconfig['tsite_upload_images'].'star-rating-sprite.png) 0 0;">
	<span style="margin: 0;float:left;display: block; width: '.$rating_width.'%; height: 13px; background: url('.$tconfig['tsite_upload_images'].'star-rating-sprite.png) 0 -13px;"></span>
	</span>';
} else {
    // $Rating = "No ratings received";
    $Rating = '<span title="'.$data_user[0]['vAvgRating'].'" style="display: block; width: 65px; height: 13px; background: url('.$tconfig['tsite_upload_images'].'star-rating-sprite.png) 0 0;">
	<span style="margin: 0;float:left;display: block; width: '.$rating_width.'%; height: 13px; background: url('.$tconfig['tsite_upload_images'].'star-rating-sprite.png) 0 -13px;"></span>
	</span>';
}
?>
<style>
    .text_design {
        font-size: 12px;
        font-weight: bold;
        font-family: verdana;
    }

    .border_table {
        border: 1px solid #dddddd;
    }

    .no-cursor {
        cursor: text;
    }
</style>

<table border="1" class="table table-bordered" width="100%" align="center" cellspacing="5" cellpadding="10px">
    <tbody>
    <tr>
        <td rowspan="3" height="150px" width="150px"><img width="150px" src="<?php echo $image_path; ?>"></td>
        <td>
            <table border="0" width="100%" height="150px" cellspacing="5" cellpadding="5px">
                <tr>
                    <td width="140px" class="text_design">Name</td>
                    <td><?php echo clearName($data_user[0]['Name']); ?></td>
                </tr>
                <tr>
                    <td class="text_design">Email</td>
                    <td><?php echo clearEmail($data_user[0]['vEmail']); ?></td>
                </tr>
                <?php if ('' !== $data_user[0]['vPhone']) { ?>
                    <tr>
                        <td class="text_design">Phone Number</td>
                        <td>
                            <?php
                            $phone = '+';
                    if ('' !== $data_user[0]['vPhoneCode']) {
                        $phone .= $data_user[0]['vPhoneCode'].'-';
                    }
                    $phone .= $data_user[0]['vPhone'];
                    echo clearPhone($phone);
                    ?>
                        </td>
                    </tr>
                <?php } ?>
                <tr>
                    <td class="text_design">Rating</td>
                    <td><?php echo $Rating; ?></td>
                </tr>
                <tr>
                    <td class="text_design">Status</td>
                    <td>
                        <?php
                        $class = '';
if ('Active' === $data_user[0]['eStatus']) {
    $class = 'btn-success';
} elseif ('Inactive' === $data_user[0]['eStatus']) {
    $class = 'btn';
} else {
    $class = 'btn-danger';
}
?>
                        <button class="btn <?php echo $class; ?> no-cursor"><?php echo ucfirst($data_user[0]['eStatus']); ?></button>
                    </td>
                </tr>

            </table>
        </td>
    </tr>
    <tr></tr>
    <tr></tr>
    <tr></tr>
    <?php if ('' !== $data_user[0]['country']) { ?>
        <tr>
            <td class="text_design">Country</td>
            <td>
                <?php echo $data_user[0]['country']; ?>
            </td>
        </tr>
    <?php } ?>
    <?php if ('' !== $reg_date) { ?>
        <tr>
            <td width="150px" class="text_design">Registration Date</td>
            <td><?php echo $reg_date; ?></td>
        </tr>
    <?php } ?>

    <!--	<tr>
			<td class="text_design">Registration Details</td>
			<td>
				<table  border="0" width="100%" cellspacing="2" cellpadding="8">
					<?php if ('' !== $reg_date) { ?>
					<tr>
						<td width="150px" class="text_design">Date</td>
						<td><?php echo $reg_date; ?></td>
					</tr>
					<?php } ?>
					<tr>
						<td class="text_design">Type</td>
						<td><?php echo $data_user[0]['eSignUpType']; ?></td>
					</tr>
					<?php if ('' !== $data_user[0]['eGender']) { ?>
					<tr>
						<td class="text_design">Gender</td>
						<td><?php echo $data_user[0]['eGender']; ?></td>
					</tr>
					<?php } ?>
				</table>
			</td>
		</tr>

	<tr>
			<td class="text_design">Referral Details</td>
			<td>
				<table  border="0" width="100%" cellspacing="2" cellpadding="8">
					<tr>
						<td width="180px" class="text_design">Referral Code</td>
						<td><?php echo $data_user[0]['vRefCode']; ?></td>
					</tr>
					<tr>
						<td class="text_design">Total Drivers Referred</td>
						<td>10</td>
					</tr>
					<tr>
						<td class="text_design">Total Riders Referred</td>
						<td>5</td>
					</tr>
				</table>
			</td>
		</tr>
		<?php if (!empty($data_referral)) { ?>
		<tr>
			<td class="text_design">Referee Details</td>
			<td>
				<table  border="0" width="100%" cellspacing="2" cellpadding="8">
					<tr>
						<td width="160px" class="text_design">Referee Code</td>
						<td><?php echo $data_referral[0]['Code']; ?></td>
					</tr>
					<tr>
						<td class="text_design">Referee Name</td>
						<td><?php echo $data_referral[0]['RName']; ?></td>
					</tr>
					<tr>
						<td class="text_design">Referee Email</td>
						<td><?php echo $data_referral[0]['REmail']; ?></td>
					</tr>
					<tr>
						<td class="text_design">Referee Type</td>
						<td><?php echo $data_user[0]['eRefType']; ?></td>
					</tr>
				</table>
			</td>
		</tr>
		<?php
		}
if ('Yes' === $RIDER_PHONE_VERIFICATION || 'Yes' === $RIDER_EMAIL_VERIFICATION) {
    ?>
			<tr>
				<td class="text_design">Verifications</td>
				<td>
					<table  border="0" width="100%" cellspacing="2" cellpadding="8">
						<?php if ('Yes' === $RIDER_EMAIL_VERIFICATION) { ?>
						<tr>
							<td width="160px" class="text_design">Email Verification</td>
							<td>
								<?php echo ('Yes' === $data_user[0]['eEmailVerified']) ? 'Verified' : 'Not Verified'; ?>
							</td>
						</tr>
						<?php
						}
    if ('Yes' === $RIDER_PHONE_VERIFICATION) {
        ?>
						<tr>
							<td class="text_design">Phone Verification</td>
							<td>
								<?php echo ('Yes' === $data_user[0]['ePhoneVerified']) ? 'Verified' : 'Not Verified'; ?>
							</td>
						</tr>
						<?php } ?>
					</table>
				</td>
			</tr>
		<?php } ?>
		<tr>
			<td class="text_design">Trip Details</td>
			<td>
				<table  border="0" width="100%"  cellspacing="5" cellpadding="10">
					<tr>
						<td width="140px" class="text_design">Total Trips</td>
						<td>125</td>
					</tr>
					<tr>
						<td class="text_design">Completed Trips</td>
						<td>100</td>
					</tr>
					<tr>
						<td class="text_design">Cancelled Trips</td>
						<td>12</td>
					</tr>
				</table>
			</td>
		</tr> -->

    </tbody>
</table>
</div>
<div class="modal-footer">
    <?php if (!empty($_REQUEST['editTrip'])) {
        if ('Yes' === $_REQUEST['editTrip']) { ?>
            <a href="<?php echo $LOCATION_FILE_ARRAY['TRIP']; ?>?action=search&searchRider=<?php echo $iUserId; ?>" class="btn btn-primary btn-ok" target="blank">View <?php echo $langage_lbl_admin['LBL_TRIP_DETAILS_TXT']; ?></a>
        <?php }
        } elseif (!empty($_REQUEST['editOrder'])) {
            if ('Yes' === $_REQUEST['editOrder']) { ?>
            <a href="allorders.php?action=search&type=allorders&searchRider=<?php echo $iUserId; ?>"
               class="btn btn-primary btn-ok" target="blank">View Order Details</a>
        <?php }
            } elseif ('hotel' !== $_SESSION['SessionUserType']) {
                ?>
        <a href="<?php echo $LOCATION_FILE_ARRAY['RIDER_ACTION']; ?>?id=<?php echo $iUserId; ?>" class="btn btn-primary btn-ok"
           target="blank">Edit <?php echo $langage_lbl_admin['LBL_RIDER']; ?></a>
    <?php } ?>
    <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal">Close</button>
</div>
