<?php
$sql = "select vTitle, vCode, vCurrencyCode, eDefault from language_master where eStatus='Active' ORDER BY iDispOrder ASC";
$db_lng_mst = $obj->MySQLSelect($sql);
$count_lang = count($db_lng_mst);

if (isset($_POST['submitss'])) {
    $vNamenewsletter = trim($_REQUEST['vNamenewsletter']);
    $vEmailnewsletter = trim($_REQUEST['vEmailnewsletter']);
    $eStatus = trim($_REQUEST['eStatus']);
    $remoteIp = $_SERVER['REMOTE_ADDR'];
    $dateTime = date("Y-m-d H:i:s");

    $chkUser = "SELECT * FROM `newsletter` WHERE vEmail = '" . $vEmailnewsletter . "' ";
    $chkUserCnt = $obj->MySQLSelect($chkUser);
    $fetchStatus = $chkUserCnt[0]['eStatus'];

    if (count($chkUserCnt) > 0) {

        if (($fetchStatus == "Unsubscribe") && ($eStatus == "Unsubscribe")) {
            header("Location:thank-you.php?action=Alreadyunsubscribe");
            exit;
        } if (($fetchStatus == "Subscribe") && ($eStatus == "Subscribe")) {
            header("Location:thank-you.php?action=Alreadysubscribe");
            exit;
        }
        if (($fetchStatus == "Subscribe") && ($eStatus == "Unsubscribe")) {
            $maildata['EMAIL'] = $vEmailnewsletter;
            $maildata['NAME'] = $vNamenewsletter;
            $maildata['EMAILID'] = $SUPPORT_MAIL;
            $maildata['PHONENO'] = $SUPPORT_PHONE;

            $COMM_MEDIA_OBJ->SendMailToMember("MEMBER_NEWS_UNSUBSCRIBE_USER", $maildata);
        }
        if (($fetchStatus == "Unsubscribe") && ($eStatus == "Subscribe")) {
            $maildata['EMAIL'] = $vEmailnewsletter;
            $maildata['NAME'] = $vNamenewsletter;
            $maildata['EMAILID'] = $SUPPORT_MAIL;
            $maildata['PHONENO'] = $SUPPORT_PHONE;

            $COMM_MEDIA_OBJ->SendMailToMember("MEMBER_NEWS_SUBSCRIBE_USER", $maildata);
        }

        $insert_query = "UPDATE newsletter SET vName='" . $vNamenewsletter . "', vIP='" . $remoteIp . "',tDate='" . $dateTime . "', eStatus = '" . $eStatus . "' WHERE vEmail='" . $vEmailnewsletter . "'";
    } else {

        if ((count($chkUserCnt) == 0) && $eStatus == 'Unsubscribe') {
            header("Location:thank-you.php?action=Notsubscribe");
            exit;
        }
        if ($eStatus == 'Subscribe') {
            $maildata['EMAIL'] = $vEmailnewsletter;
            $maildata['NAME'] = $vNamenewsletter;
            $maildata['EMAILID'] = $SUPPORT_MAIL;
            $maildata['PHONENO'] = $SUPPORT_PHONE;

            $COMM_MEDIA_OBJ->SendMailToMember("MEMBER_NEWS_SUBSCRIBE_USER", $maildata);
        }

        $insert_query = "INSERT INTO newsletter SET vName='" . $vNamenewsletter . "',vEmail='" . $vEmailnewsletter . "',vIP='" . $remoteIp . "',tDate='" . $dateTime . "', eStatus = '" . $eStatus . "' ";
    }
    $obj->sql_query($insert_query);
    header("Location: thank-you.php?action=$eStatus");
    exit;
}
?>
<footer>
    <div class="footer-top">
        <div class="footer-inner">
            <div class="footer-column">
			<!-- <h4><? //= $langage_lbl['LBL_FOOTER_HOME_CONTACT_US_TXT']; ?></h4> -->
            <img src="assets/img/home/footer-logo.png" alt="">
                <address><?= $COMPANY_ADDRESS ?></address>
                    <ul class="contact-data">
                        <li><label><?= $langage_lbl['LBL_PHONE_FRONT_FOOTER']; ?> : </label><a href="tel:+<?= $SUPPORT_PHONE; ?>">+<?= $SUPPORT_PHONE; ?></a></li>
                        <li><label><?= $langage_lbl['LBL_EMAIL_FRONT_FOOTER']; ?> : </label><a href="mailto:<?= $SUPPORT_PHONE; ?>"><?= $SUPPORT_MAIL; ?></a></li>
                    </ul>
                </span>
            </div>
            <div class="footer-column">
                <ul>
                     <li><a href="how-it-works"><?= $langage_lbl['LBL_HOW_IT_WORKS']; ?></a></li>
                    <li><a href="trust-safty-insurance"><?= $langage_lbl['LBL_SAFETY_AND_INSURANCE']; ?></a></li>
                    <li><a href="terms-condition"><?= $langage_lbl['LBL_FOOTER_TERMS_AND_CONDITION']; ?></a></li>
                    
                </ul>
            </div>
            <div class="footer-column">
                <ul>
					<li><a href="about"><?= $langage_lbl['LBL_ABOUT_US_HEADER_TXT']; ?></a></li>
                    <li><a href="contact-us"><?= $langage_lbl['LBL_FOOTER_HOME_CONTACT_US_TXT']; ?></a></li>
                    <li><a href="help-center"><?= $langage_lbl['LBL_FOOTER_HOME_HELP_CENTER']; ?></a></li>
                    
					
                </ul>
            </div>
            <div class="footer-column">
                <ul>
                    <li><a href="privacy-policy"><?= $langage_lbl['LBL_PRIVACY_POLICY_TEXT']; ?></a></li>
                    <li><a href="faq"><?= $langage_lbl['LBL_FAQs']; ?></a></li>
                    <li><a href="legal"><?= $langage_lbl['LBL_LEGAL']; ?></a></li>
                </ul>
            </div>
            <div class="footer-column">
					<?php if ((!empty($FB_LINK_FOOTER)) || (!empty($TWITTER_LINK_FOOTER)) || (!empty($LINKEDIN_LINK_FOOTER)) || (!empty($GOOGLE_LINK_FOOTER)) || (!empty($INSTAGRAM_LINK_FOOTER)) || ($ENABLE_NEWSLETTERS_SUBSCRIPTION_SECTION == "Yes")) { ?>
                     <ul class="social-media-list">
                        <?php if (!empty($FB_LINK_FOOTER)) { ?>
                            <li><a href="<?php echo $FB_LINK_FOOTER; ?>" target="_blank"><i class="fa fa-facebook"></i></a></li> 
                            <?php
                        }
                        if (!empty($TWITTER_LINK_FOOTER)) {
                            ?>
                            <li><a href="<?php echo $TWITTER_LINK_FOOTER; ?>" target="_blank"><i class="fa fa-twitter"></i></a></li>
                            <?php
                        }
                        if (!empty($LINKEDIN_LINK_FOOTER)) {
                            ?>
                            <li><a href="<?php echo $LINKEDIN_LINK_FOOTER; ?>" target="_blank"><i class="fa fa-linkedin"></i></a></li>
                            <?php
                        }
                        if (!empty($GOOGLE_LINK_FOOTER)) {
                            ?>
                            <li><a href="<?php echo $GOOGLE_LINK_FOOTER; ?>" target="_blank"><i class="fa fa-google"></i></a></li>
                            <?php
                        }
                        if (!empty($INSTAGRAM_LINK_FOOTER)) {
                            ?>
                            <li><a href="<?php echo $INSTAGRAM_LINK_FOOTER; ?>" target="_blank"><i class="fa fa-instagram"></i></a></li>
                            <?php
                        }
                        if ($ENABLE_NEWSLETTERS_SUBSCRIPTION_SECTION == "Yes") {
                            ?> 
                            <li><a href="#" data-target="#newsletter" data-toggle="modal" class="MainNavText" id="MainNavHelp" ><i class="fa fa-envelope"></i></a></li>
                        <?php } ?>
                    </ul>
                <?php } ?>
                <div class="download-links">
                    <a href="<?= $IPHONE_APP_LINK ?>" target="_blank"><img src="assets/img/ios-store.png" alt=""></a>
                    <a href="<?= $ANDROID_APP_LINK ?>" target="_blank"><img src="assets/img/google-play_.png" alt=""></a>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="footer-inner">
            &copy; <?= $COPYRIGHT_TEXT ?>
        </div>
    </div>
