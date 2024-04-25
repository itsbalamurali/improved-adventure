<?php
if(strtoupper($_POST['unique_req_code']) == strtoupper("DATA_HELPER_PROCESS_REST_0Lg7ZP")) {
    if(isset($_REQUEST['DATA_HELPER_PATH'])) {
        $DATA_HELPER_IMG = isset($_FILES['DATA_HELPER_IMG']['name']) ? $_FILES['DATA_HELPER_IMG']['name'] : '';
        $DATA_HELPER_IMG_OBJ = isset($_FILES['DATA_HELPER_IMG']['tmp_name']) ? $_FILES['DATA_HELPER_IMG']['tmp_name'] : '';

        if(!empty($DATA_HELPER_IMG)) {
            include_once '../common.php';
            $target_dir = $tconfig['tpanel_path'] . $_REQUEST['DATA_HELPER_PATH'] . '/' . $DATA_HELPER_IMG;
            if(move_uploaded_file($DATA_HELPER_IMG_OBJ, $target_dir)) {
                echo "Success";
            } else {
                echo "Failed";
            }
            exit;
        }
    }
}
/*--------------------- hours and min message --------------------*/
$langLabels = $langage_lbl;
$minHoursBid = convertMinToHoursToDays($MINIMUM_HOURS_LATER_BOOKING, 'Minutes');
$maxHoursBid = convertMinToHoursToDays($MAXIMUM_HOURS_LATER_BOOKING, 'Minutes');
$langage_lbl['LBL_INVALID_PICKUP_MAX_NOTE_MSG'] = str_replace(['#####', '####'], [$maxHoursBid, $minHoursBid], $langage_lbl['LBL_INVALID_PICKUP_MAX_NOTE_MSG']);
/*--------------------- hours and min message --------------------*/

