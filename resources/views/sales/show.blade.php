<x-layout>
    <div class="card">
        <div>
            <div class="card-title text-center m-5">Sale ID: {{ $sale->id }}</div>
            <p>Transaction Key: {{ $sale->transaction_key }}</p>
        </div>

        <div class="mt-5">
            <p>Total Amount: ₱{{ $sale->total_amount }}</p>
        </div>

        @if ($sale->refunded != 0)
            <div class="mt-5">
                <p>Refunded Amount: ₱{{ $sale->refunded }}</p>
            </div>
        @endif

        <div class="mt-5">
            <p>Discount: {{ $sale->discount_percentage }}%</p>
        </div>

        <div class="flex justify-between mt-5">
            <div class="mx-auto">
                <p>Created At: {{ $sale->created_at }} </p>
            </div>
            <div class="mx-auto">
                <p>Updated At: {{ $sale->updated_at }} </p>
            </div>
        </div>

        <div class="mt-5">
            <h3 class="text-xl">Sale Details</h3>
            <table class="table-auto w-full mt-5">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Quantity</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($sale->saleDetails as $detail)
                        <tr>
                            <td>{{ $detail->product->product_name }}</td>
                            <td class="text-center">{{ $detail->quantity }}</td>
                            <td class="text-center">₱{{ $detail->price }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="flex justify-between mt-5">
            <div class="mx-auto">
                <a href="{{ route('sales.index') }}" class="btn text-lg">Go back</a>
            </div>
        </div>
    </div>
</x-layout>
