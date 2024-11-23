<div class="bg-white shadow-md rounded-lg p-4">
    <h3 class="text-lg font-semibold text-gray-800">{{ $item->product->product_name }}</h3>
    <div class="flex gap-3 mt-2 text-gray-600">
        <p><span class="font-bold">Quantity:</span> {{ $item->quantity }}</p>
        <p><span class="font-bold">Price:</span> ₱{{ number_format($item->price, 2) }}</p>
        <p><span class="font-bold">Total:</span>
            ₱{{ number_format($item->quantity * $item->price, 2) }}</p>
    </div>
    <div class="mt-4 flex gap-2 justify-center">
        <!-- Remove button -->
        <form action="{{ route('pos.removeItem') }}" method="POST" class="inline">
            @csrf
            <input type="hidden" name="product_id" value="{{ $item->product_id }}">
            <button type="submit"
                class="w-full px-4 py-2 text-sm text-white bg-red-700 rounded-lg hover:bg-red-800">
                Remove
            </button>
        </form>
        <!-- Update button -->
        <form action="{{ route('pos.updateItem') }}" method="POST" class="flex items-center">
            @csrf
            <input type="hidden" name="product_id" value="{{ $item->product_id }}">
            <input type="number" name="quantity" value="{{ $item->quantity }}"
                min="1" class="w-16 px-2 py-1 border rounded" required>
            <button type="submit"
                class="ml-2 px-4 py-2 text-sm text-white bg-green-600 rounded-lg hover:bg-green-700">
                Update
            </button>
        </form>
    </div>
</div>
