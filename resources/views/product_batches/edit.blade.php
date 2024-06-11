<x-layout>
    <a href="{{ route('product_batches.index') }}" class="text-blue-500 underline">&larr; Go back to product batches
        list</a>
    <div class="card md:w-1/2 mx-auto mt-5">
        <h1 class="mb-5">Edit Product Batch</h1>
        <form action="{{ route('product_batches.update', $productBatches->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label for="product_id">For Product:</label>
                <select name="product_id" id="product_id" class="input">
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}"
                            {{ $productBatches->product_id == $product->id ? 'selected' : '' }}>
                            {{ $product->product_name }}</option>
                    @endforeach
                </select>
                @error('product_id')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="supplier_id">Supplier:</label>
                <select name="supplier_id" id="supplier_id" class="input">
                    <option value="">None</option>
                    @foreach ($suppliers as $supplier)
                        <option value="{{ $supplier->id }}"
                            {{ $productBatches->supplier_id == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->supplier_name }}</option>
                    @endforeach
                </select>
                @error('supplier_id')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="batch_number">Batch Number:</label>
                <input type="number" name="batch_number" id="batch_number" class="input"
                    value="{{ $productBatches->batch_number }}">
                @error('batch_number')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="expiration_date">Expiration Date:</label>
                <input type="date" name="expiration_date" id="expiration_date" class="input"
                    value="{{ $productBatches->expiration_date ? $productBatches->expiration_date->format('Y-m-d') : '' }}">
                @error('expiration_date')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="supplier_price">Supplier Price:</label>
                <input type="text" name="supplier_price" id="supplier_price" step="0.01" class="input"
                    value="{{ $productBatches->supplier_price }}">
                @error('supplier_price')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="received_date">Received Date:</label>
                <input type="date" name="received_date" id="received_date" class="input"
                    value="{{ $productBatches->received_date ? $productBatches->received_date->format('Y-m-d') : '' }}">
                @error('received_date')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-center">
                <button type="submit" class="btn text-lg">Update Product Batch</button>
            </div>
        </form>
    </div>
</x-layout>
