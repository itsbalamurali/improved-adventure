<?php

include_once("common.php");

if(SITE_TYPE=='Demo') {

    header("Location:" . $tconfig["tsite_url"] . "sign-up_rider.php?error=1");

    exit;

}



if (isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])) {



    /* Check Recaptch is valied or not */

    $valiedRecaptch = isRecaptchaValid($GOOGLE_CAPTCHA_SECRET_KEY, $_POST['g-recaptcha-response']);

    if ($valiedRecaptch) {

        if ($_POST) {

            if(!empty($_POST['vEmailo'])) $_POST['vEmail'] = $_POST['vEmailo'];

            $table_name = "organization";

            if ($ENABLE_EMAIL_OPTIONAL != "Yes") {

                if(!empty($_POST['vEmailo']) && ($THEME_OBJ->isXThemeActive() == 'Yes')) {

                    $msg= checkDuplicateFront('vEmailo','organization',Array('vEmailo'),$tconfig["tsite_url"] . "sign-up-organization.php?error=1&var_msg=Email already Exists", "Email already Exists","" ,"");

                } else {

                    $msg = checkDuplicateFront('vEmail', 'organization', Array('vEmail'), $tconfig["tsite_url"] . "sign-up-organization.php?error=1&var_msg=Email already Exists", "Email already Exists", "", "");

                }

            }

            $eSystem = "";

            $checPhoneExist = checkMemberDataInfo($_POST['vPhone'], "", $_POST['user_type'], $_POST['vCountry'], "",$eSystem);

            //echo "<pre>";

            //print_r($checPhoneExist);exit;

            if ($checPhoneExist['status'] == 0) {

		$_SESSION['postDetail'] = $_REQUEST;

		header("Location:" . $tconfig["tsite_url"] . "sign-up-organization.php?error=1&var_msg=". $langage_lbl['LBL_PHONE_EXIST_MSG']);

		exit;

            } else if ($checPhoneExist['status'] == 2) {

		header("Location:" . $tconfig["tsite_url"] . "sign-up-organization.php?error=1&var_msg=" . $langage_lbl['LBL_INVALID_MEMBER_USER_COUNTRY_EMAIL_TXT']);

		exit;

            }

            $Data['vLang'] = $_SESSION['sess_lang'];

            $Data['vPassword'] = encrypt_bycrypt($_REQUEST['vPassword']);

            $Data['vEmail'] = $_POST['vEmail'];

            $Data['vPhone'] = $_POST['vPhone'];

            $Data['vCaddress'] = $_POST['vCaddress'];

            $Data['vCity'] = $_POST['vCity'];

            $Data['vCountry'] = $_POST['vCountry'];

            $Data['vState'] = $_POST['vState'];

            $Data['vZip'] = $_POST['vZip'];

            $Data['vCode'] = $_POST['vCode'];

            $Data['vBackCheck'] = $_POST['vBackCheck'];

            $Data['vInviteCode'] = $_POST['vInviteCode'];

            $Data['vCompany'] = $_POST['vCompany'];

            $Data['vCurrency'] = $_POST['vCurrency'];

            $Data['iUserProfileMasterId'] = $_POST['iUserProfileMasterId'];

            $Data['tRegistrationDate'] = Date('Y-m-d H:i:s');

            $Data['eStatus'] = 'Inactive';

            $Data['ePaymentBy'] = 'Passenger';



            $csql = "SELECT eZeroAllowed,vCountryCode FROM `country` WHERE vPhoneCode = '" . $Data['vCode'] . "'";

            $CountryData = $obj->MySQLSelect($csql);

            $eZeroAllowed = $CountryData[0]['eZeroAllowed'];



            if ($eZeroAllowed == 'Yes') {

                $Data['vPhone'] = $Data['vPhone'];

            } else {

                $first = substr($Data['vPhone'], 0, 1);



                if ($first == "0") {

                    $Data['vPhone'] = substr($Data['vPhone'], 1);

                }

            }

            $eSystem = "";

            if($ENABLE_EMAIL_OPTIONAL != "Yes") {

                $checkValid = checkMemberDataInfo($_POST['vEmail'], "", $_POST['user_type'], $_POST['vCountry'],"",$eSystem);

            } else {

                $checkValid = checkMemberDataInfo($_POST['vPhone'], "", $_POST['user_type'], $_POST['vCountry'], "",$eSystem);

            }

         

            if ($checkValid['status'] == 1) {

                $id = $obj->MySQLQueryPerform('organization', $Data, 'insert');

                createUserLog('Organization', 'No', $id, 'Web','WebLogin','SignUp');

                if ($id != "") {

                    $_SESSION['sess_iUserId'] = $id;



                    $_SESSION['sess_iOrganizationId'] = $id;

                    $_SESSION["sess_vName"] = $Data['vCompany'];



                    $_SESSION["sess_company"] = $Data['vCompany'];

                    $_SESSION["sess_vEmail"] = $Data['vEmail'];

                    $_SESSION["sess_user"] = 'organization';

                    $_SESSION["sess_new"] = 1;

                    if(!empty($_SESSION["sess_vEmail"])) {  

                        $maildata['EMAIL'] = $_SESSION["sess_vEmail"];

                    } else {

                        $maildata['EMAIL'] = "-";

                    }

                    $maildata['NAME'] = $_SESSION["sess_vName"];

                    if($Data['vPhone'] != ''){

                        $maildata['PHONE'] = $langage_lbl['LBL_PHONE'] . ": " .$Data['vCode']." ".$Data['vPhone'];

                    } else {

                        $maildata['PHONE'] = "-";

                    }



                    $maildata['PASSWORD'] = $langage_lbl['LBL_PASSWORD'] . ": " . $_REQUEST['vPassword'];

                    $maildata['SOCIALNOTES'] = '';

                   /* print_r($maildata);die;*/

                    $COMM_MEDIA_OBJ->SendMailToMember("ORGANIZATION_REGISTRATION_ADMIN", $maildata);

                    $COMM_MEDIA_OBJ->SendMailToMember("ORGANIZATION_REGISTRATION_USER", $maildata);



                    //User login log added by Rs start

                    createUserLog('Organization', 'Yes', $id, 'Web');

                    header("Location:organization-profile.php");

		exit;

                }

            } else {

		$_SESSION['postDetail'] = $_REQUEST;

		header("Location:" . $tconfig["tsite_url"] . "sign-up-organization.php?error=1&var_msg=" . $langage_lbl['LBL_INVALID_MEMBER_USER_COUNTRY_EMAIL_TXT']);

		exit;

            }

        }

    } else {

	$_SESSION['postDetail'] = $_REQUEST;

          header("Location:" . $tconfig["tsite_url"] . "sign-up-organization.php?error=1&var_msg=" . $langage_lbl['LBL_CAPTCHA_MATCH_MSG']);

	exit;

    }

    /* }

      else

      {

      $_SESSION['postDetail'] = $_REQUEST;

      header("Location:".$tconfig["tsite_url"]."sign-up-organization.php?error=1&var_msg=Captcha did not match.");

      exit;

      } */

} else {

	$_SESSION['postDetail'] = $_REQUEST;

	header("Location:" . $tconfig["tsite_url"] . "sign-up-organization.php?error=1&var_msg=Please check reCAPTCHA box.");

	exit;

}

?>

