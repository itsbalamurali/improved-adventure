<?php

// General Functions

function paginate($reload, $page, $tpages)
{
    $adjacents = 2;

    $prevlabel = '&lsaquo; Prev';

    $nextlabel = 'Next &rsaquo;';

    $firstlabel = '&lsaquo;&lsaquo; First';

    $Lastlabel = 'Last &rsaquo;&rsaquo;';

    $out = '';

    // previous

    if (1 === $page) {
        $out .= "<span class='disabled-page001'>".$prevlabel."</span>\n";
    } elseif (2 === $page) {
        $out .= '<li><a  href="'.$reload.'">'.$prevlabel."</a>\n</li>";
    } else {
        $out .= '<li><a  href="'.$reload.'&amp;page='.($page - 1).'">'.$prevlabel."</a>\n</li>";
    }

    if ($page > 3) {
        $out .= "<a style='font-size:11px' href='".$reload."'&amp;page='1'>".$firstlabel."</a>\n";
    }

    $pmin = ($page > $adjacents) ? ($page - $adjacents) : 1;

    $pmax = ($page < ($tpages - $adjacents)) ? ($page + $adjacents) : $tpages;

    for ($i = $pmin; $i <= $pmax; ++$i) {
        if ($i === $page) {
            $out .= "<li  class=\"active\"><a href=''>".$i."</a></li>\n";
        } elseif (1 === $i) {
            $out .= '<li><a  href="'.$reload.'">'.$i."</a>\n</li>";
        } else {
            $out .= '<li><a  href="'.$reload.'&amp;page='.$i.'">'.$i."</a>\n</li>";
        }
    }

    if ($page < ($tpages - $adjacents)) {
        $out .= "<a style='font-size:11px' href=\"".$reload.'&amp;page='.$tpages.'">'.$Lastlabel."</a>\n";
    }

    // next

    if ($page < $tpages) {
        $out .= '<li><a  href="'.$reload.'&amp;page='.($page + 1).'">'.$nextlabel."</a>\n</li>";
    } else {
        $out .= "<span class='disabled-page002'>".$nextlabel."</span>\n";
    }

    $out .= '';

    return $out;
}

?>

<script>

    function checkAlls() {

        jQuery("#_list_form input[type=checkbox]").each(function () {

            if ($(this).attr('disabled') != 'disabled') {

                this.checked = 'true';

            }

        });

    }



    function uncheckAlls() {

        jQuery("#_list_form input[type=checkbox]").each(function () {

            this.checked = '';

        });

    }



	function exportMapApi(action){

		var checked = $("#_list_form input:checked").length;



	}



    function ChangeStatusAll(statusNew) {

        if (statusNew != "") {

            var status = statusNew;

            var checked = $("#_list_form input:checked").length;

            if (checked > 0) {

                if (checked == 1) {

                    if ($("#_list_form input:checked").attr("id") == 'setAllCheck') {

                        $('#is_not_check_modal').modal('show');

                        $("#changeStatus").val('');

                        return false;

                    }

                }

                $("#statusVal").val(status);

                if (status == 'Active') {

                    $('#is_actall_modal').modal('show');

                } else if (status == 'Inactive') {

                    $('#is_inactall_modal').modal('show');

                } else {

                    $('#is_dltall_modal').modal('show');

                }

                $(".action_modal_submit").unbind().click(function () {

                    var action = $("#pageForm").attr('action');

                    var formValus = $("#_list_form, #pageForm").serialize();

                    window.location.href = action + "?" + formValus;

                });

                $("#changeStatus").val('');

            } else {

                $('#is_not_check_modal').modal('show');

                $("#changeStatus").val('');

                return false;

            }

        } else {

            return false;

        }

    }





    function changeStatus(iAdminId, status) {

//		$('html').addClass('loading');

        var action = $("#pageForm").attr('action');

        var page = $("#page").val();

        if (status == 'Active') {

            status = 'Inactive';

        } else {

            status = 'Active';

        }

        $("#page").val(page);

        $("#iMainId01").val(iAdminId);

        $("#status01").val(status);

        var formValus = $("#pageForm").serialize();

        window.location.href = action + "?" + formValus;

    }

    function changeMenuStatus(id,iAdminId, status) {

//		$('html').addClass('loading');

        var action = $("#pageForm").attr('action');

        var page = $("#page").val();

        if (status == 'Active') {

            status = 'Inactive';

        } else {

            status = 'Active';

        }

        $("#page").val(page);

        $("#iMainId01").val(iAdminId);

        $("#status01").val(status);
        $("#menuid").val(id);



        var formValus = $("#pageForm").serialize();
	// var formListData = $("#_list_form"+iAdminId).serialize();
        window.location.href = action + "?" + formValus;

    }

    function changeSubCatStatus(iAdminId, status,iServiceIdEdit) {
//      $('html').addClass('loading');
        var action = $("#pageForm").attr('action');
        var page = $("#page").val();
        if (status == 'Active') {
            status = 'Inactive';
        } else {
            status = 'Active';
        }
        $("#page").val(page);
        $("#iMainId01").val(iAdminId);
        $("#status01").val(status);
        $("#iServiceIdEdit").val(iServiceIdEdit);
        var formValus = $("#pageForm").serialize();
        window.location.href = action + "?" + formValus;
    }

    function ValidateMeConfig(AllActiveServices,status,countActiveServices,selectedID){

        // console.log(AllActiveServices);

        if(((countActiveServices - 1) < 1) && (status == 'Active')){

            alert("Keep atleast one service active.");

            return false;

        }

        if(((countActiveServices - 1) != 0) && (status == 'Active')){

            var newAry = new Array();

            $.each(AllActiveServices, function (index, value) {

                    for (var k=0; k < value.length; k++) {

                        newAry.push(value[k]);

                    }

            });

            var a = newAry;

            var b = ['Geocoding','AutoComplete','Direction'];

            var unique = $.grep(b, function(element) {

                return $.inArray(element, a) == -1;

            });

            if(unique.length > 0){

                alert("You are missing a required service.");

                window.location.reload();

                return false;

            }

        }

        return true;

    }

    function ValidateMe(status,countActiveServices,selectedID){

        if(((countActiveServices - 1) < 1) && (status == 'Active')){

            alert("Keep atleast one service active.");

            return false;

        }

        if(((countActiveServices - 1) != 0) && (status == 'Active')){

            var newAry = new Array();

            delete AllActiveServices[selectedID];

            $.each(AllActiveServices, function (index, value) {

                unde_val = 1;

                if(typeof value === 'undefined') {

                    unde_val = 0;

                }

                if(unde_val==1){

                    for (var k=0; k < value.length; k++) {

                        newAry.push(value[k]);

                    }

                }

            });

            var a = newAry;

            var b = ['Geocoding','AutoComplete','Direction'];

            var unique = $.grep(b, function(element) {

                return $.inArray(element, a) == -1;

            });

            if(unique.length > 0){

                alert("You are missing a required service.");

                window.location.reload();

                return false;

            }

        }

    }

