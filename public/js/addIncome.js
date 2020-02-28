$(document).ready(function () {
    // Load income categories from DB using AJAX 
    getIncomeCategories();

    function getIncomeCategories() {
        $.ajax({
            url: "/Settings/getIncomeCategories",
            type: "GET",
            dataType: 'json'
        }).done(function (categories) {
            console.log(categories);
            displayCategories(categories);
        }).fail(function (jqXHR, textStatus) {
            console.log("No i klops!" + jqXHR + textStatus);
            console.dir(arguments);
        });
    }

    function displayCategories(categories) {
        $.each(categories, function (index) {
            console.log(index);
            console.log(categories[index]);
            console.log(categories[index].name);
            var category = "<option>" + categories[index].name + "</option>";
            $('#category').append(category);
        });
    }
    // Validate form input using jQuery Validation Plugin 
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
            category: {
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
            }
        }
    });
    //Add income to database using AJAX
    $('#addIncomeForm').on('submit', function (e) {
        e.preventDefault();
        var data = $('#addIncomeForm').serialize();
        console.log(data);
        $.ajax({
            url: "/Incomes/addIncome",
            type: "POST",
            dataType: 'json',
            data: data
        }).done(function (response) {
            if (response == true)
                console.log("Sukces!" + response);
            else {
                console.log("No tak średnio bym powiedziała." + response);
                console.dir(arguments);
            }

        }).fail(function (jqXHR, textStatus) {
            console.log("No i klops!" + jqXHR + textStatus);
            console.dir(arguments);
        });
    });
});
