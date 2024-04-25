<?php





include_once 'common.php';

$userType = $_REQUEST['userType'] ?? '';
$isRatinaDisplay = $_REQUEST['isRatinaDisplay'] ?? '';
if (!empty($isRatinaDisplay)) {
    $_COOKIE['isRatinaDisplay'] = $isRatinaDisplay;
    echo 'success';

    exit;
}

if ('rider' === $userType) {
    $table = 'register_user';
} else {
    $table = 'register_driver';
}

if (isset($_REQUEST['vPhone'])) {
    $vPhone = $_REQUEST['vPhone'];
    $sql = "SELECT vPhone FROM {$table} WHERE vPhone = '".$vPhone."' ";
    $db_comp = $obj->MySQLSelect($sql);

    if (count($db_comp) > 0) {
        echo 'false';
    } else {
        echo 'true';
    }

    exit;
}
