<x-layout>
    <div class="w-full px-4 sm:px-6 lg:px-8 bg-white p-5 rounded-lg shadow-lg">
        <div class="flex flex-col sm:flex-row justify-between items-center mb-5">
            <h1 class="text-2xl font-bold mb-2 sm:mb-0">Expense List</h1>
            <p class="mb-2 sm:mb-0">Total Expenses: {{ $expenses->total() }}</p>
            <form method="GET" action="{{ route('expenses.index') }}" class="flex w-full sm:w-auto">
                <input type="text" name="search" placeholder="Search..." value="{{ request('search') }}"
                    class="w-full sm:w-auto px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <button type="submit"
                    class="ml-2 px-4 py-2 bg-blue-700 text-white rounded-lg hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">Search</button>
            </form>
        </div>

        <div class="mb-2">
            This page lists all expenses along with their essential details such as total amount and description. Users can
            search for specific expenses, add new ones, or manage existing records with options to view, edit, or
            delete. The tools provided ensure efficient expense management for maintaining accurate and up-to-date
            records.
        </div>

        <div class="flex mb-5">
            <a href="{{ route('expenses.create') }}" class="btn text-lg w-full sm:w-auto text-center">Add Expense</a>
        </div>

        <div>
            @if (session('success'))
                <x-flashMsg msg="{{ session('success') }}" bg="bg-yellow-500" />
            @elseif (session('deleted'))
                <x-flashMsg msg="{{ session('deleted') }}" bg="bg-red-500" />
            @endif
        </div>

        <div class="overflow-x-auto sm:overflow-x-visible">
            @if ($expenses->isEmpty())
                <p class="text-center py-5 text-gray-500">Wow, this table is empty.</p>
            @else
                <div class="w-full sm:max-w-full">
                    <table class="w-full text-left rtl:text-right">
                        <thead class="uppercase">
                            <tr>
                                <th scope="col" class="px-2 py-3 sm:px-6 hidden sm:table-cell">ID</th>
                                <th scope="col" class="px-2 py-3 sm:px-6">Total Amount</th>
                                <th scope="col" class="px-2 py-3 sm:px-6 hidden sm:table-cell">Description</th>
                                <th scope="col" class="px-2 py-3 sm:px-6">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($expenses as $expense)
                                <tr
                                    class="even:bg-white even:dark:bg-gray-200 odd:bg-gray-50 odd:dark:bg-white dark:border-gray-700">
                                    <td class="px-2 py-4 sm:px-6 hidden sm:table-cell">{{ $expense->id }}</td>
                                    <td class="px-2 py-4 sm:px-6 font-medium text-gray-900 whitespace-nowrap">
                                    ₱{{ number_format($expense->total_amount, 2) }}
                                    </td>
                                    <td class="px-2 py-4 sm:px-6 hidden sm:table-cell">
                                        {{ Str::limit($expense->description, 30) }}
                                    </td>
                                    <td class="px-2 py-4 sm:px-6">
                                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2">
                                            <x-tooltip message="Click to view the full details of the expense">
                                                <a href="{{ route('expenses.show', $expense) }}"
                                                    class="font-medium text-blue-600 dark:text-blue-500 hover:underline">View</a>
                                            </x-tooltip>
                                            <a href="{{ route('expenses.edit', $expense) }}"
                                                class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Edit</a>
                                            <form action="{{ route('expenses.destroy', $expense) }}" method="post">
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
    </div>

    <div class="mt-4">
        {{ $expenses->appends(['search' => request('search')])->links('vendor.pagination.tailwind') }}
    </div>
    </div>
</x-layout>
