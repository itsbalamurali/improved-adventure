<?php



include_once '../common.php';

$script = 'language_label';
$tbl_update_name = 'language_label';

$vLabel = $_REQUEST['vLabel'] ?? '';

$CURRENT_FILE_NAME = basename($_SERVER['SCRIPT_FILENAME']);

// ################################################################################################
function isDeliverAllLanguageTables($lang_table)
{
    global $DELIVERALL_LNG_TABLES;
    if (empty($DELIVERALL_LNG_TABLES)) {
        $DELIVERALL_LNG_TABLES = [];

        for ($i = 0; $i < 500; ++$i) {
            $DELIVERALL_LNG_TABLES[] = 'language_label_'.$i;
        }
    }

    return in_array($lang_table, $DELIVERALL_LNG_TABLES, true);
}

function updateLblValues($database, $table, $obj)
{
    global $vLabel, $tbl_name;

    $taxi_db = ['master_taxi', 'master_taxi_old'];
    $taxi_tables = ['language_label_ride', 'language_label_taxi'];

    $taxi_delivery_db = ['master_taxi_delivery', 'master_ride_delivery'];
    $taxi_delivery_tables = ['language_label_ride_delivery', 'language_label_taxi_delivery'];

    $delivery_db = ['master_delivery', 'master_deliver'];
    $delivery_tables = ['language_label_delivery', 'language_label_deliver'];

    $ufx_db = ['master_ufx'];
    $ufx_tables = ['language_label_uberx'];

    $deliverall_db = ['master_DeliverAll'];
    $deliverall_tables = ['language_label_deliverall'];

    $deliverall_food_db = ['master_food'];
    $deliverall_food_tables = ['language_label_food'];

    $cubejek_db = ['master_cubejek', 'master_cubejekdevshark'];
    $cubejek_tables = ['language_label'];

    if (in_array($table, $taxi_tables, true)) {
        $obj->sql_query('DELETE FROM `'.$table."` WHERE `vLabel` LIKE '".$vLabel."'");

        return true;
    }
    if (in_array($table, $taxi_delivery_tables, true)) {
        $obj->sql_query('DELETE FROM `'.$table."` WHERE `vLabel` LIKE '".$vLabel."'");

        return true;
    }
    if (in_array($table, $delivery_tables, true)) {
        $obj->sql_query('DELETE FROM `'.$table."` WHERE `vLabel` LIKE '".$vLabel."'");

        return true;
    }
    if (in_array($table, $ufx_tables, true)) {
        $obj->sql_query('DELETE FROM `'.$table."` WHERE `vLabel` LIKE '".$vLabel."'");

        return true;
    }
    if (in_array($table, $deliverall_tables, true)) {
        $obj->sql_query('DELETE FROM `'.$table."` WHERE `vLabel` LIKE '".$vLabel."'");

        return true;
    }
    if (in_array($table, $deliverall_food_tables, true)) {
        $obj->sql_query('DELETE FROM `'.$table."` WHERE `vLabel` LIKE '".$vLabel."'");

        return true;
    }

    if (in_array($database, $taxi_db, true)) {
        if (in_array($table, $taxi_tables, true) || 'language_label' === $table) {
            $obj->sql_query('DELETE FROM `'.$table."` WHERE `vLabel` LIKE '".$vLabel."'");
        }

        return true;
    }
    if (in_array($database, $taxi_delivery_db, true)) {
        if (in_array($table, $taxi_delivery_tables, true) || 'language_label' === $table) {
            $obj->sql_query('DELETE FROM `'.$table."` WHERE `vLabel` LIKE '".$vLabel."'");
        }

        return true;
    }
    if (in_array($database, $delivery_db, true)) {
        if (in_array($table, $delivery_tables, true) || 'language_label' === $table) {
            $obj->sql_query('DELETE FROM `'.$table."` WHERE `vLabel` LIKE '".$vLabel."'");
        }

        return true;
    }
    if (in_array($database, $ufx_db, true)) {
        if (in_array($table, $ufx_tables, true) || 'language_label' === $table) {
            $obj->sql_query('DELETE FROM `'.$table."` WHERE `vLabel` LIKE '".$vLabel."'");
        }

        return true;
    }
    if (in_array($database, $deliverall_db, true)) {
        if (in_array($table, $deliverall_tables, true) || 'language_label' === $table) {
            $obj->sql_query('DELETE FROM `'.$table."` WHERE `vLabel` LIKE '".$vLabel."'");
        }

        return true;
    }
    if (in_array($database, $deliverall_food_db, true)) {
        if (in_array($table, $deliverall_food_tables, true) || 'language_label' === $table) {
            $obj->sql_query('DELETE FROM `'.$table."` WHERE `vLabel` LIKE '".$vLabel."'");
        }

        return true;
    }

    $obj->sql_query('DELETE FROM `'.$table."` WHERE `vLabel` LIKE '".$vLabel."'");

    return true;
}

