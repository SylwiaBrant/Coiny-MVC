$(document).ready(function () {
    $("#newIncomeCategory").on('click', function () {
        $('#addCategoryModal').modal('toggle');
        categoryIsValid('#addCategoryForm', 'Income', '#incomeCatsList');
    });

    $("#newExpenseCategory").on('click', function () {
        $('#addCategoryModal').modal('toggle');
        categoryIsValid('#addCategoryForm', 'Expense', '#expenseCatsList');
    });

    $("#newPaymentMethod").on('click', function () {
        $('#addCategoryModal').modal('toggle');
        categoryIsValid('#addCategoryForm', 'Payment', '#paymentMetsList');
    });

    $('#fundsBlockCheckbox').on('click', function (e) {
        if ($(this).is(':checked')) {
            $('#blockedFunds').attr('disabled', false);
        } else {
            $('#blockedFunds').attr('disabled', true);
        }
    });

    function categoryIsValid(action, parent) {
        var validator = $('#addCategoryModal').validate({
            rules: {
                name: {
                    required: true,
                    rangelength: [1, 80],
                },
                blockedFunds: {
                    required: {
                        depends: function (element) {
                            return $("#fundsBlockCheckbox").is(":checked");
                        }
                    },
                    number: true,
                    step: 0.01
                },
            },
            submitHandler: function (form) {
                let data = $(form).serializeArray();
                console.log(data);
                let url = "/Settings/add" + action + "CategoryAjax";
                $.ajax({
                    url: url,
                    type: "POST",
                    dataType: 'json',
                    cache: false,
                    data: data
                }).done(function (response) {
                    if (response > 0) {
                        console.log("Sukces!" + response);
                        $('.modal').modal('hide');
                        if (action == 'Income') {
                            appendIncome(response, parent, data);
                        } else {
                            if (data.length == 1) {
                                appendName(response, parent, data);
                            }
                            else {
                                appendNameAndLimit(response, parent, data);
                            }
                        }
                    }
                    else {
                        console.log("Lipa! Nie edytowano rekordu" + response);
                        console.dir(arguments);
                    }
                }).fail(function (response) {
                    console.log("No i klops!" + response);
                    console.dir(arguments);
                });
                return false;
            }
        });

        $(".cancel").click(function () {
            validator.resetForm();
            $(this).find('form').trigger('reset');
            $('#addCategoryModal #fundsBlockCheckbox').show();
            $('#addCategoryModal #blockedFunds').show();
            $('#blockedFunds').attr('disabled', true);
        });
    }

    function appendIncome(id, parent, data) {
        let row = "<tr data-catId='" + id + "'>" +
            "<td class='category'>" + data[0].value + "</td>" +
            "<td><div class='btn-group' role='group' aria-label='buttonsGroup'>" +
            "<button type='button' class='settingsBtn deleteBtn'><i class='icon-trash'></i></button>" + "</td>" +
            "</tr>";
        $(parent + ' tbody').append(row);
    }

    function appendName(id, parent, data) {
        let row = "<tr data-catId='" + id + "'>" +
            "<td class='category'>" + data[0].value + "</td>" +
            "<td class='category'>" + '' + "</td>" +
            "<td><div class='btn-group' role='group' aria-label='buttonsGroup'>" +
            "<button type='button' class='settingsBtn editBtn'><i class='icon-edit'></i></button>" +
            "<button type='button' class='settingsBtn deleteBtn'><i class='icon-trash'></i></button>" + "</td>" +
            "</tr>";
        $(parent + ' tbody').append(row);
    }

    function appendNameAndLimit(id, parent, data) {
        let row = "<tr data-catId='" + id + "'>" +
            "<td class='category'>" + data[0].value + "</td>" +
            "<td class='category'>" + data[2].value + "</td>" +
            "<td><div class='btn-group' role='group' aria-label='buttonsGroup'>" +
            "<button type='button' class='settingsBtn editBtn'><i class='icon-edit'></i></button>" +
            "<button type='button' class='settingsBtn deleteBtn'><i class='icon-trash'></i></button>" + "</td>" +
            "</tr>";
        $(parent + ' tbody').append(row);
    }
});
