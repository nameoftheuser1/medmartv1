<x-layout>
    <h1 class="mb-4 text-5xl font-bold">Dashboard</h1>
    <div class="w-full bg-white rounded-lg shadow p-4 md:p-6 mb-3">
        <div class="flex justify-between mb-5">
            <div class="grid gap-4 grid-cols-2">
                <div>
                    <h5 class="inline-flex items-center text-gray-500 leading-none font-normal mb-2">
                        Total Sales
                        <!-- Tooltip SVG and content -->
                    </h5>
                    <p class="text-gray-900 text-2xl leading-none font-bold">{{ $totalSales }}</p>
                </div>
                <div>
                    <h5 class="inline-flex items-center text-gray-500 leading-none font-normal mb-2">
                        Total Sales Today
                        <!-- Tooltip SVG and content -->
                    </h5>
                    <p class="text-gray-900 text-2xl leading-none font-bold">{{ $totalSalesToday }}</p>
                </div>
            </div>
            <div>
                <div class="mb-4">
                    <label for="period" class="block text-sm font-medium text-gray-700">Select Period:</label>
                    <select id="period" name="period"
                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option value="weekly" {{ $currentPeriod == 'weekly' ? 'selected' : '' }}>Weekly</option>
                        <option value="monthly" {{ $currentPeriod == 'monthly' ? 'selected' : '' }}>Monthly</option>
                        <option value="yearly" {{ $currentPeriod == 'yearly' ? 'selected' : '' }}>Yearly</option>
                    </select>
                </div>
            </div>
        </div>
        <div id="line-chart"></div>
        <div class="grid grid-cols-1 items-center border-gray-200 border-t justify-between mt-2.5">
        </div>
    </div>
    <div class="flex flex-col md:flex-row justify-between mb-6 gap-3">
        <div class="card p-4 bg-white shadow rounded mx-auto md:mb-0 md:w-1/2   ">
            <div id="column-chart"></div>
            <select name="inventory-type" onchange="this.form.submit()">
                <option value="highest" {{ $currentInventoryType == 'highest' ? 'selected' : '' }}>Highest Inventory
                </option>
                <option value="lowest" {{ $currentInventoryType == 'lowest' ? 'selected' : '' }}>Lowest Inventory
                </option>
            </select>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 md:w-1/2 max-h-96 min-h-96 overflow-y-scroll">
            <h2 class="text-xl font-bold mb-2 font-mono">Products About to Expire</h2>
            <p class="mb-5 text-gray-500 text-sm">Here showing the products that are about to expire in 30 days</p>
            @if ($expiringBatches->isEmpty())
                <p class="text-gray-200">No products are about to expire within the next 30 days.</p>
            @else
                <div class="overflow-x-auto ">
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
                                <tr>
                                    <td class="px-4 py-2 border">{{ $batch->product->product_name }}</td>
                                    <td class="px-4 py-2 border text-center">{{ $batch->batch_number }}</td>
                                    <td class="px-4 py-2 border text-center">
                                        {{ $batch->expiration_date->format('Y-m-d') }}
                                    </td>
                                    <td class="px-4 py-2 border text-center">{{ $batch->inventories->sum('quantity') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $expiringBatches->appends(['search' => request('search')])->links('vendor.pagination.tailwind') }}
                </div>
            @endif
        </div>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        <div class="card p-4 bg-white shadow rounded">
            <p class="text-lg font-semibold">Product Count:</p>
            <h1 class="text-3xl font-bold text-center mt-2">
                {{ $productCount }}
            </h1>
        </div>
        <div class="card p-4 bg-white shadow rounded flex">
            <p
                class="text-lg font-semibold h-full bg-green-500 p-2 rounded-lg flex items-center justify-center text-white w-1/4">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-11">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                </svg>
            </p>
            <div class="p-2">
                <p>Supplier Count:</p>
                <h1 class="text-3xl font-bold text-center mt-2">
                    {{ $supplierCount }}
                </h1>
            </div>
        </div>

    </div>
    <script src="{{ asset('tailwindcharts\js\apexcharts.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
                        width: 6
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
                        data: @json($salesSeries),
                        color: "#009933",
                    }],
                    legend: {
                        show: false
                    },
                    stroke: {
                        curve: 'smooth'
                    },
                    xaxis: {
                        categories: @json($categories),
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
                            formatter: function(value) {
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

            periodSelector.addEventListener('change', function() {
                const selectedPeriod = this.value;
                window.location.href = `{{ route('dashboard') }}?period=${selectedPeriod}`;
            });
        });
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const inventoryBatches = @json($inventoryBatches);

            const options = {
                colors: ["#1A56DB", "#FDBA8C"],
                series: [{
                    name: "{{ $currentInventoryType === 'highest' ? 'Highest Inventory' : 'Lowest Inventory' }}",
                    color: "#1A56DB",
                    data: inventoryBatches.map(batch => ({
                        x: batch.batch_number,
                        y: batch.quantity
                    })),
                }],
                chart: {
                    type: "bar",
                    height: "320px",
                    fontFamily: "Inter, sans-serif",
                    toolbar: {
                        show: false,
                    },
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
                        top: -14
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
                            cssClass: 'text-xs font-normal fill-gray-500 dark:fill-gray-400'
                        }
                    },
                    axisBorder: {
                        show: false,
                    },
                    axisTicks: {
                        show: false,
                    },
                },
                yaxis: {
                    show: false,
                },
                fill: {
                    opacity: 1,
                },
            };

            if (document.getElementById("column-chart") && typeof ApexCharts !== 'undefined') {
                const chart = new ApexCharts(document.getElementById("column-chart"), options);
                chart.render();
            }
        });
    </script>




</x-layout>
