<x-layout>
    <a href="{{ route('inventories.index') }}" class="text-blue-500 underline">&larr; Go back to inventory list</a>

    <div class="card md:w-1/2 mx-auto mt-5">
        <h1 class="mb-5">Add Inventory</h1>
        <form action="{{ route('inventories.store') }}" method="post">
            @csrf

            <div class="mb-4">
                <label for="batch_id">Batch ID:</label>
                <select name="batch_id" id="batch_id" class="input">
                    @foreach ($productBatches as $batch)
                        <option value="{{ $batch->id }}" {{ old('batch_id') == $batch->id ? 'selected' : '' }}>
                            {{ $batch->batch_number }} - {{ $batch->product->product_name }} (Exp: {{ $batch->expiration_date->format('Y-m-d') }})
                        </option>
                    @endforeach
                </select>
                @error('batch_id')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="quantity">Quantity: </label>
                <x-tooltip message="Enter the quantity of the inventory.">
                    <input type="text" name="quantity" id="quantity" class="input" value="{{ old('quantity') }}">
                </x-tooltip>
                @error('quantity')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-center items-center">
                <button type="submit" class="btn text-lg">Create Inventory</button>
            </div>
        </form>
    </div>
</x-layout>
