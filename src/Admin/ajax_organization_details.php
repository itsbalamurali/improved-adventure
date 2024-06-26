<?php
include_once '../common.php';

$iOrganizationId = $_REQUEST['iOrganizationId'] ?? '';

$sql = "select cmp.*,cn.vCountry as country,ct.vCity as city,st.vState as state from organization cmp left join country cn on cn.vCountryCode = cmp.vCountry left join city ct on ct.iCityId = cmp.vCity left join state st on st.iStateId = cmp.vState where iOrganizationId = '{$iOrganizationId}'";

$data_company = $obj->MySQLSelect($sql);

$reg_date1 = $data_company[0]['tRegistrationDate'];

if ('0000-00-00 00:00:00' !== $reg_date1) {
    $reg_date = date('l, M d \\<\\s\\u\\p\\>S\\<\\/\\s\\u\\p\\>\\ Y', strtotime($reg_date1));
} else {
    $reg_date = '';
}

if ('' !== $data_company[0]['vImage']) {
    $image_path = $tconfig['tsite_upload_images_organization'].'/'.$iOrganizationId.'/2_'.$data_company[0]['vImage'];
} else {
    $image_path = '../assets/img/profile-user-img.png';
}
?>
	<style>
	.text_design{
		font-size: 12px;
		font-weight: bold;
		font-family: verdana;
	}
	.border_table{
		border:1px solid #dddddd;
	}
	.no-cursor{
	    cursor: text;
	}
	</style>
	<!--<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h4><i aria-hidden="true" class="fa fa-building-o" style="margin:2px 5px 0 2px;"></i>Company Details
					<button type="button" class="close" data-dismiss="modal">x</button>
					</h4>
				</div>
				<div class="modal-body" style="max-height: 450px;overflow: auto;"> -->


					<table border="1" class="table table-bordered" width="100%" align="center" cellspacing="5" cellpadding="10px">
						<tbody>
						<tr>
							<td rowspan="3" height="150px" width="150px" ><img width="150px" src="<?php echo $image_path; ?>"></td>
							<td>
								<table border="0" width="100%" height="150px" cellspacing="5" cellpadding="5px">
									<tr>
										<td width="140px" class="text_design">Organization Name</td>
										<td><?php echo clearCmpName($data_company[0]['vCompany']); ?></td>
									</tr>
									<tr>
										<td class="text_design">Email</td>
										<td><?php echo clearEmail($data_company[0]['vEmail']); ?></td>
									</tr>

									<tr>
										<td class="text_design">Phone Number</td>
										<td>
											<?php
                                                $phone = '+';
if ('' !== $data_company[0]['vCode']) {
    $phone .= $data_company[0]['vCode'].'-';
}
$phone .= $data_company[0]['vPhone'];
echo clearPhone($phone);
?>
										</td>
									</tr>
									<?php if ('' !== $reg_date) {?>
									<tr>
										<td class="text_design">Registration Date</td>
										<!-- <td>Tuesday, Aug  22<sup>nd</sup> 2017</td> -->
										<td><?php echo $reg_date; ?></td>
									</tr>
									<?php } ?>
									<tr>
										<td class="text_design">Status</td>
										<td>
											<?php
    $class = '';
if ('Active' === $data_company[0]['eStatus']) {
    $class = 'btn-success';
} elseif ('Inactive' === $data_company[0]['eStatus']) {
    $class = 'btn';
} else {
    $class = 'btn-danger';
}
?>
											<button class="btn <?php echo $class; ?> no-cursor"><?php echo $data_company[0]['eStatus']; ?></button>
										</td>
									</tr>

								</table>
							</td>
						</tr><tr></tr><tr></tr><tr></tr>
						<tr>
							<td class="text_design">Organization Address</td>
							<td>
								<?php
                                    $address1 = $data_company[0]['vCaddress'];
if ('' !== $data_company[0]['vCadress2']) {
    $conc = ('' !== $address1) ? ', ' : '';
    $address1 .= $conc.$data_company[0]['vCadress2'];
}
if ('' !== $data_company[0]['city']) {
    $conc = ('' !== $address1) ? ', ' : '';
    $address1 .= $conc.$data_company[0]['city'];
}
if ('' !== $data_company[0]['vZip']) {
    $conc = ('' !== $address1) ? ', ' : '';
    $address1 .= $conc.$data_company[0]['vZip'];
}
if ('' !== $data_company[0]['state']) {
    $conc = ('' !== $address1) ? ', ' : '';
    $address1 .= $conc.$data_company[0]['state'];
}

if ('' !== $data_company[0]['country']) {
    $conc = ('' !== $address1) ? ', ' : '';
    $address1 .= $conc.$data_company[0]['country'];
}
echo $address1;
?>
							</td>
						</tr>
						<?php if ('' !== $data_company[0]['vVat']) {?>
						<tr>
							<td class="text_design">Vat Number</td>
							<td>
								<?php echo $data_company[0]['vVat']; ?>
							</td>
						</tr>
						<?php } ?>
						<!--
						<tr>
							<td>Total Vehicles</td>
							<td>25</td>
						</tr>
						<tr>
							<td>Driver Details</td>
							<td>
								<table  border="0" width="100%" cellspacing="2" cellpadding="10px">
									<tr>
										<td width="140px">Total Drivers</td>
										<td>25</td>
									</tr>
									<tr>
										<td>Active Drivers</td>
										<td>15</td>
									</tr>
									<tr>
										<td>Inactive Drivers</td>
										<td>10</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td>Trip Details</td>
							<td>
								<table  border="0" width="100%"  cellspacing="5" cellpadding="10px">
									<tr>
										<td width="140px">Total Trips</td>
										<td>125</td>
									</tr>
									<tr>
										<td>Completed Trips</td>
										<td>100</td>
									</tr>
									<tr>
										<td>Cancelled Trips</td>
										<td>12</td>
									</tr>
									<tr>
										<td>Rejected Trips</td>
										<td>13</td>
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
                        <?php }
                        } else { ?>
                        <a href="organization_action.php?id=<?php echo $iOrganizationId; ?>" class="btn btn-primary btn-ok" target="blank">Edit Organization</a>
                    <?php } ?>
					<button type="button" class="btn btn-danger btn-ok" data-dismiss="modal">Close</button>
				</div>


	<!--</div>
	 </div> -->