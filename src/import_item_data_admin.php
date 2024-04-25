<?php
/*
 * File Type : PHP
 * File Created On  : 19-06-2020
 * File Created By : HJ
 * Purpose : For Upload Item CSV file as Bulk Upload
 */
include_once('../common.php');

if (!$userObj->hasPermission('view-item-categories') && !$userObj->hasPermission('view-item')) {
    $userObj->redirect();
}

$script = "ImportItem";
$step = 1;
$uploadedFile = "";
$serviceCatIds = getCurrentActiveServiceCategoriesIds();
$getServiceData = $obj->MySQLSelect("SELECT iServiceId,vServiceName_".$default_lang." AS serviceName FROM service_categories WHERE iServiceId IN ($serviceCatIds);");
//echo "<pre>";print_r($getServiceData);die;
$getStoreData = $obj->MySQLSelect("SELECT iCompanyId,iServiceId,vCompany FROM company WHERE iServiceId > 0 AND eStatus='Active'");
$storeArr = array();
for($h=0;$h<count($getStoreData);$h++){
    $storeArr[$getStoreData[$h]['iServiceId']][]= $getStoreData[$h];
}
$errorMsg = "";
if (isset($_POST['comparedb'])) {
    //echo "<pre>";print_r($_FILES);die;
    if (isset($_FILES["uploadFile"])) {
        $uploadFile = uploadImage($_FILES["uploadFile"]);
        //echo "<pre>";print_r($uploadFile);die;
        if (isset($uploadFile['fileName']) && $uploadFile['fileName'] != "") {
            $uploadedFile = $uploadFile['fileName'];
            $fileextension = end(explode('.', $uploadedFile));
            if(strtoupper($fileextension) == "CSV"){
                $step = 2;
            }else{
                header("Location:import_item_data.php?error=Please must be select CSV file");
                die;
            }
            
        }
        //echo "<pre>";print_r($tableDataArr1);die;
    } else {
        header("Location:import_item_data.php?error=Please select CSV file");
        die;
    }
}
//echo "<pre>";print_r($storeArr);die;
if (isset($_REQUEST['export']) && strtoupper($_REQUEST['export']) == "CSV") {
    $langData = $obj->MySQLSelect("SELECT * FROM language_master WHERE eStatus='Active'");
    $date = new DateTime();
    $timestamp_filename = $date->getTimestamp();
    $filename = "item_".$timestamp_filename . ".csv";
    $fp = fopen('php://output', 'w');
    $sampleItem = array();
    $sampleItem1 = array("SR"=>"1","ITEM_NAME"=>"Veg. Burger","ITEM_DESC"=>"Made with Black Olives, Onions, Grilled Mushrooms & Cheeze","ITEM_CATEGORY"=>"Fast Food","IMAGE_URL"=>"https://cubejekdev.bbcsproducts.net/webimages/upload/MenuItem/1562920475_43406.jpg","ITEM_PRICE"=>"5","FOOD_TYPE"=>"Veg","OFFER_PER"=>"2","IS_AVAILABLE"=>"Yes","IS_RECOMMENDED"=>"Yes","IS_BEST_SELLER"=>"Yes","IS_ACTIVE"=>"Yes");
    $sampleItem[] = $sampleItem1;
    $sampleItem2 = array("SR"=>"2","ITEM_NAME"=>"Hamburger","ITEM_DESC"=>"Made with Black Olives, Onions, Grilled Mushrooms & Cheeze","ITEM_CATEGORY"=>"Fast Food","IMAGE_URL"=>"https://cubejekdev.bbcsproducts.net/webimages/upload/MenuItem/1562920475_43406.jpg","ITEM_PRICE"=>"8","FOOD_TYPE"=>"Veg","OFFER_PER"=>"3","IS_AVAILABLE"=>"No","IS_RECOMMENDED"=>"Yes","IS_BEST_SELLER"=>"No","IS_ACTIVE"=>"Yes");
    $sampleItem[] = $sampleItem2;
    $sampleItem3 = array("SR"=>"3","ITEM_NAME"=>"Veg. Pizza","ITEM_DESC"=>"Made with Black Olives, Onions, Grilled Mushrooms & Cheeze","ITEM_CATEGORY"=>"Fast Food","IMAGE_URL"=>"https://cubejekdev.bbcsproducts.net/webimages/upload/MenuItem/1536212826_57769.jpg","ITEM_PRICE"=>"21","FOOD_TYPE"=>"Veg","OFFER_PER"=>"2.5","IS_AVAILABLE"=>"No","IS_RECOMMENDED"=>"Yes","IS_BEST_SELLER"=>"No","IS_ACTIVE"=>"Yes");
    $sampleItem[] = $sampleItem3;
    $sampleItem4 = array("SR"=>"4","ITEM_NAME"=>"Sicilian Pizza","ITEM_DESC"=>"Made with Black Olives, Onions, Grilled Mushrooms & Cheeze","ITEM_CATEGORY"=>"Fast Food","IMAGE_URL"=>"https://cubejekdev.bbcsproducts.net/webimages/upload/MenuItem/1536212826_57769.jpg","ITEM_PRICE"=>"26","FOOD_TYPE"=>"Veg","OFFER_PER"=>"4","IS_AVAILABLE"=>"Yes","IS_RECOMMENDED"=>"No","IS_BEST_SELLER"=>"No","IS_ACTIVE"=>"Yes");
    $sampleItem[] = $sampleItem4;
    $sampleItem5 = array("SR"=>"5","ITEM_NAME"=>"Greek Pizza","ITEM_DESC"=>"Made with Black Olives, Onions, Grilled Mushrooms & Cheeze","ITEM_CATEGORY"=>"Fast Food","IMAGE_URL"=>"https://cubejekdev.bbcsproducts.net/webimages/upload/MenuItem/1536212826_57769.jpg","ITEM_PRICE"=>"40","FOOD_TYPE"=>"Veg","OFFER_PER"=>"5","IS_AVAILABLE"=>"Yes","IS_RECOMMENDED"=>"No","IS_BEST_SELLER"=>"Yes","IS_ACTIVE"=>"No");
    $sampleItem[] = $sampleItem5;
    $sampleItem6 = array("SR"=>"6","ITEM_NAME"=>"Non Veg Supreme","ITEM_DESC"=>"Made with Black Olives, Onions, Grilled Mushrooms & Cheeze","ITEM_CATEGORY"=>"Fast Food","IMAGE_URL"=>"https://cubejekdev.bbcsproducts.net/webimages/upload/MenuItem/1536212826_57769.jpg","ITEM_PRICE"=>"60","FOOD_TYPE"=>"Non Veg","OFFER_PER"=>"3","IS_AVAILABLE"=>"Yes","IS_RECOMMENDED"=>"Yes","IS_BEST_SELLER"=>"Yes","IS_ACTIVE"=>"Yes");
    $sampleItem[] = $sampleItem6;
    for($m=0;$m<count($sampleItem);$m++){
        $itemName = $sampleItem[$m]['ITEM_NAME'];
        $itemDesc = $sampleItem[$m]['ITEM_DESC'];
        $itemCategory = $sampleItem[$m]['ITEM_CATEGORY'];
        for($l=0;$l<count($langData);$l++){
            $sampleItem[$m]['ITEM_NAME_'.$langData[$l]['vCode']] = $itemName;
            $sampleItem[$m]['ITEM_DESC_'.$langData[$l]['vCode']] = $itemDesc;
            $sampleItem[$m]['ITEM_CATEGORY_'.$langData[$l]['vCode']] = $itemCategory;
        }
    }
    $header = array("SR","ITEM_NAME");
    for($l=0;$l<count($langData);$l++){
        $header[] = "ITEM_NAME_".$langData[$l]['vCode'];
    }
    $header[] = "ITEM_DESC";
    for($l=0;$l<count($langData);$l++){
        $header[] = "ITEM_DESC_".$langData[$l]['vCode'];
    }
    $header[] = "ITEM_CATEGORY";
    for($l=0;$l<count($langData);$l++){
        $header[] = "ITEM_CATEGORY_".$langData[$l]['vCode'];
    }
    
    $otherHeader = array("IMAGE_URL","ITEM_PRICE","FOOD_TYPE","OFFER_PER","IS_AVAILABLE","IS_RECOMMENDED","IS_BEST_SELLER","IS_ACTIVE");
    
    $finalHeader = array_merge($header, $otherHeader);
    //echo "<pre>";print_r($finalHeader);die;
    header('Content-type: application/csv');
    header('Content-Disposition: attachment; filename='.$filename);
    fputcsv($fp, $finalHeader);
    
    $itemDataArr = array();
    for($b=0;$b<count($sampleItem);$b++){
        $newItemArr = array();
        foreach($finalHeader as $key=>$val){
            $newItemArr[] = $sampleItem[$b][$val];
        }
        fputcsv($fp, $newItemArr);
    }
    //header("Location:import_item_data.php");
    exit;
}
$errorMsg = "";
if(isset($_REQUEST['error']) && trim($_REQUEST['error']) != ""){
    $errorMsg = trim($_REQUEST['error']);
}
function uploadImage($attachment, $time = "") {
    if ($time == '') {
        $time = date("Ymd_H:i:s");
    }
    $attachmentSize = $attachment['size'];
    $attachmentName = $attachment['name'];
    $imageType = $attachment['type'];
    $imageTempName = $attachment['tmp_name'];
    $filename = stripslashes($attachmentName);
    $uploadPath = "attachment/";
    $attachment_name = $time . "_" . $filename; // NAME NAME OF THE FILE FOR OUR SYSTEM
    $newname = $uploadPath . $attachment_name; // FULL PATH OF FILE DESTINATION
    $uploadeFile = move_uploaded_file($imageTempName, $newname);
    if ($uploadeFile) { // UPLOAD FILE TO DESTIGNATION FOLDER 
        $result = array("status" => "Success", "fileName" => $newname, "imageType" => $imageType); // IF SUCCESS THEN RETURN TYPE AND NAME
    } else { // UPLOAD ERRPR
        $result = array("status" => "Upload error");
    }
    return $result; // RETURN VALUE TO THE CALL FUNCTION 
}

