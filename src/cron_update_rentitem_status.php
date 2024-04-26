<?php
include_once('common.php');
/* Cron Log Update */
WriteToFile($tconfig['tsite_script_file_path'] . "cron_update_rentitem_status.txt", "running");
/* Cron Log Update End */
$CurrentDate = date("Y-m-d H:i:s");
// Add days to date and display it
$newdays = '+ '. $RENT_ITEM_REJECT_POST_AUTO_EXPIRE_DAYS_LIMIT .' days';

$expireDays = date('Y-m-d H:i:s', strtotime($CurrentDate.$newdays));

$sql = "SELECT * FROM rentitem_post  WHERE  eStatus = 'Reject'  AND dRejectedDate < CURRENT_DATE - INTERVAL $RENT_ITEM_REJECT_POST_AUTO_EXPIRE_DAYS_LIMIT DAY ";
$rentitem_post = $obj->MySQLSelect($sql);

$rsql = "SELECT * FROM rentitem_post  WHERE eStatus = 'Approved' AND DATE(dRenewDate) <= CURRENT_DATE()";
$rentitem_post_renewal = $obj->MySQLSelect($rsql);
$rentitem_postall = array_merge($rentitem_post,$rentitem_post_renewal); 


foreach ($rentitem_postall as $postk=>$post) {
	$cron_logs_id['inQuery'][] = $post['iRentItemPostId'];

	$updateQuery = "UPDATE `rentitem_post` SET `eStatus`='Expired' WHERE iRentItemPostId ='" . $post['iRentItemPostId'] . "' ";

	$obj->sql_query($updateQuery);

	$sql = "SELECT vEmail,vLastName,vName,vLang FROM register_user where  iUserId =  '" . $post['iUserId'] . "'";

    $data_user = $obj->MySQLSelect($sql);

    $vEmail = $data_user[0]['vEmail'];

    $vName = ucfirst($data_user[0]['vName']);

    $vLastName = $data_user[0]['vLastName'];

    $reqArr = array('vItemName','vRentItemPostNoMail');
    
    $getRentItemPostData = $RENTITEM_OBJ->getRentItemPostFinal("Web", $post['iRentItemPostId'], "" , $data_user[0]['vLang'],"","","",$reqArr);

    $mailTemplate = "USER_RENT_ITEM_EXPIRED";

    $maildata['EMAIL'] = $vEmail;

    $maildata['NAME'] = ucfirst($vName) . ' ' . $vLastName;

    $maildata['RENT_ITEM_NAME'] = $getRentItemPostData['vItemName'];

    $maildata['RENT_POST_NO'] = $getRentItemPostData['vRentItemPostNoMail'];

    $sendemail = $COMM_MEDIA_OBJ->SendMailToMember($mailTemplate, $maildata);

}

WriteToFile($tconfig['tsite_script_file_path'] . "cron_update_rentitem_status.txt", json_encode($cron_logs_id));
?>