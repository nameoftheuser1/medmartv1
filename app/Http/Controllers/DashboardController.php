<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Supplier;
use App\Models\ProductBatch;
use App\Models\Sale;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->input('period', 'weekly');
        $inventoryType = $request->input('inventory-type', 'highest');

        // Handle period selection
        switch ($period) {
            case 'monthly':
                $startDate = Carbon::now()->startOfMonth()->subMonths(11);
                $endDate = Carbon::now()->endOfMonth();
                $groupBy = 'DATE_FORMAT(created_at, "%Y-%m")';
                $dateFormat = 'Y-m';
                $displayFormat = 'M Y';
                break;
            case 'yearly':
                $startDate = Carbon::now()->startOfYear()->subYears(4);
                $endDate = Carbon::now()->endOfYear();
                $groupBy = 'YEAR(created_at)';
                $dateFormat = 'Y';
                $displayFormat = 'Y';
                break;
            default:
                $startDate = Carbon::now()->subDays(6);
                $endDate = Carbon::now();
                $groupBy = 'DATE(created_at)';
                $dateFormat = 'Y-m-d';
                $displayFormat = 'D';
        }

        $salesData = cache()->remember("sales_data_{$period}", now()->addMinutes(10), function () use ($groupBy, $startDate, $endDate) {
            return Sale::select(DB::raw("$groupBy as date"), DB::raw('SUM(total_amount) as total'))
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupBy('date')
                ->orderBy('date', 'asc')
                ->get()
                ->keyBy('date');
        });

        $categories = [];
        $salesSeries = [];
        $totalSales = 0;

        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $formattedDate = $currentDate->format($dateFormat);
            $categories[] = $currentDate->format($displayFormat);
            $dailySales = $salesData->get($formattedDate)->total ?? 0;
            $salesSeries[] = $dailySales;
            $totalSales += $dailySales;

            if ($period === 'weekly') {
                $currentDate->addDay();
            } elseif ($period === 'monthly') {
                $currentDate->addMonth();
            } else {
                $currentDate->addYear();
            }
        }

        $productCount = cache()->remember('product_count', now()->addMinutes(30), function () {
            return Product::count();
        });

        $supplierCount = cache()->remember('supplier_count', now()->addMinutes(30), function () {
            return Supplier::count();
        });

        $thresholdDate = Carbon::now()->addDays(30);

        $expiringBatches = cache()->remember('expiring_batches', now()->addMinutes(10), function () use ($thresholdDate) {
            return ProductBatch::with(['product', 'inventories'])
                ->where('expiration_date', '<=', $thresholdDate)
                ->whereHas('inventories', function ($query) {
                    $query->where('quantity', '>', 0);
                })
                ->orderBy('expiration_date', 'asc')
                ->paginate(5);
        });

        $totalSalesToday = cache()->remember('total_sales_today', now()->addMinutes(5), function () {
            return Sale::whereDate('created_at', Carbon::today())
                ->sum('total_amount');
        });

        $cacheKey = "inventory_batches_{$inventoryType}";
        $inventoryBatches = cache()->remember($cacheKey, now()->addMinutes(10), function () use ($inventoryType) {
            if ($inventoryType === 'highest') {
                $inventories = Inventory::select('batch_id', DB::raw('MAX(quantity) as quantity'))
                    ->join('product_batches', 'inventories.batch_id', '=', 'product_batches.id')
                    ->groupBy('batch_id')
                    ->orderBy('quantity', 'desc')
                    ->limit(10)
                    ->get();
            } else {
                $inventories = Inventory::select('batch_id', DB::raw('MIN(quantity) as quantity'))
                    ->join('product_batches', 'inventories.batch_id', '=', 'product_batches.id')
                    ->groupBy('batch_id')
                    ->orderBy('quantity', 'asc')
                    ->limit(10)
                    ->get();
            }

            return ProductBatch::whereIn('id', $inventories->pluck('batch_id'))
                ->get()
                ->map(function ($batch) use ($inventories) {
                    $batch->quantity = $inventories->firstWhere('batch_id', $batch->id)->quantity;
                    return $batch;
                });
        });

        return view('dashboard.index', [
            'productCount' => $productCount,
            'supplierCount' => $supplierCount,
            'expiringBatches' => $expiringBatches,
            'totalSalesToday' => $totalSalesToday,
            'categories' => $categories,
            'salesSeries' => $salesSeries,
            'totalSales' => $totalSales,
            'currentPeriod' => $period,
            'currentInventoryType' => $inventoryType,
            'inventoryBatches' => $inventoryBatches,
        ]);
    }
}