//echo $step."<br>";
$siteUrl = $tconfig['tsite_url'];
$reload = $_SERVER['PHP_SELF'] . "?tpages=" . $tpages . $var_filter;
?>
<!DOCTYPE html>
<html lang="en">
    <!-- BEGIN HEAD-->
    <head>
        <meta charset="UTF-8" />
        <title><?= $SITE_NAME ?> | Import Items</title>
        <meta content="width=device-width, initial-scale=1.0" name="viewport" />
        <link href="https://fonts.googleapis.com/css?family=Material+Icons|Roboto:300,400,500" rel="stylesheet">
        <link type="text/css" rel="stylesheet" href="<?= $siteUrl; ?>assets/css/stepper/materialize.min.css" media="screen,projection" />
        <link type="text/css" rel="stylesheet" href="<?= $siteUrl; ?>assets/css/stepper/style.css" media="screen,projection" />
        <link type="text/css" rel="stylesheet" href="<?= $siteUrl; ?>assets/css/stepper/prism.css" media="screen,projection" />
        <link type="text/css" rel="stylesheet" href="<?= $siteUrl; ?>assets/css/stepper/mstepper.min.css" media="screen,projection" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <?php include_once('global_files.php'); ?>
    </head>
    <!-- END  HEAD-->

    <!-- BEGIN BODY-->
    <body class="padTop53 " >
        <!-- Main LOading -->
        <!-- MAIN WRAPPER -->
        <div id="wrap">
            <?php include_once('header.php'); ?>
            <?php include_once('left_menu.php'); ?>

            <!--PAGE CONTENT -->
            <div id="content">
                <div class="inner">
                    <div id="add-hide-show-div">
                        <div class="row">
                            <div class="col-lg-12">
                                <h2>Import Items</h2>
                            </div>
                        </div>
                        <hr />
                    </div>
                    <?php include('valid_msg.php'); ?>
                    <div style="display: none;" id="alertfail" class="alert alert-danger alert-dismissable marginbottom-10 msg-test-001">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                        <p id="messagetxtfail"></p>
                    </div>
                    <div style="display: none;" id="alertscs" class="alert alert-success alert-dismissable marginbottom-10 msg-test-001">
                        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">x</button>
                        <p id="messagetxtscs"></p>
                    </div>
                    <div class="table-list">
                        <div class="row">
                            <div class="col-lg-12">
                                <div style="clear:both;"></div>
                                <div class="table-responsive" id="demos_horizontal">
                                    <div class="col s12">
                                        <div class="card">
                                            <div class="card-content">
                                                <ul class="stepper horizontal demos" id="horizontal" >
                                                    <li class="step" id="step1">
                                                        <div data-step-label="" class="step-title waves-effect waves-dark">Upload File</div>
                                                        <form name='dbcompare' method='post' enctype="multipart/form-data">
                                                            <div class="step-content">
                                                                <?php if(count($getServiceData) > 1) { ?>
                                                                <label for="servicecat"><strong>Select Service Category</strong></label>
                                                                    <div class="row">
                                                                        <div class="input-field col s6">
                                                                            <select name="servicecat" id="servicecat" onchange="getStoreList(this.value);" required="required">
                                                                                <option value="">Select Category</option>
                                                                                <?php for($g=0;$g<count($getServiceData);$g++) { ?>
                                                                                 <option value="<?= $getServiceData[$g]['iServiceId']; ?>"><?= $getServiceData[$g]['serviceName']; ?></option>
                                                                                <?php } ?>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                <?php }else { ?>
                                                                    <input type="hidden" value="<?= $getServiceData[0]['iServiceId']; ?>" name="servicecat" id="servicecat">
                                                                <?php } ?>
                                                                <label for="iCompanyId"><strong>Select Store</strong></label>
                                                                <div class="row">
                                                                    <div class="input-field col s6">
                                                                        <select name="iCompanyId" id="iCompanyId" onchange="setCompanyId(this.value);" required="required">
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <!--<label for="uploadFileCuisine">Select Cuisine csv File</label>
                                                                <div class="row">
                                                                    <div class="input-field col s12">
                                                                        <input type="file" class="validate" name="uploadFileCuisine" id="uploadFileCuisine" required=""/>

                                                                    </div>
                                                                </div>-->
                                                                <label for="uploadFile"><strong>Select Item csv File </strong></label>&nbsp;&nbsp;<a style="cursor: pointer;" onclick="downloadCsv();">(Download Sample File)</a>
                                                                <div class="row">
                                                                    <div class="input-field col s12">
                                                                        <input type="file" class="validate" name="uploadFile" id="uploadFile" accept=".csv" required=""/>
                                                                    </div>
                                                                </div>
                                                                <div class="step-actions">
                                                                    <input type="submit" class="waves-effect waves-dark btn blue" name='comparedb' value="CONTINUE">
                                                                    <!--<button class="waves-effect waves-dark btn blue next-step">CONTINUE</button>-->
                                                                </div>
                                                            </div>
                                                            <!--<div class="step-content">
                                                                <div class="row">
                                                                   <div class="input-field col s12">
                                                                      <input id="linear_email" name="linear_email" type="email" class="validate" required>
                                                                      <label for="linear_email">Your e-mail</label>
                                                                   </div>
                                                                </div>
                                                                <div class="step-actions">
                                                                   <button class="waves-effect waves-dark btn blue next-step">CONTINUE</button>
                                                                </div>
                                                             </div>-->
                                                        </form>
                                                    </li>
                                                    <li class="step" id="step2">
                                                        <div class="step-title waves-effect waves-dark">Data Process</div>
                                                        <div class="step-content">
                                                            <div class="row">
                                                                <div class="input-field col s12">
                                                                    <ul class="proccess-list">
                                                                        <li><i>1</i> <span>Validating your data</span><img id="validatetick" class="mark-icon" src="<?= $siteUrl; ?>assets/img/tick.png"><img id="validateloader" class="loader-gif" src="<?= $siteUrl; ?>assets/img/giphy.gif"></li>
                                                                        <li><i>2</i> <span>Importing Category</span><img id="categorytick" class="mark-icon" src="<?= $siteUrl; ?>assets/img/tick.png"><img id="categoryloader" class="loader-gif" src="<?= $siteUrl; ?>assets/img/giphy.gif"></li>
                                                                        <li><i>3</i> <span>Importing Items</span><img id="itemtick" class="mark-icon" src="<?= $siteUrl; ?>assets/img/tick.png"><img id="itemloader" class="loader-gif" src="<?= $siteUrl; ?>assets/img/giphy.gif"></li>
                                                                        <li><i>4</i> <span>Configuring Images</span><img id="imagetick" class="mark-icon" src="<?= $siteUrl; ?>assets/img/tick.png"><img id="imageloader" class="loader-gif" src="<?= $siteUrl; ?>assets/img/giphy.gif"></li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                            <div class="step-actions">
                                                                <button class="waves-effect waves-dark btn blue next-step" data-feedback="someFunction" style="color:#ffffff !important;">CONTINUE</button>
                                                                <!--<button class="waves-effect waves-dark btn-flat previous-step">BACK</button>-->
                                                            </div>
                                                        </div>
                                                    </li>
                                                    <li class="step" id="step3">
                                                        <div class="step-title waves-effect waves-dark">Data Finalize</div>
                                                        <div class="step-content center-ico">
                                                            <!--Items data added successfully!-->
                                                            <img id="finaltick" src="<?= $siteUrl; ?>assets/img/tick.png">
                                                            <div class="step-actions">
                                                                <a target="_blank" href="<?= $siteUrl; ?>menu_item.php"><span class="waves-effect waves-dark btn blue" style="color:#ffffff !important;">VIEW</span></a>
                                                            </div>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> <!--TABLE-END-->
                        </div>
                    </div>
                </div>
            </div>
            <!--END PAGE CONTENT -->
        </div>
        <!--END MAIN WRAPPER -->

        <?php include_once('footer.php'); ?>
        <!--<script src="js/jquery-1.7.1.min.js"></script>-->
        <script src="<?= $siteUrl; ?>assets/js/stepper/materialize.min.js"></script>
        <script src="<?= $siteUrl; ?>assets/js/stepper/mstepper.js"></script>
        <!-- <script src="https://cdn.jsdelivr.net/npm/clipboard@2/dist/clipboard.min.js"></script> -->
        <script src="<?= $siteUrl; ?>assets/js/stepper/prism.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var sideNav = document.querySelector('.toc-wrapper');
                var footer = document.querySelector('#footer');
                //console.log(sideNav.offsetHeight)
                M.Pushpin.init(sideNav, {top: sideNav.offsetTop, offset: 77, bottom: footer.offsetTop + footer.offsetHeight - 350});
                var scrollSpy = document.querySelectorAll('.scrollspy');
                M.ScrollSpy.init(scrollSpy);
            });
            var domSteppers = document.querySelectorAll('.stepper.demos');
            for (var i = 0, len = domSteppers.length; i < len; i++) {
                var domStepper = domSteppers[i];
                new MStepper(domStepper);
            }
            var stepNo = '<?= $step; ?>';
            $(document).ready(function () {
                var defaultServiceId = $("#servicecat").val();
                getStoreList(defaultServiceId);
                $("#validatetick,#validateloader,#categorytick,#categoryloader,#itemloader,#itemtick,#imagetick,#imageloader").hide();
                var errorMsg = "<?= $errorMsg; ?>";
                if(errorMsg != ""){
                    $("#alertfail").show();
                    $("#messagetxtfail").text(errorMsg);
                }
                setTimeout(function () {
                    $("#alertfail,#alertscs").hide();
                }, 7000);
                if (stepNo == 2) {
                    $("#step1,#step3").removeClass("active");
                    $("#step2").addClass("active");
                    $("#step1").addClass("done");
                    var uploadedFile = '<?= $uploadedFile; ?>';
                    validateData(uploadedFile, "validate");
                } else if (stepNo == 3) {
                    $("#step1,#step2").removeClass("active");
                    $("#step1,#step2").addClass("done");
                    $("#step3").addClass("active");
                } else {
                    $("#step2,#step3").removeClass("active");
                    $("#step1").addClass("active");
                }
            });
            function someFunction(destroyFeedback) {
                setTimeout(function () {
                    destroyFeedback(true);
                }, 1000);
            }
            var allstoredata = [];
            allstoredata = <?= json_encode($storeArr, JSON_UNESCAPED_UNICODE); ?>;
            function getStoreList(serviceId){
                var storeHtml = "<option value=''>Select Store</option>";
                if(serviceId > 0){
                    var storeArr = allstoredata[serviceId];
                    for (var t = 0, n = storeArr.length; t < n; t++){
                        storeHtml += "<option value='"+storeArr[t]['iCompanyId']+"'>"+storeArr[t]['vCompany']+"</option>";
                    }
                    console.log(storeHtml);
                }
                $("#iCompanyId").html(storeHtml);
            }
            function setCompanyId(iCompanyId){
                localStorage.serviceId = $("#servicecat").val();
                localStorage.companyId = iCompanyId;
            }
            function downloadCsv(){
                var action = "<?= $siteUrl; ?>import_item_data.php";
                window.location.href = action + '?export=csv';
            }
            function validateData(uploadedFile, stepType) {
                //alert(uploadedFile);
                if (stepType == "validate") {
                    $("#validateloader").show();
                    $("#validatetick,#categoryloader,#categorytick,#itemtick,#itemloader,#imagetick,#imageloader").hide();
                }
                var serviceId = localStorage.serviceId;
                var companyId = localStorage.companyId;
                $.ajax({
                    type: "POST",
                    url: "<?= $siteUrl; ?>ajax_data_process.php",
                    data: {file: uploadedFile, step: stepType,iServiceId:serviceId,iCompanyId:companyId},
                    dataType: 'json',
                    success: function (dataHtml)
                    {
                        console.log(dataHtml.action);
                        var stepName = dataHtml.step;
                        if (dataHtml.action > 0) {
                            $("#step3,#step2").removeClass("active");
                            $("#step1").addClass("active");
                            $("#step1").removeClass("done");
                            $("#validateloader").hide();
                            $("#alertfail").show();
                            $("#messagetxtfail").text(dataHtml.message);
                        } else {
                            //$("#alertscs").show();
                            //$("#messagetxtscs").text(dataHtml.message);
                            if (stepName == "validate") {
                                stepType = "importCat";
                                $("#validatetick,#categoryloader").show();
                                $("#validateloader,#categorytick,#itemtick,#itemloader,#imagetick,#imageloader").hide();
                            } else if (stepName == "importCat") {
                                stepType = "importItem";
                                $("#validatetick,#categorytick,#itemloader").show();
                                $("#validateloader,#categoryloader,#itemtick,#imagetick,#imageloader").hide();
                            } else if (stepName == "importItem") {
                                stepType = "configImage";
                                $("#validatetick,#categorytick,#itemtick,#imageloader").show();
                                $("#validateloader,#categoryloader,#itemloader,#imagetick").hide();
                            } else if (stepName == "configImage") {
                                $("#validatetick,#categorytick,#itemtick,#imagetick").show();
                                $("#validateloader,#categoryloader,#itemloader,#imageloader").hide();
                                stepType = "";
                                $("#step1,#step2").removeClass("active");
                                $("#step3").addClass("active");
                            }
                            if (stepType != "") {
                                validateData(uploadedFile, stepType);
                            } else {
                                localStorage.serviceId = 0;
                                localStorage.companyId = 0;
                                $("#step1,#step2").addClass("done");
                            }
                        }
                    }
                });
            }
        </script> 
    </body>
    <!-- END BODY-->
</html>