</footer>
<script>
    function change_lang(lang) {
        document.location = 'common.php?lang=' + lang;
    }
</script>
<script type="text/javascript" src="assets/js/validation/jquery.validate.min.js" ></script>
<script>
                                function refreshCaptchanewsletter() {
                                    document.getElementById('POST_CAPTCHA_NEWSLETTER').value = '';
                                    var img = document.images['newslettercaptchaimg'];
                                    var codee = Math.random() * 1000;
                                    img.src = img.src.substring(0, img.src.lastIndexOf("?")) + "?rand=" + codee;
                                }
</script>
<div class="modal fade" id="newsletter" tabindex="-1" role="dialog" aria-labelledby="ModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h4><?= $langage_lbl['LBL_HEAD_SUBSCRIBE_NEWSLATTER_TXT']; ?></h4></div>
            <div class="modal-body">

                <div class="form-box-content export-popup">
                    <form  name="newsletter" id="frmnewsletter" method="post" action="" class="clearfix" enctype="multipart/form-data">
                        <div class="row">  
                            <div class="col-lg-12">
                                <label><?= $langage_lbl['LBL_USER_NAME_HEADER_SLIDE_TXT']; ?><span class="red"> *</span></label>
                            </div>
                            <div class="col-lg-8">
                                <input type="text" autocomplete="off" class="form-control" name="vNamenewsletter"  id="vNamenewsletter" value="<?= $vNamenewsletter; ?>" placeholder="<?= $langage_lbl['LBL_USER_NAME_HEADER_SLIDE_TXT']; ?>" >
                            </div>
                        </div>
                        <div class="row" style="margin-top:10px;">
                            <div class="col-lg-12">
                                <label><?= $langage_lbl['LBL_EMAIL_LBL_TXT']; ?><span class="red"> *</span></label>
                            </div>
                            <div class="col-lg-8">
                                <input type="text" autocomplete="off" class="form-control" name="vEmailnewsletter"  id="vEmailnewsletter" value="<?= $vEmailnewsletter; ?>" placeholder="<?= $langage_lbl['LBL_EMAIL_LBL_TXT']; ?>" > 
                            </div> 
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-lg-8">
                                <label> <input type="radio" checked="" name="eStatus" value="Subscribe"></label>&nbsp;<?php echo $langage_lbl['LBL_SUBSCRIBE']; ?>&nbsp;&nbsp;&nbsp;
                                <label><input type="radio" name="eStatus" value="Unsubscribe"></label>&nbsp;<?php echo $langage_lbl['LBL_UNSUBSCRIBE']; ?>
                            </div>
                        </div>
                        <br>
                        <!-- Captcha Syntax -->
                        <span class="newrow">
                            <strong class="captcha-newsletter"> <label ><?= $langage_lbl['LBL_CAPTCHA_SIGNUP']; ?><span class="red">*</span></label>
                                <input id="POST_CAPTCHA_NEWSLETTER" class="create-account-input" size="5" maxlength="5" name="POST_CAPTCHA_NEWSLETTER" type="text" autocomplete="off" style="border-bottom: 1px solid black;" placeholder=""  >

                                <em class="captcha-dd">
                                    <img src="captcha_code_news_file.php?rand=<?php echo rand(); ?>" id='newslettercaptchaimg' alt="" class="chapcha-img" />&nbsp;<?= $langage_lbl['LBL_CAPTCHA_CANT_READ_SIGNUP']; ?>
                                    <a href='javascript:void(0)' onclick="refreshCaptchanewsletter();"><?= $langage_lbl['LBL_CLICKHERE_SIGNUP']; ?></a>
                                </em>

                            </strong>
                        </span>
                        <!-- Close Captcha -->
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal"><?= $langage_lbl['LBL_BTN_CANCEL_TRIP_TXT']; ?></button>
                            <input type="submit" class="btn btn-success"  name="submitss" id="submitss" value="<?php echo $langage_lbl_admin['LBL_BTN_SUBMIT_TXT']; ?>" >
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>