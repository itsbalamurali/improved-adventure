<?php
include_once 'common.php';

//added by SP for cubex changes on 07-11-2019
if ($THEME_OBJ->isXThemeActive() == 'Yes') {
    $_REQUEST['type'] = 'organization';
    include_once("cx-sign-in.php");
    //header("location: cx-sign-in.php?type=organization"); //here header location used bc type pass so include not worked
    exit;
}

$AUTH_OBJ->AuthMemberRedirect();
// echo "<pre>";print_r($_GET);

$action = isset($_GET['action']) ? $_GET['action'] : '';
//$iscompany = isset($_GET['iscompany'])?$_GET['iscompany']:'0';
//$type = "Driver";
$type = "organization";

/* if($iscompany == "1"){
  $_SESSION['postDetail']['user_type'] = "company";
  $type = "Company";
  } */
$countryList = get_value('country', 'vCountryCode,vPhoneCode,vCountry', 'eStatus', 'Active', '', '');

//$defaultcountryDataArr = get_value('configurations', 'vValue', 'vName', 'DEFAULT_COUNTRY_CODE_WEB', '', '');


$sql = "SELECT c.vValue,co.vPhoneCode from configurations as c LEFT JOIN country as co on co.vCountryCode=c.vValue where vName = 'DEFAULT_COUNTRY_CODE_WEB'";
$defaultcountryDataArr = $obj->MySQLSelect($sql);

$forpsw = isset($_REQUEST['forpsw']) ? $_REQUEST['forpsw'] : '';
$forgetPWd = isset($_REQUEST['forgetPWd']) ? $_REQUEST['forgetPWd'] : '';

$depart = '';
if (isset($_REQUEST['depart'])) {
    $_SESSION['sess_depart'] = $_REQUEST['depart'];
    $depart = $_SESSION['sess_depart'];
} else {
    if (isset($_REQUEST['depart'])) {
        unset($_SESSION['sess_depart']);
    }
}

$err_msg = "";
if (isset($_SESSION['sess_error_social'])) {
    $err_msg = $_SESSION['sess_error_social'];
    // echo "<pre>";print_r($_SESSION);

    unset($_SESSION['sess_error_social']);
    unset($_SESSION['fb_user']);   //facebook
    unset($_SESSION['oauth_token']);  //twitter
    unset($_SESSION['oauth_token_secret']); //twitter
    unset($_SESSION['access_token']);  //google
    // echo "<pre>";print_r($_SESSION);exit;
}

