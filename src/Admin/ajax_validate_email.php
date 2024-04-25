<?php



include_once '../common.php';

$iCompanyId = $_REQUEST['iCompanyId'] ?? '';
$iOrganizationId = $_REQUEST['iOrganizationId'] ?? '';
$iAdminId = $_REQUEST['iAdminId'] ?? '';
$iDriverId = $_REQUEST['iDriverId'] ?? '';
$iUserId = $_REQUEST['iUserId'] ?? '';

if ('' !== $iCompanyId) {
    $ssql = " AND iCompanyId !='".$iCompanyId."'";
} elseif ('' !== $iOrganizationId) {
    $ssql = " AND iOrganizationId !='".$iOrganizationId."'";
} elseif ('' !== $iAdminId) {
    $ssql = " AND iAdminId !='".$iAdminId."'";
} elseif ('' !== $iDriverId) {
    $ssql = " AND iDriverId !='".$iDriverId."'";
} elseif ('' !== $iUserId) {
    $ssql = " AND iUserId !='".$iUserId."'";
} else {
    $ssql = ' ';
}

if (isset($_REQUEST['iAdminId'], $_REQUEST['vEmail'])) {
    $email = $_REQUEST['vEmail'];

    $sql1 = "SELECT count('vEmail') as Total,eStatus FROM administrators WHERE vEmail = '".$email."'".$ssql;
    $db_adm = $obj->MySQLSelect($sql1);

    if ($db_adm[0]['Total'] > 0) {
        if (('Deleted' === ucfirst($db_adm[0]['eStatus'])) || ('Inactive' === ucfirst($db_adm[0]['eStatus']))) {
            echo 'deleted';
        } else {
            echo 'false';
        }
    } else {
        echo 'true';
    }
}

// Use For Organization Module

if (isset($_REQUEST['iOrganizationId'], $_REQUEST['vEmail'])) {
    $email = $_REQUEST['vEmail'];

    $sql1 = "SELECT count('vEmail') as Total,eStatus FROM organization WHERE vEmail = '".$email."'".$ssql;
    $db_comp = $obj->MySQLSelect($sql1);

    if ($db_comp[0]['Total'] > 0) {
        if (('Deleted' === ucfirst($db_comp[0]['eStatus'])) || ('Inactive' === ucfirst($db_comp[0]['eStatus']))) {
            echo 'deleted';
        } else {
            echo 'false';
        }
    } else {
        echo 'true';
    }
}

// Use For Organization Module

if (isset($_REQUEST['iCompanyId'], $_REQUEST['vEmail'])) {
    $email = $_REQUEST['vEmail'];

    $sql1 = "SELECT count('vEmail') as Total,eStatus FROM company WHERE vEmail = '".$email."'".$ssql;
    $db_comp = $obj->MySQLSelect($sql1);

    if ($db_comp[0]['Total'] > 0) {
        if (('Deleted' === ucfirst($db_comp[0]['eStatus'])) || ('Inactive' === ucfirst($db_comp[0]['eStatus']))) {
            echo 'deleted';
        } else {
            echo 'false';
        }
    } else {
        echo 'true';
    }
}

if (isset($_REQUEST['iDriverId'], $_REQUEST['vEmail'])) {
    $email = $_REQUEST['vEmail'];

    /*$sql1 = "SELECT count('vEmail') as Total,eStatus FROM administrators WHERE vEmail = '".$email."'";
    $db_adm = $obj->MySQLSelect($sql1);*/

    $sql2 = "SELECT count('vEmail') as Total,eStatus FROM register_driver WHERE vEmail = '".$email."'".$ssql;
    $db_driver = $obj->MySQLSelect($sql2);

    /*$sql2 = "SELECT count('vEmail') as Total,eStatus FROM company WHERE vEmail = '".$email."'";
    $db_comp = $obj->MySQLSelect($sql2);*/
    // if($db_adm[0]['Total'] > 0 || $db_driver[0]['Total'] > 0 || $db_comp[0]['Total'] > 0)
    if ($db_driver[0]['Total'] > 0) {
        if (('Deleted' === ucfirst($db_driver[0]['eStatus'])) || ('Inactive' === ucfirst($db_driver[0]['eStatus']))) {
            echo 'deleted';
        } else {
            echo 'false';
        }
    } else {
        echo 'true';
    }
}

if (isset($_REQUEST['iUserId'], $_REQUEST['vEmail'])) {
    $email = $_REQUEST['vEmail'];

    /*$sql1 = "SELECT count('vEmail') as Total,eStatus FROM administrators WHERE vEmail = '".$email."'";
    $db_adm = $obj->MySQLSelect($sql1);*/

    $sql2 = "SELECT count('vEmail') as Total,eStatus FROM register_user WHERE vEmail = '".$email."'".$ssql;
    $db_user = $obj->MySQLSelect($sql2);

    /*$sql2 = "SELECT count('vEmail') as Total,eStatus FROM company WHERE vEmail = '".$email."'";
    $db_comp = $obj->MySQLSelect($sql2);*/
    // if($db_adm[0]['Total'] > 0 || $db_user[0]['Total'] > 0 || $db_comp[0]['Total'] > 0)
    if ($db_user[0]['Total'] > 0) {
        if (('Deleted' === ucfirst($db_user[0]['eStatus'])) || ('Inactive' === ucfirst($db_user[0]['eStatus']))) {
            echo 'deleted';
        } else {
            echo 'false';
        }
    } else {
        echo 'true';
    }
}
