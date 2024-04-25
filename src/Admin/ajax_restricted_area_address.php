<?php



include_once '../common.php';

$countryid = $_REQUEST['countryid'] ?? '';
if ('' !== $countryid) {
    $sql = 'select vCountryCode from country where iCountryId ='.$countryid;
    $data = $obj->MySQLSelect($sql);

    echo $data[0]['vCountryCode'];
}
