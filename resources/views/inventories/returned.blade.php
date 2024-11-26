<x-layout>
    <div class="w-full px-4 sm:px-6 lg:px-8 bg-white p-5 rounded-lg shadow-lg">
        <div class="mb-5 flex items-center">
            <a href="{{ url()->previous() }}"
                class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50">
                ← Back
            </a>
            <h1 class="text-2xl font-bold ml-4">Returned Products</h1>
        </div>
    </div>


        @if ($inventories->isEmpty())
            <p class="text-center py-5 text-gray-500">No returned products found.</p>
        @else
            <div class="relative overflow-x-auto sm:rounded-lg">
                <table class="w-full text-left rtl:text-right">
                    <thead class="uppercase">
                        <tr>
                            <th class="px-4 py-3 sm:px-6">Product Name</th>
                            <th class="px-4 py-3 sm:px-6">Batch Number</th>
                            <th class="px-4 py-3 sm:px-6">Return Date</th>
                            <th class="px-4 py-3 sm:px-6">Quantity</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($inventories as $inventory)
                            <tr class="even:bg-white odd:bg-gray-50">
                                <td class="px-4 py-4 sm:px-6">
                                    {{ $inventory->productBatch->product->product_name }}
                                </td>
                                <td class="px-4 py-4 sm:px-6">
                                    {{ $inventory->productBatch->batch_number }}
                                </td>
                                <td class="px-4 py-4 sm:px-6">
                                    {{ $inventory->productBatch->return_date->format('Y-m-d') }}
                                </td>
                                <td class="px-4 py-4 sm:px-6">
                                    {{ $inventory->quantity }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $inventories->links('vendor.pagination.tailwind') }}
            </div>
        @endif
    </div>
</x-layout>
