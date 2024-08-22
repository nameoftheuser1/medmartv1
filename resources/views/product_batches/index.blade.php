<x-layout>
    <div class="w-full px-4 sm:px-6 lg:px-8 bg-white p-5 rounded-lg shadow-lg">
        <div class="flex flex-col sm:flex-row justify-between items-center mb-5">
            <h1 class="text-2xl font-bold mb-2 sm:mb-0">Product Batch List</h1>
            <p class="mb-2 sm:mb-0">Total Product Batches: {{ $productBatches->total() }}</p>
            <form method="GET" action="{{ route('product_batches.index') }}" class="flex w-full sm:w-auto">
                <input type="text" name="search" placeholder="Search..." value="{{ request('search') }}"
                    class="w-full sm:w-auto px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <button type="submit"
                    class="ml-2 px-4 py-2 bg-blue-700 text-white rounded-lg hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">Search</button>
            </form>
        </div>
        <div class="flex mb-5">
            <a href="{{ route('product_batches.create') }}" class="btn text-lg w-full sm:w-auto text-center">Add Product
                Batch</a>
        </div>
        <div>
            @if (session('success'))
                <x-flashMsg msg="{{ session('success') }}" bg="bg-yellow-500" />
            @elseif (session('deleted'))
                <x-flashMsg msg="{{ session('deleted') }}" bg="bg-red-500" />
            @endif
        </div>

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-left rtl:text-right">
                <thead class="uppercase">
                    <tr>
                        <th scope="col" class="px-4 py-3 sm:px-6 hidden md:table-cell">ID</th>
                        <th scope="col" class="px-4 py-3 sm:px-6 hidden md:table-cell">Product</th>
                        <th scope="col" class="px-4 py-3 sm:px-6">Batch Number</th>
                        <th scope="col" class="px-4 py-3 sm:px-6 hidden lg:table-cell">Expiration Date</th>
                        <th scope="col" class="px-4 py-3 sm:px-6 hidden lg:table-cell">Supplier Price</th>
                        <th scope="col" class="px-4 py-3 sm:px-6 hidden md:table-cell">Received Date</th>
                        <th scope="col" class="px-4 py-3 sm:px-6">Action</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($productBatches as $productBatch)
                        <tr class="hover:bg-green-300 cursor-pointer transition duration-150 ease-in-out"
                            onclick="window.location='{{ route('product_batches.show', $productBatch->id) }}'">
                            <td class="px-4 py-4 sm:px-6 hidden md:table-cell">{{ $productBatch->id }}</td>
                            <td
                                class="px-4 py-4 sm:px-6 font-medium text-gray-900 whitespace-nowrap hidden md:table-cell">
                                {{ $productBatch->product->product_name }}
                            </td>
                            <td class="px-4 py-4 sm:px-6">
                                {{ $productBatch->batch_number }}
                            </td>
                            <td class="px-4 py-4 sm:px-6 hidden lg:table-cell">
                                {{ $productBatch->expiration_date->format('Y-m-d') }}
                            </td>
                            <td class="px-4 py-4 sm:px-6 hidden lg:table-cell">
                                ₱{{ number_format($productBatch->supplier_price, 2) }}
                            </td>
                            <td class="px-4 py-4 sm:px-6 hidden md:table-cell">
                                {{ $productBatch->received_date->format('Y-m-d') }}
                            </td>
                            <td class="px-4 py-4 sm:px-6">
                                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2">
                                    <a href="{{ route('product_batches.show', $productBatch->id) }}"
                                        class="font-medium text-blue-600 dark:text-blue-500 hover:underline">View</a>
                                    <a href="{{ route('product_batches.edit', $productBatch->id) }}"
                                        class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Edit</a>
                                    <form action="{{ route('product_batches.destroy', $productBatch) }}"
                                        method="post">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="font-medium text-red-600 dark:text-red-500 hover:underline">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $productBatches->appends(['search' => request('search')])->links('vendor.pagination.tailwind') }}
        </div>
    </div>
</x-layout>
