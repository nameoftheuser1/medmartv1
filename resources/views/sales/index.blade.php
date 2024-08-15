<x-layout>
    <div class="w-full px-4 sm:px-6 lg:px-8">
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

        <div class="relative overflow-x-auto sm:overflow-x-visible shadow-md sm:rounded-lg">
            <table class="w-full text-left rtl:text-right">
                <thead class="uppercase">
                    <tr>
                        <th scope="col" class="px-2 py-3 sm:px-6">ID</th>
                        <th scope="col" class="px-2 py-3 sm:px-6 hidden sm:table-cell">User</th>
                        <th scope="col" class="px-2 py-3 sm:px-6 hidden sm:table-cell">Total Amount</th>
                        <th scope="col" class="px-2 py-3 sm:px-6">Sale Time</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($sales as $sale)
                        <tr
                            class="even:bg-white even:dark:bg-gray-200 odd:bg-gray-50 odd:dark:bg-white dark:border-gray-700">
                            <td class="px-2 py-4 sm:px-6">{{ $sale->id }}</td>
                            <td
                                class="px-2 py-4 sm:px-6 font-medium text-gray-900 whitespace-nowrap hidden sm:table-cell">
                                {{ $sale->user ? $sale->user->name : 'N/A' }}</td>
                            <td class="px-2 py-4 sm:px-6 hidden sm:table-cell">
                                ₱{{ number_format($sale->total_amount, 2) }}</td>
                            <td class="px-2 py-4 sm:px-6">{{ $sale->created_at }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $sales->appends(['search' => request('search')])->links() }}
        </div>
    </div>
</x-layout>
