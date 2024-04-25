<?php



include_once '../common.php';

$fromLat = $_REQUEST['fromLat'] ?? '';
$fromLong = $_REQUEST['fromLong'] ?? '';
$toLat = $_REQUEST['toLat'] ?? '';
$toLong = $_REQUEST['toLong'] ?? '';
$type = $_REQUEST['type'] ?? '';

$sourceLocationArr = [$fromLat, $fromLong];
$destinationLocationArr = [$toLat, $toLong];

/*if($type == "both"){

    if($sourceLocationArr != "") {
        $allowed_ans = checkAreaRestriction($sourceLocationArr,"No");
    }
    if($destinationLocationArr != ""){
        $allowed_ans_drop = checkAreaRestriction($destinationLocationArr,"Yes");
    }
    if($allowed_ans == "No" && $allowed_ans_drop == "No"){
        echo $langage_lbl_admin['LBL_PICK_DROP_LOCATION_NOT_ALLOW'].'. Are You Sure Continue With This Loaction.' ;
        exit;
    }
    if($allowed_ans == "Yes" && $allowed_ans_drop == "No"){
        echo $langage_lbl_admin['LBL_DROP_LOCATION_NOT_ALLOW'] .'. Are You Sure Continue With This DropOff Loaction.';
        exit;
    }
    if($allowed_ans == "No" && $allowed_ans_drop == "Yes"){
        echo $langage_lbl_admin['LBL_PICKUP_LOCATION_NOT_ALLOW'].'. Are You Sure Continue With This Pickup Loaction.';
        exit;
    }

} */

if ('from' === $type) {
    if ('' !== $sourceLocationArr) {
        $allowed_ans = checkAreaRestriction($sourceLocationArr, 'No');
    }
    if ('No' === $allowed_ans) {
        echo $langage_lbl_admin['LBL_PICKUP_LOCATION_NOT_ALLOW'].'. '.$langage_lbl_admin['LBL_MANUAL_BOOKING_ARE_SURE_CONTINUE_LOCATION'].'.';

        exit;
    }
}
if ('to' === $type) {
    if ('' !== $destinationLocationArr) {
        $allowed_ans_drop = checkAreaRestriction($destinationLocationArr, 'Yes');
    }
    if ('No' === $allowed_ans_drop) {
        echo $langage_lbl_admin['LBL_DROP_LOCATION_NOT_ALLOW'].'. '.$langage_lbl_admin['LBL_MANUAL_BOOKING_ARE_SURE_CONTINUE_LOCATION'].'.';

        exit;
    }
}
