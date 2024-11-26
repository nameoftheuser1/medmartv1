<x-layout>
    <div class="w-full px-4 sm:px-6 lg:px-8 bg-white p-5 rounded-lg shadow-lg">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Sale Details Overview</h1>
            <p class="text-gray-600 text-sm">Total Sale Details: <span class="font-medium">{{ $saleDetails->total() }}</span></p>
        </div>

        <!-- Time Period Selector and Search -->
        <div class="flex flex-col sm:flex-row justify-between items-center mb-6 space-y-4 sm:space-y-0">
            <div class="flex items-center space-x-3">
                <label for="time-period" class="text-gray-700 font-medium">View By:</label>
                <select id="time-period" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="daily" {{ request('period') == 'daily' ? 'selected' : '' }}>Daily</option>
                    <option value="weekly" {{ request('period') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                    <option value="monthly" {{ request('period') == 'monthly' ? 'selected' : '' }}>Monthly</option>
                </select>
            </div>
            <form method="GET" action="{{ route('sale_details.index') }}" class="flex items-center space-x-2">
                <input type="text" name="search" placeholder="Search..." value="{{ request('search') }}"
                    class="w-full sm:w-auto px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500">Search</button>
            </form>
        </div>

        <!-- Flash Messages -->
        <div>
            @if (session('success'))
                <x-flashMsg msg="{{ session('success') }}" bg="bg-green-500" />
            @elseif (session('deleted'))
                <x-flashMsg msg="{{ session('deleted') }}" bg="bg-red-500" />
            @endif
        </div>

        <!-- Chart Section -->
        <div class="bg-gray-50 p-4 rounded-lg shadow-lg">
            <h2 class="text-lg font-bold text-gray-800 mb-3">Top Selling Products</h2>
            <p class="text-sm text-gray-600 mb-4">
                Use the dropdown to view sales data for a specific time period. This chart shows the top-selling products based on total sales amount.
            </p>
            <div class="relative">
                <div id="loading-spinner" class="absolute inset-0 flex items-center justify-center hidden">
                    <div class="w-8 h-8 border-4 border-blue-500 border-t-transparent rounded-full animate-spin"></div>
                </div>
                <div id="sales-chart"></div>
            </div>
        </div>

        <!-- Table Display -->
        <div class="relative overflow-x-auto sm:rounded-lg mt-6">
            @if ($saleDetails->isEmpty())
                <p class="text-center py-5 text-gray-500">No sales data available at the moment.</p>
            @else
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-3">ID</th>
                            <th class="px-4 py-3">Sale ID</th>
                            <th class="px-4 py-3 hidden sm:table-cell">Product</th>
                            <th class="px-4 py-3">Quantity</th>
                            <th class="px-4 py-3">Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($saleDetails as $saleDetail)
                            <tr class="even:bg-gray-50 odd:bg-white">
                                <td class="px-4 py-3">{{ $saleDetail->id }}</td>
                                <td class="px-4 py-3">{{ $saleDetail->sale_id }}</td>
                                <td class="px-4 py-3 hidden sm:table-cell">{{ $saleDetail->product->product_name }}</td>
                                <td class="px-4 py-3">{{ $saleDetail->quantity }}</td>
                                <td class="px-4 py-3">₱{{ number_format($saleDetail->price, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $saleDetails->appends(['search' => request('search')])->links('vendor.pagination.tailwind') }}
        </div>
    </div>

    <!-- ApexCharts Script -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const chartContainer = document.querySelector("#sales-chart");
            const spinner = document.querySelector("#loading-spinner");

            function fetchChartData(period) {
                spinner.classList.remove("hidden"); // Show loading spinner
                fetch('{{ route('sale_details.chart') }}' + '?period=' + period)
                    .then(response => response.json())
                    .then(data => {
                        spinner.classList.add("hidden"); // Hide loading spinner

                        const productNames = data.map(item => item.product_name);
                        const totalAmounts = data.map(item => item.total_amount);

                        const options = {
                            chart: { type: 'bar', height: 350 },
                            series: [{ name: 'Total Amount', data: totalAmounts }],
                            xaxis: { categories: productNames },
                            title: { text: `Top Selling Products (${period})`, align: 'center' }
                        };

                        const chart = new ApexCharts(chartContainer, options);
                        chart.render();
                    });
            }

            // Fetch default chart data
            const timePeriodSelect = document.querySelector("#time-period");
            fetchChartData(timePeriodSelect.value);

            // Update chart on time period change
            timePeriodSelect.addEventListener("change", () => {
                chartContainer.innerHTML = ""; // Clear previous chart
                fetchChartData(timePeriodSelect.value);
            });
        });
    </script>
</x-layout>
