function editExpenseCategory(id, callback) {
    let url = "/Expenses/editExpenseCategoryAjax";
    let category = $('#updateExpenseForm #category').val();
    $.ajax({
        url: url,
        type: "POST",
        dataType: "json",
        cache: false,
        data: { id: id, name: category }
    }).done(function (response) {
        if (response > 0) {
            console.log("Sukces!" + response);
            callback(response);
        } else {
            console.log("Nie edytowano rekordu" + response);
            console.dir(arguments);
        }
    }).fail(function (response) {
        console.log(response);
        console.dir(arguments);
    });
}

$(document).ready(function () {
    $("#expenseCatsList").on('click', ".deleteBtn", function () {
        let button = $(this);
        let categoryData = getDataAboutCategory('Expense', button);
        let url = "/Expenses/getExpensesByCategoryAjax";
        getAssignedTransactionsFromDB(url, categoryData.id, function (transactions) {
            if (transactions.length == 0) {
                displayDeleteConfirmModal(categoryData);
            } else {
                appendExpenseTransactionsToModal(categoryData.id, transactions);
                $('#alterTransactionsModal').modal('toggle');
                confirmExpenseDeleteCollapse(categoryData.transaction);
            }
        });
    });

    // Path for NOT ready for deletion categories with assigned transactions in DB
    // Display transactions and give option to either change the category or delete whole transaction entry

    function confirmExpenseDeleteCollapse(transactionType) {
        let rowToDelete;
        let transactionId;
        $('#alterTransactionsModal').on('click', ".editTransactionBtn", function () {
            let button = $(this);
            rowToDelete = button.parents().eq(1);
            transactionId = rowToDelete.data('transactionid');
            categoryId = rowToDelete.data('categoryid');
            rowToDelete.next().children().html($('#editExpenseCategoryBlock').html());
            let selectElement = rowToDelete.next().find('#category');
            getCategoriesAjax(transactionType, function (categories) {
                //remove currently set category from options and append rest for user's choice
                for (var i = 0; i < categories.length; i++) {
                    if (categories[i].id == categoryId) {
                        categories.splice(i, 1);
                    }
                }
                $.each(categories, function (i, category) {
                    var row = "<option data-catid='" + category.id + "'>" + category.name + "</option>";
                    $(selectElement).append(row);
                });
            });
            rowToDelete.next().show();
        });

        $('#alterTransactionsModal').on('click', '#submitEditExpense', function (e) {
            e.preventDefault();
            editExpenseCategory(transactionId, function (result) {
                if (result == true) {
                    rowToDelete.next().remove();
                    rowToDelete.prev().remove();
                    rowToDelete.remove();
                    if ($("#transactionsWrapper div").length == 0) {
                        $('#alterTransactionsModal').modal('hide');
                    }
                }
            });
        });

        $('#alterTransactionsModal').on('click', ".deleteBtn", function () {
            var button = $(this);
            rowToDelete = button.parents().eq(1);
            transactionId = rowToDelete.data('transactionid');
            rowToDelete.next().children().html($('#confrimTransactionDeletion').html());
            rowToDelete.next().show();
        });

        $('#alterTransactionsModal').on('click', ".confirmBtn", function () {
            //function in deleteTransaction.js file
            deleteTransactionEntry('Expense', transactionId, function (result) {
                if (result == true) {
                    rowToDelete.next().remove();
                    rowToDelete.prev().remove();
                    rowToDelete.remove();
                    if ($("#transactionsWrapper div").length == 0) {
                        $('#alterTransactionsModal').modal('hide');
                    }
                }
            });
        });
    }
    // Path for ready for deletion categories without assigned transactions in DB
    function displayDeleteConfirmModal(categoryData) {
        $("#confirmFormId").val(categoryData.id);
        let question = "Czy na pewno chcesz usunąć kategorię <strong>" + categoryData.category + "</strong>?"
        $('#question').html(question);
        $('#confirmModal').modal('toggle');
        $("#confirmModal").on('click', "#confirmBtn", function (e) {
            e.preventDefault();
            let url = "/Settings/removeExpenseCategoryAjax";
            deleteCategory(url, categoryData.id, function (callback) {
                if (callback == true) {
                    $('#confirmModal').modal('hide');
                    categoryData.rowToDelete.remove();
                }
            });
        });
    }
});
