<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\ProductBatch;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $inventories = Inventory::query()
            ->join('product_batches', 'inventories.batch_id', '=', 'product_batches.id')
            ->join('products', 'product_batches.product_id', '=', 'products.id')
            ->select('inventories.*')
            ->where(function ($query) use ($search) {
                $query->where('product_batches.batch_number', 'like', "%{$search}%")
                    ->orWhere('product_batches.expiration_date', 'like', "%{$search}%")
                    ->orWhere('product_batches.supplier_price', 'like', "%{$search}%")
                    ->orWhere('product_batches.received_date', 'like', "%{$search}%")
                    ->orWhere('products.product_name', 'like', "%{$search}%")
                    ->orWhere('inventories.quantity', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10);

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
}
