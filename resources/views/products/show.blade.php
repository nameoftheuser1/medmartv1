<x-layout>
    <div class="card">
        <div>
            <div class="card-title">{{ $product->product_name }}</div>
            <p>{{ $product->generic_name ? $product->generic_name : 'Generic Name is not available' }}</p>
        </div>

        <div class="mt-5">
            <p>Category: {{ $product->category }}</p>
        </div>
        <div class="mt-5">
            <p>Price: ₱{{ $product->price }}</p>
        </div>
        <div class="mt-5">
            <p>Description {{ $product->product_description }} </p>
        </div>
        <div class="flex justify-between mt-5">
            <div class="mx-auto">
                <p>Created At {{ $product->created_at }} </p>
            </div>
            <div class="mx-auto">
                <p>Updated At {{ $product->updated_at }} </p>
            </div>
        </div>

        <div class="flex justify-between mt-5">
            <div class="mx-auto">
                <a href="{{route('products.index')}}" class="btn text-lg">Go back</a>
            </div>
            <div class="mx-auto">
                <a href="{{ route('products.edit', $product->id) }}" class="btn text-lg">Edit Product</a>
            </div>
        </div>
    </div>
</x-layout>
