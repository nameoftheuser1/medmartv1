// salesChart.js

// Function to generate color array based on data type
function generateColorArray(combinedData, historicalLength) {
    return combinedData.map((_, index) =>
        index < historicalLength ? '#1E90FF' : '#FF6347'
    );
}

// Function to generate color stops with gradient
function generateColorStops(combinedData, historicalLength) {
    return combinedData.map((_, index) => {
        const baseColor = index < historicalLength ? '#1E90FF' : '#FF6347';
        return {
            color: baseColor,
            opacity: index < historicalLength ? 0.7 : 0.4
        };
    });
}

// Function to render the sales chart
function renderSalesChart(historicalData, predictedData, categories, predictedDates) {
    // Merge predictedData into historicalData
    const combinedData = historicalData.concat(predictedData);

    // Determine the split between historical and predicted data
    const historicalLength = combinedData.length - predictedData.length;

    // Chart configuration options
    const options = {
        chart: {
            height: 400,
            type: 'line',
            zoom: { enabled: true },
            toolbar: {
                show: true,
                tools: {
                    download: true,
                    selection: true,
                    zoom: true,
                    zoomin: true,
                    zoomout: true,
                    pan: true,
                    reset: true
                }
            }
        },
        series: [{
            name: 'Sales',
            data: combinedData
        }],
        colors: generateColorArray(combinedData, historicalLength),
        stroke: {
            width: 3,
            colors: generateColorArray(combinedData, historicalLength),
            dashArray: combinedData.map((_, index) =>
                index < historicalLength ? 0 : 5 // Solid for historical, dashed for predicted
            )
        },
        xaxis: {
            categories: [...categories, ...predictedDates],
            title: {
                text: 'Months',
                style: { fontWeight: 600 }
            },
            labels: {
                rotate: -45,
                rotateAlways: false
            },
            axisBorder: {
                show: true,
                color: '#775DD0',
                offsetX: 0,
                offsetY: 0
            }
        },
        yaxis: {
            title: {
                text: 'Sales Amount',
                style: { fontWeight: 600 }
            },
            labels: {
                formatter: function(value) {
                    return '₱' + value.toFixed(2);
                }
            }
        },
        title: {
            text: 'Historical vs Predicted Sales',
            align: 'center',
            style: {
                fontSize: '20px',
                fontWeight: 'bold',
                color: '#333'
            }
        },
        annotations: {
            xaxis: [{
                x: categories[combinedData.length - predictedData.length],
                strokeDashArray: 2,
                borderColor: '#775DD0',
                label: {
                    borderColor: '#775DD0',
                    style: {
                        color: '#fff',
                        background: '#775DD0'
                    },
                    text: 'Prediction Starts'
                }
            }]
        },
        tooltip: {
            enabled: true,
            shared: true,
            intersect: false,
            formatter: function(value, { series, seriesIndex, dataPointIndex, w }) {
                const type = dataPointIndex < historicalLength ? 'Historical' : 'Predicted';
                return `${type} Sales: ₱${value.toFixed(2)}`;
            }
        },
        markers: {
            size: 5,
            colors: generateColorArray(combinedData, historicalLength),
            strokeColors: generateColorArray(combinedData, historicalLength),
            hover: {
                size: 7,
                sizeOffset: 3
            }
        },
        fill: {
            type: 'gradient',
            gradient: {
                shade: 'light',
                type: 'horizontal',
                shadeIntensity: 0.5,
                stops: [0, 50, 100],
                colorStops: generateColorStops(combinedData, historicalLength)
            }
        },
        grid: {
            borderColor: '#e7e7e7',
            strokeDashArray: 5,
            row: {
                colors: ['#f3f3f3', 'transparent'],
                opacity: 0.5
            }
        },
        legend: { show: false },
        responsive: [{
            breakpoint: 480,
            options: { chart: { height: 250 } }
        }]
    };

    // Render the chart
    const chart = new ApexCharts(document.querySelector("#sales-chart"), options);
    chart.render();
}

// Export the renderSalesChart function for use in the Laravel Blade view
export { renderSalesChart };
