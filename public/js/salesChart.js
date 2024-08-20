document.addEventListener('DOMContentLoaded', function () {
    const periodSelector = document.getElementById('period');
    let chart;

    function initChart() {
        const options = {
            chart: {
                height: "100%",
                maxWidth: "100%",
                type: "line",
                fontFamily: "Inter, sans-serif",
                dropShadow: {
                    enabled: false
                },
                toolbar: {
                    show: false
                },
            },
            tooltip: {
                enabled: true,
                x: {
                    show: false
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                width: 6,
                curve: 'smooth'
            },
            grid: {
                show: true,
                strokeDashArray: 4,
                padding: {
                    left: 2,
                    right: 2,
                    top: -26
                },
            },
            series: [{
                name: "Sales",
                data: salesSeries,
                color: "#009933",
            }],
            legend: {
                show: false
            },
            xaxis: {
                categories: categories,
                labels: {
                    show: true,
                    style: {
                        fontFamily: "Inter, sans-serif",
                        cssClass: 'text-xs font-normal fill-gray-500 dark:fill-gray-400'
                    }
                },
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                },
            },
            yaxis: {
                labels: {
                    show: true,
                    formatter: function (value) {
                        return '₱' + value.toLocaleString();
                    },
                    style: {
                        fontFamily: "Inter, sans-serif",
                        cssClass: 'text-xs font-normal fill-gray-500 dark:fill-gray-400'
                    }
                }
            },
        };

        if (document.getElementById("line-chart") && typeof ApexCharts !== 'undefined') {
            chart = new ApexCharts(document.getElementById("line-chart"), options);
            chart.render();
        }
    }

    initChart();

    periodSelector.addEventListener('change', function () {
        const selectedPeriod = this.value;
        window.location.href = `${dashboardRoute}?period=${selectedPeriod}`;
    });
});
