<x-layout>
    <div class="w-full px-4 sm:px-6 lg:px-8 bg-white p-5 rounded-lg shadow-lg">
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

        <div>
            @if (session('success'))
                <x-flashMsg msg="{{ session('success') }}" bg="bg-yellow-500" />
            @elseif (session('deleted'))
                <x-flashMsg msg="{{ session('deleted') }}" bg="bg-red-500" />
            @endif
        </div>

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

        <div class="mt-4">
            {{ $saleDetails->appends(['search' => request('search')])->links('vendor.pagination.tailwind') }}
        </div>
    </div>
</x-layout>
