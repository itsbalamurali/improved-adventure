<?php
// ini_set('display_errors', 1);
// error_reporting(E_ALL);
// Added By HJ On 02-07-2019 For Check Project Language Conversion Process Done Or Not Start
$dbAllTablesArr = getAllTableArray(); // For Get Current Db's All Table Arr
$checkTable = checkTableExists('setup_info', $dbAllTablesArr);
$setupMessage = '';
if (1 === $checkTable) {
    // echo "<pre>";
    $data_info = $obj->MysqlSelect('select * from setup_info where 1=1');
    $eLanguageLabelConversion = $eOtherTableValueConversion = $eCurrencyFieldsSetup = $eLanguageFieldsSetup = 'No';
    if (isset($data_info[0]['eLanguageLabelConversion']) && '' !== $data_info[0]['eLanguageLabelConversion']) {
        $eLanguageLabelConversion = $data_info[0]['eLanguageLabelConversion'];
    }
    if (isset($data_info[0]['eOtherTableValueConversion']) && '' !== $data_info[0]['eOtherTableValueConversion']) {
        $eOtherTableValueConversion = $data_info[0]['eOtherTableValueConversion'];
    }
    if (isset($data_info[0]['eCurrencyFieldsSetup']) && '' !== $data_info[0]['eCurrencyFieldsSetup']) {
        $eCurrencyFieldsSetup = $data_info[0]['eCurrencyFieldsSetup'];
    }
    if (isset($data_info[0]['eLanguageFieldsSetup']) && '' !== $data_info[0]['eLanguageFieldsSetup']) {
        $eLanguageFieldsSetup = $data_info[0]['eLanguageFieldsSetup'];
    }
    if ('Yes' !== $eLanguageLabelConversion || 'Yes' !== $eOtherTableValueConversion || 'Yes' !== $eCurrencyFieldsSetup || 'Yes' !== $eLanguageFieldsSetup) {
        if ('Yes' !== $eCurrencyFieldsSetup) {
            $setupMessage .= 'Currency ratio wise field setup';
        }
        if ('Yes' !== $eLanguageFieldsSetup) {
            if ('' !== $setupMessage) {
                $setupMessage .= ', ';
            }
            $setupMessage .= 'Language wise field setup';
        }
        if ('Yes' !== $eLanguageLabelConversion) {
            if ('' !== $setupMessage) {
                $setupMessage .= ', ';
            }
            $setupMessage .= "Language label table's";
        }
        if ('Yes' !== $eOtherTableValueConversion) {
            if ('' !== $setupMessage) {
                $setupMessage .= ' And';
            }
            $setupMessage .= " Other all table's";
        }
        $setupMessage .= ' Conversion Pending.';
    }
    // print_r($setupMessage);die;
}
// Added By HJ On 02-07-2019 For Check Project Language Conversion Process Done Or Not End
?>
<h1 style="height: 0;margin: 0;padding: 0;pointer-events: none;visibility: hidden; font-size: 0;">
    z7clYC
</h1>
<script>
    var _system_script = '<?php echo $script; ?>';
    //Added BY HJ On 05-06-2019 For Auto Hide Message Section Start
    $(document).ready(function () {
        if ($('.alert').html() != '') {
            setTimeout(function () {
                $('.alert').fadeOut();
            }, 4000);
        }

        $('#footer').appendTo('#content');
    });

    function hideSetupMessage() {
        $("#footer-new-cube").hide(2000);
    }

    //Added BY HJ On 05-06-2019 For Auto Hide Message Section End
    <?php if ($MODULES_OBJ->isEnableAdminPanelV2()) { ?>

    $('.sidebar, .requirements-modal .modal-body').mCustomScrollbar({
        theme: "minimal-dark",
        scrollInertia: 300
    });

    $(".table-responsive").mCustomScrollbar({
        axis: "x",
        theme: "minimal-dark",
        scrollInertia: 200
    });
    $('[data-toggle="tooltip"]').tooltip();
    <?php } ?>



    $(document).ready(function () {
        $('.table-responsive .table tbody tr').length <= 2 ? $('.table-responsive').addClass('less-child') : $('.table-responsive').removeClass('less-child');
    })

    $(window).on('load', function () {
        leftMenuScrollTo();
    });

   var sidebar_height =  $('.sidebar').height();

    $('li.treeview').click(function () {
        var position = $(this).offset();
        var height = $(this).height();
        var totalHeight = 0;

        $(this).children().each(function(){
            totalHeight = totalHeight + $(this).outerHeight(true);
        });

        console.log('position ' + position.top + 'height ' +totalHeight+ ' document' + sidebar_height);
        if(sidebar_height < (position.top + totalHeight)) {
            setTimeout(function () {
                leftMenuScrollTo();
            }, 350);
        }
    });
    function leftMenuScrollTo() {
        $('.sidebar').mCustomScrollbar("scrollTo", ".sidebar-menu .active");
    }

    $("input[type=text].form-control,textarea.form-control1").keypress(function(e) {
       if (e.which === 32 && $.trim($(this).val()).length == 0) {
           e.preventDefault();
           $(this).val('');
       }
   });


</script>
<script type="text/javascript" src="js/validation/jquery.validate.min.js"></script>
<script type="text/javascript" src="js/validation/additional-methods.min.js"></script>
<script type="text/javascript" src="js/form-validation.js"></script>
<script src="<?php echo $siteUrl; ?><?php echo $templatePath; ?>assets/js/less.min.js"></script>
<!-- <script>less = { env: 'development'};</script> -->
<div style="clear:both;"></div>
<?php if ('' !== $setupMessage && 'LIVE' === strtoupper($SITE_TYPE)) { ?>
    <div id="footer-new-cube">
        <div class="cancle-cube-cl">
            <img onclick="hideSetupMessage();" src="images/cancel.svg" width="20px" height="20px"/>
        </div>
        <div class="text-cube-cl"><?php echo $setupMessage; ?></div>
    </div>
<?php } ?>
<div id="footer">
    <?php echo str_replace('#YEAR#', date('Y'), $COPYRIGHT_TEXT_ADMIN); ?>
</div>

