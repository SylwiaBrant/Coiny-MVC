$(document).ready(function () {
    $('#transactionsPeriod').on('change', function (e) {
        $('input[type=date]', '#periodForm').val('');
        let action = $('#transactionsPeriod option:selected').val();
        let url = "/Invoices/" + action + "Ajax";
        $.ajax({
            url: url,
            type: "POST",
            dataType: "json"
        }).done(function (response) {
            $('#invoicesTable tbody > tr').remove();
            displayInvoices(response);
        }).fail(function (response) {
            console.log("No i klops!" + response);
            console.dir(arguments);
        });
    });

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
            let url = "/Invoices/showChosenPeriodIncomeInvoicesAjax";
            $.ajax({
                url: url,
                type: "POST",
                dataType: "json",
                data: data,
                cache: false
            }).done(function (i) {
                $('#invoicesTable tbody > tr').remove();
                displayInvoices(i);
            }).fail(function (i) {
                console.log("No i klops!" + i);
                console.dir(arguments);
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
            var row = "<tr data-catId='" + invoice.id + "'>" +
                "<td class='number'>" + invoice.number + "</td>" +
                "<td class='contractor'>" + invoice.contractor + "</td>" +
                "<td class='date'>" + invoice.date + "</td>" +
                "<td class='paymentDate'>" + invoice.payment_date + "</td>" +
                "<td class='money'><strong>" + invoice.money + "</strong></td>" +
                comment + "</tr>";
            $('#invoicesTable tbody').append(row);
        });
    }
});