// ################################################################################################

// DATABASES From 131 OR 141 Server
$all_databse_data = $obj->MySQLSelect('SHOW DATABASES');

$all_databse_data_arr = [];
foreach ($all_databse_data as $all_databse_data_item) {
    if (startsWith($all_databse_data_item['Database'], 'cubejekdev') || startsWith($all_databse_data_item['Database'], 'master_')  /* && $all_databse_data_item['Database'] == "master_cubejek" */) {
        $all_databse_data_arr[] = $all_databse_data_item['Database'];
    }
}
// DATABASES From 131 OR 141 Server

// DATABASES From opposite Server
$OPPOSITE_HOST = '';
$USER_OPPOSITE_HOST = '';
$PASSWORD_OPPOSITE_HOST = '';
if ('192.168.1.131' === $_SERVER['HTTP_HOST'] || '192.168.1.151' === $_SERVER['HTTP_HOST']) {
    $OPPOSITE_HOST = '192.168.1.141';
    $USER_OPPOSITE_HOST = 'dbuser';
    $PASSWORD_OPPOSITE_HOST = 'Admin@205';
    $obj_opposite = new DBConnection($OPPOSITE_HOST, 'cubejekdev', $USER_OPPOSITE_HOST, $PASSWORD_OPPOSITE_HOST);
} elseif ('192.168.1.141' === $_SERVER['HTTP_HOST']) {
    $OPPOSITE_HOST = '192.168.1.131';
    $USER_OPPOSITE_HOST = 'root';
    $PASSWORD_OPPOSITE_HOST = 'root';
    $obj_opposite = new DBConnection($OPPOSITE_HOST, 'cubejekdev_development', $USER_OPPOSITE_HOST, $PASSWORD_OPPOSITE_HOST);
}
if (!empty($obj_opposite)) {
    $all_databse_data_opposite_arr = [];
    $all_databse_data = $obj_opposite->MySQLSelect('SHOW DATABASES');
    $position_count = 0;
    foreach ($all_databse_data as $all_databse_data_item) {
        if (startsWith($all_databse_data_item['Database'], 'cubejekdev') || startsWith($all_databse_data_item['Database'], 'master_')  /* && $all_databse_data_item['Database'] == "cubejekdev_prod_24122019______7894" */) {
            $all_databse_data_opposite_arr[$position_count]['HOST'] = $OPPOSITE_HOST;
            $all_databse_data_opposite_arr[$position_count]['DB'] = $all_databse_data_item['Database'];
            $all_databse_data_opposite_arr[$position_count]['HOST_USER'] = $USER_OPPOSITE_HOST;
            $all_databse_data_opposite_arr[$position_count]['HOST_PASSWORD'] = $PASSWORD_OPPOSITE_HOST;
            ++$position_count;
        }
    }

    $obj_opposite->MySQLClose();
}
// DATABASES From opposite Server

// Retrieve And Set Label to multiple DB

