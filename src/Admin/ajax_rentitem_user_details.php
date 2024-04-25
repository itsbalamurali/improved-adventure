<?php
include_once '../common.php';

$iUserId = $_REQUEST['iUserId'] ?? '';

$iRentItemPostId = $_REQUEST['iRentItemPostId'] ?? '';

$eStatus = $_REQUEST['eStatus'] ?? '';

$eType = $_REQUEST['eType'] ?? 'RentItem';

$sql = "select iUserId,eStatus from rentitem_post where iRentItemPostId = '{$iRentItemPostId}' ";

$data_user = $obj->MySQLSelect($sql);

?>


<?php if ('Reject' !== $eStatus && 'Deleted' !== $eStatus) { ?>
 <form name="frmfeatured" id="frmfeatured" action="" method="post">
  	<input type="hidden" name="iUserId" value="<?php echo $data_user[0]['iUserId']; ?>" >
	<input type="hidden" name="eStatus1" value="<?php echo $eStatus; ?>" >
	<input type="hidden" name="iRentItemPostId" value="<?php echo $iRentItemPostId; ?>" >
	<input type="hidden" name="action" value="statusupdate" >
	<input type="hidden" name="eType" value="<?php echo $eType; ?>" >

	<div class="modal-footer">
		<button type="button" class="btn btn-ok" data-dismiss="modal">Not Now</button>
		<button class="save" id="<?php echo $eStatus; ?>">
			<i class="<?php echo ('Pending' === $data_user[0]['eStatus']) ? 'fa fa-check-circle' : 'fa fa-check-circle-o'; ?>"></i>&nbsp;Yes
		</button>
	</div>
</form>
<?php } elseif ('Deleted' === $eStatus) { ?>
<form name="frmfeatured" id="frmfeatured" action="" method="post">
  	<input type="hidden" name="iUserId" value="<?php echo $data_user[0]['iUserId']; ?>" >
	<input type="hidden" name="eStatus1" value="<?php echo $eStatus; ?>" >
	<input type="hidden" name="iRentItemPostId" value="<?php echo $iRentItemPostId; ?>" >
	<input type="hidden" name="action" value="statusupdate" >
	<input type="hidden" name="eDeletedBy" value="Admin" >
	<input type="hidden" name="eType" value="<?php echo $eType; ?>" >
	<label for="vDeletedReason">Delete Reason: </label>
    <br/>
    <textarea name="vDeletedReason" class="form-control1" id="vDeletedReason<?php echo $iRentItemPostId; ?>" rows="4" cols="40" required="required" style="resize: both !important;width: 100%;"></textarea>
	<div class="modal-footer">
		<button type="button" class="btn btn-ok" data-dismiss="modal">Not Now</button>
		<button class="save" id="deleted">
			<i class="<?php echo ('Pending' === $data_user[0]['eStatus']) ? 'fa fa-check-circle' : 'fa fa-check-circle-o'; ?>"></i>&nbsp;Yes
		</button>
	</div>
</form>

<?php } elseif ('Reject' === $eStatus) {  ?>
	<form role="form" name="reject_form" id="reject_form<?php echo $iRentItemPostId; ?>" method="post" action="" class="margin0">
		<input type="hidden" name="iUserId" value="<?php echo $data_user[0]['iUserId']; ?>" >
		<input type="hidden" name="eStatus1" value="<?php echo $eStatus; ?>" >
		<input type="hidden" name="iRentItemPostId" value="<?php echo $iRentItemPostId; ?>" >
		<input type="hidden" name="action" value="statusupdate" >
		<input type="hidden" name="eType" value="<?php echo $eType; ?>" >
		<label for="reject_reason">Reject Reason: </label>
		<br/>
		<textarea name="reject_reason" class="form-control1 reject_reason" id="reject_reason<?php echo $iRentItemPostId; ?>" rows="4" cols="40" required="required" style="resize: both !important;width: 100%;"></textarea>

		<div class="modal-footer">
			<button type="button" class="btn btn-ok" data-dismiss="modal">Not Now</button>
			<button class="save" id="<?php echo $eStatus; ?>">
				<i class="<?php echo ('Pending' === $data_user[0]['eStatus']) ? 'fa fa-check-circle' : 'fa fa-check-circle-o'; ?>"></i>&nbsp;Yes
			</button>
		</div>
	</form>
<?php } else { ?>
	<input type="hidden" name="iUserId" value="<?php echo $data_user[0]['iUserId']; ?>" >
	<input type="hidden" name="eStatus1" value="<?php echo $eStatus; ?>" >
	<input type="hidden" name="iRentItemPostId" value="<?php echo $iRentItemPostId; ?>" >
	<input type="hidden" name="action" value="statusupdate" >
	<input type="hidden" name="eType" value="<?php echo $eType; ?>" >
	<div class="modal-footer">
		<button type="button" class="btn btn-ok" data-dismiss="modal">Not Now</button>
		<button class="save" id="<?php echo $eStatus; ?>">
			<i class="<?php echo ('Pending' === $data_user[0]['eStatus']) ? 'fa fa-check-circle' : 'fa fa-check-circle-o'; ?>"></i>&nbsp;Yes
		</button>
	</div>
<?php }

if ('Deleted' === $eStatus || 'Reject' === $eStatus) { ?>

<script>
	$("input[type=text].form-control,textarea.form-control1").keypress(function(e) {
       if (e.which === 32 && !this.value.length) {
           e.preventDefault();
       }
   });

	$('#deleted').click(function(){
	    if($.trim($('#vDeletedReason<?php echo $iRentItemPostId; ?>').val()) == ''){
	      $('#vDeletedReason<?php echo $iRentItemPostId; ?>').val('');
	    }
	});

	$('#Reject').click(function(){
	    if($.trim($('#reject_reason<?php echo $iRentItemPostId; ?>').val()) == ''){
	      $('#reject_reason<?php echo $iRentItemPostId; ?>').val('');
	    }
	});
</script>
<?php } ?>