$(function () {
    initTooltip();

    if($(".alert").css('display') != "none" && $(".alert").attr('id') != "org_note" && $(".alert").attr('id') != "org-info")
    {
        setTimeout(function() {
            $(".alert").hide();
        }, 10000);
    }

    $('.owl-carousel').owlCarousel({
        margin: 0,
        loop: false,
        items: 1,
        stagePadding: 50,
        URLhashListener: true,
        // startPosition: 'URLHash',
        onInitialized: function(event) {
            $('.owl-item').each(function(index) {
                $(this).attr('data-itemno', index);
                if($(this).find('[name="payment_mode"]').is(':checked') == true) {
                    if($(this).find('.payment-mode-block').attr('id') == "payment_mode_cash") {
                        window.location.hash = '#cash';    
                    }
                    else if($(this).find('.payment-mode-block').attr('id') == "payment_mode_card") {
                        window.location.hash = '#card';
                    }
                    else if($(this).find('.payment-mode-block').attr('id') == "payment_mode_wallet") {
                        window.location.hash = '#wallet';
                    }   
                }
            });
        },
        onChanged: owlCarouselCallback,
    });

    setPaymentModeBtnText();
    displayInAppWallet('show');

    if($('[name="payment_mode"]:checked').val() == "card") {
        $('.card-info').slideDown();
    }
});

$(window).on('load', function(){
    hideOverlay();
});


function owlCarouselCallback(event) {
    if($(event.currentTarget).attr('id') != "personal-tab-content") {
        var items     = event.item.count;     // Number of items
        var item      = event.item.index;     // Position of the current item
        var item_id = $('.owl-stage').find('[data-itemno="' + item + '"]').find('.payment-mode-block').attr('id');
        $('#'+item_id).trigger('click'); 
    }  
}

function selectMonth(elem)
{
    var month_val = $(elem).data('val');
    $('#cardMonth').val(month_val);
    $('#select_month .list-group-item').removeClass('active');
    $(elem).addClass('active');
    $('#select_month').modal('hide');
}

function selectYear(elem)
{
    var year_val = $(elem).data('val');
    $('#cardYear').val(year_val);
    $('#select_year .list-group-item').removeClass('active');
    $(elem).addClass('active');
    $('#select_year').modal('hide');
}

function scrollToInfo()
{
    $('html,body,.custom-scroll-div').animate({
        scrollTop: $("#min_amount_info").offset().top
    }, 500);

    $("#min_amount_info .card").css('border', '2px solid #ff0000');
}

function initTooltip()
{
    if ($(window).width() >= 768) {
        $('[data-toggle="tooltip"]').tooltip();    
    }
}

function pad(n, width, z) {
    z = z || '0';
    n = n + '';
    return n.length >= width ? n : new Array(width - n.length + 1).join(z) + n;
}

function alphabetsOnly(txt, e) {
    var arr = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz ";
    var code;
    if (window.event)
        code = e.keyCode;
    else
        code = e.which;
    var char = keychar = String.fromCharCode(code);
    if (arr.indexOf(char) == -1)
        return false;
}

$('.cvv-info').click(function() {
    var tooltip_title = $(this).attr('title');
    $(this).tooltip({
        'title': tooltip_title
    });

    $(this).tooltip('show');
});

$('.delete-card').click(function() {
    $('#iPaymentInfoId').val($(this).data('cardid'));
    $('#vCardToken').val($(this).data('cardtoken'));
    $('#card_no').text($(this).data('cardno'));
    $('#delete_card_modal').modal('show');
});

$('#delete_card_btn').click(function() {
    $('#delete_card_modal').modal('hide');
    showOverlay();
});

$('.set-default-card').click(function(e) {
    $('[name="default_iPaymentInfoId"]').val($(this).data('cardid'));                
    $('#set-default-card-form').submit();
    showOverlay();
});

$('#add-card-button').click(function() {
    showOverlay();
    var url = window.location.href;
    url = url.replace('PAYMENT_LIST', 'ADD_CARD');
    window.location.href = url;
});

$('#cardholder-name').on('keypress paste', function(event) {
    return alphabetsOnly(this, event);
});

