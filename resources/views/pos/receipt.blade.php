<x-layout>
    <!-- Receipt Layout -->
    <div class="max-w-md mx-auto bg-white shadow-lg rounded-lg overflow-hidden">
        <!-- Header Section -->
        <div class="bg-gray-800 text-white py-4 px-6 text-center">
            <h1 class="text-2xl font-bold">Receipt</h1>
        </div>

        <!-- Receipt Details Section -->
        <div class="p-6 space-y-4">
            <!-- Date and Cashier Information -->
            <div class="flex justify-between">
                <p><strong>Date:</strong></p>
                <p>{{ $sale->created_at->format('Y-m-d H:i:s') }}</p>
            </div>
            <div class="flex justify-between">
                <p><strong>Cashier:</strong></p>
                <p>{{ $sale->user->name }}</p>
            </div>

            <!-- Product Details Table -->
            <table class="w-full border-collapse mt-4">
                <thead>
                    <tr>
                        <th class="border-b py-2 px-4 text-left">Product</th>
                        <th class="border-b py-2 px-4 text-left">Quantity</th>
                        <th class="border-b py-2 px-4 text-left">Price</th>
                        <th class="border-b py-2 px-4 text-left">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalQuantity = 0;
                        $totalAmount = 0;
                    @endphp
                    @foreach ($saleDetails as $detail)
                        @php
                            $totalQuantity += $detail->quantity;
                            $totalAmount += $detail->quantity * $detail->price;
                        @endphp
                        <tr>
                            <td class="border-b py-2 px-4">{{ $detail->product->product_name }}</td>
                            <td class="border-b py-2 px-4">{{ $detail->quantity }}</td>
                            <td class="border-b py-2 px-4">{{ number_format($detail->price, 2) }}</td>
                            <td class="border-b py-2 px-4">{{ number_format($detail->quantity * $detail->price, 2) }}</td>
                        </tr>
                    @endforeach
                    <!-- Row for Total Quantity and Amount -->
                    <tr>
                        <td class="border-b py-2 px-4 font-bold">TOTAL</td>
                        <td class="border-b py-2 px-4 font-bold">{{ $totalQuantity }}</td>
                        <td class="border-b py-2 px-4"></td>
                        <td class="border-b py-2 px-4 font-bold">{{ number_format($totalAmount, 2) }}</td>
                    </tr>
                </tbody>
            </table>

            <!-- Discount and Financial Summary Section -->
            <div class="space-y-2 mt-4">
                <div class="flex justify-between">
                    <p><strong>Discount:</strong></p>
                    <p>{{ $sale->discount_percentage }}%</p>
                </div>
                <div class="flex justify-between">
                    <p><strong>Total Amount:</strong></p>
                    <p>{{ number_format($sale->total_amount, 2) }}</p>
                </div>
                <div class="flex justify-between">
                    <p><strong>Exchange:</strong></p>
                    <p>{{ number_format($sale->exchange, 2) }}</p>
                </div>
                <div class="flex justify-between font-bold">
                    <p><strong>Change:</strong></p>
                    <p>{{ number_format($sale->exchange - $sale->total_amount, 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Back Button -->
    <div class="p-6 flex justify-center">
        <a href="{{ url()->previous() }}" class="text-white hover:underline rounded-lg bg-slate-500 p-4">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
</x-layout>
