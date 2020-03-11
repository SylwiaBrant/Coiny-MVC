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
            console.log(expenses);
        }).fail(function (expenses) {
            console.log("No i klops!" + expenses);
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
            let url = "/Expenses/showChosenPeriodExpensesAjax";
            $.ajax({
                url: url,
                type: "POST",
                dataType: 'json',
                data: data
            }).done(function (expenses) {
                $('#expensesTable tbody > tr').remove();
                displayExpenses(expenses);
            }).fail(function (expenses) {
                console.log("No i klops!" + expenses);
                console.dir(arguments);
            });
            return false;
        }
    });

    function displayExpenses(expenses) {
        $.each(expenses, function (i, expense) {
            console.log(expense);
            var row = "<tr data-catId='" + expense.id + "'>" +
                "<td class='category'>" + expense.category + "</td>" +
                "<td class='date'>" + expense.date + "</td>" +
                "<td class='method'>" + expense.method + "</td>" +
                "<td class='money'><strong>" + expense.money + "</strong></td>" +
                "<td class='comment'>" + expense.comment + "</td>" +
                "</tr>";
            $('#expensesTable tbody').append(row);
        });
    }
});