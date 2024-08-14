<x-layout>
    <h1 class="mb-4 text-2xl font-bold">Dashboard</h1>
    <div class="mb-6 bg-white rounded-lg shadow-sm p-4">
        <h2 class="text-xl font-bold mb-2 font-mono">Products About to Expire</h2>
        <p class="mb-5 text-gray-500 text-sm">Here showing the products that are about to expire in 30 days</p>
        @if ($expiringBatches->isEmpty())
            <p class="text-gray-200">No products are about to expire within the next 30 days.</p>
        @else
            <div class="overflow-x-auto ">
                <table class="min-w-full">
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
                                <td class="px-4 py-2 border text-center">{{ $batch->batch_number }}</td>
                                <td class="px-4 py-2 border text-center">{{ $batch->expiration_date->format('Y-m-d') }}</td>
                                <td class="px-4 py-2 border text-center">{{ $batch->inventories->sum('quantity') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $expiringBatches->links() }}
            </div>
        @endif
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        <div class="card p-4 bg-white shadow rounded">
            <p class="text-lg font-semibold">Product Count:</p>
            <h1 class="text-3xl font-bold text-center mt-2">
                {{ $productCount }}
            </h1>
        </div>
        <div class="card p-4 bg-white shadow rounded">
            <p class="text-lg font-semibold">Supplier Count:</p>
            <h1 class="text-3xl font-bold text-center mt-2">
                {{ $supplierCount }}
            </h1>
        </div>
        <div class="card p-4 bg-white shadow rounded">
            <p class="text-lg font-semibold">Total Sales Today:</p>
            <h1 class="text-3xl font-bold text-center mt-2">
                {{ $totalSalesToday }}
            </h1>
        </div>
    </div>
</x-layout>
