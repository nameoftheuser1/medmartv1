<!-- resources/views/partials/_table.blade.php -->
<div class="relative overflow-x-auto sm:rounded-lg">
    @if ($inventories->isEmpty())
        <p class="text-center py-5 text-gray-500">Wow, this table is empty.</p>
    @else
        <table class="w-full text-left rtl:text-right">
            <thead class="uppercase">
                <tr>
                    <th scope="col" class="px-4 py-3 sm:px-6 hidden md:table-cell">Product Name</th>
                    <th scope="col" class="px-4 py-3 sm:px-6 ">Batch Number</th>
                    <th scope="col" class="px-4 py-3 sm:px-6 hidden lg:table-cell">Expiration Date</th>
                    <th scope="col" class="px-4 py-3 sm:px-6 hidden lg:table-cell">Supplier Price</th>
                    <th scope="col" class="px-4 py-3 sm:px-6 hidden md:table-cell">Received Date</th>
                    <th scope="col" class="px-4 py-3 sm:px-6">Quantity</th>
                    <th scope="col" class="px-4 py-3 sm:px-6">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($inventories as $inventory)
                    @php
                        $today = \Carbon\Carbon::today();
                        $expirationDate = \Carbon\Carbon::parse($inventory->productBatch->expiration_date);
                        $daysToExpiry = $today->diffInDays($expirationDate);
                    @endphp
                    <tr
                        class="even:bg-white even:dark:bg-gray-200 odd:bg-gray-50 odd:dark:bg-white dark:border-gray-700">
                        <td class="px-4 py-4 sm:px-6 font-medium text-gray-900 whitespace-nowrap hidden md:table-cell">
                            {{ $inventory->productBatch->product->product_name }}
                        </td>
                        <td class="px-4 py-4 sm:px-6">
                            {{ $inventory->productBatch->batch_number }}
                        </td>
                        <td class="px-4 py-4 sm:px-6 hidden lg:table-cell">
                            <span
                                class="
                                @if ($daysToExpiry <= 0) text-red-500
                                @elseif($daysToExpiry <= 7)
                                    text-red-500
                                @elseif($daysToExpiry <= 30)
                                    text-yellow-500
                                @else
                                    text-black @endif
                            ">
                                {{ $inventory->productBatch->expiration_date->format('Y-m-d') }}
                            </span>
                        </td>
                        <td class="px-4 py-4 sm:px-6 hidden lg:table-cell">
                            ₱{{ number_format($inventory->productBatch->supplier_price, 2) }}
                        </td>
                        <td class="px-4 py-4 sm:px-6 hidden md:table-cell">
                            {{ $inventory->productBatch->received_date->format('Y-m-d') }}
                        </td>
                        <td class="px-4 py-4 sm:px-6">
                            <span
                                class="{{ $inventory->isOutOfStock ? 'text-red-500' : ($inventory->isLowStock ? 'text-yellow-500' : '') }}">
                                {{ $inventory->quantity }}
                            </span>
                        </td>
                        <td class="px-4 py-4 sm:px-6">
                            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2">
                                <a href="{{ route('inventories.edit', $inventory->id) }}"
                                    class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Edit</a>
                                <form action="{{ route('inventories.destroy', $inventory) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="font-medium text-red-600 dark:text-red-500 hover:underline">Delete</button>
                                </form>

                                @if ($inventory->quantity > 0)
                                    <form action="{{ route('inventories.emptyQuantity', $inventory) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="font-medium text-yellow-600 dark:text-yellow-500 hover:underline">Empty
                                            Quantity</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</div>

<div class="mt-4">
    {{ $inventories->appends(['search' => request('search')])->links('vendor.pagination.tailwind') }}
</div>
