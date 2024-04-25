<?php



include_once '../../common.php';

$reload = $_SERVER['REQUEST_URI'];
$urlparts = explode('?', $reload);
$parameters = $urlparts[1];
$id = $_REQUEST['id'] ?? '';
$iPageId = $_REQUEST['iPageId'] ?? '';
$status = $_REQUEST['status'] ?? '';
$statusVal = $_REQUEST['statusVal'] ?? '';
$action = $_REQUEST['action'] ?? 'view';
$hdn_del_id = $_REQUEST['hdn_del_id'] ?? '';
$checkbox = isset($_REQUEST['checkbox']) ? implode(',', $_REQUEST['checkbox']) : '';
$method = $_REQUEST['method'] ?? '';

if ('' !== $iPageId && '' !== $status) {
    if (SITE_TYPE !== 'Demo') {
        $query = "UPDATE pages SET eStatus = '".$status."' WHERE iPageId = '".$iPageId."'";
        $obj->sql_query($query);
        $_SESSION['success'] = '1';
        if ('Active' === $status) {
            $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_ACTIVATE_MSG'];
        } else {
            $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_INACTIVATE_MSG'];
        }
    } else {
        $_SESSION['success'] = 2;
    }
    header('Location:'.$tconfig['tsite_url_main_admin'].'page.php?'.$parameters);

    exit;
}
// End Change single Status
