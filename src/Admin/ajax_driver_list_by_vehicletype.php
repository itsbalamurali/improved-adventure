<?php



include_once '../common.php';

$vehicleid = $_REQUEST['vehicleId'] ?? '';
if ('' !== $vehicleid) {
    $sql = "SELECT rd.vName,rd.vLastName,rd.iDriverId,dv.vCarType FROM register_driver rd left join driver_vehicle dv on rd.iDriverId=dv.iDriverId where rd.eStatus='active' and dv.eStatus ='Active' group by rd.iDriverId ORDER BY vName ASC";
    $db_driver = $obj->MySQLSelect($sql);

    for ($i = 0; $i < count($db_driver); ++$i) {
        $cartypes = explode(',', $db_driver[$i]['vCarType']);
        if (in_array($vehicleid, $cartypes, true)) {
            $Data[$i]['vName'] = $db_driver[$i]['vName'];
            $Data[$i]['vLastName'] = $db_driver[$i]['vLastName'];
            $Data[$i]['iDriverId'] = $db_driver[$i]['iDriverId'];
        }
    }

    $cont = '';
    if (count($Data) > 0) {
        $cont .= '<option value="">Select '.$langage_lbl_admin['LBL_DRIVERS_NAME_ADMIN'].'</option>';
        $cont .= '';
        foreach ($Data as $values) {
            $cont .= '<option value="'.$values['iDriverId'].'">'.clearName($values['vName'].' '.$values['vLastName']).'</option>';
        }
    } else {
        $cont .= '<option value="">No '.$langage_lbl_admin['LBL_DRIVERS_NAME_ADMIN'].' Found</option>';
    }
    echo $cont;

    exit;
}
