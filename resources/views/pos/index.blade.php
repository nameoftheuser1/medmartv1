<x-layout>
    <h1>Point of Sale</h1>



    <div class="flex flex-col sm:flex-row w-full gap-2">
        <div class="rounded-lg card w-full">
            @if (session('success'))
                <p class="text-green-500">{{ session('success') }}</p>
            @endif
            @if ($errors->any())
            <ul class="text-red-500">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        @endif
            @if (session('error'))
                <p class="text-red-500">{{ session('error') }}</p>

            @endif
            <h2>Select Product</h2>
            <div class="mb-4">
                <input type="text" id="search-input" placeholder="Search products..." class="w-full m-1 input">
            </div>
            <div class="product-container overflow-y-auto max-h-[1000px] flex flex-wrap">
                @foreach ($products as $product)
                    @include('pos.partials.products', ['product' => $product])
                @endforeach
            </div>
            <div class="mt-4">
                {{ $products->appends(request()->input())->links() }}
            </div>

            <!-- Hidden Modal -->
            <div id="quantity-modal" class="fixed inset-0 flex items-center justify-center hidden">
                <div class="bg-white rounded-lg p-6 w-96">
                    <h3 class="text-xl font-semibold">Select Quantity</h3>
                    <form action="{{ route('pos.addItem') }}" method="POST" id="add-to-sale-form" class="mt-4">
                        @csrf
                        <input type="hidden" name="product_id" id="selected-product-id">

                        <div>
                            <label for="quantity" class="block text-sm font-medium text-gray-700">Quantity:</label>
                            <input type="number" name="quantity" id="quantity" class="input mt-1 w-full"
                                min="1" max="9999999999" required oninput="checkQuantity(this)">
                            <p id="quantity-error" class="text-red-500 text-sm hidden">Quantity must be up to 10 digits.</p>
                        </div>

                        <div class="flex justify-center mt-4">
                            <button type="submit" class="text-lg btn">Add to Cart</button>
                        </div>
                    </form>
                    <div class="mt-4 text-center">
                        <button id="close-modal" class="text-sm text-black-500">Close</button>
                    </div>
                </div>
            </div>
        </div>
        <script>
            function checkQuantity(input) {
                const quantityError = document.getElementById('quantity-error');
                const quantityValue = input.value;

                if (quantityValue.length > 10) {
                    quantityError.classList.remove('hidden');
                    input.setCustomValidity("Quantity cannot exceed 10 digits.");
                } else {
                    quantityError.classList.add('hidden');
                    input.setCustomValidity("");
                }
            }
        </script>

        <div class="rounded-lg card min-h-96 w-full lg:w-1/2 ">
            <h2>Cart Items</h2>
            @if ($cartItems->isNotEmpty())
                @php
                    $totalPrice = 0;
                    $totalQuantity = 0; // Initialize total quantity
                @endphp
                <div class="grid">

                    @foreach ($cartItems as $item)
                        @php
                            $totalPrice += $item->quantity * $item->price;
                            $totalQuantity += $item->quantity; // Add item quantity to total quantity
                        @endphp
                        <div class="mt-4">
                            <p class="font-semibold">Total Items: {{ $totalQuantity }}</p> <!-- Display total items -->
                        </div>
                        @include('pos.partials.cart', ['item' => $item])
                    @endforeach

                    <div class="mt-4">
                        <form action="{{ route('pos.removeAllItems') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="px-6 py-2 text-white bg-red-600 rounded w-full">Remove All
                                Items</button>
                        </form>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @if ($cartItems->isNotEmpty())
        <div class="flex bg-white w-full p-2 gap-5 rounded-lg me-5 mt-2">
            <form action="{{ route('pos.applyDiscount') }}" method="POST">
                @csrf
                <label for="discount_percentage">Discount (1-100%):</label>
                <input type="number" name="discount_percentage" id="discount_percentage" class="m-2 input"
                    value="{{ $discountPercentage }}" min="0" max="100">
                <div class="mt-4 flex gap-2">
                    @foreach ([3, 50, 100] as $discount)
                        <button type="button"
                            class="discount-card w-16 h-16 flex items-center justify-center bg-gray-200 text-lg font-bold rounded-lg hover:bg-gray-300"
                            data-discount="{{ $discount }}">
                            {{ $discount }}%
                        </button>
                    @endforeach
                </div>
                <div class="flex justify-center mt-4">
                    <button type="submit" class="text-lg btn">Apply Discount</button>
                </div>
            </form>
            <div class="mt-4 flex flex-col gap-3">
                <h3>Total Price: ₱{{ number_format($totalPrice, 2) }}</h3>
                <h3>Discount: {{ $discountPercentage }}%</h3>
                <h3>Final Price: ₱<span
                        id="final-price">{{ number_format($totalPrice * (1 - $discountPercentage / 100), 2) }}</span>
                </h3>
                <h3 class="font-bold">Change: ₱<span id="change-amount">0.00</span></h3>
                <!-- Discount Cards for 3%, 50%, 100% -->

            </div>



            <!-- Exchange Section -->
            <div class="mt-4 flex flex-col gap-3">
                <label for="exchange-input">Enter Amount:</label>
                <input type="number" id="exchange-input" class="input" min="0">

                <div class="mt-4 flex gap-2">
                    <!-- Reset Button -->
                    <button type="button" id="reset-exchange-button"
                        class="w-16 h-16 flex items-center justify-center bg-gray-200 text-lg font-bold rounded-lg hover:bg-gray-300">
                        <!-- Reset SVG Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                        </svg>
                        <form action="{{ route('pos.checkout') }}" method="POST">
                            @csrf
                            <input type="hidden" name="exchange" id="exchange-hidden-input">
                            <button type="submit" class="w-full px-6 py-2 mt-4 text-white bg-blue-600 rounded">Checkout</button>
                        </form>
                    </button>
                </div>


                <!-- Amount Selection Cards -->
                <div class="flex gap-2 mt-2">
                    <!-- Clickable cards for amounts -->
                    @foreach ([20, 50, 100, 200, 500, 1000] as $amount)
                        <button type="button"
                            class="amount-card w-16 h-16 flex items-center justify-center bg-gray-200 text-lg font-bold rounded-lg hover:bg-gray-300"
                            data-amount="{{ $amount }}">
                            ₱{{ $amount }}
                        </button>
                    @endforeach
                </div>


            </div>


        </div>
    @endif

    <script>
        // Select product and open modal
        const productElements = document.querySelectorAll('.product'); // Ensure product items have this class
        const modal = document.getElementById('quantity-modal');
        const closeModalButton = document.getElementById('close-modal');
        const selectedProductIdInput = document.getElementById('selected-product-id');

        productElements.forEach(product => {
            product.addEventListener('click', function() {
                const productId = this.getAttribute(
                    'data-product-id'); // Assuming you set the product ID as a data attribute

                // Set the selected product ID and open modal
                selectedProductIdInput.value = productId;
                modal.classList.remove('hidden');
            });
        });

        // Close the modal
        closeModalButton.addEventListener('click', function() {
            modal.classList.add('hidden');
        });
    </script>

    <script src="{{ asset('js/posScript.js') }}"></script>
</x-layout>
