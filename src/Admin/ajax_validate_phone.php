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
} elseif ('' !== $iDriverId) {
    $ssql = " AND iDriverId !='".$iDriverId."'";
} elseif ('' !== $iUserId) {
    $ssql = " AND iUserId !='".$iUserId."'";
} else {
    $ssql = ' ';
}

if (isset($_REQUEST['iCompanyId'], $_REQUEST['vPhone'])) {
    $vPhone = $_REQUEST['vPhone'];

    $sql1 = "SELECT count('vPhone') as Total,eStatus FROM company WHERE vPhone = '".$vPhone."'".$ssql;
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

if (isset($_REQUEST['iOrganizationId'], $_REQUEST['vPhone'])) {
    $vPhone = $_REQUEST['vPhone'];

    $sql1 = "SELECT count('vPhone') as Total,eStatus FROM organization WHERE vPhone = '".$vPhone."'".$ssql;
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

if (isset($_REQUEST['iDriverId'], $_REQUEST['vPhone'])) {
    $vPhone = $_REQUEST['vPhone'];

    $sql2 = "SELECT count('vPhone') as Total,eStatus FROM register_driver WHERE vPhone = '".$vPhone."'".$ssql;
    $db_driver = $obj->MySQLSelect($sql2);

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

if (isset($_REQUEST['iUserId'], $_REQUEST['vPhone'])) {
    $vPhone = $_REQUEST['vPhone'];

    $sql2 = "SELECT count('vPhone') as Total,eStatus FROM register_user WHERE vPhone = '".$vPhone."'".$ssql;
    $db_user = $obj->MySQLSelect($sql2);

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
