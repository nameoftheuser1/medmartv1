<x-layout>
    <div class="w-full px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row justify-between items-center mb-5">
            <h1 class="text-2xl font-bold mb-2 sm:mb-0">Supplier List</h1>
            <p class="mb-2 sm:mb-0">Total Supplier: {{ $suppliers->total() }}</p>
            <form method="GET" action="{{ route('suppliers.index') }}" class="flex w-full sm:w-auto">
                <input type="text" name="search" placeholder="Search..." value="{{ request('search') }}"
                    class="w-full sm:w-auto px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <button type="submit"
                    class="ml-2 px-4 py-2 bg-blue-700 text-white rounded-lg hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">Search</button>
            </form>
        </div>

        <div class="flex mb-5">
            <a href="{{ route('suppliers.create') }}" class="btn text-lg w-full sm:w-auto text-center">Add Supplier</a>
        </div>

        <div>
            @if (session('success'))
                <x-flashMsg msg="{{ session('success') }}" bg="bg-yellow-500" />
            @elseif (session('deleted'))
                <x-flashMsg msg="{{ session('deleted') }}" bg="bg-red-500" />
            @endif
        </div>

        <div class="overflow-x-auto sm:overflow-x-visible">
            <div class="w-full sm:max-w-full">
                <table class="w-full text-left rtl:text-right">
                    <thead class="uppercase">
                        <tr>
                            <th scope="col" class="px-2 py-3 sm:px-6 hidden sm:table-cell">ID</th>
                            <th scope="col" class="px-2 py-3 sm:px-6">Supplier Name</th>
                            <th scope="col" class="px-2 py-3 sm:px-6 hidden sm:table-cell">Contact Info</th>
                            <th scope="col" class="px-2 py-3 sm:px-6">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($suppliers as $supplier)
                            <tr
                                class="even:bg-white even:dark:bg-gray-200 odd:bg-gray-50 odd:dark:bg-white dark:border-gray-700">
                                <td class="px-2 py-4 sm:px-6 hidden sm:table-cell">{{ $supplier->id }}</td>
                                <td class="px-2 py-4 sm:px-6 font-medium text-gray-900 whitespace-nowrap ">
                                    {{ Str::limit($supplier->supplier_name, 15) }}
                                </td>
                                <td class="px-2 py-4 sm:px-6 hidden sm:table-cell">
                                    {{ Str::words($supplier->contact_info, 3) }}</td>
                                <td class="px-2 py-4 sm:px-6">
                                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2">
                                        <x-tooltip message="Click to view the full details of the supplier">
                                            <a href="{{ route('suppliers.show', $supplier) }}"
                                                class="font-medium text-blue-600 dark:text-blue-500 hover:underline">View</a>
                                        </x-tooltip>
                                        <a href="{{ route('suppliers.edit', $supplier) }}"
                                            class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Edit</a>
                                        <form action="{{ route('suppliers.destroy', $supplier) }}" method="post">
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
        </div>

        <div class="mt-4">
            {{ $suppliers->appends(['search' => request('search')])->links('vendor.pagination.tailwind') }}
        </div>
    </div>
</x-layout>
