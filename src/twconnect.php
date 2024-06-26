<?php





ob_start();
session_start();

include_once 'common.php';

require_once 'twitter/twitteroauth/twitteroauth.php';

$userType = (isset($_REQUEST['userType'])) ? $_REQUEST['userType'] : 'rider';
$gst = (isset($_REQUEST['gst'])) ? $_REQUEST['gst'] : '0';

if ('0' === $gst) {
    unset($_SESSION['oauth_token']);
}

$sql = "SELECT vValue FROM configurations WHERE vName='TWITTER_CONSUMER_KEY'";
$db_ckey = $obj->MySQLSelect($sql);

$sql = "SELECT vValue FROM configurations WHERE vName='TWITTER_CONSUMER_SECRET'";
$db_csecret = $obj->MySQLSelect($sql);

$twitter_callback = $tconfig['tsite_url'].'twconnect.php?gst=1&userType='.$userType;

include_once $tconfig['tsite_libraries_v'].'/Imagecrop.class.php';
$thumb = new thumbnail();
$temp_gallery = $tconfig['tsite_temp_gallery'];

include_once $tconfig['tsite_libraries_v'].'/SimpleImage.class.php';
$img = new SimpleImage();

if ('' === $db_ckey[0]['vValue'] || '' === $db_csecret[0]['vValue']) {
    echo 'You need a consumer key and secret to test the sample code. Get one from <a href="https://dev.twitter.com/apps">dev.twitter.com/apps</a>';

    exit;
}