$('#close-action').click(function() {
    showOverlay();
});

$('#personal-tab').click(function() {
    $('#personal-tab-content, #payment-mode-dots').show();
    $('#business-tab-content').hide();
    $('#business-tab').removeClass('active');
    $(this).removeClass('active').addClass('active');
    $(this).find('[name="profile"]').prop('checked', true);

    $('.payment-mode-block, .payment-mode-dots .dot').removeClass('active');
    $('[name="payment_mode"]').prop('checked', false);
    window.location.hash = '';
    $('.card-info').hide();
    $('#payment_mode_' + $('#selected_payment_mode').val()).trigger('click');
    setPaymentModeBtnText();

    displayInAppWallet('show');
    if($('#selected_org_title').length == 0) {
        $('.promocode-section').show();
    }
});

$('#business-tab').click(function() {
    if($(this).hasClass('active')) {
        return false;
    }

    $('#personal-tab-content, .card-info, #payment-mode-dots, #org_reason, #org_note').hide();

    $('.payment-mode-block, .payment-mode-dots .dot').removeClass('active');
    $('[name="payment_mode"]').prop('checked', false);

    $('#org_title').val("");
    $('#org_id').val("");

    $('#business-tab-content').show();
    $('#business_reason_other').hide();
    $('#business_reason_title, #business_reason_id, [name="business_reason_other"]').val("");
    $('#business_reason_title, [name="business_reason_other"]').removeClass("border-danger");
    $('#personal-tab, #select_business_reason .list-group-item, #select_org .list-group-item').removeClass('active');
    $(this).removeClass('active').addClass('active');
    $(this).find('[name="profile"]').prop('checked', true);
    setTimeout(function() {
        $('.owl-carousel').removeClass('owl-hidden');    
    }, 300);

    if($('#selected_org_title').length > 0 && $('#selected_org_title').val() != "") {
        $('#org_title').val($('#selected_org_title').val());
        $('#org_id').val($('#selected_org_id').val()); 
       
        $('#select_org').find('[data-id="' + $('#selected_org_id').val() + '"]').addClass('active');
        var selected_elem = $('#select_org').find('[data-id="' + $('#selected_org_id').val() + '"]');
        selectOrganization(selected_elem, 'Yes');

        $('#business_reason_title').val($('#selected_business_reason_title').val());
        $('#business_reason_id').val($('#selected_business_reason_id').val()); 
        $('#select_business_reason').find('[data-id="' + $('#selected_business_reason_id').val() + '"]').addClass('active');
        if($('#selected_business_reason_id').val() != "" && $('#selected_business_reason_id').val() == 0) {
            $('[name="business_reason_other"]').val($('#selected_business_reason_other').val());
            $('#business_reason_other').show();
        }
        $('#payment_mode_' + $('#selected_payment_mode').val()).trigger('click');
    } else {
        if($('#selected_org_title').length == 0) {
            $('.promocode-section').hide();
        }
    }
    setPaymentModeBtnText();
    displayInAppWallet();
});

$('#payment_mode_cash, #payment_mode_card, #payment_mode_wallet').on('click', function() {
    $('.payment-mode-block, .payment-mode-dots .dot').removeClass('active');
    $('[name="payment_mode"]').prop('checked', false);

    $(this).addClass('active');
    $(this).find('[name="payment_mode"]').prop('checked', true);

    if($(this).attr('id') == "payment_mode_cash") {
        window.location.hash = '#cash';
        $('#selected_payment_mode').val('cash');
    }
    else if($(this).attr('id') == "payment_mode_card") {
        window.location.hash = '#card';
        $('#selected_payment_mode').val('card');
    }
    else {
        window.location.hash = '#wallet';
        $('#selected_payment_mode').val('wallet');
    }
    if($('[name="payment_mode"]:checked').val() == "card") {
        if($('[name="profile"]:checked').val() == "business" && $('#pay_by_organization').val() == "Yes") {
            $('.card-info').slideUp('fast', function() {
                $(".container").animate({ scrollTop: $(document).height() }, "slow");
            });   
        }
        else {
            $('.card-info').slideDown('fast', function() {
                $(".container").animate({ scrollTop: $(document).height() }, "slow");
            }); 
        }
    }
    else {
        $('.card-info').slideUp('fast', function() {
            $(".container").animate({ scrollTop: $(document).height() }, "slow");
        });
    }

    setPaymentModeBtnText();
    displayInAppWallet('show');
});

