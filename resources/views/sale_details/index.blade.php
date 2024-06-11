<x-layout>
    <div class="w-full">
        <div class="flex justify-between items-center mb-5">
            <h1>Sale Details List</h1>
            <p>Total Sale Details: {{ $saleDetails->total() }}</p>
            <form method="GET" action="{{ route('sale_details.index') }}" class="flex">
                <input type="text" name="search" placeholder="Search..." value="{{ request('search') }}"
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
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

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-left rtl:text-right">
                <thead class="uppercase">
                    <tr>
                        <th scope="col" class="px-6 py-3">ID</th>
                        <th scope="col" class="px-6 py-3">Sale ID</th>
                        <th scope="col" class="px-6 py-3">Product</th>
                        <th scope="col" class="px-6 py-3">Quantity Bought</th>
                        <th scope="col" class="px-6 py-3">Price</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($saleDetails as $saleDetail)
                        <tr
                            class="even:bg-white even:dark:bg-gray-200 odd:bg-gray-50 odd:dark:bg-white dark:border-gray-700">
                            <td class="px-6 py-4">{{ $saleDetail->id }}</td>
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                {{ $saleDetail->sale_id }}</td>
                            <td class="px-6 py-4">{{ $saleDetail->product->product_name }}</td>
                            <td class="px-6 py-4">{{ $saleDetail->quantity }}</td>
                            <td class="px-6 py-4">₱{{ number_format($saleDetail->price, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div>
            {{ $saleDetails->appends(['search' => request('search')])->links() }}
        </div>
    </div>
</x-layout>
