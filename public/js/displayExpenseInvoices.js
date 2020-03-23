
$(document).ready(function () {
    $('#periodForm').validate({
        rules: {
            startingDate: {
                required: true,
                date: true,
            },
            endingDate: {
                required: true,
                date: true,
            },
        },
        submitHandler: function (form) {
            let data = $('#periodForm').serialize();
            let url = "/Invoices/showChosenPeriodExpenseInvoicesAjax";
            $.ajax({
                url: url,
                type: "POST",
                dataType: 'json',
                data: data
            }).done(function (invoices) {
                $('#invoicesTable tbody > tr').remove();
                displayInvoices(invoices);
            }).fail(function (invoices) {
                /*console.log("No i klops!" + invoices);
                console.dir(arguments);*/
            });
            return false;
        }
    });

    function displayInvoices(invoices) {
        $.each(invoices, function (i, invoice) {
            var comment;
            if (invoice.comment == null) {
                comment = "<td class='comment'>brak</td>";
            } else {
                comment = "<td class='comment'>" + invoice.comment + "</td>";
            }
            var row = "<tr data-invoiceid='" + invoice.id + "'>" +
                "<td class='number'>" + invoice.number + "</td>" +
                "<td class='contractor'>" + invoice.contractor + "</td>" +
                "<td class='date'>" + invoice.date + "</td>" +
                "<td class='paymentDate'>" + invoice.payment_date + "</td>" +
                "<td class='money'>" + invoice.money + "</td>" +
                comment +
                "<td>" +
                "<button type='button' class='settingsBtn editInvoiceBtn'><i class='icon-edit'></i></button>" +
                "<button type='button' class='settingsBtn deleteInvoiceBtn'><i class='icon-trash'></i></button></td>" +
                "</tr>";
            $('#invoicesTable tbody').append(row);
        });
    }
});