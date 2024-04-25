<h1 style="height: 0;margin: 0;padding: 0;pointer-events: none;visibility: hidden; font-size: 0;">z7clYC</h1>
<!-- Custome JS -->
<script src="<?= $siteUrl ?><?php echo $templatePath; ?>assets/js/less.min.js"></script>
<script>less = { env: 'development'};</script>
<!-- Added for LESS CSS BY GP 26 SEP 2019 -->
<script src="<?= $siteUrl ?><?php echo $templatePath; ?>assets/js/script.js"></script>
<script src="<?= $siteUrl ?>assets/plugins/bootstrap/js/bootstrap.min.js"></script>
<script src="<?= $siteUrl ?>assets/js/bootbox.min.js"></script>
<script src="<?= $siteUrl ?>assets/js/magic.js" type="text/javascript"></script>
<script src="<?= $siteUrl ?><?= $templatePath; ?>assets/js/jquery.mousewheel.js"></script> 
<script src="<?= $siteUrl ?><?= $templatePath; ?>assets/js/jquery.mCustomScrollbar.js"></script> 
<link rel="stylesheet" href="<?= $siteUrl ?>assets/css/apptype/<?= $template;?>/jquery.mCustomScrollbar.css" type="text/css">
<script type="text/javascript">
    $(document).ready(function(){
		if($('body ul.user-menu').length > 0){
			$('body ul.user-menu').mCustomScrollbar();
		}
        $(".custom-select-new").each(function(){
            $(this).wrap("<em class='select-wrapper'></em>");
            $(this).after("<em class='holder'></em>");
        });
        $(".custom-select-new").change(function(){
            var selectedOption = $(this).find(":selected").text();
            $(this).next(".holder").text(selectedOption);
        }).trigger('change');
	
		$(".label-i").on('click',function(e) {
			var lang_id = $(this).data('id');
			var from = $(this).data('value');
			// $.ajax({
			// 	type: "POST",
			// 	url: 'language_popup.php',
			// 	data: 'lang_id=' + lang_id + '&from='+from,
			// 	success: function (dataHtml)
			// 	{
			// 		$("#lang_popup").html(dataHtml);
			// 		$("#myModalHorizontal").modal('show');
			// 	},
			// 	error: function(dataHtml){
					
			// 	}
			// });

			var ajaxData = {
			    'URL': '<?= $tconfig['tsite_url'] ?>language_popup.php',
			    'AJAX_DATA': 'lang_id=' + lang_id + '&from='+from,
			};
			getDataFromAjaxCall(ajaxData, function(response) {
			    if(response.action == "1") {
			        var dataHtml = response.result;
			        $("#lang_popup").html(dataHtml);
					$("#myModalHorizontal").modal('show');
			    }
			    else {
			        console.log(response.result);
			    }
			});
			e.stopPropagation();
			return false;
		});
    });
	
	function updateLanguage(){
		var formdata = $("#_languages_form").serialize();
		// $.ajax({
		// 	type: "POST",
		// 	url: 'language_save.php',
		// 	data: formdata,
		// 	success: function (dataHtml)
		// 	{
		// 		location.reload();
		// 	},
		// 	error: function(dataHtml){
				
		// 	}
		// });

		var ajaxData = {
		    'URL': '<?= $tconfig['tsite_url'] ?>language_save.php',
		    'AJAX_DATA': formdata,
		};
		getDataFromAjaxCall(ajaxData, function(response) {
		    if(response.action == "1") {
		        var dataHtml = response.result;
		        location.reload();
		    }
		    else {
		        console.log(response.result);
		    }
		});
	}
	
</script>
<!-- Modal -->
<!--<div class="modal fade" id="myModalHorizontal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="myModalLabel">Change Language Label</h4>
            </div>
            
            <div class="modal-body" id="lang_popup">
                
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="updateLanguage();">Save changes</button>
            </div>
        </div>
    </div>
</div>-->
<!-- Modal -->
