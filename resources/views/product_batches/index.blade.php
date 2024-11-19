@php
    use Carbon\Carbon;
@endphp
<x-layout>
    <div class="w-full p-5 px-4 bg-white rounded-lg shadow-lg sm:px-6 lg:px-8">
        <div class="flex flex-col items-center justify-between mb-5 sm:flex-row">
            <h1 class="mb-2 text-2xl font-bold sm:mb-0">Product Batch List</h1>
            <p class="mb-2 text-sm text-gray-600 sm:mb-0">Total Product Batches: {{ $productBatches->total() }}</p>
            <form method="GET" action="{{ route('product_batches.index') }}"
                class="flex flex-col w-full sm:flex-row sm:w-auto">
                <input type="text" name="search" placeholder="Search..." value="{{ request('search') }}"
                    class="w-full px-4 py-2 mb-2 border border-gray-300 rounded-lg sm:w-auto focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent sm:mb-0">

                <select name="sort"
                    class="px-4 py-2 mb-2 border border-gray-300 rounded-lg sm:w-auto focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent sm:mb-0">
                    <option value="asc" {{ request('sort') == 'asc' ? 'selected' : '' }}>Ascending Expiration Date</option>
                    <option value="desc" {{ request('sort') == 'desc' ? 'selected' : '' }}>Descending Expiration Date</option>
                </select>

                <button type="submit"
                    class="px-4 py-2 ml-2 text-white bg-blue-700 rounded-lg hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
                    Search
                </button>
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

        <div class="relative overflow-x-auto sm:rounded-lg">
            @if ($productBatches->isEmpty())
                <p class="py-5 text-center text-gray-500">No product batches found.</p>
            @else
                <table class="w-full text-left rtl:text-right">
                    <thead class="bg-gray-100 uppercase">
                        <tr>
                            <th scope="col" class="px-4 py-3 sm:px-6 hidden md:table-cell">Product</th>
                            <th scope="col" class="px-4 py-3 sm:px-6">Batch Number</th>
                            <th scope="col" class="px-4 py-3 sm:px-6 hidden lg:table-cell">Expiration Date</th>
                            <th scope="col" class="px-4 py-3 sm:px-6 hidden lg:table-cell">Supplier Price</th>
                            <th scope="col" class="px-4 py-3 sm:px-6 hidden lg:table-cell">Product Price</th>
                            <th scope="col" class="px-4 py-3 sm:px-6 hidden md:table-cell">Received Date</th>
                            <th scope="col" class="px-4 py-3 sm:px-6">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($productBatches as $productBatch)
                            @php
                                $expirationDate = $productBatch->expiration_date;
                                $isExpired = $expirationDate->isPast();
                                $isNearExpiry = !$isExpired && $expirationDate->diffInDays(Carbon::today()) <= 30;
                            @endphp
                            <tr class="hover:bg-green-300 cursor-pointer transition duration-150 ease-in-out"
                                onclick="window.location='{{ route('product_batches.show', $productBatch->id) }}'">
                                <td class="px-4 py-4 sm:px-6 font-medium text-gray-900 whitespace-nowrap hidden md:table-cell">
                                    {{ $productBatch->product->product_name }}
                                </td>
                                <td class="px-4 py-4 sm:px-6">
                                    {{ $productBatch->batch_number }}
                                </td>
                                <td class="px-4 py-4 sm:px-6 hidden lg:table-cell">
                                    <span class="{{ $isExpired ? 'text-red-500' : ($isNearExpiry ? 'text-yellow-500' : '') }}">
                                        {{ $expirationDate->format('Y-m-d') }}
                                    </span>
                                </td>
                                <td class="px-4 py-4 sm:px-6 hidden lg:table-cell">
                                    ₱{{ number_format($productBatch->supplier_price, 2) }}
                                </td>
                                <td class="px-4 py-4 sm:px-6 hidden lg:table-cell">
                                    ₱{{ number_format($productBatch->product->price, 2) }}
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
                                        <form action="{{ route('product_batches.destroy', $productBatch) }}" method="post">
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
            @endif
        </div>

        <div class="mt-6">
            {{ $productBatches->appends([
                    'search' => request('search'),
                    'sort' => request('sort', 'asc'),
                ])->links('vendor.pagination.tailwind') }}
        </div>
    </div>
</x-layout>
