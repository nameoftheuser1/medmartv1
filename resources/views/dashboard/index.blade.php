{{--  This is the dashboard page.
//    its already sanitized
//
--}}

<x-layout>
    <h1 class="mb-4 text-5xl font-bold">Dashboard</h1>
    <div class="w-full bg-white rounded-lg shadow p-4 md:p-6 mb-3">
        <div class="flex justify-between mb-5">
            <div class="grid gap-4 grid-cols-2">
                <div class="flex gap-4">
                    <div>
                        <h5 class="inline-flex items-center text-gray-500 leading-none font-normal mb-2">
                            Total Sales
                        </h5>
                        <p class="text-gray-900 text-2xl leading-none font-bold">
                            {{ is_numeric($totalSales) ? $totalSales : 0 }}</p>
                    </div>
                    <div>
                        <h5 class="inline-flex items-center text-gray-500 leading-none font-normal mb-2">
                            Total Sales Today
                        </h5>
                        <p class="text-gray-900 text-2xl leading-none font-bold">
                            {{ is_numeric($totalSalesToday) ? $totalSalesToday : 0 }}</p>
                    </div>
                </div>
            </div>
            <div>
                <div class="mb-4">
                    <label for="period" class="block text-sm font-medium text-gray-700">Select Period:</label>
                    <select id="period" name="period"
                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option value="weekly" {{ e($currentPeriod) == 'weekly' ? 'selected' : '' }}>Weekly</option>
                        <option value="monthly" {{ e($currentPeriod) == 'monthly' ? 'selected' : '' }}>Monthly</option>
                        <option value="yearly" {{ e($currentPeriod) == 'yearly' ? 'selected' : '' }}>Yearly</option>
                    </select>
                </div>
            </div>
        </div>
        <div id="line-chart"></div>
    </div>
    <div class="flex flex-col md:flex-row justify-between mb-6 gap-3">
        <div class="card p-4 bg-white shadow rounded mx-auto md:mb-0 md:w-1/2 w-full">
            <form id="inventory-form" method="GET" action="{{ e(route('dashboard')) }}">
                <h2 class="text-xl font-bold mb-2 font-mono">Inventory Level</h2>
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

        <div class="bg-white rounded-lg shadow-sm p-4 md:w-1/2 max-h-[430] min-h-[430] overflow-y-scroll">
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
                                    <td class="px-4 py-2 border">{{ e($batch->product->product_name) }}</td>
                                    <td class="px-4 py-2 border text-center">{{ e($batch->batch_number) }}</td>
                                    <td class="px-4 py-2 border text-center">
                                        {{ e($batch->expiration_date->format('Y-m-d')) }}
                                    </td>
                                    <td class="px-4 py-2 border text-center">
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
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        <div class="card p-4 bg-white shadow rounded flex">
            <p
                class="text-lg font-semibold h-full bg-green-500 p-2 rounded-lg flex items-center justify-center text-white w-1/4">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="size-11">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
                </svg>
            </p>
            <p class="p-2">Product Count:</p>
            <h1 class="text-3xl font-bold text-center mt-2">
                {{ e($productCount) }}
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
                    {{ e($supplierCount) }}
                </h1>
            </div>
        </div>

    </div>
    <script src="{{ asset('tailwindcharts\js\apexcharts.js') }}"></script>

    <script>
        var salesSeries = @json($salesSeries);
        var categories = @json($categories);
        var dashboardRoute = "{{ e(route('dashboard')) }}";
    </script>

    <script src="{{ asset('js/salesChart.js') }}"></script>

    <script>
        var inventoryBatches = @json($inventoryBatches);
        var currentInventoryType = "{{ e($currentInventoryType) }}";
        var chartTitle = currentInventoryType === 'highest' ? 'Highest Inventory' : 'Lowest Inventory';
    </script>

    <script src="{{ asset('js/inventoryChart.js') }}"></script>
    <script src="{{ asset('jquery/jquery-3.7.1.min.js') }}"></script>

</x-layout>
