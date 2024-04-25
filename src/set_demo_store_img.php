<?php

include_once('common.php');
$directory = $tconfig['tsite_upload_demo_compnay_doc_path'];
$images = glob($directory . "/*.*");
$demoImgArr = array();
foreach ($images as $image) {
    $imageName = pathinfo($image);
    //print_r($imageName['basename']);die;
    if (isset($imageName['basename']) && $imageName['basename'] != "") {
        $demoImgArr[] = $imageName['basename'];
    }
}
//echo "<pre>";print_r($demoImgArr);die;
//echo "<pre>";print_r($demoImgArr);die;
$getCompanyData = $obj->MySQLSelect("SELECT iCompanyId,vCompany FROM company WHERE iServiceId=1 AND eDemoDisplay='No' AND eStatus='Active'");
//echo "<pre>";print_r($getCompanyData);die;
$storIdArr = array();
$sr = 1;
for ($c = 0; $c < count($getCompanyData); $c++) {
    $k = array_rand($demoImgArr);
    $storeImg = $demoImgArr[$k];
    $storeDemoImg = array();
    $storIdArr[] = $getCompanyData[$c]['iCompanyId'];
    $storeDemoImg['vDemoStoreImage'] = $storeImg;
    $whereStoreId = "iCompanyId='" . $getCompanyData[$c]['iCompanyId'] . "'";
    echo $sr . ") " . $storeImg . " : This Image Successfully Set for Demo Store = " . $getCompanyData[$c]['vCompany'] . ".<br>";
    $obj->MySQLQueryPerform("company", $storeDemoImg, 'update', $whereStoreId);
    $sr++;
}
echo "All Demo Store Images Set Successfully";
die;
echo "<pre>";
print_r(implode($storIdArr, ","));
die;
?>