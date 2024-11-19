<x-layout>
    <div class="w-full px-4 sm:px-6 lg:px-8 bg-white p-5 rounded-lg shadow-lg">
        <!-- Sale Details and Search Form -->
        <div class="flex flex-col sm:flex-row justify-between items-center mb-5">
            <h1 class="text-2xl font-bold mb-2 sm:mb-0">Sale Details List</h1>
            <p class="mb-2 sm:mb-0">Total Sale Details: {{ $saleDetails->total() }}</p>
            <form method="GET" action="{{ route('sale_details.index') }}" class="flex w-full sm:w-auto">
                <input type="text" name="search" placeholder="Search..." value="{{ request('search') }}"
                    class="w-full sm:w-auto px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <button type="submit"
                    class="ml-2 px-4 py-2 bg-blue-700 text-white rounded-lg hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">Search</button>
            </form>
        </div>

        <!-- Flash Messages -->
        <div>
            @if (session('success'))
                <x-flashMsg msg="{{ session('success') }}" bg="bg-yellow-500" />
            @elseif (session('deleted'))
                <x-flashMsg msg="{{ session('deleted') }}" bg="bg-red-500" />
            @endif
        </div>

        <!-- Sales Chart -->
        <div class="mt-6">
            <div id="sales-chart"></div>
        </div>

        <!-- Table Display -->
        <div class="relative overflow-x-auto sm:overflow-x-visible sm:rounded-lg">
            @if ($saleDetails->isEmpty())
                <p class="text-center py-5 text-gray-500">Wow, this table is empty.</p>
            @else
                <table class="w-full text-left rtl:text-right">
                    <thead class="uppercase">
                        <tr>
                            <th scope="col" class="px-2 py-3 sm:px-6">ID</th>
                            <th scope="col" class="px-2 py-3 sm:px-6">Sale ID</th>
                            <th scope="col" class="px-2 py-3 sm:px-6 hidden sm:table-cell">Product</th>
                            <th scope="col" class="px-2 py-3 sm:px-6">Quantity Bought</th>
                            <th scope="col" class="px-2 py-3 sm:px-6">Price</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($saleDetails as $saleDetail)
                            <tr
                                class="even:bg-white even:dark:bg-gray-200 odd:bg-gray-50 odd:dark:bg-white dark:border-gray-700">
                                <td class="px-2 py-4 sm:px-6">{{ $saleDetail->id }}</td>
                                <td class="px-2 py-4 sm:px-6 font-medium text-gray-900 whitespace-nowrap">
                                    {{ $saleDetail->sale_id }}</td>
                                <td class="px-2 py-4 sm:px-6 hidden sm:table-cell">
                                    {{ $saleDetail->product->product_name }}
                                </td>
                                <td class="px-2 py-4 sm:px-6">{{ $saleDetail->quantity }}</td>
                                <td class="px-2 py-4 sm:px-6">₱{{ number_format($saleDetail->price, 2) }}</td>
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
            var chartData = @json($chartData);

            var productNames = chartData.map(function(item) {
                return item.product_name;
            });

            var totalAmounts = chartData.map(function(item) {
                return item.total_amount;
            });

            var options = {
                chart: {
                    type: 'bar',
                    height: 350
                },
                series: [{
                    name: 'Total Amount',
                    data: totalAmounts
                }],
                xaxis: {
                    categories: productNames,
                    title: {
                        text: 'Product Name',
                        style: {
                            color: '#000', // Black text for x-axis title
                            fontSize: '14px',
                            fontWeight: 'bold'
                        }
                    },
                    labels: {
                        style: {
                            colors: '#000', // Black text for x-axis labels
                            fontSize: '12px'
                        }
                    }
                },
                yaxis: {
                    title: {
                        text: 'Total Sales Amount (₱)',
                        style: {
                            color: '#000', // Black text for y-axis title
                            fontSize: '14px',
                            fontWeight: 'bold'
                        }
                    },
                    labels: {
                        style: {
                            colors: '#000', // Black text for y-axis labels
                            fontSize: '12px'
                        }
                    }
                },
                title: {
                    text: 'Top Selling Products Today',
                    align: 'center',
                    style: {
                        color: '#000', // Black text for chart title
                        fontSize: '16px',
                        fontWeight: 'bold'
                    }
                },
                tooltip: {
                    theme: 'light',
                },
                dataLabels: {
                    style: {
                        colors: ['#000'] // Black text for data labels
                    }
                }
            };

            var chart = new ApexCharts(document.querySelector("#sales-chart"), options);
            chart.render();
        });
    </script>
</x-layout>
