<?php





include_once 'common.php';

// $log_directory = dirname(__FILE__)."/webimages/icons/country_flags/r/";
/*$log_directory = dirname(__FILE__)."/webimages/icons/country_flags/Round/";
$a = scandir($log_directory);
foreach($a as $key=>$value) {
    $name = explode('.',$value);
    if(!empty($name[0])) {
        //$new_name = str_replace("_r_r","_r",$name[0].".png");
        //rename($log_directory.$name[0].".png",$log_directory.$new_name);
        rename($log_directory.$name[0].".png",$log_directory.$name[0]."_r".".png");

    }
}

//$log_directory = dirname(__FILE__)."/webimages/icons/country_flags/s/";
$log_directory = dirname(__FILE__)."/webimages/icons/country_flags/Original/";
$a = scandir($log_directory);
foreach($a as $key=>$value) {
    $name = explode('.',$value);
    if(!empty($name[0])) {
        rename($log_directory.$name[0].".png",$log_directory.$name[0]."_s".".png");
    }
} */

/*$log_directory = dirname(__FILE__)."/webimages/icons/country_flags/";
$sqlflagdata = "SELECT iCountryId,vCountry,vCountryCode FROM country";
$dbflagdata = $obj->MySQLSelect($sqlflagdata);
$i = $j = 0;
$fileNotExistsArray_r = $fileNotExistsArray_s = array();
foreach($dbflagdata as $key=>$value) {
    $countryId = $value['iCountryId'];
    $countryCode = $value['vCountryCode'];
    $country = $value['vCountry'];
    $file_r = $log_directory.strtolower($countryCode)."_r.png";
    $file_s = $log_directory.strtolower($countryCode)."_s.png";
    if(!file_exists($file_r)) {
        $fileNotExistsArray_r[$i]['iCountryId'] = $countryId;
        $fileNotExistsArray_r[$i]['vCountryCode'] = $countryCode;
        $fileNotExistsArray_r[$i]['vCountry'] = $country;
        $fileNotExistsArray_r[$i]['filename'] = $file_r;
        $i++;
    }
    if(!file_exists($file_s)) {
        $fileNotExistsArray_s[$j]['iCountryId'] = $countryId;
        $fileNotExistsArray_s[$j]['vCountryCode'] = $countryCode;
        $fileNotExistsArray_s[$j]['vCountry'] = $country;
        $fileNotExistsArray_s[$j]['filename'] = $file_s;
        $j++;
    }
    //($value['vCountryCode']); //file_exists code
}
echo "<pre>";
print_R($fileNotExistsArray_r);
print_R($fileNotExistsArray_s);*/

/*
//update in country table all round and square images
$sqlflagdata = "SELECT iCountryId,vCountry,vCountryCode FROM country";
$dbflagdata = $obj->MySQLSelect($sqlflagdata);
foreach($dbflagdata as $key=>$value) {
    //print_R($value['vCountryCode']); exit;
    $imgname = strtolower($value['vCountryCode'])."_r.png";
    $imgnames = strtolower($value['vCountryCode'])."_s.png";
    $sql = $obj->sql_query("UPdate country SET vRImage =  '".$imgname."',  vSImage =  '".$imgnames."' WHERE iCountryId = '".$value['iCountryId']."'");
}
*/