function selectBusinessReason(elem)
{
    var reason_val = $(elem).data('val');
    var reason_id = $(elem).data('id');
    $('#business_reason_title').val(reason_val);
    $('#business_reason_id').val(reason_id);
    $('#select_business_reason .list-group-item').removeClass('active');
    $(elem).addClass('active');
    $('#select_business_reason').modal('hide');

    $('#selected_business_reason_title').val(reason_val);
    $('#selected_business_reason_id').val(reason_id);
    
    // $('#business_reason_other').hide();
    if(reason_val == "Other") {
        $('#business_reason_other').val("");
        $('#business_reason_other').slideDown();
    }
    else {
        $('#business_reason_other').slideUp();   
    }
    $('#business_reason_title').removeClass('border-danger');
}

function selectOrganization(elem, selected = "No") {
    // console.log(elem);
    var org_val = $(elem).data('val');
    var org_id = $(elem).data('id');
    var payment_by = $(elem).data('payment');
    $('#org_title').val(org_val);
    $('#org_id').val(org_id);
    $('#select_org .list-group-item').removeClass('active');
    $(elem).addClass('active');
    $('#select_org').modal('hide');
    $('#business_reason_title, [name="business_reason_other"]').val("");
    $('#business_reason_other').hide();

    $('#org_title').removeClass('border-danger');

    $('.payment-mode-block, .payment-mode-dots .dot').removeClass('active');
    $('[name="payment_mode"]').prop('checked', false);

    $('#selected_org_title').val(org_val);
    $('#selected_org_id').val(org_id);
    if(selected == "No") {
        $('#selected_business_reason_title').val("");
        $('#selected_business_reason_id').val("");
        $('#selected_business_reason_other').val("");
        $('#selected_payment_mode').val("");
    }

    if($('#selected_payment_mode').val() != "") {
        $('#payment_mode_' + $('#selected_payment_mode').val()).trigger('click');
    }

    var org_reasons_arr = $('#org_reasons_arr').text();
    var org_reasons_html = "";
    if(org_reasons_arr != "") {
        org_reasons_arr = JSON.parse(org_reasons_arr);
        var org_id_key = 'org_' + org_id;
        var org_type_reasons_arr = org_reasons_arr[org_id_key];
        // console.log(org_reasons_arr[org_id_key]);
        
        
        if(org_type_reasons_arr != "") {
            for (var i = 0; i < org_type_reasons_arr.length; i++) {
                org_reasons_html += '<li class="list-group-item" data-val="' + org_type_reasons_arr[i].vReasonTitle + '" data-id="' + org_type_reasons_arr[i].iTripReasonId + '" onclick="selectBusinessReason(this)"><span>' + org_type_reasons_arr[i].vReasonTitle + '</span></li>';
            }
        }        
    }
    org_reasons_html += '<li class="list-group-item" data-val="Other" data-id="0" onclick="selectBusinessReason(this)"><span>' + $('#other_val').val() + '</span></li>';

    $('#select_business_reason').find('ul').html("").append(org_reasons_html);

    $('#org_reason').css('display', 'block').show();

    if(payment_by == "organization") {
        $('#personal-tab-content').slideUp(function() {
            $('#org_note').slideDown();    
        });
        $('#payment-mode-dots, .card-info, #in-app-wallet').hide();
        $('#pay_by_organization').val("Yes");
        
    }
    else if ($('#org_id').length > 0) {
        $('#personal-tab-content').slideDown(function() {
            $('#org_note').slideUp();    
        });   
        $('#payment-mode-dots, #in-app-wallet').show();
        $('#pay_by_organization').val("No");
        $('#org_note').hide();
    }
}

