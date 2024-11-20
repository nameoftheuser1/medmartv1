document.addEventListener("DOMContentLoaded", function () {
    const periodSelector = document.getElementById("period");
    let salesChart, predictedSalesChart;

    function initSalesChart(salesSeries, categories) {
        const salesOptions = {
            chart: {
                height: "100%",
                maxWidth: "100%",
                type: "line",
                fontFamily: "Inter, sans-serif",
                dropShadow: {
                    enabled: false,
                },
                toolbar: {
                    show: false,
                },
            },
            tooltip: {
                enabled: true,
                x: {
                    show: false,
                },
            },
            dataLabels: {
                enabled: false,
            },
            stroke: {
                width: 6,
                curve: "smooth",
            },
            grid: {
                show: true,
                strokeDashArray: 4,
                padding: {
                    left: 2,
                    right: 2,
                    top: -26,
                },
            },
            series: [
                {
                    name: "Sales",
                    data: salesSeries,
                    color: "#009933",
                },
            ],
            legend: {
                show: true,
                position: 'top',
            },
            xaxis: {
                categories: categories,
                labels: {
                    show: true,
                    style: {
                        fontFamily: "Inter, sans-serif",
                        cssClass: "text-xs font-normal fill-gray-500 dark:fill-gray-400",
                    },
                },
                axisBorder: {
                    show: false,
                },
                axisTicks: {
                    show: false,
                },
            },
            yaxis: {
                labels: {
                    show: true,
                    formatter: function (value) {
                        return "₱" + value.toLocaleString();
                    },
                    style: {
                        fontFamily: "Inter, sans-serif",
                        cssClass: "text-xs font-normal fill-gray-500 dark:fill-gray-400",
                    },
                },
            },
            responsive: [{
                breakpoint: 600,
                options: {
                    chart: {
                        width: "100%",
                    },
                },
            }],
        };

        if (document.getElementById("sales-chart") && typeof ApexCharts !== "undefined") {
            if (salesChart) {
                salesChart.destroy();
            }
            salesChart = new ApexCharts(document.getElementById("sales-chart"), salesOptions);
            salesChart.render();
        }
    }

    function initPredictedSalesChart(predictedSales, predictedDates) {
        const predictedSalesOptions = {
            chart: {
                height: "100%",
                maxWidth: "100%",
                type: "line",
                fontFamily: "Inter, sans-serif",
                dropShadow: {
                    enabled: false,
                },
                toolbar: {
                    show: false,
                },
            },
            tooltip: {
                enabled: true,
                x: {
                    show: false,
                },
            },
            dataLabels: {
                enabled: false,
            },
            stroke: {
                width: 6,
                curve: "smooth",
            },
            grid: {
                show: true,
                strokeDashArray: 4,
                padding: {
                    left: 2,
                    right: 2,
                    top: -26,
                },
            },
            series: [
                {
                    name: "Predicted Sales",
                    data: predictedSales,
                    color: "#FF6600", // Change color for predicted sales
                    dashArray: 5, // Optional: dashed line for prediction
                }
            ],
            legend: {
                show: true,
                position: 'top',
            },
            xaxis: {
                categories: predictedDates,
                labels: {
                    show: true,
                    style: {
                        fontFamily: "Inter, sans-serif",
                        cssClass: "text-xs font-normal fill-gray-500 dark:fill-gray-400",
                    },
                },
                axisBorder: {
                    show: false,
                },
                axisTicks: {
                    show: false,
                },
            },
            yaxis: {
                labels: {
                    show: true,
                    formatter: function (value) {
                        return "₱" + value.toLocaleString();
                    },
                    style: {
                        fontFamily: "Inter, sans-serif",
                        cssClass: "text-xs font-normal fill-gray-500 dark:fill-gray-400",
                    },
                },
            },
            responsive: [{
                breakpoint: 600,
                options: {
                    chart: {
                        width: "100%",
                    },
                },
            }],
        };

        if (document.getElementById("predicted-sales-chart") && typeof ApexCharts !== "undefined") {
            if (predictedSalesChart) {
                predictedSalesChart.destroy();
            }
            predictedSalesChart = new ApexCharts(document.getElementById("predicted-sales-chart"), predictedSalesOptions);
            predictedSalesChart.render();
        }
    }

    periodSelector.addEventListener("change", function () {
        const selectedPeriod = this.value;

        $.ajax({
            url: dashboardRoute,
            method: "GET",
            data: {
                period: selectedPeriod,
            },
            success: function (response) {
                // Call the function to render sales chart
                initSalesChart(response.salesSeries, response.categories);

                // Call the function to render predicted sales chart
                initPredictedSalesChart(response.predictedSales, response.predictedDates);
            },
            error: function () {
                alert("An error occurred while fetching the data.");
            },
        });
    });

    // Initial chart rendering with the data
    initSalesChart(salesSeries, categories);
    initPredictedSalesChart(predictedSales, predictedDates);
});
