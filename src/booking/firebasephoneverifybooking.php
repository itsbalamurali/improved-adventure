<?php
include_once 'common.php';
$firebaseconfigData = $FireBaseData->getFirebaseConfig();
?>
<script>
const firebaseConfig = {
  apiKey: '<?php echo $firebaseconfigData['apiKey']; ?>',
  authDomain: '<?php echo $firebaseconfigData['authDomain']; ?>',
  databaseURL: '<?php echo $firebaseconfigData['databaseURL']; ?>',
  projectId: '<?php echo $firebaseconfigData['projectId']; ?>',
  storageBucket: '<?php echo $firebaseconfigData['storageBucket']; ?>',
  messagingSenderId:'<?php echo $firebaseconfigData['messagingSenderId']; ?>',
  appId: '<?php echo $firebaseconfigData['appId']; ?>',
  measurementId: '<?php echo $firebaseconfigData['measurementId']; ?>',
  };
// Initialize Firebase
firebase.initializeApp(firebaseConfig);

// Turn off phone auth app verification.
/*firebase.auth().settings.appVerificationDisabledForTesting= true;*/

// Create a Recaptcha verifier instance globally
// Calls submitPhoneNumberAuth() when the captcha is verified
  window.onload = function() {
    // Listening for auth state changes.
    //This function runs everytime the auth state changes. Use to verify if the user is logged in
    firebase.auth().onAuthStateChanged(function(user) {
      if (user) {
       firebase.auth().signOut().then(function() {
          // Sign-out successful.
        }, function(error) {
          // An error happened.
        });
      } else {
        // No user is signed in.
        console.log("USER NOT LOGGED IN");
      }
    });


    // [START appVerifier]
    // window.recaptchaVerifier = new firebase.auth.RecaptchaVerifier('recaptcha-container-new', {
    //   'size': 'nornal',
    //   'callback': function(response) {
    //     // reCAPTCHA solved, allow signInWithPhoneNumber.
    //     submitPhoneNumberAuth();
    //   }
    // });
    // [END appVerifier]

   /* recaptchaVerifier.render().then(function(widgetId) {
      window.recaptchaWidgetId = widgetId;
    });

    var recaptchaResponse = grecaptcha.getResponse(window.recaptchaWidgetId);*/

    //console.log(recaptchaResponse);
  };

  function initReCaptcha()
  {
    // [START appVerifier]
    window.recaptchaVerifier = new firebase.auth.RecaptchaVerifier('recaptcha-container-new', {
      'size': 'normal',
      'callback': function(response) {
        // reCAPTCHA solved, allow signInWithPhoneNumber.
        //submitPhoneNumberAuth();
      }
    });
    // [END appVerifier]
    recaptchaVerifier.render().then(function(widgetId) {
      window.recaptchaWidgetId = widgetId;
    });
  }
  /**
   * Re-initializes the ReCaptacha widget.
   */
  function resetReCaptcha() {
    if (typeof grecaptcha !== 'undefined'
        && typeof window.recaptchaWidgetId !== 'undefined') {
      grecaptcha.reset(window.recaptchaWidgetId);
    }
  }

  // This function runs when the 'sign-in-button' is clicked
  // Takes the value from the 'phoneNumber' input and sends SMS to that phone number
  function submitPhoneNumberAuth(userphoneNumber) {
    var appVerifier = window.recaptchaVerifier;
    firebase
      .auth()
      .signInWithPhoneNumber(userphoneNumber, appVerifier)
      .then(function(confirmationResult) {
      	//return confirmationResult.confirm();
        //console.log(confirmationResult);
        window.confirmationResult = confirmationResult;
       if(window.confirmationResult){
          //resetReCaptcha();
          show_alert(languagedata['LBL_SIGNUP_PHONE_VERI'],verifysmscontent,languagedata['LBL_BTN_VERIFY_TXT'],languagedata['LBL_CANCEL_TXT'],'',function (btn_id) {
                if(btn_id==0) {
                    submitPhoneNumberAuthCode();
                } else if(btn_id==1){
                    $(".custom-modal-first-div").removeClass("active");
                    $(".pay-card").removeClass("tab-disable");
                    return false;
                } else {
                    alert("Please Verify Phone Number.");
                    return false;
                }
            },false);
       }
      })
      .catch(function(error) {
          $.each(error, function(k, v){
            if(v == "auth/invalid-phone-number"){
              show_alert(languagedata['LBL_SIGNUP_PHONE_VERI'],languagedata['LBL_PHONE_VALID_MSG'],'',languagedata['LBL_BTN_OK_TXT'],'');

              return false;
            }
            if(v == "auth/missing-phone-number"){
              show_alert(languagedata['LBL_SIGNUP_PHONE_VERI'],languagedata['LBL_PHONE_VALID_MSG'],'',languagedata['LBL_BTN_OK_TXT'],'');

              return false;
            }
            if(v == "auth/too-many-requests"){
              show_alert(languagedata['LBL_SIGNUP_PHONE_VERI'],error.message,'',languagedata['LBL_BTN_OK_TXT'],'');

              return false;
            }

          });
      });
  }

  // This function runs when the 'confirm-code' button is clicked
  // Takes the value from the 'code' input and submits the code to verify the phone number
  // Return a user object if the authentication was successful, and auth is complete
  function submitPhoneNumberAuthCode() {

    var code = $("#verificationcode").val();
    if(code != ""){
      confirmationResult
        .confirm(code)
        .then(function(result) {
           $("#errormsg").html("");
            <?php if (!empty($vPhone)) { ?>
                var verifciationphone = '<?php echo $Datauser[0]['vPhoneCode'].$Datauser[0]['vPhone']; ?>';
            <?php } else { ?>
                var phonecode =  $('#vPhoneCode').val();
                var PhoneNo = $('#vPhone').val();
                var verifciationphone = phonecode + PhoneNo;
            <?php } ?>
            var dataSmsverify = {
                "tSessionId": ResponseDataArray.tSessionId,
                "GeneralMemberId": ResponseDataArray.iUserId,
                "GeneralUserType": 'Passenger',
                "MobileNo":verifciationphone,
                "type": 'sendVerificationSMS',
                "iMemberId": ResponseDataArray.iUserId,//'<?php echo $Datauser[0]['iUserId']; ?>',
                "UserType": 'Passenger',
                "REQ_TYPE": 'PHONE_VERIFIED',
                "vTimeZone": '<?php echo $vTimeZone; ?>',
            };
            dataSmsverify = $.param(dataSmsverify);

            // Added and commented by HV on 09-11-2020 as discussed with KS
            getDataFromApi(dataSmsverify, function(dataHtml) {
                var dataHtml = JSON.parse(dataHtml);

                show_alert(languagedata["LBL_SIGNUP_PHONE_VERI"],languagedata[dataHtml.message],languagedata["LBL_BTN_OK_TXT"],"","",function (btn_id) {
                    if(btn_id == 0){
                        $("#flex-row-error").html(" ");
                        SendRequestToDriver(AvailableDriverIds);
                        $("#request-loader001").show();
                        $("#requ_title").show();
                        return false;
                    }
                });
                return false;
            });

            // Added and commented by HV on 09-11-2020 as discussed with KS End
        })
        .catch(function(error) {
          $('#request-loader001').hide();
          $.each(error, function(k, v){
            if(v == "auth/invalid-verification-code"){
              $("#errormsg").html(languagedata['LBL_INVALID_VERIFICATION_CODE_ERROR']).show();

              return false;
            }

            if(v == "auth/invalid-verification-code"){
              $("#errormsg").html(languagedata['LBL_ENTER_VERIFICATION_CODE']).show();

              return false;
            }

            if(v == "auth/invalid-phone-number"){
              $("#errormsg").html(languagedata['LBL_PHONE_VALID_MSG']).show();

              return false;
            }

            if(v == "auth/missing-phone-number"){
              $("#errormsg").html(languagedata['LBL_PHONE_VALID_MSG']).show();

              return false;
            }
          });
        });
    } else {
      $('#request-loader001').hide();
      $("#errormsg").html(languagedata['LBL_ENTER_VERIFICATION_CODE']).show();

      return false;
    }
  }

</script>