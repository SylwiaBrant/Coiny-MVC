$(document).ready(function () {
    $('#addIncomeForm').validate({
        rules: {
            money: {
                required: true,
                number: true,
                step: 0.01
            },
            incomeDate: {
                required: true,
                date: true,
            },
            incomeCategory: {
                required: true,
                validCategory: true
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
            $.ajax({
                url: "/Incomes/addIncomeAjax",
                type: "POST",
                dataType: 'json',
                data: data
            }).done(function (response) {
                if (response == true) {
                    $(form).trigger("reset");
                    $('#invoiceDropdown').hide();
                    addSuccessFlash('#addIncomeForm', 'Przychód dodany pomyślnie.');
                }
                else {
                    addFailFlash('#addIncomeForm', 'Coś poszło nie tak...');
                    /*console.dir(response);*/
                }
            }).fail(function (jqXHR, textStatus) {
                addFailFlash('#addIncomeForm', 'Coś poszło nie tak...');
                /*console.log("No i klops!" + jqXHR + textStatus);
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

    $.validator.addMethod('validCategory',
        function (value, element) {
            if (value == 'Wybierz kategorię') {
                return false;
            }
            return true;
        },
        'Wybierz kategorię.'
    );
});