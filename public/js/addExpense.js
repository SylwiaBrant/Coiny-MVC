function getCategories(action, parent) {
    let url = "/Settings/" + action + "Ajax";
    $.ajax({
        url: url,
        type: "POST",
        dataType: "json"
    }).done(function (categories) {
        console.log(categories);
        displayCategories(categories, parent);
    }).fail(function (jqXHR, textStatus) {
        console.log("No i klops!" + jqXHR + textStatus);
        console.dir(arguments);
    });
}

function displayCategories(categories, parent) {
    $.each(categories, function (index) {
        console.log(index);
        console.log(categories[index]);
        console.log(categories[index].name);
        var category = "<option>" + categories[index].name + "</option>";
        $(parent).append(category);
    });
}

$(document).ready(function () {

    // Load income categories from DB using AJAX 
    $('#addIncomeBtn').on('click', function () {
        getCategories('getIncomeCategories', '#incomeCategory');
        $('#addIncomeModal').modal('toggle');
        incomeIsValid('#addIncomeForm');
    });
    // Load income categories from DB using AJAX 
    $('#addExpenseBtn').on('click', function () {
        getCategories('getExpenseCategories', '#expenseCategory');
        getCategories('getPaymentMethods', '#paymentMethod');
        $('#addExpenseModal').modal('toggle');
        expenseIsValid('#addExpenseForm');
    });

    $('#invoiceCheckbox').on('click', function () {
        if ($(this).is(':checked')) {
            $('#invoiceNumber').attr('disabled', false);
            $('#contractor').attr('disabled', false);
            $('#invoicePayDate').attr('disabled', false);
        } else {
            $('#invoiceNumber').attr('disabled', true);
            $('#contractor').attr('disabled', true);
            $('#invoicePayDate').attr('disabled', true);
        }
    });

    // Validate form input using jQuery Validation Plugin 
    function incomeIsValid(form) {
        $(form).validate({
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
                    url: "/Incomes/addIncomeAjax",
                    type: "POST",
                    dataType: 'json',
                    data: data
                }).done(function (response) {
                    if (response == true) {
                        $('#addIncomeModal').modal('hide');
                        console.log("Sukces!" + response);
                    }
                    else {
                        console.log("No tak średnio bym powiedziała." + response);
                        console.dir(arguments);
                    }
                }).fail(function (jqXHR, textStatus) {
                    console.log("No i klops!" + jqXHR + textStatus);
                    console.dir(arguments);
                });
                return false;
            }
        });
    }
    // Validate form input using jQuery Validation Plugin 
    function expenseIsValid(form) {
        $(form).validate({
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
                category: {
                    required: true,
                },
                method: {
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
                    dataType: 'json',
                    data: data
                }).done(function (response) {
                    if (response == true) {
                        $('#addExpenseModal').modal('hide');
                        console.log("Sukces!" + response);
                    }
                    else {
                        console.log("No tak średnio bym powiedziała." + response);
                        console.dir(arguments);
                    }
                }).fail(function (jqXHR, textStatus) {
                    console.log("No i klops!" + jqXHR + textStatus);
                    console.dir(arguments);
                });
                return false;
            }
        });
    }
});