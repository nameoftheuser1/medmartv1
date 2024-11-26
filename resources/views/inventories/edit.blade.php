<x-layout>
    <div class="container mx-auto px-4">
        <a href="{{ route('inventories.index') }}" class="text-blue-500 underline block mb-4">&larr; Go back to inventory
            list</a>
        <div class="card w-full md:w-2/3 lg:w-1/2 mx-auto mt-5">
            <h1 class="text-2xl font-bold mb-5">Edit Inventory</h1>
            <form action="{{ route('inventories.update', $inventory->id) }}" method="post">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label for="batch_id" class="block mb-2">Batch ID:</label>
                    <select name="batch_id" id="batch_id" class="input w-full">
                        @foreach ($productBatches as $batch)
                            <option value="{{ $batch->id }}"
                                {{ (old('batch_id') ?? $inventory->batch_id) == $batch->id ? 'selected' : '' }}>
                                {{ $batch->batch_number }}
                            </option>
                        @endforeach
                    </select>
                    @error('batch_id')
                        <p class="error mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="quantity" class="block mb-2">Quantity:</label>
                    <x-tooltip message="Enter the quantity of the inventory.">
                        <input type="text" name="quantity" id="quantity" class="input w-full"
                            value="{{ old('quantity') ?? $inventory->quantity }}">
                    </x-tooltip>
                    @error('quantity')
                        <p class="error mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex justify-center items-center">
                    <button type="submit" class="btn text-lg w-full sm:w-auto">Update Inventory</button>
                </div>
            </form>
        </div>
    </div>
</x-layout>
