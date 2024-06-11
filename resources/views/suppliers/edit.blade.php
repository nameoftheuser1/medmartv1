<x-layout>

    <a href="{{ route('suppliers.index') }}" class="text-blue-500 underline">&larr; Go back to product list</a>

    <div class="card md:w-1/2 mx-auto mt-5">
        <h1 class="mb-5">Update Supplier</h1>
        <form action="{{ route('suppliers.update', $supplier) }}" method="post">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label for="supplier_name">Supplier Name: </label>
                <x-tooltip message="Enter the supplier name.">
                    <input type="text" name="supplier_name" id="supplier_name" class="input"
                        value="{{ $supplier->supplier_name }}">
                </x-tooltip>
                @error('supplier_name')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="contact_info">Contact information: </label>
                <x-tooltip message="How the supplier can be contact?">
                    <textarea name="contact_info" id="contact_info" cols="20" rows="3" class="input">{{ $supplier->contact_info }}</textarea>
                </x-tooltip>
                @error('contact_info')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-center items-center">
                <button type="submit" class="btn text-lg">Update Supplier</button>
            </div>
        </form>
    </div>
</x-layout>
