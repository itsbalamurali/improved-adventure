<?php
include 'common.php';

$GeneralMemberId = isset($_REQUEST["GeneralMemberId"]) ? $_REQUEST["GeneralMemberId"] : '';
$GeneralUserType = isset($_REQUEST["GeneralUserType"]) ? $_REQUEST["GeneralUserType"] : '';


$screen = isset($_REQUEST["screen"]) ? $_REQUEST["screen"] : 'mainSignIn';
$signIn = isset($_REQUEST["signIn"]) ? $_REQUEST["signIn"] : '';
$AuthenticateMember = isset($_REQUEST["AuthenticateMember"]) ? $_REQUEST["AuthenticateMember"] : '';
$email = isset($_REQUEST["email"]) ? $_REQUEST["email"] : '';
$CountryCode = isset($_REQUEST["CountryCode"]) ? $_REQUEST["CountryCode"] : '';
$otpVerification = isset($_REQUEST["otpVerification"]) ? $_REQUEST["otpVerification"] : '';


$action = isset($_REQUEST["action"]) ? $_REQUEST["action"] : '';
$emailError = '';
$pagename = 'accountdeleteprocess.php';



if (strtoupper($GeneralUserType) == "PASSENGER") {
    $memberData = $obj->MySQLSelect("SELECT iUserId, vLang, vPhone, vPhoneCode, vEmail, vPassword FROM register_user WHERE iUserId = '$GeneralMemberId'");
} elseif (strtoupper($GeneralUserType) == "DRIVER") {
    $memberData = $obj->MySQLSelect("SELECT iDriverId, vLang, vPhone, vCode, vEmail, vPassword FROM register_driver WHERE iDriverId = '$GeneralMemberId'");
} elseif (strtoupper($GeneralUserType) == "COMPANY") {
    $memberData = $obj->MySQLSelect("SELECT iCompanyId, vLang, vPhone, vCode, vEmail, vPassword FROM company WHERE iCompanyId = '$GeneralMemberId'");
}

$vLang = $memberData[0]['vLang'];
$languageLabelsArr = $LANG_OBJ->FetchLanguageLabels($vLang, "1", $iServiceId);



?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $languageLabelsArr['LBL_DELETE_ACCOUNT_TXT'] ?></title>
    <link href="https://fonts.googleapis.com/css?family=Poppins:100,400,500,600,700,800,900&display=swap" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="<?= $tconfig['tsite_url'] ?>assets/css/add_countrycode_dropdown.css">
    <link rel="stylesheet" type="text/css" href="<?= $tconfig['tsite_url'] ?>assets/css/account_delete_process.css">
    <link rel="stylesheet" href="<?= $tconfig['tsite_url'] ?>assets/css/apptype/<?= $template; ?>/style.less" type="text/less">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script>
        document.write('<style type="text/css">body{display:none}</style>');
        jQuery(function($) {
            $('body').css('display', 'block');
        });
    </script>

</head>

<body class="account-delete">
    <div class="container">

        
    </div>


	<script src="assets/js/getDataFromApi.js"></script>
    <script type="text/javascript" src="<?= $tconfig['tsite_url'] ?>assets/js/add_country_code_dropdown.js"></script>
    <script src="<?= $tconfig['tsite_url'] . "/templates/" . $template . "/assets/js/less.min.js" ?>"></script>
    <script>
        less = {
            env: 'development'
        };
    </script>
    <script type="text/javascript">
        ajaxpagename = 'ajax_account_delete_process.php';
        

        var tsite_url = '<?php echo $tconfig['tsite_url']; ?>';
        var pagename = '<?php echo $pagename; ?>';
        var screen = '<?php echo $screen; ?>';
        var GeneralMemberId = '<?php echo $GeneralMemberId; ?>';
        var GeneralUserType = '<?php echo $GeneralUserType; ?>';
        
        reloadPage(screen,GeneralMemberId,GeneralUserType);

        function reloadPage(screen,GeneralMemberId,GeneralUserType)
        {
            var data = {
                data:1,
                screen: screen,
                GeneralMemberId:GeneralMemberId,
                GeneralUserType:GeneralUserType,
            };
            $.ajax({
                url: tsite_url + ajaxpagename,
                data:data,
                method:"POST",
                success: function(result) {
                    getPhoneCodeInTextBox('email', 'CountryCode');
                    $('.container').html(result);
                }
            });
        }

        function formsubmit(formName, action = '')
        {


            var formdata = $("#"+formName).serialize();
            if(action != '')
            {
                formdata = formdata+'&action='+action;

            }
            $.ajax({
                url: tsite_url + ajaxpagename,
                data:formdata,
                method:"POST",
                success: function(result) {
                    getPhoneCodeInTextBox('email', 'CountryCode');
                    $('.container').html(result);

                    if(result == 1)
                    {
                        redirectSuccess();
                    }
                }
            });

            return false;

        }

        function redirectSuccess()
        {
            var url = "<?php echo $tconfig['tsite_url'] ?>success.php?success=1&account_deleted=Yes";
            window.location.href = url;
        }
        
    </script>
</body>

</html>