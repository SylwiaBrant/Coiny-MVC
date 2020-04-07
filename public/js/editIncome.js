function fillInputsWithData(row) {
    let transactionId = row.data('transid');
    let money = row.find('.money').text();
    let date = row.find('.incomeDate').text();
    let category = row.find('.incomeCategory').text().trim();
    let comment = row.find('.comment').text().trim();
    if (comment == 'brak') {
        comment = '';
    }

    $('#editIncomeForm').find('#id').val(transactionId);
    $('#editIncomeForm').find('#money').val(money);
    $('#editIncomeForm').find('#incomeDate').val(date);
    $('#editIncomeForm').find('#incomeCategory').val(category);
    $('#editIncomeForm').find('#comment').val(comment);
}

function updateTable(row) {
    let money = $('#editIncomeForm #money').val();
    let date = $('#editIncomeForm #incomeDate').val();
    let category = $('#editIncomeForm #incomeCategory').val();
    let comment = $('#editIncomeForm #comment').val();
    if (comment == '') {
        comment = 'brak';
    }
    row.find('td.money').html(money);
    row.find('td.incomeDate').html(date);
    row.find('td.incomeCategory').html(category);
    row.find('td.comment').html(comment);
}

$(document).ready(function () {
    $('#incomesTable').on('click', '.editIncomeBtn', function () {
        let row = $(this).closest('tr');
        getCategoriesAjax('Income', function (categories) {
            $.each(categories, function (i, category) {
                var option = "<option data-transid='" + category.id + "'>" + category.name + "</option>";
                $('#incomeCategory').append(option);
            });
        });
        fillInputsWithData(row);
        $('#editIncomeModal').modal('show');
        editIncomeInDB(function (response) {
            if (response == true) {
                /*console.log("Sukces!" + response);*/
                updateTable(row);
                $('#editIncomeForm').trigger("reset");
                $('#editIncomeModal').modal("hide");
            }
        });
    });

    function editIncomeInDB(callback) {
        $('#editIncomeForm').validate({
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
                }
            },
            submitHandler: function (form) {
                var data = $(form).serialize();
                $.ajax({
                    url: "/Incomes/editIncomeAjax",
                    type: "POST",
                    dataType: "json",
                    cache: false,
                    data: data
                }).done(function (response) {
                    /*console.log(response);*/
                    callback(response);
                }).fail(function (jqXHR, textStatus) {
                    /*console.log("No i klops!" + jqXHR + textStatus);
                    console.dir(arguments);*/
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