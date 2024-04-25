// function getDataFromApi(data, responseHandler, requestDataType = "json", requestType = "POST")
function getDataFromApi(data, responseHandler, requestDataType, requestType) {
    if (requestType != '') {
        requestType = "POST";
    }
    var urlParams = new URLSearchParams(data);
    var async_request_param = urlParams.get('async_request');
    var async_request = true;
    if (async_request_param) {
        async_request = async_request_param;
    }
    $.ajax({
        type: requestType,
        url: WEBSERVICE_API_FILE_NAME,
        data: data,
        // dataType: requestDataType,
        async: async_request,
        success: function (response) {
            return responseHandler(response);
        },
        error: function (xhr, status, error) {
            return "";
        }
    });
}

function UploadDataToServer(data, responseHandler) {
    $.ajax({
        type: 'POST',
        url: WEBSERVICE_API_FILE_NAME,
        data: data,
        dataType: 'json',
        contentType: false,
        processData: false,
        success: function (response) {
            return responseHandler(response);
        },
        error: function (xhr, status, error) {
            return "";
        }
    });
}


function getDataFromAjaxCall(data, responseHandler) {
    var ajaxOptions = {};
    ajaxOptions.type = 'POST';
    if (data.hasOwnProperty('REQUEST_TYPE')) {
        ajaxOptions.type = data.REQUEST_TYPE;
    }
    ajaxOptions.url = data.URL;
    ajaxOptions.data = data.AJAX_DATA;
    if (data.hasOwnProperty('REQUEST_DATA_TYPE')) {
        ajaxOptions.dataType = data.REQUEST_DATA_TYPE;
    }
    if (data.hasOwnProperty('REQUEST_ASYNC')) {
        ajaxOptions.async = data.REQUEST_ASYNC;
    }
    if (data.hasOwnProperty('REQUEST_CACHE')) {
        ajaxOptions.cache = data.REQUEST_ASYNC;
    }
    if (data.hasOwnProperty('REQUEST_CONTENT_TYPE')) {
        ajaxOptions.contentType = data.REQUEST_CONTENT_TYPE;
    }
    if (data.hasOwnProperty('REQUEST_PROCESS_DATA')) {
        ajaxOptions.processData = data.REQUEST_PROCESS_DATA;
    }

    ajaxOptions.success = function (response) {
        var result = {
            'action': "1",
            'result': response
        };
        return responseHandler(result);
    };

    ajaxOptions.error = function (xhr, status, error) {
        var result = {
            'action': "0",
            'result': error
        };
        return responseHandler(result);
    };

    var ajaxRequest = $.ajax(ajaxOptions);
    return ajaxRequest;
}