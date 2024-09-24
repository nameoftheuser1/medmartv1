<x-layout>
    <a href="{{ route('products.index') }}" class="text-blue-500 underline">&larr; Go back to product list</a>

    <div class="mx-auto mt-5 card md:w-1/2">
        <h1 class="mb-5">Add Products</h1>
        <form action="{{ route('products.store') }}" method="post">
            @csrf

            <div class="mb-4">
                <label for="product_name">Product Name: </label>
                <x-tooltip message="Enter the product name.">
                    <input type="text" name="product_name" id="product_name" class="input"
                        value="{{ old('product_name') }}">
                </x-tooltip>
                @error('product_name')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <x-tooltip message="This only needs to be filled if the product has a generic name.">
                    <label for="generic_name">Generic Name (optional): </label>
                    <input type="text" name="generic_name" id="generic_name" class="input"
                        value="{{ old('generic_name') }}">
                </x-tooltip>
                @error('generic_name')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="category">Category: </label>
                <select name="category" id="category" class="input">
                    <option value="Medications" {{ old('category') == 'Medications' ? 'selected' : '' }}>Medications
                    </option>
                    <option value="Supplements" {{ old('category') == 'Supplements' ? 'selected' : '' }}>Supplements
                    </option>
                    <option value="Personal Care" {{ old('category') == 'Personal Care' ? 'selected' : '' }}>Personal
                        Care</option>
                    <option value="First Aid" {{ old('category') == 'First Aid' ? 'selected' : '' }}>First Aid</option>
                    <option value="Medical Equipment" {{ old('category') == 'Medical Equipment' ? 'selected' : '' }}>
                        Medical Equipment</option>
                    <option value="Baby Products" {{ old('category') == 'Baby Products' ? 'selected' : '' }}>Baby
                        Products</option>
                    <option value="Health Devices" {{ old('category') == 'Health Devices' ? 'selected' : '' }}>Health
                        Devices</option>
                    <option value="Homeopathic Remedies"
                        {{ old('category') == 'Homeopathic Remedies' ? 'selected' : '' }}>Homeopathic Remedies</option>
                    <option value="Herbal Products" {{ old('category') == 'Herbal Products' ? 'selected' : '' }}>Herbal
                        Products</option>
                    <option value="Skin Care" {{ old('category') == 'Skin Care' ? 'selected' : '' }}>Skin Care</option>
                    <option value="Hair Care" {{ old('category') == 'Hair Care' ? 'selected' : '' }}>Hair Care</option>
                    <option value="Oral Care" {{ old('category') == 'Oral Care' ? 'selected' : '' }}>Oral Care</option>
                    <option value="Sexual Health" {{ old('category') == 'Sexual Health' ? 'selected' : '' }}>Sexual
                        Health</option>
                    <option value="Eye Care" {{ old('category') == 'Eye Care' ? 'selected' : '' }}>Eye Care</option>
                    <option value="Ear Care" {{ old('category') == 'Ear Care' ? 'selected' : '' }}>Ear Care</option>
                    <option value="Nutrition" {{ old('category') == 'Nutrition' ? 'selected' : '' }}>Nutrition</option>
                    <option value="Wellness" {{ old('category') == 'Wellness' ? 'selected' : '' }}>Wellness</option>
                    <!-- Add more categories as needed -->
                </select>
                @error('category')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="price">Price: </label>
                <input name="price" id="price" type="text" class="input" value="{{ old('price') }}">
                @error('price')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="barcode">(optional)Barcode: </label>
                <input name="barcode" id="barcode" type="text" class="input" value="{{ old('barcode') }}">
                @error('barcode')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="product_description">Product Description: </label>
                <textarea name="product_description" id="product_description" cols="20" rows="3" class="input">{{ old('product_description') }}</textarea>
                @error('product_description')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>
            <div class="flex items-center justify-center">
                <button type="submit" class="text-lg btn">Create Product</button>
            </div>
        </form>
    </div>
</x-layout>
