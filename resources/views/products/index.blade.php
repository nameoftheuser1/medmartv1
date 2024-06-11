<x-layout>
    <div class="w-full">
        <div class="flex justify-between items-center mb-5">
            <h1>Product List</h1>
            <p>Total Products: {{ $products->total() }}</p>
            <form method="GET" action="{{ route('products.index') }}" class="flex">
                <input type="text" name="search" placeholder="Search..." value="{{ request('search') }}"
                    class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <button type="submit"
                    class="ml-2 px-4 py-2 bg-blue-700 text-white rounded-lg hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">Search</button>
            </form>
        </div>
        <div class="flex">
            <a href="{{ route('products.create') }}" class="btn text-lg"> Add product </a>
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
                        <th scope="col" class="px-6 py-3">Product Name</th>
                        <th scope="col" class="px-6 py-3">Generic Name</th>
                        <th scope="col" class="px-6 py-3">Category</th>
                        <th scope="col" class="px-6 py-3">Description</th>
                        <th scope="col" class="px-6 py-3">Price</th>
                        <th scope="col" class="px-6 py-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $product)
                        <tr
                            class="even:bg-white even:dark:bg-gray-200 odd:bg-gray-50 odd:dark:bg-white dark:border-gray-700">
                            <td class="px-6 py-4">{{ $product->id }}</td>
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">
                                {{ $product->product_name }}</td>
                            <td class="px-6 py-4">{{ $product->generic_name }}</td>
                            <td class="px-6 py-4">{{ $product->category }}</td>
                            <td class="px-6 py-4">{{ Str::words($product->product_description, 3) }}</td>
                            <td class="px-6 py-4">₱{{ number_format($product->price, 2) }}</td>
                            <td class="px-6 py-4 flex items-center gap-2">
                                <x-tooltip message="Click to view the full details of the product">
                                    <a href="{{ route('products.show', $product->id) }}"
                                        class="font-medium text-blue-600 dark:text-blue-500 hover:underline m-1">View</a>
                                </x-tooltip>
                                <a href="{{ route('products.edit', $product->id) }}"
                                    class="font-medium text-blue-600 dark:text-blue-500 hover:underline m-1">Edit</a>

                                <form action="{{ route('products.destroy', $product) }}" method="post">
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
            {{ $products->appends(['search' => request('search')])->links() }}
        </div>
    </div>



</x-layout>
