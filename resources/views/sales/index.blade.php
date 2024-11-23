<x-layout>
    <div class="w-full p-4 mb-3 bg-white rounded-lg md:p-6">
        <h2 class="mb-2 font-mono text-xl font-bold"></h2>
        <!-- Dropdown to select period with Tailwind CSS styling -->
        <div class="mb-4">
            <label for="period-select" class="block text-sm font-medium text-gray-700 mb-1">
                Select Period
            </label>
            <select
                id="period-select"
                class="w-full px-3 py-2 border border-gray-300 rounded-md text-gray-700 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="daily">Daily</option>
                <option value="weekly">Weekly</option>
                <option value="monthly">Monthly</option>
            </select>
        </div>


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
            specific sales records, view detailed information such as total amount, discount applied, transaction key,
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
                        <p><strong>Transaction Key:</strong> {{ $sale->transaction_key }}</p>
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
                            <th scope="col" class="px-6 py-3">Transaction Key</th>
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
    </div>

    <script src="{{ asset('tailwindcharts\js\apexcharts.js') }}"></script>




    <!-- Chart container -->
    <div id="sales-chart"></div>

    <script>
        // Pass chart data from the controller to JavaScript
        var chartData = @json($chartData);

        // Function to format the chart data based on the selected period
        function getChartData(period) {
            let dates = [];
            let totalAmounts = [];

            if (period === 'daily') {
                chartData.forEach(function(sale) {
                    dates.push(sale.date);
                    totalAmounts.push(parseFloat(sale.total_amount).toFixed(2)); // Format to 2 decimal places
                });
            } else if (period === 'weekly') {
                let weeklyData = {};
                chartData.forEach(function(sale) {
                    let weekNumber = getMonthlyWeekNumber(new Date(sale.date));
                    if (!weeklyData[weekNumber]) {
                        weeklyData[weekNumber] = 0;
                    }
                    weeklyData[weekNumber] += parseFloat(sale.total_amount);
                });

                for (let week in weeklyData) {
                    dates.push('Week ' + week);
                    totalAmounts.push(weeklyData[week].toFixed(2)); // Format to 2 decimal places
                }
            } else if (period === 'monthly') {
                let monthlyData = {};
                chartData.forEach(function(sale) {
                    let month = new Date(sale.date).toLocaleString('default', {
                        month: 'long',
                        year: 'numeric'
                    });
                    if (!monthlyData[month]) {
                        monthlyData[month] = 0;
                    }
                    monthlyData[month] += parseFloat(sale.total_amount);
                });

                for (let month in monthlyData) {
                    dates.push(month);
                    totalAmounts.push(monthlyData[month].toFixed(2)); // Format to 2 decimal places
                }
            }

            return {
                dates,
                totalAmounts
            };
        }

        // Function to get the week number of the month
        function getMonthlyWeekNumber(date) {
            const startOfMonth = new Date(date.getFullYear(), date.getMonth(), 1);
            const daysPassed = (date - startOfMonth) / (1000 * 60 * 60 * 24); // Difference in days
            return Math.ceil((daysPassed + 1) / 7); // Week number starts from 1
        }

        // Initial chart setup
        var period = 'daily'; // Default to daily
        var {
            dates,
            totalAmounts
        } = getChartData(period);

        // ApexCharts configuration
        document.addEventListener("DOMContentLoaded", function() {
            var options = {
                chart: {
                    type: 'line',
                    height: 350,
                    zoom: {
                        enabled: false
                    },
                    toolbar: {
                        show: false
                    },
                },
                series: [{
                    name: 'Total Sales',
                    data: totalAmounts
                }],
                xaxis: {
                    categories: dates,
                    title: {
                        text: 'Date'
                    },
                },
                yaxis: {
                    title: {
                        text: 'Total Amount'
                    },
                },
                title: {
                    text: 'Sales for the Last 30 Days',
                    align: 'left',
                },
                grid: {
                    borderColor: '#f1f1f1',
                    strokeDashArray: 4
                },
                colors: ['#00E396'],
            };

            var chart = new ApexCharts(document.querySelector("#sales-chart"), options);
            chart.render();

            // Event listener for dropdown change
            document.getElementById('period-select').addEventListener('change', function(event) {
                period = event.target.value;
                var {
                    dates,
                    totalAmounts
                } = getChartData(period);

                chart.updateOptions({
                    xaxis: {
                        categories: dates
                    },
                    series: [{
                        name: 'Total Sales',
                        data: totalAmounts
                    }],
                    title: {
                        text: 'Sales for the ' + period.charAt(0).toUpperCase() + period.slice(1),
                        align: 'left',
                    },
                });
            });
        });
    </script>


</x-layout>