if ($action == 'driver' && $iscompany != "1") {
    $meta_arr = $STATIC_PAGE_OBJ->FetchSeoSetting(9);
} elseif ($action == 'rider') {
    $meta_arr = $STATIC_PAGE_OBJ->FetchSeoSetting(8);
} elseif ($action == 'driver' && $iscompany == "1") {
    $meta_arr = $STATIC_PAGE_OBJ->FetchSeoSetting(10);
}
if ($host_system == "carwash") {
    $rider_email = "user@demo.com";
    $driver_email = "washer@demo.com";
} elseif ($host_system == "beautician") {
    $rider_email = "user@demo.com";
    $driver_email = "beautician@demo.com";
} elseif ($host_system == "massage4") {
    if ($iscompany == "1") {
        $driver_email = "company@gmail.com";
    } else {
        $driver_email = "massager@demo.com";
    }
    $rider_email = "user@demo.com";
} elseif ($host_system == "doctor4") {
    if ($iscompany == "1") {
        $driver_email = "company@gmail.com";
    } else {
        $driver_email = "doctor@demo.com";
    }
    $rider_email = "patient@demo.com";
} elseif ($host_system == "beautician4") {
    if ($iscompany == "1") {
        $driver_email = "company@gmail.com";
    } else {
        $driver_email = "beautician@demo.com";
    }
    $rider_email = "user@demo.com";
} elseif ($host_system == "carwash4") {
    if ($iscompany == "1") {
        $driver_email = "company@gmail.com";
    } else {
        $driver_email = "carwasher@demo.com";
    }
    $rider_email = "user@demo.com";
} elseif ($host_system == "dogwalking4") {
    if ($iscompany == "1") {
        $driver_email = "company@gmail.com";
    } else {
        $driver_email = "dogwalker@demo.com";
    }
    $rider_email = "user@demo.com";
} elseif ($host_system == "towtruck4") {
    if ($iscompany == "1") {
        $driver_email = "company@gmail.com";
    } else {
        $driver_email = "provider@demo.com";
    }
    $rider_email = "user@demo.com";
} elseif ($host_system == "tutors") {
    $rider_email = "student@demo.com";
    $driver_email = "tutor@demo.com";
} elseif ($host_system == "ufxforall") {
    $rider_email = "provider@demo.com";
    $driver_email = "user@demo.com";
} elseif ($host_system == "ufxforall4" || $domain == "cubejek") {
    if ($iscompany == "1") {
        $driver_email = "company@gmail.com";
    } else {
        $driver_email = "provider@demo.com";
    }
    $rider_email = "user@demo.com";
} else {
    $rider_email = "rider@gmail.com";
    if ($iscompany == "1") {
        $driver_email = "company@gmail.com";
    } else {
        $driver_email = "driver@gmail.com";
    }
}
$SITEPATH = $tconfig['tsite_url'];  
?>
<!DOCTYPE html>
<html lang="en" dir="<?= (isset($_SESSION['eDirectionCode']) && $_SESSION['eDirectionCode'] != "") ? $_SESSION['eDirectionCode'] : 'ltr'; ?>">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width,initial-scale=1">
        <!--   <title><?= $SITE_NAME ?> | Login Page</title>-->
        <title><?php echo $meta_arr['meta_title']; ?></title>
        <!-- Default Top Script and css -->
        <?php include_once("top/top_script.php"); ?>
        <script type="text/javascript" src="<?php echo $SITEPATH; ?>assets/js/add_country_code_dropdown.js"></script>
        <!-- End: Default Top Script and css-->
    </head>
    <body>
        <!-- home page -->
        <div id="main-uber-page">
            <!-- Left Menu -->
            <?php include_once("top/left_menu.php"); ?>
            <!-- End: Left Menu-->
            <!-- Top Menu -->
            <?php include_once("top/header_topbar.php"); ?>
            <!-- End: Top Menu-->
            <!-- contact page-->
            <div class="page-contant">
                <div class="page-contant-inner">
                    <h2 class="header-page" id="label-id"><?= $langage_lbl['LBL_SIGN_IN_TXT']; ?>
                        <? if (SITE_TYPE == 'Demo') { ?>
                            <p><?= $langage_lbl['LBL_SINCE_IT_IS_DEMO']; ?></p>
                        <? } ?>
                    </h2>
                    <!-- login in page -->
                    <div class="login-form">

                        <div class="login-err">
                            <p id="errmsg" style="display:none;" class="text-muted btn-block btn btn-danger btn-rect error-login-v"></p>
                            <p style="display:none;" class="btn-block btn btn-rect btn-success error-login-v" id="success" ></p>
                        </div>

                        <!-- <?
                        if ($action == 'rider') {
                            $action_url = 'mytrip.php';
                        } else if ($action == 'driver' && $iscompany != "1") {
                            $action_url = 'profile.php';
                        } else {
                            $action_url = 'dashboard.php';
                        }
                        ?>
                        -->
                        <!-- 	<div class="login-form-left"> <form action="<?= ($action == 'rider') ? 'mytrip.php' : 'profile.php'; ?>" class="form-signin" method = "post" id="login_box" onSubmit="return chkValid('<?= $action ?>');" >	 -->


                                        <!-- <div class="login-form-left"> <form action="<?= $action_url ?>" class="form-signin" method ="post" id="login_box" onSubmit="return chkValid('<?= $action ?>','<?= $iscompany ?>');" >	 
                        -->
                        <div class="login-form-left"> <form action="<?= $action_url ?>" class="form-signin" method ="post" id="login_box" onSubmit="return chkValid('<?= $action ?>');" >	 

                                <b>
                                    <input type="hidden" name="action" value="<? echo $action ?>"/>
                                    <input type="hidden" name="type_usr" value="<? echo $type ?>"/>
                                    <input type="hidden" name="type" id="type" value="signIn"/>

                                    <label><?= $langage_lbl['LBL_EMAIL_MOBILE_NO_TXT_MSG']; ?></label>
                                    <div class="clearfix"></div>
                                    <div class="phone-field">
                                        <input name="vEmail" type="text" placeholder="<?= $langage_lbl['LBL_ENTER_EMAIL_ID_OR_MOBILE_TXT']; ?>" class="login-input" id="vEmail" value="" required />
                                    <div class="clearfix"></div>
                                 </div>
                             </b>
                                <div class="relative_ele">
                                    <b>
                                        <label><?= $langage_lbl['LBL_COMPANY_DRIVER_PASSWORD']; ?></label>

                                        <input name="vPassword" type="password" placeholder="<?= $langage_lbl['LBL_PASSWORD_LBL_TXT']; ?>" class="login-input" id="vPassword" value="" required />
                                        <!--<button type="button" onclick="showHidePassword('pass')" id="eye"><img src="assets/img/eye.png" alt="eye"/></button>-->
                                    </b>
                                </div>
                                <b>
                                    <input type="submit" class="submit-but" value="<?= $langage_lbl['LBL_SIGN_IN_TXT']; ?>" />
                                    <a onClick="change_heading('forgot')"><?= $langage_lbl['LBL_FORGET_PASS_TXT']; ?></a>
                                </b> </form>

                            <form action="" method="post" class="form-signin" id="frmforget" onSubmit="return forgotPass();" style="display: none;">

                                <input type="hidden" name="action" id="action" value="<?= $action ?>">
                                <b>
                                    <!-- <div class="countryPhoneSelectWrapper" style="display:none;">
                                      <select name="phoneCode" id="phoneCode" class="countryPhoneSelect form-control">
                                      <?php 
                                      foreach($countryList as $Rows){ ?>
                                        <option <?php if($Rows['vPhoneCode'] == $defaultcountryDataArr[0]['vPhoneCode']) { ?>selected="selected"<?php } ?> value="<?php echo $Rows['vPhoneCode'] ?>" data-code="+<?php echo $Rows['vPhoneCode']?>" data-country="<?php echo $Rows['vCountryCode'] ?>"><?php echo $Rows['vCountry'] . ' (+' . $Rows['vPhoneCode'] .') '; ?></option>
                                      <?php }
                                      ?>
                                      </select>
                                      <div class="countryPhoneSelectChoice">
                                        <span class="countryCode"><?= $defaultcountryDataArr[0]['vValue']?></span>
                                        <span class="phoneCode">+<?= $defaultcountryDataArr[0]['vPhoneCode']?></span>
                                      </div>
                                  </div> -->
                                    <label><?= $langage_lbl['LBL_EMAIL_MOBILE_NO_TXT_MSG']; ?></label>
                                    <div class="clearfix"></div>
                                    <div class="phone-field">
                                    <input name="femail" type="text" placeholder="<?= $langage_lbl['LBL_EMAIL_MOBILE_NO_TXT_MSG']; ?>" class="login-input" id="femail" value="" required />
                                    </div>
                                </b>
                                <b>
                                    <input type="submit" class="submit-but" value="<?= $langage_lbl['LBL_Recover_Password']; ?>" />
                                    <a onClick="change_heading('login')"><?= $langage_lbl['LBL_LOGIN']; ?></a>
                                </b>	 
                            </form>	

                        </div>					

                        <div class="login-form-right login-form-right1">
                            <?php
                            if ($APP_TYPE == 'Ride-Delivery-UberX' || $APP_TYPE == 'UberX') {
                                $sign_up_rider = $cjSignUpUser;
                            } else {
                                $sign_up_rider = $cjSignUpRider;
                            }
                            ?>

                            <h3><?= $langage_lbl['LBL_DONT_HAVE_ACCOUNT']; ?></h3>

                            <!-- <? if ($iscompany != "1") { ?>
                                            <div class="login-form-right1-inner">
                                                    <span><a class="company" href="<?= ($action == 'rider') ? $sign_up_rider : $cjSignupCompany; ?>">
                                <? echo $langage_lbl['LBL_LOGIN_NEW_SIGN_UP']; ?>
                                                            </a></span>  
                                            </div>
                            <? } ?> -->
                            <div class="login-form-right1-inner">
                            <!-- 	<span>
                                            <a class="company" href="<?= ($action == 'rider') ? $sign_up_rider : $cjSignupCompany; ?>"><?
                                if (($action == 'driver' && $iscompany == "1")) {
                                    echo $langage_lbl['LBL_LOGIN_NEW_SIGN_UP_COMPANY'];
                                } else {
                                    echo $langage_lbl['LBL_LOGIN_NEW_SIGN_UP'];
                                }
                                ?>
                                            </a>
                                    </span>   -->

                                <span>
                                    <a class="company-av" href="<?= $cjSignupOrganization; ?>">
                                        <?= $langage_lbl['LBL_LOGIN_NEW_SIGN_UP']; ?>
                                    </a>
                                </span>   

                            </div>

                            <!-- <?
                            if (ONLYDELIVERALL == 'Yes' || DELIVERALL == 'Yes') {
                                if (($action == 'driver' && $iscompany == "1")) {
                                    ?>
                                                                            <div class="login-form-right1-inner-new">	
                                                                                    <span><a class="company" href="sign-up-restaurant"><?= $langage_lbl['LBL_LOGIN_NEW_SIGN_UP_STORE']; ?></a></span> 
                                                                            </div>
                                    <?
                                }
                            }
                            ?> -->

                            <? if ($iscompany == "0") { ?>
                                <div class="login-form-right1-inner">
                                        <!--span class="fb-login"><a href="facebook"><img alt="" src="assets/img/reg-fb.jpg"><? //=$langage_lbl['LBL_SIGN_UP_WITH_FACEBOOK'];   ?></a></span-->

                                                        <!--<span class="login-socials">
                                                                <a href="facebook/<?= $action ?>" class="fa fa-facebook"></a>
                                                                <a href="twitter/<?= $action ?>" class="fa fa-twitter"></a>
                                                                <a href="google/<?= $action ?>" class="fa fa-google"></a>
                                                        </span>-->
                                    <?php
                                    if ($action == 'driver') {
                                        if ($DRIVER_TWITTER_LOGIN == "Yes" || $DRIVER_GOOGLE_LOGIN == "Yes" || $DRIVER_FACEBOOK_LOGIN == "Yes") {
                                            ?>
                                            <h3><?= $langage_lbl['LBL_REGISTER_WITH_ONE_CLICK']; ?></h3>
                                        <?php } ?>
                                        <span class="login-socials">
                                            <?php if ($DRIVER_FACEBOOK_LOGIN == "Yes") { ?>						
                                                <a href="facebook/<?= $action ?>" class="fa fa-facebook"></a>
                                                <?php
                                            }
                                            if ($DRIVER_TWITTER_LOGIN == "Yes") {
                                                ?>

                                                <a href="twitter/<?= $action ?>" class="fa fa-twitter"></a>
                                            <?php } if ($DRIVER_GOOGLE_LOGIN == "Yes") { ?>

                                                <a href="google/<?= $action ?>" class="fa fa-google"></a>
                                            <?php } ?>

                                        </span>
                                        <?php
                                    }
                                    if ($action == 'rider') {
                                        if ($PASSENGER_FACEBOOK_LOGIN == "Yes" || $PASSENGER_TWITTER_LOGIN == "Yes" || $PASSENGER_GOOGLE_LOGIN == "Yes") {
                                            ?>
                                            <h3><?= $langage_lbl['LBL_REGISTER_WITH_ONE_CLICK']; ?></h3>
                                        <?php } ?>
                                        <span class="login-socials">
                                            <?php if ($PASSENGER_FACEBOOK_LOGIN == "Yes") { ?>

                                                <a href="facebook-rider/<?= $action ?>" class="fa fa-facebook"></a>
                                                <?php
                                            }
                                            if ($PASSENGER_TWITTER_LOGIN == "Yes") {
                                                ?>

                                                <a href="twitter/<?= $action ?>" class="fa fa-twitter"></a>
                                            <?php } if ($PASSENGER_GOOGLE_LOGIN == "Yes") { ?>

                                                <a href="google/<?= $action ?>" class="fa fa-google"></a>
                                            <?php } ?>

                                        </span>
                                    <?php } ?>

                                </div>
                            <? } ?>
                        </div>   
                    </div>

                    <div style="clear:both;"></div>
                    <?php
                    if (SITE_TYPE == 'Demo') {
                        if ($action == 'rider') {
                            ?>

                            <div class="text-center" style="text-align:left; display: none;">
                                <? if ($host_system == "carwash") { ?>
                                    <h4>
                                        <b>Note :</b><br /> 
                                        - If you have registered as a new user, use your registered Email Id and Password to view the detail of your Jobs.<br />
                                    </h4>
                                    To view the Standard Features of the Apps use below access detail :<br /><br />
                                    <p>
                                        <b>Rider : </b><br />
                                        Username: user@demo.com<br />
                                        Password: 123456
                                    </p>
                                <? } elseif ($host_system == "beautician" || $host_system == "beautician4" || $host_system == "carwash4" || $host_system == "dogwalking4" || $host_system == "towtruck4" || $host_system == "massage4" || $host_system == "ufxforall4" || $domain == "cubejek") { ?>
                                    <h4>
                                        <b>Note :</b><br /> 
                                        - If you have registered as a new user, use your registered Email Id and Password to view the detail of your Jobs.<br />
                                    </h4>
                                    To view the Standard Features of the Apps use below access detail :<br /><br />
                                    <p>
                                        <b>User : </b><br />
                                        Username: user@demo.com<br />
                                        Password: 123456
                                    </p>
                                <? } elseif ($host_system == "tutors") { ?>
                                    <h4>
                                        <b>Note :</b><br /> 
                                        - If you have registered as a new student, use your registered Email Id and Password to view the detail of your Jobs.<br />
                                    </h4>
                                    To view the Standard Features of the Apps use below access detail :<br /><br />
                                    <p>
                                        <b>Student : </b><br />
                                        Username: student@demo.com<br />
                                        Password: 123456
                                    </p>
                                <? } elseif ($host_system == "doctor4") { ?>
                                    <h4>
                                        <b>Note :</b><br /> 
                                        - If you have registered as a new patient, use your registered Email Id and Password to view the detail of your Appointment.<br />
                                    </h4>
                                    To view the Standard Features of the Apps use below access detail :<br /><br />
                                    <p>
                                        <b>Doctor : </b><br />
                                        Username: patient@demo.com<br />
                                        Password: 123456
                                    </p>
                                <? } else { ?>
                                    <h4>
                                        <b>Note :</b><br /> 
                                        - If you have registered as a new Rider, use your registered Email Id and Password to view the detail of your Rides.<br />
                                    </h4>
                                    To view the Standard Features of the Apps use below access detail :<br /><br />
                                    <p>
                                        <b>Rider : </b><br />
                                        Username: rider@gmail.com<br />
                                        Password: 123456
                                    </p>

                                <? } ?>
                                                                <!--<h4 ><?= $langage_lbl['LBL_PLEASE_USE_BELOW']; ?> </h4>
                                                                                <h5>
                                                                                        <p><?= $langage_lbl['LBL_IF_YOU_HAVE_REGISTER']; ?></p>
                                                                                        <p><b><?= $langage_lbl['LBL_USER_NAME_LBL_TXT']; ?></b>: <?= $langage_lbl['LBL_USERNAME']; ?></p> 
                                                                                        <p><b><?= $langage_lbl['LBL_PASSWORD_LBL_TXT']; ?></b>: <?= $langage_lbl['LBL_PASSWORD']; ?> </p>
                                                                                </h5>
                                -->
                            </div>
                            <?php
                        } else {
                            ?>
                            <div class="text-center" style="text-align:left;  display: none;">
                                <? if ($host_system == "carwash") { ?>
                                    <h4>
                                        <b>Note :</b><br /> 
                                        - If you have registered as a new Washer, use your registered Email Id and Password to view the detail of your Jobs.<br />
                                    </h4>
                                    To view the Standard Features of the Apps use below access detail :<br /><br />
                                    <p>
                                        <b>Washer : </b><br />
                                        Username: washer@demo.com<br />
                                        Password: 123456
                                    </p>
                                <? } elseif ($host_system == "beautician" || $host_system == "beautician4") { ?>
                                    <h4>
                                        <b>Note :</b><br /> 
                                        - If you have registered as a new beautician , use your registered Email Id and Password to view the detail of your Jobs.<br />
                                    </h4>
                                    To view the Standard Features of the Apps use below access detail :<br /><br />
                                    <p>
                                        <b>Beautician : </b><br />
                                        Username: beautician@demo.com<br />
                                        Password: 123456
                                    </p>
                                <? } elseif ($host_system == "tutors") { ?>
                                    <h4>
                                        <b>Note :</b><br /> 
                                        - If you have registered as a new Tutor, use your registered Email Id and Password to view the detail of your Jobs.<br />
                                    </h4>
                                    To view the Standard Features of the Apps use below access detail :<br /><br />
                                    <p>
                                        <b>Tutor : </b><br />
                                        Username: tutor@demo.com<br />
                                        Password: 123456
                                    </p>
                                <? } elseif ($host_system == "carwash4") { ?>
                                    <h4>
                                        <b>Note :</b><br /> 
                                        - If you have registered as a new car washer, use your registered Email Id and Password to view the detail of your Jobs.<br />
                                    </h4>
                                    To view the Standard Features of the Apps use below access detail :<br /><br />
                                    <p>
                                        <b>Car Washer : </b><br />
                                        Username: carwasher@demo.com<br />
                                        Password: 123456
                                    </p>			
                                <?php } elseif ($host_system == "doctor4") { ?>
                                    <h4>
                                        <b>Note :</b><br /> 
                                        - If you have registered as a new doctor, use your registered Email Id and Password to view the detail of your Jobs.<br />
                                    </h4>
                                    To view the Standard Features of the Apps use below access detail :<br /><br />
                                    <p>
                                        <b>Doctor : </b><br />
                                        Username: doctor@demo.com<br />
                                        Password: 123456
                                    </p>			
                                <?php } elseif ($host_system == "massage4") { ?>
                                    <h4>
                                        <b>Note :</b><br /> 
                                        - If you have registered as a new massge therapist, use your registered Email Id and Password to view the detail of your Jobs.<br />
                                    </h4>
                                    To view the Standard Features of the Apps use below access detail :<br /><br />
                                    <p>
                                        <b>Massage Therapist : </b><br />
                                        Username: massager@demo.com<br />
                                        Password: 123456
                                    </p>			
                                <?php } elseif ($host_system == "dogwalking4") { ?>
                                    <h4>
                                        <b>Note :</b><br /> 
                                        - If you have registered as a new dog walker, use your registered Email Id and Password to view the detail of your Jobs.<br />
                                    </h4>
                                    To view the Standard Features of the Apps use below access detail :<br /><br />
                                    <p>
                                        <b>Dog Walker : </b><br />
                                        Username: dogwalker@demo.com<br />
                                        Password: 123456
                                    </p>			
                                <?php } elseif ($host_system == "towtruck4") { ?>
                                    <h4>
                                        <b>Note :</b><br /> 
                                        - If you have registered as a new towing driver, use your registered Email Id and Password to view the detail of your Jobs.<br />
                                    </h4>
                                    To view the Standard Features of the Apps use below access detail :<br /><br />
                                    <p>
                                        <b>Dog Walker : </b><br />
                                        Username: provider@demo.com<br />
                                        Password: 123456
                                    </p>			
                                <?php } elseif ($host_system == "ufxforall4" || $domain == "cubejek") { ?>
                                    <h4>
                                        <b>Note :</b><br /> 
                                        - If you have registered as a new provider, use your registered Email Id and Password to view the detail of your Jobs.<br />
                                    </h4>
                                    To view the Standard Features of the Apps use below access detail :<br /><br />
                                    <p>
                                        <b>Provider : </b><br />
                                        Username: provider@demo.com<br />
                                        Password: 123456
                                    </p>			
                                <?php } else { ?>
                                    <h4>
                                        <b>Note :</b><br /> 
                                        - If you have registered as a new Driver, use your registered Email Id and Password to view the detail of your Rides.<br />
                                    </h4>
                                    To view the Standard Features of the Apps use below access detail :<br /><br />
                                    <p>
                                        <b>Driver : </b><br />
                                        Username: driver@gmail.com<br />
                                        Password: 123456
                                    </p>

                                <? } ?>
                                <p>
                                    <br /><b>Company : </b><br />
                                    Username: company@gmail.com<br />
                                    Password: 123456
                                </p>
                                <!--<h4 ><?= $langage_lbl['LBL_PLEASE_USE_BELOW_DRIVER']; ?> </h4>
                                <h5 >
                                        <p><?= $langage_lbl['LBL_IF_YOU_HAVE_REGISTER_DRIVER']; ?></p>
                                        <p><b><?= $langage_lbl['LBL_USER_NAME_LBL_TXT']; ?></b>: <?= $langage_lbl['LBL_USERNAME_DRIVER']; ?></p>
                                        <p><b><?= $langage_lbl['LBL_PASSWORD_LBL_TXT']; ?></b>: <?= $langage_lbl['LBL_PASSWORD']; ?> </p>
                                </h5>
                                <h4 ><?= $langage_lbl['LBL_PLEASE_USE_BELOW_DEMO']; ?></h4>
                                <h5 >
                                        <p><?= $langage_lbl['LBL_IF_YOU_HAVE_REGISTER_COMPANY']; ?></p>
                                        <p><b><?= $langage_lbl['LBL_USER_NAME_LBL_TXT']; ?></b>: <?= $langage_lbl['LBL_USERNAME_COMPANY']; ?></p>
                                        <p><b><?= $langage_lbl['LBL_PASSWORD_LBL_TXT']; ?></b>: <?= $langage_lbl['LBL_PASSWORD']; ?> </p>
                                </h5> -->
                            </div>
                            <?
                        }
                    }
                    ?>

                    <div style="clear:both;"></div>
                </div>
            </div>
            <!-- footer part -->
            <?php include_once('footer/footer_home.php'); ?>
            <!-- footer part end -->
            <!-- -->
            <div style="clear:both;"></div>
        </div>
        <!-- home page end-->
        <!-- Footer Script -->
        <?php include_once('top/footer_script.php'); ?>
        <!-- End: Footer Script -->
        <script>
          getPhoneCodeInTextBox('vEmail','CountryCode');
          // getPhoneCodeInTextBox('vNirmal','vNirmalCode');
          getPhoneCodeInTextBox('femail','CountryCodeForgt');

         </script>
        <script>
