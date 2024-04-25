<?php



include_once '../common.php';

if (isset($_REQUEST['name'])) {
    if ('' !== $_REQUEST['name']) {
        if ('Driver' === $_REQUEST['name']) {
            $user_name = $_REQUEST['name'];
            $sql = 'SELECT iDriverId,vName,vLastName FROM register_driver';
            $db_comp = $obj->MySQLSelect($sql);
            echo "<option value=''>Search By ".$langage_lbl_admin['LBL_DRIVER_TXT_ADMIN'].' type</option>';
            for ($i = 0; $i < count($db_comp); ++$i) {
                echo '<option value='.$db_comp[$i]['iDriverId'].'>'.clearName($db_comp[$i]['vName'].' '.$db_comp[$i]['vLastName']).'</option>';
            }

            exit;
        }
        $sql = 'SELECT iUserId,vName,vLastName FROM register_user ';
        $db_register_user = $obj->MySQLSelect($sql);

        echo "<option value=''>Search By ".$langage_lbl_admin['LBL_RIDER'].' type</option>';
        for ($i = 0; $i < count($db_register_user); ++$i) {
            echo '<option value='.$db_register_user[$i]['iUserId'].'>'.clearName($db_register_user[$i]['vName'].' '.$db_register_user[$i]['vLastName']).'</option>';
        }

        exit;
    }
}
