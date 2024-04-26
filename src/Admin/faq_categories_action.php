<?
	include_once('../common.php');
	
	
	
	
	require_once(TPATH_CLASS."Imagecrop.class.php");
	
	$default_lang = $LANG_OBJ->FetchSystemDefaultLang();
	$id 		= isset($_REQUEST['id'])?$_REQUEST['id']:''; // iUniqueId
	$success	= isset($_REQUEST['success'])?$_REQUEST['success']:0;
	$action 	= ($id != '')?'Edit':'Add';
	
	//$temp_gallery = $tconfig["tpanel_path"];
	$tbl_name 		= 'faq_categories';
	$script 		= 'faq_categories';
	
	// fetch all lang from language_master table 
	$sql = "SELECT * FROM `language_master` ORDER BY `iDispOrder`";
	$db_master = $obj->MySQLSelect($sql);
	$count_all = count($db_master);
	
	
	$backlink = isset($_POST['backlink']) ? $_POST['backlink'] : '';
    $previousLink = isset($_POST['backlink']) ? $_POST['backlink'] : '';
	
	// set all variables with either post (when submit) either blank (when insert)
	$vImage 		= isset($_POST['vImage'])?$_POST['vImage']:'';
	$eStatus_check 	= isset($_POST['eStatus'])?$_POST['eStatus']:'off';
	
	$eStatus 		= ($eStatus_check == 'on')?'Active':'Inactive';
	$thumb = new thumbnail();
	/* to fetch max iDisplayOrder from table for insert */
	$select_order	= $obj->MySQLSelect("SELECT MAX(iDisplayOrder) AS iDisplayOrder FROM ".$tbl_name." WHERE vCode = '".$default_lang."'");
	$iDisplayOrder	= isset($select_order[0]['iDisplayOrder'])?$select_order[0]['iDisplayOrder']:0;
	$iDisplayOrder	= $iDisplayOrder + 1; // Maximum order number
	
	$iDisplayOrder	= isset($_POST['iDisplayOrder'])?$_POST['iDisplayOrder']:$iDisplayOrder;
	$temp_order 	= isset($_POST['temp_order'])? $_POST['temp_order'] : "";
	
	if($count_all > 0) {
		for($i=0;$i<$count_all;$i++) {
			$vTitle = 'vTitle_'.$db_master[$i]['vCode'];
			$$vTitle  = isset($_POST[$vTitle])?$_POST[$vTitle]:'';
		}
	}
	
	if(isset($_POST['submit'])) { //form submit

		if($action == "Add" && !$userObj->hasPermission('create-faq-categories')){
	        $_SESSION['success'] = 3;
	        $_SESSION['var_msg'] = 'You do not have permission to create Faq Categories.';
	        header("Location:faq_categories.php");
	        exit;
	   	}

	   	if($action == "Edit" && !$userObj->hasPermission('edit-faq-categories')){
	        $_SESSION['success'] = 3;
	        $_SESSION['var_msg'] = 'You do not have permission to update Faq Categories.';
	        header("Location:faq_categories.php");
	        exit;
	   	}
		

		if(SITE_TYPE=='Demo')
		{
		header("Location:faq_categories_action.php?id=".$id.'&success=2');
		exit;
		}
          
      
    
    
    	if($temp_order > $iDisplayOrder) {
			for($i = $temp_order; $i >= $iDisplayOrder; $i--) { 
				$obj->sql_query("UPDATE ".$tbl_name." SET iDisplayOrder = ".($i+1)." WHERE iDisplayOrder = ".$i);
			}
			} else if($temp_order < $iDisplayOrder) {
			for($i = $temp_order; $i <= $iDisplayOrder; $i++) {
				$obj->sql_query("UPDATE ".$tbl_name." SET iDisplayOrder = ".($i-1)." WHERE iDisplayOrder = ".$i);
			}
		}
		
		$select_order		= $obj->MySQLSelect("SELECT MAX(iUniqueId) AS iUniqueId FROM ".$tbl_name." WHERE vCode = '".$default_lang."'");
		$iUniqueId			= isset($select_order[0]['iUniqueId'])?$select_order[0]['iUniqueId']:0;
		$iUniqueId			= $iUniqueId + 1; // Maximum order number
		
		if($count_all > 0) {
			for($i=0;$i<$count_all;$i++) {
				
				$q = "INSERT INTO ";
				$where = '';
				
				if($id != '' ){ 
					$q = "UPDATE ";
					$where = " WHERE `iUniqueId` = '".$id."' AND vCode = '".$db_master[$i]['vCode']."'";
					$iUniqueId = $id;
				}
			    //$image_object = $_FILES['vImage']['tmp_name'];  
				//$image_name   = $_FILES['vImage']['name'];
				
				/* if($image_name != ""){
					$filecheck = basename($_FILES['vImage']['name']);                            
					$fileextarr = explode(".",$filecheck);
					$ext=strtolower($fileextarr[count($fileextarr)-1]);
					$flag_error = 0;
					if($ext != "jpg" && $ext != "gif" && $ext != "png" && $ext != "jpeg" && $ext != "bmp"){
						$flag_error = 1;
						$var_msg = "Not valid image extension of .jpg, .jpeg, .gif, .png";
					}
					if($_FILES['vImage']['size'] > 1048576){
						$flag_error = 1;
						$var_msg = "Image Size is too Large";
					}
					if($flag_error == 1){
						getPostForm($_POST,$var_msg,$tconfig['tsite_url']."faq_categories_action&success=0");
						exit;
						}else{
						$Photo_Gallery_folder = $tconfig["tsite_upload_images_panel"].'/';
						if(!is_dir($Photo_Gallery_folder)){
							mkdir($Photo_Gallery_folder, 0777);
						}  
						$img = $UPLOAD_OBJ->GeneralFileUpload($Photo_Gallery_folder,$image_object,$image_name, '','jpg,png,gif,jpeg');
						$vImage = $img[0];
					}
				} */
				
				$image_object = $_FILES['vImage']['tmp_name'];  
				$category_image=$_FILES['vImage']['name'];
				
				$vImage_name1 = str_replace(" ", "_", trim($category_image));
				$img_arr = explode(".", $vImage_name1);
				// $filename = $img_arr[0];
				$filename = mt_rand(11111, 99999);
				$fileextension = $img_arr[count($img_arr) - 1];
				
                $vImage = $category_image; 
				$vImgName;
				
				if($i == 0){
					$vImgName .= $filename.'.'.$fileextension;
					
				}else{
					
					$vImgName = $vImgName;
				}
				
				
				$folder= $tconfig['tsite_upload_images_panel']; 
				// $suc=move_uploaded_file($_FILES['vImage']['tmp_name'], $folder.$vImgName);
               				
				
				
				$vTitle = 'vTitle_'.$db_master[$i]['vCode'];
				
				$query = $q ." `".$tbl_name."` SET 	
				`vTitle` = '".$$vTitle."',
				`vImage` = '".$vImgName."',
				`eStatus` = '".$eStatus."',
				`iUniqueId` = '".$iUniqueId."',
				`iDisplayOrder` = '".$iDisplayOrder."',
				`vCode` = '".$db_master[$i]['vCode']."'"
				.$where;
				$obj->sql_query($query); 
				
			}
		}
		//header("Location:faq_categories_action.php?id=".$iUniqueId.'&success=1');
		if ($action == "Add") {
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_INSERT_MSG'];
        } else {
            $_SESSION['success'] = '1';
            $_SESSION['var_msg'] = $langage_lbl_admin['LBL_Record_Updated_successfully'];
        }

        if(!empty($OPTIMIZE_DATA_OBJ)) {
	        $OPTIMIZE_DATA_OBJ->ExecuteMethod('SetFaqs');
	    }
		header("Location:faq_categories.php?id=".$iUniqueId.'&success=1');
		exit;
	}
	
	// for Edit
	if($action == 'Edit') {
		$sql = "SELECT * FROM ".$tbl_name." WHERE iUniqueId = '".$id."'";
		$db_data = $obj->MySQLSelect($sql);	
		$iUniqueId = $id;
		if(count($db_data) > 0) {
			foreach($db_data as $key => $value) { 
			
				$vTitle 			= 'vTitle_'.$value['vCode'];
				$$vTitle 			= $value['vTitle'];
				
				$eStatus 			= $value['eStatus'];
				$vImage 			= $value['vImage'];
				$iDisplayOrder 		= $value['iDisplayOrder'];
				$arrLang[$vTitle] = $$vTitle;
			}
		}
	}

	$EN_available = $LANG_OBJ->checkLanguageExist();
	$db_master = $LANG_OBJ->getLangDataDefaultFirst($db_master);
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9"> <![endif]-->
<!--[if !IE]><!--> <html lang="en"> <!--<![endif]-->
	
	<!-- BEGIN HEAD-->
	<head>
		<meta charset="UTF-8" />
		<title>Admin | FAQ Category <?=$action;?></title>
		<meta content="width=device-width, initial-scale=1.0" name="viewport" />
		<link href="../assets/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet" />
		
		<? include_once('global_files.php');?>
		<!-- On OFF switch -->
		<link href="../assets/css/jquery-ui.css" rel="stylesheet" />
		<link rel="stylesheet" href="../assets/plugins/switch/static/stylesheets/bootstrap-switch.css" />	
	</head>
	<!-- END  HEAD-->
	<!-- BEGIN BODY-->
	<body class="padTop53 " >
		
		<!-- MAIN WRAPPER -->
		<div id="wrap">
			<? include_once('header.php'); ?>
			<? include_once('left_menu.php'); ?>       
			<!--PAGE CONTENT -->
			<div id="content">
				<div class="inner">
					<div class="row">
						<div class="col-lg-12">
							<h2><?=$action;?> FAQ Category</h2>
							<a href="faq_categories.php" class="back_link">
								<input type="button" value="Back to Listing" class="add-btn">
							</a>
						</div>
					</div>
					<hr />	
					<div class="body-div">
						<div class="form-group">
							<? if($success == 1) { ?>
								<div class="alert alert-success alert-dismissable">
									<button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
									<?php echo $langage_lbl_admin['LBL_Record_Updated_successfully']; ?>
								</div><br/>
							<? } ?>
							 <? if ($success == 2) {?>
                 <div class="alert alert-danger alert-dismissable">
                      <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                      <?php echo $langage_lbl_admin['LBL_EDIT_DELETE_RECORD']; ?>
                 </div><br/>
               <?} ?>
							<form method="post" name="_faq_cat_form" id="_faq_cat_form"  action="" enctype="multipart/form-data">
								<input type="hidden" name="id" value="<?=$id;?>"/>
								<input type="hidden" name="previousLink" id="previousLink" value="<?php echo $previousLink; ?>"/>
								<input type="hidden" name="backlink" id="backlink" value="faq_categories.php"/>
								<?php /*<div class="row">
									<div class="col-lg-12">
										<label>Image</label>
									</div>
									<div class="col-md-6 col-sm-6">
									<? //if($vImage != '') { ?>
											<!--<img src="<?=$tconfig['tsite_upload_images'].$vImage;?>" style="width:200px;height:100px;">-->
										<? //} ?>
									
									
										<?															
										if($vImage != '' && file_exists($tconfig['tsite_upload_images_panel'].'/'.$vImage)) {
										?>
										<img src="<?=$tconfig['tsite_upload_images'].$vImage;?>" style="width:200px;height:100px;">
																<? } else echo $vImage; ?>
										<input type="file" name="vImage" id="vImage" value="<?=$vImage;?>"/>
									</div>
								</div> */ ?>
								<div class="row">
									<div class="col-lg-12">
										<label>Status</label>
									</div>
									<div class="col-md-6 col-sm-6">
										<div class="make-switch" data-on="success" data-off="warning">
											<input type="checkbox" name="eStatus" <?=($id != '' && $eStatus == 'Inactive')?'':'checked';?>/>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-lg-12">
										<label>Order</label>
									</div>
									<div class="col-md-6 col-sm-6">
										<?
											$temp = 1;
											$query1 = $obj->MySQLSelect("SELECT max(iDisplayOrder) as maxnumber FROM ".$tbl_name." WHERE vCode = '".$default_lang."' ORDER BY iDisplayOrder");
											$maxnum	= isset($query1[0]['maxnumber']) ? $query1[0]['maxnumber'] : 0;
											$dataArray = array();
											for ($i=1; $i <= $maxnum ; $i++) { 
												$dataArray[] = $i;
												$temp = $iDisplayOrder;
											}	
											
