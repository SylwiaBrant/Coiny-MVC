$(document).ready(function () {

    let expenseCats = $.ajax({
        url: "/Settings/getExpenseCategoriesAjax",
        type: "POST",
        cache: false,
        dataType: "json"
    });
    let paymentCats = $.ajax({
        url: "/Settings/getPaymentCategoriesAjax",
        type: "POST",
        cache: false,
        dataType: "json"
    });
    $.when(expenseCats, paymentCats).then(function (expenseCats, paymentCats) {
        addExpense(expenseCats, paymentCats);
    });


    function addExpense(expenseCats, paymentCats) {
        $('#addExpenseForm').validate({
            rules: {
                money: {
                    required: true,
                    number: true,
                    step: 0.01
                },
                expenseDate: {
                    required: true,
                    date: true
                },
                expenseCategory: {
                    required: true,
                    validCategory: true
                },
                paymentMethod: {
                    required: true,
                    validPayment: true
                },
                comment: {
                    maxlength: 400
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
                    /*console.log(jqXHR + textStatus);
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

        $('#money').on('focusout', function () {
            $('#expenseWarning').remove();
            $('#paymentWarning').remove();
            let expenseCategory = $('#expenseCategory').val();
            if (expenseCategory != 'Wybierz kategorię') {
                let expenseLimit = parseInt(getCategoryLimit(expenseCats, expenseCategory));
                checkExpenseLimit(expenseCats, expenseLimit);
            }
            let paymentMethod = $('#paymentMethod').val();
            if (paymentMethod != 'Wybierz metodę płatności') {
                let paymentLimit = parseInt(getCategoryLimit(paymentCats, paymentMethod));
                checkPaymentLimit(paymentMethod, paymentLimit);
            }
        });

        $('#expenseCategory').on('change', function () {
            $('#expenseWarning').remove();
            let expenseCategory = $('#expenseCategory').val();
            if ($('#money').val() != '' && expenseCategory != 'Wybierz kategorię') {
                let expenseLimit = parseInt(getCategoryLimit(expenseCats, expenseCategory));
                checkExpenseLimit(expenseCategory, expenseLimit);
            }
        });

        $('#paymentMethod').on('change', function () {
            $('#paymentWarning').remove();
            let paymentMethod = $('#paymentMethod').val();
            if ($('#money').val() != '' && paymentMethod != 'Wybierz metodę płatności') {
                let paymentLimit = parseInt(getCategoryLimit(paymentCats, paymentMethod));
                checkPaymentLimit(paymentMethod, paymentLimit);
            }
        });
    }

    function checkExpenseLimit(expenseCategory, expenseLimit) {
        if (expenseLimit) {
            getThisMonthExpenseSum(expenseCategory, function (expenseSum) {
                let expenseSumValue = parseInt($('#money').val()) + parseInt(expenseSum);
                if (expenseSumValue > expenseLimit) {
                    let overloadValue = expenseSumValue - expenseLimit;
                    $('#expenseCategory').parent().append("<p class='overloadWarning' id='expenseWarning'>" +
                        "Dodając transakcję limit kategorii <b>" + expenseCategory + "</b> będzie przekroczony o <b>" +
                        overloadValue + " zł</b>. Dokonaj zmian lub dodaj wydatek pomimo to.</p>");
                }
            });
        }
    }

    function checkPaymentLimit(paymentMethod, paymentLimit) {
        if (paymentLimit) {
            getThisMonthPaymentSum(paymentMethod, function (paymentSum) {
                let paymentSumValue = parseInt($('#money').val()) + parseInt(paymentSum);
                if (paymentSumValue > paymentLimit) {
                    let overloadValue = paymentSumValue - paymentLimit;
                    $('#paymentMethod').parent().append("<p class='overloadWarning' id='paymentWarning'>" +
                        "Dodając transakcję limit kategorii <b>" + paymentMethod + "</b> będzie przekroczony o <b>" +
                        overloadValue + " zł</b>. Dokonaj zmian lub dodaj wydatek pomimo to.</p>");
                }
            });
        }
    }

    function getCategoryLimit(categories, category) {
        for (var i = 0; i < categories.length; i++) {
            if (categories[0][i].name == category) {
                return categories[0][i].blocked_funds;
            }
        }
        return false;
    }

    function getThisMonthExpenseSum(expenseCategory, callback) {
        $.ajax({
            url: "/Expenses/getThisMonthExpenseSum",
            type: "POST",
            cache: false,
            dataType: "json",
            data: { category: expenseCategory }
        }).done(function (response) {
            callback(response);
        }).fail(function (jqXHR, textStatus) {
            /*console.log('Coś poszło nie tak...');
            console.log("No i klops!" + jqXHR + textStatus);
            console.dir(arguments);*/
        });
    }

    function getThisMonthPaymentSum(paymentMethod, callback) {
        $.ajax({
            url: "/Expenses/getThisMonthPaymentSum",
            type: "POST",
            cache: false,
            dataType: "json",
            data: { category: paymentMethod }
        }).done(function (response) {
            callback(response);
        }).fail(function (jqXHR, textStatus) {
            /*console.log('Coś poszło nie tak...');
            console.log("No i klops!" + jqXHR + textStatus);
            console.dir(arguments);*/
        });
    }

    $.validator.addMethod('validCategory',
        function (value, element) {
            if (value == 'Wybierz kategorię') {
                return false;
            }
            return true;
        },
        'Wybierz kategorię.'
    );

    $.validator.addMethod('validPayment',
        function (value, element) {
            if (value == 'Wybierz metodę płatności') {
                return false;
            }
            return true;
        },
        'Wybierz metodę płatności.'
    );
});