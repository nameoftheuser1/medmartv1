<x-layout>
    <a href="{{ route('sales.index') }}" class="text-blue-500 underline">&larr; Go back to sales list</a>

    <div class="mx-auto mt-5 card md:w-1/2">
        <h1 class="mb-5">Create Sale</h1>
        <form action="{{ route('sales.store') }}" method="post">
            @csrf

            <div class="mb-4">
                <label for="created_at">Date of Sale: </label>
                <input type="datetime-local" name="created_at" id="created_at" class="input"
                    value="{{ old('created_at') }}">
                @error('created_at')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="total_amount">Total Amount: </label>
                <input type="text" name="total_amount" id="total_amount" class="input"
                    value="{{ old('total_amount') }}">
                @error('total_amount')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-center">
                <button type="submit" class="text-lg btn">Create Sale</button>
            </div>
        </form>
    </div>
</x-layout>
