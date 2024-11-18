<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $sales = Sale::query()
            ->join('users', 'sales.user_id', '=', 'users.id')
            ->select('sales.*')
            ->where(function ($query) use ($search) {
                $query->where('total_amount', 'like', "%{$search}%")
                    ->orWhere('users.name', 'like', "%{$search}%")
                    ->orWhere('sales.created_at', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10);

        return view('sales.index', [
            'sales' => $sales,
        ]);
    }

    public function refund($id)
    {
        $sale = Sale::findOrFail($id);

        $sale->refunded += $sale->total_amount;
        $sale->total_amount = 0;
        $sale->status = 'refunded';

        $sale->save();

        return redirect()->route('sales.index')->with('success', 'Sale refunded successfully.');
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('sales.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'created_at' => 'required|date',
            'total_amount' => 'required|numeric|min:0',
        ]);

        // Create a new Sale record
        Sale::create([
            'user_id' => auth()->id(),
            'created_at' => $request->input('created_at'),
            'total_amount' => $request->input('total_amount'),
            'discount_percentage' => 0,
            'exchange' => 0,
        ]);

        return redirect()->route('sales.index')->with('success', 'Sale created successfully.');
    }


    /**
     * Display the specified resource.
     */
    public function show(Sale $sale)
    {
        $sale->load('saleDetails.product');

        return view('sales.show', ['sale' => $sale]);
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sale $sale) {}

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Sale $sale) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Sale $sale) {}
}
