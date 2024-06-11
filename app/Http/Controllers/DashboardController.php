<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\ProductBatch;
use App\Models\Sale;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;

class DashboardController extends Controller
{
    public function index()
    {
        $productCount = Product::count();
        $supplierCount = Supplier::count();

        // Calculate the date 30 days from now
        $thresholdDate = Carbon::now()->addDays(30);

        // Fetch product batches that are about to expire and have a positive quantity
        $expiringBatches = ProductBatch::with(['product', 'inventories'])
            ->where('expiration_date', '<=', $thresholdDate)
            ->whereHas('inventories', function ($query) {
                $query->where('quantity', '>', 0);
            })
            ->orderBy('expiration_date', 'asc')
            ->paginate(5);

        // Calculate the total sales for the current day
        $totalSalesToday = Sale::whereDate('created_at', Carbon::today())
            ->sum('total_amount');

        return view('dashboard.index', [
            'productCount' => $productCount,
            'supplierCount' => $supplierCount,
            'expiringBatches' => $expiringBatches,
            'totalSalesToday' => $totalSalesToday, // Pass the total sales to the view
        ]);
    }
}
