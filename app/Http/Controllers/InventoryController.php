<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\ProductBatch;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $showExpired = $request->input('expired') === 'true';

        $inventories = Inventory::query()
            ->join('product_batches', 'inventories.batch_id', '=', 'product_batches.id')
            ->join('products', 'product_batches.product_id', '=', 'products.id')
            ->select('inventories.*', 'product_batches.batch_number', 'product_batches.expiration_date', 'product_batches.received_date', 'product_batches.supplier_price', 'products.product_name')
            ->when($showExpired, function ($query) {
                $query->whereDate('product_batches.expiration_date', '<', now());
            })
            ->where(function ($query) use ($search) {
                $query->where('inventories.quantity', 'like', "%{$search}%")
                    ->orWhere('product_batches.batch_number', 'like', "%{$search}%")
                    ->orWhere('product_batches.expiration_date', 'like', "%{$search}%")
                    ->orWhere('product_batches.received_date', 'like', "%{$search}%")
                    ->orWhere('product_batches.supplier_price', 'like', "%{$search}%")
                    ->orWhere('products.product_name', 'like', "%{$search}%");
            })
            ->orderBy('products.product_name')  // Added this line to order by product name alphabetically
            ->latest()
            ->paginate(10);

        // Calculate expiration status and quantity status
        $inventories->getCollection()->transform(function ($inventory) {
            $expirationDate = $inventory->productBatch->expiration_date;
            $quantity = $inventory->quantity;

            // expired logic
            $inventory->isExpired = $expirationDate->isPast();
            $inventory->isNearExpiry = $expirationDate->diffInMonths(Carbon::today()) <= 3;

            // Quantity logic
            $inventory->isLowStock = $quantity <= 30;
            $inventory->isOutOfStock = $quantity == 0;

            return $inventory;
        });

        return view('inventories.index', [
            'inventories' => $inventories,
        ]);
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $productBatches = ProductBatch::with(['product', 'supplier'])->get();
        return view('inventories.create', compact('productBatches'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'batch_id' => ['required', 'exists:product_batches,id'],
            'quantity' => ['required', 'integer'],
        ]);

        Inventory::create($request->all());

        return redirect()->route('inventories.index')->with('success', 'Product batch created successfully.');
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Inventory $inventory)
    {
        $productBatches = ProductBatch::all();
        return view('inventories.edit', [
            'inventory' => $inventory,
            'productBatches' => $productBatches,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Inventory $inventory)
    {
        $request->validate([
            'batch_id' => ['required', 'exists:product_batches,id'],
            'quantity' => ['required', 'integer'],
        ]);

        $inventory->update($request->all());

        return redirect()->route('inventories.index')->with('success', 'Inventory updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Inventory $inventory)
    {
        $inventory->delete();

        return back()->with('deleted', 'The product was deleted');
    }

    /**
     * Set the quantity of an inventory item to zero if the product is expired.
     */
    public function emptyQuantity(Inventory $inventory)
    {
        // Update the quantity to zero
        $inventory->update(['quantity' => 0]);

        return redirect()->back()->with('success', 'Product quantity set to zero successfully.');
    }

    public function productInventory(Request $request)
    {
        $search = $request->input('search');
        $sort = $request->input('sort', 'asc');
        $sortBy = 'quantity'; // Fixed to always sort by quantity

        $inventories = Inventory::query()
            ->join('product_batches', 'inventories.batch_id', '=', 'product_batches.id')
            ->join('products', 'product_batches.product_id', '=', 'products.id')
            ->select('products.product_name', DB::raw('SUM(inventories.quantity) as total_quantity'))
            ->where('products.product_name', 'like', "%{$search}%")
            ->groupBy('products.product_name')
            ->orderBy('total_quantity', $sort) // Only sort by quantity
            ->paginate(10);

        return view('inventories.product', [
            'inventories' => $inventories,
            'sort' => $sort,
            'sortBy' => $sortBy, // Always 'quantity'
        ]);
    }
    public function showReturned(Request $request)
{
    // Retrieve all returned products
    $returnedInventories = Inventory::whereHas('productBatch', function ($query) {
        $query->whereNotNull('return_date'); // Assuming return_date indicates a returned product
    })->paginate(10);

    return view('inventories.returned', [
        'inventories' => $returnedInventories,
    ]);
}

}