foreach ($all_databse_data_arr as $all_databse_data_arr_item) {
    $obj_current_connection = new DBConnection(TSITE_SERVER, $all_databse_data_arr_item, TSITE_USERNAME, TSITE_PASS);

    $all_tables_lng_arr = [];
    $all_tables_arr = [];
    $all_tables = $obj_current_connection->MySQLSelect('SHOW TABLES');
    foreach ($all_tables as $all_tables_item) {
        $item = $all_tables_item['Tables_in_'.$all_databse_data_arr_item];
        $all_tables_arr[] = $item;
        if (true === startsWith($item, 'language_label') && false === startsWith($item, 'language_label_other')) {
            $all_tables_lng_arr[] = $item;
        }
    }

    for ($i = 0; $i < count($all_tables_lng_arr); ++$i) {
        $table_name_tmp = $all_tables_lng_arr[$i];

        if (false === isDeliverAllLanguageTables($table_name_tmp)) {
            updateLblValues($all_databse_data_arr_item, $table_name_tmp, $obj_current_connection);
        }
    }

    if (in_array('register_driver', $all_tables_arr, true)) {
        $obj_current_connection->sql_query("UPDATE register_driver SET eChangeLang = 'Yes' WHERE 1=1");
    }

    if (in_array('register_user', $all_tables_arr, true)) {
        $obj_current_connection->sql_query("UPDATE register_user SET eChangeLang = 'Yes' WHERE 1=1");
    }

    if (in_array('company', $all_tables_arr, true)) {
        $obj_current_connection->sql_query("UPDATE company SET eChangeLang = 'Yes' WHERE 1=1");
    }

    $obj_current_connection->MySQLClose();
}

if (!empty($all_databse_data_opposite_arr) && count($all_databse_data_opposite_arr) > 0) {
    for ($ik = 0; $ik < count($all_databse_data_opposite_arr); ++$ik) {
        $all_databse_data_opposite_arr_item = $all_databse_data_opposite_arr[$ik];

        $obj_opposite_connection = new DBConnection($all_databse_data_opposite_arr_item['HOST'], $all_databse_data_opposite_arr_item['DB'], $all_databse_data_opposite_arr_item['HOST_USER'], $all_databse_data_opposite_arr_item['HOST_PASSWORD']);

        $all_tables_lng_arr = [];
        $all_tables_arr = [];
        $all_tables = $obj_opposite_connection->MySQLSelect('SHOW TABLES');
        foreach ($all_tables as $all_tables_item) {
            $item = $all_tables_item['Tables_in_'.$all_databse_data_opposite_arr_item['DB']];
            $all_tables_arr[] = $item;
            if (true === startsWith($item, 'language_label') && false === startsWith($item, 'language_label_other')) {
                $all_tables_lng_arr[] = $item;
            }
        }

        for ($i = 0; $i < count($all_tables_lng_arr); ++$i) {
            $table_name_tmp = $all_tables_lng_arr[$i];

            if (!isDeliverAllLanguageTables($table_name_tmp)) {
                updateLblValues($all_databse_data_opposite_arr_item['DB'], $table_name_tmp, $obj_opposite_connection);
            }
        }

        if (in_array('register_driver', $all_tables_arr, true)) {
            $obj_opposite_connection->sql_query("UPDATE register_driver SET eChangeLang = 'Yes' WHERE 1=1");
        }

        if (in_array('register_user', $all_tables_arr, true)) {
            $obj_opposite_connection->sql_query("UPDATE register_user SET eChangeLang = 'Yes' WHERE 1=1");
        }

        if (in_array('company', $all_tables_arr, true)) {
            $obj_opposite_connection->sql_query("UPDATE company SET eChangeLang = 'Yes' WHERE 1=1");
        }

        $obj_opposite_connection->MySQLClose();
    }
}

$returnArr = [];
$returnArr['Action'] = '1';
$returnArr['message'] = 'Label is deleted successfully';
echo json_encode($returnArr);

exit;