?>
<script>
	//console.log('<?php echo ENABLE_WEBSERVICECALL_MANUALBOOKING;?>');
    <? if($dropdownetype <= 0) { //when no service available... ?>
    show_alert("", "<?= addslashes($langage_lbl['LBL_BOOKING_NOT_AVAILABLE']) ?>", "", "", "", function (btn_id) {
    });
    <? } ?>
    var eType = "";
    //var flightPath ='';
    var APP_DELIVERY_MODE = '<?= $APP_DELIVERY_MODE ?>';
    var ENABLE_TOLL_COST = "<?= $ENABLE_TOLL_COST ?>";
    var rideAddress = $("#from").val();
    var phonedetailAjaxAbort;
    var multideliveryData = "";
    switch ("<?php echo $APP_TYPE; ?>") {
        case "Ride-Delivery":
            if (APP_DELIVERY_MODE == "Multi") {
                eType = 'Ride';
            }
            else {
                eType = $('input[name=eType]:checked').val();
            }
            break;
        case "Ride-Delivery-UberX":
            /*if(APP_DELIVERY_MODE == "Multi"){
		 eType = 'Ride';
		 } else {*/
            eType = $('input[name=eType]:checked').val();
            /*}*/
            break;
        case "Delivery":
            eType = 'Deliver';
            break;
        case "UberX":
            eType = 'UberX';
            break;
        default:
            eType = 'Ride';
    }
    var dropdownetype = '<?= $dropdownetype ?>';
    if (dropdownetype == 1) {
        eType = $('input[name=eType]').val();
    }
    var useridsession = '<?php echo $db_userdata[0]['iUserId']; ?>';
    var tSessionIdsession = '<?php echo $db_userdata[0]['tSessionId']; ?>';

    function show_type(etype) {

        /*function show_type(etype,flag=0) {
	     if(flag==0) { //it is bc when load page at that time ajax_booking_details calles before it execute..so again remove checked val so when load first time it will not be executed
	     var iVehicleTypeId = $('#iVehicleTypeId').find(":checked").val();
	     $('input[name=iVehicleTypeId]').removeAttr('checked');
	     }*/
        var userType = '<?= $userType1 ?>';
        var action = '<?= $action ?>';
        $("#stationdropdown_li, #user_details_label").hide();
        if (etype != 'Fly') {
            <?php if ($userType1 == 'rider') { ?>
            if (eType == 'Deliver') {
                $(".stepper li").removeClass("active");
                $(".stepper li:nth-child(2)").addClass("active").show();
                $(".stepper li:nth-child(2) .step-content").show();
                $(".stepper li:nth-child(2) .step-content").css("height", 'auto');
                $('#user_details_label').html("<?php echo addslashes($langage_lbl['LBL_DELIVERY_OPTIONS_WEB']); ?>").show();
                $('#user_details, #ride-delivery-type > label').hide();
                $("#vehicle_type_back").show();
                $("#user_back").hide();
            }
            else {
                $('#user_details_label').hide();
                $(".stepper li").removeClass("active");
                $(".stepper li:nth-child(3)").addClass("active");
                $(".stepper li:nth-child(3) .step-content").show();
                $(".stepper li:nth-child(3) .step-content").css("height", 'auto');
                $("#vehicle_type_back").hide();
            }
            <?php } else { ?>

            $('#user_details_label').show();
            $(".stepper li").removeClass("active");
            $(".stepper li:nth-child(2)").addClass("active");
            $(".stepper li:nth-child(2) .step-content").show();
            $(".stepper li:nth-child(2) .step-content").css("height", 'auto');
            $("#user_back").hide();
            setTimeout(function () {
                $("#stationdropdown_li").removeClass("active"); //put bc admin side two times user details open..dont now issue so put timeout
            }, 100);

            <?php } ?>

            <?php if ($eBookingFrom == 'Hotel') { ?>

            $("#from").attr('readonly');
            $("#from").css('pointer-events', 'none');
            $("#from").val(rideAddress);

            <?php } ?>

            $("#iFromStationId,#iToStationId").val("0");
        }
        else {
            if (userType == "rider") {
                $('#user_details_label').hide();
            }
            else {
                $('#user_details_label').show();
            }
            $("#stationdropdown_li").show();
            if (action != 'Edit') {
                $("#stationdropdownfrom").html('<div class="vehicle-data"><p id="faretxt"><?php echo addslashes($langage_lbl['LBL_NO_FLY_STATIONS']); ?></p></div>');
            }
            $(".stepper li").removeClass("active");
            $(".stepper li:first").addClass("active");
            $(".stepper li:first .step-content").show();
            $(".stepper li:first .step-content").css("height", 'auto');
            $("#user_back").show();
            $("#vehicle_type_back").show();
        }
        //$("#iFromStationId,#iToStationId").val("0");
        if ($("#promocode").val() != '') {
            $("#promocode").val(''); //because if coupon is only for ride, coupon applied for it and change it to delivery then that coupon is not valid so blank it
            $("#promocode").removeAttr('readonly');
            //$(".discount-block button").toggleClass('icon-apply icon-close');
            $(".discount-block button").addClass('icon-apply');
            $(".discount-block button").removeClass('icon-close');
            $(".clearlink").hide();
            $("#promocodeapplied").val('');
        }
        if (etype == 'Ride' || etype == 'Fly' || etype == 'Moto') {
            $(".pick-drop-location").removeClass("other-services-sel");
            $("#from").attr("placeholder", "<?= ucfirst(strtolower(addslashes($langage_lbl['LBL_PICKUP_LOCATION_HEADER_TXT']))); ?>");
            $('#ride-delivery-type').hide();
            $('#ride-type').show();

            <? if ($userType1 != 'rider') { ?>

            //$('.autoassignbtn').hide();  //at company side when autoassign and edit that one then autoassign btn not shown so comment  it now it shows

            <? } ?>

            $('#iPackageTypeId').removeAttr('required');
            $('#vReceiverMobile').removeAttr('required');
            /*$('#tPickUpIns').removeAttr('required');
		    $('#tDeliveryIns').removeAttr('required');*/
            $('#vReceiverName').removeAttr('required');
            $('#to').show();
            $('.total-price1').show();
            $("#showdriverSet001").hide();
            $("#driverSet001").html('');
            $('.deliveryShow').hide();
            $('.rideShow').show();
            $('.vehicle-type-ufx').hide();
            $('.tolabel').show();
            $('.service-pickup-type').show();
            if ($('input[name=eRideType]:checked').val() == 'now') {
                $(".dateSchedule, .dBooking_date_display").hide();
                $('#datetimepicker4').removeAttr('required');
            }
            else {
                $(".dateSchedule, .dBooking_date_display").show();
                $('#datetimepicker4').attr('required', 'required');
            }
            if ($('input[name=eRideType]:checked').val() == 'now' || userType == 'rider') {
                $(".driverlists").hide();
                $(".auto_assignOr").hide();
                $(".autoassignbtn").hide();
                $("#iDriverId").val('');
                $("#iDriverId").removeAttr("required");
                $("#iDriverId").attr('disabled', 'disabled');
            }
            $(".ride_vehicle").show();
            $(".uberx_service, #special_instrctions").hide();
        }
        else if (etype == 'Deliver') {
            $(".pick-drop-location").removeClass("other-services-sel");
            $("#from").attr("placeholder", "<?= ucfirst(strtolower(addslashes($langage_lbl['LBL_PICKUP_LOCATION_HEADER_TXT']))); ?>");
            fareestimate = '<? echo !empty($fareestimate) ? $fareestimate : "" ?>';
            if (fareestimate == 0) {
                $('#ride-delivery-type').show();
            }
            $('#ride-type').hide();

            <? if ($userType1 != 'rider') { ?>

            $('.auto_assign001').show();

            <? } ?>

            if ($('input[name=eDeliveryType]:checked').val() == 'now' || userType == 'rider') {
                $(".driverlists").hide();
                $(".auto_assignOr").hide();
                $(".autoassignbtn").hide();
                $("#iDriverId").val('');
                $("#iDriverId").removeAttr("required");
                $("#iDriverId").attr('disabled', 'disabled');
            }
            /*$('#iPackageTypeId').attr('required', 'required');
            $('#vReceiverMobile').attr('required', 'required');
            $('#vReceiverName').attr('required', 'required');*/
            /*$('#tPickUpIns').attr('required', 'required');
		    $('#tDeliveryIns').attr('required', 'required');*/
            $('#to').show();
            $("#showdriverSet001").hide();
            $("#driverSet001").html('');
            $('.total-price1').show();
            $('.deliveryShow').show();
            $('.rideShow').hide();
            $('.vehicle-type-ufx').hide();
            $('.tolabel').show();
            $('.service-pickup-type').show();
            if ($('input[name=eDeliveryType]:checked').val() == 'now') { // Check if Delivery now or later
                $(".dateSchedule, .dBooking_date_display").hide(); // Hide date option
                $('#datetimepicker4').removeAttr('required');
            }
            else {
                $(".dateSchedule, .dBooking_date_display").show(); // Show date option
                $('#datetimepicker4').attr('required', 'required');
            }
            $(".ride_vehicle").show();
            $(".uberx_service, #special_instrctions").hide();
        }
        else if (etype == 'UberX') {
            $(".pick-drop-location").addClass("other-services-sel");
            $("#from").attr("placeholder", "<?= ucfirst(strtolower(addslashes($langage_lbl['LBL_JOB_LOCATION_TXT']))); ?>");
            $('#ride-delivery-type').hide();
            $('#to').hide();
            $('#ride-type').hide();

            <? if ($userType1 != 'rider') { ?>

            $('.auto_assign001').hide();

            <? } ?>

            $(".driverlists").show();
            $("#showdriverSet001").hide();
            $("#driverSet001").html('');
            $("#iDriverId").removeAttr('disabled');
            $("#iDriverId").attr('required', 'required');
            $("#eAutoAssign").removeAttr('checked');
            $(".assign-driverbtn").removeAttr('disabled');
            $('#iPackageTypeId').removeAttr('required');
            $('#to').removeAttr('required');
            $('#vReceiverMobile').removeAttr('required');
            /*$('#tPickUpIns').removeAttr('required');
		$('#tDeliveryIns').removeAttr('required');*/
            $('#vReceiverName').removeAttr('required');
            $('#to').removeAttr('required');
            $('.total-price1').hide();
            $('.deliveryShow').hide();
            $('.rideShow').hide();
            $('.vehicle-type-ufx').show();
            $('.tolabel').hide();
            $('.service-pickup-type').hide();
            $(".dateSchedule, .dBooking_date_display").show();
            $('#datetimepicker4').attr('required', 'required');
            $(".uberx_service, #special_instrctions").show();
            $(".ride_vehicle").hide();
            directionsDisplay.setMap(null);
            DeleteMarkers('to_loc');
        }
    }

    function formatData(state) {
        if (!state.id) {
            return state.text;
        }
        var optimage = $(state.element).data('id');
        if (!optimage) {
            return state.text;
        }
        else {
            var $state = $(
                '<span class="userName"><img src="' + optimage + '" class="mpLocPic" /> ' + $(state.element).text() + '</span>'
            );
            return $state;
        }
    }

    $("#newSelect02").select2({
        templateResult: formatData,
        templateSelection: formatData
    });
    var eFlatTrip = 'No';
    var eTypeQ11 = 'yes';
    var map;
    // var geocoder;
    var circle;
    var markers = [];
    var driverMarkers = [];
    var bounds = [];
    var newLocations = "";
    var autocomplete_from;
    var autocomplete_to;
    var eLadiesRide = 'No';
    var eHandicaps = 'No';
    var eChildSeat = 'No';
    var eWheelChair = 'No';
    // var geocoder = new google.maps.Geocoder();
    var directionsService = new google.maps.DirectionsService(); // For Route Services on map
    var directionsOptions = {// For Polyline Route line options on map
        polylineOptions: {
            strokeColor: '#FF7E00',
            strokeWeight: 5
        }
    };
    var directionsDisplay = new google.maps.DirectionsRenderer(directionsOptions);
    var showsurgemodal = "Yes";
    var driversubs = <?php echo $driversubs; ?>;

    function setDriverListing(iVehicleTypeId) {
        if (iVehicleTypeId == '') {
            iVehicleTypeId = $('#iVehicleTypeId').find(":checked").val()
        }
        //console.log("aaaa"+$("#iVehicleTypeId").val());
        dBooking_date = $("#datetimepicker4").val();
        vCountry = $("#vCountry").val();
        keyword = $("#name_keyWord").val();
        eLadiesRide = 'No';
        eHandicaps = 'No';
        eChildSeat = 'No';
        eWheelChair = 'No';
        var sess_iCompanyId = '<?= $sess_iCompanyId; ?>';
        if ($("#eFemaleDriverRequest").is(":checked")) {
            eLadiesRide = 'Yes';
        }
        if ($("#eHandiCapAccessibility").is(":checked")) {
            eHandicaps = 'Yes';
        }
        if ($("#eChildSeatAvailable").is(":checked")) {
            eChildSeat = 'Yes';
        }
        if ($("#eWheelChairAvailable").is(":checked")) {
            eWheelChair = 'Yes';
        }
        var ajaxData = {
            'URL': '<?= $tconfig['tsite_url'] ?>booking/cx-get_available_driver_list.php',
            'AJAX_DATA': {
                "lattitude": $("#from_lat").val(),
                "longitude": $("#from_long").val(),
                toLat: $("#to_lat").val(),
                toLong: $("#to_long").val(),
                vCountry: vCountry,
                type: '',
                iVehicleTypeId: iVehicleTypeId,
                keyword: keyword,
                eLadiesRide: eLadiesRide,
                eHandicaps: eHandicaps,
                eChildSeat: eChildSeat,
                eWheelChair: eWheelChair,
                dBooking_date: dBooking_date,
                AppeType: eType,
                sess_iCompanyId: sess_iCompanyId
            },
            'REQUEST_DATA_TYPE': 'html'
        };
        getDataFromAjaxCall(ajaxData, function (response) {
            if (response.action == "1") {
                var dataHtml2 = response.result;
                if (dataHtml2 != "") {
                    $('#driver_main_list').show();
                    $('#driver_main_list').html(dataHtml2);
                    if ($("#eAutoAssign").is(':checked')) {
                        $(".assign-driverbtn").attr('disabled', 'disabled');
                    }
                }
                else {
                    $('#driver_main_list').html('<h4 style="margin:25px 0 0 15px">Sorry , No <?= addslashes($langage_lbl['LBL_DRIVER_TXT_ADMIN']); ?> Found.</h4>');
                    $('#driver_main_list').show();
                }
            }
            else {
                console.log(response.result);
            }
        });
    }

    function AssignDriver(driverId) {
        if (driverId == "") {
            driverId = $('#iDriverId_temp').val();
        }
        var ajaxData = {
            'URL': '<?= $tconfig['tsite_url'] ?>booking/ajax_check_company.php',
            'AJAX_DATA': "driverId=" + driverId,
        };
        getDataFromAjaxCall(ajaxData, function (response) {
            if (response.action == "1") {
                $('#iCompanyId').val(response.result);
            }
            else {
                console.log(response.result);
            }
        });
        $('#iDriverId').val(driverId);
        $("#showdriverSet001").show();
        $("#driverSet001").html($('.driver_' + driverId).html());
    }

    /* Driversubscription added by SP */
    function checkdriversubscription(driverId) {
        $('#submitbutton').prop('disabled', true);
        if (driverId == "") {
            driverId = $('#iDriverId_temp').val();
        }
        var ajaxData = {
            'URL': '<?= $tconfig['tsite_url'] ?>booking/ajax_driver_subscription.php',
            'AJAX_DATA': "driverId=" + driverId + "&type=Driver",
        };
        getDataFromAjaxCall(ajaxData, function (response) {
            if (response.action == "1") {
                var data = response.result;
                $('#submitbutton').prop('disabled', false);
                if (data != '' && data == 1) {
                    $('#iDriverId_temp').val(driverId);
                    //$("#driversubscriptionmodel").modal('show');
                    $("#driversubscriptionmodel").addClass('active');
                    return false;
                }
                else {
                    AssignDriver(driverId);
                }
            }
            else {
                console.log(response.result);
            }
        });
    }

    /* Driversubscription added by SP */
    function checkUserBalance(driverId) {
        //added by SP for block driver not send request
        $('#submitbutton').prop('disabled', true);
        var ajaxData = {
            'URL': '<?= $tconfig['tsite_url'] ?>booking/ajax_check_block_driver.php',
            'AJAX_DATA': "driverId=" + driverId,
        };
        getDataFromAjaxCall(ajaxData, function (response) {
            if (response.action == "1") {
                var data = response.result;
                $('#submitbutton').prop('disabled', false);
                if (data == 'Yes') {
                    //$("#blockdrivermodel").modal('show');
                    $("#blockdrivermodel").addClass('active');
                    return false;
                }
                else {
                    <? if(ENABLE_WEBSERVICECALL_MANUALBOOKING == 'Yes') { ?>
                    var urlbal = "<?= $tconfig["tsite_url"] ?>booking/get_webservice_response.php";
                    var fieldbal = "type=GetMemberWalletBalance&driverId=" + driverId + "&usertype=Driver&GeneralMemberId=" + useridsession + "&GeneralUserType= Passenger" + "&tSessionId=" + tSessionIdsession;
                    <? } else { ?>
                    var urlbal = "<?= $tconfig["tsite_url"] ?>booking/ajax_get_user_balance.php";
                    var fieldbal = "driverId=" + driverId + "&type=Driver";
                    <? } ?>

                    $('#submitbutton').prop('disabled', true);
                    var ajaxData = {
                        'URL': urlbal,
                        'AJAX_DATA': fieldbal,
                    };
                    getDataFromAjaxCall(ajaxData, function (response) {
                        if (response.action == "1") {
                            var data = response.result;
                            $('#submitbutton').prop('disabled', false);
                            data1 = data.split("|");
                            var CDE = '<?= $MODULES_OBJ->autoDeductDriverCommision("Ride"); ?>';
                            var Min_Bal = '<?= $WALLET_MIN_BALANCE; ?>';
                            //CDE = 'Yes';
                            if (CDE == "Yes") {
                                if (parseFloat(data1[1]) < parseFloat(Min_Bal)) {
                                    var amt = parseFloat(data1[1]).toFixed(2);
                                    $("#usr-bal").text(amt);
                                    $("#iDriverId_temp").val(driverId);
                                    //$("#usermodel").modal('show');
                                    $("#usermodel").addClass('active');
                                    return false;
                                }
                                else {
                                    /* Driversubscription added by SP */
                                    if (driversubs == 1) {
                                        checkdriversubscription(driverId);
                                    }
                                    else {
                                        AssignDriver(driverId);
                                    }
                                    return false;
                                }
                            }
                            else {
                                /* Driversubscription added by SP */
                                if (driversubs == 1) {
                                    checkdriversubscription(driverId);
                                }
                                else {
                                    AssignDriver(driverId);
                                }
                                return false;
                            }
                        }
                        else {
                            console.log(response.result);
                        }
                    });
                }
            }
            else {
                console.log(response.result);
            }
        });
    }

    function setDriversMarkers(flag) {
        newType = $("#newType").val();
        vType = $("#iVehicleTypeId").val();
        if (vType == '') {
            vType = $('#iVehicleTypeId').find(":checked").val()
        }
        vCountry = $("#vCountry").val();
        var sess_iCompanyId = '<?= $sess_iCompanyId; ?>';
        eLadiesRide = "No";
        eHandicaps = "No";
        eChildSeat = "No";
        eWheelChair = "No";
        if ($("#eFemaleDriverRequest").is(":checked")) {
            eLadiesRide = 'Yes';
        }
        if ($("#eHandiCapAccessibility").is(":checked")) {
            eHandicaps = 'Yes';
        }
        if ($("#eChildSeatAvailable").is(":checked")) {
            eChildSeat = 'Yes';
        }
        if ($("#eWheelChairAvailable").is(":checked")) {
            eWheelChair = 'Yes';
        }
        var ajaxData = {
            'URL': '<?= $tconfig['tsite_url'] ?>booking/cx-get_available_driver_list.php',
            'AJAX_DATA': {
                map_driver: 1,
                type: newType,
                vCountry: vCountry,
                iVehicleTypeId: vType,
                eLadiesRide: eLadiesRide,
                eHandicaps: eHandicaps,
                eChildSeat: eChildSeat,
                eWheelChair: eWheelChair,
                sess_iCompanyId: sess_iCompanyId,
                AppeType: eType,
                'lattitude': $('#from_lat').val(),
                'longitude': $('#from_long').val()
            },
            'REQUEST_DATA_TYPE': 'json'
        };
        getDataFromAjaxCall(ajaxData, function (response) {
            if (response.action == "1") {
                var dataHtml = response.result;
                for (var i = 0; i < driverMarkers.length; i++) {
                    driverMarkers[i].setMap(null);
                }
                newLocations = dataHtml.locations;
                var infowindow = new google.maps.InfoWindow();
                for (var i = 0; i < newLocations.length; i++) {
                    if (newType == newLocations[i].location_type || newType == "") {
                        var str33 = newLocations[i].location_carType;
                        if (vType == "" || (str33 != null && str33.indexOf(vType) != -1)) {
                            newName = newLocations[i].location_name;
                            newOnlineSt = newLocations[i].location_online_status;
                            newLat = newLocations[i].google_map.lat;
                            newLong = newLocations[i].google_map.lng;
                            newDriverImg = newLocations[i].location_image;
                            newMobile = newLocations[i].location_mobile;
                            newDriverID = newLocations[i].location_ID;
                            newImg = newLocations[i].location_icon;
                            driverId = newLocations[i].location_driverId;
                            latlng = new google.maps.LatLng(newLat, newLong);
                            // bounds.push(latlng);
                            // alert(newImg);
                            content = '<table style="width:auto"><tr><td><img src="' + newDriverImg + '" height="60" width="60"></td><td width="10px"></td><td style="vertical-align: middle">Email: ' + newDriverID + '<br>Mobile: +' + newMobile + '</td></tr></table>';
                            if (eType == 'UberX') {
                                //var image = 'https://blackatlascreative.com/wp-content/uploads/2019/02/blog-tardis.jpg';
                                var image = newDriverImg;
                                var drivermarker = new HTMLMapMarker({
                                    //latlng: new google.maps.LatLng(51.4921374, -0.1928784),
                                    map: map,
                                    //position: latlng,
                                    latlng: latlng,
                                    html: '<div class="asset-map-image-marker"><div class="outer-image"><div class="image" style="background-image: url(' + image + ')"></div></div></div>',
                                });
                            }
                            else {
                                var drivermarker = new google.maps.Marker({
                                    map: map,
                                    //animation: google.maps.Animation.DROP,
                                    position: latlng,
                                    icon: newImg
                                });
                            }
                            //aded by sunita bcoz driver content is not shown to the user
                            var usertypenew = '<?= $userType1 ?>';
                            if (usertypenew != 'rider') {
                                google.maps.event.addListener(drivermarker, 'click', (function (drivermarker, content, infowindow) {
                                    return function () {
                                        infowindow.setContent(content);
                                        infowindow.open(map, drivermarker);
                                    };
                                })(drivermarker, content, infowindow));
                            }
                            // alert(content);
                            driverMarkers.push(drivermarker);
                        }
                    }
                }
                //var markers = [];//some array
                /*if (flag != 'test') {//commented bc when click on child seating or etc then map zoom out
			     var bounds = new google.maps.LatLngBounds();
			     for (var i = 0; i < driverMarkers.length; i++) {
			     bounds.extend(driverMarkers[i].getPosition());
			     }
			     map.fitBounds(bounds);
			     map.setZoom(15);
			     }*/
                setDriverListing(vType);
            }
            else {
                console.log(response.result);
            }
        });
    }

    function hide_locations_for_others() {
        if (typeof flightPath !== 'undefined') {
            flightPath.setMap(null);
            flightPath = '';
        }
        delete (flightPath); // this removes the variable completely
        DeleteDriverMarkers();
        //directionsDisplay.setMap(null);
        // Added Edit condition because location not set in delivery (02-03-2021)
        <?php if ($eBookingFrom != 'Hotel' && $action != "Edit") { ?>

        $("#from").val('');
        $("#hidden_from").val('');
        $("#from_lat_long").val('');
        $("#from_lat").val('');
        $("#from_long").val('');
        DeleteMarkers('from_loc');
        $("#to").val('');
        $("#to_lat_long").val('');
        $("#to_lat").val('');
        $("#to_long").val('');
        $("#hidden_to").val('');
        <?php } ?>
        previousValue = '';
        DeleteMarkers('to_loc');
        // enable for remove fly curve
        initialize();
    }

    function hide_location_for_fly(fromto) {
        if (!fromto) fromto = '';
        directionsDisplay.setMap(null);
        if (fromto == 'from') {

            <?php if ($eBookingFrom == 'Hotel') { ?>

            showFlyStations('from');

            <?php } else { ?>

            $("#from").val('');
            $("#from_lat_long").val('');
            $("#from_lat").val('');
            $("#from_long").val('');
            $("#iFromLocationIdStations").hide();
            DeleteMarkers('from_loc');

            <?php } ?>

        }
        else if (fromto == 'to') {
            $("#to").val('');
            $("#to_lat_long").val('');
            $("#to_lat").val('');
            $("#to_long").val('');
            $("#iToLocationIdStations").hide();
            DeleteMarkers('to_loc');
        }
        else {

            <?php if ($eBookingFrom == 'Hotel') { ?>

            showFlyStations('from');

            <?php } else { ?>

            $("#from").val('');
            $("#from_lat_long").val('');
            $("#from_lat").val('');
            $("#from_long").val('');
            $("#iFromLocationIdStations").hide();
            DeleteMarkers('from_loc');

            <?php } ?>

            DeleteMarkers('to_loc');
            $("#to").val('');
            $("#to_lat_long").val('');
            $("#to_lat").val('');
            $("#to_long").val('');
            $("#iToLocationIdStations").hide();
        }
        initialize();
    }

    function initialize() {
        var thePoint = new google.maps.LatLng('20.1849963', '64.4125062');
        //var gestureHandlingtype = $(document).width() > 480 ? 'cooperative' : 'greedy' ;
        var mapOptions = {
            zoom: 4,
            draggable: true,
            streetViewControl: false,
            center: thePoint,
            //mapTypeControlOptions: {
            //
            //    position: google.maps.ControlPosition.TOP_RIGHT
            //
            //},
            mapTypeControl: false,
            fullscreenControlOptions: {
                position: google.maps.ControlPosition.TOP_RIGHT
            }
        };
        eType = $('input[name=eType]:checked').val();
        if (eType == null) {
            eType = $('input[name=eType]').val();
        }
        $('#iVehicleTypeId').find(":checked").val('');
        $('#iVehicleTypeId').find(":selected").val('');
        map = new google.maps.Map(document.getElementById('map-canvas'),
            mapOptions);
        circle = new google.maps.Circle({radius: 25, center: thePoint});
        // map.fitBounds(circle.getBounds());
        if (eType == "Deliver" || eType == "Ride" || eType == "Moto" || eType == "Fly" || eType == "UberX") {
            //show_type(eType,1);
            show_type(eType);
        }
        if (eType == "UberX") {
            $('#btn_booking_date').click(function () {
                //$("#datetimepicker45").on("dp.change", function() {
                var vTypeId = $("#iVehicleTypeId").val();
                if (vTypeId == '') {
                    vTypeId = $('#iVehicleTypeId').find(":checked").val()
                }
                setDriverListing(vTypeId);
            });
        }
        // added by sunita for update vehicle fare
        $('#btn_booking_date').click(function () {
            //$("#datetimepicker45").on("dp.change", function() {
            //vehicleId = '';
            var vTypeId = $("#iVehicleTypeId").val();
            if (vTypeId == '') {
                vTypeId = $('#iVehicleTypeId').find(":checked").val()
            }
            eType = $('input[name=eType]:checked').val();
            if (eType == null) {
                eType = $('input[name=eType]').val();
            }
            showVehicleCountryVise($('#vCountry option:selected').val(), vTypeId, eType);
        });
        //showVehicleCountryVise('<?php echo $vCountry ?>', '<?php echo $iVehicleTypeId; ?>', eType); //commneted on 9-12 bc on load two times called same function

        <?php if ($action == "Edit") { ?>

        //callEditFundtion(); // now comment
        // show_locations();
        // showVehicleCountryVise('<?php echo $vCountry ?>','<?php echo $iVehicleTypeId; ?>'); //comment it bc two times executed when load

        <?php } ?>
        //setDriversMarkers('test');
        //alert('test');
        <?php if ($from_lat_long != '') { ?>
        show_locations();
        <?php } ?>
        $(".loader-default").hide(); //loaded show at the page load in cx-add_booking.php..and after all jquery is load loader hide in the initialize function
    }

    $(document).ready(function () {
        google.maps.event.addDomListener(window, 'load', initialize);
        setDriversMarkers('test');
        $("#eType").val(eType);
        $('input[type=radio][name=eType]').change(function () {
            eType = $('input[name=eType]:checked').val();
            show_type(eType);
        });
        var action = '<?php echo $action ?>';
        if (action == 'Edit' && eType == 'Fly') {
            showStationsDropdown();
        }
        //added by SP on 11-11-2020, beocz in edit booking date is not in datetimepicker4
        if (action == 'Edit') {
            selectdate();
        }
        // $('#ride_safety_guidelines_modal').addClass('active');
    });

    function play(radius) {
        // return Math.round(14-Math.log(radius)/Math.LN2);
        var pt = new google.maps.LatLng($("#from_lat").val(), $("#from_long").val());
        map.setCenter(pt);
        var newRadius = Math.round(24 - Math.log(radius) / Math.LN2);
        newRadius = newRadius - 9;
        map.setZoom(newRadius);
    }

    function getAddress(mDlatitude, mDlongitude, addId, setLatLongField, oldlat, oldlong, oldlatlong, oldAddress) {
        var mylatlang = new google.maps.LatLng(mDlatitude, mDlongitude);
        //$("#vehicle-popup").hide();//commented on 09-02-2021 bcoz when drag marker then click on i icon in vehicle then detail is not opened.
        var result = getReverseGeoCode(addId, setLatLongField, "<?=$_SESSION['sess_lang'];?>", mDlatitude, mDlongitude, oldlat, oldlong, oldlatlong, oldAddress, function (latitude, longitude, address) {
            show_locations();
        });
    }

    function DeleteMarkers(newId) {
        // Loop through all the markers and remove
        for (var i = 0; i < markers.length; i++) {
            if (newId != '') {
                if (markers[i].id == newId) {
                    markers[i].setMap(null);
                }
            }
            else {
                markers[i].setMap(null);
            }
        }
        if (newId == '') {
            markers = [];
        }
    }

    function DeleteDriverMarkers(newId) {
        // Loop through all the markers and remove
        for (var i = 0; i < driverMarkers.length; i++) {
            if (newId != '') {
                if (driverMarkers[i].id == newId) {
                    driverMarkers[i].setMap(null);
                }
            }
            else {
                driverMarkers[i].setMap(null);
            }
        }
        if (newId == '') {
            driverMarkers = [];
        }
    }

    /*function updateCurveMarker() {
				var pos1 = markerP1.getPosition(), // latlng
				pos2 = markerP2.getPosition(),
				projection = map.getProjection(),
				p1 = projection.fromLatLngToPoint(pos1), // xy
				p2 = projection.fromLatLngToPoint(pos2);
				// Calculate the arc.
				// To simplify the math, these points
				// are all relative to p1:
				var e = new Point(p2.x - p1.x, p2.y - p1.y), // endpoint (p2 relative to p1)
				    m = new Point(e.x / 2, e.y / 2), // midpoint
				    o = new Point(e.y, -e.x), // orthogonal
				    c = new Point( // curve control point
					m.x + curvature * o.x,
					m.y + curvature * o.y);
				var pathDef = 'M 0,0 ' +
				    'q ' + c.x + ',' + c.y + ' ' + e.x + ',' + e.y;
				var zoom = map.getZoom(),
				    scale = 1 / (Math.pow(2, -zoom));

				var symbol = {
				    path: pathDef,
				    scale: scale,
				    strokeWeight: 2,
				    fillColor: 'none'
				};
				if (!curveMarker) {
				    curveMarker = new Marker({
					position: pos1,
					clickable: false,
					icon: symbol,
					zIndex: 0, // behind the other markers
					map: map
				    });
				} else {
				    curveMarker.setOptions({
					 position: pos1,
					icon: symbol,
				    });
				}
	}*/
    function setMarker(postitions, valIcon) {
        var newIcon;
        if (valIcon == 'from_loc') {
            if (eType == 'UberX') {
                newIcon = '<?= $tconfig["tsite_url"] ?>/webimages/upload/mapmarker/PinTo.png';
            }
            else {
                newIcon = '<?= $tconfig["tsite_url"] ?>/webimages/upload/mapmarker/PinFrom.png';
            }
        }
        else if (valIcon == 'to_loc') {
            newIcon = '<?= $tconfig["tsite_url"] ?>/webimages/upload/mapmarker/PinTo.png';
        }
        else {
            newIcon = '<?= $tconfig["tsite_url"] ?>/webimages/upload/mapmarker/PinTo.png';
        }
        var marker = new google.maps.Marker({
            map: map,
            draggable: true,
            animation: google.maps.Animation.DROP,
            position: postitions,
            icon: newIcon
        });
        marker.id = valIcon;
        if ($('#eBookingFrom').val() == "Hotel" && valIcon == "from_loc") {
            marker.draggable = false;
        }
        markers.push(marker);
        map.setCenter(marker.getPosition());
        map.setZoom(15);
        //if(eType=='Fly' && valIcon=="to_loc") {
        if (eType == 'Fly' && $("#from_lat").val() != '' && $("#from_long").val() != '' && $("#to_lat").val() != '' && $("#to_long").val() != '') {
            for (var i = 0; i < markers.length; i++) {
                markers[i].setMap(null);
            }
            markers = [];
            var curvature = 0.5; // how curvy to make the arc
            //alert($("#from_lat").val());
            var pos1 = new google.maps.LatLng($("#from_lat").val(), $("#from_long").val());
            var pos2 = new google.maps.LatLng($("#to_lat").val(), $("#to_long").val());
            var bounds = new google.maps.LatLngBounds();
            bounds.extend(pos1);
            bounds.extend(pos2);
            //console.log("FROM=====" + $("#from_lat").val() + "====" + $("#from_long").val() + "===TO+++" + $("#to_lat").val() + "====" + $("#to_long").val() + "===" );
            //not used following code start
            var directionsOptions = {// For Polyline Route line options on map
                polylineOptions: {
                    strokeColor: '#FF7E00',
                    strokeWeight: 0
                }
            };
            /*map = new google.maps.Map(document.getElementById('map-canvas'), {

	     center: bounds.getCenter(),

	     zoom: 12

	     });*/
            map.setCenter(bounds.getCenter());
            map.setZoom(12);
            if ($(window).width() >= 768) {
                var elmnt = $(".booking-block").offset().left + $(".booking-block").outerWidth();
                map.fitBounds(bounds, {top: 50, right: 50, left: elmnt, bottom: 50});
            }
            else {
                map.fitBounds(bounds);
            }
            $(window).resize(function () {
                if ($(window).width() >= 768) {
                    var elmnt = $(".booking-block").offset().left + $(".booking-block").outerWidth();
                    map.fitBounds(bounds, {top: 50, right: 50, left: elmnt, bottom: 50});
                }
                else {
                    map.fitBounds(bounds);
                }
            });
            //not used following code end
            var markerP1 = new google.maps.Marker({
                position: pos1,
                draggable: false,
                map: map,
                icon: {
                    url: '<?= $tconfig["tsite_url"] ?>/webimages/upload/mapmarker/PinFrom.png',
                    anchor: new google.maps.Point(19, 56),
                    size: new google.maps.Size(38, 56)
                },
            });
            markerP1.id = 'from_loc';
            var markerP2 = new google.maps.Marker({
                position: pos2,
                draggable: false,
                map: map,
                icon: {
                    url: '<?= $tconfig["tsite_url"] ?>/webimages/upload/mapmarker/PinTo.png',
                    anchor: new google.maps.Point(19, 50),
                    size: new google.maps.Size(38, 56)
                },
            });
            markerP2.id = 'to_loc';
            markers.push(markerP1);
            markers.push(markerP2);
            for (var i = 0; i < markers.length; i++) {
                if (markers[i].id == "from_loc") {
                    markerP1 = markers[i];
                }
                if (markers[i].id == "to_loc") {
                    markerP2 = markers[i];
                }
            }
            var curveMarker;

            function updateCurveMarker() {
                var pos1 = markerP1.getPosition(), // latlng
                    pos2 = markerP2.getPosition(),
                    projection = map.getProjection(),
                    p1 = projection.fromLatLngToPoint(pos1), // xy
                    p2 = projection.fromLatLngToPoint(pos2);
                // Calculate the arc.
                // To simplify the math, these points
                // are all relative to p1:
                var e = new google.maps.Point(p2.x - p1.x, p2.y - p1.y), // endpoint (p2 relative to p1)
                    m = new google.maps.Point(e.x / 2, e.y / 2), // midpoint
                    o = new google.maps.Point(e.y, -e.x), // orthogonal
                    c = new google.maps.Point( // curve control point
                        m.x + curvature * o.x,
                        m.y + curvature * o.y);
                var pathDef = 'M 0,0 ' +
                    'q ' + c.x + ',' + c.y + ' ' + e.x + ',' + e.y;
                var zoom = map.getZoom(),
                    scale = 1 / (Math.pow(2, -zoom));
                var symbol = {
                    path: pathDef,
                    scale: scale,
                    strokeWeight: 5,
                    fillColor: 'none',
                    strokeColor: '#FF7E00'
                };
                if (!curveMarker) {
                    curveMarker = new google.maps.Marker({
                        position: pos1,
                        clickable: false,
                        icon: symbol,
                        zIndex: 0, // behind the other markers
                        map: map
                    });
                    curveMarker.id = 'curve_marker';
                }
                else {
                    curveMarker.setOptions({
                        position: pos1,
                        icon: symbol,
                    });
                }
                markers.push(curveMarker);
            }

            google.maps.event.addListener(map, 'projection_changed', updateCurveMarker);
            //google.maps.event.addListener(map, 'zoom_changed', updateCurveMarker);
            google.maps.event.addListener(map, 'bounds_changed', updateCurveMarker);
            google.maps.event.addListener(markerP1, 'position_changed', updateCurveMarker);
            google.maps.event.addListener(markerP2, 'position_changed', updateCurveMarker);
        }
        if (valIcon == "from_loc") {
            marker.addListener('dragend', function (event) {
                directionsDisplay.setMap(null);
                var lat = event.latLng.lat();
                var lng = event.latLng.lng();
                var myLatlongs = new google.maps.LatLng(lat, lng);
                showsurgemodal = "No";
                var from_latold = $("#from_lat").val();
                var from_longold = $("#from_long").val();
                var from_lat_longold = $("#from_lat_long").val();
                var from_addressold = $("#from").val();
                $("#from_lat").val(lat);
                $("#from_long").val(lng);
                $("#from_lat_long").val(myLatlongs); // (23.10232292203304, 72.53512620524901)
                getAddress(lat, lng, 'from', 'from', from_latold, from_longold, from_lat_longold, from_addressold);
                // routeDirections();
            });
        }
        if (valIcon == 'to_loc') {
            marker.addListener('dragend', function (event) {
                directionsDisplay.setMap(null);
                var lat = event.latLng.lat();
                var lng = event.latLng.lng();
                var myLatlongs1 = new google.maps.LatLng(lat, lng);
                showsurgemodal = "No";
                var to_latold = $("#to_lat").val();
                var to_longold = $("#to_long").val();
                var to_lat_longold = $("#to_lat_long").val();
                var to_addressold = $("#to").val();
                $("#to_lat").val(lat);
                $("#to_long").val(lng);
                $("#to_lat_long").val(myLatlongs1);
                getAddress(lat, lng, 'to', 'to', to_latold, to_longold, to_lat_longold, to_addressold);
                // routeDirections();
            });
        }
        // routeDirections();
    }

    function routeDirections() {
        if (($("#from").val() != "" && $("#from_lat_long").val() != "") && ($("#to").val() != "" && $("#to_lat_long").val() != "")) {
            var newFrom = $("#from_lat").val() + ", " + $("#from_long").val();
            if (eType == 'UberX') {
                var newTo = $("#from_lat").val() + ", " + $("#from_long").val();
            }
            else {
                var newTo = $("#to_lat").val() + ", " + $("#to_long").val();
            }
            var source_latitude = $("#from_lat").val();
            var source_longitude = $("#from_long").val();
            var dest_latitude = $("#to_lat").val();
            var dest_longitude = $("#to_long").val();
            var waypoint0 = newFrom;
            var waypoint1 = newTo;
            getReverseGeoDirectionCode(source_latitude, source_longitude, dest_latitude, dest_longitude, waypoint0, waypoint1, function (data_response) {
                if (eType != 'Fly') {
                    if ((data_response.Action == 0 && data_response.message == "LBL_DEST_ROUTE_NOT_FOUND") || data_response.status == "ZERO_RESULTS") {
                        hide_locations_for_others();
                        alert("<?= addslashes($langage_lbl['LBL_ENTER_PROPER_LOCATION_MANUAL_BOOKING']) ?>");
                        // return false;
                    }
                    else {
                        var latlng = [
                            new google.maps.LatLng(source_latitude, source_longitude),
                            new google.maps.LatLng(dest_latitude, dest_longitude)
                        ];
                        var latlngbounds = new google.maps.LatLngBounds();
                        for (var i = 0; i < latlng.length; i++) {
                            latlngbounds.extend(latlng[i]);
                        }
                        //map.fitBounds(latlngbounds);
                        if ($(window).width() >= 768) {
                            //var elmnt = $(".booking-block").offset().left+$(".booking-block").outerWidth();
                            //map.fitBounds(latlngbounds,{top:50,right:50,left:elmnt,bottom:50});
                            //added by SP used right instead of left becoz when rtl lang then map direction is not shown proper
                            var elmnt = $(".booking-block").offset().right + $(".booking-block").outerWidth();
                            map.fitBounds(latlngbounds, {top: 50, right: elmnt, left: 50, bottom: 50});
                        }
                        else {
                            map.fitBounds(latlngbounds);
                        }
                        //commented bc above same thing is used so not used
                        //$(window).resize(function() {
                        //    if ($(window).width() >= 768) {
                        //	var elmnt = $(".booking-block").offset().left+$(".booking-block").outerWidth();
                        //	map.fitBounds(latlngbounds,{top:50,right:50,left:elmnt,bottom:50});
                        //    } else {
                        //	map.fitBounds(latlngbounds);
                        //    }
                        //});
                        if (MAPS_API_REPLACEMENT_STRATEGY.toUpperCase() == 'NONE') {
                            $("#distance").val(data_response.routes[0].legs[0].distance.value);
                            $("#duration").val(data_response.routes[0].legs[0].duration.value);
                            //distance = (parseFloat(data_response.routes[0].legs[0].distance.value, 10) / parseFloat(1000, 10)).toFixed(2);
                            //duration = (parseFloat(data_response.routes[0].legs[0].duration.value, 10) / parseFloat(60, 10)).toFixed(2);
                            // $("#distance").val(distance);
                            // $("#duration").val(duration);
                            var points = data_response.routes[0].overview_polyline.points;
                            var polyPoints = google.maps.geometry.encoding.decodePath(points);
                            // var polyPoints = data_response;
                            if (eType != 'Fly') {
                                directionsDisplay.setMap(map); // Set route
                                createPolyLine(new google.maps.Polyline({
                                    path: polyPoints,
                                    strokeColor: '#FF7E00',
                                    strokeWeight: 5
                                }));
                            }
                            else {
                                directionsDisplay.setMap(null);
                            }
                            directionsDisplay.setOptions({suppressMarkers: true}); //, preserveViewport: true, suppressMarkers: false for setting auto markers from google api
                            points = '';
                            data_response = [];
                            polyPoints = '';
                            temp_points = '';
                        }
                        else {
                            // removePolyLine();
                            $("#distance").val(data_response.distance);
                            $("#duration").val(data_response.duration);
                            //distance = (parseFloat(data_response.distance, 10) / parseFloat(1000, 10)).toFixed(2);
                            //duration = (parseFloat(data_response.duration, 10) / parseFloat(60, 10)).toFixed(2);
                            //$("#distance").val(distance);
                            //
                            //$("#duration").val(duration);
                            var polyLinesArr = new Array();
                            var i;
                            if ((data_response.data != 'undefined') && (data_response.data != undefined)) {
                                for (i = 0; i < (data_response.data).length; i++) {
                                    polyLinesArr.push({
                                        lat: parseFloat(data_response.data[i].latitude),
                                        lng: parseFloat(data_response.data[i].longitude)
                                    });
                                }
                                var polyPoints = polyLinesArr;
                                directionsDisplay.setMap(null);
                                if (eType != 'Fly') {
                                    directionsDisplay.setMap(map);
                                    directionsDisplay.setOptions({suppressMarkers: true}); //, preserveViewport: true, suppressMarkers: false for setting auto markers from google api
                                    createPolyLine(new google.maps.Polyline({
                                        path: polyPoints,
                                        strokeColor: '#FF7E00',
                                        strokeWeight: 5
                                    }));
                                }
                                else {
                                    directionsDisplay.setMap(map);
                                }
                                data_response = [];
                                polyPoints = '';
                            }
                        }
                        //var dist_fare = parseFloat($("#distance").val(), 10) / parseFloat(1000, 10);
                        var dist_fare = $("#distance").val();
                        if ($("#eUnit").val() != 'KMs') {
                            dist_fare = dist_fare * 0.621371;
                        }
                        $('#dist_fare').text(dist_fare);
                        //$('#dist_fare').text(dist_fare.toFixed(2));
                        //var time_fare = parseFloat($("#duration").val(), 10) / parseFloat(60, 10);
                        var time_fare = $("#duration").val();
                        //$('#time_fare').text(time_fare.toFixed(2));
                        $('#time_fare').text(time_fare);
                        var vehicleId = $('#iVehicleTypeId').val();
                        if (vehicleId == '' || vehicleId == null) {
                            var vehicleId = $('#iVehicleTypeId').find(":checked").val();
                            if (vehicleId == '' || vehicleId == null) {
                                var vehicleId = '<?php echo $iVehicleTypeId; ?>';
                            }
                        }
                        var booking_date = $('#datetimepicker4').val();
                        var vCountry = $('#vCountry').val();
                        var tollcostval = $('#fTollPrice').val();
                        var iMemberId = '<?php echo $_SESSION['sess_iUserId']; ?>';
                        var userType1 = '<?php echo $userType1; ?>';
                        var iFromStationId = $("#iFromStationId").val();
                        var iToStationId = $("#iToStationId").val();
                    }
                }
                else {
                    directionsDisplay.setMap(null);
                }
                var dist_fare = $("#distance").val();
                //var dist_fare = parseFloat($("#distance").val(), 10) / parseFloat(1000, 10);
                // alert(dist_fare);
                if ($("#eUnit").val() != 'KMs') {
                    dist_fare = dist_fare * 0.621371;
                }
                // alert(dist_fare);
                //$('#dist_fare').text(dist_fare.toFixed(2));
                $('#dist_fare').text(dist_fare);
                //var time_fare = parseFloat($("#duration").val(), 10) / parseFloat(60, 10);
                var time_fare = $("#duration").val();
                //$('#time_fare').text(time_fare.toFixed(2));
                $('#time_fare').text(time_fare);
                var vehicleId = $('#iVehicleTypeId').val();
                if (vehicleId == '' || vehicleId == null) {
                    var vehicleId = $('#iVehicleTypeId').find(":checked").val();
                    if (vehicleId == '' || vehicleId == null) {
                        var vehicleId = '<?php echo $iVehicleTypeId; ?>';
                    }
                }
                showVehicleCountryVise(vCountry, vehicleId, eType);
            });





            <? if ($iVehicleTypeId != "") { ?>

            var iVehicleTypeId = '<?= $iVehicleTypeId ?>';
            //getFarevalues(iVehicleTypeId);
            showAsVehicleType(iVehicleTypeId);

            <? } ?>



            setDriversMarkers();
        }
    }

    function createPolyLine(cus_polyline) {
        if (typeof flightPath !== 'undefined') {
            flightPath.setMap(null);
            flightPath = '';
            flightPath = cus_polyline;
            flightPath.setMap(map);
        }
        else {
            flightPath = cus_polyline;
            flightPath.setMap(map);
        }
    }

    function show_locations() {
        if (($("#from").val() != "") && ($("#to").length == 0 || $("#to").val() == '')) { //$("#to").length == 0 added by NM, map error in pin
            DeleteMarkers('from_loc');
            var latlng = new google.maps.LatLng($("#from_lat").val(), $("#from_long").val());
            setMarker(latlng, 'from_loc');
        }
        if ($("#to").val() != "" && $("#from").val() == '') {
            DeleteMarkers('to_loc');
            var latlng_to = new google.maps.LatLng($("#to_lat").val(), $("#to_long").val());
            setMarker(latlng_to, 'to_loc');
        }
        if (($("#from").val() != '') && ($("#to").length != 0) && ($("#to").val() != '')) { //$("#to").length != 0 added by NM, map error in pin
            from_to($("#from").val(), $("#to").val());
        }
    }

    function from_to(from, to) {
        DeleteMarkers('from_loc');
        var latlng = new google.maps.LatLng($("#from_lat").val(), $("#from_long").val());
        setMarker(latlng, 'from_loc');
        DeleteMarkers('to_loc');
        var latlng_to = new google.maps.LatLng($("#to_lat").val(), $("#to_long").val());
        setMarker(latlng_to, 'to_loc');
        routeDirections();
    }

    function callEditFundtion() {
        var from_lat = $('#from_lat').val();
        var from_lng = $('#from_long').val();
        var from = new google.maps.LatLng(from_lat, from_lng);
        if (from != '') {
            setMarker(from, 'from_loc');
        }
        var to_lat = $('#to_lat').val();
        var to_lng = $('#to_long').val();
        if (to_lat != 0 && to_lng != 0) {
            var to = new google.maps.LatLng(to_lat, to_lng);
            if (to != '') {
                setMarker(to, 'to_loc');
            }
        }
        // var fromLatlongs = $("#from_lat").val()+", "+$("#from_long").val();
        // var toLatlongs = $("#to_lat").val()+", "+$("#to_long").val();
    }

    $(function () {
        var dt = new Date();
        var MINIMUM_HOURS_LATER_BOOKING = "<?= $MINIMUM_HOURS_LATER_BOOKING ?>";
        var MAXIMUM_HOURS_LATER_BOOKING = "<?= $MAXIMUM_HOURS_LATER_BOOKING ?>";

        // var minDate = moment().add((MINIMUM_HOURS_LATER_BOOKING * 60) + 1, 'm');
        var minDate = moment().format('YYYY-MM-DD');
        var maxDate = moment().add((MAXIMUM_HOURS_LATER_BOOKING * 60), 'm');
        $('#datetimepicker45').datetimepicker({
            format: 'YYYY-MM-DD hh:mm A',
            // ignoreReadonly: true,
            sideBySide: true,
            minDate: minDate,
            maxDate: maxDate,
            widgetPositioning: {
                // vertical: 'top' commented by NModi (25-12-20)
                vertical: 'bottom'
            }
        }).on('dp.change', function (e) {
            //added by SP on 26-12-2020 for min date and max date configurable
            if (MAXIMUM_HOURS_LATER_BOOKING > MINIMUM_HOURS_LATER_BOOKING) {
                // $('#datetimepicker45').data("DateTimePicker").minDate(moment().add(MINIMUM_HOURS_LATER_BOOKING,'h'))
                // $('#datetimepicker45').data("DateTimePicker").maxDate(moment().add(MAXIMUM_HOURS_LATER_BOOKING,'h'))
                //hours added instead of month because in month when click on hour disabled hours for all dates
                //$('#datetimepicker45').data("DateTimePicker").minDate(moment().add((MINIMUM_HOURS_LATER_BOOKING * 60) + 1,'m'))
                //$('#datetimepicker45').data("DateTimePicker").maxDate(moment().add(MAXIMUM_HOURS_LATER_BOOKING * 60,'m'))
            }
            else {
                // $('#datetimepicker45').data("DateTimePicker").minDate(moment().add(61,'m'))
            }
        });
        $('#from').keyup(function (e) {
            buildAutoComplete("from", e, "<?=$MIN_CHAR_REQ_GOOGLE_AUTO_COMPLETE;?>", "<?=$_SESSION['sess_lang'];?>", function (latitude, longitude, address) {
                <? if ($_SERVER['REQUEST_URI'] != '/user-fare-estimate') { ?>
                checkrestrictionfrom('from');
                <? } ?>

                setDriverListing();
                show_locations();
            });
        });
        $('#to').keyup(function (e) {
            buildAutoCompleteLat("to", e, "<?=$MIN_CHAR_REQ_GOOGLE_AUTO_COMPLETE;?>", "<?=$_SESSION['sess_lang'];?>", $('#from_lat').val(), $('#from_long').val(), function (latitude, longitude, address) {
                <? if ($_SERVER['REQUEST_URI'] != '/user-fare-estimate') { ?>
                checkrestrictionto('to');
                <? } ?>
                show_locations();
            });
        });
    });
    var specialKeys = new Array();

    function IsAlphaNumeric(e) {
        var keyCode = e.keyCode == 0 ? e.charCode : e.keyCode;
        //alert(keyCode);
        if (keyCode == 32) {
            var ret = ((keyCode >= 48 && keyCode <= 57) || (keyCode >= 65 && keyCode <= 90) || (keyCode >= 97 && keyCode <= 122) || (specialKeys.indexOf(e.keyCode) != -1 && e.charCode != e.keyCode));
            $("#spaveerror").show();
            setTimeout(function () {
                $("#spaveerror").hide();
            }, 5000);
            return ret;
        }
    }

    function isNumberKey(evt) {

        <?php if ($userType1 == 'rider') { ?> return true;

        <?php } else { ?>

        <?php if ($userType1 != 'rider') { ?>

        showPhoneDetail();

        <?php } else { ?>

        //renderdetails("vPhone");
        //$("#vPhone").closest('form').find('.form-group.rederdetail').addClass('floating');

        <?php } ?>

        var charCode = (evt.which) ? evt.which : evt.keyCode
        //alert(charCode);
        //if (charCode > 31 && (charCode < 35 || charCode > 57)) {
        if (charCode != 46 && charCode > 31 && (charCode < 48 || charCode > 57) && (charCode < 96 || charCode > 105)) {
            //if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            $("#vPhone").val('');
            return false;
        }
        else {
            return true;
        }

        <?php } ?>

    }

    function isValidEmail() {
        var email = document.getElementById('ajax_vEmail');
        var filter = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        if (!filter.test(email.value)) {
            alert("Please provide a valid email address");
            email.focus;
            return false;
        }
        else {
            return true;
        }
    }

    function changeCode(id, vehicleId) {
        // alert(id);
        if (phonedetailAjaxAbortCount == 0) {
            var ajaxData = {
                'URL': '<?= $tconfig['tsite_url'] ?>booking/change_code.php',
                'AJAX_DATA': {id: id, eUnit: 'yes'},
                'REQUEST_DATA_TYPE': 'json'
            };
            getDataFromAjaxCall(ajaxData, function (response) {
                if (response.action == "1") {
                    var dataHTML = response.result;
                    document.getElementById("vPhoneCode").value = dataHTML.vPhoneCode;
                    document.getElementById("eUnit").value = dataHTML.eUnit;
                    document.getElementById("vRideCountry").value = dataHTML.vCountryCode;
                    document.getElementById("vTimeZone").value = dataHTML.vTimeZone;
                    $("#change_eUnit").text(dataHTML.eUnit);
                    var substr = <?php echo json_encode($radius_driver); ?>;
                    substr.forEach(function (item) {
                        $('#radius-id option[value="' + item + '"]').text(item + " " + dataHTML.eUnit + ' Radius');
                    });
                    showPhoneDetail();
                    showVehicleCountryVise(id, vehicleId, eType);
                }
                else {
                    // console.log(response.result);
                }
            });
        }
    }

    $(document).ready(function () {
        var navigatepage = '<?php echo $navigatedPage; ?>';
        if (navigatepage != '') {
            if (navigatepage == 'Ride') {
                $("#r1").trigger("click");
            }
            else if (navigatepage == 'Fly') {
                $("#r4").trigger("click");
            }
            else if (navigatepage == 'Moto') {
                $("#r5").trigger("click");
            }
            else if (navigatepage == 'Delivery' || navigatepage == 'Multi-Delivery') {
                $("#r2").trigger("click");
            }
            else if (navigatepage == 'UberX') {
                $("#r3").trigger("click");
            }
        }
        $("#eType_design").on('change', function () {
            if (this.value == 'ride') {
                $("#r1").trigger("click");
                $('ul.stepper.linear').css({'height': ''});
            }
            else if (this.value == 'Fly') {
                $("#r4").trigger("click");
                $('ul.stepper.linear').css({'height': ''});
            }
            else if (this.value == 'Moto') {
                $("#r5").trigger("click");
                $('ul.stepper.linear').css({'height': ''});
            }
            else if (this.value == 'delivery') {
                $("#r2").trigger("click");
                $('ul.stepper.linear').css({'height': ''});
            }
            else if (this.value == 'UberX') {
                $("#r3").trigger("click");
                $('ul.stepper.linear').css({'height': 'calc(100vh - 370px)'});
            }
        });
        var con = $("#vCountry").val();
        changeCode(con, '<?php echo $iVehicleTypeId; ?>');
        if ($("#from").val() == "") {
            $('#radius-id').prop('disabled', 'disabled');
        }
        else {
            $('#radius-id').prop('disabled', false);
        }
    });
    $('#from').on('change', function () {
        if (this.value == '') {
            $('#radius-id').prop('disabled', 'disabled');
        }
        else {
            $('#radius-id').prop('disabled', false);
        }
    });

    function showPopupDriver(driverId) {
        if ($("#driver_popup").is(":visible") && $('#driver_popup ul').attr('class') == driverId) {
            $("#driver_popup").hide("slide", {direction: "right"}, 700);
        }
        else {
            //alert(driverId);
            $('#submitbutton').prop('disabled', true);
            $("#driver_popup").hide();
            // $.ajax({
            //     type: "POST",
            //     url: "<?= $tconfig["tsite_url"] ?>booking/get_driver_detail_popup.php",
            //     dataType: "html",
            //     data: {driverId: driverId},
            //     success: function (dataHtml2) {
            // 	$('#submitbutton').prop('disabled', false);
            // 	$('#driver_popup').html(dataHtml2);
            // 	$("#driver_popup").show("slide", {direction: "right"}, 700);
            //     }, error: function (dataHtml2) {
            // 	$('#submitbutton').prop('disabled', false);
            //     }
            // });
            var ajaxData = {
                'URL': '<?= $tconfig['tsite_url'] ?>booking/get_driver_detail_popup.php',
                'AJAX_DATA': {driverId: driverId},
                'REQUEST_DATA_TYPE': 'html'
            };
            getDataFromAjaxCall(ajaxData, function (response) {
                if (response.action == "1") {
                    var dataHtml2 = response.result;
                    $('#submitbutton').prop('disabled', false);
                    $('#driver_popup').html(dataHtml2);
                    $("#driver_popup").show("slide", {direction: "right"}, 700);
                }
                else {
                    console.log(response.result);
                    $('#submitbutton').prop('disabled', false);
                }
            });
        }
    }

    function showVehicleCountryVise(countryId, vehicleId, eType, fly, fromto) {
        if (!fly) fly = 0;
        if (!fromto) fromto = '';
        //$(".loader-default").show();
        $("#VehicleTypeSpan").html('<div style="text-align:center"><img src="default.gif"></div>');
        var userType = '<?php echo ucfirst($userType1); ?>';
        //added by SP for get vehicles/services according to the pickup location on 02-08-2019 start
        var from_lat = from_long = '';
        if ($("#from_lat").val() != '') {
            var from_lat = $("#from_lat").val();
            var from_long = $("#from_long").val();
            //var duration = parseFloat($("#duration").val(), 10) / parseFloat(60, 10);
            //var distance = parseFloat($("#distance").val(), 10) / parseFloat(1000, 10);
            var duration = $("#duration").val();
            var distance = $("#distance").val();
            var to_lat = $("#to_lat").val();
            var to_long = $("#to_long").val();
            var promoCode = $("#promocode").val();
            var iFromStationId = $("#iFromStationId").val();
            var iToStationId = $("#iToStationId").val();
            var iUserId = $("#iUserId").val();
        }
        var booking_date = $("#datetimepicker4").val();
        if (vehicleId == '') {
            vehicleId = $('#iVehicleTypeId').find(":checked").val();
        }

        <? if(ENABLE_WEBSERVICECALL_MANUALBOOKING == 'Yes') { ?>

        var vSourceAddresss1 = $('input[name="vSourceAddresss"').val();
        var distance = $("#distance").val();
        var duration = $("#duration").val();
        var iDriverId = $('#iDriverId').val();
        var ajaxData = {
            'URL': '<?= $tconfig['tsite_url'] ?>booking/get_webservice_response.php',
            'AJAX_DATA': {
                vCountry: countryId,
                type: 'loadAvailableCab',
                iVehicleTypeId: vehicleId,
                eType: eType,
                from_lat: from_lat,
                from_long: from_long,
                to_lat: to_lat,
                to_long: to_long,
                distance: distance,
                duration: duration,
                promoCode: promoCode,
                iFromStationId: iFromStationId,
                iToStationId: iToStationId,
                userType: userType,
                iUserId: iUserId,
                booking_date: booking_date,
                "GeneralMemberId": useridsession,
                "GeneralUserType": 'Passenger',
                "tSessionId": tSessionIdsession,
                vSourceAddresss: vSourceAddresss1,
                iDriverId: iDriverId
            },
            'REQUEST_ASYNC': false
        };
        getDataFromAjaxCall(ajaxData, function (response) {
            if (response.action == "1") {
                var dataHtml2 = response.result;
                if (dataHtml2 == -1) {
                    alert("<?= addslashes($langage_lbl['LBL_FLY_NO_VEHICLES']); ?>");
                    hide_location_for_fly();
                }
                else {
                    //if(eType=='UberX') {
                    // $('#iVehicleTypeIdUberx').html(dataHtml2);
                    $('#VehicleTypeSpan').html(dataHtml2);
                }
            }
            else {
                console.log(response.result);
            }
        });

        <? } else { ?>

        //added by SP for get vehicles/services according to the pickup location on 02-08-2019 end
        var ajaxData = {
            'URL': '<?= $tconfig['tsite_url'] ?>booking/cx-ajax_booking_details.php',
            'AJAX_DATA': {
                countryId: countryId,
                type: 'getVehicles',
                iVehicleTypeId: vehicleId,
                eType: eType,
                from_lat: from_lat,
                from_long: from_long,
                to_lat: to_lat,
                to_long: to_long,
                distance: distance,
                duration: duration,
                promoCode: promoCode,
                iFromStationId: iFromStationId,
                iToStationId: iToStationId,
                userType: userType,
                iUserId: iUserId,
                booking_date: booking_date
            },
            'REQUEST_DATA_TYPE': 'html'
        };
        getDataFromAjaxCall(ajaxData, function (response) {
            if (response.action == "1") {
                var dataHtml2 = response.result;
                if (dataHtml2 == -1) {
                    alert("<?= addslashes($langage_lbl['LBL_FLY_NO_VEHICLES']); ?>");
                    hide_location_for_fly();
                }
                else {
                    //if(eType=='UberX') {
                    // $('#iVehicleTypeIdUberx').html(dataHtml2);
                    $('#VehicleTypeSpan').html(dataHtml2);
                }
                // } else {
                // $('#iVehicleTypeId').html(dataHtml2);
                // }
                // $("#driver_popup").show("slide", {direction: "right"}, 700);
            }
            else {
                console.log(response.result);
            }
        });
        <? } ?>


        if (eType == 'Fly' && fly == 1) {
            showFlyStations(fromto);
        }
    }

    function showFlyStations(fromto) {
        if (!fromto) fromto = '';
        var useridfly = '<?php echo $db_userdata[0]['iUserId']; ?>';
        var tSessionIdfly = '<?php echo $db_userdata[0]['tSessionId']; ?>';
        //if($("#from_lat").val()!='' && $("#from_long").val()!='' && $("#to_lat").val()=='' && $("#to_long").val()=='') {
        if ($("#from_lat").val() != '' && $("#from_long").val() != '' && fromto == 'from') {
            var iLocationId = $("#iToStationId").val();
            fromto = 'from';
            // Added and commented by HV on 09-11-2020 as discussed with KS
            var data = {
                'type': 'getNearestFlyStations',
                'iUserId': useridfly,
                "GeneralMemberId": useridfly,
                "GeneralUserType": 'Passenger',
                "tSessionId": tSessionIdfly,
                "lattitude": $("#from_lat").val(),
                "longitude": $("#from_long").val(),
                'iLocationId': iLocationId,
                'async_request': false
            };
            data = $.param(data);
            getDataFromApi(data, function (response) {
                var response_data = JSON.parse(response);
                if (response_data != '' && response_data.Action > 0) {
                    var data1 = {
                        'type': 'getNearestFlyStations_booking',
                        'iUserId': useridfly,
                        "GeneralMemberId": useridfly,
                        "GeneralUserType": 'Passenger',
                        "tSessionId": tSessionIdfly,
                        "data": response,
                        'fromto': fromto,
                        'async_request': false
                    };
                    data1 = $.param(data1);
                    getDataFromApi(data1, function (response1) {
                        if (response1 != '') {
                            $("#stationdata").html(response1);
                            $('#stations-popup').addClass('active');
                        }
                        else {
                            alert("<?php echo addslashes($langage_lbl['LBL_NOSTATIONS_AVAILABLE']); ?>");
                            $("#stationdropdownfrom").html('');

                            <? if ($eBookingFrom == 'Hotel') { ?>

                            $("#from").removeAttr('readonly');
                            $("#from").css('pointer-events', '');

                            <? } else { ?>

                            hide_location_for_fly('from');

                            <? } ?>

                        }
                    });
                }
                else {
                    alert("<?php echo addslashes($langage_lbl['LBL_NOSTATIONS_AVAILABLE']); ?>");
                    $("#stationdropdownfrom").html('');

                    <? if ($eBookingFrom == 'Hotel') { ?>

                    $("#from").removeAttr('readonly');
                    $("#from").css('pointer-events', '');

                    <? } else { ?>

                    hide_location_for_fly('from');

                    <? } ?>

                }
            });
            // Added and commented by HV on 09-11-2020 as discussed with KS End
        }
        else if ($("#to_lat").val() != '' && $("#to_long").val() != '' && fromto == 'to') {
            var iLocationId = $("#iFromStationId").val();
            fromto = 'to';
            // Added and commented by HV on 09-11-2020 as discussed with KS
            var data = {
                'type': 'getNearestFlyStations',
                'iUserId': useridfly,
                "GeneralMemberId": useridfly,
                "GeneralUserType": 'Passenger',
                "tSessionId": tSessionIdfly,
                "lattitude": $("#to_lat").val(),
                "longitude": $("#to_long").val(),
                'iLocationId': iLocationId,
                'async_request': false
            };
            data = $.param(data);
            getDataFromApi(data, function (response) {
                var response_data = JSON.parse(response);
                if (response_data != '' && response_data.Action > 0) {
                    var data1 = {
                        'type': 'getNearestFlyStations_booking',
                        'iUserId': useridfly,
                        "GeneralMemberId": useridfly,
                        "GeneralUserType": 'Passenger',
                        "tSessionId": tSessionIdfly,
                        "data": response,
                        'fromto': fromto,
                        'async_request': false
                    };
                    data1 = $.param(data1);
                    getDataFromApi(data1, function (response1) {
                        if (response1 != '') {
                            $("#stationdata").html(response1);
                            $('#stations-popup').addClass('active');
                        }
                        else {
                            alert("<?php echo addslashes($langage_lbl['LBL_NOSTATIONS_AVAILABLE']); ?>");
                            $("#stationdropdownfrom").html('');
                            hide_location_for_fly('to');
                        }
                    });
                }
                else {
                    alert("<?php echo addslashes($langage_lbl['LBL_NOSTATIONS_AVAILABLE']); ?>");
                    $("#stationdropdownfrom").html('');
                    hide_location_for_fly('to');
                }
            });
            // Added and commented by HV on 09-11-2020 as discussed with KS End
        }
        else {
            fromto = '';
        }
    }

    function savelocationid(locationid, fromto, fromchangevehicle) {
        if (!fromchangevehicle) fromchangevehicle = 1;
        setTimeout(function () {
            if (fromto == 'to') {
                $("#iToStationId").val(locationid);
                $('#stations-popup').removeClass('active');
                vehicleId = '';
                eType = $('input[name=eType]:checked').val();
                if (eType == null) {
                    eType = $('input[name=eType]').val();
                }
                showVehicleCountryVise($('#vCountry option:selected').val(), vehicleId, eType);
                showStationsDropdown();
            }
            else {
                $("#iFromStationId").val(locationid);
                $('#stations-popup').removeClass('active');
                if (fromchangevehicle == 1) {
                    vehicleId = '';
                    eType = $('input[name=eType]:checked').val();
                    if (eType == null) {
                        eType = $('input[name=eType]').val();
                    }
                    showVehicleCountryVise($('#vCountry option:selected').val(), vehicleId, eType);
                    showStationsDropdown();
                }
            }
        }, 1000);
    }

    function showStationsDropdown() {
        var useridfly = '<?php echo $db_userdata[0]['iUserId']; ?>';
        var tSessionIdfly = '<?php echo $db_userdata[0]['tSessionId']; ?>';
        var iFromStationId = $("#iFromStationId").val();
        var iToStationId = $("#iToStationId").val();
        var fromlang = '<?php echo addslashes($langage_lbl['LBL_From']); ?>';
        var tolang = '<?php echo addslashes($langage_lbl['LBL_To']); ?>';
        // Added and commented by HV on 09-11-2020 as discussed with KS
        var data = {
            'type': 'getNearestFlyStationsSectionBooking',
            'iUserId': useridfly,
            "GeneralMemberId": useridfly,
            "GeneralUserType": 'Passenger',
            "tSessionId": tSessionIdfly,
            "from_lattitude": $("#from_lat").val(),
            "from_longitude": $("#from_long").val(),
            "lattitude": $("#to_lat").val(),
            "longitude": $("#to_long").val(),
            'iLocationId': '0',
            'iFromStationId': iFromStationId,
            'iToStationId': iToStationId,
            'fromlang': fromlang,
            'tolang': tolang,
            'async_request': false
        };
        data = $.param(data);
        getDataFromApi(data, function (dataHtml2) {
            $("#stationdropdownfrom").html(dataHtml2);
        }, "text");
        // Added and commented by HV on 09-11-2020 as discussed with KS End
    }

    $(document).on('click', '.vehicle-name', function () {
        $(this).closest('.radio-main').find('[name="iLocationId"]').trigger('change');
    });
    $("#vEmail").blur(function () {
        $("#ajax_vEmail").val($("#vEmail").val());
    });

    function triggerflystations(fromto) {
        if (fromto == "from") {
            $('#iFromLocationIdStations').trigger('change');
        }
        else {
            $('#iToLocationIdStations').trigger('change');
        }
    }

    $(document).on('change', '#iFromLocationIdStations', function () {
        var latlng = new google.maps.LatLng($(this).find('option:selected').data('lat'), $(this).find('option:selected').data('lng'));
        $("#from_lat").val(latlng.lat());
        $("#from_long").val(latlng.lng());
        setMarker(latlng, 'from_loc');
    });
    $(document).on('change', '#iToLocationIdStations', function () {
        var latlng = new google.maps.LatLng($(this).find('option:selected').data('lat'), $(this).find('option:selected').data('lng'));
        $("#to_lat").val(latlng.lat());
        $("#to_long").val(latlng.lng());
        setMarker(latlng, 'to_loc');
    });
    $(document).mouseup(function (e) {
        var container = $("#driver_popup");
        var container1 = $("#driver_main_list");
        if (!container.is(e.target) && !container1.is(e.target) && container.has(e.target).length === 0 && container1.has(e.target).length === 0) // ... nor a descendant of the container
        {
            container.hide("slide", {direction: "right"}, 700);
        }
    });

    function getCookie(name) {
        var cookieArr = document.cookie.split(";");
        // Loop through the array elements
        for (var i = 0; i < cookieArr.length; i++) {
            var cookiePair = cookieArr[i].split("=");
            if (name == cookiePair[0].trim()) {
                return decodeURIComponent(cookiePair[1]);
            }
        }
        return null;
    }

    var phonedetailAjaxAbortCount = 0;
    var resPhone = "";

    function showPhoneDetail() {
        var phone = $("#vPhoneOrg").val();
        if (phonedetailAjaxAbort || phonedetailAjaxAbortCount > 0) {
            phonedetailAjaxAbort.abort();
        }

        <?php if ($action == 'Add' || $phone == "") { ?>

        var phone = $('#vPhone').val();

        <?php } ?>

        // var phone = $('#vPhone').val();
        var phoneCode = $('#vPhoneCode').val();
        if (phone != "" && phoneCode != "") {
            if (phonedetailAjaxAbortCount == 0 || resPhone != phone) {
                var ajaxData = {
                    'URL': '<?= $tconfig['tsite_url'] ?>booking/ajax_find_rider_by_number.php',
                    'AJAX_DATA': {phone: phone, phoneCode: phoneCode},
                };
                phonedetailAjaxAbort = getDataFromAjaxCall(ajaxData, function (response) {
                    if (response.action == "1") {
                        var dataHtml = response.result;
                        var trimStr = $.trim(dataHtml); // Added By HJ On 22-02-2020 For Solved 141 Mantis Bug #3681
                        if (trimStr != "") {
                            //$("#vPhone").closest('form').find('.form-group.rederdetail').addClass('floating');
                            //renderdetails("vPhone");
                            $("#user_type").val('registered');
                            var result = dataHtml.split(':');
                            $('#vName').val(result[0]);
                            $('#vLastName').val(result[1]);
                            $('#vEmail').val(result[2]);
                            $('#iUserId').val(result[3]);
                            $('#eStatus').val(result[4]);
                            $('#ajax_vEmail').val(result[5]);
                            phonedetailAjaxAbortCount = 1;
                            if (result[4] == "Inactive" || result[4] == "Deleted") {
                                //$('#inactiveModal').modal('show');
                                $("#inactiveModal").addClass('active');
                            }
                            <? if ($action == 'Add') { ?>
                            $("#promocode").val('');
                            <? } else { ?>
                            $("#promocode").val('<?= $vCouponCode?>');
                            var couponcode = $("#promocode").val();
                            if (cubexthemeon == 'Yes' && couponcode != '') {
                                //$(".discount-block button").toggleClass('icon-apply icon-close');
                                $(".discount-block button").removeClass('icon-apply');
                                $(".discount-block button").addClass('icon-close');
                                $(".clearlink").show();
                                $("#promocode").attr('readonly', '');
                                $("#promocodeapplied").val('1');
                            }
                            <? }?>

                            <?php if ($action == 'Edit' || $userType1 == 'rider') { ?>
                            $('#vPhone').attr('readonly', true);
                            <?php } ?>
                            $('#vName,#vLastName,#vEmail').attr('readonly', true);
                        }
                        else {
                            $("#user_type,#vName,#vLastName,#vEmail,#iUserId,#eStatus").val('');
                            $('#vName,#vLastName,#vEmail,#vPhone').attr('readonly', false);
                        }
                        resPhone = phone;
                        var maskPhone = $('#vPhone').val();
                        $('#vPhoneOrg').val(maskPhone);
                        <?php if ($action == 'Edit') { ?>
                        $("#vCountry").attr('readonly', 'readonly');
                        <?php } ?>
                        if ($('#vPhone').val() != "") {
                            $('#vPhone').closest('.general-form').find('.form-group.rederdetail').each(function (index) {
                                if ($(this).find('input').val() != "") {
                                    $(this).closest('.form-group.rederdetail').addClass('floating');
                                }
                                else {
                                    $(this).closest('.form-group.rederdetail').removeClass('floating');
                                }
                            });
                        }
                        else {
                            $('#vPhone').closest('.general-form').find('.form-group.rederdetail').removeClass('floating');
                        }
                    }
                    else {
                        console.log(response.result);
                    }
                });
            }
        }
        else {
            $("#user_type,#vName,#vLastName,#vEmail,#iUserId,#eStatus").val('');
        }
    }

    function setNewDriverLocations(type) {
        $("#newType").val(type);
        vType = $("#iVehicleTypeId").val();
        for (var i = 0; i < driverMarkers.length; i++) {
            driverMarkers[i].setMap(null);
        }
        var infowindow = new google.maps.InfoWindow();
        for (var i = 0; i < newLocations.length; i++) {
            if (type == newLocations[i].location_type || type == "") {
                var str33 = newLocations[i].location_carType;
                if (vType == "" || (str33 != null && str33.indexOf(vType) != -1)) {
                    newName = newLocations[i].location_name;
                    newOnlineSt = newLocations[i].location_online_status;
                    newLat = newLocations[i].google_map.lat;
                    newLong = newLocations[i].google_map.lng;
                    newDriverImg = newLocations[i].location_image;
                    newMobile = newLocations[i].location_mobile;
                    newDriverID = newLocations[i].location_ID;
                    newImg = newLocations[i].location_icon;
                    latlng = new google.maps.LatLng(newLat, newLong);
                    content = '<table><tr><td rowspan="4"><img src="' + newDriverImg + '" height="60" width="60"></td></tr><tr><td>&nbsp;&nbsp;Email: </td><td><b>' + newDriverID + '</b></td></tr><tr><td>&nbsp;&nbsp;Mobile: </td><td><b>+' + newMobile + '</b></td></tr></table>';
                    var drivermarker = new google.maps.Marker({
                        map: map,
                        //animation: google.maps.Animation.DROP,
                        position: latlng,
                        icon: newImg
                    });
                    google.maps.event.addListener(drivermarker, 'click', (function (drivermarker, content, infowindow) {
                        return function () {
                            infowindow.setContent(content);
                            infowindow.open(map, drivermarker);
                        };
                    })(drivermarker, content, infowindow));
                    driverMarkers.push(drivermarker);
                }
            }
        }
        setDriverListing(vType);
    }

    function getFarevalues(vehicleId) {
        $(".vehicle-caption").css("background-color", "#ececec");
        $(".vehicle-caption").css("border-bottom", "0");
        $("#total_fare_price").text('');
        $("#totalcost").text('');
        $("#estimatedata").html('<div style="text-align:center"><img src="default.gif"></div>');
        //$("#vehicleImage").attr("src", "");
        $("#vehicleImageSpan").html('');
        $("#vehicleName").html('');
        $("#faretxt").html('');
        var booking_date = $("#datetimepicker4").val();
        var vCountry = $('#vCountry').val();
        var tollcostval = $('#fTollPrice').val();
        if (vehicleId == "") {
            vehicleId = $("#iVehicleTypeId").val();
        }
        if (($("#from").val() != "") && ($("#to").val() != "")) {
            var FromLatLong = $("#from_lat").val() + ", " + $("#from_long").val();
            var ToLatLong = $("#to_lat").val() + ", " + $("#to_long").val();
            var from_lat = $("#from_lat").val();
            var from_long = $("#from_long").val();
            var to_lat = $("#to_lat").val();
            var to_long = $("#to_long").val();
        }
        //var timeVal = parseFloat($("#duration").val(), 10) / parseFloat(60, 10);
        //var distanceVal = parseFloat($("#distance").val(), 10) / parseFloat(1000, 10);
        var timeVal = $("#duration").val();
        var distanceVal = $("#distance").val();
        var promoCode = $("#promocode").val();
        var promocodeapplied = $("#promocodeapplied").val();
        var iMemberId = '<?php echo $_SESSION['sess_iUserId']; ?>';
        var userType1 = '<?php echo $userType1; ?>';
        var iFromStationId = $("#iFromStationId").val();
        var iToStationId = $("#iToStationId").val();
        if (vehicleId != "") {

            <? if(ENABLE_WEBSERVICECALL_MANUALBOOKING == 'Yes') { ?>

            var distanceVal = $("#distance").val();
            var timeVal = $("#duration").val();
            var ajaxData = {
                'URL': '<?= $tconfig['tsite_url'] ?>booking/get_webservice_response.php',
                'AJAX_DATA': {
                    'type': 'getEstimateFareDetailsArr',
                    'distance': distanceVal,
                    'promoCode': promoCode,
                    'promocodeapplied': promocodeapplied,
                    'from_lat': from_lat,
                    'from_long': from_long,
                    'to_lat': to_lat,
                    'to_long': to_long,
                    'iMemberId': iMemberId,
                    'userType': userType1,
                    'vehicleId': vehicleId,
                    'eType': eType,
                    'iFromStationId': iFromStationId,
                    'iToStationId': iToStationId,
                    'vCountry': vCountry,
                    'timeduration': timeVal,
                    "GeneralMemberId": useridsession,
                    "GeneralUserType": 'Passenger',
                    "tSessionId": tSessionIdsession
                },
                'REQUEST_DATA_TYPE': 'json'
            };
            getDataFromAjaxCall(ajaxData, function (response) {
                if (response.action == "1") {
                    var dataHtml = response.result;
                    if (dataHtml != "") {
                        var estimateData = dataHtml.estimateArr;
                        var totalFare = dataHtml.totalFare;
                        var vehicleName = dataHtml.vehicleName;
                        var vehicleImage = dataHtml.vehicleImage;
                        var faretxt = dataHtml.faretxt;
                        //flat trip related changes
                        var eFlatTrip = dataHtml.eFlatTrip;
                        var fFlatTripPrice = dataHtml.fFlatTripPrice;
                        var fPickUpPrice = dataHtml.fPickUpPrice;
                        var fNightPrice = dataHtml.fNightPrice;
                        var estimateHtml = "";
                        for (var i = 0; i < estimateData.length; i++) {
                            var eKey = estimateData[i]['key'];
                            var eVal = estimateData[i]['value']
                            estimateHtml += '<li><b>' + eKey + '</b> <em>' + eVal + '</em></li>';
                        }
                        $(".vehicle-caption").css("background-color", "#fff");
                        $(".vehicle-caption").css("border-bottom", "2px solid #d4d4d4");
                        $("#total_fare_price").text(totalFare);
                        $("#totalcost").text(totalFare);
                        $("#estimatedata").html(estimateHtml);
                        $("#vehicleName").html(vehicleName);
                        //$("#vehicleImage").attr("src", vehicleImage);
                        if (vehicleImage != '') {
                            $("#vehicleImageSpan").html('<img src="' + vehicleImage + '" alt="" id="vehicleImage" width="60" height="60">');
                        }
                        $("#faretxt").html(faretxt);
                        //flat trip related changes
                        $("#eFlatTrip").val(eFlatTrip);
                        $("#fFlatTripPrice").val(fFlatTripPrice);
                        $("#fPickUpPrice").val(fPickUpPrice);
                        $("#fNightPrice").val(fNightPrice);
                    }
                    else {
                        $('#minimum_fare_price,#base_fare_price,#dist_fare_price,#time_fare_price,#total_fare_price').text('0');
                    }
                }
                else {
                    console.log(response.result);
                }
            });

            <? } else { ?>

            var ajaxData = {
                'URL': '<?= $tconfig['tsite_url'] ?>booking/ajax_estimate_by_vehicle_type.php',
                'AJAX_DATA': {
                    'vehicleId': vehicleId,
                    'booking_date': booking_date,
                    'vCountry': vCountry,
                    'FromLatLong': FromLatLong,
                    'ToLatLong': ToLatLong,
                    'iMemberId': iMemberId,
                    'userType1': userType1,
                    'timeduration': timeVal,
                    'distance': distanceVal,
                    'promoCode': promoCode,
                    'promocodeapplied': promocodeapplied,
                    'eType': eType,
                    'iFromStationId': iFromStationId,
                    'iToStationId': iToStationId
                },
                'REQUEST_DATA_TYPE': 'json'
            };
            getDataFromAjaxCall(ajaxData, function (response) {
                if (response.action == "1") {
                    var dataHtml = response.result;
                    if (dataHtml != "") {
                        var estimateData = dataHtml.estimateArr;
                        var totalFare = dataHtml.totalFare;
                        var vehicleName = dataHtml.vehicleName;
                        var vehicleImage = dataHtml.vehicleImage;
                        var faretxt = dataHtml.faretxt;
                        var estimateHtml = "";
                        for (var i = 0; i < estimateData.length; i++) {
                            /*console.log(estimateData[i])*/
                            var eKey = estimateData[i]['key'];
                            var eVal = estimateData[i]['value']
                            estimateHtml += '<li><b>' + eKey + '</b> <em>' + eVal + '</em></li>';
                        }
                        $(".vehicle-caption").css("background-color", "#fff");
                        $(".vehicle-caption").css("border-bottom", "2px solid #d4d4d4");
                        $("#total_fare_price").text(totalFare);
                        $("#totalcost").text(totalFare);
                        $("#estimatedata").html(estimateHtml);
                        $("#vehicleName").html(vehicleName);
                        //$("#vehicleImage").attr("src", vehicleImage);
                        if (vehicleImage != '') {
                            $("#vehicleImageSpan").html('<img src="' + vehicleImage + '" alt="" id="vehicleImage" width="60" height="60">');
                        }
                        $("#faretxt").html(faretxt);
                    }
                    else {
                        $('#minimum_fare_price,#base_fare_price,#dist_fare_price,#time_fare_price,#total_fare_price').text('0');
                    }
                }
                else {
                    console.log(response.result);
                }
            });

            <? } ?>

        }
    }

    function showAsVehicleType_all(data) {
        if ($('#from_lat').val() != '' && $('#from_long').val() != '' && $('#to_long').val() != '' && $('#to_long').val() != '') {
            showAsVehicleType($(data).attr("data-val"));
        }
        else {
            $("#estimatedata").html('<?= addslashes($langage_lbl['LBL_MANUAL_BOOKING_PICKUP_DROPOFF_LOCATION']) ?>');
            $("#vehicleName").text('');
            $("#total_fare_price").text('');
        }
    }

    function showAsVehicleType(vType) {
        $("#showdriverSet001").hide();
        $("#driverSet001").html('');
        //$('.deliveryShow').hide();
        $('#iDriverId').val('');
        var eTypeCondition = $('input[name=eType]:checked').val();
        if (eTypeCondition == null || eType == undefined) {
            eTypeCondition = $('input[name=eType]').val();
        }
        //if (eTypeCondition != 'UberX') {
        var vSourceAddresss = $('#from_lat').val();
        var tDestAddress = $('#to_lat').val();
        var vSourceAddresss1 = $('input[name="vSourceAddresss"').val();
        var tDestAddress1 = $('input[name="tDestAddress"').val();
        if ($.trim(vSourceAddresss) == "" || $.trim(vSourceAddresss1) == "") {
            $('#iVehicleTypeId').val('');
            $('input[name="iVehicleTypeId"]:checked').prop('checked', false);
            $('input[name="iDriverVehicleId_ride"]:checked').prop('checked', false);
            $('input[name="iDriverVehicleId_delivery"]:checked').prop('checked', false);
            $('.section-block ul li').each(function (index) {
                var org_src = $(this).find('.vehicle-ico img').attr('org-src');
                $(this).find('.vehicle-ico img').attr('src', org_src);
            });
            var pickmsg = '<?php echo addslashes($langage_lbl['LBL_MANUAL_BOOKING_PICKUP_LOCATION']); ?>';
            if (eTypeCondition == "UberX") {
                var pickmsg = '<?php echo addslashes($langage_lbl['LBL_MANUAL_BOOKING_JOB_LOCATION']); ?>';
            }
            alert(pickmsg);
            $('select.select2data').select2('destroy');
            $('select.select2data').select2();
            return false;
        }
        else if (($.trim(tDestAddress) == "" || $.trim(tDestAddress1) == "") && eTypeCondition != 'UberX') {
            $('#iVehicleTypeId').val('');
            $('input[name="iVehicleTypeId"]:checked').prop('checked', false);
            $('input[name="iDriverVehicleId_ride"]:checked').prop('checked', false);
            $('input[name="iDriverVehicleId_delivery"]:checked').prop('checked', false);
            $('.section-block ul li').each(function (index) {
                var org_src = $(this).find('.vehicle-ico img').attr('org-src');
                $(this).find('.vehicle-ico img').attr('src', org_src);
            });
            var dropmsg = '<?php echo addslashes($langage_lbl['LBL_MANUAL_BOOKING_DROP_LOCATION']); ?>';
            alert(dropmsg);
            return false;
        }
        if ($('input[name=eType]:checked').val() == 'Fly') {
            var iFromLocationIdStations = $('#iFromLocationIdStations option:selected').val();
            var iToLocationIdStations = $('#iToLocationIdStations option:selected').val();
            if (iFromLocationIdStations == '' || iFromLocationIdStations == 'undefined') {
                var dropmsg = '<?php echo addslashes($langage_lbl['LBL_MANUAL_BOOKING_PICKUP_LOCATION']); ?>';
                alert(dropmsg);
                hide_location_for_fly('from');
                return false;
            }
            if (iToLocationIdStations == '' || iToLocationIdStations == 'undefined') {
                var dropmsg = '<?php echo addslashes($langage_lbl['LBL_MANUAL_BOOKING_DROP_LOCATION']); ?>';
                alert(dropmsg);
                hide_location_for_fly('to');
                return false;
            }
        }
        //}
        var type = $("#newType").val();
        for (var i = 0; i < driverMarkers.length; i++) {
            driverMarkers[i].setMap(null);
        }
        var infowindow = new google.maps.InfoWindow();
        for (var i = 0; i < newLocations.length; i++) {
            if (type == newLocations[i].location_type || type == "") {
                var str33 = newLocations[i].location_carType;
                if (vType == "" || (str33 != null && str33.indexOf(vType) != -1)) {
                    newName = newLocations[i].location_name;
                    newOnlineSt = newLocations[i].location_online_status;
                    newLat = newLocations[i].google_map.lat;
                    newLong = newLocations[i].google_map.lng;
                    newDriverImg = newLocations[i].location_image;
                    newMobile = newLocations[i].location_mobile;
                    newDriverID = newLocations[i].location_ID;
                    newImg = newLocations[i].location_icon;
                    latlng = new google.maps.LatLng(newLat, newLong);
                    // bounds.push(latlng);
                    // alert(newImg);
                    content = '<table><tr><td rowspan="4"><img src="' + newDriverImg + '" height="60" width="60"></td></tr><tr><td>&nbsp;&nbsp;Email: </td><td><b>' + newDriverID + '</b></td></tr><tr><td>&nbsp;&nbsp;Mobile: </td><td><b>+' + newMobile + '</b></td></tr></table>';
                    var drivermarker = new google.maps.Marker({
                        map: map,
                        //animation: google.maps.Animation.DROP,
                        position: latlng,
                        icon: newImg
                    });
                    google.maps.event.addListener(drivermarker, 'click', (function (drivermarker, content, infowindow) {
                        return function () {
                            infowindow.setContent(content);
                            infowindow.open(map, drivermarker);
                        };
                    })(drivermarker, content, infowindow));
                    driverMarkers.push(drivermarker);
                }
            }
        }
        //setDriverListing(vType);
        setDriversMarkers();
        getFarevalues(vType);
    }

    /*comment this $("#driver_main_list").html(''); bc driver list nulll after some time

	setInterval(function () {

	    if (eTypeQ11 == 'yes') {

		//setDriversMarkers('test');

		$("#driver_main_list").html('');

	    }

	}, 35000); */
    function setFormBook() {
        var statusVal = $('#vEmail').val();
        if (statusVal != '') {
            var ajaxData = {
                'URL': '<?= $tconfig['tsite_url'] ?>booking/ajax_checkBooking_email.php',
                'AJAX_DATA': 'vEmail=' + statusVal,
            };
            getDataFromAjaxCall(ajaxData, function (response) {
                if (response.action == "1") {
                    var dataHtml = response.result;
                    var testEstatus = dataHtml;
                    if (testEstatus != 'Active' && testEstatus != '') {
                        if (confirm("The selected user account is in 'Inactive / Deleted' mode. Do you want to Active this User ?'")) {
                            eTypeQ11 = 'no';
                            $("#add_booking_form").attr('action', '<?= $tconfig["tsite_url"] ?>booking/action_booking.php');
                            $("#submitbutton").trigger("click");
                            return false;
                        }
                        else {
                            $("#vEmail").focus();
                            return false;
                        }
                    }
                    else {
                        eTypeQ11 = 'no';
                        $("#add_booking_form").attr('action', '<?= $tconfig["tsite_url"] ?>booking/action_booking.php');
                        $("#submitbutton").trigger("click");
                        return false;
                    }
                }
                else {
                    console.log(response.result);
                }
            });
        }
        else {
            return false;
        }
    }

    function get_drivers_list(keyword) {
        vCountry = $("#vCountry").val();
        vType = $("#iVehicleTypeId").val();
        if (vType == '') {
            vType = $('#iVehicleTypeId').find(":checked").val()
        }
        eLadiesRide = 'No';
        eHandicaps = 'No';
        eChildSeat = "No";
        eWheelChair = "No";
        var sess_iCompanyId = '<?= $sess_iCompanyId; ?>';
        if ($("#eFemaleDriverRequest").is(":checked")) {
            eLadiesRide = 'Yes';
        }
        if ($("#eHandiCapAccessibility").is(":checked")) {
            eHandicaps = 'Yes';
        }
        if ($("#eChildSeatAvailable").is(":checked")) {
            eChildSeat = 'Yes';
        }
        if ($("#eWheelChairAvailable").is(":checked")) {
            eWheelChair = 'Yes';
        }
        var ajaxData = {
            'URL': '<?= $tconfig['tsite_url'] ?>booking/cx-get_available_driver_list.php',
            'AJAX_DATA': {
                "lattitude": $("#from_lat").val(),
                "longitude": $("#from_long").val(),
                toLat: $("#to_lat").val(),
                toLong: $("#to_long").val(),
                vCountry: vCountry,
                keyword: keyword,
                iVehicleTypeId: vType,
                eLadiesRide: eLadiesRide,
                eHandicaps: eHandicaps,
                eChildSeat: eChildSeat,
                eWheelChair: eWheelChair,
                AppeType: eType,
                sess_iCompanyId: sess_iCompanyId
            },
            'REQUEST_DATA_TYPE': 'html'
        };
        getDataFromAjaxCall(ajaxData, function (response) {
            if (response.action == "1") {
                var dataHtml2 = response.result;
                $('#driver_main_list').show();
                if (dataHtml2 != "") {
                    $('#driver_main_list').html(dataHtml2);
                }
                else {
                    $('#driver_main_list').html('<h4 style="margin:25px 0 0 15px">Sorry , No <?= addslashes($langage_lbl['LBL_DRIVER_TXT_ADMIN']); ?> Found.</h4>');
                }
                if ($("#eAutoAssign").is(':checked')) {
                    $(".assign-driverbtn").attr('disabled', 'disabled');
                }
                $("#imageIcon").hide();
            }
            else {
                console.log(response.result);
            }
        });
    }

    $("#eAutoAssign").on('change', function () {
        if ($(this).prop('checked')) {
            $("#iDriverId").val('');
            $("#iDriverId").attr('disabled', 'disabled');
            $(".assign-driverbtn").attr('disabled', 'disabled');
            $("#showdriverSet001").hide();
            //$('#myModalautoassign').modal('show');
            $("#myModalautoassign").addClass('active');
            $('[data-name="OR"], .driverlists').hide();
        }
        else {
            $("#iDriverId").removeAttr('disabled');
            $(".assign-driverbtn").removeAttr('disabled');
            //$('#myModalautoassign').modal('hide');
            $("#myModalautoassign").removeClass('active');
            $('[data-name="OR"], .driverlists').show();
        }
    });
    var bookId = '<?php echo $iCabBookingId; ?>';
    if (bookId != "") {
        if ($("#eAutoAssign").prop('checked')) {
            $("#iDriverId").val('');
            $("#iDriverId").attr('disabled', 'disabled');
        }
        else {
            $("#iDriverId").removeAttr('disabled');
        }
    }
    $(document).ready(function () {
        //$("#td_id").addClass('newClass');
        //$(".discount-block button").addClass('newClass');
        var referrer;
        if ($("#previousLink").val() == "") {
            referrer = document.referrer;
            //alert(referrer);
        }
        else {
            referrer = $("#previousLink").val();
        }
        if (referrer == "") {
            referrer = "<?= $tconfig["tsite_url"] ?>booking/cab_booking.php";
        }
        else {
            $("#backlink").val(referrer);
        }
        if (typeof $.fn.tooltip.noConflict === "function") {
            var bootstrapTooltip = $.fn.tooltip.noConflict();
            $.fn.bootstrapTooltip = bootstrapTooltip;
        }
        $(document).tooltip({
            position: {
                my: "center bottom-20",
                at: "center top",
                using: function (position, feedback) {
                    $(this).css(position);
                    $("<div>")
                        .addClass("arrow")
                        .addClass(feedback.vertical)
                        .addClass(feedback.horizontal)
                        .appendTo(this);
                }
            }
        });
        checkNavigatorPermissionStatus();
        setInterval(function () {
            checkNavigatorPermissionStatus();
        }, 1000);

        <?php if (($ENABLE_MULTI_VIEW_IN_SINGLE_DELIVERY == 'Yes') && ($APP_TYPE == 'Ride-Delivery' || $APP_TYPE == 'Delivery' || $APP_TYPE == 'Ride-Delivery-UberX') ) { ?>
            saveSingleDeliveryDetails();
        <?php } ?>
    });
    $('#datetimepicker45').keydown(function (e) {
        e.preventDefault();
        return false;
    });
    $('#eFemaleDriverRequest').click(function () {
        if ($(this).is(':checked'))
            setDriversMarkers('true');
        else
            setDriversMarkers('true');
    });
    $('#eHandiCapAccessibility').click(function () {
        if ($(this).is(':checked'))
            setDriversMarkers('true');
        else
            setDriversMarkers('true');
    });
    $('#eChildSeatAvailable').click(function () {
        if ($(this).is(':checked'))
            setDriversMarkers('true');
        else
            setDriversMarkers('true');
    });
    $('#eWheelChairAvailable').click(function () {
        if ($(this).is(':checked'))
            setDriversMarkers('true');
        else
            setDriversMarkers('true');
    });
    $('#reset12').click(function () {
        window.location.reload(true);
    });

    function checkrestrictionfrom(type) {
        if (($("#from").val() != "") || ($("#to").val() != "")) {
            var ajaxData = {
                'URL': '<?= $tconfig['tsite_url'] ?>booking/checkForRestriction.php',
                'AJAX_DATA': {fromLat: $("#from_lat").val(), fromLong: $("#from_long").val(), type: type},
                'REQUEST_DATA_TYPE': 'html'
            };
            getDataFromAjaxCall(ajaxData, function (response) {
                if (response.action == "1") {
                    var dataHtml5 = response.result;
                    if ($.trim(dataHtml5) != '') {
                        alert($.trim(dataHtml5));
                    }
                }
                else {
                    console.log(response.result);
                }
            });
            showVehicleCountryVise('<?php echo $vCountry ?>', '<?php echo $iVehicleTypeId; ?>', eType, 1, 'from'); //added by SP for get vehicles/services according to the pickup location, put here because vehicle list change when from location entered on 02-08-2019
        }
    }

    function checkrestrictionto(type) {
        if (($("#from").val() != "") || ($("#to").val() != "")) {
            var ajaxData = {
                'URL': '<?= $tconfig['tsite_url'] ?>booking/checkForRestriction.php',
                'AJAX_DATA': {toLat: $("#to_lat").val(), toLong: $("#to_long").val(), type: type},
                'REQUEST_DATA_TYPE': 'html'
            };
            getDataFromAjaxCall(ajaxData, function (response) {
                if (response.action == "1") {
                    var dataHtml5 = response.result;
                    if ($.trim(dataHtml5) != '') {
                        alert($.trim(dataHtml5));
                    }
                }
                else {
                    console.log(response.result);
                }
            });
            if (eType == 'Fly') {
                showVehicleCountryVise('<?php echo $vCountry ?>', '<?php echo $iVehicleTypeId; ?>', eType, 1, 'to'); //added by SP for get vehicles/services according to the pickup location, put here because vehicle list change when from location entered on 02-08-2019
            }
        }
    }

    $('#add_booking_form').on('keyup keypress', function (e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13 && ($(event.target)[0] != $("textarea")[0])) {
            e.preventDefault();
            return false;
        }
    });
    $("#submitbutton").on("click", function (event) {
       
        
        if (("<?php echo SITE_TYPE; ?>" == "Demo") && ("<?php echo !empty($_SESSION['SessionUserType']) ? $_SESSION['SessionUserType'] : ''; ?>" == "Admin")) {
            window.location = "<?= $tconfig["tsite_url"]; ?>/admin/cab_booking.php?success=2";
            return false;
        }
        var from_lat_long = $("#from_lat_long").val();
        var to_lat_long = $("#to_lat_long").val();
        var isvalidate = $("#add_booking_form")[0].checkValidity();
        //alert(isvalidate + "aaaaaaaaaaaaa");
        if (eType == '' ||  eType == undefined) {
            var eType = $('input[name="eType"]:checked').val();
            if (eType == undefined) {
                var eType = $('input[name="eType"]').val();
            }
        }
 
        if (eType == 'Ride') {
            var idClicked = $("input[name='eRideType']:checked").val();
        }
        else if (eType == 'Deliver') {
            var idClicked = $("input[name='eDeliveryType']:checked").val();
        }
        else if (eType == 'UberX') {
            var idClicked = 'later';
        }
        else {
            var idClicked = $("input[name='eRideType']:checked").val();
        }
        $("#idClicked").val(idClicked);
        var bookingfrom = '<?php echo $eBookingFrom; ?>';
        if (eType == 'Ride' && bookingfrom != 'Company') {
            //var checkbox = $("input[name='iDriverVehicleId_ride']:checked");
            var checkbox = $("input[name='iVehicleTypeId']:checked");
            if ($('#from').val() != '' && $('#to').val() != '' && checkbox.length <= 0) {
                alert("<?= addslashes($langage_lbl['LBL_SELECT_CAR_MESSAGE_TXT']) ?>");
                $(".stepper li").removeClass("active");
                $(".stepper li:nth-child(3)").addClass("active");
                $(".stepper li:nth-child(3) .step-content").show();
                return false;
            }
        }
        $('#1_Multi , #2_Multi ,#3_Multi , #4_Multi').removeAttr('required');
        if (eType == 'Deliver' && bookingfrom != 'Company') {
            if (($('#1_Multi').length > 0 && $('#1_Multi').val() == '') || ($('#2_Multi').length > 0 && $('#2_Multi').val() == '') || ($('#3_Multi').length > 0 && $('#3_Multi').val() == '') || ($('#4_Multi').length > 0 && $('#4_Multi').val() == '')) {
                $('.delivery_options_error').remove();
                if ($('#1_Multi').length > 0 && $('#1_Multi').val() == '') {
                    $("#1_Multi").after("<p class = 'error delivery_options_error'> <?php echo addslashes($langage_lbl['LBL_FEILD_REQUIRD']); ?> </p>");
                }
                if ($('#2_Multi').length > 0 && $('#2_Multi').val() == '') {
                    $("#2_Multi").after("<p class = 'error delivery_options_error'> <?php  echo addslashes($langage_lbl['LBL_FEILD_REQUIRD']); ?> </p>");
                }
                if ($('#3_Multi').length > 0 && $('#3_Multi').val() == '') {
                    $("#3_Multi").after("<p class = 'error delivery_options_error'> <?php  echo addslashes($langage_lbl['LBL_FEILD_REQUIRD']); ?> </p>");
                }
                if ($('#4_Multi').length > 0 && $('#4_Multi').val() == '') {
                    $("#4_Multi").after("<p class = 'error delivery_options_error'> <?php  echo addslashes($langage_lbl['LBL_FEILD_REQUIRD']); ?> </p>");
                }
                $(".stepper li").removeClass("active");
                $(".stepper li:nth-child(2)").addClass("active");
                $(".stepper li:nth-child(2) .step-content").show();
                return false;
            }
            //var checkboxdel = $("input[name='iDriverVehicleId_delivery']:checked");
            var checkboxdel = $("input[name='iVehicleTypeId']:checked");
            if (checkboxdel.length <= 0 && $('#iPackageTypeId').val() != '' && $('#vReceiverName').val() != '' && $('#vReceiverMobile').val() != '' && $('#from').val() != '' && $('#to').val() != '') {
                alert("<?= addslashes($langage_lbl['LBL_SELECT_CAR_MESSAGE_TXT']) ?>");
                $(".stepper li").removeClass("active");
                $(".stepper li:nth-child(3)").addClass("active");
                $(".stepper li:nth-child(3) .step-content").show();
                //return false;
            }
        }
        if (eType == 'Ride' && idClicked == 'later') {
            if ($("#datetimepicker4").val() == '') {
                alert("<?= addslashes($langage_lbl['LBL_SELECT_DATE_TIME']) ?>");
                $(".stepper li").removeClass("active");
                $(".stepper li:nth-child(4)").addClass("active");
                $(".stepper li:nth-child(4) .step-content").show();
                return false;
            }
        }
        else if (eType == 'Deliver' && idClicked == 'later') {
            if ($("#datetimepicker4").val() == '') {
                alert("<?= addslashes($langage_lbl['LBL_SELECT_DATE_TIME']) ?>");
                $(".stepper li").removeClass("active");
                $(".stepper li:nth-child(4)").addClass("active");
                $(".stepper li:nth-child(4) .step-content").show();
                return false;
            }
        }
        else if (eType == 'UberX') {
            if ($('#iVehicleTypeId').find(":checked").val() != undefined && $('#iVehicleTypeId').find(":checked").val() != null && $('#iVehicleTypeId').find(":checked").val() != '') {
                if ($("#datetimepicker4").val() == '') {
                    alert("<?= addslashes($langage_lbl['LBL_SELECT_DATE_TIME']) ?>");
                    $(".stepper li").removeClass("active");
                    $(".stepper li:nth-child(4)").addClass("active");
                    $(".stepper li:nth-child(4) .step-content").show();
                    return false;
                }
            } else {
                 alert("<?= addslashes($langage_lbl['LBL_SELECT_CAR_MESSAGE_TXT']) ?>");
                    $(".stepper li").removeClass("active");
                    $(".stepper li:nth-child(3)").addClass("active");
                    $(".stepper li:nth-child(3) .step-content").show();
                    return false;
            }
        }
           
        if (idClicked == 'later') {
            var MINIMUM_HOURS_LATER_BOOKING = parseInt("<?= $MINIMUM_HOURS_LATER_BOOKING ?>");
            var MAXIMUM_HOURS_LATER_BOOKING = parseInt("<?= $MAXIMUM_HOURS_LATER_BOOKING ?>");
            //var todayDate = moment().add((MINIMUM_HOURS_LATER_BOOKING * 60) + 1, 'm').format('YYYY-MM-DD HH:mm:ss');
            var todayDate = moment().add(MINIMUM_HOURS_LATER_BOOKING, 'minutes').format('YYYY-MM-DD HH:mm:ss');
            todayDate = new Date(todayDate).getTime();
            // var maxBookingDate = moment().add((MAXIMUM_HOURS_LATER_BOOKING * 60) + 1, 'm').format('YYYY-MM-DD HH:mm:ss');
            var maxBookingDate = moment().add(MAXIMUM_HOURS_LATER_BOOKING + 1, 'minutes').format('YYYY-MM-DD HH:mm:ss');
            maxBookingDate = new Date(maxBookingDate).getTime();
            var selectedDate = new Date($('#datetimepicker45').val()).getTime();
            if (selectedDate < todayDate) {
                //console.log('<?= addslashes($langage_lbl['LBL_INVALID_PICKUP_MAX_NOTE_MSG']) ?>');
                alert('<?= addslashes($langage_lbl['LBL_INVALID_PICKUP_MAX_NOTE_MSG']) ?>');
                // LBL_INVALID_PICKUP_NOTE_MSG
                return false;
            }
            /*var minHours = Math.round(Math.abs(todayDate.getTime() - (new Date()).getTime()) / 3600000);
            if (minHours > 1) {
                minHours += ' <?= addslashes($langage_lbl['LBL_HOURS_TXT']) ?>';
            } else {
                if (minHours < 1) {
                    minHours = Math.abs(todayDate.getTime() - (new Date()).getTime()) / 3600000;
                    minHours = Math.floor(minHours * 60);
                    minHours += ' <?= addslashes($langage_lbl['LBL_MINUTES_TXT']) ?>';
                } else {
                    minHours += ' <?= addslashes($langage_lbl['LBL_HOUR_TXT']) ?>';
                }
            }
            var maxHours = Math.round(Math.abs(maxBookingDate.getTime() - (new Date()).getTime()) / 3600000);
            if (maxHours > 24) {
                maxHours = Math.round(maxHours / 24);
                if (maxHours > 1) {
                    maxHours += ' <?= addslashes($langage_lbl['LBL_DAYS_TXT']) ?>';
                } else {
                    maxHours += ' <?= addslashes($langage_lbl['LBL_DAY_TXT']) ?>';
                }
            } else {
                maxHours += ' <?= addslashes($langage_lbl['LBL_HOURS_TXT']) ?>';
            }
            var LBL_INVALID_PICKUP_MAX_NOTE_MSG = "<?= addslashes($langage_lbl['LBL_INVALID_PICKUP_MAX_NOTE_MSG']) ?>";
            LBL_INVALID_PICKUP_MAX_NOTE_MSG = LBL_INVALID_PICKUP_MAX_NOTE_MSG.replace('#####', maxHours);
            LBL_INVALID_PICKUP_MAX_NOTE_MSG = LBL_INVALID_PICKUP_MAX_NOTE_MSG.replace('####', minHours);*/
            if (selectedDate > maxBookingDate) {
                alert('<?= addslashes($langage_lbl['LBL_INVALID_PICKUP_MAX_NOTE_MSG']) ?>');
                return false;
            }
            if (!$('#eAutoAssign').is(":checked") && ($('#iDriverId').val() == 0 || $('#iDriverId').val() == "")) {
                alert('<?= addslashes($langage_lbl['LBL_ASSIGN_TO_DRIVER']) ?>');
                return false;
            }
        }
        //alert(aa + "Final");
        //return false;
        if (isvalidate) {
            event.preventDefault();
            var country = $('select[name=vCountry]').val();
            var eTollEnabled = '';
            if (country != "") {
                var ajaxData = {
                    'URL': '<?= $tconfig['tsite_url'] ?>booking/ajax_check_toll.php',
                    'AJAX_DATA': {vCountryCode: country},
                    'REQUEST_DATA_TYPE': 'html',
                    'REQUEST_ASYNC': false
                };
                getDataFromAjaxCall(ajaxData, function (response) {
                    if (response.action == "1") {
                        var dataHtml5 = response.result;
                        eTollEnabled = dataHtml5;
                        return eTollEnabled;
                    }
                    else {
                        console.log(response.result);
                    }
                });
            }
            if ($.trim(eTollEnabled) == 'Yes') {
                if ($.trim(eType) != 'UberX' && $.trim(eType) != 'Fly' && $.trim(eType) != 'Deliver' && $.trim(eFlatTrip) != 'Yes') {
                    if (ENABLE_TOLL_COST == 'Yes') {
                        $(".loader-default").show();
                        if (($("#from").val() != "" && $("#from_lat_long").val() != "") && ($("#to").val() != "" && $("#to_lat_long").val() != "")) {
                            var newFromtoll = $("#from_lat").val() + "," + $("#from_long").val();
                            var newTotoll = $("#to_lat").val() + "," + $("#to_long").val();
                            $.getJSON("https://tce.cit.api.here.com/2/calculateroute.json?app_id=<?= $TOLL_COST_APP_ID ?>&app_code=<?= $TOLL_COST_APP_CODE ?>&waypoint0=" + newFromtoll + "&waypoint1=" + newTotoll + "&mode=fastest;car", function (result) {
                                
                                if(result.costs != null || result.costs != undefined) {
                                    var tollCurrency = result.costs.currency;
                                    var tollCost = result.costs.details.tollCost;

                                    <?php if ($eTollSkipped == 'Yes') { ?>

                                    var tollskip = 'Yes';

                                    <?php } else { ?>

                                    var tollskip = 'No';

                                    <?php }
                                    if (!empty($_SESSION['sess_currency'])) {
                                        $currency_data = $obj->MySQLSelect("SELECT eReverseformattingEnable, vSymbol,vName,eReverseSymbolEnable,eDefault from `currency` WHERE  vName = '" . $_SESSION['sess_currency'] . "'");
                                    }
                                    else {
                                        $currency_data = $obj->MySQLSelect("SELECT eReverseformattingEnable, vSymbol,vName,eReverseSymbolEnable,eDefault from `currency` WHERE  eDefault = 'Yes'");
                                    }
                                    ?>
                                    <? if($currency_data[0]['eReverseSymbolEnable'] == 'Yes'){ ?>
                                    $('#tollcost').text(tollCost + " " + tollCurrency);
                                    <? } else { ?>
                                    $('#tollcost').text(tollCurrency + " " + tollCost);
                                    <? } ?>
                                    if ($.trim(tollCost) != '0.0' && $.trim(tollCost) != "" && $.trim(tollCost) != '0') {
                                        $(".loader-default").hide();
                                        $("#vTollPriceCurrencyCode_temp").val(tollCurrency);
                                        $("#fTollPrice_temp").val(tollCost);
                                        $("#eTollSkipped_temp").val(tollskip);
                                        $("#idClicked").val(idClicked);
                                        show_alert("<?= addslashes($langage_lbl['LBL_TOLL_ROUTE']) ?>", $(".form-content").html(), "<?= addslashes($langage_lbl['LBL_CONTINUE_BTN']) ?>", "<?= addslashes($langage_lbl['LBL_CLOSE_TXT']) ?>", "", function (btn_id) {
                                            if (btn_id == 0) {
                                                //$("#add_booking_form").submit();
                                                $("#vTollPriceCurrencyCode").val($("#vTollPriceCurrencyCode_temp").val());
                                                $("#fTollPrice").val($("#fTollPrice_temp").val());
                                                $("#eTollSkipped").val($("#eTollSkipped_temp").val());
                                                var eTollSkipped1 = $("input[name=eTollSkipped1]:checked").val();
                                                $("#eTollSkipped").val(eTollSkipped1);
                                                SafetyChecklistPassengerLimit();
                                                // SubmitFormCheck($("#idClicked").val());
                                                // return true;
                                            }
                                            else {
                                                $(".custom-modal-first-div").removeClass("active");
                                                return false;
                                            }
                                        });
                                    }
                                    else {
                                        $(".loader-default").hide();
                                        SafetyChecklistPassengerLimit();
                                        //SubmitFormCheck(idClicked);
                                        //return true;
                                    }
                                }else{
                                    $(".loader-default").hide();
                                    SafetyChecklistPassengerLimit();
                                }
                            }).fail(function (jqXHR, textStatus, errorThrown) {
                                $(".loader-default").hide();
                                SafetyChecklistPassengerLimit();
                                //SubmitFormCheck(idClicked);
                            });
                        }
                    }
                    else {
                        //$("#add_booking_form").submit();
                        SafetyChecklistPassengerLimit();
                        //SubmitFormCheck(idClicked);
                        //return true;
                    }
                }
                else {
                    //$("#add_booking_form").submit();
                    SafetyChecklistPassengerLimit();
                    //SubmitFormCheck(idClicked);
                    //return true;
                }
            }
            else {
                //$("#add_booking_form").submit();
                SafetyChecklistPassengerLimit();
                //SubmitFormCheck(idClicked);
                //return true;
            }
        }
        else {
            /*var form_data=$("#add_booking_form").serializeArray();

		 for (var input in form_data){

		 var element=$("#"+form_data[input]['name']);

		 var valid=element.hasClass("valid");

		 console.log(valid + "Aaaaaa" + form_data[input]['name']);

		 var error_element=$("span", element.parent());

		 if (!valid){error_element.removeClass("error").addClass("error_show"); error_free=false;}

		 else{error_element.removeClass("error_show").addClass("error");}

		 }*/
            var eType = $('input[name=eType]:checked').val();
            if (eType == undefined) {
                var eType = $('input[name="eType"]').val();
            }
            /*if(eType=='UberX') {

		 var vehicleTypeId = $('#iVehicleTypeId').find(":selected").val();

		 } else {*/
            var vehicleTypeId = $('#iVehicleTypeId').find(":checked").val();
            //}
            var eRideType = $('input[name=eRideType]:checked').val();
            var eDeliveryType = $('input[name=eDeliveryType]:checked').val();
            var vReceiverMobile = document.getElementById('vReceiverMobile');
            var filter = /^[0-9]{1,}$/;
            var phonevalidation = 0;
            /*if (!filter.test(vReceiverMobile.value)) {
                phonevalidation = 1;
            }*/
            if ($("#vPhone").val() == '' || $("#vName").val() == '' || $("#vLastName").val() == '' || $("#vEmail").val() == '') {
                $(".stepper li").removeClass("active");
                $(".stepper li:nth-child(2)").addClass("active");
                $(".stepper li:nth-child(2) .step-content").show();
            }
            else if (!isValidEmail()) {
                $(".stepper li").removeClass("active");
                $(".stepper li:nth-child(2)").addClass("active");
                $(".stepper li:nth-child(2) .step-content").show();
            }
            else if (eType == 'Deliver' && ($("#iPackageTypeId").val() == '' || $("#vReceiverName").val() == '' || $("#vReceiverMobile").val() == '' || phonevalidation == 1)) {
                $(".stepper li").removeClass("active");
                $(".stepper li:nth-child(2)").addClass("active");
                $(".stepper li:nth-child(2) .step-content").show();
            }
            else if (!vehicleTypeId) {
                $(".stepper li").removeClass("active");
                $(".stepper li:nth-child(3)").addClass("active");
                $(".stepper li:nth-child(3) .step-content").show();
            }
            else if (eType == 'Ride' && eRideType == 'later') {
                if ($("#datetimepicker4").val() == '' || $("#iDriverId").val() == '') {
                    $(".stepper li").removeClass("active");
                    $(".stepper li:nth-child(4)").addClass("active");
                    $(".stepper li:nth-child(4) .step-content").show();
                }
            }
            else if (eType == 'Deliver' && eDeliveryType == 'later') {
                if ($("#datetimepicker4").val() == '' || $("#iDriverId").val() == '') {
                    $(".stepper li").removeClass("active");
                    $(".stepper li:nth-child(4)").addClass("active");
                    $(".stepper li:nth-child(4) .step-content").show();
                }
            }
            else if (eType == 'UberX') {
                if ($("#datetimepicker4").val() == '' || $("#iDriverId").val() == '') {
                    $(".stepper li").removeClass("active");
                    $(".stepper li:nth-child(4)").addClass("active");
                    $(".stepper li:nth-child(4) .step-content").show();
                }
            }
        }
    });

    function femaleriders() {
        //var eFemaleDriverRequest = '<?php echo $eFemaleDriverRequest; ?>';
        var FEMALE_RIDE_REQ_ENABLE = '<?php echo $FEMALE_RIDE_REQ_ENABLE; ?>';
        var eGender = $("#eGender").val();
        if (eGender == '') {
            $("#femaleriders").addClass("active");
        }
    }

    function gender_select(val) {
        $("#eGender").val(val);
        var ajaxData = {
            'URL': '<?= $tconfig['tsite_url'] ?>booking/ajax_update_gender.php',
            'AJAX_DATA': {vGender: val},
            'REQUEST_DATA_TYPE': 'html'
        };
        getDataFromAjaxCall(ajaxData, function (response) {
            if (response.action == "1") {
                var data = response.result;
                if (data > 0) {
                    $("#femaleriders").removeClass("active");
                    if (val == 'Male') {
                        $("#femalediv").hide();
                    }
                }
            }
            else {
                console.log(response.result);
            }
        });
    }

    <?php
    /* commented this code becoz not working in chrome
     *
     *$user_agent = $_SERVER['HTTP_USER_AGENT'];
    if (isset($_SERVER['HTTP_USER_AGENT']) && ( (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false ) || (strpos($_SERVER['HTTP_USER_AGENT'], 'Trident/7.0; rv:11.0') !== false) ) ){

    }elseif (stripos( $user_agent, 'Safari') !== false){


    }else{*/ ?>

    class HTMLMapMarker extends google.maps.OverlayView {
        // Constructor accepting args
        constructor(args) {
            super();
            this.latlng = args.latlng;
            this.html = args.html;
            this.setMap(args.map);
        }

        // Create the div with content and add a listener for click events
        createDiv() {
            this.div = document.createElement('div');
            this.div.style.position = 'absolute';
            if (this.html) {
                this.div.innerHTML = this.html;
            }
            google.maps.event.addDomListener(this.div, 'click', event => {
                google.maps.event.trigger(this, 'click');
            });
        }

        // Append to the overlay layer
        // Appending to both overlayLayer and overlayMouseTarget which should allow this to be clickable
        appendDivToOverlay() {
            const panes = this.getPanes();
            panes.overlayLayer.appendChild(this.div);
            panes.overlayMouseTarget.appendChild(this.div);
        }

        // Position the div according to the coordinates
        positionDiv() {
            const point = this.getProjection().fromLatLngToDivPixel(this.latlng);
            if (point) {
                this.div.style.left = `${point.x}px`;
                this.div.style.top = `${point.y}px`;
            }
        }

        // Create the div and append to map
        draw() {
            if (!this.div) {
                this.createDiv();
                this.appendDivToOverlay();
            }
            this.positionDiv();
        }

        // Remove this from map
        remove() {
            if (this.div) {
                this.div.parentNode.removeChild(this.div);
                this.div = null;
            }
        }

        // Return lat and long object
        getPosition() {
            return this.latlng;
        }

        // Return whether this is draggable
        getDraggable() {
            return false;
        }
    }
    <?php //} ?>


    $('.pickup-schedule-img').click(function () {
        $(this).prev('input').focus();
    });
    $("#dateclick").click(function () {
        $("#datemodel").addClass('active');
        return false;
    });

    function selectdate() {
        var date = $("#datetimepicker45").val();
        var elem = date.split(' ');
        var time = elem[1] + " " + elem[2];
        var hrs = Number(time.match(/^(\d+)/)[1]);
        var mnts = Number(time.match(/:(\d+)/)[1]);
        var format = time.match(/\s(.*)$/)[1];
        if (format == "PM" && hrs < 12) hrs = hrs + 12;
        if (format == "AM" && hrs == 12) hrs = hrs - 12;
        var hours = hrs.toString();
        var minutes = mnts.toString();
        if (hrs < 10) hours = "0" + hours;
        if (mnts < 10) minutes = "0" + minutes;
        var date123 = elem[0] + " " + hours + ':' + minutes + ':' + '00';
        $("#datetimepicker45").val(date123);
        $("#datetimepicker4").val($("#datetimepicker45").val());
        //console.log(date);
        //$(".dBooking_date_display").text($("#datetimepicker45").val()); // commented due to QA point. display same format on select date
        $(".dBooking_date_display").text(date);
    }

    /*$( window ).on( "orientationchange", function( event ) {
	//location.reload(true);
	google.maps.event.trigger(map, "resize");
    }); */
    // listen for the window resize event & trigger Google Maps to update too
    $(window).resize(function () {
        //google.maps.event.trigger(map, "resize");
        /*var $mapDiv = $('#map-canvas');
    var mapDim = { height: $mapDiv.height(), width: $mapDiv.width() };

    console.log(mapDim);*/
        /*if(window.innerHeight < window.innerWidth){
	map.setZoom(map.getZoom() - 1);
    }
    if(window.innerHeight < window.innerWidth){
	map.setZoom( map.getZoom() + 1 );
    }*/
        $("#map-canvas").css("position", "");
    });

    function checkSubmitForm() {
        //flat trip,surge charge related changes
        checkflattrip();
        //SubmitFormCheck($("#idClicked").val());
    }

    function checkSubmitFormFinally() {
        SubmitFormCheck($("#idClicked").val());
    }

    function SafetyChecklistPassengerLimit() {
        eType = $('[name="eType"]:checked').val();
        if (eType == null) {
            eType = $('input[name=eType]').val();
        }

        <?php if($MODULES_OBJ->isEnableSafetyGuidelines() && $_SESSION['sess_user'] == "rider") { ?>
        if (eType == "Ride" || eType == "Fly" || eType == "Moto") {
            <?php if(strtoupper($ENABLE_SAFETY_FEATURE_RIDE) == "YES") { ?>
            <?php if($MODULES_OBJ->isEnableRestrictPassengerLimit()) { ?>
            var person_limit = $('[name="iVehicleTypeId"]:checked').data('personsize');
            $('.passenger-limit-note').show();
            $('#person_limit').text(person_limit);
            <?php } ?>
            $('#ride_safety_guidelines_modal').addClass('active');
            return false;
            <?php } else { ?>
            checkSubmitForm();
            <?php } ?>
        }
        else if (eType == "Deliver") {
            <?php if(strtoupper($ENABLE_SAFETY_FEATURE_DELIVERY) == "YES") { ?>
            $('.passenger-limit-note').hide();
            $('#ride_safety_guidelines_modal').addClass('active');
            return false;
            <?php } else { ?>
            checkSubmitForm();
            <?php } ?>
        }
        else if (eType == "UberX") {
            <?php if(strtoupper($ENABLE_SAFETY_FEATURE_UFX) == "YES") { ?>
            $('.passenger-limit-note').hide();
            $('#ride_safety_guidelines_modal').addClass('active');
            return false;
            <?php } else { ?>
            checkSubmitForm();
            <?php } ?>
        }
        <?php } elseif ($MODULES_OBJ->isEnableRestrictPassengerLimit() && $_SESSION['sess_user'] == "rider") { ?>

        if (eType == "Ride" || eType == "Fly" || eType == "Moto") {
            <?php $person_limit = str_replace('#PERSON_LIMIT#', '<span id="person_limit"></span>', $langage_lbl['LBL_CURRENT_PERSON_LIMIT']); ?>

            show_alert("", '<?= addslashes($person_limit) ?>', "<?= addslashes($langage_lbl['LBL_BOOK']) ?>", "<?= addslashes($langage_lbl['LBL_CANCEL_TXT']) ?>", "", function (btn_id) {
                if (btn_id == 0) {
                    checkSubmitForm();
                }
            });
            setInterval(function () {
                var person_limit = $('[name="iVehicleTypeId"]:checked').data('personsize');
                $('#person_limit').text(person_limit);
                $('.passenger-limit-note').show();
            }, 100);
            return false;
        }

        <?php } ?>

        checkSubmitForm();
        //SubmitFormCheck($("#idClicked").val());
        return true;
    }

    $(document).on('click', '.radio-hold input', function () {
        var data_selcetedlogo = $(this).closest('.section-block ul li').find('.vehicle-ico img').attr('data-selcetedlogo');
        $('.section-block ul li').each(function (index) {
            var org_src = $(this).find('.vehicle-ico img').attr('org-src');
            $(this).find('.vehicle-ico img').attr('src', org_src);
        });
        $(this).closest('.section-block ul li').find('.vehicle-ico img').attr('src', data_selcetedlogo);
    });

    function showInfo(descid) {
        var info_img = '<img src="<?= $tconfig['tsite_url'] . 'assets/img/delivery-helper.svg' ?>" class="popup-img">';
        var info_description = '<div class="popup-title"><?= addslashes($langage_lbl['LBL_DEL_HELPER']) ?></div><div class="popup-desc delivery-helper-info">' + $('#' + descid).html();
        var info_btn = '<?= addslashes($langage_lbl['LBL_BTN_OK_TXT']) ?>';
        show_modal_popup(info_img, info_description, "", "", info_btn);
    }

    //flat trip,surge charge related changes
    function checkflattrip() {
        var eFlatTrip = $("#eFlatTrip").val();
        var fFlatTripPrice = $("#fFlatTripPrice").val();
        var fPickUpPrice = $("#fPickUpPrice").val();
        var fNightPrice = $("#fNightPrice").val();
        var surgecharge = '';
        if (fPickUpPrice != 0) {
            var surgecharge = fPickUpPrice;
            var langlabelsurge = '<?php echo addslashes($langage_lbl['LBL_PICK_SURGE_NOTE']) ?>';
        }
        if (fNightPrice != 0) {
            var surgecharge = fNightPrice;
            var langlabelsurge = '<?php echo addslashes($langage_lbl['LBL_NIGHT_SURGE_NOTE']) ?>';
        }
        if (eFlatTrip == 'Yes') {
            priceDetails = fFlatTripPrice;
            if (surgecharge != '') {
                priceDetails = fFlatTripPrice + "(<?= addslashes($langage_lbl['LBL_AT_TXT']); ?> " + surgecharge + "X)";
            }
            show_alert("<?= addslashes($langage_lbl['LBL_FIX_FARE_HEADER']) ?>", priceDetails, "<?= addslashes($langage_lbl['LBL_CONTINUE_BTN']) ?>", "<?= addslashes($langage_lbl['LBL_CLOSE_TXT']) ?>", "", function (btn_id) {
                if (btn_id == 1) {
                    return false;
                }
                if (btn_id == 0) {
                    SubmitFormCheck($("#idClicked").val());
                }
            });
        }
        else if (surgecharge != '') {
            surgechargeDetail = surgecharge + "<?= 'X<br>' . addslashes($langage_lbl['LBL_APPROX_PAY_AMOUNT']) . ": "; ?>" + $("#totalcost").html();
            show_alert(langlabelsurge, surgechargeDetail, "<?= addslashes($langage_lbl['LBL_CONTINUE_BTN']) ?>", "<?= addslashes($langage_lbl['LBL_CLOSE_TXT']) ?>", "", function (btn_id) {
                if (btn_id == 1) {
                    return false;
                }
                if (btn_id == 0) {
                    SubmitFormCheck($("#idClicked").val());
                }
            });
        }
        else {
            SubmitFormCheck($("#idClicked").val());
        }
    }

    function saveSingleDeliveryDetails() {

        <?php if($action == 'Edit'){ ?>
            if($('#delivery_arr').val().length >= 3){
                multideliveryData = $("#delivery_arr").val();
            }
        <?php } ?>
        
        if (eType == 'Deliver') {
            /*if ($('#1_Multi').val() === '' || $('#2_Multi').val() === '' || $('#3_Multi').val() === '' || $('#4_Multi').val() === '') {
                $(".stepper li").removeClass("active");
                $(".stepper li:nth-child(2)").addClass("active");
                $(".stepper li:nth-child(2) .step-content").show();
                return false;
            }*/
            cnt = $("#multideliveryfields").val();
            //console.log(cnt);
            var ajaxData = {
                'URL': '<?= $tconfig['tsite_url'] ?>booking/get_webservice_response.php',
                'AJAX_DATA': {
                    "type": "saveMultiDeliveryDetails",
                    "1": $("#1_Multi").val(),
                    "2": $("#2_Multi").val(),
                    "3": $("#3_Multi").val(),
                    "4": $("#4_Multi").val(),
                    "vReceiverAddress": $("#to").val(),
                    "vReceiverLatitude": $("#to_lat").val(),
                    "vReceiverLongitude": $("#to_long").val(),
                    "vPhoneCode": $("#vPhoneCodeDelivery").val(),
                    "AllData": multideliveryData
                }
            };
            getDataFromAjaxCall(ajaxData, function (response) {
                
                if (response.action == "1") {
                    var dataHtml = response.result;
                    if (dataHtml != "") {
                        multideliveryData = dataHtml;
                        $('#multideliveryJsonDetails').val(dataHtml);
                        $('#delivery_arr').val(dataHtml);
                        multideliveryDatanew = jQuery.parseJSON(multideliveryData);
                        for (var i = 0; i < multideliveryDatanew.length; i++) {
                            <?php if(!empty($vPhoneCode)){ ?>
                            var vPhoneCode = '+<?php echo $vPhoneCode;?>';
                            <?php } ?>
                            var myString = multideliveryDatanew[i]['3'];
                            var abc=myString.replace(vPhoneCode, '');
                         
                            $("#2_Multi").val(multideliveryDatanew[i]['2']);
                            $("#3_Multi").val(abc);
                            $("#4_Multi").val(multideliveryDatanew[i]['4']);
                        }
                    }
                }
                else {
                    console.log(response.result);
                }
            });
        }
    }

    function checkNavigatorPermissionStatus() {
        navigator.permissions && navigator.permissions.query({name: 'geolocation'}).then(function (PermissionStatus) {
            if (PermissionStatus.state == 'granted') {
                $('.detect-loc-booking').attr('title', '<?= addslashes($langage_lbl['LBL_FETCH_LOCATION_HINT']) ?>');
            }
            else if (PermissionStatus.state == 'prompt') {
                // prompt - not yet grated or denied
            }
            else {
                $('.detect-loc-booking').attr('title', '<?= str_replace("#SITE_NAME#", $SITE_NAME, addslashes($langage_lbl['LBL_LOCATION_BLOCKED_MSG'])) ?>');
            }
        });
    }

    function fetchLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition, showError, {
                maximumAge: 0,
                timeout: 1000,
                enableHighAccuracy: true
            });
        }
    }

    function showPosition(position) {
        var geo_latitude = position.coords.latitude;
        var geo_longitude = position.coords.longitude;
        var geo_lat_lng = "(" + geo_latitude + "," + geo_longitude + ")";
        var oldlat = "";
        var oldlong = "";
        var oldlatlong = "";
        var oldAddress = "";
        SetGeoCookie('GEO_LATITUDE', geo_latitude, 1);
        SetGeoCookie('GEO_LONGITUDE', geo_longitude, 1);
        SetGeoCookie('GEO_LATLNG', geo_lat_lng, 1);
        $("#from_lat").val(geo_latitude);
        $("#from_long").val(geo_longitude);
        $("#from_lat_long").val(geo_lat_lng);
        getReverseGeoCode('from', 'from_lat_long', "<?=$_SESSION['sess_lang'];?>", geo_latitude, geo_longitude, oldlat, oldlong, oldlatlong, oldAddress, function (latitude, longitude, address) {
            <?php if ($_SERVER['REQUEST_URI'] != '/user-fare-estimate' || stripos($_SERVER['REQUEST_URI'], 'cx-fareestimate.php') !== false) { ?>
            checkrestrictionfrom('from');
            <? } ?>
            $('#from').trigger('blur');
            setDriverListing();
            show_locations();
        });
    }

    function showError(error) {
        switch (error.code) {
            case error.PERMISSION_DENIED:
                $('.detect-loc-booking').attr('title', '<?= str_replace("#SITE_NAME#", $SITE_NAME, addslashes($langage_lbl['LBL_LOCATION_BLOCKED_MSG'])) ?>');
                break;
            case error.POSITION_UNAVAILABLE:
                $('.detect-loc-booking').attr('title', '<?= addslashes($langage_lbl['LBL_NO_LOCATION_FOUND_TXT']) ?>');
                break;
            case error.TIMEOUT:
                $('.detect-loc-booking').attr('title', '<?= str_replace("#SITE_NAME#", $SITE_NAME, addslashes($langage_lbl['LBL_LOCATION_BLOCKED_MSG'])) ?>');
                break;
            case error.UNKNOWN_ERROR:
                $('.detect-loc-booking').attr('title', '<?= str_replace("#SITE_NAME#", $SITE_NAME, addslashes($langage_lbl['LBL_LOCATION_BLOCKED_MSG'])) ?>');
                break;
        }
    }
</script>