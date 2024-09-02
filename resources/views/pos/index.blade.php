<x-layout>
    <h1>Point of Sale</h1>

    @if (session('success'))
        <p class="text-green-500">{{ session('success') }}</p>
    @endif

    @if (session('error'))
        <p class="text-red-500">{{ session('error') }}</p>
    @endif

    <div class="mb-3 rounded-lg card">
        <h2>Select Product</h2>

        <div class="mb-4">
            <input type="text" id="search-input" placeholder="Search products..." class="w-full m-1 input">
        </div>

        <div class="product-container overflow-y-auto max-h-[1000px] flex flex-wrap">
            @foreach ($products as $product)
                <div class="w-full p-4 m-2 border rounded-lg cursor-pointer product-card card sm:w-60 bg-slate-100"
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
                    <div class="flex justify-between mb-3">
                        <p class="text-sm">Available Inventory: {{ $product->total_inventory }}</p>
                    </div>
                    <p class="text-center">₱{{ number_format($product->price, 2) }}</p>
                </div>
            @endforeach
        </div>
        <form action="{{ route('pos.addItem') }}" method="POST" id="add-to-sale-form" class="hidden w-1/2 mx-auto">
            @csrf
            <input type="hidden" name="product_id" id="selected-product-id">
            <div>
                <label for="quantity">Quantity:</label>
                <input type="number" name="quantity" id="quantity" class="input">
            </div>

            <div class="flex justify-center mt-4">
                <button type="submit" class="text-lg btn">Add to Cart</button>
            </div>
        </form>
    </div>

    <div class="rounded-lg card min-h-96">
        <h2>Cart Items</h2>
        @if (!empty($saleDetails))
            @php
                $totalPrice = 0;
            @endphp
            <div class="overflow-x-auto sm:overflow-x-visible">
                <table class="w-full text-left rtl:text-right">
                    <thead class="uppercase">
                        <tr class="sm:hidden">
                            <th scope="col" class="px-2 py-3">Product Details</th>
                        </tr>
                        <tr class="hidden sm:table-row">
                            <th scope="col" class="px-2 py-3 sm:px-4">Product</th>
                            <th scope="col" class="px-2 py-3 sm:px-4">Qty</th>
                            <th scope="col" class="px-2 py-3 sm:px-4">Price</th>
                            <th scope="col" class="px-2 py-3 sm:px-4">Total</th>
                            <th scope="col" class="px-2 py-3 sm:px-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($saleDetails as $detail)
                            @php
                                $product = \App\Models\Product::find($detail['product_id']);
                                $totalPrice += $detail['quantity'] * $detail['price'];
                            @endphp
                            <tr
                                class="block mb-4 even:bg-white even:dark:bg-gray-200 odd:bg-gray-50 odd:dark:bg-white dark:border-gray-700 sm:table-row sm:mb-0">
                                <td class="flex flex-col px-2 py-2 sm:px-4 sm:py-4 sm:table-cell">
                                    <span class="font-bold sm:hidden">Product:</span>
                                    {{ $product->product_name }}
                                    <div class="mt-2 sm:hidden">
                                        <span class="font-bold">Quantity:</span> {{ $detail['quantity'] }}<br>
                                        <span class="font-bold">Price:</span>
                                        ₱{{ number_format($detail['price']) }}<br>
                                        <span class="font-bold">Total:</span>
                                        ₱{{ $detail['quantity'] * $detail['price'] }}
                                    </div>
                                </td>
                                <td class="hidden px-2 py-2 sm:px-4 sm:py-4 sm:table-cell">{{ $detail['quantity'] }}
                                </td>
                                <td class="hidden px-2 py-2 sm:px-4 sm:py-4 sm:table-cell">
                                    ₱{{ number_format($detail['price'], 2) }}</td>
                                <td class="hidden px-2 py-2 sm:px-4 sm:py-4 sm:table-cell">
                                    ₱{{ number_format($detail['quantity'] * $detail['price'], 2) }}</td>
                                <td class="flex flex-col gap-3 px-2 py-2 sm:px-4 sm:py-4 sm:flex-row">
                                    <form action="{{ route('pos.removeItem') }}" method="POST" class="inline">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $detail['product_id'] }}">
                                        <button type="submit"
                                            class="p-2 font-medium text-white bg-red-700 rounded-lg hover:underline"><svg
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-4">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
                                        </button>
                                    </form>
                                    <form action="{{ route('pos.updateItem') }}" method="POST"
                                        class="flex-col items-start inline gap-2 sm:flex-row sm:items-center">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $detail['product_id'] }}">
                                        <input type="number" name="quantity" value="{{ $detail['quantity'] }}"
                                            min="1" class="w-20 px-2 py-1 border rounded">
                                        <button type="submit"
                                            class="p-2 font-medium text-white bg-green-600 rounded-lg hover:underline"><svg
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke-width="1.5" stroke="currentColor" class="size-4">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                                            </svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <form action="{{ route('pos.applyDiscount') }}" method="POST">
                @csrf
                <div>
                    <label for="discount_percentage">Discount (%):</label>
                    <input type="number" name="discount_percentage" id="discount_percentage" class="m-2 input"
                        value="{{ $discountPercentage }}" min="0" max="100">
                </div>
                <div class="flex justify-center mt-4">
                    <button type="submit" class="text-lg btn">Apply Discount</button>
                </div>
            </form>

            <div class="mt-4">
                <h3>Total Price: ₱{{ number_format($totalPrice, 2) }}</h3>
                <h3>Discount: {{ $discountPercentage }}%</h3>
                <h3>Final Price: ₱{{ number_format($totalPrice * (1 - $discountPercentage / 100), 2) }}</h3>
            </div>

            <form action="{{ route('pos.checkout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full px-6 py-2 mt-4 text-white bg-blue-600 rounded">Checkout</button>
            </form>
        @endif
    </div>

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
                    if (productName.includes(query)) {
                        card.classList.remove('hidden');
                    } else {
                        card.classList.add('hidden');
                    }
                });
            });

            productCards.forEach(card => {
                card.addEventListener('click', function() {
                    productCards.forEach(card => card.classList.remove('ring', 'ring-green-500',
                        'bg-green-50'));

                    this.classList.add('ring', 'ring-green-500', 'bg-green-50');

                    const productId = this.getAttribute('data-id');
                    selectedProductIdInput.value = productId;
                    addToSaleForm.classList.remove('hidden');
                });
            });
        });
    </script>
</x-layout>