function paymentModeForm() {
    if($('[name="profile"]:checked').val() == "business") {
        if($('#org_title').val() == "") {

            showSnackbar($('#org_title').data('errormsg'));

            $('#org_title').addClass('border-danger');
            return false;
        }

        if($('#business_reason_title').val() == "") {

            showSnackbar($('#business_reason_title').data('errormsg'));

            $('#business_reason_title').addClass('border-danger');
            return false;
        }

        if($('#business_reason_title').val() == "Other" && $('[name="business_reason_other"]').val().trim() == "") {

            showSnackbar($('[name="business_reason_other"]').data('errormsg'));

            $('[name="business_reason_other"]').val("");
            $('[name="business_reason_other"]').addClass('border-danger');
            return false;
        }

        if($('[name="payment_mode"]:checked').length == 0) {
            if($('#pay_by_organization').val() == "No") {
                showSnackbar($('#payment_mode_error').val());
                return false;
            }
        }
    }
    else {
        if($('[name="payment_mode"]:checked').length == 0) {
            showSnackbar($('#payment_mode_error').val());
            return false;
        }
    }

    showOverlay();
    $('#payment_mode_form').submit();
}

function showOverlay(hideLoader = "No", hideContent = "No") {
    $('body').css('overflow', 'hidden');
    $('.overlay__inner').show();    
    if(hideLoader == "Yes") {
        $('.overlay__content').hide();
        if(hideContent == "No") {
            $('.overlay').fadeIn();    
        }
        else {
            $('.overlay').show();    
        }
    }
    else {
        $('.overlay').show();
        $('.overlay__content').show();
    }
}

function hideOverlay() {
    $('.overlay').hide();
    $('.overlay__content').show();
    $('body').css('overflow', 'auto');
    $('.overlay').removeClass('bg-overlay');
}

function showSnackbar(alert_text) {
    $('.overlay').addClass('bg-overlay');
    Snackbar.show({
        text: alert_text,
        duration: 100000000,
        actionText: "&times;",
        customClass: 'snackbar-text-custom',
        // width: '-webkit-fill-available',
        onActionClick: function(element) {
            $(element).css('opacity', 0);
            hideOverlay();
        },
        actionTextColor: '#FF0000'
    });

    showOverlay('Yes');
}

$('[name="business_reason_other"]').on('keypress', function() {
    if($(this).val().trim() != "") {
        $('[name="business_reason_other"]').removeClass('border-danger');
    }
    else {
        $('[name="business_reason_other"]').addClass('border-danger');
    }
});

$('#eWalletDebit').change(function() {
    if($('[name="payment_mode_available"]').val() == "No") {
        if($(this).is(":checked")) {
            $('#payment_mode_btn').prop('disabled', false);
            $('<input>').attr({
                type: 'hidden',
                name: 'payment_mode',
                id: 'payment_mode',
                value: 'wallet'
            }).appendTo('form[id="payment_mode_form"]');
        }
        else {
            $('#payment_mode_btn').prop('disabled', true);
            $('#payment_mode').remove();
        }
    }
});

$(window).on('load', function() {
   // showOverlay('Yes', 'Yes'); 
});

function displayInAppWallet(display) {
    if($('#in-app-wallet').length > 0) {
        if((org_available == "Yes" && $('#pay_by_organization').val() == "No") || display == "show") {
            $('#in-app-wallet').show();
        }
        else {
            $('#in-app-wallet').hide();
        }
    }
}

function redirectToUrl(url) {
    showOverlay(); 
    window.location.href = url;
}

function backToPaymentList() {
    showOverlay();
    // var url = window.location.href+'&cancel=Yes';
    var url = window.location.href;
    url = url.replace('ADD_CARD', 'PAYMENT_LIST');
    url = url.replace(/&iPaymentInfoId=\d+/, '');
    window.location.href = url;
}

function changePaymentCard(url) {
    showOverlay();
    var form_parameters = $('#payment_mode_form').serialize();
    form_parameters = form_parameters.replace('form_submit=Yes&', '');
    // console.log(form_parameters);
    window.location.href = url + encodeURIComponent('&' + form_parameters);
}