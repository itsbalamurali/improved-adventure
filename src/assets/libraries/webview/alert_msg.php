<?php if(isset($_SESSION['success_msg']) && $_SESSION['success_msg'] != "") { ?>
<script type="text/javascript">
    <?php /*swal({
        text: '<?= $_SESSION['success_msg'] ?>',
        closeOnClickOutside: false,
        allowOutsideClick: false
    });*/ ?>
    showSnackbar('<?= $_SESSION['success_msg'] ?>');
</script>
<?php $_SESSION['success_msg'] = "";
} ?>

<?php if(isset($_SESSION['error_msg']) && $_SESSION['error_msg'] != "") { ?>
<script type="text/javascript">
    <?php /*swal({
        text: '<?= $_SESSION['error_msg'] ?>',
        closeOnClickOutside: false,
        allowOutsideClick: false
    });*/ ?>
    showSnackbar('<?= $_SESSION['error_msg'] ?>');
</script>
<?php $_SESSION['error_msg'] = "";
} ?>