<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\Inventory;
use App\Models\Sale;
use App\Models\SaleDetail;
use Illuminate\Http\Request;

class POSController extends Controller
{
    public function index()
    {
        $products = Product::whereHas('productBatches.inventories', function ($query) {
            $query->where('quantity', '>', 0);
        })->with(['productBatches.inventories'])->get();

        $products = $products->map(function ($product) {
            $product->total_inventory = $product->productBatches->sum(function ($batch) {
                return $batch->inventories->sum('quantity');
            });
            return $product;
        });

        $saleDetails = session()->get('saleDetails', []);

        return view('pos.index', compact('products', 'saleDetails'));
    }
    public function addItem(Request $request)
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $product = Product::find($request->product_id);
        $quantity = $request->quantity;

        // Check total available inventory for the product
        $totalAvailableInventory = $product->productBatches->sum(function ($batch) {
            return $batch->inventories->sum('quantity');
        });

        if ($quantity > $totalAvailableInventory) {
            return redirect()->route('pos.index')->with('error', 'Not enough inventory for this product.');
        }

        $saleDetails = session()->get('saleDetails', []);

        $existingIndex = null;
        foreach ($saleDetails as $index => $saleDetail) {
            if ($saleDetail['product_id'] == $product->id) {
                $existingIndex = $index;
                break;
            }
        }

        if ($existingIndex !== null) {
            $saleDetails[$existingIndex]['quantity'] += $quantity;
        } else {
            $saleDetails[] = [
                'product_id' => $product->id,
                'quantity' => $quantity,
                'price' => $product->price,
            ];
        }

        session()->put('saleDetails', $saleDetails);

        return redirect()->route('pos.index');
    }

    public function removeItem(Request $request)
    {
        $saleDetails = session()->get('saleDetails', []);

        $saleDetails = array_filter($saleDetails, function ($saleDetail) use ($request) {
            return $saleDetail['product_id'] != $request->product_id;
        });

        session()->put('saleDetails', $saleDetails);

        return redirect()->route('pos.index');
    }

    public function updateItem(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::find($request->product_id);
        $quantity = $request->quantity;

        // Check total available inventory for the product
        $totalAvailableInventory = $product->productBatches->sum(function ($batch) {
            return $batch->inventories->sum('quantity');
        });

        if ($quantity > $totalAvailableInventory) {
            return redirect()->route('pos.index')->with('error', 'Not enough inventory for this product.');
        }

        $saleDetails = session()->get('saleDetails', []);

        foreach ($saleDetails as &$saleDetail) {
            if ($saleDetail['product_id'] == $request->product_id) {
                $saleDetail['quantity'] = $request->quantity;
                break;
            }
        }

        session()->put('saleDetails', $saleDetails);

        return redirect()->route('pos.index');
    }

    public function checkout(Request $request)
    {
        $saleDetails = session()->get('saleDetails', []);

        if (empty($saleDetails)) {
            return redirect()->route('pos.index')->with('error', 'No items in the sale.');
        }

        $totalAmount = 0;
        foreach ($saleDetails as $saleDetail) {
            $totalAmount += $saleDetail['quantity'] * $saleDetail['price'];
        }

        $sale = Sale::create([
            'user_id' => auth()->user()->id,
            'total_amount' => $totalAmount,
        ]);

        foreach ($saleDetails as $saleDetail) {
            SaleDetail::create([
                'sale_id' => $sale->id,
                'product_id' => $saleDetail['product_id'],
                'quantity' => $saleDetail['quantity'],
                'price' => $saleDetail['price'],
            ]);

            // Update product batch inventories
            $quantityRemaining = $saleDetail['quantity'];
            $batches = ProductBatch::where('product_id', $saleDetail['product_id'])->orderBy('id')->with('inventories')->get();

            foreach ($batches as $batch) {
                foreach ($batch->inventories as $inventory) {
                    if ($quantityRemaining <= 0) break;

                    if ($inventory->quantity >= $quantityRemaining) {
                        $inventory->quantity -= $quantityRemaining;
                        $inventory->save();
                        $quantityRemaining = 0;
                    } else {
                        $quantityRemaining -= $inventory->quantity;
                        $inventory->quantity = 0;
                        $inventory->save();
                    }
                }
                if ($quantityRemaining <= 0) break;
            }
        }

        session()->forget('saleDetails');

        return redirect()->route('pos.index')->with('success', 'Sale completed successfully.');
    }
}
