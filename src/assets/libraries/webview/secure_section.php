<div class="col-lg-12">
    <div class="secure-section">
        <img src="<?= $tconfig['tsite_url'] ?>assets/img/secure.svg">
        <span>Secure</span>
    </div>
</div>

<script type="text/javascript">
    <?php if($MODULES_OBJ->isEnableAppHomeScreenLayoutV3() && $topBarHeight == 0) { ?>
    $('.custom-mt-header').css('margin-top', '3rem');
    <?php } ?>
</script>