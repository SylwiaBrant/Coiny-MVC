function deleteInvoice(invoiceType, invoiceId, callback) {
    console.log('No i wyszło: ', invoiceType, invoiceId);
    let url = "/Invoices/delete" + invoiceType + "Ajax";
    $.ajax({
        url: url,
        type: "POST",
        dataType: "json",
        cache: false,
        data: { id: invoiceId }
    }).done(function (response) {
        console.log(response);
        callback(response);
    }).fail(function (response) {
        console.log("No i klops!" + response);
        console.dir(arguments);
    })
}

$(document).ready(function () {
    $('#invoicesTable').on('click', '.deleteIncomeBtn', function () {
        let row = $(this).closest('tr');
        console.log(row);
        let invoiceId = row.data('invoiceid');
        let question = "Czy na pewno chcesz usunąć fakturę?"
        $('#question').html(question);
        $('#confirmModal').modal('toggle');
        confirmInvoiceDelete('IncomeInvoice', invoiceId, row)
    });

    $('#invoicesTable').on('click', '.deleteExpenseBtn', function () {
        let row = $(this).closest('tr');
        let invoiceId = row.data('invoiceid');
        let question = "Czy na pewno chcesz usunąć wpis?"
        $('#question').html(question);
        $('#confirmModal').modal('toggle');
        confirmInvoiceDelete('ExpenseInvoice', invoiceId, row)
    });

    function confirmInvoiceDelete(invoiceType, invoiceId, rowToDelete) {
        $("#confirmModal").on('click', "#confirmBtn", function (e) {
            e.preventDefault();
            deleteInvoice(invoiceType, invoiceId, function (result) {
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