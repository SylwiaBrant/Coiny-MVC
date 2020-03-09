$(document).ready(function () {
    $("#expenseCatsList").on('click', ".editBtn", function () {
        let button = $(this);
        let data = getEditData(button, 'Expense');
        confirmEdit(data);
    });

    $("#paymentCatsList").on('click', ".editBtn", function () {
        let button = $(this);
        let data = getEditData(button, 'Payment');
        confirmEdit(data);
    });

    $('#editModal').on('click', '#fundsBlockCheckbox', function (e) {
        if ($(this).is(':checked')) {
            $('#editModal #blockedFunds').attr('disabled', false);
        } else {
            $('#editModal #blockedFunds').attr('disabled', true);
        }
    });

    function getEditData(button, transactionType) {
        let data = {};
        data.transactionType = transactionType;
        data.category = button.parents().eq(1).siblings('.category').text();
        data.blockedFunds = +(button.parents().eq(1).siblings('.blockedFunds').text());
        data.toEdit = button.closest('tr');
        data.categoryId = data.toEdit.data('catid');
        return data;
    }

    function confirmEdit(data) {
        $('#updateForm').find('#id').val(data.categoryId);
        $('#updateForm').find('#name').val(data.category);
        $('#updateForm').find('#blockedFunds').val(data.blockedFunds);
        if (data.blockedFunds == true) {
            $('#fundsBlockCheckbox').attr("checked", true);
        } else {
            $('#fundsBlockCheckbox').attr("checked", false);
        }
        $('#editModal').modal('toggle');
        editIsValid(data);
    }

    function editIsValid(categoryData) {
        //   let transactionType = ;
        var validator = $('#updateForm').validate({
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
                console.log(categoryData.transactionType);
                $.ajax({
                    url: "/Settings/edit" + categoryData.transactionType + "CategoryAjax",
                    type: "POST",
                    dataType: "json",
                    cache: false,
                    data: data
                }).done(function (response) {
                    if (response > 0) {
                        console.log("Sukces!" + response);
                        if (response == true) {
                            let categoryName = $('#updateForm #name').val();
                            categoryData.toEdit.find('td.category').html(categoryName);
                            let blockedFunds = $('#updateForm #blockedFunds').val();
                            categoryData.toEdit.find('td.blockedFunds').html(blockedFunds);
                            $('#editModal').modal('hide');
                            $('#updateForm').trigger('reset');
                        }
                        else {
                            console.log("Lipa! Nie edytowano rekordu" + response);
                            console.dir(arguments);
                        }
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
        });
    }
});