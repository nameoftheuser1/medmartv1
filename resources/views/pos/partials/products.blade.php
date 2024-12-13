@foreach ($products as $product)
    <div class="w-full p-4 m-2 border rounded-lg cursor-pointer product-card card sm:w-60 bg-slate-100 flex flex-col justify-between product"
        data-product-id="{{ $product->id }}" data-name="{{ $product->product_name }}" data-price="{{ $product->price }}">
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
