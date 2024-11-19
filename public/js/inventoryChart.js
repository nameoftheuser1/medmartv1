document.addEventListener("DOMContentLoaded", function () {
    let chart;

    function initChart(inventoryBatches, currentInventoryType) {
        const chartTitle =
            currentInventoryType === "highest"
                ? "Highest Inventory"
                : "Lowest Inventory";

        if (currentInventoryType === "highest") {
            inventoryBatches.sort((a, b) => b.quantity - a.quantity);
        } else {
            inventoryBatches.sort((a, b) => a.quantity - b.quantity);
        }

        const options = {
            colors: ["#1A56DB", "#FDBA8C"],
            series: [
                {
                    name: chartTitle,
                    color: "#1A56DB",
                    data: inventoryBatches.map((batch) => ({
                        x: `${batch.product_name} (${batch.batch_number})`, // Display product name along with batch number
                        y: batch.quantity,
                        id: batch.batch_id, // Store the batch_id for later use
                    })),
                },
            ],
            chart: {
                type: "bar",
                height: "320px",
                fontFamily: "Inter, sans-serif",
                toolbar: {
                    show: false,
                },
                events: {
                    dataPointSelection: function(event, chartContext, config) {
                        // Get the batch_id of the clicked bar
                        const batchId = config.w.config.series[config.seriesIndex].data[config.dataPointIndex].id;

                        if (batchId) {
                            const url = `/product_batches/${batchId}`; // Use the batchId to redirect
                            window.location.href = url;
                        } else {
                            console.error("Batch ID is undefined");
                        }
                    }
                }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: "70%",
                    borderRadiusApplication: "end",
                    borderRadius: 8,
                },
            },
            tooltip: {
                shared: true,
                intersect: false,
                style: {
                    fontFamily: "Inter, sans-serif",
                },
                x: {
                    formatter: function(val) {
                        return val; // Display product name and batch number on hover
                    }
                },
                y: {
                    formatter: function(val) {
                        return `Quantity: ${Math.round(val)}`;
                    }
                },
            },
            states: {
                hover: {
                    filter: {
                        type: "darken",
                        value: 1,
                    },
                },
            },
            stroke: {
                show: true,
                width: 0,
                colors: ["transparent"],
            },
            grid: {
                show: false,
                strokeDashArray: 4,
                padding: {
                    left: 2,
                    right: 2,
                    top: -14,
                },
            },
            dataLabels: {
                enabled: false,
            },
            legend: {
                show: false,
            },
            xaxis: {
                floating: false,
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
                show: true,
                labels: {
                    style: {
                        fontFamily: "Inter, sans-serif",
                        cssClass:
                            "text-xs font-normal fill-gray-500 dark:fill-gray-400",
                    },
                    formatter: function (val) {
                        return Math.round(val);
                    },
                },
            },
            fill: {
                opacity: 1,
            },
        };

        if (
            document.getElementById("column-chart") &&
            typeof ApexCharts !== "undefined"
        ) {
            if (chart) {
                chart.updateOptions(options);
            } else {
                chart = new ApexCharts(
                    document.getElementById("column-chart"),
                    options
                );
                chart.render();
            }
        }
    }

    initChart(inventoryBatches, currentInventoryType);

    const inventoryTypeSelector = document.querySelector(
        'select[name="inventory-type"]'
    );
    if (inventoryTypeSelector) {
        inventoryTypeSelector.addEventListener("change", function () {
            const newInventoryType = this.value;
            const currentPeriod = document.getElementById("period").value;

            $.ajax({
                url: dashboardRoute,
                method: "GET",
                data: {
                    "inventory-type": newInventoryType,
                    period: currentPeriod,
                },
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                },
                success: function (response) {
                    initChart(response.inventoryBatches, newInventoryType);
                },
                error: function (xhr, status, error) {
                    console.error("Error fetching data:", error);
                },
            });
        });
    }
});
