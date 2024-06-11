<x-layout>
    <h1 class="mb-4">Dashboard</h1>

    <div class="mb-6">
        <h2 class="text-xl font-bold mb-2">Products About to Expire</h2>
        <p class="mb-5">Here showing the products that are about to expire in 30 days</p>
        @if ($expiringBatches->isEmpty())
            <p>No products are about to expire within the next 30 days.</p>
        @else
            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="px-4 py-2 border">Product Name</th>
                        <th class="px-4 py-2 border">Batch Number</th>
                        <th class="px-4 py-2 border">Expiration Date</th>
                        <th class="px-4 py-2 border">Quantity</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($expiringBatches as $batch)
                        <tr>
                            <td class="px-4 py-2 border">{{ $batch->product->product_name }}</td>
                            <td class="px-4 py-2 border">{{ $batch->batch_number }}</td>
                            <td class="px-4 py-2 border">{{ $batch->expiration_date->format('Y-m-d') }}</td>
                            <td class="px-4 py-2 border">{{ $batch->inventories->sum('quantity') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="mt-4">
                {{ $expiringBatches->links() }}
            </div>
        @endif
    </div>

    <div class="flex gap-3 mb-6">
        <div class="card w-96">
            <p>Product Count:</p>
            <h1 class="text-xlg text-center">
                {{ $productCount }}
            </h1>
        </div>
        <div class="card w-96">
            <p>Supplier Count:</p>
            <h1 class="text-xlg text-center">
                {{ $supplierCount }}
            </h1>
        </div>
        <div class="card w-96">
            <p>Total Sales Today:</p>
            <h1 class="text-xlg text-center">
                {{ $totalSalesToday }}
            </h1>
        </div>
    </div>
</x-layout>