if (!isset($_SESSION['oauth_token'])) {
    $connection = new TwitterOAuth($db_ckey[0]['vValue'], $db_csecret[0]['vValue']);
    $request_token = $connection->getRequestToken($twitter_callback);
    $_SESSION['oauth_token'] = $token = $request_token['oauth_token'];
    $_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];

    switch ($connection->http_code) {
        case 200:
            $url = $connection->getAuthorizeURL($token);
            header('Location:'.$url);

            break;

        default:
            $error = 'Could not connect to Twitter. Refresh the page or try again later.';
    }
} else {
    $connection = new TwitterOAuth($db_ckey[0]['vValue'], $db_csecret[0]['vValue'], $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
    $access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);
    $_SESSION['access_token'] = $access_token;
    $content = $connection->get('account/verify_credentials', ['include_email' => 'true']);
    $data = [];

    // echo "<pre>";
    // print_r($content); die;

    if (!empty($content->id)) {
        $fbid = $content->id;
        $name = explode(' ', $content->name);
        $fbfirstname = $name[0];
        $fblastname = $name[1];
        $femail = $content->email;
        // $data['screen_name'] = $content->screen_name;
        $picture_img = $content->profile_image_url;

        if ('' !== $femail) {
            $sqll001 = " vEmail='".$femail."'";
        } else {
            $sqll001 = " vFbId = '".$fbid."' AND eSignUpType = 'Twitter'";
        }
        if ('rider' === $userType) {
            if ('' !== $femail || '' !== $fbid) {
                $sql = "SELECT iUserId,vImgName,vPhone,eGender,eStatus FROM register_user WHERE {$sqll001}";
                $db_user = $obj->MySQLSelect($sql);
            }

            if (count($db_user) > 0) {
                if ('Deleted' === $db_user[0]['eStatus'] || 'Inactive' === $db_user[0]['eStatus']) {
                    if ('Deleted' === $db_user[0]['eStatus']) {
                        $_SESSION['sess_error_social'] = addslashes($langage_lbl['LBL_ACC_DELETE_TXT']);
                    } else {
                        $_SESSION['sess_error_social'] = addslashes($langage_lbl['LBL_ACCOUNT_NOT_ACTIVE_ERROR_MSG']);
                    }

                    if ('UberX' === $APP_TYPE || 'Ride-Delivery-UberX' === $APP_TYPE) {
                        $link = $tconfig['tsite_url'].'user-login';
                    } else {
                        $link = $tconfig['tsite_url'].'rider-login';
                    }

                    header('Location:'.$link);

                    exit;
                }

                $Photo_Gallery_folder = $tconfig['tsite_upload_images_passenger_path'].'/'.$db_user[0]['iUserId'].'/';

                unlink($Photo_Gallery_folder.$db_user[0]['vImgName']);
                unlink($Photo_Gallery_folder.'1_'.$db_user[0]['vImgName']);
                unlink($Photo_Gallery_folder.'2_'.$db_user[0]['vImgName']);
                unlink($Photo_Gallery_folder.'3_'.$db_user[0]['vImgName']);
                unlink($Photo_Gallery_folder.'4_'.$db_user[0]['vImgName']);

                if (!is_dir($Photo_Gallery_folder)) {
                    mkdir($Photo_Gallery_folder, 0777);
                }
                $baseurl = str_replace('_normal.', '_400x400.', $picture_img);
                $url = $fbid.'.jpg';
                $image_name = copyRemoteFile($baseurl, $Photo_Gallery_folder.$url);

                if (is_file($Photo_Gallery_folder.$url)) {
                    [$width, $height, $type, $attr] = getimagesize($Photo_Gallery_folder.$url);
                    if ($width < $height) {
                        $final_width = $width;
                    } else {
                        $final_width = $height;
                    }
                    $img->load($Photo_Gallery_folder.$url)->crop(0, 0, $final_width, $final_width)->save($Photo_Gallery_folder.$url);
                    $imgname = $UPLOAD_OBJ->img_data_upload($Photo_Gallery_folder, $url, $Photo_Gallery_folder, $tconfig['tsite_upload_images_member_size1'], $tconfig['tsite_upload_images_member_size2'], $tconfig['tsite_upload_images_member_size3'], '');
                }

                $sql = "UPDATE register_user set vFbId='".$fbid."', vImgName='".$imgname."',eGender='".$db_user[0]['eGender']."',eSignUpType = 'Twitter' WHERE iUserId='".$db_user[0]['iUserId']."'";
                $obj->sql_query($sql);

                $db_sql = "select * from register_user WHERE iUserId='".$db_user[0]['iUserId']."'";
                $db_data = $obj->MySQLSelect($db_sql);

                if (SITE_TYPE === 'Demo') {
                    $login_sql = "insert into member_log (iMemberId, eMemberType, eMemberLoginType,vIP) VALUES ('".$db_user[0]['iUserId']."', 'Passenger', 'WebLogin','".$_SERVER['REMOTE_ADDR']."')";
                    $obj->sql_query($login_sql);
                }

                /* $link = $tconfig["tsite_url"]."profile_rider.php";
                header("Location:".$link);
                exit; */

                return $COMM_MEDIA_OBJ->CheckMobileVerification($db_user[0]['iUserId'], 'rider');
            }
            $sql = "select * from currency where eDefault = 'Yes'";
            $db_curr = $obj->MySQLSelect($sql);

            $curr = $db_curr[0]['vName'];

            $sql = "select * from language_master where eDefault = 'Yes'";
            $db_lang = $obj->MySQLSelect($sql);

            $lang = $db_lang[0]['vCode'];
            $eReftype = 'Rider';
            $refercode = $REFERRAL_OBJ->GenerateReferralCode($eReftype);
            $dRefDate = date('Y-m-d H:i:s');
            $tRegistrationDate = date('Y-m-d H:i:s');
            if ('' !== $femail) {
                $sql = "insert INTO register_user (vFbId,vName, vLastName, vEmail, eStatus,vImgName,eGender,vLang,vCurrencyPassenger,vRefCode,dRefDate,tRegistrationDate) VALUES ('".$fbid."','".$fbfirstname."', '".$fblastname."', '".$femail."', 'Active','','".$fbgender."','".$lang."','".$curr."','".$refercode."','".$dRefDate."','".$tRegistrationDate."')";
                $iUserId = $obj->MySQLInsert($sql);
            } else {
                $sql = "INSERT INTO register_user (vFbId,vName, vLastName, vEmail, eStatus,vImgName,eGender,vLang,vCurrencyPassenger,vRefCode,dRefDate,tRegistrationDate) VALUES ('".$fbid."','".$fbfirstname."', '".$fblastname."', '".$femail."','Active','','".$fbgender."','".$lang."','".$curr."','".$refercode."','".$dRefDate."','".$tRegistrationDate."')";
                $iUserId = $obj->MySQLInsert($sql);
            }

            $db_sql = "select * from register_user WHERE iUserId='".$iUserId."'";
            $db_user = $obj->MySQLSelect($db_sql);

            $type = base64_encode(base64_encode('rider'));
            $id = encrypt($iUserId);
            $newToken = RandomString(32);
            $url = $tconfig['tsite_url'].'reset_password.php?type='.$type.'&id='.$id.'&_token='.$newToken;

            $maildata['EMAIL'] = $femail;
            $maildata['NAME'] = $fbfirstname.' '.$fblastname;
            $maildata['PASSWORD'] = '';
            $maildata['SOCIALNOTES'] = $langage_lbl['LBL_SOCIAL_MEDIA_NOTES1_TXT'].'<br>'.$url.'<br>'.$langage_lbl['LBL_SOCIAL_MEDIA_NOTES2_TXT'];
            $COMM_MEDIA_OBJ->SendMailToMember('MEMBER_REGISTRATION_USER', $maildata);

            $Photo_Gallery_folder = $tconfig['tsite_upload_images_passenger_path'].'/'.$iUserId.'/';

            @unlink($Photo_Gallery_folder.$db_user[0]['vImgName']);
            @unlink($Photo_Gallery_folder.'1_'.$db_user[0]['vImgName']);
            @unlink($Photo_Gallery_folder.'2_'.$db_user[0]['vImgName']);
            @unlink($Photo_Gallery_folder.'3_'.$db_user[0]['vImgName']);
            @unlink($Photo_Gallery_folder.'4_'.$db_user[0]['vImgName']);

            if (!is_dir($Photo_Gallery_folder)) {
                mkdir($Photo_Gallery_folder, 0777);
            }

            $baseurl = str_replace('_normal.', '_400x400.', $picture_img);
            $url = $fbid.'.jpg';
            $image_name = copyRemoteFile($baseurl, $Photo_Gallery_folder.$url);

            if (is_file($Photo_Gallery_folder.$url)) {
                [$width, $height, $type, $attr] = getimagesize($Photo_Gallery_folder.$url);
                if ($width < $height) {
                    $final_width = $width;
                } else {
                    $final_width = $height;
                }
                $img->load($Photo_Gallery_folder.$url)->crop(0, 0, $final_width, $final_width)->save($Photo_Gallery_folder.$url);
                $imgname = $UPLOAD_OBJ->img_data_upload($Photo_Gallery_folder, $url, $Photo_Gallery_folder, $tconfig['tsite_upload_images_member_size1'], $tconfig['tsite_upload_images_member_size2'], $tconfig['tsite_upload_images_member_size3'], '');
            }

            $sql = "UPDATE register_user set  vImgName='".$imgname."',eSignUpType = 'Twitter' WHERE iUserId='".$iUserId."'";
            $obj->sql_query($sql);

            return $COMM_MEDIA_OBJ->CheckMobileVerification($db_user[0]['iUserId'], 'rider');
        }

        if ('' !== $femail || '' !== $fbid) {
            $sql = "SELECT iDriverId,vImage,eGender,vPhone,eStatus FROM register_driver WHERE {$sqll001}";
            $db_user = $obj->MySQLSelect($sql);
        }

        if (count($db_user) > 0) {
            if ('Deleted' === $db_user[0]['eStatus']) {
                $_SESSION['sess_error_social'] = addslashes($langage_lbl['LBL_ACC_DELETE_TXT']);
                if ('UberX' === $APP_TYPE || 'Ride-Delivery-UberX' === $APP_TYPE) {
                    $link = $tconfig['tsite_url'].'provider-login';
                } else {
                    $link = $tconfig['tsite_url'].'driver-login';
                }
                header('Location:'.$link);

                exit;
            }

            $Photo_Gallery_folder = $tconfig['tsite_upload_images_driver_path'].'/'.$db_user[0]['iDriverId'].'/';

            unlink($Photo_Gallery_folder.$db_user[0]['vImage']);
            unlink($Photo_Gallery_folder.'1_'.$db_user[0]['vImage']);
            unlink($Photo_Gallery_folder.'2_'.$db_user[0]['vImage']);
            unlink($Photo_Gallery_folder.'3_'.$db_user[0]['vImage']);
            unlink($Photo_Gallery_folder.'4_'.$db_user[0]['vImage']);

            if (!is_dir($Photo_Gallery_folder)) {
                mkdir($Photo_Gallery_folder, 0777);
            }
            $baseurl = str_replace('_normal.', '_400x400.', $picture_img);
            $url = $fbid.'.jpg';
            $image_name = copyRemoteFile($baseurl, $Photo_Gallery_folder.$url);

            if (is_file($Photo_Gallery_folder.$url)) {
                [$width, $height, $type, $attr] = getimagesize($Photo_Gallery_folder.$url);
                if ($width < $height) {
                    $final_width = $width;
                } else {
                    $final_width = $height;
                }
                $img->load($Photo_Gallery_folder.$url)->crop(0, 0, $final_width, $final_width)->save($Photo_Gallery_folder.$url);
                $imgname = $UPLOAD_OBJ->img_data_upload($Photo_Gallery_folder, $url, $Photo_Gallery_folder, $tconfig['tsite_upload_images_member_size1'], $tconfig['tsite_upload_images_member_size2'], $tconfig['tsite_upload_images_member_size3'], '');
            }

            $sql = "UPDATE register_driver set vFbId='".$fbid."', vImage='".$imgname."',eGender='".$db_user[0]['eGender']."',eSignUpType = 'Twitter' WHERE iDriverId='".$db_user[0]['iDriverId']."'";
            $obj->sql_query($sql);

            if (SITE_TYPE === 'Demo') {
                $login_sql = "insert into member_log (iMemberId, eMemberType, eMemberLoginType,vIP) VALUES ('".$db_user[0]['iDriverId']."', 'Passenger', 'WebLogin','".$_SERVER['REMOTE_ADDR']."')";
                $obj->sql_query($login_sql);
            }

            return $COMM_MEDIA_OBJ->CheckMobileVerification($db_user[0]['iDriverId'], 'driver');
        }

        $sql = "select * from currency where eDefault = 'Yes'";
        $db_curr = $obj->MySQLSelect($sql);
        $curr = $db_curr[0]['vName'];

        $sql = "select * from language_master where eDefault = 'Yes'";
        $db_lang = $obj->MySQLSelect($sql);

        $lang = $db_lang[0]['vCode'];
        $eReftype = 'Driver';
        $refercode = $REFERRAL_OBJ->GenerateReferralCode($eReftype);
        $dRefDate = date('Y-m-d H:i:s');
        $tRegistrationDate = date('Y-m-d H:i:s');
        if ('' !== $femail) {
            $sql = "INSERT INTO register_driver (vFbId,vName, vLastName, vEmail, iCompanyId, eStatus,vImage,eGender,vLang,vCurrencyDriver,vRefCode,dRefDate,tRegistrationDate) VALUES ('".$fbid."','".$fbfirstname."', '".$fblastname."', '".$femail."', '1', 'Inactive','','".$fbgender."','".$lang."','".$curr."','".$refercode."','".$dRefDate."','".$tRegistrationDate."')";
            $iDriverId = $obj->MySQLInsert($sql);
        } else {
            $sql = "INSERT INTO register_driver (vFbId,vName, vLastName, vEmail, iCompanyId, eStatus,vImage,eGender,vLang,vCurrencyDriver,vRefCode,dRefDate,tRegistrationDate) VALUES ('".$fbid."','".$fbfirstname."', '".$fblastname."', '".$femail."', '1', 'Inactive','','".$fbgender."','".$lang."','".$curr."','".$refercode."','".$dRefDate."','".$tRegistrationDate."')";
            $iDriverId = $obj->MySQLInsert($sql);
        }

        if ('UberX' === $APP_TYPE || 'Ride-Delivery-UberX' === $APP_TYPE) {
            $query = "SELECT GROUP_CONCAT(iVehicleTypeId)as countId FROM `vehicle_type` WHERE `eType` = 'UberX'";
            $result = $obj->MySQLSelect($query);

            $Drive_vehicle['iDriverId'] = $iDriverId;
            $Drive_vehicle['iCompanyId'] = '1';
            $Drive_vehicle['iMakeId'] = '3';
            $Drive_vehicle['iModelId'] = '1';
            $Drive_vehicle['iYear'] = date('Y');
            $Drive_vehicle['vLicencePlate'] = 'My Services';
            $Drive_vehicle['eStatus'] = 'Active';
            $Drive_vehicle['eCarX'] = 'Yes';
            $Drive_vehicle['eCarGo'] = 'Yes';
            $Drive_vehicle['eType'] = 'UberX';
            $Drive_vehicle['vCarType'] = $result[0]['countId'];
            $iDriver_VehicleId = $obj->MySQLQueryPerform('driver_vehicle', $Drive_vehicle, 'insert');

            if ('UberX' === $APP_TYPE) {
                $sql = "UPDATE register_driver set iDriverVehicleId='".$iDriver_VehicleId."' WHERE iDriverId='".$iDriverId."'";
                $obj->sql_query($sql);
            }

            /*						if($ALLOW_SERVICE_PROVIDER_AMOUNT == "Yes"){
                                        $sql="select iVehicleTypeId,iVehicleCategoryId,eFareType,fFixedFare,fPricePerHour from vehicle_type where 1=1";
                                        $data_vehicles = $obj->MySQLSelect($sql);

                                        if($data_vehicles[$i]['eFareType'] != "Regular")
                                        {
                                            for($i=0 ; $i < count($data_vehicles); $i++){
                                                $Data_service['iVehicleTypeId'] = $data_vehicles[$i]['iVehicleTypeId'];
                                                $Data_service['iDriverVehicleId'] = $iDriver_VehicleId;

                                                if($data_vehicles[$i]['eFareType'] == "Fixed"){
                                                    $Data_service['fAmount'] = $data_vehicles[$i]['fFixedFare'];
                                                }
                                                else if($data_vehicles[$i]['eFareType'] == "Hourly"){
                                                    $Data_service['fAmount'] = $data_vehicles[$i]['fPricePerHour'];
                                                }
                                                $data_service_amount = $obj->MySQLQueryPerform('service_pro_amount',$Data_service,'insert');
                                            }
                                        }
                                    }*/

            if ('Ride-Delivery-UberX' === $APP_TYPE) {
                if (SITE_TYPE === 'Demo') {
                    $query = "SELECT GROUP_CONCAT(iVehicleTypeId)as countId FROM `vehicle_type` WHERE (`eType` = 'Ride' OR `eType` = 'Deliver')";
                    $result = $obj->MySQLSelect($query);
                    $Drive_vehicle_Ride['iDriverId'] = $iDriverId;
                    $Drive_vehicle_Ride['iCompanyId'] = '1';
                    $Drive_vehicle_Ride['iMakeId'] = '5';
                    $Drive_vehicle_Ride['iModelId'] = '18';
                    $Drive_vehicle_Ride['iYear'] = '2014';
                    $Drive_vehicle_Ride['vLicencePlate'] = 'CK201';
                    $Drive_vehicle_Ride['eStatus'] = 'Active';
                    $Drive_vehicle_Ride['eCarX'] = 'Yes';
                    $Drive_vehicle_Ride['eCarGo'] = 'Yes';
                    $Drive_vehicle_Ride['eType'] = 'Ride';
                    $Drive_vehicle_Ride['vCarType'] = $result[0]['countId'];
                    $iDriver_VehicleId = $obj->MySQLQueryPerform('driver_vehicle', $Drive_vehicle_Ride, 'insert');
                    $sql = "UPDATE register_driver set iDriverVehicleId='".$iDriver_VehicleId."' WHERE iDriverId='".$iDriverId."'";
                    $obj->sql_query($sql);

                    $query = "SELECT GROUP_CONCAT(iVehicleTypeId)as countId FROM `vehicle_type` WHERE (`eType` = 'Ride' OR `eType` = 'Deliver')";
                    $result = $obj->MySQLSelect($query);
                    $Drive_vehicle_Deliver['iDriverId'] = $iDriverId;
                    $Drive_vehicle_Deliver['iCompanyId'] = '1';
                    $Drive_vehicle_Deliver['iMakeId'] = '5';
                    $Drive_vehicle_Deliver['iModelId'] = '18';
                    $Drive_vehicle_Deliver['iYear'] = '2014';
                    $Drive_vehicle_Deliver['vLicencePlate'] = 'CK201';
                    $Drive_vehicle_Deliver['eStatus'] = 'Active';
                    $Drive_vehicle_Deliver['eCarX'] = 'Yes';
                    $Drive_vehicle_Deliver['eCarGo'] = 'Yes';
                    $Drive_vehicle_Deliver['eType'] = 'Delivery';
                    $Drive_vehicle_Deliver['vCarType'] = $result[0]['countId'];
                    $iDriver_VehicleId = $obj->MySQLQueryPerform('driver_vehicle', $Drive_vehicle_Deliver, 'insert');
                }
            }
        } else {
            if (SITE_TYPE === 'Demo') {
                if ('Delivery' === $APP_TYPE) {
                    $app_type = 'Deliver';
                } else {
                    $app_type = $APP_TYPE;
                }
                $query = "SELECT GROUP_CONCAT(iVehicleTypeId)as countId FROM `vehicle_type` WHERE `eType` = '".$app_type."'";
                $result = $obj->MySQLSelect($query);
                $Drive_vehicle['iDriverId'] = $iDriverId;
                $Drive_vehicle['iCompanyId'] = '1';
                $Drive_vehicle['iMakeId'] = '5';
                $Drive_vehicle['iModelId'] = '18';
                $Drive_vehicle['iYear'] = '2014';
                $Drive_vehicle['vLicencePlate'] = 'CK201';
                $Drive_vehicle['eStatus'] = 'Active';
                $Drive_vehicle['eCarX'] = 'Yes';
                $Drive_vehicle['eCarGo'] = 'Yes';
                $Drive_vehicle['eType'] = $app_type;
                $Drive_vehicle['vCarType'] = $result[0]['countId'];
                $iDriver_VehicleId = $obj->MySQLQueryPerform('driver_vehicle', $Drive_vehicle, 'insert');
                $sql = "UPDATE register_driver set iDriverVehicleId='".$iDriver_VehicleId."' WHERE iDriverId='".$iDriverId."'";
                $obj->sql_query($sql);
            }
        }

        $type = base64_encode(base64_encode('driver'));
        $id = encrypt($iDriverId);
        $newToken = RandomString(32);
        $url = $tconfig['tsite_url'].'reset_password.php?type='.$type.'&id='.$id.'&_token='.$newToken;

        $maildata['EMAIL'] = $femail;
        $maildata['NAME'] = $fbfirstname.' '.$fblastname;
        $maildata['PASSWORD'] = '';
        $maildata['SOCIALNOTES'] = $langage_lbl['LBL_SOCIAL_MEDIA_NOTES1_TXT'].'<br>'.$url.'<br>'.$langage_lbl['LBL_SOCIAL_MEDIA_NOTES2_TXT'];

        $COMM_MEDIA_OBJ->SendMailToMember('DRIVER_REGISTRATION_USER', $maildata);

        $Photo_Gallery_folder = $tconfig['tsite_upload_images_driver_path'].'/'.$iDriverId.'/';

        $db_sql = "select * from register_driver WHERE iDriverId='".$iDriverId."'";
        $db_user = $obj->MySQLSelect($db_sql);

        @unlink($Photo_Gallery_folder.$db_user[0]['vImage']);
        @unlink($Photo_Gallery_folder.'1_'.$db_user[0]['vImage']);
        @unlink($Photo_Gallery_folder.'2_'.$db_user[0]['vImage']);
        @unlink($Photo_Gallery_folder.'3_'.$db_user[0]['vImage']);
        @unlink($Photo_Gallery_folder.'4_'.$db_user[0]['vImage']);

        if (!is_dir($Photo_Gallery_folder)) {
            mkdir($Photo_Gallery_folder, 0777);
        }

        $baseurl = str_replace('_normal.', '_400x400.', $picture_img);
        $url = $fbid.'.jpg';
        $image_name = copyRemoteFile($baseurl, $Photo_Gallery_folder.$url);

        if (is_file($Photo_Gallery_folder.$url)) {
            [$width, $height, $type, $attr] = getimagesize($Photo_Gallery_folder.$url);
            if ($width < $height) {
                $final_width = $width;
            } else {
                $final_width = $height;
            }
            $img->load($Photo_Gallery_folder.$url)->crop(0, 0, $final_width, $final_width)->save($Photo_Gallery_folder.$url);
            $imgname = $UPLOAD_OBJ->img_data_upload($Photo_Gallery_folder, $url, $Photo_Gallery_folder, $tconfig['tsite_upload_images_member_size1'], $tconfig['tsite_upload_images_member_size2'], $tconfig['tsite_upload_images_member_size3'], '');
        }

        $sql = "UPDATE register_driver set vImage='".$imgname."',eSignUpType = 'Twitter' WHERE iDriverId='".$iDriverId."'";
        $obj->sql_query($sql);

        $db_sql = "select * from register_driver WHERE iDriverId='".$iDriverId."'";
        $db_data = $obj->MySQLSelect($db_sql);

        return $COMM_MEDIA_OBJ->CheckMobileVerification($db_user[0]['iDriverId'], 'driver');
    }
    session_unset();
    session_destroy();
    header('Location: index.php');
}
