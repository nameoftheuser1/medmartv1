<x-layout>
    <a href="{{ route('expenses.index') }}" class="text-blue-500 underline">&larr; Go back to expense list</a>

    <div class="card md:w-1/2 mx-auto mt-5">
        <h1 class="mb-5">Update Expense</h1>
        <form action="{{ route('expenses.update', $expense) }}" method="post">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="total_amount">Total Amount: </label>
                <x-tooltip message="Enter the total amount of the expense.">
                    <input type="number" name="total_amount" id="total_amount" class="input"
                        value="{{ $expense->total_amount }}" step="0.01">
                </x-tooltip>
                @error('total_amount')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="description">Description: </label>
                <x-tooltip message="Provide a description of the expense.">
                    <textarea name="description" id="description" cols="20" rows="3" class="input">{{ $expense->description }}</textarea>
                </x-tooltip>
                @error('description')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-center items-center">
                <button type="submit" class="btn text-lg">Update Expense</button>
            </div>
        </form>
    </div>
</x-layout>
