<?php



include_once '../common.php';

$countryId = $_REQUEST['countryId'] ?? '';
$stateId = $_REQUEST['stateId'] ?? '';
$selected = $_REQUEST['selected'] ?? '';
$fromMod = $_REQUEST['fromMod'] ?? '';

if (isset($_REQUEST['countryId'])) {
    if ('' === $fromMod) {
        $cons = "<option value='-1'>All</option>";
        $where = " AND iCountryId = '".$countryId."'";
    } else {
        $sql = "SELECT iCountryId FROM country WHERE 1=1 AND vCountryCode='".$countryId."' AND eStatus = 'Active'";
        $db_cntr = $obj->MySQLSelect($sql);

        $cons = "<option value=''>Select</option>";
        $where = " AND iCountryId = '".$db_cntr[0]['iCountryId']."'";
    }
    if ('' !== $countryId) {
        $sql = "SELECT iStateId, vState FROM state WHERE 1=1 AND eStatus = 'Active' {$where} ORDER BY vState ASC";
        $db_states = $obj->MySQLSelect($sql);

        foreach ($db_states as $dbs) {
            $cons .= "<option value='".$dbs['iStateId']."'";
            if ($dbs['iStateId'] === $selected) {
                $cons .= ' selected';
            }
            $cons .= '>'.$dbs['vState'].'</option>';
        }
    }
    echo $cons;

    exit;
}

if (isset($_REQUEST['stateId'])) {
    if ('' === $fromMod) {
        $cons = "<option value='-1'>All</option>";
    } else {
        $cons = "<option value=''>Select</option>";
    }
    if ('' !== $stateId) {
        $sql = "select iCityId, vcity from city where iStateId = '".$stateId."' and eStatus = 'Active' ORDER BY vcity ASC";
        $db_states = $obj->MySQLSelect($sql);

        foreach ($db_states as $dbs) {
            $cons .= "<option value='".$dbs['iCityId']."'";
            if ($dbs['iCityId'] === $selected) {
                $cons .= ' selected';
            }
            $cons .= '>'.$dbs['vcity'].'</option>';
        }
    }
    echo $cons;

    exit;
}
