<?php
include_once('common.php');

require_once(TPATH_CLASS . "Imagecrop.class.php");
$thumb = new thumbnail();
$rowid = isset($_REQUEST['rowid']) ? $_REQUEST['rowid'] : '';
$id = explode('-',$rowid);

$sql = "select  dm.`doc_masterid`, dm.`doc_usertype`, dm.`doc_name`, dm.doc_name_".$_SESSION['sess_lang']." as document , dm.`ex_status`, dl.`doc_id`, dl.`doc_masterid`, dl.`doc_usertype`, dl.`doc_userid`, dl.`ex_date`, dl.`doc_file`,rd.`iDriverId` from document_master as dm left join document_list  as dl on  dl.doc_masterid= dm.doc_masterid left join  register_driver as rd on  dl.doc_userid= rd.iDriverId where dl.doc_usertype='driver' AND  iDriverId='".$id[1]."' and dm.doc_masterid='".$id[0]."'" ;	
$db_user = $obj->MySQLSelect($sql);

$sql1="select doc_name,ex_status,doc_name_".$_SESSION['sess_lang']." as document from document_master where doc_masterid='".$id[0]."'";
$db_user1 = $obj->MySQLSelect($sql1);
    
if($db_user[0]['document']== ''){ $vName = $db_user1[0]['document'];}else{ $vName=$db_user[0]['document'];}
?>
<div class="upload-content ">
    <div class="model-header"><h4><?php echo $vName; ?></h4><i class="icon-close" data-dismiss="modal"></i></div>
    <form class="form-horizontal frm6" id="frm6" method="post" enctype="multipart/form-data" action="cx-driver_document_action.php?id=<?php echo $id[1] ; ?>&master=<?php echo $id[0] ; ?> " name="frm6">
        <input type="hidden" name="action" value ="document"/>
		<input type="hidden" name="user" value ="<?php echo $user;?>"/>
		<input type="hidden" name="doc_type" value="<?php echo $id[0]; ?>" />
        <input type="hidden" name="doc_path" value =" <?php  echo $tconfig["tsite_upload_driver_doc_path"]; ?>"/>
        
        <div class="model-body">
                
            <div class="fileupload fileupload-new" data-provides="fileupload">
                <div class="fileupload-preview thumbnail">
                    <?php if ($db_user[0]['doc_file'] == '') { 
                        echo $langage_lbl['LBL_NO']." ".$vName. " ".$langage_lbl['LBL_PHOTO'];
                    } else { ?>
                        <?php
                        $file_ext = $UPLOAD_OBJ->GetFileExtension($db_user[0]['doc_file']);
                        if ($file_ext == 'is_image') { ?>
                            <img src = "<?= $tconfig["tsite_upload_driver_doc"]. '/' . $id[1] . '/' . $db_user[0]['doc_file'] ?>" alt ="<?php echo $db_user[0]['doc_name']; ?> not found"/>
                        <?php } else { ?>
                            <a href="<?= $tconfig["tsite_upload_driver_doc"]. '/' . $id[1] . '/' . $db_user[0]['doc_file'] ?>" target="_blank"><?php echo $db_user[0]['doc_name']; ?></a>
                        <?php } ?>
                    <?php } ?>
                </div>
                <div class="newrow">
                    <span class="btn btn-file btn-success gen-btn"><span class="fileupload-new"><?=$langage_lbl['LBL_UPLOAD']; ?> <?php echo $vName ?> <?=$langage_lbl['LBL_PHOTO']; ?></span>
                        <span class="fileupload-exists"><?=$langage_lbl['LBL_CHANGE']; ?></span>
                        <input type="file" name="driver_doc" /></span>
                    <a href="#" class="btn btn-danger fileupload-exists gen-btn" data-dismiss="fileupload"><?=$langage_lbl['LBL_REMOVE_TEXT']; ?></a>
                    <input type="hidden" name="driver_doc_hidden"  id="driver_doc" value="<?php echo ($db_user[0]['doc_file'] !="") ? $db_user[0]['doc_file'] : '';?>" />
                </div>
                <div class="upload-error"><span class="file_error"></span></div>
                
            </div>
        <?php if($db_user[0]['ex_status']=='yes' || $db_user1[0]['ex_status']=='yes') { ?>
            <div class="filters-column exp-date newrow">
                <label><?=$langage_lbl['LBL_EXP_DATE']; ?></label>
                <input class="form-control" type="text" id="dtpckrdLicenceExp" name="dLicenceExp" value="<?php if($db_user[0]['ex_date'] != ''){ echo $db_user[0]['ex_date'];}?>" readonly="" required/>
                <i class="icon-cal" id="from-date"></i>
                <div class="exp-error"><span class="exp_error"></span></div>
            </div>
        <?php }  ?>

        </div>
        <div class="model-footer">
            <div class="button-block">
                <input type="submit" class="save save11 gen-btn" name="save" value="<?= $langage_lbl['LBL_Save']; ?>">
                <input type="button" class="cancel11 gen-btn" data-dismiss="modal" name="cancel" value="<?= $langage_lbl['LBL_CANCEL_TXT']; ?>">
            </div>
        </div>
    </form>
</div>
<script>
$(document).ready(function() {
    $('#frm6').validate({
        ignore: 'input[type=hidden]',
        /*errorClass: 'help-block Iserror',
        errorElement: 'span',
        errorPlacement: function(error, element) {
            if (element.attr("name") == "driver_doc")
            {
                error.insertAfter("span.file_error");
            }  else if(element.attr("name") == "dLicenceExp"){
                error.insertAfter("span.exp_error");
            } else {
                error.insertAfter(element);
            }
        },*/
        errorClass: 'help-block error',
        errorElement: 'span',
        errorPlacement: function (error, e) {
            /*if (element.attr("name") == "vCurrencyDriver")
                error.appendTo('#vCurrencyDriverCheck');
            else if (element.attr("name") == "vCountry")
                error.appendTo('#vCountryCheck');
            else
                error.insertAfter(element);*/
                e.parents('.newrow').append(error);
        },
        highlight: function (e) {
                $(e).closest('.newrow').removeClass('has-success has-error').addClass('has-error');
                $(e).closest('.newrow input').addClass('has-shadow-error');
                $(e).closest('.help-block').remove();
        },
        success: function (e) {
            e.prev('input').removeClass('has-shadow-error');
            e.closest('.newrow').removeClass('has-success has-error');
            e.closest('.help-block').remove();
            e.closest('.help-inline').remove();
        },
        rules: {
            driver_doc: {
                required: {
                    depends: function(element) {
                        if ($("#driver_doc").val() == "") { 
                            return true;
                        } else { 
                            return false;
                        } 
                    }
                },
                // accept: "image/*,.doc,.docx,.pdf"
                extension:'jpe?g,png,gif,bmp,doc,docx,pdf',
            }
        },
        messages: {
            driver_doc: {
                required: '<?= addslashes($langage_lbl['LBL_UPLOAD_IMG']); ?>',
                accept: '<?= addslashes($langage_lbl['LBL_UPLOAD_IMG_ERROR']);?>',
                extension: '<?= addslashes($langage_lbl['LBL_UPLOAD_IMG_ERROR']);?>',
            }
        }
    });
});

$(function () {

    // var nowTemp = new Date();
    // var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);

    $('#dtpckrdLicenceExp').datepicker({
       dateFormat: 'yy-mm-dd' ,
       minDate: moment().add(1, 'd').toDate(),
        // onRender: function (date) {
            // return date.valueOf() < now.valueOf() ? 'disabled' : '';
       // }
    });
    //formInit();

});
</script>