<?php if ($forgetPWd == 1) { ?>
                $('#frmforget').show();
                $('#login_box').hide();
                $('#label-id').text("<?= addslashes($langage_lbl['LBL_FORGOR_PASSWORD']); ?>");
<?php } ?>

            function change_heading(type)
            {
                $('.error-login-v').hide();
                if (type == 'forgot') {

                    $('#frmforget').show();
                    $('#login_box').hide();
                    $('#label-id').text("<?= addslashes($langage_lbl['LBL_FORGOR_PASSWORD']); ?>");
                } else {
                    $('#frmforget').hide();
                    $('#login_box').show();
                    $('#label-id').text("<?= addslashes($langage_lbl['LBL_SIGN_IN_TXT']); ?>");
                }
            }
            function chkValid(login_type)
            {

                var id = document.getElementById("vEmail").value;
                var pass = document.getElementById("vPassword").value;
                if (id == '' || pass == '')
                {
                    document.getElementById("errmsg").innerHTML = '<?= addslashes($langage_lbl['LBL_EMAIL_PASS_ERROR_MSG']); ?>';
                    document.getElementById("errmsg").style.display = '';
                    //return false;
                } else
                {
                    var request = $.ajax({
                        type: "POST",
                        url: 'ajax_organization_login_action.php',
                        data: $("#login_box").serialize(),

                        success: function (data)
                        {
                            jsonParseData = JSON.parse(data);
                            login_status = jsonParseData.login_status;
                            eSystem = jsonParseData.eSystem;

                            //console.log(login_status);
                            //return false;

                            //if(data == 1){
                            if (login_status == 1) {
                                document.getElementById("errmsg").innerHTML = '<?= addslashes($langage_lbl['LBL_ACC_DELETE_TXT']); ?>';
                                document.getElementById("errmsg").style.display = '';
                                //return false;
                            }
                            //else if(data == 2){
                            else if (login_status == 2) {

                                document.getElementById("errmsg").style.display = 'none';

                                if (login_type == 'organization'){
                                    <?php if(isset($_COOKIE['login_redirect_url_user'])) { ?>
                                            window.location = '<?php echo $_COOKIE["login_redirect_url_user"] ?>';
                                        <?php } else { ?>
                                        window.location = "organization-profile";
                                    <?php } ?>
                                }

                                /*departType = '<?php echo $depart; ?>';
                                 if(login_type == 'rider' && departType == 'mobi')
                                 window.location = "mobi";*/

                                /*else if(login_type == 'driver')
                                 window.location = "profile";*/
                                /*else if(login_type == 'driver' && iscompany == "1" && eSystem =="DeliverAll")
                                 window.location = "dashboard.php";
                                 else if(login_type == 'driver')
                                 window.location = "profile.php";*/

                                /*	else if(login_type == 'rider')
<? if ($APP_TYPE == 'Ride-Delivery-UberX' || $APP_TYPE == 'UberX') { ?>
                                     window.location = "profile-user";
<? } else { ?>
                                     window.location = "profile-rider";
<? } ?>*/

                                return true; // success registration
                            }
                            //else if(data == 3) {
                            else if (login_status == 3) {
                                document.getElementById("errmsg").innerHTML = '<?= addslashes($langage_lbl['LBL_INVALID_EMAIL_MOBILE_PASS_ERROR_MSG']); ?>';
                                document.getElementById("errmsg").style.display = '';
                                //return false;

                                //}else if(data == 4) {

                            } else if (login_status == 4) {
                                document.getElementById("errmsg").innerHTML = '<?= addslashes($langage_lbl['LBL_ACCOUNT_NOT_ACTIVE_ERROR_MSG']); ?>';
                                document.getElementById("errmsg").style.display = '';
                                //return false;

                            } else if (login_status == 5) {
                                document.getElementById("errmsg").innerHTML = '<?= addslashes($langage_lbl['LBL_INVALID_MEMBER_USER_COUNTRY_EMAIL_TXT']); ?>';
                                document.getElementById("errmsg").style.display = '';
                            } else {
                                document.getElementById("errmsg").innerHTML = '<?= addslashes($langage_lbl['LBL_INVALID_EMAIL_MOBILE_PASS_ERROR_MSG']); ?>';
                                document.getElementById("errmsg").style.display = '';
                                //setTimeout(function() {document.getElementById('errmsg1').style.display='none';},2000);
                                //return false;
                            }
                            if ($('#errmsg').html() != '') {
                                setTimeout(function () {
                                    $('#errmsg').fadeOut();
                                }, 2000);
                            }
                        }
                    });
                    request.fail(function (jqXHR, textStatus) {
                        alert("Request failed: " + textStatus);
                        return false;
                    });
                    return false;
                }
                if ($('#errmsg').html() != '') {
                    setTimeout(function () {
                        $('#errmsg').fadeOut();
                    }, 2000);
                }
            }
            <?php if($ENABLE_EMAIL_OPTIONAL == "Yes") { ?>
              //   $("#phoneCode" ).change(function() {
              //   var fruitCount = $(this).attr('data-code');
              //   var phonecode = $(this).find(':selected').attr('data-code');
              //   var phonecountry = $(this).find(':selected').attr('data-country');
              //   $('.countryCode').text(phonecountry);
              //   $('.phoneCode').text(phonecode);
              // });
              //   $("#femail").keyup(function() {
              //     var inputvalue = $("#femail").val();
              //      if($.isNumeric(inputvalue) && inputvalue!= '') {
              //        $('.countryPhoneSelectWrapper').show(100);
              //        $('#femail').removeClass('emailinput');
              //        $('#femail').addClass('phoneinput');
              //        $('.isEmail').val("No");
              //      }
              //      else {
              //       $('.countryPhoneSelectWrapper').hide(100);
              //       $('#femail').removeClass('phoneinput');
              //       $('#femail').addClass('emailinput');
              //       $('.isEmail').val("Yes");
              //      }
              //   });
            <? } ?>
            function forgotPass()
            {
                $('.error-login-v').hide();
                var site_type = '<? echo SITE_TYPE; ?>';
                var id = document.getElementById("femail").value;
                if (id == '')
                {
                    document.getElementById("errmsg").style.display = '';
                    document.getElementById("errmsg").innerHTML = '<?= addslashes($langage_lbl['LBL_FEILD_EMAIL_ERROR_TXT_IPHONE']); ?>';
                } else {
                    var request = $.ajax({
                        type: "POST",
                        url: 'ajax_fpass_action.php',
                        data: $("#frmforget").serialize(),
                        dataType: 'json',
                        beforeSend: function ()
                        {
                            //alert(id);
                        },
                        success: function (data)
                        {

                            if (data.status == 1)
                            {
                                change_heading('login');
                                document.getElementById("success").innerHTML = data.msg;
                                document.getElementById("success").style.display = '';

                            } else
                            {
                                document.getElementById("errmsg").innerHTML = data.msg;
                                document.getElementById("errmsg").style.display = '';
                            }

                        }
                    });

                    request.fail(function (jqXHR, textStatus) {
                        alert("Request failed: " + textStatus);
                    });


                }
                return false;
            }

            function fbconnect()
            {
                javscript:window.location = 'fbconnect.php';
            }

            $(document).ready(function () {
                var err_msg = '<?= $err_msg ?>';
                // alert(err_msg);
                if (err_msg != "") {
                    document.getElementById("errmsg").innerHTML = err_msg;
                    document.getElementById("errmsg").style.display = '';
                    return false;
                }
            });

        </script>
        <?php if ($forpsw == 1) { ?>
            <script>
                change_heading('forgot');
            </script>
        <?php }
        ?>
    </body>
</html>
