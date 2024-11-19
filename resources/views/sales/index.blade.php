<x-layout>
    <div class="w-full p-4 mb-3 bg-white rounded-lg md:p-6">
        <h2 class="mb-2 font-mono text-xl font-bold"></h2>
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


    <script>
        // Pass chart data from the controller to JavaScript
        var chartData = @json($chartData);
        var dates = chartData.map(function (sale) {
            return sale.date;
        });
        var totalAmounts = chartData.map(function (sale) {
            return sale.total_amount;
        });

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
                    data: totalAmounts,
                }],
                xaxis: {
                    categories: dates,
                    title: {
                        text: 'Date',
                    },
                },
                yaxis: {
                    title: {
                        text: 'Total Amount',
                    },
                },
                title: {
                    text: 'Sales for the Last 30 Days',
                    align: 'left',
                },
                grid: {
                    borderColor: '#f1f1f1',
                    strokeDashArray: 4,
                },
                colors: ['#00E396'],
            };

            var chart = new ApexCharts(document.querySelector("#sales-chart"), options);
            chart.render();
        });
    </script>
</x-layout>
