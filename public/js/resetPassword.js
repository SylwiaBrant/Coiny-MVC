$(document).ready(function () {
    $('#resetPassword').on('submit', function (e) {
        e.preventDefault();
        let email = $('#resetPassword #email').val();
        $.ajax({
            url: "/password/request-reset",
            type: "POST",
            dataType: "json",
            cache: false,
            data: { email: email }
        }).done(function (response) {
            $('#resetPassword').trigger('reset');
            addSuccessFlash('#resetPassword', 'Wiadomość z linkiem aktywacyjnym została wysłana.');
        }).fail(function (jqXHR, textStatus) {
            addFailFlash('#resetPassword', 'Hmmm coś poszło nie tak...');
            /*console.log("No i klops!" + jqXHR + textStatus);
            console.dir(arguments);*/
        });
    });
});