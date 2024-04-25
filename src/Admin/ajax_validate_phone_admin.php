<?php



include_once '../common.php';
$ssql = ' ';
$iAdminId = $_REQUEST['iAdminId'] ?? '';
if ('' !== $iAdminId) {
    $ssql = " AND iAdminId !='".$iAdminId."'";
}
if (isset($_REQUEST['iAdminId'], $_REQUEST['vContactNo'])) {
    $vContactNo = $_REQUEST['vContactNo'];
    $vCountry = $_REQUEST['vCountry'];
    $eSystem = '';
    $checEmailExist = checkMemberDataInfo($vContactNo, '', 'ADMIN', $vCountry, $iAdminId, $eSystem);
    if (0 === $checEmailExist['status']) {
        echo 'false';
    } elseif (2 === $checEmailExist['status']) {
        echo 'false';
    } else {
        echo 'true';
    }
    /* $sql1 = "SELECT count('vContactNo') as Total,eStatus FROM administrators WHERE vContactNo = '" . $vContactNo . "'" . $ssql;
      $db_admin = $obj->MySQLSelect($sql1);
      if ($db_admin[0]['Total'] > 0) {
      if ((ucfirst($db_admin[0]['eStatus']) == 'Deleted') || (ucfirst($db_admin[0]['eStatus']) == 'Inactive')) {
      echo 'deleted';
      } else {
      echo 'false';
      }
      } else {
      echo 'true';
      } */
}
