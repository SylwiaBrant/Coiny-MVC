$(document).ready(function () {
    function categoryIsValid(form, action) {
        console.log(form);
        $(form).validate({
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
                //    console.log('no i tutaj kurwa nie doszlismy');
                let data = $(form).serializeArray();
                console.log(data);
                let url = "/Settings/" + action;
                $.ajax({
                    url: url,
                    type: "POST",
                    dataType: 'json',
                    data: data
                }).done(function (response) {
                    if (response > 0) {
                        console.log("Sukces!" + response);
                        $('#addCategoryModal').modal('hide');
                        var row = "<tr data-catId='" + response + "'>" +
                            "<td class='category'>" + data[0].value + "</td>" +
                            "<td class='blockedFunds'>" + data[2].value + "</td>" +
                            "<td>" + "<div class='btn-group' role='group' aria-label='buttonsGroup'>" +
                            "<button type='button' class='settingsBtn editBtn'><i class='icon-edit'></i></button>" +
                            "<button type='button' class='settingsBtn deleteBtn'><i class='icon-trash'></i></button>" + "</td>" +
                            "</tr>";
                        $('#incomeCatsList tbody').append(row);
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
    }

    $('#fundsBlockCheckbox').on('click', function () {
        if ($(this).is(':checked')) {
            $('#blockedFunds').attr('disabled', false);
        } else {
            $('#blockedFunds').attr('disabled', true);
        }
    });

    $("#newIncomeCategory").on('click', function () {
        $('#addCategoryModal').modal('toggle');
        categoryIsValid('#addCategoryForm', 'addIncomeCategory');
    });

    $("#newExpenseCategory").on('click', function () {
        $('#addcategoryModal').modal('toggle');
        categoryIsValid('#addCategoryForm', 'addExpenseCategory');
    });

    $("#newPaymentMethod").on('click', function () {
        $('#addModal').modal('toggle');
        categoryIsValid('#addCategoryForm', 'addPaymentMethod');
    });

    $('#addCategoryModal').on('hidden.bs.modal', function () {
        $(this).find('form').trigger('reset');
    })

});
//var s = $('#post-form').find('input, textarea, select')
//.not(':checkbox')
//.serialize()