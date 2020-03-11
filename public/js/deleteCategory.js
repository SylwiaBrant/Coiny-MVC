function getDataAboutCategory(transaction, button) {
    let categoryData = {};

    let rowToDelete = button.closest('tr');
    let id = rowToDelete.data('catid');
    let category = button.parents().eq(1).siblings('.category').text();

    categoryData.transaction = transaction;
    categoryData.rowToDelete = rowToDelete;
    categoryData.id = id;
    categoryData.category = category;

    return categoryData;
}

function appendIncomeTransactionsToModal(categoryId, transactions) {
    $.each(transactions, function (i, t) {
        var row = "<hr><div class='row align-items-center' data-transactionId='" + transactions[i].transactionId +
            "' data-categoryId='" + categoryId + "'>" +
            "<div class='col'>" +
            "<div class='row'>" + transactions[i].date + "</div>" +
            "<div class='row'>" + transactions[i].name + "</div>" +
            "<div class='row'>" + transactions[i].comment + "</div>" +
            "</div>" +
            "<div class='col-3'>" + transactions[i].money + "</div>" +
            "<div class='col-4 btn-group' aria-label='buttonsGroup'>" +
            "<button type='button' class='settingsBtn editTransactionBtn'><i class='icon-edit'></i></button>" +
            "<button type='button' class='settingsBtn deleteBtn'><i class='icon-trash'></i></button></div>" +
            "</div>" +
            "<div class='row collapse'>" +
            "<div id='alterConfirm' class='card card-body'></div>" +
            "</div>";
        $('#transactionsWrapper').append(row);
    });
}

function appendExpenseTransactionsToModal(categoryId, transactions) {
    $.each(transactions, function (i, t) {
        var row = "<hr><div class='row align-items-center' data-transactionId='" + transactions[i].transactionId +
            "' data-categoryId='" + categoryId + "'>" +
            "<div class='col'>" +
            "<div class='row'>" + transactions[i].date + "</div>" +
            "<div class='row'>" + transactions[i].category + "</div>" +
            "<div class='row'>" + transactions[i].comment + "</div>" +
            "</div>" +
            "<div class='col-3'>" +
            "<div class='row'>" + transactions[i].money + "</div>" +
            "<div class='row'>" + transactions[i].method + "</div>" +
            "</div>" +
            "<div class='col-4 btn-group' aria-label='buttonsGroup'>" +
            "<button type='button' class='settingsBtn editTransactionBtn'><i class='icon-edit'></i></button>" +
            "<button type='button' class='settingsBtn deleteBtn'><i class='icon-trash'></i></button></div>" +
            "</div>" +
            "<div class='row collapse'>" +
            "<div id='alterConfirm' class='card card-body'></div>" +
            "</div>";
        $('#transactionsWrapper').append(row);
    });
}

function displayDeleteConfirmModal(categoryData) {
    $("#confirmFormId").val(categoryData.id);
    let question = "Czy na pewno chcesz usunąć kategorię <strong>" + categoryData.category + "</strong>?"
    $('#question').html(question);
    $('#confirmModal').modal('toggle');
    $("#confirmModal").on('click', "#confirmBtn", function (e) {
        e.preventDefault();
        deleteCategory(categoryData, function () {
            if (callback == true) {
                $('#confirmModal').modal('hide');
                categoryData.rowToDelete.remove();
            }
        });
    });
}

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

function deleteCategory(url, id, callback) {
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

$(document).ready(function () {
    $('#confirmModal').on('hidden.bs.modal', function () {
        $(this).find('form').trigger('reset');
    });

    $('#alterTransactionsModal').on('hidden.bs.modal', function () {
        $('#transactionsWrapper').children().remove();
    });

    $('#alterTransactionsModal').on('click', ".cancelEditBtn", function () {
        $(this).parents().eq(3).hide();
        $('#alterConfirm').empty();
    });

    $('#alterTransactionsModal').on('click', ".cancelDeleteBtn", function () {
        $(this).parents().eq(2).hide();
        $('#alterConfirm').empty();
    });
});