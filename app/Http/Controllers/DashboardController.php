<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\ProductBatch;
use App\Models\Sale;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $startDate = Carbon::now()->subDays(6);
        $endDate = Carbon::now();

        $salesData = Sale::select(DB::raw('DATE(created_at) as date'), DB::raw('sum(total_amount) as total'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();

        $categories = [];
        $salesSeries = [];

        for ($date = $startDate; $date <= $endDate; $date->addDay()) {
            $formattedDate = $date->format('Y-m-d');
            $categories[] = $date->format('D');
            $salesSeries[] = $salesData->firstWhere('date', $formattedDate)->total ?? 0;
        }

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
            'totalSalesToday' => $totalSalesToday,
            'categories' => $categories,
            'salesSeries' => $salesSeries,
        ]);
    }
}
