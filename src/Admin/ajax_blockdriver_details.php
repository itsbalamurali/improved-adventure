<?php

include_once '../common.php';

$iDriverId = $_REQUEST['iDriverId'] ?? '';

$sql = "select iDriverId,eIsBlocked from register_driver rd  where iDriverId = '{$iDriverId}'";

$data_user = $obj->MySQLSelect($sql);

?>  <form name="frmfeatured" id="frmfeatured" action="" method="post">

	  <input type="hidden" name="iDriverId" value="<?php echo $data_user[0]['iDriverId']; ?>" >

		<input type="hidden" name="eIsBlocked1" value="<?php echo ('Yes' === $data_user[0]['eIsBlocked']) ? 'No' : 'Yes'; ?>" >

		<input type="hidden" name="action" value="Blocked" >



			<div class="modal-footer">

			<button type="button" class="btn btn-ok" data-dismiss="modal">Not Now</button>

			<button class="btn btn-danger">

			<i class="<?php echo ('Yes' === $data_user[0]['eIsBlocked']) ? 'fa fa-check-circle' : 'fa fa-check-circle-o'; ?>"></i>&nbsp;Yes

			</button>

			</div>

	</form>