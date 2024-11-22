@php
    use Carbon\Carbon;
@endphp

@if ($productBatches->isEmpty())
    <p class="py-5 text-center text-gray-500">No product batches found.</p>
@else
    <table class="w-full text-left rtl:text-right">
        <thead class="bg-gray-100 uppercase">
            <tr>
                <th scope="col" class="px-4 py-3 sm:px-6 hidden md:table-cell">Product</th>
                <th scope="col" class="px-4 py-3 sm:px-6">Batch Number</th>
                <th scope="col" class="px-4 py-3 sm:px-6 hidden lg:table-cell">Expiration Date</th>
                <th scope="col" class="px-4 py-3 sm:px-6 hidden lg:table-cell">Supplier Price</th>
                <th scope="col" class="px-4 py-3 sm:px-6 hidden lg:table-cell">Product Price</th>
                <th scope="col" class="px-4 py-3 sm:px-6 hidden md:table-cell">Received Date</th>
                <th scope="col" class="px-4 py-3 sm:px-6">Action</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($productBatches as $productBatch)
                @php
                    $expirationDate = $productBatch->expiration_date;
                    $isExpired = $expirationDate->isPast();
                    $isNearExpiry = !$isExpired && $expirationDate->diffInDays(Carbon::today()) <= 30;
                @endphp
                <tr class="hover:bg-green-300 cursor-pointer transition duration-150 ease-in-out"
                    onclick="window.location='{{ route('product_batches.show', $productBatch->id) }}'">
                    <td class="px-4 py-4 sm:px-6 font-medium text-gray-900 whitespace-nowrap hidden md:table-cell">
                        {{ $productBatch->product->product_name }}
                    </td>
                    <td class="px-4 py-4 sm:px-6">{{ $productBatch->batch_number }}</td>
                    <td class="px-4 py-4 sm:px-6 hidden lg:table-cell">
                        <span class="{{ $isExpired ? 'text-red-500' : ($isNearExpiry ? 'text-black-500' : '') }}">
                            {{ $expirationDate->format('Y-m-d') }}
                        </span>
                    </td>
                    <td class="px-4 py-4 sm:px-6 hidden lg:table-cell">
                        ₱{{ number_format($productBatch->supplier_price, 2) }}
                    </td>
                    <td class="px-4 py-4 sm:px-6 hidden lg:table-cell">
                        ₱{{ number_format($productBatch->product->price, 2) }}
                    </td>
                    <td class="px-4 py-4 sm:px-6 hidden md:table-cell">
                        {{ $productBatch->received_date->format('Y-m-d') }}
                    </td>
                    <td class="px-4 py-4 sm:px-6">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-2">
                            <a href="{{ route('product_batches.show', $productBatch->id) }}"
                                class="font-medium text-blue-600 dark:text-blue-500 hover:underline">View</a>
                            <a href="{{ route('product_batches.edit', $productBatch->id) }}"
                                class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Edit</a>
                            <form action="{{ route('product_batches.destroy', $productBatch) }}" method="post">
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
