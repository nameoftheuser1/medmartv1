<div class="relative overflow-x-auto sm:rounded-lg">
    @if ($products->isEmpty())
        <p class="py-5 text-center text-gray-500">Wow, this table is empty.</p>
    @else
        <table class="w-full text-left rtl:text-right">
            <thead class="uppercase">
                <tr>
                    <th scope="col" class="px-4 py-3 sm:px-6">Product Name</th>
                    <th scope="col" class="hidden px-4 py-3 sm:px-6 sm:table-cell">Generic</th>
                    <th scope="col" class="hidden px-4 py-3 sm:px-6 sm:table-cell">Category</th>
                    <th scope="col" class="hidden px-4 py-3 sm:px-6 sm:table-cell">Description</th>
                    <th scope="col" class="px-4 py-3 sm:px-6">Price</th>
                    <th scope="col" class="px-4 py-3 sm:px-6">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                    <tr class="transition duration-150 ease-in-out cursor-pointer hover:bg-green-300"
                        onclick="window.location='{{ route('products.show', $product->id) }}'">
                        <td class="px-4 py-4 font-medium text-gray-900 sm:px-6 whitespace-nowrap">
                            {{ Str::limit($product->product_name, 15) }}
                        </td>
                        <td class="hidden px-4 py-4 sm:px-6 sm:table-cell">
                            {{ Str::limit($product->generic_name, 15) }}</td>
                        <td class="hidden px-4 py-4 sm:px-6 sm:table-cell">
                            {{ Str::limit($product->category, 15) }}</td>
                        <td class="hidden px-4 py-4 sm:px-6 sm:table-cell">
                            {{ Str::limit($product->product_description, 20) }}
                        </td>
                        <td class="flex px-4 py-4 sm:px-6">
                            ₱{{ number_format($product->price, 2) }}
                        </td>
                        <td class="px-4 py-4 sm:px-6">
                            <div class="flex flex-col items-start gap-2 sm:flex-row sm:items-center">
                                <a href="{{ route('products.show', $product->id) }}"
                                    class="font-medium text-blue-600 dark:text-blue-500 hover:underline"
                                    onclick="event.stopPropagation();">View</a>
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
    @endif
</div>
