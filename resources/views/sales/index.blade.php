<x-layout>
    <div class="w-full p-4 mb-3 bg-white rounded-lg md:p-6">


        <div id="sales-chart"></div>
    </div>
    <div class="w-full px-4 sm:px-6 lg:px-8 bg-white p-5 rounded-lg shadow-lg">
        <div class="flex flex-col sm:flex-row justify-between items-center mb-5">
            <h1 class="text-2xl font-bold mb-2 sm:mb-0">Sales List</h1>
            <p class="mb-2 sm:mb-0">Total Sales: {{ $sales->total() }}</p>
            <form method="GET" action="{{ route('sales.index') }}" class="flex w-full sm:w-auto">
                <input type="text" name="search" placeholder="Search..." value="{{ request('search') }}"
                    class="w-full sm:w-auto px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <button type="submit"
                    class="ml-2 px-4 py-2 bg-blue-700 text-white rounded-lg hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">Search</button>
            </form>
        </div>

        <div class="mb-2">
            This page provides a comprehensive overview of all sales transactions in the system. You can search for
            specific sales records, view detailed information such as total amount, discount applied, transaction
            number,
            sale time, and status. The table allows for sorting and quick access to actions like viewing receipts or
            processing refunds. Additionally, the sales chart visualizes trends over the past 30 days, helping you track
            performance and identify patterns. Use the search bar and available tools to efficiently manage and analyze
            sales data, ensuring smooth operations and better decision-making.
        </div>

        <div class="flex mb-5">
            <a href="{{ route('sales.create') }}" class="w-full text-lg text-center btn sm:w-auto">Add Sales
            </a>
        </div>

        <div class="sm:hidden">
            @if ($sales->isEmpty())
                <p class="text-center py-5 text-gray-500">Wow, this table is empty.</p>
            @else
                @foreach ($sales as $sale)
                    <a href="{{ route('sales.show', $sale->id) }}"
                        class="block bg-white shadow-md rounded-lg mb-4 p-4 hover:bg-gray-100 transition duration-150 ease-in-out">
                        <p><strong>ID:</strong> {{ $sale->id }}</p>
                        <p><strong>User:</strong> {{ $sale->user ? $sale->user->name : 'N/A' }}</p>
                        <p><strong>Total Amount:</strong> ₱{{ number_format($sale->total_amount, 2) }}</p>
                        <p><strong>Discount:</strong>
                            {{ $sale->discount_percentage > 0 ? $sale->discount_percentage . '%' : 'No Discount' }}</p>
                        <p><strong>Transaction Number:</strong> {{ $sale->transaction_key }}</p>
                        <p><strong>Sale Time:</strong> {{ $sale->created_at }}</p>
                        <p><strong>Status:</strong> {{ $sale->status ?? 'Completed' }}</p>
                        <div class="mt-2">
                            <a href="{{ route('pos.receipt', $sale->id) }}" class="text-blue-600 hover:underline">View
                                Receipt</a>
                            @if ($sale->status !== 'refunded')
                                <form action="{{ route('sales.refund', $sale->id) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-red-600 hover:underline">Refund</button>
                                </form>
                            @endif
                        </div>
                    </a>
                @endforeach
            @endif
        </div>

        <div class="hidden sm:block relative overflow-x-auto sm:rounded-lg">
            @if ($sales->isEmpty())
                <p class="text-center py-5 text-gray-500">Wow, this table is empty.</p>
            @else
                <table class="w-full text-left rtl:text-right">
                    <thead class="uppercase">
                        <tr>
                            <th scope="col" class="px-6 py-3">ID</th>
                            <th scope="col" class="px-6 py-3">Total Amount</th>
                            <th scope="col" class="px-6 py-3">Discount</th>
                            <th scope="col" class="px-6 py-3">Transaction Number</th>
                            <th scope="col" class="px-6 py-3">Sale Time</th>
                            <th scope="col" class="px-6 py-3">Status</th>
                            <th scope="col" class="px-6 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($sales as $sale)
                            <tr class="hover:bg-green-300 cursor-pointer transition duration-150 ease-in-out"
                                onclick="window.location='{{ route('sales.show', $sale->id) }}'">
                                <td class="px-6 py-4">{{ $sale->id }}</td>
                                <td class="px-6 py-4">₱{{ number_format($sale->total_amount, 2) }}</td>
                                <td class="px-6 py-4">
                                    {{ $sale->discount_percentage > 0 ? $sale->discount_percentage . '%' : 'No Discount' }}
                                </td>
                                <td class="px-6 py-4">{{ $sale->transaction_key }}</td>
                                <td class="px-6 py-4">{{ $sale->created_at->format('F j, Y h:i A') }}</td>
                                <td class="px-6 py-4">{{ $sale->status ?? 'Completed' }}</td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('pos.receipt', $sale->id) }}"
                                        class="text-blue-600 hover:underline">View Receipt</a>
                                    @if ($sale->status !== 'refunded')
                                        <form action="{{ route('sales.refund', $sale->id) }}" method="POST"
                                            class="inline">
                                            @csrf
                                            <button type="submit" class="text-red-600 hover:underline">Refund</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <div class="mt-4">
            {{ $sales->appends(['search' => request('search')])->links('vendor.pagination.tailwind') }}
        </div>

        <div class="mb-4">
            <label for="chart-selector" class="block text-sm font-medium text-gray-700">Select Chart View</label>
            <select id="chart-selector"
                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                <option value="daily">Daily Sales</option>
                <option value="weekly">Weekly Sales</option>
                <option value="monthly">Monthly Sales</option>
            </select>
        </div>

        <!-- Daily Chart -->
        <div id="chart-container">
            <div id="daily-chart" class="chart-container" style="display: block;"></div>
            <div id="weekly-chart" class="chart-container" style="display: none;"></div>
            <div id="monthly-chart" class="chart-container" style="display: none;"></div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script>
        // Data for the charts
        const dailyChartData = @json($dailyChartData);
        const weeklyChartData = @json($weeklyChartData);
        const monthlyChartData = @json($monthlyChartData);

        // Variables to store chart instances
        let dailyChart, weeklyChart, monthlyChart;

        // Function to render the daily chart
        function renderDailyChart() {
            const chartElement = document.querySelector("#daily-chart");
            if (!chartElement) return;

            const chartData = dailyChartData.map(item => item.total_amount);
            const categories = dailyChartData.map(item => item.date);

            const chartOptions = {
                chart: {
                    type: 'line',
                    height: 350,
                    width: '100%',
                    toolbar: {
                        show: false
                    }
                },
                series: [{
                    name: 'Total Amount',
                    data: chartData
                }],
                xaxis: {
                    categories: categories,
                    title: {
                        text: 'Date'
                    },
                    labels: {
                        rotate: -45,
                        rotateAlways: true,
                        trim: true
                    }
                },
                yaxis: {
                    title: {
                        text: 'Total Amount'
                    },
                    labels: {
                        formatter: function(value) {
                            return '₱' + value.toFixed(2);
                        }
                    }
                },
                title: {
                    text: 'Sales Per Day',
                    align: 'center'
                },
                tooltip: {
                    x: {
                        format: 'dd/MM/yyyy'
                    },
                    y: {
                        formatter: function(value) {
                            return '₱' + value.toFixed(2);
                        }
                    }
                }
            };

            if (dailyChart) dailyChart.destroy();
            dailyChart = new ApexCharts(chartElement, chartOptions);
            dailyChart.render();
        }

        // Function to render the weekly chart
        function renderWeeklyChart() {
            const chartElement = document.querySelector("#weekly-chart");
            if (!chartElement) return;

            const chartData = weeklyChartData.map(item => item.total_amount);
            const categories = weeklyChartData.map(item => `Week ${item.week}`);

            const chartOptions = {
                chart: {
                    type: 'line',
                    height: 350,
                    width: '100%',
                    toolbar: {
                        show: false
                    }
                },
                series: [{
                    name: 'Total Amount',
                    data: chartData
                }],
                xaxis: {
                    categories: categories,
                    title: {
                        text: 'Week'
                    }
                },
                yaxis: {
                    title: {
                        text: 'Total Amount'
                    },
                    labels: {
                        formatter: function(value) {
                            return '₱' + value.toFixed(2);
                        }
                    }
                },
                title: {
                    text: 'Sales Per Week',
                    align: 'center'
                },
                tooltip: {
                    y: {
                        formatter: function(value) {
                            return '₱' + value.toFixed(2);
                        }
                    }
                }
            };

            if (weeklyChart) weeklyChart.destroy();
            weeklyChart = new ApexCharts(chartElement, chartOptions);
            weeklyChart.render();
        }

        // Function to render the monthly chart
        function renderMonthlyChart() {
            const chartElement = document.querySelector("#monthly-chart");
            if (!chartElement) return;

            const chartData = monthlyChartData.map(item => item.total_amount);
            const categories = monthlyChartData.map(item => `Month ${item.month}`);

            const chartOptions = {
                chart: {
                    type: 'line',
                    height: 350,
                    width: '100%',
                    toolbar: {
                        show: false
                    }
                },
                series: [{
                    name: 'Total Amount',
                    data: chartData
                }],
                xaxis: {
                    categories: categories,
                    title: {
                        text: 'Month'
                    }
                },
                yaxis: {
                    title: {
                        text: 'Total Amount'
                    },
                    labels: {
                        formatter: function(value) {
                            return '₱' + value.toFixed(2);
                        }
                    }
                },
                title: {
                    text: 'Sales Per Month',
                    align: 'center'
                },
                tooltip: {
                    y: {
                        formatter: function(value) {
                            return '₱' + value.toFixed(2);
                        }
                    }
                }
            };

            if (monthlyChart) monthlyChart.destroy();
            monthlyChart = new ApexCharts(chartElement, chartOptions);
            monthlyChart.render();
        }

        // Function to handle chart switching
        function switchChart(selectedView) {
            // Remove display logic, use ApexCharts built-in show/hide
            const chartContainers = document.querySelectorAll('.chart-container');
            chartContainers.forEach(container => {
                container.style.display = 'none';
            });

            const selectedChart = document.getElementById(`${selectedView}-chart`);
            if (selectedChart) {
                selectedChart.style.display = 'block';

                // Redraw the selected chart
                switch (selectedView) {
                    case 'daily':
                        if (dailyChart) dailyChart.render();
                        break;
                    case 'weekly':
                        if (weeklyChart) weeklyChart.render();
                        break;
                    case 'monthly':
                        if (monthlyChart) monthlyChart.render();
                        break;
                }
            }
        }

        // Event listener for chart selector
        document.addEventListener('DOMContentLoaded', () => {
            const chartSelector = document.getElementById('chart-selector');

            if (chartSelector) {
                chartSelector.addEventListener('change', function() {
                    switchChart(this.value);
                });

                // Render all charts
                renderDailyChart();
                renderWeeklyChart();
                renderMonthlyChart();

                // Ensure initial chart is visible
                switchChart('daily');
            }
        });
    </script>



</x-layout>
