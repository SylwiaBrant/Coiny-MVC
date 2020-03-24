$(document).ready(function () {
    $('#transactionsPeriod').on('change', function (e) {
        $('input[type=date]', '#periodForm').val('');
        let action = $('#transactionsPeriod option:selected').val();
        let url = "/Incomes/" + action + "Ajax";
        $.ajax({
            url: url,
            type: "POST",
            dataType: "json"
        }).done(function (incomes) {
            $('#incomesTable tbody > tr').remove();
            displayIncomes(incomes);
            /*console.log(incomes);*/
        }).fail(function (jqXHR, textStatus) {
            /*console.log("No i klops!" + jqXHR + textStatus);
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
            let url = "/Incomes/showChosenPeriodIncomesAjax";
            $.ajax({
                url: url,
                type: "POST",
                dataType: "json",
                data: data
            }).done(function (incomes) {
                $('#incomesTable tbody > tr').remove();
                displayIncomes(incomes);
            }).fail(function (jqXHR, textStatus) {
                /*console.log("No i klops!" + jqXHR + textStatus);
                console.dir(arguments);*/
            });
            return false;
        }
    });

    function displayIncomes(incomes) {
        $.each(incomes, function (i, income) {
            var comment;
            if (income.comment == null) {
                comment = "<td class='comment'>brak</td>";
            } else {
                comment = "<td class='comment'>" + income.comment + "</td>";
            }
            var row = "<tr data-transid='" + income.id + "'>" +
                "<td class='incomeCategory'>" + income.name + "</td>" +
                "<td class='incomeDate'>" + income.date + "</td>" +
                "<td class='money'>" + income.money + "</td>" +
                comment +
                "<td>" +
                "<button type='button' class='settingsBtn editIncomeBtn'><i class='icon-edit'></i></button>" +
                "<button type='button' class='settingsBtn deleteIncomeBtn'><i class='icon-trash'></i></button></td>" +
                "</tr>";
            $('#incomesTable tbody').append(row);
        });
    }
});