$(document).ready(function () {
    $('#addExpenseForm').validate({
        rules: {
            money: {
                required: true,
                number: true,
                step: 0.01
            },
            expenseDate: {
                required: true,
                date: true,
            },
            expenseCategory: {
                required: true,
            },
            paymentMethod: {
                required: true,
            },
            comment: {
                maxlength: 400,
            },
            invoiceNumber: {
                required: {
                    depends: function (element) {
                        return $("#invoiceCheckbox").is(":checked");
                    }
                },
            },
            contractor: {
                required: {
                    depends: function (element) {
                        return $("#invoiceCheckbox").is(":checked");
                    }
                },
                rangelength: [1, 256],
            },
            invoicePayDate: {
                required: {
                    depends: function (element) {
                        return $("#invoiceCheckbox").is(":checked");
                    }
                },
                date: true,
            },
        },
        submitHandler: function (form) {
            var data = $(form).serialize();
            console.log(data);
            $.ajax({
                url: "/Expenses/addExpenseAjax",
                type: "POST",
                dataType: "json",
                data: data
            }).done(function (response) {
                if (response == true) {
                    $(form).trigger("reset");
                    $('#invoiceDropdown').hide();
                    addSuccessFlash('#addExpenseForm', 'Wydatek dodany pomyślnie.');
                }
                else {
                    addFailFlash('Coś poszło nie tak...');
                }
            }).fail(function (jqXHR, textStatus) {
                addFailFlash('#addExpenseForm', 'Coś poszło nie tak...');
                /*   console.log("No i klops!" + jqXHR + textStatus);
                    console.dir(arguments);*/
            });
            return false;
        }
    });

    $('#invoiceCheckbox').click(function () {
        if (this.checked) {
            $('#invoiceDropdown').show();
        }
        else { $('#invoiceDropdown').hide(); }
    });
});