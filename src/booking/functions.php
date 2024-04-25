<script>

    var cubexthemeon = '<?php echo $cubexthemeon; ?>';


    var txtnote = '<?php echo addslashes($langage_lbl['LBL_VARIFICATION_CODE_SENT_TO_MOBILE']);?>';


    var MobileNo = '<?= $vPhone; ?>';
    var AvailableDriverIds = [];


    var res = txtnote.replace("#MOBILE_NO#", MobileNo);


    var userphoneNumber;


    var newDiv;


    var verifysmscontent = "<div class='fetched-data'><div class='verifytxt' style='padding: 10px 0;'>" + res + "</div><form class='formverify' role='form' id='formverify'><div class='form-group'><label for='nom'>Verification Code</label>&nbsp;&nbsp;<input type='text' class='form-control' name ='verificationcode' id='verificationcode' required='required'></div><div id='errormsg' style='color:red;'></div></form></div>";


    var adminSkip = "No";


    var InputReceiverMobile = document.getElementById('vReceiverMobile');

    if (InputReceiverMobile) {

        InputReceiverMobile.oninvalid = function (event) {

            event.target.setCustomValidity('<?php echo addslashes($langage_lbl['LBL_INVALID_MOBILE_NO']);?>');

        }

    }



    <?php if ($userType1 == 'rider') { ?>



    $("#add-booking-form-taxi1").hide();

    $(".map-color-code").hide();


    $(".helpbutton").hide();


    $(".admin-notes").hide();


    $(".other-service").hide();


    if ($('input[name=eType]:checked').val() == 'UberX') {


        $(".auto_assignOr").hide();

        $(".driverlists").show();

        $(".autoassignbtn").hide();

        $("#iDriverId").removeAttr('disabled');

        $("#iDriverId").attr('required', 'required');


    } else {


        $(".driverlists").hide();

        $(".auto_assignOr").hide();

        $(".autoassignbtn").hide();

        $("#iDriverId").val('');

        $("#iDriverId").removeAttr("required");

        $("#iDriverId").attr('disabled', 'disabled');


    }







    <?php } else if ($userType1 == 'company' || $userType1 == 'admin') { ?>



    $("#add-booking-form-taxi1").show();


    $(".map-color-code").hide();


    $('input[type=radio][name=eType]').change(function () {


        if (($('input[name=eType]:checked').val() == 'Ride' || $('input[name=eType]:checked').val() == 'Moto' || $('input[name=eType]:checked').val() == 'Fly') && $('input[name=eRideType]:checked').val() == 'now') {


            $(".auto_assignOr").hide();


            $(".driverlists").hide();


            $(".autoassignbtn").hide();


            $("#iDriverId").val('');


            $("#iDriverId").removeAttr("required");


            $("#iDriverId").attr('disabled', 'disabled');


        } else if ($('input[name=eType]:checked').val() == 'Deliver' && $('input[name=eDeliveryType]:checked').val() == 'now') {


            $(".auto_assignOr").hide();


            $(".driverlists").hide();


            $(".autoassignbtn").hide();


            $("#iDriverId").val('');


            $("#iDriverId").removeAttr("required");


            $("#iDriverId").attr('disabled', 'disabled');


        } else if ($('input[name=eType]:checked').val() == 'UberX') {


            $(".auto_assignOr").hide();


            $(".driverlists").show();


            $(".autoassignbtn").hide();


            $("#iDriverId").removeAttr('disabled');


            $("#iDriverId").attr('required', 'required');


        } else {


            $(".auto_assignOr").show();


            $(".driverlists").show();


            $(".autoassignbtn").show();


        }


    });


    if (eType == 'Ride' || eType == 'Fly' || eType == 'Moto') {


        if ($('input[name=eRideType]:checked').val() == 'now') {


            $(".auto_assignOr").hide();


            $(".driverlists").hide();


            $("#iDriverId").val('');


            $("#iDriverId").removeAttr("required");


            $("#iDriverId").attr('disabled', 'disabled');


            $(".autoassignbtn").hide();


        } else {


            $(".auto_assignOr").show();


            $(".autoassignbtn").show();


            $("#iDriverId").removeAttr('disabled');



            <?php if ($action != 'Edit') { ?>



            $("#iDriverId").attr('required', 'required');



            <?php } else { ?>

            $(".driverlists").show();


            $("#iDriverId").removeAttr("required");



            <?php } ?>



        }


    }


    if (eType == 'Deliver') {


        if ($('input[name=eDeliveryType]:checked').val() == 'now') {


            $(".auto_assignOr").hide();


            $(".driverlists").hide();


            $(".autoassignbtn").hide();


            $("#iDriverId").val('');


            $("#iDriverId").removeAttr("required");


            $("#iDriverId").attr('disabled', 'disabled');


        } else {


            $(".auto_assignOr").show();


            $(".autoassignbtn").show();


            $("#iDriverId").removeAttr('disabled');



            <?php if ($action != 'Edit') { ?>



            $("#iDriverId").attr('required', 'required');



            <?php } else { ?>



            $(".driverlists").show();


            $("#iDriverId").removeAttr("required");



            <?php } ?>



        }


    }


    $('input[type=radio][name=eRideType]').change(function () {


        if (this.value == 'now') {


            $(".auto_assignOr").hide();


            $(".driverlists").hide();


            $(".autoassignbtn").hide();


            $("#showdriverSet001").hide();


            $("#driverSet001").html('');


            $("#iDriverId").val('');


            $("#iDriverId").removeAttr("required");


            $("#iDriverId").attr('disabled', 'disabled');


            $('#datetimepicker4').removeAttr('required');


        } else {


            $(".auto_assignOr").show();


            $(".driverlists").show();


            $(".autoassignbtn").show();


            $("#iDriverId").removeAttr('disabled');


            $("#iDriverId").attr('required', 'required');


            $('#datetimepicker4').attr('required', 'required');


        }


    });


    $('input[type=radio][name=eDeliveryType]').change(function () {


        if (this.value == 'now') {


            $(".auto_assignOr").hide();


            $(".driverlists").hide();


            $(".autoassignbtn").hide();


            $("#showdriverSet001").hide();


            $("#driverSet001").html('');


            $("#iDriverId").val('');


            $("#iDriverId").removeAttr("required");


            $("#iDriverId").attr('disabled', 'disabled');


            $('#datetimepicker4').removeAttr('required');


        } else {


            $(".auto_assignOr").show();


            $(".driverlists").show();


            $(".autoassignbtn").show();


            $("#iDriverId").removeAttr('disabled');


            $("#iDriverId").attr('required', 'required');


            $('#datetimepicker4').attr('required', 'required');


        }


    });


    $(".helpbutton").show();


    $(".admin-notes").hide();



    <?php } else {



    ?>







    $("#add-booking-form-taxi1").show();


    //$(".map-color-code").show(); //commented bc in fare estimate without login it will show so space at top contents are not shown but space bc of this div so hid it


    $(".map-color-code").hide();


    $(".auto_assignOr").show();


    $(".helpbutton").show();


    $(".driverlists").show();


    $(".admin-notes").show();


    $(".autoassignbtn").show();



    <?php } ?>











    $(".vehicleImageIdClass_ride").on('click', function () { // Call When change ride vehicles


        var vehicleId = $(this).attr('id');


        $('.vehicleImageIdClass_ride label em img').each(function () {


            var vId = $(this).data('id');


            if (vId != vehicleId) {


                $(this).attr('src', $('#vehicle_image_' + vId).val());


            } else {


                $(this).attr('src', $('#vehicle_image_hover_' + vId).val());


            }


        });


    });


    $(".vehicleImageIdClass_delivery").on('click', function () { // Call When change delivery vehicles


        var vehicleId = $(this).attr('id');


        $('.vehicleImageIdClass_delivery label em img').each(function () {


            var vId = $(this).data('id');


            if (vId != vehicleId) {


                $(this).attr('src', $('#vehicle_image_' + vId).val());


            } else {


                $(this).attr('src', $('#vehicle_image_hover_' + vId).val());


            }


        });


    });


    function showEstimateFareDisplayFare(selectedcarTypeId) {


        if ($("#from").val() != "" && $("#to").val() != "") {


            carTypeId = selectedcarTypeId;


            var StartLatitude = $("#from_lat").val();


            var EndLongitude = $("#from_long").val();


            var DestLatitude = $("#to_lat").val();


            var DestLongitude = $("#to_long").val();


            var distancenew = parseInt($("#distance").val());


            var durationnew = parseInt($("#duration").val());


            var distance_new = Math.round(distancenew);


            var duration_new = Math.round(durationnew);


            var iUserIdNew = $('#iUserId').val();


            //$("html").addClass('loading');


            // Added and commented by HV on 09-11-2020 as discussed with KS

            var data = {

                'type': 'getEstimateFareDetailsArr',

                'distance': distance_new,

                'time': duration_new,

                'SelectedCar': selectedcarTypeId,

                'StartLatitude': StartLatitude,

                'EndLongitude': EndLongitude,

                'DestLatitude': DestLatitude,

                'DestLongitude': DestLongitude,

                'iUserId': iUserIdNew,

                'UserType': 'Passenger',

                'isDestinationAdded': 'Yes'

            };

            data = $.param(data);


            getDataFromApi(data, function (response) {

                var response = JSON.parse(response);

                var resultarray = response.message;

                var table_header = "<ul>";

                var table_footer = "</ul>";

                var html = "";


                $.each(resultarray, function (k, value) {

                    //display the key and value pair

                    // console.log(resultarray.length);

                    $.each(value, function (key, val) {

                        //console.log(key+"-->"+val);

                        if (k == (resultarray.length - 1)) {

                            html += "<li><b><span>" + key + "</span>" + val + "</b></li>";

                        } else if (key == 'eDisplaySeperator') {

                            html += "<hr style='border-top: 1px solid #ddd;margin:10px 0;' />";

                        } else {

                            html += "<li><span>" + key + "</span>" + val + "</li>";

                        }

                    });

                });


                var all = table_header + html + table_footer;

                $("#showAfterDestination").html(all);

                $('.tootltipclass img').data(all);

                if (cubexthemeon == 'Yes') {

                    $("#fareEstimateModal").addClass('active');

                } else {

                    $("#fareEstimateModal").modal('show');

                }

            });


        } else {


            alert("<?= addslashes($langage_lbl['LBL_MANUAL_BOOKING_PICKUP_DROPOFF_LOCATION']) ?>.");


            return false;


        }


        return false;


    }


    function showVehicleTypeAmount(iVehicleTypeId) {


        var iUserIdNew = $('#iUserId').val();


        var ajaxData = {

            'URL': "<?= $tconfig["tsite_url"] ?>booking/ajax_ufx_service_charge_details.php",

            'AJAX_DATA': {iVehicleTypeId: iVehicleTypeId, iUserId: iUserIdNew},

            'REQUEST_DATA_TYPE': 'html'

        };

        getDataFromAjaxCall(ajaxData, function (response) {

            if (response.action == "1") {

                var dataHtml2 = response.result;

                if (dataHtml2 != '') {


                    $('#iVehicleTypeData').html(dataHtml2);


                    $('#iVehicleTypeData').show();


                }


            } else {

                console.log(response.result);

            }

        });


    }


    $('input[name=eRideType]').on('change', function () {


        var setVal = $(this).val();


        if (setVal == 'later') {


            $(".dateSchedule, .dBooking_date_display").show();


            $("#scheduleLater1").focus();


        } else {


            $(".dateSchedule, .dBooking_date_display").hide();


        }


    });


    $('input[name=eDeliveryType]').on('change', function () {


        var setVal = $(this).val();


        if (setVal == 'later') {


            $(".dateSchedule, .dBooking_date_display").show();


            $("#scheduleLater1").focus();


        } else {


            $(".dateSchedule, .dBooking_date_display").hide();


        }


    });


    function js_yyyy_mm_dd_hh_mm_ss() {


        now = new Date();


        year = "" + now.getFullYear();


        month = "" + (now.getMonth() + 1);


        if (month.length == 1) {


            month = "0" + month;


        }


        day = "" + now.getDate();


        if (day.length == 1) {


            day = "0" + day;


        }


        hour = "" + now.getHours();


        if (hour.length == 1) {


            hour = "0" + hour;


        }


        minute = "" + now.getMinutes();


        if (minute.length == 1) {


            minute = "0" + minute;


        }


        second = "" + now.getSeconds();


        if (second.length == 1) {


            second = "0" + second;


        }


        return year + "-" + month + "-" + day + " " + hour + ":" + minute + ":" + second;


    }


    function SubmitFormCheck(idClicked) {


        $(".loader-default").show();


        if (idClicked == 'later') {


            SubmitBookingForm(idClicked);


        } else {


            var today = js_yyyy_mm_dd_hh_mm_ss()


            $("#datetimepicker4").val(today);


            var ajaxData = {

                'URL': '<?= $tconfig['tsite_url'] ?>booking/action_booking.php',

                'AJAX_DATA': $("#add_booking_form").serialize(),

                'REQUEST_DATA_TYPE': 'json'

            };

            getDataFromAjaxCall(ajaxData, function (response) {

                if (response.action == "1") {

                    var data = response.result;

                    $(".loader-default").hide();


                    $("#request-loader001").show();


                    $("#requ_title").show();


                    ResponseDataArray = data;


                    loadAvailableCab();


                    return false;

                } else {

                    console.log(response.result);

                    $(".loader-default").hide();

                }

            });


            //$("#add_booking_form").submit();


            return true;


        }


    }


    function SubmitBookingForm(idClicked) {


        $(".loader-default").show();


        var ajaxData = {

            'URL': '<?= $tconfig['tsite_url'] ?>booking/action_booking.php',

            'AJAX_DATA': $("#add_booking_form").serialize(),

            'REQUEST_DATA_TYPE': 'html'

        };

        getDataFromAjaxCall(ajaxData, function (response) {

            if (response.action == "1") {

                var data = response.result;

                $(".loader-default").hide();



                <?php if (!empty($_SESSION['sess_iAdminUserId'])) { ?>



                window.location.replace("<?= $tconfig["tsite_url_main_admin"] ?>cab_booking.php");



                <?php } else if ($_SESSION['sess_user'] == 'company') { ?>



                window.location.replace("cabbooking.php");



                <? } else { ?>



                show_alert("", "<?= addslashes($langage_lbl['LBL_USER_BOOK_LATER']) ?>", "<?= addslashes($langage_lbl['LBL_OK']) ?>", "", "", function (btn_id) {


                    window.location.replace("cabbooking.php");


                });


                //window.location.replace("usercabbooking.php"); //commented bc as discussed with CD sir after later booking  user redirect to the profile page



                <? } ?>


            } else {

                console.log(response.result);

                $(".loader-default").show();

            }

        });


    }


    function loadAvailableCab() {


        var data = {


            "tSessionId": ResponseDataArray.tSessionId,


            "GeneralMemberId": ResponseDataArray.iUserId,


            "GeneralUserType": 'Passenger',


            "type": 'loadAvailableCab',


            "vTimeZone": ResponseDataArray.vTimeZone,


            "iUserId": ResponseDataArray.iUserId,


            "PassengerLat": ResponseDataArray.vSourceLatitude,


            "PassengerLon": ResponseDataArray.vSourceLongitude,


            "iVehicleTypeId": ResponseDataArray.iVehicleTypeId,


            "PickUpAddress": ResponseDataArray.vSourceAddresss,


            "eType": ResponseDataArray.eType,


            "eRental": ResponseDataArray.eRental,


            "eShowOnlyMoto": ResponseDataArray.eShowOnlyMoto,


            "isFromAdminPanel": "Yes",


            "eFemaleDriverRequest": ResponseDataArray.eFemaleDriverRequest,


            "eHandiCapAccessibility": ResponseDataArray.eHandiCapAccessibility,


            "eChildSeatAvailable": ResponseDataArray.eChildSeatAvailable,


            "iCompanyId": ResponseDataArray.iCompanyId,


            "eFly": ResponseDataArray.eFly,


            "iFromStationId": ResponseDataArray.iFromStationId,


            "iToStationId": ResponseDataArray.iToStationId,


            "isFromHotelPanel": ResponseDataArray.isFromHotelPanel,


            "async_request": false


        };


        data = $.param(data);


        // Added and commented by HV on 09-11-2020 as discussed with KS

        getDataFromApi(data, function (response) {

            var response = JSON.parse(response);

            var AvailableCabList = response.AvailableCabList;


            $.each(AvailableCabList, function (key, value) {


                if (value.ACCEPT_CASH_TRIPS == 'Yes') {


                    AvailableDriverIds.push(value.iDriverId);


                }


            });


            // sendrequestto driver


            SendRequestToDriver(AvailableDriverIds);


            return false;

        });


    }


    function SendRequestToDriver(AvailableDriverIds) {


        var driverIds = AvailableDriverIds.join(",");


        if (driverIds != '') {


            var sendrequestparam = {


                "tSessionId": ResponseDataArray.tSessionId,


                "GeneralMemberId": ResponseDataArray.iUserId,


                "GeneralUserType": 'Passenger',


                "type": 'sendRequestToDrivers',


                "vTimeZone": ResponseDataArray.vTimeZone,


                "userId": ResponseDataArray.iUserId,


                "driverIds": driverIds,


                "CashPayment": 'true', //ResponseDataArray.CashPayment,


                "SelectedCarTypeID": ResponseDataArray.iVehicleTypeId,


                "PickUpLatitude": ResponseDataArray.vSourceLatitude,


                "PickUpLongitude": ResponseDataArray.vSourceLongitude,


                "PickUpAddress": ResponseDataArray.vSourceAddresss,


                "DestLatitude": ResponseDataArray.vDestLatitude,


                "DestLongitude": ResponseDataArray.vDestLongitude,


                "DestAddress": ResponseDataArray.tDestAddress,


                "eType": ResponseDataArray.eType,


                "fTollPrice": ResponseDataArray.fTollPrice,


                "vTollPriceCurrencyCode": ResponseDataArray.vTollPriceCurrencyCode,


                "eBookingFrom": ResponseDataArray.eBookingFrom,


                "iHotelBookingId": ResponseDataArray.iHotelBookingId,


                "eTollSkipped": ResponseDataArray.eTollSkipped,


                "tPackageDetails": ResponseDataArray.tPackageDetails,


                "iPackageTypeId": ResponseDataArray.iPackageTypeId,


                "tDeliveryIns": ResponseDataArray.tDeliveryIns,


                "tPickUpIns": ResponseDataArray.tPickUpIns,


                "vReceiverName": ResponseDataArray.vReceiverName,


                "vReceiverMobile": ResponseDataArray.vReceiverMobile,


                "eFemaleDriverRequest": ResponseDataArray.eFemaleDriverRequest,


                "eHandiCapAccessibility": ResponseDataArray.eHandiCapAccessibility,


                "PromoCode": ResponseDataArray.vCouponCode,


                "eFly": ResponseDataArray.eFly,


                "iFromStationId": ResponseDataArray.iFromStationId,


                "iToStationId": ResponseDataArray.iToStationId,


                "iHotelId": ResponseDataArray.iHotelId,


                "isFromHotelPanel": ResponseDataArray.isFromHotelPanel,

                "vDistance": ResponseDataArray.vDistance,

                "vDuration": ResponseDataArray.vDuration,

                "delivery_arr": ResponseDataArray.delivery_arr,

                "async_request": false,

                "eBookingFromWeb": "Yes",

                "total_del_dist": ResponseDataArray.total_del_dist,

                "total_del_time": ResponseDataArray.total_del_time,


                "adminSkip": adminSkip
            };


            sendrequestparam = $.param(sendrequestparam);


            // Added and commented by HV on 09-11-2020 as discussed with KS

            getDataFromApi(sendrequestparam, function (response) {

                var response = JSON.parse(response);

                if (response.Action == '1') {


                    getAcceptedDriver001();


                } else {


                    //alert(response.Message);

                    message = response.message;

                    <?php if(!empty($vPhone)){ ?>
                    var verifciationphone = '<?= $vPhoneCode . $vPhone; ?>';
                    <?php } else { ?>
                    var phonecode = '<?= $vPhoneCode;?>';
                    var PhoneNo = $('#vPhone').val();
                    var verifciationphone = phonecode + PhoneNo;
                    <?php } ?>
                    if (message == 'DO_PHONE_VERIFY' && adminSkip == "No") {


                        var dataSms = {


                            "tSessionId": ResponseDataArray.tSessionId,


                            "GeneralMemberId": ResponseDataArray.iUserId,


                            "GeneralUserType": 'Passenger',


                            "MobileNo": verifciationphone,


                            "type": 'sendVerificationSMS',


                            "iMemberId": ResponseDataArray.iUserId,


                            "UserType": 'Passenger',


                            "REQ_TYPE": 'DO_PHONE_VERIFY',


                            "vTimeZone": ResponseDataArray.vTimeZone,


                            'async_request': false


                        };


                        dataSms = $.param(dataSms);


                        getDataFromApi(dataSms, function (dataHtmlSMS) {

                            var dataHtmlSMS = JSON.parse(dataHtmlSMS);

                            var verificationmethod = dataHtmlSMS.MOBILE_NO_VERIFICATION_METHOD;

                            if (typeof verificationmethod !== 'undefined' && verificationmethod == 'Firebase' && dataHtmlSMS.Action == "1") {

                                userphoneNumber = '+' + verifciationphone;

                                var ReCaptchaElement = '<div id="recaptcha-container-new" style="margin-bottom: 10px"></div><div id="captcha_error" style="color:#ff0000"></div>';

                                var verifysms_continue = '<p style="margin-bottom: 10px">We need to verify your phone number (' + userphoneNumber + ').</p>' + ReCaptchaElement;

                                //newDiv = $('<div id="recaptcha-container"></div>');

                                <?php if ($eBookingFrom == 'Admin' || $eBookingFrom == 'Hotel' || $eBookingFrom == 'Kiosk' || $eBookingFrom == 'Company') { ?>

                                var skipbutton = '<?= addslashes($langage_lbl['LBL_SKIP_SMALL']) ?>';

                                <? } else { ?>

                                var skipbutton = "";

                                <? } ?>

                                show_alert('<?= addslashes($langage_lbl['LBL_SIGNUP_PHONE_VERI']) ?>', verifysms_continue, '<?= addslashes($langage_lbl['LBL_CONTINUE_BTN']) ?>', '<?= addslashes($langage_lbl['LBL_CANCEL_TXT']) ?>', skipbutton, function (btn_id) {

                                    if (btn_id == 0) {


                                        var recaptchaResponse = grecaptcha.getResponse(window.recaptchaWidgetId);

                                        if (recaptchaResponse != "") {


                                            $("#captcha_error").html("Captcha verification required.").hide();

                                            submitPhoneNumberAuth(userphoneNumber);

                                        } else {

                                            $("#captcha_error").html("Captcha verification required.").show();

                                        }

                                        $(".custom-modal-first-div").addClass("active");

                                        return false;

                                    } else if (btn_id == 1) {

                                        $(".custom-modal-first-div").removeClass("active");

                                        $("#request-loader001").hide();

                                        return false;

                                    } else if (btn_id == 2) {


                                        adminSkip = "Yes";

                                        SendRequestToDriver(AvailableDriverIds);

                                        //$("#request-loader001").hide();


                                    } else {

                                        alert("Please Verify Phone Number.");

                                        return false;

                                    }

                                });

                                initReCaptcha();

                                return false;


                            } else if (dataHtmlSMS.Action == "1") {


                                $('#request-loader001').hide();


                                verification_code = dataHtmlSMS.message;



                                <?php if ($eBookingFrom == 'Admin' || $eBookingFrom == 'Hotel' || $eBookingFrom == 'Kiosk' || $eBookingFrom == 'Company') { ?>

                                var skipbutton = '<?= addslashes($langage_lbl['LBL_SKIP_SMALL']) ?>';

                                <? } else { ?>

                                var skipbutton = "";

                                <? } ?>



                                show_alert('<?= addslashes($langage_lbl['LBL_SIGNUP_PHONE_VERI']) ?>', verifysmscontent, '<?= addslashes($langage_lbl['LBL_BTN_VERIFY_TXT']) ?>', '<?= addslashes($langage_lbl['LBL_CANCEL_TXT']) ?>', skipbutton, function (btn_id) {


                                    if (btn_id == 0) {


                                        verifyphonenumber();


                                    } else if (btn_id == 1) {


                                        $(".custom-modal-first-div").removeClass("active");

                                        $("#request-loader001").hide();

                                        return false;


                                    } else if (btn_id == 2) {

                                        adminSkip = "Yes";

                                        SendRequestToDriver(AvailableDriverIds);

                                        /*$("#request-loader001").hide();*/
                                        $("#request-loader001").show();
                                        $("#requ_title").show();
                                        return false;
                                    } else {


                                        alert("Please Verify Phone Number.");

                                        return false;


                                    }


                                }, false);


                                return false;


                            } else {


                                var strmsg = dataHtmlSMS.message;


                                if (strmsg.match("^LBL_")) {


                                    var verificationmsg = languagedata[strmsg];


                                } else {


                                    var verificationmsg = strmsg;


                                }


                                $('#request-loader001').hide();



                                <?php if ($eBookingFrom == 'Admin' || $eBookingFrom == 'Hotel' || $eBookingFrom == 'Kiosk' || $eBookingFrom == 'Company') { ?>

                                var skipbutton = '<?= addslashes($langage_lbl['LBL_SKIP_SMALL']) ?>';

                                <? } else { ?>

                                var skipbutton = "";

                                <? } ?>



                                show_alert('<?= addslashes($langage_lbl['LBL_SIGNUP_PHONE_VERI']) ?>', verificationmsg, '<?= addslashes($langage_lbl['LBL_BTN_OK_TXT']) ?>', "", skipbutton, function (btn_id) {

                                    if (btn_id == 2) {

                                        adminSkip = "Yes";

                                        SendRequestToDriver(AvailableDriverIds);

                                        $("#request-loader001").show();
                                        $("#requ_title").show();
                                        return false;

                                    }

                                });


                                return false;


                            }

                        });

                    } else if (message == 'LBL_REQUEST_INPROCESS_TXT') {


                        alert("<?php echo addslashes($langage_lbl['LBL_REQUEST_INPROCESS_TXT']); ?>");


                        $("#request-loader001").hide();


                        $("#requ_title").hide();
                        return false;
                    } else if (message == 'DO_PHONE_VERIFY' && adminSkip == "Yes") {
                        SendRequestToDriver(AvailableDriverIds);
                        $("#request-loader001").show();
                        $("#requ_title").show();
                        return false;


                    } else {

                        alert("<?php echo addslashes($langage_lbl['LBL_NO_CARS_AVAIL_IN_TYPE']); ?>");


                        $("#request-loader001").hide();


                        $("#requ_title").hide();


                        return false;

                    }


                }

            });


            // Added and commented by HV on 09-11-2020 as discussed with KS End

        } else {


            $("#request-loader001").hide();


            $("#requ_title").hide();


            alert("<?= addslashes($langage_lbl['LBL_MANUAL_BOOKING_NO_DRIVER_AVAILABLE']) ?>.");


        }


    }


    function getAcceptedDriver001() {


        showReTry();


        interval3 = setInterval(function () {

            var data = {

                'type': 'configPassengerTripStatus',

                "GeneralMemberId": ResponseDataArray.iUserId,

                "GeneralUserType": 'Passenger',

                'iMemberId': ResponseDataArray.iUserId,

                'vLatitude': ResponseDataArray.vSourceLatitude,

                'vLongitude': ResponseDataArray.vSourceLongitude,

                'vTimeZone': ResponseDataArray.vTimeZone,

                "tSessionId": ResponseDataArray.tSessionId,

                "async_request": false

            };


            data = $.param(data);


            // Added and commented by HV on 09-11-2020 as discussed with KS

            getDataFromApi(data, function (response) {

                if (response.trim() != "") {

                    // console.log(response);

                    var obj = jQuery.parseJSON(response);

                    // console.log(obj);

                    if (obj.message != null) {


                        // var messagenew = jQuery.parseJSON(obj.message);

                        var messagenew = obj.message;


                        if (messagenew.Message == 'CabRequestAccepted') {


                            clearInterval(interval3);


                            $("#request-loader001").hide();


                            // $("#driver-bottom-set001").show();


                            // $("#driver-bottom-set001").html(response);


                            if (ResponseDataArray.eFly == "Yes") {

                                $("#driverData .modal-title").html("<?= addslashes($langage_lbl['LBL_ACCEPTED']); ?>");

                                $('#driver-bottom-set001').html("<?= addslashes($langage_lbl['LBL_DRIVER_ACCEPTED_DELIVERY_REQUEST_TXT']); ?>");

                            } else {


                                var ajaxData = {

                                    'URL': '<?= $tconfig['tsite_url'] ?>booking/fetch_record.php',

                                    'AJAX_DATA': {
                                        iMemberId: ResponseDataArray.iUserId,
                                        vLatitude: ResponseDataArray.vSourceLatitude,
                                        vLongitude: ResponseDataArray.vSourceLongitude,
                                        vTimeZone: ResponseDataArray.vTimeZone
                                    },

                                    'REQUEST_DATA_TYPE': 'html',

                                    'REQUEST_ASYNC': false

                                };

                                getDataFromAjaxCall(ajaxData, function (response) {

                                    if (response.action == "1") {

                                        var data = response.result;

                                        $('#driver-bottom-set001').html(data);

                                    } else {

                                        console.log(response.result);

                                    }

                                });


                            }


                            if (cubexthemeon == 'Yes') {


                                $('#driverData').addClass('active');


                            } else {


                                // $('#driverData').modal('show');


                                $('#driverData').modal({


                                    backdrop: 'static',


                                    keyboard: false


                                });


                            }


                            $("#btnYes").on("click", function (e) {



                                <?php if (!empty($_SESSION['sess_iAdminUserId'])) { ?>



                                window.location.replace("<?= $tconfig["tsite_url_main_admin"] ?>trip.php");



                                <?php } else if ($_SESSION['sess_user'] == 'company') { ?>



                                window.location.replace("company-trip");



                                <? } else { ?>



                                window.location.replace("mytrip");



                                <? } ?>



                            });


                            return false;


                        } else {


                            if (cubexthemeon == 'Yes') {


                                $('#driverData').removeClass('active');


                            } else {


                                $('#driverData').modal('hide');


                            }


                        }


                    }


                }

            }, "text");


            // Added and commented by HV on 09-11-2020 as discussed with KS End


        }, 6000);


    }


    $(document).ready(function () {


        $('#retryBtn').click(function () {


            $('#req_try_again').hide();


            $('.requesting-popup-sub').show();

            timeout = '<?= $RIDER_REQUEST_ACCEPT_TIME * 1000 ?>';


            setTimeout(function () {


                $('#req_try_again').show();


                $('.requesting-popup-sub').hide();


            }, timeout);


            loadAvailableCab();


        });


    });


    function cancellingRequestDriver() {

        // console.log(location.search); return false;

        if (confirm("Are you sure you want to cancel this request?")) {

            var data = {

                'type': 'cancelCabRequest',

                'iUserId': ResponseDataArray.iUserId,

                "GeneralMemberId": ResponseDataArray.iUserId,

                "GeneralUserType": 'Passenger',

                "tSessionId": ResponseDataArray.tSessionId,

                "async_request": false

            };


            data = $.param(data);


            // Added and commented by HV on 09-11-2020 as discussed with KS

            getDataFromApi(data, function (response) {

                var response = JSON.parse(response);

                if (response) {


                    $("#request-loader001").hide();


                    $('#add_booking_form').trigger("reset");



                    <?php if (!empty($_SESSION['sess_iAdminUserId']) && $_SESSION['SessionUserType'] != "hotel") { ?>



                    window.location.replace("<?= $tconfig["tsite_url"] ?>userbooking.php" + location.search);



                    <?php } else if (!empty($_SESSION['sess_iAdminUserId']) && $_SESSION['SessionUserType'] == "hotel") { ?>
                    window.location.replace("<?= $tconfig["tsite_url_main_admin"] ?>create_request.php" + location.search);
                    <? }else if ($_SESSION['sess_user'] == 'company') { ?>



                    window.location.replace("<?= $tconfig['tsite_url'] ?>companybooking" + location.search);



                    <? } else { ?>



                    window.location.replace("<?= $tconfig['tsite_url'] ?>userbooking" + location.search);



                    <? } ?>



                }

            });


            // Added and commented by HV on 09-11-2020 as discussed with KS End


            $("#request-loader001").hide();


            $("#cancelRequestDriver").hide();


        } else {


            return false;


        }


    }


    function showReTry() {

        timeout = '<?= $RIDER_REQUEST_ACCEPT_TIME * 1000 ?>';


        setTimeout(function () {


            $('#req_try_again').show();


            $('.requesting-popup-sub').hide();


        }, timeout);


    }


    function checkPromoCode(bookfrom) {


        /*if($('#_Payment_form').valid()) { // Check card form is valid



         alert("Promo code Applied Successfully.");



         }*/


        var iUserId = $("#iUserId").val();


        var PromoCode = $("#promocode").val();


        //var vehicleTypeId = $('#iVehicleTypeId').find(":selected").val();


        if (cubexthemeon == 'Yes') {


            var vehicleTypeId = $("#iVehicleTypeId").val();


            if (vehicleTypeId == '') {


                vehicleTypeId = $('#iVehicleTypeId').find(":checked").val()


            }


        } else {


            if (eType == 'Ride') {


                var vehicleTypeId = $('input[name=iDriverVehicleId_ride]:checked').val();


            } else if (eType == 'Deliver') {


                var vehicleTypeId = $('input[name=iDriverVehicleId_delivery]:checked').val();


            } else {


                var vehicleTypeId = $('#iVehicleTypeId').find(":selected").val();


            }


            if (bookfrom == 'Company') {


                var vehicleTypeId = $('#iVehicleTypeId').find(":selected").val();


            }


        }


        if ($.trim(vehicleTypeId) == "") {


            if (eType == "UberX") {


                alert("Please select a service type");


            } else {


                alert("Please select a vehicle type");


            }


            $("#promocode").val('');


            return false;


        }


        if (PromoCode != "") {


            var sLat = $('#from_lat').val();

            var sLong = $('#from_long').val();

            var dLat = $('#to_lat').val();

            var dLong = $('#to_long').val();

            var ajaxData = {

                'URL': '<?= $tconfig['tsite_url'] ?>booking/ajaxcheckpromoCode.php',

                'AJAX_DATA': {
                    type: 'CheckPromoCode',
                    PromoCode: PromoCode,
                    iUserId: iUserId,
                    eType: eType,
                    vSourceLatitude: sLat,
                    vSourceLongitude: sLong,
                    vDestLatitude: dLat,
                    vDestLongitude: dLong
                },

                'REQUEST_DATA_TYPE': 'json'

            };

            getDataFromAjaxCall(ajaxData, function (response) {

                if (response.action == "1") {

                    var dataHtml15 = response.result;

                    if (dataHtml15.Action == 1 && dataHtml15.message == 'LBL_PROMO_APPLIED') {


                        alert("<?= addslashes($langage_lbl['LBL_PROMO_APPLIED']) ?>");


                        if (cubexthemeon == 'Yes') {


                            //$(".discount-block button").toggleClass('icon-apply icon-close');


                            $(".discount-block button").removeClass('icon-apply');


                            $(".discount-block button").addClass('icon-close');


                            $(".clearlink").show();


                            $("#promocode").attr('readonly', '');


                            $("#promocodeapplied").val('1');


                        }


                        showAsVehicleType(vehicleTypeId);


                        showVehicleCountryVise($('#vCountry option:selected').val(), vehicleTypeId, eType);


                    } else {


                        $("#promocode").val('');


                        if (cubexthemeon == 'Yes') {


                            $("#promocodeapplied").val('');


                            $("#promocode").removeAttr('readonly');


                            $(".discount-block button").addClass('icon-apply');


                            $(".discount-block button").removeClass('icon-close');


                            $(".clearlink").hide();


                        }


                        alert("<?= addslashes($langage_lbl['LBL_MANUAL_BOOKING_INVALID_PROMOCODE']) ?>.");


                    }


                } else {

                    console.log(response.result);

                }

            });


        } else {


            alert("<?= addslashes($langage_lbl['LBL_MANUAL_BOOKING_ENTER_PROMOCODE']) ?>.");


        }


    }


    function renderdetails() {


        $(document).on('keyup', '#vPhone', function () {


            setTimeout(() => {


                if ($('#vPhone').val() != "") {


                    $('#vPhone').closest('.general-form').find('.form-group.rederdetail').each(function (index) {


                        if ($(this).find('input').val() != "") {


                            $(this).closest('.form-group.rederdetail').addClass('floating');


                        } else {


                            $(this).closest('.form-group.rederdetail').removeClass('floating');


                        }


                    });


                } else {


                    $('#vPhone').closest('.general-form').find('.form-group.rederdetail').removeClass('floating');


                }


            }, 100);


        })


        setTimeout(() => {


            if ($('#vPhone').val() != "") {


                $('#vPhone').closest('.general-form').find('.form-group.rederdetail').each(function (index) {


                    if ($(this).find('input').val() != "") {


                        $(this).closest('.form-group.rederdetail').addClass('floating');


                    } else {


                        $(this).closest('.form-group.rederdetail').removeClass('floating');


                    }


                });


            }


        }, 100);


    }


    if (cubexthemeon == 'Yes') {


        renderdetails();


    }


    $.fn.toggleInputError = function (erred) {


        $(this).closest('.form-group').toggleClass('has-error22', erred);


        return this;


    };


    function verifyphonenumber() {
        <?php if(!empty($vPhone)){ ?>
        var verifciationphone = '<?= $vPhoneCode . $vPhone; ?>';
        <?php } else { ?>
        var phonecode = '<?= $vPhoneCode;?>';
        var PhoneNo = $('#vPhone').val();
        var verifciationphone = phonecode + PhoneNo;
        <?php } ?>
        var verificationcodeinputval = $("#verificationcode").val();


        if (verificationcodeinputval != '' && verification_code == verificationcodeinputval) {


            $("#errormsg").hide();


            var dataSmsverify = {


                "tSessionId": ResponseDataArray.tSessionId,


                "GeneralMemberId": ResponseDataArray.iUserId,


                "GeneralUserType": 'Passenger',


                "MobileNo": verifciationphone,


                "type": 'sendVerificationSMS',


                "iMemberId": ResponseDataArray.iUserId,


                "UserType": 'Passenger',


                "REQ_TYPE": 'PHONE_VERIFIED',


                "vTimeZone": ResponseDataArray.vTimeZone,


            };


            dataSmsverify = $.param(dataSmsverify);


            // Added and commented by HV on 09-11-2020 as discussed with KS

            getDataFromApi(dataSmsverify, function (dataHtml) {

                var dataHtml = JSON.parse(dataHtml);

                show_alert(languagedata["LBL_SIGNUP_PHONE_VERI"], languagedata[dataHtml.message], languagedata["LBL_BTN_OK_TXT"], "", "", function (btn_id) {


                    if (btn_id == 0) {


                        $("#flex-row-error").html(" ");


                    }


                });


                return false;

            });


        } else {


            if (verificationcodeinputval != "") {


                $("#errormsg").html(languagedata['LBL_INVALID_VERIFICATION_CODE_ERROR']).show();


                return false;


            } else {


                $("#errormsg").html(languagedata['LBL_ENTER_VERIFICATION_CODE']).show();


                return false;


            }


        }


        return false;


    }


</script>



