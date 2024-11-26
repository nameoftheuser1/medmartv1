{{--  This is the dashboard page.
//    its already sanitized
//
--}}

{{--  dd($fastMovingProducts); --}}
<x-layout>
    <h1 class="mb-4 text-5xl font-bold">Dashboard</h1>
    <div class="w-full p-4 mb-3 bg-white rounded-lg md:p-6">
        <div class="flex justify-between mb-5">
            <div class="grid grid-cols-2 gap-4">
                <div class="flex gap-4">
                    <div>
                        <h5 class="inline-flex items-center mb-2 font-normal leading-none text-gray-500">
                            Total Sales
                        </h5>
                        <p class="text-2xl font-bold leading-none text-gray-900">
                            {{ is_numeric($totalSales) ? number_format($totalSales) : 0 }}</p>
                    </div>
                    <div>
                        <h5 class="inline-flex items-center mb-2 font-normal leading-none text-gray-500">
                            Total Sales Today
                        </h5>
                        <p class="text-2xl font-bold leading-none text-gray-900">
                            {{ is_numeric($totalSalesToday) ? number_format($totalSalesToday) : 0 }}</p>
                    </div>
                </div>
            </div>
        </div>
        <div id="line-chart"></div>
    </div>

    <div class="w-full p-6 mb-6 bg-white rounded-lg shadow-md">
        <div class="flex justify-between gap-6">

            <div id="sales-chart" class="flex-1 p-4 bg-gray-50 rounded-lg shadow-sm">
                <h5 class="inline-flex items-center mb-2 font-normal leading-none text-gray-500">
                    Sales
                </h5>
                <!-- Sales chart will be rendered here -->
            </div>

        </div>
    </div>


    <div class="flex flex-col justify-between gap-3 mb-4 md:flex-row">
        <div class="w-full p-4 mx-auto bg-white rounded-lg card md:mb-0 md:w-1/2">
            <form id="inventory-form" method="GET" action="{{ e(route('dashboard')) }}">
                <h2 class="mb-2 font-mono text-xl font-bold">Inventory Level</h2>
                <select id="inventory-type-selector" name="inventory-type" class="items-end">
                    <option value="highest" {{ e($currentInventoryType) == 'highest' ? 'selected' : '' }}>Highest
                        Inventory</option>
                    <option value="lowest" {{ e($currentInventoryType) == 'lowest' ? 'selected' : '' }}>Lowest Inventory
                    </option>
                </select>
                <input type="hidden" id="period" name="period" value="{{ e($currentPeriod) }}">
            </form>
            <div id="column-chart"></div>
        </div>

        <div class="bg-white rounded-lg p-4 md:w-1/2 max-h-[430] min-h-[430] overflow-y-scroll">
            <h2 class="mb-2 font-mono text-xl font-bold">Products About to Expire</h2>
            <p class="mb-5 text-sm text-gray-500">Here showing the products that are about to expire in 3 months</p>
            @if ($expiringBatches->isEmpty())
                <p class="text-gray-200">No products are about to expire within the next 3 months.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 border">Product Name</th>
                                <th class="px-4 py-2 border">Batch Number</th>
                                <th class="px-4 py-2 border">Expiration Date</th>
                                <th class="px-4 py-2 border">Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($expiringBatches as $batch)
                                @php
                                    $monthsToExpiry = now()->diffInMonths($batch->expiration_date);
                                    $rowClass = '';
                                    if ($monthsToExpiry <= 1) {
                                        $rowClass = 'bg-red-100';
                                    } elseif ($monthsToExpiry <= 2) {
                                        $rowClass = 'bg-red-100';
                                    }
                                @endphp
                                <tr class="{{ $rowClass }}">
                                    <td class="px-4 py-2 border">{{ e($batch->product->product_name) }}</td>
                                    <td class="px-4 py-2 text-center border">{{ e($batch->batch_number) }}</td>
                                    <td class="px-4 py-2 text-center border">
                                        {{ e($batch->expiration_date->format('Y-m-d')) }}
                                    </td>
                                    <td class="px-4 py-2 text-center border">
                                        {{ e($batch->inventories->sum('quantity')) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ e($expiringBatches->appends(['search' => request('search')])->links('vendor.pagination.tailwind')) }}
                </div>
            @endif
        </div>
    </div>
    <div class="grid grid-cols-1 gap-6 mb-6 sm:grid-cols-2 lg:grid-cols-3">
        <div class="flex items-center p-6 bg-white rounded-lg card">
            <div class="flex items-center justify-center p-3 text-white bg-green-500 rounded-lg w-14 h-14">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>

            </div>
            <div class="ml-4">
                <p class="text-lg font-semibold">Sales Count:</p>
                <h1 class="text-3xl font-bold">{{ e($saleCount) }}</h1>
            </div>
        </div>
        <div class="flex items-center p-6 bg-white rounded-lg card">
            <div class="flex items-center justify-center p-3 text-white bg-green-500 rounded-lg w-14 h-14">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-lg font-semibold">Product Count:</p>
                <h1 class="text-3xl font-bold">{{ e($productCount) }}</h1>
            </div>
        </div>
        <div class="flex items-center p-6 bg-white rounded-lg card">
            <div class="flex items-center justify-center p-3 text-white bg-green-500 rounded-lg w-14 h-14">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-6 h-6">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                </svg>
            </div>
            <div class="ml-4">
                <p class="text-lg font-semibold">Supplier Count:</p>
                <h1 class="text-3xl font-bold">{{ e($supplierCount) }}</h1>
            </div>
        </div>
    </div>

    <script src="{{ asset('tailwindcharts\js\apexcharts.js') }}"></script>

    <script>
        // In your DashboardController's index method, prepare the chart data
        var historicalData = @json($salesSeries); // Historical sales data (last 11 months)
        var predictedData = @json($predictedSales); // Predicted sales data (next 3 months)
        var categories = @json(array_merge($categories, $predictedDates)); // Merge historical and predicted dates

        // Merge predictedData into historicalData
        var combinedData = historicalData.concat(predictedData);

        var dashboardRoute = "{{ e(route('dashboard')) }}";
    </script>

    <script>
        // Color definitions
        const historicalColor = '#1E90FF'; // More distinct blue for historical data
        const predictedColor = '#FF6347'; // Bright red for predicted data

        // Prepare chart data
        var historicalData = @json($salesSeries); // Historical sales data (last 11 months)
        var predictedData = @json($predictedSales); // Predicted sales data (next 3 months)
        var categories = @json(array_merge($categories, $predictedDates)); // Merge historical and predicted dates

        // Merge predictedData into historicalData
        var combinedData = historicalData.concat(predictedData);

        // Function to generate color array based on data type
        function generateColorArray(combinedData) {
            const historicalLength = combinedData.length - 3; // Last 3 months predicted
            return combinedData.map((_, index) =>
                index < historicalLength ? historicalColor : predictedColor
            );
        }

        // Function to generate color stops with gradient
        function generateColorStops(combinedData) {
            const historicalLength = combinedData.length - 3;
            return combinedData.map((_, index) => {
                const baseColor = index < historicalLength ? historicalColor : predictedColor;
                return {
                    color: baseColor,
                    opacity: index < historicalLength ? 0.7 : 0.4
                };
            });
        }

        // Chart configuration options
        const options = {
            chart: {
                height: 400,
                type: 'line',
                zoom: {
                    enabled: true
                },
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
            colors: generateColorArray(combinedData),
            stroke: {
                width: 3,
                colors: generateColorArray(combinedData),
                dashArray: combinedData.map((_, index) =>
                    index < combinedData.length - 3 ? 0 : 5 // Solid for historical, dashed for predicted
                )
            },
            xaxis: {
                categories: categories,
                title: {
                    text: 'Months',
                    style: {
                        fontWeight: 600
                    }
                },
                labels: {
                    rotate: -45,
                    rotateAlways: false
                },
                // Add vertical line between historical and predicted data
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
                    style: {
                        fontWeight: 600
                    }
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
                    x: categories[combinedData.length - 3],
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
                formatter: function(value, {
                    series,
                    seriesIndex,
                    dataPointIndex,
                    w
                }) {
                    const historicalLength = combinedData.length - 3;
                    const type = dataPointIndex < historicalLength ? 'Historical' : 'Predicted';
                    return `${type} Sales: ₱${value.toFixed(2)}`;
                }
            },
            markers: {
                size: 5,
                colors: generateColorArray(combinedData),
                strokeColors: generateColorArray(combinedData),
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
                    colorStops: generateColorStops(combinedData)
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
            legend: {
                show: false // Kept false as in original script
            },
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        height: 250
                    }
                }
            }],
        };

        // Render the chart
        const chart = new ApexCharts(document.querySelector("#sales-chart"), options);
        chart.render();
    </script>




    <script>
        var inventoryBatches = @json($inventoryBatches);
        var currentInventoryType = "{{ e($currentInventoryType) }}";
        var chartTitle = currentInventoryType === 'highest' ? 'Highest Inventory' : 'Lowest Inventory';
    </script>

    <script src="{{ asset('js/inventoryChart.js') }}"></script>
    <script src="{{ asset('jquery/jquery-3.7.1.min.js') }}"></script>

    <div class="w-full p-4 mb-3 bg-white rounded-lg">
        <h2 class="mb-2 font-mono text-xl font-bold">Fast Moving Products</h2>
        <div id="total-sales-info"></div>
        <div id="fast-moving-products-chart"></div>
    </div>

    <script>
        var salesSeries = @json($salesSeries);
        var categories = @json($categories);
        var fastMovingProducts = @json($fastMovingProducts);

        document.addEventListener("DOMContentLoaded", function() {
            // Extract product names, quantities, and prices
            var fastMovingProductNames = fastMovingProducts.map(function(product) {
                return product.product_name;
            });
            var fastMovingProductQuantities = fastMovingProducts.map(function(product) {
                return product.total_quantity;
            });
            var fastMovingProductPrices = fastMovingProducts.map(function(product) {
                return product.price;
            });

            // Calculate total sales for each product
            var productSales = fastMovingProducts.map(function(product) {
                return product.total_quantity * product.price;
            });

            // Chart options
            var options = {
                series: [{
                    name: 'Quantity Sold',
                    data: fastMovingProductQuantities
                }],
                chart: {
                    type: 'bar',
                    height: 350
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        endingShape: 'rounded'
                    },
                },
                dataLabels: {
                    enabled: true,
                    formatter: function(val, opts) {
                        // Get the index of the current product
                        var productIndex = opts.dataPointIndex;
                        // Calculate total sales for that product
                        var totalSalesForProduct = productSales[productIndex];
                        // Format and return total sales with currency symbol
                        return '₱' + totalSalesForProduct.toFixed(2);
                    },
                    style: {
                        fontSize: '12px',
                        colors: ['#000']
                    }
                },
                xaxis: {
                    categories: fastMovingProductNames,
                },
                yaxis: {
                    title: {
                        text: 'Quantity Sold'
                    }
                },
                fill: {
                    opacity: 1
                },
            };

            // Render the chart
            var chart = new ApexCharts(document.querySelector("#fast-moving-products-chart"), options);
            chart.render();
        });
    </script>




</x-layout>
