<?php





include_once 'common.php';

$countryId = $_REQUEST['countryId'] ?? '';
$stateId = $_REQUEST['stateId'] ?? '';
$selected = $_REQUEST['selected'] ?? '';
$fromMod = $_REQUEST['fromMod'] ?? '';

if (isset($_REQUEST['countryId'])) {
    if ('' === $fromMod) {
        $cons = "<option value='-1'>All</option>";
        $where = " AND iCountryId = '".$countryId."'";
    } else {
        $sql = "select iCountryId from country where 1=1 and eStatus = 'Active' AND vCountryCode='".$countryId."'";
        $db_cntr = $obj->MySQLSelect($sql);

        $cons = "<option value='' selected>".$langage_lbl['LBL_SELECT_TXT'].' '.$langage_lbl['LBL_STATE_TXT'].'</option>';
        $where = " AND iCountryId = '".$db_cntr[0]['iCountryId']."'";
    }
    if ('' !== $countryId) {
        $sql = "select iStateId, vState from state where 1=1 and eStatus = 'Active' {$where} ORDER BY vState ASC ";
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
        $cons = "<option value='' selected>".$langage_lbl['LBL_SELECT_TXT'].' '.$langage_lbl['LBL_CITY_TXT'].'</option>';
    }
    if ('' !== $stateId) {
        $sql = "select iCityId, vcity from city where iStateId = '".$stateId."' and eStatus = 'Active' ORDER BY vcity ASC ";
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
