function fillInputsWithData(row) {
    let invoiceId = row.data('invoiceid');
    let invoiceNumber = row.find('.invoiceNumber').text();
    let paymentDate = row.find('.paymentDate').text();
    let contractor = row.find('.contractor').text();

    $('#editInvoiceForm').find('#invoiceId').val(invoiceId);
    $('#editInvoiceForm').find('#invoiceNumber').val(invoiceNumber);
    $('#editInvoiceForm').find('#invoicePayDate').val(paymentDate);
    $('#editInvoiceForm').find('#contractor').val(contractor);
}

function updateTable(row) {
    let invoiceNumber = $('#editInvoiceForm #invoiceNumber').val();
    let paymentDate = $('#editInvoiceForm #invoicePayDate').val();
    let contractor = $('#editInvoiceForm #contractor').val();
    row.find('td.invoiceNumber').html(invoiceNumber);
    row.find('td.paymentDate').html(paymentDate);
    row.find('td.contractor').html(contractor);
}

$(document).ready(function () {
    $('#invoicesTable').on('click', '.editIncomeBtn', function () {
        let row = $(this).closest('tr');
        fillInputsWithData(row);
        $('#editInvoiceModal').modal('show');
        let url = "/Invoices/editIncomeInvoiceAjax";
        editInvoiceInDB(url, function (response) {
            if (response == true) {
                updateTable(row);
                $('#editInvoiceModal').modal('hide');
                $('#editInvoiceForm').trigger('reset');
            }
        });
    });

    $('#invoicesTable').on('click', '.editExpenseBtn', function () {
        let row = $(this).closest('tr');
        fillInputsWithData(row);
        $('#editInvoiceModal').modal('show');
        let url = "/Invoices/editExpenseInvoiceAjax";
        editInvoiceInDB(url, function (response) {
            if (response == true) {
                updateTable(row);
                $('#editInvoiceModal').modal('hide');
                $('#editInvoiceForm').trigger('reset');
            }
        });
    });

    function editInvoiceInDB(url, callback) {
        $('#editInvoiceForm').validate({
            rules: {
                invoiceNumber: {
                    required: true,
                    rangelength: [1, 256]

                },
                invoicePayDate: {
                    required: true,
                    date: true
                },
                contractor: {
                    required: true,
                    rangelength: [2, 256]
                },
            },
            submitHandler: function (form) {
                var data = $(form).serialize();
                $.ajax({
                    url: url,
                    type: "POST",
                    dataType: "json",
                    cache: false,
                    data: data
                }).done(function (response) {
                    /*console.log(response);*/
                    callback(response);
                }).fail(function (jqXHR, textStatus) {
                    /*console.log("No i klops!" + jqXHR + textStatus);
                    console.dir(arguments);*/
                });
                return false;
            }
        });
    }
});