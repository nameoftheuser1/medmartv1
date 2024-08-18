<x-layout>
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

        <div class="sm:hidden">
            @foreach ($sales as $sale)
                <div class="bg-white shadow-md rounded-lg mb-4 p-4">
                    <p><strong>ID:</strong> {{ $sale->id }}</p>
                    <p><strong>User:</strong> {{ $sale->user ? $sale->user->name : 'N/A' }}</p>
                    <p><strong>Total Amount:</strong> ₱{{ number_format($sale->total_amount, 2) }}</p>
                    <p><strong>Discount:</strong> {{ $sale->discount > 0 ? $sale->discount . '%' : 'No Discount' }}</p>
                    <p><strong>Transaction Key:</strong> {{ $sale->transaction_key }}</p>
                    <p><strong>Sale Time:</strong> {{ $sale->created_at }}</p>
                </div>
            @endforeach
        </div>

        <div class="hidden sm:block relative overflow-x-auto sm:rounded-lg">
            <table class="w-full text-left rtl:text-right">
                <thead class="uppercase">
                    <tr>
                        <th scope="col" class="px-6 py-3">ID</th>
                        <th scope="col" class="px-6 py-3">User</th>
                        <th scope="col" class="px-6 py-3">Total Amount</th>
                        <th scope="col" class="px-6 py-3">Discount</th>
                        <th scope="col" class="px-6 py-3">Transaction Key</th>
                        <th scope="col" class="px-6 py-3">Sale Time</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sales as $sale)
                        <tr
                            class="even:bg-white even:dark:bg-gray-200 odd:bg-gray-50 odd:dark:bg-white dark:border-gray-700">
                            <td class="px-6 py-4">{{ $sale->id }}</td>
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                {{ $sale->user ? $sale->user->name : 'N/A' }}</td>
                            <td class="px-6 py-4">₱{{ number_format($sale->total_amount, 2) }}</td>
                            <td class="px-6 py-4">
                                {{ $sale->discount > 0 ? $sale->discount . '%' : 'No Discount' }}
                            </td>
                            <td class="px-6 py-4">{{ $sale->transaction_key }}</td>
                            <td class="px-6 py-4">{{ $sale->created_at }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $sales->appends(['search' => request('search')])->links('vendor.pagination.tailwind') }}
        </div>
    </div>
</x-layout>
