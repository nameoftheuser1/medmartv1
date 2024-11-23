<x-layout>
    <div class="card w-1/2 mx-auto">
        <div>
            <h1>Expense Details</h1>
        </div>

        <div class="mt-5">
            <p>Total Amount: ₱{{ number_format($expense->total_amount, 2) }}</p>
        </div>

        <div class="mt-3">
            <p>Description: {{ $expense->description }}</p>
        </div>

        <div class="flex justify-between mt-5">
            <div class="mx-auto">
                <a href="{{ route('expenses.index') }}" class="btn text-lg">Go back</a>
            </div>
            <div class="mx-auto">
                <a href="{{ route('expenses.edit', $expense) }}" class="btn text-lg">Edit Expense</a>
            </div>
        </div>
    </div>
</x-layout>
