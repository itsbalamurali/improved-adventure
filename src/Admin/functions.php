<?php $ManualBookingAPIUrl = $tconfig['tsite_url'].ManualBookingAPIUrl; ?>

<style>
    .map-live-hs-mid span.col5 input {
        margin: 10px 10px 0;
    }
    .map-main-page-inner-tab .map-live-hs-mid .form-control14 {
        margin: 0px 10px 0;
        width: 95%;
    }
    .service-pickup-type h3, .radio-but-type {
        margin-left: 12px;
    }
    .map-main-page-inner-tab .add-booking1 input {
        width: 96%;
    }
</style>
<script>

        $("#add-booking-form-taxi1").show();
        $(".map-color-code").hide();
        $('input[type=radio][name=eType]').change(function () {

            if ($('input[name=eType]:checked').val() == 'Ride' && $('input[name=eRideType]:checked').val() == 'now') {
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


        if (eType == 'Ride') {
            if ($('input[name=eRideType]:checked').val() == 'now') {
                $(".auto_assignOr").hide();
                $(".driverlists").hide();
                $("#iDriverId").val('');
                $("#iDriverId").removeAttr("required");
                $("#iDriverId").attr('disabled', 'disabled');
                $(".autoassignbtn").hide();


            } else {
                $(".auto_assignOr").show();
                $(".driverlists").show();
                $(".autoassignbtn").hide();
                $("#iDriverId").removeAttr('disabled');
    <?php if ('Edit' !== $action) { ?>
                    $("#iDriverId").attr('required', 'required');
    <?php } else { ?>
                    $("#iDriverId").removeAttr("required");
    <?php } ?>
            }
        } else if(eType == 'Deliver') {
            if ($('input[name=eDeliveryType]:checked').val() == 'now') {
                $(".auto_assignOr").hide();
                $(".driverlists").hide();
                $(".autoassignbtn").hide();
                $("#iDriverId").val('');
                $("#iDriverId").removeAttr("required");
                $("#iDriverId").attr('disabled', 'disabled');
            } else {
                $(".auto_assignOr").show();
                $(".driverlists").show();
                $(".autoassignbtn").show();
                $("#iDriverId").removeAttr('disabled');
    <?php if ('Edit' !== $action) { ?>
                    $("#iDriverId").attr('required', 'required');
    <?php } else { ?>
                    $("#iDriverId").removeAttr("required");
    <?php } ?>
            }
        }

        $('input[type=radio][name=eRideType]').change(function () {
            if (this.value == 'now') {
                $(".auto_assignOr").hide();
                $(".driverlists").hide();
                $(".autoassignbtn").hide();
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
                'isDestinationAdded': 'Yes',
                "async_request": false
            };
            data = $.param(data);

            getDataFromApi(data, function(response) {
                var response = JSON.parse(response);
                var resultarray = response.message;
                //console.log(resultarray);

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
                $("#fareEstimateModal").modal('show');
            });

            <?php /*
            $.ajax({
                type: "POST",
                url: "<?= $ManualBookingAPIUrl; ?>",
                data: {type: 'getEstimateFareDetailsArr', distance: distance_new, time: duration_new, SelectedCar: selectedcarTypeId, StartLatitude: StartLatitude, EndLongitude: EndLongitude, DestLatitude: DestLatitude, DestLongitude: DestLongitude, iUserId: iUserIdNew, UserType: 'Passenger', isDestinationAdded: 'Yes'},
                dataType: 'json',
                success: function (dataHtml) {
                    var resultarray = dataHtml.message;
                    //console.log(resultarray);

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
                    $("#fareEstimateModal").modal('show');
                    //$("html").removeClass('loading');
                },
                error: function (dataHtml) {

                }
            });*/ ?>
            // Added and commented by HV on 09-11-2020 as discussed with KS End
        } else {
            alert("<?php echo $langage_lbl['LBL_MANUAL_BOOKING_PICKUP_DROPOFF_LOCATION']; ?>.");
            return false;
        }
        return false;
    }

    function showVehicleTypeAmount(iVehicleTypeId) {
        var iUserIdNew = $('#iUserId').val();
        // $.ajax({
        //     type: "POST",
        //     url: "<?php echo $tconfig['tsite_url']; ?>ajax_ufx_service_charge_details.php",
        //     dataType: "html",
        //     data: {iVehicleTypeId: iVehicleTypeId, iUserId: iUserIdNew},
        //     success: function (dataHtml2) {
        //         if (dataHtml2 != '') {
        //             $('#iVehicleTypeData').html(dataHtml2);
        //             $('#iVehicleTypeData').show();
        //         }
        //     }, error: function (dataHtml2) {

        //     }
        // });

        var ajaxData = {
            'URL': '<?php echo $tconfig['tsite_url']; ?>ajax_ufx_service_charge_details.php',
            'AJAX_DATA': {iVehicleTypeId: iVehicleTypeId, iUserId: iUserIdNew},
            'REQUEST_DATA_TYPE': 'html'
        };
        getDataFromAjaxCall(ajaxData, function(response) {
            if(response.action == "1") {
                var dataHtml2 = response.result;
                if (dataHtml2 != '') {
                    $('#iVehicleTypeData').html(dataHtml2);
                    $('#iVehicleTypeData').show();
                }
            }
            else {
                console.log(response.result);
            }
        });
    }

    $('input[name=eRideType]').on('change', function () {
        var setVal = $(this).val();
        if (setVal == 'later') {
            $(".dateSchedule").show();
            $("#scheduleLater1").focus();
        } else {
            $(".dateSchedule").hide();
        }
    });

    $('input[name=eDeliveryType]').on('change', function () {
        var setVal = $(this).val();
        if (setVal == 'later') {
            $(".dateSchedule").show();
            $("#scheduleLater1").focus();
        } else {
            $(".dateSchedule").hide();
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
            // $.ajax({
            //     type: "POST",
            //     url: 'action_booking.php',
            //     dataType: "json",
            //     async: true,
            //     data: $("#add_booking_form").serialize(),
            //     success: function (response)
            //     {
            //         $(".loader-default").hide();
            //         $("#request-loader001").show();
            //         $("#requ_title").show();
            //         ResponseDataArray = response;
            //         loadAvailableCab();
            //         return false;
            //     }
            // });

            var ajaxData = {
                'URL': '<?php echo $tconfig['tsite_url_main_admin']; ?>action_booking.php',
                'AJAX_DATA': $("#add_booking_form").serialize(),
                'REQUEST_DATA_TYPE': 'json',
            };
            getDataFromAjaxCall(ajaxData, function(response) {
                if(response.action == "1") {
                    var data = response.result;
                    $(".loader-default").hide();
                    $("#request-loader001").show();
                    $("#requ_title").show();
                    ResponseDataArray = data;
                    loadAvailableCab();
                    return false;
                }
                else {
                    console.log(response.result);
                }
            });

            //$("#add_booking_form").submit();
            return true;

        }
    }

    function SubmitBookingForm(idClicked) {
        /*console.log($("#add_booking_form").serialize());
         return false;*/
        // $.ajax({
        //     type: "POST",
        //     url: 'action_booking.php',
        //     dataType: "html",
        //     async: true,
        //     data: $("#add_booking_form").serialize(),
        //     beforeSend: function () {
        //         $(".loader-default").show();
        //     },
        //     success: function (response)
        //     {
        //         $(".loader-default").hide();
        //         window.location.replace("cab_booking.php");
        //     }
        // });

        var ajaxData = {
            'URL': '<?php echo $tconfig['tsite_url_main_admin']; ?>action_booking.php',
            'AJAX_DATA': $("#add_booking_form").serialize(),
            'REQUEST_DATA_TYPE': 'html',
        };
        $(".loader-default").show();
        getDataFromAjaxCall(ajaxData, function(response) {
            if(response.action == "1") {
                var data = response.result;
                $(".loader-default").hide();
                window.location.replace("cab_booking.php");
            }
            else {
                console.log(response.result);
                $(".loader-default").hide();
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
            "async_request": false
        };

        data = $.param(data);
        // Added and commented by HV on 09-11-2020 as discussed with KS
        getDataFromApi(data, function(response) {
            var response = JSON.parse(response);
            var AvailableCabList = response.AvailableCabList;
            var AvailableDriverIds = [];
            $.each(AvailableCabList, function (key, value) {
                AvailableDriverIds.push(value.iDriverId);
            });
            // sendrequestto driver
            SendRequestToDriver(AvailableDriverIds);
            return false;
        });

        <?php /*
        $.ajax({
            type: "POST",
            dataType: "json",
            url: "<?= $ManualBookingAPIUrl; ?>",
            data: data,
            async: false,
            success: function (response) {
                var AvailableCabList = response.AvailableCabList;
                var AvailableDriverIds = [];
                $.each(AvailableCabList, function (key, value) {
                    AvailableDriverIds.push(value.iDriverId);
                });
                // sendrequestto driver
                SendRequestToDriver(AvailableDriverIds);
                return false;
            }
        });*/ ?>
        // Added and commented by HV on 09-11-2020 as discussed with KS End
    }

    function SendRequestToDriver(AvailableDriverIds) {
        //$(".loader-default").hide();
        /*$("#request-loader001").show();
         $("#requ_title").show();*/
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
                "async_request": false
            };
            sendrequestparam = $.param(sendrequestparam);

            // Added and commented by HV on 09-11-2020 as discussed with KS
            getDataFromApi(sendrequestparam, function(response) {
                var response = JSON.parse(response);
                if (response.Action == '1') {
                    getAcceptedDriver001();
                } else {
                    //alert(response.Message);
                    alert("<?php echo $langage_lbl['LBL_NO_CARS_AVAIL_IN_TYPE']; ?>");
                    $("#request-loader001").hide();
                    $("#requ_title").hide();
                }
            });

            <?php /*
            $.ajax({
                type: "POST",
                dataType: "json",
                async: false,
                url: "<?= $ManualBookingAPIUrl; ?>",
                data: sendrequestparam,
                success: function (response) {
                    if (response.Action == '1') {

                        getAcceptedDriver001();
                    } else {
                        //alert(response.Message);
                        alert("<?php echo $langage_lbl['LBL_NO_CARS_AVAIL_IN_TYPE']; ?>");
                        $("#request-loader001").hide();
                        $("#requ_title").hide();
                    }
                }
            });
            */ ?>
            // Added and commented by HV on 09-11-2020 as discussed with KS End
        } else {
            $("#request-loader001").hide();
            $("#requ_title").hide();
            alert("<?php echo $langage_lbl['LBL_MANUAL_BOOKING_NO_DRIVER_AVAILABLE']; ?>.");
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
            getDataFromApi(data, function(response) {
                if (response.trim() != "") {
                    var obj = jQuery.parseJSON(response);
                    if (obj.message != null) {
                        var messagenew = jQuery.parseJSON(obj.message);
                        if (messagenew.Message == 'CabRequestAccepted') {
                            clearInterval(interval3);
                            $("#request-loader001").hide();
                            // $("#driver-bottom-set001").show();
                            // $("#driver-bottom-set001").html(response);
                            // $.ajax({
                            //     type: 'post',
                            //     dataType: "html",
                            //     async: false,
                            //     url: 'fetch_record.php', //Here you will fetch records
                            //     data: {iMemberId: ResponseDataArray.iUserId, vLatitude: ResponseDataArray.vSourceLatitude, vLongitude: ResponseDataArray.vSourceLongitude, vTimeZone: ResponseDataArray.vTimeZone}, //Pass $id
                            //     success: function (data) {
                            //         $('#driver-bottom-set001').html(data);//Show fetched data from database
                            //     }
                            // });

                            var ajaxData = {
                                'URL': '<?php echo $tconfig['tsite_url_main_admin']; ?>fetch_record.php',
                                'AJAX_DATA': {iMemberId: ResponseDataArray.iUserId, vLatitude: ResponseDataArray.vSourceLatitude, vLongitude: ResponseDataArray.vSourceLongitude, vTimeZone: ResponseDataArray.vTimeZone},
                                'REQUEST_DATA_TYPE': 'html',
                                'REQUEST_ASYNC': false,
                            };
                            getDataFromAjaxCall(ajaxData, function(response) {
                                if(response.action == "1") {
                                    var data = response.result;
                                    $('#driver-bottom-set001').html(data);
                                }
                                else {
                                    console.log(response.result);
                                }
                            });
                            // $('#driverData').modal('show');
                            $('#driverData').modal({
                                backdrop: 'static',
                                keyboard: false
                            });
                            $("#btnYes").on("click", function (e) {
                                window.location.replace("trip.php");
                            });
                            return false;
                        } else {
                            $('#driverData').modal('hide');
                        }
                    }
                }
            },"text");

            <?php /*
            $.ajax({
                type: "POST",
                url: "<?= $ManualBookingAPIUrl; ?>",
                async: false,
                data: {type: 'configPassengerTripStatus', "GeneralMemberId": ResponseDataArray.iUserId,
                    "GeneralUserType": 'Passenger', iMemberId: ResponseDataArray.iUserId, vLatitude: ResponseDataArray.vSourceLatitude, vLongitude: ResponseDataArray.vSourceLongitude, vTimeZone: ResponseDataArray.vTimeZone, "tSessionId": ResponseDataArray.tSessionId},
                success: function (dataHtml3) {
                    if (dataHtml3.trim() != "") {
                        var obj = jQuery.parseJSON(dataHtml3);
                        if (obj.message != null) {
                            var messagenew = jQuery.parseJSON(obj.message);
                            if (messagenew.Message == 'CabRequestAccepted') {
                                clearInterval(interval3);
                                $("#request-loader001").hide();
                                //	 $("#driver-bottom-set001").show();
                                 // $("#driver-bottom-set001").html(dataHtml3);
                                $.ajax({
                                    type: 'post',
                                    dataType: "html",
                                    async: false,
                                    url: 'fetch_record.php', //Here you will fetch records
                                    data: {iMemberId: ResponseDataArray.iUserId, vLatitude: ResponseDataArray.vSourceLatitude, vLongitude: ResponseDataArray.vSourceLongitude, vTimeZone: ResponseDataArray.vTimeZone}, //Pass $id
                                    success: function (data) {
                                        $('#driver-bottom-set001').html(data);//Show fetched data from database
                                    }
                                });
                                // $('#driverData').modal('show');
                                $('#driverData').modal({
                                    backdrop: 'static',
                                    keyboard: false
                                });
                                $("#btnYes").on("click", function (e) {
                                    window.location.replace("trip.php");
                                });
                                return false;
                            } else {
                                $('#driverData').modal('hide');
                            }
                        }
                    }
                },
                error: function (dataHtml3) {
                }
            }); */ ?>
            // Added and commented by HV on 09-11-2020 as discussed with KS End
        }, 6000);
    }

    $(document).ready(function () {
        $('#retryBtn').click(function () {
            $('#req_try_again').hide();
            $('.requesting-popup-sub').show();
            setTimeout(function () {
                $('#req_try_again').show();
                $('.requesting-popup-sub').hide();
            }, 45000);
            loadAvailableCab();
        });
    });

    function cancellingRequestDriver() {
        if (confirm("Are you sure you want to cancel this request?")) {

            // Added and commented by HV on 09-11-2020 as discussed with KS
            var data = {
                'type': 'cancelCabRequest',
                'iUserId': ResponseDataArray.iUserId,
                "GeneralMemberId": ResponseDataArray.iUserId,
                "GeneralUserType": 'Passenger',
                "tSessionId": ResponseDataArray.tSessionId,
                "async_request": false
            };
            data = $.param(data);

            getDataFromApi(data, function(response) {
                var response = JSON.parse(response);
                if (response) {
                    $("#request-loader001").hide();
                    $('#add_booking_form').trigger("reset");
                    window.location.replace("add_booking.php");
                }
            });
            <?php /*
            $.ajax({
                type: "POST",
                url: "<?= $ManualBookingAPIUrl; ?>",
                dataType: "json",
                async: false,
                data: {type: 'cancelCabRequest', iUserId: ResponseDataArray.iUserId, "GeneralMemberId": ResponseDataArray.iUserId, "GeneralUserType": 'Passenger', "tSessionId": ResponseDataArray.tSessionId},
                success: function (dataHtml2) {
                    if (dataHtml2) {
                        $("#request-loader001").hide();
                        $('#add_booking_form').trigger("reset");
                        window.location.replace("add_booking.php");
                    }
                },
                error: function (dataHtml2) {}
            }); */ ?>
            // Added and commented by HV on 09-11-2020 as discussed with KS End

            $("#request-loader001").hide();
            $("#cancelRequestDriver").hide();
        } else {
            return false;
        }
    }

    function showReTry() {
        setTimeout(function () {
            $('#req_try_again').show();
            $('.requesting-popup-sub').hide();
        }, 45000);
    }

    function checkPromoCode(bookfrom) {

        /*if($('#_Payment_form').valid()) { // Check card form is valid
         alert("Promo code Applied Successfully.");
         }*/

        var iUserId = $("#iUserId").val();
        var PromoCode = $("#promocode").val();
        //var vehicleTypeId = $('#iVehicleTypeId').find(":selected").val();

        if(eType=='Ride') {
            var vehicleTypeId = $('input[name=iDriverVehicleId_ride]:checked').val();
        } else if(eType=='Deliver') {
            var vehicleTypeId = $('input[name=iDriverVehicleId_delivery]:checked').val();
        } else {
            var vehicleTypeId = $('#iVehicleTypeId').find(":selected").val();
        }
        if(bookfrom=='Company') {
            var vehicleTypeId = $('#iVehicleTypeId').find(":selected").val();
        }

        if (PromoCode != "") {
            // $.ajax({
            //     type: "POST",
            //     url: "booking/ajaxcheckpromoCode.php",
            //     dataType: "json",
            //     data: {type: 'CheckPromoCode', PromoCode: PromoCode, iUserId: iUserId},
            //     success: function (dataHtml15) {

            //         if (dataHtml15.Action == 1 && dataHtml15.message == 'LBL_PROMO_APPLIED') {
            //             alert("<?php echo $langage_lbl['LBL_PROMO_APPLIED']; ?>");
            //             showAsVehicleType(vehicleTypeId);
            //         } else if (dataHtml15.Action == 1 && dataHtml15.message == 'LBL_PROMOCODE_ALREADY_USED') {
            //             $("#promocode").val('');
            //             alert("<?php echo $langage_lbl['LBL_PROMOCODE_ALREADY_USED']; ?>");
            //         } else if (dataHtml15.Action == 1 && dataHtml15.message == 'LBL_PROMOCODE_COMPLETE_USAGE_LIMIT') {
            //             $("#promocode").val('');
            //             alert("<?php echo $langage_lbl['LBL_PROMOCODE_COMPLETE_USAGE_LIMIT']; ?>");
            //         } else if (dataHtml15.Action == 1 && dataHtml15.message == 'LBL_PROMOCODE_EXPIRED') {
            //             $("#promocode").val('');
            //             alert("<?php echo $langage_lbl['LBL_PROMOCODE_EXPIRED']; ?>");
            //         } else {
            //             $("#promocode").val('');
            //             alert("<?php echo $langage_lbl['LBL_MANUAL_BOOKING_INVALID_PROMOCODE']; ?>.");
            //         }
            //     },
            //     error: function (dataHtml15) {
            //     }
            // });

            var ajaxData = {
                'URL': '<?php echo $tconfig['tsite_url']; ?>booking/ajaxcheckpromoCode.php',
                'AJAX_DATA': {type: 'CheckPromoCode', PromoCode: PromoCode, iUserId: iUserId},
                'REQUEST_DATA_TYPE': 'json'
            };
            getDataFromAjaxCall(ajaxData, function(response) {
                if(response.action == "1") {
                    var dataHtml15 = response.result;
                    if (dataHtml15.Action == 1 && dataHtml15.message == 'LBL_PROMO_APPLIED') {
                        alert("<?php echo $langage_lbl['LBL_PROMO_APPLIED']; ?>");
                        showAsVehicleType(vehicleTypeId);
                    } else if (dataHtml15.Action == 1 && dataHtml15.message == 'LBL_PROMOCODE_ALREADY_USED') {
                        $("#promocode").val('');
                        alert("<?php echo $langage_lbl['LBL_PROMOCODE_ALREADY_USED']; ?>");
                    } else if (dataHtml15.Action == 1 && dataHtml15.message == 'LBL_PROMOCODE_COMPLETE_USAGE_LIMIT') {
                        $("#promocode").val('');
                        alert("<?php echo $langage_lbl['LBL_PROMOCODE_COMPLETE_USAGE_LIMIT']; ?>");
                    } else if (dataHtml15.Action == 1 && dataHtml15.message == 'LBL_PROMOCODE_EXPIRED') {
                        $("#promocode").val('');
                        alert("<?php echo $langage_lbl['LBL_PROMOCODE_EXPIRED']; ?>");
                    } else {
                        $("#promocode").val('');
                        alert("<?php echo $langage_lbl['LBL_MANUAL_BOOKING_INVALID_PROMOCODE']; ?>.");
                    }
                }
                else {
                    console.log(response.result);
                }
            });
        } else {
            alert("<?php echo $langage_lbl['LBL_MANUAL_BOOKING_ENTER_PROMOCODE']; ?>.");
        }
    }



    $.fn.toggleInputError = function (erred) {
        $(this).closest('.form-group').toggleClass('has-error22', erred);
        return this;
    };

</script>
