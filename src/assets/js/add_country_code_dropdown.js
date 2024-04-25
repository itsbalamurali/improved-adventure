function getPhoneCodeInTextBox(placeId, DropDownName, callback = '') {

    var ajaxData = {
        'URL': 'add-countycode-dropdown.php',
        'AJAX_DATA': {DropDownName: DropDownName, placeId: placeId},
    };
    getDataFromAjaxCall(ajaxData, function (response) {
        if (response.action == "1") {
            var data = response.result;
            $('#' + placeId).before(data);
            if (typeof callback === 'function') {
                callback();
            } else {
                console.error('Callback is not a function!');
            }
        } else {

        }
    });

}

function isEmail(email) {
    var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    return regex.test(email);
}

function validatePhone(vPhoneNumber) {

    var numbers = /^[0-9]+$/;

    var val = vPhoneNumber;

    if (val.match(numbers)) {

        return true;

    } else {

        return false;

    }

}