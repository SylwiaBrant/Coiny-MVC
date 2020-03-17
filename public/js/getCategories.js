function getCategoriesAjax(transactionType, callback) {
    $.ajax({
        url: "/Settings/get" + transactionType + "CategoriesAjax",
        type: "POST",
        cache: false,
        dataType: "json"
    }).done(function (categories) {
        callback(categories);
    }).fail(function (jqXHR, textStatus) {
        /*console.log("No i klops!" + jqXHR + textStatus);
        console.dir(arguments);*/
    });
}