$(document).ready(function () {
    $('#transactionsPeriod').on('change', function (e) {
        $('input[type=date]', '#periodForm').val('');
        let action = $('#transactionsPeriod option:selected').val();
        let url = "/Expenses/" + action + "Ajax";
        $.ajax({
            url: url,
            type: "POST",
            dataType: "json"
        }).done(function (expenses) {
            $('#expensesTable tbody > tr').remove();
            displayExpenses(expenses);
            /*console.log(expenses);*/
        }).fail(function (expenses) {
            /*console.log("No i klops!" + expenses);
             console.dir(arguments);*/
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
            let url = "/Expenses/showChosenPeriodExpensesAjax";
            $.ajax({
                url: url,
                type: "POST",
                dataType: "json",
                data: data
            }).done(function (expenses) {
                $('#expensesTable tbody > tr').remove();
                displayExpenses(expenses);
            }).fail(function (jqXHR, textStatus) {
                /*console.log("No i klops!" + jqXHR + textStatus);
                console.dir(arguments);*/
            });
            return false;
        }
    });

    function displayExpenses(expenses) {
        $.each(expenses, function (i, expense) {
            var comment;
            if (expense.comment == null) {
                comment = "<td class='comment'>brak</td>";
            } else {
                comment = "<td class='comment'>" + expense.comment + "</td>";
            }
            var row = "<tr data-transid='" + expense.id + "'>" +
                "<td class='category'>" + expense.category + "</td>" +
                "<td class='date'>" + expense.date + "</td>" +
                "<td class='payment'>" + expense.method + "</td>" +
                "<td class='money'>" + expense.money + "</td>" +
                comment +
                "<td>" +
                "<button type='button' class='settingsBtn editExpenseBtn'><i class='icon-edit'></i></button>" +
                "<button type='button' class='settingsBtn deleteExpenseBtn'><i class='icon-trash'></i></button></td>" +
                "</tr>";
            $('#expensesTable tbody').append(row);
        });
    }
});