<x-layout>
    <div class="w-full">
        <div class="flex justify-between items-center mb-5">
            <h1>Product Batch List</h1>
            <p>Total Product Batches: {{ $productBatches->total() }}</p>
            <form method="GET" action="{{ route('product_batches.index') }}" class="flex">
                <input type="text" name="search" placeholder="Search..." value="{{ request('search') }}"
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <button type="submit"
                    class="ml-2 px-4 py-2 bg-blue-700 text-white rounded-lg hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">Search</button>
            </form>
        </div>
        <div class="flex">
            <a href="{{ route('product_batches.create') }}" class="btn text-lg"> Add Product Batch </a>
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
                        <th scope="col" class="px-6 py-3">ID</th>
                        <th scope="col" class="px-6 py-3">Product</th>
                        <th scope="col" class="px-6 py-3">Supplier</th>
                        <th scope="col" class="px-6 py-3">Batch Number</th>
                        <th scope="col" class="px-6 py-3">Expiration Date</th>
                        <th scope="col" class="px-6 py-3">Supplier Price</th>
                        <th scope="col" class="px-6 py-3">Received Date</th>
                        <th scope="col" class="px-6 py-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($productBatches as $productBatch)
                        <tr
                            class="even:bg-white even:dark:bg-gray-200 odd:bg-gray-50 odd:dark:bg-white dark:border-gray-700">
                            <td class="px-6 py-4">{{ $productBatch->id }}</td>
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                {{ $productBatch->product->product_name }}</td>
                            <td class="px-6 py-4">{{ $productBatch->supplier->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4">{{ $productBatch->batch_number }}</td>
                            <td class="px-6 py-4">{{ $productBatch->expiration_date->format('Y-m-d') }}</td>
                            <td class="px-6 py-4">₱{{ number_format($productBatch->supplier_price, 2) }}</td>
                            <td class="px-6 py-4">{{ $productBatch->received_date->format('Y-m-d') }}</td>
                            <td class="px-6 py-4 flex   gap-2">
                                <a href="{{ route('product_batches.edit', $productBatch->id) }}"
                                    class="font-medium text-blue-600 dark:text-blue-500 hover:underline m-1">Edit</a>

                                <form action="{{ route('product_batches.destroy', $productBatch) }}" method="post">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" value=""
                                        class="font-medium text-red-600 dark:text-red-500 hover:underline m-1">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div>
            {{ $productBatches->appends(['search' => request('search')])->links() }}
        </div>
    </div>
</x-layout>
