<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Sale;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        // Get expenses based on search
        $expenses = Expense::query()
            ->where('description', 'like', "%{$search}%")
            ->orWhere('total_amount', 'like', "%{$search}%")
            ->latest()
            ->paginate(10);

        // Define the start and end of the current month
        $currentMonthStart = now()->startOfMonth();  // Start of the current month
        $currentMonthEnd = now()->endOfMonth();      // End of the current month

        // Calculate total sales for this month
        $salesThisMonth = Sale::whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])
            ->sum('total_amount');

        // Calculate total expenses for this month
        $expensesThisMonth = Expense::whereBetween('created_at', [$currentMonthStart, $currentMonthEnd])
            ->sum('total_amount');

        // Calculate monthly income (sales - expenses)
        $monthlyIncome = $salesThisMonth - $expensesThisMonth;

        // Pass the monthly calculations to the view
        return view('expenses.index', [
            'expenses' => $expenses,
            'salesThisMonth' => $salesThisMonth,
            'expensesThisMonth' => $expensesThisMonth,
            'monthlyIncome' => $monthlyIncome,
        ]);
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('expenses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'total_amount' => ['required', 'numeric', 'min:0'],
            'description' => ['required', 'max:255'],
        ]);

        Expense::create($validatedData);

        return redirect()->route('expenses.index')->with('success', 'The expense was added.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Expense $expense)
    {
        return view('expenses.show', ['expense' => $expense]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Expense $expense)
    {
        return view('expenses.edit', ['expense' => $expense]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Expense $expense)
    {
        $validatedData = $request->validate([
            'total_amount' => ['required', 'numeric', 'min:0'],
            'description' => ['required', 'max:255'],
        ]);

        $expense->update($validatedData);

        return redirect()->route('expenses.index')->with('success', 'The expense was updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Expense $expense)
    {
        $expense->delete();

        return back()->with('deleted', 'The expense was deleted.');
    }
}
