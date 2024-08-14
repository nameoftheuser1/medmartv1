<x-layout>
    <div class="w-full px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow-md rounded-lg p-6">
            <div class="text-2xl font-bold">{{ $productBatch->product->product_name }}</div>
            <p class="mt-2">
                Supplier: {{ $productBatch->supplier->name ?? 'N/A' }}
            </p>

            <div class="mt-5">
                <p class="font-semibold">Batch Number:</p>
                <p>{{ $productBatch->batch_number }}</p>
            </div>
            <div class="mt-5">
                <p class="font-semibold">Expiration Date:</p>
                <p>{{ $productBatch->expiration_date->format('Y-m-d') }}</p>
            </div>
            <div class="mt-5">
                <p class="font-semibold">Supplier Price:</p>
                <p>₱{{ number_format($productBatch->supplier_price, 2) }}</p>
            </div>
            <div class="mt-5">
                <p class="font-semibold">Received Date:</p>
                <p>{{ $productBatch->received_date->format('Y-m-d') }}</p>
            </div>

            <div class="flex justify-between mt-5">
                <div class="mx-auto">
                    <p class="font-semibold">Created At:</p>
                    <p>{{ $productBatch->created_at->format('Y-m-d H:i:s') }}</p>
                </div>
                <div class="mx-auto">
                    <p class="font-semibold">Updated At:</p>
                    <p>{{ $productBatch->updated_at->format('Y-m-d H:i:s') }}</p>
                </div>
            </div>

            <div class="flex justify-between mt-5">
                <div class="mx-auto">
                    <a href="{{ route('product_batches.index') }}" class="btn text-lg">Go back</a>
                </div>
                <div class="mx-auto">
                    <a href="{{ route('product_batches.edit', $productBatch->id) }}" class="btn text-lg">Edit Product
                        Batch</a>
                </div>
            </div>
        </div>
    </div>
</x-layout>
