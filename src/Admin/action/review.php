<?php



include_once '../../common.php';

$reload = $_SERVER['REQUEST_URI'];

$urlparts = explode('?', $reload);

$parameters = $urlparts[1];

$id = $_REQUEST['id'] ?? '';

$iRatingId = $_REQUEST['iRatingId'] ?? '';

$status = $_REQUEST['status'] ?? '';

$statusVal = $_REQUEST['statusVal'] ?? '';

$action = $_REQUEST['action'] ?? 'view';

$hdn_del_id = $_REQUEST['hdn_del_id'] ?? '';

$checkbox = isset($_REQUEST['checkbox']) ? implode(',', $_REQUEST['checkbox']) : '';

$method = $_REQUEST['method'] ?? '';

$reviewtype = $_REQUEST['reviewtype'] ?? '';

// Start make deleted

if ('delete' === $method && '' !== $iRatingId) {
    if (!$userObj->hasPermission('delete-reviews')) {
        $_SESSION['success'] = 3;

        $_SESSION['var_msg'] = 'You do not have permission to delete review';
    } else {
        if (SITE_TYPE !== 'Demo') {
            $query = "UPDATE ratings_user_driver SET eStatus = 'Deleted' WHERE iRatingId = '".$iRatingId."'";

            $obj->sql_query($query);

            $iTripId = get_value('ratings_user_driver', 'iTripId', 'iRatingId', $iRatingId, '', 'true');
            if ('Driver' === $reviewtype) {
                $iDriverId = get_value('trips', 'iDriverId', 'iTripId', $iTripId, '', 'true');
                $tableName = 'register_driver';
                $where = "iDriverId='".$iDriverId."'";
                $iMemberId = $iDriverId;
            } else {
                $iUserId = get_value('trips', 'iUserId', 'iTripId', $iTripId, '', 'true');
                $tableName = 'register_user';
                $where = "iUserId='".$iUserId."'";
                $iMemberId = $iUserId;
            }

            $Data_update['vAvgRating'] = FetchUserAvgRating($iMemberId, $reviewtype, 'Yes');

            $id1 = $obj->MySQLQueryPerform($tableName, $Data_update, 'update', $where);

            $_SESSION['success'] = '1';

            $_SESSION['var_msg'] = $langage_lbl_admin['LBL_RECORD_DELETE_MSG'];
        } else {
            $_SESSION['success'] = '2';
        }
    }

    header('Location:'.$tconfig['tsite_url_main_admin'].'review.php?'.$parameters);

    exit;
}

// End make deleted
