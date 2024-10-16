document.addEventListener("DOMContentLoaded", function () {
    const periodSelector = document.getElementById("period");
    let chart;

    function initChart(
        salesSeries,
        categories,
        predictedSales,
        predictedCategories
    ) {
        const options = {
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
                width: [6, 4], // Change width for predicted series
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
                {
                    name: "Predicted Sales",
                    data: predictedSales,
                    color: "#ff9800", // Different color for predicted
                },
            ],
            legend: {
                show: true, // Enable legend
            },
            xaxis: {
                categories: [...categories, ...predictedCategories], // Merge categories
                labels: {
                    show: true,
                    style: {
                        fontFamily: "Inter, sans-serif",
                        cssClass:
                            "text-xs font-normal fill-gray-500 dark:fill-gray-400",
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
                        cssClass:
                            "text-xs font-normal fill-gray-500 dark:fill-gray-400",
                    },
                },
            },
        };

        if (
            document.getElementById("line-chart") &&
            typeof ApexCharts !== "undefined"
        ) {
            if (chart) {
                chart.destroy();
            }
            chart = new ApexCharts(
                document.getElementById("line-chart"),
                options
            );
            chart.render();
        }
    }

    initChart(salesSeries, categories);

    periodSelector.addEventListener("change", function () {
        const selectedPeriod = this.value;

        $.ajax({
            url: dashboardRoute,
            method: "GET",
            data: {
                period: selectedPeriod,
            },
            success: function (response) {
                initChart(
                    response.salesSeries,
                    response.categories,
                    response.predictedSales,
                    response.predictedCategories
                );
            },
            error: function () {
                alert("An error occurred while fetching the data.");
            },
        });
    });
});