/*											while($res = mysqli_fetch_array($query1)) 
											{
												$dataArray[] = $res['iDisplayOrder'];
												$temp = $iDisplayOrder;
											}*/
										?>
										<input type="hidden" name="temp_order" id="temp_order" value="<?=$temp?>">
										<select name="iDisplayOrder" class="form-control">
											<? foreach($dataArray as $arr):?>
											<option <?= $arr == $temp ? ' selected="selected"' : '' ?> value="<?=$arr;?>" >
												-- <?= $arr ?> --
											</option>
											<? endforeach; ?>
											<?if($action=="Add") {?>
												<option value="<?=$temp;?>" >
													-- <?= $temp ?> --
												</option>
											<? }?>
										</select>
									</div>
								</div>

								<?php if (count($db_master) > 1) { ?>
								<div class="row">
                                    <div class="col-lg-12">
                                        <label>FAQ Category Label <span class="red"> *</span></label>
                                    </div>
                                    <div class="col-md-6 col-sm-6">
                                        <input type="text" class="form-control <?= ($id == "") ?  'readonly-custom' : '' ?>" id="vTitle_Default" name="vTitle_Default" value="<?= $arrLang['vTitle_'.$default_lang]; ?>" data-originalvalue="<?= $arrLang['vTitle_'.$default_lang]; ?>" readonly="readonly" <?php if($id == "") { ?> onclick="editFaqCategory('Add')" <?php } ?>>
                                    </div>
                                    <?php if($id != "") { ?>
                                    <div class="col-lg-2">
                                        <button type="button" class="btn btn-info" data-toggle="tooltip" data-original-title="Edit" onclick="editFaqCategory('Edit')"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>
                                    </div>
                                    <?php } ?>
                                </div>

                                <div  class="modal fade" id="faq_cat_Modal" tabindex="-1" role="dialog" aria-hidden="true" data-backdrop="static" data-keyboard="false">
                                    <div class="modal-dialog modal-lg" >
                                        <div class="modal-content nimot-class">
                                            <div class="modal-header">
                                                <h4>
                                                    <span id="modal_action"></span> FAQ Category Label
                                                    <button type="button" class="close" data-dismiss="modal" onclick="resetToOriginalValue(this, 'vTitle_')">x</button>
                                                </h4>
                                            </div>
                                            
                                            <div class="modal-body">
                                                <?php
                                                    
                                                    for ($i = 0; $i < $count_all; $i++) 
                                                    {
                                                        $vCode = $db_master[$i]['vCode'];
                                                        $vTitle = $db_master[$i]['vTitle'];
                                                        $eDefault = $db_master[$i]['eDefault'];
                                                        $vValue = 'vTitle_' . $vCode;
                                                        
                                                        $required = ($eDefault == 'Yes') ? 'required' : '';
                                                        $required_msg = ($eDefault == 'Yes') ? '<span class="red"> *</span>' : '';
                                                ?>
                                                		<?php
                                                        $page_title_class = 'col-lg-12';
                                                        if (count($db_master) > 1) {
                                                            if($EN_available) {
                                                                if($vCode == "EN") { 
                                                                    $page_title_class = 'col-md-9 col-sm-9';
                                                                }
                                                            } else { 
                                                                if($vCode == $default_lang) {
                                                                    $page_title_class = 'col-md-9 col-sm-9';
                                                                }
                                                            }
                                                        }
                                                        ?>
                                                        <div class="row">
                                                            <div class="col-lg-12">
                                                                <label>FAQ Category Label (<?= $vTitle; ?>) <?php echo $required_msg; ?></label>
                                                                
                                                            </div>
                                                            <div class="<?= $page_title_class ?>">
                                                                <input type="text" class="form-control" name="<?= $vValue; ?>" id="<?= $vValue; ?>" value="<?= $$vValue; ?>" data-originalvalue="<?= $$vValue; ?>" placeholder="<?= $vTitle; ?> Value">
                                                                <div class="text-danger" id="<?= $vValue.'_error'; ?>" style="display: none;"><?= $langage_lbl_admin['LBL_REQUIRED'] ?></div>
                                                            </div>
                                                            <?php
                                                            if (count($db_master) > 1) {
                                                                if($EN_available) {
                                                                    if($vCode == "EN") { ?>
                                                                    <div class="col-md-3 col-sm-3">
                                                                        <button type ="button" name="allLanguage" id="allLanguage" class="btn btn-primary" onClick="getAllLanguageCode('vTitle_', 'EN');" >Convert To All Language</button>
                                                                    </div>
                                                                <?php }
                                                                } else { 
                                                                    if($vCode == $default_lang) { ?>
                                                                    <div class="col-md-3 col-sm-3">
                                                                        <button type ="button" name="allLanguage" id="allLanguage" class="btn btn-primary" onClick="getAllLanguageCode('vTitle_', '<?= $default_lang ?>');" >Convert To All Language</button>
                                                                    </div>
                                                                <?php }
                                                                }
                                                            }
                                                            ?>
                                                        </div> 
                                                    <?php 
                                                    }
                                                ?>
                                            </div>
                                            <div class="modal-footer" style="margin-top: 0">
                                            	<h5 class="text-left" style="margin-bottom: 15px; margin-top: 0;"><strong><?= $langage_lbl['LBL_NOTE']; ?>: </strong><?= $langage_lbl['LBL_SAVE_INFO']; ?></h5>
                                                <div class="nimot-class-but" style="margin-bottom: 0">
                                                    <button type="button" class="save" style="margin-left: 0 !important" onclick="saveFaqCategory()"><?= $langage_lbl['LBL_Save']; ?></button>
                                                    <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal" onclick="resetToOriginalValue(this, 'vTitle_')"><?= $langage_lbl['LBL_CANCEL_TXT']; ?></button>
                                                </div>
                                            </div>
                                            
                                            <div style="clear:both;"></div>
                                        </div>
                                    </div>
                                </div>
                                <?php } else { ?>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <label>FAQ Category Label <span class="red"> *</span></label>
                                    </div>
                                    <div class="col-md-6 col-sm-6">
                                        <input type="text" class="form-control" id="vTitle_<?= $default_lang ?>" name="vTitle_<?= $default_lang ?>" value="<?= $arrLang['vTitle_'.$default_lang]; ?>" required>
                                    </div>
                                </div>
                                <?php } ?>
								<? /*
									if($count_all > 0) {
										for($i=0;$i<$count_all;$i++) {
											$vCode = $db_master[$i]['vCode'];
											$vTitleLn = $db_master[$i]['vTitle'];
											$eDefault = $db_master[$i]['eDefault'];
											
											$vTitle = 'vTitle_'.$vCode;
											
											$required = ($eDefault == 'Yes')?'required':'';
											$required_msg = ($eDefault == 'Yes')?'<span class="red"> *</span>':'';
										?>
										<div class="row">
											<div class="col-lg-12">
												<label><?=$vTitleLn;?> Label<?=$required_msg;?></label>
											</div>
											<div class="col-md-6 col-sm-6">
												<input type="text" class="form-control" name="<?=$vTitle;?>"  id="<?=$vTitle;?>" value="<?=$$vTitle;?>" placeholder="FAQ Category" <?=$required;?>>
												<div class="text-danger" id="<?= $vTitle.'_error'; ?>" style="display: none;"><?= $langage_lbl_admin['LBL_REQUIRED'] ?></div>
											</div>
											<? if($vCode == $default_lang  && count($db_master) > 1){ ?>
                                                    <div class="col-md-6 col-sm-6">
                                                        <button type ="button" name="allLanguage" id="allLanguage" class="btn btn-primary" onClick="getAllLanguageCode('vTitle_', '<?= $default_lang ?>');"> Convert To All Language</button>
                                                    </div>
                                                <?php } ?>
										</div>
										<? } 
									} */?>
									<div class="row faq-but">
										<div class="col-lg-12">			
											<?php if(($action == 'Edit' && $userObj->hasPermission('edit-faq-categories')) || ($action == 'Add' &&  $userObj->hasPermission('create-faq-categories'))){ ?>
												<input type="submit" class="btn btn-default" name="submit" id="submit" value="<?=$action;?> FAQ Category">
												<input type="reset" value="Reset" class="btn btn-default">
											<?php } ?>
											<!-- <a href="javascript:void(0);" onclick="reset_form('_faq_cat_form');" class="btn btn-default">Reset</a> -->
                                            <a href="faq_categories.php" class="btn btn-default back_link">Cancel</a>
										</div>
									</div>
							</form>
						</div>
					</div>
                    <div class="clear"></div>
				</div>
			</div>
			<!--END PAGE CONTENT -->
		</div>
		<!--END MAIN WRAPPER -->
		
		<div class="row loding-action" id="loaderIcon" style="display:none;">
            <div align="center">                                                                       
                <img src="default.gif">                                                              
                <span>Language Translation is in Process. Please Wait...</span>                       
            </div>                                                                                 
        </div>
		
		<? include_once('footer.php');?>
		<script src="../assets/plugins/switch/static/js/bootstrap-switch.min.js"></script>
		<script>
$(document).ready(function() {
	var referrer;
	if($("#previousLink").val() == "" ){ 
		referrer =  document.referrer;
        // alert(referrer);		
	}else { 
		referrer = $("#previousLink").val();
	}
	if(referrer == "") {
		referrer = "faq_categories.php";
	}else { 
		$("#backlink").val(referrer);		
	}
	$(".back_link").attr('href',referrer); 	
});

function editFaqCategory(action)
{
    $('#modal_action').html(action);
    $('#faq_cat_Modal').modal('show');
}

function saveFaqCategory()
{
    if($('#vTitle_<?= $default_lang ?>').val() == "") {
        $('#vTitle_<?= $default_lang ?>_error').show();
        $('#vTitle_<?= $default_lang ?>').focus();
        clearInterval(langVar);
        langVar = setTimeout(function() {
            $('#vTitle_<?= $default_lang ?>_error').hide();
        }, 5000);
        return false;
    }

    $('#vTitle_Default').val($('#vTitle_<?= $default_lang ?>').val());
    $('#vTitle_Default').closest('.row').removeClass('has-error');
    $('#vTitle_Default-error').remove();
    $('#faq_cat_Modal').modal('hide');
}

</script>

	</body>
	<!-- END BODY-->    
</html>