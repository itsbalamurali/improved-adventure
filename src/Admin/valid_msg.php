<?php
// echo "<pre>";print_r($_SESSION);die;
if (isset($_SESSION['success']) && 1 === $_SESSION['success']) { ?>
<div class="alert alert-success alert-dismissable marginbottom-10 msg-test-001">
    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
    <?php echo $_SESSION['var_msg'];
    unset($_SESSION['var_msg'], $_SESSION['success']); ?>
</div>
<?php } elseif (isset($_SESSION['success']) && 2 === $_SESSION['success']) { ?>
<div class="alert alert-danger alert-dismissable marginbottom-10 msg-test-001">
    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
    <?php if ('' !== $_SESSION['var_msg']) { ?>
    	<?php echo $_SESSION['var_msg'];
        unset($_SESSION['var_msg']); ?>
	<?php } else { ?>
    	"Edit / Delete Record Feature" has been disabled on the Demo Admin Panel. This feature will be enabled on the main script we will provide you.
    <?php } ?>
    <?php unset($_SESSION['success']); ?>
</div>
<?php } elseif (isset($_SESSION['success']) && 3 === $_SESSION['success']) { ?>
<div class="alert alert-danger alert-dismissable marginbottom-10 msg-test-001">
    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
    <?php echo $_SESSION['var_msg'];
    unset($_SESSION['var_msg'], $_SESSION['success']); ?>
</div>
<?php } ?>
<script>
$(".alert").show().delay(7000).fadeOut();
</script>