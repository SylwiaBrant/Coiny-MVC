// Fetch from DB income categories assigned to user using AJAX
function getIncomeCategories() {
    $.ajax({
        url: "/Settings/getIncomeCategories",
        type: "GET",
        dataType: 'json'
    }).done(function (categories) {
        displayCategories(categories, '#incomeCatsList')
    }).fail(function (jqXHR, textStatus) {
        console.log("No i klops!" + jqXHR + textStatus);
        console.dir(arguments);
    });
}

// Fetch from DB expense categories assigned to user using AJAX
function getExpenseCategories() {
    $.ajax({
        url: "/Settings/getExpenseCategories",
        type: "GET",
        dataType: 'json'
    }).done(function (categories) {
        displayCategories(categories, '#expenseCatsList');
    }).fail(function (jqXHR, textStatus) {
        console.log("No i klops!" + jqXHR + textStatus);
        console.dir(arguments);
    });
}

// Fetch from DB payment methods assigned to user using AJAX
function getPaymentMethods() {
    $.ajax({
        url: "/Settings/getPaymentMethods",
        type: "GET",
        dataType: 'json',
    }).done(function (categories) {
        displayCategories(categories, '#paymentMetsList');
    }).fail(function (jqXHR, textStatus) {
        console.log("No i klops!" + jqXHR + textStatus);
        console.dir(arguments);
    });
}