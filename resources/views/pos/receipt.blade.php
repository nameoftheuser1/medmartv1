<x-layout>
    <!-- Back Button -->

    <div class="max-w-md mx-auto bg-white shadow-lg rounded-lg overflow-hidden">
        <div class="bg-gray-800 text-white py-4 px-6 text-center">
            <h1 class="text-2xl font-bold text-white">Receipt</h1>
        </div>



        <div class="p-6 space-y-4">
            <div class="flex justify-between">
                <p>Date:</p>
                <p>{{ $sale->created_at->format('Y-m-d H:i:s') }}</p>
            </div>
            <div class="flex justify-between">
                <p>Cashier:</p>
                <p>{{ $sale->user->name }}</p>
            </div>

            <table class="w-full border-collapse">
                <thead>
                    <tr>
                        <th class="border-b py-2 px-4 text-left">Product</th>
                        <th class="border-b py-2 px-4 text-left">Quantity</th>
                        <th class="border-b py-2 px-4 text-left">Price</th>
                        <th class="border-b py-2 px-4 text-left">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($saleDetails as $detail)
                        <tr>
                            <td class="border-b py-2 px-4">{{ $detail->product->product_name }}</td>
                            <td class="border-b py-2 px-4">{{ $detail->quantity }}</td>
                            <td class="border-b py-2 px-4">{{ number_format($detail->price, 2) }}</td>
                            <td class="border-b py-2 px-4">{{ number_format($detail->quantity * $detail->price, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="space-y-2">
                <div class="flex justify-between">
                    <p>Discount:</p>
                    <p>{{ $sale->discount_percentage }}%</p>
                </div>
                <div class="flex justify-between">
                    <p>Total Amount:</p>
                    <p>{{ number_format($sale->total_amount, 2) }}</p>
                </div>
                <div class="flex justify-between">
                    <p>Exchange:</p>
                    <p>{{ number_format($sale->exchange, 2) }}</p>
                </div>
                <div class="flex justify-between font-bold">
                    <p>Change:</p>
                    <p>{{ number_format($sale->exchange - $sale->total_amount, 2) }}</p>
                </div>
            </div>
        </div>
    </div>
    <div class="p-6 flex justify-center">
        <a href="{{ url()->previous() }}" class="text-white hover:underline rounded-lg bg-slate-500 p-4">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
</x-layout>
