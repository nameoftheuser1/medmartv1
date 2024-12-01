<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\Inventory;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\TemporaryCartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class POSController extends Controller
{
    private function getSessionId()
    {
        if (!session()->has('cart_session_id')) {
            session()->put('cart_session_id', Str::uuid());
        }
        return session()->get('cart_session_id');
    }

    public function index(Request $request)
    {
        $query = Product::whereHas('productBatches.inventories', function ($query) {
            $query->where('quantity', '>', 0);
        })->with(['productBatches.inventories']);

        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                    ->orWhere('barcode', 'like', "%{$searchTerm}%")
                    ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }

        $products = $query->paginate(10);

        $products->getCollection()->map(function ($product) {
            $product->total_inventory = $product->productBatches->sum(function ($batch) {
                return $batch->inventories->sum('quantity');
            });
            return $product;
        });

        $sessionId = $this->getSessionId();
        $cartItems = TemporaryCartItem::where('session_id', $sessionId)->get();
        $discountPercentage = session()->get('discountPercentage', 0);

        return view('pos.index', compact('products', 'cartItems', 'discountPercentage'));
    }


    public function addItem(Request $request)
    {
        $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $product = Product::findOrFail($request->product_id);
        $quantity = $request->quantity;

        $totalAvailableInventory = 0;

        foreach ($product->productBatches as $batch) {
            foreach ($batch->inventories as $inventory) {
                $totalAvailableInventory += $inventory->quantity;
            }
        }

        $sessionId = $this->getSessionId();
        $currentCartItem = TemporaryCartItem::where('session_id', $sessionId)
            ->where('product_id', $product->id)
            ->first();

        $currentQuantityInCart = $currentCartItem ? $currentCartItem->quantity : 0;

        if (($currentQuantityInCart + $quantity) > $totalAvailableInventory) {
            return redirect()->route('pos.index')->with('error', 'Not enough inventory for this product.');
        }

        TemporaryCartItem::updateOrCreate(
            ['session_id' => $sessionId, 'product_id' => $product->id],
            ['quantity' => DB::raw('quantity + ' . $quantity), 'price' => $product->price]
        );

        return redirect()->route('pos.index');
    }

    public function removeAllItems()
    {
        $sessionId = $this->getSessionId();
        TemporaryCartItem::where('session_id', $sessionId)->delete();

        return redirect()->route('pos.index')->with('success', 'All items removed from the cart.');
    }

    public function removeItem(Request $request)
    {
        $sessionId = $this->getSessionId();
        TemporaryCartItem::where('session_id', $sessionId)
            ->where('product_id', $request->product_id)
            ->delete();

        return redirect()->route('pos.index');
    }

    public function updateItem(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);
        $quantity = $request->quantity;

        $totalAvailableInventory = $product->productBatches->sum(function ($batch) {
            return $batch->inventories->sum('quantity');
        });

        if ($quantity > $totalAvailableInventory) {
            return redirect()->route('pos.index')->with('error', 'Not enough inventory for this product.');
        }

        $sessionId = $this->getSessionId();
        TemporaryCartItem::where('session_id', $sessionId)
            ->where('product_id', $request->product_id)
            ->update(['quantity' => $quantity]);

        return redirect()->route('pos.index');
    }

    public function applyDiscount(Request $request)
    {
        $request->validate([
            'discount_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        session()->put('discountPercentage', $request->discount_percentage);

        return redirect()->route('pos.index');
    }

    public function checkout(Request $request)
    {
        // Validate the exchange field with regex
        $request->validate([
            'exchange' => [
                'required',
                'regex:/^\d{1,10}(\.\d{1,2})?$/', // Allows up to 10 digits with optional 2 decimal places
            ],
        ]);

        // Retrieve session ID and cart items
        $sessionId = $this->getSessionId();
        $cartItems = TemporaryCartItem::where('session_id', $sessionId)->get();
        $discountPercentage = session()->get('discountPercentage', 0);
        $exchange = $request->input('exchange');

        // Check if the cart is empty
        if ($cartItems->isEmpty()) {
            return redirect()->route('pos.index')->with('error', 'No items in the cart.');
        }

        // Calculate the total amount of the cart
        $totalAmount = $cartItems->sum(function ($item) {
            return $item->quantity * $item->price;
        });

        // Apply discount
        $totalAmount = $totalAmount * (1 - $discountPercentage / 100);

        // Calculate change amount
        $changeAmount = $exchange - $totalAmount;

        // Check if the exchange amount is sufficient
        if ($changeAmount < 0) {
            return redirect()->route('pos.index')->with('error', 'Insufficient funds.');
        }

        // Create a sale record
        $sale = Sale::create([
            'user_id' => auth()->user()->id,
            'total_amount' => $totalAmount,
            'discount_percentage' => $discountPercentage,
            'exchange' => $exchange,
        ]);

        // Loop through cart items, create sale details and update inventory
        foreach ($cartItems as $cartItem) {
            SaleDetail::create([
                'sale_id' => $sale->id,
                'product_id' => $cartItem->product_id,
                'quantity' => $cartItem->quantity,
                'price' => $cartItem->price,
            ]);

            // Update inventory for each product
            $this->updateInventory($cartItem->product_id, $cartItem->quantity);
        }

        // Clear the cart and reset discount session
        TemporaryCartItem::where('session_id', $sessionId)->delete();
        session()->forget('discountPercentage');

        // Return success response
        return redirect()->route('pos.receipt', ['sale_id' => $sale->id])
            ->with('success', 'Sale completed successfully. Change: ₱' . number_format($changeAmount, 2));
    }


    public function receipt($saleId)
    {
        $sale = Sale::findOrFail($saleId); // Find the sale by its ID
        $saleDetails = SaleDetail::where('sale_id', $sale->id)->get(); // Get all sale details for the sale

        return view('pos.receipt', compact('sale', 'saleDetails'));
    }

    private function updateInventory($productId, $quantityToReduce)
    {
        $quantityRemaining = $quantityToReduce;
        $batches = ProductBatch::where('product_id', $productId)->orderBy('id')->with('inventories')->get();

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
}
