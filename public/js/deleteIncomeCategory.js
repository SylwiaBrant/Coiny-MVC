$(document).ready(function () {
    $("#incomeCatsList").on('click', ".deleteBtn", function () {
        let button = $(this);
        let categoryData = getDataAboutCategory('Income', button);
        let url = "/Incomes/getIncomesByCategoryAjax";
        getAssignedTransactionsFromDB(url, categoryData.id, function (transactions) {
            if (transactions.length == 0) {
                console.log(transactions);
                displayDeleteConfirmModal(categoryData);
            } else {
                appendIncomeTransactionsToModal(categoryData.id, transactions);
                $('#alterTransactionsModal').modal('toggle');
                confirmIncomeDeleteCollapse(categoryData.transaction);
            }
        });
    });

    // Path for NOT ready for deletion categories with assigned transactions in DB
    // Display transactions and give option to either change the category or delete whole transaction entry

    function confirmIncomeDeleteCollapse(transactionType) {
        let rowToDelete;
        let transactionId;
        $('#alterTransactionsModal').on('click', ".editTransactionBtn", function () {
            let button = $(this);
            rowToDelete = button.parents().eq(1);
            transactionId = rowToDelete.data('transactionid');
            categoryId = rowToDelete.data('categoryid');
            rowToDelete.next().children().html($('#editIncomeCategoryBlock').html());
            let selectElement = rowToDelete.next().find('#category');
            getCategoriesAjax(transactionType, function (categories) {
                //remove currently set category from options and append rest for user's choice
                for (var i = 0; i < categories.length; i++) {
                    if (categories[i].id == categoryId) {
                        console.log(categories[i].id);
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

        $('#alterTransactionsModal').on('click', ".deleteBtn", function () {
            var button = $(this);
            rowToDelete = button.parents().eq(1);
            transactionId = rowToDelete.data('transactionid');
            rowToDelete.next().children().html($('#confrimTransactionDeletion').html());
            rowToDelete.next().show();
        });

        $('#alterTransactionsModal').on('click', ".confirmBtn", function () {
            //function in deleteTransaction.js file
            deleteTransactionEntry(transactionId, function (result) {
                if (result == true) {
                    let container = rowToDelete.parent();
                    rowToDelete.next().children().hide();
                    rowToDelete.next().children().remove();
                    rowToDelete.remove();
                    if ($(container[0].id + " div").length == 0) {
                        console.log('No juz ni ma');
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
            deleteCategory(categoryData, function () {
                if (callback == true) {
                    $('#confirmModal').modal('hide');
                    categoryData.rowToDelete.remove();
                }
            });
        });
    }
});
