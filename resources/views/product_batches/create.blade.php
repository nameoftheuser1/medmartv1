<x-layout>
    <a href="{{ route('product_batches.index') }}" class="text-blue-500 underline">&larr; Go back to product batches
        list</a>
    <div class="card md:w-1/2 mx-auto mt-5">
        <h1 class="mb-5">Add Product Batch</h1>
        <form action="{{ route('product_batches.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label for="product_id">For Product:</label>
                <select name="product_id" id="product_id" class="input">
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                            {{ $product->product_name }}</option>
                    @endforeach
                </select>
                @error('product_id')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="supplier_id">Supplier(optional):</label>
                <select name="supplier_id" id="supplier_id" class="input">
                    <option value="">None</option>
                    @foreach ($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->supplier_name }}</option>
                    @endforeach
                </select>
                @error('supplier_id')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="batch_number">Batch Name and Number:</label>
                <input type="text" name="batch_number" id="batch_number" class="input"
                    value="{{ old('batch_number') }}">
                @error('batch_number')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="expiration_date">Expiration Date:</label>
                <input type="date" name="expiration_date" id="expiration_date" class="input"
                    value="{{ old('expiration_date') }}">
                @error('expiration_date')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="quantity">Quantity:</label>
                <input type="number" name="quantity" id="quantity" class="input" value="{{ old('quantity') }}">
                @error('quantity')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="supplier_price">Supplier Price:</label>
                <input type="text" name="supplier_price" id="supplier_price" step="0.01" class="input"
                    value="{{ old('supplier_price') }}">
                @error('supplier_price')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="received_date">Received Date:</label>
                <input type="date" name="received_date" id="received_date" class="input"
                    value="{{ old('received_date') }}">
                @error('received_date')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-center">
                <button type="submit" class="btn text-lg">Add Product Batch</button>
            </div>
        </form>
    </div>
</x-layout>
