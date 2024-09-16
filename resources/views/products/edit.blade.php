<x-layout>

    <a href="{{ route('products.index') }}" class="text-blue-500 underline">&larr; Go back to product list</a>

    <div class="card md:w-1/2 mx-auto mt-5">
        <h1 class="mb-5">Update Product</h1>
        <form action="{{ route('products.update', $product) }}" method="post">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label for="product_name">Product Name: </label>
                <x-tooltip message="Enter the product name.">
                    <input type="text" name="product_name" id="product_name" class="input"
                        value="{{ $product->product_name }}">
                </x-tooltip>
                @error('product_name')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <x-tooltip message="This only needs to be filled if the product has a generic name.">
                    <label for="generic_name">Generic Name (optional): </label>
                    <input type="text" name="generic_name" id="generic_name" class="input"
                        value="{{ $product->generic_name }}">
                </x-tooltip>
                @error('generic_name')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="category">Category: </label>
                <select name="category" id="category" class="input">
                    <option value="Medications" {{ $product->category == 'Medications' ? 'selected' : '' }}>Medications
                    </option>
                    <option value="Supplements" {{ $product->category == 'Supplements' ? 'selected' : '' }}>Supplements
                    </option>
                    <option value="Personal Care" {{ $product->category == 'Personal Care' ? 'selected' : '' }}>Personal
                        Care</option>
                    <option value="First Aid" {{ $product->category == 'First Aid' ? 'selected' : '' }}>First Aid
                    </option>
                    <option value="Medical Equipment"
                        {{ $product->category == 'Medical Equipment' ? 'selected' : '' }}>Medical Equipment</option>
                    <option value="Baby Products" {{ $product->category == 'Baby Products' ? 'selected' : '' }}>Baby
                        Products</option>
                    <option value="Health Devices" {{ $product->category == 'Health Devices' ? 'selected' : '' }}>
                        Health Devices</option>
                    <option value="Homeopathic Remedies"
                        {{ $product->category == 'Homeopathic Remedies' ? 'selected' : '' }}>Homeopathic Remedies
                    </option>
                    <option value="Herbal Products" {{ $product->category == 'Herbal Products' ? 'selected' : '' }}>
                        Herbal Products</option>
                    <option value="Skin Care" {{ $product->category == 'Skin Care' ? 'selected' : '' }}>Skin Care
                    </option>
                    <option value="Hair Care" {{ $product->category == 'Hair Care' ? 'selected' : '' }}>Hair Care
                    </option>
                    <option value="Oral Care" {{ $product->category == 'Oral Care' ? 'selected' : '' }}>Oral Care
                    </option>
                    <option value="Sexual Health" {{ $product->category == 'Sexual Health' ? 'selected' : '' }}>Sexual
                        Health</option>
                    <option value="Eye Care" {{ $product->category == 'Eye Care' ? 'selected' : '' }}>Eye Care</option>
                    <option value="Ear Care" {{ $product->category == 'Ear Care' ? 'selected' : '' }}>Ear Care</option>
                    <option value="Nutrition" {{ $product->category == 'Nutrition' ? 'selected' : '' }}>Nutrition
                    </option>
                    <option value="Wellness" {{ $product->category == 'Wellness' ? 'selected' : '' }}>Wellness</option>
                </select>
                @error('category')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="price">Price: </label>
                <input name="price" id="price" type="text" class="input" value="{{ $product->price }}">
                @error('price')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="barcode">Barcode: </label>
                <input name="barcode" id="barcode" type="text" class="input" value="{{ $product->barcode }}">
                @error('barcode')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="product_description">Product Description: </label>
                <textarea name="product_description" id="product_description" cols="20" rows="3" class="input">{{ $product->product_description }}</textarea>
                @error('product_description')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex justify-center items-center">
                <button type="submit" class="btn text-lg">Update Product</button>
            </div>
        </form>
    </div>
</x-layout>
