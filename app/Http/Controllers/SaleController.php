<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
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

        // Get the sales with applied search filter
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

        // Get combined sales per day for the last 30 days
        $salesPerDay = $this->getSalesPerDay();

        // Prepare data for ApexChart (Dates and Total Amounts)
        $chartData = $salesPerDay->map(function ($sale) {
            return [
                'date' => $sale->date,
                'total_amount' => $sale->total_amount,
            ];
        });

        // Pass data to the view
        return view('sales.index', [
            'sales' => $sales,
            'salesPerDay' => $salesPerDay,
            'chartData' => $chartData,
        ]);
    }

    /**
     * Get combined sales data per day for the past 30 days.
     */
    private function getSalesPerDay()
    {
        return Sale::query()
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total_amount')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupByRaw('DATE(created_at)')
            ->orderBy('date', 'asc')
            ->get();
    }


    public function refund($id)
    {
        $sale = Sale::findOrFail($id);

        // Fetch all sale details related to this sale
        $saleDetails = $sale->saleDetails;

        foreach ($saleDetails as $detail) {
            // Find the corresponding inventory batch
            $inventory = Inventory::where('batch_id', $detail->product_id)->first();

            if ($inventory) {
                // Increment inventory quantity
                $inventory->quantity += $detail->quantity;
                $inventory->save();
            }
        }

        // Update the sale record
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
