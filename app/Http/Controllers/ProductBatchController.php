<?php

namespace App\Http\Controllers;

use App\Models\ProductBatch;
use App\Http\Requests\StoreProductBatchRequest;
use App\Http\Requests\UpdateProductBatchRequest;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;

class ProductBatchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $productBatches = ProductBatch::query()
            ->join('products', 'product_batches.product_id', '=', 'products.id')
            ->select('product_batches.*')
            ->where(function ($query) use ($search) {
                $query->where('product_batches.batch_number', 'like', "%{$search}%")
                    ->orWhere('product_batches.expiration_date', 'like', "%{$search}%")
                    ->orWhere('product_batches.supplier_price', 'like', "%{$search}%")
                    ->orWhere('product_batches.received_date', 'like', "%{$search}%")
                    ->orWhere('products.product_name', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10);

        return view('product_batches.index', [
            'productBatches' => $productBatches,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::all();
        $suppliers = Supplier::all();

        return view('product_batches.create', [
            'products' => $products,
            'suppliers' => $suppliers,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'batch_number' => ['required', 'string'],
            'expiration_date' => ['required', 'date'],
            'supplier_price' => ['required', 'numeric'],
            'received_date' => ['required', 'date'],
            'quantity' => ['required', 'integer'],
        ]);

        $productBatch = ProductBatch::create($validatedData);

        Inventory::create([
            'batch_id' => $productBatch->id,
            'quantity' => $validatedData['quantity'],
        ]);

        return redirect()->route('product_batches.index')->with('success', 'Product batch and inventory created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductBatch $productBatch)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductBatch $productBatch)
    {
        $products = Product::all();
        $suppliers = Supplier::all();

        return view('product_batches.edit', [
            'productBatches' => $productBatch,
            'products' => $products,
            'suppliers' => $suppliers,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProductBatch $productBatch)
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'batch_number' => ['required', 'max:50'],
            'expiration_date' => ['required', 'date'],
            'supplier_price' => ['required', 'numeric'],
            'received_date' => ['required', 'date'],
        ]);

        $productBatch->update($request->all());

        return redirect()->route('product_batches.index')->with('success', 'Product batch updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductBatch $productBatch)
    {
        $productBatch->delete();

        return back()->with('deleted', 'The product was deleted');
    }
}
