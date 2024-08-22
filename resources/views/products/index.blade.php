<x-layout>
    <div class="w-full px-4 sm:px-6 lg:px-8 bg-white p-5 rounded-lg shadow-lg">
        <div class="flex flex-col sm:flex-row justify-between items-center mb-5">
            <h1 class="text-2xl font-bold mb-2 sm:mb-0">Product List</h1>
            <p class="mb-2 sm:mb-0">@lang('message.total') Products: {{ $products->total() }}</p>
            <form method="GET" action="{{ route('products.index') }}" class="flex flex-col sm:flex-row w-full sm:w-auto">
                <input type="text" name="search" placeholder="Search..." value="{{ request('search') }}"
                    class="w-full sm:w-auto px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent mb-2 sm:mb-0">

                <select name="category"
                    class="w-full sm:w-auto px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent ml-2 mb-2 sm:mb-0">
                    <option value="">All Categories</option>
                    <option value="Medications" {{ request('category') == 'Medications' ? 'selected' : '' }}>Medications
                    </option>
                    <option value="Supplements" {{ request('category') == 'Supplements' ? 'selected' : '' }}>Supplements
                    </option>
                    <option value="Personal Care" {{ request('category') == 'Personal Care' ? 'selected' : '' }}>
                        Personal Care</option>
                    <option value="First Aid" {{ request('category') == 'First Aid' ? 'selected' : '' }}>First Aid
                    </option>
                    <option value="Medical Equipment"
                        {{ request('category') == 'Medical Equipment' ? 'selected' : '' }}>Medical Equipment</option>
                    <option value="Baby Products" {{ request('category') == 'Baby Products' ? 'selected' : '' }}>Baby
                        Products</option>
                    <option value="Health Devices" {{ request('category') == 'Health Devices' ? 'selected' : '' }}>
                        Health Devices</option>
                    <option value="Homeopathic Remedies"
                        {{ request('category') == 'Homeopathic Remedies' ? 'selected' : '' }}>Homeopathic Remedies
                    </option>
                    <option value="Herbal Products" {{ request('category') == 'Herbal Products' ? 'selected' : '' }}>
                        Herbal Products</option>
                    <option value="Skin Care" {{ request('category') == 'Skin Care' ? 'selected' : '' }}>Skin Care
                    </option>
                    <option value="Hair Care" {{ request('category') == 'Hair Care' ? 'selected' : '' }}>Hair Care
                    </option>
                    <option value="Oral Care" {{ request('category') == 'Oral Care' ? 'selected' : '' }}>Oral Care
                    </option>
                    <option value="Sexual Health" {{ request('category') == 'Sexual Health' ? 'selected' : '' }}>Sexual
                        Health</option>
                    <option value="Eye Care" {{ request('category') == 'Eye Care' ? 'selected' : '' }}>Eye Care
                    </option>
                    <option value="Ear Care" {{ request('category') == 'Ear Care' ? 'selected' : '' }}>Ear Care
                    </option>
                    <option value="Nutrition" {{ request('category') == 'Nutrition' ? 'selected' : '' }}>Nutrition
                    </option>
                    <option value="Wellness" {{ request('category') == 'Wellness' ? 'selected' : '' }}>Wellness
                    </option>
                </select>

                <button type="submit"
                    class="ml-2 px-4 py-2 bg-blue-700 text-white rounded-lg hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">Search</button>
            </form>
        </div>
        <div class="flex mb-5">
            <a href="{{ route('products.create') }}" class="btn text-lg w-full sm:w-auto text-center">@lang('message.add', ['item' => 'Product'])
            </a>
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
                        <th scope="col" class="px-4 py-3 sm:px-6">Product Name</th>
                        <th scope="col" class="px-4 py-3 sm:px-6 hidden sm:table-cell">Generic</th>
                        <th scope="col" class="px-4 py-3 sm:px-6 hidden sm:table-cell">Category</th>
                        <th scope="col" class="px-4 py-3 sm:px-6 hidden sm:table-cell">Description</th>
                        <th scope="col" class="px-4 py-3 sm:px-6">Price</th>
                        <th scope="col" class="px-4 py-3 sm:px-6">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $product)
                        <tr class="hover:bg-green-300 cursor-pointer transition duration-150 ease-in-out"
                            onclick="window.location='{{ route('products.show', $product->id) }}'">
                            <td class="px-4 py-4 sm:px-6 hidden md:table-cell">{{ $product->id }}</td>
                            <td class="px-4 py-4 sm:px-6 font-medium text-gray-900 whitespace-nowrap">
                                {{ Str::limit($product->product_name, 15) }}
                            </td>
                            <td class="px-4 py-4 sm:px-6 hidden sm:table-cell">
                                {{ Str::limit($product->generic_name, 15) }}</td>
                            <td class="px-4 py-4 sm:px-6 hidden sm:table-cell">
                                {{ Str::limit($product->category, 15) }}</td>
                            <td class="px-4 py-4 sm:px-6 hidden sm:table-cell">
                                {{ Str::limit($product->product_description, 20) }}
                            </td>
                            <td class="px-4 py-4 sm:px-6">₱{{ number_format($product->price, 2) }}</td>
                            <td class="px-4 py-4 sm:px-6">
                                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2">
                                    <x-tooltip message="Click to view the full details of the product">
                                        <a href="{{ route('products.show', $product->id) }}"
                                            class="font-medium text-blue-600 dark:text-blue-500 hover:underline"
                                            onclick="event.stopPropagation();">View</a>
                                    </x-tooltip>
                                    <a href="{{ route('products.edit', $product->id) }}"
                                        class="font-medium text-blue-600 dark:text-blue-500 hover:underline"
                                        onclick="event.stopPropagation();">Edit</a>
                                    <form action="{{ route('products.destroy', $product) }}" method="POST"
                                        onsubmit="event.stopPropagation();">
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
            {{ $products->appends(['search' => request('search')])->links('vendor.pagination.tailwind') }}
        </div>
    </div>
</x-layout>
