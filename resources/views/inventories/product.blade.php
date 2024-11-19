<x-layout>
    <div class="w-full p-5 px-4 bg-white rounded-lg shadow-lg sm:px-6 lg:px-8">
        <div class="flex flex-col items-center justify-between mb-5 sm:flex-row">
            <h1 class="mb-2 text-2xl font-bold sm:mb-0">Product Inventory</h1>
            <p class="mb-2 text-sm text-gray-600 sm:mb-0">Total Products: {{ $inventories->total() }}</p>
            <form method="GET" action="{{ route('inventories.product') }}"
                class="flex flex-col w-full sm:flex-row sm:w-auto">
                <input type="text" name="search" placeholder="Search product..." value="{{ request('search') }}"
                    class="w-full px-4 py-2 mb-2 border border-gray-300 rounded-lg sm:w-auto focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent sm:mb-0">

                <select name="sort"
                    class="px-4 py-2 mb-2 border border-gray-300 rounded-lg sm:w-auto focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent sm:mb-0">
                    <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>Ascending</option>
                    <option value="desc" {{ request('sort') == 'desc' ? 'selected' : '' }}>Descending</option>
                </select>

                <button type="submit"
                    class="px-4 py-2 ml-2 text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                    Search
                </button>
            </form>
        </div>




        <div class="mb-2">
            This page allows you to view and manage the product inventory effectively. Use the search and sorting
            options to customize your view.
        </div>

        <div class="relative overflow-x-auto sm:rounded-lg">
            @if ($inventories->isEmpty())
                <p class="py-5 text-center text-gray-500">No products found.</p>
            @else
                <table class="w-full text-left rtl:text-right">
                    <thead class="bg-gray-100 uppercase">
                        <tr>
                            <th scope="col" class="px-4 py-3 sm:px-6">Product Name</th>
                            <th scope="col" class="px-4 py-3 sm:px-6">Total Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($inventories as $inventory)
                            <tr class="transition duration-150 ease-in-out cursor-pointer hover:bg-green-300">
                                <td class="px-4 py-4 font-medium text-gray-900 sm:px-6 whitespace-nowrap">
                                    {{ Str::limit($inventory->product_name, 15) }}
                                </td>
                                <td
                                    class="px-4 py-4 sm:px-6 text-sm {{ $inventory->total_quantity <= 0 ? 'text-red-500 font-bold' : 'text-gray-500' }}">
                                    {{ $inventory->total_quantity }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <div class="mt-6">
            {{ $inventories->appends([
                    'search' => request('search'),
                    'sort' => request('sort', 'asc'),
                    'sortBy' => request('sortBy', 'quantity'),
                ])->links('vendor.pagination.tailwind') }}
        </div>
    </div>

    <script>
        document.getElementById('sortSelect').addEventListener('change', function() {
            window.location.href = this.value;
        });
    </script>
</x-layout>
