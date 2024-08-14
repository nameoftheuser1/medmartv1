<x-layout>
    <h1>Point of Sale</h1>

    @if (session('success'))
        <p class="text-green-500">{{ session('success') }}</p>
    @endif

    @if (session('error'))
        <p class="text-red-500">{{ session('error') }}</p>
    @endif

    <div class="card mb-3">
        <h2>Select Product</h2>

        <div class="mb-4">
            <input type="text" id="search-input" placeholder="Search products..." class="input w-full">
        </div>

        <div class="product-container overflow-y-auto max-h-96 flex flex-wrap">
            @foreach ($products as $product)
                <div class="product-card card sm:w-60 w-full border p-4 m-2 cursor-pointer" data-id="{{ $product->id }}"
                    data-name="{{ $product->product_name }}" data-price="{{ $product->price }}">
                    <div class="divide-y grid grid-cols-1 gap-2">
                        <h3 class="font-bold">{{ $product->product_name }}</h3>
                        @if ($product->generic_name)
                            <p>Generic Name: {{ $product->generic_name }}</p>
                        @else
                            <p class="text-gray-500 italic">No generic name</p>
                        @endif
                    </div>
                    <p>Available Inventory: {{ $product->total_inventory }}</p>
                    <p>₱{{ $product->price }}</p>
                </div>
            @endforeach
        </div>

        <form action="{{ route('pos.addItem') }}" method="POST" id="add-to-sale-form" class="hidden">
            @csrf
            <input type="hidden" name="product_id" id="selected-product-id">
            <div>
                <label for="quantity">Quantity:</label>
                <input type="number" name="quantity" id="quantity" class="input">
            </div>

            <div class="flex justify-center mt-4">
                <button type="submit" class="btn text-lg">Add to Sale</button>
            </div>
        </form>
    </div>

    <div class="card">
        <h2>Sale Items</h2>
        @if (!empty($saleDetails))
            @php
                $totalPrice = 0;
            @endphp
            <table class="w-full text-left rtl:text-right">
                <thead class="uppercase">
                    <tr>
                        <th scope="col" class="px-6 py-3">Product</th>
                        <th scope="col" class="px-6 py-3">Quantity</th>
                        <th scope="col" class="px-6 py-3">Price</th>
                        <th scope="col" class="px-6 py-3">Total</th>
                        <th scope="col" class="px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($saleDetails as $detail)
                        @php
                            $product = \App\Models\Product::find($detail['product_id']);
                            $totalPrice += $detail['quantity'] * $detail['price'];
                        @endphp
                        <tr
                            class="even:bg-white even:dark:bg-gray-200 odd:bg-gray-50 odd:dark:bg-white dark:border-gray-700">
                            <td class="px-6 py-4">{{ $product->product_name }}</td>
                            <td class="px-6 py-4">{{ $detail['quantity'] }}</td>
                            <td class="px-6 py-4">₱{{ $detail['price'] }}</td>
                            <td class="px-6 py-4">₱{{ $detail['quantity'] * $detail['price'] }}</td>
                            <td class="px-6 py-4 flex gap-2">
                                <form action="{{ route('pos.removeItem') }}" method="POST" style="display:inline;">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $detail['product_id'] }}">
                                    <button type="submit"
                                        class="font-medium text-red-600 dark:text-red-500 hover:underline m-1">Remove</button>
                                </form>
                                <form action="{{ route('pos.updateItem') }}" method="POST" style="display:inline;">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $detail['product_id'] }}">
                                    <input type="number" name="quantity" value="{{ $detail['quantity'] }}"
                                        min="1" class="px-2 py-1 border rounded">
                                    <button type="submit"
                                        class="font-medium text-blue-600 dark:text-blue-500 hover:underline m-1">Update</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-4">
                <h3>Total Price: ₱{{ $totalPrice }}</h3>
            </div>

            <form action="{{ route('pos.checkout') }}" method="POST">
                @csrf
                <button type="submit" class="px-6 py-2 mt-4 bg-blue-600 text-white rounded">Checkout</button>
            </form>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const productCards = document.querySelectorAll('.product-card');
            const addToSaleForm = document.getElementById('add-to-sale-form');
            const selectedProductIdInput = document.getElementById('selected-product-id');
            const searchInput = document.getElementById('search-input');

            // Search functionality
            searchInput.addEventListener('input', function() {
                const query = this.value.toLowerCase();
                productCards.forEach(card => {
                    const productName = card.getAttribute('data-name').toLowerCase();
                    if (productName.includes(query)) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });

            productCards.forEach(card => {
                card.addEventListener('click', function() {
                    const productId = this.getAttribute('data-id');
                    selectedProductIdInput.value = productId;
                    addToSaleForm.classList.remove('hidden');
                });
            });
        });
    </script>

    <style>
        .product-container {
            max-height: 24rem;
        }
    </style>
</x-layout>
