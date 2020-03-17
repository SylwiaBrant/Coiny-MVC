$(document).ready(function () {
    $.ajax({
        url: "/Home/getIncomeCategoriesSumsAjax",
        method: "POST",
        dataType: "json"
    }).done(function (response) {
        incomeCategoriesSums = response['incomeCategoriesSums'];
        expenseCategoriesSums = response['expenseCategoriesSums'];
        expenseLimits = response['expenseLimits'];
        paymentLimits = response['paymentLimits'];
        renderProgressBars(expenseLimits, paymentLimits);
        setPieChartData(incomeCategoriesSums, '#incomesPie', 'Przychody wg. kategorii');
        setPieChartData(expenseCategoriesSums, '#expensesPie', 'Wydatki wg. kategorii');
    }).fail(function (jqXHR, textStatus) {
        /*console.log("No i klops!" + jqXHR + textStatus);
        console.dir(arguments);*/
    });

    function setPieChartData(sums, canvas, title) {
        var labels = sums.map(function (e) {
            return e.name;
        });
        var data = sums.map(function (e) {
            return e.money;
        });
        var pieConfig = {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: [
                        'rgba(92, 104, 152, 0.7)',
                        'rgba(243, 210, 172, 0.7)',
                        'rgba(204, 119, 147, 0.7)',
                        'rgba(129, 208, 233, 0.7)',
                        'rgba(0, 48, 65, 0.7)',
                        'rgba(142, 38, 72, 0.7)',
                        'rgba(224, 138, 123, 0.7)',
                        'rgba(179, 218, 97, 0.7)',
                    ],
                    borderColor: [
                        'rgba(255, 255, 255, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                title: {
                    display: true,
                    fontFamily: 'Helvetica',
                    fontSize: 16,
                    text: title,
                    fontColor: '#212529'
                },
                legend: {
                    display: true,
                    position: 'bottom',
                    align: 'left',
                    labels: {
                        boxWidth: 15
                    }
                }
            }
        };
        renderChart(canvas, pieConfig);
    }
    function renderChart(canvas, config) {
        let ctx = $(canvas)[0].getContext('2d');
        let chart = new Chart(ctx, config);
    }

    function renderProgressBars(expenseLimits, paymentLimits) {
        $.each(expenseLimits, function (i, limit) {
            var row = '';
            if (limit.percentage > 100) {
                limit.percentage = 100;
            }
            row = "<h3>" + limit.name + " - limit " + limit.blocked_funds + " zł</h3>" +
                "<div class='limitedFunds'>" +
                "<span class='moneySpent text-center' style='width: " + limit.percentage + "%'>" + limit.money + " zł</span>" +
                "</div>";
            $('#limitedFunds').append(row);
        });
        $.each(paymentLimits, function (i, limit) {
            var row = '';
            if (limit.percentage > 100) {
                limit.percentage = 100;
            }
            row = "<h3>" + limit.name + " - limit " + limit.blocked_funds + " zł</h3>" +
                "<div class='limitedFunds'>" +
                "<span class='moneySpent text-center' style='width: " + limit.percentage + "%'>" + limit.money + " zł</span>" +
                "</div>";
            $('#limitedFunds').append(row);
        });
    }
});