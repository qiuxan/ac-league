var OZM = {
};

OZM.switchLang = function (language) {
    $.ajax({
        data: {lang: language, _token: $('[name="_token"]').val()},
        type: 'POST',
        url: '/switchLang',
        success: function (response) {
            response = JSON.parse(response);
            if(response.result == 1)
            {
                window.location.reload();
            }
        }
    });
}