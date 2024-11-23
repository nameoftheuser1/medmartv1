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
        // Get the search input and sort direction from the request
        $search = $request->input('search');
        $sort = $request->input('sort', 'asc'); // Default sorting to ascending if not specified

        // Query for product batches
        $productBatches = ProductBatch::query()
            ->join('products', 'product_batches.product_id', '=', 'products.id')
            ->select('product_batches.*')
            ->where(function ($query) use ($search) {
                // Only search for the product name
                $query->where('products.product_name', 'like', "%{$search}%");
            })
            // Exclude expired product batches
            ->where('product_batches.expiration_date', '>', now())
            // Sort alphabetically by product name
            ->orderBy('products.product_name', $sort)
            ->paginate(10);

        // Check if it's an AJAX request
        if ($request->ajax()) {
            // Return the HTML content of the table for the AJAX request
            return view('product_batches.partials._table', compact('productBatches'));
        }

        // Otherwise, return the full view
        return view('product_batches.index', compact('productBatches'));
    }



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = Product::orderBy('product_name')->get();
        $suppliers = Supplier::orderBy('supplier_name')->get();

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
        // Validate the incoming data for multiple product batches
        $validatedData = $request->validate([
            'batch_number' => ['required', 'string'],
            'received_date' => ['required', 'date'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'product_id' => ['required', 'array'],
            'product_id.*' => ['exists:products,id'],
            'expiration_date' => ['required', 'array'],
            'expiration_date.*' => ['date'],
            'quantity' => ['required', 'array'],
            'quantity.*' => ['integer'],
            'supplier_price' => ['required', 'array'],
            'supplier_price.*' => ['numeric'],
        ]);

        // Loop through each product and create corresponding entries
        foreach ($validatedData['product_id'] as $index => $productId) {
            // Create product batch entries
            $productBatch = ProductBatch::create([
                'product_id' => $productId,
                'supplier_id' => $validatedData['supplier_id'][$index] ?? null,
                'batch_number' => $validatedData['batch_number'],
                'expiration_date' => $validatedData['expiration_date'][$index],
                'supplier_price' => $validatedData['supplier_price'][$index],
                'received_date' => $validatedData['received_date'],
            ]);

            // Create an inventory record for each product batch
            Inventory::create([
                'batch_id' => $productBatch->id,
                'quantity' => $validatedData['quantity'][$index],
            ]);
        }

        return redirect()->route('product_batches.index')->with('success', 'Product batch and inventory created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductBatch $productBatch)
    {
        $products = Product::orderBy('product_name')->get();
        $suppliers = Supplier::orderBy('supplier_name')->get();

        return view('product_batches.show', [
            'productBatch' => $productBatch,
            'products' => $products,
            'suppliers' => $suppliers,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductBatch $productBatch)
    {
        $products = Product::orderBy('product_name')->get();
        $suppliers = Supplier::orderBy('supplier_name')->get();

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
            'batch_number' => ['required', 'string', 'unique:product_batches,batch_number,' . $productBatch->id],
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
