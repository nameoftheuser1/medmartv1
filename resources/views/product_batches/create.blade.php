<x-layout>
    <div class="container mx-auto px-4">
        <a href="{{ route('product_batches.index') }}" class="text-blue-500 underline block mb-4">&larr; Go back to
            Product Batches</a>
        <div class="card w-full md:w-4/5 lg:w-3/4 mx-auto mt-5">
            <h1 class="text-2xl font-bold mb-5">Add Product Batch</h1>

            @if ($errors->any())
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <strong class="font-bold">Whoops!</strong>
                    <span class="block sm:inline">There are some problems with your input.</span>
                    <ul class="mt-2 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('product_batches.store') }}" method="POST" id="productBatchForm">
                @csrf

                <!-- Top Section - 2 Column Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                    <!-- Left Column -->
                    <div>
                        <div class="mb-4">
                            <label for="batch_number" class="block mb-2 font-medium">Batch Name and Number:</label>
                            <input type="text" name="batch_number" id="batch_number"
                                class="input w-full rounded-lg border-gray-300" value="{{ old('batch_number') }}"
                                placeholder="Enter batch name/number">
                            @error('batch_number')
                                <p class="error mt-1 text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label for="received_date" class="block mb-2 font-medium">Received Date:</label>
                            <input type="date" name="received_date" id="received_date"
                                class="input w-full rounded-lg border-gray-300" value="{{ old('received_date') }}">
                            @error('received_date')
                                <p class="error mt-1 text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div>
                        <div class="mb-4">
                            <label for="supplier_id" class="block mb-2 font-medium">Supplier (optional):</label>
                            <select name="supplier_id" id="supplier_id" class="input w-full rounded-lg border-gray-300">
                                <option value="">Select a supplier</option>
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}"
                                        {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->supplier_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('supplier_id')
                                <p class="error mt-1 text-red-500 text-sm">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Products Section -->
                <div id="productBatchesContainer">
                    <div class="productBatch border border-gray-300 rounded-lg p-6 mb-6 bg-gray-50" id="batch1">
                        <!-- Product Count (Above the Product Info) -->
                        <div class="product-count mb-4 text-gray-600">
                            Product #1
                        </div>

                        <!-- Product Info - 2x2 Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="mb-4 relative">
                                <label for="product_input1" class="block mb-2 font-medium">Product:</label>
                                <input type="text" id="product_input1" name="product_input[]"
                                    class="input w-full rounded-lg border-gray-300"
                                    placeholder="Search or select a product" autocomplete="off">
                                <select name="product_id[]" id="productSelect1" class="hidden" required>
                                    <!-- Existing product options will be dynamically populated here -->
                                </select>
                                <ul id="productSearchResults1"
                                    class="absolute z-10 bg-white border border-gray-300 rounded-lg mt-1 max-h-60 overflow-y-auto hidden w-full">
                                </ul>
                            </div>
                            <div class="mb-4">
                                <label for="expiration_date" class="block mb-2 font-medium">Expiration Date:</label>
                                <input type="date" name="expiration_date[]"
                                    class="input w-full rounded-lg border-gray-300"
                                    value="{{ old('expiration_date.0') }}">
                            </div>
                            <div class="mb-4">
                                <label for="quantity" class="block mb-2 font-medium">Quantity:</label>
                                <input type="number" name="quantity[]" class="input w-full rounded-lg border-gray-300"
                                    value="{{ old('quantity.0') }}" placeholder="Enter quantity">
                            </div>
                            <div class="mb-4">
                                <label for="supplier_price" class="block mb-2 font-medium">Supplier Price:</label>
                                <div class="relative">
                                    <span
                                        class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500">₱</span>
                                    <input type="text" name="supplier_price[]"
                                        class="input w-full rounded-lg border-gray-300 pl-8"
                                        value="{{ old('supplier_price.0') }}" placeholder="0.00">
                                </div>
                            </div>
                        </div>

                        <!-- Remove Button -->
                        <button type="button" class="removeProductBatch text-red-500 hover:text-red-700 mt-4">
                            Remove Product Batch
                        </button>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row justify-center gap-4 mt-8">
                    <button type="button" id="addAnotherProduct" class="btn-secondary px-6 py-2 rounded-lg text-sm">
                        Add Another Product
                    </button>
                    <button type="submit"
                        class="btn bg-blue-600 hover:bg-blue-700 text-white px-8 py-2 rounded-lg text-sm">
                        Save Product Batch
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            let productBatchCount = 1;

            // Add Another Product Batch
            $('#addAnotherProduct').click(function() {
                productBatchCount++;
                let newProductBatch = $('#batch1').clone();

                // Update IDs
                newProductBatch.find('input[id^="product_input"]')
                    .attr('id', 'product_input' + productBatchCount)
                    .attr('onchange', 'searchProduct(this)');
                newProductBatch.find('ul[id^="productSearchResults"]')
                    .attr('id', 'productSearchResults' + productBatchCount);
                newProductBatch.find('select[id^="productSelect"]')
                    .attr('id', 'productSelect' + productBatchCount);

                // Clear input values
                newProductBatch.find('input, select').val('');

                $('#productBatchesContainer').append(newProductBatch);
            });

            // Function to search products dynamically
            window.searchProduct = function(input) {
                const batchNumber = input.id.replace('product_input', '');
                const query = input.value;
                const resultsContainer = document.getElementById('productSearchResults' + batchNumber);
                const productSelect = document.getElementById('productSelect' + batchNumber);

                if (query.length < 2) {
                    resultsContainer.classList.add('hidden');
                    return;
                }

                fetch(`/search-products?query=${query}`)
                    .then(response => response.json())
                    .then(data => {
                        resultsContainer.innerHTML = '';
                        productSelect.innerHTML = '';

                        if (data.length === 0) {
                            resultsContainer.innerHTML =
                                '<li class="p-2 text-gray-500">No products found</li>';
                            resultsContainer.classList.remove('hidden');
                        } else {
                            data.forEach(product => {
                                // Create search results
                                const resultLi = document.createElement('li');
                                resultLi.textContent = product.product_name;
                                resultLi.classList.add('p-2', 'hover:bg-gray-100',
                                    'cursor-pointer');
                                resultLi.onclick = () => {
                                    input.value = product.product_name;
                                    const option = document.createElement('option');
                                    option.value = product.id;
                                    option.selected = true;
                                    productSelect.innerHTML = '';
                                    productSelect.appendChild(option);
                                    resultsContainer.classList.add('hidden');
                                };
                                resultsContainer.appendChild(resultLi);

                                // Populate select for form submission
                                const option = document.createElement('option');
                                option.value = product.id;
                                option.textContent = product.product_name;
                                productSelect.appendChild(option);
                            });
                            resultsContainer.classList.remove('hidden');
                        }
                    })
                    .catch(error => console.error('Error:', error));
            };

            // Add event listener for input
            document.getElementById('product_input1').addEventListener('input', function() {
                searchProduct(this);
            });

            // Hide results when clicking outside
            document.addEventListener('click', function(e) {
                const searchResults = document.getElementById('productSearchResults1');
                if (searchResults && !e.target.closest('.relative')) {
                    searchResults.classList.add('hidden');
                }
            });
        });
    </script>

</x-layout>
