<x-layout>
    <h1>Point of Sale</h1>

    @if (session('success'))
        <p class="text-green-500">{{ session('success') }}</p>
    @endif

    @if (session('error'))
        <p class="text-red-500">{{ session('error') }}</p>
    @endif

    <div class="flex flex-col sm:flex-row  w-full gap-2">
        <div class="rounded-lg card w-full ">
            <h2>Select Product</h2>
            <div class="mb-4">
                <input type="text" id="search-input" placeholder="Search products..." class="w-full m-1 input">
            </div>
            <div class="product-container overflow-y-auto max-h-[1000px] flex flex-wrap">
                @foreach ($products as $product)
                    <div class="w-full p-4 m-2 border rounded-lg cursor-pointer product-card card sm:w-60 bg-slate-100 flex flex-col justify-between"
                        data-id="{{ $product->id }}" data-name="{{ $product->product_name }}"
                        data-price="{{ $product->price }}">
                        <div class="grid grid-cols-1 gap-2 divide-y">
                            <h3 class="text-xl font-bold">{{ strtoupper($product->product_name) }}</h3>
                            @if ($product->generic_name)
                                <p class="text-sm text-gray-500">{{ $product->generic_name }}</p>
                            @else
                                <p class="italic text-gray-500">No generic name</p>
                            @endif
                        </div>
                        <div class="flex-grow"></div>
                        <p class="text-sm">
                            Available Inventory:
                            <span class="font-bold text-lg {{ $product->total_inventory < 20 ? 'text-red-500' : '' }}">
                                {{ $product->total_inventory }}
                            </span>
                        </p>
                        <p class="text-center text-lg">₱{{ number_format($product->price, 2) }}</p>
                    </div>
                @endforeach
            </div>
            <div class="mt-4">
                {{ $products->links() }}
            </div>
            <form action="{{ route('pos.addItem') }}" method="POST" id="add-to-sale-form"
                class="hidden w-1/2 mx-auto">
                @csrf
                <input type="hidden" name="product_id" id="selected-product-id">
                <div>
                    <label for="quantity">Quantity:</label>
                    <input type="number" name="quantity" id="quantity" class="input" min="1" required>
                </div>

                <div class="flex justify-center mt-4">
                    <button type="submit" class="text-lg btn">Add to Cart</button>
                </div>
            </form>
        </div>

        <div class="rounded-lg card min-h-96 w-full lg:w-1/2 ">
            <h2>Cart Items</h2>
            @if ($cartItems->isNotEmpty())
                @php
                    $totalPrice = 0;
                @endphp
                <div class="overflow-x-auto max-h-[1000px]">
                    <div class="grid">
                        @foreach ($cartItems as $item)
                            @php
                                $totalPrice += $item->quantity * $item->price;
                            @endphp
                            <div class="bg-white shadow-md rounded-lg p-4">
                                <h3 class="text-lg font-semibold text-gray-800">{{ $item->product->product_name }}</h3>
                                <div class="flex gap-3 mt-2 text-gray-600">
                                    <p><span class="font-bold">Quantity:</span> {{ $item->quantity }}</p>
                                    <p><span class="font-bold">Price:</span> ₱{{ number_format($item->price, 2) }}</p>
                                    <p><span class="font-bold">Total:</span>
                                        ₱{{ number_format($item->quantity * $item->price, 2) }}</p>
                                </div>
                                <div class="mt-4 flex gap-2">
                                    <!-- Remove button -->
                                    <form action="{{ route('pos.removeItem') }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                                        <button type="submit"
                                            class="w-full px-4 py-2 text-sm text-white bg-red-700 rounded-lg hover:bg-red-800">
                                            Remove
                                        </button>
                                    </form>
                                    <!-- Update button -->
                                    <form action="{{ route('pos.updateItem') }}" method="POST"
                                        class="flex items-center">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                                        <input type="number" name="quantity" value="{{ $item->quantity }}"
                                            min="1" class="w-16 px-2 py-1 border rounded" required>
                                        <button type="submit"
                                            class="ml-2 px-4 py-2 text-sm text-white bg-green-600 rounded-lg hover:bg-green-700">
                                            Update
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    @if ($cartItems->isNotEmpty())
        <div class="flex bg-white w-full p-2 gap-5 rounded-lg me-5 mt-2">
            <form action="{{ route('pos.applyDiscount') }}" method="POST">
                @csrf
                <label for="discount_percentage">Discount (%):</label>
                <input type="number" name="discount_percentage" id="discount_percentage" class="m-2 input"
                    value="{{ $discountPercentage }}" min="0" max="100">
                <div class="flex justify-center mt-4">
                    <button type="submit" class="text-lg btn">Apply Discount</button>
                </div>
            </form>
            <div class="mt-4 flex flex-col gap-3">
                <h3>Total Price: ₱{{ number_format($totalPrice, 2) }}</h3>
                <h3>Discount: {{ $discountPercentage }}%</h3>
                <h3>Final Price: ₱{{ number_format($totalPrice * (1 - $discountPercentage / 100), 2) }}</h3>
            </div>

            <form action="{{ route('pos.checkout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full px-6 py-2 mt-4 text-white bg-blue-600 rounded">Checkout</button>
            </form>
        </div>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const productCards = document.querySelectorAll('.product-card');
            const addToSaleForm = document.getElementById('add-to-sale-form');
            const selectedProductIdInput = document.getElementById('selected-product-id');
            const searchInput = document.getElementById('search-input');

            searchInput.addEventListener('input', function() {
                const query = this.value.toLowerCase();
                productCards.forEach(card => {
                    const productName = card.getAttribute('data-name').toLowerCase();
                    card.classList.toggle('hidden', !productName.includes(query));
                });
            });

            productCards.forEach(card => {
                card.addEventListener('click', function() {
                    productCards.forEach(card => card.classList.remove('ring', 'ring-green-500',
                        'bg-green-50'));
                    this.classList.add('ring', 'ring-green-500', 'bg-green-50');

                    selectedProductIdInput.value = this.getAttribute('data-id');
                    addToSaleForm.classList.remove('hidden');
                });
            });
        });
    </script>
</x-layout>
