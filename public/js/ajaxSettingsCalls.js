function getCategoriesAjax(transactionType, callback) {
    $.ajax({
        url: "/Settings/get" + transactionType + "CategoriesAjax",
        type: "POST",
        cache: false,
        dataType: 'json'
    }).done(function (categories) {
        callback(categories);
    }).fail(function (jqXHR, textStatus) {
        console.log("No i klops!" + jqXHR + textStatus);
        console.dir(arguments);
    });
}

function getAssignedTransactionsFromDB(url, id, callback) {
    $.ajax({
        url: url,
        type: "POST",
        dataType: 'json',
        cache: false,
        data: { categoryId: id }
    }).done(function (response) {
        console.log(response);
        callback(response);
    }).fail(function (response) {
        console.log("No i klops!" + response);
        console.dir(arguments);
    })
}

function deleteCategory(categoryData, callback) {
    let url = "/Settings/remove" + categoryData.transaction + "CategoryAjax";
    $.ajax({
        url: url,
        type: "POST",
        dataType: 'json',
        cache: false,
        data: { categoryId: categoryData.id }
    }).done(function (response) {
        console.log(response);
        deleteCategory(callback);
    }).fail(function (response) {
        console.log("No i klops!" + response);
        console.dir(arguments);
    })
}