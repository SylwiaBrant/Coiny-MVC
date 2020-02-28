$(document).ready(function () {
    // Load expense categories from DB using AJAX 
    getExpenseCategories();
    getPaymentMethods();

    function getExpenseCategories() {
        $.ajax({
            url: "/Settings/getExpenseCategories",
            type: "GET",
            dataType: 'json'
        }).done(function (categories) {
            console.log(categories);
            displayCategories(categories, "#expenseCategory");
        }).fail(function (jqXHR, textStatus) {
            console.log("No i klops!" + jqXHR + textStatus);
            console.dir(arguments);
        });
    }

    function getPaymentMethods() {
        $.ajax({
            url: "/Settings/getPaymentMethods",
            type: "GET",
            dataType: 'json',
        }).done(function (categories) {
            displayCategories(categories, '#paymentMethod');
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
    $('#expenseForm').validate({
        rules: {
            money: {
                required: true,
                number: true,
                step: 0.01
            },
            date: {
                required: true,
                date: true,
            },
            category: {
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
                date: true
            }
        }
    });
    //Add expense to database using AJAX
    $('#addExpenseForm').on('submit', function (e) {
        e.preventDefault();
        var data = $('#addExpenseForm').serialize();
        console.log(data);
        $.ajax({
            url: "/Expenses/addExpense",
            type: "POST",
            dataType: 'json',
            data: data
        }).done(function (response) {
            if (response == true) {
                console.log("Sukces!" + response);
                $("#addExpenseForm")[0].reset();
                document.getElementById("addExpenseForm").reset();
            } else {
                console.log("No tak średnio bym powiedziała." + response);
                console.dir(arguments);
            }
        }).fail(function (jqXHR, textStatus) {
            console.log("No i klops!" + jqXHR + textStatus);
            console.dir(arguments);
        });
    });
});