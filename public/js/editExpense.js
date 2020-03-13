function fillInputsWithData(row) {
    let transactionId = row.data('transid');
    let money = row.find('.money').text();
    let date = row.find('.date').text();
    let payment = row.find('.payment').text();
    let category = row.find('.category').text();
    let comment = row.find('.comment').text();
    if (comment == 'brak') {
        comment = '';
    }

    $('#editExpenseForm').find('#id').val(transactionId);
    $('#editExpenseForm').find('#money').val(money);
    $('#editExpenseForm').find('#expenseDate').val(date);
    $('#editExpenseForm').find('#paymentMethod').val(payment);
    $('#editExpenseForm').find('#expenseCategory').val(category);
    $('#editExpenseForm').find('#comment').val(comment);
}

function updateTable(row) {
    let money = $('#editExpenseForm #money').val();
    let date = $('#editExpenseForm #expenseDate').val();
    let payment = $('#editExpenseForm #paymentMethod').val();
    let category = $('#editExpenseForm #expenseCategory').val();
    let comment = $('#editExpenseForm #comment').val();
    if (comment == '') {
        comment = 'brak';
    }
    row.find('td.money').html(money);
    row.find('td.expenseDate').html(date);
    row.find('td.paymentMethod').html(payment);
    row.find('td.category').html(category);
    row.find('td.comment').html(comment);
}

$(document).ready(function () {
    $('#expensesTable').on('click', '.editExpenseBtn', function () {
        let row = $(this).closest('tr');
        getCategoriesAjax('Expense', function (categories) {
            $.each(categories, function (i, category) {
                var option = "<option data-transid='" + category.id + "'>" + category.name + "</option>";
                $('#expenseCategory').append(option);
            });
        });
        getCategoriesAjax('Payment', function (methods) {
            $.each(methods, function (i, method) {
                var option = "<option data-transid='" + method.id + "'>" + method.name + "</option>";
                $('#paymentMethod').append(option);
            });
        });
        fillInputsWithData(row);
        $('#editExpenseModal').modal('show');
        editExpenseInDB(function (response) {
            if (response == true) {
                console.log("Sukces!" + response);
                updateTable(row);
                $('#editExpenseForm').trigger("reset");
            }
        });
    });

    function editExpenseInDB(callback) {
        console.log('weszlo');
        $('#editExpenseForm').validate({
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
            },
            submitHandler: function (form) {
                var data = $(form).serialize();
                $.ajax({
                    url: "/Expenses/editExpenseAjax",
                    type: "POST",
                    dataType: "json",
                    cache: false,
                    data: data
                }).done(function (response) {
                    console.log(response);
                    callback(response);
                }).fail(function (jqXHR, textStatus) {
                    console.log("No i klops!" + jqXHR + textStatus);
                    console.dir(arguments);
                });
                return false;
            }
        });
    }
    $('#invoiceCheckbox').on('click', function () {
        if (this.checked) {
            $('#invoiceDropdown').show();
        }
        else { $('#invoiceDropdown').hide(); }
    });
});