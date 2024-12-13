<x-layout>
    <div class="w-full px-4 sm:px-6 lg:px-8 bg-white p-5 rounded-lg shadow-lg">

        <div class="my-5 flex justify-center gap-4">
            <a href="{{ route('inventories.product') }}"
                class="px-4 py-2 bg-slate-500 text-white rounded-lg hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-opacity-50">
                Overall Product Quantity
            </a>
            <a href="{{ route('inventories.index') }}"
                class="px-4 py-2 bg-slate-500 text-white rounded-lg hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-opacity-50">
                Show All Products
            </a>
            <a href="{{ route('inventories.index', ['expired' => 'true']) }}"
                class="px-4 py-2 bg-slate-500 text-white rounded-lg hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-opacity-50">
                Show Expired Products
            </a>
            <a href="{{ route('inventories.returned') }}"
                class="px-4 py-2 bg-slate-500 text-white rounded-lg hover:bg-slate-600 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-opacity-50">
                Show Returned Products
            </a>

        </div>

        <div class="flex flex-col sm:flex-row justify-between items-center mb-5">
            <h1 class="text-2xl font-bold mb-2 sm:mb-0">Inventory List</h1>
            <p class="mb-2 sm:mb-0">Total Items: {{ $inventories->total() }}</p>
            <form method="GET" action="{{ route('inventories.index') }}" class="flex w-full sm:w-auto">
                <input type="text" name="search" placeholder="Search..." value="{{ request('search') }}"
                    class="w-full sm:w-auto px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <button type="submit"
                    class="ml-2 px-4 py-2 bg-blue-700 text-white rounded-lg hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">Search</button>
            </form>
        </div>

        <div class="mb-2">
            This page provides a detailed overview of the current inventory, including product quantities, batch
            information, and expiration details. Users can search for specific items, view expired or low-stock
            products, and take actions such as editing, deleting, or emptying product quantities. The tools available
            ensure efficient inventory tracking and management for maintaining optimal stock levels.
        </div>

        <div class="mt-6 p-4 bg-gray-100 rounded-lg border border-gray-200">
            <h2 class="text-lg font-bold mb-2">Legend</h2>
            <ul class="list-disc list-inside">
                <li><span class="text-red-500 font-bold">Red</span>: Expiration date within 90 days or quantity less
                    than or equal to 30.</li>
                <li><span class="text-black">Black</span>: Expiration date more than 90 days and quantity greater than
                    30.</li>
            </ul>
        </div>

        <div>
            @if (session('success'))
                <x-flashMsg msg="{{ session('success') }}" bg="bg-yellow-500" />
            @elseif (session('deleted'))
                <x-flashMsg msg="{{ session('deleted') }}" bg="bg-red-500" />
            @endif
        </div>

        <div class="relative overflow-x-auto sm:rounded-lg">
            @if ($inventories->isEmpty())
                <p class="text-center py-5 text-gray-500">Wow, this table is empty.</p>
            @else
                <table class="w-full text-left rtl:text-right">
                    <thead class="uppercase">
                        <tr>
                            <th scope="col" class="px-4 py-3 sm:px-6 hidden md:table-cell">Product Name</th>
                            <th scope="col" class="px-4 py-3 sm:px-6 ">Batch Number</th>
                            <th scope="col" class="px-4 py-3 sm:px-6 hidden lg:table-cell">Expiration Date</th>
                            <th scope="col" class="px-4 py-3 sm:px-6 hidden lg:table-cell">Supplier Price</th>
                            <th scope="col" class="px-4 py-3 sm:px-6 hidden md:table-cell">Received Date</th>
                            <th scope="col" class="px-4 py-3 sm:px-6">Quantity</th>
                            <th scope="col" class="px-4 py-3 sm:px-6">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($inventories as $inventory)
                            @php
                                $today = \Carbon\Carbon::today(); // Get today's date
                                $expirationDate = \Carbon\Carbon::parse($inventory->productBatch->expiration_date); // Parse the expiration date
                                $daysToExpiry = $today->diffInDays($expirationDate); // Calculate the difference in days
                            @endphp
                            <tr
                                class="even:bg-white even:dark:bg-gray-200 odd:bg-gray-50 odd:dark:bg-white dark:border-gray-700">
                                <td
                                    class="px-4 py-4 sm:px-6 font-medium text-gray-900 whitespace-nowrap hidden md:table-cell">
                                    {{ $inventory->productBatch->product->product_name }}
                                </td>
                                <td class="px-4 py-4 sm:px-6">
                                    {{ $inventory->productBatch->batch_number }}
                                </td>
                                <td class="px-4 py-4 sm:px-6 hidden lg:table-cell">
                                    <span
                                        class="
                    @if ($daysToExpiry <= 90) text-red-500
                    @else
                        text-black @endif
                ">
                                        {{ $inventory->productBatch->expiration_date->format('Y-m-d') }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 sm:px-6 hidden lg:table-cell">
                                    ₱{{ number_format($inventory->productBatch->supplier_price, 2) }}
                                </td>
                                <td class="px-4 py-4 sm:px-6 hidden md:table-cell">
                                    {{ $inventory->productBatch->received_date->format('Y-m-d') }}
                                </td>
                                <td class="px-4 py-4 sm:px-6">
                                    <span
                                        class="
                    @if ($inventory->quantity <= 30) text-red-500
                    @else
                        text-black @endif
                ">
                                        {{ $inventory->quantity }}
                                    </span>
                                </td>

                                <td class="px-4 py-4 sm:px-6">
                                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2">
                                        <a href="{{ route('inventories.edit', $inventory->id) }}"
                                            class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Edit</a>
                                        <form action="{{ route('inventories.destroy', $inventory) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="font-medium text-red-600 dark:text-red-500 hover:underline">Delete</button>
                                        </form>
                                        @if ($inventory->quantity != 0)
                                            <form
                                                action="{{ route('product_batches.returnDate', $inventory->productBatch->id) }}"
                                                method="POST" class="inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                    class="font-small text-green-600 dark:text-green-500 hover:underline">
                                                    Return Product
                                                </button>
                                            </form>
                                        @endif
                                        @if ($inventory->productBatch->return_date)
                                            <span class="text-red-600">Returned</span>
                                            <!-- Display 'Returned' if the return_date is set -->
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>
            @endif
        </div>

        <div class="mt-4">
            {{ $inventories->appends(['search' => request('search')])->links('vendor.pagination.tailwind') }}
        </div>
    </div>
</x-layout>