function changeStatusForMapAPI(iAdminId, status,countActiveServices,selectedID,demoOrnot) {

    var demoOrnot = demoOrnot;
    if(demoOrnot == 'Demo'){
        window.location.href = "map_api_setting.php?success=2";
        return false;
    }else{
   var result = ValidateMe(status,countActiveServices,selectedID);

   if(result != false){

            var action = $("#pageForm").attr('action');

            var page = $("#page").val();

            if (status == 'Active') {

                status = 'Inactive';

            } else if(status == 'Inactive') {

                status = 'Active';

            } else {
                status = 'Delete';
            }

            $("#page").val(page);

            $("#iMainId01").val(iAdminId);

            $("#status01").val(status);

            var formValus = $("#pageForm").serialize();

            window.location.href = action + "?" + formValus;

        }
    }

    }

    //make

    /* function changeStatus(iMakeId,status) {

     //		$('html').addClass('loading');

     var action = $("#pageForm").attr('action');

     var page = $("#page").val();

     if(status == 'Active') {

     status = 'Inactive';

     }else {

     status = 'Active';

     }

     $("#page").val(page);

     $("#iMainId01").val(iAdminId);

     $("#status01").val(status);

     var formValus = $("#pageForm").serialize();

     window.location.href = action+"?"+formValus;

     } */

    //make

    function changeStatusDelete(iAdminId) {

        $('#is_dltSngl_modal').modal('show');

        $(".action_modal_submit").unbind().click(function () {

//			$('html').addClass('loading');



            var action = $("#pageForm").attr('action');



            var page = $("#pageId").val();

            $("#pageId01").val(page);

            $("#iMainId01").val(iAdminId);

            $("#method").val('delete');

            var formValus = $("#pageForm").serialize();

            window.location.href = action + "?" + formValus;



        });

    }

    function changeStatusDeletevehicle(iAdminId, driverid) {

        $('#is_dltSngl_modal').modal('show');

        $(".action_modal_submit").unbind().click(function () {

//			$('html').addClass('loading');

            var action = $("#pageForm").attr('action');

            var page = $("#pageId").val();

            $("#pageId01").val(page);

            $("#iMainId01").val(iAdminId);

            $("#iDriverId").val(driverid);

            $("#method").val('delete');

            var formValus = $("#pageForm").serialize();

            window.location.href = action + "?" + formValus;



        });

    }



    function changeStatusDeletecd(iAdminId) {

        $('#is_dltSngl_modal_cd').modal('show');

        $(".action_modal_submit").unbind().click(function () {

//			$('html').addClass('loading');

            var action = $("#pageForm").attr('action');

            var page = $("#pageId").val();

            $("#pageId01").val(page);

            $("#iMainId01").val(iAdminId);

            $("#method").val('delete');

            var formValus = $("#pageForm").serialize();

            window.location.href = action + "?" + formValus;



        });

    }



    function changeStatusDeletestore(iAdminId) {

        $('#is_dltSngl_modal_store').modal('show');

        $(".action_modal_submit").unbind().click(function () {

//			$('html').addClass('loading');

            var action = $("#pageForm").attr('action');

            var page = $("#pageId").val();

            $("#pageId01").val(page);

            $("#iMainId01").val(iAdminId);

            $("#method").val('delete');

            var formValus = $("#pageForm").serialize();

            window.location.href = action + "?" + formValus;



        });

    }





    function resetTripStatus(iAdminId) {

        $('#is_resetTrip_modal').modal('show');

        $(".action_modal_submit").unbind().click(function () {

//			$('html').addClass('loading');

            var action = $("#pageForm").attr('action');

            var page = $("#pageId").val();

            $("#pageId01").val(page);

            $("#iMainId01").val(iAdminId);

            $("#method").val('reset');

            var formValus = $("#pageForm").serialize();

            window.location.href = action + "?" + formValus;

        });

    }



    function showExportTypes(section) {

        if (section == 'store_review') {

            $("#show_export_types_modal_excel").modal('show');

            $("#export_modal_submit_excel").on('click', function () {

                var action = "main_export.php";

                var formValus = $("#_export_form, #pageForm, #show_export_modal_form_excel").serialize();



                window.location.href = action + '?section=' + section + '&' + formValus;

                $("#show_export_types_modal_excel").modal('hide');

                return false;

            });

        }else if (section == 'map_api') {

            $("#show_export_types_modal_json").modal('show');

            $("#export_modal_submit_json").on('click', function () {

                var action = "main_export.php";

                var formValus = $("#_export_form, #pageForm, #show_export_modal_form_json").serialize();



                window.location.href = action + '?section=' + section + '&' + formValus;

                $("#show_export_types_modal_json").modal('hide');

                return false;

            });

        } else {

			// var checked = $("#_list_form input:checked").val();

			var checkedValues = $("input[name='checkbox[]']:checked")

              .map(function(){return $(this).val();}).get();



            $("#show_export_types_modal").modal('show');

            $("#export_modal_submit").on('click', function () {

                var action = "main_export.php";

                var formValus = $("#_export_form, #pageForm, #show_export_modal_form").serialize();

                window.location.href = action + '?section=' + section + '&' + formValus+'&checkedvalues='+checkedValues;

                $("#show_export_types_modal").modal('hide');

                return false;

            });

        }

    }

	    function showImportTypes(section) {

       $("#import_modal").modal('show');

    }

    function Redirect(sortby, order) {

        //$('html').addClass('loading');

        $("#sortby").val(sortby);

        if (order == 0) {

            order = 1;

        } else {

            order = 0;

        }

        $("#order").val(order);

        $("#page").val('1');

        var action = $("#_list_form").attr('action');

        var formValus = $("#pageForm").serialize();

        //alert(formValus);

        window.location.href = action + "?" + formValus;

    }



    function reset_form(formId) {

        $("#" + formId).find("input[type=text],input[type=password],input[type=file], textarea, select").val("");

    }



    //function openHoverAction(openId) {

    $('.openHoverAction-class').click(function (e) {

        // openHoverAction-class

        //e.preventDefault();

        alert('hiii');

        // hide all span

        var $this = $(this).find('.show-moreOptions');

        $(".openHoverAction-class .show-moreOptions").not($this).hide();



        // here is what I want to do

        $this.toggle();



//            if($(".openPops_"+openId).hasClass('active')) {

//                $('.show-moreOptions').removeClass('active');

//            }else {

//

//            }

//            $(".openPops_"+openId).addClass('active');

    });

    function reportExportTypes(section) {

        var action = "report_export.php";

        var formValus = $("#pageForm").serialize();

        //alert(formValus);

        window.location.href = action + '?section=' + section + '&' + formValus;

        return false;

    }


    function Paytouser() {

        var checked = $("#_list_form input:checked").length;

        if (checked > 0) {

            if (checked == 1) {

                if ($("#_list_form input:checked").attr("id") == 'setAllCheck') {

                    $('#is_not_check_modal').modal('show');

                    $("#changeStatus").val('');

                    return false;

                }

            }

            $('#is_payTo_user_modal').modal('show');

            $(".action_modal_submit").unbind().click(function () {

                $("#ePayDriver").val('Yes');

                var action = $("#pageForm").attr('action');

                var formValus = $("#_list_form, #pageForm").serialize();

                window.location.href = action + "?" + formValus;

            });

        } else {

            $('#is_not_check_modal').modal('show');

            return false;

        }

    }


    function Paytodriver() {

        var checked = $("#_list_form input:checked").length;

        if (checked > 0) {

            if (checked == 1) {

                if ($("#_list_form input:checked").attr("id") == 'setAllCheck') {

                    $('#is_not_check_modal').modal('show');

                    $("#changeStatus").val('');

                    return false;

                }

            }

            $('#is_payTo_modal').modal('show');

            $(".action_modal_submit").unbind().click(function () {

                $("#ePayDriver").val('Yes');

                var action = $("#pageForm").attr('action');

                var formValus = $("#_list_form, #pageForm").serialize();

                window.location.href = action + "?" + formValus;

            });

        } else {

            $('#is_not_check_modal').modal('show');

            return false;

        }

    }

    function PaytouserRent() {

        var checked = $("#_list_form input:checked").length;

        if (checked > 0) {

            if (checked == 1) {

                if ($("#_list_form input:checked").attr("id") == 'setAllCheck') {

                    $('#is_not_check_modal').modal('show');

                    $("#changeStatus").val('');

                    return false;

                }

            }

            $('#is_payTo_user_modal').modal('show');

            $(".action_modal_submit").unbind().click(function () {

                $("#ePayUser").val('Yes');

                var action = $("#pageForm").attr('action');

                var formValus = $("#_list_form, #pageForm").serialize();

                window.location.href = action + "?" + formValus;

            });

        } else {

            $('#is_not_check_modal').modal('show');

            return false;

        }

    }


    function PaytodriverforCancel() {

        var checked = $("#_list_form input:checked").length;

        if (checked > 0) {

            if (checked == 1) {

                if ($("#_list_form input:checked").attr("id") == 'setAllCheck') {

                    $('#is_not_check_modal').modal('show');

                    $("#changeStatus").val('');

                    return false;

                }

            }

            $('#is_payTo_modal').modal('show');

            $(".action_modal_submit").unbind().click(function () {

                $("#ePayDriver").val('Yes');

                var action = $("#pageForm").attr('action');

                var formValus = $("#_list_form, #pageForm").serialize();

                window.location.href = action + "?" + formValus;

            });

        } else {

            $('#is_not_check_modal').modal('show');

            return false;

        }

    }

    function PaytoorganizationforCancel() {

        var checked = $("#_list_form input:checked").length;

        if (checked > 0) {

            if (checked == 1) {

                if ($("#_list_form input:checked").attr("id") == 'setAllCheck') {

                    $('#is_not_check_modal').modal('show');

                    $("#changeStatus").val('');

                    return false;

                }

            }

            $('#is_payTo_organization_modal').modal('show');

            $(".action_modal_submit").unbind().click(function () {

                $("#ePayDriver").val('Yes');

                var action = $("#pageForm").attr('action');

                var formValus = $("#_list_form, #pageForm").serialize();

                window.location.href = action + "?" + formValus;

            });

        } else {

            $('#is_not_check_modal').modal('show');

            return false;

        }

    }



    function changeOrder(iAdminId) {

        $('#is_dltSngl_modal').modal('show');

        $(".action_modal_submit").unbind().click(function () {

            var action = $("#pageForm").attr('action');

            var page = $("#pageId").val();

            $("#pageId01").val(page);

            $("#iMainId01").val(iAdminId);

            $("#method").val('delete');

            var formValus = $("#pageForm").serialize();

            window.location.href = action + "?" + formValus;

        });

    }



    function PaytoRestaurant() {



        var checked = $("#_list_form input:checked").length;



        if (checked > 0) {

            if (checked == 1) {



                if ($("#_list_form input:checked").attr("id") == 'setAllCheck') {

                    $('#is_not_check_modal').modal('show');

                    $("#changeStatus").val('');

                    return false;

                }

            }

            $('#is_payTo_Res_modal').modal('show');

            $(".action_modal_submit").unbind().click(function () {

                $("#ePayRestaurant").val('Yes');

                var action = $("#pageForm").attr('action');

                var formValus = $("#_list_form, #pageForm").serialize();

                window.location.href = action + "?" + formValus;

            });

        } else {

            $('#is_not_check_modal').modal('show');

            return false;

        }

    }


</script>

