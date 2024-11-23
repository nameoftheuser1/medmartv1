<x-layout>
    <div class="container mx-auto px-4">
        <a href="{{ route('product_batches.index') }}" class="text-blue-500 underline block mb-4">&larr; Go back to
            Product Batches</a>
        <div class="card w-full md:w-4/5 lg:w-3/4 mx-auto mt-5">
            <h1 class="text-2xl font-bold mb-5">Add Product Batch</h1>
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
                            <div class="mb-4">
                                <label for="product_id" class="block mb-2 font-medium">Select Product:</label>
                                <select name="product_id[]" class="input w-full rounded-lg border-gray-300">
                                    @foreach ($products as $product)
                                        <option value="{{ $product->id }}"
                                            {{ old('product_id.0') == $product->id ? 'selected' : '' }}>
                                            {{ $product->product_name }}
                                        </option>
                                    @endforeach
                                </select>
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

            $('#addAnotherProduct').click(function() {
                productBatchCount++;

                let newProductBatch = $('#batch1').clone();

                // Update IDs and clear values
                newProductBatch.attr('id', 'batch' + productBatchCount);

                // Update the product batch count display
                newProductBatch.find('.product-count').text('Product Batch #' + productBatchCount);

                newProductBatch.find('input, select').each(function() {
                    $(this).val('');
                });

                // Add remove button for additional batches
                if (productBatchCount > 1) {
                    let removeButton = $('<button>', {
                        type: 'button',
                        class: 'text-red-500 hover:text-red-700 underline text-sm mt-4',
                        text: 'Remove Product',
                        click: function() {
                            $(this).closest('.productBatch').remove();
                            // Recalculate product batch count after removal
                            updateProductBatchCounts();
                        }
                    });

                    newProductBatch.append(removeButton);
                }

                $('#productBatchesContainer').append(newProductBatch);
            });

            // Function to update the count of product batches
            function updateProductBatchCounts() {
                $('#productBatchesContainer .productBatch').each(function(index) {
                    $(this).find('.product-count').text('Product Batch #' + (index + 1));
                });
            }
        });
    </script>
</x-layout>
