function deleteTransactionEntry(transactionType, transactionId, callback) {
    console.log('No i wyszło: ', transactionType, transactionId);
    let url = "/" + transactionType + "s/deleteEntryAjax";
    $.ajax({
        url: url,
        type: "POST",
        dataType: "json",
        cache: false,
        data: { id: transactionId }
    }).done(function (response) {
        console.log(response);
        callback(response);
    }).fail(function (response) {
        console.log("No i klops!" + response);
        console.dir(arguments);
    })
}

$(document).ready(function () {
    $('#incomesTable').on('click', '.deleteIncomeBtn', function () {
        let row = $(this).closest('tr');
        console.log(row);
        let transactionId = row.data('catid');
        let question = "Czy na pewno chcesz usunąć wpis?"
        $('#question').html(question);
        $('#confirmModal').modal('toggle');
        confirmTransactionDelete('Income', transactionId, row)
    });

    $('#expensesTable').on('click', '.deleteExpenseBtn', function () {
        let row = $(this).closest('tr');
        let transactionId = row.data('catid');
        let question = "Czy na pewno chcesz usunąć wpis?"
        $('#question').html(question);
        $('#confirmModal').modal('toggle');
        confirmTransactionDelete('Expense', transactionId, row)
    });

    function confirmTransactionDelete(transactionType, transactionId, rowToDelete) {
        $("#confirmModal").on('click', "#confirmBtn", function (e) {
            e.preventDefault();
            deleteTransactionEntry(transactionType, transactionId, function (result) {
                if (result == true) {
                    $('#confirmModal').modal('hide');
                    rowToDelete.remove();
                }
            });
        });
    }

    $('#confirmModal').on('hidden.bs.modal', function () {
        $(this).find('form').trigger('reset');
    });
});