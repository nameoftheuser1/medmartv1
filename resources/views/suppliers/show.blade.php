<x-layout>
    <div class="card w-1/2 mx-auto">
        <div>
            <h1>{{ $supplier->supplier_name }}</h1>
        </div>

        <div class="mt-5">
            <p>Contact Info: {{ $supplier->contact_info }} </p>
        </div>

        <div class="flex justify-between mt-5">
            <div class="mx-auto">
                <a href="{{ route('suppliers.index') }}" class="btn text-lg">Go back</a>
            </div>
            <div class="mx-auto">
                <a href="{{ route('suppliers.edit', $supplier) }}" class="btn text-lg">Edit Product</a>
            </div>
        </div>
    </div>
</x-layout>
