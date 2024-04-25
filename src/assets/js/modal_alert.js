//WARNING: WHEN CHANGE IN THIS FUNCTION PLEASE KEEP PROPER NOTE ON IT >> BECAUSE IT IS GENERAL FUNCTION
//function show_alert(title = "", content = "", positive_btn = "", negative_btn = "", natural_btn = "", callback, isCloseAlertOnClick = true) { // Do not REMOVE Orignal function
//show_modal_popup created by whom don't know,,
//isCloseAlertOnClick added by sunita and dont know what is usage of it..
//isCloseIcon is used for close icon on header shown or not.
//isAdminModel is used for admin model, in admin modeal header is black, in front theme wise color.
function show_alert(title, content, positive_btn, negative_btn, natural_btn, callback, isCloseAlertOnClick = true, isCloseIcon = true, isAdminModel = false) {
    title = title || '';
    content = content || '';
    positive_btn = positive_btn || '';
    negative_btn = negative_btn || '';
    natural_btn = natural_btn || '';
    isCloseAlertOnClick = isCloseAlertOnClick;
    adminclass = '';
    if (isAdminModel == true) {
        adminclass = 'adminmodelheader';
    }
    var str = "<div class='custom-modal-first-div active' id='custom-alert'>" +
        "<div class='custom-modal-sec-div' role='document'>";
    if (title != "") {
        str += "<div class='custom-model-header " + adminclass + "'><h4 class='custom-modal-title' id='inactiveModalLabel'>" + title + "</h4>";
        if (isCloseIcon == true) {
            str += "<i style='color:#fff;' class='icon-close' data-dismiss='modal' style='font-size:20px;'></i>";
        }
        str += "</div>";
    }
    str += "<div class='custom-model-body'>" + content + "</div>" +
        "<input type='hidden' name='iDriverId_temp' id='iDriverId_temp'>" +
        "<div class='custom-model-footer'>";
    str += "<div class='button-block'>";
    if (natural_btn != "") {
        str = str + "<button type='button' class='btn custom-modal-genbtn' onclick='handle_click(2, " + callback + ", " + isCloseAlertOnClick + ")'>" + natural_btn + "</button>";
    }
    if (positive_btn != "") {
        str = str + "<button type='button' class='gen-btn' onclick='handle_click(0, " + callback + ", " + isCloseAlertOnClick + ")'>" + positive_btn + "</button>";
    }
    if (negative_btn != "") {
        str = str + "<button type='button' class='gen-btn' onclick='handle_click(1, " + callback + ", " + isCloseAlertOnClick + ")'>" + negative_btn + "</button>";
    }
    str = str + "</div></div>" +
        "</div>" +
        "</div>";
    if ($("#custom-alert").length > 0) {
        $('body').find('#custom-alert').remove();
        $('body').append(str);
    } else {
        $('body').append(str);
    }
}

function show_modal_popup(title, content, positive_btn, negative_btn, natural_btn, callback, isCloseAlertOnClick = true) {
    title = title || '';
    content = content || '';
    positive_btn = positive_btn || '';
    negative_btn = negative_btn || '';
    natural_btn = natural_btn || '';
    isCloseAlertOnClick = isCloseAlertOnClick;
    var str = "<div class='custom-modal-first-div active custom-admin-info-popup' id='custom-modal-popup'>" +
        "<div class='custom-modal-sec-div' role='document'>";
    if (title != "") {
        str += "<div class='custom-model-header'>" + title;
        // str += "<i style='color:#fff;' class='icon-close' data-dismiss='modal' style='font-size:20px;'></i>";
        str += "</div>";
    }
    str += "<div class='custom-model-body'>" + content + "</div>" + "<div class='custom-model-footer'>";
    if (natural_btn != "") {
        str = str + "<button type='button' class='btn custom-modal-genbtn' onclick='handle_click(2, " + callback + ", " + isCloseAlertOnClick + ")'>" + natural_btn + "</button>";
    }
    str += "<div class='button-block'>";
    if (positive_btn != "") {
        str = str + "<button type='button' class='gen-btn' onclick='handle_click(0, " + callback + ", " + isCloseAlertOnClick + ")'>" + positive_btn + "</button>";
    }
    if (negative_btn != "") {
        str = str + "<button type='button' class='gen-btn' onclick='handle_click(1, " + callback + ", " + isCloseAlertOnClick + ")'>" + negative_btn + "</button>";
    }
    str += "</div></div></div></div>";
    if ($("#custom-modal-popup").length > 0) {
        $('body').find('#custom-modal-popup').remove();
        $('body').append(str);
    } else {
        $('body').append(str);
    }
}

function handle_click(btn_id, callback, isCloseAlertOnClick) {
    if (isCloseAlertOnClick == true) {
        $('.custom-modal-first-div').removeClass('active');
    }
    if (typeof callback !== 'undefined') {
        callback(btn_id);
    }
}

$(document).on('click', '[data-dismiss="modal"]', function (e) {
    e.preventDefault();
    $(this).closest('.custom-modal-first-div').removeClass('active');
});
$('body').keydown(function (e) {
    if (e.which == 27) {
        $('[data-dismiss="modal"]').trigger('click')
    }
});
$(document).on('click', '[data-toggle="modal"]', function (e) {
    e.preventDefault();
    var data_target = $(this).attr('data-target');
    $('.custom-modal-first-div').removeClass('active');
    $(document).find(data_target).addClass('active');
});